<div class="row">
	<div class="btn-group" role="group" aria-label="...">
	  <a href="?CRM&inicio" <?php if(isset($_GET['inicio'])){echo 'class="btn btn-sm btn-primary"';}else{echo 'class="btn btn-sm btn-default"';} ?>>Inicio</a>
	  <a href="?CRM&oportunidades" <?php if(isset($_GET['oportunidades'])){echo 'class="btn btn-sm btn-primary"';}else{echo 'class="btn btn-sm btn-default"';} ?>>Oportunidades</a>
	  <a href="?CRM&po_clientes" <?php if(isset($_GET['po_clientes'])){echo 'class="btn btn-sm btn-primary"';}else{echo 'class="btn btn-sm btn-default"';} ?>>Posibles Clientes</a>
	  <a href="?CRM&cuentas" <?php if(isset($_GET['cuentas'])){echo 'class="btn btn-sm btn-primary"';}else{echo 'class="btn btn-sm btn-default"';} ?>>Cuentas</a>
	  <a href="?CRM&contactos" <?php if(isset($_GET['contactos']) || isset($_GET['select'])){echo 'class="btn btn-sm btn-primary"';}else{echo 'class="btn btn-sm btn-default"';} ?>>Contactos</a>
	  <a href="?CRM&tareas" <?php if(isset($_GET['tareas'])){echo 'class="btn btn-sm btn-primary"';}else{echo 'class="btn btn-sm btn-default"';} ?>>Tareas</a>
	  <a href="?CRM&informes" <?php if(isset($_GET['informes'])){echo 'class="btn btn-sm btn-primary"';}else{echo 'class="btn btn-sm btn-default"';} ?>>Informes</a>
	</div>
	<hr>	
</div>

<div class="row">
	<?
	if(isset($_GET['inicio'])){
		include('inicio.php');
	}else if(isset($_GET['po_clientes'])){
		include('posibles_clientes.php');
	}else if(isset($_GET['cuentas'])){
		include('cuentas.php');
	}else if(isset($_GET['contactos'])){
		include('contactos.php');
	}else if(isset($_GET['oportunidades'])){
		include('oportunidades.php');
	}else if(isset($_GET['tareas'])){
		include('tareas.php');
	}else if(isset($_GET['informes'])){
		include('informes.php');
	}else{
		include('inicio.php');
	}
	?>		
</div>