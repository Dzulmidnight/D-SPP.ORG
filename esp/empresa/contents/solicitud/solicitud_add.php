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

	$insertSQL = sprintf("INSERT INTO solicitud_registro (tipo_solicitud, facturacion_total, idempresa, idoc, comprador_final, intermediario, maquilador, contacto1_nombre, contacto2_nombre, contacto1_cargo, contacto2_cargo, contacto1_email, contacto2_email, contacto1_telefono, contacto2_telefono, adm1_nombre, adm2_nombre, adm1_email, adm2_email, adm1_telefono, adm2_telefono, preg1, preg2, preg3, preg4, produccion, procesamiento, importacion, preg6, preg7, preg8, preg9, preg10, preg12, preg13, preg14, preg15, responsable, fecha_registro, estatus_interno ) VALUES (%s, %s, %s, %s, %s, %s, %s,%s, %s, %s, %s, %s, %s, %s, %s, %s,%s, %s, %s, %s, %s, %s, %s, %s, %s,%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
		   GetSQLValueString($_POST['tipo_solicitud'], "text"),
		   GetSQLValueString($_POST['facturacion_total'], "double"),
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
		$insertSQL = sprintf("INSERT INTO contactos(idempresa, nombre, cargo, telefono1, email1) VALUES (%s, %s, %s, %s, %s)",
			GetSQLValueString($idempresa, "int"),
			GetSQLValueString($_POST['contacto1_nombre'], "text"),
			GetSQLValueString($_POST['contacto1_cargo'], "text"),
			GetSQLValueString($_POST['contacto1_telefono'], "text"),
			GetSQLValueString($_POST['contacto1_email'], "text"));
		$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

	}
	if(!empty($_POST['contacto2_nombre'])){
		$insertSQL = sprintf("INSERT INTO contactos(idempresa, nombre, cargo, telefono1, email1) VALUES (%s, %s, %s, %s, %s)",
			GetSQLValueString($idempresa, "int"),
			GetSQLValueString($_POST['contacto2_nombre'], "text"),
			GetSQLValueString($_POST['contacto2_cargo'], "text"),
			GetSQLValueString($_POST['contacto2_telefono'], "text"),
			GetSQLValueString($_POST['contacto2_email'], "text"));
		$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

	}
	if(!empty($_POST['adm1_nombre'])){
		$insertSQL = sprintf("INSERT INTO contactos(idempresa, nombre, cargo, telefono1, email1) VALUES (%s, %s, %s, %s, %s)",
			GetSQLValueString($idempresa, "int"),
			GetSQLValueString($_POST['adm1_nombre'], "text"),
			GetSQLValueString('ADMINISTRATIVO', "text"),
			GetSQLValueString($_POST['adm1_telefono'], "text"),
			GetSQLValueString($_POST['adm1_email'], "text"));
		$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

	}
	if(!empty($_POST['contacto2_nombre'])){
		$insertSQL = sprintf("INSERT INTO contactos(idempresa, nombre, cargo, telefono1, email1) VALUES (%s, %s, %s, %s, %s)",
			GetSQLValueString($idempresa, "int"),
			GetSQLValueString($_POST['contacto2_nombre'], "text"),
			GetSQLValueString('ADMINISTRATIVO', "text"),
			GetSQLValueString($_POST['contacto2_telefono'], "text"),
			GetSQLValueString($_POST['contacto2_email'], "text"));
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
		$asunto = "D-SPP | Solicitud de Registro para Compradores y otros Actores ";
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
		              <th scope="col" align="left" width="280"><strong>Solicitud de Registro para Compradores y otro Actores / Application for Buyers’, Registration </strong></th>
		            </tr>
		            <tr>
		              <td style="padding-top:10px;">
		   
		              Para poder consultar la solicitud, por favor iniciar sesión en su cuenta de OC(Organismo de Certificación) en el siguiente enlace: <a href="http://d-spp.org" target="_new">www.d-spp.org</a>
		              <br>
		              To consult the application, please log in to your CE(Certification Entity) account, in the following link: <a href="http://d-spp.org" target="_new">www.d-spp.org</a>

		         

		              </td>
		            </tr>
				    <tr>
				      <td align="left">Teléfono / Company phone: '.$_POST['telefono'].'</td>
				    </tr>

				    <tr>
				      <td align="left">'.$_POST['pais'].'</td>
				    </tr>
				    <tr>
				      <td align="left" style="color:#ff738a;">Email: '.$_POST['email'].'</td>
				    </tr>
				    <tr>
				      <td align="left" style="color:#ff738a;">Email: '.$_POST['contacto1_email'].'</td>
				    </tr>

				    <tr>
				      <td colspan="2">
				        <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;" border="1" width="650px">
				          <tbody>
				            <tr style="font-size: 12px; text-align:center; background-color:#dff0d8; color:#3c763d;" height="50px;">
				              <td width="130px">Nombre de la Empresa/Company name</td>
				              <td width="130px">País / Country</td>
				              <td width="130px">Organismo de Certificación / Certification Entity</td>
				           
				              <td width="130px">Fecha de solicitud/Date of application</td>
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
        //$mail->Username = "soporte@d-spp.org";
        //$mail->Password = "/aung5l6tZ";
        $mail->Subject = utf8_decode($asunto);
        $mail->Body = utf8_decode($cuerpo_correo);
        $mail->MsgHTML(utf8_decode($cuerpo_correo));
        $mail->Send();
        $mail->ClearAddresses();

 		$mensaje = "Se ha enviado la Solicitud de Registro al OC, en breve seras contactado";


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
				<h3 class="text-center">Solicitud de Registro para Compradores y otros Actores</h3>
			</div>

			<div class="col-md-12 text-center alert alert-success" style="padding:7px;"><b>DATOS GENERALES</b></div>

			<div class="col-lg-12 alert alert-info" style="padding:7px;">

				<div class="col-md-12 alert alert-warning" style="padding:5px;">
						<ul>
							<li>
								<b>
									SI CONTARON CON CERTIFICACIÓN DE SPP ANTERIORMENTE (CON EL ORGANISMO DE CERTIFICACIÓN ACTUAL ó CON OTRO ORGANISMO DE CERTIFICACIÓN) DEBEN ELEGIR <span style="color:red">"RENOVACION DEL CERTIFICADO"</span>
								</b>
							</li>
							<li><b>SI ES LA PRIMERA VEZ QUE SE CERTIFICAN DEBEN ELEGIR <span style="color:red">"PRIMERA VEZ"</span></b></li>
						</ul>
					 
				</div>

				<div class="col-md-6">
					<div class="col-md-12">
						<b>ENVAR AL OC (selecciona el OC al que deseas enviar la solicitud):</b>
					</div>
					<div class="col-md-12">
						<select class="form-control" name="idoc" id="" required>
							<option value="">Seleccione un OC</option>
							<?php 
							$query = "SELECT idoc, abreviacion FROM oc";
							$row_oc = mysql_query($query,$dspp) or die(mysql_error());

							while($oc = mysql_fetch_assoc($row_oc)){
							?>
							<option value="<?php echo $oc['idoc']; ?>" <?php if($empresa['idoc'] == $oc['idoc']){ echo "selected"; } ?>><?php echo $oc['abreviacion']; ?></option>
							<?php
							}
							 ?>
							 <option value="TODOS">ENVIAR A TODOS LOS OC</option>
						</select>
					</div>
				</div>
				<div class="col-md-6">
					<div class="col-md-12">
						<p class="text-center"><strong>SELECCIONE EL TIPO DE SOLICITUD</strong></p>
					</div>
					<div class="col-md-6">
						<label for="nueva">1º SOLICITUD</label>
						<input type="radio" class="form-control" id="nueva" name="tipo_solicitud" value="NUEVA">
					</div>
					<div class="col-md-6">
						<label for="renovacion">RENOVACIÓN DE REGISTRO</label>
						<input type="radio" class="form-control" id="renovacion" name="tipo_solicitud" value="RENOVACION">
					</div>
				</div>
			</div>
			<?php 
			if($empresa['comprador']){
			?>
				<div class="row">
					<div class="col-md-12 alert alert-info">
						<b>VALOR TOTAL DE VENTAS (<i>independientemente de si se trata de ventas SPP o no</i>)</b>
						<br>
						Nota: <i>Este dato es necesario para determinar la membresia a pagar por parte del Comprador Final.   <a href="#">Reglamento de Costos V8_2017-02-03, 4.3 (descargar</a>) "Los Compradores Finales pagan una cuota de Membresía Anual (en USD) equivalente a  un porcentaje del total de facturación de la empresa, independientemente de si se trata de ventas SPP o no"</i>

						<input type="number" step="any" class="form-control" id="facturacion_total" name="facturacion_total" placeholder="Valor de ventas, ingresar solo numeros" required>
					</div>
				</div>
			<?php
			}
			 ?>

			<!------ INICIA INFORMACION GENERAL Y DATOS FISCALES ------>
			<div class="row">
				<div class="col-md-6">
					<div class="col-md-12 text-center alert alert-warning" style="padding:7px;">INFORMACION GENERALES</div>
					<label for="fecha_elaboracion">FECHA ELABORACIÓN</label>
					<input type="text" class="form-control" id="fecha_elaboracion" name="fecha_elaboracion" value="<?php echo date('Y-m-d', time()); ?>" readonly>	

					<label for="spp">CODIGO DE IDENTIFICACIÓN SPP(#SPP): </label>
					<input type="text" class="form-control" id="spp" name="spp" value="<?php echo $empresa['spp']; ?>">

					<label for="nombre">NOMBRE COMPLETO DE LA ORGANIZACIÓN DE PEQUEÑOS PRODUCTORES: </label>
					<textarea name="nombre" id="nombre" class="form-control"><?php echo $empresa['nombre']; ?></textarea>


					<label for="pais">PAÍS:</label>
					<?php 
					$row_pais = mysql_query("SELECT * FROM paises",$dspp) or die(mysql_error());
					 ?>
					 <select name="pais" id="pais" class="form-control">
					 	<option value="">Selecciona un País</option>
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

					<label for="direccion_oficina">DIRECCIÓN COMPLETA DE SUS OFICINAS CENTRALES(CALLE, BARRIO, LUGAR, REGIÓN)</label>
					<textarea name="direccion_oficina" id="direccion_oficina"  class="form-control"><?php echo $empresa['direccion_oficina']; ?></textarea>

					<label for="email">CORREO ELECTRÓNICO:</label>
					<input type="text" class="form-control" id="email" name="email" value="<?php echo $empresa['email']; ?>">

					<label for="email">TELÉFONOS (CODIGO DE PAÍS + CÓDIGO DE ÁREA + NÚMERO):</label>
					<input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo $empresa['telefono']; ?>">	

					<label for="sitio_web">SITIO WEB:</label>
					<input type="text" class="form-control" id="sitio_web" name="sitio_web" value="<?php echo $empresa['sitio_web']; ?>">

				</div>

				<div class="col-md-6">
					<div class="col-md-12 text-center alert alert-warning" style="padding:7px;">DATOS FISCALES PARA FACTURACIÓN</div>

					<label for="razon_social">RAZÓN SOCIAL</label>
					<input type="text" class="form-control" id="razon_social" name="razon_social" value="<?php echo $empresa['razon_social']; ?>">

					<label for="direccion_fiscal">DIRECCIÓN FISCAL</label>
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
					<div class="col-md-12 text-center alert alert-warning" style="padding:7px;">PERSONA(S) DE CONTACTO</div>

					<label for="persona1">PERSONA(S) DE CONTACTO</label>
					<input type="text" class="form-control" id="persona1" name="contacto1_nombre" placeholder="* Nombre Persona 1" required>
					<input type="text" class="form-control" id="" name="contacto2_nombre" placeholder="Nombre Persona 2">

					<label for="cargo">CARGO</label>
					<input type="text" class="form-control" id="cargo" name="contacto1_cargo" placeholder="* Cargo Persona 1" required>
					<input type="text" class="form-control" id="" name="contacto2_cargo" placeholder="Cargo Persona 2">

					<label for="email">CORREO ELECTRÓNICO</label>
					<input type="email" class="form-control" id="email" name="contacto1_email" placeholder="* Email Persona 1" required>
					<input type="email" class="form-control" id="" name="contacto2_email" placeholder="Email Persona 2">

					<label for="telefono">TELEFONO</label>
					<input type="text" class="form-control" id="telefono" name="contacto1_telefono" placeholder="* Telefono Persona 1" required>
					<input type="text" class="form-control" id="" name="contacto2_telefono" placeholder="Telefono Persona 2">

				</div>

				<div class="col-md-6">
					<div class="col-md-12 text-center alert alert-warning" style="padding:7px;">PERSONA(S) ÁREA ADMINISTRATIVA</div>

					<label for="persona_adm">PERSONA(S) DEL ÁREA ADMINSITRATIVA</label>
					<input type="text" class="form-control" id="persona_adm" name="adm1_nombre" placeholder="Nombre Persona 1">
					<input type="text" class="form-control" id="" name="adm2_nombre" placeholder="Nombre Persona 2">

					<label for="email_adm">CORREO ELECTRÓNICO</label>
					<input type="email" class="form-control" id="email_adm" name="adm1_email" placeholder="Email Persona 1">
					<input type="email" class="form-control" id="" name="adm2_email" placeholder="Email Persona 2">

					<label for="telefono_adm">TELÉFONO</label>
					<input type="text" class="form-control" id="telefono_adm" name="adm1_telefono" placeholder="Telefono Persona 1">
					<input type="text" class="form-control" id="" name="adm2_telefono" placeholder="Telefono Persona 2">
				</div>
			</div>
			<!------ FIN INFORMACION CONTACTOS Y AREA ADMINISTRATIVA ------>

			<!------ INICIA INFORMACION DATOS DE OPERACIÓN ------>
			<div class="col-md-12 alert alert-info">
				<div>
					<label for="alcance_opp">
						SELECCIONE EL TIPO DE EMPRESA SPP PARA EL CUAL SE SOLICITA EL REGISTRO. UN INTERMEDIARIO NO PUEDE REGISTRARSE SPP SI NO CUENTA CON UN COMPRADOR FINAL REGISTRADO SPP O EN PROCESO DE REGISTRO. 
					</label>
				</div>

                  <div class="checkbox">
                    <label class="col-sm-4">
                      <input type="checkbox" name="comprador" <?php if($empresa['comprador']){echo "checked"; } ?> value="1"> COMPRADOR-FINAL
                    </label>
                    <label class="col-sm-4">
                      <input type="checkbox" name="intermediario" <?php if($empresa['intermediario']){echo "checked"; } ?> value="1"> INTERMEDIARIO
                    </label>
                    <label class="col-sm-4">
                      <input type="checkbox" name="maquilador" <?php if($empresa['maquilador']){echo "checked"; } ?> value="1"> MAQUILADOR
                    </label>
                  </div>
			</div>


			<div class="col-md-12 text-center alert alert-success" style="padding:7px;">DATOS DE OPERACIÓN</div>

			<div class="row">
				<div class="col-md-12">
					<label for="preg1">
						1.	¿CUÁLES SON LAS ORGANIZACIONES DE PEQUEÑOS PRODUCTORES A LAS QUE LES COMPRA O PRETENDE COMPRAR BAJO EL ESQUEMA DEL SÍMBOLO DE PEQUEÑOS PRODUCTORES?
					</label>
					<textarea name="preg1" id="preg1" class="form-control"></textarea>


					<label for="preg2">
						2.	¿QUIÉN O QUIÉNES SON LOS PROPIETARIOS DE LA EMPRESA?
					</label>
					<textarea name="preg2" id="preg2" class="form-control"></textarea>

					<label for="preg3">
						3. ESPECIFIQUE QUÉ PRODUCTO(S) QUIERE INCLUIR EN EL CERTIFICADO DEL SÍMBOLO DE PEQUEÑOS PRODUCTORES PARA LOS CUALES EL ORGNISMO DE CERTIFICACIÓN REALIZARÁ LA EVALUACIÓN.<sup>4</sup>
					</label>
					<input type="text" class="form-control" id="preg3" name="preg3">

					<label for="preg4">
						4. SI SU EMPRESA ES UN COMPRADOR FINAL, MENCIONE SI QUIEREN INCLUIR ALGÚN CALIFICATIVO ADICIONAL PARA USO COMPLEMENTARIO CON EL DISEÑO GRÁFICO DEL SÍMBOLO DE PEQUEÑOS PRODUCTORES.
					</label>
					<textarea name="preg4" id="preg4" class="form-control"></textarea>

					<div >
						<label for="alcance_opp">
							5. SELECCIONE EL ALCANCE QUE TIENE LA EMPRESA:
						</label>
					</div>
					<div class="col-md-4">
						<label>PRODUCCIÓN</label>
						<input type="checkbox" name="produccion" class="form-control" value="1">
					</div>
					<div class="col-md-4">
						<label>PROCESAMIENTO</label>
						<input type="checkbox" name="procesamiento" class="form-control" value="1">
					</div>
					<div class="col-md-4">
						<label>IMPORTACIÓN</label>
						<input type="checkbox" name="importacion" class="form-control" value="1">
					</div>


				<p><b>6.	SELECCIONE SI SUBCONTRATA LOS SERVICIOS DE PLANTAS DE PROCESAMIENTO, EMPRESAS DE COMERCIALIZACIÓN O EMPRESAS QUE REALICEN LA IMPORTACIÓN O EXPORTACIÓN</b></p>
				<div class="col-md-6">
					SI <input type="radio" class="form-control" name="preg6" onclick="mostrar_empresas()" id="preg6" value="SI">
				</div>
				<div class="col-md-6">
					NO <input type="radio" class="form-control" name="preg6" onclick="ocultar_empresas()" id="preg6" value="NO">
				</div>



				<p>SI LA RESPUESTA ES AFIRMATIVA, MENCIONE EL NOMBRE Y EL SERVICIO QUE REALIZA</p>
				<div id="contenedor_tablaEmpresas" class="col-md-12" style="display:none">
					<table class="table table-bordered" id="tablaEmpresas">
						<tr>
							<td>NOMBRE DE LA EMPRESA</td>
							<td>SERVICIO QUE REALIZA</td>
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
						7.	SI SUBCONTRATA LOS SERVICIOS DE PLANTAS DE PROCESAMIENTO, EMPRESAS DE COMERCIALIZACIÓN O EMPRESAS QUE REALICEN LA IMPORTACIÓN O EXPORTACIÓN, INDIQUE SI ESTAS ESTAN REGISTRADAS O VAN A REALIZAR EL REGISTRO BAJO EL PROGRAMA DEL SPP O SERÁN CONTROLADAS A TRAVÉS DE SU EMPRESA <sup>5</sup>
						<br>
						<small><sup>5</sup> Revisar el documento de 'Directrices Generales del Sistema SPP' en su última versión.</small>
					</label>
					<textarea name="preg7" id="preg7" class="form-control"></textarea>

					<label for="preg8">
						8. ADICIONAL A SUS OFICINAS CENTRALES, ESPECIFIQUE CUÁNTOS CENTROS DE ACOPIO, ÁREAS DE PROCESAMIENTO U OFICINAS ADICIONALES TIENE.
					</label>
					<textarea name="preg8" id="preg8" class="form-control"></textarea>

					<label for="preg9">
						9. EN CASO DE TENER CENTROS DE ACOPIO, ÁREAS DE PROCESAMIENTO U OFICINAS ADICIONALES,  ANEXAR UN CROQUIS GENERAL MOSTRANDO SU UBICACIÓN.
					</label>
					<input type="file" id="preg9" name="preg9" class="form-control">

					<label for="preg10">
						10. ¿CUENTA CON UN SISTEMA DE CONTROL INTERNO PARA DAR CUMPLIMIENTO A LOS CRITERIOS DE LA NORMA GENERAL DEL SÍMBOLO DE PEQUEÑOS PRODUCTORES?, EN SU CASO, EXPLIQUE.
					</label>
					<textarea name="preg10" id="preg10" class="form-control"></textarea>

					<p class="alert alert-info"><b>11. LLENAR LA TABLA DE ACUERDO A LAS CERTIFICACIONES QUE TIENE, (EJEMPLO: EU, NOP, JASS, FLO, etc).</b></p>

					<table class="table table-bordered" id="tablaCertificaciones">
						<tr>
							<td>CERTIFICACIÓN</td>
							<td>CERTIFICADORA</td>
							<td>AÑO INICIAL DE CERTIFICACIÓN?</td>
							<td>¿HA SIDO INTERRUMPIDA?</td>	
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
								<div class="col-md-6">SI<input type="radio" class="form-control" name="interrumpida[0]" value="SI"></div>
								<div class="col-md-6">NO<input type="radio" class="form-control" name="interrumpida[0]" value="NO"></div>
							</td>
						</tr>
					</table>	

					<label for="preg12">
						12.DE LAS CERTIFICACIONES CON LAS QUE CUENTA, EN SU MÁS RECIENTE EVALUACIÓN INTERNA Y EXTERNA, ¿CUÁNTOS INCUMPLIMIENTOS SE IDENTIFICARON? Y EN SU CASO, ¿ESTÁN RESUELTOS O CUÁL ES SU ESTADO?</label>
					<textarea name="preg12" id="preg12" class="form-control"></textarea>

					<p for="op_preg11">
						<b>13. DEL TOTAL DE SUS COMPRAS ¿QUÉ PORCENTAJE DEL PRODUCTO CUENTA CON LA CERTIFICACIÓN DE ORGÁNICO, COMERCIO JUSTO Y/O SÍMBOLO DE PEQUEÑOS PRODUCTORES?</b>
						<i>(* Introducir solo cantidad, entero o decimales)</i>
						<div class="row">
							<div class="col-lg-12">
								<div class="col-md-3">
									<label for="organico">% ORGÁNICO</label>
									<input type="number" step="any" class="form-control" id="organico" name="organico" placeholder="Ej: 0.0">
								</div>
								<div class="col-md-3">
									<label for="comercio_justo">% COMERCIO JUSTO</label>
									<input type="number" step="any" class="form-control" id="comercio_justo" name="comercio_justo" placeholder="Ej: 0.0">
								</div>
								<div class="col-md-3">
									<label for="spp">SÍMBOLO DE PEQUEÑOS PRODUCTORES</label>
									<input type="number" step="any" class="form-control" id="spp" name="spp" placeholder="Ej: 0.0">
								</div>
								<div class="col-md-3">
									<label for="otro">SIN CERTIFICADO</label>
									<input type="number" step="any" class="form-control" id="otro" name="sin_certificado" placeholder="Ej: 0.0">
								</div>
							</div>
						</div>
					</p>					

					<p><b>14. TUVO COMPRAS SPP DURANTE EL CICLO DE REGISTRO ANTERIOR?</b></p>
						<div class="col-md-6">
							SI <input type="radio" class="form-control" name="preg13" id="preg13" value="SI">
						</div>
						<div class="col-md-6">
							NO <input type="radio" class="form-control" name="preg13" id="preg13" value="NO">
						</div>			
					<p>
						<b>15. SI SU RESPUESTA FUE POSITIVA FAVOR DE SELECCIONAR EL RANGO DEL VALOR TOTAL DE SUS COMPRAS SPP DEL CICLO ANTERIOR DE ACUERDO A LA SIGUIENTE TABLA 
					</p>

					<div class="well col-md-12 " id="tablaVentas">
						<div class="col-md-6"><p>Hasta $3,000 USD</p></div>
						<div class="col-md-6 "><input type="radio" name="preg14" class="form-control" id="ver" onclick="ocultar()" value="HASTA $3,000 USD"></div>
					
					
						<div class="col-md-6"><p>Entre $3,000 y $10,000 USD</p></div>
						<div class="col-md-6"><input type="radio" name="preg14" class="form-control" id="ver" onclick="ocultar()" value="ENTRE $3,000 Y $10,000 USD"></div>
					
					
						<div class="col-md-6"><p>Entre $10,000 a $25,000 USD</p></div>
						<div class="col-md-6"><input type="radio" name="preg14" class="form-control"  id="ver" onclick="ocultar()" value="ENTRE $10,000 A $25,000 USD"></div>
					
						<div class="col-md-6"><p>Más de $25,000 USD <sup>*</sup><br><h6><sup>*</sup>Especifique la cantidad.</h6></p></div>
						<div class="col-md-6"><input type="radio" name="preg14" class="form-control" id="exampleInputEmail1" onclick="mostrar()" value="mayor">
							<input type="text" name="preg14_1" class="form-control" id="oculto" style='display:none;' placeholder="Especifique la Cantidad">
						</div>

					</div>
							
					<label for="preg15">
						16. FECHA ESTIMADA PARA COMENZAR A USAR EL SÍMBOLO DE PEQUEÑOS PRODUCTORES.
					</label>
					<input type="text" class="form-control" id="preg15" name="preg15">
				</div>
			</div>

			<!------ FIN INFORMACION DATOS DE OPERACIÓN ------>

			<div class="col-md-12 text-center alert alert-success" style="padding:7px;">DATOS DE PRODUCTOS PARA LOS CUALES QUIERE UTILIZAR EL SÍMBOLO<sup>6</sup></div>
			<div class="col-lg-12">
				<table class="table table-bordered" id="tablaProductos">
					<tr>
						<td>Producto</td>
						<td>Volumen Total Estimado a Comercializar</td>
						<td>Volumen como Producto Terminado</td>
						<td>Volumen como Materia Prima</td>
						<td>País(es) de Origen</td>
						<td>País(es) Destino</td>
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
							<h6><sup>6</sup> La información proporcionada en esta sección será tratada con plena confidencialidad. Favor de insertar filas adicionales de ser necesario.</h6>
						</td>
					</tr>
				</table>
			</div>

			<div class="col-lg-12 text-center alert alert-success" style="padding:7px;">
				<b>COMPROMISOS</b>
			</div>
			<div class="col-lg-12 text-justify">
				<p>1. Con el envío de esta solicitud se manifiesta el interés de recibir una propuesta de Registro.</p>
				<p>2. El proceso de Registro comenzará en el momento que se confirme la recepción del pago correspondiente.</p>
				<p>3. La entrega y recepción de esta solicitud no garantiza que el proceso de Registro será positivo.</p>
				<p>4. Conocer y dar cumplimiento a todos los requisitos de la Norma General del Símbolo de Pequeños Productores que le apliquen como Compradores, Comercializadoras Colectiva de Organizaciones de Pequeños Productores, Intermediarios y Maquiladores, tanto Críticos como Mínimos, independientemente del tipo de evaluación que se realice.</p>
			</div>
			<div class="col-lg-12">
				<p style="font-size:14px;">
					<strong>Nombre de la persona que se responsabiliza de la veracidad de la información del formato y que le dará seguimiento a la solicitud de parte del solicitante:</strong>
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
				<input type="submit" class="btn btn-primary form-control" value="Enviar Solicitud" onclick="return validar()">
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
      alert("Debes de seleccionar un Tipo de Solicitud");
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
      alert("Debe seleccionar \"SI\" tuvo ó \"NO\" compras");
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
	      alert("Seleccionaste que \"SI\" tuviste compras, debes seleccionar el monto de compras SPP");
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