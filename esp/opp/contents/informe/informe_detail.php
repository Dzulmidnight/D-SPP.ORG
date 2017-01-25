<?php 
require_once('../Connections/dspp.php'); 
require_once('../Connections/mail.php');

mysql_select_db($database_dspp, $dspp);

if (!isset($_SESSION)) {
  session_start();
	
	$redireccion = "../index.php?OPP";

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
//$query_informe = "SELECT formato_ventas.*, intermediarios.primero, intermediarios.segundo, referencia_contrato.clave, referencia_contrato.fecha AS 'fecha_contrato', cantidad_total_contrato.peso AS 'c_t_c_peso', cantidad_total_contrato.unidad AS 'c_t_c_unidad', peso_total_reglamento.peso AS 'p_t_r_peso', peso_total_reglamento.unidad AS 'p_t_r_unidad', precio_total_unitario.precio AS 'p_t_u_precio', precio_total_unitario.unidad AS 'p_t_u_unidad', reconocimiento_organico.precio AS 'r_o_precio', reconocimiento_organico.unidad AS 'r_o_unidad', precio_sustentable.precio AS 'p_s_precio', precio_sustentable.unidad AS 'p_s_unidad', incentivo_spp.precio AS 'i_spp_precio', incentivo_spp.unidad AS 'i_spp_unidad', cuota_uso_reglamento.cuota AS 'c_u_r_cuota', cuota_uso_reglamento.unidad AS 'c_u_r_unidad' FROM formato_ventas INNER JOIN intermediarios ON formato_ventas.idformato_ventas = intermediarios.idformato_ventas INNER JOIN referencia_contrato ON formato_ventas.idformato_ventas = referencia_contrato.idformato_ventas INNER JOIN cantidad_total_contrato ON formato_ventas.idformato_ventas = cantidad_total_contrato.idformato_ventas INNER JOIN peso_total_reglamento ON formato_ventas.idformato_ventas = peso_total_reglamento.idformato_ventas INNER JOIN precio_total_unitario ON formato_ventas.idformato_ventas = precio_total_unitario.idformato_ventas INNER JOIN reconocimiento_organico ON formato_ventas.idformato_ventas = reconocimiento_organico.idformato_ventas INNER JOIN precio_sustentable ON formato_ventas.idformato_ventas = precio_sustentable.idformato_ventas INNER JOIN incentivo_spp ON formato_ventas.idformato_ventas = incentivo_spp.idformato_ventas INNER JOIN cuota_uso_reglamento ON formato_ventas.idformato_ventas = cuota_uso_reglamento.idformato_ventas WHERE idopp = $idopp";

//$query_informe = "SELECT * FROM "
//$row_informe = mysql_query($query_informe, $dspp) or die(mysql_error());

$row_informe = mysql_query("SELECT * FROM informe_general WHERE idopp = $idopp AND FROM_UNIXTIME(ano, '%Y') = $ano_actual", $dspp) or die(mysql_error());
$informe_general = mysql_fetch_assoc($row_informe);


?>

<h3>INFORME TRIMESTRAL GENERAL</h3>

<table class="table table-bordered" style="font-size:11px;">
	<thead>
		<tr class="success">
		<th class="text-center">#</th>
		<th class="text-center">OPP</th>
		<th class="text-center">País de la OPP</th>
		<th class="text-center">Fecha de Compra</th>
		<th class="text-center">Primer Intermediario</th>
		<th class="text-center">Segundo Intermediario</th>
		<th class="text-center">Tipo de Producto</th>
		<th colspan="2" class="text-center">Referencia Contrato Original con OPP</th>
		<th class="text-center">Producto Especifico de acuerdo al contrato original</th>
		<th colspan="2" class="text-center">Cantidad Total Conforme Contrato</th>
		<th colspan="2" class="text-center">Peso Total Conforme Unidad de Medida Reglamento de Uso</th>
		<th colspan="2" class="text-center">Precio Total Unitario</th>
		<th colspan="2" class="text-center">Precio Sustentable Minimo</th>
		<th colspan="2" class="text-center">Reconocimiento Orgánico</th>
		<th colspan="2" class="text-center">Incentivo SPP</th>
		<th class="text-center">Valor Total Contrato</th>
		</tr>
	</thead>
	<tbody>
		<?php 
		$total_final = 0;
		if(isset($informe_general['trim1'])){
			$row_registro = mysql_query("SELECT formato_ventas.idtrim, formato_ventas.comprador, formato_ventas.pais, formato_ventas.fecha_compra, formato_ventas.producto_general, formato_ventas.producto_especifico, formato_ventas.total_contrato, intermediarios.primero, intermediarios.segundo, referencia_contrato.clave, referencia_contrato.fecha AS 'fecha_contrato', cantidad_total_contrato.peso AS 'ctc_peso', cantidad_total_contrato.unidad AS 'ctc_unidad', peso_total_reglamento.peso AS 'ptr_peso', peso_total_reglamento.unidad AS 'ptr_unidad', precio_total_unitario.precio AS 'ptu_precio', precio_total_unitario.unidad AS 'ptu_unidad', precio_sustentable.precio AS 'ps_precio', precio_sustentable.unidad AS 'ps_unidad', reconocimiento_organico.precio AS 'ro_precio', reconocimiento_organico.unidad AS 'ro_unidad', incentivo_spp.precio AS 'incentivo_precio', incentivo_spp.unidad AS 'incentivo_unidad' FROM formato_ventas INNER JOIN intermediarios ON formato_ventas.idformato_ventas = intermediarios.idformato_ventas INNER JOIN referencia_contrato ON formato_ventas.idformato_ventas = referencia_contrato.idformato_ventas INNER JOIN cantidad_total_contrato ON formato_ventas.idformato_ventas = cantidad_total_contrato.idformato_ventas INNER JOIN peso_total_reglamento ON formato_ventas.idformato_ventas = peso_total_reglamento.idformato_ventas INNER JOIN precio_total_unitario ON formato_ventas.idformato_ventas = precio_total_unitario.idformato_ventas INNER JOIN precio_sustentable ON formato_ventas.idformato_ventas = precio_sustentable.idformato_ventas INNER JOIN reconocimiento_organico ON formato_ventas.idformato_ventas = reconocimiento_organico.idformato_ventas INNER JOIN incentivo_spp ON formato_ventas.idformato_ventas = incentivo_spp.idformato_ventas WHERE formato_ventas.idtrim  = '$informe_general[trim1]'");
			$contador = 1;
			$total_trim1 = 0;
			while($informacion_formato = mysql_fetch_assoc($row_registro)){
			?>
				<tr>
					<td><?php echo $contador; ?></td>
					<td><?php echo $informacion_formato['comprador']; ?></td>
					<td><?php echo $informacion_formato['pais']; ?></td>
					<td><?php echo date('d/m/Y',$informacion_formato['fecha_compra']); ?></td>
					<td><?php echo $informacion_formato['primero']; ?></td>
					<td><?php echo $informacion_formato['segundo']; ?></td>
					<td><?php echo $informacion_formato['producto_general']; ?></td>
					<td><?php echo $informacion_formato['clave']; ?></td>
					<td><?php echo date('d/m/Y',$informacion_formato['fecha_contrato']); ?></td>
					<td><?php echo $informacion_formato['producto_especifico']; ?></td>
					<td><?php echo $informacion_formato['ctc_peso']; ?></td>
					<td><?php echo $informacion_formato['ctc_unidad']; ?></td>
					<td><?php echo $informacion_formato['ptr_peso']; ?></td>
					<td><?php echo $informacion_formato['ptr_unidad']; ?></td>
					<td><?php echo $informacion_formato['ptu_precio']; ?></td>
					<td><?php echo $informacion_formato['ptu_unidad']; ?></td>
					<td><?php echo $informacion_formato['ps_precio']; ?></td>
					<td><?php echo $informacion_formato['ps_unidad']; ?></td>
					<td><?php echo $informacion_formato['ro_precio']; ?></td>
					<td><?php echo $informacion_formato['ro_unidad']; ?></td>
					<td><?php echo $informacion_formato['incentivo_precio']; ?></td>
					<td><?php echo $informacion_formato['incentivo_unidad']; ?></td>
					<td style="background-color:#e74c3c;color:#ecf0f1;"><?php echo $informacion_formato['total_contrato']; ?></td>
				</tr>
			<?php
			$contador++;
			$total_trim1 = $total_trim1 + $informacion_formato['total_contrato'];
			}
			echo "<tr>
				<td colspan='22' class='text-right warning'>Primer Trimestre</td>
				<td style='background-color:#2c3e50;color:#ecf0f1' class='danger'>$total_trim1</td>
			</tr>";



		}
		if(isset($informe_general['trim2'])){
			$row_registro = mysql_query("SELECT formato_ventas.idtrim, formato_ventas.comprador, formato_ventas.pais, formato_ventas.fecha_compra, formato_ventas.producto_general, formato_ventas.producto_especifico, formato_ventas.total_contrato, intermediarios.primero, intermediarios.segundo, referencia_contrato.clave, referencia_contrato.fecha AS 'fecha_contrato', cantidad_total_contrato.peso AS 'ctc_peso', cantidad_total_contrato.unidad AS 'ctc_unidad', peso_total_reglamento.peso AS 'ptr_peso', peso_total_reglamento.unidad AS 'ptr_unidad', precio_total_unitario.precio AS 'ptu_precio', precio_total_unitario.unidad AS 'ptu_unidad', precio_sustentable.precio AS 'ps_precio', precio_sustentable.unidad AS 'ps_unidad', reconocimiento_organico.precio AS 'ro_precio', reconocimiento_organico.unidad AS 'ro_unidad', incentivo_spp.precio AS 'incentivo_precio', incentivo_spp.unidad AS 'incentivo_unidad' FROM formato_ventas INNER JOIN intermediarios ON formato_ventas.idformato_ventas = intermediarios.idformato_ventas INNER JOIN referencia_contrato ON formato_ventas.idformato_ventas = referencia_contrato.idformato_ventas INNER JOIN cantidad_total_contrato ON formato_ventas.idformato_ventas = cantidad_total_contrato.idformato_ventas INNER JOIN peso_total_reglamento ON formato_ventas.idformato_ventas = peso_total_reglamento.idformato_ventas INNER JOIN precio_total_unitario ON formato_ventas.idformato_ventas = precio_total_unitario.idformato_ventas INNER JOIN precio_sustentable ON formato_ventas.idformato_ventas = precio_sustentable.idformato_ventas INNER JOIN reconocimiento_organico ON formato_ventas.idformato_ventas = reconocimiento_organico.idformato_ventas INNER JOIN incentivo_spp ON formato_ventas.idformato_ventas = incentivo_spp.idformato_ventas WHERE formato_ventas.idtrim  = '$informe_general[trim2]'");
			$contador = 1;
			$total_trim2 = 0;
			while($informacion_formato = mysql_fetch_assoc($row_registro)){
			?>
				<tr>
					<td><?php echo $contador; ?></td>
					<td><?php echo $informacion_formato['comprador']; ?></td>
					<td><?php echo $informacion_formato['pais']; ?></td>
					<td><?php echo date('d/m/Y',$informacion_formato['fecha_compra']); ?></td>
					<td><?php echo $informacion_formato['primero']; ?></td>
					<td><?php echo $informacion_formato['segundo']; ?></td>
					<td><?php echo $informacion_formato['producto_general']; ?></td>
					<td><?php echo $informacion_formato['clave']; ?></td>
					<td><?php echo date('d/m/Y',$informacion_formato['fecha_contrato']); ?></td>
					<td><?php echo $informacion_formato['producto_especifico']; ?></td>
					<td><?php echo $informacion_formato['ctc_peso']; ?></td>
					<td><?php echo $informacion_formato['ctc_unidad']; ?></td>
					<td><?php echo $informacion_formato['ptr_peso']; ?></td>
					<td><?php echo $informacion_formato['ptr_unidad']; ?></td>
					<td><?php echo $informacion_formato['ptu_precio']; ?></td>
					<td><?php echo $informacion_formato['ptu_unidad']; ?></td>
					<td><?php echo $informacion_formato['ps_precio']; ?></td>
					<td><?php echo $informacion_formato['ps_unidad']; ?></td>
					<td><?php echo $informacion_formato['ro_precio']; ?></td>
					<td><?php echo $informacion_formato['ro_unidad']; ?></td>
					<td><?php echo $informacion_formato['incentivo_precio']; ?></td>
					<td><?php echo $informacion_formato['incentivo_unidad']; ?></td>
					<td style="background-color:#e74c3c;color:#ecf0f1;"><?php echo $informacion_formato['total_contrato']; ?></td>
				</tr>
			<?php
			$contador++;
			$total_trim2 = $total_trim2 + $informacion_formato['total_contrato'];
			}
			echo "<tr>
				<td colspan='22' class='text-right warning'>Segundo Trimestre</td>
				<td style='background-color:#2c3e50;color:#ecf0f1' class='danger'>$total_trim2</td>
			</tr>";


		}
		if(isset($informe_general['trim3'])){
			$row_registro = mysql_query("SELECT formato_ventas.idtrim, formato_ventas.comprador, formato_ventas.pais, formato_ventas.fecha_compra, formato_ventas.producto_general, formato_ventas.producto_especifico, formato_ventas.total_contrato, intermediarios.primero, intermediarios.segundo, referencia_contrato.clave, referencia_contrato.fecha AS 'fecha_contrato', cantidad_total_contrato.peso AS 'ctc_peso', cantidad_total_contrato.unidad AS 'ctc_unidad', peso_total_reglamento.peso AS 'ptr_peso', peso_total_reglamento.unidad AS 'ptr_unidad', precio_total_unitario.precio AS 'ptu_precio', precio_total_unitario.unidad AS 'ptu_unidad', precio_sustentable.precio AS 'ps_precio', precio_sustentable.unidad AS 'ps_unidad', reconocimiento_organico.precio AS 'ro_precio', reconocimiento_organico.unidad AS 'ro_unidad', incentivo_spp.precio AS 'incentivo_precio', incentivo_spp.unidad AS 'incentivo_unidad' FROM formato_ventas INNER JOIN intermediarios ON formato_ventas.idformato_ventas = intermediarios.idformato_ventas INNER JOIN referencia_contrato ON formato_ventas.idformato_ventas = referencia_contrato.idformato_ventas INNER JOIN cantidad_total_contrato ON formato_ventas.idformato_ventas = cantidad_total_contrato.idformato_ventas INNER JOIN peso_total_reglamento ON formato_ventas.idformato_ventas = peso_total_reglamento.idformato_ventas INNER JOIN precio_total_unitario ON formato_ventas.idformato_ventas = precio_total_unitario.idformato_ventas INNER JOIN precio_sustentable ON formato_ventas.idformato_ventas = precio_sustentable.idformato_ventas INNER JOIN reconocimiento_organico ON formato_ventas.idformato_ventas = reconocimiento_organico.idformato_ventas INNER JOIN incentivo_spp ON formato_ventas.idformato_ventas = incentivo_spp.idformato_ventas WHERE formato_ventas.idtrim  = '$informe_general[trim3]'");
			$contador = 1;
			$total_trim3 = 0;
			while($informacion_formato = mysql_fetch_assoc($row_registro)){
			?>
				<tr>
					<td><?php echo $contador; ?></td>
					<td><?php echo $informacion_formato['comprador']; ?></td>
					<td><?php echo $informacion_formato['pais']; ?></td>
					<td><?php echo date('d/m/Y',$informacion_formato['fecha_compra']); ?></td>
					<td><?php echo $informacion_formato['primero']; ?></td>
					<td><?php echo $informacion_formato['segundo']; ?></td>
					<td><?php echo $informacion_formato['producto_general']; ?></td>
					<td><?php echo $informacion_formato['clave']; ?></td>
					<td><?php echo date('d/m/Y',$informacion_formato['fecha_contrato']); ?></td>
					<td><?php echo $informacion_formato['producto_especifico']; ?></td>
					<td><?php echo $informacion_formato['ctc_peso']; ?></td>
					<td><?php echo $informacion_formato['ctc_unidad']; ?></td>
					<td><?php echo $informacion_formato['ptr_peso']; ?></td>
					<td><?php echo $informacion_formato['ptr_unidad']; ?></td>
					<td><?php echo $informacion_formato['ptu_precio']; ?></td>
					<td><?php echo $informacion_formato['ptu_unidad']; ?></td>
					<td><?php echo $informacion_formato['ps_precio']; ?></td>
					<td><?php echo $informacion_formato['ps_unidad']; ?></td>
					<td><?php echo $informacion_formato['ro_precio']; ?></td>
					<td><?php echo $informacion_formato['ro_unidad']; ?></td>
					<td><?php echo $informacion_formato['incentivo_precio']; ?></td>
					<td><?php echo $informacion_formato['incentivo_unidad']; ?></td>
					<td style="background-color:#e74c3c;color:#ecf0f1;"><?php echo $informacion_formato['total_contrato']; ?></td>
				</tr>
			<?php
			$contador++;
			$total_trim3 = $total_trim3 + $informacion_formato['total_contrato'];
			}
			echo "<tr>
				<td colspan='22' class='text-right warning'>Tercer Trimestre</td>
				<td style='background-color:#2c3e50;color:#ecf0f1' class='danger'>$total_trim3</td>
			</tr>";
		}
		if(isset($informe_general['trim4'])){
			$row_registro = mysql_query("SELECT formato_ventas.idtrim, formato_ventas.comprador, formato_ventas.pais, formato_ventas.fecha_compra, formato_ventas.producto_general, formato_ventas.producto_especifico, formato_ventas.total_contrato, intermediarios.primero, intermediarios.segundo, referencia_contrato.clave, referencia_contrato.fecha AS 'fecha_contrato', cantidad_total_contrato.peso AS 'ctc_peso', cantidad_total_contrato.unidad AS 'ctc_unidad', peso_total_reglamento.peso AS 'ptr_peso', peso_total_reglamento.unidad AS 'ptr_unidad', precio_total_unitario.precio AS 'ptu_precio', precio_total_unitario.unidad AS 'ptu_unidad', precio_sustentable.precio AS 'ps_precio', precio_sustentable.unidad AS 'ps_unidad', reconocimiento_organico.precio AS 'ro_precio', reconocimiento_organico.unidad AS 'ro_unidad', incentivo_spp.precio AS 'incentivo_precio', incentivo_spp.unidad AS 'incentivo_unidad' FROM formato_ventas INNER JOIN intermediarios ON formato_ventas.idformato_ventas = intermediarios.idformato_ventas INNER JOIN referencia_contrato ON formato_ventas.idformato_ventas = referencia_contrato.idformato_ventas INNER JOIN cantidad_total_contrato ON formato_ventas.idformato_ventas = cantidad_total_contrato.idformato_ventas INNER JOIN peso_total_reglamento ON formato_ventas.idformato_ventas = peso_total_reglamento.idformato_ventas INNER JOIN precio_total_unitario ON formato_ventas.idformato_ventas = precio_total_unitario.idformato_ventas INNER JOIN precio_sustentable ON formato_ventas.idformato_ventas = precio_sustentable.idformato_ventas INNER JOIN reconocimiento_organico ON formato_ventas.idformato_ventas = reconocimiento_organico.idformato_ventas INNER JOIN incentivo_spp ON formato_ventas.idformato_ventas = incentivo_spp.idformato_ventas WHERE formato_ventas.idtrim  = '$informe_general[trim4]'");
			$contador = 1;
			$total_trim4 = 0;
			while($informacion_formato = mysql_fetch_assoc($row_registro)){
			?>
				<tr>
					<td><?php echo $contador; ?></td>
					<td><?php echo $informacion_formato['comprador']; ?></td>
					<td><?php echo $informacion_formato['pais']; ?></td>
					<td><?php echo date('d/m/Y',$informacion_formato['fecha_compra']); ?></td>
					<td><?php echo $informacion_formato['primero']; ?></td>
					<td><?php echo $informacion_formato['segundo']; ?></td>
					<td><?php echo $informacion_formato['producto_general']; ?></td>
					<td><?php echo $informacion_formato['clave']; ?></td>
					<td><?php echo date('d/m/Y',$informacion_formato['fecha_contrato']); ?></td>
					<td><?php echo $informacion_formato['producto_especifico']; ?></td>
					<td><?php echo $informacion_formato['ctc_peso']; ?></td>
					<td><?php echo $informacion_formato['ctc_unidad']; ?></td>
					<td><?php echo $informacion_formato['ptr_peso']; ?></td>
					<td><?php echo $informacion_formato['ptr_unidad']; ?></td>
					<td><?php echo $informacion_formato['ptu_precio']; ?></td>
					<td><?php echo $informacion_formato['ptu_unidad']; ?></td>
					<td><?php echo $informacion_formato['ps_precio']; ?></td>
					<td><?php echo $informacion_formato['ps_unidad']; ?></td>
					<td><?php echo $informacion_formato['ro_precio']; ?></td>
					<td><?php echo $informacion_formato['ro_unidad']; ?></td>
					<td><?php echo $informacion_formato['incentivo_precio']; ?></td>
					<td><?php echo $informacion_formato['incentivo_unidad']; ?></td>
					<td style="background-color:#e74c3c;color:#ecf0f1;"><?php echo $informacion_formato['total_contrato']; ?></td>
				</tr>
			<?php
			$contador++;
			$total_trim4 = $total_trim4 + $informacion_formato['total_contrato'];
			}
			echo "<tr>
				<td colspan='22' class='text-right warning'>Cuarto Trimestre</td>
				<td style='background-color:#2c3e50;color:#ecf0f1' class='danger'>$total_trim4</td>
			</tr>";
		}
		?>
		<tr>
			<td class="text-right" colspan="27">
				
			</td>
		</tr>
	</tbody>
</table>