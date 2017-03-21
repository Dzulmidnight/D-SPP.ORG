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

$row_informes = mysql_query("SELECT informe_general.*, trim1.estado_trim1, trim1.valor_contrato_trim1, trim1.cuota_uso_trim1, trim2.estado_trim2, trim2.valor_contrato_trim2, trim2.cuota_uso_trim2, trim3.estado_trim3, trim3.valor_contrato_trim3, trim3.cuota_uso_trim3, trim4.estado_trim4, trim4.valor_contrato_trim4, trim4.cuota_uso_trim4, empresa.abreviacion FROM informe_general INNER JOIN empresa ON informe_general.idempresa = empresa.idempresa LEFT JOIN trim1 ON informe_general.trim1 = trim1.idtrim1 LEFT JOIN trim2 ON informe_general.trim2 = trim2.idtrim2 LEFT JOIN trim3 ON informe_general.trim3 = trim3.idtrim3 LEFT JOIN trim4 ON informe_general.trim4 = trim4.idtrim4", $dspp) or die(mysql_error());
$total_informes = mysql_num_rows($row_informes);
//$plataformas_spp = array('Ecuador', 'Perú', 'Colombia', 'Guatemala');

$row_plataformas = mysql_query("SELECT * FROM plataformas_spp", $dspp) or die(mysql_errno());

