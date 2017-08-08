<?php require_once('../Connections/dspp.php'); ?>
<?php
  error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

if (!isset($_SESSION)) {
  session_start();
  
  $redireccion = "../index.php?OC";

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

  $theValue = function_exissts("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

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

if(isset($_POST['opp_delete'])){
  $query=sprintf("delete from opp where idopp = %s",GetSQLValueString($_POST['idopp'], "text"));
  $ejecutar=mysql_query($query,$dspp) or die(mysql_error());
}

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_opp = 20;
$pageNum_opp = 0;
if (isset($_GET['pageNum_opp'])) {
  $pageNum_opp = $_GET['pageNum_opp'];
}
$startRow_opp = $pageNum_opp * $maxRows_opp;

mysql_select_db($database_dspp, $dspp);

$query_limit_opp = sprintf("%s LIMIT %d, %d", $query_opp, $startRow_opp, $maxRows_opp);
$opp = mysql_query($query_limit_opp,$dspp) or die(mysql_error());
//$row_opp = mysql_fetch_assoc($opp);


if(isset($_POST['comprobanteMembresia']) && $_POST['comprobanteMembresia'] == "2"){
    $idsolicitud_certificacion = $_POST['idsolicitud'];
    $statusInterno = 10;
    $idexterno = $_POST['idmembresia'];
    $fecha = $_POST['fecha'];
    $identificador = "membresia";
    $idcertificado = $_POST['idcertificado'];
    $emailOPP1 = $_POST['emailOPP1'];
    $emailOPP2 = $_POST['emailOPP2'];

  if(isset($_POST['aprobar'])){
    $status = "APROBADO";
    $insertar = "INSERT INTO fecha (fecha,idexterno,identificador,status) VALUES ($fecha,$idexterno,'$identificador','$status')";
    $ejecutar = mysql_query($insertar,$dspp) or die(mysql_error());
    $actualizar = "UPDATE certificado SET statuspago = '$status' WHERE idcertificado = $idcertificado";
    $ejecutar = mysql_query($actualizar,$dspp) or die(mysql_error());
    //echo $insertar;
    //echo "<br>".$actualizar; 
    $update = "UPDATE solicitud_certificacion SET status = '$statusInterno' WHERE idsolicitud_certificacion = $idsolicitud_certificacion";
    $actualizar = mysql_query($update,$dspp);

    $idopp = $_POST['idopp'];
    $estadoOPP = "NUEVO";

    $update = "UPDATE opp SET estado = '$estadoOPP' WHERE idopp = $idopp";
    $actualizar = mysql_query($update,$dspp);
    
   
    $destinatario = $emailOPP1.",";
    $destinatario .= $emailOPP2;

        $asunto = "D-SPP - Certificación para Organizaciones de Pequeños Productores"; 

    $cuerpo = '
      <html>
      <head>
        <meta charset="utf-8">
      </head>
      <body>
      
        <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
          <tbody>
            <tr>
              <th rowspan="3" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
              <th scope="col" align="left" width="280"><strong>Notificación de Estado / Status Notification ('.$fecha.')</strong></th>
            </tr>

            <tr>
              <td><b>Felicidades!!!!, su membresia ha sido aprobada, ahora puede disponer de su certificado, para descargar su certificado por favor inicie sesión en su cuenta de OPP(<a href="http://www.d-spp.org/?OPP">www.d-spp.org/?OPP</a>) en la opción de "SOLICITUDES" dentro de la sección certificación.</b></td>
            </tr>

            <tr>
              <td align="left" style="color:#ff738a;">Para cualquier duda o aclaración enviar un e-mail a: cert@spp.coop</td>
            </tr>


          </tbody>
        </table>

      </body>
      </html>
    ';


        //para el envío en formato HTML 
        $headers = "MIME-Version: 1.0\r\n"; 
        $headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 

        //dirección del remitente 
        $headers .= "From: cert@spp.coop\r\n"; 

        //dirección de respuesta, si queremos que sea distinta que la del remitente 
        

        //ruta del mensaje desde origen a destino 
        //$headers .= "Return-path: holahola@desarrolloweb.com\r\n"; 

        //direcciones que recibián copia 
        //$headers .= "Cc: maria@desarrolloweb.com\r\n"; 

        //direcciones que recibirán copia oculta 
        $headers .= "Bcc: yasser.midnight@gmail.com\r\n";
        //$headers .= "Bcc: isc.jesusmartinez@gmail.com  \r\n"; 

        mail($destinatario,$asunto,$cuerpo,$headers) ;

  }
  if(isset($_POST['denegar'])){
    $status = "DENEGAR";
    $insertar = "INSERT INTO fecha (fecha,idexterno,identificador,status) VALUES ($fecha,$idexterno,'$identificador','$status')";
    $ejecutar = mysql_query($insertar,$dspp) or die(mysql_error());
    $actualizar = "UPDATE certificado SET statuspago = '$status' WHERE idcertificado = $idcertificado";
    $ejecutar = mysql_query($actualizar,$dspp) or die(mysql_error());

    //echo $insertar; 
    //echo "<br>".$actualizar; 


    $destinatario = $emailOPP1.",";
    $destinatario .= $emailOPP2;

        $asunto = "D-SPP - Certificación para Organizaciones de Pequeños Productores"; 

    $cuerpo = '
      <html>
      <head>
        <meta charset="utf-8">
      </head>
      <body>
      
        <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
          <tbody>
            <tr>
              <th rowspan="3" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
              <th scope="col" align="left" width="280"><strong>Notificación de Estado / Status Notification ('.$fecha.')</strong></th>
            </tr>

            <tr>
              <td><b>Lo sentimos su comprobante no es el correcto, por favor asegúrese de que el comprobante seá el correcto. Para reenviar el comprobante siga los pasos anteriormente realizados (<a href="http://www.d-spp.org/?OPP">www.d-spp.org/?OPP</a>).</b></td>
            </tr>

            <tr>
              <td align="left" style="color:#ff738a;">Para cualquier duda o aclaración enviar un e-mail a: cert@spp.coop</td>
            </tr>


          </tbody>
        </table>

      </body>
      </html>
    ';


        //para el envío en formato HTML 
        $headers = "MIME-Version: 1.0\r\n"; 
        $headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 

        //dirección del remitente 
        $headers .= "From: cert@spp.coop\r\n"; 

        //dirección de respuesta, si queremos que sea distinta que la del remitente 
        

        //ruta del mensaje desde origen a destino 
        //$headers .= "Return-path: holahola@desarrolloweb.com\r\n"; 

        //direcciones que recibián copia 
        //$headers .= "Cc: maria@desarrolloweb.com\r\n"; 

        //direcciones que recibirán copia oculta 
        $headers .= "Bcc: yasser.midnight@gmail.com\r\n";
        //$headers .= "Bcc: isc.jesusmartinez@gmail.com  \r\n"; 

        mail($destinatario,$asunto,$cuerpo,$headers) ;



  }
}



/**********************************************************************************************/
/*****************************  INICIA INSERTAR OBJECIÓN  ********************************/
/**********************************************************************************************/

if (isset($_POST['insertarObjecion']) && $_POST['insertarObjecion'] == "periodoObjecion") {

  $fechaInicio = $_POST['fechaInicio'];
  $fechaFin = $_POST['fechaFin'];
  $status = $_POST['statusObjecion_hdn'];

  $observacion = $_POST['observacion_txt'];
  $idopp = $_POST['objecionIdOpp_hdn'];
  $idoc = $_POST['objecionIdOc_hdn'];
  $idsolicitud_certificacion = $_POST['idsolicitud'];

  $query = "INSERT INTO objecion (fechainicio, fechafin, status, adjunto, observacion, idsolicitud) VALUES ('$fechaInicio', '$fechaFin', '$status', '$adjunto', '$observacion', $idsolicitud_certificacion)";

  $insertarQuery = mysql_query($query, $dspp) or die(mysql_error());

  $query = "UPDATE solicitud_certificacion SET status_publico = '$status' WHERE idsolicitud_certificacion = $idsolicitud_certificacion";
  $insertar = mysql_query($query,$dspp) or die(mysql_error());



  $query = "SELECT * FROM oc WHERE idoc = $idoc";
  $ejecutar = mysql_query($query,$dspp);
  $row_oc = mysql_fetch_assoc($ejecutar);

  $fecha = date("d/m/Y", time());
  $emailOPP1 = $_POST['emailOPP1'];
  $emailOPP2 = $_POST['emailOPP2'];



      $queryCorreo = "SELECT solicitud_certificacion.*, opp.idopp,opp.nombre, opp.pais, opp.abreviacion AS 'abreviacionOPP', oc.abreviacion AS 'abreviacionOC' FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp INNER JOIN oc ON solicitud_certificacion.idoc = oc.idoc WHERE idsolicitud_certificacion = $idsolicitud_certificacion";
      $ejecutar = mysql_query($queryCorreo,$dspp) or die(msql_error());
      $datosCorreo = mysql_fetch_assoc($ejecutar);

      $queryProductos = "SELECT producto FROM productos WHERE idsolicitud_certificacion = $idsolicitud_certificacion";
      $ejecutar = mysql_query($queryProductos,$dspp) or die(mysql_error());
      $productos = "";
      while($datosProductos = mysql_fetch_assoc($ejecutar)){
        $productos .= $datosProductos['producto']." - ";
      }
      $fecha_elaboracion = date("d/m/Y",$datosCorreo['fecha_elaboracion']);
      $fecha = date("d/m/Y", time());


      $nombreOPP = $datosCorreo['nombre'];
      $abreviacionOPP = $datosCorreo['abreviacionOPP'];
      $paisOPP = $datosCorreo['pais'];
      $abreviacionOC = $datosCorreo['abreviacionOC'];
      $alcance = $datosCorreo['op_resp4'];
      /*****************************INICIO MAIL OC***************************************************/
      /********************************************************************************/


/////////*************** ENVIO EMAIL DE OC *******************//////////////////


      $queryOC = "SELECT email FROM oc WHERE email !=''";
      $ejecutar = mysql_query($queryOC,$dspp) or die(mysql_error());

      $destinatarioOC = "";
      while($emailOC = mysql_fetch_assoc($ejecutar)){
          $destinatarioOC .= $emailOC['email'].',';
      }


              //$destinatario = $emailOC['email'];
              //$headers .= "Bcc: yasser.midnight@gmail.com\r\n";
              //$headers .= "Bcc: isc.jesusmartinez@gmail.com  \r\n"; 

              $asunto = "D-SPP - Certificación para Organizaciones de Pequeños Productores"; 

          $cuerpo = '


            <html>
            <head>
              <meta charset="utf-8">
            </head>
            <body>
            
                <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
                <tbody>
                  <tr>
                    <th rowspan="2" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                    <th scope="col" align="left" width="280"><strong>Notificación de Intenciones / Notification of Intentions</strong></th>
                  </tr>
                  <tr>
                    <td align="left" style="color:#ff738a;">Fecha: '.$fecha.'</td>
                  </tr>

                  <tr>
                    <td colspan="2">
                      <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;" border="1" width="650px">
                        <tbody>
                          <tr style="font-size: 8px; text-align:center; background-color:#dff0d8; color:#3c763d;" height="50px;">
                            <td width="72px">Tipo / Type</td>
                            <td width="72px">Nombre de la organización/Organization name</td>
                            <td width="72px">Abreviación / Short name</td>
                            <td width="72px">País / Country</td>
                            <td width="72px">Organismo de Certificación / Certification Entity</td>
                            <td width="72px">Alcance / Scope</td>
                            <td width="72px">Productos / Products</td>
                            <td width="72px">Tipo de solicitud / Kind of application</td>
                            <td width="72px">Fecha de solicitud/Date of application</td>
                          </tr>
                          <tr style="font-size: 12px;">
                            <td style="padding:10px;">
                              OPP 
                            </td>
                            <td style="padding:10px;">
                              '.$nombreOPP.'
                            </td>
                            <td style="padding:10px;">
                              '.$abreviacionOPP.'
                            </td>
                            <td style="padding:10px;">
                              '.$paisOPP.'
                            </td>
                            <td style="padding:10px;">
                              '.$abreviacionOC.'
                            </td>
                            <td style="padding:10px;">
                              '.$alcance.'
                            </td>
                            <td style="padding:10px;">
                              '.$productos.'
                            </td>
                            <td style="padding:10px;">
                              Registro / Registration                   
                            </td>
                            <td style="padding:10px;">
                              '.$fecha_elaboracion.'
                            </td>
                          </tr>

                        </tbody>
                      </table>        
                    </td>
                  </tr>

                  <tr>
                    <td style="text-align:justify;" colspan="2">
                      FUNDEPPO publica y notifica las “Intenciones de Certificación, Registro o Autorización” basada en nuevas solicitudes de: 1) Certificación de Organizaciones de Pequeños Productores, 2) Registro de Compradores y otros actores y 3) Autorización de Organismos de Certificación, con el objetivo de informarles y recibir las eventuales objeciones contra la incorporación de los solicitantes.
                      Estas eventuales objeciones presentadas deben estar sustentadas con información concreta y verificable con respecto a incumplimientos de la Normatividad del SPP y/o nuestro Código de Conducta (disponibles en <a href="http://www.spp.coop/"><strong>www.spp.coop</strong></a>, en el área de Funcionamiento). Las objeciones presentadas y enviadas a <a href="cert@spp.coop"><strong>cert@spp.coop</strong></a> serán tomadas en cuenta en los procesos de certificación, registro o autorización.
                      Estas notificaciones son enviadas por FUNDEPPO en un lapso menor a 24 horas a partir del momento en que le llegue la solicitud correspondiente. Si se presentan objeciones antes de que el solicitante se Certifique, Registre o Autorice su tratamiento por parte del Organismo de Certificación debe ser parte de la misma evaluación documental. Si la objeción se presenta cuando el Solicitante ya esta Certificado se aplica el Procedimiento de Inconformidades del Símbolo de Pequeños Productores. Las nuevas intenciones de Certificación, Registro o Autorización, se detallan al inicio de este documento.  
                      <br><br>
                      FUNDEPPO publishes and notifies the “Certification, Registration and Authorization Intentions” based on new applications submitted for: 1) Certification of Small Producers’ Organizations, 2) Registration of Buyers and other stakeholders, and 3) Authorization of Certification Entities, with the objective of keeping you informed and receiving any objections to the incorporation of any new applicants into the system.
                      Any objections submitted must be supported with concrete, verifiable information regarding non-compliance with the Standards and/or Code of Conduct of the Small Producers’ Symbol (available at <a href="http://www.spp.coop/"><strong>www.spp.coop</strong></a> in the section on Operation). The objections submitted and sent to <a href="cert@spp.coop"><strong>cert@spp.coop</strong></a> will be taken into consideration during certification, registration and authorization processes.
                      These notifications are sent by FUNDEPPO in a period of less than 24 hours from the time a corresponding application is received. If objections are presented before getting the Certification, Registration or Authorization, the Certification Entity must incorporate them as part of the same evaluation-process. If the objection is presented when the applicant has already been certified, the SPP Dissents\' Procedure has to be applied. The new intentions for Certification, Registration and Authorization are detailed at the beginning (of this document.
                    </td>
                  </tr>
                </tbody>
              </table>
              </table>

            </body>
            </html>
          ';


              //para el envío en formato HTML 
              $headers = "MIME-Version: 1.0\r\n"; 
              $headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 

              //dirección del remitente 
              $headers .= "From: cert@spp.coop\r\n"; 

              //dirección de respuesta, si queremos que sea distinta que la del remitente 
              

              //ruta del mensaje desde origen a destino 
              //$headers .= "Return-path: holahola@desarrolloweb.com\r\n"; 

              //direcciones que recibián copia 
              //$headers .= "Cc: maria@desarrolloweb.com\r\n"; 

              //direcciones que recibirán copia oculta 
              $headers .= "Bcc: yasser.midnight@gmail.com\r\n";
              //$headers .= "Bcc: isc.jesusmartinez@gmail.com  \r\n"; 

              mail($destinatarioOC,$asunto,$cuerpo,$headers) ;


