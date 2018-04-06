<?php 
require_once('../Connections/dspp.php'); 
require_once('../Connections/mail.php');

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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}


/************ VARIABLES DE CONTROL ******************/
//ESTATUS PUBLICO //////////////////////////

//1) Solicitud
//2) En proceso
//3) Evaluacion positiva
//4) Certificada
//5) No certificada

//ESTATUS INTERNO ///////////////////////////
//1) 1ra Evaluacion
//2) Completar informacion
//3) 2da revision
//4) Proceso interrumpido
//5) Evaluacion in situ
//6) Informe de evaluacion
//7) Acciones correctivas
//8) Dictamen positivo
//9) Dictamen negativo
//10) Certificada
//11) Certificado expirado
//12) Certificado por expirar
//13) Suspendida
//14) Cancelada
//15) Desactivacion
//16) Aviso de renovacion del certificado
//20) Renovación del certificado
$charset='utf-8';
$fecha = time();
$ruta_croquis = "../../archivos/oppArchivos/croquis/";
$spp_global = "cert@spp.coop";
$administrador = "yasser.midnight@gmail.com";
$idoc = $_SESSION['idoc'];
/************ VARIABLES DE CONTROL ******************/



if(isset($_POST['insertar_solicitud']) && $_POST['insertar_solicitud'] == 1){
	$estatus_publico = 1; // EN REVISIÓN
	$estatus_interno = NULL;
	$estatus_dspp = 1; // SOLICITUD EN REVISIÓN
	$alcance_opp = "";
	$idopp = $_POST['idopp'];


	/* INICIA CAPTURA ALCANCE DEL OPP */
	if(isset($_POST['produccion'])){
		$produccion = $_POST['produccion'];
	}else{
		$produccion = '';
	}
	if(isset($_POST['procesamiento'])){
		$procesamiento = $_POST['procesamiento'];
	}else{
		$procesamiento = '';
	}
	if(isset($_POST['exportacion'])){
		$exportacion = $_POST['exportacion'];
	}else{
		$exportacion = '';
	}

	/* TERMINA CAPTURA ALCANCE DEL OPP */

	if(isset($_POST['op_preg12'])){
		$op_preg12 = $_POST['op_preg12'];
	}else{
		$op_preg12 = "";
	}

	if(isset($_POST['op_preg13'])){
		if($_POST['op_preg13'] == 'mayor'){
			$op_preg13 = $_POST['op_preg13_1'];
		}else{
			$op_preg13 = $_POST['op_preg13'];
		}
	}else{
		$op_preg13 = "";
	}



	if(!empty($_FILES['op_preg15']['name'])){
	    $_FILES["op_preg15"]["name"];
	      move_uploaded_file($_FILES["op_preg15"]["tmp_name"], $ruta_croquis.date("Ymd H:i:s")."_".$_FILES["op_preg15"]["name"]);
	      $croquis = $ruta_croquis.basename(date("Ymd H:i:s")."_".$_FILES["op_preg15"]["name"]);
	}else{
		$croquis = NULL;
	}

	// INGRESAMOS LA INFORMACION A LA SOLICITUD DE CERTIFICACION
	$insertSQL = sprintf("INSERT INTO solicitud_certificacion (tipo_solicitud, idopp, idoc, contacto1_nombre, contacto2_nombre, contacto1_cargo, contacto2_cargo, contacto1_email, contacto2_email, contacto1_telefono, contacto2_telefono, adm1_nombre, adm2_nombre, adm1_email, adm2_email, adm1_telefono, adm2_telefono, resp1, resp2, resp3, resp4, op_preg1, preg1_1, preg1_2, preg1_3, preg1_4, op_preg2, op_preg3, produccion, procesamiento, exportacion, op_preg5, op_preg6, op_preg7, op_preg8, op_preg10, op_preg12, op_preg13, op_preg14, op_preg15, responsable, fecha_registro, estatus_dspp ) VALUES (%s, %s, %s, %s, %s, %s,%s, %s, %s, %s, %s, %s, %s, %s, %s,%s, %s, %s, %s, %s, %s, %s, %s, %s,%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
		   GetSQLValueString($_POST['tipo_solicitud'], "text"),
		   GetSQLValueString($idopp, "int"),
           GetSQLValueString($idoc, "int"),
           GetSQLValueString($_POST['contacto1_nombre'], "text"),
           GetSQLValueString($_POST['contacto2_nombre'], "text"),
           GetSQLValueString($_POST['contacto1_cargo'], "text"),
           GetSQLValueString($_POST['contacto2_cargo'], "text"),
           GetSQLValueString($_POST['contacto1_email'], "text"),
           GetSQLValueString($_POST['contacto2_email'], "text"),
           GetSQLValueString($_POST['contacto1_telefono'], "text"),
           GetSQLValueString($_POST['contacto2_telefono'], "text"),
           GetSQLValueString($_POST['adm1_nombre'], "text"),
           GetSQLValueString($_POST['adm2_nombre'], "text"),
           GetSQLValueString($_POST['adm1_email'], "text"),
           GetSQLValueString($_POST['adm2_email'], "text"),
           GetSQLValueString($_POST['adm1_telefono'], "text"),
           GetSQLValueString($_POST['adm2_telefono'], "text"),
           GetSQLValueString($_POST['resp1'], "text"),
           GetSQLValueString($_POST['resp2'], "text"),
           GetSQLValueString($_POST['resp3'], "text"),
           GetSQLValueString($_POST['resp4'], "text"),
           GetSQLValueString($_POST['op_preg1'], "text"),
           GetSQLValueString($_POST['preg1_1'], "text"),
           GetSQLValueString($_POST['preg1_2'], "text"),
           GetSQLValueString($_POST['preg1_3'], "text"),
           GetSQLValueString($_POST['preg1_4'], "text"),
           GetSQLValueString($_POST['op_preg2'], "text"),
           GetSQLValueString($_POST['op_preg3'], "text"),
           GetSQLValueString($produccion, "int"),
           GetSQLValueString($procesamiento, "int"),
           GetSQLValueString($exportacion, "int"),
           GetSQLValueString($_POST['op_preg5'], "text"),
           GetSQLValueString($_POST['op_preg6'], "text"),
           GetSQLValueString($_POST['op_preg7'], "text"),
           GetSQLValueString($_POST['op_preg8'], "text"),
           GetSQLValueString($_POST['op_preg10'], "text"),
           GetSQLValueString($op_preg12, "text"),
           GetSQLValueString($op_preg13, "text"),
           GetSQLValueString($_POST['op_preg14'], "text"),
           GetSQLValueString($croquis, "text"),
           GetSQLValueString($_POST['responsable'], "text"),
           GetSQLValueString($fecha, "int"),
           GetSQLValueString($estatus_dspp, "int"));


		  $Result1 = mysql_query($insertSQL, $dspp) or die(mysql_error());
		 
		 $idsolicitud_certificacion = mysql_insert_id($dspp); 

	///INGRESAMOS EL TIPO DE SOLICITUD A LA TABLA OPP y EL ALCANCE DE LA OPP
	$updateSQL = sprintf("UPDATE opp SET produccion = %s, procesamiento = %s, exportacion = %s, estatus_opp = %s WHERE idopp = %s",
		GetSQLValueString($produccion, "int"),
		GetSQLValueString($procesamiento, "int"),
		GetSQLValueString($exportacion, "int"),
		GetSQLValueString($_POST['tipo_solicitud'], "int"),
		GetSQLValueString($idopp, "int"));
	$actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());

	// INGRESAMOS LOS CONTACTOS DE LA SOLICITUD A LA TABLA DE CONTACTOS
	if(!empty($_POST['contacto1_nombre'])){
		$insertSQL = sprintf("INSERT INTO contactos(idopp, nombre, cargo, telefono1, email1, idsolicitud_certificacion) VALUES (%s, %s, %s, %s, %s, %s)",
			GetSQLValueString($idopp, "int"),
			GetSQLValueString($_POST['contacto1_nombre'], "text"),
			GetSQLValueString($_POST['contacto1_cargo'], "text"),
			GetSQLValueString($_POST['contacto1_telefono'], "text"),
			GetSQLValueString($_POST['contacto1_email'], "text"),
			GetSQLValueString($idsolicitud_certificacion, "int"));
		$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

	}
	if(!empty($_POST['contacto2_nombre'])){
		$insertSQL = sprintf("INSERT INTO contactos(idopp, nombre, cargo, telefono1, email1, idsolicitud_certificacion) VALUES (%s, %s, %s, %s, %s, %s)",
			GetSQLValueString($idopp, "int"),
			GetSQLValueString($_POST['contacto2_nombre'], "text"),
			GetSQLValueString($_POST['contacto2_cargo'], "text"),
			GetSQLValueString($_POST['contacto2_telefono'], "text"),
			GetSQLValueString($_POST['contacto2_email'], "text"),
			GetSQLValueString($idsolicitud_certificacion, "int"));
		$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

	}
	if(!empty($_POST['adm1_nombre'])){
		$insertSQL = sprintf("INSERT INTO contactos(idopp, nombre, cargo, telefono1, email1, idsolicitud_certificacion) VALUES (%s, %s, %s, %s, %s, %s)",
			GetSQLValueString($idopp, "int"),
			GetSQLValueString($_POST['adm1_nombre'], "text"),
			GetSQLValueString('ADMINISTRATIVO', "text"),
			GetSQLValueString($_POST['adm1_telefono'], "text"),
			GetSQLValueString($_POST['adm1_email'], "text"),
			GetSQLValueString($idsolicitud_certificacion, "int"));
		$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

	}
	if(!empty($_POST['adm2_nombre'])){
		$insertSQL = sprintf("INSERT INTO contactos(idopp, nombre, cargo, telefono1, email1, idsolicitud_certificacion) VALUES (%s, %s, %s, %s, %s, %s)",
			GetSQLValueString($idopp, "int"),
			GetSQLValueString($_POST['adm2_nombre'], "text"),
			GetSQLValueString('ADMINISTRATIVO', "text"),
			GetSQLValueString($_POST['contacto2_telefono'], "text"),
			GetSQLValueString($_POST['contacto2_email'], "text"),
			GetSQLValueString($idsolicitud_certificacion, "int"));
		$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

	}



	// INGRESAMOS EL NUMERO DE SOCIOS A LA TABLA NUM_SOCIOS
	if(isset($_POST['resp1'])){
		if($_POST['tipo_solicitud'] == 'NUEVA'){ //si es nueva, se inserta un nuevo registro
			$insertSQL = sprintf("INSERT INTO num_socios (idopp, numero, fecha_registro) VALUES (%s, %s, %s)",
				GetSQLValueString($idopp, "int"),
				GetSQLValueString($_POST['resp1'], "text"),
				GetSQLValueString($fecha, "int"));
			$ejecutar = mysql_query($insertSQL,$dspp) or die(mysql_error());
		}else{// si es renovacion, se actualiza el registro
			$updateSQL = sprintf("UPDATE num_socios SET numero = %s, fecha_registro = %s WHERE idopp = %s",
				GetSQLValueString($_POST['resp1'], "text"),
				GetSQLValueString($fecha, "int"),
				GetSQLValueString($idopp, "int"));
			$actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());
		}

	}


		 // INGRESAMOS EL PORCENTAJE DE VENTA DE LOS PRODUCTOS

	 	if(!empty($_POST['organico']) || !empty($_POST['comercio_justo']) || !empty($_POST['spp']) || !empty($_POST['sin_certificado'])){
	 		$insertSQL = sprintf("INSERT INTO porcentaje_productoVentas (organico, comercio_justo, spp, sin_certificado, idsolicitud_certificacion, idopp) VALUES (%s, %s, %s, %s, %s, %s)",
	 			GetSQLValueString($_POST['organico'], "text"),
	 			GetSQLValueString($_POST['comercio_justo'], "text"),
	 			GetSQLValueString($_POST['spp'], "text"),
	 			GetSQLValueString($_POST['sin_certificado'], "text"),
	 			GetSQLValueString($idsolicitud_certificacion, "int"),
	 			GetSQLValueString($idopp, "int"));
	 		$insertar = mysql_query($insertSQL,$dspp) or die(mysql_error());
	 	}
	


		/*************************** INICIA INSERTAR PROCESO DE CERTIFICACIÓN ***************************/
		$insertSQL = sprintf("INSERT INTO proceso_certificacion (idsolicitud_certificacion, estatus_publico, estatus_interno, estatus_dspp, fecha_registro) VALUES (%s, %s, %s, %s, %s)",
			GetSQLValueString($idsolicitud_certificacion, "int"),
			GetSQLValueString($estatus_publico, "int"),
			GetSQLValueString($estatus_interno, "int"),
			GetSQLValueString($estatus_dspp, "int"),
			GetSQLValueString($fecha, "int"));
		$insertar = mysql_query($insertSQL,$dspp) or die(mysql_error());
		/*************************** TERMINA INSERTAR PROCESO DE CERTIFICACIÓN ***************************/



		/*************************** INICIA INSERTAR CERTIFICACIONES ***************************/
			if(isset($_POST['certificacion'])){
				$certificacion = $_POST['certificacion'];
			}else{
				$certificacion = NULL;
			}


			if(isset($_POST['certificadora'])){
				$certificadora = $_POST['certificadora'];
			}else{
				$certificadora = NULL;
			}

			if(isset($_POST['ano_inicial'])){
				$ano_inicial = $_POST['ano_inicial'];
			}else{
				$ano_inicial = NULL;
			}

			if(isset($_POST['interrumpida'])){
				$interrumpida = $_POST['interrumpida'];
			}else{
				$interrumpida = NULL;
			}

			for($i=0;$i<count($certificacion);$i++){
				if($certificacion[$i] != NULL){
					#for($i=0;$i<count($certificacion);$i++){
					$insertSQL = sprintf("INSERT INTO certificaciones (idsolicitud_certificacion, certificacion, certificadora, ano_inicial, interrumpida) VALUES (%s, %s, %s, %s, %s)",
					    GetSQLValueString($idsolicitud_certificacion, "int"),
					    GetSQLValueString(strtoupper($certificacion[$i]), "text"),
					    GetSQLValueString(strtoupper($certificadora[$i]), "text"),
					    GetSQLValueString($ano_inicial[$i], "text"),
					    GetSQLValueString($interrumpida[$i], "text"));

					$Result = mysql_query($insertSQL, $dspp) or die(mysql_error());
					#}
				}
			}
		/*************************** INICIA INSERTAR CERTIFICACIONES ***************************/




		/*************************** INICIA INSERTAR PRODUCTOS ***************************/
		$producto_general = $_POST['producto_general'];
		$producto = $_POST['producto'];
		$volumen = $_POST['volumen'];
		$materia = $_POST['materia'];
		$destino = $_POST['destino'];
		/*$marca_propia = $_POST['marca_propia'];
		$marca_cliente = $_POST['marca_cliente'];
		$sin_cliente = $_POST['sin_cliente'];*/

		for ($i=0;$i<count($producto);$i++) { 
			if($producto[$i] != NULL){

					$array1[$i] = "terminado".$i; 
					$array2[$i] = "marca_propia".$i;
					$array3[$i] = "marca_cliente".$i;
					$array4[$i] = "sin_cliente".$i;

					if(isset($_POST[$array1[$i]])){
						$terminado = $_POST[$array1[$i]];
					}else{
						$terminado = null;
					}
					if(isset($_POST[$array2[$i]])){
						$marca_propia = $_POST[$array2[$i]];
					}else{
						$marca_propia = null;
					}
					if(isset($_POST[$array3[$i]])){
						$marca_cliente = $_POST[$array3[$i]];
					}else{
						$marca_cliente = null;
					}
					if(isset($_POST[$array4[$i]])){
						$sin_cliente = $_POST[$array4[$i]];
					}else{
						$sin_cliente = null;
					}

					//$terminado = $_POST[$array1[$i]];
					//$marca_propia = $_POST[$array2[$i]];
					//$marca_cliente = $_POST[$array3[$i]];
					//$sin_cliente = $_POST[$array4[$i]];

					$str = iconv($charset, 'ASCII//TRANSLIT', $producto_general[$i]);
					$producto_general[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));

					$str = iconv($charset, 'ASCII//TRANSLIT', $producto[$i]);
					$producto[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));

					$str = iconv($charset, 'ASCII//TRANSLIT', $destino[$i]);
					$destino[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));

					$str = iconv($charset, 'ASCII//TRANSLIT', $materia[$i]);
					$materia[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));


				    $insertSQL = sprintf("INSERT INTO productos (idopp, idsolicitud_certificacion, producto_general, producto, volumen, terminado, materia, destino, marca_propia, marca_cliente, sin_cliente) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
				    	GetSQLValueString($idopp, "int"),
				          GetSQLValueString($idsolicitud_certificacion, "int"),
				          GetSQLValueString($producto_general[$i], "text"),
				          GetSQLValueString($producto[$i], "text"),
				          GetSQLValueString($volumen[$i], "text"),
				          GetSQLValueString($terminado[$i], "text"),
				          GetSQLValueString($materia[$i], "text"),
				          GetSQLValueString($destino[$i], "text"),
				          GetSQLValueString($marca_propia[$i], "text"),
				          GetSQLValueString($marca_cliente[$i], "text"),                    
				          GetSQLValueString($sin_cliente[$i], "text"));

				  $Result = mysql_query($insertSQL, $dspp) or die(mysql_error());
			}
		}
		/***************************** TERMINA INSERTAR PRODUCTOS ******************************/

		///// INICIA ENVIO DEL MENSAJE POR CORREO AL OC y a SPP GLOBAL
		$asunto = "D-SPP Solicitud de Certificación para Organizaciones de Pequeños Productores";
		$row_oc = mysql_query("SELECT * FROM oc WHERE idoc = $idoc", $dspp) or die(mysql_error());
		$oc = mysql_fetch_assoc($row_oc);

		$row_opp = mysql_query("SELECT * FROM opp WHERE idopp = $idopp", $dspp) or die(mysql_error());
		$opp = mysql_fetch_assoc($row_opp);

		$cuerpo_correo = '
			<html>
			<head>
				<meta charset="utf-8">
			</head>
			<body>
			
				<table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
				  <tbody>
		            <tr>
		              <th rowspan="7" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
		              <th scope="col" align="left" width="280"><strong>Solicitud de Certificación para Organizaciones de Pequeños Productores / Application for Small Producers\' Organization Certification </strong></th>
		            </tr>
		            <tr>
		              <td style="padding-top:10px;">
		   
		              Para poder consultar la solicitud, por favor iniciar sesión en su cuenta de OC(Organismo de Certificación) en el siguiente enlace: <a href="http://d-spp.org" target="_new">www.d-spp.org</a>
		              <br>
		              To consult the application, please log in to your CE(Certification Entity) account, in the following link: <a href="http://d-spp.org" target="_new">www.d-spp.org</a>

		         

		              </td>
		            </tr>
				    <tr>
				      <td align="left">Teléfono / phone Organización: '.$opp['telefono'].'</td>
				    </tr>

				    <tr>
				      <td align="left">'.$opp['pais'].'</td>
				    </tr>
				    <tr>
				      <td align="left" style="color:#ff738a;">Email: '.$opp['email'].'</td>
				    </tr>
				    <tr>
				      <td align="left" style="color:#ff738a;">Email: '.$_POST['contacto1_email'].'</td>
				    </tr>

				    <tr>
				      <td colspan="2">
				        <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;" border="1" width="650px">
				          <tbody>
				            <tr style="font-size: 12px; text-align:center; background-color:#dff0d8; color:#3c763d;" height="50px;">
				              <td width="130px">Nombre de la organización/Organization name</td>
				              <td width="130px">País / Country</td>
				              <td width="130px">Organismo de Certificación / Certification Entity</td>
				           
				              <td width="130px">Fecha de solicitud/Date of application</td>
				            </tr>
				            <tr style="font-size: 12px;">
				              <td style="padding:10px;">
				              	'.$opp['nombre'].'
				              </td>
				              <td style="padding:10px;">
				                '.$opp['pais'].'
				              </td>
				              <td style="padding:10px;">
				                '.$oc['nombre'].'
				              </td>
				              <td style="padding:10px;">
				              '.date('d/m/Y', $fecha).'
				              </td>
				            </tr>

				          </tbody>
				        </table>        
				      </td>
				    </tr>

				  </tbody>
				</table>

			</body>
			</html>
		';
		///// TERMINA ENVIO DEL MENSAJE POR CORREO AL OC y a SPP GLOBAL

		if(!empty($oc['email1'])){
			$token = strtok($oc['email1'], "\/\,\;");
			while ($token !== false)
			{
				$mail->AddAddress($token);
				$token = strtok('\/\,\;');
			}
		}
		if(!empty($oc['email2'])){
			$token = strtok($oc['email2'], "\/\,\;");
			while ($token !== false)
			{
				$mail->AddAddress($token);
				$token = strtok('\/\,\;');
			}
		}
	    $mail->AddBCC($spp_global);
        //$mail->Username = "soporte@d-spp.org";
        //$mail->Password = "/aung5l6tZ";
        $mail->Subject = utf8_decode($asunto);
        $mail->Body = utf8_decode($cuerpo_correo);
        $mail->MsgHTML(utf8_decode($cuerpo_correo));
        $mail->Send();
        $mail->ClearAddresses();

 		$mensaje = "Se ha enviado la Solicitud de Certificacion al OC, en breve seras contactado";


}

  //$insertGoTo = "main_menu.php?SOLICITUD&add&mensaje=Solicitud agregada correctamente, se ha notificado al OC por email.";
