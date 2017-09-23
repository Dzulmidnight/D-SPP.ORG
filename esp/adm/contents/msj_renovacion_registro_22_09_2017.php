<?php 
    ////////////////********************************** *****************************************************************//////////////////////////////////    
    ////////////////********************************** INICIA RENOVACION DEL REGISTRO EMPRESAS **********************************//////////////////////////////////
    ////////////////********************************** *****************************************************************//////////////////////////////////

    $row_empresa = mysql_query("SELECT empresa.idempresa, empresa.spp, empresa.nombre, empresa.abreviacion, empresa.password, empresa.email, empresa.pais, MAX(certificado.idcertificado) AS 'idcertificado' FROM empresa INNER JOIN certificado ON empresa.idempresa = certificado.idempresa INNER JOIN oc ON certificado.entidad = oc.idoc GROUP BY certificado.idempresa", $dspp) or die(mysql_error());

    while($empresa = mysql_fetch_assoc($row_empresa)){
      $row_certificado = mysql_query("SELECT certificado.entidad, certificado.vigencia_fin, oc.email1 AS 'oc_email1', oc.email2 AS 'oc_email2' FROM certificado INNER JOIN oc ON certificado.entidad = oc.idoc WHERE idcertificado = $empresa[idcertificado]", $dspp) or die(mysql_error());
      $detalle_certificado = mysql_fetch_assoc($row_certificado);

      $row_contactos = mysql_query("SELECT contactos.email1, contactos.email2 FROM contactos WHERE contactos.idempresa = $empresa[idempresa]", $dspp) or die(mysql_error());
      $contactos = mysql_fetch_assoc($row_empresa);

      ///revisamos si se han enviado avisos de renovación
      $row_aviso_renovacion = mysql_query("SELECT * FROM avisos_renovacion WHERE idempresa = $empresa[idempresa] AND ano_aviso = '$anio_actual' ORDER BY avisos_renovacion.idaviso_renovacion LIMIT 1", $dspp) or die(mysql_error());
      $aviso_renovacion = mysql_fetch_assoc($row_aviso_renovacion);
      // variables generales
      $fecha_vigencia = date('d-m-Y', strtotime($detalle_certificado['vigencia_fin']));
      $nombre_empresa = $empresa['nombre'];
      $abreviacion_empresa = $empresa['abreviacion'];
      $vigencia_final = $detalle_certificado['vigencia_fin'];
      //revisamos el año del ultimo aviso de renovacion
      $anio_aviso = $aviso_renovacion['ano_aviso'];


      /*if(isset($aviso_renovacion['idaviso_renovacion'])){
        echo "<p style='color:green'>#$contador - Si hay aviso de renovacion, idempresa: $empresa[idempresa]<br></p>";
      }else{
        echo "<p style='color:red'>#$contador - No hay aviso de renovacion, idempresa: $empresa[idempresa]<br></p>";
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
          $destinatario_empresa = "";
          //$row_oc = mysql_query("SELECT certificado.idcertificado, certificado.entidad, oc.* FROM certificado INNER JOIN oc ON certificado.entidad = oc.idoc WHERE idcertificado = $empresa[idcertificado]", $dspp) or die(mysql_error());

          //$oc = mysql_fetch_assoc($row_oc);


          //if(!isset($aviso_renovacion['aviso1']) || ($anio_aviso != $anio_actual)){ // revisamos si se ha enviado el PRIMER AVISO O si el aviso que existe se haya enviado en el año en curso(comparamos años)
          if(!isset($aviso_renovacion['idaviso_renovacion'])){  /// INIICIA IF AVISO RENOVACION
            $asunto = "D-SPP - Aviso de Renovacion de Registro /  Registration Renewal Notice"; 
            ///CORREOS A LOS QUE SE ENVIARA EL CORREO DE RENOVACIÓN
            if(!empty($empresa['email'])){
              $token = strtok($empresa['email'], "\/\,\;");
              while ($token !== false)
              {
                $mail->AddAddress($token);
                $token = strtok('\/\,\;');
              }

            }
            if(!empty($contactos['email1'])){
              $token = strtok($contactos['email1'], "\/\,\;");
              while ($token !== false)
              {
                $mail->AddAddress($token);
                $token = strtok('\/\,\;');
              }

            }
            if(!empty($contactos['email2'])){
              $token = strtok($contactos['email2'], "\/\,\;");
              while ($token !== false)
              {
                $mail->AddAddress($token);
                $token = strtok('\/\,\;');
              }

            }
            if(!empty($detalle_certificado['oc_email1'])){
              $token = strtok($detalle_certificado['oc_email1'], "\/\,\;");
              while ($token !== false)
              {
                $mail->AddAddress($token);
                $token = strtok('\/\,\;');
              }

            }
            if(!empty($detalle_certificado['oc_email2'])){
              $token = strtok($detalle_certificado['oc_email2'], "\/\,\;");
              while ($token !== false)
              {
                $mail->AddAddress($token);
                $token = strtok('\/\,\;');
              }

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
                     
                        <p>Estimados Representantes de <strong style="color:red">'.$nombre_empresa.', (<u>'.$abreviacion_empresa.'</u>)</strong>:</p>
                        
                        <p>Por este conducto se les informa la necesidad de renovación de su Registro SPP. La fecha de su vigencia de su Registro SPP es <strong style="color:red">'.$fecha_vigencia.'</strong>, por lo que deben proceder con la evaluación anual.</p>
                        
                        <p>De acuerdo a los procedimientos del SPP, se puede llevar a cabo la evaluación un mes antes de la fecha de vigencia o máximo un mes después.  Si la evaluación se realiza un mes después, se esperaría que el dictamen se obtuviera 4 meses después  (de la fecha de vencimiento del Registro) como plazo máximo, para obtener el dictamen positivo de parte del Organismo de Certificación.</p>
                      
                        <p>Queremos enfatizar que actualmente existen políticas para la suspensión y/o cancelación del Registro por lo que si ustedes no solicitan a tiempo pueden ser acreedores de una suspensión.</p>
                        
                        <p>Agradeciendo su atención, nos despedimos y enviamos saludos del SPP GLOBAL.</p>

                        <p style="color:#2c3e50"><b>En caso de haber iniciado ya su proceso de renovación del registro por favor hacer caso omiso a este mensaje</b></p>
                        
                        <p>CUALQUIER INCONVENIENTE FAVOR DE NOTIFICARLO A SPP GLOBAL AL CORREO <strong>cert@spp.coop</strong></p>
                      
                      </td>
                    </tr>
                    <tr>
                      <th colspan="2" style="padding-top:5em;">
                        <b>English below</b>
                        <hr>
                      </th>
                    </tr>
                    <tr>
                      <th scope="col" align="left" width="500"><strong><h3>SPP Registration Renewal Notice</h3></strong></th>
                    </tr>
                    <tr>
                      <td style="text-align:justify; padding-top:2em" colspan="2">
                     
                        <p>Dear <strong style="color:red">'.$nombre_empresa.', (<u>'.$abreviacion_empresa.'</u>)</strong> Representatives</p>
                        
                        <p>You are hereby informed of the need for renewal of your SPP Registration. The effective date of your SPP Registration is: <strong style="color:red">'.$fecha_vigencia.'</strong>, so you must proceed with the annual evaluation.</p>
                        
                        <p>According to the SPP procedures, the evaluation can be carried out one month before the effective date or maximum one month later. If the evaluation is made a month later, it would be expected that the opinion would be obtained 4 months later (from the date of expiration of the Register) as a maximum term, in order to obtain a positive opinion from the Certification Body.</p>
                      
                        <p>We want to emphasize that there are currently policies for the suspension and / or cancellation of the Registration, so if you do not apply on time you may be entitled to a suspension.</p>
                        
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
            $mail->Body = utf8_decode($cuerpo);
            $mail->MsgHTML(utf8_decode($cuerpo));
            $mail->Send();
            $mail->ClearAddresses();

            $insertSQL = sprintf("INSERT INTO avisos_renovacion(idempresa, aviso1, ano_aviso, idcertificado, fecha_certificado) VALUES(%s, %s, %s, %s, %s)",
              GetSQLValueString($empresa['idempresa'], "int"),
              GetSQLValueString($time_actual, "int"),
              GetSQLValueString($anio_actual, "text"),
              GetSQLValueString($empresa['idcertificado'], "int"),
              GetSQLValueString($detalle_certificado['vigencia_fin'], "text"));
            $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
            //echo "empresa: $empresa[idempresa] - ";
          } // TERMINA IF AVISO RENOVACION
          //echo "<p style='color:green'>SE ENVIO CORREO id certificado: $empresa[idcertificado] - idempresa: $empresa[idempresa]</p>";

        }else{
          $estatus_certificado = 10; // CERTIFICADO ACTIVO
          //echo "<p style='color:blue'>DENTRO DE FECHA id certificado: $empresa[idcertificado] - idempresa: $empresa[idempresa]</p>";
        }
      }else{
       
        if($prorroga >= $time_actual){
          $estatus_certificado = 15; // CERTIFICADO POR EXPIRAR(segun estatus_dspp)
          if(!isset($aviso_renovacion['aviso2'])){
            //agregamos el aviso de renovacion
            if(!isset($aviso_renovacion['idaviso_renovacion'])){
              $insertSQL = sprintf("INSERT INTO avisos_renovacion(idempresa, aviso1, aviso2, ano_aviso, idcertificado, fecha_certificado) VALUES(%s, %s, %s, %s, %s, %s)",
                GetSQLValueString($empresa['idempresa'], "int"),
                GetSQLValueString($time_actual, "int"),
                GetSQLValueString($time_actual, "int"),
                GetSQLValueString($anio_actual, "text"),
                GetSQLValueString($empresa['idcertificado'], "int"),
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
            if(!empty($empresa['email'])){
              $token = strtok($empresa['email'], "\/\,\;");
              while ($token !== false)
              {
                $mail->AddAddress($token);
                $token = strtok('\/\,\;');
              }

            }
            if(!empty($contactos['email1'])){
              $token = strtok($contactos['email1'], "\/\,\;");
              while ($token !== false)
              {
                $mail->AddAddress($token);
                $token = strtok('\/\,\;');
              }

            }
            if(!empty($contactos['email2'])){
              $token = strtok($contactos['email2'], "\/\,\;");
              while ($token !== false)
              {
                $mail->AddAddress($token);
                $token = strtok('\/\,\;');
              }

            }
            if(!empty($detalle_certificado['oc_email1'])){
              $token = strtok($detalle_certificado['oc_email1'], "\/\,\;");
              while ($token !== false)
              {
                $mail->AddAddress($token);
                $token = strtok('\/\,\;');
              }

            }
            if(!empty($detalle_certificado['oc_email2'])){
              $token = strtok($detalle_certificado['oc_email2'], "\/\,\;");
              while ($token !== false)
              {
                $mail->AddAddress($token);
                $token = strtok('\/\,\;');
              }

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
                     
                        <p>Estimados Representantes de <strong style="color:red">'.$nombre_empresa.', (<u>'.$abreviacion_empresa.'</u>)</strong>:</p>
                        
                        <p>Por este conducto se les informa la necesidad de renovación de su Registro SPP. La fecha de su vigencia de su Registro SPP es <strong style="color:red">'.$fecha_vigencia.'</strong>, por lo que deben proceder con la evaluación anual.</p>
                        
                        <p>De acuerdo a los procedimientos del SPP, se puede llevar a cabo la evaluación un mes antes de la fecha de vigencia o máximo un mes después.  Si la evaluación se realiza un mes después, se esperaría que el dictamen se obtuviera 4 meses después  (de la fecha de vencimiento del Registro) como plazo máximo, para obtener el dictamen positivo de parte del Organismo de Certificación.</p>
                      
                        <p>Queremos enfatizar que actualmente existen políticas para la suspensión y/o cancelación del Registro por lo que si ustedes no solicitan a tiempo pueden ser acreedores de una suspensión.</p>
                        
                        <p>Agradeciendo su atención, nos despedimos y enviamos saludos del SPP GLOBAL.</p>

                        <p style="color:#2c3e50"><b>En caso de haber iniciado ya su proceso de renovación del registro por favor hacer caso omiso a este mensaje</b></p>
                        
                        <p>CUALQUIER INCONVENIENTE FAVOR DE NOTIFICARLO A SPP GLOBAL AL CORREO <strong>cert@spp.coop</strong></p>
                      
                      </td>
                    </tr>
                    <tr>
                      <th colspan="2" style="padding-top:5em;">
                        <b>English below</b>
                        <hr>
                      </th>
                    </tr>
                    <tr>
                      <th scope="col" align="left" width="500"><strong><h3>SPP Registration Renewal Notice</h3></strong></th>
                    </tr>
                    <tr>
                      <td style="text-align:justify; padding-top:2em" colspan="2">
                     
                        <p>Dear <strong style="color:red">'.$nombre_empresa.', (<u>'.$abreviacion_empresa.'</u>)</strong> Representatives</p>
                        
                        <p>You are hereby informed of the need for renewal of your SPP Registration. The effective date of your SPP Registration is: <strong style="color:red">'.$fecha_vigencia.'</strong>, so you must proceed with the annual evaluation.</p>
                        
                        <p>According to the SPP procedures, the evaluation can be carried out one month before the effective date or maximum one month later. If the evaluation is made a month later, it would be expected that the opinion would be obtained 4 months later (from the date of expiration of the Register) as a maximum term, in order to obtain a positive opinion from the Certification Body.</p>
                      
                        <p>We want to emphasize that there are currently policies for the suspension and / or cancellation of the Registration, so if you do not apply on time you may be entitled to a suspension.</p>
                        
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


            //$mail->Username = "soporte@d-spp.org";
            //$mail->Password = "/aung5l6tZ";
            $mail->Subject = utf8_decode($asunto);
            $mail->Body = utf8_decode($cuerpo);
            $mail->MsgHTML(utf8_decode($cuerpo));
            $mail->Send();
            $mail->ClearAddresses();


          }

          //echo "<p style='color:black'>CERTIFICADO POR EXPIRAR $opp[idcertificado] - idopp: $opp[idopp]</p>";
        }
 ?>