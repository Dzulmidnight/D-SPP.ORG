<?php 
require_once('../Connections/dspp.php');
require_once('../Connections/mail.php');

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
mysql_select_db($database_dspp, $dspp);

$fecha = time();
$fecha_actual = time();
$anio = date('Y', time());
$anio_actual = date('Y', time());

$correo_certificacion = 'cert@spp.coop';
$correo_finanzas = 'adm@spp.coop';


if(isset($_POST['guardar_comprobante']) && !empty($_POST['guardar_comprobante'])){
	$idsolicitud_certificacion = $_POST['idsolicitud_certificacion'];
	$idmembresia = $_POST['idmembresia'];
	$idcomprobante_pago = $_POST['guardar_comprobante'];
	$monto_transferido = $_POST['monto_transferido'].' '.$_POST['tipo_moneda_transferido'];
	$monto_recibido = $_POST['monto_recibido'].' '.$_POST['tipo_moneda_recibido'];

	$estatus_dspp = 10; //membresia cargada
	$estatus_comprobante = 'ENVIADO';
	$nombre = "COMPROBANTE DE PAGO";
	$rutaArchivo = "../../archivos/oppArchivos/membresia/";

	if(!empty($_FILES['comprobante_de_pago']['name'])){
	  $_FILES["comprobante_de_pago"]["name"];
	    move_uploaded_file($_FILES["comprobante_de_pago"]["tmp_name"], $rutaArchivo.$fecha."_".$_FILES["comprobante_de_pago"]["name"]);
	    $comprobante_pago = $rutaArchivo.basename($fecha."_".$_FILES["comprobante_de_pago"]["name"]);
	}else{
		$comprobante_pago = NULL;
	}

	//creamos el proceso de certificacion
	$insertSQL = sprintf("INSERT INTO proceso_certificacion(idsolicitud_certificacion, estatus_dspp, nombre_archivo, archivo, fecha_registro) VALUES(%s, %s, %s, %s, %s)",
		GetSQLValueString($_POST['idsolicitud_certificacion'], "int"),
		GetSQLValueString($estatus_dspp, "int"),
		GetSQLValueString($nombre, "text"),
		GetSQLValueString($comprobante_pago, "text"),
		GetSQLValueString($fecha, "int"));
	$insertar = mysql_query($insertSQL,$dspp) or die(mysql_error());

	//actualizamos el comprobante de pago membresia
	/*$updateSQL = sprintf("UPDATE comprobante_pago SET estatus_comprobante = %s, archivo = %s, fecha_registro = %s WHERE idcomprobante_pago = %s",
		GetSQLValueString($estatus_comprobante, "text"),
		GetSQLValueString($comprobante_pago, "text"),
		GetSQLValueString($fecha, "int"),
		GetSQLValueString($idcomprobante_pago, "int"));
	$actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());*/

	/// se aprueba automaticamente el comprobante

	//SE APRUEBA EL COMPROBANTE DE PAGO
	$estatus_comprobante = "ACEPTADO"; //se acepta el comprobante
	$estatus_membresia = "APROBADA"; //se acepta la membresia
	$estatus_dspp = 18; //MEMBRESIA APROBADA
	//actualizamos comprobante_pago
	$updateSQL = sprintf("UPDATE comprobante_pago SET estatus_comprobante = %s, archivo = %s, monto_transferido = %s, monto_recibido = %s, fecha_registro = %s WHERE idcomprobante_pago = %s",
		GetSQLValueString($estatus_comprobante, "text"),
		GetSQLValueString($comprobante_pago, "text"),
		GetSQLValueString($monto_transferido, "text"),
		GetSQLValueString($monto_recibido, "text"),
		GetSQLValueString($fecha, "int"),
		GetSQLValueString($idcomprobante_pago, "int"));
	$actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());
	//actualizamos la membresia
	$updateSQL = sprintf("UPDATE membresia SET estatus_membresia = %s, fecha_registro = %s WHERE idmembresia = %s",
		GetSQLValueString($estatus_membresia, "text"),
		GetSQLValueString($fecha, "int"),
		GetSQLValueString($idmembresia, "int"));
	$actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

	$updateSQL = sprintf("UPDATE solicitud_certificacion SET estatus_dspp = %s WHERE idsolicitud_certificacion = %s",
		GetSQLValueString($estatus_dspp, "int"),
		GetSQLValueString($idsolicitud_certificacion, "int"));
	$actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

	//insertarmos el proceso_certificacion
	$insertSQL = sprintf("INSERT INTO proceso_certificacion(idsolicitud_certificacion, estatus_dspp, fecha_registro) VALUES (%s, %s, %s)",
		GetSQLValueString($idsolicitud_certificacion, "int"),
		GetSQLValueString($estatus_dspp, "int"),
		GetSQLValueString($fecha, "int"));
	$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

	//inicia enviar mensaje aprobacion membresia
	$row_informacion = mysql_query("SELECT solicitud_certificacion.idopp, solicitud_certificacion.contacto1_email, opp.email, opp.nombre, oc.email1, oc.email2 FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp INNER JOIN oc ON solicitud_certificacion.idoc = oc.idoc WHERE idsolicitud_certificacion = $idsolicitud_certificacion", $dspp) or die(mysql_error());
	$informacion = mysql_fetch_assoc($row_informacion);

	$asunto = "D-SPP | Membresia SPP aprobada";

	$cuerpo_mensaje = '
	      <html>
	      <head>
	        <meta charset="utf-8">
	      </head>
	      <body>
	        <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
	          <tbody>
	            <tr>
	              <th rowspan="2" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
	              <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">Membresías SPP aprobada</span></p></th>

	            </tr>
	            <tr>
	             <th scope="col" align="left" width="280"><p>OPP: <span style="color:red">'.$informacion['nombre'].'</span></p></th>
	            </tr>

	            <tr>
	              <td colspan="2">
	               <p>Felicidades!!! el pago de su membresía fue aprobado, su certificado estara disponible en breve por favor espere.</p>
	              </td>
	            </tr>
	            <tr>
	              <td colspan="2">
	                <p>Para cualquier duda o aclaración por favor escribir a: <span style="color:red">cert@spp.coop</span> o <span style="color:red">soporte@d-spp.org</span></p>
	              </td>
	            </tr>
	          </tbody>
	        </table>
	      </body>
	      </html>
	';

	if(!empty($informacion['contacto1_email'])){
		//$mail->AddAddress($informacion['contacto1_email']);
		$token = strtok($informacion['contacto1_email'], "\/\,\;");
		while ($token !== false)
		{
		  $mail->AddAddress($token);
		  $token = strtok('\/\,\;');
		}

	}
	if(!empty($informacion['email'])){
		//$mail->AddAddress($informacion['email']);
		$token = strtok($informacion['email'], "\/\,\;");
		while ($token !== false)
		{
		  $mail->AddAddress($token);
		  $token = strtok('\/\,\;');
		} 
	}

	$mail->Subject = utf8_decode($asunto);
	$mail->Body = utf8_decode($cuerpo_mensaje);
	$mail->MsgHTML(utf8_decode($cuerpo_mensaje));
	/*$mail->Send();
	$mail->ClearAddresses();*/
	if($mail->Send()){
		$mail->ClearAddresses();
		echo "<script>alert('Se ha aprobado el pago de la membresia, la OPP sera noticada en breve.');</script>";
	}else{
		$mail->ClearAddresses();
		echo "<script>alert('Error, no se pudo enviar el correo, por favor contacte al administrador: soporte@d-spp.org');</script>";
	}



}

