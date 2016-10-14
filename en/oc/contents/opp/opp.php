<ul class="nav nav-pills">
	<li role="presentation" <?php if(isset($_GET['select'])){echo "class='active'"; } ?>>
		<a href="?OPP&select">OPP</a>
	</li>
	<li role="presentation" <?php if(isset($_GET['add'])){echo "class='active'"; } ?>>
		<a href="?OPP&add" aria-label="Left Align">
			<span class="glyphicon glyphicon-open-file" aria-hidden="true"></span> Nuevo OPP
		</a>
	</li>
	<?php 
	if(isset($_GET['detail'])){
	?>
	<li class="active">
		<a href="#">
			<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> Detalle 	
		 </a>
	</li>
	<?php
	}
	 ?>
</ul>


<? if(isset($_GET['mensaje'])){?>
<p>
<div class="alert alert-success" role="alert"><? echo $_GET['mensaje']?></div>
</p>
<? }?>


<?
if(isset($_GET['select']))
	{include ("opp_select.php");
}else if(isset($_GET['add'])){
	include ("opp_add.php");
}else if(isset($_GET['detail'])){
	include ("opp_detail.php");
}
?>