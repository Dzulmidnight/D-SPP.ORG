<?php 
require_once('../Connections/dspp.php'); 
require_once('../Connections/mail.php');
      /*include_once("../../PHPMailer/class.phpmailer.php");
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
        $mail->AddReplyTo("cert@spp.coop");*/
?>
<?php
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

if(isset($_POST['cancelar']) && $_POST['cancelar'] == "cancelar"){
  $idsolicitud_certificacion = $_POST['idsolicitud_certificacion'];
  $estatus_interno = 24;  

  $updateSQL = "UPDATE solicitud_certificacion SET status = $estatus_interno WHERE idsolicitud_certificacion = $idsolicitud_certificacion";
  $ejecutar = mysql_query($updateSQL,$dspp) or die(mysql_error());
}


$currentPage = $_SERVER["PHP_SELF"];

$maxRows_opp = 20;
$pageNum_opp = 0;
if (isset($_GET['pageNum_opp'])) {
  $pageNum_opp = $_GET['pageNum_opp'];
}
$startRow_opp = $pageNum_opp * $maxRows_opp;

mysql_select_db($database_dspp, $dspp);
if(isset($_GET['query'])){
  $query_opp = "SELECT opp.* ,solicitud_certificacion.* FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE solicitud_certificacion.idoc = $_SESSION[idoc] AND solicitud_certificacion.status != 24 ORDER BY solicitud_certificacion.fecha_elaboracion DESC";

  #$query_opp = "SELECT * FROM solicitud_certificacion where idsolicitud_certificacion ='".$_GET['query']."' ORDER BY fecha DESC";
}else{
  #SELECT solicitud_certificacion.* FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE opp.idopp = 15

  $query_opp = "SELECT opp.* ,solicitud_certificacion.* FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE solicitud_certificacion.idoc = $_SESSION[idoc] AND solicitud_certificacion.status != 24 ORDER BY solicitud_certificacion.fecha_elaboracion DESC"; 

  #$query_opp = "SELECT opp.* ,solicitud_certificacion.* FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp ORDER BY solicitud_certificacion.fecha_elaboracion ASC";  

  #$query_opp = "SELECT * FROM solicitud_certificacion ORDER BY fecha ASC";
}
  //DEFINO LA RUTA DEL FORMATO DE anexo
  $rutaArchivo = "../../archivos/ocArchivos/anexos/";


if(isset($_POST['statusCertificado']) && $_POST['statusCertificado'] == "statusCertificado"){
  $fecha = date("d/m/Y", $_POST['statusFecha']);
  $fecha_actual = time();
  $idopp = $_POST['idopp'];
  $idoc = $_POST['idoc'];
  $status = $_POST['status'];
  $idsolicitud = $_POST['idsolicitud'];
  $registro1 = $_POST['registro1'];
  $query = "INSERT INTO certificado (status, idsolicitud, idopp, entidad) VALUES ('$status', $idsolicitud, $idopp, $idoc)";
  $certificado = mysql_query($query, $dspp) or die(mysql_error());

  $idcertificado = mysql_insert_id($dspp);
  $identificador = "CERTIFICADO";
  $idexterno = $idsolicitud;
    $archivoDictamen = '';
    $archivoExtra = '';

 /* $nombreArchivoEstatus = $_POST['nombreArchivoEstatus'];
  $archivoExtra = $_POST['nombreArchivo'];*/

    
    if(isset($_POST['montoMembresia'])){
      $montoMembresia = $_POST['montoMembresia'];
    }else{
      $montoMembresia = NULL;
    }

    $mensajeOPP = $_POST['mensajeOPP'];
    $archivoDictamen = '';
    $archivoExtra = '';




    if(!empty($_POST['nombreArchivoEstatus'])){
      $nombreArchivo = $_POST['nombreArchivoEstatus'];
    }else{
      $nombreArchivo = $_POST['nombreArchivo'];
    }


    //AL NOMBRE DEL ARCHIVO(FORMATO) LE CONCATENO EL TIME
    if(!empty($_FILES['archivoDictamen']['name'])){
        $_FILES["archivoDictamen"]["name"];
          move_uploaded_file($_FILES["archivoDictamen"]["tmp_name"], $rutaArchivo.time()."_".$_FILES["archivoDictamen"]["name"]);
          $archivoDictamen = $rutaArchivo.basename(time()."_".$_FILES["archivoDictamen"]["name"]);
    }else{
      $archivoDictamen = NULL;
    }
    //AL NOMBRE DEL ARCHIVO(FORMATO) LE CONCATENO EL TIME
    if(!empty($_FILES['archivoExtra']['name'])){
        $_FILES["archivoExtra"]["name"];
          move_uploaded_file($_FILES["archivoExtra"]["tmp_name"], $rutaArchivo.time()."_".$_FILES["archivoExtra"]["name"]);
          $archivoExtra = $rutaArchivo.basename(time()."_".$_FILES["archivoExtra"]["name"]);
    }else{
      $archivoExtra = NULL;
    }

      if(!empty($archivoDictamen)){
        $adjuntoEstatus = $archivoDictamen;
      }else{
        $adjuntoEstatus = $archivoExtra;
      }

    $query = "INSERT INTO proceso_certificacion(idstatus,idopp,idoc,idsolicitud_certificacion,registro,archivo,nombre,fecha) VALUES($status,$idopp,$idoc,$idsolicitud,'$registro1','$adjuntoEstatus','$nombreArchivo',$fecha_actual)";
    $proceso_certificacion = mysql_query($query,$dspp) or die(mysql_error());



  $queryFecha = "INSERT INTO fecha (fecha, idexterno, idopp, idoc, idcertificado, identificador, status) VALUES($fecha_actual, $idexterno, $idopp, $idoc, $idcertificado, '$identificador', '$status')"; 
  $insertarFecha = mysql_query($queryFecha, $dspp) or die(mysql_error());

  $query = "UPDATE solicitud_certificacion SET status = '$status' WHERE idsolicitud_certificacion = $idsolicitud";
  $actualizar = mysql_query($query,$dspp) or die(mysql_error());


    if($status == 8){
        $asunto = "Contrato de uso del Simbolo de Pequeños Productores - SPP";

        $querySolicitud = "SELECT solicitud_certificacion.idopp,solicitud_certificacion.p1_email, solicitud_certificacion.p2_email,opp.idopp, opp.email FROM solicitud_certificacion LEFT JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE idsolicitud_certificacion = $idsolicitud";
        $rowSolicitud = mysql_query($querySolicitud,$dspp) or die(mysql_error());
        $datosSolicitud = mysql_fetch_assoc($rowSolicitud);

/*        $destinatarios .= $datosSolicitud['p1_email'];
        $destinatarios .= $datosSolicitud['p2_email'];
        $destinatarios .= $datosSolicitud['email'];
  */    
        $mail->AddAddress($datosSolicitud['p1_email']);
        $mail->AddAddress($datosSolicitud['p2_email']);
        $mail->AddAddress($datosSolicitud['email']);




      $anexoNombres = "";
      $query_anexos = "SELECT * FROM anexos WHERE idstatus_interno = 8";
      $row_anexos = mysql_query($query_anexos,$dspp) or die(mysql_error());

      while($datos_anexos = mysql_fetch_assoc($row_anexos)){
        $mail->AddAttachment($datos_anexos['archivo']);
        $anexoNombres .= "<li>".$datos_anexos['anexo']."</li>";
      }

      $queryProceso_certificacion = "SELECT * FROM proceso_certificacion WHERE idstatus = $status AND idoc = $idoc";
      $row_proceso_certificacion = mysql_query($queryProceso_certificacion,$dspp) or die(mysql_error());

     while($archivoProceso = mysql_fetch_assoc($row_proceso_certificacion)){
        $mail->AddAttachment($archivoProceso['archivo']);
        $anexoNombres .= "<li>".utf8_decode($archivoProceso['nombre'])."</li>";
      }

      $mensajeDefault = "";
      if(!empty($mensajeOPP)){
        $mensajeDefault = $mensajeOPP;
      }else{
        $mensajeDefault = "<p>Reciban ustedes un cordial y atento saludo, así como el deseo de éxito en todas y cada una de sus actividades</p>
                    <p>La presente tiene por objetivo hacerles llegar el documentro <strong>Contrato de Uso del Simbolo de Pequeños Productores y Acuse de Recibido</strong>; documentos que se requieren sean leidos y entendidos, una vez revisada la información de los documentos mencionados, por favor proceder a firmarlos y envíar los mismos por este medio.</p>
                    <p>El Contrato de Uso menciona como anexo el documento Manual del SPP y este Manual a su vez menciona como anexos los siguientes documentos.</p>";
      }

        $mensaje = '
          <html>
          <head>
            <meta charset="utf-8">
            
            
          </head>
          <body>
            <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
              <tbody>
                <tr>
                  <th rowspan="2" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                  <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">Contrato de uso del Simbolo de Pequeños Productores - SPP</span></p></th>

                </tr>
                <tr>
                 <th scope="col" align="left" width="280"><p>Para: <span style="color:red">'.$_POST['nombreOPP'].'</span></p></th>
                </tr>

                <tr>
                  <td colspan="2">
                    '.$mensajeDefault.'
                  </td>
                </tr>
                <tr>
                  <td><p><strong>Documentos Anexos</strong></p></td>
                </tr>
                <tr>
                  <td>
                    <ul>
                      '.$anexoNombres.'
                    </ul>
                  </td>
                </tr>
                <tr>
                  <td>
                    <p><strong>Memresía SPP</strong></p>
                    <p>Asi mismo se anexan los datos bancarios para el respectivo pago de la membresía SPP</p>
                    <p>El monto total de la membresía SPP es de: <span style="color:red;">'.$montoMembresia.'</span></p>
                  </td>

                </tr>
              </tbody>
            </table>
          </body>
          </html>
        ';


        //$mail->Username = "soporte@d-spp.org";
        //$mail->Password = "/aung5l6tZ";
        $mail->Subject = utf8_decode($asunto);
        $mail->Body = utf8_decode($mensaje);
        $mail->MsgHTML(utf8_decode($mensaje));

        

        if($mail->Send()){
          
          echo "<script>alert('Correo enviado Exitosamente.');location.href ='javascript:history.back()';</script>";
        }else{
              echo "<script>alert('Error, no se pudo enviar el correo');location.href ='javascript:history.back()';</script>";
     
        }
        $mail->ClearAddresses();

    }


}

