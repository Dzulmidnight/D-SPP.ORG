<?php
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

	$fecha = time();
  //DEFINO LA RUTA DEL FORMATO DE documentacion
  $rutaArchivo = "../../archivos/formatos/documentacion/";

if ((isset($_POST["agregar_documentacion"])) && ($_POST["agregar_documentacion"] == "1")) {
  //AL NOMBRE DEL ARCHIVO(FORMATO) LE CONCATENO EL TIME
  if(!empty($_FILES['archivo']['name'])){
      $_FILES["archivo"]["name"];
        move_uploaded_file($_FILES["archivo"]["tmp_name"], $rutaArchivo.time()."_".$_FILES["archivo"]["name"]);
        $archivo = $rutaArchivo.basename(time()."_".$_FILES["archivo"]["name"]);
  }else{
    $archivo = NULL;
  }
  $insertSQL = sprintf("INSERT INTO documentacion (idestatus_interno, nombre, archivo, idioma, fecha_registro) VALUES (%s, %s, %s, %s, %s)",
  	GetSQLValueString($_POST['idestatus_interno'], "int"),
  	GetSQLValueString($_POST['nombre'], "text"),
  	GetSQLValueString($archivo, "text"),
  	GetSQLValueString($_POST['idioma'], "text"),
  	GetSQLValueString($fecha, "int"));
  $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
  $mensaje = "Se ha agregado el documento correctamente";

}
if(isset($_POST['actualizar_documentacion']) && $_POST['actualizar_documentacion'] == 1){

}

if(isset($_POST['actualizar_documentacion']) && $_POST['actualizar_documentacion'] == "1"){
  //AL NOMBRE DEL ARCHIVO(FORMATO) LE CONCATENO EL TIME
  if(!empty($_FILES['nuevo_archivo']['name'])){
      unlink($_POST['archivo']);//BORRO EL ARCHIVO ANTERIOR ANTES DE ACTUALIZAR

      $_FILES["nuevo_archivo"]["name"];
        move_uploaded_file($_FILES["nuevo_archivo"]["tmp_name"], $rutaArchivo.time()."_".$_FILES["nuevo_archivo"]["name"]);
        $archivo = $rutaArchivo.basename(time()."_".$_FILES["nuevo_archivo"]["name"]);
  }else{
    $archivo = $_POST['archivo'];
  }
  $updateSQL = sprintf("UPDATE documentacion SET nombre = %s, archivo = %s, idestatus_interno = %s, idioma = %s WHERE iddocumento = %s",
  	GetSQLValueString($_POST['nombre'], "text"),
  	GetSQLValueString($_POST['archivo'], "text"),
  	GetSQLValueString($_POST['idestatus_interno'], "int"),
  	GetSQLValueString($_POST['idioma'], "text"),
  	GetSQLValueString($_POST['iddocumento'], "int"));
  $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

  $mensaje = "Se ha actualizado el documento correctamente";

}
if(isset($_POST['eliminar_documentacion']) && $_POST['eliminar_documentacion'] == 1){
  unlink($_POST['archivo']);//ELIMINO EL ARCHIVO
  $deleteSQL = sprintf("DELETE FROM documentacion WHERE iddocumento=%s",
                       GetSQLValueString($_POST['iddocumento'], "int"));
  $eliminaran = mysql_query($deleteSQL, $dspp) or die(mysql_error());
}


	$row_documentacion = mysql_query("SELECT documentacion.*, estatus_interno.nombre AS 'nombre_interno' FROM documentacion LEFT JOIN estatus_interno ON documentacion.idestatus_interno = estatus_interno.idestatus_interno",$dspp) or die(mysql_error());

	$row_estatus_interno = mysql_query("SELECT * FROM estatus_interno", $dspp) or die(mysql_error());
	//$query_status = "SELECT * FROM status";
	//$consulta_status = mysql_query($query_status,$dspp) or die(mysql_errno());
?>

