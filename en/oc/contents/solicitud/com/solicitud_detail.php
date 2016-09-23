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
mysql_select_db($database_dspp, $dspp);

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




$colname_contacto_detail = "-1";
if (isset($_GET['idcontacto'])) {
  $colname_contacto_detail = $_GET['idcontacto'];
}
mysql_select_db($database_dspp, $dspp);
$query_contacto_detail = sprintf("SELECT * FROM contacto WHERE idcontacto = %s", GetSQLValueString($colname_contacto_detail, "int"));
$contacto_detail = mysql_query($query_contacto_detail, $dspp) or die(mysql_error());
$row_contacto_detail = mysql_fetch_assoc($contacto_detail);
$totalRows_contacto_detail = mysql_num_rows($contacto_detail);

$colname_cta_bn_detail = "-1";
if (isset($_GET['idcta_bn'])) {
  $colname_cta_bn_detail = $_GET['idcta_bn'];
}
mysql_select_db($database_dspp, $dspp);
$query_cta_bn_detail = sprintf("SELECT * FROM cta_bn WHERE idcta_bn = %s", GetSQLValueString($colname_cta_bn_detail, "int"));
$cta_bn_detail = mysql_query($query_cta_bn_detail, $dspp) or die(mysql_error());
$row_cta_bn_detail = mysql_fetch_assoc($cta_bn_detail);
$totalRows_cta_bn_detail = mysql_num_rows($cta_bn_detail);

$maxRows_accion_detalle = 20;
$pageNum_accion_detalle = 0;
if (isset($_GET['pageNum_accion_detalle'])) {
  $pageNum_accion_detalle = $_GET['pageNum_accion_detalle'];
}
$startRow_accion_detalle = $pageNum_accion_detalle * $maxRows_accion_detalle;

$colname_accion_detalle = "-1";
if (isset($_GET['idsolicitud'])) {
  $colname_accion_detalle = $_GET['idsolicitud'];
}


###################################################################################################

mysql_select_db($database_dspp, $dspp);
$query_accion_detalle = sprintf("SELECT solicitud_registro.*, oc.idoc, oc.nombre AS 'nombreOC' FROM solicitud_registro INNER JOIN oc ON solicitud_registro.idoc = oc.idoc WHERE idsolicitud_registro = %s", GetSQLValueString($colname_accion_detalle, "int"));
$query_limit_accion_detalle = sprintf("%s LIMIT %d, %d", $query_accion_detalle, $startRow_accion_detalle, $maxRows_accion_detalle);
$accion_detalle = mysql_query($query_limit_accion_detalle, $dspp) or die(mysql_error());

$row_solicitud = mysql_fetch_assoc($accion_detalle);




         

###################################################################################################





if (isset($_GET['totalRows_accion_detalle'])) {
  $totalRows_accion_detalle = $_GET['totalRows_accion_detalle'];
} else {
  $all_accion_detalle = mysql_query($query_accion_detalle);
  $totalRows_accion_detalle = mysql_num_rows($all_accion_detalle);
}
$totalPages_accion_detalle = ceil($totalRows_accion_detalle/$maxRows_accion_detalle)-1;

$colname_accion_detail = "-1";
if (isset($_GET['idultima_accion'])) {
  $colname_accion_detail = $_GET['idultima_accion'];
}
mysql_select_db($database_dspp, $dspp);
$query_accion_detail = sprintf("SELECT * FROM ultima_accion WHERE idultima_accion = %s", GetSQLValueString($colname_accion_detail, "int"));
$accion_detail = mysql_query($query_accion_detail, $dspp) or die(mysql_error());
$row_accion_detail = mysql_fetch_assoc($accion_detail);
$totalRows_accion_detail = mysql_num_rows($accion_detail);

mysql_select_db($database_dspp, $dspp);
$query_accion_lateral = "SELECT idultima_accion, idcom, ultima_accion FROM ultima_accion ORDER BY fecha DESC";
$accion_lateral = mysql_query($query_accion_lateral, $dspp) or die(mysql_error());
$row_accion_lateral = mysql_fetch_assoc($accion_lateral);
$totalRows_accion_lateral = mysql_num_rows($accion_lateral);




$colname_com = "-1";
 
$colname_com = $_GET['idsolicitud'];




$query_com = sprintf("SELECT com.* ,solicitud_registro.* FROM solicitud_registro INNER JOIN com ON solicitud_registro.idcom = com.idcom WHERE solicitud_registro.idsolicitud_registro = %s ORDER BY solicitud_registro.fecha_elaboracion DESC", GetSQLValueString($colname_com, "int"));


$com = mysql_query($query_com, $dspp) or die(mysql_error());
$row_com = mysql_fetch_assoc($com);
$totalRows_com = mysql_num_rows($com);

$colname_cta_bn = "-1";
if (isset($_GET['idcom'])) {
  $colname_cta_bn = $_GET['idcom'];
}
$query_cta_bn = sprintf("SELECT * FROM cta_bn WHERE idcom = %s", GetSQLValueString($colname_cta_bn, "int"));
$cta_bn = mysql_query($query_cta_bn, $dspp) or die(mysql_error());
//$row_cta_bn = mysql_fetch_assoc($cta_bn);
$totalRows_cta_bn = mysql_num_rows($cta_bn);

$colname_contacto = "-1";
if (isset($_GET['idcom'])) {
  $colname_contacto = $_GET['idcom'];
}
$query_contacto = sprintf("SELECT * FROM contacto WHERE idcom = %s ORDER BY tipo ASC, contacto asc", GetSQLValueString($colname_contacto, "int"));
$contacto = mysql_query($query_contacto, $dspp) or die(mysql_error());
//$row_contacto = mysql_fetch_assoc($contacto);
$totalRows_contacto = mysql_num_rows($contacto);

//$query_oc = "SELECT * FROM oc ORDER BY nombre ASC";
$query_oc = "SELECT * FROM oc WHERE idoc = $_SESSION[idoc]";
$oc = mysql_query($query_oc, $dspp) or die(mysql_error());
$row_oc = mysql_fetch_assoc($oc);
$totalRows_oc = mysql_num_rows($oc);

$query_pais = "SELECT * FROM paises ORDER BY nombre ASC";
$pais = mysql_query($query_pais, $dspp) or die(mysql_error());
//$row_pais = mysql_fetch_assoc($pais);
$totalRows_pais = mysql_num_rows($pais);



/**************************************** VARIABLES DE CONTROL ********************************************/
//ESTATUS PUBLICO ///////////////////////////
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
//17) Cotización Enviada
//18) Proceso Iniciado

$estado_publico = 2;
$estado_interno = 0;

$correoOC = $row_oc['email'];
/////// EMAILS OBTENIDOS DEL FORMULARIO EN EL AREA DE PERSONAS DE CONTACTO
$correocom1 = $row_solicitud['p1_correo'];
$correocom2 = $row_solicitud['p2_correo'];
$correoOculto = "yasser.midnight@gmail.com";
/////////////////////////////////////////////////////////////////////////

/******************************************* VARIABLES DE CONTROL ******************************************/

