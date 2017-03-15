<?php 
require_once('../Connections/dspp.php'); 

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
	//$aprobado = '';
	//$fecha_fin = time();
	$anio_actual = date('Y', time());
	//$idinforme_general = $_GET['informe'];
	$num_trim = $_GET['num'];
	$txt_trim = 'trim'.$num_trim;
	$txt_idtrim = 'idtrim'.$num_trim;
	//$txt_valor_contrato = 'valor_contrato_trim'.$num_trim;
	//$txt_cuota_uso = 'cuota_uso_trim'.$num_trim;
	//$txt_estado_trim = 'estado_trim'.$num_trim;

	$idtrimestre = $_GET['trim'];

	$row_configuracion = mysql_query("SELECT * FROM porcentaje_ajuste WHERE anio = $anio_actual", $dspp) or die(mysql_error());
	$configuracion = mysql_fetch_assoc($row_configuracion);

	$row_trim = mysql_query("SELECT * FROM $txt_trim WHERE $txt_idtrim = '$idtrimestre'", $dspp) or die(mysql_error());

	$row_formatos = mysql_query("SELECT empresa.spp, empresa.abreviacion, empresa.pais, COUNT(idformato_compras) AS 'num_contratos', SUM(valor_total_contrato) AS 'total_contrato', SUM(total_a_pagar) AS 'total_cuota' FROM formato_compras INNER JOIN empresa ON formato_compras.idempresa = empresa.idempresa WHERE idtrim = '$idtrimestre'", $dspp) or die(mysql_error());
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
	$estatus_trim = 'FINALIZADO';
	if(isset($_POST['finalizar']) && $_POST['finalizar'] == 'SI'){
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
			$tipo_empresa = $tipo_empresa;


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
					        La empresa <span style="color:red">'.$empresa['abreviacion'].'</span> ha finalizado el <span style="color:red">TRIMESTRE '.$_GET['trim'].'</span>, a continuación se muestran una tabla con el resumen de las operaciones.
					        <hr>
					        <p style="color:#2c3e50;font-weight:bold">El Área de Certificación y Calidad SPP ha autorizado el siguiente Informe Trimestral de Compras, por favor proceda a generar la factura correspondiente(<small style="color:#7f8c8d">Se adjunta el PDF con los registro correspondientes al trimestre finalizado</small>).</p>
					        <p style="color:#2c3e50;font-weight:bold">Una vez creada la factura dar clic en el siguiente enlace para poder adjuntarla. <a href="http://localhost/D-SPP.ORG_2/procesar/facturacion.php?num='.$_GET['trim'].'&trim='.$idtrimestre.'" style="color:red">Clic para poder adjuntar factura</a></p>
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
					            <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">'.$empresa['abreviacion'].'</td>
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


		echo "<script>alert('Se ha enviado la información al area de ADMINSITRACIÓN para poder generar la factura');</script>";
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


?>
<style>
.myButton {
	-moz-box-shadow: 0px 10px 14px -7px #3e7327;
	-webkit-box-shadow: 0px 10px 14px -7px #3e7327;
	box-shadow: 0px 10px 14px -7px #3e7327;
	background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #77b55a), color-stop(1, #72b352));
	background:-moz-linear-gradient(top, #77b55a 5%, #72b352 100%);
	background:-webkit-linear-gradient(top, #77b55a 5%, #72b352 100%);
	background:-o-linear-gradient(top, #77b55a 5%, #72b352 100%);
	background:-ms-linear-gradient(top, #77b55a 5%, #72b352 100%);
	background:linear-gradient(to bottom, #77b55a 5%, #72b352 100%);
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#77b55a', endColorstr='#72b352',GradientType=0);
	background-color:#77b55a;
	-moz-border-radius:4px;
	-webkit-border-radius:4px;
	border-radius:4px;
	border:1px solid #4b8f29;
	display:inline-block;
	cursor:pointer;
	color:#ffffff;
	font-family:Arial;
	font-size:13px;
	font-weight:bold;
	padding:6px 12px;
	text-decoration:none;
	text-shadow:0px 1px 0px #5b8a3c;
	width: 200px;
}
.myButton:hover {
	background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #72b352), color-stop(1, #77b55a));
	background:-moz-linear-gradient(top, #72b352 5%, #77b55a 100%);
	background:-webkit-linear-gradient(top, #72b352 5%, #77b55a 100%);
	background:-o-linear-gradient(top, #72b352 5%, #77b55a 100%);
	background:-ms-linear-gradient(top, #72b352 5%, #77b55a 100%);
	background:linear-gradient(to bottom, #72b352 5%, #77b55a 100%);
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#72b352', endColorstr='#77b55a',GradientType=0);
	background-color:#72b352;
}
.myButton:active {
	position:relative;
	top:1px;
}

</style>

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
				      <td style="padding-top:10px;" colspan="2">           
				        La empresa <span style="color:red"><?php echo $formatos['abreviacion']; ?></span> ha finalizado el <span style="color:red">TRIMESTRE <?php echo $num_trim; ?></span>, a continuación se muestran una tabla con el resumen de las operaciones.
				      </td>
				    </tr>
				    <tr>
				      <td colspan="9">
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
						    <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;"><?php echo $formatos['abreviacion'];  ?></td>
						    <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">COMPRADOR FINAL</td>
						    <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;"><?php echo $idtrimestre; ?></td>
						    <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;"><?php echo $formatos['num_contratos']; ?></td>
						    <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;"><?php echo $valor_total_contratos; ?></td>
						    <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;"><?php echo $configuracion['cuota_compradores'].'%'; ?></td>
						    <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;"><?php echo $valor_cuota_de_uso; ?></td>
						  </tr>
						</table>
				      </td>
				    </tr>
				    <tr>
						<td style="padding-top:10px;" colspan="2">
							Debe dar clic en el siguiente botón para poder cargar la factura correspondiente al informe trimestral
						</td>
				    	<td>
					    	<form action="" method="POST">
					    		<button class="mybutton" type="submit" name="finalizar" value="SI">Aprobar Informe Trimestral</button>
					    	</form>	
				    	</td>
				    </tr>
				</tbody>
			</table>

		</body>
		</html>