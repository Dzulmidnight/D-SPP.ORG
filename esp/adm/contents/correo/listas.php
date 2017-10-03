<?php 
function mayuscula($variable) {
	$variable = strtr(strtoupper($variable),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
	return $variable;
}
 ?>
<div class="col-md-12">
	<table class="table table-bordered" style="font-size: 10px;">
		<thead>
			<tr>
				<th colspan="10">LISTA DE OPPS</th>
			</tr>
			<tr>
				<th>#</th>
				<th>ID CONTACTO</th>
				<th>IDOC</th>
				<th>ID LISTA</th>
				<th>Organización</th>
				<th>País</th>
				<th>Nombre</th>
				<th>Cargo</th>
				<th>Telefono(s)</th>
				<th>Correo(s)</th>
			</tr>
		</thead>
		<tbody>
			<?php 
			$contador = 1;
			$query = "SELECT contactos.*, opp.abreviacion AS 'abreviacion_opp', opp.pais FROM contactos INNER JOIN opp ON contactos.idopp = opp.idopp GROUP BY nombre ORDER BY nombre ASC";
			$consultar = mysql_query($query, $dspp) or die(mysql_error()); 

			while($registros = mysql_fetch_assoc($consultar)){
			?>
			<tr>
				<td><?php echo $contador; ?></td>
				<td><?php echo $registros['idcontacto']; ?></td>
				<td><?php echo 'id oc: '.$registros['idoc']; ?></td>
				<td><?php echo 'lista: '.$registros['lista_contactos']; ?></td>
				<!-- ABREVIACIÓN ORGANIZACIÓN -->
				<td><?php echo $registros['idopp'].' - '.mayuscula($registros['abreviacion_opp']); ?></td>
				<!-- PAIS -->
				<td><?php echo mayuscula($registros['pais']); ?></td>
				<!-- NOMBRE -->
				<td><?php echo mayuscula($registros['nombre']); ?></td>
				<!-- CARGO -->
				<td><?php echo mayuscula($registros['cargo']); ?></td>
				<!-- TELEFONO -->
				<td>
					<b>Tel 1:</b> <?php echo '<span style="color:red">'.$registros['telefono1'].'</span>'; ?>
					<br>
					<b>Tel 2:</b> <?php echo '<span style="color:#e67e22">'.$registros['telefono2'].'</span>'; ?>
				</td>
				<!-- CORREO -->
				<td>
					<b>Correo 1:</b> <?php echo '<span style="color:red">'.$registros['email1'].'</span>'; ?>
					<br>
					<b>Correo 2:</b> <?php echo '<span style="color:#e67e22">'.$registros['email2'].'</span>'; ?>
				</td>
			</tr>
			<?php
			$contador++;
			}
			 ?>
			
		</tbody>
	</table>
</div>
