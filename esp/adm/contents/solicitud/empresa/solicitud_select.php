<?php 
require_once('../Connections/dspp.php'); 
require_once('../Connections/mail.php');

//error_reporting(E_ALL ^ E_DEPRECATED);
mysql_select_db($database_dspp, $dspp);

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
$fecha = time();
$spp_global = "cert@spp.coop";
$administrador = "yasser.midnight@gmail.com";

if(isset($_POST['aprobar_periodo']) && $_POST['aprobar_periodo'] == 1){
  $reemplazar = array("/",";"); /// caracteres que se reemplazaran de los correos
  $idperiodo_objecion = $_POST['idperiodo_objecion2'];

  $estatus_dspp = 6; //INICIA PERIODO DE OBJECIÓN

  $estatus_objecion = "ACTIVO";
  
  $updateSQL = sprintf("UPDATE solicitud_registro SET estatus_dspp = %s WHERE idsolicitud_registro = %s",
    GetSQLValueString($estatus_dspp, "int"),
    GetSQLValueString($_POST['idsolicitud_registro'], "int"));
  $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

  //ACTUALIZAMOS EL PERIODO DE OBJECIÓN
  $updateSQL = sprintf("UPDATE periodo_objecion SET estatus_objecion = %s WHERE idperiodo_objecion = %s",
    GetSQLValueString($estatus_objecion, "text"),
    GetSQLValueString($idperiodo_objecion, "int"));
 $actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());


  //INSERTAMOS EL PROCESO DE CERTIFICACIÓN
  $insertSQL = sprintf("INSERT INTO proceso_certificacion (idsolicitud_registro, estatus_dspp, fecha_registro) VALUES (%s, %s, %s)",
    GetSQLValueString($_POST['idsolicitud_registro'], "int"),
    GetSQLValueString($estatus_dspp, "int"),
    GetSQLValueString($fecha, "int"));
  $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

  /// se consultan los datos de de solicitud, empresa, oc para el mensaje
  //$row_empresa = mysql_query("SELECT solicitud_registro.*, empresa.idempresa, empresa.nombre AS 'nombre_empresa', empresa.abreviacion AS 'abreviacion_empresa', empresa.telefono, empresa.email, empresa.pais, oc.nombre AS 'nombre_oc', oc.email1 AS 'email_oc' FROM solicitud_registro LEFT JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa LEFT JOIN oc ON solicitud_registro.idoc = oc.idoc WHERE idsolicitud_registro = $_POST[idsolicitud_registro]", $dspp) or die(mysql_error());

  $row_empresa = mysql_query("SELECT solicitud_registro.idempresa, solicitud_registro.idoc, empresa.nombre AS 'nombre_empresa', empresa.abreviacion AS 'abreviacion_empresa', empresa.maquilador, empresa.comprador, empresa.intermediario, empresa.telefono, empresa.email, empresa.pais, oc.nombre AS 'nombre_oc', oc.email1 AS 'email_oc', oc.email2 AS 'email_oc2' FROM periodo_objecion LEFT JOIN solicitud_registro ON periodo_objecion.idsolicitud_registro = solicitud_registro.idsolicitud_registro LEFT JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa LEFT JOIN oc ON solicitud_registro.idoc = oc.idoc WHERE periodo_objecion.idperiodo_objecion = $idperiodo_objecion", $dspp) or die(mysql_error());

  $detalle_empresa = mysql_fetch_assoc($row_empresa);

  $tipo = '';
  if(isset($detalle_empresa['maquilador'])){
    $tipo = 'MAQUILADOR';
  }else if(isset($detalle_empresa['intermediario'])){
    $tipo = 'INTERMEDIARIO';
  }else if(isset($detalle_empresa['comprador'])){
    $tipo = 'COMPRADOR FINAL';
  }

 ///INICIA ENVIAR MENSAJE PERIODO DE OBJECIÓN
  $row_periodo = mysql_query("SELECT fecha_inicio, fecha_fin FROM periodo_objecion WHERE idperiodo_objecion = $idperiodo_objecion",$dspp) or die(mysql_error());
  $periodo = mysql_fetch_assoc($row_periodo);

    $asunto = "D-SPP | Aviso Notificación de Intenciones de Certificación /<br> Intentions Notification of certification";

    $cuerpo_mensaje = '
      <html>
        <head>
          <meta charset="utf-8">
        </head>
        <body>
        
          <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="700px">
            <thead>
              <tr>
                <th>
                  <img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" />
                </th>
                <th style="text-align:left">
                  D-SPP | Aviso Notificación de Intenciones de Certificación / Intentions Certification
                </th>
              </tr>
            </thead>
            <tbody>

              <tr style="width:100%">
                <td colspan="2">
                  <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;" border="1" width="650px">

                    <tr style="font-size: 12px; text-align:center; background-color:#dff0d8; color:#3c763d;" height="50px;">
                      <td style="text-align:center">Tipo / Type</td>
                      <td style="text-align:center">Nombre de la Empresa/Company name</td>
                      <td style="text-align:center">Abreviación / Short name</td>
                      <td style="text-align:center">País / Country</td>
                      <td style="text-align:center">Organismo de Certificación / Certification Entity</td>

                      <td style="text-align:center">Tipo de solicitud / Kind of application</td>
                      <td style="text-align:center">Fecha de solicitud/Date of application</td>
                      <td style="text-align:center">Fin período de objeción/Objection period end</td>
                    </tr>
                    <tr style="font-size:12px">
                      <td>'.$tipo.'</td>
                      <td>'.$detalle_empresa['nombre_empresa'].'</td>
                      <td>'.$detalle_empresa['abreviacion_empresa'].'</td>
                      <td>'.$detalle_empresa['pais'].'</td>
                      <td>'.$detalle_empresa['nombre_oc'].'</td>
                      <td>Certificación</td>
                      <td>'.date('d/m/Y', $periodo['fecha_inicio']).'</td>
                      <td>'.date('d/m/Y', $periodo['fecha_fin']).'</td>
                    </tr>
                </td>
                </table>
              </tr>
          <tr>
            <td style="text-align:justify;" colspan="2">
              SPP GLOBAL publica y notifica las "Intenciones de Certificación, Registro o Autorización" basada en nuevas solicitudes de: 1) Certificación de Organizaciones de Pequeños Productores, 2) Registro de Compradores y otros actores y 3) Autorización de Organismos de Certificación, con el objetivo de informarles y recibir las eventuales objeciones contra la incorporación de los solicitantes.
              Estas eventuales objeciones presentadas deben estar sustentadas con información concreta y verificable con respecto a incumplimientos de la Normatividad del SPP y/o nuestro Código de Conducta (disponibles en <a href="http://www.spp.coop/"><strong>www.spp.coop</strong></a>, en el área de Funcionamiento). Las objeciones presentadas y enviadas a <a href="cert@spp.coop"><strong>cert@spp.coop</strong></a> serán tomadas en cuenta en los procesos de certificación, registro o autorización.
              Estas notificaciones son enviadas por SPP GLOBAL en un lapso menor a 24 horas a partir del momento en que le llegue la solicitud correspondiente. Si se presentan objeciones antes de que el solicitante se Certifique, Registre o Autorice su tratamiento por parte del Organismo de Certificación debe ser parte de la misma evaluación documental. Si la objeción se presenta cuando el Solicitante ya esta Certificado se aplica el Procedimiento de Inconformidades del Símbolo de Pequeños Productores. Las nuevas intenciones de Certificación, Registro o Autorización, se detallan al inicio de este documento.  
              <br><br>
              SPP GLOBAL publishes and notifies the "Certification, Registration and Authorization Intentions" based on new applications submitted for: 1) Certification of Small Producers\' Organizations, 2) Registration of Buyers and other stakeholders, and 3) Authorization of Certification Entities, with the objective of keeping you informed and receiving any objections to the incorporation of any new applicants into the system.
              Any objections submitted must be supported with concrete, verifiable information regarding non-compliance with the Standards and/or Code of Conduct of the Small Producers\' Symbol (available at <a href="http://www.spp.coop/"><strong>www.spp.coop</strong></a> in the section on Operation). The objections submitted and sent to <a href="cert@spp.coop"><strong>cert@spp.coop</strong></a> will be taken into consideration during certification, registration and authorization processes.
              These notifications are sent by SPP GLOBAL in a period of less than 24 hours from the time a corresponding application is received. If objections are presented before getting the Certification, Registration or Authorization, the Certification Entity must incorporate them as part of the same evaluation-process. If the objection is presented when the applicant has already been certified, the SPP Dissents Procedure has to be applied. The new intentions for Certification, Registration and Authorization are detailed at the beginning (of this document).
            </td>
          </tr>
            </tbody>
          </table>

        </body>
      </html>
    ';

    ///// inicia envio a correos Empresas
      $query_empresa = "SELECT email FROM empresa WHERE email !=''";
      $ejecutar = mysql_query($query_empresa,$dspp) or die(mysql_error());

      while($email_empresa = mysql_fetch_assoc($ejecutar)){
        if(!empty($email_empresa['email'])){
          
          $token = strtok($email_empresa['email'], "\/\,\;");

          while ($token !== false)
          {
            $mail->AddAddress($token);
            $token = strtok('\/\,\;');
          }

          //$limpio = str_replace($reemplazar, ',', $email_empresa['email']);
          //$limpio2 = $limpio;
          //$mail->AddAddress($limpio2);
        }
      }


        $mail->Subject = utf8_decode($asunto);
        $mail->Body = utf8_decode($cuerpo_mensaje);
        $mail->MsgHTML(utf8_decode($cuerpo_mensaje));
        $mail->Send();
        $mail->ClearAddresses();

    ///// termina envio a correo Empresas

    //// inicia envio a correo OPP
      $query_opp = "SELECT email FROM opp WHERE email !=''";
      $ejecutar = mysql_query($query_opp,$dspp) or die(mysql_error());


      while($email_opp = mysql_fetch_assoc($ejecutar)){
        if(!empty($email_opp['email'])){
          
          $token = strtok($email_opp['email'], "\/\,\;");

          while ($token !== false)
          {
            $mail->AddAddress($token);
            $token = strtok('\/\,\;');
          }
          //$limpio = str_replace($reemplazar, ' ', $email_opp['email']);
          //$mail->AddAddress($limpio);
        }
      }


        $mail->Subject = utf8_decode($asunto);
        $mail->Body = utf8_decode($cuerpo_mensaje);
        $mail->MsgHTML(utf8_decode($cuerpo_mensaje));
        $mail->Send();
        $mail->ClearAddresses();

    //// termina envio a correo OPP

    //// inicia envio a correo OC
      $query_oc = "SELECT email1, email2 FROM oc";
      $ejecutar = mysql_query($query_oc,$dspp) or die(mysql_error());


      while($email_oc = mysql_fetch_assoc($ejecutar)){
        if(!empty($email_oc['email1'])){
          //$limpio = str_replace($reemplazar, ' ', $email_oc['email1']);
          //$mail->AddAddress($limpio);
          $token = strtok($email_oc['email1'], "\/\,\;");

          while ($token !== false)
          {
            $mail->AddAddress($token);
            $token = strtok('\/\,\;');
          }
        }
        if(!empty($email_oc['email2'])){
          //$limpio = str_replace($reemplazar, ' ', $email_oc['email2']);
          //$mail->AddAddress($limpio);
          $token = strtok($email_oc['email2'], "\/\,\;");

          while ($token !== false)
          {
            $mail->AddAddress($token);
            $token = strtok('\/\,\;');
          }
        }
      }


        $mail->Subject = utf8_decode($asunto);
        $mail->Body = utf8_decode($cuerpo_mensaje);
        $mail->MsgHTML(utf8_decode($cuerpo_mensaje));
        $mail->Send();
        $mail->ClearAddresses();

    //// termina envio a correo OC

      //// ENVIO DE NOTIFICACIONES A LAS LISTAS DE CONTACTOS APROBADAS
      $query_contactos = mysql_query("SELECT lista_contactos.idlista_contactos, contactos.lista_contactos, contactos.email1, contactos.email2 FROM lista_contactos INNER JOIN contactos ON lista_contactos.idlista_contactos = contactos.lista_contactos WHERE lista_contactos.notificaciones = 1", $dspp) or die(mysql_error());

      while($lista_contactos = mysql_fetch_assoc($query_contactos)){
        if(!empty($lista_contactos['email1'])){
          //$limpio = str_replace($reemplazar, ' ', $lista_contactos['email1']);
          //$mail->AddAddress($limpio);
          $token = strtok($lista_contactos['email1'], "\/\,\;");

          while ($token !== false)
          {
            $mail->AddAddress($token);
            $token = strtok('\/\,\;');
          }
        }
        if(!empty($lista_contactos['email2'])){
          //$limpio = str_replace($reemplazar, ' ', $lista_contactos['email2']);
          //$mail->AddAddress($limpio);
          $token = strtok($lista_contactos['email2'], "\/\,\;");

          while ($token !== false)
          {
            $mail->AddAddress($token);
            $token = strtok('\/\,\;');
          }

        }
      }


        $mail->Subject = utf8_decode($asunto);
        $mail->Body = utf8_decode($cuerpo_mensaje);
        $mail->MsgHTML(utf8_decode($cuerpo_mensaje));
        $mail->Send();
        $mail->ClearAddresses();

    //// inicia envio a correo ADM
        $query_adm = "SELECT email FROM adm";
        $ejecutar = mysql_query($query_adm,$dspp) or die(mysql_error());

        while($email_adm = mysql_fetch_assoc($ejecutar)){  
          if($email_adm['email'] != "procu@spp.coop" ){
            $mail->AddAddress($email_adm['email']);
          }
        }

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
          echo "<script>alert('Error, no se pudo enviar el correo');location.href ='javascript:history.back()';</script>";
        }*/

    //// termina envio a correo ADM


 /// TERMINA ENVIAR MENSAJE PERIODO DE OBJECIÓN

 $mensaje = "Se ha iniciado el Periodo de Objeción";

}
//SE CARGA Y ENVIA LA RESOLUCIÓN DE OBJECIÓN
if(isset($_POST['enviar_resolucion']) && $_POST['enviar_resolucion'] == 1){
  /// se consultan los datos de de solicitud, empresa, oc para el mensaje
  $idperiodo_objecion = $_POST['idperiodo_objecion'];


  $ruta = "../../archivos/admArchivos/resolucion/";

  if(!empty($_FILES['cargar_resolucion']['name'])){
    $_FILES['cargar_resolucion']['name'];
        move_uploaded_file($_FILES["cargar_resolucion"]["tmp_name"], $ruta.$fecha."_".$_FILES["cargar_resolucion"]["name"]);
        $resolucion = $ruta.basename($fecha."_".$_FILES["cargar_resolucion"]["name"]);
  }else{
    $resolucion = NULL;
  }

  $updateSQL = sprintf("UPDATE solicitud_registro SET estatus_dspp = %s WHERE idsolicitud_registro = %s",
    GetSQLValueString(7, "int"),
    GetSQLValueString($_POST['idsolicitud_registro'], "int"));
  $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

  $row_empresa = mysql_query("SELECT solicitud_registro.idoc, solicitud_registro.idempresa, empresa.idempresa, empresa.nombre AS 'nombre_empresa', empresa.abreviacion AS 'abreviacion_empresa', solicitud_registro.comprador_final, solicitud_registro.intermediario, solicitud_registro.maquilador, empresa.telefono, empresa.email AS 'email_empresa', empresa.pais, oc.nombre AS 'nombre_oc', oc.email1 AS 'email_oc1', oc.email2 AS 'email_oc2' FROM solicitud_registro LEFT JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa LEFT JOIN oc ON solicitud_registro.idoc = oc.idoc WHERE idsolicitud_registro = $_POST[idsolicitud_registro]", $dspp) or die(mysql_error());
  $detalle_empresa = mysql_fetch_assoc($row_empresa);

  $row_periodo = mysql_query("SELECT fecha_inicio, fecha_fin FROM periodo_objecion WHERE idperiodo_objecion = $_POST[idperiodo_objecion]",$dspp) or die(mysql_error());
  $periodo = mysql_fetch_assoc($row_periodo);

  //actualizamos el periodo de objeción
  $estatus_objecion = 'FINALIZADO';

  $updateSQL = sprintf("UPDATE periodo_objecion SET estatus_objecion = %s, observacion = %s, dictamen = %s, documento = %s WHERE idperiodo_objecion = %s",
    GetSQLValueString($estatus_objecion, "text"),
    GetSQLValueString($_POST['observacion'], "text"),
    GetSQLValueString($_POST['dictamen'], "text"),
    GetSQLValueString($resolucion, "text"),
    GetSQLValueString($_POST['idperiodo_objecion'], "int"));
  $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());
    
  $tipo = '';
  if(isset($detalle_empresa['comprador_final'])){
    $tipo .= 'COMPRADOR FINAL / FINAL BUYER<br>';
  }
  if(isset($detalle_empresa['intermediario'])){
    $tipo .= 'INTERMEDIARIO / INTERMEDIARY<br>';
  }
  if(isset($detalle_empresa['maquilador'])){
    $tipo .= 'MAQUILADOR / MAQUILA COMPANY<br>';
  }

  /// inicia envio correo "periodo de objeción finalizado" a OC
  $asunto = "D-SPP | Periodo de Objeción Finalizado";

  $mensaje_oc = '
    <html>
      <head>
        <meta charset="utf-8">
      </head>
      <body>
      
        <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="700px">
          <thead>
            <tr>
              <th>
                <img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" />
              </th>
              <th style="text-align:left">
                D-SPP | Periodo de Objeción Finalizado / Objection Period Ended
                
              </th>
            </tr>
          </thead>
          <tbody>
            <tr style="text-align:left">
              <td colspan="2"><p><b>Ha finalizado el periodo de objeción con una resolución: <span style="color:red;">'.$_POST['dictamen'].'</span></b></p></td>
            </tr>
            <tr> 
              <td colspan="2">Fecha Inicio: <span style="color:red">'.date('d/m/Y', $periodo['fecha_inicio']).'</span></td>
            </tr>
            <tr>
              <td colspan="2">Fecha Fin: <span style="color:red">'.date('d/m/Y', $periodo['fecha_fin']).'</span></td>
            </tr>
            <tr>
              <td colspan="2">
                Ahora puede iniciar el proceso de certificación, por favor ponerse en contacto con:
                
                <p>Organización: <span style="color:red">'.$detalle_empresa['nombre_empresa'].'</span></p>
                
                <p>Telefono / phone: <span style="color:red">'.$detalle_empresa['telefono'].'</span></p>
                
                <p>Email: <span style="color:red">'.$detalle_empresa['email_empresa'].'</span></p>
              </td>
            </tr>
            <tr style="width:100%">
              <td colspan="2">
                <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;" border="1" width="650px">

                  <tr style="font-size: 12px; text-align:center; background-color:#dff0d8; color:#3c763d;" height="50px;">
                    <td style="text-align:center">Tipo / Type</td>
                    <td style="text-align:center">Nombre de la Empresa/Company name</td>
                    <td style="text-align:center">Abreviación / Short name</td>
                    <td style="text-align:center">País / Country</td>
                    <td style="text-align:center">Organismo de Certificación / Certification Entity</td>
                    <td style="text-align:center">Tipo de solicitud / Kind of application</td>
                    <td style="text-align:center">Fecha de solicitud/Date of application</td>
                    <td style="text-align:center">Fin período de objeción/Objection period end</td>
                  </tr>
                  <tr style="font-size:12px;">
                    <td>'.$tipo.'</td>
                    <td>'.$detalle_empresa['nombre_empresa'].'</td>
                    <td>'.$detalle_empresa['abreviacion_empresa'].'</td>
                    <td>'.$detalle_empresa['pais'].'</td>
                    <td>'.$detalle_empresa['nombre_oc'].'</td>
                    <td>Registro</td>
                    <td>'.date('d/m/Y', $periodo['fecha_inicio']).'</td>
                    <td>'.date('d/m/Y', $periodo['fecha_fin']).'</td>
                  </tr>
              </td>
              </table>
            </tr>

          </tbody>
        </table>

      </body>
    </html>
  ';
  if(!empty($detalle_empresa['email_oc1'])){
          $token = strtok($detalle_empresa['email_oc1'], "\/\,\;");
          while ($token !== false)
          {
            $mail->AddAddress($token);
            $token = strtok('\/\,\;');
          }

  }
  if(!empty($detalle_empresa['email_oc2'])){
          $token = strtok($detalle_empresa['email_oc2'], "\/\,\;");
          while ($token !== false)
          {
            $mail->AddAddress($token);
            $token = strtok('\/\,\;');
          }

  }

  $mail->AddBCC($spp_global);  
  $mail->AddAttachment($resolucion);

  $mail->Subject = utf8_decode($asunto);
  $mail->Body = utf8_decode($mensaje_oc);
  $mail->MsgHTML(utf8_decode($mensaje_oc));
  $mail->Send();
  $mail->ClearAddresses();
  $mail->clearAttachments();

  /// termina envio correo "periodo de objeción finalizado" a OC

  /// inicia envio correo "periodo de objeción finalizado" a OC
  $asunto = "D-SPP | Periodo de Objeción Finalizado";

  $mensaje_opp = '
    <html>
      <head>
        <meta charset="utf-8">
      </head>
      <body>
      
        <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="700px">
          <thead>
            <tr>
              <th>
                <img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" />
              </th>
              <th style="text-align:left">
                D-SPP | Periodo de Objeción Finalizado / Objection Period Ended
                
              </th>
            </tr>
          </thead>
          <tbody>
            <tr style="text-align:left">
              <td colspan="2"><p><b>Ha finalizado el periodo de objeción con una resolución: <span style="color:red;">'.$_POST['dictamen'].'</span></b></p></td>
            </tr>
            <tr> 
              <td colspan="2">Fecha Inicio: <span style="color:red">'.date('d/m/Y', $periodo['fecha_inicio']).'</span></td>
            </tr>
            <tr>
              <td colspan="2">Fecha Fin: <span style="color:red">'.date('d/m/Y', $periodo['fecha_fin']).'</span></td>
            </tr>
            <tr>
              <td colspan="2">
                Ha finalizado el periodo de objeción. Se ha iniciado el Proceso de Certificación, por favor ponerse en contacto con su Organismo de Certificación.
                
                <p>Organismo de Certificación: <span style="color:red">'.$detalle_empresa['nombre_oc'].'</span></p>
                
                <p>Email: <span style="color:red">'.$detalle_empresa['email_oc1'].'</span></p>
              </td>
            </tr>
            <tr style="width:100%">
              <td colspan="2">
                <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;" border="1" width="650px">

                  <tr style="font-size: 12px; text-align:center; background-color:#dff0d8; color:#3c763d;" height="50px;">
                    <td style="text-align:center">Tipo / Type</td>
                    <td style="text-align:center">Nombre de la Empresa/Company name</td>
                    <td style="text-align:center">Abreviación / Short name</td>
                    <td style="text-align:center">País / Country</td>
                    <td style="text-align:center">Organismo de Certificación / Certification Entity</td>
                    <td style="text-align:center">Tipo de solicitud / Kind of application</td>
                    <td style="text-align:center">Fecha de solicitud/Date of application</td>
                    <td style="text-align:center">Fin período de objeción/Objection period end</td>
                  </tr>
                  <tr style="font-size:12px;">
                    <td>'.$tipo.'</td>
                    <td>'.$detalle_empresa['nombre_empresa'].'</td>
                    <td>'.$detalle_empresa['abreviacion_empresa'].'</td>
                    <td>'.$detalle_empresa['pais'].'</td>
                    <td>'.$detalle_empresa['nombre_oc'].'</td>
                    <td>Registro</td>
                    <td>'.date('d/m/Y', $periodo['fecha_inicio']).'</td>
                    <td>'.date('d/m/Y', $periodo['fecha_fin']).'</td>
                  </tr>
              </td>
              </table>
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

  if(isset($detalle_empresa['contacto1_email'])){

          $token = strtok($detalle_empresa['contacto1_email'], "\/\,\;");
          while ($token !== false)
          {
            $mail->AddAddress($token);
            $token = strtok('\/\,\;');
          }

  }
  if(isset($detalle_empresa['contacto2_email'])){
          $token = strtok($detalle_empresa['contacto2_email'], "\/\,\;");
          while ($token !== false)
          {
            $mail->AddAddress($token);
            $token = strtok('\/\,\;');
          }

  }
  if(isset($detalle_empresa['adm1_email'])){

          $token = strtok($detalle_empresa['adm1_email'], "\/\,\;");
          while ($token !== false)
          {
            $mail->AddAddress($token);
            $token = strtok('\/\,\;');
          }

  }
  if(isset($detalle_empresa['email_empresa'])){
          $token = strtok($detalle_empresa['email_empresa'], "\/\,\;");
          while ($token !== false)
          {
            $mail->AddAddress($token);
            $token = strtok('\/\,\;');
          }

  }

  $mail->AddBCC($spp_global);

  $mail->AddAttachment($resolucion);
  $mail->Subject = utf8_decode($asunto);
  $mail->Body = utf8_decode($mensaje_opp);
  $mail->MsgHTML(utf8_decode($mensaje_opp));

  if($mail->Send()){
    $mail->ClearAddresses();
    $mail->clearAttachments();
    echo "<script>alert('Correo enviado Exitosamente.');location.href ='javascript:history.back()';</script>";
  }else{
    $mail->ClearAddresses();
    $mail->clearAttachments();
    echo "<script>alert('Error, no se pudo enviar el correo');location.href ='javascript:history.back()';</script>";
  }
  /// termina envio correo "periodo de objeción finalizado" a empresa
  $mensaje = "Se ha enviado correctamente la resolucion de objeción";

}
//SE APRUEBA EL COMPROBANTE DE PAGO
if(isset($_POST['aprobar_comprobante']) && $_POST['aprobar_comprobante'] == 1){
  $estatus_comprobante = "ACEPTADO"; //se acepta el comprobante
  $estatus_membresia = "APROBADA"; //se acepta la membresia
  $estatus_dspp = 18; //MEMBRESIA APROBADA
  //actualizamos comprobante_pago
  $updateSQL = sprintf("UPDATE comprobante_pago SET estatus_comprobante = %s WHERE idcomprobante_pago = %s",
    GetSQLValueString($estatus_comprobante, "text"),
    GetSQLValueString($_POST['idcomprobante_pago'], "int"));
  $actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());
  //actualizamos la membresia
  $updateSQL = sprintf("UPDATE membresia SET estatus_membresia = %s, fecha_registro = %s WHERE idmembresia = %s",
    GetSQLValueString($estatus_membresia, "text"),
    GetSQLValueString($fecha, "int"),
    GetSQLValueString($_POST['idmembresia'], "int"));
  $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

  $updateSQL = sprintf("UPDATE solicitud_registro SET estatus_dspp = %s WHERE idsolicitud_registro = %s",
    GetSQLValueString($estatus_dspp, "int"),
    GetSQLValueString($_POST['idsolicitud_registro'], "int"));
  $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

  //insertarmos el proceso_certificacion
  $insertSQL = sprintf("INSERT INTO proceso_certificacion(idsolicitud_registro, estatus_dspp, fecha_registro) VALUES (%s, %s, %s)",
    GetSQLValueString($_POST['idsolicitud_registro'], "int"),
    GetSQLValueString($estatus_dspp, "int"),
    GetSQLValueString($fecha, "int"));
  $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

  //inicia enviar mensaje aprobacion membresia
  $row_informacion = mysql_query("SELECT solicitud_registro.idempresa, solicitud_registro.contacto1_email, empresa.email, empresa.nombre, oc.email1, oc.email2 FROM solicitud_registro INNER JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa INNER JOIN oc ON solicitud_registro.idoc = oc.idoc WHERE idsolicitud_registro = $_POST[idsolicitud_registro]", $dspp) or die(mysql_error());
  $informacion = mysql_fetch_assoc($row_informacion);

  $asunto = "D-SPP | Membresia SPP aprobada";

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
                  <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">Membresías SPP aprobada</span></p></th>

                </tr>
                <tr>
                 <th scope="col" align="left" width="280"><p>EMPRESA: <span style="color:red">'.$informacion['nombre'].'</span></p></th>
                </tr>

                <tr>
                  <td colspan="2">
                   <p>Felicidades!!! el pago de su membresía fue aprobado, su certificado estara disponible en breve por favor espere.</p>
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

  if(!empty($informacion['contacto1_email'])){
          $token = strtok($informacion['contacto1_email'], "\/\,\;");
          while ($token !== false)
          {
            $mail->AddAddress($token);
            $token = strtok('\/\,\;');
          }

  }
  if(!empty($informacion['email'])){
          $token = strtok($informacion['email'], "\/\,\;");
          while ($token !== false)
          {
            $mail->AddAddress($token);
            $token = strtok('\/\,\;');
          }

  }

  $mail->Subject = utf8_decode($asunto);
  $mail->Body = utf8_decode($cuerpo_mensaje);
  $mail->MsgHTML(utf8_decode($cuerpo_mensaje));
  /*$mail->Send();
  $mail->ClearAddresses();*/
  if($mail->Send()){
    $mail->ClearAddresses();
    echo "<script>alert('Se ha aprobado el pago de la membresia, la EMPRESA sera noticada en breve.');location.href ='javascript:history.back()';</script>";
  }else{
    $mail->ClearAddresses();
    echo "<script>alert('Error, no se pudo enviar el correo, por favor contacte al administrador: soporte@d-spp.org');location.href ='javascript:history.back()';</script>";
  }

  //termina enviar mensaje aprobacion de membresia
  if($_POST['tipo_solicitud'] == 'RENOVACION'){
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
    if(!empty($informacion['email1'])){
          $token = strtok($informacion['email1'], "\/\,\;");
          while ($token !== false)
          {
            $mail->AddAddress($token);
            $token = strtok('\/\,\;');
          }

    }
    if(!empty($informacion['email2'])){
          $token = strtok($informacion['email2'], "\/\,\;");
          while ($token !== false)
          {
            $mail->AddAddress($token);
            $token = strtok('\/\,\;');
          }

    }

    $mail->Subject = utf8_decode($asunto);
    $mail->Body = utf8_decode($cuerpo_mensaje);
    $mail->MsgHTML(utf8_decode($cuerpo_mensaje));
      /*$mail->Send();
      $mail->ClearAddresses();*/
     
    if($mail->Send()){
      $mail->ClearAddresses();
      echo "<script>alert('Se ha aprobado la \"Membresía SPP\", se le ha notificado al OC para que cargue Formato, Dictamen e Informe de Evaluación.');location.href ='javascript:history.back()';</script>";
    }else{
      $mail->ClearAddresses();
      echo "<script>alert('Error, no se pudo enviar el correo, por favor contacte al administrador: soporte@d-spp.org');location.href ='javascript:history.back()';</script>";
    }
      //$mensaje = "Se ha aprobado la \"Membresía SPP\", se le ha notificado al OC para que cargue Formato, Dictamen e Informe de Evaluación";
  }
  else{
    //revisamos el el contrato de uso ya fue aprobado, esto para enviar la notificación al OC de que suba sus archivos
    if(!empty($_POST['idcontrato'])){
      $row_contrato = mysql_query("SELECT * FROM contratos WHERE idcontrato = $_POST[idcontrato]", $dspp) or die(mysql_error());
      $contrato = mysql_fetch_assoc($row_contrato);
      if($contrato['estatus_contrato'] == 'ACEPTADO'){ //si el contrato fue aceptado entonces enviamos el correo al OC
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
          if(isset($informacion['email1'])){
            $token = strtok($informacion['email1'], "\/\,\;");
            while ($token !== false)
            {
              $mail->AddAddress($token);
              $token = strtok('\/\,\;');
            }
          }
          if(isset($informacion['email2'])){
            $token = strtok($informacion['email2'], "\/\,\;");
            while ($token !== false)
            {
              $mail->AddAddress($token);
              $token = strtok('\/\,\;');
            }
          }
          $mail->Subject = utf8_decode($asunto);
          $mail->Body = utf8_decode($cuerpo_mensaje);
          $mail->MsgHTML(utf8_decode($cuerpo_mensaje));
          /*$mail->Send();
          $mail->ClearAddresses();*/
        if($mail->Send()){
          $mail->ClearAddresses();
          echo "<script>alert('Se ha aprobado el \"Contrato de Uso\" y la \"Membresía SPP\", se le ha notificado al OC para que cargue Formato, Dictamen e Informe de Evaluación');location.href ='javascript:history.back()';</script>";
        }else{
          $mail->ClearAddresses();
          echo "<script>alert('Error, no se pudo enviar el correo, por favor contacte al administrador: soporte@d-spp.org');location.href ='javascript:history.back()';</script>";
        }
        //$mensaje = "Se ha aprobado el \"Contrato de Uso\" y la \"Membresía SPP\", se le ha notificado al OC para que cargue Formato, Dictamen e Informe de Evaluación";

      }else{
        $mensaje = "Se ha aprobado la membresia";
      }
    }else{
      $mensaje = "Se ha aprobado la membresia";
    }
  }

}

