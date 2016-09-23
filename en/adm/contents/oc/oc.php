<ul class="nav nav-pills">
<li role="presentation"
<? if(isset($_GET['select'])){?> class="active" <? }?>><a href="?OC&select">OC</a></li>
<li role="presentation"
<? if(isset($_GET['add'])){?> class="active" <? }?>><a href="?OC&add">
		<span class="glyphicon glyphicon-open-file" aria-hidden="true"></span> Nuevo OC
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
if(isset($_GET['select'])){include ("oc_select.php");}
else
if(isset($_GET['add'])){include ("oc_add.php");}
else
if(isset($_GET['detail'])){include ("oc_detail.php");}
else
if(isset($_GET['solicitud'])){include("oc_solicitud.php");}
else
if(isset($_GET['detailBlock'])){include("oc_solicitud_detail.php");}
?>