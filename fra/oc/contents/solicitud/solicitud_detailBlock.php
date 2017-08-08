<?php require_once('../Connections/dspp.php'); 
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
$query_accion_detalle = sprintf("SELECT solicitud_certificacion.*, oc.idoc, oc.nombre AS 'nombreOC' FROM solicitud_certificacion INNER JOIN oc ON solicitud_certificacion.idoc = oc.idoc WHERE idsolicitud_certificacion = %s", GetSQLValueString($colname_accion_detalle, "int"));
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
$query_accion_lateral = "SELECT idultima_accion, idopp, ultima_accion FROM ultima_accion ORDER BY fecha DESC";
$accion_lateral = mysql_query($query_accion_lateral, $dspp) or die(mysql_error());
$row_accion_lateral = mysql_fetch_assoc($accion_lateral);
$totalRows_accion_lateral = mysql_num_rows($accion_lateral);




$colname_opp = "-1";
 
$colname_opp = $_GET['idsolicitud'];




$query_opp = sprintf("SELECT opp.* ,solicitud_certificacion.* FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE solicitud_certificacion.idsolicitud_certificacion = %s ORDER BY solicitud_certificacion.fecha_elaboracion DESC", GetSQLValueString($colname_opp, "int"));


$opp = mysql_query($query_opp, $dspp) or die(mysql_error());
$row_opp = mysql_fetch_assoc($opp);
$totalRows_opp = mysql_num_rows($opp);

$colname_cta_bn = "-1";
if (isset($_GET['idopp'])) {
  $colname_cta_bn = $_GET['idopp'];
}
$query_cta_bn = sprintf("SELECT * FROM cta_bn WHERE idopp = %s", GetSQLValueString($colname_cta_bn, "int"));
$cta_bn = mysql_query($query_cta_bn, $dspp) or die(mysql_error());
//$row_cta_bn = mysql_fetch_assoc($cta_bn);
$totalRows_cta_bn = mysql_num_rows($cta_bn);

$colname_contacto = "-1";
if (isset($_GET['idopp'])) {
  $colname_contacto = $_GET['idopp'];
}
$query_contacto = sprintf("SELECT * FROM contacto WHERE idopp = %s ORDER BY tipo ASC, contacto asc", GetSQLValueString($colname_contacto, "int"));
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
$correoOPP1 = $row_solicitud['p1_email'];
$correoOPP2 = $row_solicitud['p2_email'];
$correoOculto = "yasser.midnight@gmail.com";
/////////////////////////////////////////////////////////////////////////

/******************************************* VARIABLES DE CONTROL ******************************************/

/************************* SE APRUEBA Y ENVIAN LAS COTIZACIONES***********************************/
if(isset($_POST['cotizacion'])){
  $fecha_actual = time();
  $idopp = $_POST['idopp'];
  $idoc = $_POST['idoc'];
  $status = "17";

  $rutaArchivo = "../../archivos/ocArchivos/cotizaciones/";
  $procedimiento = $_POST['procedimiento'];

  if(!empty($_FILES['cotizacion_opp']['name'])){
      $_FILES["cotizacion_opp"]["name"];
        move_uploaded_file($_FILES["cotizacion_opp"]["tmp_name"], $rutaArchivo.date("Ymd H:i:s")."_".$_FILES["cotizacion_opp"]["name"]);
        $cotizacion_opp = $rutaArchivo.basename(date("Ymd H:i:s")."_".$_FILES["cotizacion_opp"]["name"]);
  }else{
    $cotizacion_opp = NULL;
  }
  if(!empty($_FILES['cotizacion_adm']['name'])){
      $_FILES["cotizacion_adm"]["name"];
        move_uploaded_file($_FILES["cotizacion_adm"]["tmp_name"], $rutaArchivo.date("Ymd H:i:s")."_".$_FILES["cotizacion_adm"]["name"]);
        $cotizacion_adm = $rutaArchivo.basename(date("Ymd H:i:s")."_".$_FILES["cotizacion_adm"]["name"]);
  }else{
    $cotizacion_adm = NULL;
  }
    if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {

        $updateSQL = "UPDATE solicitud_certificacion SET 
          idoc= $_POST[idoc], 
          cotizacion_opp = '$cotizacion_opp',
          cotizacion_adm = '$cotizacion_adm',
          status= '$status',
          procedimiento = '$procedimiento'
          WHERE idsolicitud_certificacion = $_POST[idsolicitud_certificacion]";

          mysql_select_db($database_dspp, $dspp);
          $Result1 = mysql_query($updateSQL, $dspp) or die(mysql_error());
          
          /*$insertGoTo = "main_menu.php?SOLICITUD&select";
          if (isset($_SERVER['QUERY_STRING'])) {
            $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
            $insertGoTo .= $_SERVER['QUERY_STRING'];
          }
          header(sprintf("Location: %s", $insertGoTo));*/



            $nombre = $_POST['nombreOPP'];
            $abreviacion = $_POST['abreviacionOPP'];
            $fecha = date("d/m/Y", $_POST['fecha_cotizacion']);
            $paisOPP = $_POST['pais'];
        

            //$correo = $_POST['p1_email'];
            //$correo = $_POST['p2_email'];

            $asunto = "D-SPP Cotización Certificación para Organizaciones de Pequeños Productores"; 

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
                  <td aling="left" style="text-align:justify">Se ha enviado la cotización correspondiente a la Solicitud de Certificación para Organizaciones de Pequeños Productores.
                  <br><br> Por favor iniciar sesión en el siguiente enlace <a href="http://d-spp.org/?OPP">www.d-spp.org/?OPP</a> , para poder acceder a la cotización.</td>
                </tr>

                <tr>
                  <td colspan="2">
                    <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;" border="1" width="650px">
                      <tbody>
                        <tr style="font-size: 12px; text-align:center; background-color:#dff0d8; color:#3c763d;" height="50px;">
                          <td width="130px">Nombre de la organización/Organization name</td>
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
                            '.$paisOPP.'
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

        $mail->AddAddress($correoOPP1);
        $mail->AddAddress($correoOPP2);
        $mail->AddAttachment($cotizacion_opp);
        //$mail->Username = "soporte@d-spp.org";
        //$mail->Password = "/aung5l6tZ";
        $mail->Subject = utf8_decode($asunto);
        $mail->Body = utf8_decode($cuerpo);
        $mail->MsgHTML(utf8_decode($cuerpo));
        $mail->Send();
        $mail->ClearAddresses();


            // SE INSERTA LA FECHA EN LA QUE SE ENVIO LA COTIZACION

            $idexterno = $_POST['idsolicitud_certificacion'];
            $identificador = "SOLICITUD";
        
            $queryFecha = "INSERT INTO fecha(fecha, idexterno, idopp, idoc, identificador, status) VALUES($fecha_actual, $idexterno, $idopp, $idoc, '$identificador', $status)";
            $ejecutar = mysql_query($queryFecha,$dspp) or die(mysql_error());
          
        $queryMensaje = "INSERT INTO mensajes(idopp, idoc, asunto, mensaje, destinatario, remitente, fecha) VALUES($idopp, $idoc, '$asunto', '$cuerpo', 'OPP', 'OC', $fecha_actual)";
        $ejecutar = mysql_query($queryMensaje,$dspp) or die(mysql_error());

          /*$insertGoTo = "oc/main_menu.php?SOLICITUD&select&mensaje=Cotización agregada correctamente, se ha notificado al OC por email.";
          if (isset($_SERVER['QUERY_STRING'])) {
            $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
            $insertGoTo .= $_SERVER['QUERY_STRING'];
          }
          header(sprintf("Location: %s", $insertGoTo));*/

          echo "<script>window.location='main_menu.php?SOLICITUD&select&mensaje=Cotización agregada correctamente, se ha notificado al OPP por email.'</script>"; 
    }

}
/************************* FIN SE APRUEBA Y ENVIAN LAS COTIZACIONES***********************************/

