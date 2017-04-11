<?php 
require_once('../Connections/dspp.php'); 
require_once('../Connections/mail.php');

mysql_select_db($database_dspp, $dspp);

if (!isset($_SESSION)) {
  session_start();
	
	$redireccion = "../index.php?opp";

	if(!$_SESSION["autentificado"]){
		header("Location:".$redireccion);
	}
}

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
$ano_actual = date('Y', time());
$idopp = $_SESSION['idopp'];
$row_opp = mysql_query("SELECT idopp, pais, abreviacion FROM opp WHERE idopp = $idopp", $dspp) or die(mysql_error());
$opp = mysql_fetch_assoc($row_opp);
//$query_informe = "SELECT formato_ventas.*, intermediarios.primero, intermediarios.segundo, referencia_contrato.clave, referencia_contrato.fecha AS 'fecha_contrato', cantidad_total_contrato.peso AS 'c_t_c_peso', cantidad_total_contrato.unidad AS 'c_t_c_unidad', peso_total_reglamento.peso AS 'p_t_r_peso', peso_total_reglamento.unidad AS 'p_t_r_unidad', precio_total_unitario.precio AS 'p_t_u_precio', precio_total_unitario.unidad AS 'p_t_u_unidad', reconocimiento_organico.precio AS 'r_o_precio', reconocimiento_organico.unidad AS 'r_o_unidad', precio_sustentable.precio AS 'p_s_precio', precio_sustentable.unidad AS 'p_s_unidad', incentivo_spp.precio AS 'i_spp_precio', incentivo_spp.unidad AS 'i_spp_unidad', cuota_uso_reglamento.cuota AS 'c_u_r_cuota', cuota_uso_reglamento.unidad AS 'c_u_r_unidad' FROM formato_ventas INNER JOIN intermediarios ON formato_ventas.idformato_ventas = intermediarios.idformato_ventas INNER JOIN referencia_contrato ON formato_ventas.idformato_ventas = referencia_contrato.idformato_ventas INNER JOIN cantidad_total_contrato ON formato_ventas.idformato_ventas = cantidad_total_contrato.idformato_ventas INNER JOIN peso_total_reglamento ON formato_ventas.idformato_ventas = peso_total_reglamento.idformato_ventas INNER JOIN precio_total_unitario ON formato_ventas.idformato_ventas = precio_total_unitario.idformato_ventas INNER JOIN reconocimiento_organico ON formato_ventas.idformato_ventas = reconocimiento_organico.idformato_ventas INNER JOIN precio_sustentable ON formato_ventas.idformato_ventas = precio_sustentable.idformato_ventas INNER JOIN incentivo_spp ON formato_ventas.idformato_ventas = incentivo_spp.idformato_ventas INNER JOIN cuota_uso_reglamento ON formato_ventas.idformato_ventas = cuota_uso_reglamento.idformato_ventas WHERE idopp = $idopp";

//$query_informe = "SELECT * FROM "
//$row_informe = mysql_query($query_informe, $dspp) or die(mysql_error());

$row_informe = mysql_query("SELECT informe_general.*, trim1.total_trim1, trim2.total_trim2, trim3.total_trim3, trim4.total_trim4, SUM(trim1.total_trim1 + trim2.total_trim2 + trim3.total_trim3 + trim4.total_trim4) AS 'balance_final' FROM informe_general LEFT JOIN trim1 ON informe_general.trim1 = trim1.idtrim1 LEFT JOIN trim2 ON informe_general.trim2 = trim2.idtrim2 LEFT JOIN trim3 ON informe_general.trim3 = trim3.idtrim3 LEFT JOIN trim4 ON informe_general.trim4 = trim4.idtrim4 WHERE informe_general.idopp = $idopp AND FROM_UNIXTIME(informe_general.ano, '%Y') = $ano_actual", $dspp) or die(mysql_error());
$informe_general = mysql_fetch_assoc($row_informe);



?>

<h4>INFORMES TRIMESTRALES <span style="color:#e74c3c"><?php echo date('Y',$informe_general['ano']); ?></span></h4>

