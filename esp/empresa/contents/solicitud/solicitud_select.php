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

/**** VARIABLES GLOBALES *******/
$spp_global = "cert@spp.coop";
$finanzas_spp = "adm@spp.coop";
$administrador = "yasser.midnight@gmail.com";
$fecha = time();
$idempresa = $_SESSION['idempresa'];
/********************************/


/*************************** VARIABLES DE CONTROL **********************************/
  $estado_interno = "2";

//  $validacionStatus = $row_empresa['status'] != 1 && $row_empresa['status'] != 2 && $row_empresa['status'] != 3 && $row_empresa['status'] != 14 && $row_empresa['status'] != 15;

/*************************** VARIABLES DE CONTROL **********************************/
/// INICIA SE ACEPTA O RECHAZA COTIZACIÓN
if(isset($_POST['cotizacion']) ){
  $row_empresa = mysql_query("SELECT solicitud_registro.*, empresa.idempresa, empresa.nombre AS 'nombre_empresa', empresa.abreviacion AS 'abreviacion_empresa', empresa.telefono, empresa.email, empresa.pais, oc.nombre AS 'nombre_oc', oc.email1 AS 'email_oc', oc.email2 AS 'email_oc2' FROM solicitud_registro LEFT JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa LEFT JOIN oc ON solicitud_registro.idoc = oc.idoc WHERE idsolicitud_registro = $_POST[idsolicitud_registro]", $dspp) or die(mysql_error());
  $detalle_empresa = mysql_fetch_assoc($row_empresa);


  $estatus_dspp = $_POST['cotizacion'];
  
  if($estatus_dspp == 5){ // se acepta la cotización, modificamos la solicitud y fijamos las fechas del periodo de objeción
    $asunto_empresa = "D-SPP Cotización de Solicitud Aceptada";

    $updateSQL = sprintf("UPDATE solicitud_registro SET estatus_dspp = %s, fecha_aceptacion = %s WHERE idsolicitud_registro = %s",
      GetSQLValueString(5, "int"),
      GetSQLValueString($fecha, "int"),
      GetSQLValueString($_POST['idsolicitud_registro'], "int"));
    $actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());

    if($_POST['tipo_solicitud'] == 'RENOVACION'){
        $mensaje_empresa = '
          <html>
          <head>
            <meta charset="utf-8">
          </head>
          <body>
          
            <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
              <tbody>
                <tr>
                  <th rowspan="6" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                  <th scope="col" align="left" width="280"><strong>Cotización de Solicitud Aceptada / Application Price Quote Accepted ('.date('d/m/Y', $fecha).')</strong></th>
                </tr>

                <tr>
                  <td align="left" style="color:#ff738a;">
                    Felicidades se ha aceptado su cotización, ahora <span style="color:red">puede iniciar el Proceso de Certificación</span>, ya que esta solicitud se encuentra en proceso de "RENOVACIÓN DEL REGISTRO" :
                    <br><br>
                    Congratulations. Your price quote has been accepted. <span style="color:red">You may now begin the Certification Process</span>, since your application is in the "REGISTRATION RENEWAL" process.
                  </td>
                </tr>

                <tr>
                  <td align="left">Teléfono Empresa / Company phone: '.$detalle_empresa['telefono'].'</td>
                </tr>
                <tr>
                  <td align="left">País / Country: '.$detalle_empresa['pais'].'</td>
                </tr>
                <tr>
                  <td align="left" style="color:#ff738a;">Nombre: '.$detalle_empresa['contacto1_nombre'].' | '.$detalle_empresa['contacto1_email'].'</td>
                </tr>
                <tr>
                  <td align="left" style="color:#ff738a;">Nombre: '.$detalle_empresa['contacto2_nombre'].' | '.$detalle_empresa['contacto2_email'].'</td>
                </tr>


                <tr>
                  <td colspan="2">
                    <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;" border="1" width="650px">
                      <tbody>
                        <tr style="font-size: 12px; text-align:center; background-color:#dff0d8; color:#3c763d;" height="50px;">
                          <td width="162.5px">Nombre de la Empresa/Company name</td>
                          <td width="162.5px">Abreviación / Short name</td>
                          <td width="162.5px">País / Country</td>
                          <td width="162.5px">Organismo de Certificación / Certification Entity</td> 
                        </tr>
                        <tr style="font-size: 12px; text-align:justify">
                          <td style="padding:10px;">
                            '.$detalle_empresa['nombre_empresa'].'
                          </td>
                          <td style="padding:10px;">
                            '.$detalle_empresa['abreviacion_empresa'].'
                          </td>
                          <td style="padding:10px;">
                            '.$detalle_empresa['pais'].'
                          </td>
                          <td style="padding:10px;">
                            '.$detalle_empresa['nombre_oc'].'
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
      
      if(!empty($detalle_empresa['email_oc'])){
        //$mail->AddAddress($detalle_opp['email_opp']);
        $token = strtok($detalle_empresa['email_oc'], "\/\,\;");
        while ($token !== false)
        {
          $mail->AddAddress($token);
          $token = strtok('\/\,\;');
        }

      }

      if(!empty($detalle_empresa['email_oc2'])){
        //$mail->AddAddress($detalle_opp['email_opp']);
        $token = strtok($detalle_empresa['email_oc2'], "\/\,\;");
        while ($token !== false)
        {
          $mail->AddAddress($token);
          $token = strtok('\/\,\;');
        }

      }

      $mail->AddBCC($administrador);
      //$mail->Username = "soporte@d-spp.org";
      //$mail->Password = "/aung5l6tZ";
      $mail->Subject = utf8_decode($asunto_empresa);
      $mail->Body = utf8_decode($mensaje_empresa);
      $mail->MsgHTML(utf8_decode($mensaje_empresa));
      $mail->Send();
      $mail->ClearAddresses();

      $mensaje = "La cotización ha sido aceptada, en breve el Organismo de Certificación se pondra en contacto.";

    }else{
      //CALCULAMOS Y FIJAMOS EL PERIODO DE OBJECIÓN
      $periodo = 15*(24*60*60); //calculamos los segundos de 15 dias
      $fecha_inicio = time();
      $fecha_fin = $fecha + $periodo;
      $estatus_objecion = 'EN ESPERA';
      $alerta1 = 1; //se envia la primera alerta para pedir autorización al adm de que inicie el periodo de objeción

      //INSERTAMOS EL PERIODO DE OBJECIÓN
      $insertSQL = sprintf("INSERT INTO periodo_objecion (idsolicitud_registro, fecha_inicio, fecha_fin, estatus_objecion, alerta1) VALUES (%s, %s, %s, %s, %s)",
        GetSQLValueString($_POST['idsolicitud_registro'], "int"),
        GetSQLValueString($fecha_inicio, "int"),
        GetSQLValueString($fecha_fin, "int"),
        GetSQLValueString($estatus_objecion, "text"),
        GetSQLValueString($alerta1, "int"));
      $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

      //////// INICIA ENVIAR CORREO AL OC SOBRE LA ACEPTACION DE LA COTIZACION
      $row_productos = mysql_query("SELECT producto FROM productos WHERE idsolicitud_registro = $_POST[idsolicitud_registro]", $dspp) or die(mysql_error());
      $nombre_productos = '';
      while($producto = mysql_fetch_assoc($row_productos)){
        $nombre_productos .= $producto['producto']."<br>"; 
      } 
      $alcance = '';
      if(isset($detalle_empresa['produccion'])){
        $alcance .= 'PRODUCCION - PRODUCTION.<br>';
      }
      if(isset($detalle_empresa['procesamiento'])){
        $alcance .= 'PROCESAMIENTO - PROCESSING.<br>';
      }
      if(isset($detalle_empresa['exportacion'])){
        $alcance .= 'EXPORTACIÓN - TRAIDING.<br>';
      }


      $asunto_oc = "D-SPP Cotización de Solicitud Aceptada";
      
      $mensaje_oc = '
        <html>
        <head>
          <meta charset="utf-8">
        </head>
        <body>
        
          <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
            <tbody>
              <tr>
                <th rowspan="6" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                <th scope="col" align="left" width="280"><strong>Notificación de Propuesta / Application Price Quote Accepted ('.date('d/m/Y', $fecha).')</strong></th>
              </tr>

              <tr>
                <td align="left" style="color:#ff738a;">
                  Felicidades se ha aceptado su cotización, será informado una vez que inicie el período de objeción:
                  <br><br>
                  Congratulations. Your price quote has been accepted. You will be informed as soon as the objection period begins.
                </td>
              </tr>

              <tr>
                <td align="left">Teléfono / Telephono: '.$detalle_empresa['telefono'].'</td>
              </tr>
              <tr>
                <td align="left">País / Country: '.$detalle_empresa['pais'].'</td>
              </tr>
              <tr>
                <td align="left" style="color:#ff738a;">Nombre: '.$detalle_empresa['contacto1_nombre'].' | '.$detalle_empresa['contacto1_email'].'</td>
              </tr>
              <tr>
                <td align="left" style="color:#ff738a;">Nombre: '.$detalle_empresa['contacto2_nombre'].' | '.$detalle_empresa['contacto2_email'].'</td>
              </tr>


              <tr>
                <td colspan="2">
                  <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;" border="1" width="650px">
                    <tbody>
                      <tr style="font-size: 12px; text-align:center; background-color:#dff0d8; color:#3c763d;" height="50px;">
                        <td width="162.5px">Nombre de la Empresa/Company name</td>
                        <td width="162.5px">Abreviación / Short name</td>
                        <td width="162.5px">País / Country</td>
                        <td width="162.5px">Organismo de Certificación / Certification Entity</td> 
                      </tr>
                      <tr style="font-size: 12px; text-align:justify">
                        <td style="padding:10px;">
                          '.$detalle_empresa['nombre_empresa'].'
                        </td>
                        <td style="padding:10px;">
                          '.$detalle_empresa['abreviacion_empresa'].'
                        </td>
                        <td style="padding:10px;">
                          '.$detalle_empresa['pais'].'
                        </td>
                        <td style="padding:10px;">
                          '.$detalle_empresa['nombre_oc'].'
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

      if(!empty($detalle_empresa['email_oc'])){
        //$mail->AddAddress($detalle_opp['email_opp']);
        $token = strtok($detalle_empresa['email_oc'], "\/\,\;");
        while ($token !== false)
        {
          $mail->AddAddress($token);
          $token = strtok('\/\,\;');
        }

      }
      if(!empty($detalle_empresa['email_oc2'])){
        //$mail->AddAddress($detalle_opp['email_opp']);
        $token = strtok($detalle_empresa['email_oc2'], "\/\,\;");
        while ($token !== false)
        {
          $mail->AddAddress($token);
          $token = strtok('\/\,\;');
        }

      }


      $mail->AddBCC($administrador);
      //$mail->Username = "soporte@d-spp.org";
      //$mail->Password = "/aung5l6tZ";
      $mail->Subject = utf8_decode($asunto_oc);
      $mail->Body = utf8_decode($mensaje_oc);
      $mail->MsgHTML(utf8_decode($mensaje_oc));
      $mail->Send();
      $mail->ClearAddresses();
      /////// TERMINA ENVIAR CORREO AL OC SOBRE LA ACEPTACIÓN DE LA COTIZACION

      ////// INICIA ENVIAR CORREO AL ADMINISTRADOR PARA APROBAR PERIODO DE OBJECIÓN
      $asunto_adm = "D-SPP Aprobación Periodo de Objeción";

      $tipo = '';
      if(isset($detalle_empresa['comprador_final'])){
        $tipo .= 'COMPRADOR FINAL / FINAL BUYER<br>';
      }
      if(isset($detalle_empresa['intermediario'])){
        $tipo .= 'INTERMEDIARIO / INTERMEDIARY<br>';
      }
      if(isset($detalle_empresa['maquilador'])){
        $tipo .= 'MAQUILADOR / MAQUILA COMPANY<br>';
      }

      $mensaje_adm = '
                  <html>
                  <head>
                    <meta charset="utf-8">
                  </head>
                  <body>
                  
                    <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="700px">
                      <thead>
                        <tr>
                          <th>
                            <img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" />
                          </th>
                          <th style="text-align:left">
                            D-SPP Cotización Aceptada | Aviso Notificación de Intenciones
                            <br><br>Se ha aceptado la cotización de: <span style="color:red">'.$detalle_empresa['nombre_oc'].'</span>
                          </th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr style="text-align:left">
                          <td colspan="2"><p style="color:red;"><b>El Periodo de Objeción tendra fecha del:</b></p></td>
                        </tr>
                        <tr> 
                          <td colspan="2">Fecha Inicio: <span style="color:red">'.date('d/m/Y', $fecha_inicio).'</span></td>
                        </tr>
                        <tr>
                          <td colspan="2">Fecha Fin: <span style="color:red">'.date('d/m/Y', $fecha_fin).'</span></td>
                        </tr>
                        <tr>
                          <td colspan="2">
                            <p style="font-size:14px;color:red">Para aprobar el Periodo de Objeción debe ingresar en su cuenta de Administrador</p>
                          </td>
                        </tr>

                        <tr style="width:100%">
                          <td colspan="2">
                            <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;" border="1" width="650px">

                              <tr style="font-size: 12px; text-align:center; background-color:#dff0d8; color:#3c763d;" height="50px;">
                                <td style="text-align:center">Tipo / Type</td>
                                <td style="text-align:center">Nombre de la Empresa/Company name</td>
                                <td style="text-align:center">Abreviación / Short name</td>
                                <td style="text-align:center">País / Country</td>
                                <td style="text-align:center">Organismo de Certificación / Certification Entity</td>

                                <td style="text-align:center">Tipo de solicitud / Kind of application</td>
                                <td style="text-align:center">Fecha de solicitud/Date of application</td>
                                <td style="text-align:center">Fin período de objeción/Objection period end</td>
                              </tr>
                              <tr>
                                <td>'.$tipo.'</td>
                                <td>'.$detalle_empresa['nombre_empresa'].'</td>
                                <td>'.$detalle_empresa['abreviacion_empresa'].'</td>
                                <td>'.$detalle_empresa['pais'].'</td>
                                <td>'.$detalle_empresa['nombre_oc'].'</td>
                                <td>Registro / Registration</td>
                                <td>'.date('d/m/Y', $fecha_inicio).'</td>
                                <td>'.date('d/m/Y', $fecha_fin).'</td>
                              </tr>
                          </td>
                          </table>
                        </tr>
                      </tbody>
                    </table>

                  </body>
                  </html>
      ';

      $mail->AddAddress($spp_global);
      $mail->AddBCC($administrador);
      //$mail->Username = "soporte@d-spp.org";
      //$mail->Password = "/aung5l6tZ";
      $mail->Subject = utf8_decode($asunto_adm);
      $mail->Body = utf8_decode($mensaje_adm);
      $mail->MsgHTML(utf8_decode($mensaje_adm));
      $mail->Send();
      $mail->ClearAddresses();


      ////// TERMINA ENVIAR CORREO AL ADMINISTRADOR PARA APROBAR PERIODO DE OBJECIÓN

      $mensaje = "La cotización ha sido aceptada, el periodo de objeción ha empezado, en breve seras contactado";

    }

  }else{
    $updateSQL = sprintf("UPDATE solicitud_registro SET estatus_dspp = %s WHERE idsolicitud_registro = %s",
      GetSQLValueString(17, "int"),
      GetSQLValueString($_POST['idsolicitud_registro'], "int"));
    $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());
    $mensaje = "La cotización ha sido rechazada";
  }

  //INSERTAMOS EL PROCESO DE CERTIFICACIÓN
  $insertSQL = sprintf("INSERT INTO proceso_certificacion (idsolicitud_registro, estatus_dspp, fecha_registro) VALUES (%s, %s, %s)",
    GetSQLValueString($_POST['idsolicitud_registro'], "int"),
    GetSQLValueString($estatus_dspp, "int"),
    GetSQLValueString($fecha, "int"));
  $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
}
/// TERMINA SE ACEPTA O RECHAZA COTIZACIÓN