/************************* INICIO SE DENIEGA Y ENVIAR A REVISION***********************************/

if(isset($_POST['denegado'])){
  $estado_interno = "2";
  $idopp = $_POST['idopp'];

    if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {

      $updateSQL = "UPDATE solicitud_certificacion SET 
        idoc= $_POST[idoc], 
        status= '$estado_interno' 
        WHERE idsolicitud_certificacion= $_POST[idsolicitud_certificacion]";

        mysql_select_db($database_dspp, $dspp);
        $Result1 = mysql_query($updateSQL, $dspp) or die(mysql_error());

    }

   
        $nombre = "$row_opp[nombre]";


        $asunto = "D-SPP Modificación Solicitud de Certificación para Organizaciones de Pequeños Productores"; 


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
              <td aling="left" style="text-align:justify">"Se han encontrado ciertas anomalias en la Solicitud de Certificación para Organizaciones de Pequeños Productores, por lo tanto se han realizado las observaciones correspondientes.
              <br><br>  Por favor iniciar sesión en el siguiente enlace <a href="http://d-spp.org/?OPP">www.d-spp.org/?OPP</a> , para poder acceder a su solicitud y asi <b>corregir los datos de la misma</b>.</td>
            </tr>

            <tr>
              <td colspan="2">
                <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;" border="1" width="650px">
                  <tbody>
                    <tr style="font-size: 12px; text-align:center; background-color:#dff0d8; color:#3c763d;" height="50px;">
                      <td width="130px">Nombre de la organización/Organization name</td>
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
                        '.$paisOPP.'
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

        $mail->AddAddress($correoOPP1);
        $mail->AddAddress($correoOPP2);

        //$mail->Username = "soporte@d-spp.org";
        //$mail->Password = "/aung5l6tZ";
        $mail->Subject = utf8_decode($asunto);
        $mail->Body = utf8_decode($cuerpo);
        $mail->MsgHTML(utf8_decode($cuerpo));
        $mail->Send();
        $mail->ClearAddresses();


        $queryMensaje = "INSERT INTO mensajes(idopp, idoc, asunto, mensaje, destinatario, remitente, fecha) VALUES($idopp, $idoc, $asunto', '$cuerpo', 'OPP', 'OC', $fecha_actual)";
        $ejecutar = mysql_query($queryMensaje,$dspp) or die(mysql_error());

}
/************************* FIN SE DENIEGA Y ENVIA A REVISION***********************************/

/************************* INICIO PARA GUARDAR LOS CAMBIOS ***********************************/

