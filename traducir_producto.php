<?php 
require_once('Connections/dspp.php'); 

mysql_select_db($database_dspp, $dspp);


if (!function_exists("GetSQLValueString")) {
	function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
	{
	  if (PHP_VERSION < 6) {
	    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
	  }

	  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

	  switch ($theType) {
	    case "text":
	      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
	      break;    
	    case "long":
	    case "int":
	      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
	      break;
	    case "double":
	      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
	      break;
	    case "date":
	      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
	      break;
	    case "defined":
	      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
	      break;
	  }
	  return $theValue;
	}
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}
	$charset='utf-8';

	if(isset($_POST['guardar_traduccion']) && $_POST['guardar_traduccion'] == 1){

		if(!empty($_POST['producto_ingles'])){
			$updateSQL = sprintf("UPDATE productos SET producto_ingles = %s WHERE producto = %s",
				GetSQLValueString(strtoupper($_POST['producto_ingles']), "text"),
				GetSQLValueString($_POST['producto'], "text"));
			$actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());
		}
	}
	$row_productos = mysql_query("SELECT producto, producto_ingles FROM productos GROUP BY producto", $dspp) or die(mysql_error());
	/*************************** INICIA INSERTAR CERTIFICACIONES ***************************/
	
 ?>

<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="img/FUNDEPPO.png">
    <title>SPP GLOBAL | D-SPP</title>

    <!-- Bootstrap core CSS -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="bootstrap/css/bootstrap-theme.css" rel="stylesheet">
    <link href="bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="bootstrap/css/bootstrap-theme.min.css" rel="stylesheet">

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>


  </head>

  <body>

	<div class="container">

	<?php 
	if(isset($mensaje_productos)){
	?>
	<div class="col-md-12 alert alert-success alert-dismissible" role="alert">
	  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	  <?php echo $mensaje_productos; ?>
	</div>
	<?php
	}
	?>

	<?php 
	if(isset($_GET['opp'])){
	?>
		<iframe name="editar" frameborder="1" src="" style="height:80;width:200; position:fixed;top:0px; left:1900px;"></iframe>

			<div class="col-lg-12">
				<p class="alert alert-warning">Traducir productos</p>
				<div id="contenedor_tabla_productos">
					<table class="table table-bordered table-condensed" id="tabla_productos" style="font-size:11px;">
					  <thead>
					  	<tr>
					  		<th>Nº</th>
					  		<th>Nombre del Producto</th>
					  		<th>Traducción</th>
					  	</tr>
					  </thead>
					  <?php
					  $contador = 1;
					  while($producto = mysql_fetch_assoc($row_productos)){
					  ?>
						  <form action="ejecucion.php" method="POST" target="editar">
							  <tr class="text-center">
							  	<td><?php echo $contador; ?></td>
							    <td class="text-left">
							    	<?php echo $producto['producto']; ?>
							    	<input type="hidden" name="producto" value="<?php echo $producto['producto']; ?>">
							    </td>
							    <td>
							    	<?php 
							    	if(isset($producto['producto_ingles'])){
							    		echo '<input type="text" name="producto_ingles" style="width:100%;" value="'.$producto['producto_ingles'].'" placeholder="Nombre del Producto en Ingles" onchange="this.form.submit()">';
							    	}else{
							    		echo '<input type="text" name="producto_ingles" style="width:100%;" placeholder="Nombre del Producto en Ingles" onchange="this.form.submit()">';
							    	}
							    	 ?>
							    	<input type="hidden" name="guardar_traduccion" value="1">
							    </td>
							  </tr>
						  </form>
					  <?php
					  $contador++;
					  }
					   ?>
					</table>
				</div> 	
			</div>
		</div>
	<?php
	}
	if (isset($_GET['empresa'])) {
	?>
		<iframe name="editar" frameborder="1" src="" style="height:80;width:200; position:fixed;top:0px; left:1900px;"></iframe>

			<div class="col-lg-12">
				<p class="alert alert-warning">Traducir productos</p>
				<div id="contenedor_tabla_productos">
					<table class="table table-bordered table-condensed" id="tabla_productos" style="font-size:11px;">
					  <thead>
					  	<tr>
					  		<th>Nº</th>
					  		<th>Nombre del Producto</th>
					  		<th>Traducción</th>
					  	</tr>
					  </thead>
					  <?php
					  $contador = 1;
					  while($producto = mysql_fetch_assoc($row_productos)){
					  ?>
						  <form action="ejecucion.php" method="POST" target="editar">
							  <tr class="text-center">
							  	<td><?php echo $contador; ?></td>
							    <td class="text-left">
							    	<?php echo $producto['producto']; ?>
							    	<input type="hidden" name="producto" value="<?php echo $producto['producto']; ?>">
							    </td>
							    <td>
							    	<?php 
							    	if(isset($producto['producto_ingles'])){
							    		echo '<input type="text" name="producto_ingles" style="width:100%;" value="'.$producto['producto_ingles'].'" placeholder="Nombre del Producto en Ingles" onchange="this.form.submit()">';
							    	}else{
							    		echo '<input type="text" name="producto_ingles" style="width:100%;" placeholder="Nombre del Producto en Ingles" onchange="this.form.submit()">';
							    	}
							    	 ?>
							    	<input type="hidden" name="guardar_traduccion" value="1">
							    </td>
							  </tr>
						  </form>
					  <?php
					  $contador++;
					  }
					   ?>
					</table>
				</div> 	
			</div>
		</div>
	
	<?php
	}
	 ?>

  </body>
</html>