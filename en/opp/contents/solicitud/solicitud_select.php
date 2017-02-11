<?php 
require_once('../Connections/dspp.php'); 
require_once('../Connections/mail.php');


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

    $remitente = "cert@spp.coop";
    $correoCert = "cert@spp.coop";
    $fecha_actual = time();

if(isset($_POST['opp_propuesta'])){

          $idopp = $_SESSION['idopp'];
          $idoc = $_POST['idoc'];
          $identificador = "SOLICITUD";
          $status = $_POST['propuesta_txt'];
          $idexterno = $_POST['idsolicitud'];
          $totalFecha = $_POST['totalFecha'];


        if($_POST['estadoOPP'] == 20){
          if($_POST['propuesta_txt'] == 18){
            $status_publico = 8;
          }else{
            $status_publico = 10;
          }
          

          $updateSQL = "UPDATE solicitud_certificacion SET 
          status= '".$_POST['propuesta_txt']."',
          status_publico = '".$status_publico."'
          WHERE idsolicitud_certificacion = '".$_POST['idsolicitud']."'";
          $Result = mysql_query($updateSQL, $dspp) or die(mysql_error());


          $query = "INSERT INTO fecha(fecha, idexterno, idopp, idoc, identificador, status, status_publico) VALUES($fecha_actual, $idexterno, $idopp, $idoc, '$identificador', $status, $status_publico)";
          $ejecutar = mysql_query($query,$dspp) or die(mysql_error());

        }else{
          $updateSQL = "UPDATE solicitud_certificacion SET 
          status= '".$_POST['propuesta_txt']."'
          WHERE idsolicitud_certificacion = '".$_POST['idsolicitud']."'";
          $Result = mysql_query($updateSQL, $dspp) or die(mysql_error());

          //$query = "INSERT INTO fecha(fecha, idexterno, identificador, status) VALUES($fecha_actual, $idopp, '$identificador', $status)";

          $query = "INSERT INTO fecha(fecha, idexterno, idopp, idoc, identificador, status) VALUES($fecha_actual, $idexterno, $idopp, $idoc, '$identificador', $status)";
          $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
        }

          //$actualizar = "UPDATE solicitud_certificacion SET status = ".$_POST['']."";

        $emailOPP1 = $_POST['emailOPP1'];
        $emailOPP2 = $_POST['emailOPP2'];
        $emailOC = $_POST['emailOC'];
        $nombreOC = $_POST['nombreOC'];
        $nombreOPP = $_POST['nombreOPP'];
        $emailFundeppo = "soporteinforganic@gmail.com";
        $telefonoOPP = $_POST['telefonoOPP'];
        $abreviacionOPP = $_POST['abreviacionOPP'];
        $paisOPP =$_POST['paisOPP'];
        $ciudad = $_POST['ciudadOPP'];
        $fecha = date("d/m/Y", time());
        $nombreOPP1 = $_POST['nombreOPP1'];
        $nombreOPP2 = $_POST['nombreOPP2'];
        $paisEstado = $paisOPP.' / '.$ciudad;
        $fecha_elaboracion = $_POST['fecha_elaboracion'];
      /*****************************INICIO MAIL OC***************************************************/
      /********************************************************************************/

        //$correo = $_POST['p1_email'];
        //$correo = $_POST['p2_email'];

        $destinatario = $emailOC;
        
        $asunto = "D-SPP - Certificación para Organizaciones de Pequeños Productores";


        if($_POST['propuesta_txt'] == 18){   /******************************** EL OPP HA ACEPTADO LA PROPUESTA ************************************************/

          if($totalFecha > 0){ /******************************** EL OPP SE ENCUENTRA EN PROCESO DE RENOVACIÓN ************************************************/
            $estatusInterno = 19; //E.I = CERTIFICACIÓN INICIADA
            $estatusPublico = 8; // E.P = PROCESO DE CERTIFICACIÓN


            $query = "UPDATE solicitud_certificacion SET status = '$estatusInterno', status_publico = '$estatusPublico' WHERE idsolicitud_certificacion = $_POST[idsolicitud]";
            $insertar = mysql_query($query,$dspp) or die(mysql_error());

            $mensajeEmail = '
              <html>
              <head>
                <meta charset="utf-8">
              </head>
              <body>
              
                <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
                  <tbody>
                    <tr>
                      <th rowspan="6" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                      <th scope="col" align="left" width="280"><strong>Notificación de Estado / Status Notification ('.$fecha.')</strong></th>
                    </tr>

                    <tr>
                      <td align="left" style="color:#ff738a;">Felicidades se ha aceptado su cotización, por favor ponerse en contacto con <b>'.$nombreOPP.'</b> para inciar el proceso de renovación del certificado.</td>
                    </tr>

                    <tr>
                      <td align="left">Teléfono / phone OPP: '.$telefonoOPP.'</td>
                    </tr>
                    <tr>
                      <td align="left">'.$paisEstado.'</td>
                    </tr>
                    <tr>
                      <td align="left" style="color:#ff738a;">Nombre: '.$nombreOPP1.' | '.$emailOPP1.'</td>
                    </tr>
                    <tr>
                      <td align="left" style="color:#ff738a;">Nombre: '.$nombreOPP2.' | '.$emailOPP2.'</td>
                    </tr>


                  </tbody>
                </table>

              </body>
              </html>
            ';

          /******************************** INICIO MAIL FUNDEPPO************************************************/
          /********************************************************************************/
            
            //$correo = $_POST['p1_email'];
            //$correo = $_POST['p2_email'];

            $destinatario2 = "cert@spp.coop";
            
            $asunto2 = "D-SPP - Renovación de Certificado para Organizaciones de Pequeños Productores"; 


            $mensajeEmail2 = '
              <html>
              <head>
                <meta charset="utf-8">
              </head>
              <body>
              
                <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
                  <tbody>
                    <tr>
                      <th rowspan="6" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                      <th scope="col" align="left" width="280"><strong>Notificación de Propuesta / Notification of Proposal ('.$fecha.')</strong></th>
                    </tr>

                    <tr>
                      <td align="left" style="color:#ff738a;">Se ha aceptado la cotizacion de <b>'.$nombreOC.'</b>.
                      <br><br>Se ha iniciado el proceso de renovacion del certificado.
                      </td>
                    </tr>

                    <tr>
                      <td align="left">Teléfono / phone OPP: '.$telefonoOPP.'</td>
                    </tr>
                    <tr>
                      <td align="left">'.$paisEstado.'</td>
                    </tr>
                    <tr>
                      <td align="left" style="color:#ff738a;">Nombre: '.$nombreOPP1.' | '.$emailOPP1.'</td>
                    </tr>
                    <tr>
                      <td align="left" style="color:#ff738a;">Nombre: '.$nombreOPP2.' | '.$emailOPP2.'</td>
                    </tr>


                    <tr>
                      <td colspan="2">
                        <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;" border="1" width="650px">
                          <tbody>
                            <tr style="font-size: 12px; text-align:center; background-color:#dff0d8; color:#3c763d;" height="50px;">
                              <td width="162.5px">Nombre de la organización/Organization name</td>
                              <td width="162.5px">Abreviación / Short name</td>
                              <td width="162.5px">País / Country</td>
                              <td width="162.5px">Organismo de Certificación / Certification Entity</td> 
                            </tr>
                            <tr style="font-size: 12px; text-align:justify">
                              <td style="padding:10px;">
                                '.$nombreOPP.'
                              </td>
                              <td style="padding:10px;">
                                '.$abreviacionOPP.'
                              </td>
                              <td style="padding:10px;">
                                '.$paisEstado.'
                              </td>
                              <td style="padding:10px;">
                                '.$nombreOC.'
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


          }/** FIN DEL PROCESO DE RENOVACIÓN  ***/
          else{  /******************************** EL OPP INICIA POR PRIMERA VEZ LA CERTIFICACIÓN ************************************************/
            $estatusAceptado = 18; //18 = PROCESO INICIADO
            $estatusDenegado = 24; //24 = COTIZACIÓN RECHAZADA


            $updateSQL = "UPDATE solicitud_certificacion SET status = $estatusDenegado WHERE idsolicitud_certificacion != $_POST[idsolicitud] AND fecha_elaboracion = $fecha_elaboracion";
            //CUANDO SE ACEPTA SE DENIEGA TODAS LA SOLICITUD QUE TENGAN DIFERENTE ID_SOLICITUD PERO TENGAN LA MISMA FECHA
            $ejecutar = mysql_query($updateSQL,$dspp) or die(mysql_error());

            $mensajeEmail = '
              <html>
              <head>
                <meta charset="utf-8">
              </head>
              <body>
              
                <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
                  <tbody>
                    <tr>
                      <th rowspan="6" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                      <th scope="col" align="left" width="280"><strong>Notificación de Propuesta / Notification of Proposal ('.$fecha.')</strong></th>
                    </tr>

                    <tr>
                      <td align="left" style="color:#ff738a;">Felicidades se ha aceptado su cotización, sera informado una vez que inicie el período de objeción, después podra ponerse en contacto con:</td>
                    </tr>

                    <tr>
                      <td align="left">Teléfono / phone OPP: '.$telefonoOPP.'</td>
                    </tr>
                    <tr>
                      <td align="left">'.$paisEstado.'</td>
                    </tr>
                    <tr>
                      <td align="left" style="color:#ff738a;">Nombre: '.$nombreOPP1.' | '.$emailOPP1.'</td>
                    </tr>
                    <tr>
                      <td align="left" style="color:#ff738a;">Nombre: '.$nombreOPP2.' | '.$emailOPP2.'</td>
                    </tr>


                    <tr>
                      <td colspan="2">
                        <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;" border="1" width="650px">
                          <tbody>
                            <tr style="font-size: 12px; text-align:center; background-color:#dff0d8; color:#3c763d;" height="50px;">
                              <td width="162.5px">Nombre de la organización/Organization name</td>
                              <td width="162.5px">Abreviación / Short name</td>
                              <td width="162.5px">País / Country</td>
                              <td width="162.5px">Organismo de Certificación / Certification Entity</td> 
                            </tr>
                            <tr style="font-size: 12px; text-align:justify">
                              <td style="padding:10px;">
                                '.$nombreOPP.'
                              </td>
                              <td style="padding:10px;">
                                '.$abreviacionOPP.'
                              </td>
                              <td style="padding:10px;">
                                '.$paisEstado.'
                              </td>
                              <td style="padding:10px;">
                                '.$nombreOC.'
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
          $mail->Body = utf8_decode($mensajeEmail);
          $mail->MsgHTML(utf8_decode($mensajeEmail));
          $mail->Send();
          $mail->ClearAddresses();



         // INICIA EL ENVIO DE MENSAJE DE DENEGACIÓN MASIVA DE COTIZACIONES

          $queryMensaje = "INSERT INTO mensajes(idopp, asunto, mensaje, destinatario, remitente, fecha) VALUES($idopp, '$asunto', '$mensajeEmail', 'OC', 'OPP', $fecha_actual)";
          $ejecutar = mysql_query($queryMensaje,$dspp) or die(mysql_error());


            $query = "SELECT solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp, solicitud_certificacion.idoc, solicitud_certificacion.status, solicitud_certificacion.fecha_elaboracion, solicitud_certificacion.cotizacion_opp, oc.idoc, oc.nombre, oc.email FROM solicitud_certificacion INNER JOIN oc ON solicitud_certificacion.idoc = oc.idoc WHERE idopp = $idopp AND status = $estatusDenegado AND fecha_elaboracion = $fecha_elaboracion AND cotizacion_opp != ''";
            $ejecutar = mysql_query($query,$dspp) or die(mysql_error());

            while($datosSolicitud = mysql_fetch_assoc($ejecutar)){
              $destinatario = $datosSolicitud['email'];

              $mensajeEmail = '
                <html>
                <head>
                  <meta charset="utf-8">
                </head>
                <body>
                
                  <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
                    <tbody>
                      <tr>
                        <th rowspan="6" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                        <th scope="col" align="left" width="280"><strong>Notificación de Propuesta / Notification of Proposal ('.$fecha.')</strong></th>
                      </tr>

                      <tr>
                        <td align="left" style="color:#ff738a;">Lo sentimos, su propuesta de cotización ha sido rechazada</td>
                      </tr>

                      <tr>
                        <td align="left">Teléfono / phone OPP: '.$telefonoOPP.'</td>
                      </tr>
                      <tr>
                        <td align="left">'.$paisEstado.'</td>
                      </tr>
                      <tr>
                        <td align="left" style="color:#ff738a;">Nombre: '.$nombreOPP1.' | '.$emailOPP1.'</td>
                      </tr>
                      <tr>
                        <td align="left" style="color:#ff738a;">Nombre: '.$nombreOPP2.' | '.$emailOPP2.'</td>
                      </tr>


                      <tr>
                        <td colspan="2">
                          <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;" border="1" width="650px">
                            <tbody>
                              <tr style="font-size: 12px; text-align:center; background-color:#dff0d8; color:#3c763d;" height="50px;">
                                <td width="162.5px">Nombre de la organización/Organization name</td>
                                <td width="162.5px">Abreviación / Short name</td>
                                <td width="162.5px">País / Country</td>
                                <td width="162.5px">Organismo de Certificación / Certification Entity</td> 
                              </tr>
                              <tr style="font-size: 12px; text-align:justify">
                                <td style="padding:10px;">
                                  '.$nombreOPP.'
                                </td>
                                <td style="padding:10px;">
                                  '.$abreviacionOPP.'
                                </td>
                                <td style="padding:10px;">
                                  '.$paisEstado.'
                                </td>
                                <td style="padding:10px;">
                                  '.$datosSolicitud['nombre'].'
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
              $mail->Body = utf8_decode($mensajeEmail);
              $mail->MsgHTML(utf8_decode($mensajeEmail));
              $mail->Send();
              $mail->ClearAddresses();

            }// FIN ENVIAR MENSAJE DENEGACIÓN DE COTIZACIÓN MASIVA




          /******************************** INICIO MAIL FUNDEPPO************************************************/
          /********************************************************************************/
            
            //$correo = $_POST['p1_email'];
            //$correo = $_POST['p2_email'];

            $destinatario2 = "cert@spp.coop";
            
            $asunto2 = "D-SPP - Cotización Certificación para Organizaciones de Pequeños Productores"; 


            $mensajeEmail2 = '
              <html>
              <head>
                <meta charset="utf-8">
              </head>
              <body>
              
                <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
                  <tbody>
                    <tr>
                      <th rowspan="6" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                      <th scope="col" align="left" width="280"><strong>Notificación de Propuesta / Notification of Proposal ('.$fecha.')</strong></th>
                    </tr>

                    <tr>
                      <td align="left" style="color:#ff738a;">Se ha aceptado la cotizacion de <b>'.$nombreOC.'</b>.
                      <br><br>Para poder iniciar el período de objeción por favor iniciar sesión en el siguiente enlace <a href="http://d-spp.org/?ADM">www.d-spp.org/?ADM</a>.
                      </td>
                    </tr>

                    <tr>
                      <td align="left">Teléfono / phone OPP: '.$telefonoOPP.'</td>
                    </tr>
                    <tr>
                      <td align="left">'.$paisEstado.'</td>
                    </tr>
                    <tr>
                      <td align="left" style="color:#ff738a;">Nombre: '.$nombreOPP1.' | '.$emailOPP1.'</td>
                    </tr>
                    <tr>
                      <td align="left" style="color:#ff738a;">Nombre: '.$nombreOPP2.' | '.$emailOPP2.'</td>
                    </tr>


                    <tr>
                      <td colspan="2">
                        <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;" border="1" width="650px">
                          <tbody>
                            <tr style="font-size: 12px; text-align:center; background-color:#dff0d8; color:#3c763d;" height="50px;">
                              <td width="162.5px">Nombre de la organización/Organization name</td>
                              <td width="162.5px">Abreviación / Short name</td>
                              <td width="162.5px">País / Country</td>
                              <td width="162.5px">Organismo de Certificación / Certification Entity</td> 
                            </tr>
                            <tr style="font-size: 12px; text-align:justify">
                              <td style="padding:10px;">
                                '.$nombreOPP.'
                              </td>
                              <td style="padding:10px;">
                                '.$abreviacionOPP.'
                              </td>
                              <td style="padding:10px;">
                                '.$paisEstado.'
                              </td>
                              <td style="padding:10px;">
                                '.$nombreOC.'
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



          }



          $mail->AddAddress($correoCert);

          //$mail->Username = "soporte@d-spp.org";
          //$mail->Password = "/aung5l6tZ";
          $mail->Subject = utf8_decode($asunto2);
          $mail->Body = utf8_decode($mensajeEmail2);
          $mail->MsgHTML(utf8_decode($mensajeEmail2));
          $mail->Send();
          $mail->ClearAddresses();



         // mail($destinatario2,$asunto2,utf8_decode($cuerpo2),$headers);

          $queryMensaje = "INSERT INTO mensajes(idopp, asunto, mensaje, destinatario, remitente, fecha) VALUES($idopp, '$asunto2', '$mensajeEmail2', 'ADM', 'OPP', $fecha_actual)";
          $ejecutar = mysql_query($queryMensaje,$dspp) or die(mysql_error());


        }else{ /******************************** EL OPP HA RECHAZADO LA PROPUESTA ************************************************/

          $estatusDenegado = 24;
          $updateSQL = "UPDATE solicitud_certificacion SET status = $estatusDenegado WHERE idsolicitud_certificacion != $_POST[idsolicitud] AND fecha_elaboracion = $fecha_elaboracion";
          $ejecutar = mysql_query($updateSQL,$dspp) or die(mysql_error());

          $mensajeEmail = '
            <html>
            <head>
              <meta charset="utf-8">
            </head>
            <body>
            
              <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
                <tbody>
                  <tr>
                    <th rowspan="6" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                    <th scope="col" align="left" width="280"><strong>Notificación de Propuesta / Notification of Proposal ('.$fecha.')</strong></th>
                  </tr>

                  <tr>
                    <td align="left" style="color:#ff738a;">Lo sentimos, su propuesta de cotización ha sido rechazada</td>
                  </tr>

                  <tr>
                    <td align="left">Teléfono / phone OPP: '.$telefonoOPP.'</td>
                  </tr>
                  <tr>
                    <td align="left">'.$paisEstado.'</td>
                  </tr>
                  <tr>
                    <td align="left" style="color:#ff738a;">Nombre: '.$nombreOPP1.' | '.$emailOPP1.'</td>
                  </tr>
                  <tr>
                    <td align="left" style="color:#ff738a;">Nombre: '.$nombreOPP2.' | '.$emailOPP2.'</td>
                  </tr>


                  <tr>
                    <td colspan="2">
                      <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;" border="1" width="650px">
                        <tbody>
                          <tr style="font-size: 12px; text-align:center; background-color:#dff0d8; color:#3c763d;" height="50px;">
                            <td width="162.5px">Nombre de la organización/Organization name</td>
                            <td width="162.5px">Abreviación / Short name</td>
                            <td width="162.5px">País / Country</td>
                            <td width="162.5px">Organismo de Certificación / Certification Entity</td> 
                          </tr>
                          <tr style="font-size: 12px; text-align:justify">
                            <td style="padding:10px;">
                              '.$nombreOPP.'
                            </td>
                            <td style="padding:10px;">
                              '.$abreviacionOPP.'
                            </td>
                            <td style="padding:10px;">
                              '.$paisEstado.'
                            </td>
                            <td style="padding:10px;">
                              '.$nombreOC.'
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
          $mail->Body = utf8_decode($mensajeEmail);
          $mail->MsgHTML(utf8_decode($mensajeEmail));
          $mail->Send();
          $mail->ClearAddresses();


          $queryMensaje = "INSERT INTO mensajes(idopp, idoc, asunto, mensaje, destinatario, remitente, fecha) VALUES($idopp, $idoc, '$asunto', '$mensajeEmail', 'OC', 'OPP', $fecha_actual)";
          $ejecutar = mysql_query($queryMensaje,$dspp) or die(mysql_error());


        }


}


