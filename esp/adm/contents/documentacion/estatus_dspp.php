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



if ((isset($_POST["MM_insert_estatus"])) && ($_POST["MM_insert_estatus"] == "form1")) {

	if(!empty($_POST['idstatus_interno'])){
	  $insertSQL = sprintf("UPDATE status SET descripcion_interna = %s WHERE idstatus = %s",
	                       GetSQLValueString($_POST['descripcion_estatus'], "text"),
	                       GetSQLValueString($_POST['idstatus_interno'], "int"));
	}
	if(!empty($_POST['idstatus_publico'])){
	  $insertSQL = sprintf("UPDATE status_publico SET descripcion_publica = %s WHERE idstatus_publico = %s",
	                       GetSQLValueString($_POST['descripcion_estatus'], "text"),
	                       GetSQLValueString($_POST['idstatus_publico'], "int"));
	}


  $Result1 = mysql_query($insertSQL, $dspp) or die(mysql_error());
}
if(isset($_POST['MM_actualizar_estatus']) && $_POST['MM_actualizar_estatus'] == "form_status"){

	if(!empty($_POST['idstatus_interno'])){
	  $updateSQL = sprintf("UPDATE status SET descripcion_interna = %s WHERE idstatus = %s",
	                       GetSQLValueString($_POST['descripcion_estatus'], "text"),
	                       GetSQLValueString($_POST['idstatus_interno'], "int"));
	}
	if(!empty($_POST['idstatus_publico'])){
	  $updateSQL = sprintf("UPDATE status_publico SET descripcion_publica = %s WHERE idstatus_publico = %s",
	                       GetSQLValueString($_POST['descripcion_estatus'], "text"),
	                       GetSQLValueString($_POST['idstatus_publico'], "int"));
	}


  $Result1 = mysql_query($updateSQL, $dspp) or die(mysql_error());

}


	$query = "SELECT * FROM status ORDER BY nombre";
	$estatus_interno = mysql_query($query,$dspp) or die(mysql_error());

	$query = "SELECT * FROM status_publico ORDER BY nombre";
	$estatus_publico = mysql_query($query,$dspp) or die(mysql_error());

	$query_status_interno = "SELECT * FROM status ORDER BY nombre";
	$consulta_status_interno = mysql_query($query_status_interno,$dspp) or die(mysql_error());

	$query_status_publico = "SELECT * FROM status_publico ORDER BY nombre";
	$consulta_status_publico = mysql_query($query_status_publico,$dspp) or die(mysql_error());

?>

