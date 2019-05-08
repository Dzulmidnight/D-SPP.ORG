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

/*if(isset($_POST['opp_delete'])){
  $query=sprintf("delete from opp where idopp = %s",GetSQLValueString($_POST['idopp'], "text"));
  $ejecutar=mysql_query($query,$dspp) or die(mysql_error());
}*/

/************ INICIA VARIABLES GLOBALES ***************/
$fecha = time();
$idoc = $_SESSION['idoc'];
$spp_global = "cert@spp.coop";
$finanzas_spp = "adm@spp.coop";
$administrador = "yasser.midnight@gmail.com";

$row_correo = mysql_query("SELECT * FROM oc WHERE idoc = $idoc", $dspp) or die(mysql_error());
$correos_oc = mysql_fetch_assoc($row_correo);
/**********  TERMINA VARIABLES GLOBALES *****************/
 
if(isset($_POST['cancelar']) && $_POST['cancelar'] == "cancelar"){
  $idsolicitud_certificacion = $_POST['idsolicitud_certificacion'];
  $estatus_interno = 24;  

  $updateSQL = "UPDATE solicitud_certificacion SET status = $estatus_interno WHERE idsolicitud_certificacion = $idsolicitud_certificacion";
  $ejecutar = mysql_query($updateSQL,$dspp) or die(mysql_error());
}