$currentPage = $_SERVER["PHP_SELF"];

$maxRows_opp = 20;
$pageNum_opp = 0;
if (isset($_GET['pageNum_opp'])) {
  $pageNum_opp = $_GET['pageNum_opp'];
}
$startRow_opp = $pageNum_opp * $maxRows_opp;

mysql_select_db($database_dspp, $dspp);
if(isset($_GET['query'])){
  $query_opp = "SELECT opp.* ,solicitud_certificacion.*, oc.idoc AS 'idoc', oc.nombre AS 'nombreOC', oc.idoc, oc.email FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp INNER JOIN oc ON solicitud_certificacion.idoc = oc.idoc WHERE idsolicitud_certificacion = ".$_GET['query']." AND solicitud_certificacion.status != 24 ORDER BY solicitud_certificacion.fecha_elaboracion DESC";
  #$query_opp = "SELECT * FROM solicitud_certificacion where idsolicitud_certificacion ='".$_GET['query']."' ORDER BY fecha DESC";
}else{
  #SELECT solicitud_certificacion.* FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE opp.idopp = 15

  $query_opp = "SELECT opp.* ,solicitud_certificacion.*, oc.idoc AS 'idoc', oc.nombre AS 'nombreOC', oc.idoc, oc.email FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp INNER JOIN oc ON solicitud_certificacion.idoc = oc.idoc WHERE solicitud_certificacion.idopp = '".$_SESSION['idopp']."' AND solicitud_certificacion.status != 24 ORDER BY solicitud_certificacion.fecha_elaboracion DESC"; 

  #$query_opp = "SELECT opp.* ,solicitud_certificacion.* FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp ORDER BY solicitud_certificacion.fecha_elaboracion ASC";  

  #$query_opp = "SELECT * FROM solicitud_certificacion ORDER BY fecha ASC";
}

