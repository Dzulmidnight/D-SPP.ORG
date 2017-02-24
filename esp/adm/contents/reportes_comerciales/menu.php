<div class="row">
	<div class="btn-group" role="group" aria-label="...">
		<a href="?REPORTES&informe_compras" <?php if(isset($_GET['informe_compras'])){echo 'class="btn btn-sm btn-primary"';}else{echo 'class="btn btn-sm btn-default"';} ?>>Informes compras</a>
		<a href="?REPORTES&distribucion_p" <?php if(isset($_GET['distribucion_p'])){echo 'class="btn btn-sm btn-primary"';}else{echo 'class="btn btn-sm btn-default"';} ?>>Distribucion plataformas</a>
	  <a href="?FINANZAS&f_informe" <?php if(isset($_GET['f_informe'])){echo 'class="btn btn-sm btn-primary"';}else{echo 'class="btn btn-sm btn-default"';} ?>>Formato Informes</a>
	  <a href="?FINANZAS&f_producto" <?php if(isset($_GET['f_producto'])){echo 'class="btn btn-sm btn-primary"';}else{echo 'class="btn btn-sm btn-default"';} ?>>Formato Producto Terminado</a>
	</div>	
</div>



<div class="row">
	<?
	if(isset($_GET['informe_compras'])){
		include('informe_compras.php');
	}else if(isset($_GET['distribucion_p'])){
		include('distribucion_plataformas.php');
	}
	if(isset($_GET['f_informe'])){include ("f_informes.php");}
		else
	if(isset($_GET['f_producto'])){include ("f_productos.php");}
	?>		
</div>