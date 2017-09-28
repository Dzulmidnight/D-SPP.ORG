<?php 
  ///////////////////// SECCIÓN MENSAJES PAGO DE MEMBRESIA //////////
  $fecha_actual = time();
  $cinco_dias = 432000;
  $diez_dias = 864000;
  $veinte_dias = $diez_dias*2;

  $query = "SELECT opp.nombre AS 'nombre_opp', opp.abreviacion AS 'abreviacion_opp', opp.pais,  solicitud_certificacion.contacto1_email, solicitud_certificacion.contacto2_email, solicitud_certificacion.adm1_email, solicitud_certificacion.adm2_email, proceso_certificacion.idproceso_certificacion, proceso_certificacion.idsolicitud_certificacion, proceso_certificacion.fecha_registro AS 'fecha_dictamen', membresia.idmembresia, membresia.idopp, membresia.idcomprobante_pago, membresia.estatus_membresia, membresia.fecha_registro AS 'fecha_activacion', comprobante_pago.monto, comprobante_pago.estatus_comprobante, comprobante_pago.archivo, comprobante_pago.aviso1, comprobante_pago.aviso2, comprobante_pago.aviso3, comprobante_pago.notificacion_suspender FROM proceso_certificacion INNER JOIN solicitud_certificacion ON proceso_certificacion.idsolicitud_certificacion = solicitud_certificacion.idsolicitud_certificacion INNER JOIN membresia ON proceso_certificacion.idsolicitud_certificacion = membresia.idsolicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp INNER JOIN comprobante_pago ON membresia.idcomprobante_pago = comprobante_pago.idcomprobante_pago WHERE proceso_certificacion.estatus_interno = 8 GROUP BY membresia.idsolicitud_certificacion ORDER BY proceso_certificacion.fecha_registro DESC";
  $ejecutar = mysql_query($query, $dspp) or die(mysql_error());
  $contador = 1;
  while($registros = mysql_fetch_assoc($ejecutar)){ //// inicia while
    $formato_dictamen = date('d/m/Y', $registros['fecha_dictamen']);
    $fecha_dictamen = $registros['fecha_dictamen'];
    $recordatorio1 = $fecha_dictamen + $diez_dias;
    $recordatorio2 = $fecha_dictamen + $veinte_dias;
    $alerta_suspension = $recordatorio2 + $cinco_dias;
    $correo_suspender = $veinte_dias + $diez_dias;

    /// notificación 1º aviso
    if(!$registros['aviso1'] && $registros['estatus_comprobante'] != 'ACEPTADO'){
      if($fecha_actual >= $recordatorio1){

        $query = "UPDATE comprobante_pago SET aviso1 = 1 WHERE idcomprobante_pago = $registros[idcomprobante_pago]";
        $updateSQL = mysql_query($query, $dspp) or die(mysql_error());

        $asunto = "D-SPP | 1er recordatorio pago Membresía SPP (1st reminder payment SPP Membership)";

        $cuerpo_mensaje = '
              <html>
              <head>
                <meta charset="utf-8">
              </head>
              <body>
                      <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px;" border="0" width="650px">
                        <tbody>
                          <tr>
                            <th rowspan="1" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                            <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">1er recordatorio pago Membresía SPP (<i style="color:#34495e">1st reminder payment SPP Membership</i>)</span></p></th>
                          </tr>

                          <tr>
                            <td colspan="2" style="text-align:justify">
                              <p>
                                Estimados Representantes de : <span style="color:red">'.$registros['nombre_opp'].'</span> - ('.$registros['abreviacion_opp'].')
                              </p>
                              <p>
                                En seguimiento a la notificación de su dictamen positivo SPP, enviado con fecha  '.$formato_dictamen.' se les recuerda que tienen un plazo máximo de 30 días posterior a la fecha de notificación para realizar el pago correspondiente a <span style="color:red">'.$registros['monto'].'</span> por Membresía SPP y posteriormente cargar el comprobante de pago en el D-SPP.  
                              </p>
                              <p>
                                El no pagar la Membresía SPP oportunamente es un incumplimiento con el Marco Regulatorio, por lo tanto si el sistema no detecta un comprobante de pago, automáticamente enviará  la suspensión del Certificado.
                              </p>
                              <p>
                                 Por lo anterior, les solicitamos de la manera más atenta se proceda a realizar el pago a la mayor brevedad para evitar ser suspendidos.
                              </p>
                            </td>
                          </tr>
                          <tr>
                            <td colspan="2" style="padding-top:2em;">
                              <p>Para cualquier duda o aclaración por favor escribir a: <span style="color:red">cert@spp.coop</span> o <span style="color:red">soporte@d-spp.org</span></p>
                            </td>
                          </tr>

                          <tr style="color: #797979;">
                            <td style="padding-top:2em;">
                              <b>English Below</b>
                              <hr>
                            </td>
                          </tr>
                          <tr style="color: #797979;text-align:justify">
                            <td colspan="2">
                              <p>
                                Dear Representatives of: <span style="color:red">'.$registros['nombre_opp'].'</span> - ('.$registros['abreviacion_opp'].')
                              </p>
                              <p>
                                Following the notification of their positive opinion SPP, sent on '.$formato_dictamen.' they are reminded that they have a maximum period of 30 days after the date of notification to make the payment corresponding to <span style="color:red">'.$registros['monto'].'</span> per Membership SPP and later Load the proof of payment into the D-SPP.  
                              </p>
                              <p>
                                Failure to pay the SPP Membership in a timely manner is a breach of the Regulatory Framework, therefore if the system does not detect a payment receipt, it will automatically send the suspension of the Certificate.
                              </p>
                              <p>
                                 For the above, we ask you in the most careful way to proceed to make the payment as soon as possible to avoid being suspended.
                              </p>
                            </td>
                          </tr>

                          <tr>
                            <td colspan="2" style="padding-top:2em;">
                              <p>For any doubt or clarification please write to: <span style="color:red">cert@spp.coop</span> o <span style="color:red">soporte@d-spp.org</span></p>
                            </td>
                          </tr>
                        </tbody>
                      </table>
              </body>
              </html>
        ';

        if(!empty($registros['contacto1_email'])){
          $token = strtok($registros['contacto1_email'], "\/\,\;");
          while ($token !== false)
          {
            $mail->AddAddress($token);
            $token = strtok('\/\,\;');
          }

        }
        if(!empty($registros['contacto2_email'])){
          $token = strtok($registros['contacto2_email'], "\/\,\;");
          while ($token !== false)
          {
            $mail->AddAddress($token);
            $token = strtok('\/\,\;');
          }

        }
        if(!empty($registros['adm1_email'])){
          $token = strtok($registros['adm1_email'], "\/\,\;");
          while ($token !== false)
          {
            $mail->AddAddress($token);
            $token = strtok('\/\,\;');
          }

        }
        if(!empty($registros['adm2_email'])){
          $token = strtok($registros['adm2_email'], "\/\,\;");
          while ($token !== false)
          {
            $mail->AddAddress($token);
            $token = strtok('\/\,\;');
          }

        }

        $mail->Subject = utf8_decode($asunto);
        $mail->Body = utf8_decode($cuerpo_mensaje);
        $mail->MsgHTML(utf8_decode($cuerpo_mensaje));
        $mail->Send();
        $mail->ClearAddresses();


      }
    }


    /// notificación 2º aviso
    if(!$registros['aviso2'] && $registros['estatus_comprobante'] != 'ACEPTADO'){
      if($fecha_actual >= $recordatorio2){

        $query = "UPDATE comprobante_pago SET aviso2 = 1 WHERE idcomprobante_pago = $registros[idcomprobante_pago]";
        $updateSQL = mysql_query($query, $dspp) or die(mysql_error());

        $asunto = "D-SPP | 2do recordatorio pago Membresía SPP (2nd reminder payment SPP Membership)";
        $cuerpo_mensaje = '
              <html>
              <head>
                <meta charset="utf-8">
              </head>
              <body>
                      <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px;" border="0" width="650px">
                        <tbody>
                          <tr>
                            <th rowspan="1" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                            <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">2do recordatorio pago Membresía SPP (<i style="color:re#34495e">2nd reminder payment SPP Membership</i>)</span></p></th>

                          </tr>


                          <tr>
                            <td colspan="2" style="text-align:justify">
                              <p>
                                Estimados Representantes de : <span style="color:red">'.$registros['nombre_opp'].'</span> - ('.$registros['abreviacion_opp'].')
                              </p>
                              <p>
                                En seguimiento a la notificación de su dictamen positivo SPP, enviado con fecha  '.$formato_dictamen.' se les recuerda que tienen un plazo máximo de 30 días posterior a la fecha de notificación para realizar el pago correspondiente a <span style="color:red">'.$registros['monto'].'</span> por Membresía SPP y posteriormente cargar el comprobante de pago en el D-SPP.  
                              </p>
                              <p>
                                El no pagar la Membresía SPP oportunamente es un incumplimiento con el Marco Regulatorio, por lo tanto si el sistema no detecta un comprobante de pago, automáticamente enviará  la suspensión del Certificado.
                              </p>
                              <p>
                                 Por lo anterior, les solicitamos de la manera más atenta se proceda a realizar el pago a la mayor brevedad para evitar ser suspendidos.
                              </p>
                            </td>
                          </tr>
                          <tr>
                            <td colspan="2" style="padding-top:2em;">
                              <p>Para cualquier duda o aclaración por favor escribir a: <span style="color:red">cert@spp.coop</span> o <span style="color:red">soporte@d-spp.org</span></p>
                            </td>
                          </tr>

                          <tr style="color: #797979;">
                            <td style="padding-top:2em;">
                              <b>English Below</b>
                              <hr>
                            </td>
                          </tr>
                          <tr style="color: #797979;text-align:justify">
                            <td colspan="2">
                              <p>
                                Dear Representatives of: <span style="color:red">'.$registros['nombre_opp'].'</span> - ('.$registros['abreviacion_opp'].')
                              </p>
                              <p>
                                Following the notification of their positive opinion SPP, sent on '.$formato_dictamen.' they are reminded that they have a maximum period of 30 days after the date of notification to make the payment corresponding to <span style="color:red">'.$registros['monto'].'</span> per Membership SPP and later Load the proof of payment into the D-SPP.  
                              </p>
                              <p>
                                Failure to pay the SPP Membership in a timely manner is a breach of the Regulatory Framework, therefore if the system does not detect a payment receipt, it will automatically send the suspension of the Certificate.
                              </p>
                              <p>
                                 For the above, we ask you in the most careful way to proceed to make the payment as soon as possible to avoid being suspended.
                              </p>
                            </td>
                          </tr>

                          <tr>
                            <td colspan="2" style="padding-top:2em;">
                              <p>For any doubt or clarification please write to: <span style="color:red">cert@spp.coop</span> o <span style="color:red">soporte@d-spp.org</span></p>
                            </td>
                          </tr>
                        </tbody>
                      </table>
              </body>
              </html>
        ';

        if(!empty($registros['contacto1_email'])){
          $token = strtok($registros['contacto1_email'], "\/\,\;");
          while ($token !== false)
          {
            $mail->AddAddress($token);
            $token = strtok('\/\,\;');
          }

        }
        if(!empty($registros['contacto2_email'])){
          $token = strtok($registros['contacto2_email'], "\/\,\;");
          while ($token !== false)
          {
            $mail->AddAddress($token);
            $token = strtok('\/\,\;');
          }

        }
        if(!empty($registros['adm1_email'])){
          $token = strtok($registros['adm1_email'], "\/\,\;");
          while ($token !== false)
          {
            $mail->AddAddress($token);
            $token = strtok('\/\,\;');
          }

        }
        if(!empty($registros['adm2_email'])){
          $token = strtok($registros['adm2_email'], "\/\,\;");
          while ($token !== false)
          {
            $mail->AddAddress($token);
            $token = strtok('\/\,\;');
          }

        }
        $mail->Subject = utf8_decode($asunto);
        $mail->Body = utf8_decode($cuerpo_mensaje);
        $mail->MsgHTML(utf8_decode($cuerpo_mensaje));
        $mail->Send();
        $mail->ClearAddresses();

      }
    }

    /// notificación 3º aviso
    if(!$registros['aviso3'] && $registros['estatus_comprobante'] != 'ACEPTADO'){
      if($fecha_actual >= $alerta_suspension){

        $query = "UPDATE comprobante_pago SET aviso3 = 1 WHERE idcomprobante_pago = $registros[idcomprobante_pago]";
        $updateSQL = mysql_query($query, $dspp) or die(mysql_error());
        
        $asunto = "D-SPP | Alerta de Suspensión (Suspension Alert)";
        $cuerpo_mensaje = '
              <html>
              <head>
                <meta charset="utf-8">
              </head>
              <body>
                      <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px;" border="0" width="650px">
                        <tbody>
                          <tr>
                            <th rowspan="1" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                            <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">Alerta de Suspensión (<i style="color:#34495e">Suspension Alert</i>)</span></p></th>

                          </tr>


                          <tr>
                            <td colspan="2" style="text-align:justify">
                              <p>
                                Estimados Representantes de : <span style="color:red">'.$registros['nombre_opp'].'</span> - ('.$registros['abreviacion_opp'].')
                              </p>
                              <p>
                                En seguimiento a la notificación de su dictamen positivo SPP, enviado con fecha '.$formato_dictamen.' y a los recordatorios enviados posteriormente, se les informa que concluido el plazo máximo de 30 días, se enviara automáticamente la Suspensión del Certificado.
                              </p>
                              <p>
                                El importe de su Membresía SPP  es de <span style="color:red">'.$registros['monto'].'</span>.
                              </p>
                              <p>
                                 Cabe mencionar que es necesario cargar el comprobante de pago para que no se genere la suspensión automáticamente.
                              </p>
                            </td>
                          </tr>
                          <tr>
                            <td colspan="2" style="padding-top:2em;">
                              <p>Para cualquier duda o aclaración por favor escribir a: <span style="color:red">cert@spp.coop</span> o <span style="color:red">soporte@d-spp.org</span></p>
                            </td>
                          </tr>

                          <tr style="color: #797979;">
                            <td style="padding-top:2em;">
                              <b>English Below</b>
                              <hr>
                            </td>
                          </tr>
                          <tr style="color: #797979;text-align:justify">
                            <td colspan="2">
                              <p>
                                Dear Representatives of: <span style="color:red">'.$registros['nombre_opp'].'</span> - ('.$registros['abreviacion_opp'].')
                              </p>
                              <p>
                                Following the notification of their positive opinion SPP, sent on '.$formato_dictamen.' and the reminders sent later, they are informed that after the maximum period of 30 days, the Suspension of the Certificate will be sent automatically.  
                              </p>
                              <p>
                                The amount of your SPP Membership is <span style="color:red">'.$registros['monto'].'</span>.
                              </p>
                              <p>
                                 It is worth mentioning that it is necessary to load the proof of payment so that the suspension is not generated automatically.
                              </p>
                            </td>
                          </tr>

                          <tr>
                            <td colspan="2" style="padding-top:2em;">
                              <p>For any doubt or clarification please write to: <span style="color:red">cert@spp.coop</span> o <span style="color:red">soporte@d-spp.org</span></p>
                            </td>
                          </tr>
                        </tbody>
                      </table>
              </body>
              </html>
        ';

        if(!empty($registros['contacto1_email'])){
          $token = strtok($registros['contacto1_email'], "\/\,\;");
          while ($token !== false)
          {
            $mail->AddAddress($token);
            $token = strtok('\/\,\;');
          }

        }
        if(!empty($registros['contacto2_email'])){
          $token = strtok($registros['contacto2_email'], "\/\,\;");
          while ($token !== false)
          {
            $mail->AddAddress($token);
            $token = strtok('\/\,\;');
          }

        }
        if(!empty($registros['adm1_email'])){
          $token = strtok($registros['adm1_email'], "\/\,\;");
          while ($token !== false)
          {
            $mail->AddAddress($token);
            $token = strtok('\/\,\;');
          }

        }
        if(!empty($registros['adm2_email'])){
          $token = strtok($registros['adm2_email'], "\/\,\;");
          while ($token !== false)
          {
            $mail->AddAddress($token);
            $token = strtok('\/\,\;');
          }

        }
        $mail->AddBCC("cert@spp.coop");
        $mail->AddBCC("adm@spp.coop");
        $mail->Subject = utf8_decode($asunto);
        $mail->Body = utf8_decode($cuerpo_mensaje);
        $mail->MsgHTML(utf8_decode($cuerpo_mensaje));
        $mail->Send();
        $mail->ClearAddresses();

      }
    }
    /// Se envia el correo a los administradores para suspender a la organización
    if(($registros['aviso3'] && !$registros['notificacion_suspender'] && $registros['estatus_comprobante'] != 'ACEPTADO' && !$registros['archivo']) && ($fecha_actual >= $correo_suspender)){
      //echo '<p style="color:red">ENVIADO</p>';
      $query = "UPDATE comprobante_pago SET notificacion_suspender = 1 WHERE idcomprobante_pago = $registros[idcomprobante_pago]";
      $updateSQL = mysql_query($query, $dspp) or die(mysql_error());
      
      $asunto = "D-SPP | Suspender Organización";
      $cuerpo_mensaje = '
            <html>
            <head>
              <meta charset="utf-8">
            </head>
            <body>
              <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px;" border="0" width="650px">
                  <tr>
                    <th rowspan="1" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                    <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">Suspender Organización por falta de pago de la membresía SPP</span></p></th>

                  </tr>


                  <tr>
                    <td colspan="2" style="text-align:justify">
                      <p>
                        Organización: <span style="color:red">'.$registros['nombre_opp'].'</span> - ('.$registros['abreviacion_opp'].')
                      </p>
                      <p>
                        En seguimiento a la notificación del dictamen positivo SPP y de acuerdo al plazo máximo de 30 días se han enviado los recordatorios y la alerta de suspensión.
                      </p>
                      <p>
                        Al no haber detectado un comprobante de pago de la membresía SPP cargado dentro del sistema D-SPP, el sistema solicita que ingrese al sistema para poder enviar la suspensión de dicha organización.
                      </p>
                      <p>
                        A continuación se muestra una tabla con información relevante:
                      </p>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2">
                      <style>
                        table.tabla1, td.tabla1, th.tabla1 {    
                            border: 1px solid #ddd;
                            text-align: left;
                        }

                        table.tabla1 {
                            border-collapse: collapse;
                            font-size: 11px;
                            width: 100%;
                        }

                        th.tabla1, td.tabla1 {
                            padding: 5px;
                        }
                      </style>
                      <table class="tabla1">
                        <tr class="tabla1">
                          <th style="text-align:center;" class="tabla1">País</th>
                          <th style="text-align:center;" class="tabla1">Organización</th>
                          <th style="text-align:center;" class="tabla1">Fecha Dictamen</th>
                          <th style="text-align:center;" class="tabla1">Monto Membresía</th>
                          <th style="text-align:center;" class="tabla1" colspan="3">Fecha Mensajes</th>
                          
                        </tr>
                        <tr class="tabla1">
                          <td class="tabla1">'.$registros['pais'].'</td>
                          <td class="tabla1">'.$registros['nombre_opp'].'(<span style="color:red">'.$registros['abreviacion_opp'].'</span>)</td>
                          <td class="tabla1">'.date('d/m/Y',$registros['fecha_dictamen']).'</td>
                          <td class="tabla1">'.$registros['monto'].'</td>
                          <td class="tabla1">1º Recordatorio<br>'.date('d/m/Y', $recordatorio1).'</td>
                          <td class="tabla1">2º Recordatorio<br>'.date('d/m/Y', $recordatorio2).'</td>
                          <td class="tabla1">Alerta<br>'.date('d/m/Y', $alerta_suspension).'</td>
                          
                        </tr>
                      </table>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2">
                      <p>Pasos para suspender una organización:</p>
                      <ol>
                        <li>Debes ingresar en tu cuenta de administrador.</li>
                        <li>Debes seleccionar la opción <span style="color:red;">"Membresias"</span>.</li>
                        <li>Localizar la fila con el nombre de la Organización.</li>
                        <li>Dar clic en la opción de <span style="color:red">"Suspender"</span>.</li>
                      </ol>
                    </td>
                  </tr>
              </table>
            </body>
            </html>
      ';

      $mail->AddAddress("cert@spp.coop");
      $mail->AddAddress("adm@spp.coop");
      $mail->Subject = utf8_decode($asunto);
      $mail->Body = utf8_decode($cuerpo_mensaje);
      $mail->MsgHTML(utf8_decode($cuerpo_mensaje));
      $mail->Send();
      $mail->ClearAddresses();
    }
  

  } /// termina while
 ?>