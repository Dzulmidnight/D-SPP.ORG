<?php 
require_once('Connections/dspp.php');
mysql_select_db($database_dspp, $dspp);

$salida = "";
$query = "SELECT * FROM contactos ORDER BY nombre ASC";

if(isset($_POST['consulta'])){
	$q = $_POST['consulta'];
	$query = "SELECT * FROM contactos WHERE nombre LIKE '%".$q."%'";
}
$resultado = mysql_query($query,$dspp) or die(mysql_error());
$total = mysql_num_rows($resultado);

if($total > 0){
	$salida .= "
		<table>
			<thead>
				<tr>ID CONTACTO</tr>
				<tr>NOMBRE</tr>
				<tr>CARGO</tr>
				<tr>TELEFONO</tr>
				<tr>PAIS</tr>
			</thead>
			<tbody>
	";
	while ($fila = mysql_fetch_assoc($resultado)) {
		$salida .= 
		"<tr>		
			<td>".$fila['idcontacto']."</td>
			<td>".$fila['nombre']."</td>
			<td>".$fila['cargo']."</td>
			<td>".$fila['telefono1']."</td>
			<td>".$fila['email1']."</td>
		</tr>";	
	}
	$salida .= "
			</tbody>
		</table>
	";
}else{
	$salida .= "NO HAY DATOS";
}
echo $salida;
 ?>