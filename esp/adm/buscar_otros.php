<?php 
require_once('../Connections/dspp.php');
mysql_select_db($database_dspp, $dspp);

function mayuscula($variable) {
	$variable = strtr(strtoupper($variable),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
	return $variable;
}

$salida = "";
$query = "SELECT contactos.*, lista_contactos.nombre AS 'nombre_lista' FROM contactos INNER JOIN lista_contactos ON contactos.lista_contactos = lista_contactos.idlista_contactos GROUP BY contactos.nombre ORDER BY contactos.nombre ASC";

if(isset($_POST['consulta'])){
	$q = $_POST['consulta'];
	$query = "SELECT contactos.*, lista_contactos.nombre AS 'nombre_lista' FROM contactos INNER JOIN lista_contactos ON contactos.lista_contactos = lista_contactos.idlista_contactos WHERE contactos.nombre LIKE '%".$q."%' OR contactos.pais LIKE '%".$q."%' OR lista_contactos.nombre LIKE '%".$q."%' GROUP BY contactos.nombre ORDER BY contactos.nombre ASC";
}
$resultado = mysql_query($query,$dspp) or die(mysql_error());
$total = mysql_num_rows($resultado);
$contador = 1;

if($total > 0){
	$salida .= "
		<table class='table table-bordered table-condensed' style='font-size:12px;'>
			<thead>
				<tr>
					<th>#</th>
					<th>Sección</th>
					<th>País</th>
					<th>Nombre</th>
					<th>Cargo</th>
					<th>Telefono(s)</th>
					<th>Correo(s)</th>
				</tr>
			</thead>
			<tbody>
	";
	while ($fila = mysql_fetch_assoc($resultado)) {
		$salida .= 
		"<tr>
			<td>".$contador."</td>
			<!-- NOMBRE LISTA DE CONTACTOS -->
			<td>".$fila['nombre_lista']."</td>
			<!-- PAIS -->
			<td>".mayuscula($fila['pais'])."</td>
			<!-- NOMBRE DE CONTACTO -->
			<td>".mayuscula($fila['nombre'])."</td>
			<!-- CARGO -->
			<td>".mayuscula($fila['cargo'])."</td>
			<!-- TELEFONO -->
			<td>
				<b>Tel 1:</b> ".'<span style="color:red">'.$fila['telefono1'].'</span>'."
				<br>
				<b>Tel 2:</b> ".'<span style="color:#e67e22">'.$fila['telefono2'].'</span>'."
			</td>
			<!-- CORREO -->
			<td>
				<b>Correo 1:</b> <span style='color:red'>".$fila['email1']."</span>
				<br>
				<b>Correo 2:</b> <span style='color:#e67e22'>".$fila['email2']."</span>
			</td>
		</tr>";	
		$contador++;
	}
	$salida .= "
			</tbody>
		</table>
	";
}else{
	$salida .= "<p class='alert alert-warning'>No se encontraron coincidencias</p>";
}
echo $salida;
 ?>

