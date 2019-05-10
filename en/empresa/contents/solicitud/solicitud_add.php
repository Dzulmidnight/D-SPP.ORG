<?php 
require_once('../Connections/dspp.php'); 
require_once('../Connections/mail.php');

mysql_select_db($database_dspp, $dspp);

if (!isset($_SESSION)) {
  session_start();
	
	$redireccion = "../index.php?EMPRESA";

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
$idempresa = $_SESSION['idempresa'];
$ruta_croquis = "../../archivos/empresaArchivos/croquis/";
$spp_global = "cert@spp.coop";
$auxiliar = "acc@spp.coop";
$administrador = "yasser.midnight@gmail.com";
/************ VARIABLES DE CONTROL ******************/



if(isset($_POST['insertar_solicitud']) && $_POST['insertar_solicitud'] == 1){
	$idoc = $_POST['idoc'];

	$estatus_publico = 1; // EN REVISIÓN
	$estatus_interno = NULL;
	$estatus_dspp = 1; // SOLICITUD EN REVISIÓN
	$alcance_opp = "";

	/* INICIA CAPTURA ALCANCE DE LA EMPRESA */
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
	if(isset($_POST['importacion'])){
		$importacion = $_POST['importacion'];
	}else{
		$importacion = '';
	}

    ///CAPTURAMOS EL TIPO DE EMPRESA
    if(isset($_POST['comprador'])){
    	$comprador = $_POST['comprador'];
    }else{
    	$comprador = '';
    }
    if(isset($_POST['intermediario'])){
    	$intermediario = $_POST['intermediario'];
    }else{
    	$intermediario = '';
    }
    if(isset($_POST['maquilador'])){
    	$maquilador = $_POST['maquilador'];
    }else{
    	$maquilador = '';
    }

	/* TERMINA CAPTURA ALCANCE DEL OPP */


	if(isset($_POST['preg6'])){
		$preg6 = $_POST['preg6'];
	}else{
		$preg6 = "";
	}
	if(isset($_POST['preg13'])){
		$preg13 = $_POST['preg13'];
	}else{
		$preg13 = "";
	}
	if(isset($_POST['preg14'])){
		if($_POST['preg14'] == 'mayor'){
			$preg14 = $_POST['preg14_1'];
		}else{
			$preg14 = $_POST['preg14'];
		}
	}else{
		$preg14 = "";
	}

	if(!empty($_FILES['preg9']['name'])){
	    $_FILES["preg9"]["name"];
	      move_uploaded_file($_FILES["preg9"]["tmp_name"], $ruta_croquis.date("Ymd H:i:s")."_".$_FILES["preg9"]["name"]);
	      $croquis = $ruta_croquis.basename(date("Ymd H:i:s")."_".$_FILES["preg9"]["name"]);
	}else{
		$croquis = NULL;
	}


	///INGRESAMOS EL TIPO DE SOLICITUD A LA TABLA OPP y EL ALCANCE DE LA OPP
	$updateSQL = sprintf("UPDATE empresa SET comprador = %s, intermediario = %s, maquilador = %s, estatus_empresa = %s WHERE idempresa = %s",
		GetSQLValueString($comprador, "int"),
		GetSQLValueString($intermediario, "int"),
		GetSQLValueString($maquilador, "int"),
		GetSQLValueString($_POST['tipo_solicitud'], "int"),
		GetSQLValueString($idempresa, "int"));
	$actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());



	if($idoc == 'TODOS'){
		$row_oc = mysql_query("SELECT * FROM oc", $dspp) or die(mysql_error());

		while($informacion_oc = mysql_fetch_assoc($row_oc)){ //// INICIA WHILE INFORMACIÓN OC
			// INGRESAMOS LA INFORMACION A LA SOLICITUD DE CERTIFICACION
			$insertSQL = sprintf("INSERT INTO solicitud_registro (tipo_solicitud, idempresa, idoc, comprador_final, intermediario, maquilador, contacto1_nombre, contacto2_nombre, contacto1_cargo, contacto2_cargo, contacto1_email, contacto2_email, contacto1_telefono, contacto2_telefono, adm1_nombre, adm2_nombre, adm1_email, adm2_email, adm1_telefono, adm2_telefono, preg1, preg2, preg3, preg4, produccion, procesamiento, importacion, preg6, preg7, preg8, preg9, preg10, preg12, preg13, preg14, preg15, responsable, fecha_registro, estatus_interno ) VALUES (%s, %s, %s, %s, %s, %s,%s, %s, %s, %s, %s, %s, %s, %s, %s,%s, %s, %s, %s, %s, %s, %s, %s, %s,%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
				   GetSQLValueString($_POST['tipo_solicitud'], "text"),
				   GetSQLValueString($idempresa, "int"),
		           GetSQLValueString($informacion_oc['idoc'], "int"),
		           GetSQLValueString($comprador, "int"),
		           GetSQLValueString($intermediario, "int"),
		           GetSQLValueString($maquilador, "int"),
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
		           GetSQLValueString($_POST['preg1'], "text"),
		           GetSQLValueString($_POST['preg2'], "text"),
		           GetSQLValueString($_POST['preg3'], "text"),
		           GetSQLValueString($_POST['preg4'], "text"),
		           GetSQLValueString($produccion, "int"),
		           GetSQLValueString($procesamiento, "int"),
		           GetSQLValueString($importacion, "int"),
		           GetSQLValueString($preg6, "text"),
		           GetSQLValueString($_POST['preg7'], "text"),
		           GetSQLValueString($_POST['preg8'], "text"),
		           GetSQLValueString($croquis, "text"),
		           GetSQLValueString($_POST['preg10'], "text"),
		           GetSQLValueString($_POST['preg12'], "text"),
		           GetSQLValueString($preg13, "text"),
		           GetSQLValueString($preg14, "text"),
		           GetSQLValueString($_POST['preg15'], "text"),
		           GetSQLValueString($_POST['responsable'], "text"),
		           GetSQLValueString($fecha, "int"),
		           GetSQLValueString($estatus_dspp, "int"));
				  $Result1 = mysql_query($insertSQL, $dspp) or die(mysql_error());
				 $idsolicitud_registro = mysql_insert_id($dspp); 

				 // INGRESAMOS EL PORCENTAJE DE VENTA DE LOS PRODUCTOS
			 	if(!empty($_POST['organico']) || !empty($_POST['comercio_justo']) || !empty($_POST['spp']) || !empty($_POST['sin_certificado'])){
			 		$insertSQL = sprintf("INSERT INTO porcentaje_productoVentas (organico, comercio_justo, spp, sin_certificado, idsolicitud_registro, idempresa) VALUES (%s, %s, %s, %s, %s, %s)",
			 			GetSQLValueString($_POST['organico'], "text"),
			 			GetSQLValueString($_POST['comercio_justo'], "text"),
			 			GetSQLValueString($_POST['spp'], "text"),
			 			GetSQLValueString($_POST['sin_certificado'], "text"),
			 			GetSQLValueString($idsolicitud_registro, "int"),
			 			GetSQLValueString($idempresa, "int"));
			 		$insertar = mysql_query($insertSQL,$dspp) or die(mysql_error());
			 	}
			


				/*************************** INICIA INSERTAR PROCESO DE CERTIFICACIÓN ***************************/
				$insertSQL = sprintf("INSERT INTO proceso_certificacion (idsolicitud_registro, estatus_publico, estatus_interno, estatus_dspp, fecha_registro) VALUES (%s, %s, %s, %s, %s)",
					GetSQLValueString($idsolicitud_registro, "int"),
					GetSQLValueString($estatus_publico, "int"),
					GetSQLValueString($estatus_interno, "int"),
					GetSQLValueString($estatus_dspp, "int"),
					GetSQLValueString($fecha, "int"));
				$insertar = mysql_query($insertSQL,$dspp) or die(mysql_error());
				/*************************** TERMINA INSERTAR PROCESO DE CERTIFICACIÓN ***************************/



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
							$insertSQL = sprintf("INSERT INTO certificaciones (idsolicitud_registro, certificacion, certificadora, ano_inicial, interrumpida) VALUES (%s, %s, %s, %s, %s)",
							    GetSQLValueString($idsolicitud_registro, "int"),
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
				$producto = $_POST['producto'];
				$volumen_estimado = $_POST['volumen_estimado'];
				$volumen_terminado = $_POST['volumen_terminado'];
				$volumen_materia = $_POST['volumen_materia'];
				$destino = $_POST['destino'];
				$origen = $_POST['origen'];

				/*$marca_propia = $_POST['marca_propia'];
				$marca_cliente = $_POST['marca_cliente'];
				$sin_cliente = $_POST['sin_cliente'];*/

				for ($i=0;$i<count($producto);$i++) { 
					if($producto[$i] != NULL){


							//$terminado = $_POST[$array1[$i]];
							//$marca_propia = $_POST[$array2[$i]];
							//$marca_cliente = $_POST[$array3[$i]];
							//$sin_cliente = $_POST[$array4[$i]];
							/* 18_12_2017
							$str = iconv($charset, 'ASCII//TRANSLIT', $producto[$i]);
							$producto[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));

							$str = iconv($charset, 'ASCII//TRANSLIT', $destino[$i]);
							$destino[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));

							$str = iconv($charset, 'ASCII//TRANSLIT', $origen[$i]);
							$origen[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));
							18_12_2017*/


						    $insertSQL = sprintf("INSERT INTO productos (idempresa, idsolicitud_registro, producto, volumen_estimado, volumen_terminado, volumen_materia, origen, destino) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
						    	GetSQLValueString($idempresa, "int"),
						          GetSQLValueString($idsolicitud_registro, "int"),
						          GetSQLValueString($producto[$i], "text"),
						          GetSQLValueString($volumen_estimado[$i], "text"),
						          GetSQLValueString($volumen_terminado[$i], "text"),
						          GetSQLValueString($volumen_materia[$i], "text"),
						          GetSQLValueString($origen[$i], "text"),
						          GetSQLValueString($destino[$i], "text"));

						  $Result = mysql_query($insertSQL, $dspp) or die(mysql_error());
					}
				}
				/***************************** TERMINA INSERTAR PRODUCTOS ******************************/

				/************************** INICIA INSERTAR SUB EMPRESAS **************************/
				$subempresa = $_POST['subempresa'];
				$servicio = $_POST['servicio'];


				for ($i=0;$i<count($subempresa);$i++) { 
					if($subempresa[$i] != NULL){
					    $insertSQL = sprintf("INSERT INTO sub_empresas (idsolicitud_registro, nombre, servicio, idempresa) VALUES (%s, %s, %s, %s)",
					          GetSQLValueString($idsolicitud_registro, "int"),
					          GetSQLValueString($subempresa[$i], "text"),
					          GetSQLValueString($servicio[$i], "text"),
					          GetSQLValueString($idempresa, "int"));
					  	$Result = mysql_query($insertSQL, $dspp) or die(mysql_error());
					}
				}


				///// INICIA ENVIO DEL MENSAJE POR CORREO AL OC y a SPP GLOBAL
				$asunto = "D-SPP | Solicitud de Registro para Compradores y otros Actores / Application for Buyers and other Actors ";
				/*$row_oc_correo = mysql_query("SELECT * FROM oc WHERE idoc = $informacion_oc[idoc]", $dspp) or die(mysql_error());
				$oc = mysql_fetch_assoc($row_oc_correo);
*/
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
				              <th scope="col" align="left" width="280"><strong>Solicitud de Registro para Compradores y otro Actores / Application for Buyers and other Actors </strong></th>
				            </tr>
				            <tr>
				              <td style="padding-top:10px;">
				   
				              Para poder consultar la solicitud, por favor iniciar sesión en su cuenta de OC(Organismo de Certificación) en el siguiente enlace: <a href="http://d-spp.org" target="_new">www.d-spp.org</a>
				              <br>
				              To consult the application, please log in to your CE(Certification Entity) account, in the following link: <a href="http://d-spp.org" target="_new">www.d-spp.org</a>
				              </td>
				            </tr>
						    <tr>
						      <td align="left">Teléfono / Company phone: '.$_POST['telefono'].'</td>
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
						              <td width="130px">Nombre de la Empresa/Company name</td>
						              <td width="130px">País / Country</td>
						              <td width="130px">Organismo de Certificación / Certification Entity</td>
						           
						              <td width="130px">Fecha de solicitud/Date of application</td>
						            </tr>
						            <tr style="font-size: 12px;">
						              <td style="padding:10px;">
						              	'.$_POST['nombre'].'
						              </td>
						              <td style="padding:10px;">
						                '.$_POST['pais'].'
						              </td>
						              <td style="padding:10px;">
						                '.$informacion_oc['nombre'].'
						              </td>
						              <td style="padding:10px;">
						              '.date('d/m/Y', $fecha).'
						              </td>
						            </tr>

						          </tbody>
						        </table>        
						      </td>
						    </tr>

						  </tbody>
						</table>

					</body>
					</html>
				';
				///// TERMINA ENVIO DEL MENSAJE POR CORREO AL OC y a SPP GLOBAL

				  if(!empty($informacion_oc['email1'])){
				    //$mail->AddAddress($detalle_opp['email_opp']);
				    $token = strtok($informacion_oc['email1'], "\/\,\;");
				    while ($token !== false)
				    {
				      $mail->AddAddress($token);
				      $token = strtok('\/\,\;');
				    }

				  }
				  if(!empty($informacion_oc['email2'])){
				    //$mail->AddAddress($detalle_opp['email_opp']);
				    $token = strtok($informacion_oc['email2'], "\/\,\;");
				    while ($token !== false)
				    {
				      $mail->AddAddress($token);
				      $token = strtok('\/\,\;');
				    }

				  }

			    //$mail->AddBCC($spp_global);
		        //$mail->Username = "soporte@d-spp.org";
		        //$mail->Password = "/aung5l6tZ";
		        $mail->Subject = utf8_decode($asunto);
		        $mail->Body = utf8_decode($cuerpo_correo);
		        $mail->MsgHTML(utf8_decode($cuerpo_correo));
		        $mail->Send();
		        $mail->ClearAddresses();

		} /// TERMINA WHILE INFORMACIÓN OC

	}else{ //// INICIA ELSE ENVIAR A OC
		// INGRESAMOS LA INFORMACION A LA SOLICITUD DE CERTIFICACION
		$insertSQL = sprintf("INSERT INTO solicitud_registro (tipo_solicitud, idempresa, idoc, comprador_final, intermediario, maquilador, contacto1_nombre, contacto2_nombre, contacto1_cargo, contacto2_cargo, contacto1_email, contacto2_email, contacto1_telefono, contacto2_telefono, adm1_nombre, adm2_nombre, adm1_email, adm2_email, adm1_telefono, adm2_telefono, preg1, preg2, preg3, preg4, produccion, procesamiento, importacion, preg6, preg7, preg8, preg9, preg10, preg12, preg13, preg14, preg15, responsable, fecha_registro, estatus_interno ) VALUES (%s, %s, %s, %s, %s, %s,%s, %s, %s, %s, %s, %s, %s, %s, %s,%s, %s, %s, %s, %s, %s, %s, %s, %s,%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
			   GetSQLValueString($_POST['tipo_solicitud'], "text"),
			   GetSQLValueString($idempresa, "int"),
		       GetSQLValueString($_POST['idoc'], "int"),
		       GetSQLValueString($comprador, "int"),
		       GetSQLValueString($intermediario, "int"),
		       GetSQLValueString($maquilador, "int"),
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
		       GetSQLValueString($_POST['preg1'], "text"),
		       GetSQLValueString($_POST['preg2'], "text"),
		       GetSQLValueString($_POST['preg3'], "text"),
		       GetSQLValueString($_POST['preg4'], "text"),
		       GetSQLValueString($produccion, "int"),
		       GetSQLValueString($procesamiento, "int"),
		       GetSQLValueString($importacion, "int"),
		       GetSQLValueString($preg6, "text"),
		       GetSQLValueString($_POST['preg7'], "text"),
		       GetSQLValueString($_POST['preg8'], "text"),
		       GetSQLValueString($croquis, "text"),
		       GetSQLValueString($_POST['preg10'], "text"),
		       GetSQLValueString($_POST['preg12'], "text"),
		       GetSQLValueString($preg13, "text"),
		       GetSQLValueString($preg14, "text"),
		       GetSQLValueString($_POST['preg15'], "text"),
		       GetSQLValueString($_POST['responsable'], "text"),
		       GetSQLValueString($fecha, "int"),
		       GetSQLValueString($estatus_dspp, "int"));


			  $Result1 = mysql_query($insertSQL, $dspp) or die(mysql_error());
			 
			 $idsolicitud_registro = mysql_insert_id($dspp); 


			 // INGRESAMOS EL PORCENTAJE DE VENTA DE LOS PRODUCTOS
		 	if(!empty($_POST['organico']) || !empty($_POST['comercio_justo']) || !empty($_POST['spp']) || !empty($_POST['sin_certificado'])){
		 		$insertSQL = sprintf("INSERT INTO porcentaje_productoVentas (organico, comercio_justo, spp, sin_certificado, idsolicitud_registro, idempresa) VALUES (%s, %s, %s, %s, %s, %s)",
		 			GetSQLValueString($_POST['organico'], "text"),
		 			GetSQLValueString($_POST['comercio_justo'], "text"),
		 			GetSQLValueString($_POST['spp'], "text"),
		 			GetSQLValueString($_POST['sin_certificado'], "text"),
		 			GetSQLValueString($idsolicitud_registro, "int"),
		 			GetSQLValueString($idempresa, "int"));
		 		$insertar = mysql_query($insertSQL,$dspp) or die(mysql_error());
		 	}
		


			/*************************** INICIA INSERTAR PROCESO DE CERTIFICACIÓN ***************************/
			$insertSQL = sprintf("INSERT INTO proceso_certificacion (idsolicitud_registro, estatus_publico, estatus_interno, estatus_dspp, fecha_registro) VALUES (%s, %s, %s, %s, %s)",
				GetSQLValueString($idsolicitud_registro, "int"),
				GetSQLValueString($estatus_publico, "int"),
				GetSQLValueString($estatus_interno, "int"),
				GetSQLValueString($estatus_dspp, "int"),
				GetSQLValueString($fecha, "int"));
			$insertar = mysql_query($insertSQL,$dspp) or die(mysql_error());
			/*************************** TERMINA INSERTAR PROCESO DE CERTIFICACIÓN ***************************/



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
						$insertSQL = sprintf("INSERT INTO certificaciones (idsolicitud_registro, certificacion, certificadora, ano_inicial, interrumpida) VALUES (%s, %s, %s, %s, %s)",
						    GetSQLValueString($idsolicitud_registro, "int"),
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
			$producto = $_POST['producto'];
			$volumen_estimado = $_POST['volumen_estimado'];
			$volumen_terminado = $_POST['volumen_terminado'];
			$volumen_materia = $_POST['volumen_materia'];
			$destino = $_POST['destino'];
			$origen = $_POST['origen'];

			/*$marca_propia = $_POST['marca_propia'];
			$marca_cliente = $_POST['marca_cliente'];
			$sin_cliente = $_POST['sin_cliente'];*/

			for ($i=0;$i<count($producto);$i++) { 
				if($producto[$i] != NULL){


						//$terminado = $_POST[$array1[$i]];
						//$marca_propia = $_POST[$array2[$i]];
						//$marca_cliente = $_POST[$array3[$i]];
						//$sin_cliente = $_POST[$array4[$i]];

						$str = iconv($charset, 'ASCII//TRANSLIT', $producto[$i]);
						$producto[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));

						$str = iconv($charset, 'ASCII//TRANSLIT', $destino[$i]);
						$destino[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));

						$str = iconv($charset, 'ASCII//TRANSLIT', $origen[$i]);
						$origen[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));


					    $insertSQL = sprintf("INSERT INTO productos (idempresa, idsolicitud_registro, producto, volumen_estimado, volumen_terminado, volumen_materia, origen, destino) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
					    	GetSQLValueString($idempresa, "int"),
					          GetSQLValueString($idsolicitud_registro, "int"),
					          GetSQLValueString($producto[$i], "text"),
					          GetSQLValueString($volumen_estimado[$i], "text"),
					          GetSQLValueString($volumen_terminado[$i], "text"),
					          GetSQLValueString($volumen_materia[$i], "text"),
					          GetSQLValueString($origen[$i], "text"),
					          GetSQLValueString($destino[$i], "text"));

					  $Result = mysql_query($insertSQL, $dspp) or die(mysql_error());
				}
			}
			/***************************** TERMINA INSERTAR PRODUCTOS ******************************/

			/************************** INICIA INSERTAR SUB EMPRESAS **************************/
			$subempresa = $_POST['subempresa'];
			$servicio = $_POST['servicio'];


			for ($i=0;$i<count($subempresa);$i++) { 
				if($subempresa[$i] != NULL){
				    $insertSQL = sprintf("INSERT INTO sub_empresas (idsolicitud_registro, nombre, servicio, idempresa) VALUES (%s, %s, %s, %s)",
				          GetSQLValueString($idsolicitud_registro, "int"),
				          GetSQLValueString($subempresa[$i], "text"),
				          GetSQLValueString($servicio[$i], "text"),
				          GetSQLValueString($idempresa, "int"));
				  	$Result = mysql_query($insertSQL, $dspp) or die(mysql_error());
				}
			}

			///// INICIA ENVIO DEL MENSAJE POR CORREO AL OC y a SPP GLOBAL
			$asunto = "D-SPP | Solicitud de Registro para Compradores y otros Actores / Application for Buyers and other Actors";
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
			              <th scope="col" align="left" width="280"><strong>Solicitud de Registro para Compradores y otro Actores / Application for Buyers and other Actors</strong></th>
			            </tr>
			            <tr>
			              <td style="padding-top:10px;">
			   
			              Para poder consultar la solicitud, por favor iniciar sesión en su cuenta de OC(Organismo de Certificación) en el siguiente enlace: <a href="http://d-spp.org" target="_new">www.d-spp.org</a>
			              <br>
			              To consult the application, please log in to your CE(Certification Entity) account, in the following link: <a href="http://d-spp.org" target="_new">www.d-spp.org</a>
			              </td>
			            </tr>
					    <tr>
					      <td align="left">Teléfono / Company phone: '.$_POST['telefono'].'</td>
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
					              <td width="130px">Nombre de la Empresa/Company name</td>
					              <td width="130px">País / Country</td>
					              <td width="130px">Organismo de Certificación / Certification Entity</td>
					           
					              <td width="130px">Fecha de solicitud/Date of application</td>
					            </tr>
					            <tr style="font-size: 12px;">
					              <td style="padding:10px;">
					              	'.$_POST['nombre'].'
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

					  </tbody>
					</table>

				</body>
				</html>
			';
			///// TERMINA ENVIO DEL MENSAJE POR CORREO AL OC y a SPP GLOBAL

			  if(!empty($oc['email1'])){
			    //$mail->AddAddress($detalle_opp['email_opp']);
			    $token = strtok($oc['email1'], "\/\,\;");
			    while ($token !== false)
			    {
			      $mail->AddAddress($token);
			      $token = strtok('\/\,\;');
			    }

			  }
			  if(!empty($oc['email2'])){
			    //$mail->AddAddress($detalle_opp['email_opp']);
			    $token = strtok($oc['email2'], "\/\,\;");
			    while ($token !== false)
			    {
			      $mail->AddAddress($token);
			      $token = strtok('\/\,\;');
			    }

			  }

		    $mail->AddBCC($spp_global);
		    $mail->AddBCC($auxiliar);
	        //$mail->Username = "soporte@d-spp.org";
	        //$mail->Password = "/aung5l6tZ";
	        $mail->Subject = utf8_decode($asunto);
	        $mail->Body = utf8_decode($cuerpo_correo);
	        $mail->MsgHTML(utf8_decode($cuerpo_correo));
	        $mail->Send();
	        $mail->ClearAddresses();

	} /// TERMINA ELSE ENVIAR A OC
	// INGRESAMOS LOS CONTACTOS DE LA SOLICITUD A LA TABLA DE CONTACTOS
	if(!empty($_POST['contacto1_nombre'])){
		$insertSQL = sprintf("INSERT INTO contactos(idempresa, nombre, cargo, telefono1, email1, idsolicitud_registro) VALUES (%s, %s, %s, %s, %s, %s)",
			GetSQLValueString($idempresa, "int"),
			GetSQLValueString($_POST['contacto1_nombre'], "text"),
			GetSQLValueString($_POST['contacto1_cargo'], "text"),
			GetSQLValueString($_POST['contacto1_telefono'], "text"),
			GetSQLValueString($_POST['contacto1_email'], "text"),
			GetSQLValueString($idsolicitud_registro, "int"));
		$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

	}
	if(!empty($_POST['contacto2_nombre'])){
		$insertSQL = sprintf("INSERT INTO contactos(idempresa, nombre, cargo, telefono1, email1, idsolicitud_registro) VALUES (%s, %s, %s, %s, %s, %s)",
			GetSQLValueString($idempresa, "int"),
			GetSQLValueString($_POST['contacto2_nombre'], "text"),
			GetSQLValueString($_POST['contacto2_cargo'], "text"),
			GetSQLValueString($_POST['contacto2_telefono'], "text"),
			GetSQLValueString($_POST['contacto2_email'], "text"),
			GetSQLValueString($idsolicitud_registro, "int"));
		$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

	}
	if(!empty($_POST['adm1_nombre'])){
		$insertSQL = sprintf("INSERT INTO contactos(idempresa, nombre, cargo, telefono1, email1, idsolicitud_registro) VALUES (%s, %s, %s, %s, %s, %s)",
			GetSQLValueString($idempresa, "int"),
			GetSQLValueString($_POST['adm1_nombre'], "text"),
			GetSQLValueString('ADMINISTRATIVO', "text"),
			GetSQLValueString($_POST['adm1_telefono'], "text"),
			GetSQLValueString($_POST['adm1_email'], "text"),
			GetSQLValueString($idsolicitud_registro, "int"));
		$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

	}
	if(!empty($_POST['adm2_nombre'])){
		$insertSQL = sprintf("INSERT INTO contactos(idempresa, nombre, cargo, telefono1, email1, idsolicitud_registro) VALUES (%s, %s, %s, %s, %s, %s)",
			GetSQLValueString($idempresa, "int"),
			GetSQLValueString($_POST['adm2_nombre'], "text"),
			GetSQLValueString('ADMINISTRATIVO', "text"),
			GetSQLValueString($_POST['contacto2_telefono'], "text"),
			GetSQLValueString($_POST['contacto2_email'], "text"),
			GetSQLValueString($idsolicitud_registro, "int"));
		$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

	}

 	$mensaje = "The Application has been sent to the Certification Entity, you will shortly be contacted";


}

  //$insertGoTo = "main_menu.php?SOLICITUD&add&mensaje=Solicitud agregada correctamente, se ha notificado al OC por email.";
