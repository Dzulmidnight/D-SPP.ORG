<?php require_once('Connections/dspp.php');
mysql_select_db($database_dspp, $dspp);

 ?>

<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <!-- Enjoy Hint -->

  <!--<link href="enjoyhint/enjoyhint.css" rel="stylesheet">
  <script src="enjoyhint/enjoyhint.min.js"></script>
    <!-- Enjoy Hint -->



    <title>D-SPP.ORG</title>

    <!-- Bootstrap core CSS -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="bootstrap/css/bootstrap-theme.css" rel="stylesheet">
    <link href="bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="bootstrap/css/bootstrap-theme.min.css" rel="stylesheet">


    <!--<link href="css/fileinput.css" media="all" rel="stylesheet" type="text/css" /> 
    <script src="js/fileinput.min.js" type="text/javascript"/>-->

    <!-- Custom styles for this template -->
    <!-- <link href="login.css" rel="stylesheet"> -->

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

 
  </head>
 
<div class="col-xs-12 alert alert-warning">
  
                    <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
                      <tbody>
                        <tr>
                          <th rowspan="4" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                          <th scope="col" align="left" width="280"><strong>Notificación de Estado / Status Notification ('.$fecha.')</strong></th>
                        </tr>
                        <tr>
                        <td>Se ha cargado el comprobante de pago, por favor inicie sesión en la cuenta de administrador (<a href="http://d-spp.org/?ADM">www.d-spp.org/?ADM</a>) para poder revisarlo.</td>
                        </tr>
                        <tr>
                          <td>
                            <!--<form name="ingresar" action="http://d-spp.org/formularioMail.php" method="POST" enctype="application/x-www-form-urlencoded">
                              <input type="text" name="formularioComprobante" value="1">
                              <a href="#" onclick="document.ingresar.submit()" type="Submit" style="background-color: #4CAF50;border: none;color: white;padding: 15px 32px;text-align: center;text-decoration: none;display: inline-block;font-size: 16px;" ><input type="text" name="aceptar" value="aceptar">Aceptar</a>
                              <a href="#" onclick="document.ingresar.submit()" type="Submit" style="background-color: red;border: none;color: white;padding: 15px 32px;text-align: center;text-decoration: none;display: inline-block;font-size: 16px;"><input type="text" name="denegar">Denegar</a>
                            </form>-->
                            <form action="http://d-spp.org/formularioMail.php" method="GET" enctype="application/x-www-form-urlencoded">
                              <input type="text" name="formularioComprobante" value="1">
                              <input type="text" name="aceptar" value="aceptar">
                              <input type="submit">
                            </form>
                          </td>
                        </tr>
                        
                      </tbody>
                    </table>
</div>



            <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
              <tbody>
                <tr>
                  <th rowspan="1" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                  <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">Contrato de uso del Simbolo de Pequeños Productores - SPP</span></p></th>

                </tr>


                <tr>
                  <td style="padding-top:20px;" colspan="2">
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Maxime aspernatur et quis, libero at voluptatibus quam labore sequi voluptatum ut! Praesentium aspernatur dolor rem obcaecati fugiat, commodi quas, ex rerum.
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Soluta dicta cumque autem dolores velit incidunt amet rerum magni ab illum quidem impedit rem necessitatibus distinctio placeat possimus, eligendi repellendus ipsam.
                  </td>
                </tr>
              </tbody>
            </table>

<body>
<div class="col-xs-12">
  <table class="table table-bordered">
    <tr>
      <td>idmensaje</td>
      <td>idopp</td>
      <td>asunto</td>
      <td>mensaje</td>
      <td>destinatario</td>
      <td>remitente</td>
    </tr>
    <?php 
      $query = "SELECT * FROM mensajes";
      $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
      while($registro = mysql_fetch_assoc($ejecutar)){
    ?>
    <tr>
      <td><?php echo $registro["idmensaje"]; ?></td>
      <td><?php echo $registro["idopp"]; ?></td>
      <td><?php echo $registro["asunto"]; ?></td>
      <td><?php echo $registro["mensaje"]; ?></td>
      <td><?php echo $registro["destinatario"]; ?></td>
      <td><?php echo $registro["remitente"]; ?></td>
    </tr>
    <?php 
      }
     ?>
  </table>
