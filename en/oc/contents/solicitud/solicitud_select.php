<?php 
require_once('../Connections/dspp.php'); 
require_once('../Connections/mail.php');
mysql_select_db($database_dspp, $dspp);


if (!isset($_SESSION)) {
  session_start();
  
  $redireccion = "../index.php?OC";

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

/*if(isset($_POST['opp_delete'])){
  $query=sprintf("delete from opp where idopp = %s",GetSQLValueString($_POST['idopp'], "text"));
  $ejecutar=mysql_query($query,$dspp) or die(mysql_error());
}*/

/************ INICIA VARIABLES GLOBALES ***************/
$fecha = time();
$idoc = $_SESSION['idoc'];
$spp_global = "cert@spp.coop";
$administrador = "yasser.midnight@gmail.com";
/**********  TERMINA VARIABLES GLOBALES *****************/
 
if(isset($_POST['cancelar']) && $_POST['cancelar'] == "cancelar"){
  $idsolicitud_certificacion = $_POST['idsolicitud_certificacion'];
  $estatus_interno = 24;  

  $updateSQL = "UPDATE solicitud_certificacion SET status = $estatus_interno WHERE idsolicitud_certificacion = $idsolicitud_certificacion";
  $ejecutar = mysql_query($updateSQL,$dspp) or die(mysql_error());
}

if(isset($_POST['guardar_proceso']) && $_POST['guardar_proceso'] == 1){

  $query_opp = mysql_query("SELECT solicitud_certificacion.idopp, solicitud_certificacion.idoc, solicitud_certificacion.contacto1_email, solicitud_certificacion.contacto2_email, solicitud_certificacion.adm1_email, opp.nombre, opp.email FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE solicitud_certificacion.idsolicitud_certificacion = $_POST[idsolicitud_certificacion]", $dspp) or die(mysql_error());
  $detalle_opp = mysql_fetch_assoc($query_opp);


  $estatus_dspp = 8; //INICIA PROCESO DE CERTIFICACION
  //ESTATUS INTERNO
  // 8 = DICTAMEN POSITIVO
  //9 = DICTAMEN NEGATIVO
  // INSERTAMOS EL REGISTRO DE PROCESO_CERTIFICACION
  if(isset($_POST['nombre_archivo'])){
    $nombre_archivo = $_POST['nombre_archivo'];
  }else{
    $nombre_archivo = '';
  }
  if(isset($_POST['accion'])){
    $accion = $_POST['accion'];
  }else{
    $accion = '';
  }

  $rutaArchivo = "../../archivos/ocArchivos/anexos/";

  if(!empty($_FILES['archivo_estatus']['name'])){
      $_FILES["archivo_estatus"]["name"];
        move_uploaded_file($_FILES["archivo_estatus"]["tmp_name"], $rutaArchivo.time()."_".$_FILES["archivo_estatus"]["name"]);
        $archivo = $rutaArchivo.basename(time()."_".$_FILES["archivo_estatus"]["name"]);
  }else{
    $archivo = NULL;
  }



  if($_POST['estatus_interno'] == 8){ //checamos si el estatus_interno es positvo
      $query_oc = mysql_query("SELECT nombre, email1, email2 FROM oc WHERE idoc = $detalle_opp[idoc]", $dspp) or die(mysql_error());
      $detalle_oc = mysql_fetch_assoc($query_oc);

    $documentacion_nombres = '';
    $estatus_dspp = 9; //Termina proceso de certificación
    //creamos la variable del archivo extra
    if(!empty($_FILES['archivo_extra']['name'])){
        $_FILES["archivo_extra"]["name"];
          move_uploaded_file($_FILES["archivo_extra"]["tmp_name"], $rutaArchivo.time()."_".$_FILES["archivo_extra"]["name"]);
          $archivo = $rutaArchivo.basename(time()."_".$_FILES["archivo_extra"]["name"]);
    }else{
      $archivo = NULL;
    }

    $asunto = "D-SPP | NOTIFICACIÓN DE DICTAMEN";

    if($_POST['tipo_solicitud'] == 'RENOVACION'){
      if(isset($_POST['mensaje_renovacion'])){
        $mensaje_renovacion = $_POST['mensaje_renovacion'];
      }else{
        $mensaje_renovacion = '';
      }

      $insertSQL = sprintf("INSERT INTO proceso_certificacion (idsolicitud_certificacion, estatus_interno, estatus_dspp, nombre_archivo, accion, archivo, fecha_registro) VALUES (%s, %s, %s, %s, %s, %s, %s)",
        GetSQLValueString($_POST['idsolicitud_certificacion'], "int"),
        GetSQLValueString($_POST['estatus_interno'], "int"),
        GetSQLValueString($estatus_dspp, "int"),
        GetSQLValueString($_POST['nombreArchivo'], "text"),
        GetSQLValueString($accion, "text"),
        GetSQLValueString($archivo, "text"),
        GetSQLValueString($fecha, "int"));
      $insertar = mysql_query($insertSQL,$dspp) or die(mysql_error());

      //creamos el monto del comprobante de pago
      $estatus_comprobante = "EN ESPERA";

      $insertSQL = sprintf("INSERT INTO comprobante_pago(estatus_comprobante, monto) VALUES (%s, %s)",
        GetSQLValueString($estatus_comprobante, "text"),
        GetSQLValueString($_POST['monto_membresia'], "text"));
      $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

      //capturamos el id del comprobante para vincularlo con la membresia
      $idcomprobante_pago = mysql_insert_id($dspp);

      //creamos la membresia
      $estatus_membresia = "EN ESPERA";
      $insertSQL = sprintf("INSERT INTO membresia(estatus_membresia, idopp, idsolicitud_certificacion, idcomprobante_pago) VALUES (%s, %s, %s, %s)",
        GetSQLValueString($estatus_membresia, "text"),
        GetSQLValueString($_POST['idopp'], "int"),
        GetSQLValueString($_POST['idsolicitud_certificacion'], "int"),
        GetSQLValueString($idcomprobante_pago, "int"));
      $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

      ///inicia envio de mensaje dictamen positivo


      //$mail->AddAttachment($archivo);

      if(!empty($_POST['mensaje_renovacion'])){
        $cuerpo_mensaje = '
              <html>
              <head>
                <meta charset="utf-8">
              </head>
              <body>
                <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;text-align:justify" border="0" width="650px">
                  <tbody>
                    <tr>
                      <th rowspan="2" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                      <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">NOTIFICACIÓN DE DICTAMEN</span></p></th>

                    </tr>
                    <tr>
                     <th scope="col" align="left" width="280"><p>Para: <span style="color:red">'.$detalle_opp['nombre'].'</span></p></th>
                    </tr>

                    <tr>
                      <td colspan="2">
                        <p>
                          '.$_POST['mensaje_renovacion'].'
                        </p>
                        <p>
                          <b>
                            NOTA: El pago de membresìa se considera una ratificación de la firma de Contrato de Uso por lo que no es necesario firmar el contrato cada año que renuevan su certificado.
                          </b>
                        </p>
                        <hr>
                      </td>
                    </tr>
                    <tr>
                      <td><p><strong>Datos Bancarios</strong></p></td>
                    </tr>
                    <tr>
                      <td>
                        <ul>
                          Datos Bancarios Anexos en el correo.
                        </ul>
                      </td>
                    </tr>

                    <tr>
                      <td colspan="2">
                        <p>En caso de cualquier duda o aclaración por favor escribir a <span style="color:red">'.$detalle_oc['email1'].', '.$detalle_oc['email2'].'/span></p>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </body>
              </html>
        ';
      }else{
        $cuerpo_mensaje = '
              <html>
              <head>
                <meta charset="utf-8">
              </head>
              <body>
                <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;text-align:justify" border="0" width="650px">
                  <tbody>
                    <tr>
                      <th rowspan="2" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                      <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">NOTIFICACIÓN DE DICTAMEN</span></p></th>

                    </tr>
                    <tr>
                     <th scope="col" align="left" width="280"><p>Para: <span style="color:red">'.$detalle_opp['nombre'].'</span></p></th>
                    </tr>

                    <tr>
                      <td colspan="2">
                        <p>
                          1. Nosotros <span style="color:red">'.$detalle_oc['nombre'].'</span>, como Organismo de Certificación autorizado por SPP Global, nos complace informar por este medio que la evaluaciòn SPP fue concluida con resultado <span style="color:red">positivo</span>.
                        </p>
                        <p>
                          2. Para concluir el proceso, se solicita de la manera más atenta se <span style="color:red">proceda con el pago de membresìa a SPP Global</span>, de acuerdo al monto de: <strong style="color:red">'.$_POST['monto_membresia'].'</strong>. (Se anexan los datos bancarios, favor de leer las Disposiciones Generales de Pago para evitar se generen intereses). Una vez que haya realizado el pago, favor de entrar a su cuenta y cargar el comprobante bancario.
                        </p>
                        <p>
                          3. Una vez que SPP Global confirme a través del Sistema la recepción del pago en la cuenta de SPP Global, el Organismo de Certificaciòn hará entrega del Certificado.
                        </p>
                        <p>
                          <b>
                            NOTA: El pago de membresìa se considera una ratificación de la firma de Contrato de Uso por lo que no es necesario firmar el contrato cada año que renuevan su certificado.
                          </b>
                        </p>
                        <hr>
                      </td>
                    </tr>
                    <tr>
                      <td><p><strong>Datos Bancarios</strong></p></td>
                    </tr>
                    <tr>
                      <td>
                        <ul>
                          Datos Bancarios Anexos en el correo.
                        </ul>
                      </td>
                    </tr>

                    <tr>
                      <td colspan="2">
                        
                      </td>
                    </tr>
                  </tbody>
                </table>
              </body>
              </html>
        ';
      }

      $row_documentacion = mysql_query("SELECT * FROM documentacion WHERE nombre = 'Datos Bancarios SPP'", $dspp) or die(mysql_error());
      $documentacion = mysql_fetch_assoc($row_documentacion);
          $mail->AddAttachment($documentacion['archivo']);
   

          if(!empty($archivo)){
            $mail->AddAttachment($archivo);
          }
          $mail->AddAddress($detalle_opp['contacto1_email']);
          $mail->AddAddress($detalle_opp['contacto2_email']);
          $mail->AddAddress($detalle_opp['adm1_email']);
          $mail->AddAddress($detalle_opp['email']);
          $mail->AddBCC($administrador);
          $mail->AddBCC($spp_global);
          if(isset($detalle_oc['email1'])){
            $mail->AddBCC($detalle_oc['email1']);
          }
          if(isset($detalle_oc['email2'])){
            $mail->AddBCC($detalle_oc['email2']);
          }
          $mail->Subject = utf8_decode($asunto);
          $mail->Body = utf8_decode($cuerpo_mensaje);
          $mail->MsgHTML(utf8_decode($cuerpo_mensaje));

          if($mail->Send()){
            
           echo "<script>alert('Correo enviado Exitosamente.');location.href ='javascript:history.back()';</script>";
          }else{
            echo "<script>alert('Error, no se pudo enviar el correo, por favor contacte al administrador: soporte@d-spp.org');location.href ='javascript:history.back()';</script>";
       
          }
          //$mail->Send();
          $mail->ClearAddresses();
      ///termina envio de mensaje dictamen positivo
      ///termina envio de mensaje dictamen positivo
    }else{
      $insertSQL = sprintf("INSERT INTO proceso_certificacion (idsolicitud_certificacion, estatus_interno, estatus_dspp, nombre_archivo, accion, archivo, fecha_registro) VALUES (%s, %s, %s, %s, %s, %s, %s)",
        GetSQLValueString($_POST['idsolicitud_certificacion'], "int"),
        GetSQLValueString($_POST['estatus_interno'], "int"),
        GetSQLValueString($estatus_dspp, "int"),
        GetSQLValueString($_POST['nombreArchivo'], "text"),
        GetSQLValueString($accion, "text"),
        GetSQLValueString($archivo, "text"),
        GetSQLValueString($fecha, "int"));
      $insertar = mysql_query($insertSQL,$dspp) or die(mysql_error());

      //creamos el monto del comprobante de pago
      $estatus_comprobante = "EN ESPERA";

      $insertSQL = sprintf("INSERT INTO comprobante_pago(estatus_comprobante, monto) VALUES (%s, %s)",
        GetSQLValueString($estatus_comprobante, "text"),
        GetSQLValueString($_POST['monto_membresia'], "text"));
      $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

      //capturamos el id del comprobante para vincularlo con la membresia
      $idcomprobante_pago = mysql_insert_id($dspp);

      //creamos la membresia
      $estatus_membresia = "EN ESPERA";
      $insertSQL = sprintf("INSERT INTO membresia(estatus_membresia, idopp, idsolicitud_certificacion, idcomprobante_pago) VALUES (%s, %s, %s, %s)",
        GetSQLValueString($estatus_membresia, "text"),
        GetSQLValueString($_POST['idopp'], "int"),
        GetSQLValueString($_POST['idsolicitud_certificacion'], "int"),
        GetSQLValueString($idcomprobante_pago, "int"));
      $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

      ///inicia envio de mensaje dictamen positivo

      $row_documentacion = mysql_query("SELECT * FROM documentacion WHERE idestatus_interno = 8 AND idioma = '$_POST[idioma]'", $dspp) or die(mysql_error());
      while($documentacion = mysql_fetch_assoc($row_documentacion)){
          $mail->AddAttachment($documentacion['archivo']);
          $documentacion_nombres .= "<li>".$documentacion['nombre']."</li>";   
      }
      if(!empty($archivo)){
        $documentacion_nombres .= '<li>'.$_POST['nombreArchivo'].'</li>';
        $mail->AddAttachment($archivo);
      }

      $documentacion = mysql_fetch_assoc($row_documentacion);

      $asunto = "D-SPP | NOTIFICACIÓN DE DICTAMEN";

      if(!empty($_POST['mensajeOPP'])){
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
                      <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">NOTIFICACIÓN DE DICTAMEN</span></p></th>

                    </tr>
                    <tr>
                     <th scope="col" align="left" width="280"><p>Para: <span style="color:red">'.$detalle_opp['nombre'].'</span></p></th>
                    </tr>

                    <tr>
                      <td colspan="2" style="text-align:justify">
                        <p>'.$_POST['mensajeOPP'].'</p>
                      </td>
                    </tr>
                    <tr>
                      <td><p><strong>DOCUMENTOS ANEXOS</strong></p></td>
                    </tr>
                    <tr>
                      <td>
                        <ul>
                          '.$documentacion_nombres.'
                        </ul>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="2" style="text-align:justify">
                        <p style="color:red"><strong>MEMBRESÍA SPP</strong></p>
                        <p>
                          ADICIONALMENTE SE SOLICITA DE LA MANERA MÁS ATENTA REALIZAR EL PAGO CORRESPONDIENTE A LA MEMBRESIA SPP POR EL IMPORTE DE: <span style="color:red;">'.$_POST['monto_membresia'].'</span>
                        </p>
                        <p>
                          LOS DATOS BANCARIOS SE ENCUENTRAN ANEXOS AL CORREO.
                        </p>
                        <p>
                          DESPUÉS DE REALIZAR EL PAGO POR FAVOR PROCEDA A CARGAR EL <span style="color:red">CONTRATO DE USO FIRMADO</span> ASÍ MISMO EL <span style="color:red">COMPROBANTE DE PAGO</span> POR MEDIO DEL SISTEMA D-SPP, ESTO INGRESANDO EN SU CUENTA DE OPP(Organización de Pequeños Productores) EN LA SIGUIENTE DIRECCIÓN <a href="http://d-spp.org/esp/?OPP">http://d-spp.org/esp/?OPP</a>.
                        </p>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="2">
                        <p>En caso de cualquier duda o aclaración por favor escribir a <span style="color:red">cert@spp.coop</span> o <span style="color:red">soporte@d-spp.org</span></p>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </body>
              </html>
        ';
      }else{
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
                      <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">NOTIFICACIÓN DE DICTAMEN</span></p></th>

                    </tr>
                    <tr>
                     <th scope="col" align="left" width="280"><p>Para: <span style="color:red">'.$detalle_opp['nombre'].'</span></p></th>
                    </tr>
                    <tr>
                      <td colspan="2" style="text-align:justify">
                        <p>1. Nosotros <span style="color:red">'.$detalle_oc['nombre'].'</span>, como Organismo de Certificación autorizado por SPP Global, nos complace informar por este medio que la evaluaciòn SPP fue concluida con resultado positivo.</p>
                        <p>
                          2. Para concluir el proceso, se solicita de la manera más atenta leer los documentos anexos y posteriormente <span style="color:red">firmar el Contrato de Uso y Acuse de Recibo</span>.
                        </p>
                        <p>
                          Una vez que haya firmado los documentos indicados, <span style="color:red">ingresar a su cuenta y cargar los documentos para que éstos sean revisados por SPP Global</span>.
                        </p>
                        <p>
                          3. Una vez que SPP Global confirme a través del Sistema la recepción de los documentos y la recepciòn del pago en la cuenta de SPP Global, el Organismo de Certificaciòn hará entrega del Certificad
                        </p>
                      </td>
                    </tr>
                    <tr>
                      <td><p><strong>DOCUMENTOS ANEXOS</strong></p></td>
                    </tr>
                    <tr>
                      <td>
                        <ul>
                          '.$documentacion_nombres.'
                        </ul>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="2" style="text-align:justify">
                        <p style="color:red"><strong>MEMBRESÍA SPP</strong></p>
                        <p>
                          Adicionalmente se solicita de la manera más atenta, se proceda con el <span style="color:red">pago de membresìa a SPP Global</span>, de acuerdo al monto indicado de: <strong style="color:red;">'.$_POST['monto_membresia'].'</strong>. (Se anexan los datos bancarios, favor de leer las Disposiciones Generales de Pago para evitar se generen intereses). Una vez que haya realizado el pago, favor de <span style="color:red">entrar a su cuenta y cargar el comprobante bancario</span>.
                        </p>
                        <p>
                          LOS DATOS BANCARIOS SE ENCUENTRAN ANEXOS AL CORREO.
                        </p>
                        <p>
                          DESPUÉS DE REALIZAR EL PAGO POR FAVOR PROCEDA A CARGAR EL <span style="color:red">CONTRATO DE USO FIRMADO</span> ASÍ MISMO EL <span style="color:red">COMPROBANTE DE PAGO</span> POR MEDIO DEL SISTEMA D-SPP, ESTO INGRESANDO EN SU CUENTA DE OPP(Organización de Pequeños Productores) EN LA SIGUIENTE DIRECCIÓN <a href="http://d-spp.org/esp/?OPP">http://d-spp.org/esp/?OPP</a>.
                        </p>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="2">
                        <p>En caso de cualquier duda o aclaración por favor escribir a <span style="color:red">cert@spp.coop</span> o <span style="color:red">soporte@d-spp.org</span></p>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </body>
              </html>
        ';
      }
      if(!empty($detalle_opp['contacto1_email'])){
        $mail->AddAddress($detalle_opp['contacto1_email']);
      }
      if(!empty($detalle_opp['contacto2_email'])){
        $mail->AddAddress($detalle_opp['contacto2_email']);
      }
      if(!empty($detalle_opp['adm1_email'])){
        $mail->AddAddress($detalle_opp['adm1_email']);
      }
      if(!empty($detalle_opp['email'])){
        $mail->AddAddress($detalle_opp['email']);
      }
      $mail->AddBCC($spp_global);
      $mail->Subject = utf8_decode($asunto);
      $mail->Body = utf8_decode($cuerpo_mensaje);
      $mail->MsgHTML(utf8_decode($cuerpo_mensaje));
      /*$mail->Send();
      $mail->ClearAddresses();*/
      if($mail->Send()){
        $mail->ClearAddresses();
        echo "<script>alert('Correo enviado Exitosamente.');location.href ='javascript:history.back()';</script>";
      }else{
        $mail->ClearAddresses();
        echo "<script>alert('Error, no se pudo enviar el correo, por favor contacte al administrador: soporte@d-spp.org');location.href ='javascript:history.back()';</script>";
      }

          
      ///termina envio de mensaje dictamen positivo
    }



  }else if($_POST['estatus_interno'] == 9){ // EL DICTAMEN ES NEGATIVO **********************************************************************/
      $query_oc = mysql_query("SELECT nombre FROM oc WHERE idoc = $detalle_opp[idoc]", $dspp) or die(mysql_error());
      $detalle_oc = mysql_fetch_assoc($query_oc);

    $documentacion_nombres = '';
    $estatus_dspp = 9; //Termina proceso de certificación
    //creamos la variable del archivo extra
    if(!empty($_FILES['archivo_extra']['name'])){
        $_FILES["archivo_extra"]["name"];
          move_uploaded_file($_FILES["archivo_extra"]["tmp_name"], $rutaArchivo.time()."_".$_FILES["archivo_extra"]["name"]);
          $archivo = $rutaArchivo.basename(time()."_".$_FILES["archivo_extra"]["name"]);
    }else{
      $archivo = NULL;
    }

    $asunto = "D-SPP | NOTIFICACIÓN DE DICTAMEN";

    if($_POST['tipo_solicitud'] == 'RENOVACION'){
      if(isset($_POST['mensaje_renovacion'])){
        $mensaje_renovacion = $_POST['mensaje_renovacion'];
      }else{
        $mensaje_renovacion = '';
      }

      $insertSQL = sprintf("INSERT INTO proceso_certificacion (idsolicitud_certificacion, estatus_interno, estatus_dspp, nombre_archivo, accion, archivo, fecha_registro) VALUES (%s, %s, %s, %s, %s, %s, %s)",
        GetSQLValueString($_POST['idsolicitud_certificacion'], "int"),
        GetSQLValueString($_POST['estatus_interno'], "int"),
        GetSQLValueString($estatus_dspp, "int"),
        GetSQLValueString($_POST['nombreArchivo'], "text"),
        GetSQLValueString($accion, "text"),
        GetSQLValueString($archivo, "text"),
        GetSQLValueString($fecha, "int"));
      $insertar = mysql_query($insertSQL,$dspp) or die(mysql_error());


      ///inicia envio de mensaje dictamen positivo


      //$mail->AddAttachment($archivo);

      if(!empty($_POST['mensaje_renovacion'])){
        $cuerpo_mensaje = '
              <html>
              <head>
                <meta charset="utf-8">
              </head>
              <body>
                <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;text-align:justify" border="0" width="650px">
                  <tbody>
                    <tr>
                      <th rowspan="2" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                      <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">NOTIFICACIÓN DE DICTAMEN</span></p></th>

                    </tr>
                    <tr>
                     <th scope="col" align="left" width="280"><p>Para: <span style="color:red">'.$detalle_opp['nombre'].'</span></p></th>
                    </tr>

                    <tr>
                      <td colspan="2">
                        <p>
                          '.$_POST['mensaje_renovacion'].'
                        </p>
                      </td>
                    </tr>

                    <tr>
                      <td colspan="2">
                        <p>En caso de cualquier duda o aclaración por favor escribir a <span style="color:red">soporte@d-spp.org</span></p>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </body>
              </html>
        ';
      }else{
        $cuerpo_mensaje = '
              <html>
              <head>
                <meta charset="utf-8">
              </head>
              <body>
                <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;text-align:justify" border="0" width="650px">
                  <tbody>
                    <tr>
                      <th rowspan="2" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                      <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">NOTIFICACIÓN DE DICTAMEN</span></p></th>

                    </tr>
                    <tr>
                     <th scope="col" align="left" width="280"><p>Para: <span style="color:red">'.$detalle_opp['nombre'].'</span></p></th>
                    </tr>

                    <tr>
                      <td colspan="2">
                        <p>
                          Nosotros <span style="color:red">'.$detalle_oc['nombre'].'</span>, como Organismo de Certificación autorizado por SPP Global, lamentamos informar que su Dictamen de la evaluación para el Certificación SPP es negatvio. 
                        </p>
                      </td>
                    </tr>

                    <tr>
                      <td colspan="2">
                        <p>En caso de cualquier duda o aclaración por favor escribir a <span style="color:red">soporte@d-spp.org</span></p>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </body>
              </html>
        ';
      }
   

          if(!empty($archivo)){
            $mail->AddAttachment($archivo);
          }
          $mail->AddAddress($detalle_opp['contacto1_email']);
          $mail->AddAddress($detalle_opp['contacto2_email']);
          $mail->AddAddress($detalle_opp['adm1_email']);
          $mail->AddAddress($detalle_opp['email']);
          $mail->AddBCC($administrador);
          $mail->AddBCC($spp_global);
          $mail->Subject = utf8_decode($asunto);
          $mail->Body = utf8_decode($cuerpo_mensaje);
          $mail->MsgHTML(utf8_decode($cuerpo_mensaje));

          if($mail->Send()){
            
           echo "<script>alert('Correo enviado Exitosamente.');location.href ='javascript:history.back()';</script>";
          }else{
            echo "<script>alert('Error, no se pudo enviar el correo, por favor contacte al administrador: soporte@d-spp.org');location.href ='javascript:history.back()';</script>";
       
          }
          //$mail->Send();
          $mail->ClearAddresses();
      ///termina envio de mensaje dictamen positivo
      ///termina envio de mensaje dictamen positivo
    }else{
      $insertSQL = sprintf("INSERT INTO proceso_certificacion (idsolicitud_certificacion, estatus_interno, estatus_dspp, nombre_archivo, accion, archivo, fecha_registro) VALUES (%s, %s, %s, %s, %s, %s, %s)",
        GetSQLValueString($_POST['idsolicitud_certificacion'], "int"),
        GetSQLValueString($_POST['estatus_interno'], "int"),
        GetSQLValueString($estatus_dspp, "int"),
        GetSQLValueString($_POST['nombreArchivo'], "text"),
        GetSQLValueString($accion, "text"),
        GetSQLValueString($archivo, "text"),
        GetSQLValueString($fecha, "int"));
      $insertar = mysql_query($insertSQL,$dspp) or die(mysql_error());

      //creamos el monto del comprobante de pago
      $estatus_comprobante = "EN ESPERA";

      $insertSQL = sprintf("INSERT INTO comprobante_pago(estatus_comprobante, monto) VALUES (%s, %s)",
        GetSQLValueString($estatus_comprobante, "text"),
        GetSQLValueString($_POST['monto_membresia'], "text"));
      $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

      //capturamos el id del comprobante para vincularlo con la membresia
      $idcomprobante_pago = mysql_insert_id($dspp);

      //creamos la membresia
      $estatus_membresia = "EN ESPERA";
      $insertSQL = sprintf("INSERT INTO membresia(estatus_membresia, idopp, idsolicitud_certificacion, idcomprobante_pago) VALUES (%s, %s, %s, %s)",
        GetSQLValueString($estatus_membresia, "text"),
        GetSQLValueString($_POST['idopp'], "int"),
        GetSQLValueString($_POST['idsolicitud_certificacion'], "int"),
        GetSQLValueString($idcomprobante_pago, "int"));
      $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

      ///inicia envio de mensaje dictamen positivo

      $row_documentacion = mysql_query("SELECT * FROM documentacion WHERE idestatus_interno = 8 AND idioma = '$_POST[idioma]'", $dspp) or die(mysql_error());
      while($documentacion = mysql_fetch_assoc($row_documentacion)){
          $mail->AddAttachment($documentacion['archivo']);
          $documentacion_nombres .= "<li>".$documentacion['nombre']."</li>";   
      }
      if(!empty($archivo)){
        $documentacion_nombres .= '<li>'.$_POST['nombreArchivo'].'</li>';
        $mail->AddAttachment($archivo);
      }

      $documentacion = mysql_fetch_assoc($row_documentacion);

      $asunto = "D-SPP | NOTIFICACIÓN DE DICTAMEN";

      if(!empty($_POST['mensajeOPP'])){
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
                      <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">NOTIFICACIÓN DE DICTAMEN</span></p></th>

                    </tr>
                    <tr>
                     <th scope="col" align="left" width="280"><p>Para: <span style="color:red">'.$detalle_opp['nombre'].'</span></p></th>
                    </tr>

                    <tr>
                      <td colspan="2" style="text-align:justify">
                        <p>'.$_POST['mensajeOPP'].'</p>
                      </td>
                    </tr>
                    <tr>
                      <td><p><strong>DOCUMENTOS ANEXOS</strong></p></td>
                    </tr>
                    <tr>
                      <td>
                        <ul>
                          '.$documentacion_nombres.'
                        </ul>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="2" style="text-align:justify">
                        <p style="color:red"><strong>MEMBRESÍA SPP</strong></p>
                        <p>
                          ADICIONALMENTE SE SOLICITA DE LA MANERA MÁS ATENTA REALIZAR EL PAGO CORRESPONDIENTE A LA MEMBRESIA SPP POR EL IMPORTE DE: <span style="color:red;">'.$_POST['monto_membresia'].'</span>
                        </p>
                        <p>
                          LOS DATOS BANCARIOS SE ENCUENTRAN ANEXOS AL CORREO.
                        </p>
                        <p>
                          DESPUÉS DE REALIZAR EL PAGO POR FAVOR PROCEDA A CARGAR EL <span style="color:red">CONTRATO DE USO FIRMADO</span> ASÍ MISMO EL <span style="color:red">COMPROBANTE DE PAGO</span> POR MEDIO DEL SISTEMA D-SPP, ESTO INGRESANDO EN SU CUENTA DE OPP(Organización de Pequeños Productores) EN LA SIGUIENTE DIRECCIÓN <a href="http://d-spp.org/esp/?OPP">http://d-spp.org/esp/?OPP</a>.
                        </p>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="2">
                        <p>En caso de cualquier duda o aclaración por favor escribir a <span style="color:red">cert@spp.coop</span> o <span style="color:red">soporte@d-spp.org</span></p>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </body>
              </html>
        ';
      }else{
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
                      <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">NOTIFICACIÓN DE DICTAMEN</span></p></th>

                    </tr>
                    <tr>
                     <th scope="col" align="left" width="280"><p>Para: <span style="color:red">'.$detalle_opp['nombre'].'</span></p></th>
                    </tr>
                    <tr>
                      <td colspan="2" style="text-align:justify">
                        <p>1. Nosotros <span style="color:red">'.$detalle_oc['nombre'].'</span>, como Organismo de Certificación autorizado por SPP Global, nos complace informar por este medio que la evaluaciòn SPP fue concluida con resultado positivo.</p>
                        <p>
                          2. Para concluir el proceso, se solicita de la manera más atenta leer los documentos anexos y posteriormente <span style="color:red">firmar el Contrato de Uso y Acuse de Recibo</span>.
                        </p>
                        <p>
                          Una vez que haya firmado los documentos indicados, <span style="color:red">ingresar a su cuenta y cargar los documentos para que éstos sean revisados por SPP Global</span>.
                        </p>
                        <p>
                          3. Una vez que SPP Global confirme a través del Sistema la recepción de los documentos y la recepciòn del pago en la cuenta de SPP Global, el Organismo de Certificaciòn hará entrega del Certificad
                        </p>
                      </td>
                    </tr>
                    <tr>
                      <td><p><strong>DOCUMENTOS ANEXOS</strong></p></td>
                    </tr>
                    <tr>
                      <td>
                        <ul>
                          '.$documentacion_nombres.'
                        </ul>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="2" style="text-align:justify">
                        <p style="color:red"><strong>MEMBRESÍA SPP</strong></p>
                        <p>
                          Adicionalmente se solicita de la manera más atenta, se proceda con el <span style="color:red">pago de membresìa a SPP Global</span>, de acuerdo al monto indicado de: <strong style="color:red;">'.$_POST['monto_membresia'].'</strong>. (Se anexan los datos bancarios, favor de leer las Disposiciones Generales de Pago para evitar se generen intereses). Una vez que haya realizado el pago, favor de <span style="color:red">entrar a su cuenta y cargar el comprobante bancario</span>.
                        </p>
                        <p>
                          LOS DATOS BANCARIOS SE ENCUENTRAN ANEXOS AL CORREO.
                        </p>
                        <p>
                          DESPUÉS DE REALIZAR EL PAGO POR FAVOR PROCEDA A CARGAR EL <span style="color:red">CONTRATO DE USO FIRMADO</span> ASÍ MISMO EL <span style="color:red">COMPROBANTE DE PAGO</span> POR MEDIO DEL SISTEMA D-SPP, ESTO INGRESANDO EN SU CUENTA DE OPP(Organización de Pequeños Productores) EN LA SIGUIENTE DIRECCIÓN <a href="http://d-spp.org/esp/?OPP">http://d-spp.org/esp/?OPP</a>.
                        </p>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="2">
                        <p>En caso de cualquier duda o aclaración por favor escribir a <span style="color:red">cert@spp.coop</span> o <span style="color:red">soporte@d-spp.org</span></p>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </body>
              </html>
        ';
      }
      if(!empty($detalle_opp['contacto1_email'])){
        $mail->AddAddress($detalle_opp['contacto1_email']);
      }
      if(!empty($detalle_opp['contacto2_email'])){
        $mail->AddAddress($detalle_opp['contacto2_email']);
      }
      if(!empty($detalle_opp['adm1_email'])){
        $mail->AddAddress($detalle_opp['adm1_email']);
      }
      if(!empty($detalle_opp['email'])){
        $mail->AddAddress($detalle_opp['email']);
      }
      $mail->AddBCC($spp_global);
      $mail->Subject = utf8_decode($asunto);
      $mail->Body = utf8_decode($cuerpo_mensaje);
      $mail->MsgHTML(utf8_decode($cuerpo_mensaje));
      /*$mail->Send();
      $mail->ClearAddresses();*/
      if($mail->Send()){
        $mail->ClearAddresses();
        echo "<script>alert('Correo enviado Exitosamente.');location.href ='javascript:history.back()';</script>";
      }else{
        $mail->ClearAddresses();
        echo "<script>alert('Error, no se pudo enviar el correo, por favor contacte al administrador: soporte@d-spp.org');location.href ='javascript:history.back()';</script>";
      }

          
      ///termina envio de mensaje dictamen positivo
    }
  }else{
    $insertSQL = sprintf("INSERT INTO proceso_certificacion (idsolicitud_certificacion, estatus_interno, estatus_dspp, nombre_archivo, accion, archivo, fecha_registro) VALUES (%s, %s, %s, %s, %s, %s, %s)",
      GetSQLValueString($_POST['idsolicitud_certificacion'], "int"),
      GetSQLValueString($_POST['estatus_interno'], "int"),
      GetSQLValueString($estatus_dspp, "int"),
      GetSQLValueString($nombre_archivo, "text"),
      GetSQLValueString($accion, "text"),
      GetSQLValueString($archivo, "text"),
      GetSQLValueString($fecha, "int"));
    $insertar = mysql_query($insertSQL,$dspp) or die(mysql_error());
  }

  $mensaje = "Se ha actualizado el Proceso de Certificación";
}

if(isset($_POST['cargar_documentos']) && $_POST['cargar_documentos'] == 1){
  $ruta_evaluacion = "../../archivos/ocArchivos/documentos_evaluacion/";

  $estatus_formato = "ENVIADO";
  $estatus_dictamen = "ENVIADO";
  $estatus_informe = "ENVIADO";

  if(!empty($_FILES['formato_evaluacion']['name'])){
      $_FILES["formato_evaluacion"]["name"];
        move_uploaded_file($_FILES["formato_evaluacion"]["tmp_name"], $ruta_evaluacion.$fecha."_".$_FILES["formato_evaluacion"]["name"]);
        $formato = $ruta_evaluacion.basename($fecha."_".$_FILES["formato_evaluacion"]["name"]);
  }else{
    $formato = NULL;
  }
  //insertamos formato_evaluacion
  $insertSQL = sprintf("INSERT INTO formato_evaluacion (idopp, idsolicitud_certificacion, estatus_formato, archivo, fecha_registro) VALUES (%s, %s, %s, %s, %s)",
    GetSQLValueString($_POST['idopp'], "int"),
    GetSQLValueString($_POST['idsolicitud_certificacion'], "int"),
    GetSQLValueString($estatus_formato, "text"),
    GetSQLValueString($formato, "text"),
    GetSQLValueString($fecha, "int"));
  $insertar = mysql_query($insertSQL,$dspp) or die(mysql_error());

  //insertamos el proceso_certificacion
  $estatus_dspp = 22; //Formato de Evaluación Cargado
  $nombre_archivo = "Formato de Evaluación";
  $insertSQL = sprintf("INSERT INTO proceso_certificacion (idsolicitud_certificacion, estatus_dspp, nombre_archivo, archivo, fecha_registro) VALUES (%s, %s, %s, %s, %s)",
    GetSQLValueString($_POST['idsolicitud_certificacion'], "int"),
    GetSQLValueString($estatus_dspp, "int"),
    GetSQLValueString($nombre_archivo, "text"),
    GetSQLValueString($formato, "text"),
    GetSQLValueString($fecha, "int"));
  $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

  if(!empty($_FILES['informe_evaluacion']['name'])){
      $_FILES["informe_evaluacion"]["name"];
        move_uploaded_file($_FILES["informe_evaluacion"]["tmp_name"], $ruta_evaluacion.$fecha."_".$_FILES["informe_evaluacion"]["name"]);
        $informe = $ruta_evaluacion.basename($fecha."_".$_FILES["informe_evaluacion"]["name"]);
  }else{
    $informe = NULL;
  }

  //insertamos informe_evaluacion
  $insertSQL = sprintf("INSERT INTO informe_evaluacion (idopp, idsolicitud_certificacion, estatus_informe, archivo, fecha_registro) VALUES (%s, %s, %s, %s, %s)",
    GetSQLValueString($_POST['idopp'], "int"),
    GetSQLValueString($_POST['idsolicitud_certificacion'], "int"),
    GetSQLValueString($estatus_informe, "text"),
    GetSQLValueString($informe, "text"),
    GetSQLValueString($fecha, "int"));
  $insertar = mysql_query($insertSQL,$dspp) or die(mysql_error());

  //insertamos el proceso_certificacion
  $estatus_dspp = 20; //Informe de Evaluación Cargado
  $nombre_archivo = "Informe de Evaluación";
  $insertSQL = sprintf("INSERT INTO proceso_certificacion (idsolicitud_certificacion, estatus_dspp, nombre_archivo, archivo, fecha_registro) VALUES (%s, %s, %s, %s, %s)",
    GetSQLValueString($_POST['idsolicitud_certificacion'], "int"),
    GetSQLValueString($estatus_dspp, "int"),
    GetSQLValueString($nombre_archivo, "text"),
    GetSQLValueString($informe, "text"),
    GetSQLValueString($fecha, "int"));
  $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

  if(!empty($_FILES['dictamen_evaluacion']['name'])){
      $_FILES["dictamen_evaluacion"]["name"];
        move_uploaded_file($_FILES["dictamen_evaluacion"]["tmp_name"], $ruta_evaluacion.$fecha."_".$_FILES["dictamen_evaluacion"]["name"]);
        $dictamen = $ruta_evaluacion.basename($fecha."_".$_FILES["dictamen_evaluacion"]["name"]);
  }else{
    $dictamen = NULL;
  }

  //insertarmos el dictamen de evaluación
  $insertSQL = sprintf("INSERT INTO dictamen_evaluacion(idopp, idsolicitud_certificacion, estatus_dictamen, archivo, fecha_registro) VALUES (%s, %s, %s, %s, %s)",
    GetSQLValueString($_POST['idopp'], "int"),
    GetSQLValueString($_POST['idsolicitud_certificacion'], "int"),
    GetSQLValueString($estatus_dictamen, "text"),
    GetSQLValueString($dictamen, "text"),
    GetSQLValueString($fecha, "int"));
  $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

  //se crear el proceso_certificacion
  $estatus_dspp = 21; //Dictamen de Evaluación Cargado
  $nombre_archivo = "Dictamen de Evaluación";
  $insertSQL = sprintf("INSERT INTO proceso_certificacion(idsolicitud_certificacion, estatus_dspp, nombre_archivo, archivo, fecha_registro) VALUES (%s, %s, %s, %s, %s)",
    GetSQLValueString($_POST['idsolicitud_certificacion'], "int"),
    GetSQLValueString($estatus_dspp, "int"),
    GetSQLValueString($nombre_archivo, "text"),
    GetSQLValueString($dictamen, "text"),
    GetSQLValueString($fecha, "int"));
  $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

  //inicia enviar correo a ADM sobre documentacion de Evaluación
  $row_informacion = mysql_query("SELECT solicitud_certificacion.idsolicitud_certificacion, opp.nombre AS 'nombre_opp', oc.nombre AS 'nombre_oc' FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp INNER JOIN oc ON solicitud_certificacion.idoc = oc.idoc WHERE idsolicitud_certificacion = $_POST[idsolicitud_certificacion]", $dspp) or die(mysql_error());
  $informacion = mysql_fetch_assoc($row_informacion);
  $asunto = "D-SPP | Se ha cargado el Formato, Dictamen e Informe de Evaluación";

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
              <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">Revisión Formato, Dictamen e Informe de Evaluación</span></p></th>

            </tr>
            <tr>
             <th scope="col" align="left" width="280"><p>OPP: <span style="color:red">'.$informacion['nombre_opp'].'</span></p></th>
            </tr>

            <tr>
              <td colspan="2">
               <p>El OC: <span style="color:red">'.$informacion['nombre_oc'].'</span> ha cargado la documentación de evaluación correspondiente al proceso de certificación de la OPP: '.$informacion['nombre_opp'].'
               <p>
                Por favor proceda a ingresar en su cuenta de ADMINISTRADOR dentro del sistema D-SPP para poder revisar los siguientes documento: 
                   <ul style="color:red">
                     <li>Formato de Evaluación</li>
                     <li>Informe de Evaluación</li>
                     <li>Dictamen de Evaluación</li>
                   </ul>

               </p>
              </td>
            </tr>
            <tr>
              <td coslpan="2">
                <p><span style="color:red">En caso de que los documentos sean aprobados se notificara al OC para que puedar cargar y enviar el certificado.</span></p>
              </td>
            </tr>
          </tbody>
        </table>
      </body>
    </html>
  ';
    $mail->AddAddress($spp_global);
    $mail->AddAttachment($formato);
    $mail->AddAttachment($informe);
    $mail->AddAttachment($dictamen);

    //$mail->Username = "soporte@d-spp.org";
    //$mail->Password = "/aung5l6tZ";
    $mail->Subject = utf8_decode($asunto);
    $mail->Body = utf8_decode($cuerpo_mensaje);
    $mail->MsgHTML(utf8_decode($cuerpo_mensaje));
    //$mail->Send();
    //$mail->ClearAddresses();
    if($mail->Send()){
      $mail->ClearAddresses();
      echo "<script>alert('Se ha enviado el Formato, Dictamen e Informe de Evaluación, en breve sera contactado.');location.href ='javascript:history.back()';</script>";
    }else{
      $mail->ClearAddresses();
      echo "<script>alert('Error, no se pudo enviar el correo, por favor contacte al administrador: soporte@d-spp.org');location.href ='javascript:history.back()';</script>";
    }
  //termina enviar correo a ADM sobre documentación de Evaluación

  //$mensaje = "Se ha enviado el Formato, Dictamen e Informe de Evaluación";

}

if(isset($_POST['enviar_certificado']) && $_POST['enviar_certificado'] == 1){
  $estatus_dspp = 13; //CERTIFICADA
  $rutaArchivo = "../../archivos/ocArchivos/certificados/";
  if(!empty($_FILES['certificado']['name'])){
      $_FILES["certificado"]["name"];
        move_uploaded_file($_FILES["certificado"]["tmp_name"], $rutaArchivo.time()."_".$_FILES["certificado"]["name"]);
        $certificado = $rutaArchivo.basename(time()."_".$_FILES["certificado"]["name"]);
  }else{
    $certificado = NULL;
  }
  //insertamos el certificado
  $insertSQL = sprintf("INSERT INTO certificado(idopp, idsolicitud_certificacion, entidad, estatus_certificado, vigencia_inicio, vigencia_fin, archivo, fecha_registro) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
    GetSQLValueString($_POST['idopp'], "int"),
    GetSQLValueString($_POST['idsolicitud_certificacion'], "int"),
    GetSQLValueString($_POST['idoc'], "int"),
    GetSQLValueString($estatus_dspp, "int"),
    GetSQLValueString($_POST['fecha_inicio'], "text"),
    GetSQLValueString($_POST['fecha_fin'], "text"),
    GetSQLValueString($certificado, "text"),
    GetSQLValueString($fecha, "int"));
  $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

  //insertamos el proceso de certificacion
  $estatus_proceso = 12; //es el estatus_dspp (certificado emitido)
  $nombre_archivo = "CERTIFICADO";
  $insertSQL = sprintf("INSERT INTO proceso_certificacion(idsolicitud_certificacion, estatus_dspp, nombre_archivo, archivo, fecha_registro) VALUES (%s, %s, %s, %s, %s)",
    GetSQLValueString($_POST['idsolicitud_certificacion'], "int"),
    GetSQLValueString($estatus_proceso, "int"),
    GetSQLValueString($nombre_archivo, "text"),
    GetSQLValueString($certificado, "text"),
    GetSQLValueString($fecha, "int"));
  $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

  //ACTUALIZAMOS A LA OPP
  $estatus_dspp = 13; //certificada
  $estatus_interno = 8; //dictamen_positivo
  $estatus_publico = 2; //certificado
  $estatus_opp = "CERTITICADO";
  $updateSQL = sprintf("UPDATE opp SET estatus_opp = %s, estatus_publico = %s, estatus_interno = %s, estatus_dspp = %s WHERE idopp = %s",
    GetSQLValueString($estatus_opp, "text"),
    GetSQLValueString($estatus_publico, "int"),
    GetSQLValueString($estatus_interno, "int"),
    GetSQLValueString($estatus_dspp, "int"),
    GetSQLValueString($_POST['idopp'], "int"));
  $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

  //inicia correo envio de certificado
  $row_informacion = mysql_query("SELECT solicitud_certificacion.idopp, solicitud_certificacion.contacto1_email, opp.nombre AS 'nombre_opp', opp.email FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE idsolicitud_certificacion = $_POST[idsolicitud_certificacion]", $dspp) or die(mysql_error());
  $informacion = mysql_fetch_assoc($row_informacion);
  $inicio = strtotime($_POST['fecha_inicio']);
  $fin = strtotime($_POST['fecha_fin']);
  $asunto = "D-SPP | Certificado Disponible para Descargar";

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
                <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">Certificado Disponible para descargar</span></p></th>

              </tr>
              <tr>
               <th scope="col" align="left" width="280"><p>OPP: <span style="color:red">'.$informacion['nombre_opp'].'</span></p></th>
              </tr>

              <tr>
                <td colspan="2">
                 <p>
                  Felicidades!!!, su Certificado ha sido liberado, ahora puede descargarlo.
                 </p>
                 <p>El Certificado tiene un vigencia del dia <span style="color:red">'.date('d/m/Y', $inicio).'</span> al dia: <span style="color:red">'.date('d/m/Y', $fin).'</span>, el cual se encuentra anexo a este correo.</p>
                 
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
    $mail->AddAddress($informacion['email']);
    $mail->AddAddress($informacion['contacto1_email']);
    $mail->AddBCC($spp_global);
    $mail->AddAttachment($certificado);
    //$mail->Username = "soporte@d-spp.org";
    //$mail->Password = "/aung5l6tZ";
    $mail->Subject = utf8_decode($asunto);
    $mail->Body = utf8_decode($cuerpo_mensaje);
    $mail->MsgHTML(utf8_decode($cuerpo_mensaje));
    $mail->Send();
    $mail->ClearAddresses();



  //termina correo envio de certificado

  $mensaje = "Se ha cargado el Certificado y se ha notificado a SPP GLOBAL y a la Organización de Pequeños Productores";
}


$currentPage = $_SERVER["PHP_SELF"];

$maxRows_opp = 20;
$pageNum_opp = 0;
if (isset($_GET['pageNum_opp'])) {
  $pageNum_opp = $_GET['pageNum_opp'];
}
$startRow_opp = $pageNum_opp * $maxRows_opp;

//$query = "SELECT solicitud_certificacion.idsolicitud_certificacion AS 'idsolicitud', solicitud_certificacion.tipo_solicitud, solicitud_certificacion.idopp, solicitud_certificacion.idoc, solicitud_certificacion.fecha_registro, solicitud_certificacion.fecha_aceptacion, solicitud_certificacion.cotizacion_opp, solicitud_certificacion.contacto1_email, solicitud_certificacion.contacto2_email, opp.nombre AS 'nombre_opp', opp.abreviacion AS 'abreviacion_opp', opp.email, proceso_certificacion.idproceso_certificacion, proceso_certificacion.estatus_publico, proceso_certificacion.estatus_interno, proceso_certificacion.estatus_dspp, estatus_publico.nombre AS 'nombre_publico', estatus_interno.nombre AS 'nombre_interno', estatus_dspp.nombre AS 'nombre_dspp', periodo_objecion.*, membresia.idmembresia, membresia.estatus_membresia, contratos.idcontrato, contratos.estatus_contrato, certificado.idcertificado, formato_evaluacion.idformato_evaluacion, dictamen_evaluacion.iddictamen_evaluacion, informe_evaluacion.idinforme_evaluacion FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp LEFT JOIN proceso_certificacion ON solicitud_certificacion.idsolicitud_certificacion = proceso_certificacion.idsolicitud_certificacion LEFT JOIN estatus_publico ON proceso_certificacion.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON proceso_certificacion.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON proceso_certificacion.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN periodo_objecion ON solicitud_certificacion.idsolicitud_certificacion = periodo_objecion.idsolicitud_certificacion LEFT JOIN membresia ON solicitud_certificacion.idsolicitud_certificacion = membresia.idsolicitud_certificacion LEFT JOIN contratos ON solicitud_certificacion.idsolicitud_certificacion = contratos.idsolicitud_certificacion LEFT JOIN certificado ON solicitud_certificacion.idopp = certificado.idopp LEFT JOIN dictamen_evaluacion ON solicitud_certificacion.idsolicitud_certificacion = dictamen_evaluacion.idsolicitud_certificacion LEFT JOIN informe_evaluacion ON solicitud_certificacion.idsolicitud_certificacion = informe_evaluacion.idsolicitud_certificacion LEFT JOIN formato_evaluacion ON solicitud_certificacion.idsolicitud_certificacion = formato_evaluacion.idsolicitud_certificacion WHERE solicitud_certificacion.idoc = $idoc GROUP BY proceso_certificacion.estatus_dspp ORDER BY proceso_certificacion.estatus_dspp DESC";

$query = "SELECT solicitud_certificacion.*, solicitud_certificacion.idopp, solicitud_certificacion.idsolicitud_certificacion AS 'idsolicitud', oc.abreviacion AS 'abreviacionOC', opp.nombre AS 'nombre_opp', opp.abreviacion AS 'abreviacion_opp', opp.email, periodo_objecion.idperiodo_objecion, periodo_objecion.fecha_inicio, periodo_objecion.fecha_fin, periodo_objecion.estatus_objecion, periodo_objecion.observacion, periodo_objecion.dictamen, periodo_objecion.documento, membresia.idmembresia, certificado.idcertificado, contratos.idcontrato, contratos.estatus_contrato, informe_evaluacion.idinforme_evaluacion, formato_evaluacion.idformato_evaluacion, dictamen_evaluacion.iddictamen_evaluacion FROM solicitud_certificacion INNER JOIN oc ON solicitud_certificacion.idoc = oc.idoc INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp LEFT JOIN periodo_objecion ON solicitud_certificacion.idsolicitud_certificacion  = periodo_objecion.idsolicitud_certificacion LEFT JOIN membresia ON solicitud_certificacion.idsolicitud_certificacion = membresia.idsolicitud_certificacion LEFT JOIN certificado ON solicitud_certificacion.idsolicitud_certificacion = certificado.idsolicitud_certificacion LEFT JOIN contratos ON solicitud_certificacion.idsolicitud_certificacion = contratos.idsolicitud_certificacion LEFT JOIN informe_evaluacion ON solicitud_certificacion.idsolicitud_certificacion = informe_evaluacion.idsolicitud_certificacion LEFT JOIN formato_evaluacion ON solicitud_certificacion.idsolicitud_certificacion = formato_evaluacion.idsolicitud_certificacion LEFT JOIN dictamen_evaluacion ON solicitud_certificacion.idsolicitud_certificacion = dictamen_evaluacion.idsolicitud_certificacion WHERE solicitud_certificacion.idoc = $idoc ORDER BY solicitud_certificacion.fecha_registro DESC";

$row_solicitud = mysql_query($query,$dspp) or die(mysql_error());

?>

<div class="row">
  <?php 
  if(isset($mensaje)){
  ?>
  <div class="col-md-12 alert alert-success alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 style="font-size:14px;" class="text-center"><?php echo $mensaje; ?><h4/>
  </div>
  <?php
  }
  ?>

  <div class="col-md-12">
    <table class="table table-condensed table-bordered" style="font-size:12px;">
      <thead>   
        <tr class="success">
          <th class="text-center">ID</th>
          <th class="text-center"><a href="#" data-toggle="tooltip" title="Type of application"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>Type</a></th>
          <th class="text-center">Application date</th>
          <th class="text-center"><a href="#" data-toggle="tooltip" title="Certification Entity"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>CE</a></th>
          <th class="text-center"><a href="#" data-toggle="tooltip" title="Small Producers´Organization"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> SPO</a></th>
          <th class="text-center">Application status</th>
          <th class="text-center">Quotation<br>(downloadable)</th>
          <th class="text-center">Objection Process</th>
          <th class="text-center">Certification <br>Process</th>
          <th class="text-center">Certificate</th>
          <th class="text-center" colspan="2">Actions</th>
        </tr>  

      </thead>
      <tbody>
      <?php 
      while($solicitud = mysql_fetch_assoc($row_solicitud)){
          $query_proceso = "SELECT proceso_certificacion.*, proceso_certificacion.idsolicitud_certificacion, estatus_publico.idestatus_publico, estatus_publico.nombre_ingles AS 'nombre_publico', estatus_interno.idestatus_interno, estatus_interno.nombre_ingles AS 'nombre_interno', estatus_dspp.idestatus_dspp, estatus_dspp.nombre_ingles AS 'nombre_dspp', membresia.idmembresia, membresia.estatus_membresia, membresia.idcomprobante_pago, membresia.fecha_registro FROM proceso_certificacion LEFT JOIN estatus_publico ON proceso_certificacion.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON proceso_certificacion.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON proceso_certificacion.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN membresia ON proceso_certificacion.idsolicitud_certificacion = membresia.idsolicitud_certificacion WHERE proceso_certificacion.idsolicitud_certificacion =  $solicitud[idsolicitud] ORDER BY proceso_certificacion.idproceso_certificacion DESC LIMIT 1";
          $ejecutar = mysql_query($query_proceso,$dspp) or die(mysql_error());
          $proceso_certificacion = mysql_fetch_assoc($ejecutar);

      ?>
        <tr <?php if($proceso_certificacion['estatus_dspp'] == 12){ echo "class='success'"; }else if($proceso_certificacion['estatus_interno'] == 9){ echo "class='danger'"; } ?>>
          <td>
            <?php echo $solicitud['idsolicitud']; ?>
            <input type="hidden" name="idsolicitud_certificacion" value="<?php echo $solicitud['idsolicitud']; ?>">
            <input type="hidden" name="tipo_solicitud" value="<?php echo $solicitud['tipo_solicitud']; ?>">
          </td>
          <td <?php if($solicitud['tipo_solicitud'] == 'NUEVA'){ echo "class='success'"; }else{ echo "class='warning'"; } ?>class="warning">
            <?php echo $solicitud['tipo_solicitud']; ?>
          </td>
          <td>
            <?php echo date('d/m/Y', $solicitud['fecha_registro']); ?>
          </td>
          <td>
            <?php echo $solicitud['abreviacionOC']; ?>
          </td>
          <td>
            <a href="?OPP&detail&idopp=<?php echo $solicitud['idopp']; ?>"><?php echo $solicitud['abreviacion_opp']; ?></a>
          </td>
          <td>
            <?php 
            if(isset($proceso_certificacion['estatus_dspp'])){
              echo $proceso_certificacion['nombre_dspp'];
            }else{
              echo "Not available";
            }
             ?>
          </td>

          <td>
            <?php
            if(isset($solicitud['cotizacion_opp'])){
               echo "<a class='btn btn-success form-control' style='font-size:12px;color:white;height:30px;' href='".$solicitud['cotizacion_opp']."' target='_blank'><span class='glyphicon glyphicon-download' aria-hidden='true'></span> Download Quotation</a>";
               if($proceso_certificacion['estatus_dspp'] == 5){ // SE ACEPTA LA COTIZACIÓN
                echo "<p class='alert alert-success' style='padding:7px;'>Status: ".$proceso_certificacion['nombre_dspp']."</p>"; 
               }else if($proceso_certificacion['estatus_dspp'] == 17){ // SE RECHAZA LA COTIZACIÓN
                echo "<p class='alert alert-danger' style='padding:7px;'>Status: ".$proceso_certificacion['nombre_dspp']."</p>"; 
               }else{
                echo "<p class='alert alert-info' style='padding:7px;'>Status: ".$proceso_certificacion['nombre_dspp']."</p>"; 
               }

            }else{ // INICIA CARGAR COTIZACIÓN
              echo "Not available";
            } // TERMINA CARGAR COTIZACIÓN
             ?>
          </td>
          <td>
            <?php
            if($solicitud['tipo_solicitud'] == 'RENOVACION'){
            ?>
              <a href="#" data-toggle="tooltip" title="This Application is in Process of Renewal of the Registry therefore does not apply the period of objection" style="padding:7px;"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>APPLICATION FOR RENEWAL</a>
            <?php
            }else{
              if(isset($solicitud['idperiodo_objecion']) && $solicitud['estatus_objecion'] != 'EN ESPERA'){
              ?>
                <p class="alert alert-info" style="margin-bottom:0;padding:0px;">Start: <?php echo date('d/m/Y', $solicitud['fecha_inicio']); ?></p>
                <p class="alert alert-danger" style="margin-bottom:0;padding:0px;">End: <?php echo date('d/m/Y', $solicitud['fecha_fin']); ?></p>
                <?php 
                if(isset($solicitud['documento'])){
                ?>
                  <p class="alert alert-success" style="margin-bottom:0;padding:0px;">Resolution: <?php echo $solicitud['dictamen']; ?></p>
                  <a class="btn btn-info" style="font-size:12px;width:100%;height:30px;" href='<?php echo $solicitud['documento']; ?>' target='_blank'><span class='glyphicon glyphicon-download' aria-hidden='true'></span> Download Resolution</a> 
                <?php
                }
                ?>
              <?php
              }else{
                echo "Not available";
              }
            }
            ?>
          </td>
          <!---- INICIA PROCESO DE CERTIFICACIÓN ---->
          <td>
            <form action="" method="POST" enctype="multipart/form-data">
              <?php 
              if(isset($solicitud['dictamen']) && $solicitud['dictamen'] == 'POSITIVO' || ($solicitud['tipo_solicitud']) == 'RENOVACION' && !empty($solicitud['fecha_aceptacion'])){
              ?>
                <button type="button" class="btn btn-sm btn-primary" style="width:100%" data-toggle="modal" data-target="<?php echo "#certificacion".$solicitud['idsolicitud_certificacion']; ?>">Certification process</button>

                <!-- inicia modal proceso de certificación -->
                <div id="<?php echo "certificacion".$solicitud['idsolicitud_certificacion']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                  <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Proceso de Certificación</h4>
                      </div>
                      <div class="modal-body"><!-- INICIA MODAL BODY -->
                        <div class="row"><!--INICIA ROW-->
                          <?php 

                          $row_proceso_certificacion = mysql_query("SELECT proceso_certificacion.*, estatus_interno.nombre_ingles FROM proceso_certificacion INNER JOIN estatus_interno ON proceso_certificacion.estatus_interno = estatus_interno.idestatus_interno WHERE proceso_certificacion.idsolicitud_certificacion = '$solicitud[idsolicitud]' AND proceso_certificacion.estatus_interno IS NOT NULL", $dspp) or die(mysql_error());
                          while($historial_certificacion = mysql_fetch_assoc($row_proceso_certificacion)){
                            echo "<div class='col-md-10'>Proceso: $historial_certificacion[nombre]</div>";
                            echo "<div class='col-md-2'>Fecha: ".date('d/m/Y',$historial_certificacion['fecha_registro'])."</div>";
                          }

                          if(!isset($solicitud['idcomprobante_pago'])){
                          ?>
                          <div class="col-md-12">
                            <select class="form-control" name="estatus_interno" id="<?php echo 'statusSelect'.$solicitud['idsolicitud']; ?>" onchange="<?php echo 'funcionSelect'.$solicitud['idsolicitud'].'()'; ?>">
                              <option value="">Select the process you are in</option>
                              <?php 
                              $row_estatus_interno = mysql_query("SELECT * FROM estatus_interno",$dspp) or die(mysql_error());
                              while($estatus_interno = mysql_fetch_assoc($row_estatus_interno)){
                                echo "<option value='$estatus_interno[idestatus_interno]'>$estatus_interno[nombre_ingles]</option>";
                              }
                               ?>
                            </select>                        
                          </div>

                          <?php
                          }
                          ?>

                              <div class="col-xs-12" id="<?php echo 'divSelect'.$solicitud['idsolicitud']; ?>" style="margin-top:10px;">
                                <div class="col-xs-6">
                                  <input style="display:none" id="<?php echo 'nombreArchivo'.$solicitud['idsolicitud']; ?>"  type='text' class='form-control' name='nombre_archivo' placeholder="File name"/>
                                </div>
                                <!-- INICIA CARGAR ARCHIVO ESTATUS -->
                                <div class="col-xs-6">
                                  <input style="display:none" id="<?php echo 'archivo_estatus'.$solicitud['idsolicitud']; ?>" type='file' class='form-control' name='archivo_estatus' />
                                </div>
                                <!-- TERMINA CARGAR ARCHIVO ESTATUS -->

                                <!-- INICIA ACCION PROCESO CERTIFICACION -->
                                <div class="col-md-12">
                                  <textarea id="<?php echo 'registroOculto'.$solicitud['idsolicitud']; ?>" style="display:none" name="mensaje_negativo" class="form-control textareaMensaje" cols="30" rows="10" placeholder="Write here"></textarea>  
                                </div>
                                

                                <!-- TERMINA ACCION PROCESO CERTIFICACION -->
                              </div>
                              
                              <!-------- INICIA VENTANA DICTAMEN POSITIVO ---------->
                              <div id="<?php echo 'tablaCorreo'.$solicitud['idsolicitud']; ?>" style="display:none"> 
                                <div class="col-xs-12"  style="margin-top:10px;">
                                  <?php 
                                  if($solicitud['tipo_solicitud'] == 'RENOVACION'){
                                  ?>
                                    <p class="alert alert-info">The following format will be sent shortly to the SPO</p>
                                    <div class="col-xs-12">
                                      
                                      <div class="col-xs-12">
                                        <div class="col-xs-6"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></div>
                                        <div class="col-xs-6">
                                          <p>Posted on: <?php echo date("d/m/Y", time()); ?></p>
                                          <p>for: <span style="color:red"><?php echo $solicitud['nombre_opp']; ?></span></p>
                                          <p>Email(s): <?php echo $solicitud['contacto1_email']." ".$solicitud['contacto2_email']." ".$solicitud['email']; ?></p>
                                          <p>Subject: <span style="color:red">Notification of the judgement - SPP</span></p>
                                          
                                        </div>
                                      </div>
                                      <div class="col-xs-12">
                                        <h4 style="font-size:14px;padding:5px;">SPO MESSAGE( <small style="font-size:13px;">The OC must write in the field below, the text on the Notification of the judgement and in case the judgement is positive, it must explain that the actor must read the attached documents and sign the User's Contract and Acknowledgment of Receipt. <span style="color:red">If you do not enter a message, the system will send a default message <a href="dictamen_positivo.php?opp=<?php echo $solicitud['nombre_opp'];?>&renovacion" target="ventana1" onclick="ventanaNueva ('', 500, 400, 'ventana1');">view message</a></span></small>)</h4>
                                        <textarea name="mensaje_renovacion" class="form-control textareaMensaje" id="" cols="30" rows="10" placeholder="Enter a message in case you need it"></textarea>

                                      </div>
                                    </div>

                                    <div class="col-xs-12">
                                      <div class="col-xs-12">
                                        <h4 style="font-size:14px;">ATTACHED FILES( <small>Attachments within email</small> )</h4>
                                        <?php 
                                          echo '<h5 style="font-size:12px;" class="alert alert-warning">
                                          <ul>
                                            <li>This Organization is in "Process of Renewal of the Certificate", therefore no "User\'s Contract" will be sent.</li>
                                            <li style="color:red;">The payment of the SPP Membership is considered a ratification of the signing of the contract.</li>
                                          </ul>
                                          </h5>';
                                         ?>
                                      </div>
                                      <div class="col-xs-12">
                                        <h5 class="alert alert-warning">SPP MEMBERSHIP: <span style="color:#7f8c8d">Indicate the total amount of the membership, as well as the type of currency.</span></h5>
                                        <p>Total Membership: <input type="text" class="form-control" name="monto_membresia" placeholder="Total Membership"></p>
                                      </div>

                                      <div class="col-xs-12">
                                        <h4 style="font-size:14px;">EXTRA FILE( <small><span style="color:red">*optional</span>. Attach some other file other than those sent by SPP GLOBAL</small>)</h4>
                                        <div class="col-xs-12">
                                          <input type="text" class="form-control" name="nombreArchivo" placeholder="File name">
                                        </div>
                                        <div class="col-xs-12">
                                          <input type="file" class="form-control" name="archivo_extra">
                                        </div>
                                      </div>
                                    </div>

                                  <?php
                                  }else{
                                  ?>
                                    <p class="alert alert-info">The following format will be sent shortly to the SPO</p>
                                    <div class="col-xs-12">
                                      
                                      <div class="col-xs-12">
                                        <div class="col-xs-6"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></div>
                                        <div class="col-xs-6">
                                          <p>Posted on: <?php echo date("d/m/Y", time()); ?></p>
                                          <p>for: <span style="color:red"><?php echo $solicitud['nombre_opp']; ?></span></p>
                                          <p>Email(s): <?php echo $solicitud['contacto1_email']." , ".$solicitud['contacto2_email']." , ".$solicitud['email']; ?></p>
                                          <p>Subject: <span style="color:red">Notification of the judgement - SPP</span></p>
                                          
                                        </div>
                                      </div>
                                      <div class="col-xs-12">
                                        <h4 style="font-size:14px;padding:5px;">SPO MESSAGE( <small style="font-size:13px;">The OC must write in the field below, the text on the Notification of the judgement and in case the judgement is positive, it must explain that the actor must read the attached documents and sign the User's Contract and Acknowledgment of Receipt. <span style="color:red">If you do not enter a message, the system will send a default message <a href="dictamen_positivo.php?opp=<?php echo $solicitud['nombre_opp'];?>" target="ventana1" onclick="ventanaNueva ('', 500, 400, 'ventana1');">view message</a></span></small>)</h4>
                                        <textarea name="mensajeOPP" class="form-control textareaMensaje" id="" cols="30" rows="10" placeholder="Enter a message in case you need it" ></textarea>

                                      </div>
                                    </div>
                                    <div class="col-xs-12">
                                      <div class="col-xs-12">
                                        <h4 style="font-size:14px;">ATTACHED FILES( <span style="color:#7f8c8d">Documentation sent to the actor once the Certification Process has completed with a Positive judgement.</span></h4>
                                        <?php 
                                        $row_documentacion = mysql_query("SELECT * FROM documentacion WHERE idestatus_interno = 8", $dspp) or die(mysql_error());
                                        while($documetacion = mysql_fetch_assoc($row_documentacion)){

                                          echo "<span class='glyphicon glyphicon-ok' aria-hidden='true'></span> <a href='$documetacion[archivo]' target='_blank'>$documetacion[nombre]</a><br>";
                                        }
                                         ?>
                                        <p class="alert alert-warning" style="padding:5px;">
                                          Select the language in which you want to send the files:
                                          <label class="radio-inline">
                                            <input type="radio" name="idioma" id="inlineRadio1" value="ESP"> Spanish
                                          </label>
                                          <label class="radio-inline">
                                            <input type="radio" name="idioma" id="inlineRadio2" value="EN"> English
                                          </label>
                                        </p>
      
                                      </div>
                                      <div class="col-xs-12">
                                        <h4 style="font-size:14px;">EXTRA FILE: <span style="color:#7f8c8d">Attach some other file if required.</span></h4>
                                        <div class="col-xs-12">
                                          <input type="text" class="form-control" name="nombreArchivo" placeholder="File name">
                                        </div>
                                        <div class="col-xs-12">
                                          <input type="file" class="form-control" name="archivo_extra">
                                        </div>
                                      </div>
                                      <div class="col-xs-12">
                                        <h4 style="font-size:14px;">SPP MEMBERSHIP: <span style="color:#7f8c8d">Indicate the total amount of the membership, as well as the type of currency.</span></h4>
                                        <input type="text" class="form-control" name="monto_membresia" placeholder="Total Membership">
                                      </div>
                                    </div>

                                  <?php
                                  }
                                  ?>
                                </div>
                              </div>
                              <!-------- TERMINA VENTANA DICTAMEN POSITIVO ---------->





                              <!-------- INICIA VENTANA DICTAMEN NEGATIVO ---------->
                              <div id="<?php echo 'tabla_negativo'.$solicitud['idsolicitud_certificacion']; ?>" style="display:none"> 
                                <div class="col-xs-12"  style="margin-top:10px;">
                                  <?php 
                                  if($solicitud['tipo_solicitud'] == 'RENOVACION'){
                                  ?>
                                    <p class="alert alert-info">The following format will be sent shortly to the SPO</p>
                                    <div class="col-xs-12">
                                      
                                      <div class="col-xs-12">
                                        <div class="col-xs-6"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></div>
                                        <div class="col-xs-6">
                                          <p>Posted on: <?php echo date("d/m/Y", time()); ?></p>
                                          <p>for: <span style="color:red"><?php echo $solicitud['nombre_opp']; ?></span></p>
                                          <p>Email(s): <?php echo $solicitud['contacto1_email']." ".$solicitud['contacto2_email']." ".$solicitud['email']; ?></p>
                                          <p>Subject: <span style="color:red">Notification of the judgement - SPP</span></p>
                                          
                                        </div>
                                      </div>
                                      <div class="col-xs-12">
                                        <h4 style="font-size:14px;padding:5px;">SPO MESSAGE( <small style="font-size:13px;">The OC must write in the field below, the text on the Notification of the judgement and in case the judgement is positive, it must explain that the actor must read the attached documents and sign the User's Contract and Acknowledgment of Receipt. <span style="color:red">If you do not enter a message, the system will send a default message <a href="dictamen_positivo.php?opp=<?php echo $solicitud['nombre_opp'];?>&renovacion" target="ventana1" onclick="ventanaNueva ('', 500, 400, 'ventana1');">view message</a></span></small>)</h4>
                                        <textarea name="mensaje_renovacion" class="form-control textareaMensaje" id="" cols="30" rows="10" placeholder="Enter a message in case you need it"></textarea>

                                      </div>
                                    </div>

                                    <div class="col-xs-12">
   

                                      <div class="col-xs-12">
                                        <h4 style="font-size:14px;">EXTRA FILE( <small><span style="color:red">*optional</span>. Attach some other file other than those sent by SPP GLOBAL</small>)</h4>
                                        <div class="col-xs-12">
                                          <input type="text" class="form-control" name="nombreArchivo" placeholder="File Name">
                                        </div>
                                        <div class="col-xs-12">
                                          <input type="file" class="form-control" name="archivo_extra">
                                        </div>
                                      </div>
                                    </div>

                                  <?php
                                  }else{
                                  ?>
                                    <p class="alert alert-info">The following format will be sent shortly to the SPO</p>
                                    <div class="col-xs-12">
                                      
                                      <div class="col-xs-12">
                                        <div class="col-xs-6"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></div>
                                        <div class="col-xs-6">
                                          <p>Posted on: <?php echo date("d/m/Y", time()); ?></p>
                                          <p>for: <span style="color:red"><?php echo $solicitud['nombre_opp']; ?></span></p>
                                          <p>Email(s): <?php echo $solicitud['contacto1_email']." , ".$solicitud['contacto2_email']." , ".$solicitud['email']; ?></p>
                                          <p>Subject: <span style="color:red">Notification of the judgement - SPP</span></p>
                                          
                                        </div>
                                      </div>
                                      <div class="col-xs-12">
                                        <h4 style="font-size:14px;padding:5px;">SPO MESSAGE( <small style="font-size:13px;">The OC must write in the field below, the text on the Notification of the judgement and in case the judgement is positive, it must explain that the actor must read the attached documents and sign the User's Contract and Acknowledgment of Receipt. <span style="color:red">If you do not enter a message, the system will send a default message <a href="dictamen_positivo.php?opp=<?php echo $solicitud['nombre_opp'];?>" target="ventana1" onclick="ventanaNueva ('', 500, 400, 'ventana1');">view message</a></span></small>)</h4>
                                        <textarea name="mensajeOPP" class="form-control textareaMensaje" id="" cols="30" rows="10" placeholder="Enter a message in case you need it" ></textarea>

                                      </div>
                                    </div>
                                    <div class="col-xs-12">
                                      <div class="col-xs-12">
                                        <h4 style="font-size:14px;">ATTACHED FILES( <span style="color:#7f8c8d">Documentation sent to the actor once the Certification Process has completed with a Positive judgement.</span></h4>
                                        <?php 
                                        $row_documentacion = mysql_query("SELECT * FROM documentacion WHERE idestatus_interno = 8", $dspp) or die(mysql_error());
                                        while($documetacion = mysql_fetch_assoc($row_documentacion)){

                                          echo "<span class='glyphicon glyphicon-ok' aria-hidden='true'></span> <a href='$documetacion[archivo]' target='_blank'>$documetacion[nombre]</a><br>";
                                        }
                                         ?>
                                        <p class="alert alert-warning" style="padding:5px;">
                                          Select the language in which you want to send the files:
                                          <label class="radio-inline">
                                            <input type="radio" name="idioma" id="inlineRadio1" value="ESP"> Spanish
                                          </label>
                                          <label class="radio-inline">
                                            <input type="radio" name="idioma" id="inlineRadio2" value="EN"> English
                                          </label>
                                        </p>
      
                                      </div>
                                      <div class="col-xs-12">
                                        <h4 style="font-size:14px;">EXTRA FILE: <span style="color:#7f8c8d">Attach some other file if required.</span></h4>
                                        <div class="col-xs-12">
                                          <input type="text" class="form-control" name="nombreArchivo" placeholder="File name">
                                        </div>
                                        <div class="col-xs-12">
                                          <input type="file" class="form-control" name="archivo_extra">
                                        </div>
                                      </div>
                                      <div class="col-xs-12">
                                        <h4 style="font-size:14px;">SPP MEMBERSHIP: <span style="color:#7f8c8d">Indicate the total amount of the membership, as well as the type of currency.</span></h4>
                                        <input type="text" class="form-control" name="monto_membresia" placeholder="Total Membership">
                                      </div>
                                    </div>

                                  <?php
                                  }
                                  ?>
                                </div>
                              </div>
                              <!-------- TERMINA VENTANA DICTAMEN NEGATIVO ---------->



                        </div><!--TERMINA ROW-->
                      </div><!-- TERMINA MODAL BODY -->

                      <?php
                          echo "<script>";
                          echo "function funcionSelect".$solicitud['idsolicitud']."() {";
                            echo "var valorSelect = document.getElementById('statusSelect".$solicitud['idsolicitud']."').value;";
                            echo "if(valorSelect == '4'){";
                              echo "document.getElementById('divSelect".$solicitud['idsolicitud']."').style.display = 'block';";
                              echo "document.getElementById('registroOculto".$solicitud['idsolicitud']."').style.display = 'block';";
                              echo "document.getElementById('nombreArchivo".$solicitud['idsolicitud']."').style.display = 'block';";
                              echo "document.getElementById('archivo_estatus".$solicitud['idsolicitud']."').style.display = 'block';";
                              echo "document.getElementById('tablaCorreo".$solicitud['idsolicitud']."').style.display = 'none';";
                              echo "document.getElementById('tabla_negativo".$solicitud['idsolicitud']."').style.display = 'none';";
                            echo "}else if(valorSelect == '6'){";
                              echo "document.getElementById('divSelect".$solicitud['idsolicitud']."').style.display = 'block';";
                              echo "document.getElementById('nombreArchivo".$solicitud['idsolicitud']."').style.display = 'block';";
                              echo "document.getElementById('archivo_estatus".$solicitud['idsolicitud']."').style.display = 'block';";
                              echo "document.getElementById('registroOculto".$solicitud['idsolicitud']."').style.display = 'block';";
                              echo "document.getElementById('tablaCorreo".$solicitud['idsolicitud']."').style.display = 'none';";
                              echo "document.getElementById('tabla_negativo".$solicitud['idsolicitud']."').style.display = 'none';";
                            echo "}else if(valorSelect == '7'){";
                              echo "document.getElementById('divSelect".$solicitud['idsolicitud']."').style.display = 'block';";
                              echo "document.getElementById('nombreArchivo".$solicitud['idsolicitud']."').style.display = 'none';";
                              echo "document.getElementById('archivo_estatus".$solicitud['idsolicitud']."').style.display = 'none';";
                              echo "document.getElementById('registroOculto".$solicitud['idsolicitud']."').style.display = 'block';";
                              echo "document.getElementById('tablaCorreo".$solicitud['idsolicitud']."').style.display = 'none';";
                              echo "document.getElementById('tabla_negativo".$solicitud['idsolicitud']."').style.display = 'none';";
                            echo "}else if(valorSelect == '8'){";
                              echo "document.getElementById('tablaCorreo".$solicitud['idsolicitud']."').style.display = 'block';"; 
                              echo "document.getElementById('nombreArchivo".$solicitud['idsolicitud']."').style.display = 'none';";
                              echo "document.getElementById('archivo_estatus".$solicitud['idsolicitud']."').style.display = 'none';";
                              echo "document.getElementById('registroOculto".$solicitud['idsolicitud']."').style.display = 'none';";
                              echo "document.getElementById('tabla_negativo".$solicitud['idsolicitud']."').style.display = 'none';";
                            echo "}else if(valorSelect == '9'){";
                              echo "document.getElementById('tabla_negativo".$solicitud['idsolicitud']."').style.display = 'block';"; 
                              echo "document.getElementById('nombreArchivo".$solicitud['idsolicitud']."').style.display = 'none';";
                              echo "document.getElementById('archivo_estatus".$solicitud['idsolicitud']."').style.display = 'none';";
                              echo "document.getElementById('tablaCorreo".$solicitud['idsolicitud']."').style.display = 'none';";
                              echo "document.getElementById('registroOculto".$solicitud['idsolicitud']."').style.display = 'none';";                 
                            echo "}else if(valorSelect == '23'){";
                              echo "document.getElementById('divSelect".$solicitud['idsolicitud']."').style.display = 'block';";
                              echo "document.getElementById('tablaCorreo".$solicitud['idsolicitud']."').style.display = 'none';";
                              echo "document.getElementById('nombreArchivo".$solicitud['idsolicitud']."').style.display = 'none';"; 
                              echo "document.getElementById('archivo_estatus".$solicitud['idsolicitud']."').style.display = 'none';";
                              echo "document.getElementById('registroOculto".$solicitud['idsolicitud']."').style.display = 'block';";    
                              echo "document.getElementById('tabla_negativo".$solicitud['idsolicitud']."').style.display = 'none';";            
                            echo "}else{";
                              echo "document.getElementById('nombreArchivo".$solicitud['idsolicitud']."').style.display = 'none';";
                              echo "document.getElementById('archivo_estatus".$solicitud['idsolicitud']."').style.display = 'none';";
                              echo "document.getElementById('registroOculto".$solicitud['idsolicitud']."').style.display = 'none';";
                              echo "document.getElementById('tablaCorreo".$solicitud['idsolicitud']."').style.display = 'none';";
                              echo "document.getElementById('divSelect".$solicitud['idsolicitud']."').style.display = 'none';";
                              echo "document.getElementById('tabla_negativo".$solicitud['idsolicitud']."').style.display = 'none';";
                            echo "}";
                          echo "}";
                          echo "</script>";
                      ?>
                      <div class="modal-footer">
                        <input type="hidden" name="idsolicitud_certificacion" value="<?php echo $solicitud['idsolicitud_certificacion']; ?>">
                        <input type="hidden" name="idoc" value="<?php echo $solicitud['idoc']; ?>">
                        <input type="hidden" name="idopp" value="<?php echo $solicitud['idopp']; ?>">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <?php 
                        if(empty($solicitud['idmembresia'])){
                        ?>
                        <button type="submit" class="btn btn-success" name="guardar_proceso" value="1">Save Process</button>
                        <?php
                        }
                         ?>
                        <!--<button type="button" class="btn btn-primary">Guardar Cambios</button>-->
                      </div>
                    </div>
                  </div>
                </div>
                <!-- termina modal proceso de certificación -->

              <?php
              }else{
                echo "Not available";
              }
               ?>
               <input type="hidden" name="tipo_solicitud" value="<?php echo $solicitud['tipo_solicitud']; ?>">
               <input type="hidden" name="idsolicitud_certificacion" value="<?php echo $solicitud['idsolicitud']; ?>">
            </form>
          </td>
          <!---- TERMINA PROCESO DE CERTIFICACIÓN ---->

          <!---- INICIA SECCION CERTIFICADO ------>
          <form action="" method="POST" enctype="multipart/form-data">
          <td>
            <button type="button" class="btn btn-sm btn-info" style="width:100%" data-toggle="modal" data-target="<?php echo "#certificado".$solicitud['idsolicitud']; ?>">Upload Certificate</button>
          </td>
                <!-- inicia modal estatus_Certificado -->

                <div id="<?php echo "certificado".$solicitud['idsolicitud']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                  <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="myModalLabel">Upload Certificate</h4>
                      </div>
                      <div class="modal-body">
                        <div class="row">
                          <div class="col-md-6">
                            <?php 
                            if($solicitud['tipo_solicitud'] == 'RENOVACION'){
                              if($proceso_certificacion['estatus_membresia'] == "APROBADA"){
                                if(isset($solicitud['idformato_evaluacion']) && isset($solicitud['idinforme_evaluacion']) && isset($solicitud['iddictamen_evaluacion'])){

                                  $row_formato = mysql_query("SELECT * FROM formato_evaluacion WHERE idformato_evaluacion = $solicitud[idformato_evaluacion]", $dspp) or die(mysql_error());
                                  $formato = mysql_fetch_assoc($row_formato);
                                  $row_informe = mysql_query("SELECT * FROM informe_evaluacion WHERE idinforme_evaluacion = $solicitud[idinforme_evaluacion]", $dspp) or die(mysql_error());
                                  $informe = mysql_fetch_assoc($row_informe);
                                  $row_dictamen = mysql_query("SELECT * FROM dictamen_evaluacion WHERE iddictamen_evaluacion = $solicitud[iddictamen_evaluacion]", $dspp) or die(mysql_error());
                                  $dictamen = mysql_fetch_assoc($row_dictamen);

                                ?>
                                <p>Status of the Evaluation Format: <span style="color:red"><?php echo $formato['estatus_formato']; ?></span></p>
                                <a href="<?php echo $formato['archivo']; ?>" class="btn btn-info" style="width:100%" target="_blank">Download Evaluation Format</a>

                                <p>Status of the Evaluation Judgement: <span style="color:red"><?php echo $dictamen['estatus_dictamen']; ?></span></p>
                                <a href="<?php echo $dictamen['archivo']; ?>" class="btn btn-info" style="width:100%" target="_blank">Download Judgement</a>
                                <p>Status of the Evaluation Report: <span style="color:red"><?php echo $informe['estatus_informe']; ?></span></p>
                                <a href="<?php echo $informe['archivo']; ?>" class="btn btn-info" style="width:100%" target="_blank">Download Report</a>

                                <?php
                                }else{
                                ?>
                                  <p class="alert alert-info">Please upload the following documents:</p>
                                  <p class="alert alert-info">

                                    Evaluation Format
                                    <input type="file" class="form-control" name="formato_evaluacion" >

                                    Evaluation report
                                    <input type="file" class="form-control" name="informe_evaluacion" >
                       
                                    Dictamen de Evaluación
                                    <input type="file" class="form-control" name="dictamen_evaluacion" >

                                  </p>
                                  <button type="submit" class="btn btn-success" style="width:100%" name="cargar_documentos" value="1">Send documents</button>
                                <?php
                                }

                              }else{
                                echo "<p class='alert alert-danger'>The \"Membership\" has not yet been \"Approved\" </p>";
                              }
                            }else{
                              if($solicitud['estatus_contrato'] == 'ACEPTADO' && $proceso_certificacion['estatus_membresia'] == "APROBADA"){
                                if(isset($solicitud['idformato_evaluacion']) && isset($solicitud['idinforme_evaluacion']) && isset($solicitud['iddictamen_evaluacion'])){

                                  $row_formato = mysql_query("SELECT * FROM formato_evaluacion WHERE idformato_evaluacion = $solicitud[idformato_evaluacion]", $dspp) or die(mysql_error());
                                  $formato = mysql_fetch_assoc($row_formato);
                                  $row_informe = mysql_query("SELECT * FROM informe_evaluacion WHERE idinforme_evaluacion = $solicitud[idinforme_evaluacion]", $dspp) or die(mysql_error());
                                  $informe = mysql_fetch_assoc($row_informe);
                                  $row_dictamen = mysql_query("SELECT * FROM dictamen_evaluacion WHERE iddictamen_evaluacion = $solicitud[iddictamen_evaluacion]", $dspp) or die(mysql_error());
                                  $dictamen = mysql_fetch_assoc($row_dictamen);

                                ?>
                                <p>Status of the Evaluation Format: <span style="color:red"><?php echo $formato['estatus_formato']; ?></span></p>
                                <a href="<?php echo $formato['archivo']; ?>" class="btn btn-info" style="width:100%" target="_blank">Download Evaluation Format</a>

                                <p>Status of the Evaluation Opinion: <span style="color:red"><?php echo $dictamen['estatus_dictamen']; ?></span></p>
                                <a href="<?php echo $dictamen['archivo']; ?>" class="btn btn-info" style="width:100%" target="_blank">Download Judgement</a>
                                <p>Status of the Evaluation Report: <span style="color:red"><?php echo $informe['estatus_informe']; ?></span></p>
                                <a href="<?php echo $informe['archivo']; ?>" class="btn btn-info" style="width:100%" target="_blank">Download Report</a>

                                <?php
                                }else{
                                ?>
                                  <p class="alert alert-info">Please upload the following documents:</p>
                                  <p class="alert alert-info">

                                    Evaluation Format
                                    <input type="file" class="form-control" name="formato_evaluacion" >

                                    Evaluation report
                                    <input type="file" class="form-control" name="informe_evaluacion" >
                       
                                    Dictamen de Evaluación
                                    <input type="file" class="form-control" name="dictamen_evaluacion" >

                                  </p>
                                  <button type="submit" class="btn btn-success" style="width:100%" name="cargar_documentos" value="1">Send documents</button>
                                <?php
                                }

                              }else{
                                echo "<p class='alert alert-danger'>The \"Membership\" has not yet been \"Approved\" and neither has \"Approved\" the membership</p>";
                              }
                            }
                             ?>
                          </div>
                          
                          <div class="col-md-6">
                            <h4 style="font-size:14px;">Upload Certificate</h4>
                            <?php 
                            if(isset($solicitud['iddictamen_evaluacion']) && isset($solicitud['idformato_evaluacion']) && isset($solicitud['idinforme_evaluacion'])){

                                $row_formato = mysql_query("SELECT * FROM formato_evaluacion WHERE idformato_evaluacion = $solicitud[idformato_evaluacion]", $dspp) or die(mysql_error());
                                $formato = mysql_fetch_assoc($row_formato);
                                $row_informe = mysql_query("SELECT * FROM informe_evaluacion WHERE idinforme_evaluacion = $solicitud[idinforme_evaluacion]", $dspp) or die(mysql_error());
                                $informe = mysql_fetch_assoc($row_informe);
                                $row_dictamen = mysql_query("SELECT * FROM dictamen_evaluacion WHERE iddictamen_evaluacion = $solicitud[iddictamen_evaluacion]", $dspp) or die(mysql_error());
                                $dictamen = mysql_fetch_assoc($row_dictamen);

                                if($formato['estatus_formato'] == 'ACEPTADO' && $informe['estatus_informe'] == 'ACEPTADO' && $dictamen['estatus_dictamen'] == 'ACEPTADO'){
                                  if(isset($solicitud['idcertificado'])){
                                    $row_certificado = mysql_query("SELECT * FROM certificado WHERE idcertificado = $solicitud[idcertificado]", $dspp) or die(mysql_error());
                                    $certificado = mysql_fetch_assoc($row_certificado);
                                    $inicio = strtotime($certificado['vigencia_inicio']);
                                    $fin = strtotime($certificado['vigencia_fin']);
                                  ?>
                                    <p class="alert alert-info">The certificate has been loaded, which has a validity of: <b><?php echo date('d/m/Y', $inicio); ?></b> to <b><?php echo date('d/m/Y', $fin); ?></b></p>
                                    <a href="<?php echo $certificado['archivo']; ?>" class="btn btn-success" style="width:100%" target="_blank">Download Certificate</a>
                                  <?php
                                  }else{
                                  ?>
                                    <div class="col-md-12">
                                      <p class="alert alert-info">Please set the Certificate Start and End date.</p>
                                    </div>
                                    <div class="col-md-6">
                                      <label for="fecha_inicio">Start Date</label> 
                                      <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" placeholder="dd/mm/aaaa" required> 
                                    </div>
                                    <div class="col-md-6">
                                      <label for="fecha_fin">End Date</label>
                                      <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" placeholder="dd/mm/aaaa" required>
                                    </div>
                                    
                                    <label for="certificado">Please select Certificate</label>
                                    <input type="file" name="certificado" id="certificado" class="form-control" required>
                                    <button type="submit" name="enviar_certificado" value="1" class="btn btn-success" style="width:100%">Send Certificate</button>  
                                  <?php
                                  }
                                }else{
                                  echo '<p class="alert alert-warning">
                                  Once the "Evaluation Report" has been approved and the "Evaluation Judgement" can load the Certificate.
                              </p> ';
                                }

                            }else{
                              echo '<p class="alert alert-warning">
                                Once the "Evaluation Report" has been approved and the "Evaluation Judgement" can load the Certificate.
                              </p> ';
                            }
                            ?>
                          </div>
                        </div>
                      </div>

                      <div class="modal-footer">
                        <input type="hidden" name="tipo_solicitud" value="<?php echo $solicitud['tipo_solicitud']; ?>">

                        <input type="hidden" name="idsolicitud_certificacion" value="<?php echo $solicitud['idsolicitud']; ?>">
                        <input type="hidden" name="idoc" value="<?php echo $solicitud['idoc']; ?>">
                        <input type="hidden" name="idopp" value="<?php echo $solicitud['idopp']; ?>">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- termina modal estatus_Certificado -->

          <!---- TERMINA SECCION CERTIFICADO ------>
            </form>
          <td>
            <a class="btn btn-sm btn-primary" data-toggle="tooltip" title="Consult Application" href="?SOLICITUD&IDsolicitud=<?php echo $solicitud['idsolicitud']; ?>">Consult</a>
          </td>
          <td>
            <form action="../../reportes/solicitud.php" method="POST" target="_new">
              <button class="btn btn-xs btn-default" data-toggle="tooltip" title="Download Application" target="_new" type="submit" ><img src="../../img/pdf.png" style="height:30px;" alt=""></button>

              <input type="hidden" name="idsolicitud_certificacion" value="<?php echo $solicitud['idsolicitud']; ?>">
              <input type="hidden" name="generar_formato" value="1">
            </form>
          </td>

        </tr>
      <?php
      }
      ?>
      </tbody>
    </table>
  </div>
</div>
<script type="text/javascript">
<!--
function ventanaNueva(documento,ancho,alto,nombreVentana){
    window.open(documento, nombreVentana,'width=' + ancho + ', height=' + alto);
}
     
//-->
</script>
<!--<table>
<tr>
<td width="20"><?php if ($pageNum_opp > 0) { // Show if not first page ?>
<a href="<?php printf("%s?pageNum_opp=%d%s", $currentPage, 0, $queryString_opp); ?>">
<span class="glyphicon glyphicon-fast-backward" aria-hidden="true"></span>
</a>
<?php } // Show if not first page ?></td>
<td width="20"><?php if ($pageNum_opp > 0) { // Show if not first page ?>
<a href="<?php printf("%s?pageNum_opp=%d%s", $currentPage, max(0, $pageNum_opp - 1), $queryString_opp); ?>">
<span class="glyphicon glyphicon-backward" aria-hidden="true"></span>
</a>
<?php } // Show if not first page ?></td>
<td width="20"><?php if ($pageNum_opp < $totalPages_opp) { // Show if not last page ?>
<a href="<?php printf("%s?pageNum_opp=%d%s", $currentPage, min($totalPages_opp, $pageNum_opp + 1), $queryString_opp); ?>">
<span class="glyphicon glyphicon-forward" aria-hidden="true"></span>
</a>
<?php } // Show if not last page ?></td>
<td width="20"><?php if ($pageNum_opp < $totalPages_opp) { // Show if not last page ?>
<a href="<?php printf("%s?pageNum_opp=%d%s", $currentPage, $totalPages_opp, $queryString_opp); ?>">
<span class="glyphicon glyphicon-fast-forward" aria-hidden="true"></span>
</a>
<?php } // Show if not last page ?></td>
</tr>
</table>-->