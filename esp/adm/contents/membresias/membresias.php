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
$anio = date('Y', time());


if(isset($_POST['guardar_comprobante']) && !empty($_POST['guardar_comprobante'])){
	$idsolicitud_certificacion = $_POST['idsolicitud_certificacion'];
	$idmembresia = $_POST['idmembresia'];
	$idcomprobante_pago = $_POST['guardar_comprobante'];
	$monto_real = $_POST['monto_real'].' '.$_POST['tipo_moneda'];

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
	$updateSQL = sprintf("UPDATE comprobante_pago SET estatus_comprobante = %s, archivo = %s, monto_real = %s, fecha_registro = %s WHERE idcomprobante_pago = %s",
		GetSQLValueString($estatus_comprobante, "text"),
		GetSQLValueString($comprobante_pago, "text"),
		GetSQLValueString($monto_real, "text"),
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
	$updateSQL = sprintf("UPDATE comprobante_pago SET estatus_comprobante = %s WHERE idcomprobante_pago = %s",
	GetSQLValueString($estatus_comprobante, "text"),
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

if(isset($_POST['rechar_comprobante'])){
	$idmembresia = $_POST['rechar_comprobante'];
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

if(isset($_POST['consultar']) && $_POST['consultar'] == 1){
	$estatus_membresia = $_POST['estatus_membresia'];
	$pais_membresia = $_POST['pais_membresia'];
	$anio_membresia = $_POST['anio_membresia'];
	
	if(!empty($estatus_membresia) && !empty($pais_membresia) && !empty($anio_membresia)){
		$query = "SELECT opp.abreviacion, opp.pais, membresia.*, comprobante_pago.monto, comprobante_pago.archivo FROM membresia INNER JOIN opp ON membresia.idopp = opp.idopp INNER JOIN comprobante_pago ON membresia.idcomprobante_pago = comprobante_pago.idcomprobante_pago WHERE membresia.estatus_membresia = '$estatus_membresia' AND opp.pais = '$pais_membresia' AND FROM_UNIXTIME(membresia.fecha_registro,'%Y') = '$anio_membresia'  ORDER BY membresia.idmembresia DESC";
	}else if(!empty($estatus_membresia) && empty($pais_membresia) && !empty($anio_membresia)){
		$query = "SELECT opp.abreviacion, opp.pais, membresia.*, comprobante_pago.monto, comprobante_pago.archivo FROM membresia INNER JOIN opp ON membresia.idopp = opp.idopp INNER JOIN comprobante_pago ON membresia.idcomprobante_pago = comprobante_pago.idcomprobante_pago WHERE membresia.estatus_membresia = '$estatus_membresia' AND FROM_UNIXTIME(membresia.fecha_registro,'%Y') = '$anio_membresia'  ORDER BY membresia.idmembresia DESC";
	}else if(!empty($estatus_membresia) && !empty($pais_membresia) && empty($anio_membresia)){
		$query = "SELECT opp.abreviacion, opp.pais, membresia.*, comprobante_pago.monto, comprobante_pago.archivo FROM membresia INNER JOIN opp ON membresia.idopp = opp.idopp INNER JOIN comprobante_pago ON membresia.idcomprobante_pago = comprobante_pago.idcomprobante_pago WHERE membresia.estatus_membresia = '$estatus_membresia' AND opp.pais = '$pais_membresia'  ORDER BY membresia.idmembresia DESC";
	}else if(!empty($pais_membresia) && !empty($anio_membresia)){
		$query = "SELECT opp.abreviacion, opp.pais, membresia.*, comprobante_pago.monto, comprobante_pago.archivo FROM membresia INNER JOIN opp ON membresia.idopp = opp.idopp INNER JOIN comprobante_pago ON membresia.idcomprobante_pago = comprobante_pago.idcomprobante_pago WHERE opp.pais = '$pais_membresia' AND FROM_UNIXTIME(membresia.fecha_registro,'%Y') = '$anio_membresia'  ORDER BY membresia.idmembresia DESC";
	}else if(!empty($pais_membresia)){
		$query = "SELECT opp.abreviacion, opp.pais, membresia.*, comprobante_pago.monto, comprobante_pago.archivo FROM membresia INNER JOIN opp ON membresia.idopp = opp.idopp INNER JOIN comprobante_pago ON membresia.idcomprobante_pago = comprobante_pago.idcomprobante_pago WHERE opp.pais = '$pais_membresia' ORDER BY membresia.idmembresia DESC";
	}else if(!empty($anio_membresia)){
		$query = "SELECT opp.abreviacion, opp.pais, membresia.*, comprobante_pago.monto, comprobante_pago.archivo FROM membresia INNER JOIN opp ON membresia.idopp = opp.idopp INNER JOIN comprobante_pago ON membresia.idcomprobante_pago = comprobante_pago.idcomprobante_pago WHERE FROM_UNIXTIME(membresia.fecha_registro,'%Y') = '$anio_membresia' ORDER BY membresia.idmembresia DESC";
	}else if(!empty($estatus_membresia)){
		$query = "SELECT opp.abreviacion, opp.pais, membresia.*, comprobante_pago.monto, comprobante_pago.archivo FROM membresia INNER JOIN opp ON membresia.idopp = opp.idopp INNER JOIN comprobante_pago ON membresia.idcomprobante_pago = comprobante_pago.idcomprobante_pago WHERE membresia.estatus_membresia = '$estatus_membresia' ORDER BY membresia.idmembresia DESC";
	}else{
		$query = "SELECT opp.abreviacion, opp.pais, membresia.*, comprobante_pago.monto, comprobante_pago.archivo FROM membresia INNER JOIN opp ON membresia.idopp = opp.idopp INNER JOIN comprobante_pago ON membresia.idcomprobante_pago = comprobante_pago.idcomprobante_pago ORDER BY membresia.idmembresia DESC";
	}
	$row_membresias = mysql_query($query, $dspp) or die(mysql_error());

}else{
	//$row_membresias = mysql_query("SELECT membresia.* FROM membresia ORDER BY membresia.idmembresia DESC", $dspp) or die(mysql_error());
	//22_08_2017 $row_membresias = mysql_query("SELECT opp.abreviacion, opp.pais, membresia.*, comprobante_pago.monto, comprobante_pago.archivo, proceso_certificacion.fecha_registro AS 'periodo' FROM membresia INNER JOIN opp ON membresia.idopp = opp.idopp INNER JOIN comprobante_pago ON membresia.idcomprobante_pago = comprobante_pago.idcomprobante_pago LEFT JOIN proceso_certificacion ON membresia.idsolicitud_certificacion = proceso_certificacion.idsolicitud_certificacion WHERE FROM_UNIXTIME(proceso_certificacion.fecha_registro, '%Y') = $anio AND proceso_certificacion.estatus_interno = 8 GROUP BY membresia.idsolicitud_certificacion ORDER BY membresia.idmembresia DESC", $dspp) or die(mysql_error());
	$row_membresias = mysql_query("SELECT opp.nombre AS 'nombre_opp', opp.abreviacion AS 'abreviacion_opp', opp.pais, solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.contacto1_email, solicitud_certificacion.contacto2_email, solicitud_certificacion.adm1_email, solicitud_certificacion.adm2_email, proceso_certificacion.idproceso_certificacion, proceso_certificacion.idsolicitud_certificacion, proceso_certificacion.fecha_registro AS 'fecha_dictamen', membresia.idmembresia, membresia.idopp, membresia.idcomprobante_pago, membresia.estatus_membresia, membresia.fecha_registro AS 'fecha_activacion', comprobante_pago.monto, comprobante_pago.monto_real, comprobante_pago.estatus_comprobante, comprobante_pago.archivo, comprobante_pago.aviso1, comprobante_pago.aviso2, comprobante_pago.aviso3, comprobante_pago.notificacion_suspender, comprobante_pago.fecha_registro AS 'fecha_carga' FROM proceso_certificacion INNER JOIN solicitud_certificacion ON proceso_certificacion.idsolicitud_certificacion = solicitud_certificacion.idsolicitud_certificacion INNER JOIN membresia ON proceso_certificacion.idsolicitud_certificacion = membresia.idsolicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp INNER JOIN comprobante_pago ON membresia.idcomprobante_pago = comprobante_pago.idcomprobante_pago WHERE proceso_certificacion.estatus_interno = 8 GROUP BY membresia.idsolicitud_certificacion ORDER BY proceso_certificacion.fecha_registro DESC", $dspp) or die(mysql_error());
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
					<select name="estatus_membresia" id="estatus_membresia">
						<option value="">TODAS</option>
						<?php 
						while($estatus_membresia = mysql_fetch_assoc($row_estatus)){
							echo '<option value="'.$estatus_membresia['estatus_membresia'].'">'.$estatus_membresia['estatus_membresia'].'</option>';
						}
						 ?>
					</select>
				</th>
				<th colspan="3">
					<h5>País</h5>
					<?php 
					$row_pais = mysql_query("SELECT pais FROM opp INNER JOIN membresia ON membresia.idopp = opp.idopp GROUP BY pais", $dspp) or die(mysql_error());
					 ?>
					<select name="pais_membresia" id="pais_membresia">
						<option value="">Lista de Paises</option>
						<?php
						while($pais = mysql_fetch_assoc($row_pais)){
							echo '<option value="'.$pais['pais'].'">'.$pais['pais'].'</option>';
						}
						 ?>
					</select>
				</th>
				<th>
					<h5>Año</h5>
					<?php
					$anio_actual = date('Y', time());
					$row_anio = mysql_query("SELECT FROM_UNIXTIME(fecha_registro,'%Y') AS 'anio' FROM membresia GROUP BY anio ORDER BY anio DESC", $dspp) or die(mysql_error());
					 ?>
					<select name="anio_membresia" id="anio_membresia">
						<option value="">Todos</option>
						<?php
						while($fecha = mysql_fetch_assoc($row_anio)){
							if($anio == $fecha['anio']){
								echo '<option value="'.$fecha['anio'].'" selected>'.$fecha['anio'].'</option>';
							}else{
								echo '<option value="'.$fecha['anio'].'">'.$fecha['anio'].'</option>';
							}
						}
						 ?>
					</select>
				</th>
				<th>
					<button type="submit" class="btn btn-default" name="consultar" value="1"><span class="glyphicon glyphicon-search" aria-hidde="true"></span> Consultar</button>
				</th>
				<th style="font-size: 10px;" class="text-center warning">EN ESPERA</th>
				<th style="font-size: 10px;" class="text-center success">APROBADA</th>
			</form>
		</tr>
		<tr>
			<th class="text-center">#</th>
			<!--<th class="text-center">Estatus membresia</th>-->
			<th class="text-center">ID</th>
			<th class="text-center">País</th>
			<th class="text-center">Organización</th>
			<th class="text-center">Comprobante</th>
			<th class="text-center">Fecha dictamen</th>
			<th class="text-center">Recordatorio 1</th>
			<th class="text-center">Recordatorio 2</th>
			<th class="text-center">Alerta</th>
			<th class="text-center">Periodo</th>
			
			<th class="info text-center">Monto membresia</th>
			<th class="text-center">Monto reflejado</th>
			
			<th class="text-center">Fecha de activación</th>
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
				$clase_aviso1 = 'label label-danger';
			}else{
				$clase_aviso1 = 'label label-default';
			}
			if($registros['aviso2']){
				$clase_aviso2 = 'label label-danger';
			}else{
				$clase_aviso2 = 'label label-default';
			}
			if($registros['aviso3']){
				$clase_aviso3 = 'label label-danger';
			}else{
				$clase_aviso3 = 'label label-default';
			}

			if($registros['estatus_membresia'] == 'EN ESPERA'){
				$estatus = 'warning';
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
				<td><?php echo $registros['pais'] ?></td>
				<!-- ABREVIACIÓN DE LA ORGANIZACIÓN -->
				<td class="<?php echo $estatus; ?>">
					<?php 
					echo '<b>'.$registros['abreviacion_opp'].'</b>'; 
					if($registros['notificacion_suspender'] && $registros['estatus_membresia'] == 'EN ESPERA' && !$registros['archivo']){
					?>
						<button class="btn btn-xs btn-danger" style="width: 100%" data-toggle="modal" data-target="<?php echo '#suspender_organizacion'.$registros['idcomprobante_pago']; ?>" disabled><span class="glyphicon glyphicon-ban-circle" aria-hidden="true"></span> Suspender</button>

					<!-- Modal Suspender Organización -->
					<form action="" method="POST" enctype="multipart/form-data">
						<div class="modal fade" id="<?php echo 'suspender_organizacion'.$registros['idcomprobante_pago']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
							<div class="modal-dialog" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										<h4 class="modal-title" id="myModalLabel">Suspender Organización</h4>
									</div>
									<div class="modal-body">
										<div class="row">
											<div class="col-md-12">
												
											</div>
										</div>
										<!--<table class="table">
											<tr>
												<td><b>Organización</b></td>
												<td><?php echo $registros['nombre_opp'].' (<span style="color:red">'.$registros['abreviacion_opp'].'</span>)'; ?></td>
											</tr>
											<tr>
												<td>
													<p><b>Monto calculado membresía SPP:</b></p>
													<p style="color:red"><?php echo $registros['monto']; ?></p>
												</td>
												<td class="success">
													<p><b>Monto depositado</b></p>
													<p>
														<input type="text" id="monto_real" name="monto_real" class="form-control" style="width: 60%;display:inline" placeholder="0000.00">
														<select class="form-control" style="width: 30%;display: inline" name="tipo_moneda" id="tipo_moneda">
															<option value="USD">USD</option>
															<option value="MX">MXN</option>
														</select>
													</p>
												</td>
											</tr>
											<tr>
												<td><b>Cargar Comprobante de pago</b></td>
												<td><input type="file" id="comprobante_de_pago" name="comprobante_de_pago" class="form-control"></td>
											</tr>
										</table>-->
									</div>
									<div class="modal-footer">
										<input type="hidden" name="idsolicitud_certificacion" value="<?php echo $registros['idsolicitud_certificacion']; ?>">
										<input type="hidden" name="idmembresia" value="<?php echo $registros['idmembresia']; ?>">
										<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
										<button type="submit" name="guardar_comprobante" value="<?php echo $registros['idcomprobante_pago']; ?>" class="btn btn-primary" onclick="return validar();">Guardar Comprobante</button>
										<!--<button type="submit" name="guardar_comprobante" value="<?php echo $registros['idcomprobante_pago']; ?>" class="btn btn-primary" onclick="return validar();">Guardar Comprobante</button>-->
									</div>
								</div>
							</div>
						</div>
					</form>

					<?php
					}
					?>

				</td>
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
						echo '<form action="" method="POST" style="display:inline-block">';
							echo '<button style="width:58px;" name="aprobar_comprobante" value="'.$registros['idmembresia'].'" class="btn btn-xs btn-success" data-toggle="tooltip" title="Autorizar membresia" onclick="return confirm(\'¿Desea Aprobar el Comprobante de pago?\');">
								<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
							</button>';

							echo '<button style="width:58px;" name="rechar_comprobante" value="'.$registros['idmembresia'].'" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Rechazar membresia" onclick="return confirm(\'¿Desea Rechazar el Comprobante de Pago?\');">
								<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>	
							</button>';
							echo '<input type="hidden" name="idcomprobante_pago" value="'.$registros['idcomprobante_pago'].'">';
							echo '<input type="hidden" name="idsolicitud_certificacion" value="'.$registros['idsolicitud_certificacion'].'">';
						echo '</form>';
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
										<table class="table">
											<tr>
												<td><b>Organización</b></td>
												<td><?php echo $registros['nombre_opp'].' (<span style="color:red">'.$registros['abreviacion_opp'].'</span>)'; ?></td>
											</tr>
											<tr>
												<td>
													<p><b>Monto calculado membresía SPP:</b></p>
													<p style="color:red"><?php echo $registros['monto']; ?></p>
												</td>
												<td class="success">
													<p><b>Monto depositado</b></p>
													<p>
														<input type="text" id="monto_real" name="monto_real" class="form-control" style="width: 60%;display:inline" placeholder="0000.00">
														<select class="form-control" style="width: 30%;display: inline" name="tipo_moneda" id="tipo_moneda">
															<option value="USD">USD</option>
															<option value="MX">MXN</option>
														</select>
													</p>
												</td>
											</tr>
											<tr>
												<td><b>Cargar Comprobante de pago</b></td>
												<td><input type="file" id="comprobante_de_pago" name="comprobante_de_pago" class="form-control"></td>
											</tr>
										</table>
									</div>
									<div class="modal-footer">
										<input type="hidden" name="idsolicitud_certificacion" value="<?php echo $registros['idsolicitud_certificacion']; ?>">
										<input type="hidden" name="idmembresia" value="<?php echo $registros['idmembresia']; ?>">
										<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
										<button type="submit" name="guardar_comprobante" value="<?php echo $registros['idcomprobante_pago']; ?>" class="btn btn-primary" onclick="return validar();">Guardar Comprobante</button>
										<!--<button type="submit" name="guardar_comprobante" value="<?php echo $registros['idcomprobante_pago']; ?>" class="btn btn-primary" onclick="return validar();">Guardar Comprobante</button>-->
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

					 			$query = "UPDATE comprobante_pago SET aviso1 = 1 WHERE idcomprobante_pago = $registros[idcomprobante_pago]";
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
					 			$query = "UPDATE comprobante_pago SET aviso2 = 1 WHERE idcomprobante_pago = $registros[idcomprobante_pago]";
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
					 			$query = "UPDATE comprobante_pago SET aviso3 = 1 WHERE idcomprobante_pago = $registros[idcomprobante_pago]";
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
				 			$query = "UPDATE comprobante_pago SET notificacion_suspender = 1 WHERE idcomprobante_pago = $registros[idcomprobante_pago]";
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
				<td class="text-center">
					<?php
						$nuevo_anio = 3.154e+7 + $registros['fecha_dictamen'];
						echo date('d/m/Y', $registros['fecha_dictamen']);
						echo '<br>-<br>';
						//echo '<br>';
						echo date('d/m/Y', $nuevo_anio);
					?>
				</td>
				
				<!-- MONTO CALCULADO DE LA MEMBRESIA -->
				<td class="info">
					<?php echo $registros['monto']; ?>
				</td>
				<!-- MONTO DEPOSITADO(reflejado) -->
				<td>
					<?php echo $registros['monto_real']; ?>
				</td>
				<td>
					<?php 
					if(isset($registros['fecha_activacion'])){
						echo date('d/m/Y',$registros['fecha_activacion']);
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
        monto_real = document.getElementById("monto_real").value;
        if ( monto_real == null || monto_real.length == 0 || /^\s+$/.test(monto_real)) {
        // Si no se cumple la condicion...
            alert('DEBES INGRESAR EL MONTO DEPOSITADO');
            document.getElementById("monto_real").focus();
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
