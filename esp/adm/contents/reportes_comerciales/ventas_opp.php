<?php
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

	$row_formatos = mysql_query("SELECT opp.spp, opp.abreviacion, opp.pais, COUNT(idformato_ventas) AS 'num_contratos', ROUND(SUM(valor_total_contrato),2) AS 'total_contrato', SUM(total_a_pagar) AS 'total_cuota' FROM formato_ventas INNER JOIN opp ON formato_ventas.idopp = opp.idopp WHERE idtrim = '$idtrimestre'", $dspp) or die(mysql_error());
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
		$porcetaje_cuota = $configuracion['cuota_productores'];
		//$tipo_opp = $tipo_opp;

	    //$pdf_listo = chunk_split(base64_encode($mpdf));
	   // $nombre_archivo = 'reporte.pdf';
		$txt_reporte = 'reporte_trim'.$num_trim;
		$row_trim = mysql_query("SELECT * FROM $txt_trim WHERE $txt_idtrim = '".$idtrimestre."'",$dspp) or die(mysql_error());
		$info_trim = mysql_fetch_assoc($row_trim);
		$reporte = $info_trim[$txt_reporte];
	/********/
		$asunto = 'D-SPP - Factura Informe Trimestral Ventas';
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
				        La opp <span style="color:red">'.$formatos['abreviacion'].'</span> ha finalizado el <span style="color:red">TRIMESTRE '.$num_trim.'</span>, a continuación se muestran una tabla con el resumen de las operaciones.
				        <hr>
				        <p style="color:#2c3e50;font-weight:bold">El Área de Certificación y Calidad SPP ha autorizado el siguiente Informe Trimestral de Ventas, por favor proceda a generar la factura correspondiente(<small style="color:#7f8c8d">Se adjunta el PDF con los registro correspondientes al trimestre finalizado</small>).</p>
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
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">Organización</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">Informe</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">Num. de Contratos</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">Valor total de los contratos</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">Cuota de uso aplicada acorde al año en curso</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">Total cuota de uso</td>
				          </tr>
				          <tr style="border: 1px solid #ddd;border-collapse: collapse;">
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">'.$formatos['abreviacion'].'</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">'.$idtrimestre.'</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">'.$num_contratos.'</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">'.number_format($formatos['total_contrato'],2).'</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">'.$porcetaje_cuota.'%</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">'.number_format($formatos['total_cuota'],2).'</td>
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

	    $mail->AddAttachment($reporte);
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

	$row_formatos = mysql_query("SELECT opp.spp, opp.abreviacion, opp.pais, COUNT(idformato_ventas) AS 'num_contratos', ROUND(SUM(valor_total_contrato),2) AS 'total_contrato', SUM(total_a_pagar) AS 'total_cuota' FROM formato_ventas INNER JOIN opp ON formato_ventas.idopp = opp.idopp WHERE idtrim = '$idtrimestre'", $dspp) or die(mysql_error());
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
	$idtrimestre = $_POST['idtrimestre'];

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
		GetSQLValueString($idtrimestre, "text"));
	$actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());


	/********/
		$asunto = 'D-SPP - Factura Informe Trimestral Ventas';
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
				      <th scope="col" align="left" width="280">Factura - Reporte Trimestral de Ventas SPP</th>
				    </tr>
				    <tr>
				      <td style="padding-top:10px;">           
				        <p style="color:#2c3e50;font-weight:bold">El Área de Adminsitración de SPP Global ha cargado la factura correspondiente al Informe Trimestral de Ventas, por favor proceda a realizar el pago correspondiente por la cantidad de: <span style="color:red">'.number_format($valor_cuota_de_uso,2).' USD</span></small>).</p>
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
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">Organización</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">Informe</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">Num. de Contratos</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">Valor total de los contratos</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">Cuota de uso aplicada acorde al año en curso</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">Total cuota de uso</td>
				          </tr>
				          <tr style="border: 1px solid #ddd;border-collapse: collapse;">
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">'.$formatos['abreviacion'].'</td>
				            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">'.$idtrimestre.'</td>
						    <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">'.$formatos['num_contratos'].'</td>
						    <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">'.number_format($valor_total_contratos,2).'</td>
						    <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">'.$configuracion['cuota_productores'].'%'.'</td>
						    <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">'.number_format($valor_cuota_de_uso,2).'</td>
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
			echo "<script>alert('Se ha enviado la factura y notificado por email a la opp $formatos[abreviacion]');</script>";
	    }else{
			echo "<script>alert('No se pudo enviar el correo, por favor ponerse en contacto con el area de soporte);</script>";
	    }
		///se envia correo al area de certificacion para corroborar la informacion
}
//// TERMINA ENVIAR FACTURA ////