//SE RECHAZA EL COMPROBANTE DE PAGO
if(isset($_POST['rechazar_comprobante']) && $_POST['rechazar_comprobante'] == 2){
  $estatus_comprobante = "RECHAZADO"; //se rechaza el comprobante
  $estatus_membresia = "RECHAZADO"; //se rechaza la membresia
  //actualizamos comprobante_pago
  $updateSQL = sprintf("UPDATE comprobante_pago SET estatus_comprobante = %s, observaciones = %s WHERE idcomprobante_pago = %s",
    GetSQLValueString($estatus_comprobante, "text"),
    GetSQLValueString($_POST['observaciones_comprobante'], "text"),
    GetSQLValueString($_POST['idcomprobante_pago'], "int"));
  $actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());
  //actualizamos la membresia
  $updateSQL = sprintf("UPDATE membresia SET estatus_membresia = %s WHERE idmembresia = %s",
    GetSQLValueString($estatus_membresia, "text"),
    GetSQLValueString($_POST['idmembresia'], "int"));
  $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

  $mensaje = "Se ha rechaza la membresia y la Empresa ha sido notificado";
}

//SE APRUEBA EL CONTRATO DE USO
if(isset($_POST['aprobar_contrato']) && $_POST['aprobar_contrato'] == 1){
  $estatus_dspp = 19; //CONTRATO DE USO APROBADO
  $estatus_contrato = "ACEPTADO";;
  //actualizamos el contrato de uso

  $updateSQL = sprintf("UPDATE contratos SET estatus_contrato = %s WHERE idcontrato = %s",
    GetSQLValueString($estatus_contrato, "text"),
    GetSQLValueString($_POST['idcontrato'], "int"));
  $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

  $updateSQL = sprintf("UPDATE solicitud_registro SET estatus_dspp = %s WHERE idsolicitud_registro = %s",
    GetSQLValueString($estatus_dspp, "int"),
    GetSQLValueString($_POST['idsolicitud_registro'], "int"));
  $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

  //creamos el proceso_certificacion
  $insertSQL = sprintf("INSERT INTO proceso_certificacion(idsolicitud_registro, estatus_dspp, fecha_registro) VALUES(%s, %s, %s)",
    GetSQLValueString($_POST['idsolicitud_registro'], "int"),
    GetSQLValueString($estatus_dspp, "int"),
    GetSQLValueString($fecha, "int"));
  $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());


  if(!empty($_POST['idmembresia'])){

    $row_membresia = mysql_query("SELECT solicitud_registro.idempresa, solicitud_registro.idoc, empresa.nombre, oc.email1, oc.email2, membresia.idsolicitud_registro, membresia.estatus_membresia FROM solicitud_registro INNER JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa INNER JOIN oc ON solicitud_registro.idoc = oc.idoc INNER JOIN membresia ON solicitud_registro.idsolicitud_registro = membresia.idsolicitud_registro WHERE membresia.idmembresia = $_POST[idmembresia]", $dspp) or die(mysql_error());
    $membresia = mysql_fetch_assoc($row_membresia);

    if ($membresia['estatus_membresia'] == 'APROBADA') {

      /*07_04_2017 $asunto = "D-SPP | Formatos de Evaluación";

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
               <th scope="col" align="left" width="280"><p>OPP: <span style="color:red">'.$membresia['nombre'].'</span></p></th>
              </tr>

              <tr>
                <td colspan="2">
                 <p>SPP GLOBLA notifica que la OPP: '.$membresia['nombre'].' ha cumplido con la documentación necesaria.</p>
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
                <td colspan="2">
                  ¿Pasos para cargar la documentación?. Para poder cargar la documentación debe seguir los siguiente pasos:
                  <ol>
                    <li>Dar clic en la opción "SOLICITUDES"</li>
                    <li>Seleccionar "Solicitudes OPP"</li>
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
                  <p>Para cualquier duda o aclaración por favor escribir a: <span style="color:red">cert@spp.coop</span> o <span style="color:red">soporte@d-spp.org</span></p>
                </td>
              </tr>
            </tbody>
          </table>
        </body>
        </html>
      '; 07_04_2017*/

      $asunto = "D-SPP | Notificación de Certificado";

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
                <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">Notificación de Certificado</span></p></th>

              </tr>
              <tr>
               <th scope="col" align="left" width="280"><p>Empresa: <span style="color:red">'.$membresia['nombre'].'</span></p></th>
              </tr>

              <tr>
                <td colspan="2">
                  <p>SPP GLOBLA notifica que la Empresa: '.$membresia['nombre'].' ha cumplido con la documentación necesaria.</p>
                 <p><span style="color:red">Se encuentra autorizado para poder cargar el Certificado dentro del sistema D-SPP</span> (<a href="http://d-spp.org/">www.d-spp.org</a>)</p>
                 <p>
                   Pasos que debe seguir para cargar el certificado:
                   <ol>
                     <li>Ingrese en su cuenta de OC.</li>
                     <li>Seleccione la pestaña "Solicitudes" y de clic en la opción "Solicitudes Empresas".</li>
                     <li>Localice la solicitud de la Organización '.$membresia['nombre'].'</li>
                     <li>Debe posicionarse en la columna "Certificado" y dar clic en la opción "Cargar Certificado".</li>
                   </ol>
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
      if(isset($membresia['email1'])){
        $token = strtok($membresia['email1'], "\/\,\;");
        while ($token !== false)
        {
          $mail->AddAddress($token);
          $token = strtok('\/\,\;');
        }
      }
      if(isset($membresia['email2'])){
        $token = strtok($membresia['email2'], "\/\,\;");
        while ($token !== false)
        {
          $mail->AddAddress($token);
          $token = strtok('\/\,\;');
        }
      }

      $mail->Subject = utf8_decode($asunto);
      $mail->Body = utf8_decode($cuerpo_mensaje);
      $mail->MsgHTML(utf8_decode($cuerpo_mensaje));
      //$mail->Send();
      //$mail->ClearAddresses();
      if($mail->Send()){
        $mail->ClearAddresses();
        //22_03_2017echo "<script>alert('Se ha aprobado el \"Contrato de Uso\" y la \"Membresía SPP\", se le ha notificado al OC para que cargue Formato, Dictamen e Informe de Evaluación');location.href ='javascript:history.back()';</script>";
      }else{
        $mail->ClearAddresses();
        echo "<script>alert('Error, no se pudo enviar el correo, por favor contacte al administrador: soporte@d-spp.org');location.href ='javascript:history.back()';</script>";
      }
        //$mensaje = "Se ha aprobado el \"Contrato de Uso\" y la \"Membresía SPP\", se le ha notificado al OC para que cargue Formato, Dictamen e Informe de Evaluación";

   }else{
      $mensaje = "Se ha aprobado el \"Contrato de Uso\"";
    } 
  }

  $mensaje = "Se ha aprobado el \"Contrato de Uso\"";

}

