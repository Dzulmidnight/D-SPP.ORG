<?php 
require_once('../Connections/dspp.php'); 
require_once('../Connections/mail.php');
mysql_select_db($database_dspp, $dspp);


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

/*if(isset($_POST['empresa_delete'])){
  $query=sprintf("delete from empresa where idempresa = %s",GetSQLValueString($_POST['idempresa'], "text"));
  $ejecutar=mysql_query($query,$dspp) or die(mysql_error());
}*/

/************ INICIA VARIABLES GLOBALES ***************/
$fecha = time();
$idoc = $_SESSION['idoc'];
$spp_global = "cert@spp.coop";
$finanzas_spp = "adm@spp.coop";
$administrador = "yasser.midnight@gmail.com";
$query_oc = mysql_query("SELECT * FROM oc WHERE idoc = $idoc", $dspp) or die(mysql_error());
$oc = mysql_fetch_assoc($query_oc);
/**********  TERMINA VARIABLES GLOBALES *****************/
 
if(isset($_POST['cancelar']) && $_POST['cancelar'] == "cancelar"){
  $idsolicitud_registro = $_POST['idsolicitud_registro'];
  $estatus_interno = 24;  

  $updateSQL = "UPDATE solicitud_registro SET status = $estatus_interno WHERE idsolicitud_registro = $idsolicitud_registro";
  $ejecutar = mysql_query($updateSQL,$dspp) or die(mysql_error());
}

if(isset($_POST['reemplazar_cotizacion']) && $_POST['reemplazar_cotizacion'] == 1){
  $idsolicitud_registro = $_POST['idsolicitud_registro'];
  $rutaArchivo = "../../archivos/ocArchivos/cotizaciones/";

  if(!empty($_FILES['nueva_cotizacion']['name'])){
      $_FILES["nueva_cotizacion"]["name"];
        move_uploaded_file($_FILES["nueva_cotizacion"]["tmp_name"], $rutaArchivo.$fecha."_".$_FILES["nueva_cotizacion"]["name"]);
        $archivo = $rutaArchivo.basename($fecha."_".$_FILES["nueva_cotizacion"]["name"]);
        unlink($_POST['cotizacion_actual']);
        $updateSQL = sprintf("UPDATE solicitud_registro SET cotizacion_empresa = %s WHERE idsolicitud_registro = %s",
          GetSQLValueString($archivo, "text"),
          GetSQLValueString($idsolicitud_registro, "int"));
        $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

        $row_empresa = mysql_query("SELECT empresa.nombre, empresa.abreviacion AS 'abreviacion_empresa', empresa.pais AS 'empresa_pais', empresa.spp, empresa.password, empresa.email, oc.email1, oc.email2, oc.abreviacion AS 'abreviacion_oc', oc.pais AS 'pais_oc', solicitud_registro.contacto1_email, solicitud_registro.contacto2_email, solicitud_registro.adm1_email FROM empresa INNER JOIN solicitud_registro ON empresa.idempresa = solicitud_registro.idempresa INNER JOIN oc ON solicitud_registro.idoc = oc.idoc WHERE idsolicitud_registro = $idsolicitud_registro", $dspp) or die(mysql_error());
        $empresa_detail = mysql_fetch_assoc($row_empresa);

        $asunto = "D-SPP Cotización actualizada / updated Price Quote ";

        $cuerpo_mensaje = '
          <html>
          <head>
            <meta charset="utf-8">
          </head>
          <body>
          
            <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
              <tbody>
                <tr>
                  <th rowspan="4" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                  <th scope="col" align="left" width="280" ><strong>Notificación de Cotización / Price Notification</strong></th>
                </tr>
                <tr>
                  <td align="left" style="color:#ff738a;">Email Organismo de Certificación / Certification Entity: '.$empresa_detail['email1'].'</td>
                </tr>
                <tr>
                  <td aling="left" style="text-align:justify">
                  <b style="color:red">'.$empresa_detail['abreviacion_oc'].'</b> ha enviado la cotización actualizada correspondiente a la Solicitud de Registro para Compradores y Otros Actores.
                  <br><br> Por favor iniciar sesión en el siguiente enlace <a href="http://d-spp.org/">www.d-spp.org/</a> como OPP, para poder acceder a la cotización.

                  <br><br>
                  <b style="color:red">'.$empresa_detail['abreviacion_oc'].'</b> has sent the quotation corresponding to the Certification Application for Small producers organizations.
                    <br><br>Please log in to the following link <a href="http://d-spp.org/?OPP">www.d-spp.org/</a> as OPP to access the quotation.

                  </td>
                </tr>

                <tr>
                  <td colspan="2">
                    <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;" border="1" width="650px">
                      <tbody>
                        <tr style="font-size: 12px; text-align:center; background-color:#dff0d8; color:#3c763d;" height="50px;">
                          <td width="130px">Nombre de la Empresa/Company name</td>
                          <td width="130px">País / Country</td>
                          <td width="130px">Organismo de Certificación / Certification Entity</td>
                          <td width="130px">Fecha de envío / Shipping Date</td>
                       
                          
                        </tr>
                        <tr style="font-size: 12px; text-align:justify">
                          <td style="padding:10px;">
                            '.$empresa_detail['nombre'].' - ('.$empresa_detail['abreviacion_empresa'].')
                          </td>
                          <td style="padding:10px;">
                            '.$empresa_detail['empresa_pais'].'
                          </td>
                          <td style="padding:10px;">
                            '.$empresa_detail['abreviacion_oc'].'
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
                        <td coslpan="2">Para cualquier duda o aclaración por favor contactar a: soporte@d-spp.org</td>
                      </tr>
              </tbody>
            </table>

          </body>
          </html>
        ';
        if(!empty($empresa_detail['email'])){
          $mail->AddAddress($empresa_detail['email']);
        }
        if(!empty($empresa_detail['contacto1_email'])){
          $mail->AddAddress($empresa_detail['contacto1_email']);
        }
        if(!empty($empresa_detail['contacto2_email'])){
          $mail->AddAddress($empresa_detail['contacto2_email']);
        }

        $mail->AddBCC($administrador);
        $mail->AddBCC($spp_global);
        if(!empty($oc['email1'])){
          $mail->AddCC($oc['email1']);
        }
        if(!empty($oc['email2'])){
          $mail->AddCC($oc['email2']);
        }
        //se adjunta la cotización
        $mail->AddAttachment($archivo);

        //$mail->Username = "soporte@d-spp.org";
        //$mail->Password = "/aung5l6tZ";
        $mail->Subject = utf8_decode($asunto);
        $mail->Body = utf8_decode($cuerpo_mensaje);
        $mail->MsgHTML(utf8_decode($cuerpo_mensaje));
        $mail->Send();
        $mail->ClearAddresses();


        echo "<script>alert('Se ha actualizado la cotización');</script>";


  }else{
    $archivo = $_POST['cotizacion_actual'];
    echo "<script>alert('No se ha podido actualizar la cotización');</script>";

  }


}

