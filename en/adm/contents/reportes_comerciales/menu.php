<div class="row">
	<div class="btn-group" role="group" aria-label="...">
		<a href="?REPORTES&ingresos=informe_compras" <?php if(isset($_GET['ingresos'])){echo 'class="btn btn-sm btn-primary"';}else{echo 'class="btn btn-sm btn-default"';} ?>>Ingresos cuota de uso</a>
		<a href="?REPORTES&distribucion_p" <?php if(isset($_GET['distribucion_p'])){echo 'class="btn btn-sm btn-primary"';}else{echo 'class="btn btn-sm btn-default"';} ?>>Distribucion plataformas origen</a>
		<a href="?REPORTES&membresias" <?php if(isset($_GET['membresias'])){echo 'class="btn btn-sm btn-primary"';}else{echo 'class="btn btn-sm btn-default"';} ?>>Ingreso por membresias</a>
		<!--<a href="?REPORTES&plataformas" <?php if(isset($_GET['plataformas'])){echo 'class="btn btn-sm btn-primary"';}else{echo 'class="btn btn-sm btn-default"';} ?>><span class="glyphicon glyphicon-home" aria-hidden="true"></span> Plataformas SPP</a>-->
		<a href="?REPORTES&configuracion" <?php if(isset($_GET['configuracion'])){echo 'class="btn btn-sm btn-primary"';}else{echo 'class="btn btn-sm btn-default"';} ?>><span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Configuración</a>
	  <a href="#" <?php if(isset($_GET['f_informe'])){echo 'class="btn btn-sm btn-primary"';}else{echo 'class="btn btn-sm btn-default"';} ?>>Informes</a>
	  <!--24_02_2017<a href="?FINANZAS&f_producto" <?php if(isset($_GET['f_producto'])){echo 'class="btn btn-sm btn-primary"';}else{echo 'class="btn btn-sm btn-default"';} ?>>Producto Terminado</a>24_02_2017-->
	</div>	
</div>



<div class="row">
	<?
	if(isset($_GET['ingresos'])){
		include('informes.php');
	}else if(isset($_GET['distribucion_p'])){
		include('distribucion_plataformas.php');
	}else if(isset($_GET['plataformas'])){
		include('plataformas.php');
	}else if(isset($_GET['f_informe']))
		{include ("f_informes.php");}
	else if(isset($_GET['configuracion'])){
		include("configuracion.php");
	}else if(isset($_GET['f_producto']))
		{include ("f_productos.php");}
	?>		
</div>