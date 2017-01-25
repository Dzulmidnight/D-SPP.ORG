<div class="row">
	<div class="btn-group" role="group" aria-label="...">
	  <!--<a href="?ESTADISTICAS&select" <?php if(isset($_GET['select'])){echo 'class="btn btn-sm btn-primary"'; }else{echo 'class="btn btn-sm btn-default"'; } ?>>Generales</a>-->
	  <a href="?FINANZAS&select" <?php if(isset($_GET['certificacion']) || isset($_GET['select'])){echo 'class="btn btn-sm btn-primary"';}else{echo 'class="btn btn-sm btn-default"';} ?>>Reporte General</a>
	</div>
	<hr>	
</div>



<? if(isset($_GET['mensaje'])){?>
<p>
<div class="alert alert-success" role="alert"><? echo $_GET['mensaje']?></div>
</p>
<? }?>


<div class="row">
	<?
	if(isset($_GET['select'])){include ("estadisticas_finanzas.php");}
		else
	if(isset($_GET['certificacion'])){include ("certificacion.php");}
		else
	if(isset($_GET['lista_opp'])){include ("lista_opp.php");}
		else
	if(isset($_GET['socios'])){include("socios.php");}
		else
	if(isset($_GET['productos'])){include ("productos.php");}
		else
	if(isset($_GET['detail'])){include ("opp_detail.php");}
	?>		
</div>