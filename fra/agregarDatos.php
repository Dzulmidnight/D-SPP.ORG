<?php 
require_once('../Connections/dspp.php'); 
require_once('../Connections/mail.php');

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

if(isset($_POST['agregar_plataforma']) && $_POST['agregar_plataforma'] == 1){
	$insertSQL = sprintf("INSERT INTO plataformas_spp(pais) VALUES(%s)",
		GetSQLValueString($_POST['pais'], "text"));
	$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
}

if(isset($_POST['agregar_ajuste']) && $_POST['agregar_ajuste'] == 1){
	$insertSQL = sprintf("INSERT INTO porcentaje_ajuste(cuota_compradores, cuota_productores, membresia_compradores, distribucion_plataforma_origen, distribucion_plataforma_destino, anio) VALUES (%s, %s, %s, %s, %s, %s)",
		GetSQLValueString($_POST['cuota_compradores'], "double"),
		GetSQLValueString($_POST['cuota_productores'], "double"),
		GetSQLValueString($_POST['membresia_compradores'], "double"),
		GetSQLValueString($_POST['distribucion_plataforma_origen'], "double"),
		GetSQLValueString($_POST['distribucion_plataforma_destino'], "double"),
		GetSQLValueString($_POST['anio'], "int"));
	$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
}


if(isset($_POST['actualizar_ajuste']) && $_POST['actualizar_ajuste'] == 1){
	$updateSQL = sprintf("UPDATE porcentaje_ajuste SET cuota_compradores = %s, cuota_productores = %s, membresia_compradores = %s, distribucion_plataforma_origen = %s, distribucion_plataforma_destino = %s, anio = %s WHERE idporcentaje_ajuste = %s",
		GetSQLValueString($_POST['cuota_compradores'], "double"),
		GetSQLValueString($_POST['cuota_productores'], "double"),
		GetSQLValueString($_POST['membresia_compradores'], "double"),
		GetSQLValueString($_POST['distribucion_plataforma_origen'], "double"),
		GetSQLValueString($_POST['distribucion_plataforma_destino'], "double"),
		GetSQLValueString($_POST['anio'], "int"),
		GetSQLValueString($_POST['idporcentaje_ajuste'], "int"));
	$actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());
}

?>