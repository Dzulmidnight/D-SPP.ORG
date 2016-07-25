<?php 
require_once('../Connections/dspp.php'); 
require_once('../Connections/mail.php'); 


  error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

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

$query_limit_opp = sprintf("%s LIMIT %d, %d", $query_opp, $startRow_opp, $maxRows_opp);
$opp = mysql_query($query_limit_opp,$dspp) or die(mysql_error());
//$row_opp = mysql_fetch_assoc($opp);

if(isset($_POST['todos'])){
  $query_oc = "SELECT idoc FROM oc WHERE idoc != 9999";

  $oc_query = mysql_query($query_oc, $dspp) or die(mysql_error());
  $idsolicitud_certificacion = $_POST['todos'];

  while($oc = mysql_fetch_assoc($oc_query)){

    $query = "INSERT INTO solicitud_certificacion (idopp, ciudad, ruc, p1_nombre, p1_cargo, p1_telefono, p1_email, p2_nombre, p2_cargo, p2_telefono, p2_email, adm_nom1, adm_nom2, adm_tel1, adm_tel2, adm_email1, adm_email2, resp1, resp2, resp3, resp4, op_resp1, op_area1, op_area2, op_area3, op_area4, op_resp2, op_resp3, op_resp4, op_resp5, op_resp6, op_resp7, op_resp8, op_resp10, op_resp11, op_resp12, op_resp13, op_resp14, op_resp15, responsable, idoc, observaciones, fecha_elaboracion, cotizacion_opp, cotizacion_adm, status) SELECT idopp, ciudad, ruc, p1_nombre, p1_cargo, p1_telefono, p1_email, p2_nombre, p2_cargo, p2_telefono, p2_email, adm_nom1, adm_nom2, adm_tel1, adm_tel2, adm_email1, adm_email2, resp1, resp2, resp3, resp4, op_resp1, op_area1, op_area2, op_area3, op_area4, op_resp2, op_resp3, op_resp4, op_resp5, op_resp6, op_resp7, op_resp8, op_resp10, op_resp11, op_resp12, op_resp13, op_resp14, op_resp15, responsable, $oc[idoc], observaciones, fecha_elaboracion, cotizacion_opp, cotizacion_adm, status FROM solicitud_certificacion WHERE idsolicitud_certificacion = $idsolicitud_certificacion";

    $resultado = mysql_query($query, $dspp) or die(mysql_error());

  }

  $eliminar="DELETE FROM solicitud_certificacion WHERE idsolicitud_certificacion = $idsolicitud_certificacion";
  $ejecutar=mysql_query($eliminar,$dspp) or die(mysql_error());


}



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
?>


<?php 
  $query_buscar = "SELECT opp.* ,solicitud_certificacion.* FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE solicitud_certificacion.idoc = 99 AND solicitud_certificacion.status = 'REVISION' ORDER BY solicitud_certificacion.fecha_elaboracion DESC";

  $ejecutar_busqueda = mysql_query($query_buscar, $dspp) or die(mysql_error());
  $numero = mysql_num_rows($ejecutar_busqueda);


 ?>

