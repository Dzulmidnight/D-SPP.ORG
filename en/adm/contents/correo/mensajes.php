<?php 
//error_reporting(E_ALL ^ E_DEPRECATED);
mysql_select_db($database_dspp, $dspp);

if (!isset($_SESSION)) {
  session_start();
  
  $redireccion = "../index.php?ADM";

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

    $theValue = function_exissts("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

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
$rutaArchivo = "../../archivos/recursos/";

if(isset($_POST['enviar_prueba']) && $_POST['enviar_prueba'] == 3){

      $asunto = "D-SPP | Aviso Notificación de Intenciones de Certificación / Intentions Notification of certification";

    $cuerpo_mensaje = '
      <html>
        <head>
          <meta charset="utf-8">
          <style>
            table, td, th {    
                border: 1px solid #ddd;
                text-align: left;
            }

            table {
                border-collapse: collapse;
                width: 100%;
            }

            th, td {
                padding: 15px;
            }
            span{
              color: #34495e;
            }
          </style>
        </head>
        <body>
        
          <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="700px">
            <thead>
              <tr>
                <th>
                  <img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" />
                </th>
                <th style="text-align:left">
                  D-SPP | <span>Notificación de Intenciones de Certificación, Registro y Autorización</span> / <i>Notification of Certification, Registration and Authorization Intents</i>
                </th>
              </tr>
            </thead>
            <tbody>

              <tr style="width:100%">
                <td colspan="2">
                  <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;" border="1" width="650px">

                    <tr style="font-size: 12px; text-align:center; background-color:#dff0d8; color:#3c763d;" height="50px;">
                      <td >Tipo / Type</td>
                      <td >Nombre de la organización / <i>Organization name</i></td>
                      <td >Abreviación / <i>Short name</i></td>
                      <td >País / <i>Country</i></td>
                      <td >Organismo de Certificación / <i>Certification Entity</i></td>
                      <td >Alcance / <i>Scope</i></td>
                      <td >Países en los que ofrecerá servicio / <i>Countries   in which it will offer its services</i></td>
                      <td >Fecha de solicitud / <i>Date of application</i></td>
                      <td >Fin período de objeción / <i>Objection period end</i></td>
                    </tr>
                    <tr style="font-size:12px">
                      <td ><span>OC</span> / <i>CE</i></td>
                      <td ><span>Certification of Environmental Standards GmbH</span></td>
                      <td><span>CERES</span></td>
                      <td><span>Alemania</span> / <i>Germany</i></td>
                      <td><span>SPP GLOBAL</span></td>
                      
                      <td><span>Certificación de OPP y Registro de empresas</span> / <i>Certification of SPO and Registration of companies</i></td>
                      <td><span>A nivel mundial</span> / <i>Worldwide</i></td>
                      <td><span>31/01/2018</span></td>
                      <td><span>17/02/2018</span></td>
                    </tr>
                </td>
                </table>
              </tr>
          <tr>
            <td style="text-align:justify;" colspan="2">
              <span>
              SPP GLOBAL publica y notifica las "Intenciones de Certificación, Registro o Autorización" basada en nuevas solicitudes de: 1) Certificación de Organizaciones de Pequeños Productores, 2) Registro de Compradores y otros actores y 3) Autorización de Organismos de Certificación, con el objetivo de informarles y recibir las eventuales objeciones contra la incorporación de los solicitantes.
              Estas eventuales objeciones presentadas deben estar sustentadas con información concreta y verificable con respecto a incumplimientos de la Normatividad del SPP y/o nuestro Código de Conducta (disponibles en <a href="http://www.spp.coop/"><strong>www.spp.coop</strong></a>, en el área de Funcionamiento). Las objeciones presentadas y enviadas a <a href="cert@spp.coop"><strong>cert@spp.coop</strong></a> serán tomadas en cuenta en los procesos de certificación, registro o autorización.
              Estas notificaciones son enviadas por SPP GLOBAL en un lapso menor a 24 horas a partir del momento en que le llegue la solicitud correspondiente. Si se presentan objeciones antes de que el solicitante se Certifique, Registre o Autorice su tratamiento por parte del Organismo de Certificación debe ser parte de la misma evaluación documental. Si la objeción se presenta cuando el Solicitante ya esta Certificado se aplica el Procedimiento de Inconformidades del Símbolo de Pequeños Productores. Las nuevas intenciones de Certificación, Registro o Autorización, se detallan al inicio de este documento.
              </span>
              <br><br>
              <i>
                SPP GLOBAL publishes and notifies the "Certification, Registration and Authorization Intentions" based on new applications submitted for: 1) Certification of Small Producers\' Organizations, 2) Registration of Buyers and other stakeholders, and 3) Authorization of Certification Entities, with the objective of keeping you informed and receiving any objections to the incorporation of any new applicants into the system.
                Any objections submitted must be supported with concrete, verifiable information regarding non-compliance with the Standards and/or Code of Conduct of the Small Producers\' Symbol (available at <a href="http://www.spp.coop/"><strong>www.spp.coop</strong></a> in the section on Operation). The objections submitted and sent to <a href="cert@spp.coop"><strong>cert@spp.coop</strong></a> will be taken into consideration during certification, registration and authorization processes.
                These notifications are sent by SPP GLOBAL in a period of less than 24 hours from the time a corresponding application is received. If objections are presented before getting the Certification, Registration or Authorization, the Certification Entity must incorporate them as part of the same evaluation-process. If the objection is presented when the applicant has already been certified, the SPP Dissents Procedure has to be applied. The new intentions for Certification, Registration and Authorization are detailed at the beginning (of this document).
              </i>
            </td>
          </tr>
            </tbody>
          </table>

        </body>
      </html>
    ';
      /*if(isset($correos_oc['email1'])){
        $token = strtok($correos_oc['email1'], "\/\,\;");
        while ($token !== false)
        {
          $mail->AddCC($token);
          $token = strtok('\/\,\;');
        }
      }
      if(isset($correos_oc['email2'])){
        $token = strtok($correos_oc['email2'], "\/\,\;");
        while ($token !== false)
        {
          $mail->AddCC($token);
          $token = strtok('\/\,\;');
        }
      }*/
      $mail->AddCC('jaime.picado@biolatina.com');
      $mail->AddCC('roxana.laynes@biolatina.com ');
      $mail->Subject = utf8_decode($asunto);
      $mail->Body = utf8_decode($cuerpo_mensaje);
      $mail->MsgHTML(utf8_decode($cuerpo_mensaje));
      $mail->Send();
      $mail->ClearAddresses();
      $mail->ClearAttachments();

      echo "<script>alert('SE ENVIO EL CORREO')</script>";
}

