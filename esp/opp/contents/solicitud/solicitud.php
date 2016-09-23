<ul class="nav nav-pills">
	<li role="presentation" <?php if(isset($_GET['select'])){ echo "class='active'"; } ?>>
		<a href="?SOLICITUD&select">
			<span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Listado Solicitudes
		</a>
	</li>
	<li role="presentation" <?php if(isset($_GET['add'])){ echo "class='active'"; } ?>>
		<a href="?SOLICITUD&add">
			<span class="glyphicon glyphicon-open-file" aria-hidden="true"></span> Nueva Solicitud
		</a>
	</li>
	<?php 
	if(isset($_GET['detail'])){
	?>
		<li role="presentation" class="active">
			<a href="#">Detalle</a>
		</li>
	<?php
	}
	?>
</ul>

<?php
 if(isset($_GET['mensaje'])){
?>
	<p>
		<div class="alert alert-success alert-dismissible" role="alert">
		  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		  <strong><? echo $_GET['mensaje'];?></strong>
		</div>
	</p>
<? }?>


<?
if(isset($_GET['select'])){
	include ("solicitud_select.php");
}else if(isset($_GET['add'])){
	include ("solicitud_add.php");
}else if(isset($_GET['detail'])){
	include ("solicitud_detail.php");
}else if(isset($_GET['detailBlock'])){
	include ("solicitud_detailBlock.php");
}
?>