<?php 
require_once('../Connections/dspp.php'); 
require_once('../Connections/mail.php');

mysql_select_db($database_dspp, $dspp);

if (!isset($_SESSION)) {
  session_start();
	
	$redireccion = "../index.php?EMPRESA";

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
$idempresa = $_SESSION['idempresa'];
//$query_informe = "SELECT formato_producto_empresa.*, intermediarios.primero, intermediarios.segundo, referencia_contrato.clave, referencia_contrato.fecha AS 'fecha_contrato', cantidad_total_contrato.peso AS 'c_t_c_peso', cantidad_total_contrato.unidad AS 'c_t_c_unidad', peso_total_reglamento.peso AS 'p_t_r_peso', peso_total_reglamento.unidad AS 'p_t_r_unidad', precio_total_unitario.precio AS 'p_t_u_precio', precio_total_unitario.unidad AS 'p_t_u_unidad', reconocimiento_organico.precio AS 'r_o_precio', reconocimiento_organico.unidad AS 'r_o_unidad', precio_sustentable.precio AS 'p_s_precio', precio_sustentable.unidad AS 'p_s_unidad', incentivo_spp.precio AS 'i_spp_precio', incentivo_spp.unidad AS 'i_spp_unidad', cuota_uso_reglamento.cuota AS 'c_u_r_cuota', cuota_uso_reglamento.unidad AS 'c_u_r_unidad' FROM formato_producto_empresa INNER JOIN intermediarios ON formato_producto_empresa.idformato_producto_empresa = intermediarios.idformato_producto_empresa INNER JOIN referencia_contrato ON formato_producto_empresa.idformato_producto_empresa = referencia_contrato.idformato_producto_empresa INNER JOIN cantidad_total_contrato ON formato_producto_empresa.idformato_producto_empresa = cantidad_total_contrato.idformato_producto_empresa INNER JOIN peso_total_reglamento ON formato_producto_empresa.idformato_producto_empresa = peso_total_reglamento.idformato_producto_empresa INNER JOIN precio_total_unitario ON formato_producto_empresa.idformato_producto_empresa = precio_total_unitario.idformato_producto_empresa INNER JOIN reconocimiento_organico ON formato_producto_empresa.idformato_producto_empresa = reconocimiento_organico.idformato_producto_empresa INNER JOIN precio_sustentable ON formato_producto_empresa.idformato_producto_empresa = precio_sustentable.idformato_producto_empresa INNER JOIN incentivo_spp ON formato_producto_empresa.idformato_producto_empresa = incentivo_spp.idformato_producto_empresa INNER JOIN cuota_uso_reglamento ON formato_producto_empresa.idformato_producto_empresa = cuota_uso_reglamento.idformato_producto_empresa WHERE idempresa = $idempresa";

//$query_informe = "SELECT * FROM "
//$row_informe_producto = mysql_query($query_informe, $dspp) or die(mysql_error());

$row_informe_producto = mysql_query("SELECT informe_general_producto.*, trim1_producto.total_trim1, trim2_producto.total_trim2, trim3_producto.total_trim3, trim4_producto.total_trim4, SUM(trim1_producto.total_trim1 + trim2_producto.total_trim2 + trim3_producto.total_trim3 + trim4_producto.total_trim4) AS 'balance_final' FROM informe_general_producto LEFT JOIN trim1_producto ON informe_general_producto.trim1_producto = trim1_producto.idtrim1_producto LEFT JOIN trim2_producto ON informe_general_producto.trim2_producto = trim2_producto.idtrim2_producto LEFT JOIN trim3_producto ON informe_general_producto.trim3_producto = trim3_producto.idtrim3_producto LEFT JOIN trim4_producto ON informe_general_producto.trim4_producto = trim4_producto.idtrim4_producto WHERE informe_general_producto.idempresa = $idempresa AND FROM_UNIXTIME(informe_general_producto.ano, '%Y') = $ano_actual", $dspp) or die(mysql_error());
$informe_general_producto = mysql_fetch_assoc($row_informe_producto);

?>

<h4>QUARTERLY REPORTS <span style="color:#e74c3c"><?php echo date('Y',$informe_general_producto['ano']); ?></span></h4>
<?php 
	if(!isset($informe_general['trim2'])){
		echo "<h4 class='alert alert-danger'><span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span> Por disposiciones de SPP Global debe iniciar la captura de transacciones en el \"Trimestre 2\".</h4>";
	}
 ?>
<table class="table table-bordered" style="font-size:11px;">
	<thead>
		<tr class="success">
			<th class="text-center">#</th>
			<th class="text-center">Country of destination of finished product</th>
			<th class="text-center">Type of currency</th>
			<th class="text-center">SPP Total Sales Value</th>
		</tr>
	</thead>
	<tbody>
		<?php 
		$balance_final = 0;

		if(isset($informe_general_producto['trim1_producto'])){
			$row_registro = mysql_query("SELECT formato_producto_empresa.* FROM formato_producto_empresa WHERE formato_producto_empresa.idtrim = '$informe_general_producto[trim1_producto]'", $dspp) or die(mysql_error());

			$contador = 1;
			$total_trim1 = 0;
			while($formato = mysql_fetch_assoc($row_registro)){
			?>
				<tr>
					<td><?php echo $contador; ?></td>
					<td><?php echo $formato['pais']; ?></td>
					<td><?php echo $formato['tipo_moneda']; ?></td>
					<td style="background-color:#e74c3c;color:#ecf0f1;"><?php echo $formato['ventas_totales']; ?></td>
				</tr>
			<?php
			$contador++;
			$total_trim1 = $total_trim1 + $formato['ventas_totales'];
			}
			echo "<tr>
				<td colspan='3' class='text-right warning'><h5>FIRST TRIMESTER</h5></td>
				<td style='background-color:#2c3e50;color:#ecf0f1' class='danger'>$total_trim1</td>
			</tr>";



		}
		if(isset($informe_general_producto['trim2_producto'])){
			$row_registro = mysql_query("SELECT formato_producto_empresa.* FROM formato_producto_empresa WHERE formato_producto_empresa.idtrim = '$informe_general_producto[trim2_producto]'");
			$contador = 1;
			$total_trim2 = 0;
			while($formato = mysql_fetch_assoc($row_registro)){
			?>
				<tr>
					<td><?php echo $contador; ?></td>
					<td><?php echo $formato['pais']; ?></td>
					
					<td><?php echo $formato['tipo_moneda']; ?></td>
					<td style="background-color:#e74c3c;color:#ecf0f1;"><?php echo $formato['ventas_totales']; ?></td>
				</tr>
			<?php
			$contador++;
			$total_trim2 = $total_trim2 + $formato['ventas_totales'];
			}
			echo "<tr>
				<td colspan='3' class='text-right warning'><h5>SECOND TRIMESTER</h5></td>
				<td style='background-color:#2c3e50;color:#ecf0f1' class='danger'>$total_trim2</td>
			</tr>";


		}
		if(isset($informe_general_producto['trim3_producto'])){
			$row_registro = mysql_query("SELECT formato_producto_empresa.* FROM formato_producto_empresa WHERE formato_producto_empresa.idtrim = '$informe_general_producto[trim3_producto]'");
			$contador = 1;
			$total_trim3 = 0;
			while($formato = mysql_fetch_assoc($row_registro)){
			?>
				<tr>
					<td><?php echo $contador; ?></td>
					<td><?php echo $formato['pais']; ?></td>
					
					<td><?php echo $formato['tipo_moneda']; ?></td>
					<td style="background-color:#e74c3c;color:#ecf0f1;"><?php echo $formato['ventas_totales']; ?></td>
				</tr>
			<?php
			$contador++;
			$total_trim3 = $total_trim3 + $formato['ventas_totales'];
			}
			echo "<tr>
				<td colspan='3' class='text-right warning'><h5>THIRD TRIMESTER</h5></td>
				<td style='background-color:#2c3e50;color:#ecf0f1' class='danger'>$total_trim3</td>
			</tr>";
		}
		if(isset($informe_general_producto['trim4_producto'])){
			$row_registro = mysql_query("SELECT formato_producto_empresa.* FROM formato_producto_empresa WHERE formato_producto_empresa.idtrim = '$informe_general_producto[trim4_producto]'");
			$contador = 1;
			$total_trim4 = 0;
			while($formato = mysql_fetch_assoc($row_registro)){
			?>
				<tr>
					<td><?php echo $contador; ?></td>
					<td><?php echo $formato['pais']; ?></td>
					
					<td><?php echo $formato['tipo_moneda']; ?></td>
					<td style="background-color:#e74c3c;color:#ecf0f1;"><?php echo $formato['ventas_totales']; ?></td>
				</tr>
			<?php
			$contador++;
			$total_trim4 = $total_trim4 + $formato['ventas_totales'];
			}
			echo "<tr>
				<td colspan='3' class='text-right warning'><h5>FOURTH TRIMESTER</h5></td>
				<td style='background-color:#2c3e50;color:#ecf0f1' class='danger'>$total_trim4</td>
			</tr>";
		}
		//$balance_final = $total_trim1 + $total_trim2 + $total_trim3 + $total_trim4;
		?>
		<tr>
			<td class="text-right" colspan="4">
				<h5>Current total: <span style="color:#c0392b"><?php echo $informe_general_producto['total_informe']; ?></span></h5>
			</td>
		</tr>
	</tbody>
</table>