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

$idopp = $_SESSION['idopp'];
$ano_actual = date('Y', time());
echo $ano_actual;

//consultamos las solicitudes que tiene en el año
$row_solicitudes = mysql_query("SELECT idsolicitud_certificacion FROM solicitud_certificacion WHERE idopp = $idopp AND FROM_UNIXTIME(fecha_registro,'%Y') = $ano_actual", $dspp) or die(mysql_error());
$total = mysql_num_rows($row_solicitudes);

echo "EL TOTAL ES: ".$total;
 ?>

<ul class="nav nav-pills">
	<li role="presentation" <?php if(isset($_GET['select'])){ echo "class='active'"; } ?>>
		<a href="?SOLICITUD&select">
			<span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Listado Solicitudes
		</a>
	</li>
	<li role="presentation" <?php if(isset($_GET['add'])){ echo "class='active'"; } ?>>
		<a href="?SOLICITUD&add">
			<span class="glyphicon glyphicon-open-file" aria-hidden="true"></span> Nueva Solicitud
		</a>
	</li>
	<?php 
	if(isset($_GET['detail'])){
	?>
		<li role="presentation" class="active">
			<a href="#">Detalle</a>
		</li>
	<?php
	}
	?>
</ul>

<?php
 if(isset($_GET['mensaje'])){
?>
	<p>
		<div class="alert alert-success alert-dismissible" role="alert">
		  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		  <strong><? echo $_GET['mensaje'];?></strong>
		</div>
	</p>
<? }?>


<?
if(isset($_GET['select'])){
	include ("solicitud_select.php");
}else if(isset($_GET['add'])){
	include ("solicitud_add.php");
}else if(isset($_GET['detail'])){
	include ("solicitud_detail.php");
}else if(isset($_GET['detailBlock'])){
	include ("solicitud_detailBlock.php");
}
?>