/// INICIA ENVIAR COMPROBANTE DE PAGO
if(isset($_POST['enviar_comprobante']) && $_POST['enviar_comprobante'] == 1){
  $estatus_dspp = 10; //membresia cargada
  $estatus_comprobante = 'ENVIADO';
  $nombre = "COMPROBANTE DE PAGO";
  $rutaArchivo = "../../archivos/empresaArchivos/membresia/";

  if(!empty($_FILES['comprobante_pago']['name'])){
      $_FILES["comprobante_pago"]["name"];
        move_uploaded_file($_FILES["comprobante_pago"]["tmp_name"], $rutaArchivo.$fecha."_".$_FILES["comprobante_pago"]["name"]);
        $comprobante_pago = $rutaArchivo.basename($fecha."_".$_FILES["comprobante_pago"]["name"]);
  }else{
    $comprobante_pago = NULL;
  }

  $updateSQL = sprintf("UPDATE solicitud_registro SET estatus_dspp = %s WHERE idsolicitud_registro = %s",
    GetSQLValueString($estatus_dspp, "int"),
    GetSQLValueString($_POST['idsolicitud_registro'], "int"));
  $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());
  //creamos el proceso de certificacion
  $insertSQL = sprintf("INSERT INTO proceso_certificacion(idsolicitud_registro, estatus_dspp, nombre_archivo, archivo, fecha_registro) VALUES(%s, %s, %s, %s, %s)",
    GetSQLValueString($_POST['idsolicitud_registro'], "int"),
    GetSQLValueString($estatus_dspp, "int"),
    GetSQLValueString($nombre, "text"),
    GetSQLValueString($comprobante_pago, "text"),
    GetSQLValueString($fecha, "int"));
  $insertar = mysql_query($insertSQL,$dspp) or die(mysql_error());

  //actualizamos el comprobante de pago membresia
  $updateSQL = sprintf("UPDATE comprobante_pago SET estatus_comprobante = %s, archivo = %s, fecha_registro = %s WHERE idcomprobante_pago = %s",
    GetSQLValueString($estatus_comprobante, "text"),
    GetSQLValueString($comprobante_pago, "text"),
    GetSQLValueString($fecha, "int"),
    GetSQLValueString($_POST['idcomprobante_pago'], "int"));
  $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

  //inicia correo enviar comprobante de pago
  $row_informacion = mysql_query("SELECT membresia.idempresa, membresia.idcomprobante_pago, empresa.nombre, comprobante_pago.monto FROM membresia INNER JOIN empresa ON membresia.idempresa = empresa.idempresa INNER JOIN comprobante_pago ON membresia.idcomprobante_pago = comprobante_pago.idcomprobante_pago WHERE membresia.idcomprobante_pago = $_POST[idcomprobante_pago]", $dspp) or die(mysql_error());
  $informacion = mysql_fetch_assoc($row_informacion);

  $asunto = "D-SPP | Comprobante de Pago por Aprobar";

  $cuerpo_mensaje = '
    <html>
    <head>
      <meta charset="utf-8">
    </head>
    <body>
      <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979; text-align:justify" border="0" width="650px">
        <tbody>
          <tr>
            <th rowspan="2" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
            <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">Comprobante de pago </span></p></th>

          </tr>
          <tr>
           <th scope="col" align="left" width="280"><p>OPP: <span style="color:red">'.$informacion['nombre'].'</span></p></th>
          </tr>

          <tr>
            <td colspan="2">
             <p>La OPP: <span style="color:red">'.$informacion['nombre'].'</span> ha cargado el Comprobante de Pago de la membresia SPP por un monto total de: <span style="color:red">'.$informacion['monto'].'</span>.</p>
            </td>
          </tr>
          <tr>
            <td colspan="2">
              <p>Después de revisa el comprobante <span style="color:red">debe ingresar en su cuenta de administrador dentro del D-SPP, para poder APROBAR o RECHAZAR el comprobante de pago</span> </p>
              <p>Pasos para "Aprobar" o "Rechazar" el Comprobante de pago:</p>
              <ol>
                <li>Debe ingresar en su cuenta de Adminitrador dentro del D-SPP.</li>
                <li>Seleccionar "Solicitudes" > "Solicitudes Empresas".</li>
                <li>Dentro de la tabla de Solicitudes debe localizar la solicitud de la Empresa.</li>
                <li>Posicionarse sobre la columna "Membresia" y dar clic en el botón "Estatus Membresia".</li>
                <li>Se mostrara una ventana donde podra "Aprobar" o "Rechazar" el comprobante.</li>
              </ol>
            </td>
          </tr>
        </tbody>
      </table>
    </body>
    </html>
  ';
    $mail->AddAddress($spp_global);
    $mail->AddAddress($finanzas_spp);
    $mail->AddAttachment($comprobante_pago);
    //$mail->Username = "soporte@d-spp.org";
    //$mail->Password = "/aung5l6tZ";
    $mail->Subject = utf8_decode($asunto);
    $mail->Body = utf8_decode($cuerpo_mensaje);
    $mail->MsgHTML(utf8_decode($cuerpo_mensaje));
    /*$mail->Send();
    $mail->ClearAddresses();*/

  //termina correo enviar comprobante de pago
    if($mail->Send()){
      $mail->ClearAddresses();
      echo "<script>alert('Se ha enviado el comprobante de pago, en breve sera contactado.');location.href ='javascript:history.back()';</script>";
    }else{
      $mail->ClearAddresses();
      echo "<script>alert('Error, no se pudo enviar el correo, por favor contacte al administrador: soporte@d-spp.org');location.href ='javascript:history.back()';</script>";
    }
  $mensaje = "Se ha enviado el comprobante de pago, en breve sera contactado.";
}
/// TERMINA ENVIAR COMPROBANTE DE PAGO