/*$query = "SELECT * FROM opp WHERE idoc = $idoc";
$row_opp = mysql_query($query,$dspp) or die(mysql_error());
$opp = mysql_fetch_assoc($row_opp);
*/
$row_opp = mysql_query("SELECT * FROM opp WHERE idoc = $idoc", $dspp) or die(mysql_error());
?>

<div class="row">
	<div class="col-lg-12">
	<?php 
	if(isset($mensaje)){
	?>
	<div class="col-md-12 alert alert-success alert-dismissible" role="alert">
	  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	  <?php echo $mensaje; ?>
	</div>
	<?php
	}
	?>
	</div>
</div>

<div class="row" style="font-size:12px;">
	<form action="" name="" method="POST" enctype="multipart/form-data">
		<fieldset>
			<div class="col-md-12 alert alert-primary" style="padding:7px;">
				<h3 class="text-center">Demande de Certification pour les Organisations de Petits Producteurs</h3>
			</div>


			<div class="col-md-12 text-center alert alert-success" style="padding:7px;"><b>INFORMATIONS GENERALES</b></div>

			<div class="col-lg-12 alert alert-info" style="padding:7px;">
				<div class="col-md-6">
					<div class="col-xs-12">
						<b>Envoi à l'OC (choisissez l'OC auquel vous souhaitez envoyer la demande) :</b>
					</div>
					<div class="col-xs-12">
						<select class="form-control" name="idopp" id="" required>
							<option value="">Sélectionnez une organisation</option>
							<?php 
							while($opp = mysql_fetch_assoc($row_opp)){
							?>
							<option value="<?php echo $opp['idopp']; ?>"><?php if(!empty($opp['abreviacion'])){ echo $opp['abreviacion']; }else{ echo $opp['nombre']; } ?></option>
							<?php
							}
							 ?>
						</select>
					</div>
				</div>
				<div class="col-md-6">
					<div class="col-xs-12">
						<p class="text-center"><strong>Sélectionnez le type de demande</strong></p>
					</div>
					<div class="col-xs-6">
						<label for="nueva">Première fois</label>
						<input type="radio" class="form-control" id="nueva" name="tipo_solicitud" value="NUEVA">
					</div>
					<div class="col-xs-6">
						<label for="renovacion">Renouvellement du certificat</label>
						<input type="radio" class="form-control" id="renovacion" name="tipo_solicitud" value="RENOVACION">
					</div>
				</div>
			</div>
			<!------ INICIA INFORMACION GENERAL Y DATOS FISCALES ------>
			<div class="col-lg-12">
				<div class="col-md-12">
					<p class="alert alert-info text-center"><b>L'INFORMATION GÉNÉRALE ET LES INFORMATIONS FISCALES SONT ENREGISTRÉES DANS LE SYSTÈME</b></p>
				</div>
			<!------ INICIA INFORMACION GENERAL Y DATOS FISCALES ------>

			<!------ INICIA INFORMACION CONTACTOS Y AREA ADMINISTRATIVA ------>
			<div class="col-lg-12">
				<div class="col-md-6">
					<div class="col-md-12 text-center alert alert-warning" style="padding:7px;">PERSONNE(S) A CONTACTER</div>

					<label for="persona1">PERSONNE(S) A CONTACTER :</label>
					<input type="text" class="form-control" id="persona1" name="contacto1_nombre" placeholder="* Nombre Persona 1" required>
					<input type="text" class="form-control" id="" name="contacto2_nombre" placeholder="Nombre Persona 2">

					<label for="cargo">FONCTION(S)</label>
					<input type="text" class="form-control" id="cargo" name="contacto1_cargo" placeholder="* Cargo Persona 1" required>
					<input type="text" class="form-control" id="" name="contacto2_cargo" placeholder="Cargo Persona 2">

					<label for="email">ADRESSE MAIL DES PERSONNES A CONTACTER:</label>
					<input type="email" class="form-control" id="email" name="contacto1_email" placeholder="* Email Persona 1" required>
					<input type="email" class="form-control" id="" name="contacto2_email" placeholder="Email Persona 2">

					<label for="telefono">TELEPHONE</label>
					<input type="text" class="form-control" id="telefono" name="contacto1_telefono" placeholder="* Telefono Persona 1" required>
					<input type="text" class="form-control" id="" name="contacto2_telefono" placeholder="Telefono Persona 2">
				</div>

				<div class="col-md-6">
					<div class="col-md-12 text-center alert alert-warning" style="padding:7px;">RESPONSABLE DU SERVICE ADMINISTRATIF</div>

					<label for="persona_adm">RESPONSABLE DU SERVICE ADMINISTRATIF</label>
					<input type="text" class="form-control" id="persona_adm" name="adm1_nombre" placeholder="Nombre Persona 1">
					<input type="text" class="form-control" id="" name="adm2_nombre" placeholder="Nombre Persona 2">

					<label for="email_adm">ADRESSE MAIL DU SERVICE ADMINISTRATIF</label>
					<input type="email" class="form-control" id="email_adm" name="adm1_email" placeholder="Email Persona 1">
					<input type="email" class="form-control" id="" name="adm2_email" placeholder="Email Persona 2">

					<label for="telefono_adm">TELEPHONE</label>
					<input type="text" class="form-control" id="telefono_adm" name="adm1_telefono" placeholder="Telefono Persona 1">
					<input type="text" class="form-control" id="" name="adm2_telefono" placeholder="Telefono Persona 2">
				</div>
			</div>
			<!------ FIN INFORMACION CONTACTOS Y AREA ADMINISTRATIVA ------>

			<!------ INICIA INFORMACION DATOS DE OPERACIÓN ------>
			<div class="col-lg-12">
				<div class="col-md-12">
					<label for="resp1">NOMBRE DE MEMBRES PRODUCTEURS:</label>
					<input type="text" class="form-control" id="resp1" name="resp1" placeholder="Solo numero" required>

					<label for="resp2">NOMBRE DE MEMBRES PRODUCTEURS DU (DES) PRODUIT(S) A INCLUIRE DANS LA CERTIFICATION</label>
					<input type="text" class="form-control" id="resp2" name="resp2" >

					<label for="resp3">VOLUME(S) DE PRODUCTION TOTALE PAR PRODUIT (UNITE DE MESURE)</label>
					<input type="text" class="form-control" id="resp3" name="resp3" >
					
					<label for="resp4">TAILLE MAXIMALE DE L’UNITE DE PRODUCTION PAR PRODUCTEUR DU (DES) PRODUIT(S) A INCLURE DANS LA CERTIFICATION</label>
					<input type="text" class="form-control" id="resp4" name="resp4" >
				</div>
			</div>

			<div class="col-md-12 text-center alert alert-success" style="padding:7px;">INFORMATIONS SUR LE TYPE D’OPERATION</div>

			<div class="col-lg-12">
				<div class="col-md-12">
					<label for="op_preg1">
						1.	INDIQUEZ-S’IL S’AGIT D’UNE ORGANISATION DE PETITS PRODUCTEURS DE 1er, 2eme, 3eme OU 4eme NIVEAU, AINSI QUE LE NOMBRE D’OPP DE 3eme, 2eme OU 1er NIVEAU ET LE NOMBRE DE COMMUNAUTES, DE ZONES OU DE GROUPES DE TRAVAIL DONT VOUS DISPOSEZ :
					</label>
					<textarea name="op_preg1" id="op_preg1" class="form-control" rows="2"></textarea>

					<div class="col-xs-3">
						<label for="preg1_1">
							1.1: NOMBRE D’OPP DE 3eme NIVEAU:
						</label>
						<input type="number" class="form-control" id="preg1_1" name="preg1_1" placeholder="Solo numero" >
					</div>
					<div class="col-xs-3">
						<label for="preg1_2">
							1.2: NOMBRE D’OPP DE 2eme NIVEAU:
						</label>
						<input type="text" class="form-control" id="preg1_2" name="preg1_2" >
					</div>
					<div class="col-xs-3">
						<label for="preg1_3">
							1.3: NOMBRE D’OPP DE 1er NIVEAU:
						</label>
						<input type="text" class="form-control" id="preg1_3" name="preg1_3" >
					</div>
					<div class="col-xs-3">
						<label for="preg1_4">
							1.4: NOMBRE DE COMMUNAUTES, DE ZONES OU DE GROUPES DE TRAVAIL:
						</label>
						<input type="text" class="form-control" id="preg1_4" name="preg1_4" >
					</div>


					<label for="op_preg2">
						2. INDIQUEZ QUEL(S) PRODUIT(S) VOUS SOUHAITEZ INCLURE DANS LA CERTIFICATION DU SYMBOLE DES PETITS PRODUCTEURS POUR LE(S) QUEL (S) L’ORGANISME DE CERTIFICATION REALIZERA L’EVALUATION.
					</label>
					<textarea name="op_preg2" id="op_preg2" class="form-control"></textarea>

					<label for="op_preg3">
						3.	INDIQUEZ SI VOTRE ORGANISATION SOUHAITE INCLURE UNE QUALIFICATION OPTIONNELLE POUR UNE UTILISATION COMPLEMENTAIRE AVEC LE LOGO GRAPHIQUE DU SYMBOLE DES PETITS PRODUCTEURS. <sup>4</sup>
					</label>
					<input type="text" class="form-control" id="op_preg3" name="op_preg3">

					<div >
						<label for="alcance_opp">
							4.	MARQUEZ D’UNE CROIX L’ACTIVITE EXERCEE PAR L’ORGANISATION DES PETITS PRODUCTEURS : 
						</label>
					</div>
					<div class="col-xs-4">
						<label>PRODUCTION</label>
						<input type="checkbox" name="produccion" class="form-control" value="1">
					</div>
					<div class="col-xs-4">
						<label>TRANSFORMATION</label>
						<input type="checkbox" name="procesamiento" class="form-control" value="1">
					</div>
					<div class="col-xs-4">
						<label>EXPORTATION</label>
						<input type="checkbox" name="exportacion" class="form-control" value="1">
					</div>

					<label for="op_preg5">
						5.	INDIQUEZ SI VOUS UTILISEZ EN SOUS-TRAITANCE LES SERVICES D’USINES DE TRANSFORMATION, D’ENTREPRISES DE COMMERCIALISATION OU D’ENTREPRISES D’IMPORT/EXPORT, LE CAS ECHEANT, MENTIONNEZ LE TYPE DE SERVICE REALISE.
					</label>
					<textarea name="op_preg5" id="op_preg5" class="form-control"></textarea>

					<label for="op_preg6">
						6.	SI VOUS SOUS-TRAITEZ DES SERVICES A DES USINES DE TRANSFORMATION, A DES ENTREPRISES DE COMMERCIALISATION OU A DES ENTREPRISES D’IMPORT/EXPORT, INDIQUEZ SI CELLES-CI SONT ENREGISTREES, EN COURS D’ENREGISTREMENT SOUS LE PROGRAMME DU SPP OU SI ELLES SERONT CONTROLEES AU TRAVERS DE L’ORGANISATION DE PETITS PRODUCTEURS.<sup>5</sup>
						<br>
						<small><sup>5</sup> Voir le document ‘Directives Générales du Système SPP’ dans sa dernière version.</small>
					</label>
					<textarea name="op_preg6" id="op_preg6" class="form-control"></textarea>

					<label for="op_preg7">
						7.	EN PLUS DE VOTRE SIEGE SOCIAL, INDIQUEZ LE NOMBRE DE CENTRES DE COLLECTE, DE TRANSFORMATION OU DE BUREAUX SUPPLEMENTAIRES QUE VOUS POSSEDEZ.  
					</label>
					<textarea name="op_preg7" id="op_preg7" class="form-control"></textarea>

					<label for="op_preg8">
						8.	EST-CE QUE VOUS DISPOSEZ D’UN SYSTEME DE CONTROLE INTERNE AFIN DE RESPECTER LES CRITERES DE LA NORME GENERALE DU SYMBOLE DES PETITS PRODUCTEURS? DANS CE CAS VEUILLEZ EXPLIQUER.
					<textarea name="op_preg8" id="op_preg8" class="form-control"></textarea>
					<p class="alert alert-info">9.	REMPLIR LE TABLEAU DE VOS CERTIFICATIONS, (EXEMPLE: EU, NOP, JASS, FLO, etc.)</p>

					<table class="table table-bordered" id="tablaCertificaciones">
						<tr>
							<td>CERTIFICATION</td>
							<td>CERTIFICATEUR</td>
							<td>ANNEE DE LA CERTIFICATION</td>
							<td>A-T-ELLE ETE INTERROMPUE?</td>	
							<td>
								<button type="button" onclick="tablaCertificaciones()" class="btn btn-primary" aria-label="Left Align">
								  <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
								</button>
								
							</td>
						</tr>
						<tr class="text-center">
							<td><input type="text" class="form-control" name="certificacion[0]" id="exampleInputEmail1" placeholder="CERTIFICACIÓN"></td>
							<td><input type="text" class="form-control" name="certificadora[0]" id="exampleInputEmail1" placeholder="CERTIFICADORA"></td>
							<td><input type="text" class="form-control" name="ano_inicial[0]" id="exampleInputEmail1" placeholder="AÑO INICIAL"></td>
							<!--<td><input type="text" class="form-control" name="interrumpida[0]" id="exampleInputEmail1" placeholder="¿HA SIDO INTERRUMPIDA?"></td>-->
							<td>
								<div class="col-xs-6">Oui<input type="radio" class="form-control" name="interrumpida[0]" value="SI"></div>
								<div class="col-xs-6">Non<input type="radio" class="form-control" name="interrumpida[0]" value="NO"></div>
							</td>
						</tr>
					</table>	

					<label for="op_preg10">
						10.	PARMI LES CERTIFICATIONS DONT VOUS DISPOSEZ ET LORS DE LEUR PLUS RECENTE EVALUATION INTERNE ET EXTERNE, COMBIEN DE NON CONFORMITES ONT ETE IDENTIFIEES? CELLES-CI ONT-ELLES ETE RESOLUES? QUEL EST LEUR ETAT ACTUEL? </label>
					<textarea name="op_preg10" id="op_preg10" class="form-control"></textarea>

					<p for="op_preg11">
						<b>11.	SUR L’ENSEMBLE DE VOS VENTES, QUEL EST LE POURCENTAGE REALISE SOUS LES CERTIFICATIONS BIOLOGIQUES, DU COMMERCE EQUITABLE ET / OU DU SYMBOLE DES PETITS PRODUCTEURS ?</b>
						<i>(* Entrez uniquement la quantité, le nombre entier ou les décimales)</i>
						<div class="row">
							<div class="col-lg-12">
								<div class="col-xs-3">
									<label for="organico">% BIOLOGIQUES</label>
									<input type="number" step="any" class="form-control" id="organico" name="organico" placeholder="Ej: 0.0">
								</div>
								<div class="col-xs-3">
									<label for="comercio_justo">% COMMERCE EQUITABLE</label>
									<input type="number" step="any" class="form-control" id="comercio_justo" name="comercio_justo" placeholder="Ej: 0.0">
								</div>
								<div class="col-xs-3">
									<label for="spp">SYMBOLE DES PETITS PRODUCTEURS</label>
									<input type="number" step="any" class="form-control" id="spp" name="spp" placeholder="Ej: 0.0">
								</div>
								<div class="col-xs-3">
									<label for="otro">Sans certificat</label>
									<input type="number" step="any" class="form-control" id="otro" name="sin_certificado" placeholder="Ej: 0.0">
								</div>
							</div>
						</div>
					</p>

					<p><b>12.	AVEZ-VOUS REALISE DES VENTES SOUS LE SPP DURANT LE CYCLE DE CERTIFICATION ANTERIEUR ? </b></p>
						<div class="col-xs-6">
							Oui <input type="radio" class="form-control" name="op_preg12" id="op_preg12" value="SI">
						</div>
						<div class="col-xs-6">
							Non <input type="radio" class="form-control" name="op_preg12" id="op_preg12" value="NO">
						</div>			

					<p>
						<b>13. SI VOTRE RÉPONSE A ÉTÉ POSITIF, VEUILLEZ INDIQUER LA GAMME DE LA VALEUR TOTALE DE VOS VENTES SPP DU CYCLE PRÉCÉDENT SELON LA TABLE SUIVANTE:</b>
					</p>

					<div class="well col-xs-12 " id="tablaVentas">
						<div class="col-xs-6"><p>Jusqu’à 3.000 USD</p></div>
						<div class="col-xs-6 "><input type="radio" name="op_preg13" class="form-control" id="ver" onclick="ocultar()" value="HASTA $3,000 USD"></div>
					
					
						<div class="col-xs-6"><p>Entre 3.000 et 10.000 USD</p></div>
						<div class="col-xs-6"><input type="radio" name="op_preg13" class="form-control" id="ver" onclick="ocultar()" value="ENTRE $3,000 Y $10,000 USD"></div>
					
					
						<div class="col-xs-6"><p>De 10.000 à 25.000 USD</p></div>
						<div class="col-xs-6"><input type="radio" name="op_preg13" class="form-control"  id="ver" onclick="ocultar()" value="ENTRE $10,000 A $25,000 USD"></div>
					
						<div class="col-xs-6"><p>Plus de 25.000 USD<sup>*</sup><br><h6><sup>*</sup>Indiquez le montant.</h6></p></div>
						<div class="col-xs-6"><input type="radio" name="op_preg13" class="form-control" id="exampleInputEmail1" onclick="mostrar()" value="mayor">
							<input type="text" name="op_resp13_1" class="form-control" id="oculto" style='display:none;' placeholder="Indiquez le montant.">
						</div>

					</div>
							
					<label for="op_preg14">
						14. DATE ESTIMEE DE DEBUT D’UTILISATION DU SYMBOLE DES PETITS PRODUCTEURS :
					</label>
					<input type="text" class="form-control" id="op_preg14" name="op_preg14">

					<label for="op_preg15">
						15.	PRESENTER EN ANNEXE UN CROQUIS GENERAL DE VOTRE OPP EN INDIQUANT LES ZONES OCCUPEES PAR VOS MEMBRES.
					</label>
					<input type="file" class="form-control" id="op_preg15" name="op_preg15">
				</div>
			</div>

			<!------ FIN INFORMACION DATOS DE OPERACIÓN ------>

			<div class="col-md-12 text-center alert alert-success" style="padding:7px;">INFORMATIONS SUR LES PRODUITS POUR LESQUELS VOUS DEMANDEZ A UTILISER LE SYMBOLE<sup>6</sup></div>
			<div class="col-lg-12">
				<table class="table table-bordered" id="tablaProductos">
					<tr>
						<td><b>Produit général</b> (ej: cafe, cacao, miel, etc...)</td>
						<td><b>Produit spécifique</b> (ej: Café vert, poudre de cacao, miel)</td>
						<td>Volume Total Estimé à Commercialiser</td>
						<td>Produit Finit</td>
						<td>Matière Première</td>
						<td>Pays de Destination</td>
						<td>Marque Propre</td>
						<td>Marque d’un Client</td>
						<td>Pas encore de client </td>
						<td>
							<button type="button" onclick="tablaProductos()" class="btn btn-primary" aria-label="Left Align">
							  <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
							</button>
							
						</td>					
					</tr>
					<tr>
						<td>
							<input type="text" class="form-control" name="producto_general[0]" id="exampleInputEmail1" placeholder="Produit général">
						</td>
						<td>
							<input type="text" class="form-control" name="producto[0]" id="exampleInputEmail1" placeholder="Producto Específico">
						</td>
						<td>
							<input type="text" class="form-control" name="volumen[0]" id="exampleInputEmail1" placeholder="Volumen">
						</td>
						<td>
							Oui <input type="radio"  name="terminado[0]" id="" value="SI"><br>
							Non <input type="radio"  name="terminado[0]" id="" value="NO" >
						</td>
						<td>
							<input type="text" class="form-control" name="materia[0]" id="exampleInputEmail1" placeholder="Materia">
						</td>
						<td>
							<input type="text" class="form-control" name="destino[0]" id="exampleInputEmail1" placeholder="Destino">
						</td>
						<td>
							Oui <input type="radio"  name="marca_propia[0]" id="" value="SI"><br>
							Non <input type="radio"  name="marca_propia[0]" id="" value="NO" >
						</td>
						<td>
							Oui <input type="radio"  name="marca_cliente[0]" id="" value="SI"><br>
							Non <input type="radio"  name="marca_cliente[0]" id="" value="NO">
						</td>
						<td>
							Oui <input type="radio"  name="sin_cliente[0]" id="" value="SI"><br>
							Non <input type="radio"  name="sin_cliente[0]" id="" value="NO">
						</td>
					</tr>				
					<tr>
						<td colspan="9">
							<h6><sup>6</sup> L’information fournie dans cette section sera traitée en toute confidentialité. Veuillez insérer des colonnes supplémentaires si nécessaire.</h6>
						</td>
					</tr>
				</table>
			</div>

			<div class="col-lg-12 text-center alert alert-success" style="padding:7px;">
				<b>ENGAGEMENTS</b>
			</div>
			<div class="col-lg-12 text-justify">
				<p>1. Par l’envoi de cette demande, vous manifestez le souhait de recevoir une proposition d’enregistrement.</p>
				<p>2. Le processus d’enregistrement débutera dès réception du paiement.</p>
				<p>3. L’envoi et la réception de cette demande ne garantissent pas l’acceptation de l’enregistrement.</p>
				<p>4. Connaître et respecter toutes les exigences de la Norme Générale du Symbole des Petits Producteurs qui vous    sont appliquées en qualité d’Organisations de Petits Producteurs, tant critiques que minima, indépendamment du type d’évaluation réalisée.</p>
			</div>
			<div class="col-lg-12">
				<p style="font-size:14px;">
					<strong>Nom de la personne responsable de la véracité des informations fournies:</strong>
				</p>
				<input type="text" class="form-control" id="responsable" name="responsable" placeholder="Nom de la personne responsable" required>	

				<!--<label for="nombre_oc">
					OC que recibe la solicitud:
				</label>
				<input type="text" class="form-control" id="nombre_oc" name="nombre_oc">-->
			</div>
			<div class="col-xs-12">
				<hr>
				<input type="hidden" name="insertar_solicitud" value="1">
				<input type="submit" class="btn btn-primary form-control" value="Envoyer la demande" onclick="return validar()">
			</div>

		</fieldset>
	</form>
