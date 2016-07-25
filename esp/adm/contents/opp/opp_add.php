<?php 
require_once('../Connections/dspp.php'); 
require_once('../Connections/mail.php'); 

?>

<?php
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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {

  $pais = $_POST['pais'];
  $query = "SELECT * FROM opp WHERE pais = '$pais'";
  $ejecutar = mysql_query($query) or die(mysql_error());
  $datos_opp = mysql_fetch_assoc($ejecutar);
  $fecha = $_POST['fecha_inclusion'];

  $vigenciaInicio = $_POST['vigenciaInicio'];
  $vigenciaFin = $_POST['vigenciaFin'];
  $numeroSocios = $_POST['socios'];

  setlocale(LC_ALL, 'en_US.UTF8');

  if(!empty($_POST['idf'])){
    $idfopp = $_POST['idf'];
  }else{
    $charset='utf-8'; // o 'UTF-8'
    $str = iconv($charset, 'ASCII//TRANSLIT', $pais);
    $pais = preg_replace("/[^a-zA-Z0-9]/", '', $str);

    $paisDigitos = strtoupper(substr($pais, 0, 3));
    $formatoFecha = date("d/m/Y", $fecha);
    $fechaDigitos = substr($formatoFecha, -2);
    $contador = 1;
    $contador = str_pad($contador, 3, "0", STR_PAD_LEFT);
    //$numero =  strlen($contador);

    $idfopp = "OPP-".$paisDigitos."-".$fechaDigitos."-".$contador;

    while ($datos_opp = mysql_fetch_assoc($ejecutar)) {
      if($datos_opp['idf'] == $idfopp){
        //echo "<b style='color:red'>es igual el OPP con id: $datos_opp[idf]</b><br>";
        $contador++;
        $contador = str_pad($contador, 3, "0", STR_PAD_LEFT);
        $idfopp = "OPP-".$paisDigitos."-".$fechaDigitos."-".$contador;
      }/*else{
        echo "el id encontrado es: $datos_opp[idf]<br>";
      }*/
      
    }
    //echo "se ha creado un nuevo idf de opp el cual es: <b>$idfopp</b>";
  }

  $logitud = 8;
  $psswd = substr( md5(microtime()), 1, $logitud);


  $insertSQL = sprintf("INSERT INTO opp (idf, password, nombre, abreviacion, sitio_web, telefono, email, pais, idoc, razon_social, direccion_fiscal, rfc, fecha_inclusion) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($idfopp, "text"),
                       GetSQLValueString($psswd, "text"),
                       GetSQLValueString($_POST['nombre'], "text"),
                       GetSQLValueString($_POST['abreviacion'], "text"),
                       GetSQLValueString($_POST['sitio_web'], "text"),
                       GetSQLValueString($_POST['telefono'], "text"),
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($_POST['pais'], "text"),
                       GetSQLValueString($_POST['idoc'], "int"),
                       GetSQLValueString($_POST['razon_social'], "text"),
                       GetSQLValueString($_POST['direccion_fiscal'], "text"),
                       GetSQLValueString($_POST['rfc'], "text"),
                       GetSQLValueString($_POST['fecha_inclusion'], "int"));

  mysql_select_db($database_dspp, $dspp);
  $Result1 = mysql_query($insertSQL, $dspp) or die(mysql_error());

  $timeActual = time();

  $timeVencimiento = strtotime($vigenciaFin);
  $timeRestante = ($timeVencimiento - $timeActual);
  $estatusCertificado = "";
  $plazo = 60 *(24*60*60);
  $plazoDespues = ($timeVencimiento + $plazo);
  $prorroga = ($timeVencimiento + $plazo);


  if($timeActual <= $timeVencimiento){
    if($timeRestante <= $plazo){
      $estatusCertificado = 16; // AVISO DE RENOVACIÓN
    }else{
      $estatusCertificado = 10; // CERTIFICADO ACTIVO
    }
  }else{
    if($prorroga >= $timeActual){
      $estatusCertificado = 12; // CERTIFICADO POR EXPIRAR
    }else{
      $estatusCertificado = 11; // CERTIFICADO EXPIRADO
    }
  }
  $idopp = mysql_insert_id($dspp);

    $actualizar = "UPDATE opp SET estado = '$estatusCertificado' WHERE idopp = $idopp";
    $ejecutar = mysql_query($actualizar,$dspp) or die(mysql_error());

    $query = "INSERT INTO certificado(status, vigenciainicio, vigenciafin, idopp) VALUES('$estatusCertificado', '$vigenciaInicio', '$vigenciaFin', $idopp)";
    $ejecutar = mysql_query($query,$dspp) or die(mysql_error());

    if(!empty($numero_socios)){
      $query = "INSERT INTO numero_socios (idopp, socios, fecha_captura) VALUES($idopp, $numeroSocios, $timeActual)";
      $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
    }

        $destinatario = $_POST['email'];
        $asunto = "D-SPP Datos de Usuario / User Data"; 

    $mensaje = '
      <html>
      <head>
        <meta charset="utf-8">
      </head>
      <body>
      
        <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
          <tbody>
                <tr>
                  <th rowspan="7" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                  <th scope="col" align="left" width="280"><strong style="color:#27ae60;">Nuevo Registro / New Register</strong></th>
                </tr>
                <tr>
                  <td style="text-align:justify;padding-top:10px;"><i>Felicidades, se han registrado sus datos correctamente. A continuación se muestra su <b>#SPP y su contraseña, necesarios para poder inicia sesión</b>: <a href="http://d-spp.org/?OPP" target="_new">www.d-spp.org/?OPP</a></i>, una vez que haya iniciado sesión se le recomienda cambiar su contraseña en la sección Información OPP, en dicha sección se encuentran sus datos los cuales pueden ser modificados en caso de ser necesario.</td>
                </tr>
                <tr>
                  <td style="text-align:justify;padding-top:10px;"><i>Congratulations , your data have been recorded correctly. Below is your <b>#SPP and password needed to log in </b>: <a href="http://d-spp.org/?OPP" target="_new">www.d-spp.org/?OPP</a></i>, once you have logged you are advised to change your password on the Information OPP section, in that section are data which can be modified if be necessary.</td>
                </tr>
            <tr>
              <td align="left"><br><b>Nombre / Name:</b> <span style="color:#27ae60;">'.$_POST['nombre'].'</span></td>
            </tr>
            <tr>
              <td align="left"><br><b>#SPP:</b> <span style="color:#27ae60;">'.$idfopp.'</span></td>
            </tr>
            <tr>
              <td align="left"><b>Contraseña / Password:</b> <span style="color:#27ae60;">'.$psswd.'</span></td>
            </tr>
            <tr>
              <td>Cualquier duda escribir a / Any questions write to : <u style="color:#27ae60;">cert@spp.coop</u></td>
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
        $mail->Body = utf8_decode($mensaje);
        $mail->MsgHTML(utf8_decode($mensaje));

        

        if($mail->Send()){
          
          echo "<script>alert('Correo enviado Exitosamente.');location.href ='javascript:history.back()';</script>";
        }else{
              echo "<script>alert('Error, no se pudo enviar el correo');location.href ='javascript:history.back()';</script>";
     
        }


        //para el envío en formato HTML 
       /* $headers = "MIME-Version: 1.0\r\n"; 
        $headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 

        //dirección del remitente 
        $headers .= "From: d-spp.org\r\n"; 

        //dirección de respuesta, si queremos que sea distinta que la del remitente 
        //$headers .= "Reply-To: ".$correo."\r\n"; 

        //ruta del mensaje desde origen a destino 
        //$headers .= "Return-path: holahola@desarrolloweb.org\r\n"; 

        //direcciones que recibián copia 
        //$headers .= "Cc: maria@desarrolloweb.org\r\n"; 

        //direcciones que recibirán copia oculta 
        $headers .= "Bcc: yasser.midnight@gmail.com\r\n"; 
        //$headers .= "Bcc: isc.jesusmartinez@gmail.org \r\n";

        mail($destinatario,$asunto,utf8_decode($cuerpo),$headers) ;*/

  $insertGoTo = "main_menu.php?OPP&add&mensaje=OPP agregado correctamente";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_dspp, $dspp);
