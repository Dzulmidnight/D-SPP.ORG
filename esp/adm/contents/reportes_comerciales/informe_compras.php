<?php 
require_once('../Connections/dspp.php'); 
require_once('../Connections/mail.php');
require_once('../../mpdf/mpdf.php');

mysql_select_db($database_dspp, $dspp);

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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}


//// INICIA APROBAR REPORTE  ////
if(isset($_POST['aprobar_reporte']) && $_POST['aprobar_reporte'] == 'SI'){
	$num_trim = $_POST['num_trim'];
	$idtrimestre = $_POST['idtrimestre'];
	$idinforme_general = $_POST['idinforme_general'];


	$aprobado = '';
	$fecha_fin = time();
	$anio_actual = date('Y', time());

	$txt_trim = 'trim'.$num_trim;
	$txt_idtrim = 'idtrim'.$num_trim;
	$txt_valor_contrato = 'valor_contrato_trim'.$num_trim;
	$txt_cuota_uso = 'cuota_uso_trim'.$num_trim;
	$txt_estado_trim = 'estado_trim'.$num_trim;
	$estatus_trim = 'APROBADO';
	//$idtrimestre = $_GET['trim'];

	$row_configuracion = mysql_query("SELECT * FROM porcentaje_ajuste WHERE anio = $anio_actual", $dspp) or die(mysql_error());
	$configuracion = mysql_fetch_assoc($row_configuracion);

	$row_trim = mysql_query("SELECT * FROM $txt_trim WHERE $txt_idtrim = '$idtrimestre'", $dspp) or die(mysql_error());

	$row_formatos = mysql_query("SELECT empresa.spp, empresa.abreviacion, empresa.pais, COUNT(idformato_compras) AS 'num_contratos', ROUND(SUM(valor_total_contrato),2) AS 'total_contrato', SUM(total_a_pagar) AS 'total_cuota' FROM formato_compras INNER JOIN empresa ON formato_compras.idempresa = empresa.idempresa WHERE idtrim = '$idtrimestre'", $dspp) or die(mysql_error());
	$formatos = mysql_fetch_assoc($row_formatos);
	$valor_total_contratos = $formatos['total_contrato'];
	$valor_cuota_de_uso = $formatos['total_cuota'];


	$row_informe = mysql_query("SELECT total_valor_contrato, total_cuota_uso FROM informe_general WHERE idinforme_general = '$idinforme_general'", $dspp) or die(mysql_error());
	$informe = mysql_fetch_assoc($row_informe);

	$total_valor_contrato = $informe['total_valor_contrato'] + $valor_total_contratos;
	$total_cuota_uso = $informe['total_cuota_uso'] + $valor_cuota_de_uso;

	$updateSQL = sprintf("UPDATE $txt_trim SET $txt_estado_trim = %s, fecha_fin = %s, $txt_valor_contrato = %s, $txt_cuota_uso = %s WHERE $txt_idtrim = %s",
		GetSQLValueString($estatus_trim, "text"),
		GetSQLValueString($fecha_fin, "int"),
		GetSQLValueString($valor_total_contratos, "double"),
		GetSQLValueString($valor_cuota_de_uso, "double"),
		GetSQLValueString($idtrimestre, "text"));
	$actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

	$updateSQL = sprintf("UPDATE informe_general SET total_valor_contrato = %s, total_cuota_uso = %s WHERE idinforme_general = %s",
		GetSQLValueString($total_valor_contrato, "double"),
		GetSQLValueString($total_cuota_uso, "double"),
		GetSQLValueString($idinforme_general, "text"));
	$actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

	/********  SE ENVIA CORREO SOBRE REPORTE TRIMESTRAL  ************/
		//$idtrimestre = $_POST['idtrim'];
		$porcetaje_cuota = $configuracion['cuota_compradores'];
		//$tipo_empresa = $tipo_empresa;

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
	              '.$formatos['spp'].'
	            </td>
	            <td style="">
	              '.$formatos['abreviacion'].'
	            </td>
	            <td style="">
	              COMPRADOR FINAL
	            </td>
	            <td style="">
	              '.$formatos['pais'].'
	            </td>
	            <td style="">
	              '.$idtrimestre.'
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


	          ';
	          	$contador = 1;
				$row_formato_compras = mysql_query("SELECT * FROM formato_compras WHERE idtrim = '$idtrimestre'", $dspp) or die(mysql_error());
				$num_contratos = mysql_num_rows($row_formato_compras);
				while($formato_compras = mysql_fetch_assoc($row_formato_compras)){
				  $html .= '
					<tr>
					    <td>'.$contador.'</td>
					    <td>'.$formato_compras['spp'].'</td>
					    <td>'.$formato_compras['opp'].'</td>
					    <td>'.$formato_compras['pais'].'</td>
					    <td>'.$formato_compras['fecha_facturacion'].'</td>
					    <td>'.$formato_compras['primer_intermediario'].'</td>
					    <td>'.$formato_compras['segundo_intermediario'].'</td>
					    <td>'.$formato_compras['clave_contrato'].'</td>
					    <td>'.$formato_compras['fecha_contrato'].'</td>
					    <td>'.$formato_compras['producto_general'].'</td>
					    <td>'.$formato_compras['producto_especifico'].'</td>
					    <td>'.$formato_compras['producto_terminado'].'</td>
					    <td>Se exporta: '.$formato_compras['se_exporta'].'</td>
					    <td>'.$formato_compras['unidad_cantidad_factura'].'</td>
					    <td>'.$formato_compras['cantidad_total_factura'].'</td>
					    <td>'.$formato_compras['precio_sustentable_minimo'].'</td>
					    <td>'.$formato_compras['reconocimiento_organico'].'</td>
					    <td>'.$formato_compras['incentivo_spp'].'</td>
					    <td>'.$formato_compras['otros_premios'].'</td>
					    <td>'.$formato_compras['precio_total_unitario'].'</td>
					    <td>'.$formato_compras['valor_total_contrato'].'</td>
					    <td>'.$formato_compras['cuota_uso_reglamento'].'</td>
					    <td>'.$formato_compras['total_a_pagar'].'</td>
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
	    $pdf_listo = $mpdf->Output('reporte_trimestral.pdf', 'S'); //reemplazamos la I por S(regresa el documento como string)

	    //$pdf_listo = chunk_split(base64_encode($mpdf));
	   // $nombre_archivo = 'reporte.pdf';

	/********/
		$asunto = 'D-SPP - Factura Informe Trimestral Compras';
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
				      <th scope="col" align="left" width="280">Detalle Reporte Trimestral de Compras SPP</th>
				    </tr>
				    <tr>
				      <td style="padding-top:10px;">           
				        La empresa <span style="color:red">'.$formatos['abreviacion'].'</span> ha finalizado el <span style="color:red">TRIMESTRE '.$num_trim.'</span>, a continuación se muestran una tabla con el resumen de las operaciones.
				        <hr>
				        <p style="color:#2c3e50;font-weight:bold">El Área de Certificación y Calidad SPP ha autorizado el siguiente Informe Trimestral de Compras, por favor proceda a generar la factura correspondiente(<small style="color:#7f8c8d">Se adjunta el PDF con los registro correspondientes al trimestre finalizado</small>).</p>
				        <p style="color:#2c3e50;font-weight:bold">Una vez creada la factura dar clic en el siguiente enlace para poder adjuntarla. <a href="http://localhost/D-SPP.ORG_2/procesar/facturacion.php?num='.$num_trim.'&trim='.$idtrimestre.'" style="color:red">Clic para poder adjuntar factura</a></p>
				      </td>

				    </tr>
				    <tr>
				      <td colspan="2">
				        <table style="border: 1px solid #ddd;border-collapse: collapse;font-size:12px;">
				          <tr style="border: 1px solid #ddd;border-collapse: collapse;">
				            <td colspan="7" style="text-align:center">Resumen de operaciones</td>
				          </tr>
				          <tr style="border: 1px solid #ddd;border-collapse: collapse;">
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">Empresa</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">Tipo de Empresa</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">Informe</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">Num. de Contratos</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">Valor total de los contratos</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">Cuota de uso aplicada acorde al año en curso</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">Total cuota de uso</td>
				          </tr>
				          <tr style="border: 1px solid #ddd;border-collapse: collapse;">
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">'.$formatos['abreviacion'].'</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">COMPRADOR FINAL</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">'.$idtrimestre.'</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">'.$num_contratos.'</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">'.$total_valor_contrato.'</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">'.$porcetaje_cuota.'%</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">'.$total_cuota_uso.'</td>
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

		$mail->AddAddress('yasser.midnight@gmail.com');


	    //$mail->Username = "soporte@d-spp.org";
	    //$mail->Password = "/aung5l6tZ";
	    $mail->Subject = utf8_decode($asunto);
	    $mail->Body = utf8_decode($mensaje_correo);
	    $mail->MsgHTML(utf8_decode($mensaje_correo));
	    //$mail->AddAttachment($pdf_listo, 'reporte.pdf');

	    $mail->addStringAttachment($pdf_listo, 'reporte_trimestral.pdf');
	    $mail->Send();
	    $mail->ClearAddresses();
		///se envia correo al area de certificacion para corroborar la informacion


	echo "<script>alert('Se ha enviado la información al area de ADMINISTRACIÓN para poder generar la factura');</script>";
	$aprobado = 1;
	if($txt_trim == 'trim4'){
		//revisamos si el trim4 ha finalizado, entonces cambiamos el estatus del INFORME GENERAL a FINALIZADO ya que se han concluido los 4 trimestres
		//Tambien se agregar el monto de los 4 informes trimestrales dentro del TOTAL DEL INFORME GENERAL
		$updateSQL = sprintf("UPDATE informe_general SET estado_informe = %s WHERE idinforme_general = %s",
			GetSQLValueString('FINALIZADO', "text"),
			GetSQLValueString($idinforme_general, "text"));
		$actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());
	}
}
/// TERMINA APROBAR REPORTE  ///