/////////*************** ENVIO EMAIL DE OPP *******************//////////////////

      $queryOPP = "SELECT email FROM opp WHERE email !=''";
      $ejecutar = mysql_query($queryOPP,$dspp) or die(mysql_error());

      $destinatarioOPP = "";
      while($emailOPP = mysql_fetch_assoc($ejecutar)){
          $destinatarioOPP .= $emailOPP['email'].',';
      }


              //$destinatario = $emailOC['email'];
              //$headers .= "Bcc: yasser.midnight@gmail.com\r\n";
              //$headers .= "Bcc: isc.jesusmartinez@gmail.com  \r\n"; 

              $asunto = "D-SPP - Certificación para Organizaciones de Pequeños Productores"; 

          $cuerpo = '


            <html>
            <head>
              <meta charset="utf-8">
            </head>
            <body>
            
                <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
                <tbody>
                  <tr>
                    <th rowspan="2" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                    <th scope="col" align="left" width="280"><strong>Notificación de Intenciones / Notification of Intentions</strong></th>
                  </tr>
                  <tr>
                    <td align="left" style="color:#ff738a;">Fecha: '.$fecha.'</td>
                  </tr>

                  <tr>
                    <td colspan="2">
                      <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;" border="1" width="650px">
                        <tbody>
                          <tr style="font-size: 8px; text-align:center; background-color:#dff0d8; color:#3c763d;" height="50px;">
                            <td width="72px">Tipo / Type</td>
                            <td width="72px">Nombre de la organización/Organization name</td>
                            <td width="72px">Abreviación / Short name</td>
                            <td width="72px">País / Country</td>
                            <td width="72px">Organismo de Certificación / Certification Entity</td>
                            <td width="72px">Alcance / Scope</td>
                            <td width="72px">Productos / Products</td>
                            <td width="72px">Tipo de solicitud / Kind of application</td>
                            <td width="72px">Fecha de solicitud/Date of application</td>
                          </tr>
                          <tr style="font-size: 12px;">
                            <td style="padding:10px;">
                              OPP 
                            </td>
                            <td style="padding:10px;">
                              '.$nombreOPP.'
                            </td>
                            <td style="padding:10px;">
                              '.$abreviacionOPP.'
                            </td>
                            <td style="padding:10px;">
                              '.$paisOPP.'
                            </td>
                            <td style="padding:10px;">
                              '.$abreviacionOC.'
                            </td>
                            <td style="padding:10px;">
                              '.$alcance.'
                            </td>
                            <td style="padding:10px;">
                              '.$productos.'
                            </td>
                            <td style="padding:10px;">
                              Registro / Registration                   
                            </td>
                            <td style="padding:10px;">
                              '.$fecha_elaboracion.'
                            </td>
                          </tr>

                        </tbody>
                      </table>        
                    </td>
                  </tr>

                  <tr>
                    <td style="text-align:justify;" colspan="2">
                      FUNDEPPO publica y notifica las “Intenciones de Certificación, Registro o Autorización” basada en nuevas solicitudes de: 1) Certificación de Organizaciones de Pequeños Productores, 2) Registro de Compradores y otros actores y 3) Autorización de Organismos de Certificación, con el objetivo de informarles y recibir las eventuales objeciones contra la incorporación de los solicitantes.
                      Estas eventuales objeciones presentadas deben estar sustentadas con información concreta y verificable con respecto a incumplimientos de la Normatividad del SPP y/o nuestro Código de Conducta (disponibles en <a href="http://www.spp.coop/"><strong>www.spp.coop</strong></a>, en el área de Funcionamiento). Las objeciones presentadas y enviadas a <a href="cert@spp.coop"><strong>cert@spp.coop</strong></a> serán tomadas en cuenta en los procesos de certificación, registro o autorización.
                      Estas notificaciones son enviadas por FUNDEPPO en un lapso menor a 24 horas a partir del momento en que le llegue la solicitud correspondiente. Si se presentan objeciones antes de que el solicitante se Certifique, Registre o Autorice su tratamiento por parte del Organismo de Certificación debe ser parte de la misma evaluación documental. Si la objeción se presenta cuando el Solicitante ya esta Certificado se aplica el Procedimiento de Inconformidades del Símbolo de Pequeños Productores. Las nuevas intenciones de Certificación, Registro o Autorización, se detallan al inicio de este documento.  
                      <br><br>
                      FUNDEPPO publishes and notifies the “Certification, Registration and Authorization Intentions” based on new applications submitted for: 1) Certification of Small Producers’ Organizations, 2) Registration of Buyers and other stakeholders, and 3) Authorization of Certification Entities, with the objective of keeping you informed and receiving any objections to the incorporation of any new applicants into the system.
                      Any objections submitted must be supported with concrete, verifiable information regarding non-compliance with the Standards and/or Code of Conduct of the Small Producers’ Symbol (available at <a href="http://www.spp.coop/"><strong>www.spp.coop</strong></a> in the section on Operation). The objections submitted and sent to <a href="cert@spp.coop"><strong>cert@spp.coop</strong></a> will be taken into consideration during certification, registration and authorization processes.
                      These notifications are sent by FUNDEPPO in a period of less than 24 hours from the time a corresponding application is received. If objections are presented before getting the Certification, Registration or Authorization, the Certification Entity must incorporate them as part of the same evaluation-process. If the objection is presented when the applicant has already been certified, the SPP Dissents\' Procedure has to be applied. The new intentions for Certification, Registration and Authorization are detailed at the beginning (of this document.
                    </td>
                  </tr>
                </tbody>
              </table>
              </table>

            </body>
            </html>
          ';


              //para el envío en formato HTML 
              $headers = "MIME-Version: 1.0\r\n"; 
              $headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 

              //dirección del remitente 
              $headers .= "From: cert@spp.coop\r\n"; 

              //dirección de respuesta, si queremos que sea distinta que la del remitente 
              

              //ruta del mensaje desde origen a destino 
              //$headers .= "Return-path: holahola@desarrolloweb.com\r\n"; 

              //direcciones que recibián copia 
              //$headers .= "Cc: maria@desarrolloweb.com\r\n"; 

              //direcciones que recibirán copia oculta 
              $headers .= "Bcc: yasser.midnight@gmail.com\r\n";
              //$headers .= "Bcc: isc.jesusmartinez@gmail.com  \r\n"; 

              mail($destinatarioOPP,$asunto,$cuerpo,$headers) ;


}
/**********************************************************************************************/
/*****************************  FIN INSERTAR OBJECIÓN  ********************************/
/**********************************************************************************************/