/// INICIA ENVIAR CONTRATO DE USO
if(isset($_POST['enviar_contrato']) && $_POST['enviar_contrato'] == 1){
  $estatus_dspp = 11; //CONTRATO CARGADO
  $estatus_contrato = "ENVIADO";
  $rutaArchivo = "../../archivos/admArchivos/contratos/";
  $nombre = "CONTRATO DE USO";


  // se carga el Contrato de Uso
  if(!empty($_FILES['contrato']['name'])){
      $_FILES["contrato"]["name"];
        move_uploaded_file($_FILES["contrato"]["tmp_name"], $rutaArchivo.$fecha."_".$_FILES["contrato"]["name"]);
        $contrato = $rutaArchivo.basename($fecha."_".$_FILES["contrato"]["name"]);
  }else{
    $contrato = NULL;
  }

  // se carga el ACUSE DE Recibo
  if(!empty($_FILES['acuse_recibo']['name'])){
      $_FILES["acuse_recibo"]["name"];
        move_uploaded_file($_FILES["acuse_recibo"]["tmp_name"], $rutaArchivo.$fecha."_".$_FILES["acuse_recibo"]["name"]);
        $acuse_recibo = $rutaArchivo.basename($fecha."_".$_FILES["acuse_recibo"]["name"]);
  }else{
    $acuse_recibo = NULL;
  }
  $updateSQL = sprintf("UPDATE solicitud_registro SET estatus_dspp = %s WHERE idsolicitud_registro = %s",
    GetSQLValueString($estatus_dspp, "int"),
    GetSQLValueString($_POST['idsolicitud_registro'], "int"));
  $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());
  //insertamos el contrato
  $insertSQL = sprintf("INSERT INTO contratos(idsolicitud_registro, nombre, archivo, acuse_recibo, estatus_contrato, fecha_registro) VALUES (%s, %s, %s, %s, %s, %s)",
    GetSQLValueString($_POST['idsolicitud_registro'], "int"),
    GetSQLValueString($nombre, "text"),
    GetSQLValueString($contrato, "text"),
    GetSQLValueString($acuse_recibo, "text"),
    GetSQLValueString($estatus_contrato, "text"),
    GetSQLValueString($fecha, "int"));
  $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

  //insertamos el proceso_certificacion
  $insertSQL = sprintf("INSERT INTO proceso_certificacion (idsolicitud_registro, estatus_dspp, nombre_archivo, archivo, fecha_registro) VALUES (%s, %s, %s, %s, %s)",
    GetSQLValueString($_POST['idsolicitud_registro'], "int"),
    GetSQLValueString($estatus_dspp, "text"),
    GetSQLValueString($nombre, "text"),
    GetSQLValueString($contrato, "text"),
    GetSQLValueString($fecha, "int"));
  $insertar = mysql_query($insertSQL,$dspp) or die(mysql_error());

  //inicia enviar mensaje contrato de uso
  $row_informacion = mysql_query("SELECT solicitud_registro.idempresa, empresa.nombre FROM solicitud_registro INNER JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa WHERE idsolicitud_registro = $_POST[idsolicitud_registro]", $dspp) or die(mysql_error());
  $informacion = mysql_fetch_assoc($row_informacion);

  $asunto = "D-SPP | Contrato de Uso y Acuse de Recibo";

  $cuerpo_mensaje = '
          <html>
          <head>
            <meta charset="utf-8">
          </head>
          <body>
            <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979; text-align:justify" border="0" width="650px">
              <tbody>
                <tr>
                  <th rowspan="2" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                  <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">Aprobación Contrato de Uso y Acuse de Recibo </span></p></th>

                </tr>
                <tr>
                 <th scope="col" align="left" width="280"><p>OPP: <span style="color:red">'.$informacion['nombre'].'</span></p></th>
                </tr>

                <tr>
                  <td colspan="2">
                   <p>La OPP: '.$informacion['nombre'].' ha cargado el "Contrato de Uso" y "Acuse de Recibo".</p>
                  </td>
                </tr>
                <tr>
                  <td colspan="2">
                    <p>Después de revisa el "Contrato de Uso" y "Acuse de Recibo" <span style="color:red">debe ingresar en su cuenta de administrador dentro del D-SPP, para poder APROBAR o RECHAZAR el mismo</span> </p>
                    <p>¿Cómo "Aprobar" o "Rechazar" el Contrato de Uso?</p>
                    <ol>
                      <li>Debe de ingresar en su cuenta de administrador dentro del D-SPP.</li>
                      <li>Seleccionar "Solicitudes" > "Solicitudes OPP".</li>
                      <li>Localizar la Solicitud de la OPP: '.$informacion['nombre'].'.</li>
                      <li>Posicionarse sobre la columna "Certificado" y dar clic en el bóton "Consultar Certificado".</li>
                      <li>Se mostrara una ventana donde podra "Aprobar" o "Rechazar" el Contrato de Uso.</li>
                    </ol>
                  </td>
                </tr>
              </tbody>
            </table>
          </body>
          </html>
  ';
    $mail->AddAddress($spp_global);
    $mail->AddAttachment($contrato);
    $mail->AddAttachment($acuse_recibo);
    //$mail->Username = "soporte@d-spp.org";
    //$mail->Password = "/aung5l6tZ";
    $mail->Subject = utf8_decode($asunto);
    $mail->Body = utf8_decode($cuerpo_mensaje);
    $mail->MsgHTML(utf8_decode($cuerpo_mensaje));
    /*$mail->Send();
    $mail->ClearAddresses();*/
    if($mail->Send()){
      $mail->ClearAddresses();
      echo "<script>alert('Se ha enviado el Contrato de Uso Y Acuse de Recibo, en breve sera contactado.');location.href ='javascript:history.back()';</script>";
    }else{
      $mail->ClearAddresses();
      echo "<script>alert('Error, no se pudo enviar el correo, por favor contacte al administrador: soporte@d-spp.org');location.href ='javascript:history.back()';</script>";
    }

  //termina enviar mensaje contrato de uso
  //$mensaje = "Se ha enviado el Contrato de Uso, en breve sera contactado";
}
/// TERMINA ENVIAR CONTRATO DE USO


