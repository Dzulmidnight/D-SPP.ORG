<?php 
require_once('../Connections/dspp.php'); 
require_once('../Connections/mail.php');

mysql_select_db($database_dspp, $dspp);

if (!isset($_SESSION)) {
  session_start();
	
	$redireccion = "../index.php?EMPRESA";

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
$idempresa = $_SESSION['idempresa'];
$ruta_croquis = "../../archivos/empresaArchivos/croquis/";
$spp_global = "cert@spp.coop";
$auxiliar = "acc@spp.coop";
$administrador = "yasser.midnight@gmail.com";
/************ VARIABLES DE CONTROL ******************/



if(isset($_POST['insertar_solicitud']) && $_POST['insertar_solicitud'] == 1){
	$estatus_publico = 1; // EN REVISIÓN
	$estatus_interno = NULL;
	$estatus_dspp = 1; // SOLICITUD EN REVISIÓN
	$alcance_opp = "";


	/* INICIA CAPTURA ALCANCE DE LA EMPRESA */
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
	if(isset($_POST['importacion'])){
		$importacion = $_POST['importacion'];
	}else{
		$importacion = '';
	}

    ///CAPTURAMOS EL TIPO DE EMPRESA
    if(isset($_POST['comprador'])){
    	$comprador = $_POST['comprador'];
    }else{
    	$comprador = '';
    }
    if(isset($_POST['intermediario'])){
    	$intermediario = $_POST['intermediario'];
    }else{
    	$intermediario = '';
    }
    if(isset($_POST['maquilador'])){
    	$maquilador = $_POST['maquilador'];
    }else{
    	$maquilador = '';
    }

	/* TERMINA CAPTURA ALCANCE DEL OPP */


	if(isset($_POST['preg6'])){
		$preg6 = $_POST['preg6'];
	}else{
		$preg6 = "";
	}
	if(isset($_POST['preg13'])){
		$preg13 = $_POST['preg13'];
	}else{
		$preg13 = "";
	}
	if(isset($_POST['preg14'])){
		if($_POST['preg14'] == 'mayor'){
			$preg14 = $_POST['preg14_1'];
		}else{
			$preg14 = $_POST['preg14'];
		}
	}else{
		$preg14 = "";
	}




	if(!empty($_FILES['preg9']['name'])){
	    $_FILES["preg9"]["name"];
	      move_uploaded_file($_FILES["preg9"]["tmp_name"], $ruta_croquis.date("Ymd H:i:s")."_".$_FILES["preg9"]["name"]);
	      $croquis = $ruta_croquis.basename(date("Ymd H:i:s")."_".$_FILES["preg9"]["name"]);
	}else{
		$croquis = NULL;
	}

	// INGRESAMOS LA INFORMACION A LA SOLICITUD DE CERTIFICACION

	$insertSQL = sprintf("INSERT INTO solicitud_registro (tipo_solicitud, idempresa, idoc, comprador_final, intermediario, maquilador, contacto1_nombre, contacto2_nombre, contacto1_cargo, contacto2_cargo, contacto1_email, contacto2_email, contacto1_telefono, contacto2_telefono, adm1_nombre, adm2_nombre, adm1_email, adm2_email, adm1_telefono, adm2_telefono, preg1, preg2, preg3, preg4, produccion, procesamiento, importacion, preg6, preg7, preg8, preg9, preg10, preg12, preg13, preg14, preg15, responsable, fecha_registro, estatus_interno ) VALUES (%s, %s, %s, %s, %s, %s, %s,%s, %s, %s, %s, %s, %s, %s, %s, %s,%s, %s, %s, %s, %s, %s, %s, %s, %s,%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
		   GetSQLValueString($_POST['tipo_solicitud'], "text"),
		   GetSQLValueString($idempresa, "int"),
           GetSQLValueString($_POST['idoc'], "int"),
           GetSQLValueString($comprador, "int"),
           GetSQLValueString($intermediario, "int"),
           GetSQLValueString($maquilador, "int"),
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
           GetSQLValueString($_POST['preg1'], "text"),
           GetSQLValueString($_POST['preg2'], "text"),
           GetSQLValueString($_POST['preg3'], "text"),
           GetSQLValueString($_POST['preg4'], "text"),
           GetSQLValueString($produccion, "int"),
           GetSQLValueString($procesamiento, "int"),
           GetSQLValueString($importacion, "int"),
           GetSQLValueString($preg6, "text"),
           GetSQLValueString($_POST['preg7'], "text"),
           GetSQLValueString($_POST['preg8'], "text"),
           GetSQLValueString($croquis, "text"),
           GetSQLValueString($_POST['preg10'], "text"),
           GetSQLValueString($_POST['preg12'], "text"),
           GetSQLValueString($preg13, "text"),
           GetSQLValueString($preg14, "text"),
           GetSQLValueString($_POST['preg15'], "text"),
           GetSQLValueString($_POST['responsable'], "text"),
           GetSQLValueString($fecha, "int"),
           GetSQLValueString($estatus_dspp, "int"));


		  $Result1 = mysql_query($insertSQL, $dspp) or die(mysql_error());
		 
		 $idsolicitud_registro = mysql_insert_id($dspp); 

	///INGRESAMOS EL TIPO DE SOLICITUD A LA TABLA OPP y EL ALCANCE DE LA OPP
	$updateSQL = sprintf("UPDATE empresa SET comprador = %s, intermediario = %s, maquilador = %s, estatus_empresa = %s WHERE idempresa = %s",
		GetSQLValueString($comprador, "int"),
		GetSQLValueString($intermediario, "int"),
		GetSQLValueString($maquilador, "int"),
		GetSQLValueString($_POST['tipo_solicitud'], "int"),
		GetSQLValueString($idempresa, "int"));
	$actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());

	// INGRESAMOS LOS CONTACTOS DE LA SOLICITUD A LA TABLA DE CONTACTOS
	if(!empty($_POST['contacto1_nombre'])){
		$insertSQL = sprintf("INSERT INTO contactos(idempresa, nombre, cargo, telefono1, email1, idsolicitud_registro) VALUES (%s, %s, %s, %s, %s, %s)",
			GetSQLValueString($idempresa, "int"),
			GetSQLValueString($_POST['contacto1_nombre'], "text"),
			GetSQLValueString($_POST['contacto1_cargo'], "text"),
			GetSQLValueString($_POST['contacto1_telefono'], "text"),
			GetSQLValueString($_POST['contacto1_email'], "text"),
			GetSQLValueString($idsolicitud_registro, "int"));
		$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

	}
	if(!empty($_POST['contacto2_nombre'])){
		$insertSQL = sprintf("INSERT INTO contactos(idempresa, nombre, cargo, telefono1, email1, idsolicitud_registro) VALUES (%s, %s, %s, %s, %s, %s)",
			GetSQLValueString($idempresa, "int"),
			GetSQLValueString($_POST['contacto2_nombre'], "text"),
			GetSQLValueString($_POST['contacto2_cargo'], "text"),
			GetSQLValueString($_POST['contacto2_telefono'], "text"),
			GetSQLValueString($_POST['contacto2_email'], "text"),
			GetSQLValueString($idsolicitud_registro, "int"));
		$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

	}
	if(!empty($_POST['adm1_nombre'])){
		$insertSQL = sprintf("INSERT INTO contactos(idempresa, nombre, cargo, telefono1, email1, idsolicitud_registro) VALUES (%s, %s, %s, %s, %s, %s)",
			GetSQLValueString($idempresa, "int"),
			GetSQLValueString($_POST['adm1_nombre'], "text"),
			GetSQLValueString('ADMINISTRATIVO', "text"),
			GetSQLValueString($_POST['adm1_telefono'], "text"),
			GetSQLValueString($_POST['adm1_email'], "text"),
			GetSQLValueString($idsolicitud_registro, "int"));
		$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

	}
	if(!empty($_POST['adm2_nombre'])){
		$insertSQL = sprintf("INSERT INTO contactos(idempresa, nombre, cargo, telefono1, email1, idsolicitud_registro) VALUES (%s, %s, %s, %s, %s, %s)",
			GetSQLValueString($idempresa, "int"),
			GetSQLValueString($_POST['adm2_nombre'], "text"),
			GetSQLValueString('ADMINISTRATIVO', "text"),
			GetSQLValueString($_POST['contacto2_telefono'], "text"),
			GetSQLValueString($_POST['contacto2_email'], "text"),
			GetSQLValueString($idsolicitud_registro, "int"));
		$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

	}


		 // INGRESAMOS EL PORCENTAJE DE VENTA DE LOS PRODUCTOS
	 	if(!empty($_POST['organico']) || !empty($_POST['comercio_justo']) || !empty($_POST['spp']) || !empty($_POST['sin_certificado'])){
	 		$insertSQL = sprintf("INSERT INTO porcentaje_productoVentas (organico, comercio_justo, spp, sin_certificado, idsolicitud_registro, idempresa) VALUES (%s, %s, %s, %s, %s, %s)",
	 			GetSQLValueString($_POST['organico'], "text"),
	 			GetSQLValueString($_POST['comercio_justo'], "text"),
	 			GetSQLValueString($_POST['spp'], "text"),
	 			GetSQLValueString($_POST['sin_certificado'], "text"),
	 			GetSQLValueString($idsolicitud_registro, "int"),
	 			GetSQLValueString($idempresa, "int"));
	 		$insertar = mysql_query($insertSQL,$dspp) or die(mysql_error());
	 	}
	


		/*************************** INICIA INSERTAR PROCESO DE CERTIFICACIÓN ***************************/
		$insertSQL = sprintf("INSERT INTO proceso_certificacion (idsolicitud_registro, estatus_publico, estatus_interno, estatus_dspp, fecha_registro) VALUES (%s, %s, %s, %s, %s)",
			GetSQLValueString($idsolicitud_registro, "int"),
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
					$insertSQL = sprintf("INSERT INTO certificaciones (idsolicitud_registro, certificacion, certificadora, ano_inicial, interrumpida) VALUES (%s, %s, %s, %s, %s)",
					    GetSQLValueString($idsolicitud_registro, "int"),
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
		$producto = $_POST['producto'];
		$volumen_estimado = $_POST['volumen_estimado'];
		$volumen_terminado = $_POST['volumen_terminado'];
		$volumen_materia = $_POST['volumen_materia'];
		$destino = $_POST['destino'];
		$origen = $_POST['origen'];

		/*$marca_propia = $_POST['marca_propia'];
		$marca_cliente = $_POST['marca_cliente'];
		$sin_cliente = $_POST['sin_cliente'];*/

		for ($i=0;$i<count($producto);$i++) { 
			if($producto[$i] != NULL){


					//$terminado = $_POST[$array1[$i]];
					//$marca_propia = $_POST[$array2[$i]];
					//$marca_cliente = $_POST[$array3[$i]];
					//$sin_cliente = $_POST[$array4[$i]];

					$str = iconv($charset, 'ASCII//TRANSLIT', $producto[$i]);
					$producto[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));

					$str = iconv($charset, 'ASCII//TRANSLIT', $destino[$i]);
					$destino[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));

					$str = iconv($charset, 'ASCII//TRANSLIT', $origen[$i]);
					$origen[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));


				    $insertSQL = sprintf("INSERT INTO productos (idempresa, idsolicitud_registro, producto, volumen_estimado, volumen_terminado, volumen_materia, origen, destino) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
				    	GetSQLValueString($idempresa, "int"),
				          GetSQLValueString($idsolicitud_registro, "int"),
				          GetSQLValueString($producto[$i], "text"),
				          GetSQLValueString($volumen_estimado[$i], "text"),
				          GetSQLValueString($volumen_terminado[$i], "text"),
				          GetSQLValueString($volumen_materia[$i], "text"),
				          GetSQLValueString($origen[$i], "text"),
				          GetSQLValueString($destino[$i], "text"));

				  $Result = mysql_query($insertSQL, $dspp) or die(mysql_error());
			}
		}
		/***************************** TERMINA INSERTAR PRODUCTOS ******************************/

		/************************** INICIA INSERTAR SUB EMPRESAS **************************/
		$subempresa = $_POST['subempresa'];
		$servicio = $_POST['servicio'];


		for ($i=0;$i<count($subempresa);$i++) { 
			if($subempresa[$i] != NULL){
			    $insertSQL = sprintf("INSERT INTO sub_empresas (idsolicitud_registro, nombre, servicio, idempresa) VALUES (%s, %s, %s, %s)",
			          GetSQLValueString($idsolicitud_registro, "int"),
			          GetSQLValueString($subempresa[$i], "text"),
			          GetSQLValueString($servicio[$i], "text"),
			          GetSQLValueString($idempresa, "int"));
			  	$Result = mysql_query($insertSQL, $dspp) or die(mysql_error());
			}
		}

		/************************** TERMINA INSERTAR SUB EMPRESAS **************************/

		///// INICIA ENVIO DEL MENSAJE POR CORREO AL OC y a SPP GLOBAL
		$asunto = "D-SPP | Demande d'enregistrement pour les Acheteurs et autres acteurs";
		$row_oc = mysql_query("SELECT * FROM oc WHERE idoc = $_POST[idoc]", $dspp) or die(mysql_error());
		$oc = mysql_fetch_assoc($row_oc);

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
		              <th scope="col" align="left" width="280"><strong>Demande d\'enregistrement pour les Acheteurs et autres acteurs / Application for Buyers’, Registration </strong></th>
		            </tr>
		            <tr>
		              <td style="padding-top:10px;">
		   				Pour pouvoir consulter la demande, merci d\'ouvrir une session avec votre compte d\'Organisme de certification à l\'adresse suivante : <a href="http://d-spp.org" target="_new">www.d-spp.org</a>
		              <br>
		              To consult the application, please log in to your CE(Certification Entity) account, in the following link: <a href="http://d-spp.org" target="_new">www.d-spp.org</a>

		         

		              </td>
		            </tr>
				    <tr>
				      <td align="left">Téléphone / Company phone: '.$_POST['telefono'].'</td>
				    </tr>

				    <tr>
				      <td align="left">'.$_POST['pais'].'</td>
				    </tr>
				    <tr>
				      <td align="left" style="color:#ff738a;">Courriel: '.$_POST['email'].'</td>
				    </tr>
				    <tr>
				      <td align="left" style="color:#ff738a;">Courriel: '.$_POST['contacto1_email'].'</td>
				    </tr>

				    <tr>
				      <td colspan="2">
				        <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;" border="1" width="650px">
				          <tbody>
				            <tr style="font-size: 12px; text-align:center; background-color:#dff0d8; color:#3c763d;" height="50px;">
				              <td width="130px">Nom de l\'entreprise / Company name</td>
				              <td width="130px">Pays / Country</td>
				              <td width="130px">Organisme de certification / Certification Entity</td>
				           
				              <td width="130px">Date de la demande / Date of application</td>
				            </tr>
				            <tr style="font-size: 12px;">
				              <td style="padding:10px;">
				              	'.$_POST['nombre'].'
				              </td>
				              <td style="padding:10px;">
				                '.$_POST['pais'].'
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
		    //$mail->AddAddress($detalle_opp['email_opp']);
		    $token = strtok($oc['email1'], "\/\,\;");
		    while ($token !== false)
		    {
		      $mail->AddAddress($token);
		      $token = strtok('\/\,\;');
		    }

		  }
		  if(!empty($oc['email2'])){
		    //$mail->AddAddress($detalle_opp['email_opp']);
		    $token = strtok($oc['email2'], "\/\,\;");
		    while ($token !== false)
		    {
		      $mail->AddAddress($token);
		      $token = strtok('\/\,\;');
		    }

		  }

	    $mail->AddBCC($spp_global);
	    $mail->AddBCC($auxiliar);
        //$mail->Username = "soporte@d-spp.org";
        //$mail->Password = "/aung5l6tZ";
        $mail->Subject = utf8_decode($asunto);
        $mail->Body = utf8_decode($cuerpo_correo);
        $mail->MsgHTML(utf8_decode($cuerpo_correo));
        $mail->Send();
        $mail->ClearAddresses();

 		$mensaje = "La demande d'inscription a été envoyée au CO, vous serez bientôt contacté";


}

  //$insertGoTo = "main_menu.php?SOLICITUD&add&mensaje=Solicitud agregada correctamente, se ha notificado al OC por email.";