//SE RECHAZA EL CONTRATO DE USO
if(isset($_POST['rechazar_contrato']) && $_POST['rechazar_contrato'] == 2){
  $estatus_dspp = 19; //CONTRATO DE USO APROBADO
  $estatus_contrato = "RECHAZADO";;
  //actualizamos el contrato de uso
  $updateSQL = sprintf("UPDATE solicitud_registro SET estatus_dspp = %s WHERE idsolicitud_registro = %s",
    GetSQLValueString($estatus_dspp, "int"),
    GetSQLValueString($_POST['idsolicitud_registro'], "int"));
  $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());
  
  $updateSQL = sprintf("UPDATE contratos SET estatus_contrato = %s, observaciones = %s WHERE idcontrato = %s",
    GetSQLValueString($estatus_contrato, "text"),
    GetSQLValueString($_POST['observaciones_contrato'], "text"),
    GetSQLValueString($_POST['idcontrato'], "int"));
  $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

  $mensaje = "Se ha rechazado el \"Contrato de Uso\"";
}
//SE APRUEBA O RECHAZA EL INFORME Y DICTAMEN DE EVALUACION
if(isset($_POST['documentos_evaluacion']) && $_POST['documentos_evaluacion'] == 1){
  //actualizamos el formato de evaluación
  $updateSQL = sprintf("UPDATE formato_evaluacion SET estatus_formato = %s WHERE idformato_evaluacion = %s",
    GetSQLValueString($_POST['estatus_formato'], "text"),
    GetSQLValueString($_POST['idformato_evaluacion'], "int"));
  $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

  //actualizamos el informe de evaluacion
  $updateSQL = sprintf("UPDATE informe_evaluacion SET estatus_informe = %s WHERE idinforme_evaluacion = %s",
    GetSQLValueString($_POST['estatus_informe'], "text"),
    GetSQLValueString($_POST['idinforme_evaluacion'], "int"));
  $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

  //actualizamos el dictamen de evaluacion
  $updateSQL = sprintf("UPDATE dictamen_evaluacion SET estatus_dictamen = %s WHERE iddictamen_evaluacion = %s",
    GetSQLValueString($_POST['estatus_dictamen'], "text"),
    GetSQLValueString($_POST['iddictamen_evaluacion'], "int"));
  $actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());

  //si toda la documentacion es aceptada se envia el correo al OC
  if(($_POST['estatus_formato'] == 'ACEPTADO') && ($_POST['estatus_informe'] == 'ACEPTADO') && ($_POST['estatus_dictamen'] == 'ACEPTADO')){
    $row_informacion = mysql_query("SELECT solicitud_registro.idoc, solicitud_registro.idempresa, empresa.nombre AS 'nombre_empresa', oc.email1, oc.email2 FROM solicitud_registro INNER JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa INNER JOIN oc ON solicitud_registro.idoc = oc.idoc WHERE solicitud_registro.idsolicitud_registro = $_POST[idsolicitud_registro]", $dspp) or die(mysql_error());
    $informacion = mysql_fetch_assoc($row_informacion);

    $asunto = "D-SPP | Notificación Certificado";

    /*$cuerpo_mensaje = '
      <html>
      <head>
        <meta charset="utf-8">
      </head>
      <body>
        <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
          <tbody>
            <tr>
              <th rowspan="2" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
              <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">Notificación Certificado</span></p></th>

            </tr>
            <tr>
             <th scope="col" align="left" width="280"><p>Empresa: <span style="color:red">'.$informacion['nombre_empresa'].'</span></p></th>
            </tr>

            <tr>
              <td colspan="2">
               <p>
                Se ha revisado y aprobado la siguiente documentación: 
                   <ul style="color:red">
                     <li>Formato de Evaluación</li>
                     <li>Informe de Evaluación</li>
                     <li>Dictamen de Evaluación</li>
                   </ul>

               </p>
               <p><span style="color:red">Se encuentra autorizado para poder cargar el Certificado dentro del sistema D-SPP</span> (<a href="http://d-spp.org/">www.d-spp.org</a>)</p>
               <p>
                 Pasos que debe seguir para cargar el certificado:
                 <ol>
                   <li>Ingrese en su cuenta de OC.</li>
                   <li>Seleccione la pestaña "Solicitudes" y de clic en la opción "Solicitudes Empresas".</li>
                   <li>Localice la solicitud de la Organización '.$informacion['nombre_empresa'].'</li>
                   <li>Debe posicionarse en la columna "Certificado" y dar clic en la opción "Cargar Certificado".</li>
                 </ol>
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
    ';*/

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
              <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">Notificación Certificado</span></p></th>

            </tr>
            <tr>
             <th scope="col" align="left" width="280"><p>Empresa: <span style="color:red">'.$informacion['nombre_empresa'].'</span></p></th>
            </tr>

            <tr>
              <td colspan="2">
               <p>
                Se ha revisado y aprobado la siguiente documentación: 
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
                <p>Para cualquier duda o aclaración por favor escribir a: <span style="color:red">cert@spp.coop</span> o <span style="color:red">soporte@d-spp.org</span></p>
              </td>
            </tr>

          </tbody>
        </table>
      </body>
      </html>
    ';
      if(isset($informacion['email1'])){
        $token = strtok($informacion['email1'], "\/\,\;");
        while ($token !== false)
        {
          $mail->AddAddress($token);
          $token = strtok('\/\,\;');
        }
      }
      if(isset($informacion['email2'])){
        $token = strtok($informacion['email2'], "\/\,\;");
        while ($token !== false)
        {
          $mail->AddAddress($token);
          $token = strtok('\/\,\;');
        }
      }

      $mail->AddCC($spp_global);
      $mail->Subject = utf8_decode($asunto);
      $mail->Body = utf8_decode($cuerpo_mensaje);
      $mail->MsgHTML(utf8_decode($cuerpo_mensaje));
      $mail->Send();
      $mail->ClearAddresses();

  }

  $mensaje = "Se ha notificado al OC";
}



