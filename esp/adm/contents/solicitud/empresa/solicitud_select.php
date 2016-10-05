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
  $estatus_dspp = 6; //INICIA PERIODO DE OBJECIÓN
  $idperiodo_objecion = $_POST['idperiodo_objecion'];
  $estatus_objecion = "ACTIVO";
  

  /// se consultan los datos de de solicitud, empresa, oc para el mensaje
  $row_empresa = mysql_query("SELECT solicitud_registro.*, empresa.idempresa, empresa.nombre AS 'nombre_empresa', empresa.abreviacion AS 'abreviacion_empresa', empresa.telefono, empresa.email, empresa.pais, oc.nombre AS 'nombre_oc', oc.email1 AS 'email_oc' FROM solicitud_registro LEFT JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa LEFT JOIN oc ON solicitud_registro.idoc = oc.idoc WHERE idsolicitud_registro = $_POST[idsolicitud_registro]", $dspp) or die(mysql_error());
  $detalle_empresa = mysql_fetch_assoc($row_empresa);


  //INSERTAMOS EL PROCESO DE CERTIFICACIÓN
  $insertSQL = sprintf("INSERT INTO proceso_certificacion (idsolicitud_registro, estatus_dspp, fecha_registro) VALUES (%s, %s, %s)",
    GetSQLValueString($_POST['idsolicitud_registro'], "int"),
    GetSQLValueString($estatus_dspp, "int"),
    GetSQLValueString($fecha, "int"));
  $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

  //ACTUALIZAMOS EL PERIODO DE OBJECIÓN
  $updateSQL = sprintf("UPDATE periodo_objecion SET estatus_objecion = %s WHERE idperiodo_objecion = %s",
    GetSQLValueString($estatus_objecion, "text"),
    GetSQLValueString($idperiodo_objecion, "int"));
 $actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());

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
                      <td style="text-align:center">Nombre de la organización/Organization name</td>
                      <td style="text-align:center">Abreviación / Short name</td>
                      <td style="text-align:center">País / Country</td>
                      <td style="text-align:center">Organismo de Certificación / Certification Entity</td>

                      <td style="text-align:center">Tipo de solicitud / Kind of application</td>
                      <td style="text-align:center">Fecha de solicitud/Date of application</td>
                      <td style="text-align:center">Fin período de objeción/Objection period end</td>
                    </tr>
                    <tr style="font-size:12px">
                      <td>empresa</td>
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

    ///// inicia envio a correos empresa
      $query_empresa = "SELECT email FROM empresa WHERE email !=''";
      $ejecutar = mysql_query($query_empresa,$dspp) or die(mysql_error());


      while($email_empresa = mysql_fetch_assoc($ejecutar)){
        $mail->AddAddress($email_empresa['email']);
      }

        $mail->AddBCC($administrador);
        $mail->Subject = utf8_decode($asunto);
        $mail->Body = utf8_decode($cuerpo_mensaje);
        $mail->MsgHTML(utf8_decode($cuerpo_mensaje));
        $mail->Send();
        $mail->ClearAddresses();

    ///// termina envio a correo empresa

    //// inicia envio a correo Empresas
      $query_empresa = "SELECT email FROM empresa WHERE email !=''";
      $ejecutar = mysql_query($query_empresa,$dspp) or die(mysql_error());


      while($email_empresa = mysql_fetch_assoc($ejecutar)){
        $mail->AddAddress($email_empresa['email']);
      }

        $mail->AddBCC($administrador);
        $mail->Subject = utf8_decode($asunto);
        $mail->Body = utf8_decode($cuerpo_mensaje);
        $mail->MsgHTML(utf8_decode($cuerpo_mensaje));
        $mail->Send();
        $mail->ClearAddresses();

    //// termina envio a correo Empresas

    //// inicia envio a correo OC
      $query_oc = "SELECT email1 FROM oc WHERE email1 !=''";
      $ejecutar = mysql_query($query_oc,$dspp) or die(mysql_error());


      while($email_oc = mysql_fetch_assoc($ejecutar)){
        $mail->AddAddress($email_oc['email1']);
      }

        $mail->AddBCC($administrador);
        $mail->Subject = utf8_decode($asunto);
        $mail->Body = utf8_decode($cuerpo_mensaje);
        $mail->MsgHTML(utf8_decode($cuerpo_mensaje));
        $mail->Send();
        $mail->ClearAddresses();

    //// termina envio a correo OC

    //// inicia envio a correo ADM
        $query_adm = "SELECT email FROM adm";
        $ejecutar = mysql_query($query_adm,$dspp) or die(mysql_error());

        while($email_adm = mysql_fetch_assoc($ejecutar)){  
          if($email_adm['email'] != "isc.jesusmartinez@gmail.com"){
            $mail->AddAddress($email_adm['email']);
          }
        }

        $mail->AddBCC($administrador);
        $mail->Subject = utf8_decode($asunto);
        $mail->Body = utf8_decode($cuerpo_mensaje);
        $mail->MsgHTML(utf8_decode($cuerpo_mensaje));
        if($mail->Send()){
          
          echo "<script>alert('Correo enviado Exitosamente.');location.href ='javascript:history.back()';</script>";
        }else{
              echo "<script>alert('Error, no se pudo enviar el correo');location.href ='javascript:history.back()';</script>";
     
        }
    //// termina envio a correo ADM


 /// TERMINA ENVIAR MENSAJE PERIODO DE OBJECIÓN

 $mensaje = "Se ha iniciado el Periodo de Objeción";

}
//SE CARGA Y ENVIA LA RESOLUCIÓN DE OBJECIÓN
if(isset($_POST['enviar_resolucion']) && $_POST['enviar_resolucion'] == 1){
  /// se consultan los datos de de solicitud, empresa, oc para el mensaje
  $row_empresa = mysql_query("SELECT solicitud_registro.*, empresa.idempresa, empresa.nombre AS 'nombre_empresa', empresa.abreviacion AS 'abreviacion_empresa', empresa.telefono, empresa.email AS 'email_empresa', empresa.pais, oc.nombre AS 'nombre_oc', oc.email1 AS 'email_oc' FROM solicitud_registro LEFT JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa LEFT JOIN oc ON solicitud_registro.idoc = oc.idoc WHERE idsolicitud_registro = $_POST[idsolicitud_registro]", $dspp) or die(mysql_error());
  $detalle_empresa = mysql_fetch_assoc($row_empresa);

  $row_periodo = mysql_query("SELECT fecha_inicio, fecha_fin FROM periodo_objecion WHERE idperiodo_objecion = $_POST[idperiodo_objecion]",$dspp) or die(mysql_error());
  $periodo = mysql_fetch_assoc($row_periodo);


  $ruta = "../../archivos/admArchivos/resolucion/";

  if(!empty($_FILES['cargar_resolucion']['name'])){
    $_FILES['cargar_resolucion']['name'];
        move_uploaded_file($_FILES["cargar_resolucion"]["tmp_name"], $ruta.$fecha."_".$_FILES["cargar_resolucion"]["name"]);
        $resolucion = $ruta.basename($fecha."_".$_FILES["cargar_resolucion"]["name"]);
  }else{
    $resolucion = NULL;
  }
  //actualizamos el periodo de objeción
  $estatus_objecion = 'FINALIZADO';

  $updateSQL = sprintf("UPDATE periodo_objecion SET estatus_objecion = %s, observacion = %s, dictamen = %s, documento = %s WHERE idperiodo_objecion = %s",
    GetSQLValueString($estatus_objecion, "text"),
    GetSQLValueString($_POST['observacion'], "text"),
    GetSQLValueString($_POST['dictamen'], "text"),
    GetSQLValueString($resolucion, "text"),
    GetSQLValueString($_POST['idperiodo_objecion'], "int"));
  $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());


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
              <td colspan="2"><p><b>Ha finalizado el periodo de objeción con un dictamen: <span style="color:red;">'.$_POST['dictamen'].'</span></b></p></td>
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
                    <td>empresa</td>
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

          </tbody>
        </table>

      </body>
    </html>
  ';

  $mail->AddAddress($detalle_empresa['email_oc']);
  $mail->AddBCC($spp_global);  
  $mail->AddBCC($administrador);

  $mail->AddAttachment($resolucion);

  $mail->Subject = utf8_decode($asunto);
  $mail->Body = utf8_decode($mensaje_oc);
  $mail->MsgHTML(utf8_decode($mensaje_oc));
  $mail->Send();
  $mail->ClearAddresses();

  /// termina envio correo "periodo de objeción finalizado" a OC

  /// inicia envio correo "periodo de objeción finalizado" a empresa
  $mensaje_empresa = '
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
              <td colspan="2"><p><b>Ha finalizado el periodo de objeción con un dictamen: <span style="color:red;">'.$_POST['dictamen'].'</span></b></p></td>
            </tr>
            <tr> 
              <td colspan="2">Fecha Inicio: <span style="color:red">'.date('d/m/Y', $periodo['fecha_inicio']).'</span></td>
            </tr>
            <tr>
              <td colspan="2">Fecha Fin: <span style="color:red">'.date('d/m/Y', $periodo['fecha_fin']).'</span></td>
            </tr>
            <tr>
              <td colspan="2">
                Ha finalizado el periodo de objeción. Se ha iniciado el Proceso de Certificación, por favor ponerse en contacto con su Organismo de Certificación, para cualquier duda o aclaración por favor escribir a: cert@spp.coop
                
                <p>Organismo de Certificación: <span style="color:red">'.$detalle_empresa['nombre_oc'].'</span></p>
                
                <p>Email: <span style="color:red">'.$detalle_empresa['email_oc'].'</span></p>
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
                    <td>empresa</td>
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

          </tbody>
        </table>

      </body>
    </html>
  ';

  $mail->AddAddress($detalle_empresa['email_empresa']); 
  if(!empty($detalle_empresa['contacto1_email'])){
    $mail->AddAddress($detalle_empresa['contacto1_email']); 
  }
  if(!empty($detalle_empresa['contacto2_email'])){
    $mail->AddAddress($detalle_empresa['contacto2_email']); 
  }
  if(!empty($detalle_empresa['adm1_email'])){
    $mail->AddAddress($detalle_empresa['adm1_email']); 
  }
  $mail->AddBCC($administrador);

  $mail->AddAttachment($resolucion);

  $mail->Subject = utf8_decode($asunto);
  $mail->Body = utf8_decode($mensaje_empresa);
  $mail->MsgHTML(utf8_decode($mensaje_empresa));
  $mail->Send();
  $mail->ClearAddresses();

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

  //insertarmos el proceso_certificacion
  $insertSQL = sprintf("INSERT INTO proceso_certificacion(idsolicitud_registro, estatus_dspp, fecha_registro) VALUES (%s, %s, %s)",
    GetSQLValueString($_POST['idsolicitud_registro'], "int"),
    GetSQLValueString($estatus_dspp, "int"),
    GetSQLValueString($fecha, "int"));
  $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

  //inicia enviar mensaje aprobacion membresia
  $row_informacion = mysql_query("SELECT solicitud_registro.idempresa, solicitud_registro.contacto1_email, empresa.email, empresa.nombre, oc.email1 FROM solicitud_registro INNER JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa INNER JOIN oc ON solicitud_registro.idoc = oc.idoc", $dspp) or die(mysql_error());
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
                 <th scope="col" align="left" width="280"><p>empresa: <span style="color:red">'.$informacion['nombre'].'</span></p></th>
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
    $mail->AddAddress($informacion['contacto1_email']); 
  }
  if(!empty($informacion['email'])){
    $mail->AddAddress($informacion['email']); 
  }

  $mail->Subject = utf8_decode($asunto);
  $mail->Body = utf8_decode($cuerpo_mensaje);
  $mail->MsgHTML(utf8_decode($cuerpo_mensaje));
  $mail->Send();
  $mail->ClearAddresses();
  //termina enviar mensaje aprobacion de membresia

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
               <th scope="col" align="left" width="280"><p>empresa: <span style="color:red">'.$informacion['nombre'].'</span></p></th>
              </tr>

              <tr>
                <td colspan="2">
                 <p>SPP GLOBLA notifica que la empresa: '.$informacion['nombre'].' ha cumplido con la documentación necesaria.</p>
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
                    <li>Seleccionar "Solicitudes empresa"</li>
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

        $mail->AddAddress($informacion['email1']); 
        $mail->Subject = utf8_decode($asunto);
        $mail->Body = utf8_decode($cuerpo_mensaje);
        $mail->MsgHTML(utf8_decode($cuerpo_mensaje));
        $mail->Send();
        $mail->ClearAddresses();

        $mensaje = "Se ha aprobado el \"Contrato de Uso\" y la \"Membresía SPP\", se le ha notificado al OC para que cargue Formato, Dictamen e Informe de Evaluación";

    }else{
      $mensaje = "Se ha aprobado la membresia";
    }
  }else{
    $mensaje = "Se ha aprobado la membresia";
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

  $mensaje = "Se ha rechaza la membresia y el empresa ha sido notificado";
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

  //creamos el proceso_certificacion
  $insertSQL = sprintf("INSERT INTO proceso_certificacion(idsolicitud_registro, estatus_dspp, fecha_registro) VALUES(%s, %s, %s)",
    GetSQLValueString($_POST['idsolicitud_registro'], "int"),
    GetSQLValueString($estatus_dspp, "int"),
    GetSQLValueString($fecha, "int"));
  $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
  if(!empty($_POST['idmembresia'])){
    $row_membresia = mysql_query("SELECT solicitud_registro.idempresa, solicitud_registro.idoc, empresa.nombre, oc.email1, membresia.idsolicitud_registro, membresia.estatus_membresia FROM solicitud_registro INNER JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa INNER JOIN oc ON solicitud_registro.idoc = oc.idoc INNER JOIN membresia ON solicitud_registro.idsolicitud_registro = membresia.idsolicitud_registro WHERE membresia.idmembresia = $_POST[idmembresia]", $dspp) or die(mysql_error());
    $membresia = mysql_fetch_assoc($row_membresia);
    if ($membresia['estatus_membresia'] == 'APROBADA') {

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
               <th scope="col" align="left" width="280"><p>empresa: <span style="color:red">'.$membresia['nombre'].'</span></p></th>
              </tr>

              <tr>
                <td colspan="2">
                 <p>SPP GLOBLA notifica que la empresa: '.$membresia['nombre'].' ha cumplido con la documentación necesaria.</p>
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
                    <li>Seleccionar "Solicitudes empresa"</li>
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

        $mail->AddAddress($membresia['email1']); 
        $mail->Subject = utf8_decode($asunto);
        $mail->Body = utf8_decode($cuerpo_mensaje);
        $mail->MsgHTML(utf8_decode($cuerpo_mensaje));
        $mail->Send();
        $mail->ClearAddresses();


        $mensaje = "Se ha aprobado el \"Contrato de Uso\" y la \"Membresía SPP\", se le ha notificado al OC para que cargue Formato, Dictamen e Informe de Evaluación";

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
    $row_informacion = mysql_query("SELECT solicitud_registro.idoc, solicitud_registro.idempresa, empresa.nombre AS 'nombre_empresa', oc.email1 FROM solicitud_registro INNER JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa INNER JOIN oc ON solicitud_registro.idoc = oc.idoc WHERE solicitud_registro.idsolicitud_registro = $_POST[idsolicitud_registro]", $dspp) or die(mysql_error());
    $informacion = mysql_fetch_assoc($row_informacion);

    $asunto = "D-SPP | Notificación Certificado";

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
             <th scope="col" align="left" width="280"><p>empresa: <span style="color:red">'.$informacion['nombre_empresa'].'</span></p></th>
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
                   <li>Seleccione la pestaña "Solicitudes" y de clic en la opción "Solicitudes empresa".</li>
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
    ';

      $mail->AddAddress($informacion['email1']); 
      $mail->Subject = utf8_decode($asunto);
      $mail->Body = utf8_decode($cuerpo_mensaje);
      $mail->MsgHTML(utf8_decode($cuerpo_mensaje));
      $mail->Send();
      $mail->ClearAddresses();

  }

  $mensaje = "Se ha notificado al OC";
}


$row_solicitud = mysql_query("SELECT solicitud_registro.idsolicitud_registro AS 'idsolicitud', solicitud_registro.fecha_registro, empresa.nombre AS 'nombre_empresa', empresa.abreviacion AS 'abreviacion_empresa', proceso_certificacion.idproceso_certificacion, proceso_certificacion.estatus_interno, proceso_certificacion.estatus_dspp, estatus_dspp.nombre AS 'nombre_dspp', solicitud_registro.cotizacion_empresa, periodo_objecion.*, membresia.idmembresia, membresia.estatus_membresia, contratos.idcontrato, contratos.estatus_contrato, certificado.idcertificado, formato_evaluacion.idformato_evaluacion, informe_evaluacion.idinforme_evaluacion, dictamen_evaluacion.iddictamen_evaluacion FROM solicitud_registro LEFT JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa LEFT JOIN proceso_certificacion ON solicitud_registro.idsolicitud_registro = proceso_certificacion.idsolicitud_registro LEFT JOIN periodo_objecion ON solicitud_registro.idsolicitud_registro = periodo_objecion.idsolicitud_registro LEFT JOIN estatus_dspp ON proceso_certificacion.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN membresia ON solicitud_registro.idsolicitud_registro = membresia.idsolicitud_registro LEFT JOIN contratos ON solicitud_registro.idsolicitud_registro = contratos.idsolicitud_registro LEFT JOIN certificado ON solicitud_registro.idsolicitud_registro = certificado.idsolicitud_registro LEFT JOIN formato_evaluacion ON solicitud_registro.idsolicitud_registro = formato_evaluacion.idsolicitud_registro LEFT JOIN informe_evaluacion ON solicitud_registro.idsolicitud_registro = informe_evaluacion.idsolicitud_registro LEFT JOIN dictamen_evaluacion ON solicitud_registro.idsolicitud_registro = dictamen_evaluacion.idsolicitud_registro  ORDER BY proceso_certificacion.idproceso_certificacion DESC", $dspp) or die(mysql_error());

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
    <table class="table table-bordered" style="font-size:12px">
      <thead>
        <tr>
          <th class="text-center">ID</th>
          <th class="text-center">Fecha Solicitud</th>
          <th class="text-center">Organización</th>
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
        <form action="" method="POST" enctype="multipart/form-data">
          <?php 
          while($solicitud = mysql_fetch_assoc($row_solicitud)){
          ?>
            <tr>
              <td>
                <?php echo $solicitud['idsolicitud']; ?>
                <input type="hidden" name="idsolicitud_registro" value="<?php echo $solicitud['idsolicitud']; ?>">
              </td>
              <td><?php echo date('d/m/Y',$solicitud['fecha_registro']); ?></td>
              <td><?php echo $solicitud['abreviacion_empresa']; ?></td>
              <td><?php echo $solicitud['nombre_dspp']; ?></td>
              <td>
              <?php
              if(isset($solicitud['cotizacion_empresa'])){
                 echo "<a class='btn btn-success form-control' style='font-size:12px;color:white;height:30px;' href='".$solicitud['cotizacion_empresa']."' target='_blank'><span class='glyphicon glyphicon-download' aria-hidden='true'></span> Descargar Cotización</a>";
                 if($solicitud['estatus_dspp'] == 5){ // SE ACEPTA LA COTIZACIÓN
                  echo "<p class='alert alert-success' style='padding:7px;'>Estatus: ".$solicitud['nombre_dspp']."</p>"; 
                 }else if($solicitud['estatus_dspp'] == 17){ // SE RECHAZA LA COTIZACIÓN
                  echo "<p class='alert alert-danger' style='padding:7px;'>Estatus: ".$solicitud['nombre_dspp']."</p>"; 
                 }else{
                  echo "<p class='alert alert-info' style='padding:7px;'>Estatus: ".$solicitud['nombre_dspp']."</p>"; 
                 }

              }else{ // INICIA CARGAR COTIZACIÓN
                echo "No Disponible";
              } // TERMINA CARGAR COTIZACIÓN
               ?>
              </td>
              <td>
                <?php 
                // //CHECAMOS SI LA HORA ACTUAL ES IGUAL o MAYOR A LA FECHA_FINAL DEL PERIODO DE OBJECION
                if(isset($solicitud['idperiodo_objecion']) && $solicitud['estatus_objecion'] == 'ACTIVO'){
                  if($fecha > $solicitud['fecha_fin']){
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
                      GetSQLValueString($solicitud['idperiodo_objecion'], "int"));
                    $actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());

                  }
                }
                if(isset($solicitud['idperiodo_objecion'])){
                ?>
                  <button type="button" class="btn btn-sm btn-primary" style="width:100%" data-toggle="modal" data-target="<?php echo "#objecion".$solicitud['idperiodo_objecion']; ?>">Proceso Objeción</button>
                <?php
                }else{
                  echo "<button class='btn btn-sm btn-default' style='width:100%' disabled>Consultar Proceso</button>";
                }
                 ?>
                <!-- INICIA MODAL PROCESO DE OBJECIÓN -->

                <div id="<?php echo "objecion".$solicitud['idperiodo_objecion']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                  <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Proceso de Objeción</h4>
                      </div>
                      <div class="modal-body">
                        <div class="row">
                          <div class="col-md-6">
                            <h4>Periodo de Objeción <small>(<?php echo $solicitud['estatus_objecion']; ?>)</small></h4>
                            <p class="alert alert-info" style="padding:7px;">Inicio: <?php echo date('d/m/Y',$solicitud['fecha_inicio']); ?></p>
                            <p class="alert alert-danger" style="padding:7px;">Fin: <?php echo date('d/m/Y',$solicitud['fecha_fin']); ?></p>
                            <?php 
                            if($solicitud['estatus_objecion'] == 'EN ESPERA'){
                              echo '<button type="submit" class="btn btn-success" name="aprobar_periodo" value="1">Aprobar Periodo</button>';
                            }
                            ?>
                          </div>

                          <div class="col-md-6">
                            <?php 
                            if($solicitud['estatus_objecion'] == 'FINALIZADO'){
                            ?>
                              <h4>Resolución de Objeción</h4>
                              <p class="alert alert-info" style="padding:7px;">
                                <b style="margin-right:10px;">Dictamen:</b>
                                <?php 
                                if(empty($solicitud['dictamen'])){
                                ?>
                                  <label class="radio-inline">
                                    <input type="radio" name="dictamen" id="positivo" value="POSITIVO"> Positivo
                                  </label>
                                  <label class="radio-inline">
                                    <input type="radio" name="dictamen" id="negativo" value="NEGATIVO"> Negativo
                                  </label>
                                <?php
                                }else{
                                  echo "<span style='color:#c0392b'>".$solicitud['dictamen']."</span>";
                                }
                                 ?>
                              </p>
                              <label for="observacion">Observaciones</label>
                              <?php 
                              if(empty($solicitud['observacion'])){
                                echo '<textarea name="observacion" id="observacion" class="form-control"></textarea>';
                              }else{
                                echo "<p style='color:#c0392b'>".$solicitud['observacion']."</p>";
                              }

                              if(empty($solicitud['documento'])){
                              ?>
                                <label for="cargar_resolucion">Cargar Resolución</label>
                                <input type="file" class="form-control" id="cargar_resolucion" name="cargar_resolucion" >

                                <button type="submit" class="btn btn-success" style="width:100%" name="enviar_resolucion" value="1">Enviar Resolución</button>
                              <?php
                              }else{
                                echo "<a href='".$solicitud['documento']."' class='btn btn-info' style='width:100%' target='_blank'>Descargar Resolución</a>";
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
                        <input type="hidden" name="idperiodo_objecion" value="<?php echo $solicitud['idperiodo_objecion']; ?>">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        <!--<button type="button" class="btn btn-primary">Guardar Cambios</button>-->
                      </div>
                    </div>
                  </div>
                </div>
                <!-- TERMINA MODAL PROCESO DE OBJECIÓN -->

              </td>
              <!----- INICIA PROCESO CERTIFICACIÓN ---->
              <td>
                <?php 
                if(isset($solicitud['estatus_objecion']) && $solicitud['estatus_objecion'] == 'FINALIZADO' && isset($solicitud['documento'])){
                ?>
                <button type="button" class="btn btn-sm btn-primary" style="width:100%" data-toggle="modal" data-target="<?php echo "#certificacion".$solicitud['idperiodo_objecion']; ?>">Proceso Certificación</button>
                <?php
                }else{
                  echo "<button class='btn btn-sm btn-default' disabled>Proceso Certificación</button>";
                }
                ?>
              </td>

                <!-- inicia modal proceo de certificación -->

                <div id="<?php echo "certificacion".$solicitud['idperiodo_objecion']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                  <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Proceso de Certificación</h4>
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
                        <input type="hidden" name="idperiodo_objecion" value="<?php echo $solicitud['idperiodo_objecion']; ?>">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        <!--<button type="button" class="btn btn-primary">Guardar Cambios</button>-->
                      </div>
                    </div>
                  </div>
                </div>
                <!-- termina modal proceo de certificación -->

              <!----- TERMINA PROCESO CERTIFICACIÓN ---->

              <!-- INICIA MEMBRESIA -->
              <td>
                <?php 
                if(isset($solicitud['idmembresia'])){
                  $row_membresia = mysql_query("SELECT membresia.*, comprobante_pago.* FROM membresia LEFT JOIN comprobante_pago ON membresia.idcomprobante_pago = comprobante_pago.idcomprobante_pago WHERE idmembresia = $solicitud[idmembresia]", $dspp) or die(mysql_error());
                  $membresia = mysql_fetch_assoc($row_membresia);
                ?>
                  <button type="button" class="btn btn-sm btn-primary" style="width:100%" data-toggle="modal" data-target="<?php echo "#membresia".$solicitud['idmembresia']; ?>">Estatus Membresía</button>
                <?php
                }
                 ?>
              </td>

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
                            }else{

                            }
                             ?>

                            <p><b>Comprobante de Pago</b></p>
                            <?php 
                              if(!isset($membresia['archivo'])){
                                echo "<p class='alert alert-warning'>Aun no se ha cargado el comprobante de pago</p>";
                              }else{
                                echo "<p class='alert alert-success'>Se ha cargado el comprobante de pago, ahora puede descargarlo. Una vez revisado debera de \"APROBAR\" o \"RECHAZAR\" el comprobante de pago de la membresia</p>";

                              ?>
                                <a href="<?php echo $membresia['archivo']; ?>" target="_blank" class="btn btn-info" style="width:100%">Descargar Comprobante</a>
                                <hr>
                                <?php 
                                if($membresia['estatus_comprobante'] == 'ACEPTADO'){
                                  echo "<p class='text-center alert alert-success'><b>La membresía se ha activado</b></p>";
                                }else{
                                ?>
                                  <p class="alert alert-info">
                                    Para aprobar la membresia debe de "APROBAR" el comprobante de pago, si se "RECHAZA" se le notificara al empresa para que pueda revisarlo y cargar nuevamente uno nuevo.
                                  </p>
                                    <div class="text-center">
                                      <label for="observaciones">Observaciones(<span style="color:red">en caso de ser rechazado</span>)</label>
                                      <textarea name="observaciones_comprobante" id="observaciones_comprobante" class="form-control" placeholder="Observaciones"></textarea>
                                      <input type="hidden" name="idcomprobante_pago" value="<?php echo $membresia['idcomprobante_pago']; ?>">
                                      <input type="hidden" name="idmembresia" value="<?php echo $solicitud['idmembresia']; ?>">
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

              <!-- TERMINA MEMBRESIA -->
              
              <!----- INICIA VENTANA CERTIFICADO ------>
              <td>
                <button type="button" class="btn btn-sm btn-primary" style="width:100%" data-toggle="modal" data-target="<?php echo "#certificado".$solicitud['idsolicitud_registro']; ?>">Consultar Certificado</button>
              </td>
                <!-- inicia modal estatus membresia -->

                <div id="<?php echo "certificado".$solicitud['idsolicitud_registro']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                  <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Información Certificado</h4>
                      </div>
                      <div class="modal-body">
                        <div class="row">
                          <div class="col-md-6">
                            <h4>Contrato de Uso</h4>
                            <?php 
                            if(isset($solicitud['idcontrato'])){
                              $row_contrato = mysql_query("SELECT * FROM contratos WHERE idcontrato = $solicitud[idcontrato]", $dspp) or die(mysql_error());
                              $contrato = mysql_fetch_assoc($row_contrato);

                              if($contrato['estatus_contrato'] == "ACEPTADO"){
                                echo "<p class='alert alert-success'>Se ha aceptado el Contrato de Uso</p>";
                                echo "<a href=".$contrato['archivo']." target='_blank' class='btn btn-sm btn-success' style='width:100%'>Descargar Contrato</a>";

                              }else{
                              ?>
                                <a href="<?php echo $contrato['archivo']; ?>" target="_blank" class="btn btn-sm btn-success" style="width:100%">Descargar Contrato</a>
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

                            <h4>Formato, Dictamen e Informe de Evaluación</h4>
                            <?php 
                            if(isset($solicitud['iddictamen_evaluacion']) && isset($solicitud['idinforme_evaluacion'])){
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
                                  <button type="submit" class="btn btn-primary" name="documentos_evaluacion" value="1" onclick="return validar()">Actualizar Documentos</button>
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
                                $inicio = strtotime($certificado['vigencia_inicio']);
                                $fin = strtotime($certificado['vigencia_fin']);
                              ?>
                                <p class="alert alert-info">Se ha cargado el certificado, el cual tienen una Vigencia del <b><?php echo date('d/m/Y', $inicio); ?></b> al <b><?php echo date('d/m/Y', $fin); ?></b></p>
                                <a href="<?php echo $certificado['archivo']; ?>" class="btn btn-success" style="width:100%" target="_blank">Descargar Certificado</a>
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

              <!----- TERMINA VENTANA CERTIFICADO ------>
              <td>
                <a class="btn btn-primary" data-toggle="tooltip" title="Visualizar Solicitud" href="?SOLICITUD&idsolicitud_empresa=<?php echo $solicitud['idsolicitud']; ?>"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></a>
              </td>
            </tr>
          <?php
          }
           ?>
        </form>
      </tbody>
    </table>
  </div>
</div>

<script>
  
  function validar(){
   /* valor = document.getElementById("cotizacion_empresa").value;
    if( valor == null || valor.length == 0 ) {
      alert("No se ha cargado la cotización de el empresa");
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