if (isset($_POST['actualizarStatus']) && $_POST['actualizarStatus'] == "actualizarStatus") {
    $fecha_actual = time();
    $fecha = date("d/m/Y", time());
    $idopp = $_POST['idopp'];
    $idoc = $_POST['idoc'];
    $idsolicitud = $_POST['idsolicitud'];
    $idcertificado = $_POST['idcertificado'];
    $status = $_POST['status'];    
    $query = "UPDATE certificado SET status = '$status' WHERE idcertificado = $idcertificado";
    $actualizar = mysql_query($query, $dspp) or die(mysql_error());
    /*$nombreArchivo = $_POST['nombreArchivo'];
    $nombreArchivoEstatus = $_POST['nombreArchivoEstatus'];*/
    

    if(!empty($_POST['nombreArchivoEstatus'])){
      $nombreArchivo = $_POST['nombreArchivoEstatus'];
    }else{
      $nombreArchivo = $_POST['nombreArchivo'];
    }


    $registro1 = $_POST['registro1'];
    
    if(isset($_POST['montoMembresia'])){
      $montoMembresia = $_POST['montoMembresia'];
    }else{
      $montoMembresia = NULL;
    }

    $mensajeOPP = $_POST['mensajeOPP'];
    $archivoDictamen = '';
    $archivoExtra = '';


    $anexoNombres = "";
    $query_anexos = "SELECT * FROM anexos WHERE idstatus_interno = 8";
    $row_anexos = mysql_query($query_anexos,$dspp) or die(mysql_error());

    /*while($datos_anexos = mysql_fetch_assoc($row_anexos)){
      $anexoNombres .= "<p><i class='fa fa-check-circle-o fa-2x'></i><a href=http://d-spp.org/".utf8_decode($datos_anexos['archivo']).">".$datos_anexos['anexo']."</a></p>";
    }*/

        /*$destinatarioADM = "";
        $query = "SELECT  email FROM adm";
        $ejecutar = mysql_query($query,$dspp) or die(mysql_error());

        while($emailADM = mysql_fetch_assoc($ejecutar)){  
          if($emailADM['email'] != "isc.jesusmartinez@gmail.com"){
            $destinatarioADM .= $emailADM['email'].',';
          }
        }*/



    //AL NOMBRE DEL ARCHIVO(FORMATO) LE CONCATENO EL TIME

    if(!empty($_FILES['archivoDictamen']['name'])){
        $_FILES["archivoDictamen"]["name"];
          move_uploaded_file($_FILES["archivoDictamen"]["tmp_name"], $rutaArchivo.time()."_".$_FILES["archivoDictamen"]["name"]);
          $archivoDictamen = $rutaArchivo.basename(time()."_".$_FILES["archivoDictamen"]["name"]);
    }else{
      $archivoDictamen = NULL;
    }

    if(!empty($_FILES['archivoExtra']['name'])){
        $_FILES["archivoExtra"]["name"];
          move_uploaded_file($_FILES["archivoExtra"]["tmp_name"], $rutaArchivo.time()."_".$_FILES["archivoExtra"]["name"]);
          $archivoExtra = $rutaArchivo.basename(time()."_".$_FILES["archivoExtra"]["name"]);
    }else{
      $archivoExtra = NULL;
    }

      if(!empty($archivoDictamen)){
        $adjuntoEstatus = $archivoDictamen;
      }else{
        $adjuntoEstatus = $archivoExtra;
      }

      $query = "INSERT INTO proceso_certificacion(idstatus,idopp,idoc,idsolicitud_certificacion,registro,archivo,nombre,fecha) VALUES($status,$idopp,$idoc,$idsolicitud,'$registro1','$adjuntoEstatus','$nombreArchivo',$fecha_actual)";
      $proceso_certificacion = mysql_query($query,$dspp) or die(mysql_error());


    $idexterno = $idsolicitud;
    $identificador = "CERTIFICADO";

    $queryFecha = "INSERT INTO fecha (fecha, idexterno, idopp, idoc, idcertificado, identificador, status) VALUES($fecha_actual, $idexterno, $idopp, $idoc, $idcertificado, '$identificador', '$status')";
    $insertarFecha = mysql_query($queryFecha, $dspp) or die(mysql_error());

    $query = "UPDATE solicitud_certificacion SET status = '$status' WHERE idsolicitud_certificacion = $idsolicitud";
    $actualizar = mysql_query($query,$dspp) or die(mysql_error());

    if($status == 8){


        $asunto = "Contrato de uso del Simbolo de Pequeños Productores - SPP";


        $querySolicitud = "SELECT solicitud_certificacion.idopp,solicitud_certificacion.p1_email, solicitud_certificacion.p2_email,opp.idopp, opp.email FROM solicitud_certificacion LEFT JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE idsolicitud_certificacion = $idsolicitud";
        $rowSolicitud = mysql_query($querySolicitud,$dspp) or die(mysql_error());
        $datosSolicitud = mysql_fetch_assoc($rowSolicitud);

/*        $destinatarios .= $datosSolicitud['p1_email'];
        $destinatarios .= $datosSolicitud['p2_email'];
        $destinatarios .= $datosSolicitud['email'];
  */    
        $mail->AddAddress($datosSolicitud['p1_email']);
        $mail->AddAddress($datosSolicitud['p2_email']);
        $mail->AddAddress($datosSolicitud['email']);




      $anexoNombres = "";
      $query_anexos = "SELECT * FROM anexos WHERE idstatus_interno = 8";
      $row_anexos = mysql_query($query_anexos,$dspp) or die(mysql_error());

      while($datos_anexos = mysql_fetch_assoc($row_anexos)){
        $mail->AddAttachment($datos_anexos['archivo']);
        $anexoNombres .= "<li>".$datos_anexos['anexo']."</li>";
      }

      $queryProceso_certificacion = "SELECT * FROM proceso_certificacion WHERE idstatus = $status AND idoc = $idoc";
      $row_proceso_certificacion = mysql_query($queryProceso_certificacion,$dspp) or die(mysql_error());

     while($archivoProceso = mysql_fetch_assoc($row_proceso_certificacion)){
        $mail->AddAttachment($archivoProceso['archivo']);
        $anexoNombres .= "<li>".utf8_decode($archivoProceso['nombre'])."</li>";
      }

      $mensajeDefault = "";
      if(!empty($mensajeOPP)){
        $mensajeDefault = $mensajeOPP;
      }else{
        $mensajeDefault = "<p>Reciban ustedes un cordial y atento saludo, así como el deseo de éxito en todas y cada una de sus actividades</p>
                    <p>La presente tiene por objetivo hacerles llegar el documentro <strong>Contrato de Uso del Simbolo de Pequeños Productores y Acuse de Recibido</strong>; documentos que se requieren sean leidos y entendidos, una vez revisada la información de los documentos mencionados, por favor proceder a firmarlos y envíar los mismos por este medio.</p>
                    <p>El Contrato de Uso menciona como anexo el documento Manual del SPP y este Manual a su vez menciona como anexos los siguientes documentos.</p>";
      }

        $mensaje = '
          <html>
          <head>
            <meta charset="utf-8">
            
            
          </head>
          <body>
            <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
              <tbody>
                <tr>
                  <th rowspan="2" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                  <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">Contrato de uso del Simbolo de Pequeños Productores - SPP</span></p></th>

                </tr>
                <tr>
                 <th scope="col" align="left" width="280"><p>Para: <span style="color:red">'.$_POST['nombreOPP'].'</span></p></th>
                </tr>

                <tr>
                  <td colspan="2">
                    '.$mensajeDefault.'
                  </td>
                </tr>
                <tr>
                  <td><p><strong>Documentos Anexos</strong></p></td>
                </tr>
                <tr>
                  <td>
                    <ul>
                      '.$anexoNombres.'
                    </ul>
                  </td>
                </tr>
                <tr>
                  <td>
                    <p><strong>Memresía SPP</strong></p>
                    <p>Asi mismo se anexan los datos bancarios para el respectivo pago de la membresía SPP</p>
                    <p>El monto total de la membresía SPP es de: <span style="color:red;">'.$montoMembresia.'</span></p>
                  </td>

                </tr>
              </tbody>
            </table>
          </body>
          </html>
        ';


        //$mail->Username = "soporte@d-spp.org";
        //$mail->Password = "/aung5l6tZ";
        $mail->Subject = utf8_decode($asunto);
        $mail->Body = utf8_decode($mensaje);
        $mail->MsgHTML(utf8_decode($mensaje));

        

        if($mail->Send()){
          
          echo "<script>alert('Correo enviado Exitosamente.');location.href ='javascript:history.back()';</script>";
        }else{
              echo "<script>alert('Error, no se pudo enviar el correo');location.href ='javascript:history.back()';</script>";
     
        }
        $mail->ClearAddresses();

    }

}