/****************************************************************************************************/
/***********************************   CARGAR COMPROBANTE  ******************************************/
/****************************************************************************************************/
if(isset($_POST['membresia']) && $_POST['membresia'] == "1"){

    $status = $_POST['statusInterno'];
    $fecha_actual = time();
    $fechaupload = $_POST['fechaupload'];
    $idopp = $_POST['membresiaidopp'];
    $idoc = $_POST['membresiaidoc'];
    $idsolicitud = $_POST['idsolicitud'];
    $idcertificado = $_POST['idcertificado'];
    $statuspago = "REVISION";
    $identificador = "MEMBRESIA";
    $idexterno = $idsolicitud;
    //$archivoPago = $_POST['archivoPago'];

    $ruta = "../../archivos/oppArchivos/membresia/comprobantes/";

    if($statuspago == "DENEGADO"){
      $query = "SELECT * FROM membresia WHERE idopp = $idopp";
      $row_membresia = mysql_query($query,$dspp) or die(mysql_error());
      $informacion_membresia = mysql_fetch_assoc($row_membresia);

      unlink($informacion_membresia['adjunto']);//borramos el comprobante de pago que fue denegado

      if(!empty($_FILES['comprobante']['name'])){
        $_FILES['comprobante']['name'];
            move_uploaded_file($_FILES["comprobante"]["tmp_name"], $ruta.time()."_".$_FILES["comprobante"]["name"]);
            $comprobantePago = $ruta.basename(time()."_".$_FILES["comprobante"]["name"]);


          /******************************** INICIO MAIL FUNDEPPO************************************************/
          /********************************************************************************/
            
            //$correo = $_POST['p1_email'];
            //$correo = $_POST['p2_email'];

            $destinatario2 = "cert@spp.coop";
            $fecha = date("d/m/Y", time());
            $asunto2 = "D-SPP - Comprobante de Pago - Membresia"; 


        $mensajeEmail2 = '
                  <html>
                  <head>
                    <meta charset="utf-8">
                  </head>
                  <body>
                  
                    <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
                      <tbody>
                        <tr>
                          <th rowspan="4" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                          <th scope="col" align="left" width="280"><strong>Notificación de Estado / Status Notification ('.$fecha.')</strong></th>
                        </tr>
                        <tr>
                        <td>Se ha cargado el comprobante de pago, por favor inicie sesión en la cuenta de administrador (<a href="http://d-spp.org/?ADM">www.d-spp.org/?ADM</a>) para poder revisarlo.</td>
                        </tr>
                        <!--<tr>
                          <td>
                            <!--<form name="ingresar" action="http://d-spp.org/formularioMail.php" method="POST" enctype="application/x-www-form-urlencoded">
                              <input type="text" name="formularioComprobante" value="1">
                              <a href="#" onclick="document.ingresar.submit()" type="Submit" style="background-color: #4CAF50;border: none;color: white;padding: 15px 32px;text-align: center;text-decoration: none;display: inline-block;font-size: 16px;" ><input type="text" name="aceptar" value="aceptar">Aceptar</a>
                              <a href="#" onclick="document.ingresar.submit()" type="Submit" style="background-color: red;border: none;color: white;padding: 15px 32px;text-align: center;text-decoration: none;display: inline-block;font-size: 16px;"><input type="text" name="denegar">Denegar</a>
                            </form>-->
                            <form action="http://d-spp.org/formularioMail.php" method="GET" enctype="application/x-www-form-urlencoded">
                              <input type="text" name="formularioComprobante" value="1">
                              <input type="text" name="aceptar" value="aceptar">
                              <input type="submit">
                            </form>
                          </td>
                        </tr>-->
                        
                      </tbody>
                    </table>

                  </body>
                  </html>
        ';


        $mail->AddAddress($correoCert);
        $mail->AddAttachment($comprobantePago);

        //$mail->Username = "soporte@d-spp.org";
        //$mail->Password = "/aung5l6tZ";
        $mail->Subject = utf8_decode($asunto2);
        $mail->Body = utf8_decode($mensajeEmail2);
        $mail->MsgHTML(utf8_decode($mensajeEmail2));
        $mail->Send();
        $mail->ClearAddresses();



            $queryMensaje = "INSERT INTO mensajes(idopp, asunto, mensaje, destinatario, remitente, fecha) VALUES($idopp, '$asunto2', '$mensajeEmail2', 'ADM', 'OPP', $fecha_actual)";
            $ejecutar = mysql_query($queryMensaje,$dspp) or die(mysql_error());

          /******************************* FIN MAIL FUNDEPPO *************************************************/
          /********************************************************************************/


      }else{
        $comprobantePago = NULL;
      }
      $adjunto = $comprobantePago;

      $query = "UPDATE membresia SET estado = '$statuspago', adjunto = '$adjunto', fechaupload = $fechaupload, idopp = $idopp WHERE idmembresia = $informacion_membresia[idmembresia]";
      $insertar = mysql_query($query,$dspp) or die(mysql_error());
      
      //echo "la consulta es: ".$query;

      //$idmembresia = mysql_insert_id($dspp);
      $identificador = "MEMBRESIA";


      $queryFecha = "INSERT INTO fecha (fecha, idexterno, idopp, idoc, idmembresia, identificador, status) VALUES ($fecha_actual, $idexterno, $idopp, $idoc, $informacion_membresia[idmembresia], '$identificador', '$statuspago')";
      $insertarFecha = mysql_query($queryFecha,$dspp) or die(mysql_error());
      //echo "<br>".$queryFecha;

      $update = "UPDATE certificado SET statuspago = '$statuspago' WHERE idcertificado = $idcertificado";
      $insertarupdate = mysql_query($update,$dspp) or die(mysql_error());



    }else{

      if(!empty($_FILES['comprobante']['name'])){
        $_FILES['comprobante']['name'];
            move_uploaded_file($_FILES["comprobante"]["tmp_name"], $ruta.time()."_".$_FILES["comprobante"]["name"]);
            $comprobantePago = $ruta.basename(time()."_".$_FILES["comprobante"]["name"]);


          /******************************** INICIO MAIL FUNDEPPO************************************************/
          /********************************************************************************/
            
            //$correo = $_POST['p1_email'];
            //$correo = $_POST['p2_email'];

            $destinatario2 = "cert@spp.coop";
            $fecha = date("d/m/Y", time());
            $asunto2 = "D-SPP - Comprobante de Pago - Membresia"; 


        $mensajeEmail2 = '
                  <html>
                  <head>
                    <meta charset="utf-8">
                  </head>
                  <body>
                  
                    <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
                      <tbody>
                        <tr>
                          <th rowspan="4" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                          <th scope="col" align="left" width="280"><strong>Notificación de Estado / Status Notification ('.$fecha.')</strong></th>
                        </tr>
                        <tr>
                        <tr>
                        <td>Se ha cargado el comprobante de pago, por favor inicie sesión en la cuenta de administrador (<a href="http://d-spp.org/?ADM">www.d-spp.org/?ADM</a>) para poder revisarlo.</td>
                        </tr>
                        <!--<tr>
                          <td>
                            <!--<form name="ingresar" action="http://d-spp.org/formularioMail.php" method="POST" enctype="application/x-www-form-urlencoded">
                              <input type="text" name="formularioComprobante" value="1">
                              <a href="#" onclick="document.ingresar.submit()" type="Submit" style="background-color: #4CAF50;border: none;color: white;padding: 15px 32px;text-align: center;text-decoration: none;display: inline-block;font-size: 16px;" ><input type="text" name="aceptar" value="aceptar">Aceptar</a>
                              <a href="#" onclick="document.ingresar.submit()" type="Submit" style="background-color: red;border: none;color: white;padding: 15px 32px;text-align: center;text-decoration: none;display: inline-block;font-size: 16px;"><input type="text" name="denegar">Denegar</a>
                            </form>-->
                            <form action="http://d-spp.org/formularioMail.php" method="GET" enctype="application/x-www-form-urlencoded">
                              <input type="text" name="formularioComprobante" value="1">
                              <input type="text" name="aceptar" value="aceptar">
                              <input type="submit">
                            </form>
                          </td>
                        </tr>-->
                        
                      </tbody>
                    </table>

                  </body>
                  </html>
        ';


        $mail->AddAddress($correoCert);
        $mail->AddAttachment($comprobantePago);

        //$mail->Username = "soporte@d-spp.org";
        //$mail->Password = "/aung5l6tZ";
        $mail->Subject = utf8_decode($asunto2);
        $mail->Body = utf8_decode($mensajeEmail2);
        $mail->MsgHTML(utf8_decode($mensajeEmail2));
        $mail->Send();
        $mail->ClearAddresses();



            $queryMensaje = "INSERT INTO mensajes(idopp, asunto, mensaje, destinatario, remitente, fecha) VALUES($idopp, '$asunto2', '$mensajeEmail2', 'ADM', 'OPP', $fecha_actual)";
            $ejecutar = mysql_query($queryMensaje,$dspp) or die(mysql_error());

          /******************************* FIN MAIL FUNDEPPO *************************************************/
          /********************************************************************************/


      }else{
        $comprobantePago = NULL;
      }
      $adjunto = $comprobantePago;

      $query = "INSERT INTO membresia (estado,adjunto,fechaupload,idopp) VALUES ('$statuspago','$adjunto',$fechaupload,$idopp)";
      $insertar = mysql_query($query,$dspp) or die(mysql_error());
      
      //echo "la consulta es: ".$query;

      $idmembresia = mysql_insert_id($dspp);
      $identificador = "MEMBRESIA";


      $queryFecha = "INSERT INTO fecha (fecha, idexterno, idopp, idoc, idmembresia, identificador, status) VALUES ($fecha_actual, $idexterno, $idopp, $idoc, $idmembresia, '$identificador', '$statuspago')";
      $insertarFecha = mysql_query($queryFecha,$dspp) or die(mysql_error());
      //echo "<br>".$queryFecha;

      $update = "UPDATE certificado SET statuspago = '$statuspago' WHERE idcertificado = $idcertificado";
      $insertarupdate = mysql_query($update,$dspp) or die(mysql_error());

    }

    //echo "<br>".$update;
}
/****************************************************************************************************/
/****************************************************************************************************/
/****************************************************************************************************/

