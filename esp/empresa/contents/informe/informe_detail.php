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
$idempresa = $_SESSION['idempresa'];
$query_informe = "SELECT formato_compras.*, intermediarios.primero, intermediarios.segundo, referencia_contrato.clave, referencia_contrato.fecha AS 'fecha_contrato', cantidad_total_contrato.peso AS 'c_t_c_peso', cantidad_total_contrato.unidad AS 'c_t_c_unidad', peso_total_reglamento.peso AS 'p_t_r_peso', peso_total_reglamento.unidad AS 'p_t_r_unidad', precio_total_unitario.precio AS 'p_t_u_precio', precio_total_unitario.unidad AS 'p_t_u_unidad', reconocimiento_organico.precio AS 'r_o_precio', reconocimiento_organico.unidad AS 'r_o_unidad', precio_sustentable.precio AS 'p_s_precio', precio_sustentable.unidad AS 'p_s_unidad', incentivo_spp.precio AS 'i_spp_precio', incentivo_spp.unidad AS 'i_spp_unidad', cuota_uso_reglamento.cuota AS 'c_u_r_cuota', cuota_uso_reglamento.unidad AS 'c_u_r_unidad' FROM formato_compras INNER JOIN intermediarios ON formato_compras.idformato_compras = intermediarios.idformato_compras INNER JOIN referencia_contrato ON formato_compras.idformato_compras = referencia_contrato.idformato_compras INNER JOIN cantidad_total_contrato ON formato_compras.idformato_compras = cantidad_total_contrato.idformato_compras INNER JOIN peso_total_reglamento ON formato_compras.idformato_compras = peso_total_reglamento.idformato_compras INNER JOIN precio_total_unitario ON formato_compras.idformato_compras = precio_total_unitario.idformato_compras INNER JOIN reconocimiento_organico ON formato_compras.idformato_compras = reconocimiento_organico.idformato_compras INNER JOIN precio_sustentable ON formato_compras.idformato_compras = precio_sustentable.idformato_compras INNER JOIN incentivo_spp ON formato_compras.idformato_compras = incentivo_spp.idformato_compras INNER JOIN cuota_uso_reglamento ON formato_compras.idformato_compras = cuota_uso_reglamento.idformato_compras WHERE idempresa = $idempresa";
$row_informe = mysql_query($query_informe, $dspp) or die(mysql_error());

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
		<th colspan="2" class="text-center">Cuota de Uso Reglamento</th>
		<th class="text-center">Total a Pagar</th>
		</tr>
	</thead>
	<tbody>
		<?php 
		$contador = 1;
		while($informe = mysql_fetch_assoc($row_informe)){
		?>
			<tr>
				<td><?php echo $contador; ?></td>
				<td><?php echo $informe['opp']; ?></td>
				<td><?php echo $informe['pais']; ?></td>
				<td><?php echo date('d/m/Y',$informe['fecha_compra']); ?></td>
				<td><?php echo $informe['primero']; ?></td>
				<td><?php echo $informe['segundo']; ?></td>
				<td><?php echo $informe['producto_general']; ?></td>
				<td><?php echo $informe['clave']; ?></td>
				<td><?php echo date('d/m/Y',$informe['fecha_contrato']); ?></td>
				<td><?php echo $informe['producto_especifico']; ?></td>
				<td><?php echo $informe['c_t_c_peso']; ?></td>
				<td><?php echo $informe['c_t_c_unidad']; ?></td>
				<td><?php echo $informe['p_t_r_peso']; ?></td>
				<td><?php echo $informe['p_t_r_unidad']; ?></td>
				<td><?php echo $informe['p_t_u_precio']; ?></td>
				<td><?php echo $informe['p_t_r_unidad']; ?></td>
				<td><?php echo $informe['p_s_precio']; ?></td>
				<td><?php echo $informe['p_s_unidad']; ?></td>
				<td><?php echo $informe['r_o_precio']; ?></td>
				<td><?php echo $informe['r_o_unidad']; ?></td>
				<td><?php echo $informe['i_spp_precio']; ?></td>
				<td><?php echo $informe['i_spp_unidad']; ?></td>
				<td><?php echo $informe['valor_total_contrato']; ?></td>
				<td><?php echo $informe['c_u_r_cuota']; ?></td>
				<td><?php echo $informe['c_u_r_unidad']; ?></td>
				<td style="background-color:#e74c3c;color:#ecf0f1;"><?php echo $informe['total']; ?></td>
			</tr>
		<?php
		$contador++;
		}
		 ?>
	</tbody>
</table>