/**********************************************************************************************/
/*****************************  INICIA RESOLUCION DE OBJECIÓN  ********************************/
/**********************************************************************************************/

if(isset($_POST['resolucionObjecion']) && $_POST['resolucionObjecion'] == "resolucionObjecion"){

  $ruta = "archivos/";
  $idobjecion = $_POST['idobjecion'];
  $idopp = $_POST['objecionIdOpp_hdn'];
  $idoc = $_POST['objecionIdOc_hdn'];
  $idsolicitud = $_POST['idsolicitud'];

  if(!empty($_FILES['adjunto_fld']['name'])){
    $_FILES['adjunto_fld']['name'];
        move_uploaded_file($_FILES["adjunto_fld"]["tmp_name"], $ruta.time()."_".$_FILES["adjunto_fld"]["name"]);
        $objecionAdjunto = $ruta.basename(time()."_".$_FILES["adjunto_fld"]["name"]);
  }else{
    $objecionAdjunto = NULL;
  }

  $statusObjecion = $_POST['statusObjecion_hdn'];
  $adjunto = $objecionAdjunto;
  $statusInterno = $_POST['statusInterno'];

  $query = "UPDATE objecion SET status = '$statusObjecion', adjunto = '$adjunto' WHERE idobjecion = $idobjecion";
  $insertarResolucion = mysql_query($query, $dspp) or die(mysql_error());

  $query = "UPDATE solicitud_certificacion SET status = '$statusInterno', status_publico = '$statusObjecion' WHERE idsolicitud_certificacion = $idsolicitud";
  $insertar = mysql_query($query,$dspp) or die(mysql_error());


  $query = "SELECT * FROM oc WHERE idoc = $idoc";
  $ejecutar = mysql_query($query,$dspp);
  $row_oc = mysql_fetch_assoc($ejecutar);


        /************************  INICIAR MAIL OC  *******************************************/

        $destinatario = $row_oc['email'];
        $asunto = "D-SPP - Certificación para Organizaciones de Pequeños Productores"; 

    $cuerpo = '
      <html>
      <head>
        <meta charset="utf-8">
      </head>
      <body>
      
        <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
          <tbody>
            <tr>
              <th rowspan="3" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
              <th scope="col" align="left" width="280"><strong>Notificación de Estado / Status Notification ('.$fecha.')</strong></th>
            </tr>

            <tr>
              <td><b>Ha finalizado el período de objeción, ahora podra empezar el proceso de certificación. Por favor inicie sesión en su cuenta de OC en el siguiente enlace <a href="http://d-spp.org/?OC">www.d-spp.org/?OC</a> .</b></td>
            </tr>

            <tr>
              <td align="left" style="color:#ff738a;">Email: cert@spp.coop</td>
            </tr>


          </tbody>
        </table>

      </body>
      </html>
    ';


        //para el envío en formato HTML 
        $headers = "MIME-Version: 1.0\r\n"; 
        $headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 

        //dirección del remitente 
        $headers .= "From: cert@spp.coop\r\n"; 

        //dirección de respuesta, si queremos que sea distinta que la del remitente 
        

        //ruta del mensaje desde origen a destino 
        //$headers .= "Return-path: holahola@desarrolloweb.com\r\n"; 

        //direcciones que recibián copia 
        //$headers .= "Cc: maria@desarrolloweb.com\r\n"; 

        //direcciones que recibirán copia oculta 
        $headers .= "Bcc: yasser.midnight@gmail.com\r\n";
        //$headers .= "Bcc: isc.jesusmartinez@gmail.com  \r\n"; 

        mail($destinatario,$asunto,$cuerpo,$headers) ;
        /************************  FIN MAIL OC  *********************************************/


        /************************  INICIA MAIL OPP  *******************************************/
      $emailOPP1 = $_POST['emailOPP1'];
      $emailOPP2 = $_POST['emailOPP2'];

      $destinatario = $emailOPP1.",";
      $destinatario .= $emailOPP2;

        $asunto = "D-SPP - Certificación para Organizaciones de Pequeños Productores"; 

    $cuerpo = '
      <html>
      <head>
        <meta charset="utf-8">
      </head>
      <body>
      
        <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
          <tbody>
            <tr>
              <th rowspan="3" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
              <th scope="col" align="left" width="280"><strong>Notificación de Estado / Status Notification ('.$fecha.')</strong></th>
            </tr>

            <tr>
              <td><b>Ha finalizado el período de objeción, para descargar la resolución de objeción por favor entre en su cuenta de OPP en el siguiente enlace <a href="http://d-spp.org/?OPP">www.d-spp.org/?OPP</a> .</b></td>
            </tr>

            <tr>
              <td align="left" style="color:#ff738a;">Email: cert@spp.coop</td>
            </tr>


          </tbody>
        </table>

      </body>
      </html>
    ';


        //para el envío en formato HTML 
        $headers = "MIME-Version: 1.0\r\n"; 
        $headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 

        //dirección del remitente 
        $headers .= "From: cert@spp.coop\r\n"; 

        //dirección de respuesta, si queremos que sea distinta que la del remitente 
        

        //ruta del mensaje desde origen a destino 
        //$headers .= "Return-path: holahola@desarrolloweb.com\r\n"; 

        //direcciones que recibián copia 
        //$headers .= "Cc: maria@desarrolloweb.com\r\n"; 

        //direcciones que recibirán copia oculta 
        $headers .= "Bcc: yasser.midnight@gmail.com\r\n";
        //$headers .= "Bcc: isc.jesusmartinez@gmail.com  \r\n"; 

        mail($destinatario,$asunto,$cuerpo,$headers) ;
        /************************  FIN MAIL OPP  *********************************************/



}
/*******************************************************************************************/
/*****************************  FIN RESOLUCION DE OBJECIÓN  ********************************/
/*******************************************************************************************/

 if(isset($_POST['filtroPalabra']) && $_POST['filtroPalabra'] == "1" && $_POST['palabraClave']){
  $palabraClave = $_POST['palabraClave'];

        $query_buscar = "SELECT solicitud_certificacion.*, opp.idopp,opp.nombre, opp.pais FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE opp.nombre LIKE '%$palabraClave%' OR solicitud_certificacion.p1_email LIKE '%palabraClave%' OR opp.pais LIKE '%palabraClave%' ORDER BY solicitud_certificacion.fecha_elaboracion DESC";
  //$query_buscar = "SELECT solicitud_certificacion.*, opp.idopp,opp.nombre, opp.pais FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp ORDER BY solicitud_certificacion.fecha_elaboracion DESC";

  //$query_buscar = "SELECT opp.* ,solicitud_certificacion.* FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE solicitud_certificacion.idopp = LIKE '%$palabraClave%' OR solicitud_certificacion.idoc = LIKE '%$palabraClave%' OR FROM_UNIXTIME(solicitud_certificacion.fecha_elaboracion, '%d/%m/%Y' ) = LIKE '%$palabraClave%'  ORDER BY solicitud_certificacion.fecha_elaboracion DESC";

  //$query_buscar = "SELECT * FROM opp WHERE idf LIKE '%$palabraClave%' OR nombre LIKE '%$palabraClave%' OR abreviacion LIKE '%$palabraClave%' OR sitio_web LIKE '%$palabraClave%' OR email LIKE '%$palabraClave%' OR pais LIKE '%$palabraClave%' OR razon_social LIKE '%$palabraClave%' OR direccion_fiscal LIKE '%$palabraClave%' OR rfc LIKE '%$palabraClave%' ORDER BY nombre ASC";
}else if(!isset($_POST['filtroPalabra']) || empty($_POST['palabraClave'])){
        $query_buscar = "SELECT solicitud_certificacion.*, opp.idopp,opp.nombre, opp.pais FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp ORDER BY solicitud_certificacion.fecha_elaboracion DESC";

}
/*}else if(isset($_POST['filtroPais']) && $_POST['filtroPais'] == "2" && $_POST['busquedaPais'] != NULL){
  $pais = $_POST['busquedaPais'];
  $query_buscar = "SELECT * FROM opp WHERE pais LIKE '%$pais%'";
}else{
  $query_buscar = "SELECT * FROM opp ORDER BY nombre ASC";
}*/

       $ejecutar_busqueda = mysql_query($query_buscar, $dspp) or die(mysql_error());