if(isset($_POST['cargarCertificado']) && $_POST['cargarCertificado'] == "cargarCertificado"){

  $idopp = $_POST['idopp'];
  $idoc = $_POST['idoc'];
  $ruta = "../../archivos/ocArchivos/certificados/";

  if(!empty($_FILES['certificado_fld']['name'])){
    $_FILES['certificado_fld']['name'];
        move_uploaded_file($_FILES["certificado_fld"]["tmp_name"], $ruta.time()."_".$_FILES["certificado_fld"]["name"]);
        //dirección del archivo
        $adjunto = $ruta.basename(time()."_".$_FILES["certificado_fld"]["name"]);
  }else{
    $adjunto = NULL;
  }
  
  $vigenciaInicio = $_POST['vigenciaInicio'];
  $vigenciaFin = $_POST['vigenciaFin'];
  //$idoc = $_POST['certificadoIdoc'];
  //$idopp = $_POST['certificadoIdopp'];
  $statusPago = "POR REALIZAR";
  $fecha_actual = time();
  $fecha = date("d/m/Y", $_POST['fechaCarga']);
  $idcertificado = $_POST['idcertificado'];
  $nombreOPP = $_POST['nombreOPP'];

  $idexterno = $_POST['idsolicitud'];
  $identificador = "CERTIFICADO";


  $query = "UPDATE certificado SET vigenciainicio = '$vigenciaInicio', vigenciafin = '$vigenciaFin', adjunto = '$adjunto', statuspago = '$statusPago', fechaupload = $fecha_actual WHERE idcertificado = $idcertificado";
  $certificado = mysql_query($query, $dspp) or die(mysql_error());
  //echo "la consulta es: ".$query;

  $queryFecha = "INSERT INTO fecha (fecha, idexterno, idopp, idoc, idcertificado, identificador, status) VALUES($fecha_actual, $idexterno, $idopp, $idoc, $idcertificado, '$identificador', '$statusPago')";
  $insertarFecha = mysql_query($queryFecha, $dspp) or die(mysql_error());

            $destinatario = "cert@spp.coop";
            $asunto = "D-SPP - Certificado disponible(".$nombreOPP.")"; 

            $cuerpo = '
              <html>
              <head>
                <meta charset="utf-8">
              </head>
              <body>
              
                <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
                  <tbody>
                    <tr>
                      <th rowspan="2" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                      <th scope="col" align="left" width="280"><strong>Notificación de Estado / Status Notification ('.$fecha.')</strong></th>
                    </tr>

                    <tr>
                      <td><b>Se ha cargado el certificado correpondiente a '.$nombreOPP.', se notificara cuando se haya realizado el pago correpondiente a la membresia por parte del OPP.</b></td>
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



                $queryMensaje = "INSERT INTO mensajes(idopp, idoc,asunto, mensaje, destinatario, remitente, fecha) VALUES($idopp, $idoc, '$asunto', '$cuerpo', 'ADM', 'OC', $fecha_actual)";
                $ejecutar = mysql_query($queryMensaje,$dspp) or die(mysql_error());

                /************************  FIN MAIL FUNDEPPO  *********************************************/


                /************************  INICIA MAIL OPP  *******************************************/
            $emailOPP1 = $_POST['emailOPP1'];
            $emailOPP2 = $_POST['emailOPP2'];


            $asunto = "D-SPP - Certificado disponible(".$nombreOPP.")"; 

            $cuerpo = '
              <html>
              <head>
                <meta charset="utf-8">
              </head>
              <body>
              
                <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
                  <tbody>
                    <tr>
                      <th rowspan="3" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                      <th scope="col" align="left" width="280"><strong>Notificación de Estado / Status Notification ('.$fecha.')</strong></th>
                    </tr>
                    <tr>
                      <td>Se ha cargado su certificado dentro del sistema, por favor proceda a realizar el pago correspondiente a la membresia, después de realizar el pago subir el comprobante a nuestra plataforma dentro de su sesión de OPP <a href="http://d-spp.org/?OPP">www.d-spp.org/?OPP</a>, para poder realizarlo diríjase a la sección de "SOLICITUDES", en el apartado de "Certificación" podra realizar esté proceso.</td>
                    </tr>
         
                    <tr>
                      <td align="left" style="color:#ff738a;">En caso de alguna duda por favor envienos un correo a : cert@spp.coop</td>
                    </tr>
            

                  </tbody>
                </table>

              </body>
              </html>
            ';

        $mail->AddAddress($emailOPP1);
        $mail->AddAddress($emailOPP2);


        //$mail->Username = "soporte@d-spp.org";
        //$mail->Password = "/aung5l6tZ";
        $mail->Subject = utf8_decode($asunto);
        $mail->Body = utf8_decode($cuerpo);
        $mail->MsgHTML(utf8_decode($cuerpo));
        $mail->Send();
        $mail->ClearAddresses();




                $queryMensaje = "INSERT INTO mensajes(idopp, idoc,asunto, mensaje, destinatario, remitente, fecha) VALUES($idopp, $idoc, '$asunto', '$cuerpo', 'OPP', 'OC', $fecha_actual)";
                $ejecutar = mysql_query($queryMensaje,$dspp) or die(mysql_error());
}