<div class="col-xs-12">
	<h1 class="page-header">Estatus D-SPP( Interno / Publico)</h1>
	<div class="col-xs-12">
		<p class="alert alert-info">En caso requerir alguna descripción sobre un estatus favor de seleccionar el nombre del estatus y agregar la descripción. </p>
	</div>
	<div class="col-xs-6">
		<?php 
		if(isset($_POST['actualizar_descripcion'])){
		 ?>
			<form method="POST" name="form2" action="<?php echo $editFormAction; ?>" enctype="multipart/form-data">
				<table class="table">
					<thead>
						<tr>
							<th>Estatus</th>
							<th>Descripción</th>
						</tr>
					</thead>
					<tbody>
						<?php 
						if(isset($_POST['estatus_interno']) && $_POST['estatus_interno'] == "estatus_interno"){
						 ?>
							<tr>
								<td>
									<select name="idstatus_interno" id="" class="form-control">
										<option value="">Estatus Interno</option>
										<?php 
										while($row_status_interno = mysql_fetch_assoc($consulta_status_interno)){
										?>
											<option value="<?php echo $row_status_interno['idstatus'];?>"<?php if($row_status_interno['idstatus'] == $_POST['idstatus_interno']){ echo "selected";} ?>><?php echo $row_status_interno['nombre']; ?></option>
										 <?php 
										}
										  ?>
									</select>
								</td>

								<td>
									<textarea name="descripcion_estatus" class="form-control" id="" cols="30" rows="10"><?php echo $_POST['descripcion_interna'] ?></textarea>
								</td>
							</tr>

						<?php
						}
						?>
						<?php 
						if(isset($_POST['estatus_publico']) && $_POST['estatus_publico'] == "estatus_publico"){
						 ?>
							<tr>
								<td>
									<select name="idstatus_publico" id="" class="form-control">
										<option value="">Estatus Publico</option>
										<?php 
										while($row_status_publico = mysql_fetch_assoc($consulta_status_publico)){
										?>
											<option value="<?php echo $row_status_publico['idstatus_publico'];?>"<?php if($row_status_publico['idstatus_publico'] == $_POST['idstatus_publico']){ echo "selected";} ?>><?php echo $row_status_publico['nombre']; ?></option>
										 <?php 
										}
										  ?>
									</select>
								</td>
								<td>
									<textarea name="descripcion_estatus" class="form-control" id="" cols="30" rows="10"><?php echo $_POST['descripcion_publica'] ?></textarea>
								</td>

							</tr>
						<?php
						}
						?>
						<tr>
							<td colspan="2"><button type="submit" class="btn btn-primary">Actualizar</button></td>
						</tr>
					</tbody>
					<input type="hidden" name="MM_actualizar_estatus" value="form_status">
				</table>
			</form> 
		<?php 
		}else{
		 ?>
			<form method="POST" name="form1" action="<?php echo $editFormAction; ?>" enctype="multipart/form-data">
				<table class="table">
					<thead>
						<tr>
							<th>Estatus</th>
							<th>Descripción</th>
							
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<select name="idstatus_interno" id="" class="form-control">
									<option value="">Estatus Interno</option>
									<?php 
									while($row_status_interno = mysql_fetch_assoc($consulta_status_interno)){
									?>
										<option value="<?php echo $row_status_interno['idstatus']; ?>"><?php echo $row_status_interno['nombre']; ?></option>
									 <?php 
									}
									  ?>
								</select>
								<hr>
								<select name="idstatus_publico" id="" class="form-control">
									<option value="">Estatus Publico</option>
									<?php 
									while($row_status_publico = mysql_fetch_assoc($consulta_status_publico)){
									?>
										<option value="<?php echo $row_status_publico['idstatus_publico']; ?>"><?php echo $row_status_publico['nombre']; ?></option>
									 <?php 
									}
									  ?>
								</select>

							</td>
							<td><textarea name="descripcion_estatus" id="" cols="30" rows="10" class="form-control"></textarea></td>
						</tr>
						<tr>
							<td colspan="2"><button type="submit" class="btn btn-primary">Insertar</button></td>
						</tr>
					</tbody>
					<input type="hidden" name="MM_insert_estatus" value="form1">
				</table>
			</form> 
		 <?php 
		}
		  ?>
	</div>

	<div class="col-xs-6">
		<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
		  <div class="panel panel-default">

		    <div class="panel-heading" role="tab" id="headingOne">
		      <h4 class="panel-title">
		        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
		         Listado Estatus Interno
		        </a>
		      </h4>
		    </div>
		    <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
		      <div class="panel-body">
				<table class="table table-striped">
						<thead>
							<tr>
								<th>Estatus</th>
								<th>Descripción</th>
							</tr>
						</thead>
						<tbody>
						<?php 
						while($row_estatus_interno = mysql_fetch_assoc($estatus_interno)){
						 ?>
						 	<tr>
						 		<td><?php echo $row_estatus_interno['nombre']; ?></td>
						 		<td><?php if(isset($row_estatus_interno['descripcion_interna'])){echo $row_estatus_interno['descripcion_interna'];}else{echo "No Disponible";} ?></td>
					            <td class="text-center" width="42px">
					              <form method="post">
					              <button type="submit" name="actualizar_descripcion" class="btn btn-warning"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
					              <input type="hidden" name="idstatus_interno" value="<?php echo $row_estatus_interno['idstatus']?>" />
					              <input type="hidden" name="descripcion_interna" value="<?php echo $row_estatus_interno['descripcion_interna']?>" />
					              <input type="hidden" name="estatus_interno" value="estatus_interno">
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
		  </div>

		  <div class="panel panel-default">
		    <div class="panel-heading" role="tab" id="headingTwo">
		      <h4 class="panel-title">
		        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
		        	Listado Estatus Publico
		        </a>
		      </h4>
		    </div>
		    <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
		      <div class="panel-body">
				<table class="table table-striped">
					<thead>
						<tr>
							<th>Nombre</th>
							<th>Descripción</th>
						</tr>
					</thead>
					<tbody>
					<?php 
					while($row_estatus_publico = mysql_fetch_assoc($estatus_publico)){
					 ?>
					 	<tr>
					 		<td><?php echo $row_estatus_publico['nombre']; ?></td>
					 		<td><?php if(isset($row_estatus_publico['descripcion_publica'])){echo $row_estatus_publico['descripcion_publica'];}else{echo "No Disponible";} ?></td>
				            <td class="text-center" width="42px">
				              <form method="post">
				              <button type="submit" name="actualizar_descripcion" class="btn btn-warning"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
				              <input type="hidden" name="idstatus_publico" value="<?php echo $row_estatus_publico['idstatus_publico']?>" />
				              <input type="hidden" name="descripcion_publica" value="<?php echo $row_estatus_publico['descripcion_publica']?>" />
				              <input type="hidden" name="estatus_publico" value="estatus_publico">
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
		  </div>
		</div>
	</div>
</div>