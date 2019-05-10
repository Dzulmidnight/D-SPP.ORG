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


/************ VARIABLES DE CONTROL ******************/
//ESTATUS PUBLICO //////////////////////////

//1) Solicitud
//2) En proceso
//3) Evaluacion positiva
//4) Certificada
//5) No certificada

//ESTATUS INTERNO ///////////////////////////
//1) 1ra Evaluacion
//2) Completar informacion
//3) 2da revision
//4) Proceso interrumpido
//5) Evaluacion in situ
//6) Informe de evaluacion
//7) Acciones correctivas
//8) Dictamen positivo
//9) Dictamen negativo
//10) Certificada
//11) Certificado expirado
//12) Certificado por expirar
//13) Suspendida
//14) Cancelada
//15) Desactivacion
//16) Aviso de renovacion del certificado
//20) Renovación del certificado
$charset='utf-8';
$fecha = time();
$idopp = $_SESSION['idopp'];
$ruta_croquis = "../../archivos/oppArchivos/croquis/";
$spp_global = "cert@spp.coop";
$auxiliar = "acc@spp.coop";
$administrador = "yasser.midnight@gmail.com";
$fecha_registro = time();
setlocale(LC_ALL, 'en_US.UTF8');
/************ VARIABLES DE CONTROL ******************/



