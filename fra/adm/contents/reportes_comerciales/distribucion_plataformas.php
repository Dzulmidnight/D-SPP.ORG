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



$anio_actual = date('Y',time());
$row_configuracion = mysql_query("SELECT * FROM porcentaje_ajuste WHERE anio = $anio_actual", $dspp) or die(mysql_error());
$configuracion = mysql_fetch_assoc($row_configuracion);

//$plataformas_spp = mysql_fetch_assoc($row_plataformas);
//$plataformas_spp = array('Ecuador', 'Perú', 'Colombia', 'Guatemala');


$row_anio = mysql_query("SELECT FROM_UNIXTIME(ano,'%Y') AS 'anio' FROM informe_general GROUP BY FROM_UNIXTIME(ano,'%Y')", $dspp) or die(mysql_error());
?>
<hr style="margin-bottom:0;">
<div class="row">
	<div class="col-md-12">
		<a href="?REPORTES&distribucion_p=compras" class="btn btn-sm <?php if($_GET['distribucion_p'] == 'compras'){echo 'btn-primary'; }else{echo 'btn-default';} ?>">Compras</a>
		<a href="?REPORTES&distribucion_p=producto" class="btn btn-sm <?php if($_GET['distribucion_p'] == 'producto'){echo 'btn-primary'; }else{echo 'btn-default';} ?>">Producto Terminado</a>	
	</div>	
</div>