if(isset($_POST['reemplazar_cotizacion']) && $_POST['reemplazar_cotizacion'] == 1){
  $idsolicitud_certificacion = $_POST['idsolicitud_certificacion'];
  $rutaArchivo = "../../archivos/ocArchivos/cotizaciones/";

  if(!empty($_FILES['nueva_cotizacion']['name'])){
      $_FILES["nueva_cotizacion"]["name"];
        move_uploaded_file($_FILES["nueva_cotizacion"]["tmp_name"], $rutaArchivo.$fecha."_".$_FILES["nueva_cotizacion"]["name"]);
        $archivo = $rutaArchivo.basename($fecha."_".$_FILES["nueva_cotizacion"]["name"]);
        unlink($_POST['cotizacion_actual']);
        $updateSQL = sprintf("UPDATE solicitud_certificacion SET cotizacion_opp = %s WHERE idsolicitud_certificacion = %s",
          GetSQLValueString($archivo, "text"),
          GetSQLValueString($idsolicitud_certificacion, "int"));
        $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

        $row_opp = mysql_query("SELECT opp.nombre, opp.abreviacion AS 'abreviacion_opp', opp.pais AS 'opp_pais', opp.spp, opp.password, opp.email, oc.email1, oc.email2, oc.abreviacion AS 'abreviacion_oc', oc.pais AS 'pais_oc', solicitud_certificacion.contacto1_email, solicitud_certificacion.contacto2_email, solicitud_certificacion.adm1_email FROM opp INNER JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp INNER JOIN oc ON solicitud_certificacion.idoc = oc.idoc WHERE idsolicitud_certificacion = $idsolicitud_certificacion", $dspp) or die(mysql_error());
        $opp_detail = mysql_fetch_assoc($row_opp);

        $asunto = "D-SPP Cotización actualizada / Updated quotation (Solicitud de Certificación para Organizaciones de Pequeños Productores)";

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
                  <td align="left" style="color:#ff738a;">Email Organismo de Certificación / Certification Entity: '.$opp_detail['email1'].'</td>
                </tr>
                <tr>
                  <td aling="left" style="text-align:justify">
                  <b style="color:red">'.$opp_detail['abreviacion_oc'].'</b> ha enviado la cotización actualizada correspondiente a la Solicitud de Certificación para Organizaciones de Pequeños Productores.
                  <br><br> Por favor iniciar sesión en el siguiente enlace <a href="http://d-spp.org/">www.d-spp.org/</a> como OPP, para poder acceder a la cotización.

                  <br><br>
                  <b style="color:red">'.$opp_detail['abreviacion_oc'].'</b> has sent the updated price quote corresponding to the Certification Application for Small Producers’ Organizations (SPOs).
                    Please open a session as an SPO at the following link <a href="http://d-spp.org/">www.d-spp.org/</a> in order to access the price quote.

                  </td>
                </tr>


                <tr>
                  <td colspan="2">
                    <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;" border="1" width="650px">
                      <tbody>
                        <tr style="font-size: 12px; text-align:center; background-color:#dff0d8; color:#3c763d;" height="50px;">
                          <td width="130px">Nombre de la organización/Organization name</td>
                          <td width="130px">País / Country</td>
                          <td width="130px">Organismo de Certificación / Certification Entity</td>
                          <td width="130px">Fecha de envío / Shipping Date</td>
                       
                          
                        </tr>
                        <tr style="font-size: 12px; text-align:justify">
                          <td style="padding:10px;">
                            '.$opp_detail['nombre'].' - ('.$opp_detail['abreviacion_opp'].')
                          </td>
                          <td style="padding:10px;">
                            '.$opp_detail['opp_pais'].'
                          </td>
                          <td style="padding:10px;">
                            '.$opp_detail['abreviacion_oc'].'
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
        if($idoc != 19 && $idoc != 15){
            if(!empty($opp_detail['email'])){
              $token = strtok($opp_detail['email'], "\/\,\;");
              while ($token !== false)
              {
                $mail->AddAddress($token);
                $token = strtok('\/\,\;');
              }
            }
            if(!empty($opp_detail['contacto1_email'])){
              $token = strtok($opp_detail['contacto1_email'], "\/\,\;");
              while ($token !== false)
              {
                $mail->AddAddress($token);
                $token = strtok('\/\,\;');
              }
            }
            if(!empty($opp_detail['contacto2_email'])){
              $token = strtok($opp_detail['contacto2_email'], "\/\,\;");
              while ($token !== false)
              {
                $mail->AddAddress($token);
                $token = strtok('\/\,\;');
              }
            }

            //$mail->AddBCC($spp_global);

            if(!empty($oc['email1'])){
              $token = strtok($oc['email1'], "\/\,\;");
              while ($token !== false)
              {
                $mail->AddCC($token);
                $token = strtok('\/\,\;');
              }
            }
            if(!empty($oc['email2'])){
              $token = strtok($oc['email2'], "\/\,\;");
              while ($token !== false)
              {
                $mail->AddCC($token);
                $token = strtok('\/\,\;');
              }
            }
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
        $mail->ClearAttachments();


        echo "<script>alert('Se ha actualizado la cotización');</script>";


  }else{
    $archivo = $_POST['cotizacion_actual'];
    echo "<script>alert('No se ha podido actualizar la cotización');</script>";

  }


}

if(isset($_POST['guardar_proceso']) && $_POST['guardar_proceso'] == 1){

  $query_opp = mysql_query("SELECT solicitud_certificacion.idopp, solicitud_certificacion.idoc, solicitud_certificacion.contacto1_email, solicitud_certificacion.contacto2_email, solicitud_certificacion.adm1_email, opp.nombre, opp.abreviacion AS 'abreviacion_opp', opp.email FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE solicitud_certificacion.idsolicitud_certificacion = $_POST[idsolicitud_certificacion]", $dspp) or die(mysql_error());
  $detalle_opp = mysql_fetch_assoc($query_opp);

  $row_informacion = mysql_query("SELECT solicitud_certificacion.idopp, solicitud_certificacion.idoc, oc.email1 AS 'oc_email1', oc.email2 AS 'oc_email2', solicitud_certificacion.contacto1_email, opp.nombre AS 'nombre_opp', opp.abreviacion AS 'abreviacion_opp', opp.email FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp INNER JOIN oc ON solicitud_certificacion.idoc = oc.idoc WHERE idsolicitud_certificacion = $_POST[idsolicitud_certificacion]", $dspp) or die(mysql_error());
  $informacion = mysql_fetch_assoc($row_informacion);

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
    $query_oc = mysql_query("SELECT nombre FROM oc WHERE idoc = $detalle_opp[idoc]", $dspp) or die(mysql_error());
    $detalle_oc = mysql_fetch_assoc($query_oc);

    $documentacion_nombres = '';
    $estatus_dspp = 9; //Termina proceso de certificación
    //creamos la variable del archivo extra

    if(!empty($_FILES['archivo_dictamen']['name'])){
        $_FILES["archivo_dictamen"]["name"];
          move_uploaded_file($_FILES["archivo_dictamen"]["tmp_name"], $rutaArchivo.$fecha."_".$_FILES["archivo_dictamen"]["name"]);
          $archivo_dictamen = $rutaArchivo.basename($fecha."_".$_FILES["archivo_dictamen"]["name"]);
    }else{
      $archivo_dictamen = NULL;
    }

    $asunto = "D-SPP | NOTIFICACIÓN DE DICTAMEN (NOTIFICATION OF RESOLUTION)";

    if($_POST['tipo_solicitud'] == 'RENOVACION'){
      if(isset($_POST['mensaje_renovacion'])){
        $mensaje_renovacion = $_POST['mensaje_renovacion'];
      }else{
        $mensaje_renovacion = '';
      }
      $updateSQL = sprintf("UPDATE solicitud_certificacion SET estatus_interno = %s, estatus_dspp = %s WHERE idsolicitud_certificacion = %s",
        GetSQLValueString($_POST['estatus_interno'], "int"),
        GetSQLValueString($estatus_dspp, "int"),
        GetSQLValueString($_POST['idsolicitud_certificacion'], "int"));
      $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

      $insertSQL = sprintf("INSERT INTO proceso_certificacion (idsolicitud_certificacion, estatus_interno, estatus_dspp, nombre_archivo, accion, archivo, fecha_registro) VALUES (%s, %s, %s, %s, %s, %s, %s)",
        GetSQLValueString($_POST['idsolicitud_certificacion'], "int"),
        GetSQLValueString($_POST['estatus_interno'], "int"),
        GetSQLValueString($estatus_dspp, "int"),
        GetSQLValueString($_POST['nombreArchivo'], "text"),
        GetSQLValueString($accion, "text"),
        GetSQLValueString($archivo_dictamen, "text"),
        GetSQLValueString($fecha, "int"));
      $insertar = mysql_query($insertSQL,$dspp) or die(mysql_error());

      //creamos el monto del comprobante de pago
      $estatus_comprobante = "EN ESPERA";

      $insertSQL = sprintf("INSERT INTO comprobante_pago(estatus_comprobante, monto) VALUES (%s, %s)",
        GetSQLValueString($estatus_comprobante, "text"),
        GetSQLValueString($_POST['total_membresia'], "text"));
      $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

      //capturamos el id del comprobante para vincularlo con la membresia
      $idcomprobante_pago = mysql_insert_id($dspp);

      //creamos la membresia
      $estatus_membresia = "EN ESPERA";
      $insertSQL = sprintf("INSERT INTO membresia(estatus_membresia, idopp, idsolicitud_certificacion, idcomprobante_pago) VALUES (%s, %s, %s, %s)",
        GetSQLValueString($estatus_membresia, "text"),
        GetSQLValueString($_POST['idopp'], "int"),
        GetSQLValueString($_POST['idsolicitud_certificacion'], "int"),
        GetSQLValueString($idcomprobante_pago, "int"));
      $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

      ///inicia envio de mensaje dictamen positivo


      //$mail->AddAttachment($archivo);

       if(!empty($_POST['mensaje_renovacion'])){
        $cuerpo_mensaje = '
              <html>
              <head>
                <meta charset="utf-8">
              </head>
              <body>
                <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;text-align:justify" border="0" width="650px">
                  <tbody>
                    <tr>
                      <th rowspan="2" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                      <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">NOTIFICACIÓN DE DICTAMEN</span></p></th>

                    </tr>
                    <tr>
                     <th scope="col" align="left" width="280"><p>Para: <span style="color:red">'.$detalle_opp['nombre'].' - ('.$detalle_opp['abreviacion_opp'].')</span></p></th>
                    </tr>

                    <tr>
                      <td colspan="2">
                        <p>
                          '.$_POST['mensaje_renovacion'].'
                        </p>
                        <p>
                          <b>
                            NOTA: El pago de membresía se considera una ratificación de la firma de Contrato de Uso por lo que no es necesario firmar el contrato cada año que renuevan su certificado.
                          </b>
                        </p>
                        <hr>
                      </td>
                    </tr>
                    <tr>
                      <td><p><strong>Datos Bancarios</strong></p></td>
                    </tr>
                    <tr>
                      <td>
                        <ul>
                          Datos Bancarios Anexos en el correo.
                        </ul>
                      </td>
                    </tr>

                    <tr>
                      <td colspan="2">
                        <p>En caso de cualquier duda o aclaración por favor escribir a <span style="color:red">'.$correos_oc['email1'].', '.$correos_oc['email2'].'</span></p>
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
                <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; text-align:justify" border="0" width="650px">
                  <tbody>
                    <tr>
                      <th rowspan="2" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                      <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">NOTIFICACIÓN DE DICTAMEN</span></p></th>

                    </tr>
                    <tr>
                     <th scope="col" align="left" width="280"><p>Para: <span style="color:red">'.$detalle_opp['nombre'].' - ('.$detalle_opp['abreviacion_opp'].')</span></p></th>
                    </tr>

                    <tr>
                      <td colspan="2">
                        <p>
                          1. Nosotros <span style="color:red">'.$detalle_oc['nombre'].'</span>, como Organismo de Certificación autorizado por SPP Global, nos complace informar por este medio que la evaluación SPP fue concluida con resultado <span style="color:red">positivo</span>.
                        </p>
                        <p>
                          2. A continuación, procederemos a subir (cargar) el Certificado en el sistema D-SPP para que usted pueda descargarlo.
                        </p>
                        <p>
                          3. Se solicita de la manera más atenta se <span style="color:red">proceda con el pago de membresía a SPP Global</span>. De acuerdo al número se socios, el importe de la membresía es: <strong style="color:red">'.$_POST['total_membresia'].'</strong>. (Se anexan los datos bancarios, favor de leer las Disposiciones Generales de Pago).
                        </p>

                        <p>
                          La Organización de Pequeños Productores (OPP) tendrá un plazo máximo de 30 días naturales para realizar el pago y cargar su comprobante al D-SPP.
                        </p>
                        <p>
                          En caso de que tuvieran algún problema para realizar el pago oportunamente, se debe informar al área de Administración y Finanzas de SPP Global (adm@spp.coop).
                        </p>
                        <p>
                          De no recibir el pago o comunicación de parte de la OPP, lamentablemente se enviará la Suspensión de su Certificado.
                        </p>
                        <p>
                          <b>
                            NOTA: El pago de membresía se considera una ratificación de la firma de Contrato de Uso por lo que no es necesario firmar el contrato cada año que renuevan su certificado.
                          </b>
                        </p>
                        <hr>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="2">
                        <h3>English Below</h3>
                      </td>
                    </tr>

                    <tr style="font-style: italic; color: #797979;">
                      <td colspan="2">
                        <p>
                          1. We, <span style="color:red">'.$detalle_oc['nombre'].'</span>, as a Certification Entity authorized by SPP Global, are pleased to inform you, by this means, that the SPP evaluation has been concluded with a <span style="color:red">positive</span> result.
                        </p>
                        <p>
                          2. Next, we will proceed to upload the Certificate in the D-SPP system so that you can download it.
                        </p>
                        <p>
                          3. We request the most careful way to <span style="color:red">proceed with the payment of membership to SPP Global</span>. According to the number of members, the amount of the membership is: <strong style="color:red">'.$_POST['total_membresia'].'</strong>. (The bank details are attached, please read the General Payment Provisions)
                        </p>

                        <p>
                          The Small Producers Organization (OPP) will have a maximum deadline of 30 calendar days to make the payment and load your receipt to the D-SPP.
                        </p>
                        <p>
                          In case of any problems to make the payment in due time, please inform the Administration and Finance area of SPP Global (adm@spp.coop).
                        </p>
                        <p>
                          Failure to receive payment or communication from the OPP, will unfortunately send the Suspension of your Certificate.
                        </p>
                        <p>
                          <b>
                            NOTE: Payment of membership fee is considered as ratification of the signing of the User’s Contract, and it is thus not necessary to sign the contract each year in order to renew your certificate.
                          </b>
                        </p>
                        <hr>
                      </td>
                    </tr>

                    <tr>
                      <td><p><strong>Datos Bancarios</strong></p></td>
                    </tr>
                    <tr>
                      <td>
                        <ul>
                          Datos Bancarios Anexos en el correo.
                          <br>
                          Bank Information attached to email.
                        </ul>
                      </td>
                    </tr>

                    <tr>
                      <td colspan="2">
                        <p>En caso de cualquier duda o aclaración por favor escribir a <span style="color:red">'.$correos_oc['email1'].', '.$correos_oc['email2'].'</span></p>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </body>
              </html>
        ';
      }

      $row_documentacion = mysql_query("SELECT * FROM documentacion WHERE nombre = 'Datos Bancarios SPP'", $dspp) or die(mysql_error());
      $documentacion = mysql_fetch_assoc($row_documentacion);

      $mail->AddAttachment($documentacion['archivo']);


      if(isset($archivo_dictamen)){
        $mail->AddAttachment($archivo_dictamen);
      }

      if($idoc != 19 && $idoc != 15){
          if(isset($detalle_opp['contacto1_email'])){
            $token = strtok($detalle_opp['contacto1_email'], "\/\,\;");
            while ($token !== false)
            {
              $mail->AddAddress($token);
              $token = strtok('\/\,\;');
            }

          }
          if(isset($detalle_opp['contacto2_email'])){
            $token = strtok($detalle_opp['contacto2_email'], "\/\,\;");
            while ($token !== false)
            {
              $mail->AddAddress($token);
              $token = strtok('\/\,\;');
            }
          }
          if(isset($detalle_opp['adm1_email'])){
            $token = strtok($detalle_opp['adm1_email'], "\/\,\;");
            while ($token !== false)
            {
              $mail->AddAddress($token);
              $token = strtok('\/\,\;');
            }
          }
          if(isset($detalle_opp['email'])){
            $token = strtok($detalle_opp['email'], "\/\,\;");
            while ($token !== false)
            {
              $mail->AddAddress($token);
              $token = strtok('\/\,\;');
            }
          }
          if(isset($correos_oc['email1'])){
            $token = strtok($correos_oc['email1'], "\/\,\;");
            while ($token !== false)
            {
              $mail->AddCC($token);
              $token = strtok('\/\,\;');
            }
          }
          if(isset($correos_oc['email2'])){
            $token = strtok($correos_oc['email2'], "\/\,\;");
            while ($token !== false)
            {
              $mail->AddCC($token);
              $token = strtok('\/\,\;');
            }
          }
      }

      $mail->AddBCC($spp_global);
      $mail->AddBCC($finanzas_spp);

      $mail->Subject = utf8_decode($asunto);
      $mail->Body = utf8_decode($cuerpo_mensaje);
      $mail->MsgHTML(utf8_decode($cuerpo_mensaje));

      /*if($mail->Send()){
        
       echo "<script>alert('Correo enviado Exitosamente.');location.href ='javascript:history.back()';</script>";
      }else{
        echo "<script>alert('Error, no se pudo enviar el correo, por favor contacte al administrador: soporte@d-spp.org');location.href ='javascript:history.back()';</script>";
   
      }*/
      $mail->Send();
      $mail->ClearAddresses();
      $mail->ClearAttachments();
      
      /// INICIA MENSAJE "CARGAR DOCUMENTOS DE EVALUACIÓN"

      $asunto = "D-SPP | Formatos de Evaluación";

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
                <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">Cargar Formato, Dictamen e Informe de Evaluación</span></p></th>

              </tr>
              <tr>
               <th scope="col" align="left" width="280"><p>OPP: <span style="color:red">'.$informacion['nombre_opp'].'</span></p></th>
              </tr>

              <tr>
                <td colspan="2">
                 <p>SPP GLOBLA notifica que la OPP: '.$informacion['nombre_opp'].' ha cumplido con la documentación necesaria.</p>
                 <p>
                  Por favor procedan a ingresar en su cuenta de OC dentro del sistema D-SPP para poder cargar los siguientes documentos: 
                     <ul style="color:red">
                       <li>Formato de Evaluación</li>
                       <li>Informe de Evaluación</li>
                       <li>Dictamen de Evaluación</li>
                     </ul>

                 </p>
                </td>
              </tr>
              <tr>
                <td colspan="2">
                  ¿Pasos para cargar la documentación?. Para poder cargar la documentación debe seguir los siguiente pasos:
                  <ol>
                    <li>Dar clic en la opción "SOLICITUDES"</li>
                    <li>Seleccionar "Solicitudes OPP"</li>
                    <li>Posicionarse en la columna "Certificado" y dar clic en el boton "Cargar Certificado"</li>
                    <li>Se desplegara una ventan donde podra cargar la documentación</li>
                  </ol>
                  <p style="color:red">
                    Una vez que cargue los documentos, puede cargar el Certificado SPP.
                  </p>
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

      if($idoc != 19 && $idoc != 15){
          if(isset($correos_oc['email1'])){
            $token = strtok($correos_oc['email1'], "\/\,\;");
            while ($token !== false)
            {
              $mail->AddCC($token);
              $token = strtok('\/\,\;');
            }
          }
          if(isset($correos_oc['email2'])){
            $token = strtok($correos_oc['email2'], "\/\,\;");
            while ($token !== false)
            {
              $mail->AddCC($token);
              $token = strtok('\/\,\;');
            }
          }
      }

      $mail->Subject = utf8_decode($asunto);
      $mail->Body = utf8_decode($cuerpo_mensaje);
      $mail->MsgHTML(utf8_decode($cuerpo_mensaje));
      $mail->Send();
      $mail->ClearAddresses();
      $mail->ClearAttachments();

      /// TERMINA MENSAJE "CARGAR DOCUEMENTOS DE EVALUACIÓN"

      ///termina envio de mensaje dictamen positivo
    ////////// SE ENVIA DICTAMEN POSITIVO PRIMERA VEZ ////////////////////
    }else{ 
    ////////// SE ENVIA DICTAMEN POSITIVO PRIMERA VEZ ////////////////////
      $updateSQL = sprintf("UPDATE solicitud_certificacion SET estatus_interno = %s, estatus_dspp = %s WHERE idsolicitud_certificacion = %s",
        GetSQLValueString($_POST['estatus_interno'], "int"),
        GetSQLValueString($estatus_dspp, "int"),
        GetSQLValueString($_POST['idsolicitud_certificacion'], "int"));
      $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

      $insertSQL = sprintf("INSERT INTO proceso_certificacion (idsolicitud_certificacion, estatus_interno, estatus_dspp, nombre_archivo, accion, archivo, fecha_registro) VALUES (%s, %s, %s, %s, %s, %s, %s)",
        GetSQLValueString($_POST['idsolicitud_certificacion'], "int"),
        GetSQLValueString($_POST['estatus_interno'], "int"),
        GetSQLValueString($estatus_dspp, "int"),
        GetSQLValueString($_POST['nombre_archivo_dictamen'], "text"),
        GetSQLValueString($accion, "text"),
        GetSQLValueString($archivo_dictamen, "text"),
        GetSQLValueString($fecha, "int"));
      $insertar = mysql_query($insertSQL,$dspp) or die(mysql_error());

      //creamos el monto del comprobante de pago
      $estatus_comprobante = "EN ESPERA";

      $insertSQL = sprintf("INSERT INTO comprobante_pago(estatus_comprobante, monto) VALUES (%s, %s)",
        GetSQLValueString($estatus_comprobante, "text"),
        GetSQLValueString($_POST['total_membresia'], "text"));
      $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

      //capturamos el id del comprobante para vincularlo con la membresia
      $idcomprobante_pago = mysql_insert_id($dspp);

      //creamos la membresia
      $estatus_membresia = "EN ESPERA";
      $insertSQL = sprintf("INSERT INTO membresia(estatus_membresia, idopp, idsolicitud_certificacion, idcomprobante_pago) VALUES (%s, %s, %s, %s)",
        GetSQLValueString($estatus_membresia, "text"),
        GetSQLValueString($_POST['idopp'], "int"),
        GetSQLValueString($_POST['idsolicitud_certificacion'], "int"),
        GetSQLValueString($idcomprobante_pago, "int"));
      $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

      ///inicia envio de mensaje dictamen positivo
      if(isset($_POST['idioma'])){
        $row_documentacion = mysql_query("SELECT * FROM documentacion WHERE idestatus_interno = 8 AND idioma = '$_POST[idioma]'", $dspp) or die(mysql_error());
        while($documentacion = mysql_fetch_assoc($row_documentacion)){
            $mail->AddAttachment($documentacion['archivo']);
            $documentacion_nombres .= "<li>".$documentacion['nombre']."</li>";   
        }
      }else{
        $row_documentacion = mysql_query("SELECT * FROM documentacion WHERE idestatus_interno = 8 AND idioma = 'ESP'", $dspp) or die(mysql_error());
        while($documentacion = mysql_fetch_assoc($row_documentacion)){
            $mail->AddAttachment($documentacion['archivo']);
            $documentacion_nombres .= "<li>".$documentacion['nombre']."</li>";   
        }
      }

      if(isset($archivo_dictamen)){
        $documentacion_nombres .= '<li>'.$_POST['nombre_archivo_dictamen'].'</li>';
        $mail->AddAttachment($archivo_dictamen);
      }

      $documentacion = mysql_fetch_assoc($row_documentacion);
      /*29_05_2017
      $asunto = "D-SPP | NOTIFICACIÓN DE DICTAMEN (NOTIFICATION OF RESOLUTION)";

      if(!empty($_POST['mensajeOPP'])){
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
                      <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">NOTIFICACIÓN DE DICTAMEN / NOTIFICATION OF RESOLUTION</span></p></th>

                    </tr>
                    <tr>
                     <th scope="col" align="left" width="280"><p>Para: <span style="color:red">'.$detalle_opp['nombre'].' - ('.$detalle_opp['abreviacion_opp'].')</span></p></th>
                    </tr>

                    <tr>
                      <td colspan="2" style="text-align:justify">
                        <p>'.$_POST['mensajeOPP'].'</p>
                      </td>
                    </tr>
                    <tr>
                      <td><p><strong>DOCUMENTOS ANEXOS</strong></p></td>
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
                        <p style="color:red"><strong>MEMBRESÍA SPP</strong></p>
                        <p>
                          ADICIONALMENTE SE SOLICITA DE LA MANERA MÁS ATENTA REALIZAR EL PAGO CORRESPONDIENTE A LA MEMBRESIA SPP POR EL IMPORTE DE: <span style="color:red;">'.$_POST['total_membresia'].'</span>
                        </p>
                        <p>
                          LOS DATOS BANCARIOS SE ENCUENTRAN ANEXOS AL CORREO.
                        </p>
                        <p>
                          DESPUÉS DE REALIZAR EL PAGO POR FAVOR PROCEDA A CARGAR EL <span style="color:red">CONTRATO DE USO y ACUSE DE RECIBO FIRMADO</span> ASÍ MISMO EL <span style="color:red">COMPROBANTE DE PAGO</span> POR MEDIO DEL SISTEMA D-SPP, ESTO INGRESANDO EN SU CUENTA DE OPP(Organización de Pequeños Productores) EN LA SIGUIENTE DIRECCIÓN <a href="http://d-spp.org/esp/?OPP">http://d-spp.org/esp/?OPP</a>.
                        </p>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="2">
                      <hr>
                        <p>En caso de cualquier duda o aclaración por favor escribir a <span style="color:red">'.$correos_oc['email1'].', '.$correos_oc['email2'].'</span></p>
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
                      <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">NOTIFICACIÓN DE DICTAMEN / NOTIFICATION OF RESOLUTION </span></p></th>

                    </tr>
                    <tr>
                     <th scope="col" align="left" width="280"><p>Para: <span style="color:red">'.$detalle_opp['nombre'].' - ('.$detalle_opp['abreviacion_opp'].')</span></p></th>
                    </tr>
                    <tr>
                      <td colspan="2" style="text-align:justify">

                        <p>1. Nosotros <span style="color:red">'.$detalle_oc['nombre'].'</span>, como Organismo de Certificación autorizado por SPP Global, nos complace informar por este medio que la evaluación SPP fue concluida con resultado positivo.</p>
                        <p>
                          2. Para concluir el proceso, se solicita de la manera más atenta leer los documentos anexos y posteriormente <span style="color:red">firmar el Contrato de Uso y Acuse de Recibo</span>. Favor de completar los datos de su organización y del representante legal en los <span style="color:red">textos marcados en color rojo dentro del Contrato de Uso</span>.
                        </p>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="2" style="text-align:justify">

                        <p>1. We, <span style="color:red">'.$detalle_oc['nombre'].'</span>, as a Certification Entity authorized by SPP Global, are pleased to inform you, by this means, that the SPP evaluation has been concluded with a “positive” result.</p>
                        <p>
                          2. In order to complete the process, the most careful request is to read the attached documents and subsequently sign <span style="color:red">the User´s Contract and Confirmation of Receipt </span>. Please complete the information of your organization and the legal representative in <span style="color:red">the texts marked in red</span> within the Contract of Use.
                        </p>
                      </td>
                    </tr>

                    <tr>
                      <td><p><strong>DOCUMENTOS ANEXOS / ATTACHED DOCUMENTS</strong></p></td>
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
                        <p style="color:red"><strong>MEMBRESÍA SPP</strong></p>
                        <p>
                          Adicionalmente se solicita de la manera más atenta, se proceda con el <span style="color:red">pago de membresía a SPP Global</span>, de acuerdo al monto indicado de: <strong style="color:red;">'.$_POST['total_membresia'].'</strong>. (Se anexan los datos bancarios, favor de leer las Disposiciones Generales de Pago para evitar se generen intereses). Una vez que haya realizado el pago, favor de <span style="color:red">entrar a su cuenta y cargar el comprobante bancario</span>.
                        </p>
                        <p>
                          LOS DATOS BANCARIOS SE ENCUENTRAN ANEXOS AL CORREO.
                        </p>
                        <p>
                          DESPUÉS DE REALIZAR EL PAGO POR FAVOR PROCEDA A CARGAR EL <span style="color:red">CONTRATO DE USO y ACUSE DE RECIBO FIRMADO</span> ASÍ MISMO EL <span style="color:red">COMPROBANTE DE PAGO</span> POR MEDIO DEL SISTEMA D-SPP, ESTO INGRESANDO EN SU CUENTA DE OPP(Organización de Pequeños Productores) EN LA SIGUIENTE DIRECCIÓN <a href="http://d-spp.org/esp/?OPP">http://d-spp.org/esp/?OPP</a>.
                        </p>
                        <p>
                          3. Una vez que SPP Global confirme a través del Sistema la recepción de los documentos y la recepción del pago en la cuenta de SPP Global, procederemos a hacer entrega del Certificado.
                        </p>

                      </td>
                    </tr>

                    <tr>
                      <td colspan="2" style="text-align:justify">
                        <p style="color:red"><strong>SPP MEMBERSHIP</strong></p>
                        <p>
                          In order to complete the process, you are asked to please proceed with payment of the membership fee to SPP Global, in the following amount: <strong style="color:red;">'.$_POST['total_membresia'].'</strong>. (Bank information is attached. Please read the General Payment Provisions to avoid interest charges.) After payment has been made, please <span style="color:red">enter your account in the D-SPP system and upload the bank receipt.</span>
                        </p>
                        <p>
                          BANK INFORMATION ATTACHED TO EMAIL
                        </p>
                        <p>
                          AFTER MAKING PAYMENT, PLEASE <span style="color:red">UPLOAD “SIGNED USER’S CONTRACT AND ACKNOWLEDGEMENT OF RECEIPT</span> AND ALSO <span style="color:red">THE “RECEIPT OF PAYMENT”</span> THROUGH THE D-SPP SYSTEM, BY ENTERING YOUR ACCOUNT AS AN SPO (Small Producers’ Organization) IN THE FOLLOWING LINK: <a href="http://d-spp.org/esp/?OPP">http://d-spp.org/esp/?OPP</a>
                        </p>
                        <p>
                          3.  After SPP Global has confirmed through the System that payment has been received in the SPP Global account, your Certificate will be made available to you.
                        </p>

                      </td>
                    </tr>

                    <tr>
                      <td colspan="2">
                      <hr>
                        <p>En caso de cualquier duda o aclaración por favor escribir a <span style="color:red">'.$correos_oc['email1'].', '.$correos_oc['email2'].'</span></p>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </body>
              </html>
        ';
      }
      if(!empty($detalle_opp['contacto1_email'])){
        $token = strtok($detalle_opp['contacto1_email'], "\/\,\;");
        while ($token !== false)
        {
          $mail->AddAddress($token);
          $token = strtok('\/\,\;');
        }

      }
      if(!empty($detalle_opp['contacto2_email'])){
        $token = strtok($detalle_opp['contacto2_email'], "\/\,\;");
        while ($token !== false)
        {
          $mail->AddAddress($token);
          $token = strtok('\/\,\;');
        }
      }
      if(!empty($detalle_opp['adm1_email'])){
        $token = strtok($detalle_opp['adm1_email'], "\/\,\;");
        while ($token !== false)
        {
          $mail->AddAddress($token);
          $token = strtok('\/\,\;');
        }
      }
      if(!empty($detalle_opp['email'])){
        $token = strtok($detalle_opp['email'], "\/\,\;");
        while ($token !== false)
        {
          $mail->AddAddress($token);
          $token = strtok('\/\,\;');
        }
      }
      if(isset($correos_oc['email1'])){
        $token = strtok($correos_oc['email1'], "\/\,\;");
        while ($token !== false)
        {
          $mail->AddCC($token);
          $token = strtok('\/\,\;');
        }
      }
      if(isset($correos_oc['email2'])){
        $token = strtok($correos_oc['email2'], "\/\,\;");
        while ($token !== false)
        {
          $mail->AddCC($token);
          $token = strtok('\/\,\;');
        }
      }

      $mail->AddBCC($spp_global);
      $mail->AddBCC($finanzas_spp);

      $mail->Subject = utf8_decode($asunto);
      $mail->Body = utf8_decode($cuerpo_mensaje);
      $mail->MsgHTML(utf8_decode($cuerpo_mensaje));
      $mail->Send();
      $mail->ClearAddresses();
      $mail->ClearAttachments(); 29_05_2017*/
      /*if($mail->Send()){
        $mail->ClearAddresses();
        echo "<script>alert('Correo enviado Exitosamente.');location.href ='javascript:history.back()';</script>";
      }else{
        $mail->ClearAddresses();
        echo "<script>alert('Error, no se pudo enviar el correo, por favor contacte al administrador: soporte@d-spp.org');location.href ='javascript:history.back()';</script>";
      }*/
      /// INICIA MENSAJE "CARGAR DOCUMENTOS DE EVALUACIÓN"

      $asunto = "D-SPP | Formatos de Evaluación";

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
                <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">Cargar Formato, Dictamen e Informe de Evaluación</span></p></th>

              </tr>
              <tr>
               <th scope="col" align="left" width="280"><p>OPP: <span style="color:red">'.$informacion['nombre_opp'].'</span></p></th>
              </tr>

              <tr>
                <td colspan="2">
                 <p>SPP GLOBLA notifica que la OPP: '.$informacion['nombre_opp'].' ha cumplido con la documentación necesaria.</p>
                 <p>
                  Por favor procedan a ingresar en su cuenta de OC dentro del sistema D-SPP para poder cargar los siguientes documentos: 
                     <ul style="color:red">
                       <li>Formato de Evaluación</li>
                       <li>Informe de Evaluación</li>
                       <li>Dictamen de Evaluación</li>
                     </ul>

                 </p>
                </td>
              </tr>
              <tr>
                <td colspan="2">
                  ¿Pasos para cargar la documentación?. Para poder cargar la documentación debe seguir los siguiente pasos:
                  <ol>
                    <li>Dar clic en la opción "SOLICITUDES"</li>
                    <li>Seleccionar "Solicitudes OPP"</li>
                    <li>Posicionarse en la columna "Certificado" y dar clic en el boton "Cargar Certificado"</li>
                    <li>Se desplegara una ventan donde podra cargar la documentación</li>
                  </ol>
                  <p style="color:red">
                    Una vez que cargue los documentos, puede cargar el Certificado SPP.
                  </p>
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
      if($idoc != 19 && $idoc != 15){
          if(isset($correos_oc['email1'])){
            $token = strtok($correos_oc['email1'], "\/\,\;");
            while ($token !== false)
            {
              $mail->AddCC($token);
              $token = strtok('\/\,\;');
            }
          }
          if(isset($correos_oc['email2'])){
            $token = strtok($correos_oc['email2'], "\/\,\;");
            while ($token !== false)
            {
              $mail->AddCC($token);
              $token = strtok('\/\,\;');
            }
          }
      }

      $mail->Subject = utf8_decode($asunto);
      $mail->Body = utf8_decode($cuerpo_mensaje);
      $mail->MsgHTML(utf8_decode($cuerpo_mensaje));
      $mail->Send();
      $mail->ClearAddresses();
      $mail->ClearAttachments();

      /// TERMINA MENSAJE "CARGAR DOCUEMENTOS DE EVALUACIÓN"
          
      ///termina envio de mensaje dictamen positivo
    }



  }//***********************************************************************************************//
   // EL DICTAMEN ES NEGATIVO **********************************************************************/
   //***************************************************************************************************/
  else if($_POST['estatus_interno'] == 9){
      $query_oc = mysql_query("SELECT nombre FROM oc WHERE idoc = $detalle_opp[idoc]", $dspp) or die(mysql_error());
      $detalle_oc = mysql_fetch_assoc($query_oc);

    $documentacion_nombres = '';
    $estatus_dspp = 9; //Termina proceso de certificación
    //creamos la variable del archivo extra
    if(isset($_POST['nombre_archivo'])){
      $nombre_archivo = $_POST['nombre_archivo'];
    }else{
      $nombre_archivo = '';
    }

    if(!empty($_FILES['archivo_extra']['name'])){
        $_FILES["archivo_extra"]["name"];
          move_uploaded_file($_FILES["archivo_extra"]["tmp_name"], $rutaArchivo.$fecha."_".$_FILES["archivo_extra"]["name"]);
          $archivo = $rutaArchivo.basename($fecha."_".$_FILES["archivo_extra"]["name"]);
    }else{
      $archivo = NULL;
    }

    $asunto = "D-SPP | NOTIFICACIÓN DE DICTAMEN NEGATIVO";

    ///*******************************                              ***********************************************
    ///****************************** DICTAMEN NEGATIVO (RENOVACION)************************************************
    //09_04_2017if($_POST['tipo_solicitud'] == 'RENOVACION'){
      if(isset($_POST['mensaje_renovacion'])){
        $mensaje_renovacion = $_POST['mensaje_renovacion'];
      }else{
        $mensaje_renovacion = '';
      }

      $updateSQL = sprintf("UPDATE solicitud_certificacion SET estatus_interno = %s, estatus_dspp = %s WHERE idsolicitud_certificacion = %s",
        GetSQLValueString($_POST['estatus_interno'], "int"),
        GetSQLValueString($estatus_dspp, "int"),
        GetSQLValueString($_POST['idsolicitud_certificacion'], "int"));
      $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

      $estatus_opp = 8;// dictamen negativo
      $updateSQL = sprintf("UPDATE opp SET estatus_opp = %s WHERE idopp = %s",
        GetSQLValueString($estatus_opp, "int"),
        GetSQLValueString($_POST['idopp'], "int"));
      $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

      $insertSQL = sprintf("INSERT INTO proceso_certificacion (idsolicitud_certificacion, estatus_interno, estatus_dspp, nombre_archivo, accion, archivo, fecha_registro) VALUES (%s, %s, %s, %s, %s, %s, %s)",
        GetSQLValueString($_POST['idsolicitud_certificacion'], "int"),
        GetSQLValueString($_POST['estatus_interno'], "int"),
        GetSQLValueString($estatus_dspp, "int"),
        GetSQLValueString($_POST['nombreArchivo'], "text"),
        GetSQLValueString($accion, "text"),
        GetSQLValueString($archivo, "text"),
        GetSQLValueString($fecha, "int"));
      $insertar = mysql_query($insertSQL,$dspp) or die(mysql_error());


      ///inicia envio de mensaje dictamen positivo


      //$mail->AddAttachment($archivo);

      if(!empty($_POST['mensaje_renovacion'])){
        $cuerpo_mensaje = '
              <html>
              <head>
                <meta charset="utf-8">
              </head>
              <body>
                <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;text-align:justify" border="0" width="650px">
                  <tbody>
                    <tr>
                      <th rowspan="2" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                      <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">NOTIFICACIÓN DE DICTAMEN</span></p></th>

                    </tr>
                    <tr>
                     <th scope="col" align="left" width="280"><p>Para: <span style="color:red">'.$detalle_opp['nombre'].' - ('.$detalle_opp['abreviacion_opp'].')</span></p></th>
                    </tr>

                    <tr>
                      <td colspan="2">
                        <p>
                          '.$_POST['mensaje_renovacion'].'
                        </p>
                      </td>
                    </tr>

                    <tr>
                      <td colspan="2">
                        <p>En caso de cualquier duda o aclaración por favor escribir a <span style="color:red">'.$correos_oc['email1'].', '.$correos_oc['email2'].'</span></p>
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
                <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;text-align:justify" border="0" width="650px">
                  <tbody>
                    <tr>
                      <th rowspan="2" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                      <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">NOTIFICACIÓN DE DICTAMEN / NOTIFICATION OF RESOLUTION </span></p></th>

                    </tr>
                    <tr>
                     <th scope="col" align="left" width="280"><p>Para: <span style="color:red">'.$detalle_opp['nombre'].' - ('.$detalle_opp['abreviacion_opp'].')</span></p></th>
                    </tr>

                    <tr>
                      <td colspan="2">
                        <p>
                          Nosotros <span style="color:red">'.$detalle_oc['nombre'].'</span>, como Organismo de Certificación autorizado por SPP Global, lamentamos informar que su Dictamen de la evaluación para el Certificación SPP es negatvio. 
                        </p>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="2">
                        <p>
                          We, <span style="color:red">'.$detalle_oc['nombre'].'</span>, as a Certification Entity authorized by SPP Global, regret to inform you that your Resolution for the SPP Certification evaluation is negative. 
                        </p>
                      </td>
                    </tr>

                    <tr>
                      <td colspan="2">
                        <p>En caso de cualquier duda o aclaración por favor escribir a <span style="color:red">'.$correos_oc['email1'].', '.$correos_oc['email2'].'</span></p>
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
      if($idoc != 19 && $idoc != 15){
          if(isset($detalle_opp['contacto1_email'])){
            $token = strtok($detalle_opp['contacto1_email'], "\/\,\;");
            while ($token !== false)
            {
              $mail->AddAddress($token);
              $token = strtok('\/\,\;');
            }
          }
          if(isset($detalle_opp['contacto2_email'])){
            $token = strtok($detalle_opp['contacto2_email'], "\/\,\;");
            while ($token !== false)
            {
              $mail->AddAddress($token);
              $token = strtok('\/\,\;');
            }
          }
          if(isset($detalle_opp['adm1_email'])){
            $token = strtok($detalle_opp['adm1_email'], "\/\,\;");
            while ($token !== false)
            {
              $mail->AddAddress($token);
              $token = strtok('\/\,\;');
            }
          }
          if(isset($detalle_opp['email'])){
            $token = strtok($detalle_opp['email'], "\/\,\;");
            while ($token !== false)
            {
              $mail->AddAddress($token);
              $token = strtok('\/\,\;');
            }
          }
          if(isset($correos_oc['email1'])){
            $token = strtok($correos_oc['email1'], "\/\,\;");
            while ($token !== false)
            {
              $mail->AddCC($token);
              $token = strtok('\/\,\;');
            }
          }
          if(isset($correos_oc['email2'])){
            $token = strtok($correos_oc['email2'], "\/\,\;");
            while ($token !== false)
            {
              $mail->AddCC($token);
              $token = strtok('\/\,\;');
            }
          }
      }

      $mail->AddBCC($spp_global);
      $mail->AddBCC($finanzas_spp);

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
      $mail->ClearAttachments();
      ///termina envio de mensaje dictamen positivo
    //09_04_2017}
    ///********************************* TERMINA DICTAMEN NEGATIVO RENOVACION*********************************************
    ///*********************************                                     *********************************************
    //09_04_2017else if($_POST['tipo_solicitud'] == 'NUEVA'){
    ///********************************* TERMINA DICTAMEN POSITIVO RENOVACION*********************************************
    ///*********************************                                     *********************************************
      /*09_04_2017$updateSQL = sprintf("UPDATE solicitud_certificacion SET estatus_interno = %s, estatus_dspp = %s WHERE idsolicitud_certificacion = %s",
        GetSQLValueString($_POST['estatus_interno'], "int"),
        GetSQLValueString($estatus_dspp, "int"),
        GetSQLValueString($_POST['idsolicitud_certificacion'], "int"));
      $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

      $insertSQL = sprintf("INSERT INTO proceso_certificacion (idsolicitud_certificacion, estatus_interno, estatus_dspp, nombre_archivo, accion, archivo, fecha_registro) VALUES (%s, %s, %s, %s, %s, %s, %s)",
        GetSQLValueString($_POST['idsolicitud_certificacion'], "int"),
        GetSQLValueString($_POST['estatus_interno'], "int"),
        GetSQLValueString($estatus_dspp, "int"),
        GetSQLValueString($_POST['nombreArchivo'], "text"),
        GetSQLValueString($accion, "text"),
        GetSQLValueString($archivo, "text"),
        GetSQLValueString($fecha, "int"));
      $insertar = mysql_query($insertSQL,$dspp) or die(mysql_error());

      //creamos el monto del comprobante de pago
      $estatus_comprobante = "EN ESPERA";

      $insertSQL = sprintf("INSERT INTO comprobante_pago(estatus_comprobante, monto) VALUES (%s, %s)",
        GetSQLValueString($estatus_comprobante, "text"),
        GetSQLValueString($_POST['monto_membresia'], "text"));
      $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

      //capturamos el id del comprobante para vincularlo con la membresia
      $idcomprobante_pago = mysql_insert_id($dspp);

      //creamos la membresia
      $estatus_membresia = "EN ESPERA";
      $insertSQL = sprintf("INSERT INTO membresia(estatus_membresia, idopp, idsolicitud_certificacion, idcomprobante_pago) VALUES (%s, %s, %s, %s)",
        GetSQLValueString($estatus_membresia, "text"),
        GetSQLValueString($_POST['idopp'], "int"),
        GetSQLValueString($_POST['idsolicitud_certificacion'], "int"),
        GetSQLValueString($idcomprobante_pago, "int"));
      $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

      ///inicia envio de mensaje dictamen positivo
      if(isset($_POST['idioma'])){
        $row_documentacion = mysql_query("SELECT * FROM documentacion WHERE idestatus_interno = 8 AND idioma = '$_POST[idioma]'", $dspp) or die(mysql_error());
        while($documentacion = mysql_fetch_assoc($row_documentacion)){
            $mail->AddAttachment($documentacion['archivo']);
            $documentacion_nombres .= "<li>".$documentacion['nombre']."</li>";   
        }
      }else{
        $row_documentacion = mysql_query("SELECT * FROM documentacion WHERE idestatus_interno = 8 AND idioma = 'ESP'", $dspp) or die(mysql_error());
        while($documentacion = mysql_fetch_assoc($row_documentacion)){
            $mail->AddAttachment($documentacion['archivo']);
            $documentacion_nombres .= "<li>".$documentacion['nombre']."</li>";   
        }
      }

      if(!empty($archivo)){
        $documentacion_nombres .= '<li>'.$_POST['nombreArchivo'].'</li>';
        $mail->AddAttachment($archivo);
      }

      $documentacion = mysql_fetch_assoc($row_documentacion);

      $asunto = "D-SPP | NOTIFICACIÓN DE DICTAMEN";

      if(!empty($_POST['mensajeOPP'])){
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
                      <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">NOTIFICACIÓN DE DICTAMEN</span></p></th>

                    </tr>
                    <tr>
                     <th scope="col" align="left" width="280"><p>Para: <span style="color:red">'.$detalle_opp['nombre'].' - ('.$detalle_opp['abreviacion_opp'].')</span></p></th>
                    </tr>

                    <tr>
                      <td colspan="2" style="text-align:justify">
                        <p>'.$_POST['mensajeOPP'].'</p>
                      </td>
                    </tr>
                    <tr>
                      <td><p><strong>DOCUMENTOS ANEXOS</strong></p></td>
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
                        <p style="color:red"><strong>MEMBRESÍA SPP</strong></p>
                        <p>
                          ADICIONALMENTE SE SOLICITA DE LA MANERA MÁS ATENTA REALIZAR EL PAGO CORRESPONDIENTE A LA MEMBRESIA SPP POR EL IMPORTE DE: <span style="color:red;">'.$_POST['monto_membresia'].'</span>
                        </p>
                        <p>
                          LOS DATOS BANCARIOS SE ENCUENTRAN ANEXOS AL CORREO.
                        </p>
                        <p>
                          DESPUÉS DE REALIZAR EL PAGO POR FAVOR PROCEDA A CARGAR EL <span style="color:red">CONTRATO DE USO FIRMADO</span> ASÍ MISMO EL <span style="color:red">COMPROBANTE DE PAGO</span> POR MEDIO DEL SISTEMA D-SPP, ESTO INGRESANDO EN SU CUENTA DE OPP(Organización de Pequeños Productores) EN LA SIGUIENTE DIRECCIÓN <a href="http://d-spp.org/esp/?OPP">http://d-spp.org/esp/?OPP</a>.
                        </p>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="2">
                      <hr>
                        <p>En caso de cualquier duda o aclaración por favor escribir a <span style="color:red">cert@spp.coop</span> o <span style="color:red">'.$correos_oc['email1'].', '.$correos_oc['email2'].'</span></p>
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
                      <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">NOTIFICACIÓN DE DICTAMEN / NOTIFICATION OF RESOLUTION </span></p></th>

                    </tr>
                    <tr>
                     <th scope="col" align="left" width="280"><p>Para: <span style="color:red">'.$detalle_opp['nombre'].' - ('.$detalle_opp['abreviacion_opp'].')</span></p></th>
                    </tr>
                    <tr>
                      <td colspan="2" style="text-align:justify">
                        <p>1. Nosotros <span style="color:red">'.$detalle_oc['nombre'].'</span>, como Organismo de Certificación autorizado por SPP Global, nos complace informar por este medio que la evaluación SPP fue concluida con resultado positivo.</p>
                        <p>
                          2. Para concluir el proceso, se solicita de la manera más atenta leer los documentos anexos y posteriormente <span style="color:red">firmar el Contrato de Uso y Acuse de Recibo</span>.
                        </p>
                      </td>
                    </tr>

                    <tr>
                      <td colspan="2" style="text-align:justify">
                        <p>
                          1.  We, <span style="color:red">'.$detalle_oc['nombre'].'</span>, as a Certification Entity authorized by SPP Global, are pleased to inform you, by this means, that the SPP evaluation has been concluded with a “positive” result
                        <p>
                          2. In order to complete the process, the most careful request is to read the attached documents and subsequently <span style="color:red">sign the User´s Contract and Acknowledgment of Receipt</span>.
                        </p>
                      </td>
                    </tr>

                    <tr>
                      <td><p><strong>DOCUMENTOS ANEXOS /  ATTACHED DOCUMENTS  </strong></p></td>
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
                        <p style="color:red"><strong>MEMBRESÍA SPP</strong></p>
                        <p>
                          Adicionalmente se solicita de la manera más atenta, se proceda con el <span style="color:red">pago de membresía a SPP Global</span>, de acuerdo al monto indicado de: <strong style="color:red;">'.$_POST['monto_membresia'].'</strong>. (Se anexan los datos bancarios, favor de leer las Disposiciones Generales de Pago para evitar se generen intereses). Una vez que haya realizado el pago, favor de <span style="color:red">entrar a su cuenta y cargar el comprobante bancario</span>.
                        </p>
                        <p>
                          LOS DATOS BANCARIOS SE ENCUENTRAN ANEXOS AL CORREO.
                        </p>


                        <p>
                          DESPUÉS DE REALIZAR EL PAGO POR FAVOR PROCEDA A CARGAR EL <span style="color:red">CONTRATO DE USO FIRMADO</span> ASÍ MISMO EL <span style="color:red">COMPROBANTE DE PAGO</span> POR MEDIO DEL SISTEMA D-SPP, ESTO INGRESANDO EN SU CUENTA DE OPP(Organización de Pequeños Productores) EN LA SIGUIENTE DIRECCIÓN <a href="http://d-spp.org/esp/?OPP">http://d-spp.org/esp/?OPP</a>.
                        </p>
                        <p>
                          3. Una vez que SPP Global confirme a través del Sistema la recepción de los documentos y la recepción del pago en la cuenta de SPP Global, procederemos a hacer entrega del Certificado.
                        </p>

                      </td>
                    </tr>

                    <tr>
                      <td colspan="2" style="text-align:justify">
                        <p style="color:red"><strong>SPP MEMBERSHIP</strong></p>
                        <p>
                          In order to complete the process, you are asked to please proceed with <span style="color:red">payment of the membership fee to SPP Global</span>, in the following amount: <strong style="color:red;">'.$_POST['monto_membresia'].'</strong>. (Bank information is attached. Please read the General Payment Provisions to avoid interest charges.) After payment has been made, please <span style="color:red">enter your account in the D-SPP system and upload the bank receipt</span>.
                        </p>
                        <p>
                          • BANK INFORMATION IS ATTACHED TO EMAIL
                        </p>


                        <p>
                          AFTER MAKING PAYMENT, PLEASE <span style="color:red">UPLOAD “SIGNED USER’S CONTRACT AND ACKNOWLEDGEMENT OF RECEIPT.”</span> AND ALSO <span style="color:red">THE “RECEIPT OF PAYMENT”</span> THROUGH THE D-SPP SYSTEM, BY ENTERING YOUR ACCOUNT AS AN SPO (Small Producers’ Organization) IN THE FOLLOWING LINK: <a href="http://d-spp.org/esp/?OPP">http://d-spp.org/esp/?OPP</a>. 
                        </p>
                        <p>
                          3. Una vez que SPP Global confirme a través del Sistema la recepción de los documentos y la recepción del pago en la cuenta de SPP Global, procederemos a hacer entrega del Certificado.
                        </p>

                      </td>
                    </tr>

                    <tr>
                      <td colspan="2">
                      <hr>
                        <p>En caso de cualquier duda o aclaración por favor escribir a <span style="color:red">cert@spp.coop</span> o <span style="color:red">'.$correos_oc['email1'].', '.$correos_oc['email2'].'</span></p>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </body>
              </html>
        ';
      }
      if(!empty($detalle_opp['contacto1_email'])){
        $mail->AddAddress($detalle_opp['contacto1_email']);
      }
      if(!empty($detalle_opp['contacto2_email'])){
        $mail->AddAddress($detalle_opp['contacto2_email']);
      }
      if(!empty($detalle_opp['adm1_email'])){
        $mail->AddAddress($detalle_opp['adm1_email']);
      }
      if(!empty($detalle_opp['email'])){
        $mail->AddAddress($detalle_opp['email']);
      }    
      if(isset($correos_oc['email1'])){
        $mail->AddCC($correos_oc['email1']);
      }
      if(isset($correos_oc['email2'])){
        $mail->AddCC($correos_oc['email2']);
      }
      $mail->AddBCC($spp_global);
      $mail->AddBCC($finanzas_spp);
      $mail->Subject = utf8_decode($asunto);
      $mail->Body = utf8_decode($cuerpo_mensaje);
      $mail->MsgHTML(utf8_decode($cuerpo_mensaje));
      $mail->Send();
      $mail->ClearAddresses();
      $mail->ClearAttachments();
      /*if($mail->Send()){
        $mail->ClearAddresses();
        echo "<script>alert('Correo enviado Exitosamente.');location.href ='javascript:history.back()';</script>";
      }else{
        $mail->ClearAddresses();
        echo "<script>alert('Error, no se pudo enviar el correo, por favor contacte al administrador: soporte@d-spp.org');location.href ='javascript:history.back()';</script>";
      }*/

      /// INICIA MENSAJE "CARGAR DOCUMENTOS DE EVALUACIÓN"

/*09_04_2017      $asunto = "D-SPP | Formatos de Evaluación";

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
                <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">Cargar Formato, Dictamen e Informe de Evaluación</span></p></th>

              </tr>
              <tr>
               <th scope="col" align="left" width="280"><p>OPP: <span style="color:red">'.$informacion['nombre'].'</span></p></th>
              </tr>

              <tr>
                <td colspan="2">
                 <p>SPP GLOBLA notifica que la OPP: '.$informacion['nombre'].' ha cumplido con la documentación necesaria.</p>
                 <p>
                  Por favor procedan a ingresar en su cuenta de OC dentro del sistema D-SPP para poder cargar los siguientes documentos: 
                     <ul style="color:red">
                       <li>Formato de Evaluación</li>
                       <li>Informe de Evaluación</li>
                       <li>Dictamen de Evaluación</li>
                     </ul>

                 </p>
                </td>
              </tr>
              <tr>
                <td colspan="2">
                  ¿Pasos para cargar la documentación?. Para poder cargar la documentación debe seguir los siguiente pasos:
                  <ol>
                    <li>Dar clic en la opción "SOLICITUDES"</li>
                    <li>Seleccionar "Solicitudes OPP"</li>
                    <li>Posicionarse en la columna "Certificado" y dar clic en el boton "Cargar Certificado"</li>
                    <li>Se desplegara una ventan donde podra cargar la documentación</li>
                  </ol>
                  <p style="color:red">
                    Se notificara una vez que sea aprobada la documentación para poder cargar el certificado.
                  </p>
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
      if(isset($correos_oc['email1'])){
        $mail->AddCC($correos_oc['email1']);
      }
      if(isset($correos_oc['email2'])){
        $mail->AddCC($correos_oc['email2']);
      }

      $mail->Subject = utf8_decode($asunto);
      $mail->Body = utf8_decode($cuerpo_mensaje);
      $mail->MsgHTML(utf8_decode($cuerpo_mensaje));
      $mail->Send();
      $mail->ClearAddresses();
      $mail->ClearAttachments();

      /// TERMINA MENSAJE "CARGAR DOCUEMENTOS DE EVALUACIÓN"
      
          
      ///termina envio de mensaje dictamen positivo
    //09_04_2017}  09_04_2017*/
  }else{
    $updateSQL = sprintf("UPDATE solicitud_certificacion SET estatus_interno = %s, estatus_dspp = %s WHERE idsolicitud_certificacion = %s",
      GetSQLValueString($_POST['estatus_interno'], "int"),
      GetSQLValueString($estatus_dspp, "int"),
      GetSQLValueString($_POST['idsolicitud_certificacion'], "int"));
    $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

    $insertSQL = sprintf("INSERT INTO proceso_certificacion (idsolicitud_certificacion, estatus_interno, estatus_dspp, nombre_archivo, accion, archivo, fecha_registro) VALUES (%s, %s, %s, %s, %s, %s, %s)",
      GetSQLValueString($_POST['idsolicitud_certificacion'], "int"),
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
  $fecha_sistema = $_POST['fecha_sistema'];

  $estatus_formato = "ENVIADO";
  $estatus_dictamen = "ENVIADO";
  $estatus_informe = "ENVIADO";

  if(!empty($_FILES['formato_evaluacion']['name'])){
      $_FILES["formato_evaluacion"]["name"];
        move_uploaded_file($_FILES["formato_evaluacion"]["tmp_name"], $ruta_evaluacion.$fecha_sistema."_".$_FILES["formato_evaluacion"]["name"]);
        $formato = $ruta_evaluacion.basename($fecha_sistema."_".$_FILES["formato_evaluacion"]["name"]);
  }else{
    $formato = NULL;
  }
  //insertamos formato_evaluacion
  $insertSQL = sprintf("INSERT INTO formato_evaluacion (idopp, idsolicitud_certificacion, estatus_formato, archivo, fecha_registro) VALUES (%s, %s, %s, %s, %s)",
    GetSQLValueString($_POST['idopp'], "int"),
    GetSQLValueString($_POST['idsolicitud_certificacion'], "int"),
    GetSQLValueString($estatus_formato, "text"),
    GetSQLValueString($formato, "text"),
    GetSQLValueString($fecha_sistema, "int"));
  $insertar = mysql_query($insertSQL,$dspp) or die(mysql_error());

  //insertamos el proceso_certificacion
  $estatus_dspp = 22; //Formato de Evaluación Cargado
  $nombre_archivo = "Formato de Evaluación";
  $insertSQL = sprintf("INSERT INTO proceso_certificacion (idsolicitud_certificacion, estatus_dspp, nombre_archivo, archivo, fecha_registro) VALUES (%s, %s, %s, %s, %s)",
    GetSQLValueString($_POST['idsolicitud_certificacion'], "int"),
    GetSQLValueString($estatus_dspp, "int"),
    GetSQLValueString($nombre_archivo, "text"),
    GetSQLValueString($formato, "text"),
    GetSQLValueString($fecha_sistema, "int"));
  $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

  if(!empty($_FILES['informe_evaluacion']['name'])){
      $_FILES["informe_evaluacion"]["name"];
        move_uploaded_file($_FILES["informe_evaluacion"]["tmp_name"], $ruta_evaluacion.$fecha_sistema."_".$_FILES["informe_evaluacion"]["name"]);
        $informe = $ruta_evaluacion.basename($fecha_sistema."_".$_FILES["informe_evaluacion"]["name"]);
  }else{
    $informe = NULL;
  }

  //insertamos informe_evaluacion
  $insertSQL = sprintf("INSERT INTO informe_evaluacion (idopp, idsolicitud_certificacion, estatus_informe, archivo, fecha_registro) VALUES (%s, %s, %s, %s, %s)",
    GetSQLValueString($_POST['idopp'], "int"),
    GetSQLValueString($_POST['idsolicitud_certificacion'], "int"),
    GetSQLValueString($estatus_informe, "text"),
    GetSQLValueString($informe, "text"),
    GetSQLValueString($fecha_sistema, "int"));
  $insertar = mysql_query($insertSQL,$dspp) or die(mysql_error());

  //insertamos el proceso_certificacion
  $estatus_dspp = 20; //Informe de Evaluación Cargado
  $nombre_archivo = "Informe de Evaluación";
  $insertSQL = sprintf("INSERT INTO proceso_certificacion (idsolicitud_certificacion, estatus_dspp, nombre_archivo, archivo, fecha_registro) VALUES (%s, %s, %s, %s, %s)",
    GetSQLValueString($_POST['idsolicitud_certificacion'], "int"),
    GetSQLValueString($estatus_dspp, "int"),
    GetSQLValueString($nombre_archivo, "text"),
    GetSQLValueString($informe, "text"),
    GetSQLValueString($fecha, "int"));
  $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

  if(!empty($_FILES['dictamen_evaluacion']['name'])){
      $_FILES["dictamen_evaluacion"]["name"];
        move_uploaded_file($_FILES["dictamen_evaluacion"]["tmp_name"], $ruta_evaluacion.$fecha_sistema."_".$_FILES["dictamen_evaluacion"]["name"]);
        $dictamen = $ruta_evaluacion.basename($fecha_sistema."_".$_FILES["dictamen_evaluacion"]["name"]);
  }else{
    $dictamen = NULL;
  }

  //insertarmos el dictamen de evaluación
  $insertSQL = sprintf("INSERT INTO dictamen_evaluacion(idopp, idsolicitud_certificacion, estatus_dictamen, archivo, fecha_registro) VALUES (%s, %s, %s, %s, %s)",
    GetSQLValueString($_POST['idopp'], "int"),
    GetSQLValueString($_POST['idsolicitud_certificacion'], "int"),
    GetSQLValueString($estatus_dictamen, "text"),
    GetSQLValueString($dictamen, "text"),
    GetSQLValueString($fecha_sistema, "int"));
  $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

  //se crear el proceso_certificacion
  $estatus_dspp = 21; //Dictamen de Evaluación Cargado
  $nombre_archivo = "Dictamen de Evaluación";
  $insertSQL = sprintf("INSERT INTO proceso_certificacion(idsolicitud_certificacion, estatus_dspp, nombre_archivo, archivo, fecha_registro) VALUES (%s, %s, %s, %s, %s)",
    GetSQLValueString($_POST['idsolicitud_certificacion'], "int"),
    GetSQLValueString($estatus_dspp, "int"),
    GetSQLValueString($nombre_archivo, "text"),
    GetSQLValueString($dictamen, "text"),
    GetSQLValueString($fecha_sistema, "int"));
  $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

  //inicia enviar correo a ADM sobre documentacion de Evaluación
  $row_informacion = mysql_query("SELECT solicitud_certificacion.idsolicitud_certificacion, opp.nombre AS 'nombre_opp', opp.abreviacion AS 'abreviacion_opp', oc.nombre AS 'nombre_oc' FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp INNER JOIN oc ON solicitud_certificacion.idoc = oc.idoc WHERE idsolicitud_certificacion = $_POST[idsolicitud_certificacion]", $dspp) or die(mysql_error());
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
             <th scope="col" align="left" width="280"><p>OPP: <span style="color:red">'.$informacion['nombre_opp'].' - ('.$informacion['abreviacion_opp'].')</span></p></th>
            </tr>

            <tr>
              <td colspan="2">
               <p>El OC: <span style="color:red">'.$informacion['nombre_oc'].'</span> ha cargado la documentación de evaluación correspondiente al proceso de certificación de la OPP: '.$informacion['nombre_opp'].'
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
                <p><span style="color:red">Una vez que cargue los documentos, puede cargar el Certificado SPP.</span></p>
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
    //$mail->Send();
    //$mail->ClearAddresses();
    if($mail->Send()){
      $mail->ClearAddresses();
      echo "<script>alert('Se ha enviado el Formato, Dictamen e Informe de Evaluación, en breve sera contactado.');location.href ='javascript:history.back()';</script>";
    }else{
      $mail->ClearAddresses();
      echo "<script>alert('Error, no se pudo enviar el correo, por favor contacte al administrador: soporte@d-spp.org');location.href ='javascript:history.back()';</script>";
    }
    $mail->ClearAttachments();
  //termina enviar correo a ADM sobre documentación de Evaluación

  //$mensaje = "Se ha enviado el Formato, Dictamen e Informe de Evaluación";

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
  $fecha_inicio = strtotime($_POST['fecha_inicio']);
  $fecha_fin = strtotime($_POST['fecha_fin']);
  $vigencia_inicio = date('Y-m-d', $fecha_inicio);
  $vigencia_fin = date('Y-m-d', $fecha_fin);

  //insertamos el certificado
  $insertSQL = sprintf("INSERT INTO certificado(idopp, idsolicitud_certificacion, entidad, estatus_certificado, vigencia_inicio, vigencia_fin, archivo, fecha_registro) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
    GetSQLValueString($_POST['idopp'], "int"),
    GetSQLValueString($_POST['idsolicitud_certificacion'], "int"),
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
  $insertSQL = sprintf("INSERT INTO proceso_certificacion(idsolicitud_certificacion, estatus_dspp, nombre_archivo, archivo, fecha_registro) VALUES (%s, %s, %s, %s, %s)",
    GetSQLValueString($_POST['idsolicitud_certificacion'], "int"),
    GetSQLValueString($estatus_proceso, "int"),
    GetSQLValueString($nombre_archivo, "text"),
    GetSQLValueString($certificado, "text"),
    GetSQLValueString($fecha, "int"));
  $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());


  $updateSQL = sprintf("UPDATE solicitud_certificacion SET estatus_interno = %s, estatus_dspp = %s WHERE idsolicitud_certificacion = %s",
    GetSQLValueString(8, "int"),
    GetSQLValueString($estatus_proceso, "int"),
    GetSQLValueString($_POST['idsolicitud_certificacion'], "int"));
  $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

  //ACTUALIZAMOS A LA OPP
  $estatus_dspp = 13; //certificada
  $estatus_interno = 8; //dictamen_positivo
  $estatus_publico = 2; //certificado
  $estatus_opp = "CERTIFICADO";
  $updateSQL = sprintf("UPDATE opp SET estatus_opp = %s, estatus_publico = %s, estatus_interno = %s, estatus_dspp = %s WHERE idopp = %s",
    GetSQLValueString($estatus_opp, "text"),
    GetSQLValueString($estatus_publico, "int"),
    GetSQLValueString($estatus_interno, "int"),
    GetSQLValueString($estatus_dspp, "int"),
    GetSQLValueString($_POST['idopp'], "int"));
  $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

  //inicia correo envio de certificado
  $row_informacion = mysql_query("SELECT solicitud_certificacion.idopp, solicitud_certificacion.idoc, oc.email1 AS 'oc_email1', oc.email2 AS 'oc_email2', solicitud_certificacion.contacto1_email, opp.nombre AS 'nombre_opp', opp.abreviacion AS 'abreviacion_opp', opp.email FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp INNER JOIN oc ON solicitud_certificacion.idoc = oc.idoc WHERE idsolicitud_certificacion = $_POST[idsolicitud_certificacion]", $dspp) or die(mysql_error());
  $informacion = mysql_fetch_assoc($row_informacion);

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
                <th rowspan="2" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">Certificado Disponible para descargar / Certificate Ready to be Downloaded</span></p></th>

              </tr>
              <tr>
               <th scope="col" align="left" width="280"><p>OPP: <span style="color:red">'.$informacion['nombre_opp'].' - ('.$informacion['abreviacion_opp'].')</span></p></th>
              </tr>

              <tr>
                <td colspan="2">
                 <p>
                  Felicidades!!!, su Certificado ha sido liberado, ahora puede descargarlo.
                 </p>
                 <p>El Certificado tiene un vigencia del dia <span style="color:red">'.date('d/m/Y', $fecha_inicio).'</span> al dia: <span style="color:red">'.date('d/m/Y', $fecha_fin).'</span>, el cual se encuentra anexo a este correo.</p>
                 
                </td>
              </tr>

              <tr>
                <td colspan="2">
                 <p>
                  • Congratulations!!! Your Certificate has been issued and can now be downloaded.
                 </p>
                 <p>
                 •  The Certificate is in effect from <span style="color:red">'.date('d/m/Y', $fecha_inicio).'</span> to <span style="color:red">'.date('d/m/Y', $fecha_fin).'</span>, and is attached to this email.
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
    if($idoc != 19 && $idoc != 15){
        $mail->AddAddress($informacion['email']);
        $mail->AddAddress($informacion['contacto1_email']);
        if(isset($informacion['oc_email1'])){
          $token = strtok($informacion['oc_email1'], "\/\,\;");
          while ($token !== false)
          {
            $mail->AddCC($token);
            $token = strtok('\/\,\;');
          }

        }
        if(isset($informacion['oc_email2'])){
          $token = strtok($informacion['oc_email2'], "\/\,\;");
          while ($token !== false)
          {
            $mail->AddCC($token);
            $token = strtok('\/\,\;');
          }

        }
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
    $mail->ClearAttachments();



  //termina correo envio de certificado

  $mensaje = "Se ha cargado el Certificado y se ha notificado a SPP GLOBAL y a la Organización de Pequeños Productores";
}


$currentPage = $_SERVER["PHP_SELF"];

$maxRows_opp = 20;
$pageNum_opp = 0;
if (isset($_GET['pageNum_opp'])) {
  $pageNum_opp = $_GET['pageNum_opp'];
}
$startRow_opp = $pageNum_opp * $maxRows_opp;

//$query = "SELECT solicitud_certificacion.idsolicitud_certificacion AS 'idsolicitud', solicitud_certificacion.tipo_solicitud, solicitud_certificacion.idopp, solicitud_certificacion.idoc, solicitud_certificacion.fecha_registro, solicitud_certificacion.fecha_aceptacion, solicitud_certificacion.cotizacion_opp, solicitud_certificacion.contacto1_email, solicitud_certificacion.contacto2_email, opp.nombre AS 'nombre_opp', opp.abreviacion AS 'abreviacion_opp', opp.email, proceso_certificacion.idproceso_certificacion, proceso_certificacion.estatus_publico, proceso_certificacion.estatus_interno, proceso_certificacion.estatus_dspp, estatus_publico.nombre AS 'nombre_publico', estatus_interno.nombre AS 'nombre_interno', estatus_dspp.nombre AS 'nombre_dspp', periodo_objecion.*, membresia.idmembresia, membresia.estatus_membresia, contratos.idcontrato, contratos.estatus_contrato, certificado.idcertificado, formato_evaluacion.idformato_evaluacion, dictamen_evaluacion.iddictamen_evaluacion, informe_evaluacion.idinforme_evaluacion FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp LEFT JOIN proceso_certificacion ON solicitud_certificacion.idsolicitud_certificacion = proceso_certificacion.idsolicitud_certificacion LEFT JOIN estatus_publico ON proceso_certificacion.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON proceso_certificacion.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON proceso_certificacion.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN periodo_objecion ON solicitud_certificacion.idsolicitud_certificacion = periodo_objecion.idsolicitud_certificacion LEFT JOIN membresia ON solicitud_certificacion.idsolicitud_certificacion = membresia.idsolicitud_certificacion LEFT JOIN contratos ON solicitud_certificacion.idsolicitud_certificacion = contratos.idsolicitud_certificacion LEFT JOIN certificado ON solicitud_certificacion.idopp = certificado.idopp LEFT JOIN dictamen_evaluacion ON solicitud_certificacion.idsolicitud_certificacion = dictamen_evaluacion.idsolicitud_certificacion LEFT JOIN informe_evaluacion ON solicitud_certificacion.idsolicitud_certificacion = informe_evaluacion.idsolicitud_certificacion LEFT JOIN formato_evaluacion ON solicitud_certificacion.idsolicitud_certificacion = formato_evaluacion.idsolicitud_certificacion WHERE solicitud_certificacion.idoc = $idoc GROUP BY proceso_certificacion.estatus_dspp ORDER BY proceso_certificacion.estatus_dspp DESC";

$query = "SELECT solicitud_certificacion.*, solicitud_certificacion.idopp, solicitud_certificacion.idsolicitud_certificacion AS 'idsolicitud', oc.abreviacion AS 'abreviacionOC', opp.nombre AS 'nombre_opp', opp.abreviacion AS 'abreviacion_opp', opp.email, periodo_objecion.idperiodo_objecion, periodo_objecion.fecha_inicio, periodo_objecion.fecha_fin, periodo_objecion.estatus_objecion, periodo_objecion.observacion, periodo_objecion.dictamen, periodo_objecion.documento, membresia.idmembresia, certificado.idcertificado, contratos.idcontrato, contratos.estatus_contrato, informe_evaluacion.idinforme_evaluacion, formato_evaluacion.idformato_evaluacion, dictamen_evaluacion.iddictamen_evaluacion FROM solicitud_certificacion INNER JOIN oc ON solicitud_certificacion.idoc = oc.idoc INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp LEFT JOIN periodo_objecion ON solicitud_certificacion.idsolicitud_certificacion  = periodo_objecion.idsolicitud_certificacion LEFT JOIN membresia ON solicitud_certificacion.idsolicitud_certificacion = membresia.idsolicitud_certificacion LEFT JOIN certificado ON solicitud_certificacion.idsolicitud_certificacion = certificado.idsolicitud_certificacion LEFT JOIN contratos ON solicitud_certificacion.idsolicitud_certificacion = contratos.idsolicitud_certificacion LEFT JOIN informe_evaluacion ON solicitud_certificacion.idsolicitud_certificacion = informe_evaluacion.idsolicitud_certificacion LEFT JOIN formato_evaluacion ON solicitud_certificacion.idsolicitud_certificacion = formato_evaluacion.idsolicitud_certificacion LEFT JOIN dictamen_evaluacion ON solicitud_certificacion.idsolicitud_certificacion = dictamen_evaluacion.idsolicitud_certificacion WHERE solicitud_certificacion.idoc = $idoc GROUP BY solicitud_certificacion.idsolicitud_certificacion  ORDER BY solicitud_certificacion.fecha_registro DESC";

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
          <th class="text-center"><a href="#" data-toggle="tooltip" title="Tipo de Solicitud"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>Type</a></th>
          <th class="text-center">Application Date</th>
          <th class="text-center">CE</th>
          <th class="text-center">SPO</th>
          <th class="text-center">Application status</th>
          <th class="text-center">Quotation <br>(downloadable)</th>
          <th class="text-center">Objection Process</th>
          <th class="text-center"><a href="#" data-toggle="tooltip" title="The CE must update the status of the process by choosing one of the options that is displayed in this section"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>Certification<br>Process</a></th>
          <th class="text-center">Certificate</th>
          <th class="text-center" colspan="2">Actions</th>
        </tr>       
      </thead>
      <tbody>
      <?php 
      while($solicitud = mysql_fetch_assoc($row_solicitud)){
          $query_proceso = "SELECT proceso_certificacion.*, proceso_certificacion.idsolicitud_certificacion, estatus_publico.idestatus_publico, estatus_publico.nombre_ingles AS 'nombre_publico', estatus_interno.idestatus_interno, estatus_interno.nombre_ingles AS 'nombre_interno', estatus_dspp.idestatus_dspp, estatus_dspp.nombre_ingles AS 'nombre_dspp', membresia.idmembresia, membresia.estatus_membresia, membresia.idcomprobante_pago, membresia.fecha_registro FROM proceso_certificacion LEFT JOIN estatus_publico ON proceso_certificacion.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON proceso_certificacion.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON proceso_certificacion.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN membresia ON proceso_certificacion.idsolicitud_certificacion = membresia.idsolicitud_certificacion WHERE proceso_certificacion.idsolicitud_certificacion =  $solicitud[idsolicitud] ORDER BY proceso_certificacion.idproceso_certificacion DESC LIMIT 1";
          $ejecutar = mysql_query($query_proceso,$dspp) or die(mysql_error());
          $proceso_certificacion = mysql_fetch_assoc($ejecutar);

      ?>
        <tr <?php if($proceso_certificacion['estatus_dspp'] == 12){ echo "class='success'"; }else if($proceso_certificacion['estatus_interno'] == 9){ echo "class='danger'"; } ?>>
          <td>
            <?php echo $solicitud['idsolicitud']; ?>
            <input type="hidden" name="idsolicitud_certificacion" value="<?php echo $solicitud['idsolicitud']; ?>">
            <input type="hidden" name="tipo_solicitud" value="<?php echo $solicitud['tipo_solicitud']; ?>">
          </td>
          <td <?php if($solicitud['tipo_solicitud'] == 'NUEVA'){ echo "class='success'"; }else{ echo "class='warning'"; } ?>class="warning">
            <?php echo $solicitud['tipo_solicitud']; ?>
          </td>
          <td>
            <?php echo date('d/m/Y', $solicitud['fecha_registro']); ?>
          </td>
          <td>
            <?php echo $solicitud['abreviacionOC']; ?>
          </td>
          <td>
            <a href="?OPP&detail&idopp=<?php echo $solicitud['idopp']; ?>"><?php echo $solicitud['abreviacion_opp']; ?></a>
          </td>
          <td>
            <?php 
            if(isset($proceso_certificacion['estatus_dspp'])){
              echo $proceso_certificacion['nombre_dspp'];
            }else{
              echo "Not available";
            }
             ?>
          </td>

          <td>
            <?php
            if(isset($solicitud['cotizacion_opp'])){
            ?>
              <div class="btn-group" role="group" aria-label="...">
                <button type="button" class="btn btn-sm btn-default" data-toggle="modal" data-target="<?php echo "#cotizacion".$solicitud['idsolicitud_certificacion']; ?>" title="Reemplazar Cotización"><span class="glyphicon glyphicon-refresh" aria-hidden="true"></span></button>
                <a href='<?php echo $solicitud['cotizacion_opp']; ?>' class='btn btn-sm btn-success' style='font-size:12px;color:white;height:30px;' target='_blank'><span class='glyphicon glyphicon-download' aria-hidden='true'></span> Download quotation</a>
              </div>
            <?php
               if($proceso_certificacion['estatus_dspp'] == 5){ // SE ACEPTA LA COTIZACIÓN
                echo "<p class='alert alert-success' style='padding:7px;'>Status: ".$proceso_certificacion['nombre_dspp']."</p>"; 
               }else if($proceso_certificacion['estatus_dspp'] == 17){ // SE RECHAZA LA COTIZACIÓN
                echo "<p class='alert alert-danger' style='padding:7px;'>Status: ".$proceso_certificacion['nombre_dspp']."</p>"; 
               }else{
                echo "<p class='alert alert-info' style='padding:7px;'>Status: ".$proceso_certificacion['nombre_dspp']."</p>"; 
               }

            }else{ // INICIA CARGAR COTIZACIÓN
              echo "Not available";
            } // TERMINA CARGAR COTIZACIÓN
             ?>

            <!-- Modal -->
            <div class="modal fade" id="<?php echo "cotizacion".$solicitud['idsolicitud_certificacion']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
              <div class="modal-dialog" role="document">
                <form action="" method="POST" enctype="multipart/form-data">
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      <h4 class="modal-title" id="myModalLabel">Replace Current quotation</h4>
                    </div>
                    <div class="modal-body">
                      <div class="form-group">
                        <label for="nueva_cotizacion">New quotation</label>
                        <input type="file" id="nueva_cotizacion" name="nueva_cotizacion">
                        <input type="hidden" name="cotizacion_actual" value="<?php echo $solicitud['cotizacion_opp']; ?>">
                        <input type="hidden" name="idsolicitud_certificacion" value="<?php echo $solicitud['idsolicitud_certificacion']; ?>">
                      </div>
                    </div>

                    <div class="modal-footer">
                      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                      <button type="submit" name="reemplazar_cotizacion" value="1" class="btn btn-primary">Replace quotation</button>
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
              <a href="#" data-toggle="tooltip" title="This application is in Process of Renewal of the Registry therefore does not apply the period of objection" style="padding:7px;"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>APPLICATION FOR RENEWAL</a>
            <?php
            }else{
              if(isset($solicitud['idperiodo_objecion']) && $solicitud['estatus_objecion'] != 'EN ESPERA'){
              ?>
                <p class="alert alert-info" style="margin-bottom:0;padding:0px;">Start: <?php echo date('d/m/Y', $solicitud['fecha_inicio']); ?></p>
                <p class="alert alert-danger" style="margin-bottom:0;padding:0px;">End: <?php echo date('d/m/Y', $solicitud['fecha_fin']); ?></p>
                <?php 
                if(isset($solicitud['documento'])){
                ?>
                  <p class="alert alert-success" style="margin-bottom:0;padding:0px;">Resolution: <?php echo $solicitud['dictamen']; ?></p>
                  <a class="btn btn-info" style="font-size:12px;width:100%;height:30px;" href='<?php echo $solicitud['documento']; ?>' target='_blank'><span class='glyphicon glyphicon-download' aria-hidden='true'></span> Download resolution</a> 
                <?php
                }
                ?>
              <?php
              }else{
                echo "Not available";
              }
            }
            ?>
          </td>
          <!---- INICIA PROCESO DE CERTIFICACIÓN ---->
          <td>
            <form action="" method="POST" enctype="multipart/form-data">
              <?php 
              if(isset($solicitud['dictamen']) && $solicitud['dictamen'] == 'POSITIVO' || ($solicitud['tipo_solicitud']) == 'RENOVACION' && !empty($solicitud['fecha_aceptacion'])){
              ?>
                <button type="button" class="btn btn-sm btn-primary" style="width:100%" data-toggle="modal" data-target="<?php echo "#certificacion".$solicitud['idsolicitud_certificacion']; ?>">Certification process</button>

                <!-- inicia modal proceso de certificación -->
                <div id="<?php echo "certificacion".$solicitud['idsolicitud_certificacion']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                  <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Process of certification</h4>
                      </div>
                      <div class="modal-body"><!-- INICIA MODAL BODY -->
                        <div class="row"><!--INICIA ROW-->
                          <?php 

                          $row_proceso_certificacion = mysql_query("SELECT proceso_certificacion.*, estatus_interno.nombre_ingles FROM proceso_certificacion INNER JOIN estatus_interno ON proceso_certificacion.estatus_interno = estatus_interno.idestatus_interno WHERE proceso_certificacion.idsolicitud_certificacion = '$solicitud[idsolicitud]' AND proceso_certificacion.estatus_interno IS NOT NULL", $dspp) or die(mysql_error());
                          while($historial_certificacion = mysql_fetch_assoc($row_proceso_certificacion)){
                            echo "<div class='col-md-10'>Process: $historial_certificacion[nombre]</div>";
                            echo "<div class='col-md-2'>Date: ".date('d/m/Y',$historial_certificacion['fecha_registro'])."</div>";
                          }

                          if(!isset($solicitud['idcomprobante_pago'])){
                          ?>
                          <div class="col-md-12">
                            <select class="form-control" name="estatus_interno" id="<?php echo 'statusSelect'.$solicitud['idsolicitud']; ?>" onchange="<?php echo 'funcionSelect'.$solicitud['idsolicitud'].'()'; ?>">
                              <option value="">Select the process in which the certification is found</option>
                              <?php 
                              $row_estatus_interno = mysql_query("SELECT * FROM estatus_interno",$dspp) or die(mysql_error());
                              while($estatus_interno = mysql_fetch_assoc($row_estatus_interno)){
                                echo "<option value='$estatus_interno[idestatus_interno]'>$estatus_interno[nombre_ingles]</option>";
                              }
                               ?>
                            </select>                        
                          </div>

                          <?php
                          }
                          ?>

                              <div class="col-xs-12" id="<?php echo 'divSelect'.$solicitud['idsolicitud']; ?>" style="margin-top:10px;">
                                <div class="col-xs-6">
                                  <input style="display:none" id="<?php echo 'nombreArchivo'.$solicitud['idsolicitud']; ?>"  type='text' class='form-control' name='nombre_archivo' placeholder="File name"/>
                                </div>
                                <!-- INICIA CARGAR ARCHIVO ESTATUS -->
                                <div class="col-xs-6">
                                  <input style="display:none" id="<?php echo 'archivo_estatus'.$solicitud['idsolicitud']; ?>" type='file' class='form-control' name='archivo_estatus' />
                                </div>
                                <!-- TERMINA CARGAR ARCHIVO ESTATUS -->

                                <!-- INICIA ACCION PROCESO CERTIFICACION -->
                                <div class="col-md-12">
                                  <textarea id="<?php echo 'registroOculto'.$solicitud['idsolicitud']; ?>" style="display:none" name="mensaje_negativo" class="form-control textareaMensaje" cols="30" rows="10" placeholder="Write here"></textarea>  
                                </div>
                                

                                <!-- TERMINA ACCION PROCESO CERTIFICACION -->
                              </div>
                              
                              <!-------- INICIA VENTANA DICTAMEN POSITIVO ---------->
                              <div id="<?php echo 'tablaCorreo'.$solicitud['idsolicitud']; ?>" style="display:none"> 
                                <div class="col-xs-12"  style="margin-top:10px;">
                                  <?php 
                                  if($solicitud['tipo_solicitud'] == 'RENOVACION'){ ///  RENOVACIÓN
                                  ?>
                                    <p class="alert alert-info">The following format will be sent shortly to the SPO</p>
                                    <div class="col-xs-12">
                                      
                                      <div class="col-xs-12">
                                        <div class="col-xs-6"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></div>
                                        <div class="col-xs-6">
                                          <p>Message sent on: <?php echo date("d/m/Y", time()); ?></p>
                                          <p>To: <span style="color:red"><?php echo $solicitud['nombre_opp']; ?></span></p>
                                          <p>Email(s): <?php echo $solicitud['contacto1_email']." ".$solicitud['contacto2_email']." ".$solicitud['email']; ?></p>
                                          <p>Subject: <span style="color:red">Notification of the opinion - SPP</span></p>
                                          
                                        </div>
                                      </div>
                                      <div class="col-xs-12">
                                        <h4 style="font-size:14px;padding:5px;">SPO MESSAGE( <small style="font-size:13px;">The CE must write in the field below, the text on the Notification of the opinion and in case the opinion is positive, it must explain that the actor must read the attached documents and sign the User´s_Contract and   Confirmation of Receipt. <span style="color:red">If you do not write any message the system will send a default message <a href="dictamen_positivo.php?opp=<?php echo $solicitud['nombre_opp'];?>&renovacion_positivo&oc=<?php echo $solicitud['idoc']; ?>" target="ventana1" onclick="ventanaNueva ('', 500, 400, 'ventana1');">view message</a></span></small>)</h4>
                                        <textarea name="mensaje_renovacion" class="form-control textareaMensaje" id="" cols="30" rows="10" placeholder="Enter a message in case you wanted it"></textarea>

                                      </div>
                                    </div>

                                    <div class="col-xs-12">
                                      <div class="col-xs-12">
                                        <h4 style="font-size:14px;">ATTACHED FILES( <small>Attachments within email</small> )</h4>
                                        <?php 
                                          echo '<h5 style="font-size:12px;" class="alert alert-warning">
                                          <ul>
                                            <li>This Organization is in "Process of Renewal of the Certificate", therefore no "User´s_Contract" will be sent.</li>
                                            <li style="color:red;">The payment of the SPP Membership is considered a ratification of the signing of the contract.</li>
                                          </ul>
                                          </h5>';
                                         ?>
                                      </div>
                                      <div class="col-xs-12 alert alert-info">
                                        <h5 class="">SPP MEMBERSHIP: <span style="color:#7f8c8d">Indicate the total amount of the membership, as well as the type of currency.</span></h5>
                                        <?php 
                                        // calculamos el valor de la membresia SPP
                                        $num_productores = $solicitud['resp1'];
                                        if($num_productores <= 100){
                                          $valor_membresia = '$ 150 USD';
                                        }
                                        else if(($num_productores > 100) && ($num_productores <= 250)){
                                          $valor_membresia = '$ 187.50 USD';
                                        }
                                        else if(($num_productores > 250) && ($num_productores <= 500)){
                                          $valor_membresia = '$ 375.00 USD';
                                        }
                                        else if(($num_productores > 500) && ($num_productores <= 1000)){
                                          $valor_membresia = '$ 562.50 USD';
                                        }
                                        else if($num_productores > 1000){
                                          $valor_membresia = '$750.00 USD';
                                        }else{
                                          $valor_membresia = '';
                                        }
                                         ?>
                                        <input type="text" class="form-control" name="total_membresia" id="total_membresia_2"  value="<?php echo $valor_membresia; ?>" placeholder="Total Membership">
                                      </div>

                                      <div class="col-xs-12">
                                        <h4 style="font-size:14px;">EXTRA FILE( <small><span style="color:red">*optional</span>. Attach some other file in different to those sent by SPP GLOBAL</small>)</h4>
                                        <div class="col-xs-12">
                                          <input type="text" class="form-control" name="nombre_archivo_dictamen" placeholder="File name">
                                        </div>
                                        <div class="col-xs-12">
                                          <input type="file" class="form-control" name="archivo_dictamen">
                                        </div>
                                      </div>
                                    </div>

                                  <?php
                                  }else{ ///// PRIMERA VEZ
                                  ?>
                                    <p class="alert alert-info">The following format will be sent shortly to SPO 1: <?php echo $solicitud['idsolicitud'] ?> 2: <?php echo $solicitud['idsolicitud_certificacion']; ?></p>
                                    <div class="col-xs-12">
                                      
                                      <div class="col-xs-12">
                                        <div class="col-xs-6"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></div>
                                        <div class="col-xs-6">
                                          <p>Message sent on: <?php echo date("d/m/Y", time()); ?></p>
                                          <p>To: <span style="color:red"><?php echo $solicitud['nombre_opp']; ?></span></p>
                                          <p>Email(s): <?php echo $solicitud['contacto1_email']." , ".$solicitud['contacto2_email']." , ".$solicitud['email']; ?></p>
                                          <p>Subject: <span style="color:red">Notification of the opinion - SPP</span></p>
                                          
                                        </div>
                                      </div>
                                      <div class="col-xs-12">
                                        <h4 style="font-size:14px;padding:5px;">MESSAGE SPO( <small style="font-size:13px;">The CE must write in the field below, the text on the Notification of the opinion and in case the opinion is positive, it must explain that the actor must read the attached documents and sign the User´s_Contract and   Confirmation of Receipt. <span style="color:red">If you do not write any message the system will send a default message <a href="dictamen_positivo.php?opp=<?php echo $solicitud['nombre_opp'];?>&oc=<?php echo $solicitud['idoc']; ?>" target="ventana1" onclick="ventanaNueva ('', 500, 400, 'ventana1');">view message</a></span></small>)</h4>
                                        <textarea name="mensajeOPP" class="form-control textareaMensaje" id="" cols="30" rows="10" placeholder="Enter a message in case you wanted it"></textarea>

                                      </div>
                                    </div>
                                    <div class="col-xs-12">
                                      <div class="col-xs-12">
                                        <h4 style="font-size:14px;">ATTACHED FILES: <span style="color:#7f8c8d">Documentation sent to the actor once the Certification Process has completed with a Positive Judgement.</span></h4>
                                        <?php 
                                        $row_documentacion = mysql_query("SELECT * FROM documentacion WHERE idestatus_interno = 8 AND idioma = 'EN'", $dspp) or die(mysql_error());
                                        while($documentacion = mysql_fetch_assoc($row_documentacion)){

                                          echo "<span class='glyphicon glyphicon-ok' aria-hidden='true'></span> <a href='$documentacion[archivo]' target='_blank'>$documentacion[nombre]</a><br>";
                                        }
                                         ?>
                                        <p class="alert alert-warning">
                                          <strong>
                                            Select the language in which you want to send the <span style="color:red">SPP Manual, User´s_Contract and  Confirmation of Receipt</span>:
                                            <label class="radio-inline">
                                              <input type="radio" name="idioma" id="inlineRadio1" value="ESP"> Spanish
                                            </label>
                                            <label class="radio-inline">
                                              <input type="radio" name="idioma" id="inlineRadio2" value="EN"> English
                                            </label>
                                          </strong>
                                        </p>
      
                                      </div>
                                      <div class="col-xs-12">
                                        <h4 style="font-size:14px;">EXTRA FILE: <span style="color:#7f8c8d">Attach some other file if required.</span></h4>
                                        <input type="text" class="form-control" name="nombre_archivo_dictamen" placeholder="File name">
                                        <input type="file" class="form-control" name="archivo_dictamen">
                                        <hr>
                                      </div>

                                      <div class="col-xs-12 alert alert-info">
                                        <?php 
                                        $num_productores = $solicitud['resp1'];
                                        if($num_productores <= 100){
                                          $valor_membresia = '$ 150 USD';
                                        }
                                        else if(($num_productores > 100) && ($num_productores <= 250)){
                                          $valor_membresia = '$ 187.50 USD';
                                        }
                                        else if(($num_productores > 250) && ($num_productores <= 500)){
                                          $valor_membresia = '$ 375.00 USD';
                                        }
                                        else if(($num_productores > 500) && ($num_productores <= 1000)){
                                          $valor_membresia = '$ 562.50 USD';
                                        }
                                        else if($num_productores > 1000){
                                          $valor_membresia = '$750.00 USD';
                                        }else{
                                          $valor_membresia = '';
                                        }
                                         ?>

                                        <h4 style="font-size:14px;">SPP MEMBERSHIP: <span style="color:#7f8c8d">Indicate the total amount of the membership, as well as the type of currency.</span></h4>
                                        <input type="text" class="form-control" name="total_membresia" id="total_membresia" value="<?php echo $valor_membresia; ?>" placeholder="Total Membership">
                                      </div>
                                    </div>

                                  <?php
                                  }
                                  ?>
                                </div>
                              </div>
                              <!-------- TERMINA VENTANA DICTAMEN POSITIVO ---------->





                              <!-------- INICIA VENTANA DICTAMEN NEGATIVO ---------->
                              <div id="<?php echo 'tabla_negativo'.$solicitud['idsolicitud_certificacion']; ?>" style="display:none"> 
                                <div class="col-xs-12"  style="margin-top:10px;">
                                  <?php 
                                  if($solicitud['tipo_solicitud'] == 'RENOVACION'){
                                  ?>
                                    <p class="alert alert-info">The following format will be sent shortly to OPP 1: <?php echo $proceso_certificacion['idsolicitud_certificacion'] ?> 2: <?php echo $proceso_certificacion['idsolicitud_certificacion']; ?></p>
                                    <div class="col-xs-12">
                                      
                                      <div class="col-xs-12">
                                        <div class="col-xs-6"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></div>
                                        <div class="col-xs-6">
                                          <p>Message sent on: <?php echo date("d/m/Y", time()); ?></p>
                                          <p>To: <span style="color:red"><?php echo $solicitud['nombre_opp']; ?></span></p>
                                          <p>Email(s): <?php echo $solicitud['contacto1_email']." ".$solicitud['contacto2_email']." ".$solicitud['email']; ?></p>
                                          <p>Subject: <span style="color:red">Notification of the opinion - SPP</span></p>
                                          
                                        </div>
                                      </div>
                                      <div class="col-xs-12">
                                        <h4 style="font-size:14px;padding:5px;">SPO MESSAGE( <small style="font-size:13px;">The OC must write in the field below, the text on the Notification of the opinion. <span style="color:red">If you do not enter a message, the system will send a default message <a href="dictamen_positivo.php?opp=<?php echo $solicitud['nombre_opp'];?>&renovacion_negativo&oc=<?php echo $solicitud['idoc'] ?>" target="ventana1" onclick="ventanaNueva ('', 500, 400, 'ventana1');">view message</a></span></small>)</h4>
                                        <textarea name="mensaje_renovacion" class="form-control textareaMensaje" id="" cols="30" rows="10" placeholder="Enter a message in case you wanted it"></textarea>

                                      </div>
                                    </div>

                                    <div class="col-xs-12">
   

                                      <div class="col-xs-12">
                                        <h4 style="font-size:14px;">EXTRA FILE( <small><span style="color:red">*optional</span>. Attach some other file in different to those sent by SPP GLOBAL</small>)</h4>
                                        <div class="col-xs-12">
                                          <input type="text" class="form-control" name="nombreArchivo" placeholder="File name">
                                        </div>
                                        <div class="col-xs-12">
                                          <input type="file" class="form-control" name="archivo_extra">
                                        </div>
                                      </div>
                                    </div>

                                  <?php
                                  }else{
                                  ?>
                                    <p class="alert alert-info">The following format will be sent shortly to the SPO 1: <?php echo $proceso_certificacion['idsolicitud_certificacion'] ?> 2: <?php echo $proceso_certificacion['idsolicitud_certificacion']; ?></p>
                                    <div class="col-xs-12">
                                      
                                      <div class="col-xs-12">
                                        <div class="col-xs-6"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></div>
                                        <div class="col-xs-6">
                                          <p>Message sent on: <?php echo date("d/m/Y", time()); ?></p>
                                          <p>To: <span style="color:red"><?php echo $solicitud['nombre_opp']; ?></span></p>
                                          <p>Email(s): <?php echo $solicitud['contacto1_email']." , ".$solicitud['contacto2_email']." , ".$solicitud['email']; ?></p>
                                          <p>Subject: <span style="color:red">Notification of the opinion - SPP</span></p>
                                          
                                        </div>
                                      </div>
                                      <div class="col-xs-12">
                                        <h4 style="font-size:14px;padding:5px;">SPO MESSAGE( <small style="font-size:13px;">The OC must write in the field below, the text on the Notification of the opinion and in case the opinion is positive, it must explain that the actor must read the attached documents and sign the User´s_Contract and   Confirmation of Receipt. <span style="color:red">If you do not enter a message, the system will send a default message <a href="dictamen_positivo.php?opp=<?php echo $solicitud['nombre_opp'];?>&negativo&oc=<?php echo $solicitud['idoc']; ?>" target="ventana1" onclick="ventanaNueva ('', 500, 400, 'ventana1');">view message</a></span></small>)</h4>
                                        <textarea name="mensajeOPP" class="form-control textareaMensaje" id="" cols="30" rows="10" placeholder="Enter a message in case you wanted it"></textarea>

                                      </div>
                                    </div>
                                  <?php
                                  }
                                  ?>
                                </div>
                              </div>
                              <!-------- TERMINA VENTANA DICTAMEN NEGATIVO ---------->



                        </div><!--TERMINA ROW-->
                      </div><!-- TERMINA MODAL BODY -->

                      <?php
                          echo "<script>";
                          echo "function funcionSelect".$solicitud['idsolicitud']."() {";
                            echo "var valorSelect = document.getElementById('statusSelect".$solicitud['idsolicitud']."').value;";
                            echo "if(valorSelect == '4'){";
                              echo "document.getElementById('divSelect".$solicitud['idsolicitud']."').style.display = 'block';";
                              echo "document.getElementById('registroOculto".$solicitud['idsolicitud']."').style.display = 'block';";
                              echo "document.getElementById('nombreArchivo".$solicitud['idsolicitud']."').style.display = 'block';";
                              echo "document.getElementById('archivo_estatus".$solicitud['idsolicitud']."').style.display = 'block';";
                              echo "document.getElementById('tablaCorreo".$solicitud['idsolicitud']."').style.display = 'none';";
                              echo "document.getElementById('tabla_negativo".$solicitud['idsolicitud']."').style.display = 'none';";
                              echo "document.getElementById('boton1".$solicitud['idsolicitud']."').style.display = 'block';";
                              echo "document.getElementById('boton2".$solicitud['idsolicitud']."').style.display = 'none';";
                            echo "}else if(valorSelect == '6'){";
                              echo "document.getElementById('divSelect".$solicitud['idsolicitud']."').style.display = 'block';";
                              echo "document.getElementById('nombreArchivo".$solicitud['idsolicitud']."').style.display = 'block';";
                              echo "document.getElementById('archivo_estatus".$solicitud['idsolicitud']."').style.display = 'block';";
                              echo "document.getElementById('registroOculto".$solicitud['idsolicitud']."').style.display = 'block';";
                              echo "document.getElementById('tablaCorreo".$solicitud['idsolicitud']."').style.display = 'none';";
                              echo "document.getElementById('tabla_negativo".$solicitud['idsolicitud']."').style.display = 'none';";
                              echo "document.getElementById('boton1".$solicitud['idsolicitud']."').style.display = 'block';";
                              echo "document.getElementById('boton2".$solicitud['idsolicitud']."').style.display = 'none';";
                            echo "}else if(valorSelect == '7'){";
                              echo "document.getElementById('divSelect".$solicitud['idsolicitud']."').style.display = 'block';";
                              echo "document.getElementById('nombreArchivo".$solicitud['idsolicitud']."').style.display = 'none';";
                              echo "document.getElementById('archivo_estatus".$solicitud['idsolicitud']."').style.display = 'none';";
                              echo "document.getElementById('registroOculto".$solicitud['idsolicitud']."').style.display = 'block';";
                              echo "document.getElementById('tablaCorreo".$solicitud['idsolicitud']."').style.display = 'none';";
                              echo "document.getElementById('tabla_negativo".$solicitud['idsolicitud']."').style.display = 'none';";
                              echo "document.getElementById('boton1".$solicitud['idsolicitud']."').style.display = 'block';";
                              echo "document.getElementById('boton2".$solicitud['idsolicitud']."').style.display = 'none';";
                            echo "}else if(valorSelect == '8'){";
                              echo "document.getElementById('tablaCorreo".$solicitud['idsolicitud']."').style.display = 'block';"; 
                              echo "document.getElementById('nombreArchivo".$solicitud['idsolicitud']."').style.display = 'none';";
                              echo "document.getElementById('archivo_estatus".$solicitud['idsolicitud']."').style.display = 'none';";
                              echo "document.getElementById('registroOculto".$solicitud['idsolicitud']."').style.display = 'none';";
                              echo "document.getElementById('tabla_negativo".$solicitud['idsolicitud']."').style.display = 'none';";
                              echo "document.getElementById('boton1".$solicitud['idsolicitud']."').style.display = 'none';";
                              echo "document.getElementById('boton2".$solicitud['idsolicitud']."').style.display = 'block';";
                            echo "}else if(valorSelect == '9'){";
                              echo "document.getElementById('tabla_negativo".$solicitud['idsolicitud']."').style.display = 'block';"; 
                              echo "document.getElementById('nombreArchivo".$solicitud['idsolicitud']."').style.display = 'none';";
                              echo "document.getElementById('archivo_estatus".$solicitud['idsolicitud']."').style.display = 'none';";
                              echo "document.getElementById('tablaCorreo".$solicitud['idsolicitud']."').style.display = 'none';";
                              echo "document.getElementById('registroOculto".$solicitud['idsolicitud']."').style.display = 'none';";  
                              echo "document.getElementById('boton1".$solicitud['idsolicitud']."').style.display = 'block';";
                              echo "document.getElementById('boton2".$solicitud['idsolicitud']."').style.display = 'none';";
                            echo "}else if(valorSelect == '23'){";
                              echo "document.getElementById('divSelect".$solicitud['idsolicitud']."').style.display = 'block';";
                              echo "document.getElementById('tablaCorreo".$solicitud['idsolicitud']."').style.display = 'none';";
                              echo "document.getElementById('nombreArchivo".$solicitud['idsolicitud']."').style.display = 'none';"; 
                              echo "document.getElementById('archivo_estatus".$solicitud['idsolicitud']."').style.display = 'none';";
                              echo "document.getElementById('registroOculto".$solicitud['idsolicitud']."').style.display = 'block';";    
                              echo "document.getElementById('tabla_negativo".$solicitud['idsolicitud']."').style.display = 'none';";  
                              echo "document.getElementById('boton1".$solicitud['idsolicitud']."').style.display = 'block';";
                              echo "document.getElementById('boton2".$solicitud['idsolicitud']."').style.display = 'none';";
                            echo "}else{";
                              echo "document.getElementById('nombreArchivo".$solicitud['idsolicitud']."').style.display = 'none';";
                              echo "document.getElementById('archivo_estatus".$solicitud['idsolicitud']."').style.display = 'none';";
                              echo "document.getElementById('registroOculto".$solicitud['idsolicitud']."').style.display = 'none';";
                              echo "document.getElementById('tablaCorreo".$solicitud['idsolicitud']."').style.display = 'none';";
                              echo "document.getElementById('divSelect".$solicitud['idsolicitud']."').style.display = 'none';";
                              echo "document.getElementById('tabla_negativo".$solicitud['idsolicitud']."').style.display = 'none';";
                              echo "document.getElementById('boton1".$solicitud['idsolicitud']."').style.display = 'block';";
                              echo "document.getElementById('boton2".$solicitud['idsolicitud']."').style.display = 'none';";
                            echo "}";
                          echo "}";
                          echo "</script>";
                      ?>
                      <div class="modal-footer">
                        <input type="hidden" name="idsolicitud_certificacion" value="<?php echo $solicitud['idsolicitud_certificacion']; ?>">
                        <input type="hidden" name="idoc" value="<?php echo $solicitud['idoc']; ?>">
                        <input type="hidden" name="idopp" value="<?php echo $solicitud['idopp']; ?>">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        <?php 
                        if(empty($solicitud['idmembresia'])){
                        ?>
                        <button type="submit" class="btn btn-success" style="width:100%" id="<?php echo 'boton1'.$solicitud['idsolicitud_certificacion']; ?>" name="guardar_proceso" value="1">Save Process</button>
                        <?php 
                        if($solicitud['tipo_solicitud'] == 'RENOVACION'){
                        ?>
                        <button type="submit" class="btn btn-success" id="<?php echo 'boton2'.$solicitud['idsolicitud_certificacion']; ?>" name="guardar_proceso" value="1" onclick="return validarRenovacion()" style="width:100%; display:none" >Send Opinion</button>
                        <?php
                        }else{
                        ?>
                        <button type="submit" class="btn btn-success" id="<?php echo 'boton2'.$solicitud['idsolicitud_certificacion']; ?>" name="guardar_proceso" value="1" onclick="return validar()" style="width:100%; display:none" >Send Opinion</button>
                        <?php
                        }
                         ?>
                        
                        <?php
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
                echo "Not available";
              }
               ?>
               <input type="hidden" name="tipo_solicitud" value="<?php echo $solicitud['tipo_solicitud']; ?>">
               <input type="hidden" name="idsolicitud_certificacion" value="<?php echo $solicitud['idsolicitud']; ?>">
            </form>
          </td>
          <!---- TERMINA PROCESO DE CERTIFICACIÓN ---->

          <!---- INICIA SECCION CERTIFICADO ------>
          <form action="" method="POST" enctype="multipart/form-data">
          <td>
            <button type="button" class="btn btn-sm btn-info" style="width:100%" data-toggle="modal" data-target="<?php echo "#certificado".$solicitud['idsolicitud']; ?>">Upload Certificate</button>
          </td>
                <!-- inicia modal estatus_Certificado -->

                <div id="<?php echo "certificado".$solicitud['idsolicitud']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                  <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="myModalLabel">Upload Certificate</h4>
                      </div>
                      <div class="modal-body">
                        <div class="row">
                          <div class="col-md-6">
                            <?php 
                            if($solicitud['tipo_solicitud'] == 'RENOVACION'){
                              if($proceso_certificacion['estatus_membresia'] == "APROBADA"){
                                if(isset($solicitud['idformato_evaluacion']) && isset($solicitud['idinforme_evaluacion']) && isset($solicitud['iddictamen_evaluacion'])){

                                  $row_formato = mysql_query("SELECT * FROM formato_evaluacion WHERE idformato_evaluacion = $solicitud[idformato_evaluacion]", $dspp) or die(mysql_error());
                                  $formato = mysql_fetch_assoc($row_formato);
                                  $row_informe = mysql_query("SELECT * FROM informe_evaluacion WHERE idinforme_evaluacion = $solicitud[idinforme_evaluacion]", $dspp) or die(mysql_error());
                                  $informe = mysql_fetch_assoc($row_informe);
                                  $row_dictamen = mysql_query("SELECT * FROM dictamen_evaluacion WHERE iddictamen_evaluacion = $solicitud[iddictamen_evaluacion]", $dspp) or die(mysql_error());
                                  $dictamen = mysql_fetch_assoc($row_dictamen);

                                ?>
                                <p>Status: Evaluation Format: <span style="color:red"><?php echo $formato['estatus_formato']; ?></span></p>
                                <a href="<?php echo $formato['archivo']; ?>" class="btn btn-info" style="width:100%" target="_blank">Download Evaluation Format</a>

                                <p>Status Evaluation Opinion: <span style="color:red"><?php echo $dictamen['estatus_dictamen']; ?></span></p>
                                <a href="<?php echo $dictamen['archivo']; ?>" class="btn btn-info" style="width:100%" target="_blank">Download opinion</a>
                                <p>Status Evaluation Report: <span style="color:red"><?php echo $informe['estatus_informe']; ?></span></p>
                                <a href="<?php echo $informe['archivo']; ?>" class="btn btn-info" style="width:100%" target="_blank">Download Report</a>

                                <?php
                                }else{
                                ?>
                                  <p class="alert alert-info">Please upload the following documents:</p>
                                  <p class="alert alert-info">

                                    Evaluation Format
                                    <input type="file" class="form-control" name="formato_evaluacion" >

                                    Evaluation Report
                                    <input type="file" class="form-control" name="informe_evaluacion" >
                       
                                    Evaluation Opinion
                                    <input type="file" class="form-control" name="dictamen_evaluacion" >

                                  </p>
                                  <button type="submit" class="btn btn-success" style="width:100%" name="cargar_documentos" value="1">Send documents</button>
                                <?php
                                }

                              }else{
                                echo "<p class='alert alert-danger'>Membership Not yet Approved</p>";
                              }
                            }else{
                              if($solicitud['estatus_contrato'] == 'ACEPTADO' && $proceso_certificacion['estatus_membresia'] == "APROBADA"){
                                if(isset($solicitud['idformato_evaluacion']) && isset($solicitud['idinforme_evaluacion']) && isset($solicitud['iddictamen_evaluacion'])){

                                  $row_formato = mysql_query("SELECT * FROM formato_evaluacion WHERE idformato_evaluacion = $solicitud[idformato_evaluacion]", $dspp) or die(mysql_error());
                                  $formato = mysql_fetch_assoc($row_formato);
                                  $row_informe = mysql_query("SELECT * FROM informe_evaluacion WHERE idinforme_evaluacion = $solicitud[idinforme_evaluacion]", $dspp) or die(mysql_error());
                                  $informe = mysql_fetch_assoc($row_informe);
                                  $row_dictamen = mysql_query("SELECT * FROM dictamen_evaluacion WHERE iddictamen_evaluacion = $solicitud[iddictamen_evaluacion]", $dspp) or die(mysql_error());
                                  $dictamen = mysql_fetch_assoc($row_dictamen);

                                ?>
                                <p>Status: Evaluation Format: <span style="color:red"><?php echo $formato['estatus_formato']; ?></span></p>
                                <a href="<?php echo $formato['archivo']; ?>" class="btn btn-info" style="width:100%" target="_blank">Download Evaluation Format</a>

                                <p>Status Evaluation Opinion: <span style="color:red"><?php echo $dictamen['estatus_dictamen']; ?></span></p>
                                <a href="<?php echo $dictamen['archivo']; ?>" class="btn btn-info" style="width:100%" target="_blank">Download opinion</a>
                                <p>Status Evaluation Report: <span style="color:red"><?php echo $informe['estatus_informe']; ?></span></p>
                                <a href="<?php echo $informe['archivo']; ?>" class="btn btn-info" style="width:100%" target="_blank">Download Report</a>

                                <?php
                                }else{
                                ?>
                                  <p class="alert alert-info">Please upload the following documents:</p>
                                  <p class="alert alert-info">

                                    Evaluation Format
                                    <input type="file" class="form-control" name="formato_evaluacion" >

                                    Evaluation Report
                                    <input type="file" class="form-control" name="informe_evaluacion" >
                       
                                    Evaluation Opinion
                                    <input type="file" class="form-control" name="dictamen_evaluacion" >

                                  </p>
                                  <button type="submit" class="btn btn-success" style="width:100%" name="cargar_documentos" value="1">Send documents</button>
                                <?php
                                }

                              }else{
                                echo "<p class='alert alert-danger'>The User´s_Contract has not yet been Charged</p>";
                              }
                            }
                             ?>
                          </div>
                          
                          <div class="col-md-6">
                            <h4 style="font-size:14px;">Upload Certificate</h4>
                            <?php 
                            if(isset($solicitud['iddictamen_evaluacion']) && isset($solicitud['idformato_evaluacion']) && isset($solicitud['idinforme_evaluacion'])){

                                $row_formato = mysql_query("SELECT * FROM formato_evaluacion WHERE idformato_evaluacion = $solicitud[idformato_evaluacion]", $dspp) or die(mysql_error());
                                $formato = mysql_fetch_assoc($row_formato);
                                $row_informe = mysql_query("SELECT * FROM informe_evaluacion WHERE idinforme_evaluacion = $solicitud[idinforme_evaluacion]", $dspp) or die(mysql_error());
                                $informe = mysql_fetch_assoc($row_informe);
                                $row_dictamen = mysql_query("SELECT * FROM dictamen_evaluacion WHERE iddictamen_evaluacion = $solicitud[iddictamen_evaluacion]", $dspp) or die(mysql_error());
                                $dictamen = mysql_fetch_assoc($row_dictamen);

                                if($formato['estatus_formato'] == 'ACEPTADO' && $informe['estatus_informe'] == 'ACEPTADO' && $dictamen['estatus_dictamen'] == 'ACEPTADO'){
                                  if(isset($solicitud['idcertificado'])){
                                    $row_certificado = mysql_query("SELECT * FROM certificado WHERE idcertificado = $solicitud[idcertificado]", $dspp) or die(mysql_error());
                                    $certificado = mysql_fetch_assoc($row_certificado);
                                    $inicio = strtotime($certificado['vigencia_inicio']);
                                    $fin = strtotime($certificado['vigencia_fin']);
                                  ?>
                                    <p class="alert alert-info">The certificate has been loaded, which is valid from: <b><?php echo date('d/m/Y', $inicio); ?></b> to <b><?php echo date('d/m/Y', $fin); ?></b></p>
                                    <a href="<?php echo $certificado['archivo']; ?>" class="btn btn-success" style="width:100%" target="_blank">Download Certificate</a>
                                  <?php
                                  }else{
                                  ?>
                                    <div class="col-md-12">
                                      <p class="alert alert-info">Please set the Certificate Start and End date.</p>
                                    </div>
                                    <div class="col-md-6">
                                      <label for="fecha_inicio">Start date</label> 
                                      <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" placeholder="dd/mm/aaaa" required> 
                                    </div>
                                    <div class="col-md-6">
                                      <label for="fecha_fin">End date</label>
                                      <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" placeholder="dd/mm/aaaa" required>
                                    </div>
                                    
                                    <label for="certificado">Please select the Certificate</label>
                                    <input type="file" name="certificado" id="certificado" class="form-control" required>
                                    <button type="submit" name="enviar_certificado" value="1" class="btn btn-success" style="width:100%">Send Certificate</button>  
                                  <?php
                                  }
                                }else{
                                  echo '<p class="alert alert-warning">
                                  Once the "Evaluation Report" has been approved and the "Evaluation Opinion" can load the Certificate
                              </p> ';
                                }

                            }else{
                              echo '<p class="alert alert-warning">
                                Once the "Evaluation Report" has been approved and the "Evaluation Opinion" can load the Certificate
                              </p> ';
                            }
                            ?>
                          </div>
                        </div>
                      </div>

                      <div class="modal-footer">
                        <input type="hidden" name="tipo_solicitud" value="<?php echo $solicitud['tipo_solicitud']; ?>">
                        <input type="hidden" name="fecha_sistema" value="<?php echo time(); ?>">
                        <input type="hidden" name="idsolicitud_certificacion" value="<?php echo $solicitud['idsolicitud']; ?>">
                        <input type="hidden" name="idoc" value="<?php echo $solicitud['idoc']; ?>">
                        <input type="hidden" name="idopp" value="<?php echo $solicitud['idopp']; ?>">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- termina modal estatus_Certificado -->

          <!---- TERMINA SECCION CERTIFICADO ------>
            </form>
          <td>
            <a class="btn btn-sm btn-primary" data-toggle="tooltip" title="Consult application" href="?SOLICITUD&IDsolicitud=<?php echo $solicitud['idsolicitud']; ?>">Consult</a>
          </td>
          <td>
            <form action="../../reportes/solicitud.php" method="POST" target="_new">
              <button class="btn btn-xs btn-default" data-toggle="tooltip" title="Download request" target="_new" type="submit" ><img src="../../img/pdf.png" style="height:30px;" alt=""></button>

              <input type="hidden" name="idsolicitud_certificacion" value="<?php echo $solicitud['idsolicitud']; ?>">
              <input type="hidden" name="generar_formato" value="1">
            </form>
            <form action="../../reportes/solicitud_excel_en.php" method="POST" target="_new">
              <button class="btn btn-xs btn-default" data-toggle="tooltip" title="Solicitud en excel" target="_new" type="submit" ><img src="../../img/excel.png" style="height:30px;" alt=""></button>

              <input type="hidden" name="idsolicitud_certificacion" value="<?php echo $solicitud['idsolicitud']; ?>">
              <input type="hidden" name="generar_excel" value="2">
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

/*function validar(){
  membresia = document.getElementById("total_membresia").value;
  if( membresia == null || membresia.length == 0 ) {
    alert("Debe de escribir el Total de la Membresia asi como el tipo de moneda");
    return false;
  }
  
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
}*/
/*
function validarRenovacion(){
  membresia = document.getElementById("total_membresia_2").value;
  if( membresia == null || membresia.length == 0 ) {
    alert("Debe de escribir el Total de la Membresia asi como el tipo de moneda");
    return false;
  }  
}
*/


<!--
function ventanaNueva(documento,ancho,alto,nombreVentana){
    window.open(documento, nombreVentana,'width=' + ancho + ', height=' + alto);
}
     
//-->
</script>
<!--<table>
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
</table>-->