///////// INICIAN CAMPOS DE BUSQUEDA

if(isset($_POST['filtrar']) && $_POST['filtrar'] == 1){
  $pais = $_POST['filtrar_pais'];
  $oc = $_POST['filtrar_oc'];
  $tipo = $_POST['filtrar_tipo'];

  if(!empty($pais) && !empty($oc) && !empty($tipo)){
    $query = "SELECT solicitud_registro.idsolicitud_registro AS 'idsolicitud', solicitud_registro.tipo_solicitud, solicitud_registro.cotizacion_empresa, solicitud_registro.idoc AS 'id_oc',solicitud_registro.fecha_registro, solicitud_registro.fecha_aceptacion, oc.abreviacion AS 'abreviacionOC', empresa.idempresa, empresa.abreviacion AS 'abreviacion_empresa', empresa.pais, periodo_objecion.idperiodo_objecion, membresia.idmembresia, comprobante_pago.idcomprobante_pago, certificado.idcertificado, contratos.idcontrato, formato_evaluacion.idformato_evaluacion, informe_evaluacion.idinforme_evaluacion, dictamen_evaluacion.iddictamen_evaluacion FROM solicitud_registro INNER JOIN oc ON solicitud_registro.idoc = oc.idoc INNER JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa LEFT JOIN periodo_objecion ON solicitud_registro.idsolicitud_registro  = periodo_objecion.idsolicitud_registro LEFT JOIN membresia ON solicitud_registro.idsolicitud_registro = membresia.idsolicitud_registro LEFT JOIN comprobante_pago ON membresia.idcomprobante_pago = comprobante_pago.idcomprobante_pago LEFT JOIN certificado ON solicitud_registro.idsolicitud_registro = certificado.idsolicitud_registro LEFT JOIN contratos ON solicitud_registro.idsolicitud_registro = contratos.idsolicitud_registro LEFT JOIN formato_evaluacion ON solicitud_registro.idsolicitud_registro = formato_evaluacion.idsolicitud_registro LEFT JOIN informe_evaluacion ON solicitud_registro.idsolicitud_registro = informe_evaluacion.idsolicitud_registro LEFT JOIN dictamen_evaluacion ON solicitud_registro.idsolicitud_registro = dictamen_evaluacion.idsolicitud_registro WHERE solicitud_registro.tipo_solicitud = '$tipo' AND empresa.pais = '$pais' AND solicitud_registro.idoc = $oc GROUP BY solicitud_registro.idsolicitud_registro ORDER BY solicitud_registro.fecha_registro DESC";
  }else if(!empty($pais) && empty($oc) && empty($tipo)){
    $query = "SELECT solicitud_registro.idsolicitud_registro AS 'idsolicitud', solicitud_registro.tipo_solicitud, solicitud_registro.cotizacion_empresa, solicitud_registro.idoc AS 'id_oc',solicitud_registro.fecha_registro, solicitud_registro.fecha_aceptacion, oc.abreviacion AS 'abreviacionOC', empresa.idempresa, empresa.abreviacion AS 'abreviacion_empresa', empresa.pais, periodo_objecion.idperiodo_objecion, membresia.idmembresia, comprobante_pago.idcomprobante_pago, certificado.idcertificado, contratos.idcontrato, formato_evaluacion.idformato_evaluacion, informe_evaluacion.idinforme_evaluacion, dictamen_evaluacion.iddictamen_evaluacion FROM solicitud_registro INNER JOIN oc ON solicitud_registro.idoc = oc.idoc INNER JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa LEFT JOIN periodo_objecion ON solicitud_registro.idsolicitud_registro  = periodo_objecion.idsolicitud_registro LEFT JOIN membresia ON solicitud_registro.idsolicitud_registro = membresia.idsolicitud_registro LEFT JOIN comprobante_pago ON membresia.idcomprobante_pago = comprobante_pago.idcomprobante_pago LEFT JOIN certificado ON solicitud_registro.idsolicitud_registro = certificado.idsolicitud_registro LEFT JOIN contratos ON solicitud_registro.idsolicitud_registro = contratos.idsolicitud_registro LEFT JOIN formato_evaluacion ON solicitud_registro.idsolicitud_registro = formato_evaluacion.idsolicitud_registro LEFT JOIN informe_evaluacion ON solicitud_registro.idsolicitud_registro = informe_evaluacion.idsolicitud_registro LEFT JOIN dictamen_evaluacion ON solicitud_registro.idsolicitud_registro = dictamen_evaluacion.idsolicitud_registro WHERE empresa.pais = '$pais' GROUP BY solicitud_registro.idsolicitud_registro ORDER BY solicitud_registro.fecha_registro DESC";
  }else if(empty($pais) && !empty($oc) && !empty($tipo)){
    $query = "SELECT solicitud_registro.idsolicitud_registro AS 'idsolicitud', solicitud_registro.tipo_solicitud, solicitud_registro.cotizacion_empresa, solicitud_registro.idoc AS 'id_oc',solicitud_registro.fecha_registro, solicitud_registro.fecha_aceptacion, oc.abreviacion AS 'abreviacionOC', empresa.idempresa, empresa.abreviacion AS 'abreviacion_empresa', empresa.pais, periodo_objecion.idperiodo_objecion, membresia.idmembresia, comprobante_pago.idcomprobante_pago, certificado.idcertificado, contratos.idcontrato, formato_evaluacion.idformato_evaluacion, informe_evaluacion.idinforme_evaluacion, dictamen_evaluacion.iddictamen_evaluacion FROM solicitud_registro INNER JOIN oc ON solicitud_registro.idoc = oc.idoc INNER JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa LEFT JOIN periodo_objecion ON solicitud_registro.idsolicitud_registro  = periodo_objecion.idsolicitud_registro LEFT JOIN membresia ON solicitud_registro.idsolicitud_registro = membresia.idsolicitud_registro LEFT JOIN comprobante_pago ON membresia.idcomprobante_pago = comprobante_pago.idcomprobante_pago LEFT JOIN certificado ON solicitud_registro.idsolicitud_registro = certificado.idsolicitud_registro LEFT JOIN contratos ON solicitud_registro.idsolicitud_registro = contratos.idsolicitud_registro LEFT JOIN formato_evaluacion ON solicitud_registro.idsolicitud_registro = formato_evaluacion.idsolicitud_registro LEFT JOIN informe_evaluacion ON solicitud_registro.idsolicitud_registro = informe_evaluacion.idsolicitud_registro LEFT JOIN dictamen_evaluacion ON solicitud_registro.idsolicitud_registro = dictamen_evaluacion.idsolicitud_registro WHERE solicitud_registro.idoc = $oc AND solicitud_registro.tipo_solicitud = '$tipo' GROUP BY solicitud_registro.idsolicitud_registro ORDER BY solicitud_registro.fecha_registro DESC";
  }else if(empty($pais) && empty($oc) && !empty($tipo)){
    $query = "SELECT solicitud_registro.idsolicitud_registro AS 'idsolicitud', solicitud_registro.tipo_solicitud, solicitud_registro.cotizacion_empresa, solicitud_registro.idoc AS 'id_oc',solicitud_registro.fecha_registro, solicitud_registro.fecha_aceptacion, oc.abreviacion AS 'abreviacionOC', empresa.idempresa, empresa.abreviacion AS 'abreviacion_empresa', empresa.pais, periodo_objecion.idperiodo_objecion, membresia.idmembresia, comprobante_pago.idcomprobante_pago, certificado.idcertificado, contratos.idcontrato, formato_evaluacion.idformato_evaluacion, informe_evaluacion.idinforme_evaluacion, dictamen_evaluacion.iddictamen_evaluacion FROM solicitud_registro INNER JOIN oc ON solicitud_registro.idoc = oc.idoc INNER JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa LEFT JOIN periodo_objecion ON solicitud_registro.idsolicitud_registro  = periodo_objecion.idsolicitud_registro LEFT JOIN membresia ON solicitud_registro.idsolicitud_registro = membresia.idsolicitud_registro LEFT JOIN comprobante_pago ON membresia.idcomprobante_pago = comprobante_pago.idcomprobante_pago LEFT JOIN certificado ON solicitud_registro.idsolicitud_registro = certificado.idsolicitud_registro LEFT JOIN contratos ON solicitud_registro.idsolicitud_registro = contratos.idsolicitud_registro LEFT JOIN formato_evaluacion ON solicitud_registro.idsolicitud_registro = formato_evaluacion.idsolicitud_registro LEFT JOIN informe_evaluacion ON solicitud_registro.idsolicitud_registro = informe_evaluacion.idsolicitud_registro LEFT JOIN dictamen_evaluacion ON solicitud_registro.idsolicitud_registro = dictamen_evaluacion.idsolicitud_registro WHERE solicitud_registro.tipo_solicitud = '$tipo' GROUP BY solicitud_registro.idsolicitud_registro ORDER BY solicitud_registro.fecha_registro DESC";
  }else{
    $query = "SELECT solicitud_registro.idsolicitud_registro AS 'idsolicitud', solicitud_registro.tipo_solicitud, solicitud_registro.cotizacion_empresa, solicitud_registro.idoc AS 'id_oc',solicitud_registro.fecha_registro, solicitud_registro.fecha_aceptacion, oc.abreviacion AS 'abreviacionOC', empresa.idempresa, empresa.abreviacion AS 'abreviacion_empresa', empresa.pais, periodo_objecion.idperiodo_objecion, membresia.idmembresia, comprobante_pago.idcomprobante_pago, certificado.idcertificado, contratos.idcontrato, formato_evaluacion.idformato_evaluacion, informe_evaluacion.idinforme_evaluacion, dictamen_evaluacion.iddictamen_evaluacion FROM solicitud_registro INNER JOIN oc ON solicitud_registro.idoc = oc.idoc INNER JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa LEFT JOIN periodo_objecion ON solicitud_registro.idsolicitud_registro  = periodo_objecion.idsolicitud_registro LEFT JOIN membresia ON solicitud_registro.idsolicitud_registro = membresia.idsolicitud_registro LEFT JOIN comprobante_pago ON membresia.idcomprobante_pago = comprobante_pago.idcomprobante_pago LEFT JOIN certificado ON solicitud_registro.idsolicitud_registro = certificado.idsolicitud_registro LEFT JOIN contratos ON solicitud_registro.idsolicitud_registro = contratos.idsolicitud_registro LEFT JOIN formato_evaluacion ON solicitud_registro.idsolicitud_registro = formato_evaluacion.idsolicitud_registro LEFT JOIN informe_evaluacion ON solicitud_registro.idsolicitud_registro = informe_evaluacion.idsolicitud_registro LEFT JOIN dictamen_evaluacion ON solicitud_registro.idsolicitud_registro = dictamen_evaluacion.idsolicitud_registro GROUP BY solicitud_registro.idsolicitud_registro ORDER BY solicitud_registro.fecha_registro DESC";
  }

}else if(isset($_POST['buscar']) && $_POST['buscar'] == 1){
  $buscar = $_POST['campo_busqueda'];
  $query = "SELECT solicitud_registro.idsolicitud_registro AS 'idsolicitud', solicitud_registro.tipo_solicitud, solicitud_registro.cotizacion_empresa, solicitud_registro.idoc AS 'id_oc',solicitud_registro.fecha_registro, solicitud_registro.fecha_aceptacion, oc.abreviacion AS 'abreviacionOC', empresa.idempresa, empresa.abreviacion AS 'abreviacion_empresa', empresa.pais, periodo_objecion.idperiodo_objecion, membresia.idmembresia, comprobante_pago.idcomprobante_pago, certificado.idcertificado, contratos.idcontrato, formato_evaluacion.idformato_evaluacion, informe_evaluacion.idinforme_evaluacion, dictamen_evaluacion.iddictamen_evaluacion FROM solicitud_registro INNER JOIN oc ON solicitud_registro.idoc = oc.idoc INNER JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa LEFT JOIN periodo_objecion ON solicitud_registro.idsolicitud_registro  = periodo_objecion.idsolicitud_registro LEFT JOIN membresia ON solicitud_registro.idsolicitud_registro = membresia.idsolicitud_registro LEFT JOIN comprobante_pago ON membresia.idcomprobante_pago = comprobante_pago.idcomprobante_pago LEFT JOIN certificado ON solicitud_registro.idsolicitud_registro = certificado.idsolicitud_registro LEFT JOIN contratos ON solicitud_registro.idsolicitud_registro = contratos.idsolicitud_registro LEFT JOIN formato_evaluacion ON solicitud_registro.idsolicitud_registro = formato_evaluacion.idsolicitud_registro LEFT JOIN informe_evaluacion ON solicitud_registro.idsolicitud_registro = informe_evaluacion.idsolicitud_registro LEFT JOIN dictamen_evaluacion ON solicitud_registro.idsolicitud_registro = dictamen_evaluacion.idsolicitud_registro WHERE empresa.spp LIKE '%$buscar%' OR empresa.nombre LIKE '%$buscar' OR empresa.abreviacion LIKE '%$buscar%' OR empresa.pais LIKE '%$buscar%' OR empresa.email LIKE '%$buscar%' OR oc.abreviacion LIKE '%$buscar%' GROUP BY solicitud_registro.idsolicitud_registro ORDER BY solicitud_registro.fecha_registro DESC";
}else{
  $query = "SELECT solicitud_registro.idsolicitud_registro AS 'idsolicitud', solicitud_registro.tipo_solicitud, solicitud_registro.cotizacion_empresa, solicitud_registro.idoc AS 'id_oc',solicitud_registro.fecha_registro, solicitud_registro.fecha_aceptacion, oc.abreviacion AS 'abreviacionOC', empresa.idempresa, empresa.abreviacion AS 'abreviacion_empresa', empresa.pais, periodo_objecion.idperiodo_objecion, membresia.idmembresia, comprobante_pago.idcomprobante_pago, certificado.idcertificado, contratos.idcontrato, formato_evaluacion.idformato_evaluacion, informe_evaluacion.idinforme_evaluacion, dictamen_evaluacion.iddictamen_evaluacion FROM solicitud_registro INNER JOIN oc ON solicitud_registro.idoc = oc.idoc INNER JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa LEFT JOIN periodo_objecion ON solicitud_registro.idsolicitud_registro  = periodo_objecion.idsolicitud_registro LEFT JOIN membresia ON solicitud_registro.idsolicitud_registro = membresia.idsolicitud_registro LEFT JOIN comprobante_pago ON membresia.idcomprobante_pago = comprobante_pago.idcomprobante_pago LEFT JOIN certificado ON solicitud_registro.idsolicitud_registro = certificado.idsolicitud_registro LEFT JOIN contratos ON solicitud_registro.idsolicitud_registro = contratos.idsolicitud_registro LEFT JOIN formato_evaluacion ON solicitud_registro.idsolicitud_registro = formato_evaluacion.idsolicitud_registro LEFT JOIN informe_evaluacion ON solicitud_registro.idsolicitud_registro = informe_evaluacion.idsolicitud_registro LEFT JOIN dictamen_evaluacion ON solicitud_registro.idsolicitud_registro = dictamen_evaluacion.idsolicitud_registro GROUP BY solicitud_registro.idsolicitud_registro ORDER BY solicitud_registro.fecha_registro DESC";
}
//////// TERMINAN CAMPOS DE BUSQUEDA


