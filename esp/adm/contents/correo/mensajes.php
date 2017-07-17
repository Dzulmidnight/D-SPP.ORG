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

      $asunto = "D-SPP | Formatos de Evaluación";

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
                <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">Cargar Formato, Dictamen e Informe de Evaluación</span></p></th>

              </tr>
              <tr>
               <th scope="col" align="left" width="280"><p>OPP: <span style="color:red">Cooperativa Muitiservicios de Productores Cañeros de Cuidad Antigua R.L. COOPROCA R.L.</span></p></th>
              </tr>

              <tr>
                <td colspan="2">
                 <p>SPP GLOBLA notifica que la OPP: Cooperativa Muitiservicios de Productores Cañeros de Cuidad Antigua R.L. COOPROCA R.L. , ha cumplido con la documentación necesaria.</p>
                 <p>
                  Por favor procedan a ingresar en su cuenta de OC dentro del sistema D-SPP para poder cargar los siguientes documentos: 
                     <ul style="color:red">
                       <li>Formato de Evaluación</li>
                       <li>Informe de Evaluación</li>
                       <li>Dictamen de Evaluación</li>
                     </ul>

                 </p>
                </td>
              </tr>
              <tr>
                <td colspan="2">
                  ¿Pasos para cargar la documentación?. Para poder cargar la documentación debe seguir los siguiente pasos:
                  <ol>
                    <li>Dar clic en la opción "SOLICITUDES"</li>
                    <li>Seleccionar "Solicitudes OPP"</li>
                    <li>Posicionarse en la columna "Certificado" y dar clic en el boton "Cargar Certificado"</li>
                    <li>Se desplegara una ventan donde podra cargar la documentación</li>
                  </ol>
                  <p style="color:red">
                    Se notificara una vez que sea aprobada la documentación para poder cargar el certificado.
                  </p>
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

    $asunto = $_POST['asunto'];
    $contenido = $_POST['contenido'];

    $cuerpo_mensaje = '
      <html>
        <head>
          <meta charset="utf-8">
        </head>
        <body>
    
          <table style="font-family: Tahoma, Geneva, sans-serif;"  border="0" width="650px">
            <thead>
              <tr>
                <th scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                <th scope="col" align="left" width="280"><strong>Asunto: <span style="color:red">'.$asunto.'</span> </strong></th>
              </tr>
            </thead>
            <tbody>
              <tr style="padding-top:20px;">
                <td colspan="2">'.$contenido.'</td>
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

        $mail->AddAddress('dspporg@d-spp.org');
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

<!--<form action="" method="POST">
  <p>Correo particular</p>
  <input type="text" name="enviar_prueba" value="3">
  <input type="submit" value="enviar">
</form>-->