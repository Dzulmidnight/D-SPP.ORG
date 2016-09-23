<ul class="nav nav-pills">
	<li role="presentation"
		<? if(isset($_GET['select'])){?> class="active" <? }?>><a href="?ESTADISTICAS&select">Generales</a></li>

	<li role="presentation"
		<? if(isset($_GET['certificacion'])){?> class="active" <? }?>><a href="?ESTADISTICAS&certificacion">Certificación</a></li>

	<li role="presentation"
		<? if(isset($_GET['listaOPP'])){?> class="active" <? }?>><a href="?ESTADISTICAS&listaOPP">Lista OPP</a></li>

	<li role="presentation"
	<? if(isset($_GET['productos'])){?> class="active" <? }?>>
		<a href="?ESTADISTICAS&productos" aria-label="Left Align">
			<span class="glyphicon glyphicon-open-file" aria-hidden="true"></span> Productos
		</a>
	</li>

	<? if(isset($_GET['detail'])){?>
	<li role="presentation" 
	 class="active" ><a href="#">
		<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> Detalle 	
	 </a>
	</li>
	<? }?>




</ul>

<? if(isset($_GET['mensaje'])){?>
<p>
<div class="alert alert-success" role="alert"><? echo $_GET['mensaje']?></div>
</p>
<? }?>


<?
if(isset($_GET['select'])){include ("generales.php");}
	else
if(isset($_GET['certificacion'])){include ("certificacion.php");}
	else
if(isset($_GET['listaOPP'])){include ("listaOPP.php");}
	else
if(isset($_GET['productos'])){include ("est_productos.php");}
	else
if(isset($_GET['detail'])){include ("opp_detail.php");}
?>