//// INICIA APROBAR O DENEGAR COMPROBANTE PAGO
if(isset($_POST['aprobar_comprobante']) && $_POST['aprobar_comprobante'] == 1){

	$idtrimestre = $_POST['idtrimestre'];	
	$num = $_POST['num_trimestre'];
	$txt_id = 'idtrim'.$num;
	$txt_trim = 'trim'.$num;
	$txt_estatus_trim = 'estado_trim'.$num;
	$txt_estatus_factura = 'estatus_factura_trim'.$num;
	$txt_estatus_comprobante = 'estatus_comprobante_trim'.$num;
	$estatus_trim = 'FINALIZADO';
	$estatus_factura = 'PAGADA';
	$estatus_comprobante = 'APROBADO';

	$updateSQL = sprintf("UPDATE $txt_trim SET $txt_estatus_trim = %s, $txt_estatus_factura = %s, $txt_estatus_comprobante = %s WHERE $txt_id = %s",
		GetSQLValueString($estatus_trim, "text"),
		GetSQLValueString($estatus_factura, "text"),
		GetSQLValueString($estatus_comprobante, "text"),
		GetSQLValueString($idtrimestre, "text"));
	$actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

	/// se envia mensaje a la opp
	/********/
		$asunto = 'D-SPP - Pago Informe Trimestral de Ventas';
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
				      <th scope="col" align="left" width="280">Factura - Reporte Trimestral de Ventas SPP</th>
				    </tr>
				    <tr>
				      <td style="padding-top:10px;">           
				        <p style="color:#2c3e50;font-weight:bold">Se ha aprobado el comprobante de pago.</p>
				      </td>
				    </tr>
				</tbody>
			</table>

			</body>
			</html>
		';

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
			echo "<script>alert('Se ha notificado a la opp');</script>";
	    }else{
			echo "<script>alert('No se pudo enviar el correo, por favor ponerse en contacto con el area de soporte);</script>";
	    }


}
//// TERMINA APROBAR O DENEGAR COMPROBANTE PAGO


$row_informes = mysql_query("SELECT informe_general.*, trim1.*, trim2.*, trim3.*, trim4.*, opp.idopp, opp.abreviacion FROM informe_general INNER JOIN opp ON informe_general.idopp = opp.idopp LEFT JOIN trim1 ON informe_general.trim1 = trim1.idtrim1 LEFT JOIN trim2 ON informe_general.trim2 = trim2.idtrim2 LEFT JOIN trim3 ON informe_general.trim3 = trim3.idtrim3 LEFT JOIN trim4 ON informe_general.trim4 = trim4.idtrim4", $dspp) or die(mysql_error());
$total_informes = mysql_num_rows($row_informes);
//$plataformas_spp = array('Ecuador', 'Perú', 'Colombia', 'Guatemala');

$row_plataformas = mysql_query("SELECT * FROM plataformas_spp", $dspp) or die(mysql_errno());

function redondear_dos_decimal($valor) { 
   $float_redondeado=round($valor * 100) / 100; 
   return $float_redondeado; 
}
?>

