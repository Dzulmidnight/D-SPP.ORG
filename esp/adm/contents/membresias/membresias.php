<?php 
require_once('../Connections/dspp.php');
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
mysql_select_db($database_dspp, $dspp);


//$row_membresias = mysql_query("SELECT membresia.* FROM membresia ORDER BY membresia.idmembresia DESC", $dspp) or die(mysql_error());
$row_membresias = mysql_query("SELECT opp.abreviacion, membresia.*, comprobante_pago.monto, comprobante_pago.archivo FROM membresia INNER JOIN opp ON membresia.idopp = opp.idopp INNER JOIN comprobante_pago ON membresia.idcomprobante_pago = comprobante_pago.idcomprobante_pago ORDER BY membresia.idmembresia DESC", $dspp) or die(mysql_error());

?>

Membresias OPP


<table class="table table-bordered table-condensed" style="font-size:12px;">
	<thead>
		<tr>
			<th>ID</th>
			<th>Comprobante</th>
			<th>OPP</th>
			<th>Monto membresia</th>
			<th>Estatus membresia</th>
			<th>Fecha de activación</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$estatus = '';
		$contador = 1;
		while($membresias = mysql_fetch_assoc($row_membresias)){
			if($membresias['estatus_membresia'] == 'EN ESPERA'){
				$estatus = 'danger';
			}else{
				$estatus = 'success';
			}
		?>
			<tr>
				<td><?php echo $contador.' - '.$membresias['idmembresia']; ?></td>
				<td>
					<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
					<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
					<?php 
					if(isset($membresias['archivo'])){
					?>
						<a href="<?php echo $membresias['archivo']; ?>" target="_blank"><span class="glyphicon glyphicon-file" aria-hidden="true"></span></a>
					<?php
					}else{

					}
					 ?>
				</td>
				<td><?php echo $membresias['idopp'].' - '.$membresias['abreviacion']; ?></td>
				<td>$ <?php echo $membresias['idcomprobante_pago'].' - '.$membresias['monto']; ?></td>
				<td class="<?php echo $estatus; ?>"><?php echo $membresias['estatus_membresia']; ?></td>
				<td>
					<?php 
					if(isset($membresias['fecha_registro'])){
						echo date('d/m/Y',$membresias['fecha_registro']);
					} 
					?>
				</td>
			</tr>
		<?php
		$contador++;
		}
		 ?>
	</tbody>
</table>