$query = "SELECT solicitud_registro.*, oc.abreviacion AS 'abreviacionOC', periodo_objecion.idperiodo_objecion, periodo_objecion.fecha_inicio, periodo_objecion.fecha_fin, periodo_objecion.estatus_objecion, periodo_objecion.observacion, periodo_objecion.dictamen, periodo_objecion.documento, membresia.idmembresia, certificado.idcertificado, contratos.idcontrato FROM solicitud_registro INNER JOIN oc ON solicitud_registro.idoc = oc.idoc LEFT JOIN periodo_objecion ON solicitud_registro.idsolicitud_registro  = periodo_objecion.idsolicitud_registro LEFT JOIN membresia ON solicitud_registro.idsolicitud_registro = membresia.idsolicitud_registro LEFT JOIN certificado ON solicitud_registro.idsolicitud_registro = certificado.idsolicitud_registro LEFT JOIN contratos ON solicitud_registro.idsolicitud_registro = contratos.idsolicitud_registro WHERE solicitud_registro.idempresa = $idempresa GROUP BY solicitud_registro.idsolicitud_registro ORDER BY solicitud_registro.fecha_registro DESC";
$row_solicitud_registro = mysql_query($query, $dspp) or die(mysql_error());
$total_solicitudes = mysql_num_rows($row_solicitud_registro);



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
    <table class="table table-bordered" style="font-size:12px;">
      <thead>
        <tr class="success">
          <th class="text-center">ID</th>
          <th class="text-center"><a href="#" data-toggle="tooltip" title="Tipo de Solicitud"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>Tipo</a></th>
          <th class="text-center">Fecha</th>
          <th class="text-center">OC</th>
          <th class="text-center">Estatus Solicitud</th>
          <th class="text-center">Cotización</th>
          <th class="text-center">Proceso de Objecion</th>
          <th class="text-center">Proceso Certificación</th>
          <th class="text-center">Membresía SPP</th>
          <th class="text-center">Certificado</th>
          <th class="text-center">Acciones</th>
        </tr>
      </thead>
      <tbody>

        <?php 
        if($total_solicitudes != 0){
          while($solicitud = mysql_fetch_assoc($row_solicitud_registro)){
          $query_proceso = "SELECT proceso_certificacion.*, proceso_certificacion.idsolicitud_registro, estatus_publico.nombre AS 'nombre_publico', estatus_interno.nombre AS 'nombre_interno', estatus_dspp.nombre AS 'nombre_dspp', membresia.idmembresia, membresia.estatus_membresia, membresia.idcomprobante_pago, membresia.fecha_registro FROM proceso_certificacion LEFT JOIN estatus_publico ON proceso_certificacion.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON proceso_certificacion.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON proceso_certificacion.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN membresia ON proceso_certificacion.idsolicitud_registro = membresia.idsolicitud_registro WHERE proceso_certificacion.idsolicitud_registro =  $solicitud[idsolicitud_registro] ORDER BY proceso_certificacion.idproceso_certificacion DESC LIMIT 1";
          $ejecutar = mysql_query($query_proceso,$dspp) or die(mysql_error());
          $proceso_certificacion = mysql_fetch_assoc($ejecutar);
          ?>
            <tr>
              <td>
                <form action="../../reportes/solicitud.php" method="POST" target="_new">
                  <button class="btn btn-xs btn-default" data-toggle="tooltip" title="Descargar solicitud" target="_new" type="submit" ><?php echo $solicitud['idsolicitud_registro']; ?> <img src="../../img/pdf.png" style="height:30px;" alt=""></button>

                  <input type="hidden" name="idsolicitud_registro" value="<?php echo $solicitud['idsolicitud_registro']; ?>">
                  <input type="hidden" name="generar_formato" value="1">
                </form>
                
            
              </td>
          <!--<form action="" method="POST" enctype="multipart/form-data">-->
              <input type="hidden" name="idsolicitud_registro" value="<?php echo $solicitud['idsolicitud_registro']; ?>">
              <!---- inicia TIPO SOLICITUD ---->
              <td <?php if($solicitud['tipo_solicitud'] == 'NUEVA'){ echo "class='success'"; }else{ echo "class='warning'"; } ?>class="warning">
                <?php echo $solicitud['tipo_solicitud']; ?>
              </td>
              <!---- inicia TIPO SOLICITUD ---->

              <td><?php echo date('d/m/Y',$solicitud['fecha_registro']); ?></td>
              <td><?php echo $solicitud['abreviacionOC']; ?></td>
              <td><?php echo $proceso_certificacion['nombre_dspp']; ?></td>
              <td>
                <!--- INICIA FORMULARIO PARA ACEPTAR COTIZACION ---->
                <form action="" id="frm_cotizacion" method="POST" enctype="application/x-www-form-urlencoded">
                  <?php
                  if(isset($solicitud['cotizacion_empresa'])){
                    echo "<a class='btn btn-info form-control' style='font-size:12px;color:white;height:30px;' href='".$solicitud['cotizacion_empresa']."' target='_blank'><span class='glyphicon glyphicon-download' aria-hidden='true'></span> Descargar Cotización</a>";

                     if($proceso_certificacion['estatus_dspp'] == 5){ // SE ACEPTA LA COTIZACIÓN
                      if($solicitud['tipo_solicitud'] == 'RENOVACION'){
                        echo 'ACEPTADA';
                      }else{
                        echo "<p class='alert alert-success' style='padding:2px;'>Estatus: ".$proceso_certificacion['nombre_dspp']."</p>"; 
                      }
                     }else if($proceso_certificacion['estatus_dspp'] == 17){ // SE RECHAZA LA COTIZACIÓN
                      echo "<p class='alert alert-danger' style='padding:2px;'>Estatus: ".$proceso_certificacion['nombre_dspp']."</p>"; 
                     }else{
                      if(empty($solicitud['fecha_aceptacion'])){ //si inicio el periodo de objecion quiere decir que se acepto la cotización
                      ?>
                        <div class="text-center">
                          <button class='btn btn-xs btn-success' type="submit" name="cotizacion" value="5" style='width:45%' data-toggle="tooltip" data-placement="bottom" title="Aceptar cotización"><span class='glyphicon glyphicon-ok'></span></button>

                          <button class='btn btn-xs btn-danger' style='width:45%' name="cotizacion" value="17" data-toggle="tooltip" data-placement="bottom" title="Rechazar cotización"><span class='glyphicon glyphicon-remove'></span></button>
                          <input type="hidden" name="idsolicitud_registro" value="<?php echo $solicitud['idsolicitud_registro']; ?>">
                          <input type="hidden" name="tipo_solicitud" value="<?php echo $solicitud['tipo_solicitud']; ?>">
                        </div>
                      <?php //
                     }
                    }
                  }else{
                    echo "COTIZACIÓN EMPRESA";
                  }
                  ?>
                </form>
                <!--- INICIA FORMULARIO PARA ACEPTAR COTIZACION ---->
              </td>
              <td>
                <?php
                $row_objecion = mysql_query("SELECT * FROM periodo_objecion WHERE idsolicitud_registro = $solicitud[idsolicitud_registro]", $dspp) or die(mysql_error());
                $objecion = mysql_fetch_assoc($row_objecion);

                if($solicitud['tipo_solicitud'] == 'RENOVACION'){
                ?>
                  <a href="#" data-toggle="tooltip" title="Esta solicitud se encuentra en Proceso de Renovación del Certificado por lo tanto no aplica el periodo de objeción" style="padding:7px;"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>SOLICITUD EN RENOVACIÓN</a>  
                <?php
                }else{
                  if(empty($objecion['idperiodo_objecion'])){
                    echo "No Disponible";
                  }else if($objecion['estatus_objecion'] == 'EN ESPERA'){ // no se muestra nada si esta en espera
                    echo "No Disponible";
                  }else{ // si se autorizo se muestra:
                    if(empty($objecion['documento'])){ //si no se ha cargado un documento se muestra el estatus
                    ?>
                      <p class="alert alert-info" style="margin-bottom:0;padding:2px;">Inicio: <?php echo date('d/m/Y', $objecion['fecha_inicio']); ?></p>
                      <p class="alert alert-danger" style="margin-bottom:0;padding:2px;">Fin: <?php echo date('d/m/Y', $objecion['fecha_fin']); ?></p>
                    <?php
                    }else{ // se muestra boton descargar resolución y dictamen del mismo
                     ?>
                      <p class="alert alert-info" style="margin-bottom:0;padding:2px;">Inicio: <?php echo date('d/m/Y', $objecion['fecha_inicio']); ?></p>
                      <p class="alert alert-danger" style="margin-bottom:0;padding:2px;">Fin: <?php echo date('d/m/Y', $objecion['fecha_fin']); ?></p>

                     <p class="alert alert-success" style="margin-bottom:0;padding:2px;">Dictamen: <?php echo $objecion['dictamen']; ?></p>
                     <a class="btn btn-info" style="font-size:12px;width:100%;" href='<?php echo $objecion['documento']; ?>' target='_blank'><span class='glyphicon glyphicon-download' aria-hidden='true'></span> Descargar Resolución</a> 

                    <?php
                    }
                  }  
                }              
                ?>
              </td>
              <!----- INICIA PROCESO CERTIFICACIÓN ---->
              <td>
                <?php
                if($solicitud['tipo_solicitud'] == 'RENOVACION' && $proceso_certificacion['estatus_dspp'] == 5){ // SE ACEPTA LA COTIZACIÓN EN PROCESO DE RENOVACIÓN
                ?>
                  <button type="button" class="btn btn-sm btn-primary" style="width:100%" data-toggle="modal" data-target="<?php echo "#certificacion".$solicitud['idperiodo_objecion']; ?>">Proceso Certificación</button>
                <?php
                }else{
                  if(!empty($solicitud['fecha_aceptacion']) && $solicitud['tipo_solicitud'] == 'RENOVACION'){
                  ?>
                    <button type="button" class="btn btn-sm btn-primary" style="width:100%" data-toggle="modal" data-target="<?php echo "#certificacion".$solicitud['idperiodo_objecion']; ?>">Proceso Certificación</button>
                  <?php
                  }else{
                    if(isset($solicitud['estatus_objecion']) && $solicitud['estatus_objecion'] == 'FINALIZADO' && isset($solicitud['documento'])){
                    ?>
                      <button type="button" class="btn btn-sm btn-primary" style="width:100%" data-toggle="modal" data-target="<?php echo "#certificacion".$solicitud['idperiodo_objecion']; ?>">Proceso Certificación</button>
                    <?php
                    }else{
                      echo "<button class='btn btn-sm btn-default' disabled>Proceso Certificación</button>";
                    }
                  }

                }
                ?>
              </td>

                <!-- inicia modal proceo de certificación -->
                <div id="<?php echo "certificacion".$solicitud['idperiodo_objecion']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                  <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Proceso de Certificación</h4>
                      </div>
                      <div class="modal-body">
                        <div class="row">

                            <div class="col-md-12">
                              Historial Estatus Certificación
                            </div>
                            <?php 
                            $row_proceso_certificacion = mysql_query("SELECT proceso_certificacion.*, estatus_interno.nombre FROM proceso_certificacion INNER JOIN estatus_interno ON proceso_certificacion.estatus_interno = estatus_interno.idestatus_interno WHERE idsolicitud_registro = $solicitud[idsolicitud_registro] AND estatus_interno IS NOT NULL", $dspp) or die(mysql_error());
                            while($historial_certificacion = mysql_fetch_assoc($row_proceso_certificacion)){
                            echo "<div class='col-md-10'>Proceso: $historial_certificacion[nombre]</div>";
                            echo "<div class='col-md-2'>Fecha: ".date('d/m/Y',$historial_certificacion['fecha_registro'])."</div>";
                            }
                             ?>

                        </div>
                      </div>
                      <div class="modal-footer">
                        <input type="hidden" name="idperiodo_objecion" value="<?php echo $solicitud['idperiodo_objecion']; ?>">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        <!--<button type="button" class="btn btn-primary">Guardar Cambios</button>-->
                      </div>
                    </div>
                  </div>
                </div>
                <!-- termina modal proceo de certificación -->

              <!----- TERMINA PROCESO CERTIFICACIÓN ---->

              <!----- INICIA MEMBRESIA ------>
              <td>
                <?php 
                if(isset($solicitud['idmembresia'])){
                  $row_membresia = mysql_query("SELECT membresia.*, comprobante_pago.monto, comprobante_pago.estatus_comprobante, comprobante_pago.archivo FROM membresia LEFT JOIN comprobante_pago ON membresia.idcomprobante_pago = comprobante_pago.idcomprobante_pago WHERE idmembresia = $solicitud[idmembresia]", $dspp) or die(mysql_error());
                  $membresia = mysql_fetch_assoc($row_membresia);
                ?>
                  <button type="button" class="btn btn-sm btn-primary" style="width:100%" data-toggle="modal" data-target="<?php echo "#membresia".$solicitud['idperiodo_objecion']; ?>">Estatus Membresia</button>
                <?php
                }else{
                  echo "MEMBRESIA";
                }
                 ?>

                <!-- inicia modal estatus membresia -->

                <div id="<?php echo "membresia".$solicitud['idperiodo_objecion']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                  <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                      <!--- INICIA FORMULARIO MEMBRECIA -->
                      <form action="" id="frm_membresia" method="POST" enctype="multipart/form-data">
                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                          <h4 class="modal-title" id="myModalLabel">Proceso Membresía</h4>
                        </div>
                        <div class="modal-body">
                          <div class="row">
                            <div class="col-md-6">
                              <p class="alert alert-info">Debe cargar el comprobante de pago de la membresia SPP por el monto de <b style="color:red;"><?php echo $membresia['monto'] ?></b>, una vez cargado debe dar clic en enviar. Sera notificado por correo una vez que sea aprobada su membresia</p>
                              <p class="alert alert-warning">Estatus Actual: <?php echo $membresia['estatus_comprobante']; ?></p>
                            </div>
                            <div class="col-md-6">
                              <?php 
                              if(isset($membresia['archivo'] )){
                                if($membresia['estatus_comprobante'] == 'ACEPTADO'){
                                  echo "<h4 class='alert alert-success'>Felicidades tu membresía se encuentra activa</h4>";
                                }else if($membresia['estatus_comprobante'] == 'RECHAZADO'){
                                  echo "<p class='alert alert-warning'>Se han encontrado irregularidades en su comprobante de pago, por favor reviselo y cargue otro</p>";
                                  echo "<p>Observaciones: $membresia[observaciones]</p>";
                                ?>
                                  <p class="alert alert-info">
                                    Cargar Comprobante de pago
                                    <input type="file" class="form-control" name="comprobante_pago">
                                  </p>
                                <?php
                                }else{
                                  echo "SE HA CARGADO EL COMPRANTE POR FAVOR ESPERE";
                                }
                              }else{
                              ?>
                                <p class="alert alert-info">
                                  Cargar Comprobante de pago
                                  <input type="file" class="form-control" name="comprobante_pago">
                                </p>
                              <?php
                              }
                              ?>
                            </div>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <input type="hidden" name="idperiodo_objecion" value="<?php echo $solicitud['idperiodo_objecion']; ?>">
                          <input type="hidden" name="idcomprobante_pago" value="<?php echo $membresia['idcomprobante_pago']; ?>">
                          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                          <?php 
                          if($membresia['estatus_comprobante'] == 'RECHAZADO' || $membresia['estatus_comprobante'] == 'EN ESPERA'){
                            echo '<button type="submit" class="btn btn-primary" name="enviar_comprobante" value="1">Enviar Comprobante</button>';
                          }
                           ?>
                        </div>

                      </form>
                      <!--- TERMINA FORMULARIO MEMBRECIA -->
                    </div>
                  </div>
                </div>
                <!-- termina modal estatus membresia -->

              </td>
              <!----- TERMINA MEMBRESIA ------>

              <!---- INICIA CONSULTAR CERTIFICADO ---->
              <td>
                <?php 
                if(isset($solicitud['idmembresia'])){
                ?>
                  <button type="button" class="btn btn-sm btn-primary" style="width:100%" data-toggle="modal" data-target="<?php echo "#certificado".$solicitud['idsolicitud_registro']; ?>">Consultar Certificado</button>
                <?php
                }else{
                ?>
                  <button type="button" class="btn btn-sm btn-default" style="width:100%" disabled>Consultar Certificado</button>
                <?php
                }
                 ?>
                <!-- inicia modal estatus_Certificado -->

                <div id="<?php echo "certificado".$solicitud['idsolicitud_registro']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                  <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">

                      <!--- INICIA FORMULARIO CERTIFICADO -->
                      <form action="" id="frm_certificado" method="POST" enctype="multipart/form-data">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title" id="myModalLabel">Proceso Certificado</h4>
                          </div>
                          <div class="modal-body">
                            <div class="row">
                              <div class="col-md-6">
                                <p style="margin-bottom:0px;" class="alert alert-warning">
                                  <?php 
                                  if($solicitud['tipo_solicitud'] == 'RENOVACION'){
                                    echo "Esta Organización se encuentra en proceso de Renovación del Certificado por lo tanto no debe enviar Contrato de Uso";
                                  }else{
                                    if(isset($solicitud['idcontrato'])){
                                      $row_contrato = mysql_query("SELECT * FROM contratos WHERE idcontrato = $solicitud[idcontrato]", $dspp) or die(mysql_error());
                                      $contrato = mysql_fetch_assoc($row_contrato);
                                    ?>
                                      Se ha cargado el contrato y Acuse de Recibo
                                    <?php
                                    }else{
                                    ?>
                                      <?php 
                                      if(isset($membresia['idcomprobante_pago'])){
                                      ?>
                                        <b>Debe cargar el <span style="color:red">"Contrato de Uso" y "Acuse de Recibo"</span> firmado, para poder completar el proceso de certficación</b>
                                        <div style="padding:0px;margin-bottom:0px;" class="alert alert-warning form-group">
                                          <label for="contrato">Contrato de Uso</label>
                                          <input type="file" id="contrato" name="contrato">
                                        </div>
                                        <div style="padding:0px;margin-bottom:0px;" class="alert alert-warning form-group">
                                          <label for="acuse_recibo">Acuse de Recibo</label>
                                          <input type="file" id="acuse_recibo" name="acuse_recibo">
                                        </div>
                                        <!--<input type="file" name="contrato" class="form-control">
                                        <input type="file" name="acuse_recibo" class="form-control">-->
                                        <button type="submit" class="btn btn-success" style="width:100%" name="enviar_contrato" value="1">Enviar Contrato y Acuse de Recibo</button>

                                      <?php
                                      }
                                       ?>
                                    <?php
                                    }
                                     
                                  }
                                  ?>
                                </p>

                                <?php 
                                if($solicitud['tipo_solicitud'] == 'RENOVACION'){

                                }else{
                                  if(isset($solicitud['idcontrato'])){
                                    echo "<p class='alert alert-info'>Estatus del Contrato: <span style='color:red'>".$contrato['estatus_contrato']."</span></p>";
                                    if($contrato['estatus_contrato'] == "ENVIADO"){
                                      echo "<p class='alert alert-warning'>El contratos se encuentra en proceso de revisión. Sera notificado una vez que sea \"APROBADO\" o \"RECHAZADO\"</p>";
                                    }else if($contrato['estatus_contrato'] == "ACEPTADO"){
                                      echo "<p class='alert alert-success'><b>Se ha aprobado el contrato</b></p>";
                                    }else if($contrato['estatus_contrato'] == "RECHAZADO"){
                                      echo "<p class='alert alert-danger'>Se ha encontrado irregularidades en el contrato</p>";
                                      echo "<p>Observaciones: <span>".$contrato['observaciones']."</span></p>";
                                    ?>
                                      <b>Debe cargar el <span style="color:red">"Contrato de Uso" y "Acuse de Recibo"</span> firmado, para poder completar el proceso de certficación</b>
                                        <div style="padding:0px;margin-bottom:0px;" class="alert alert-warning form-group">
                                          <label for="contrato">Contrato de Uso</label>
                                          <input type="file" id="contrato" name="contrato">
                                        </div>
                                        <div style="padding:0px;margin-bottom:0px;" class="alert alert-warning form-group">
                                          <label for="acuse_recibo">Acuse de Recibo</label>
                                          <input type="file" id="acuse_recibo" name="acuse_recibo">
                                        </div>

                                      <!--<input type="file" name="contrato" class="form-control">
                                      <input type="file" name="acuse_recibo" class="form-control">-->
                                      <button type="submit" class="btn btn-success" style="width:100%" name="enviar_contrato" value="1">Enviar Contrato y Acuse de Recibo</button>
                                    <?php
                                    }
                                  }else{
                                    echo '<p class="alert alert-danger">No se ha cargado el "Contrato de Uso"</p>';
                                  }
                                }
                                ?>
                                
                              </div>
                              <div class="col-md-6">
                                <h4>Certificado</h4>
                                <?php 
                                if(isset($solicitud['idcertificado'])){
                                  $row_certificado = mysql_query("SELECT * FROM certificado WHERE idcertificado = $solicitud[idcertificado]", $dspp) or die(mysql_error());
                                  $certificado = mysql_fetch_assoc($row_certificado);
                                  if(isset($certificado['idsolicitud_registro'])){
                                    $inicio = strtotime($certificado['vigencia_inicio']);
                                    $fin = strtotime($certificado['vigencia_fin']);

                                    echo "<h4 class='alert alert-success'>Felicidades tu Certificado ha sido aprobado.<br> El certificado tienen una vigencia del <span style='color:red'>".date('d/m/Y', $inicio)."</span> al  <span style='color:red'>".date('d/m/Y', $fin)."</span></h4>";
                                    echo "<a href='".$certificado['archivo']."' class='btn btn-success' style='width:100%' target='_blank'>Descargar Certificado</a>";
                                  }else{
                                    echo "<p class='alert alert-danger'>El Certificado aun no esta disponible</p>";
                                  }
                                }else{
                                  echo "<p class='alert alert-danger'>El Certificado aun no esta disponible</p>";
                                }
                                 ?>
                              </div>
                            </div>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                          </div>
                          <input type="hidden" name="idsolicitud_registro" value="<?php echo $solicitud['idsolicitud_registro']; ?>">
                      </form>
                      <!-- TERMINA FORMULARIO CERTICADO-->
                    </div>
                  </div>
                </div>
                <!-- termina modal estatus_Certificado -->
              </td>
              <!----- TERMINA CONSULTAR CERTIFICADO ---->

              <td>
                <input type="hidden" name="tipo_solicitud" value="<?php echo $solicitud['tipo_solicitud']; ?>">
                <a class="btn btn-sm btn-primary" style="display:inline-block" href="?SOLICITUD&amp;detail&amp;idsolicitud=<?php echo $solicitud['idsolicitud_registro']; ?>" data-toggle="tooltip" title="Visualizar Solicitud" >
                  Consultar
                </a>
               <!-- <form action="" method="POST"  style="display:inline-block">-->
                  <button class="btn btn-sm btn-danger" name="eliminar_solicitud" value="1" data-toggle="tooltip" title="Eliminar Solicitud" type="submit" onclick="return confirm('¿Está seguro?, los datos se eliminaran permanentemente');">
                    <span aria-hidden="true" class="glyphicon glyphicon-trash"></span>
                  </button>         
                <!--</form>-->
              </td>
            </tr>

          <?php
          }
        }else{
        ?>
          <tr class="info text-center">
            <td colspan="10">No se encontraron registros</td>
          </tr>
        <?php
        }
         ?>
      </tbody>
    </table>
  </div>
