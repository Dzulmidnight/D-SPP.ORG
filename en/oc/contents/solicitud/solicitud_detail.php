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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}
/*********** INICIAN VARIABLES GLOBALES ************/
$idsolicitud_certificacion = $_GET['IDsolicitud'];
$charset='utf-8';
$administrador = "yasser.midnight@gmail.com";
$spp_global = "cert@spp.coop";
$fecha = time();
/*********** TERMINAN VARIABLES GLOBALES ************/

if(isset($_POST['guardar_cambios']) && $_POST['guardar_cambios'] == "1"){


  if(isset($_POST['op_preg12'])){
    $op_preg12 = $_POST['op_preg12'];
  }else{
    $op_preg12 = "";
  }

  if(isset($_POST['op_preg13'])){
    $op_preg13 = $_POST['op_preg13'];
  }else{
    $op_preg13 = "";
  }

  //ACTUALIZAMOS EL NUMERO DE SOCIOS
  $updateSQL = sprintf("UPDATE num_socios SET numero = %s WHERE fecha_registro = %s AND idopp = %s",
      GetSQLValueString($_POST['resp1'], "int"),
      GetSQLValueString($_POST['fecha_registro'], "int"),
      GetSQLValueString($_POST['idopp'], "int"));
  $actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());

  if(!empty($_POST['produccion'])){
    $produccion = $_POST['produccion'];
  }else{
    $produccion = '';
  }
  if(!empty($_POST['procesamiento'])){
    $procesamiento = $_POST['procesamiento'];
  }else{
    $procesamiento = '';
  }
  if(!empty($_POST['exportacion'])){
    $exportacion = $_POST['exportacion'];
  }else{
    $exportacion = '';
  }




  // ACTUALIZAMOS LA INFORMACION DE LA SOLICITUD
  $updateSQL = sprintf("UPDATE solicitud_certificacion SET resp1 = %s, resp2 = %s, resp3 = %s, resp4 = %s, op_preg1 = %s, preg1_1 = %s, preg1_2 = %s, preg1_3 = %s, preg1_4 = %s, op_preg2 = %s, op_preg3 = %s, produccion = %s, procesamiento = %s, exportacion = %s, op_preg5 = %s, op_preg6 = %s, op_preg7 = %s, op_preg8 = %s, op_preg10 = %s, op_preg14 = %s WHERE idsolicitud_certificacion = %s",
         GetSQLValueString($_POST['resp1'], "text"),
         GetSQLValueString($_POST['resp2'], "text"),
         GetSQLValueString($_POST['resp3'], "text"),
         GetSQLValueString($_POST['resp4'], "text"),
         GetSQLValueString($_POST['op_preg1'], "text"),
         GetSQLValueString($_POST['preg1_1'], "text"),
         GetSQLValueString($_POST['preg1_2'], "text"),
         GetSQLValueString($_POST['preg1_3'], "text"),
         GetSQLValueString($_POST['preg1_4'], "text"),
         GetSQLValueString($_POST['op_preg2'], "text"),
         GetSQLValueString($_POST['op_preg3'], "text"),
         GetSQLValueString($produccion, "int"),
         GetSQLValueString($procesamiento, "int"),
         GetSQLValueString($exportacion, "int"),
         GetSQLValueString($_POST['op_preg5'], "text"),
         GetSQLValueString($_POST['op_preg6'], "text"),
         GetSQLValueString($_POST['op_preg7'], "text"),
         GetSQLValueString($_POST['op_preg8'], "text"),
         GetSQLValueString($_POST['op_preg10'], "text"),
         //GetSQLValueString($op_preg12, "text"),
         //GetSQLValueString($op_preg13, "text"),
         GetSQLValueString($_POST['op_preg14'], "text"),
         GetSQLValueString($idsolicitud_certificacion, "int"));
  $actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());

    if(isset($op_preg12) && $op_preg12 == "SI"){
      if(!empty($_POST['organico']) || !empty($_POST['comercio_justo']) || !empty($_POST['spp']) || !empty($_POST['sin_certificado'])){
        $insertSQL = sprintf("INSERT INTO porcentaje_productoVentas (organico, comercio_justo, spp, sin_certificado, idsolicitud_certificacion, idopp) VALUES (%s, %s, %s, %s, %s, %s)",
          GetSQLValueString($_POST['organico'], "text"),
          GetSQLValueString($_POST['comercio_justo'], "text"),
          GetSQLValueString($_POST['spp'], "text"),
          GetSQLValueString($_POST['sin_certificado'], "text"),
          GetSQLValueString($idsolicitud_certificacion, "int"),
          GetSQLValueString($idopp, "int"));
        $insertar = mysql_query($insertSQL,$dspp) or die(mysql_error());
      }
    }  


    // SE ACTUALIZAN LAS CERTIFICACIONES

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
    $idcertificacion = $_POST['idcertificacion'];

    for($i=0;$i<count($certificacion);$i++){
      if($certificacion[$i] != NULL){
        #for($i=0;$i<count($certificacion);$i++){

        $updateSQL = sprintf("UPDATE certificaciones SET certificacion = %s, certificadora = %s, ano_inicial = %s, interrumpida = %s WHERE idcertificacion = %s",
          GetSQLValueString(strtoupper($certificacion[$i]), "text"),
          GetSQLValueString(strtoupper($certificadora[$i]), "text"),
          GetSQLValueString($ano_inicial[$i], "text"),
          GetSQLValueString($interrumpida[$i], "text"),
          GetSQLValueString($idcertificacion[$i], "int"));

        //$updateSQL = "UPDATE certificaciones SET certificacion= '".$certificacion[$i]."', certificadora='".$certificadora[$i]."', ano_inicial= '".$ano_inicial[$i]."', interrumpida= '".$interrumpida[$i]."' WHERE idcertificacion= '".$idcertificacion[$i]."'";

        $Result1 = mysql_query($updateSQL, $dspp) or die(mysql_error());
        }
    }

    // SE ACTUALIZAN LOS PRODUCTOS
      $producto = $_POST['producto'];
      $volumen = $_POST['volumen'];
      $materia = $_POST['materia'];
      $destino = $_POST['destino'];
      $idproducto = $_POST['idproducto'];
      /*$marca_propia = $_POST['marca_propia'];
      $marca_cliente = $_POST['marca_cliente'];
      $sin_cliente = $_POST['sin_cliente'];*/

    for ($i=0;$i<count($producto);$i++) { 
      if($producto[$i] != NULL){

      $array1 = "terminado".$i; 
      $array2 = "marca_propia".$i;
      $array3 = "marca_cliente".$i;
      $array4 = "sin_cliente".$i;


      if(isset($_POST[$array1])){
        $terminado = $_POST[$array1];
      }else{
        $terminado = '';
      }
      if(isset($_POST[$array2])){
        $marca_propia = $_POST[$array2];
      }else{
        $marca_propia = '';
      }
      if(isset($_POST[$array3])){
        $marca_cliente = $_POST[$array3];
      }else{
        $marca_cliente = '';
      }
      if(isset($_POST[$array4])){
        $sin_cliente = $_POST[$array4];
      }else{
        $sin_cliente = '';
      }

          $str = iconv($charset, 'ASCII//TRANSLIT', $producto[$i]);
          $producto[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));

          $str = iconv($charset, 'ASCII//TRANSLIT', $destino[$i]);
          $destino[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));

          $str = iconv($charset, 'ASCII//TRANSLIT', $materia[$i]);
          $materia[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));


      $updateSQL = sprintf("UPDATE productos SET producto = %s, volumen = %s, terminado = %s, materia = %s, destino = %s, marca_propia = %s, marca_cliente = %s, sin_cliente = %s WHERE idproducto = %s",
        GetSQLValueString($producto[$i], "text"),
        GetSQLValueString($volumen[$i], "text"),
        GetSQLValueString($terminado, "text"),
        GetSQLValueString($materia[$i], "text"),
        GetSQLValueString($destino[$i], "text"),
        GetSQLValueString($marca_propia, "text"),
        GetSQLValueString($marca_cliente, "text"),
        GetSQLValueString($sin_cliente, "text"),
        GetSQLValueString($idproducto[$i], "int"));
      $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());


      }
    }

  $mensaje = "Datos Actualizados Correctamente";

}

