<?php require_once('../Connections/dspp.php'); 
      require_once('../Connections/mail.php');

      /*include_once("../../PHPMailer/class.phpmailer.php");
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
        $mail->AddReplyTo("cert@spp.coop");*/
?>
<?php
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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {

	$idopp = $_POST['idopp'];
	$fecha_actual = time();
  	$array_resp4 = "";
	$op_resp13 = "";
	$op_resp12 = "";

	if(isset($_POST['op_resp4'])){
		if(is_array($_POST['op_resp4'])){
			$resp4 = $_POST['op_resp4'];

			for ($i=0; $i < count($resp4) ; $i++) { 
				$array_resp4 .= $resp4[$i]." - ";
			}
		}else{
				$array_resp4 = NULL;
		}
	}else{
		$array_resp4 = NULL;
	}

	
	if(isset($_POST['op_resp13'])){
		if(isset($_POST['op_resp13']) && $_POST['op_resp13'] == "mayor"){
			$op_resp13 = $_POST['op_resp13_1'];
		}else{
			$op_resp13 = $_POST['op_resp13'];
		}
	}else{
		$op_resp13 = NULL;
	}

	if(isset($_POST['op_resp12'])){
		$op_resp12 = $_POST['op_resp12'];
	}else{
		$op_resp12 = "";
	}

  $rutaArchivo = "../../archivos/oppArchivos/croquis/";

	if(!empty($_FILES['op_resp15']['name'])){
	    $_FILES["op_resp15"]["name"];
	      move_uploaded_file($_FILES["op_resp15"]["tmp_name"], $rutaArchivo.date("Ymd H:i:s")."_".$_FILES["op_resp15"]["name"]);
	      $croquis = $rutaArchivo.basename(date("Ymd H:i:s")."_".$_FILES["op_resp15"]["name"]);
	}else{
		$croquis = NULL;
	}

/*************************************************************************************************************************/
/*************************************************************************************************************************/
/*************************************************************************************************************************/
  setlocale(LC_ALL, 'en_US.UTF8');

    if($_POST['idoc'] == "99"){
    	$queryOC = "SELECT * FROM oc";
    	$ejecutar = mysql_query($queryOC,$dspp) or die(mysql_error());
    	while($infoOC = mysql_fetch_assoc($ejecutar)){


  			$insertSQL = sprintf("INSERT INTO solicitud_certificacion (idoc, idopp, ciudad, ruc, p1_nombre, p1_cargo, p1_telefono, p1_email, p2_nombre, p2_cargo, p2_telefono, p2_email, adm_nom1, adm_nom2, adm_tel1, adm_tel2, adm_email1, adm_email2, resp1, resp2, resp3, resp4, op_resp1, op_area1, op_area2, op_area3, op_area4, op_resp2, op_resp3, op_resp4, op_resp5, op_resp6, op_resp7, op_resp8, op_resp10, op_resp11, op_resp12, op_resp13, op_resp14, op_resp15, fecha_elaboracion, status, status_publico, responsable) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s,%s, %s, %s, %s, %s, %s, %s, %s, %s,%s, %s, %s, %s, %s, %s, %s, %s, %s,%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
  					   GetSQLValueString($infoOC['idoc'], "int"),
                       GetSQLValueString($_POST['idopp'], "int"),
                       GetSQLValueString($_POST['ciudadOPP'], "text"),
                       GetSQLValueString($_POST['ruc'], "text"),
                       GetSQLValueString($_POST['p1_nombre'], "text"),
                       GetSQLValueString($_POST['p1_cargo'], "text"),
                       GetSQLValueString($_POST['p1_telefono'], "text"),
                       GetSQLValueString($_POST['p1_email'], "text"),
                       GetSQLValueString($_POST['p2_nombre'], "text"),
                       GetSQLValueString($_POST['p2_cargo'], "text"),
                       GetSQLValueString($_POST['p2_telefono'], "text"),
                       GetSQLValueString($_POST['p2_email'], "text"),
                       GetSQLValueString($_POST['adm_nom1'], "text"),
                       GetSQLValueString($_POST['adm_nom2'], "text"),
                       GetSQLValueString($_POST['adm_tel1'], "text"),
                       GetSQLValueString($_POST['adm_tel2'], "text"),
                       GetSQLValueString($_POST['adm_email1'], "text"),
                       GetSQLValueString($_POST['adm_email2'], "text"),
                       GetSQLValueString($_POST['resp1'], "text"),
                       GetSQLValueString($_POST['resp2'], "text"),
                       GetSQLValueString($_POST['resp3'], "text"),
                       GetSQLValueString($_POST['resp4'], "text"),
                       GetSQLValueString($_POST['op_resp1'], "text"),
                       GetSQLValueString($_POST['op_area1'], "text"),
                       GetSQLValueString($_POST['op_area2'], "text"),
                       GetSQLValueString($_POST['op_area3'], "text"),
                       GetSQLValueString($_POST['op_area4'], "text"),
                       GetSQLValueString($_POST['op_resp2'], "text"),
                       GetSQLValueString($_POST['op_resp3'], "text"),
                       GetSQLValueString($array_resp4, "text"),
                       GetSQLValueString($_POST['op_resp5'], "text"),
                       GetSQLValueString($_POST['op_resp6'], "text"),
                       GetSQLValueString($_POST['op_resp7'], "text"),
                       GetSQLValueString($_POST['op_resp8'], "text"),
                       GetSQLValueString($_POST['op_resp10'], "text"),
                       GetSQLValueString($_POST['op_resp11'], "text"),
                       GetSQLValueString($op_resp12, "text"),
                       GetSQLValueString($op_resp13, "text"),
                       GetSQLValueString($_POST['op_resp14'], "text"),
                       GetSQLValueString($croquis, "text"),
                       GetSQLValueString($_POST['fecha_elaboracion'], "int"),
                       GetSQLValueString($_POST['procedimiento'], "int"),
                        GetSQLValueString($_POST['status_publico'], "int"),
                       GetSQLValueString($_POST['responsable'], "text"));

					  mysql_select_db($database_dspp, $dspp);
					  $Result1 = mysql_query($insertSQL, $dspp) or die(mysql_error());

					  $idsolicitud_certificacion = mysql_insert_id($dspp); 



				  	
				  	/*$certificacion = $_POST['certificacion'];
					$certificadora = $_POST['certificadora'];
					$ano_inicial = $_POST['ano_inicial'];
					$interrumpida = $_POST['interrumpida'];

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
					}*/

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


			$charset='utf-8'; // o 'UTF-8'


			$producto = $_POST['producto'];
			$volumen = $_POST['volumen'];
			$materia = $_POST['materia'];
			$destino = $_POST['destino'];




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


					    $insertSQL = sprintf("INSERT INTO productos (idsolicitud_certificacion, producto, volumen, terminado, materia, destino, marca_propia, marca_cliente, sin_cliente) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
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





		   $query = "SELECT * FROM oc WHERE idoc = $infoOC[idoc]";
		   $oc = mysql_query($query,$dspp) or die(mysql_error());
		   $row_oc = mysql_fetch_assoc($oc);


		    $nombre = $_POST['nombreOPP'];
		    $abreviacion = $_POST['abreviacion'];
		    $pais = $_POST['paisOPP'];
		    $nombreOC = $row_oc['nombre'];
		    $fecha_elaboracion = $_POST['fecha_elaboracion'];
		    $producto = $_POST['producto'];
		    $telefono1 = $_POST['p1_telefono'];
		    $direccion = $_POST['direccionOPP'];
		    $ciudad = $_POST['ciudadOPP'];
		    $emailOPP1 = $_POST['p1_email'];
		    $emailOPP2 = $_POST['p2_email'];
		    $fecha = date("d/m/Y", $fecha_elaboracion);
		    //$correo = $_POST['p1_email'];
		    //$correo = $_POST['p2_email'];

		    $paisEstado = $pais.' / '.$ciudad;


		    $destinatario = $row_oc['email'];
		    $asunto = "D-SPP Solicitud de Certificación para Organizaciones de Pequeños Productores"; 


		$mensajeEmail = '
			<html>
			<head>
				<meta charset="utf-8">
			</head>
			<body>
			
				<table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
				  <tbody>
		            <tr>
		              <th rowspan="7" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
		              <th scope="col" align="left" width="280"><strong>Notificación de Intenciones / Notification of Intentions</strong></th>
		            </tr>
		            <tr>
		              <td style="padding-top:10px;"><i>Para poder consultar la solicitud, por favor iniciar sesión en su cuenta de OC en el siguiente enlace: <a href="http://d-spp.org/?OC" target="_new">www.d-spp.org/?OC</a></i>.</td>
		            </tr>
				    <tr>
				      <td align="left">Teléfono / phone OPP: '.$telefono1.'</td>
				    </tr>
				    <tr>
				      <td align="left">'.$direccion.'</td>
				    </tr>
				    <tr>
				      <td align="left">'.$paisEstado.'</td>
				    </tr>
				    <tr>
				      <td align="left" style="color:#ff738a;">Email: '.$emailOPP1.'</td>
				    </tr>
				    <tr>
				      <td align="left" style="color:#ff738a;">Email: '.$emailOPP2.'</td>
				    </tr>

				    <tr>
				      <td colspan="2">
				        <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;" border="1" width="650px">
				          <tbody>
				            <tr style="font-size: 12px; text-align:center; background-color:#dff0d8; color:#3c763d;" height="50px;">
				              <td width="130px">Nombre de la organización/Organization name</td>
				              <td width="130px">Abreviación / Short name</td>
				              <td width="130px">País / Country</td>
				              <td width="130px">Organismo de Certificación / Certification Entity</td>
				           
				              <td width="130px">Fecha de solicitud/Date of application</td>
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

				    <tr>
				      <td style="text-align:justify;" colspan="2">
				        FUNDEPPO publica y notifica las “Intenciones de Certificación, Registro o Autorización” basada en nuevas solicitudes de: 1) Certificación de Organizaciones de Pequeños Productores, 2) Registro de Compradores y otros actores y 3) Autorización de Organismos de Certificación, con el objetivo de informarles y recibir las eventuales objeciones contra la incorporación de los solicitantes.
				        Estas eventuales objeciones presentadas deben estar sustentadas con información concreta y verificable con respecto a incumplimientos de la Normatividad del SPP y/o nuestro Código de Conducta (disponibles en <a href="http://www.spp.coop/">www.spp.coop</a>, en el área de Funcionamiento). Las objeciones presentadas y enviadas a <a href="cert@spp.coop">cert@spp.coop</a> serán tomadas en cuenta en los procesos de certificación, registro o autorización.
				        Estas notificaciones son enviadas por FUNDEPPO en un lapso menor a 24 horas a partir del momento en que le llegue la solicitud correspondiente. Si se presentan objeciones antes de que el solicitante se Certifique, Registre o Autorice su tratamiento por parte del Organismo de Certificación debe ser parte de la misma evaluación documental. Si la objeción se presenta cuando el Solicitante ya esta Certificado se aplica el Procedimiento de Inconformidades del Símbolo de Pequeños Productores. Las nuevas intenciones de Certificación, Registro o Autorización, se detallan al inicio de este documento.  
				        <br><br>
				        FUNDEPPO publishes and notifies the “Certification, Registration and Authorization Intentions” based on new applications submitted for: 1) Certification of Small Producers’ Organizations, 2) Registration of Buyers and other stakeholders, and 3) Authorization of Certification Entities, with the objective of keeping you informed and receiving any objections to the incorporation of any new applicants into the system.
				        Any objections submitted must be supported with concrete, verifiable information regarding non-compliance with the Standards and/or Code of Conduct of the Small Producers’ Symbol (available at <a href="http://www.spp.coop/">www.spp.coop</a> in the section on Operation). The objections submitted and sent to <a href="cert@spp.coop">cert@spp.coop</a> will be taken into consideration during certification, registration and authorization processes.
				        These notifications are sent by FUNDEPPO in a period of less than 24 hours from the time a corresponding application is received. If objections are presented before getting the Certification, Registration or Authorization, the Certification Entity must incorporate them as part of the same evaluation-process. If the objection is presented when the applicant has already been certified, the SPP Dissents\' Procedure has to be applied. The new intentions for Certification, Registration and Authorization are detailed at the beginning (of this document.
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
        $mail->Body = utf8_decode($mensajeEmail);
        $mail->MsgHTML(utf8_decode($mensajeEmail));
        $mail->Send();
        $mail->ClearAddresses();


    	}