if(isset($_POST['nuevaCotizacion']) && $_POST['nuevaCotizacion'] == "nuevaCotizacion"){
  $rutaArchivo = "../../archivos/ocArchivos/cotizaciones/";
  $archivo = $_POST['cotizacionVieja'];
  $idsolicitud_certificacion = $_POST['idsolicitud_certificacion'];

  //AL NOMBRE DEL ARCHIVO(FORMATO) LE CONCATENO EL TIME
  if(!empty($_FILES['nuevaCotizacion']['name'])){

      unlink($archivo);//BORRO EL ARCHIVO ANTERIOR ANTES DE ACTUALIZAR

      $_FILES["nuevaCotizacion"]["name"];
        move_uploaded_file($_FILES["nuevaCotizacion"]["tmp_name"], $rutaArchivo.time()."_".$_FILES["nuevaCotizacion"]["name"]);
        $archivo = $rutaArchivo.basename(time()."_".$_FILES["nuevaCotizacion"]["name"]);
  }else{
    $archivo = $_POST['cotizacionVieja'];
  }

  $updateSQL = sprintf("UPDATE solicitud_certificacion SET cotizacion_opp=%s WHERE idsolicitud_certificacion=%s",
                       GetSQLValueString($archivo, "text"),
                       GetSQLValueString($idsolicitud_certificacion, "int"));


  $Result1 = mysql_query($updateSQL, $dspp) or die(mysql_error());

}



$query_limit_opp = sprintf("%s LIMIT %d, %d", $query_opp, $startRow_opp, $maxRows_opp);
$opp = mysql_query($query_limit_opp, $dspp) or die(mysql_error());
//$row_opp = mysql_fetch_assoc($opp);

if (isset($_GET['totalRows_opp'])) {
  $totalRows_opp = $_GET['totalRows_opp'];
} else {
  $all_opp = mysql_query($query_opp);
  $totalRows_opp = mysql_num_rows($all_opp);
}
$totalPages_opp = ceil($totalRows_opp/$maxRows_opp)-1;

$queryString_opp = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_opp") == false && 
        stristr($param, "totalRows_opp") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_opp = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_opp = sprintf("&totalRows_opp=%d%s", $totalRows_opp, $queryString_opp);


///////////////////////////////////// VARIABLES DE CONTROL ////////////////////////////////////////
  $validacionStatus = $row_opp['status'] != 1 && $row_opp['status'] != 2 && $row_opp['status'] != 3 && $row_opp['status'] != 14 && $row_opp['status'] != 15;
///////////////////////////////////// VARIABLES DE CONTROL ////////////////////////////////////////

?>
<?php 
  $query = "SELECT * FROM status ORDER BY status.nombre";
  $ejecutarStatus = mysql_query($query,$dspp) or die(mysql_error());

 ?>