function redondear_dos_decimal($valor) { 
   $float_redondeado=round($valor * 100) / 100; 
   return $float_redondeado; 
}
?>
<hr>
<div class="row">
	<div class="col-md-12">
		<table class="table table-bordered table-hover table-condensed" style="font-size:12px;">
			<thead>
				<tr>
					<td>
						<span class="glyphicon glyphicon-time" aria-hidden="true"></span> Aun no se ha cargado comprobante de pago
					</td>
					<td><img src="../../img/circulo_verde.jpg" alt=""> Activo</td>
				</tr>
				<tr>
					<td>
						<span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Pagado
					</td>
					<td><img src="../../img/circulo_rojo.jpg" alt=""> Finalizado</span></td>
				</tr>

				<tr class="warning">
					<th>	
						Año: 
						<select name="anio_reporte">
							<option>Todos</option>
							<option>2017</option>
							<option>2016</option>
						</select>
					</th>
					<th class="text-center" colspan="7">Resumen cuota de uso</th>
				</tr>
				<tr>
					<th class="text-center">Año</th>
					<th class="text-center">ID informe general</th>
					<!--<th class="text-center">Estado</th>-->
					<th class="text-center">Empresa</th>
					<th class="text-center">Trimestre 1</th>
					<th class="text-center">Trimestre 2</th>
					<th class="text-center">Trimestre 3</th>
					<th class="text-center">Trimestre 4</th>
					<th class="text-center">Total Cuota de uso</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$total = 0;
				$cuota_uso_trim1 = 0;
				$cuota_uso_trim2 = 0;
				$cuota_uso_trim3 = 0;
				$cuota_uso_trim4 = 0;
				$total_final = 0;
				if($total_informes == 0){
					echo '<tr><td colspan="7">No se encontraron registros</td></tr>';
				}else{
					while($informes = mysql_fetch_assoc($row_informes)){
						
						$cuota_uso_trim1 += $informes['cuota_uso_trim1'];
						$cuota_uso_trim2 += $informes['cuota_uso_trim2'];
						$cuota_uso_trim3 += $informes['cuota_uso_trim3'];
						$cuota_uso_trim4 += $informes['cuota_uso_trim4'];
						$total_final = $cuota_uso_trim1 + $cuota_uso_trim2 + $cuota_uso_trim3 + $cuota_uso_trim4;
						echo "<tr>";
							echo '<td>'.date('Y',$informes['ano']).'</td>';
							if($informes['estado_informe'] == 'ACTIVO'){
								echo '<td><img src="../../img/circulo_verde.jpg"> <a href="?REPORTES&informe_compras='.$informes['idinforme_general'].'"><span class="glyphicon glyphicon-list-alt"></span> '.$informes['idinforme_general'].'</a></td>';
							}else if($informes['estado_informe'] == 'FINALIZADO'){
								echo '<td><img src="../../img/circulo_rojo.jpg"> <a href="?REPORTES&informe_compras='.$informes['idinforme_general'].'"><span class="glyphicon glyphicon-list-alt"></span> '.$informes['idinforme_general'].'</a></td>';
							}
							//echo '<td><a href="?REPORTES&informe_compras='.$informes['idinforme_general'].'"><span class="glyphicon glyphicon-list-alt"></span> '.$informes['idinforme_general'].'</a></td>';
							//echo '<td>'.$informes['estado_informe'].'</td>';
							echo '<td><a href="?EMPRESAS&detail&idempresa='.$informes['idempresa'].'">'.$informes['abreviacion'].'</a></td>';
							echo '<td>'.$informes['cuota_uso_trim1'].'</td>';
							echo '<td>'.$informes['cuota_uso_trim2'].'</td>';
							echo '<td>'.$informes['cuota_uso_trim3'].'</td>';
							echo '<td>'.$informes['cuota_uso_trim4'].'</td>';
							echo '<td><a href="?REPORTES&informe_compras&detalle_total='.$informes['idinforme_general'].'"><span class="glyphicon glyphicon-search"></span> '.$informes['total_cuota_uso'].' USD</a></td>';
						echo '</tr>';
					}
					echo '<tr>';
						echo '<td colspan="3" style="text-align:right"><b>Suma total</b></td>';
						echo '<td style="color:#e74c3c;">'.$cuota_uso_trim1.'</td>';
						echo '<td style="color:#e74c3c;">'.$cuota_uso_trim2.'</td>';
						echo '<td style="color:#e74c3c;">'.$cuota_uso_trim3.'</td>';
						echo '<td style="color:#e74c3c;">'.$cuota_uso_trim4.'</td>';
						echo '<td style="color:#e74c3c;">'.$total_final.' USD</td>';
					echo '</tr>';
					//echo '<tr><td colspan="9"><b>Suma total: <span style="color:#e74c3c">'.$total.'</span> USD</b></td></tr>';
				}
				 ?>
			</tbody>
		</table>
	</div>
	<!--
	<div class="col-md-12">

		<?php 
		if(!empty($_GET['informe_compras'])){
			$idinforme_general = $_GET['informe_compras'];
			//$row_informe = mysql_query("SELECT * FROM formato_compras WHERE idinforme_general = '$idinforme_general'", $dspp) or die(mysql_error());

		?>
			<table class="table table-bordered table-condensed" style="font-size:12px;">
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
						<th colspan="3">Distribución plataformas SPP | Informe general: <span style="color:#e74c3c"><?php echo $idinforme_general; ?></span></th>
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
					$transacciones = 0;
					$row_informe = mysql_query("SELECT informe_general.idinforme_general, informe_general.trim1, informe_general.trim2, informe_general.trim3, informe_general.trim4 FROM informe_general WHERE idinforme_general = '$idinforme_general'", $dspp) or die(mysql_error());
					$informe = mysql_fetch_assoc($row_informe);

					$row_reembolso = mysql_query("SELECT COUNT(idformato_compras) AS 'total_formatos', pais, SUM(total_a_pagar) AS 'compras_totales' FROM formato_compras WHERE pais = '$value' AND (formato_compras.idtrim = '$informe[trim1]' || formato_compras.idtrim = '$informe[trim2]' || formato_compras.idtrim = '$informe[trim3]' || formato_compras.idtrim = '$informe[trim4]')", $dspp) or die(mysql_error());
					$reembolso = mysql_fetch_assoc($row_reembolso);
					$transacciones = mysql_num_rows($row_reembolso);
				?>
					<tr>
						<td><?php echo $value; ?></td>
						<td><?php echo $reembolso['total_formatos']; ?></td>
						<td><?php if(empty($reembolso['compras_totales'])){ echo 0; }else{ echo round($reembolso['compras_totales'],2); } ?> USD</td>
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
			</table>
		<?php
		}else{
		?>
			<table class="table table-bordered table-condensed" style="font-size:12px;">
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
						<th colspan="3">Distribución plataformas SPP | <span style="color:#e74c3c">Concentrado general</span></th>
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
			</table>

		<?php
		}
		 ?>
	</div>
</div>-->