//****** INICIA ENVIAR COTIZACION *******///
if(isset($_POST['enviar_cotizacion']) && $_POST['enviar_cotizacion'] == "1"){
  $fecha_aceptacion = time();
  $idoc = $_POST['idoc'];
  $estatus_dspp = 0;
  $estatus_publico = '1';

  $rutaArchivo = "../../archivos/ocArchivos/cotizaciones/";
  $procedimiento = $_POST['procedimiento'];

  if(!empty($_FILES['cotizacion_opp']['name'])){
      $_FILES["cotizacion_opp"]["name"];
        move_uploaded_file($_FILES["cotizacion_opp"]["tmp_name"], $rutaArchivo.$fecha."_".$_FILES["cotizacion_opp"]["name"]);
        $cotizacion_opp = $rutaArchivo.basename($fecha."_".$_FILES["cotizacion_opp"]["name"]);
  }else{
    $cotizacion_opp = NULL;
  }

  //ACTUALIZAMOS LA SOLICITUD DE CERTIFICACION AGREGANDO LA COTIZACIÓN
  if($idoc == 15 || $idoc == 19){
    $estatus_dspp = 5; // COTIZACIÓN ACEPTADA
    $updateSQL = sprintf("UPDATE solicitud_certificacion SET tipo_procedimiento = %s, cotizacion_opp = %s, fecha_aceptacion = %s, estatus_dspp = %s WHERE idsolicitud_certificacion = %s",
      GetSQLValueString($procedimiento, "text"),
      GetSQLValueString($cotizacion_opp, "text"),
      GetSQLValueString($fecha_aceptacion, "int"),
      GetSQLValueString($estatus_dspp, "int"),
      GetSQLValueString($idsolicitud_certificacion, "int"));
    $actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());

  }else{
    $estatus_dspp = 4; // COTIZACIÓN ENVIADA
    $updateSQL = sprintf("UPDATE solicitud_certificacion SET tipo_procedimiento = %s, cotizacion_opp = %s, estatus_dspp = %s WHERE idsolicitud_certificacion = %s",
      GetSQLValueString($procedimiento, "text"),
      GetSQLValueString($cotizacion_opp, "text"),
      GetSQLValueString($estatus_dspp, "int"),
      GetSQLValueString($idsolicitud_certificacion, "int"));
    $actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());
  }


  // ACTUALIZAMOS EL ESTATUS_DSPP DEL OPP
  $updateSQL = sprintf("UPDATE opp SET estatus_dspp = %s WHERE idopp = %s",
    GetSQLValueString($estatus_dspp, "int"),
    GetSQLValueString($_POST['idopp'], "int"));
  $actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());

  //AGREGAMOS EL PROCESO DE CERTIFICACIÓN
  $insertSQL = sprintf("INSERT INTO proceso_certificacion (idsolicitud_certificacion, estatus_publico, estatus_dspp) VALUES (%s, %s, %s)",
    GetSQLValueString($idsolicitud_certificacion, "int"),
    GetSQLValueString($estatus_publico, "int"),
    GetSQLValueString($estatus_dspp, "int"));
  $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

  //ASUNTO DEL CORREO
  //$row_oc = mysql_query("SELECT * FROM oc WHERE idoc = $_POST[idoc]", $dspp) or die(mysql_error());
  //$oc = mysql_fetch_assoc($row_oc);

  $row_opp = mysql_query("SELECT opp.nombre, opp.spp, opp.password, opp.email, oc.idoc, oc.email1, oc.email2, oc.abreviacion AS 'abreviacion_oc', oc.pais AS 'pais_oc', solicitud_certificacion.contacto1_email, solicitud_certificacion.contacto2_email, solicitud_certificacion.adm1_email FROM opp INNER JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp INNER JOIN oc ON solicitud_certificacion.idoc = oc.idoc WHERE idsolicitud_certificacion = $idsolicitud_certificacion", $dspp) or die(mysql_error());
  $opp_detail = mysql_fetch_assoc($row_opp);

  if($opp_detail['idoc'] != 15 && $opp_detail['idoc'] != 19){
    $asunto = "D-SPP Cotización (Solicitud de Certificación para Organizaciones de Pequeños Productores)";

    $cuerpo_mensaje = '
      <html>
      <head>
        <meta charset="utf-8">
      </head>
      <body>
      
        <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
          <tbody>
            <tr>
              <th rowspan="4" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
              <th scope="col" align="left" width="280" ><strong>Notificación de Cotización / Price Notification</strong></th>
            </tr>
            <tr>
              <td align="left" style="color:#ff738a;">Email Organismo de Certificación / Certification Entity: '.$opp_detail['email1'].'</td>
            </tr>
            <tr>
              <td aling="left" style="text-align:justify">
              <b style="color:red">'.$opp_detail['abreviacion_oc'].'</b> ha enviado la cotización correspondiente a la Solicitud de Certificación para Organizaciones de Pequeños Productores.
              <br><br> Por favor iniciar sesión en el siguiente enlace <a href="http://d-spp.org/">www.d-spp.org/</a> como OPP, para poder acceder a la cotización.

              <br><br>
              <b style="color:red">'.$opp_detail['abreviacion_oc'].'</b> has sent the quotation corresponding to the Certification Application for Small producers organizations.
                <br><br>Please log in to the following link <a href="http://d-spp.org/?OPP">www.d-spp.org/</a> as OPP to access the quotation.

              </td>
            </tr>

            <tr>
              <td colspan="2">
                <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;" border="1" width="650px">
                  <tbody>
                    <tr style="font-size: 12px; text-align:center; background-color:#dff0d8; color:#3c763d;" height="50px;">
                      <td width="130px">Nombre de la organización/Organization name</td>
                      <td width="130px">País / Country</td>
                      <td width="130px">Organismo de Certificación / Certification Entity</td>
                      <td width="130px">Fecha de envío / Shipping Date</td>
                   
                      
                    </tr>
                    <tr style="font-size: 12px; text-align:justify">
                      <td style="padding:10px;">
                        '.$_POST['nombre'].'
                      </td>
                      <td style="padding:10px;">
                        '.$_POST['pais'].'
                      </td>
                      <td style="padding:10px;">
                        '.$opp_detail['abreviacion_oc'].'
                      </td>
                      <td style="padding:10px;">
                      '.date('d/m/Y', $fecha).'
                      </td>
                    </tr>

                  </tbody>
                </table>        
              </td>
            </tr>
                  <tr>
                    <td colspan="2">
                      <span style="color:red">¿Qué es lo de debo realizar ahora?. Debes "Aceptar" o "Rechazar" la cotización</span>
                      <ol>

                        <li>Debes iniciar sesión dentro del sistema <a href="http://d-spp.org/">D-SPP (clic aquí)</a> como Organización de Pequeños Productores(OPP).</li>
                        <li>Tu Usuario: <b style="color:red">'.$opp_detail['spp'].'</b> y Contraseña: <b style="color:red">'.$opp_detail['password'].'</b></li>
                        <li>Dentro de tu cuenta debes seleccionar Solicitudes > Listado Solicitudes.</li>
                        <li>Dentro de la tabla solicitudes debes localizar la columna "Cotización" Y seleccionar el botón Verde (aceptar cotización) ó el botón Rojo (rechazar cotización)</li>
                        <li>En caso de aceptar la cotización debes esperar a que finalice el "Periodo de Objeción"(en caso de que sea la primera vez que solicitas la certificación SPP)</li>
                      </ol>
                    </td>
                  </tr> 
                  <tr>
                    <td coslpan="2">Para cualquier duda o aclaración por favor contactar a: soporte@d-spp.org</td>
                  </tr>
          </tbody>
        </table>

      </body>
      </html>
    ';

    $mail->AddAddress($_POST['email']);
    $mail->AddAddress($_POST['contacto1_email']);
    $mail->AddBCC($spp_global);
    if(!empty($oc['email1'])){
      $mail->AddCC($oc['email1']);
    }
    if(!empty($oc['email2'])){
      $mail->AddCC($oc['email2']);
    }
    //se adjunta la cotización
    $mail->AddAttachment($cotizacion_opp);

    //$mail->Username = "soporte@d-spp.org";
    //$mail->Password = "/aung5l6tZ";
    $mail->Subject = utf8_decode($asunto);
    $mail->Body = utf8_decode($cuerpo_mensaje);
    $mail->MsgHTML(utf8_decode($cuerpo_mensaje));
    $mail->Send();
    $mail->ClearAddresses();


    $mensaje = "Se ha enviado la cotizacion al OPP";
  }

}
//****** TERMINA ENVIAR COTIZACION *******///

  ////INICIA INGRESAR LAS OBSERVACIONES REALIZADAS