if(isset($_POST['guardar_proceso']) && $_POST['guardar_proceso'] == 1){
  $query_empresa = mysql_query("SELECT solicitud_registro.idempresa, solicitud_registro.idoc, solicitud_registro.contacto1_email, solicitud_registro.contacto2_email, solicitud_registro.adm1_email, empresa.nombre, empresa.abreviacion AS 'abreviacion_empresa', empresa.email FROM solicitud_registro INNER JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa WHERE solicitud_registro.idsolicitud_registro = $_POST[idsolicitud_registro]", $dspp) or die(mysql_error());
  $detalle_empresa = mysql_fetch_assoc($query_empresa);
  $estatus_dspp = 8; //INICIA PROCESO DE CERTIFICACION
  //ESTATUS INTERNO
  // 8 = DICTAMEN POSITIVO
  //9 = DICTAMEN NEGATIVO
  // INSERTAMOS EL REGISTRO DE PROCESO_CERTIFICACION
  if(isset($_POST['nombre_archivo'])){
    $nombre_archivo = $_POST['nombre_archivo'];
  }else{
    $nombre_archivo = '';
  }
  if(isset($_POST['accion'])){
    $accion = $_POST['accion'];
  }else{
    $accion = '';
  }

  $rutaArchivo = "../../archivos/ocArchivos/anexos/";

  if(!empty($_FILES['archivo_estatus']['name'])){
      $_FILES["archivo_estatus"]["name"];
        move_uploaded_file($_FILES["archivo_estatus"]["tmp_name"], $rutaArchivo.$fecha."_".$_FILES["archivo_estatus"]["name"]);
        $archivo = $rutaArchivo.basename($fecha."_".$_FILES["archivo_estatus"]["name"]);
  }else{
    $archivo = NULL;
  }



  if($_POST['estatus_interno'] == 8){ //checamos si el estatus_interno es positvo
    $nombre_oc = mysql_query("SELECT nombre, email1, email2 FROM oc WHERE idoc = $detalle_empresa[idoc]", $dspp) or die(mysql_error());
    $detalle_oc = mysql_fetch_assoc($nombre_oc);
      $estatus_dspp = 9; //Termina proceso de certificación
      $asunto = "D-SPP | Notificación de Dictamen";
    if($_POST['tipo_solicitud'] == 'RENOVACION'){

      //creamos la variable del archivo extra
      if(!empty($_FILES['archivo_extra']['name'])){
          $_FILES["archivo_extra"]["name"];
            move_uploaded_file($_FILES["archivo_extra"]["tmp_name"], $rutaArchivo.$fecha."_".$_FILES["archivo_extra"]["name"]);
            $archivo = $rutaArchivo.basename($fecha."_".$_FILES["archivo_extra"]["name"]);
      }else{
        $archivo = NULL;
      }

      $updateSQL = sprintf("UPDATE solicitud_registro SET estatus_interno = %s, estatus_dspp = %s WHERE idsolicitud_registro = %s",
        GetSQLValueString($_POST['estatus_interno'], "int"),
        GetSQLValueString($estatus_dspp, "int"),
        GetSQLValueString($_POST['idsolicitud_registro'], "int"));
      $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

      $insertSQL = sprintf("INSERT INTO proceso_certificacion (idsolicitud_registro, estatus_interno, estatus_dspp, accion, archivo, fecha_registro) VALUES (%s, %s, %s, %s, %s, %s)",
        GetSQLValueString($_POST['idsolicitud_registro'], "int"),
        GetSQLValueString($_POST['estatus_interno'], "int"),
        GetSQLValueString($estatus_dspp, "int"),
        GetSQLValueString($accion, "text"),
        GetSQLValueString($archivo, "text"),
        GetSQLValueString($fecha, "int"));
      $insertar = mysql_query($insertSQL,$dspp) or die(mysql_error());
      if(!empty($_POST['mensaje_empresa'])){
        $cuerpo_mensaje_empresa = '
          <html>
          <head>
            <meta charset="utf-8">
          </head>
          <body>
            <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
              <tbody>
                <tr>
                  <th rowspan="2" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                  <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">Notificación de Dictamen</span></p></th>

                </tr>
                <tr>
                 <th scope="col" align="left" width="280"><p>EMPRESA: <span style="color:red">'.$detalle_empresa['nombre'].' - ('.$detalle_empresa['abreviacion_empresa'].')</span></p></th>
                </tr>

                <tr>
                  <td colspan="2">
                      <p>
                        '.$_POST['mensaje_empresa'].'
                      </p>
                  </td>
                </tr>

                <tr>
                  <td colspan="2">
                    <p>Para cualquier duda o aclaración por favor escribir a: <span style="color:red">cert@spp.coop</span> o <span style="color:red">'.$oc['email1'].', '.$oc['email2'].'</span></p>
                  </td>
                </tr>
              </tbody>
            </table>
          </body>
          </html>
        ';
      }else{
        $cuerpo_mensaje_empresa = '
          <html>
          <head>
            <meta charset="utf-8">
          </head>
          <body>
            <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
              <tbody>
                <tr>
                  <th rowspan="2" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                  <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">Notificación de Dictamen / Notification of resolution</span></p></th>

                </tr>
                <tr>
                 <th scope="col" align="left" width="280"><p>EMPRESA / COMPANY: <span style="color:red">'.$detalle_empresa['nombre'].' - ('.$detalle_empresa['abreviacion_empresa'].')</span></p></th>
                </tr>

                <tr>
                  <td colspan="2">
                      <p>
                        1. Nosotros <span style="color:red">'.$detalle_oc['nombre'].'</span>, como Organismo de Certificación autorizado por SPP Global, nos complace informar por este medio que la evaluaciòn SPP fue concluida con resultado positivo. En breve sera contactado.
                      </p>
                  </td>
                </tr>
                <tr>
                  <td colspan="2">
                      <p>
                        1. We, <span style="color:red">'.$detalle_oc['nombre'].'</span>, as a Certification Entity authorized by SPP Global, are pleased to inform you, by this means, that the SPP evaluation has been concluded with a “positive” result. You will be contacted soon.
                      </p>
                  </td>
                </tr>

                <tr>
                  <td colspan="2">
                    <p>Para cualquier duda o aclaración por favor escribir a: <span style="color:red">'.$oc['email1'].', '.$oc['email2'].'</span></p>
                  </td>
                </tr>
              </tbody>
            </table>
          </body>
          </html>
        ';
      }
      if(!empty($archivo)){
        $mail->AddAttachment($archivo);
      }

      if(!empty($detalle_empresa['contacto1_email'])){
        $mail->AddAddress($detalle_empresa['contacto1_email']);
      }
      if(!empty($detalle_empresa['contacto2_email'])){
        $mail->AddAddress($detalle_empresa['contacto2_email']);
      }
      if(!empty($detalle_empresa['adm1_email'])){
        $mail->AddAddress($detalle_empresa['adm1_email']);
      }
      if(!empty($detalle_empresa['email'])){
        $mail->AddAddress($detalle_empresa['email']);
      }
      if(isset($oc['email1'])){
        $mail->AddCC($oc['email1']);
      }
      if(isset($oc['email2'])){
        $mail->AddCC($oc['email2']);
      }
      $mail->AddBCC($spp_global);
      $mail->AddBCC($finanzas_spp);


      $mail->Subject = utf8_decode($asunto);
      $mail->Body = utf8_decode($cuerpo_mensaje_empresa);
      $mail->MsgHTML(utf8_decode($cuerpo_mensaje_empresa));

      $mail->Send();
      $mail->ClearAddresses();

      $cuerpo_mensaje = '
        <html>
        <head>
          <meta charset="utf-8">
        </head>
        <body>
          <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
            <tbody>
              <tr>
                <th rowspan="2" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">Cargar Formato, Dictamen e Informe de Evaluación / Upload Evaluation Form, Resolution and Report</span></p></th>

              </tr>
              <tr>
               <th scope="col" align="left" width="280"><p>EMPRESA / COMPANY: <span style="color:red">'.$detalle_empresa['nombre'].' - ('.$detalle_empresa['abreviacion_empresa'].')</span></p></th>
              </tr>

              <tr>
                <td colspan="2">
                 <p>SPP GLOBLA notifica que la empresa: '.$detalle_empresa['nombre'].' ha concluido su Renovación del Registro por lo tanto debe realizar las siguientes acciones.</p>
                 <p>
                  Por favor procedan a ingresar en su cuenta de OC dentro del sistema D-SPP para poder cargar los siguientes documento: 
                     <ul style="color:red">
                       <li>Formato de Evaluación</li>
                       <li>Informe de Evaluación</li>
                       <li>Dictamen de Evaluación</li>
                     </ul>

                 </p>
                </td>
              </tr>
              <tr>
                <td coslpan="2">
                  ¿Pasos para cargar la documentación?. Para poder cargar la documentación debe seguir los siguiente pasos:
                  <ol>
                    <li>Dar clic en la opción "SOLICITUDES"</li>
                    <li>Seleccionar "Solicitudes EMPRESA"</li>
                    <li>Posicionarse en la columna "Certificado" y dar clic en el boton "Cargar Certificado"</li>
                    <li>Se desplegara una ventana donde podran cargar la documentación</li>
                  </ol>
                  <p style="color:red">
                    Se notificara una vez que sea aprobada la documentación para poder cargar el certificado.
                  </p>
                </td>
              </tr>


              <tr>
                <td colspan="2">
                 <p>
                  SPP Global hereby notifies that the '.$detalle_empresa['nombre'].' company has concluded its Registration Renewal and should thus carry out the following actions:
                 </p>
                 <p>
                  Please proceed to enter your CE account in the D-SPP system in order to upload the following documents:
                     <ul style="color:red">
                       <li>•  Evaluation Form</li>
                       <li>•  Evaluation Report</li>
                       <li>•  Evaluation Resolution</li>
                     </ul>

                 </p>
                </td>
              </tr>
              <tr>
                <td coslpan="2">
                  Steps to upload documents? In order to upload documents, these steps should be followed:
                  <ol>
                    <li>• Click on the “APPLICATIONS” option.</li>
                    <li>• Select “COMPANY Applications.”</li>
                    <li>• Go to the “Certificate” column and click on the “Upload Certificate” button.</li>
                    <li>• A window will open and documents may be uploaded there.</li>
                  </ol>
                  <p style="color:red">
                    • When documents have been approved, you will be notified that the certificate may be uploaded.
                  </p>
                </td>
              </tr>


              <tr>
                <td colspan="2">
                  <p>Para cualquier duda o aclaración por favor escribir a: <span style="color:red">'.$oc['email1'].', '.$oc['email2'].'</span></p>
                </td>
              </tr>
            </tbody>
          </table>
        </body>
        </html>
      ';

          $mail->AddAddress($oc['email1']);
          $mail->AddAddress($oc['email2']);
          $mail->Subject = utf8_decode($asunto);
          $mail->Body = utf8_decode($cuerpo_mensaje);
          $mail->MsgHTML(utf8_decode($cuerpo_mensaje));

          if($mail->Send()){
            
            echo "<script>alert('Correo enviado Exitosamente.');location.href ='javascript:history.back()';</script>";
          }else{
                echo "<script>alert('Error, no se pudo enviar el correo, por favor contacte al administrador: soporte@d-spp.org');location.href ='javascript:history.back()';</script>";
       
          }
          //$mail->Send();
          $mail->ClearAddresses();
      ///termina envio de mensaje dictamen positivo


    }else{  //incia se envian documento si es una nueva solicitud

      $documentacion_nombres = '';

      //creamos la variable del archivo extra
      if(!empty($_FILES['archivo_extra']['name'])){
          $_FILES["archivo_extra"]["name"];
            move_uploaded_file($_FILES["archivo_extra"]["tmp_name"], $rutaArchivo.$fecha."_".$_FILES["archivo_extra"]["name"]);
            $archivo = $rutaArchivo.basename($fecha."_".$_FILES["archivo_extra"]["name"]);
      }else{
        $archivo = NULL;
      }
      $updateSQL = sprintf("UPDATE solicitud_registro SET estatus_interno = %s, estatus_dspp = %s WHERE idsolicitud_registro = %s",
        GetSQLValueString($_POST['estatus_interno'], "int"),
        GetSQLValueString($estatus_dspp, "int"),
        GetSQLValueString($_POST['idsolicitud_registro'], "int"));
      $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

      $insertSQL = sprintf("INSERT INTO proceso_certificacion (idsolicitud_registro, estatus_interno, estatus_dspp, nombre_archivo, accion, archivo, fecha_registro) VALUES (%s, %s, %s, %s, %s, %s, %s)",
        GetSQLValueString($_POST['idsolicitud_registro'], "int"),
        GetSQLValueString($_POST['estatus_interno'], "int"),
        GetSQLValueString($estatus_dspp, "int"),
        GetSQLValueString($_POST['nombreArchivo'], "text"),
        GetSQLValueString($accion, "text"),
        GetSQLValueString($archivo, "text"),
        GetSQLValueString($fecha, "int"));
      $insertar = mysql_query($insertSQL,$dspp) or die(mysql_error());

      //creamos el monto del comprobante de pago
      /*$estatus_comprobante = "EN ESPERA";

      $insertSQL = sprintf("INSERT INTO comprobante_pago(estatus_comprobante, monto) VALUES (%s, %s)",
        GetSQLValueString($estatus_comprobante, "text"),
        GetSQLValueString($_POST['monto_membresia'], "text"));
      $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

      //capturamos el id del comprobante para vincularlo con la membresia
      $idcomprobante_pago = mysql_insert_id($dspp);*/

      //creamos la membresia
      /*$estatus_membresia = "EN ESPERA";
      $insertSQL = sprintf("INSERT INTO membresia(estatus_membresia, idempresa, idsolicitud_registro, idcomprobante_pago) VALUES (%s, %s, %s, %s)",
        GetSQLValueString($estatus_membresia, "text"),
        GetSQLValueString($_POST['idempresa'], "int"),
        GetSQLValueString($_POST['idsolicitud_registro'], "int"),
        GetSQLValueString($idcomprobante_pago, "int"));
      $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());*/

      ///inicia envio de mensaje dictamen positivo

      $row_documentacion = mysql_query("SELECT * FROM documentacion WHERE idestatus_interno = 8 AND idioma = '$_POST[idioma]' AND documentacion.nombre != 'Datos Bancarios SPP'", $dspp) or die(mysql_error());
      while($documentacion = mysql_fetch_assoc($row_documentacion)){
          $mail->AddAttachment($documentacion['archivo']);
          $documentacion_nombres .= "<li>".$documentacion['nombre']."</li>";   
      }
      if(!empty($archivo)){
        $documentacion_nombres .= '<li>'.$_POST['nombreArchivo'].'</li>';
        $mail->AddAttachment($archivo);
      }

      $documentacion = mysql_fetch_assoc($row_documentacion);

      if(!empty($_POST['mensaje_empresa'])){
        $cuerpo_mensaje = '
          <html>
          <head>
            <meta charset="utf-8">
          </head>
          <body>
            <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
              <tbody>
                <tr>
                  <th rowspan="2" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                  <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">Notificación de Dictamen</span></p></th>

                </tr>
                <tr>
                 <th scope="col" align="left" width="280"><p>Para: <span style="color:red">'.$detalle_empresa['nombre'].' - ('.$detalle_empresa['abreviacion_empresa'].')</span></p></th>
                </tr>

                <tr>
                  <td colspan="2">
                    '.$_POST['mensaje_empresa'].'
                  </td>
                </tr>
                <tr>
                  <td><p><strong>Documentos Anexos</strong></p></td>
                </tr>
                <tr>
                  <td>
                    <ul>
                      '.$documentacion_nombres.'
                    </ul>
                  </td>
                </tr>
                <tr>
                  <td colspan="2" style="text-align:justify">
                      <p>
                        DESPUÉS DE FIRMAR EL <span style="color:red">CONTRATO DE USO</span> PROCEDA A CARGAR EL MISMO POR MEDIO DEL SISTEMA D-SPP, ESTO INGRESANDO EN SU CUENTA DE EMPRESA EN LA SIGUIENTE DIRECCIÓN <a href="http://d-spp.org/esp/?COM">http://d-spp.org/esp/?COM</a>.
                      </p>
                  </td>
                </tr>
                <tr>
                  <td colspan="2" style="padding-top:10px;">
                    <p>En caso de cualquier duda o aclaración por favor escribir a <span style="color:red">cert@spp.coop</span> o <span style="color:red">'.$detalle_oc['email1'].', '.$detalle_oc['email2'].'</span></p>
                  </td>
                </tr>
              </tbody>
            </table>
          </body>
          </html>
        ';
      }else{
        $cuerpo_mensaje = '
          <html>
          <head>
            <meta charset="utf-8">
          </head>
          <body>
            <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
              <tbody>
                <tr>
                  <th rowspan="2" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                  <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">Notificación de Dictamen / Notification of resolution</span></p></th>

                </tr>
                <tr>
                 <th scope="col" align="left" width="280"><p>Para: <span style="color:red">'.$detalle_empresa['nombre'].' - ('.$detalle_empresa['abreviacion_empresa'].')</span></p></th>
                </tr>

                <tr>
                  <td colspan="2" style="text-align:justify">
                    <p>
                      1. Nosotros <span style="color:red">'.$detalle_oc['nombre'].'</span>, como Organismo de Certificación autorizado por SPP Global, nos complace informar por este medio que la evaluaciòn SPP fue concluida con resultado positivo.
                    </p>
                    <p>
                      2. <span style="color:red">Para concluir el proceso, se solicita de la manera más atenta leer los documentos anexos y posteriormente firmar el Contrato de Uso y Acuse de Recibo.</span>
                    </p>
                    <p>
                      Una vez que haya firmado los documentos indicados, ingresar a su cuenta de Empresa dentro del sistema <a href="http://d-spp.org/">d-spp.org</a> y cargar los documentos para que éstos sean revisados por SPP Global.
                    </p>
                    <p>
                      4. Una vez que SPP Global confirme a través del Sistema la recepción de los documentos, el Organismo de Certificaciòn hará entrega del Certificado.
                    </p>
                  </td>
                </tr>

                <tr>
                  <td colspan="2" style="text-align:justify">
                    <p>
                      1.  We, <span style="color:red">'.$detalle_oc['nombre'].'</span>, as a Certification Entity authorized by SPP Global, are pleased to inform you, by this means, that the SPP evaluation has been concluded with a “positive” result.
                    </p>
                    <p>
                      2. <span style="color:red">In order to complete the process, the most careful request is to read the attached documents and subsequently sign the User´s Contract and Acknowledgment of Receipt.</span>
                    </p>
                    <p>
                      Once you have signed the indicated documents, enter your Company account in the system <a href="http://d-spp.org/">d-spp.org</a> and load the documents so that they are reviewed by SPP Global.
                    </p>
                    <p>
                      4. Once SPP Global confirms through the System the receipt of the documents, the Certification Entity will deliver the Certificate.
                    </p>
                  </td>
                </tr>

                <tr>
                  <td><p><strong>Documentos Anexos / Attached documents</strong></p></td>
                </tr>
                <tr>
                  <td>
                    <ul>
                      '.$documentacion_nombres.'
                    </ul>
                  </td>
                </tr>
                <tr>
                  <td colspan="2" style="text-align:justify">
                      <p>
                        AFTER MAKING PAYMENT, PLEASE UPLOAD “SIGNED <span style="color:red">USER’S CONTRACT</span> AND <span style="color:red">ACKNOWLEDGEMENT OF RECEIPT</span>.”  THROUGH THE D-SPP SYSTEM, BY ENTERING YOUR ACCOUNT AS A COMPANY.
                      </p>
                  </td>
                </tr>
                <tr>
                  <td colspan="2" style="padding-top:10px;">
                    <p>En caso de cualquier duda o aclaración por favor escribir a <span style="color:red">'.$detalle_oc['email1'].', '.$detalle_oc['email2'].'</span></p>
                  </td>
                </tr>
              </tbody>
            </table>
          </body>
          </html>
        ';
      }
      if(!empty($detalle_empresa['contacto1_email'])){
        $mail->AddAddress($detalle_empresa['contacto1_email']);
      }
      if(!empty($detalle_empresa['contacto2_email'])){
        $mail->AddAddress($detalle_empresa['contacto2_email']);
      }
      if(!empty($detalle_empresa['adm1_email'])){
        $mail->AddAddress($detalle_empresa['adm1_email']);
      }
      if(!empty($detalle_empresa['email'])){
        $mail->AddAddress($detalle_empresa['email']);
      }
      if(isset($detalle_oc['email1'])){
        $mail->AddCC($detalle_oc['email1']);
      }
      if(isset($detalle_oc['email2'])){
        $mail->AddCC($detalle_oc['email2']);
      }
      $mail->AddBCC($spp_global);
      $mail->AddBCC($finanzas_spp);

      $mail->Subject = utf8_decode($asunto);
      $mail->Body = utf8_decode($cuerpo_mensaje);
      $mail->MsgHTML(utf8_decode($cuerpo_mensaje));
      $mail->Send();
      $mail->ClearAddresses();
      /*if($mail->Send()){
        $mail->ClearAddresses();
        echo "<script>alert('Correo enviado Exitosamente.');location.href ='javascript:history.back()';</script>";
      }else{
        $mail->ClearAddresses();
        echo "<script>alert('Error, no se pudo enviar el correo, por favor contacte al administrador: soporte@d-spp.org');location.href ='javascript:history.back()';</script>";
      }*/
      //$mail->Send();
            ///termina envio de mensaje dictamen positivo

    }//incia se envian documento si es una nueva solicitud


  }else{
      $updateSQL = sprintf("UPDATE solicitud_registro SET estatus_interno = %s, estatus_dspp = %s WHERE idsolicitud_registro = %s",
        GetSQLValueString($_POST['estatus_interno'], "int"),
        GetSQLValueString($estatus_dspp, "int"),
        GetSQLValueString($_POST['idsolicitud_registro'], "int"));
      $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

    $insertSQL = sprintf("INSERT INTO proceso_certificacion (idsolicitud_registro, estatus_interno, estatus_dspp, nombre_archivo, accion, archivo, fecha_registro) VALUES (%s, %s, %s, %s, %s, %s, %s)",
      GetSQLValueString($_POST['idsolicitud_registro'], "int"),
      GetSQLValueString($_POST['estatus_interno'], "int"),
      GetSQLValueString($estatus_dspp, "int"),
      GetSQLValueString($nombre_archivo, "text"),
      GetSQLValueString($accion, "text"),
      GetSQLValueString($archivo, "text"),
      GetSQLValueString($fecha, "int"));
    $insertar = mysql_query($insertSQL,$dspp) or die(mysql_error());
  }

  $mensaje = "Se ha actualizado el Proceso de Certificación";
}