$query = "SELECT * FROM empresa WHERE idempresa = $idempresa";
$row_empresa = mysql_query($query,$dspp) or die(mysql_error());
$empresa = mysql_fetch_assoc($row_empresa);

?>
<div class="row">
	<?php 
	if(isset($mensaje)){
	?>
	<div class="col-lg-12 alert alert-success alert-dismissible" role="alert">
	  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	  <?php echo $mensaje; ?>
	</div>
	<?php
	}
	?>
</div>

<div class="row" style="font-size:12px;">
	<form action="" name="" method="POST" enctype="multipart/form-data">
		<fieldset>
			<div class="col-md-12 alert alert-primary" style="padding:7px;">
				<h3 class="text-center">Demande d'enregistrement pour les Acheteurs et autres acteurs</h3>
			</div>

			<div class="col-md-12 text-center alert alert-success" style="padding:7px;"><b>DONNÉES GÉNÉRALES</b></div>

			<div class="col-lg-12 alert alert-info" style="padding:7px;">

				<div class="col-md-12 alert alert-warning" style="padding:5px;">
						<ul>
							<li>
								<b>
									Si vous avez eu précédemment une certification SPP, avec le même organisme de certification ou avec un autre, vous devez choisir <span style="color:red">"Renouvellement du certificat"</span>
								</b>
							</li>
							<li>
								<b>
									S'il s'agit de la première fois que vous demandez la certification, vous devez choisir <span style="color:red">"Première fois"</span>.
								</b>
							</li>
						</ul>
					 
				</div>

				<div class="col-md-6">
					<div class="col-md-12">
						<b>Envoi à l'OC (choisissez l'OC auquel vous souhaitez envoyer la demande) :</b>
					</div>
					<div class="col-md-12">
						<select class="form-control" name="idoc" id="" required>
							<option value="">Sélectionnez un OC</option>
							<?php 
							$query = "SELECT idoc, abreviacion FROM oc";
							$row_oc = mysql_query($query,$dspp) or die(mysql_error());

							while($oc = mysql_fetch_assoc($row_oc)){
							?>
							<option value="<?php echo $oc['idoc']; ?>" <?php if($empresa['idoc'] == $oc['idoc']){ echo "selected"; } ?>><?php echo $oc['abreviacion']; ?></option>
							<?php
							}
							 ?>
							 <option value="TODOS">Envoyer à tous les organismes de certification</option>
						</select>
					</div>
				</div>
				<div class="col-md-6">
					<div class="col-md-12">
						<p class="text-center"><strong>Sélectionnez le type de demande</strong></p>
					</div>
					<div class="col-md-6">
						<label for="nueva">Première fois</label>
						<input type="radio" class="form-control" id="nueva" name="tipo_solicitud" value="NUEVA">
					</div>
					<div class="col-md-6">
						<label for="renovacion">Renouvellement de l'enregistrement</label>
						<input type="radio" class="form-control" id="renovacion" name="tipo_solicitud" value="RENOVACION">
					</div>
				</div>
			</div>


			<!------ INICIA INFORMACION GENERAL Y DATOS FISCALES ------>
			<div class="row">
				<div class="col-md-6">
					<div class="col-md-12 text-center alert alert-warning" style="padding:7px;">INFORMATIONS GENERALES</div>
					<label for="fecha_elaboracion">DATE DE REALISATION</label>
					<input type="text" class="form-control" id="fecha_elaboracion" name="fecha_elaboracion" value="<?php echo date('Y-m-d', time()); ?>" readonly>	

					<label for="spp">CODE D’IDENTIFICATION SPP(#SPP): </label>
					<input type="text" class="form-control" id="spp" name="spp" value="<?php echo $empresa['spp']; ?>">

					<label for="nombre">DENOMINATION SOCIALE DE L’ENTREPRISE: </label>
					<textarea name="nombre" id="nombre" class="form-control"><?php echo $empresa['nombre']; ?></textarea>


					<label for="pais">PAYS:</label>
					<?php 
					$row_pais = mysql_query("SELECT * FROM paises",$dspp) or die(mysql_error());
					 ?>
					 <select name="pais" id="pais" class="form-control">
					 	<option value="">Sélectionnez un pays</option>
					 	<?php 
					 	while($pais = mysql_fetch_assoc($row_pais)){
					 		if(utf8_encode($pais['nombre']) == $empresa['pais']){
					 			echo "<option value='".utf8_encode($pais['nombre'])."' selected>".utf8_encode($pais['nombre'])."</option>";
					 		}else{
					 			echo "<option value='".utf8_encode($pais['nombre'])."'>".utf8_encode($pais['nombre'])."</option>";
					 		}
					 	}
					 	 ?>
					 </select>

					<label for="direccion_oficina">ADRESSE COMPLETE DU SIEGE SOCIAL (RUE, COMPLEMENT D’ADRESSE, CODE POSTAL, REGION) :</label>
					<textarea name="direccion_oficina" id="direccion_oficina"  class="form-control"><?php echo $empresa['direccion_oficina']; ?></textarea>

					<label for="email">ADRESSE MAIL:</label>
					<input type="text" class="form-control" id="email" name="email" value="<?php echo $empresa['email']; ?>">

					<label for="email">TELEPHONE (INDICATIF PAYS+NUMERO):</label>
					<input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo $empresa['telefono']; ?>">	

					<label for="sitio_web">SITE WEB:</label>
					<input type="text" class="form-control" id="sitio_web" name="sitio_web" value="<?php echo $empresa['sitio_web']; ?>">

				</div>

				<div class="col-md-6">
					<div class="col-md-12 text-center alert alert-warning" style="padding:7px;">INFORMATIONS FISCALES POUR LA FACTURATION</div>

					<label for="razon_social">REGISTRE DU COMMERCE</label>
					<input type="text" class="form-control" id="razon_social" name="razon_social" value="<?php echo $empresa['razon_social']; ?>">

					<label for="direccion_fiscal">DOMICILIATION</label>
					<textarea class="form-control" name="direccion_fiscal" id="direccion_fiscal"><?php echo $empresa['direccion_fiscal']; ?></textarea>

					<label for="rfc">RFC</label>
					<input type="text" class="form-control" id="rfc" name="rfc" value="<?php echo $empresa['rfc']; ?>">

					<label for="ruc">RUC</label>
					<input type="text" class="form-control" id="ruc" name="ruc" value="<?php echo $empresa['ruc']; ?>">
				</div>
			</div>
			<!------ INICIA INFORMACION GENERAL Y DATOS FISCALES ------>


			<!------ INICIA INFORMACION CONTACTOS Y AREA ADMINISTRATIVA ------>
			<div class="row">
				<div class="col-md-6">
					<div class="col-md-12 text-center alert alert-warning" style="padding:7px;">PERSONNE(S) A CONTACTER :</div>

					<label for="persona1">PERSONNE(S) A CONTACTER :</label>
					<input type="text" class="form-control" id="persona1" name="contacto1_nombre" placeholder="* Nombre Persona 1" required>
					<input type="text" class="form-control" id="" name="contacto2_nombre" placeholder="Nombre Persona 2">

					<label for="cargo">FONCTION(S)</label>
					<input type="text" class="form-control" id="cargo" name="contacto1_cargo" placeholder="* Cargo Persona 1" required>
					<input type="text" class="form-control" id="" name="contacto2_cargo" placeholder="Cargo Persona 2">

					<label for="email">ADRESSE(S) MAIL:</label>
					<input type="email" class="form-control" id="email" name="contacto1_email" placeholder="* Email Persona 1" required>
					<input type="email" class="form-control" id="" name="contacto2_email" placeholder="Email Persona 2">

					<label for="telefono">TELEPHONE(S):</label>
					<input type="text" class="form-control" id="telefono" name="contacto1_telefono" placeholder="* Telefono Persona 1" required>
					<input type="text" class="form-control" id="" name="contacto2_telefono" placeholder="Telefono Persona 2">

				</div>

				<div class="col-md-6">
					<div class="col-md-12 text-center alert alert-warning" style="padding:7px;">RESPONSABLE DU SERVICE ADMINISTRATIF</div>

					<label for="persona_adm">RESPONSABLE DU SERVICE ADMINISTRATIF:</label>
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
			<div class="col-md-12 alert alert-info">
				<div>
					<label for="alcance_opp">
						Sélectionnez le type d'entreprise SPP pour lequel l'enregistrement est demandé. Un intermédiaire ne peut être entegistré SPP s'il n'a pas un acheteur final enregistré SPP ou en cours d'enregistrement.
					</label>
				</div>

                  <div class="checkbox">
                    <label class="col-sm-4">
                      <input type="checkbox" name="comprador" <?php if($empresa['comprador']){echo "checked"; } ?> value="1"> ACHETEUR FINAL
                    </label>
                    <label class="col-sm-4">
                      <input type="checkbox" name="intermediario" <?php if($empresa['intermediario']){echo "checked"; } ?> value="1"> INTERMEDIAIRE
                    </label>
                    <label class="col-sm-4">
                      <input type="checkbox" name="maquilador" <?php if($empresa['maquilador']){echo "checked"; } ?> value="1"> SOUS-TRAITANT:
                    </label>
                  </div>
			</div>


			<div class="col-md-12 text-center alert alert-success" style="padding:7px;">INFORMATIONS SUR TYPE D’ ACTIVITE</div>

			<div class="row">
				<div class="col-md-12">
					<label for="preg1">
						1.	QUELLES SONT LES ORGANISATIONS DE PETITS PRODUCTEURS AUXQUELLES VOUS ACHETEZ OU COMPTEZ ACHETER SOUS LE SYMBOLE DES PETITS PRODUCTEURS ?
					</label>
					<textarea name="preg1" id="preg1" class="form-control"></textarea>


					<label for="preg2">
						2.	NOMS DES PROPRIÉTAIRES DE L'ENTREPRISE?
					</label>
					<textarea name="preg2" id="preg2" class="form-control"></textarea>

					<label for="preg3">
						3. INDIQUEZ QUEL(S) PRODUIT(S) VOUS SOUHAITEZ INCLURE DANS LA CERTIFICATION DU SYMBOLE DES PETITS PRODUCTEURS POUR LE(S)QUEL(S) L’ORGANISME DE CERTIFICATION REALISERA L’EVALUATION.
					</label>
					<input type="text" class="form-control" id="preg3" name="preg3">

					<label for="preg4">
						4. SI VOTRE ENTREPRISE EST UN ACHETEUR FINAL, INDIQUEZ SI VOUS SOUHAITEZ INCLURE UNE QUALIFICATION OPTIONNELLE POUR UNE UTILISATION COMPLEMENTAIRE AVEC LE LOGO GRAPHIQUE DU SYMBOLE DES PETITS PRODUCTEURS.<sup>4</sup>
					</label>
					<textarea name="preg4" id="preg4" class="form-control"></textarea>

					<div >
						<label for="alcance_opp">
							5. MARQUEZD’UNECROIXL’ACTIVITEDEL’ENTREPRISE
						</label>
					</div>
					<div class="col-md-4">
						<label>PRODUCTION</label>
						<input type="checkbox" name="produccion" class="form-control" value="1">
					</div>
					<div class="col-md-4">
						<label>TRAITEMENT</label>
						<input type="checkbox" name="procesamiento" class="form-control" value="1">
					</div>
					<div class="col-md-4">
						<label>IMPORTATION</label>
						<input type="checkbox" name="importacion" class="form-control" value="1">
					</div>


				<p><b>6. INDIQUEZ SI VOUS UTILISEZ LES SERVICES DE SOUS-TRAITANCE D’USINES DE TRANSFORMATION POUR LES TRANSACTIONS SPP, CEUX D’ENTREPRISES DE COMMERCIALISATION OU D’ENTREPRISES D’IMPORT/EXPORT ;LE CAS ECHEANT, MENTIONNEZ LE TYPE DE SERVICE REALISE.</b></p>
				<div class="col-md-6">
					Oui <input type="radio" class="form-control" name="preg6" onclick="mostrar_empresas()" id="preg6" value="SI">
				</div>
				<div class="col-md-6">
					Non <input type="radio" class="form-control" name="preg6" onclick="ocultar_empresas()" id="preg6" value="NO">
				</div>

				<p>SI LA RÉPONSE EST AFFIRMATIVE, ELLE MENE LE NUMÉRO ET LE SERVICE QU'ELLE FAIT</p>
				<div id="contenedor_tablaEmpresas" class="col-md-12" style="display:none">
					<table class="table table-bordered" id="tablaEmpresas">
						<tr>
							<td>DENOMINATION SOCIALE DE L’ENTREPRISE</td>
							<td>SERVICE REALISE</td>
							<td>
								<button type="button" onclick="tablaEmpresas()" class="btn btn-primary" aria-label="Left Align">
								  <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
								</button>
								
							</td>
						</tr>
						<tr class="text-center">
							<td><input type="text" class="form-control" name="subempresa[0]" id="exampleInputEmail1" placeholder="EMPRESA"></td>
							<td><input type="text" class="form-control" name="servicio[0]" id="exampleInputEmail1" placeholder="SERVICIO"></td>
						</tr>
					</table>	
				</div>		


					<label for="preg7">
						7.	SI VOUS UTILISEZ LES SERVICES DE SOUS-TRAITANCE D’USINES DE TRANSFORMATION, D’ENTREPRISES DE COMMERCIALISATION OU D’ENTREPRISES D’IMPORT/EXPORT, INDIQUEZ SI CELLES-CI SONT ENREGISTREES, EN COURS D’ENREGISTREMENT SOUS LE PROGRAMME DU SPP OU SI ELLES SERONT CONTROLEES AU TRAVERS DE VOTRE ENTREPRISE <sup>5</sup>
						<br>
						<small><sup>5</sup> Voir le document “Directives Générales du Système SPP”.</small>
					</label>
					<textarea name="preg7" id="preg7" class="form-control"></textarea>

					<label for="preg8">
						8. EN PLUS DE VOTRE SIEGE SOCIAL, INDIQUEZ LE NOMBRE DE CENTRES DE COLLECTE, D’UNITES DE TRANSFORMATION OU DE BUREAUX SUPPLEMENTAIRES QUE VOUS POSSEDEZ.
					</label>
					<textarea name="preg8" id="preg8" class="form-control"></textarea>

					<label for="preg9">
						9. AU CAS OU VOUS POSSEDEZ DES CENTRES DE COLLECTE, D’UNITES DE TRANSFORMATION OU DES BUREAUX SUPPLEMENTAIRES, VEUILLEZ PRESENTER EN ANNEXE UN SCHEMA GENERAL INDIQUANT LEUR LOCALISATION.
					</label>
					<input type="file" id="preg9" name="preg9" class="form-control">

					<label for="preg10">
						10. SI VOUS DISPOSEZ D’UN SYSTEME DE CONTROLE INTERNE AFIN DE RESPECTER LES CRITERES DE LA NORME GENERALE DU SYMBOLE DES PETITS PRODUCTEURS, VEUILLEZ L’EXPLIQUER.
					</label>
					<textarea name="preg10" id="preg10" class="form-control"></textarea>

					<p class="alert alert-info"><b>11.  REMPLIR LE TABLEAU DE VOS CERTIFICATIONS, (EXEMPLE : EU, NOP, JASS, FLO, etc.).</b></p>

					<table class="table table-bordered" id="tablaCertificaciones">
						<tr>
							<td>CERTIFICATION</td>
							<td>CERTIFICATEUR</td>
							<td>ANNEE DE LA CERTIFICATION INITIALE</td>
							<td>A-T-ELLE ETE INTERROMPUE ?</td>	
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
								<div class="col-md-6">Oui<input type="radio" class="form-control" name="interrumpida[0]" value="SI"></div>
								<div class="col-md-6">Non<input type="radio" class="form-control" name="interrumpida[0]" value="NO"></div>
							</td>
						</tr>
					</table>	

					<label for="preg12">
						12. PARMI LES CERTIFICATIONS DONT VOUS DISPOSEZ ET LORS DE LEUR PLUS RECENTE EVALUATION INTERNE ET EXTERNE, COMBIEN DE NON CONFORMITES DE LA NORME GENERALE ONT ETE IDENTIFIEES ? CELLES-CI ONT-ELLES ETE RESOLUES ? QUEL EST LEUR ETAT ACTUEL ?</label>
					<textarea name="preg12" id="preg12" class="form-control"></textarea>

					<p for="op_preg11">
						<b>13. SUR L’ENSEMBLE DE VOS VENTES LORS DU CYCLE D’ENREGISTREMENT ANTERIEUR, QUEL A ETE LE POURCENTAGE REALISE SOUS LES CERTIFICATIONS BIOLOGIQUE, DU COMMERCE EQUITABLE ET / OU DU SYMBOLE DES PETITS PRODUCTEURS ?</b>
						<i>(* Entrez uniquement la quantité, le nombre entier ou les décimales)</i>
						<div class="row">
							<div class="col-lg-12">
								<div class="col-md-3">
									<label for="organico">% BIOLOGIQUE</label>
									<input type="number" step="any" class="form-control" id="organico" name="organico" placeholder="Ej: 0.0">
								</div>
								<div class="col-md-3">
									<label for="comercio_justo">% COMMERCE ÉQUITABLE</label>
									<input type="number" step="any" class="form-control" id="comercio_justo" name="comercio_justo" placeholder="Ej: 0.0">
								</div>
								<div class="col-md-3">
									<label for="spp">SYMBOLE DES PETITS PRODUCTEURS</label>
									<input type="number" step="any" class="form-control" id="spp" name="spp" placeholder="Ej: 0.0">
								</div>
								<div class="col-md-3">
									<label for="otro">SANS CERTIFICAT</label>
									<input type="number" step="any" class="form-control" id="otro" name="sin_certificado" placeholder="Ej: 0.0">
								</div>
							</div>
						</div>
					</p>					

					<p><b>14. A-T-ON OBSERVE DES ACHATS SOUS LE SPP DURANT LE CYCLE D’ENREGISTREMENT ANTERIEUR ?</b></p>
						<div class="col-md-6">
							Oui <input type="radio" class="form-control" name="preg13" id="preg13" value="SI">
						</div>
						<div class="col-md-6">
							Non <input type="radio" class="form-control" name="preg13" id="preg13" value="NO">
						</div>			
					<p>
						<b>15.  LE CAS ECHEANT, MERCI DE MARQUER D’UNE CROIX LE RANG DE LA VALEUR TOTALE DE VOS ACHATS SOUS LE SPP POUR LE CYCLE D’ENREGISTREMENT ANTERIEUR SELON LE TABLEAU SUIVANT
					</p>

					<div class="well col-md-12 " id="tablaVentas">
						<div class="col-md-6"><p>Jusqu’à 3 000 USD</p></div>
						<div class="col-md-6 "><input type="radio" name="preg14" class="form-control" id="ver" onclick="ocultar()" value="HASTA $3,000 USD"></div>
					
					
						<div class="col-md-6"><p>Entre 3 000 et 10 000 USD</p></div>
						<div class="col-md-6"><input type="radio" name="preg14" class="form-control" id="ver" onclick="ocultar()" value="ENTRE $3,000 Y $10,000 USD"></div>
					
					
						<div class="col-md-6"><p>De 10000 à 25000 USD</p></div>
						<div class="col-md-6"><input type="radio" name="preg14" class="form-control"  id="ver" onclick="ocultar()" value="ENTRE $10,000 A $25,000 USD"></div>
					
						<div class="col-md-6"><p>Plus de 25 000 USD* <sup>*</sup><br><h6><sup>*</sup>Indiquez le montant</h6></p></div>
						<div class="col-md-6"><input type="radio" name="preg14" class="form-control" id="exampleInputEmail1" onclick="mostrar()" value="mayor">
							<input type="text" name="preg14_1" class="form-control" id="oculto" style='display:none;' placeholder="Especifique la Cantidad">
						</div>

					</div>
							
					<label for="preg15">
						16. DATE ESTIMEE DE DEBUT D’UTILISATION DU SYMBOLE DES PETITS PRODUCTEURS :
					</label>
					<input type="text" class="form-control" id="preg15" name="preg15">
				</div>
			</div>

			<!------ FIN INFORMACION DATOS DE OPERACIÓN ------>

			<div class="col-md-12 text-center alert alert-success" style="padding:7px;">INFORMATIONS SUR LES PRODUITS POUR LESQUELS VOUS DEMANDEZ A UTILISER LE SYMBOLE<sup>6</sup></div>
			<div class="col-lg-12">
				<table class="table table-bordered" id="tablaProductos">
					<tr>
						<td>Produit</td>
						<td>Volume Total Estimé à Commercialiser</td>
						<td>Volume Produit Fini</td>
						<td>Volume Matière Première</td>
						<td>Pays d’ Origine</td>
						<td>Pays de Destinatio</td>
						<td>
							<button type="button" onclick="tablaProductos()" class="btn btn-primary" aria-label="Left Align">
							  <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
							</button>
							
						</td>					
					</tr>
					<tr>
						<td>
							<input type="text" class="form-control" name="producto[0]" id="exampleInputEmail1" placeholder="Producto">
						</td>
						<td>
							<input type="text" class="form-control" name="volumen_estimado[0]" id="exampleInputEmail1" placeholder="Volumen Estimado">
						</td>
						<td>
							<input type="text" class="form-control" name="volumen_terminado[0]" id="exampleInputEmail1" placeholder="Volumen Terminado">
						</td>

						<td>
							<input type="text" class="form-control" name="volumen_materia[0]" id="exampleInputEmail1" placeholder="Volumen Materia">
						</td>
						<td>
							<input type="text" class="form-control" name="origen[0]" id="exampleInputEmail1" placeholder="Origen">
						</td>
						<td>
							<input type="text" class="form-control" name="destino[0]" id="exampleInputEmail1" placeholder="Destino">
						</td>


					</tr>				
					<tr>
						<td colspan="6">
							<h6><sup>6</sup> L’information fournie dans cette section sera traitée en toute confidentialité. Veuillez insérer des colonnes supplémentaires si nécessaire.</h6>
						</td>
					</tr>
				</table>
			</div>

			<div class="col-lg-12 text-center alert alert-success" style="padding:7px;">
				<b>ENGAGEMENTS</b>
			</div>
			<div class="col-lg-12 text-justify">
				<p>1. Par l’envoi de cette demande, vous manifestez souhaiter recevoir une proposition d’enregistrement.</p>
				<p>2. Le processus d’enregistrement débutera dès réception du paiement.</p>
				<p>3. L’envoi et la réception de cette demande ne garantissent pas l’acceptation de l’enregistrement.</p>
				<p>4. Connaître et respecter toutes les exigences de la Norme Générale du Symbole des Petits Producteurs qui vous sont appliquées en qualité d’Acheteurs, de Centrale de commercialisation d’Organisations de Petits Producteurs, d’Intermédiaires et de Sous-traitants, tant critiques que minima, indépendamment du type d’évaluation réalisée.</p>
			</div>
			<div class="col-lg-12">
				<p style="font-size:14px;">
					<strong>Nom de la personne responsable de la véracité des informations fournies:</strong>
				</p>
				<input type="text" class="form-control" id="responsable" name="responsable" placeholder="Nombre del Responsable" required>	

				<!--<label for="nombre_oc">
					OC que recibe la solicitud:
				</label>
				<input type="text" class="form-control" id="nombre_oc" name="nombre_oc">-->
			</div>
			<div class="col-md-12">
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
    tuvo_ventas = document.getElementsByName("preg13");
    opcion_venta = document.getElementsByName("preg14");
     
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
      alert('Vous devez sélectionner "OUI" si vous avez eu des achats, sinon "NON"');
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
	      alert('Vous avez sélectionné "OUI" vous avez eu des achats; vous devez indiquer le montant des achats SPP');
	      //alert(valor_venta);
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
	  cell4.innerHTML = '<div class="col-md-6">SI<input type="radio" class="form-control" name="interrumpida['+contador+']" value="SI"></div><div class="col-md-6">NO<input type="radio" class="form-control" name="interrumpida['+contador+']" value="NO"></div>';
	  }
	}	

	function tablaEmpresas()
	{
		contador++;
	var table = document.getElementById("tablaEmpresas");
	  {
	  var row = table.insertRow(2);
	  var cell1 = row.insertCell(0);
	  var cell2 = row.insertCell(1);


	  cell1.innerHTML = '<input type="text" class="form-control" name="subempresa['+contador+']" id="exampleInputEmail1" placeholder="EMPRESA">';
	  cell2.innerHTML = '<input type="text" class="form-control" name="servicio['+contador+']" id="exampleInputEmail1" placeholder="SERVICIO">';

	  }
	}	
	function mostrar_empresas(){
		document.getElementById('contenedor_tablaEmpresas').style.display = 'block';
	}
	function ocultar_empresas()
	{
		document.getElementById('contenedor_tablaEmpresas').style.display = 'none';
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
	 
	  cell1.innerHTML = '<input type="text" class="form-control" name="producto['+cont+']" id="exampleInputEmail1" placeholder="Producto">';
	  
	  cell2.innerHTML = '<input type="text" class="form-control" name="volumen_estimado['+cont+']" id="exampleInputEmail1" placeholder="Volumen Estimado">';
	  
	  cell3.innerHTML = '<input type="text" class="form-control" name="volumen_terminado['+cont+']" id="exampleInputEmail1" placeholder="Volumen Terminado">';
	  
	  cell4.innerHTML = '<input type="text" class="form-control" name="volumen_materia['+cont+']" id="exampleInputEmail1" placeholder="Volumen Materia">';
	  
	  cell5.innerHTML = '<input type="text" class="form-control" name="origen['+cont+']" id="exampleInputEmail1" placeholder="Origen">';
	  
	  cell6.innerHTML = '<input type="text" class="form-control" name="destino['+cont+']" id="exampleInputEmail1" placeholder="Destino">';
	   

	  }

	}	

</script>