<?php 
if($_GET['distribucion_p'] == 'producto'){ /// SECCIÓN DE PRODUCTO TERMINADO
	$row_informes_producto = mysql_query("SELECT informe_general_producto.*, trim1_producto.total_trim1, trim2_producto.total_trim2, trim3_producto.total_trim3, trim4_producto.total_trim4, empresa.abreviacion FROM informe_general_producto INNER JOIN empresa ON informe_general_producto.idempresa = empresa.idempresa LEFT JOIN trim1_producto ON informe_general_producto.trim1_producto = trim1_producto.idtrim1_producto LEFT JOIN trim2_producto ON informe_general_producto.trim2_producto = trim2_producto.idtrim2_producto LEFT JOIN trim3_producto ON informe_general_producto.trim3_producto = trim3_producto.idtrim3_producto LEFT JOIN trim4_producto ON informe_general_producto.trim4_producto = trim4_producto.idtrim4_producto", $dspp) or die(mysql_error());
	//$plataformas_spp = array();
	$total_informes = mysql_num_rows($row_informes_producto);
	//$row_plataformas = mysql_query("SELECT * FROM plataformas_spp", $dspp) or die(mysql_error());
	/*while($array_plataformas = mysql_fetch_assoc($row_plataformas)){
		$plataformas_spp[] = $array_plataformas['pais'];
	}*/

?>
		<h4>
			Distribución plataformas Producto Terminado | Año
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
			$txt_id = 'idtrim'.$i.'_producto';
			$txt_trim = 'trim'.$i.'_producto';
			$txt_idtrim = 'TE'.$i.'-'.$anio_actual;
			$txt_estado = 'estado_trim'.$i;

			$clave_distribucion = 0;
			///calculamos las claves de distribucion, esto sumando el valor del contrato de cada formato del primer trimestre
			$row_formato_producto = mysql_query("SELECT SUM(ventas_totales) AS 'total_ventas' FROM formato_producto_empresa WHERE idtrim LIKE '%$txt_idtrim%'", $dspp) or die(mysql_error());
			$formato_producto = mysql_fetch_assoc($row_formato_producto);
			//$porcentaje_anual = $configuracion['distribucion_plataforma_origen'];
			$porcentaje_anual = 10;
			$cuota_uso = round(($formato_producto['total_ventas'] * $porcentaje_anual) / 100, 2);


			$row_trim = mysql_query("SELECT $txt_id FROM $txt_trim WHERE $txt_id LIKE '%$txt_idtrim%'", $dspp) or die(mysql_error());
			$num_trimestres = mysql_num_rows($row_trim);
			//echo '<br>TOTAL TRIMS: '.$num_trimestres;
			$row_trim = mysql_query("SELECT $txt_id FROM $txt_trim WHERE $txt_id LIKE '%$txt_idtrim%' AND $txt_estado = 'ACTIVO'", $dspp) or die(mysql_error());
			$num_activo = mysql_num_rows($row_trim);
			//echo '<br>TOTAL TRIMS activos: '.$num_activo;
			$row_trim = mysql_query("SELECT $txt_id FROM $txt_trim WHERE $txt_id LIKE '%$txt_idtrim%' AND $txt_estado = 'FINALIZADO'", $dspp) or die(mysql_error());
			$num_finalizado = mysql_num_rows($row_trim);
			//echo '<br>TOTAL TRIMS finalizados: '.$num_finalizado;
			

			if($num_trimestres != 0){
			?>
				<div class="row">
					<div class="col-md-8">
						<table class="table table-bordered" style="font-size:12px;">
							<thead>
								<tr>
									<th class="success"><b>Trimestre <?php echo $i; ?></b></th>
									<th class="info">Numero de informes: <span style="color:#e74c3c"><?php echo $num_trimestres; ?></span></th>
									<th class="info"><?php echo 'Activos: <span style="color:#e74c3c">'.$num_activo.'</span> Finalizados: <span style="color:#e74c3c">'.$num_finalizado.'</span>'; ?></th>
									<th class="info">Ventas totales: <span style="color:red"><?php echo number_format(round($formato_producto['total_ventas'],2)).' (<span style="color:#2c3e50">'.$porcentaje_anual.'%</span> = '.number_format($cuota_uso).')'; ?></span></th>
								</tr>
								<tr>
									<th>Plataforma</th>
									<th>Ventas totales</th>
									<th>Porcentaje Clave distribución</th>
									<th>Valor clave distribución</th>
								</tr>
							</thead>
							<tbody>
								<?php 

									$row_producto = mysql_query("SELECT COUNT(idformato_producto_empresa) AS 'total_formatos', SUM(formato_producto_empresa.ventas_totales) AS 'total' FROM formato_producto_empresa WHERE idtrim LIKE '%$txt_idtrim%' AND pais = 'Francia'", $dspp) or die(mysql_error());
									$formatos = mysql_fetch_assoc($row_producto);
									//$num_formatos = mysql_num_rows($row_plataformas);
									//$query = "SELECT * FROM formatos_empresa WHERE idtrim LIKE '%$txt_idtrim%' AND pais = 'Francia'";

									if($formatos['total'] > 0){
										$clave_distribucion = round(($formatos['total'] * 100) / $formato_producto['total_ventas'], 2);
										$valor_clave_distribucion = round(($cuota_uso * $clave_distribucion) / 100,2);
									}else{
										$clave_distribucion = 0;
										$valor_clave_distribucion = 0;
									}
								?>
								<tr>
									<td style="background-color:#27ae60;color:#ecf0f1"><?php echo 'Francia'; ?></td>
									<td>
										<?php 
										if(isset($formatos['total'])){
											echo number_format(round($formatos['total'],2)).' USD';
										}else{
											echo "0 USD";
										}
										?>
									</td>
									<td><?php echo $clave_distribucion.' %'; ?></td>
									<td><?php echo number_format($valor_clave_distribucion).' USD'; ?></td>
									
								</tr>

							</tbody>
						</table>						
					</div>
					<div class="col-md-4" id="<?php echo 'chart_div'.$i; ?>" style="margin-top:-2em;">
						<?php 
						$anio = date('Y',time());
						$id = 'TE'.$i.'-'.$anio;
						$row_total_trim_productos = mysql_query("SELECT pais, SUM(ventas_totales) AS 'total_ventas' FROM formato_producto_empresa WHERE idtrim LIKE '%$id%' GROUP BY pais", $dspp) or die(mysql_error());

						 ?>
						<script type="text/javascript" src="https://www.google.com/jsapi"></script>
						<script type="text/javascript">
							google.load('visualization', '1.0', {'packages':['corechart']});

							google.setOnLoadCallback(dibujaGrafica);

							function dibujaGrafica() {

							var data = new google.visualization.DataTable();

							data.addColumn('string', 'Pais');
							data.addColumn('number', 'Ventas');
							<?php
							echo "data.addRows(["; 

							while($pais = mysql_fetch_assoc($row_total_trim_productos)){
								echo "['$pais[pais]', $pais[total_ventas]],";
							}

							echo "]);";
							 ?>

							/*['Lunes', 50],
							['Martes', 61],
							['Miercoles', 55],
							['Jueves', 70],
							['Viernes', 42],
							['Sabado', 67],
							['Domingo', 52]*/
							

							var opciones = {'title':'Ventas por País',
							'width':400,
							'height':300};

							var chart = new google.visualization.PieChart(document.getElementById('<?php echo "chart_div".$i; ?>'));
							chart.draw(data, opciones);
							}
						</script>
					</div>

				</div>
			<?php
			}else{
			?>
				<div class="row">
					<div class="col-md-12">
						<table class="table">
							<thead>
								<tr>
									<th class="warning"><b>No se encontraron registros sobre el Trimestre <?php echo $i; ?></b></th>
								</tr>
							</thead>
						</table>
					</div>
				</div>
			<?php
			}
		}