<div class="row">
	<div class="col-md-12">
		<table class="table table-bordered table-hover table-condensed" style="font-size:12px;">
			<thead>
				<tr>
					<td style="border-style:hidden;"><span class="disabled btn btn-xs btn-info glyphicon glyphicon-open-file"></span> = Factura enviada</td>
					<td style="border-style:hidden;"><img src="../../img/circulo_verde.jpg" alt=""> Activo</td>
					<td style="border-style:hidden;"><span class="disabled btn btn-xs btn-info glyphicon glyphicon-picture" aria-hidden="true"></span> = Comprobante de pago cargado</td>
				</tr>
				<tr>
					<td style="border-style:hidden;">
						<span class="disabled btn btn-xs btn-info glyphicon glyphicon-ok" aria-hidden="true"></span> Pagado
					</td>
					<td style="border-style:hidden;"><img src="../../img/circulo_rojo.jpg" alt=""> Finalizado</span></td>
				</tr>

				<tr class="info">
					<th>	
						Año: 
						<select name="anio_reporte">
							<option>Todos</option>
							<option>2017</option>
							<option>2016</option>
						</select>
					</th>
					<th class="text-center" colspan="7"><h4>Resumen Cuota de Uso - Informes Ventas</h4></th>
				</tr>
				<tr>
					<th class="text-center">Año</th>
					<th class="text-center">ID informe general</th>
					<!--<th class="text-center">Estado</th>-->
					<th class="text-center">Organización</th>
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
					echo '<tr><td colspan="8">No se encontraron registros</td></tr>';
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
							echo '<td><a href="?OPP&detail&idopp='.$informes['idopp'].'">'.$informes['abreviacion'].'</a></td>';
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
													<h4 class="text-center alert alert-info" style="padding:5px;">Reporte Trimestral</h4>
													<form action="" method="POST">
														<?php 
														if($informes['estado_trim1'] == 'EN ESPERA'){
														?>
															<p>Para poder aprobar el reporte trimestral debe dar clic en el siguiente boton</p>
															<button type="submit" class="btn btn-sm btn-success" style="width:100%" name="aprobar_reporte" value="SI">Aprobar Reporte</button>
															<input type="hidden" name="num_trim" value="1">
															<input type="hidden" name="idtrimestre" value="<?php echo $informes['trim1']; ?>">
															<input type="hidden" name="idinforme_general" value="<?php echo $informes['idinforme_general']; ?>">
														<?php
														}else if($informes['estado_trim1'] == 'APROBADO' || $informes['estado_trim1'] == 'FINALIZADO'){
															echo "El informe trimestral ha sido aprobado";
														}else{
															echo "Aun no esta disponible esta sección";
														}
														 ?>

													</form>
													
												</div>
												<div class="col-md-4">
													<h4 class="text-center alert alert-warning" style="padding:5px;">Factura</h4>
													<?php
													if($informes['estado_trim1'] == 'APROBADO' || $informes['estado_trim1'] == 'FINALIZADO'){
														if(isset($informes['estatus_factura_trim1'])){
														?>
															<p>Se ha enviado la factura</p>
															<a class="btn btn-sm btn-success" style="width:100%" href="<?php echo $informes['factura_trim1']; ?>" target="_new">Descargar Factura</a>
														<?php
														}else{
														?>
															<form action="" method="POST" enctype="multipart/form-data">
																<div class="form-group">
																    <label for="exampleInputEmail1">Cargar Factura</label>
																	<input type="file" class="form-control" id="factura" name="factura_trimestre">
																</div>
																<button type="submit" class="btn btn-sm btn-success" style="width:100%" name="enviar_factura" value="1">Enviar Factura</button>
																<input type="hidden" name="num_trim" value="1">
																<input type="hidden" name="idtrimestre" value="<?php echo $informes['trim1']; ?>">
																<input type="hidden" name="idinforme_general" value="<?php echo $informes['idinforme_general']; ?>">
															</form>
														<?php
														}
													}else{
													?>
														Se debe de aprobar primero el reporte trimestral.
													<?php
													}
													 ?>

												</div>
												<div class="col-md-4">
													<h4 class="text-center alert alert-info" style="padding:5px;">Acreditar pago</h4>
													<?php 
													if(isset($informes['estatus_comprobante_trim1']) && $informes['estatus_comprobante_trim1'] == 'ENVIADO'){
													?>
													<form action="" method="POST">
														<div class="row">
															<div class="col-xs-6"><button class="btn btn-sm btn-success" style="width:100%" type="submit" name="aprobar_pago" value="1"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Aprobar pago</button></div>
															<div class="col-xs-6"><button class="btn btn-sm btn-danger" style="width:100%" type="submit" name="rechazar_pago" value="2"><span class="glyphicon glyphicon-remove" aria-hidden="trie"></span> Rechazar pago</button></div>
															<input type="hidden" name="num_trimestre" value="1">
															<input type="hidden" name="idtrimestre" value="<?php echo $informes['trim1']; ?>">
															<input type="hidden" name="aprobar_comprobante" value="1">
														</div>

													</form>
													<?php
													}else if($informes['estatus_comprobante_trim1'] == 'APROBADO'){
														echo "<p>Se ha aprobado el comprobante de pago</p>";
														echo "<a href='".$informes['comprobante_pago_trim1']."' class='btn btn-sm btn-info' style='width:100%' target='_new'>Descargar Comprobante de Pago</a>";
													}else{
														echo "Aun no se ha cargado el comprobante de pago";
													}
													 ?>
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
								if($informes['estatus_factura_trim1'] == 'ENVIADA' || $informes['estatus_factura_trim1'] == 'PAGADA'){
									///boton para descargar factura
									echo "<a href='$informes[factura_trim1]' target='_new' data-toggle='tooltip' title='Descargar factura'><span class='btn btn-xs btn-info glyphicon glyphicon-open-file'></span></a>";
									//echo "<span class='btn btn-xs btn-info glyphicon glyphicon-open-file'></span>";
								}
								if($informes['estatus_comprobante_trim1'] == 'ENVIADO' || $informes['estatus_comprobante_trim1'] == 'APROBADO' || $informes['estatus_comprobante_trim1'] == 'FINALIZADO'){
									/// boton para descargar comprobante de pago
									echo "<a href='$informes[comprobante_pago_trim1]' target='_new' data-toggle='tooltip' title='Descargar comprobante de pago'><span class='btn btn-xs btn-info glyphicon glyphicon-picture'></span></a>";				
								}
								if($informes['estado_trim1'] == 'ACTIVO' || $informes['estado_trim1'] == 'EN ESPERA' || $informes['estado_trim1'] == 'APROBADO'){
									echo '<img src="../../img/circulo_verde.jpg">';
								}else if($informes['estado_trim1'] == 'FINALIZADO'){
									echo '<img src="../../img/circulo_rojo.jpg">';
								}
								
								echo ' $'.number_format($informes['cuota_uso_trim1'],2).' USD';
								if($informes['estatus_factura_trim1'] == 'PAGADA'){
									echo "<span class='disabled btn btn-xs btn-info glyphicon glyphicon-ok' aria-hidden='true'></span>";
								}
								if(isset($informes['reporte_trim1'])){
									echo '<a href="'.$informes['reporte_trim1'].'" target="_new"><img height="25px;" src="../../img/pdf.png" alt=""></a>';
								}
							echo '</td>';

							echo '<td>'; //// TRIMESTRE 2
							?>
								<button type="button" class="btn btn-xs btn-primary" data-toggle="modal" data-target="<?php echo "#trim2".$informes['trim2']; ?>"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></button>

								<div id="<?php echo "trim2".$informes['trim2']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
									<div class="modal-dialog modal-lg" role="document">
									  <div class="modal-content">
									    <div class="modal-header">
									      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									      <h4 class="text-center modal-title" id="myModalLabel">Administración Trimestre 2</h4>
									    </div>
									    <div class="modal-body">
											<div class="row">
												<div class="col-md-4">
													<h4 class="text-center alert alert-info" style="padding:5px;">Reporte Trimestral</h4>
													<form action="" method="POST">
														<?php 
														if($informes['estado_trim2'] == 'EN ESPERA'){
														?>
															<p>Para poder aprobar el reporte trimestral debe dar clic en el siguiente boton</p>
															<button type="submit" class="btn btn-sm btn-success" style="width:100%" name="aprobar_reporte" value="SI">Aprobar Reporte</button>
															<input type="hidden" name="num_trim" value="2">
															<input type="hidden" name="idtrimestre" value="<?php echo $informes['trim2']; ?>">
															<input type="hidden" name="idinforme_general" value="<?php echo $informes['idinforme_general']; ?>">
														<?php
														}else if($informes['estado_trim2'] == 'APROBADO' || $informes['estado_trim2'] == 'FINALIZADO'){
															echo "El informe trimestral ha sido aprobado";
														}else{
															echo "Aun no esta disponible esta sección";
														}
														 ?>

													</form>
													
												</div>
												<div class="col-md-4">
													<h4 class="text-center alert alert-warning" style="padding:5px;">Factura</h4>
													<?php
													if($informes['estado_trim2'] == 'APROBADO' || $informes['estado_trim2'] == 'FINALIZADO'){
														if(isset($informes['estatus_factura_trim2'])){
														?>
															<p>Se ha enviado la factura</p>
															<a class="btn btn-sm btn-success" style="width:100%" href="<?php echo $informes['factura_trim2']; ?>" target="_new">Descargar Factura</a>
														<?php
														}else{
														?>
															<form action="" method="POST" enctype="multipart/form-data">
																<div class="form-group">
																    <label for="exampleInputEmail1">Cargar Factura</label>
																	<input type="file" class="form-control" id="factura" name="factura_trimestre">
																</div>
																<button type="submit" class="btn btn-sm btn-success" style="width:100%" name="enviar_factura" value="1">Enviar Factura</button>
																<input type="hidden" name="num_trim" value="2">
																<input type="hidden" name="idtrimestre" value="<?php echo $informes['trim2']; ?>">
																<input type="hidden" name="idinforme_general" value="<?php echo $informes['idinforme_general']; ?>">
															</form>
														<?php
														}
													}else{
													?>
														Se debe de aprobar primero el reporte trimestral.
													<?php
													}
													 ?>

												</div>
												<div class="col-md-4">
													<h4 class="text-center alert alert-info" style="padding:5px;">Acreditar pago</h4>
													<?php 
													if(isset($informes['estatus_comprobante_trim2']) && $informes['estatus_comprobante_trim2'] == 'ENVIADO'){
													?>
													<form action="" method="POST">
														<div class="row">
															<div class="col-xs-6"><button class="btn btn-sm btn-success" style="width:100%" type="submit" name="aprobar_pago" value="1"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Aprobar pago</button></div>
															<div class="col-xs-6"><button class="btn btn-sm btn-danger" style="width:100%" type="submit" name="rechazar_pago" value="2"><span class="glyphicon glyphicon-remove" aria-hidden="trie"></span> Rechazar pago</button></div>
															<input type="hidden" name="num_trimestre" value="2">
															<input type="hidden" name="idtrimestre" value="<?php echo $informes['trim2']; ?>">
															<input type="hidden" name="aprobar_comprobante" value="1">
														</div>

													</form>
													<?php
													}else if($informes['estatus_comprobante_trim2'] == 'APROBADO'){
														echo "<p>Se ha aprobado el comprobante de pago</p>";
														echo "<a href='".$informes['comprobante_pago_trim2']."' class='btn btn-sm btn-info' style='width:100%' target='_new'>Descargar Comprobante de Pago</a>";
													}else{
														echo "Aun no se ha cargado el comprobante de pago";
													}
													 ?>
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
								if($informes['estatus_factura_trim2'] == 'ENVIADA' || $informes['estatus_factura_trim2'] == 'PAGADA'){
									///boton para descargar factura
									echo "<a href='$informes[factura_trim2]' target='_new' data-toggle='tooltip' title='Descargar factura'><span class='btn btn-xs btn-info glyphicon glyphicon-open-file'></span></a>";
									//echo "<span class='btn btn-xs btn-info glyphicon glyphicon-open-file'></span>";
								}
								if($informes['estatus_comprobante_trim2'] == 'ENVIADO' || $informes['estatus_comprobante_trim2'] == 'APROBADO' || $informes['estatus_comprobante_trim2'] == 'FINALIZADO'){
									/// boton para descargar comprobante de pago
									echo "<a href='$informes[comprobante_pago_trim2]' target='_new' data-toggle='tooltip' title='Descargar comprobante de pago'><span class='btn btn-xs btn-info glyphicon glyphicon-picture'></span></a>";				
								}
								if($informes['estado_trim2'] == 'ACTIVO' || $informes['estado_trim2'] == 'EN ESPERA' || $informes['estado_trim2'] == 'APROBADO'){
									echo '<img src="../../img/circulo_verde.jpg">';
								}else if($informes['estado_trim2'] == 'FINALIZADO'){
									echo '<img src="../../img/circulo_rojo.jpg">';
								}
								
								echo ' $'.number_format($informes['cuota_uso_trim2'],2).' USD';
								if($informes['estatus_factura_trim2'] == 'PAGADA'){
									echo "<span class='disabled btn btn-xs btn-info glyphicon glyphicon-ok' aria-hidden='true'></span>";
								}
								if(isset($informes['reporte_trim2'])){
									echo '<a href="'.$informes['reporte_trim2'].'" target="_new"><img height="25px;" src="../../img/pdf.png" alt=""></a>';
								}
							echo '</td>';

							echo '<td>'; /// TRIMESTRE 3
							?>
								<button type="button" class="btn btn-xs btn-primary" data-toggle="modal" data-target="<?php echo "#trim3".$informes['trim3']; ?>"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></button>

								<div id="<?php echo "trim3".$informes['trim3']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
									<div class="modal-dialog modal-lg" role="document">
									  <div class="modal-content">
									    <div class="modal-header">
									      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									      <h4 class="text-center modal-title" id="myModalLabel">Administración Trimestre 3</h4>
									    </div>
									    <div class="modal-body">
											<div class="row">
												<div class="col-md-4">
													<h4 class="text-center alert alert-info" style="padding:5px;">Reporte Trimestral</h4>
													<form action="" method="POST">
														<?php 
														if($informes['estado_trim3'] == 'EN ESPERA'){
														?>
															<p>Para poder aprobar el reporte trimestral debe dar clic en el siguiente boton</p>
															<button type="submit" class="btn btn-sm btn-success" style="width:100%" name="aprobar_reporte" value="SI">Aprobar Reporte</button>
															<input type="hidden" name="num_trim" value="3">
															<input type="hidden" name="idtrimestre" value="<?php echo $informes['trim3']; ?>">
															<input type="hidden" name="idinforme_general" value="<?php echo $informes['idinforme_general']; ?>">
														<?php
														}else if($informes['estado_trim3'] == 'APROBADO' || $informes['estado_trim3'] == 'FINALIZADO'){
															echo "El informe trimestral ha sido aprobado";
														}else{
															echo "Aun no esta disponible esta sección";
														}
														 ?>

													</form>
													
												</div>
												<div class="col-md-4">
													<h4 class="text-center alert alert-warning" style="padding:5px;">Factura</h4>
													<?php
													if($informes['estado_trim3'] == 'APROBADO' || $informes['estado_trim3'] == 'FINALIZADO'){
														if(isset($informes['estatus_factura_trim3'])){
														?>
															<p>Se ha enviado la factura</p>
															<a class="btn btn-sm btn-success" style="width:100%" href="<?php echo $informes['factura_trim3']; ?>" target="_new">Descargar Factura</a>
														<?php
														}else{
														?>
															<form action="" method="POST" enctype="multipart/form-data">
																<div class="form-group">
																    <label for="exampleInputEmail1">Cargar Factura</label>
																	<input type="file" class="form-control" id="factura" name="factura_trimestre">
																</div>
																<button type="submit" class="btn btn-sm btn-success" style="width:100%" name="enviar_factura" value="1">Enviar Factura</button>
																<input type="hidden" name="num_trim" value="3">
																<input type="hidden" name="idtrimestre" value="<?php echo $informes['trim3']; ?>">
																<input type="hidden" name="idinforme_general" value="<?php echo $informes['idinforme_general']; ?>">
															</form>
														<?php
														}
													}else{
													?>
														Se debe de aprobar primero el reporte trimestral.
													<?php
													}
													 ?>

												</div>
												<div class="col-md-4">
													<h4 class="text-center alert alert-info" style="padding:5px;">Acreditar pago</h4>
													<?php 
													if(isset($informes['estatus_comprobante_trim3']) && $informes['estatus_comprobante_trim3'] == 'ENVIADO'){
													?>
													<form action="" method="POST">
														<div class="row">
															<div class="col-xs-6"><button class="btn btn-sm btn-success" style="width:100%" type="submit" name="aprobar_pago" value="1"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Aprobar pago</button></div>
															<div class="col-xs-6"><button class="btn btn-sm btn-danger" style="width:100%" type="submit" name="rechazar_pago" value="2"><span class="glyphicon glyphicon-remove" aria-hidden="trie"></span> Rechazar pago</button></div>
															<input type="hidden" name="num_trimestre" value="3">
															<input type="hidden" name="idtrimestre" value="<?php echo $informes['trim3']; ?>">
															<input type="hidden" name="aprobar_comprobante" value="1">
														</div>

													</form>
													<?php
													}else if($informes['estatus_comprobante_trim3'] == 'APROBADO'){
														echo "<p>Se ha aprobado el comprobante de pago</p>";
														echo "<a href='".$informes['comprobante_pago_trim3']."' class='btn btn-sm btn-info' style='width:100%' target='_new'>Descargar Comprobante de Pago</a>";
													}else{
														echo "Aun no se ha cargado el comprobante de pago";
													}
													 ?>
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
								if($informes['estatus_factura_trim3'] == 'ENVIADA' || $informes['estatus_factura_trim3'] == 'PAGADA'){
									///boton para descargar factura
									echo "<a href='$informes[factura_trim3]' target='_new' data-toggle='tooltip' title='Descargar factura'><span class='btn btn-xs btn-info glyphicon glyphicon-open-file'></span></a>";
									//echo "<span class='btn btn-xs btn-info glyphicon glyphicon-open-file'></span>";
								}
								if($informes['estatus_comprobante_trim3'] == 'ENVIADO' || $informes['estatus_comprobante_trim3'] == 'APROBADO' || $informes['estatus_comprobante_trim3'] == 'FINALIZADO'){
									/// boton para descargar comprobante de pago
									echo "<a href='$informes[comprobante_pago_trim3]' target='_new' data-toggle='tooltip' title='Descargar comprobante de pago'><span class='btn btn-xs btn-info glyphicon glyphicon-picture'></span></a>";				
								}
								if($informes['estado_trim3'] == 'ACTIVO' || $informes['estado_trim3'] == 'EN ESPERA' || $informes['estado_trim3'] == 'APROBADO'){
									echo '<img src="../../img/circulo_verde.jpg">';
								}else if($informes['estado_trim3'] == 'FINALIZADO'){
									echo '<img src="../../img/circulo_rojo.jpg">';
								}
								
								echo ' $'.number_format($informes['cuota_uso_trim3'],2).' USD';
								if($informes['estatus_factura_trim3'] == 'PAGADA'){
									echo "<span class='disabled btn btn-xs btn-info glyphicon glyphicon-ok' aria-hidden='true'></span>";
								}
								if(isset($informes['reporte_trim3'])){
									echo '<a href="'.$informes['reporte_trim3'].'" target="_new"><img height="25px;" src="../../img/pdf.png" alt=""></a>';
								}
							echo '</td>';

							echo '<td>';  /// TRIMESTRE 4
							?>
								<button type="button" class="btn btn-xs btn-primary" data-toggle="modal" data-target="<?php echo "#trim4".$informes['trim4']; ?>"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></button>

								<div id="<?php echo "trim4".$informes['trim4']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
									<div class="modal-dialog modal-lg" role="document">
									  <div class="modal-content">
									    <div class="modal-header">
									      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									      <h4 class="text-center modal-title" id="myModalLabel">Administración Trimestre 4</h4>
									    </div>
									    <div class="modal-body">
											<div class="row">
												<div class="col-md-4">
													<h4 class="text-center alert alert-info" style="padding:5px;">Reporte Trimestral</h4>
													<form action="" method="POST">
														<?php 
														if($informes['estado_trim4'] == 'EN ESPERA'){
														?>
															<p>Para poder aprobar el reporte trimestral debe dar clic en el siguiente boton</p>
															<button type="submit" class="btn btn-sm btn-success" style="width:100%" name="aprobar_reporte" value="SI">Aprobar Reporte</button>
															<input type="hidden" name="num_trim" value="4">
															<input type="hidden" name="idtrimestre" value="<?php echo $informes['trim4']; ?>">
															<input type="hidden" name="idinforme_general" value="<?php echo $informes['idinforme_general']; ?>">
														<?php
														}else if($informes['estado_trim4'] == 'APROBADO' || $informes['estado_trim4'] == 'FINALIZADO'){
															echo "El informe trimestral ha sido aprobado";
														}else{
															echo "Aun no esta disponible esta sección";
														}
														 ?>

													</form>
													
												</div>
												<div class="col-md-4">
													<h4 class="text-center alert alert-warning" style="padding:5px;">Factura</h4>
													<?php
													if($informes['estado_trim4'] == 'APROBADO' || $informes['estado_trim4'] == 'FINALIZADO'){
														if(isset($informes['estatus_factura_trim4'])){
														?>
															<p>Se ha enviado la factura</p>
															<a class="btn btn-sm btn-success" style="width:100%" href="<?php echo $informes['factura_trim4']; ?>" target="_new">Descargar Factura</a>
														<?php
														}else{
														?>
															<form action="" method="POST" enctype="multipart/form-data">
																<div class="form-group">
																    <label for="exampleInputEmail1">Cargar Factura</label>
																	<input type="file" class="form-control" id="factura" name="factura_trimestre">
																</div>
																<button type="submit" class="btn btn-sm btn-success" style="width:100%" name="enviar_factura" value="1">Enviar Factura</button>
																<input type="hidden" name="num_trim" value="4">
																<input type="hidden" name="idtrimestre" value="<?php echo $informes['trim4']; ?>">
																<input type="hidden" name="idinforme_general" value="<?php echo $informes['idinforme_general']; ?>">
															</form>
														<?php
														}
													}else{
													?>
														Se debe de aprobar primero el reporte trimestral.
													<?php
													}
													 ?>

												</div>
												<div class="col-md-4">
													<h4 class="text-center alert alert-info" style="padding:5px;">Acreditar pago</h4>
													<?php 
													if(isset($informes['estatus_comprobante_trim4']) && $informes['estatus_comprobante_trim4'] == 'ENVIADO'){
													?>
													<form action="" method="POST">
														<div class="row">
															<div class="col-xs-6"><button class="btn btn-sm btn-success" style="width:100%" type="submit" name="aprobar_pago" value="1"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Aprobar pago</button></div>
															<div class="col-xs-6"><button class="btn btn-sm btn-danger" style="width:100%" type="submit" name="rechazar_pago" value="2"><span class="glyphicon glyphicon-remove" aria-hidden="trie"></span> Rechazar pago</button></div>
															<input type="hidden" name="num_trimestre" value="4">
															<input type="hidden" name="idtrimestre" value="<?php echo $informes['trim4']; ?>">
															<input type="hidden" name="aprobar_comprobante" value="1">
														</div>

													</form>
													<?php
													}else if($informes['estatus_comprobante_trim4'] == 'APROBADO'){
														echo "<p>Se ha aprobado el comprobante de pago</p>";
														echo "<a href='".$informes['comprobante_pago_trim4']."' class='btn btn-sm btn-info' style='width:100%' target='_new'>Descargar Comprobante de Pago</a>";
													}else{
														echo "Aun no se ha cargado el comprobante de pago";
													}
													 ?>
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
								if($informes['estatus_factura_trim4'] == 'ENVIADA' || $informes['estatus_factura_trim4'] == 'PAGADA'){
									///boton para descargar factura
									echo "<a href='$informes[factura_trim4]' target='_new' data-toggle='tooltip' title='Descargar factura'><span class='btn btn-xs btn-info glyphicon glyphicon-open-file'></span></a>";
									//echo "<span class='btn btn-xs btn-info glyphicon glyphicon-open-file'></span>";
								}
								if($informes['estatus_comprobante_trim4'] == 'ENVIADO' || $informes['estatus_comprobante_trim4'] == 'APROBADO' || $informes['estatus_comprobante_trim4'] == 'FINALIZADO'){
									/// boton para descargar comprobante de pago
									echo "<a href='$informes[comprobante_pago_trim4]' target='_new' data-toggle='tooltip' title='Descargar comprobante de pago'><span class='btn btn-xs btn-info glyphicon glyphicon-picture'></span></a>";				
								}
								if($informes['estado_trim4'] == 'ACTIVO' || $informes['estado_trim4'] == 'EN ESPERA' || $informes['estado_trim4'] == 'APROBADO'){
									echo '<img src="../../img/circulo_verde.jpg">';
								}else if($informes['estado_trim4'] == 'FINALIZADO'){
									echo '<img src="../../img/circulo_rojo.jpg">';
								}
								
								echo ' $'.number_format($informes['cuota_uso_trim4'],2).' USD';
								if($informes['estatus_factura_trim4'] == 'PAGADA'){
									echo "<span class='disabled btn btn-xs btn-info glyphicon glyphicon-ok' aria-hidden='true'></span>";
								}
								if(isset($informes['reporte_trim4'])){
									echo '<a href="'.$informes['reporte_trim1'].'" target="_new"><img height="25px;" src="../../img/pdf.png" alt=""></a>';
								}
							echo '</td>';

							echo '<td><a href="?REPORTES&informe_compras&detalle_total='.$informes['idinforme_general'].'"><span class="glyphicon glyphicon-search"></span> '.number_format($informes['total_cuota_uso'],2).' USD</a></td>';
						echo '</tr>';
					}
					echo '<tr>';
						echo '<td colspan="3" style="text-align:right"><b>Suma total</b></td>';
						echo '<td style="color:#e74c3c;">'.number_format($cuota_uso_trim1,2).'</td>';
						echo '<td style="color:#e74c3c;">'.number_format($cuota_uso_trim2,2).'</td>';
						echo '<td style="color:#e74c3c;">'.number_format($cuota_uso_trim3,2).'</td>';
						echo '<td style="color:#e74c3c;">'.number_format($cuota_uso_trim4,2).'</td>';
						echo '<td style="color:#e74c3c;">'.number_format($total_final,2).' USD</td>';
					echo '</tr>';
					//echo '<tr><td colspan="9"><b>Suma total: <span style="color:#e74c3c">'.$total.'</span> USD</b></td></tr>';
				}
				 ?>
			</tbody>
		</table>
	</div>