/*if(isset($_GET['query'])){
  $idoc = $_GET['query'];
  
  $query_buscar = "SELECT opp.* ,solicitud_certificacion.* FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE solicitud_certificacion.idoc = $idoc ORDER BY solicitud_certificacion.fecha_elaboracion DESC";
}else if(isset($_POST['buscar'])){

  $query_buscar = "";

    if(isset($_POST['fecha_txt'])){
      $fecha = date("d/m/Y", strtotime($_POST['fecha_txt']));
    }
    if(isset($_POST['opp_slc'])){
      $opp = $_POST['opp_slc'];
    }
    if(isset($_POST['oc_slc'])){
      $oc = $_POST['oc_slc'];
    }


    if(!empty($_POST['fecha_txt'])){
      
      if(!empty($_POST['opp_slc'])){
        
        if(!empty($_POST['oc_slc'])){

          /**************************************************************/
/*
          $query_buscar = "SELECT opp.* ,solicitud_certificacion.* FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE solicitud_certificacion.idopp = $opp AND solicitud_certificacion.idoc = $oc AND FROM_UNIXTIME(solicitud_certificacion.fecha_elaboracion, '%d/%m/%Y' ) = '$fecha'  ORDER BY solicitud_certificacion.fecha_elaboracion DESC";
          //$fecha + $opp + $oc;
        }else{
          $query_buscar = "SELECT opp.* ,solicitud_certificacion.* FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE solicitud_certificacion.idopp = $opp AND FROM_UNIXTIME(solicitud_certificacion.fecha_elaboracion, '%d/%m/%Y' ) = '$fecha' ORDER BY solicitud_certificacion.fecha_elaboracion DESC";
          //$query_buscar = "opp+fecha  ----------------- ORDER BY solicitud_certificacion.fecha_elaboracion DESC";
          //$fecha + $opp;
        }
      }else{
        if(!empty($_POST['oc_slc'])){
          $query_buscar = "SELECT opp.* ,solicitud_certificacion.* FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE  solicitud_certificacion.idoc = $oc AND FROM_UNIXTIME(solicitud_certificacion.fecha_elaboracion, '%d/%m/%Y' ) = '$fecha' ORDER BY solicitud_certificacion.fecha_elaboracion DESC";
          //$query_buscar = "fecha+oc   -------------------- ORDER BY solicitud_certificacion.fecha_elaboracion DESC";

          //$fecha + $oc;
        }else{
            /**************************************************************/
     /*     $query_buscar = "SELECT opp.* ,solicitud_certificacion.* FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE FROM_UNIXTIME(solicitud_certificacion.fecha_elaboracion, '%d/%m/%Y' ) = '$fecha' ORDER BY solicitud_certificacion.fecha_elaboracion DESC";
  //        $query_buscar = "fecha ORDER BY solicitud_certificacion.fecha_elaboracion DESC";

          //$fecha;
        }
      }


    }else{
      if(!empty($_POST['opp_slc'])){
        if(!empty($_POST['oc_slc'])){
          $query_buscar = "SELECT opp.* ,solicitud_certificacion.* FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE solicitud_certificacion.idopp = $opp AND solicitud_certificacion.idoc = $oc ORDER BY solicitud_certificacion.fecha_elaboracion DESC";
          //$query_buscar = "opp+oc -------------------- ORDER BY solicitud_certificacion.fecha_elaboracion DESC";

          //$opp + $oc;
        }else{
          $query_buscar = "SELECT opp.* ,solicitud_certificacion.* FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE solicitud_certificacion.idopp = $opp ORDER BY solicitud_certificacion.fecha_elaboracion DESC";

          //$query_buscar = "opp -------------------- ORDER BY solicitud_certificacion.fecha_elaboracion DESC";
        }
      }else{
        if(!empty($_POST['oc_slc'])){
         // $query_buscar = "oc -------------------- ORDER BY solicitud_certificacion.fecha_elaboracion DESC";

          $query_buscar = "SELECT opp.* ,solicitud_certificacion.* FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE solicitud_certificacion.idoc = $oc ORDER BY solicitud_certificacion.fecha_elaboracion DESC";
     
          //$oc;
        }else{
          $query_buscar = "SELECT solicitud_certificacion.*, opp.idopp,opp.nombre, opp.pais FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp ORDER BY solicitud_certificacion.fecha_elaboracion DESC";


        }
      }
    }
       $ejecutar_busqueda = mysql_query($query_buscar, $dspp) or die(mysql_error());

}else{*/


