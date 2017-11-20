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

        $asunto = "D-SPP Cotation mise à jour / Updated quotation (Solicitud de Certificación para Organizaciones de Pequeños Productores)";

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
                  <th scope="col" align="left" width="280" ><strong>Notification de cotation / Price Notification</strong></th>
                </tr>
                <tr>
                  <td align="left" style="color:#ff738a;">Courriel de l\'Organisme de certification / Certification Entity: '.$opp_detail['email1'].'</td>
                </tr>
                <tr>
                  <td aling="left" style="text-align:justify">
                  <b style="color:red">'.$opp_detail['abreviacion_oc'].'</b> Certimex a soumis la cotation mise à jour correspondant à la demande de certification pour les organisations de petits producteurs.
                  <br><br> Connectez-vous au lien suivant <a href="http://d-spp.org/">www.d-spp.org/</a> en tant que OPP, afin d\'accéder à la cotation.
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
                          <td width="130px">Nom de l\'organisation / Organization name</td>
                          <td width="130px">Pays / Country</td>
                          <td width="130px">Organisme de certification / Certification Entity</td>
                          <td width="130px">Date d\'envoi / Shipping Date</td>
                       
                          
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
                        <td coslpan="2">En cas de doute ou de question, merci de contacter: soporte@d-spp.org</td>
                      </tr>
              </tbody>
            </table>

          </body>
          </html>
        ';
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


        echo "<script>alert('Cotation mise à jour');</script>";


  }else{
    $archivo = $_POST['cotizacion_actual'];
    echo "<script>alert('Impossible de mettre à jour la cotation');</script>";

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

    $asunto = "D-SPP | NOTIFICATION D'AVIS (NOTIFICATION OF RESOLUTION)";

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
                      <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">NOTIFICATION D\'AVIS</span></p></th>

                    </tr>
                    <tr>
                     <th scope="col" align="left" width="280"><p>Pour: <span style="color:red">'.$detalle_opp['nombre'].' - ('.$detalle_opp['abreviacion_opp'].')</span></p></th>
                    </tr>

                    <tr>
                      <td colspan="2">
                        <p>
                          '.$_POST['mensaje_renovacion'].'
                        </p>
                        <p>
                          <b>
                            Note : le paiement de l\'adhésion vaut ratification de la signature du Contrat d\'utilisation, il n\'est donc pas nécessaire de signer le contrat chaque année lors du renouvellement de votre certificat.
                          </b>
                        </p>
                        <hr>
                      </td>
                    </tr>
                    <tr>
                      <td><p><strong>Coordonnées bancaires</strong></p></td>
                    </tr>
                    <tr>
                      <td>
                        <ul>
                          Coordonnées bancaires annexées au courrier.
                        </ul>
                      </td>
                    </tr>

                    <tr>
                      <td colspan="2">
                        <p>En cas de doute ou de question, merci de contacter: <span style="color:red">'.$correos_oc['email1'].', '.$correos_oc['email2'].'</span></p>
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
                      <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">NOTIFICATION D\'AVIS</span></p></th>

                    </tr>
                    <tr>
                     <th scope="col" align="left" width="280"><p>Pour: <span style="color:red">'.$detalle_opp['nombre'].' - ('.$detalle_opp['abreviacion_opp'].')</span></p></th>
                    </tr>

                    <tr>
                      <td colspan="2">
                        <p>
                          1. Nous <span style="color:red">'.$detalle_oc['nombre'].'</span>, comme Organisme de certification autorisé par SPP Global, avons l\'honneur de vous informer par la présente que le résultat de l\'évaluation SPP est <span style="color:red">positif</span>.
                        </p>
                        <p>
                          2. Pour terminer le processus, veuillez procéder au paiement de l\'adhésion à SPP Global pour le montant de : <strong style="color:red">'.$_POST['total_membresia'].'</strong> (En annexe, les coordonnée bancaires, merci de lire les Conditions générales de paiement pour éviter le paiement d\'intérêts). Une fois le paiement réalisé, merci d\'accéder à votre compte et de télécharger le justificatif bancaire.
                        </p>
                        <p>
                          3. Une fois que SPP Global aura confirmé au travers du système la réception du paiement dans le compte de SPP Global, il sera procédé à la délivrance de votre certificat. 
                        </p>

                        <p>
                          L\'organisation des petits producteurs (OPP) aura un délai maximum de 30 jours civils pour effectuer le paiement et télécharger votre reçu au D-SPP.
                        </p>
                        <p>
                          En cas de problème pour effectuer le paiement en temps voulu, veuillez informer le secteur Administration et Finance de SPP Global (adm@spp.coop).
                        </p>
                        <p>
                          Si vous ne parvenez pas à recevoir un paiement ou une communication de l\'OPP, vous enverrez malheureusement la suspension de votre certificat.
                        </p>
                        <p>
                          <b>
                            Note : le paiement de l\'adhésion vaut ratification de la signature du Contrat d\'utilisation, il n\'est donc pas nécessaire de signer le contrat chaque année lors du renouvellement de votre certificat.
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

      $asunto = "D-SPP | Formulaire d'évaluation";

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
                <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">Télécharger le formulaire, Opinion et rapport d\'évaluation</span></p></th>

              </tr>
              <tr>
               <th scope="col" align="left" width="280"><p>OPP: <span style="color:red">'.$informacion['nombre_opp'].'</span></p></th>
              </tr>

              <tr>
                <td colspan="2">
                  <p>
                    SPP Global notifie que l\'OPP '.$informacion['nombre_opp'].' a fourni toute la documentation nécessaire.
                  </p>
                  <p>
                    Merci d\'accéder à votre compte d\'OC (organisme de certification) du système D-SPP pour pouvoir télécharger les documents suivants. 
                      <ul style="color:red">
                        <li>Formulaire d\'évaluation</li>
                        <li>Rapport d\'évaluation</li>
                        <li>Avis d\'évaluation</li>
                      </ul>
                  </p>
                </td>
              </tr>
              <tr>
                <td colspan="2">
                  Etapes pour télécharger la documentation. Pour ce faire, vous devez suivre les étapes suivantes :
                  <ol>
                    <li>Cliquez sur "Demandes"</li>
                    <li>Sélectionner les demandes d\'OPP</li>
                    <li>Se mettre dans la colonne \'Certifié" et cliquer sur le bouton "Télécharger le certificat".</li>
                    <li>Une fenêtre d\'où vous pourrez télécharger la documentation correspondante s\'ouvrira.</li>
                  </ol>
                  <p style="color:red">
                    Une fois que la documentation sera approuvée, il y aura une notification pour pouvoir télécharger le certificat.
                  </p>
                </td>
              </tr>
              <tr>
                <td colspan="2">
                  <p>En cas de doute ou de question, merci de contacter: <span style="color:red">cert@spp.coop</span> ou <span style="color:red">soporte@d-spp.org</span></p>
                </td>
              </tr>
            </tbody>
          </table>
        </body>
        </html>
      ';
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

      $mail->Subject = utf8_decode($asunto);
      $mail->Body = utf8_decode($cuerpo_mensaje);
      $mail->MsgHTML(utf8_decode($cuerpo_mensaje));
      $mail->Send();
      $mail->ClearAddresses();
      $mail->ClearAttachments();

      /// TERMINA MENSAJE "CARGAR DOCUEMENTOS DE EVALUACIÓN"

      ///termina envio de mensaje dictamen positivo
    ////////// SE ENVIA DICTAMEN POSITIVO PRIMERA VEZ ////////////////////
    }else if($_POST['tipo_solicitud'] == 'NUEVA'){ 
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

      //$documentacion = mysql_fetch_assoc($row_documentacion);
      /// INICIA MENSAJE "CARGAR DOCUMENTOS DE EVALUACIÓN"

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
                    <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">NOTIFICATION D\'AVIS</span></p></th>

                  </tr>
                  <tr>
                   <th scope="col" align="left" width="280"><p>Pour: <span style="color:red">'.$detalle_opp['nombre'].' - ('.$detalle_opp['abreviacion_opp'].')</span></p></th>
                  </tr>

                  <tr>
                    <td colspan="2">
                      <p>
                        1. Nous <span style="color:red">'.$detalle_oc['nombre'].'</span>, comme Organisme de certification autorisé par SPP Global, avons l\'honneur de vous informer par la présente que le résultat de l\'évaluation SPP est <span style="color:red">positif</span>.
                      </p>
                      <p>
                        2. Pour terminer le processus, merci de lire de manière attentive les documents joints et ensuite de signer le Contrat d\'utilisation et l\'Accusé de réception. Veuillez compléter les informations de votre organisation et le représentant légal dans les textes marqués en rouge dans le contrat d\'utilisation.
                      </p>

                      <p>
                        3. Une fois que vous aurez signé les documenst indiqués, accédez à votre compte et téléchargez les documents pour qu\'ils soient revus par SPP Global.
                      </p>

                      <p>
                        4. Une fois que SPP Global aura confirmé au travers du système la réception des documents, il sera procédé à la délivrance du certificat.
                      </p>


                      <p>
                        <b>Adhésion</b>
                      </p>
                      <p>
                        Nous demandons le moyen le plus prudent de procéder au paiement de l\'adhésion à SPP Global. Selon le nombre de membres, le montant de l\'adhésion est de: <strong style="color:red">'.$_POST['total_membresia'].'</strong>. (Les coordonnées bancaires sont jointes, veuillez lire les Dispositions générales de paiement)
                      </p>
                      <p>
                        L\'organisation des petits producteurs (OPP) aura un délai maximum de 30 jours civils pour effectuer le paiement et télécharger votre reçu au D-SPP.
                      </p>
                      <p>
                        En cas de problème pour effectuer le paiement en temps voulu, veuillez informer le secteur Administration et Finance de SPP Global (adm@spp.coop).
                      </p>
                      <p>
                        Si vous ne parvenez pas à recevoir un paiement ou une communication de l\'OPP, vous enverrez malheureusement la suspension de votre certificat.
                      </p>

                      <hr>
                    </td>
                  </tr>

                  <tr>
                    <td><p><strong>DOCUMENTS ATTACHÉS / ATTACHED DOCUMENTS</strong></p></td>
                  </tr>
                    <tr style="color:#000">
                      <td>
                        <ul>
                          '.$documentacion_nombres.'
                        </ul>
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
                        2. In order to complete the process, the most careful request is to read the attached documents and subsequently sign the <span style="color:red">User´s Contract and Confirmation of Receipt</span> . Please complete the information of your organization and the legal representative in the texts marked in red within the Contract of Use.
                      </p>
                      <p>
                        3. Once you have signed the indicated documents, enter your account as Small Producers Organization (OPP) within the d-spp.org system and upload the documents so that they are reviewed by SPP Global.
                      </p>
                      <p>
                        4. Once SPP Global confirms the receipt of the documents in the SPP Global account through the System, the Certificate will be delivered.
                      </p>
                      <p>
                        <b>Membership</b>
                      </p>
                      <p>
                        We request the most careful way to <span style="color:red">proceed with the payment of membership to SPP Global</span>. According to the number of members, the amount of the membership is: <strong style="color:red">'.$_POST['total_membresia'].'</strong>. (The bank details are attached, please read the General Payment Provisions)
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

                      <hr>
                    </td>
                  </tr>

                  <tr>
                    <td colspan="2">
                      <p>En cas de doute ou de clarification, veuillez écrire à <span style="color:red">'.$correos_oc['email1'].', '.$correos_oc['email2'].'</span></p>
                    </td>
                  </tr>
                </tbody>
              </table>
            </body>
            </html>
      ';

      if(isset($archivo_dictamen)){
        $mail->AddAttachment($archivo_dictamen);
      }
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


      $asunto = "D-SPP | Formulaire d'évaluation";

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
                <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">Télécharger le formulaire, Opinion et rapport d\'évaluation</span></p></th>

              </tr>
              <tr>
               <th scope="col" align="left" width="280"><p>OPP: <span style="color:red">'.$informacion['nombre_opp'].'</span></p></th>
              </tr>

              <tr>
                <td colspan="2">
                  <p>
                    SPP Global notifie que l\'OPP '.$informacion['nombre_opp'].' a fourni toute la documentation nécessaire.
                  </p>
                  <p>
                    Merci d\'accéder à votre compte d\'OC (organisme de certification) du système D-SPP pour pouvoir télécharger les documents suivants.
                    <ul style="color:red">
                      <li>Formulaire d\'évaluation</li>
                      <li>Rapport d\'évaluation</li>
                      <li>Avis d\'évaluation</li>
                    </ul>

                 </p>
                </td>
              </tr>
              <tr>
                <td colspan="2">
                  Etapes pour télécharger la documentation. Pour ce faire, vous devez suivre les étapes suivantes :
                  <ol>
                    <li>Cliquez sur "Demandes"</li>
                    <li>Sélectionner les demandes d\'OPP</li>
                    <li>Se mettre dans la colonne \'Certifié" et cliquer sur le bouton "Télécharger le certificat".</li>
                    <li>Une fenêtre d\'où vous pourrez télécharger la documentation correspondante s\'ouvrira.</li>
                  </ol>
                  <p style="color:red">
                    Une fois que la documentation sera approuvée, il y aura une notification pour pouvoir télécharger le certificat.
                  </p>
                </td>
              </tr>
              <tr>
                <td colspan="2">
                  <p>En cas de doute ou de question, merci de contacter: <span style="color:red">cert@spp.coop</span> o <span style="color:red">soporte@d-spp.org</span></p>
                </td>
              </tr>
            </tbody>
          </table>
        </body>
        </html>
      ';
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

    $asunto = "D-SPP | Notification d'avis négatif";

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
                      <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">NOTIFICATION D\'AVIS</span></p></th>

                    </tr>
                    <tr>
                     <th scope="col" align="left" width="280"><p>Pour: <span style="color:red">'.$detalle_opp['nombre'].' - ('.$detalle_opp['abreviacion_opp'].')</span></p></th>
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
                        <p>En cas de doute ou de question, merci de contacter: <span style="color:red">'.$correos_oc['email1'].', '.$correos_oc['email2'].'</span></p>
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
                      <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">NOTIFICATION D\'AVIS / NOTIFICATION OF RESOLUTION </span></p></th>

                    </tr>
                    <tr>
                     <th scope="col" align="left" width="280"><p>Pour: <span style="color:red">'.$detalle_opp['nombre'].' - ('.$detalle_opp['abreviacion_opp'].')</span></p></th>
                    </tr>

                    <tr>
                      <td colspan="2">
                        <p>
                          Nous <span style="color:red">'.$detalle_oc['nombre'].'</span>, comme Organisme de certification autorisé par SPP Global, avons le regret de vous informer par la présente que le résultat de l\'évaluation SPP est négatif.
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
                        <p>En cas de doute ou de question, merci de contacter: <span style="color:red">'.$correos_oc['email1'].', '.$correos_oc['email2'].'</span></p>
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

      $mail->AddBCC($spp_global);
      $mail->AddBCC($finanzas_spp);

      $mail->Subject = utf8_decode($asunto);
      $mail->Body = utf8_decode($cuerpo_mensaje);
      $mail->MsgHTML(utf8_decode($cuerpo_mensaje));

      if($mail->Send()){
       echo "<script>alert('E-mail envoyé avec succès.');location.href ='javascript:history.back()';</script>";
      }else{
        echo "<script>alert('Erreur. Le courrier n'a pas pu être envoyé, merci de contacter l'administrateur : soporte@d-spp.org');location.href ='javascript:history.back()';</script>";
      }
      //$mail->Send();
      $mail->ClearAddresses();
      $mail->ClearAttachments();

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

  $mensaje = "Le processus de certification a été mis à jour";
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
    //$mail->Send();
    //$mail->ClearAddresses();
    if($mail->Send()){
      $mail->ClearAddresses();
      echo "<script>alert('Le formulaire, l'avis et le rapport d'évalaution ont été envoyés, vous serez contacté sous peu.');location.href ='javascript:history.back()';</script>";
    }else{
      $mail->ClearAddresses();
      echo "<script>alert('Erreur. Le courrier n'a pas pu être envoyé, merci de contacter l'administrateur : soporte@d-spp.org');location.href ='javascript:history.back()';</script>";
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

  $asunto = "D-SPP | Certificat disponible pour téléchargement. / Certificate Ready to be Downloaded";

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
                    Votre certificat est prêt, vous pouvez le télécharger maintenant.
                  </p>
                  <p>
                    Le certificat est valide du <span style="color:red">'.date('d/m/Y', $fecha_inicio).'</span> au <span style="color:red">'.date('d/m/Y', $fecha_fin).'</span>
                  </p>
                 
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
                  <p>En cas de doute ou de question, merci de contacter: <span style="color:red">cert@spp.coop</span> o <span style="color:red">soporte@d-spp.org</span></p>
                </td>
              </tr>

            </tbody>
          </table>
        </body>
      </html>
  ';
    $mail->AddAddress($informacion['email']);
    $mail->AddAddress($informacion['contacto1_email']);
    $mail->AddBCC($spp_global);
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

  $mensaje = "Le certificat a été téléchargé et cela a été notifié à SPP Global et à l'Organisation de petits producteurs.";
}


$currentPage = $_SERVER["PHP_SELF"];

$maxRows_opp = 20;
$pageNum_opp = 0;
if (isset($_GET['pageNum_opp'])) {
  $pageNum_opp = $_GET['pageNum_opp'];
}
$startRow_opp = $pageNum_opp * $maxRows_opp;

//$query = "SELECT solicitud_certificacion.idsolicitud_certificacion AS 'idsolicitud', solicitud_certificacion.tipo_solicitud, solicitud_certificacion.idopp, solicitud_certificacion.idoc, solicitud_certificacion.fecha_registro, solicitud_certificacion.fecha_aceptacion, solicitud_certificacion.cotizacion_opp, solicitud_certificacion.contacto1_email, solicitud_certificacion.contacto2_email, opp.nombre AS 'nombre_opp', opp.abreviacion AS 'abreviacion_opp', opp.email, proceso_certificacion.idproceso_certificacion, proceso_certificacion.estatus_publico, proceso_certificacion.estatus_interno, proceso_certificacion.estatus_dspp, estatus_publico.nombre AS 'nombre_publico', estatus_interno.nombre AS 'nombre_interno', estatus_dspp.nombre AS 'nombre_dspp', periodo_objecion.*, membresia.idmembresia, membresia.estatus_membresia, contratos.idcontrato, contratos.estatus_contrato, certificado.idcertificado, formato_evaluacion.idformato_evaluacion, dictamen_evaluacion.iddictamen_evaluacion, informe_evaluacion.idinforme_evaluacion FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp LEFT JOIN proceso_certificacion ON solicitud_certificacion.idsolicitud_certificacion = proceso_certificacion.idsolicitud_certificacion LEFT JOIN estatus_publico ON proceso_certificacion.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON proceso_certificacion.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON proceso_certificacion.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN periodo_objecion ON solicitud_certificacion.idsolicitud_certificacion = periodo_objecion.idsolicitud_certificacion LEFT JOIN membresia ON solicitud_certificacion.idsolicitud_certificacion = membresia.idsolicitud_certificacion LEFT JOIN contratos ON solicitud_certificacion.idsolicitud_certificacion = contratos.idsolicitud_certificacion LEFT JOIN certificado ON solicitud_certificacion.idopp = certificado.idopp LEFT JOIN dictamen_evaluacion ON solicitud_certificacion.idsolicitud_certificacion = dictamen_evaluacion.idsolicitud_certificacion LEFT JOIN informe_evaluacion ON solicitud_certificacion.idsolicitud_certificacion = informe_evaluacion.idsolicitud_certificacion LEFT JOIN formato_evaluacion ON solicitud_certificacion.idsolicitud_certificacion = formato_evaluacion.idsolicitud_certificacion WHERE solicitud_certificacion.idoc = $idoc GROUP BY proceso_certificacion.estatus_dspp ORDER BY proceso_certificacion.estatus_dspp DESC";

$query = "SELECT solicitud_certificacion.*, solicitud_certificacion.idopp, solicitud_certificacion.idsolicitud_certificacion AS 'idsolicitud', oc.abreviacion AS 'abreviacionOC', opp.nombre AS 'nombre_opp', opp.abreviacion AS 'abreviacion_opp', opp.email, opp.estatus_opp, periodo_objecion.idperiodo_objecion, periodo_objecion.fecha_inicio, periodo_objecion.fecha_fin, periodo_objecion.estatus_objecion, periodo_objecion.observacion, periodo_objecion.dictamen, periodo_objecion.documento, membresia.idmembresia, certificado.idcertificado, contratos.idcontrato, contratos.estatus_contrato, informe_evaluacion.idinforme_evaluacion, formato_evaluacion.idformato_evaluacion, dictamen_evaluacion.iddictamen_evaluacion FROM solicitud_certificacion INNER JOIN oc ON solicitud_certificacion.idoc = oc.idoc INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp LEFT JOIN periodo_objecion ON solicitud_certificacion.idsolicitud_certificacion  = periodo_objecion.idsolicitud_certificacion LEFT JOIN membresia ON solicitud_certificacion.idsolicitud_certificacion = membresia.idsolicitud_certificacion LEFT JOIN certificado ON solicitud_certificacion.idsolicitud_certificacion = certificado.idsolicitud_certificacion LEFT JOIN contratos ON solicitud_certificacion.idsolicitud_certificacion = contratos.idsolicitud_certificacion LEFT JOIN informe_evaluacion ON solicitud_certificacion.idsolicitud_certificacion = informe_evaluacion.idsolicitud_certificacion LEFT JOIN formato_evaluacion ON solicitud_certificacion.idsolicitud_certificacion = formato_evaluacion.idsolicitud_certificacion LEFT JOIN dictamen_evaluacion ON solicitud_certificacion.idsolicitud_certificacion = dictamen_evaluacion.idsolicitud_certificacion WHERE solicitud_certificacion.idoc = $idoc GROUP BY solicitud_certificacion.idsolicitud_certificacion  ORDER BY solicitud_certificacion.fecha_registro DESC";

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
          <th class="text-center"><a href="#" data-toggle="tooltip" title="Type de demande"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>Type</a></th>
          <th class="text-center">Date</th>
          <th class="text-center">OC</th>
          <th class="text-center">Organisation</th>
          <th class="text-center">Etat de la demande</th>
          <th class="text-center">Cotation (téléchargeable)</th>
          <th class="text-center">Processus d'objection</th>
          <th class="text-center"><a href="#" data-toggle="tooltip" title="Sélectionnez le procesuus de certification dans lequel se trouve l'OPP"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>Processus de<br> certification</a></th>
          <th class="text-center">Certificat</th>
          <th class="text-center" colspan="2">Actions</th>
        </tr>       
      </thead>
      <tbody>
      <?php 
      while($solicitud = mysql_fetch_assoc($row_solicitud)){
          $query_proceso = "SELECT proceso_certificacion.*, proceso_certificacion.idsolicitud_certificacion, estatus_publico.idestatus_publico, estatus_publico.nombre_frances AS 'nombre_publico', estatus_interno.idestatus_interno, estatus_interno.nombre_frances AS 'nombre_interno', estatus_dspp.idestatus_dspp, estatus_dspp.nombre_frances AS 'nombre_dspp', membresia.idmembresia, membresia.estatus_membresia, membresia.idcomprobante_pago, membresia.fecha_registro FROM proceso_certificacion LEFT JOIN estatus_publico ON proceso_certificacion.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON proceso_certificacion.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON proceso_certificacion.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN membresia ON proceso_certificacion.idsolicitud_certificacion = membresia.idsolicitud_certificacion WHERE proceso_certificacion.idsolicitud_certificacion =  $solicitud[idsolicitud] ORDER BY proceso_certificacion.idproceso_certificacion DESC LIMIT 1";
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
              echo "Non disponible";
            }
             ?>
          </td>

          <td>
            <?php
            if(isset($solicitud['cotizacion_opp'])){
            ?>
              <div class="btn-group" role="group" aria-label="...">
                <button type="button" class="btn btn-sm btn-default" data-toggle="modal" data-target="<?php echo "#cotizacion".$solicitud['idsolicitud_certificacion']; ?>" title="Remplacer la cotation actuelle"><span class="glyphicon glyphicon-refresh" aria-hidden="true"></span></button>
                <a href='<?php echo $solicitud['cotizacion_opp']; ?>' class='btn btn-sm btn-success' style='font-size:12px;color:white;height:30px;' target='_blank'><span class='glyphicon glyphicon-download' aria-hidden='true'></span> Télécharger la cotation</a>
              </div>
            <?php
               if($proceso_certificacion['estatus_dspp'] == 5){ // SE ACEPTA LA COTIZACIÓN
                echo "<p class='alert alert-success' style='padding:7px;'>Statut: ".$proceso_certificacion['nombre_dspp']."</p>"; 
               }else if($proceso_certificacion['estatus_dspp'] == 17){ // SE RECHAZA LA COTIZACIÓN
                echo "<p class='alert alert-danger' style='padding:7px;'>Statut: ".$proceso_certificacion['nombre_dspp']."</p>"; 
               }else{
                echo "<p class='alert alert-info' style='padding:7px;'>Statut: ".$proceso_certificacion['nombre_dspp']."</p>"; 
               }

            }else{ // INICIA CARGAR COTIZACIÓN
              echo "Non disponible";
            } // TERMINA CARGAR COTIZACIÓN
             ?>

            <!-- Modal -->
            <div class="modal fade" id="<?php echo "cotizacion".$solicitud['idsolicitud_certificacion']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
              <div class="modal-dialog" role="document">
                <form action="" method="POST" enctype="multipart/form-data">
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      <h4 class="modal-title" id="myModalLabel">Remplacer la cotation actuelle</h4>
                    </div>
                    <div class="modal-body">
                      <div class="form-group">
                        <label for="nueva_cotizacion">Nouvelle cotation</label>
                        <input type="file" id="nueva_cotizacion" name="nueva_cotizacion">
                        <input type="hidden" name="cotizacion_actual" value="<?php echo $solicitud['cotizacion_opp']; ?>">
                        <input type="hidden" name="idsolicitud_certificacion" value="<?php echo $solicitud['idsolicitud_certificacion']; ?>">
                      </div>
                    </div>

                    <div class="modal-footer">
                      <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                      <button type="submit" name="reemplazar_cotizacion" value="1" class="btn btn-primary">Remplacer la cotation actuelle</button>
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
              <a href="#" data-toggle="tooltip" title="Cette demande se trouve en Processus de renouvellement du certificat, la période d'objection ne s'applique donc pas." style="padding:7px;"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>Demande de renouvellement</a>
            <?php
            }else{
              if(isset($solicitud['idperiodo_objecion']) && $solicitud['estatus_objecion'] != 'EN ESPERA'){
              ?>
                <p class="alert alert-info" style="margin-bottom:0;padding:0px;">Date de début: <?php echo date('d/m/Y', $solicitud['fecha_inicio']); ?></p>
                <p class="alert alert-danger" style="margin-bottom:0;padding:0px;">Date de fin: <?php echo date('d/m/Y', $solicitud['fecha_fin']); ?></p>
                <?php 
                if(isset($solicitud['documento'])){
                ?>
                  <p class="alert alert-success" style="margin-bottom:0;padding:0px;">Résolution: <?php echo $solicitud['dictamen']; ?></p>
                  <a class="btn btn-info" style="font-size:12px;width:100%;height:30px;" href='<?php echo $solicitud['documento']; ?>' target='_blank'><span class='glyphicon glyphicon-download' aria-hidden='true'></span> Télécharger la résolution</a> 
                <?php
                }
                ?>
              <?php
              }else{
                echo "Non disponible";
              }
            }
            ?>
          </td>
          <!---- INICIA PROCESO DE CERTIFICACIÓN ---->
          <td>
            <?php echo $solicitud['dictamen']; ?>
            <form action="" method="POST" enctype="multipart/form-data">
              <?php 
              if((isset($solicitud['dictamen']) && $solicitud['dictamen'] == 'POSITIVO') || ($solicitud['tipo_solicitud']) == 'RENOVACION' && !empty($solicitud['fecha_aceptacion'])){
              ?>
                <button type="button" class="btn btn-sm btn-primary" style="width:100%" data-toggle="modal" data-target="<?php echo "#certificacion".$solicitud['idsolicitud_certificacion']; ?>">Processus de certification</button>

                <!-- inicia modal proceso de certificación -->
                <div id="<?php echo "certificacion".$solicitud['idsolicitud_certificacion']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                  <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Processus de certification</h4>
                      </div>
                      <div class="modal-body"><!-- INICIA MODAL BODY -->
                        <div class="row"><!--INICIA ROW-->
                          <?php 

                          $row_proceso_certificacion = mysql_query("SELECT proceso_certificacion.*, estatus_interno.nombre FROM proceso_certificacion INNER JOIN estatus_interno ON proceso_certificacion.estatus_interno = estatus_interno.idestatus_interno WHERE proceso_certificacion.idsolicitud_certificacion = '$solicitud[idsolicitud]' AND proceso_certificacion.estatus_interno IS NOT NULL", $dspp) or die(mysql_error());
                          while($historial_certificacion = mysql_fetch_assoc($row_proceso_certificacion)){
                            echo "<div class='col-md-10'>Proceso: $historial_certificacion[nombre]</div>";
                            echo "<div class='col-md-2'>Fecha: ".date('d/m/Y',$historial_certificacion['fecha_registro'])."</div>";
                          }

                          if(!isset($solicitud['idcomprobante_pago'])){
                          ?>
                          <div class="col-md-12">
                            <select class="form-control" name="estatus_interno" id="<?php echo 'statusSelect'.$solicitud['idsolicitud']; ?>" onchange="<?php echo 'funcionSelect'.$solicitud['idsolicitud'].'()'; ?>">
                              <option value="">Sélectionnez le procesuus de certification dans lequel se trouve l'OPP</option>
                              <?php 
                              $row_estatus_interno = mysql_query("SELECT * FROM estatus_interno",$dspp) or die(mysql_error());
                              while($estatus_interno = mysql_fetch_assoc($row_estatus_interno)){
                                echo "<option value='$estatus_interno[idestatus_interno]'>$estatus_interno[nombre_frances]</option>";
                              }
                               ?>
                            </select>                        
                          </div>

                          <?php
                          }
                          ?>

                              <div class="col-xs-12" id="<?php echo 'divSelect'.$solicitud['idsolicitud']; ?>" style="margin-top:10px;">
                                <div class="col-xs-6">
                                  <input style="display:none" id="<?php echo 'nombreArchivo'.$solicitud['idsolicitud']; ?>"  type='text' class='form-control' name='nombre_archivo' placeholder="Nom du fichier"/>
                                </div>
                                <!-- INICIA CARGAR ARCHIVO ESTATUS -->
                                <div class="col-xs-6">
                                  <input style="display:none" id="<?php echo 'archivo_estatus'.$solicitud['idsolicitud']; ?>" type='file' class='form-control' name='archivo_estatus' />
                                </div>
                                <!-- TERMINA CARGAR ARCHIVO ESTATUS -->

                                <!-- INICIA ACCION PROCESO CERTIFICACION -->
                                <div class="col-md-12">
                                  <textarea id="<?php echo 'registroOculto'.$solicitud['idsolicitud']; ?>" style="display:none" name="mensaje_negativo" class="form-control textareaMensaje" cols="30" rows="10" placeholder="Ecrivez ici"></textarea>  
                                </div>
                                

                                <!-- TERMINA ACCION PROCESO CERTIFICACION -->
                              </div>
                              
                              <!-------- INICIA VENTANA DICTAMEN POSITIVO ---------->
                              <div id="<?php echo 'tablaCorreo'.$solicitud['idsolicitud']; ?>" style="display:none"> 
                                <div class="col-xs-12"  style="margin-top:10px;">
                                  <?php 
                                  if($solicitud['tipo_solicitud'] == 'RENOVACION'){ ///  RENOVACIÓN
                                  ?>
                                    <p class="alert alert-info">Le formulaire suivant sera envoyé à l'OPP sous peu</p>
                                    <div class="col-xs-12">
                                      
                                      <div class="col-xs-12">
                                        <div class="col-xs-6"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></div>
                                        <div class="col-xs-6">
                                          <p>Message envoyé le : <?php echo date("d/m/Y", time()); ?></p>
                                          <p>Pour: <span style="color:red"><?php echo $solicitud['nombre_opp']; ?></span></p>
                                          <p>Courrier(s): <?php echo $solicitud['contacto1_email']." ".$solicitud['contacto2_email']." ".$solicitud['email']; ?></p>
                                          <p>Objet: <span style="color:red">Notification de l'avis SPP</span></p>
                                          
                                        </div>
                                      </div>
                                      <div class="col-xs-12">
                                        <h4 style="font-size:14px;padding:5px;">Message pour l'OPP( <small style="font-size:13px;">L'OC doit écrire dans le champ ci-dessous le texte relatif à la notification de l'avis et s'il est positif, elle doit expliquer que le récipiendaire doit lire les documents annexés et signer les Contrat d'utilistion et Accusé de réception. <span style="color:red">Si vous n'écrivez rien, le système enverra un message prédéterminé. <a href="dictamen_positivo.php?opp=<?php echo $solicitud['nombre_opp'];?>&renovacion_positivo&oc=<?php echo $solicitud['idoc']; ?>" target="ventana1" onclick="ventanaNueva ('', 500, 400, 'ventana1');">Voir le message</a></span></small>)</h4>
                                        <textarea name="mensaje_renovacion" class="form-control textareaMensaje" id="" cols="30" rows="10" placeholder="Saisissez un message si vous le souhaitez"></textarea>

                                      </div>
                                    </div>

                                    <div class="col-xs-12">
                                      <div class="col-xs-12">
                                        <h4 style="font-size:14px;">Fichiers joints FICHIERS JOINTS( <small>Fichiers joints au courrier</small> )</h4>
                                        <?php 
                                          echo '<h5 style="font-size:12px;" class="alert alert-warning">
                                          <ul>
                                            <li>Cette organisation est en cours de renouvellement du certificat, le contrat d\'utilisation ne sera donc pas envoyé.</li>
                                            <li style="color:red;">Le paiement de l\'adhésion SPP vaut ratification de la signature du contrat.</li>
                                          </ul>
                                          </h5>';
                                         ?>
                                      </div>
                                      <div class="col-xs-12 alert alert-info">
                                        <h5 class="">Adhésion SPP ADHÉSION SPP: <span style="color:#7f8c8d">Indiquer le montant total de l'adhésion ainsi que la devise.</span></h5>
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
                                        <input type="text" class="form-control" name="total_membresia" id="total_membresia_2"  value="<?php echo $valor_membresia; ?>" placeholder="Total de l'adhésion">
                                      </div>

                                      <div class="col-xs-12">
                                        <h4 style="font-size:14px;">
                                          Fichier supplémentaire (<span style="color:red">* optionnel:</span> <small>joindre un fichier autre que ceux envoyés par SPP Global</small>)
                                        </h4>
                                        <div class="col-xs-12">
                                          <input type="text" class="form-control" name="nombre_archivo_dictamen" placeholder="Nom du fichier">
                                        </div>
                                        <div class="col-xs-12">
                                          <input type="file" class="form-control" name="archivo_dictamen">
                                        </div>
                                      </div>
                                    </div>

                                  <?php
                                  }else{ ///// PRIMERA VEZ
                                  ?>
                                    <p class="alert alert-info">Le formulaire suivant sera envoyé sous peu à l'OPP.</p>
                                    <div class="col-xs-12">
                                      
                                      <div class="col-xs-12">
                                        <div class="col-xs-6"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></div>
                                        <div class="col-xs-6">
                                          <p>Message envoyé le : <?php echo date("d/m/Y", time()); ?></p>
                                          <p>Pour: <span style="color:red"><?php echo $solicitud['nombre_opp']; ?></span></p>
                                          <p>Courrier(s): <?php echo $solicitud['contacto1_email']." , ".$solicitud['contacto2_email']." , ".$solicitud['email']; ?></p>
                                          <p>Objet: <span style="color:red">Notification de l'avis SPP</span></p>
                                          
                                        </div>
                                      </div>
                                      <div class="col-xs-12">
                                        <h4 style="font-size:14px;padding:5px;">
                                          Message pour l'OPP( <small style="font-size:13px;">L'OC doit écrire dans le champ ci-dessous le texte relatif à la notification de l'avis et s'il est positif, elle doit expliquer que le récipiendaire doit lire les documents annexés et signer les Contrat d'utilistion et Accusé de réception. <span style="color:red">Si vous n'écrivez rien, le système enverra un message prédéterminé. <a href="dictamen_positivo.php?opp=<?php echo $solicitud['nombre_opp'];?>&oc=<?php echo $solicitud['idoc']; ?>" target="ventana1" onclick="ventanaNueva ('', 500, 400, 'ventana1');">Voir le message</a></span></small>)
                                        </h4>
                                        <textarea name="mensajeOPP" class="form-control textareaMensaje" id="" cols="30" rows="10" placeholder="Saisissez un message si vous le souhaitez" ></textarea>

                                      </div>
                                    </div>
                                    <div class="col-xs-12">
                                      <div class="col-xs-12">
                                        <h4 style="font-size:14px;">Fichiers joints: <span style="color:#7f8c8d">documentation envoyée à l'organisation une fois que le processus de certification a été finalisé avec un avis</span></h4>
                                        <?php 
                                        $row_documentacion = mysql_query("SELECT * FROM documentacion WHERE idestatus_interno = 8", $dspp) or die(mysql_error());
                                        while($documetacion = mysql_fetch_assoc($row_documentacion)){

                                          echo "<span class='glyphicon glyphicon-ok' aria-hidden='true'></span> <a href='$documetacion[archivo]' target='_blank'>$documetacion[nombre]</a><br>";
                                        }
                                         ?>
                                        <p class="alert alert-warning">
                                          <strong>
                                            Choisssez la langue dans laquelle vous voulez envoyer le <span style="color:red">Manuel SPP, le Contrat d'utilisation et l'Accusé de réception</span>.
                                            <label class="radio-inline">
                                              <input type="radio" name="idioma" id="inlineRadio1" value="ESP"> Espagnol
                                            </label>
                                            <label class="radio-inline">
                                              <input type="radio" name="idioma" id="inlineRadio2" value="EN"> Anglais
                                            </label>

                                          </strong>
                                        </p>
      
                                      </div>
                                      <div class="col-xs-12">
                                        <h4 style="font-size:14px;">Fichier supplémentaire: <span style="color:#7f8c8d">joindre un fichier autre que ceux envoyés par SPP Global.</span></h4>
                                        <input type="text" class="form-control" name="nombre_archivo_dictamen" placeholder="Nom du fichier">
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

                                        <h4 style="font-size:14px;">ADHÉSION SPP: <span style="color:#7f8c8d">Indiquer le montant total de l'adhésion ainsi que la devise.</span></h4>
                                        <input type="text" class="form-control" name="total_membresia" id="total_membresia" value="<?php echo $valor_membresia; ?>" placeholder="Total de l'adhésion">
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
                                    <p class="alert alert-info">Le formulaire suivant sera envoyé sous peu à l'OPP.</p>
                                    <div class="col-xs-12">
                                      
                                      <div class="col-xs-12">
                                        <div class="col-xs-6"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></div>
                                        <div class="col-xs-6">
                                          <p>Message envoyé le : <?php echo date("d/m/Y", time()); ?></p>
                                          <p>Pour: <span style="color:red"><?php echo $solicitud['nombre_opp']; ?></span></p>
                                          <p>Courrier(s) <?php echo $solicitud['contacto1_email']." ".$solicitud['contacto2_email']." ".$solicitud['email']; ?></p>
                                          <p>Objet :: <span style="color:red">Notification de l'avis SPP</span></p>
                                          
                                        </div>
                                      </div>
                                      <div class="col-xs-12">
                                        <h4 style="font-size:14px;padding:5px;">Message pour l'OPP( <small style="font-size:13px;">L'OC doit écrire dans le champ ci-dessous le texte relatif à la notification de l'avi <span style="color:red">Si vous n'écrivez rien, le système enverra un message prédéterminé. <a href="dictamen_positivo.php?opp=<?php echo $solicitud['nombre_opp'];?>&renovacion_negativo&oc=<?php echo $solicitud['idoc'] ?>" target="ventana1" onclick="ventanaNueva ('', 500, 400, 'ventana1');">Voir le message</a></span></small>)</h4>
                                        <textarea name="mensaje_renovacion" class="form-control textareaMensaje" id="" cols="30" rows="10" placeholder="Saisissez un message si vous le souhaitez"></textarea>

                                      </div>
                                    </div>

                                    <div class="col-xs-12">
                                      <div class="col-xs-12">
                                        <h4 style="font-size:14px;">Fichier supplémentaire( <small><span style="color:red">*optionnel:</span>. joindre un fichier autre que ceux envoyés par SPP Global</small>)</h4>
                                        <div class="col-xs-12">
                                          <input type="text" class="form-control" name="nombreArchivo" placeholder="Nom du fichier">
                                        </div>
                                        <div class="col-xs-12">
                                          <input type="file" class="form-control" name="archivo_extra">
                                        </div>
                                      </div>
                                    </div>

                                  <?php
                                  }else{
                                  ?>
                                    <p class="alert alert-info">Le formulaire suivant sera envoyé sous peu à l'OPP.</p>
                                    <div class="col-xs-12">
                                      
                                      <div class="col-xs-12">
                                        <div class="col-xs-6"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></div>
                                        <div class="col-xs-6">
                                          <p>Message envoyé le : <?php echo date("d/m/Y", time()); ?></p>
                                          <p>Pour: <span style="color:red"><?php echo $solicitud['nombre_opp']; ?></span></p>
                                          <p>Courrier(s) <?php echo $solicitud['contacto1_email']." ".$solicitud['contacto2_email']." ".$solicitud['email']; ?></p>
                                          <p>Objet :: <span style="color:red">Notification de l'avis SPP</span></p>
                                          
                                        </div>
                                      </div>
                                      <div class="col-xs-12">
                                        <h4 style="font-size:14px;padding:5px;">Message pour l'OPP( <small style="font-size:13px;">L'OC doit écrire dans le champ ci-dessous le texte relatif à la notification de l'avis et s'il est positif, elle doit expliquer que le récipiendaire doit lire les documents annexés et signer les Contrat d'utilistion et Accusé de réception. <span style="color:red">Si vous n'écrivez rien, le système enverra un message prédéterminé. <a href="dictamen_positivo.php?opp=<?php echo $solicitud['nombre_opp'];?>&negativo&oc=<?php echo $solicitud['idoc']; ?>" target="ventana1" onclick="ventanaNueva ('', 500, 400, 'ventana1');">Voir le message</a></span></small>)</h4>
                                        <textarea name="mensajeOPP" class="form-control textareaMensaje" id="" cols="30" rows="10" placeholder="Ingrese un mensaje en caso de que lo deseé" ></textarea>

                                      </div>
                                    </div>
                                    <div class="col-xs-12">
                                      <div class="col-xs-12">
                                        <h4 style="font-size:14px;">Fichier supplémentaire( <small><span style="color:red">*optionnel:</span>. Joindre un fichier autre que ceux envoyés par SPP Globa</small>)</h4>
                                        <div class="col-xs-12">
                                          <input type="text" class="form-control" name="nombreArchivo" placeholder="Nom du fichier">
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
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Fermer</button>
                        <?php 
                        if(empty($solicitud['idmembresia']) && $solicitud['estatus_opp'] != '8'){
                        ?>
                        <button type="submit" class="btn btn-success" style="width:100%" id="<?php echo 'boton1'.$solicitud['idsolicitud_certificacion']; ?>" name="guardar_proceso" value="1">Enregistrer le processus</button>
                        <?php 
                        if($solicitud['tipo_solicitud'] == 'RENOVACION'){
                        ?>
                        <button type="submit" class="btn btn-success" id="<?php echo 'boton2'.$solicitud['idsolicitud_certificacion']; ?>" name="guardar_proceso" value="1" onclick="return validarRenovacion()" style="width:100%; display:none" >Envoyer l'avis</button>
                        <?php
                        }else{
                        ?>
                        <button type="submit" class="btn btn-success" id="<?php echo 'boton2'.$solicitud['idsolicitud_certificacion']; ?>" name="guardar_proceso" value="1" onclick="return validar()" style="width:100%; display:none" >Envoyer l'avis</button>
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
                echo "Non disponible";
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
            <button type="button" class="btn btn-sm btn-info" style="width:100%" data-toggle="modal" data-target="<?php echo "#certificado".$solicitud['idsolicitud']; ?>">Télécharger le certificat</button>
          </td>
                <!-- inicia modal estatus_Certificado -->

                <div id="<?php echo "certificado".$solicitud['idsolicitud']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                  <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="myModalLabel">Télécharger le certificat</h4>
                      </div>
                      <div class="modal-body">
                        <div class="row">
                          <div class="col-md-6">
                            <?php 
                            if($solicitud['tipo_solicitud'] == 'RENOVACION'){
                              //22_03_2017if($proceso_certificacion['estatus_membresia'] == "APROBADA"){
                              if(isset($proceso_certificacion['estatus_membresia'])){  
                                if(isset($solicitud['idformato_evaluacion']) && isset($solicitud['idinforme_evaluacion']) && isset($solicitud['iddictamen_evaluacion'])){

                                  $row_formato = mysql_query("SELECT * FROM formato_evaluacion WHERE idformato_evaluacion = $solicitud[idformato_evaluacion]", $dspp) or die(mysql_error());
                                  $formato = mysql_fetch_assoc($row_formato);
                                  $row_informe = mysql_query("SELECT * FROM informe_evaluacion WHERE idinforme_evaluacion = $solicitud[idinforme_evaluacion]", $dspp) or die(mysql_error());
                                  $informe = mysql_fetch_assoc($row_informe);
                                  $row_dictamen = mysql_query("SELECT * FROM dictamen_evaluacion WHERE iddictamen_evaluacion = $solicitud[iddictamen_evaluacion]", $dspp) or die(mysql_error());
                                  $dictamen = mysql_fetch_assoc($row_dictamen);

                                ?>
                                <p>Statut du formulaire d'évaluation: <span style="color:red"><?php echo $formato['estatus_formato']; ?></span></p>
                                <a href="<?php echo $formato['archivo']; ?>" class="btn btn-info" style="width:100%" target="_blank">Télécharger</a>

                                <p>Statut de l'opinion d'évaluation: <span style="color:red"><?php echo $dictamen['estatus_dictamen']; ?></span></p>
                                <a href="<?php echo $dictamen['archivo']; ?>" class="btn btn-info" style="width:100%" target="_blank">Télécharger L'opinion d'évaluation</a>
                                <p>Statut du rapport d'évaluation: <span style="color:red"><?php echo $informe['estatus_informe']; ?></span></p>
                                <a href="<?php echo $informe['archivo']; ?>" class="btn btn-info" style="width:100%" target="_blank">Télécharger Rapport d'évaluation</a>

                                <?php
                                }else{
                                ?>
                                  <p class="alert alert-info">Veuillez charger les documents suivants:</p>
                                  <p class="alert alert-info">

                                    Formulaire d'évaluation
                                    <input type="file" class="form-control" name="formato_evaluacion" >

                                    L'opinion d'évaluation
                                    <input type="file" class="form-control" name="informe_evaluacion" >
                       
                                    Rapport d'évaluation
                                    <input type="file" class="form-control" name="dictamen_evaluacion" >

                                  </p>
                                  <button type="submit" class="btn btn-success" style="width:100%" name="cargar_documentos" value="1">Envoyer des documents</button>
                                <?php
                                }

                              }else{
                                echo "<p class='alert alert-danger'>Aun no se ha \"Aprobado\" la membresia</p>";
                              }
                            }else{
                              //22_03_2017if($solicitud['estatus_contrato'] == 'ACEPTADO' && $proceso_certificacion['estatus_membresia'] == "APROBADA"){
                              if(isset($proceso_certificacion['estatus_membresia'])){
                                if(isset($solicitud['idformato_evaluacion']) && isset($solicitud['idinforme_evaluacion']) && isset($solicitud['iddictamen_evaluacion'])){

                                  $row_formato = mysql_query("SELECT * FROM formato_evaluacion WHERE idformato_evaluacion = $solicitud[idformato_evaluacion]", $dspp) or die(mysql_error());
                                  $formato = mysql_fetch_assoc($row_formato);
                                  $row_informe = mysql_query("SELECT * FROM informe_evaluacion WHERE idinforme_evaluacion = $solicitud[idinforme_evaluacion]", $dspp) or die(mysql_error());
                                  $informe = mysql_fetch_assoc($row_informe);
                                  $row_dictamen = mysql_query("SELECT * FROM dictamen_evaluacion WHERE iddictamen_evaluacion = $solicitud[iddictamen_evaluacion]", $dspp) or die(mysql_error());
                                  $dictamen = mysql_fetch_assoc($row_dictamen);

                                ?>
                                <p>Statut du formulaire d'évaluation: <span style="color:red"><?php echo $formato['estatus_formato']; ?></span></p>
                                <a href="<?php echo $formato['archivo']; ?>" class="btn btn-info" style="width:100%" target="_blank">Télécharger Formulaire d'évaluation</a>

                                <p>Statut de l'opinion d'évaluation: <span style="color:red"><?php echo $dictamen['estatus_dictamen']; ?></span></p>
                                <a href="<?php echo $dictamen['archivo']; ?>" class="btn btn-info" style="width:100%" target="_blank">Télécharger L'opinion d'évaluation</a>
                                <p>Statut du rapport d'évaluation: <span style="color:red"><?php echo $informe['estatus_informe']; ?></span></p>
                                <a href="<?php echo $informe['archivo']; ?>" class="btn btn-info" style="width:100%" target="_blank">Télécharger Rapport d'évaluation</a>

                                <?php
                                }else{
                                ?>
                                  <p class="alert alert-info">Veuillez télécharger les documents suivants:</p>
                                  <p class="alert alert-info">

                                    Formulaire d'évaluation
                                    <input type="file" class="form-control" name="formato_evaluacion" >

                                    L'opinion d'évaluation
                                    <input type="file" class="form-control" name="informe_evaluacion" >
                       
                                    Rapport d'évaluation
                                    <input type="file" class="form-control" name="dictamen_evaluacion" >

                                  </p>
                                  <button type="submit" class="btn btn-success" style="width:100%" name="cargar_documentos" value="1">Envoyer des documents</button>
                                <?php
                                }

                              }else{
                                if(!isset($solicitud['idcontrato'])){
                                  echo "<p class='alert alert-danger'>L'Accord d'utilisation n'a pas encore été \"Chargé\"</p>";
                                }else{
                                  echo "<p class='alert alert-warning'>L'Accord d'utilisation n'a pas encore été APPROUVÉ</p>";
                                }
                                
                              }
                            }
                             ?>
                          </div>
                          
                          <div class="col-md-6">
                            <h4 style="font-size:14px;">Télécharger le certificat</h4>
                            <?php 
                            if(isset($solicitud['iddictamen_evaluacion']) && isset($solicitud['idformato_evaluacion']) && isset($solicitud['idinforme_evaluacion'])){

                                $row_formato = mysql_query("SELECT * FROM formato_evaluacion WHERE idformato_evaluacion = $solicitud[idformato_evaluacion]", $dspp) or die(mysql_error());
                                $formato = mysql_fetch_assoc($row_formato);
                                $row_informe = mysql_query("SELECT * FROM informe_evaluacion WHERE idinforme_evaluacion = $solicitud[idinforme_evaluacion]", $dspp) or die(mysql_error());
                                $informe = mysql_fetch_assoc($row_informe);
                                $row_dictamen = mysql_query("SELECT * FROM dictamen_evaluacion WHERE iddictamen_evaluacion = $solicitud[iddictamen_evaluacion]", $dspp) or die(mysql_error());
                                $dictamen = mysql_fetch_assoc($row_dictamen);

                                if($solicitud['tipo_solicitud'] == 'RENOVACION'){ // EN CASO DE QUE SEA UNA SOLICITUD EN RENOVACIÓN
                                  // inicia validación ///
                                  if(($formato['estatus_formato'] == 'ACEPTADO' && $informe['estatus_informe'] == 'ACEPTADO' && $dictamen['estatus_dictamen'] == 'ACEPTADO')){
                                    if(isset($solicitud['idcertificado'])){
                                      $row_certificado = mysql_query("SELECT * FROM certificado WHERE idcertificado = $solicitud[idcertificado]", $dspp) or die(mysql_error());
                                      $certificado = mysql_fetch_assoc($row_certificado);
                                      $inicio = strtotime($certificado['vigencia_inicio']);
                                      $fin = strtotime($certificado['vigencia_fin']);
                                    ?>
                                      <p class="alert alert-info">
                                        Le certificat a été téléchargé, il est valide du <b><?php echo date('d/m/Y', $inicio); ?></b> au <b><?php echo date('d/m/Y', $fin); ?></b>
                                      </p>
                                      <a href="<?php echo $certificado['archivo']; ?>" class="btn btn-success" style="width:100%" target="_blank">Télécharger le certificat</a>
                                    <?php
                                    }else{
                                    ?>
                                      <div class="col-md-12">
                                        <p class="alert alert-info">Meci de définir la date de début et de fin du certificat.</p>
                                        <p class="alert alert-warning" style="padding:5px;">Au cas où le calendrier ne s'ouvrirait pas, merci d'indqiquer la date sous la forme <span style="color:red">jj-mm-aaaa</span></p>
                                      </div>
                                      <div class="col-md-6">
                                        <label for="fecha_inicio">Date de début</label> 
                                        <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" placeholder="jj-mm-aaaa" required> 
                                      </div>
                                      <div class="col-md-6">
                                        <label for="fecha_fin">Fin du certificat</label>
                                        <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" placeholder="jj-mm-aaaa" required>
                                      </div>
                                      
                                      <label for="certificado">Merci de sélectionner le certificat</label>
                                      <input type="file" name="certificado" id="certificado" class="form-control" required>
                                      <button type="submit" name="enviar_certificado" value="1" class="btn btn-success" style="width:100%">Envoyer le certificat</button>  
                                    <?php
                                    }
                                  }else{
                                    echo '<p class="alert alert-warning">
                                    Una vez aprobada la documentación necesaria por parte de SPP Global, podra cargar el Certificado SPP.
                                    </p> ';
                                  }
                                  // termina validación //
                                }else{ /// EN CASO DE QUE SEA UNA NUEVA SOLICITUD
                                  // inicia validación ///
                                  if(($formato['estatus_formato'] == 'ACEPTADO' && $informe['estatus_informe'] == 'ACEPTADO' && $dictamen['estatus_dictamen'] == 'ACEPTADO') && ($solicitud['estatus_contrato'] == 'ACEPTADO')){
                                    if(isset($solicitud['idcertificado'])){
                                      $row_certificado = mysql_query("SELECT * FROM certificado WHERE idcertificado = $solicitud[idcertificado]", $dspp) or die(mysql_error());
                                      $certificado = mysql_fetch_assoc($row_certificado);
                                      $inicio = strtotime($certificado['vigencia_inicio']);
                                      $fin = strtotime($certificado['vigencia_fin']);
                                    ?>
                                      <p class="alert alert-info">
                                        Le certificat a été téléchargé, il est valide du <b><?php echo date('d/m/Y', $inicio); ?></b> au <b><?php echo date('d/m/Y', $fin); ?></b>
                                      </p>
                                      <a href="<?php echo $certificado['archivo']; ?>" class="btn btn-success" style="width:100%" target="_blank">Télécharger le certificat</a>
                                    <?php
                                    }else{
                                    ?>
                                      <div class="col-md-12">
                                        <p class="alert alert-info">
                                          Meci de définir la date de début et de fin du certificat.
                                        </p>
                                        <p class="alert alert-warning" style="padding:5px;">
                                          Au cas où le calendrier ne s'ouvrirait pas, merci d'indqiquer la date sous la forme <span style="color:red">dd-mm-aaaa</span>
                                        </p>
                                      </div>
                                      <div class="col-md-6">
                                        <label for="fecha_inicio">Date de début</label> 
                                        <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" placeholder="dd-mm-aaaa" required> 
                                      </div>
                                      <div class="col-md-6">
                                        <label for="fecha_fin">Fin du certificat</label>
                                        <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" placeholder="dd-mm-aaaa" required>
                                      </div>
                                      
                                      <label for="certificado">Merci de sélectionner le certificat</label>
                                      <input type="file" name="certificado" id="certificado" class="form-control" required>
                                      <button type="submit" name="enviar_certificado" value="1" class="btn btn-success" style="width:100%">Envoyer le certificat</button>  
                                    <?php
                                    }
                                  }else{
                                    echo '<p class="alert alert-warning">
                                   Une fois que la documentation nécessaire aura été approuvée par SPP Global, vous pourrez télécharger le certificat SPP.
                                    </p> ';
                                  }
                                  // termina validación //
                                }
                            }else{
                              if($solicitud['tipo_solicitud'] == 'RENOVACION'){
                                echo '<p class="alert alert-warning">
                                  Les documents d\'évaluation n\'ont pas encore été téléchargés.
                                </p> ';
                              }else{
                                echo '<p class="alert alert-warning">
                                  Une fois que la documentation nécessaire aura été approuvée par SPP Global, vous pourrez télécharger le certificat SPP.
                                </p> ';
                              }
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
                        <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- termina modal estatus_Certificado -->

          <!---- TERMINA SECCION CERTIFICADO ------>
            </form>
          <td>
            <a class="btn btn-sm btn-primary" data-toggle="tooltip" title="Détail de la demande" href="?SOLICITUD&IDsolicitud=<?php echo $solicitud['idsolicitud']; ?>">Détail de la demande</a>
          </td>
          <td>
            <form action="../../reportes/solicitud_fra.php" method="POST" target="_new">
              <button class="btn btn-xs btn-default" data-toggle="tooltip" title="Télécharger le demande" target="_new" type="submit" ><img src="../../img/pdf.png" style="height:30px;" alt=""></button>

              <input type="hidden" name="idsolicitud_certificacion" value="<?php echo $solicitud['idsolicitud']; ?>">
              <input type="hidden" name="generar_formato" value="1">
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