/*************************************************************************************************************************/
/*************************************************************************************************************************/
/*************************************************************************************************************************/
    }else{

		  $insertSQL = sprintf("INSERT INTO solicitud_certificacion (idoc, idopp, ciudad, ruc, p1_nombre, p1_cargo, p1_telefono, p1_email, p2_nombre, p2_cargo, p2_telefono, p2_email, adm_nom1, adm_nom2, adm_tel1, adm_tel2, adm_email1, adm_email2, resp1, resp2, resp3, resp4, op_resp1, op_area1, op_area2, op_area3, op_area4, op_resp2, op_resp3, op_resp4, op_resp5, op_resp6, op_resp7, op_resp8, op_resp10, op_resp11, op_resp12, op_resp13, op_resp14, op_resp15, fecha_elaboracion, status, status_publico, responsable) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s,%s, %s, %s, %s, %s, %s, %s, %s, %s,%s, %s, %s, %s, %s, %s, %s, %s, %s,%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
		  					   GetSQLValueString($_POST['idoc'], "int"),
		                       GetSQLValueString($_POST['idopp'], "int"),
		                       GetSQLValueString($_POST['ciudadOPP'], "text"),
		                       GetSQLValueString($_POST['ruc'], "text"),
		                       GetSQLValueString($_POST['p1_nombre'], "text"),
		                       GetSQLValueString($_POST['p1_cargo'], "text"),
		                       GetSQLValueString($_POST['p1_telefono'], "text"),
		                       GetSQLValueString($_POST['p1_email'], "text"),
		                       GetSQLValueString($_POST['p2_nombre'], "text"),
		                       GetSQLValueString($_POST['p2_cargo'], "text"),
		                       GetSQLValueString($_POST['p2_telefono'], "text"),
		                       GetSQLValueString($_POST['p2_email'], "text"),
		                       GetSQLValueString($_POST['adm_nom1'], "text"),
		                       GetSQLValueString($_POST['adm_nom2'], "text"),
		                       GetSQLValueString($_POST['adm_tel1'], "text"),
		                       GetSQLValueString($_POST['adm_tel2'], "text"),
		                       GetSQLValueString($_POST['adm_email1'], "text"),
		                       GetSQLValueString($_POST['adm_email2'], "text"),
		                       GetSQLValueString($_POST['resp1'], "text"),
		                       GetSQLValueString($_POST['resp2'], "text"),
		                       GetSQLValueString($_POST['resp3'], "text"),
		                       GetSQLValueString($_POST['resp4'], "text"),
		                       GetSQLValueString($_POST['op_resp1'], "text"),
		                       GetSQLValueString($_POST['op_area1'], "text"),
		                       GetSQLValueString($_POST['op_area2'], "text"),
		                       GetSQLValueString($_POST['op_area3'], "text"),
		                       GetSQLValueString($_POST['op_area4'], "text"),
		                       GetSQLValueString($_POST['op_resp2'], "text"),
		                       GetSQLValueString($_POST['op_resp3'], "text"),
		                       GetSQLValueString($array_resp4, "text"),
		                       GetSQLValueString($_POST['op_resp5'], "text"),
		                       GetSQLValueString($_POST['op_resp6'], "text"),
		                       GetSQLValueString($_POST['op_resp7'], "text"),
		                       GetSQLValueString($_POST['op_resp8'], "text"),
		                       GetSQLValueString($_POST['op_resp10'], "text"),
		                       GetSQLValueString($_POST['op_resp11'], "text"),
		                       GetSQLValueString($op_resp12, "text"),
		                       GetSQLValueString($op_resp13, "text"),
		                       GetSQLValueString($_POST['op_resp14'], "text"),
		                       GetSQLValueString($croquis, "text"),
		                       GetSQLValueString($_POST['fecha_elaboracion'], "int"),
		                       GetSQLValueString($_POST['procedimiento'], "int"),
		                        GetSQLValueString($_POST['status_publico'], "int"),
		                       GetSQLValueString($_POST['responsable'], "text"));

		  mysql_select_db($database_dspp, $dspp);
		  $Result1 = mysql_query($insertSQL, $dspp) or die(mysql_error());

		  $idsolicitud_certificacion = mysql_insert_id($dspp); 


			$idexterno = $idsolicitud_certificacion;
			$identificador = "SOLICITUD";
			$status = $_POST['procedimiento'];

			$queryFecha = "INSERT INTO fecha(fecha, idexterno, idopp, identificador, status) VALUES($fecha_actual, $idexterno, $idopp, '$identificador', $status)";


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


			$charset='utf-8'; // o 'UTF-8'


			$producto = $_POST['producto'];
			$volumen = $_POST['volumen'];
			$materia = $_POST['materia'];
			$destino = $_POST['destino'];
			/*$marca_propia = $_POST['marca_propia'];
			$marca_cliente = $_POST['marca_cliente'];
			$sin_cliente = $_POST['sin_cliente'];*/

			/*for ($i=0;$i<count($producto);$i++) { VERSION CON FALLAS
				if($producto[$i] != NULL){

						$array1[$i] = "terminado".$i; 
						$array2[$i] = "marca_propia".$i;
						$array3[$i] = "marca_cliente".$i;
						$array4[$i] = "sin_cliente".$i;

						$terminado = $_POST[$array1[$i]];
						$marca_propia = $_POST[$array2[$i]];
						$marca_cliente = $_POST[$array3[$i]];
						$sin_cliente = $_POST[$array4[$i]];

					    $insertSQL = sprintf("INSERT INTO productos (idsolicitud_certificacion, producto, volumen, terminado, materia, destino, marca_propia, marca_cliente, sin_cliente) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
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
			}*/



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


					    $insertSQL = sprintf("INSERT INTO productos (idsolicitud_certificacion, producto, volumen, terminado, materia, destino, marca_propia, marca_cliente, sin_cliente) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
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



		   $query = "SELECT * FROM oc WHERE idoc = $_POST[idoc]";
		   $oc = mysql_query($query,$dspp) or die(mysql_error());
		   $row_oc = mysql_fetch_assoc($oc);

		   	$idopp = $_POST['idopp'];
		   	$idoc = $_POST['idoc'];


		    $nombre = $_POST['nombreOPP'];
		    $abreviacion = $_POST['abreviacion'];
		    $pais = $_POST['paisOPP'];
		    $nombreOC = $row_oc['nombre'];
		    $fecha_elaboracion = $_POST['fecha_elaboracion'];
		    $producto = $_POST['producto'];
		    $telefono1 = $_POST['p1_telefono'];
		    $direccion = $_POST['direccionOPP'];
		    $ciudad = $_POST['ciudadOPP'];
		    $emailOPP1 = $_POST['p1_email'];
		    $emailOPP2 = $_POST['p2_email'];
		    $fecha = date("d/m/Y", $fecha_elaboracion);
		    //$correo = $_POST['p1_email'];
		    //$correo = $_POST['p2_email'];

		    $paisEstado = $pais.' / '.$ciudad;


		    $destinatario = $row_oc['email'];
		    $asunto = "D-SPP Solicitud de Certificación para Organizaciones de Pequeños Productores"; 
 			$asunto2 = "D-SPP - Cotización Certificación para Organizaciones de Pequeños Productores"; 

		$mensajeEmail = '
			<html>
			<head>
				<meta charset="utf-8">
			</head>
			<body>
			
				<table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
				  <tbody>
		            <tr>
		              <th rowspan="7" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
		              <th scope="col" align="left" width="280"><strong>Notificación de Intenciones / Notification of Intentions</strong></th>
		            </tr>
		            <tr>
		              <td style="padding-top:10px;"><i>Para poder consultar la solicitud, por favor iniciar sesión en su cuenta de OC en el siguiente enlace: <a href="http://d-spp.org/?OC" target="_new">www.d-spp.org/?OC</a></i>.</td>
		            </tr>
				    <tr>
				      <td align="left">Teléfono / phone OPP: '.$telefono1.'</td>
				    </tr>
				    <tr>
				      <td align="left">'.$direccion.'</td>
				    </tr>
				    <tr>
				      <td align="left">'.$paisEstado.'</td>
				    </tr>
				    <tr>
				      <td align="left" style="color:#ff738a;">Email: '.$emailOPP1.'</td>
				    </tr>
				    <tr>
				      <td align="left" style="color:#ff738a;">Email: '.$emailOPP2.'</td>
				    </tr>

				    <tr>
				      <td colspan="2">
				        <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;" border="1" width="650px">
				          <tbody>
				            <tr style="font-size: 12px; text-align:center; background-color:#dff0d8; color:#3c763d;" height="50px;">
				              <td width="130px">Nombre de la organización/Organization name</td>
				              <td width="130px">Abreviación / Short name</td>
				              <td width="130px">País / Country</td>
				              <td width="130px">Organismo de Certificación / Certification Entity</td>
				           
				              <td width="130px">Fecha de solicitud/Date of application</td>
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

				    <tr>
				      <td style="text-align:justify;" colspan="2">
				        FUNDEPPO publica y notifica las “Intenciones de Certificación, Registro o Autorización” basada en nuevas solicitudes de: 1) Certificación de Organizaciones de Pequeños Productores, 2) Registro de Compradores y otros actores y 3) Autorización de Organismos de Certificación, con el objetivo de informarles y recibir las eventuales objeciones contra la incorporación de los solicitantes.
				        Estas eventuales objeciones presentadas deben estar sustentadas con información concreta y verificable con respecto a incumplimientos de la Normatividad del SPP y/o nuestro Código de Conducta (disponibles en <a href="http://www.spp.coop/">www.spp.coop</a>, en el área de Funcionamiento). Las objeciones presentadas y enviadas a <a href="cert@spp.coop">cert@spp.coop</a> serán tomadas en cuenta en los procesos de certificación, registro o autorización.
				        Estas notificaciones son enviadas por FUNDEPPO en un lapso menor a 24 horas a partir del momento en que le llegue la solicitud correspondiente. Si se presentan objeciones antes de que el solicitante se Certifique, Registre o Autorice su tratamiento por parte del Organismo de Certificación debe ser parte de la misma evaluación documental. Si la objeción se presenta cuando el Solicitante ya esta Certificado se aplica el Procedimiento de Inconformidades del Símbolo de Pequeños Productores. Las nuevas intenciones de Certificación, Registro o Autorización, se detallan al inicio de este documento.  
				        <br><br>
				        FUNDEPPO publishes and notifies the “Certification, Registration and Authorization Intentions” based on new applications submitted for: 1) Certification of Small Producers’ Organizations, 2) Registration of Buyers and other stakeholders, and 3) Authorization of Certification Entities, with the objective of keeping you informed and receiving any objections to the incorporation of any new applicants into the system.
				        Any objections submitted must be supported with concrete, verifiable information regarding non-compliance with the Standards and/or Code of Conduct of the Small Producers’ Symbol (available at <a href="http://www.spp.coop/">www.spp.coop</a> in the section on Operation). The objections submitted and sent to <a href="cert@spp.coop">cert@spp.coop</a> will be taken into consideration during certification, registration and authorization processes.
				        These notifications are sent by FUNDEPPO in a period of less than 24 hours from the time a corresponding application is received. If objections are presented before getting the Certification, Registration or Authorization, the Certification Entity must incorporate them as part of the same evaluation-process. If the objection is presented when the applicant has already been certified, the SPP Dissents Procedure has to be applied. The new intentions for Certification, Registration and Authorization are detailed at the beginning (of this document).
				      </td>
				    </tr>
				  </tbody>
				</table>

			</body>
			</html>
		';


        $queryMensaje = "INSERT INTO mensajes(idopp, idoc,asunto, mensaje, destinatario, remitente, fecha) VALUES($idopp, $idoc, '$asunto', '$mensajeEmail', 'OC', 'OPP', $fecha_actual)";
        $ejecutar = mysql_query($queryMensaje,$dspp) or die(mysql_error());



        $mail->AddAddress($destinatario);

        //$mail->Username = "soporte@d-spp.org";
        //$mail->Password = "/aung5l6tZ";
        $mail->Subject = utf8_decode($asunto);
        $mail->Body = utf8_decode($mensajeEmail);
        $mail->MsgHTML(utf8_decode($mensajeEmail));
        $mail->Send();
        $mail->ClearAddresses();

    }
