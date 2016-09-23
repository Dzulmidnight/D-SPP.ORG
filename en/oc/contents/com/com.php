<ul class="nav nav-pills">
<li role="presentation"
<? if(isset($_GET['select'])){?> class="active" <? }?>><a href="?COM&select">Empresa</a></li>
<li role="presentation"
<? if(isset($_GET['add'])){?> class="active" <? }?>>
	<a href="?COM&add" aria-label="Left Align">
		<span class="glyphicon glyphicon-open-file" aria-hidden="true"></span> Nueva Empresa
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
if(isset($_GET['select'])){include ("com_select.php");}
else
if(isset($_GET['add'])){include ("com_add.php");}
else
if(isset($_GET['detail'])){include ("com_detail.php");}
?>