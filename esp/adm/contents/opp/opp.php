<ul class="nav nav-pills">
	
	<li role="presentation" <?php if(isset($_GET['map'])){ echo "class='active'"; } ?>>
		<a href="?OPP&map" aria-label="Left Align">
			<span class="glyphicon glyphicon-globe" aria-hidden="true"></span> Distribución OPP
		</a>
	</li>
	<li role="presentation" class="dropdown">
	    <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
	      Organizaciones <span class="caret"></span>
	    </a>
	    <ul class="dropdown-menu">
			<li>
				<a href="?OPP&proceso">En Proceso</a>
			</li>
			<li>
				<a href="?OPP&nuevas">Nuevas</a>
			</li>
			<li>
				<a href="?OPP&canceladas">Canceladas</a>
			</li>
			<li>
				<a href="?OPP&archivadas">Archivadas</a>
			</li>
	    </ul>
  	</li>
	<!--<li role="presentation" <?php if(isset($_GET['select']) || isset($_GET['filed'])){ echo "class='active'"; } ?>>
		<a href="?OPP&select">OPP</a>
	</li>-->
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
if(isset($_GET['map'])){include ("opp_map.php");}
else
if(isset($_GET['select'])){include ("opp_select.php");}
else

if(isset($_GET['proceso'])){include ("opp_proceso.php");}
else
if(isset($_GET['nuevas'])){include ("opp_nuevas.php");}
else
if(isset($_GET['canceladas'])){include ("opp_canceladas.php");}
else
if(isset($_GET['archivadas'])){include ("opp_archivadas.php");}
else

if(isset($_GET['add'])){include ("opp_add.php");}
else
if(isset($_GET['detail'])){include ("opp_detail.php");}
else
if(isset($_GET['filed'])){include ("opp_filed.php");}
else
if(isset($_GET['exportar'])){include ("exportar.php");}

?>