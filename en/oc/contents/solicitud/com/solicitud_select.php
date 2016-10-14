<?php 
require_once('../Connections/dspp.php');
require_once('../Connections/mail.php');



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

if(isset($_POST['com_delete'])){
	$query=sprintf("delete from com where idcom = %s",GetSQLValueString($_POST['idcom'], "text"));
	$ejecutar=mysql_query($query,$dspp) or die(mysql_error());
}

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_com = 20;
$pageNum_com = 0;
if (isset($_GET['pageNum_com'])) {
  $pageNum_com = $_GET['pageNum_com'];
}
$startRow_com = $pageNum_com * $maxRows_com;

mysql_select_db($database_dspp, $dspp);
if(isset($_GET['query'])){
  $query_com = "SELECT com.* ,solicitud_registro.* FROM solicitud_registro INNER JOIN com ON solicitud_registro.idcom = com.idcom WHERE solicitud_registro.idoc = $_SESSION[idoc] AND solicitud_registro.status_interno != 24 ORDER BY solicitud_registro.fecha_elaboracion DESC";

	#$query_com = "SELECT * FROM solicitud_registro where idsolicitud_registro ='".$_GET['query']."' ORDER BY fecha DESC";
}else{
  #SELECT solicitud_registro.* FROM solicitud_registro INNER JOIN com ON solicitud_registro.idcom = com.idcom WHERE com.idcom = 15

  $query_com = "SELECT com.* ,solicitud_registro.* FROM solicitud_registro INNER JOIN com ON solicitud_registro.idcom = com.idcom WHERE solicitud_registro.idoc = $_SESSION[idoc] AND solicitud_registro.status_interno != 24 ORDER BY solicitud_registro.fecha_elaboracion DESC"; 

  #$query_com = "SELECT com.* ,solicitud_registro.* FROM solicitud_registro INNER JOIN com ON solicitud_registro.idcom = com.idcom ORDER BY solicitud_registro.fecha_elaboracion ASC";  

	#$query_com = "SELECT * FROM solicitud_registro ORDER BY fecha ASC";
}
  //DEFINO LA RUTA DEL FORMATO DE anexo
  //$rutaArchivo = "../formatos/anexos/";
  $rutaArchivo = "../../archivos/ocArchivos/anexos/";

