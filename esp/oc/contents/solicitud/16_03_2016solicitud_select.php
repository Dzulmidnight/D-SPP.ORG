<?php require_once('../Connections/dspp.php'); ?>
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

if(isset($_POST['opp_delete'])){
	$query=sprintf("delete from opp where idopp = %s",GetSQLValueString($_POST['idopp'], "text"));
	$ejecutar=mysql_query($query,$dspp) or die(mysql_error());
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
  $query_opp = "SELECT opp.* ,solicitud_certificacion.* FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE solicitud_certificacion.idoc = $_SESSION[idoc] ORDER BY solicitud_certificacion.fecha_elaboracion DESC";

	#$query_opp = "SELECT * FROM solicitud_certificacion where idsolicitud_certificacion ='".$_GET['query']."' ORDER BY fecha DESC";
}else{
  #SELECT solicitud_certificacion.* FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE opp.idopp = 15

  $query_opp = "SELECT opp.* ,solicitud_certificacion.* FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE solicitud_certificacion.idoc = $_SESSION[idoc] ORDER BY solicitud_certificacion.fecha_elaboracion DESC"; 

  #$query_opp = "SELECT opp.* ,solicitud_certificacion.* FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp ORDER BY solicitud_certificacion.fecha_elaboracion ASC";  

	#$query_opp = "SELECT * FROM solicitud_certificacion ORDER BY fecha ASC";
}



if(isset($_POST['statusCertificado']) && $_POST['statusCertificado'] == "statusCertificado"){
  $fecha = date("d/m/Y", $_POST['statusFecha']);
  $fecha_actual = time();
  $idopp = $_POST['idopp'];
  $idoc = $_POST['idoc'];
  $status = $_POST['status'];
  $idsolicitud = $_POST['idsolicitud'];

  $query = "INSERT INTO certificado (status, idsolicitud, idopp, entidad) VALUES ('$status', $idsolicitud, $idopp, $idoc)";
  $certificado = mysql_query($query, $dspp) or die(mysql_error());

  $idcertificado = mysql_insert_id($dspp);
  $identificador = "CERTIFICADO";
  $idexterno = $idsolicitud;


  $queryFecha = "INSERT INTO fecha (fecha, idexterno, idopp, idoc, idcertificado, identificador, status) VALUES($fecha_actual, $idexterno, $idopp, $idoc, $idcertificado, '$identificador', '$status')"; 
  $insertarFecha = mysql_query($queryFecha, $dspp) or die(mysql_error());

  $query = "UPDATE solicitud_certificacion SET status = '$status' WHERE idsolicitud_certificacion = $idsolicitud";
  $actualizar = mysql_query($query,$dspp) or die(mysql_error());
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

    $idexterno = $idsolicitud;
    $identificador = "CERTIFICADO";

    $queryFecha = "INSERT INTO fecha (fecha, idexterno, idopp, idoc, idcertificado, identificador, status) VALUES($fecha_actual, $idexterno, $idopp, $idoc, $idcertificado, '$identificador', '$status')";
    $insertarFecha = mysql_query($queryFecha, $dspp) or die(mysql_error());

    $query = "UPDATE solicitud_certificacion SET status = '$status' WHERE idsolicitud_certificacion = $idsolicitud";
    $actualizar = mysql_query($query,$dspp) or die(mysql_error());

    if($status == 8){

            $destinatario = "cert@spp.coop";
            $asunto = "D-SPP - Proceso de certificación finalizado"; 

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
                      <td><b>Ha finalizado el proceso de certificación con un Dictamen positivo, dentro de poco estará disponible el certificado para ser descargado.</b></td>
                    </tr>
                  </tbody>
                </table>
              </body>
              </html>
            ';


                //para el envío en formato HTML 
                $headers = "MIME-Version: 1.0\r\n"; 
                $headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 

                //dirección del remitente 
                $headers .= "From: cert@spp.coop\r\n"; 

                //dirección de respuesta, si queremos que sea distinta que la del remitente 
                

                //ruta del mensaje desde origen a destino 
                //$headers .= "Return-path: holahola@desarrolloweb.com\r\n"; 

                //direcciones que recibián copia 
                //$headers .= "Cc: maria@desarrolloweb.com\r\n"; 

                //direcciones que recibirán copia oculta 
                $headers .= "Bcc: yasser.midnight@gmail.com\r\n";
                //$headers .= "Bcc: isc.jesusmartinez@gmail.com  \r\n"; 

                mail($destinatario,$asunto,utf8_decode($cuerpo),$headers);


        $queryMensaje = "INSERT INTO mensajes(idopp, idoc,asunto, mensaje, destinatario, remitente, fecha) VALUES($idopp, $idoc, '$asunto', '$cuerpo', 'ADM', 'OC', $fecha_actual)";
        $ejecutar = mysql_query($queryMensaje,$dspp) or die(mysql_error());
                /************************  FIN MAIL FUNDEPPO  *********************************************/


                /************************  INICIA MAIL OPP  *******************************************/
            $emailOPP1 = $_POST['emailOPP1'];
            $emailOPP2 = $_POST['emailOPP2'];

            $destinatario = $emailOPP1.",";
            $destinatario .= $emailOPP2;

            $asunto = "D-SPP - Certificación para Organizaciones de Pequeños Productores"; 

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
                      <td><b>Ha finalizado el proceso de certificación con un Dictamen positivo, dentro de poco estará disponible el certificado para ser descargado.</b></td>
                    </tr>

                  </tbody>
                </table>

              </body>
              </html>
            ';


                //para el envío en formato HTML 
                $headers = "MIME-Version: 1.0\r\n"; 
                $headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 

                //dirección del remitente 
                $headers .= "From: cert@spp.coop\r\n"; 

                //dirección de respuesta, si queremos que sea distinta que la del remitente 
                

                //ruta del mensaje desde origen a destino 
                //$headers .= "Return-path: holahola@desarrolloweb.com\r\n"; 

                //direcciones que recibián copia 
                //$headers .= "Cc: maria@desarrolloweb.com\r\n"; 

                //direcciones que recibirán copia oculta 
                $headers .= "Bcc: yasser.midnight@gmail.com\r\n";
                //$headers .= "Bcc: isc.jesusmartinez@gmail.com  \r\n"; 

                mail($destinatario,$asunto,utf8_decode($cuerpo),$headers) ;

                $queryMensaje = "INSERT INTO mensajes(idopp, idoc,asunto, mensaje, destinatario, remitente, fecha) VALUES($idopp, $idoc, '$asunto', '$cuerpo', 'OPP', 'OC', $fecha_actual)";
                $ejecutar = mysql_query($queryMensaje,$dspp) or die(mysql_error());


    }

}


