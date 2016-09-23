<?php require_once('../Connections/dspp.php'); 
      include_once("../../PHPMailer/class.phpmailer.php");
      include_once("../../PHPMailer/class.smtp.php");

        $mail = new PHPMailer();

        $mail->IsSMTP();
        //$mail->SMTPSecure = "ssl";
        $mail->Host = "mail.d-spp.org";
        $mail->Port = 25;
        $mail->SMTPAuth = true;
        $mail->Username = "soporte@d-spp.org";
        $mail->Password = "/aung5l6tZ";
        //$mail->SMTPDebug = 1;

        $mail->From = "soporte@d-spp.org";
        $mail->FromName = "CERT - DSPP";
        $mail->AddBCC("yasser.midnight@gmail.com", "correo Oculto");
        $mail->AddReplyTo("cert@spp.coop");
?>
<?php
if (!isset($_SESSION)) {
  session_start();
	
	$redireccion = "../index.php?COM";

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

/*if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {



  $insertGoTo = "main_menu.php?SOLICITUD&add&mensaje=Solicitud agregada correctamente, se ha notificado al OC por email.";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}*/


mysql_select_db($database_dspp, $dspp);
$query_pais = "SELECT nombre FROM paises ORDER BY nombre ASC";
$pais = mysql_query($query_pais, $dspp) or die(mysql_error());
$row_pais = mysql_fetch_assoc($pais);
$totalRows_pais = mysql_num_rows($pais);

mysql_select_db($database_dspp, $dspp);
$query_oc = "SELECT idoc, idf, abreviacion, pais FROM oc ORDER BY nombre ASC";
$oc = mysql_query($query_oc, $dspp) or die(mysql_error());
$row_oc = mysql_fetch_assoc($oc);
$totalRows_oc = mysql_num_rows($oc);


/*$query_com = "SELECT * FROM com WHERE idcom='$_SESSION[idcom]'";
$com = mysql_query($query_com,$dspp) or die(mysql_error());
$row_com = mysql_fetch_assoc($com);

$query_contacto = "SELECT * FROM contacto WHERE idcom='$_SESSION[idcom]'";
$contacto = mysql_query($query_contacto,$dspp) or die(mysql_error());
$row_contacto = mysql_fetch_assoc($contacto);
*/

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

$estadoPublico = "2";
$estadoInterno = "1";
$fecha_actual = time();

/************ VARIABLES DE CONTROL ******************/




if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) { // INICIA INSERCION DE SOLICITUD
 mysql_select_db($database_dspp, $dspp);

    $array_resp5 = NULL;
    $array_tipo_empresa = NULL;
    $resp14_15 = NULL;
    $resp14_15_1 = NULL;
    $idcom = $_POST['idcom'];



	if(isset($_POST['resp5'])){
		if(is_array($_POST['resp5'])){
			$resp5 = $_POST['resp5'];

			for ($i=0; $i < count($resp5) ; $i++) { 
				$array_resp5 .= $resp5[$i]." - ";
			}
		}else{
				$array_resp5 = NULL;
		}
	}else{
		$array_resp5 = NULL;
	}

	if(isset($_POST['resp6'])){
		$resp6 = $_POST['resp6'];
	}else{
		$resp6 = null;
	}
	
	if(isset($_POST['tipo_empresa'])){
		if(is_array($_POST['tipo_empresa'])){
			$tipo_empresa = $_POST['tipo_empresa'];

			for ($i=0; $i < count($tipo_empresa) ; $i++) { 
				$array_tipo_empresa .= $tipo_empresa[$i]." - ";
			}
		}else{
				$array_tipo_empresa = NULL;
		}
	}else{
		$array_tipo_empresa = NULL;
	}


	if(isset($_POST['resp14_15'])){
		if(isset($_POST['resp14_15']) && $_POST['resp14_15'] == "mayor"){
			$resp14_15 = $_POST['resp14_15_1'];
		}else{
			$resp14_15 = $_POST['resp14_15'];
		}
	}else{
		$op_resp13 = NULL;
	}


	if(!empty($_POST['resp14'])){

		if(isset($_POST['resp14'])){
			$resp14 = $_POST['resp14'];
		}else{
			$resp14 = "";
		}

	}else{
		$resp14 = NULL;
	}

    
    if(isset($_POST['idoc'])){ /* INICIA INSERCION INDIVIDUAL DE SOLICITUD*/
    	$idoc = $_POST['idoc'];
    	$idcom = $_POST['idcom'];

		$insertSQL = sprintf("INSERT INTO solicitud_registro(idcom, idoc, procedimiento, p1_nombre, p2_nombre, p1_cargo, p2_cargo, p1_correo, p2_correo, p1_telefono, p2_telefono, adm1_nombre, adm2_nombre, adm1_correo, adm2_correo, adm1_telefono, adm2_telefono, tipo_empresa, resp1, resp2, resp3, resp4, resp5, resp6, resp7, resp8, resp9, resp10, resp12, resp13, resp14, resp14_15, resp16, responsable, fecha_elaboracion, status_interno, status_publico) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
		   GetSQLValueString($_POST['idcom'], "int"),
	       GetSQLValueString($_POST['idoc'], "int"),
	       GetSQLValueString($_POST['procedimiento'], "text"),
	       GetSQLValueString($_POST['p1_nombre'], "text"),
	       GetSQLValueString($_POST['p2_nombre'], "text"),
	       GetSQLValueString($_POST['p1_cargo'], "text"),
	       GetSQLValueString($_POST['p2_cargo'], "text"),
	       GetSQLValueString($_POST['p1_correo'], "text"),
	       GetSQLValueString($_POST['p2_correo'], "text"),
	       GetSQLValueString($_POST['p1_telefono'], "text"),
	       GetSQLValueString($_POST['p2_telefono'], "text"),
	       GetSQLValueString($_POST['adm1_nombre'], "text"),
	       GetSQLValueString($_POST['adm2_nombre'], "text"),
	       GetSQLValueString($_POST['adm1_correo'], "text"),
	       GetSQLValueString($_POST['adm2_correo'], "text"),
	       GetSQLValueString($_POST['adm1_telefono'], "text"),
	       GetSQLValueString($_POST['adm2_telefono'], "text"),
	       GetSQLValueString($array_tipo_empresa, "text"),
	       GetSQLValueString($_POST['resp1'], "text"),
	       GetSQLValueString($_POST['resp2'], "text"),
	       GetSQLValueString($_POST['resp3'], "text"),
	       GetSQLValueString($_POST['resp4'], "text"),
	       GetSQLValueString($array_resp5, "text"),
	       GetSQLValueString($resp6, "text"),
	       GetSQLValueString($_POST['resp7'], "text"),
	       GetSQLValueString($_POST['resp8'], "text"),
	       GetSQLValueString($_POST['resp9'], "text"),
	       GetSQLValueString($_POST['resp10'], "text"),
	       GetSQLValueString($_POST['resp12'], "text"),
	       GetSQLValueString($_POST['resp13'], "text"),
	       GetSQLValueString($resp14, "text"),
	       GetSQLValueString($resp14_15, "text"),
	       GetSQLValueString($_POST['resp16'], "text"),
	       GetSQLValueString($_POST['responsable'], "text"),
	       GetSQLValueString($_POST['fecha_elaboracion'], "int"),
	       GetSQLValueString($_POST['tipo_solicitud'], "int"),
	       GetSQLValueString($_POST['status_publico'], "int"));

	   	   $Result1 = mysql_query($insertSQL,$dspp) or die(mysql_error());

			$idsolicitud_registro = mysql_insert_id($dspp); 

			if(!empty($_POST['certificacion'])){
				$certificacion = $_POST['certificacion'];
			}else{
				$certificacion = NULL;
			}


			if(!empty($_POST['certificadora'])){
				$certificadora = $_POST['certificadora'];
			}else{
				$certificadora = NULL;
			}

			if(!empty($_POST['ano_inicial'])){
				$ano_inicial = $_POST['ano_inicial'];
			}else{
				$ano_inicial = NULL;
			}

			if(!empty($_POST['interrumpida'])){
				$interrumpida = $_POST['interrumpida'];
			}else{
				$interrumpida = NULL;
			}

			for($i=0;$i<count($certificacion);$i++){// INICIA FOR CERTIFICACIONES
				if($certificacion[$i] != NULL){
					#for($i=0;$i<count($certificacion);$i++){
					$insertSQL = sprintf("INSERT INTO certificaciones (idsolicitud_registro, certificacion, certificadora, ano_inicial, interrumpida) VALUES (%s, %s, %s, %s, %s)",
					    GetSQLValueString($idsolicitud_registro, "int"),
					    GetSQLValueString($certificacion[$i], "text"),
					    GetSQLValueString($certificadora[$i], "text"),
					    GetSQLValueString($ano_inicial[$i], "text"),
					    GetSQLValueString($interrumpida[$i], "text"));

					$Result = mysql_query($insertSQL, $dspp) or die(mysql_error());
					#}
				}
			}// TERMINA FOR CERTIFICACIONES

			$producto = $_POST['producto'];
			$volumenEstimado = $_POST['volumenEstimado'];
			$volumenTerminado = $_POST['volumenTerminado'];
			$materia = $_POST['materia'];
			$paisOrigen = $_POST['paisOrigen'];
			$paisDestino = $_POST['paisDestino'];

			for ($i=0;$i<count($producto);$i++) { // INICIA FOR "PRODUCTOS"
				if($producto[$i] != NULL){

				    $insertSQL = sprintf("INSERT INTO productos (idsolicitud_registro, producto, volumenEstimado, volumenTerminado, materia, origen, destino) VALUES (%s, %s, %s, %s, %s, %s, %s)",
				          GetSQLValueString($idsolicitud_registro, "int"),
				          GetSQLValueString($producto[$i], "text"),
				          GetSQLValueString($volumenEstimado[$i], "text"),
				          GetSQLValueString($volumenTerminado[$i], "text"),
				          GetSQLValueString($materia[$i], "text"),
				          GetSQLValueString($paisOrigen[$i], "text"),
				          GetSQLValueString($paisDestino[$i], "text"));

				  $Result = mysql_query($insertSQL, $dspp) or die(mysql_error());
				}
			}// TERMINA FOR "PRODUCTOS"

				$resp6_empresa = $_POST['resp6_empresa'];
				$resp6_servicio = $_POST['resp6_servicio'];


				for ($i=0;$i<count($resp6_empresa);$i++) { // INICIA FOR "SUB EMPRESAS"
					if($resp6_empresa[$i] != NULL){

					    $insertSQL = sprintf("INSERT INTO subEmpresas (idsolicitud_registro, nombre, servicio, idcom) VALUES (%s, %s, %s, %s)",
					          GetSQLValueString($idsolicitud_registro, "int"),
					          GetSQLValueString($resp6_empresa[$i], "text"),
					          GetSQLValueString($resp6_servicio[$i], "text"),
					          GetSQLValueString($_POST['idcom'], "int"));

					  $Result = mysql_query($insertSQL, $dspp) or die(mysql_error());
					}
				}// TERMINA FOR "SUB EMPRESAS"

	
			   $query = "SELECT * FROM oc WHERE idoc = $_POST[idoc]";
			   $oc = mysql_query($query,$dspp) or die(mysql_error());
			   $row_oc = mysql_fetch_assoc($oc);

			   $querycom = "SELECT * FROM com WHERE idcom = $_POST[idcom]";
			   $ejecutar = mysql_query($querycom,$dspp) or die(mysql_error());
			   $row_com = mysql_fetch_assoc($ejecutar);


			    $nombre = $row_com['nombre'];
			    $abreviacion = $row_com['abreviacion'];
			    $pais = $row_com['pais'];
			    $nombreOC = $row_oc['nombre'];
			    $fecha_elaboracion = $_POST['fecha_elaboracion'];
			    $producto = $_POST['producto'];
			    $telefono1 = $_POST['p1_telefono'];
			    $direccion = $row_com['direccion'];
			    $ciudad = $row_com['ciudad'];
			    $emailCOM1 = $_POST['p1_correo'];
			    $emailCOM2 = $_POST['p2_correo'];
			    $fecha = date("d/m/Y", $fecha_elaboracion);
			    //$correo = $_POST['p1_correo'];
			    //$correo = $_POST['p2_correo'];

			    $paisEstado = $pais.' / '.$ciudad;


			    $destinatario = $row_oc['email'];
			    $asunto = "D-SPP Solcitud de Registro para Compradores y otros Actores"; 


				$cuerpo = '
					<html>
					<head>
						<meta charset="utf-8">
					</head>
					<body>
					
						<table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
						  <tbody>
				            <tr>
				              <th rowspan="7" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
				              <th scope="col" align="left" width="280"><strong>Solcitud de Registro para Compradores y otros Actores  / Application for Buyers`, Registration</strong></th>
				            </tr>
				            <tr>
				              <td style="padding-top:10px;"><i>Para poder consultar la solicitud, por favor iniciar sesión en su cuenta de OC en el siguiente enlace: <a href="http://d-spp.org/?OC" target="_new">www.d-spp.org/?OC</a></i>.</td>
				            </tr>
						    <tr>
						      <td align="left">Teléfono / phone COM: '.$telefono1.'</td>
						    </tr>
						    <tr>
						      <td align="left">'.$direccion.'</td>
						    </tr>
						    <tr>
						      <td align="left">'.$paisEstado.'</td>
						    </tr>
						    <tr>
						      <td align="left" style="color:#ff738a;">Email: '.$emailCOM1.'</td>
						    </tr>
						    <tr>
						      <td align="left" style="color:#ff738a;">Email: '.$emailCOM2.'</td>
						    </tr>

						    <tr>
						      <td colspan="2">
						        <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;" border="1" width="650px">
						          <tbody>
						            <tr style="font-size: 12px; text-align:center; background-color:#dff0d8; color:#3c763d;" height="50px;">
						              <td width="130px">Nombre de la Empresa / Company Name</td>
						              <td width="130px">Abreviación / Short name</td>
						              <td width="130px">País / Country</td>
						              <td width="130px">Organismo de Certificación / Certification Entity</td>
						           
						              <td width="130px">Fecha de solicitud / Date of application</td>
						            </tr>
						            <tr style="font-size: 12px;">
						              <td style="padding:10px;">
						              	'.$nombre.'
						              </td>
						              <td style="padding:10px;">
						                '.$abreviacion.'
						              </td>
						              <td style="padding:10px;">
						                '.$pais.'
						              </td>
						              <td style="padding:10px;">
						                '.$nombreOC.'
						              </td>
						              <td style="padding:10px;">
						              '.$fecha.'
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

        $mail->AddAddress($destinatario);

        //$mail->Username = "soporte@d-spp.org";
        //$mail->Password = "/aung5l6tZ";
        $mail->Subject = utf8_decode($asunto);
        $mail->Body = utf8_decode($cuerpo);
        $mail->MsgHTML(utf8_decode($cuerpo));
        $mail->Send();
        $mail->ClearAddresses();


        		$queryMensaje = "INSERT INTO mensajes(idcom, idoc, asunto, mensaje, destinatario, remitente, fecha) VALUES($idcom, $idoc, '$asunto', '$cuerpo', 'OC', 'COM', $fecha_actual)";
        		$ejecutar = mysql_query($queryMensaje,$dspp) or die(mysql_error());


	} //TERMINA INSERCION INDIVIDUAL DE SOLICITUD

    		//INSERTAMOS LOS DATOS DE CONTACTO DE LA SOLICITUD, DENTRO DE LOS CONTACTO DEL COM
    		$idcom = $_POST['idcom'];
    		if(!empty($_POST['p1_nombre'])){
    			$nombre = $_POST['p1_nombre'];
    			$cargo = $_POST['p1_cargo'];
    			$telefono = $_POST['p1_telefono'];
    			$email = $_POST['p1_telefono'];

    			$query = "INSERT INTO contacto(idcom, contacto, cargo, telefono1, email1) VALUES($idcom ,'$nombre', '$cargo', '$telefono', '$email')";
    			$ejecutar = mysql_query($query,$dspp) or die(mysql_error());
    		}
    		if(!empty($_POST['p2_nombre'])){
    			$nombre = $_POST['p2_nombre'];
    			$cargo = $_POST['p2_cargo'];
    			$telefono = $_POST['p2_telefono'];
    			$email = $_POST['p2_telefono'];

    			$query = "INSERT INTO contacto(idcom, contacto, cargo, telefono1, email1) VALUES($idcom ,'$nombre', '$cargo', '$telefono', '$email')";
    			$ejecutar = mysql_query($query,$dspp) or die(mysql_error());
    		}
    		if (!empty($_POST['adm1_nombre'])) {
    			$nombre = $_POST['adm1_nombre'];
    			$cargo = 'Administrador';
    			$telefono = $_POST['adm1_telefono'];
    			$email = $_POST['adm1_correo'];

    			$query = "INSERT INTO contacto(idcom, contacto, cargo, telefono1, email1) VALUES($idcom ,'$nombre', '$cargo', '$telefono', '$email')";
    			$ejecutar = mysql_query($query,$dspp) or die(mysql_error());
    		}
    		if (!empty($_POST['adm2_nombre'])) {
    			$nombre = $_POST['adm2_nombre'];
    			$cargo = 'Administrador';
    			$telefono = $_POST['adm2_telefono'];
    			$email = $_POST['adm2_correo'];

    			$query = "INSERT INTO contacto(idcom, contacto, cargo, telefono1, email1) VALUES($idcom ,'$nombre', '$cargo', '$telefono', '$email')";
    			$ejecutar = mysql_query($query,$dspp) or die(mysql_error());
    		}

			//actualizamos COM para cambiar el estado de su solicitud
			$procedimiento = $_POST['tipo_solicitud'];
			$query = "UPDATE com SET estado = $procedimiento WHERE idcom = $idcom";
			$actualizar = mysql_query($query,$dspp) or die(mysql_error());

			//llenamos el registro de la fecha para llevar un control de las acciones que se han realizado dentro del sistema
			$fecha = time();
			$idexterno = $_POST['idcom'];
			$identificador = "COM";
			$status = $_POST['tipo_solicitud'];

			$queryFecha = "INSERT INTO fecha(fecha, idexterno, identificador, status) VALUES($fecha, $idexterno, '$identificador', $status)";
			$ejecutar = mysql_query($queryFecha,$dspp) or die(mysql_error());

		    $mensaje = "Se ha enviado la Solicitud de Registro para Compradores, y otros Actores por parte de <b>$row_com[nombre]</b>";




  /*$insertGoTo = "main_menu.php?SOLICITUD&selectCOM&mensaje=Solicitud agregada correctamente, se ha notificado al OC por email.";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
	*/
  echo "<script>window.location='main_menu.php?SOLICITUD&selectCOM&mensaje=Solicitud agregada correctamente, se ha notificado al OC por email.'</script>"; 

} //TERMINA INSERCION DE SOLICITUD


?>

<br>
<script>
	
  //function validar(){
    /*valor = document.getElementById("cotizacion_com").value;
    if( valor == null || valor.length == 0 ) {
      alert("No se ha cargado la cotización de com");
      return false;
    }*/
    
    /*Procedimiento = document.getElementsByName("procedimiento");
     
    var seleccionado = false;
    for(var i=0; i<Procedimiento.length; i++) {    
      if(Procedimiento[i].checked) {
        seleccionado = true;
        break;
      }
    }
     
    if(!seleccionado) {
      alert("Debes de seleecionar un Tipo de Procedimiento");
      return false;
    }

    Procedimiento2 = document.getElementsByName("tipo_solicitud");
     
    var seleccionado2 = false;
    for(var i=0; i<Procedimiento2.length; i++) {    
      if(Procedimiento2[i].checked) {
        seleccionado2 = true;
        break;
      }
    }
     
    if(!seleccionad2) {
      alert("Debes de seleecionar el Tipo de Solicitud");
      return false;
    }
    return true
  }*/

</script>

<script>
	
  function validar(){
    /*valor = document.getElementById("cotizacion_opp").value;
    if( valor == null || valor.length == 0 ) {
      alert("No se ha cargado la cotización de OPP");
      return false;
    }*/
    
    Procedimiento = document.getElementsByName("procedimiento");
     
    var seleccionado = false;
    for(var i=0; i<Procedimiento.length; i++) {    
      if(Procedimiento[i].checked) {
        seleccionado = true;
        break;
      }
    }
     
    if(!seleccionado) {
      alert("Debes de seleecionar un Tipo de Procedimiento");
      return false;
    }

    Tipo_solicitud = document.getElementsByName("tipo_solicitud");
     
    var seleccionado = false;
    for(var i=0; i<Tipo_solicitud.length; i++) {    
      if(Tipo_solicitud[i].checked) {
        seleccionado = true;
        break;
      }
    }
     
    if(!seleccionado) {
      alert("Debes de seleecionar un Tipo de Solicitud");
      return false;
    }

    return true
  }

</script>

<form class="" method="post" name="form1" action="<?php echo $editFormAction; ?>" enctype="multipart/form-data">
 <table class="table table-bordered table-striped">
 	<thead>
 		<tr>
 			<th >
	      		<h5>Enviar a </h5><br>
	      		<div class="col-xs-12">
			      	<h4 class="alert alert-success"><?php echo $_SESSION['nombreOC']; ?></h4>
	      		</div>
 			</th>
 			<th>
 	  	    	<div class="col-xs-4">
	  	    		<div class="row">
						<h4>Tipo de Solicitud <small>(Selecciona el tipo de solicitud que deseas realizar)</small></h4>
	  	    		</div>
	  	    	</div>
	  	    	<div class="col-xs-4">
	  	    		<div class="row">
		  	    		<div class="col-xs-12">
		  	    			<p style="font-size:12px;"><b>Nueva Solicitud</b></p>	
		  	    		</div>	  	 
		  	    		<div class="col-xs-12">
		  	    			<input type="radio" data-on-color="success" data-off-color="danger" data-size="small" name="tipo_solicitud" value='1'>
	
		  	    		</div>		  	    		   			
	  	    		</div>
	  	    	</div>
	  	    	<div class="col-xs-4">
	  	    		<div class="row">
		  	    		<div class="col-xs-12">
		  	    			<p style="font-size:12px;"><b>Renovación de Certificación</b></p>	
		  	    		</div>
		  	    		<div class="col-xs-12">
		  	    			<input type="radio" data-on-color="success" data-off-color="danger" data-size="small" name="tipo_solicitud" value='20'>
	
		  	    		</div>		  	    		
	  	    		</div>
	  	    	</div>

 			</th>

 		</tr>
 		<tr>
 			<th colspan="2">
	  	    	<div class="col-xs-12">
	  	    		<div class="row">
						<h4 class="text-center">Procedimiento de Certificación <br><small>(realizado por OC)</small></h4>
	  	    		</div>
	  	    	</div>
	  	    	<div class="col-xs-3">
	  	    		<div class="row">
		  	    		<div class="col-xs-12">
		  	    			<p style="font-size:10px;"><b>DOCUMENTAL "ACORTADO"</b></p>	
		  	    		</div>	  	 
		  	    		<div class="col-xs-12">
		  	    			<input type="radio" data-on-color="success" data-off-color="danger" data-size="small" name="procedimiento" value='DOCUMENTAL "ACORTADO"'>
	
		  	    		</div>		  	    		   			
	  	    		</div>
	  	    	</div>
	  	    	<div class="col-xs-3">
	  	    		<div class="row">
		  	    		<div class="col-xs-12">
		  	    			<p style="font-size:10px;"><b>DOCUMENTAL "NORMAL"</b></p>	
		  	    		</div>
		  	    		<div class="col-xs-12">
		  	    			<input type="radio" data-on-color="success" data-off-color="danger" data-size="small" name="procedimiento" value='DOCUMENTAL "NORMAL"'>
	
		  	    		</div>		  	    		
	  	    		</div>
	  	    	</div>
	  	    	<div class="col-xs-3">
	  	    		<div class="row">
		  	    		<div class="col-xs-12">
		  	    			<p style="font-size:10px;"><b>COMPLETO "IN SITU"</b></p>	
		  	    		</div>
		  	    		<div class="col-xs-12">
		  	    			<input type="radio" data-on-color="success" data-off-color="danger" data-size="small" name="procedimiento" value='COMPLETO "IN SITU"'>
	
		  	    		</div>		  	    		
	  	    		</div>
	  	    	</div>
	  	    	<div class="col-xs-3">
	  	    		<div class="row">
		  	    		<div class="col-xs-12">
		  	    			<p style="font-size:10px;"><b>COMPLETO "A DISTANCIA"</b></p>	
		  	    		</div>
		  	    		<div class="col-xs-12">
		  	    			<input type="radio" data-on-color="success" data-off-color="danger" data-size="small" name="procedimiento" value='COMPLETO "A DISTANCIA"'>
	
		  	    		</div>		  	    		
	  	    		</div>
	  	    	</div>
	  	    	
	  	    	<!--<div class="col-xs-2"><button class="btn btn-danger">boton</button>DOCUMENTAL "ACORTADO"</div>
	  	    	<div class="col-xs-2"><button class="btn btn-danger">boton</button>DOCUMENTAL "NORMAL"</div>
	  	    	<div class="col-xs-2"><button class="btn btn-danger">boton</button>COMPLETO "IN SITU"</div>
	  	    	<div class="col-xs-2"><button class="btn btn-danger">boton</button>COMPLETO "A DISTANCIA"</div>-->
	  	    
 			</th>
 		</tr>
  		<tr>
 			<th class="text-center" colspan="2"><h3>Solicitud de Registro para Compradores y otros Actores</h3></th>
 		</tr>
 		<tr>
 			<th colspan="2" class="text-center alert alert-success">DATOS GENERALES</th>
 		</tr>
 	</thead>
 	<tbody>
		<tr>
			<td>
				<h4>Seleccione el COM que envia la solicitud</h4>
			</td>
			<!--<td colspan="2">
				<input type="text" autofocus="autofocus" class="form-control" id="exampleInputEmail1" size="70" placeholder="Nombre Organización" name="" value="<?php echo $row_com['nombre']?>" readonly>
			</td>-->
	      	<td colspan="2" class="alert alert-danger">

	      		<div class="col-xs-12">
			      	<select class="form-control" name="idcom" required>
			      		<!--<option class="form-control" value="9999">FUNDEPPO</option>-->
			      		<option class="form-control" value="">SELECCIONE UN COM</option>
			        <?php 
			        	$query_com = "SELECT * FROM com where idoc='".$_SESSION['idoc']."' ORDER BY nombre ASC";
			        	$consultacom = mysql_query($query_com,$dspp) or die(mysql_error());


						while ($com_OC = mysql_fetch_assoc($consultacom)){  
						?>
						        <option class="form-control" value="<?php echo $com_OC['idcom']?>"><?php echo $com_OC['abreviacion']?></option>
						        <?php
						}
					?>
			      	</select>		
	      		</div>
	  	    </td>
		</tr>
		<!--<tr>
			<td class="text-center" colspan="4">
				DATOS FISCALES(PARA FACTURACIÓN COMO DOMICILIO, RFC, RUC, CIUDAD, PAÍS, ETC)<br>
			</td>
		</tr>
		<tr>
			<td colspan=>
				TELEFONO<br>
				<?php 
					if(isset($row_contacto['telefono1'])){
						echo "<input type='text' class='form-control' name='telefono' id='exampleInputEmail1' value='$row_contacto[telefono1]' readonly>";
					}else{
						echo "<input type='text' class='form-control' name='telefono' id='exampleInputEmail1' placeholder='Telefono'>";
					}
				 ?>
				
			</td>
			<?php 
				/*if(isset($row_com['direccion_fiscal'])){
					echo "<td class='col-xs-3'>DOMICILIO: <input type='text' class='form-control' name='f_domicilio' id='exampleInputEmail1' value='$row_com[direccion_fiscal]' readonly></td>";
				}else{
					echo "<td class='col-xs-3'>DOMICILIO: <input type='text' class='form-control' name='f_domicilio' id='exampleInputEmail1' placeholder='Domicilio' readonly></td>";
				}*/
				if(isset($row_com['rfc'])){
					echo "<td class='col-xs-3'>RFC: <input type='text' class='form-control' name='f_rfc' id='exampleInputEmail1' value='$row_com[rfc]' readonly></td>";
				}else{
					echo "<td class='col-xs-3'>RFC: <input type='text' class='form-control' name='f_rfc' id='exampleInputEmail1' placeholder='RFC'></td>";
				}
			 ?>	
			<td class="col-xs-3">RUC: <input type="text" class="form-control" name="ruc" id="exampleInputEmail1" placeholder="RUC"></td>
			
			<td class="col-xs-3">CIUDAD: <input type="text" class="form-control" name="ciudad" id="exampleInputEmail1" placeholder="Ciudad"></td>
		</tr>-->

		<!--<tr>
			<td>
				<p>NOMBRE DE LA EMPRESA</p>
			</td>
			<td>
				<input type="text" class="form-control" value="<?php echo $row_com['nombre']?>" readonly>
			</td>
		</tr>
		<tr>
			<td>
				<p>DIRECCIÓN COMPLETA DE LAS OFICINAS CENTRALES (CALLE, BARRIO, LUGAR, REGIÓN)</p>
			</td>
			<td>
				<input type="text" class="form-control" value="<?php echo $row_com['direccion'];?>" placeholder="Dirección de las Oficinas" readonly>
			</td>
		</tr>
		<tr>
			<td>
				<p>CORREO ELECTRÓNICO</p> 
				<input type="text" class="form-control" value="<?php echo $row_com['email']?>" readonly>
			</td>
			<td>
				<p>TELÉFONOS (CÓDIGO DE PAÍS+CÓDIGO DE ÁREA+NÚMERO)</p>	
				<input type="text" class="form-control" value="<?php echo $row_com['telefono']?>" readonly>
			</td>
		</tr>
		<tr>
			<td>
				<p>PAÍS</p>
				<input type="text" class="form-control" value="<?php echo $row_com['pais']?>" readonly>
			</td>
			<td>
				<p>SITIO WEB</p>
				<input type="text" class="form-control" value="<?php echo $row_com['sitio_web']?>" readonly>
			</td>
		</tr>
		<tr>
			<td>
				<p>CIUDAD</p>
				<input type="text" class="form-control" value="<?php echo $row_com['ciudad']?>" readonly>
			</td>
			<td>
				<p>DOMICILIO FISCAL</p>
				<input type="text" class="form-control" value="<?php echo $row_com['direccion_fiscal']?>" readonly>
			</td>
		</tr>
		<tr>
			<td>
				<p>RUC</p>
				<input type="text" class="form-control" value="<?php echo $row_com['ruc']?>" readonly>
			</td>
			<td>
				<p>RFC</p>
				<input type="text" class="form-control" value="<?php echo $row_com['rfc']?>" readonly>
			</td>
		</tr>-->
		<tr>
			<td class="text-center">
				DATOS FISCALES(PARA FACTURACIÓN COMO DOMICILIO, RFC, RUC, CIUDAD, PAÍS, ETC)<br>
			</td>
		</tr>
		<tr>
			<td colspan="2">
			<div class="col-xs-3">
				TELEFONO<br>
				<?php 
						echo "<input type='text' class='form-control' name='telefono' id='exampleInputEmail1' placeholder='Telefono'>";
				 ?>

			</div>

			<?php 
				/*if(isset($row_com['direccion_fiscal'])){
					echo "<div class='col-xs-3'>DOMICILIO: <input type='text' class='form-control' name='f_domicilio' id='exampleInputEmail1' value='$row_com[direccion_fiscal]' readonly></div>";
				}else{
					echo "<div class='col-xs-3'>DOMICILIO: <input type='text' class='form-control' name='f_domicilio' id='exampleInputEmail1' placeholder='Domicilio' readonly></div>";
				}*/
				if(isset($row_com['rfc'])){
					echo "<div class='col-xs-3'>RFC: <input type='text' class='form-control' name='f_rfc' id='exampleInputEmail1' value='$row_com[rfc]' readonly></div>";
				}else{
					echo "<div class='col-xs-3'>RFC: <input type='text' class='form-control' name='f_rfc' id='exampleInputEmail1' placeholder='RFC'></div>";
				}
			 ?>	
			<div class="col-xs-3">RUC: <input type="text" class="form-control" name="ruc" id="exampleInputEmail1" placeholder="RUC"></div>
			
			<div class="col-xs-3">CIUDAD: <input type="text" class="form-control" name="ciudad" id="exampleInputEmail1" placeholder="Ciudad"></div>				
			</td>

		</tr>

		<!------------------------------------------ INICIA DATOS DE CONTACTO ---------------------------------------->
		<tr>
			<td colspan="2" class="text-center alert alert-warning"> CONTACTO </td>
		</tr>
		<tr>
			<td colspan="2">
				<p>PERSONA(S) DE CONTACTO SOLICITUD</p>
				<div class="col-xs-6">
					<input type="text" class="form-control" name="p1_nombre" placeholder="Nombre 1" required>
					<input type="email" class="form-control" name="p1_correo" placeholder="Correo Electronico 1" required>
				</div>
				<div class="col-xs-6">
					<input type="text" class="form-control" name="p1_cargo" placeholder="Cargo 1" required>
					<input type="text" class="form-control" name="p1_telefono" placeholder="Telefono 1" required>
				</div>
				<div class="col-xs-12"><br></div>
				<div class="col-xs-6">
					<input type="text" class="form-control" name="p2_nombre" placeholder="Nombre 2">
					<input type="email" class="form-control" name="p2_correo" placeholder="Correo Electronico 2">
				</div>
				<div class="col-xs-6">
					<input type="text" class="form-control" name="p2_cargo" placeholder="Cargo 2">
					<input type="text" class="form-control" name="p2_telefono" placeholder="Telefono 2">
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="text-center alert alert-warning">
				ÁREA ADMINISTRATIVA
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<p>PERSONA(S) DEL ÁREA ADMINISTRATIVA </p>
				<div class="col-xs-6">
					<input type="text" class="form-control" name="adm1_nombre" placeholder="Nombre 1" required>
					<input type="email" class="form-control" name="adm1_correo" placeholder="Correo Electronico 1" required>
				</div>
				<div class="col-xs-6">

					<input type="text" class="form-control" name="adm1_telefono" placeholder="Telefono 1" required>
				</div>
				<div class="col-xs-12"><br></div>
				<div class="col-xs-6">
					<input type="text" class="form-control" name="adm2_nombre" placeholder="Nombre 2">
					<input type="email" class="form-control" name="adm2_correo" placeholder="Correo Electronico 2">
				</div>
				<div class="col-xs-6">

					<input type="text" class="form-control" name="adm2_telefono" placeholder="Telefono 2">
				</div>
			</td>
		</tr>
		<!----------------------------------------------------------- INICIA DATOS DE OPERACION -------------------------------------------------------------------------->
		<tr class="text-center alert alert-success">
			<td colspan="2">DATOS DE OPERACIÓN</td>
		</tr>
		<tr>
			<td colspan="2">
				<p>SELECCIONE EL TIPO DE EMPRESA QUE ES. DE ACUERDO AL SISTEMA SPP LOS TIPOS  DE EMPRESA SON</p>
				<div class="col-xs-4">
					COMPRADOR FINAL <input class="form-control" name="tipo_empresa[]" type="checkbox" value="comprador_final">
				</div>
				<div class="col-xs-4">
					INTERMEDIARIO <input class="form-control" name="tipo_empresa[]" type="checkbox" value="intermediario">
				</div>
				<div class="col-xs-4">
					MAQUILADOR <input class="form-control" name="tipo_empresa[]" type="checkbox" value="maquilador">
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<p>1.- ¿CUÁLES SON LAS ORGANIZACIONES DE PEQUEÑOS PRODUCTORES A LAS QUE LES COMPRA O PRETENDE COMPRAR BAJO EL ESQUEMA DEL SÍMBOLO DE PEQUEÑOS PRODUCTORES?</p>
				<input type="text" class="form-control" name="resp1">
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<p>2.- ¿QUIÉN O QUIÉNES SON LOS PROPIETARIOS DE LA EMPRESA?</p>
				<input type="text" class="form-control" name="resp2">
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<p>3.- ESPECIFIQUE QUÉ PRODUCTO(S) QUIERE INCLUIR EN EL CERTIFICADO DEL SÍMBOLO DE PEQUEÑOS PRODUCTORES PARA LOS CUALES EL ORGNISMO DE CERTIFICACIÓN REALIZARÁ LA EVALUACIÓN.</p>
				<input type="text" class="form-control" name="resp3">
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<p>4.- SI SU EMPRESA ES UN COMPRADOR FINAL, MENCIONE SI QUIEREN INCLUIR ALGÚN CALIFICATIVO ADICIONAL PARA USO COMPLEMENTARIO CON EL DISEÑO GRÁFICO DEL SÍMBOLO DE PEQUEÑOS PRODUCTORES. <sup>4</sup> </p>
				<input type="text" class="form-control" name="resp4">
				<p><small><sup>4</sup>   Revisar el Reglamento Gráfico y la Lista de Calificativos Complementarios Opcionales vigentes</small></p>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<p>5.- SELECCIONE EL ALCANCE QUE TIENE LA EMPRESA</p>

				<div class="col-xs-4">
					PRODUCCIÓN <input class="form-control" name="resp5[]" type="checkbox" value="PRODUCCION">
				</div>
				<div class="col-xs-4">
					PROCESAMIENTO <input class="form-control" name="resp5[]" type="checkbox" value="PROCESAMIENTO">
				</div>
				<div class="col-xs-4">
					IMPORTACIÓN <input class="form-control" name="resp5[]" type="checkbox" value="IMPORTACION">
				</div>

				
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<p>6.	SELECCIONE SI SUBCONTRATA LOS SERVICIOS DE PLANTAS DE PROCESAMIENTO, EMPRESAS DE COMERCIALIZACIÓN O EMPRESAS QUE REALICEN LA IMPORTACIÓN O EXPORTACIÓN</p>
				<div class="col-xs-6">
					SI <input type="radio" class="form-control" name="resp6" onclick="mostrar_empresas()" id="resp6" value="SI">
				</div>
				<div class="col-xs-6">
					NO <input type="radio" class="form-control" name="resp6" onclick="ocultar_empresas()" id="resp6" value="NO">
				</div>


				<!--<input type="text" class="form-control" name="resp6">-->
			</td>
		</tr>
		<tr >
			<td colspan="2" >
				<p>SI LA RESPUESTA ES AFIRMATIVA, MENCIONE EL NOMBRE Y EL SERVICIO QUE REALIZA</p>
				<div id="contenedor_tablaEmpresas" class="col-xs-12" style="display:none">
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
							<td><input type="text" class="form-control" name="resp6_empresa[0]" id="exampleInputEmail1" placeholder="EMPRESA"></td>
							<td><input type="text" class="form-control" name="resp6_servicio[0]" id="exampleInputEmail1" placeholder="SERVICIO"></td>
						</tr>
					</table>	
				</div>		
			</td>
		</tr>


		<tr>
			<td colspan="2">
				<p>7.	SI SUBCONTRATA LOS SERVICIOS DE PLANTAS DE PROCESAMIENTO, EMPRESAS DE COMERCIALIZACIÓN O EMPRESAS QUE REALICEN LA IMPORTACIÓN O EXPORTACIÓN, INDIQUE SI ESTAS ESTAN REGISTRADAS O VAN A REALIZAR EL REGISTRO BAJO EL PROGRAMA DEL SPP O SERÁN CONTROLADAS A TRAVÉS DE SU EMPRESA. <sup>5</sup></p>
				<input type="text" class="form-control" name="resp7">
				<p><small><sup>5</sup> Revisar el documento de "Directrices Generales del Sistema SPP".</small></p>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<p>8.	ADICIONAL A SUS OFICINAS CENTRALES, ESPECIFIQUE CUÁNTOS CENTROS DE ACOPIO, AREAS DE      PROCESAMIENTO U OFICINAS ADICIONALES TIENE.</p>
				<input type="text" class="form-control" name="resp8">
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<p>9.	EN CASO DE TENER CENTROS DE ACOPIO, ÁREAS DE PROCESAMIENTO U OFICINAS ADICIONALES,  ANEXAR UN CROQUIS GENERAL MOSTRANDO SU UBICACIÓN</p>
				<input type="text" class="form-control" name="resp9">
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<p>10.	CUENTA CON UN SISTEMA DE CONTROL INTERNO PARA DAR CUMPLIMIENTO A LOS CRITERIOS DE LA NORMA GENERAL DEL SÍMBOLO DE PEQUEÑOS PRODUCTORES, EN SU CASO EXPLIQUE.</p>
				<input type="text" class="form-control" name="resp10">
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<p>11.	LLENAR LA TABLA DE ACUERDO A LAS CERTIFICACIONES QUE TIENE, (EJEMPLO: EU, NOP, JASS, FLO, etc)</p>
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
						<td>
							<div class="col-xs-6">SI<input type="radio" class="form-control" name="interrumpida[0]" value="SI"></div>
							<div class="col-xs-6">NO<input type="radio" class="form-control" name="interrumpida[0]" value="NO"></div>
						</td>
					</tr>

				</table>			
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<p>12.-	 DE LAS CERTIFICACIONES CON LAS QUE CUENTA, EN SU MÁS RECIENTE EVALUACIÓN INTERNA Y EXTERNA, ¿CUÁNTOS INCUMPLIMIENTOS SE IDENTIFICARON? Y EN SU CASO, ¿ESTÁN RESUELTOS O CUÁL ES SU ESTADO?</p>
				<input type="text" class="form-control" name="resp12">
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<p>13.-	DEL TOTAL DE SU COMERCIALIZACIÓN EL CICLO PASADO, ¿QUÉ PORCENTAJE FUERON REALIZADAS BAJO LOS ESQUEMAS CERTIFICADOS DE ORGÁNICO, COMERCIO JUSTO Y/O SÍMBOLO DE PEQUEÑOS PRODUCTORES? </p>
				<input type="text" class="form-control" name="resp13">
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<p>14.- TUVO COMPRAS SPP DURANTE EL CICLO DE REGISTRO ANTERIOR?</p>
				<div class="col-xs-6">
					SI <input type="radio" class="form-control" name="resp14" onclick="mostrar_ventas()" id="resp14" value="SI">
				</div>
				<div class="col-xs-6">
					NO <input type="radio" class="form-control" name="resp14" onclick="ocultar_ventas()" id="resp14" value="NO">
				</div>

	          <tr >
	            <td colspan="2">
	              15.- SI SU RESPUESTA FUE POSITIVA, FAVOR DE INIDICAR EL RANGO DEL VALOR TOTAL DE SUS VENTAS SPP DEL CICLO ANTERIOR DE ACUERDO A LA SIGUIENTE TABLA

	              <div class="well col-xs-12 " id="tablaVentas" style="display:none;">
	                
	                  <div class="col-xs-6"><p>Hasta $3,000 USD</p></div>
	                  <div class="col-xs-6 "><input type="radio" name="resp14_15" class="form-control" id="ver" onclick="ocultar()" value="HASTA $3,000 USD"></div>
	                
	                
	                  <div class="col-xs-6"><p>Entre $3,000 y $10,000 USD</p></div>
	                  <div class="col-xs-6"><input type="radio" name="resp14_15" class="form-control" id="ver" onclick="ocultar()" value="ENTRE $3,000 Y $10,000 USD"></div>
	                
	                
	                  <div class="col-xs-6"><p>Entre $10,000 a $25,000 USD</p></div>
	                  <div class="col-xs-6"><input type="radio" name="resp14_15" class="form-control"  id="ver" onclick="ocultar()" value="ENTRE $10,000 A $25,000 USD"></div>
	                
	                  <div class="col-xs-6"><p>Más de $25,000 USD <sup>*</sup><br><h6><sup>*</sup>Especifique la cantidad.</h6></p></div>
	                  <div class="col-xs-6"><input type="radio" name="resp14_15" class="form-control" id="exampleInputEmail1" onclick="mostrar()" value="mayor">
	                   <input type="text" name="resp14_15_1" class="form-control" id="oculto" style='display:none;' placeholder="Especifique la Cantidad">
	                  </div>
	                
	              </div>
	          
	            </td>
	          </tr>

			</td>
		</tr>
		<tr>
			<td colspan="2">
				<p>16.- FECHA ESTIMADA PARA COMENZAR A USAR EL SÍMBOLO DE PEQUEÑOS PRODUCTOR</p>
				<input type="text" class="form-control" name="resp16">
			</td>
		</tr>
		<tr class="text-center alert alert-success">
			<td colspan="2">
				DATOS DE PRODUCTOS PARA LOS CUALES SOLICITA UTILIZAR EL SÍMBOLO <sup>6</sup>
			</td>
		</tr>

		<tr>
			<td colspan="2">
				<table class="table table-bordered" id="tablaProductos">
					<tr>
						<td>Producto</td>
						<td>Volumen Total Estimado a Comercializar</td>
						<td>Volumen como Producto Terminado</td>
						<td>Volumen como Materia Prima</td>
						<td>País(es) de Origen (<small>Por favor separar con coma</small>)</td>
						<td>País(es) destino (<small>Por favor separar con coma</small>)</td>
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
							<input type="text" class="form-control" name="volumenEstimado[0]" id="exampleInputEmail1" placeholder="Volumen">
						</td>
						<td>
							<input type="text" class="form-control" name="volumenTerminado[0]" placeholder="Volumen">
						</td>
						<td>
							<input type="text" class="form-control" name="materia[0]" id="exampleInputEmail1" placeholder="Volumen">
						</td>
						<!--<td >
					        <select  class="form-control chosen-select-deselect" data-placeholder="Buscar por país" name="paisOrigen0[]" id="" multiple>
					          <option value="">Selecciona un país</option>
					          <?php 
					            $query = "SELECT * FROM paises";
					            $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
					            while($row_paises = mysql_fetch_assoc($ejecutar)){
					          ?>
					            <option value="<?php echo utf8_encode($row_paises['nombre']);?>"><?php echo utf8_encode($row_paises['nombre']) ?></option>
					          <?php
					            }
					          ?>
					        </select>
					        <input type="hidden" name="filtroPais" value="2">
						</td>-->
						<td>
							<textarea class="form-control" name="paisOrigen[0]" id="" cols="30" rows="3" placeholder="Pais de origen"></textarea>	
						</td>
						<td>	    
							<textarea class="form-control" name="paisDestino[0]" id="" cols="30" rows="3" placeholder="Pais de destino"></textarea>
						</td>
					</tr>				
					<tr>
						<td colspan="8">
							<h6><sup>6</sup> La información proporcionada en esta sección será tratada con plena confidencialidad. Favor de insertar filas adicionales de ser necesario.</h6>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr class="text-center alert alert-success">
			<td colspan="2">COMPROMISOS</td>
		</tr>
		<tr>
			<td colspan="2">
				<p>1.	Con el envío de esta solicitud se manifiesta el interés de recibir una propuesta de Registro.</p>
				<p>2.	El proceso de Registro comenzará en el momento que se confirme la recepción del pago correspondiente.</p>
				<p>3.	La entrega y recepción de esta solicitud no garantiza que el proceso de Registro será positivo.</p>
				<p>4.	Conocer y dar cumplimiento a todos los requisitos de la Norma General del Símbolo de Pequeños Productores que le apliquen como Compradores, Comercializadoras Colectiva de Organizaciones de Pequeños Productores, Intermediarios y Maquiladores, tanto Críticos como Mínimos, independientemente del tipo de evaluación que se realice.</p>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<p>Nombre de la persona que se responsabiliza de la veracidad de la información del formato y que le dará seguimiento a la solicitud de parte del Solicitante:</p>
				<input type="text" class="form-control" name="responsable" placeholder="Nombre de la persona" required>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<p>Nombre del personal del OC, que recibe la solicitud</p>
				<p class="alert alert-warning"><?php echo $_SESSION['nombreOC']; ?></p>
			</td>
		</tr>
		<tr>
			<td style="border:hidden">
				<div class="col-xs-12">
					<input type="hidden" name="MM_insert" value="form1">
					<input type="hidden" name="idoc" value="<?php echo $_SESSION['idoc']; ?>">
					<input type="hidden" name="fecha_elaboracion" value="<?php echo time()?>">
					<input type="hidden" name="status_publico" value="<?php echo $estadoPublico;?>">
					<input type="hidden" name="status_interno" value="<?php echo $estadoInterno;?>">
					<input type="hidden" name="mensaje" value="Acción agregada correctamente" />
					<!--<input type="hidden" name="idcom" value="<?php echo $_SESSION['idcom']?>">-->
					<input type="hidden" name="abreviacion" value="<?php echo $row_com['abreviacion'];?>">
					<!--<input type="text" name="nombreCOM" value="<?php echo $row_com['nombre']; ?>">
					<input type="text" name="paisCOM" value="<?php echo $row_com['pais']; ?>">-->
				</div>

			    <button style="width:200px;" class="btn btn-primary" type="submit" value="Enviar Solicitud" aria-label="Left Align" onclick="return validar()">
			      <span class="glyphicon glyphicon-open-file" aria-hidden="true"></span> Enviar
			    </button>

				<!--<input type="submit" class="btn btn-primary" style="width:200px" value="Enviar Solicitud">-->
			</td>
		</tr>
 	</tbody>
 </table>	
</form>



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

	  cell1.innerHTML = '<input type="text" class="form-control" name="certificadora['+contador+']" id="exampleInputEmail1" placeholder="CERTIFICACIÓN">';
	  cell2.innerHTML = '<input type="text" class="form-control" name="certificacion['+contador+']" id="exampleInputEmail1" placeholder="CERTIFICADORA">';
	  cell3.innerHTML = '<input type="text" class="form-control" name="ano_inicial['+contador+']" id="exampleInputEmail1" placeholder="AÑO INICIAL">';
	  cell4.innerHTML = '<div class="col-xs-6">SI<input type="radio" class="form-control" name="interrumpida['+contador+']" value="SI"></div><div class="col-xs-6">NO<input type="radio" class="form-control" name="interrumpida['+contador+']" value="NO"></div>';
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


	  cell1.innerHTML = '<input type="text" class="form-control" name="resp6_empresa['+contador+']" id="exampleInputEmail1" placeholder="EMPRESA">';
	  cell2.innerHTML = '<input type="text" class="form-control" name="resp6_servicio['+contador+']" id="exampleInputEmail1" placeholder="SERVICIO">';

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

	function mostrar_empresas(){
		document.getElementById('contenedor_tablaEmpresas').style.display = 'block';
	}
	function ocultar_empresas()
	{
		document.getElementById('contenedor_tablaEmpresas').style.display = 'none';
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
	  
	  cell2.innerHTML = '<input type="text" class="form-control" name="volumenEstimado['+cont+']" id="exampleInputEmail1" placeholder="Volumen">';
	  
	  cell3.innerHTML = '<input type="text" class="form-control" name="volumenTerminado['+cont+']" id="exampleInputEmail1" placeholder="Volumen">';
	  
	  cell4.innerHTML = '<input type="text" class="form-control" name="materia['+cont+']" id="exampleInputEmail1" placeholder="Volumen">';
	  
	  //cell4.innerHTML = '<input type="text" class="form-control" name="destino['+cont+']" id="exampleInputEmail1" placeholder="Destino">';
	  
	  //cell6.innerHTML = 'SI <input type="radio" name="marca_propia'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="marca_propia'+cont+'['+cont+']" id="" value="NO">';

	  //cell5.innerHTML = '<select  class="form-control chosen-select-deselect" data-placeholder="Buscar por país" name="paisOrigen0[]" id="" multiple><option value="">Selecciona un país</option></select>';
	  
	  cell5.innerHTML = '<textarea class="form-control" name="paisOrigen['+cont+']" id="" cols="30" rows="3" placeholder="Pais de origen"></textarea>';

	  cell6.innerHTML = '<textarea class="form-control" name="paisDestino['+cont+']" id="" cols="30" rows="3" placeholder="Pais de destino"></textarea>';


	  }

	}	

</script>


<?
mysql_free_result($pais);

mysql_free_result($oc);
?>