<table class="table table-bordered" style="font-size:11px;">
	<thead>
		<tr class="success">
			<th class="text-center">#</th>
			<th class="text-center">País de la OPP</th>
			<th class="text-center">#SPP</th>
			<th class="text-center"><span style="color:red">Nombre del Comprador Final</span></th>
			<th class="text-center"><span style="color:red">País del Comprador Final</span></th>
			<th class="text-center">Fecha de Facturación</th>
			<th class="text-center">Primer Intermediario</th>
			<th class="text-center">Segundo Intermediario</th>
			<th colspan="2" class="text-center"><span style="color:red">Referencia del Contrato Original con el Comprador Final</span></th>
			<th class="text-center">Producto General</th>
			<th class="text-center">Producto Especifico</th>
			<th class="text-center">¿Producto terminado?</th>
			<th class="text-center">¿Se exporta?</th>
			<th colspan="2" class="text-center">Cantidad Total Conforme Factura</th>
			<th class="text-center">Precio Sustentable Mínimo</th>
			<th class="text-center">Reconocimiento Orgánico</th>
			<th class="text-center">Incentivo SPP</th>
			<th class="text-center">Otros premios</th>
			<th class="text-center">Precio Total Unitario pagado</th>
			<th class="text-center">Valor Total Contrato</th>
			<th class="text-center">Cuota de Uso Reglamento</th>
			<th class="text-center">Total a pagar</th>
		</tr>
	</thead>
	<tbody>
		<?php 
		$balance_final = 0;

		if(isset($informe_general['trim1'])){
			$row_registro = mysql_query("SELECT formato_ventas.* FROM formato_ventas WHERE formato_ventas.idtrim = '$informe_general[trim1]'", $dspp) or die(mysql_error());
			$total_contrato = 0;
			$contador = 1;
			$total_trim1 = 0;
			while($formato = mysql_fetch_assoc($row_registro)){
			?>
				<tr>
					<td><?php echo $contador; ?></td>
					<td><?php echo $opp['pais']; ?></td>
					<td><?php echo $formato['spp']; ?></td>
					<td><?php echo $formato['empresa']; ?></td>
					<td><?php echo $formato['pais_empresa']; ?></td>
					<td><?php echo date('d/m/Y',$formato['fecha_facturacion']); ?></td>
					<td><?php echo $formato['primer_intermediario']; ?></td>
					<td><?php echo $formato['segundo_intermediario']; ?></td>
					<td><?php echo $formato['clave_contrato']; ?></td>
					<td>
						<?php 
						if(isset($formato['fecha_contrato'])){
							echo date('d/m/Y', $formato['fecha_contrato']);
						}
					
						?>
					</td>
					<td><?php echo $formato['producto_general']; ?></td>
					<td><?php echo $formato['producto_especifico']; ?></td>
					<td><?php echo $formato['producto_terminado']; ?></td>
					<td><?php echo $formato['se_exporta']; ?></td>
					<td><?php echo $formato['unidad_cantidad_factura']; ?></td>
					<td><?php echo $formato['cantidad_total_factura']; ?></td>
					<td><?php echo $formato['precio_sustentable_minimo']; ?></td>
					<td><?php echo $formato['reconocimiento_organico']; ?></td>
					<td><?php echo $formato['incentivo_spp']; ?></td>
					<td><?php echo $formato['otros_premios']; ?></td>
					<td><?php echo number_format($formato['precio_total_unitario'],2); ?></td>
					<td><?php echo number_format($formato['valor_total_contrato'],2); ?></td>
					<td><?php echo $formato['cuota_uso_reglamento']; ?></td>
					<td style="background-color:#e74c3c;color:#ecf0f1;"><?php echo number_format($formato['total_a_pagar'],2); ?></td>
				</tr>
			<?php
			$contador++;
			$total_contrato = $total_contrato + $formato['valor_total_contrato'];
			$total_trim1 = $total_trim1 + $formato['total_a_pagar'];
			}
			echo "<tr>
				<td colspan='21' class='text-right warning'><h5>PRIMER TRIMESTRE</h5></td>
				<td style='background-color:#2c3e50;color:#ecf0f1' class='danger'>".number_format($total_contrato,2)."</td>
				<td class='warning'></td>
				<td style='background-color:#2c3e50;color:#ecf0f1' class='danger'>".number_format($total_trim1,2)."</td>
			</tr>";



		}
		if(isset($informe_general['trim2'])){
			$row_registro = mysql_query("SELECT formato_ventas.* FROM formato_ventas WHERE formato_ventas.idtrim = '$informe_general[trim2]'", $dspp) or die(mysql_error());
			$contador = 1;
			$total_contrato = 0;
			$total_trim2 = 0;
			while($formato = mysql_fetch_assoc($row_registro)){
			?>
				<tr>
					<td><?php echo $contador; ?></td>
					<td><?php echo $opp['pais_opp']; ?></td>
					<td><?php echo $formato['spp']; ?></td>
					<td><?php echo $formato['empresa']; ?></td>
					<td><?php echo $formato['pais_empresa']; ?></td>
					<td><?php echo date('d/m/Y',$formato['fecha_facturacion']); ?></td>
					<td><?php echo $formato['primer_intermediario']; ?></td>
					<td><?php echo $formato['segundo_intermediario']; ?></td>
					<td><?php echo $formato['clave_contrato']; ?></td>
					<td>
						<?php 
						if(isset($formato['fecha_contrato'])){
							echo date('d/m/Y', $formato['fecha_contrato']);
						}
					
						?>
					</td>
					<td><?php echo $formato['producto_general']; ?></td>
					<td><?php echo $formato['producto_especifico']; ?></td>
					<td><?php echo $formato['producto_terminado']; ?></td>
					<td><?php echo $formato['se_exporta']; ?></td>
					<td><?php echo $formato['unidad_cantidad_factura']; ?></td>
					<td><?php echo $formato['cantidad_total_factura']; ?></td>
					<td><?php echo $formato['precio_sustentable_minimo']; ?></td>
					<td><?php echo $formato['reconocimiento_organico']; ?></td>
					<td><?php echo $formato['incentivo_spp']; ?></td>
					<td><?php echo $formato['otros_premios']; ?></td>
					<td><?php echo number_format($formato['precio_total_unitario'],2); ?></td>
					<td><?php echo number_format($formato['valor_total_contrato'],2); ?></td>
					<td><?php echo $formato['cuota_uso_reglamento']; ?></td>
					<td style="background-color:#e74c3c;color:#ecf0f1;"><?php echo number_format($formato['total_a_pagar'],2); ?></td>
				</tr>
			<?php
			$contador++;
			$total_contrato = $total_contrato = $formato['valor_total_contrato'];
			$total_trim2 = $total_trim2 + $formato['total_a_pagar'];
			}

			echo "<tr>
				<td colspan='21' class='text-right warning'><h5>SEGUNDO TRIMESTRE</h5></td>
				<td style='background-color:#2c3e50;color:#ecf0f1' class='danger'>".number_format($total_contrato,2)."</td>
				<td class='warning'></td>
				<td style='background-color:#2c3e50;color:#ecf0f1' class='danger'>".number_format($total_trim2,2)."</td>
			</tr>";



		}
		if(isset($informe_general['trim3'])){
			$row_registro = mysql_query("SELECT formato_ventas.* FROM formato_ventas WHERE formato_ventas.idtrim = '$informe_general[trim3]'", $dspp) or die(mysql_error());
			$contador = 1;
			$total_contrato = 0;
			$total_trim3 = 0;
			while($formato = mysql_fetch_assoc($row_registro)){
			?>
				<tr>
					<td><?php echo $contador; ?></td>
					<td><?php echo $opp['pais_opp']; ?></td>
					<td><?php echo $formato['spp']; ?></td>
					<td><?php echo $formato['empresa']; ?></td>
					<td><?php echo $formato['pais_empresa']; ?></td>
					<td><?php echo date('d/m/Y',$formato['fecha_facturacion']); ?></td>
					<td><?php echo $formato['primer_intermediario']; ?></td>
					<td><?php echo $formato['segundo_intermediario']; ?></td>
					<td><?php echo $formato['clave_contrato']; ?></td>
					<td>
						<?php 
						if(isset($formato['fecha_contrato'])){
							echo date('d/m/Y', $formato['fecha_contrato']);
						}
					
						?>
					</td>
					<td><?php echo $formato['producto_general']; ?></td>
					<td><?php echo $formato['producto_especifico']; ?></td>
					<td><?php echo $formato['producto_terminado']; ?></td>
					<td><?php echo $formato['se_exporta']; ?></td>
					<td><?php echo $formato['unidad_cantidad_factura']; ?></td>
					<td><?php echo $formato['cantidad_total_factura']; ?></td>
					<td><?php echo $formato['precio_sustentable_minimo']; ?></td>
					<td><?php echo $formato['reconocimiento_organico']; ?></td>
					<td><?php echo $formato['incentivo_spp']; ?></td>
					<td><?php echo $formato['otros_premios']; ?></td>
					<td><?php echo number_format($formato['precio_total_unitario'],2); ?></td>
					<td><?php echo number_format($formato['valor_total_contrato'],2); ?></td>
					<td><?php echo $formato['cuota_uso_reglamento']; ?></td>
					<td style="background-color:#e74c3c;color:#ecf0f1;"><?php echo number_format($formato['total_a_pagar'],2); ?></td>
				</tr>
			<?php
			$contador++;
			$total_contrato = $total_contrato = $formato['valor_total_contrato'];
			$total_trim3 = $total_trim3 + $formato['total_a_pagar'];
			}

			echo "<tr>
				<td colspan='21' class='text-right warning'><h5>TERCER TRIMESTRE</h5></td>
				<td style='background-color:#2c3e50;color:#ecf0f1' class='danger'>".number_format($total_contrato,2)."</td>
				<td class='warning'></td>
				<td style='background-color:#2c3e50;color:#ecf0f1' class='danger'>".number_format($total_trim3,2)."</td>
			</tr>";

		}
		if(isset($informe_general['trim4'])){
			$row_registro = mysql_query("SELECT formato_ventas.* FROM formato_ventas WHERE formato_ventas.idtrim = '$informe_general[trim4]'", $dspp) or die(mysql_error());
			$contador = 1;
			$total_contrato = 0;
			$total_trim4 = 0;
			while($formato = mysql_fetch_assoc($row_registro)){
			?>
				<tr>
					<td><?php echo $contador; ?></td>
					<td><?php echo $opp['pais_opp']; ?></td>
					<td><?php echo $formato['spp']; ?></td>
					<td><?php echo $formato['empresa']; ?></td>
					<td><?php echo $formato['pais_empresa']; ?></td>
					<td><?php echo date('d/m/Y',$formato['fecha_facturacion']); ?></td>
					<td><?php echo $formato['primer_intermediario']; ?></td>
					<td><?php echo $formato['segundo_intermediario']; ?></td>
					<td><?php echo $formato['clave_contrato']; ?></td>
					<td>
						<?php 
						if(isset($formato['fecha_contrato'])){
							echo date('d/m/Y', $formato['fecha_contrato']);
						}
					
						?>
					</td>
					<td><?php echo $formato['producto_general']; ?></td>
					<td><?php echo $formato['producto_especifico']; ?></td>
					<td><?php echo $formato['producto_terminado']; ?></td>
					<td><?php echo $formato['se_exporta']; ?></td>
					<td><?php echo $formato['unidad_cantidad_factura']; ?></td>
					<td><?php echo $formato['cantidad_total_factura']; ?></td>
					<td><?php echo $formato['precio_sustentable_minimo']; ?></td>
					<td><?php echo $formato['reconocimiento_organico']; ?></td>
					<td><?php echo $formato['incentivo_spp']; ?></td>
					<td><?php echo $formato['otros_premios']; ?></td>
					<td><?php echo number_format($formato['precio_total_unitario']); ?></td>
					<td><?php echo number_format($formato['valor_total_contrato']); ?></td>
					<td><?php echo $formato['cuota_uso_reglamento']; ?></td>
					<td style="background-color:#e74c3c;color:#ecf0f1;"><?php echo number_format($formato['total_a_pagar'],2); ?></td>
				</tr>
			<?php
			$contador++;
			$total_contrato = $total_contrato = $formato['valor_total_contrato'];
			$total_trim4 = $total_trim4 + $formato['total_a_pagar'];
			}

			echo "<tr>
				<td colspan='21' class='text-right warning'><h5>CUARTO TRIMESTRE</h5></td>
				<td style='background-color:#2c3e50;color:#ecf0f1' class='danger'>".number_format($total_contrato,2)."</td>
				<td class='warning'></td>
				<td style='background-color:#2c3e50;color:#ecf0f1' class='danger'>".number_format($total_trim4,2)."</td>
			</tr>";

		}
		//$balance_final = $total_trim1 + $total_trim2 + $total_trim3 + $total_trim4;
		?>
		<tr>
			<td class="text-right" colspan="27">
				<h5>Total actual: <span style="color:#c0392b"><?php echo number_format($informe_general['total_cuota_uso'],2); ?> USD</span></h5>
			</td>
		</tr>
	</tbody>
</table>