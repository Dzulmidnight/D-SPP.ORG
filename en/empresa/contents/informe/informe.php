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
$row_empresa = mysql_query("SELECT spp, abreviacion, pais, maquilador, comprador, intermediario FROM empresa WHERE idempresa = $idempresa", $dspp) or die(mysql_error());
$empresa = mysql_fetch_assoc($row_empresa);
$tipo_empresa = '';
if($empresa['maquilador']){
	$tipo_empresa = 'MAQUILADOR';
}else if($empresa['comprador']){
	$tipo_empresa = 'COMPRADOR FINAL';
}else if($empresa['intermediario']){
	$tipo_empresa = 'INTERMEDIARIO';
}

function mayus($variable) {
	$variable = strtr(strtoupper($variable),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
	return $variable;
}

$row_configuracion = mysql_query("SELECT * FROM porcentaje_ajuste WHERE anio = $ano_actual", $dspp) or die(mysql_error());
$configuracion = mysql_fetch_assoc($row_configuracion);

$correo_cert = 'yasser.midnight@gmail.com';
$correo_adm = 'yasser.midnight@gmail.com';
$row_informe_general = mysql_query("SELECT informe_general.*, trim1.total_trim1, trim2.total_trim2, trim3.total_trim3, trim4.total_trim4, ROUND(SUM(trim1.total_trim1 + trim2.total_trim2 + trim3.total_trim3 + trim4.total_trim4), 2) AS 'balance_final' FROM informe_general LEFT JOIN trim1 ON informe_general.trim1 = trim1.idtrim1 LEFT JOIN trim2 ON informe_general.trim2 = trim2.idtrim2 LEFT JOIN trim3 ON informe_general.trim3 = trim3.idtrim3 LEFT JOIN trim4 ON informe_general.trim4 = trim4.idtrim4 WHERE informe_general.idempresa = $idempresa AND FROM_UNIXTIME(informe_general.ano, '%Y') = '$ano_actual'", $dspp) or die(mysql_error());

//$row_informe_general = mysql_query("SELECT idinforme_general, FROM_UNIXTIME(ano, '%Y') AS 'ano_informe', total_informe FROM informe_general WHERE idempresa = ".$idempresa." AND FROM_UNIXTIME(ano, '%Y') = '".$ano_actual."'", $dspp) or die(mysql_error());
$total_informe = mysql_num_rows($row_informe_general);
$informe_general = mysql_fetch_assoc($row_informe_general);



//////// INFORME GENERAL DE PRODUCTOS

$row_informe_general_producto = mysql_query("SELECT informe_general_producto.*, trim1_producto.total_trim1, trim2_producto.total_trim2, trim3_producto.total_trim3, trim4_producto.total_trim4, ROUND(SUM(trim1_producto.total_trim1 + trim2_producto.total_trim2 + trim3_producto.total_trim3 + trim4_producto.total_trim4), 2) AS 'balance_final' FROM informe_general_producto LEFT JOIN trim1_producto ON informe_general_producto.trim1_producto = trim1_producto.idtrim1_producto LEFT JOIN trim2_producto ON informe_general_producto.trim2_producto = trim2_producto.idtrim2_producto LEFT JOIN trim3_producto ON informe_general_producto.trim3_producto = trim3_producto.idtrim3_producto LEFT JOIN trim4_producto ON informe_general_producto.trim4_producto = trim4_producto.idtrim4_producto WHERE informe_general_producto.idempresa = $idempresa AND FROM_UNIXTIME(informe_general_producto.ano, '%Y') = '$ano_actual'", $dspp) or die(mysql_error());

//$row_informe_general_producto = mysql_query("SELECT idinforme_general, FROM_UNIXTIME(ano, '%Y') AS 'ano_informe', total_informe FROM informe_general WHERE idempresa = ".$idempresa." AND FROM_UNIXTIME(ano, '%Y') = '".$ano_actual."'", $dspp) or die(mysql_error());
$total_informe_producto = mysql_num_rows($row_informe_general_producto);
$informe_general_producto = mysql_fetch_assoc($row_informe_general_producto);


 ?>

<h4>
	<a <?php if(isset($_GET['select'])){ echo "class='btn btn-sm btn-primary'"; }else{ echo "class='btn btn-sm btn-default'"; } ?> href="?INFORME&select">Summary of general reports</a> | 
	<?php 
	// si se ha creado un informe general con el "IDEMRESA" y el año del informe corresponde al año se muestra el boton "Informe General"
	if($total_informe == 1){
	?>
		<a <?php if(isset($_GET['general_detail'])){ echo "class='btn btn-sm btn-primary'"; }else{ echo "class='btn btn-sm btn-default'"; } ?> href="?INFORME&general_detail"><span class="glyphicon glyphicon-book" aria-hidden="true"></span> General Purchasing Report</a>
	<?php
	}else{ // si no se ha creado un informe general del año en curso, se muestra el boton "Crear Nuevo Informe General"
	?>
		<a <?php if(isset($_GET['add_general'])){ echo "class='btn btn-sm btn-primary'"; }else{ echo "class='btn btn-sm btn-default'"; } ?> href="?INFORME&add_general"><span class="glyphicon glyphicon-book" aria-hidden="true"></span> Create New General Purchasing Report</a>
	<?php
	}
	?>
		<a class="btn btn-sm <?php if(isset($_GET['producto'])){ echo 'btn-success';}else{ echo 'btn-default';} ?>" href="?INFORME&producto"><span class="glyphicon glyphicon-apple" aria-hidden="true"></span> Report Finished Products</a>

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
}else if(isset($_GET['add_producto'])){
	include('informe_productos/informe_add_producto.php');
}else if(isset($_GET['producto'])){
	include('informe_productos/informe_general.php');
}else{
	include ('listado_informes.php');
}


?>