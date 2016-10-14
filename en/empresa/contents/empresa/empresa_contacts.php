<?php 
require_once('../../Connections/dspp.php'); 
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

if(isset($_POST['agregar_contacto']) && $_POST['agregar_contacto'] == 1){
  $insertSQL = sprintf("INSERT INTO contactos(idempresa, nombre, cargo, telefono1, telefono2, email1, email2) VALUES (%s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_SESSION['idempresa'], "int"),
                       GetSQLValueString($_POST['nombre'], "text"),
                       GetSQLValueString($_POST['cargo'], "text"),
                       GetSQLValueString($_POST['telefono1'], "text"),
                       GetSQLValueString($_POST['telefono2'], "text"),
                       GetSQLValueString($_POST['email1'], "text"),
                       GetSQLValueString($_POST['email2'], "text"));
  $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
  $mensaje = "Contacto Agregado Correctamente";
}
if(isset($_POST['actualizar_contacto']) && $_POST['actualizar_contacto'] == 1){
	$updateSQL = sprintf("UPDATE contactos SET nombre = %s, cargo = %s, telefono1 = %s, telefono2 = %s, email1 = %s, email2 = %s WHERE idcontacto = %s",
			GetSQLValueString($_POST['nombre'], "text"),
			GetSQLValueString($_POST['cargo'], "text"),
			GetSQLValueString($_POST['telefono1'], "text"),
			GetSQLValueString($_POST['telefono2'], "text"),
			GetSQLValueString($_POST['email1'], "text"),
			GetSQLValueString($_POST['email2'], "text"),
			GetSQLValueString($_POST['idcontacto'], "int"));
	$actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());
	$mensaje = "Contacto Actualizado Correctamente";

}
if(isset($_POST['eliminar_contacto']) && $_POST['eliminar_contacto'] == 1){
	$deleteSQL = "DELETE FROM contactos WHERE idcontacto = $_POST[idcontacto]";
	$eliminar = mysql_query($deleteSQL,$dspp) or die(mysql_error());
	$mensaje = "Contacto Eliminado Correctamente";
}
	$query = "SELECT * FROM contactos WHERE idempresa = $_SESSION[idempresa]";
	$row_contactos = mysql_query($query,$dspp) or die(mysql_error());
	$num_row_contactos = mysql_num_rows($row_contactos);

 ?>

<div class="row">
	<div class="col-md-12">
		<h3>Mis Contactos</h3>
	</div>

	<?php 
	if(isset($mensaje)){
	?>
	<div class="col-md-12 alert alert-success alert-dismissible" role="alert">
	  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	  <?php echo $mensaje; ?>
	</div>
	<?php
	}
	?>

	<div class="col-md-6">
		<b>Agregar Contacto</b>
		<?php 
		if(!empty($_GET['contacts']) && $_GET['contacts'] != 0){
			echo "<a class='btn btn-primary' href='?EMPRESA&contacts'>Nuevo Contacto</a>";
		}
		?>
		<?php 
		if(!empty($_GET['contacts']) && $_GET['contacts'] != 0){
			$idcontacto = $_GET['contacts'];
			$query = "SELECT * FROM contactos WHERE idcontacto = $idcontacto";
			$ejecutar = mysql_query($query,$dspp) or die(mysql_error());
			$datos_contacto = mysql_fetch_assoc($ejecutar);
		?>
			<form action="" method="POST">
				<table class="table">
					<tr>
						<td>* Nombre</td>
						<td><input class="form-control" type="text" id="" name="nombre" value="<?php echo $datos_contacto['nombre']; ?>"></td>
					</tr>
					<tr>
						<td>Cargo</td>
						<td><input class="form-control" type="text" id="" name="cargo" value="<?php echo $datos_contacto['cargo']; ?>"></td>
					</tr>
					<tr>
						<td>* Teléfono 1</td>
						<td><input class="form-control" type="text" id="" name="telefono1" value="<?php echo $datos_contacto['telefono1']; ?>"></td>
					</tr>
					<tr>
						<td>Teléfono 2</td>
						<td><input class="form-control" type="text" id="" name="telefono2" value="<?php echo $datos_contacto['telefono2']; ?>"></td>
					</tr>
					<tr>
						<td>* Email 1</td>
						<td><input class="form-control" type="email" id="" name="email1" value="<?php echo $datos_contacto['email1']; ?>"></td>
					</tr>
					<tr>
						<td>Email 2</td>
						<td><input class="form-control" type="email" id="" name="email2" value="<?php echo $datos_contacto['email2']; ?>"></td>
					</tr>
					<tr>
						<td colspan="2">
							<input class="btn btn-success" type="submit" value="Actualizar Contacto">
							<input type="hidden" name="idcontacto" value="<?php echo $datos_contacto['idcontacto']; ?>">
							<input type="hidden" name="actualizar_contacto" value="1">
						</td>
					</tr>
				</table>
			</form>
		<?php
		}else{
		?>
			<form action="" method="POST">
				<table class="table">
					<tr>
						<td>* Nombre</td>
						<td><input class="form-control" type="text" id="" name="nombre" required></td>
					</tr>
					<tr>
						<td>Cargo</td>
						<td><input class="form-control" type="text" id="" name="cargo"></td>
					</tr>
					<tr>
						<td>* Teléfono 1</td>
						<td><input class="form-control" type="text" id="" name="telefono1" required></td>
					</tr>
					<tr>
						<td>Teléfono 2</td>
						<td><input class="form-control" type="text" id="" name="telefono2"></td>
					</tr>
					<tr>
						<td>* Email 1</td>
						<td><input class="form-control" type="email" id="" name="email1" required></td>
					</tr>
					<tr>
						<td>Email 2</td>
						<td><input class="form-control" type="email" id="" name="email2"></td>
					</tr>
					<tr>
						<td colspan="2">
							<input class="btn btn-success" type="submit" value="Agregar Contacto">
							<input type="hidden" name="agregar_contacto" value="1">
						</td>
					</tr>
				</table>
			</form>
		<?php
		}
		?>
	</div>
	<div class="col-md-6">
		<b>Listado Contactos</b>
		<form action="" method="POST">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>Nombre</th>
						<th>Acciones</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					if($num_row_contactos == 0){
					?>
					<tr class="info">
						<td colspan="2">No se encontraron contactos</td>
					</tr>
					<?php
					}else{
						while($contactos = mysql_fetch_assoc($row_contactos)){
					?>
						<tr <?php if($_GET['contacts'] != 0 && $_GET['contacts'] == $contactos['idcontacto']){ echo "class='info'";} ?>>
							<td><?php echo $contactos['nombre']; ?></td>
			  				<td>
			  					<input type="hidden" name="idcontacto" value="<?php echo $contactos['idcontacto']; ?>">
			  					<a class="btn btn-xs btn-warning" data-toggle="tooltip" title="Ver | Editar Contacto" href="?EMPRESA&contacts=<?php echo $contactos['idcontacto']; ?>"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
			  					<button class="btn btn-xs btn-danger" name="eliminar_contacto" value="1" data-toggle="tooltip" title="Eliminar Contacto" type="submit" onclick="return confirm('¿Está seguro ?, los datos se eliminaran permanentemente');"><span aria-hidden="true" class="glyphicon glyphicon-trash"></span></button>
			  				</td>
						</tr>
					<?php
						}
					}
					 ?>

				</tbody>
			</table>
		</form>
	</div>
</div>