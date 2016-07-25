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

	$fechaupload = $_POST['fechaupload'];
	$idopp = $_POST['membresiaidopp'];
	$idsolicitud = $_POST['idsolicitud'];
	$statuspago = "REVISION";



  $ruta = "archivos/membresia/";

  if(!empty($_FILES['comprobante']['name'])){
    $_FILES['comprobante']['name'];
        move_uploaded_file($_FILES["comprobante"]["tmp_name"], $ruta.time()."_".$_FILES["comprobante"]["name"]);
        $comprobantePago = $ruta.basename(time()."_".$_FILES["comprobante"]["name"]);
  }else{
    $comprobantePago = NULL;
  }
	$adjunto = $comprobantePago;

  $query = "INSERT INTO membresia (adjunto,fechaupload,idopp) VALUES ('$adjunto',$fechaupload,$idopp)";
  $insertar = mysql_query($query,$dspp) or die(mysql_error());
  
  //echo "la consulta es: ".$query;

  $idexterno = mysql_insert_id($dspp);
  $identificador = "membresia";


  $queryFecha = "INSERT INTO fecha (fecha, idexterno, identificador, status) VALUES ($fechaupload, $idexterno, '$identificador', '$statuspago')";
  $insertarFecha = mysql_query($queryFecha,$dspp) or die(mysql_error());
  //echo "<br>".$queryFecha;

  $update = "UPDATE certificado SET statuspago = '$statuspago' WHERE idsolicitud = $idsolicitud";
  $insertarupdate = mysql_query($update,$dspp) or die(mysql_error());
  //echo "<br>".$update;