</div>

<script>
	
  function validar(){

    tipo_solicitud = document.getElementsByName("tipo_solicitud");
    tuvo_ventas = document.getElementsByName("op_preg12");
    opcion_venta = document.getElementsByName("op_preg13");
     
    // INICIA SELECCION TIPO SOLICITUD
    var seleccionado = false;
    for(var i=0; i<tipo_solicitud.length; i++) {    
      if(tipo_solicitud[i].checked) {
        seleccionado = true;
        break;
      }
    }
     
    if(!seleccionado) {
      alert("Vous devez sélectionner un type de demande");
      return false;
    }
    //// TERMINA SELECCION TIPO SOLICITUD

    /// INICIA OPCION DE VENTAS
    var ventas = false;
    var valor_venta = '';
    for(var i=0; i<tuvo_ventas.length; i++) {    
      if(tuvo_ventas[i].checked) {
      	valor_venta = tuvo_ventas[i].value;
        ventas = true;
        break;
      }
    }
     
    if(!ventas) {
      alert("Vous devez sélectionner \"OUI\"  s'il y a eu des ventes ou \"NON\" dans le cas contraire.");
      return false;
    }
    /// TERMINA OPCION DE VENTAS


    if(valor_venta != 'NO'){
	    var monto = false;
	    for(var i=0; i<opcion_venta.length; i++) {    
	      if(opcion_venta[i].checked) {
	        monto = true;
	        break;
	      }
	    }
	     
	    if(!monto) {
	      alert("Vous avez sélectionné \"OUI\" il y a eu des ventes, vous devez sélectionner le montant des ventes SPP");
	      return false;
	    }

    }

    return true
  }