<div class="col-xs-12">
	<h1 class="page-header">Documentación</h1>
  <?php 
  if(isset($mensaje)){
  ?>
  <div class="col-md-12 alert alert-success alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 style="font-size:14px;" class="text-center"><?php echo $mensaje; ?><h4/>
  </div>
  <?php
  }
  ?>
	<div class="col-xs-6">
		<?php 
		if(isset($_POST['actualizar'])){
			$iddocumento = $_POST['actualizar'];
			$row_datos = mysql_query("SELECT * FROM documentacion WHERE iddocumento = $iddocumento", $dspp) or die(mysql_error());
			$datos = mysql_fetch_assoc($row_datos);
		 ?>
			<form method="POST" name="form2" action="<?php echo $editFormAction; ?>" enctype="multipart/form-data">
				<table class="table" style="font-size:12px;">
					<thead>
						<tr>
							<th>Nombre</th>
							<th>Idioma</th>
							<th>Actualizar Archivo</th>
							<th>Estatus</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><input type="text" class="form-control" name="nombre" value="<?php echo $datos['nombre']; ?>"></td>
							<td>
								<select class="form-control" name="idioma" id="">
									<option value="">Seleccione un idioma</option>
									<option value="ESP" <?php if($datos['idioma'] == 'ESP'){ echo 'selected'; } ?>>Español</option>
									<option value="EN" <?php if($datos['idioma'] == 'EN'){ echo 'selected'; } ?>>Ingles</option>
								</select>
							</td>
							<td>
								<input type="file" class="form-control" name="nuevo_archivo">
								<input type="hidden" name="archivo" value="<?php echo $datos['archivo']; ?>">
							</td>
							<td>
								<select name="idestatus_interno" id="" class="form-control">
									<?php 
									while($estatus_interno = mysql_fetch_assoc($row_estatus_interno)){
									?>
										<option value="<?php echo $estatus_interno['idestatus_interno'];?>"<?php if($estatus_interno['idestatus_interno'] == $datos['idestatus_interno']){ echo "selected";} ?>><?php echo $estatus_interno['nombre']; ?></option>
									 <?php 
									}
									  ?>
								</select>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<button type="submit" class="btn btn-primary" name="actualizar_documentacion" value="1">Actualizar Archivo</button>
							</td>
						</tr>
					</tbody>
					<input type="hidden" name="iddocumento" value="<?php echo $datos['iddocumento']; ?>">
				</table>
			</form> 
		<?php 
		}else{
		 ?>
			<form method="POST" name="form1" action="<?php echo $editFormAction; ?>" enctype="multipart/form-data">
				<table class="table" style="font-size:12px;">
					<thead>
						<tr>
							<th>Nombre</th>
							<th>Idioma</th>
							<th>Archivo</th>
							<th>Estatus( <small>Utilizar en:</small> )</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><input type="text" class="form-control" name="nombre" placeholder="Nombre"></td>
							<td>
								<select class="form-control" name="idioma" id="" required>
									<option value="">Seleccione un idioma</option>
									<option value="ESP">Español</option>
									<option value="EN">Ingles</option>
								</select>
							</td>
							<td><input type="file" class="form-control" name="archivo" required></td>
							<td>
								<select name="idestatus_interno" id="" class="form-control" required>
									<option value="">Selecciona un Estatus</option>
									<?php 
									while($estatus_interno = mysql_fetch_assoc($row_estatus_interno)){
									?>
										<option value="<?php echo $estatus_interno['idestatus_interno']; ?>"><?php echo $estatus_interno['nombre']; ?></option>
									 <?php 
									}
									  ?>
								</select>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<button type="submit" class="btn btn-primary" name="agregar_documentacion" value="1">Agregar Documento</button>
							</td>
						</tr>
					</tbody>
				</table>
			</form> 
		 <?php 
		}
		  ?>
	</div>

	<div class="col-xs-6">
		<table class="table table-condensed" style="font-size:12px;">
			<thead>
				<tr>
					<th colspan="4" class="text-center">Listado de Anexos</th>
				</tr>
				<tr>
					<th>Idioma</th>
					<th>Nombre</th>
					<th>Archivo</th>
					<th>Estatus( <small>Utilizar en:</small> )</th>
					<th>Agregado el:</th>
				</tr>
			</thead>
			<tbody>
				<?php 
				while($documentacion = mysql_fetch_assoc($row_documentacion)){
				 ?>
				<form action="" method="POST">
				 	<tr>
				 		<td><?php echo $documentacion['idioma']; ?></td>
				 		<td><?php echo $documentacion['nombre']; ?></td>
				 		<td><?php echo "<a href='".$documentacion['archivo']."' target='_blank'>Visualizar Formato</a>"; ?></td>
				 		<td><?php echo $documentacion['nombre_interno']; ?></td>
				 		<td><?php echo date('d/m/Y',$documentacion['fecha_registro']); ?></td>
				 		<td>
				 			<button type="submit" class="btn btn-sm btn-warning" data-toggle="tooltip" title="Actualizar Documento" name="actualizar" value="<?php echo $documentacion['iddocumento']; ?>"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
				 		</td>

				 		<td>
							<button class="btn btn-sm btn-danger" data-toggle="tooltip" title="Eliminar Nota" type="submit" onclick="return confirm('¿Está seguro ?, los datos se eliminaran permanentemente');" name="eliminar_documentacion" value="1"><span aria-hidden="true" class="glyphicon glyphicon-trash"></span></button>
				 		</td>
			              <input type="hidden" name="iddocumento" value="<?php echo $documentacion['iddocumento']?>" />
			              <input type="hidden" name="nombre" value="<?php echo $documentacion['nombre']?>" />
			              <input type="hidden" name="idioma" value="<?php echo $documentacion['idioma']; ?>">
			              <input type="hidden" name="archivo" value="<?php echo $documentacion['archivo']?>" />
			              <input type="hidden" name="idestatus_interno" value="<?php echo $documentacion['idestatus_interno']?>" />
				 	</tr>
				</form>

				<?php
				}
				?>
			</tbody>
		</table>
	</div>

</div>