if(isset($_POST['formulario_contrato']) && $_POST['formulario_contrato'] == 1){
  $rutaArchivo = "../../archivos/admArchivos/contratos/";
  $nombreArchivo1 = "Contrato de Uso SPP";
  $nombreArchivo2 = "Acuse de Recibido";

  $contrato_uso = "";
  $acuse_recibido = "";

  $_FILES['contrato_uso']['name'];
      move_uploaded_file($_FILES["contrato_uso"]["tmp_name"], $rutaArchivo.time()."_".$_FILES["contrato_uso"]["name"]);
      $contrato_uso = $rutaArchivo.basename(time()."_".$_FILES["contrato_uso"]["name"]);

  $_FILES['acuse_recibido']['name'];
      move_uploaded_file($_FILES["acuse_recibido"]["tmp_name"], $rutaArchivo.time()."_".$_FILES["acuse_recibido"]["name"]);
      $acuse_recibido = $rutaArchivo.basename(time()."_".$_FILES["acuse_recibido"]["name"]);


  $idopp = $_POST['idopp'];
  $idoc = $_POST['idoc'];
  $idsolicitud_certificacion = $_POST['idsolicitud_certificacion'];

  $estatusContrato = "ENVIADO";
  $fecha = time();

  $query = "INSERT INTO contratos(nombre,archivo,idopp,idoc,idsolicitud_certificacion,estatusContrato,fecha) VALUES('$nombreArchivo1', '$contrato_uso', $idopp, $idoc, $idsolicitud_certificacion,'$estatusContrato', $fecha)";
  $insertar = mysql_query($query,$dspp) or die(mysql_error());

  $query = "INSERT INTO contratos(nombre,archivo,idopp,idoc,idsolicitud_certificacion,estatusContrato,fecha) VALUES('$nombreArchivo2', '$acuse_recibido', $idopp, $idoc, $idsolicitud_certificacion,'$estatusContrato', $fecha)";
  $insertar = mysql_query($query,$dspp) or die(mysql_error());


  $destinatario = "cert@spp.coop";
  $fecha = date("d/m/Y", time());
  $asunto = "D-SPP - Contrato de Uso SPP - Acuse de Recibido"; 


        $mensaje = '
                  <html>
                  <head>
                    <meta charset="utf-8">
                  </head>
                  <body>
                  
                    <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
                      <tbody>
                        <tr>
                          <th rowspan="4" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                          <th scope="col" align="left" width="280"><strong>Notificación de Estado / Status Notification ('.$fecha.')</strong></th>
                        </tr>
                        <td>Se ha cargado el Contrato de Uso SPP así como el Acuse de Recibido, por favor inicie sesión en la cuenta de administrador (<a href="http://d-spp.org/?ADM">www.d-spp.org/?ADM</a>) para poder autorizarlo.</td>
                        </tr>
                        <!--<tr>
                          <td>
                            <!--<form name="ingresar" action="http://d-spp.org/formularioMail.php" method="POST" enctype="application/x-www-form-urlencoded">
                              <input type="text" name="formularioComprobante" value="1">
                              <a href="#" onclick="document.ingresar.submit()" type="Submit" style="background-color: #4CAF50;border: none;color: white;padding: 15px 32px;text-align: center;text-decoration: none;display: inline-block;font-size: 16px;" ><input type="text" name="aceptar" value="aceptar">Aceptar</a>
                              <a href="#" onclick="document.ingresar.submit()" type="Submit" style="background-color: red;border: none;color: white;padding: 15px 32px;text-align: center;text-decoration: none;display: inline-block;font-size: 16px;"><input type="text" name="denegar">Denegar</a>
                            </form>-->
                            <form action="http://d-spp.org/formularioMail.php" method="GET" enctype="application/x-www-form-urlencoded">
                              <input type="text" name="formularioComprobante" value="1">
                              <input type="text" name="aceptar" value="aceptar">
                              <input type="submit">
                            </form>
                          </td>
                        </tr>-->
                        
                      </tbody>
                    </table>

                  </body>
                  </html>
        ';


        $mail->AddAddress($correoCert);
        $mail->AddAttachment($contrato_uso);
        $mail->AddAttachment($acuse_recibido);

        //$mail->Username = "soporte@d-spp.org";
        //$mail->Password = "/aung5l6tZ";
        $mail->Subject = utf8_decode($asunto);
        $mail->Body = utf8_decode($mensaje);
        $mail->MsgHTML(utf8_decode($mensaje));
        
        if($mail->Send()){
          
          echo "<script>alert('Se han enviado los documentos correspondientes, sera notificado por correo una vez revisados..');location.href ='javascript:history.back()';</script>";
        }else{
              echo "<script>alert('Error, no se pudo enviar el correo');location.href ='javascript:history.back()';</script>";
     
        }

        /*if($mail->Send()){
          echo "<script>alert('Se han enviado los documentos correspondientes, sera notificado por correo una vez revisados.');</script>";
        }*/
        //$mail->Send();
        $mail->ClearAddresses();



            $queryMensaje = "INSERT INTO mensajes(idopp, asunto, mensaje, destinatario, remitente, fecha) VALUES($idopp, '$asunto', '$mensaje', 'ADM', 'OPP', $fecha_actual)";
            $ejecutar = mysql_query($queryMensaje,$dspp) or die(mysql_error());


}