/*************************************************************************************************************************/
/*************************************************************************************************************************/
/*************************************************************************************************************************/
    

    		//INSERTAMOS LOS DATOS DE CONTACTO DE LA SOLICITUD, DENTRO DE LOS CONTACTO DEL OPP
    		$idopp = $_POST['idopp'];
    		if(!empty($_POST['p1_nombre'])){
    			$nombre = $_POST['p1_nombre'];
    			$cargo = $_POST['p1_cargo'];
    			$telefono = $_POST['p1_telefono'];
    			$email = $_POST['p1_telefono'];

    			$query = "INSERT INTO contacto(idopp, contacto, cargo, telefono1, email1) VALUES($idopp ,'$nombre', '$cargo', '$telefono', '$email')";
    			$ejecutar = mysql_query($query,$dspp) or die(mysql_error());
    		}
    		if(!empty($_POST['p2_nombre'])){
    			$nombre = $_POST['p2_nombre'];
    			$cargo = $_POST['p2_cargo'];
    			$telefono = $_POST['p2_telefono'];
    			$email = $_POST['p2_telefono'];

    			$query = "INSERT INTO contacto(idopp, contacto, cargo, telefono1, email1) VALUES($idopp ,'$nombre', '$cargo', '$telefono', '$email')";
    			$ejecutar = mysql_query($query,$dspp) or die(mysql_error());
    		}
    		if (!empty($_POST['adm_nom1'])) {
    			$nombre = $_POST['adm_nom1'];
    			$cargo = 'Administrador';
    			$telefono = $_POST['adm_tel1'];
    			$email = $_POST['adm_email1'];

    			$query = "INSERT INTO contacto(idopp, contacto, cargo, telefono1, email1) VALUES($idopp ,'$nombre', '$cargo', '$telefono', '$email')";
    			$ejecutar = mysql_query($query,$dspp) or die(mysql_error());
    		}
    		if (!empty($_POST['adm_nom2'])) {
    			$nombre = $_POST['adm_nom2'];
    			$cargo = 'Administrador';
    			$telefono = $_POST['adm_tel2'];
    			$email = $_POST['adm_email2'];

    			$query = "INSERT INTO contacto(idopp, contacto, cargo, telefono1, email1) VALUES($idopp ,'$nombre', '$cargo', '$telefono', '$email')";
    			$ejecutar = mysql_query($query,$dspp) or die(mysql_error());
    		}

			//actualizamos OPP para cambiar el estado de su solicitud
			$procedimiento = $_POST['procedimiento'];
			$query = "UPDATE opp SET 
			nombre = '$_POST[nombreOPP]',
			sitio_web = '$_POST[sitio_web]',
			email = '$_POST[emailOPP]',
			telefono = '$_POST[telefonoOPP]',
			pais = '$_POST[paisOPP]',
			ciudad = '$_POST[ciudadOPP]',
			direccion = '$_POST[direccionOPP]',
			direccion_fiscal = '$_POST[direccion_fiscal]',
			rfc = '$_POST[rfc]',
			ruc = '$_POST[ruc]',
			estado = $procedimiento WHERE idopp = $_SESSION[idopp]";
			$actualizar = mysql_query($query,$dspp) or die(mysql_error());

			//llenamos el registro de la fecha para llevar un control de las acciones que se han realizado dentro del sistema

			/*CHECAR ESTA VERSION YA QUE NO ME ACUERDO DE DONDE VENIA EL IDEXTERNO XD
				
			$fecha = time();
			$idexterno = $_POST['idoc'];
			$identificador = "OC";
			$estatus = $_POST['procedimiento'];

			$queryFecha = "INSERT INTO fecha(fecha, idexterno, identificador, status) VALUES($fecha, $idexterno, '$identificador', $estatus)";
			$ejecutar = mysql_query($queryFecha,$dspp) or die(mysql_error());
			*/

			// IDEXTERNO = ID DE LA SOLICITUD DE CERTIFICACION(OPP) o SOLICITUD REGISTRO(com)
			$idexterno = $idsolicitud_certificacion;
			$identificador = "OPP";
			$estatus = $_POST['procedimiento'];
			$queryFecha = "INSERT INTO fecha(fecha,idexterno,idopp,identificador,status) VALUES($fecha_actual,$idexterno,$idopp,'$identificador',$estatus)";
			$ejecutar = mysql_query($queryFecha,$dspp) or die(mysql_error());


		    $mensaje = "It sent the Request for Certification for Small Producers' Organizations by <b>$_SESSION[nombreOPP]</b>";




 /* $insertSQL = sprintf("INSERT INTO certificaciones (idsolicitud_certificacion, certificacion, certificadora, ano_inicial, interrumpida) VALUES (%s, %s, %s, %s, %s)",
        GetSQLValueString($_POST['idsolicitud_certificacion'], "int"),
        GetSQLValueString($_POST['ciudad'], "text"),
        GetSQLValueString($_POST['ruc'], "text"),
        GetSQLValueString($_POST['op_resp15'], "text"),
        GetSQLValueString($_POST['responsable'], "text"));

  		$Result1 = mysql_query($insertSQL, $dspp) or die(mysql_error());*/


  $insertGoTo = "main_menu.php?SOLICITUD&add&mensaje=Request added correctly, It has notified the OC by email.";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}


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


