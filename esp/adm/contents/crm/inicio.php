<?php 
require_once('../Connections/dspp.php');
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
mysql_select_db($database_dspp, $dspp);
?>
<div class="btn-group" role="group" aria-label="...">
	<!-- Split button -->
	<div class="btn-group">
	  <button type="button" class="btn btn-sm btn-danger">Todas las Tareas</button>
	  <button type="button" class="btn btn-sm btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	    <span class="caret"></span>
	    <span class="sr-only">Toggle Dropdown</span>
	  </button>
	  <ul class="dropdown-menu">
	    <li><a href="#">Action</a></li>
	    <li><a href="#">Another action</a></li>
	    <li><a href="#">Something else here</a></li>
	    <li role="separator" class="divider"></li>
	    <li><a href="#">Separated link</a></li>
	  </ul>
	</div>
  <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Agregar Tarea</button>
  <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Agregar Nota</button>
  <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Agregar Reunión</button>
	<div class="input-group">
	  <input type="text" class="form-control" placeholder="Search for...">
	  <span class="input-group-btn">
	    <button class="btn btn-default" type="button">Go!</button>
	  </span>
	</div><!-- /input-group -->


</div>	

<!---  TABLA DE ULTIMAS ACCIONES -->
<?php 
	$row_bitacora = mysql_query("SELECT * FROM bitacora_crm", $dspp) or die(mysql_error());
 ?>
<table class="table">
	<thead>
		<tr>
			<th>Acciones</th>
			<th>Asunto</th>
			<th>Fecha Vencimiento</th>
			<th>Estado</th>
			<th>Prioridad</th>
			<th>Tipo Tarea</th>
		</tr>	
	</thead>
	<tbody>
		<?php 
		while($bitacora = mysql_fetch_assoc($row_bitacora)){
		?>
		<tr>
			<td><?php echo $bitacora['accion']; ?></td>
			<td><?php echo $bitacora['idbitacora_crm']; ?></td>
			<td><?php echo $bitacora['idcontacto']; ?></td>
			<td><?php echo $bitacora['idempresa']; ?></td>
			<td><?php echo $bitacora['idtarea']; ?></td>
			<td><?php echo $bitacora['idnota']; ?></td>
		</tr>
		<?php
		}
		 ?>
	</tbody>
</table>