/*}*/

if (isset($_GET['totalRows_opp'])) {
  $totalRows_opp = $_GET['totalRows_opp'];
} else {
  $all_opp = mysql_query($query_opp);
  $totalRows_opp = mysql_num_rows($all_opp);
}
$totalPages_opp = ceil($totalRows_opp/$maxRows_opp)-1;

$queryString_opp = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_opp") == false && 
        stristr($param, "totalRows_opp") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_opp = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_opp = sprintf("&totalRows_opp=%d%s", $totalRows_opp, $queryString_opp);


///////////////////////////////////// VARIABLES DE CONTROL ////////////////////////////////////////

  $validacionStatus = $registro_busqueda['status'] != 1 && $registro_busqueda['status'] != 2 && $registro_busqueda['status'] != 3 && $registro_busqueda['status'] != 14 && $registro_busqueda['status'] != 15;

///////////////////////////////////// VARIABLES DE CONTROL ////////////////////////////////////////

?>



  <hr>
  <div class="row">
    <div class="col-xs-6">
      <h5 class="alert alert-info" >Busqueda extendida(idf, nombre, abreviacion, sitio web, email, país, etc...). Sensible a acentos.</h5>

      <form method="post" name="filtro" action="" enctype="application/x-www-form-urlencoded">
        <div class="input-group">
          <input type="text" class="form-control" name="palabraClave" placeholder="Palabra clave...">
          <span class="input-group-btn">
            <input type="hidden" name="filtroPalabra" value="1">
            <button class="btn btn-default" type="submit">Buscar !</button>
          </span>
        </div><!-- /input-group -->        
      </form>
    </div><!-- /.col-lg-6 -->

    <div class="col-xs-4">
      <h5 class="alert alert-info" >Consultar OPPs por país. Sensible a acentos</h5>
      <form method="post" name="filtro2" action="" enctype="application/x-www-form-urlencoded">      
        <select class="form-control chosen-select-deselect" data-placeholder="Buscar por país" name="busquedaPais" id="" onchange="this.form.submit()">
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
      </form>
    </div>
  </div>

<hr>

