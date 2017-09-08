<?php require_once('Connections/dspp.php');
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


	if(isset($_POST['actualizar']) && $_POST['actualizar'] == 1){
		$contador = 1;
    $name = $_POST['name'];
    $nom = $_POST['nom'];
    $iso2 = $_POST['iso2'];
    $iso3 = $_POST['iso3'];
    $codigo_telefono = '+'.$_POST['codigo_telefono'];
    $idpais = $_POST['idpais'];
    //$updateSQL = "UPDATE paises SET name = '$name', nom = '$nom', iso2 = '$iso2', iso3 = '$iso3', codigo_telefono = '$codigo_telefono' WHERE id = $idpais";
		//$actualizar = mysql_query($updateSQL) or die(mysql_error());
    $updateSQL = sprintf("UPDATE paises SET name = %s, nom = %s, iso2 = %s, iso3 = %s, codigo_telefono = %s WHERE id = %s",
      GetSQLValueString($name, "text"),
      GetSQLValueString($nom, "text"),
      GetSQLValueString($iso2, "text"),
      GetSQLValueString($iso3, "text"),
      GetSQLValueString($codigo_telefono, "text"),
      GetSQLValueString($idpais, "int"));
    $actualizar = mysql_query($updateSQL) or die(mysql_error());

	}



  /*$insertSQL = sprintf("INSERT INTO opp (idf, password, nombre, abreviacion, sitio_web, email, pais, idoc, razon_social, direccion_fiscal, rfc, fecha_inclusion) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($idfopp, "text"),
                       GetSQLValueString($psswd, "text"),
                       GetSQLValueString($_POST['nombre'], "text"),
                       GetSQLValueString($_POST['abreviacion'], "text"),
                       GetSQLValueString($_POST['sitio_web'], "text"),
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($_POST['pais'], "text"),
                       GetSQLValueString($oc['idoc'], "int"),
                       GetSQLValueString($_POST['razon_social'], "text"),
                       GetSQLValueString($_POST['direccion_fiscal'], "text"),
                       GetSQLValueString($_POST['rfc'], "text"),
                       GetSQLValueString($_POST['fecha_inclusion'], "int"));


  $Result1 = mysql_query($insertSQL, $dspp) or die(mysql_error());*/

 ?>


<!DOCTYPE html>
<html lang="es">
  <head>
<script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
  <script>tinymce.init({ selector:'textarea' });</script>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>D-SPP.ORG</title>

    <!-- Bootstrap core CSS -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="bootstrap/css/bootstrap-theme.css" rel="stylesheet">
    <link href="bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="bootstrap/css/bootstrap-theme.min.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <!--<script src="../js/fileinput.min.js" type="text/javascript"></script>
    <script src="../js/fileinput_locale_es.js"></script>-->


     <!---LIBRERIAS DE Bootstrap File Input-->

    <script type="text/javascript" src="js/bootstrap-filestyle.js"></script>
    <link rel="stylesheet" href="chosen/chosen.css">


    <!------------------- bootstrap-switch -------------->

      <link href="bootstrap-switch-master/bootstrap-switch.css" rel="stylesheet">
      <script src="bootstrap-switch-master/bootstrap-switch.js"></script>

    <!------------------- bootstrap-switch -------------->    

  <style>
  .chosen-container-multi .chosen-choices li.search-field input[type="text"]{padding: 15px;}
  </style>
 
  </head>

  <body>
	<div class="col-md-12">

			<table class="table table-bordered table-condensed">
				<tr>
					<td colspan="3">PAISES</td>
				</tr>
				<tr>
					<td>#</td>
					<td>iso</td>
					<td>Nombre</td>
					<td>Name</td>
					<td>Nom</td>
					<td>iso2</td>
					<td>iso3</td>
					<td>codigo_telefono</td>
				</tr>
				<?php 
				$query = "SELECT * FROM paises";
				$consultar = mysql_query($query) or die(mysql_error());
				$contador = 1;

				while($datos = mysql_fetch_assoc($consultar)){
				?>
          <form action="" method="POST">
            <tr>
              <td><?php echo $contador; ?></td>
              <td><?php echo $datos['iso']; ?></td>
              <td><input type="text" name="nombre" value="<?php echo utf8_encode($datos['nombre']); ?>"></td>
              <td><input type="text" name="name" value="<?php echo utf8_encode($datos['name']); ?>"></td>
              <td><input type="text" name="nom" value="<?php echo utf8_encode($datos['nom']); ?>"></td>
              <td><input type="text" name="iso2" value="<?php echo $datos['iso2']; ?>"></td>
              <td><input type="text" name="iso3" value="<?php echo $datos['iso3']; ?>"></td>
              <td><input type="text" name="codigo_telefono" value="<?php echo $datos['codigo_telefono']; ?>"></td>
              <td><button type="submit" name="actualizar" value="1">Actualizar</button></td>
              <input type="hidden" name="idpais" value="<?php echo $datos['id']; ?>">
            </tr>
          </form>
				<?php
				$contador++;
				}
				 ?>


			</table>			

	</div>





  </body>
</html>




  <script src="chosen/chosen.jquery.js" type="text/javascript"></script>
  <script type="text/javascript">
    var config = {
      '.chosen-select'           : {},
      '.chosen-select-deselect'  : {allow_single_deselect:true},
      '.chosen-select-no-single' : {disable_search_threshold:10},
      '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
      '.chosen-select-width'     : {width:"95%"}
    }
    for (var selector in config) {
      $(selector).chosen(config[selector]);
    }
  </script>