/************************* SE APRUEBA Y ENVIAN LAS COTIZACIONES***********************************/
if(isset($_POST['cotizacion'])){
  $fecha_actual = time();
  $idcom = $_POST['idcom'];
  $idoc = $_POST['idoc'];
  $status = "17";
  $rutaArchivo = "../../archivos/ocArchivos/cotizaciones/";
  $procedimiento = $_POST['procedimiento'];

  if(!empty($_FILES['cotizacion_com']['name'])){
      $_FILES["cotizacion_com"]["name"];
        move_uploaded_file($_FILES["cotizacion_com"]["tmp_name"], $rutaArchivo.date("Ymd H:i:s")."_".$_FILES["cotizacion_com"]["name"]);
        $cotizacion_com = $rutaArchivo.basename(date("Ymd H:i:s")."_".$_FILES["cotizacion_com"]["name"]);
  }else{
    $cotizacion_com = NULL;
  }
  if(!empty($_FILES['cotizacion_adm']['name'])){
      $_FILES["cotizacion_adm"]["name"];
        move_uploaded_file($_FILES["cotizacion_adm"]["tmp_name"], $rutaArchivo.date("Ymd H:i:s")."_".$_FILES["cotizacion_adm"]["name"]);
        $cotizacion_adm = $rutaArchivo.basename(date("Ymd H:i:s")."_".$_FILES["cotizacion_adm"]["name"]);
  }else{
    $cotizacion_adm = NULL;
  }
    if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {

        $updateSQL = "UPDATE solicitud_registro SET 
          idoc= $_POST[idoc], 
          cotizacion_com = '$cotizacion_com',
          cotizacion_adm = '$cotizacion_adm',
          status_interno= '$status',
          procedimiento = '$procedimiento'
          WHERE idsolicitud_registro = $_POST[idsolicitud_registro]";

          mysql_select_db($database_dspp, $dspp);
          $Result1 = mysql_query($updateSQL, $dspp) or die(mysql_error());
          
          /*$insertGoTo = "main_menu.php?SOLICITUD&select";
          if (isset($_SERVER['QUERY_STRING'])) {
            $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
            $insertGoTo .= $_SERVER['QUERY_STRING'];
          }
          header(sprintf("Location: %s", $insertGoTo));*/



            $nombre = $_POST['nombreCOM'];
            $abreviacion = $_POST['abreviacionCOM'];
            $fecha = date("d/m/Y", $_POST['fecha_cotizacion']);
            $paiscom = $_POST['paisCOM'];
        

            //$correo = $_POST['p1_email'];
            //$correo = $_POST['p2_email'];

            $destinatario = $correocom1.",";
            $destinatario .= $correocom2;
            

            $asunto = "D-SPP Cotización Solicitud Registro para Compradores y otros Actores"; 

        $cuerpo = '
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
                  <td align="left" style="color:#ff738a;">Email OC: '.$row_oc['email'].'</td>
                </tr>

                <tr>
                  <td align="left">'.$row_oc['pais'].'</td>
                </tr>
                <tr>
                  <td aling="left" style="text-align:justify">Se ha enviado la cotización correspondiente a la Solicitud de Registro para Compradores y otros Actores.
                  <br><br> Por favor iniciar sesión en el siguiente enlace <a href="http://d-spp.org/?OC">www.d-spp.org/?OC</a> , para poder continuar aceptar o denegar a la cotización.</td>
                </tr>

                <tr>
                  <td colspan="2">
                    <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;" border="1" width="650px">
                      <tbody>
                        <tr style="font-size: 12px; text-align:center; background-color:#dff0d8; color:#3c763d;" height="50px;">
                          <td width="130px">Nombre de la organización/Company name</td>
                          <td width="130px">Abreviación / Short name</td>
                          <td width="130px">País / Country</td>
                          <td width="130px">Organismo de Certificación / Certification Entity</td>
                          <td width="130px">Fecha de envío / Shipping Date</td>
                       
                          
                        </tr>
                        <tr style="font-size: 12px; text-align:justify">
                          <td style="padding:10px;">
                            '.$nombre.'
                          </td>
                          <td style="padding:10px;">
                            '.$abreviacion.'
                          </td>
                          <td style="padding:10px;">
                            '.$paiscom.'
                          </td>
                          <td style="padding:10px;">
                            '.$row_solicitud['nombreOC'].'
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

        //$mail->AddAddress("soporteinforganic@gmail.com");
        $mail->AddAddress($correocom1);
        $mail->AddAddress($correocom2);
        $mail->AddAttachment($cotizacion_com);

        //$mail->Username = "soporte@d-spp.org";
        //$mail->Password = "/aung5l6tZ";
        $mail->Subject = utf8_decode($asunto);
        $mail->Body = utf8_decode($cuerpo);
        $mail->MsgHTML(utf8_decode($cuerpo));
        $mail->Send();
        $mail->ClearAddresses();

            // SE INSERTA LA FECHA EN LA QUE SE ENVIO LA COTIZACION

            $idexterno = $_POST['idsolicitud_registro'];
            $identificador = "SOLICITUD";
        
            //$queryFecha = "INSERT INTO fecha(fecha, idexterno, identificador, status) VALUES($fecha_actual, $idexterno, '$identificador', $status)";
            //$ejecutar = mysql_query($queryFecha,$dspp) or die(mysql_error());
          
            $queryFecha = "INSERT INTO fecha(fecha, idexterno, idcom, idoc, identificador, status) VALUES($fecha_actual, $idexterno, $idcom, $idoc, '$identificador', $status)";
            $ejecutar = mysql_query($queryFecha,$dspp) or die(mysql_error());

        $queryMensaje = "INSERT INTO mensajes(idcom, idoc, asunto, mensaje, destinatario, remitente, fecha) VALUES($idcom, $idoc, '$asunto', '$cuerpo', 'COM', 'OC', $fecha_actual)";
        $ejecutar = mysql_query($queryMensaje,$dspp) or die(mysql_error());


          echo "<script>window.location='main_menu.php?SOLICITUD&selectCOM&mensaje=Se ha enviado la cotización'</script>"; 
    }

}
/************************* FIN SE APRUEBA Y ENVIAN LAS COTIZACIONES***********************************/

/************************* INICIO SE DENIEGA Y ENVIAR A REVISION***********************************/

if(isset($_POST['denegado'])){
  $estado_interno = "2";
  $idcom = $_POST['idcom'];

    if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {

      $updateSQL = "UPDATE solicitud_registro SET 
        idoc= $_POST[idoc], 
        status_interno = '$estado_interno' 
        WHERE idsolicitud_registro= $_POST[idsolicitud_registro]";

        mysql_select_db($database_dspp, $dspp);
        $Result1 = mysql_query($updateSQL, $dspp) or die(mysql_error());

    }

   
        $nombre = "$row_com[nombre]";



        $asunto = "D-SPP Modificación Solicitud de Registro para Compradores y otros Actores"; 


    $cuerpo = '
      <html>
      <head>
        <meta charset="utf-8">
      </head>
      <body>
      
        <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
          <tbody>
            <tr>
              <th rowspan="4" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
              <th scope="col" align="left" width="280" ><strong>Notificación de Solicitud / Notification of request</strong></th>
            </tr>
            <tr>
              <td align="left" style="color:#ff738a;">Email OC: '.$row_oc['email'].'</td>
            </tr>

            <tr>
              <td align="left">'.$row_oc['pais'].'</td>
            </tr>
            <tr>
              <td aling="left" style="text-align:justify">"Se han encontrado ciertas anomalias en la Solicitud de Registro para Compradores y otros Actores, por lo tanto se han realizado las observaciones correspondientes.
              <br><br>  Por favor iniciar sesión en el siguiente enlace <a href="http://d-spp.org/?COM">www.d-spp.org/?COM</a> , para poder acceder a su solicitud y asi <b>corregir los datos de la misma</b>.</td>
            </tr>

            <tr>
              <td colspan="2">
                <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;" border="1" width="650px">
                  <tbody>
                    <tr style="font-size: 12px; text-align:center; background-color:#dff0d8; color:#3c763d;" height="50px;">
                      <td width="130px">Nombre de la Empresa/Company name</td>
                      <td width="130px">Abreviación / Short name</td>
                      <td width="130px">País / Country</td>
                      <td width="130px">Organismo de Certificación / Certification Entity</td>
                      <td width="130px">Fecha de envío / Shipping Date</td>
                   
                      <td width="130px">Fecha de solicitud/Date of application</td>
                    </tr>
                    <tr style="font-size: 12px; text-align:justify">
                      <td style="padding:10px;">
                        '.$nombre.'
                      </td>
                      <td style="padding:10px;">
                        '.$abreviacion.'
                      </td>
                      <td style="padding:10px;">
                        '.$paiscom.'
                      </td>
                      <td style="padding:10px;">
                        '.$row_solicitud['nombreOC'].'
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

        $mail->AddAddress($correocom1);
        $mail->AddAddress($correocom2);

        //$mail->Username = "soporte@d-spp.org";
        //$mail->Password = "/aung5l6tZ";
        $mail->Subject = utf8_decode($asunto);
        $mail->Body = utf8_decode($cuerpo);
        $mail->MsgHTML(utf8_decode($cuerpo));
        $mail->Send();
        $mail->ClearAddresses();


        $queryMensaje = "INSERT INTO mensajes(idcom, idoc, asunto, mensaje, destinatario, remitente, fecha) VALUES($idcom, $idoc, $asunto', '$cuerpo', 'COM', 'OC', $fecha_actual)";
        $ejecutar = mysql_query($queryMensaje,$dspp) or die(mysql_error());

}
/************************* FIN SE DENIEGA Y ENVIA A REVISION***********************************/