if(isset($_POST['aprobar_comprobante'])){
	$idmembresia = $_POST['aprobar_comprobante'];
	//SE APRUEBA EL COMPROBANTE DE PAGO
	$estatus_comprobante = "ACEPTADO"; //se acepta el comprobante
	$estatus_membresia = "APROBADA"; //se acepta la membresia
	$estatus_dspp = 18; //MEMBRESIA APROBADA
	
	//actualizamos comprobante_pago
	$monto_transferido = $_POST['monto_transferido'].' '.$_POST['tipo_moneda_transferido'];
	$monto_recibido = $_POST['monto_recibido'].' '.$_POST['tipo_moneda_recibido'];

	$updateSQL = sprintf("UPDATE comprobante_pago SET estatus_comprobante = %s, monto_transferido = %s, monto_recibido = %s WHERE idcomprobante_pago = %s",
	GetSQLValueString($estatus_comprobante, "text"),
	GetSQLValueString($monto_transferido, "text"),
	GetSQLValueString($monto_recibido, "text"),
	GetSQLValueString($_POST['idcomprobante_pago'], "int"));
	$actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());
	//actualizamos la membresia
	$updateSQL = sprintf("UPDATE membresia SET estatus_membresia = %s, fecha_registro = %s WHERE idmembresia = %s",
	GetSQLValueString($estatus_membresia, "text"),
	GetSQLValueString($fecha, "int"),
	GetSQLValueString($idmembresia, "int"));
	$actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

	$updateSQL = sprintf("UPDATE solicitud_certificacion SET estatus_dspp = %s WHERE idsolicitud_certificacion = %s",
	GetSQLValueString($estatus_dspp, "int"),
	GetSQLValueString($_POST['idsolicitud_certificacion'], "int"));
	$actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

	//insertarmos el proceso_certificacion
	$insertSQL = sprintf("INSERT INTO proceso_certificacion(idsolicitud_certificacion, estatus_dspp, fecha_registro) VALUES (%s, %s, %s)",
	GetSQLValueString($_POST['idsolicitud_certificacion'], "int"),
	GetSQLValueString($estatus_dspp, "int"),
	GetSQLValueString($fecha, "int"));
	$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

	//inicia enviar mensaje aprobacion membresia
	$row_informacion = mysql_query("SELECT solicitud_certificacion.idopp, solicitud_certificacion.contacto1_email, opp.email, opp.nombre, oc.email1, oc.email2 FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp INNER JOIN oc ON solicitud_certificacion.idoc = oc.idoc WHERE idsolicitud_certificacion = $_POST[idsolicitud_certificacion]", $dspp) or die(mysql_error());
	$informacion = mysql_fetch_assoc($row_informacion);

	$asunto = "D-SPP | Membresia SPP aprobada";

	$cuerpo_mensaje = '
	      <html>
	      <head>
	        <meta charset="utf-8">
	      </head>
	      <body>
	        <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
	          <tbody>
	            <tr>
	              <th rowspan="2" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
	              <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">Membresías SPP aprobada</span></p></th>

	            </tr>
	            <tr>
	             <th scope="col" align="left" width="280"><p>OPP: <span style="color:red">'.$informacion['nombre'].'</span></p></th>
	            </tr>

	            <tr>
	              <td colspan="2">
	               <p>Felicidades!!! el pago de su membresía fue aprobado, su certificado estara disponible en breve por favor espere.</p>
	              </td>
	            </tr>
	            <tr>
	              <td colspan="2">
	                <p>Para cualquier duda o aclaración por favor escribir a: <span style="color:red">cert@spp.coop</span> o <span style="color:red">soporte@d-spp.org</span></p>
	              </td>
	            </tr>
	          </tbody>
	        </table>
	      </body>
	      </html>
	';

	if(!empty($informacion['contacto1_email'])){
		//$mail->AddAddress($informacion['contacto1_email']);
		$token = strtok($informacion['contacto1_email'], "\/\,\;");
		while ($token !== false)
		{
		  $mail->AddAddress($token);
		  $token = strtok('\/\,\;');
		}

	}
	if(!empty($informacion['email'])){
		//$mail->AddAddress($informacion['email']);
		$token = strtok($informacion['email'], "\/\,\;");
		while ($token !== false)
		{
		  $mail->AddAddress($token);
		  $token = strtok('\/\,\;');
		} 
	}

	$mail->Subject = utf8_decode($asunto);
	$mail->Body = utf8_decode($cuerpo_mensaje);
	$mail->MsgHTML(utf8_decode($cuerpo_mensaje));
	/*$mail->Send();
	$mail->ClearAddresses();*/
	if($mail->Send()){
		$mail->ClearAddresses();
		echo "<script>alert('Se ha aprobado el pago de la membresia, la OPP sera noticada en breve.');</script>";
	}else{
		$mail->ClearAddresses();
		echo "<script>alert('Error, no se pudo enviar el correo, por favor contacte al administrador: soporte@d-spp.org');</script>";
	}
}

if(isset($_POST['rechazar_comprobante'])){
	$idmembresia = $_POST['rechazar_comprobante'];
	//SE RECHAZA EL COMPROBANTE DE PAGO
	  $estatus_comprobante = "RECHAZADO"; //se rechaza el comprobante
	  $estatus_membresia = "RECHAZADO"; //se rechaza la membresia
	  //actualizamos comprobante_pago
	  /*27_04_2017 $updateSQL = sprintf("UPDATE comprobante_pago SET estatus_comprobante = %s, observaciones = %s WHERE idcomprobante_pago = %s",
	    GetSQLValueString($estatus_comprobante, "text"),
	    GetSQLValueString($_POST['observaciones_comprobante'], "text"),
	    GetSQLValueString($_POST['idcomprobante_pago'], "int"));27_04_2017*/
	  $updateSQL = sprintf("UPDATE comprobante_pago SET estatus_comprobante = %s WHERE idcomprobante_pago = %s",
	    GetSQLValueString($estatus_comprobante, "text"),
	    GetSQLValueString($_POST['idcomprobante_pago'], "int"));

	  $actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());
	  //actualizamos la membresia
	  $updateSQL = sprintf("UPDATE membresia SET estatus_membresia = %s WHERE idmembresia = %s",
	    GetSQLValueString($estatus_membresia, "text"),
	    GetSQLValueString($idmembresia, "int"));
	  $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

	  $mensaje = "Se ha rechaza la membresia y el OPP ha sido notificado";
	  echo "<script>alert('Se ha rechaza la membresia y el OPP ha sido notificado');location.href ='javascript:history.back()';</script>";

}
//// ENVIAR SUSPENSIÓN DE LA ORGANIZACIÓN
if(isset($_POST['enviar_suspension']) && $_POST['enviar_suspension'] == 1){
    $idopp = $_POST['idopp'];
    $idaviso_renovacion = $_POST['idaviso_renovacion'];
    $motivo_suspension = $_POST['motivo_suspension'];

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
                      <p>Código de identificación SPP: <span style="color:red">'.$spp.'</span></p>
                    </td>
                    <td>
                      <p>Fecha de Envio: <br><span style="color:red">'.date('d-m-Y', time()).'</span></p>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <p>Nombre de la instancia: <span style="color:red">'.$nombre_opp.'</span></p>
                    </td>
                    <td>
                      <p>Nº de Certificado: <span style="color:red">'.$certificado['idcertificado'].'</span></p>
                    </td>
                    <td>
                      <p>Vigencia del Certificado: <br><span style="color:red">'.$fecha_vigencia.'</span></p>
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
                    
                    <p>
                      Por medio de la presente se hace la notificación del aviso de Suspensión del Certificado por Incumplimiento con el Marco Regulatorio SPP de acuerdo a la información presentada en el siguiente archivo PDF:
                    </p>
                    
                    <p>CUALQUIER INCONVENIENTE FAVOR DE NOTIFICARLO A SPP GLOBAL AL CORREO <strong>cert@spp.coop</strong></p>
                  </td>
                </tr>
                <tr>
                  <td style="padding-top:2em;" colspan="2">
                    <b>English Below</b>
                    <hr>
                  </td>
                </tr>
                <tr>
                  <td style="text-align:justify; padding-top:2em" colspan="2">
                    <p>
                      Dear <strong style="color:red">'.$nombre_opp.', (<u>'.$abreviacion_opp.'</u>)</strong> Representatives</p>
                    <p>
                      Notification of Suspension of Non-Compliance Certificate with the SPP Regulatory Framework is hereby made according to the information presented in the following PDF file:
                    </p>
                    
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

      $updateSQL = "UPDATE avisos_renovacion SET suspender = $time_actual, motivo_suspension = '$motivo_suspension' WHERE idaviso_renovacion = $idaviso_renovacion";
      $ejecutar = mysql_query($updateSQL, $dspp) or die(mysql_error());

      $estatus_interno = 11; // SUSPENDIDO
      $updateSQL = "UPDATE opp SET estatus_interno = $estatus_interno WHERE idopp = $idopp";
      $ejecutar = mysql_query($updateSQL, $dspp) or die(mysql_error());
}

//// ENVIAR PRORROGA DEL PAGO

