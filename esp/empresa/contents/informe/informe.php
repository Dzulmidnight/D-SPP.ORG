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
$ano_actual = date('Y', time());

$row_informe_general = mysql_query("SELECT idinforme_general, FROM_UNIXTIME(ano, '%Y') AS 'ano_informe' FROM informe_general WHERE idempresa = ".$idempresa." AND FROM_UNIXTIME(ano, '%Y') = '".$ano_actual."'", $dspp) or die(mysql_error());
$total_informe = mysql_num_rows($row_informe_general);
$informe_general = mysql_fetch_assoc($row_informe_general);

 ?>

<h4>
	<a <?php if(isset($_GET['select'])){ echo "class='btn btn-sm btn-primary'"; }else{ echo "class='btn btn-sm btn-default'"; } ?> href="?INFORME&select">Listado Informes Generales</a> | 
	<?php 
	// si se ha creado un informe general con el "IDEMRESA" y el año del informe corresponde al año se muestra el boton "Informe General"
	if($total_informe == 1){
	?>
		<a <?php if(isset($_GET['general_detail'])){ echo "class='btn btn-sm btn-primary'"; }else{ echo "class='btn btn-sm btn-default'"; } ?> href="?INFORME&general_detail"><span class="glyphicon glyphicon-book" aria-hidden="true"></span> Informe General:</a>
	<?php
	}else{ // si no se ha creado un informe general del año en curso, se muestra el boton "Crear Nuevo Informe General"
	?>
		<a <?php if(isset($_GET['add_general'])){ echo "class='btn btn-sm btn-primary'"; }else{ echo "class='btn btn-sm btn-default'"; } ?> href="?INFORME&add_general"><span class="glyphicon glyphicon-book" aria-hidden="true"></span> Crear Nuevo Informe General</a>
	<?php
	}
	?>
</h4>



<?php 
if(isset($_GET['general_detail'])){
	include("informe_general.php");
}else if(isset($_GET['add_general'])){
	include("informe_general.php");
}else if(isset($_GET['detail'])){
	include ("informe_detail.php");
}else if(isset($_GET['add'])){
	include ("informe_add.php");
}else{
	include ('listado_informes.php');
}
?>