if(isset($_POST['cargar_documentos']) && $_POST['cargar_documentos'] == 1){
  $ruta_evaluacion = "../../archivos/ocArchivos/documentos_evaluacion/";

  $estatus_formato = "ENVIADO";
  $estatus_dictamen = "ENVIADO";
  $estatus_informe = "ENVIADO";

  if(!empty($_FILES['formato_evaluacion']['name'])){
      $_FILES["formato_evaluacion"]["name"];
        move_uploaded_file($_FILES["formato_evaluacion"]["tmp_name"], $ruta_evaluacion.$fecha."_".$_FILES["formato_evaluacion"]["name"]);
        $formato = $ruta_evaluacion.basename($fecha."_".$_FILES["formato_evaluacion"]["name"]);
  }else{
    $formato = NULL;
  }
  //insertamos formato_evaluacion
  $insertSQL = sprintf("INSERT INTO formato_evaluacion (idempresa, idsolicitud_registro, estatus_formato, archivo, fecha_registro) VALUES (%s, %s, %s, %s, %s)",
    GetSQLValueString($_POST['idempresa'], "int"),
    GetSQLValueString($_POST['idsolicitud_registro'], "int"),
    GetSQLValueString($estatus_formato, "text"),
    GetSQLValueString($formato, "text"),
    GetSQLValueString($fecha, "int"));
  $insertar = mysql_query($insertSQL,$dspp) or die(mysql_error());

  //insertamos el proceso_certificacion
  $estatus_dspp = 22; //Formato de Evaluación Cargado
  $nombre_archivo = "Formato de Evaluación";
  $insertSQL = sprintf("INSERT INTO proceso_certificacion (idsolicitud_registro, estatus_dspp, nombre_archivo, archivo, fecha_registro) VALUES (%s, %s, %s, %s, %s)",
    GetSQLValueString($_POST['idsolicitud_registro'], "int"),
    GetSQLValueString($estatus_dspp, "int"),
    GetSQLValueString($nombre_archivo, "text"),
    GetSQLValueString($formato, "text"),
    GetSQLValueString($fecha, "int"));
  $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

  if(!empty($_FILES['informe_evaluacion']['name'])){
      $_FILES["informe_evaluacion"]["name"];
        move_uploaded_file($_FILES["informe_evaluacion"]["tmp_name"], $ruta_evaluacion.$fecha."_".$_FILES["informe_evaluacion"]["name"]);
        $informe = $ruta_evaluacion.basename($fecha."_".$_FILES["informe_evaluacion"]["name"]);
  }else{
    $informe = NULL;
  }

  //insertamos informe_evaluacion
  $insertSQL = sprintf("INSERT INTO informe_evaluacion (idempresa, idsolicitud_registro, estatus_informe, archivo, fecha_registro) VALUES (%s, %s, %s, %s, %s)",
    GetSQLValueString($_POST['idempresa'], "int"),
    GetSQLValueString($_POST['idsolicitud_registro'], "int"),
    GetSQLValueString($estatus_informe, "text"),
    GetSQLValueString($informe, "text"),
    GetSQLValueString($fecha, "int"));
  $insertar = mysql_query($insertSQL,$dspp) or die(mysql_error());

  //insertamos el proceso_certificacion
  $estatus_dspp = 20; //Informe de Evaluación Cargado
  $nombre_archivo = "Informe de Evaluación";
  $insertSQL = sprintf("INSERT INTO proceso_certificacion (idsolicitud_registro, estatus_dspp, nombre_archivo, archivo, fecha_registro) VALUES (%s, %s, %s, %s, %s)",
    GetSQLValueString($_POST['idsolicitud_registro'], "int"),
    GetSQLValueString($estatus_dspp, "int"),
    GetSQLValueString($nombre_archivo, "text"),
    GetSQLValueString($informe, "text"),
    GetSQLValueString($fecha, "int"));
  $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

  if(!empty($_FILES['dictamen_evaluacion']['name'])){
      $_FILES["dictamen_evaluacion"]["name"];
        move_uploaded_file($_FILES["dictamen_evaluacion"]["tmp_name"], $ruta_evaluacion.$fecha."_".$_FILES["dictamen_evaluacion"]["name"]);
        $dictamen = $ruta_evaluacion.basename($fecha."_".$_FILES["dictamen_evaluacion"]["name"]);
  }else{
    $dictamen = NULL;
  }

  //insertarmos el dictamen de evaluación
  $insertSQL = sprintf("INSERT INTO dictamen_evaluacion(idempresa, idsolicitud_registro, estatus_dictamen, archivo, fecha_registro) VALUES (%s, %s, %s, %s, %s)",
    GetSQLValueString($_POST['idempresa'], "int"),
    GetSQLValueString($_POST['idsolicitud_registro'], "int"),
    GetSQLValueString($estatus_dictamen, "text"),
    GetSQLValueString($dictamen, "text"),
    GetSQLValueString($fecha, "int"));
  $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

  //se crear el proceso_certificacion
  $estatus_dspp = 21; //Dictamen de Evaluación Cargado
  $nombre_archivo = "Dictamen de Evaluación";
  $insertSQL = sprintf("INSERT INTO proceso_certificacion(idsolicitud_registro, estatus_dspp, nombre_archivo, archivo, fecha_registro) VALUES (%s, %s, %s, %s, %s)",
    GetSQLValueString($_POST['idsolicitud_registro'], "int"),
    GetSQLValueString($estatus_dspp, "int"),
    GetSQLValueString($nombre_archivo, "text"),
    GetSQLValueString($dictamen, "text"),
    GetSQLValueString($fecha, "int"));
  $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

  //inicia enviar correo a ADM sobre documentacion de Evaluación
  $row_informacion = mysql_query("SELECT solicitud_registro.idsolicitud_registro, empresa.nombre AS 'nombre_empresa', empresa.abreviacion AS 'abreviacion_empresa', oc.nombre AS 'nombre_oc' FROM solicitud_registro INNER JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa INNER JOIN oc ON solicitud_registro.idoc = oc.idoc WHERE idsolicitud_registro = $_POST[idsolicitud_registro]", $dspp) or die(mysql_error());
  $informacion = mysql_fetch_assoc($row_informacion);
  $asunto = "D-SPP | Se ha cargado el Formato, Dictamen e Informe de Evaluación";

  $cuerpo_mensaje = '
    <html>
      <head>
        <meta charset="utf-8">
      </head>
      <body>
        <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
          <tbody>
            <tr>
              <th rowspan="2" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
              <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">Revisión Formato, Dictamen e Informe de Evaluación</span></p></th>

            </tr>
            <tr>
             <th scope="col" align="left" width="280"><p>empresa: <span style="color:red">'.$informacion['nombre_empresa'].'</span></p></th>
            </tr>

            <tr>
              <td colspan="2">
               <p>El OC: <span style="color:red">'.$informacion['nombre_oc'].'</span> ha cargado la documentación de evaluación correspondiente al proceso de certificación de la empresa: '.$informacion['nombre_empresa'].' - ('.$informacion['abreviacion_empresa'].')
               <p>
                Por favor proceda a ingresar en su cuenta de ADMINISTRADOR dentro del sistema D-SPP para poder revisar los siguientes documento: 
                   <ul style="color:red">
                     <li>Formato de Evaluación</li>
                     <li>Informe de Evaluación</li>
                     <li>Dictamen de Evaluación</li>
                   </ul>

               </p>
              </td>
            </tr>
            <tr>
              <td coslpan="2">
                <p><span style="color:red">En caso de que los documentos sean aprobados se notificara al OC para que puedar cargar y enviar el certificado.</span></p>
              </td>
            </tr>
          </tbody>
        </table>
      </body>
    </html>
  ';
    $mail->AddAddress($spp_global);
    $mail->AddAttachment($formato);
    $mail->AddAttachment($informe);
    $mail->AddAttachment($dictamen);

    //$mail->Username = "soporte@d-spp.org";
    //$mail->Password = "/aung5l6tZ";
    $mail->Subject = utf8_decode($asunto);
    $mail->Body = utf8_decode($cuerpo_mensaje);
    $mail->MsgHTML(utf8_decode($cuerpo_mensaje));
    $mail->Send();
    $mail->ClearAddresses();

  //termina enviar correo a ADM sobre documentación de Evaluación

  $mensaje = "Se ha enviado el Formato, Dictamen e Informe de Evaluación";

}

