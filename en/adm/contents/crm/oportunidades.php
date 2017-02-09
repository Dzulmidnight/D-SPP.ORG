<?php include('funciones.php');?>
<div class="btn-group" role="group" aria-label="...">
	<!-- Split button -->
	<div class="btn-group">
	  <a href="?CRM&oportunidades" class="btn btn-sm btn-default">Todas las Oportunidades</a>
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
  <a href="?CRM&oportunidades=add" <?php clase_boton('oportunidades','add'); ?>><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Agregar Oportunidad</a>
  <a href="?CRM&oportunidades=add_tarea" <?php clase_boton('oportunidades','add_tarea'); ?>><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Agregar Tarea</a>
  <a href="?CRM&oportunidades=add_nota" <?php clase_boton('oportunidades','add_nota'); ?>><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Agregar Nota</a>
  <a href="?CRM&oportunidades=add_reunion" <?php clase_boton('oportunidades','add_reunion'); ?>><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Agregar Reunión</a>
	<div class="input-group">
	  <input type="text" class="form-control" placeholder="Search for...">
	  <span class="input-group-btn">
	    <button class="btn btn-default" type="button">Go!</button>
	  </span>
	</div><!-- /input-group -->
</div>	

<?php 
switch ($_GET['oportunidades']) {
	case 'add':
		include('oportunidades/add_oportunidad.php');
		break;
	case 'add_tarea':
		include('oportunidades/add_tarea.php');
		break;
	case 'add_nota':
		include('oportunidades/add_nota.php');
		break;
	case 'add_reunion':
		include('oportunidades/add_tarea.php');
		break;
	default:
		include('oportunidades/tabla_oportunidades.php');
		break;
}
 ?>