if(isset($_POST['cargarCertificado']) && $_POST['cargarCertificado'] == "cargarCertificado"){

  $idopp = $_POST['idopp'];
  $idoc = $_POST['idoc'];
  $ruta = "archivos/certificados/";

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

                //para el envío en formato HTML 
                $headers = "MIME-Version: 1.0\r\n"; 
                $headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 

                //dirección del remitente 
                $headers .= "From: cert@spp.coop\r\n"; 

                //dirección de respuesta, si queremos que sea distinta que la del remitente 
                

                //ruta del mensaje desde origen a destino 
                //$headers .= "Return-path: holahola@desarrolloweb.com\r\n"; 

                //direcciones que recibián copia 
                //$headers .= "Cc: maria@desarrolloweb.com\r\n"; 

                //direcciones que recibirán copia oculta 
                $headers .= "Bcc: yasser.midnight@gmail.com\r\n";
                //$headers .= "Bcc: isc.jesusmartinez@gmail.com  \r\n"; 

                mail($destinatario,$asunto,utf8_decode($cuerpo),$headers);

                $queryMensaje = "INSERT INTO mensajes(idopp, idoc,asunto, mensaje, destinatario, remitente, fecha) VALUES($idopp, $idoc, '$asunto', '$cuerpo', 'ADM', 'OC', $fecha_actual)";
                $ejecutar = mysql_query($queryMensaje,$dspp) or die(mysql_error());

                /************************  FIN MAIL FUNDEPPO  *********************************************/


                /************************  INICIA MAIL OPP  *******************************************/
            $emailOPP1 = $_POST['emailOPP1'];
            $emailOPP2 = $_POST['emailOPP2'];

            $destinatario = $emailOPP1.",";
            $destinatario .= $emailOPP2;

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
                //para el envío en formato HTML 
                $headers = "MIME-Version: 1.0\r\n"; 
                $headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 

                //dirección del remitente 
                $headers .= "From: cert@spp.coop\r\n"; 

                //dirección de respuesta, si queremos que sea distinta que la del remitente 
                

                //ruta del mensaje desde origen a destino 
                //$headers .= "Return-path: holahola@desarrolloweb.com\r\n"; 

                //direcciones que recibián copia 
                //$headers .= "Cc: maria@desarrolloweb.com\r\n"; 

                //direcciones que recibirán copia oculta 
                $headers .= "Bcc: yasser.midnight@gmail.com\r\n";
                //$headers .= "Bcc: isc.jesusmartinez@gmail.com  \r\n"; 

                mail($destinatario,$asunto,utf8_decode($cuerpo),$headers) ;


                $queryMensaje = "INSERT INTO mensajes(idopp, idoc,asunto, mensaje, destinatario, remitente, fecha) VALUES($idopp, $idoc, '$asunto', '$cuerpo', 'OPP', 'OC', $fecha_actual)";
                $ejecutar = mysql_query($queryMensaje,$dspp) or die(mysql_error());
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
  $query = "SELECT * FROM status";
  $ejecutarStatus = mysql_query($query,$dspp) or die(mysql_error());
 ?>

<h4>Consulta Solicitudes</h4>
<table class="table table-condensed table-bordered table-hover">
  <tr class="success">
    <th class="text-center"><h5><b>Fecha<br>Solicitud</b></h5></th>
    <th class="text-center"><h5>Organización</h5></th>
    <th class="text-center" colspan="3"><h5>Cotización<br>(Descargable)</h5></th>
    <th class="text-center"><h5>Sitio WEB</h5></th>
    <th class="text-center"><h5>Contacto</h5></th>
    <th class="text-center"><h5>País</h5></th>
    <th class="text-center"><h5>Status Publico</h5></th>
    <th class="text-center"><h5>Status Interno</h5></th>
    <th class="text-center" colspan="2"><h5>Certificado</h5></th>
    <!--<th class="text-center">Propuesta</th>-->
    <th class="text-center"><h5>Observaciones<br>Solicitud</h5></th>
    <!--<th>OC</th>
    <th>Razón social</th>
    <th>Dirección fiscal</th>
    <th>RFC</th>-->
    <!--<th>Eliminar</th>-->
  </tr>



  <?php $cont=0; while ($row_opp = mysql_fetch_assoc($opp)) {$cont++; ?>
    <tr <?php if($row_opp['estado'] == 20){ echo "style='border-style:solid;border-color:#E74C3C'";} ?>>
      <?php  $fecha = $row_opp['fecha_elaboracion']; ?>

        <!----------------------------------------- ULTIMA ACTUALIZACION ---------------------------------------------->
        <td>
          <h6>
            <a class="btn btn-primary btn-sm" style="width:100%" href="?SOLICITUD&amp;detailBlock&amp;idsolicitud=<?php echo $row_opp['idsolicitud_certificacion']; ?>" aria-label="Left Align">

              <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
              <?php echo  date("d/m/Y", $fecha); ?><br>Ver solicitud
            </a>
          </h6>
        </td>
        <!----------------------------------------- ULTIMA ACTUALIZACION ---------------------------------------------->


        <!----------------------------------------- ORGANIZACION ---------------------------------------------->
          <td>
            <h6 class="text-center">
              <?php 
                if(isset($row_opp['nombre'])){
                  echo "<p class='alert alert-success'>".$row_opp['nombre'].", <u>".$row_opp['abreviacion']."</u></p>";
                }else{
                  echo "<p class='alert alert-danger'>No Disponible</p>";
                } 
              ?>
            </h6>
          </td>
        <!----------------------------------------- ORGANIZACION ---------------------------------------------->


        <!----------------------------------------- COTIZACION OPP ---------------------------------------------->
        <td>
          <h6>
            <a href="http://d-spp.org/oc/<?echo $row_opp['cotizacion_opp']?>" target="_blank" type="button" class="btn <?php if(empty($row_opp['cotizacion_opp'])){ echo 'btn-danger btn-sm';}else{echo 'btn-success btn-sm';} ?>" aria-label="Left Align" <?php if(empty($row_opp['cotizacion_opp'])){echo "disabled";}?>>
              <span class="glyphicon glyphicon-download-alt"></span> OPP
            </a> 
          </h6>
        </td>
        <!----------------------------------------- COTIZACION OPP ---------------------------------------------->


        <!----------------------------------------- COTIZACION FUNDEPPO ---------------------------------------------->
        <td>
          <h6>
            <a href="http://d-spp.org/oc/<?echo $row_opp['cotizacion_adm']?>" target="_blank" type="button" class="btn <?php if(empty($row_opp['cotizacion_adm'])){ echo 'btn-danger btn-sm';}else{echo 'btn-success btn-sm';} ?>" aria-label="Left Align" <?php if(empty($row_opp['cotizacion_adm'])){echo "disabled";}?>>
              <span class="glyphicon glyphicon-download-alt"></span> FUNDEPPO
            </a> 
          </h6>       
        </td>
        <!----------------------------------------- COTIZACION FUNDEPPO ---------------------------------------------->

        <!----------------------------------------- PROPUESTA ---------------------------------------------->
        <td>
          <h6>
            <?php 
              if($row_opp['status'] != 1 && $row_opp['status'] != 2 && $row_opp['status'] != 3 && $row_opp['status'] != 14 && $row_opp['status'] != 15 && $row_opp['status'] != 17 && $row_opp['status'] != 20){
            ?>
              <h6 class="alert alert-success">Aceptada</h6>
                  <!--<button class="btn btn-success btn-sm" disabled>
                    <span class="glyphicon glyphicon-check" aria-hidden="true"></span> Aceptada
                  </button>-->          
              <?php 
                }else{
              ?>
                <h6 class="alert alert-danger">Pendiente</h6>
                  <!--<button class="btn btn-default btn-sm" disabled>
                    <span class="glyphicon glyphicon-check" aria-hidden="true"></span> Aceptada
                  </button> -->              
            <?php 
              }
            ?>      
          </h6>
        </td>
        <!----------------------------------------- PROPUESTA ---------------------------------------------->

        <!-----------------------------------------SITIO WEB ---------------------------------------------->
          <td>
            <h6 class="text-center">
              <?php 
                if(isset($row_opp['sitio_web'])){
                  echo "<p class='alert alert-success'>".$row_opp['sitio_web']."</p>";
                }else{
                  echo "<p class='alert alert-danger'>No Disponible</p>";
                } 
              ?>
            </h6>
          </td>
        <!-----------------------------------------SITIO WEB ---------------------------------------------->


        <!----------------------------------------- EMAIL CONTACTO ---------------------------------------------->
          <td>
            <h6 class="text-center">
              <?php 
                if(isset($row_opp['p1_email'])){
                  echo "<p class='alert alert-success'>".$row_opp['p1_email']."</p>";
                }else{
                  echo "<p class='alert alert-danger'>No Disponible</p>";
                } 
              ?>
            </h6>
          </td>        <!----------------------------------------- EMAIL CONTACTO ---------------------------------------------->


        <!----------------------------------------- PAIS ---------------------------------------------->
          <td>
            <h6 class="text-center">
              <?php 
                if(isset($row_opp['pais'])){
                  echo "<p class='alert alert-success'>".$row_opp['pais']."</p>";
                }else{
                  echo "<p class='alert alert-danger'>No Disponible</p>";
                } 
              ?>
            </h6>
          </td>        <!----------------------------------------- PAIS ---------------------------------------------->


        <!----------------------------------------- STATUS PUBLICO ---------------------------------------------->
        <td>
        <?php 
          $query_status = "SELECT * FROM status_publico WHERE idstatus_publico = $row_opp[status_publico]";
          $ejecutar = mysql_query($query_status,$dspp) or die(mysql_error());
          $estatus_publico = mysql_fetch_assoc($ejecutar);
         ?>

            <h6>
              <?php 
                if($row_opp['status'] == 10){
                  echo "<p class='text-center alert alert-success'><b><u>Certificado</u></b></p>";
                }else{
                   echo "<p class='text-center alert alert-warning'>".$estatus_publico['nombre']."</p>"; 
                }
              ?>
            </h6>

        </td>
        <!----------------------------------------- STATUS PUBLICO ---------------------------------------------->

        <!------------------------------------ SECCION STATUS INTERNO ------------------------------------>
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

            <h6 <?echo $colorEstado;?>>
              <?php echo $estatus_interno['nombre']; ?>
            </h6>

          </td>
        <!------------------------------------ SECCION STATUS INTERNO ------------------------------------>

    


        <!----------------------------------------- STATUS CERTIFICADO -------------------------------------------->
       
              <?php 
                $query = "SELECT * FROM certificado WHERE idsolicitud = $row_opp[idsolicitud_certificacion]";
                $ejecutar = mysql_query($query, $dspp) or die(mysql_error());
                $registroCertificado = mysql_fetch_assoc($ejecutar);

                $queryObjecion = "SELECT * FROM objecion WHERE idsolicitud = $row_opp[idsolicitud_certificacion]";
                $ejecutar2 = mysql_query($queryObjecion,$dspp);
                $registroObjecion = mysql_fetch_assoc($ejecutar2); 
               ?>


        <!----------------------------------------- STATUS CERTIFICADO ---------------------------------------------->
        <td>     
          <?php if($row_opp['status'] != 1 && $row_opp['status'] != 2 && $row_opp['status'] != 3 && $row_opp['status'] != 14 && $row_opp['status'] != 15 && $row_opp['status'] != 17 && $row_opp['status'] != 20){ ?>
            <?php if(isset($registroObjecion['idobjecion'])){ ?>
              <h6>
                <button class="btn btn-success btn-sm" data-toggle="modal" <?php echo "data-target='#status".$row_opp['idsolicitud_certificacion']."'"?>  >
                <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Consultar<br>Status
                </button>       
              </h6>
            <?php }else{ ?>
              <h6>
                <button class="btn btn-warning btn-sm" data-toggle="modal" <?php echo "data-target='#status".$row_opp['idsolicitud_certificacion']."'"?>  >
                <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Consultar<br>Status
                </button>            
              </h6>
            <?php } ?>

          <?php }else{ ?>
            <h6>
              <button class="btn btn-danger btn-sm" data-toggle="modal" <?php echo "data-target='#status".$row_opp['idsolicitud_certificacion']."'"?>  disabled>
              <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Consultar<br>Status
              </button>            
            </h6>          
          <?php } ?>
        </td>
        <!----------------------------------------- STATUS CERTIFICADO ---------------------------------------------->


              <!----------------------------------------- MODAL STATUS CERTIFICADO -------------------------------------->
              <form action="" method="post" id="statusCertificado" enctype="application/x-www-form-urlencoded">
                <div class="modal fade" <?php echo "id='status".$row_opp['idsolicitud_certificacion']."'" ?> tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Status Certificación</h4>
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
                                  <?php if(!empty($registroCertificado['status'])){ ?>
                                    <?
                                      $query = "SELECT * FROM status WHERE idstatus = $registroCertificado[status]";
                                      $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
                                      $estatus = mysql_fetch_assoc($ejecutar);
                                    ?>
                                    <label class="control-label" for="statusActual">Status Actual</label>
                                    <p name="statusActual" class="alert alert-success"><? echo $estatus['nombre'];?></p>

                                    <select name="status" class="form-control" id="status" required>
                                      <option class="form-control" value="">Seleccione un estado</option>
                                      <?php while($row_status = mysql_fetch_assoc($ejecutarStatus)){ ?>
                                        <?php 
                                          if($row_status['idstatus'] != 1 && $row_status['idstatus'] != 2 && $row_status['idstatus'] != 3 && $row_status['idstatus'] != 10 && $row_status['idstatus'] != 17 && $row_status['idstatus'] != 18 && $row_status['idstatus'] != 19 ){
                                        ?>
                                          <option class="form-control" value="<?echo $row_status['idstatus'];?>"><?echo $row_status['nombre'];?></option>                                        
                                        <?php
                                          }
                                        ?>

                                      <?php } ?>
                                    </select>

                                  <?php }else{ ?>
                                    <label class="control-label" for="status">Status Certificación</label>
                                    <select name="status" class="form-control" id="status" required>
                                      <option class="form-control" value="">Seleccione un estado</option>
                                      <?php while($row_status = mysql_fetch_assoc($ejecutarStatus)){ ?>
                                        <?php
                                          if($row_status['idstatus'] != 1 && $row_status['idstatus'] != 2 && $row_status['idstatus'] != 3 && $row_status['idstatus'] != 10 && $row_status['idstatus'] != 17 && $row_status['idstatus'] != 18 && $row_status['idstatus'] != 19){
                                        ?>
                                          <option class="form-control" value="<?echo $row_status['idstatus'];?>"><?echo $row_status['nombre'];?></option>                                        
                                        <?php
                                          }
                                        ?>
                                      <?php } ?>
                                    </select>
                                    
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
                          <input type="hidden" name="emailOPP1" value="<?php echo $row_opp['p1_email'];?>">
                          <input type="hidden" name="emailOPP2" value="<?php echo $row_opp['p2_email'];?>">
                          <input type="hidden" name="idopp" value="<?php echo $row_opp['idopp'];?>">
                          <input type="hidden" name="idoc" value="<?php echo $row_opp['idoc'];?>">

                          <button type="submit" class="btn btn-primary">Actualizar</button>

                        <?php }else{ ?>
                          <?php if(!empty($registroObjecion['adjunto']) || $row_opp['status_publico'] == 8){ ?>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                          <?php } ?>
                          <input type="hidden" name="statusCertificado" value="statusCertificado">
                        <?php } ?>
                          <input type="hidden" name="idopp" value="<?php echo $row_opp['idopp'];?>">
                          <input type="hidden" name="idoc" value="<?php echo $row_opp['idoc'];?>">
                          <input type="hidden" name="statusFecha" value="<?echo time();?>">
                          <input type="hidden" name="idsolicitud" value="<?echo $row_opp['idsolicitud_certificacion'];?>">




                      </div>
                    </div>
                  </div>
                </div>
              </form>
              <!----------------------------------------- MODAL STATUS CERTIFICADO --------------------------------------->


        <!----------------------------------------- CERTIFICADO CERTIFICADO ---------------------------------------------->
        <td>
          <?php if(isset($registroCertificado['status']) && ($registroCertificado['status'] == 8 || $registroCertificado['status'] == 10)){ ?>
            

            <h6>
              <button class="btn btn-success btn-sm" data-toggle="modal" <?php echo "data-target='#certificado".$row_opp['idsolicitud_certificacion']."'"?>  >
              <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Detalle<br>Certificado
              </button>            
            </h6>
          <?php }else{ ?>
            <h6>
              <button class="btn btn-danger btn-sm" data-toggle="modal" <?php echo "data-target='#certificado".$row_opp['idsolicitud_certificacion']."'"?>  disabled>
              <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Detalle<br>Certificado
              </button>            
            </h6>
          <?php } ?>
        </td>
        <!----------------------------------------- CERTIFICADO CERTIFICADO ---------------------------------------------->

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



        <!----------------------------------------- OBSERVACIONES ---------------------------------------------->
        <td>
          <h6>
            <?php if(empty($row_opp['observaciones'])){ ?>
              <button class="btn btn-default btn-sm" disabled>
                <span class="glyphicon glyphicon-list-alt"></span> Consultar
              </button>         
            <?php }else{ ?>
              <a class="btn btn-info btn-sm" href="?SOLICITUD&amp;detailBlock&amp;idsolicitud=<?php echo $row_opp['idsolicitud_certificacion']; ?>">
                <span class="glyphicon glyphicon-list-alt"></span> Consultar
              </a>
            <?php } ?>
          </h6>
        </td>
         <!----------------------------------------- OBSERVACIONES ---------------------------------------------->

       

        <form action="" method="post">
        <!--<input class="btn btn-danger" type="submit" value="Eliminar" />-->
        <input type="hidden" value="OPP eliminado correctamente" name="mensaje" />
        <input type="hidden" value="1" name="opp_delete" />
        <input type="hidden" value="<?php echo $row_opp['idopp']; ?>" name="idopp" />
        </form>
        <!--</td>-->
    </tr>
    <?php }  ?>
    <? if($cont==0){?>
    <tr><td colspan="13" class="alert alert-info" role="alert">No se encontraron registros</td></tr>
    <? }?>
</table>
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
