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
$idoc = $_SESSION['idoc'];
$fecha = time();
$asunto_usuario = "D-SPP Datos de Usuario / User Data";

if ((isset($_POST["registro_opp"])) && ($_POST["registro_opp"] == "1")) {
  $pais = $_POST['pais'];

  //$query = "SELECT idopp, spp, pais FROM opp WHERE pais = '$pais'";
  //$ejecutar_spp = mysql_query($query) or die(mysql_error());
  $row_opp = mysql_query("SELECT idopp, spp, pais FROM opp WHERE pais = '$pais'",$dspp) or die(mysql_error());
  //$datos_opp = mysql_fetch_assoc($ejecutar);
  //$fecha = $_POST['fecha_inclusion'];

  setlocale(LC_ALL, 'en_US.UTF8');

  if(!empty($_POST['spp'])){
    $spp = $_POST['spp'];
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

    $spp = "OPP-".$paisDigitos."-".$fechaDigitos."-".$contador;

    while($datos_opp = mysql_fetch_assoc($row_opp)) {
      if($datos_opp['spp'] == $spp){
        //echo "<b style='color:red'>es igual el OPP con id: $datos_opp[idf]</b><br>";
        $contador++;
        $contador = str_pad($contador, 3, "0", STR_PAD_LEFT);
        $spp = "OPP-".$paisDigitos."-".$fechaDigitos."-".$contador;
      }/*else{
        echo "el id encontrado es: $datos_opp[idf]<br>";
      }*/
      
    }
  }
  //echo "se ha creado un nuevo idf de opp el cual es: <b>$spp</b>";

  $logitud = 8;
  $psswd = substr( md5(microtime()), 1, $logitud);

  /*$spp_oc = $_POST['spp_oc'];
  $query = "SELECT idoc, spp FROM oc WHERE spp = '$spp_oc'";
  $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
  $oc = mysql_fetch_assoc($ejecutar);
*/

  $insertSQL = sprintf("INSERT INTO opp (idoc, spp, nombre, abreviacion, password, sitio_web, email, telefono, pais, ciudad, razon_social, direccion_oficina, direccion_fiscal, rfc, ruc, fecha_registro) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                      // GetSQLValueString($oc['idoc'], "text"),
                       GetSQLValueString($idoc, "int"),
                       GetSQLValueString($spp, "text"),
                       GetSQLValueString($_POST['nombre'], "text"),
                       GetSQLValueString($_POST['abreviacion'], "text"),
                       GetSQLValueString($psswd, "text"),
                       GetSQLValueString($_POST['sitio_web'], "text"),
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($_POST['telefono'], "text"),
                       GetSQLValueString($_POST['pais'], "text"),
                       GetSQLValueString($_POST['ciudad'], "text"),
                       GetSQLValueString($_POST['razon_social'], "text"),
                       GetSQLValueString($_POST['direccion_oficina'], "text"),
                       GetSQLValueString($_POST['direccion_fiscal'], "text"),
                       GetSQLValueString($_POST['rfc'], "text"),
                       GetSQLValueString($_POST['ruc'], "text"),
                       GetSQLValueString($fecha, "int"));
  $Result1 = mysql_query($insertSQL, $dspp) or die(mysql_error());

  $destinatario = $_POST['email'];
        //$asunto = "D-SPP Datos de Usuario / User Data"; 

  $cuerpo = '
      <html>
      <head>
        <meta charset="utf-8">
      </head>
      <body>
      
        <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
          <thead>
            <tr>
              <th rowspan="7" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
              <th scope="col" align="left" width="280"><strong style="color:#27ae60;">Nuevo Registro / New Register</strong></th>
            </tr>
          </thead>
          <tbody>
                <tr>
                  <td colspan="2" style="text-align:justify;padding-top:10px;"><i>Felicidades, se han registrado sus datos correctamente. A continuación se muestra su <b>#SPP y su contraseña, necesarios para poder iniciar sesión</b>: <a href="http://d-spp.org/?OPP" target="_new">www.d-spp.org/?OPP</a></i>, una vez que haya iniciado sesión se le recomienda cambiar su contraseña en la sección Información OPP, en dicha sección se encuentran sus datos los cuales pueden ser modificados en caso de ser necesario.</td>
                </tr>
                <tr>
                  <td colspan="2">
                    <h4 style="color:red">Ahora debe de ingresar a su cuenta dentro del sistema D-SPP para poder completar su Solicitud de Certificación para Organizaciones de Pequeños Productores, esto realizando los siguientes pasos:</h4>
                    <ol>
                      <li>Ingresar en la dirección http://d-spp.org/ .</li>
                      <li>Seleccionar el idioma en el que desea utilizar el sistema.</li>
                      <li>Después de seleccionar el idioma, debe seleccionar la opción "Organización de Pequeños Productores"(OPP) o dar clic en el siguiente link <a href="http://d-spp.org/esp/?OPP">Español</a> o en <a href="http://d-spp.org/en/?OPP">Ingles</a> </li>
                      <li>Debe de iniciar sesión con su usuario(#SPP): <span style="color:#27ae60;">'.$spp.'</span> y su contraseña: <span style="color:#27ae60;">'.$psswd.'</span> </li>
                      <li>Una vez que ha iniciado sesión debe seleccionar la opción "Solicitudes" > "Nueva Solicitud"</li>
                      <li>Después de realizar esos pasos se mostrara la Solicitud electronica donde deberá completar la información correspondiente y al finalizar dar clic en “Enviar Solicitud”.</li>
                      <li>Después de enviar la solicitud, el Organismo de Certificación correspondiente le enviara la cotización por medio del sistema, la cual también le llegara a los correos dados de alta en la solicitud.</li>
                    </ol>
                  </td>
                </tr>
                <!--<tr>
                  <td style="text-align:justify;padding-top:10px;"><i>Congratulations , your data have been recorded correctly. Below is your <b>#SPP and password needed to log in </b>: <a href="http://d-spp.org/?OPP" target="_new">www.d-spp.org/?OPP</a></i>, once you have logged you are advised to change your password on the Information OPP section, in that section are data which can be modified if be necessary.</td>
                </tr>-->

            <tr>
              <td colspan="2" align="left">
                <b style="color:red">
                  Para poder ingresar en el sistema D-SPP debes seleccionar "Organización de Pequeños Productores(OPP)" / To join the system you must select "Small Producers\' Organization (SPO)"
                </b>
                <br>
                <p>
                  <h4>Información del registro</h4>
                </p>
                <p>
                  Pais / Country: <span style="color:#27ae60;">'.$_POST['pais'].'</span>
                </p>
                <p>
                <p>
                  Nombre / Name: <span style="color:#27ae60;">'.$_POST['nombre'].'</span>
                </p>
                <p>
                  Abreviación / Short name: <span style="color:#27ae60;">'.$_POST['abreviacion'].'</span>
                </p>
                <hr>
              </td>
            </tr>

            <tr>
              <td colspan="2">Cualquier duda escribir a / Any questions write to : <u style="color:#27ae60;">cert@spp.coop</u></td>
            </tr>
          </tbody>
        </table>

      </body>
      </html>
    ';

      $mail->AddAddress($destinatario);
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

$row_pais = mysql_query("SELECT * FROM paises");


mysql_select_db($database_dspp, $dspp);
$query_oc = "SELECT idoc, spp, abreviacion, pais FROM oc where idoc = $_SESSION[idoc] ORDER BY nombre ASC";
$oc = mysql_query($query_oc, $dspp) or die(mysql_error());
$row_oc = mysql_fetch_assoc($oc);
$totalRows_oc = mysql_num_rows($oc);
$alerta = '
           <p style="background-color:#e74c3c; border: solid 2px #c0392b; color:#ecf0f1; text-align:center; padding:5px;">
            SI YA ES O FUE UNA ORGANIZACIÓN CERTIFICADA CON EL SPP, DEBE DE CONSULTAR EN SU LISTA DE "Informacion OPP" ANTES DE CREAR UN NUEVO USUARIO.
          </p>';

?>

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


<div class="row">
  <div class="col-md-12">
    <?php echo $alerta; ?>
    <form action="" method="POST" class="form-horizontal">
      <div class="panel panel-info">
        <div class="panel-heading">
          <h3 class="panel-title">Formulario de Registro para OPP</h3>
        </div>
        <div class="panel-body" style="font-size:12px;">
          <p class="alert alert-warning" style="padding:7px;">El #SPP y la contraseña son proporcionados por D-SPP, dichos datos son enviados por email al OPP</p>

          <div class="form-group">
            <label for="spp" class="col-sm-2 control-label">#SPP (En caso de contar con uno)</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="spp" name="spp" placeholder="En caso de contar con uno">
            </div>
          </div>

          <div class="form-group">
            <label for="nombre" class="col-sm-2 control-label">* Nombre de la OPP</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Escriba el nombre" autofocus required>
            </div>
          </div>
          <div class="form-group">
            <label for="abreviacion" class="col-sm-2 control-label">Abreviación de la OPP</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="abreviacion" placeholder="Escriba la abreviación" name="abreviacion">
            </div>
          </div>
          <div class="form-group">
            <label for="sitio_web" class="col-sm-2 control-label">Sitio Web</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="sitio_web" name="sitio_web">
            </div>
          </div>
          <div class="form-group">
            <label for="email" class="col-sm-2 control-label">* Correo Electronico</label>
            <div class="col-sm-10">
              <input type="email" class="form-control" id="email" name="email" placeholder="Escriba el correo electronico" required>
            </div>
          </div>
          <div class="form-group">
            <label for="telefono" class="col-sm-2 control-label">* Teléfono de Oficina</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="telefono" name="telefono" placeholder="Escriba el teléfono de oficina" required>
            </div>
          </div>
          <div class="form-group">
            <label for="pais" class="col-sm-2 control-label">País</label>
            <div class="col-sm-10">
              <select name="pais" id="pais" class="form-control" required>
                <option value="">Selecciona un País</option>
                <?php 
                while($pais = mysql_fetch_assoc($row_pais)){
                  echo "<option value='".utf8_encode($pais['nombre'])."'>".utf8_encode($pais['nombre'])."</option>";
                }
                 ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label for="ciudad" class="col-sm-2 control-label">Ciudad</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="ciudad" name="ciudad">
            </div>
          </div>
          <div class="form-group">
            <label for="direccion_oficina" class="col-sm-2 control-label">Dirección de las Oficinas</label>
            <div class="col-sm-10">
              <input type="text" id="direccion_oficina" class="form-control" name="direccion_oficina" placeholder="Dirección de las Oficinas">
            </div>
          </div>
          <p class="alert alert-warning text-center" style="padding:7px;">Datos Fiscales (Opcionales)</p>
          <div class="form-group">
            <label for="razon_social" class="col-sm-2 control-label">Razón Social</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="razon_social" name="razon_social">
            </div>
          </div>
          <div class="form-group">
            <label for="direccion_fiscal" class="col-sm-2 control-label">Dirección Fiscal</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="direccion_fiscal" name="direccion_fiscal">
            </div>
          </div>
          <div class="form-group">
            <label for="rfc" class="col-sm-2 control-label">RFC</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="rfc" name="rfc">
            </div>
          </div>
          <div class="form-group">
            <label for="ruc" class="col-sm-2 control-label">RUC</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="ruc" name="ruc">
            </div>
          </div>
          <input type="hidden" name="registro_opp" value="1">
          <button type="submit" class="btn btn-success form-control"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Registrarse</button>

        </div>
      </div>
    </form>
  </div>
</div>