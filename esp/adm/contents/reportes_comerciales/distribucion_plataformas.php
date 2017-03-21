<?php 
require_once('../Connections/dspp.php'); 
require_once('../Connections/mail.php');

mysql_select_db($database_dspp, $dspp);

if (!function_exists("GetSQLValueString")) {
	function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
	{
	  if (PHP_VERSION < 6) {
	    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
	  }

	  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

	  switch ($theType) {
	    case "text":
	      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
	      break;    
	    case "long":
	    case "int":
	      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
	      break;
	    case "double":
	      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
	      break;
	    case "date":
	      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
	      break;
	    case "defined":
	      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
	      break;
	  }
	  return $theValue;
	}
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

$row_informes = mysql_query("SELECT informe_general.*, trim1.total_trim1, trim2.total_trim2, trim3.total_trim3, trim4.total_trim4, empresa.abreviacion FROM informe_general INNER JOIN empresa ON informe_general.idempresa = empresa.idempresa LEFT JOIN trim1 ON informe_general.trim1 = trim1.idtrim1 LEFT JOIN trim2 ON informe_general.trim2 = trim2.idtrim2 LEFT JOIN trim3 ON informe_general.trim3 = trim3.idtrim3 LEFT JOIN trim4 ON informe_general.trim4 = trim4.idtrim4", $dspp) or die(mysql_error());
$plataformas_spp = array();
$total_informes = mysql_num_rows($row_informes);
$row_plataformas = mysql_query("SELECT * FROM plataformas_spp", $dspp) or die(mysql_error());
while($array_plataformas = mysql_fetch_assoc($row_plataformas)){
	$plataformas_spp[] = $array_plataformas['pais'];
}

$anio_actual = date('Y',time());
$row_configuracion = mysql_query("SELECT * FROM porcentaje_ajuste WHERE anio = $anio_actual", $dspp) or die(mysql_error());
$configuracion = mysql_fetch_assoc($row_configuracion);

//$plataformas_spp = mysql_fetch_assoc($row_plataformas);
//$plataformas_spp = array('Ecuador', 'Perú', 'Colombia', 'Guatemala');


$row_anio = mysql_query("SELECT FROM_UNIXTIME(ano,'%Y') AS 'anio' FROM informe_general GROUP BY FROM_UNIXTIME(ano,'%Y')", $dspp) or die(mysql_error());
?>
<h4>
	Distribución plataformas de origen SPP | Año
	<select name="anio">
		<?php 
		while($anio = mysql_fetch_assoc($row_anio)){
			$fecha = $anio['anio'];
			echo "<option value='$fecha'>$fecha</option>";
		}
		 ?>
	</select>
</h4>

<?php
for ($i=1; $i <= 4; $i++) {
	$txt_id = 'idtrim'.$i;
	$txt_trim = 'trim'.$i;
	$txt_idtrim = 'T'.$i.'-'.$anio_actual;
	$txt_estado = 'estado_trim'.$i;

	$clave_distribucion = 0;
	///calculamos las claves de distribucion, esto sumando el valor del contrato de cada formato del primer trimestre
	$row_formato_compras = mysql_query("SELECT SUM(valor_total_contrato) AS 'total_contrato' FROM formato_compras WHERE idtrim LIKE '%$txt_idtrim%'", $dspp) or die(mysql_error());
	$formato_compras = mysql_fetch_assoc($row_formato_compras);
	$porcentaje_anual = $configuracion['distribucion_plataforma_origen'];
	$cuota_uso = round(($formato_compras['total_contrato'] * $porcentaje_anual) / 100, 2);


	$row_trim = mysql_query("SELECT $txt_id FROM $txt_trim WHERE $txt_id LIKE '%$txt_idtrim%'", $dspp) or die(mysql_error());
	$num_trim = mysql_num_rows($row_trim);
	//echo '<br>TOTAL TRIMS: '.$num_trim;
	$row_trim = mysql_query("SELECT $txt_id FROM $txt_trim WHERE $txt_id LIKE '%$txt_idtrim%' AND $txt_estado = 'ACTIVO'", $dspp) or die(mysql_error());
	$num_activo = mysql_num_rows($row_trim);
	//echo '<br>TOTAL TRIMS activos: '.$num_activo;
	$row_trim = mysql_query("SELECT $txt_id FROM $txt_trim WHERE $txt_id LIKE '%$txt_idtrim%' AND $txt_estado = 'FINALIZADO'", $dspp) or die(mysql_error());
	$num_finalizado = mysql_num_rows($row_trim);
	//echo '<br>TOTAL TRIMS finalizados: '.$num_finalizado;

	if($num_trim != 0){
	?>
		<table class="table table-bordered" style="font-size:12px;">
			<thead>
				<tr>
					<th class="success"><b>Trimestre <?php echo $i; ?></b></th>
					<th class="info">Numero de informes: <span style="color:#e74c3c"><?php echo $num_trim; ?></span></th>
					<th class="info"><?php echo 'Activos: <span style="color:#e74c3c">'.$num_activo.'</span> Finalizados: <span style="color:#e74c3c">'.$num_finalizado.'</span>'; ?></th>
					<th class="info">Valor total contratos: <span style="color:red"><?php echo round($formato_compras['total_contrato'],2).' (<span style="color:#2c3e50">'.$configuracion['distribucion_plataforma_origen'].'%</span> = '.$cuota_uso.')'; ?></span></th>
				</tr>
				<tr>
					<th>Plataforma</th>
					<th>Valor total contratos</th>
					<th>Porcentaje Clave distribución</th>
					<th>Valor clave distribución</th>
				</tr>
			</thead>
			<tbody>
				<?php 
				$row_plataformas = mysql_query("SELECT * FROM plataformas_spp", $dspp) or die(mysql_error());
				while($plataformas = mysql_fetch_assoc($row_plataformas)){
					$row_formatos = mysql_query("SELECT COUNT(idformato_compras) AS 'total_formatos', SUM(formato_compras.valor_total_contrato) AS 'total_contrato' FROM formato_compras WHERE idtrim LIKE '%$txt_idtrim%' AND pais = '$plataformas[pais]'", $dspp) or die(mysql_error());
					$formatos = mysql_fetch_assoc($row_formatos);
					$num_formatos = mysql_num_rows($row_plataformas);
					$query = "SELECT * FROM formato_compras WHERE idtrim LIKE '%$txt_idtrim%' AND pais = '$plataformas[pais]'";

					if($formatos['total_contrato'] > 0){
						$clave_distribucion = round(($formatos['total_contrato'] * 100) / $formato_compras['total_contrato'], 2);
						$valor_clave_distribucion = round(($cuota_uso * $clave_distribucion) / 100,2);
					}else{
						$clave_distribucion = 0;
						$valor_clave_distribucion = 0;
					}
				?>
				<tr>
					<td style="background-color:#27ae60;color:#ecf0f1"><?php echo $plataformas['pais']; ?></td>
					<td>
						<?php 
						if(isset($formatos['total_contrato'])){
							echo round($formatos['total_contrato'],2).' USD';
						}else{
							echo "0 USD";
						}
						?>
					</td>
					<td><?php echo $clave_distribucion.' %'; ?></td>
					<td><?php echo $valor_clave_distribucion.' USD'; ?></td>
					
					<!--<td>
						<?php echo "Num formatos: ".$formatos['total_formatos']." - Total contrato: ".$formatos['total_contrato']; ?>
						<?php

					
						echo "Clave: <span style='color:red'>".$clave_distribucion." %</span>";
						 ?>
					</td>
					<td>
						<?php 

						echo $cuota_uso.' ('.$clave_distribucion.' %) = '.$valor_clave_distribucion; 
						?>
					</td>
					<td></td>-->
				</tr>
				<?php
				}
				 ?>
			</tbody>
		</table>
	<?php
	}else{
	?>
		<table class="table">
			<thead>
				<tr>
					<th class="warning"><b>No se encontraron registros sobre el Trimestre <?php echo $i; ?></b></th>
				</tr>
			</thead>
		</table>
	<?php
	}
}
 ?>




<!--<table class="table table-bordered table-condensed" style="font-size:12px;">
	<tr class="info">
		<th style="background-color:#ECF0F1;color:#2980B9">
			Año:
			<select name="anio_reporte">
				<option>Todos</option>
				<option>2017</option>
				<option>2016</option>
			</select>

		</th>
		<th colspan="3" style="background-color:#ECF0F1;color:#2980B9">
			Empresa:
			<?php 
			//seleccionamos y englobamos todas las empresa que han creado reportes
			$row_empresa = mysql_query("SELECT formato_compras.idempresa, empresa.abreviacion FROM formato_compras INNER JOIN empresa ON formato_compras.idempresa = empresa.idempresa GROUP BY formato_compras.idempresa",$dspp) or die(mysql_error());
			 ?>
			<form action="" method="POST">
				<select name="consultar_empresa" onchange="this.form.submit();">
					<option value="todos">Todos</option>
					<?php 
					while($empresa = mysql_fetch_assoc($row_empresa)){
						if(isset($_POST['consultar_empresa']) && $_POST['consultar_empresa'] == $empresa['idempresa']){
						?>
							<option value="<?php echo $empresa['idempresa']; ?>" selected><?php echo $empresa['abreviacion']; ?></option>
						<?php
						}else{
						?>
							<option value="<?php echo $empresa['idempresa']; ?>"><?php echo $empresa['abreviacion']; ?></option>
						<?php
						}
					?>
					<?php
					}
					 ?>
				</select>
			</form>
		</th>
		<th class="text-center" colspan="17">Distribución plataformas SPP | <span style="color:#e74c3c">Concentrado general</span></th>
	</tr>
	<tr>
		<th class="text-center" rowspan="2">Plataforma SPP</th>
		<th class="text-center" colspan="5">Nº Transacciones</th>
		<th class="text-center" style="background-color:#2980B9;color:#ECF0F1" colspan="5">Cuota uso</th>
		<th class="text-center" style="background-color:#2980B9;color:#ECF0F1" colspan="5">Valor contrato</th>
		<th class="text-center" style="background-color:#E74C3C;color:#ECF0F1" colspan="5">Calculos</th>

	</tr>
	<tr>
		<?php 
		for ($i=0; $i < 4; $i++) { 
		?>
			<td class="text-center">Trim1</td>
			<td class="text-center">Trim2</td>
			<td class="text-center">Trim3</td>
			<td class="text-center">Trim4</td>
			<td class="text-center">Total</td>
		<?php
		}
		 ?>
	</tr>
	<?php 
	/*if(isset($_POST['consultar_empresa'])){
		if($_POST['consultar_empresa'] == 'todos'){
			echo "TODOS";
		}else{
			echo 'empresa'.$_POST['consultar_empresa'];
		}
	}else{
		echo "NO SE ENVIO";
	}*/

	foreach ($plataformas_spp as $value) {
		$row_reembolso = mysql_query("SELECT COUNT(idformato_compras) AS 'total_formatos', pais, SUM(total_a_pagar) AS 'compras_totales' FROM formato_compras WHERE pais = '$value'", $dspp) or die(mysql_error());
		$reembolso = mysql_fetch_assoc($row_reembolso);
		$transacciones = mysql_num_rows($row_reembolso);

	?>
	<tr>
		<td style="background-color:#34495e;color:#ecf0f1"><?php echo $value; ?></td>
		<?php
		$idtrim = '';
		$total_transacciones = 0;
		$total_contrato = 0;
		$total_cuota_uso = 0;
		$total_reembolso = 0;
		$sql_empresa = '';
		if(isset($_POST['consultar_empresa']) && $_POST['consultar_empresa'] != 'todos'){
			$sql_empresa = ' AND formato_compras.idempresa = '.$_POST['consultar_empresa'];
		}
		//nº transacciones
		for ($i=1; $i <= 4; $i++) {
			$idtrim = 'T'.$i;
			//query transacciones
			$row_transacciones = mysql_query("SELECT COUNT(idformato_compras) AS 'transacciones' FROM formato_compras WHERE pais = '$value' AND idtrim LIKE '%$idtrim%' $sql_empresa", $dspp) or die(mysql_error());
			$transacciones = mysql_fetch_assoc($row_transacciones);
			echo '<td>'.$transacciones['transacciones'].'</td>';
			$total_transacciones += $transacciones['transacciones'];
		}
			echo '<td>'.$total_transacciones.'</td>';
		//total_cuota_uso
		for ($i=1; $i <= 4; $i++) { 
			$idtrim = 'T'.$i;
			$row_contrato = mysql_query("SELECT ROUND(SUM(valor_total_contrato),2) AS 'contrato' FROM formato_compras WHERE pais = '$value' AND idtrim LIKE '%$idtrim%' $sql_empresa", $dspp) or die(mysql_error());
			$contrato = mysql_fetch_assoc($row_contrato);
			echo '<td style="background-color:#2980B9;color:#ECF0F1">'.$contrato['contrato'].'</td>';
			$total_contrato += $contrato['contrato'];
		}
			echo '<td style="background-color:#ecf0f1;color:#c0392b;font-weight:bold">'.$total_contrato.' USD</td>';
		//total_valor_contrato
		for ($i=1; $i <= 4; $i++) { 
			$idtrim = 'T'.$i;
			$row_cuota_uso = mysql_query("SELECT ROUND(SUM(total_a_pagar),2) AS 'cuota_uso' FROM formato_compras WHERE pais = '$value' AND idtrim LIKE '%$idtrim%' $sql_empresa", $dspp) or die(mysql_error());
			$cuota_uso = mysql_fetch_assoc($row_cuota_uso);
			echo '<td style="background-color:#2980B9;color:#ECF0F1">'.$cuota_uso['cuota_uso'].'</td>';
			$total_cuota_uso += $cuota_uso['cuota_uso'];
		}
			echo '<td style="background-color:#ecf0f1;color:#c0392b;font-weight:bold">'.$total_cuota_uso.' USD</td>';
		//reembolso
		for ($i=1; $i <= 4; $i++) {
			$idtrim = 'T'.$i;
			$row_reembolso = mysql_query("SELECT ROUND(SUM(valor_total_contrato),2) AS 'compras' FROM formato_compras WHERE pais = '$value' AND idtrim LIKE '%$idtrim%' $sql_empresa", $dspp) or die(mysql_error());
			$reembolso = mysql_fetch_assoc($row_reembolso);
			$porcentaje = round(($reembolso['compras'] * 0.10),2);
			echo '<td style="background-color:#E74C3C;color:#ECF0F1;">'.$porcentaje.'</td>';
			$total_reembolso += $porcentaje;
		}
			echo '<td style="background-color:#ecf0f1;color:#c0392b;font-weight:bold">'.$total_reembolso.' USD</td>';
		 ?>
	</tr>
	<?php
	}
	?>

</table>

			<!--<table class="table table-bordered table-condensed" style="font-size:12px;">
				<thead>
					<tr class="info">
						<th>	
							Año: 
							<select name="anio_reporte">
								<option>Todos</option>
								<option>2017</option>
								<option>2016</option>
							</select>
						</th>
						<th colspan="4">Distribución plataformas SPP | <span style="color:#e74c3c">Concentrado general</span></th>
					</tr> 
					
					<tr>
						<th class="text-center">Plataforma SPP</th>
						<th class="text-center">Nº transacciones</th>
						<th class="text-center">Compras totales</th>
						<th class="text-center">Reembolso(10%)</th>
						
					</tr>

				</thead>
				<tbody>
				<?php
				$total_compras = 0;
				$total_reembolso = 0;
				foreach ($plataformas_spp as  $value) {
					$row_reembolso = mysql_query("SELECT COUNT(idformato_compras) AS 'total_formatos', pais, SUM(total_a_pagar) AS 'compras_totales' FROM formato_compras WHERE pais = '$value'", $dspp) or die(mysql_error());
					$reembolso = mysql_fetch_assoc($row_reembolso);
					$transacciones = mysql_num_rows($row_reembolso);
				?>
					<tr>
						<td><?php echo $value; ?></td>
						<td><?php echo $reembolso['total_formatos']; ?></td>
						<td><?php if(empty($reembolso['compras_totales'])){ echo 0; }else{ echo $reembolso['compras_totales']; } ?> USD</td>
						<td><?php echo round(($reembolso['compras_totales'] * 0.10), 2); ?> USD</td>
					</tr>
				<?php
				$total_compras += $reembolso['compras_totales'];

				}
				 ?>
				 	<tr>
				 		<td></td>
				 		<td></td>
				 		<td><?php echo $total_compras; ?> USD</td>
				 		<td><?php echo round(($total_compras * 0.10),2); ?> USD</td>
				 	</tr>
				</tbody>
			</table>-->