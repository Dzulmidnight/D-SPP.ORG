<?php 
require_once('../Connections/dspp.php'); 
require_once('../Connections/mail.php');
require_once('../../mpdf/mpdf.php');

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
//// CORREOS GENERALES
//$correo_cert = 'cert@spp.coop';
//$correo_adm = 'adm@spp.coop';
$correo_cert = 'yasser.midnight@gmail.com';
$correo_adm = 'yasser.midnight@gmail.com';
?>
<hr style="margin-bottom:0px;">
<div class="btn-group" role="group" aria-label="...">
	<a href="?REPORTES&ingresos=informe_compras" <?php if($_GET['ingresos'] == 'informe_compras'){ echo 'class="btn btn-sm btn-primary"'; }else{ echo 'class="btn btn-sm btn-default"'; } ?>>Empresas</a>
	<a href="?REPORTES&ingresos=informe_ventas" <?php if($_GET['ingresos'] == 'informe_ventas'){ echo 'class="btn btn-sm btn-primary"'; }else{ echo 'class="btn btn-sm btn-default"'; } ?>>OPP</a>
</div>

<?php 
if($_GET['ingresos'] == 'informe_compras'){
	include('compras_empresa.php');
}else if($_GET['ingresos'] == 'informe_ventas'){
	include('ventas_opp.php');
}
?>