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

$idsolicitud_certificacion = $_GET['idsolicitud'];
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
  if(!empty($_POST['exportacion'])){
  	$exportacion = $_POST['exportacion'];
  }else{
  	$exportacion = 0;
  }

	if(!empty($_FILES['op_preg15']['name'])){
	    $_FILES["op_preg15"]["name"];
	      move_uploaded_file($_FILES["op_preg15"]["tmp_name"], $ruta_croquis.date("Ymd H:i:s")."_".$_FILES["op_preg15"]["name"]);
	      $croquis = $ruta_croquis.basename(date("Ymd H:i:s")."_".$_FILES["op_preg15"]["name"]);
	}else{
		$croquis = NULL;
	}

  // ACTUALIZAMOS LA INFORMACION DE LA SOLICITUD
	$updateSQL = sprintf("UPDATE solicitud_certificacion SET resp1 = %s, resp2 = %s, resp3 = %s, resp4 = %s, op_preg1 = %s, preg1_1 = %s, preg1_2 = %s, preg1_3 = %s, preg1_4 = %s, op_preg2 = %s, op_preg3 = %s, produccion = %s, procesamiento = %s, exportacion = %s, op_preg5 = %s, op_preg6 = %s, op_preg7 = %s, op_preg8 = %s, op_preg10 = %s, op_preg14 = %s, op_preg15 = %s WHERE idsolicitud_certificacion = %s",
	       GetSQLValueString($_POST['resp1'], "text"),
	       GetSQLValueString($_POST['resp2'], "text"),
	       GetSQLValueString($_POST['resp3'], "text"),
	       GetSQLValueString($_POST['resp4'], "text"),
	       GetSQLValueString($_POST['op_preg1'], "text"),
	       GetSQLValueString($_POST['preg1_1'], "text"),
	       GetSQLValueString($_POST['preg1_2'], "text"),
	       GetSQLValueString($_POST['preg1_3'], "text"),
	       GetSQLValueString($_POST['preg1_4'], "text"),
	       GetSQLValueString($_POST['op_preg2'], "text"),
	       GetSQLValueString($_POST['op_preg3'], "text"),
	       GetSQLValueString($produccion, "int"),
	       GetSQLValueString($procesamiento, "int"),
	       GetSQLValueString($exportacion, "int"),
	       GetSQLValueString($_POST['op_preg5'], "text"),
	       GetSQLValueString($_POST['op_preg6'], "text"),
	       GetSQLValueString($_POST['op_preg7'], "text"),
	       GetSQLValueString($_POST['op_preg8'], "text"),
	       GetSQLValueString($_POST['op_preg10'], "text"),
	       //GetSQLValueString($op_preg12, "text"),
	       //GetSQLValueString($op_preg13, "text"),
	       GetSQLValueString($_POST['op_preg14'], "text"),
	       GetSQLValueString($croquis, "text"),
	       GetSQLValueString($idsolicitud_certificacion, "int"));
	$actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());



  // ACTUALIZAMOS LA INFORMACION DE LA OPP
	$updateSQL = sprintf("UPDATE opp SET nombre = %s, pais = %s, direccion_oficina = %s, email = %s, telefono = %s, sitio_web = %s, razon_social = %s, direccion_fiscal = %s, rfc = %s, ruc = %s, produccion = %s, procesamiento = %s, exportacion = %s WHERE idopp = %s",
		GetSQLValueString($_POST['nombre'], "text"),
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
		GetSQLValueString($exportacion, "int"),
		GetSQLValueString($_POST['idopp'], "int"));
	$actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

  //ACTUALIZAMOS EL NUMERO DE SOCIOS
	$updateSQL = sprintf("UPDATE num_socios SET numero = %s WHERE fecha_registro = %s AND idopp = %s",
	  GetSQLValueString($_POST['resp1'], "int"),
	  GetSQLValueString($_POST['fecha_registro'], "int"),
	  GetSQLValueString($_POST['idopp'], "int"));
	$actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());

	////ACTUALIZAMOS LOS PORCENTAJES DE VENTAS
  if(!empty($_POST['organico']) || !empty($_POST['comercio_justo']) || !empty($_POST['spp']) || !empty($_POST['sin_certificado'])){
  	$row_ventas = mysql_query("SELECT * FROM porcentaje_productoVentas WHERE idsolicitud_certificacion = $_GET[idsolicitud]", $dspp) or die(mysql_error());
  	$existe_venta = mysql_num_rows($row_ventas);
  	if($existe_venta){
	  	$updateSQL = sprintf("UPDATE porcentaje_productoVentas SET organico = %s, comercio_justo = %s, spp = %s, sin_certificado = %s WHERE idsolicitud_certificacion = %s",
	  		GetSQLValueString($_POST['organico'], "text"),
	  		GetSQLValueString($_POST['comercio_justo'], "text"),
	  		GetSQLValueString($_POST['spp'], "text"),
	  		GetSQLValueString($_POST['sin_certificado'], "text"),
	  		GetSQLValueString($idsolicitud_certificacion, "int"));
	  	$actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());
  	}else{
  		$insertSQL = sprintf("INSERT INTO porcentaje_productoVentas (organico, comercio_justo, spp, sin_certificado, idsolicitud_certificacion, idopp) VALUES (%s, %s, %s, %s, %s, %s)",
	  		GetSQLValueString($_POST['organico'], "text"),
	  		GetSQLValueString($_POST['comercio_justo'], "text"),
	  		GetSQLValueString($_POST['spp'], "text"),
	  		GetSQLValueString($_POST['sin_certificado'], "text"),
	  		GetSQLValueString($idsolicitud_certificacion, "int"),
  			GetSQLValueString($_POST['idopp'], "int"));
  		$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
  	}

  }
 

	/*************************** INICIA INSERTAR CERTIFICACIONES ***************************/

		if(isset($_POST['certificadora'])){
			$certificadora = $_POST['certificadora'];
		}else{
			$certificadora = NULL;
		}

		if(isset($_POST['ano_inicial'])){
			$ano_inicial = $_POST['ano_inicial'];
		}else{
			$ano_inicial = NULL;
		}

		if(isset($_POST['interrumpida'])){
			$interrumpida = $_POST['interrumpida'];
		}else{
			$interrumpida = NULL;
		}

		if(isset($_POST['certificacion'])){
			$certificacion = $_POST['certificacion'];
			
			for($i=0;$i<count($certificacion);$i++){
				if($certificacion[$i] != NULL){
					#for($i=0;$i<count($certificacion);$i++){
					$insertSQL = sprintf("INSERT INTO certificaciones (idsolicitud_certificacion, certificacion, certificadora, ano_inicial, interrumpida) VALUES (%s, %s, %s, %s, %s)",
					    GetSQLValueString($idsolicitud_certificacion, "int"),
					    GetSQLValueString(strtoupper($certificacion[$i]), "text"),
					    GetSQLValueString(strtoupper($certificadora[$i]), "text"),
					    GetSQLValueString($ano_inicial[$i], "text"),
					    GetSQLValueString($interrumpida[$i], "text"));

					$Result = mysql_query($insertSQL, $dspp) or die(mysql_error());
					#}
				}
			}

		}
	/*************************** INICIA INSERTAR CERTIFICACIONES ***************************/


    // SE ACTUALIZAN LAS CERTIFICACIONES
    if(isset($_POST['idcertificacion'])){
	    $idcertificacion = $_POST['idcertificacion'];
			if(isset($_POST['certificacion_actual'])){
				$certificacion_actual = $_POST['certificacion_actual'];
			}else{
				$certificacion_actual = NULL;
			}


			if(isset($_POST['certificadora_actual'])){
				$certificadora_actual = $_POST['certificadora_actual'];
			}else{
				$certificadora_actual = NULL;
			}

			if(isset($_POST['ano_inicial_actual'])){
				$ano_inicial_actual = $_POST['ano_inicial_actual'];
			}else{
				$ano_inicial_actual = NULL;
			}

			if(isset($_POST['interrumpida_actual'])){
				$interrumpida_actual = $_POST['interrumpida_actual'];
			}else{
				$interrumpida_actual = NULL;
			}


	    for($i=0;$i<count($certificacion_actual);$i++){
	      if($certificacion_actual[$i] != NULL){
	        #for($i=0;$i<count($certificacion_actual);$i++){

	      	$updateSQL = sprintf("UPDATE certificaciones SET certificacion = %s, certificadora = %s, ano_inicial = %s, interrumpida = %s WHERE idcertificacion = %s",
	      		GetSQLValueString(strtoupper($certificacion_actual[$i]), "text"),
	      		GetSQLValueString(strtoupper($certificadora_actual[$i]), "text"),
	      		GetSQLValueString($ano_inicial_actual[$i], "text"),
	      		GetSQLValueString($interrumpida_actual[$i], "text"),
	      		GetSQLValueString($idcertificacion[$i], "int"));

	        //$updateSQL = "UPDATE certificaciones SET certificacion= '".$certificacion[$i]."', certificadora='".$certificadora_actual[$i]."', ano_inicial= '".$ano_inicial_actual[$i]."', interrumpida= '".$interrumpida[$i]."' WHERE idcertificacion= '".$idcertificacion[$i]."'";

	        $Result1 = mysql_query($updateSQL, $dspp) or die(mysql_error());
	        }
	    }
    }


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

					$str = iconv($charset, 'ASCII//TRANSLIT', $producto[$i]);
					$producto[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));

					$str = iconv($charset, 'ASCII//TRANSLIT', $destino[$i]);
					$destino[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));

					$str = iconv($charset, 'ASCII//TRANSLIT', $materia[$i]);
					$materia[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));


				    $insertSQL = sprintf("INSERT INTO productos (idopp, idsolicitud_certificacion, producto, volumen, terminado, materia, destino, marca_propia, marca_cliente, sin_cliente) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
				    	GetSQLValueString($_POST['idopp'], "int"),
				          GetSQLValueString($idsolicitud_certificacion, "int"),
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

						$str = iconv($charset, 'ASCII//TRANSLIT', $producto_actual[$i]);
						$producto_actual[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));

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
 

