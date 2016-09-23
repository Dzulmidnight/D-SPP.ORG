<?php require_once('../Connections/dspp.php'); ?>
<?php
  error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

if (!isset($_SESSION)) {
  session_start();
  
  $redireccion = "../index.php?ADM";

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

if(isset($_POST['com_delete'])){
  $query=sprintf("delete from com where idcom = %s",GetSQLValueString($_POST['idcom'], "text"));
  $ejecutar=mysql_query($query,$dspp) or die(mysql_error());
}

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_com = 20;
$pageNum_com = 0;
if (isset($_GET['pageNum_com'])) {
  $pageNum_com = $_GET['pageNum_com'];
}
$startRow_com = $pageNum_com * $maxRows_com;

mysql_select_db($database_dspp, $dspp);

$query_limit_com = sprintf("%s LIMIT %d, %d", $query_opp, $startRow_com, $maxRows_com);
$com = mysql_query($query_limit_com,$dspp) or die(mysql_error());
//$row_com = mysql_fetch_assoc($com);


if(isset($_POST['comprobanteMembresia']) && $_POST['comprobanteMembresia'] == "2"){
    $fecha_actual = time();
    $fecha = date("d/m/Y", $_POST['fecha']);
    $idsolicitud_registro = $_POST['idsolicitud_registro'];
    $statusInterno = 10;
    
    $idmembresia = $_POST['idmembresia'];
    $identificador = "MEMBRESIA";
    $idcertificado = $_POST['idcertificado'];
    $emailCOM1 = $_POST['emailCOM1'];
    $emailCOM2 = $_POST['emailCOM2'];
    $idcom = $_POST['idcom'];
    $idoc = $_POST['idoc'];
    $idexterno = $idsolicitud_registro;

  if(isset($_POST['aprobar'])){
    $statusCertificado = 10;
    $status = "APROBADO";
  
    $update = "UPDATE membresia SET estado = '$status' WHERE idmembresia = $idmembresia";
    $actualizar = mysql_query($update,$dspp) or die(mysql_error());

    //$insertar = "INSERT INTO fecha (fecha,idexterno,identificador,status) VALUES ($fecha,$idexterno,'$identificador','$status')";
    $insertar = "INSERT INTO fecha (fecha, idexterno, idcom, idoc, idmembresia, identificador,status) VALUES ($fecha_actual, $idexterno, $idcom, $idoc, $idmembresia, '$identificador','$status')";
    $ejecutar = mysql_query($insertar,$dspp) or die(mysql_error());

    $actualizar = "UPDATE certificado SET status = $statusCertificado, statuspago = '$status', idcom = '$idcom' WHERE idcertificado = $idcertificado";
    $ejecutar = mysql_query($actualizar,$dspp) or die(mysql_error());
    //echo $insertar;
    //echo "<br>".$actualizar; 
    $update = "UPDATE solicitud_registro SET status_interno = '$statusInterno' WHERE idsolicitud_registro = $idsolicitud_registro";
    $actualizar = mysql_query($update,$dspp);

    $estadoCOM = 10;

    $update = "UPDATE com SET estado = '$estadoCOM' WHERE idcom = $idcom";
    $actualizar = mysql_query($update,$dspp);
    
   
    $destinatario = $emailCOM1.",";
    $destinatario .= $emailCOM2;

        $asunto = "D-SPP - Membresia Aprobada"; 

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
              <td><b>Felicidades!!!!, su membresia ha sido aprobada, ahora puede disponer de su certificado, para descargar su certificado por favor inicie sesión en su cuenta de COM(<a href="http://www.d-spp.org/?COM">www.d-spp.org/?COM</a>) en la opción de "SOLICITUDES" dentro de la sección certificación.</b></td>
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
        //$headers .= "Return-path: holahola@desarrolloweb.org\r\n"; 

        //direcciones que recibián copia 
        //$headers .= "Cc: maria@desarrolloweb.org\r\n"; 

        //direcciones que recibirán copia oculta 
        $headers .= "Bcc: yasser.midnight@gmail.com\r\n";
        //$headers .= "Bcc: isc.jesusmartinez@gmail.com  \r\n"; 

        mail($destinatario,$asunto,utf8_decode($cuerpo),$headers);

        $queryMensaje = "INSERT INTO mensajes(idcom, asunto, mensaje, destinatario, remitente, fecha) VALUES($idcom, '$asunto', '$cuerpo', 'COM', 'ADM', $fecha_actual)";
        $ejecutar = mysql_query($queryMensaje,$dspp) or die(mysql_error());


  }
  if(isset($_POST['denegar'])){
    $status = "DENEGAR";
    $insertar = "INSERT INTO fecha (fecha,idexterno,identificador,status) VALUES ($fecha_actual,$idexterno,'$identificador','$status')";
    $ejecutar = mysql_query($insertar,$dspp) or die(mysql_error());
    $actualizar = "UPDATE certificado SET statuspago = '$status' WHERE idcertificado = $idcertificado";
    $ejecutar = mysql_query($actualizar,$dspp) or die(mysql_error());

    //echo $insertar; 
    //echo "<br>".$actualizar; 


    $destinatario = $emailCOM1.",";
    $destinatario .= $emailCOM2;

        $asunto = "D-SPP - Comprobación pago de membresia"; 

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
              <td><b>Lo sentimos su comprobante no es el correcto, por favor asegúrese de que el comprobante seá el correcto. Para reenviar el comprobante siga los pasos anteriormente realizados (<a href="http://www.d-spp.org/?COM">www.d-spp.org/?COM</a>).</b></td>
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
        //$headers .= "Return-path: holahola@desarrolloweb.org\r\n"; 

        //direcciones que recibián copia 
        //$headers .= "Cc: maria@desarrolloweb.org\r\n"; 

        //direcciones que recibirán copia oculta 
        $headers .= "Bcc: yasser.midnight@gmail.com\r\n";
        //$headers .= "Bcc: isc.jesusmartinez@gmail.com  \r\n"; 

        mail($destinatario,$asunto,utf8_decode($cuerpo),$headers);

        $queryMensaje = "INSERT INTO mensajes(idcom, asunto, mensaje, destinatario, remitente, fecha) VALUES($idcom, '$asunto', '$cuerpo', 'COM', 'ADM', $fecha_actual)";
        $ejecutar = mysql_query($queryMensaje,$dspp) or die(mysql_error());


  }
}



/**********************************************************************************************/
/*****************************  INICIA INSERTAR OBJECIÓN  ********************************/
/**********************************************************************************************/

if (isset($_POST['insertarObjecion']) && $_POST['insertarObjecion'] == "periodoObjecion") {
  $fecha_actual = time();
  $fechaInicio = $_POST['fechaInicio'];
  $fechaFin = $_POST['fechaFin'];
  $status = $_POST['statusObjecion_hdn'];
  $status_publico = $_POST['statusObjecion_hdn'];
  $observacion = $_POST['observacion_txt'];
  $idcom = $_POST['objecionIdcom_hdn'];
  $idoc = $_POST['objecionIdOc_hdn'];
  $idsolicitud_registro = $_POST['idsolicitud_registro'];
  $idexterno = $idsolicitud_registro;
  $identificador = "OBJECION";

  $query = "INSERT INTO objecion (fechainicio, fechafin, status, observacion, idsolicitud_registro) VALUES ('$fechaInicio', '$fechaFin', '$status', '$observacion', $idsolicitud_registro)";
  $insertarQuery = mysql_query($query, $dspp) or die(mysql_error());
  $idobjecion = mysql_insert_id($dspp);

  //se insertan las fechas de la objecion  
  $insertar = "INSERT INTO fecha (fecha, idexterno, idcom, idoc, idobjecion, identificador, status_publico) VALUES ($fecha_actual, $idexterno, $idcom, $idoc, $idobjecion, '$identificador', $status_publico)";
  $ejecutar = mysql_query($insertar,$dspp) or die(mysql_error());


  $query = "UPDATE solicitud_registro SET status_publico = '$status' WHERE idsolicitud_registro = $idsolicitud_registro";
  $insertar = mysql_query($query,$dspp) or die(mysql_error());



  $query = "SELECT * FROM oc WHERE idoc = $idoc";
  $ejecutar = mysql_query($query,$dspp);
  $row_oc = mysql_fetch_assoc($ejecutar);

  $fecha = date("d/m/Y", time());
  $emailCOM1 = $_POST['emailCOM1'];
  $emailCOM2 = $_POST['emailCOM2'];



      $queryCorreo = "SELECT solicitud_registro.*, com.idcom,com.nombre, com.pais, com.abreviacion AS 'abreviacionCOM', oc.abreviacion AS 'abreviacionOC' FROM solicitud_registro INNER JOIN com ON solicitud_registro.idcom = com.idcom INNER JOIN oc ON solicitud_registro.idoc = oc.idoc WHERE idsolicitud_registro = $idsolicitud_registro";
      $ejecutar = mysql_query($queryCorreo,$dspp) or die(msql_error());
      $datosCorreo = mysql_fetch_assoc($ejecutar);

      $queryProductos = "SELECT producto FROM productos WHERE idsolicitud_registro = $idsolicitud_registro";
      $ejecutar = mysql_query($queryProductos,$dspp) or die(mysql_error());
      $productos = "";
      while($datosProductos = mysql_fetch_assoc($ejecutar)){
        $productos .= $datosProductos['producto']." - ";
      }
      $fecha_elaboracion = date("d/m/Y",$datosCorreo['fecha_elaboracion']);
      $fecha = date("d/m/Y", time());


      $nombreCOM = $datosCorreo['nombre'];
      $abreviacionCOM = $datosCorreo['abreviacionCOM'];
      $paisCOM = $datosCorreo['pais'];
      $abreviacionOC = $datosCorreo['abreviacionOC'];
      $alcance = $datosCorreo['op_resp4'];
      /*****************************INICIO MAIL OC***************************************************/
      /********************************************************************************/


/////////*************** ENVIO EMAIL DE OC *******************//////////////////
////////////////////////////////////////////////////////////////////////////////


      $queryOC = "SELECT email FROM oc WHERE email !=''";
      $ejecutar = mysql_query($queryOC,$dspp) or die(mysql_error());

      $destinatarioOC = "";
      while($emailOC = mysql_fetch_assoc($ejecutar)){
          $destinatarioOC .= $emailOC['email'].',';
      }


              //$destinatario = $emailOC['email'];
              //$headers .= "Bcc: yasser.midnight@gmail.com\r\n";
              //$headers .= "Bcc: isc.jesusmartinez@gmail.com  \r\n"; 

              $asunto = "D-SPP - Notificación de Intenciones / Notification of Intentions"; 

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
                            <td width="72px">Nombre de la Empresa/Company name</td>
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
                              '.$nombreCOM.'
                            </td>
                            <td style="padding:10px;">
                              '.$abreviacionCOM.'
                            </td>
                            <td style="padding:10px;">
                              '.$paisCOM.'
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
                      These notifications are sent by FUNDEPPO in a period of less than 24 hours from the time a corresponding application is received. If objections are presented before getting the Certification, Registration or Authorization, the Certification Entity must incorporate them as part of the same evaluation-process. If the objection is presented when the applicant has already been certified, the SPP Dissents Procedure has to be applied. The new intentions for Certification, Registration and Authorization are detailed at the beginning (of this document.
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
              //$headers .= "Return-path: holahola@desarrolloweb.org\r\n"; 

              //direcciones que recibián copia 
              //$headers .= "Cc: maria@desarrolloweb.org\r\n"; 

              //direcciones que recibirán copia oculta 
              $headers .= "Bcc: yasser.midnight@gmail.com\r\n";
              //$headers .= "Bcc: isc.jesusmartinez@gmail.com  \r\n"; 

              mail($destinatarioOC,$asunto,utf8_decode($cuerpo),$headers);


        $queryMensaje = "INSERT INTO mensajes(idcom, idoc, asunto, mensaje, destinatario, remitente, fecha) VALUES($idcom, $idoc, '$asunto', '$cuerpo', 'OC', 'ADM', $fecha_actual)";
        $ejecutar = mysql_query($queryMensaje,$dspp) or die(mysql_error());



/////////*************** ENVIO EMAIL DE OPP *******************//////////////////
////////////////////////////////////////////////////////////////////////////////

          $queryOPP = "SELECT email FROM opp WHERE email !=''";
          $ejecutar = mysql_query($queryOPP,$dspp) or die(mysql_error());

          $destinatarioOPP = "";
          while($emailOPP = mysql_fetch_assoc($ejecutar)){
              $destinatarioOPP .= $emailOPP['email'].',';
          }


              //$destinatario = $emailOC['email'];
              //$headers .= "Bcc: yasser.midnight@gmail.com\r\n";
              //$headers .= "Bcc: isc.jesusmartinez@gmail.com  \r\n"; 

              $asunto = "D-SPP - Notificación de Intenciones / Notification of Intentions"; 

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
                            <td width="72px">Nombre de la Empresa/Company name</td>
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
                              '.$nombreCOM.'
                            </td>
                            <td style="padding:10px;">
                              '.$abreviacionCOM.'
                            </td>
                            <td style="padding:10px;">
                              '.$paisCOM.'
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
                      These notifications are sent by FUNDEPPO in a period of less than 24 hours from the time a corresponding application is received. If objections are presented before getting the Certification, Registration or Authorization, the Certification Entity must incorporate them as part of the same evaluation-process. If the objection is presented when the applicant has already been certified, the SPP Dissents Procedure has to be applied. The new intentions for Certification, Registration and Authorization are detailed at the beginning (of this document.
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
              //$headers .= "Return-path: holahola@desarrolloweb.org\r\n"; 

              //direcciones que recibián copia 
              //$headers .= "Cc: maria@desarrolloweb.org\r\n"; 

              //direcciones que recibirán copia oculta 
              $headers .= "Bcc: yasser.midnight@gmail.com\r\n";
              //$headers .= "Bcc: isc.jesusmartinez@gmail.com  \r\n"; 

              mail($destinatarioOPP,$asunto,utf8_decode($cuerpo),$headers);

        $queryMensaje = "INSERT INTO mensajes(idcom, idoc, asunto, mensaje, destinatario, remitente, fecha) VALUES($idcom, $idoc, '$asunto', '$cuerpo', 'OPP', 'ADM', $fecha_actual)";
        $ejecutar = mysql_query($queryMensaje,$dspp) or die(mysql_error());



/////////*************** ENVIO EMAIL DE COM(EMPRESAS) *******************//////////////////
////////////////////////////////////////////////////////////////////////////////

          $queryCOM = "SELECT email FROM com WHERE email !=''";
          $ejecutar = mysql_query($queryCOM,$dspp) or die(mysql_error());

          $destinatarioCOM = "";
          while($emailCOM = mysql_fetch_assoc($ejecutar)){
              $destinatarioCOM .= $emailCOM['email'].',';
          }


              //$destinatario = $emailOC['email'];
              //$headers .= "Bcc: yasser.midnight@gmail.com\r\n";
              //$headers .= "Bcc: isc.jesusmartinez@gmail.com  \r\n"; 

              $asunto = "D-SPP - Notificación de Intenciones / Notification of Intentions"; 

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
                            <td width="72px">Nombre de la Empresa/Company name</td>
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
                              '.$nombreCOM.'
                            </td>
                            <td style="padding:10px;">
                              '.$abreviacionCOM.'
                            </td>
                            <td style="padding:10px;">
                              '.$paisCOM.'
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
                      These notifications are sent by FUNDEPPO in a period of less than 24 hours from the time a corresponding application is received. If objections are presented before getting the Certification, Registration or Authorization, the Certification Entity must incorporate them as part of the same evaluation-process. If the objection is presented when the applicant has already been certified, the SPP Dissents Procedure has to be applied. The new intentions for Certification, Registration and Authorization are detailed at the beginning (of this document.
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
              //$headers .= "Return-path: holahola@desarrolloweb.org\r\n"; 

              //direcciones que recibián copia 
              //$headers .= "Cc: maria@desarrolloweb.org\r\n"; 

              //direcciones que recibirán copia oculta 
              $headers .= "Bcc: yasser.midnight@gmail.com\r\n";
              //$headers .= "Bcc: isc.jesusmartinez@gmail.com  \r\n"; 

              mail($destinatarioCOM,$asunto,utf8_decode($cuerpo),$headers);

        $queryMensaje = "INSERT INTO mensajes(idcom, idoc, asunto, mensaje, destinatario, remitente, fecha) VALUES($idcom, $idoc, '$asunto', '$cuerpo', 'COM', 'ADM', $fecha_actual)";
        $ejecutar = mysql_query($queryMensaje,$dspp) or die(mysql_error());




/////////*************** ENVIO EMAIL DE ADM *******************//////////////////
////////////////////////////////////////////////////////////////////////////////

        $destinatarioADM = "";
        $query = "SELECT  email FROM adm";
        $ejecutar = mysql_query($query,$dspp) or die(mysql_error());

        while($emailADM = mysql_fetch_assoc($ejecutar)){  
          if($emailADM['email'] != "isc.jesusmartinez@gmail.com"){
            $destinatarioADM .= $emailADM['email'].',';
          }
        }

 
              //$headers .= "Bcc: yasser.midnight@gmail.com\r\n";
              //$headers .= "Bcc: isc.jesusmartinez@gmail.com  \r\n"; 

              $asunto = "D-SPP - Notificación de Intenciones / Notification of Intentions"; 

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
                            <td width="72px">Nombre de la Empresa/Company name</td>
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
                              '.$nombreCOM.'
                            </td>
                            <td style="padding:10px;">
                              '.$abreviacionCOM.'
                            </td>
                            <td style="padding:10px;">
                              '.$paisCOM.'
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
                      These notifications are sent by FUNDEPPO in a period of less than 24 hours from the time a corresponding application is received. If objections are presented before getting the Certification, Registration or Authorization, the Certification Entity must incorporate them as part of the same evaluation-process. If the objection is presented when the applicant has already been certified, the SPP Dissents Procedure has to be applied. The new intentions for Certification, Registration and Authorization are detailed at the beginning (of this document.
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
              //$headers .= "Return-path: holahola@desarrolloweb.org\r\n"; 

              //direcciones que recibián copia 
              //$headers .= "Cc: maria@desarrolloweb.org\r\n"; 

              //direcciones que recibirán copia oculta 
              $headers .= "Bcc: yasser.midnight@gmail.com\r\n";
              //$headers .= "Bcc: isc.jesusmartinez@gmail.com  \r\n"; 

              mail($destinatarioADM,$asunto,utf8_decode($cuerpo),$headers);


        $queryMensaje = "INSERT INTO mensajes(idcom, idoc, asunto, mensaje, destinatario, remitente, fecha) VALUES($idcom, $idoc, '$asunto', '$cuerpo', 'ADM', 'ADM', $fecha_actual)";
        $ejecutar = mysql_query($queryMensaje,$dspp) or die(mysql_error());


}
/**********************************************************************************************/
/*****************************  FIN INSERTAR OBJECIÓN  ********************************/
/**********************************************************************************************/



/**********************************************************************************************/
/*****************************  INICIA RESOLUCION DE OBJECIÓN  ********************************/
/**********************************************************************************************/

if(isset($_POST['resolucionObjecion']) && $_POST['resolucionObjecion'] == "resolucionObjecion"){
  $fecha = date("d/m/Y", time());
  $fecha_actual = time();
  $ruta = "../../archivos/admArchivos/resolucion/";
  $idobjecion = $_POST['idobjecion'];
  $idcom = $_POST['objecionIdcom_hdn'];
  $idoc = $_POST['objecionIdOc_hdn'];
  $idsolicitud_registro = $_POST['idsolicitud_registro'];

  if(!empty($_FILES['adjunto_fld']['name'])){
    $_FILES['adjunto_fld']['name'];
        move_uploaded_file($_FILES["adjunto_fld"]["tmp_name"], $ruta.$fecha_actual."_".$_FILES["adjunto_fld"]["name"]);
        $objecionAdjunto = $ruta.basename($fecha_actual."_".$_FILES["adjunto_fld"]["name"]);
  }else{
    $objecionAdjunto = NULL;
  }

  $statusObjecion = $_POST['statusObjecion_hdn'];
  $adjunto = $objecionAdjunto;
  $statusInterno = $_POST['statusInterno'];
  $identificador = "OBJECION";
  $idexterno = $idsolicitud_registro;
  $status_publico = $statusObjecion;


  $query = "UPDATE objecion SET status = '$statusObjecion', adjunto = '$adjunto' WHERE idobjecion = $idobjecion";
  $insertarResolucion = mysql_query($query, $dspp) or die(mysql_error());

  $query = "UPDATE solicitud_registro SET status_interno = '$statusInterno', status_publico = '$statusObjecion' WHERE idsolicitud_registro = $idsolicitud_registro";
  $insertar = mysql_query($query,$dspp) or die(mysql_error());

  $insertar = "INSERT INTO fecha (fecha, idexterno, idcom, idoc, idobjecion, identificador, status_publico) VALUES ($fecha_actual, $idexterno, $idcom, $idoc, $idobjecion, '$identificador', $status_publico)";
  $ejecutar = mysql_query($insertar,$dspp) or die(mysql_error());

  $query = "SELECT * FROM oc WHERE idoc = $idoc";
  $ejecutar = mysql_query($query,$dspp);
  $row_oc = mysql_fetch_assoc($ejecutar);


        /************************  INICIAR MAIL OC  *******************************************/

        $destinatario = $row_oc['email'];
        $asunto = "D-SPP - Periodo de Objeción Finalizado"; 

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
        //$headers .= "Return-path: holahola@desarrolloweb.org\r\n"; 

        //direcciones que recibián copia 
        //$headers .= "Cc: maria@desarrolloweb.org\r\n"; 

        //direcciones que recibirán copia oculta 
        $headers .= "Bcc: yasser.midnight@gmail.com\r\n";
        //$headers .= "Bcc: isc.jesusmartinez@gmail.com  \r\n"; 

        mail($destinatario,$asunto,utf8_decode($cuerpo),$headers);

        $queryMensaje = "INSERT INTO mensajes(idcom, idoc, asunto, mensaje, destinatario, remitente, fecha) VALUES($idcom, $idoc, '$asunto', '$cuerpo', 'OC', 'ADM', $fecha_actual)";
        $ejecutar = mysql_query($queryMensaje,$dspp) or die(mysql_error());


        /************************  FIN MAIL OC  *********************************************/


        /************************  INICIA MAIL COM  *******************************************/
      $emailCOM1 = $_POST['emailCOM1'];
      $emailCOM2 = $_POST['emailCOM2'];

      $destinatario = $emailCOM1.",";
      $destinatario .= $emailCOM2;

        $asunto = "D-SPP - Periodo de Objeción Finalizado"; 

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
              <td><b>Ha finalizado el período de objeción, para descargar la resolución de objeción por favor entre en su cuenta de COM en el siguiente enlace <a href="http://d-spp.org/?COM">www.d-spp.org/?COM</a> .</b></td>
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
        //$headers .= "Return-path: holahola@desarrolloweb.org\r\n"; 

        //direcciones que recibián copia 
        //$headers .= "Cc: maria@desarrolloweb.org\r\n"; 

        //direcciones que recibirán copia oculta 
        $headers .= "Bcc: yasser.midnight@gmail.com\r\n";
        //$headers .= "Bcc: isc.jesusmartinez@gmail.com  \r\n"; 

        mail($destinatario,$asunto,utf8_decode($cuerpo),$headers);

        $queryMensaje = "INSERT INTO mensajes(idcom, idoc, asunto, mensaje, destinatario, remitente, fecha) VALUES($idcom, $idoc, '$asunto', '$cuerpo', 'COM', 'ADM', $fecha_actual)";
        $ejecutar = mysql_query($queryMensaje,$dspp) or die(mysql_error());


        /************************  FIN MAIL COM  *********************************************/


}
/*******************************************************************************************/
/*****************************  FIN RESOLUCION DE OBJECIÓN  ********************************/
/*******************************************************************************************/

 if(isset($_POST['filtroPalabra']) && $_POST['filtroPalabra'] == "1" && $_POST['palabraClave']){
  $palabraClave = $_POST['palabraClave'];

        $query_buscar = "SELECT solicitud_registro.*, com.idcom,com.nombre, com.pais, com.estado, com.abreviacion AS 'abreviacionCOM', oc.idoc, oc.nombre, oc.abreviacion FROM solicitud_registro INNER JOIN com ON solicitud_registro.idcom = com.idcom INNER JOIN oc ON solicitud_registro.idoc = oc.idoc WHERE (com.nombre LIKE '%$palabraClave%') OR (solicitud_registro.p1_correo LIKE '%$palabraClave%') OR (com.pais LIKE '%$palabraClave%') OR (oc.nombre LIKE '%$palabraClave%') OR (oc.abreviacion LIKE '%$palabraClave%') AND (solicitud_registro.status_interno = 10) ORDER BY solicitud_registro.fecha_elaboracion DESC";

        $queryTotal = "SELECT COUNT(idsolicitud_registro) AS 'totalConsulta' FROM solicitud_registro INNER JOIN com ON solicitud_registro.idcom = com.idcom INNER JOIN oc ON solicitud_registro.idoc = oc.idoc WHERE (com.nombre LIKE '%$palabraClave%') OR (solicitud_registro.p1_correo LIKE '%$palabraClave%') OR (com.pais LIKE '%$palabraClave%') OR (oc.nombre LIKE '%$palabraClave%') OR (oc.abreviacion LIKE '%$palabraClave%') AND (solicitud_registro.status_interno = 10) ORDER BY solicitud_registro.fecha_elaboracion DESC";

}else if(!isset($_POST['filtroPalabra']) || empty($_POST['palabraClave'])){
        $query_buscar = "SELECT solicitud_registro.*, com.idcom,com.nombre, com.pais, com.estado,com.abreviacion AS 'abreviacionCOM', oc.idoc, oc.nombre, oc.abreviacion FROM solicitud_registro INNER JOIN com ON solicitud_registro.idcom = com.idcom INNER JOIN oc ON solicitud_registro.idoc  = oc.idoc WHERE solicitud_registro.status_interno = 10 ORDER BY solicitud_registro.fecha_elaboracion DESC";

        $queryTotal = "SELECT COUNT(idsolicitud_registro) AS 'totalSolicitudes' FROM solicitud_registro WHERE solicitud_registro.status_interno = 10";
}
/*}else if(isset($_POST['filtroPais']) && $_POST['filtroPais'] == "2" && $_POST['busquedaPais'] != NULL){
  $pais = $_POST['busquedaPais'];
  $query_buscar = "SELECT * FROM com WHERE pais LIKE '%$pais%'";
}else{
  $query_buscar = "SELECT * FROM com ORDER BY nombre ASC";
}*/

      $ejecutarTotal = mysql_query($queryTotal,$dspp) or die(mysql_error());
      $totalSolicitudes = mysql_fetch_assoc($ejecutarTotal);


       $ejecutar_busqueda = mysql_query($query_buscar, $dspp) or die(mysql_error());




if (isset($_GET['totalRows_com'])) {
  $totalRows_com = $_GET['totalRows_com'];
} else {
  $all_com = mysql_query($query_opp);
  $totalRows_com = mysql_num_rows($all_com);
}
$totalPages_com = ceil($totalRows_com/$maxRows_com)-1;

$queryString_com = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_com") == false && 
        stristr($param, "totalRows_com") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_com = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_com = sprintf("&totalRows_com=%d%s", $totalRows_com, $queryString_com);


///////////////////////////////////// VARIABLES DE CONTROL ////////////////////////////////////////

  $validacionStatus = $registro_busqueda['status_interno'] != 1 && $registro_busqueda['status_interno'] != 2 && $registro_busqueda['status_interno'] != 3 && $registro_busqueda['status_interno'] != 14 && $registro_busqueda['status_interno'] != 15;

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
      <h4 class="well">
        <?php 
          if(isset($totalSolicitudes['totalConsulta'])){
        ?>
          Solicitudes Encontradas: <span style="color:red"><?php echo $totalSolicitudes['totalConsulta']; ?></span>
        <?php
          }else{
        ?>
          Total Solicitudes: <span style="color:red"><?php echo $totalSolicitudes['totalSolicitudes']; ?></span>
        <?php
          }
         ?>
        
      </h4>
    </div>
  </div>

<hr>


  <table class="table table-condensed table-bordered table-hover" style="font-size:12px">
    <thead>
      <tr>
         <a class="btn btn-sm btn-danger" href="?SOLICITUD&cancelCOM">Solicitudes Canceladas</a>
         <a class="btn btn-sm btn-success" href="?SOLICITUD&finalizadoCOM">Proceso Finalizado</a>
      </tr>
      <tr>
        <th class="text-center">ID</th>
        <th class="text-center">Solicitud</th>
       <!-- <th class="text-center">Nombre</th>-->
        <!--<th class="text-ceter">Certificadora</th>-->
        <th class="text-center">Cotización</th>
        <!--<th class="text-center">Sitio WEB</th>-->
        <th class="text-center">Empresa</th>
        <!--<th class="text-center">País</th>-->
        <th class="text-center" colspan="2">Información Estatus</th>
        <!--<th class="text-center">Status <br>Interno</th>-->
        <!--<th class="text-center">Propuesta</th>-->
        <th class="text-center">Información de Objeción</th>
        <th class="text-center">Certificado / Membresia</th>
        <th class="text-center">Observaciones</th>
        <!--<th>OC</th>
        <th>Razón social</th>
        <th>Dirección fiscal</th>
        <th>RFC</th>-->
        <!--<th>Eliminar</th>-->
      </tr>
    </thead>
    <tbody>
        <?php mysql_select_db($database_dspp, $dspp); ?>


        <?php $cont=0; while($registro_busqueda = mysql_fetch_assoc($ejecutar_busqueda)){ $cont++;?>
          <tr <?php if($registro_busqueda['estado'] == 20){ echo "style='border-style:solid;border-color:#E74C3C'";} ?>>
        <?php  $fecha = $registro_busqueda['fecha_elaboracion']; ?> 
            <td><?php echo $registro_busqueda['idsolicitud_registro']; ?></td>

  <!---------------------------------------- INICIA BOTON VER SOLICITUD -------------------------------------->
          <td>
            <a class="btn btn-primary btn-sm" style="width:100%" href="?COM&amp;detailCOM&amp;query=<?php echo $registro_busqueda['idoc']; ?>&amp;formato=<?php echo $registro_busqueda['idsolicitud_registro']; ?>">
              <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> <?php echo  date("d/m/Y", $fecha); ?><br>Ver solicitud
            </a>
            <p>
            <?php 
            /******** ABREVIACION OC ******************/
              if(isset($registro_busqueda['idoc'])){
                $query = "SELECT idoc,nombre,abreviacion FROM oc WHERE idoc = $registro_busqueda[idoc]";
                $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
                $datosOC = mysql_fetch_assoc($ejecutar);
                echo "<a href='?OC&detail&idoc=$datosOC[idoc]'>".$datosOC['abreviacion']."</a>";
              }else{
                echo "No Disponible";
              } 
            ?>
            </p>              
          </td>
  <!---------------------------------------- TERMINA BOTON VER SOLICITUD -------------------------------------->

  <!----------------------------------------- INICIA SECCION COTIZACION ---------------------------------------------->
          <td>
            <a href="<?echo $registro_busqueda['cotizacion_com']?>" target="_blank" type="button" class="botonCotizacion btn <?php if(empty($registro_busqueda['cotizacion_com'])){ echo 'btn-default btn-sm';}else{echo 'btn-success btn-sm';} ?>" aria-label="Left Align" <?php if(empty($registro_busqueda['cotizacion_com'])){echo "disabled";}?>>
              <span class="glyphicon glyphicon-download-alt"></span> Empresa
            </a> 

            <!--<a href="http://d-spp.org/oc/<?echo $registro_busqueda['cotizacion_adm']?>" target="_blank" type="button" class="botonCotizacion btn <?php if(empty($registro_busqueda['cotizacion_adm'])){ echo 'btn-danger btn-sm';}else{echo 'btn-success btn-sm';} ?>" aria-label="Left Align" <?php if(empty($registro_busqueda['cotizacion_adm'])){echo "disabled";}?>>
              <span class="glyphicon glyphicon-download-alt"></span> FUNDEPPO
            </a> -->
 
            <?php 
              if($registro_busqueda['status_interno'] != 1 && $registro_busqueda['status_interno'] != 2 && $registro_busqueda['status_interno'] != 3 && $registro_busqueda['status_interno'] != 14 && $registro_busqueda['status_interno'] != 15 && $registro_busqueda['status_interno'] != 17 && $registro_busqueda['status_interno'] != 20){
            ?>
              <p class="alert alert-success text-center informacion" style="padding:7px;"><span class="estatus"></span> Aceptada</p>
              <?php 
                }else if($registro_busqueda['status_interno'] == 24){
              ?>
              <p class="alert alert-danger text-center informacion" style="padding:7px;"><span class="estatus"></span> Rechazada</p>
              <?php
                }else{
              ?>
                <p class="alert alert-warning text-center informacion" style="padding:7px;"><span class="estatus"></span> Pendiente</p>
            <?php 
              }
            ?>  
          </td>
  <!----------------------------------------- TERMINA SECCION COTIZACION ---------------------------------------------->

  <!---------------------------------------- INICIA SECCION EMPRESA ---------------------------------------------------->
            <td>
              <p class="alert alert-success informacion" >
              <?php 
              /******** NOMBRE COMPLETO COM ******************/
                if(isset($registro_busqueda['nombre'])){
                  echo $registro_busqueda['nombre']." , <u><a href='?COM&detail&idcom=$registro_busqueda[idcom]'>".$registro_busqueda['abreviacionCOM']."</a></u>";
                }else{
                  echo "No Disponible";
                } 
              ?>
              </p>
            </td>
  <!---------------------------------------- INICIA SECCION EMPRESA ---------------------------------------------------->

  <!------------------------------------ INICIA SECCION STATUS PUBLICO ------------------------------------>
            <?php 
              $query_status = "SELECT * FROM status_publico WHERE idstatus_publico = $registro_busqueda[status_publico]";
              $ejecutar = mysql_query($query_status,$dspp) or die(mysql_error());
              $estatus_publico = mysql_fetch_assoc($ejecutar);
             ?>
            <td>
              <?php 
              /******** INFORMACION DEL STATUS PUBLICO ******************/
                if($registro_busqueda['status_interno'] == 10){
                  echo "<p class='text-center alert alert-success informacion' style='padding:7px;'><span class='estatus'>Estatus Publico:</span>  <b><u>Certificado</u></b></p>";
                }else{
                   echo "<p class='text-center alert alert-warning informacion' style='padding:7px;'><span class='estatus'>Estatus Publico:</span> <a href='#' data-toggle='tooltip' title='".$estatus_publico['descripcion_publica']."'>".$estatus_publico['nombre']."</a></p>"; 
                }
              ?>
            </td>

            <?php 
              /******** INFORMACION DEL STATUS INTERNO ******************/
              $query_status = "SELECT * FROM status WHERE idstatus = $registro_busqueda[status_interno]";
              $ejecutar = mysql_query($query_status,$dspp) or die(mysql_error());
              $estatus_interno = mysql_fetch_assoc($ejecutar);

              if($registro_busqueda['status_interno'] == 4 || $registro_busqueda['status_interno'] == 11 || $registro_busqueda['status_interno'] == 13 || $registro_busqueda['status_interno'] == 14 || $registro_busqueda['status_interno'] == 15){
                $colorEstado = "class='informacion text-center alert alert-danger'";
              }else if($registro_busqueda['status_interno'] == 10){
                $colorEstado = "class='informacion text-center alert alert-success'";
              }else{
                $colorEstado = "class='informacion text-center alert alert-warning'";
              }
             ?>

            <td>
              <p <?echo $colorEstado;?> style="padding:7px;">
                <?php echo "<span class='estatus'>Estatus Interno: </span> <a href='#' data-toggle='tooltip' title='".$estatus_interno['descripcion_interna']."'>".$estatus_interno['nombre']."</a>"; ?>
              </p>

              <button type="button" class="btn btn-success" style="height:25px;font-size:12px;width:100%;" data-toggle="modal" <?php echo "data-target='#detalle".$registro_busqueda['idsolicitud_registro']."'" ?>>
                Historial Estatus
              </button>

              <?php 
                $queryProcesoCertificacion = "SELECT proceso_certificacion.*, status.idstatus, status.nombre AS 'nombreEstatus' FROM proceso_certificacion LEFT JOIN status ON proceso_certificacion.idstatus = status.idstatus WHERE idsolicitud_registro = $registro_busqueda[idsolicitud_registro]";
                $row_proceso = mysql_query($queryProcesoCertificacion,$dspp) or die(mysql_error());

               ?>
              <!-- Modal -->
              <div class="modal fade" <?php echo "id='detalle".$registro_busqueda['idsolicitud_registro']."'" ?> id="detalle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog modal-lg" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      <h4 class="modal-title" id="myModalLabel">Detalle Estatus Interno</h4>
                    </div>
                    <div class="modal-body">
                      <table class="table">
                        <thead>
                          <tr>
                            <th>FECHA</th>
                            <th>ESTATUS</th>
                            <th>DESCRIPCIÓN</th>
                            <th>ARCHIVO</th>
                            <th>NOMBRE</th>
                          </tr>
                        </thead>

                        <?php 
                        while($proceso_certificacion = mysql_fetch_assoc($row_proceso)){
                        ?>
                          <tbody>
                            <tr>
                              <td><?php echo date("d/m/Y", $proceso_certificacion['fecha']); ?></td>
                              <td><?php echo $proceso_certificacion['nombreEstatus']; ?></td>
                              <td><?php if(!empty($proceso_certificacion['registro'])){ echo $proceso_certificacion['registro']; }else{ echo "No Disponible"; } ?></td>
                              <td><?php if(!empty($proceso_certificacion['archivo'])){ echo "<a href='$proceso_certificacion[archivo]' target='_blank'>Visualizar</a>";}else{ echo "No Disponible"; } ?></td>
                              <td><?php echo $proceso_certificacion['nombre'];?></td>
                            </tr>
                          </tbody>
                        <?php
                        }
                         ?>
                      </table>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                      
                    </div>
                  </div>
                </div>
              </div>
            </td>
  <!------------------------------------ TERMINA SECCION STATUS PUBLICO ------------------------------------>

                <!-- consulta sobre los datos de objecion -->
                <?php 
                 // $queryObjecion = "SELECT * FROM objecion WHERE idcom = $registro_busqueda[idcom] AND idoc = $registro_busqueda[idoc]";
                  $queryObjecion = "SELECT * FROM objecion WHERE idsolicitud_registro = $registro_busqueda[idsolicitud_registro]";
                  $resultado = mysql_query($queryObjecion,$dspp) or die(mysql_error());

                  $resultado2 = mysql_fetch_assoc($resultado);

                 ?>
                <!-- consulta sobre los datos de objecion -->

              <!--INICIA PERIODO OBJECIÓN-->
            <?php if($registro_busqueda['status_interno'] != 1 && $registro_busqueda['status_interno'] != 2 && $registro_busqueda['status_interno'] != 3 && $registro_busqueda['status_interno'] != 14 && $registro_busqueda['status_interno'] != 15 && $registro_busqueda['status_interno'] != 17 ){ ?>

  <!------------------------------------ INICIA SECCION INFORMACION DE OBJECION ------------------------------------------------>
              <?php //if($registro_busqueda['estado'] == 20){ /***** SI LA SOLICITUD ESTA EN PROCESOS DE RENOVACION*********/?>
          
                
              <?php //}else{ /****** INICIO ELSE SI LA SOLICITUD NO ESTA EN PROCESO DE RENOVACION*******/?>

            <td class="text-center">
              <?php 

              $consultaFecha = "SELECT idfecha FROM fecha WHERE idexterno = '$registro_busqueda[idcom]' AND identificador = 'COM' AND status = 20";
              $ejecutar = mysql_query($consultaFecha,$dspp) or die(mysql_error());
              $total = mysql_num_rows($ejecutar);

              if(empty($total)){ 
                ?>
                <button <?if(isset($resultado2['idobjecion'])){echo "class='botonObjecion btn btn-success btn-sm'";}else{echo "class='btn btn-danger btn-sm'";}?> data-toggle="modal" <?php echo "data-target='#myModal".$registro_busqueda['idsolicitud_registro']."'"?> >
                  <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Periodo Objeción
                </button>
              <?php }else{ ?>
                <p class="informacion alert alert-success text-center" style='padding:7px;'>PROCESO DE RENOVACIÓN</p>
              <?php } ?>

              <?php 
                $queryCertificado = "SELECT * FROM certificado WHERE idsolicitud_registro = $registro_busqueda[idsolicitud_registro] AND idcom = $registro_busqueda[idcom]";
                $ejecutarCertificado = mysql_query($queryCertificado,$dspp) or die(mysql_error());
                $certificado = mysql_fetch_assoc($ejecutarCertificado);
                $num = mysql_num_rows($ejecutarCertificado);

                if($num != 0 && isset($certificado['statuspago'])){

                  //$queryMembresia = "SELECT membresia.*, fecha.*, MAX(fecha.fecha) AS 'ultimafecha' FROM membresia INNER JOIN fecha ON membresia.idmembresia = fecha.idexterno WHERE membresia.idcom = $registro_busqueda[idcom] AND fecha.identificador = 'membresia'";

                  $queryMembresia = "SELECT membresia.*, fecha.*, MAX(fecha.fecha) AS 'ultimafecha' FROM membresia INNER JOIN fecha ON membresia.idmembresia = fecha.idmembresia WHERE membresia.idcom = $registro_busqueda[idcom] AND fecha.identificador = 'MEMBRESIA'";
                  $ejecutar = mysql_query($queryMembresia, $dspp) or die(mysql_error());

               
                    $membresia = mysql_fetch_assoc($ejecutar);

                    if(isset($membresia['idmembresia'])){
                      $queryStatus = "SELECT * FROM fecha WHERE fecha = $membresia[ultimafecha]";
                      $eje = mysql_query($queryStatus,$dspp) or die(mysql_error());
                      $registroStatus = mysql_fetch_assoc($eje);
                    }
                
                }                           
               ?>

                <?php 

                  $consultaFecha = "SELECT idfecha FROM fecha WHERE idexterno = '$registro_busqueda[idcom]' AND identificador = 'COM' AND status = 20";
                  $ejecutar = mysql_query($consultaFecha,$dspp) or die(mysql_error());
                  $total = mysql_num_rows($ejecutar);


                  if(empty($total)){
                    if(isset($resultado2['status']) && $resultado2['status'] == "6" || $resultado2['status'] == "7"){ 
                ?>
                    <button <?if(!empty($resultado2['adjunto'])){echo "class='botonObjecion btn btn-success btn-sm'";}else{echo "class=' botonObjecion btn btn-danger btn-sm'";}?> data-toggle="modal" <?php echo "data-target='#resolucion".$registro_busqueda['idsolicitud_registro']."'"?>>
                      <span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Resolución <br>de Objeción
                    </button>
                  <?php }else{ ?>
                    <button class="botonObjecion btn btn-danger btn-sm" data-toggle="modal" <?php echo "data-target='#resolucion".$registro_busqueda['idsolicitud_registro']."'"?> disabled>
                      <span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Resolución <br>de Objeción
                    </button>
                  <?php 
                    }
                  }
                  ?>

                  <!-- Modal -->
                  <form action="" method="post" id="periodoObjecion" enctype="application/x-www-form-urlencoded">
                    <div class="modal fade" <?php echo "id='myModal".$registro_busqueda['idsolicitud_registro']."'" ?> tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
                            <input type="hidden" name="emailCOM1" value="<?php echo $registro_busqueda['p1_correo'];?>">
                            <input type="hidden" name="emailCOM2" value="<?php echo $registro_busqueda['p2_correo'];?>"> 
                            <input type="hidden" name="objecionIdcom_hdn" value="<?php echo $registro_busqueda['idcom'];?>">
                            <input type="hidden" name="objecionIdOc_hdn" value="<?php echo $registro_busqueda['idoc'];?>">
                            <input type="hidden" name="statusObjecion_hdn" value="6">
                            <input type="hidden" name="insertarObjecion" value="periodoObjecion">
                            <input type="hidden" name="idsolicitud_registro" value="<?echo $registro_busqueda['idsolicitud_registro'];?>">

                          </div>
                        </div>
                      </div>
                    </div>
                  </form>
                  <!-- Modal -->
                </td>

              <?php// } /******FIN ELSE SOLICITUD NO ESTA EN PROCESO DE RENOVACION*******/?>


    
            <!------------------------------- INICIA SECCION RESOLUCIÓN DE OBJECION ------------------------------>
              

        
                <?php 
                  if(isset($resultado2['idobjecion'])){
                 ?>    
                <!-- Modal -->
                <form action="" method="POST" enctype="multipart/form-data">
                  
                  <div class="modal fade" <?php echo "id='resolucion".$registro_busqueda['idsolicitud_registro']."'" ?> tabindex="-1" role="dialog" aria-labelledby="resolucionLabel">
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
                                  <a class="col-xs-12 btn btn-info" style="margin-top:-10px;" role="button" name="descarga" href="<?echo $resultado2['adjunto']?>" target="_blank"><span aria-hidden="true" class="glyphicon glyphicon-download-alt"></span> Descargar</a>
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
                            <input type="hidden" name="emailCOM1" value="<?php echo $registro_busqueda['p1_correo'];?>">
                            <input type="hidden" name="emailCOM2" value="<?php echo $registro_busqueda['p2_correo'];?>"> 
                            <input type="hidden" name="objecionIdcom_hdn" value="<?php echo $registro_busqueda['idcom'];?>">
                            <input type="hidden" name="objecionIdOc_hdn" value="<?php echo $registro_busqueda['idoc'];?>">
                            <input type="hidden" name="statusObjecion_hdn" value="7">
                            <input type="hidden" name="statusInterno" value="19">
                            <input type="hidden" name="idobjecion" value="<? echo $resultado2['idobjecion'];?>">
                            <input type="hidden" name="idsolicitud_registro" value="<?echo $registro_busqueda['idsolicitud_registro'];?>">                       
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

  <!----------------------------------- TERMINA SECCION INFORMACION DE OBJECION ------------------------------------------------>
              
  <!--------------------------------- INICIA SECCION ESTATUS DE CERTIFICACION -------------------------------------------------->
              <td>
                <button class="btn btn-warning btn-sm" data-toggle="modal" <?php echo "data-target='#certificado".$registro_busqueda['idsolicitud_registro']."'"?>>
                  <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Consultar<br>Estatus
                </button>    
              </td>

              <?php 
                $query = "SELECT * FROM fecha";
                $ejecutar1 = mysql_query($query,$dspp) or die(mysql_error());
                $numero = mysql_num_rows($ejecutar1);

                if($numero != 0 && isset($certificado['idcertificado'])){
                  $queryFecha = "SELECT fecha.*, MAX(fecha) AS 'ultimaFecha' FROM fecha WHERE idcom = $certificado[idcom] AND identificador = 'certificado'";
                  $ejecutar = mysql_query($queryFecha,$dspp) or die(mysql_error());
                  $registroFecha = mysql_fetch_assoc($ejecutar);
                }
               ?>
                <!-- Modal -->
                <form action="" method="POST" enctype="multipart/form-data">
                  
                  <div class="modal fade" <?php echo "id='certificado".$registro_busqueda['idsolicitud_registro']."'" ?> tabindex="-1" role="dialog" aria-labelledby="resolucionLabel">
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
                                  <?php if($registro_busqueda['status_interno'] == 19 ){ ?>
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
                                        <a class="btn btn-success" href="<?echo $certificado['adjunto'];?>" target="_blank">Descargar Certificado</a>
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
                                      <p><strong>No se ha realizado el pago correspondiente, intente revisar más tarde.</strong></p>
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
                                        <a href="<?echo $membresia['adjunto']?>" class="btn btn-danger btn-sm" target="_blank">Descargar <br>Comprobante</a>
                                        
                                      </div>
                                      <?php if($registroStatus['status'] != "APROBADO"){ ?>
                                        <div class="col-xs-12">
                       
                                            <button class="btn btn-success btn-sm" type="submit" name="aprobar" value="aprobado">Aprobar</button>
                                            <button class="btn btn-danger btn-sm" typw="submit" name="denegar" value="denegado">Denegar</button>
                                            <input type="hidden" name="idmembresia" value="<?echo $membresia['idmembresia'];?>">
                                            <input type="hidden" name="idcom" value="<?php echo $registro_busqueda['idcom'];?>">
                                            <input type="hidden" name="idoc" value="<?php echo $registro_busqueda['idoc'];?>">
                                            <input type="hidden" name="idcertificado" value="<?php echo $certificado['idcertificado'];?>">
                                            <input type="hidden" name="emailCOM1" value="<?php echo $registro_busqueda['p1_correo'];?>">
                                            <input type="hidden" name="emailCOM2" value="<?php echo $registro_busqueda['p2_correo'];?>">
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
                        
                            <input type="hidden" name="objecionIdcom_hdn" value="<?php echo $registro_busqueda['idcom'];?>">
                            <input type="hidden" name="objecionIdOc_hdn" value="<?php echo $registro_busqueda['idoc'];?>">
                            <input type="hidden" name="statusObjecion_hdn" value="Inicia Periodo de Objeción">
                            
                            <input type="hidden" name="idobjecion" value="<? echo $resultado2['idobjecion'];?>">
                            <input type="hidden" name="idsolicitud_registro" value="<?echo $registro_busqueda['idsolicitud_registro'];?>">                          


                        </div>
                      </div>
                    </div>
                  </div>

                </form>
                <!-- Modal -->

            <?php }else{ ?>
                <?php if($registro_busqueda['estado'] == 20){ ?>
                  <td class="text-center">
                    <p class="informacion alert alert-success text-center" style="padding:7px;">PROCESO DE RENOVACIÓN</p>
                  </td>
                <?php }else{ ?>
                  <td class="text-center">
                      <!--<?php echo $registro_busqueda["idsolicitud_registro"]; ?>-->
                    <button class="botonObjecion btn btn-sm btn-default" disabled>Periodo Objeción</button>

                    <button class="botonObjecion btn btn-default btn-sm" data-toggle="modal" <?php echo "data-target='#resolucion".$registro_busqueda['idsolicitud_registro']."'"?> disabled>
                      <span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Resolución <br>de Objeción
                    </button>
                  </td>
                <?php } ?>
<!-------------------------------------- TERMINA SECCION ESTATUS DE CERTIFICACION -------------------------------------------------->

             <!-- <td>
                  <h6>
                    <button class="btn btn-default btn-sm" data-toggle="modal" <?php echo "data-target='#resolucion".$registro_busqueda['idsolicitud_registro']."'"?> disabled>
                      <span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Resolución <br>de Objeción
                    </button>
                  </h6>
              </td>-->
              <!--FIN PERIODO OBJECIÓN--> 
               
              <!--INICIA CERTIFICACION-->
               <td>     
                <button class="btn btn-default" disabled>
                  <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Consultar<br>Estatus
                </button>
              </td>

              <!--FIN CERTIFICACION-->          
            <?php } ?>
            <td>
              <?php if(empty($registro_busqueda['observaciones'])){ ?>
                <button class="btn btn-default btn-sm" disabled>
                  <span class="glyphicon glyphicon-list-alt"></span> Consultar
                </button>         
              <?php }else{ ?>
                <a class="btn btn-info btn-sm" href="?COM&amp;detailCOM&amp;query=<?php echo $registro_busqueda['idoc']; ?>&amp;formato=<?php echo $registro_busqueda['idsolicitud_registro']; ?>">
                  <span class="glyphicon glyphicon-list-alt"></span> Consultar
                </a>
              <?php } ?>
            </td>
          <!--<td>-->
          </tr>

        <?php } ?>

        <? if($cont==0){?>
        <tr><td colspan="12" class="alert alert-info" role="alert">No se encontraron registros</td></tr>

        <? }?>
    </tbody>
  </table>




<table>
  <tr>
  <td width="20"><?php if ($pageNum_com > 0) { // Show if not first page ?>
  <a href="<?php printf("%s?pageNum_com=%d%s", $currentPage, 0, $queryString_com); ?>">
  <span class="glyphicon glyphicon-fast-backward" aria-hidden="true"></span>
  </a>
  <?php } // Show if not first page ?></td>
  <td width="20"><?php if ($pageNum_com > 0) { // Show if not first page ?>
  <a href="<?php printf("%s?pageNum_com=%d%s", $currentPage, max(0, $pageNum_com - 1), $queryString_com); ?>">
  <span class="glyphicon glyphicon-backward" aria-hidden="true"></span>
  </a>
  <?php } // Show if not first page ?></td>
  <td width="20"><?php if ($pageNum_com < $totalPages_com) { // Show if not last page ?>
  <a href="<?php printf("%s?pageNum_com=%d%s", $currentPage, min($totalPages_com, $pageNum_com + 1), $queryString_com); ?>">
  <span class="glyphicon glyphicon-forward" aria-hidden="true"></span>
  </a>
  <?php } // Show if not last page ?></td>
  <td width="20"><?php if ($pageNum_com < $totalPages_com) { // Show if not last page ?>
  <a href="<?php printf("%s?pageNum_com=%d%s", $currentPage, $totalPages_com, $queryString_com); ?>">
  <span class="glyphicon glyphicon-fast-forward" aria-hidden="true"></span>
  </a>
  <?php } // Show if not last page ?></td>
  </tr>
</table>
<?php
mysql_free_result($com);
?>