if(isset($_POST['agregar_observaciones']) && $_POST['agregar_observaciones'] == 1){
  $idsolicitud_certificacion = $_POST['idsolicitud_certificacion'];


  $updateSQL = sprintf("UPDATE solicitud_certificacion SET observaciones = %s WHERE idsolicitud_certificacion = %s",
    GetSQLValueString($_POST['observaciones_solicitud'], "text"),
    GetSQLValueString($idsolicitud_certificacion, "int"));
  $actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());

  $row_informacion = mysql_query("SELECT opp.nombre, opp.spp, opp.password, opp.email, oc.email1, oc.email2, oc.abreviacion AS 'abreviacion_oc', solicitud_certificacion.contacto1_email, solicitud_certificacion.contacto2_email, solicitud_certificacion.adm1_email FROM opp INNER JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp INNER JOIN oc ON solicitud_certificacion.idoc = oc.idoc WHERE idsolicitud_certificacion = $idsolicitud_certificacion", $dspp) or die(mysql_error());
  $informacion = mysql_fetch_assoc($row_informacion);
  
  $asunto = "D-SPP | Observaciones Solicitud Certficación SPP";

  $cuerpo_mensaje = '
    <html>
    <head>
      <meta charset="utf-8">
    </head>
    <body>
    
      <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
        <tbody>
          <tr>
            <th rowspan="4" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
            <th scope="col" align="left" width="280" ><strong>Observaciones Realizadas a la Solicitud de Certificación SPP</strong></th>
          </tr>
          <tr>
            <td align="left" style="color:#ff738a;">
              <p>Organismo de Certificación: '.$informacion['abreviacion_oc'].'</p>
              <p>Email Organismo de Certificación / Certification Entity: '.$informacion['email1'].'</p>
            </td>
          </tr>

          <tr>
            <td aling="left" style="text-align:justify">
            A continuación se listan las siguientes observaciones realizadas a su Solicitud de Certificación SPP. Por favor proceda a corregir y/o complementar su solicitud, para poder continuar con el proceso de certificación.
            </td>
          </tr>

          <tr>
            <td colspan="2" style="padding-top:20px;">
            <hr>
              '.$_POST['observaciones_solicitud'].'   
            <hr>  
            </td>
          </tr>
                <tr>
                  <td colspan="2">
                    <span style="color:red">¿Qué es lo de debo realizar ahora?</span>
                    <ol>
                      <li>Debes iniciar sesión dentro del sistema <a href="http://d-spp.org/">D-SPP (clic aquí)</a> como Organización de Pequeños Productores(OPP).</li>
                      <li>Usuario(#SPP): <span style="color:red">'.$informacion['spp'].'</span> y contraseña: <span style="color:red">'.$informacion['password'].'</span> de su cuenta.</li>
                      <li>Dentro de tu cuenta debes seleccionar <span style="color:red">"Solicitudes"</span> > <span style="color:red">Listado Solicitudes</span>.</li>
                      <li>Dentro de la tabla solicitudes debes localizar la columna <span style="color:red">"Acciones"</span> Y seleccionar el botón <span style="color:red">"CONSULTAR"</span>.</li>
                      <li>Al dar clic en "Consultar" podra visualizar su Solicitud de Certificación" la cual puede ser modificada.</li>
                      <li>Una vez realizados los cambios correspondientes debe dar clic en el boton <span style="color:red">"Actualizar Solicitud" al inicio de su Solicitud</span>.</li>
                    </ol>
                  </td>
                </tr> 
                <tr>
                  <td colspan="2">Para cualquier duda o aclaración por favor contactar a: soporte@d-spp.org</td>
                </tr>
        </tbody>
      </table>

    </body>
    </html>
  ';
  if(isset($informacion['contacto1_email'])){
    $mail->AddAddress($informacion['contacto1_email']);
  }
  if(isset($informacion['contacto2_email'])){
    $mail->AddAddress($informacion['contacto2_email']);
  }
  if(isset($informacion['adm1_email'])){
    $mail->AddAddress($informacion['adm1_email']);
  }
  //$mail->Username = "soporte@d-spp.org";
  //$mail->Password = "/aung5l6tZ";
  $mail->Subject = utf8_decode($asunto);
  $mail->Body = utf8_decode($cuerpo_mensaje);
  $mail->MsgHTML(utf8_decode($cuerpo_mensaje));

  if($mail->Send()){
    $mail->ClearAddresses();
    echo "<script>alert('Correo enviado Exitosamente.');location.href ='javascript:history.back()';</script>";
  }else{
    $mail->ClearAddresses();
    echo "<script>alert('Error, no se pudo enviar el correo, por favor contacte al administrador: soporte@d-spp.org');location.href ='javascript:history.back()';</script>";
  }

}
  //// TERMINA INGRESAR OBSERVACIONES

