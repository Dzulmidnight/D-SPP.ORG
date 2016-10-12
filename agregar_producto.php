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


	if(isset($_POST['agregar_producto']) && $_POST['agregar_producto'] == 1){
		if(!empty($_GET['idopp'])){
			$idopp = $_POST['idopp'];

			if(isset($_POST['nombre_producto'])){
				$nombre_producto = $_POST['nombre_producto'];
			}else{
				$nombre_producto = NULL;
			}

			for($i=0;$i<count($nombre_producto);$i++){
				if($nombre_producto[$i] != NULL){

						$str = iconv($charset, 'ASCII//TRANSLIT', $nombre_producto[$i]);
						$nombre_producto[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));

					#for($i=0;$i<count($certificacion);$i++){
					$insertSQL = sprintf("INSERT INTO productos(idopp, producto) VALUES (%s, %s)",
						GetSQLValueString($idopp, "int"),
						GetSQLValueString($nombre_producto[$i], "text"));
					$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
				}
			}
		}
		if(!empty($_GET['idempresa'])){
			$idempresa = $_POST['idempresa'];

			if(isset($_POST['nombre_producto'])){
				$nombre_producto = $_POST['nombre_producto'];
			}else{
				$nombre_producto = NULL;
			}

			for($i=0;$i<count($nombre_producto);$i++){
				if($nombre_producto[$i] != NULL){

						$str = iconv($charset, 'ASCII//TRANSLIT', $nombre_producto[$i]);
						$nombre_producto[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));

					#for($i=0;$i<count($certificacion);$i++){
					$insertSQL = sprintf("INSERT INTO productos(idempresa, producto) VALUES (%s, %s)",
						GetSQLValueString($idempresa, "int"),
						GetSQLValueString($nombre_producto[$i], "text"));
					$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
				}
			}
		}
		$mensaje_productos = 'SE HAN AGREGADO LOS PRODUCTOS';
	/*************************** INICIA INSERTAR CERTIFICACIONES ***************************/
	}
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

		<div class="col-lg-12">
			<p class="alert alert-warning">Agregar Producto(s) a la OPP</p>
			<div id="contenedor_tabla_productos">
				<form action="" method="POST">
					<table class="table table-bordered" id="tabla_productos">
					  <tr>
					    <td>Nombre del Producto</td>
					    <td style="border:hidden;">
					      <button type="button" onclick="tabla_productos()" class="btn btn-xs btn-primary" aria-label="Left Align" data-toggle="tooltip" title="Agregar otro Campo">
					        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
					      </button>
					      
					    </td>
					  </tr>
					  <tr class="text-center">
					    <td><input type="text" class="form-control" name="nombre_producto[0]" id="exampleInputEmail1" placeholder="Nombre del Producto"></td>
					  </tr>
					</table> 
					<input type="hidden" name="idopp" value="<?php echo $_GET['idopp']; ?>">
					<input type="hidden" name="idempresa" value="<?php echo $_GET['idempresa']; ?>">
					<button type="submit" style="width:100%" class="btn btn-success" name="agregar_producto" value="1">Agregar Producto(s)</button>
				</form> 
			</div> 	
		</div>
	</div>

	<script>
	var contador=0;

	  function tabla_productos()
	  {
	    contador++;
	  var table = document.getElementById("tabla_productos");
	    {
	    var row = table.insertRow(2);
	    var cell1 = row.insertCell(0);

	    cell1.innerHTML = '<input type="text" class="form-control" name="nombre_producto['+contador+']" id="exampleInputEmail1" placeholder="Nombre del Producto">';

	    }
	  } 


	</script>


    <script>
      $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    })
    </script>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>

  </body>
</html>