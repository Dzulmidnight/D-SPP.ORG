<?php 
require_once('../Connections/dspp.php'); 
require_once('../Connections/mail.php');

mysql_select_db($database_dspp, $dspp);

if (!isset($_SESSION)) {
  session_start();
	
	$redireccion = "../index.php?OPP";

	if(!$_SESSION["autentificado"]){
		header("Location:".$redireccion);
	}
}

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

$idsolicitud_colectiva = $_GET['idsolicitud_colectiva'];
$charset='utf-8'; 


if(isset($_POST['actualizar_solicitud']) && $_POST['actualizar_solicitud'] == 1){
$ruta_croquis = "../../archivos/oppArchivos/croquis/";
	/*
	SE ACTUALIZA LA SOLICITUD
	LA INFORMACION DE OPP
	NUMERO DE SOCIOS
	CONTACTOS
	PRODUCTOS
	CERTIFICACIONES
	*/

  if(isset($_POST['op_preg12'])){
    $op_preg12 = $_POST['op_preg12'];
  }else{
    $op_preg12 = "";
  }

  if(isset($_POST['op_preg13'])){
    $op_preg13 = $_POST['op_preg13'];
  }else{
    $op_preg13 = "";
  }


  /*if(!empty($_FILES['op_preg15']['name'])){
      $_FILES["op_preg15"]["name"];
        move_uploaded_file($_FILES["op_preg15"]["tmp_name"], $ruta_croquis.date("Ymd H:i:s")."_".$_FILES["op_preg15"]["name"]);
        $croquis = $ruta_croquis.basename(date("Ymd H:i:s")."_".$_FILES["op_preg15"]["name"]);
  }else{
    $croquis = NULL;
  }*/
  if(!empty($_POST['produccion'])){
  	$produccion = $_POST['produccion'];
  }else{
  	$produccion = 0;
  }
  if(!empty($_POST['procesamiento'])){
  	$procesamiento = $_POST['procesamiento'];
  }else{
  	$procesamiento = 0;
  }
  if(!empty($_POST['comercializacion'])){
  	$comercializacion = $_POST['comercializacion'];
  }else{
  	$comercializacion = 0;
  }

	if(!empty($_FILES['op_preg15']['name'])){
	    $_FILES["op_preg15"]["name"];
	      move_uploaded_file($_FILES["op_preg15"]["tmp_name"], $ruta_croquis.date("Ymd H:i:s")."_".$_FILES["op_preg15"]["name"]);
	      $croquis = $ruta_croquis.basename(date("Ymd H:i:s")."_".$_FILES["op_preg15"]["name"]);
	}else{
		$croquis = NULL;
	}

  // ACTUALIZAMOS LA INFORMACION DE LA SOLICITUD
	$updateSQL = sprintf("UPDATE solicitud_colectiva SET contacto1_nombre = %s, contacto2_nombre = %s, contacto1_cargo = %s, contacto2_cargo = %s, contacto1_email = %s, contacto2_email = %s, contacto1_telefono = %s, contacto2_telefono = %s, adm1_nombre = %s, adm2_nombre = %s, adm1_email = %s, adm2_email = %s, adm1_telefono = %s, adm2_telefono = %s, total_miembros = %s, produccion = %s, procesamiento = %s, comercializacion = %s, preg2 = %s, preg3 = %s, preg4 = %s, preg5 = %s, preg6 = %s, preg8 = %s, preg10 = %s WHERE idsolicitud_colectiva = %s",
	       GetSQLValueString($_POST['contacto1_nombre'], "text"),
	       GetSQLValueString($_POST['contacto2_nombre'], "text"),
	       GetSQLValueString($_POST['contacto1_cargo'], "text"),
	       GetSQLValueString($_POST['contacto2_cargo'], "text"),
	       GetSQLValueString($_POST['contacto1_email'], "text"),
	       GetSQLValueString($_POST['contacto2_email'], "text"),
	       GetSQLValueString($_POST['contacto1_telefono'], "text"),
	       GetSQLValueString($_POST['contacto2_telefono'], "text"),
	       GetSQLValueString($_POST['adm1_nombre'], "text"),
	       GetSQLValueString($_POST['adm2_nombre'], "text"),
	       GetSQLValueString($_POST['adm1_email'], "text"),
	       GetSQLValueString($_POST['adm2_email'], "text"),
	       GetSQLValueString($_POST['adm1_telefono'], "text"),
	       GetSQLValueString($_POST['adm2_telefono'], "text"),
	       GetSQLValueString($_POST['total_miembros'], "text"),

	       GetSQLValueString($produccion, "int"),
	       GetSQLValueString($procesamiento, "int"),
	       GetSQLValueString($comercializacion, "int"),
	       GetSQLValueString($_POST['preg2'], "text"),
	       GetSQLValueString($_POST['preg3'], "text"),
	       GetSQLValueString($_POST['preg4'], "text"),
	       GetSQLValueString($_POST['preg5'], "text"),
	       GetSQLValueString($_POST['preg6'], "text"),
	       GetSQLValueString($_POST['preg8'], "text"),
	       //GetSQLValueString($_POST['preg9'], "text"),
	       GetSQLValueString($_POST['preg10'], "text"),
	       //GetSQLValueString($_POST['preg11'], "text"),
	       GetSQLValueString($idsolicitud_colectiva, "int"));
	$actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());



  // ACTUALIZAMOS LA INFORMACION DE LA OPP
	$updateSQL = sprintf("UPDATE opp SET nombre = %s, pais = %s, direccion_oficina = %s, email = %s, telefono = %s, sitio_web = %s, razon_social = %s, direccion_fiscal = %s, rfc = %s, ruc = %s, produccion = %s, procesamiento = %s, comercializacion = %s WHERE idopp = %s",
		GetSQLValueString($_POST['nombre_facilitador'], "text"),
		GetSQLValueString($_POST['pais'], "text"),
		GetSQLValueString($_POST['direccion_oficina'], "text"),
		GetSQLValueString($_POST['email'], "text"),
		GetSQLValueString($_POST['telefono'], "text"),
		GetSQLValueString($_POST['sitio_web'], "text"),
		GetSQLValueString($_POST['razon_social'], "text"),
		GetSQLValueString($_POST['direccion_fiscal'], "text"),
		GetSQLValueString($_POST['rfc'], "text"),
		GetSQLValueString($_POST['ruc'], "text"),
		GetSQLValueString($produccion, "int"),
		GetSQLValueString($procesamiento, "int"),
		GetSQLValueString($comercializacion, "int"),
		GetSQLValueString($_POST['idopp'], "int"));
	$actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

  //ACTUALIZAMOS EL NUMERO DE SOCIOS
	/*$updateSQL = sprintf("UPDATE num_socios SET numero = %s WHERE fecha_registro = %s AND idopp = %s",
	  GetSQLValueString($_POST['resp1'], "int"),
	  GetSQLValueString($_POST['fecha_registro'], "int"),
	  GetSQLValueString($_POST['idopp'], "int"));
	$actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());*/

	////ACTUALIZAMOS LOS PORCENTAJES DE VENTAS
  if(!empty($_POST['organico']) || !empty($_POST['comercio_justo']) || !empty($_POST['spp']) || !empty($_POST['sin_certificado'])){
  	$row_ventas = mysql_query("SELECT * FROM porcentaje_productoVentas WHERE idsolicitud_colectiva = $idsolicitud_colectiva", $dspp) or die(mysql_error());
  	$existe_venta = mysql_num_rows($row_ventas);
  	if($existe_venta){
	  	$updateSQL = sprintf("UPDATE porcentaje_productoVentas SET organico = %s, comercio_justo = %s, spp = %s, sin_certificado = %s WHERE idsolicitud_colectiva = %s",
	  		GetSQLValueString($_POST['organico'], "text"),
	  		GetSQLValueString($_POST['comercio_justo'], "text"),
	  		GetSQLValueString($_POST['spp'], "text"),
	  		GetSQLValueString($_POST['sin_certificado'], "text"),
	  		GetSQLValueString($idsolicitud_colectiva, "int"));
	  	$actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());
  	}else{
  		$insertSQL = sprintf("INSERT INTO porcentaje_productoVentas (organico, comercio_justo, spp, sin_certificado, idsolicitud_colectiva, idopp) VALUES (%s, %s, %s, %s, %s, %s)",
	  		GetSQLValueString($_POST['organico'], "text"),
	  		GetSQLValueString($_POST['comercio_justo'], "text"),
	  		GetSQLValueString($_POST['spp'], "text"),
	  		GetSQLValueString($_POST['sin_certificado'], "text"),
	  		GetSQLValueString($idsolicitud_colectiva, "int"),
  			GetSQLValueString($_POST['idopp'], "int"));
  		$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
  	}

  }
 

		/*************************** INICIA ACTUALIZAR SUB ORGANIZACIONES ***************************/

		    // SE ACTUALIZAN LAS CERTIFICACIONES

    // SE ACTUALIZAN LOS PRODUCTOS
  	if(isset($_POST['sub_nombre'])){
  		$sub_nombre = $_POST['sub_nombre'];
  	}else{
  		$sub_nombre = '';
  	}
  	if(isset($_POST['sub_producto'])){
  		$sub_producto = $_POST['sub_producto'];
  	}else{
  		$sub_producto = '';
  	}
  	if(isset($_POST['num_productores'])){
  		$num_productores = $_POST['num_productores'];
  	}else{
  		$num_productores = '';
  	}
  	if(isset($_POST['unidad_produccion'])){
  		$unidad_produccion = $_POST['unidad_produccion'];
  	}else{
  		$unidad_produccion = '';
  	}
  	if(isset($_POST['sub_incumplimientos'])){
  		$sub_incumplimientos = $_POST['sub_incumplimientos'];
  	}else{
  		$sub_incumplimientos = '';
  	}
  	if(isset($_POST['sub_certificaciones'])){
  		$sub_certificaciones = $_POST['sub_certificaciones'];
  	}else{
  		$sub_certificaciones = '';
  	}
  	if(isset($_POST['sub_certificadora'])){
  		$sub_certificadora = $_POST['sub_certificadora'];
  	}else{
  		$sub_certificadora = '';
  	}


  	if(isset($_POST['sub_anio_certificacion'])){
  		$sub_anio_certificacion = $_POST['sub_anio_certificacion'];
  	}else{
  		$sub_anio_certificacion = '';
  	}
  	if(isset($_POST['sub_interrumpido'])){
  		$sub_interrumpido = $_POST['sub_interrumpido'];
  	}else{
  		$sub_interrumpido = '';
  	}


  	if(isset($_POST['idsub_organizacion'])){
  		$idsub_organizacion = $_POST['idsub_organizacion'];
  	}else{
  		$idsub_organizacion = '';
  	}

  	if(isset($_POST['idsub_organizacion'])){
	    for ($i=0;$i<count($sub_nombre);$i++) { 
	      if($sub_nombre[$i] != NULL){

	      $array9 = "sub_interrumpido".$i; 


	      if(isset($_POST[$array9])){
	      	$sub_interrumpido = $_POST[$array9];
	      }else{
	      	$sub_interrumpido = '';
	      }


						//$str = iconv($charset, 'ASCII//TRANSLIT', $producto_actual[$i]);
						//$producto_actual[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));

						/*$str = iconv($charset, 'ASCII//TRANSLIT', $destino_actual[$i]);
						$destino_actual[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));

						$str = iconv($charset, 'ASCII//TRANSLIT', $materia_actual[$i]);
						$materia_actual[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));*/


	      $updateSQL = sprintf("UPDATE sub_organizaciones SET nombre = %s, productos = %s, num_productores = %s, unidad_produccion = %s, incumplimientos = %s, certificaciones = %s, certificadora = %s, anio_inicial = %s, interrumpida = %s WHERE idsub_organizacion = %s",
	      	GetSQLValueString($sub_nombre[$i], "text"),
	      	GetSQLValueString($sub_producto[$i], "text"),
	      	GetSQLValueString($num_productores[$i], "text"),
	      	GetSQLValueString($unidad_produccion[$i], "text"),
	      	GetSQLValueString($sub_incumplimientos[$i], "text"),
	      	GetSQLValueString($sub_certificaciones[$i], "text"),
	      	GetSQLValueString($sub_certificadora[$i], "text"),
	      	GetSQLValueString($sub_anio_certificacion[$i], "text"),
	      	GetSQLValueString($sub_interrumpido, "text"),
	      	GetSQLValueString($idsub_organizacion[$i], "int"));
	      $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());


	      }
	    }
  	}
		/*************************** TERMINA ACTUALIZAR SUB ORGANIZACIONES ***************************/




	/*************************** INICIA INSERTAR PRODUCTOS ***************************/

	if(isset($_POST['volumen'])){
		$volumen = $_POST['volumen'];
	}
	if(isset($_POST['materia'])){
		$materia = $_POST['materia'];
	}
	if(isset($_POST['destino'])){
		$destino = $_POST['destino'];
	}

	if(isset($_POST['producto'])){
		$producto = $_POST['producto'];


		for ($i=0;$i<count($producto);$i++) { 
			if($producto[$i] != NULL){

					$array1[$i] = "terminado".$i; 
					$array2[$i] = "marca_propia".$i;
					$array3[$i] = "marca_cliente".$i;
					$array4[$i] = "sin_cliente".$i;

					if(isset($_POST[$array1[$i]])){
						$terminado = $_POST[$array1[$i]];
					}else{
						$terminado = null;
					}
					if(isset($_POST[$array2[$i]])){
						$marca_propia = $_POST[$array2[$i]];
					}else{
						$marca_propia = null;
					}
					if(isset($_POST[$array3[$i]])){
						$marca_cliente = $_POST[$array3[$i]];
					}else{
						$marca_cliente = null;
					}
					if(isset($_POST[$array4[$i]])){
						$sin_cliente = $_POST[$array4[$i]];
					}else{
						$sin_cliente = null;
					}

					//$terminado = $_POST[$array1[$i]];
					//$marca_propia = $_POST[$array2[$i]];
					//$marca_cliente = $_POST[$array3[$i]];
					//$sin_cliente = $_POST[$array4[$i]];

					//$str = iconv($charset, 'ASCII//TRANSLIT', $producto[$i]);
					//$producto[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));
					

					$str = iconv($charset, 'ASCII//TRANSLIT', $destino[$i]);
					$destino[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));

					$str = iconv($charset, 'ASCII//TRANSLIT', $materia[$i]);
					$materia[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));


				    $insertSQL = sprintf("INSERT INTO productos (idopp, idsolicitud_colectiva, producto, volumen, terminado, materia, destino, marca_propia, marca_cliente, sin_cliente) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
				    	GetSQLValueString($_POST['idopp'], "int"),
				          GetSQLValueString($idsolicitud_colectiva, "int"),
				          GetSQLValueString($producto[$i], "text"),
				          GetSQLValueString($volumen[$i], "text"),
				          GetSQLValueString($terminado[$i], "text"),
				          GetSQLValueString($materia[$i], "text"),
				          GetSQLValueString($destino[$i], "text"),
				          GetSQLValueString($marca_propia[$i], "text"),
				          GetSQLValueString($marca_cliente[$i], "text"),                    
				          GetSQLValueString($sin_cliente[$i], "text"));

				  $Result = mysql_query($insertSQL, $dspp) or die(mysql_error());
			}
		}
		/***************************** TERMINA INSERTAR PRODUCTOS ******************************/

	}

	/*$marca_propia = $_POST['marca_propia'];
	$marca_cliente = $_POST['marca_cliente'];
	$sin_cliente = $_POST['sin_cliente'];*/





    // SE ACTUALIZAN LOS PRODUCTOS
  	if(isset($_POST['producto_actual'])){
  		$producto_actual = $_POST['producto_actual'];
  	}else{
  		$producto_actual = '';
  	}
  	if(isset($_POST['volumen_actual'])){
  		$volumen_actual = $_POST['volumen_actual'];
  	}else{
  		$volumen_actual = '';
  	}
  	if(isset($_POST['materia_actual'])){
  		$materia_actual = $_POST['materia_actual'];
  	}else{
  		$materia_actual = '';
  	}
  	if(isset($_POST['destino_actual'])){
  		$destino_actual = $_POST['destino_actual'];
  	}else{
  		$destino_actual = '';
  	}

  	if(isset($_POST['idproducto'])){
  		$idproducto = $_POST['idproducto'];
  	}else{
  		$idproducto = '';
  	}

  	if(isset($_POST['idproducto'])){
	    for ($i=0;$i<count($producto_actual);$i++) { 
	      if($producto_actual[$i] != NULL){

	      $array1 = "terminado_actual".$i; 
	      $array2 = "marca_propia_actual".$i;
	      $array3 = "marca_cliente_actual".$i;
	      $array4 = "sin_cliente_actual".$i;


	      if(isset($_POST[$array1])){
	      	$terminado = $_POST[$array1];
	      }else{
	      	$terminado = '';
	      }
	      if(isset($_POST[$array2])){
	      	$marca_propia = $_POST[$array2];
	      }else{
	      	$marca_propia = '';
	      }
	      if(isset($_POST[$array3])){
	      	$marca_cliente = $_POST[$array3];
	      }else{
	      	$marca_cliente = '';
	      }
	      if(isset($_POST[$array4])){
	      	$sin_cliente = $_POST[$array4];
	      }else{
	      	$sin_cliente = '';
	      }

						//$str = iconv($charset, 'ASCII//TRANSLIT', $producto_actual[$i]);
						//$producto_actual[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));

						$str = iconv($charset, 'ASCII//TRANSLIT', $destino_actual[$i]);
						$destino_actual[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));

						$str = iconv($charset, 'ASCII//TRANSLIT', $materia_actual[$i]);
						$materia_actual[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));


	      $updateSQL = sprintf("UPDATE productos SET producto = %s, volumen = %s, terminado = %s, materia = %s, destino = %s, marca_propia = %s, marca_cliente = %s, sin_cliente = %s WHERE idproducto = %s",
	      	GetSQLValueString($producto_actual[$i], "text"),
	      	GetSQLValueString($volumen_actual[$i], "text"),
	      	GetSQLValueString($terminado, "text"),
	      	GetSQLValueString($materia_actual[$i], "text"),
	      	GetSQLValueString($destino_actual[$i], "text"),
	      	GetSQLValueString($marca_propia, "text"),
	      	GetSQLValueString($marca_cliente, "text"),
	      	GetSQLValueString($sin_cliente, "text"),
	      	GetSQLValueString($idproducto[$i], "int"));
	      $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());


	      }
	    }
  	}




  $mensaje = "Datos Actualizados Correctamente";
}
 

