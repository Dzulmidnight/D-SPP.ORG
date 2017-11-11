<ul class="nav nav-pills">
	
	<li role="presentation" <?php if(isset($_GET['map'])){ echo "class='active'"; } ?>>
		<a href="?OPP&map" aria-label="Left Align">
			<span class="glyphicon glyphicon-globe" aria-hidden="true"></span> Distribución OPP
		</a>
	</li>
	<li <?php if(isset($_GET['sin_solicitud'])){ echo "class='active'"; } ?>>
		<a href="?OPP&sin_solicitud">Sin Solicitud</a>
	</li>
	<li <?php if(isset($_GET['proceso'])){ echo "class='active'"; } ?>>
		<a href="?OPP&proceso&proceso_primera_vez">En Proceso</a>
	</li>
	<li <?php if(isset($_GET['certificadas'])){ echo "class='active'"; } ?>>
		<a href="?OPP&certificadas&nuevo_certificado">Certificadas</a>
	</li>
	<li <?php if(isset($_GET['canceladas'])){ echo "class='active'"; } ?>>
		<a href="?OPP&canceladas">Canceladas</a>
	</li>
	<li <?php if(isset($_GET['suspendidas'])){ echo "class='active'"; } ?>>
		<a href="?OPP&suspendidas">Suspendidas</a>
	</li>
	<li <?php if(isset($_GET['archivadas'])){ echo "class='active'"; } ?>>
		<a href="?OPP&archivadas">Archivadas</a>
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
  function mayuscula($variable) {
    $variable = strtr(strtoupper($variable),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
    return $variable;
  }
  
  mysql_select_db($database_dspp, $dspp);

if(isset($_GET['map'])){include ("opp_map.php");}
else
if(isset($_GET['sin_solicitud'])){include ("opp_sin_solicitud.php");}
else
if(isset($_GET['select'])){include ("opp_select.php");}
else
if(isset($_GET['proceso'])){include ("opp_proceso.php");}
else
if(isset($_GET['certificadas'])){include ("opp_certificadas.php");}
else
if(isset($_GET['suspendidas'])){include ("opp_suspendidas.php");}
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