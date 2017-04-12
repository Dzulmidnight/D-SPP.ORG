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
$row_opp = mysql_query("SELECT spp, abreviacion, pais FROM opp WHERE idopp = $idopp", $dspp) or die(mysql_error());
$opp = mysql_fetch_assoc($row_opp);

$row_configuracion = mysql_query("SELECT * FROM porcentaje_ajuste WHERE anio = $ano_actual", $dspp) or die(mysql_error());
$configuracion = mysql_fetch_assoc($row_configuracion);
/*$row_opp = mysql_query("SELECT spp, abreviacion, pais, maquilador, comprador, intermediario FROM opp WHERE idopp = $idopp", $dspp) or die(mysql_error());
$opp = mysql_fetch_assoc($row_opp);
$tipo_opp = '';
if($opp['maquilador']){
	$tipo_opp = 'MAQUILADOR';
}else if($opp['comprador']){
	$tipo_opp = 'COMPRADOR FINAL';
}else if($opp['intermediario']){
	$tipo_opp = 'INTERMEDIARIO';
}
*/
$row_configuracion = mysql_query("SELECT * FROM porcentaje_ajuste WHERE anio = $ano_actual", $dspp) or die(mysql_error());
$configuracion = mysql_fetch_assoc($row_configuracion);


$row_informe_general = mysql_query("SELECT informe_general.*, trim1.total_trim1, trim2.total_trim2, trim3.total_trim3, trim4.total_trim4, ROUND(SUM(trim1.total_trim1 + trim2.total_trim2 + trim3.total_trim3 + trim4.total_trim4), 2) AS 'balance_final' FROM informe_general LEFT JOIN trim1 ON informe_general.trim1 = trim1.idtrim1 LEFT JOIN trim2 ON informe_general.trim2 = trim2.idtrim2 LEFT JOIN trim3 ON informe_general.trim3 = trim3.idtrim3 LEFT JOIN trim4 ON informe_general.trim4 = trim4.idtrim4 WHERE informe_general.idopp = $idopp AND FROM_UNIXTIME(informe_general.ano, '%Y') = '$ano_actual'", $dspp) or die(mysql_error());

//$row_informe_general = mysql_query("SELECT idinforme_general, FROM_UNIXTIME(ano, '%Y') AS 'ano_informe', total_informe FROM informe_general WHERE idopp = ".$idopp." AND FROM_UNIXTIME(ano, '%Y') = '".$ano_actual."'", $dspp) or die(mysql_error());
	$total_informe = mysql_num_rows($row_informe_general);
	$informe_general = mysql_fetch_assoc($row_informe_general);

	function mayus($variable) {
		$variable = strtr(strtoupper($variable),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
		return $variable;
	}
?>

<h4>
	<a <?php if(isset($_GET['select'])){ echo "class='btn btn-sm btn-primary'"; }else{ echo "class='btn btn-sm btn-default'"; } ?> href="?INFORME&select">Summary of general reports</a> | 
	<?php 
	// si se ha creado un informe general con el "IDEMRESA" y el año del informe corresponde al año se muestra el boton "Informe General"
	if($total_informe == 1){
	?>
		<a <?php if(isset($_GET['general_detail'])){ echo "class='btn btn-sm btn-primary'"; }else{ echo "class='btn btn-sm btn-default'"; } ?> href="?INFORME&general_detail"><span class="glyphicon glyphicon-book" aria-hidden="true"></span> General Sales Report</a>
	<?php
	}else{ // si no se ha creado un informe general del año en curso, se muestra el boton "Crear Nuevo Informe General"
	?>
		<a <?php if(isset($_GET['add_general'])){ echo "class='btn btn-sm btn-primary'"; }else{ echo "class='btn btn-sm btn-default'"; } ?> href="?INFORME&add_general"><span class="glyphicon glyphicon-book" aria-hidden="true"></span> Create New General Sales Report</a>
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
}else if(isset($_GET['edit'])){
	include ("informe_edit.php");
}else{
	include ('listado_informes.php');
}


?>