if(isset($_POST['statusCertificado']) && $_POST['statusCertificado'] == "statusCertificado"){
  //$queryFecha = "INSERT INTO fecha (fecha, idexterno, identificador, status) VALUES($fecha_actual, $idexterno, '$identificador', '$status')"; 
  //$insertarFecha = mysql_query($queryFecha, $dspp) or die(mysql_error());

  $fecha = date("d/m/Y", $_POST['statusFecha']);
  $fecha_actual = time();
  $idcom = $_POST['idcom'];
  $idoc = $_POST['idoc'];
  $status = $_POST['status'];
  $idsolicitud_registro = $_POST['idsolicitud_registro'];
  $registro1 = $_POST['registro1'];

  $query = "INSERT INTO certificado (status, idsolicitud_registro, idcom, entidad) VALUES ('$status', $idsolicitud_registro, $idcom, $idoc)";
  $certificado = mysql_query($query, $dspp) or die(mysql_error());

  $idcertificado = mysql_insert_id($dspp);
  $identificador = "CERTIFICADO";
  $idexterno = $idsolicitud_registro;
  
  $nombreArchivoEstatus = $_POST['nombreArchivoEstatus'];
    $archivoDictamen = '';
    $archivoExtra = '';

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

      $query = "INSERT INTO proceso_certificacion(idstatus,idcom,idoc,idsolicitud_registro,registro,archivo,nombre,fecha) VALUES($status,$idcom,$idoc,$idsolicitud_registro,'$registro1','$adjuntoEstatus','$nombreArchivo',$fecha_actual)";
      $proceso_certificacion = mysql_query($query,$dspp) or die(mysql_error());


  $queryFecha = "INSERT INTO fecha (fecha, idexterno, idcom, idoc, idcertificado, identificador, status) VALUES($fecha_actual, $idexterno, $idcom, $idoc, $idcertificado, '$identificador', '$status')"; 
  $insertarFecha = mysql_query($queryFecha, $dspp) or die(mysql_error());

  $query = "UPDATE solicitud_registro SET status_interno = '$status' WHERE idsolicitud_registro = $idsolicitud_registro";
  $actualizar = mysql_query($query,$dspp) or die(mysql_error());

  if($status == 8){// INICIA IF $status == 8 *********************/
    $asunto = "Contrato de Uso del Simbolo de Pequeños Productores - SPP";
    
    $querySolicitud = "SELECT solicitud_registro.idcom,solicitud_registro.p1_correo, solicitud_registro.p2_correo,com.idcom, com.email FROM solicitud_registro LEFT JOIN com ON solicitud_registro.idcom = com.idcom WHERE idsolicitud_registro = $idsolicitud_registro";

        $rowSolicitud = mysql_query($querySolicitud,$dspp) or die(mysql_error());
        $datosSolicitud = mysql_fetch_assoc($rowSolicitud);

/*        $destinatarios .= $datosSolicitud['p1_email'];
        $destinatarios .= $datosSolicitud['p2_email'];
        $destinatarios .= $datosSolicitud['email'];
  */    
        $mail->AddAddress($datosSolicitud['p1_correo']);
        $mail->AddAddress($datosSolicitud['p2_correo']);
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
      if(!empty($mensajeCOM)){
        $mensajeDefault = $mensajeCOM;
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

  }// TERMINA IF $status == 8  *********************/

}

  if (isset($_POST['actualizarStatus']) && $_POST['actualizarStatus'] == "actualizarStatus") {
    $fecha = date("d/m/Y", $_POST['statusFecha']);
    $fecha_actual = time();
    $idcom = $_POST['idcom'];
    $idoc = $_POST['idoc'];
    $idsolicitud_registro = $_POST['idsolicitud_registro'];
    $idcertificado = $_POST['idcertificado'];
    $status = $_POST['statusOption'];    
    $identificador = "CERTIFICADO";
    $idexterno = $idsolicitud_registro;

    $query = "UPDATE certificado SET status = '$status' WHERE idcertificado = $idcertificado";
    $actualizar = mysql_query($query, $dspp) or die(mysql_error());


    
    $registro1 = $_POST['registro1'];

    if(!empty($_POST['nombreArchivoEstatus'])){
      $nombreArchivo = $_POST['nombreArchivoEstatus'];
    }else{
      $nombreArchivo = $_POST['nombreArchivo'];
    }


    if(isset($_POST['montoMembresia'])){
      $montoMembresia = $_POST['montoMembresia'];
    }else{
      $montoMembresia = NULL;
    }

    $mensajeCOM = $_POST['mensajeCOM'];
    $archivoDictamen = '';
    $archivoExtra = '';


    $anexoNombres = "";
    $query_anexos = "SELECT * FROM anexos WHERE idstatus_interno = 8";
    $row_anexos = mysql_query($query_anexos,$dspp) or die(mysql_error());
    //$queryFecha = "INSERT INTO fecha (fecha, idexterno, identificador, status) VALUES($fecha_actual, $idcertificado, '$identificador', '$status')"; 
    //$insertarFecha = mysql_query($queryFecha, $dspp) or die(mysql_error());

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

      $query = "INSERT INTO proceso_certificacion(idstatus,idcom,idoc,idsolicitud_registro,registro,archivo,nombre,fecha) VALUES($status,$idcom,$idoc,$idsolicitud_registro,'$registro1','$adjuntoEstatus','$nombreArchivo',$fecha_actual)";
      $proceso_certificacion = mysql_query($query,$dspp) or die(mysql_error());

    $idexterno = $idsolicitud_registro;
    $identificador = "CERTIFICADO";

    $queryFecha = "INSERT INTO fecha (fecha, idexterno, idcom, idoc, idcertificado, identificador, status) VALUES($fecha_actual, $idexterno, $idcom, $idoc, $idcertificado, '$identificador', '$status')";
    $insertarFecha = mysql_query($queryFecha, $dspp) or die(mysql_error());

    $query = "UPDATE solicitud_registro SET status_interno = '$status' WHERE idsolicitud_registro = $idsolicitud_registro";
    $actualizar = mysql_query($query,$dspp) or die(mysql_error());



    if($status == 8){


        $asunto = "Contrato de uso del Simbolo de Pequeños Productores - SPP";

        $querySolicitud = "SELECT solicitud_registro.idcom,solicitud_registro.p1_correo, solicitud_registro.p2_correo,com.idcom, com.email FROM solicitud_registro LEFT JOIN com ON solicitud_registro.idcom = com.idcom WHERE idsolicitud_registro = $idsolicitud_registro";
        $rowSolicitud = mysql_query($querySolicitud,$dspp) or die(mysql_error());
        $datosSolicitud = mysql_fetch_assoc($rowSolicitud);

        $mail->AddAddress($datosSolicitud['p1_correo']);
        $mail->AddAddress($datosSolicitud['p2_correo']);
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
      if(!empty($mensajeCOM)){
        $mensajeDefault = "<pre>".$mensajeCOM."</pre>";
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
                 <th scope="col" align="left" width="280"><p>Para: <span style="color:red">'.$_POST['nombreCOM'].'</span></p></th>
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
  $fecha_actual = time();
  $fecha = date("d/m/Y", $_POST['fechaCarga']);
  $idcom = $_POST['idcom'];
  $idoc = $_POST['idoc'];
  $ruta = "../../archivos/ocArchivos/certificados/";

  if(!empty($_FILES['certificado_fld']['name'])){
    $_FILES['certificado_fld']['name'];
        move_uploaded_file($_FILES["certificado_fld"]["tmp_name"], $ruta.$fecha_actual."_".$_FILES["certificado_fld"]["name"]);
        //direccion del certificado
        $adjunto = $ruta.basename($fecha_actual."_".$_FILES["certificado_fld"]["name"]);
  }else{
    $adjunto = NULL;
  }
  
  $vigenciaInicio = $_POST['vigenciaInicio'];
  $vigenciaFin = $_POST['vigenciaFin'];
  //$idoc = $_POST['certificadoIdoc'];
  //$idcom = $_POST['certificadoIdcom'];
  $statusPago = "POR REALIZAR";
  $idcertificado = $_POST['idcertificado'];
  $idexterno = $_POST['idsolicitud_registro'];
  $identificador = "CERTIFICADO";

  $nombrecom = $_POST['nombrecom'];


  $query = "UPDATE certificado SET vigenciainicio = '$vigenciaInicio', vigenciafin = '$vigenciaFin', adjunto = '$adjunto', statuspago = '$statusPago', fechaupload = $fecha_actual WHERE idcertificado = $idcertificado";
  $certificado = mysql_query($query, $dspp) or die(mysql_error());
  //echo "la consulta es: ".$query;

  $queryFecha = "INSERT INTO fecha (fecha, idexterno, idcom, idoc, idcertificado, identificador, status) VALUES($fecha_actual, $idexterno, $idcom, $idoc, $idcertificado, '$identificador', '$statusPago')";
  $insertarFecha = mysql_query($queryFecha, $dspp) or die(mysql_error());

            $destinatario = "cert@spp.coop";
            $asunto = "D-SPP - Certificado disponible( ".$nombrecom." )"; 

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
                      <td><b>Se ha cargado el certificado correpondiente a '.$nombrecom.', se notificara cuando se haya realizado el pago correpondiente a la membresia por parte del COM.</b></td>
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


                $queryMensaje = "INSERT INTO mensajes(idcom, idoc,asunto, mensaje, destinatario, remitente, fecha) VALUES($idcom, $idoc, '$asunto', '$cuerpo', 'ADM', 'OC', $fecha_actual)";
                $ejecutar = mysql_query($queryMensaje,$dspp) or die(mysql_error());

                /************************  FIN MAIL FUNDEPPO  *********************************************/


                /************************  INICIA MAIL com  *******************************************/
            $emailcom1 = $_POST['emailcom1'];
            $emailcom2 = $_POST['emailcom2'];


            $asunto = "D-SPP - Certificado disponible( ".$nombrecom." )"; 

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
                      <td>Se ha cargado su certificado dentro del sistema, por favor proceda a realizar el pago correspondiente a la membresia, después de realizar el pago subir el comprobante a nuestra plataforma dentro de su sesión de com <a href="http://d-spp.org/?COM">www.d-spp.org/?COM</a>, para poder realizarlo diríjase a la sección de "SOLICITUDES", en el apartado de "Certificación" podra realizar esté proceso.</td>
                    </tr>
         
                    <tr>
                      <td align="left" style="color:#ff738a;">En caso de alguna duda por favor envienos un correo a : cert@spp.coop</td>
                    </tr>
            

                  </tbody>
                </table>

              </body>
              </html>
            ';
        $mail->AddAddress($emailcom1);
        $mail->AddAddress($emailcom2);

        //$mail->Username = "soporte@d-spp.org";
        //$mail->Password = "/aung5l6tZ";
        $mail->Subject = utf8_decode($asunto);
        $mail->Body = utf8_decode($cuerpo);
        $mail->MsgHTML(utf8_decode($cuerpo));
        $mail->Send();
        $mail->ClearAddresses();


                $queryMensaje = "INSERT INTO mensajes(idcom, idoc,asunto, mensaje, destinatario, remitente, fecha) VALUES($idcom, $idoc, '$asunto', '$cuerpo', 'COM', 'OC', $fecha_actual)";
                $ejecutar = mysql_query($queryMensaje,$dspp) or die(mysql_error());
}


$query_limit_com = sprintf("%s LIMIT %d, %d", $query_com, $startRow_com, $maxRows_com);
$com = mysql_query($query_limit_com, $dspp) or die(mysql_error());
//$row_com = mysql_fetch_assoc($com);

if (isset($_GET['totalRows_com'])) {
  $totalRows_com = $_GET['totalRows_com'];
} else {
  $all_com = mysql_query($query_com);
  $totalRows_com = mysql_num_rows($all_com);
}
$totalPages_com = ceil($totalRows_com/$maxRows_com)-1;

$queryString_com = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_com") == false && 
        stristr($param, "totalRows_com") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_com = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_com = sprintf("&totalRows_com=%d%s", $totalRows_com, $queryString_com);


///////////////////////////////////// VARIABLES DE CONTROL ////////////////////////////////////////
  //$validacionStatus = $row_com['status_interno'] != 1 && $row_com['status_interno'] != 2 && $row_com['status_interno'] != 3 && $row_com['status_interno'] != 14 && $row_com['status_interno'] != 15;
///////////////////////////////////// VARIABLES DE CONTROL ////////////////////////////////////////

?>
<?php 
  $query = "SELECT * FROM status";
  $ejecutarStatus = mysql_query($query,$dspp) or die(mysql_error());
 ?>


<div class="panel panel-default">
  <div class="panel-heading">Solicitudes</div>
  <div class="panel-body">

    <table class="table table-condensed table-striped table-striped" style="font-size:12px">
      <thead>
        <tr>
          <th class="text-center">ID</th>
          <th class="text-center">Fecha Solicitud</th>
          <th class="text-center">Empresa</th>
          <th class="text-center">Cotización (Descargable)</th>
          <th class="text-center">Resolución de Objeción</th>
          <!--<th class="text-center">Sitio WEB</th>-->
          <!--<th class="text-center">Contacto</th>-->
          <!--<th class="text-center">País</th>-->
          <th class="text-center">Status Publico</th>
          <th class="text-center">Status Interno</th>
          <th class="text-center" colspan="2">Certificado</th>
          <!--<th class="text-center">Propuesta</th>-->
          <th class="text-center">Observaciones Solicitud</th>
          <!--<th>OC</th>
          <th>Razón social</th>
          <th>Dirección fiscal</th>
          <th>RFC</th>-->
          <!--<th>Eliminar</th>-->
        </tr>
      </thead>
      <tbody>
        <?php $cont=0; while ($row_com = mysql_fetch_assoc($com)) {$cont++; ?>
          <tr <?php if($row_com['estado'] == 20){ echo "style='border-style:solid;border-color:#E74C3C'";} ?>>
            <?php  $fecha = $row_com['fecha_elaboracion']; ?>
              <td><?php echo $row_com['idsolicitud_registro']; ?></td>

  <!----------------------------------------- INICIA BOTON VER SOLICITUD---------------------------------------------->
              <td>
                <a class="btn btn-primary btn-sm" style="width:100%" href="?SOLICITUD&amp;detailCOM&amp;idsolicitud=<?php echo $row_com['idsolicitud_registro']; ?>" aria-label="Left Align">
                  <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
                  <?php echo  date("d/m/Y", $fecha); ?><br>Ver solicitud
                </a>
              </td>
  <!----------------------------------------- TERMINA BOTON VER SOLICITUD---------------------------------------------->


  <!----------------------------------------- INICIA SECCION ORGANIZACION ---------------------------------------------->
                <td>
                  <?php 
                    if(isset($row_com['abreviacion'])){
                      echo "<a href='?COM&detail&idcom=$row_com[idcom]'>".$row_com['abreviacion']."</a>";
                    }else if($row_com['nombre']){
                      echo "<a href='?COM&detail&idcom=$row_com[idcom]'>".$row_com['nombre']."</a>";
                    }else{
                      echo "No Disponible";
                    } 
                  ?>
                </td>
  <!----------------------------------------- TERMINA SECCION ORGANIZACION ---------------------------------------------->


  <!----------------------------------------- INICIA SECCION COTIZACION COM ---------------------------------------------->
              <td style="width:150px;">
                <a href="<?echo $row_com['cotizacion_com']?>" target="_blank" type="button" class="btn <?php if(empty($row_com['cotizacion_com'])){ echo 'btn-default btn-sm';}else{echo 'btn-success btn-sm';} ?>" aria-label="Left Align" <?php if(empty($row_com['cotizacion_com'])){echo "disabled";}?>>
                  <span class="glyphicon glyphicon-download-alt"></span> com
                </a> 

              <!----------------------------------------- COTIZACION FUNDEPPO ---------------------------------------------->
              <!--<td>
                <h6>
                  <a href="http://d-spp.org/oc/<?echo $row_com['cotizacion_adm']?>" target="_blank" type="button" class="btn <?php if(empty($row_com['cotizacion_adm'])){ echo 'btn-danger btn-sm';}else{echo 'btn-success btn-sm';} ?>" aria-label="Left Align" <?php if(empty($row_com['cotizacion_adm'])){echo "disabled";}?>>
                    <span class="glyphicon glyphicon-download-alt"></span> FUNDEPPO
                  </a> 
                </h6>       
              </td>-->
              <!----------------------------------------- COTIZACION FUNDEPPO ---------------------------------------------->

              <!----------------------------------------- PROPUESTA ---------------------------------------------->

                  <?php 
                    if($row_com['status_interno'] != 1 && $row_com['status_interno'] != 2 && $row_com['status_interno'] != 3 && $row_com['status_interno'] != 14 && $row_com['status_interno'] != 15 && $row_com['status_interno'] != 17 && $row_com['status_interno'] != 20 && $row_com['status_interno'] != 24){
                  ?>
                    <span class="alert alert-success" style="padding:7px;">Aceptada</span>       
                  <?php 
                    }else if($row_com['status_interno'] == 24){
                  ?>
                    <span class="alert alert-danger" style="padding:7px;">Rechazada</span>
                  <?php
                    }else{
                  ?>
                    <span style="color:rgb(153, 153, 153);background-color:#FFF;border:solid 1px #ddd;padding:7px;">Pendiente</span>           
                  <?php 
                    }
                  ?>      
                </h6>
              </td>
              <!----------------------------------------- PROPUESTA ---------------------------------------------->

  <!----------------------------------------- TERMINA SECCION COTIZACION COM ---------------------------------------------->


                  <?php 
                    $query = "SELECT * FROM certificado WHERE idsolicitud_registro = $row_com[idsolicitud_registro]";
                    $ejecutar = mysql_query($query, $dspp) or die(mysql_error());
                    $registroCertificado = mysql_fetch_assoc($ejecutar);

                    $queryObjecion = "SELECT * FROM objecion WHERE idsolicitud_registro = $row_com[idsolicitud_registro]";
                    $ejecutar2 = mysql_query($queryObjecion,$dspp);
                    $registroObjecion = mysql_fetch_assoc($ejecutar2); 

                    $query_ane = "SELECT * FROM anexos WHERE idstatus_interno = 8";
                    $anexos = mysql_query($query_ane,$dspp) or die(mysql_error());
                   ?>
  <!----------------------------------------- INICIA SECCIÓN DICTAMEN OBJECIÓN ---------------------------------------------->
            <td>
              <?php
              if(isset($registroObjecion['dictamen'])){
                echo "<p>Dictamen: <span style='color:red'>$registroObjecion[dictamen]</span></p>";
                echo "<a href='http://d-spp.org/adm/$registroObjecion[adjunto]' target='_blank'>Descargar</a>";
              }
               ?>
            </td>
  <!----------------------------------------- TERMINA SECCIÓN DICTAMEN OBJECIÓN ---------------------------------------------->

  <!----------------------------------------- INICIA SECCION STATUS PUBLICO ---------------------------------------------->
              <td>
              <?php 
                $query_status = "SELECT * FROM status_publico WHERE idstatus_publico = $row_com[status_publico]";
                $ejecutar = mysql_query($query_status,$dspp) or die(mysql_error());
                $estatus_publico = mysql_fetch_assoc($ejecutar);
               ?>
                <?php 
                  if($row_com['status_interno'] == 10){
                    echo "<p class='text-center alert alert-success' style='padding:7px;'><b><u>Certificado</u></b></p>";
                  }else{
                     echo "<p class='text-center alert alert-warning' style='padding:7px;'> <a href='#' data-toggle='tooltip' title='".$estatus_publico['descripcion_publica']."''>".$estatus_publico['nombre']."</a> </p>"; 
                  }
                ?>
              </td>
  <!----------------------------------------- TERMINA SECCION STATUS PUBLICO ---------------------------------------------->

  <!------------------------------------ INICIA SECCION STATUS INTERNO ------------------------------------>
                <td>
                  <?php 
                    $query_status = "SELECT * FROM status WHERE idstatus = $row_com[status_interno]";
                    $ejecutar = mysql_query($query_status,$dspp) or die(mysql_error());
                    $estatus_interno = mysql_fetch_assoc($ejecutar);

                    if($row_com['status_interno'] == 4 || $row_com['status_interno'] == 11 || $row_com['status_interno'] == 13 || $row_com['status_interno'] == 14 || $row_com['status_interno'] == 15 || $row_com['status_interno'] == 24){
                      $colorEstado = "class='text-center alert alert-danger'";
                    }else if($row_com['status_interno'] == 10){
                      $colorEstado = "class='text-center alert alert-success'";
                    }else{
                      $colorEstado = "class='text-center alert alert-warning'";
                    }
                   ?>

                  <p <?echo $colorEstado;?> style="padding:7px;">
                    <?php echo '<a href="#" data-toggle="tooltip" title="'.$estatus_interno['descripcion_interna'].'">'.$estatus_interno['nombre'].'</a>'; ?>
                  </p>

                </td>
  <!------------------------------------ TERMINA SECCION STATUS INTERNO ------------------------------------>

              <!----------------------------------------- STATUS CERTIFICADO -------------------------------------------->
             
                    <?php 
                      $query = "SELECT * FROM certificado WHERE idsolicitud_registro = $row_com[idsolicitud_registro]";
                      $ejecutar = mysql_query($query, $dspp) or die(mysql_error());
                      $registroCertificado = mysql_fetch_assoc($ejecutar);

                      $queryObjecion = "SELECT * FROM objecion WHERE idsolicitud_registro = $row_com[idsolicitud_registro]";
                      $ejecutar2 = mysql_query($queryObjecion,$dspp);
                      $registroObjecion = mysql_fetch_assoc($ejecutar2); 

                      $query_ane = "SELECT * FROM anexos WHERE idstatus_interno = 8";
                      $anexos = mysql_query($query_ane,$dspp) or die(mysql_error());


                     ?>


  <!----------------------------------------- INICIA SECCIÓN STATUS CERTIFICADO ---------------------------------------------->
              <td>     
                <?php if($row_com['status_interno'] != 1 && $row_com['status_interno'] != 2 && $row_com['status_interno'] != 3 && $row_com['status_interno'] != 14 && $row_com['status_interno'] != 15 && $row_com['status_interno'] != 17 && $row_com['status_interno'] != 20 && $row_com['status_interno'] != 24){ ?>
                  <?php if(isset($registroObjecion['idobjecion'])){ ?>
                    <button class="btn btn-success btn-sm" data-toggle="modal" <?php echo "data-target='#status".$row_com['idsolicitud_registro']."'"?>  >
                    <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Consultar<br>Status
                    </button>       
                  <?php }else{ ?>
                    <button class="btn btn-warning btn-sm" data-toggle="modal" <?php echo "data-target='#status".$row_com['idsolicitud_registro']."'"?>  >
                    <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Consultar<br>Status
                    </button>            
                  <?php } ?>

                <?php }else{ ?>
                  <button class="btn btn-default btn-sm" data-toggle="modal" <?php echo "data-target='#status".$row_com['idsolicitud_registro']."'"?>  disabled>
                  <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Consultar<br>Status
                  </button>                    
                <?php } ?>
              </td>
  <!----------------------------------------- TERMINA SECCIÓN STATUS CERTIFICADO ---------------------------------------------->


              <!----------------------------------------- MODAL STATUS CERTIFICADO -------------------------------------->
                    <form action="" method="post" id="statusCertificado" enctype="multipart/form-data">
                      <div class="modal fade" <?php echo "id='status".$row_com['idsolicitud_registro']."'" ?> tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog modal-lg" role="document">
                          <div class="modal-content">
                            <div class="modal-header">
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                              <h4 class="modal-title" id="myModalLabel">Status Certificación</h4>
                            </div>
                            <div class="modal-body">
                              <div class="row">
                                  <p class="alert alert-info">* Una vez finalizada la certificación, debe de seleccionar "Dictamen positivo" esto para poder cargar el certificado.</p>
                                  <?php if($row_com['status_publico'] != '8' &&  empty($registroObjecion['adjunto']) ){ ?>
                                    <div class="col-xs-12 alert alert-danger">
                                      <p>Una vez finalizado el periodo de objeción, podra iniciar el periodo de certificación.</p>
                                    </div>

                                  <?php }else{ ?>
                                    <div class="col-xs-12">
                                      <div class="col-xs-12">
                                        <?php if(!empty($registroCertificado['status'])){ ?>
                                          <?
                                            /*$query = "SELECT * FROM status WHERE idstatus = $registroCertificado[status]";
                                            $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
                                            $estatus = mysql_fetch_assoc($ejecutar);*/

                                            $queryProceso = "SELECT proceso_certificacion.*, status.idstatus, status.nombre AS 'nombreEstatus' FROM proceso_certificacion LEFT JOIN status ON proceso_certificacion.idstatus = status.idstatus WHERE idsolicitud_registro = $row_com[idsolicitud_registro]";
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
                                          
                                            <?php 
                                            if($row_com['estado'] != 10){
                                            ?>
                                              <div class="col-xs-12">
                                                <select name="statusOption" class="form-control" id="statusSelect" onchange="funcionSelect()" required>
                                                  <option class="form-control" value="">Seleccione un estado</option>
                                                  <?php /*while($row_status = mysql_fetch_assoc($ejecutarStatus)){ ?>
                                                    <?php 
                                                      if($row_status['idstatus'] != 1 && $row_status['idstatus'] != 2 && $row_status['idstatus'] != 3 && $row_status['idstatus'] != 10 && $row_status['idstatus'] != 17 && $row_status['idstatus'] != 18 && $row_status['idstatus'] != 19 ){
                                                    ?>
                                                      <option class="form-control" value="<?echo $row_status['idstatus'];?>"><?echo $row_status['nombre'];?></option>                                        
                                                    <?php
                                                      }
                                                    ?>

                                                  <?php } */ ?>
                                                  <?php require_once("../option_estados.php"); ?>
                                                </select>
                                              </div>
                                            <?php
                                            }
                                             ?>

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
                                            <p>Para: <span style="color:red"><?php echo $row_com['nombre']; ?></span></p>
                                            <p>Correo(s): <?php echo $row_com['p1_correo']." ".$row_com['p2_correo']." ".$row_com['email']; ?></p>
                                            <p>Asunto: <span style="color:red">Contrato de uso del Simbolo de Pequeños Productores - SPP</span></p>
                                            
                                          </div>
                                        </div>
                                        <div class="col-xs-12">
                                          <h5 class="alert alert-warning">MENSAJE OPP( <small>Cuerpo del mensaje en caso de ser requerido</small>)</h5>
                                          <textarea name="mensajeCOM" class="form-control" id="" cols="30" rows="10" placeholder="Ingrese un mensaje en caso de que lo deseé"></textarea>

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
                                        <?php }else{ ?>
                                          <label class="control-label" for="status">Estatus Certificación</label>
                                          <div class="col-xs-12">
                                            <select name="statusOption" class="form-control" id="statusSelect" onchange="funcionSelect()" required>
                                              <option class="form-control" value="">Seleccione un estado</option>
                                              <?php /*while($row_status = mysql_fetch_assoc($ejecutarStatus)){ ?>
                                                <?php
                                                  if($row_status['idstatus'] != 1 && $row_status['idstatus'] != 2 && $row_status['idstatus'] != 3 && $row_status['idstatus'] != 10 && $row_status['idstatus'] != 17 && $row_status['idstatus'] != 18 && $row_status['idstatus'] != 19){
                                                ?>
                                                  <option class="form-control" value="<?echo $row_status['idstatus'];?>"><?echo $row_status['nombre'];?></option>                                        
                                                <?php
                                                  }
                                                ?>
                                              <?php } */ ?>
                                              <?php require_once("../option_estados.php"); ?>
                                            </select>
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
                                            <p>Para: <span style="color:red"><?php echo $row_com['nombre']; ?></span></p>
                                            <p>Correo(s): <?php echo $row_com['p1_correo']." ".$row_com['p2_correo']." ".$row_com['email']; ?></p>
                                            <p>Asunto: <span style="color:red">Contrato de uso del Simbolo de Pequeños Productores - SPP</span></p>
                                            
                                          </div>
                                        </div>
                                        <div class="col-xs-12">
                                          <h5 class="alert alert-warning">MENSAJE OPP( <small>Cuerpo del mensaje en caso de ser requerido</small>)</h5>
                                          <textarea name="mensajeCOM" class="form-control" id="" cols="30" rows="10" placeholder="Ingrese un mensaje en caso de que lo deseé"></textarea>

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


                                          <!--<input class="form-control" type="text" name="status" placeholder="Ingresar status">-->

                                        <?php } ?>
                                      </div>
                                    </div>



                                  <?php } ?>
                              </div>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                              <?php if(!empty($registroCertificado['status'])){ ?>

                                <input type="hidden" name="actualizarStatus" value="actualizarStatus">
                                <input type="hidden" name="idcertificado" value="<?echo $registroCertificado['idcertificado'];?>">
                                <input type="hidden" name="emailcom1" value="<?php echo $row_com['p1_correo'];?>">
                                <input type="hidden" name="emailcom2" value="<?php echo $row_com['p2_correo'];?>">
                                <input type="hidden" name="idcom" value="<?php echo $row_com['idcom'];?>">
                                <input type="hidden" name="idoc" value="<?php echo $row_com['idoc'];?>">

                                <button type="submit" class="btn btn-primary">Actualizar</button>

                              <?php }else{ ?>
                                <?php if(!empty($registroObjecion['adjunto']) || $row_com['status_publico'] == 8){ ?>
                                  <button type="submit" class="btn btn-primary">Guardar</button>

                                <input type="hidden" name="emailOPP1" value="<?php echo $row_opp['p1_email'];?>">
                                <input type="hidden" name="emailOPP2" value="<?php echo $row_opp['p2_email'];?>">
                                <input type="hidden" name="idopp" value="<?php echo $row_opp['idopp'];?>">
                                <input type="hidden" name="idoc" value="<?php echo $row_opp['idoc'];?>">
                                <input type="hidden" name="nombreOPP" value="<?php echo $row_opp['nombre'];?>">
                                <?php } ?>

                                <input type="hidden" name="statusCertificado" value="statusCertificado">
                              <?php } ?>

                                <input type="hidden" name="idcom" value="<?php echo $row_com['idcom'];?>">
                                <input type="hidden" name="nombreCOM" value="<?php echo $row_com['nombre']; ?>">
                                <input type="hidden" name="idoc" value="<?php echo $row_com['idoc'];?>">
                                <input type="hidden" name="statusFecha" value="<?echo time();?>">
                                <input type="hidden" name="idsolicitud_registro" value="<?echo $row_com['idsolicitud_registro'];?>">




                            </div>
                          </div>
                        </div>
                      </div>
                    </form>


              <!----------------------------------------- MODAL STATUS CERTIFICADO -------------------------------------->

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


  <!----------------------------------------- INICIA SECCION DETALLE CERTIFICADO ---------------------------------------------->
              <td>
                <?php if(isset($registroCertificado['status']) && ($registroCertificado['status'] == 8 || $registroCertificado['status'] == 10)){ ?>
                  <button class="btn btn-success btn-sm" data-toggle="modal" <?php echo "data-target='#certificado".$row_com['idsolicitud_registro']."'"?>  >
                  <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Detalle<br>Certificado
                  </button>            
                <?php }else{ ?>
                  <button class="btn btn-default btn-sm" data-toggle="modal" <?php echo "data-target='#certificado".$row_com['idsolicitud_registro']."'"?>  disabled>
                  <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Detalle<br>Certificado
                  </button>            
                <?php } ?>
              </td>
  <!----------------------------------------- INICIA SECCION DETALLE CERTIFICADO ---------------------------------------------->

              <!----------------------------------------- MODAL CERTIFICADO CERTIFICADO ---------------------------------->
                    <form action="" method="post" id="cargarCertificado" enctype="multipart/form-data">
                      <div class="modal fade" <?php echo "id='certificado".$row_com['idsolicitud_registro']."'" ?> tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
                                      <input name="certificado_fld" id="certificado_fld" type="file" class="filestyle" data-buttonName="btn-success" data-buttonBefore="true" data-buttonText="Cargar Certificado" required> 


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
                              <input type="hidden" name="idcom" value="<?php echo $row_com['idcom'];?>">
                              <input type="hidden" name="idoc" value="<?php echo $row_com['idoc'];?>">
                              <input type="hidden" name="emailcom1" value="<?php echo $row_com['p1_correo'];?>">
                              <input type="hidden" name="emailcom2" value="<?php echo $row_com['p2_correo'];?>">
                              <input type="hidden" name="nombrecom" value="<?php echo $row_com['nombre'];?>">
                              <input type="hidden" name="fechaCarga" value="<?echo time();?>">
                              <input type="hidden" name="idcertificado" value="<?echo $registroCertificado['idcertificado'];?>">
                              <input type="hidden" name="cargarCertificado" value="cargarCertificado">
                              <input type="hidden" name="idsolicitud_registro" value="<?echo $row_com['idsolicitud_registro'];?>">

                            </div>
                          </div>
                        </div>
                      </div>
                    </form>
              <!----------------------------------------- MODAL CERTIFICADO CERTIFICADO ---------------------------------->



  <!----------------------------------------- INICIA SECCIÓN OBSERVACIONES ---------------------------------------------->
              <td>
                <?php if(empty($row_com['observaciones'])){ ?>
                  <button class="btn btn-default btn-sm" disabled>
                    <span class="glyphicon glyphicon-list-alt"></span> Consultar
                  </button>         
                <?php }else{ ?>
                  <a class="btn btn-info btn-sm" href="?SOLICITUD&amp;detailCOM&amp;idsolicitud=<?php echo $row_com['idsolicitud_registro']; ?>">
                    <span class="glyphicon glyphicon-list-alt"></span> Consultar
                  </a>
                <?php } ?>
              </td>
  <!----------------------------------------- TERMINA SECCIÓN OBSERVACIONES ---------------------------------------------->

             

              <form action="" method="post">
              <!--<input class="btn btn-danger" type="submit" value="Eliminar" />-->
              <input type="hidden" value="com eliminado correctamente" name="mensaje" />
              <input type="hidden" value="1" name="com_delete" />
              <input type="hidden" value="<?php echo $row_com['idcom']; ?>" name="idcom" />
              </form>
              <!--</td>-->
          </tr>
          <?php }  ?>
          <? if($cont==0){?>
          <tr><td colspan="13" class="alert alert-info" role="alert">No se encontraron registros</td></tr>
          <? }?>

      </tbody>
    </table>
  </div>
</div>

<table>
<tr>
<td width="20"><?php if ($pageNum_com > 0) { // Show if not first page ?>
<a href="<?php printf("%s?pageNum_com=%d%s", $currentPage, 0, $queryString_com); ?>">
<span class="glyphicon glyphicon-fast-backward" aria-hidden="true"></span>
</a>
<?php } // Show if not first page ?></td>
<td width="20"><?php if ($pageNum_com > 0) { // Show if not first page ?>
<a href="<?php printf("%s?pageNum_com=%d%s", $currentPage, max(0, $pageNum_com - 1), $queryString_com); ?>">
<span class="glyphicon glyphicon-backward" aria-hidden="true"></span>
</a>
<?php } // Show if not first page ?></td>
<td width="20"><?php if ($pageNum_com < $totalPages_com) { // Show if not last page ?>
<a href="<?php printf("%s?pageNum_com=%d%s", $currentPage, min($totalPages_com, $pageNum_com + 1), $queryString_com); ?>">
<span class="glyphicon glyphicon-forward" aria-hidden="true"></span>
</a>
<?php } // Show if not last page ?></td>
<td width="20"><?php if ($pageNum_com < $totalPages_com) { // Show if not last page ?>
<a href="<?php printf("%s?pageNum_com=%d%s", $currentPage, $totalPages_com, $queryString_com); ?>">
<span class="glyphicon glyphicon-fast-forward" aria-hidden="true"></span>
</a>
<?php } // Show if not last page ?></td>
</tr>
</table>
<?php
mysql_free_result($com);
?>