<h4>Menú principal Administrador</h4>


        <div class="col-xs-12 ">
          <?php 
            $timeActual = "";
            $timeVencimiento = "";
            $timeRestante = "";
            $plazo = "";

            $timeActual= time();   // Obtenemos el timestamp del momento actual
            //$timeVencimiento = strtotime("2016-02-12");
            //$timeVencimiento = strtotime(); // Obtenemos timestamp de la fecha de vencimiento
           // $timeRestante = ($timeVencimiento - $timeActual);

            $plazo = 60 *(24*60*60);
            // Calculamos el número de segundos que tienen 60 dias

        /*
            if ($timeRestante <= $plazo) {
                echo "<script>alert('Se ha enviado un correo');</script>";
            }else{
                echo "<script>alert('Faltan Dias');</script>";
            }*/

            $contador = 1;

            //$query = "SELECT opp.*, certificado.status AS 'statusCertificado', certificado.* FROM opp INNER JOIN certificado ON opp.idopp = certificado.idopp";
            //$query = "SELECT opp.*, certificado.*, certificado.status AS 'statusCertificado', status.nombre AS 'nombreStatus' FROM opp INNER JOIN certificado ON opp.idopp = certificado.idopp  INNER JOIN status ON opp.estado = status.idstatus ORDER BY vigenciafin";
            $queryConsulta = "SELECT opp.*, certificado.*, certificado.status AS 'statusCertificado', status.nombre AS 'nombreStatus', oc.abreviacion AS 'abreviacionOC', notificaciones.idnotificacion, notificaciones.tipo_notificacion FROM opp INNER JOIN certificado ON opp.idopp = certificado.idopp INNER JOIN oc ON certificado.entidad = oc.idoc INNER JOIN status ON opp.estado = status.idstatus LEFT JOIN notificaciones ON opp.idopp = notificaciones.idopp ORDER BY vigenciafin";

            $ejecutarOPP = mysql_query($queryConsulta,$dspp) or die(mysql_error());

            ?>

                <script>
                function tablaOPP() {
                  document.getElementById("tablaOPP").style.display = 'block';
                  document.getElementById("tablaEmpresas").style.display = 'none';

                }
                function tablaEmpresas(){
                  document.getElementById("tablaEmpresas").style.display = 'block';
                  document.getElementById("tablaOPP").style.display = 'none';
                }
                </script>

            <div class="col-xs-12">
              <button class="btn btn-primary" onclick="tablaOPP()">LISTA OPP(s)</button>
              <button class="btn btn-primary" onclick="tablaEmpresas()">LISTA EMPRESAS</button>
            </div>


            <div class="col-xs-12" id="tablaOPP" style="display:none;">
                <table class="table table-bordered table-hover" style="font-size:12px;">
                    <tr><td colspan="7" class="text-center"><h4>LISTA DE OPP CON FECHA DE CERTIFICADO</h4></td></tr>
                    <tr>
                        <td>Nº</td>
                        <!--<td>ID OPP</td>-->
                        <!--<td>ID CERTIFICADO</td>-->
                        <td>ABREVIACION</td>
                        <td>ENTIDAD QUE OTORGÓ EL CERTIFICADO</td>
                        <td>ESTATUS OPP</td>
                        <td>ESTATUS CERTIFICADO</td>
                        <td>VENCIMIENTO CERTIFICADO</td>
                        <td>¿SE ENVIO MENSAJE?</td>
                        <!--<td>CORREOS</td>
                        <td>
                            CONTACTOS
                        </td>-->

                    </tr>
                    <?php while($row_opp = mysql_fetch_assoc($ejecutarOPP)){?>

                    <tr>
                        <td><?php echo $contador; ?></td>
                        <!--<td><?php echo $row_opp['idopp']; ?></td>-->
                        <!--<td class="alert alert-info"><?php echo $row_opp['idcertificado']; ?></td>-->
                        <td><?php echo $row_opp['abreviacionOC']; ?></td>
                        <td><?php echo "<a href='?OPP&detail&idopp=$row_opp[idopp]'>$row_opp[abreviacion]</a>"; ?></td>
                        <td <?php if($row_opp['estado'] == 10){echo "class='alert alert-success'";}else if($row_opp['estado'] == 11){ echo "class='alert alert-danger'";}else if($row_opp['estado'] == 12){ echo "class='alert alert-info'";}else if($row_opp['estado'] == 16){ echo "class='alert alert-warning'";} ?>><?php echo $row_opp['nombreStatus']; ?></td>
                        <td <?php if($row_opp['statusCertificado'] == 10){echo "class='alert alert-success'";}else if($row_opp['statusCertificado'] == 11){ echo "class='alert alert-danger'";}else if($row_opp['statusCertificado'] == 12){ echo "class='alert alert-info'";}else if($row_opp['statusCertificado'] == 16){ echo "class='alert alert-warning'";} ?>><?php echo "<p>".$row_opp['nombreStatus']."</p>"; ?></td>                        
                        <td>
                          <?php 
                            $fechaVigencia = date('d-m-Y', strtotime($row_opp['vigenciafin']));
                            echo $fechaVigencia;
                            //echo "<br>";
                          //echo $row_opp['vigenciafin']; 
                          ?>
                        </td>

                        <td>
                          <?php 
                          $timeActual = time();

                          $timeVencimiento = strtotime($row_opp['vigenciafin']);
                          $timeRestante = ($timeVencimiento - $timeActual);
                          $estatusCertificado = "";
                          $plazo = 60 *(24*60*60);
                          $plazoDespues = ($timeVencimiento + $plazo);
                          $prorroga = ($timeVencimiento + $plazo);


                          if($timeActual <= $timeVencimiento){
                            if($timeRestante <= $plazo){
                              $estatusCertificado = 16; // AVISO DE RENOVACIÓN


                              $queryOPP = "SELECT opp.*, contacto.* FROM opp INNER JOIN contacto ON opp.idopp = contacto.idopp WHERE opp.idopp = $row_opp[idopp]";
                              $ejecutar2 = mysql_query($queryOPP,$dspp) or die(mysql_error());


                              $destinatarioOPP = "";
                              /*19_04_2016 while($emailOPP = mysql_fetch_assoc($ejecutar2)){
                                  $destinatarioOPP .= $emailOPP['email'].','.$emailOPP['email1'].','.$emailOPP['email2'];
                              } 19_04_2016*/
                              $emailOPP = mysql_fetch_assoc($ejecutar2);
                              
                              $queryEmailOc = "SELECT certificado.idcertificado, certificado.entidad, oc.* FROM certificado INNER JOIN oc ON certificado.entidad = oc.idoc WHERE idcertificado = $row_opp[idcertificado]";
                              $ejecutar = mysql_query($queryEmailOc,$dspp) or die(mysql_error());
                              $emailOC = mysql_fetch_assoc($ejecutar);

                              $fechaVigencia = date('d-m-Y', strtotime($row_opp['vigenciafin']));
                              $nombreOPP = $row_opp['nombre'];
                              $abreviacionOPP = $row_opp['abreviacion'];

                              $vigenciaFinal = $row_opp['vigenciafin'];
                              $OCemail = $emailOC['email'];

                              if(empty($row_opp['idnotificacion'])){
                                $asunto = "D-SPP - Aviso de Renovacion de Certificado"; 
                                
                                /*$mail = new PHPMailer();
                                // Crear una nueva  instancia de PHPMailer habilitando el tratamiento de excepciones
                                // Configuramos el protocolo SMTP con autenticación
                                $mail->IsSMTP();
                                $mail->SMTPAuth = true;
                                // Puerto de escucha del servidor
                                $mail->Port = 25;
                                // Dirección del servidor SMTP
                                $mail->Host = 'mail.d-spp.org';
                                // Usuario y contraseña para autenticación en el servidor
                                $mail->Username   = "soporte@d-spp.org";
                                $mail->Password = "/aung5l6tZ";
                                $mail->From = "soporte@d-spp.org";
                                $mail->FromName = "CERT - DSPP";*/



                                $mail->AddAddress($emailOPP['email']);
                                $mail->AddAddress($emailOPP['email1']);
                                $mail->AddAddress($emailOPP['email2']);
                                $mail->AddAddress($OCemail);
                                
                                //$mail->AddBCC("yasser.midnight@gmail.com", "correo Oculto");
                                $mail->AddBCC("cert@spp.coop");
                                $mail->AddBCC("adm@spp.coop");
                                $mail->AddBCC("com@spp.coop");


                                $cuerpo = '
                                  <html>
                                  <head>
                                    <meta charset="utf-8">
                                  </head>
                                  <body>    
                                    <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
                                      <tbody>
                                        <tr>
                                          <th rowspan="1" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                                          <th scope="col" align="left" width="500"><strong><h3>Aviso de Renovación de Certificado SPP</h3></strong></th>
                                        </tr>
                                        <tr>
                                          <td style="text-align:justify; padding-top:2em" colspan="2">
                                         
                                            <p>Estimados Representantes de <strong style="color:red">'.$nombreOPP.', (<u>'.$abreviacionOPP.'</u>)</strong>:</p>
                                            
                                            <p>Por este conducto se les informa la necesidad de renovación de su Certificado SPP. La fecha de su vigencia es <strong style="color:red">'.$fechaVigencia.'</strong>, por lo que deben proceder con la evaluación anual.</p>
                                            
                                            <p>De acuerdo a los procedimientos del SPP, se puede llevar a cabo la evaluación dos meses antes de la fecha de vigencia o máximo dos meses después.  Si la evaluación se realiza dos meses después, se esperaría que el dictamen se obtuviera 4 meses después  (de la fecha de vencimiento del certificado) como plazo máximo, para obtener el dictamen positivo de parte del Organismo de Certificación.</p>
                                          
                                            <p>Queremos enfatizar que actualmente existen políticas para la suspensión y/o cancelación del certificado por lo que si ustedes no solicitan a tiempo pueden ser acreedores de una suspensión.</p>
                                            
                                            <p>Agradeciendo su atención, nos despedimos y enviamos saludos del SPP-FUNDEPPO.</p>
                                            
                                            <p>CUALQUIER INCONVENIENTE FAVOR DE NOTIFICARLO A FUNDEPPO-SPP AL CORREO <strong>cert@spp.coop</strong></p>
                                          
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
                                $mail->Body = utf8_decode($cuerpo);
                                $mail->MsgHTML(utf8_decode($cuerpo));
                                $mail->Send();
                                $mail->ClearAddresses();

                                $query = "INSERT INTO notificaciones (idopp, tipo_notificacion, fecha) VALUES (".$row_opp['idopp'].", '2', $timeActual)";
                                $insertar = mysql_query($query,$dspp) or die(mysql_error());
                              }

                              echo "<p style='color:green'>SE ENVIO CORREO</p>";

                            }else{
                              $estatusCertificado = 10; // CERTIFICADO ACTIVO
                              echo "<p style='color:blue'>DENTRO DE FECHA</p>";
                            }
                          }else{
                            if($prorroga >= $timeActual){
                              $estatusCertificado = 12; // CERTIFICADO POR EXPIRAR
                              if(empty($row_opp['idnotificacion'])){
                                $query = "INSERT INTO notificaciones (idopp, tipo_notificacion, fecha) VALUES (".$row_opp['idopp'].", '1', $timeActual)";
                                $insertar = mysql_query($query,$dspp) or die(mysql_error());
                              }

                              echo "<p style='color:black'>CERTIFICADO POR EXPIRAR</p>";
                            }else{
                              $estatusCertificado = 11; // CERTIFICADO EXPIRADO
                              if(empty($row_opp['idnotificacion'])){
                                $query = "INSERT INTO notificaciones (idopp, tipo_notificacion, fecha) VALUES (".$row_opp['idopp'].", '3', $timeActual)";
                                $insertar = mysql_query($query,$dspp) or die(mysql_error());
                              }
                              echo "<p style='color:red'>FECHA ANTIGUA</p>";
                            }
                          }

                          $actualizar = "UPDATE opp SET estado = $estatusCertificado WHERE idopp = $row_opp[idopp]";
                          $ejecutar = mysql_query($actualizar,$dspp) or die(mysql_error());

                          $actualizar = "UPDATE certificado SET status = $estatusCertificado WHERE idcertificado = $row_opp[idcertificado]";
                          $ejecutar = mysql_query($actualizar,$dspp) or die(mysql_error());

                          ?>     
                        </td>     
                    </tr>
            
            <?php $contador++; } ?>
                </table>
            </div>
 



          <?php 
            $timeActual = "";
            $timeVencimiento = "";
            $timeRestante = "";
            $plazo = "";

            $timeActual= time();   // Obtenemos el timestamp del momento actual
            //$timeVencimiento = strtotime("2016-02-12");
            //$timeVencimiento = strtotime(); // Obtenemos timestamp de la fecha de vencimiento
           // $timeRestante = ($timeVencimiento - $timeActual);

            $plazo = 60 *(24*60*60);
            // Calculamos el número de segundos que tienen esos 3 días

        /*
            if ($timeRestante <= $plazo) {
                echo "<script>alert('Se ha enviado un correo');</script>";
            }else{
                echo "<script>alert('Faltan Dias');</script>";
            }*/

            $contador = 1;

            //$query = "SELECT opp.*, certificado.status AS 'statusCertificado', certificado.* FROM opp INNER JOIN certificado ON opp.idopp = certificado.idopp";
            //$query = "SELECT opp.*, certificado.*, certificado.status AS 'statusCertificado', status.nombre AS 'nombreStatus' FROM opp INNER JOIN certificado ON opp.idopp = certificado.idopp  INNER JOIN status ON opp.estado = status.idstatus ORDER BY vigenciafin";
            $queryConsultaCOM = "SELECT com.*, certificado.*, certificado.status AS 'statusCertificado', status.nombre AS 'nombreStatus', oc.abreviacion AS 'abreviacionOC' FROM com INNER JOIN certificado ON com.idcom = certificado.idcom INNER JOIN oc ON certificado.entidad = oc.idoc INNER JOIN status ON com.estado = status.idstatus ORDER BY vigenciafin";

            $ejecutarCOM = mysql_query($queryConsultaCOM,$dspp) or die(mysql_error());


    


            ?>

            <div class="col-xs-12" id="tablaEmpresas" style="display:none;">
                <table class="table table-bordered table-hover" style="font-size:12px;">
                    <tr><td colspan="7" class="text-center"><h4>LISTA DE EMPRESAS CON FECHA DE CERTIFICADO</h4></td></tr>
                    <tr>
                        <td>Nº</td>
                        <!--<td>ID com</td>-->
                        <!--<td>ID CERTIFICADO</td>-->
                        <td>ABREVIACION</td>
                        <td>ENTIDAD QUE OTORGÓ EL CERTIFICADO</td>
                        <td>ESTATUS EMPRESA</td>
                        <td>ESTATUS CERTIFICADO</td>
                        <td>VENCIMIENTO CERTIFICADO</td>
                        <td>¿SE ENVIO MENSAJE?</td>
                        <!--<td>CORREOS</td>
                        <td>
                            CONTACTOS
                        </td>-->

                    </tr>

                    <?php while($row_com = mysql_fetch_assoc($ejecutarCOM)){?>

                    <tr>
                        <td><?php echo $contador; ?></td>
                        <!--<td><?php echo $row_opp['idopp']; ?></td>-->
                        <!--<td class="alert alert-info"><?php echo $row_opp['idcertificado']; ?></td>-->
                        <td><?php echo $row_com['abreviacionOC']; ?></td>
                        <td><?php echo "<a href='?COM&detail&idcom=$row_com[idcom]'>$row_com[abreviacion]</a>"; ?></td>
                        <td <?php if($row_com['estado'] == 10){echo "class='alert alert-success'";}else if($row_com['estado'] == 11){ echo "class='alert alert-danger'";}else{ echo "class='alert alert-warning'";} ?>><?php echo $row_com['nombreStatus']; ?></td>
                        <td <?php if($row_com['statusCertificado'] == 10){echo "class='alert alert-success'";}else if($row_com['statusCertificado'] == 11){ echo "class='alert alert-danger'";}else{ echo "class='alert alert-warning'";} ?>><?php echo "<p>".$row_com['nombreStatus']."</p>"; ?></td>                        
                        <td>
                          <?php 
                            $fechaVigencia = date('d-m-Y', strtotime($row_com['vigenciafin']));
                            echo $fechaVigencia;
                            //echo "<br>";
                          //echo $row_com['vigenciafin']; 
                          ?>
                        </td>

                        <td>
                          <?php 

                              $timeVencimiento = strtotime($row_com['vigenciafin']);
                              $timeRestante = ($timeVencimiento - $timeActual);

                              if ($row_com['estado'] == "10" || $row_com['estado'] == "11") {
                                  if($timeVencimiento > $timeActual){
                                      if ($timeRestante <= $plazo) {

                                          $actualizar = "UPDATE opp SET estado = '16' WHERE idopp = $row_com[idopp]";
                                          $ejecutar = mysql_query($actualizar,$dspp) or die(mysql_error());

                                          $actualizar = "UPDATE certificado SET status = '16' WHERE idcertificado = $row_com[idcertificado]";
                                          $ejecutar = mysql_query($actualizar,$dspp) or die(mysql_error());


                                       /* $queryOPP = "SELECT opp.*, contacto.* FROM opp INNER JOIN contacto ON opp.idopp = contacto.idopp WHERE opp.idopp = $row_com[idopp]";
                                        $ejecutar2 = mysql_query($queryOPP,$dspp) or die(mysql_error());

                                        $destinatarioOPP = "";
                                        while($emailOPP = mysql_fetch_assoc($ejecutar2)){
                                            $destinatarioOPP .= $emailOPP['email'].',';
                                        }*/


                                        $queryOPP = "SELECT opp.*, contacto.* FROM opp INNER JOIN contacto ON opp.idopp = contacto.idopp WHERE opp.idopp = $row_com[idopp]";
                                        $ejecutar2 = mysql_query($queryOPP,$dspp) or die(mysql_error());

                                        $destinatarioOPP = "";
                        while($emailOPP = mysql_fetch_assoc($ejecutar2)){
                            $destinatarioOPP .= $emailOPP['email'].','.$emailOPP['email1'].','.$emailOPP['email2'];
                        }
                        
                        $queryEmailOc = "SELECT certificado.idcertificado, certificado.entidad, oc.* FROM certificado INNER JOIN oc ON certificado.entidad = oc.idoc WHERE idcertificado = $row_com[idcertificado]";
                        $ejecutar = mysql_query($queryEmailOc,$dspp) or die(mysql_error());
                        $emailOC = mysql_fetch_assoc($ejecutar);

                        $fechaVigencia = date('d-m-Y', strtotime($row_com['vigenciafin']));
                        $nombreOPP = $row_com['nombre'];
                        $abreviacionOPP = $row_com['abreviacion'];

                        $vigenciaFinal = $row_com['vigenciafin'];
                        $OCemail = $emailOC['email'];

                        $destinatarioOPP.= $OCemail;

                        /*echo "el nombre es:".$nombreOPP;
                        echo "<br>Fin del certificado es:".$vigenciaFinal;
                        echo "el destino es:".$destinatarioOPP;
                        echo "<br>El email del OC :".$OCemail;
                        echo "<br>El final es:".$destinatarioOPP;*/

                                //$destinatario = $emailOPP['email'];
                                //$headers .= "Bcc: yasser.midnight@gmail.com\r\n";
                                //$headers .= "Bcc: isc.jesusmartinez@gmail.com  \r\n"; 
                        //$destinatarioOPP = "yassemh@hotmail.org";

                                $asunto = "D-SPP - Aviso de Renovacion de Certificado"; 

                            $cuerpo = '
                              <html>
                              <head>
                                <meta charset="utf-8">
                              </head>
                              <body>    
                                <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
                                  <tbody>
                                    <tr>
                                      <th rowspan="1" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                                      <th scope="col" align="left" width="500"><strong><h3>Aviso de Renovación de Certificado SPP</h3></strong></th>
                                    </tr>
                                    <tr>
                                      <td style="text-align:justify; padding-top:2em" colspan="2">
                                     
                                        <p>Estimados Representantes de <strong style="color:red">'.$nombreOPP.', '.$abreviacionOPP.'</strong>:</p>
                                        
                                        <p>Por este conducto se les informa la necesidad de renovación de su Certificado SPP. La fecha de su vigencia es <strong style="color:red">'.$fechaVigencia.'</strong>, por lo que deben proceder con la evaluación anual.</p>
                                        
                                        <p>De acuerdo a los procedimientos del SPP, se puede llevar a cabo la evaluación dos meses antes de la fecha de vigencia o máximo dos meses después.  Si la evaluación se realiza dos meses después, se esperaría que el dictamen se obtuviera 4 meses después  (de la fecha de vencimiento del certificado) como plazo máximo, para obtener el dictamen positivo de parte del Organismo de Certificación.</p>
                                      
                                        <p>Queremos enfatizar que actualmente existen políticas para la suspensión y/o cancelación del certificado por lo que si ustedes no solicitan a tiempo pueden ser acreedores de una suspensión.</p>
                                        
                                        <p>Agradeciendo su atención, nos despedimos y enviamos saludos del SPP-FUNDEPPO.</p>
                                        
                                        <p>POR CUALQUIER INCONVENIENTE FAVOR DE NOTIFICARLO A FUNDEPPO-SPP al correo <strong>cert@spp.coop</strong></p>
                                      
                                      </td>
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
                                //$headers .= "Return-path: holahola@desarrolloweb.org\r\n"; 

                                //direcciones que recibián copia 
                                //$headers .= "Cc: maria@desarrolloweb.org\r\n"; 

                                //direcciones que recibirán copia oculta 
                                $headers .= "Bcc: yasser.midnight@gmail.com\r\n";
                                $headers .= "Bcc: yassemh@hotmail.org\r\n";

                                $headers .= "Bcc: cert@spp.coop \r\n"; 
                                $headers .= "Bcc:  adm@spp.coop \r\n"; 
                                $headers .= "Bcc: com@spp.coop \r\n"; 

                                mail($destinatarioOPP,$asunto,utf8_decode($cuerpo),$headers);


                                          //echo "<p style='color:green'>SE ENVIO CORREO</p>";

                                      }else{
                                          echo "<p style='color:blue'>DENTRO DE FECHA</p>";
                                      }
                                  }else{
                                      echo "<p style='color:red'>FECHA ANTIGUA</p>";
                                  }

                              }else if($row_com['estado'] == "16"){
                                echo "<p style='color:green'>SE ENVIO CORREO</p>";
                              }
                          ?>     




                            <?php 

                               /* $timeVencimiento = strtotime($row_com['vigenciafin']);
                                $timeRestante = ($timeVencimiento - $timeActual);

                                if ($row_com['estado'] == "10" || $row_com['estado'] == "11") {
                                    if($timeVencimiento > $timeActual){
                                      
                                        if ($timeRestante >= $plazo) {
                                          echo "<p style='color:blue'>DENTRO DE FECHA</p>";
                                        }
                                    }else{
                                        echo "<p style='color:red'>FECHA ANTIGUA</p>";
                                    }

                                }else if($row_com['estado'] == "16"){
                                  echo "<p style='color:green'>SE ENVIO CORREO</p>";
                                }*/
                            ?>     

                        </td>     

                    </tr>
            
            <?php $contador++; } ?>
                </table>
            </div>
        </div>







