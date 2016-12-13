<?php 
require_once('Connections/dspp.php'); 

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
	$charset='utf-8';

	if(isset($_POST['guardar_traduccion']) && $_POST['guardar_traduccion'] == 1){

		if(!empty($_POST['producto_ingles'])){
			$updateSQL = sprintf("UPDATE productos SET producto_ingles = %s WHERE producto = %s",
				GetSQLValueString(strtoupper($_POST['producto_ingles']), "text"),
				GetSQLValueString($_POST['producto'], "text"));
			$actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());
		}
	}

?>