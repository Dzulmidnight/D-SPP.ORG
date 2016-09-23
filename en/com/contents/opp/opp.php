<ul class="nav nav-pills">

<? if(isset($_GET['detail'])){?>
<li role="presentation" 
 class="active" >
 <a href="#">
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
if(isset($_GET['select'])){include ("opp_select.php");}
else
if(isset($_GET['add'])){include ("opp_add.php");}
else
if(isset($_GET['detail'])){include ("opp_detail.php");}
?>