if(isset($_POST['enviar_certificado']) && $_POST['enviar_certificado'] == 1){
  $estatus_dspp = 13; //CERTIFICADA
  $rutaArchivo = "../../archivos/ocArchivos/certificados/";
  if(!empty($_FILES['certificado']['name'])){
      $_FILES["certificado"]["name"];
        move_uploaded_file($_FILES["certificado"]["tmp_name"], $rutaArchivo.$fecha."_".$_FILES["certificado"]["name"]);
        $certificado = $rutaArchivo.basename($fecha."_".$_FILES["certificado"]["name"]);
  }else{
    $certificado = NULL;
  }
  //insertamos el certificado
  $fecha_inicio = strtotime($_POST['fecha_inicio']);
  $fecha_fin = strtotime($_POST['fecha_fin']);
  $vigencia_inicio = date('Y-m-d', $fecha_inicio);
  $vigencia_fin = date('Y-m-d', $fecha_fin);

  $insertSQL = sprintf("INSERT INTO certificado(idempresa, idsolicitud_registro, entidad, estatus_certificado, vigencia_inicio, vigencia_fin, archivo, fecha_registro) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
    GetSQLValueString($_POST['idempresa'], "int"),
    GetSQLValueString($_POST['idsolicitud_registro'], "int"),
    GetSQLValueString($_POST['idoc'], "int"),
    GetSQLValueString($estatus_dspp, "int"),
    GetSQLValueString($vigencia_inicio, "text"),
    GetSQLValueString($vigencia_fin, "text"),
    GetSQLValueString($certificado, "text"),
    GetSQLValueString($fecha, "int"));
  $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

  //insertamos el proceso de certificacion
  $estatus_proceso = 12; //es el estatus_dspp (certificado emitido)
  $nombre_archivo = "CERTIFICADO";
  $insertSQL = sprintf("INSERT INTO proceso_certificacion(idsolicitud_registro, estatus_dspp, nombre_archivo, archivo, fecha_registro) VALUES (%s, %s, %s, %s, %s)",
    GetSQLValueString($_POST['idsolicitud_registro'], "int"),
    GetSQLValueString($estatus_proceso, "int"),
    GetSQLValueString($nombre_archivo, "text"),
    GetSQLValueString($certificado, "text"),
    GetSQLValueString($fecha, "int"));
  $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

      $updateSQL = sprintf("UPDATE solicitud_registro SET estatus_interno = %s, estatus_dspp = %s WHERE idsolicitud_registro = %s",
        GetSQLValueString(8, "int"),
        GetSQLValueString($estatus_proceso, "int"),
        GetSQLValueString($_POST['idsolicitud_registro'], "int"));
      $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

  //ACTUALIZAMOS A LA empresa
  $estatus_dspp = 13; //certificada
  $estatus_interno = 8; //dictamen_positivo
  $estatus_publico = 2; //certificado
  $estatus_empresa = "CERTIFICADO";
  $updateSQL = sprintf("UPDATE empresa SET estatus_empresa = %s, estatus_publico = %s, estatus_interno = %s, estatus_dspp = %s WHERE idempresa = %s",
    GetSQLValueString($estatus_empresa, "text"),
    GetSQLValueString($estatus_publico, "int"),
    GetSQLValueString($estatus_interno, "int"),
    GetSQLValueString($estatus_dspp, "int"),
    GetSQLValueString($_POST['idempresa'], "int"));
  $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

  //inicia correo envio de certificado
  $row_informacion = mysql_query("SELECT solicitud_registro.idempresa, solicitud_registro.idoc, oc.email1 AS 'email_oc1', oc.email2 AS 'email_oc2', solicitud_registro.contacto1_email, solicitud_registro.contacto2_email, empresa.nombre AS 'nombre_empresa', empresa.abreviacion AS 'abreviacion_empresa', empresa.email FROM solicitud_registro INNER JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa INNER JOIN oc ON solicitud_registro.idoc = oc.idoc WHERE idsolicitud_registro = $_POST[idsolicitud_registro]", $dspp) or die(mysql_error());
  $informacion = mysql_fetch_assoc($row_informacion);
  $inicio = strtotime($_POST['fecha_inicio']);
  $fin = strtotime($_POST['fecha_fin']);
  $formato_inicio = date('d/m/Y', $inicio);
  $formato_fin = date('d/m/Y', $fin);
  $asunto = "D-SPP | Certificado Disponible para Descargar / Certificate Ready to be Downloaded";

  $cuerpo_mensaje = '
      <html>
        <head>
          <meta charset="utf-8">
        </head>
        <body>
          <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
            <tbody>
              <tr>
                <th rowspan="2" scope="col" align="center" valign="middle
                " width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">Certificado Disponible para descargar / Certificate Ready to be Downloaded</span></p></th>

              </tr>
              <tr>
               <th scope="col" align="left" width="280"><p>empresa: <span style="color:red">'.$informacion['nombre_empresa'].' - ('.$informacion['abreviacion_empresa'].')</span></p></th>
              </tr>

              <tr>
                <td colspan="2">
                 <p>
                  Felicidades!!!, su Certificado ha sido liberado, ahora puede descargarlo.
                 </p>
                 <p>El Certificado tiene un vigencia del dia <span style="color:red">'.$formato_inicio.'</span> al dia: <span style="color:red">'.$formato_fin.'</span>, el cual se encuentra anexo a este correo.</p>
                 
                </td>
              </tr>
              <tr>
                <td colspan="2">
                 <p>
                  Congratulations!!! Your Certificate has been issued and can now be downloaded.
                 </p>
                 <p>
                  The Certificate is in effect from <span style="color:red">'.$formato_inicio.'</span> to <span style="color:red">'.$formato_fin.'</span>, and is attached to this email.
                </td>
              </tr>

              <tr>
                <td colspan="2">
                  <p>Para cualquier duda o aclaración por favor escribir a: <span style="color:red">cert@spp.coop</span> o <span style="color:red">soporte@d-spp.org</span></p>
                </td>
              </tr>

            </tbody>
          </table>
        </body>
      </html>
  ';
    if(!empty($informacion['email'])){
      $mail->AddAddress($informacion['email']);
    }
    if(!empty($informacion['contacto1_email'])){
      $mail->AddAddress($informacion['contacto1_email']);
    }
    if(!empty($informacion['contacto2_email'])){
      $mail->AddAddress($informacion['contacto2_email']);
    }
    if(!empty($informacion['email_oc1'])){
      $mail->AddAddress($informacion['email_oc1']);
    }
    if(!empty($informacion['email2'])){
      $mail->AddAddress($informacion['email2']);
    }

    $mail->AddBCC($spp_global);
    $mail->AddAttachment($certificado);
    //$mail->Username = "soporte@d-spp.org";
    //$mail->Password = "/aung5l6tZ";
    $mail->Subject = utf8_decode($asunto);
    $mail->Body = utf8_decode($cuerpo_mensaje);
    $mail->MsgHTML(utf8_decode($cuerpo_mensaje));
    $mail->Send();
    $mail->ClearAddresses();

  //termina correo envio de certificado

  $mensaje = "Se ha cargado el Certificado y se ha notificado a SPP GLOBAL y a la Organización de Pequeños Productores";
}


