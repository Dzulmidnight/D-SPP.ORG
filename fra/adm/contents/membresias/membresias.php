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
	$row_membresias = mysql_query("SELECT opp.abreviacion, opp.pais, membresia.*, comprobante_pago.monto, comprobante_pago.archivo FROM membresia INNER JOIN opp ON membresia.idopp = opp.idopp INNER JOIN comprobante_pago ON membresia.idcomprobante_pago = comprobante_pago.idcomprobante_pago ORDER BY membresia.idmembresia DESC", $dspp) or die(mysql_error());
}


?>

<h4>
	Membresias OPP
</h4>
<table class="table table-bordered table-condensed" style="font-size:12px;">
	<thead>
		<tr>
			<form action="" method="POST">
				<th>
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
				<th>
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
							echo '<option value="'.$fecha['anio'].'">'.$fecha['anio'].'</option>';
						}
						 ?>
					</select>
				</th>
				<th>
					<button type="submit" class="btn btn-default" name="consultar" value="1"><span class="glyphicon glyphicon-search" aria-hidde="true"></span> Consultar</button>
				</th>
			</form>
		</tr>
		<tr>
			<th class="text-center">#</th>
			<th class="text-center">ID</th>
			<th class="text-center">Comprobante</th>
			<th class="text-center">Organización</th>
			<th class="text-center">País</th>
			<th class="text-center">Monto membresia</th>
			<th class="text-center">Estatus membresia</th>
			<th class="text-center">Fecha de activación</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$estatus = '';
		$contador = 1;
		while($membresias = mysql_fetch_assoc($row_membresias)){
			if($membresias['estatus_membresia'] == 'EN ESPERA'){
				$estatus = 'danger';
			}else{
				$estatus = 'success';
			}
		?>
			<tr>
				<td><?php echo $contador; ?></td>
				<td><?php echo $membresias['idmembresia']; ?></td>
				<td>
					<?php 
					if($membresias['estatus_membresia'] == 'EN ESPERA' && isset($membresias['archivo'])){
						echo '<form action="" method="POST" style="display:inline-block">';
							echo '<button name="aprobar_comprobante" value="'.$membresias['idmembresia'].'" class="btn btn-xs btn-success" data-toggle="tooltip" title="Autorizar membresia" onclick="return confirm(\'¿Desea Aprobar el Comprobante de pago?\');">
								<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
							</button>';

							echo '<button name="rechar_comprobante" value="'.$membresias['idmembresia'].'" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Rechazar membresia" onclick="return confirm(\'¿Desea Rechazar el Comprobante de Pago?\');">
								<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>	
							</button>';
							echo '<input type="hidden" name="idcomprobante_pago" value="'.$membresias['idcomprobante_pago'].'">';
							echo '<input type="hidden" name="idsolicitud_certificacion" value="'.$membresias['idsolicitud_certificacion'].'">';
						echo '</form>';
					}
					 ?>

					
					<?php 
					if(isset($membresias['archivo'])){
					?>
						<a class="btn btn-xs btn-primary" href="<?php echo $membresias['archivo']; ?>" target="_blank" data-toggle="tooltip" title="Descargar Comprobante">
							<span class="glyphicon glyphicon-file" aria-hidden="true"></span>
						</a>
					<?php
					}else{
						echo '<p style="color:red">Comprobante no disponible</p>';
					}
					 ?>
				</td>
				<td><?php echo $membresias['abreviacion']; ?></td>
				<td><?php echo $membresias['pais'] ?></td>
				<td><?php echo $membresias['monto']; ?></td>
				<td class="<?php echo $estatus; ?>"><?php echo $membresias['estatus_membresia']; ?></td>
				<td>
					<?php 
					if(isset($membresias['fecha_registro'])){
						echo date('d/m/Y',$membresias['fecha_registro']);
					} 
					?>
				</td>
			</tr>
		<?php
		$contador++;
		}
		 ?>
	</tbody>
</table>