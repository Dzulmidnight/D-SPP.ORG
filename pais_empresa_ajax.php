<?php 
require_once('Connections/dspp.php');
mysql_select_db($database_dspp, $dspp);
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}
$charset='utf-8';

$nombre_pais = "";
$consultaBusqueda = $_POST['valorBusqueda'];

if (isset($consultaBusqueda)) {
	$consulta = mysql_query("SELECT pais FROM empresa WHERE spp LIKE '%$consultaBusqueda%'", $dspp) or die(mysql_error());
	//Obtiene la cantidad de filas que hay en la consulta
	$filas = mysql_num_rows($consulta);
//Si no existe ninguna fila que sea igual a $consultaBusqueda, entonces mostramos el siguiente pais
	if ($filas === 0) {
		$pais = "No se encontro resultado";
	} else {
		//Si existe alguna fila que sea igual a $consultaBusqueda, entonces mostramos el siguiente pais
		//echo 'Resultados para <strong>'.$consultaBusqueda.'</strong>';
		//La variable $resultado contiene el array que se genera en la consulta, así que obtenemos los datos y los mostramos en un bucle
		$resultados = mysql_fetch_assoc($consulta);
		$nombre_pais = $resultados['pais'];
	}
}
echo $nombre_pais;
?>