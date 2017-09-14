<?php 
  function separarCorreo($correo){
    if(!empty($correo)){
      $token = strtok($correo, "\/\,\;");
      while($token !== false){
        $mail->AddAddress($token);
        $token = strtok('\/\,\;');
      }
    }
  }

  $time_actual = time();
  $anio_actual = date('Y', time());
  $time_vencimiento = "";
  $time_restante = "";
  $contador = 1;
  // periodos en los que se enviaran las notificaciones
  $plazo = 60 *(24*60*60); // Calculamos el número de segundos que tienen 60 dias 
  $primer_aviso = 5.184e+6; // 60 dias
  $segundo_aviso = 2.592e+6; // 30 dias
  $tercer_aviso = ""; // igual a la vigencia final del certificado
  $aviso_suspension = 2.592e+6; // 30 dias despues del plazo
    //$time_vencimiento = strtotime("2016-02-12");
    //$time_vencimiento = strtotime(); // Obtenemos timestamp de la fecha de vencimiento
   // $time_restante = ($time_vencimiento - $time_actual);
  $destinatario_opp = "";
  $asunto = "";
  // consultamos la información de la OPP de acuerdo al certificado
  $row_opp = mysql_query("SELECT opp.idopp, opp.spp, opp.nombre, opp.abreviacion, opp.password, opp.email, opp.pais, MAX(certificado.idcertificado) AS 'idcertificado' FROM opp INNER JOIN certificado ON opp.idopp = certificado.idopp INNER JOIN oc ON certificado.entidad = oc.idoc GROUP BY certificado.idopp", $dspp) or die(mysql_error());

  while($opp = mysql_fetch_assoc($row_opp)){ /// INICIA WHILE 1
    //consultamos la información del certificado asi como los datos del OC
    $row_certificado = mysql_query("SELECT certificado.entidad, certificado.vigencia_fin, oc.email1 AS 'oc_email1', oc.email2 AS 'oc_email2' FROM certificado INNER JOIN oc ON certificado.entidad = oc.idoc WHERE idcertificado = $opp[idcertificado]", $dspp) or die(mysql_error());
    $detalle_certificado = mysql_fetch_assoc($row_certificado);

    // consultamos lo contactos registrados del OPP
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
    //

    //convertimos la fecha de vigencia mas reciente que obtenemos
    $time_vencimiento = strtotime($detalle_certificado['vigencia_fin']);
    // restamos la (fecha de vigencia - la fecha actual) para saber CUANTO TIEMPO NOS QUEDA
    $time_restante = ($time_vencimiento - $time_actual);
    $estatus_certificado = "";

    if($time_vencimiento >= $time_actual){ /// inicia IF 1
      //comparamos si el tiempo que nos queda es menor al plazo que tenemos, si es menor entrariamos a comparar los avisos de renovacion
      if($time_restante <= $plazo){ /// inicia IF 2
        $estatus_certificado = 14; // AVISO RENOVACIÓN (de acuerdo al estatus_dspp)
        if(!isset($aviso_renovacion['idaviso_renovacion'])){ // inicia IF 3
          $asunto = "1er Aviso de Renovación del Certificado / SPP Certificate Renewal Notice";

          separarCorreo($opp['email']);
          separarCorreo($contactos['email1']));
          separarCorreo($contactos['email2']));
          separarCorreo($detalle_certificado['oc_email1']);
          separarCorreo($detalle_certificado['oc_email2']);
          $mail->AddBCC("cert@spp.coop");
          $mail->AddBCC("adm@spp.coop");
          $mail->AddBCC("com@spp.coop");
        } // termina IF 3
      }/// termina IF 2
      else{ /// inicia ELSE 2

      } /// termina ELSE 2

    } /// termina IF 1


  } /// TERMINA WHILE 1

    while($opp = mysql_fetch_assoc($row_opp)){
      //$plazo_despues = ($time_vencimiento + $plazo); //sumamos la fecha de vigencia + el plazo para que puedan renovar que se fija
      $prorroga = ($time_vencimiento + $plazo); //sumamos la fecha de vigencia + el plazo para que puedan renovar que se fija
      /////INICIA IF 1
      if($time_actual <= $time_vencimiento){ //EJ: 20 de febrero <= 28 de febrero
        if($time_restante <= $plazo){ //comparamos si el tiempo que nos queda es menor al plazo que tenemos, si es menor entrariamos a comparar los avisos de renovacion


          //if(!isset($aviso_renovacion['aviso1']) || ($anio_aviso != $anio_actual)){ // revisamos si se ha enviado el PRIMER AVISO O si el aviso que existe se haya enviado en el año en curso(comparamos años)
          if(!isset($aviso_renovacion['idaviso_renovacion'])){  /// INIICIA IF AVISO RENOVACION
            $asunto = "D-SPP - Aviso de Renovacion de Certificado / SPP Certificate Renewal Notice"; 
            ///CORREOS A LOS QUE SE ENVIARA EL CORREO DE RENOVACIÓN
            /*if(!empty($opp['email'])){
              $token = strtok($opp['email'], "\/\,\;");
              while ($token !== false)
              {
                $mail->AddAddress($token);
                $token = strtok('\/\,\;');
              }

            }*/
            /*if(!empty($contactos['email1'])){
              $token = strtok($contactos['email1'], "\/\,\;");
              while ($token !== false)
              {
                $mail->AddAddress($token);
                $token = strtok('\/\,\;');
              }

            }*/
            /*if(!empty($contactos['email2'])){
              $token = strtok($contactos['email2'], "\/\,\;");
              while ($token !== false)
              {
                $mail->AddAddress($token);
                $token = strtok('\/\,\;');
              }

            }*/
            /*if(!empty($detalle_certificado['oc_email1'])){
              $token = strtok($detalle_certificado['oc_email1'], "\/\,\;");
              while ($token !== false)
              {
                $mail->AddAddress($token);
                $token = strtok('\/\,\;');
              }

            }*/
            /*if(!empty($detalle_certificado['oc_email2'])){
              $token = strtok($detalle_certificado['oc_email2'], "\/\,\;");
              while ($token !== false)
              {
                $mail->AddAddress($token);
                $token = strtok('\/\,\;');
              }

            }*/
            ///correos SPP GLOBAL con copia oculta
           /* $mail->AddBCC("cert@spp.coop");
            $mail->AddBCC("adm@spp.coop");
            $mail->AddBCC("com@spp.coop");*/

            //// MENSAJE 1er AVISO DE RENOVACIÓN DEL CERTIFICADO (2 MESES ANTES)
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
                      <th scope="col" align="left" width="500"><strong><h3>1er Aviso de Renovación de Certificado SPP</h3></strong></th>
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
              $token = strtok($opp['email'], "\/\,\;");
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
                      <th scope="col" align="left" width="500"><strong><h3>Aviso de Renovación de Certificado SPP / SPP Certificate Renewal Notice</h3></strong></th>
                    </tr>
                    <tr>
                      <td style="text-align:justify; padding-top:2em" colspan="2">
                     
                        <p>Estimados Representantes de <strong style="color:red">'.$nombre_opp.', (<u>'.$abreviacion_opp.'</u>)</strong>:</p>
                        
                        <p>Por este conducto se les informa la necesidad de renovación de su Certificado SPP. La fecha de su vigencia es <strong style="color:red">'.$fecha_vigencia.'</strong>, la cual se encuentra a punto de expirar.</p>
                        
                        <p>De acuerdo a los procedimientos del SPP, se puede llevar a cabo la evaluación un mes antes de la fecha de vigencia o máximo un meses después.  Si la evaluación se realiza <span style="color:red">un mes después</span>, se esperaría que el dictamen se obtuviera 4 meses <span style="color:red">después  (de la fecha de vencimiento del certificado) como plazo máximo</span>, para obtener el dictamen positivo de parte del Organismo de Certificación.</p>
                      
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


            //$mail->Username = "soporte@d-spp.org";
            //$mail->Password = "/aung5l6tZ";
            $mail->Subject = utf8_decode($asunto);
            $mail->Body = utf8_decode($cuerpo);
            $mail->MsgHTML(utf8_decode($cuerpo));
            $mail->Send();
            $mail->ClearAddresses();
          }

        }

      }
      $contador++;

    }
  //////////////////////// TERMINA SECCIÓN MENSAJES RENOVACÓN DEL CERTIFICADO ///////////////////////////
    ////////////////********************************** *****************************************************************//////////////////////////////////    
    ////////////////********************************** TERMINA RENOVACION DEL CERTIFICADO OPP **********************************//////////////////////////////////
    ////////////////********************************** *****************************************************************//////////////////////////////////


else{
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
            /*09_06_2017 $asunto = "D-SPP - Certificado Expirado"; 
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
            $mail->ClearAddresses();09_06_2017*/

          }
         // echo "<p style='color:#8e44ad'>CERTIIFICADO EXPIRADO $opp[idcertificado] - idopp: $opp[idopp]</p>";
        }
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
    ////////////////********************************** *****************************************************************//////////////////////////////////    
    ////////////////********************************** TERMINA RENOVACION DEL REGISTRO EMPRESAS **********************************//////////////////////////////////
    ////////////////********************************** *****************************************************************//////////////////////////////////

 ?>