/************************* INICIO PARA GUARDAR LOS CAMBIOS ***********************************/

if(isset($_POST['guardarCambios']) && $_POST['guardarCambios'] == "guardarCambios"){

    if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {

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


    $updateSQL = "UPDATE solicitud_registro SET 
      idcom= $_POST[idcom], 
      p1_nombre= '$_POST[p1_nombre]', 
      p1_cargo= '$_POST[p1_cargo]', 
      p1_telefono= '$_POST[p1_telefono]', 
      p1_correo= '$_POST[p1_correo]', 
      p2_nombre= '$_POST[p2_nombre]', 
      p2_cargo= '$_POST[p2_cargo]', 
      p2_telefono= '$_POST[p2_telefono]', 
      p2_correo= '$_POST[p2_correo]', 
      adm1_nombre= '$_POST[adm1_nombre]', 
      adm2_nombre= '$_POST[adm2_nombre]', 
      adm1_telefono= '$_POST[adm1_telefono]', 
      adm2_telefono= '$_POST[adm2_telefono]', 
      adm1_correo= '$_POST[adm1_correo]', 
      adm2_correo= '$_POST[adm2_correo]', 
      tipo_empresa = '$array_tipo_empresa',
      resp1= '$_POST[resp1]', 
      resp2= '$_POST[resp2]', 
      resp3= '$_POST[resp3]', 
      resp4= '$_POST[resp4]',
      resp5= '$array_resp5',  
      resp6= '$resp6', 
      resp7= '$_POST[resp7]', 
      resp8= '$_POST[resp8]', 
      resp9= '$_POST[resp9]', 
      resp10= '$_POST[resp10]', 
      resp12= '$_POST[resp12]', 
      resp13= '$_POST[resp13]', 
      resp14= '$resp14', 
      resp14_15= '$resp14_15', 
      resp16 = '$_POST[resp16]',
      responsable = '$_POST[responsable]'
      WHERE idsolicitud_registro= $_POST[idsolicitud_registro]";

      mysql_select_db($database_dspp, $dspp);
      $Result1 = mysql_query($updateSQL, $dspp) or die(mysql_error());


      $updateSQL = "UPDATE com SET
      nombre = '$_POST[nombreCOM]',
      direccion = '$_POST[direccionCOM]',
      email = '$_POST[correoCOM]',
      telefono = '$_POST[telefonoCOM]',
      sitio_web = '$_POST[paginaCOM]',
      ciudad = '$_POST[ciudadCOM]',
      direccion_fiscal = '$_POST[domicilioFiscal]',
      ruc = '$_POST[ruc]',
      rfc = '$_POST[rfc]'
      WHERE idcom = $_POST[idcom]";

      $ejecutar = mysql_query($updateSQL,$dspp) or die(mysql_error());

   /* $certificacion = $_POST['certificacion'];
    $certificadora = $_POST['certificadora'];
    $ano_inicial = $_POST['ano_inicial'];
    $interrumpida = $_POST['interrumpida'];
    $idcertificacion = $_POST['idcertificacion'];

    for($i=0;$i<count($certificacion);$i++){
      if($certificacion[$i] != NULL){
        #for($i=0;$i<count($certificacion);$i++){

        $updateSQL = "UPDATE certificaciones SET certificacion= '".$certificacion[$i]."', certificadora='".$certificadora[$i]."', ano_inicial= '".$ano_inicial[$i]."', interrumpida= '".$interrumpida[$i]."' WHERE idcertificacion= '".$idcertificacion[$i]."'";

        $Result1 = mysql_query($updateSQL, $dspp) or die(mysql_error());
      }
    }


      $producto = $_POST['producto'];
      $volumen = $_POST['volumen'];
      $materia = $_POST['materia'];
      $destino = $_POST['destino'];
      $idproducto = $_POST['idproducto'];
      /*$marca_propia = $_POST['marca_propia'];
      $marca_cliente = $_POST['marca_cliente'];
      $sin_cliente = $_POST['sin_cliente'];*/

  /*  for ($i=0;$i<count($producto);$i++) { 
      if($producto[$i] != NULL){

      $array1 = "terminado".$i; 
      $array2 = "marca_propia".$i;
      $array3 = "marca_cliente".$i;
      $array4 = "sin_cliente".$i;

      $terminado = $_POST[$array1];
      $marca_propia = $_POST[$array2];
      $marca_cliente = $_POST[$array3];
      $sin_cliente = $_POST[$array4];

          $updateSQL = "UPDATE productos SET 
          producto= '".$producto[$i]."',
          volumen= '".$volumen[$i]."',
          terminado= '".$terminado."',
          materia='".$materia[$i]."',
          destino='".$destino[$i]."',
          marca_propia='". $marca_propia."',
          marca_cliente='".$marca_cliente."', 
          sin_cliente= '".$sin_cliente."' 
          WHERE idproducto= '".$idproducto[$i]."'";
          $Result = mysql_query($updateSQL, $dspp) or die(mysql_error());
      }
    }*/

}



      $insertGoTo = "main_menu.php?SOLICITUD&select";
      if (isset($_SERVER['QUERY_STRING'])) {
        $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
        $insertGoTo .= $_SERVER['QUERY_STRING'];
      }
      //header(sprintf("Location: %s", $insertGoTo));
      echo "<script>alert('Se han modificado los datos correctamente');</script>";
      //echo "<script>location.reload(true)</script>";
      echo "<script>window.location='main_menu.php?SOLICITUD&detailCOM&idsolicitud=$_POST[idsolicitud_registro]'</script>"; 



}
/************************* FIN SE GUARDAN LOS CAMBIOS ***********************************/




if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form2")) {
  $updateSQL = "UPDATE solicitud_registro SET 
  observaciones= '$_POST[observaciones]' 
  WHERE idsolicitud_registro= $_POST[idsolicitud_registro]";

  mysql_select_db($database_dspp, $dspp);
  $Result1 = mysql_query($updateSQL, $dspp) or die(mysql_error());


  /*$insertGoTo = "oc/main_menu.php?SOLICITUD&select";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));*/
  echo "<script>window.location='main_menu.php?SOLICITUD&selectCOM'</script>"; 


}


/***********************************************************************************************/
/***********************************************************************************************/




?>

