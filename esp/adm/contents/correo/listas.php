<?php 
function mayuscula($variable) {
	$variable = strtr(strtoupper($variable),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
	return $variable;
}
 ?>

<div class="col-md-6">
	<div class="form-group">
    	<label for="caja_busqueda">Buscar:</label>
    	<input type="text" class="form-control" id="caja_busqueda" name="caja_busqueda" placeholder="Buscar por nombre, país">
	</div>
</div>

<div class="col-md-12" id="datos">
	
</div>


<!--03_10_2017<div class="col-md-12">
	<table class="table table-bordered" style="font-size:12px;">
		<thead>
			<tr>
				<th colspan="">LISTA DE CONTACTOS OPP</th>
				<th>
					<?php 
					$query = "SELECT opp.pais FROM opp GROUP BY pais";
					$consultar = mysql_query($query, $dspp) or die(mysql_error());
					 ?>
					<select name="pais" id="">
						<option value="">Lista de Paises</option>
						<?php 
						while($paises = mysql_fetch_assoc($consultar)){
							echo '<option value="'.$paises['pais'].'">'.$paises['pais'].'</option>';
						}
						 ?>
					</select>
				</th>
				<th>
					
				</th>
				<th>
					
				</th>
				<th>
					
				</th>
				<form action="" method="POST">
					<th colspan="5">
						<input id="buscador" name="buscador" onkeyup="buscar();" type="text" class="form-control" placeholder="Buscar ...">
					</th>					
				</form>

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
		<tbody id="desplegar" name="desplegar">
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
		<!--03_10		<td><?php echo $registros['idopp'].' - <a href="?OPP&detail&idopp='.$registros['idopp'].'">'.mayuscula($registros['abreviacion_opp']).'</a>'; ?></td>
				<!-- PAIS -->
		<!-- 03_10		<td><?php echo mayuscula($registros['pais']); ?></td>
				<!-- NOMBRE -->
		<!-- 03_10		<td><?php echo '<a href="?OPP&detail&idopp='.$registros['idopp'].'&contacto='.$registros['idcontacto'].'">'.mayuscula($registros['nombre']).'</a>'; ?></td>
				<!-- CARGO -->
		<!-- 03_10		<td><?php echo mayuscula($registros['cargo']); ?></td>
				<!-- TELEFONO -->
		<!-- 03_10		<td>
					<b>Tel 1:</b> <?php echo '<span style="color:red">'.$registros['telefono1'].'</span>'; ?>
					<br>
					<b>Tel 2:</b> <?php echo '<span style="color:#e67e22">'.$registros['telefono2'].'</span>'; ?>
				</td>
				<!-- CORREO -->
		<!-- 03_10		<td>
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
</div>03_10-->