$currentPage = $_SERVER["PHP_SELF"];

$maxRows_empresa = 20;
$pageNum_empresa = 0;
if (isset($_GET['pageNum_empresa'])) {
  $pageNum_empresa = $_GET['pageNum_empresa'];
}
$startRow_empresa = $pageNum_empresa * $maxRows_empresa;

//$query = "SELECT solicitud_registro.idsolicitud_registro AS 'idsolicitud', solicitud_registro.tipo_solicitud, solicitud_registro.idempresa, solicitud_registro.idoc, solicitud_registro.fecha_registro, solicitud_registro.fecha_aceptacion, solicitud_registro.cotizacion_empresa, solicitud_registro.contacto1_email, solicitud_registro.contacto2_email, empresa.nombre AS 'nombre_empresa', empresa.abreviacion AS 'abreviacion_empresa', empresa.email, proceso_certificacion.idproceso_certificacion, proceso_certificacion.estatus_publico, proceso_certificacion.estatus_interno, proceso_certificacion.estatus_dspp, estatus_publico.nombre AS 'nombre_publico', estatus_interno.nombre AS 'nombre_interno', estatus_dspp.nombre AS 'nombre_dspp', periodo_objecion.*, membresia.idmembresia, membresia.estatus_membresia, contratos.idcontrato, contratos.estatus_contrato, certificado.idcertificado, formato_evaluacion.idformato_evaluacion, dictamen_evaluacion.iddictamen_evaluacion, informe_evaluacion.idinforme_evaluacion FROM solicitud_registro INNER JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa LEFT JOIN proceso_certificacion ON solicitud_registro.idsolicitud_registro = proceso_certificacion.idsolicitud_registro LEFT JOIN estatus_publico ON proceso_certificacion.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON proceso_certificacion.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON proceso_certificacion.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN periodo_objecion ON solicitud_registro.idsolicitud_registro = periodo_objecion.idsolicitud_registro LEFT JOIN membresia ON solicitud_registro.idsolicitud_registro = membresia.idsolicitud_registro LEFT JOIN contratos ON solicitud_registro.idsolicitud_registro = contratos.idsolicitud_registro LEFT JOIN certificado ON solicitud_registro.idempresa = certificado.idempresa LEFT JOIN dictamen_evaluacion ON solicitud_registro.idsolicitud_registro = dictamen_evaluacion.idsolicitud_registro LEFT JOIN informe_evaluacion ON solicitud_registro.idsolicitud_registro = informe_evaluacion.idsolicitud_registro LEFT JOIN formato_evaluacion ON solicitud_registro.idsolicitud_registro = formato_evaluacion.idsolicitud_registro WHERE solicitud_registro.idoc = $idoc ORDER BY proceso_certificacion.idproceso_certificacion DESC";

$query = "SELECT solicitud_registro.*, oc.abreviacion AS 'abreviacionOC', empresa.nombre AS 'nombre_empresa', empresa.abreviacion AS 'abreviacion_empresa', empresa.email, periodo_objecion.idperiodo_objecion, periodo_objecion.fecha_inicio, periodo_objecion.fecha_fin, periodo_objecion.estatus_objecion, periodo_objecion.observacion, periodo_objecion.dictamen, periodo_objecion.documento, membresia.idmembresia, certificado.idcertificado, contratos.idcontrato, contratos.estatus_contrato, formato_evaluacion.idformato_evaluacion, formato_evaluacion.estatus_formato, dictamen_evaluacion.iddictamen_evaluacion, dictamen_evaluacion.estatus_dictamen, informe_evaluacion.idinforme_evaluacion, informe_evaluacion.estatus_informe FROM solicitud_registro INNER JOIN oc ON solicitud_registro.idoc = oc.idoc INNER JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa LEFT JOIN periodo_objecion ON solicitud_registro.idsolicitud_registro  = periodo_objecion.idsolicitud_registro LEFT JOIN membresia ON solicitud_registro.idsolicitud_registro = membresia.idsolicitud_registro LEFT JOIN certificado ON solicitud_registro.idempresa = certificado.idempresa LEFT JOIN contratos ON solicitud_registro.idsolicitud_registro = contratos.idsolicitud_registro LEFT JOIN formato_evaluacion ON solicitud_registro.idsolicitud_registro = formato_evaluacion.idsolicitud_registro LEFT JOIN dictamen_evaluacion ON solicitud_registro.idsolicitud_registro = dictamen_evaluacion.idsolicitud_registro LEFT JOIN informe_evaluacion ON solicitud_registro.idsolicitud_registro = informe_evaluacion.idsolicitud_registro WHERE solicitud_registro.idoc = $idoc GROUP BY solicitud_registro.idsolicitud_registro ORDER BY solicitud_registro.idsolicitud_registro DESC";

$row_solicitud = mysql_query($query,$dspp) or die(mysql_error());

?>