$query_pais = "SELECT nombre FROM paises ORDER BY nombre ASC";
$pais = mysql_query($query_pais, $dspp) or die(mysql_error());
$row_pais = mysql_fetch_assoc($pais);
$totalRows_pais = mysql_num_rows($pais);

mysql_select_db($database_dspp, $dspp);
$query_oc = "SELECT idoc, idf, abreviacion, pais FROM oc ORDER BY nombre ASC";
$oc = mysql_query($query_oc, $dspp) or die(mysql_error());
$row_oc = mysql_fetch_assoc($oc);
$totalRows_oc = mysql_num_rows($oc);


?>
<br>
<form class="" method="post" name="form1" action="<?php echo $editFormAction; ?>">
  <table class="table col-xs-8">
    <tr valign="baseline">
      <th colspan="3" class="alert alert-warning">El #OPP y la contraseña son proporcionados por el sistema, dichos datos son enviados por email al OPP</th>
    </tr>
    <!--<tr valign="baseline">
      <th nowrap align="left">Password</th>
      <td><input class="form-control" type="text" name="password" value="" size="32"></td>
    </tr>-->
    <tr valign="baseline">
      <th nowrap align="left">#SPP <br>(En caso de contar con uno)</th>
      <td colspan="2"><input class="form-control" type="text" id="idf" name="idf" value="" size="32"></td>
    </tr>

    <tr valign="baseline">
      <th nowrap align="left">* Nombre</th>
      <td colspan="2"><input required="required" class="form-control" type="text" name="nombre" value="" size="32"></td>
    </tr>
    <tr valign="baseline">
      <th nowrap align="left">Abreviacion</th>
      <td colspan="2"><input class="form-control" type="text" name="abreviacion" value="" size="32" ></td>
    </tr>
    <tr valign="baseline">
      <th nowrap align="left">Sitio_web</th>
      <td colspan="2"><input class="form-control" type="text" name="sitio_web" value="" size="32"></td>
    </tr>
    <tr valign="baseline">
      <th nowrap align="left">* Teléfono Oficinas</th>
      <td colspan="2"><input class="form-control" type="text" name="telefono" value="" size="32" required></td>
    </tr>
    <tr valign="baseline">
      <th nowrap align="left">* Email</th>
      <td colspan="2"><input class="form-control" type="email" name="email" value="" size="32" required></td>
    </tr>
    <tr valign="baseline">
      <th nowrap align="left">* Pais</th>
      <td colspan="2"><select required class="form-control" name="pais">
        <option value="">Selecciona</option>
        <?php 
        do {  
        ?>
        <option class="form-control" value="<?php echo utf8_encode($row_pais['nombre']);?>"><?php echo utf8_encode($row_pais['nombre']);?></option>
        <?php
        } while ($row_pais = mysql_fetch_assoc($pais));
        ?>
      </select></td>
    <tr>
    <tr valign="baseline">
      <th nowrap align="left">#SPP OC</th>
      <td colspan="2"><select class="form-control" name="idoc">
        <option value="">Selecciona</option>
        <?php 
          do {  
          ?>
            <option class="form-control" value="<?php echo $row_oc['idoc']?>" ><?php echo $row_oc['abreviacion']?></option>
          <?php
          } while ($row_oc = mysql_fetch_assoc($oc));
