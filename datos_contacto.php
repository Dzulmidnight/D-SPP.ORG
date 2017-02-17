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

$row_paises = mysql_query("SELECT * FROM paises", $dspp) or die(mysql_error());
?>

<html>
<head>
<meta charset="utf-8">
<title>Documento sin título</title>
</head>

<body>
	<form action="" method="POST">
	<!--<input type="text" name="busqueda" id="busqueda" value="" placeholder="" maxlength="30" autocomplete="off" />-->
		<input type="text" name="busqueda" id="busqueda" value="" placeholder="" maxlength="30" autocomplete="off" onKeyUp="buscar();" />
	</form>
	<div id="resultadoBusqueda">
		<input id="spp" name="spp" value="" placeholder="nombre">
		<input id="pais" name="pais" value="" placeholder="pais">
	</div>

</body>
</html>



<script>
$(document).ready(function() {
//    $("#resultadoBusqueda").val('<p>JQUERY VACIO</p>');
    $("#spp").val('JQUERY VACIO');
//    $("#resultadoBusqueda").val('<p>JQUERY VACIO</p>');
    $("#pais").val('JQUERY VACIO');


});

function buscar() {
    var textoBusqueda = $("input#busqueda").val();
 
     if (textoBusqueda != "") {
        $.post("ejecucion.php", {valorBusqueda: textoBusqueda}, function(mensaje) {
            $("#spp").val(mensaje);
            $("#pais").val(mensaje);
         }); 
     } else { 
        $("#spp").val('JQUERY VACIO');
        $("#pais").val('JQUERY VACIO');
     };




};
</script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>