$query_opp = "SELECT * FROM opp WHERE idopp='$_SESSION[idopp]'";
$opp = mysql_query($query_opp,$dspp) or die(mysql_error());
$row_opp = mysql_fetch_assoc($opp);

$query_contacto = "SELECT * FROM contacto WHERE idopp='$_SESSION[idopp]'";
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
//20) Renovación del certificado

$estadoPublico = "2";
$estadoInterno = "1";

/************ VARIABLES DE CONTROL ******************/


?>
<script>
	
  function validar(){
    /*valor = document.getElementById("cotizacion_opp").value;
    if( valor == null || valor.length == 0 ) {
      alert("No se ha cargado la cotización de OPP");
      return false;
    }*/
    
    Procedimiento = document.getElementsByName("procedimiento");
     
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

<br>

<? if(isset($_POST['update'])){?>
  
	<div class="alert alert-success alert-dismissible" role="alert">
	  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	  <strong><? echo $_POST['update'];?></strong>
	</div>
<? }?>

<form class="" method="post" name="form1" action="<?php echo $editFormAction; ?>" enctype="multipart/form-data">

	<table class="table table-bordered table-striped col-xs-8">
	    <tr valign="baseline">
	      	<td>
	      		<div class="col-xs-12">
	      			<h5>Send to</h5>
	      		</div>
	      		<div class="col-xs-12">
			      	<select class="form-control" name="idoc" required>
			      		<!--<option class="form-control" value="9999">FUNDEPPO</option>-->
			      		<option class="form-control" value="">SELECT OC</option>
						
			        <?php 

						do {  
						?>
						        <option class="form-control" value="<?php echo $row_oc['idoc']?>"><?php echo $row_oc['abreviacion'];if($row_oc['idoc'] == 99){echo " (TODOS) ";}?></option>
						        <?php
						} while ($row_oc = mysql_fetch_assoc($oc));
					?>
			      	</select>		
	      		</div>
	  	    </td>
	  	    <td class="text-center alert alert-danger" colspan="3">
	  	    	<div class="col-xs-3">
	  	    		<div class="row">
						<h4>Type of Application <small>(Select the type of procedure you want to perform)</small></h4>
	  	    		</div>
	  	    	</div>
	  	    	<div class="col-xs-2">
	  	    		<div class="row">
		  	    		<div class="col-xs-12">
		  	    			<p style="font-size:12px;"><b>New Application</b></p>	
		  	    		</div>	  	 
		  	    		<div class="col-xs-12">
		  	    			<input type="radio" data-on-color="success" data-off-color="danger" data-size="small" name="procedimiento" value='1'>
	
		  	    		</div>		  	    		   			
	  	    		</div>
	  	    	</div>
	  	    	<div class="col-xs-4">
	  	    		<div class="row">
		  	    		<div class="col-xs-12">
		  	    			<p style="font-size:12px;"><b>Certification Renewal</b></p>	
		  	    		</div>
		  	    		<div class="col-xs-12">
		  	    			<input type="radio" data-on-color="success" data-off-color="danger" data-size="small" name="procedimiento" value='20'>
	
		  	    		</div>		  	    		
	  	    		</div>
	  	    	</div>

	  	   
	  	    	
	  	    	<!--<div class="col-xs-2"><button class="btn btn-danger">boton</button>DOCUMENTAL "ACORTADO"</div>
	  	    	<div class="col-xs-2"><button class="btn btn-danger">boton</button>DOCUMENTAL "NORMAL"</div>
	  	    	<div class="col-xs-2"><button class="btn btn-danger">boton</button>COMPLETO "IN SITU"</div>
	  	    	<div class="col-xs-2"><button class="btn btn-danger">boton</button>COMPLETO "A DISTANCIA"</div>-->
	  	    </td>
	    <tr>


		<tr>
			<th colspan="4" class="text-center"><h3>Application for Small Producers´Organization Certification</h3></th>
		</tr>

		<tr class="success">
			<th colspan="4" class="text-center">GENERAL INFORMATION</th>
		</tr>
		<tr>
			<td colspan="2">
				NAME OF SMALL PRODUCER ORGANIZATION
			</td>
			<td colspan="2">
				<input type="text" autofocus="autofocus" class="form-control" id="exampleInputEmail1" size="70" placeholder="Name of Organization" name="nombreOPP" value="<?php echo $row_opp['nombre']?>" >
			</td>
		</tr>

		<tr>
			<td colspan="3">
				COMPLETE ADDRESS FOR ORGANIZATION’S LOCATION (STREET, DISTRICT, TOWN/CITY, REGION)<br>
				<input type='text' class='form-control' name='direccionOPP' id='exampleInputEmail1' placeholder='Address' value="<?php echo $row_opp['direccion_fiscal']; ?>">
			</td>
			<td colspan="1">
				COUNTRY
				<?php if(isset($row_opp['pais'])){
						echo "<input type='text' class='form-control' name='paisOPP' id='exampleInputEmail1' placeholder='Dirección de Oficinas' value=$row_opp[pais] >";}
					else{ ?>
				PAÍS<br>
		      <select required class="form-control" name="paisOPP">
			      <option value="">Select</option>
					<?php 
					do {  
					?>
					<option class="form-control" name="paisOPP" value="<?php echo utf8_encode($row_pais['nombre']);?>" ><?php echo utf8_encode($row_pais['nombre']);?></option>
					<?php
					} while ($row_pais = mysql_fetch_assoc($pais));
					?>
		      </select>
		      <?php } ?>
			</td>
		</tr>	
		<tr>
			<td colspan="2">ORGANIZATION’S EMAIL ADDRESS</td>
			<td colspan="2">
				<input type='email' class='form-control' name='emailOPP' id='exampleInputEmail1' placeholder='Email Address' value="<?php echo $row_opp['email'] ?>">
			</td>
		</tr>
		<tr>
			<td colspan="3">
				WEB SITE<br>
				<input type='text' class='form-control' name='sitio_web' id='exampleInputEmail1' placeholder='Web Site' value="<?php echo $row_opp['sitio_web']; ?>">				
			</td>
			<td colspan="1">
				ORGANIZATION’S TELEPHONES(COUNTRY CODE+AREA CODE+NUMBER)<br>
				<input type='text' class='form-control' name='telefonoOPP' id='exampleInputEmail1' placeholder='Telephone' value="<?php echo $row_opp['telefono'] ?>">
			</td>
		</tr>		
		<tr>
			<td class="text-center" colspan="4">
				DATA FOR INVOICING (ADDRES, COUNTRY, ETC.)<br>
			</td>
		</tr>
		<tr>
			<td class='col-xs-3'>ADDRES: <input type='text' class='form-control' name='direccion_fiscal' id='exampleInputEmail1' placeholder='Address' value="<?php echo $row_opp['direccion_fiscal']; ?>"></td>
			<td class='col-xs-3'>RFC: <input type='text' class='form-control' name='rfc' id='exampleInputEmail1' placeholder='RFC' value="<?php echo $row_opp['rfc']; ?>"></td>
	
			<td class="col-xs-3">RUC: <input type="text" class="form-control" name="ruc" id="exampleInputEmail1" placeholder="RUC" value="<?php echo $row_opp['ruc']; ?>"></td>
			
			<td class="col-xs-3">CITY: <input type="text" class="form-control" name="ciudadOPP" id="exampleInputEmail1" placeholder="City" value="<?php echo $row_opp['ciudad']; ?>"></td>
		</tr>
		<tr class="text-center warning">
			<td colspan="4">CONTACT PERSON(S) OF APPLICATION</td>
		</tr>
		<tr>
			<td colspan="2">
				CONTACT NAME APPLICATION<br>
				<input type="text" class="form-control" name="p1_nombre" id="exampleInputEmail1" placeholder="Contact Name 1" required><br>
				<input type="text" class="form-control" name="p2_nombre" id="exampleInputEmail1" placeholder="Contact Name 2"><br>
				EMAIL  ADDRESS FROM THE CONTACT PERSON(S)
				<input type="email" class="form-control" name="p1_email" id="exampleInputEmail1" placeholder="Email 1" required><br>
				<input type="email" class="form-control" name="p2_email" id="exampleInputEmail1" placeholder="Email 2"><br>
			</td>
			<td colspan="2">
				POSITION(S)<br>
				<input type="text" class="form-control" name="p1_cargo" id="exampleInputEmail1" placeholder="Position 1" required><br>
				<input type="text" class="form-control" name="p2_cargo" id="exampleInputEmail1" placeholder="Position 2"><br>
				TELEPHONE(S)<br>
				<input type="text" class="form-control" name="p1_telefono" id="exampleInputtext1" placeholder="Telephone 1"><br>
				<input type="text" class="form-control" name="p2_telefono" id="exampleInputEmail1" placeholder="Telephone 2"><br>
			</td>
		</tr>
		<tr class="text-center warning">
			<td colspan="4">PERSON(S) OF THE ADMINISTRATIVE AREA</td>
		</tr>

		<tr>
			<td colspan="2">
				PERSON OF THE ADMINISTRATIVE AREA<br>
				<input type="text" class="form-control" name="adm_nom1" id="exampleInputEmail1" placeholder="Name 1" ><br>
				<input type="text" class="form-control" name="adm_nom2" id="exampleInputEmail1" placeholder="Name 2"><br>
				EMAIL ADDRESS(ES) FOR CONTACT ADMINISTRATIVE AREA:
				<input type="email" class="form-control" name="adm_email1" id="exampleInputEmail1" placeholder="Email 1" ><br>
				<input type="email" class="form-control" name="adm_email2" id="exampleInputEmail1" placeholder="Email 2">
			</td>
			<td colspan="2">
				TELEPHONE(S)  PERSON(S) ADMINISTRATIVE AREA:<br>
				<input type="text" class="form-control" name="adm_tel1" id="exampleInputEmail1" placeholder="Telephone 1" ><br>
				<input type="text" class="form-control" name="adm_tel2" id="exampleInputEmail1" placeholder="Telephone 2">
			</td>
		</tr>	

		<tr class="success">
			<th colspan="4" class="text-center">INFORMATION ON OPERATIONS</th>
		</tr>
		<tr >
			<td>NUMBER OF PRODUCERS MEMBERS</td>
			<td><input type="text" class="form-control" name="resp1" id="exampleInputEmail1" placeholder="Number of Producers"></td>
			<td>NUMBER OF PRODUCERS MEMBERS OF THE  PRODUCT(S) TO BE INCLUDED IN THE CERTIFICATION</td>
			<td><input type="text" class="form-control" name="resp2" id="exampleInputEmail1" placeholder="Number of Producers"></td>
		</tr>

		<tr >
			<td>TOTAL PRODUCTION VOLUME(S) BY PRODUCT (UNITE OF MEASURE)</td>
			<td><input type="text" class="form-control" name="resp3" id="exampleInputEmail1" placeholder="Total Production"></td>
			<td>MAXIMUM SIZE OF THE UNIT OF PRODUCTION BY THE PRODUCER OF THE PRODUCT(S) TO INCLUDE IN THE CERTIFICATION</td>
			<td><input type="text" class="form-control" name="resp4" id="exampleInputEmail1" placeholder="Maximum Size"></td>
		</tr>

		<tr>
			<td colspan="4">
				1. EXPLAIN IF THE SMALL PRODUCERS’ ORGANIZATION (SPO) IS AT THE 1st, 2nd, 3rd or 4th LEVEL, AS WELL AS EXPLAIN THE NUMBER OF ORGANIZATIONS OF THE 3rd,2nd or 1st LEVEL, AND THE NUMBER OF COMMUNITIES, AREAS OR GROUPS OF WORK, IN HIS OR HER CASE, THAT ACCOUNT
				<br>
				<textarea class="form-control" name="op_resp1" id="" rows="3"></textarea>
				
			</td>
		</tr>
		<tr>
			<td>
				<h5 class="col-xs-12">NUMBER OF SPO 3rd  LEVEL:</h5>
				<input type="text" class="col-xs-12 form-control" name="op_area1" id="">
				<!--<textarea class="col-xs-12 form-control" name="op_area1" id="" cols="10" rows="2"></textarea>-->
				
			</td>
			<td>
				<h5 class="col-xs-12">NUMBER OF SPO 2nd  LEVEL:</h5>	
				<input type="text" class="col-xs-12 form-control" name="op_area2" id="">
				<!--<textarea class="col-xs-12 form-control" name="op_area2" id="" cols="10" rows="2"></textarea>-->
			</td>
			<td>
				<h5 class="col-xs-12">NUMBER OF SPO 1st  LEVEL:</h5>
				<input type="text" class="col-xs-12 form-control" name="op_area3" id="">
				<!--<textarea class="col-xs-12 form-control" name="op_area3" id="" cols="10" rows="2"></textarea>-->
			</td>
			<td>
				<h5 class="col-xs-12">NUMBER OF COMMUNITIES, AREAS OR GROUPS OF WORK:</h5>
				<input type="text" class="col-xs-12 form-control" name="op_area4" id="">
				<!--<textarea class="col-xs-12 form-control" name="op_area4" id="" cols="10" rows="2"></textarea>-->
				
			</td>
		</tr>
		<tr>
			<td colspan="4">
				2.	SPECIFY WHICH PRODUCT (S) YOU WANT TO INCLUDE IN THE CERTIFICATE OF THE SYMBOL OF SMALL PRODUCERS FOR WHICH THE CERTIFICATION ENTITY WILL CONDUCT THE ASSESSMENT.
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<textarea name="op_resp2" id="" class="form-control" rows="3"></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				3.	MENTION IF YOUR ORGANIZATION WOULD LIKE TO INCLUDE SOME ADDITIONAL DESCRIPTOR FOR COMPLEMENTARY USE WITH THE GRAPHIC DESIGN OF THE SMALL PRODUCERS’ SYMBOL<sup>4</sup>
				<br>
				<h6><sup>4</sup> Review the Regulations on Graphics and the list of Optional Complementary Descriptors.</h6>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<textarea name="op_resp3" id="" class="form-control" rows="3"></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				4. SELECT THE SCOPE OF THE SMALL PRODUCERS’ ORGANIZATION:
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<div class="col-xs-4">
					PRODUCTION <input name="op_resp4[]" type="checkbox" class="form-control" value="PRODUCCION">
				</div>
				<div class="col-xs-4">
					PROCESSING <input name="op_resp4[]" type="checkbox" class="form-control" value="PROCESAMIENTO">
				</div>
				<div class="col-xs-4">
					TRADING <input name="op_resp4[]" type="checkbox" class="form-control" value="EXPORTACION">
				</div>

			</td>
		</tr>
		<tr>
			<td colspan="4">
				5.	SPECIFY IF YOU SUBCONTRACT THE SERVICES OF PROCESSING PLANTS, TRADING COMPANIES OR COMPANIES THAT CARRY OUT THE IMPORT OR EXPORT, IF THE ANSWER IS AFFIRMATIVE, MENTION THE NAME AND THE SERVICE THAT PERFORMS.
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<textarea class="form-control" name="op_resp5" id="" rows="3"></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				6.	IF YOU SUBCONTRACT THE SERVICES OF PROCESSING PLANTS, TRADING COMPANIES OR COMPANIES THAT CARRY OUT THE IMPORT OR EXPORT, INDICATE WHETHER THESE COMPANIES ARE GOING TO APPLY FOR THE REGISTRATION UNDER SPP CERTIFICATION PROGRAM.<sup>5</sup>
				<br>
				<h6><sup>5</sup> Review the General Application Guidelines to the SPP System.</h6>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<textarea class="form-control" name="op_resp6" id="" rows="3"></textarea>
			</td>
		</tr>		
		<tr>
			<td colspan="4">
				7.	IN ADDITION TO YOUR MAIN OFFICES, PLEASE SPECIFY HOW MANY COLLECTION CENTERS, PROCESSING AREAS AND ADDITIONAL OFFICES YOU HAVE.
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<textarea class="form-control" name="op_resp7" id="" rows="3"></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				8.	IF THE ORGANIZATION HAS AN INTERNAL CONTROL SYSTEM FOR COMPLYING WITH THE CRITERIA IN THE GENERAL STANDARD OF THE SMALL PRODUCERS’ SYMBOL, PLEASE EXPLAIN HOW IT WORKS.
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<textarea class="form-control" name="op_resp8" id="" rows="3"></textarea>
			</td>
		</tr>	
		<tr>
			<td colspan="4">
				9.	FILL OUT THE TABLE ACCORDING YOUR CERTIFICATIONS, (example: EU, NOP, JASS, FLO, etc).
			</td>
		</tr>
		<tr>


			<td colspan="4">
				<table class="table table-bordered" id="tablaCertificaciones">
					<tr>
						<td>CERTIFICATION</td>
						<td>CERTIFICATION ENTITY</td>
						<td>INITIAL YEAR OF CERTIFICATION.</td>
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
						<td><input type="text" class="form-control" name="ano_inicial[0]" id="exampleInputEmail1" placeholder="YEAR OF CERTIFICATION"></td>
						<!--<td><input type="text" class="form-control" name="interrumpida[0]" id="exampleInputEmail1" placeholder="¿HA SIDO INTERRUMPIDA?"></td>-->
						<td>
							<div class="col-xs-6">YES<input type="radio" class="form-control" name="interrumpida[0]" value="SI"></div>
							<div class="col-xs-6">NO<input type="radio" class="form-control" name="interrumpida[0]" value="NO"></div>
						</td>
					</tr>
				</table>			
			</td>
		</tr>
		<tr>
			<td colspan="4">
				10.	ACCORDING THE CERTIFICATIONS, IN ITS MOST RECENT INTERNAL AND EXTERNAL EVALUATIONS, HOW MANY CASES OF NON COMPLIANCE WERE IDENTIFIED? PLEASE EXPLAIN IF THEY HAVE BEEN RESOLVED OR WHAT THEIR STATUS IS?
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<textarea class="form-control" name="op_resp10" id="" rows="3"></textarea>
			</td>
		</tr>	
		<tr>
			<td colspan="4">
				11.	OF THE APPLICANT’S TOTAL TRADING DURING THE PREVIOUS CYCLE, WHAT PERCENTAGE WAS CONDUCTED UNDER THE SCHEMES OF CERTIFICATION FOR ORGANIC, FAIR TRADE AND/OR THE SMALL PRODUCERS’ SYMBOL?
			</td>
		</tr>	
		<tr>
			<td colspan="4">
				<textarea class="form-control" name="op_resp11" id="" rows="3"></textarea>
			</td>
		</tr>	
		<tr>
			<td colspan="4">
				12.	DID YOU HAVE SPP PURCHASES DURING THE PREVIOUS CERTIFICATION CYCLE?
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<div class="col-xs-6">
					YES <input type="radio" class="form-control" name="op_resp12" onclick="mostrar_ventas()" id="op_resp12" value="SI">
				</div>
				<div class="col-xs-6">
					NO <input type="radio" class="form-control" name="op_resp12" onclick="ocultar_ventas()" id="op_resp12" value="NO">
				</div>
			</td>
		</tr>
		
		
		<tr >
			<td colspan="4">
				13.	IF YOUR RESPONSE WAS POSITIVE, PLEASE SELECT THE RANGE OF THE TOTAL VALUE SPP FROM THE PREVIOUS CYCLE ACCORDING TO THE FOLLOWING TABLE:

				<div class="well col-xs-12 " id="tablaVentas" style="display:none;">
					
						<div class="col-xs-6"><p>LESS THAN $3,000 USD</p></div>
						<div class="col-xs-6 "><input type="radio" name="op_resp13" class="form-control" id="ver" onclick="ocultar()" value="HASTA $3,000 USD"></div>
					
					
						<div class="col-xs-6"><p>BETWEENN $3,000 AND $10,000 USD</p></div>
						<div class="col-xs-6"><input type="radio" name="op_resp13" class="form-control" id="ver" onclick="ocultar()" value="ENTRE $3,000 Y $10,000 USD"></div>
					
					
						<div class="col-xs-6"><p>BEETWENN $10,000 AND $25,000 USD</p></div>
						<div class="col-xs-6"><input type="radio" name="op_resp13" class="form-control"  id="ver" onclick="ocultar()" value="ENTRE $10,000 A $25,000 USD"></div>
					
						<div class="col-xs-6"><p>MORE THAN $25,000 USD <sup>*</sup><br><h6><sup>*</sup>SPECIFY THE QUANTITY.</h6></p></div>
						<div class="col-xs-6"><input type="radio" name="op_resp13" class="form-control" id="exampleInputEmail1" onclick="mostrar()" value="mayor">
							<input type="text" name="op_resp13_1" class="form-control" id="oculto" style='display:none;' placeholder="Specify the Quantity">
						</div>
					
				</div>
							
			</td>
		</tr>





		<tr>
			<td colspan="4">
				14.	ESTIMATED DATE FOR BEGINNING TO USE THE SMALL PRODUCERS’ SYMBOL:
			</td>
		</tr>	
		<tr>
			<td colspan="4">
				<textarea class="form-control" name="op_resp14" id="" rows="3"></textarea>
			</td>
		</tr>	
		<tr>
			<td colspan="4">
				15.	PLEASE ATTACH A GENERAL MAP OF THE AREA WHERE YOUR SPO OPERATES, INDICATING THE ZONES WHERE MEMBERS ARE LOCATED.
			</td>
		</tr>	
		<tr>
			<br><br>
			<td colspan="4">
	            <input name="op_resp15" id="op_resp15" type="file" class="filestyle" data-buttonName="btn-info" data-buttonBefore="true" data-buttonText="Select General Map"> 
			</td>
		</tr>	
		<tr class="success">
			<th colspan="4" class="text-center">INFORMATION ON PRODUCTS FOR WHICH APPLICANT WISHES TO USE SYMBOL<sup>6</sup></th>
		</tr>



		<tr>
			<td colspan="4">
				<table class="table table-bordered" id="tablaProductos">
					<tr>
						<td>Product</td>
						<td>Total Estimated Volume to be Traded</td>
						<td>Finished Product?</td>
						<td>Raw material</td>
						<td>Destination Countries</td>
						<td>Own brand?</td>
						<td>Client’s brand?</td>
						<td>Still without client?</td>
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
							<input type="text" class="form-control" name="volumen[0]" id="exampleInputEmail1" placeholder="Volume">
						</td>
						<td>
							SI <input type="radio"  name="terminado0[0]" id="" value="SI"><br>
							NO <input type="radio"  name="terminado0[0]" id="" value="NO" >
						</td>
						<td>
							<input type="text" class="form-control" name="materia[0]" id="exampleInputEmail1" placeholder="Material">
						</td>
						<td>
							<input type="text" class="form-control" name="destino[0]" id="exampleInputEmail1" placeholder="Destination">
						</td>
						<td>
							YES <input type="radio"  name="marca_propia0[0]" id="" value="SI"><br>
							NO <input type="radio"  name="marca_propia0[0]" id="" value="NO" >
						</td>
						<td>
							YES <input type="radio"  name="marca_cliente0[0]" id="" value="SI"><br>
							NO <input type="radio"  name="marca_cliente0[0]" id="" value="NO">
						</td>
						<td>
							YES <input type="radio"  name="sin_cliente0[0]" id="" value="SI"><br>
							NO <input type="radio"  name="sin_cliente0[0]" id="" value="NO">
						</td>
					</tr>				
					<tr>
						<td colspan="8">
							<h6><sup>6</sup> Information provided in this section will be handled with complete confidentiality. Please insert additional lines if necessary.</h6>
						</td>
					</tr>
				</table>
			</td>

		</tr>
		<tr>
			<th class="success" colspan="4">
				COMMITMENTS
			</th>
		</tr>
		<tr class="text-justify">
			<td colspan="4">
				1.	By sending in this document, the applicant expresses its interest in receiving a proposal for certification with the Small Producers’ Symbol.<br>
				2.	The certification process will begin when it is confirmed that the payment corresponding to the proposal has been received.<br>
				3.	The fact that this application is delivered and received does not guarantee that the results of the certification process will be positive.<br>
				4.	The applicant will become familiar with and comply with all the applicable requirements in the General Standard of the Small Producers’ Symbol for a Small Producers’ Organization, including both Critical and Minimum Criteria, and independently of the type of evaluation conducted.
			</td>
		</tr>
		<tr>
			<td colspan="2">
				Name of the Certification Entity  personnel who receives the application:
			</td>
			<td colspan="2">
				<input type="text" class="form-control" name="responsable" required>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				OC who receives the application:
			</td>
			<td colspan="2">
				<input type="text" class="form-control" name="personal_oc" readonly>
			</td>
		</tr>		
	</table>
	<input type="hidden" name="MM_insert" value="form1">
	<input type="hidden" name="fecha_elaboracion" value="<?php echo time()?>">
	<input type="hidden" name="status_publico" value="<?php echo $estadoPublico;?>">
	<input type="hidden" name="status_interno" value="<?php echo $estadoInterno;?>">
	<input type="hidden" name="mensaje" value="Action added correctly" />
	<input type="hidden" name="idopp" value="<?php echo $_SESSION['idopp']?>">
	<input type="hidden" name="abreviacion" value="<?php echo $row_opp['abreviacion'];?>">
	<input type="hidden" name="nombreOPP" value="<?php echo $row_opp['nombre']; ?>">
	<input type="hidden" name="paisOPP" value="<?php echo $row_opp['pais']; ?>">
	

    <button style="width:200px;" class="btn btn-primary col-xs-2 col-xs-offset-5" type="submit" value="Enviar Solicitud" aria-label="Left Align" onclick="return validar()">
      <span class="glyphicon glyphicon-open-file" aria-hidden="true"></span> Send
    </button>
  

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
	  cell3.innerHTML = '<input type="text" class="form-control" name="ano_inicial['+contador+']" id="exampleInputEmail1" placeholder="YEAR OF CERTIFICATION">';
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

	  

	  cell1.innerHTML = '<input type="text" class="form-control" name="producto['+cont+']" id="exampleInputEmail1" placeholder="Product">';
	  
	  cell2.innerHTML = '<input type="text" class="form-control" name="volumen['+cont+']" id="exampleInputEmail1" placeholder="Volume">';
	  
	  cell3.innerHTML = 'YES <input type="radio" name="terminado'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="terminado'+cont+'['+cont+']" id="" value="NO">';
	  
	  cell4.innerHTML = '<input type="text" class="form-control" name="materia['+cont+']" id="exampleInputEmail1" placeholder="Material">';
	  
	  cell5.innerHTML = '<input type="text" class="form-control" name="destino['+cont+']" id="exampleInputEmail1" placeholder="Destination">';
	  
	  cell6.innerHTML = 'YES <input type="radio" name="marca_propia'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="marca_propia'+cont+'['+cont+']" id="" value="NO">';
	  
	  cell7.innerHTML = 'YES <input type="radio" name="marca_cliente'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="marca_cliente'+cont+'['+cont+']" id="" value="NO">';
	  
	  cell8.innerHTML = 'YES <input type="radio" name="sin_cliente'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="sin_cliente'+cont+'['+cont+']" id="" value="NO">';	  

	  }

	}	

</script>


<?
mysql_free_result($pais);

mysql_free_result($oc);
?>