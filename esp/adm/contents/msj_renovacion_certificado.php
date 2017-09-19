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
  $destinatario_opp = "";
  $asunto = "";
  $nombre_opp = '';
  $abreviacion_opp = '';
  $fecha_vigencia = '';


  // consultamos la información de la OPP de acuerdo al certificado
  //$row_opp = mysql_query("SELECT opp.idopp, opp.spp, opp.nombre, opp.abreviacion, opp.password, opp.email, opp.pais, MAX(certificado.idcertificado) AS 'idcertificado' FROM opp INNER JOIN certificado ON opp.idopp = certificado.idopp INNER JOIN oc ON certificado.entidad = oc.idoc GROUP BY certificado.idopp", $dspp) or die(mysql_error());


  $row_certificado = mysql_query("SELECT opp.idopp, opp.spp, opp.nombre, opp.abreviacion, opp.password, opp.email, opp.pais, certificado.idcertificado, certificado.entidad, certificado.vigencia_inicio, certificado.vigencia_fin, oc.email1 AS 'oc_email1', oc.email2 AS 'oc_email2' FROM certificado INNER JOIN opp ON certificado.idopp = opp.idopp INNER JOIN oc ON certificado.entidad = oc.idoc WHERE certificado.vigencia_inicio LIKE '%".$anio_actual."%'", $dspp) or die(mysql_error());

  while($certificado = mysql_fetch_assoc($row_certificado)){ /// INICIA WHILE 1
    // consultamos lo contactos registrados del OPP
    $row_contactos = mysql_query("SELECT contactos.email1, contactos.email2 FROM contactos WHERE contactos.idopp = $certificado[idopp] GROUP BY email1", $dspp) or die(mysql_error());
    $contactos = mysql_fetch_assoc($row_contactos);

    ///revisamos si se han enviado avisos de renovación
    $row_aviso_renovacion = mysql_query("SELECT * FROM avisos_renovacion WHERE idcertificado = $certificado[idcertificado] ORDER BY avisos_renovacion.idaviso_renovacion", $dspp) or die(mysql_error());
    $aviso_renovacion = mysql_fetch_assoc($row_aviso_renovacion);

    // variables generales
      $fecha_vigencia = date('d-m-Y', strtotime($certificado['vigencia_fin']));
      $nombre_opp = $certificado['nombre'];
      $abreviacion_opp = $certificado['abreviacion'];
      $vigencia_final = $certificado['vigencia_fin'];
      //revisamos el año del ultimo aviso de renovacion
      $anio_aviso = $aviso_renovacion['ano_aviso'];
    //

    //convertimos la fecha de vigencia mas reciente que obtenemos
    $time_vencimiento = strtotime($certificado['vigencia_fin']);
    $primer_aviso = $time_vencimiento - $primero;
    $segundo_aviso = $time_vencimiento - $segundo;
    $tercer_aviso = $time_vencimiento;
    $cuarto_aviso = $time_vencimiento + $cuarto;
    // restamos la (fecha de vigencia - la fecha actual) para saber CUANTO TIEMPO NOS QUEDA
    $time_restante = ($time_vencimiento - $time_actual);
    $estatus_certificado = "";
    $plazo = $time_vencimiento - $time_actual; //calculamos los dias (60) de diferencia que debemos tener para empezar a enviar las notificaciones

    $prorroga = ($time_vencimiento + $plazo); //sumamos la fecha de vigencia + el plazo para que puedan renovar que se fija

    if($plazo <= $primero){ /// se valida el envio de los primeros 2 aviso
      if(($plazo <= $primero && $plazo > $segundo) && !isset($aviso_renovacion['idaviso_renovacion'])){ // se valida el envio del PRIMER AVISO
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
        $mail->AddBCC("cert@spp.coop");
        $mail->AddBCC("adm@spp.coop");
        $mail->AddBCC("com@spp.coop");

        // Definimos el mensaje general que se utilizara en el 1º, 2º, 3º aviso
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
                   
                      <p>Estimados Representantes de <strong style="color:red">'.$nombre_opp.', (<u>'.$abreviacion_opp.'</u>)</strong>:</p>
                      
                      <p>Por este conducto se les informa la necesidad de renovación de su Certificado SPP. La fecha de su vigencia de su certificado spp es <strong style="color:red">'.$fecha_vigencia.'</strong>, por lo que deben proceder con la evaluación anual.</p>
                      
                      <p>De acuerdo a los procedimientos del SPP, se puede llevar a cabo la evaluación un mes antes de la fecha de vigencia o máximo un mes después.  Si la evaluación se realiza un mes después, se esperaría que el dictamen se obtuviera 4 meses después  (de la fecha de vencimiento del certificado) como plazo máximo, para obtener el dictamen positivo de parte del Organismo de Certificación.</p>
                    
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
                      <p>According to the SPP procedures, the evaluation can be carried out one month before the effective date or maximum one month later. If the evaluation is carried out one month later, it would be expected that the opinion would be obtained 4 months later (from the expiration date of the certificate) as a maximum term, to obtain a positive opinion from the Certification Body</p>
                    
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
        $mail->Body = utf8_decode($mensaje_general);
        $mail->MsgHTML(utf8_decode($mensaje_general));
        $mail->Send();
        $mail->ClearAddresses();

        $insertSQL = sprintf("INSERT INTO avisos_renovacion(idopp, aviso1, ano_aviso, idcertificado, fecha_certificado) VALUES(%s, %s, %s, %s, %s)",
            GetSQLValueString($certificado['idopp'], "int"),
            GetSQLValueString($time_actual, "int"),
            GetSQLValueString($anio_actual, "text"),
            GetSQLValueString($certificado['idcertificado'], "int"),
            GetSQLValueString($certificado['vigencia_fin'], "text"));
        $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
      }else if(($plazo <= $segundo && $plazo >= 0) && !isset($aviso_renovacion['aviso2'])){ // se valida el envio del SEGUNDO AVISO
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
        $mail->AddBCC("cert@spp.coop");
        $mail->AddBCC("adm@spp.coop");
        $mail->AddBCC("com@spp.coop");

        // Definimos el mensaje general que se utilizara en el 1º, 2º, 3º aviso
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
                   
                      <p>Estimados Representantes de <strong style="color:red">'.$nombre_opp.', (<u>'.$abreviacion_opp.'</u>)</strong>:</p>
                      
                      <p>Por este conducto se les informa la necesidad de renovación de su Certificado SPP. La fecha de su vigencia de su certificado spp es <strong style="color:red">'.$fecha_vigencia.'</strong>, por lo que deben proceder con la evaluación anual.</p>
                      
                      <p>De acuerdo a los procedimientos del SPP, se puede llevar a cabo la evaluación un mes antes de la fecha de vigencia o máximo un mes después.  Si la evaluación se realiza un mes después, se esperaría que el dictamen se obtuviera 4 meses después  (de la fecha de vencimiento del certificado) como plazo máximo, para obtener el dictamen positivo de parte del Organismo de Certificación.</p>
                    
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
                      <p>According to the SPP procedures, the evaluation can be carried out one month before the effective date or maximum one month later. If the evaluation is carried out one month later, it would be expected that the opinion would be obtained 4 months later (from the expiration date of the certificate) as a maximum term, to obtain a positive opinion from the Certification Body</p>
                    
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
        $mail->Body = utf8_decode($mensaje_general);
        $mail->MsgHTML(utf8_decode($mensaje_general));
        $mail->Send();
        $mail->ClearAddresses();

        $updateSQL = sprintf("UPDATE avisos_renovacion SET aviso2 = %s WHERE idaviso_renovacion = %s",                GetSQLValueString($time_actual, "int"),
          GetSQLValueString($aviso_renovacion['idaviso_renovacion'], "text"));
        $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

      }
    } if(($time_actual >= $cuarto_aviso) && !isset($aviso_renovacion['aviso4'])){ /// se valida el envio del 4º aviso
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
      $mail->AddBCC("cert@spp.coop");
      $mail->AddBCC("adm@spp.coop");
      $mail->AddBCC("com@spp.coop");

      // Definimos el mensaje general que se utilizara en el 1º, 2º, 3º aviso
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
                 
                    <p>Estimados Representantes de <strong style="color:red">'.$nombre_opp.', (<u>'.$abreviacion_opp.'</u>)</strong>:</p>
                    
                    <p>Por este conducto se les informa la necesidad de renovación de su Certificado SPP. La fecha de su vigencia de su certificado spp es <strong style="color:red">'.$fecha_vigencia.'</strong>, por lo que deben proceder con la evaluación anual.</p>
                    
                    <p>De acuerdo a los procedimientos del SPP, se puede llevar a cabo la evaluación un mes antes de la fecha de vigencia o máximo un mes después.  Si la evaluación se realiza un mes después, se esperaría que el dictamen se obtuviera 4 meses después  (de la fecha de vencimiento del certificado) como plazo máximo, para obtener el dictamen positivo de parte del Organismo de Certificación.</p>
                  
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
                    <p>According to the SPP procedures, the evaluation can be carried out one month before the effective date or maximum one month later. If the evaluation is carried out one month later, it would be expected that the opinion would be obtained 4 months later (from the expiration date of the certificate) as a maximum term, to obtain a positive opinion from the Certification Body</p>
                  
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
      $mail->Body = utf8_decode($mensaje_general);
      $mail->MsgHTML(utf8_decode($mensaje_general));
      $mail->Send();
      $mail->ClearAddresses();

      $updateSQL = sprintf("UPDATE avisos_renovacion SET aviso4 = %s WHERE idaviso_renovacion = %s",                GetSQLValueString($time_actual, "int"),
        GetSQLValueString($aviso_renovacion['idaviso_renovacion'], "text"));
      $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

    }

  }
 ?>