</script>

<script>
var contador=0;
	function tablaCertificaciones()
	{
		contador++;
	var table = document.getElementById("tablaCertificaciones");
	  {
	  var row = table.insertRow(2);
	  var cell1 = row.insertCell(0);
	  var cell2 = row.insertCell(1);
	  var cell3 = row.insertCell(2);
	  var cell4 = row.insertCell(3);

	  cell1.innerHTML = '<input type="text" class="form-control" name="certificacion['+contador+']" id="exampleInputEmail1" placeholder="CERTIFICACIÓN">';
	  cell2.innerHTML = '<input type="text" class="form-control" name="certificadora['+contador+']" id="exampleInputEmail1" placeholder="CERTIFICADORA">';
	  cell3.innerHTML = '<input type="text" class="form-control" name="ano_inicial['+contador+']" id="exampleInputEmail1" placeholder="AÑO INICIAL">';
	  cell4.innerHTML = '<div class="col-xs-6">SI<input type="radio" class="form-control" name="interrumpida['+contador+']" value="SI"></div><div class="col-xs-6">NO<input type="radio" class="form-control" name="interrumpida['+contador+']" value="NO"></div>';
	  }
	}	

	function mostrar(){
		document.getElementById('oculto').style.display = 'block';
	}
	function ocultar()
	{
		document.getElementById('oculto').style.display = 'none';
	}

	function mostrar_ventas(){
		document.getElementById('tablaVentas').style.display = 'block';
	}
	function ocultar_ventas()
	{
		document.getElementById('tablaVentas').style.display = 'none';
	}		

	var cont=0;
	function tablaProductos()
	{

	var table = document.getElementById("tablaProductos");
	  {
	cont++;

	  var row = table.insertRow(1);
	  var cell1 = row.insertCell(0);
	  var cell2 = row.insertCell(1);
	  var cell3 = row.insertCell(2);
	  var cell4 = row.insertCell(3);
	  var cell5 = row.insertCell(4);
	  var cell6 = row.insertCell(5);
	  var cell7 = row.insertCell(6); 
	  var cell8 = row.insertCell(7);
	  var cell9 = row.insertCell(8); 	   	  
	  
	  cell1.innerHTML = '<input type="text" class="form-control" name="producto_general['+cont+']" id="exampleInputEmail1" placeholder="Produit général">';

	  cell2.innerHTML = '<input type="text" class="form-control" name="producto['+cont+']" id="exampleInputEmail1" placeholder="Producto Específico">';
	  
	  cell3.innerHTML = '<input type="text" class="form-control" name="volumen['+cont+']" id="exampleInputEmail1" placeholder="Volumen">';
	  
	  cell4.innerHTML = 'SI <input type="radio" name="terminado'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="terminado'+cont+'['+cont+']" id="" value="NO">';
	  
	  cell5.innerHTML = '<input type="text" class="form-control" name="materia['+cont+']" id="exampleInputEmail1" placeholder="Materia">';
	  
	  cell6.innerHTML = '<input type="text" class="form-control" name="destino['+cont+']" id="exampleInputEmail1" placeholder="Destino">';
	  
	  cell7.innerHTML = 'SI <input type="radio" name="marca_propia'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="marca_propia'+cont+'['+cont+']" id="" value="NO">';
	  
	  cell8.innerHTML = 'SI <input type="radio" name="marca_cliente'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="marca_cliente'+cont+'['+cont+']" id="" value="NO">';
	  
	  cell9.innerHTML = 'SI <input type="radio" name="sin_cliente'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="sin_cliente'+cont+'['+cont+']" id="" value="NO">';	  

	  }

	}	

</script>