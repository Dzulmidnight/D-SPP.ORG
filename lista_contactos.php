<?php 
require_once('Connections/dspp.php'); 
mysql_select_db($database_dspp, $dspp);
$charset='utf-8';
$buscar = "";
$consultaBusqueda = $_POST['buscar'];


function mayuscula($variable) {
	$variable = strtr(strtoupper($variable),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
	return $variable;
}
echo 'HOLA';
 ?>