$query = "SELECT solicitud_colectiva.*, opp.idopp AS 'id_opp', opp.nombre, opp.spp AS 'spp_opp', opp.sitio_web, opp.email, opp.telefono, opp.pais, opp.ciudad, opp.razon_social, opp.direccion_oficina, opp.direccion_fiscal, opp.rfc, opp.ruc, oc.abreviacion AS 'abreviacionOC', porcentaje_productoVentas.* FROM solicitud_colectiva INNER JOIN opp ON solicitud_colectiva.idopp = opp.idopp INNER JOIN oc ON solicitud_colectiva.idoc = oc.idoc LEFT JOIN porcentaje_productoVentas ON solicitud_colectiva.idsolicitud_colectiva = porcentaje_productoVentas.idsolicitud_colectiva WHERE solicitud_colectiva.idsolicitud_colectiva = $idsolicitud_colectiva";
$ejecutar = mysql_query($query,$dspp) or die(mysql_error());
$solicitud = mysql_fetch_assoc($ejecutar);

$row_pais = mysql_query("SELECT * FROM paises", $dspp) or die(mysql_error());
?>
<div class="row" style="font-size:12px;">

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

	<form action="" name="" method="POST" enctype="multipart/form-data">
		<fieldset>
			<div class="col-md-12 alert alert-primary" style="padding:7px;">
				<h3 class="text-center">Solicitud de Certificación Colectiva OPP</h3>
			</div>
			<div class="col-md-12 well text-justify">
				<p class="text-center" style="color:red"><b>Información importante sobre las directrices para la certificación colectiva:</b></p>
				<p><b>Alcance:</b></p>
				<p>
					1. Los Procedimientos de Certificación Colectiva se aplican a las Organizaciones de primer nivel que son miembros de una Organización de Pequeños Productores de nivel superior que solicita la Certificación, basada en la Norma General para el Símbolo de Pequeños Productores, a través de la Organización de Pequeños Productores de un nivel superior . 
				</p>  

				<p>
					2. La Organización de Pequeños Productores de Alto Nivel no adquiere la certificación. Si la Organización de Pequeños Productores de Alto Nivel es la organización que está comercializando productos bajo el Símbolo de Pequeños Productores, debería registrarse como Intermediaria (INT) o Compañía de Comercio Colectivo propiedad de Organizaciones de Pequeños Productores (C-OPP).
				</p>      

				<p>
					<b>Requisitos:</b><br>
					i.	La Organización de Pequeños Productores (OPP) de alto nivel debe trabajar para facilitar y promover el proceso de certificación para sus miembros y debe proporcionar toda la información necesaria basada en su sistema de control interno. 
				</p>
				<p>
					ii.	La OPP de alto nivel debe completar el Formulario de Evaluación del SPP como una forma de autoevaluación en línea con la información de cada una de las OPPs de primer nivel implicadas.
				</p>
				<p>
					iii. La OPP de alto nivel debe enviar la documentación especificada en el Formulario de Evaluación como documentación de apoyo, así como la información solicitada por la Entidad Certificadora (CE). 
				</p> 
			</div>


			<div class="col-md-12 text-center alert alert-success" style="padding:7px;"><b>DATOS GENERALES</b></div>

			<div class="col-lg-12 alert alert-info" style="padding:7px;">
				<div class="col-md-4">
					<div class="col-xs-12">
						<b>ENVAR AL OC (selecciona el OC al que deseas enviar la solicitud):</b>
					</div>
					<div class="col-xs-12">
						<input type="text" class="form-control" value="<?php echo $solicitud['abreviacionOC']; ?>" readonly>
					</div>
				</div>
				<div class="col-md-4">
					<div class="col-xs-12">
						<b>TIPO DE SOLICITUD</b>
					</div>
					<div class="col-xs-6">
						<input type="text" class="form-control" value="<?php echo $solicitud['tipo_solicitud']; ?>"readonly>
					</div>
					
				</div>
				<div class="col-md-4">

					<input type="hidden" name="actualizar_solicitud" value="1">
					<button style="color:white" type="submit" class="btn btn-warning form-control"><span class="glyphicon glyphicon-refresh" aria-hidden="true"></span> ACTUALIZAR SOLICITUD</button>
				</div>

			</div>

			<!------ INICIA INFORMACION GENERAL Y DATOS FISCALES ------>
			<div class="col-md-12 text-center alert alert-success" style="padding:7px;"><b>ORGANIZACIÓN FACILITADORA( <a data-toggle="tooltip" title="Organización de Pequeños Productores de segundo o más alto nivel que representa a sus organizaciones miembros en su proceso de certificación colectiva" href="#"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span></a> ) INFORMACIÓN GENERAL ( <a data-toggle="tooltip" title="Los datos generales de la Organización de Pequeños Productores solicitante serán publicados por SPP Global." href="#"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span></a> )</b></div>
			<!------ INICIA INFORMACION GENERAL Y DATOS FISCALES ------>
			<div class="col-lg-12">
				<div class="col-md-6">
					<div class="col-md-12 text-center alert alert-warning" style="padding:7px;">INFORMACIÓN GENERAL</div>
					<label for="fecha_elaboracion">FECHA DE ELABORACIÓN</label>
					<input type="text" class="form-control" id="fecha_elaboracion" name="fecha_elaboracion" value="<?php echo date('Y-m-d', time()); ?>" readonly>	

					<label for="spp">CODIGO DE IDENTIFICACIÓN SPP(#SPP): </label>
					<input type="text" class="form-control" id="spp" name="spp" value="<?php echo $solicitud['spp_opp']; ?>" readonly>

					<label for="nombre_facilitador" style="color:red">NOMBRE COMPLETO DE LA ORGANIZACIÓN FACILITADORA DE LA QUE FORMAN PARTE LAS ORGANIZACIONES DE BASE A INCLUIR EN LA CERTIFICACIÓN COLECTIVA:</label>
					<textarea name="nombre_facilitador" id="nombre_facilitador" class="form-control"><?php echo $solicitud['nombre']; ?></textarea>


					<label for="pais">PAÍS:</label>
					<?php 
					$row_pais = mysql_query("SELECT * FROM paises",$dspp) or die(mysql_error());
					 ?>
					 <select name="pais" id="pais" class="form-control">
					 	<option value="">Selecciona un pais</option>
					 	<?php 
					 	while($pais = mysql_fetch_assoc($row_pais)){
					 		if(utf8_encode($pais['nombre']) == $solicitud['pais']){
					 			echo "<option value='".utf8_encode($pais['nombre'])."' selected>".utf8_encode($pais['nombre'])."</option>";
					 		}else{
					 			echo "<option value='".utf8_encode($pais['nombre'])."'>".utf8_encode($pais['nombre'])."</option>";
					 		}
					 	}
					 	 ?>
					 </select>
					 <input type="hidden" name="pais_facilitador" value="<?php echo $solicitud['pais']; ?>">

					<label for="direccion_oficina">DIRECCIÓN COMPLETA DE SUS OFICINAS CENTRALES DE LA ORGANIZACIÓN FACILITADORA (CALLE, BARRIO, LUGAR, REGIÓN):</label>
					<textarea name="direccion_oficina" id="direccion_oficina"  class="form-control"><?php echo $solicitud['direccion_oficina']; ?></textarea>

					<label for="email">CORREO ELECTRÓNICO:</label>
					<input type="text" class="form-control" id="email" name="email" value="<?php echo $solicitud['email']; ?>">

					<label for="email">TELÉFONOS (CÓDIGO DE PAÍS+ CÓDIGO DE ÁREA + NÚMERO):</label>
					<input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo $solicitud['telefono']; ?>">	

					<label for="sitio_web">SITIO WEB:</label>
					<input type="text" class="form-control" id="sitio_web" name="sitio_web" value="<?php echo $solicitud['sitio_web']; ?>">

				</div>

				<div class="col-md-6">
					<div class="col-md-12 text-center alert alert-warning" style="padding:7px;">INFORMACIÓN FISCAL</div>

					<label for="razon_social">NOMBRE COMERCIAL</label>
					<input type="text" class="form-control" id="razon_social" name="razon_social" value="<?php echo $solicitud['razon_social']; ?>">

					<label for="direccion_fiscal">DIRECCIÓN FISCAL</label>
					<textarea class="form-control" name="direccion_fiscal" id="direccion_fiscal"><?php echo $solicitud['direccion_fiscal']; ?></textarea>

					<label for="rfc">RFC</label>
					<input type="text" class="form-control" id="rfc" name="rfc" value="<?php echo $solicitud['rfc']; ?>">

					<label for="ruc">RUC</label>
					<input type="text" class="form-control" id="ruc" name="ruc" value="<?php echo $solicitud['ruc']; ?>">
				</div>
			</div>
			<!------ INICIA INFORMACION GENERAL Y DATOS FISCALES ------>


			<!------ INICIA INFORMACION CONTACTOS Y AREA ADMINISTRATIVA ------>
			<div class="col-lg-12">
				<div class="col-md-6">
					<div class="col-md-12 text-center alert alert-warning" style="padding:7px;">PERSONAS DE CONTACTO DE LA SOLICITUD</div>

					<label for="persona1">PERSONAS DE CONTACTO</label>
					<input type="text" class="form-control" id="persona1" name="contacto1_nombre" value="<?php echo $solicitud['contacto1_nombre']; ?>" placeholder="* Nombre persona 1" required>
					<input type="text" class="form-control" id="" name="contacto2_nombre" value="<?php echo $solicitud['contacto2_nombre']; ?>" placeholder="Name Person 2">

					<label for="cargo">CARGO(S)</label>
					<input type="text" class="form-control" id="cargo" name="contacto1_cargo" value="<?php echo $solicitud['contacto1_cargo']; ?>" placeholder="* Cargo persona 1" required>
					<input type="text" class="form-control" id="" name="contacto2_cargo" value="<?php echo $solicitud['contacto2_cargo']; ?>" placeholder="Position Person 2">

					<label for="email">CORREO ELECTRÓNICO PERSONA(S) DE CONTACTO</label>
					<input type="email" class="form-control" id="email" name="contacto1_email" value="<?php echo $solicitud['contacto1_email']; ?>" placeholder="* Email persona 1" required>
					<input type="email" class="form-control" id="" name="contacto2_email" value="<?php echo $solicitud['contacto2_email']; ?>" placeholder="Email Person 2">

					<label for="telefono">TELÉFONO PERSONA(S) DE CONTACTO:</label>
					<input type="text" class="form-control" id="telefono" name="contacto1_telefono" value="<?php echo $solicitud['contacto1_telefono']; ?>" placeholder="* Teléfono persona 1" required>
					<input type="text" class="form-control" id="" name="contacto2_telefono" value="<?php echo $solicitud['contacto2_telefono']; ?>" placeholder="Teléfono persona 2">

				</div>

				<div class="col-md-6">
					<div class="col-md-12 text-center alert alert-warning" style="padding:7px;">PERSONAL DEL ÁREA ADMINISTRATIVA</div>

					<label for="persona_adm">PERSONA(S) DEL ÁREA ADMINISTRATIVA</label>
					<input type="text" class="form-control" id="persona_adm" name="adm1_nombre" value="<?php echo $solicitud['adm1_nombre']; ?>" placeholder="Nombre persona 1">
					<input type="text" class="form-control" id="" name="adm2_nombre" value="<?php echo $solicitud['adm2_nombre']; ?>" placeholder="Nombre persona 2">

					<label for="email_adm">CORREO ELECTRÓNICO</label>
					<input type="email" class="form-control" id="email_adm" name="adm1_email" value="<?php echo $solicitud['adm1_email']; ?>" placeholder="Email persona 1">
					<input type="email" class="form-control" id="" name="adm2_email" value="<?php echo $solicitud['adm2_email']; ?>" placeholder="Email persona 2">

					<label for="telefono_adm">TELÉFONO(S) PERSONA(S) DEL ÁREA ADMINISTRATIVA:</label>
					<input type="text" class="form-control" id="telefono_adm" name="adm1_telefono" value="<?php echo $solicitud['adm1_telefono']; ?>" placeholder="Teléfono persona 1">
					<input type="text" class="form-control" id="" name="adm2_telefono" value="<?php echo $solicitud['adm2_telefono']; ?>" placeholder="Teléfono persona 2">
				</div>
			</div>
			<!------ FIN INFORMACION CONTACTOS Y AREA ADMINISTRATIVA ------>
			<!--- INICIA TABLA SOBRE INFORMACION DE LAS ORGANIZACIONES INVOLUCRADAS -->
			<div class="col-lg-12">
				<table class="table table-bordered" id="tabla_organizaciones">
					<thead>
						<tr class="success">
							<th rowspan="2" style="margin:0px;padding:0px;">
								<button type="button" onclick="tabla_organizaciones()" class="btn btn-primary" aria-label="Left Align">
								  <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
								</button>
							</th>

							<th rowspan="2">Nombre Completo</th>
							<th rowspan="2">Código de Identificación SPP Identification Code</th>
							<th rowspan="2">Número de Socios</th>
							<th rowspan="2">Productos a incluir en la certificación Colectiva</th>
							<th rowspan="2">Tamaño máximo de la unidad Producción <span style="background:yellow">por Productor</span> del producto para incluir en la certificación colectiva (unidad de medida)</th>
							<th colspan="4">Llenar la tabla de acuerdo a las certificaciones que tiene (Ejemplo: EU, NOP, JASS, FLO, etc)</th>

							<th rowspan="2">De las certificaciones con las que cuenta, en su más reciente evaluación externa, ¿cuántos incumplimientos se identificaron. ¿Están resueltos o cuál es su estado?</th>

						</tr>
						<tr>
							<th>Certificación</th>
							<th>Certificadora</th>
							<th>Año inicial</th>
							<th>¿Ha sido Interrumpida?</th>
						</tr>

					</thead>
					<tbody>
						<?php 
						$query_sub_organizaciones = "SELECT * FROM sub_organizaciones WHERE idsolicitud_colectiva = $idsolicitud_colectiva";
						$detalle_sub_organizacioens = mysql_query($query_sub_organizaciones, $dspp) or die(mysql_error());
						$contador = 0;
						$contador2 = 1;

						while($row_sub_organizaciones = mysql_fetch_assoc($detalle_sub_organizacioens)){
						?>
							<tr>	
								<td>
									<?php echo $contador2; ?>
								</td>
								<td>
									<input type="text" class="form-control" style="width:150px;" name="sub_nombre[]" value="<?php echo $row_sub_organizaciones['nombre']; ?>" placeholder="Nombre completo">
								</td>
								<td>
									<?php echo $row_sub_organizaciones['spp']; ?>
								</td>
								<td>
									<input type="number" class="form-control" style="width:120px;" name="num_productores[]" value="<?php echo $row_sub_organizaciones['num_productores']; ?>" placeholder="Solo numero">
								</td>
								<td>
									<textarea class="form-control" style="width:200px;" name="sub_producto[]" id="" rows="3" placeholder="Productos"><?php echo $row_sub_organizaciones['productos']; ?></textarea>
								</td>
								<td>
									<input type="text" class="form-control" style="width:150px;" name="unidad_produccion[]" value="<?php echo $row_sub_organizaciones['unidad_produccion']; ?>" placeholder="Unidad producción">
								</td>
								<td>
	                                <textarea class="form-control" style="width:150px;" name="sub_certificaciones[]" placeholder="Certificación" rows="3" required><?php echo $row_sub_organizaciones['certificaciones']; ?></textarea>
								</td>
								<td>
	                                <textarea class="form-control" style="width:150px;" name="sub_certificadora[]" placeholder="Certificadora" rows="3" required><?php echo $row_sub_organizaciones['certificadora']; ?></textarea>
								</td>
								<td>
	                                <textarea class="form-control" style="width:150px;" name="sub_anio_certificacion[]"  placeholder="Año inicial" rows="3" required><?php echo $row_sub_organizaciones['anio_inicial']; ?></textarea>
								</td>
								<td>
							      <?php 
							        if($row_sub_organizaciones['interrumpida'] == 'SI'){
							          echo "SI <input type='radio'  name='sub_interrumpido".$contador."' value='SI' checked><br>";
							        }else{
							          echo "SI <input type='radio'  name='sub_interrumpido".$contador."' value='SI'><br>";
							        }
							        if($row_sub_organizaciones['interrumpida'] == 'NO'){
							          echo "NO <input type='radio'  name='sub_interrumpido".$contador."' value='NO' checked>";
							        }else{
							          echo "NO <input type='radio'  name='sub_interrumpido".$contador."' value='NO'>";
							        }
							       ?> 
								</td>
								<td>
									<textarea class="form-control" style="width:200px;" name="sub_incumplimientos[]" id="" rows="3"><?php echo $row_sub_organizaciones['incumplimientos']; ?></textarea>
								</td>
								
							</tr>
							<input type="hidden" name="idsub_organizacion[]" value="<?echo $row_sub_organizaciones['idsub_organizacion']?>">
						<?php

						$contador++;
						$contador2++;
						}
						 ?>

						<tr>
							<td colspan="11">
								<h6>La información proporcionada en esta sección será manejada con total confidencialidad. Por favor, inserte líneas adicionales si es necesario.</h6>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<!--- TERMINA TABLA SOBRE LA INFORMACION DE LAS ORGANIZACIONES INVOLUCRADAS -->

			<!------ INICIA INFORMACION DATOS DE OPERACIÓN ------>
		
			<div class="col-lg-6" style="background:#2ecc71">
					<label for="total_miembros">TOTAL DE MIEMBROS INCLUIDOS EN LA CERTIFICACIÓN COLECTIVA:</label>
					<input type="number" class="form-control" id="total_miembros" name="total_miembros" value="<?php echo $solicitud['total_miembros']; ?>" placeholder="just number" required>
			</div>

			<div class="col-md-12 text-center alert alert-success" style="margin-top:5em;">DATOS DE OPERACIÓN</div>

			<div class="col-lg-12">
				<div class="col-md-12">

					<div >
						<label for="alcance_opp">
							1.	INDIQUE CON UNA  ‘X’ EL ALCANCE QUE TIENE LA ORGANIZACIÓN DE PEQUEÑOS PRODUCTORES:
						</label>
					</div>
					<div class="col-xs-4">
						<label>PRODUCCIÓN</label>
						<?php 
						if($solicitud['produccion'] == 1){
							echo '<input type="checkbox" name="produccion" class="form-control" value="1" checked>';
						}else{
							echo '<input type="checkbox" name="produccion" class="form-control" value="1">';
						}
						?>
					</div>
					<div class="col-xs-4">
						<label>PROCESAMIENTO</label>
						<?php 
						if($solicitud['procesamiento'] == 1){
							echo '<input type="checkbox" name="procesamiento" class="form-control" value="1" checked>';
						}else{
							echo '<input type="checkbox" name="procesamiento" class="form-control" value="1">';
						}
						?>
					</div>
					<div class="col-xs-4">
						<label>COMERCIALIZACIÓN</label>
						<?php 
						if($solicitud['comercializacion'] == 1){
							echo '<input type="checkbox" name="comercializacion" class="form-control" value="1" checked>';
						}else{
							echo '<input type="checkbox" name="comercializacion" class="form-control" value="1">';
						}
						?>

					</div>


					<label for="preg2">
						2.	ESPECIFIQUE EL NOMBRE DE LA INSTANCIA QUE LLEVA A CABO LA COMERCIALIZACIÓN, IMPORTACIÓN O EXPORTACIÓN DE LAS TRANSACCIONES SPP.
					</label>
					<input type="text" class="form-control" id="preg2" name="preg2" value="<?php echo $solicitud['preg2']; ?>">


					<label for="preg3">
						3.	ESPECIFIQUE SI SUBCONTRATA LOS SERVICIOS DE PLANTAS DE PROCESAMIENTO, EMPRESAS DE COMERCIALIZACIÓN O EMPRESAS QUE REALICEN LA IMPORTACIÓN O EXPORTACIÓN, SI LA RESPUESTA ES AFIRMATIVA, MENCIONE EL NOMBRE Y EL SERVICIO QUE REALIZA.
					</label>
					<textarea name="preg3" id="preg3" class="form-control"><?php echo $solicitud['preg3']; ?></textarea>

					<label for="preg4">
						4.	ADICIONAL A SUS OFICINAS CENTRALES (DE LA ORGANIZACIÓN FACILITADORA), ESPECIFIQUE CUÁNTOS CENTROS DE ACOPIO, ÁREAS DE PROCESAMIENTO U OFICINAS ADICIONALES TIENE.
					</label>
					<textarea class="form-control" name="preg4" id="" rows="3"><?php echo $solicitud['preg4']; ?></textarea>

					<label for="preg5">
						5.	SI SUBCONTRATA LOS SERVICIOS DE PLANTAS DE PROCESAMIENTO, EMPRESAS DE COMERCIALIZACIÓN O EMPRESAS QUE REALICEN LA IMPORTACIÓN O EXPORTACIÓN, INDIQUE SI ESTAS EMPRESAS VAN A REALIZAR EL REGISTRO BAJO EL PROGRAMA DEL SPP O SERÁN CONTROLADAS A TRAVÉS DE LA ORGANIZACIÓN DE PEQUEÑOS PRODUCTORES. <sup><a data-toggle="tooltip" title="Revisar las Directrices Generales de Sistema SPP" href="#">4</a></sup>
					</label>
					<textarea name="preg5" id="preg5" class="form-control"><?php echo $solicitud['preg5']; ?></textarea>

					<label for="preg6">
						6.	¿CUENTA CON UN SISTEMA DE CONTROL INTERNO PARA DAR CUMPLIMIENTO A LOS CRITERIOS DE LA NORMA GENERAL DEL SÍMBOLO DE PEQUEÑOS PRODUCTORES?, EN SU CASO, EXPLIQUE.WORKS.
					</label>
					<textarea name="preg6" id="preg6" class="form-control"><?php echo $solicitud['preg6']; ?></textarea>

					<p for="preg7">
						<b>7.	DEL TOTAL DE SUS VENTAS ¿QUÉ PORCENTAJE DEL PRODUCTO CUENTA CON LA CERTIFICACIÓN DE ORGÁNICO, COMERCIO JUSTO Y/O SÍMBOLO DE PEQUEÑOS PRODUCTORES?  </b>
						<i>(* Enter only quantity, integer or decimal)</i>
						<div class="col-lg-12">
							<div class="row">
								<div class="col-xs-3">
									<label for="organico">% ORGANICO</label>
									<input type="number" step="any" class="form-control" id="organico" name="organico" value="<?php echo $solicitud['organico']; ?>" placeholder="Ej: 0.0">
								</div>
								<div class="col-xs-3">
									<label for="comercio_justo">% COMERCIO JUSTO</label>
									<input type="number" step="any" class="form-control" id="comercio_justo" name="comercio_justo" value="<?php echo $solicitud['comercio_justo']; ?>" placeholder="Ej: 0.0">
								</div>
								<div class="col-xs-3">
									<label for="spp">SPP</label>
									<input type="number" step="any" class="form-control" id="spp" name="spp" value="<?php echo $solicitud['spp']; ?>" placeholder="Ej: 0.0">
								</div>
								<div class="col-xs-3">
									<label for="otro">SIN CERTIFICADO</label>
									<input type="number" step="any" class="form-control" id="otro" name="sin_certificado" value="<?php echo $solicitud['sin_certificado']; ?>" placeholder="Ej: 0.0">
								</div>
							</div>
						</div>
					</p>

	

					<p><b>8. ¿TUVO VENTAS SPP DURANTE EL CICLO DE CERTIFICACIÓN ANTERIOR?</b></p>
						<div class="col-xs-6">
							<?php 
							if($solicitud['preg8'] == 'SI'){
								echo 'SI <input type="radio" class="form-control" name="preg8" onclick="mostrar_ventas()" id="preg8" value="SI" checked>';
							}else{
								echo 'SI <input type="radio" class="form-control" name="preg8" onclick="mostrar_ventas()" id="preg8" value="SI">';
							}
							 ?>
						</div>
						<div class="col-xs-6">
							<?php 
							if($solicitud['preg8'] == 'NO'){
								echo 'NO <input type="radio" class="form-control" name="preg8" onclick="mostrar_ventas()" id="preg8" value="NO" checked>';
							}else{
								echo 'NO <input type="radio" class="form-control" name="preg8" onclick="mostrar_ventas()" id="preg8" value="NO">';
							}
							 ?>
						</div>			

					<p>
						<b>9.	SI SU RESPUESTA FUE POSITIVA, FAVOR DE INIDICAR CON UNA ‘X ‘EL RANGO DEL VALOR TOTAL DE SUS VENTAS SPP  DEL CICLO ANTERIOR DE ACUERDO A LA SIGUIENTE TABLA:</b>
					</p>

					<div class="col-xs-12 ">
				        <?php
				          if($solicitud['preg8'] == 'SI'){
				        ?>
				          <div class="col-xs-6">
				            <p class='text-center alert alert-success'><span class='glyphicon glyphicon-ok' aria-hidden='true'></span> SI</p>
				          </div>
				          <div class="col-xs-6">
				            <?php 
				              if(empty($solicitud['preg9'])){
				             ?>
				              <p class="alert alert-danger">No se proporciono ninguna respuesta.</p>
				            <?php 
				              }else if($solicitud['preg9'] == "HASTA $3,000 USD"){
				             ?>
				              <p class="alert alert-info">HASTA $3,000 USD</p>
				            <?php 
				              }else if($solicitud['preg9'] == "ENTRE $3,000 Y $10,000 USD"){
				             ?>
				             <p class="alert alert-info">ENTRE $3,000 Y $10,000 USD</p>
				            <?php 
				              }else if($solicitud['preg9'] == "ENTRE $10,000 A $25,000 USD"){
				             ?>
				             <p class="alert alert-info">ENTRE $10,000 A $25,000 USD</p>
				            <?php 
				              }else if($solicitud['preg9'] != "HASTA $3,000 USD" && $solicitud['preg9'] != "ENTRE $3,000 Y $10,000 USD" && $solicitud['preg9'] != "ENTRE $10,000 A $25,000 USD"){
				             ?>
				             <p class="alert alert-info"><?php echo $solicitud['preg9']; ?></p>
				             
				            <?php 
				              }
				             ?>
				          </div>
				        <?php
				          }else if($solicitud['preg8'] == 'NO'){
				        ?>
				          <div class="col-xs-12">
				            <p class='text-center alert alert-danger'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span> NO</p>
				          </div>
				        
				        <?php         
				          }
				        ?>
					</div>

							
					<label for="preg10">
						10.	FECHA ESTIMADA PARA COMENZAR A USAR EL SÍMBOLO DE PEQUEÑOS PRODUCTORES.
					</label>
					<input type="text" class="form-control" id="preg10" name="preg10" value="<?php echo $solicitud['preg10']; ?>">

					<label for="preg11">
						11.	ANEXAR EL CROQUIS GENERAL DE SU OPP, INDICANDO LAS ZONAS EN DONDE CUENTA CON SOCIOS.
					</label>
					<input type="file" class="form-control" id="preg11" name="preg11">
				</div>
			</div>

			<!------ FIN INFORMACION DATOS DE OPERACIÓN ------>


			<div class="col-md-12 text-center alert alert-success" style="padding:7px;">DATOS DE PRODUCTOS PARA LOS CUALES QUIERE UTILIZAR EL SÍMBOLO<sup>6</sup></div>
			<div class="col-lg-12">
				<table class="table table-bordered" id="tablaProductos">
					<tr>
						<td>Producto</td>
						<td>Volumen Total Estimado a Comercializar</td>
						<td>Producto Terminado</td>
						<td>Materia Prima</td>
						<td>País(es) de Destino</td>
						<td>Marca Propia</td>
						<td>Marca de un Cliente</td>
						<td>Sin cliente aún</td>
						<td>
							<button type="button" onclick="tablaProductos()" class="btn btn-primary" aria-label="Left Align">
							  <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
							</button>
							
						</td>		
					</tr>
					<?php 
					$query_producto_detalle = "SELECT * FROM productos WHERE idsolicitud_colectiva = $idsolicitud_colectiva";
					$producto_detalle = mysql_query($query_producto_detalle, $dspp) or die(mysql_error());
					$contador = 0;
					while($row_producto = mysql_fetch_assoc($producto_detalle)){
					?>
					  <tr>
					    <td>
					      <input type="text" class="form-control" name="producto_actual[]" id="exampleInputEmail1" placeholder="Producto" value="<?echo $row_producto['producto']?>">
					    </td>
					    <td>
					      <input type="text" class="form-control" name="volumen_actual[]" id="exampleInputEmail1" placeholder="Volumen" value="<?echo $row_producto['volumen']?>">
					    </td>
					    <td>
					      <?php 
					        if($row_producto['terminado'] == 'SI'){
					          echo "SI <input type='radio'  name='terminado_actual".$contador."' value='SI' checked><br>";
					        }else{
					          echo "SI <input type='radio'  name='terminado_actual".$contador."' value='SI'><br>";
					        } 
					        if($row_producto['terminado'] == 'NO'){
					          echo "NO <input type='radio'  name='terminado_actual".$contador."' value='NO' checked>";
					        }else{
					          echo "NO <input type='radio'  name='terminado_actual".$contador."' value='NO'>";
					        }
					       ?>
					    </td>          
					    <td>
					      <input type="text" class="form-control" name="materia_actual[]" id="exampleInputEmail1" placeholder="Materia" value="<?echo $row_producto['materia']?>">
					    </td>
					    <td>
					      <input type="text" class="form-control" name="destino_actual[]" id="exampleInputEmail1" placeholder="Destino" value="<?echo $row_producto['destino']?>">
					    </td>
					    <td>
					      <?php 
					        if($row_producto['marca_propia'] == 'SI'){
					          echo "SI <input type='radio'  name='marca_propia_actual".$contador."' value='SI' checked><br>";
					        }else{
					          echo "SI <input type='radio'  name='marca_propia_actual".$contador."' value='SI'><br>";
					        } 
					        if($row_producto['marca_propia'] == 'NO'){
					          echo "NO <input type='radio'  name='marca_propia_actual".$contador."' value='NO' checked>";
					        }else{
					          echo "NO <input type='radio'  name='marca_propia_actual".$contador."' value='NO'>";
					        }
					       ?>
					    </td>
					    <td>
					      <?php 
					        if($row_producto['marca_cliente'] == 'SI'){
					          echo "SI <input type='radio'  name='marca_cliente_actual".$contador."' value='SI' checked><br>";
					        }else{
					          echo "SI <input type='radio'  name='marca_cliente_actual".$contador."' value='SI'><br>";
					        } 
					        if($row_producto['marca_cliente'] == 'NO'){
					          echo "NO <input type='radio'  name='marca_cliente_actual".$contador."' value='NO' checked>";
					        }else{
					          echo "NO <input type='radio'  name='marca_cliente_actual".$contador."' value='NO'>";                  
					        }
					       ?>              
					    </td>
					    <td>
					      <?php 
					        if($row_producto['sin_cliente'] == 'SI'){
					          echo "SI <input type='radio'  name='sin_cliente_actual".$contador."' value='SI' checked><br>";
					        }else{
					          echo "SI <input type='radio'  name='sin_cliente_actual".$contador."' value='SI'><br>";
					        }
					        if($row_producto['sin_cliente'] == 'NO'){
					          echo "NO <input type='radio'  name='sin_cliente_actual".$contador."' value='NO' checked>";
					        }else{
					          echo "NO <input type='radio'  name='sin_cliente_actual".$contador."' value='NO'>";
					        }
					       ?> 
					    </td>
					      <input type="hidden" name="idproducto[]" value="<?echo $row_producto['idproducto']?>">                     
					  </tr>
					<?php 
					$contador++;
					}
					?>				
					<tr>
						<td colspan="8">
							<h6><sup>6</sup> La información proporcionada en esta sección será tratada con plena confidencialidad. Favor de insertar filas adicionales de ser necesario.</h6>
						</td>
					</tr>
				</table>
			</div>

			<div class="col-lg-12 text-center alert alert-success" style="padding:7px;">
				<b>COMPROMISOS</b>
			</div>
			<div class="col-lg-12 text-justify">
				<p>1.	Con el envío de esta solicitud se manifiesta el interés de recibir una propuesta de Certificación.</p>
				<p>2.	El proceso de Certificación comenzará en el momento que se confirme la recepción del pago correspondiente.</p>
				<p>3.	La entrega y recepción de esta solicitud no garantiza que el proceso de Certificación será positivo.</p>
				<p>4.	Conocer y dar cumplimiento a todos los requisitos de la Norma General del Símbolo de Pequeños Productores que le apliquen como Organización de Pequeños Productores, tanto Críticos como Mínimos, independientemente del tipo de evaluación que se realice.</p>

			</div>
			<div class="col-lg-12">

				<p style="font-size:14px;">
					<strong>Nombre de la persona que se responsabiliza de la veracidad de la información del formato y que le dará seguimiento a la solicitud de parte del solicitante:</strong>
				</p>

				<input type="hidden" name="idopp" value="<?php echo $solicitud['id_opp']; ?>">
				<input type="hidden" name="fecha_registro" value="<?php echo $solicitud['fecha_registro']; ?>">
				<input type="text" class="form-control" id="responsable" value="<?php echo $solicitud['responsable']; ?>" readonly>	

				<p>
					<b>Organismo de Certificación que recibe la solicitud:</b>
				</p>
				<p class="alert alert-info" style="padding:7px;">
					<?php echo $solicitud['abreviacionOC']; ?>
				</p>	
			</div>


		</fieldset>
	</form>
</div>


<script>
	
  function validar(){

    tipo_solicitud = document.getElementsByName("tipo_solicitud");
     
    var seleccionado = false;
    for(var i=0; i<tipo_solicitud.length; i++) {    
      if(tipo_solicitud[i].checked) {
        seleccionado = true;
        break;
      }
    }
     
    if(!seleccionado) {
      alert("Debes de seleccionar un Tipo de Solicitud");
      return false;
    }

    return true
  }

</script>
<script>
var contador=0;
	function tablaCertificaciones()
	{

	var table = document.getElementById("tablaCertificaciones");
	  {
	  var row = table.insertRow(1);
	  var cell1 = row.insertCell(0);
	  var cell2 = row.insertCell(1);
	  var cell3 = row.insertCell(2);
	  var cell4 = row.insertCell(3);

	  cell1.innerHTML = '<input type="text" class="form-control" name="certificacion['+contador+']" id="exampleInputEmail1" placeholder="CERTIFICACIÓN">';
	  cell2.innerHTML = '<input type="text" class="form-control" name="certificadora['+contador+']" id="exampleInputEmail1" placeholder="CERTIFICADORA">';
	  cell3.innerHTML = '<input type="text" class="form-control" name="ano_inicial['+contador+']" id="exampleInputEmail1" placeholder="AÑO INICIAL">';
	  cell4.innerHTML = '<div class="col-xs-6">SI<input type="radio" class="form-control" name="interrumpida['+contador+']" value="SI"></div><div class="col-xs-6">NO<input type="radio" class="form-control" name="interrumpida['+contador+']" value="NO"></div>';
	  }
		contador++;
	}		

	function mostrar(){
		document.getElementById('oculto').style.display = 'block';
	}
	function ocultar()
	{
		document.getElementById('oculto').style.display = 'none';
	}

	function mostrar_ventas(){
		document.getElementById('tablaVentas').style.display = 'block';
	}
	function ocultar_ventas()
	{
		document.getElementById('tablaVentas').style.display = 'none';
	}		

	var cont=0;
	function tablaProductos()
	{

	var table = document.getElementById("tablaProductos");
	  {

	  var row = table.insertRow(1);
	  var cell1 = row.insertCell(0);
	  var cell2 = row.insertCell(1);
	  var cell3 = row.insertCell(2);
	  var cell4 = row.insertCell(3);
	  var cell5 = row.insertCell(4);
	  var cell6 = row.insertCell(5);
	  var cell7 = row.insertCell(6); 
	  var cell8 = row.insertCell(7); 	   	  

	  

	  cell1.innerHTML = '<input type="text" class="form-control" name="producto['+cont+']" id="exampleInputEmail1" placeholder="Producto">';
	  
	  cell2.innerHTML = '<input type="text" class="form-control" name="volumen['+cont+']" id="exampleInputEmail1" placeholder="Volumen">';
	  
	  cell3.innerHTML = 'SI <input type="radio" name="terminado'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="terminado'+cont+'['+cont+']" id="" value="NO">';
	  
	  cell4.innerHTML = '<input type="text" class="form-control" name="materia['+cont+']" id="exampleInputEmail1" placeholder="Materia">';
	  
	  cell5.innerHTML = '<input type="text" class="form-control" name="destino['+cont+']" id="exampleInputEmail1" placeholder="Destino">';
	  
	  cell6.innerHTML = 'SI <input type="radio" name="marca_propia'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="marca_propia'+cont+'['+cont+']" id="" value="NO">';
	  
	  cell7.innerHTML = 'SI <input type="radio" name="marca_cliente'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="marca_cliente'+cont+'['+cont+']" id="" value="NO">';
	  
	  cell8.innerHTML = 'SI <input type="radio" name="sin_cliente'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="sin_cliente'+cont+'['+cont+']" id="" value="NO">';	 

	  cont++;
	  }

	}	

</script>