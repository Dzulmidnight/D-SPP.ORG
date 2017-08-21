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
  $estatus_dspp = '4'; // COTIZACIÓN ENVIADA
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
  $updateSQL = sprintf("UPDATE solicitud_certificacion SET tipo_procedimiento = %s, cotizacion_opp = %s, estatus_dspp = %s WHERE idsolicitud_certificacion = %s",
    GetSQLValueString($procedimiento, "text"),
    GetSQLValueString($cotizacion_opp, "text"),
    GetSQLValueString($estatus_dspp, "int"),
    GetSQLValueString($idsolicitud_certificacion, "int"));
  $actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());

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

  $row_opp = mysql_query("SELECT opp.nombre, opp.abreviacion AS 'abreviacion_opp', opp.spp, opp.password, opp.email, oc.email1, oc.email2, oc.abreviacion AS 'abreviacion_oc', oc.pais AS 'pais_oc', solicitud_certificacion.contacto1_email, solicitud_certificacion.contacto2_email, solicitud_certificacion.adm1_email FROM opp INNER JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp INNER JOIN oc ON solicitud_certificacion.idoc = oc.idoc WHERE idsolicitud_certificacion = $idsolicitud_certificacion", $dspp) or die(mysql_error());
  $opp_detail = mysql_fetch_assoc($row_opp);

  $asunto = "D-SPP Cotation (Demande de certification pour les Organisations de petits producteurs";

  $cuerpo_mensaje = '
    <html>
    <head>
      <meta charset="utf-8">
    </head>
    <body>
    
      <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
        <thead>
          <tr>
            <th rowspan="4" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
            <th scope="col" align="left" width="280" ><strong>Notification de cotation / Price quote  Notification</strong></th>
          </tr>
        </thead>
        <tbody>
          <tr style="text-align:justify">
            <td colspan="2">
              <p>
                Courriel de l\'Organisme de certification / Certification Entity: <span style="color:red">'.$opp_detail['email1'].'</span>
              </p>
            </td>
          </tr>
          <tr style="text-align:justify">
            <td colspan="2">
              <p>
                <b style="color:red">'.$opp_detail['abreviacion_oc'].'</b> a envoyé la cotation correspondante à la demande de certification pour les Organisations de petits producteurs
              </p>
              <p>
                Merci d\'ouvrir une session en <a href="http://d-spp.org/">www.d-spp.org/</a>, comme OPP, pour accéder à la cotation.
              </p>
            </td>
          </tr>

          <tr style="text-align:justify">
            <td colspan="2">
              <span style="color:red">Que dois-je faire maintenant ? Vous devez "Accepter" ou "refuser3 la cotation</span>
              <ol>

                <li>
                  Vous devez ouvrir la session du système <a href="http://d-spp.org/">D-SPP</a> comme Organisation de petits producteurs.
                </li>
                <li>
                  Votre identifiant: <b style="color:red">'.$opp_detail['spp'].'</b> et votre mot de passe : <b style="color:red">'.$opp_detail['password'].'</b>
                </li>
                <li>
                  Depuis votre compte, vous devez sélectionner Demandes > Liste des demandes
                </li>
                <li>
                  Dans la liste des demandess, vous devez trouver la colonne "Cotation" et sélectionner le bouton vert (accepter cotation) ou le bouton rouge (refuser cotation).
                </li>
                <li>
                  Si vous acceptez la cotation, vous devez attendre la fin de la "Période d\'objection" (si c\'est la première fois que vous demandez la certification SPP).
                </li>
              </ol>
            </td>
          </tr> 
  
          <tr>
            <td colspan="2" style="padding-bottom:10px;">
              <hr>
              <p><b>English below</b></p>
            </td>
          </tr>
          
          <tr style="font-style: italic; text-align:justify">
            <td colspan="2">
              <p>
                <b style="color:red">'.$opp_detail['abreviacion_oc'].'</b> has sent the price quote corresponding to the Certification Application for Small Producers’ Organizations (SPOs)
              </p>
              <p>
                Please open a session as an SPO at the following link: <a href="http://d-spp.org/">www.d-spp.org/</a> in order to access the price quote.
              </p>
            </td>
          </tr>
          <tr style="font-style: italic; text-align:justify">
            <td colspan="2">
              <span style="color:red">What should I do now? You should “Accept” or “Reject” the price quote.</span>
              <ol>

                <li>
                  You should open a session in the <a href="http://d-spp.org/">D-SPP (clic aquí)</a> system as a Small Producers’ Organization (SPO).
                </li>
                <li>Your User (SPP#): <b style="color:red">'.$opp_detail['spp'].'</b> and your Password is:  <b style="color:red">'.$opp_detail['password'].'</b></li>
                <li>Within your account you should select Applications  >  Applications List.</li>
                <li>In the Applications table, you should locate the column entitled “Price Quote” and select the Green button (accept price quote) or the Red button (reject price quote).</li>
                <li>If you accept the price quote, you will need to wait until the “Objection Period” is over (if this is the first time you are applying for SPP certification).</li>
              </ol>
            </td>
          </tr> 

          <tr>
            <td colspan="2">
              <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;" border="1" width="650px">
                <tbody>
                  <tr style="font-size: 12px; text-align:center; background-color:#dff0d8; color:#3c763d;" height="50px;">
                    <td width="130px">Nom de l\'organisation / Organization name</td>
                    <td width="130px">Pays / Country</td>
                    <td width="130px">Organisme de certification / Certification Entity</td>
                    <td width="130px">Date d\'envoi / Shipping Date</td>
                 
                    
                  </tr>
                  <tr style="font-size: 12px; text-align:justify">
                    <td style="padding:10px;">
                      '.$_POST['nombre'].' - ('.$opp_detail['abreviacion_opp'].')
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
            <td coslpan="2">En cas de doute ou de question, merci de contacter: soporte@d-spp.org</td>
          </tr>
        </tbody>
      </table>

    </body>
    </html>
  ';

  $mail->AddAddress($_POST['email']);
  $mail->AddAddress($_POST['contacto1_email']);
  $mail->AddBCC($spp_global);
  if(!empty($opp['email1'])){
    //$mail->AddCC($oc['email1']);
      $token = strtok($opp['email1'], "\/\,\;");
      while ($token !== false)
      {
        $mail->AddCC($token);
        $token = strtok('\/\,\;');
      }

  }
  if(!empty($opp['email2'])){
    //$mail->AddCC($oc['email2']);
      $token = strtok($opp['email2'], "\/\,\;");
      while ($token !== false)
      {
        $mail->AddCC($token);
        $token = strtok('\/\,\;');
      }

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


  $mensaje = "La cotation a été envoyée à l'OPP";
}
//****** TERMINA ENVIAR COTIZACION *******///

  ////INICIA INGRESAR LAS OBSERVACIONES REALIZADAS
if(isset($_POST['agregar_observaciones']) && $_POST['agregar_observaciones'] == 1){
  $idsolicitud_certificacion = $_POST['idsolicitud_certificacion'];


  $updateSQL = sprintf("UPDATE solicitud_certificacion SET observaciones = %s WHERE idsolicitud_certificacion = %s",
    GetSQLValueString($_POST['observaciones_solicitud'], "text"),
    GetSQLValueString($idsolicitud_certificacion, "int"));
  $actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());

  $row_informacion = mysql_query("SELECT opp.nombre, opp.abreviacion AS 'abreviacion_opp', opp.spp, opp.password, opp.email, oc.email1, oc.email2, oc.abreviacion AS 'abreviacion_oc', solicitud_certificacion.contacto1_email, solicitud_certificacion.contacto2_email, solicitud_certificacion.adm1_email FROM opp INNER JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp INNER JOIN oc ON solicitud_certificacion.idoc = oc.idoc WHERE idsolicitud_certificacion = $idsolicitud_certificacion", $dspp) or die(mysql_error());
  $informacion = mysql_fetch_assoc($row_informacion);
  
  $asunto = "D-SPP | Observations demande de certification SPP";

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
            <th scope="col" align="left" width="280" ><strong>Observations faites sur la demande de certification SPP / Observations SPP Certification Application</strong></th>
          </tr>
          <tr>
            <td align="left" style="color:#ff738a;">
              <p>Organisme de certification: '.$informacion['abreviacion_oc'].'</p>
              <p>Courrier de l\'organisme de certification / Certification Entity: '.$informacion['email1'].'</p>
            </td>
          </tr>

          <tr>
            <td aling="left" style="text-align:justify">
              Ci-dessous, les différentes observations faites sur votre demande de certification SPP
            </td>
          </tr>
          <tr>
            <td aling="left" style="text-align:justify">
            Following is a list of observations regarding your SPP Certification Application. Please correct and/or complement your application, in order for the certification process to continue.
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
                    <span style="color:red">Que dois-je faire maintenant?</span>
                    <ol>
                      <li>
                        Vous devez ouvrir la session du système <a href="http://d-spp.org/">D-SPP</a> comme Organisation de petits producteurs.
                      </li>
                      <li>
                        Votre identifiant (#SPP): <span style="color:red">'.$informacion['spp'].'</span> et votre mot de passe : <span style="color:red">'.$informacion['password'].'</span>
                      </li>
                      <li>
                        Dans votre compte, vous devez sélectionner <span style="color:red">"Demandes"</span> > <span style="color:red">Demandes d\'inscription</span>
                      </li>
                      <li>
                        Dans le tableau des demandes, vous devez trouver la colonne <span style="color:red">"Actions"</span> et sélectionner <span style="color:red">"CONSULTER"</span>
                      </li>
                      <li>
                        En cliquant sur "Consulter", vous pourrez visualiser votre demande de certification "qui peut être modifiée.
                      </li>
                      <li>
                        Une fois que vous avez effectué les modifications correspondantes, vous devez cliquer sur le bouton <span style="color:red">"Mettre à jour la demande"</span> au début de votre demande
                      </li>
                    </ol>
                  </td>
                </tr> 

                <tr>
                  <td colspan="2">
                    <span style="color:red">What should I do now?</span>
                    <ol>
                      <li>
                        You should open a session in the <a href="http://d-spp.org/">D-SPP (click here)</a> system as a Small Producers’ Organization (SPO).
                      </li>
                      <li>
                        Your User (#SPP) is: <span style="color:red">'.$informacion['spp'].'</span> and your password is: <span style="color:red">'.$informacion['password'].'</span> de su cuenta.
                      </li>
                      <li>
                        Within your account, you should select “Applications”  >  Applications List.
                      </li>
                      <li>
                        In the Applications table, you should locate the column entitled “<span style="color:red">Actions”</span> and select the <span style="color:red">“CONSULT”</span> button.
                      </li>
                      <li>
                        If you click on “Consult,” you will be able to see your Certification Application, which may be modified.
                      </li>
                      <li>
                        After making the corresponding changes, you should click on the <span style="color:red">“Update Application”</span> button at the top of your Application.
                      </li>
                    </ol>
                  </td>
                </tr>

                <tr>
                  <td colspan="2">En cas de doute ou de question, merci d\'écrire à soporte@d-spp.org</td>
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
    echo "<script>alert('Courrier envoyé avec succès');location.href ='javascript:history.back()';</script>";
  }else{
    $mail->ClearAddresses();
    echo "<script>alert('Erreur. Le courrier n'a pas pu être envoyé, merci de contacter l'administrateur : soporte@d-spp.org');location.href ='javascript:history.back()';</script>";
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
        <h3 class="text-center">Demande de certification pour les Organisations de Petits Producteurs</h3>
      </div>


      <div class="col-md-12 text-center alert alert-success" style="padding:7px;"><b>DONNÉES GÉNÉRALES</b></div>

      <div class="col-lg-12 alert alert-info" style="padding:7px;">
        <div class="col-md-12">
          <!--<div class="col-xs-4">
            <b>ENVAR AL OC (selecciona el OC al que deseas enviar la solicitud):</b>
            <input type="text" class="form-control" value="<?php echo $solicitud['abreviacionOC']; ?>" readonly>
          </div>-->
          <div class="col-md-4">
            <b>AJOUTER DES OBSERVATIONS</b>
            <button type="button" class="btn btn-primary" style="width:100%" data-toggle="modal" data-target="<?php echo "#observaciones".$_GET['IDsolicitud']; ?>">Ajouter des observations</button>
          </div>

          <div class="col-xs-4">
            <b>TYPE DE DEMANTE</b>
            <input type="text" class="form-control" value="<?php echo $solicitud['tipo_solicitud']; ?>"readonly>
            <button type="submit" class="btn btn-warning form-control" style="color:white" name="guardar_cambios" value="1">
              <span class="glyphicon glyphicon-refresh" aria-hidden="true"></span>Mettre à jour la demande
            </button>
            <!--<input type="submit" style="color:white" class="btn btn-warning form-control" value="Actualizar Solicitud">
            <input type="hidden" name="guarda_cambios" value="1">-->

          </div>
          <div class="col-md-4">
            <?php 
            if(empty($solicitud['cotizacion_opp'])){
            ?>
              <b>CHARGEMENT DES COTATIONS</b>
              <input type="file" class="form-control" id="cotizacion_opp" name="cotizacion_opp"> 
              <input type="hidden" name="idoc" value="<?php echo $solicitud['idoc']; ?>"> 
              <button class="btn btn-sm btn-success form-control" style="color:white" id="enviar_cotizacion" name="enviar_cotizacion" type="submit" value="1" onclick="return validar()">
                <span class="glyphicon glyphicon-envelope" aria-hidden="true"></span> Envoyer cotation
              </button>
              <!--<button type="submit" class="btn btn-success form-control" style="color:white" name="enviar_cotizacion" value="Enviar"><span class="glyphicon glyphicon-envelope" aria-hidden="true" onclick="return validar()"></span> Enviar Cotización</button>-->

            <?php 
            }else{
              echo "<b style='font-size:14px;'>Le cotation a déjà été soumis</b>";
            }
             ?>
          </div>

        </div>
      </div>
      <div class="col-xs-12 text-center">
        <div class="row">
      <h4>Procédure de certification <br>(<small>réalisée par l'OC</small>)</h4>
        </div>
      </div>
      <div class="col-xs-3 text-center">
        <div class="row">
          <div class="col-xs-12">
            <p style="font-size:10px;"><b>Documentaire simplifié</b></p> 
          </div>       
          <div class="col-xs-12">
            <input type="radio" data-on-color="success" data-off-color="danger" data-size="small" name="procedimiento" value='DOCUMENTAL "ACORTADO"' <?php if($solicitud['tipo_procedimiento'] == 'DOCUMENTAL "ACORTADO"'){ echo "checked"; } ?>>

          </div>                        
        </div>
      </div>
      <div class="col-xs-3 text-center">
        <div class="row">
          <div class="col-xs-12">
            <p style="font-size:10px;"><b>Documentaire normal</b></p> 
          </div>
          <div class="col-xs-12">
            <input type="radio" data-on-color="success" data-off-color="danger" data-size="small" name="procedimiento" value='DOCUMENTAL "NORMAL"' <?php if($solicitud['tipo_procedimiento'] == 'DOCUMENTAL "NORMAL"'){ echo "checked"; } ?>>

          </div>                
        </div>
      </div>
      <div class="col-xs-3 text-center">
        <div class="row">
          <div class="col-xs-12">
            <p style="font-size:10px;"><b>Complet sur site</b></p>  
          </div>
          <div class="col-xs-12">
            <input type="radio" data-on-color="success" data-off-color="danger" data-size="small" name="procedimiento" value='COMPLETO "IN SITU"' <?php if($solicitud['tipo_procedimiento'] == 'COMPLETO "IN SITU"'){ echo "checked"; } ?>>

          </div>                
        </div>
      </div>
      <div class="col-xs-3 text-center">
        <div class="row">
          <div class="col-xs-12">
            <p style="font-size:10px;"><b>Complet à distance</b></p>  
          </div>
          <div class="col-xs-12">
            <input type="radio" data-on-color="success" data-off-color="danger" data-size="small" name="procedimiento" value='COMPLETO "A DISTANCIA"' <?php if($solicitud['tipo_procedimiento'] == 'COMPLETO "A DISTANCIA"'){ echo "checked"; } ?>>

          </div>                
        </div>
      </div> 

      <!------ INICIA INFORMACION GENERAL Y DATOS FISCALES ------>
      <div class="col-lg-12">
        <div class="col-md-6">
          <div class="col-md-12 text-center alert alert-warning" style="padding:7px;">INFORMATIONS GENERALES</div>
          <label for="fecha_elaboracion">DATE DE REALISATION</label>
          <input type="text" class="form-control" id="fecha_elaboracion" name="fecha_elaboracion" value="<?php echo date('Y-m-d', time()); ?>" readonly>  

          <label for="spp">CODE D’IDENTIFICATION SPP (#SPP):</label>
          <input type="text" class="form-control" id="spp" name="spp" value="<?php echo $solicitud['spp_opp']; ?>" readonly>

          <label for="nombre">DENOMINATION SOCIALE COMPLETE DE L’ORGANISATION DE PETITS PRODUCTEURS :</label>
          <textarea name="nombre" id="nombre" class="form-control" readonly><?php echo $solicitud['nombre']; ?></textarea>

          <label for="pais">PAYS:</label>
          <input type="text" class="form-control" id="pais" name="pais" value="<?php echo $solicitud['pais']; ?>" readonly>

          <label for="direccion_fisica">ADRESSE COMPLETE DU SIEGE SOCIAL (RUE, VILLE, COMPLEMENT D’ADRESSE, CODE POSTAL, REGION) :</label>
          <textarea name="direccion_fisica" id="direccion_fisica"  class="form-control" readonly><?php echo $solicitud['direccion_oficina']; ?></textarea>

          <label for="email">ADRESSE MAIL::</label>
          <input type="text" class="form-control" id="email" name="email" value="<?php echo $solicitud['email']; ?>" readonly>

          <label for="email">TELEPHONE (INDICATIF + NUMERO):</label>
          <input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo $solicitud['telefono']; ?>" readonly>  

          <label for="sitio_web">SITE WEB:</label>
          <input type="text" class="form-control" id="sitio_web" name="sitio_web" value="<?php echo $solicitud['sitio_web']; ?>" readonly>

        </div>

        <div class="col-md-6">
          <div class="col-md-12 text-center alert alert-warning" style="padding:7px;">INFORMATIONS POUR LA FACTURATION</div>

          <label for="razon_social">REGISTRE DU COMMERCE</label>
          <input type="text" class="form-control" id="razon_social" name="razon_social" value="<?php echo $solicitud['razon_social']; ?>" readonly>

          <label for="direccion_fiscal">DOMICILIATION</label>
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
          <div class="col-md-12 text-center alert alert-warning" style="padding:7px;">PERSONNE(S) A CONTACTER :</div>

          <label for="persona1">PERSONNE(S) A CONTACTER :</label>
          <input type="text" class="form-control" id="persona1" value="<?php echo $solicitud['contacto1_nombre']; ?>"  readonly>
          <input type="text" class="form-control" id="" value="<?php echo $solicitud['contacto2_nombre']; ?>" placeholder="Nombre Persona 2" readonly> 

          <label for="cargo">FONCTION(S)</label>
          <input type="text" class="form-control" id="cargo" value="<?php echo $solicitud['contacto1_cargo']; ?>" placeholder="* Cargo Persona 1" readonly>
          <input type="text" class="form-control" id="" value="<?php echo $solicitud['contacto2_cargo']; ?>" palceholder="Cargo Persona 2" readonly>

          <label for="email">ADRESSE MAIL DES PERSONNES A CONTACTER:</label>
          <input type="email" class="form-control" name="contacto1_email" id="email" value="<?php echo $solicitud['contacto1_email']; ?>" placeholder="* Email Persona 1" readonly>
          <input type="email" class="form-control" name="contacto2_email" id="" value="<?php echo $solicitud['contacto2_email']; ?>" placeholder="Email Persona 2" readonly>

          <label for="telefono">TELEPHONE</label>
          <input type="text" class="form-control" id="telefono" value="<?php echo $solicitud['contacto1_telefono']; ?>" placeholder="* Telefono Persona 1" readonly>
          <input type="text" class="form-control" id="" value="<?php echo $solicitud['contacto2_telefono']; ?>" placeholder="Telefono Persona 2" readonly>

        </div>

        <div class="col-md-6">
          <div class="col-md-12 text-center alert alert-warning" style="padding:7px;">RESPONSABLE DU SERVICE ADMINISTRATIF </div>

          <label for="persona_adm">RESPONSABLE DU SERVICE ADMINISTRATIF </label>
          <input type="text" class="form-control" id="persona_adm" value="<?php echo $solicitud['adm1_nombre']; ?>" placeholder="Nombre Persona 1" readonly>
          <input type="text" class="form-control" id="" value="<?php echo $solicitud['adm2_nombre']; ?>" placeholder="Nombre Persona 2" readonly>

          <label for="email_adm">ADRESSE MAIL DU SERVICE ADMINISTRATIF</label>
          <input type="email" class="form-control" id="email_adm" value="<?php echo $solicitud['adm1_email']; ?>" placeholder="Email Persona 1" readonly>
          <input type="email" class="form-control" id="" value="<?php echo $solicitud['adm2_email']; ?>" placeholder="Email Persona 2" readonly>

          <label for="telefono_adm">TELEPHONE</label>
          <input type="text" class="form-control" id="telefono_adm" value="<?php echo $solicitud['adm1_telefono']; ?>" placeholder="Telefono Persona 1" readonly>
          <input type="text" class="form-control" id="" value="<?php echo $solicitud['adm2_telefono']; ?>" placeholder="Telefono Persona 2" readonly>
        </div>
      </div>
      <!------ FIN INFORMACION CONTACTOS Y AREA ADMINISTRATIVA ------>



      <!------ INICIA INFORMACION DATOS DE OPERACIÓN ------>


      <div class="col-lg-12">
        <div class="col-md-12">
          <label for="resp1">NOMBRE DE MEMBRES PRODUCTEURS:</label>
          <input type="number" step="any" class="form-control" id="resp1" name="resp1" value="<?php echo $solicitud['resp1']; ?>" >

          <label for="resp2">NOMBRE DE MEMBRES PRODUCTEURS DU (DES) PRODUIT(S) A INCLUIRE DANS LA CERTIFICATION :</label>
          <input type="text" class="form-control" id="resp2" name="resp2" value="<?php echo $solicitud['resp2']; ?>" >

          <label for="resp3">VOLUME(S) DE PRODUCTION TOTALE PAR PRODUIT (UNITE DE MESURE) :</label>
          <input type="text" class="form-control" id="resp3" name="resp3" value="<?php echo $solicitud['resp3']; ?>" >
          
          <label for="resp4">TAILLE MAXIMALE DE L’UNITE DE PRODUCTION PAR PRODUCTEUR DU (DES) PRODUIT(S) A INCLURE DANS LA CERTIFICATION : </label>
          <input type="text" class="form-control" id="resp4" name="resp4" value="<?php echo $solicitud['resp4']; ?>" >



        </div>
      </div>

      <div class="col-md-12 text-center alert alert-success" style="padding:7px;">INFORMATIONS SUR LE TYPE D’OPERATION</div>

      <div class="col-lg-12">
        <div class="col-md-12">
          <label for="op_preg1">
            1.  INDIQUEZ-S’IL S’AGIT D’UNE ORGANISATION DE PETITS PRODUCTEURS DE 1er, 2eme, 3eme OU 4eme NIVEAU, AINSI QUE LE NOMBRE D’OPP DE 3eme, 2eme OU 1er NIVEAU ET LE NOMBRE DE COMMUNAUTES, DE ZONES OU DE GROUPES DE TRAVAIL DONT VOUS DISPOSEZ : 
          </label>
          <textarea name="op_preg1" id="op_preg1" class="form-control" rows="2"><?php echo $solicitud['op_preg1']; ?></textarea>

          <div class="col-xs-3">
            <label for="preg1_1">
              1.3: NOMBRE D’OPP DE 3eme NIVEAU:
            </label>
            <textarea class="form-control" name="preg1_1" id="preg1_1" rows="3"><?php echo $solicitud['preg1_1']; ?></textarea>
           
          </div>
          <div class="col-xs-3">
            <label for="preg1_2">
              1.2: NOMBRE D’OPP DE 2eme NIVEAU:
            </label>
            <textarea class="form-control" name="preg1_2" id="preg1_2" rows="3"><?php echo $solicitud['preg1_2']; ?></textarea>
           
          </div>
          <div class="col-xs-3">
            <label for="preg1_3">
              1.3: NOMBRE D’OPP DE 1er NIVEAU:
            </label>
            <textarea class="form-control" name="preg1_3" id="preg1_3" rows="3"><?php echo $solicitud['preg1_3']; ?></textarea>

          </div>
          <div class="col-xs-3">
            <label for="preg1_4">
              1.4: NOMBRE DE COMMUNAUTES, DE ZONES OU DE GROUPES DE TRAVAIL:
            </label>
            <textarea class="form-control" name="preg1_4" id="preg1_4" rows="3"><?php echo $solicitud['preg1_4']; ?></textarea>

          </div>

          <label for="op_preg2">
            2.  INDIQUEZ QUEL(S) PRODUIT(S) VOUS SOUHAITEZ INCLURE DANS LA CERTIFICATION DU SYMBOLE DES PETITS PRODUCTEURS POUR LE(S) QUEL (S) L’ORGANISME DE CERTIFICATION REALIZERA L’EVALUATION
          </label>
          <textarea name="op_preg2" id="op_preg2" class="form-control"><?php echo $solicitud['op_preg2']; ?></textarea>

          <label for="op_preg3">
            3. INDIQUEZ SI VOTRE ORGANISATION SOUHAITE INCLURE UNE QUALIFICATION OPTIONNELLE POUR UNE UTILISATION COMPLEMENTAIRE AVEC LE LOGO GRAPHIQUE DU SYMBOLE DES PETITS PRODUCTEURS. <sup>4</sup>
          </label>
          <input type="text" class="form-control" id="op_preg3" name="op_preg3" value="<?php echo $solicitud['op_preg3']; ?>">

          <div >
            <label for="alcance_opp">
              4. MARQUEZ D’UNE CROIX L’ACTIVITE EXERCEE PAR L’ORGANISATION DES PETITS PRODUCTEURS : 
            </label>
          </div>
          
          <div class="col-xs-4">
            <label>PRODUCTION</label>
            <input type="checkbox" name="produccion" class="form-control" value="1" <?php if($solicitud['produccion']){ echo 'checked';} ?>>
          </div>
          <div class="col-xs-4">
            <label>TRANSFORMATION</label>
            <input type="checkbox" name="procesamiento" class="form-control" value="1" <?php if($solicitud['procesamiento']){ echo 'checked';} ?>>
          </div>
          <div class="col-xs-4">
            <label>EXPORTATION</label>
            <input type="checkbox" name="exportacion" class="form-control" value="1" <?php if($solicitud['exportacion']){ echo 'checked';} ?>>
          </div>

          <label for="op_preg5">
            5.  INDIQUEZ SI VOUS UTILISEZ EN SOUS-TRAITANCE LES SERVICES D’USINES DE TRANSFORMATION, D’ENTREPRISES DE COMMERCIALISATION OU D’ENTREPRISES D’IMPORT/EXPORT, LE CAS ECHEANT, MENTIONNEZ LE TYPE DE SERVICE REALISE. 
          </label>
          <textarea name="op_preg5" id="op_preg5" class="form-control"><?php echo $solicitud['op_preg5']; ?></textarea>

          <label for="op_preg6">
            6.  SI VOUS SOUS-TRAITEZ DES SERVICES A DES USINES DE TRANSFORMATION, A DES ENTREPRISES DE COMMERCIALISATION OU A DES ENTREPRISES D’IMPORT/EXPORT, INDIQUEZ SI CELLES-CI SONT ENREGISTREES, EN COURS D’ENREGISTREMENT SOUS LE PROGRAMME DU SPP OU SI ELLES SERONT CONTROLEES AU TRAVERS DE L’ORGANISATION DE PETITS PRODUCTEURS.<sup>5</sup>
            <br>
            <small><sup>5</sup> Voir le document ‘Directives Générales du Système SPP’ dans sa dernière version.</small>
          </label>
          <textarea name="op_preg6" id="op_preg6" class="form-control"><?php echo $solicitud['op_preg6']; ?></textarea>

          <label for="op_preg7">
            7.  EN PLUS DE VOTRE SIEGE SOCIAL, INDIQUEZ LE NOMBRE DE CENTRES DE COLLECTE, DE TRANSFORMATION OU DE BUREAUX SUPPLEMENTAIRES QUE VOUS POSSEDEZ.
          </label>
          <textarea name="op_preg7" id="op_preg7" class="form-control"><?php echo $solicitud['op_preg7']; ?></textarea>

          <label for="op_preg8">
            8.  EST-CE QUE VOUS DISPOSEZ D’UN SYSTEME DE CONTROLE INTERNE AFIN DE RESPECTER LES CRITERES DE LA NORME GENERALE DU SYMBOLE DES PETITS PRODUCTEURS? DANS CE CAS VEUILLEZ EXPLIQUER.
          </label>
          <textarea name="op_preg8" id="op_preg8" class="form-control"><?php echo $solicitud['op_preg8']; ?></textarea>
          <p class="alert alert-info">9. REMPLIR LE TABLEAU DE VOS CERTIFICATIONS, (EXEMPLE: EU, NOP, JASS, FLO, etc.)</p>

          <table class="table table-bordered" id="tablaCertificaciones">
            <tr>
              <td>CERTIFICATION</td>
              <td>CERTIFICATEUR</td>
              <td>ANNEE DE LA CERTIFICATION</td>
              <td>A-T-ELLE ETE INTERROMPUE?</td>
            </tr>
            <?php 
            $query_certificacion_detalle = "SELECT * FROM certificaciones WHERE idsolicitud_certificacion = $idsolicitud_certificacion";
            $certificacion_detalle = mysql_query($query_certificacion_detalle, $dspp) or die(mysql_error());
            $contador = 0;
            while($row_certificacion = mysql_fetch_assoc($certificacion_detalle)){
            ?>
              <tr class="text-center">
                <td><input type="text" class="form-control" name="certificacion[]" id="exampleInputEmail1" placeholder="CERTIFICACIÓN" value="<?echo $row_certificacion['certificacion']?>"></td>
                <td><input type="text" class="form-control" name="certificadora[]" id="exampleInputEmail1" placeholder="CERTIFICADORA" value="<?echo $row_certificacion['certificadora']?>"></td>
                <td><input type="text" class="form-control" name="ano_inicial[]" id="exampleInputEmail1" placeholder="AÑO INICIAL" value="<?echo $row_certificacion['ano_inicial']?>"></td>
                <td><input type="text" class="form-control" name="interrumpida[]" id="exampleInputEmail1" placeholder="¿HA SIDO INTERRUMPIDA?" value="<?echo $row_certificacion['interrumpida']?>"></td>
                <input type="hidden" name="idcertificacion[]" value="<?echo $row_certificacion['idcertificacion']?>">
              </tr>
            <?php 
              $contador++; 
            } 
            ?> 
          </table>  

          <label for="op_preg10">
            10.  PARMI LES CERTIFICATIONS DONT VOUS DISPOSEZ ET LORS DE LEUR PLUS RECENTE EVALUATION INTERNE ET EXTERNE, COMBIEN DE NON CONFORMITES ONT ETE IDENTIFIEES? CELLES-CI ONT-ELLES ETE RESOLUES? QUEL EST LEUR ETAT ACTUEL?  </label>
          <textarea name="op_preg10" id="op_preg10" class="form-control"><?php echo $solicitud['op_preg10']; ?></textarea>

          <p for="op_preg11">
            <b>11.  SUR L’ENSEMBLE DE VOS VENTES, QUEL EST LE POURCENTAGE REALISE SOUS LES CERTIFICATIONS BIOLOGIQUES, DU COMMERCE EQUITABLE ET / OU DU SYMBOLE DES PETITS PRODUCTEURS ?</b>
            <i>(* Entrez uniquement la quantité, le nombre entier ou les décimales)</i>
            <div class="col-lg-12">
              <div class="row">
                <div class="col-xs-3">
                  <label for="organico">% BIOLOGIQUES</label>
                  <input type="number" step="any" class="form-control" id="organico" name="organico" value="<?php echo $solicitud['organico']; ?>" placeholder="Ej: 0.0" readonly>
                </div>
                <div class="col-xs-3">
                  <label for="comercio_justo">% COMMERCE EQUITABLE</label>
                  <input type="number" step="any" class="form-control" id="comercio_justo" name="comercio_justo" value="<?php echo $solicitud['comercio_justo']; ?>" placeholder="Ej: 0.0" readonly>
                </div>
                <div class="col-xs-3">
                  <label for="spp">SYMBOLE DES PETITS PRODUCTEURS</label>
                  <input type="number" step="any" class="form-control" id="spp" name="spp" value="<?php echo $solicitud['spp']; ?>" placeholder="Ej: 0.0" readonly>
                </div>
                <div class="col-xs-3">
                  <label for="otro">SANS CERTIFICAT</label>
                  <input type="number" step="any" class="form-control" id="otro" name="sin_certificado" value="<?php echo $solicitud['sin_certificado']; ?>" placeholder="Ej: 0.0" readonly> 
                </div>
              </div>
            </div>
          </p>
          
          <p><b>12 - 13. AVEZ-VOUS REALISE DES VENTES SOUS LE SPP DURANT LE CYCLE DE CERTIFICATION ANTERIEUR ? </b></p>
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
                      <p class="alert alert-danger">Aucune réponse n'a été fournie.</p>
                    <?php 
                      }else if($solicitud['op_preg13'] == "HASTA $3,000 USD"){
                     ?>
                      <p class="alert alert-info">Jusqu’à 3.000 USD</p>
                    <?php 
                      }else if($solicitud['op_preg13'] == "ENTRE $3,000 Y $10,000 USD"){
                     ?>
                     <p class="alert alert-info">Entre 3.000 et 10.000 USD</p>
                    <?php 
                      }else if($solicitud['op_preg13'] == "ENTRE $10,000 A $25,000 USD"){
                     ?>
                     <p class="alert alert-info">De 10.000 à 25.000 USD</p>
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
                    <p class='text-center alert alert-danger'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span> Non</p>
                  </div>
                
                <?php         
                  }
                ?>
          </div>
              
          <label for="op_preg14">
            14. DATE ESTIMEE DE DEBUT D’UTILISATION DU SYMBOLE DES PETITS PRODUCTEURS:
          </label>
          <input type="text" class="form-control" id="op_preg14" name="op_preg14" value="<?php echo $solicitud['op_preg14']; ?>">

          <p>
            <b>15.  PRESENTER EN ANNEXE UN CROQUIS GENERAL DE VOTRE OPP EN INDIQUANT LES ZONES OCCUPEES PAR VOS MEMBRES.</b>
          </p>
          <?php 
          if(empty($solicitud['op_preg15'])){
            echo "<p class='alert alert-danger' style='padding:7px;'>No Disponible</p>";
          }else{
          ?>
            <a class="btn btn-success" href="<?echo $solicitud['op_preg15']?>" target="_blank"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Descargar Croquis</a>  
          <?php
          }
           ?>
          
        </div>
      </div>

      <!------ FIN INFORMACION DATOS DE OPERACIÓN ------>

      <div class="col-md-12 text-center alert alert-success" style="padding:7px;">INFORMATIONS SUR LES PRODUITS POUR LESQUELS VOUS DEMANDEZ A UTILISER LE SYMBOLE<sup>6</sup></div>
      <div class="col-lg-12">
        <table class="table table-bordered" id="tablaProductos">
          <tr>
            <td>Produit</td>
            <td>Volume Total Estimé à Commercialiser</td>
            <td>Produit Finit</td>
            <td>Matière Première</td>
            <td>Pays de Destination</td>
            <td>Marque Propre</td>
            <td>Marque d’un Client</td>
            <td>Pas encore de client</td>          
          </tr>
          <?php 
          $query_producto_detalle = "SELECT * FROM productos WHERE idsolicitud_certificacion = $idsolicitud_certificacion";
          $producto_detalle = mysql_query($query_producto_detalle, $dspp) or die(mysql_error());
          $contador = 0;
          while($row_producto = mysql_fetch_assoc($producto_detalle)){
          ?>
            <tr>
              <td>
                <input type="text" class="form-control" name="producto[]" id="exampleInputEmail1" placeholder="Produit" value="<?echo $row_producto['producto']?>">
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
                <input type="text" class="form-control" name="materia[]" id="exampleInputEmail1" placeholder="Materia" value="<?echo $row_producto['materia']?>">
              </td>
              <td>
                <input type="text" class="form-control" name="destino[]" id="exampleInputEmail1" placeholder="Destino" value="<?echo $row_producto['destino']?>">
              </td>
              <td>
                <?php 
                  if($row_producto['marca_propia'] == 'SI'){
                    echo "SI <input type='radio'  name='marca_propia".$contador."' value='SI' checked><br>";
                  }else{
                    echo "SI <input type='radio'  name='marca_propia".$contador."' value='SI'><br>";
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
                    echo "SI <input type='radio'  name='marca_cliente".$contador."' value='SI' checked><br>";
                  }else{
                    echo "SI <input type='radio'  name='marca_cliente".$contador."' value='SI'><br>";
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
                    echo "SI <input type='radio'  name='sin_cliente".$contador."' value='SI' checked><br>";
                  }else{
                    echo "SI <input type='radio'  name='sin_cliente".$contador."' value='SI'><br>";
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
              <h6><sup>6</sup> L’information fournie dans cette section sera traitée en toute confidentialité. Veuillez insérer des colonnes supplémentaires si nécessaire. </h6>
            </td>
          </tr>
        </table>
      </div>

      <div class="col-lg-12 text-center alert alert-success" style="padding:7px;">
        <b>COMPROMISOS</b>
      </div>
      <div class="col-lg-12 text-justify">
        <p>1. Par l’envoi de cette demande, vous manifestez le souhait de recevoir une proposition d’enregistrement.</p> 
        <p>2. Le processus d’enregistrement débutera dès réception du paiement.</p>
        <p>3. L’envoi et la réception de cette demande ne garantissent pas l’acceptation de l’enregistrement.</p>
        <p>4. Connaître et respecter toutes les exigences de la Norme Générale du Symbole des Petits Producteurs qui vous    sont appliquées en qualité d’Organisations de Petits Producteurs, tant critiques que minima, indépendamment du type d’évaluation réalisée.</p>
      </div>
      <div class="col-lg-12">
        <label for="responsable">
          <p style="font-size:14px;"><strong>Nom de la personne responsable de la véracité des informations fournies et qui assurera le suivi de cette demande de la part du demandeur : </strong></p>
        </label>
        <input type="text" class="form-control" id="responsable" value="<?php echo $solicitud['responsable']; ?>" > 
        <input type="hidden" name="fecha_registro" value="<?php echo $solicitud['fecha_registro'] ?>">
        <input type="hidden" name="idopp" value="<?php echo $solicitud['idopp']; ?>">

        <p>
          <b>Organisme de certification qui reçoit la demande:</b>
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
          <h4 class="modal-title" id="myModalLabel">Ajouter des observations sur la demande</h4>
        </div>
        <div class="modal-body">
          <textarea name="observaciones_solicitud" id="" class="textareaMensaje" cols="30" rows="10"></textarea>
        </div>

        <div class="modal-footer">
          <input type="hidden" name="tipo_solicitud" value="<?php echo $solicitud['tipo_solicitud']; ?>">
          <input type="hidden" name="idsolicitud_certificacion" value="<?php echo $_GET['IDsolicitud']; ?>">
          <input type="hidden" name="agregar_observaciones" value="1">
          <button type="submit" class="btn btn-success">Envoyer des observations</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
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
      alert("La cotation de l'OPP n'a pas été téléchargée");
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
      alert("Vous devez sélectionner une procédure de certification");
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

    cell1.innerHTML = '<input type="text" class="form-control" name="certificadora['+contador+']" id="exampleInputEmail1" placeholder="CERTIFICACIÓN">';
    cell2.innerHTML = '<input type="text" class="form-control" name="certificacion['+contador+']" id="exampleInputEmail1" placeholder="CERTIFICADORA">';
    cell3.innerHTML = '<input type="text" class="form-control" name="ano_inicial['+contador+']" id="exampleInputEmail1" placeholder="AÑO INICIAL">';
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

    

    cell1.innerHTML = '<input type="text" class="form-control" name="producto['+cont+']" id="exampleInputEmail1" placeholder="Producto">';
    
    cell2.innerHTML = '<input type="text" class="form-control" name="volumen['+cont+']" id="exampleInputEmail1" placeholder="Volumen">';
    
    cell3.innerHTML = 'SI <input type="radio" name="terminado'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="terminado'+cont+'['+cont+']" id="" value="NO">';
    
    cell4.innerHTML = '<input type="text" class="form-control" name="materia['+cont+']" id="exampleInputEmail1" placeholder="Materia">';
    
    cell5.innerHTML = '<input type="text" class="form-control" name="destino['+cont+']" id="exampleInputEmail1" placeholder="Destino">';
    
    cell6.innerHTML = 'SI <input type="radio" name="marca_propia'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="marca_propia'+cont+'['+cont+']" id="" value="NO">';
    
    cell7.innerHTML = 'SI <input type="radio" name="marca_cliente'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="marca_cliente'+cont+'['+cont+']" id="" value="NO">';
    
    cell8.innerHTML = 'SI <input type="radio" name="sin_cliente'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="sin_cliente'+cont+'['+cont+']" id="" value="NO">';   

    }

  } 

</script>