<div class="row">
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

  <div class="col-md-12">
    <table class="table table-condensed table-bordered" style="font-size:12px;">
      <thead>
        <tr class="success">
          <th class="text-center">ID</th>
          <th class="text-center"><a href="#" data-toggle="tooltip" title="Tipo de Solicitud"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>Tipo</a></th>
          <th class="text-center">Fecha Solicitud</th>
          <th class="text-center">Organización</th>
          <th class="text-center">Estatus Solicitud</th>
          <th class="text-center">Cotización <br>(Descargable)</th>
          <!--<th class="text-center">Sitio WEB</th>-->
          <!--<th class="text-center">Contacto</th>-->
          <!--<th class="text-center">País</th>-->
          <th class="text-center">Proceso de Objeción</th>
          <th class="text-center">Proceso <br>Certificación</th>
          <th class="text-center">Certificado</th>
          <!--<th class="text-center">Propuesta</th>-->
          <!--<th class="text-center">Observaciones Solicitud</th>-->
          <th class="text-center" colspan="2">Acciones</th>
          <!--<th>OC</th>
          <th>Razón social</th>
          <th>Dirección fiscal</th>
          <th>RFC</th>-->
          <!--<th>Eliminar</th>-->
        </tr>       
      </thead>
      <tbody>
      <?php 
      while($solicitud = mysql_fetch_assoc($row_solicitud)){
          $query_proceso = "SELECT proceso_certificacion.*, proceso_certificacion.idsolicitud_registro, estatus_publico.idestatus_publico, estatus_publico.nombre AS 'nombre_publico', estatus_interno.idestatus_interno, estatus_interno.nombre AS 'nombre_interno', estatus_dspp.idestatus_dspp, estatus_dspp.nombre AS 'nombre_dspp', membresia.idmembresia, membresia.estatus_membresia, membresia.idcomprobante_pago, membresia.fecha_registro FROM proceso_certificacion LEFT JOIN estatus_publico ON proceso_certificacion.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON proceso_certificacion.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON proceso_certificacion.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN membresia ON proceso_certificacion.idsolicitud_registro = membresia.idsolicitud_registro WHERE proceso_certificacion.idsolicitud_registro =  $solicitud[idsolicitud_registro] ORDER BY proceso_certificacion.idproceso_certificacion DESC LIMIT 1";
          $ejecutar = mysql_query($query_proceso,$dspp) or die(mysql_error());
          $proceso_certificacion = mysql_fetch_assoc($ejecutar);

      ?>
        <tr <?php if($proceso_certificacion['estatus_dspp'] == 12){ echo "class='success'"; } ?>>
          <td>
            <?php echo $solicitud['idsolicitud_registro']; ?>
          </td>
          <td <?php if($solicitud['tipo_solicitud'] == 'NUEVA'){ echo "class='success'"; }else{ echo "class='warning'"; } ?>class="warning">
            <?php echo $solicitud['tipo_solicitud']; ?>
          </td>
          <td>
            <?php echo date('d/m/Y', $solicitud['fecha_registro']); ?>
          </td>
          <td>
            <?php echo $solicitud['abreviacion_empresa']; ?>
          </td>
          <td>
            <?php 
            if(isset($proceso_certificacion['estatus_dspp'])){
              echo $proceso_certificacion['nombre_dspp'];
            }else{
              echo "No Disponible";
            }
             ?>
          </td>


          <td>
            <?php
            if(isset($solicitud['cotizacion_empresa'])){
            ?>
              <div class="btn-group" role="group" aria-label="...">
                <button type="button" class="btn btn-sm btn-default" data-toggle="modal" data-target="<?php echo "#cotizacion".$solicitud['idsolicitud_registro']; ?>" title="Reemplazar Cotización"><span class="glyphicon glyphicon-refresh" aria-hidden="true"></span></button>
                <a href='<?php echo $solicitud['cotizacion_empresa']; ?>' class='btn btn-sm btn-success' style='font-size:12px;color:white;height:30px;' target='_blank'><span class='glyphicon glyphicon-download' aria-hidden='true'></span> Descargar Cotización</a>
              </div>
            <?php
               //echo "<a class='btn btn-success form-control' style='font-size:12px;color:white;height:30px;' href='".$solicitud['cotizacion_empresa']."' target='_blank'><span class='glyphicon glyphicon-download' aria-hidden='true'></span> Descargar Cotización</a>";
               if($proceso_certificacion['estatus_dspp'] == 5){ // SE ACEPTA LA COTIZACIÓN
                echo "<p class='alert alert-success' style='padding:7px;'>Estatus: ".$proceso_certificacion['nombre_dspp']."</p>"; 
               }else if($proceso_certificacion['estatus_dspp'] == 17){ // SE RECHAZA LA COTIZACIÓN
                echo "<p class='alert alert-danger' style='padding:7px;'>Estatus: ".$proceso_certificacion['nombre_dspp']."</p>"; 
               }else{
                echo "<p class='alert alert-info' style='padding:7px;'>Estatus: ".$proceso_certificacion['nombre_dspp']."</p>"; 
               }

            }else{ // INICIA CARGAR COTIZACIÓN
              echo "No Disponible";
            } // TERMINA CARGAR COTIZACIÓN
             ?>

            <!-- Modal -->
            <div class="modal fade" id="<?php echo "cotizacion".$solicitud['idsolicitud_registro']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
              <div class="modal-dialog" role="document">
                <form action="" method="POST" enctype="multipart/form-data">
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      <h4 class="modal-title" id="myModalLabel">Reemplazar Cotización Actual</h4>
                    </div>
                    <div class="modal-body">
                      <div class="form-group">
                        <label for="nueva_cotizacion">Nueva Cotización</label>
                        <input type="file" id="nueva_cotizacion" name="nueva_cotizacion">
                        <input type="hidden" name="cotizacion_actual" value="<?php echo $solicitud['cotizacion_empresa']; ?>">
                        <input type="hidden" name="idsolicitud_registro" value="<?php echo $solicitud['idsolicitud_registro']; ?>">
                      </div>
                    </div>

                    <div class="modal-footer">
                      <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                      <button type="submit" name="reemplazar_cotizacion" value="1" class="btn btn-primary">Reemplazar Cotización</button>
                    </div>                    

                  </div>
                </form>
              </div>
            </div>

          </td>

          <td>
            <?php
            if($solicitud['tipo_solicitud'] == 'RENOVACION'){
            ?>
              <a href="#" data-toggle="tooltip" title="Esta solicitud se encuentra en Proceso de Renovación del Registro por lo tanto no aplica el periodo de objeción" style="padding:7px;"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>SOLICITUD EN RENOVACIÓN</a>
            <?php
            }else{
              if(isset($solicitud['idperiodo_objecion']) && $solicitud['estatus_objecion'] != 'EN ESPERA'){
              ?>
                <p class="alert alert-info" style="margin-bottom:0;padding:0px;">Inicio: <?php echo date('d/m/Y', $solicitud['fecha_inicio']); ?></p>
                <p class="alert alert-danger" style="margin-bottom:0;padding:0px;">Fin: <?php echo date('d/m/Y', $solicitud['fecha_fin']); ?></p>
                <?php 
                if(isset($solicitud['documento'])){
                ?>
                  <p class="alert alert-success" style="margin-bottom:0;padding:0px;">Resolución: <?php echo $solicitud['dictamen']; ?></p>
                  <a class="btn btn-info" style="font-size:12px;width:100%;height:30px;" href='<?php echo $solicitud['documento']; ?>' target='_blank'><span class='glyphicon glyphicon-download' aria-hidden='true'></span> Descargar Resolución</a> 
                <?php
                }
                ?>
              <?php
              }else{
                echo "No Disponible";
              }
            }
            ?>
          </td>
          <!---- INICIA PROCESO DE CERTIFICACIÓN ---->
          <td>
            <form action="" method="POST" enctype="multipart/form-data">
              <?php 
              if((isset($solicitud['dictamen']) && $solicitud['dictamen'] == 'POSITIVO') || ($solicitud['tipo_solicitud'] == 'RENOVACION' && !empty($solicitud['fecha_aceptacion']))){
              ?>
                <button type="button" class="btn btn-sm btn-primary" style="width:100%" data-toggle="modal" data-target="<?php echo "#certificacion".$solicitud['idsolicitud_registro']; ?>">Proceso Certificación</button>
                <!-- inicia modal proceso de certificación -->
                <div id="<?php echo "certificacion".$solicitud['idsolicitud_registro']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                  <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Proceso de Certificación</h4>
                      </div>
                      <div class="modal-body">
                        
                        <div class="row">
                          <?php 

                          $row_proceso_certificacion = mysql_query("SELECT proceso_certificacion.*, estatus_interno.nombre FROM proceso_certificacion INNER JOIN estatus_interno ON proceso_certificacion.estatus_interno = estatus_interno.idestatus_interno WHERE proceso_certificacion.idsolicitud_registro = '$solicitud[idsolicitud_registro]' AND proceso_certificacion.estatus_interno IS NOT NULL", $dspp) or die(mysql_error());
                          while($historial_proceso = mysql_fetch_assoc($row_proceso_certificacion)){
                            echo "<div class='col-md-10'>Proceso: $historial_proceso[nombre]</div>";
                            echo "<div class='col-md-2'>Fecha: ".date('d/m/Y',$historial_proceso['fecha_registro'])."</div>";
                          }

                          if(!isset($solicitud['idcomprobante_pago'])){
                          ?>
                          <div class="col-md-12">
                            <select class="form-control" name="estatus_interno" id="<?php echo 'statusSelect'.$solicitud['idsolicitud_registro']; ?>" onchange="<?php echo 'funcionSelect'.$solicitud['idsolicitud_registro'].'()'; ?>">
                              <option value="">Seleccione el proceso en el que se encuentra</option>
                              <?php 
                              $row_estatus_interno = mysql_query("SELECT * FROM estatus_interno",$dspp) or die(mysql_error());
                              while($estatus_interno = mysql_fetch_assoc($row_estatus_interno)){
                                echo "<option value='$estatus_interno[idestatus_interno]'>$estatus_interno[nombre]</option>";
                              }
                               ?>
                            </select>                        
                          </div>
                          <?php
                          }
                          ?>

                              <div class="col-xs-12" id="<?php echo 'divSelect'.$solicitud['idsolicitud_registro']; ?>" style="margin-top:10px;">
                                <div class="col-xs-6">
                                  <input style="display:none" id="<?php echo 'nombreArchivo'.$solicitud['idsolicitud_registro']; ?>" type='text' class='form-control' name='nombre_archivo' placeholder="Nombre del Archivo"/>
                                </div>
                                <!-- INICIA CARGAR ARCHIVO ESTATUS -->
                                <div class="col-xs-6">
                                  <input style="display:none" id="<?php echo 'archivo_estatus'.$solicitud['idsolicitud_registro']; ?>" type='file' class='form-control' name='archivo_estatus' />
                                </div>
                                <!-- TERMINA CARGAR ARCHIVO ESTATUS -->

                                <!-- INICIA ACCION PROCESO CERTIFICACION -->
                                <textarea class="form-control" id="<?php echo 'registroOculto'.$solicitud['idsolicitud_registro']; ?>" style="display:none" name="accion" cols="30" rows="10" value="" placeholder="Escribe aquí"></textarea>
                                <!-- TERMINA ACCION PROCESO CERTIFICACION -->
                              </div>

                              <div id="<?php echo 'tablaCorreo'.$solicitud['idsolicitud_registro']; ?>" style="display:none">
                                <div class="col-xs-12"  style="margin-top:10px;">
                                  <?php 
                                  if($solicitud['tipo_solicitud'] == 'RENOVACION'){
                                    echo '<h5 style="font-size:15px;" class="alert alert-warning">
                                    <ul>
                                      <li>Está empresa se encuentra en "Proceso de Renovación del Registro", por lo tanto no se enviara "Contrato de Uso".</li>
                                      <li>Solo las empresas nuevas que no cuentan con registro se les enviara "Contrato de uso.</li>
                                      <li>Solo de clic en "Guardar Proceso" para notificar a SPP Global.</li>
                                    </ul>
                                    </h5>';
                                  ?>
                                      <p class="alert alert-info">El siguiente formato sera enviado en breve al empresa</p>
                                      <div class="col-xs-12">
                                        
                                        <div class="col-xs-12">
                                          <div class="col-xs-6"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></div>
                                          <div class="col-xs-6">
                                            <p>Enviado el: <?php echo date("d/m/Y", time()); ?></p>
                                            <p>Para: <span style="color:red"><?php echo $solicitud['nombre_empresa']; ?></span></p>
                                            <p>Correo(s): <?php echo $solicitud['contacto1_email']." , ".$solicitud['contacto2_email']." , ".$solicitud['email']; ?></p>
                                            <p>Asunto: <span style="color:red">Notificación del Dictamen - SPP</span></p>
                                            
                                          </div>
                                        </div>
                                        <div class="col-xs-12">
                                          <h4 style="font-size:14px;padding:5px;">MENSAJE EMPRESA( <small style="font-size:13px;">EL OC debe escribir  en el campo debajo, el texto sobre la Notificación del dictamen y en caso de que el dictamen sea positivo, debe explicar que el actor debe leer los  documentos anexos y firmar el Contrato de Uso y Acuse de Recibo. <span style="color:red">Si no escribe ningun mensaje el sistema mandara un mensaje predeterminado <a href="dictamen_positivo.php?empresa=<?php echo $solicitud['nombre_empresa'];?>" target="ventana1" onclick="ventanaNueva ('', 500, 400, 'ventana1');">ver mensaje</a></span></small>)</h4>
                                          <textarea name="mensaje_empresa" class="form-control textareaMensaje" id="" cols="30" rows="10" placeholder="Ingrese un mensaje en caso de que lo deseé"></textarea>

                                        </div>
                                      </div>
                                      <div class="col-xs-12">

                                        <div class="col-xs-12">
                                          <h4 style="font-size:14px;">ARCHIVO EXTRA: <span style="color:#7f8c8d">Anexar algun otro archivo en caso de ser requerido.</span></h4>
                                          <div class="col-xs-12">
                                            <input type="text" class="form-control" name="nombreArchivo" placeholder="Nombre Archivo">
                                          </div>
                                          <div class="col-xs-12">
                                            <input type="file" class="form-control" name="archivo_extra">
                                          </div>
                                        </div>
                                      </div>
                                  <?php
                                  }else{
                                  ?>
                                      <p class="alert alert-info">El siguiente formato sera enviado en breve al empresa</p>
                                      <div class="col-xs-12">
                                        
                                        <div class="col-xs-12">
                                          <div class="col-xs-6"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></div>
                                          <div class="col-xs-6">
                                            <p>Enviado el: <?php echo date("d/m/Y", time()); ?></p>
                                            <p>Para: <span style="color:red"><?php echo $solicitud['nombre_empresa']; ?></span></p>
                                            <p>Correo(s): <?php echo $solicitud['contacto1_email']." , ".$solicitud['contacto2_email']." , ".$solicitud['email']; ?></p>
                                            <p>Asunto: <span style="color:red">Notificación del Dictamen - SPP</span></p>
                                            
                                          </div>
                                        </div>
                                        <div class="col-xs-12">
                                          <h4 style="font-size:14px;padding:5px;">MENSAJE EMPRESA( <small style="font-size:13px;">EL OC debe escribir  en el campo debajo, el texto sobre la Notificación del dictamen y en caso de que el dictamen sea positivo, debe explicar que el actor debe leer los  documentos anexos y firmar el Contrato de Uso y Acuse de Recibo. <span style="color:red">Si no escribe ningun mensaje el sistema mandara un mensaje predeterminado <a href="dictamen_positivo.php?empresa=<?php echo $solicitud['nombre_empresa'];?>" target="ventana1" onclick="ventanaNueva ('', 500, 400, 'ventana1');">ver mensaje</a></span></small>)</h4>
                                          <textarea name="mensaje_empresa" class="form-control textareaMensaje" id="" cols="30" rows="10" placeholder="Ingrese un mensaje en caso de que lo deseé"></textarea>

                                        </div>
                                      </div>
                                      <div class="col-xs-12">
                                        <div class="col-xs-12">
                                          <h4 style="font-size:14px;">ARCHIVOS ADJUNTOS: <span style="color:#7f8c8d">Documentación enviada al actor una vez que ha finalizado el Proceso de Certificación con un dictamen Positivo.</span></h4>
                                          <?php 
                                          $row_documentacion = mysql_query("SELECT * FROM documentacion WHERE idestatus_interno = 8 AND documentacion.nombre != 'Datos Bancarios SPP'", $dspp) or die(mysql_error());
                                          while($documetacion = mysql_fetch_assoc($row_documentacion)){

                                            echo "<span class='glyphicon glyphicon-ok' aria-hidden='true'></span> <a href='$documetacion[archivo]' target='_blank'>$documetacion[nombre]</a><br>";
                                          }
                                           ?>
                                          <p class="alert alert-warning" style="padding:5px;">
                                            Seleccione el idioma en el que desea enviar los archivos:
                                            <label class="radio-inline">
                                              <input type="radio" name="idioma" id="inlineRadio1" value="ESP"> Español
                                            </label>
                                            <label class="radio-inline">
                                              <input type="radio" name="idioma" id="inlineRadio2" value="EN"> Ingles
                                            </label>
                                          </p>
        
                                        </div>
                                        <div class="col-xs-12">
                                          <h4 style="font-size:14px;">ARCHIVO EXTRA: <span style="color:#7f8c8d">Anexar algun otro archivo en caso de ser requerido.</span></h4>
                                          <div class="col-xs-12">
                                            <input type="text" class="form-control" name="nombreArchivo" placeholder="Nombre Archivo">
                                          </div>
                                          <div class="col-xs-12">
                                            <input type="file" class="form-control" name="archivo_extra">
                                          </div>
                                        </div>
                                      </div>
                                  <?php
                                  }
                                   ?>
                                </div>
                              </div>

                        </div>

                      <?php
                          echo "<script>";
                          echo "function funcionSelect".$solicitud['idsolicitud_registro']."() {";
                            echo "var valorSelect = document.getElementById('statusSelect".$solicitud['idsolicitud_registro']."').value;";
                            echo "if(valorSelect == '4'){";
                              echo "document.getElementById('divSelect".$solicitud['idsolicitud_registro']."').style.display = 'block';";
                              echo "document.getElementById('registroOculto".$solicitud['idsolicitud_registro']."').style.display = 'block';";
                              echo "document.getElementById('nombreArchivo".$solicitud['idsolicitud_registro']."').style.display = 'block';";
                              echo "document.getElementById('archivo_estatus".$solicitud['idsolicitud_registro']."').style.display = 'block';";
                              echo "document.getElementById('tablaCorreo".$solicitud['idsolicitud_registro']."').style.display = 'none';";
                              echo "document.getElementById('boton1".$solicitud['idsolicitud_registro']."').style.display = 'block';";
                              echo "document.getElementById('boton2".$solicitud['idsolicitud_registro']."').style.display = 'none';";

                            echo "}else if(valorSelect == '6'){";
                              echo "document.getElementById('divSelect".$solicitud['idsolicitud_registro']."').style.display = 'block';";
                              echo "document.getElementById('nombreArchivo".$solicitud['idsolicitud_registro']."').style.display = 'block';";
                              echo "document.getElementById('archivo_estatus".$solicitud['idsolicitud_registro']."').style.display = 'block';";
                              echo "document.getElementById('registroOculto".$solicitud['idsolicitud_registro']."').style.display = 'block';";
                              echo "document.getElementById('tablaCorreo".$solicitud['idsolicitud_registro']."').style.display = 'none';";
                              echo "document.getElementById('boton1".$solicitud['idsolicitud_registro']."').style.display = 'block';";
                              echo "document.getElementById('boton2".$solicitud['idsolicitud_registro']."').style.display = 'none';";

                            echo "}else if(valorSelect == '7'){";
                              echo "document.getElementById('divSelect".$solicitud['idsolicitud_registro']."').style.display = 'block';";
                              echo "document.getElementById('nombreArchivo".$solicitud['idsolicitud_registro']."').style.display = 'none';";
                              echo "document.getElementById('archivo_estatus".$solicitud['idsolicitud_registro']."').style.display = 'none';";
                              echo "document.getElementById('registroOculto".$solicitud['idsolicitud_registro']."').style.display = 'block';";
                              echo "document.getElementById('tablaCorreo".$solicitud['idsolicitud_registro']."').style.display = 'none';";
                              echo "document.getElementById('boton1".$solicitud['idsolicitud_registro']."').style.display = 'block';";
                              echo "document.getElementById('boton2".$solicitud['idsolicitud_registro']."').style.display = 'none';";

                            echo "}else if(valorSelect == '8'){";
                              echo "document.getElementById('tablaCorreo".$solicitud['idsolicitud_registro']."').style.display = 'block';"; 
                              echo "document.getElementById('nombreArchivo".$solicitud['idsolicitud_registro']."').style.display = 'none';";
                              echo "document.getElementById('archivo_estatus".$solicitud['idsolicitud_registro']."').style.display = 'none';";
                              echo "document.getElementById('registroOculto".$solicitud['idsolicitud_registro']."').style.display = 'none';";
                              echo "document.getElementById('boton1".$solicitud['idsolicitud_registro']."').style.display = 'none';";
                              echo "document.getElementById('boton2".$solicitud['idsolicitud_registro']."').style.display = 'block';";
                            echo "}else if(valorSelect == '9'){";
                              echo "document.getElementById('divSelect".$solicitud['idsolicitud_registro']."').style.display = 'block';";
                              echo "document.getElementById('tablaCorreo".$solicitud['idsolicitud_registro']."').style.display = 'none';"; 
                              echo "document.getElementById('nombreArchivo".$solicitud['idsolicitud_registro']."').style.display = 'block';";
                              echo "document.getElementById('archivo_estatus".$solicitud['idsolicitud_registro']."').style.display = 'block';";
                              echo "document.getElementById('registroOculto".$solicitud['idsolicitud_registro']."').style.display = 'block';"; 
                              echo "document.getElementById('boton1".$solicitud['idsolicitud_registro']."').style.display = 'block';";
                              echo "document.getElementById('boton2".$solicitud['idsolicitud_registro']."').style.display = 'none';";

                            echo "}else if(valorSelect == '23'){";
                              echo "document.getElementById('divSelect".$solicitud['idsolicitud_registro']."').style.display = 'block';";
                              echo "document.getElementById('tablaCorreo".$solicitud['idsolicitud_registro']."').style.display = 'none';";
                              echo "document.getElementById('nombreArchivo".$solicitud['idsolicitud_registro']."').style.display = 'none';"; 
                              echo "document.getElementById('archivo_estatus".$solicitud['idsolicitud_registro']."').style.display = 'none';";
                              echo "document.getElementById('registroOculto".$solicitud['idsolicitud_registro']."').style.display = 'block';";
                              echo "document.getElementById('boton1".$solicitud['idsolicitud_registro']."').style.display = 'block';";
                              echo "document.getElementById('boton2".$solicitud['idsolicitud_registro']."').style.display = 'none';";

                            echo "}else{";
                              echo "document.getElementById('nombreArchivo".$solicitud['idsolicitud_registro']."').style.display = 'none';";
                              echo "document.getElementById('archivo_estatus".$solicitud['idsolicitud_registro']."').style.display = 'none';";
                              echo "document.getElementById('registroOculto".$solicitud['idsolicitud_registro']."').style.display = 'none';";
                              echo "document.getElementById('tablaCorreo".$solicitud['idsolicitud_registro']."').style.display = 'none';";
                              echo "document.getElementById('divSelect".$solicitud['idsolicitud_registro']."').style.display = 'none';";
                              echo "document.getElementById('boton1".$solicitud['idsolicitud_registro']."').style.display = 'block';";
                              echo "document.getElementById('boton2".$solicitud['idsolicitud_registro']."').style.display = 'none';";

                            echo "}";
                          echo "}";
                          echo "</script>";
                      ?>

                      </div>
                      <div class="modal-footer">
                        <input type="hidden" name="tipo_solicitud" value="<?php echo $solicitud['tipo_solicitud']; ?>">
                        <input type="hidden" name="idperiodo_objecion" value="<?php echo $solicitud['idperiodo_objecion']; ?>">
                        <input type="hidden" name="idoc" value="<?php echo $solicitud['idoc']; ?>">
                        <input type="hidden" name="idempresa" value="<?php echo $solicitud['idempresa']; ?>">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                        <?php 
                        if($solicitud['tipo_solicitud'] == 'RENOVACION'){
                          if(!empty($solicitud['idformato_evaluacion']) || !empty($solicitud['idinforme_evaluacion']) || !empty($solicitud['iddictamen_evaluacion'])){

                          }else{
                            if($proceso_certificacion['estatus_interno'] != 8){
                              echo '<button type="submit" class="btn btn-success" name="guardar_proceso" value="1">Guardar Proceso</button>';
                            }
                          }
                        }else{
                          if($proceso_certificacion['estatus_interno'] != 8){
                          ?>
                            <button type="submit" class="btn btn-success" style="width:100%" id="<?php echo 'boton1'.$solicitud['idsolicitud_registro']; ?>" name="guardar_proceso" value="1">Guardar Proceso</button>
                            <button type="submit" class="btn btn-success" id="<?php echo 'boton2'.$solicitud['idsolicitud_registro']; ?>" name="guardar_proceso" value="1" onclick="return validar()" style="width:100%; display:none" >Enviar Dictamen</button>
                          <?php
                          }
                        }                        
                        ?>
                        <!--<button type="button" class="btn btn-primary">Guardar Cambios</button>-->
                      </div>
                    </div>
                  </div>
                </div>
                <!-- termina modal proceso de certificación -->

              <?php
              }else{
                echo "No Disponible";
              }
               ?>
               <input type="hidden" name="idsolicitud_registro" value="<?php echo $solicitud['idsolicitud_registro']; ?>">
            </form>
          </td>
          <!---- TERMINA PROCESO DE CERTIFICACIÓN ---->

          <!---- INICIA SECCION CERTIFICADO ------>
          <form action="" method="POST" enctype="multipart/form-data">
          <td>
            <?php /*
            if(isset($solicitud['iddictamen_evaluacion'])){
            ?>
            <button type="button" class="btn btn-sm btn-info" style="width:100%" data-toggle="modal" data-target="<?php echo "#certificado".$solicitud['idsolicitud_registro']; ?>">Cargar Certificado</button>
            <?php
            }else{
            ?>
            <button type="button" class="btn btn-sm btn-default" style="width:100%" disabled>Cargar Certificado</button>
            <?php
            }*/
            ?>
                <button type="button" class="btn btn-sm btn-info" style="width:100%" data-toggle="modal" data-target="<?php echo "#certificado".$solicitud['idsolicitud_registro']; ?>">Cargar Certificado</button>

          </td>
                <!-- inicia modal estatus_Certificado -->

                <div id="<?php echo "certificado".$solicitud['idsolicitud_registro']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                  <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="myModalLabel">Cargar Certificado

                      </div>
                      <div class="modal-body">
                        <div class="row">
                          <div class="col-md-6">

                            <?php 
                            if($solicitud['tipo_solicitud'] == 'RENOVACION'){
                              if($proceso_certificacion['estatus_interno'] == "8" || !empty($solicitud['idformato_evaluacion']) || !empty($solicitud['idinforme_evaluacion']) || !empty($solicitud['iddictamen_evaluacion'])){
                                if(isset($solicitud['idformato_evaluacion']) && isset($solicitud['idinforme_evaluacion']) && isset($solicitud['iddictamen_evaluacion'])){

                                  $row_formato = mysql_query("SELECT * FROM formato_evaluacion WHERE idformato_evaluacion = $solicitud[idformato_evaluacion]", $dspp) or die(mysql_error());
                                  $formato = mysql_fetch_assoc($row_formato);
                                  $row_informe = mysql_query("SELECT * FROM informe_evaluacion WHERE idinforme_evaluacion = $solicitud[idinforme_evaluacion]", $dspp) or die(mysql_error());
                                  $informe = mysql_fetch_assoc($row_informe);
                                  $row_dictamen = mysql_query("SELECT * FROM dictamen_evaluacion WHERE iddictamen_evaluacion = $solicitud[iddictamen_evaluacion]", $dspp) or die(mysql_error());
                                  $dictamen = mysql_fetch_assoc($row_dictamen);

                                ?>
                                <p>Estatus Formato de Evaluación: <span style="color:red"><?php echo $formato['estatus_formato']; ?></span></p>
                                <a href="<?php echo $formato['archivo']; ?>" class="btn btn-info" style="width:100%" target="_blank">Descargar Formato de Evaluación</a>

                                <p>Estatus Dictamen de Evaluación: <span style="color:red"><?php echo $dictamen['estatus_dictamen']; ?></span></p>
                                <a href="<?php echo $dictamen['archivo']; ?>" class="btn btn-info" style="width:100%" target="_blank">Descargar Dictamen</a>
                                <p>Estatus Informe de Evaluación: <span style="color:red"><?php echo $informe['estatus_informe']; ?></span></p>
                                <a href="<?php echo $informe['archivo']; ?>" class="btn btn-info" style="width:100%" target="_blank">Descargar Informe</a>

                                <?php
                                }else{
                                ?>
                                  <p class="alert alert-info">Por favor cargue los siguientes documentos:</p>
                                  <p class="alert alert-info">

                                    Formato de Evaluación
                                    <input type="file" class="form-control" name="formato_evaluacion" required>

                                    Informe de Evaluación
                                    <input type="file" class="form-control" name="informe_evaluacion" required>
                       
                                    Dictamen de Evaluación
                                    <input type="file" class="form-control" name="dictamen_evaluacion" required>

                                  </p>
                                  <button type="submit" class="btn btn-success" style="width:100%" name="cargar_documentos" value="1">Enviar Documentos</button>
                                <?php
                                }

                              }

                            }else{
                              if($solicitud['estatus_contrato'] == "ACEPTADO"){
                                if(!empty($solicitud['idformato_evaluacion']) && !empty($solicitud['idinforme_evaluacion']) && !empty($solicitud['iddictamen_evaluacion'])){

                                  $row_formato = mysql_query("SELECT * FROM formato_evaluacion WHERE idformato_evaluacion = $solicitud[idformato_evaluacion]", $dspp) or die(mysql_error());
                                  $formato = mysql_fetch_assoc($row_formato);
                                  $row_informe = mysql_query("SELECT * FROM informe_evaluacion WHERE idinforme_evaluacion = $solicitud[idinforme_evaluacion]", $dspp) or die(mysql_error());
                                  $informe = mysql_fetch_assoc($row_informe);
                                  $row_dictamen = mysql_query("SELECT * FROM dictamen_evaluacion WHERE iddictamen_evaluacion = $solicitud[iddictamen_evaluacion]", $dspp) or die(mysql_error());
                                  $dictamen = mysql_fetch_assoc($row_dictamen);

                                ?>
                                <p>Estatus Formato de Evaluación: <span style="color:red"><?php echo $formato['estatus_formato']; ?></span></p>
                                <a href="<?php echo $formato['archivo']; ?>" class="btn btn-info" style="width:100%" target="_blank">Descargar Formato de Evaluación</a>

                                <p>Estatus Dictamen de Evaluación: <span style="color:red"><?php echo $dictamen['estatus_dictamen']; ?></span></p>
                                <a href="<?php echo $dictamen['archivo']; ?>" class="btn btn-info" style="width:100%" target="_blank">Descargar Dictamen</a>
                                <p>Estatus Informe de Evaluación: <span style="color:red"><?php echo $informe['estatus_informe']; ?></span></p>
                                <a href="<?php echo $informe['archivo']; ?>" class="btn btn-info" style="width:100%" target="_blank">Descargar Informe</a>

                                <?php
                                }else{
                                ?>
                                  <p class="alert alert-info">Por favor cargue los siguientes documentos:</p>
                                  <p class="alert alert-info">

                                    Formato de Evaluación
                                    <input type="file" class="form-control" name="formato_evaluacion" required>

                                    Informe de Evaluación
                                    <input type="file" class="form-control" name="informe_evaluacion" required>
                       
                                    Dictamen de Evaluación
                                    <input type="file" class="form-control" name="dictamen_evaluacion" required>

                                  </p>
                                  <button type="submit" class="btn btn-success" style="width:100%" name="cargar_documentos" value="1">Enviar Documentos</button>
                                <?php
                                }

                              }else{
                                echo "<p class='alert alert-danger'>Aun no se ha \"Aprobado\" el \"Contrato de Uso\" ni se ha \"Aprobado\" la membresia</p>";
                              }
          
                            }
                            ?>
                          </div>
                          
                          <div <?php if($solicitud['tipo_solicitud'] == 'RENOVACION'){echo 'class="col-md-6"'; }else{ echo 'class="col-md-6"';} ?>>
                            <h4 style="font-size:14px;">Cargar Certificado</h4>
                            <?php 
                            if(isset($solicitud['iddictamen_evaluacion']) && isset($solicitud['idformato_evaluacion']) && isset($solicitud['idinforme_evaluacion'])){

                                $row_formato = mysql_query("SELECT * FROM formato_evaluacion WHERE idformato_evaluacion = $solicitud[idformato_evaluacion]", $dspp) or die(mysql_error());
                                $formato = mysql_fetch_assoc($row_formato);
                                $row_informe = mysql_query("SELECT * FROM informe_evaluacion WHERE idinforme_evaluacion = $solicitud[idinforme_evaluacion]", $dspp) or die(mysql_error());
                                $informe = mysql_fetch_assoc($row_informe);
                                $row_dictamen = mysql_query("SELECT * FROM dictamen_evaluacion WHERE iddictamen_evaluacion = $solicitud[iddictamen_evaluacion]", $dspp) or die(mysql_error());
                                $dictamen = mysql_fetch_assoc($row_dictamen);

                                if($formato['estatus_formato'] == 'ACEPTADO' && $informe['estatus_informe'] == 'ACEPTADO' && $dictamen['estatus_dictamen'] == 'ACEPTADO'){
                                    $row_certificado = mysql_query("SELECT * FROM certificado WHERE idcertificado = $solicitud[idcertificado] AND idsolicitud_registro = $solicitud[idsolicitud_registro]", $dspp) or die(mysql_error());
                                    $certificado = mysql_fetch_assoc($row_certificado);

                                  if(isset($certificado['idcertificado'])){
                                    $inicio = strtotime($certificado['vigencia_inicio']);
                                    $fin = strtotime($certificado['vigencia_fin']);
                                  ?>
                                    <p class="alert alert-info"><?php echo $certificado['idcertificado']; ?> Se ha cargado el certificado, el cual tienen una Vigencia del <b><?php echo date('d/m/Y', $inicio); ?></b> al <b><?php echo date('d/m/Y', $fin); ?></b></p>
                                    <a href="<?php echo $certificado['archivo']; ?>" class="btn btn-success" style="width:100%" target="_blank">Descargar Certificado</a>
                                  <?php
                                  }else{
                                  ?>
                                    <div class="col-md-12">
                                      <p class="alert alert-info">Por favor defina la fecha de Inicio y Fin del Certificado.</p>
                                      <p class="alert alert-warning" style="padding:5px;">En caso de que no se despliegue el calendario, por favor definir la fecha con el siguiente formato <span style="color:red">dd-mm-aaaa</span></p>
                                    </div>
                                    <div class="col-md-6">
                                      <label for="fecha_inicio">Fecha Inicio</label> 
                                      <input type="text" name="fecha_inicio" id="fecha_inicio" class="form-control" placeholder="dd-mm-aaaa" required> 
                                    </div>
                                    <div class="col-md-6">
                                      <label for="fecha_fin">Fecha Fin</label>
                                      <input type="text" name="fecha_fin" id="fecha_fin" class="form-control" placeholder="dd-mm-aaaa" required>
                                    </div>
                                    
                                    <label for="certificado">Por favor seleccione el Certificado</label>
                                    <input type="file" name="certificado" id="certificado" class="form-control" required>
                                    <button type="submit" name="enviar_certificado" value="1" class="btn btn-success" style="width:100%">Enviar Certificado</button>  
                                  <?php
                                  }
                                }else{
                                  echo '<p class="alert alert-warning">
                                  Una vez aprobado el "Informe de Evaluación" y el "Dictamen de Evaluación" podra cargar el Certificado
                              </p> ';
                                }

                            }else{
                              echo '<p class="alert alert-warning">
                                Una vez aprobado el "Informe de Evaluación" y el "Dictamen de Evaluación" podra cargar el Certificado
                              </p> ';
                            }
                            ?>
                          </div>
                        </div>
                      </div>

                      <div class="modal-footer">
                        <input type="hidden" name="idsolicitud_registro" value="<?php echo $solicitud['idsolicitud_registro']; ?>">
                        <input type="hidden" name="idoc" value="<?php echo $solicitud['idoc']; ?>">
                        <input type="hidden" name="idempresa" value="<?php echo $solicitud['idempresa']; ?>">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- termina modal estatus_Certificado -->

          <!---- TERMINA SECCION CERTIFICADO ------>
            </form>
          <td>
            <a class="btn btn-sm btn-primary" data-toggle="tooltip" title="Consultar Solicitud" href="?SOLICITUD&IDsolicitud_empresa=<?php echo $solicitud['idsolicitud_registro']; ?>">consultar</a>
          </td>
          <td>
            <form action="../../reportes/reporte.php" method="POST" target="_new">
              <button class="btn btn-xs btn-default" data-toggle="tooltip" title="Descargar solicitud" target="_new" type="submit" ><img src="../../img/pdf.png" style="height:30px;" alt=""></button>

              <input type="hidden" name="idsolicitud_registro" value="<?php echo $solicitud['idsolicitud_registro']; ?>">
              <input type="hidden" name="generar_formato_empresa" value="2">
            </form>
          </td>

        </tr>
      <?php
      }
      ?>
      </tbody>
    </table>
  </div>
