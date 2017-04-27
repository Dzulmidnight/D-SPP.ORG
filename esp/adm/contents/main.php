<?php 
require_once('../Connections/dspp.php'); 
require_once('../Connections/mail.php'); 

//error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

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
mysql_select_db($database_dspp, $dspp);

$estado = 1;
$administrador = 'cert@spp.coop';

$row_periodo = mysql_query("SELECT * FROM periodo_objecion");

?>
<h4>Menú principal Administrador</h4>

<?php
  ////////////////////// INICIA SECCIÓN MENSAJES PERIODO DE OBJECIÓN //////////////////////////////
  while($periodo = mysql_fetch_assoc($row_periodo)){  //// INICIIA WHILE
      // REVISION PRIMER MENSAJE 7 DIAS
    $num_dias1 = 7;
    $num_dias2 = 10;

    if($periodo['estatus_objecion'] == 'ACTIVO'){
      $dias = $num_dias1 *(24*60*60);
      $alerta2 = $periodo['fecha_inicio'] + $dias;

      if(!isset($periodo['alerta2'])){
        if(time() >= $alerta2){
          $updateSQL = sprintf("UPDATE periodo_objecion SET alerta2 = %s WHERE idperiodo_objecion = %s",
            GetSQLValueString($estado, "int"),
            GetSQLValueString($periodo['idperiodo_objecion'], "int"));
          $actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error()); 

          //////////////////////// INICIA ENVIO DE MENSAJES //////////////////////
          if(isset($periodo['idsolicitud_certificacion'])){
            $row_opp = mysql_query("SELECT solicitud_certificacion.idopp, solicitud_certificacion.idoc, GROUP_CONCAT(productos.producto) AS lista_productos, opp.nombre AS 'nombre_opp', opp.pais, opp.direccion_oficina, opp.direccion_fiscal, oc.nombre AS 'nombre_oc' FROM solicitud_certificacion LEFT JOIN productos ON solicitud_certificacion.idsolicitud_certificacion = productos.idsolicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp INNER JOIN oc ON solicitud_certificacion.idoc = oc.idoc WHERE solicitud_certificacion.idsolicitud_certificacion = $periodo[idsolicitud_certificacion]", $dspp) or die(mysql_error());
            $informacion_opp = mysql_fetch_assoc($row_opp);

            $direccion_opp = '';
            if(isset($informacion_opp['direccion_oficina'])){
              $direccion_opp = $informacion_opp['direccion_oficina'];
            }else{
              $direccion_opp = $informacion_opp['direccion_fiscal'];
            }
            $asunto = "D-SPP | PRIMER RECORDATORIO: PERIODO DE OBJECIÓN";

            $mensaje = '
              <html>
                <head>
                  <meta charset="utf-8">
                </head>
                <body>
                
                <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;text-align:justify" border="0" width="650px">
                  <tbody>
                    <tr>
                      <th rowspan="2" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                      <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red"><span style="color:#2c3e50">PRIMER RECORDATORIO</span>: PERIODO DE OBJECIÓN</span></p></th>

                    </tr>
                    <tr>
                      <td colspan="2" >
                        <p>
                          Recordatorio enviado del sistema D-SPP, recordandole que el Periodo de Objeción de la Organización: <b style="color:red">'.$informacion_opp['nombre_opp'].'</b> está por concluir (<span style="color:red">faltan 8 días</span>).
                        </p>
                      </td>
                    </tr>
                    <tr style="width:100%">
                      <td colspan="2">
                          <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px; border:#27ae60" border="1" width="650px">

                            <tr style="font-size: 12px; text-align:center; background-color:#27ae60; color:#ecf0f1;" height="50px;">
                              <td style="text-align:center">Tipo / Type</td>
                              <td style="text-align:center">Nombre de la organización/Organization name</td>
                              <td style="text-align:center">País / Country</td>
                              <td style="text-align:center">Dirección / Address</td>
                              <td style="text-align:center">Organismo de Certificación / Certification Entity</td>

                              <td style="text-align:center">Tipo de solicitud / Kind of application</td>
                              <td style="text-align:center">Productos / Products</td>
                              <td style="text-align:center">Inicio Periodo de Objeción</td>
                              <td style="text-align:center">Fin período de objeción</td>
                            </tr>
                            <tr style="font-size:12px; color:#2c3e50;">
                              <td style="padding:10px;">OPP</td>
                              <td style="padding:10px;">'.$informacion_opp['nombre_opp'].'</td>
                              <td style="padding:10px;">'.$informacion_opp['pais'].'</td>
                              <td style="padding:10px;">'.$direccion_opp.'</td>
                              <td style="padding:10px;">'.$informacion_opp['nombre_oc'].'</td>
                              <td style="padding:10px;">Certificación</td>
                              <td style="text-align:center">'.$informacion_opp['lista_productos'].'</td>
                              <td style="padding:10px;">'.date('d/m/Y', $periodo['fecha_inicio']).'</td>
                              <td style="padding:10px;background-color:#e74c3c; color:#ecf0f1">'.date('d/m/Y', $periodo['fecha_fin']).'</td>
                            </tr>
                          </table>
                      </td>
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

          }else if(isset($periodo['idsolicitud_registro'])){
            $row_empresa = mysql_query("SELECT solicitud_registro.idempresa, solicitud_registro.idoc, empresa.nombre AS 'nombre_empresa', empresa.pais, oc.nombre AS 'nombre_oc' FROM solicitud_registro INNER JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa INNER JOIN oc ON solicitud_registro.idoc = oc.idoc WHERE idsolicitud_registro = $periodo[idsolicitud_registro]", $dspp) or die(mysql_error());
            $informacion_empresa = mysql_fetch_assoc($row_empresa);

            $asunto = "D-SPP | PRIMER RECORDATORIO: PERIODO DE OBJECIÓN";

            $mensaje = '
              <html>
                <head>
                  <meta charset="utf-8">
                </head>
                <body>
                
                  <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;text-align:justify" border="0" width="650px">
                    <tbody>
                      <tr>
                        <th rowspan="2" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                        <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red"><span style="color:#2c3e50">PRIMER RECORDATORIO</span>: PERIODO DE OBJECIÓN</span></p></th>

                      </tr>
                      <tr>
                        <td colspan="2" >
                          <p>
                            Recordatorio enviado del sistema D-SPP, recordandole que el Periodo de Objeción de la Empresa: <b style="color:red">'.$informacion_empresa['nombre_empresa'].'</b> está por concluir (<span style="color:red">faltan 8 días</span>).
                          </p>
                        </td>
                      </tr>
                      <tr style="width:100%">
                        <td colspan="2">
                            <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;border: #3498db" border="1" width="650px">

                              <tr style="font-size: 12px; text-align:center; background-color:#3498db; color:#ecf0f1;" height="50px;">
                                <td style="text-align:center">Tipo / Type</td>
                                <td style="text-align:center">Nombre de la organización/Organization name</td>
                                <td style="text-align:center">País / Country</td>
                                <td style="text-align:center">Organismo de Certificación / Certification Entity</td>

                                <td style="text-align:center">Tipo de solicitud / Kind of application</td>
                                <td style="text-align:center">Inicio Periodo de Objeción</td>
                                <td style="text-align:center">Fin período de objeción</td>
                              </tr>
                              <tr style="font-size:12px; color:#2c3e50;">
                                <td style="padding:10px;">Empresa</td>
                                <td style="padding:10px;">'.$informacion_empresa['nombre_empresa'].'</td>
                                <td style="padding:10px;">'.$informacion_empresa['pais'].'</td>
                                <td style="padding:10px;">'.$informacion_empresa['nombre_oc'].'</td>
                                <td style="padding:10px;">Registro</td>
                                <td style="padding:10px;">'.date('d/m/Y', $periodo['fecha_inicio']).'</td>
                                <td style="padding:10px;background-color:#e74c3c; color:#ecf0f1">'.date('d/m/Y', $periodo['fecha_fin']).'</td>
                              </tr>
                            </table>
                        </td>
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
          }
          $mail->AddAddress($administrador);
          $mail->Subject = utf8_decode($asunto);
          $mail->Body = utf8_decode($mensaje);
          $mail->MsgHTML(utf8_decode($mensaje));
          $mail->Send();
          $mail->ClearAddresses();
          
        }
        //////////////////////// TERMINA ENVIO DE MENSAJES //////////////////////
      }
    }

    // REVISION SEGUNDO MENSAJE 10 DIAS
    if($periodo['estatus_objecion'] == 'ACTIVO'){ 
      $dias = $num_dias2 *(24*60*60);
      $alerta3 = $periodo['fecha_inicio'] + $dias;

      if(!isset($periodo['alerta3'])){ ////// INICIA REVISION SEGUNDO MENSAJE
        if(time() >= $alerta3){
          $updateSQL = sprintf("UPDATE periodo_objecion SET alerta3 = %s WHERE idperiodo_objecion = %s",
            GetSQLValueString($estado, "int"),
            GetSQLValueString($periodo['idperiodo_objecion'], "int"));
          $actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());

          //////////////////////// INICIA ENVIO DE MENSAJES //////////////////////
          if(isset($periodo['idsolicitud_certificacion'])){
            $row_opp = mysql_query("SELECT solicitud_certificacion.idopp, solicitud_certificacion.idoc, GROUP_CONCAT(productos.producto) AS lista_productos, opp.nombre AS 'nombre_opp', opp.pais, opp.direccion_oficina, opp.direccion_fiscal, oc.nombre AS 'nombre_oc' FROM solicitud_certificacion LEFT JOIN productos ON solicitud_certificacion.idsolicitud_certificacion = productos.idsolicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp INNER JOIN oc ON solicitud_certificacion.idoc = oc.idoc WHERE solicitud_certificacion.idsolicitud_certificacion = $periodo[idsolicitud_certificacion]", $dspp) or die(mysql_error());
            $informacion_opp = mysql_fetch_assoc($row_opp);

            $asunto = "D-SPP | SEGUNDO RECORDATORIO: PERIODO DE OBJECIÓN";

            $mensaje = '
              <html>
                <head>
                  <meta charset="utf-8">
                </head>
                <body>
                
                <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;text-align:justify" border="0" width="650px">
                  <tbody>
                    <tr>
                      <th rowspan="2" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                      <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red"><span style="color:#2c3e50">SEGUNDO RECORDATORIO</span>: PERIODO DE OBJECIÓN</span></p></th>

                    </tr>
                    <tr>
                      <td colspan="2" >
                        <p>
                          Recordatorio enviado del sistema D-SPP, recordandole que el Periodo de Objeción de la Organización: <b style="color:red">'.$informacion_opp['nombre_opp'].'</b> está por concluir (<span style="color:red">faltan 5 días</span>).
                        </p>
                      </td>
                    </tr>
                    <tr style="width:100%">
                      <td colspan="2">
                          <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px; border:#27ae60" border="1" width="650px">

                            <tr style="font-size: 12px; text-align:center; background-color:#27ae60; color:#ecf0f1;" height="50px;">
                              <td style="text-align:center">Tipo / Type</td>
                              <td style="text-align:center">Nombre de la organización/Organization name</td>
                              <td style="text-align:center">País / Country</td>
                              <td style="text-align:center">Dirección / Address</td>
                              <td style="text-align:center">Organismo de Certificación / Certification Entity</td>

                              <td style="text-align:center">Tipo de solicitud / Kind of application</td>
                              <td style="text-align:center">Productos / Products</td>
                              <td style="text-align:center">Inicio Periodo de Objeción</td>
                              <td style="text-align:center">Fin período de objeción</td>
                            </tr>
                            <tr style="font-size:12px; color:#2c3e50;">
                              <td style="padding:10px;">OPP</td>
                              <td style="padding:10px;">'.$informacion_opp['nombre_opp'].'</td>
                              <td style="padding:10px;">'.$informacion_opp['pais'].'</td>
                              <td style="padding:10px;">'.$direccion_opp.'</td>
                              <td style="padding:10px;">'.$informacion_opp['nombre_oc'].'</td>
                              <td style="padding:10px;">Certificación</td>
                              <td style="text-align:center">'.$informacion_opp['lista_productos'].'</td>
                              <td style="padding:10px;">'.date('d/m/Y', $periodo['fecha_inicio']).'</td>
                              <td style="padding:10px;background-color:#e74c3c; color:#ecf0f1">'.date('d/m/Y', $periodo['fecha_fin']).'</td>
                            </tr>
                          </table>
                      </td>
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

          }else if(isset($periodo['idsolicitud_registro'])){
            $row_empresa = mysql_query("SELECT solicitud_registro.idempresa, solicitud_registro.idoc, empresa.nombre AS 'nombre_empresa', empresa.pais, oc.nombre AS 'nombre_oc' FROM solicitud_registro INNER JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa INNER JOIN oc ON solicitud_registro.idoc = oc.idoc WHERE idsolicitud_registro = $periodo[idsolicitud_registro]", $dspp) or die(mysql_error());
            $informacion_empresa = mysql_fetch_assoc($row_empresa);

            $asunto = "D-SPP | SEGUNDO RECORDATORIO: PERIODO DE OBJECIÓN";

            $mensaje = '
              <html>
                <head>
                  <meta charset="utf-8">
                </head>
                <body>
                
                  <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;text-align:justify" border="0" width="650px">
                    <tbody>
                      <tr>
                        <th rowspan="2" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                        <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red"><span style="color:#2c3e50">SEGUNDO RECORDATORIO</span>: PERIODO DE OBJECIÓN</span></p></th>

                      </tr>
                      <tr>
                        <td colspan="2" >
                          <p>
                            Recordatorio enviado del sistema D-SPP, recordandole que el Periodo de Objeción de la Empresa: <b style="color:red">'.$informacion_empresa['nombre_empresa'].'</b> está por concluir (<span style="color:red">faltan 5 días</span>).
                          </p>
                        </td>
                      </tr>
                      <tr style="width:100%">
                        <td colspan="2">
                            <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;border: #3498db" border="1" width="650px">

                              <tr style="font-size: 12px; text-align:center; background-color:#3498db; color:#ecf0f1;" height="50px;">
                                <td style="text-align:center">Tipo / Type</td>
                                <td style="text-align:center">Nombre de la organización/Organization name</td>
                                <td style="text-align:center">País / Country</td>
                                <td style="text-align:center">Organismo de Certificación / Certification Entity</td>

                                <td style="text-align:center">Tipo de solicitud / Kind of application</td>
                                <td style="text-align:center">Inicio Periodo de Objeción</td>
                                <td style="text-align:center">Fin período de objeción</td>
                              </tr>
                              <tr style="font-size:12px; color:#2c3e50;">
                                <td style="padding:10px;">Empresa</td>
                                <td style="padding:10px;">'.$informacion_empresa['nombre_empresa'].'</td>
                                <td style="padding:10px;">'.$informacion_empresa['pais'].'</td>
                                <td style="padding:10px;">'.$informacion_empresa['nombre_oc'].'</td>
                                <td style="padding:10px;">Registro</td>
                                <td style="padding:10px;">'.date('d/m/Y', $periodo['fecha_inicio']).'</td>
                                <td style="padding:10px;background-color:#e74c3c; color:#ecf0f1">'.date('d/m/Y', $periodo['fecha_fin']).'</td>
                              </tr>
                            </table>
                        </td>
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
          }
          $mail->AddAddress($administrador);
          $mail->Subject = utf8_decode($asunto);
          $mail->Body = utf8_decode($mensaje);
          $mail->MsgHTML(utf8_decode($mensaje));
          $mail->Send();
          $mail->ClearAddresses();

        }
        //////////////////////// TERMINA ENVIO DE MENSAJES //////////////////////
      }
      ////// TERMINA REVISION SEGUNDO MENSAJE

    }
  } //// TERMINA WHILE

  ///// TERMINA SECCIÓN MENSAJES PERIODO DE OBJECIÓN ///////////////////////////////////
  /**************************************/
  /**************************************/
  /**************************************/
  ///////////////////////// INICIA SECCIÓN MENSAJES RENOVACÓN DEL CERTIFICADO ///////////////////////////
    $time_actual = time();
    $anio_actual = date('Y', time());
    $time_vencimiento = "";
    $time_restante = "";
    $plazo = 60 *(24*60*60); // Calculamos el número de segundos que tienen 60 dias
    $time_actual= time();   // Obtenemos el timestamp del momento actual
    $contador = 1;
    //$time_vencimiento = strtotime("2016-02-12");
    //$time_vencimiento = strtotime(); // Obtenemos timestamp de la fecha de vencimiento
   // $time_restante = ($time_vencimiento - $time_actual);
    $row_opp = mysql_query("SELECT opp.idopp, opp.spp, opp.nombre, opp.abreviacion, opp.password, opp.email, opp.pais, MAX(certificado.idcertificado) AS 'idcertificado' FROM opp INNER JOIN certificado ON opp.idopp = certificado.idopp INNER JOIN oc ON certificado.entidad = oc.idoc GROUP BY certificado.idopp", $dspp) or die(mysql_error());

    while($opp = mysql_fetch_assoc($row_opp)){
      $row_certificado = mysql_query("SELECT certificado.entidad, certificado.vigencia_fin, oc.email1 AS 'oc_email1', oc.email2 AS 'oc_email2' FROM certificado INNER JOIN oc ON certificado.entidad = oc.idoc WHERE idcertificado = $opp[idcertificado]", $dspp) or die(mysql_error());
      $detalle_certificado = mysql_fetch_assoc($row_certificado);

      $row_contactos = mysql_query("SELECT contactos.email1, contactos.email2 FROM contactos WHERE contactos.idopp = $opp[idopp]", $dspp) or die(mysql_error());
      $contactos = mysql_fetch_assoc($row_opp);

      ///revisamos si se han enviado avisos de renovación
      $row_aviso_renovacion = mysql_query("SELECT * FROM avisos_renovacion WHERE idopp = $opp[idopp] AND ano_aviso = '$anio_actual' ORDER BY avisos_renovacion.idaviso_renovacion LIMIT 1", $dspp) or die(mysql_error());
      $aviso_renovacion = mysql_fetch_assoc($row_aviso_renovacion);
      // variables generales
      $fecha_vigencia = date('d-m-Y', strtotime($detalle_certificado['vigencia_fin']));
      $nombre_opp = $opp['nombre'];
      $abreviacion_opp = $opp['abreviacion'];
      $vigencia_final = $detalle_certificado['vigencia_fin'];
      //revisamos el año del ultimo aviso de renovacion
      $anio_aviso = $aviso_renovacion['ano_aviso'];


      /*if(isset($aviso_renovacion['idaviso_renovacion'])){
        echo "<p style='color:green'>#$contador - Si hay aviso de renovacion, idopp: $opp[idopp]<br></p>";
      }else{
        echo "<p style='color:red'>#$contador - No hay aviso de renovacion, idopp: $opp[idopp]<br></p>";
      }*/


      $time_vencimiento = strtotime($detalle_certificado['vigencia_fin']); //convertimos la fecha de vigencia mas reciente que obtenemos
      $time_restante = ($time_vencimiento - $time_actual); // restamos la fecha de vigencia - la fecha actual para saber CUANTO TIEMPO NOS QUEDA
      $estatus_certificado = "";

      //$plazo_despues = ($time_vencimiento + $plazo); //sumamos la fecha de vigencia + el plazo para que puedan renovar que se fija
      $prorroga = ($time_vencimiento + $plazo); //sumamos la fecha de vigencia + el plazo para que puedan renovar que se fija
      /////INICIA IF 1
      if($time_actual <= $time_vencimiento){ //EJ: 20 de febrero <= 28 de febrero
        if($time_restante <= $plazo){ //comparamos si el tiempo que nos queda es menor al plazo que tenemos, si es menor entrariamos a comparar los avisos de renovacion

          $estatus_certificado = 14; // AVISO DE RENOVACIÓN(de acuerdo al estatus_dspp)
          $destinatario_opp = "";
          //$row_oc = mysql_query("SELECT certificado.idcertificado, certificado.entidad, oc.* FROM certificado INNER JOIN oc ON certificado.entidad = oc.idoc WHERE idcertificado = $opp[idcertificado]", $dspp) or die(mysql_error());

          //$oc = mysql_fetch_assoc($row_oc);


          //if(!isset($aviso_renovacion['aviso1']) || ($anio_aviso != $anio_actual)){ // revisamos si se ha enviado el PRIMER AVISO O si el aviso que existe se haya enviado en el año en curso(comparamos años)
          if(!isset($aviso_renovacion['idaviso_renovacion'])){  /// INIICIA IF AVISO RENOVACION
            $asunto = "D-SPP - Aviso de Renovacion de Certificado / SPP Certificate Renewal Notice"; 
            ///CORREOS A LOS QUE SE ENVIARA EL CORREO DE RENOVACIÓN
            if(!empty($opp['email'])){
              $mail->AddAddress($opp['email']);
            }
            if(!empty($contactos['email1'])){
              $mail->AddAddress($opp['email1']);
            }
            if(!empty($contactos['email2'])){
              $mail->AddAddress($opp['email2']);
            }
            if(!empty($detalle_certificado['oc_email1'])){
              $mail->AddAddress($detalle_certificado['oc_email1']);
            }
            if(!empty($detalle_certificado['oc_email2'])){
              $mail->AddAddress($detalle_certificado['oc_email2']);
            }
            ///correos SPP GLOBAL con copia oculta
            $mail->AddBCC("cert@spp.coop");
            $mail->AddBCC("adm@spp.coop");
            $mail->AddBCC("com@spp.coop");


            $cuerpo = '
              <html>
              <head>
                <meta charset="utf-8">
              </head>
              <body>    
                <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
                  <tbody>
                    <tr>
                      <th rowspan="1" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                      <th scope="col" align="left" width="500"><strong><h3>Aviso de Renovación de Certificado SPP</h3></strong></th>
                    </tr>
                    <tr>
                      <td style="text-align:justify; padding-top:2em" colspan="2">
                     
                        <p>Estimados Representantes de <strong style="color:red">'.$nombre_opp.', (<u>'.$abreviacion_opp.'</u>)</strong>:</p>
                        
                        <p>Por este conducto se les informa la necesidad de renovación de su Certificado SPP. La fecha de su vigencia de su certificado spp es <strong style="color:red">'.$fecha_vigencia.'</strong>, por lo que deben proceder con la evaluación anual.</p>
                        
                        <p>De acuerdo a los procedimientos del SPP, se puede llevar a cabo la evaluación dos meses antes de la fecha de vigencia o máximo dos meses después.  Si la evaluación se realiza dos meses después, se esperaría que el dictamen se obtuviera 4 meses después  (de la fecha de vencimiento del certificado) como plazo máximo, para obtener el dictamen positivo de parte del Organismo de Certificación.</p>
                      
                        <p>Queremos enfatizar que actualmente existen políticas para la suspensión y/o cancelación del certificado por lo que si ustedes no solicitan a tiempo pueden ser acreedores de una suspensión.</p>
                        
                        <p>Agradeciendo su atención, nos despedimos y enviamos saludos del SPP GLOBAL.</p>

                        <p style="color:#2c3e50"><b>En caso de haber iniciado ya su proceso de renovación del certificado por favor hacer caso omiso a este mensaje</b></p>
                        
                        <p>CUALQUIER INCONVENIENTE FAVOR DE NOTIFICARLO A SPP GLOBAL AL CORREO <strong>cert@spp.coop</strong></p>
                      
                      </td>
                    </tr>


                    <tr>
                      <td style="text-align:justify; padding-top:2em" colspan="2">
                     
                        <p>Dear <strong style="color:red">'.$nombre_opp.', (<u>'.$abreviacion_opp.'</u>)</strong> Representatives</p>
                        
                        <p>You are hereby informed of the need for renewal of your SPP Certificate. The effective date of your SPP certificate is: <strong style="color:red">'.$fecha_vigencia.'</strong>, so you must proceed with the annual evaluation.</p>
                        
                        <p>According to the procedures of the SPP, the evaluation can be carried out two months before the effective date of your certificate or maximum two months later. If the evaluation is carried out two months later, it would be expected that the opinion would be obtained 4 months <span style="color:red">later (from the expiration date of the certificate) as a maximum term</span>, in order to obtain a positive opinion from the Certification Entity.</p>
                      
                        <p>We want to emphasize that there are currently policies for the suspension and / or cancellation of the certificate, so if you do not apply on time you may be entitled to a suspension.</p>
                        
                        <p>Thank you for your attention, we said goodbye and we send greetings from SPP GLOBAL.</p>

                        <p style="color:#2c3e50"><b>If you have already started your certificate renewal process please ignore this message</b></p>
                        
                        <p>ANY INCONVENIENT PLEASE NOTICE TO SPP GLOBAL TO THE MAIL <strong>cert@spp.coop</strong></p>
                      
                      </td>
                    </tr>

                  </tbody>
                </table>
              </body>
              </html>
            ';
            $mail->Subject = utf8_decode($asunto);
            $mail->Body = utf8_decode($cuerpo);
            $mail->MsgHTML(utf8_decode($cuerpo));
            $mail->Send();
            $mail->ClearAddresses();

            $insertSQL = sprintf("INSERT INTO avisos_renovacion(idopp, aviso1, ano_aviso, idcertificado, fecha_certificado) VALUES(%s, %s, %s, %s, %s)",
              GetSQLValueString($opp['idopp'], "int"),
              GetSQLValueString($time_actual, "int"),
              GetSQLValueString($anio_actual, "text"),
              GetSQLValueString($opp['idcertificado'], "int"),
              GetSQLValueString($detalle_certificado['vigencia_fin'], "text"));
            $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
            //echo "opp: $opp[idopp] - ";
          } // TERMINA IF AVISO RENOVACION
          //echo "<p style='color:green'>SE ENVIO CORREO id certificado: $opp[idcertificado] - idopp: $opp[idopp]</p>";

        }else{
          $estatus_certificado = 10; // CERTIFICADO ACTIVO
          //echo "<p style='color:blue'>DENTRO DE FECHA id certificado: $opp[idcertificado] - idopp: $opp[idopp]</p>";
        }
      }else{
       
        if($prorroga >= $time_actual){
          $estatus_certificado = 15; // CERTIFICADO POR EXPIRAR(segun estatus_dspp)
          if(!isset($aviso_renovacion['aviso2'])){
            //agregamos el aviso de renovacion
            if(!isset($aviso_renovacion['idaviso_renovacion'])){
              $insertSQL = sprintf("INSERT INTO avisos_renovacion(idopp, aviso1, aviso2, ano_aviso, idcertificado, fecha_certificado) VALUES(%s, %s, %s, %s, %s, %s)",
                GetSQLValueString($opp['idopp'], "int"),
                GetSQLValueString($time_actual, "int"),
                GetSQLValueString($time_actual, "int"),
                GetSQLValueString($anio_actual, "text"),
                GetSQLValueString($opp['idcertificado'], "int"),
                GetSQLValueString($detalle_certificado['vigencia_fin'], "text"));
              $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
              
            }else{
              
              $updateSQL = sprintf("UPDATE avisos_renovacion SET aviso2 = %s WHERE idaviso_renovacion = %s",
                GetSQLValueString($time_actual, "int"),
                GetSQLValueString($aviso_renovacion['idaviso_renovacion'], "text"));
              $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());
            }

            $asunto = "D-SPP - Certificado por expirar / Certified for expiring"; 
            ///CORREOS A LOS QUE SE ENVIARA EL CORREO DE RENOVACIÓN
            if(!empty($opp['email'])){
              $mail->AddAddress($opp['email']);
            }
            if(!empty($contactos['email1'])){
              $mail->AddAddress($opp['email1']);
            }
            if(!empty($contactos['email2'])){
              $mail->AddAddress($opp['email2']);
            }
            if(!empty($detalle_certificado['oc_email1'])){
              $mail->AddAddress($detalle_certificado['oc_email1']);
            }
            if(!empty($detalle_certificado['oc_email2'])){
              $mail->AddAddress($detalle_certificado['oc_email2']);
            }

            ///correos SPP GLOBAL con copia oculta
            $mail->AddBCC("cert@spp.coop");
            $mail->AddBCC("adm@spp.coop");
            $mail->AddBCC("com@spp.coop");


            $cuerpo = '
              <html>
              <head>
                <meta charset="utf-8">
              </head>
              <body>    
                <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
                  <tbody>
                    <tr>
                      <th rowspan="1" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                      <th scope="col" align="left" width="500"><strong><h3>Aviso de Renovación de Certificado SPP / SPP Certificate Renewal Notice</h3></strong></th>
                    </tr>
                    <tr>
                      <td style="text-align:justify; padding-top:2em" colspan="2">
                     
                        <p>Estimados Representantes de <strong style="color:red">'.$nombre_opp.', (<u>'.$abreviacion_opp.'</u>)</strong>:</p>
                        
                        <p>Por este conducto se les informa la necesidad de renovación de su Certificado SPP. La fecha de su vigencia es <strong style="color:red">'.$fecha_vigencia.'</strong>, la cual se encuentra a punto de expirar.</p>
                        
                        <p>De acuerdo a los procedimientos del SPP, se puede llevar a cabo la evaluación dos meses antes de la fecha de vigencia o máximo dos meses después.  Si la evaluación se realiza <span style="color:red">dos meses después</span>, se esperaría que el dictamen se obtuviera 4 meses <span style="color:red">después  (de la fecha de vencimiento del certificado) como plazo máximo</span>, para obtener el dictamen positivo de parte del Organismo de Certificación.</p>
                      
                        <p>Queremos enfatizar que actualmente existen políticas para la suspensión y/o cancelación del certificado por lo que si ustedes no solicitan a tiempo pueden ser acreedores de una suspensión.</p>
                        
                        <p>Agradeciendo su atención, nos despedimos y enviamos saludos del SPP GLOBAL.</p>

                        <p style="color:#2c3e50"><b>En caso de haber iniciado ya su proceso de renovación del certificado por favor hacer caso omiso a este mensaje</b></p>
                        
                        <p>CUALQUIER INCONVENIENTE FAVOR DE NOTIFICARLO A SPP GLOBAL AL CORREO <strong>cert@spp.coop</strong></p>
                      
                      </td>
                    </tr>

                    <tr>
                      <td style="text-align:justify; padding-top:2em" colspan="2">
                     
                        <p>Dear <strong style="color:red">'.$nombre_opp.', (<u>'.$abreviacion_opp.'</u>)</strong> Representatives</p>
                        
                        <p>You are hereby informed of the need for renewal of your SPP Certificate. The effective date of your SPP certificate is: <strong style="color:red">'.$fecha_vigencia.'</strong>, so you must proceed with the annual evaluation.</p>
                        
                        <p>According to the procedures of the SPP, the evaluation can be carried out two months before the effective date of your certificate or maximum two months later. If the evaluation is carried out two months later, it would be expected that the opinion would be obtained 4 months <span style="color:red">later (from the expiration date of the certificate) as a maximum term</span>, in order to obtain a positive opinion from the Certification Entity.</p>
                      
                        <p>We want to emphasize that there are currently policies for the suspension and / or cancellation of the certificate, so if you do not apply on time you may be entitled to a suspension.</p>
                        
                        <p>Thank you for your attention, we said goodbye and we send greetings from SPP GLOBAL.</p>

                        <p style="color:#2c3e50"><b>If you have already started your certificate renewal process please ignore this message</b></p>
                        
                        <p>ANY INCONVENIENT PLEASE NOTICE TO SPP GLOBAL TO THE MAIL <strong>cert@spp.coop</strong></p>
                      
                      </td>
                    </tr>

                  </tbody>
                </table>
              </body>
              </html>
            ';


            //$mail->Username = "soporte@d-spp.org";
            //$mail->Password = "/aung5l6tZ";
            $mail->Subject = utf8_decode($asunto);
            $mail->Body = utf8_decode($cuerpo);
            $mail->MsgHTML(utf8_decode($cuerpo));
            $mail->Send();
            $mail->ClearAddresses();


          }

          //echo "<p style='color:black'>CERTIFICADO POR EXPIRAR $opp[idcertificado] - idopp: $opp[idopp]</p>";
        }/*else{
          $estatus_certificado = 11; // CERTIFICADO EXPIRADO
          if(!isset($aviso_renovacion['aviso3'])){

            //agregamos el aviso de renovacion
            if(!isset($aviso_renovacion['idaviso_renovacion'])){
              $insertSQL = sprintf("INSERT INTO avisos_renovacion(idopp, aviso1, aviso2, ano_aviso, idcertificado, fecha_certificado) VALUES(%s, %s, %s, %s, %s, %s)",
                GetSQLValueString($opp['idopp'], "int"),
                GetSQLValueString($time_actual, "int"),
                GetSQLValueString($time_actual, "int"),
                GetSQLValueString($anio_actual, "text"),
                GetSQLValueString($opp['idcertificado'], "int"),
                GetSQLValueString($detalle_certificado['vigencia_fin'], "text"));
              $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
              
            }else{
              
              $updateSQL = sprintf("UPDATE avisos_renovacion SET aviso3 = %s WHERE idaviso_renovacion = %s",
                GetSQLValueString($time_actual, "int"),
                GetSQLValueString($aviso_renovacion['idaviso_renovacion'], "text"));
              $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());
            }
            $asunto = "D-SPP - Certificado Expirado"; 
            ///CORREOS A LOS QUE SE ENVIARA EL CORREO DE RENOVACIÓN
            if(!empty($opp['email'])){
              $mail->AddAddress($opp['email']);
            }
            if(!empty($contactos['email1'])){
              $mail->AddAddress($opp['email1']);
            }
            if(!empty($contactos['email2'])){
              $mail->AddAddress($opp['email2']);
            }
            if(!empty($detalle_certificado['oc_email1'])){
              $mail->AddAddress($detalle_certificado['oc_email1']);
            }
            if(!empty($detalle_certificado['oc_email2'])){
              $mail->AddAddress($detalle_certificado['oc_email2']);
            }

            ///correos SPP GLOBAL con copia oculta
            $mail->AddBCC("cert@spp.coop");
            $mail->AddBCC("adm@spp.coop");
            $mail->AddBCC("com@spp.coop");


            $cuerpo = '
              <html>
              <head>
                <meta charset="utf-8">
              </head>
              <body>    
                <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
                  <tbody>
                    <tr>
                      <th rowspan="1" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                      <th scope="col" align="left" width="500"><strong><h3>Aviso de Renovación de Certificado SPP</h3></strong></th>
                    </tr>
                    <tr>
                      <td style="text-align:justify; padding-top:2em" colspan="2">
                     
                        <p>Estimados Representantes de <strong style="color:red">'.$nombre_opp.', (<u>'.$abreviacion_opp.'</u>)</strong>:</p>
                        
                        <p>Por este conducto se les informa que su Certificado SPP ha expirado, el cual tenia una vigencia hasta el dia: <strong style="color:red">'.$fecha_vigencia.'</strong>.
                        
                        <p>CUALQUIER INCONVENIENTE FAVOR DE NOTIFICARLO A SPP GLOBAL AL CORREO <strong>cert@spp.coop</strong></p>
                      
                      </td>
                    </tr>
                  </tbody>
                </table>
              </body>
              </html>
            ';


            //$mail->Username = "soporte@d-spp.org";
            //$mail->Password = "/aung5l6tZ";
            $mail->Subject = utf8_decode($asunto);
            $mail->Body = utf8_decode($cuerpo);
            $mail->MsgHTML(utf8_decode($cuerpo));
            $mail->Send();
            $mail->ClearAddresses();

          }
          echo "<p style='color:#8e44ad'>CERTIIFICADO EXPIRADO $opp[idcertificado] - idopp: $opp[idopp]</p>";
        } */
      }
      ///TERMINA IF 1
      /* 14/02/201/
      $actualizar = "UPDATE opp SET estado = $estatus_certificado WHERE idopp = $row_opp[idopp]";
      $ejecutar = mysql_query($actualizar,$dspp) or die(mysql_error());

      $actualizar = "UPDATE certificado SET status = $estatus_certificado WHERE idcertificado = $row_opp[idcertificado]";
      $ejecutar = mysql_query($actualizar,$dspp) or die(mysql_error());
      14/02/2017 */
      $contador++;

    }
  //////////////////////// TERMINA SECCIÓN MENSAJES RENOVACÓN DEL CERTIFICADO ///////////////////////////
?>