<div class="panel panel-default">
  <div class="panel-heading">Solicitudes</div>
  <div class="panel-body">

    <table class="table table-condensed table-striped" style="font-size:12px">
      <thead>
        <tr>
          <th class="text-center">ID</th>
          <th class="text-center">Fecha Solicitud</th>
          <th class="text-center">Organización</th>
          <th class="text-center">Cotización (Descargable)</th>
          <!--<th class="text-center">Sitio WEB</th>-->
          <!--<th class="text-center">Contacto</th>-->
          <!--<th class="text-center">País</th>-->
          <th class="text-center">Resolución de Objeción</th>
          <th class="text-center">Estatus Publico</th>
          <th class="text-center">Estatus Interno</th>
          <th class="text-center" colspan="2">Certificado</th>

          <!--<th class="text-center">Propuesta</th>-->
          <th class="text-center">Observaciones Solicitud</th>
          <th class="text-center">Acciones</th>
          <!--<th>OC</th>
          <th>Razón social</th>
          <th>Dirección fiscal</th>
          <th>RFC</th>-->
          <!--<th>Eliminar</th>-->
        </tr>
      </thead>

      <?php $cont=0; while ($row_opp = mysql_fetch_assoc($opp)) {$cont++; ?>
        <tr <?php if($row_opp['estado'] == 20){ echo "style='border-style:solid;border-color:#E74C3C'";}if($row_opp['status'] == 24){ echo "class='warning'";} ?>>
          <?php  $fecha = $row_opp['fecha_elaboracion']; ?>
            <td><?php echo $row_opp['idsolicitud_certificacion']; ?></td>

 <!----------------------------------------- INICIA BOTON VER SOLICITUD ---------------------------------------------->
            <td>
              <a class="btn btn-primary btn-sm" style="width:100%" href="?SOLICITUD&amp;detailBlock&amp;idsolicitud=<?php echo $row_opp['idsolicitud_certificacion']; ?>" aria-label="Left Align">

                <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
                <?php echo  date("d/m/Y", $fecha); ?><br>Ver solicitud
              </a>
            </td>
  <!----------------------------------------- TERMINA BOTON VER SOLICITUD ---------------------------------------------->


  <!----------------------------------------- INICIA SECCIÓN ORGANIZACIÓN ---------------------------------------------->
              <td>
                <?php 
                  if(isset($row_opp['nombre'])){
                    echo "<a href='?OPP&detail&idopp=$row_opp[idopp]'>$row_opp[abreviacion]</a>";
                  }else{
                    echo "No Disponible";
                  } 
                ?>
              </td>
  <!----------------------------------------- INICIA SECCIÓN ORGANIZACIÓN ---------------------------------------------->


  <!----------------------------------------- INICIA SECCION COTIZACION OPP ---------------------------------------------->
            <td class="text-center" style="width:250px;">
              <?php 
                $consultarFecha = "SELECT * FROM fecha WHERE idopp = $row_opp[idopp] AND identificador = 'SOLICITUD' AND status = 18";
                $row_fecha = mysql_query($consultarFecha,$dspp) or die(mysql_error());
                $totalFecha = mysql_num_rows($row_fecha);
               ?>
              <?php 
              if($totalFecha == 0){
                if(!empty($row_opp['cotizacion_opp'])){
              ?>
                  <?php 
                  if($row_opp['status'] == 24){
                  ?>
                  <div class="col-xs-12">
                    <form  action="" method="post" id="nuevaCotizacion" enctype="multipart/form-data">
                      <button type="button" class="btn btn-sm btn-info" data-toggle="modal" <?php echo "data-target='#nuevaCotizacion".$row_opp['idsolicitud_certificacion']."'"?>>
                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Nueva Cotización
                      </button>
                  <?php
                  }else{
                  ?>
                  <div class="col-xs-2">
                    <form  action="" method="post" id="nuevaCotizacion" enctype="multipart/form-data">
                      <button type="button" class="btn btn-sm btn-default" data-toggle="modal" <?php echo "data-target='#nuevaCotizacion".$row_opp['idsolicitud_certificacion']."'"?>>
                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                      </button>
                  <?php
                  }
                }
                 ?>

                    <div class="modal fade" <?php echo "id='nuevaCotizacion".$row_opp['idsolicitud_certificacion']."'" ?> tabindex="-1" role="dialog">
                      <div class="modal-dialog modal-sm">
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Cargar Nueva Cotización (<?php echo $row_opp['idsolicitud_certificacion'] ?>)</h4>
                          </div>
                          <div class="modal-body">
                            <input type="file" name="nuevaCotizacion">
                            <input type="hidden" name="cotizacionVieja" value="<?php echo $row_opp['cotizacion_opp']; ?>">
                            <input type="hidden" name="idsolicitud_certificacion" value="<?php echo $row_opp['idsolicitud_certificacion']; ?>">
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                          </div>
                        </div><!-- /.modal-content -->
                      </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->


                    <input type="hidden" name="nuevaCotizacion" value="nuevaCotizacion">
    
                  </form>

                </div>
              <?php
              }
               ?>

   

              <a href="<?echo $row_opp['cotizacion_opp']?>" target="_blank" type="button" class="btn <?php if(empty($row_opp['cotizacion_opp'])){ echo 'btn-default btn-sm';}else{echo 'btn-success btn-sm';} ?>" aria-label="Left Align" <?php if(empty($row_opp['cotizacion_opp'])){echo "disabled";}?>>
                <span class="glyphicon glyphicon-download-alt"></span> OPP
              </a> 


  <!----------------------------------------- TERMINA SECCION COTIZACION OPP ---------------------------------------------->


  <!----------------------------------------- INICIA SECCIÓN COTIZACION FUNDEPPO ---------------------------------------------->
            <!--<td>
              <a href="http://d-spp.org/oc/<?echo $row_opp['cotizacion_adm']?>" target="_blank" type="button" class="btn <?php if(empty($row_opp['cotizacion_adm'])){ echo 'btn-danger btn-sm';}else{echo 'btn-success btn-sm';} ?>" aria-label="Left Align" <?php if(empty($row_opp['cotizacion_adm'])){echo "disabled";}?>>
                <span class="glyphicon glyphicon-download-alt"></span> FUNDEPPO
              </a>       
            </td>-->
  <!----------------------------------------- TERMINA SECCIÓN COTIZACION FUNDEPPO ---------------------------------------------->

  <!----------------------------------------- INICIA SECCIÓN PROPUESTA ---------------------------------------------->

              <?php 
                if($row_opp['status'] == 24){
              ?>
                <p class="alert alert-danger" style="display:inline;padding:7px;">Rechazada</p>
                    <!--<button class="btn btn-success btn-sm" disabled>
                      <p class="glyphicon glyphicon-check" aria-hidden="true"></p> Aceptada
                    </button>-->          
                <?php 
                  }else if($row_opp['status'] != 1 && $row_opp['status'] != 2 && $row_opp['status'] != 3 && $row_opp['status'] != 14 && $row_opp['status'] != 15 && $row_opp['status'] != 17 && $row_opp['status'] != 20){
                ?>
                <p class="alert alert-success" style="display:inline;padding:7px;">Aceptada</p>
                <?php
                  }else{
                ?>
                  <p style="display:inline;">Pendiente</p>
                    <!--<button class="btn btn-default btn-sm" disabled>
                      <p class="glyphicon glyphicon-check" aria-hidden="true"></p> Aceptada
                    </button> -->              
              <?php 
                }
              ?>      
            </td>
  <!----------------------------------------- TERMINA SECCIÓN PROPUESTA ---------------------------------------------->

                  <?php 
                    $query = "SELECT * FROM certificado WHERE idsolicitud = $row_opp[idsolicitud_certificacion]";
                    $ejecutar = mysql_query($query, $dspp) or die(mysql_error());
                    $registroCertificado = mysql_fetch_assoc($ejecutar);

                    $queryObjecion = "SELECT * FROM objecion WHERE idsolicitud = $row_opp[idsolicitud_certificacion]";
                    $ejecutar2 = mysql_query($queryObjecion,$dspp);
                    $registroObjecion = mysql_fetch_assoc($ejecutar2); 

                    $query_ane = "SELECT * FROM anexos WHERE idstatus_interno = 8";
                    $anexos = mysql_query($query_ane,$dspp) or die(mysql_error());

                  $consultaFecha = "SELECT idfecha FROM fecha WHERE idopp = '$row_opp[idopp]' AND identificador = 'OPP' AND status = 20";
                  $ejecutar = mysql_query($consultaFecha,$dspp) or die(mysql_error());
                  $total = mysql_num_rows($ejecutar);


                   ?>
  <!----------------------------------------- INICIA SECCIÓN DICTAMEN OBJECIÓN ---------------------------------------------->
            <td class="text-center" style="width:130px;">
              <div style="margin-top:6px;">
                <?php
                if(!empty($total)){
                  echo "<h6 class='alert alert-success' style='margin:7px;'>Proceso de Renovación</h6>";
                }else if(isset($registroObjecion['dictamen'])){
                  echo "<p class='alert alert-danger' style='padding:7px;font-size:10px;display:inline;'>$registroObjecion[dictamen]</p>";
                  echo "<a class='btn btn-sm btn-success' style='display:inline;' href='$registroObjecion[adjunto]' target='_blank'><span class='glyphicon glyphicon-download-alt' aria-hidden='true'></span></a>";
                }
                 ?>
              </div>
            </td>
  <!----------------------------------------- TERMINA SECCIÓN DICTAMEN OBJECIÓN ---------------------------------------------->


  <!----------------------------------------- INICIA SECCIÓN STATUS PUBLICO ---------------------------------------------->
            <td>
            <?php 
              $query_status = "SELECT * FROM status_publico WHERE idstatus_publico = $row_opp[status_publico]";
              $ejecutar = mysql_query($query_status,$dspp) or die(mysql_error());
              $estatus_publico = mysql_fetch_assoc($ejecutar);
             ?>
                <?php 
                  if($row_opp['status'] == 10){
                    //echo '<a href="#" data-toggle="tooltip" title="'.$estatus_interno['descripcion_interna'].'">'.$estatus_interno['nombre'].'</a>';
                    echo "<p class='text-center alert alert-success' style='padding:5px;'><b><u>Certificado</u></b></p>";
                  }else{
                    echo "<p class='text-center alert alert-warning' style='padding:5px;'>";
                    echo '<a href="#" data-toggle="tooltip" title="'.$estatus_publico['descripcion_publica'].'">'.$estatus_publico['nombre'].'</a>';
                    echo "</p>";
                  }
                ?>
            </td>
  <!----------------------------------------- TERMINA SECCIÓN STATUS PUBLICO ---------------------------------------------->

  <!------------------------------------ INICIA SECCION STATUS INTERNO ------------------------------------>
              <td>
                <?php 
                  $query_status = "SELECT * FROM status WHERE idstatus = $row_opp[status]";
                  $ejecutar = mysql_query($query_status,$dspp) or die(mysql_error());
                  $estatus_interno = mysql_fetch_assoc($ejecutar);

                  if($row_opp['status'] == 4 || $row_opp['status'] == 11 || $row_opp['status'] == 13 || $row_opp['status'] == 14 || $row_opp['status'] == 15){
                    $colorEstado = "class='text-center alert alert-danger'";
                  }else if($row_opp['status'] == 10){
                    $colorEstado = "class='text-center alert alert-success'";
                  }else{
                    $colorEstado = "class='text-center alert alert-warning'";
                  }
                 ?>

                <p <?echo $colorEstado;?> style="padding:5px;">
                  <?php echo '<a href="#" data-toggle="tooltip" title="'.$estatus_interno['descripcion_interna'].'">'.$estatus_interno['nombre'].'</a>'; ?>
                </p>

              </td>
  <!------------------------------------ TERMINA SECCION STATUS INTERNO ------------------------------------>



  <!----------------------------------------- INICIA SECCION STATUS CERTIFICADO ---------------------------------------------->

            <td>     
              <?php if($row_opp['status'] != 1 && $row_opp['status'] != 2 && $row_opp['status'] != 3 && $row_opp['status'] != 14 && $row_opp['status'] != 15 && $row_opp['status'] != 17 && $row_opp['status'] != 20){ ?>
                <?php if(isset($registroObjecion['dictamen']) && $registroObjecion['dictamen'] == 'POSITIVO'){ ?>
                  <button class="btn btn-success btn-sm" data-toggle="modal" <?php echo "data-target='#status".$row_opp['idsolicitud_certificacion']."'"?>  >
                  <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Estatus<br>Certificación
                  </button>       
                <?php }else{ ?>
                  <button class="btn btn-warning btn-sm" data-toggle="modal" <?php echo "data-target='#status".$row_opp['idsolicitud_certificacion']."'"?>  >
                  <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Estatus<br>Certificación
                  </button>            
                <?php } ?>

              <?php }else{ ?>
                <button class="btn btn-default btn-sm" data-toggle="modal" <?php echo "data-target='#status".$row_opp['idsolicitud_certificacion']."'"?>  disabled>
                <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Estatus<br>Certificación
                </button>                     
              <?php } ?>
            </td>
  <!----------------------------------------- TERMINA SECCION STATUS CERTIFICADO ---------------------------------------------->

            <!----------------------------------------- INICIA MODAL STATUS CERTIFICADO -------------------------------------------------------------------------------->
                  <form action="" method="post" id="statusCertificado" enctype="multipart/form-data">
                    <div class="modal fade" <?php echo "id='status".$row_opp['idsolicitud_certificacion']."'" ?> tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                      <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">Estatus Certificación</h4>
                          </div>
                          <div class="modal-body">
                            <div class="row">
                                <p class="alert alert-info">* Una vez finalizada la certificación, debe de seleccionar "Dictamen positivo" esto para poder cargar el certificado.</p>
                                <?php if($row_opp['status_publico'] != '8' &&  empty($registroObjecion['adjunto']) ){ ?>
                                  <div class="col-xs-12 alert alert-danger">
                                    <p>Una vez finalizado el periodo de objeción, podra iniciar el periodo de certificación.</p>
                                  </div>

                                <?php }else{ ?>
                                  <div class="col-xs-12">
                                    <div class="col-xs-12">
                                      <?php if(!empty($registroCertificado['status'])){
                                            $queryProceso = "SELECT proceso_certificacion.*, status.idstatus, status.nombre AS 'nombreEstatus' FROM proceso_certificacion LEFT JOIN status ON proceso_certificacion.idstatus = status.idstatus WHERE idsolicitud_certificacion = $row_opp[idsolicitud_certificacion]";
                                            $row_proceso = mysql_query($queryProceso,$dspp) or die(mysql_error());
        
                                          ?>
                                          <div class="col-xs-12">
                                            <label class="control-label" for="statusActual">Historial Estatus</label>
                                            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                                              <div class="panel panel-default">
                                            <?php
                                            while($proceso_certificacion = mysql_fetch_assoc($row_proceso)){
                                            ?>
                                              <div class="panel-heading" role="tab" id="proceso<?php echo $proceso_certificacion['idproceso_certificacion'];?>">
                                                <h4 class="panel-title">
                                                  <a role="button" data-toggle="collapse" data-parent="#accordion" href="#idproceso<?php echo $proceso_certificacion['idproceso_certificacion']?>" aria-expanded="true" aria-controls="idproceso<?php echo $proceso_certificacion['idproceso_certificacion']?>">
                                                    <?php echo $proceso_certificacion['nombreEstatus']; ?>
                                                  </a>
                                                </h4>
                                              </div>
                                              <div id="idproceso<?php echo $proceso_certificacion['idproceso_certificacion']?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="proceso<?php echo $proceso_certificacion['idproceso_certificacion'];?>">
                                                <div class="panel-body">
                                                  <p>FECHA: <?php echo date("d / m / Y",$proceso_certificacion['fecha']); ?></p>
                                                  <p>ESTATUS: <?php echo $proceso_certificacion['nombreEstatus']; ?></p>
                                                  <p>REGISTRO: <?php if(!empty($proceso_certificacion['registro'])){ echo $proceso_certificacion['registro']; }else{ echo "No Disponible";} ?></p>
                                                  <p>ARCHIVO: <?php if(!empty($proceso_certificacion['archivo'])){ echo "<a href='$proceso_certificacion[archivo]' target='_blank'>Visualizar</a>"; }else{ echo "No Disponible";} ?></p>
                                                </div>
                                              </div>
                                            
                                            <?php
                                            }
                                             ?>
                                              </div>
                                            </div>
                                          </div>

                                          <div class="col-xs-12">
                                            <?php 
                                            if($row_opp['estado'] != 10){
                                              if($registroCertificado['status'] != 8){
                                            ?>
                                              <select name="status" class="form-control" id="statusSelect" onchange="funcionSelect()" required>
                                                <option class="form-control" value="">Seleccione un estado</option>


                                                <?php/* $contador =0; while($row_status = mysql_fetch_assoc($ejecutarStatus)){ ?>
                                                  <?php 
                                                    if($row_status['idstatus'] != 1 && $row_status['idstatus'] != 2 && $row_status['idstatus'] != 3 && $row_status['idstatus'] != 10 && $row_status['idstatus'] != 17 && $row_status['idstatus'] != 18 && $row_status['idstatus'] != 19 ){
                                                      $contador++;
                                                  ?>
                                                    <option class="form-control" value="<?echo $row_status['idstatus'];?>"><?echo $contador.".- ".$row_status['nombre'];?></option>                                        
                                                  <?php
                                                    }
                                                  ?>

                                                <?php } */
                                                ?>
                                                <?php require_once("../option_estados.php"); ?>

                                              </select>
                                            <?php
                                              }
                                            }
                                             ?>
                                          </div>

                                            <div class="col-xs-12" id="divSelect" style="margin-top:10px;">
                                              <div class="col-xs-6">
                                                <input style="display:none" id="nombreArchivo" type='text' class='form-control' name='nombreArchivoEstatus' placeholder="Nombre del Archivo"/>
                                              </div>
                                              <div class="col-xs-6">
                                                <input style="display:none" id="archivoDictamen" type='file' class='form-control' name='archivoDictamen' />
                                              </div>
                                              <textarea class="form-control" id="registroOculto" style="display:none" name="registro1" cols="30" rows="10" value="" placeholder="Escribe aquí"></textarea>
                                            </div>

                                <div id="tablaCorreo" style="display:none">
                                  <div class="col-xs-12"  style="margin-top:10px;">
                                    <p class="alert alert-info">El siguiente formato sera enviado en breve al OPP</p>
                                    <div class="col-xs-6">
                                      
                                      <div class="col-xs-12">
                                        <div class="col-xs-6"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></div>
                                        <div class="col-xs-6">
                                          <p>Enviado el: <?php echo date("d/m/Y", time()); ?></p>
                                          <p>Para: <span style="color:red"><?php echo $row_opp['nombre']; ?></span></p>
                                          <p>Correo(s): <?php echo $row_opp['p1_email']." ".$row_opp['p2_email']." ".$row_opp['email']; ?></p>
                                          <p>Asunto: <span style="color:red">Contrato de uso del Simbolo de Pequeños Productores - SPP</span></p>
                                          
                                        </div>
                                      </div>
                                      <div class="col-xs-12">
                                        <h5 class="alert alert-warning">MENSAJE OPP( <small>Cuerpo del mensaje en caso de ser requerido</small>)</h5>
                                        <textarea name="mensajeOPP" class="form-control" id="textareaMensaje" cols="30" rows="10" placeholder="Ingrese un mensaje en caso de que lo deseé"></textarea>

                                      </div>
                                    </div>
                                    <div class="col-xs-6">
                                      <div class="col-xs-12">
                                        <h4>ARCHIVOS ADJUNTOS( <small>Archivos adjuntos dentro del email</small> )</h4>
                                        <?php 
                                        while($row_anexos = mysql_fetch_assoc($anexos)){

                                          echo "<span class='glyphicon glyphicon-ok' aria-hidden='true'></span> <a href='$row_anexos[archivo]' target='_blank'>$row_anexos[anexo]</a><br>";
                                        }
                                         ?>
                                        
                                      </div>
                                      <div class="col-xs-12">
                                        <h4>ARCHIVO EXTRA( <small>Anexar algun otro archivo en caso de ser requerido</small>)</h4>
                                        <div class="col-xs-12">
                                          <input type="text" class="form-control" name="nombreArchivo" placeholder="Nombre Archivo">
                                        </div>
                                        <div class="col-xs-12">
                                          <input type="file" class="form-control" name="archivoExtra">
                                        </div>
                                      </div>
                                      <div class="col-xs-12">
                                        <h5 class="alert alert-warning">MEMBRESÍA SPP( Indicar el monto total de la membresía )</h5>
                                        <p>Total Membresía: <input type="text" class="form-control" name="montoMembresia" placeholder="Total Membresía"></p>
                                      </div>
                                    </div>

                                  </div>
                                </div>



                                      <?php }
                                      else{ 
                                        if($registroCertificado['status'] != 8){
                                        ?>
                                        <label class="control-label" for="status">Estatus Certificación</label>
                                        <select name="status" class="form-control" id="statusSelect" onchange="funcionSelect()" required>
                                          <option class="form-control" value="">Seleccione un estado</option>
                                          <?php $contador=0; while($row_status = mysql_fetch_assoc($ejecutarStatus)){ ?>
                                            
                                            <?php /*
                                              if($row_status['idstatus'] != 1 && $row_status['idstatus'] != 2 && $row_status['idstatus'] != 3 && $row_status['idstatus'] != 10 && $row_status['idstatus'] != 17 && $row_status['idstatus'] != 18 && $row_status['idstatus'] != 19){
                                                $contador++;
                                            ?>
                                              <option class="form-control" value="<?echo $row_status['idstatus'];?>"><?echo $contador.".- ".$row_status['nombre'];?></option>                                        
                                            <?php
                                              }
                                            */?>
                                              <?php require_once("../option_estados.php"); ?>
                                      <?php 
                                        } 
                                      } 
                                    ?>
                                        </select>
                                        
                                        <!--<input class="form-control" type="text" name="status" placeholder="Ingresar status">-->
                                <div class="col-xs-12" id="divSelect" style="margin-top:10px;">
                                  <div class="col-xs-6">
                                    <input style="display:none" id="nombreArchivo" type='text' class='form-control' name='nombreArchivoEstatus' placeholder="Nombre del Archivo"/>
                                  </div>
                                  <div class="col-xs-6">
                                    <input style="display:none" id="archivoDictamen" type='file' class='form-control' name='archivoDictamen' />
                                  </div>

                                  <textarea class="form-control" id="registroOculto" style="display:none" name="registro1" cols="30" rows="10" value="" placeholder="Escribe aquí"></textarea>
                                </div>

                                <div id="tablaCorreo" style="display:none">
                                  <div class="col-xs-12"  style="margin-top:10px;">
                                    <p class="alert alert-info">El siguiente formato sera enviado en breve al OPP</p>
                                    <div class="col-xs-6">
                                      
                                      <div class="col-xs-12">
                                        <div class="col-xs-6"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></div>
                                        <div class="col-xs-6">
                                          <p>Enviado el: <?php echo date("d/m/Y", time()); ?></p>
                                          <p>Para: <span style="color:red"><?php echo $row_opp['nombre']; ?></span></p>
                                          <p>Correo(s): <?php echo $row_opp['p1_email']." ".$row_opp['p2_email']." ".$row_opp['email']; ?></p>
                                          <p>Asunto: <span style="color:red">Contrato de uso del Simbolo de Pequeños Productores - SPP</span></p>
                                          
                                        </div>
                                      </div>
                                      <div class="col-xs-12">
                                        <h5 class="alert alert-warning">MENSAJE OPP( <small>Cuerpo del mensaje en caso de ser requerido</small>)</h5>
                                        <textarea name="mensajeOPP" class="form-control" id="textareaMensaje" cols="30" rows="10" placeholder="Ingrese un mensaje en caso de que lo deseé"></textarea>

                                      </div>
                                    </div>
                                    <div class="col-xs-6">
                                      <div class="col-xs-12">
                                        <h4>ARCHIVOS ADJUNTOS( <small>Archivos adjuntos dentro del email</small> )</h4>
                                        <?php 
                                        while($row_anexos = mysql_fetch_assoc($anexos)){

                                          echo "<span class='glyphicon glyphicon-ok' aria-hidden='true'></span> <a href='$row_anexos[archivo]' target='_blank'>$row_anexos[anexo]</a><br>";
                                        }
                                         ?>
                                        
                                      </div>
                                      <div class="col-xs-12">
                                        <h4>ARCHIVO EXTRA( <small>Anexar algun otro archivo en caso de ser requerido</small>)</h4>
                                        <div class="col-xs-12">
                                          <input type="text" class="form-control" name="nombreArchivo" placeholder="Nombre Archivo">
                                        </div>
                                        <div class="col-xs-12">
                                          <input type="file" class="form-control" name="archivoExtra">
                                        </div>

       
                                      </div>
                                      <div class="col-xs-12">
                                        <h5 class="alert alert-warning">MEMBRESÍA SPP( Indicar el monto total de la membresía )</h5>
                                        <p>Total Membresía: <input type="text" class="form-control" name="montoMembresia" placeholder="Total Membresía"></p>
                                      </div>
                                    </div>

                                  </div>
                                </div>

                                      <?php } ?>
                                    </div>
                                  </div>
                                      
                                <?php } ?>
                            </div>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                            <?php 
                            if($registroCertificado['status'] != 8){
                            ?>
                              <?php if(!empty($registroCertificado['status'])){ ?>

                                <input type="hidden" name="actualizarStatus" value="actualizarStatus">
                                <input type="hidden" name="idcertificado" value="<?echo $registroCertificado['idcertificado'];?>">
                                <input type="hidden" name="emailOPP1" value="<?php echo $row_opp['p1_email'];?>">
                                <input type="hidden" name="emailOPP2" value="<?php echo $row_opp['p2_email'];?>">
                                <input type="hidden" name="idopp" value="<?php echo $row_opp['idopp'];?>">
                                <input type="hidden" name="idoc" value="<?php echo $row_opp['idoc'];?>">
                                <input type="hidden" name="nombreOPP" value="<?php echo $row_opp['nombre'];?>">

                                <button type="submit" class="btn btn-primary">Actualizar</button>

                              <?php }else{ ?>
                                <?php if(!empty($registroObjecion['adjunto']) || $row_opp['status_publico'] == 8){ ?>
                                  <button type="submit" class="btn btn-primary">Guardar</button>
                                
                                <input type="hidden" name="emailOPP1" value="<?php echo $row_opp['p1_email'];?>">
                                <input type="hidden" name="emailOPP2" value="<?php echo $row_opp['p2_email'];?>">
                                <input type="hidden" name="idopp" value="<?php echo $row_opp['idopp'];?>">
                                <input type="hidden" name="idoc" value="<?php echo $row_opp['idoc'];?>">
                                <input type="hidden" name="nombreOPP" value="<?php echo $row_opp['nombre'];?>">

                                <?php } ?>
                                <input type="hidden" name="statusCertificado" value="statusCertificado">
                              <?php } ?>
                                
                                <input type="hidden" name="idopp" value="<?php echo $row_opp['idopp'];?>">
                                <input type="hidden" name="idoc" value="<?php echo $row_opp['idoc'];?>">
                                <input type="hidden" name="statusFecha" value="<?echo time();?>">
                                <input type="hidden" name="idsolicitud" value="<?echo $row_opp['idsolicitud_certificacion'];?>">
                            <?php
                            }
                             ?>
                          </div>
                        </div>
                      </div>
                    </div>
                  </form>
            <!----------------------------------------- TERMINA MODAL STATUS CERTIFICADO -------------------------------------------------------------------------------->

                    <script>
                    function funcionSelect() {
                      var valorSelect = document.getElementById("statusSelect").value;

                      if(valorSelect == '4'){
                        document.getElementById('divSelect').style.display = 'block';
                        document.getElementById("registroOculto").style.display = 'block';
                        document.getElementById("nombreArchivo").style.display = 'block';
                        document.getElementById("archivoDictamen").style.display = 'block';
                        document.getElementById("tablaCorreo").style.display = 'none';
                      }else if(valorSelect == '6'){
                        document.getElementById('divSelect').style.display = 'block';
                        document.getElementById("nombreArchivo").style.display = 'block';
                        document.getElementById("archivoDictamen").style.display = 'block';
                        document.getElementById("registroOculto").style.display = 'block';
                        document.getElementById("tablaCorreo").style.display = 'none';
                      }else if(valorSelect == '7'){
                        document.getElementById('divSelect').style.display = 'block';
                        document.getElementById("nombreArchivo").style.display = 'none';
                        document.getElementById("archivoDictamen").style.display = 'none';
                        document.getElementById("registroOculto").style.display = 'block';
                        document.getElementById("tablaCorreo").style.display = 'none';
                      }else if(valorSelect == '8'){
                        document.getElementById("tablaCorreo").style.display = 'block'; 
                        document.getElementById("nombreArchivo").style.display = 'none';
                        document.getElementById("archivoDictamen").style.display = 'none';
                        document.getElementById("registroOculto").style.display = 'none';
                      }else if(valorSelect == '9'){
                        document.getElementById('divSelect').style.display = 'block';
                        document.getElementById("tablaCorreo").style.display = 'none'; 
                        document.getElementById("nombreArchivo").style.display = 'block';
                        document.getElementById("archivoDictamen").style.display = 'block';
                        document.getElementById("registroOculto").style.display = 'block';                 
                      }else if(valorSelect == '23'){
                        document.getElementById('divSelect').style.display = 'block';
                        document.getElementById("tablaCorreo").style.display = 'none';
                        document.getElementById("nombreArchivo").style.display = 'none'; 
                        document.getElementById("archivoDictamen").style.display = 'none';
                        document.getElementById("registroOculto").style.display = 'block'                  
                      }else{
                        document.getElementById("nombreArchivo").style.display = 'none';
                        document.getElementById("archivoDictamen").style.display = 'none';
                        document.getElementById("registroOculto").style.display = 'none'
                        document.getElementById("tablaCorreo").style.display = 'none';
                        document.getElementById('divSelect').style.display = 'none';
                      }
                    }
                    </script>



  <!----------------------------------------- INICIA SECCION CERTIFICADO CERTIFICADO ---------------------------------------------->
            <td>
              <?php if(isset($registroCertificado['status']) && ($registroCertificado['status'] == 8 || $registroCertificado['status'] == 10)){ ?>
                <button class="btn btn-success btn-sm" data-toggle="modal" <?php echo "data-target='#certificado".$row_opp['idsolicitud_certificacion']."'"?>  >
                <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Detalle<br>Certificado
                </button>            
              <?php }else{ ?>
                <button class="btn btn-default btn-sm" data-toggle="modal" <?php echo "data-target='#certificado".$row_opp['idsolicitud_certificacion']."'"?>  disabled>
                <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Detalle<br>Certificado
                </button>            
              <?php } ?>
            </td>
  <!----------------------------------------- TERMINA SECCION CERTIFICADO CERTIFICADO ---------------------------------------------->

              <!----------------------------------------- MODAL CERTIFICADO CERTIFICADO ---------------------------------->
                  <form action="" method="post" id="cargarCertificado" enctype="multipart/form-data">
                    <div class="modal fade" <?php echo "id='certificado".$row_opp['idsolicitud_certificacion']."'" ?> tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">Certificado</h4>
                          </div>
                          <div class="modal-body">
                            <div class="row">
                              <div class="col-xs-12">

                                <div class="col-xs-6">
                                  <label class="control-label" for="vigenciaInicio">Vigencia Inicio</label>
                                  <?php if(isset($registroCertificado['vigenciainicio'])){ ?>
                                    <input class="form-control" name="vigenciaInicio" id="vigenciaInicio" type="date" placeholder="dd/mm/aaaa" value="<?echo $registroCertificado['vigenciainicio'];?>" disabled>
                                  <?php }else{ ?>
                                    <input class="form-control" name="vigenciaInicio" id="vigenciaInicio" type="date" placeholder="dd/mm/aaaa" required>
                                  <?php } ?>
                                  <hr>
                                  <label class="control-label" for="vigenciaFin">Vigencia Fin</label>
                                  <?php if(isset($registroCertificado['vigenciafin'])){ ?>
                                    <input class="form-control" name="vigenciaFin" id="vigenciaFin" type="date" placeholder="dd/mm/aaaa" value="<?echo $registroCertificado['vigenciafin'];?>" disabled>
                                  <?php }else{ ?>
                                    <input class="form-control" name="vigenciaFin" id="vigenciaFin" type="date" placeholder="dd/mm/aaaa" required>
                                  <?php } ?>                                                          
                                </div>

                                <div class="col-xs-6">
                                  <?php if(!empty($registroCertificado['adjunto'])){ ?>
                                    <label class="control-label" for="certificado_fld">Certificado</label>
                                    <br>
                                    <a class="btn btn-info" target="_blank" href="<?echo $registroCertificado['adjunto'];?>"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Descargar Certificado</a>
                                  <?php }else{ ?>
                                    <label class="control-label" for="certificado_fld">Cargar Certificado</label>
                                    <input name="certificado_fld" id="certificado_fld" type="file" class="filestyle" data-buttonName="btn-success" data-buttonBefore="true" data-buttonText="Cargar Certificado"> 


                                  <?php } ?>
                                </div>

                              </div>
                            </div>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                            <?php if(empty($registroCertificado['adjunto'])){ ?>
                              <button type="submit" class="btn btn-primary">Guardar</button>
                            <?php }?>
                            <input type="hidden" name="idopp" value="<?php echo $row_opp['idopp'];?>">
                            <input type="hidden" name="idoc" value="<?php echo $row_opp['idoc'];?>">
                            <input type="hidden" name="emailOPP1" value="<?php echo $row_opp['p1_email'];?>">
                            <input type="hidden" name="emailOPP2" value="<?php echo $row_opp['p2_email'];?>">
                            <input type="hidden" name="nombreOPP" value="<?php echo $row_opp['nombre'];?>">
                            <input type="hidden" name="fechaCarga" value="<?echo time();?>">
                            <input type="hidden" name="idcertificado" value="<?echo $registroCertificado['idcertificado'];?>">
                            <input type="hidden" name="cargarCertificado" value="cargarCertificado">
                            <input type="hidden" name="idsolicitud" value="<?echo $row_opp['idsolicitud_certificacion'];?>">

                          </div>
                        </div>
                      </div>
                    </div>
                  </form>
              <!----------------------------------------- MODAL CERTIFICADO CERTIFICADO ---------------------------------->


  <!----------------------------------------- INICIA SECCION OBSERVACIONES ---------------------------------------------->
            <td class="text-center">
              <?php if(empty($row_opp['observaciones'])){ ?>
                <button class="btn btn-default btn-sm" disabled>
                  <span class="glyphicon glyphicon-list-alt"></span> Consultar
                </button>         
              <?php }else{ ?>
                <a class="btn btn-info btn-sm" href="?SOLICITUD&amp;detailBlock&amp;idsolicitud=<?php echo $row_opp['idsolicitud_certificacion']; ?>">
                  <span class="glyphicon glyphicon-list-alt"></span> Consultar
                </a>
              <?php } ?>
            </td>
  <!----------------------------------------- TERMINA SECCION OBSERVACIONES ---------------------------------------------->

           

            <!--06/04/2016<form action="" method="post">
            <!--<input class="btn btn-danger" type="submit" value="Eliminar" />-->
            <!--06/04/2016<input type="hidden" value="OPP eliminado correctamente" name="mensaje" />
            <input type="hidden" value="1" name="opp_delete" />
            <input type="hidden" value="<?php echo $row_opp['idopp']; ?>" name="idopp" />
            </form>
            <!--</td>-->

            <td>
              <form action="" method="post" id="cancelarSolicitud">
                <button class="btn btn-sm btn-danger" type="submit"  data-toggle="tooltip" data-placement="top" title="Cancelar Solicitud">
                  <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                </button>
                <input type="hidden" name="idsolicitud_certificacion" value="<?php echo $row_opp['idsolicitud_certificacion']; ?>">
                <input type="hidden" name="cancelar" value="cancelar">
              </form>
            </td>


        </tr>
        <?php }  ?>
        <? if($cont==0){?>
        <tr><td colspan="13" class="alert alert-info" role="alert">No se encontraron registros</td></tr>
        <? }?>
    </table>


  </div>
</div>

<table>
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
</table>
<?php
mysql_free_result($opp);
?>
