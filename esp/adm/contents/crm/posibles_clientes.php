<?php include('funciones.php');?>
<div class="btn-group" role="group" aria-label="...">
	<!-- Split button -->
	<div class="btn-group">
	  <a href="?CRM&po_clientes" class="btn btn-sm btn-default">Todas los posibles Clientes</a>
	  <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
  <a href="?CRM&po_clientes=add" <?php clase_boton('po_clientes','add'); ?>><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Agregar Posible Cliente</a>
  <a href="?CRM&po_clientes=add_tarea" <?php clase_boton('po_clientes','add_tarea'); ?>><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Agregar Tarea</a>
  <a href="?CRM&po_clientes=add_nota" <?php clase_boton('po_clientes','add_nota'); ?>><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Agregar Nota</a>
  <a href="?CRM&po_clientes=add_reunion" <?php clase_boton('po_clientes','add_reunion'); ?>><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Agregar Reunión</a>
	<div class="input-group">
	  <input type="text" class="form-control" placeholder="Search for...">
	  <span class="input-group-btn">
	    <button class="btn btn-default" type="button">Go!</button>
	  </span>
	</div><!-- /input-group -->
</div>	

<?php 
switch ($_GET['po_clientes']) {
	case 'add':
		include('add_cliente.php');
		break;
	case 'add_tarea':
		include('add_tarea.php');
		break;
	case 'add_nota':
		include('add_nota.php');
		break;
	case 'add_reunion':
		include('add_tarea.php');
		break;
	default:
		include('tabla_posibles_clientes.php');
		break;
}
 ?>