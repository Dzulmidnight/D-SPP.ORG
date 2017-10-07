<?php 
require_once('../Connections/dspp.php');
mysql_select_db($database_dspp, $dspp);

function mayuscula($variable) {
	$variable = strtr(strtoupper($variable),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
	return $variable;
}

$salida = "";
$query = "SELECT contactos.*, opp.abreviacion AS 'abreviacion_opp', opp.pais FROM contactos INNER JOIN opp ON contactos.idopp = opp.idopp GROUP BY contactos.nombre ORDER BY contactos.nombre ASC";

if(isset($_POST['consulta'])){
	$q = $_POST['consulta'];
	$query = "SELECT contactos.*, opp.abreviacion AS 'abreviacion_opp', opp.pais FROM contactos INNER JOIN opp ON contactos.idopp = opp.idopp WHERE contactos.nombre LIKE '%".$q."%' OR opp.abreviacion LIKE '%".$q."%' OR opp.pais LIKE '%".$q."%' GROUP BY contactos.nombre ORDER BY contactos.nombre ASC";
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
					<th>Organización</th>
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
			<!-- ABREVIACIÓN ORGANIZACIÓN -->
			<td><a href='?OPP&detail&idopp=".$fila['idopp']."'>".mayuscula($fila['abreviacion_opp'])."</a></td>
			<!-- PAIS -->
			<td>".mayuscula($fila['pais'])."</td>
			<!-- NOMBRE -->
			<td>".'<a href="?OPP&detail&idopp='.$fila['idopp'].'&contacto='.$fila['idcontacto'].'">'.mayuscula($fila['nombre']).'</a>'."</td>
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

