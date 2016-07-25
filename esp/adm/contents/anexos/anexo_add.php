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

  //DEFINO LA RUTA DEL FORMATO DE anexo
  $rutaArchivo = "../formatos/anexos/";

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  //AL NOMBRE DEL ARCHIVO(FORMATO) LE CONCATENO EL TIME
  if(!empty($_FILES['archivo']['name'])){
      $_FILES["archivo"]["name"];
        move_uploaded_file($_FILES["archivo"]["tmp_name"], $rutaArchivo.time()."_".$_FILES["archivo"]["name"]);
        $archivo = $rutaArchivo.basename(time()."_".$_FILES["archivo"]["name"]);
  }else{
    $archivo = NULL;
  }
  $insertSQL = sprintf("INSERT INTO anexos (anexo,archivo,idstatus_interno) VALUES (%s, %s, %s)",
                       GetSQLValueString($_POST['anexo'], "text"),
                       GetSQLValueString($archivo, "text"),
                       GetSQLValueString($_POST['idstatus_interno'], "int"));


  $Result1 = mysql_query($insertSQL, $dspp) or die(mysql_error());
}
if(isset($_POST['MM_update']) && $_POST['MM_update'] == "form2"){
  //AL NOMBRE DEL ARCHIVO(FORMATO) LE CONCATENO EL TIME
  if(!empty($_FILES['archivo_actualizar']['name'])){
      unlink($_POST['archivo']);//BORRO EL ARCHIVO ANTERIOR ANTES DE ACTUALIZAR

      $_FILES["archivo_actualizar"]["name"];
        move_uploaded_file($_FILES["archivo_actualizar"]["tmp_name"], $rutaArchivo.time()."_".$_FILES["archivo_actualizar"]["name"]);
        $archivo = $rutaArchivo.basename(time()."_".$_FILES["archivo_actualizar"]["name"]);
  }else{
    $archivo = $_POST['archivo'];
  }

  $updateSQL = sprintf("UPDATE anexos SET anexo=%s, archivo=%s, idstatus_interno=%s WHERE idanexo=%s",
                       GetSQLValueString($_POST['anexo'], "text"),
                       GetSQLValueString($archivo, "text"),
                       GetSQLValueString($_POST['idstatus_interno'], "int"),
                       GetSQLValueString($_POST['idanexo'], "int"));


  $Result1 = mysql_query($updateSQL, $dspp) or die(mysql_error());

}
if ((isset($_POST['eliminar'])) && ($_POST['idanexo'] != "")) {
  unlink($_POST['archivo']);//ELIMINO EL ARCHIVO
  $deleteSQL = sprintf("DELETE FROM anexos WHERE idanexo=%s",
                       GetSQLValueString($_POST['idanexo'], "int"));

  $Result1 = mysql_query($deleteSQL, $dspp) or die(mysql_error());
}

	$query = "SELECT anexos.*, status.idstatus,status.nombre AS 'nombreStatus' FROM anexos LEFT JOIN status ON anexos.idstatus_interno = status.idstatus";
	$anexos = mysql_query($query,$dspp) or die(mysql_error());

	$query_status = "SELECT * FROM status";
	$consulta_status = mysql_query($query_status,$dspp) or die(mysql_errno());
?>

<div class="col-xs-12">
	<h1 class="page-header">Anexos</h1>

	<div class="col-xs-6">
		<?php 
		if(isset($_POST['actualizar'])){
		 ?>
			<form method="POST" name="form2" action="<?php echo $editFormAction; ?>" enctype="multipart/form-data">
				<table class="table">
					<thead>
						<tr>
							<th>Nombre</th>
							<th>Archivo</th>
							<th>Estatus</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><input type="text" class="form-control" name="anexo" value="<?php echo $_POST['anexo']; ?>"></td>
							<td>
								<input type="file" class="form-control" name="archivo_actualizar">
								<input type="hidden" name="archivo" value="<?php echo $_POST['archivo']; ?>">
							</td>
							<td>
								<select name="idstatus_interno" id="" class="form-control">
									<?php 
									while($row_status = mysql_fetch_assoc($consulta_status)){
									?>
										<option value="<?php echo $row_status['idstatus'];?>"<?php if($row_status['idstatus'] == $_POST['idstatus_interno']){ echo "selected";} ?>><?php echo $row_status['nombre']; ?></option>
									 <?php 
									}
									  ?>
								</select>
							</td>
						</tr>
						<tr>
							<td colspan="2"><button type="submit" class="btn btn-primary">Actualizar</button></td>
						</tr>
					</tbody>
					<input type="hidden" name="MM_update" value="form2">
					<input type="hidden" name="idanexo" value="<?php echo $_POST['idanexo']; ?>">
				</table>
			</form> 
		<?php 
		}else{
		 ?>
			<form method="POST" name="form1" action="<?php echo $editFormAction; ?>" enctype="multipart/form-data">
				<table class="table">
					<thead>
						<tr>
							<th>Nombre</th>
							<th>Archivo</th>
							<th>Estatus( <small>Utilizar en:</small> )</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><input type="text" class="form-control" name="anexo" placeholder="Nombre del Formato"></td>
							<td><input type="file" class="form-control" name="archivo" required></td>
							<td>
								<select name="idstatus_interno" id="" class="form-control" required>
									<option value="">Selecciona un Estatus</option>
									<?php 
									while($row_status = mysql_fetch_assoc($consulta_status)){
									?>
										<option value="<?php echo $row_status['idstatus']; ?>"><?php echo $row_status['nombre']; ?></option>
									 <?php 
									}
									  ?>
								</select>
							</td>
						</tr>
						<tr>
							<td colspan="2"><button type="submit" class="btn btn-primary">Insertar</button></td>
						</tr>
					</tbody>
					<input type="hidden" name="MM_insert" value="form1">
				</table>
			</form> 
		 <?php 
		}
		  ?>
	</div>

	<div class="col-xs-6">
		<table class="table">
			<thead>
				<tr>
					<th colspan="4" class="text-center">Listado de Anexos</th>
				</tr>
				<tr>
					<th>Nombre</th>
					<th>Archivo</th>
					<th>Estatus( <small>Utilizar en:</small> )</th>
				</tr>
			</thead>
			<tbody>
				<?php 
				while($row_anexo = mysql_fetch_assoc($anexos)){
				 ?>
				 	<tr>
				 		<td><?php echo $row_anexo['anexo']; ?></td>
				 		<td><?php echo "<a href='".$row_anexo['archivo']."' target='_blank'>Visualizar Formato</a>"; ?></td>
				 		<td><?php echo $row_anexo['nombreStatus']; ?></td>
			            <td class="text-center" width="42px">
			              <form method="post">
			              <button type="submit" name="actualizar" class="btn btn-warning"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
			              <input type="hidden" name="idanexo" value="<?php echo $row_anexo['idanexo']?>" />
			              <input type="hidden" name="anexo" value="<?php echo $row_anexo['anexo']?>" />
			              <input type="hidden" name="archivo" value="<?php echo $row_anexo['archivo']?>" />
			              <input type="hidden" name="idstatus_interno" value="<?php echo $row_anexo['idstatus_interno']?>" />
			              </form>
			            </td>
			            <td class="text-center" width="42px">
			              <form method="post">
			              <button type="submit" name="eliminar" class="btn btn-danger"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
			              <input type="hidden" name="archivo" value="<?php echo $row_anexo['archivo']?>" />
			              <input type="hidden" name="idanexo" value="<?php echo $row_anexo['idanexo']?>" />
			              </form>
			            </td>
				 	</tr>
				<?php
				}
				?>
			</tbody>
		</table>
	</div>

</div>