if(isset($_POST['enviar_correo']) && $_POST['enviar_correo'] == 1){
  $administrador = 'yasser.midnight@gmail.com';

    if(!empty($_FILES['archivo1']['name'])){
        $_FILES["archivo1"]["name"];
          move_uploaded_file($_FILES["archivo1"]["tmp_name"], $rutaArchivo.$_FILES["archivo1"]["name"]);
          $archivo1 = $rutaArchivo.basename($_FILES["archivo1"]["name"]);
          //$mail->AddAttachment($archivo1);
    }
    if(!empty($_FILES['archivo2']['name'])){
        $_FILES["archivo2"]["name"];
          move_uploaded_file($_FILES["archivo2"]["tmp_name"], $rutaArchivo.$_FILES["archivo2"]["name"]);
          $archivo2 = $rutaArchivo.basename($_FILES["archivo2"]["name"]);
          //$mail->AddAttachment($archivo2);
    }

    $asunto = "D-SPP | Aviso Notificación de Intenciones de Certificación / Intentions Notification of certification";

    $cuerpo_mensaje = '
      <html>
        <head>
          <meta charset="utf-8">
          <style>
            table, td, th {    
                border: 1px solid #ddd;
                text-align: left;
            }

            table {
                border-collapse: collapse;
                width: 100%;
            }

            th, td {
                padding: 15px;
            }
            span{
              color: #34495e;
            }
          </style>
        </head>
        <body>
        
          <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="700px">
            <thead>
              <tr>
                <th>
                  <img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" />
                </th>
                <th style="text-align:left">
                  D-SPP | <span>Notificación de Intenciones de Certificación, Registro y Autorización</span> / <i>Notification of Certification, Registration and Authorization Intents</i>
                </th>
              </tr>
            </thead>
            <tbody>

              <tr style="width:100%">
                <td colspan="2">
                  <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;" border="1" width="650px">

                    <tr style="font-size: 12px; text-align:center; background-color:#dff0d8; color:#3c763d;" height="50px;">
                      <td >Tipo / Type</td>
                      <td >Nombre de la organización / <i>Organization name</i></td>
                      <td >Abreviación / <i>Short name</i></td>
                      <td >País / <i>Country</i></td>
                      <td >Organismo de Certificación / <i>Certification Entity</i></td>
                      <td >Alcance / <i>Scope</i></td>
                      <td >Países en los que ofrecerá servicio / <i>Countries   in which it will offer its services</i></td>
                      <td >Fecha de solicitud / <i>Date of application</i></td>
                      <td >Fin período de objeción / <i>Objection period end</i></td>
                    </tr>
                    <tr style="font-size:12px">
                      <td ><span>OC</span> / <i>CE</i></td>
                      <td ><span>Certification of Environmental Standards GmbH</span></td>
                      <td><span>CERES</span></td>
                      <td><span>Alemania</span> / <i>Germany</i></td>
                      <td><span>SPP GLOBAL</span></td>
                      
                      <td><span>Certificación de OPP y Registro de empresas</span> / <i>Certification of SPO and Registration of companies</i></td>
                      <td><span>A nivel mundial</span> / <i>Worldwide</i></td>
                      <td><span>31/01/2018</span></td>
                      <td><span>17/02/2018</span></td>
                    </tr>
                </td>
                </table>
              </tr>
          <tr>
            <td style="text-align:justify;" colspan="2">
              <span>
              SPP GLOBAL publica y notifica las "Intenciones de Certificación, Registro o Autorización" basada en nuevas solicitudes de: 1) Certificación de Organizaciones de Pequeños Productores, 2) Registro de Compradores y otros actores y 3) Autorización de Organismos de Certificación, con el objetivo de informarles y recibir las eventuales objeciones contra la incorporación de los solicitantes.
              Estas eventuales objeciones presentadas deben estar sustentadas con información concreta y verificable con respecto a incumplimientos de la Normatividad del SPP y/o nuestro Código de Conducta (disponibles en <a href="http://www.spp.coop/"><strong>www.spp.coop</strong></a>, en el área de Funcionamiento). Las objeciones presentadas y enviadas a <a href="cert@spp.coop"><strong>cert@spp.coop</strong></a> serán tomadas en cuenta en los procesos de certificación, registro o autorización.
              Estas notificaciones son enviadas por SPP GLOBAL en un lapso menor a 24 horas a partir del momento en que le llegue la solicitud correspondiente. Si se presentan objeciones antes de que el solicitante se Certifique, Registre o Autorice su tratamiento por parte del Organismo de Certificación debe ser parte de la misma evaluación documental. Si la objeción se presenta cuando el Solicitante ya esta Certificado se aplica el Procedimiento de Inconformidades del Símbolo de Pequeños Productores. Las nuevas intenciones de Certificación, Registro o Autorización, se detallan al inicio de este documento.
              </span>
              <br><br>
              <i>
                SPP GLOBAL publishes and notifies the "Certification, Registration and Authorization Intentions" based on new applications submitted for: 1) Certification of Small Producers\' Organizations, 2) Registration of Buyers and other stakeholders, and 3) Authorization of Certification Entities, with the objective of keeping you informed and receiving any objections to the incorporation of any new applicants into the system.
                Any objections submitted must be supported with concrete, verifiable information regarding non-compliance with the Standards and/or Code of Conduct of the Small Producers\' Symbol (available at <a href="http://www.spp.coop/"><strong>www.spp.coop</strong></a> in the section on Operation). The objections submitted and sent to <a href="cert@spp.coop"><strong>cert@spp.coop</strong></a> will be taken into consideration during certification, registration and authorization processes.
                These notifications are sent by SPP GLOBAL in a period of less than 24 hours from the time a corresponding application is received. If objections are presented before getting the Certification, Registration or Authorization, the Certification Entity must incorporate them as part of the same evaluation-process. If the objection is presented when the applicant has already been certified, the SPP Dissents Procedure has to be applied. The new intentions for Certification, Registration and Authorization are detailed at the beginning (of this document).
              </i>
            </td>
          </tr>
            </tbody>
          </table>

        </body>
      </html>
    ';


  foreach ($_POST['lista_contactos'] as $value) {

    if($value == 'opp'){ // SE ENVIAN LOS CORREOS A LA OPP
      $row_opp = mysql_query("SELECT email FROM opp", $dspp);
      while($opp = mysql_fetch_assoc($row_opp)){
        if(!empty($opp['email'])){
          //$mail->AddAddress($email_opp['email']);
          $token = strtok($opp['email'], "\/\,\;");
          while ($token !== false)
          {
            $mail->AddBCC($token);
            $token = strtok('\/\,\;');
          }

        }
      }
      if(isset($archivo1)){
         $mail->AddAttachment($archivo1);
      }
      if(isset($archivo2)){
         $mail->AddAttachment($archivo2);
      }
        $mail->AddBCC($administrador);
        $mail->Subject = utf8_decode($asunto);
        $mail->Body = utf8_decode($cuerpo_mensaje);
        $mail->MsgHTML(utf8_decode($cuerpo_mensaje));
        $mail->Send();
        $mail->clearAttachments();
        $mail->ClearAddresses();

      echo "<script>alert('OPP');</script>";
    }
    if($value == 'oc'){ // SE ENVIAN LOS CORREOS A LA OPP
      $row_oc = mysql_query("SELECT email1, email2 FROM oc", $dspp);
      while($oc = mysql_fetch_assoc($row_oc)){
        if(!empty($oc['email1'])){
          //$mail->AddAddress($email_oc['email']);
          $token = strtok($oc['email1'], "\/\,\;");
          while ($token !== false)
          {
            $mail->AddBCC($token);
            $token = strtok('\/\,\;');
          }
        }

        if(!empty($oc['email2'])){
          //$mail->AddAddress($email_oc['email']);
          $token = strtok($oc['email2'], "\/\,\;");
          while ($token !== false)
          {
            $mail->AddBCC($token);
            $token = strtok('\/\,\;');
          }
        }

      }
      if(isset($archivo1)){
         $mail->AddAttachment($archivo1);
      }
      if(isset($archivo2)){
         $mail->AddAttachment($archivo2);
      }
        $mail->AddBCC($administrador);
        $mail->Subject = utf8_decode($asunto);
        $mail->Body = utf8_decode($cuerpo_mensaje);
        $mail->MsgHTML(utf8_decode($cuerpo_mensaje));
        $mail->Send();
        $mail->clearAttachments();
        $mail->ClearAddresses();

      echo "<script>alert('OC');</script>";
    }

     if($value == 'contactos'){
      $row_contactos = mysql_query("SELECT email1 FROM contactos GROUP BY email1", $dspp);
      while($contactos = mysql_fetch_assoc($row_contactos)){
        if(!empty($contactos['email1'])){
          //$mail->AddAddress($email1_contactos['email1']);
          $token = strtok($contactos['email1'], "\/\,\;");
          while ($token !== false)
          {
            $mail->AddBCC($token);
            $token = strtok('\/\,\;');
          }

        }
      }
      if(isset($archivo1)){
         $mail->AddAttachment($archivo1);
      }
      if(isset($archivo2)){
         $mail->AddAttachment($archivo2);
      }
        $mail->AddBCC($administrador);
        $mail->Subject = utf8_decode($asunto);
        $mail->Body = utf8_decode($cuerpo_mensaje);
        $mail->MsgHTML(utf8_decode($cuerpo_mensaje));
        $mail->Send();
        $mail->clearAttachments();
        $mail->ClearAddresses();

      echo "<script>alert('LISTA DE OPP');</script>";  
    }
     if($value == 'empresas'){ /// SE ENVIAN LOS CORREOS A LAS EMPRESAS
      $row_empresa = mysql_query("SELECT email FROM empresa", $dspp);
      while($empresa = mysql_fetch_assoc($row_empresa)){
        if(!empty($empresa['email'])){
          //$mail->AddAddress($email_empresa['email']);
          $token = strtok($empresa['email'], "\/\,\;");
          while ($token !== false)
          {
            $mail->AddBCC($token);
            $token = strtok('\/\,\;');
          }

        }
      }
      if(isset($archivo1)){
         $mail->AddAttachment($archivo1);
      }
      if(isset($archivo2)){
         $mail->AddAttachment($archivo2);
      }
        $mail->AddBCC($administrador);

        $mail->Subject = utf8_decode($asunto);
        $mail->Body = utf8_decode($cuerpo_mensaje);
        $mail->MsgHTML(utf8_decode($cuerpo_mensaje));
        $mail->Send();
        $mail->clearAttachments();
        $mail->ClearAddresses();
      echo "<script>alert('EMPRESAS');</script>";
    }
     if($value == 'administradores'){
      $row_adm = mysql_query("SELECT email FROM adm", $dspp);
      while($adm = mysql_fetch_assoc($row_adm)){
        if(!empty($adm['email'])){
          //$mail->AddAddress($email_adm['email']);
          $token = strtok($adm['email'], "\/\,\;");
          while ($token !== false)
          {
            $mail->AddAddress($token);
            $token = strtok('\/\,\;');
          }

        }
      }
      $mail->AddBCC('p.lacroix@tero.coop');
      if(isset($archivo1)){
         $mail->AddAttachment($archivo1);
      }
      if(isset($archivo2)){
         $mail->AddAttachment($archivo2);
      }

        $mail->AddBCC($administrador);
        //$mail->AddAddress('cert@spp.coop');

        //$mail->AddAddress('dspporg@d-spp.org');
        $mail->Subject = utf8_decode($asunto);
        $mail->Body = utf8_decode($cuerpo_mensaje);
        $mail->MsgHTML(utf8_decode($cuerpo_mensaje));
        $mail->Send();
        $mail->clearAttachments();
        $mail->ClearAddresses();
      echo "<script>alert('ADMINISTRADORES');</script>";
    }
  }
  /*unlink($archivo1);
  unlink($archivo2);*/
}
 ?>