$query = "SELECT * FROM empresa WHERE idempresa = $idempresa";
$row_empresa = mysql_query($query,$dspp) or die(mysql_error());
$empresa = mysql_fetch_assoc($row_empresa);

?>
<div class="row">
	<?php 
	if(isset($mensaje)){
	?>
	<div class="col-lg-12 alert alert-success alert-dismissible" role="alert">
	  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	  <?php echo $mensaje; ?>
	</div>
	<?php
	}
	?>
</div>

<div class="row" style="font-size:12px;">
	<form action="" name="" method="POST" enctype="multipart/form-data">
		<fieldset>
			<div class="col-md-12 alert alert-primary" style="padding:7px;">
				<h3 class="text-center">Application for Buyers´, Registration</h3>
			</div>

			<div class="col-md-12 text-center alert alert-success" style="padding:7px;"><b>GENERAL INFORMATION</b></div>

			<div class="col-lg-12 alert alert-info" style="padding:7px;">

				<div class="col-md-12 alert alert-warning" style="padding:5px;">
						<ul>
							<li>
								<b>
									IF YOU HAD CERTIFICATION PREVIOUSLY SPP (WITH CURRENT CERTIFICATION ENTITY OR OTHER CERTIFICATION ENTITY) TO CHOOSE <span style="color:red">""RENEWAL OF CERTIFICATE"</span>
								</b>
							</li>
							<li><b>IF THE FIRST TIME YOU CHOOSE TO CERTIFY: <span style="color:red">"FIRST TIME"</span></b></li>
						</ul>
					 
				</div>

				<div class="col-md-6">
					<div class="col-md-12">
						<b>Send to Certification Entity:</b>
					</div>
					<div class="col-md-12">
						<select class="form-control" name="idoc" id="" required>
							<option value="">Choose one</option>
							<?php 
							$query = "SELECT idoc, abreviacion FROM oc";
							$row_oc = mysql_query($query,$dspp) or die(mysql_error());

							while($oc = mysql_fetch_assoc($row_oc)){
							?>
							<option value="<?php echo $oc['idoc']; ?>" <?php if($empresa['idoc'] == $oc['idoc']){ echo "selected"; } ?>><?php echo $oc['abreviacion']; ?></option>
							<?php
							}
							 ?>
							 <option value="TODOS">SEND ALL</option>
						</select>
					</div>
				</div>
				<div class="col-md-6">
					<div class="col-md-12">
						<p class="text-center"><strong>SELECT TYPE APPLICATION</strong></p>
					</div>
					<div class="col-md-6">
						<label for="nueva">FIRST TIME</label>
						<input type="radio" class="form-control" id="nueva" name="tipo_solicitud" value="NUEVA">
					</div>
					<div class="col-md-6">
						<label for="renovacion">RENEWAL OF REGISTRATION</label>
						<input type="radio" class="form-control" id="renovacion" name="tipo_solicitud" value="RENOVACION">
					</div>
				</div>
			</div>
			<?php 
			/* 18_12_2017
			if($empresa['comprador']){
			?>
				<div class="row">
					<div class="col-md-12 alert alert-info">
						<b>VALOR TOTAL DE VENTAS (<i>independientemente de si se trata de ventas SPP o no</i>)</b>
						<br>
						Nota: <i>Este dato es necesario para determinar la membresia a pagar por parte del Comprador Final.   <a href="#">Reglamento de Costos V8_2017-02-03, 4.3 (descargar</a>) "Los Compradores Finales pagan una cuota de Membresía Anual (en USD) equivalente a  un porcentaje del total de facturación de la empresa, independientemente de si se trata de ventas SPP o no"</i>

						<input type="number" step="any" class="form-control" id="facturacion_total" name="facturacion_total" placeholder="Valor de ventas, ingresar solo numeros" required>
					</div>
				</div>
			<?php
			}
			18_12_2017 */
			 ?>

			<!------ INICIA INFORMACION GENERAL Y DATOS FISCALES ------>
			<div class="row">
				<div class="col-md-6">
					<div class="col-md-12 text-center alert alert-warning" style="padding:7px;">GENERAL INFORMATION</div>
					<label for="fecha_elaboracion">DATE OF LABORATION</label>
					<input type="text" class="form-control" id="fecha_elaboracion" name="fecha_elaboracion" value="<?php echo date('Y-m-d', time()); ?>" readonly>	

					<label for="spp">SPP IDENTIFICATION CODE(#SPP): </label>
					<input type="text" class="form-control" id="spp" name="spp" value="<?php echo $empresa['spp']; ?>">

					<label for="nombre">COMPANY NAME: </label>
					<textarea name="nombre" id="nombre" class="form-control"><?php echo $empresa['nombre']; ?></textarea>


					<label for="pais">COUNTRY:</label>
					<?php 
					$row_pais = mysql_query("SELECT * FROM paises",$dspp) or die(mysql_error());
					 ?>
					 <select name="pais" id="pais" class="form-control">
					 	<option value="">Select a country</option>
					 	<?php 
					 	while($pais = mysql_fetch_assoc($row_pais)){
					 		if(utf8_encode($pais['nombre']) == $empresa['pais']){
					 			echo "<option value='".utf8_encode($pais['nombre'])."' selected>".utf8_encode($pais['nombre'])."</option>";
					 		}else{
					 			echo "<option value='".utf8_encode($pais['nombre'])."'>".utf8_encode($pais['nombre'])."</option>";
					 		}
					 	}
					 	 ?>
					 </select>

					<label for="direccion_oficina">COMPLETE ADDRESS FOR COMPANY LOCATION (STREET, DISTRICT, TOWN / CITY, REGION):</label>
					<textarea name="direccion_oficina" id="direccion_oficina"  class="form-control"><?php echo $empresa['direccion_oficina']; ?></textarea>

					<label for="email">EMAIL:</label>
					<input type="text" class="form-control" id="email" name="email" value="<?php echo $empresa['email']; ?>">

					<label for="email">COMPANY TELEPHONES(COUNTRY CODE+AREA CODE+NUMBER):</label>
					<input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo $empresa['telefono']; ?>">	

					<label for="sitio_web">WEBSITE:</label>
					<input type="text" class="form-control" id="sitio_web" name="sitio_web" value="<?php echo $empresa['sitio_web']; ?>">

				</div>

				<div class="col-md-6">
					<div class="col-md-12 text-center alert alert-warning" style="padding:7px;">FISCAL DATA</div>

					<label for="razon_social">BUSINESS NAME</label>
					<input type="text" class="form-control" id="razon_social" name="razon_social" value="<?php echo $empresa['razon_social']; ?>">

					<label for="direccion_fiscal">FISCAL ADDRESS</label>
					<textarea class="form-control" name="direccion_fiscal" id="direccion_fiscal"><?php echo $empresa['direccion_fiscal']; ?></textarea>

					<label for="rfc">RFC</label>
					<input type="text" class="form-control" id="rfc" name="rfc" value="<?php echo $empresa['rfc']; ?>">

					<label for="ruc">RUC</label>
					<input type="text" class="form-control" id="ruc" name="ruc" value="<?php echo $empresa['ruc']; ?>">
				</div>
			</div>
			<!------ INICIA INFORMACION GENERAL Y DATOS FISCALES ------>


			<!------ INICIA INFORMACION CONTACTOS Y AREA ADMINISTRATIVA ------>
			<div class="row">
				<div class="col-md-6">
					<div class="col-md-12 text-center alert alert-warning" style="padding:7px;">CONTACT PERSON(S) OF APPLICATION</div>

					<label for="persona1">CONTACT PERSON(S)</label>
					<input type="text" class="form-control" id="persona1" name="contacto1_nombre" placeholder="* Nombre Persona 1" required>
					<input type="text" class="form-control" id="" name="contacto2_nombre" placeholder="Nombre Persona 2">

					<label for="cargo">POSITION(S)</label>
					<input type="text" class="form-control" id="cargo" name="contacto1_cargo" placeholder="* Cargo Persona 1" required>
					<input type="text" class="form-control" id="" name="contacto2_cargo" placeholder="Cargo Persona 2">

					<label for="email">EMAIL:</label>
					<input type="email" class="form-control" id="email" name="contacto1_email" placeholder="* Email Persona 1" required>
					<input type="email" class="form-control" id="" name="contacto2_email" placeholder="Email Persona 2">

					<label for="telefono">TELEPHONE(S)  FOR CONTACT PERSON(S):</label>
					<input type="text" class="form-control" id="telefono" name="contacto1_telefono" placeholder="* Telefono Persona 1" required>
					<input type="text" class="form-control" id="" name="contacto2_telefono" placeholder="Telefono Persona 2">

				</div>

				<div class="col-md-6">
					<div class="col-md-12 text-center alert alert-warning" style="padding:7px;">PERSON(S) OF THE ADMINISTRATIVE AREA:</div>

					<label for="persona_adm">PERSON(S) OF THE ADMINISTRATIVE AREA</label>
					<input type="text" class="form-control" id="persona_adm" name="adm1_nombre" placeholder="Nombre Persona 1">
					<input type="text" class="form-control" id="" name="adm2_nombre" placeholder="Nombre Persona 2">

					<label for="email_adm">EMAIL</label>
					<input type="email" class="form-control" id="email_adm" name="adm1_email" placeholder="Email Persona 1">
					<input type="email" class="form-control" id="" name="adm2_email" placeholder="Email Persona 2">

					<label for="telefono_adm">TELEPHONE</label>
					<input type="text" class="form-control" id="telefono_adm" name="adm1_telefono" placeholder="Telefono Persona 1">
					<input type="text" class="form-control" id="" name="adm2_telefono" placeholder="Telefono Persona 2">
				</div>
			</div>
			<!------ FIN INFORMACION CONTACTOS Y AREA ADMINISTRATIVA ------>

			<!------ INICIA INFORMACION DATOS DE OPERACIÓN ------>
			<div class="col-md-12 alert alert-info">
				<div>
					<label for="alcance_opp">
						SELECT TYPE OF COMPANY SPP FOR WHICH REGISTRATION IS SOUGHT. INTERMEDIARY MAY NOT SIGN SPP IF NOT HAVE A BUYER OR FINAL SPP REGISTERED REGISTRATION PROCESS.
					</label>
				</div>

                  <div class="checkbox">
                    <label class="col-sm-4">
                      <input type="checkbox" name="comprador" <?php if($empresa['comprador']){echo "checked"; } ?> value="1"> FINAL BUYER
                    </label>
                    <label class="col-sm-4">
                      <input type="checkbox" name="intermediario" <?php if($empresa['intermediario']){echo "checked"; } ?> value="1"> INTERMEDIARY
                    </label>
                    <label class="col-sm-4">
                      <input type="checkbox" name="maquilador" <?php if($empresa['maquilador']){echo "checked"; } ?> value="1"> MAQUILA COMPANY
                    </label>
                  </div>
			</div>


			<div class="col-md-12 text-center alert alert-success" style="padding:7px;">INFORMATION ON OPERATION</div>

			<div class="row">
				<div class="col-md-12">
					<label for="preg1">
						1.	FROM WHICH SMALL PRODUCERS’ ORGANIZATIONS DO YOU MAKE PURCHASES OR ATTEMPT TO DO SO UNDER THE SMALL PRODUCERS’ SYMBOL SCHEME?
					</label>
					<textarea name="preg1" id="preg1" class="form-control"></textarea>


					<label for="preg2">
						2.	WHO IS/ARE THE OWNER(S) OF THE COMPANY? 
					</label>
					<textarea name="preg2" id="preg2" class="form-control"></textarea>

					<label for="preg3">
						3.	SPECIFY WHICH PRODUCT (S) YOU WANT TO INCLUDE IN THE CERTIFICATE OF THE SMALL PRODUCERS’  SYMBOL FOR WHICH THE CERTIFICATION ENTITY WILL CONDUCT THE ASSESSMENT.<sup>4 Check the Regulation on Graphics and the List of  Optional Complementary Criteria.</sup>
					</label>
					<input type="text" class="form-control" id="preg3" name="preg3">

					<label for="preg4">
						4.	IF YOUR COMPANY IS A FINAL BUYER, MENTION IF YOUR ORGANIZATION WOULD LIKE TO INCLUDE AN ADDITIONAL DESCRIPTOR FOR COMPLEMENTARY USE WITH THE GRAPHIC DESIGN OF THE SMALL PRODUCERS’ SYMBOL 
					</label>
					<textarea name="preg4" id="preg4" class="form-control"></textarea>

					<div >
						<label for="alcance_opp">
							5. SELECT THE SCOPE OF THE COMPANY:
						</label>
					</div>
					<div class="col-md-4">
						<label>PRODUCTION</label>
						<input type="checkbox" name="produccion" class="form-control" value="1">
					</div>
					<div class="col-md-4">
						<label>PROCESSING</label>
						<input type="checkbox" name="procesamiento" class="form-control" value="1">
					</div>
					<div class="col-md-4">
						<label>TRAIDING</label>
						<input type="checkbox" name="importacion" class="form-control" value="1">
					</div>


				<p><b>6.	SPECIFY IF YOUR COMPANY SUBCONTRACT THE SERVICES OF PROCESSING PLANTS, TRADING COMPANIES OR COMPANIES THAT CARRY OUT THE IMPORT OR EXPORT, IF THE ANSWER IS AFFIRMATIVE, MENTION THE NAME AND THE SERVICE THAT PERFORMS</b></p>
				<div class="col-md-6">
					YES <input type="radio" class="form-control" name="preg6" onclick="mostrar_empresas()" id="preg6" value="SI">
				</div>
				<div class="col-md-6">
					NO <input type="radio" class="form-control" name="preg6" onclick="ocultar_empresas()" id="preg6" value="NO">
				</div>



				<p>IF THE RESPONSE IS AFFIRMATIVE, MENTION THE NAME AND THE SERVICE THAT IT REALIZES</p>
				<div id="contenedor_tablaEmpresas" class="col-md-12" style="display:none">
					<table class="table table-bordered" id="tablaEmpresas">
						<tr>
							<td>NAME OF THE COMPANY</td>
							<td>SERVICE THAT IT REALIZES</td>
							<td>
								<button type="button" onclick="tablaEmpresas()" class="btn btn-primary" aria-label="Left Align">
								  <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
								</button>
								
							</td>
						</tr>
						<tr class="text-center">
							<td><input type="text" class="form-control" name="subempresa[0]" id="exampleInputEmail1" placeholder="COMPANY"></td>
							<td><input type="text" class="form-control" name="servicio[0]" id="exampleInputEmail1" placeholder="SERVICE"></td>
						</tr>
					</table>	
				</div>		


					<label for="preg7">
						7.	IF YOU SUBCONTRACT THE SERVICES OF PROCESSING PLANTS, TRADING COMPANIES OR COMPANIES THAT CARRY OUT THE IMPORT OR EXPORT, INDICATE WHETHER THESE COMPANIES ARE GOING TO APPLYFOR THE REGISTRATION UNDER SPP CERTIFICATION PROGRAM. <sup>5</sup>
						<br>
						<small><sup>5</sup> Check the document General Application Guidelines SPP System.</small>
					</label>
					<textarea name="preg7" id="preg7" class="form-control"></textarea>

					<label for="preg8">
						8.	IN ADDITION TO YOUR MAIN OFFICES, PLEASE SPECIFY HOW MANY COLLECTION CENTERS, PROCESSING AREAS AND ADDITIONAL OFFICES YOU HAVE.
					</label>
					<textarea name="preg8" id="preg8" class="form-control"></textarea>

					<label for="preg9">
						9.	IF YOU HAVE COLLECTION CENTERS, PROCESSING AREAS OR ADDITIONAL OFFICES, PLEASE ATTACH A GENERAL MAP INDICATING WHERE THEY ARE LOCATED.
					</label>
					<input type="file" id="preg9" name="preg9" class="form-control">

					<label for="preg10">
						10. IF THE APPLICANT HAS AN INTERNAL CONTROL SYSTEM FOR COMPLYING WITH THE CRITERIA IN THE GENERAL STANDARD OF THE SMALL PRODUCERS’ SYMBOL, PLEASE EXPLAIN HOW IT WORKS.
					</label>
					<textarea name="preg10" id="preg10" class="form-control"></textarea>

					<p class="alert alert-info">11.	FILL OUT THE TABLE ACCORDING YOUR CERTIFICATIONS, (example: EU, NOP, JASS, FLO, etc).</p>

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
						<tr class="text-center">
							<td><input type="text" class="form-control" name="certificacion[0]" id="exampleInputEmail1" placeholder="CERTIFICATION"></td>
							<td><input type="text" class="form-control" name="certificadora[0]" id="exampleInputEmail1" placeholder="CERTIFICATION ENTITY"></td>
							<td><input type="text" class="form-control" name="ano_inicial[0]" id="exampleInputEmail1" placeholder="INITIAL YEAR"></td>
							<!--<td><input type="text" class="form-control" name="interrumpida[0]" id="exampleInputEmail1" placeholder="¿HA SIDO INTERRUMPIDA?"></td>-->
							<td>
								<div class="col-md-6">YES<input type="radio" class="form-control" name="interrumpida[0]" value="SI"></div>
								<div class="col-md-6">NO<input type="radio" class="form-control" name="interrumpida[0]" value="NO"></div>
							</td>
						</tr>
					</table>	

					<label for="preg12">
						12.	ACCORDING THE CERTIFICATIONS, IN ITS MOST RECENT INTERNAL AND EXTERNAL EVALUATIONS, HOW MANY CASES OF NON COMPLIANCE WERE INDENTIFIED? PLEASE EXPLAIN IF THEY HAVE BEEN RESOLVED OR WHAT THEIR STATUS IS?</label>
					<textarea name="preg12" id="preg12" class="form-control"></textarea>

					<p for="op_preg11">
						<b>13.	OF THE APPLICANT’S TOTAL TRADING DURING THE PREVIOUS CYCLE, WHAT PERCENTAGE WAS CONDUCTED UNDER THE SCHEMES OF CERTIFICATION FOR ORGANIC, FAIR TRADE AND/OR THE SMALL PRODUCERS’ SYMBOL?</b>
						<i>(* Enter only quantity, integer or decimals)</i>
						<div class="row">
							<div class="col-lg-12">
								<div class="col-md-3">
									<label for="organico">% ORGANIC</label>
									<input type="number" step="any" class="form-control" id="organico" name="organico" placeholder="Ej: 0.0">
								</div>
								<div class="col-md-3">
									<label for="comercio_justo">% FAIR TRADE</label>
									<input type="number" step="any" class="form-control" id="comercio_justo" name="comercio_justo" placeholder="Ej: 0.0">
								</div>
								<div class="col-md-3">
									<label for="spp">SMALL PRODUCERS´ SYMBOL</label>
									<input type="number" step="any" class="form-control" id="spp" name="spp" placeholder="Ej: 0.0">
								</div>
								<div class="col-md-3">
									<label for="otro">WITHOUT CERTIFICATE</label>
									<input type="number" step="any" class="form-control" id="otro" name="sin_certificado" placeholder="Ej: 0.0">
								</div>
							</div>
						</div>
					</p>					

					<p><b>14.	DID YOU HAVE SPP PURCHASES DURING THE PREVIOUS CERTIFICATION CYCLE?</b></p>
						<div class="col-md-6">
							YES <input type="radio" class="form-control" name="preg13" id="preg13" value="SI">
						</div>
						<div class="col-md-6">
							NO <input type="radio" class="form-control" name="preg13" id="preg13" value="NO">
						</div>			
					<p>
						<b>15.	IF YOUR RESPONSE WAS POSSITIVE, PLEASE SELECT THE RANGE OF THE TOTAL VALUE SPP PURCHASES ACCORDING TO THE FOLLOWING TABLE:
					</p>

					<div class="well col-md-12 " id="tablaVentas">
						<div class="col-md-6"><p>UP TO  $3,000 USD</p></div>
						<div class="col-md-6 "><input type="radio" name="preg14" class="form-control" id="ver" onclick="ocultar()" value="HASTA $3,000 USD"></div>
					
					
						<div class="col-md-6"><p>BETWEEN $3,000 AND $10,000 USD</p></div>
						<div class="col-md-6"><input type="radio" name="preg14" class="form-control" id="ver" onclick="ocultar()" value="ENTRE $3,000 Y $10,000 USD"></div>
					
					
						<div class="col-md-6"><p>BETWEEN $10,000 AND $25,000 USD</p></div>
						<div class="col-md-6"><input type="radio" name="preg14" class="form-control"  id="ver" onclick="ocultar()" value="ENTRE $10,000 A $25,000 USD"></div>
					
						<div class="col-md-6"><p>MORE THAT $25,000 USD* <sup>*</sup><br><h6><sup>*</sup>SPECIFY THE QUANTITY.</h6></p></div>
						<div class="col-md-6"><input type="radio" name="preg14" class="form-control" id="exampleInputEmail1" onclick="mostrar()" value="mayor">
							<input type="text" name="preg14_1" class="form-control" id="oculto" style='display:none;' placeholder="SPECIFY THE QUANTITY">
						</div>

					</div>
							
					<label for="preg15">
						16.	ESTIMATED DATE FOR BEGINNING TO USE THE SMALL PRODUCERS’ SYMBOL:
					</label>
					<input type="text" class="form-control" id="preg15" name="preg15">
				</div>
			</div>

			<!------ FIN INFORMACION DATOS DE OPERACIÓN ------>

			<div class="col-md-12 text-center alert alert-success" style="padding:7px;">INFORMATION ON PRODUCTS FOR WHICH APPLICAT WISHES TO USE SYMBOL<sup>6</sup></div>
			<div class="col-lg-12">
				<table class="table table-bordered" id="tablaProductos">
					<tr>
						<td>Product</td>
						<td>Total Estimated Volume to be Sold</td>
						<td>Volume of Finished Product</td>
						<td>Volume of Raw Material</td>
						<td>Country/Countries of Origin</td>
						<td>Country/Countries of Destination</td>
						<td>
							<button type="button" onclick="tablaProductos()" class="btn btn-primary" aria-label="Left Align">
							  <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
							</button>
							
						</td>					
					</tr>
					<tr>
						<td>
							<input type="text" class="form-control" name="producto[0]" id="exampleInputEmail1" placeholder="Product">
						</td>
						<td>
							<input type="text" class="form-control" name="volumen_estimado[0]" id="exampleInputEmail1" placeholder="Estimated volume">
						</td>
						<td>
							<input type="text" class="form-control" name="volumen_terminado[0]" id="exampleInputEmail1" placeholder="Volume finished">
						</td>

						<td>
							<input type="text" class="form-control" name="volumen_materia[0]" id="exampleInputEmail1" placeholder="Volume of material">
						</td>
						<td>
							<input type="text" class="form-control" name="origen[0]" id="exampleInputEmail1" placeholder="Origen">
						</td>
						<td>
							<input type="text" class="form-control" name="destino[0]" id="exampleInputEmail1" placeholder="Destination">
						</td>


					</tr>				
					<tr>
						<td colspan="6">
							<h6><sup>6</sup> Information provided in this section will be handled with complete confidentiality. Please insert additional lines necessary. </h6>
						</td>
					</tr>
				</table>
			</div>

			<div class="col-lg-12 text-center alert alert-success" style="padding:7px;">
				<b>COMMITMENTS</b>
			</div>
			<div class="col-lg-12 text-justify">
				<p>1.	By signing and sending in this document, the applicant expresses its interest in receiving a proposal for Registration with the Small Producers’ Symbol.</p>
				<p>2.	The registration process will begin when it is confirmed that the payment corresponding to the proposal has been received. </p>
				<p>3.	The fact that this application is delivered and received does not guarantee that the results of the registration process will be positive. </p>
				<p>4.	The applicant will become familiar with and comply with all the applicable requirements in the General Standard of the Small Producers’ Symbol for Buyers, Collective Trading Companies owned by Small Producers’ Organizations, Intermediaries and Maquila Companies, including both Critical and Minimum Criteria, and independently of the type of evaluation conducted.  </p>
			</div>
			<div class="col-lg-12">
				<p style="font-size:14px;">
					<strong>Name of the person who is responsible for the accuracy of the information on this form, and who, on behalf of the Applicant, will follow up on the application:</strong>
				</p>
				<input type="text" class="form-control" id="responsable" name="responsable" placeholder="Name of the person who is responsible" required>	

				<!--<label for="nombre_oc">
					OC que recibe la solicitud:
				</label>
				<input type="text" class="form-control" id="nombre_oc" name="nombre_oc">-->
			</div>
			<div class="col-md-12">
				<hr>
				<input type="hidden" name="insertar_solicitud" value="1">
				<input type="submit" class="btn btn-primary form-control" value="Enviar Solicitud" onclick="return validar()">
			</div>

		</fieldset>
	</form>
</div>


<script>
	
  function validar(){

    tipo_solicitud = document.getElementsByName("tipo_solicitud");
    tuvo_ventas = document.getElementsByName("preg13");
    opcion_venta = document.getElementsByName("preg14");
     
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
      alert("Must select whether or not you had purchases");
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
	      alert("You selected that if you had purchases, you must select the amount of purchases SPP");
	      //alert(valor_venta);
	      return false;
	    }

    }

    return true
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
	  cell2.innerHTML = '<input type="text" class="form-control" name="certificadora['+contador+']" id="exampleInputEmail1" placeholder="CERTIFICATION ENTITY">';
	  cell3.innerHTML = '<input type="text" class="form-control" name="ano_inicial['+contador+']" id="exampleInputEmail1" placeholder="INITIAL YEAR">';
	  cell4.innerHTML = '<div class="col-md-6">YES<input type="radio" class="form-control" name="interrumpida['+contador+']" value="SI"></div><div class="col-md-6">NO<input type="radio" class="form-control" name="interrumpida['+contador+']" value="NO"></div>';
	  }
	}	

	function tablaEmpresas()
	{
		contador++;
	var table = document.getElementById("tablaEmpresas");
	  {
	  var row = table.insertRow(2);
	  var cell1 = row.insertCell(0);
	  var cell2 = row.insertCell(1);


	  cell1.innerHTML = '<input type="text" class="form-control" name="subempresa['+contador+']" id="exampleInputEmail1" placeholder="COMPANY">';
	  cell2.innerHTML = '<input type="text" class="form-control" name="servicio['+contador+']" id="exampleInputEmail1" placeholder="SERVICE">';

	  }
	}	
	function mostrar_empresas(){
		document.getElementById('contenedor_tablaEmpresas').style.display = 'block';
	}
	function ocultar_empresas()
	{
		document.getElementById('contenedor_tablaEmpresas').style.display = 'none';
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
	 
	  cell1.innerHTML = '<input type="text" class="form-control" name="producto['+cont+']" id="exampleInputEmail1" placeholder="Product">';
	  
	  cell2.innerHTML = '<input type="text" class="form-control" name="volumen_estimado['+cont+']" id="exampleInputEmail1" placeholder="Estimated volume">';
	  
	  cell3.innerHTML = '<input type="text" class="form-control" name="volumen_terminado['+cont+']" id="exampleInputEmail1" placeholder="Finished volume">';
	  
	  cell4.innerHTML = '<input type="text" class="form-control" name="volumen_materia['+cont+']" id="exampleInputEmail1" placeholder="Volume of material">';
	  
	  cell5.innerHTML = '<input type="text" class="form-control" name="origen['+cont+']" id="exampleInputEmail1" placeholder="Origen">';
	  
	  cell6.innerHTML = '<input type="text" class="form-control" name="destino['+cont+']" id="exampleInputEmail1" placeholder="Destination">';
	   

	  }

	}	

</script>