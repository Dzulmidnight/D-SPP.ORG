<?php require_once('../Connections/dspp.php'); ?>
<?php 
      include_once("../../PHPMailer/class.phpmailer.php");
      include_once("../../PHPMailer/class.smtp.php");

  $rutaArchivo = "../formatos/anexos/";

if (isset($_POST['correo1']) && $_POST['correo1'] == 1) {

/*    //AL NOMBRE DEL ARCHIVO(FORMATO) LE CONCATENO EL TIME
    if(!empty($_FILES['archivoExtra']['name'])){
        $_FILES["archivoExtra"]["name"];
          move_uploaded_file($_FILES["archivoExtra"]["tmp_name"], $rutaArchivo.time()."_".$_FILES["archivoExtra"]["name"]);
          $archivoExtra = $rutaArchivo.basename(time()."_".$_FILES["archivoExtra"]["name"]);
    }else{
      $archivoExtra = NULL;
    }

*/

      //AL NOMBRE DEL ARCHIVO(FORMATO) LE CONCATENO EL TIME
       /* $rutaArchivo = "formatos/anexos/";
        if(!empty($_FILES['archivoAdjunto']['name'])){
            $_FILES["archivoAdjunto"]["name"];
              move_uploaded_file($_FILES["archivoAdjunto"]["tmp_name"], $rutaArchivo.time()."_".$_FILES["archivoAdjunto"]["name"]);
              $archivoAdjunto = $rutaArchivo.basename(time()."_".$_FILES["archivoAdjunto"]["name"]);
        }else{
          $archivoAdjunto = NULL;
        }*/

        $mail = new PHPMailer();
        $mail->IsSMTP();
        //$mail->SMTPSecure = "ssl";
        $mail->Host = "mail.d-spp.org";
        $mail->Port = 587;
        $mail->SMTPAuth = true;
        $mail->Username = "soporte@d-spp.org";
        $mail->Password = "/aung5l6tZ";
        $mail->SMTPDebug = 1;

        $mensajeDefault = $_POST['mensaje'];
        $asunto = $_POST['asunto'];

    if(!empty($_FILES['archivo']['name'])){
        $_FILES["archivo"]["name"];
          move_uploaded_file($_FILES["archivo"]["tmp_name"], $rutaArchivo.$_FILES["archivo"]["name"]);
          $archivo = $rutaArchivo.basename($_FILES["archivo"]["name"]);
    }



/**************************************** INICIA ENVIO EMAIL OC  **************************************************************/

        $queryOC = "SELECT email FROM oc WHERE email !=''";
        $oc = mysql_query($queryOC,$dspp) or die(mysql_error());
        $destinatarioOC = "";


        while($row_oc = mysql_fetch_assoc($oc)){
          $mail->AddAddress($row_oc['email']);
        }

          $mail->From = "soporte@d-spp.org";
          $mail->FromName = "CERT - DSPP";
          $mail->addReplyTo('cert@spp.coop', 'Certificacion');
          $mail->AddBCC("yasser.midnight@gmail.com", "correo Oculto");
          $mail->AddAttachment($archivo);
          //$mail->AddAddress($$row_oc['email']);

          $mensaje = '
            <html>
            <head>
              <meta charset="utf-8">
            </head>
            <body>
              <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
                <tbody>
                  <tr>
                    <th rowspan="1" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                    <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">'.$asunto.'</span></p></th>

                  </tr>


                  <tr>
                    <td style="padding-top:20px;" colspan="2">
                      '.$mensajeDefault.'
                    </td>
                  </tr>
                </tbody>
              </table>
            </body>
            </html>
          ';

          $mail->Subject = utf8_decode($asunto);
          $mail->Body = utf8_decode($mensaje);
          $mail->MsgHTML(utf8_decode($mensaje));
          // Enviamos el Mensaje 
          $mail->send(); 
       
          // Borramos el destinatario, de esta forma nuestros clientes no ven los correos de las otras personas y parece que fuera un único correo para ellos. 
          $mail->ClearAddresses(); 


/**************************************** TERMINA ENVIO EMAIL OC  **************************************************************/


/**************************************** INICIA ENVIO EMAIL OPP  **************************************************************/

        $queryOPP = "SELECT email FROM opp WHERE email !=''";
        $opp = mysql_query($queryOPP,$dspp) or die(mysql_error());
        $destinatarioOPP = "";
        while($row_opp = mysql_fetch_assoc($opp)){
          $mail->AddAddress($row_opp['email']);
        }

          $mail->From = "soporte@d-spp.org";
          $mail->FromName = "CERT - DSPP";
          $mail->addReplyTo('cert@spp.coop', 'Certificacion');
          $mail->AddBCC("yasser.midnight@gmail.com", "correo Oculto");
          $mail->AddAttachment($archivo);


          $mensaje = '
            <html>
            <head>
              <meta charset="utf-8">
            </head>
            <body>
              <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
                <tbody>
                  <tr>
                    <th rowspan="1" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                    <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">'.$asunto.'</span></p></th>

                  </tr>


                  <tr>
                    <td style="padding-top:20px;" colspan="2">
                      '.$mensajeDefault.'
                    </td>
                  </tr>
                </tbody>
              </table>
            </body>
            </html>
          ';

          $mail->Subject = utf8_decode($asunto);
          $mail->Body = utf8_decode($mensaje);
          $mail->MsgHTML(utf8_decode($mensaje));
          // Enviamos el Mensaje 
          $mail->send(); 
       
          // Borramos el destinatario, de esta forma nuestros clientes no ven los correos de las otras personas y parece que fuera un único correo para ellos. 
          $mail->ClearAddresses(); 


/**************************************** TERMINA ENVIO EMAIL OPP  **************************************************************/

/**************************************** INICIA ENVIO EMAIL COM  **************************************************************/

         $queryCOM = "SELECT email FROM com WHERE email !=''";
        $com = mysql_query($queryCOM,$dspp) or die(mysql_error());
        $destinatarioCOM = "";
        while($row_com = mysql_fetch_assoc($com)){
          $mail->AddAddress($row_com['email']);
        }
          $mail->From = "soporte@d-spp.org";
          $mail->FromName = "CERT - DSPP";
          $mail->addReplyTo('cert@spp.coop', 'Certificacion');
          $mail->AddBCC("yasser.midnight@gmail.com", "correo Oculto");
          $mail->AddAttachment($archivo);



          $mensaje = '
            <html>
            <head>
              <meta charset="utf-8">
            </head>
            <body>
              <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
                <tbody>
                  <tr>
                    <th rowspan="1" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                    <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">'.$asunto.'</span></p></th>

                  </tr>


                  <tr>
                    <td style="padding-top:20px;" colspan="2">
                      '.$mensajeDefault.'
                    </td>
                  </tr>
                </tbody>
              </table>
            </body>
            </html>
          ';

          $mail->Subject = utf8_decode($asunto);
          $mail->Body = utf8_decode($mensaje);
          $mail->MsgHTML(utf8_decode($mensaje));
          // Enviamos el Mensaje 
          $mail->send(); 
       
          // Borramos el destinatario, de esta forma nuestros clientes no ven los correos de las otras personas y parece que fuera un único correo para ellos. 
          $mail->ClearAddresses(); 

/**************************************** TERMINA ENVIO EMAIL COM  **************************************************************/

/**************************************** INICIA ENVIO EMAIL ADM  **************************************************************/

         $queryADM = "SELECT email FROM adm WHERE email !=''";
        $adm = mysql_query($queryADM,$dspp) or die(mysql_error());
        $destinatarioADM = "";
        while($row_adm = mysql_fetch_assoc($adm)){
          $mail->AddAddress($row_adm['email']);
        } 
          $mail->From = "soporte@d-spp.org";
          $mail->FromName = "CERT - DSPP";
          $mail->addReplyTo('cert@spp.coop', 'Certificacion');
          $mail->AddBCC("yasser.midnight@gmail.com", "correo Oculto");
          $mail->AddAttachment($archivo);


          $mensaje = '
            <html>
            <head>
              <meta charset="utf-8">
            </head>
            <body>
              <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
                <tbody>
                  <tr>
                    <th rowspan="1" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                    <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">'.$asunto.'</span></p></th>

                  </tr>


                  <tr>
                    <td style="padding-top:20px;" colspan="2">
                      '.$mensajeDefault.'
                    </td>
                  </tr>
                </tbody>
              </table>
            </body>
            </html>
          ';

            $mail->Subject = utf8_decode($asunto);
            $mail->Body = utf8_decode($mensaje);
            $mail->MsgHTML(utf8_decode($mensaje));
            // Enviamos el Mensaje 
            $mail->send(); 
         
            // Borramos el destinatario, de esta forma nuestros clientes no ven los correos de las otras personas y parece que fuera un único correo para ellos. 
            $mail->ClearAddresses(); 


        if($mail->Send()){
          echo "<script>alert('Correo enviado Exitosamente.');location.href ='javascript:history.back()';</script>";
        }else{
              echo "<script>alert('Error, no se pudo enviar el correo');location.href ='javascript:history.back()';</script>";
        }


/**************************************** TERMINA ENVIO EMAIL COM  **************************************************************/


}

 ?>
 <!--<div class="col-xs-3">
  Destinatario<input type="text" class="form-control" name="destinatario"> 
 </div>-->
 <form action="" method="post" name="correo1" enctype="multipart/form-data">
   <div class="col-xs-12">
    Asunto: <input type="text" class="form-control" name="asunto">
   </div>
   <div class="col-xs-12">
    Mensaje: <textarea name="mensaje" id="textareaMensaje" cols="30" rows="10" class="form-control"></textarea>   
   </div>
   <div class="col-xs-12">
     Archivos
     <input type="file" name="archivo" class="form-control">
   </div>

   <div class="col-xs-12" style="margin-top:10px;">
    <input type="submit" class="btn btn-success">   
    <input type="hidden" name="correo1" value="1">
   </div>

 </form>

  
