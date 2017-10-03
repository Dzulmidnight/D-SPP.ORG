<?php 
require_once('../Connections/dspp.php'); 
require_once('../Connections/mail.php'); 
require_once('../../mpdf/mpdf.php');

//error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

if (!isset($_SESSION)) {
  session_start();
  
  $redireccion = "../index.php?OC";

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
mysql_select_db($database_dspp, $dspp);

$estado = 1;
$administrador = 'cert@spp.coop';

$row_periodo = mysql_query("SELECT * FROM periodo_objecion");

?>
<h4>Menú principal Administrador</h4>

<?php
  ////////////////////// INICIA SECCIÓN MENSAJES PERIODO DE OBJECIÓN //////////////////////////////
  echo '<p class="alert alert-danger">PERIODO OBJECIÓN</p>';
  include('msj_periodo_objecion.php');

  echo '<p class="alert alert-danger">RECORDATORIO PAGO</p>';
  /**************************************/
  include('msj_recordatorio_pago.php');

  echo '<p class="alert alert-danger">RENOVACIÓN CERTIFICADO</p>';
  include('msj_renovacion_certificado.php');
  
  echo '<p class="alert alert-danger">RENOVACIÓN REGISTRO</p>';
  include('msj_renovacion_registro.php');
?>