$query = "SELECT solicitud_certificacion.*, opp.nombre, opp.spp AS 'spp_opp', opp.sitio_web, opp.email, opp.telefono, opp.pais, opp.ciudad, opp.razon_social, opp.direccion_oficina, opp.direccion_fiscal, opp.rfc, opp.ruc, oc.abreviacion AS 'abreviacionOC', porcentaje_productoVentas.* FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp INNER JOIN oc ON solicitud_certificacion.idoc = oc.idoc LEFT JOIN porcentaje_productoVentas ON solicitud_certificacion.idsolicitud_certificacion = porcentaje_productoVentas.idsolicitud_certificacion WHERE solicitud_certificacion.idsolicitud_certificacion = $idsolicitud_certificacion";
$ejecutar = mysql_query($query,$dspp) or die(mysql_error());
$solicitud = mysql_fetch_assoc($ejecutar);

?>

<div class="row" style="font-size:12px;">

  <?php 
  if(isset($mensaje)){
  ?>
  <div class="col-md-12 alert alert-success alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <p class="text-center"><strong><?php echo $mensaje; ?></strong></p>
  </div>
  <?php
  }
  ?>

  <form action="" name="" method="POST" enctype="multipart/form-data">
    <fieldset>
      <div class="col-md-12 alert alert-primary" style="padding:7px;">
        <h3 class="text-center">Application for Small Producers´Organization Certification</h3>
      </div>


      <div class="col-md-12 text-center alert alert-success" style="padding:7px;"><b>GENERAL INFORMATION</b></div>

      <div class="col-lg-12 alert alert-info" style="padding:7px;">
        <div class="col-md-12">
          <!--<div class="col-xs-4">
            <b>ENVAR AL OC (selecciona el OC al que deseas enviar la solicitud):</b>
            <input type="text" class="form-control" value="<?php echo $solicitud['abreviacionOC']; ?>" readonly>
          </div>-->
          <div class="col-md-4">
            <b>ADD OBSERVATIONS</b>
            <button type="button" class="btn btn-primary" style="width:100%" data-toggle="modal" data-target="<?php echo "#observaciones".$_GET['IDsolicitud']; ?>">Add Observations</button>
          </div>

          <div class="col-xs-4">
            <b>TYPE OF APPLICATION</b>
            <input type="text" class="form-control" value="<?php echo $solicitud['tipo_solicitud']; ?>"readonly>
            <button type="submit" class="btn btn-warning form-control" style="color:white" name="guardar_cambios" value="1">
              <span class="glyphicon glyphicon-refresh" aria-hidden="true"></span>Update Application
            </button>
            <!--<input type="submit" style="color:white" class="btn btn-warning form-control" value="Actualizar Solicitud">
            <input type="hidden" name="guarda_cambios" value="1">-->

          </div>
          <div class="col-md-4">
            <?php 
            if(empty($solicitud['cotizacion_opp'])){
            ?>
              <b>LOAD QUOTATION</b>
              <input type="file" class="form-control" id="cotizacion_opp" name="cotizacion_opp"> 
              <input type="hidden" name="idoc" value="<?php echo $solicitud['idoc']; ?>"> 
              <button class="btn btn-sm btn-success form-control" style="color:white" id="enviar_cotizacion" name="enviar_cotizacion" type="submit" value="1" onclick="return validar()">
                <span class="glyphicon glyphicon-envelope" aria-hidden="true"></span> Send Quotation
              </button>
              <!--<button type="submit" class="btn btn-success form-control" style="color:white" name="enviar_cotizacion" value="Enviar"><span class="glyphicon glyphicon-envelope" aria-hidden="true" onclick="return validar()"></span> Enviar Cotización</button>-->

            <?php 
            }else{
              echo "<b style='padding:10px;background:#e74c3c;color:#fff;font-size:14px;'>Quotation sent</b>";
            }
             ?>
          </div>

        </div>
      </div>
      <div class="col-xs-12 text-center">
        <div class="row">
      <h4>Certification Procedure <br><small>(by Certification Entity)</small></h4>
        </div>
      </div>
      <div class="col-xs-3 text-center">
        <div class="row">
          <div class="col-xs-12">
            <p style="font-size:10px;"><b>"SHORTENED" DOCUMENT-BASED</b></p> 
          </div>       
          <div class="col-xs-12">
            <input type="radio" data-on-color="success" data-off-color="danger" data-size="small" name="procedimiento" value='DOCUMENTAL "ACORTADO"' <?php if($solicitud['tipo_procedimiento'] == 'DOCUMENTAL "ACORTADO"'){ echo "checked"; } ?>>

          </div>                        
        </div>
      </div>
      <div class="col-xs-3 text-center">
        <div class="row">
          <div class="col-xs-12">
            <p style="font-size:10px;"><b>"NORMAL" DOCUMENT-BASED</b></p> 
          </div>
          <div class="col-xs-12">
            <input type="radio" data-on-color="success" data-off-color="danger" data-size="small" name="procedimiento" value='DOCUMENTAL "NORMAL"' <?php if($solicitud['tipo_procedimiento'] == 'DOCUMENTAL "NORMAL"'){ echo "checked"; } ?>>

          </div>                
        </div>
      </div>
      <div class="col-xs-3 text-center">
        <div class="row">
          <div class="col-xs-12">
            <p style="font-size:10px;"><b>COMPLETE "ON SITE"</b></p>  
          </div>
          <div class="col-xs-12">
            <input type="radio" data-on-color="success" data-off-color="danger" data-size="small" name="procedimiento" value='COMPLETO "IN SITU"' <?php if($solicitud['tipo_procedimiento'] == 'COMPLETO "IN SITU"'){ echo "checked"; } ?>>

          </div>                
        </div>
      </div>
      <div class="col-xs-3 text-center">
        <div class="row">
          <div class="col-xs-12">
            <p style="font-size:10px;"><b>COMPLETE "REMOTE"</b></p>  
          </div>
          <div class="col-xs-12">
            <input type="radio" data-on-color="success" data-off-color="danger" data-size="small" name="procedimiento" value='COMPLETO "A DISTANCIA"' <?php if($solicitud['tipo_procedimiento'] == 'COMPLETO "A DISTANCIA"'){ echo "checked"; } ?>>

          </div>                
        </div>
      </div> 

      <!------ INICIA INFORMACION GENERAL Y DATOS FISCALES ------>
      <div class="col-lg-12">
        <div class="col-md-6">
          <div class="col-md-12 text-center alert alert-warning" style="padding:7px;">GENERAL INFORMATION</div>
          <label for="fecha_elaboracion">DATE OF ELABORATION</label>
          <input type="text" class="form-control" id="fecha_elaboracion" name="fecha_elaboracion" value="<?php echo date('Y-m-d', time()); ?>" readonly>  

          <label for="spp">SPP IDENTIFICATION CODE(#SPP): </label>
          <input type="text" class="form-control" id="spp" name="spp" value="<?php echo $solicitud['spp_opp']; ?>" readonly>

          <label for="nombre">NAME OF SMALL PRODUCER ORGANIZATION: </label>
          <textarea name="nombre" id="nombre" class="form-control" readonly><?php echo $solicitud['nombre']; ?></textarea>

          <label for="pais">COUNTRY:</label>
          <input type="text" class="form-control" id="pais" name="pais" value="<?php echo $solicitud['pais']; ?>" readonly>

          <label for="direccion_fisica">COMPLETE ADDRESS FOR ORGANIZATION’S LOCATION (STREET, DISTRICT, TOWN/CITY, REGION):</label>
          <textarea name="direccion_fisica" id="direccion_fisica"  class="form-control" readonly><?php echo $solicitud['direccion_oficina']; ?></textarea>

          <label for="email">EMAIL:</label>
          <input type="text" class="form-control" id="email" name="email" value="<?php echo $solicitud['email']; ?>" readonly>

          <label for="email">ORGANIZATION’S TELEPHONES(COUNTRY CODE+AREA CODE+NUMBER)</label>
          <input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo $solicitud['telefono']; ?>" readonly>  

          <label for="sitio_web">Website:</label>
          <input type="text" class="form-control" id="sitio_web" name="sitio_web" value="<?php echo $solicitud['sitio_web']; ?>" readonly>

        </div>

        <div class="col-md-6">
          <div class="col-md-12 text-center alert alert-warning" style="padding:7px;">FISCAL DATA</div>

          <label for="razon_social">BUSINESS NAME</label>
          <input type="text" class="form-control" id="razon_social" name="razon_social" value="<?php echo $solicitud['razon_social']; ?>" readonly>

          <label for="direccion_fiscal">FISCAL ADDRESS</label>
          <textarea class="form-control" name="direccion_fiscal" id="direccion_fiscal" readonly><?php echo $solicitud['direccion_fiscal']; ?></textarea>

          <label for="rfc">RFC</label>
          <input type="text" class="form-control" id="rfc" name="rfc" value="<?php echo $solicitud['rfc']; ?>" readonly>

          <label for="ruc">RUC</label>
          <input type="text" class="form-control" id="ruc" name="ruc" value="<?php echo $solicitud['ruc']; ?>" readonly>
        </div>
      </div>
      <!------ INICIA INFORMACION GENERAL Y DATOS FISCALES ------>


      <!------ INICIA INFORMACION CONTACTOS Y AREA ADMINISTRATIVA ------>
      <div class="col-lg-12">
        <div class="col-md-6">
          <div class="col-md-12 text-center alert alert-warning" style="padding:7px;">CONTACT PERSON(S) OF APPLICATION</div>

          <label for="persona1">CONTACT PERSON(S)</label>
          <input type="text" class="form-control" id="persona1" value="<?php echo $solicitud['contacto1_nombre']; ?>"  readonly>
          <input type="text" class="form-control" id="" value="<?php echo $solicitud['contacto2_nombre']; ?>" placeholder="Person Name 2" readonly> 

          <label for="cargo">POSITION(S)</label>
          <input type="text" class="form-control" id="cargo" value="<?php echo $solicitud['contacto1_cargo']; ?>" placeholder="* Position Persona 1" readonly>
          <input type="text" class="form-control" id="" value="<?php echo $solicitud['contacto2_cargo']; ?>" palceholder="Position Persona 2" readonly>

          <label for="email">EMAIL</label>
          <input type="email" class="form-control" name="contacto1_email" id="email" value="<?php echo $solicitud['contacto1_email']; ?>" placeholder="* Email Persona 1" readonly>
          <input type="email" class="form-control" name="contacto2_email" id="" value="<?php echo $solicitud['contacto2_email']; ?>" placeholder="Email Persona 2" readonly>

          <label for="telefono">TELEPHONE(S) FOR CONTAC PERSON(S):</label>
          <input type="text" class="form-control" id="telefono" value="<?php echo $solicitud['contacto1_telefono']; ?>" placeholder="* Telephone Person 1" readonly>
          <input type="text" class="form-control" id="" value="<?php echo $solicitud['contacto2_telefono']; ?>" placeholder="Telephone Person 2" readonly>

        </div>

        <div class="col-md-6">
          <div class="col-md-12 text-center alert alert-warning" style="padding:7px;">PERSON(S) OF THE ADMINISTRATIVE AREA:</div>

          <label for="persona_adm">PERSON(S) OF THE ADMINISTRATIVE AREA</label>
          <input type="text" class="form-control" id="persona_adm" value="<?php echo $solicitud['adm1_nombre']; ?>" placeholder="Person Name 1" readonly>
          <input type="text" class="form-control" id="" value="<?php echo $solicitud['adm2_nombre']; ?>" placeholder="Person Name 2" readonly>

          <label for="email_adm">EMAIL</label>
          <input type="email" class="form-control" id="email_adm" value="<?php echo $solicitud['adm1_email']; ?>" placeholder="Email Persona 1" readonly>
          <input type="email" class="form-control" id="" value="<?php echo $solicitud['adm2_email']; ?>" placeholder="Email Persona 2" readonly>

          <label for="telefono_adm">TELEPHONE</label>
          <input type="text" class="form-control" id="telefono_adm" value="<?php echo $solicitud['adm1_telefono']; ?>" placeholder="Telephone Person 1" readonly>
          <input type="text" class="form-control" id="" value="<?php echo $solicitud['adm2_telefono']; ?>" placeholder="Telephone Person 2" readonly>
        </div>
      </div>
      <!------ FIN INFORMACION CONTACTOS Y AREA ADMINISTRATIVA ------>



      <!------ INICIA INFORMACION DATOS DE OPERACIÓN ------>


      <div class="col-lg-12">
        <div class="col-md-12">
          <label for="resp1">NUMBER OF PRODUCERS MEMBERS:</label>
          <input type="number" step="any" class="form-control" id="resp1" name="resp1" value="<?php echo $solicitud['resp1']; ?>" >

          <label for="resp2">NUMBER OF PRODUCERS MEMBERS OF THE  PRODUCT (S) TO BE INCLUDED IN THE CERTIFICATION:</label>
          <input type="text" class="form-control" id="resp2" name="resp2" value="<?php echo $solicitud['resp2']; ?>" >

          <label for="resp3">TOTAL PRODUCTION VOLUME(S) BY PRODUCT (UNITE OF MEASURE):</label>
          <input type="text" class="form-control" id="resp3" name="resp3" value="<?php echo $solicitud['resp3']; ?>" >
          
          <label for="resp4">MAXIMUM SIZE OF THE UNIT OF PRODUCTION BY THE PRODUCER OF THE PRODUCT(S) TO INCLUDE IN THE CERTIFICATION:</label>
          <input type="text" class="form-control" id="resp4" name="resp4" value="<?php echo $solicitud['resp4']; ?>" >



        </div>
      </div>

      <div class="col-md-12 text-center alert alert-success" style="padding:7px;">INFORMATION ON OPERATIONS</div>

      <div class="col-lg-12">
        <div class="col-md-12">
          <label for="op_preg1">
            1. EXPLAIN IF THE SMALL PRODUCERS’ ORGANIZATION (SPO) IS AT THE 1st, 2nd, 3rd or 4th LEVEL, AS WELL AS EXPLAIN THE NUMBER OF ORGANIZATIONS OF THE 3rd,2nd or 1st LEVEL, AND THE NUMBER OF COMMUNITIES, AREAS OR GROUPS OF WORK, IN HIS OR HER CASE, THAT ACCOUNT:
          </label>
          <input type="text" class="form-control" id="op_preg1" name="op_preg1" value="<?php echo $solicitud['op_preg1']; ?>" >

          <div class="col-xs-3">
            <label for="preg1_1">
              1.1 NUMBER OF SPO 3rd  LEVEL:
            </label>
            <input type="text" class="form-control" id="preg1_1" name="preg1_1" value="<?php echo $solicitud['preg1_1']; ?>" >
          </div>
          <div class="col-xs-3">
            <label for="preg1_2">
              1.2 NUMBER OF SPO 2nd  LEVEL::
            </label>
            <input type="text" class="form-control" id="preg1_2" name="preg1_2" value="<?php echo $solicitud['preg1_2']; ?>" >
          </div>
          <div class="col-xs-3">
            <label for="preg1_3">
              1.3 NUMBER OF SPO 1st  LEVEL:
            </label>
            <input type="text" class="form-control" id="preg1_3" name="preg1_3" value="<?php echo $solicitud['preg1_3']; ?>" >
          </div>
          <div class="col-xs-3">
            <label for="preg1_4">
              1.4 NUMBER OF COMMUNITIES, AREAS OR GROUPS OF WORK:
            </label>
            <input type="text" class="form-control" id="preg1_4" name="preg1_4" value="<?php echo $solicitud['preg1_4']; ?>" >
          </div>

          <label for="op_preg2">
            2.  SPECIFY WHICH PRODUCT (S) YOU WANT TO INCLUDE IN THE CERTIFICATE OF THE SYMBOL OF SMALL PRODUCERS FOR WHICH THE CERTIFICATION ENTITY WILL CONDUCT THE ASSESSMENT.
          </label>
          <textarea name="op_preg2" id="op_preg2" class="form-control"><?php echo $solicitud['op_preg2']; ?></textarea>

          <label for="op_preg3">
            3.  MENTION IF YOUR ORGANIZATION WOULD LIKE TO INCLUDE SOME ADDITIONAL DESCRIPTOR FOR COMPLEMENTARY USE WITH THE GRAPHIC DESIGN OF THE SMALL PRODUCERS’ SYMBOL.<sup>4</sup>
          </label>
          <input type="text" class="form-control" id="op_preg3" name="op_preg3" value="<?php echo $solicitud['op_preg3']; ?>">

          <div >
            <label for="alcance_opp">
              4. SELECT THE SCOPE OF THE SMALL PRODUCERS’ ORGANIZATION:
            </label>
          </div>
          
          <div class="col-xs-4">
            <label>PRODUCTION</label>
            <input type="checkbox" name="produccion" class="form-control" value="1" <?php if($solicitud['produccion']){ echo 'checked';} ?>>
          </div>
          <div class="col-xs-4">
            <label>PROCESSING</label>
            <input type="checkbox" name="procesamiento" class="form-control" value="1" <?php if($solicitud['procesamiento']){ echo 'checked';} ?>>
          </div>
          <div class="col-xs-4">
            <label>TRADING</label>
            <input type="checkbox" name="exportacion" class="form-control" value="1" <?php if($solicitud['exportacion']){ echo 'checked';} ?>>
          </div>

          <label for="op_preg5">
            5.  SPECIFY IF YOU SUBCONTRACT THE SERVICES OF PROCESSING PLANTS, TRADING COMPANIES OR COMPANIES THAT CARRY OUT THE IMPORT OR EXPORT, IF THE ANSWER IS AFFIRMATIVE, MENTION THE NAME AND THE SERVICE THAT PERFORMS.
          </label>
          <textarea name="op_preg5" id="op_preg5" class="form-control"><?php echo $solicitud['op_preg5']; ?></textarea>

          <label for="op_preg6">
            6.  IF YOU SUBCONTRACT THE SERVICES OF PROCESSING PLANTS, TRADING COMPANIES OR COMPANIES THAT CARRY OUT THE IMPORT OR EXPORT, INDICATE WHETHER THESE COMPANIES ARE GOING TO APPLY FOR THE REGISTRATION UNDER SPP CERTIFICATION PROGRAM. <sup>5</sup>
            <br>
            <small><sup>5</sup> Review the General Application Guidelines to the SPP System.</small>
          </label>
          <textarea name="op_preg6" id="op_preg6" class="form-control"><?php echo $solicitud['op_preg6']; ?></textarea>

          <label for="op_preg7">
            7.  IN ADDITION TO YOUR MAIN OFFICES, PLEASE SPECIFY HOW MANY COLLECTION CENTERS, PROCESSING AREAS AND ADDITIONAL OFFICES YOU HAVE.
          </label>
          <textarea name="op_preg7" id="op_preg7" class="form-control"><?php echo $solicitud['op_preg7']; ?></textarea>

          <label for="op_preg8">
            8.  IF THE ORGANIZATION HAS AN INTERNAL CONTROL SYSTEM FOR COMPLYING WITH THE CRITERIA IN THE GENERAL STANDARD OF THE SMALL PRODUCERS’ SYMBOL, PLEASE EXPLAIN HOW IT WORKS.
          </label>
          <textarea name="op_preg8" id="op_preg8" class="form-control"><?php echo $solicitud['op_preg8']; ?></textarea>
          <p class="alert alert-info">9.  FILL OUT THE TABLE ACCORDING YOUR CERTIFICATIONS, (example: EU, NOP, JASS, FLO, etc)</p>

          <table class="table table-bordered" id="tablaCertificaciones">
            <tr>
              <td>CERTIFICATION</td>
              <td>CERTIFICATION ENTITY</td>
              <td>INITIAL YEAR OF CERTIFICATION</td>
              <td>HAS BEEN INTERRUPTED?</td>
            </tr>
            <?php 
            $query_certificacion_detalle = "SELECT * FROM certificaciones WHERE idsolicitud_certificacion = $idsolicitud_certificacion";
            $certificacion_detalle = mysql_query($query_certificacion_detalle, $dspp) or die(mysql_error());
            $contador = 0;
            while($row_certificacion = mysql_fetch_assoc($certificacion_detalle)){
            ?>
              <tr class="text-center">
                <td><input type="text" class="form-control" name="certificacion[]" id="exampleInputEmail1" placeholder="CERTIFICATION" value="<?echo $row_certificacion['certificacion']?>"></td>
                <td><input type="text" class="form-control" name="certificadora[]" id="exampleInputEmail1" placeholder="CERTIFICATION ENTITY" value="<?echo $row_certificacion['certificadora']?>"></td>
                <td><input type="text" class="form-control" name="ano_inicial[]" id="exampleInputEmail1" placeholder="INITIAL YEAR" value="<?echo $row_certificacion['ano_inicial']?>"></td>
                <td><input type="text" class="form-control" name="interrumpida[]" id="exampleInputEmail1" placeholder="HAS BEEN INTERRUPTED?" value="<?echo $row_certificacion['interrumpida']?>"></td>
                <input type="hidden" name="idcertificacion[]" value="<?echo $row_certificacion['idcertificacion']?>">
              </tr>
            <?php 
              $contador++; 
            } 
            ?> 
          </table>  

          <label for="op_preg10">
            10. ACCORDING THE CERTIFICATIONS, IN ITS MOST RECENT INTERNAL AND EXTERNAL EVALUATIONS, HOW MANY CASES OF NON COMPLIANCE WERE IDENTIFIED? PLEASE EXPLAIN IF THEY HAVE BEEN RESOLVED OR WHAT THEIR STATUS IS?</label>
          <textarea name="op_preg10" id="op_preg10" class="form-control"><?php echo $solicitud['op_preg10']; ?></textarea>

          <p for="op_preg11">
            <b>11.  OF THE APPLICANT’S TOTAL TRADING DURING THE PREVIOUS CYCLE, WHAT PERCENTAGE WAS CONDUCTED UNDER THE SCHEMES OF CERTIFICATION FOR ORGANIC, FAIR TRADE AND/OR THE SMALL PRODUCERS’ SYMBOL?</b>
            <i>(* Enter only quantity, integer or decimal)</i>
            <div class="col-lg-12">
              <div class="row">
                <div class="col-xs-3">
                  <label for="organico">% ORGANIC</label>
                  <input type="number" step="any" class="form-control" id="organico" name="organico" value="<?php echo $solicitud['organico']; ?>" placeholder="Ej: 0.0" readonly>
                </div>
                <div class="col-xs-3">
                  <label for="comercio_justo">% FAIR TRADE</label>
                  <input type="number" step="any" class="form-control" id="comercio_justo" name="comercio_justo" value="<?php echo $solicitud['comercio_justo']; ?>" placeholder="Ej: 0.0" readonly>
                </div>
                <div class="col-xs-3">
                  <label for="spp">SMALL PRODUCERS´SYMBOL</label>
                  <input type="number" step="any" class="form-control" id="spp" name="spp" value="<?php echo $solicitud['spp']; ?>" placeholder="Ej: 0.0" readonly>
                </div>
                <div class="col-xs-3">
                  <label for="otro">WITHOUT CERTIFICATE</label>
                  <input type="number" step="any" class="form-control" id="otro" name="sin_certificado" value="<?php echo $solicitud['sin_certificado']; ?>" placeholder="Ej: 0.0" readonly> 
                </div>
              </div>
            </div>
          </p>

          <p><b>12. DID YOU HAVE SPP PURCHASES DURING THE PREVIOUS CERTIFICATION CYCLE?</b></p>
          <div class="col-xs-12 ">
                <?php
                  if($solicitud['op_preg12'] == 'SI'){
                      //echo "SI <input type='radio' name='op_preg12'  checked readonly>";
                    /*echo "</div>";
                    echo "<div class='col-xs-6'>";
                      echo "<p class='text-center alert alert-danger'>NO</p>";
                      echo "NO <input type='radio' name='op_preg12'  readonly>";
                    echo "</div>";*/
                ?>
                  <div class="col-xs-6">
                    <p class='text-center alert alert-success'><span class='glyphicon glyphicon-ok' aria-hidden='true'></span> SI</p>
                  </div>
                  <div class="col-xs-6">
                    <?php 
                      if(empty($solicitud['op_preg13'])){
                     ?>
                      <p class="alert alert-danger">No response was provided.</p>
                    <?php 
                      }else if($solicitud['op_preg13'] == "HASTA $3,000 USD"){
                     ?>
                      <p class="alert alert-info">LESS THAN $3,000 USD</p>
                    <?php 
                      }else if($solicitud['op_preg13'] == "ENTRE $3,000 Y $10,000 USD"){
                     ?>
                     <p class="alert alert-info">BETWEENN $3,000 AND $10,000 USD</p>
                    <?php 
                      }else if($solicitud['op_preg13'] == "ENTRE $10,000 A $25,000 USD"){
                     ?>
                     <p class="alert alert-info">BEETWENN $10,000 AND $25,000 USD</p>
                    <?php 
                      }else if($solicitud['op_preg13'] != "HASTA $3,000 USD" && $solicitud['op_preg13'] != "ENTRE $3,000 Y $10,000 USD" && $solicitud['op_preg13'] != "ENTRE $10,000 A $25,000 USD"){
                     ?>
                     <p class="alert alert-info"><?php echo $solicitud['op_preg13']; ?></p>
                     
                    <?php 
                      }
                     ?>
                  </div>
                <?php
                  }else if($solicitud['op_preg12'] == 'NO'){
                ?>
                  <div class="col-xs-12">
                    <p class='text-center alert alert-danger'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span> NO</p>
                  </div>
                
                <?php         
                  }
                ?>
          </div>
              
          <label for="op_preg14">
            14. ESTIMATED DATE FOR BEGINNING TO USE THE SMALL PRODUCERS’ SYMBOL:
          </label>
          <input type="text" class="form-control" id="op_preg14" name="op_preg14" value="<?php echo $solicitud['op_preg14']; ?>">

          <p>
            <b>15.  PLEASE ATTACH A GENERAL MAP OF THE AREA WHERE YOUR SPO OPERATES, INDICATING THE ZONES WHERE MEMBERS ARE LOCATED.</b>
          </p>
          <?php 
          if(empty($solicitud['op_preg15'])){
            echo "<p class='alert alert-danger' style='padding:7px;'>No Disponible</p>";
          }else{
          ?>
            <a class="btn btn-success" href="<?echo $solicitud['op_preg15']?>" target="_blank"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Download the sketch</a>  
          <?php
          }
           ?>
          
        </div>
      </div>

      <!------ FIN INFORMACION DATOS DE OPERACIÓN ------>

      <div class="col-md-12 text-center alert alert-success" style="padding:7px;">INFORMATION ON PRODUCTS FOR WHICH APPLICANT WISHES TO USE SYMBOL<sup>6</sup></div>
      <div class="col-lg-12">
        <table class="table table-bordered" id="tablaProductos">
          <tr>
            <td>Product</td>
            <td>Total Estimated Volume to be Traded</td>
            <td>Finished Product</td>
            <td>Raw material</td>
            <td>Destination Countries</td>
            <td>Own brand</td>
            <td>Client’s brand</td>
            <td>Still without client</td>          
          </tr>
          <?php 
          $query_producto_detalle = "SELECT * FROM productos WHERE idsolicitud_certificacion = $idsolicitud_certificacion";
          $producto_detalle = mysql_query($query_producto_detalle, $dspp) or die(mysql_error());
          $contador = 0;
          while($row_producto = mysql_fetch_assoc($producto_detalle)){
          ?>
            <tr>
              <td>
                <input type="text" class="form-control" name="producto[]" id="exampleInputEmail1" placeholder="Product" value="<?echo $row_producto['producto']?>">
              </td>
              <td>
                <input type="text" class="form-control" name="volumen[]" id="exampleInputEmail1" placeholder="Volume" value="<?echo $row_producto['volumen']?>">
              </td>
              <td>
                <?php 
                  if($row_producto['terminado'] == 'SI'){
                    echo "SI <input type='radio'  name='terminado".$contador."' value='SI' checked><br>";
                  }else{
                    echo "SI <input type='radio'  name='terminado".$contador."' value='SI'><br>";
                  } 
                  if($row_producto['terminado'] == 'NO'){
                    echo "NO <input type='radio'  name='terminado".$contador."' value='NO' checked>";
                  }else{
                    echo "NO <input type='radio'  name='terminado".$contador."' value='NO'>";
                  }
                 ?>
              </td>          
              <td>
                <input type="text" class="form-control" name="materia[]" id="exampleInputEmail1" placeholder="Material" value="<?echo $row_producto['materia']?>">
              </td>
              <td>
                <input type="text" class="form-control" name="destino[]" id="exampleInputEmail1" placeholder="Destination" value="<?echo $row_producto['destino']?>">
              </td>
              <td>
                <?php 
                  if($row_producto['marca_propia'] == 'SI'){
                    echo "YES <input type='radio'  name='marca_propia".$contador."' value='SI' checked><br>";
                  }else{
                    echo "YES <input type='radio'  name='marca_propia".$contador."' value='SI'><br>";
                  } 
                  if($row_producto['marca_propia'] == 'NO'){
                    echo "NO <input type='radio'  name='marca_propia".$contador."' value='NO' checked>";
                  }else{
                    echo "NO <input type='radio'  name='marca_propia".$contador."' value='NO'>";
                  }
                 ?>
              </td>
              <td>
                <?php 
                  if($row_producto['marca_cliente'] == 'SI'){
                    echo "YES <input type='radio'  name='marca_cliente".$contador."' value='SI' checked><br>";
                  }else{
                    echo "YES <input type='radio'  name='marca_cliente".$contador."' value='SI'><br>";
                  } 
                  if($row_producto['marca_cliente'] == 'NO'){
                    echo "NO <input type='radio'  name='marca_cliente".$contador."' value='NO' checked>";
                  }else{
                    echo "NO <input type='radio'  name='marca_cliente".$contador."' value='NO'>";                  
                  }
                 ?>              
              </td>
              <td>
                <?php 
                  if($row_producto['sin_cliente'] == 'SI'){
                    echo "YES <input type='radio'  name='sin_cliente".$contador."' value='SI' checked><br>";
                  }else{
                    echo "YES <input type='radio'  name='sin_cliente".$contador."' value='SI'><br>";
                  }
                  if($row_producto['sin_cliente'] == 'NO'){
                    echo "NO <input type='radio'  name='sin_cliente".$contador."' value='NO' checked>";
                  }else{
                    echo "NO <input type='radio'  name='sin_cliente".$contador."' value='NO'>";
                  }
                 ?> 
              </td>
                <input type="hidden" name="idproducto[]" value="<?echo $row_producto['idproducto']?>">                     
            </tr>
          <?php 
          $contador++;
          }
          ?>        
          <tr>
            <td colspan="8">
              <h6><sup>6</sup> Information provided in this section will be handled with complete confidentiality. Please insert additional lines if necessary.</h6>
            </td>
          </tr>
        </table>
      </div>

      <div class="col-lg-12 text-center alert alert-success" style="padding:7px;">
        <b>COMMITMENTS</b>
      </div>
      <div class="col-lg-12 text-justify">
        <p>1. By sending in this document, the applicant expresses its interest in receiving a proposal for certification with the Small Producers’ Symbol.</p>
        <p>2. The certification process will begin when it is confirmed that the payment corresponding to the proposal has been received.</p>
        <p>3. The fact that this application is delivered and received does not guarantee that the results of the certification process will be positive</p>
        <p>4. The applicant will become familiar with and comply with all the applicable requirements in the General Standard of the Small Producers’ Symbol for a Small Producers’ Organization, including both Critical and Minimum Criteria, and independently of the type of evaluation conducted. </p>
      </div>
      <div class="col-lg-12">
        <label for="responsable">
          <p style="font-size:14px;"><strong>Name of the person who is responsible for the accuracy of the information on this form, and who, on behalf of the Applicant, will follow up on the application</strong></p>
        </label>
        <input type="text" class="form-control" id="responsable" value="<?php echo $solicitud['responsable']; ?>" > 
        <input type="hidden" name="fecha_registro" value="<?php echo $solicitud['fecha_registro'] ?>">
        <input type="hidden" name="idopp" value="<?php echo $solicitud['idopp']; ?>">

        <p>
          <b>Certification Entity who receives the application:</b>
        </p>
        <p class="alert alert-info" style="padding:7px;">
          <strong><?php echo $solicitud['abreviacionOC']; ?></strong>
        </p>  
      </div>
      <!--<div class="col-xs-12">
        <hr>
        <input type="text" name="insertar_solicitud" value="1">
        <input type="submit" class="btn btn-primary form-control" value="Enviar Solicitud" onclick="return validar()">
      </div>-->

    </fieldset>
  </form>
</div>
<!-- inicia modal estatus_Certificado -->

<div id="<?php echo "observaciones".$_GET['IDsolicitud']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form action="" method="POST">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title" id="myModalLabel">Add Observations on the Application</h4>
        </div>
        <div class="modal-body">
          <textarea name="observaciones_solicitud" id="" class="textareaMensaje" cols="30" rows="10"></textarea>
        </div>

        <div class="modal-footer">
          <input type="hidden" name="tipo_solicitud" value="<?php echo $solicitud['tipo_solicitud']; ?>">
          <input type="hidden" name="idsolicitud_certificacion" value="<?php echo $_GET['IDsolicitud']; ?>">
          <input type="hidden" name="agregar_observaciones" value="1">
          <button type="submit" class="btn btn-success">Send Observations</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- termina modal estatus_Certificado -->

<script>
  
  function validar(){
    valor = document.getElementById("cotizacion_opp").value;
    if( valor == null || valor.length == 0 ) {
      alert("No se ha cargado la cotización de el OPP");
      return false;
    }
    
    Procedimiento = document.getElementsByName("procedimiento");
     
    var seleccionado = false;
    for(var i=0; i<Procedimiento.length; i++) {    
      if(Procedimiento[i].checked) {
        seleccionado = true;
        break;
      }
    }
     
    if(!seleccionado) {
      alert("Debes de seleecionar un Procedimiento de Certificación");
      return false;
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

    cell1.innerHTML = '<input type="text" class="form-control" name="certificadora['+contador+']" id="exampleInputEmail1" placeholder="CERTIFICATION">';
    cell2.innerHTML = '<input type="text" class="form-control" name="certificacion['+contador+']" id="exampleInputEmail1" placeholder="CERTIFICATION ENTITY">';
    cell3.innerHTML = '<input type="text" class="form-control" name="ano_inicial['+contador+']" id="exampleInputEmail1" placeholder="INITIAL YEAR">';
    cell4.innerHTML = '<div class="col-xs-6">SI<input type="radio" class="form-control" name="interrumpida['+contador+']" value="SI"></div><div class="col-xs-6">NO<input type="radio" class="form-control" name="interrumpida['+contador+']" value="NO"></div>';
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
    var cell7 = row.insertCell(6); 
    var cell8 = row.insertCell(7);        

    

    cell1.innerHTML = '<input type="text" class="form-control" name="producto['+cont+']" id="exampleInputEmail1" placeholder="Product">';
    
    cell2.innerHTML = '<input type="text" class="form-control" name="volumen['+cont+']" id="exampleInputEmail1" placeholder="Volume">';
    
    cell3.innerHTML = 'YES <input type="radio" name="terminado'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="terminado'+cont+'['+cont+']" id="" value="NO">';
    
    cell4.innerHTML = '<input type="text" class="form-control" name="materia['+cont+']" id="exampleInputEmail1" placeholder="Material">';
    
    cell5.innerHTML = '<input type="text" class="form-control" name="destino['+cont+']" id="exampleInputEmail1" placeholder="Destination">';
    
    cell6.innerHTML = 'YES <input type="radio" name="marca_propia'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="marca_propia'+cont+'['+cont+']" id="" value="NO">';
    
    cell7.innerHTML = 'YES <input type="radio" name="marca_cliente'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="marca_cliente'+cont+'['+cont+']" id="" value="NO">';
    
    cell8.innerHTML = 'YES <input type="radio" name="sin_cliente'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="sin_cliente'+cont+'['+cont+']" id="" value="NO">';   

    }

  } 

</script>