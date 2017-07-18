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
/**** VARIABLES GLOBALES *******/
$spp_global = "cert@spp.coop";
$finanzas_spp = "adm@spp.coop";
$administrador = "yasser.midnight@gmail.com";
$fecha = time();
$idopp = $_SESSION['idopp'];
/********************************/


$query = "SELECT solicitud_certificacion.idsolicitud_certificacion FROM solicitud_certificacion WHERE solicitud_certificacion.idopp = $idopp";
$row_solicitud_certificacion = mysql_query($query, $dspp) or die(mysql_error());
$total_solicitudes = mysql_num_rows($row_solicitud_certificacion);


if($total_solicitudes == 1){
  include('select_normal.php');
}else{
  include('select_colectiva.php');
}
?>