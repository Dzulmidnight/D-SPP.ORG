<?php 
require_once('../Connections/dspp.php'); 
require_once('../Connections/mail.php');

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

if(isset($_POST['agregar_plataforma']) && $_POST['agregar_plataforma'] == 1){
	$insertSQL = sprintf("INSERT INTO plataformas_spp(pais) VALUES(%s)",
		GetSQLValueString($_POST['pais'], "text"));
	$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
}

if(!empty($_POST['eliminar_plataforma'])){
	$deleteSQL = sprintf("DELETE FROM plataformas_spp WHERE idplataforma_spp = %s", 
		GetSQLValueString($_POST['eliminar_plataforma'], "int"));
	$eliminar = mysql_query($deleteSQL, $dspp) or die(mysql_error());
}

if(isset($_POST['agregar_ajuste']) && $_POST['agregar_ajuste'] == 1){
	$insertSQL = sprintf("INSERT INTO porcentaje_ajuste(cuota_compradores, cuota_productores, membresia_compradores, distribucion_plataforma_origen, distribucion_plataforma_destino, anio) VALUES (%s, %s, %s, %s, %s, %s)",
		GetSQLValueString($_POST['cuota_compradores'], "double"),
		GetSQLValueString($_POST['cuota_productores'], "double"),
		GetSQLValueString($_POST['membresia_compradores'], "double"),
		GetSQLValueString($_POST['distribucion_plataforma_origen'], "double"),
		GetSQLValueString($_POST['distribucion_plataforma_destino'], "double"),
		GetSQLValueString($_POST['anio'], "int"));
	$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
}

if(isset($_POST['actualizar_ajuste']) && $_POST['actualizar_ajuste'] == 1){
	$updateSQL = sprintf("UPDATE porcentaje_ajuste SET cuota_compradores = %s, cuota_productores = %s, membresia_compradores = %s, distribucion_plataforma_origen = %s, distribucion_plataforma_destino = %s, anio = %s WHERE idporcentaje_ajuste = %s",
		GetSQLValueString($_POST['cuota_compradores'], "double"),
		GetSQLValueString($_POST['cuota_productores'], "double"),
		GetSQLValueString($_POST['membresia_compradores'], "double"),
		GetSQLValueString($_POST['distribucion_plataforma_origen'], "double"),
		GetSQLValueString($_POST['distribucion_plataforma_destino'], "double"),
		GetSQLValueString($_POST['anio'], "int"),
		GetSQLValueString($_POST['idporcentaje_ajuste'], "int"));
	$actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());
}


