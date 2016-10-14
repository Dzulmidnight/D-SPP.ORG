<?php 
require_once('Connections/dspp.php');
mysql_select_db($database_dspp, $dspp);


if(isset($_POST['agregar']) && $_POST['agregar'] == "1"){
	$tipo = $_POST['tipo'];
	$descripcion = $_POST['descripcion'];
	$query = "INSERT INTO tipo_notificacion (tipo,descripcion) VALUES('$tipo', '$descripcion')";
	$ejecutar = mysql_query($query,$dspp) or die(mysql_error());
}
 ?>

<form action="" method="POST">
	<label for="tipo">TIPO NOTIFICACIÓN</label><input type="text" name="tipo">
	<br>
	<br>
	<label for="descripcion">DESCRIPCIÓN</label><textarea name="descripcion" id="" cols="30" rows="10"></textarea>
	<br>
	<input type="hidden" name="agregar" value="1">
	<br>
	<input type="submit">
</form>