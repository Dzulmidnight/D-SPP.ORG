<?php 
  if(isset($_POST['enviar_suspension']) && $_POST['enviar_suspension'] == 1){
    $idcertificado = $_POST['idcertificado'];
    $spp = $_POST['spp'];
    $nombre_opp = $_POST['nombre_opp'];
    $abreviacion_opp = $_POST['abreviacion_opp'];
    $fecha_vigencia = $_POST['fecha_vigencia'];
    $nombre_oc = $_POST['nombre_oc'];
     ///// GENERAMOS EL FORMATO DE SUSPENSIÓN
      ///inician variables del PDF
      $ruta_pdf = '../../archivos/admArchivos/suspension/';
      $nombre_pdf = 'formato_suspension_certificado_'.time().'.pdf';
      $reporte = $ruta_pdf.$nombre_pdf;
      /// fin

      /// SE GENERA EL ARCHIVO PDF Y SE GUARDA EN EL SERVIDOR
      $html = '

        <table style="font-family: Tahoma, Geneva, sans-serif;font-size:12px;">
            <tr>
              <td>
                <h3>1 DATOS</h3>
              </td>
            </tr>
            <tr>
              <td>
                <table class="formatoTabla">
                  <tr>
                    <td>
                      <p>Tipo de Actor: <span style="color:red">OPP</span></p>
                    </td>
                    <td>
                      <p>Nº de Certificado: <span style="color:red">'.$certificado['idcertificado'].'</span></p>
                    </td>
                    <td rowspan="2">
                      <p>Fecha: <span style="color:red">'.date('d/m/Y', time()).'</span></p>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <p>Nombre de la instancia: <span style="color:red">'.$nombre_opp.'</span></p>
                    </td>
                    <td>
                      <p>Código de identificación SPP: <span style="color:red">'.$spp.'</span></p> 
                    </td>
                  </tr>
                  <tr>
                    <td colspan="3">
                      <p>Organismo de Certificación que otorgó el Certificado: <span style="color:red">'.$nombre_oc.'</span></p>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
            <tr>
              <td style="padding-top:3em;">
                <p>Estimados representantes de la Organización: <span style="color:red">'.$nombre_opp.'</span> ('.$abreviacion_opp.').</p>
              </td>
            </tr>
            <tr>
              <td style="padding-top:1em;">
                <p>Por medio de la presente se hace la notificación del aviso de Suspensión del Certificado por Incumplimiento con el Marco Regulatorio SPP de acuerdo a la siguiente información:</p>
              </td>
            </tr>
            <tr>
              <td style="padding-top:2em;">
                <h3>2 MOTIVO</h3>
              </td>
            </tr>
          </table>
            
          <table class="formatoTabla">
            <tr>
              <th width="5%"><p style="font-size:18px;">#</p></th>
              <th width="25%"><p style="font-size:18px;">Descripción de la No Conformidad</p></th>
              <th width="15%"><p style="font-size:18px;">Referencia</p></th>
              <th width="10%"><p style="font-size:18px;">Acción Correctiva</p></th>
              <th width="25%"><p style="font-size:18px;">Plazo<sup>1</sup></p></th>
              <th width="20%"><p style="font-size:18px;">Observaciones</p></th>
            </tr>
            <tr>
              <td>
                <p style="font-size:18px;">1</p>
              </td>
              <td class="justificado">
                <p style="font-size:18px;">
                  La <span style="color:red">OPP</span> no ha renovado su <span style="color:red">Certificado</span> SPP de acuerdo a lo establecido en el Procedimiento SPP que indica lo siguiente:
                </p>
                <p style="font-size:18px;">
                  “La OPP/empresa debe haber iniciado - mediante la aprobación formal de una oferta de certificación - la evaluación anual de certificación en el periodo entre un mes antes y un  mes después de la vigencia de su certificado.
                </p>
              </td>
              <td class="justificado" style="width:100px;">
                <p style="font-size:18px;">
                  Proc_Cert_OPP_SPP_V6_31-Jul-2016_E1_10-Mar-2017_Vf
                <br>
                  Procedimiento_Registro_Compradores_Finales_otros_actores_V6_31-Jul-2016_E1_10-Mar-2017
                </p>
              </td>
              <td>
                <p style="font-size:18px;">Llevar a cabo la evaluación para la renovación del Certificado.</p>
              </td>
              <td class="justificado">
                <p style="font-size:18px;">
                  De acuerdo al Procedimiento de certificación y registro, tienen 90 días naturales para llevar a cabo todo el proceso, incluido el tiempo que tiene el OC para responder.
                </p>
                <p style="font-size:18px;">
                  (Ver el plazo máximo para llevar a cabo los diferentes pasos del proceso de certificación en el Procedimiento de Certificación para OPP y Procedimiento de Registro para Compradores Finales y otros actores).
                </p>
              </td>
              <td class="justificado">
                <p style="font-size:18px;">
                  Si la OPP o empresa no envía la documentación para iniciar la evaluación sino hasta 20 días antes de concluir el plazo máximo, el OC ya no recibirá la información porque en dicho tiempo ya no se tendría oportunidad de llevar a cabo todas las etapas del proceso.
                </p>
              </td>
            </tr>
            <tr style="padding-top:2em;">
              <td colspan="6">
                <p style="font-size:18px;">
                  <sup>1</sup> A partir de la fecha de notificación del presente Aviso de Suspensión.    
                </p>
              </td>
            </tr>
          </table>

          <table>
            <tr>
              <td style="padding-top:2em;"><h3>3 CONSECUENCIAS DE LA SUSPENSIÓN DEL CERTIFICADO</h3></td>
            </tr>
            <tr>
              <td>
                <ol>
                  <li>No puede celebrar nuevos contratos comerciales SPP con algún operador certificado o registrado.</li>
                  <li>Debe cumplir con los contratos SPP ya celebrados vigentes.</li>
                  <li>Se mantiene en listas oficiales de empresas del SPP de FUNDEPPO con estatus ‘Suspendido’.</li>
                  <li>No se detienen los tiempos de los ciclos de registro en curso, es decir, se mantienen vigentes los tiempos establecidos para la renovación en el último certificado o registro.</li>
                </ol>
              </td>
            </tr>
            <tr>
              <td style="padding-top:2em;"><h3>4 LEVANTAMIENTO</h3></td>
            </tr>
            <tr>
              <td >
                <ol>
                  <li>
                    Se levanta la Suspensión cuando se declaren resueltos los motivos por los cuales se les determinó dicho estatus.
                  </li>
                  <li>
                    Adicionalmente deben pagarse eventuales adeudos pendientes, como el pago de membresía y cuota de uso SPP, en caso de que exista. 
                  </li>
                </ol>
              </td>
            </tr>
            <tr>
              <td style="padding-top:2em;">
                <h3 style="padding-top:2em;">5 INFORMACIÓN SOBRE CANCELACION</h3>
              </td>
            </tr>
            <tr>
              <td >
                <p>
                  <b>
                    Se debe tener en cuenta de que de no resolverse la suspensión en el plazo indicado, se emitirá la cancelación del Certificado o Registro que lleva a las siguientes consecuencias:
                  </b>
                </p>
              </td>
            </tr>
            <tr>
              <td>
                <ol>
                  <li>
                    No puede hacer transacciones nuevas en condiciones SPP.
                  </li>
                  <li>
                    Debe cumplir contratos SPP hechos, siempre y cuando se respete lo siguiente: Producto sujeto a contratos hechos cuando la entidad estaba aún certificada o registrada se puede vender en el mercado como SPP hasta máximo un año en el caso de productos de ciclo anual; hasta  6 meses en caso de producción bianual o hasta 3 meses en el caso de productos de producción constante
                  </li>
                  <li>
                    Debe reiniciar el proceso como solicitud de nuevo ingreso, sin poder aplicar al procedimiento acortado.
                  </li>
                  <li>
                    Deberá demostrar haber resuelto los motivos por los cuales el certificado fue cancelado.
                  </li>
                  <li>
                    El tiempo mínimo para solicitar nuevamente el registro es dos años después de la fecha de notificación de la cancelación.
                  </li>
                </ol>
              </td>
            </tr>
        </table>';


      $mpdf = new mPDF('c', 'Letter'); // seleccionamos el tamaño de la hoja
      ob_start();

      $mpdf->setAutoTopMargin = 'pad';
      $mpdf->keep_table_proportions = TRUE;
      $mpdf->SetHTMLHeader('
      <header class="clearfix">
        <div>
          <table class="formatoTabla" style="padding:0px;margin-top:-20px;">
            <tr>
              <td style="text-align:left;margin-bottom:0px;font-size:12px;">
                    <div>
                      <img src="../../img/mailFUNDEPPO.jpg">
                    </div>
              </td>
              <td style="text-align:right;font-size:12px;">
                    <div>
                      <h3>
                          Suspensión del Certificado
                      </h3>             
                    </div>

                    <div>Simbolo de Pequeños Productores</div>
                    <div>Versión 2.  18-Ago-2017</div>
              </td>
            </tr>
          </table>
        </div>
      </header>
        ');
      $css = file_get_contents('../../archivos/css/style_reporte.css');  
      //$mpdf->AddPage('L'); //se cambia la orientacion de la pagina
      $mpdf->pagenumPrefix = 'Página ';
      $mpdf->pagenumSuffix = ' - ';
      $mpdf->nbpgPrefix = ' de ';
      //$mpdf->nbpgSuffix = ' pages';
      $mpdf->SetHTMLFooter('
        <footer>
          <table>
            <tr>
              <td style="text-align:right;">
                <p>
                  Formato_Suspensión_Certificado_Registro_SPP_V2_2017-08-18
                </p>
              </td>
            </tr>
          </table>
        </footer>
          ');
      $mpdf->writeHTML($css,1);

      ob_end_clean();

      $mpdf->writeHTML($html);
      //$pdf_listo = $mpdf->Output('reporte.pdf', 'I');
      
      /// CON LA LINEA DE ABAJO GENERAMOS EL PDF Y LO ENVIAMOS POR EMAIL, PERO NO LO GUARDAMOS
      //28_03_2017 $pdf_listo = $mpdf->Output('reporte_trimestral.pdf', 'S'); //reemplazamos la I por S(regresa el documento como string)
      /// CON LA LINEA DE ABAJO GENERAMOS EL PDF Y LO GUARDAMOS EN UNA CARPETA
      $mpdf->Output(''.$ruta_pdf.''.$nombre_pdf.'', 'F'); //reemplazamos la I por S(regresa el documento como string)

      /// FIN


      /// SE GENERA EL CORREO
      $asunto = 'Suspensión del Certificado SPP';

      $mensaje_correo = '
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


      /*if(!empty($certificado['email'])){
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
      }*/

      $mail->AddAddress('soporteinforganic@gmail.com');

      /*$mail->AddBCC($certificacion_spp);
      $mail->AddBCC($direccion_spp);
      $mail->AddBCC($finanzas_spp);
      $mail->AddBCC($asistencia_spp);*/
      $mail->AddAttachment($reporte);
      $mail->Subject = utf8_decode($asunto);
      $mail->Body = utf8_decode($mensaje_correo);
      $mail->MsgHTML(utf8_decode($mensaje_correo));
      //$mail->AddAttachment($pdf_listo, 'reporte.pdf');
      //$mail->addStringAttachment($pdf_listo, 'reporte_trimestral.pdf'); // SE ENVIA LA CADENA DE TEXTO DEL PDF POR EMAIL
      $mail->Send();
      $mail->ClearAddresses();

  }

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

  $direccion_spp = "adm@spp.coop";
  $asistencia_spp = "opera@spp.coop";
  $certificacion_spp = "cert@spp.coop";
  $finanzas_spp = "com@spp.coop";

  //28_09_2017 $row_certificado = mysql_query("SELECT opp.idopp, opp.spp, opp.nombre, opp.abreviacion, opp.password, opp.email, opp.pais, certificado.idcertificado, certificado.entidad, certificado.vigencia_inicio, certificado.vigencia_fin, oc.email1 AS 'oc_email1', oc.email2 AS 'oc_email2' FROM certificado INNER JOIN opp ON certificado.idopp = opp.idopp INNER JOIN oc ON certificado.entidad = oc.idoc WHERE certificado.vigencia_inicio LIKE '%".$anio_actual."%' ORDER BY certificado.vigencia_fin DESC", $dspp) or die(mysql_error());

  $row_certificado = mysql_query("SELECT opp.idopp, opp.spp, opp.nombre, opp.abreviacion, opp.password, opp.email, opp.pais, certificado.idcertificado, certificado.entidad, certificado.vigencia_inicio, certificado.vigencia_fin, oc.nombre AS 'nombre_oc', oc.email1 AS 'oc_email1', oc.email2 AS 'oc_email2' FROM certificado INNER JOIN opp ON certificado.idopp = opp.idopp INNER JOIN oc ON certificado.entidad = oc.idoc ORDER BY certificado.vigencia_fin DESC", $dspp) or die(mysql_error());

?>
  <h4>
    Fecha actual: <?php echo date('d/m/Y', time()); ?>
  </h4>
  <table class="table table-bordered" style="font-size:10px;">
    <thead>
      <tr>
        <td>ID CERTIFICADO</td>
        <td>ID OPP</td>
        <td>ORGANIZACIÓN</td>
        <td>FECHA INICIO</td>
        <td>FECHA FIN</td>
        <td>ID AVISO</td>
        <td>1º AVISO</td>
        <td>enviado 1</td>
        <td>2º AVISO</td>
        <td>enviado 2</td>
        <td>3º AVISO</td>
        <td>enviado 3</td>
        <td>4º AVISO</td>
        <td>enviado 4</td>
        <td>SUSPENSIÓN</td>
      </tr>
    </thead>
    <tbody>
      <?php 
      while($certificado = mysql_fetch_assoc($row_certificado)){
        $fecha_inicio = $certificado['vigencia_inicio'];
        $fecha_fin = $certificado['vigencia_fin'];

        $row_contactos = mysql_query("SELECT contactos.email1, contactos.email2 FROM contactos WHERE contactos.idopp = $certificado[idopp] GROUP BY email1", $dspp) or die(mysql_error());
        $contactos = mysql_fetch_assoc($row_contactos);

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
        ?>
        <tr>
          <!-- ID CERTIFICADO -->
          <td><?php echo $certificado['idcertificado']; ?></td>
          <!-- ID EMPRESA -->
          <td><?php echo $certificado['idopp']; ?></td>
          <!-- ABREVIACIÓN EMPRESA -->
          <td>
            <?php echo $certificado['abreviacion']; ?>
          </td>
          <!-- FECHA INICIO -->
          <td>
            <?php echo $fecha_inicio; ?>
          </td>
          <!-- FECHA FIN -->
          <td>
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

                  if(isset($aviso_renovacion['idaviso_renovacion'])){
                    $updateSQL = sprintf("UPDATE avisos_renovacion SET aviso1 = %s WHERE idaviso_renovacion = %s",
                      GetSQLValueString($time_actual, "int"),
                      GetSQLValueString($aviso_renovacion['idaviso_renovacion'], "text"));
                    $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());
                  }else{
                    $insertSQL = sprintf("INSERT INTO avisos_renovacion(idopp, aviso1, ano_aviso, idcertificado, fecha_certificado) VALUES(%s, %s, %s, %s, %s)",
                      GetSQLValueString($certificado['idopp'], "int"),
                      GetSQLValueString($time_actual, "int"),
                      GetSQLValueString($anio_actual, "text"),
                      GetSQLValueString($certificado['idcertificado'], "int"),
                      GetSQLValueString($certificado['vigencia_fin'], "text"));
                    $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
                  }
                }
              }else{
                echo date('d/m/Y', $aviso_renovacion['aviso1']);
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
                                <p>Estimados Representantes de <strong style="color:red">'.$nombre_opp.', (<u>'.$abreviacion_opp.'</u>)</strong>:</p>

                                <p>
                                  De acuerdo a los avisos de renovación del certificado enviados con anterioridad y de acuerdo a los procedimientos del sistema SPP se hace un gentil recordatorio que el plazo máximo para iniciar la evaluación es un mes después de la vigencia de su certificado (<strong style="color:red">'.$fecha_vigencia.'</strong>). Concluido el mes, el sistema digital D-SPP procederá a enviar la <span style="color:red">suspensión de su certificado</span>.
                                </p>
                                <p>
                                  Una vez que es emitida la suspensión la suspensión del certificado no podrá levantarse la misma hasta concluir el proceso de certificación con un <span style="color:red">dictamen positivo</span>.
                                </p>
                                <p>
                                  Una de las <span style="color:red">consecuencias</span> de la suspensión es que <span style="color:red">no pueden celebrar nuevos contratos</span> bajo la certificación SPP.
                                </p>
                                <p>
                                  Necesariamente deben de iniciar su proceso de renovación a travez del sistema digital D-SPP (<a href="http://d-spp.org/">http://d-spp.org/</a>).
                                </p>

                                <p>
                                  <b style="color:red">Para poder completar su Solicitud de Renovación del Certificado para Organizaciones de Pequeños Productores, debe realizar los siguientes pasos:</b>
                                </p>
                                <ol>
                                  <li>Ingresar en la dirección <a href="http://d-spp.org/">http://d-spp.org/</a>.</li>
                                  <li>Seleccionar el idioma en el que desea utilizar el sistema.</li>
                                  <li>Después de seleccionar el idioma, debe seleccionar la opción "Organización de Pequeños Productores"(OPP) o dar clic en el siguiente link <a href="http://d-spp.org/esp/">Español</a> o en <a href="http://d-spp.org/en/">Ingles</a></li>
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
                                <p>Dear Representatives of: <strong style="color:red">'.$nombre_opp.', (<u>'.$abreviacion_opp.'</u>)</strong>:</p>

                                <p>
                                  According to the certificate renewal notices previously sent and according to the procedures of the SPP system a gentle reminder is made that the maximum period to start the evaluation is one month after the validity of your certificate (<strong style="color:red">'.$fecha_vigencia.'</strong>). At the end of the month, the D-SPP digital system will proceed to send the <span style="color:red">suspension of its certificate</span>.
                                </p>
                                <p>
                                  Once the suspension is issued, the suspension of the certificate can not be lifted until the certification process is concluded with a <span style="color:red">positive opinion</span>.
                                </p>
                                <p>
                                  One of the <span style="color:red">consequences</span> of the suspension is that they can not enter into new contracts under the SPP certification.
                                </p>
                                <p>
                                  You must necessarily start your renewal process through the D-SPP digital system (<a href="http://d-spp.org/">http://d-spp.org/</a>).
                                </p>

                                <p>
                                  <b style="color:red">In order to complete your Certificate Renewal Request for Small Producer Organizations, you must complete the following steps:</b>
                                </p>
                                <ol>
                                  <li>
                                    Enter at <a href="http://d-spp.org/">http://d-spp.org/</a>.
                                  </li>
                                  <li>
                                    Select the language in which you want to use the system.
                                  </li>
                                  <li>
                                    After selecting the language, you must select the "Small Producers Organization" (OPP) or click on the following link <a href="http://d-spp.org/esp/">Español</a> or <a href="http://d-spp.org/en/">Ingles</a>
                                  </li>
                                  <li>
                                    You must login with your user (#SPP): <span style="color:#27ae60">'.$certificado['spp'].'</span> and your password: <span style="color:#27ae60">'.$certificado['password'].'</span>
                                  </li>
                                  <li>
                                    Once you have logged in you must select the "Applications"> "New application".
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
          if(!empty($aviso_renovacion['aviso4'])){
          ?>
            <form action="" method="POST">
              <input type="hidden" name="idcertificado" value="<?php echo $certificado['idcertificado']; ?>">
              <input type="hidden" name="spp" value="<?php echo $certificado['spp']; ?>">
              <input type="hidden" name="nombre_opp" value="<?php echo $nombre_opp; ?>">
              <input type="hidden" name="abreviacion_opp" value="<?php echo $abreviacion_opp; ?>">
              <input type="hidden" name="nombre_oc" value="<?php echo $certificado['nombre_oc'] ?>">
              <input type="hidden" name="fecha_vigencia" value="<?php echo $fecha_vigencia; ?>">
              <button type="submit" name="enviar_suspension" value="1" class="btn btn-sm btn-danger"><span class="glyphicon glyphicon-ban-circle" aria-hidden="true"></span> Suspender</button>  
            </form>
            
          <?php
          }
           ?>         

          </td>
        </tr>

        <?php
      }
       ?>
    </tbody>
  </table>