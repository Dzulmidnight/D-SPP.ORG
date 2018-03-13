<?php 
  $time_actual = time();
  $anio_actual = date('Y', time());
  $time_vencimiento = "";
  $time_restante = "";
  $contador = 1;
  // periodos en los que se enviaran las notificaciones
  $plazo = ''; // Calculamos el número de segundos que tienen 60 dias 
  $primero = 5.184e+6; // 60 dias
  $segundo = 2.592e+6; // 30 dias
  $tercer_aviso = ""; // igual a la vigencia final del certificado
  $cuarto = 2.592e+6; // 30 dias despues del plazo
    //$time_vencimiento = strtotime("2016-02-12");
    //$time_vencimiento = strtotime(); // Obtenemos timestamp de la fecha de vencimiento
   // $time_restante = ($time_vencimiento - $time_actual);
  $destinatario_empresa = "";
  $asunto = "";
  $nombre_empresa = '';
  $abreviacion_empresa = '';
  $fecha_vigencia = '';

  $direccion_spp = "adm@spp.coop";
  $asistencia_spp = "opera@spp.coop";
  $certificacion_spp = "cert@spp.coop";
  $finanzas_spp = "com@spp.coop";

  // consultamos la información de la EMPRESA de acuerdo al certificado

  $row_certificado = mysql_query("SELECT empresa.idempresa, empresa.spp, empresa.nombre, empresa.abreviacion, empresa.password, empresa.email, empresa.pais, certificado.idcertificado, certificado.entidad, certificado.vigencia_inicio, certificado.vigencia_fin, oc.email1 AS 'oc_email1', oc.email2 AS 'oc_email2' FROM certificado INNER JOIN empresa ON certificado.idempresa = empresa.idempresa INNER JOIN oc ON certificado.entidad = oc.idoc WHERE certificado.vigencia_inicio LIKE '%2017%' OR certificado.vigencia_inicio LIKE '%2018%' OR certificado.idcertificado = 118 ORDER BY certificado.vigencia_fin ASC", $dspp) or die(mysql_error());

  ?>
  <table class="table table-bordered" style="font-size:10px;">
    <thead>
      <tr>
        <th class="success" colspan="13">
          <h5>Listado Avisos de Renovación del Certificado</h5>
        </th>
        <th class="warning" colspan="3">
          <h5>Fecha actual: <?php echo date('d/m/Y',time()); ?></h5>
        </th>
      </tr>

      <tr>
        <th>ID CERTIFICADO</th>
        <th>ID EMPRESA</th>
        <th>EMPRESA</th>
        <th>FECHA INICIO</th>
        <th>FECHA FIN</th>
        <th>ID AVISO</th>
        <th>1º AVISO</th>
        <th>enviado 1</th>
        <th>2º AVISO</th>
        <th>enviado 2</th>
        <th>3º AVISO</th>
        <th>enviado 3</th>
        <th>4º AVISO</th>
        <th>enviado 4</th>
        <th>Suspender</th>
        <th>SUSPENSIÓN</th>
      </tr>
    </thead>
    <tbody>
      <?php 
      while($certificado = mysql_fetch_assoc($row_certificado)){
        $fecha_inicio = date('d/m/Y',strtotime($certificado['vigencia_inicio']));
        $fecha_fin = date('d/m/Y',strtotime($certificado['vigencia_fin']));

        // consultamos lo contactos registrados de la EMPRESA
        $row_contactos = mysql_query("SELECT contactos.email1, contactos.email2 FROM contactos WHERE contactos.idempresa = $certificado[idempresa] GROUP BY email1", $dspp) or die(mysql_error());
        $contactos = mysql_fetch_assoc($row_contactos);

        ///revisamos si se han enviado avisos de renovación
        $row_aviso_renovacion = mysql_query("SELECT * FROM avisos_renovacion WHERE idcertificado = $certificado[idcertificado] ORDER BY avisos_renovacion.idaviso_renovacion", $dspp) or die(mysql_error());
        $aviso_renovacion = mysql_fetch_assoc($row_aviso_renovacion);


      // variables generales
        $fecha_vigencia = date('d-m-Y', strtotime($certificado['vigencia_fin']));
        $nombre_empresa = $certificado['nombre'];
        $abreviacion_empresa = $certificado['abreviacion'];
        $vigencia_final = $certificado['vigencia_fin'];
        //revisamos el año del ultimo aviso de renovacion
        $anio_aviso = $aviso_renovacion['ano_aviso'];

        //convertimos la fecha de vigencia mas reciente que obtenemos
        $time_vencimiento = strtotime($certificado['vigencia_fin']);
        $primer_aviso = $time_vencimiento - $primero;
        $segundo_aviso = $time_vencimiento - $segundo;
        $tercer_aviso = $time_vencimiento;
        $cuarto_aviso = $time_vencimiento + $cuarto;
        // restamos la (fecha de vigencia - la fecha actual) para saber CUANTO TIEMPO NOS QUEDA
        $estatus_certificado = "";
      ?>
        <tr>
          <!-- ID CERTIFICADO -->
          <td><?php echo $certificado['idcertificado']; ?></td>
          <!-- ID EMPRESA -->
          <td><?php echo $certificado['idempresa']; ?></td>
          <!-- ABREVIACIÓN EMPRESA -->
          <td>
            <?php echo $certificado['abreviacion']; ?>
          </td>
          <!-- FECHA INICIO -->
          <td>
            <?php echo $fecha_inicio; ?>
          </td>
          <!-- FECHA FIN -->
          <td class="danger">
            <?php echo $fecha_fin; ?>
          </td>
          <!-- ID AVISO -->
          <td><?php echo $aviso_renovacion['idaviso_renovacion']; ?></td>
          <!-- 1º AVISO -->
          <td>
            <?php echo date('d/m/Y',$primer_aviso); ?>
          </td>
          <td>
            <?php 
                    if(!isset($aviso_renovacion['idaviso_renovacion']) || !isset($aviso_renovacion['aviso1'])){
                      if($time_actual >= $primer_aviso){
                        
                        $asunto = "1er Aviso de Renovación del Certificado / 1st Certificate Renewal Notice";

                        if(!empty($certificado['email'])){
                          $token = strtok($certificado['email'], "\/\,\;");
                          while($token !== false){   
                            $mail->AddAddress($token);
                            $token = strtok('\/\,\;');
                          }
                        }
                        if(!empty($contactos['email1'])){
                          $token = strtok($contactos['email1'], "\/\,\;");
                          while($token !== false){
                            $mail->AddAddress($token);
                            $token = strtok('\/\,\;');
                          }
                        }
                        if(!empty($contactos['email2'])){
                          $token = strtok($contactos['email2'], "\/\,\;");
                          while($token !== false){
                            $mail->AddAddress($token);
                            $token = strtok('\/\,\;');
                          }
                        }
                        if(!empty($certificado['oc_email1'])){
                          $token = strtok($certificado['oc_email1'], "\/\,\;");
                          while($token !== false){
                            $mail->AddAddress($token);
                            $token = strtok('\/\,\;');
                          }
                        }
                        if(!empty($certificado['oc_email2'])){
                          $token = strtok($certificado['oc_email2'], "\/\,\;");
                          while($token !== false){
                            $mail->AddAddress($token);
                            $token = strtok('\/\,\;');
                          }
                        }
                  $mail->AddBCC($certificacion_spp);
                  $mail->AddBCC($direccion_spp);
                  $mail->AddBCC($finanzas_spp);
                  $mail->AddBCC($asistencia_spp);



                        // Definimos el mensaje general que se utilizara en el 1º aviso
                        $mensaje_general = '
                          <html>
                            <head>
                              <meta charset="utf-8">
                            </head>
                            <body>
                              <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
                                <tbody>
                                  <tr>
                                    <th rowspan="1" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                                    <th scope="col" align="left" width="500"><strong><h3>'.$asunto.'</h3></strong></th>
                                  </tr>
                                  <tr>
                                    <td style="text-align:justify; padding-top:2em" colspan="2">
                                   
                                      <p>Estimados Representantes de <strong style="color:red">'.$nombre_empresa.', (<u>'.$abreviacion_empresa.'</u>)</strong>:</p>
                                      
                                      <p>Por este conducto se les informa la necesidad de renovación de su Registro SPP. La fecha de su vigencia de su registro spp es <strong style="color:red">'.$fecha_vigencia.'</strong>, por lo que deben proceder con la evaluación anual.</p>
                                      
                                      <p>De acuerdo a los procedimientos del SPP, se puede llevar a cabo la evaluación un mes antes de la fecha de vigencia o máximo un mes después.  Si la evaluación se realiza un mes después, se esperaría que el dictamen se obtuviera 4 meses después  (de la fecha de vencimiento del registro) como plazo máximo, para obtener el dictamen positivo de parte del Organismo de Certificación.</p>
                                    
                                      <p>Queremos enfatizar que actualmente existen políticas para la suspensión y/o cancelación del registro por lo que si ustedes no solicitan a tiempo pueden ser acreedores de una suspensión.</p>
                                      
                                      <p>
                                        A continuación se muestra su <b>#SPP y su contraseña, necesarios para poder iniciar sesión</b>: <a href="http://d-spp.org/esp/?COM" target="_new">www.d-spp.org/esp/?COM</a></i>
                                      </p>
                                      <p>
                                        <b>Usuario(#SPP) / User: </b> <span style="color:#27ae60;">'.$certificado['spp'].'</span>
                                        <br>
                                        <b>Contraseña / Password:</b> <span style="color:#27ae60;">'.$certificado['password'].'</span>
                                      </p>

                                      <p>Agradeciendo su atención, nos despedimos y enviamos saludos del SPP GLOBAL.</p>

                                      <p style="color:#2c3e50"><b>En caso de haber iniciado ya su proceso de renovación del registro por favor hacer caso omiso a este mensaje</b></p>
                                      
                                      <p>CUALQUIER INCONVENIENTE FAVOR DE NOTIFICARLO A SPP GLOBAL AL CORREO <strong>cert@spp.coop</strong></p>
                                    </td>
                                  </tr>

                                  <tr>
                                    <td style="text-align:justify; padding-top:2em" colspan="2">
                                      <p>Dear <strong style="color:red">'.$nombre_empresa.', (<u>'.$abreviacion_empresa.'</u>)</strong> Representatives</p>
                                      <p>In this way, you are informed of the need to renew your SPP Registry. The effective date of your spp registration is: <strong style="color:red">'.$fecha_vigencia.'</strong>, so you must proceed with the annual evaluation.</p>
                                      <p>According to the SPP procedures, the evaluation can be carried out one month before the effective date or maximum one month later. If the evaluation is carried out one month later, it would be expected that the opinion would be obtained 4 months later (from the expiration date of the certificate) as a maximum term, to obtain a positive opinion from the Certification Body</p>
                                    
                                      <p>We want to emphasize that currently there are policies for the suspension and / or cancellation of the registration so that if you do not apply on time you can be liable for a suspension.</p>
                                      
                                      <p>
                                        Below is your <b>#SPP and password needed to log in </b>: <a href="http://d-spp.org/en/?COM" target="_new">www.d-spp.org/en/?COM</a></i>.
                                      </p>
                                      <p>
                                        <b>Usuario(#SPP) / User: </b> <span style="color:#27ae60;">'.$certificado['spp'].'</span>
                                        <br>
                                        <b>Contraseña / Password:</b> <span style="color:#27ae60;">'.$certificado['password'].'</span>
                                      </p>


                                      <p>Thank you for your attention, we said goodbye and we send greetings from SPP GLOBAL.</p>

                                      <p style="color:#2c3e50"><b>If you have already started your registration renewal process please ignore this message</b></p>
                                      
                                      <p>ANY INCONVENIENT PLEASE NOTICE TO SPP GLOBAL TO THE MAIL <strong>cert@spp.coop</strong></p>
                                    </td>
                                  </tr>
                                </tbody>
                              </table>
                            </body>
                          </html>
                        ';

                        $mail->Subject = utf8_decode($asunto);
                        $mail->Body = utf8_decode($mensaje_general);
                        $mail->MsgHTML(utf8_decode($mensaje_general));
                        $mail->Send();
                        $mail->ClearAddresses();

                        if(isset($aviso_renovacion['idaviso_renovacion'])){
                          $updateSQL = sprintf("UPDATE avisos_renovacion SET aviso1 = %s WHERE idaviso_renovacion = %s",
                            GetSQLValueString($time_actual, "int"),
                            GetSQLValueString($aviso_renovacion['idaviso_renovacion'], "text"));
                          $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());
                        }else{
                          $insertSQL = sprintf("INSERT INTO avisos_renovacion(idempresa, aviso1, ano_aviso, idcertificado, fecha_certificado) VALUES(%s, %s, %s, %s, %s)",
                            GetSQLValueString($certificado['idempresa'], "int"),
                            GetSQLValueString($time_actual, "int"),
                            GetSQLValueString($anio_actual, "text"),
                            GetSQLValueString($certificado['idcertificado'], "int"),
                            GetSQLValueString($certificado['vigencia_fin'], "text"));
                          $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
                        }
                      }
                    }else{
                      echo date('d/m/Y',$aviso_renovacion['aviso1']);
                    }
             ?>

          </td>
          <!-- 2º AVISO -->
          <td>
            <?php echo date('d/m/Y',$segundo_aviso); ?>
          </td>
          <td>
            <?php 
                    if(!isset($aviso_renovacion['aviso2'])){
                      if($time_actual >= $segundo_aviso){
                        $asunto = "2do Aviso de Renovación del Certificado / 2nd Certificate Renewal Notice";

                        if(!empty($certificado['email'])){
                          $token = strtok($certificado['email'], "\/\,\;");
                          while($token !== false){
                            $mail->AddAddress($token);
                            $token = strtok('\/\,\;');
                          }
                        }
                        if(!empty($contactos['email1'])){
                          $token = strtok($contactos['email1'], "\/\,\;");
                          while($token !== false){
                            $mail->AddAddress($token);
                            $token = strtok('\/\,\;');
                          }
                        }
                        if(!empty($contactos['email2'])){
                          $token = strtok($contactos['email2'], "\/\,\;");
                          while($token !== false){
                            $mail->AddAddress($token);
                            $token = strtok('\/\,\;');
                          }
                        }
                        if(!empty($certificado['oc_email1'])){
                          $token = strtok($certificado['oc_email1'], "\/\,\;");
                          while($token !== false){
                            $mail->AddAddress($token);
                            $token = strtok('\/\,\;');
                          }
                        }
                        if(!empty($certificado['oc_email2'])){
                          $token = strtok($certificado['oc_email2'], "\/\,\;");
                          while($token !== false){
                            $mail->AddAddress($token);
                            $token = strtok('\/\,\;');
                          }
                        }
                  $mail->AddBCC($certificacion_spp);
                  $mail->AddBCC($direccion_spp);
                  $mail->AddBCC($finanzas_spp);
                  $mail->AddBCC($asistencia_spp);

                        // Definimos el mensaje general que se utilizara en el 2º aviso
                        $mensaje_general = '
                          <html>
                            <head>
                              <meta charset="utf-8">
                            </head>
                            <body>
                              <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
                                <tbody>
                                  <tr>
                                    <th rowspan="1" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                                    <th scope="col" align="left" width="500"><strong><h3>'.$asunto.'</h3></strong></th>
                                  </tr>
                                  <tr>
                                    <td style="text-align:justify; padding-top:2em" colspan="2">
                                   
                                      <p>Estimados Representantes de <strong style="color:red">'.$nombre_empresa.', (<u>'.$abreviacion_empresa.'</u>)</strong>:</p>
                                      
                                      <p>Por este conducto se les informa la necesidad de renovación de su Registro SPP. La fecha de su vigencia de su registro spp es <strong style="color:red">'.$fecha_vigencia.'</strong>, por lo que deben proceder con la evaluación anual.</p>
                                      
                                      <p>De acuerdo a los procedimientos del SPP, se puede llevar a cabo la evaluación un mes antes de la fecha de vigencia o máximo un mes después.  Si la evaluación se realiza un mes después, se esperaría que el dictamen se obtuviera 4 meses después  (de la fecha de vencimiento del registro) como plazo máximo, para obtener el dictamen positivo de parte del Organismo de Certificación.</p>
                                    
                                      <p>Queremos enfatizar que actualmente existen políticas para la suspensión y/o cancelación del registro por lo que si ustedes no solicitan a tiempo pueden ser acreedores de una suspensión.</p>
                                      
                                      <p>Agradeciendo su atención, nos despedimos y enviamos saludos del SPP GLOBAL.</p>

                                      <p style="color:#2c3e50"><b>En caso de haber iniciado ya su proceso de renovación del registro por favor hacer caso omiso a este mensaje</b></p>
                                      
                                      <p>CUALQUIER INCONVENIENTE FAVOR DE NOTIFICARLO A SPP GLOBAL AL CORREO <strong>cert@spp.coop</strong></p>
                                    </td>
                                  </tr>

                                  <tr>
                                    <td style="text-align:justify; padding-top:2em" colspan="2">
                                      <p>Dear <strong style="color:red">'.$nombre_empresa.', (<u>'.$abreviacion_empresa.'</u>)</strong> Representatives</p>
                                      <p>In this way, you are informed of the need to renew your SPP Registry. The effective date of your spp registration is: <strong style="color:red">'.$fecha_vigencia.'</strong>, so you must proceed with the annual evaluation.</p>
                                      <p>According to the SPP procedures, the evaluation can be carried out one month before the effective date or maximum one month later. If the evaluation is carried out one month later, it would be expected that the opinion would be obtained 4 months later (from the expiration date of the certificate) as a maximum term, to obtain a positive opinion from the Certification Body</p>
                                    
                                      <p>We want to emphasize that currently there are policies for the suspension and / or cancellation of the registration so that if you do not apply on time you can be liable for a suspension.</p>
                                      
                                      <p>Thank you for your attention, we said goodbye and we send greetings from SPP GLOBAL.</p>

                                      <p style="color:#2c3e50"><b>If you have already started your registration renewal process please ignore this message</b></p>
                                      
                                      <p>ANY INCONVENIENT PLEASE NOTICE TO SPP GLOBAL TO THE MAIL <strong>cert@spp.coop</strong></p>
                                    </td>
                                  </tr>
                                </tbody>
                              </table>
                            </body>
                          </html>
                        ';

                        $mail->Subject = utf8_decode($asunto);
                        $mail->Body = utf8_decode($mensaje_general);
                        $mail->MsgHTML(utf8_decode($mensaje_general));
                        $mail->Send();
                        $mail->ClearAddresses();

                        $updateSQL = sprintf("UPDATE avisos_renovacion SET aviso2 = %s WHERE idaviso_renovacion = %s",
                          GetSQLValueString($time_actual, "int"),
                          GetSQLValueString($aviso_renovacion['idaviso_renovacion'], "text"));
                        $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

                      }
                    }else{
                      echo date('d/m/Y', $aviso_renovacion['aviso2']);
                    }
             ?>
          </td>
          <!-- 3º AVISO -->
          <td>
            <?php echo date('d/m/Y',$tercer_aviso); ?>
          </td>
          <td>
            <?php 
                    if(!isset($aviso_renovacion['aviso3'])){
                      if($time_actual >= $tercer_aviso){
                        
                        $asunto = "3er Aviso de Renovación del Certificado - Alerta de suspensión / 3rd Certificate Renewal Notice - Suspension alert";

                        if(!empty($certificado['email'])){
                          $token = strtok($certificado['email'], "\/\,\;");
                          while($token !== false){
                            $mail->AddAddress($token);
                            $token = strtok('\/\,\;');
                          }
                        }
                        if(!empty($contactos['email1'])){
                          $token = strtok($contactos['email1'], "\/\,\;");
                          while($token !== false){
                            $mail->AddAddress($token);
                            $token = strtok('\/\,\;');
                          }
                        }
                        if(!empty($contactos['email2'])){
                          $token = strtok($contactos['email2'], "\/\,\;");
                          while($token !== false){
                            $mail->AddAddress($token);
                            $token = strtok('\/\,\;');
                          }
                        }
                        if(!empty($detalle_certificado['oc_email1'])){
                          $token = strtok($detalle_certificado['oc_email1'], "\/\,\;");
                          while($token !== false){
                            $mail->AddAddress($token);
                            $token = strtok('\/\,\;');
                          }
                        }
                        if(!empty($detalle_certificado['oc_email2'])){
                          $token = strtok($detalle_certificado['oc_email2'], "\/\,\;");
                          while($token !== false){
                            $mail->AddAddress($token);
                            $token = strtok('\/\,\;');
                          }
                        }
                  $mail->AddBCC($certificacion_spp);
                  $mail->AddBCC($direccion_spp);
                  $mail->AddBCC($finanzas_spp);
                  $mail->AddBCC($asistencia_spp);

                        // Definimos el mensaje para el 3º aviso
                        $mensaje_general = '
                          <html>
                            <head>
                              <meta charset="utf-8">
                            </head>
                            <body>
                              <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
                                <tbody>
                                  <tr>
                                    <th rowspan="1" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                                    <th scope="col" align="left" width="500"><strong><h3>'.$asunto.'</h3></strong></th>
                                  </tr>
                                  <tr>
                                    <td style="text-align:justify; padding-top:2em" colspan="2">
                                      <p>Estimados Representantes de <strong style="color:red">'.$nombre_empresa.', (<u>'.$abreviacion_empresa.'</u>)</strong>:</p>

                                      <p>
                                        De acuerdo a los avisos de renovación del registro enviados con anterioridad y de acuerdo a los procedimientos del sistema SPP se hace un gentil recordatorio que el plazo máximo para iniciar la evaluación es un mes después de la vigencia de su registro (<strong style="color:red">'.$fecha_vigencia.'</strong>). Concluido el mes, el sistema digital D-SPP procederá a enviar la <span style="color:red">suspensión de su registro</span>.
                                      </p>
                                      <p>
                                        Una vez que es emitida la suspensión la suspensión del registro no podrá levantarse la misma hasta concluir el proceso de registro con un <span style="color:red">dictamen positivo</span>.
                                      </p>
                                      <p>
                                        Una de las <span style="color:red">consecuencias</span> de la suspensión es que <span style="color:red">no pueden celebrar nuevos contratos</span> bajo la certificación SPP.
                                      </p>
                                      <p>
                                        Necesariamente deben de iniciar su proceso de renovación a travez del sistema digital D-SPP (<a href="http://d-spp.org/">http://d-spp.org/</a>).
                                      </p>

                                      <p>
                                        <b style="color:red">Para completar su solicitud de renovación de registro, debe completar los siguientes pasos:</b>
                                      </p>
                                      <ol>
                                        <li>Ingresar en la dirección <a href="http://d-spp.org/">http://d-spp.org/</a>.</li>
                                        <li>Seleccionar el idioma en el que desea utilizar el sistema.</li>
                                        <li>Después de seleccionar el idioma, debe seleccionar la opción "Empresas" o dar clic en el siguiente link <a href="http://d-spp.org/esp/?COM">Español</a> o en <a href="http://d-spp.org/en/?COM">Ingles</a></li>
                                        <li>Debe de iniciar sesión con su usuario(#SPP): <span style="color:#27ae60">'.$certificado['spp'].'</span> y su contraseña: <span style="color:#27ae60">'.$certificado['password'].'</span></li>
                                        <li>Una vez que ha iniciado sesión debe seleccionar la opción "Solicitudes" > "Nueva Solicitud"</li>
                                        <li>Después de realizar esos pasos se mostrara la Solicitud electronica donde deberá completar la información correspondiente y al finalizar dar clic en "Enviar Solicitud".</li>
                                        <li>Después de enviar la solicitud, el Organismo de Certificación correspondiente le enviara la cotización por medio del sistema, la cual también le llegara a los correos dados de alta en la solicitud.</li>
                                      </ol>
                                      
                                      <p>Agradeciendo su atención, nos despedimos y enviamos saludos por parte del equipo de SPP GLOBAL.</p>

                                      <p style="color:#2c3e50"><b>En caso de haber iniciado ya su proceso de renovación del certificado por favor hacer caso omiso a este mensaje</b></p>
                                      
                                      <p>CUALQUIER INCONVENIENTE FAVOR DE NOTIFICARLO A SPP GLOBAL AL CORREO <strong>cert@spp.coop</strong></p>
                                    </td>
                                  </tr>
                                  <tr style="font-style:italic">
                                    <td colspan="2" style="padding-top:10px;">
                                      <h3>
                                        <b style="color:#000">English Below</b>
                                      </h3>
                                      <hr>
                                      <p>Dear Representatives of: <strong style="color:red">'.$nombre_empresa.', (<u>'.$abreviacion_empresa.'</u>)</strong>:</p>

                                      <p>
                                        According to the registration renewal notices sent previously and according to the procedures of the SPP system, a gentle reminder is made that the maximum period for starting the evaluation is one month after the date of registration (<strong style="color:red">'.$fecha_vigencia.'</strong>). At the end of the month, the D-SPP digital system will proceed to send the <span style="color:red">suspension of its registration</span>.
                                      </p>
                                      <p>
                                        Once the suspension is issued, the suspension of registration can not be lifted until the registration process is concluded with a positive <span style="color:red">positive opinion</span>.
                                      </p>
                                      <p>
                                        One of the <span style="color:red">consequences</span> of the suspension is that they can not enter into new contracts under the SPP certification.
                                      </p>
                                      <p>
                                        You must necessarily start your renewal process through the D-SPP digital system (<a href="http://d-spp.org/">http://d-spp.org/</a>).
                                      </p>

                                      <p>
                                        <b style="color:red">In order to complete your registration renewal application, you must complete the following steps:</b>
                                      </p>
                                      <ol>
                                        <li>
                                          Enter at <a href="http://d-spp.org/">http://d-spp.org/</a>.
                                        </li>
                                        <li>
                                          Select the language in which you want to use the system.
                                        </li>
                                        <li>
                                          After selecting the language, you must select the option "Companies" or click on the following link <a href="http://d-spp.org/esp/?COM">Spanish</a> or <a href="http://d-spp.org/en/?COM">English</a>.
                                        </li>
                                        <li>
                                          You must login with your user (#SPP): <span style="color:#27ae60">'.$certificado['spp'].'</span> and your password: <span style="color:#27ae60">'.$certificado['password'].'</span>
                                        </li>
                                        <li>
                                          Once you have logged in you must select the "Applications"> "New application"
                                        </li>
                                        <li>
                                          After completing these steps, the Electronic Application will be displayed, where you will have to fill in the corresponding information and click "Send Application".
                                        </li>
                                        <li>
                                          After sending the application, the corresponding Certification Entity will send the quotation through the system, which will also reach the emails given in the application.
                                        </li>
                                      </ol>
                                      
                                      <p>Thank you for your attention, we said goodbye and we send greetings from the team of SPP GLOBAL.</p>

                                      <p style="color:#2c3e50"><b>If you have already started your certificate renewal process please ignore this message</b></p>
                                      
                                      <p>ANY INCONVENIENT PLEASE NOTIFY SPP GLOBAL TO THE MAIL: <strong>cert@spp.coop</strong></p>
                                    </td>
                                  </tr>
                                </tbody>
                              </table>
                            </body>
                          </html>
                        ';


                        $mail->Subject = utf8_decode($asunto);
                        $mail->Body = utf8_decode($mensaje_general);
                        $mail->MsgHTML(utf8_decode($mensaje_general));
                        $mail->Send();
                        $mail->ClearAddresses();

                        $updateSQL = sprintf("UPDATE avisos_renovacion SET aviso3 = %s WHERE idaviso_renovacion = %s",
                          GetSQLValueString($time_actual, "int"),
                          GetSQLValueString($aviso_renovacion['idaviso_renovacion'], "text"));
                        $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

                      }
                    }else{
                      echo date('d/m/Y', $aviso_renovacion['aviso3']);
                    }
             ?>
          </td>
          <!-- 4º AVISO -->
          <td>
            <?php echo date('d/m/Y',$cuarto_aviso); ?>
          </td>
          <td>
            <?php 
                    if(!isset($aviso_renovacion['aviso4'])){
                      if($time_actual >= $cuarto_aviso){
                        $asunto = "Suspensión del certificado / Suspension of certificate";

                        if(!empty($certificado['email'])){
                          $token = strtok($certificado['email'], "\/\,\;");
                          while($token !== false){
                            $mail->AddAddress($token);
                            $token = strtok('\/\,\;');
                          }
                        }
                        if(!empty($contactos['email1'])){
                          $token = strtok($contactos['email1'], "\/\,\;");
                          while($token !== false){
                            $mail->AddAddress($token);
                            $token = strtok('\/\,\;');
                          }
                        }
                        if(!empty($contactos['email2'])){
                          $token = strtok($contactos['email2'], "\/\,\;");
                          while($token !== false){
                            $mail->AddAddress($token);
                            $token = strtok('\/\,\;');
                          }
                        }
                        if(!empty($certificado['oc_email1'])){
                          $token = strtok($certificado['oc_email1'], "\/\,\;");
                          while($token !== false){
                            $mail->AddAddress($token);
                            $token = strtok('\/\,\;');
                          }
                        }
                        if(!empty($certificado['oc_email2'])){
                          $token = strtok($certificado['oc_email2'], "\/\,\;");
                          while($token !== false){
                            $mail->AddAddress($token);
                            $token = strtok('\/\,\;');
                          }
                        }
                  $mail->AddBCC($certificacion_spp);
                  $mail->AddBCC($direccion_spp);
                  $mail->AddBCC($finanzas_spp);
                  $mail->AddBCC($asistencia_spp);

                        // Definimos el mensaje general que se utilizara en el 4º aviso
                        $mensaje_general = '
                          <html>
                            <head>
                              <meta charset="utf-8">
                            </head>
                            <body>
                              <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
                                <tbody>
                                  <tr>
                                    <th rowspan="1" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                                    <th scope="col" align="left" width="500"><strong><h3>'.$asunto.'</h3></strong></th>
                                  </tr>
                                  <tr>
                                    <td style="text-align:justify; padding-top:2em" colspan="2">
                                   
                                      <p>Estimados Representantes de <strong style="color:red">'.$nombre_empresa.', (<u>'.$abreviacion_empresa.'</u>)</strong>:</p>
                                      
                                      <p>Por este conducto se les informa la necesidad de renovación de su Registro SPP. La fecha de su vigencia de su registro spp es <strong style="color:red">'.$fecha_vigencia.'</strong>, por lo que deben proceder con la evaluación anual.</p>
                                      
                                      <p>De acuerdo a los procedimientos del SPP, se puede llevar a cabo la evaluación un mes antes de la fecha de vigencia o máximo un mes después.  Si la evaluación se realiza un mes después, se esperaría que el dictamen se obtuviera 4 meses después  (de la fecha de vencimiento del registro) como plazo máximo, para obtener el dictamen positivo de parte del Organismo de Certificación.</p>
                                    
                                      <p>Queremos enfatizar que actualmente existen políticas para la suspensión y/o cancelación del registro por lo que si ustedes no solicitan a tiempo pueden ser acreedores de una suspensión.</p>
                                      
                                      <p>Agradeciendo su atención, nos despedimos y enviamos saludos del SPP GLOBAL.</p>

                                      <p style="color:#2c3e50"><b>En caso de haber iniciado ya su proceso de renovación del registro por favor hacer caso omiso a este mensaje</b></p>
                                      
                                      <p>CUALQUIER INCONVENIENTE FAVOR DE NOTIFICARLO A SPP GLOBAL AL CORREO <strong>cert@spp.coop</strong></p>
                                    </td>
                                  </tr>

                                  <tr>
                                    <td style="text-align:justify; padding-top:2em" colspan="2">
                                      <p>Dear <strong style="color:red">'.$nombre_empresa.', (<u>'.$abreviacion_empresa.'</u>)</strong> Representatives</p>
                                      <p>In this way, you are informed of the need to renew your SPP Registry. The effective date of your spp registration is: <strong style="color:red">'.$fecha_vigencia.'</strong>, so you must proceed with the annual evaluation.</p>
                                      <p>According to the SPP procedures, the evaluation can be carried out one month before the effective date or maximum one month later. If the evaluation is carried out one month later, it would be expected that the opinion would be obtained 4 months later (from the expiration date of the certificate) as a maximum term, to obtain a positive opinion from the Certification Body</p>
                                    
                                      <p>We want to emphasize that currently there are policies for the suspension and / or cancellation of the registration so that if you do not apply on time you can be liable for a suspension.</p>
                                      
                                      <p>Thank you for your attention, we said goodbye and we send greetings from SPP GLOBAL.</p>

                                      <p style="color:#2c3e50"><b>If you have already started your registration renewal process please ignore this message</b></p>
                                      
                                      <p>ANY INCONVENIENT PLEASE NOTICE TO SPP GLOBAL TO THE MAIL <strong>cert@spp.coop</strong></p>
                                    </td>
                                  </tr>
                                </tbody>
                              </table>
                            </body>
                          </html>
                        ';

                        $mail->Subject = utf8_decode($asunto);
                        $mail->Body = utf8_decode($mensaje_general);
                        $mail->MsgHTML(utf8_decode($mensaje_general));
                        $mail->Send();
                        $mail->ClearAddresses();

                        $updateSQL = sprintf("UPDATE avisos_renovacion SET aviso4 = %s WHERE idaviso_renovacion = %s",
                          GetSQLValueString($time_actual, "int"),
                          GetSQLValueString($aviso_renovacion['idaviso_renovacion'], "text"));
                        $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

                      }
                    }else{
                      echo date('d/m/Y', $aviso_renovacion['aviso4']);
                    }
             ?>
          </td>
          <!-- SUSPENSIÓN -->
          <td>
            <?php 
            if(isset($aviso_renovacion['suspender'])){
              echo date('d/m/Y', $aviso_renovacion['suspender']);
            }
             ?>
          </td>
          <td>
          <?php 
          if(!empty($aviso_renovacion['aviso4']) && empty($aviso_renovacion['suspender'])){
          ?>
            <form action="" method="POST">
              <!-- Button trigger modal -->
              <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="<?php echo '#modalSuspender'.$aviso_renovacion['idaviso_renovacion']; ?>">
                <span class="glyphicon glyphicon-ban-circle" aria-hidden="true"></span> Suspender
              </button>

              <!-- Modal -->
              <div class="modal fade" id="<?php echo 'modalSuspender'.$aviso_renovacion['idaviso_renovacion']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog modal-lg" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      <h4 class="modal-title" id="myModalLabel">SUSPENDER ORGANIZACIÓN</h4>
                    </div>
                    <div class="modal-body" style="font-size:12px;">
                      <p>
                        Se procedera a suspender a la organización: <?php echo '<span style="color:red">'.$certificado['nombre'].'</span> ('.$certificado['abreviacion'].')'; ?>
                      </p>
                      <div class="form-group has-error">
                        <label class="control-label" for="motivo_suspension">A continuación debe de justificar el motivo de la suspensión de la organización:</label>
                        <textarea class="form-control" name="motivo_suspension" id="motivo_suspension" cols="5" placeholder="Escribir el motivo de la suspensión" required></textarea>
                        <p>*Nota: El motivo de la suspensión solo podra ser revisado por los administradores de SPP Global.</p>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <input type="text" name="idopp" value="<?php echo $certificado['idopp']; ?>">
                      <input type="hidden" name="idaviso_renovacion" value="<?php echo $aviso_renovacion['idaviso_renovacion']; ?>">
                      <input type="hidden" name="idcertificado" value="<?php echo $certificado['idcertificado']; ?>">
                      <input type="hidden" name="spp" value="<?php echo $certificado['spp']; ?>">
                      <input type="hidden" name="nombre_opp" value="<?php echo $nombre_opp; ?>">
                      <input type="hidden" name="abreviacion_opp" value="<?php echo $abreviacion_opp; ?>">
                      <input type="hidden" name="nombre_oc" value="<?php echo $certificado['nombre_oc'] ?>">
                      <input type="hidden" name="fecha_vigencia" value="<?php echo $fecha_vigencia; ?>">


                      <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                      <button type="submit" class="btn btn-primary" name="enviar_suspension" onclick="return confirm('¿Desea continuar con la suspensión de la organización?');" value="1">Suspender Organización</button>
                    </div>
                  </div>
                </div>
              </div>

              <!--<button type="submit" name="enviar_suspension" value="1" class="btn btn-sm btn-danger"><span class="glyphicon glyphicon-ban-circle" aria-hidden="true"></span> Suspender</button>-->
            </form>
            
          <?php
          }else if(isset($aviso_renovacion['suspender'])){
            echo 'ORGANIZACIÓN SUSPENDIDA';
          }
           ?>         

          </td>
        </tr>

        <?php
      }
       ?>
    </tbody>
  </table>