if(isset($_POST['guardarCambios']) && $_POST['guardarCambios'] == "guardarCambios"){

    if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {



      $array_resp4 = NULL;
      $op_resp13 = NULL;
      $op_resp13_1 = NULL;
      $croquis = NULL;
      if(!empty($_POST['op_resp15'])){
        $croquis = $_POST['op_resp15'];
      }
      if(!empty($_FILES["op_resp15_1"]["name"])){
        $rutaArchivo = "../../archivos/oppArchivos/croquis/";

        $_FILES["op_resp15_1"]["name"];
          move_uploaded_file($_FILES["op_resp15_1"]["tmp_name"], $rutaArchivo.date("Ymd H:i:s")."_".$_FILES["op_resp15_1"]["name"]);
          $croquis = $rutaArchivo.basename(date("Ymd H:i:s")."_".$_FILES["op_resp15_1"]["name"]);
      }



      
      if(!empty($_POST['op_resp4'])){
        $resp4 = $_POST['op_resp4'];

        for ($i=0; $i < count($resp4) ; $i++) { 
          $array_resp4 .= $resp4[$i]." - ";
        }
      }else{
          $array_resp4 = NULL;
      }

      if(isset($_POST['op_resp13'])){
        if(isset($_POST['op_resp13']) && $_POST['op_resp13'] == "mayor"){
          $op_resp13 = $_POST['op_resp13_1'];
        }else{
          $op_resp13 = $_POST['op_resp13'];
        }
      }else{
        $op_resp13 = NULL;
      }

      if(isset($_POST['op_resp12'])){
        $op_resp12 = $_POST['op_resp12'];
      }else{
        $op_resp12 = "";
      }


    $updateSQL = "UPDATE solicitud_certificacion SET 
      idopp= $_POST[idopp], 
      ciudad= '$_POST[ciudad]', 
      ruc= '$_POST[ruc]', 
      p1_nombre= '$_POST[p1_nombre]', 
      p1_cargo= '$_POST[p1_cargo]', 
      p1_telefono= '$_POST[p1_telefono]', 
      p1_email= '$_POST[p1_email]', 
      p2_nombre= '$_POST[p2_nombre]', 
      p2_cargo= '$_POST[p2_cargo]', 
      p2_telefono= '$_POST[p2_telefono]', 
      p2_email= '$_POST[p2_email]', 
      adm_nom1= '$_POST[adm_nom1]', 
      adm_nom2= '$_POST[adm_nom2]', 
      adm_tel1= '$_POST[adm_tel1]', 
      adm_tel2= '$_POST[adm_tel2]', 
      adm_email1= '$_POST[adm_email1]', 
      adm_email2= '$_POST[adm_email2]', 
      resp1= '$_POST[resp1]', 
      resp2= '$_POST[resp2]', 
      resp3= '$_POST[resp3]', 
      resp4= '$_POST[resp4]',
      op_area1 = '$_POST[op_area1]',
      op_area2 = '$_POST[op_area2]',
      op_area3 = '$_POST[op_area3]',
      op_area4 = '$_POST[op_area4]',
      
      op_resp1= '$_POST[op_resp1]',  
      op_resp2= '$_POST[op_resp2]', 
      op_resp3= '$_POST[op_resp3]', 
      op_resp4= '$array_resp4', 
      op_resp5= '$_POST[op_resp5]', 
      op_resp6= '$_POST[op_resp6]', 
      op_resp7= '$_POST[op_resp7]', 
      op_resp8= '$_POST[op_resp8]', 
      op_resp10= '$_POST[op_resp10]', 
      op_resp11= '$_POST[op_resp11]', 
      op_resp12 = '$op_resp12',
      op_resp13 = '$op_resp13',
      op_resp14= '$_POST[op_resp14]', 
      op_resp15= '$croquis' 
      WHERE idsolicitud_certificacion= $_POST[idsolicitud_certificacion]";

      mysql_select_db($database_dspp, $dspp);
      $Result1 = mysql_query($updateSQL, $dspp) or die(mysql_error());



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
      echo "<script>window.location='main_menu.php?SOLICITUD&detailBlock&idsolicitud=$_POST[idsolicitud_certificacion]'</script>"; 



}
/************************* FIN SE GUARDAN LOS CAMBIOS ***********************************/




if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form2")) {
  $updateSQL = "UPDATE solicitud_certificacion SET 
  observaciones= '$_POST[observaciones]' 
  WHERE idsolicitud_certificacion= $_POST[idsolicitud_certificacion]";

  mysql_select_db($database_dspp, $dspp);
  $Result1 = mysql_query($updateSQL, $dspp) or die(mysql_error());


  /*$insertGoTo = "oc/main_menu.php?SOLICITUD&select";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));*/
  echo "<script>window.location='main_menu.php?SOLICITUD&select'</script>"; 


}


/***********************************************************************************************/
/***********************************************************************************************/




?>