<script>
  function validar(){
    valor = document.getElementById("cotizacion_com").value;
    if( valor == null || valor.length == 0 ) {
      alert("No se ha cargado la cotización de com");
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
 /* function mostrar(){
    document.getElementById('oculto').style.display = 'block';
  }
  function ocultar()
  {
    document.getElementById('oculto').style.display = 'none';
  }*/

    function habilitar(){
        $(".claseModificar").removeAttr("readonly");
        document.getElementById('guardarCambios').style.display = 'inline';
        var modificar = document.getElementsByName('tablaOculta');
        modificar[0].style.display = 'block';
        modificar[1].style.display = 'none';
        modificar[2].style.display = 'block';
        modificar[3].style.display = 'none';
        modificar[4].style.display = 'block';
        //document.getElementsByName('tablaOculta').style.display = 'none';

    }
 
    function deshabilitar(){
        $(".claseModificar").attr("readonly","readonly");
        //document.getElementsByClassName("tablaOculta").style.display = 'none';
    }

</script>



<hr>


<div class="row-xs-12">
  
  <div class="col-xs-12">

  <!------------------------------ MENSAJE ACTUALIZAR ---------------------------------------------->
  <? if(isset($_POST['aprobado'])){?>
    <p>
    <div class="alert alert-success" role="alert"><b>Se han enviado las cotizaciones</b></div>
    </p>
  <? }else if(isset($_POST['denegado'])){?>
    <p>
    <div class="alert alert-danger" role="alert"><b>Se ha denegado la solicitud</b></div>

      <form action="<?php echo $editFormAction; ?>" name="form2" method="post">
          <div class="form-group has-error">
            <textarea type="text" class="form-control" id="inputError1" name="observaciones" placeholder="Puntos por los cuales no se pudo aprobar la solicitud" required></textarea>
          </div>  

          <input type="hidden" name="MM_insert" value="form2">
          <input type="hidden" name="idsolicitud_registro" value="<?php echo $_GET['idsolicitud']?>">
          <input class="btn btn-danger" id="opinion" name="opinion" type="submit" value="Guardar">
      </form>


    </p>
  <?}?>  
  <!---------------------------------- MENSAJE ACTUALIZAR ------------------------------------------>
    
  <!------------------------------ MENSAJE DE DENEGACION ---------------------------------------------->
  <? if(!empty($row_solicitud['observaciones'])){?>
    <p>
      <div class="alert alert-danger" role="alert">
        <h4>Observaciones realizadas por: <?echo $row_solicitud['nombreOC']?></h4>
        <br>
        <? echo nl2br($row_solicitud['observaciones']);?>
      </div>
    </p>
  <? }?>
  <!---------------------------------- MENSAJE DE DENEGACION ------------------------------------------>
    



<form class="" method="post" name="formularioOC" action="<?php echo $editFormAction; ?>" enctype="multipart/form-data">

<?php if(empty($row_solicitud['cotizacion_com']) && empty($row_solicitud['cotizacion_adm'])){?>
<div class="col-xs-12">
  <div class="panel panel-primary">
    <div class="panel-heading">
      <h3 class="panel-title text-center">Cotizaciones</h3>
    </div>
    <div class="panel-body"><!---- INICIA BODY ----> 
      <div class="row">
        <div class="col-xs-12">
    
          <?php if(empty($row_solicitud['cotizacion_com'])){ ?>
        
            <div class="col-xs-6">
              <h5>Enviar cotización a <u style="color:red;"><?echo $row_com['nombre'];?></u></h5>
            </div>
            <div class="col-xs-6">
              <input name="cotizacion_com" id="cotizacion_com" type="file" class="filestyle" data-buttonName="btn-info" data-buttonBefore="true" data-buttonText="Cargar Cotización"> 
            </div>
        
          <?php }?>

        </div>

        <div class="col-xs-12">
      
          <?php if(empty($row_solicitud['cotizacion_adm'])){ ?>

            <div class="col-xs-6">
              <h5>Enviar cotización a <u style="color:red;">FUNDEPPO</u> (Opcional)</h5>
            </div>
            <div class="col-xs-6">
              <input name="cotizacion_adm" type="file" class="filestyle" data-buttonName="btn-info" data-buttonBefore="true" data-buttonText="Cargar Cotización">
            </div>
          <?php } ?> 

        </div>
      </div>   

    </div><!---- TERMINA BODY ---->

    <div class="panel-footer">
         <?php /*
          if(empty($row_solicitud['cotizacion_com']) || empty($row_solicitud['cotizacion_adm'])){
            if($row_solicitud['status_publico'] == $estado_publico){
         ?>
            <div class="row">
              <div class="col-xs-2">
                <button class="btn btn-sm btn-success" id="cotizacion" name="cotizacion" type="submit" value="Enviar" aria-label="Left Align" onclick="return validar()">
                  <span class="glyphicon glyphicon-open-file" aria-hidden="true"></span> Enviar Cotización
                </button>
                
              </div>

              <div class="col-xs-3">
                <button class="btn btn-sm btn-danger" id="denegado" name="denegado" type="submit" value="denegado" aria-label="Left Align">
                  <span class="glyphicon glyphicon-share" aria-hidden="true"></span> Denegar | Observaciones
                </button>
                
              </div>

              <div class="col-xs-2">
                  <button type="button" class="btn btn-sm btn-primary"  onclick="habilitar()"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Modificar Solicitud</button>   
                  
              </div>

              <div class="col-xs-2">
                <!--<a href="?SOLICITUD&amp;detail&amp;idsolicitud=<?php echo $row_com['idsolicitud_registro']; ?>" class="btn btn-primary">
                  <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Modificar-->
                <!--  <button type="button" class="btn btn-primary"><span class="glyphicon glyphicon-pencil" aria-hidden="true" onclick="myFunction()"></span> Modificar</button>
                </a>-->

                <!--<button type="button" class="btn btn-primary"  onclick="deshabilita()"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> DEHABILIA</button>-->

              </div>

            </div>
          <?php 
              }
            } */
          ?>      
      
         <?php 
          if(empty($row_solicitud['cotizacion_com']) || empty($row_solicitud['cotizacion_adm'])){
            if($row_solicitud['status_publico'] == $estado_publico){
         ?>

                <button class="btn btn-sm btn-success" id="cotizacion" name="cotizacion" type="submit" value="Enviar" aria-label="Left Align" onclick="return validar()">
                  <span class="glyphicon glyphicon-open-file" aria-hidden="true"></span> Enviar Cotización
                </button>

            
                <button type="button" class="btn btn-sm btn-primary"  onclick="habilitar()"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Modificar Solicitud</button>   
    
                <button class="btn btn-sm btn-danger" id="denegado" name="denegado" type="submit" value="denegado" aria-label="Left Align">
                  <span class="glyphicon glyphicon-share" aria-hidden="true"></span> Denegar | Observaciones
                </button>

          <?php 
              }
            } 
          ?>   



    </div>

  </div>

</div>

<?php } ?>

<p class="alert alert-info" name="tablaOculta" style="display:none">Se han desbloqueado los campos de la solicitud, ahora puedes corregir la información, una vez finalizada la revisión da click en el siguiente botón   <button type="submit" class="btn btn-success" name="guardarCambios" id="guardarCambios" onclick="habilitar()" value="guardarCambios" style="display:none"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Guardar Cambios</button>    </p>  

  <table class="table table-bordered table-striped col-xs-12">
    <tr>
      <th colspan="4" class="text-center"><h3>Solicitud de Registro para Compradores y otros Actores</h3></th>
    </tr>
    <?php 
      $procedimiento = $row_solicitud['procedimiento'];
     ?>
    <tr>
      <?php if($procedimiento == null){ ?>

        <td colspan="4">
                  <div class="col-xs-12 text-center">
                    <div class="row">
                  <h4>Procedimiento de Certificación <br><small>(realizado por OC)</small></h4>
                    </div>
                  </div>
                  <div class="col-xs-3 text-center">
                    <div class="row">
                      <div class="col-xs-12">
                        <p style="font-size:10px;"><b>DOCUMENTAL "ACORTADO"</b></p> 
                      </div>       
                      <div class="col-xs-12">
                        <input type="radio" data-on-color="success" data-off-color="danger" data-size="small" name="procedimiento" value='DOCUMENTAL "ACORTADO"' <?php if($procedimiento == null){}else{} ?>>
        
                      </div>                        
                    </div>
                  </div>
                  <div class="col-xs-3 text-center">
                    <div class="row">
                      <div class="col-xs-12">
                        <p style="font-size:10px;"><b>DOCUMENTAL "NORMAL"</b></p> 
                      </div>
                      <div class="col-xs-12">
                        <input type="radio" data-on-color="success" data-off-color="danger" data-size="small" name="procedimiento" value='DOCUMENTAL "NORMAL"'>
        
                      </div>                
                    </div>
                  </div>
                  <div class="col-xs-3 text-center">
                    <div class="row">
                      <div class="col-xs-12">
                        <p style="font-size:10px;"><b>COMPLETO "IN SITU"</b></p>  
                      </div>
                      <div class="col-xs-12">
                        <input type="radio" data-on-color="success" data-off-color="danger" data-size="small" name="procedimiento" value='COMPLETO "IN SITU"'>
        
                      </div>                
                    </div>
                  </div>
                  <div class="col-xs-3 text-center">
                    <div class="row">
                      <div class="col-xs-12">
                        <p style="font-size:10px;"><b>COMPLETO "A DISTANCIA"</b></p>  
                      </div>
                      <div class="col-xs-12">
                        <input type="radio" data-on-color="success" data-off-color="danger" data-size="small" name="procedimiento" value='COMPLETO "A DISTANCIA"'>
        
                      </div>                
                    </div>
                  </div>    
        </td>

      <?php }else{ ?>

        <td colspan="4">

                  <div class="col-xs-12 text-center">
                    <div class="row">
                  <h4>Procedimiento de Certificación <br><small>(realizado por OC)</small></h4>
                    </div>
                  </div>
                  <div class="col-xs-3 text-center">
                    <div class="row">
                      <div class="col-xs-12">
                        <p style="font-size:10px;"><b>DOCUMENTAL "ACORTADO"</b></p> 
                      </div>       
                      <div class="col-xs-12">
                        <input type="radio" data-on-color="success" data-off-color="danger" data-size="small" name="procedimiento" value='DOCUMENTAL "ACORTADO"' <?php if($procedimiento == 'DOCUMENTAL "ACORTADO"'){echo "checked";}else{echo "readonly";} ?> >
        
                      </div>                        
                    </div>
                  </div>
                  <div class="col-xs-3 text-center">
                    <div class="row">
                      <div class="col-xs-12">
                        <p style="font-size:10px;"><b>DOCUMENTAL "NORMAL"</b></p> 
                      </div>
                      <div class="col-xs-12">
                        <input type="radio" data-on-color="success" data-off-color="danger" data-size="small" name="procedimiento" value='DOCUMENTAL "NORMAL"' <?php if($procedimiento == 'DOCUMENTAL "NORMAL"'){echo "checked";}else{echo "readonly";} ?> >
        
                      </div>                
                    </div>
                  </div>
                  <div class="col-xs-3 text-center">
                    <div class="row">
                      <div class="col-xs-12">
                        <p style="font-size:10px;"><b>COMPLETO "IN SITU"</b></p>  
                      </div>
                      <div class="col-xs-12">
                        <input type="radio" data-on-color="success" data-off-color="danger" data-size="small" name="procedimiento" value='COMPLETO "IN SITU"' <?php if($procedimiento == 'COMPLETO "IN SITU"'){echo "checked";}else{echo "readonly";} ?> >
        
                      </div>                
                    </div>
                  </div>
                  <div class="col-xs-3 text-center">
                    <div class="row">
                      <div class="col-xs-12">
                        <p style="font-size:10px;"><b>COMPLETO "A DISTANCIA"</b></p>  
                      </div>
                      <div class="col-xs-12">
                        <input type="radio" data-on-color="success" data-off-color="danger" data-size="small" name="procedimiento" value='COMPLETO "A DISTANCIA"' <?php if($procedimiento == 'COMPLETO "A DISTANCIA"'){echo "checked";}else{echo "readonly";} ?> >
        
                      </div>                
                    </div>
                  </div>    
        </td>

      <?php } ?>

    </tr>

    <tr class="success">
      <th colspan="4" class="text-center">DATOS GENERALES</th>
    </tr>

    <tr>
      <td>
        <p>NOMBRE DE LA EMPRESA</p>
      </td>
      <td>
        <input type="text" class="claseModificar form-control" name="nombreCOM" value="<?php echo $row_com['nombre']?>" readonly>
      </td>
    </tr>
    <tr>
      <td>
        <p>DIRECCIÓN COMPLETA DE LAS OFICINAS CENTRALES (CALLE, BARRIO, LUGAR, REGIÓN)</p>
      </td>
      <td>
        <input type="text" class="claseModificar form-control" name="direccionCOM" value="<?php echo $row_com['direccion'];?>" placeholder="Dirección de las Oficinas" readonly>
      </td>
    </tr>
    <tr>
      <td>
        <p>CORREO ELECTRÓNICO</p> 
        <input type="text" class="claseModificar form-control" name="correoCOM" value="<?php echo $row_com['email']?>" readonly>
      </td>
      <td>
        <p>TELÉFONOS (CÓDIGO DE PAÍS+CÓDIGO DE ÁREA+NÚMERO)</p> 
        <input type="text" class="claseModificar form-control" name="telefonoCOM" value="<?php echo $row_com['telefono']?>" readonly>
      </td>
    </tr>
    <tr>
      <td>
        <p>PAÍS</p>
        <input type="text" class="claseModificar form-control" name="paisCOM" value="<?php echo $row_com['pais']?>" readonly>
      </td>
      <td>
        <p>SITIO WEB</p>
        <input type="text" class="claseModificar form-control" name="paginaCOM" value="<?php echo $row_com['sitio_web']?>" readonly>
      </td>
    </tr>
    <tr>
      <td>
        <p>CIUDAD</p>
        <input type="text" class="claseModificar form-control" name="ciudadCOM" value="<?php echo $row_com['ciudad']?>" readonly>
      </td>
      <td>
        <p>DOMICILIO FISCAL</p>
        <input type="text" class="claseModificar form-control" name="domicilioFiscal" value="<?php echo $row_com['direccion_fiscal']?>" readonly>
      </td>
    </tr>
    <tr>
      <td>
        <p>RUC</p>
        <input type="text" class="claseModificar form-control" name="ruc" value="<?php echo $row_com['ruc']?>" readonly>
      </td>
      <td>
        <p>RFC</p>
        <input type="text" class="claseModificar form-control" name="rfc" value="<?php echo $row_com['rfc']?>" readonly>
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
          <input type="text" class="claseModificar form-control" name="p1_nombre" value="<?php echo $row_solicitud['p1_nombre']; ?>" placeholder="Nombre 1" readonly required>
          <input type="email" class="claseModificar form-control" name="p1_correo" value="<?php echo $row_solicitud['p1_correo']; ?>" placeholder="Correo Electronico 1" readonly required>
        </div>
        <div class="col-xs-6">
          <input type="text" class="claseModificar form-control" name="p1_cargo" value="<?php echo $row_solicitud['p1_cargo']; ?>" placeholder="Cargo 1" readonly required>
          <input type="text" class="claseModificar form-control" name="p1_telefono" value="<?php echo $row_solicitud['p1_telefono']; ?>" placeholder="Telefono 1" readonly required>
        </div>
        <div class="col-xs-12"><br></div>
        <div class="col-xs-6">
          <input type="text" class="claseModificar form-control" name="p2_nombre" value="<?php echo $row_solicitud['p2_nombre']; ?>" placeholder="Nombre 2" readonly>
          <input type="email" class="claseModificar form-control" name="p2_correo" value="<?php echo $row_solicitud['p2_correo']; ?>" placeholder="Correo Electronico 2" readonly>
        </div>
        <div class="col-xs-6">
          <input type="text" class="claseModificar form-control" name="p2_cargo" value="<?php echo $row_solicitud['p2_cargo']; ?>" placeholder="Cargo 2" readonly>
          <input type="text" class="claseModificar form-control" name="p2_telefono" value="<?php echo $row_solicitud['p2_telefono']; ?>" placeholder="Telefono 2" readonly>
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
          <input type="text" class="claseModificar form-control" name="adm1_nombre" value="<?php echo $row_solicitud['adm1_nombre']; ?>" placeholder="Nombre 1" readonly required>
          <input type="email" class="claseModificar form-control" name="adm1_correo" value="<?php echo $row_solicitud['adm1_correo']; ?>" placeholder="Correo Electronico 1" readonly required>
        </div>
        <div class="col-xs-6">

          <input type="text" class="claseModificar form-control" name="adm1_telefono" value="<?php echo $row_solicitud['adm1_telefono']; ?>" placeholder="Telefono 1" readonly required>
        </div>
        <div class="col-xs-12"><br></div>
        <div class="col-xs-6">
          <input type="text" class="claseModificar form-control" name="adm2_nombre" value="<?php echo $row_solicitud['adm2_nombre']; ?>" placeholder="Nombre 2" readonly>
          <input type="email" class="claseModificar form-control" name="adm2_correo" value="<?php echo $row_solicitud['adm2_correo']; ?>" placeholder="Correo Electronico 2" readonly>
        </div>
        <div class="col-xs-6">

          <input type="text" class="claseModificar form-control" name="adm2_telefono" value="<?php echo $row_solicitud['adm2_telefono']; ?>" placeholder="Telefono 2" readonly>
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

      <?php 
        $texto = $row_solicitud['tipo_empresa'];
       ?>
        <div class="col-xs-4">
         <?php 
          $cadena_buscada = "comprador_final";
          $posicion_coincidencia = strpos($texto, $cadena_buscada);
          if($posicion_coincidencia === false){
            echo 'COMPRADOR FINAL <input class="form-control" name="tipo_empresa[]" type="checkbox" value="comprador_final"  readonly>';
          }else{
            echo 'COMPRADOR FINAL <input class="form-control" name="tipo_empresa[]" type="checkbox" value="comprador_final" checked readonly>';
          }
          ?>
        </div>
        <div class="col-xs-4">
         <?php 
          $cadena_buscada = "intermediario";
          $posicion_coincidencia = strpos($texto, $cadena_buscada);
          if($posicion_coincidencia === false){
            echo 'INTERMEDIARIO <input class="form-control" name="tipo_empresa[]" type="checkbox" value="intermediario"  readonly>';
          }else{
            echo 'INTERMEDIARIO <input class="form-control" name="tipo_empresa[]" type="checkbox" value="intermediario" checked readonly>';
          }
          ?>
        </div>
        <div class="col-xs-4">
         <?php 
          $cadena_buscada = "maquilador";
          $posicion_coincidencia = strpos($texto, $cadena_buscada);
          if($posicion_coincidencia === false){
            echo 'MAQUILADOR <input class="form-control" name="tipo_empresa[]" type="checkbox" value="maquilador"  readonly>';
          }else{
            echo 'MAQUILADOR <input class="form-control" name="tipo_empresa[]" type="checkbox" value="maquilador" checked readonly>';
          }
          ?>
        </div>

      </td>
    </tr>
    <tr>
      <td colspan="2">
        <p>1.- ¿CUÁLES SON LAS ORGANIZACIONES DE PEQUEÑOS PRODUCTORES A LAS QUE LES COMPRA O PRETENDE COMPRAR BAJO EL ESQUEMA DEL SÍMBOLO DE PEQUEÑOS PRODUCTORES?</p>
        <textarea name="resp1" id="" class="claseModificar form-control" value="" readonly><?php echo $row_solicitud['resp1']; ?></textarea>
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <p>2.- ¿QUIÉN O QUIÉNES SON LOS PROPIETARIOS DE LA EMPRESA?</p>
        <textarea name="resp2" id="" class="claseModificar form-control" value="" readonly><?php echo $row_solicitud['resp2']; ?></textarea>
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <p>3.- ESPECIFIQUE QUÉ PRODUCTO(S) QUIERE INCLUIR EN EL CERTIFICADO DEL SÍMBOLO DE PEQUEÑOS PRODUCTORES PARA LOS CUALES EL ORGNISMO DE CERTIFICACIÓN REALIZARÁ LA EVALUACIÓN.</p>
        <textarea name="resp3" id="" class="claseModificar form-control" value="" readonly><?php echo $row_solicitud['resp3']; ?></textarea>
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <p>4.- SI SU EMPRESA ES UN COMPRADOR FINAL, MENCIONE SI QUIEREN INCLUIR ALGÚN CALIFICATIVO ADICIONAL PARA USO COMPLEMENTARIO CON EL DISEÑO GRÁFICO DEL SÍMBOLO DE PEQUEÑOS PRODUCTORES. <sup>4</sup> </p>
        
        <textarea name="resp4" id="" class="claseModificar form-control" value="" readonly><?php echo $row_solicitud['resp4']; ?></textarea>
        <p><small><sup>4</sup>   Revisar el Reglamento Gráfico y la Lista de Calificativos Complementarios Opcionales vigentes</small></p>
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <p>5.- SELECCIONE EL ALCANCE QUE TIENE LA EMPRESA</p>

      <?php 
        $texto = $row_solicitud['resp5'];
       ?>
        <div class="col-xs-4">
         <?php 
          $cadena_buscada = "PRODUCCION";
          $posicion_coincidencia = strpos($texto, $cadena_buscada);
          if($posicion_coincidencia === false){
            echo 'PRODUCCIÓN <input class="form-control" name="resp5[]" type="checkbox" value="PRODUCCION"  readonly>';
          }else{
            echo 'PRODUCCIÓN <input class="form-control" name="resp5[]" type="checkbox" value="PRODUCCION" checked readonly>';
          }
          ?>
        </div>
        <div class="col-xs-4">
         <?php 
          $cadena_buscada = "PROCESAMIENTO";
          $posicion_coincidencia = strpos($texto, $cadena_buscada);
          if($posicion_coincidencia === false){
            echo 'PROCESAMIENTO <input class="form-control" name="resp5[]" type="checkbox" value="PROCESAMIENTO"  readonly>';
          }else{
            echo 'PROCESAMIENTO <input class="form-control" name="resp5[]" type="checkbox" value="PROCESAMIENTO" checked readonly>';
          }
          ?>
        </div>
        <div class="col-xs-4">
         <?php 
          $cadena_buscada = "IMPORTACION";
          $posicion_coincidencia = strpos($texto, $cadena_buscada);
          if($posicion_coincidencia === false){
            echo 'IMPORTACIÓN <input class="form-control" name="resp5[]" type="checkbox" value="IMPORTACION"  readonly>';
          }else{
            echo 'IMPORTACIÓN <input class="form-control" name="resp5[]" type="checkbox" value="IMPORTACION" checked readonly>';
          }
          ?>
        </div>
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <p>6. SELECCIONE SI SUBCONTRATA LOS SERVICIOS DE PLANTAS DE PROCESAMIENTO, EMPRESAS DE COMERCIALIZACIÓN O EMPRESAS QUE REALICEN LA IMPORTACIÓN O EXPORTACIÓN</p>
        <div class="col-xs-6">
          <?php 
            if($row_solicitud['resp6'] == "SI"){
              echo 'SI <input type="radio" class="form-control" name="resp6" onclick="mostrar_empresas()" id="resp6" value="SI" checked>';
            }else{
              echo 'SI <input type="radio" class="form-control" name="resp6" onclick="mostrar_empresas()" id="resp6" value="SI">';
            }
           ?>
        </div>
        <div class="col-xs-6">
          <?php 
            if($row_solicitud['resp6'] == "NO"){
              echo 'NO <input type="radio" class="form-control" name="resp6" onclick="ocultar_empresas()" id="resp6" value="NO" checked>';
            }else{
              echo 'NO <input type="radio" class="form-control" name="resp6" onclick="ocultar_empresas()" id="resp6" value="NO">';
            }
           ?>
        </div>
        <!--<input type="text" class="form-control" name="resp6">-->
      </td>
    </tr>

    <tr >
      <td colspan="2" >
        <p>SI LA RESPUESTA ES AFIRMATIVA, MENCIONE EL NOMBRE Y EL SERVICIO QUE REALIZA</p>
        <div id="contenedor_tablaEmpresas" class="col-xs-12" style="display:block">

          <table class="table table-bordered" id="tablaEmpresas">
            <tr>
              <td>NOMBRE DE LA EMPRESA</td>
              <td>SERVICIO QUE REALIZA</td>

              <!--<td>
                <button type="button" onclick="tablaProductos()" class="btn btn-primary" aria-label="Left Align">
                  <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                </button>
                
              </td>-->          
            </tr>

            <?php 
            $query_empresa_detalle = "SELECT * FROM subEmpresas WHERE idsolicitud_registro = $_GET[idsolicitud]";
            $empresa_detalle = mysql_query($query_empresa_detalle, $dspp) or die(mysql_error());
            $contador = 0;
            while($row_empresa = mysql_fetch_assoc($empresa_detalle)){
              ?>

            <tr>
              <td>
                <input type="text" class="form-control claseModificar" name="resp6_empresa[$contador]" id="exampleInputEmail1" placeholder="Producto" value="<?echo $row_empresa['nombre']?>" readonly>
              </td>
              <td>
                <input type="text" class="form-control claseModificar" name="resp6_servicio[$contador]" id="exampleInputEmail1" placeholder="Servicio" value="<?echo $row_empresa['servicio']?>" readonly>
              </td>
            </tr>

            <?php $contador++; }?>    


          </table>
        </div>    
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <p>7. SI SUBCONTRATA LOS SERVICIOS DE PLANTAS DE PROCESAMIENTO, EMPRESAS DE COMERCIALIZACIÓN O EMPRESAS QUE REALICEN LA IMPORTACIÓN O EXPORTACIÓN, INDIQUE SI ESTAS ESTAN REGISTRADAS O VAN A REALIZAR EL REGISTRO BAJO EL PROGRAMA DEL SPP O SERÁN CONTROLADAS A TRAVÉS DE SU EMPRESA. <sup>5</sup></p>
        
        <textarea name="resp7" id="" class="claseModificar form-control" value="" readonly><?php echo $row_solicitud['resp7']; ?></textarea>
        <p><small><sup>5</sup> Revisar el documento de "Directrices Generales del Sistema SPP".</small></p>
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <p>8. ADICIONAL A SUS OFICINAS CENTRALES, ESPECIFIQUE CUÁNTOS CENTROS DE ACOPIO, AREAS DE      PROCESAMIENTO U OFICINAS ADICIONALES TIENE.</p>
        
        <textarea name="resp8" id="" class="claseModificar form-control" value="" readonly><?php echo $row_solicitud['resp8']; ?></textarea>
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <p>9. EN CASO DE TENER CENTROS DE ACOPIO, ÁREAS DE PROCESAMIENTO U OFICINAS ADICIONALES,  ANEXAR UN CROQUIS GENERAL MOSTRANDO SU UBICACIÓN</p>
        <textarea name="resp9" id="" class="claseModificar form-control" value="" readonly><?php echo $row_solicitud['resp9']; ?></textarea>
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <p>10.  CUENTA CON UN SISTEMA DE CONTROL INTERNO PARA DAR CUMPLIMIENTO A LOS CRITERIOS DE LA NORMA GENERAL DEL SÍMBOLO DE PEQUEÑOS PRODUCTORES, EN SU CASO EXPLIQUE.</p>
        <textarea name="resp10" id="" class="claseModificar form-control" value="" readonly><?php echo $row_solicitud['resp10']; ?></textarea>
      </td>
    </tr>
   <tr>
      <td colspan="2">
        <p>11.  LLENAR LA TABLA DE ACUERDO A LAS CERTIFICACIONES QUE TIENE, (EJEMPLO: EU, NOP, JASS, FLO, etc)</p>
        <!--<table class="table table-bordered" id="tablaCertificaciones">
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
            <td><input type="date" class="form-control" name="ano_inicial[0]" id="exampleInputEmail1" placeholder="AÑO INICIAL"></td>
            <td>
              <div class="col-xs-6">SI<input type="radio" class="form-control" name="interrumpida[0]" value="SI"></div>
              <div class="col-xs-6">NO<input type="radio" class="form-control" name="interrumpida[0]" value="NO"></div>
            </td>
          </tr>

        </table> -->
        <table class="table table-bordered" id="tablaCertificaciones">
          <tr>
            <td>CERTIFICACIÓN</td>
            <td>CERTIFICADORA</td>
            <td>AÑO INICIAL DE CERTIFICACIÓN?</td>
            <td>¿HA SIDO INTERRUMPIDA?</td> 
            <!--<td>
              <button type="button" onclick="tablaCertificaciones()" class="btn btn-primary" aria-label="Left Align">
                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
              </button>
              
            </td>-->
          </tr>

          <?php 
          $query_certificacion_detalle = "SELECT * FROM certificaciones WHERE idsolicitud_registro = $_GET[idsolicitud]";
          $certificacion_detalle = mysql_query($query_certificacion_detalle, $dspp) or die(mysql_error());
          $contador = 0;
          while($row_certificacion = mysql_fetch_assoc($certificacion_detalle)){
            ?>
            <tr class="text-center">
              <td><input type="text" class="form-control claseModificar" name="certificacion[$contador]" id="exampleInputEmail1" placeholder="CERTIFICACIÓN" value="<?echo $row_certificacion['certificacion']?>" readonly></td>
              <td><input type="text" class="form-control claseModificar" name="certificadora[$contador]" id="exampleInputEmail1" placeholder="CERTIFICADORA" value="<?echo $row_certificacion['certificadora']?>" readonly></td>
              <td><input type="text" class="form-control claseModificar" name="ano_inicial[$contador]" id="exampleInputEmail1" placeholder="AÑO INICIAL" value="<?echo $row_certificacion['ano_inicial']?>" readonly></td>
              <td><input type="text" class="form-control claseModificar" name="interrumpida[$contador]" id="exampleInputEmail1" placeholder="¿HA SIDO INTERRUMPIDA?" value="<?echo $row_certificacion['interrumpida']?>" readonly></td>
            </tr>
          <?php $contador++; } ?> 
          
   
        </table>
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <p>12.-  DE LAS CERTIFICACIONES CON LAS QUE CUENTA, EN SU MÁS RECIENTE EVALUACIÓN INTERNA Y EXTERNA, ¿CUÁNTOS INCUMPLIMIENTOS SE IDENTIFICARON? Y EN SU CASO, ¿ESTÁN RESUELTOS O CUÁL ES SU ESTADO?</p>
        <textarea name="resp12" id="" class="claseModificar form-control" value="" readonly><?php echo $row_solicitud['resp12']; ?></textarea>
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <p>13.- DEL TOTAL DE SU COMERCIALIZACIÓN EL CICLO PASADO, ¿QUÉ PORCENTAJE FUERON REALIZADAS BAJO LOS ESQUEMAS CERTIFICADOS DE ORGÁNICO, COMERCIO JUSTO Y/O SÍMBOLO DE PEQUEÑOS PRODUCTORES? </p>
        <textarea name="resp13" id="" class="claseModificar form-control" value="" readonly><?php echo $row_solicitud['resp13']; ?></textarea>
      </td>
    </tr>

      <td colspan="2" name="tablaOculta">
        <p>14.- TUVO COMPRAS SPP DURANTE EL CICLO DE REGISTRO ANTERIOR?</p>
        <?php
          if($row_solicitud['resp14'] == 'SI'){
              //echo "SI <input type='radio' name='op_resp14'  checked readonly>";
            /*echo "</div>";
            echo "<div class='col-xs-6'>";
              echo "<p class='text-center alert alert-danger'>NO</p>";
              echo "NO <input type='radio' name='op_resp14'  readonly>";
            echo "</div>";*/
        ?>
          <div class="col-xs-6">
            <p class='text-center alert alert-success'><span class='glyphicon glyphicon-ok' aria-hidden='true'></span> SI</p>
          </div>
          <div class="col-xs-6">
            <?php 
              if(empty($row_solicitud['resp14_15'])){
             ?>
              <p class="alert alert-danger">No se proporciono ninguna respuesta.</p>
            <?php 
              }else if($row_solicitud['resp14_15'] == "HASTA $3,000 USD"){
             ?>
              <p class="alert alert-info">HASTA $3,000 USD</p>
            <?php 
              }else if($row_solicitud['resp14_15'] == "ENTRE $3,000 Y $10,000 USD"){
             ?>
             <p class="alert alert-info">ENTRE $3,000 Y $10,000 USD</p>
            <?php 
              }else if($row_solicitud['resp14_15'] == "ENTRE $10,000 A $25,000 USD"){
             ?>
             <p class="alert alert-info">ENTRE $10,000 A $25,000 USD</p>
            <?php 
              }else if($row_solicitud['resp14_15'] != "HASTA $3,000 USD" && $row_solicitud['resp14_15'] != "ENTRE $3,000 Y $10,000 USD" && $row_solicitud['resp14_15'] != "ENTRE $10,000 A $25,000 USD"){
             ?>
             <p class="alert alert-info"><?php echo $row_solicitud['resp14_15']; ?></p>
             
            <?php 
              }
             ?>
          </div>
        <?php
          }else if($row_solicitud['resp14'] == 'NO'){
        ?>
          <div class="col-xs-12">
            <p class='text-center alert alert-danger'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span> NO</p>
          </div>
        
        <?php         
          }
        ?>
      </td>

    </tr>
    <tr>
      <td colspan="2">
        <p>16.- FECHA ESTIMADA PARA COMENZAR A USAR EL SÍMBOLO DE PEQUEÑOS PRODUCTOR</p>
        <textarea name="resp16" id="" class="claseModificar form-control" value="" readonly><?php echo $row_solicitud['resp16']; ?></textarea>
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

            <!--<td>
              <button type="button" onclick="tablaProductos()" class="btn btn-primary" aria-label="Left Align">
                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
              </button>
              
            </td>-->          
          </tr>

          <?php 
          $query_producto_detalle = "SELECT * FROM productos WHERE idsolicitud_registro = $_GET[idsolicitud]";
          $producto_detalle = mysql_query($query_producto_detalle, $dspp) or die(mysql_error());
          $contador = 0;
          while($row_producto = mysql_fetch_assoc($producto_detalle)){
            ?>

          <tr>
            <td>
              <textarea name="producto[$contador]" id="" class="form-control" readonly><?php echo $row_producto['producto']; ?></textarea>
            </td>
            <td>
              <textarea name="volumenEstimado[$contador]" id="" class="form-control" readonly><?php echo $row_producto['volumenEstimado']; ?></textarea>
            </td>
            <td>
              <textarea name="volumenTerminado[$contador]" id="" class="form-control" readonly><?php echo $row_producto['volumenTerminado']; ?></textarea>
            </td>
            <td>
              <textarea name="materia[$contador]" id="" class="form-control" readonly><?php echo $row_producto['materia']; ?></textarea>
            </td>
            <td>
              <textarea class="form-control" name="paisOrigen[$contador]" id="exampleInputEmail1" placeholder="Origen" readonly><?echo $row_producto['origen']?></textarea>
            </td>         
            <td>
              <textarea class="form-control" name="paisDestino[$contador]" id="exampleInputEmail1" placeholder="Destino" readonly><?echo $row_producto['destino']?></textarea>
            </td>

          </tr>

          <?php $contador++; }?>    
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
        <p>1. Con el envío de esta solicitud se manifiesta el interés de recibir una propuesta de Registro.</p>
        <p>2. El proceso de Registro comenzará en el momento que se confirme la recepción del pago correspondiente.</p>
        <p>3. La entrega y recepción de esta solicitud no garantiza que el proceso de Registro será positivo.</p>
        <p>4. Conocer y dar cumplimiento a todos los requisitos de la Norma General del Símbolo de Pequeños Productores que le apliquen como Compradores, Comercializadoras Colectiva de Organizaciones de Pequeños Productores, Intermediarios y Maquiladores, tanto Críticos como Mínimos, independientemente del tipo de evaluación que se realice.</p>
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <p>Nombre de la persona que se responsabiliza de la veracidad de la información del formato y que le dará seguimiento a la solicitud de parte del Solicitante:</p>
        <input type="text" name="responsable" class="claseModificar form-control" value="<?php echo $row_solicitud['responsable'] ?>" readonly>
      </td>
    </tr>
    <tr style="background-color:#ccc">
      <td colspan="2">
        <p>Nombre del personal del OC, que recibe la solicitud</p>
        <input type="text" name="nombreOC" class="form-control" value="<?php echo $row_solicitud['nombreOC'] ?>" readonly>
      </td>
    </tr>
    <tr>
      <td style="border:hidden">
        <div class="col-xs-12">
          <input type="hidden" name="MM_update" value="form1">
        <!--  <input type="hidden" name="fecha_elaboracion" value="<?php echo time()?>">
          <input type="hidden" name="status_publico" value="<?php echo $estadoPublico;?>">
          <input type="hidden" name="status_interno" value="<?php echo $estadoInterno;?>">
          <input type="hidden" name="mensaje" value="Acción agregada correctamente" />
          <input type="hidden" name="idcom" value="<?php echo $row_com['idcom']; ?>">
          <input type="hidden" name="idoc" value="<?php echo $_SESSION['idoc']; ?>">
          <input type="hidden" name="idsolicitud_registro" value="<?php echo $row_com['idsolicitud_registro']; ?>">
          <input type="hidden" name="abreviacion" value="<?php echo $row_com['abreviacion'];?>">
          <input type="hidden" name="nombreCOM" value="<?php echo $row_com['nombre']; ?>">
          <input type="hidden" name="paisCOM" value="<?php echo $row_com['pais']; ?>">-->
          <input type="hidden" name="fecha_cotizacion" value="<?php echo time()?>">
          <input type="hidden" name="idoc" value="<?php echo $_SESSION['idoc']?>">
          <input type="hidden" name="idsolicitud_registro" value="<?php echo $_GET['idsolicitud']?>">
          <input type="hidden" name="idcom" value="<?php echo $row_com['idcom'];?>">
          <input type="hidden" name="idsolicitud_certificacion" value="<?php echo $_GET['idsolicitud']?>">  
          <input type="hidden" name="abreviacionCOM" value="<?php echo $row_com['abreviacion'];?>">


        </div>

          <!--<button style="width:200px;" class="btn btn-primary" type="submit" value="Enviar Solicitud" aria-label="Left Align" onclick="return validar()">
            <span class="glyphicon glyphicon-open-file" aria-hidden="true"></span> Enviar
          </button>-->

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

    cell1.innerHTML = '<input type="text" class="form-control claseModificar" name="certificadora['+contador+']" id="exampleInputEmail1" placeholder="CERTIFICACIÓN">';
    cell2.innerHTML = '<input type="text" class="form-control claseModificar" name="certificacion['+contador+']" id="exampleInputEmail1" placeholder="CERTIFICADORA">';
    cell3.innerHTML = '<input type="text" class="form-control claseModificar" name="ano_inicial['+contador+']" id="exampleInputEmail1" placeholder="AÑO INICIAL">';
    cell4.innerHTML = '<input type="text" class="form-control claseModificar" name="interrumpida['+contador+']" id="exampleInputEmail1" placeholder="¿HA SIDO INTERRUMPIDA?">';   
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

    

    cell1.innerHTML = '<input type="text" class="form-control claseModificar" name="producto['+cont+']" id="exampleInputEmail1" placeholder="Producto">';
    
    cell2.innerHTML = '<input type="text" class="form-control claseModificar" name="volumen['+cont+']" id="exampleInputEmail1" placeholder="Volumen">';
    
    cell3.innerHTML = 'SI <input type="radio" name="terminado'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="terminado'+cont+'['+cont+']" id="" value="NO">';
    
    cell4.innerHTML = '<input type="text" class="form-control claseModificar" name="materia['+cont+']" id="exampleInputEmail1" placeholder="Materia">';
    
    cell5.innerHTML = '<input type="text" class="form-control claseModificar" name="destino['+cont+']" id="exampleInputEmail1" placeholder="Destino">';
    
    cell6.innerHTML = 'SI <input type="radio" name="marca_propia'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="marca_propia'+cont+'['+cont+']" id="" value="NO">';
    
    cell7.innerHTML = 'SI <input type="radio" name="marca_cliente'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="marca_cliente'+cont+'['+cont+']" id="" value="NO">';
    
    cell8.innerHTML = 'SI <input type="radio" name="sin_cliente'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="sin_cliente'+cont+'['+cont+']" id="" value="NO">';   

    }

  } 

</script>

</div>
<?php
mysql_free_result($com);

mysql_free_result($cta_bn);

mysql_free_result($contacto);

mysql_free_result($oc);

mysql_free_result($pais);

mysql_free_result($contacto_detail);

mysql_free_result($cta_bn_detail);

mysql_free_result($accion_detalle);

mysql_free_result($accion_detail);

mysql_free_result($accion_lateral);
?>
