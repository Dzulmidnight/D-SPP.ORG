<div class="row">
	<div class="btn-group" role="group" aria-label="...">
		<a class="btn btn-sm <?php if(isset($_GET['select'])){ echo 'btn-primary'; }else{ echo 'btn-default'; } ?>" href="?SOLICITUD&select">Solicitudes OPP</a>
		<a class="btn btn-sm <?php if(isset($_GET['select_empresa'])){ echo 'btn-primary'; }else{ echo 'btn-default'; } ?>" href="?SOLICITUD&select_empresa">Solicitudes Empresas</a>

	  <!--<button type="button" class="btn btn-default" href="?SOLICITUD&add">Agregar Solicitud</button>-->
	</div>


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
	if(isset($_GET['idsolicitud_empresa'])){include ("empresa/solicitud_detail.php");}
	else
	if(isset($_GET['select_empresa'])){include ("empresa/solicitud_select.php");}
	else
	if(isset($_GET['cancelCOM'])){include ("com/solicitud_cancel.php");}
	else
	if(isset($_GET['finalizadoCOM'])){include ("com/proceso_finalizado.php");}

	?>	

</div>