<div class="table-responsive" style="height:500px;">
  <table class="table table-condensed table-bordered table-hover" style="overflow: scroll;">
      <tr class="success">
        <th class="text-center"><h5><b>Fecha<br>Solicitud</b></h5></th>
        <th class="text-center"><h5>Nombre</h5></th>
        <th class="text-ceter"><h5>Certificadora</h5></th>
        <th class="text-center" colspan="3"><h5>Cotizaciones</h5></th>
        <!--<th class="text-center">Sitio WEB</th>-->
        <th class="text-center"><h5>Contacto OPP</h5></th>
        <th class="text-center"><h5>País</h5></th>
        <th class="text-center"><h5>Status <br>Publico</h5></th>
        <th class="text-center"><h5>Status <br>Interno</h5></th>
        <!--<th class="text-center">Propuesta</th>-->
        <th class="text-center" colspan="2"><h5>Periodo de Objeción</h5></th>
        <th class="text-center"><h5>Certificado/<br>Membresia</h5></th>
        <th class="text-center"><h5>Observaciones</h5></th>
        <!--<th>OC</th>
        <th>Razón social</th>
        <th>Dirección fiscal</th>
        <th>RFC</th>-->
        <!--<th>Eliminar</th>-->
      </tr>

      <?php mysql_select_db($database_dspp, $dspp); ?>


      <?php $cont=0; while($registro_busqueda = mysql_fetch_assoc($ejecutar_busqueda)){ $cont++;?>
        <tr>
      <?php  $fecha = $registro_busqueda['fecha_elaboracion']; ?> 

          <!---------------------------------------- SECCION ULTIMA ACTUALIZACION -------------------------------------->
          <td>
            <h6>
              <a class="btn btn-primary btn-sm" style="width:100%" href="?OC&amp;detailBlock&amp;query=<?php echo $registro_busqueda['idoc']; ?>&amp;formato=<?php echo $registro_busqueda['idsolicitud_certificacion']; ?>">
                <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> <?php echo  date("d/m/Y", $fecha); ?><br>Ver solicitud
              </a> 
            </h6>         
          </td>
          <!---------------------------------------- SECCION ULTIMA ACTUALIZACION -------------------------------------->

          <!---------------------------------------- SECCION  NOMBRE -------------------------------------->
          <td>
            <h6 class="text-center">
              <?php 
                if(isset($registro_busqueda['nombre'])){
                  echo "<p class='alert alert-success'>".$registro_busqueda['nombre']."</p>";
                }else{
                  echo "<p class='alert alert-danger'>No Disponible</p>";
                } 
              ?>
            </h6>
          </td>
          <!---------------------------------------- SECCION  NOMBRE -------------------------------------->

          <!---------------------------------------- SECCION  CERTIFICADORA -------------------------------------->
          <td>
            <h6 class="text-center">
              <?php 
                if(isset($registro_busqueda['idoc'])){
                  $query = "SELECT idoc,nombre,abreviacion FROM oc WHERE idoc = $registro_busqueda[idoc]";
                  $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
                  $datosOC = mysql_fetch_assoc($ejecutar);
                  echo "<p class='alert alert-success'>".$datosOC['abreviacion']."</p>";
                }else{
                  echo "<p class='alert alert-danger'>No Disponible</p>";
                } 
              ?>
            </h6>
          </td>
          <!---------------------------------------- SECCION  CERTIFICADORA -------------------------------------->

        <!----------------------------------------- COTIZACION OPP ---------------------------------------------->
        <td>
          <h6>
            <a href="http://d-spp.org/oc/<?echo $registro_busqueda['cotizacion_opp']?>" target="_blank" type="button" class="btn <?php if(empty($registro_busqueda['cotizacion_opp'])){ echo 'btn-danger btn-sm';}else{echo 'btn-success btn-sm';} ?>" aria-label="Left Align" <?php if(empty($registro_busqueda['cotizacion_opp'])){echo "disabled";}?>>
              <span class="glyphicon glyphicon-download-alt"></span> OPP
            </a> 
          </h6>
        </td>
        <!----------------------------------------- COTIZACION OPP ---------------------------------------------->


        <!----------------------------------------- COTIZACION FUNDEPPO ---------------------------------------------->
        <td>
          <h6>
            <a href="http://d-spp.org/oc/<?echo $registro_busqueda['cotizacion_adm']?>" target="_blank" type="button" class="btn <?php if(empty($registro_busqueda['cotizacion_adm'])){ echo 'btn-danger btn-sm';}else{echo 'btn-success btn-sm';} ?>" aria-label="Left Align" <?php if(empty($registro_busqueda['cotizacion_adm'])){echo "disabled";}?>>
              <span class="glyphicon glyphicon-download-alt"></span> FUNDEPPO
            </a> 
          </h6>       
        </td>
        <!----------------------------------------- COTIZACION FUNDEPPO ---------------------------------------------->


          <!--------------------------------------- SECCION PROPUESTA -------------------------------------->
          <td>
            <?php 
              if($registro_busqueda['status'] != 1 && $registro_busqueda['status'] != 2 && $registro_busqueda['status'] != 3 && $registro_busqueda['status'] != 14 && $registro_busqueda['status'] != 15 && $registro_busqueda['status'] != 17){
            ?>
              <h6 class="alert alert-success">Aceptada</h6>
                  <!--<button class="btn btn-success btn-sm" disabled>
                    <span class="glyphicon glyphicon-check" aria-hidden="true"></span> Aceptada
                  </button>-->          
              <?php 
                }else{
              ?>
                <h6 class="alert alert-danger">Pendiente</h6>
                  <!--<button class="btn btn-default btn-sm" disabled>
                    <span class="glyphicon glyphicon-check" aria-hidden="true"></span> Aceptada
                  </button> -->              
            <?php 
              }
            ?>              
          </td>
          <!--------------------------------------- SECCION PROPUESTA -------------------------------------->


          <!--<td>
            <small><?php if(empty($registro_busqueda['sitio_web'])){echo "Sitio Web no disponible";}else{echo $registro_busqueda['sitio_web'];} ?></small>
          </td>-->

          <!---------------------------- SECCION EMAIL ---------------------------->
          <td>
            <h6 class="text-center">
              <?php 
                if(isset($registro_busqueda['p1_email'])){
                  echo "<p class='alert alert-success'>".$registro_busqueda['p1_email']."</p>";
                }else{
                  echo "<p class='alert alert-danger'>No Disponible</p>";
                } 
              ?>
            </h6>
          </td>
          <!---------------------------- SECCION EMAIL ---------------------------->

          <!---------------------------- SECCION PAIS ---------------------------->
          <td>
            <h6 class="text-center">
              <?php 
                if(isset($registro_busqueda['pais'])){
                  echo "<p class='alert alert-success'>".$registro_busqueda['pais']."</p>";
                }else{
                  echo "<p class='alert alert-danger'>No Disponible</p>";
                } 
              ?>
            </h6>
          </td>
          <!---------------------------- SECCION PAIS ---------------------------->

          <!------------------------------------ SECCION STATUS PUBLICO ------------------------------------>
          <td>
            <?php 
              $query_status = "SELECT * FROM status_publico WHERE idstatus_publico = $registro_busqueda[status_publico]";
              $ejecutar = mysql_query($query_status,$dspp) or die(mysql_error());
              $estatus_publico = mysql_fetch_assoc($ejecutar);
             ?>

            <h6>
              <?php 
                if($registro_busqueda['status'] == 10){
                  echo "<p class='text-center alert alert-success'><b><u>Certificado</u></b></p>";
                }else{
                   echo "<p class='text-center alert alert-warning'>".$estatus_publico['nombre']."</p>"; 
                }
              ?>
            </h6>
          </td>
          <!------------------------------------ SECCION STATUS PUBLICO ------------------------------------>


          <!------------------------------------ SECCION STATUS INTERNO ------------------------------------>
          <td>
            <?php 
              $query_status = "SELECT * FROM status WHERE idstatus = $registro_busqueda[status]";
              $ejecutar = mysql_query($query_status,$dspp) or die(mysql_error());
              $estatus_interno = mysql_fetch_assoc($ejecutar);

              if($registro_busqueda['status'] == 4 || $registro_busqueda['status'] == 11 || $registro_busqueda['status'] == 13 || $registro_busqueda['status'] == 14 || $registro_busqueda['status'] == 15){
                $colorEstado = "class='text-center alert alert-danger'";
              }else if($registro_busqueda['status'] == 10){
                $colorEstado = "class='text-center alert alert-success'";
              }else{
                $colorEstado = "class='text-center alert alert-warning'";
              }
             ?>

            <h6 <?echo $colorEstado;?>>
              <?php echo $estatus_interno['nombre']; ?>
            </h6>
          </td>
          <!------------------------------------ SECCION STATUS INTERNO ------------------------------------>



              <!-- consulta sobre los datos de objecion -->
              <?php 
               // $queryObjecion = "SELECT * FROM objecion WHERE idopp = $registro_busqueda[idopp] AND idoc = $registro_busqueda[idoc]";
                $queryObjecion = "SELECT * FROM objecion WHERE idsolicitud = $registro_busqueda[idsolicitud_certificacion]";
                $resultado = mysql_query($queryObjecion,$dspp) or die(mysql_error());

                $resultado2 = mysql_fetch_assoc($resultado);

               ?>
              <!-- consulta sobre los datos de objecion -->

            <!--INICIA PERIODO OBJECIÓN-->
          <?php if($registro_busqueda['status'] != 1 && $registro_busqueda['status'] != 2 && $registro_busqueda['status'] != 3 && $registro_busqueda['status'] != 14 && $registro_busqueda['status'] != 15 && $registro_busqueda['status'] != 17){ ?>

          <!-------------------- INICIA SECCION PERIODO DE OBJECION -------------------->
            <td>
              <h6>
                <button <?if(isset($resultado2['idobjecion'])){echo "class='btn btn-success btn-sm'";}else{echo "class='btn btn-danger btn-sm'";}?> data-toggle="modal" <?php echo "data-target='#myModal".$registro_busqueda['idsolicitud_certificacion']."'"?> >
                  <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Periodo Objeción
                </button>
              </h6>

                          <?php 
                            $queryCertificado = "SELECT * FROM certificado WHERE idsolicitud = $registro_busqueda[idsolicitud_certificacion]";
                            $ejecutarCertificado = mysql_query($queryCertificado,$dspp) or die(mysql_error());
                            $certificado = mysql_fetch_assoc($ejecutarCertificado);
                            $num = mysql_num_rows($ejecutarCertificado);

                            if($num != 0 && isset($certificado['statuspago'])){

                              $queryMembresia = "SELECT membresia.*, fecha.*, MAX(fecha.fecha) AS 'ultimafecha' FROM membresia INNER JOIN fecha ON membresia.idmembresia = fecha.idexterno WHERE membresia.idopp = $registro_busqueda[idopp] AND fecha.identificador = 'membresia'";
                              $ejecutar = mysql_query($queryMembresia, $dspp) or die(mysql_error());

                           
                                $membresia = mysql_fetch_assoc($ejecutar);

                                if(isset($membresia['idmembresia'])){
                                  $queryStatus = "SELECT * FROM fecha WHERE fecha = $membresia[ultimafecha]";
                                  $eje = mysql_query($queryStatus,$dspp) or die(mysql_error());
                                  $registroStatus = mysql_fetch_assoc($eje);
                                }
                            
                            }                           
                           ?>


              <!-- Modal -->
              <form action="" method="post" id="periodoObjecion" enctype="application/x-www-form-urlencoded">
                <div class="modal fade" <?php echo "id='myModal".$registro_busqueda['idsolicitud_certificacion']."'" ?> tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Periodo de Objeción</h4>
                      </div>
                      <div class="modal-body">
                        <div class="row">
                          <div class="col-xs-12">
                            <div class="col-xs-6 form-group has-success">
                              <label class="control-label" for="inputSuccess1">Observaciones</label>
                              <?php if(isset($resultado2['observacion'])){ ?>
                                <textarea name="observacion_txt" class="form-control" id="inputSuccess1" cols="7" rows="7" disabled><?php echo $resultado2['observacion']; ?></textarea>
                              <?php }else{ ?>
                                <textarea name="observacion_txt" class="form-control" id="inputSuccess1" cols="7" rows="7"></textarea>
                              <?php } ?>
                              
                            </div>
                            <div class="col-xs-6">
                              <label class="control-label" for="fechaInicio">Fecha de Inicio</label>
                              <?php if(isset($resultado2['fechainicio'])){ ?>
                                <input class="form-control" name="fechaInicio" id="fechaInicio" type="date" placeholder="dd/mm/aaaa" value="<?echo $resultado2['fechainicio'];?>" disabled>
                              <?php }else{ ?>
                                <input class="form-control" name="fechaInicio" id="fechaInicio" type="date" placeholder="dd/mm/aaaa" required>
                              <?php } ?>
                              <hr>
                              <label class="control-label" for="fechaFin">Fecha Final</label>
                              <?php if(isset($resultado2['fechainicio'])){ ?>
                                <input class="form-control" name="fechaFin" id="fechaFin" type="date" placeholder="dd/mm/aaaa" value="<?echo $resultado2['fechafin'];?>" disabled>
                              <?php }else{ ?>
                                <input class="form-control" name="fechaFin" id="fechaFin" type="date" placeholder="dd/mm/aaaa" required>
                              <?php } ?>                                                          
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                        <?php if(empty($resultado2['fechainicio'])){ ?>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <?php } ?>
                        <input type="hidden" name="emailOPP1" value="<?php echo $registro_busqueda['p1_email'];?>">
                        <input type="hidden" name="emailOPP2" value="<?php echo $registro_busqueda['p2_email'];?>"> 
                        <input type="hidden" name="objecionIdOpp_hdn" value="<?php echo $registro_busqueda['idopp'];?>">
                        <input type="hidden" name="objecionIdOc_hdn" value="<?php echo $registro_busqueda['idoc'];?>">
                        <input type="hidden" name="statusObjecion_hdn" value="6">
                        <input type="hidden" name="insertarObjecion" value="periodoObjecion">
                        <input type="hidden" name="idsolicitud" value="<?echo $registro_busqueda['idsolicitud_certificacion'];?>">

                      </div>
                    </div>
                  </div>
                </div>
              </form>
              <!-- Modal -->
            </td>
            <!------------------------------- INICIA SECCION RESOLUCIÓN DE OBJECION ------------------------------>
            <td>
              <?php if(isset($resultado2['status']) && $resultado2['status'] == "6" || $resultado2['status'] == "7"){ ?>
                <h6>
                  <button <?if(!empty($resultado2['adjunto'])){echo "class='btn btn-success btn-sm'";}else{echo "class='btn btn-danger btn-sm'";}?> data-toggle="modal" <?php echo "data-target='#resolucion".$registro_busqueda['idsolicitud_certificacion']."'"?>>
                    <span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Resolución <br>de Objeción
                  </button>
                </h6>
              <?php }else{ ?>
                <h6>
                  <button class="btn btn-danger btn-sm" data-toggle="modal" <?php echo "data-target='#resolucion".$registro_busqueda['idsolicitud_certificacion']."'"?> disabled>
                    <span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Resolución <br>de Objeción
                  </button>
                </h6>
              <?php } ?>
      
              <?php 
                if(isset($resultado2['idobjecion'])){
               ?>    
              <!-- Modal -->
              <form action="" method="POST" enctype="multipart/form-data">
                
                <div class="modal fade" <?php echo "id='resolucion".$registro_busqueda['idsolicitud_certificacion']."'" ?> tabindex="-1" role="dialog" aria-labelledby="resolucionLabel">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="resolucionLabel">Resolución de Objeción</h4>
                      </div>

                      <div class="modal-body">
                        <div class="row">
                          <div class="col-xs-12">
                            <div class="col-xs-6">
                              <h4 class="control-label" for="status">Status</h4>

                              <?php 
                                $query = "SELECT * FROM status_publico WHERE idstatus_publico = $resultado2[status]";
                                $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
                                $statusResolucion = mysql_fetch_assoc($ejecutar);
                              ?>
                                <p name="status" class="alert alert-success">

                                  <? echo $statusResolucion['nombre'];?>
                                </p>

                            </div>
                            <div class="col-xs-6">
                              <?php if(!empty($resultado2['adjunto'])){ ?>
                                <h4 class="control-label" for="descarga">Descargar Resolución</h4>
                                <br>
                                <a class="col-xs-12 btn btn-info" style="margin-top:-10px;" role="button" name="descarga" href="<?echo $resultado2['adjunto']?>"><span aria-hidden="true" class="glyphicon glyphicon-download-alt"></span> Descargar</a>
                              <?php }else{ ?>
                                <h4 class="control-label" for="">Adjuntar archivo</h4>
                                <input name="adjunto_fld" id="adjunto_fld" type="file" class="filestyle" data-buttonName="btn-info" data-buttonBefore="true" data-buttonText="Cargar Archivo" required> 
                              <?php } ?>     
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                        <?php if(empty($resultado2['adjunto'])){ ?>
                            <button type="submit" class="btn btn-primary">Finalizar</button>
                        <?php } ?>
                          <input type="hidden" name="emailOPP1" value="<?php echo $registro_busqueda['p1_email'];?>">
                          <input type="hidden" name="emailOPP2" value="<?php echo $registro_busqueda['p2_email'];?>"> 
                          <input type="hidden" name="objecionIdOpp_hdn" value="<?php echo $registro_busqueda['idopp'];?>">
                          <input type="hidden" name="objecionIdOc_hdn" value="<?php echo $registro_busqueda['idoc'];?>">
                          <input type="hidden" name="statusObjecion_hdn" value="7">
                          <input type="hidden" name="statusInterno" value="19">
                          <input type="hidden" name="idobjecion" value="<? echo $resultado2['idobjecion'];?>">
                          <input type="hidden" name="idsolicitud" value="<?echo $registro_busqueda['idsolicitud_certificacion'];?>">                       
                          <input type="hidden" name="resolucionObjecion" value="resolucionObjecion">


                      </div>
                    </div>
                  </div>
                </div>

              </form>
              <!-- Modal -->
              <?php 
                } 
              ?>



            </td>
          <!-------------------- TERMINAR SECCION PERIODO DE OBJECION -------------------->

            <!--FIN PERIODO OBJECIÓN--> 
            
            <!--------------------------INICIA STATUS CERTIFICACION---------------------------->
             <td>
               <h6>
                <button class="btn btn-warning btn-sm" data-toggle="modal" <?php echo "data-target='#certificado".$registro_busqueda['idsolicitud_certificacion']."'"?>>
                  <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Consultar<br>Status
                </button>
               </h6>     
            </td>

            <?php 
              $query = "SELECT * FROM fecha";
              $ejecutar1 = mysql_query($query,$dspp) or die(mysql_error());
              $numero = mysql_num_rows($ejecutar1);

              if($numero != 0 && isset($certificado['idcertificado'])){
                $queryFecha = "SELECT fecha.*, MAX(fecha) AS 'ultimaFecha' FROM fecha WHERE idexterno = $certificado[idcertificado] AND identificador = 'certificado'";
                $ejecutar = mysql_query($queryFecha,$dspp) or die(mysql_error());
                $registroFecha = mysql_fetch_assoc($ejecutar);
              }
             ?>
              <!-- Modal -->
              <form action="" method="POST" enctype="multipart/form-data">
                
                <div class="modal fade" <?php echo "id='certificado".$registro_busqueda['idsolicitud_certificacion']."'" ?> tabindex="-1" role="dialog" aria-labelledby="resolucionLabel">
                  <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="resolucionLabel">STATUS CERTIFICADO / MEMBRESIA</h4>
                      </div>

                      <div class="modal-body">
                        <div class="row">
                          <div class="col-xs-12">

                            <div class="col-xs-6">
                              <?php if(empty($certificado['status'])){ ?>
                                <?php if($registro_busqueda['status'] == 19){ ?>
                                  <div class="col-xs-12 alert alert-warning" role="alert">
                                    <div class="col-xs-12">
                                      Se ha iniciado la certificación.
                                    </div>        
                                  </div> 
                                <?php }else{ ?>
                                  <div class="col-xs-12 alert alert-danger" role="alert">
                                    <div class="col-xs-12">
                                      No se ha iniciado la certificación.
                                    </div>        
                                  </div> 
                                <?php } ?>
                              <?php }else{ ?>
                                <div class="col-xs-12 alert alert-success" role="alert">
                                  <div class="col-xs-12">Status Certificado al día: <b><?echo date("Y/m/d", $registroFecha['ultimaFecha']) ?></b></div>
                                  <hr>
                                  <div class="col-xs-12 ">
                                    <?
                                      $query = "SELECT * FROM status WHERE idstatus = $certificado[status]";
                                      $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
                                      $estatus = mysql_fetch_assoc($ejecutar);
                                    ?>
                                    <h4><?php echo $estatus['nombre']; ?></h4>
                                   
                                  </div>        
                                </div>                               
                              <?php } ?>
                              <?php if(empty($certificado['adjunto'])){ ?>
                                <div class="col-xs-12 alert alert-danger">
                                    <div class="col-xs-12"><h4>Certificado</h4></div>
                                    <hr>
                                    <div class="col-xs-12">No se ha finalizado el proceso de certificación</div>
                                </div>                               
                              <?php }else{ ?>
                                <div class="col-xs-12 alert alert-info">
                                    <div class="col-xs-12"><h4>Certificado</h4></div>
                                    <hr>
                                    <div class="col-xs-6">Su certificado vence el dia: <?echo $certificado['vigenciafin']?></div>
                                    <div class="col-xs-6">
                                      <a class="btn btn-success" href="http://d-spp.org/oc/<?echo $certificado['adjunto'];?>" target="_blank">Descargar Certificado</a>
                                    </div> 

                                </div> 
                              <?php } ?>   
                            </div>                              

                            <div class="col-xs-6">
                              <?php if(empty($certificado['adjunto'])){ ?>
                                <div class="col-xs-12 alert alert-danger">
                                  <p style="text-align:justify"><strong>No se ha completado el proceso de certificación, una vez completado se iniciara el proceso de pago de membresia.</strong></p>
                                </div>
                              <?php }else{ ?>
                                <?php if(empty($membresia['idmembresia'])){ ?>
                                  <div class="col-xs-12 alert alert-warning">
                                    <p><strong>No se ha realizado el pago correpondiente, intente revisar más tarde.</strong></p>
                                  </div>
                                <?php }else if(isset($membresia['adjunto'])){ ?>
                                  <div class="col-xs-12 alert alert-info">
                                    <h4>Membresia</h4>
                                    <hr>
                                    <div class="col-xs-8 alert alert-success">
                                      <div class="col-xs-12 text-center">
                                        Fecha: <?echo date("d/m/Y",$membresia['ultimafecha']);?>
                                      </div>
                                      <hr>
                                      <div class="col-xs-12">
                                        <small>Membresia: <strong><?echo $registroStatus['status'];?></strong></small>
                                      </div>
                                     
                                    </div>

                                    <div class="col-xs-4">
                                      <strong>Comprobante</strong>
                                      <a href="http://d-spp.org/opp/<?echo $membresia['adjunto']?>" class="btn btn-danger btn-sm" target="_blank">Descargar <br>Comprobante</a>
                                      
                                    </div>
                                    <?php if($registroStatus['status'] != "APROBADO"){ ?>
                                      <div class="col-xs-12">
                     
                                          <button class="btn btn-success btn-sm" type="submit" name="aprobar" value="aprobado">Aprobar</button>
                                          <button class="btn btn-danger btn-sm" typw="submit" name="denegar" value="denegado">Denegar</button>
                                          <input type="hidden" name="idmembresia" value="<?echo $membresia['idmembresia'];?>">
                                          <input type="text" name="idopp" value="<?php echo $registro_busqueda['idopp'];?>">
                                          <input type="hidden" name="idcertificado" value="<?php echo $certificado['idcertificado'];?>">
                                          <input type="hidden" name="emailOPP1" value="<?php echo $registro_busqueda['p1_email'];?>">
                                          <input type="hidden" name="emailOPP2" value="<?php echo $registro_busqueda['p2_email'];?>">
                                          <input type="hidden" name="statusInterno" value="10">
                                          <input type="hidden" name="fecha" value="<?echo time()?>">
                                          <input type="hidden" name="comprobanteMembresia" value="2">
                                      </div>                                    
                                    <?php } ?>
                                  </div>                                
                                <?php } ?>

                              <?php } ?>
                            </div>

                          </div>
                        </div>
                      </div>

                      <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                        <?php if(empty($resultado2['adjunto'])){ ?>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        <?php } ?>
                          <input type="hidden" name="objecionIdOpp_hdn" value="<?php echo $registro_busqueda['idopp'];?>">
                          <input type="hidden" name="objecionIdOc_hdn" value="<?php echo $registro_busqueda['idoc'];?>">
                          <input type="hidden" name="statusObjecion_hdn" value="Inicia Periodo de Objeción">
                          
                          <input type="hidden" name="idobjecion" value="<? echo $resultado2['idobjecion'];?>">
                          <input type="hidden" name="idsolicitud" value="<?echo $registro_busqueda['idsolicitud_certificacion'];?>">                          


                      </div>
                    </div>
                  </div>
                </div>

              </form>
              <!-- Modal -->


            <!---------------------------FIN STATUS CERTIFICACION---------------------------->
          <?php }else{ ?>
            <td>
                <!--<?php echo $registro_busqueda["idsolicitud_certificacion"]; ?>-->

              <h6>
                <button class="btn btn-sm btn-default" disabled>Periodo Objeción</button>
              </h6>
            </td>
            <td>
                <h6>
                  <button class="btn btn-default btn-sm" data-toggle="modal" <?php echo "data-target='#resolucion".$registro_busqueda['idsolicitud_certificacion']."'"?> disabled>
                    <span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Resolución <br>de Objeción
                  </button>
                </h6>
            </td>
            <!--FIN PERIODO OBJECIÓN--> 
            
            <!--INICIA CERTIFICACION-->
             <td>     
                <h6>
                  <button class="btn btn-default" data-toggle="modal" <?php echo "data-target='#myModal".$row_opp['idsolicitud_certificacion']."'"?> disabled>
                    <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Status
                  </button>
                </h6>    
            </td>

            <!--FIN CERTIFICACION-->          
          <?php } ?>
          <td>
            <h6>
              <?php if(empty($registro_busqueda['observaciones'])){ ?>
                <button class="btn btn-default btn-sm" disabled>
                  <span class="glyphicon glyphicon-list-alt"></span> Consultar
                </button>         
              <?php }else{ ?>
                <a class="btn btn-info btn-sm" href="?OC&amp;detailBlock&amp;query=<?php echo $registro_busqueda['idoc']; ?>&amp;formato=<?php echo $registro_busqueda['idsolicitud_certificacion']; ?>">
                  <span class="glyphicon glyphicon-list-alt"></span> Consultar
                </a>
              <?php } ?>
            </h6>
          </td>
        <!--<td>-->
        </tr>

      <?php } ?>

      <? if($cont==0){?>
      <tr><td colspan="12" class="alert alert-info" role="alert">No se encontraron registros</td></tr>

      <? }?>
  </table>
