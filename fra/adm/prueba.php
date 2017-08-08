<?php 
require_once('../Connections/dspp.php'); 
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

if(isset($_POST['comprobanteMembresia']) && $_POST['comprobanteMembresia'] == "2"){
    $idexterno = $_POST['idmembresia'];
    $fecha = $_POST['fecha'];
    $identificador = "membresia";
    $idcertificado = $_POST['idcertificado'];

  if(isset($_POST['aprobar'])){
    $status = "APROBADO";
    $insertar = "INSERT INTO fecha (fecha,idexterno,identificador,status) VALUES ($fecha,$idexterno,'$identificador','$status')";
    $ejecutar = mysql_query($insertar,$dspp) or die(mysql_error());
    $actualizar = "UPDATE certificado SET statuspago = '$status' WHERE idcertificado = $idcertificado";
    $ejecutar = mysql_query($actualizar,$dspp) or die(mysql_error());
    echo $insertar;
    echo "<br>".$actualizar; 

  }
  if(isset($_POST['denegar'])){
    $status = "DENEGAR";
    $insertar = "INSERT INTO fecha (fecha,idexterno,identificador,status) VALUES ($fecha,$idexterno,'$identificador','$status')";
    $ejecutar = mysql_query($insertar,$dspp) or die(mysql_error());
    $actualizar = "UPDATE certificado SET statuspago = '$status' WHERE idcertificado = $idcertificado";
    $ejecutar = mysql_query($actualizar,$dspp) or die(mysql_error());

    echo $insertar; 
    echo "<br>".$actualizar; 
  }
}
echo "<br>".$_POST['comprobanteMembresia'];