if(isset($_POST['cancelar']) && $_POST['cancelar'] == "cancelar"){
  $idsolicitud_certificacion = $_POST['idsolicitud_certificacion'];
  $estatus_interno = 14;  

  $updateSQL = "UPDATE solicitud_certificacion SET status = $estatus_interno WHERE idsolicitud_certificacion = $idsolicitud_certificacion";
  $ejecutar = mysql_query($updateSQL,$dspp) or die(mysql_error());
}


$query_limit_opp = sprintf("%s LIMIT %d, %d", $query_opp, $startRow_opp, $maxRows_opp);
$opp = mysql_query($query_limit_opp, $dspp) or die(mysql_error());
//$row_opp = mysql_fetch_assoc($opp);

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

/*************************** VARIABLES DE CONTROL **********************************/
  $estado_interno = "2";

  $validacionStatus = $row_opp['status'] != 1 && $row_opp['status'] != 2 && $row_opp['status'] != 3 && $row_opp['status'] != 14 && $row_opp['status'] != 15;

/*************************** VARIABLES DE CONTROL **********************************/


?>

<hr>

<div class="panel panel-primary">
  <div class="panel-heading">Solicitudes</div>
  <div class="panel-body">

    <?php 
      if(isset($_POST['mensaje'])){
    ?>
      <div class="alert alert-success" role="alert"><?php echo "<b>".$_POST['mensaje']."</b>"; ?></div>
    <?php
      }
     ?>
            <div class="col-xs-12">
              <?php if(isset($_POST['membresia']) && $_POST['membresia'] == "1"){ ?>
                <div class="alert alert-info alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    Su <strong>comprobante de pago</strong> ha sido enviado a revisión, se le notificara cuando haya sido aprobada su membresia.
                </div>
              <?php } ?>
            </div>



    <table class="table table-bordered table-striped" style="font-size:11px">
      <thead>
        <tr>
          <th class="text-center">ID</th>
          <th class="text-center">Application date Solicitud</th>
          <!--<th class="text-center"><h5>Nombre</h5></th>-->
          <th class="text-center">Short name</th>
          <!--<th class="text-center">Contacto OPP</th>
          <th class="text-center">País</th>-->
          <th class="text-center">CE</th>
          <th class="text-center">Application status</th>
          <th class="text-center">Quotation</th>
          
          <th class="text-center">Application observations</th>
          <th class="text-center">Resolution of objection</th>
          <th class="text-center">SPP Membership/Certificate</th>
          <th class="text-center">Actions</th>

          <!--<th>Razón social</h6></th>
          <th>Dirección fiscal</th>
          <th>RFC</th>-->
          <!--<th>Eliminar</th>-->
        </tr>
      </thead>
      <tbody>
        <?php $cont=0; while ($row_opp = mysql_fetch_assoc($opp)) {$cont++; ?>
          <tr class="text-center" >
            <?php  
              $fecha = $row_opp['fecha_elaboracion']; 

              $consultaFecha = "SELECT idfecha FROM fecha WHERE idopp = '$row_opp[idopp]' AND identificador = 'OPP' AND status = 20";
              $ejecutar = mysql_query($consultaFecha,$dspp) or die(mysql_error());
              $totalFecha = mysql_num_rows($ejecutar);
            ?>
            <td><?php echo $row_opp['idsolicitud_certificacion']; ?></td>
<!-------------------------------- INICIA BOTON VER SOLICITUD ---------------------------------->
            <td>
              <?php 
                if($row_opp['status'] == $estado_interno){
              ?>   
                <a class="btn btn-sm btn-primary" style="width:100%" href="?SOLICITUD&amp;detail&amp;idsolicitud=<?php echo $row_opp['idsolicitud_certificacion']; ?>&contact" aria-label="Left Align">
                  <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
                  <?php echo  date("d/m/Y", $fecha); ?><br>Consult application
                </a> 
              <?php
                }else{
               ?>
                <a class="btn btn-sm btn-primary" style="width:100%" href="?SOLICITUD&amp;detail&amp;idsolicitud=<?php echo $row_opp['idsolicitud_certificacion']; ?>&contact" aria-label="Left Align">
                  <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
                  <?php echo  date("d/m/Y", $fecha); ?><br>Consult application
                </a>    
               <?php 
                }
                ?>
            </td>
<!-------------------------------- TERMINA BOTON VER SOLICITUD ---------------------------------->

<!-------------------------------- INICIA NOMBRE DEL OPP ---------------------------------->
            <td>
              <?php 
                if(isset($row_opp['nombre'])){
                  echo $row_opp['nombre'];
                }else{
                  echo "Not available";
                } 
              ?>
            </td>
<!-------------------------------- TERMINA NOMBRE DEL OPP ---------------------------------->


<!-------------------------------- INICIA NOMBRE OC ---------------------------------->
                <td>
                  <?php 
                    if(isset($row_opp['nombreOC'])){
                      echo $row_opp['nombreOC'];
                    }else{
                      echo "Not available";
                    } 
                  ?>
                </td>
 <!-------------------------------- TERMINA NOMBRE OC ---------------------------------->

 <!-------------------------------- INICIA SECCIÓN ESTATUS ---------------------------------->
              <td>
                <?php 
                  $query = "SELECT * FROM status_publico WHERE idstatus_publico = $row_opp[status_publico]";
                  $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
                  $estatus_publico = mysql_fetch_assoc($ejecutar);

                  $query = "SELECT * FROM status WHERE idstatus = $row_opp[status]";
                  $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
                  $estatus_interno = mysql_fetch_assoc($ejecutar);
                 ?>

                <?php if($estatus_interno['idstatus'] == 2){ ?>
                    <a class="btn btn-sm btn-warning" href="?SOLICITUD&amp;detail&amp;idsolicitud=<?php echo $row_opp['idsolicitud_certificacion']; ?>">
                      <span class="glyphicon glyphicon-list-alt"></span> <?php echo $estatus_interno['nombre']; ?>
                    </a>

                <?php }else if($row_opp['status'] != 1 && $row_opp['status'] != 2 && $row_opp['status'] != 3 && $row_opp['status'] != 14 && $row_opp['status'] != 15){?>

                    <?php echo '<p class="alert alert-warning" style="padding:5px"><a  href="#" data-toggle="tooltip" title="'.$estatus_interno['descripcion_interna'].'">'.$estatus_interno['nombre_ingles'].'</a></p>'; ?>

                  <?php }else{ ?>

                    <?php echo '<p class="alert alert-warning" style="padding:5px"><a href="#" data-toggle="tooltip" title="'.$estatus_publico['descripcion_publica'].'">'.$estatus_publico['nombre_ingles'].'</a></p>'; ?>

                <?php } ?>
              </td>
  <!-------------------------------- INICIA SECCIÓN ESTATUS ---------------------------------->

  <!-------------------------------- INICIA SECCIÓN COTIZACIÓN ---------------------------------->
              <td class="text-center" style="width:150px;">
                <form action="" method="post">  
                  <?php 
                  if(!empty($row_opp['cotizacion_opp'])){
                  ?>
                    <a class="btn btn-sm btn-success" href="<?echo $row_opp['cotizacion_opp']?>" target="_blank" type="button" data-toggle="tooltip" data-placement="top" title="Download quotation">
                      <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span>
                    </a>
                  <?php
                  }else{
                  ?>
                    <a class="btn btn-sm btn-default disabled" href="">
                      <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span>
                    </a>
                  <?php
                  }
                   ?>   
                  <?php 
                      if($row_opp['status'] == 1 || $row_opp['status'] == 2 || $row_opp['status'] == 3 || $row_opp['status'] == 20){
                   ?>
                      <button class="btn btn-sm btn-default" disabled>
                        <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
                      </button>
                      <button class="btn btn-sm btn-default" disabled>
                        <span class="glyphicon glyphicon-remove" aria-hidden="true"></span> 
                      </button>
                      
                   <?php 
                    }else if($row_opp['status'] == "17" ){
                    ?>
                      <!-- PROPUESTA ACEPTADA -->
                      <button class="btn btn-sm btn-info" type="submit" name="propuesta_txt" data-toggle="tooltip" data-placement="top" title="Accept quotation" value="18"·>
                        <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
                      </button>
                      
                      <!-- PROPUESTA RECHAZADA -->
                      <button class="btn btn-sm btn-danger" type="submit" name="propuesta_txt" data-toggle="tooltip" data-placement="top" title="Reject quotation" value="24">
                        <span class="glyphicon glyphicon-remove" aria-hidden="true"></span> 
                      </button>
                      <input type="hidden" name="fecha_elaboracion" value="<?php echo $row_opp['fecha_elaboracion']; ?>" >

                  <?php 
                    }else if($row_opp['status'] != 1 && $row_opp['status'] != 2 && $row_opp['status'] != 3 && $row_opp['status'] != 14 && $row_opp['status'] != 15){
                   ?>   
                      <button class="btn btn-sm btn-success" type="submit" name="propuesta_txt" disabled>
                        <span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Accepted
                        <input type="hidden" value="You accepted the quotation, soon you will be contacted" name="mensaje" />
                      </button>
                      
                  <?php
                    }else if($row_opp['status'] == 24){
                  ?>
                      <button class="btn btn-sm btn-danger" type="submit" name="propuesta_txt" disabled>
                        <span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Rejected
                        <input type="hidden" value="You have rejected the quotation" name="mensaje" />
                      </button>
                      


                  <?php } ?>
                    
                    <input type="hidden" value="1" name="opp_propuesta" />
                    <input type="hidden" value="<?php echo $row_opp['p1_email']?>" name="emailOPP1">
                    <input type="hidden" value="<?php echo $row_opp['p2_email']?>" name="emailOPP2">
                    <input type="hidden" value="<?php echo $row_opp['email']?>" name="emailOC">
                    <input type="hidden" value="<?php echo $row_opp['nombreOC']?>" name="nombreOC">
                    <input type="hidden" value="<?php echo $row_opp['nombre']?>" name="nombreOPP">
                    <input type="hidden" value="<?php if(isset($row_opp['telefono'])){echo $row_opp['telefono'];}else if(isset($row_opp['p1_telefono'])){echo $row_opp['p1_telefono'];}?>" name="telefonoOPP">
                    <input type="hidden" value="<?php echo $row_opp['abreviacion']?>" name="abreviacionOPP">
                    <input type="hidden" value="<?php echo $row_opp['pais']?>" name="paisOPP">
                    <input type="hidden" value="<?php echo $row_opp['idsolicitud_certificacion']; ?>" name="idsolicitud" />
                    <input type="hidden" value="<?php echo $row_opp['p1_nombre'];?>" name="nombreOPP1">
                    <input type="hidden" value="<?php echo $row_opp['p2_nombre'];?>" name="nombreOPP2">
                    <input type="hidden" value="<?php echo $row_opp['ciudad'] ?>" name="ciudadOPP">
                    <input type="hidden" value="<?php echo $row_opp['estado']?>" name="estadoOPP">
                    <input type="hidden" value="<?php echo $row_opp['idoc'] ?>" name="idoc">
                    <input type="hidden" value="<?php echo $totalFecha ?>" name="totalFecha">
                </form>
         
              </td>
 <!-------------------------------- TERMINA SECCIÓN COTIZACIÓN ---------------------------------->


 <!-------------------------------- INICIA SECCIÓN OBSERVACIONES ---------------------------------->
              <td>
                <?php if(empty($row_opp['observaciones'])){ ?>
                  <button class="btn btn-sm btn-default" disabled>
                    <span class="glyphicon glyphicon-list-alt"></span> Consult
                  </button>       
                <?php }else{ ?>
                  <a class="btn btn-sm btn-info" style="width:100%" href="?SOLICITUD&amp;detail&amp;idsolicitud=<?php echo $row_opp['idsolicitud_certificacion']; ?>&contact" aria-label="Left Align">
                    <span class="glyphicon glyphicon-list-alt"></span> Consult
                  </a>
                <?php } ?>
              </td>
  <!-------------------------------- TERMINA SECCIÓN OBSERVACIONES ---------------------------------->
            <?php 
              $query_objecion = "SELECT * FROM objecion WHERE idsolicitud = $row_opp[idsolicitud_certificacion]";
              $ejecutar = mysql_query($query_objecion, $dspp) or die(mysql_error());
              $registroObjecion = mysql_fetch_assoc($ejecutar);

             ?>

  <!-------------------------------- INICIA SECCIÓN RESOLUCION DE OBJECION ---------------------------------->
              <td>
                <?php 
                  if(!empty($totalFecha)){
                    echo "<h6 class='alert alert-success' style='margin:7px;'>Renewal Process</h6>";
                  }else{
                 ?>
                <?php 
                if(isset($registroObjecion['dictamen'])){
                  echo "<p class='alert alert-info' style='padding:7px;display:inline;'>$registroObjecion[dictamen]</p>";
                }
                 ?>
                
                  <?php if($row_opp['status'] != 1 && $row_opp['status'] != 2 && $row_opp['status'] != 3 && $row_opp['status'] != 14 && $row_opp['status'] != 15){ ?>
                    <?php if(!empty($registroObjecion['adjunto'])){ ?>

                      <a class="btn btn-sm btn-info" href="<?echo $registroObjecion['adjunto'];?>" target="_blank">
                        <span class="glyphicon glyphicon-download-alt">
                      </a>

                    <?php }else if(!empty($registroObjecion)){ ?>
                    <?php 
                      $query = "SELECT * FROM status_publico WHERE idstatus_publico = $registroObjecion[status]";
                      $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
                      $statusObjecion = mysql_fetch_assoc($ejecutar);
                     ?>
                      <p class="alert alert-danger" role="alert" style="padding:7px;"><?php echo $statusObjecion['nombre_ingles']; ?></p>
                    <?php } ?>
                  <?php } ?>
                
                <?php } ?>
              </td>
  <!-------------------------------- TERMINA SECCIÓN RESOLUCION DE OBJECION ---------------------------------->

  <!-------------------------------- MEMBRESIA SPP / CERTIFICACION ---------------------------------->
                    <?php 
                      //$query = "SELECT * FROM certificado WHERE idsolicitud = $row_opp[idsolicitud_certificacion]";
                      $query = "SELECT certificado.*, MAX(fecha) AS 'fecha',fecha.idfecha, fecha.idexterno, fecha.idcertificado FROM certificado INNER JOIN fecha ON certificado.idcertificado = fecha.idcertificado WHERE certificado.idsolicitud = $row_opp[idsolicitud_certificacion]";
                      $ejecutar = mysql_query($query, $dspp) or die(mysql_error());
                      $registroCertificado = mysql_fetch_assoc($ejecutar);

                      $query = "SELECT * FROM contratos WHERE idsolicitud_certificacion = $row_opp[idsolicitud_certificacion]";
                      $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
                      $registroContatos = mysql_fetch_assoc($ejecutar);

                     ?>

              <td>
                <div class="row">
                  <div class="col-xs-12">
                      <?php if($row_opp['status'] != 1 && $row_opp['status'] != 2 && $row_opp['status'] != 3 && $row_opp['status'] != 14 && $row_opp['status'] != 15 && $row_opp['status'] != 17 && $row_opp['status'] != 20 ){ ?>
                          <button class="btn btn-sm btn-warning" data-toggle="modal" <?php echo "data-target='#myModal".$row_opp['idsolicitud_certificacion']."'"?>>
                            <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Status<br> SPP Membership / Certificate
                      <?php }else{?>
                          <button class="btn btn-sm btn-default" disabled><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Status<br> SPP Membership / Certificate</button>
                      <?php } ?>
                  </div>
                  <?php 
                    if($registroCertificado['statuspago'] == "APROBADO"){
                  ?>
                    <div class="col-xs-12">
                      <a style="font-size:11px;color:white;width:165px;" class="btn btn-success" href="<?php echo $registroCertificado['adjunto']; ?>" target="_blank">Download Certificate</a>
                    </div>
                  <?php
                    }
                   ?>
                </div>
              </td>


                

                    <!-- Modal -->

                      <div class="modal fade" <?php echo "id='myModal".$row_opp['idsolicitud_certificacion']."'" ?> tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog modal-lg" role="document">
                          <div class="modal-content">
                            <div class="modal-header">
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                              <h4 class="modal-title" id="myModalLabel">SPP Membership / Certificate</h4>
                            </div>
                            <div class="modal-body">
                              <div class="row">
                                <div class="col-xs-12">

                                  <!------>
                                  <div class="col-xs-6 ">

                                      <?php if(empty($registroCertificado['idcertificado'])){ ?>
                                        <?php if($row_opp['status'] == 19){ ?>
                                          <div class="col-xs-12 alert alert-warning" role="alert">
                                            <div class="col-xs-12">
                                              Certificate Status: <strong>The certification process has started.</strong>  
                                            </div>                                  
                                          </div>
                                        <?php }else{ ?>
                                          <div class="col-xs-12 alert alert-danger" role="alert">
                                            <div class="col-xs-12">
                                              Certificate Status: <strong>The certification process has not been started.</strong>  
                                            </div>                                  
                                          </div>
                                        <?php } ?>
                                      <?php }else{ ?>
                                        <div class="col-xs-12 alert alert-success" role="alert">
                                          <div class="col-xs-12">
                                            Certificate Status updated: <b><?echo date("d/m/Y", $registroCertificado['fecha']) ?></b>
                                          </div>
                                          <hr>
                                          <div class="col-xs-12 ">
                                          <?
                                            $query = "SELECT * FROM status WHERE idstatus = $registroCertificado[status]";
                                            $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
                                            $estatus = mysql_fetch_assoc($ejecutar);
                                          ?>

                                            <h4><?php echo $estatus['nombre_ingles']; ?></h4>
                                          </div>   
                                          <?php 
                                          /*if($registroCertificado['status'] == 8){
                                          ?>
                                            <div class="col-xs-12 well">
                                              <p>Para poder seguir el Proceso, por favor cargar el Contrato de Uso del Simbolo de Pequeños Productores y Acuse de Recibido firmados.</p>
                                              <p><input type="file" name="contratosSPP"></p>

                                              <input type="submit" class="btn btn-sm btn-primary">
                                              <input type="text" name="contratosSPP" value="1">
                                            </div>
                                          <?php
                                          }*/
                                           ?>     
                                        </div>                                
                                      <?php } ?>

                                      <?php 
                                      if(isset($registroContatos['idcontrato'])){
                                      ?>

                                          <div class="col-xs-12 alert alert-warning">
                                            <div class="col-xs-12"><h4> User´s_Contract</h4></div>
                                            <div class="col-xs-12">
                                              The SPP User´s_Contract has been charged, as well as the Confirmation of Receipt.
                                            </div>
                                            <div class="col-xs-12">Status: <b><?php echo $registroContatos['estatusContrato']; ?></b></div>

                                          </div>

                                      <?php
                                      }else if($row_opp['status'] == 8){
                                      ?>
                                        <form action="" method="post" id="contrato_spp" enctype="multipart/form-data">
                                          <div class="col-xs-12 alert alert-warning">
                                            <div class="col-xs-12"><h4>User´s_Contract</h4></div>
                                            <div class="col-xs-12">
                                              Please upload the User´s_Contract, as well as the Confirmation of Receipt which must be duly signed.
                                            </div>
                                            <div class="col-xs-12">
                                              <input name="contrato_uso" type="file" class="filestyle" data-buttonName="btn-info" data-buttonBefore="true" data-buttonText="Contrato SPP" required>
                                            </div>
                                            <div class="col-xs-12">
                                              <input name="acuse_recibido" type="file" class="filestyle" data-buttonName="btn-info" data-buttonBefore="true" data-buttonText="Acuse" required>
                                            </div>
                                            <div class="col-xs-12">
                                              <input type="submit" class="btn btn-success">
                                              <input type="hidden" name="idopp" value="<?php echo $row_opp['idopp'];?>">
                                              <input type="hidden" name="idoc" value="<?php echo $row_opp['idoc'];?>">
                                              <input type="hidden" name="idsolicitud_certificacion" value="<?php echo $row_opp['idsolicitud_certificacion'] ?>">
                                              <input type="hidden" name="formulario_contrato" value="1">
                                            </div>
                                          </div>
                                        </form>
                                      <?php
                                      }else{
                                      ?>
                                          <div class="col-xs-12 alert alert-danger">
                                            <div class="col-xs-12"><h4>User´s_Contract</h4></div>
                                            <div class="col-xs-12">
                                              The Certification Process has not yet been completed.
                                            </div>
                                          </div>
                                      <?php 
                                      }
                                       ?>



                                  </div>
                    <form action="" method="post" id="pagoCertificado" enctype="multipart/form-data">
                                  <?php if($registroCertificado['statuspago'] == "APROBADO"){ ?>
                                    <div class="col-xs-6 alert alert-success">
                                      <div class="col-xs-12">
                                      <p>Membership Status: <b><?if(empty($registroCertificado['statuspago'])){echo "The payment has not been made";}else{echo $registroCertificado['statuspago'];}?></b></p>
                                      <p><strong>Congratulations!!!</strong> your membership has been accredited, from this moment you will be able to have your certificate.</p>
                                      </div>
                                      <div class="col-xs-12">
                                        <?php 
                                          if($registroCertificado['statuspago'] == "APROBADO"){
                                            echo "<div class='col-xs-6'><b><u>Your certificate expires on: ".$registroCertificado['vigenciafin']."</u></b></div>";
                                          }
                                         ?>
                                        <!--<button class="btn btn-info">Cargar Comprobante de Pago</button>-->
                                        <!--<input name="comprobante" type="file" class="filestyle" data-buttonName="btn-info" data-buttonBefore="true" data-buttonText="Cargar Comprobante de Pago"> -->
                                      </div>



                                    </div>
                                  <?php }else if($registroCertificado['statuspago'] == "REVISION"){ ?>
                                    <div class="col-xs-6 alert alert-warning">
                                      <div class="col-xs-12">
                                      <p>Membership Status: <b><?if(empty($registroCertificado['statuspago'])){echo "The payment has not been made";}else{echo $registroCertificado['statuspago'];}?></b></p>
                                      <p>Your proof of payment has been sent for review, you will be notified when your membership has been approved</p>
                                      </div>
                                      <div class="col-xs-12">

                                        <!--<button class="btn btn-info">Cargar Comprobante de Pago</button>-->
                                        <!--<input name="comprobante" type="file" class="filestyle" data-buttonName="btn-info" data-buttonBefore="true" data-buttonText="Cargar Comprobante de Pago"> -->
                                      </div>
                                    </div>
                                  <?php }else if($registroCertificado['statuspago'] == "POR REALIZAR" || $registroCertificado['statuspago'] == "DENEGADO" || empty($registroCertificado['statuspago'])){ ?>
                                    <div class="col-xs-6 alert alert-danger">
                                      <div class="col-xs-12">
                                      <p>Membership Status: <b><?if(empty($registroCertificado['statuspago'])){echo "The payment has not been made";}else{echo $registroCertificado['statuspago'];}?></b></p>
                                      <p>Once you have paid the membership and have been credited, you can have your certificate.</p>
                                      </div>
                                      <div class="col-xs-12">
                                        <?php if(empty($registroCertificado['adjunto'])){ ?>
                                          <p class="well">Once the certification is finished, the option will be unblocked so that you can make the payment corresponding to your membership.</p>
                                        <?php }else{ ?>
                                          <input name="comprobante" type="file" class="filestyle" data-buttonName="btn-info" data-buttonBefore="true" data-buttonText="Load Proof of Payment">
                                        <?php } ?>
                                        <!--<button class="btn btn-info">Cargar Comprobante de Pago</button>-->
                                                                      <?php if($registroCertificado['statuspago'] == "POR REALIZAR" || $registroCertificado['statuspago'] == "DENEGADO"){ ?>
                                <button type="submit" class="btn btn-primary">Save</button>
                              <?php }?>
                                      </div>
                                    </div>
                                  <?php } ?>


                                  <!------>
                                </div>
                              </div>
                            </div>


                            <div class="modal-footer">
                              <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>

                              
                              <input type="hidden" name="membresiaidopp" value="<?php echo $row_opp['idopp'];?>">
                              <input type="hidden" name="membresiaidoc" value="<?php echo $row_opp['idoc'];?>">
                              <input type="hidden" name="idcertificado" value="<?php echo $registroCertificado['idcertificado'];?>">
                              <input type="hidden" name="statusMembresia" value="<?php echo $registroCertificado['statuspago'];?>">


                              <input type="hidden" name="fechaupload" value="<?php echo time();?>">
                              <input type="hidden" name="membresia" value="1">
                              <input type="hidden" name="statusInterno" value="10">
                              <input type="hidden" name="idsolicitud" value="<?echo $row_opp['idsolicitud_certificacion'];?>">

                            </div>
                          </div>
                        </div>
                      </div>
                    </form>
                    <!-- Modal -->

  <!-------------------------------- MEMBRESIA SPP / CERTIFICACION ---------------------------------->


            <td>
              <?php 
              if($row_opp['status'] != 10){
              ?>
                <form action="" method="post" id="cancelarSolicitud">
                  <button class="btn btn-sm btn-danger" type="submit"  data-toggle="tooltip" data-placement="top" title="Cancel application">
                    <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                  </button>
                  <input type="hidden" name="idsolicitud_certificacion" value="<?php echo $row_opp['idsolicitud_certificacion']; ?>">
                  <input type="hidden" name="cancelar" value="cancelar">
                </form>       
              <?php
              }
               ?>
            </td>
          </tr>
          <?php }  ?>

          <? if($cont==0){?>
          <tr><td colspan="12" class="alert alert-info" role="alert">No records found</td></tr>
          <? }?>
      </tbody>
    </table>


  </div>
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