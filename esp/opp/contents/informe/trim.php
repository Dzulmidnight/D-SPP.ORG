<?php 
require_once('../Connections/dspp.php'); 
require_once('../Connections/mail.php');
require_once('../../mpdf/mpdf.php');

mysql_select_db($database_dspp, $dspp);

if (!isset($_SESSION)) {
  session_start();
	
	$redireccion = "../index.php?OPP";

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
/// PORCENTAJE ACORDE AL AÑO
$porcetaje_cuota = $configuracion['cuota_productores'];

if(isset($_POST['nuevo_trim']) && $_POST['nuevo_trim'] == 'SI'){
	$txt_num_trim = 'trim'.$_GET['trim'];
	$txt_idtrim = 'idtrim'.$_GET['trim'];
	$txt_estatus_trim = 'estado_trim'.$_GET['trim'];
	$idinforme_general = $_POST['idinforme_general'];
	$ano = date('Y', time());
	$idtrim = 'TO'.$_GET['trim'].'-'.$ano.'-'.$idopp;
	$estado_trim = "ACTIVO";

	$insertSQL = sprintf("INSERT INTO $txt_num_trim ($txt_idtrim, idopp, fecha_inicio, $txt_estatus_trim) VALUES (%s, %s, %s, %s)",
		GetSQLValueString($idtrim, "text"),
		GetSQLValueString($idopp, "int"),
		GetSQLValueString($fecha_actual, "int"),
		GetSQLValueString($estado_trim, "text"));
	$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

	$updateSQL = sprintf("UPDATE informe_general SET $txt_num_trim = %s WHERE idinforme_general = %s",
		GetSQLValueString($idtrim, "text"),
		GetSQLValueString($idinforme_general, "text"));
	$actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

	echo "<script>alert('Se ha creado un nuevo formato trimestral $idtrim');</script>";
}

if(isset($_POST['finalizar_trim']) && $_POST['finalizar_trim'] == 'SI'){
	$idtrimestre = $_POST['idtrim'];

	$row_valor_total_contrato = mysql_query("SELECT SUM(valor_total_contrato) AS 'total_contrato' FROM formato_ventas WHERE idtrim = '$idtrimestre'", $dspp) or die(mysql_error());
	$valor_total_contrato = mysql_fetch_assoc($row_valor_total_contrato);

	$row_total_a_pagar = mysql_query("SELECT ROUND(SUM(total_a_pagar),2) AS 'total_a_pagar' FROM formato_ventas WHERE idtrim = '$idtrimestre'", $dspp) or die(mysql_error());
	$total_a_pagar = mysql_fetch_assoc($row_total_a_pagar);


	/*if(isset($_POST['suma_cuota_uso']) || $_POST['suma_cuota_uso'] != 0 || $_POST['suma_cuota_uso'] != NULL){
		$suma_cuota_uso = $_POST['suma_cuota_uso'];
	}else{
		$suma_cuota_uso = 0;
	}
	if(isset($_POST['suma_valor_contrato']) || $_POST['suma_valor_contrato'] != 0 || $_POST['suma_valor_contrato'] != NULL){
		$suma_valor_contrato = $_POST['suma_valor_contrato'];
	}else{
		$suma_valor_contrato = 0;
	}*/

	$row_informe = mysql_query("SELECT total_informe, total_cuota_uso, total_valor_contrato FROM informe_general WHERE idinforme_general = '$informe_general[idinforme_general]'", $dspp) or die(mysql_error());
	$informe = mysql_fetch_assoc($row_informe);

	$total_cuota_uso = $informe['total_cuota_uso'] + $suma_cuota_uso;
	$total_valor_contrato = $informe['total_valor_contrato'] + $suma_valor_contrato;
	$txt_idtrim = 'idtrim'.$_GET['trim'];
	$txt_numero_trim = 'trim'.$_GET['trim'];
	$txt_estado_trim = 'estado_'.$txt_numero_trim;
	$txt_total_trim = 'total_'.$txt_numero_trim;
	$txt_valor_contrato = 'valor_contrato_'.$txt_numero_trim;
	$txt_cuota_uso = 'cuota_uso_'.$txt_numero_trim;
	$txt_reporte_trim = 'reporte_trim'.$_GET['trim'];
	$estatus_trim = 'EN ESPERA';
	$ruta_pdf = '../../archivos/admArchivos/facturas/reportes_opp/';
	$nombre_pdf = 'reporte_comprador_'.time().'.pdf';
	$reporte = $ruta_pdf.$nombre_pdf;
	

	$updateSQL = sprintf("UPDATE $txt_numero_trim SET $txt_valor_contrato = %s, $txt_cuota_uso = %s, $txt_estado_trim = %s, $txt_reporte_trim = %s WHERE $txt_idtrim = %s",
		GetSQLValueString($valor_total_contrato['total_contrato'], "text"),
		GetSQLValueString($total_a_pagar['total_a_pagar'], "double"),
		GetSQLValueString($estatus_trim, "text"),
		GetSQLValueString($reporte, "text"),
		GetSQLValueString($_POST['idtrim'], "text"));
	$actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

	/*14_03_2017$updateSQL = sprintf("UPDATE informe_general SET total_valor_contrato = %s, total_cuota_uso = %s WHERE idinforme_general = %s",
		GetSQLValueString($total_valor_contrato, "double"),
		GetSQLValueString($total_cuota_uso, "double"),
		GetSQLValueString($informe_general['idinforme_general'], "text"));
	$actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

	
	if($txt_numero_trim == 'trim4'){
		//revisamos si el trim4 ha finalizado, entonces cambiamos el estatus del INFORME GENERAL a FINALIZADO ya que se han concluido los 4 trimestres
		//Tambien se agregar el monto de los 4 informes trimestrales dentro del TOTAL DEL INFORME GENERAL
		$updateSQL = sprintf("UPDATE informe_general SET estado_informe = %s WHERE idinforme_general = %s",
			GetSQLValueString('FINALIZADO', "text"),
			GetSQLValueString($informe_general['idinforme_general'], "text"));
		$actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());
	}14_03_2017*/


/********  SE ENVIA CORREO SOBRE REPORTE TRIMESTRAL  ************/

	//$tipo_empresa = $tipo_empresa;

	$txt_cuota = 'cuota_uso_trim'.$_GET['trim'];
	$txt_trim = 'trim'.$_GET['trim'];
	$txt_id = 'idtrim'.$_GET['trim'];
	$row_total = mysql_query("SELECT $txt_cuota AS 'total_cuota_uso' FROM $txt_trim WHERE $txt_id = '$idtrimestre'", $dspp) or die(mysql_error());
	$total = mysql_fetch_assoc($row_total);


    $html = '
      <div>
        <table style="border: 1px solid #ddd;border-collapse: collapse;font-family: Tahoma, Geneva, sans-serif;font-size:12px;">
          <tr style="background-color:#B8D186">
            <td style="">
              Fecha de Elaboración
            </td>
            <td style="">
              #SPP
            </td>
            <td style="">
              Abreviación de la OPP
            </td>
            <td style="">
              País de la OPP
            </td>
            <td style="">
              Trimestre
            </td>
            <td>
				Total a pagar
            </td>
          </tr>

          <tr>
            <td style="">
              '.date('d/m/Y', time()).'
            </td>
            <td style="">
              '.$opp['spp'].'
            </td>
            <td style="">
              '.$opp['abreviacion'].'
            </td>
            <td style="">
              '.$opp['pais'].'
            </td>
            <td style="">
              '.$idtrimestre.'
            </td>
            <td style="background-color:#e74c3c;color:#ecf0f1">
              '.number_format($total_a_pagar['total_a_pagar'],2).' USD
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
            	País OPP
            </th>
            <th>
              #SPP
            </th>
            <th>
              Nombre de la Empresa
            </th>
            <th>
              País de la Empresa
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
              Referencia Contrato Original con la Empresa
            </th>
            <th>
              Producto General
            </th>
            <th>
              Producto Especifico
            </th>
            <th colspan="3">
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


          ';
          	$contador = 1;
			$row_formato_ventas = mysql_query("SELECT * FROM formato_ventas WHERE idtrim = '$idtrimestre'", $dspp) or die(mysql_error());
			$num_contratos = mysql_num_rows($row_formato_ventas);
			while($formato_ventas = mysql_fetch_assoc($row_formato_ventas)){
				if(isset($formato_ventas['fecha_contrato'])){
					$fecha_contrato = date('d/m/Y', $formato_ventas['fecha_contrato']);
				}else{
					$fecha_contrato = '';
				}

			  $html .= '
				<tr>
				    <td>'.$contador.'</td>
				    <td>'.$formato_ventas['pais_opp'].'</td>
				    <td>'.$formato_ventas['spp'].'</td>
				    <td>'.$formato_ventas['empresa'].'</td>
				    <td>'.$formato_ventas['pais_empresa'].'</td>
				    <td>'.date('d/m/Y', $formato_ventas['fecha_facturacion']).'</td>
				    <td>'.$formato_ventas['primer_intermediario'].'</td>
				    <td>'.$formato_ventas['segundo_intermediario'].'</td>
				    <td>'.$formato_ventas['clave_contrato'].'</td>
				    <td>'.$fecha_contrato.'</td>
				    <td>'.$formato_ventas['producto_general'].'</td>
				    <td>'.$formato_ventas['producto_especifico'].'</td>
				    <td>'.$formato_ventas['producto_terminado'].'</td>
				    <td>Se exporta: '.$formato_ventas['se_exporta'].'</td>
				    <td>Valor_ingredientes: '.$formato_ventas['valor_ingredientes'].'</td>
				    <td>'.$formato_ventas['unidad_cantidad_factura'].'</td>
				    <td>'.number_format($formato_ventas['cantidad_total_factura'],2).' USD</td>
				    <td>'.$formato_ventas['precio_sustentable_minimo'].' USD</td>
				    <td>'.$formato_ventas['reconocimiento_organico'].' USD</td>
				    <td>'.$formato_ventas['incentivo_spp'].' USD</td>
				    <td>'.$formato_ventas['otros_premios'].' USD</td>
				    <td>'.$formato_ventas['precio_total_unitario'].' USD</td>
				    <td>'.number_format($formato_ventas['valor_total_contrato'],2).' USD</td>
				    <td>'.$formato_ventas['cuota_uso_reglamento'].'</td>
				    <td>'.number_format($formato_ventas['total_a_pagar'],2).' USD</td>
				</tr>
			  ';
			 $contador++;
			}

    $html .= '
        </table>
      </div>

    ';

    $mpdf = new mPDF('c', 'Legal');
	ob_start();

    $mpdf->setAutoTopMargin = 'pad';
    $mpdf->keep_table_proportions = TRUE;
    $mpdf->SetHTMLHeader('
    <header class="clearfix">
      <div>
        <table style="padding:0px;margin-top:-20px;">
          <tr>
            <td style="text-align:left;margin-bottom:0px;font-size:12px;">
                  <div>
                <img src="../../reportes/img/FUNDEPPO.jpg" >
                  </div>
            </td>
            <td style="text-align:right;font-size:12px;">
                  <div>
                <h2>
                  Detalle Reporte Trimestral de Ventas
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
    $css = file_get_contents('../../reportes/css/style_reporte.css');  
    $mpdf->AddPage('L'); //se cambia la orientacion de la pagina
    $mpdf->pagenumPrefix = 'Página / Page ';
    $mpdf->pagenumSuffix = ' - ';
    $mpdf->nbpgPrefix = ' de ';
    //$mpdf->nbpgSuffix = ' pages';
    $mpdf->SetFooter('{PAGENO}{nbpg}');
    $mpdf->writeHTML($css,1);

	ob_end_clean();

    $mpdf->writeHTML($html);
    //$pdf_listo = $mpdf->Output('reporte.pdf', 'I');
    
    /// CON LA LINEA DE ABAJO GENERAMOS EL PDF Y LO ENVIAMOS POR EMAIL, PERO NO LO GUARDAMOS
    //28_03_2017 $pdf_listo = $mpdf->Output('reporte_trimestral.pdf', 'S'); //reemplazamos la I por S(regresa el documento como string)
	/// CON LA LINEA DE ABAJO GENERAMOS EL PDF Y LO GUARDAMOS EN UNA CARPETA
	$mpdf->Output(''.$ruta_pdf.''.$nombre_pdf.'', 'F'); //reemplazamos la I por S(regresa el documento como string)
    //$pdf_listo = chunk_split(base64_encode($mpdf));
   // $nombre_archivo = 'reporte.pdf';

/********/
	$asunto = 'D-SPP - Informe Trimestral Ventas';
	$mensaje_correo = '
		<html>
		<head>
			<meta charset="utf-8">
		</head>
		<body>
		
			<table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
				<tbody>
				    <tr>
				      <th rowspan="2" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
				      <th scope="col" align="left" width="280">Detalle Reporte Trimestral de Ventas SPP</th>
				    </tr>
				    <tr>
				      <td style="padding-top:10px;">           
				        La OPP <span style="color:red">'.$opp['abreviacion'].'</span> ha finalizado el <span style="color:red">TRIMESTRE '.$_GET['trim'].'</span>, a continuación se muestra una tabla con el resumen de las transacciones realizadas.
				      </td>
				    </tr>
				    <tr>
				      <td colspan="2">
				        <table style="border: 1px solid #ddd;border-collapse: collapse;font-size:12px;">
				          <tr style="border: 1px solid #ddd;border-collapse: collapse;">
				            <td colspan="7" style="text-align:center">Resumen de las transacciones</td>
				          </tr>
				          <tr style="border: 1px solid #ddd;border-collapse: collapse;">
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">OPP</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">Informe</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">Num. de transacciones</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">Valor total de los contratos</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">Cuota de uso aplicada acorde al año en curso</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">Total cuota de uso</td>
				          </tr>
				          <tr style="border: 1px solid #ddd;border-collapse: collapse;">
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">'.$opp['abreviacion'].'</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">'.$idtrimestre.'</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">'.$num_contratos.'</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">'.number_format($valor_total_contrato['total_contrato'],2).'</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">'.$porcetaje_cuota.'%</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">'.number_format($total_a_pagar['total_a_pagar'],2).'</td>
				          </tr>
				        </table>
				      </td>
				    </tr>
				    <tr>
				      <td style="padding-top:10px;" colspan="2">
				        Se adjunta el PDF con las transacciones correspondientes al trimestre finalizado. Por favor verificar la información, en caso de que la información sea correcta realizar los siguientes pasos para poder "APROBAR" el reporte:
				        <ol>
				        	<li>Debe de ingresar en su cuenta como Administrador dentro del sistema D-SPP</li>
				        	<li>Seleccionar la opción "Reportes Comerciales"</li>
				        	<li>Dar clic en la pestaña "Ingreso Cuota de Uso"</li>
				        	<li>Seleccionar la pestaña "OPP"</li>
				        	<li>Localizar la Organización y el trimestre correspondiente y dar clic en el boton "+"(más)</li>
				        </ol>
				        
				      </td>
				    </tr>
				</tbody>
			</table>

		</body>
		</html>
	';
	///// TERMINA ENVIO DEL MENSAJE POR CORREO AL OC y a SPP GLOBAL
	//$mail->AddAddress($correo_cert);

		$mail->AddAddress('cert@spp.coop');


    //$mail->Username = "soporte@d-spp.org";
    //$mail->Password = "/aung5l6tZ";
    $mail->Subject = utf8_decode($asunto);
    $mail->Body = utf8_decode($mensaje_correo);
    $mail->MsgHTML(utf8_decode($mensaje_correo));
    //$mail->AddAttachment($pdf_listo, 'reporte.pdf');

    //28_03_2017$mail->addStringAttachment($pdf_listo, 'reporte_trimestral.pdf'); // SE ENVIA LA CADENA DE TEXTO DEL PDF POR EMAIL
    $mail->AddAttachment($reporte);
    $mail->Send();
    $mail->ClearAddresses();
	///se envia correo al area de certificacion para corroborar la informacion

}
//// INICIA ENVIO COMPROBANTE DE PAGO
if(isset($_POST['enviar_comprobante']) && $_POST['enviar_comprobante'] == 1){
	$fecha = time();
	$idopp = $_POST['idopp'];
	$txt_idtrim = 'idtrim'.$_POST['num_trimestre'];
	$trim = 'trim'.$_POST['num_trimestre'];
	//$txt_idtrim = 'idtrim'.$_POST['num_trimestre'];
	$idtrimestre = $_POST['idtrimestre'];
	$comprobante_pago = $_POST['comprobante_pago'];
	$estatus_comprobante = 'ENVIADO';
	$num_trimestre = $_POST['num_trimestre'];
	$txt_valor_contrato = 'valor_contrato_trim'.$_POST['num_trimestre'];
	$txt_cuota_uso = 'cuota_uso_trim'.$_POST['num_trimestre'];
	$txt_reporte = 'reporte_trim'.$_POST['num_trimestre'];

	$row_opp = mysql_query("SELECT spp, abreviacion FROM opp WHERE idopp = $idopp", $dspp) or die(mysql_error());
	$opp = mysql_fetch_assoc($row_opp);

	$row_trim = mysql_query("SELECT $txt_valor_contrato, $txt_cuota_uso, $txt_reporte FROM $trim WHERE $txt_idtrim = '$idtrimestre'", $dspp) or die(mysql_error());
	$detalle_trim = mysql_fetch_assoc($row_trim);


	///cargamos y guardamos el comprobante de pago
	$rutaArchivo = "../../archivos/admArchivos/facturas/comprobante_pago/";
	if(!empty($_FILES['comprobante_pago']['name'])){
	  $_FILES["comprobante_pago"]["name"];
	    move_uploaded_file($_FILES["comprobante_pago"]["tmp_name"], $rutaArchivo.$fecha."_".$_FILES["comprobante_pago"]["name"]);
	    $archivo_comprobante = $rutaArchivo.basename($fecha."_".$_FILES["comprobante_pago"]["name"]);
	}else{
		$archivo_comprobante = NULL;
	}

	$txt_comprobante = 'comprobante_pago_trim'.$num_trimestre;
	$txt_estatus_comprobante = 'estatus_comprobante_trim'.$num_trimestre;

	$updateSQL = sprintf("UPDATE $trim SET $txt_comprobante = %s, $txt_estatus_comprobante = %s WHERE $txt_idtrim = %s",
		GetSQLValueString($archivo_comprobante, "text"),
		GetSQLValueString($estatus_comprobante, "text"),
		GetSQLValueString($idtrimestre, "text"));
	$actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

	$asunto = 'D-SPP - Pago Informe Trimestral Ventas';
	$mensaje_correo = '
		<html>
		<head>
			<meta charset="utf-8">
		</head>
		<body>
		
			<table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
				<tbody>
				    <tr>
				      <th rowspan="2" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
				      <th scope="col" align="left" width="280">Detalle Reporte Trimestral de Ventas SPP</th>
				    </tr>
				    <tr>
				      <td style="padding-top:10px;">           
				        La OPP '.$opp['abreviacion'].' ha cargado el comprobante de pago del Trimestre '.$_POST['num_trimestre'].'.
				      </td>
				    </tr>

				    <tr>
				      <td colspan="2">
				        <table style="border: 1px solid #ddd;border-collapse: collapse;font-size:12px;">
				          <tr style="border: 1px solid #ddd;border-collapse: collapse;">
				            <td colspan="7" style="text-align:center">Resumen de las transacciones</td>
				          </tr>
				          <tr style="border: 1px solid #ddd;border-collapse: collapse;">
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">OPP</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">Informe</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">Valor total de los contratos</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">Cuota de uso aplicada acorde al año en curso</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">Total cuota de uso</td>
				          </tr>
				          <tr style="border: 1px solid #ddd;border-collapse: collapse;">
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">'.$opp['abreviacion'].'</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">'.$idtrimestre.'</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">'.number_format($detalle_trim[$txt_valor_contrato],2).'</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">'.$porcetaje_cuota.'%</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">'.number_format($detalle_trim[$txt_cuota_uso],2).'</td>
				          </tr>
				        </table>
				      </td>
				    </tr>
				</tbody>
			</table>

		</body>
		</html>
	';
	///// TERMINA ENVIO DEL MENSAJE POR CORREO AL OC y a SPP GLOBAL

	$mail->AddBCC('soporteinforganic@gmail.com');
	//$mail->AddAddress($correo_cert);
	//$mail->AddAddress($correo_adm);
	
		$mail->AddAddress('adm@spp.coop');
		$mail->AddBCC('cert@spp.coop');


	$mail->AddAttachment($detalle_trim[$txt_reporte]);
	$mail->AddAttachment($archivo_comprobante);
    //$mail->Username = "soporte@d-spp.org";
    //$mail->Password = "/aung5l6tZ";
    $mail->Subject = utf8_decode($asunto);
    $mail->Body = utf8_decode($mensaje_correo);
    $mail->MsgHTML(utf8_decode($mensaje_correo));

    if($mail->Send()){
    	$mail->ClearAddresses();
		echo "<script>alert('Se ha enviado y notificado el pago a SPP Global');</script>";
    }else{
		echo "<script>alert('No se pudo enviar el correo, por favor ponerse en contacto con el area de soporte);</script>";
    }


}
//// TERMINA ENVIO COMPROBANTE DE PAGO

if(isset($_GET['trim'])){

	$num_trim = "trim".$_GET['trim'];
	$ano_actual = date('Y', time());
	$row_trim = mysql_query("SELECT * FROM $num_trim WHERE idopp = $idopp AND FROM_UNIXTIME(fecha_inicio, '%Y') = $ano_actual", $dspp) or die(mysql_error());
	$total_trim = mysql_num_rows($row_trim);
	$trim = mysql_fetch_assoc($row_trim);
	$idtrim = "id".$num_trim;
	$estado_trim = "estado_trim".$_GET['trim'];
	$estatus = $trim[$estado_trim];
	if(isset($estatus)){
		if($estatus == 'ACTIVO'){
			$estatus = $trim[$estado_trim];
			$pregunta = "
			<form action='' method='POST'>
				<p style='font-size:13px;'>
					<b style='color:red'>¿Desea concluir la captura de transacciones en el formato de trimestre actual? </b>
					<button class='' type='subtmit' value='SI'  name='finalizar_trim' data-toggle='tooltip' data-placement='top' title='Finalizar trimestre actual' onclick='return confirm(\"¿Desea finalizar la captura del trimestre actual?\");' >SI</button>
					<!--<input class='btn btn-success' type='submit' name='finalizar_trim' value='SI'>-->
					<input type='hidden' name='idtrim' value='".$trim[$idtrim]."'>
					<input type='hidden' name='fecha' value='".time()."'>
					
				</p>
			</form>
			";
		}else{
			$estatus = $trim[$estado_trim];
		}
	}else{
		$estatus = 'No Disponible';
	}
	$titulo_trim = '';


	switch ($_GET['trim']) {
		case '1':
			$titulo_trim = "<h4>PRIMER TRIMESTRE <span style='color:red'>$ano_actual</span> | <small>Estatus:  $estatus</small></h4>";
			break;
		case '2':
			$titulo_trim = "<h4>SEGUNDO TRIMESTRE <span style='color:red'>$ano_actual</span> | <small>Estatus: $estatus</small></h4>";
			break;
		case '3':
			$titulo_trim = "<h4>TERCER TRIMESTRE <span style='color:red'>$ano_actual</span> | <small>Estatus: $estatus</small></h4>";
			break;
		case '4':
			$titulo_trim = "<h4>CUARTO TRIMESTRE <span style='color:red'>$ano_actual</span> | <small>Estatus: $estatus</small></h4>";
			break;		
		default:
			$titulo_trim = "<h4>TRIMESTRE NO DISPONIBLE</small></h4>";
			break;
	}

	//echo $titulo_trim;
	if($total_trim == 1){
		$ano_actual = date('Y', time());
		$query_formato = "SELECT formato_ventas.* FROM formato_ventas WHERE formato_ventas.idtrim = '$trim[$idtrim]' AND idopp = $idopp";
		$row_formato = mysql_query($query_formato, $dspp) or die(mysql_error());
		$idtrim_txt = 'idtrim'.$_GET['trim'];
		$txt_trim = 'trim'.$_GET['trim'];
		$row_trim_menu = mysql_query("SELECT * FROM $txt_trim WHERE idopp = $idopp AND FROM_UNIXTIME(fecha_inicio, '%Y') = $ano_actual");
	  	$trim_options = mysql_fetch_assoc($row_trim_menu);

		if(isset($_GET['add'])){
			include('informe_add.php');
		}else{
		?>
		<div style="margin-top:10px;">
			<a class="btn btn-default" href="?INFORME&general_detail&trim=<?php echo $_GET['trim']; ?>&add&idtrim=<?php echo $trim_options[$idtrim_txt]; ?>"><span class="glyphicon glyphicon-plus"></span> Agregar nueva transacción</a>
			<a class="btn btn-default" href="?INFORME&general_detail&trim=<?php echo $_GET['trim']; ?>&edit&idtrim=<?php echo $trim_options[$idtrim_txt]; ?>"><span class="glyphicon glyphicon-pencil"></span> Editar transacciónes actuales</a>
		</div>



			<table class="table table-bordered" style="font-size:11px;">
				<thead>
					<tr>
						<th colspan="7">
							<?php 
							echo $titulo_trim;
							 ?>
						</th>


						<th colspan="4">
							<?php echo "<h4>".$opp['abreviacion']."</h4>"; ?>
						</th>
						<th colspan="5" class="info" style="border-style:hidden;border-left-style:solid;border-bottom-style:solid">
							<?php 
							if(isset($pregunta)){
								echo $pregunta;
							}

							$txt_trim = 'trim'.$_GET['trim'];
							$txt_id = 'idtrim'.$_GET['trim'];
							$txt_estado_trim = 'estado_trim'.$_GET['trim'];
							$txt_estatus_factura = 'estatus_factura_trim'.$_GET['trim'];
							$txt_factura = 'factura_trim'.$_GET['trim'];
							$txt_estatus_comprobante = 'estatus_comprobante_trim'.$_GET['trim'];
							$txt_comprobante = 'comprobante_pago_trim'.$_GET['trim'];
							$txt_reporte_trim = 'reporte_trim'.$_GET['trim'];
							$row_trim = mysql_query("SELECT * FROM $txt_trim WHERE $txt_id = '$trim[$idtrim]'", $dspp) or die(mysql_error());
							$trim = mysql_fetch_assoc($row_trim); 
							if($trim[$txt_estatus_factura] == 'ENVIADA'){
								echo "<div>";
									echo "<span style='color:red'>SE HA ENVIADO LA FACTURA</span><br>";
									echo "<a class='btn btn-success' href='".$trim[$txt_factura]."' target='_new'><span class='glyphicon glyphicon-floppy-save' aria-hidden='true'></span> Descargar Factura</a>";
								echo "</div>";
							}else if($trim[$txt_estado_trim] == 'EN ESPERA'){
								echo "<p style='color:red;font-size:12px;'>El Informe trimestral está en proceso de revisión</p>";
							}else if($trim[$txt_estado_trim] == 'FINALIZADO'){
								echo "<p style='color:red;font-size:12px;'>Ha concluido el Informe Trimestral</p>";
							}
							//echo 'asfasfds'.$trim[$txt_estatus_factura];
							 ?>
						</th>
						<th colspan="9">
							<?php
							if($trim[$txt_estatus_factura] == 'ENVIADA'){
								if(isset($trim[$txt_estatus_comprobante]) && $trim[$txt_estatus_comprobante] == 'ENVIADO'){
									echo "<span style='color:red'>SE HA ENVIADO EL COMPROBANTE DE PAGO</span>";
								}else if($trim[$txt_estatus_comprobante] == 'APROBADO'){
									echo "<p>Se ha APROBADO el comprobante de pago</p>";
									echo "<a href='".$trim[$txt_comprobante]."' target='_new' class='btn btn-success'><span class='glyphicon glyphicon-floppy-save' aria-hidden='true'></span> Descarga comproban de pago</a>";
								}else{
								?>
									<form action="" method="POST" enctype="multipart/form-data">
										<p style="font-size:12px;">Dar clic en el siguiente botón para poder cargar el comprobante de pago correspondiente al Informe Trimestral.</p>
										<input type="file" class="form-control" name="comprobante_pago">
										<input type="hidden" name="num_trimestre" value="<?php echo $_GET['trim']; ?>">
										<input type="hidden" name="idtrimestre" value="<?php echo $trim[$txt_id]; ?>">
										<input type="hidden" name="idopp" value="<?php echo $trim['idopp']; ?>">
										<button class="btn btn-warning" type="submit" name="enviar_comprobante" value="1">Enviar Comprobante</button>								
									</form>
								<?php
								}
							}else if($trim[$txt_estado_trim] == 'FINALIZADO'){
								if(isset($trim[$txt_factura])){
									echo "<a href='".$trim[$txt_factura]."' target='_new' class='btn btn-success'><span class='glyphicon glyphicon-floppy-save' aria-hidden='true'></span> Descargar Factura</a>";
								}else{
									echo "<div class='col-xs-6'><p style='color:red'><span class='glyphicon glyphicon-ban-circle' aria-hidden='true'></span> Factura no disponible </p></div>";
								}
								if(isset($trim[$txt_reporte_trim])){
									echo "<a href='".$trim[$txt_reporte_trim]."' target='_new' class='btn btn-success'><span class='glyphicon glyphicon-floppy-save' aria-hidden='true'></span> Descarga Reporte Trimestral</a>";
								}else{
									echo "<div class='col-xs-6'><p style='color:red'><span class='glyphicon glyphicon-ban-circle' aria-hidden='true'></span> Reporte trimestral no disponible</p></div>";
								}
							}
							 ?>
						</th>
					</tr>
		<form action="" method="POST">	
					<tr class="success">
						<th class="text-center">#</th>
						<th class="text-center">Pais de la OPP</th>
						<th class="text-center">#SPP del Comprador Final</th>
						<th class="text-center">Nombre del Comprador Final</th>
						<th class="text-center">País del Comprador Final</th>
						<th class="text-center">Fecha de Facturación</th>
						<th class="text-center">Primer Intermediario</th>
						<th class="text-center">Segundo Intermediario</th>
						<th colspan="2" class="text-center">Referencia Contrato Original con el Comprador Final</th>
						<th class="text-center">Producto General</th>
						<th class="text-center">Producto Especifico</th>
						<th class="warning text-center">¿Producto terminado?</th>
						<th class="warning text-center">Se exporta a travez de:</th>
						<th class="warning text-center">Valor ingredientes</th>
						<th colspan="2" class="text-center">Cantidad Total Conforme Factura</th>
						<th class="text-center">Precio Sustentable Mínimo</th>
						<th class="text-center">Reconocimiento Orgánico</th>
						<th class="text-center">Incentivo SPP</th>
						<th class="text-center">Otros premios</th>
						<th class="text-center">Precio Total Unitario pagado</th>
						<th class="text-center">Valor Total Contrato</th>
						<th class="text-center">Cuota de Uso Reglamento</th>
						<th class="text-center">Total a pagar</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$contador = 1;
					$suma_cuota_uso = 0;
					$suma_valor_contrato = 0;
					while($formato = mysql_fetch_assoc($row_formato)){
					?>
						<tr>
							<td><?php echo $contador; ?></td>
							<td><?php echo $formato['pais_opp'] ?></td>
							<td><?php echo $formato['spp']; ?></td>
							<td><?php echo $formato['empresa']; ?></td>
							<td><?php echo $formato['pais_empresa']; ?></td>
							<td><?php echo date('d/m/Y',$formato['fecha_facturacion']); ?></td>
							<td><?php echo $formato['primer_intermediario']; ?></td>
							<td><?php echo $formato['segundo_intermediario']; ?></td>
							<td><?php echo $formato['clave_contrato']; ?></td>
							<td>
								<?php
								if(isset($formato['fecha_contrato'])){
									echo date('d/m/Y',$formato['fecha_contrato']);
								}
								?>
							</td>
							<td><?php echo $formato['producto_general']; ?></td>
							<td><?php echo $formato['producto_especifico']; ?></td>
							<td><?php echo $formato['producto_terminado']; ?></td>
							<td><?php echo $formato['se_exporta']; ?></td>
							<td><?php echo $formato['valor_ingredientes']; ?></td>
							<td><?php echo $formato['unidad_cantidad_factura']; ?></td>
							<td><?php echo number_format($formato['cantidad_total_factura'],2); ?></td>
							<td><?php echo $formato['precio_sustentable_minimo']; ?></td>
							<td><?php echo $formato['reconocimiento_organico']; ?></td>
							<td><?php echo $formato['incentivo_spp']; ?></td>
							<td><?php echo $formato['otros_premios']; ?></td>
							<td><?php echo $formato['precio_total_unitario']; ?></td>
							<td><?php echo number_format($formato['valor_total_contrato'],2).' USD'; ?></td>
							<td><?php echo $formato['cuota_uso_reglamento']; ?></td>
							<td style="background-color:#e74c3c;color:#ecf0f1;"><?php echo number_format($formato['total_a_pagar'],2).' USD'; ?></td>
						</tr>
					<?php
					$suma_cuota_uso = $formato['total_a_pagar'] + $suma_cuota_uso;
					$suma_valor_contrato = $formato['valor_total_contrato'] + $suma_valor_contrato; 
					$contador++;
					}
						
						echo "<tr class='info'>
							<td colspan='22'></td>
							<td class='text-right'><b style='color:red'>".number_format($suma_valor_contrato,2)." USD</b></td>
							<td></td>
							<td class='text-right'><b style='color:red'>".number_format($suma_cuota_uso,2)." USD</b></td>
						</tr>";
						//EL TOTAL A PAGAR AL FINALIZAR EL TRIMESTRE
						echo "<input type='hidden' name='suma_cuota_uso' value='$suma_cuota_uso'>";
						echo "<input type='hidden' name='suma_valor_contrato' value='$suma_valor_contrato'>";
					 ?>
				</tbody>
			</table>

		</form>
		<?php
		}
	?>


	<?php
	}else{
		/////
		$row_trim1 = mysql_query("SELECT idtrim1, estado_trim1 FROM trim1 WHERE idopp = $idopp AND FROM_UNIXTIME(fecha_inicio, '%Y') = $ano_actual", $dspp) or die(mysql_error());
		$trim1 = mysql_fetch_assoc($row_trim1);

		if(!empty($trim1['idtrim1'])){ //confirmamos que se ha creado el primer trim (TRIM1)
			$num_trim = $_GET['trim'];
			$trim_actual = 'trim'.$num_trim;
			$txt_idtrim = 'idtrim'.$num_trim;

			if($trim1['estado_trim1'] == 'FINALIZADO'){ // SI EL TRIM1 HA FINALIZADO, REVISAREMOS QUE LOS TRIMS SIGUIENTES CONCLUYAN PARA PODER CREAR UNO NUEVO
				if($num_trim != 1){
					//checamos que el trim anterior haya finalizado
					//// iniciamos VARIABLES DEL TRIM ANTERIOR
						$trim_anterior = 'trim'.($num_trim - 1); //restamos 1 al trim actual para poder consultar el anterio
						$idtrim_anterior = 'idtrim'.($num_trim - 1);
						$estado_trim = 'estado_trim'.($num_trim - 1);
					// terminamos VARIABLES DEL TRIM ANTERIOR


					$row_trim_anterior = mysql_query("SELECT * FROM $trim_anterior WHERE idopp = $idopp AND FROM_UNIXTIME(fecha_inicio, '%Y') = $ano_actual", $dspp) or die(mysql_error());
					$informacion_trim_anterior = mysql_fetch_assoc($row_trim_anterior);

					if(isset($informacion_trim_anterior[$idtrim_anterior]) && $informacion_trim_anterior[$estado_trim] == 'FINALIZADO'){ /// SI EL TRIM ANTERIOR HA FINALIZADO, MOSTRAREMOS LA OPCIÓN PARA PODER CREAR UN NUEVO TRIM
						$num_trim_actual = 'trim'.$_GET['trim'];
						$row_trim_actual = mysql_query("SELECT * FROM $trim_actual WHERE idopp = $idopp AND FROM_UNIXTIME(fecha_inicio, '%Y') = $ano_actual", $dspp) or die(mysql_error());
						$total_trim_actual = mysql_num_rows($row_trim_actual);
						if($total_trim_actual != 1){ // si ya se ha iniciado el nuevo trim, ya no mostraremos la opción
							echo '
								<form action="" method="POST">
									<p class="alert alert-info">
										<strong>¿Desea crear un nuevo Formato para Informe Trimestral?</strong>
										<input class="btn btn-success" type="submit" name="nuevo_trim" value="SI">
										<input type="hidden" name="idinforme_general" value="'.$informe_general['idinforme_general'].'">
									</p>
								</form>
							';
						}
					}else{
						echo "<p class='alert alert-danger'><span class='glyphicon glyphicon-ban-circle' aria-hidden='true'></span> AUN NO SE PUEDE INICIAR ESTE INFORME TRIMESTRAL, <b>DEBE FINALIZAR EL INFORME ANTERIOR</b></p>";
					}
					$row_trim = mysql_query("SELECT * FROM $trim_actual WHERE idopp AND FROM_UNIXTIME(fecha_inicio, '%Y') = $ano_actual",$dspp) or die(mysql_error());
					$informacion_trim = mysql_fetch_assoc($row_trim);
				}
			}
		}
	}


}
 ?>