</div>
<script type="text/javascript">

function validar(){

  Idioma = document.getElementsByName("idioma");
   
  var seleccionado = false;
  for(var i=0; i<Idioma.length; i++) {    
    if(Idioma[i].checked) {
      seleccionado = true;
      break;
    }
  }
   
  if(!seleccionado) {
    alert("Debes seleccionar el idioma en el que se enviara el Contrato de Uso, Manual SPP y Acuse de Recibo");
    return false;
  }

  return true
}

<!--
function ventanaNueva(documento,ancho,alto,nombreVentana){
    window.open(documento, nombreVentana,'width=' + ancho + ', height=' + alto);
}
     
//-->
</script>
<!--<table>
<tr>
<td width="20"><?php if ($pageNum_empresa > 0) { // Show if not first page ?>
<a href="<?php printf("%s?pageNum_empresa=%d%s", $currentPage, 0, $queryString_empresa); ?>">
<span class="glyphicon glyphicon-fast-backward" aria-hidden="true"></span>
</a>
<?php } // Show if not first page ?></td>
<td width="20"><?php if ($pageNum_empresa > 0) { // Show if not first page ?>
<a href="<?php printf("%s?pageNum_empresa=%d%s", $currentPage, max(0, $pageNum_empresa - 1), $queryString_empresa); ?>">
<span class="glyphicon glyphicon-backward" aria-hidden="true"></span>
</a>
<?php } // Show if not first page ?></td>
<td width="20"><?php if ($pageNum_empresa < $totalPages_empresa) { // Show if not last page ?>
<a href="<?php printf("%s?pageNum_empresa=%d%s", $currentPage, min($totalPages_empresa, $pageNum_empresa + 1), $queryString_empresa); ?>">
<span class="glyphicon glyphicon-forward" aria-hidden="true"></span>
</a>
<?php } // Show if not last page ?></td>
<td width="20"><?php if ($pageNum_empresa < $totalPages_empresa) { // Show if not last page ?>
<a href="<?php printf("%s?pageNum_empresa=%d%s", $currentPage, $totalPages_empresa, $queryString_empresa); ?>">
<span class="glyphicon glyphicon-fast-forward" aria-hidden="true"></span>
</a>
<?php } // Show if not last page ?></td>
</tr>
</table>-->