if(isset($_POST['insertar_solicitud']) && $_POST['insertar_solicitud'] == 1){
	$estatus_publico = 1; // EN REVISIÓN
	$estatus_interno = NULL;
	$estatus_dspp = 1; // SOLICITUD EN REVISIÓN
	$alcance_opp = "";


	/* INICIA CAPTURA ALCANCE DEL OPP */
	if(isset($_POST['produccion'])){
		$produccion = $_POST['produccion'];
	}else{
		$produccion = '';
	}
	if(isset($_POST['procesamiento'])){
		$procesamiento = $_POST['procesamiento'];
	}else{
		$procesamiento = '';
	}
	if(isset($_POST['comercializacion'])){
		$comercializacion = $_POST['comercializacion'];
	}else{
		$comercializacion = '';
	}

	/* TERMINA CAPTURA ALCANCE DEL OPP */

	if(isset($_POST['preg8'])){
		$preg8 = $_POST['preg8'];
	}else{
		$preg8 = "";
	}

	if(isset($_POST['preg9'])){
		if($_POST['preg9'] == 'mayor'){
			$preg9 = $_POST['preg9_1'];
		}else{
			$preg9 = $_POST['preg9'];
		}
	}else{
		$preg9 = "";
	}


	if(!empty($_FILES['preg11']['name'])){
	    $_FILES["preg11"]["name"];
	      move_uploaded_file($_FILES["preg11"]["tmp_name"], $ruta_croquis.date("Ymd H:i:s")."_".$_FILES["preg11"]["name"]);
	      $croquis = $ruta_croquis.basename(date("Ymd H:i:s")."_".$_FILES["preg11"]["name"]);
	}else{
		$croquis = NULL;
	}

	// INGRESAMOS LA INFORMACION A LA SOLICITUD DE CERTIFICACION
	$insertSQL = sprintf("INSERT INTO solicitud_colectiva (tipo_solicitud, idopp, idoc, contacto1_nombre, contacto2_nombre, contacto1_cargo, contacto2_cargo, contacto1_email, contacto2_email, contacto1_telefono, contacto2_telefono, adm1_nombre, adm2_nombre, adm1_email, adm2_email, adm1_telefono, adm2_telefono, total_miembros, produccion, procesamiento, comercializacion, preg2, preg3, preg4, preg5, preg6, preg8, preg9, preg10, preg11, responsable, fecha_registro, estatus_dspp) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
		   GetSQLValueString($_POST['tipo_solicitud'], "text"),
		   GetSQLValueString($idopp, "int"),
           GetSQLValueString($_POST['idoc'], "int"),
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
           GetSQLValueString($preg9, "text"),
           GetSQLValueString($_POST['preg10'], "text"),
           GetSQLValueString($croquis, "text"),
           GetSQLValueString($_POST['responsable'], "text"),
           GetSQLValueString($fecha_registro, "int"),
           GetSQLValueString($estatus_dspp, "int"));


		  $Result1 = mysql_query($insertSQL, $dspp) or die(mysql_error());
		 
		 $idsolicitud_colectiva = mysql_insert_id($dspp); 

	///INGRESAMOS EL TIPO DE SOLICITUD A LA TABLA OPP y EL ALCANCE DE LA OPP
	$updateSQL = sprintf("UPDATE opp SET produccion = %s, procesamiento = %s, comercializacion = %s, estatus_opp = %s WHERE idopp = %s",
		GetSQLValueString($produccion, "int"),
		GetSQLValueString($procesamiento, "int"),
		GetSQLValueString($comercializacion, "int"),
		GetSQLValueString($_POST['tipo_solicitud'], "int"),
		GetSQLValueString($idopp, "int"));
	$actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());

	// INGRESAMOS LOS CONTACTOS DE LA SOLICITUD A LA TABLA DE CONTACTOS
	if(!empty($_POST['contacto1_nombre'])){
		$insertSQL = sprintf("INSERT INTO contactos(idopp, nombre, cargo, telefono1, email1) VALUES (%s, %s, %s, %s, %s)",
			GetSQLValueString($idopp, "int"),
			GetSQLValueString($_POST['contacto1_nombre'], "text"),
			GetSQLValueString($_POST['contacto1_cargo'], "text"),
			GetSQLValueString($_POST['contacto1_telefono'], "text"),
			GetSQLValueString($_POST['contacto1_email'], "text"));
		$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

	}
	if(!empty($_POST['contacto2_nombre'])){
		$insertSQL = sprintf("INSERT INTO contactos(idopp, nombre, cargo, telefono1, email1) VALUES (%s, %s, %s, %s, %s)",
			GetSQLValueString($idopp, "int"),
			GetSQLValueString($_POST['contacto2_nombre'], "text"),
			GetSQLValueString($_POST['contacto2_cargo'], "text"),
			GetSQLValueString($_POST['contacto2_telefono'], "text"),
			GetSQLValueString($_POST['contacto2_email'], "text"));
		$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

	}
	if(!empty($_POST['adm1_nombre'])){
		$insertSQL = sprintf("INSERT INTO contactos(idopp, nombre, cargo, telefono1, email1) VALUES (%s, %s, %s, %s, %s)",
			GetSQLValueString($idopp, "int"),
			GetSQLValueString($_POST['adm1_nombre'], "text"),
			GetSQLValueString('ADMINISTRATIVO', "text"),
			GetSQLValueString($_POST['adm1_telefono'], "text"),
			GetSQLValueString($_POST['adm1_email'], "text"));
		$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

	}
	if(!empty($_POST['contacto2_nombre'])){
		$insertSQL = sprintf("INSERT INTO contactos(idopp, nombre, cargo, telefono1, email1) VALUES (%s, %s, %s, %s, %s)",
			GetSQLValueString($idopp, "int"),
			GetSQLValueString($_POST['contacto2_nombre'], "text"),
			GetSQLValueString('ADMINISTRATIVO', "text"),
			GetSQLValueString($_POST['contacto2_telefono'], "text"),
			GetSQLValueString($_POST['contacto2_email'], "text"));
		$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

	}

	// INGRESAMOS EL NUMERO DE SOCIOS A LA TABLA NUM_SOCIOS
	/*08_06_2017 if(isset($_POST['resp1'])){
		if($_POST['tipo_solicitud'] == "NUEVA"){ //si es nueva se inserta un registro de numero de socios
			$insertSQL = sprintf("INSERT INTO num_socios (idopp, numero, fecha_registro) VALUES (%s, %s, %s)",
				GetSQLValueString($idopp, "int"),
				GetSQLValueString($_POST['resp1'], "text"),
				GetSQLValueString($fecha, "int"));
			$ejecutar = mysql_query($insertSQL,$dspp) or die(mysql_error());
		}else{ //si es renovacion, se actualiza el registro de numero de socios
			$updateSQL = sprintf("UPDATE num_socios SET numero = %s, fecha_registro = %s WHERE idopp = %s",
				GetSQLValueString($_POST['resp1'], "text"),
				GetSQLValueString($fecha, "int"), 
				GetSQLValueString($idopp, "int"));
			$actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());
		}
	} 08_06_2017*/


		 // INGRESAMOS EL PORCENTAJE DE VENTA DE LOS PRODUCTOS

		 	if(!empty($_POST['organico']) || !empty($_POST['comercio_justo']) || !empty($_POST['spp']) || !empty($_POST['sin_certificado'])){
		 		$insertSQL = sprintf("INSERT INTO porcentaje_productoVentas (organico, comercio_justo, spp, sin_certificado, idsolicitud_colectiva, idopp) VALUES (%s, %s, %s, %s, %s, %s)",
		 			GetSQLValueString($_POST['organico'], "text"),
		 			GetSQLValueString($_POST['comercio_justo'], "text"),
		 			GetSQLValueString($_POST['spp'], "text"),
		 			GetSQLValueString($_POST['sin_certificado'], "text"),
		 			GetSQLValueString($idsolicitud_colectiva, "int"),
		 			GetSQLValueString($idopp, "int"));
		 		$insertar = mysql_query($insertSQL,$dspp) or die(mysql_error());
		 	}


		/*************************** INICIA INSERTAR PROCESO DE CERTIFICACIÓN ***************************/
		$insertSQL = sprintf("INSERT INTO proceso_certificacion (idsolicitud_colectiva, estatus_publico, estatus_interno, estatus_dspp, fecha_registro) VALUES (%s, %s, %s, %s, %s)",
			GetSQLValueString($idsolicitud_colectiva, "int"),
			GetSQLValueString($estatus_publico, "int"),
			GetSQLValueString($estatus_interno, "int"),
			GetSQLValueString($estatus_dspp, "int"),
			GetSQLValueString($fecha, "int"));
		$insertar = mysql_query($insertSQL,$dspp) or die(mysql_error());
		/*************************** TERMINA INSERTAR PROCESO DE CERTIFICACIÓN ***************************/

		/*************************** INICIA INSERTAR SUB ORGANIZACIONES ***************************/
			//$spp_suborganizacion = 'PRUEBA';
			$pais_facilitador = $_POST['pais_facilitador'];
			if(isset($_POST['sub_nombre'])){
				$nombre = $_POST['sub_nombre'];
			}else{
				$nombre = NULL;
			}

			if(isset($_POST['unidad_produccion'])){
				$unidad_produccion = $_POST['unidad_produccion'];
			}else{
				$unidad_produccion = NULL;
			}
			if(isset($_POST['sub_producto'])){
				$sub_producto = $_POST['sub_producto'];
			}else{
				$sub_producto = NULL;
			}
			if(isset($_POST['num_productores'])){
				$num_productores = $_POST['num_productores'];
			}else{
				$num_productores = NULL;
			}
			if(isset($_POST['sub_incumplimientos'])){
				$incumplimientos = $_POST['sub_incumplimientos'];
			}else{
				$incumplimientos = NULL;
			}
			if(isset($_POST['sub_certificaciones'])){
				$certificaciones = $_POST['sub_certificaciones'];
			}else{
				$certificaciones = NULL;
			}
			if(isset($_POST['sub_certificadora'])){
				$certificadora = $_POST['sub_certificadora'];
			}else{
				$certificadora = NULL;
			}
			if(isset($_POST['sub_anio_certificacion'])){
				$anio_inicial = $_POST['sub_anio_certificacion'];
			}else{
				$anio_inicial = NULL;
			}
			if(isset($_POST['sub_interrumpido'])){
				$interrumpida = $_POST['sub_interrumpido'];
			}else{
				$interrumpida = NULL;
			}

						$contador = 1;

			for($i=0;$i<count($nombre);$i++){
				if($nombre[$i] != NULL){
						$contador++;
					$row_opp = mysql_query("SELECT idopp, spp, pais FROM opp WHERE pais = '$pais_facilitador'",$dspp) or die(mysql_error());
					//$datos_opp = mysql_fetch_assoc($ejecutar);
					//$fecha = $_POST['fecha_inclusion'];


						$charset='utf-8'; // o 'UTF-8'
						$str = iconv($charset, 'ASCII//TRANSLIT', $pais_facilitador);
						$pais_facilitador = preg_replace("/[^a-zA-Z0-9]/", '', $str);

						$pais_facilitadorDigitos = strtoupper(substr($pais_facilitador, 0, 3));
						$formatoFecha = date("d/m/Y", $fecha);
						$fechaDigitos = substr($formatoFecha, -2);

						$contador = str_pad($contador, 3, "0", STR_PAD_LEFT);
						//$numero =  strlen($contador);

						$spp_suborganizacion = "OPP-".$pais_facilitadorDigitos."-".$fechaDigitos."-".$contador;

						while($datos_opp = mysql_fetch_assoc($row_opp)) {
						  if($datos_opp['spp'] == $spp_suborganizacion){
						    //echo "<b style='color:red'>es igual el OPP con id: $datos_opp[idf]</b><br>";
						    $contador++;
						    $contador = str_pad($contador, 3, "0", STR_PAD_LEFT);
						    $spp_suborganizacion = "OPP-".$pais_facilitadorDigitos."-".$fechaDigitos."-".$contador;
						  }/*else{
						    echo "el id encontrado es: $datos_opp[idf]<br>";
						  }*/
						  
						}


					#for($i=0;$i<count($certificacion);$i++){
					$insertSQL = sprintf("INSERT INTO sub_organizaciones (spp, nombre, pais, productos, unidad_produccion, num_productores, incumplimientos, certificaciones, certificadora, anio_inicial, interrumpida, idsolicitud_colectiva) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
						GetSQLValueString($spp_suborganizacion, "text"),
					    GetSQLValueString(strtoupper($nombre[$i]), "text"),
					    GetSQLValueString(strtoupper($pais_facilitador), "text"),
					    GetSQLValueString(strtoupper($sub_producto[$i]), "text"),
					    GetSQLValueString(strtoupper($unidad_produccion[$i]), "text"),
					    GetSQLValueString(strtoupper($num_productores[$i]), "text"),
					    GetSQLValueString(strtoupper($incumplimientos[$i]), "text"),
					    GetSQLValueString(strtoupper($certificaciones[$i]), "text"),
					    GetSQLValueString(strtoupper($certificadora[$i]), "text"),
					    GetSQLValueString($anio_inicial[$i], "text"),
					    GetSQLValueString(strtoupper($interrumpida[$i]), "text"),
					    GetSQLValueString($idsolicitud_colectiva, "int"));
					$Result = mysql_query($insertSQL, $dspp) or die(mysql_error());
					#}
				}
			}
		/*************************** INICIA INSERTAR SUB ORGANIZACIONES ***************************/

		/*************************** INICIA INSERTAR CERTIFICACIONES ***************************/
			if(isset($_POST['certificacion'])){
				$certificacion = $_POST['certificacion'];
			}else{
				$certificacion = NULL;
			}


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

			for($i=0;$i<count($certificacion);$i++){
				if($certificacion[$i] != NULL){
					#for($i=0;$i<count($certificacion);$i++){
					$insertSQL = sprintf("INSERT INTO certificaciones (idsolicitud_colectiva, certificacion, certificadora, ano_inicial, interrumpida) VALUES (%s, %s, %s, %s, %s)",
					    GetSQLValueString($idsolicitud_colectiva, "int"),
					    GetSQLValueString(strtoupper($certificacion[$i]), "text"),
					    GetSQLValueString(strtoupper($certificadora[$i]), "text"),
					    GetSQLValueString($ano_inicial[$i], "text"),
					    GetSQLValueString($interrumpida[$i], "text"));

					$Result = mysql_query($insertSQL, $dspp) or die(mysql_error());
					#}
				}
			}
		/*************************** INICIA INSERTAR CERTIFICACIONES ***************************/


		/*************************** INICIA INSERTAR PRODUCTOS ***************************/
		$producto_general = $_POST['producto_general'];
		$producto = $_POST['producto'];
		$volumen = $_POST['volumen'];
		$materia = $_POST['materia'];
		$destino = $_POST['destino'];
		/*$marca_propia = $_POST['marca_propia'];
		$marca_cliente = $_POST['marca_cliente'];
		$sin_cliente = $_POST['sin_cliente'];*/

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

					$str = iconv($charset, 'ASCII//TRANSLIT', $producto_general[$i]);
					$producto_general[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));

					$str = iconv($charset, 'ASCII//TRANSLIT', $producto[$i]);
					$producto[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));

					$str = iconv($charset, 'ASCII//TRANSLIT', $destino[$i]);
					$destino[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));

					$str = iconv($charset, 'ASCII//TRANSLIT', $materia[$i]);
					$materia[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));


				    $insertSQL = sprintf("INSERT INTO productos (idopp, idsolicitud_colectiva, producto_general, producto, volumen, terminado, materia, destino, marca_propia, marca_cliente, sin_cliente) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
				    	GetSQLValueString($idopp, "int"),
				        GetSQLValueString($idsolicitud_colectiva, "int"),
				        GetSQLValueString($producto_general[$i], "text"),
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

		///// INICIA ENVIO DEL MENSAJE POR CORREO AL OC y a SPP GLOBAL
		$asunto = "D-SPP Solicitud de Certificación Colectiva para Organizaciones de Pequeños Productores";
		$row_oc = mysql_query("SELECT * FROM oc WHERE idoc = $_POST[idoc]", $dspp) or die(mysql_error());
		$oc = mysql_fetch_assoc($row_oc);

		$cuerpo_correo = '
			<html>
			<head>
				<meta charset="utf-8">
			</head>
			<body>
			
		        <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
		          <tbody>
		                <tr>
		                  <th rowspan="7" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
		                  <th scope="col" align="left" width="280"><strong>Solicitud de Certificación Colectiva para Organizaciones de Pequeños Productores / Application for Collective Certification for Small Producers Organizations </strong></th>
		                </tr>
		                <tr>
		                  <td style="padding-top:10px;">
		       
		                    Para poder consultar la solicitud, por favor iniciar sesión en su cuenta de OC(Organismo de Certificación) en el siguiente enlace: <a href="http://d-spp.org" target="_new">www.d-spp.org</a>
		                  <br>
		                    In order to consult the application, please open a session in your  Certification Entity (CE) account at the following link: <a href="http://d-spp.org" target="_new">www.d-spp.org</a>
		                  </td>
		                </tr>
		            <tr>
		              <td align="left">Teléfono / phone Organización: '.$_POST['telefono'].'</td>
		            </tr>

		            <tr>
		              <td align="left">'.$_POST['pais'].'</td>
		            </tr>
		            <tr>
		              <td align="left" style="color:#ff738a;">Email: '.$_POST['email'].'</td>
		            </tr>
		            <tr>
		              <td align="left" style="color:#ff738a;">Email: '.$_POST['contacto1_email'].'</td>
		            </tr>

		            <tr>
		              <td colspan="2">
		                <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;" border="1" width="650px">
		                  <tbody>
		                    <tr style="font-size: 12px; text-align:center; background-color:#dff0d8; color:#3c763d;" height="50px;">
		                      <td width="130px">Nombre de la organización/Organization name</td>
		                      <td width="130px">País / Country</td>
		                      <td width="130px">Organismo de Certificación / Certification Entity</td>
		                   
		                      <td width="130px">Fecha de solicitud/Date of application</td>
		                    </tr>
		                    <tr style="font-size: 12px;">
		                      <td style="padding:10px;">
		                        '.$_POST['nombre_facilitador'].'
		                      </td>
		                      <td style="padding:10px;">
		                        '.$_POST['pais'].'
		                      </td>
		                      <td style="padding:10px;">
		                        '.$oc['nombre'].'
		                      </td>
		                      <td style="padding:10px;">
		                      '.date('d/m/Y', $fecha).'
		                      </td>
		                    </tr>

		                  </tbody>
		                </table>  
		              </td>
		            </tr>
		            <tr>
		              <td colspan="2">
		                <span style="color:red">¿Qué es lo de debo realizar ahora?. Debes revisar la solicitud y cargar una cotización</span>
		                <ol>
		                  <li>Debes iniciar sesión dentro del sistema <a href="http://d-spp.org/">D-SPP (clic aquí)</a> como Organismo de Certificación(OC).</li>
		                  <li>Dentro de tu cuenta debes seleccionar Solicitudes > Solicitudes OPP.</li>
		                  <li>Dentro de la tabla solicitudes debes localizar la columna "Acciones" Y seleccionar el boton Azul Consultar</li>
		                  <li>Para poder enviar la cotización debes seleccionar el "Procedimiento de Certificación" y cargar la cotización</li>
		                </ol>
		              </td>
		            </tr> 
		            <tr>
		              <td colspan="2">
		                <span style="color:red">What should I do now? You should review the application and upload a price quote.</span>
		                <ol>
		                  <li>•	You should open a session in the D-SPP system as a Certification Entity (CE).</li>
		                  <li>•	Within your account, you should select ApplicationsSolicitudes > SPO Applications Solicitudes OPP</li>
		                  <li>•	In the applications table, you should locate the column entitled “Actions” and select the Blue Consult button.  </li>
		                  <li>•	To send your price quote, you should select “Certification Procedure” and upload your price quote.</li>
		                </ol>
		              </td>
		            </tr> 


				  </tbody>
				</table>

			</body>
			</html>
		';
		///// TERMINA ENVIO DEL MENSAJE POR CORREO AL OC y a SPP GLOBAL
		$destinatario = $oc['email1'];
		if(isset($oc['email1'])){
			//$mail->AddAddress($oc['email1']);

			$token = strtok($oc['email1'], "\/\,\;");
			while ($token !== false)
			{
				$mail->AddAddress($token);
				$token = strtok('\/\,\;');
			}

		}
		$destinatario = $oc['email2'];
		if(isset($oc['email2'])){
			//$mail->AddAddress($oc['email2']);
			$token = strtok($oc['email2'], "\/\,\;");
			while ($token !== false)
			{
				$mail->AddAddress($token);
				$token = strtok('\/\,\;');
			}

		}
		if(isset($_POST['email'])){
			//$mail->AddCC($_POST['email']);
			$token = strtok($_POST['email'], "\/\,\;");
			while ($token !== false)
			{
				$mail->AddCC($token);
				$token = strtok('\/\,\;');
			}

		}
		if(isset($_POST['contacto1_email'])){
			//$mail->AddCC($_POST['contacto1_email']);
			$token = strtok($_POST['contacto1_email'], "\/\,\;");
			while ($token !== false)
			{
				$mail->AddCC($token);
				$token = strtok('\/\,\;');
			}

		}

	    $mail->AddCC($administrador);
	    $mail->AddBCC($auxiliar);
	    $mail->AddBCC($spp_global);
        //$mail->Username = "soporte@d-spp.org";
        //$mail->Password = "/aung5l6tZ";
        $mail->Subject = utf8_decode($asunto);
        $mail->Body = utf8_decode($cuerpo_correo);
        $mail->MsgHTML(utf8_decode($cuerpo_correo));
        $mail->Send();
        $mail->ClearAddresses();

 		$mensaje = "The Application has been sent to the OC, soon you will be contacted";


}

  //$insertGoTo = "main_menu.php?SOLICITUD&add&mensaje=Solicitud agregada correctamente, se ha notificado al OC por email.";