//// INICIA ENVIAR FACTURA //// 
if(isset($_POST['enviar_factura']) && $_POST['enviar_factura'] == 1){

	$fecha = time();
	$anio_actual = date('Y', time());
	//$idinforme_general = $_GET['informe'];
	$num_trim = $_POST['num_trim'];
	$txt_trim = 'trim'.$num_trim;
	$txt_idtrim = 'idtrim'.$num_trim;
	//$txt_valor_contrato = 'valor_contrato_trim'.$num_trim;
	//$txt_cuota_uso = 'cuota_uso_trim'.$num_trim;
	//$txt_estado_trim = 'estado_trim'.$num_trim;

	$idtrimestre = $_POST['idtrimestre'];

	$row_configuracion = mysql_query("SELECT * FROM porcentaje_ajuste WHERE anio = $anio_actual", $dspp) or die(mysql_error());
	$configuracion = mysql_fetch_assoc($row_configuracion);

	$row_trim = mysql_query("SELECT * FROM $txt_trim WHERE $txt_idtrim = '$idtrimestre'", $dspp) or die(mysql_error());

	$row_formatos = mysql_query("SELECT empresa.spp, empresa.abreviacion, empresa.pais, COUNT(idformato_compras) AS 'num_contratos', ROUND(SUM(valor_total_contrato),2) AS 'total_contrato', SUM(total_a_pagar) AS 'total_cuota' FROM formato_compras INNER JOIN empresa ON formato_compras.idempresa = empresa.idempresa WHERE idtrim = '$idtrimestre'", $dspp) or die(mysql_error());
	$formatos = mysql_fetch_assoc($row_formatos);
	$valor_total_contratos = $formatos['total_contrato'];
	$valor_cuota_de_uso = $formatos['total_cuota'];



	/*
	$txt_idtrim = 'idtrim'.$_GET['trim'];
	$txt_numero_trim = 'trim'.$_GET['trim'];
	$txt_estado_trim = 'estado_'.$txt_numero_trim;
	$txt_total_trim = 'total_'.$txt_numero_trim;
	$txt_valor_contrato = 'valor_contrato_'.$txt_numero_trim;
	$txt_cuota_uso = 'cuota_uso_'.$txt_numero_trim;
	$estatus_trim = 'FINALIZADO';
	*/
	//$estatus_trim = 'FINALIZADO';


	$txt_idtrim = 'idtrim'.$_POST['num_trim'];
	$num_trim = 'trim'.$_POST['num_trim'];
	$txt_factura = 'factura_trim'.$_POST['num_trim'];
	$txt_estatus = 'estatus_factura_trim'.$_POST['num_trim'];

	///cargamos y guardamos la factura
	$rutaArchivo = "../../archivos/admArchivos/facturas/";
	if(!empty($_FILES['factura_trimestre']['name'])){
	  $_FILES["factura_trimestre"]["name"];
	    move_uploaded_file($_FILES["factura_trimestre"]["tmp_name"], $rutaArchivo.$fecha."_".$_FILES["factura_trimestre"]["name"]);
	    $archivo_factura = $rutaArchivo.basename($fecha."_".$_FILES["factura_trimestre"]["name"]);
	}else{
		$archivo_factura = NULL;
	}
	$estatus_factura = 'ENVIADA';

	$updateSQL = sprintf("UPDATE $num_trim SET $txt_factura = %s, $txt_estatus = %s WHERE $txt_idtrim = %s",
		GetSQLValueString($archivo_factura, "text"),
		GetSQLValueString($estatus_factura, "text"), 
		GetSQLValueString($_GET['trim'], "text"));
	$actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());


	/********/
		$asunto = 'D-SPP - Factura Informe Trimestral Compras';
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
				      <th scope="col" align="left" width="280">Factura - Reporte Trimestral de Compras SPP</th>
				    </tr>
				    <tr>
				      <td style="padding-top:10px;">           
				        <p style="color:#2c3e50;font-weight:bold">El Área de Adminsitración de SPP Global ha cargado la factura correspondiente al Informe Trimestral de Compras, por favor proceda a realizar el pago correspondiente por la cantidad de: '.$valor_cuota_de_uso.' USD</small>).</p>
				    <p>
				      Se anexan los siguiente documentos:
				      <ol>
				        <li>Factura</li>
				        <li>Reglamento de Costos</li>
				        <li>Datos bancarios</li>
				      </ol>
				    </p>
				        <p style="color:#2c3e50;font-weight:bold">Una vez realizado el pago por favor proceda a realizar las siguientes acciones</p>
				         <ol>
				           <li>Ingresar en su cuenta de Empresa dentro del sistema D-SPP.</li>
				           <li></li>
				           <li></li>
				         </ol>
				      </td>

				    </tr>
				    <tr>
				      <td colspan="2">
				        <table style="border: 1px solid #ddd;border-collapse: collapse;font-size:12px;">
				          <tr style="border: 1px solid #ddd;border-collapse: collapse;">
				            <td colspan="7" style="text-align:center">Resumen de operaciones</td>
				          </tr>
				          <tr style="border: 1px solid #ddd;border-collapse: collapse;">
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">Empresa</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">Tipo de Empresa</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">Informe</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">Num. de Contratos</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">Valor total de los contratos</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">Cuota de uso aplicada acorde al año en curso</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">Total cuota de uso</td>
				          </tr>
				          <tr style="border: 1px solid #ddd;border-collapse: collapse;">
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">'.$formatos['abreviacion'].'</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">COMPRADOR FINAL</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">'.$idtrimestre.'</td>
						    <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">'.$formatos['num_contratos'].'</td>
						    <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">'.$valor_total_contratos.'</td>
						    <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">'.$configuracion['cuota_compradores'].'%'.'</td>
						    <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">'.$valor_cuota_de_uso.'</td>
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
		$row_documentacion = mysql_query("SELECT * FROM documentacion WHERE nombre = 'Datos Bancarios SPP'", $dspp) or die(mysql_error());
		$documentacion = mysql_fetch_assoc($row_documentacion);

		$mail->AddAttachment($archivo_factura);
		$mail->AddAttachment($documentacion['archivo']);
		$mail->AddAddress('yasser.midnight@gmail.com');


	    //$mail->Username = "soporte@d-spp.org";
	    //$mail->Password = "/aung5l6tZ";
	    $mail->Subject = utf8_decode($asunto);
	    $mail->Body = utf8_decode($mensaje_correo);
	    $mail->MsgHTML(utf8_decode($mensaje_correo));
	    //$mail->AddAttachment($pdf_listo, 'reporte.pdf');

	    //$mail->addStringAttachment($pdf_listo, 'reporte_trimestral.pdf');
	    if($mail->Send()){
	    	$mail->ClearAddresses();
			echo "<script>alert('Se ha enviado la factura y notificado por email a la empresa $formatos[abreviacion]');</script>";
	    }else{
			echo "<script>alert('No se pudo enviar el correo, por favor ponerse en contacto con el area de soporte);</script>";
	    }
		///se envia correo al area de certificacion para corroborar la informacion
}