if(isset($_POST['enviar_prorroga']) && $_POST['enviar_prorroga'] == 1){
	$idopp = $_POST['idopp'];
	$idsolicitud_certificacion = $_POST['idsolicitud_certificacion'];
	$nombre_opp = $_POST['nombre_opp'];
	$abreviacion_opp = $_POST['abreviacion_opp'];
	$idcomprobante_pago = $_POST['idcomprobante_pago'];

	$prorroga_inicio = strtotime($_POST['prorroga_inicio']);
	$prorroga_fin = strtotime($_POST['prorroga_fin']);
	$justificacion_prorroga = $_POST['justificacion_prorroga'];

	$query = "SELECT solicitud_certificacion.contacto1_email, solicitud_certificacion.contacto1_email, solicitud_certificacion.adm1_email, solicitud_certificacion.adm2_email, opp.email FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE idsolicitud_certificacion = $idsolicitud_certificacion";
	$consultar = mysql_query($query,$dspp) or die(mysql_error());
	$correos_opp = mysql_fetch_assoc($consultar);


	$asunto = "D-SPP | Prorroga Pago Membresia SPP aprobada";

	$cuerpo_mensaje = '
	      <html>
	      <head>
	        <meta charset="utf-8">
	      </head>
	      <body>
          <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
            <tbody>
              <tr>
                <th rowspan="2" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">'.$asunto.'</span></p></th>

              </tr>
              <tr>
               <th scope="col" align="left" width="280"><p>OPP: <span style="color:red">'.$nombre_opp.'</span></p></th>
              </tr>

              <tr>
                <td colspan="2">
                 <p>Estimados representantes de: <span style="color:red">'.$nombre_opp.'</span> ('.$abreviacion_opp.'), se ha aprobado una prorroga para realizar el pago de la membresía spp, dicha prorroga tiene un período del dia: <span style="color:red">'.date('d/m/Y',$prorroga_inicio).'</span> al <span style="color:red">'.date('d/m/Y',$prorroga_fin).'</span>.</p>
                 <p>Una vez finalizado el período de la prorroga, si aun no se ha cargado el comprobante de pago de la membresía SPP dentro del sistema D-SPP se procedera a suspender a la organización.</p>
                </td>
              </tr>
              <tr>
                <td colspan="2">
                  <p>Para cualquier duda o aclaración por favor escribir a: <span style="color:red">cert@spp.coop</span> o <span style="color:red">soporte@d-spp.org</span></p>
                </td>
              </tr>
            </tbody>
          </table>
	      </body>
	      </html>
	';

	if(!empty($correos_opp['email'])){
		$token = strtok($correos_opp['email'], "\/\,\;");
		while ($token !== false)
		{
		  $mail->AddAddress($token);
		  $token = strtok('\/\,\;');
		}
	}
	if(!empty($correos_opp['contacto1_email'])){
		$token = strtok($correos_opp['contacto1_email'], "\/\,\;");
		while ($token !== false)
		{
		  $mail->AddAddress($token);
		  $token = strtok('\/\,\;');
		}
	}
	if(!empty($correos_opp['contacto2_email'])){
		$token = strtok($correos_opp['contacto2_email'], "\/\,\;");
		while ($token !== false)
		{
		  $mail->AddAddress($token);
		  $token = strtok('\/\,\;');
		}
	}
	if(!empty($correos_opp['adm1_email'])){
		$token = strtok($correos_opp['adm1_email'], "\/\,\;");
		while ($token !== false)
		{
		  $mail->AddAddress($token);
		  $token = strtok('\/\,\;');
		}
	}
	if(!empty($correos_opp['adm2_email'])){
		$token = strtok($correos_opp['adm2_email'], "\/\,\;");
		while ($token !== false)
		{
		  $mail->AddAddress($token);
		  $token = strtok('\/\,\;');
		}
	}

	$mail->AddBCC($correo_certificacion);
	$mail->AddBCC($correo_finanzas);
	$mail->Subject = utf8_decode($asunto);
	$mail->Body = utf8_decode($cuerpo_mensaje);
	$mail->MsgHTML(utf8_decode($cuerpo_mensaje));
	$mail->Send();
	$mail->ClearAddresses();

	$updateSQL = "UPDATE comprobante_pago SET prorroga_inicio = $prorroga_inicio, prorroga_fin = $prorroga_fin, justificacion = '$justificacion_prorroga' WHERE idcomprobante_pago = $idcomprobante_pago";
	$actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

}

if(isset($_POST['consultar']) && $_POST['consultar'] == 1){

	$estatus_membresia = $_POST['estatus_membresia'];
	if(empty($estatus_membresia)){
		$q_estatus = "";
	}else if($estatus_membresia == 'TODAS'){
		$q_estatus = "";
	}else{
		$q_estatus = "AND membresia.estatus_membresia = '".$estatus_membresia."'";
	}

	$pais_membresia = $_POST['pais_membresia'];
	if(empty($pais_membresia)){
		$q_pais = "";
	}else{
		$q_pais = "AND opp.pais = '".$pais_membresia."'";
	}

	$anio_membresia = $_POST['anio_membresia'];
	if(empty($anio_membresia)){
		$q_anio = "";
	}else if($anio_membresia == 'TODOS'){
		$q_anio = "";
	}else{
		$q_anio = "AND FROM_UNIXTIME(proceso_certificacion.fecha_registro,'%Y') = '".$anio_membresia."'";
	}
	$query = "SELECT opp.spp, opp.nombre AS 'nombre_opp', opp.abreviacion AS 'abreviacion_opp', opp.pais, solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.contacto1_email, solicitud_certificacion.contacto2_email, solicitud_certificacion.adm1_email, solicitud_certificacion.adm2_email, proceso_certificacion.idproceso_certificacion, proceso_certificacion.idsolicitud_certificacion, proceso_certificacion.fecha_registro AS 'fecha_dictamen', membresia.idmembresia, membresia.idopp, membresia.idcomprobante_pago, membresia.estatus_membresia, membresia.fecha_registro AS 'fecha_activacion', comprobante_pago.monto, comprobante_pago.monto_transferido, comprobante_pago.monto_recibido, comprobante_pago.estatus_comprobante, comprobante_pago.archivo, comprobante_pago.aviso1, comprobante_pago.validar1, comprobante_pago.aviso2, comprobante_pago.validar2, comprobante_pago.aviso3, comprobante_pago.validar3, comprobante_pago.notificacion_suspender, comprobante_pago.fecha_registro AS 'fecha_carga', comprobante_pago.prorroga_inicio, comprobante_pago.prorroga_fin, comprobante_pago.justificacion FROM proceso_certificacion INNER JOIN solicitud_certificacion ON proceso_certificacion.idsolicitud_certificacion = solicitud_certificacion.idsolicitud_certificacion INNER JOIN membresia ON proceso_certificacion.idsolicitud_certificacion = membresia.idsolicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp INNER JOIN comprobante_pago ON membresia.idcomprobante_pago = comprobante_pago.idcomprobante_pago WHERE proceso_certificacion.estatus_interno = 8 $q_estatus $q_pais $q_anio GROUP BY membresia.idsolicitud_certificacion ORDER BY proceso_certificacion.fecha_registro DESC";

	$row_membresias = mysql_query($query, $dspp) or die(mysql_error());

}else{
	$row_membresias = mysql_query("SELECT opp.spp, opp.nombre AS 'nombre_opp', opp.abreviacion AS 'abreviacion_opp', opp.pais, solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.contacto1_email, solicitud_certificacion.contacto2_email, solicitud_certificacion.adm1_email, solicitud_certificacion.adm2_email, proceso_certificacion.idproceso_certificacion, proceso_certificacion.idsolicitud_certificacion, proceso_certificacion.fecha_registro AS 'fecha_dictamen', membresia.idmembresia, membresia.idopp, membresia.idcomprobante_pago, membresia.estatus_membresia, membresia.fecha_registro AS 'fecha_activacion', comprobante_pago.monto, comprobante_pago.monto_transferido, comprobante_pago.monto_recibido, comprobante_pago.estatus_comprobante, comprobante_pago.archivo, comprobante_pago.aviso1, comprobante_pago.validar1, comprobante_pago.aviso2, comprobante_pago.validar2, comprobante_pago.aviso3, comprobante_pago.validar3, comprobante_pago.notificacion_suspender, comprobante_pago.fecha_registro AS 'fecha_carga', comprobante_pago.prorroga_inicio, comprobante_pago.prorroga_fin, comprobante_pago.justificacion FROM proceso_certificacion INNER JOIN solicitud_certificacion ON proceso_certificacion.idsolicitud_certificacion = solicitud_certificacion.idsolicitud_certificacion INNER JOIN membresia ON proceso_certificacion.idsolicitud_certificacion = membresia.idsolicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp INNER JOIN comprobante_pago ON membresia.idcomprobante_pago = comprobante_pago.idcomprobante_pago WHERE proceso_certificacion.estatus_interno = 8 AND FROM_UNIXTIME(proceso_certificacion.fecha_registro,'%Y') = '$anio_actual' GROUP BY membresia.idsolicitud_certificacion ORDER BY proceso_certificacion.fecha_registro DESC", $dspp) or die(mysql_error());
}


?>

<h4>
	Membresias OPP