?>
      </select></td>
    <tr>
    <tr valign="baseline">
      <th nowrap align="left">Razon_social</th>
      <td colspan="2"><input class="form-control" type="text" name="razon_social" value="" size="32"></td>
    </tr>
    <tr valign="baseline">
      <th nowrap align="left">Direccion_fiscal</th>
      <td colspan="2"><input class="form-control" type="text" name="direccion_fiscal" value="" size="32"></td>
    </tr>
    <tr valign="baseline">
      <th nowrap align="left">RFC</th>
      <td colspan="2"><input class="form-control" type="text" name="rfc" value="" size="32"></td>
    </tr>
    <tr>
      <td class="alert alert-warning" colspan="2"><b>Información Necesaria en caso de que se cuente con la misma</b></td>
    </tr>
    <tr valign="baseline">
      <td><label for="vigenciaInicio">Fecha Inicial del Certificado</label><input class="form-control" type="date" name="vigenciaInicio" value="" ></td>
      <td><label for="vigenciaFin">Fecha Final del Certificado</label><input class="form-control" type="date" name="vigenciaFin" value="" ></td>
      <td><label for="socios">Numero de Socios</label><input type="text" name="socios" class="form-control" value=""></td>
    </tr>
    <tr valign="baseline" >
      <td colspan="3"><span class="alert alert-info"><b>*</b> Datos requeridos</span> <input class="btn btn-primary" type="submit" value="Agregar OPP"></td>
    </tr>


  </table>
  <input type="hidden" name="fecha_inclusion" value="<?php echo time();?>">
  <input type="hidden" name="MM_insert" value="form1">
</form>

<?
mysql_free_result($pais);

mysql_free_result($oc);
?>