<script>
  function validar(){
    valor = document.getElementById("cotizacion_opp").value;
    if( valor == null || valor.length == 0 ) {
      alert("No se ha cargado la cotización de OPP");
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
          <input type="hidden" name="idsolicitud_certificacion" value="<?php echo $_GET['idsolicitud']?>">
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

<?php if(empty($row_solicitud['cotizacion_opp']) && empty($row_solicitud['cotizacion_adm'])){?>
<div class="col-xs-12">
  <div class="panel panel-primary">
    <div class="panel-heading">
      <h3 class="panel-title text-center">Cotizaciones</h3>
    </div>
    <div class="panel-body"><!---- INICIA BODY ----> 
      <div class="row">
        <div class="col-xs-12">
    
          <?php if(empty($row_solicitud['cotizacion_opp'])){ ?>
        
            <div class="col-xs-6">
              <h5>Enviar cotización a <u style="color:red;"><?echo $row_opp['nombre'];?></u></h5>
            </div>
            <div class="col-xs-6">
              <input name="cotizacion_opp" id="cotizacion_opp" type="file" class="filestyle" data-buttonName="btn-info" data-buttonBefore="true" data-buttonText="Cargar Cotización"> 
            </div>
        
          <?php }?>

        </div>

        <!--<div class="col-xs-12">
      
          <?php if(empty($row_solicitud['cotizacion_adm'])){ ?>

            <div class="col-xs-6">
              <h5>Enviar cotización a <u style="color:red;">FUNDEPPO</u> (Opcional)</h5>
            </div>
            <div class="col-xs-6">
              <input name="cotizacion_adm" type="file" class="filestyle" data-buttonName="btn-info" data-buttonBefore="true" data-buttonText="Cargar Cotización">
            </div>
          <?php } ?> 

        </div>-->
      </div>   

    </div><!---- TERMINA BODY ---->

    <div class="panel-footer">
         <?php /*
          if(empty($row_solicitud['cotizacion_opp']) || empty($row_solicitud['cotizacion_adm'])){
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
                <!--<a href="?SOLICITUD&amp;detail&amp;idsolicitud=<?php echo $row_opp['idsolicitud_certificacion']; ?>" class="btn btn-primary">
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
          if(empty($row_solicitud['cotizacion_opp']) || empty($row_solicitud['cotizacion_adm'])){
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
			<th colspan="4" class="text-center"><h3>Solicitud de Certificación para Organizaciones de Pequeños Productores</h3></th>
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
			<td colspan="2">
				NOMBRE COMPLETO DE LA ORGANIZACIÓN DE PEQUEÑOS PRODCUTORES
			</td>
			<td colspan="2">
				<input type="text" autofocus="autofocus" class="form-control" name="nombreOPP" id="exampleInputEmail1" size="70" placeholder="Nombre Organización" value="<?php echo $row_opp['nombre'];?>" readonly>
        <!--<input type="hidden" name="nombreOPP" value="<?php echo $row_opp['nombre'];?>">-->
			</td>
		</tr>
		<tr>
			<td colspan="2">RFC</td>
			<td colspan="2">
				<?php 
					if(isset($row_opp['rfc'])){
						echo "<input type='text' class='form-control' id='exampleInputEmail1' placeholder='RFC' value='$row_opp[rfc]' readonly>";

					}else{
						echo "<input type='text' class='form-control' id='exampleInputEmail1' placeholder='NO DISPONIBLE' readonly>";

					}
				 ?>
			</td>
		</tr>
		<tr>
			<td colspan="3">
				DIRECCIÓN COMPLETA DE SUS OFICINAS CENTRALES(CALLE, BARRIO, LUGAR, REGIÓN)<br>
				<?php 
					if(isset($row_opp['direccion_fiscal'])){
						echo "<input type='text' class='form-control' name='direccion_opp' id='exampleInputEmail1' value='$row_opp[direccion_fiscal]' readonly>";
					}else{
						echo "<input type='text' class='form-control' name='direccion_opp' id='exampleInputEmail1' placeholder='No Disponible' readonly>";
					}
				 ?>

			</td>
			<td colspan="1">
				PAÍS<br>
				<?php if(isset($row_opp['pais'])){
						echo "<input type='text' class='form-control' id='exampleInputEmail1' name='pais' placeholder='Dirección de Oficinas' value='$row_opp[pais]' readonly>";
            }
					else{ ?>
					No Disponible				
		      <?php } ?>
			</td>
		</tr>	
		<tr>
			<td colspan="2">CORREO ELECTRONICO</td>
			<td colspan="2">
				<?php 
					if(isset($row_opp['email'])){
						echo "<input type='email' class='form-control' name='email_opp' id='exampleInputEmail1' value='$row_opp[email]' readonly>";
					}else{
						echo "<input type='email' class='form-control' name='email_opp' id='exampleInputEmail1' placeholder='No Disponible' readonly>";
					}
				 ?>

			</td>
		</tr>
		<tr>
			<td colspan="3">
				SITIO WEB<br>
				<?php 
					if(isset($row_opp['sitio_web'])){
						echo "<input type='text' class='form-control' name='web_opp' id='exampleInputEmail1' value='$row_opp[sitio_web]' readonly>";
					}else{
						echo "<input type='text' class='form-control' name='web_opp' id='exampleInputEmail1' placeholder='No Disponible' readonly>";
					}
				 ?>
				
			</td>
			<td colspan="1">
				TELEFONO<br>
				<?php 
					if(isset($row_opp['telefono1'])){
						echo "<input type='text' class='form-control' name='telefono' id='exampleInputEmail1' value='$row_opp[telefono1]' readonly>";
					}else{
						echo "<input type='text' class='form-control' name='telefono' id='exampleInputEmail1' placeholder='No Disponible' readonly>";
					}
				 ?>
				
			</td>
		</tr>		
		<tr>
			<td class="text-center" colspan="4">
				DATOS FISCALES(PARA FACTURACIÓN COMO DOMICILIO, RFC, RUC, CIUDAD, PAÍS, ETC)<br>
			</td>
		</tr>
		<tr>
			<?php 
				if(isset($row_solicitud['direccion_fiscal'])){
					echo "<td class='col-xs-3'>DOMICILIO: <input type='text' class='form-control' name='f_domicilio' id='exampleInputEmail1' value='$row_solicitud[direccion_fiscal]' readonly></td>";
				}else{
					echo "<td class='col-xs-3'>DOMICILIO: <input type='text' class='form-control' name='f_domicilio' id='exampleInputEmail1' placeholder='No Disponible' readonly></td>";
				}
				if(isset($row_solicitud['rfc'])){
					echo "<td class='col-xs-3'>RFC: <input type='text' class='form-control' name='f_rfc' id='exampleInputEmail1' value='$row_solicitud[rfc]' readonly></td>";
				}else{
					echo "<td class='col-xs-3'>RFC: <input type='text' class='form-control' name='f_rfc' id='exampleInputEmail1' placeholder='No Disponible' readonly></td>";
				}
			 ?>		
			<td class="col-xs-3">RUC: <input type="text" class="form-control" name="ruc" id="exampleInputEmail1" placeholder="RUC" value="<?php echo $row_solicitud['ruc']?>" readonly></td>
			
			<td class="col-xs-3">CIUDAD: <input type="text" class="form-control" name="ciudad" id="exampleInputEmail1" placeholder="Ciudad" value="<?php echo $row_solicitud['ciudad']?>" readonly></td>
		</tr>
		<tr class="text-center warning">
			<td colspan="4">PERSONA(S) DE CONTACTO</td>
		</tr>
		<tr>
			<td colspan="2">
				NOMBRE DE CONTACTO SOLICITUD<br>


				<input type="text" class="form-control claseModificar" name="p1_nombre" id="editar2" placeholder="Contacto Solicitud 1" value="<?php echo $row_solicitud['p1_nombre']?>" readonly>
        <br>
				<input type="text" class="form-control claseModificar" name="p2_nombre" id="editar2" placeholder="Contacto Solicitud 2" value="<?php echo $row_solicitud['p2_nombre']?>" readonly><br>
				CORREO ELECTRÓNICO DE CONTACTO
				<input type="email" class="form-control claseModificar" name="p1_email" id="editar2" placeholder="Correo Electrónico 1" value="<?php echo $row_solicitud['p1_email']?>" readonly><br>
				<input type="email" class="form-control claseModificar" name="p2_email" id="editar2" placeholder="Correo Electrónico 2" value="<?php echo $row_solicitud['p2_email']?>" readonly><br>
			</td>
			<td colspan="2">
				CARGO<br>
				<input type="text" class="form-control claseModificar" name="p1_cargo" id="exampleInputEmail1" placeholder="Cargo 1" value="<?php echo $row_solicitud['p1_cargo']?>" readonly><br>
				<input type="text" class="form-control claseModificar" name="p2_cargo" id="exampleInputEmail1" placeholder="Cargo 2" value="<?php echo $row_solicitud['p2_cargo']?>" readonly><br>
				TELÉFONO<br>
				<input type="text" class="form-control claseModificar" name="p1_telefono" id="exampleInputtext1" placeholder="Telefono 1" value="<?php echo $row_solicitud['p1_telefono']?>" readonly><br>
				<input type="text" class="form-control claseModificar" name="p2_telefono" id="exampleInputEmail1" placeholder="Telefono 2" value="<?php echo $row_solicitud['p2_telefono']?>" readonly><br>
			</td>
		</tr>
		<tr class="text-center warning">
			<td colspan="4">PERSONA DEL ÁREA ADMINISTRATIVA</td>
		</tr>

		<tr>
			<td colspan="2">
				PERSONA DEL ÁREA ADMINISTRATIVA<br>
				<input type="text" class="form-control claseModificar" name="adm_nom1" id="exampleInputEmail1" placeholder="Persona del Área Administrativa 1" value="<?php echo $row_solicitud['adm_nom1']?>" readonly><br>
				<input type="text" class="form-control claseModificar" name="adm_nom2" id="exampleInputEmail1" placeholder="Persona del Área Administrativa 2" value="<?php echo $row_solicitud['adm_nom2']?>" readonly><br>
				CORREO ELECTRÓNICO DEL ÁREA ADMINISTRATIVA
				<input type="email" class="form-control claseModificar" name="adm_email1" id="exampleInputEmail1" placeholder="Correo Electrónico 1" value="<?php echo $row_solicitud['adm_email1']?>" readonly><br>
				<input type="email" class="form-control claseModificar" name="adm_email2" id="exampleInputEmail1" placeholder="Correo Electrónico 2" value="<?php echo $row_solicitud['adm_email2']?>" readonly>
			</td>
			<td colspan="2">
				TELÉFONO PERSONA DEL ÁREA ADMINISTRATIVA<br>
				<input type="text" class="form-control claseModificar" name="adm_tel1" id="exampleInputEmail1" placeholder="Teléfono Área Adminsitrativa 1" value="<?php echo $row_solicitud['adm_tel1']?>" readonly><br>
				<input type="text" class="form-control claseModificar" name="adm_tel2" id="exampleInputEmail1" placeholder="Teléfono Área Administrativa 2" value="<?php echo $row_solicitud['adm_tel2']?>" readonly>
			</td>
		</tr>	
		<tr >
			<td>NÚMERO DE SOCIOS PRODUCTORES</td>
			<td>
        <input type="text" class="form-control claseModificar" name="resp1" id="exampleInputEmail1" placeholder="Número de socios" value="<?php echo $row_solicitud['resp1']?>" readonly>
      </td>
			<td>NÚMERO DE SOCIOS PRODUCTORES DEL (DE LOS) PRODUCTO(S) A INCLUIR EN LA CERTIFICACION</td>
			<td>
        <input type="text" class="form-control claseModificar" name="resp2" id="exampleInputEmail1" placeholder="Número de socios" value="<?php echo $row_solicitud['resp2']?>" readonly>
      </td>
		</tr>

		<tr >
			<td>VOLUMEN(ES) DE PRODUCCIÓN TOTAL POR PRODUCTO (UNIDAD DE MEDIDA):</td>
			<td>
        <input type="text" class="form-control claseModificar" name="resp3" id="exampleInputEmail1" placeholder="Número de socios" value="<?php echo $row_solicitud['resp3']?>" readonly>
      </td>
			<td>TAMAÑO MÁXIMO DE LA UNIDAD DE PRODUCCIÓN POR PRODUCTOR DEL (DE LOS) PRODUCTO(S) A INCLUIR EN LA CERTIFICACIÓN:</td>
			<td>
        <input type="text" class="form-control claseModificar" name="resp4" id="exampleInputEmail1" placeholder="Número de socios" value="<?php echo $row_solicitud['resp4']?>" readonly>
      </td>
		</tr>
		<tr class="success">
			<th colspan="4" class="text-center">DATOS DE OPERACIÓN</th>
		</tr>
		<tr>
			<td colspan="4">
				1. EXPLIQUE SI SE TRATA DE UNA ORGANIZACIÓN DE PEQUEÑOS PRODUCTORES DE 1ER, 2DO, 3ER O 4TO GRADO, ASÍ COMO EL NÚMERO DE OPP DE 3ER, 2DO O 1ER GRADO, Y EL NÚMERO DE COMUNIDADES, ZONAS O GRUPOS DE TRABAJO, EN SU CASO, CON LAS QUE CUENTA:
				<br>
				<textarea class="form-control claseModificar" name="op_resp1" id="" rows="3" readonly><?php echo $row_solicitud['op_resp1']?></textarea>
				
			</td>
		</tr>
		<tr>
			<td>
				<h5 class="col-xs-12">NÚMERO DE OPP DE 3ER GRADO:</h5>
				<textarea class="col-xs-12 form-control claseModificar" name="op_area1" id="" cols="10" rows="5" readonly><?php echo $row_solicitud['op_area1']?></textarea>
				
			</td>
			<td>
				<h5 class="col-xs-12">NÚMERO DE OPP DE 2DO GRADO:</h5>	
				<textarea class="col-xs-12 form-control claseModificar" name="op_area2" id="" cols="10" rows="5" readonly><?php echo $row_solicitud['op_area2']?></textarea>
			</td>
			<td>
				<h5 class="col-xs-12">NÚMERO DE OPP DE 1ER GRADO:</h5>
				<textarea class="col-xs-12 form-control claseModificar" name="op_area3" id="" cols="10" rows="5" readonly><?php echo $row_solicitud['op_area3']?></textarea>
			</td>
			<td>
				<h5 class="col-xs-12">NÚMERO DE COMUNIDADES, ZONAS O GRUPOS DE TRABAJO:</h5>
				<textarea class="col-xs-12 form-control claseModificar" name="op_area4" id="" cols="10" rows="5" readonly><?php echo $row_solicitud['op_area4']?></textarea>
				
			</td>
		</tr>
		<tr>
			<td colspan="4">
				2. ESPECIFIQUE QUÉ PRODUCTO(S) QUIERE INCLUIR EN EL CERTIFICADO DEL SÍMBOLO DE PEQUEÑOS PRODUCTORES PARA LOS CUALES EL ORGANISMO DE CERTIFICACIÓN REALIZARÁ LA EVALUACIÓN.
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<textarea name="op_resp2" id="" class="form-control claseModificar" rows="3" readonly><?php echo $row_solicitud['op_resp2']?></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				3. MENCIONE SI SU ORGANIZACIÓN QUIERE INCLUIR ALGÚN CALIFICATIVO ADICIONAL PARA USO COMPLEMENTARIO CON EL DISEÑO GRÁFICO DEL SÍMBOLO DE PEQUEÑOS PRODUCTORES.<sup>4</sup>
				<br>
				<h6><sup>4</sup> Revisar el Reglamento Gráfico y la lista de Calificativos Complementarios opcionales vigentes.</h6>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<textarea name="op_resp3" id="" class="form-control claseModificar" rows="3" readonly><?php echo $row_solicitud['op_resp3']?></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				4. SELECCIONE EL ALCANCE QUE TIENE LA ORGANIZACIÓN DE PEQUEÑOS PRODUCTORES:
			</td>
		</tr>
		<tr>
<?php 
$texto = $row_solicitud['op_resp4'];
 ?>

      <td colspan="4">
        <div class="col-xs-4">
          <?php 
$cadena_buscada   = 'PRODUCCION';
$posicion_coincidencia = strpos($texto, $cadena_buscada);

            if($posicion_coincidencia === false){
              echo "PRODUCCIÓN <input name='op_resp4[]' type='checkbox' value='PRODUCCION' readonly>";
            }else{
              echo "PRODUCCIÓN <input name='op_resp4[]' type='checkbox' value='PRODUCCION' checked readonly>";
            } 
          ?>
          
        </div>
        <div class="col-xs-4">
          <?php 
$cadena_buscada   = 'PROCESAMIENTO';
$posicion_coincidencia = strpos($texto, $cadena_buscada);

            if($posicion_coincidencia === false){
              echo "PROCESAMIENTO <input name='op_resp4[]' type='checkbox' value='PROCESAMIENTO' readonly>";
            }else{
              echo "PROCESAMIENTO <input name='op_resp4[]' type='checkbox' value='PROCESAMIENTO' checked readonly>";
            } 
          ?>
        </div>
        <div class="col-xs-4">
          <?php
$cadena_buscada   = 'EXPORTACION';
$posicion_coincidencia = strpos($texto, $cadena_buscada);

            if($posicion_coincidencia === false){
              echo "EXPORTACIÓN <input name='op_resp4[]' type='checkbox' value='EXPORTACION' readonly>";
            }else{
              echo "EXPORTACIÓN <input name='op_resp4[]' type='checkbox' value='EXPORTACION' checked readonly>";
            } 
          ?>          
        </div>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				5. ESPECIFIQUE SI SUBCONTRATA LOS SERVICIOS DE PLANTAS DE PROCESAMIENTO, EMPRESAS DE COMERCIALIZACIÓN O EMPRESAS QUE REALICEN LA IMPORTACIÓN O EXPORTACIÓN, SI LA RESPUESTA ES AFIRMATIVA, MENCIONE EL NOMBRE Y EL SERVICIO QUE REALIZA.
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<textarea class="form-control claseModificar" name="op_resp5" id="" rows="3" readonly><?php echo $row_solicitud['op_resp5']?></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				6. SI SUBCONTRATA LOS SERVICIOS DE PLANTAS DE PROCESAMIENTO, EMPRESAS DE COMERCIALIZACIÓN O EMPRESAS QUE REALICEN LA IMPORTACIÓN O EXPORTACIÓN, INDIQUE SI ESTAS EMPRESAS VAN A REALIZAR EL REGISTRO BAJO EL PROGRAMA DEL SPP O SERÁN CONTROLADAS A TRAVÉS DE LA ORGANIZACIÓN DE PEQUEÑOS PRODUCTORES.<sup>5</sup>
				<br>
				<h6><sup>5</sup> Revisar el documento de 'Directrices Generales del Sistema SPP' en su última versión.</h6>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<textarea class="form-control claseModificar" name="op_resp6" id="" rows="3" readonly><?php echo $row_solicitud['op_resp6']?></textarea>
			</td>
		</tr>		
		<tr>
			<td colspan="4">
				7. ADICIONAL A SUS OFICINAS CENTRALES, ESPECIFIQUE CUÁNTOS CENTROS DE ACOPIO, ÁREAS DE PROCESAMIENTO U OFICINAS ADICIONALES TIENE.
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<textarea class="form-control claseModificar" name="op_resp7" id="" rows="3" readonly><?php echo $row_solicitud['op_resp7']?></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				8. ¿CUENTA CON UN SISTEMA DE CONTROL INTERNO PARA DAR CUMPLIMIENTO A LOS CRITERIOS DE LA NORMA GENERAL DEL SÍMBOLO DE PEQUEÑOS PRODUCTORES?, EN SU CASO, EXPLIQUE.
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<textarea class="form-control claseModificar" name="op_resp8" id="" rows="3" readonly><?php echo $row_solicitud['op_resp8']?></textarea>
			</td>
		</tr>	
		<tr>
			<td colspan="4">
				9. LLENAR LA TABLA DE ACUERDO A LAS CERTIFICACIONES QUE TIENE, (EJEMPLO: EU, NOP, JASS, FLO, etc).
			</td>
		</tr>
		<tr>


			<td colspan="4">
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
          $query_certificacion_detalle = "SELECT * FROM certificaciones WHERE idsolicitud_certificacion = $_GET[idsolicitud]";
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
			<td colspan="4">
				10.DE LAS CERTIFICACIONES CON LAS QUE CUENTA, EN SU MÁS RECIENTE EVALUACIÓN INTERNA Y EXTERNA, ¿CUÁNTOS INCUMPLIMIENTOS SE IDENTIFICARON? Y EN SU CASO, ¿ESTÁN RESUELTOS O CUÁL ES SU ESTADO?
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<textarea class="form-control claseModificar" name="op_resp10" id="" rows="3" readonly><?php echo $row_solicitud['op_resp10']?></textarea>
			</td>
		</tr>	
		<tr>
			<td colspan="4">
				11.DEL TOTAL DE SUS VENTAS ¿QUÉ PORCENTAJE DEL PRODUCTO CUENTA CON LA CERTIFICACIÓN DE ORGÁNICO, COMERCIO JUSTO Y/O SÍMBOLO DE PEQUEÑOS PRODUCTORES?
			</td>
		</tr>	
		<tr>
			<td colspan="4">
				<textarea class="form-control claseModificar" name="op_resp11" id="" rows="3" readonly><?php echo $row_solicitud['op_resp11']?></textarea>
			</td>
		</tr>	
		<tr>
			<td colspan="4">
				12. ¿TUVO VENTAS SPP DURANTE EL CICLO DE CERTIFICACIÓN ANTERIOR?
			</td>
		</tr>
		<tr>
			<td colspan="4" name="tablaOculta">
        <?php
          if($row_solicitud['op_resp12'] == 'SI'){
              //echo "SI <input type='radio' name='op_resp12'  checked readonly>";
            /*echo "</div>";
            echo "<div class='col-xs-6'>";
              echo "<p class='text-center alert alert-danger'>NO</p>";
              echo "NO <input type='radio' name='op_resp12'  readonly>";
            echo "</div>";*/
        ?>
          <div class="col-xs-6">
            <p class='text-center alert alert-success'><span class='glyphicon glyphicon-ok' aria-hidden='true'></span> SI</p>
          </div>
          <div class="col-xs-6">
            <?php 
              if(empty($row_solicitud['op_resp13'])){
             ?>
              <p class="alert alert-danger">No se proporciono ninguna respuesta.</p>
            <?php 
              }else if($row_solicitud['op_resp13'] == "HASTA $3,000 USD"){
             ?>
              <p class="alert alert-info">HASTA $3,000 USD</p>
            <?php 
              }else if($row_solicitud['op_resp13'] == "ENTRE $3,000 Y $10,000 USD"){
             ?>
             <p class="alert alert-info">ENTRE $3,000 Y $10,000 USD</p>
            <?php 
              }else if($row_solicitud['op_resp13'] == "ENTRE $10,000 A $25,000 USD"){
             ?>
             <p class="alert alert-info">ENTRE $10,000 A $25,000 USD</p>
            <?php 
              }else if($row_solicitud['op_resp13'] != "HASTA $3,000 USD" && $row_solicitud['op_resp13'] != "ENTRE $3,000 Y $10,000 USD" && $row_solicitud['op_resp13'] != "ENTRE $10,000 A $25,000 USD"){
             ?>
             <p class="alert alert-info"><?php echo $row_solicitud['op_resp13']; ?></p>
             
            <?php 
              }
             ?>
          </div>
        <?php
          }else if($row_solicitud['op_resp12'] == 'NO'){
        ?>
          <div class="col-xs-12">
            <p class='text-center alert alert-danger'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span> NO</p>
          </div>
        
        <?php         
          }
        ?>
			</td>
      <td colspan="4" >
          <div class="col-xs-12" name="tablaOculta" style="display:none">
              <div class="col-xs-6">
                SI <input type="radio" class="form-control" name="op_resp12" onclick="mostrar_ventas()" id="op_resp12" value="SI">
              </div>
              <div class="col-xs-6">
                NO <input type="radio" class="form-control" name="op_resp12" onclick="ocultar_ventas()" id="op_resp12" value="NO">
              </div>
          </div>

          
          
          <tr >
            <td colspan="4">
              13. SI SU RESPUESTA FUE POSITIVA, FAVOR DE INIDICAR CON UNA 'X' EL RANGO DEL VALOR TOTAL DE SUS VENTAS SPP DEL CICLO ANTERIOR DE ACUERDO A LA SIGUIENTE TABLA:

              <div class="well col-xs-12 " id="tablaVentas" style="display:none;">
                
                  <div class="col-xs-6"><p>Hasta $3,000 USD</p></div>
                  <div class="col-xs-6 "><input type="radio" name="op_resp13" class="form-control" id="ver" onclick="ocultar()" value="HASTA $3,000 USD"></div>
                
                
                  <div class="col-xs-6"><p>Entre $3,000 y $10,000 USD</p></div>
                  <div class="col-xs-6"><input type="radio" name="op_resp13" class="form-control" id="ver" onclick="ocultar()" value="ENTRE $3,000 Y $10,000 USD"></div>
                
                
                  <div class="col-xs-6"><p>Entre $10,000 a $25,000 USD</p></div>
                  <div class="col-xs-6"><input type="radio" name="op_resp13" class="form-control"  id="ver" onclick="ocultar()" value="ENTRE $10,000 A $25,000 USD"></div>
                
                  <div class="col-xs-6"><p>Más de $25,000 USD <sup>*</sup><br><h6><sup>*</sup>Especifique la cantidad.</h6></p></div>
                  <div class="col-xs-6"><input type="radio" name="op_resp13" class="form-control" id="exampleInputEmail1" onclick="mostrar()" value="mayor">
                    <input type="text" name="op_resp13_1" class="form-control" id="oculto" style='display:none;' placeholder="Especifique la Cantidad">
                  </div>
                
              </div>
                    
            </td>
          </tr>

      </td>
		</tr>

  
		<tr>
			<td colspan="4">
				14. FECHA ESTIMADA PARA COMENZAR A USAR EL SÍMBOLO DE PEQUEÑOS PRODUCTORES.
			</td>
		</tr>	
		<tr>
			<td colspan="4">
				<textarea class="form-control claseModificar" name="op_resp14" id="" rows="3" readonly><?php echo $row_solicitud['op_resp14']?></textarea>
			</td>
		</tr>	
		<tr>
			<td colspan="4">
				15. ANEXAR EL CROQUIS GENERAL DE SU OPP, INDICANDO LAS ZONAS EN DONDE CUENTA CON SOCIOS.
			</td>
		</tr>	
		<tr id="mensajeCotizacion">

      <?php   $sizeRuta = strlen("../../croquis/"); ?>  
      <?php if(strlen($row_solicitud["op_resp15"])<=$sizeRuta){ ?>

        <td colspan="4" name="tablaOculta">
          <p class="alert alert-danger">No Disponible</p>
        </td>
        <td colspan="4" name="tablaOculta" style="display:none">
          <input name="op_resp15_1" type="file" class="filestyle" data-buttonName="btn-info" data-buttonBefore="true" data-buttonText="Cargar Croquis"> 
        </td>
      <?php }else{ ?>
        <td colspan="4">

          <a class="btn btn-success" href="<?echo $row_solicitud['op_resp15']?>" target="_blank"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Descargar Croquis</a>
          <input type="hidden" name="op_resp15" value="<?php echo $row_solicitud['op_resp15']?>">
        </td>
      <?php } ?>

		</tr>	
    <tr id="cargarCotizacion" style="display:none">
      <td>
          <input name="op_resp15" id="op_resp15" type="file" class="filestyle" data-buttonName="btn-info" data-buttonBefore="true" data-buttonText="Cargar Croquis"> 
      </td>
    </tr>
		<tr class="success">
			<th colspan="4" class="text-center">DATOS DE PRODUCTOS PARA LOS CUALES QUIERE UTILIZAR EL SÍMBOLO<sup>6</sup></th>
		</tr>



		<tr>
			<td colspan="4">
				<table class="table table-bordered" id="tablaProductos">
					<tr>
						<td>Producto</td>
						<td>Volumen Total Estimado a Comercializar</td>
						<td>Producto Terminado</td>
						<td>Materia Prima</td>
						<td>País(es) de Destino</td>
						<td>Marca Propia</td>
						<td>Marca de un Cliente</td>
						<td>Sin cliente aún</td>
						<!--<td>
							<button type="button" onclick="tablaProductos()" class="btn btn-primary" aria-label="Left Align">
							  <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
							</button>
							
						</td>-->					
					</tr>

          <?php 
          $query_producto_detalle = "SELECT * FROM productos WHERE idsolicitud_certificacion = $_GET[idsolicitud]";
          $producto_detalle = mysql_query($query_producto_detalle, $dspp) or die(mysql_error());
          $contador = 0;
          while($row_producto = mysql_fetch_assoc($producto_detalle)){
            ?>

					<tr>
						<td>
							<input type="text" class="form-control claseModificar" name="producto[$contador]" id="exampleInputEmail1" placeholder="Producto" value="<?echo $row_producto['producto']?>" readonly>
						</td>
						<td>
							<input type="text" class="form-control claseModificar" name="volumen[$contador]" id="exampleInputEmail1" placeholder="Volumen" value="<?echo $row_producto['volumen']?>" readonly>
						</td>
						<td>
              <?php 
                if($row_producto['terminado'] == 'SI'){
                  echo "SI <input type='radio'  name='terminado".$contador."[$contador]' id=' value='SI' checked readonly><br>";
                }else if($row_producto['terminado'] == 'NO'){
                  echo "NO <input type='radio'  name='terminado".$contador."[$contador]' id=' value='NO' checked readonly>";
                }
               ?>
            </td>          
						<td>
							<textarea cols="30" rows="5" type="text" class="form-control claseModificar" name="materia[$contador]" id="exampleInputEmail1" placeholder="Materia" readonly><?echo $row_producto['materia']?></textarea>
						</td>
						<td>
							<textarea cols="30" rows="5" type="text" class="form-control claseModificar" name="destino[$contador]" id="exampleInputEmail1" placeholder="Destino" readonly><?echo $row_producto['destino']?></textarea>
						</td>
						<td>
              <?php 
                if($row_producto['marca_propia'] == 'SI'){
                  echo "SI <input type='radio'  name='marca_propia".$contador."[0]' id=' value='SI' checked readonly><br>";
                }else if($row_producto['marca_propia'] == 'NO'){
                  echo "NO <input type='radio'  name='marca_propia".$contador."[0]' id=' value='NO' checked readonly>";
                }
               ?>
						</td>
						<td>
              <?php 
                if($row_producto['marca_cliente'] == 'SI'){
                  echo "SI <input type='radio'  name='marca_cliente".$contador."[0]' id=' value='SI' checked readonly><br>";
                }else if($row_producto['marca_cliente'] == 'NO'){
                  echo "NO <input type='radio'  name='marca_cliente".$contador."[0]' id=' value='NO' checked readonly>";
                }
               ?>              
						</td>
						<td>
              <?php 
                if($row_producto['sin_cliente'] == 'SI'){
                  echo "SI <input type='radio'  name='sin_cliente".$contador."[0]' id=' value='SI' checked readonly><br>";
                }else if($row_producto['sin_cliente'] == 'NO'){
                  echo "NO <input type='radio'  name='sin_cliente".$contador."[0]' id=' value='NO' checked readonly>";
                }
               ?> 
						</td>
					</tr>

          <?php $contador++; }?>		
          <tr>
            <td colspan="8"></td>
          </tr>
					<tr>
						<td colspan="8">
							<h6><sup>6</sup> La información proporcionada en esta sección será tratada con plena confidencialidad. Favor de insertar filas adicionales de ser necesario.</h6>
						</td>
					</tr>
				</table>
			</td>

		</tr>
    
		<tr>
			<th class="success" colspan="4">
				COMPROMISOS
			</th>
		</tr>
		<tr class="text-justify">
			<td colspan="4">
				1. Con el envío de esta solicitud se manifiesta el interés de recibir una propuesta de Certificación.<br>
				2. El proceso de Certificación comenzará en el momento que se confirme la recepción del pago correspondiente.<br>
				3. La entrega y recepción de esta solicitud no garantiza que el proceso de Certificación será positivo.<br>
				4. Conocer y dar cumplimiento a todos los requisitos de la Norma General del Símbolo de Pequeños Productores que le apliquen como Organización de Pequeños Productores, tanto Críticos como Mínimos, independientemente del tipo de evaluación que se realice.
			</td>
		</tr>
		<tr>
			<td colspan="2">
				Nombre de la persona que se responsabiliza de la veracidad de la información del formato y que le dará seguimiento a la solicitud de parte del solicitante:
			</td>
			<td colspan="2">
				<?php if(isset($row_solicitud['responsable'])){ ?>
				<input type="text" class="form-control claseModificar" name="responsable" value="<?php echo $row_solicitud['responsable']?>" readonly>
				<?}else{?>
				En Proceso
				<?}?>
			</td>
		</tr>
    <tr>
      <td colspan="2">
        OC que recibe la solicitud:
      </td>
      <td colspan="2">
        <input type="text" class="form-control claseModificar" name="personal_oc" value="<?echo $row_solicitud['nombreOC']?>" disabled>
      </td>

    </tr>  


	</table>


	
  <input type="hidden" name="MM_update" value="form1">

	<!--<input type="hidden" name="updateAprobado" value="Se ha aprobado la solicitud" />
  <input type="hidden" name="updateDenegado" value="Se ha denegado la solicitud" />-->
  <input type="hidden" name="fecha_cotizacion" value="<?php echo time()?>">
	<input type="hidden" name="idoc" value="<?php echo $_SESSION['idoc']?>">
  <input type="hidden" name="idsolicitud" value="<?php echo $_GET['idsolicitud']?>">
  <input type="hidden" name="idopp" value="<?php echo $row_opp['idopp'];?>">
  <input type="hidden" name="idsolicitud_certificacion" value="<?php echo $_GET['idsolicitud']?>">  
  <input type="hidden" name="abreviacionOPP" value="<?php echo $row_opp['abreviacion'];?>">
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
mysql_free_result($opp);

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
