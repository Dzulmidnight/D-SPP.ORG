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
	$row_membresias = mysql_query("SELECT opp.abreviacion, opp.pais, membresia.*, comprobante_pago.monto, comprobante_pago.archivo, comprobante_pago.aviso1, comprobante_pago.aviso2, comprobante_pago.aviso3, proceso_certificacion.fecha_registro AS 'periodo' FROM membresia INNER JOIN opp ON membresia.idopp = opp.idopp INNER JOIN comprobante_pago ON membresia.idcomprobante_pago = comprobante_pago.idcomprobante_pago LEFT JOIN proceso_certificacion ON membresia.idsolicitud_certificacion = proceso_certificacion.idsolicitud_certificacion WHERE proceso_certificacion.estatus_interno = 8 GROUP BY membresia.idsolicitud_certificacion ORDER BY membresia.idmembresia DESC", $dspp) or die(mysql_error());
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
			</form>
		</tr>
		<tr>
			<th class="text-center">#</th>
			<th class="text-center">ID</th>
			<th class="text-center">Comprobante</th>
			<th class="text-center">Organización</th>
			<th class="text-center">Fecha dictamen</th>
			<th class="text-center">Recordatorio 1</th>
			<th class="text-center">Recordatorio 2</th>
			<th class="text-center">Alerta</th>
			<th class="text-center">Periodo</th>
			<th class="text-center">País</th>
			<th class="text-center">Monto membresia</th>
			<th class="text-center">Estatus membresia</th>
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

		while($membresias = mysql_fetch_assoc($row_membresias)){

			$fecha_dictamen = $membresias['periodo'];
			$recordatorio1 = $fecha_dictamen + $diez_dias;
			$recordatorio2 = $fecha_dictamen + $veinte_dias;
			$alerta_suspension = $recordatorio2 + $cinco_dias;

			if($membresias['aviso1']){
				$clase_aviso1 = 'label label-success';
			}else{
				$clase_aviso1 = 'label label-default';
			}
			if($membresias['aviso2']){
				$clase_aviso2 = 'label label-success';
			}else{
				$clase_aviso2 = 'label label-default';
			}
			if($membresias['aviso3']){
				$clase_aviso3 = 'label label-success';
			}else{
				$clase_aviso3 = 'label label-default';
			}



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
				<!-- INICIAN LOS PERIODOS DE LAS ALERTAS -->
				<td class="warning"> <!-- fecha del dictamen positivo -->
					<?php echo date('d/m/Y', $membresias['periodo']); ?>
				</td>
				<td> <!-- RECORDATORIO 1 -->
					<?php echo '<span class="'.$clase_aviso1.'">'.date('d/m/Y', $recordatorio1).'</span>'; ?>
				</td>
				<td> <!-- RECORDATORIO 2 -->
					<?php echo '<span class="'.$clase_aviso2.'">'.date('d/m/Y', $recordatorio2).'</span>'; ?>
				</td>
				<td> <!-- ALERTA -->
					<?php echo '<span class="'.$clase_aviso3.'">'.date('d/m/Y', $alerta_suspension).'</span>'; ?>
				</td>
				<!-- TERMINAN LOS PERIODOS DE LAS ALERTAS -->
				<td><?php echo date('d/m/Y', $membresias['periodo']); ?></td>
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

<?php

$query = "SELECT * FROM comprobante_pago";
$consultar = mysql_query($query,$dspp) or die(mysql_error());
while($registros = mysql_fetch_assoc($consultar)){

}
 ?>

 <table class="table table-bordered table-condensed">
 	<thead>
 		<tr>
 			<th>Nº</th>
 			<th>ID COMPROBANTE</th>
 			<th>ID MEMBRESIA</th>
 			<th>ID PROCESO_CERTIFICACIÓN</th>
 			<th>COMPROBANTE?</th>
 			<th>FECHA DICTAMEN</th>
 			<th>AVISO 1</th>
 			<th>AVISO 2</th>
 			<th>AVISO 3</th>
 		</tr>
 	</thead>
 	<?php

 	$fecha_actual = time();
	$cinco_dias = 432000;
	$diez_dias = 864000;
	$veinte_dias = $diez_dias*2;

 	$query = "SELECT opp.nombre AS 'nombre_opp', opp.abreviacion AS 'abreviacion_opp', solicitud_certificacion.contacto1_email, solicitud_certificacion.contacto2_email, solicitud_certificacion.adm1_email, solicitud_certificacion.adm2_email, proceso_certificacion.idproceso_certificacion, proceso_certificacion.idsolicitud_certificacion, proceso_certificacion.fecha_registro AS 'fecha_dictamen', membresia.idmembresia, membresia.idopp, membresia.idcomprobante_pago, comprobante_pago.monto, comprobante_pago.estatus_comprobante, comprobante_pago.archivo, comprobante_pago.aviso1, comprobante_pago.aviso2, comprobante_pago.aviso3 FROM proceso_certificacion INNER JOIN solicitud_certificacion ON proceso_certificacion.idsolicitud_certificacion = solicitud_certificacion.idsolicitud_certificacion INNER JOIN membresia ON proceso_certificacion.idsolicitud_certificacion = membresia.idsolicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp INNER JOIN comprobante_pago ON membresia.idcomprobante_pago = comprobante_pago.idcomprobante_pago WHERE proceso_certificacion.estatus_interno = 8 ORDER BY proceso_certificacion.fecha_registro DESC";
 	$ejecutar = mysql_query($query, $dspp) or die(mysql_error());
 	$contador = 1;
 	while($registros = mysql_fetch_assoc($ejecutar)){
	 	$formato_dictamen = date('d/m/Y', $registros['fecha_dictamen']);
		$fecha_dictamen = $registros['fecha_dictamen'];
		$recordatorio1 = $fecha_dictamen + $diez_dias;
		$recordatorio2 = $fecha_dictamen + $veinte_dias;
		$alerta_suspension = $recordatorio2 + $cinco_dias;

			if($registros['aviso1']){
				$clase_aviso1 = 'label label-success';
			}else{
				$clase_aviso1 = 'label label-default';
			}
			if($registros['aviso2']){
				$clase_aviso2 = 'label label-success';
			}else{
				$clase_aviso2 = 'label label-default';
			}
			if($registros['aviso3']){
				$clase_aviso3 = 'label label-success';
			}else{
				$clase_aviso3 = 'label label-default';
			}


 	?>
 		<tr>
 			<td>
 				<?php echo $contador; ?>
 			</td>
 			<td>
 				<?php echo $registros['idcomprobante_pago']; ?>
 			</td>
 			<td>
 				<?php echo $registros['idmembresia']; ?>
 			</td>
 			<td>
 				<?php echo $registros['idproceso_certificacion']; ?>
 			</td>
 			<td>
 				<?php 
 				if(isset($registros['archivo'])){
 					echo 'SI';
 				}else{
 					echo 'NO';
 				}
 				 ?>
 			</td>
 			<td>
 				<?php 
 				
 				echo date('d/m/Y', $registros['fecha_dictamen']); 
 				
 				?>
 			</td>
			<td> <!-- RECORDATORIO 1 -->
				<?php 
				echo '<span class="'.$clase_aviso1.'">'.date('d/m/Y', $recordatorio1).'</span>'; 
			 	/// notificación 1º aviso
			 	if(!$registros['aviso1'] && $registros['estatus_comprobante'] != 'ACEPTADO'){
			 		if($fecha_actual >= $recordatorio1){

			 			$query = "UPDATE comprobante_pago SET aviso1 = 1 WHERE idcomprobante_pago = $registros[idcomprobante_pago]";
			 			$updateSQL = mysql_query($query, $dspp) or die(mysql_error());
			 			echo '<p style="color:red">ENVIADO</p>';

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
			 			echo '<p style="color:green">ACTIVO</p>';
			 		}
			 	}

				?>
			</td>
			<td> <!-- RECORDATORIO 2 -->
				<?php 
				echo '<span class="'.$clase_aviso2.'">'.date('d/m/Y', $recordatorio2).'</span>'; 
			 	/// notificación 2º aviso
			 	if(!$registros['aviso2'] && $registros['estatus_comprobante'] != 'ACEPTADO'){
			 		if($fecha_actual >= $recordatorio2){
			 			echo '<p style="color:red">ENVIADO</p>';
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
			 			echo '<p style="color:green">ACTIVO</p>';
			 		}
			 	}

				?>
			</td>
			<td> <!-- ALERTA -->
				<?php 
				echo '<span class="'.$clase_aviso3.'">'.date('d/m/Y', $alerta_suspension).'</span>'; 
			 	/// notificación 2º aviso
			 	if(!$registros['aviso3'] && $registros['estatus_comprobante'] != 'ACEPTADO'){
			 		if($fecha_actual >= $alerta_suspension){
			 			echo '<p style="color:red">ENVIADO</p>';
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

						$mail->Subject = utf8_decode($asunto);
						$mail->Body = utf8_decode($cuerpo_mensaje);
						$mail->MsgHTML(utf8_decode($cuerpo_mensaje));
						$mail->Send();
						$mail->ClearAddresses();

			 		}else{
			 			echo '<p style="color:green">ACTIVO</p>';
			 		}
			 	}

				?>
			</td>

 		</tr>
 	<?php


 	/// notificación 2º aviso

 	///notificación 3º aviso


 	$contador++;
 	}
 	 ?>
 </table>