</h4>
<table class="table table-bordered table-condensed" style="font-size:12px;">
	<thead>
		<tr>
			<form action="" method="POST">
				<th colspan="3">
					<?php
					$row_estatus = mysql_query("SELECT estatus_membresia FROM membresia GROUP BY estatus_membresia", $dspp) or die(mysql_error());

					 ?>
					<h5>Estatus membresia</h5>
					<select class="form-control" name="estatus_membresia" id="estatus_membresia">
						<?php 
						if(isset($_POST['estatus_membresia']) && $_POST['estatus_membresia'] == 'TODAS'){
							echo '<option value="TODAS" selected>TODAS</option>';
						}else{
							echo '<option value="TODAS">TODAS</option>';
						}
						 ?>
						<?php 
						while($estatus_membresia = mysql_fetch_assoc($row_estatus)){
							if(isset($_POST['estatus_membresia']) && $_POST['estatus_membresia'] == $estatus_membresia['estatus_membresia']){
								echo '<option value="'.$estatus_membresia['estatus_membresia'].'" selected>'.$estatus_membresia['estatus_membresia'].'</option>';
							}else{
								echo '<option value="'.$estatus_membresia['estatus_membresia'].'">'.$estatus_membresia['estatus_membresia'].'</option>';
							}
						}
						 ?>
					</select>
				</th>
				<th colspan="2">
					<h5>País</h5>
					<?php 
					$row_pais = mysql_query("SELECT pais FROM opp INNER JOIN membresia ON membresia.idopp = opp.idopp GROUP BY pais", $dspp) or die(mysql_error());
					 ?>
					<select class="form-control" name="pais_membresia" id="pais_membresia">
						<option value="">Todos los paises</option>
						<?php
						while($pais = mysql_fetch_assoc($row_pais)){
							if(isset($_POST['pais_membresia']) && $_POST['pais_membresia'] == $pais['pais']){
								echo '<option value="'.$pais['pais'].'" selected>'.$pais['pais'].'</option>';
							}else{
								echo '<option value="'.$pais['pais'].'">'.$pais['pais'].'</option>';
							}
						}
						 ?>
					</select>
				</th>
				<th>
					<h5>Año</h5>
					<?php
					$row_anio = mysql_query("SELECT proceso_certificacion.idsolicitud_certificacion, FROM_UNIXTIME(proceso_certificacion.fecha_registro,'%Y') AS 'anio', proceso_certificacion.fecha_registro AS 'fecha_dictamen' FROM proceso_certificacion INNER JOIN solicitud_certificacion ON proceso_certificacion.idsolicitud_certificacion = solicitud_certificacion.idsolicitud_certificacion INNER JOIN membresia ON proceso_certificacion.idsolicitud_certificacion = membresia.idsolicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp INNER JOIN comprobante_pago ON membresia.idcomprobante_pago = comprobante_pago.idcomprobante_pago WHERE proceso_certificacion.estatus_interno = 8 GROUP BY anio ORDER BY anio DESC", $dspp) or die(mysql_error());
					//$row_anio = mysql_query("SELECT FROM_UNIXTIME(fecha_registro,'%Y') AS 'anio' FROM membresia GROUP BY anio ORDER BY anio DESC", $dspp) or die(mysql_error());
					 ?>
					<select class="form-control" name="anio_membresia" id="anio_membresia">
						<?php 
						if(isset($_POST['anio_membresia']) && $_POST['anio_membresia'] == 'TODOS'){
							echo '<option value="TODOS" selected>TODOS</option>';
						}else{
							echo '<option value="TODOS">TODOS</option>';
						}
						 ?>
						<?php
						while($fecha = mysql_fetch_assoc($row_anio)){
							if(isset($_POST['anio_membresia']) && $_POST['anio_membresia'] != 'TODOS'){
								if($_POST['anio_membresia'] == $fecha['anio']){
									echo '<option value="'.$fecha['anio'].'" selected>'.$fecha['anio'].'</option>';
								}else{
									echo '<option value="'.$fecha['anio'].'">'.$fecha['anio'].'</option>';
								}
							}else{
								if($fecha['anio'] == $anio_actual && !isset($_POST['anio_membresia'])){
									echo '<option value="'.$fecha['anio'].'" selected>'.$fecha['anio'].'</option>';
								}else{
									echo '<option value="'.$fecha['anio'].'">'.$fecha['anio'].'</option>';
								}
							}
						}
						 ?>
					</select>
				</th>
				<th colspan="3">
					<button type="submit" class="btn btn-info btn-default" style="width:100%" name="consultar" value="1"><span class="glyphicon glyphicon-search" aria-hidde="true"></span> Consultar</button>
				</th>

			</form>
		</tr>
		<tr>
			<th class="text-center">#</th>
			<!--<th class="text-center">Estatus membresia</th>-->
			<th class="text-center">ID</th>
			
			<th class="text-center" style="width:140px;">Organización</th>
			<th class="text-center" style="width:120px;">País</th>
			<th class="text-center" style="width:110px;">Comprobante</th>
			<th class="text-center">Fecha dictamen</th>
			<th class="text-center">Recordatorio 1</th>
			<th class="text-center">Recordatorio 2</th>
			<th class="text-center">Alerta</th>
			<!-- 06/10/2017 <th class="text-center">Periodo</th> 06/10/2017-->
			
			<th class="info text-center">Monto membresia</th>
			<th class="text-center">Monto transferido</th>
			<th class="text-center">Monto recibido</th>
			
			<th class="text-center">Fecha de activación</th>
			<th colspan="2" class="text-center">Acciones</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$estatus = '';
		$cinco_dias = 432000;
		$diez_dias = 864000;
		$veinte_dias = $diez_dias*2;
		$contador = 1;

		while($registros = mysql_fetch_assoc($row_membresias)){

			$fecha_dictamen = $registros['fecha_dictamen'];
			$recordatorio1 = $fecha_dictamen + $diez_dias;
			$recordatorio2 = $fecha_dictamen + $veinte_dias;
			$alerta_suspension = $recordatorio2 + $cinco_dias;
			$correo_suspender = $veinte_dias + $diez_dias;
			$formato_dictamen = date('d/m/Y', $fecha_dictamen);
			$fecha_actual = time();

			if($registros['aviso1']){
				if($registros['validar1']){
					$clase_aviso1 = 'label label-danger';
				}else{
					$clase_aviso1 = 'label label-warning';
				}
			}else{
				$clase_aviso1 = 'label label-default';
			}
			if($registros['aviso2']){
				if($registros['validar2']){
					$clase_aviso2 = 'label label-danger';
				}else{
					$clase_aviso2 = 'label label-warning';
				}
			}else{
				$clase_aviso2 = 'label label-default';
			}
			if($registros['aviso3']){
				if($registros['validar3']){
					$clase_aviso3 = 'label label-danger';
				}else{
					$clase_aviso3 = 'label label-warning';
				}
			}else{
				$clase_aviso3 = 'label label-default';
			}

			if($registros['estatus_membresia'] == 'EN ESPERA'){
				$estatus = '';
			}else{
				$estatus = 'success';
			}
		?>
			<tr>
				<td><?php echo $contador; ?></td>
				<!-- ESTATUS DE LA MEMBRESIA -->
				<!--<td class="<?php echo $estatus; ?>">
					<?php echo $registros['estatus_membresia']; ?>
				</td>-->
				<td><?php echo $registros['idmembresia']; ?></td>
				
				<!-- ABREVIACIÓN DE LA ORGANIZACIÓN -->
				<td class="<?php echo $estatus; ?>">
					<?php
						if(isset($registros['prorroga_inicio'])){
							echo '<button class="btn btn-xs btn-warning" data-toggle="modal" data-target="#informacion_prorroga'.$registros['idcomprobante_pago'].'"><span class="glyphicon glyphicon-time" aria-hidden="true"></span></button> <a href="?OPP&detail&idopp='.$registros['idopp'].'"><b>'.$registros['abreviacion_opp'].'</b></a>';
							if($fecha_actual > $registros['prorroga_fin']){
								echo '<br>SE HA SUSPENDIDO A LA ORGANIZACIÓN';
							}else{
								//echo '<br>ESTA ACTIVA';
							}
						?>
						<!-- Modal Prorroga Organización -->
				            <div class="modal fade" id="<?php echo 'informacion_prorroga'.$registros['idcomprobante_pago']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
				            	<div class="modal-dialog modal-lg" role="document">
				                  	<div class="modal-content">
					                    <div class="modal-header">
					                    	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					                      	<h4 class="modal-title" id="myModalLabel">PRORROGA PARA LA ORGANIZACIÓN: <?php echo '<span style="color:red">'.$registros['nombre_opp'].'</span> ('.$registros['abreviacion_opp'].')'; ?></h4>
					                    </div>
					                    <div class="modal-body" style="font-size:12px;">
					                    	<p>Información sobre la prorroga de la organización</p>
					                    	<div class="col-md-6">
					                    		<p><b>PERÍODO DE LA PRORROGA</b></p>
					                    		<div class="row">
					                    			<div class="col-xs-6">
							                        	<label class="control-label" for="prorroga_inicio">Fecha de Inicio:</label>
							                        	<input type="text" class="form-control" id="prorroga_inicio" name="prorroga_inicio" value="<?php echo date('d/m/Y', $registros['prorroga_inicio']); ?>" disabled>
					                    			</div>
					                    			<div class="col-xs-6">
							                        	<label class="control-label" for="prorroga_fin">Fecha Final:</label>
							                        	<input type="text" class="form-control" id="prorroga_fin" name="prorroga_fin" value="<?php echo date('d/m/Y', $registros['prorroga_fin']); ?>" disabled>
					                    			</div>
					                    		</div>

					                    	</div>
					                    	<div class="col-md-6">
						                      	<div class="form-group has-success">
						                        	<label class="control-label" for="justificacion_prorroga">JUSTIFICACIÓN DE LA PRORROGA:</label>
						                        	<p class="well"><?php echo nl2br($registros['justificacion']); ?></p>
						                        	
						                      	</div>
					                    	</div>
					                    </div>
					                    <div class="modal-footer">
					                      	<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
					                    </div>
				                 	</div>
				                </div>
				           </div>

						<?php
						}else{
							echo '<a href="?OPP&detail&idopp='.$registros['idopp'].'"><b>'.$registros['abreviacion_opp'].'</b></a>';
						}
					?>
				</td>
				<td><?php echo $registros['pais'] ?></td>
				<td class="text-center">
					<?php 
					if(isset($registros['archivo'])){
						//echo 'Cargado el: '.date('d/m/Y',$registros['fecha_carga']);
					?>
						<a class="btn btn-xs btn-primary" style="width: 100%" href="<?php echo $registros['archivo']; ?>" target="_blank" data-toggle="tooltip" title="Descargar Comprobante">
							<span class="glyphicon glyphicon-file" aria-hidden="true"></span> Descargar
						</a>

					<?php
					}else{
						echo '<p style="color:red">No disponible</p>';
						echo '<button class="btn btn-xs btn-default" data-toggle="modal" data-target="#cargar_comprobante'.$registros['idcomprobante_pago'].'">Cargar Comprobante</button>';
					}

					if($registros['estatus_membresia'] == 'EN ESPERA' && isset($registros['archivo'])){
					?>
						<form action="" method="POST" style="display:inline-block">
							<!-- BOTON AUTORIZAR MEMBRESIA -->
							<button class="btn btn-xs btn-success" type="button" data-toggle="modal" data-target="<?php echo '#aprobar_comprobante'.$registros['idcomprobante_pago']; ?>" data-toggle="tooltip" title="Autorizar membresia">
								<span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Autorizar
							</button>

							<!-- INICIA MODAL PARA AUTORIZAR MEMBRESIA -->
							<div class="modal fade" id="<?php echo 'aprobar_comprobante'.$registros['idcomprobante_pago']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
								<div class="modal-dialog" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
											<h4 class="modal-title" id="myModalLabel">Autorizar Membresia SPP</h4>
										</div>
										<div class="modal-body">
											<table class="table text-left">
												<tr>
													<td width="50%">
														<p><b>Organización</b></p>
														<p>
															<?php echo $registros['nombre_opp'].' (<span style="color:red">'.$registros['abreviacion_opp'].'</span>)'; ?>
														</p>
													</td>
													<td width="50%">
														<p>
															<a data-toggle="tooltip" data-placement="top" title="Importe calculado por el sistema" href="#"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span><b> Importe de la Membresía SPP:</b></a>
														</p>
														<p style="color:red"><?php echo $registros['monto']; ?></p>
													</td>
												</tr>
												<tr>
													<td>
														<p>
															<a data-toggle="tooltip" data-placement="top" title="Monto que depósito la organización" href="#"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span><b> Monto transferido:</b></a>
														</p>
														<p>
															<input type="text" id="monto_transferido" name="monto_transferido" class="form-control" style="width: 60%;display:inline" placeholder="0000.00">
															<select class="form-control" style="width: 30%;display: inline" name="tipo_moneda_transferido" id="tipo_moneda_transferido">
																<option value="USD">USD</option>
																<option value="MX">MXN</option>
															</select>
														</p>
													</td>
													<td class="success">
														<p>
															<a data-toggle="tooltip" data-placement="top" title="Monto ingresado al final, después de las comisiones" href="#"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span><b> Monto recibido:</b></a>
														</p>
														<p>
															<input type="text" id="monto_recibido" name="monto_recibido" class="form-control" style="width: 60%;display:inline" placeholder="0000.00">
															<select class="form-control" style="width: 30%;display: inline" name="tipo_moneda_recibido" id="tipo_moneda_recibido">
																<option value="USD">USD</option>
																<option value="MX">MXN</option>
															</select>
														</p>
													</td>
												</tr>

											</table>
										</div>
										<div class="modal-footer">
											<input type="hidden" name="idmembresia" value="<?php echo $registros['idmembresia']; ?>">
											<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
											<button type="submit" name="aprobar_comprobante" value="<?php echo $registros['idmembresia']; ?>" class="btn btn-success">Autorizar membresia SPP</button>
											<!--<button type="submit" name="guardar_comprobante" value="<?php echo $registros['idcomprobante_pago']; ?>" class="btn btn-primary" >Guardar Comprobante</button>-->
										</div>
									</div>
								</div>
							</div>
							<!-- TERMINA MODAL PARA AUTORIZAR MEMBRESIA -->

							<!-- BOTON DENEGAR MEMBRESIA -->
							<button name="rechazar_comprobante" value="<?php echo $registros['idmembresia']; ?>" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Rechazar membresia" onclick="return confirm('¿Desea Rechazar el Comprobante de Pago?');" >
								<span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Rechazar
							</button>

							<input type="hidden" name="idcomprobante_pago" value="<?php echo $registros['idcomprobante_pago']; ?>">
							<input type="hidden" name="idsolicitud_certificacion" value="<?php echo $registros['idsolicitud_certificacion']; ?>">
						</form>
					<?php
					}
					 ?>

					<!-- Modal -->
					<form action="" method="POST" enctype="multipart/form-data">
						<div class="modal fade" id="<?php echo 'cargar_comprobante'.$registros['idcomprobante_pago']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
							<div class="modal-dialog" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										<h4 class="modal-title" id="myModalLabel">Comprobante de Pago Membresia SPP</h4>
									</div>
									<div class="modal-body">
										<table class="table text-left">
											<tr>
												<td width="50%">
													<p><b>Organización</b></p>
													<p>
														<?php echo $registros['nombre_opp'].' (<span style="color:red">'.$registros['abreviacion_opp'].'</span>)'; ?>
													</p>
												</td>
												<td width="50%">
													<p>
														<a data-toggle="tooltip" data-placement="top" title="Importe calculado por el sistema" href="#"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span><b> Importe de la Membresía SPP:</b></a>
													</p>
													<p style="color:red"><?php echo $registros['monto']; ?></p>
												</td>
											</tr>
											<tr>
												<td>
													<p>
														<a data-toggle="tooltip" data-placement="top" title="Monto que depósito la organización" href="#"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span><b> Monto transferido:</b></a>
													</p>
													<p>
														<input type="text" id="monto_transferido" name="monto_transferido" class="form-control" style="width: 60%;display:inline" placeholder="0000.00">
														<select class="form-control" style="width: 30%;display: inline" name="tipo_moneda_transferido" id="tipo_moneda_transferido">
															<option value="USD">USD</option>
															<option value="MX">MXN</option>
														</select>
													</p>
												</td>
												<td class="success">
													<p>
														<a data-toggle="tooltip" data-placement="top" title="Monto ingresado al final, después de las comisiones" href="#"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span><b> Monto recibido:</b></a>
													</p>
													<p>
														<input type="text" id="monto_recibido" name="monto_recibido" class="form-control" style="width: 60%;display:inline" placeholder="0000.00">
														<select class="form-control" style="width: 30%;display: inline" name="tipo_moneda_recibido" id="tipo_moneda_recibido">
															<option value="USD">USD</option>
															<option value="MX">MXN</option>
														</select>
													</p>
												</td>
											</tr>
											<tr>
												<td colspan="2">
													<label for="comprobante_de_pago">Cargar Comprobante de pago</label>
													<input type="file" id="comprobante_de_pago" name="comprobante_de_pago" class="form-control">
												</td>
											</tr>
										</table>
									</div>
									<div class="modal-footer">
										<input type="hidden" name="idsolicitud_certificacion" value="<?php echo $registros['idsolicitud_certificacion']; ?>">
										<input type="hidden" name="idmembresia" value="<?php echo $registros['idmembresia']; ?>">
										<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
										<button type="submit" name="guardar_comprobante" value="<?php echo $registros['idcomprobante_pago']; ?>" class="btn btn-primary" >Guardar Comprobante</button>
										<!--<button type="submit" name="guardar_comprobante" value="<?php echo $registros['idcomprobante_pago']; ?>" class="btn btn-primary" >Guardar Comprobante</button>-->
									</div>
								</div>
							</div>
						</div>
					</form>
				</td>
				<!-- FECHA DEL DICTAME POSITIVO -->
				<td class="warning">
					<?php echo $formato_dictamen; ?>
				</td>

				<!-- INICIAN LOS PERIODOS DE LAS ALERTAS -->

				<!----------- RECORDATORIO 1 ------------>
				<td>
					<?php echo '<span class="'.$clase_aviso1.'">'.date('d/m/Y', $recordatorio1).'</span>'; ?>
					<?php 
					 	if(!$registros['aviso1'] && $registros['estatus_comprobante'] != 'ACEPTADO'){
					 		if($fecha_actual >= $recordatorio1){

					 			$query = "UPDATE comprobante_pago SET aviso1 = $fecha_actual WHERE idcomprobante_pago = $registros[idcomprobante_pago]";
					 			$updateSQL = mysql_query($query, $dspp) or die(mysql_error());
					 			//echo '<p style="color:red">ENVIADO</p>';

								$asunto = "D-SPP | 1er recordatorio pago Membresía SPP (1st reminder payment SPP Membership)";

								$cuerpo_mensaje = '
								      <html>
								      <head>
								        <meta charset="utf-8">
								      </head>
								      <body>
					                    <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px;" border="0" width="650px">
					                      <tbody>
					                        <tr>
					                          <th rowspan="1" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
					                          <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">1er recordatorio pago Membresía SPP (<i style="color:#34495e">1st reminder payment SPP Membership</i>)</span></p></th>
					                        </tr>

					                        <tr>
					                          <td colspan="2" style="text-align:justify">
					                            <p>
					                              Estimados Representantes de : <span style="color:red">'.$registros['nombre_opp'].'</span> - ('.$registros['abreviacion_opp'].')
					                            </p>
					                            <p>
					                              En seguimiento a la notificación de su dictamen positivo SPP, enviado con fecha  '.$formato_dictamen.' se les recuerda que tienen un plazo máximo de 30 días posterior a la fecha de notificación para realizar el pago correspondiente a <span style="color:red">'.$registros['monto'].'</span> por Membresía SPP y posteriormente cargar el comprobante de pago en el D-SPP.  
					                            </p>
					                            <p>
					                              El no pagar la Membresía SPP oportunamente es un incumplimiento con el Marco Regulatorio, por lo tanto si el sistema no detecta un comprobante de pago, automáticamente enviará  la suspensión del Certificado.
					                            </p>
					                            <p>
					                               Por lo anterior, les solicitamos de la manera más atenta se proceda a realizar el pago a la mayor brevedad para evitar ser suspendidos.
					                            </p>
					                          </td>
					                        </tr>
					                        <tr>
					                          <td colspan="2" style="padding-top:2em;">
					                            <p>Para cualquier duda o aclaración por favor escribir a: <span style="color:red">cert@spp.coop</span> o <span style="color:red">soporte@d-spp.org</span></p>
					                          </td>
					                        </tr>

					                        <tr style="color: #797979;">
					                          <td style="padding-top:2em;">
					                            <b>English Below</b>
					                            <hr>
					                          </td>
					                        </tr>
					                        <tr style="color: #797979;text-align:justify">
					                          <td colspan="2">
					                            <p>
					                              Dear Representatives of: <span style="color:red">'.$registros['nombre_opp'].'</span> - ('.$registros['abreviacion_opp'].')
					                            </p>
					                            <p>
					                              Following the notification of their positive opinion SPP, sent on '.$formato_dictamen.' they are reminded that they have a maximum period of 30 days after the date of notification to make the payment corresponding to <span style="color:red">'.$registros['monto'].'</span> per Membership SPP and later Load the proof of payment into the D-SPP.  
					                            </p>
					                            <p>
					                              Failure to pay the SPP Membership in a timely manner is a breach of the Regulatory Framework, therefore if the system does not detect a payment receipt, it will automatically send the suspension of the Certificate.
					                            </p>
					                            <p>
					                               For the above, we ask you in the most careful way to proceed to make the payment as soon as possible to avoid being suspended.
					                            </p>
					                          </td>
					                        </tr>

					                        <tr>
					                          <td colspan="2" style="padding-top:2em;">
					                            <p>For any doubt or clarification please write to: <span style="color:red">cert@spp.coop</span> o <span style="color:red">soporte@d-spp.org</span></p>
					                          </td>
					                        </tr>
					                      </tbody>
					                    </table>
								      </body>
								      </html>
								';

								if(!empty($registros['contacto1_email'])){
									$token = strtok($registros['contacto1_email'], "\/\,\;");
									while ($token !== false)
									{
									  $mail->AddAddress($token);
									  $token = strtok('\/\,\;');
									}

								}
								if(!empty($registros['contacto2_email'])){
									$token = strtok($registros['contacto2_email'], "\/\,\;");
									while ($token !== false)
									{
									  $mail->AddAddress($token);
									  $token = strtok('\/\,\;');
									}

								}
								if(!empty($registros['adm1_email'])){
									$token = strtok($registros['adm1_email'], "\/\,\;");
									while ($token !== false)
									{
									  $mail->AddAddress($token);
									  $token = strtok('\/\,\;');
									}

								}
								if(!empty($registros['adm2_email'])){
									$token = strtok($registros['adm2_email'], "\/\,\;");
									while ($token !== false)
									{
									  $mail->AddAddress($token);
									  $token = strtok('\/\,\;');
									}

								}

								$mail->Subject = utf8_decode($asunto);
								$mail->Body = utf8_decode($cuerpo_mensaje);
								$mail->MsgHTML(utf8_decode($cuerpo_mensaje));
								$mail->Send();
								$mail->ClearAddresses();


					 		}else{
					 			//echo '<p style="color:green">ACTIVO</p>';
					 		}
					 	}
					 ?>

				</td>
				<!----------- RECORDATORIO 2 ------------>
				<td>
					<?php echo '<span class="'.$clase_aviso2.'">'.date('d/m/Y', $recordatorio2).'</span>'; ?>
					<?php 
					 	if(!$registros['aviso2'] && $registros['estatus_comprobante'] != 'ACEPTADO'){
					 		if($fecha_actual >= $recordatorio2){
					 			//echo '<p style="color:red">ENVIADO</p>';
					 			$query = "UPDATE comprobante_pago SET aviso2 = $fecha_actual WHERE idcomprobante_pago = $registros[idcomprobante_pago]";
					 			$updateSQL = mysql_query($query, $dspp) or die(mysql_error());

								$asunto = "D-SPP | 2do recordatorio pago Membresía SPP (2nd reminder payment SPP Membership)";
								$cuerpo_mensaje = '
								      <html>
								      <head>
								        <meta charset="utf-8">
								      </head>
								      <body>
					                    <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px;" border="0" width="650px">
					                      <tbody>
					                        <tr>
					                          <th rowspan="1" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
					                          <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">2do recordatorio pago Membresía SPP (<i style="color:re#34495e">2nd reminder payment SPP Membership</i>)</span></p></th>

					                        </tr>


					                        <tr>
					                          <td colspan="2" style="text-align:justify">
					                            <p>
					                              Estimados Representantes de : <span style="color:red">'.$registros['nombre_opp'].'</span> - ('.$registros['abreviacion_opp'].')
					                            </p>
					                            <p>
					                              En seguimiento a la notificación de su dictamen positivo SPP, enviado con fecha  '.$formato_dictamen.' se les recuerda que tienen un plazo máximo de 30 días posterior a la fecha de notificación para realizar el pago correspondiente a <span style="color:red">'.$registros['monto'].'</span> por Membresía SPP y posteriormente cargar el comprobante de pago en el D-SPP.  
					                            </p>
					                            <p>
					                              El no pagar la Membresía SPP oportunamente es un incumplimiento con el Marco Regulatorio, por lo tanto si el sistema no detecta un comprobante de pago, automáticamente enviará  la suspensión del Certificado.
					                            </p>
					                            <p>
					                               Por lo anterior, les solicitamos de la manera más atenta se proceda a realizar el pago a la mayor brevedad para evitar ser suspendidos.
					                            </p>
					                          </td>
					                        </tr>
					                        <tr>
					                          <td colspan="2" style="padding-top:2em;">
					                            <p>Para cualquier duda o aclaración por favor escribir a: <span style="color:red">cert@spp.coop</span> o <span style="color:red">soporte@d-spp.org</span></p>
					                          </td>
					                        </tr>

					                        <tr style="color: #797979;">
					                          <td style="padding-top:2em;">
					                            <b>English Below</b>
					                            <hr>
					                          </td>
					                        </tr>
					                        <tr style="color: #797979;text-align:justify">
					                          <td colspan="2">
					                            <p>
					                              Dear Representatives of: <span style="color:red">'.$registros['nombre_opp'].'</span> - ('.$registros['abreviacion_opp'].')
					                            </p>
					                            <p>
					                              Following the notification of their positive opinion SPP, sent on '.$formato_dictamen.' they are reminded that they have a maximum period of 30 days after the date of notification to make the payment corresponding to <span style="color:red">'.$registros['monto'].'</span> per Membership SPP and later Load the proof of payment into the D-SPP.  
					                            </p>
					                            <p>
					                              Failure to pay the SPP Membership in a timely manner is a breach of the Regulatory Framework, therefore if the system does not detect a payment receipt, it will automatically send the suspension of the Certificate.
					                            </p>
					                            <p>
					                               For the above, we ask you in the most careful way to proceed to make the payment as soon as possible to avoid being suspended.
					                            </p>
					                          </td>
					                        </tr>

					                        <tr>
					                          <td colspan="2" style="padding-top:2em;">
					                            <p>For any doubt or clarification please write to: <span style="color:red">cert@spp.coop</span> o <span style="color:red">soporte@d-spp.org</span></p>
					                          </td>
					                        </tr>
					                      </tbody>
					                    </table>
								      </body>
								      </html>
								';

								if(!empty($registros['contacto1_email'])){
									$token = strtok($registros['contacto1_email'], "\/\,\;");
									while ($token !== false)
									{
									  $mail->AddAddress($token);
									  $token = strtok('\/\,\;');
									}

								}
								if(!empty($registros['contacto2_email'])){
									$token = strtok($registros['contacto2_email'], "\/\,\;");
									while ($token !== false)
									{
									  $mail->AddAddress($token);
									  $token = strtok('\/\,\;');
									}

								}
								if(!empty($registros['adm1_email'])){
									$token = strtok($registros['adm1_email'], "\/\,\;");
									while ($token !== false)
									{
									  $mail->AddAddress($token);
									  $token = strtok('\/\,\;');
									}

								}
								if(!empty($registros['adm2_email'])){
									$token = strtok($registros['adm2_email'], "\/\,\;");
									while ($token !== false)
									{
									  $mail->AddAddress($token);
									  $token = strtok('\/\,\;');
									}

								}
								$mail->Subject = utf8_decode($asunto);
								$mail->Body = utf8_decode($cuerpo_mensaje);
								$mail->MsgHTML(utf8_decode($cuerpo_mensaje));
								$mail->Send();
								$mail->ClearAddresses();

					 		}else{
					 			//echo '<p style="color:green">ACTIVO</p>';
					 		}
					 	}
					 ?>
				</td>
				<!----------- ALERTA ------------>
				<td>
					<?php echo '<span class="'.$clase_aviso3.'">'.date('d/m/Y', $alerta_suspension).'</span>'; ?>
					<?php 
					 	if(!$registros['aviso3'] && $registros['estatus_comprobante'] != 'ACEPTADO'){
					 		if($fecha_actual >= $alerta_suspension){
					 			//echo '<p style="color:red">ENVIADO</p>';
					 			$query = "UPDATE comprobante_pago SET aviso3 = $fecha_actual WHERE idcomprobante_pago = $registros[idcomprobante_pago]";
					 			$updateSQL = mysql_query($query, $dspp) or die(mysql_error());
								
								$asunto = "D-SPP | Alerta de Suspensión (Suspension Alert)";
								$cuerpo_mensaje = '
								      <html>
								      <head>
								        <meta charset="utf-8">
								      </head>
								      <body>
					                    <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px;" border="0" width="650px">
					                      <tbody>
					                        <tr>
					                          <th rowspan="1" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
					                          <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">Alerta de Suspensión (<i style="color:#34495e">Suspension Alert</i>)</span></p></th>

					                        </tr>


					                        <tr>
					                          <td colspan="2" style="text-align:justify">
					                            <p>
					                              Estimados Representantes de : <span style="color:red">'.$registros['nombre_opp'].'</span> - ('.$registros['abreviacion_opp'].')
					                            </p>
					                            <p>
					                              En seguimiento a la notificación de su dictamen positivo SPP, enviado con fecha '.$formato_dictamen.' y a los recordatorios enviados posteriormente, se les informa que concluido el plazo máximo de 30 días, se enviara automáticamente la Suspensión del Certificado.
					                            </p>
					                            <p>
					                              El importe de su Membresía SPP  es de <span style="color:red">'.$registros['monto'].'</span>.
					                            </p>
					                            <p>
					                               Cabe mencionar que es necesario cargar el comprobante de pago para que no se genere la suspensión automáticamente.
					                            </p>
					                          </td>
					                        </tr>
					                        <tr>
					                          <td colspan="2" style="padding-top:2em;">
					                            <p>Para cualquier duda o aclaración por favor escribir a: <span style="color:red">cert@spp.coop</span> o <span style="color:red">soporte@d-spp.org</span></p>
					                          </td>
					                        </tr>

					                        <tr style="color: #797979;">
					                          <td style="padding-top:2em;">
					                            <b>English Below</b>
					                            <hr>
					                          </td>
					                        </tr>
					                        <tr style="color: #797979;text-align:justify">
					                          <td colspan="2">
					                            <p>
					                              Dear Representatives of: <span style="color:red">'.$registros['nombre_opp'].'</span> - ('.$registros['abreviacion_opp'].')
					                            </p>
					                            <p>
					                              Following the notification of their positive opinion SPP, sent on '.$formato_dictamen.' and the reminders sent later, they are informed that after the maximum period of 30 days, the Suspension of the Certificate will be sent automatically.  
					                            </p>
					                            <p>
					                              The amount of your SPP Membership is <span style="color:red">'.$registros['monto'].'</span>.
					                            </p>
					                            <p>
					                               It is worth mentioning that it is necessary to load the proof of payment so that the suspension is not generated automatically.
					                            </p>
					                          </td>
					                        </tr>

					                        <tr>
					                          <td colspan="2" style="padding-top:2em;">
					                            <p>For any doubt or clarification please write to: <span style="color:red">cert@spp.coop</span> o <span style="color:red">soporte@d-spp.org</span></p>
					                          </td>
					                        </tr>
					                      </tbody>
					                    </table>
								      </body>
								      </html>
								';

								if(!empty($registros['contacto1_email'])){
									$token = strtok($registros['contacto1_email'], "\/\,\;");
									while ($token !== false)
									{
									  $mail->AddAddress($token);
									  $token = strtok('\/\,\;');
									}

								}
								if(!empty($registros['contacto2_email'])){
									$token = strtok($registros['contacto2_email'], "\/\,\;");
									while ($token !== false)
									{
									  $mail->AddAddress($token);
									  $token = strtok('\/\,\;');
									}

								}
								if(!empty($registros['adm1_email'])){
									$token = strtok($registros['adm1_email'], "\/\,\;");
									while ($token !== false)
									{
									  $mail->AddAddress($token);
									  $token = strtok('\/\,\;');
									}

								}
								if(!empty($registros['adm2_email'])){
									$token = strtok($registros['adm2_email'], "\/\,\;");
									while ($token !== false)
									{
									  $mail->AddAddress($token);
									  $token = strtok('\/\,\;');
									}

								}
								$mail->AddBCC("cert@spp.coop");
								$mail->AddBCC("adm@spp.coop");
								$mail->Subject = utf8_decode($asunto);
								$mail->Body = utf8_decode($cuerpo_mensaje);
								$mail->MsgHTML(utf8_decode($cuerpo_mensaje));
								$mail->Send();
								$mail->ClearAddresses();

					 		}else{
					 			//echo '<p style="color:green">ACTIVO</p>';
					 		}
					 	}

					 	/// Se envia el correo a los administradores para suspender a la organización
					 	if(($registros['aviso3'] && !$registros['notificacion_suspender'] && $registros['estatus_comprobante'] != 'ACEPTADO' && !$registros['archivo']) && ($fecha_actual >= $correo_suspender)){
				 			//echo '<p style="color:red">ENVIADO</p>';
				 			$query = "UPDATE comprobante_pago SET notificacion_suspender = $fecha_actual WHERE idcomprobante_pago = $registros[idcomprobante_pago]";
				 			$updateSQL = mysql_query($query, $dspp) or die(mysql_error());
							
							$asunto = "D-SPP | Suspender Organización";
							$cuerpo_mensaje = '
							      <html>
							      <head>
							        <meta charset="utf-8">
							      </head>
							      <body>
	                                <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px;" border="0" width="650px">
	                                    <tr>
	                                      <th rowspan="1" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
	                                      <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">Suspender Organización por falta de pago de la membresía SPP</span></p></th>

	                                    </tr>


	                                    <tr>
	                                      <td colspan="2" style="text-align:justify">
	                                        <p>
	                                          Organización: <span style="color:red">'.$registros['nombre_opp'].'</span> - ('.$registros['abreviacion_opp'].')
	                                        </p>
	                                        <p>
	                                          En seguimiento a la notificación del dictamen positivo SPP y de acuerdo al plazo máximo de 30 días se han enviado los recordatorios y la alerta de suspensión.
	                                        </p>
	                                        <p>
	                                          Al no haber detectado un comprobante de pago de la membresía SPP cargado dentro del sistema D-SPP, el sistema solicita que ingrese al sistema para poder enviar la suspensión de dicha organización.
	                                        </p>
	                                        <p>
	                                          A continuación se muestra una tabla con información relevante:
	                                        </p>
	                                      </td>
	                                    </tr>
	                                    <tr>
	                                      <td colspan="2">
	                                        <style>
	                                          table.tabla1, td.tabla1, th.tabla1 {    
	                                              border: 1px solid #ddd;
	                                              text-align: left;
	                                          }

	                                          table.tabla1 {
	                                              border-collapse: collapse;
	                                              font-size: 11px;
	                                              width: 100%;
	                                          }

	                                          th.tabla1, td.tabla1 {
	                                              padding: 5px;
	                                          }
	                                        </style>
	                                        <table class="tabla1">
	                                          <tr class="tabla1">
	                                            <th style="text-align:center;" class="tabla1">País</th>
	                                            <th style="text-align:center;" class="tabla1">Organización</th>
	                                            <th style="text-align:center;" class="tabla1">Fecha Dictamen</th>
	                                            <th style="text-align:center;" class="tabla1">Monto Membresía</th>
	                                            <th style="text-align:center;" class="tabla1" colspan="3">Fecha Mensajes</th>
	                                            
	                                          </tr>
	                                          <tr class="tabla1">
	                                            <td class="tabla1">'.$registros['pais'].'</td>
	                                            <td class="tabla1">'.$registros['nombre_opp'].'(<span style="color:red">'.$registros['abreviacion_opp'].'</span>)</td>
	                                            <td class="tabla1">'.date('d/m/Y',$registros['fecha_dictamen']).'</td>
	                                            <td class="tabla1">'.$registros['monto'].'</td>
	                                            <td class="tabla1">1º Recordatorio<br>'.date('d/m/Y', $recordatorio1).'</td>
	                                            <td class="tabla1">2º Recordatorio<br>'.date('d/m/Y', $recordatorio2).'</td>
	                                            <td class="tabla1">Alerta<br>'.date('d/m/Y', $alerta_suspension).'</td>
	                                            
	                                          </tr>
	                                        </table>
	                                      </td>
	                                    </tr>
	                                    <tr>
	                                      <td colspan="2">
	                                        <p>Pasos para suspender una organización:</p>
	                                        <ol>
	                                          <li>Debes ingresar en tu cuenta de administrador.</li>
	                                          <li>Debes seleccionar la opción <span style="color:red;">"Membresias"</span>.</li>
	                                          <li>Localizar la fila con el nombre de la Organización.</li>
	                                          <li>Dar clic en la opción de <span style="color:red">"Suspender"</span>.</li>
	                                        </ol>
	                                      </td>
	                                    </tr>
	                                </table>
							      </body>
							      </html>
							';

							$mail->AddAddress("cert@spp.coop");
							$mail->AddAddress("adm@spp.coop");
							$mail->Subject = utf8_decode($asunto);
							$mail->Body = utf8_decode($cuerpo_mensaje);
							$mail->MsgHTML(utf8_decode($cuerpo_mensaje));
							$mail->Send();
							$mail->ClearAddresses();
					 	}
					 ?>
				</td>
				<!-- INICIA SECCIÓN PERIODO -->
				<!--06/10/2017 <td class="text-center">
					<?php
						$nuevo_anio = 3.154e+7 + $registros['fecha_dictamen'];
						echo date('d/m/Y', $registros['vigencia_fin']);
						echo '<br>-<br>';
						//echo '<br>';
						echo date('d/m/Y', $nuevo_anio);
					?>
				</td> 06/10/2017-->
				
				<!-- MONTO CALCULADO DE LA MEMBRESIA -->
				<td class="info">
					<?php echo $registros['monto']; ?>
				</td>
				<!-- MONTO TRANSFERIDO(reflejado) -->
				<td>
					<?php echo $registros['monto_transferido']; ?>
				</td>
				<!-- MONTO RECIBIDO -->
				<td>
					<?php echo $registros['monto_recibido']; ?>
				</td>
				<td>
					<?php 
					if(isset($registros['fecha_activacion'])){
						echo date('d/m/Y',$registros['fecha_activacion']);
					} 
					?>
				</td>
				<!-- SUSPENDER ORGANIZACIÓN -->
				<td>
					<?php
					if($registros['notificacion_suspender'] && $registros['estatus_membresia'] == 'EN ESPERA' && !$registros['archivo']){
					?>
					<form action="" method="POST" enctype="multipart/form-data">
						<button class="btn btn-xs btn-danger" data-toggle="modal" data-target="<?php echo '#suspender_organizacion'.$registros['idcomprobante_pago']; ?>" ><span class="glyphicon glyphicon-ban-circle" aria-hidden="true"></span> Suspender</button>

					<!-- Modal Suspender Organización -->
			            <div class="modal fade" id="<?php echo 'suspender_organizacion'.$registros['idcomprobante_pago']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
			            	<div class="modal-dialog modal-lg" role="document">
			                  	<div class="modal-content">
				                    <div class="modal-header">
				                    	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				                      	<h4 class="modal-title" id="myModalLabel">SUSPENDER ORGANIZACIÓN</h4>
				                    </div>
				                    <div class="modal-body" style="font-size:12px;">
				                    	<p>
				                        	Se procedera a suspender a la organización: <?php echo '<span style="color:red">'.$registros['nombre_opp'].'</span> ('.$registros['abreviacion_opp'].')'; ?>
				                      	</p>
				                      	<div class="form-group has-error">
				                        	<label class="control-label" for="motivo_suspension">A continuación debe de justificar el motivo de la suspensión de la organización:</label>
				                        	<textarea class="form-control" name="motivo_suspension" id="motivo_suspension" cols="5" placeholder="Escribir el motivo de la suspensión" required></textarea>
				                        	<p>*Nota: El motivo de la suspensión solo podra ser revisado por los administradores de SPP Global.</p>
				                      	</div>
				                    </div>
				                    <div class="modal-footer">
				                      	<input type="hidden" name="idopp" value="<?php echo $registros['idopp']; ?>">
				                      	
				                    	<input type="hidden" name="spp" value="<?php echo $registros['spp']; ?>">
				                      	<input type="hidden" name="nombre_opp" value="<?php echo $registros['nombre_opp']; ?>">
				                      	<input type="hidden" name="abreviacion_opp" value="<?php echo $registros['abreviacion_opp']; ?>">

				                      	<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
				                     	<button type="submit" class="btn btn-primary" name="enviar_suspension" onclick="return confirm('¿Desea continuar con la suspensión de la organización?');" value="1">Suspender Organización</button>
				                    </div>
			                 	</div>
			                </div>
			           </div>
					</form>

					<?php
					}
					?>
				</td>
				<!-- PRORROGA -->
				<td>
					<?php
					if($registros['notificacion_suspender'] && $registros['estatus_membresia'] == 'EN ESPERA' && !$registros['archivo']){
					?>
					<form action="" method="POST" enctype="multipart/form-data">
						<?php 
						if(!isset($registros['prorroga_inicio'])){
						?>
						<button class="btn btn-xs btn-info" data-toggle="modal" data-target="<?php echo '#prorroga_organizacion'.$registros['idcomprobante_pago']; ?>" ><span class="glyphicon glyphicon-time" aria-hidden="true"></span> Prorroga</button>
						<?php
						}
						 ?>

					<!-- Modal Prorroga Organización -->
			            <div class="modal fade" id="<?php echo 'prorroga_organizacion'.$registros['idcomprobante_pago']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
			            	<div class="modal-dialog modal-lg" role="document">
			                  	<div class="modal-content">
				                    <div class="modal-header">
				                    	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				                      	<h4 class="modal-title" id="myModalLabel">PRORROGA PARA LA ORGANIZACIÓN: <?php echo '<span style="color:red">'.$registros['nombre_opp'].'</span> ('.$registros['abreviacion_opp'].')'; ?></h4>
				                    </div>
				                    <div class="modal-body" style="font-size:12px;">
				                    	<p>A continuación debes de fijar la "Fecha Final" de la prorroga que se otorgara a la organización así como la "Justificación".</p>
				                    	<div class="col-md-6">
				                    		<p><b>PERÍODO DE LA PRORROGA</b></p>
				                    		<div class="row">
				                    			<div class="col-xs-6">
						                        	<label class="control-label" for="prorroga_inicio">Fecha de Inicio:</label>
						                        	<input type="date" class="form-control" id="prorroga_inicio" name="prorroga_inicio" placeholder="dd/mm/aaaa">
				                    			</div>
				                    			<div class="col-xs-6">
						                        	<label class="control-label" for="prorroga_fin">Fecha Final:</label>
						                        	<input type="date" class="form-control" id="prorroga_fin" name="prorroga_fin" placeholder="dd/mm/aaaa">
				                    			</div>
				                    		</div>

				                    	</div>
				                    	<div class="col-md-6">
					                      	<div class="form-group has-success">
					                        	<label class="control-label" for="justificacion_prorroga">JUSTIFICACIÓN:</label>
					                        	<textarea class="form-control" name="justificacion_prorroga" id="justificacion_prorroga" cols="5" placeholder="Escribir la justificación de la prorroga" required></textarea>
					                        	<p>*Debes escribir porque se ha otorgado una prorroga a la organización.</p>
					                      	</div>
				                    	</div>
				                    </div>
				                    <div class="modal-footer">
				                      	<input type="hidden" name="idopp" value="<?php echo $registros['idopp']; ?>">
				                      	<input type="hidden" name="idsolicitud_certificacion" value="<?php echo $registros['idsolicitud_certificacion']; ?>">
				                      	<input type="hidden" name="idcomprobante_pago" value="<?php echo $registros['idcomprobante_pago']; ?>">
				                    	<input type="hidden" name="spp" value="<?php echo $registros['spp']; ?>">
				                      	<input type="hidden" name="nombre_opp" value="<?php echo $registros['nombre_opp']; ?>">
				                      	<input type="hidden" name="abreviacion_opp" value="<?php echo $registros['abreviacion_opp']; ?>">

				                      	<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
				                     	<button type="submit" class="btn btn-primary" name="enviar_prorroga" onclick="return confirm('¿Desea activar la prorroga de la organización?');" value="1">Enviar Prorroga</button>
				                    </div>
			                 	</div>
			                </div>
			           </div>
					</form>

					<?php
					}
					?>

				</td>
			</tr>
		<?php
		$contador++;
		}
 	//$query = "SELECT opp.nombre AS 'nombre_opp', opp.abreviacion AS 'abreviacion_opp', solicitud_certificacion.contacto1_email, solicitud_certificacion.contacto2_email, solicitud_certificacion.adm1_email, solicitud_certificacion.adm2_email, proceso_certificacion.idproceso_certificacion, proceso_certificacion.idsolicitud_certificacion, proceso_certificacion.fecha_registro AS 'fecha_dictamen', membresia.idmembresia, membresia.idopp, membresia.idcomprobante_pago, comprobante_pago.monto, comprobante_pago.estatus_comprobante, comprobante_pago.archivo, comprobante_pago.aviso1, comprobante_pago.aviso2, comprobante_pago.aviso3 FROM proceso_certificacion INNER JOIN solicitud_certificacion ON proceso_certificacion.idsolicitud_certificacion = solicitud_certificacion.idsolicitud_certificacion INNER JOIN membresia ON proceso_certificacion.idsolicitud_certificacion = membresia.idsolicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp INNER JOIN comprobante_pago ON membresia.idcomprobante_pago = comprobante_pago.idcomprobante_pago WHERE proceso_certificacion.estatus_interno = 8 GROUP BY membresia.idsolicitud_certificacion ORDER BY proceso_certificacion.fecha_registro DESC";

		 ?>
	</tbody>
</table>

<script>
    /// FUNCIÓN PARA VALIDAR LOS CAMPOS OBLIGATORIOS
    function validar() {
        monto_transferido = document.getElementById("monto_transferido").value;
        if ( monto_transferido == null || monto_transferido.length == 0 || /^\s+$/.test(monto_transferido)) {
        // Si no se cumple la condicion...
            alert('DEBES INGRESAR EL MONTO DEPOSITADO');
            document.getElementById("monto_transferido").focus();
            return false;
        }

		comprobante_de_pago = document.getElementById("comprobante_de_pago").value;
        if ( comprobante_de_pago == null || comprobante_de_pago.length == 0 || /^\s+$/.test(comprobante_de_pago)) {
        // Si no se cumple la condicion...
            alert('DEBES SELECCIONAR EL COMPROBANTE DE PAGO');
            document.getElementById("comprobante_de_pago").focus();
            return false;
        }
       
        return true;
    }


  </script>
