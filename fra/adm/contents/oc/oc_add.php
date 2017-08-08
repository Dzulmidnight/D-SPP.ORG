<?php 
require_once('../Connections/dspp.php'); 
require_once('../Connections/mail.php'); 
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
  mysql_select_db($database_dspp, $dspp);

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
$fecha = time();
$asunto_usuario = "D-SPP Datos de Usuario / User Data";
  $logitud = 8;
  $psswd = substr( md5(microtime()), 1, $logitud);

  $insertSQL = sprintf("INSERT INTO oc (spp, password, nombre, abreviacion, email1, email2, sitio_web, pais, telefono, direccion_oficina, razon_social, direccion_fiscal, rfc) VALUES (%s, %s ,%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['spp'], "text"),
                       GetSQLValueString($psswd, "text"),
                       GetSQLValueString($_POST['nombre'], "text"),
                       GetSQLValueString($_POST['abreviacion'], "text"),
                       GetSQLValueString($_POST['email1'], "text"),
                       GetSQLValueString($_POST['email2'], "text"),
                       GetSQLValueString($_POST['sitio_web'], "text"),
                       GetSQLValueString($_POST['pais'], "text"),
                       GetSQLValueString($_POST['telefono'], "text"),
                       GetSQLValueString($_POST['direccion_oficina'], "text"),
                       GetSQLValueString($_POST['razon_social'], "text"),
                       GetSQLValueString($_POST['direccion_fiscal'], "text"),
                       GetSQLValueString($_POST['rfc'], "text"));

  $Result1 = mysql_query($insertSQL, $dspp) or die(mysql_error());

  $cuerpo = '
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
                  <td style="text-align:justify;padding-top:10px;"><i>Felicidades, se han registrado sus datos correctamente. A continuación se muestra su <b>#SPP y su contraseña, necesarios para poder inicia sesión</b>: <a href="http://d-spp.org" target="_new">www.d-spp.org</a></i>, una vez que haya iniciado sesión se le recomienda cambiar su contraseña en la sección Información OC, en dicha sección se encuentran sus datos los cuales pueden ser modificados en caso de ser necesario.</td>
                </tr>
                <tr>
                  <td style="text-align:justify;padding-top:10px;"><i>Congratulations , your data have been recorded correctly. Below is your <b>#SPP and password needed to log in </b>: <a href="http://d-spp.org" target="_new">www.d-spp.org</a></i>, once you have logged you are advised to change your password on the Information OC section, in that section are data which can be modified if be necessary.</td>
                </tr>
            <tr>
              <td align="left"><br><b>Nombre / Name:</b> <span style="color:#27ae60;">'.$_POST['nombre'].'</span></td>
            </tr>
            <tr>
              <td align="left"><br><b>#SPP:</b> <span style="color:#27ae60;">'.$_POST['spp'].'</span></td>
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


      if(!empty($_POST['email1'])){
        $mail->AddAddress($_POST['email1']);
      }
      if(!empty($_POST['email2'])){
        $mail->AddAddress($_POST['email2']);
      }

      $mail->AddBCC('yasser.midnight@gmail.com');
      $mail->AddBCC('cert@spp.coop');
      //$mail->Username = "soporte@d-spp.org";
      //$mail->Password = "/aung5l6tZ";
      $mail->Subject = utf8_decode($asunto_usuario);
      $mail->Body = utf8_decode($cuerpo);
      $mail->MsgHTML(utf8_decode($cuerpo));
      $mail->Send();
      $mail->ClearAddresses();
      $mensaje = "<strong>Datos Registrados Correctamente, por favor revisa tu bandeja de correo electronico, si no encuentras tus datos revisa tu bandeja de spam</strong>";

}

mysql_select_db($database_dspp, $dspp);
$query_pais = "SELECT nombre FROM paises ORDER BY nombre ASC";
$pais = mysql_query($query_pais, $dspp) or die(mysql_error());
$row_pais = mysql_fetch_assoc($pais);
$totalRows_pais = mysql_num_rows($pais);
?>
<br>

<div class="row">
  <div class="col-md-12">
  <?php 
  if(isset($mensaje)){
  ?>
    <div class="alert alert-success alert-dismissible" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <?php echo $mensaje; ?>
    </div>
  <?php
  }
  ?>
  </div>
</div>


<form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
  <table  class="table col-xs-8">
    <tr valign="baseline">
      <th nowrap="nowrap" align="right" width="1">#SPP</th>
      <td><input autofocus="autofocus" type="text"  class="form-control" name="spp" value="OC-" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <th nowrap="nowrap" align="right">Nombre(<small style="color:red">requerido</small>)</th>
      <td><input required="required" type="text"  class="form-control" name="nombre" value="" size="32" required /></td>
    </tr>
    <tr valign="baseline">
      <th nowrap="nowrap" align="right">Abreviación</th>
      <td><input type="text"  class="form-control" name="abreviacion" value="" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <th nowrap="nowrap" align="right">Email 1(<small style="color:red">requerido</small>)</th>
      <td><input type="text"  class="form-control" name="email1" value="" size="32" required /></td>
    </tr>
    </tr>
    <tr valign="baseline">
      <th nowrap="nowrap" align="right">Email 2</th>
      <td><input type="text"  class="form-control" name="email2" value="" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <th nowrap="nowrap" align="right">Sitio Web</th>
      <td><input type="text"  class="form-control" name="sitio_web" value="" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <th nowrap="nowrap" align="right">País</th>
      <td>
        <select required class="form-control" name="pais">
        <option value="">Selecciona</option>
        <?php 
        do {  
        ?>
        <option class="form-control" value="<?php echo utf8_encode($row_pais['nombre']);?>" ><?php echo utf8_encode($row_pais['nombre']);?></option>
        <?php
        } while ($row_pais = mysql_fetch_assoc($pais));
        ?>
        </select>
      </td>
    </tr>
    <tr valign="baseline">
      <th nowrap="nowrap" align="right">Dirección Oficinas</th>
      <td><input type="text"  class="form-control" name="direccion_oficina" value="" size="32" /></td>
    </tr>

    <tr valign="baseline">
      <th nowrap="nowrap" align="right">Telefono(<small style="color:red">requerido</small>)</th>
      <td><input type="text"  class="form-control" name="telefono" value="" size="32" required/></td>
    </tr>
    <tr valign="baseline">
      <th nowrap="nowrap" align="right">Razón social</th>
      <td><input type="text"  class="form-control" name="razon_social" value="" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <th nowrap="nowrap" align="right">Dirección fiscal</th>
      <td><input type="text"  class="form-control" name="direccion_fiscal" value="" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <th nowrap="nowrap" align="right">RFC</th>
      <td><input type="text"  class="form-control" name="rfc" value="" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <th nowrap="nowrap" align="right">&nbsp;</th>
      <td><input type="submit" class="btn btn-primary" value="Agregar OC" /></td>
    </tr>
  </table>
  <input type="hidden" name="MM_insert" value="form1" />
</form>

<?php
mysql_free_result($pais);
?>