</div>



<table>
  <tr>
  <td width="20"><?php if ($pageNum_opp > 0) { // Show if not first page ?>
  <a href="<?php printf("%s?pageNum_opp=%d%s", $currentPage, 0, $queryString_opp); ?>">
  <span class="glyphicon glyphicon-fast-backward" aria-hidden="true"></span>
  </a>
  <?php } // Show if not first page ?></td>
  <td width="20"><?php if ($pageNum_opp > 0) { // Show if not first page ?>
  <a href="<?php printf("%s?pageNum_opp=%d%s", $currentPage, max(0, $pageNum_opp - 1), $queryString_opp); ?>">
  <span class="glyphicon glyphicon-backward" aria-hidden="true"></span>
  </a>
  <?php } // Show if not first page ?></td>
  <td width="20"><?php if ($pageNum_opp < $totalPages_opp) { // Show if not last page ?>
  <a href="<?php printf("%s?pageNum_opp=%d%s", $currentPage, min($totalPages_opp, $pageNum_opp + 1), $queryString_opp); ?>">
  <span class="glyphicon glyphicon-forward" aria-hidden="true"></span>
  </a>
  <?php } // Show if not last page ?></td>
  <td width="20"><?php if ($pageNum_opp < $totalPages_opp) { // Show if not last page ?>
  <a href="<?php printf("%s?pageNum_opp=%d%s", $currentPage, $totalPages_opp, $queryString_opp); ?>">
  <span class="glyphicon glyphicon-fast-forward" aria-hidden="true"></span>
  </a>
  <?php } // Show if not last page ?></td>
  </tr>
</table>
<?php
mysql_free_result($opp);
?>