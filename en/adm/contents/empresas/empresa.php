<ul class="nav nav-pills">
	<li role="presentation" <?php if(isset($_GET['select'])){ echo "class='active'"; } ?>>
		<a href="?EMPRESAS&select">Empresa</a>
	</li>
	<li role="presentation" <?php if(isset($_GET['add'])){ echo "class='active'"; } ?>>
		<a href="?EMPRESAS&add">Nueva Empresa</a>
	</li>
	<? if(isset($_GET['detail'])){?>
		<li role="presentation" 
		 class="active" ><a href="?EMPRESAS&detail&idempresa=<?php echo $_GET['idempresa']; ?>">
			<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> Detalle Empresa 	
		 </a>
		</li>

	<? }?>
</ul>


<?php 
if(isset($mensaje)){
?>
<div class="col-md-12 alert alert-success alert-dismissible" role="alert">
<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
<h4 style="font-size:14px;" class="text-center"><?php echo $mensaje; ?><h4/>
</div>
<?php
}
?>




<?
if(isset($_GET['select'])){include ("empresa_select.php");}
else
if(isset($_GET['add'])){include ("empresa_add.php");}
else
if(isset($_GET['detail'])){include ("empresa_detail.php");}
else
if(isset($_GET['filed'])){include ("empresa_filed.php");}
else
if(isset($_GET['detailCOM'])){include ("contents/solicitud/com/solicitud_detail.php");}
?>