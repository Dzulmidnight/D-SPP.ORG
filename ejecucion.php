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

//Variable de búsqueda
//$consultaBusqueda = '';

$mensaje = "";
$consultaBusqueda = $_POST['valorBusqueda'];

if (isset($consultaBusqueda)) {

//Variable de búsqueda

	//Selecciona todo de la tabla mmv001 
	//donde el nombre sea igual a $consultaBusqueda, 
	//o el apellido sea igual a $consultaBusqueda, 
	//o $consultaBusqueda sea igual a nombre + (espacio) + apellido
	$consulta = mysql_query("SELECT nombre, pais FROM opp WHERE spp LIKE '%$consultaBusqueda%'", $dspp) or die(mysql_error());

	//Obtiene la cantidad de filas que hay en la consulta
	$filas = mysql_num_rows($consulta);

//Si no existe ninguna fila que sea igual a $consultaBusqueda, entonces mostramos el siguiente mensaje
	if ($filas === 0) {
		$mensaje = "No se encontro resultado";
	} else {
		//Si existe alguna fila que sea igual a $consultaBusqueda, entonces mostramos el siguiente mensaje
		//echo 'Resultados para <strong>'.$consultaBusqueda.'</strong>';

		//La variable $resultado contiene el array que se genera en la consulta, así que obtenemos los datos y los mostramos en un bucle
		$resultados = mysql_fetch_assoc($consulta);
		$mensaje = $resultados['nombre'];
		$pais = $resultados['pais'];
		/*while($resultados = mysql_fetch_assoc($consulta)) {
			$spp = $resultados['spp'];
			$nombre = $resultados['nombre'];
			$abreviacion = $resultados['abreviacion'];

			$mensaje = $abreviacion;
			$pais = $resultados['pais'];
			//Output
			/*$mensaje .= '
			<p>
			<strong>Nombre:</strong> ' . $spp . '<br>
			<strong>Apellido:</strong> ' . $nombre . '<br>
			<strong>Edad:</strong> ' . $abreviacion . '<br>
			</p>';*/

		//}//Fin while $resultados

	} //Fin else $filas

}


//Devolvemos el mensaje que tomará jQuery
echo $pais;
echo $mensaje;
 ?>