</div>

    
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
    // Calculamos el número de segundos que tienen esos 3 días

/*
    if ($timeRestante <= $plazo) {
        echo "<script>alert('Se ha enviado un correo');</script>";
    }else{
        echo "<script>alert('Faltan Dias');</script>";
    }*/

    $contador = 1;

    //$query = "SELECT opp.*, certificado.status AS 'statusCertificado', certificado.* FROM opp INNER JOIN certificado ON opp.idopp = certificado.idopp";
    $query = "SELECT opp.*, certificado.*, certificado.status AS 'statusCertificado', contacto.*, status.nombre AS 'nombreStatus' FROM opp INNER JOIN certificado ON opp.idopp = certificado.idopp INNER JOIN contacto ON opp.idopp = contacto.idopp INNER JOIN status ON opp.estado = status.idstatus";
    $ejecutarOPP = mysql_query($query,$dspp) or die(mysql_error());
    ?>
    <div class="col-xs-6">
      <?php     echo date("d-m-Y", 1456415100); ?>
        <table class="table table-bordered table-hover">
            <tr><td colspan="5"><h3>LISTA DE OPP CON FECHA DE CERTIFICADO</h3></td></tr>
            <tr>
                <td>Nº</td>
                <td>ID OPP</td>
                <td>ID CERTIFICADO</td>
                <td>ABREVIACION</td>
                <td>ESTATUS OPP</td>
                <td>ESTATUS CERTIFICADO</td>
                <td>VENCIMIENTO CERTIFICADO</td>
                <td>¿SE ENVIO MENSAJE?</td>
                <td>CORREOS</td>
                <td>
                    CONTACTOS
                </td>
                <?php echo strtotime("2015-07-14") ?>
                <?php echo "<br>".$timeActual; ?>
                <?php echo "<br>".$plazo; ?>

            </tr>



            <?while($row_opp = mysql_fetch_assoc($ejecutarOPP)){?>

            <tr>
                <td><?php echo $contador; ?></td>
                <td><?php echo $row_opp['idopp']; ?></td>
                <td class="alert alert-info"><?php echo $row_opp['idcertificado']; ?></td>
                <td><?php echo $row_opp['abreviacion']; ?></td>
                <td <?php if($row_opp['estado'] == 10){echo "class='alert alert-success'";}else if($row_opp['estado'] == 11){ echo "class='alert alert-danger'";}else{ echo "class='alert alert-warning'";} ?>><?php echo $row_opp['nombreStatus']; ?></td>
                <td <?php if($row_opp['statusCertificado'] == 10){echo "class='alert alert-success'";}else if($row_opp['statusCertificado'] == 11){ echo "class='alert alert-danger'";}else{ echo "class='alert alert-warning'";} ?>><?php echo "<p>".$row_opp['nombreStatus']."</p>"; ?></td>

                <td> 
                  <?php 
                    echo $row_opp['vigenciafin']; 
                    $fechaVigencia = strtotime($row_opp['vigenciafin']);
                    echo "el formato de fecha cambiado es: ".date('d/m/Y', $fechaVigencia);
                  ?>
                </td>

                <td>
                    <?php 

                        $timeVencimiento = strtotime($row_opp['vigenciafin']);
                        $timeRestante = ($timeVencimiento - $timeActual);

                        if ($row_opp['estado'] == "10" || $row_opp['estado'] == "11") {
                            if($timeVencimiento > $timeActual){
                                if ($timeRestante <= $plazo) {

                                    $actualizar = "UPDATE opp SET estado = '16' WHERE idopp = $row_opp[idopp]";
                                    $ejecutar = mysql_query($actualizar,$dspp) or die(mysql_error());

                                    $actualizar = "UPDATE certificado SET status = '16' WHERE idcertificado = $row_opp[idcertificado]";
                                    $ejecutar = mysql_query($actualizar,$dspp) or die(mysql_error());


                                 /* $queryOPP = "SELECT opp.*, contacto.* FROM opp INNER JOIN contacto ON opp.idopp = contacto.idopp WHERE opp.idopp = $row_opp[idopp]";
                                  $ejecutar2 = mysql_query($queryOPP,$dspp) or die(mysql_error());

                                  $destinatarioOPP = "";
                                  while($emailOPP = mysql_fetch_assoc($ejecutar2)){
                                      $destinatarioOPP .= $emailOPP['email'].',';
                                  }*/


                                  $queryOPP = "SELECT opp.*, contacto.* FROM opp INNER JOIN contacto ON opp.idopp = contacto.idopp WHERE opp.idopp = $row_opp[idopp]";
                                  $ejecutar2 = mysql_query($queryOPP,$dspp) or die(mysql_error());

                                  $destinatarioOPP = "";
                                  while($emailOPP = mysql_fetch_assoc($ejecutar2)){
                                      $destinatarioOPP .= $emailOPP['email'].','.$emailOPP['email1'].','.$emailOPP['email2'];
                                  }
                                  echo "el destino es:".$destinatarioOPP;

                                          //$destinatario = $emailOPP['email'];
                                          //$headers .= "Bcc: yasser.midnight@gmail.com\r\n";
                                          //$headers .= "Bcc: isc.jesusmartinez@gmail.com  \r\n"; 

                                          $asunto = "D-SPP - Aviso de Renovacion de Certificado"; 

                                      $cuerpo = '


                                        <html>
                                        <head>
                                          <meta charset="utf-8">
                                        </head>
                                        <body>
                                        
                                            <table>
                                            <tr><h3>Mensaje para la renovacion de su certificado</h3></tr>
                                              <tr>
                                                <td style="text-align:justify;" colspan="2">
                                                  FUNDEPPO publica y notifica las “Intenciones de Certificación, Registro o Autorización” basada en nuevas solicitudes de: 1) Certificación de Organizaciones de Pequeños Productores, 2) Registro de Compradores y otros actores y 3) Autorización de Organismos de Certificación, con el objetivo de informarles y recibir las eventuales objeciones contra la incorporación de los solicitantes.
                                                  Estas eventuales objeciones presentadas deben estar sustentadas con información concreta y verificable con respecto a incumplimientos de la Normatividad del SPP y/o nuestro Código de Conducta (disponibles en <a href="http://www.spp.coop/"><strong>www.spp.coop</strong></a>, en el área de Funcionamiento). Las objeciones presentadas y enviadas a <a href="cert@spp.coop"><strong>cert@spp.coop</strong></a> serán tomadas en cuenta en los procesos de certificación, registro o autorización.
                                                  Estas notificaciones son enviadas por FUNDEPPO en un lapso menor a 24 horas a partir del momento en que le llegue la solicitud correspondiente. Si se presentan objeciones antes de que el solicitante se Certifique, Registre o Autorice su tratamiento por parte del Organismo de Certificación debe ser parte de la misma evaluación documental. Si la objeción se presenta cuando el Solicitante ya esta Certificado se aplica el Procedimiento de Inconformidades del Símbolo de Pequeños Productores. Las nuevas intenciones de Certificación, Registro o Autorización, se detallan al inicio de este documento.  
                                                  <br><br>
                                                  FUNDEPPO publishes and notifies the “Certification, Registration and Authorization Intentions” based on new applications submitted for: 1) Certification of Small Producers’ Organizations, 2) Registration of Buyers and other stakeholders, and 3) Authorization of Certification Entities, with the objective of keeping you informed and receiving any objections to the incorporation of any new applicants into the system.
                                                  Any objections submitted must be supported with concrete, verifiable information regarding non-compliance with the Standards and/or Code of Conduct of the Small Producers’ Symbol (available at <a href="http://www.spp.coop/"><strong>www.spp.coop</strong></a> in the section on Operation). The objections submitted and sent to <a href="cert@spp.coop"><strong>cert@spp.coop</strong></a> will be taken into consideration during certification, registration and authorization processes.
                                                  These notifications are sent by FUNDEPPO in a period of less than 24 hours from the time a corresponding application is received. If objections are presented before getting the Certification, Registration or Authorization, the Certification Entity must incorporate them as part of the same evaluation-process. If the objection is presented when the applicant has already been certified, the SPP Dissents\' Procedure has to be applied. The new intentions for Certification, Registration and Authorization are detailed at the beginning (of this document.
                                                </td>
                                              </tr>
                                     
                                    
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

                                          mail($destinatarioOPP,$asunto,$cuerpo,$headers) ;



                                    echo "<p style='color:green'>SE ENVIO CORREO</p>";



                                }else{
                                    echo "<p style='color:blue'>NO SE HA ENVIADO CORREO</p>";
                                }
                            }else{
                                echo "<p style='color:red'>FECHA ANTIGUA</p>";
                            }
                        }
                    ?>     

                </td>     
                <td>
                    <?php echo $row_opp['email']; ?>
                </td>
                <td>
                    <?php 
                    echo $row_opp['email1'];
                    echo "<br>".$row_opp['email2'];
                     ?>
                </td>
            </tr>
    
    <?php $contador++; } ?>
        </table>
    </div>

    <?php 
    $query = "SELECT * FROM opp ORDER BY idopp";
    $ejecutar2 = mysql_query($query,$dspp) or die(mysql_error());
    $contador2 = 1;
     ?>
    <!--<div class="col-xs-6">
        <table class="table table-bordered">
            <tr><td colspan="5"><h3>LISTA DE OPP SIN CERTIFICADO</h3></td></tr>
            <tr>
                <td>Nº</td>
                <td>ID OPP</td>
                <td>ABREVIACION</td>
                <td>ESTATUS</td>
                <td>VENCIMIENTO CERTIFICADO</td>
            </tr>



            <?while($row_opp2 = mysql_fetch_assoc($ejecutar2)){?>

            <tr>
                <td><?php echo $contador2; ?></td>
                <td><?php echo $row_opp2['idopp']; ?></td>
                <td><?php echo $row_opp2['abreviacion']; ?></td>
                <td><?php echo $row_opp2['estado']; ?></td>
                <td></td>     
            </tr>
    
    <?php $contador2++; } ?>
        </table>
    </div>-->
    <?
    // Condición: Si la diferencia entre la fecha de vencimiento y la fecha actual es menor de 3 días
    /*if( ($timeVencimiento-$timeActual) < $segundos) {
        echo "<script>HA ENTRADO EN PERIODO DE VENCIMIENTO('alerta')</script>";
         echo "<br>EL TIEMPO DE VENCIMIENTO(1) ES: ".$timeVencimiento."</u> LO CUAL ES IGUAL A: <u>".date("d/m/Y", $timeVencimiento)."</u>";
         echo "<br>EL TIEMPO ACTUAL ES: ".$timeActual."</u> LO CUAL ES IGUAL <u>".date("d/m/Y", $timeActual)."</u>";
 
         $resultado = $timeVencimiento - $timeActual;
         echo "<br>EL RESULTADO ES: ".$resultado." ES IGUAL A ".date("d", $resultado)."";
         echo "<br>los segundos son: ".$segundos. " ES IGUAL A ".date("j", $segundos)."";

    }else{
          echo "<script>HA ENTRADO EN PERIODO DE VENCIMIENTO('alerta')</script>";
         echo "<script>alert('FALTAN DIAS PARA EL VENCIMIENTO $timeVencimiento - $timeActual;')</script>";
         echo "<br>EL TIEMPO DE VENCIMIENTO(2) ES: <u>".$timeVencimiento."</u> LO CUAL ES IGUAL A: <u>".date("d/m/Y", $timeVencimiento)."</u>";
         echo "<br>EL TIEMPO ACTUAL ES: <u>".$timeActual."</u> LO CUAL ES IGUAL <u>".date("d/m/Y", $timeActual)."</u>";
 
        
         echo "<br>EL RESULTADO ES: ".$resultado."lo cual es igual a <u>".date("d", $resultado)."</u>";
         echo "<br>los segundos son: ".$segundos."lo cual es igual a <u>".date("d", $segundos)."</u>";
   }*/

   ?>






</div>

</body>
</html>