<form action="" method="POST" enctype="multipart/form-data">

	<div class="col-md-2 panel panel-primary">
		<div class="panel-heading">Seleccione la lista de contactos a los que desea enviar la notificación.</div>
		<div class="panel-body">
			<div class="checkbox" >
				<label>
					<input type="checkbox" name="lista_contactos[]" value="todos" > Todos
				</label>
			</div>
			<div class="checkbox" >
				<label>
					<input type="checkbox" name="lista_contactos[]" value="opp" > OPP(s)
				</label>
			</div>
      <div class="checkbox" >
        <label>
          <input type="checkbox" name="lista_contactos[]" value="contactos" > Contacto(s)
        </label>
      </div>

			<div class="checkbox" >
				<label>
					<input type="checkbox" name="lista_contactos[]" value="empresas" > Empresas
				</label>
			</div>
      <div class="checkbox" >
        <label>
          <input type="checkbox" name="lista_contactos[]" value="oc" > OC
        </label>
      </div>

			<div class="checkbox" >
				<label>
					<input type="checkbox" name="lista_contactos[]" value="administradores" > Administradores
				</label>
			</div>
		</div>
	</div>

	<div class="col-md-10">
    <input type="text" name="asunto" class="form-control" placeholder="Asunto del Correo">
		<textarea class="editor_texto" name="contenido" id="" cols="30" rows="10"></textarea>

    Archivo 1<input type="file" name="archivo1" value="">
    Archivo 2<input type="file" name="archivo2" value="">
	</div>

	<div class="col-xs-12" style="margin-top:10px;">
		<input type="submit" class="btn btn-success"> 
		<input type="hidden" name="enviar_correo" value="1">
	</div>

</form>

<form action="" method="POST">
  <p>Correo particular</p>
  <input type="text" name="enviar_prueba" value="3">
  <input type="submit" value="enviar">
</form>