$query = "SELECT solicitud_certificacion.*, opp.idopp AS 'id_opp', opp.nombre, opp.spp AS 'spp_opp', opp.sitio_web, opp.email, opp.telefono, opp.pais, opp.ciudad, opp.razon_social, opp.direccion_oficina, opp.direccion_fiscal, opp.rfc, opp.ruc, oc.abreviacion AS 'abreviacionOC', porcentaje_productoVentas.* FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp INNER JOIN oc ON solicitud_certificacion.idoc = oc.idoc LEFT JOIN porcentaje_productoVentas ON solicitud_certificacion.idsolicitud_certificacion = porcentaje_productoVentas.idsolicitud_certificacion WHERE solicitud_certificacion.idsolicitud_certificacion = $idsolicitud_certificacion";
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
				<h3 class="text-center">Application for Small Producers´ Organization Certification</h3>
			</div>


			<div class="col-md-12 text-center alert alert-success" style="padding:7px;"><b>GENERAL INFORMATION</b></div>

			<div class="col-lg-12 alert alert-info" style="padding:7px;">
				<div class="col-md-4">
					<div class="col-xs-12">
						<b>Send to Certification Entity:</b>
					</div>
					<div class="col-xs-12">
						<input type="text" class="form-control" value="<?php echo $solicitud['abreviacionOC']; ?>" readonly>
					</div>
				</div>
				<div class="col-md-4">
					<div class="col-xs-12">
						<b>TYPE OF APPLICATION</b>
					</div>
					<div class="col-xs-6">
						<input type="text" class="form-control" value="<?php echo $solicitud['tipo_solicitud']; ?>"readonly>
					</div>
					
				</div>
				<div class="col-md-4">

					<input type="hidden" name="actualizar_solicitud" value="1">
					<button style="color:white" type="submit" class="btn btn-warning form-control"><span class="glyphicon glyphicon-refresh" aria-hidden="true"></span> UPDATE APPLICATION</button>
				</div>

			</div>

			<!------ INICIA INFORMACION GENERAL Y DATOS FISCALES ------>
			<div class="col-lg-12">
				<div class="col-md-6">
					<div class="col-md-12 text-center alert alert-warning" style="padding:7px;">GENERAL INFORMATION</div>
					<label for="fecha_elaboracion">DATE OF ELABORATION</label>
					<input type="text" class="form-control" id="fecha_elaboracion" name="fecha_elaboracion" value="<?php echo date('Y-m-d', time()); ?>" readonly>	

					<label for="spp">SPP IDENTIFICATION CODE(#SPP): </label>
					<input type="text" class="form-control" id="spp" name="spp" value="<?php echo $solicitud['spp_opp']; ?>" readonly>

					<label for="nombre">NAME OF SMALL PRODUCER ORGANIZATION: </label>
					<textarea name="nombre" id="nombre" class="form-control"><?php echo $solicitud['nombre']; ?></textarea>

					<label for="pais">COUNTRY:</label>
					 <select name="pais" id="pais" class="form-control">
					 	<option value="">Select a country</option>
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

					<label for="direccion_oficina">COMPLETE ADDRESS FOR ORGANIZATION’S LOCATION (STREET, DISTRICT, TOWN/CITY, REGION):</label>
					<textarea name="direccion_oficina" id="direccion_oficina"  class="form-control"><?php echo $solicitud['direccion_oficina']; ?></textarea>

					<label for="email">EMAIL:</label>
					<input type="email" class="form-control" id="email" name="email" value="<?php echo $solicitud['email']; ?>">

					<label for="telefono">ORGANIZATION’S TELEPHONES(COUNTRY CODE+AREA CODE+NUMBER)</label>
					<input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo $solicitud['telefono']; ?>">	

					<label for="sitio_web">Website:</label>
					<input type="text" class="form-control" id="sitio_web" name="sitio_web" value="<?php echo $solicitud['sitio_web']; ?>">

				</div>

				<div class="col-md-6">
					<div class="col-md-12 text-center alert alert-warning" style="padding:7px;">DATA FOR INVOICING</div>

					<label for="razon_social">BUSINESS NAME</label>
					<input type="text" class="form-control" id="razon_social" name="razon_social" value="<?php echo $solicitud['razon_social']; ?>">

					<label for="direccion_fiscal">FISCAL ADDRESS</label>
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
					<div class="col-md-12 text-center alert alert-warning" style="padding:7px;">CONTACT PERSON(S) OF APPLICATION</div>

					<label for="persona1">CONTACT PERSON(S)</label>
					<input type="text" class="form-control" id="persona1" value="<?php echo $solicitud['contacto1_nombre']; ?>"  readonly>
					<input type="text" class="form-control" id="" value="<?php echo $solicitud['contacto2_nombre']; ?>" placeholder="Name Person 2" readonly>

					<label for="cargo">POSITION(S)</label>
					<input type="text" class="form-control" id="cargo" value="<?php echo $solicitud['contacto1_cargo']; ?>" placeholder="* Position Person 1" readonly>
					<input type="text" class="form-control" id="" value="<?php echo $solicitud['contacto2_cargo']; ?>" palceholder="Position Person 2" readonly>

					<label for="email">EMAIL ADDRESS FROM THE CONTACT PERSON(S)</label>
					<input type="email" class="form-control" id="email" value="<?php echo $solicitud['contacto1_email']; ?>" placeholder="* Email Person 1" readonly>
					<input type="email" class="form-control" id="" value="<?php echo $solicitud['contacto2_email']; ?>" placeholder="Email Person 2" readonly>

					<label for="telefono">TELEPHONE(S) FOR CONTACT PERSON(S)</label>
					<input type="text" class="form-control" id="telefono" value="<?php echo $solicitud['contacto1_telefono']; ?>" placeholder="* Telephone Person 1" readonly>
					<input type="text" class="form-control" id="" value="<?php echo $solicitud['contacto2_telefono']; ?>" placeholder="Telephone Person 2" readonly>

				</div>

				<div class="col-md-6">
					<div class="col-md-12 text-center alert alert-warning" style="padding:7px;">ERSON(S) OF THE ADMINISTRATIVE AREA</div>

					<label for="persona_adm">PERSON(S) OF THE ADMINISTRATIVE AREA</label>
					<input type="text" class="form-control" id="persona_adm" value="<?php echo $solicitud['adm1_nombre']; ?>" placeholder="Name Person 1" readonly>
					<input type="text" class="form-control" id="" value="<?php echo $solicitud['adm2_nombre']; ?>" placeholder="Name Person 2" readonly>

					<label for="email_adm">EMAIL</label>
					<input type="email" class="form-control" id="email_adm" value="<?php echo $solicitud['adm1_email']; ?>" placeholder="Email Person 1" readonly>
					<input type="email" class="form-control" id="" value="<?php echo $solicitud['adm2_email']; ?>" placeholder="Email Person 2" readonly>

					<label for="telefono_adm">TELEPHONE(S) PERSON(S) ADMINISTRATIVE AREA</label>
					<input type="text" class="form-control" id="telefono_adm" value="<?php echo $solicitud['adm1_telefono']; ?>" placeholder="Telephone Person 1" readonly>
					<input type="text" class="form-control" id="" value="<?php echo $solicitud['adm2_telefono']; ?>" placeholder="Telephone Person 2" readonly>
				</div>
			</div>
			<!------ FIN INFORMACION CONTACTOS Y AREA ADMINISTRATIVA ------>



			<!------ INICIA INFORMACION DATOS DE OPERACIÓN ------>


			<div class="col-lg-12">
				<div class="col-md-12">
					<label for="resp1">NUMBER OF PRODUCERS MEMBERS:</label>
					<input type="number" step="any" class="form-control" id="resp1" name="resp1" value="<?php echo $solicitud['resp1']; ?>" >

					<label for="resp2">NUMBER OF PRODUCERS MEMBERS OF THE  PRODUCT (S) TO BE INCLUDED IN THE CERTIFICATION:</label>
					<input type="text" class="form-control" id="resp2" name="resp2" value="<?php echo $solicitud['resp2']; ?>" >

					<label for="resp3">TOTAL PRODUCTION VOLUME(S) BY PRODUCT (UNITE OF MEASURE):</label>
					<input type="text" class="form-control" id="resp3" name="resp3" value="<?php echo $solicitud['resp3']; ?>" >
					
					<label for="resp4">MAXIMUM SIZE OF THE UNIT OF PRODUCTION BY THE PRODUCER OF THE PRODUCT(S) TO INCLUDE IN THE CERTIFICATION:</label>
					<input type="text" class="form-control" id="resp4" name="resp4" value="<?php echo $solicitud['resp4']; ?>" >



				</div>
			</div>

			<div class="col-md-12 text-center alert alert-success" style="padding:7px;">INFORMATION ON OPERATIONS</div>

			<div class="col-lg-12">
				<div class="col-md-12">
					<label for="op_preg1">
						1.	EXPLAIN IF THE SMALL PRODUCERS’ ORGANIZATION (SPO) IS AT THE 1st, 2nd, 3rd or 4th LEVEL, AS WELL AS EXPLAIN THE NUMBER OF ORGANIZATIONS OF THE 3rd,2nd or 1st LEVEL, AND THE NUMBER OF COMMUNITIES, AREAS OR GROUPS OF WORK, IN HIS OR HER CASE, THAT ACCOUNT:
					</label>
					<input type="text" class="form-control" id="op_preg1" name="op_preg1" value="<?php echo $solicitud['op_preg1']; ?>" >

					<div class="col-xs-3">
						<label for="preg1_1">
							1.1 NUMBER OF SPO 3rd  LEVEL:
						</label>
						<input type="text" class="form-control" id="preg1_1" name="preg1_1" value="<?php echo $solicitud['preg1_1']; ?>" >
					</div>
					<div class="col-xs-3">
						<label for="preg1_2">
							1.2 NUMBER OF SPO 2nd  LEVEL:
						</label>
						<input type="text" class="form-control" id="preg1_2" name="preg1_2" value="<?php echo $solicitud['preg1_2']; ?>" >
					</div>
					<div class="col-xs-3">
						<label for="preg1_3">
							1.3 NUMBER OF SPO 1st  LEVEL:
						</label>
						<input type="text" class="form-control" id="preg1_3" name="preg1_3" value="<?php echo $solicitud['preg1_3']; ?>" >
					</div>
					<div class="col-xs-3">
						<label for="preg1_4">
							1.4 NUMBER OF COMMUNITIES, AREAS OR GROUPS OF WORK:
						</label>
						<input type="text" class="form-control" id="preg1_4" name="preg1_4" value="<?php echo $solicitud['preg1_4']; ?>" >
					</div>


					<label for="op_preg2">
						2.	SPECIFY WHICH PRODUCT (S) YOU WANT TO INCLUDE IN THE CERTIFICATE OF THE SYMBOL OF SMALL PRODUCERS FOR WHICH THE CERTIFICATION ENTITY WILL CONDUCT THE ASSESSMENT.
					</label>
					<textarea name="op_preg2" id="op_preg2" class="form-control"><?php echo $solicitud['op_preg2']; ?></textarea>

					<label for="op_preg3">
						3.	MENTION IF YOUR ORGANIZATION WOULD LIKE TO INCLUDE SOME ADDITIONAL DESCRIPTOR FOR COMPLEMENTARY USE WITH THE GRAPHIC DESIGN OF THE SMALL PRODUCERS’ SYMBOL.<sup>4</sup>
					</label>
					<input type="text" class="form-control" id="op_preg3" name="op_preg3" value="<?php echo $solicitud['op_preg3']; ?>">

					<div >
						<label for="alcance_opp">
							4. SELECT THE SCOPE OF THE SMALL PRODUCERS’ ORGANIZATION:
						</label>
					</div>
					
					<div class="col-xs-4">
						<label>PRODUCTION</label>
						<input type="checkbox" name="produccion" class="form-control" value="1" <?php if($solicitud['produccion']){ echo 'checked';} ?>>
					</div>
					<div class="col-xs-4">
						<label>PROCESSING</label>
						<input type="checkbox" name="procesamiento" class="form-control" value="1" <?php if($solicitud['procesamiento']){ echo 'checked';} ?>>
					</div>
					<div class="col-xs-4">
						<label>TRAIDING</label>
						<input type="checkbox" name="exportacion" class="form-control" value="1" <?php if($solicitud['exportacion']){ echo 'checked';} ?>>
					</div>


					<label for="op_preg5">
						5.	SPECIFY IF YOU SUBCONTRACT THE SERVICES OF PROCESSING PLANTS, TRADING COMPANIES OR COMPANIES THAT CARRY OUT THE IMPORT OR EXPORT, IF THE ANSWER IS AFFIRMATIVE, MENTION THE NAME AND THE SERVICE THAT PERFORMS.
					</label>
					<textarea name="op_preg5" id="op_preg5" class="form-control"><?php echo $solicitud['op_preg5']; ?></textarea>

					<label for="op_preg6">
						6.	IF YOU SUBCONTRACT THE SERVICES OF PROCESSING PLANTS, TRADING COMPANIES OR COMPANIES THAT CARRY OUT THE IMPORT OR EXPORT, INDICATE WHETHER THESE COMPANIES ARE GOING TO APPLY FOR THE REGISTRATION UNDER SPP CERTIFICATION PROGRAM. <sup>5</sup>
						<br>
						<small><sup>5</sup> Review the General Application Guidelines to the SPP System.</small>
					</label>
					<textarea name="op_preg6" id="op_preg6" class="form-control"><?php echo $solicitud['op_preg6']; ?></textarea>

					<label for="op_preg7">
						7.	IN ADDITION TO YOUR MAIN OFFICES, PLEASE SPECIFY HOW MANY COLLECTION CENTERS, PROCESSING AREAS AND ADDITIONAL OFFICES YOU HAVE.
					</label>
					<textarea name="op_preg7" id="op_preg7" class="form-control"><?php echo $solicitud['op_preg7']; ?></textarea>

					<label for="op_preg8">
						8.	IF THE ORGANIZATION HAS AN INTERNAL CONTROL SYSTEM FOR COMPLYING WITH THE CRITERIA IN THE GENERAL STANDARD OF THE SMALL PRODUCERS’ SYMBOL, PLEASE EXPLAIN HOW IT WORKS.
					</label>
					<textarea name="op_preg8" id="op_preg8" class="form-control"><?php echo $solicitud['op_preg8']; ?></textarea>
					<p class="alert alert-info">9.	FILL OUT THE TABLE ACCORDING YOUR CERTIFICATIONS, (example: EU, NOP, JASS, FLO, etc)</p>

					<table class="table table-bordered" id="tablaCertificaciones">
						<tr>
							<td>CERTIFICATION</td>
							<td>CERTIFICATION ENTITY</td>
							<td>INITIAL YEAR OF CERTIFICATION</td>
							<td>HAS BEEN INTERRUPTED?</td>
							<td>
								<button type="button" onclick="tablaCertificaciones()" class="btn btn-primary" aria-label="Left Align">
								  <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
								</button>
								
							</td>

						</tr>
						<?php 
						$query_certificacion_detalle = "SELECT * FROM certificaciones WHERE idsolicitud_certificacion = $idsolicitud_certificacion";
						$certificacion_detalle = mysql_query($query_certificacion_detalle, $dspp) or die(mysql_error());
						$contador = 0;
						while($row_certificacion = mysql_fetch_assoc($certificacion_detalle)){
						?>
							<tr class="text-center">
							  <td><input type="text" class="form-control" name="certificacion_actual[]" id="exampleInputEmail1" placeholder="CERTIFICACIÓN" value="<?echo $row_certificacion['certificacion']?>"></td>
							  <td><input type="text" class="form-control" name="certificadora_actual[]" id="exampleInputEmail1" placeholder="CERTIFICADORA" value="<?echo $row_certificacion['certificadora']?>"></td>
							  <td><input type="text" class="form-control" name="ano_inicial_actual[]" id="exampleInputEmail1" placeholder="AÑO INICIAL" value="<?echo $row_certificacion['ano_inicial']?>"></td>
							  <td><input type="text" class="form-control" name="interrumpida_actual[]" id="exampleInputEmail1" placeholder="¿HA SIDO INTERRUMPIDA?" value="<?echo $row_certificacion['interrumpida']?>"></td>
							  <input type="hidden" name="idcertificacion[]" value="<?echo $row_certificacion['idcertificacion']?>">
							</tr>
						<?php 
							$contador++; 
						} 
						?> 
					</table>	

					<label for="op_preg10">
						10.	ACCORDING THE CERTIFICATIONS, IN ITS MOST RECENT INTERNAL AND EXTERNAL EVALUATIONS, HOW MANY CASES OF NON COMPLIANCE WERE IDENTIFIED? PLEASE EXPLAIN IF THEY HAVE BEEN RESOLVED OR WHAT THEIR STATUS IS?</label>
					<textarea name="op_preg10" id="op_preg10" class="form-control"><?php echo $solicitud['op_preg10']; ?></textarea>

					<p for="op_preg11">
						<b>11.	OF THE APPLICANT’S TOTAL TRADING DURING THE PREVIOUS CYCLE, WHAT PERCENTAGE WAS CONDUCTED UNDER THE SCHEMES OF CERTIFICATION FOR ORGANIC, FAIR TRADE AND/OR THE SMALL PRODUCERS’ SYMBOL?</b>
					</p>
					<p><i>(* Introducir solo cantidad, entero o decimales)</i></p>
						<div class="col-xs-3">
							<label for="organico">% ORGANIC</label>
							<input type="number" step="any" class="form-control" id="organico" name="organico" value="<?php echo $solicitud['organico']; ?>" placeholder="Ej: 0.0">
						</div>
						<div class="col-xs-3">
							<label for="comercio_justo">% FAIR TRADE</label>
							<input type="number" step="any" class="form-control" id="comercio_justo" name="comercio_justo" value="<?php echo $solicitud['comercio_justo']; ?>" placeholder="Ej: 0.0">
						</div>
						<div class="col-xs-3">
							<label for="spp">SMALL PRODUCERS´ SYMBOL</label>
							<input type="number" step="any" class="form-control" id="spp" name="spp" value="<?php echo $solicitud['spp']; ?>" placeholder="Ej: 0.0">
							
						</div>
						<div class="col-xs-3">
							<label for="otro">OTHER</label>
							<input type="number" step="any" class="form-control" id="otro" name="sin_certificado" value="<?php echo $solicitud['sin_certificado']; ?>" placeholder="Ej: 0.0">
							
						</div>

					<p for="op_preg11">
						<b>11.	OF THE APPLICANT’S TOTAL TRADING DURING THE PREVIOUS CYCLE, WHAT PERCENTAGE WAS CONDUCTED UNDER THE SCHEMES OF CERTIFICATION FOR ORGANIC, FAIR TRADE AND/OR THE SMALL PRODUCERS’ SYMBOL?</b>
						<i>(* Enter only quantity, integer or decimal)</i>
						<div class="col-lg-12">
							<div class="row">
								<div class="col-xs-3">
									<label for="organico">% ORGANIC</label>
									<input type="number" step="any" class="form-control" id="organico" name="organico" value="<?php echo $solicitud['organico']; ?>" placeholder="Ej: 0.0">
								</div>
								<div class="col-xs-3">
									<label for="comercio_justo">% FAIR TRADE</label>
									<input type="number" step="any" class="form-control" id="comercio_justo" name="comercio_justo" value="<?php echo $solicitud['comercio_justo']; ?>" placeholder="Ej: 0.0">
								</div>
								<div class="col-xs-3">
									<label for="spp">SMALL PRODUCERS´ SYMBOL</label>
									<input type="number" step="any" class="form-control" id="spp" name="spp" value="<?php echo $solicitud['spp']; ?>" placeholder="Ej: 0.0">
								</div>
								<div class="col-xs-3">
									<label for="otro">WITHOUT CERTIFICATE</label>
									<input type="number" step="any" class="form-control" id="otro" name="sin_certificado" value="<?php echo $solicitud['sin_certificado']; ?>" placeholder="Ej: 0.0">
								</div>
							</div>
						</div>
					</p>	


					<p><b>12 - 13.	DID YOU HAVE SPP PURCHASES DURING THE PREVIOUS CERTIFICATION CYCLE?</b></p>
					<div class="col-xs-12 ">
				        <?php
				          if($solicitud['op_preg12'] == 'SI'){
				              //echo "SI <input type='radio' name='op_preg12'  checked readonly>";
				            /*echo "</div>";
				            echo "<div class='col-xs-6'>";
				              echo "<p class='text-center alert alert-danger'>NO</p>";
				              echo "NO <input type='radio' name='op_preg12'  readonly>";
				            echo "</div>";*/
				        ?>
				          <div class="col-xs-6">
				            <p class='text-center alert alert-success'><span class='glyphicon glyphicon-ok' aria-hidden='true'></span> SI</p>
				          </div>
				          <div class="col-xs-6">
				            <?php 
				              if(empty($solicitud['op_preg13'])){
				             ?>
				              <p class="alert alert-danger">No response was provided.</p>
				            <?php 
				              }else if($solicitud['op_preg13'] == "HASTA $3,000 USD"){
				             ?>
				              <p class="alert alert-info">LESS THAN $3,000 USD</p>
				            <?php 
				              }else if($solicitud['op_preg13'] == "ENTRE $3,000 Y $10,000 USD"){
				             ?>
				             <p class="alert alert-info">BETWEENN $3,000 AND $10,000 USD</p>
				            <?php 
				              }else if($solicitud['op_preg13'] == "ENTRE $10,000 A $25,000 USD"){
				             ?>
				             <p class="alert alert-info">BEETWENN $10,000 AND $25,000 USD</p>
				            <?php 
				              }else if($solicitud['op_preg13'] != "HASTA $3,000 USD" && $solicitud['op_preg13'] != "ENTRE $3,000 Y $10,000 USD" && $solicitud['op_preg13'] != "ENTRE $10,000 A $25,000 USD"){
				             ?>
				             <p class="alert alert-info"><?php echo $solicitud['op_preg13']; ?></p>
				             
				            <?php 
				              }
				             ?>
				          </div>

				        <?php
				          }else if($solicitud['op_preg12'] == 'NO'){
				        ?>
				          <div class="col-xs-12">
				            <p class='text-center alert alert-danger'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span> NO</p>
				          </div>
				        
				        <?php         
				          }
				        ?>
					</div>
					
					<label for="op_preg14">
						14.	ESTIMATED DATE FOR BEGINNING TO USE THE SMALL PRODUCERS’ SYMBOL:
					</label>
					<input type="text" class="form-control" id="op_preg14" name="op_preg14" value="<?php echo $solicitud['op_preg14']; ?>">

					<div class="col-md-12" style="margin-top:10px;">
						<div class="row">
							<div class="col-xs-12" style="padding:0px;">
								<p><strong>15.	PLEASE ATTACH A GENERAL MAP OF THE AREA WHERE YOUR SPO OPERATES, INDICATING THE ZONES WHERE MEMBERS ARE LOCATED.</strong></p>
							</div>
							<?php 
							if(empty($solicitud['op_preg15'])){
							?>
								<div class="col-xs-6 alert alert-danger">
									Not Available
								</div>
								<div class="col-xs-6 alert alert-success" style="padding:0px;">
									<b>To add sketch</b>
									<input type="file" class="form-control" id="op_preg15" name="op_preg15">
								</div>	
							<?php
							}else{
							?>
								<a class="btn btn-success" href="<?echo $solicitud['op_preg15']?>" target="_blank"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Download sketch</a>
							<?php
							}
							 ?>
						</div>
					</div>

					
				</div>
			</div>

			<!------ FIN INFORMACION DATOS DE OPERACIÓN ------>

			<div class="col-md-12 text-center alert alert-success" style="padding:7px;">INFORMATION ON PRODUCTS FOR WHICH APPLICATION WISHES TO USE SYMBOL<sup>6</sup></div>
			<div class="col-lg-12">
				<table class="table table-bordered" id="tablaProductos">
					<tr>
						<td>Product</td>
						<td>Total Estimated Volume to be Traded</td>
						<td>Finished Product</td>
						<td>Row material</td>
						<td>Destination Countries</td>
						<td>Own brand</td>
						<td>Client´s brand</td>
						<td>Still without client</td>
						<td>
							<button type="button" onclick="tablaProductos()" class="btn btn-primary" aria-label="Left Align">
							  <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
							</button>
							
						</td>		
					</tr>
					<?php 
					$query_producto_detalle = "SELECT * FROM productos WHERE idsolicitud_certificacion = $idsolicitud_certificacion";
					$producto_detalle = mysql_query($query_producto_detalle, $dspp) or die(mysql_error());
					$contador = 0;
					while($row_producto = mysql_fetch_assoc($producto_detalle)){
					?>
					  <tr>
					    <td>
					      <input type="text" class="form-control" name="producto_actual[]" id="exampleInputEmail1" placeholder="Product" value="<?echo $row_producto['producto']?>">
					    </td>
					    <td>
					      <input type="text" class="form-control" name="volumen_actual[]" id="exampleInputEmail1" placeholder="Volume" value="<?echo $row_producto['volumen']?>">
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
					      <input type="text" class="form-control" name="materia_actual[]" id="exampleInputEmail1" placeholder="Material" value="<?echo $row_producto['materia']?>">
					    </td>
					    <td>
					      <input type="text" class="form-control" name="destino_actual[]" id="exampleInputEmail1" placeholder="Destination" value="<?echo $row_producto['destino']?>">
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
							<h6><sup>6</sup> Information provided in this section will be handled with complete confidentiality. Please insert additional lines if necessary.</h6>
						</td>
					</tr>
				</table>
			</div>

			<div class="col-lg-12 text-center alert alert-success" style="padding:7px;">
				<b>COMMITMENTS</b>
			</div>
			<div class="col-lg-12 text-justify">
				<p>1.	By sending in this document, the applicant expresses its interest in receiving a proposal for certification with the Small Producers’ Symbol.</p>
				<p>2.	The certification process will begin when it is confirmed that the payment corresponding to the proposal has been received.</p>
				<p>3.	The fact that this application is delivered and received does not guarantee that the results of the certification process will be positive.</p>
				<p>4.	The applicant will become familiar with and comply with all the applicable requirements in the General Standard of the Small Producers’ Symbol for a Small Producers’ Organization, including both Critical and Minimum Criteria, and independently of the type of evaluation conducted.</p>
			</div>
			<div class="col-lg-12">

				<p style="font-size:14px;">
					<strong>Name of the person who is responsible for the accuracy of the information on this form, and who, on behalf of the Applicant, will follow up on the application:</strong>
				</p>

				<input type="hidden" name="idopp" value="<?php echo $solicitud['idopp']; ?>">
				<input type="hidden" name="fecha_registro" value="<?php echo $solicitud['fecha_registro']; ?>">
				<input type="text" class="form-control" id="responsable" value="<?php echo $solicitud['responsable']; ?>" >	

				<p>
					<b>Certification Entity who receives the application:</b>
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

	  cell1.innerHTML = '<input type="text" class="form-control" name="certificacion['+contador+']" id="exampleInputEmail1" placeholder="CERTIFICATION">';
	  cell2.innerHTML = '<input type="text" class="form-control" name="certificadora['+contador+']" id="exampleInputEmail1" placeholder="ENTITY">';
	  cell3.innerHTML = '<input type="text" class="form-control" name="ano_inicial['+contador+']" id="exampleInputEmail1" placeholder="INITIAL YEAR">';
	  cell4.innerHTML = '<div class="col-xs-6">YES<input type="radio" class="form-control" name="interrumpida['+contador+']" value="SI"></div><div class="col-xs-6">NO<input type="radio" class="form-control" name="interrumpida['+contador+']" value="NO"></div>';
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

	  

	  cell1.innerHTML = '<input type="text" class="form-control" name="producto['+cont+']" id="exampleInputEmail1" placeholder="Product">';
	  
	  cell2.innerHTML = '<input type="text" class="form-control" name="volumen['+cont+']" id="exampleInputEmail1" placeholder="Volume">';
	  
	  cell3.innerHTML = 'YES <input type="radio" name="terminado'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="terminado'+cont+'['+cont+']" id="" value="NO">';
	  
	  cell4.innerHTML = '<input type="text" class="form-control" name="materia['+cont+']" id="exampleInputEmail1" placeholder="Material">';
	  
	  cell5.innerHTML = '<input type="text" class="form-control" name="destino['+cont+']" id="exampleInputEmail1" placeholder="Destination">';
	  
	  cell6.innerHTML = 'YES <input type="radio" name="marca_propia'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="marca_propia'+cont+'['+cont+']" id="" value="NO">';
	  
	  cell7.innerHTML = 'YES <input type="radio" name="marca_cliente'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="marca_cliente'+cont+'['+cont+']" id="" value="NO">';
	  
	  cell8.innerHTML = 'YES <input type="radio" name="sin_cliente'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="sin_cliente'+cont+'['+cont+']" id="" value="NO">';	 

	  cont++;
	  }

	}	

</script>