$row_solicitud = mysql_query($query, $dspp) or die(mysql_error());
$total_solicitudes = mysql_num_rows($row_solicitud);


  /* INICIA PAGINACION */
    //limitamos la consulta
    $regXPag = 20;
    $pagina = false; //cuando se ingresa al menu no tiene ningun valor

    //Examinar la pagina a mostrar y el inicio del registro a mostrar
    if(isset($_GET['p'])){
      $pagina = $_GET['p'];
    }
    if(!$pagina){ //si la pagina es falsa
      $inicio = 0;
      $pagina = 1;
    }else{
      $inicio = ($pagina - 1) * $regXPag;
    }
    //calculamos el total de páginas
    $total_paginas = ceil($total_solicitudes / $regXPag);

  $query .= " LIMIT ".$inicio.",".$regXPag;

  $paginacion = "<p style='margin-bottom:-20px;'>";
    $paginacion .= "Número de resultados: <b>$total_solicitudes</b>. ";
    $paginacion .= "Mostrando <b>$regXPag</b> resultados por página. ";
    $paginacion .= "Página <b>$pagina</b> de <b>$total_paginas</b>. ";
  $paginacion .= "</p>";

  if($total_paginas > 1){
    $paginacion .= '<nav aria-label="Page navigation">';
      $paginacion .= '<ul class="pagination">';
        $paginacion .= ($pagina != 1)?'<li><a href="?SOLICITUD&select_empresa&p='.($pagina-1).'" aria-label="Previous"> <span aria-hidden="true">&laquo;</span></a></li>':'';

      for ($i=1; $i <= $total_paginas; $i++) {
        //si muestro el indice de la pagina actual, no coloco enlace
        $actual = "<li class='active'><a href='#'>".$pagina."</a></li>";
        //si el indice no corresponde con la pagina mostrada actualmente, coloco el enlace para ir a esa pagina
        $enlace = '<li><a href="?SOLICITUD&select_empresa&p='.$i.'">'.$i.'</a></li>';

        $paginacion .= ($pagina == $i)?$actual:$enlace;
      }
      $paginacion .= ($pagina!=$total_paginas)?"<li><a href='?SOLICITUD&select_empresa&p=".($pagina+1)."' aria-label='Next'><span aria-hidden='true'>&raquo;</span></a></li>":"";
      $paginacion .= "</ul>";
    $paginacion .= "</nav>";
  }
  $row_solicitud = mysql_query($query,$dspp) or die(mysql_error());

  /* TERMINA PAGINACIÓN*/

  if(isset($_POST['anclar']) && $_POST['anclar'] == 1){
  $idsolicitud_registro = $_POST['idsolicitud_registro'];

  $query_solicitud = "SELECT idsolicitud_registro, idempresa, contacto1_nombre, contacto1_cargo, contacto1_email, contacto1_telefono, contacto2_nombre, contacto2_cargo, contacto2_email, contacto2_telefono, adm1_nombre, adm1_email, adm1_telefono, adm2_nombre, adm2_email, adm2_telefono  FROM solicitud_registro";
  $row_solicitud_cert = mysql_query($query_solicitud, $dspp) or die(mysql_error());


  while($solicitud = mysql_fetch_assoc($row_solicitud_cert)){
    if(isset($solicitud['contacto1_nombre'])){
      $insertSQL = sprintf("INSERT INTO contactos (idempresa, nombre, cargo, telefono1, email1, idsolicitud_registro) VALUES (%s, %s, %s, %s, %s, %s)",
        GetSQLValueString($solicitud['idempresa'], 'text'),
        GetSQLValueString($solicitud['contacto1_nombre'], 'text'),
        GetSQLValueString($solicitud['contacto1_cargo'], 'text'),
        GetSQLValueString($solicitud['contacto1_telefono'], 'text'),
        GetSQLValueString($solicitud['contacto1_email'], 'text'),
        GetSQLValueString($solicitud['idsolicitud_registro'], 'int'));
      $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
    }

    if(isset($solicitud['contacto2_nombre'])){
      $insertSQL = sprintf("INSERT INTO contactos (idempresa, nombre, cargo, telefono1, email1, idsolicitud_registro) VALUES (%s, %s, %s, %s, %s, %s)",
        GetSQLValueString($solicitud['idempresa'], 'text'),
        GetSQLValueString($solicitud['contacto2_nombre'], 'text'),
        GetSQLValueString($solicitud['contacto2_cargo'], 'text'),
        GetSQLValueString($solicitud['contacto2_telefono'], 'text'),
        GetSQLValueString($solicitud['contacto2_email'], 'text'),
        GetSQLValueString($solicitud['idsolicitud_registro'], 'int'));
      $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
    }

    if(isset($solicitud['adm1_nombre'])){
      $insertSQL = sprintf("INSERT INTO contactos (idempresa, nombre, cargo, telefono1, email1, idsolicitud_registro) VALUES (%s, %s, %s, %s, %s, %s)",
        GetSQLValueString($solicitud['idempresa'], 'text'),
        GetSQLValueString($solicitud['adm1_nombre'], 'text'),
        GetSQLValueString('Administrativo', 'text'),
        GetSQLValueString($solicitud['adm1_telefono'], 'text'),
        GetSQLValueString($solicitud['adm1_email'], 'text'),
        GetSQLValueString($solicitud['idsolicitud_registro'], 'int'));
      $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
    }

    if(isset($solicitud['adm2_nombre'])){
      $insertSQL = sprintf("INSERT INTO contactos (idempresa, nombre, cargo, telefono1, email1, idsolicitud_registro) VALUES (%s, %s, %s, %s, %s, %s)",
        GetSQLValueString($solicitud['idempresa'], 'text'),
        GetSQLValueString($solicitud['adm2_nombre'], 'text'),
        GetSQLValueString('Administrativo', 'text'),
        GetSQLValueString($solicitud['adm2_telefono'], 'text'),
        GetSQLValueString($solicitud['adm2_email'], 'text'),
        GetSQLValueString($solicitud['idsolicitud_registro'], 'int'));
      $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
    }
  }
  

  
}
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
    <table class="table table-bordered table-condensed" style="font-size:12px">
      <thead>
        <tr>
          <td colspan="6">
            <form action="" method="POST" id="frm_filtrar">
              <select name="filtrar_oc" id="">
                <option value="">Buscar por OC</option>
                <?php 
                $row_oc = mysql_query("SELECT oc.idoc, oc.abreviacion FROM oc", $dspp) or die(mysql_error());
                while($oc = mysql_fetch_assoc($row_oc)){
                  echo "<option value='$oc[idoc]'>$oc[abreviacion]</option>";
                }
                 ?>
              </select>
              <select name="filtrar_pais" id="">
                <option value="">Buscar por país</option>
                <?php 
                $row_pais = mysql_query("SELECT empresa.pais FROM empresa GROUP BY empresa.pais", $dspp) or die(mysql_error());
                while($pais = mysql_fetch_assoc($row_pais)){
                  echo "<option value='$pais[pais]'>$pais[pais]</option>";
                }
                 ?>
              </select>
              <select name="filtrar_tipo" id="">
                <option value="">Tipo de solicitud</option>
                <option value="NUEVA">Nueva</option>
                <option value="RENOVACION">Renovación</option>
              </select>
              <button class="btn btn-info" name="filtrar" value="1" type="submit">Filtrar</button>
            </form>
          </td>
          <td colspan="5">
            <form action="" method="POST" id="frm_buscar">
              <div class="col-lg-6">
                <div class="input-group">
                  <input type="text" class="form-control" name="campo_busqueda" placeholder="campo de busqueda">
                  <span class="input-group-btn">
                    <button class="btn btn-info" name="buscar" value="1" type="submit">Buscar</button>
                  </span>
                </div><!-- /input-group -->
              </div><!-- /.col-lg-6 -->
            </form>
          </td>
          <td colspan="2">
            <h4>Total: <span style="color:red"><?php echo $total_solicitudes; ?></span></h4>
          </td>
        </tr>
        <tr class="info">
          <th class="text-center">ID</th>
          <th class="text-center"><a href="#" data-toggle="tooltip" title="Tipo de Solicitud"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>Tipo</a></th>
          <th class="text-center">Fecha Solicitud</th>
          <th class="text-center">OC</th>
          <th class="text-center">Organización</th>
          <th class="text-center">Pais</th>
          <th class="text-center">Estatus Solicitud</th>
          <th class="text-center">Cotización</th>
          <th class="text-center">Proceso de Objeción</th>
          <th class="text-center">Proceso Certificación</th>
          <th class="text-center">Membresia</th>
          <th class="text-center">Certificado</th>
          <!--<th class="text-center">Observaciones Solicitud</th>-->
          <th class="text-center">Acciones</th>
        </tr>
      </thead>
      <tbody>

          <?php 
          while($solicitud = mysql_fetch_assoc($row_solicitud)){

            $query_proceso = "SELECT proceso_certificacion.estatus_publico, estatus_publico.nombre AS 'nombre_publico', proceso_certificacion.estatus_interno, estatus_interno.nombre AS 'nombre_interno', proceso_certificacion.estatus_dspp, estatus_dspp.nombre AS 'nombre_dspp' FROM proceso_certificacion LEFT JOIN estatus_publico ON proceso_certificacion.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON proceso_certificacion.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON proceso_certificacion.estatus_dspp = estatus_dspp.idestatus_dspp WHERE proceso_certificacion.idsolicitud_registro = $solicitud[idsolicitud] ORDER BY proceso_certificacion.idproceso_certificacion DESC LIMIT 1";
            $ejecutar = mysql_query($query_proceso,$dspp) or die(mysql_error());
            $proceso_certificacion = mysql_fetch_assoc($ejecutar);

            //CONSULTAMOS LA INFORMACION DEL OC
            $row_oc = mysql_query("SELECT idoc, abreviacion FROM oc WHERE idoc = $solicitud[id_oc]",$dspp) or die(mysql_error()); 
            $oc = mysql_fetch_assoc($row_oc);

            //CONSULTAMOS LA INFORMACION DEL PERIODO DE OBJECION
            if(isset($solicitud['idperiodo_objecion'])){
                $row_periodo_objecion = mysql_query("SELECT * FROM periodo_objecion WHERE idsolicitud_registro = ".$solicitud['idsolicitud']."", $dspp) or die(mysql_error());
                $periodo_objecion = mysql_fetch_assoc($row_periodo_objecion);
            }

          ?>
          <form action="" method="POST" enctype="multipart/form-data">

            <tr <?php if($proceso_certificacion['estatus_dspp'] == 12){ echo "class='success'"; }else if($proceso_certificacion['estatus_interno'] == 9){ echo "class='danger'"; } ?>>
              <!---- inicia ID ---->
              <td>
                <?php echo $solicitud['idsolicitud']; ?>
              </td>
              <!---- termina ID ---->

              <!---- inicia TIPO SOLICITUD ---->
              <td <?php if($solicitud['tipo_solicitud'] == 'NUEVA'){ echo "class='success'"; }else{ echo "class='warning'"; } ?>class="warning">
                <?php echo $solicitud['tipo_solicitud']; ?>
              </td>
              <!---- inicia TIPO SOLICITUD ---->

              <!---- inicia FECHA SOLICITUD ---->
              <td>
                <?php echo date('d/m/Y',$solicitud['fecha_registro']); ?>
                <a class="btn btn-xs btn-primary" href="?SOLICITUD&idsolicitud_empresa=<?php echo $solicitud['idsolicitud']; ?>">consultar</a>
              </td>
              <!---- termina FECHA SOLICITUD ---->

              <!---- inicia ABREVIACION OC ---->
              <td>
                <a href="?OC&detail&idoc=<?php echo $oc['idoc']; ?>"><?php echo $oc['abreviacion']; ?></a>
              </td>
              <!---- termina ABREVIACION OC ---->

              <!---- inicia ORGANIZACION ---->
              <td>
                <a href="?EMPRESAS&detail&idempresa=<?php echo $solicitud['idempresa']; ?>"><?php echo $solicitud['abreviacion_empresa']; ?></a>
              </td>
              <td>
                <b style="color:#c0392b"><?php echo $solicitud['pais']; ?></b>
              </td>
              <!---- termina ORGANIZACION ---->

              <!---- inicia ESTATUS SOLICITUD ---->
              <td>
                <?php echo $proceso_certificacion['nombre_dspp']; ?>
              </td>
              <!---- termina ESTATUS SOLICITUD ---->

              <!---- inicia COTIZACIÓN ---->
              <td>
                <?php
                if(isset($solicitud['cotizacion_empresa'])){
                   echo "<a class='btn btn-success form-control' style='font-size:12px;color:white;height:30px;' href='".$solicitud['cotizacion_empresa']."' target='_blank'><span class='glyphicon glyphicon-download' aria-hidden='true'></span> Descargar Cotización</a>";
                   if($proceso_certificacion['estatus_dspp'] == 5){ // se acepta la cotizacion
                    echo "<p class='alert alert-success' style='padding:5px;margin-bottom:5px;'>Estatus: ".$proceso_certificacion['nombre_dspp']."</p>"; 
                   }else if($proceso_certificacion['estatus_dspp'] == 17){ // se rechaza la cotización
                    echo "<p class='alert alert-danger' style='padding:5px;margin-bottom:5px;'>Estatus: ".$proceso_certificacion['nombre_dspp']."</p>"; 
                   }else{
                    echo "<p class='alert alert-info' style='padding:5px;margin-bottom:5px;'>Estatus: ".$proceso_certificacion['nombre_dspp']."</p>"; 
                   }

                }else{ // INICIA CARGAR COTIZACIÓN
                  echo "No Disponible";
                } // TERMINA CARGAR COTIZACIÓN
                 ?>
              </td>
              <!---- termina COTIZACIÓN ---->
              
              <!---- inicia PROCESO DE OBJECIÓN ---->
              <td>
                <?php 
                if($solicitud['tipo_solicitud'] == 'RENOVACION'){
                ?>
                  <a href="#" data-toggle="tooltip" title="Esta solicitud se encuentra en Proceso de Renovación del Registro por lo tanto no aplica el periodo de objeción" style="padding:7px;"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>SOLICITUD EN RENOVACIÓN</a>
                <?php
                }else{
                  if(isset($solicitud['idperiodo_objecion'])){

                  // //CHECAMOS SI LA HORA ACTUAL ES IGUAL o MAYOR A LA FECHA_FINAL DEL PERIODO DE OBJECION
                  if(isset($periodo_objecion['idperiodo_objecion']) && $periodo_objecion['estatus_objecion'] == 'ACTIVO'){
                    if($fecha > $periodo_objecion['fecha_fin']){
                      $estatus_dspp = 7; //TERMINA PERIODO DE OBJECIÓN
                      $estatus_objecion = 'FINALIZADO';


                      //INSERTARMOS PROCESO_CERTIFICACION
                      $insertSQL = sprintf("INSERT INTO proceso_certificacion (idsolicitud_registro, estatus_dspp, fecha_registro) VALUES(%s, %s, %s)",
                        GetSQLValueString($solicitud['idsolicitud'], "int"),
                        GetSQLValueString($estatus_dspp, "int"),
                        GetSQLValueString($fecha, "int"));
                      $insertar = mysql_query($insertSQL,$dspp) or die(mysql_error());
         
                      //ACTUALIZAMOS EL PERIODO_OBJECION
                      $updateSQL = sprintf("UPDATE periodo_objecion SET estatus_objecion = %s WHERE idperiodo_objecion = %s",
                        GetSQLValueString($estatus_objecion, "text"),
                        GetSQLValueString($periodo_objecion['idperiodo_objecion'], "int"));
                      $actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());
    
                    }
                  }

                  ?>
                    <button type="button" class="btn btn-sm btn-primary" style="width:100%" data-toggle="modal" data-target="<?php echo "#objecion".$periodo_objecion['idperiodo_objecion']; ?>">Proceso Objeción</button>
                  <?php
                  }else{
                    echo "<button class='btn btn-sm btn-default' style='width:100%' disabled>Consultar Proceso</button>";
                  }
                   ?>
                  <!-- INICIA MODAL PROCESO DE OBJECIÓN -->

                  <div id="<?php echo "objecion".$periodo_objecion['idperiodo_objecion']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                    <div class="modal-dialog modal-lg" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                          <h4 class="modal-title" id="myModalLabel">Proceso de Objeción</h4>
                        </div>
                        <div class="modal-body">
                          <div class="row">
                            <div class="col-md-6">
                              <h4>Periodo de Objeción <small>(<?php echo $periodo_objecion['estatus_objecion']; ?>)</small></h4>
                              <p class="alert alert-info" style="padding:7px;">Inicio: <?php echo date('d/m/Y',$periodo_objecion['fecha_inicio']); ?></p>
                              <p class="alert alert-danger" style="padding:7px;">Fin: <?php echo date('d/m/Y',$periodo_objecion['fecha_fin']); ?></p>
                              <?php 
                              if($periodo_objecion['estatus_objecion'] == 'EN ESPERA'){
                              ?>
                                <button type="submit" class="btn btn-success" style="width:100%" name="aprobar_periodo" value="1">
                                  <span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Aprobar Período
                                </button>
                                <input type="hidden" name="idperiodo_objecion2" value="<?php echo $periodo_objecion['idperiodo_objecion']; ?>">
                              <?php
                              }
                              ?>
                            </div>

                            <div class="col-md-6">
                              <?php 
                              if($periodo_objecion['estatus_objecion'] == 'FINALIZADO'){
                              ?>
                                <h4>Resolución de Objeción</h4>
                                <p class="alert alert-info" style="padding:7px;">
                                  <b style="margin-right:10px;">Dictamen:</b>
                                  <?php 
                                  if(empty($periodo_objecion['dictamen'])){
                                  ?>
                                    <label class="radio-inline">
                                      <input type="radio" name="dictamen" id="positivo" value="POSITIVO"> Positivo
                                    </label>
                                    <label class="radio-inline">
                                      <input type="radio" name="dictamen" id="negativo" value="NEGATIVO"> Negativo
                                    </label>
                                  <?php
                                  }else{
                                    echo "<span style='color:#c0392b'>".$periodo_objecion['dictamen']."</span>";
                                  }
                                   ?>
                                </p>
                                <label for="observacion">Observaciones</label>
                                <?php 
                                if(empty($periodo_objecion['observacion'])){
                                  echo '<textarea name="observacion" id="observacion" class="form-control"></textarea>';
                                }else{
                                  echo "<p style='color:#c0392b'>".$periodo_objecion['observacion']."</p>";
                                }

                                if(empty($periodo_objecion['documento'])){
                                ?>
                                  <label for="cargar_resolucion">Cargar Resolución</label>
                                  <input type="file" class="form-control" id="cargar_resolucion" name="cargar_resolucion" >
                                  <button type="submit" class="btn btn-success" style="width:100%" name="enviar_resolucion" value="1">Enviar Resolución</button>
                                <?php
                                }else{
                                  echo "<a href='".$periodo_objecion['documento']."' class='btn btn-info' style='width:100%' target='_blank'>Descargar Resolución</a>";
                                }
                                 ?>
                              <?php
                              }else{
                                echo "<p class='alert alert-warning'><strong>Una vez finalizado el Periodo de Objeción podra cargar la resolución del mismo</strong></p>";
                              }
                               ?>
                            </div>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                          <!--<button type="button" class="btn btn-primary">Guardar Cambios</button>-->
                        </div>
                      </div>
                    </div>
                  </div>
                 <!-- TERMINA MODAL PROCESO DE OBJECIÓN -->
                <?php
                }
                ?>
              </td>
              <!---- termina PROCESO DE OBJECIÓN ---->

              <!---- inicia PROCESO CERTIFICACION ---->
              <td>
                <?php 
                if($solicitud['tipo_solicitud'] == 'RENOVACION'){
                  if(!empty($solicitud['fecha_aceptacion'])){
                  ?>
                    <button type="button" class="btn btn-sm btn-primary" style="width:100%" data-toggle="modal" data-target="<?php echo "#certificacion".$solicitud['idsolicitud']; ?>">Proceso Certificación</button>
                  <?php
                  }else{
                    echo "<button class='btn btn-sm btn-default' disabled>Proceso Certificación</button>";
                  }
                }else{
                  if(isset($periodo_objecion['estatus_objecion']) && $periodo_objecion['estatus_objecion'] == 'FINALIZADO' && isset($periodo_objecion['documento'])){
                  ?>
                    <button type="button" class="btn btn-sm btn-primary" style="width:100%" data-toggle="modal" data-target="<?php echo "#certificacion".$solicitud['idsolicitud']; ?>">Proceso Certificación</button>
                  <?php
                  }else{
                    echo "<button class='btn btn-sm btn-default' disabled>Proceso Certificación</button>";
                  }
                }
                ?>

                  <!-- inicia modal proceo de certificación -->
                  <div id="<?php echo "certificacion".$solicitud['idsolicitud']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                    <div class="modal-dialog modal-lg" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                          <h4 class="modal-title" id="myModalLabel">Proceso de Certificación <?php echo $solicitud['idsolicitud']; ?></h4>
                        </div>
                        <div class="modal-body">
                          <div class="row">
                            <div class="col-md-12">
                              Historial Estatus Certificación
                            </div>
                            <?php 
                            $row_proceso_certificacion = mysql_query("SELECT proceso_certificacion.*, estatus_interno.nombre FROM proceso_certificacion INNER JOIN estatus_interno ON proceso_certificacion.estatus_interno = estatus_interno.idestatus_interno WHERE idsolicitud_registro = $solicitud[idsolicitud] AND estatus_interno IS NOT NULL", $dspp) or die(mysql_error());
                            while($historial_certificacion = mysql_fetch_assoc($row_proceso_certificacion)){
                            echo "<div class='col-md-10'>Proceso: $historial_certificacion[nombre]</div>";
                            echo "<div class='col-md-2'>Fecha: ".date('d/m/Y',$historial_certificacion['fecha_registro'])."</div>";
                            }
                             ?>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                          <!--<button type="button" class="btn btn-primary">Guardar Cambios</button>-->
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- termina modal proceo de certificación -->

              </td>



              <!---- termina PROCESO CERTIFICACIÓN ---->

              <!---- inicia MEMBRESIA ---->
              <td>
                <?php 
                if(isset($solicitud['idmembresia'])){
                  $row_membresia = mysql_query("SELECT membresia.*, comprobante_pago.* FROM membresia LEFT JOIN comprobante_pago ON membresia.idcomprobante_pago = comprobante_pago.idcomprobante_pago WHERE idmembresia = $solicitud[idmembresia]", $dspp) or die(mysql_error());
                  $membresia = mysql_fetch_assoc($row_membresia);
                ?>
                  <button type="button" class="btn btn-sm btn-primary" style="width:100%" data-toggle="modal" data-target="<?php echo "#membresia".$solicitud['idmembresia']; ?>">Estatus Membresía</button>
                <?php
                }else{
                  echo "NO DISPONIBLE";
                }
                 ?>
                <!-- inicia modal estatus membresia -->
                <div id="<?php echo "membresia".$solicitud['idmembresia']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                  <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Estatus Membresía</h4>
                      </div>
                      <div class="modal-body">
                        <div class="row">
                          <div class="col-md-12">
                            <?php 
                            if(isset($membresia['idcomprobante_pago'])){
                              echo '<p class="alert alert-info">
                              Estatus Comprobante: <span style="color:red">'.$membresia['estatus_comprobante'].'</span><br>
                              Monto de la membresia: <span style="color:red">'.$membresia['monto'].'</span>
                              </p>';
                            }
                            ?>
                            <p>
                              <b>Comprobante de Pago</b>
                            </p>
                            <?php 
                              if(!isset($membresia['archivo'])){
                                echo "<p class='alert alert-warning'>Aun no se ha cargado el comprobante de pago</p>";
                              }else{
                              ?>
                                <p class="alert alert-success">Se ha cargado el comprobante de pago, ahora puede descargarlo. Una vez revisado debera de "APROBAR" o "RECHAZAR" el comprobante de pago de la membresia</p>
                                <a href="<?php echo $membresia['archivo']; ?>" target="_blank" class="btn btn-info" style="width:100%">Descargar Comprobante</a>
                                <hr>
                                <?php 
                                if($membresia['estatus_comprobante'] == 'ACEPTADO'){
                                  echo "<p class='text-center alert alert-success'><b>La membresía se ha activado</b></p>";
                                }else{
                                ?>
                                  <p class="alert alert-info">
                                    Para aprobar la membresia debe de "APROBAR" el comprobante de pago, si se "RECHAZA" se le notificara a la Empresa para que pueda revisarlo y cargar nuevamente uno nuevo.
                                  </p>
                                    <div class="text-center">
                                      <label for="observaciones">Observaciones(<span style="color:red">en caso de ser rechazado</span>)</label>
                                      <textarea name="observaciones_comprobante" id="observaciones_comprobante" class="form-control" placeholder="Observaciones"></textarea>
                                      <input type="hidden" name="idcomprobante_pago" value="<?php echo $membresia['idcomprobante_pago']; ?>">
                                      <input type="hidden" name="idmembresia" value="<?php echo $membresia['idmembresia']; ?>">
                                      <button type="submit" class="btn btn-sm btn-success" style="width:45%" name="aprobar_comprobante" value="1"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Aprobar</button>
                                      <button type="submit" class="btn btn-sm btn-danger" style="width:45%" name="rechazar_comprobante" value="2"><span class="glyphicon glyphicon-remove"></span> Rechazar</button>
                                    </div>
                                <?php
                                }
                                ?>
                              <?php
                              }
                             ?>
                          </div>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        <!--<button type="button" class="btn btn-primary">Guardar Cambios</button>-->
                      </div>
                    </div>
                  </div>
                </div>
                <!-- termina modal estatus membresia -->

              </td>
              <!---- termina MEMBRESIA ---->

              <!---- inicia CERTIFICADO ---->
              <td>
                <button type="button" class="btn btn-sm btn-primary" style="width:100%" data-toggle="modal" data-target="<?php echo "#certificado".$solicitud['idsolicitud']; ?>">Consultar Certificado</button>

                <!-- revisamos la documentación que han cargado -->
                <?php 
                if(isset($solicitud['iddictamen_evaluacion']) && isset($solicitud['idinforme_evaluacion']) && isset($solicitud['idformato_evaluacion'])){
                  $query_informe = mysql_query("SELECT * FROM formato_evaluacion WHERE idsolicitud_registro = $solicitud[idsolicitud]", $dspp) or die(mysql_error());
                  $formato = mysql_fetch_assoc($query_informe);

                  $query_informe = mysql_query("SELECT * FROM informe_evaluacion WHERE idsolicitud_registro = $solicitud[idsolicitud]", $dspp) or die(mysql_error());
                  $informe = mysql_fetch_assoc($query_informe);

                  $query_informe = mysql_query("SELECT * FROM dictamen_evaluacion WHERE idsolicitud_registro = $solicitud[idsolicitud]", $dspp) or die(mysql_error());
                  $dictamen = mysql_fetch_assoc($query_informe);
                ?>
                  <div class="row">
                    <div class="col-md-12">
                      <?php 
                      if(file_exists($formato['archivo'])){
                      ?>
                        <a href="<?php echo $formato['archivo']; ?>" target="_new" style="color:green">
                          <span class="glyphicon glyphicon-file"></span> Formato
                        </a>
                      <?php
                      }else{
                      ?>
                        <a href="#" style="color:red" class="disabled">
                          <span class="glyphicon glyphicon-remove"></span> Formato
                        </a>
                      <?php
                      }
                       ?>
                    </div>

                    <div class="col-md-12">
                      <?php 
                      if(file_exists($informe['archivo'])){
                      ?>
                        <a href="<?php echo $informe['archivo']; ?>" target="_new" style="color:green">
                          <span class="glyphicon glyphicon-file"></span> Informe
                        </a>
                      <?php
                      }else{
                      ?>
                        <a href="#" style="color:red" class="disabled">
                          <span class="glyphicon glyphicon-remove"></span> Informe
                        </a>
                      <?php
                      }
                       ?>
                    </div>

                    <div class="col-md-12">
                      <?php 
                      if(file_exists($dictamen['archivo'])){
                      ?>
                        <a href="<?php echo $dictamen['archivo']; ?>" target="_new" style="color:green">
                          <span class="glyphicon glyphicon-file"></span> Dictamen
                        </a>
                      <?php
                      }else{
                      ?>
                        <a href="#" style="color:red" class="disabled">
                          <span class="glyphicon glyphicon-remove"></span> Dictamen
                        </a>
                      <?php
                      }
                       ?>
                    </div>

                  </div>
                <?php
                }
                 ?>

                <!-- inicia modal estatus membresia -->
                <div id="<?php echo "certificado".$solicitud['idsolicitud']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                  <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Información Certificado</h4>
                      </div>
                      <div class="modal-body">
                        <div class="row">
                          <div class="col-md-6">
                            <?php 
                            if($solicitud['tipo_solicitud'] != 'RENOVACION'){
                            ?>
                              <h4>Contrato de Uso y Acuse de Recibo</h4>
                              <?php 
                              if(isset($solicitud['idcontrato'])){
                                $row_contrato = mysql_query("SELECT * FROM contratos WHERE idcontrato = $solicitud[idcontrato]", $dspp) or die(mysql_error());
                                $contrato = mysql_fetch_assoc($row_contrato);

                                if($contrato['estatus_contrato'] == "ACEPTADO"){
                                  echo "<p class='alert alert-success'>Se ha aceptado el Contrato de Uso</p>";
                                  echo "<a href=".$contrato['archivo']." target='_blank' class='btn btn-sm btn-success' style='width:100%'>Descargar Contrato</a>";
                                  if(empty($contrato['acuse_recibo'])){
                                    echo "<a href=".$contrato['acuse_recibo']." target='_blank' class='disabled btn btn-sm btn-success' style='width:100%'>Descargar Acuse de Recibo</a>";
                                  }else{
                                    echo "<a href=".$contrato['acuse_recibo']." target='_blank' class='btn btn-sm btn-success' style='width:100%'>Descargar Acuse de Recibo</a>";
                                  }
                                }else{
                                ?>
                                  <div class="btn-group" role="group" aria-label="...">
                                    <a href="<?php echo $contrato['archivo']; ?>" target="_blank" class="btn btn-default"><span class="glyphicon glyphicon-flempresay-save" aria-hidden="true"></span> Descargar Contrato</a>
                                    <?php 
                                    if(empty($contrato['acuse_recibo'])){
                                    ?>
                                      <a href="<?php echo $contrato['acuse_recibo']; ?>" target="_blank" class="disabled btn btn-default"><span class="glyphicon glyphicon-ban-circle" aria-hidden="true"></span> Acuse de Recibo No Disponible</a>
                                    <?php
                                    }else{
                                    ?>
                                      <a href="<?php echo $contrato['acuse_recibo']; ?>" target="_blank" class="btn btn-default"><span class="glyphicon glyphicon-flempresay-save" aria-hidden="true"></span> Descargar Acuse de Recibo</a>
                                    <?php
                                    }
                                     ?>
                                  </div>                               
                                  <!--<a href="<?php echo $contrato['archivo']; ?>" target="_blank" class="btn btn-sm btn-success" style="width:100%">Descargar Contrato</a>
                                  <a href="<?php echo $contrato['acuse_recibo']; ?>" target="_blank" class="btn btn-sm btn-success" style="width:100%">Descargar Acuse de Recibo</a>-->
                                  <label for="observaciones_contrato">Observaciones (<span style="color:red">en caso de ser rechazado</span>)</label>
                                  <textarea name="observaciones_contrato" id="observaciones_contrato" class="form-control" placeholder="Observaciones Contrato"></textarea>
                                  <div class="col-md-12">
                                    <button class="btn btn-sm btn-success" name="aprobar_contrato" value="1" style="width:45%"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Aprobar</button>
                                    <button class="btn btn-sm btn-danger" name="rechazar_contrato" value="2" style="width:45%"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Rechazar</button>
                                  </div>
                                <?php
                                }
                              ?>

                              <?php
                              }else{
                                echo "<p class='alert alert-warning'>Aun no se ha cargado el <span style='colore:red'>Contrato de Uso</span></p>";
                              }
                               ?>

                            <?php
                            }
                             ?>

                            <h4>Formato, Dictamen e Informe de Evaluación</h4>
                            <?php 
                            if(isset($solicitud['iddictamen_evaluacion']) && isset($solicitud['idinforme_evaluacion']) && isset($solicitud['idformato_evaluacion'])){
                              $row_formato = mysql_query("SELECT * FROM formato_evaluacion WHERE idformato_evaluacion = $solicitud[idformato_evaluacion]", $dspp) or die(mysql_error());
                              $formato = mysql_fetch_assoc($row_formato);
                              $row_dictamen = mysql_query("SELECT * FROM dictamen_evaluacion WHERE iddictamen_evaluacion = $solicitud[iddictamen_evaluacion]", $dspp) or die(mysql_error());
                              $dictamen = mysql_fetch_assoc($row_dictamen);
                              $row_informe = mysql_query("SELECT * FROM informe_evaluacion WHERE idinforme_evaluacion = $solicitud[idinforme_evaluacion]", $dspp) or die(mysql_error());
                              $informe = mysql_fetch_assoc($row_informe);
                            ?>

                                <div class="alert alert-info">
                                  <p>
                                    Formato de Evaluación
                                  </p>
                                  <a href="<?php echo $formato['archivo']; ?>" class="btn btn-success" target="_new">Descargar Formato</a>
                                  <label class="radio-inline">
                                    <input type="radio" name="estatus_formato" id="" value="ACEPTADO" <?php if($formato['estatus_formato'] == 'ACEPTADO'){ echo "checked"; } ?>> ACEPTADO
                                  </label>
                                  <label class="radio-inline">
                                    <input type="radio" name="estatus_formato" id="" value="RECHAZADO" <?php if($formato['estatus_formato'] == 'RECHAZADO'){ echo "checked"; } ?>> RECHAZADO
                                  </label>

                                </div>

                                <div class="alert alert-warning">
                                  <p>
                                    Informe de Evaluación
                                  </p>
                                  <a href="<?php echo $informe['archivo']; ?>" class="btn btn-success" target="_new">Descargar Informe</a>
                                  <label class="radio-inline">
                                    <input type="radio" name="estatus_informe" id="" value="ACEPTADO" <?php if($informe['estatus_informe'] == 'ACEPTADO'){ echo "checked"; } ?>> ACEPTADO
                                  </label>
                                  <label class="radio-inline">
                                    <input type="radio" name="estatus_informe" id="" value="RECHAZADO" <?php if($informe['estatus_informe'] == 'RECHAZADO'){ echo "checked"; } ?>> RECHAZADO
                                  </label>

                                </div>
                                <div class="alert alert-info">
                                  <p>Dictamen de Evaluación</p>
                                  <a href="<?php echo $dictamen['archivo']; ?>" class="btn btn-success" target="_new">Descargar Dictamen</a>
                                  <label class="radio-inline">
                                    <input type="radio" name="estatus_dictamen" id="inlineRadio1" value="ACEPTADO" <?php if($dictamen['estatus_dictamen'] == 'ACEPTADO'){ echo "checked"; } ?>> ACEPTADO
                                  </label>
                                  <label class="radio-inline">
                                    <input type="radio" name="estatus_dictamen" id="inlineRadio2" value="RECHAZADO" <?php if($dictamen['estatus_dictamen'] == 'RECHAZADO'){ echo "checked"; } ?>> RECHAZADO
                                  </label>

                                </div>
                                <input type="hidden" name="idformato_evaluacion" value="<?php echo $formato['idformato_evaluacion']; ?>">
                                <input type="hidden" name="iddictamen_evaluacion" value="<?php echo $dictamen['iddictamen_evaluacion']; ?>">
                                <input type="hidden" name="idinforme_evaluacion" value="<?php echo $informe['idinforme_evaluacion']; ?>">
                                <?php 
                                if($dictamen['estatus_dictamen'] != "ACEPTADO" && $informe['estatus_informe'] != "ACEPTADO"){
                                ?>
                                  <button type="submit" class="form-control btn btn-primary" style="color:white" name="documentos_evaluacion" value="1" onclick="return validar()">ACEPTAR DOCUMENTOS</button>
                                <?php
                                }
                                 ?>
                                
                            <?php
                            }else{
                              echo "<p class='alert alert-warning'>Aun no se ha cargado el \"Informe de Evaluación\" así como el \"Dictamen de Evaluación\"</p>";
                            }
                             ?>
                          </div>
                          <div class="col-md-6">
                            <h4>Certificado</h4>
                            <?php 
                            if(isset($solicitud['idcertificado'])){
                                $row_certificado = mysql_query("SELECT * FROM certificado WHERE idcertificado = $solicitud[idcertificado]", $dspp) or die(mysql_error());

                                $certificado = mysql_fetch_assoc($row_certificado);

                                if(isset($certificado['idsolicitud_registro'])){
                                  $inicio = strtotime($certificado['vigencia_inicio']);
                                  $fin = strtotime($certificado['vigencia_fin']);
                                ?>
                                  <p class="alert alert-info">Se ha cargado el certificado, el cual tienen una Vigencia del <b><?php echo date('d/m/Y', $inicio); ?></b> al <b><?php echo date('d/m/Y', $fin); ?></b></p>
                                  <a href="<?php echo $certificado['archivo']; ?>" class="btn btn-success" style="width:100%" target="_blank">Descargar Certificado</a>

                                <?php
                                }      
                              ?>
                              <?php
                            }else{
                              echo "<p class='alert alert-danger'>Aun no se ha cargado el Certificado</p>";
                            }
                             ?>
                          </div>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <input type="hidden" name="idcontrato" value="<?php echo $solicitud['idcontrato']; ?>">
                        <input type="hidden" name="idmembresia" value="<?php echo $solicitud['idmembresia']; ?>">


                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        <!--<button type="button" class="btn btn-primary">Guardar Cambios</button>-->
                      </div>
                    </div>
                  </div>
                </div>
                <!-- termina modal estatus membresia -->
                <input type="hidden" name="idperiodo_objecion" value="<?php echo $solicitud['idperiodo_objecion']; ?>">
                <input type="hidden" name="tipo_solicitud" value="<?php echo $solicitud['tipo_solicitud']; ?>">
                <input type="hidden" name="idsolicitud_registro" value="<?php echo $solicitud['idsolicitud']; ?>">
              </td>
              <!---- inicia CERTIFICADO ---->
          </form>
              <!---- inicia CONSULTAR SOLICITUD ---->
              <td>
                <!--<a class="btn btn-sm btn-primary" data-toggle="tooltip" title="Visualizar Solicitud" href="?SOLICITUD&idsolicitud=<?php echo $solicitud['idsolicitud']; ?>"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></a>-->
                <form action="../../reportes/solicitud_empresa.php" method="POST" target="_new">
                  <button class="btn btn-xs btn-default" data-toggle="tooltip" title="Descargar solicitud" target="_new" type="submit" ><img src="../../img/pdf.png" style="height:30px;" alt=""></button>

                  <input type="hidden" name="idsolicitud_registro" value="<?php echo $solicitud['idsolicitud']; ?>">
                  <input type="hidden" name="generar_formato" value="1">
                </form>

                <form action="" method="POST">
                  <input type="hidden" name="idsolicitud_registro" value="<?php echo $solicitud['idsolicitud']; ?>">
                  <button type="submit" name="anclar" value="1">Anclar</button>

                </form>

              </td>
              <!---- termina CONSULTAR SOLICITUD ---->

            </tr>

          <?php
          }
          ?>
      </tbody>
    </table>
<?php 
  echo $paginacion;
 ?>
    
  </div>
</div>

<script>
  
  function validar(){
   /* valor = document.getElementById("cotizacion_empresa").value;
    if( valor == null || valor.length == 0 ) {
      alert("No se ha cargado la cotización de el OPP");
      return false;
    }*/
    
    estatus_informe = document.getElementsByName("estatus_informe");
     
    var seleccionado = false;
    for(var i=0; i<estatus_informe.length; i++) {    
      if(estatus_informe[i].checked) {
        seleccionado = true;
        break;
      }
    }
     
    if(!seleccionado) {
      alert("Debes de seleecionar \"ACEPTAR\" o \"DENEGAR\" el Informe de Evaluación");
      return false;
    }

    estatus_dictamen = document.getElementsByName("estatus_dictamen");
    var seleccionado2 = false;
    for(var i=0; i<estatus_dictamen.length; i++) {    
      if(estatus_dictamen[i].checked) {
        seleccionado2 = true;
        break;
      }
    }
     
    if(!seleccionado2) {
      alert("Debes de seleecionar \"ACEPTAR\" o \"DENEGAR\" el Dictamen de Evaluación");
      return false;
    }


    return true
  }

</script>