$query = "SELECT * FROM opp WHERE idopp = $idopp";
$row_opp = mysql_query($query,$dspp) or die(mysql_error());
$opp = mysql_fetch_assoc($row_opp);

?>
<div class="row" style="font-size:12px;">
	<?php 
	if(isset($mensaje)){
	?>
	<div class="col-md-12 alert alert-success alert-dismissible" role="alert">
	  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	  <?php echo $mensaje; ?>
	</div>
	<?php
	}
	?>

	<form action="" name="" method="POST" enctype="multipart/form-data">
		<fieldset>
			<div class="col-md-12 alert alert-primary" style="padding:7px;">
				<h3 class="text-center">Application for Collective Certification SPO</h3>
			</div>
			<div class="col-md-12 well text-justify">
				<p class="text-center" style="color:red"><b>Important Information about the Guidelines for the Collective Certification:</b></p>
				<p><b>Scope:</b></p>
				<p>
					1.	The Collective Certification Procedures apply to first-level Organizations that are members of a higher level Small Producers’ Organization that applies for Certification, based on the General Standard for the Small Producers’ Symbol, through the Small Producers’ Organization of a higher level. 
				</p>  

				<p>
					2.	The high-level Small Producers’ Organization does not acquire certification. If the high-level Small Producers’ Organization is the organization that is commercializing products under the Small Producers’ Symbol, it should become registered as an Intermediary (INT) or Collective Trading Company owned by Small Producers’ Organizations (C-OPP). 
				</p>      

				<p>
					<b>Requirements:</b><br>
					i.	The high-level Small Producers’ Organization (SPO) should work to facilitate and promote the certification process for its members and should provide all the necessary information based on their internal control system. 
				</p>
				<p>
					ii.	The high-level SPO should complete the SPP Evaluation Form as a form of self-assessment in line with the information from each of the first-level SPOs involved.
				</p>
				<p>
					iii.	The high-level SPO should send the documentation specified in the Evaluation Form as support documentation, as well as the information requested by the Certification Entity (CE). 
				</p> 


			</div>
			<div class="col-lg-12 alert alert-info" style="padding:7px;">
				<div class="col-md-12 alert alert-warning" style="padding:5px;">
					<ul>
						<li>
							<b>
								IF YOU HAD CERTIFICATION PREVIOUSLY SPP (WITH CURRENT CERTIFICATION ENTITY OR OTHER CERTIFICATION ENTITY) TO CHOOSE <span style="color:red">""RENEWAL OF CERTIFICATE"</span>
							</b>.
						</li>
						<li><b>IF THE FIRST TIME YOU CHOOSE TO CERTIFY: <span style="color:red">"FIRST TIME"</span></b></li>
					</ul>	 
				</div>

				<div class="col-md-6">
					<div class="col-xs-12">
						<b>Send to Certification Entity:</b>
					</div>
					<div class="col-xs-12">
						<select class="form-control" name="idoc" id="" required>
							<option value="">Choose one</option>
							<?php 
							$query = "SELECT idoc, abreviacion FROM oc";
							$row_oc = mysql_query($query,$dspp) or die(mysql_error());

							while($oc = mysql_fetch_assoc($row_oc)){
							?>
							<option value="<?php echo $oc['idoc']; ?>" <?php if($opp['idoc'] == $oc['idoc']){ echo "selected"; } ?>><?php echo $oc['abreviacion']; ?></option>
							<?php
							}
							 ?>
							 <option value="TODOS">Send all</option>
						</select>
					</div>
				</div>
				<div class="col-md-6">
					<div class="col-xs-12">
						<p class="text-center"><strong>SELECT TYPE APPLICATION</strong></p>
					</div>
					<div class="col-md-6">
						<label for="nueva">FIRST TIME</label>
						<input type="radio" class="form-control" id="nueva" name="tipo_solicitud" value="NUEVA">
					</div>
					<div class="col-md-6">
						<label for="renovacion">RENEWAL OF CERTIFICATE</label>
						<input type="radio" class="form-control" id="renovacion" name="tipo_solicitud" value="RENOVACION">
					</div>
				</div>
			</div>

			<div class="col-md-12 text-center alert alert-success" style="padding:7px;"><b>FACILITATING SPO´S( <a data-toggle="tooltip" title="Facilitating SPO: Second or higher-level Small Producers´ Organization representing its member organizations in their collective certification process" href="#"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span></a> ) GENERAL INFORMATION ( <a data-toggle="tooltip" title="General information corresponding to the Facilitating Small Producers´ Organization submitting the application will be published by SPP Global" href="#"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span></a> )</b></div>
			<!------ INICIA INFORMACION GENERAL Y DATOS FISCALES ------>
			<div class="col-lg-12">
				<div class="col-md-6">
					<div class="col-md-12 text-center alert alert-warning" style="padding:7px;">GENERAL INFORMATION</div>
					<label for="fecha_elaboracion">DATE OF ELABORATION</label>
					<input type="text" class="form-control" id="fecha_elaboracion" name="fecha_elaboracion" value="<?php echo date('Y-m-d', time()); ?>" readonly>	

					<label for="spp">SPP IDENTIFICATION CODE(#SPP): </label>
					<input type="text" class="form-control" id="spp" name="spp" value="<?php echo $opp['spp']; ?>" readonly>

					<label for="nombre_facilitador" style="color:red">COMPLETE NAME OF THE FACILITATING ORGANIZATION </label>
					<textarea name="nombre_facilitador" id="nombre_facilitador" class="form-control"><?php echo $opp['nombre']; ?></textarea>


					<label for="pais">COUNTRY:</label>
					<?php 
					$row_pais = mysql_query("SELECT * FROM paises",$dspp) or die(mysql_error());
					 ?>
					 <select name="pais" id="pais" class="form-control">
					 	<option value="">Select a Country</option>
					 	<?php 
					 	while($pais = mysql_fetch_assoc($row_pais)){
					 		if(utf8_encode($pais['nombre']) == $opp['pais']){
					 			echo "<option value='".utf8_encode($pais['nombre'])."' selected>".utf8_encode($pais['nombre'])."</option>";
					 		}else{
					 			echo "<option value='".utf8_encode($pais['nombre'])."'>".utf8_encode($pais['nombre'])."</option>";
					 		}
					 	}
					 	 ?>
					 </select>
					 <input type="hidden" name="pais_facilitador" value="<?php echo $opp['pais']; ?>">

					<label for="direccion_oficina">COMPLETE ADDRESS FOR THE FACILITATING ORGANIZATION (STREET, DISTRICT, TOWN/CITY, REGION):</label>
					<textarea name="direccion_oficina" id="direccion_oficina"  class="form-control"><?php echo $opp['direccion_oficina']; ?></textarea>

					<label for="email">ORGANIZATION´S EMAIL ADDRESS:</label>
					<input type="text" class="form-control" id="email" name="email" value="<?php echo $opp['email']; ?>">

					<label for="email">ORGANIZATION’S TELEPHONES(COUNTRY CODE+AREA CODE+NUMBER):</label>
					<input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo $opp['telefono']; ?>">	

					<label for="sitio_web">WEBSITE:</label>
					<input type="text" class="form-control" id="sitio_web" name="sitio_web" value="<?php echo $opp['sitio_web']; ?>">

				</div>

				<div class="col-md-6">
					<div class="col-md-12 text-center alert alert-warning" style="padding:7px;">DATA FOR INVOICING</div>

					<label for="razon_social">BUSINESS NAME</label>
					<input type="text" class="form-control" id="razon_social" name="razon_social" value="<?php echo $opp['razon_social']; ?>">

					<label for="direccion_fiscal">FISCAL ADDRESS</label>
					<textarea class="form-control" name="direccion_fiscal" id="direccion_fiscal"><?php echo $opp['direccion_fiscal']; ?></textarea>

					<label for="rfc">RFC</label>
					<input type="text" class="form-control" id="rfc" name="rfc" value="<?php echo $opp['rfc']; ?>">

					<label for="ruc">RUC</label>
					<input type="text" class="form-control" id="ruc" name="ruc" value="<?php echo $opp['ruc']; ?>">
				</div>
			</div>
			<!------ INICIA INFORMACION GENERAL Y DATOS FISCALES ------>


			<!------ INICIA INFORMACION CONTACTOS Y AREA ADMINISTRATIVA ------>
			<div class="col-lg-12">
				<div class="col-md-6">
					<div class="col-md-12 text-center alert alert-warning" style="padding:7px;">CONTACT PERSON(S) OF APPLICATION</div>

					<label for="persona1">CONTACT PERSON(S)</label>
					<input type="text" class="form-control" id="persona1" name="contacto1_nombre" placeholder="* Name person 1" required>
					<input type="text" class="form-control" id="" name="contacto2_nombre" placeholder="Name Person 2">

					<label for="cargo">POSITION(S)</label>
					<input type="text" class="form-control" id="cargo" name="contacto1_cargo" placeholder="* Position Person 1" required>
					<input type="text" class="form-control" id="" name="contacto2_cargo" placeholder="Position Person 2">

					<label for="email">EMAIL ADDRESS FROM THE CONTACT PERSON(S)</label>
					<input type="email" class="form-control" id="email" name="contacto1_email" placeholder="* Email Person 1" required>
					<input type="email" class="form-control" id="" name="contacto2_email" placeholder="Email Person 2">

					<label for="telefono">TELEPHONE(S) FOR CONTACT PERSON(S)</label>
					<input type="text" class="form-control" id="telefono" name="contacto1_telefono" placeholder="* Telephone person 1" required>
					<input type="text" class="form-control" id="" name="contacto2_telefono" placeholder="Telephone person 2">

				</div>

				<div class="col-md-6">
					<div class="col-md-12 text-center alert alert-warning" style="padding:7px;">PERSON(S) OF THE ADMINISTRATIVE AREA</div>

					<label for="persona_adm">PERSON(S) OF THE ADMINISTRATIVE AREA</label>
					<input type="text" class="form-control" id="persona_adm" name="adm1_nombre" placeholder="Name person 1">
					<input type="text" class="form-control" id="" name="adm2_nombre" placeholder="Name person 2">

					<label for="email_adm">EMAIL</label>
					<input type="email" class="form-control" id="email_adm" name="adm1_email" placeholder="Email Person 1">
					<input type="email" class="form-control" id="" name="adm2_email" placeholder="Email Person 2">

					<label for="telefono_adm">TELEPHONE(S) PERSON(S) ADMINISTRATIVE AREA</label>
					<input type="text" class="form-control" id="telefono_adm" name="adm1_telefono" placeholder="Telephone person 1">
					<input type="text" class="form-control" id="" name="adm2_telefono" placeholder="Telephone person 2">
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

							<th rowspan="2">Complete Name</th>
							<th rowspan="2">SPP Identification Code</th>
							<th rowspan="2">Number of Producer Members</th>
							<th rowspan="2">Name of products to be included in the collective certification</th>
							<th rowspan="2">Maximum Size of the Unit Production <span style="background:yellow">by Producer</span> of the product(s) to include in the collective certification (unit of measure):</th>
							<th colspan="4">Fill out the table according your certifications, (example: EU, NOP, JAS, FLO, etc.)</th>

							<th rowspan="2">According the certifications, in its most recent internal and external evaluations, how many cases of non compliance were identified? Please explain if they have been resolved or what their status is?</th>

						</tr>
						<tr>
							<th>Certification</th>
							<th>Certification Entity</th>
							<th>Initial Year of Certification</th>
							<th>Has been interrupted?</th>
						</tr>

					</thead>
					<tbody>
						<tr>	
							<td>
								1
							</td>
							<td>
								<input type="text" class="form-control" style="width:150px;" name="sub_nombre[0]" placeholder="Complete name">
							</td>
							<td>
								Generated by the system
							</td>
							<td>
								<input type="number" class="form-control" style="width:120px;" name="num_productores[0]" placeholder="Only numbers">
							</td>
							<td>
								<textarea class="form-control" style="width:200px;" name="sub_producto[0]" id="" rows="3" placeholder="Products"></textarea>
							</td>
							<td>
								<input type="text" class="form-control" style="width:150px;" name="unidad_produccion[0]" placeholder="Unit production">
							</td>
							<td>
                                <textarea class="form-control" style="width:150px;" name="sub_certificaciones[0]" placeholder="Certification(s)" rows="3" required></textarea>
							</td>
							<td>
                                <textarea class="form-control" style="width:150px;" name="sub_certificadora[0]" placeholder="Certification Entity" rows="3" required></textarea>
							</td>
							<td>
                                <textarea class="form-control" style="width:150px;" name="sub_anio_certificacion[0]" placeholder="Initial Year" rows="3" required></textarea>
							</td>
							<td>
								YES <input type="radio"  name="sub_interrumpido[0]" id="" value="SI"><br>
								NO <input type="radio"  name="sub_interrumpido[0]" id="" value="NO" >
							</td>
							<td>
								<textarea class="form-control" style="width:200px;" name="sub_incumplimientos[0]" id="" rows="3"></textarea>
							</td>

						</tr>
						<tr>
							<td colspan="11">
								<h6>Information provided in this section will be handled with complete confidentiality. Please insert additional lines if necessary. </h6>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<!--- TERMINA TABLA SOBRE LA INFORMACION DE LAS ORGANIZACIONES INVOLUCRADAS -->

			<!------ INICIA INFORMACION DATOS DE OPERACIÓN ------>
		
			<div class="col-lg-6" style="background:#2ecc71">
					<label for="total_miembros">TOTAL MEMBERS INCLUDED IN THE COLLECTIVE CERTIFICATION:</label>
					<input type="number" class="form-control" id="total_miembros" name="total_miembros" placeholder="just number" required>
			</div>

			<div class="col-md-12 text-center alert alert-success" style="margin-top:5em;">INFORMATION ON OPERATION FOR SPP TRANSACTIONS</div>

			<div class="col-lg-12">
				<div class="col-md-12">

					<div >
						<label for="alcance_opp">
							1. MARK WITH AN 'X' THE SCOPE OF THE SMALL PRODUCERS` ORGANIZATIONS INCLUDED IN THE COLLECTIVE CERTIFICATION:
						</label>
					</div>
					<div class="col-xs-4">
						<label>PRODUCTION</label>
						<input type="checkbox" name="produccion" class="form-control" value="1">
					</div>
					<div class="col-xs-4">
						<label>PROCESSING</label>
						<input type="checkbox" name="procesamiento" class="form-control" value="1">
					</div>
					<div class="col-xs-4">
						<label>TRAIDING</label>
						<input type="checkbox" name="comercializacion" class="form-control" value="1">
					</div>


					<label for="preg2">
						2. SPECIFY THE NAME OF THE ENTITY THAT CARRY OUT THE IMPORT OR EXPORT FOR SPP TRANSACTIONS.
					</label>
					<input type="text" class="form-control" id="preg2" name="preg2" >


					<label for="preg3">
						3.	SPECIFY IF YOU SUBCONTRACT THE SERVICES OF PROCESSING PLANTS, TRADING COMPANIES OR COMPANIES THAT CARRY OUT THE IMPORT OR EXPORT, IF THE ANSWER IS AFFIRMATIVE, MENTION THE NAME AND THE SERVICE THAT PERFORMS.
					</label>
					<textarea name="preg3" id="preg3" class="form-control"></textarea>

					<label for="preg4">
						4.	IN ADDITION TO THE MAIN OFFICES (OF THE FACILITATING SPO), PLEASE SPECIFY HOW MANY COLLECTION CENTERS, PROCESSING AREAS AND ADDITIONAL OFFICES YOU HAVE.
					</label>
					<textarea class="form-control" name="preg4" id="" rows="3"></textarea>

					<label for="preg5">
						5.	IF YOU SUBCONTRACT THE SERVICES OF PROCESSING PLANTS, TRADING COMPANIES OR COMPANIES THAT CARRY OUT THE IMPORT OR EXPORT, INDICATE WHETHER THESE COMPANIES ARE GOING TO APPLY FOR THE REGISTRATION UNDER SPP CERTIFICATION PROGRAM. <sup><a data-toggle="tooltip" title="Review the General Application Guidelines to the SPP System" href="#">4</a></sup>
						<br>
						<small><sup>5</sup>  Review the General Application Guidelines to the SPP System.</small>
					</label>
					<textarea name="preg5" id="preg5" class="form-control"></textarea>

					<label for="preg6">
						6.	IF THE ORGANIZATION HAS AN INTERNAL CONTROL SYSTEM FOR COMPLYING WITH THE CRITERIA IN THE GENERAL STANDARD OF THE SMALL PRODUCERS´ SYMBOL, PLEASE EXPLAIN HOW IT WORKS.
					</label>
					<textarea name="preg6" id="preg6" class="form-control"></textarea>

					<p for="preg7">
						<b>7.	OF THE APPLICANT’S TOTAL TRADING DURING THE PREVIOUS CYCLE, WHAT PERCENTAGE WAS CONDUCTED UNDER THE SCHEMES OF CERTIFICATION FOR ORGANIC, FAIR TRADE AND/OR THE SMALL PRODUCERS’ SYMBOL?</b>
						<i>(* Enter only quantity, integer or decimal)</i>
						<div class="col-lg-12">
							<div class="row">
								<div class="col-xs-3">
									<label for="organico">% ORGANIC</label>
									<input type="number" step="any" class="form-control" id="organico" name="organico" placeholder="Ej: 0.0">
								</div>
								<div class="col-xs-3">
									<label for="comercio_justo">% FAIR TRADE</label>
									<input type="number" step="any" class="form-control" id="comercio_justo" name="comercio_justo" placeholder="Ej: 0.0">
								</div>
								<div class="col-xs-3">
									<label for="spp">SMALL PRODUCERS' SYMBOL</label>
									<input type="number" step="any" class="form-control" id="spp" name="spp" placeholder="Ej: 0.0">
								</div>
								<div class="col-xs-3">
									<label for="otro">WITHOUT CERTIFICATE</label>
									<input type="number" step="any" class="form-control" id="otro" name="sin_certificado" placeholder="Ej: 0.0">
								</div>
							</div>
						</div>
					</p>

	

					<p><b>8. DID YOU HAVE SPP PURCHASES DURING THE PREVIOUS CERTIFICATION CYCLE?</b></p>
						<div class="col-xs-6">
							YES <input type="radio" class="form-control" name="preg8" onclick="mostrar_ventas()" id="preg8" value="SI">
						</div>
						<div class="col-xs-6">
							NO <input type="radio" class="form-control" name="preg8" onclick="ocultar_ventas()" id="preg8" value="NO">
						</div>			

					<p>
						<b>9. IF YOUR RESPONSE WAS POSITIVE, PLEASE MARK THE RANGE OF THE TOTAL VALUE SPP FROM THE PREVIOUS CYCLE ACCORDING TO THE FOLLOWING TABLE:</b>
					</p>

					<div class="well col-xs-12">
						<div class="col-xs-6"><p>LESS THAN $3,000 USD</p></div>
						<div class="col-xs-6 "><input type="radio" name="preg9" class="form-control" id="ver" onclick="ocultar()" value="HASTA $3,000 USD"></div>
					
						<div class="col-xs-6"><p>BETWEENN $3,000 AND $10,000 USD</p></div>
						<div class="col-xs-6"><input type="radio" name="preg9" class="form-control" id="ver" onclick="ocultar()" value="ENTRE $3,000 Y $10,000 USD"></div>
					
						<div class="col-xs-6"><p>BEETWENN $10,000 AND $25,000 USD</p></div>
						<div class="col-xs-6"><input type="radio" name="preg9" class="form-control"  id="ver" onclick="ocultar()" value="ENTRE $10,000 A $25,000 USD"></div>
					
						<div class="col-xs-6"><p>MORE THAN $25,000 USD<sup>*</sup><br><h6><sup>*</sup>SPECIFY THE QUANTITY</h6></p></div>
						<div class="col-xs-6"><input type="radio" name="preg9" class="form-control" id="exampleInputEmail1" onclick="mostrar()" value="mayor">
							<input type="text" name="preg9_1" class="form-control" id="oculto" style='display:none;' placeholder="Specify Amount">
						</div>

					</div>
							
					<label for="preg10">
						10.	ESTIMATED DATE FOR BEGINNING TO USE THE SMALL PRODUCERS’ SYMBOL:
					</label>
					<input type="text" class="form-control" id="preg10" name="preg10">

					<label for="preg11">
						11.	PLEASE ATTACH A GENERAL MAP OF THE AREA WHERE YOUR SPO OPERATES, INDICATING THE ZONES WHERE MEMBERS ARE LOCATED.
					</label>
					<input type="file" class="form-control" id="preg11" name="preg11">
				</div>
			</div>

			<!------ FIN INFORMACION DATOS DE OPERACIÓN ------>

			<div class="col-md-12 text-center alert alert-success" style="margin-top:5em;"><b>INFORMATION ON PRODUCTS FOR WHICH APPLICATION WISHES TO USE SYMBOL<sup>6</sup></b></div>
			<div class="col-lg-12">
				<table class="table table-bordered" id="tablaProductos">
					<tr>
						<td><b>General product</b> (ej: coffee, cocoa, honey, ...)</td>
						<td><b>Specific product</b> (ej: green coffee, Cocoa powder, honey bee)</td>
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
					<tr>
						<td>
							<input type="text" class="form-control" name="producto_general[0]" id="exampleInputEmail1" placeholder="General product">
						</td>
						<td>
							<input type="text" class="form-control" name="producto[0]" id="exampleInputEmail1" placeholder="Specific product">
						</td>
						<td>
							<input type="text" class="form-control" name="volumen[0]" id="exampleInputEmail1" placeholder="Volume">
						</td>
						<td>
							YES <input type="radio"  name="terminado[0]" id="" value="SI"><br>
							NO <input type="radio"  name="terminado[0]" id="" value="NO" >
						</td>
						<td>
							<input type="text" class="form-control" name="materia[0]" id="exampleInputEmail1" placeholder="Material">
						</td>
						<td>
							<input type="text" class="form-control" name="destino[0]" id="exampleInputEmail1" placeholder="Destination">
						</td>
						<td>
							YES <input type="radio"  name="marca_propia[0]" id="" value="SI"><br>
							NO <input type="radio"  name="marca_propia[0]" id="" value="NO" >
						</td>
						<td>
							YES <input type="radio"  name="marca_cliente[0]" id="" value="SI"><br>
							NO <input type="radio"  name="marca_cliente[0]" id="" value="NO">
						</td>
						<td>
							YES <input type="radio"  name="sin_cliente[0]" id="" value="SI"><br>
							NO <input type="radio"  name="sin_cliente[0]" id="" value="NO">
						</td>
					</tr>				
					<tr>
						<td colspan="9">
							<h6><sup>6</sup> Information provided in this section will be handled with complete confidentiality. Please insert additional lines if necessary. </h6>
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
				<input type="text" class="form-control" id="responsable" name="responsable" placeholder="Responsible name" required>	

				<!--<label for="nombre_oc">
					OC que recibe la solicitud:
				</label>
				<input type="text" class="form-control" id="nombre_oc" name="nombre_oc">-->
			</div>

			<div class="col-xs-12">
				<hr>
				<input type="hidden" name="insertar_solicitud" value="1">
				<input type="submit" class="btn btn-primary form-control" style="color: white;font-size:14px" value="Send Application" onclick="return validar()">
			</div>


		</fieldset>
	</form>
</div>


<script>
	
  function validar(){

    tipo_solicitud = document.getElementsByName("tipo_solicitud");
    tuvo_ventas = document.getElementsByName("preg8");
    opcion_venta = document.getElementsByName("preg9");
     
    // INICIA SELECCION TIPO SOLICITUD
    var seleccionado = false;
    for(var i=0; i<tipo_solicitud.length; i++) {    
      if(tipo_solicitud[i].checked) {
        seleccionado = true;
        break;
      }
    }
     
    if(!seleccionado) {
      alert("You must select an application type");
      return false;
    }
    //// TERMINA SELECCION TIPO SOLICITUD

    /// INICIA OPCION DE VENTAS
    var ventas = false;
    var valor_venta = '';
    for(var i=0; i<tuvo_ventas.length; i++) {    
      if(tuvo_ventas[i].checked) {
      	valor_venta = tuvo_ventas[i].value;
        ventas = true;
        break;
      }
    }
     
    if(!ventas) {
      alert("Must select whether or not you had sales");
      return false;
    }
    /// TERMINA OPCION DE VENTAS


    if(valor_venta != 'NO'){
	    var monto = false;
	    for(var i=0; i<opcion_venta.length; i++) {    
	      if(opcion_venta[i].checked) {
	        monto = true;
	        break;
	      }
	    }
	     
	    if(!monto) {
	      alert("You selected that you had sales, you must select the SPP sales amount");
	      return false;
	    }

    }

    return true
  }

</script>

<script>
var contador=0;
var contador2=1;
	function tabla_organizaciones()
	{
		contador++;
		contador2++;

		var table = document.getElementById("tabla_organizaciones");
		{
			var row = table.insertRow(2);
			var cell1 = row.insertCell(0);
			var cell2 = row.insertCell(1);
			var cell3 = row.insertCell(2);
			var cell4 = row.insertCell(3);
			var cell5 = row.insertCell(4);
			var cell6 = row.insertCell(5);
			var cell7 = row.insertCell(6);
			var cell8 = row.insertCell(7);
			var cell9 = row.insertCell(8);
			var cell10 = row.insertCell(9);
			var cell11 = row.insertCell(10);

			cell1.innerHTML = ''+contador2+'';
			cell2.innerHTML = '<input type="text" class="form-control" style="width:150px;" name="sub_nombre['+contador+']" id="" placeholder="Complete name">';
			cell3.innerHTML = 'Generated by the system';
			cell4.innerHTML = '<input type="text" class="form-control" style="width:120px;" name="num_productores['+contador+']" id="" placeholder="Only numbers">';
			cell5.innerHTML = '<textarea class="form-control" name="sub_producto['+contador+']" id="" rows="3" placeholder="Products"></textarea>';
			cell6.innerHTML = '<input type="text" class="form-control" name="unidad_produccion['+contador+']" id="" placeholder="Unit production">';
			cell7.innerHTML = '<textarea class="form-control" style="width:150px;" name="sub_certificaciones['+contador+']" placeholder="Certification(s)" rows="3" required></textarea>';
			cell8.innerHTML = '<textarea class="form-control" style="width:150px;" name="sub_certificadora['+contador+']" placeholder="Certification Entity" rows="3" required></textarea>';
			cell9.innerHTML = '<textarea class="form-control" style="width:150px;" name="sub_anio_certificacion['+contador+']" placeholder="Initial Year" rows="3" required></textarea>';
			cell10.innerHTML = 'YES <input type="radio" name="sub_interrumpido'+contador+'['+contador+']" id="" value="SI"><br>NO <input type="radio" name="sub_interrumpido'+contador+'['+contador+']" id="" value="NO">';
			cell11.innerHTML = '<textarea class="form-control" name="sub_incumplimientos['+contador+']" id="" rows="3"></textarea>';
		}
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
	cont++;

	  var row = table.insertRow(1);
	  var cell1 = row.insertCell(0);
	  var cell2 = row.insertCell(1);
	  var cell3 = row.insertCell(2);
	  var cell4 = row.insertCell(3);
	  var cell5 = row.insertCell(4);
	  var cell6 = row.insertCell(5);
	  var cell7 = row.insertCell(6); 
	  var cell8 = row.insertCell(7);
	  var cell9 = row.insertCell(8);

	  cell1.innerHTML = '<input type="text" class="form-control" name="producto_general['+cont+']" id="exampleInputEmail1" placeholder="General product">';

	  cell2.innerHTML = '<input type="text" class="form-control" name="producto['+cont+']" id="exampleInputEmail1" placeholder="Specific Product">';
	  
	  cell3.innerHTML = '<input type="text" class="form-control" name="volumen['+cont+']" id="exampleInputEmail1" placeholder="Volume">';
	  
	  cell4.innerHTML = 'YES <input type="radio" name="terminado'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="terminado'+cont+'['+cont+']" id="" value="NO">';
	  
	  cell5.innerHTML = '<input type="text" class="form-control" name="materia['+cont+']" id="exampleInputEmail1" placeholder="Material">';
	  
	  cell6.innerHTML = '<input type="text" class="form-control" name="destino['+cont+']" id="exampleInputEmail1" placeholder="Destination">';
	  
	  cell7.innerHTML = 'YES <input type="radio" name="marca_propia'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="marca_propia'+cont+'['+cont+']" id="" value="NO">';
	  
	  cell8.innerHTML = 'YES <input type="radio" name="marca_cliente'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="marca_cliente'+cont+'['+cont+']" id="" value="NO">';
	  
	  cell9.innerHTML = 'YES <input type="radio" name="sin_cliente'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="sin_cliente'+cont+'['+cont+']" id="" value="NO">';	  

	  }

	}	

</script>

<script>
var contador=0;
	function tablaCertificaciones()
	{
		contador++;
	var table = document.getElementById("tablaCertificaciones");
	  {
	  var row = table.insertRow(2);
	  var cell1 = row.insertCell(0);
	  var cell2 = row.insertCell(1);
	  var cell3 = row.insertCell(2);
	  var cell4 = row.insertCell(3);

	  cell1.innerHTML = '<input type="text" class="form-control" name="certificacion['+contador+']" id="exampleInputEmail1" placeholder="CERTIFICATION">';
	  cell2.innerHTML = '<input type="text" class="form-control" name="certificadora['+contador+']" id="exampleInputEmail1" placeholder="ENTITY">';
	  cell3.innerHTML = '<input type="text" class="form-control" name="ano_inicial['+contador+']" id="exampleInputEmail1" placeholder="INITIAL YEAR">';
	  cell4.innerHTML = '<div class="col-xs-6">YES<input type="radio" class="form-control" name="interrumpida['+contador+']" value="SI"></div><div class="col-xs-6">NO<input type="radio" class="form-control" name="interrumpida['+contador+']" value="NO"></div>';
	  }
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
	cont++;

	  var row = table.insertRow(1);
	  var cell1 = row.insertCell(0);
	  var cell2 = row.insertCell(1);
	  var cell3 = row.insertCell(2);
	  var cell4 = row.insertCell(3);
	  var cell5 = row.insertCell(4);
	  var cell6 = row.insertCell(5);
	  var cell7 = row.insertCell(6); 
	  var cell8 = row.insertCell(7);
	  var cell9 = row.insertCell(8);

	  cell1.innerHTML = '<input type="text" class="form-control" name="producto_general['+cont+']" id="exampleInputEmail1" placeholder="General product">';

	  cell2.innerHTML = '<input type="text" class="form-control" name="producto['+cont+']" id="exampleInputEmail1" placeholder="Specific Product">';
	  
	  cell3.innerHTML = '<input type="text" class="form-control" name="volumen['+cont+']" id="exampleInputEmail1" placeholder="Volume">';
	  
	  cell4.innerHTML = 'YES <input type="radio" name="terminado'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="terminado'+cont+'['+cont+']" id="" value="NO">';
	  
	  cell5.innerHTML = '<input type="text" class="form-control" name="materia['+cont+']" id="exampleInputEmail1" placeholder="Material">';
	  
	  cell6.innerHTML = '<input type="text" class="form-control" name="destino['+cont+']" id="exampleInputEmail1" placeholder="Destination">';
	  
	  cell7.innerHTML = 'YES <input type="radio" name="marca_propia'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="marca_propia'+cont+'['+cont+']" id="" value="NO">';
	  
	  cell8.innerHTML = 'YES <input type="radio" name="marca_cliente'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="marca_cliente'+cont+'['+cont+']" id="" value="NO">';
	  
	  cell9.innerHTML = 'YES <input type="radio" name="sin_cliente'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="sin_cliente'+cont+'['+cont+']" id="" value="NO">';	  

	  }

	}	

</script>