<?php
	require_once('../Connections/mail.php');
	require_once('../Connections/dspp.php');
	require_once('../mpdf/mpdf.php');
	/** Se agrega la libreria PHPExcel */
	require_once '../PHPExcel/PHPExcel.php';
  mysql_select_db($database_dspp, $dspp);


// ahora viene la creacion del objeto dompdf
/*$dompdf = new DOMPDF();
$dompdf->set_paper('a4','landscape');
$dompdf->load_html($cuerpo_mensaje);
$dompdf->render();
$nombre_archivo = "miarchivo.pdf";




//usuario de destino y cuerpo del mensaje

$mail->AddAddress($usuario_obj->email);
$mail->Subject = "Prueba de phpmailer";
$mail->Body = $cuerpo_mensaje;
//nombre del archivo
$mail->AddAttachment($nombre_archivo,"nombre_adjunto.pdf");

*/



/********  SE ENVIA CORREO SOBRE REPORTE TRIMESTRAL  ************/
    $html = '
      <div>
        <table style="border: 1px solid #ddd;border-collapse: collapse;font-family: Tahoma, Geneva, sans-serif;font-size:12px;">
          <tr style="background-color:#B8D186">
            <td style="">
              Fecha de Elaboracion
            </td>
            <td style="">
              #SPP
            </td>
            <td style="">
              Abreviación de la Empresa
            </td>
            <td style="">
              Tipo de Empresa
            </td>
            <td style="">
              País
            </td>
            <td style="">
              Trimestre
            </td>
          </tr>

          <tr>
            <td style="">
              '.date('d/m/Y', time()).'
            </td>
            <td style="">
              
            </td>
            <td style="">
              ALCE NERO
            </td>
            <td style="">
              COMPRADOR FINAL
            </td>
            <td style="">
              
            </td>
            <td style="">
              1
            </td>
          </tr>


        </table>
      </div>

      <div>
        <table style="border: 1px solid #ddd;border-collapse: collapse;font-family: Tahoma, Geneva, sans-serif;font-size:12px;">
          <tr style="background-color:#B8D186">
            <th>
              #
            </th>
            <th>
              #SPP
            </th>
            <th>
              Nombre OPP proovedora
            </th>
            <th>
              País de OPP proveedora
            </th>
            <th>
              Fecha de Facturación
            </th>
            <th>
              Primer Intermediario
            </th>
            <th>
              Segundo Intermediario
            </th>
            <th colspan="2">
              Referencia Contrato Original con OPP
            </th>
            <th>
              Producto General
            </th>
            <th>
              Producto Especifico
            </th>
            <th colspan="2">
              Producto Terminado
            </th>
            <th colspan="2">
              Cantidad Total Conforme Factura
            </th>
            <th>
              Precio Sustentable Mínimo
            </th>
            <th>
              Reconocimiento Orgánico
            </th>
            <th>
              Incentivo SPP
            </th>
            <th>
              Otros premios
            </th>
            <th>
              Precio Total Unitario pagado
            </th>
            <th>
              Valor Total Contrato
            </th>
            <th>
              Cuota de Uso Reglamento
            </th>
            <th>
              Total a pagar
            </th>
          </tr>

          <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
          </tr>

        </table>
      </div>

    ';
    $contador = 1;
    




    $mpdf = new mPDF('c', 'Legal');
    $mpdf->setAutoTopMargin = 'pad';
    $mpdf->keep_table_proportions = TRUE;
    $mpdf->SetHTMLHeader('
    <header class="clearfix">
      <div>
        <table style="padding:0px;margin-top:-20px;">
          <tr>
            <td style="text-align:left;margin-bottom:0px;font-size:12px;">
                  <div>
                <img src="img/FUNDEPPO.jpg" >
                  </div>
            </td>
            <td style="text-align:right;font-size:12px;">
                  <div>
                <h2>
                  Detalle Reporte Trimestral de Compras
                </h2>             
                  </div>
                  <div>Símbolo de Pequeños Productores</div>
                  <div>'.date('d/m/Y', time()).'</div>
            </td>
          </tr>
        </table>
      </div>
    </header>
      ');
    $css = file_get_contents('css/style_reporte.css');  
    $mpdf->AddPage('L');
    $mpdf->pagenumPrefix = 'Página / Page ';
    $mpdf->pagenumSuffix = ' - ';
    $mpdf->nbpgPrefix = ' de ';
    //$mpdf->nbpgSuffix = ' pages';
    $mpdf->SetFooter('{PAGENO}{nbpg}');
    $mpdf->writeHTML($css,1);
    $mpdf->writeHTML($html);
    //$pdf_listo = $mpdf->Output('reporte.pdf', 'I');
    $pdf_listo = $mpdf->Output('reporte.pdf', 'I'); //reemplazamos la I por S(regresa el documento como string)

    //$pdf_listo = chunk_split(base64_encode($mpdf));
   // $nombre_archivo = 'reporte.pdf';

/********/

	/*	$cuerpo_correo = '
			<html>
			<head>
				<meta charset="utf-8">
			</head>
			<body>
			
				<table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
				  <tbody>
		            <tr>
		              <th rowspan="7" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
		              <th scope="col" align="left" width="280"><strong>Solicitud de Registro para Compradores y otro Actores / Application for Buyers’, Registration </strong></th>
		            </tr>
		            <tr>
		              <td style="padding-top:10px;">
		   
		              Para poder consultar la solicitud, por favor iniciar sesión en su cuenta de OC(Organismo de Certificación) en el siguiente enlace: <a href="http://d-spp.org" target="_new">www.d-spp.org</a>
		              <br>
		              To consult the application, please log in to your CE(Certification Entity) account, in the following link: <a href="http://d-spp.org" target="_new">www.d-spp.org</a>

		         

		              </td>
		            </tr>
				    <tr>
				      <td align="left">Teléfono / Company phone: </td>
				    </tr>

				    <tr>
				      <td align="left"></td>
				    </tr>
				    <tr>
				      <td align="left" style="color:#ff738a;">Email: </td>
				    </tr>
				    <tr>
				      <td align="left" style="color:#ff738a;">Email:</td>
				    </tr>

				  </tbody>
				</table>

			</body>
			</html>
		';
		///// TERMINA ENVIO DEL MENSAJE POR CORREO AL OC y a SPP GLOBAL

		$mail->AddAddress('soporteinforganic@gmail.com');


        //$mail->Username = "soporte@d-spp.org";
        //$mail->Password = "/aung5l6tZ";
        $mail->Subject = utf8_decode($asunto);
        $mail->Body = utf8_decode($cuerpo_correo);
        $mail->MsgHTML(utf8_decode($cuerpo_correo));
        //$mail->AddAttachment($pdf_listo, 'reporte.pdf');

        $mail->addStringAttachment($pdf_listo, 'reporte.pdf');
        $mail->Send();
        $mail->ClearAddresses();

 		$mensaje = "Se ha enviado la Solicitud de Registro al OC, en breve seras contactado";

?>