</div>



<hr>


<!--
<table>
<tr>
<td width="20"><?php if ($pageNum_empresa > 0) { // Show if not first page ?>
<a href="<?php printf("%s?pageNum_empresa=%d%s", $currentPage, 0, $queryString_empresa); ?>">
<span class="glyphicon glyphicon-fast-backward" aria-hidden="true"></span>
</a>
<?php } // Show if not first page ?></td>
<td width="20"><?php if ($pageNum_empresa > 0) { // Show if not first page ?>
<a href="<?php printf("%s?pageNum_empresa=%d%s", $currentPage, max(0, $pageNum_empresa - 1), $queryString_empresa); ?>">
<span class="glyphicon glyphicon-backward" aria-hidden="true"></span>
</a>
<?php } // Show if not first page ?></td>
<td width="20"><?php if ($pageNum_empresa < $totalPages_empresa) { // Show if not last page ?>
<a href="<?php printf("%s?pageNum_empresa=%d%s", $currentPage, min($totalPages_empresa, $pageNum_empresa + 1), $queryString_empresa); ?>">
<span class="glyphicon glyphicon-forward" aria-hidden="true"></span>
</a>
<?php } // Show if not last page ?></td>
<td width="20"><?php if ($pageNum_empresa < $totalPages_empresa) { // Show if not last page ?>
<a href="<?php printf("%s?pageNum_empresa=%d%s", $currentPage, $totalPages_empresa, $queryString_empresa); ?>">
<span class="glyphicon glyphicon-fast-forward" aria-hidden="true"></span>
</a>
<?php } // Show if not last page ?></td>
</tr>
</table>-->
