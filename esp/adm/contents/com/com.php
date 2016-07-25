<ul class="nav nav-pills">
<li role="presentation"
<? if(isset($_GET['select'])){?> class="active" <? }?>><a href="?COM&select">Empresa</a></li>
<li role="presentation"
<? if(isset($_GET['add'])){?> class="active" <? }?>><a href="?COM&add">Nueva Empresa</a></li>

<? if(isset($_GET['detail'])){?>
<li role="presentation" 
 class="active" ><a href="#">Detalle</a></li>
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
else
if(isset($_GET['filed'])){include ("com_filed.php");}
else
if(isset($_GET['detailCOM'])){include ("contents/solicitud/com/solicitud_detail.php");}
?>