<?php if($numero >0){ ?>

  <div class="col-xs-12">
    
      <table class="table table-bordered">
        <tr>
          <th class="text-center"><h5><b>Ultima <br>Actualización</b></h5></th>
          <th class="text-center">Nombre</th>
          <th class="text-center">Cotización FUNDEPPO</th>
          <th class="text-center">Sitio WEB</th>
          <th class="text-center">Email</th>
          <th class="text-center">País</th>
          <th class="text-center">Status</th>
          <th class="text-center">Propuesta</th>
          <th class="text-center">Observaciones</th>
          <!--<th>OC</th>
          <th>Razón social</th>
          <th>Dirección fiscal</th>
          <th>RFC</th>-->
          <!--<th>Eliminar</th>-->
        </tr>

        <?php mysql_select_db($database_dspp, $dspp); ?>


        <?php $cont=0; while($registro_busqueda = mysql_fetch_assoc($ejecutar_busqueda)){ $cont++;?>
          <tr>
        <?php  $fecha = $registro_busqueda['fecha_elaboracion']; ?> 
            <td>
              <small>
                <a class="btn btn-primary" style="width:100%" href="?OC&amp;detailBlock&amp;query=<?php echo $registro_busqueda['idoc']; ?>&amp;formato=<?php echo $registro_busqueda['idsolicitud_certificacion']; ?>">
                  <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> <?php echo  date("d/m/Y", $fecha); ?>
                </a> 
              </small>         
            </td>
            <td>
              <small><?php echo $registro_busqueda['nombre']; ?></small>
            </td>
            <td class="text-center">
              <small>    
                <a href="http://d-spp.org/oc/<?echo $registro_busqueda['cotizacion_adm']?>" target="_blank" type="button"  aria-label="Left Align" <?php if(!isset($registro_busqueda['cotizacion_adm'])){echo "class='btn btn-default btn-danger' disabled";}else{echo "class='btn btn-default btn-success'";}?>>
                  <span class="glyphicon glyphicon-paperclip"></span> Descargar
                </a>  
              </small>          
            </td>

            <td>
              <small><?php if(empty($registro_busqueda['sitio_web'])){echo "Sitio Web no disponible";}else{echo $registro_busqueda['sitio_web'];} ?></small>
            </td>
            <td><small><?php echo $registro_busqueda['p1_email']; ?></small></td>
            <td><small><?php echo $registro_busqueda['pais']; ?></small></td>
            <td><small><?php echo $registro_busqueda['status']; ?></small></td>

            <td>
              <small>
                <?php 
                  if($registro_busqueda['status'] == "APROBADO"){
                 ?>
                    <button class="btn btn-success">
                      <span class="glyphicon glyphicon-check" aria-hidden="true"></span> Aceptada
                    </button>          
                <?php 
                  }else{
                 ?>
                    <button class="btn btn-default" disabled>
                      <span class="glyphicon glyphicon-check" aria-hidden="true"></span> Aceptada
                    </button>               
                 <?php 
                  }
                  ?>
              </small>
            </td>

            <td>
              <small>
                <?php if(empty($registro_busqueda['observaciones'])){ ?>
                  <button class="btn btn-default" disabled>
                    <span class="glyphicon glyphicon-list-alt"></span> Consultar
                  </button>         
                <?php }else{ ?>
                  <a class="btn btn-info" href="?OC&amp;detailBlock&amp;query=<?php echo $registro_busqueda['idoc']; ?>&amp;formato=<?php echo $registro_busqueda['idsolicitud_certificacion']; ?>">
                    <span class="glyphicon glyphicon-list-alt"></span> Consultar
                  </a>
                <?php } ?>
              </small>
            </td>
 
            <form action="" method="post">
              <td>
                <button class="btn btn-success" id="todos" name="todos" value="<?php echo $registro_busqueda['idsolicitud_certificacion']?>" type="submit">
                  <span class="glyphicon glyphicon-envelope" aria-hidden="true"></span> <small>Enviar a OCs</small>
                  
                </button>
              </td>
          <!--<td>-->



              <!--<input class="btn btn-danger" type="submit" value="Eliminar" />-->
              <input type="hidden" value="<?php echo $registro_busqueda['idsolicitud_certificacion'];?>" name="idsolicitud">
              <input type="hidden" value="OPP eliminado correctamente" name="mensaje" />
              <input type="hidden" value="<?php echo $registro_busqueda['idopp']; ?>" name="idopp" />
            </form>


          </tr>

        <?php } ?>

        <? if($cont==0){?>
        <tr><td colspan="11" class="alert alert-info" role="alert">No se encontraron registros</td></tr>

        <? }?>

      </table>


  </div>

<?php } ?>


