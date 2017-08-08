<ul class="nav nav-pills">
	<li role="presentation" <?php if(isset($_GET['select']) || isset($_GET['filed'])){ echo "class='active'"; } ?>>
		<a href="?OPP&select">OPP</a>
	</li>
	<li role="presentation" <?php if(isset($_GET['add'])){ echo "class='active'"; } ?>>
		<a href="?OPP&add" aria-label="Left Align">
			<span class="glyphicon glyphicon-open-file" aria-hidden="true"></span> Nuevo OPP
		</a>
	</li>
	<?php 
	if(isset($_GET['detail'])){
	?>
		<li role="presentation" 
		 class="active" ><a href="?OPP&detail&idopp=<?php echo $_GET['idopp']; ?>">
			<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> Detalle Organización 	
		 </a>
		</li>
	<?php
	}
	 ?>
	<li role="presentation">
		
	</li>

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
if(isset($_GET['select'])){include ("opp_select.php");}
else
if(isset($_GET['add'])){include ("opp_add.php");}
else
if(isset($_GET['detail'])){include ("opp_detail.php");}
else
if(isset($_GET['filed'])){include ("opp_filed.php");}
else
if(isset($_GET['exportar'])){include ("exportar.php");}

?>