////********************************************************** INICIA SECCIÓN DISTRIBUCIÓN COMPRAS ******************************************************************//////////////////
////****************************************************************************************************************************//////////////////
}else{ //// SECCIÓN INFORMES COMPRAS
	$row_informes = mysql_query("SELECT informe_general.*, trim1.total_trim1, trim2.total_trim2, trim3.total_trim3, trim4.total_trim4 FROM informe_general LEFT JOIN trim1 ON informe_general.trim1 = trim1.idtrim1 LEFT JOIN trim2 ON informe_general.trim2 = trim2.idtrim2 LEFT JOIN trim3 ON informe_general.trim3 = trim3.idtrim3 LEFT JOIN trim4 ON informe_general.trim4 = trim4.idtrim4", $dspp) or die(mysql_error());
	$plataformas_spp = array();
	$total_informes = mysql_num_rows($row_informes);
	$row_plataformas = mysql_query("SELECT * FROM plataformas_spp", $dspp) or die(mysql_error());

	while($array_plataformas = mysql_fetch_assoc($row_plataformas)){
		$plataformas_spp[] = $array_plataformas['pais'];
	}

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
		$txt_idtrim_opp = 'TO'.$i.'_'.$anio_actual;
		$txt_estado = 'estado_trim'.$i;



		$clave_distribucion = 0;
		///calculamos las claves de distribucion, esto sumando el valor del contrato de cada formato del primer trimestre
		$row_formato_compras = mysql_query("SELECT SUM(valor_total_contrato) AS 'total_contrato' FROM formato_compras WHERE idtrim LIKE '%$txt_idtrim%'", $dspp) or die(mysql_error());
		$formato_compras = mysql_fetch_assoc($row_formato_compras);
		$sum_total_contrato = $formato_compras['total_contrato'];

		$porcentaje_anual = $configuracion['distribucion_plataforma_origen'];
		$cuota_uso = round(($sum_total_contrato * $porcentaje_anual) / 100, 2);

		/// NUMERO DE INFORMES TRIMESTRALES DE LOS COMPRADORES FINALES
			$row_trim = mysql_query("SELECT $txt_id, idempresa FROM $txt_trim WHERE $txt_id LIKE '%$txt_idtrim%'", $dspp) or die(mysql_error());
			$num_informes = mysql_num_rows($row_trim);


		//NUMERO DE TRIMESTRES ACTIVOS - FINALIZADOS DE LOS COMPRADORES
			$row_trim_activo = mysql_query("SELECT $txt_id FROM $txt_trim WHERE $txt_id LIKE '%$txt_idtrim%' AND ($txt_estado = 'ACTIVO' OR $txt_estado = 'EN ESPERA' OR $txt_estado = 'APROBADO')", $dspp) or die(mysql_error());
			$num_activo = mysql_num_rows($row_trim_activo);

			$row_trim_finalizado = mysql_query("SELECT $txt_id FROM $txt_trim WHERE $txt_id LIKE '%$txt_idtrim%' AND $txt_estado = 'FINALIZADO'", $dspp) or die(mysql_error());
			$num_finalizado = mysql_num_rows($row_trim_finalizado);


		if($num_informes != 0){
		?>
			<div class="row">
				<div class="col-md-8">
					<table class="table table-bordered" style="font-size:12px;">
						<thead>
							<tr>
								<th rowspan="3" class="success"><b>Trimestre <?php echo $i; ?></b></th>
							</tr>

							<tr>
								<th class="info" colspan="2">
									Numero de informes Globales = <span style="color:#e74c3c"><?php echo $num_informes; ?></span>
								</th>
								<th class="info" colspan="2">
									Informes Activos = <span style="color:red"><?php echo $num_activo; ?></span>
								</th>
								<th class="info" colspan="2">
									Informes Finalizados = <span style="color:red"><?php echo $num_finalizado; ?></span>
								</th>
								<th class="info" colspan="2">
									<span style="background-color:#A4FFF1">
										Valor Global Contratos: ( <?php echo $configuracion['distribucion_plataforma_origen']; ?> % <?php echo number_format(round($sum_total_contrato,2)).' ) = $ <span style="color:red;font-size:16px;">'.number_format($cuota_uso).'</span> USD'; ?>
									</span>
								</th>

							</tr>
							<tr>
								<th class="text-center">Plataforma</th>
								<th colspan="2" class="text-center">Valor total contratos</th>
								<th colspan="4" class="text-center">Porcentaje Clave distribución</th>
								<th colspan="2" class="text-center">Valor clave distribución</th>
							</tr>
						</thead>
						<tbody>
							<?php 
							$row_plataformas = mysql_query("SELECT * FROM plataformas_spp", $dspp) or die(mysql_error());
							while($plataformas = mysql_fetch_assoc($row_plataformas)){
								//FORMATO COMPRAS(empresa)
								$row_formatos = mysql_query("SELECT COUNT(idformato_compras) AS 'total_formatos', SUM(formato_compras.valor_total_contrato) AS 'total_contrato' FROM formato_compras WHERE idtrim LIKE '%$txt_idtrim%' AND pais = '$plataformas[pais]'", $dspp) or die(mysql_error());
								$formatos = mysql_fetch_assoc($row_formatos);
								$num_formatos = mysql_num_rows($row_plataformas);

								$sum_formatos = $formatos['total_contrato'];
		 

								if($sum_formatos > 0){
									$clave_distribucion = round(($sum_formatos * 100) / $sum_total_contrato, 2);
									$valor_clave_distribucion = round(($cuota_uso * $clave_distribucion) / 100,2);
								}else{
									$clave_distribucion = 0;
									$valor_clave_distribucion = 0;
								}
							?>
							<tr>
								<td style="background-color:#27ae60;color:#ecf0f1"><?php echo $plataformas['pais']; ?></td>
								<!-- SE MUESTRA EL VALOR TOTAL DE LOS CONTRATO -->
								<td colspan="2">
									<?php 
									if(isset($sum_formatos)){
										echo number_format(round($sum_formatos,2)).' USD';
									}else{
										echo "0 USD";
									}
									?>
								</td>
								<!-- SE MUESTRA EL % DE LA CLAVE DE DISTRIBUCIÓN -->
								<td colspan="4">
									<?php echo $clave_distribucion.' %'; ?>
								</td>
								<!-- SE MUESTRA EL VALOR DE LA CLAVE DE DISTRIBUCIÓN -->
								<td colspan="2">
									<span style="background-color:#A4FFF1;font-weight:bold"><?php echo number_format($valor_clave_distribucion).' USD'; ?></span>
								</td>
								
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
				</div>
				<!---- INICIA ESTADISTICA PAISES DE LOS COMPRADORES ---->
				<div class="col-md-4" id="<?php echo 'div_grafica'.$i; ?>" style="margin-top:-2em;">

					<?php 

					$anio = date('Y',time());
					$id = 'T'.$i.'-'.$anio;
					$row_total_trim_productos = mysql_query("SELECT pais, SUM(valor_total_contrato) AS 'total_contrato' FROM formato_compras WHERE idtrim LIKE '%$id%' GROUP BY pais", $dspp) or die(mysql_error());

					 ?>
					<script type="text/javascript" src="https://www.google.com/jsapi"></script>
					<script type="text/javascript">
						google.load('visualization', '1.0', {'packages':['corechart']});

						google.setOnLoadCallback(dibujaGrafica);

						function dibujaGrafica() {

						var data = new google.visualization.DataTable();

						data.addColumn('string', 'Pais');
						data.addColumn('number', 'Ventas');
						<?php
						echo "data.addRows(["; 

						while($pais = mysql_fetch_assoc($row_total_trim_productos)){
							echo "['$pais[pais]', $pais[total_contrato]],";
						}

						echo "]);";
						 ?>

						/*['Lunes', 50],
						['Martes', 61],
						['Miercoles', 55],
						['Jueves', 70],
						['Viernes', 42],
						['Sabado', 67],
						['Domingo', 52]*/
						

						var opciones = {'title':'Compras por País',
						'width':400,
						'height':300};

						var chart = new google.visualization.PieChart(document.getElementById('<?php echo "div_grafica".$i; ?>'));
						chart.draw(data, opciones);
						}
					</script>

				</div>
				<!---- TERMINA ESTADISTICA PAISES DE LOS COMPRADORES ---->
			</div>
		<?php
		}else{
		?>
			<div class="row">
				<div class="col-md-12">
					<table class="table">
						<thead>
							<tr>
								<th class="warning"><b>No se encontraron registros sobre el Trimestre <?php echo $i; ?></b></th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		<?php
		}
	}
	 ?>
<?php
}
////********************************************************* TERMINA SECCION DISTRIBUCIÓN COMPRAS *******************************************************************//////////////////
////****************************************************************************************************************************//////////////////
 ?>