//// TERMINA ENVIAR FACTURA ////

$row_informes = mysql_query("SELECT informe_general.*, trim1.estado_trim1, trim1.valor_contrato_trim1, trim1.cuota_uso_trim1, trim1.estatus_factura_trim1, trim2.estado_trim2, trim2.valor_contrato_trim2, trim2.cuota_uso_trim2, trim2.estatus_factura_trim2, trim3.estado_trim3, trim3.valor_contrato_trim3, trim3.cuota_uso_trim3, trim3.estatus_factura_trim3, trim4.estado_trim4, trim4.valor_contrato_trim4, trim4.cuota_uso_trim4, trim4.estatus_factura_trim4, empresa.abreviacion FROM informe_general INNER JOIN empresa ON informe_general.idempresa = empresa.idempresa LEFT JOIN trim1 ON informe_general.trim1 = trim1.idtrim1 LEFT JOIN trim2 ON informe_general.trim2 = trim2.idtrim2 LEFT JOIN trim3 ON informe_general.trim3 = trim3.idtrim3 LEFT JOIN trim4 ON informe_general.trim4 = trim4.idtrim4", $dspp) or die(mysql_error());
$total_informes = mysql_num_rows($row_informes);
//$plataformas_spp = array('Ecuador', 'Perú', 'Colombia', 'Guatemala');

