<?php 
	require_once('Connections/dspp.php'); 
	mysql_select_db($database_dspp, $dspp);
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
//    $("#resultadoBusqueda").val('<p>CAMPO VACIO</p>');
    $("#spp").val('CAMPO VACIO');
//    $("#resultadoBusqueda").val('<p>CAMPO VACIO</p>');
    $("#pais").val('CAMPO VACIO');


});

function buscar() {
    var textoBusqueda = $("input#busqueda").val();
 
     if (textoBusqueda != "") {
        $.post("ejecucion.php", {valorBusqueda: textoBusqueda}, function(nombre_opp) {
            $("#spp").val(nombre_opp);
         }); 
     } else { 
        $("#spp").val('CAMPO VACIO');
     };

     if (textoBusqueda != "") {
        $.post("pais_ajax.php", {valorBusqueda: textoBusqueda}, function(nombre_pais) {
            $("#pais").val(nombre_pais);
         }); 
     } else { 
        $("#pais").val('CAMPO VACIO');
     };

};
</script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>