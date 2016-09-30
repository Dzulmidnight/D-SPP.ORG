<ul class="nav nav-pills">
	<li role="presentation"

	<? if(isset($_GET['select'])){?> class="active" <? }?>>
		<a href="?SOLICITUD&select" aria-label="Left Align">
			<span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Solicitudes OPP
		</a>
	

	</li>
	<li role="presentation"

	<? if(isset($_GET['selectCOM'])){?> class="active" <? }?>>
		<a href="?SOLICITUD&selectCOM" aria-label="Left Align">
			<span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Solicitudes Empresas
		</a>
	

	</li>

	<li role="presentation"

	<? if(isset($_GET['add'])){?> class="active" <? }?>>
		<a href="?SOLICITUD&add" aria-label="Left Align">
			<span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Agregar Solicitud
		</a>
	

	</li>


	<? if(isset($_GET['detail'])){?>
	<li role="presentation" 
	 class="active" ><a href="#">Detalle</a></li>
	<? }?>
</ul>

<ul class="nav nav-pills">
	<li role="presentation" <?php if(isset($_GET['select'])){ echo "class='active'"; } ?>>
	</li>
</ul>

<? if(isset($_GET['mensaje'])){?>
<p>
<div class="alert alert-success" role="alert"><? echo $_GET['mensaje']?></div>
</p>
<? }?>


<?
if(isset($_GET['select'])){include ("solicitud_select.php");}
else
if(isset($_GET['add'])){include ("solicitud_add.php");}
else
if(isset($_GET['idsolicitud'])){include ("solicitud_detail.php");}
else
if(isset($_GET['cancel'])){include ("solicitud_cancel.php");}
else 
if(isset($_GET['finalizado'])){include ("proceso_finalizado.php");}
else 
if(isset($_GET['detailBlock'])){include ("solicitud_detailBlock.php");}
else
if(isset($_GET['detailCOM'])){include ("com/solicitud_detail.php");}
else
if(isset($_GET['selectCOM'])){include ("com/solicitud_select.php");}
else
if(isset($_GET['cancelCOM'])){include ("com/solicitud_cancel.php");}
else
if(isset($_GET['finalizadoCOM'])){include ("com/proceso_finalizado.php");}

?>