?>
<h4>Configuracion reportes comerciales</h4>
<div class="row">
	<!-- SECCIÓN PLATAFORMAS SPP -->
	<div id="div_central" class="col-md-4">
		<table class="table table-bordered table-condensed" style="font-size:12px;">
			<thead>
				<tr>
					<th class="info text-center" colspan="2">Plataformas SPP actuales</th>
				</tr>
			</thead>
			<tbody>
				<?php 
				$row_plataformas = mysql_query("SELECT * FROM plataformas_spp", $dspp) or die(mysql_error());
				$total_plataformas = mysql_num_rows($row_plataformas);
				
				if($total_plataformas == 0){
					echo "<tr><td colspan='3'>No se encontraron registros</td></tr>";
				}else{
					$contador = 1;
					while($plataformas = mysql_fetch_assoc($row_plataformas)){
					?>
						<form action="" method="POST">
							<tr>
								<td><button type="submit" name="eliminar_plataforma" value="<?php echo $plataformas['idplataforma_spp']; ?>" class="btn btn-sm btn-danger"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button></td>
								<td><?php echo $plataformas['pais']; ?></td>
							</tr>
						</form>
					<?php
					$contador++;
					}
				}
				?>
				<form action="" method="POST">
					<tr>
						<td><button type="submit" class="btn btn-sm btn-success" name="agregar_plataforma" value="1"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> nueva plataforma</button></td>
						<td>
							<select name="pais" id="" required>
								<option value="">- - -</option>
							<?php 
							$row_pais = mysql_query("SELECT * FROM paises", $dspp) or die(mysql_error());
							while($pais = mysql_fetch_assoc($row_pais)){
								echo "<option value='".utf8_encode($pais['nombre'])."'>".utf8_encode($pais['nombre'])."</option>";
							}
							 ?>
							</select>
						</td>
					</tr>
				</form>
			</tbody>
		</table>
	</div>

	<!-- SECCIÓN PORCENTAJE AJUSTE FINANCIERO -->
	<div id="div_central" class="col-md-8">

		<table class="table table-bordered table-striped table-condensed" style="font-size:12px;">
			<thead>
				<tr>
					<th class="info text-center" colspan="7">Porcentajes ajuste financiero</th>
				</tr>
				<tr style="font-size:10px;text-align: center;">
					<th></th>
					<th>Año de aplicación</th>
					<th>Cuota Compradores sobre ventas SPP</th>
					<th>Cuota Productores sobre ventas SPP</th>
					<th>Membresía Compradores(promedio)</th>
					<th>Distribución Cuotas de Uso a Plataformas SPP Locales(Destino)</th>
					<th>Distribución Cuotas de Uso a Plataformas SPP Locales(Origen)</th>
				</tr>
			</thead>
			<tbody>
				<?php 
				$row_ajuste = mysql_query("SELECT * FROM porcentaje_ajuste", $dspp) or die(mysql_error());
				$total_rows = mysql_num_rows($row_ajuste);

				if($total_rows > 0){
					while($ajuste = mysql_fetch_assoc($row_ajuste)){
					?>
						<form action="" method="POST">
							<tr>
								<td>
									<button class="btn btn-sm btn-success" type="submit" name="actualizar_ajuste" value="1"><span class="glyphicon glyphicon-refresh" aria-hidden="true"></span></button>
								</td>
								<td>
									<input type="text" class="form-control" name="anio" value="<?php echo $ajuste['anio']; ?>">
								</td>
								<td>
									<div class="form-group">
									    <div class="input-group">
									      <input type="text" class="form-control" name="cuota_compradores" value="<?php echo $ajuste['cuota_compradores']; ?>">
									      <div class="input-group-addon">%</div>
									    </div>
									 </div>
								</td>
								<td>
									<div class="form-group">
									    <div class="input-group">
									      <input type="text" class="form-control" name="cuota_productores" value="<?php echo $ajuste['cuota_productores']; ?>">
									      <div class="input-group-addon">%</div>
									    </div>
									 </div>	
								</td>
								<td>
									<div class="form-group">
									    <div class="input-group">
									      <input type="text" class="form-control" name="membresia_compradores" value="<?php echo $ajuste['membresia_compradores']; ?>">
									      <div class="input-group-addon">%</div>
									    </div>
									 </div>
								</td>
								<td>
									<div class="form-group">
									    <div class="input-group">
									      <input type="text" class="form-control" name="distribucion_plataforma_destino" value="<?php echo $ajuste['distribucion_plataforma_destino']; ?>">
									      <div class="input-group-addon">%</div>
									    </div>
									 </div>
								</td>
								<td>
									<div class="form-group">
									    <div class="input-group">
									      <input type="text" class="form-control" name="distribucion_plataforma_origen" value="<?php echo $ajuste['distribucion_plataforma_origen']; ?>">
									      <div class="input-group-addon">%</div>
									    </div>
									 </div>
									<input type="hidden" name="idporcentaje_ajuste" value="<?php echo $ajuste['idporcentaje_ajuste']; ?>">
								</td>
							</tr>
						</form>
					<?php
					}
				}else{
					echo "<tr><td colspan='6'>No se encontraron registros</td></tr>";
				}
				 ?>
					<form action="" method="POST">
						<tr class="success">
							<td></td>
							<td><input type="number" name="anio" required></td>
							<td><input type="number" step="any" name="cuota_compradores" required></td>
							<td><input type="number" step="any" name="cuota_productores" required></td>
							<td><input type="number" step="any" name="membresia_compradores" required></td>
							<td><input type="number" step="any" name="distribucion_plataforma_destino" required></td>
							<td><input type="number" step="any" name="distribucion_plataforma_origen" required></td>

						</tr>
						<tr>
							<td colspan="7"><button type="submit" class="btn btn-sm btn-warning" name="agregar_ajuste" value="1"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Agregar registro</button></td>
						</tr>
					</form>

			</tbody>
		</table>
	</div>
</div>


<script>
	function formulario_plataforma(){
		$("#div_central").removeClass("col-md-12");
		$("#div_central").addClass("col-md-6");

		document.getElementById('div_formulario').style.display = 'block';
		//alert('SE LANZA EL FORMULARIO');
	}
		/*contador++;
	var table = document.getElementById("tablaCertificaciones");
	  {
	  var row = table.insertRow(2);
	  var cell1 = row.insertCell(0);
	  var cell2 = row.insertCell(1);
	  var cell3 = row.insertCell(2);
	  var cell4 = row.insertCell(3);

	  cell1.innerHTML = '<input type="text" class="form-control" name="certificacion['+contador+']" id="exampleInputEmail1" placeholder="CERTIFICACIÓN">';
	  cell2.innerHTML = '<input type="text" class="form-control" name="certificadora['+contador+']" id="exampleInputEmail1" placeholder="CERTIFICADORA">';
	  cell3.innerHTML = '<input type="text" class="form-control" name="ano_inicial['+contador+']" id="exampleInputEmail1" placeholder="AÑO INICIAL">';
	  cell4.innerHTML = '<div class="col-md-6">SI<input type="radio" class="form-control" name="interrumpida['+contador+']" value="SI"></div><div class="col-md-6">NO<input type="radio" class="form-control" name="interrumpida['+contador+']" value="NO"></div>';
	  }
	}	*/

</script>

