<?php 
  $row_periodo = mysql_query("SELECT * FROM periodo_objecion");

  while($periodo = mysql_fetch_assoc($row_periodo)){  //// INICIA WHILE
    // REVISION PRIMER MENSAJE 7 DIAS
    $num_dias1 = 7;
    $num_dias2 = 10;

    if($periodo['estatus_objecion'] == 'ACTIVO'){ // inicia primer if
      $dias = $num_dias1 * (24*60*60);
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
                              <td style="padding:10px;">'.$informacion_opp['direccion_oficina'].'</td>
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
 ?>