$row_plataformas = mysql_query("SELECT * FROM plataformas_spp", $dspp) or die(mysql_errno());

function redondear_dos_decimal($valor) { 
   $float_redondeado=round($valor * 100) / 100; 
   return $float_redondeado; 
}
?>
<hr>
<div class="row">
	<div class="col-md-12">
		<table class="table table-bordered table-hover table-condensed" style="font-size:12px;">
			<thead>
				<tr>
					<td style="border-style:hidden;">
						<span class="btn btn-xs btn-info glyphicon glyphicon-time" aria-hidden="true"></span> = Aun no se ha cargado comprobante de pago
					</td>
					<td style="border-style:hidden;"><span class="btn btn-xs btn-info glyphicon glyphicon-open-file"></span> = Factura enviada</td>
					<td style="border-style:hidden;"><img src="../../img/circulo_verde.jpg" alt=""> Activo</td>
				</tr>
				<tr>
					<td style="border-style:hidden;">
						<span class="btn btn-xs btn-info glyphicon glyphicon-ok" aria-hidden="true"></span> Pagado
					</td>
					<td style="border-style:hidden;"><img src="../../img/circulo_rojo.jpg" alt=""> Finalizado</span></td>
				</tr>

				<tr class="warning">
					<th>	
						Año: 
						<select name="anio_reporte">
							<option>Todos</option>
							<option>2017</option>
							<option>2016</option>
						</select>
					</th>
					<th class="text-center" colspan="7">Resumen cuota de uso</th>
				</tr>
				<tr>
					<th class="text-center">Año</th>
					<th class="text-center">ID informe general</th>
					<!--<th class="text-center">Estado</th>-->
					<th class="text-center">Empresa</th>
					<th class="text-center">Trimestre 1</th>
					<th class="text-center">Trimestre 2</th>
					<th class="text-center">Trimestre 3</th>
					<th class="text-center">Trimestre 4</th>
					<th class="text-center">Total Cuota de uso</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$total = 0;
				$cuota_uso_trim1 = 0;
				$cuota_uso_trim2 = 0;
				$cuota_uso_trim3 = 0;
				$cuota_uso_trim4 = 0;
				$total_final = 0;
				if($total_informes == 0){
					echo '<tr><td colspan="7">No se encontraron registros</td></tr>';
				}else{
					while($informes = mysql_fetch_assoc($row_informes)){
						
						$cuota_uso_trim1 += $informes['cuota_uso_trim1'];
						$cuota_uso_trim2 += $informes['cuota_uso_trim2'];
						$cuota_uso_trim3 += $informes['cuota_uso_trim3'];
						$cuota_uso_trim4 += $informes['cuota_uso_trim4'];
						$total_final = $cuota_uso_trim1 + $cuota_uso_trim2 + $cuota_uso_trim3 + $cuota_uso_trim4;
						echo "<tr>";
							echo '<td>'.date('Y',$informes['ano']).'</td>';
							if($informes['estado_informe'] == 'ACTIVO'){
								echo '<td><img src="../../img/circulo_verde.jpg"> <a href="?REPORTES&informe_compras='.$informes['idinforme_general'].'"><span class="glyphicon glyphicon-list-alt"></span> '.$informes['idinforme_general'].'</a></td>';
							}else if($informes['estado_informe'] == 'FINALIZADO'){
								echo '<td><img src="../../img/circulo_rojo.jpg"> <a href="?REPORTES&informe_compras='.$informes['idinforme_general'].'"><span class="glyphicon glyphicon-list-alt"></span> '.$informes['idinforme_general'].'</a></td>';
							}
							//echo '<td><a href="?REPORTES&informe_compras='.$informes['idinforme_general'].'"><span class="glyphicon glyphicon-list-alt"></span> '.$informes['idinforme_general'].'</a></td>';
							//echo '<td>'.$informes['estado_informe'].'</td>';
							echo '<td><a href="?EMPRESAS&detail&idempresa='.$informes['idempresa'].'">'.$informes['abreviacion'].'</a></td>';
							echo '<td>'; //// TRIMESTRE 1
							?>
								<button type="button" class="btn btn-xs btn-primary" data-toggle="modal" data-target="<?php echo "#trim1".$informes['trim1']; ?>"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></button>

								<div id="<?php echo "trim1".$informes['trim1']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
									<div class="modal-dialog modal-lg" role="document">
									  <div class="modal-content">
									    <div class="modal-header">
									      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									      <h4 class="text-center modal-title" id="myModalLabel">Administración Trimestre 1</h4>
									    </div>
									    <div class="modal-body">
											<div class="row">
												<div class="col-md-4">
													<h4 class="alert alert-info" style="padding:5px;">Reporte Trimestral</h4>
													<form action="" method="POST">
														<?php 
														if($informes['estado_trim1'] == 'EN ESPERA'){
														?>
															<p>Para poder aprobar el reporte trimestral debe dar clic en el siguiente boton</p>
															<button type="submit" class="btn btn-sm btn-success" style="width:100%" name="aprobar_reporte" value="SI">Aprobar Reporte</button>
															<input type="text" name="num_trim" value="1">
															<input type="text" name="idtrimestre" value="<?php echo $informes['trim1']; ?>">
															<input type="text" name="idinforme_general" value="<?php echo $informes['idinforme_general']; ?>">
														<?php
														}else if($informes['estado_trim1'] == 'APROBADO'){
															echo "El informe trimestral ha sido aprobado";
														}else{
															echo "Aun no esta disponible esta sección";
														}
														 ?>

													</form>
													
												</div>
												<div class="col-md-4">
													<h4 class="alert alert-warning" style="padding:5px;">Factura</h4>
													<?php
													if($informes['estado_trim1'] == 'APROBADO'){
													?>
														<form action="" method="POST">
															<div class="form-group">
															    <label for="exampleInputEmail1">Cargar Factura</label>
																<input type="file" class="form-control" id="factura" name="factura">
															</div>
															<button type="submit" class="btn btn-sm btn-success" style="width:100%" name="enviar_factura" value="1">Enviar Factura</button>
															<input type="text" name="num_trim" value="1">
															<input type="text" name="idtrimestre" value="<?php echo $informes['trim1']; ?>">
															<input type="text" name="idinforme_general" value="<?php echo $informes['idinforme_general']; ?>">
														</form>
													<?php
													}else{
													?>
														Aun no se ha aprobado
													<?php
													}
													 ?>

												</div>
												<div class="col-md-4" style="padding:5px;">
													<h4 class="alert alert-info">Acreditar pago</h4>
												</div>
											</div>
									    </div>
									    <div class="modal-footer">
									      <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
									      <!--<button type="button" class="btn btn-primary">Guardar Cambios</button>-->
									    </div>
									  </div>
									</div>
								</div>

							<?php
								
								if($informes['estado_trim1'] == 'ACTIVO' || $informes['estado_trim1'] == 'EN ESPERA' || $informes['estado_trim1'] == 'APROBADO'){
									echo '<img src="../../img/circulo_verde.jpg">';
									if($informes['estatus_factura_trim1'] == 'ENVIADA'){
										echo "<span class='btn btn-xs btn-info glyphicon glyphicon-open-file'></span>";
									}
									echo ' $'.$informes['cuota_uso_trim1'].' USD';
								}else if($informes['estado_trim1'] == 'FINALIZADO'){
									echo '<img src="../../img/circulo_rojo.jpg"> $'.$informes['cuota_uso_trim1'].' USD';
								}
							echo '</td>';

							echo '<td>'; //// TRIMESTRE 2
								if($informes['estado_trim2'] == 'ACTIVO' || $informes['estado_trim2'] == 'EN ESPERA' || $informes['estado_trim1'] == 'APROBADO'){
									echo '<img src="../../img/circulo_verde.jpg"> $'.$informes['cuota_uso_trim2'].' USD';
								}else if($informes['estado_trim2'] == 'FINALIZADO'){
									echo '<img src="../../img/circulo_rojo.jpg"> $'.$informes['cuota_uso_trim2'].' USD';
								}
							echo '</td>';

							echo '<td>'; /// TRIMESTRE 3
								if($informes['estado_trim3'] == 'ACTIVO' || $informes['estado_trim3'] == 'EN ESPERA' || $informes['estado_trim1'] == 'APROBADO'){
									echo '<img src="../../img/circulo_verde.jpg"> $'.$informes['cuota_uso_trim3'].' USD';
								}else if($informes['estado_trim3'] == 'FINALIZADO'){
									echo '<img src="../../img/circulo_rojo.jpg"> $'.$informes['cuota_uso_trim3'].' USD';
								}
							echo '</td>';

							echo '<td>';  /// TRIMESTRE 4
								if($informes['estado_trim4'] == 'ACTIVO' || $informes['estado_trim4'] == 'EN ESPERA' || $informes['estado_trim1'] == 'APROBADO'){
									echo '<img src="../../img/circulo_verde.jpg"> $'.$informes['cuota_uso_trim4'].' USD';
								}else if($informes['estado_trim4'] == 'FINALIZADO'){
									echo '<img src="../../img/circulo_rojo.jpg"> $'.$informes['cuota_uso_trim4'].' USD';
								}
							echo '</td>';

							echo '<td><a href="?REPORTES&informe_compras&detalle_total='.$informes['idinforme_general'].'"><span class="glyphicon glyphicon-search"></span> '.$informes['total_cuota_uso'].' USD</a></td>';
						echo '</tr>';
					}
					echo '<tr>';
						echo '<td colspan="3" style="text-align:right"><b>Suma total</b></td>';
						echo '<td style="color:#e74c3c;">'.$cuota_uso_trim1.'</td>';
						echo '<td style="color:#e74c3c;">'.$cuota_uso_trim2.'</td>';
						echo '<td style="color:#e74c3c;">'.$cuota_uso_trim3.'</td>';
						echo '<td style="color:#e74c3c;">'.$cuota_uso_trim4.'</td>';
						echo '<td style="color:#e74c3c;">'.$total_final.' USD</td>';
					echo '</tr>';
					//echo '<tr><td colspan="9"><b>Suma total: <span style="color:#e74c3c">'.$total.'</span> USD</b></td></tr>';
				}
				 ?>
			</tbody>
		</table>
	</div>