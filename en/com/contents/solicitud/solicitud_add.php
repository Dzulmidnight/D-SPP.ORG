<?php require_once('../Connections/dspp.php'); 
      include_once("../../PHPMailer/class.phpmailer.php");
      include_once("../../PHPMailer/class.smtp.php");


        $mail = new PHPMailer();
        $mail->IsSMTP();
        //$mail->SMTPSecure = "ssl";
        $mail->Host = "mail.d-spp.org";
        $mail->Port = 25;
        $mail->SMTPAuth = true;
        $mail->Username = "soporte@d-spp.org";
        $mail->Password = "/aung5l6tZ";
        //$mail->SMTPDebug = 1;

        $mail->From = "soporte@d-spp.org";
        $mail->FromName = "CERT - DSPP";
        $mail->AddBCC("yasser.midnight@gmail.com", "correo Oculto");
        $mail->AddReplyTo("cert@spp.coop");

?>
?>
<?php
if (!isset($_SESSION)) {
  session_start();
	
	$redireccion = "../index.php?COM";

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

/*if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {



  $insertGoTo = "main_menu.php?SOLICITUD&add&mensaje=Solicitud agregada correctamente, se ha notificado al OC por email.";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}*/


mysql_select_db($database_dspp, $dspp);
$query_pais = "SELECT nombre FROM paises ORDER BY nombre ASC";
$pais = mysql_query($query_pais, $dspp) or die(mysql_error());
$row_pais = mysql_fetch_assoc($pais);
$totalRows_pais = mysql_num_rows($pais);

mysql_select_db($database_dspp, $dspp);
$query_oc = "SELECT idoc, idf, abreviacion, pais FROM oc ORDER BY nombre ASC";
$oc = mysql_query($query_oc, $dspp) or die(mysql_error());
$row_oc = mysql_fetch_assoc($oc);
$totalRows_oc = mysql_num_rows($oc);


$query_com = "SELECT * FROM com WHERE idcom='$_SESSION[idcom]'";
$com = mysql_query($query_com,$dspp) or die(mysql_error());
$row_com = mysql_fetch_assoc($com);

$query_contacto = "SELECT * FROM contacto WHERE idcom='$_SESSION[idcom]'";
$contacto = mysql_query($query_contacto,$dspp) or die(mysql_error());
$row_contacto = mysql_fetch_assoc($contacto);


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

$estadoPublico = "2";
$estadoInterno = "1";
$fecha_actual = time();

/************ VARIABLES DE CONTROL ******************/




if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) { // INICIA INSERCION DE SOLICITUD
 mysql_select_db($database_dspp, $dspp);

 	$fecha_actual = time();
 	$idcom = $_POST['idcom'];
    $array_resp5 = NULL;
    $array_tipo_empresa = NULL;
    $resp14_15 = NULL;
    $resp14_15_1 = NULL;
    $idcom = $_POST['idcom'];
    $resp6 = NULL;


    if(isset($_POST['resp6'])){
    	$resp6 = $_POST['resp6'];
    }else{
    	$resp6 = NULL;
    }



	if(isset($_POST['resp5'])){
		if(is_array($_POST['resp5'])){
			$resp5 = $_POST['resp5'];

			for ($i=0; $i < count($resp5) ; $i++) { 
				$array_resp5 .= $resp5[$i]." - ";
			}
		}else{
				$array_resp5 = NULL;
		}
	}else{
		$array_resp5 = NULL;
	}
	
	if(isset($_POST['tipo_empresa'])){
		if(is_array($_POST['tipo_empresa'])){
			$tipo_empresa = $_POST['tipo_empresa'];

			for ($i=0; $i < count($tipo_empresa) ; $i++) { 
				$array_tipo_empresa .= $tipo_empresa[$i]." - ";
			}
		}else{
				$array_tipo_empresa = NULL;
		}
	}else{
		$array_tipo_empresa = NULL;
	}


	if(isset($_POST['resp14_15'])){
		if(isset($_POST['resp14_15']) && $_POST['resp14_15'] == "mayor"){
			$resp14_15 = $_POST['resp14_15_1'];
		}else{
			$resp14_15 = $_POST['resp14_15'];
		}
	}else{
		$op_resp13 = NULL;
	}


	if(!empty($_POST['resp14'])){

		if(isset($_POST['resp14'])){
			$resp14 = $_POST['resp14'];
		}else{
			$resp14 = "";
		}

	}else{
		$resp14 = NULL;
	}

    if($_POST['idocSelect'] == "99"){
    	$queryOC = "SELECT * FROM oc";
    	$ejecutarOC = mysql_query($queryOC,$dspp) or die(mysql_error());
    	while($infoOC = mysql_fetch_assoc($ejecutarOC)){



    		$insertSQL = sprintf("INSERT INTO solicitud_registro(idcom, idoc, p1_nombre, p2_nombre, p1_cargo, p2_cargo, p1_correo, p2_correo, p1_telefono, p2_telefono, adm1_nombre, adm2_nombre, adm1_correo, adm2_correo, adm1_telefono, adm2_telefono, tipo_empresa, resp1, resp2, resp3, resp4, resp5, resp6, resp7, resp8, resp9, resp10, resp12, resp13, resp14, resp14_15, resp16, responsable, fecha_elaboracion, status_interno, status_publico) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s,	%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
			   GetSQLValueString($_POST['idcom'], "int"),
               GetSQLValueString($infoOC['idoc'], "int"),
               GetSQLValueString($_POST['p1_nombre'], "text"),
               GetSQLValueString($_POST['p2_nombre'], "text"),
               GetSQLValueString($_POST['p1_cargo'], "text"),
               GetSQLValueString($_POST['p2_cargo'], "text"),
               GetSQLValueString($_POST['p1_correo'], "text"),
               GetSQLValueString($_POST['p2_correo'], "text"),
               GetSQLValueString($_POST['p1_telefono'], "text"),
               GetSQLValueString($_POST['p2_telefono'], "text"),
               GetSQLValueString($_POST['adm1_nombre'], "text"),
               GetSQLValueString($_POST['adm2_nombre'], "text"),
               GetSQLValueString($_POST['adm1_correo'], "text"),
               GetSQLValueString($_POST['adm2_correo'], "text"),
               GetSQLValueString($_POST['adm1_telefono'], "text"),
               GetSQLValueString($_POST['adm2_telefono'], "text"),
               GetSQLValueString($array_tipo_empresa, "text"),
               GetSQLValueString($_POST['resp1'], "text"),
               GetSQLValueString($_POST['resp2'], "text"),
               GetSQLValueString($_POST['resp3'], "text"),
               GetSQLValueString($_POST['resp4'], "text"),
               GetSQLValueString($array_resp5, "text"),
               GetSQLValueString($resp6, "text"),
               GetSQLValueString($_POST['resp7'], "text"),
               GetSQLValueString($_POST['resp8'], "text"),
               GetSQLValueString($_POST['resp9'], "text"),
               GetSQLValueString($_POST['resp10'], "text"),
               GetSQLValueString($_POST['resp12'], "text"),
               GetSQLValueString($_POST['resp13'], "text"),
               GetSQLValueString($resp14, "text"),
               GetSQLValueString($resp14_15, "text"),
               GetSQLValueString($_POST['resp16'], "text"),
               GetSQLValueString($_POST['responsable'], "text"),
               GetSQLValueString($_POST['fecha_elaboracion'], "int"),
               GetSQLValueString($_POST['status_interno'], "int"),
               GetSQLValueString($_POST['status_publico'], "int"));

		   	   $Result1 = mysql_query($insertSQL,$dspp) or die(mysql_error());

				$idsolicitud_registro = mysql_insert_id($dspp); 



			//llenamos el registro de la fecha para llevar un control de las acciones que se han realizado dentro del sistema

			$idexterno = $idsolicitud_registro;
			$identificador = "SOLICITUD";
			$status = $_POST['my-checkbox'];

			//$queryFecha = "INSERT INTO fecha(fecha, idexterno, identificador, status) VALUES($fecha, $idexterno, '$identificador', $status)";
			//$ejecutar = mysql_query($queryFecha,$dspp) or die(mysql_error());

			$queryFecha = "INSERT INTO fecha(fecha, idexterno, idcom, identificador, status) VALUES($fecha_actual, $idexterno, $idcom, '$identificador', $status)";
			$ejecutar = mysql_query($queryFecha,$dspp) or die(mysql_error());

		    $mensaje = "Se ha enviado la Solicitud de Registro para Compradores, y otros Actores por parte de <b>$_SESSION[nombreCOM]</b>";


			if(!empty($_POST['certificacion'])){
				$certificacion = $_POST['certificacion'];
			}else{
				$certificacion = NULL;
			}


			if(!empty($_POST['certificadora'])){
				$certificadora = $_POST['certificadora'];
			}else{
				$certificadora = NULL;
			}

			if(!empty($_POST['ano_inicial'])){
				$ano_inicial = $_POST['ano_inicial'];
			}else{
				$ano_inicial = NULL;
			}

			if(!empty($_POST['interrumpida'])){
				$interrumpida = $_POST['interrumpida'];
			}else{
				$interrumpida = NULL;
			}


				for($i=0;$i<count($certificacion);$i++){// INICIA FOR CERTIFICACIONES
					if($certificacion[$i] != NULL){
						#for($i=0;$i<count($certificacion);$i++){
						$insertSQL = sprintf("INSERT INTO certificaciones (idsolicitud_registro, certificacion, certificadora, ano_inicial, interrumpida) VALUES (%s, %s, %s, %s, %s)",
						    GetSQLValueString($idsolicitud_registro, "int"),
						    GetSQLValueString($certificacion[$i], "text"),
						    GetSQLValueString($certificadora[$i], "text"),
						    GetSQLValueString($ano_inicial[$i], "text"),
						    GetSQLValueString($interrumpida[$i], "text"));

						$Result = mysql_query($insertSQL, $dspp) or die(mysql_error());
						#}
					}
				}// TERMINA FOR CERTIFICACIONES

				$producto = $_POST['producto'];
				$volumenEstimado = $_POST['volumenEstimado'];
				$volumenTerminado = $_POST['volumenTerminado'];
				$materia = $_POST['materia'];
				$paisOrigen = $_POST['paisOrigen'];
				$paisDestino = $_POST['paisDestino'];

				for ($i=0;$i<count($producto);$i++) { // INICIA FOR "PRODUCTOS"
					if($producto[$i] != NULL){

					    $insertSQL = sprintf("INSERT INTO productos (idsolicitud_registro, producto, volumenEstimado, volumenTerminado, materia, origen, destino) VALUES (%s, %s, %s, %s, %s, %s, %s)",
					          GetSQLValueString($idsolicitud_registro, "int"),
					          GetSQLValueString($producto[$i], "text"),
					          GetSQLValueString($volumenEstimado[$i], "text"),
					          GetSQLValueString($volumenTerminado[$i], "text"),
					          GetSQLValueString($materia[$i], "text"),
					          GetSQLValueString($paisOrigen[$i], "text"),
					          GetSQLValueString($paisDestino[$i], "text"));

					  $Result = mysql_query($insertSQL, $dspp) or die(mysql_error());
					}
				}// TERMINA FOR "PRODUCTOS"


				$resp6_empresa = $_POST['resp6_empresa'];
				$resp6_servicio = $_POST['resp6_servicio'];


				for ($i=0;$i<count($resp6_empresa);$i++) { // INICIA FOR "SUB EMPRESAS"
					if($resp6_empresa[$i] != NULL){

					    $insertSQL = sprintf("INSERT INTO subEmpresas (idsolicitud_registro, nombre, servicio, idcom) VALUES (%s, %s, %s, %s)",
					          GetSQLValueString($idsolicitud_registro, "int"),
					          GetSQLValueString($resp6_empresa[$i], "text"),
					          GetSQLValueString($resp6_servicio[$i], "text"),
					          GetSQLValueString($idcom, "int"));

					  $Result = mysql_query($insertSQL, $dspp) or die(mysql_error());
					}
				}// TERMINA FOR "SUB EMPRESAS"

			   /*$query = "SELECT * FROM oc";
			   $oc = mysql_query($query,$dspp) or die(mysql_error());
			   $row_oc = mysql_fetch_assoc($oc);
				*/

			    $nombre = $_POST['nombreCOM'];
			    $abreviacion = $_POST['abreviacion'];
			    $pais = $_POST['paisCOM'];
			    $nombreOC = $infoOC['nombre'];
			    $fecha_elaboracion = $_POST['fecha_elaboracion'];
			    $producto = $_POST['producto'];
			    $telefono1 = $_POST['p1_telefono'];
			    $direccion = $row_com['direccion'];
			    $ciudad = $row_com['ciudad'];
			    $emailCOM1 = $_POST['p1_correo'];
			    $emailCOM2 = $_POST['p2_correo'];
			    $fecha = date("d/m/Y", $fecha_elaboracion);
			    //$correo = $_POST['p1_correo'];
			    //$correo = $_POST['p2_correo'];

			    $paisEstado = $pais.' / '.$ciudad;

 

			    $asunto = "D-SPP Solcitud de Registro para Compradores y otros Actores / Application for Buyers’, Registration "; 


				$cuerpo = '
					<html>
					<head>
						<meta charset="utf-8">
					</head>
					<body>
					
						<table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
						  <tbody>
				            <tr>
				              <th rowspan="7" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
				              <th scope="col" align="left" width="280"><strong>Solcitud de Registro para Compradores y otros Actores  / Application for Buyers`, Registration</strong></th>
				            </tr>
				            <tr>
				              <td style="padding-top:10px;"><i>Para poder consultar la solicitud, por favor iniciar sesión en su cuenta de OC en el siguiente enlace: <a href="http://d-spp.org/?OC" target="_new">www.d-spp.org/?OC</a></i>.</td>
				            </tr>
				            <tr>
				              <td style="padding-top:10px;"><i>To consult the application, please log in to your OC at the following link: <a href="http://d-spp.org/?OC" target="_new">www.d-spp.org/?OC</a></i>.</td>
				            </tr>

						    <tr>
						      <td align="left">Teléfono / Telephone COM: '.$telefono1.'</td>
						    </tr>
						    <tr>
						      <td align="left">'.$direccion.'</td>
						    </tr>
						    <tr>
						      <td align="left">'.$paisEstado.'</td>
						    </tr>
						    <tr>
						      <td align="left" style="color:#ff738a;">Email: '.$emailCOM1.'</td>
						    </tr>
						    <tr>
						      <td align="left" style="color:#ff738a;">Email: '.$emailCOM2.'</td>
						    </tr>

						    <tr>
						      <td colspan="2">
						        <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;" border="1" width="650px">
						          <tbody>
						            <tr style="font-size: 12px; text-align:center; background-color:#dff0d8; color:#3c763d;" height="50px;">
						              <td width="130px">Nombre de la Empresa / Company Name</td>
						              <td width="130px">Abreviación / Short name</td>
						              <td width="130px">País / Country</td>
						              <td width="130px">Organismo de Certificación / Certification Entity</td>
						           
						              <td width="130px">Fecha de solicitud / Application date</td>
						            </tr>
						            <tr style="font-size: 12px;">
						              <td style="padding:10px;">
						              	'.$nombre.'
						              </td>
						              <td style="padding:10px;">
						                '.$abreviacion.'
						              </td>
						              <td style="padding:10px;">
						                '.$pais.'
						              </td>
						              <td style="padding:10px;">
						                '.$nombreOC.'
						              </td>
						              <td style="padding:10px;">
						              '.$fecha.'
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

			    //$destinatario = $infoOC['email'].",";
			    //$destinatario .= $infoOC['email2'];

        $mail->AddAddress($infoOC['email']);
        $mail->AddAddress($infoOC['email2']);


        //$mail->Username = "soporte@d-spp.org";
        //$mail->Password = "/aung5l6tZ";
        $mail->Subject = utf8_decode($asunto);
        $mail->Body = utf8_decode($cuerpo);
        $mail->MsgHTML(utf8_decode($cuerpo));
        $mail->Send();
        $mail->ClearAddresses();




    	}// TERMINA WHILE INSERTAR "TODOS LOS OC"
    }/*TERMINA IF DE INSERCION "TODOS LOS OC" */ 
    else if(isset($_POST['idocSelect'])){ /* INICIA INSERCION INDIVIDUAL DE SOLICITUD*/
    	$idoc = $_POST['idocSelect'];
    	$idcom = $_POST['idcom'];

		$insertSQL = sprintf("INSERT INTO solicitud_registro(idcom, idoc, p1_nombre, p2_nombre, p1_cargo, p2_cargo, p1_correo, p2_correo, p1_telefono, p2_telefono, adm1_nombre, adm2_nombre, adm1_correo, adm2_correo, adm1_telefono, adm2_telefono, tipo_empresa, resp1, resp2, resp3, resp4, resp5, resp6, resp7, resp8, resp9, resp10, resp12, resp13, resp14, resp14_15, resp16, responsable, fecha_elaboracion, status_interno, status_publico) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
		   GetSQLValueString($_POST['idcom'], "int"),
	       GetSQLValueString($_POST['idocSelect'], "int"),
	       GetSQLValueString($_POST['p1_nombre'], "text"),
	       GetSQLValueString($_POST['p2_nombre'], "text"),
	       GetSQLValueString($_POST['p1_cargo'], "text"),
	       GetSQLValueString($_POST['p2_cargo'], "text"),
	       GetSQLValueString($_POST['p1_correo'], "text"),
	       GetSQLValueString($_POST['p2_correo'], "text"),
	       GetSQLValueString($_POST['p1_telefono'], "text"),
	       GetSQLValueString($_POST['p2_telefono'], "text"),
	       GetSQLValueString($_POST['adm1_nombre'], "text"),
	       GetSQLValueString($_POST['adm2_nombre'], "text"),
	       GetSQLValueString($_POST['adm1_correo'], "text"),
	       GetSQLValueString($_POST['adm2_correo'], "text"),
	       GetSQLValueString($_POST['adm1_telefono'], "text"),
	       GetSQLValueString($_POST['adm2_telefono'], "text"),
	       GetSQLValueString($array_tipo_empresa, "text"),
	       GetSQLValueString($_POST['resp1'], "text"),
	       GetSQLValueString($_POST['resp2'], "text"),
	       GetSQLValueString($_POST['resp3'], "text"),
	       GetSQLValueString($_POST['resp4'], "text"),
	       GetSQLValueString($array_resp5, "text"),
	       GetSQLValueString($resp6, "text"),
	       GetSQLValueString($_POST['resp7'], "text"),
	       GetSQLValueString($_POST['resp8'], "text"),
	       GetSQLValueString($_POST['resp9'], "text"),
	       GetSQLValueString($_POST['resp10'], "text"),
	       GetSQLValueString($_POST['resp12'], "text"),
	       GetSQLValueString($_POST['resp13'], "text"),
	       GetSQLValueString($resp14, "text"),
	       GetSQLValueString($resp14_15, "text"),
	       GetSQLValueString($_POST['resp16'], "text"),
	       GetSQLValueString($_POST['responsable'], "text"),
	       GetSQLValueString($_POST['fecha_elaboracion'], "int"),
	       GetSQLValueString($_POST['my-checkbox'], "int"),
	       GetSQLValueString($_POST['status_publico'], "int"));

	   	   $Result1 = mysql_query($insertSQL,$dspp) or die(mysql_error());

			$idsolicitud_registro = mysql_insert_id($dspp); 

			//llenamos el registro de la fecha para llevar un control de las acciones que se han realizado dentro del sistema

			$idexterno = $idsolicitud_registro;
			$identificador = "SOLICITUD";
			$status = $_POST['my-checkbox'];

			//$queryFecha = "INSERT INTO fecha(fecha, idexterno, identificador, status) VALUES($fecha, $idexterno, '$identificador', $status)";
			//$ejecutar = mysql_query($queryFecha,$dspp) or die(mysql_error());

			$queryFecha = "INSERT INTO fecha(fecha, idexterno, idcom, identificador, status) VALUES($fecha_actual, $idexterno, $idcom, '$identificador', $status)";
			$ejecutar = mysql_query($queryFecha,$dspp) or die(mysql_error());

		    $mensaje = "Se ha enviado la Solicitud de Registro para Compradores, y otros Actores por parte de <b>$_SESSION[nombreCOM]</b>";


			if(!empty($_POST['certificacion'])){
				$certificacion = $_POST['certificacion'];
			}else{
				$certificacion = NULL;
			}


			if(!empty($_POST['certificadora'])){
				$certificadora = $_POST['certificadora'];
			}else{
				$certificadora = NULL;
			}

			if(!empty($_POST['ano_inicial'])){
				$ano_inicial = $_POST['ano_inicial'];
			}else{
				$ano_inicial = NULL;
			}

			if(!empty($_POST['interrumpida'])){
				$interrumpida = $_POST['interrumpida'];
			}else{
				$interrumpida = NULL;
			}

			for($i=0;$i<count($certificacion);$i++){// INICIA FOR CERTIFICACIONES
				if($certificacion[$i] != NULL){
					#for($i=0;$i<count($certificacion);$i++){
					$insertSQL = sprintf("INSERT INTO certificaciones (idsolicitud_registro, certificacion, certificadora, ano_inicial, interrumpida) VALUES (%s, %s, %s, %s, %s)",
					    GetSQLValueString($idsolicitud_registro, "int"),
					    GetSQLValueString($certificacion[$i], "text"),
					    GetSQLValueString($certificadora[$i], "text"),
					    GetSQLValueString($ano_inicial[$i], "text"),
					    GetSQLValueString($interrumpida[$i], "text"));

					$Result = mysql_query($insertSQL, $dspp) or die(mysql_error());
					#}
				}
			}// TERMINA FOR CERTIFICACIONES

			$producto = $_POST['producto'];
			$volumenEstimado = $_POST['volumenEstimado'];
			$volumenTerminado = $_POST['volumenTerminado'];
			$materia = $_POST['materia'];
			$paisOrigen = $_POST['paisOrigen'];
			$paisDestino = $_POST['paisDestino'];

			for ($i=0;$i<count($producto);$i++) { // INICIA FOR "PRODUCTOS"
				if($producto[$i] != NULL){

				    $insertSQL = sprintf("INSERT INTO productos (idsolicitud_registro, producto, volumenEstimado, volumenTerminado, materia, origen, destino) VALUES (%s, %s, %s, %s, %s, %s, %s)",
				          GetSQLValueString($idsolicitud_registro, "int"),
				          GetSQLValueString($producto[$i], "text"),
				          GetSQLValueString($volumenEstimado[$i], "text"),
				          GetSQLValueString($volumenTerminado[$i], "text"),
				          GetSQLValueString($materia[$i], "text"),
				          GetSQLValueString($paisOrigen[$i], "text"),
				          GetSQLValueString($paisDestino[$i], "text"));

				  $Result = mysql_query($insertSQL, $dspp) or die(mysql_error());
				}
			}// TERMINA FOR "PRODUCTOS"

				$resp6_empresa = $_POST['resp6_empresa'];
				$resp6_servicio = $_POST['resp6_servicio'];


				for ($i=0;$i<count($resp6_empresa);$i++) { // INICIA FOR "SUB EMPRESAS"
					if($resp6_empresa[$i] != NULL){

					    $insertSQL = sprintf("INSERT INTO subEmpresas (idsolicitud_registro, nombre, servicio, idcom) VALUES (%s, %s, %s, %s)",
					          GetSQLValueString($idsolicitud_registro, "int"),
					          GetSQLValueString($resp6_empresa[$i], "text"),
					          GetSQLValueString($resp6_servicio[$i], "text"),
					          GetSQLValueString($_POST['idcom'], "int"));

					  $Result = mysql_query($insertSQL, $dspp) or die(mysql_error());
					}
				}// TERMINA FOR "SUB EMPRESAS"

	
			   $query = "SELECT * FROM oc WHERE idoc = $_POST[idocSelect]";
			   $oc = mysql_query($query,$dspp) or die(mysql_error());
			   $row_oc = mysql_fetch_assoc($oc);


			    $nombre = $_POST['nombreCOM'];
			    $abreviacion = $_POST['abreviacion'];
			    $pais = $_POST['paisCOM'];
			    $nombreOC = $row_oc['nombre'];
			    $fecha_elaboracion = $_POST['fecha_elaboracion'];
			    $producto = $_POST['producto'];
			    $telefono1 = $_POST['p1_telefono'];
			    $direccion = $row_com['direccion'];
			    $ciudad = $row_com['ciudad'];
			    $emailCOM1 = $_POST['p1_correo'];
			    $emailCOM2 = $_POST['p2_correo'];
			    $fecha = date("d/m/Y", $fecha_elaboracion);
			    //$correo = $_POST['p1_correo'];
			    //$correo = $_POST['p2_correo'];

			    $paisEstado = $pais.' / '.$ciudad;


			    $destinatario = $row_oc['email'];
			    $asunto = "D-SPP Solcitud de Registro para Compradores y otros Actores / Application for Buyers’, Registration "; 


				$cuerpo = '
					<html>
					<head>
						<meta charset="utf-8">
					</head>
					<body>
					
						<table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
						  <tbody>
				            <tr>
				              <th rowspan="7" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
				              <th scope="col" align="left" width="280"><strong>Solcitud de Registro para Compradores y otros Actores  / Application for Buyers`, Registration</strong></th>
				            </tr>
				            <tr>
				              <td style="padding-top:10px;"><i>Para poder consultar la solicitud, por favor iniciar sesión en su cuenta de OC en el siguiente enlace: <a href="http://d-spp.org/?OC" target="_new">www.d-spp.org/?OC</a></i>.</td>
				            </tr>
				            <tr>
				              <td style="padding-top:10px;"><i>To consult the application, please log in to your OC at the following link: <a href="http://d-spp.org/?OC" target="_new">www.d-spp.org/?OC</a></i>.</td>
				            </tr>

						    <tr>
						      <td align="left">Teléfono / Telephone COM: '.$telefono1.'</td>
						    </tr>
						    <tr>
						      <td align="left">'.$direccion.'</td>
						    </tr>
						    <tr>
						      <td align="left">'.$paisEstado.'</td>
						    </tr>
						    <tr>
						      <td align="left" style="color:#ff738a;">Email: '.$emailCOM1.'</td>
						    </tr>
						    <tr>
						      <td align="left" style="color:#ff738a;">Email: '.$emailCOM2.'</td>
						    </tr>

						    <tr>
						      <td colspan="2">
						        <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;" border="1" width="650px">
						          <tbody>
						            <tr style="font-size: 12px; text-align:center; background-color:#dff0d8; color:#3c763d;" height="50px;">
						              <td width="130px">Nombre de la Empresa / Company Name</td>
						              <td width="130px">Abreviación / Short name</td>
						              <td width="130px">País / Country</td>
						              <td width="130px">Organismo de Certificación / Certification Entity</td>
						           
						              <td width="130px">Fecha de solicitud / Application date</td>
						            </tr>
						            <tr style="font-size: 12px;">
						              <td style="padding:10px;">
						              	'.$nombre.'
						              </td>
						              <td style="padding:10px;">
						                '.$abreviacion.'
						              </td>
						              <td style="padding:10px;">
						                '.$pais.'
						              </td>
						              <td style="padding:10px;">
						                '.$nombreOC.'
						              </td>
						              <td style="padding:10px;">
						              '.$fecha.'
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

        $mail->AddAddress($destinatario);

        //$mail->Username = "soporte@d-spp.org";
        //$mail->Password = "/aung5l6tZ";
        $mail->Subject = utf8_decode($asunto);
        $mail->Body = utf8_decode($cuerpo);
        $mail->MsgHTML(utf8_decode($cuerpo));
        $mail->Send();
        $mail->ClearAddresses();



        		$queryMensaje = "INSERT INTO mensajes(idcom, idoc, asunto, mensaje, destinatario, remitente, fecha) VALUES($idcom, $idoc, '$asunto', '$cuerpo', 'OC', 'COM', $fecha_actual)";
        		$ejecutar = mysql_query($queryMensaje,$dspp) or die(mysql_error());


	} //TERMINA INSERCION INDIVIDUAL DE SOLICITUD

    		//INSERTAMOS LOS DATOS DE CONTACTO DE LA SOLICITUD, DENTRO DE LOS CONTACTO DEL COM
    		$idcom = $_POST['idcom'];
    		if(!empty($_POST['p1_nombre'])){
    			$nombre = $_POST['p1_nombre'];
    			$cargo = $_POST['p1_cargo'];
    			$telefono = $_POST['p1_telefono'];
    			$email = $_POST['p1_telefono'];

    			$query = "INSERT INTO contacto(idcom, contacto, cargo, telefono1, email1) VALUES($idcom ,'$nombre', '$cargo', '$telefono', '$email')";
    			$ejecutar = mysql_query($query,$dspp) or die(mysql_error());
    		}
    		if(!empty($_POST['p2_nombre'])){
    			$nombre = $_POST['p2_nombre'];
    			$cargo = $_POST['p2_cargo'];
    			$telefono = $_POST['p2_telefono'];
    			$email = $_POST['p2_telefono'];

    			$query = "INSERT INTO contacto(idcom, contacto, cargo, telefono1, email1) VALUES($idcom ,'$nombre', '$cargo', '$telefono', '$email')";
    			$ejecutar = mysql_query($query,$dspp) or die(mysql_error());
    		}
    		if (!empty($_POST['adm1_nom'])) {
    			$nombre = $_POST['adm1_nom'];
    			$cargo = 'Administrador';
    			$telefono = $_POST['adm1_tel'];
    			$email = $_POST['adm1_correo'];

    			$query = "INSERT INTO contacto(idcom, contacto, cargo, telefono1, email1) VALUES($idcom ,'$nombre', '$cargo', '$telefono', '$email')";
    			$ejecutar = mysql_query($query,$dspp) or die(mysql_error());
    		}
    		if (!empty($_POST['adm2_nom'])) {
    			$nombre = $_POST['adm2_nom'];
    			$cargo = 'Administrador';
    			$telefono = $_POST['adm2_tel'];
    			$email = $_POST['adm2_correo'];

    			$query = "INSERT INTO contacto(idcom, contacto, cargo, telefono1, email1) VALUES($idcom ,'$nombre', '$cargo', '$telefono', '$email')";
    			$ejecutar = mysql_query($query,$dspp) or die(mysql_error());
    		}

			//actualizamos COM para cambiar el estado de su solicitud
			$procedimiento = $_POST['my-checkbox'];
			$query = "UPDATE com SET 
			nombre = '$_POST[nombreCOM]',
			sitio_web = '$_POST[sitio_web]',
			email = '$_POST[correoCOM]',
			telefono = '$_POST[telefonoCOM]',
			pais = '$_POST[paisCOM]',
			direccion = '$_POST[direccionCOM]',
			direccion_fiscal = '$_POST[direccionFiscalCOM]',
			rfc = '$_POST[rfc]',
			ruc = '$_POST[ruc]',
			ciudad = '$_POST[ciudadCOM]',
			estado = $procedimiento WHERE idcom = $idcom";


			$actualizar = mysql_query($query,$dspp) or die(mysql_error());






  /*$insertGoTo = "main_menu.php?SOLICITUD&select&mensaje=Solicitud agregada correctamente, se ha notificado al OC por email.";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));*/
echo "<script>window.location='main_menu.php?SOLICITUD&select&mensaje=Application added correctly, has been notified to the OC by email.'</script>"; 

} //TERMINA INSERCION DE SOLICITUD


?>

<br>
<script>
	
  function validar(){
    /*valor = document.getElementById("cotizacion_opp").value;
    if( valor == null || valor.length == 0 ) {
      alert("No se ha cargado la cotización de OPP");
      return false;
    }*/
    
    Procedimiento = document.getElementsByName("my-checkbox");
     
    var seleccionado = false;
    for(var i=0; i<Procedimiento.length; i++) {    
      if(Procedimiento[i].checked) {
        seleccionado = true;
        break;
      }
    }
     
    if(!seleccionado) {
      alert("You must select a type of Application");
      return false;
    }

    return true
  }

</script>

<form class="" method="post" name="form1" action="<?php echo $editFormAction; ?>" enctype="multipart/form-data">
 <table class="table table-bordered">
 	<thead>
 		<tr>
 			<th class="text-center" colspan="2"><h3>Application for Buyers’, Registration </h3></th>
 		</tr>
 		<tr>
 			<th>
	      		<div class="col-xs-12">
	      			<h5>Send to</h5>
	      		</div>
	      		<div class="col-xs-12">
			      	<select class="form-control" name="idocSelect" required>
			      		<!--<option class="form-control" value="99">FUNDEPPO</option>-->
			      		<option class="form-control" value="">Select One</option>
						<option value="99">FUNDEPPO( Everybody )</option>
			        <?php 

						do {  
						?>
						        <option class="form-control" value="<?php echo $row_oc['idoc']?>"><?php echo $row_oc['abreviacion']?></option>
						        <?php
						} while ($row_oc = mysql_fetch_assoc($oc));
					?>
			      	</select>		
	      		</div>
 			</th>
 			<th class="danger">
	  	    	<div class="col-xs-6">
	  	    		<div class="row">
						<h4>Type of Application <small>(Select the type of procedure you want to perform)</small></h4>
	  	    		</div>
	  	    	</div>
	  	    	<div class="col-xs-3">
	  	    		<div class="row">
		  	    		<div class="col-xs-12">
		  	    			<p style="font-size:12px;"><b>New Application</b></p>	
		  	    		</div>	  	 
		  	    		<div class="col-xs-12">
		  	    			<input type="radio" data-on-color="success" data-off-color="danger" data-size="small" name="my-checkbox" value='1'>
	
		  	    		</div>		  	    		   			
	  	    		</div>
	  	    	</div>
	  	    	<div class="col-xs-3">
	  	    		<div class="row">
		  	    		<div class="col-xs-12">
		  	    			<p style="font-size:12px;"><b>Registration Renewal</b></p>	
		  	    		</div>
		  	    		<div class="col-xs-12">
		  	    			<input type="radio" data-on-color="success" data-off-color="danger" data-size="small" name="my-checkbox" value='20'>
	
		  	    		</div>		  	    		
	  	    		</div>
	  	    	</div>

	  	    	</div>
	  	    	
	  	    	<!--<div class="col-xs-2"><button class="btn btn-danger">boton</button>DOCUMENTAL "ACORTADO"</div>
	  	    	<div class="col-xs-2"><button class="btn btn-danger">boton</button>DOCUMENTAL "NORMAL"</div>
	  	    	<div class="col-xs-2"><button class="btn btn-danger">boton</button>COMPLETO "IN SITU"</div>
	  	    	<div class="col-xs-2"><button class="btn btn-danger">boton</button>COMPLETO "A DISTANCIA"</div>-->
	  	    
 			</th>
 		</tr>
 		<tr>
 			<th colspan="2" class="text-center alert alert-success">APPLICANT’S GENERAL INFORMATION</th>
 		</tr>
 	</thead>
 	<tbody>

		<tr>
			<td>
				<p>COMPANY NAME</p>
			</td>
			<td>
				<input type="text" class="form-control" name="nombreCOM" value="<?php echo $row_com['nombre']?>">
			</td>
		</tr>
		<tr>
			<td>
				<p>COMPLETE ADDRESS FOR COMPANY LOCATION (STREET, DISTRICT, TOWN / CITY, REGION)</p>
			</td>
			<td>
				<input type="text" class="form-control" name="direccionCOM" value="<?php echo $row_com['direccion'];?>" placeholder="Company Address">
			</td>
		</tr>
		<tr>
			<td>
				<p>COMPANY EMAIL ADDRESS</p> 
				<input type="text" class="form-control" name="correoCOM" value="<?php echo $row_com['email']?>">
			</td>
			<td>
				<p>COMPANY TELEPHONES(COUNTRY CODE+AREA CODE+NUMBER):</p>	
				<input type="text" class="form-control" name="telefonoCOM" value="<?php echo $row_com['telefono']?>">
			</td>
		</tr>
		<tr>
			<td>
				<p>COUNTRY</p>
				<input type="text" class="form-control" name="paisCOM" value="<?php echo $row_com['pais']?>">
			</td>
			<td>
				<p>WEB SITE</p>
				<input type="text" class="form-control" name="sitio_web" value="<?php echo $row_com['sitio_web']?>">
			</td>
		</tr>
		<tr>
			<td>
				<p>CITY</p>
				<input type="text" class="form-control" name="ciudadCOM" value="<?php echo $row_com['ciudad']?>">
			</td>
			<td>
				<p>FISCAL ADDRESS</p>
				<input type="text" class="form-control" name="direccionFiscalCOM" value="<?php echo $row_com['direccion_fiscal']?>">
			</td>
		</tr>
		<tr>
			<td>
				<p>RUC</p>
				<input type="text" class="form-control" name="ruc" value="<?php echo $row_com['ruc']?>">
			</td>
			<td>
				<p>RFC</p>
				<input type="text" class="form-control" name="rfc" value="<?php echo $row_com['rfc']?>">
			</td>
		</tr>
		<!------------------------------------------ INICIA DATOS DE CONTACTO ---------------------------------------->
		<tr>
			<td colspan="2" class="text-center alert alert-warning"> CONTACT </td>
		</tr>
		<tr>
			<td colspan="2">
				<p>CONTACT PERSON(S) OF APPLICATION</p>
				<div class="col-xs-6">
					<input type="text" class="form-control" name="p1_nombre" placeholder="Name 1" required>
					<input type="email" class="form-control" name="p1_correo" placeholder="Email 1" required>
				</div>
				<div class="col-xs-6">
					<input type="text" class="form-control" name="p1_cargo" placeholder="Position 1" required>
					<input type="text" class="form-control" name="p1_telefono" placeholder="Telephone 1" required>
				</div>
				<div class="col-xs-12"><br></div>
				<div class="col-xs-6">
					<input type="text" class="form-control" name="p2_nombre" placeholder="Name 2">
					<input type="email" class="form-control" name="p2_correo" placeholder="Email 2">
				</div>
				<div class="col-xs-6">
					<input type="text" class="form-control" name="p2_cargo" placeholder="Position 2">
					<input type="text" class="form-control" name="p2_telefono" placeholder="Telephone 2">
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="text-center alert alert-warning">
				ADMINISTRATIVE AREA
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<p>PERSON(S) OF THE ADMINISTRATIVE AREA</p>
				<div class="col-xs-6">
					<input type="text" class="form-control" name="adm1_nombre" placeholder="Name 1">
					<input type="email" class="form-control" name="adm1_correo" placeholder="Email 1">
				</div>
				<div class="col-xs-6">

					<input type="text" class="form-control" name="adm1_telefono" placeholder="Telephone 1">
				</div>
				<div class="col-xs-12"><br></div>
				<div class="col-xs-6">
					<input type="text" class="form-control" name="adm2_nombre" placeholder="Name 2">
					<input type="email" class="form-control" name="adm2_correo" placeholder="Email 2">
				</div>
				<div class="col-xs-6">

					<input type="text" class="form-control" name="adm2_telefono" placeholder="Telephone 2">
				</div>
			</td>
		</tr>
		<!----------------------------------------------------------- INICIA DATOS DE OPERACION -------------------------------------------------------------------------->
		<tr class="text-center alert alert-success">
			<td colspan="2">INFORMATION ON OPERATION</td>
		</tr>
		<tr>
			<td colspan="2">
				<p>
					SELECT THE TYPE OF COMPANY FOR WHICH THE SPP REGISTRATION IS REQUESTED. AN INTERMEDIARY CAN NOT BE REGISTERED UNDER SPP IF IT DOES NOT HAVE A REGISTERED SPP-FINAL BUYER (OR IN THE PROCESS OF SPP REGISTRATION) TO WHOM IT WILL SELL.

				</p>
				<div class="col-xs-4">
					FINAL BUYER <input class="form-control" name="tipo_empresa[]" type="checkbox" value="comprador_final">
				</div>
				<div class="col-xs-4">
					INTERMEDIARY <input class="form-control" name="tipo_empresa[]" type="checkbox" value="intermediario">
				</div>
				<div class="col-xs-4">
					MAQUILA COMPANY <input class="form-control" name="tipo_empresa[]" type="checkbox" value="maquilador">
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<p>1.- FROM WHICH SMALL PRODUCERS’ ORGANIZATIONS DO YOU MAKE PURCHASES OR ATTEMPT TO DO SO UNDER THE SMALL PRODUCERS’ SYMBOL SCHEME? </p>
				<input type="text" class="form-control" name="resp1">
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<p>2.- WHO IS/ARE THE OWNER(S) OF THE COMPANY?</p>
				<input type="text" class="form-control" name="resp2">
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<p>3.- SPECIFY WHICH PRODUCT (S) YOU WANT TO INCLUDE IN THE CERTIFICATE OF THE SMALL PRODUCERS’ SYMBOL FOR WHICH THE CERTIFICATION ENTITY WILL CONDUCT THE ASSESSMENT.</p>
				<input type="text" class="form-control" name="resp3">
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<p>4.- IF YOUR COMPANY IS A FINAL BUYER, MENTION IF YOUR ORGANIZATION WOULD LIKE TO INCLUDE AN ADDITIONAL DESCRIPTOR FOR COMPLEMENTARY USE WITH THE GRAPHIC DESIGN OF THE SMALL PRODUCERS’ SYMBOL.<sup>4</sup> </p>
				<input type="text" class="form-control" name="resp4">
				<p><small><sup>4</sup> Review “Regulation on Graphics” and the “List of Optional Complementary Criteria”</small></p>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<p>5.- SELECT THE SCOPE OF THE COMPANY</p>

				<div class="col-xs-4">
					PRODUCTION <input class="form-control" name="resp5[]" type="checkbox" value="PRODUCCION">
				</div>
				<div class="col-xs-4">
					PROCESSING <input class="form-control" name="resp5[]" type="checkbox" value="PROCESAMIENTO">
				</div>
				<div class="col-xs-4">
					TRADING <input class="form-control" name="resp5[]" type="checkbox" value="IMPORTACION">
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<p>6.	SPECIFY IF YOUR COMPANY SUBCONTRACT THE SERVICES OF PROCESSING PLANTS, TRADING COMPANIES OR COMPANIES THAT CARRY OUT THE IMPORT OR EXPORT.</p>
				<div class="col-xs-6">
					YES <input type="radio" class="form-control" name="resp6" onclick="mostrar_empresas()" id="resp6" value="SI">
				</div>
				<div class="col-xs-6">
					NO <input type="radio" class="form-control" name="resp6" onclick="ocultar_empresas()" id="resp6" value="NO">
				</div>


				<!--<input type="text" class="form-control" name="resp6">-->
			</td>
		</tr>
		<tr >
			<td colspan="2" >
				<p>IF THE ANSWER IS AFFIRMATIVE, MENTION THE NAME AND THE SERVICE THAT PERFORMS.</p>
				<div id="contenedor_tablaEmpresas" class="col-xs-12" style="display:none">
					<table class="table table-bordered" id="tablaEmpresas">
						<tr>
							<td>NAME OF THE COMPANY</td>
							<td>SERVICE OFFERED</td>
							<td>
								<button type="button" onclick="tablaEmpresas()" class="btn btn-primary" aria-label="Left Align">
								  <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
								</button>
								
							</td>
						</tr>
						<tr class="text-center">
							<td><input type="text" class="form-control" name="resp6_empresa[0]" id="exampleInputEmail1" placeholder="COMPANY"></td>
							<td><input type="text" class="form-control" name="resp6_servicio[0]" id="exampleInputEmail1" placeholder="SERVICE"></td>
						</tr>
					</table>	
				</div>		
			</td>
		</tr>


		<tr>
			<td colspan="2">
				<p>7. IF YOU SUBCONTRACT THE SERVICES OF PROCESSING PLANTS, TRADING COMPANIES OR COMPANIES THAT CARRY OUT THE IMPORT OR EXPORT, INDICATE WHETHER THESE COMPANIES ARE GOING TO APPLYFOR THE REGISTRATION UNDER SPP CERTIFICATION PROGRAM.</p>
				<textarea class="form-control" name="resp7" id="" cols="30" rows="3"></textarea>

			</td>
		</tr>
		<tr>
			<td colspan="2">
				<p>8.  IN ADDITION TO YOUR MAIN OFFICES, PLEASE SPECIFY HOW MANY COLLECTION CENTERS, PROCESSING AREAS AND ADDITIONAL OFFICES YOU HAVE.</p>
				<input type="text" class="form-control" name="resp8">
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<p>9.	IF YOU HAVE COLLECTION CENTERS, PROCESSING AREAS OR ADDITIONAL OFFICES, PLEASE ATTACH A GENERAL MAP INDICATING WHERE THEY ARE LOCATED</p>
				<input type="text" class="form-control" name="resp9">
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<p>10.	IF THE APPLICANT HAS AN INTERNAL CONTROL SYSTEM FOR COMPLYING WITH THE CRITERIA IN THE GENERAL STANDARD OF THE SMALL PRODUCERS’ SYMBOL, PLEASE EXPLAIN HOW IT WORKS.</p>
				<input type="text" class="form-control" name="resp10">
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<p>11.	FILL OUT THE TABLE ACCORDING YOUR CERTIFICATIONS, (example: EU, NOP, JASS, FLO, etc).</p>
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
						<td>
							<div class="col-xs-6">YES<input type="radio" class="form-control" name="interrumpida[0]" value="SI"></div>
							<div class="col-xs-6">NO<input type="radio" class="form-control" name="interrumpida[0]" value="NO"></div>
						</td>
					</tr>

				</table>			
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<p>12.-	 ACCORDING THE CERTIFICATIONS, IN ITS MOST RECENT INTERNAL AND EXTERNAL EVALUATIONS, HOW MANY CASES OF NON COMPLIANCE WERE INDENTIFIED? PLEASE EXPLAIN IF THEY HAVE BEEN RESOLVED OR WHAT THEIR STATUS IS?</p>
				<textarea class="form-control" name="resp12" id="" cols="30" rows="3"></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<p>13.-	OF THE APPLICANT’S TOTAL TRADING DURING THE PREVIOUS CYCLE, WHAT PERCENTAGE WAS CONDUCTED UNDER THE SCHEMES OF CERTIFICATION FOR ORGANIC, FAIR TRADE AND/OR THE SMALL PRODUCERS’ SYMBOL?</p>
				<input type="text" class="form-control" name="resp13">
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<p>14.- DID YOU HAVE SPP PURCHASES DURING THE PREVIOUS CERTIFICATION CYCLE?</p>
				<div class="col-xs-6">
					YES <input type="radio" class="form-control" name="resp14" onclick="mostrar_ventas()" id="resp14" value="SI">
				</div>
				<div class="col-xs-6">
					NO <input type="radio" class="form-control" name="resp14" onclick="ocultar_ventas()" id="resp14" value="NO">
				</div>

	          <tr >
	            <td colspan="2">
	              15.-  IF YOUR RESPONSE WAS POSSITIVE, PLEASE MARK WITH AN 'X 'THE RANGE OF THE TOTAL VALUE SPP PURCHASES ACCORDING TO THE FOLLOWING TABLE:

	              <div class="well col-xs-12 " id="tablaVentas" style="display:none;">
	                
	                  <div class="col-xs-6"><p>UP TO  $3,000 USD</p></div>
	                  <div class="col-xs-6 "><input type="radio" name="resp14_15" class="form-control" id="ver" onclick="ocultar()" value="HASTA $3,000 USD"></div>
	                
	                
	                  <div class="col-xs-6"><p>BETWEEN $3,000 AND $10,000 USD</p></div>
	                  <div class="col-xs-6"><input type="radio" name="resp14_15" class="form-control" id="ver" onclick="ocultar()" value="ENTRE $3,000 Y $10,000 USD"></div>
	                
	                
	                  <div class="col-xs-6"><p>BETWEEN $10,000 AND $25,000 USD</p></div>
	                  <div class="col-xs-6"><input type="radio" name="resp14_15" class="form-control"  id="ver" onclick="ocultar()" value="ENTRE $10,000 A $25,000 USD"></div>
	                
	                  <div class="col-xs-6"><p>MORE THAT $25,000 USD <sup>*</sup><br><h6><sup>*</sup>Specify the quantity.</h6></p></div>
	                  <div class="col-xs-6"><input type="radio" name="resp14_15" class="form-control" id="exampleInputEmail1" onclick="mostrar()" value="mayor">
	                   <input type="text" name="resp14_15_1" class="form-control" id="oculto" style='display:none;' placeholder="Specify the quantity">
	                  </div>
	                
	              </div>
	          
	            </td>
	          </tr>

			</td>
		</tr>
		<tr>
			<td colspan="2">
				<p>16.- ESTIMATED DATE FOR BEGINNING TO USE THE SMALL PRODUCERS’ SYMBOL</p>
				<input type="text" class="form-control" name="resp16">
			</td>
		</tr>
		<tr class="text-center alert alert-success">
			<td colspan="2">
				INFORMATION ON PRODUCTS FOR WHICH APPLICANT WISHES TO USE SYMBOL <sup>6</sup>
			</td>
		</tr>

		<tr>
			<td colspan="2">
				<table class="table table-bordered" id="tablaProductos">
					<tr>
						<td>Product</td>
						<td>Total Estimated Volume to be Sold</td>
						<td>Volume of Finished Product</td>
						<td>Volume of Raw Material</td>
						<td>Country/Countries of Origin (<small>Please separate comma</small>)</td>
						<td>Country/Countries of Destination (<small>Please separate comma</small>)</td>
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
							<input type="text" class="form-control" name="volumenEstimado[0]" id="exampleInputEmail1" placeholder="Volume">
						</td>
						<td>
							<input type="text" class="form-control" name="volumenTerminado[0]" placeholder="Volume">
						</td>
						<td>
							<input type="text" class="form-control" name="materia[0]" id="exampleInputEmail1" placeholder="Volume">
						</td>
						<!--<td >
					        <select  class="form-control chosen-select-deselect" data-placeholder="Buscar por país" name="paisOrigen0[]" id="" multiple>
					          <option value="">Selecciona un país</option>
					          <?php 
					            $query = "SELECT * FROM paises";
					            $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
					            while($row_paises = mysql_fetch_assoc($ejecutar)){
					          ?>
					            <option value="<?php echo utf8_encode($row_paises['nombre']);?>"><?php echo utf8_encode($row_paises['nombre']) ?></option>
					          <?php
					            }
					          ?>
					        </select>
					        <input type="hidden" name="filtroPais" value="2">
						</td>-->
						<td>
							<textarea class="form-control" name="paisOrigen[0]" id="" cols="30" rows="3" placeholder="Origin"></textarea>	
						</td>
						<td>	    
							<textarea class="form-control" name="paisDestino[0]" id="" cols="30" rows="3" placeholder="Destination"></textarea>
						</td>
					</tr>				
					<tr>
						<td colspan="8">
							<h6><sup>6</sup> Information provided in this section will be handled with complete confidentiality. Please insert additional lines necessary.</h6>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr class="text-center alert alert-success">
			<td colspan="2">COMMITMENTS</td>
		</tr>
		<tr>
			<td colspan="2">
				<p>1.	By signing and sending in this document, the applicant expresses its interest in receiving a proposal for Registration with the Small Producers’ Symbol.</p>
				<p>2.	The registration process will begin when it is confirmed that the payment corresponding to the proposal has been received.</p>
				<p>3.	The fact that this application is delivered and received does not guarantee that the results of the registration process will be positive. </p>
				<p>4.	The applicant will become familiar with and comply with all the applicable requirements in the General Standard of the Small Producers’ Symbol for Buyers, Collective Trading Companies owned by Small Producers’ Organizations, Intermediaries and Maquila Companies, including both Critical and Minimum Criteria, and independently of the type of evaluation conducted.</p>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<p>Name of the person who is responsible for the accuracy of the information on this form, and who, on behalf of the Applicant, will follow up on the application:</p>
				<input type="text" class="form-control" name="responsable" placeholder="Name of the person" required>
			</td>
		</tr>
		<tr style="background-color:#ccc">
			<td colspan="2">
				<p>Entity who receives the application:</p>
				<input type="text" class="form-control" name="" disabled>
			</td>
		</tr>
		<tr>
			<td style="border:hidden">
				<div class="col-xs-12">
					<input type="hidden" name="MM_insert" value="form1">
					<input type="hidden" name="fecha_elaboracion" value="<?php echo time()?>">
					<input type="hidden" name="status_publico" value="<?php echo $estadoPublico;?>">
					<input type="hidden" name="status_interno" value="<?php echo $estadoInterno;?>">
					<input type="hidden" name="mensaje" value="Action added correctly" />
					<input type="hidden" name="idcom" value="<?php echo $_SESSION['idcom']?>">
					<input type="hidden" name="abreviacion" value="<?php echo $row_com['abreviacion'];?>">
					<input type="hidden" name="nombreCOM" value="<?php echo $row_com['nombre']; ?>">
					<input type="hidden" name="paisCOM" value="<?php echo $row_com['pais']; ?>">
				</div>

			    <button style="width:200px;" class="btn btn-primary" type="submit" value="Enviar Solicitud" aria-label="Left Align" onclick="return validar()">
			      <span class="glyphicon glyphicon-open-file" aria-hidden="true"></span> Send
			    </button>

				<!--<input type="submit" class="btn btn-primary" style="width:200px" value="Enviar Solicitud">-->
			</td>
		</tr>
 	</tbody>
 </table>	
</form>



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

	  cell1.innerHTML = '<input type="text" class="form-control" name="certificadora['+contador+']" id="exampleInputEmail1" placeholder="CERTIFICATION">';
	  cell2.innerHTML = '<input type="text" class="form-control" name="certificacion['+contador+']" id="exampleInputEmail1" placeholder="CERTIFICATION ENTITY">';
	  cell3.innerHTML = '<input type="text" class="form-control" name="ano_inicial['+contador+']" id="exampleInputEmail1" placeholder="INITIAL YEAR">';
	  cell4.innerHTML = '<div class="col-xs-6">YES<input type="radio" class="form-control" name="interrumpida['+contador+']" value="SI"></div><div class="col-xs-6">NO<input type="radio" class="form-control" name="interrumpida['+contador+']" value="NO"></div>';
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


	  cell1.innerHTML = '<input type="text" class="form-control" name="resp6_empresa['+contador+']" id="exampleInputEmail1" placeholder="COMPANY">';
	  cell2.innerHTML = '<input type="text" class="form-control" name="resp6_servicio['+contador+']" id="exampleInputEmail1" placeholder="SERVICE">';

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

	function mostrar_empresas(){
		document.getElementById('contenedor_tablaEmpresas').style.display = 'block';
	}
	function ocultar_empresas()
	{
		document.getElementById('contenedor_tablaEmpresas').style.display = 'none';
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
	  
	  cell2.innerHTML = '<input type="text" class="form-control" name="volumenEstimado['+cont+']" id="exampleInputEmail1" placeholder="Volume">';
	  
	  cell3.innerHTML = '<input type="text" class="form-control" name="volumenTerminado['+cont+']" id="exampleInputEmail1" placeholder="Volume">';
	  
	  cell4.innerHTML = '<input type="text" class="form-control" name="materia['+cont+']" id="exampleInputEmail1" placeholder="Volume">';
	  
	  //cell4.innerHTML = '<input type="text" class="form-control" name="destino['+cont+']" id="exampleInputEmail1" placeholder="Destino">';
	  
	  //cell6.innerHTML = 'SI <input type="radio" name="marca_propia'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="marca_propia'+cont+'['+cont+']" id="" value="NO">';

	  //cell5.innerHTML = '<select  class="form-control chosen-select-deselect" data-placeholder="Buscar por país" name="paisOrigen0[]" id="" multiple><option value="">Selecciona un país</option></select>';
	  
	  cell5.innerHTML = '<textarea class="form-control" name="paisOrigen['+cont+']" id="" cols="30" rows="3" placeholder="Origin"></textarea>';

	  cell6.innerHTML = '<textarea class="form-control" name="paisDestino['+cont+']" id="" cols="30" rows="3" placeholder="Destination"></textarea>';


	  }

	}	

</script>


<?
mysql_free_result($pais);

mysql_free_result($oc);
?>