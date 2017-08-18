<?php 
require_once('../Connections/dspp.php');
require_once('../Connections/mail.php');
mysql_select_db($database_dspp, $dspp);
        //$asunto = "Nuevo Registro - D-SPP( Datos de Acceso )";


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

// *** Validate request to login to this site.
if (!isset($_SESSION)) {
  session_start();
}

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
  $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}



/***  VARIABLE GENERALES  ****/
$administrador = 'cert@spp.coop';
$fecha = time();
$asunto_usuario = "D-SPP Données utilisateur / User Data";

/**********************************************/

/****************************** INICIA FORMULARIO INSERTAR OPP **************************************************/
if ((isset($_POST["registro_opp"])) && ($_POST["registro_opp"] == "1")) {
  mysql_select_db($database_dspp, $dspp);

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
    $contador_num = 1;
    $contador = str_pad($contador_num, 3, "0", STR_PAD_LEFT);
    //$numero =  strlen($contador);

    $spp = "OPP-".$paisDigitos."-".$fechaDigitos."-".$contador;

    while($datos_opp = mysql_fetch_assoc($row_opp)) {
      if($datos_opp['spp'] == $spp){
        //echo "<b style='color:red'>es igual el OPP con id: $datos_opp[idf]</b><br>";
        $contador_num++;
        $contador = str_pad($contador_num, 3, "0", STR_PAD_LEFT);
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
  if(empty($_POST['idoc'])){
    $idoc = NULL;
  }else{
    $idoc = $_POST['idoc'];
  }

  $insertSQL = sprintf("INSERT INTO opp (spp, nombre, abreviacion, password, sitio_web, email, telefono, idoc, pais, ciudad, razon_social, direccion_oficina, direccion_fiscal, rfc, ruc, fecha_registro) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                      // GetSQLValueString($oc['idoc'], "text"),
                       GetSQLValueString($spp, "text"),
                       GetSQLValueString($_POST['nombre'], "text"),
                       GetSQLValueString($_POST['abreviacion'], "text"),
                       GetSQLValueString($psswd, "text"),
                       GetSQLValueString($_POST['sitio_web'], "text"),
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($_POST['telefono'], "text"),
                       GetSQLValueString($_POST['idoc'], "int"),
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
      $mail->AddBCC($administrador);
      //$mail->Username = "soporte@d-spp.org";
      //$mail->Password = "/aung5l6tZ";
      $mail->Subject = utf8_decode($asunto_usuario);
      $mail->Body = utf8_decode($cuerpo);
      $mail->MsgHTML(utf8_decode($cuerpo));
      $mail->Send();
      $mail->ClearAddresses();
      $mensaje = "<strong>Datos Registrados Correctamente, por favor revisa tu bandeja de correo electronico, si no encuentras tus datos revisa tu bandeja de spam</strong>";

}
/****************************** FIN FORMULARIO INSERTAR OPP **************************************************/

/****************************** INICIA FORMULARIO INSERTAR EMPRESA **************************************************/
if ((isset($_POST["registro_empresa"])) && ($_POST["registro_empresa"] == "1")) {
  mysql_select_db($database_dspp, $dspp);

  $tipo_empresa = '';
  if(isset($_POST['maquilador'])){
    $maquilador = $_POST['maquilador'];
    $tipo_empresa = 'MAQ-';
  }else{
    $maquilador = '';
  }
  if(isset($_POST['comprador'])){
    $comprador = $_POST['comprador'];
    $tipo_empresa = 'COM-';
  }else{
    $comprador = '';
  }
  if(isset($_POST['intermediario'])){
    $intermediario = $_POST['intermediario'];
    $tipo_empresa = 'INT-';
  }else{
    $intermediario = '';
  }

  $pais = $_POST['pais'];

  $query = "SELECT idempresa, spp, pais FROM empresa WHERE pais = '$pais'";
  $ejecutar_spp = mysql_query($query) or die(mysql_error());
  //$datos_empresa = mysql_fetch_assoc($ejecutar);
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

    $spp = $tipo_empresa.$paisDigitos."-".$fechaDigitos."-".$contador;

    while ($datos_empresa = mysql_fetch_assoc($ejecutar_spp)) {
      if($datos_empresa['spp'] == $spp){
        //echo "<b style='color:red'>es igual el OPP con id: $datos_empresa[idf]</b><br>";
        $contador++;
        $contador = str_pad($contador, 3, "0", STR_PAD_LEFT);
        $spp = $tipo_empresa.$paisDigitos."-".$fechaDigitos."-".$contador;
      }/*else{
        echo "el id encontrado es: $datos_empresa[idf]<br>";
      }*/
      
    }
  }
  //echo "se ha creado un nuevo idf de opp el cual es: <b>$idfcom</b>";

  $logitud = 8;
  $psswd = substr( md5(microtime()), 1, $logitud);



  $insertSQL = sprintf("INSERT INTO empresa (idoc, spp, maquilador, comprador, intermediario, nombre, abreviacion, password, sitio_web, email, telefono, pais, ciudad, razon_social, direccion_oficina, direccion_fiscal, rfc, ruc, fecha_registro) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
     GetSQLValueString($_POST['idoc'], "int"),
     GetSQLValueString($spp, "text"),
     GetSQLValueString($maquilador, "int"),
     GetSQLValueString($comprador, "int"),
     GetSQLValueString($intermediario, "int"),
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
        <tbody>
              <tr>
                <th rowspan="7" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                <th scope="col" align="left" width="280"><strong style="color:#27ae60;">Nuevo Registro / New Register</strong></th>
              </tr>
              <tr>
                <td style="text-align:justify;padding-top:10px;"><i>Felicidades, se han registrado sus datos correctamente. A continuación se muestra su <b>#SPP y su contraseña, necesarios para poder iniciar sesión</b>: <a href="http://d-spp.org/?COM" target="_new">www.d-spp.org/?COM</a></i>, una vez que haya iniciado sesión se le recomienda cambiar su contraseña en la sección Información COM, en dicha sección se encuentran sus datos los cuales pueden ser modificados en caso de ser necesario.</td>
              </tr>
              <tr>
                <td style="text-align:justify;padding-top:10px;"><i>Congratulations , your data have been recorded correctly. Below is your <b>#SPP and password needed to log in </b>: <a href="http://d-spp.org/?COM" target="_new">www.d-spp.org/?COM</a></i>, once you have logged you are advised to change your password on the Information COM section, in that section are data which can be modified if be necessary.</td>
              </tr>
          <tr>
            <td align="left">
              <br>
              <b>Pais / Country: </b><span style="color:#27ae60;">'.$_POST['pais'].'</span>
              <br>
              <b>Nombre de la Empresa / Company Name:</b> <span style="color:#27ae60;">'.$_POST['nombre'].'</span>
            </td>
          </tr>
          <tr>
            <td align="left"><br><b>#SPP:</b> <span style="color:#27ae60;">'.$spp.'</span></td>
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
    $mail->AddBCC($administrador);

    //$mail->Username = "soporte@d-spp.org";
    //$mail->Password = "/aung5l6tZ";
    $mail->Subject = utf8_decode($asunto_usuario);
    $mail->Body = utf8_decode($cuerpo);
    $mail->MsgHTML(utf8_decode($cuerpo));
    $mail->Send();
    $mail->ClearAddresses();
    
    $mensaje = "<strong>Datos Registrados Correctamente, por favor revisa tu bandeja de correo electronico, si no encuentras tus datos revisa tu bandeja de spam</strong>";
}

/****************************** FIN FORMULARIO INSERTAR EMPRESA **************************************************/


/************************************* INICIO DE SESION ADMINISTRADOR **************************************************************/
if (isset($_POST['username'])) {
  $loginUsername=$_POST['username'];
  $password=$_POST['password'];
  $MM_fldUserAuthorization = "clase";
  $MM_redirectLoginSuccess = "adm/main_menu.php";
  $MM_redirectLoginFailed = "?";
  $MM_redirecttoReferrer = false;
  mysql_select_db($database_dspp, $dspp);
    
  $LoginRS__query=sprintf("SELECT username, password, clase FROM adm WHERE username=%s AND password=%s",
  GetSQLValueString($loginUsername, "text"), GetSQLValueString($password, "text")); 
   
  $LoginRS = mysql_query($LoginRS__query, $dspp) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);
  if ($loginFoundUser) {
    
    $loginStrGroup  = mysql_result($LoginRS,0,'clase');
    
  if (PHP_VERSION >= 5.1) {session_regenerate_id(true);} else {session_regenerate_id();}
    //declare two session variables and assign them
    $row_adm = mysql_query("SELECT idadm FROM adm WHERE username = '$_POST[username]' AND password = '$_POST[password]'", $dspp) or die(mysql_error());
    $adm = mysql_fetch_assoc($row_adm);
    $_SESSION['idioma'] = "ESP";
    $_SESSION['MM_Username'] = $loginUsername;
    $_SESSION['MM_UserGroup'] = $loginStrGroup;
    $_SESSION['idadministrador'] = $adm['idadm'];       

    if (isset($_SESSION['PrevUrl']) && false) {
      $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];  
    }
    header("Location: " . $MM_redirectLoginSuccess );
  }
  else {
    header("Location: ". $MM_redirectLoginFailed );
  }
}
/************************************* INICIO DE SESION ADMINISTRADOR **************************************************************/



/************************************* INICIO DE SESION OPP **************************************************************/
if (isset($_POST['SPP_OPP'])) {

    $loginUsername=$_POST['SPP_OPP'];
    $password=$_POST['password'];
    $MM_fldUserAuthorization = "clase";
    $MM_redirectLoginSuccess = "opp/main_menu.php";
    $MM_redirectLoginFailed = "?OPP";
    $MM_redirecttoReferrer = false;
    mysql_select_db($database_dspp, $dspp);
      
    $LoginRS__query=sprintf("SELECT idopp, spp, password, nombre AS 'nombreOPP' FROM opp WHERE spp=%s AND password=%s",
    GetSQLValueString($loginUsername, "text"), GetSQLValueString($password, "text")); 
     
    $LoginRS = mysql_query($LoginRS__query, $dspp) or die(mysql_error());
    $loginFoundUser = mysql_num_rows($LoginRS);

    if ($loginFoundUser) {
      
      $loginStrGroup  = mysql_fetch_assoc($LoginRS);
      
    if (PHP_VERSION >= 5.1) {session_regenerate_id(true);} else {session_regenerate_id();}
      //declare two session variables and assign them
      $_SESSION['idioma'] = "ESP";
      $_SESSION['MM_Username'] = $loginUsername;
      $_SESSION['MM_UserGroup'] = $loginStrGroup['nombre'];       
      $_SESSION["autentificado"] = true;
      //$_SESSION["nombre"] = $loginStrGroup['nombre'];
      $_SESSION["nombreOPP"] = $loginStrGroup['nombreOPP'];
      $_SESSION["idopp"] = $loginStrGroup['idopp'];
      $_SESSION["spp_opp"] = $loginStrGroup['spp'];    
      if (isset($_SESSION['PrevUrl']) && false) {
        $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];  
      }
      header("Location: " . $MM_redirectLoginSuccess );
    }
    else {
      header("Location: ". $MM_redirectLoginFailed );
    }
}
/************************************* FIN INICIO DE SESION OPP **************************************************************/

/************************************* INICIO DE SESION COM **************************************************************/

if (isset($_POST['SPP_EMPRESA'])) {

    $loginUsername=$_POST['SPP_EMPRESA'];
    $password=$_POST['password'];
    $MM_fldUserAuthorization = "clase";
    $MM_redirectLoginSuccess = "empresa/main_menu.php";
    $MM_redirectLoginFailed = "?EMPRESA";
    $MM_redirecttoReferrer = false;
    mysql_select_db($database_dspp, $dspp);
      
    $LoginRS__query=sprintf("SELECT idempresa, spp, password, nombre AS 'nombreEmpresa' FROM empresa WHERE spp=%s AND password=%s",
    GetSQLValueString($loginUsername, "text"), GetSQLValueString($password, "text")); 
     
    $LoginRS = mysql_query($LoginRS__query, $dspp) or die(mysql_error());
    $loginFoundUser = mysql_num_rows($LoginRS);

    if ($loginFoundUser) {
      
      $loginStrGroup  = mysql_fetch_assoc($LoginRS);
      
    if (PHP_VERSION >= 5.1) {session_regenerate_id(true);} else {session_regenerate_id();}
      //declare two session variables and assign them
      $_SESSION['idioma'] = "ESP";
      $_SESSION['MM_Username'] = $loginUsername;       
      $_SESSION["autentificado"] = true;
      $_SESSION["nombreEmpresa"] = $loginStrGroup['nombreEmpresa'];
      $_SESSION["idempresa"] = $loginStrGroup['idempresa'];
      $_SESSION["spp_empresa"] = $loginStrGroup['spp'];    
      if (isset($_SESSION['PrevUrl']) && false) {
        $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];  
      }
      header("Location: " . $MM_redirectLoginSuccess );
    }
    else {
      header("Location: ". $MM_redirectLoginFailed );
    }
}
/************************************* FIN INICIO DE SESION COM **************************************************************/

/************************************* INICIO DE SESION OC **************************************************************/

if (isset($_POST['SPP_OC'])) {
  $loginUsername=$_POST['SPP_OC'];
  $password=$_POST['password'];
  $MM_fldUserAuthorization = "clase";
  $MM_redirectLoginSuccess = "oc/main_menu.php";
  $MM_redirectLoginFailed = "?OC";
  $MM_redirecttoReferrer = false;
  mysql_select_db($database_dspp, $dspp);
    
  $LoginRS__query=sprintf("SELECT idoc, spp, password, nombre AS 'nombreOC' FROM oc WHERE spp=%s AND password=%s",
  GetSQLValueString($loginUsername, "text"), GetSQLValueString($password, "text")); 
   
  $LoginRS = mysql_query($LoginRS__query, $dspp) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);

  if ($loginFoundUser) {
    
    $loginStrGroup  = mysql_fetch_assoc($LoginRS);
    
  if (PHP_VERSION >= 5.1) {session_regenerate_id(true);} else {session_regenerate_id();}
    //declare two session variables and assign them
    $_SESSION['idioma'] = "ESP";
    $_SESSION['MM_Username'] = $loginUsername;      
    $_SESSION["autentificado"] = true;
    $_SESSION["nombreOC"] = $loginStrGroup['nombreOC'];
    $_SESSION["idoc"] = $loginStrGroup['idoc'];
    $_SESSION["spp_oc"] = $loginStrGroup['spp'];    
    if (isset($_SESSION['PrevUrl']) && false) {
      $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];  
    }
    header("Location: " . $MM_redirectLoginSuccess );
  }
  else {
    header("Location: ". $MM_redirectLoginFailed );
  }
}
/************************************* INICIO DE SESION OC **************************************************************/

$row_pais = mysql_query("SELECT * FROM paises", $dspp) or die(mysql_error());
$row_oc = mysql_query("SELECT idoc, abreviacion FROM oc", $dspp) or die(mysql_error());
$alerta = '
           <p style="background-color:#e74c3c; border: solid 2px #c0392b; color:#ecf0f1; text-align:center">
            SI VOUS ÊTES OU VOUS ÊTES UNE ORGANISATION OU UNE SOCIÉTÉ CERTIFIÉE AVEC LE SPP, VOUS DEVEZ DEMANDER VOTRE UTILISATEUR ET MOT DE PASSE AU MAIL SUIVANT: <a href="mailto:soporte@d-spp.org" style="color:#ecf0f1;background-color:#2980b9">soporte@d-spp.org</a> ET OMMIT LE FORMULAIRE SUIVANT.
          </p>';
$alerta2 = '
           <p style="background-color:#e74c3c; border: solid 2px #c0392b; color:#ecf0f1; text-align:center">
            SI VOUS ÊTES UNE ORGANISATION OU UNE SOCIÉTÉ CERTIFIÉE AVEC LE SPP, VOUS DEVEZ DEMANDER VOTRE UTILISATEUR ET MOT DE PASSE AU MAINTENANT SUIVANT: <a href="mailto:soporte@d-spp.org" style="color:#ecf0f1;background-color:#2980b9">soporte@d-spp.org</a>.
          </p>';

?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../img/FUNDEPPO.png">
    <title>SPP GLOBAL | D-SPP</title>

    <!-- Bootstrap core CSS -->
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../bootstrap/css/bootstrap-theme.css" rel="stylesheet">
    <link href="../bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="../bootstrap/css/bootstrap-theme.min.css" rel="stylesheet">

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>


  </head>

  <body>
  <div class="container-fluid">
    <div class="row">
      <!-- INICIA BARRA DE NAVEGACIÓN  -->
      <div class="col-md-12">
        <ul class="nav nav-pills">
          <li role="presentation" style="margin:0px;padding:0px;"><a href="index.php"><img src="../img/FUNDEPPO.png" alt=""></a></li>
          <li role="presentation" <? if(isset($_GET['OPP'])){?> class="active" <? }?>><a href="?OPP" data-toggle="tooltip" data-placement="bottom" title="Cliquez pour commencer la session">Organisation de petits producteurs</a></li>
          <li role="presentation" <? if(isset($_GET['OC'])){?> class="active" <? }?>><a href="?OC" data-toggle="tooltip" data-placement="bottom" title="Cliquez pour commencer la session">Organisme de certification</a></li>
          <li role="presentation" <? if(isset($_GET['COM'])){?> class="active" <? }?>><a href="?COM" data-toggle="tooltip" data-placement="bottom" title="Cliquez pour commencer la session">Entreprises</a></li>
          <li role="presentation" <? if(isset($_GET['ADM'])){?> class="active" <? }?>><a href="?ADM">ADM</a></li>
          <!--<li role="presentation" <? if(isset($_GET['RECURSOS'])){?> class="active" <? }?>><a href="#">RECURSOS</a></li>-->
        </ul>
        <hr>
      </div>
      <!-- TERMINA BARRA DE NAVEGACIÓN  -->
      <!-- (SEGUNDA)INICIA SECCIÓN VENTAN DE LOGEO -->
      <div class="col-md-4 col-md-push-8">
        <?php 
        if(isset($_GET['OPP'])){
        ?>

            <div>
              <h4 class="alert alert-success">Connectez-vous en tant organisation de petits producteurs</h4>
            </div>
            <div class="panel-body panel-success">
              <form ACTION="<?php echo $loginFormAction; ?>" METHOD="POST" class="form-signin" id="opp">
               
                <input type="text" id="SPP_OPP" name="SPP_OPP" class="form-control" placeholder="#SPP" required autofocus>
                <br>
                <input type="password" id="inputPassword" class="form-control" name="password" placeholder="Mot de passe" required>
                <br>
                
                <br>
                <button class="btn btn-primary btn-block" type="submit">Connectez-vous</button>
                <a class="btn btn-danger btn-block" type="submit" name="registrarse" <?php if(isset($_GET['SPP_OC'])){echo "href='?registro&SPP_OC=$_GET[SPP_OC]'";}else{ echo "href='?registroOPP'";} ?>>Créer un nouvel utilisateur</a>
              </form>
            </div>
         
        <?php
        }else if(isset($_GET['OC'])){
        ?>

            <div>
              <h4 class="alert alert-info">Connectez-vous en tant qu'organisme de certification</h4>
            </div>
            <div class="panel-body">
              <form ACTION="<?php echo $loginFormAction; ?>" METHOD="POST" class="form-signin" id="oc">
               
                <input type="text" id="SPP_OC" name="SPP_OC" class="form-control" placeholder="#SPP" required autofocus>
                <br>
                <input type="password" id="inputPassword" class="form-control" name="password" placeholder="Mot de passe" required>
                <br>
                
                <br>
                <button class="btn btn-primary form-control"  type="submit">Connectez-vous</button>

              </form>
            </div>


        <?php
        }else if(isset($_GET['COM'])){
        ?>

            <div >
              <h4 class="alert alert-success">Connectez-vous en tant qu'entreprise</h4>
            </div>
            <div class="panel-body">
              <form ACTION="<?php echo $loginFormAction; ?>" METHOD="POST" class="form-signin" id="empresa">
                <input type="text" id="SPP_EMPRESA" name="SPP_EMPRESA" class="form-control" placeholder="#SPP" required autofocus>
                <br>
                <input type="password" id="inputPassword" class="form-control" name="password" placeholder="Mot de passe" required>
                <br>
                
                <br>
                <button class="btn btn-primary btn-block" type="submit">Connectez-vous</button>
                <a class="btn btn-danger btn-block"  type="submit" name="registrarse" <?php if(isset($_GET['SPP_OC'])){echo "href='?registro&SPP_OC=$_GET[SPP_OC]'";}else{ echo "href='?registroCOM'";} ?>>Créer un nouvel utilisateur</a>
              </form>
            </div>
   
        <?php
        }else if(isset($_GET['adm']) || isset($_GET['ADM'])){
        ?>

            <div class="alert alert-info">
              <h3>SPP GLOBAL</h3>
              <p>Administrateurs d'accès</p>
            </div>
            <div class="panel-body">
            <form ACTION="<?php echo $loginFormAction; ?>" METHOD="POST" class="form-signin" id="adm">
              <h2 class="form-signin-heading">Informations de connexion</h2>
              <label for="inputEmail" class="sr-only">Email address</label>
              <input type="text" id="" name="username" class="form-control" placeholder="Username" required autofocus>
              <label for="inputPassword" class="sr-only">Mot de passe</label>
              <input type="password" id="inputPassword" class="form-control" name="password" placeholder="Mot de passe" required>
              <button class="btn btn-lg btn-primary btn-block" type="submit">Connectez-vous</button>
            </form>
            </div>
        <?php
        }else{
        ?>
          <div class="panel panel-primary">
            <div class="panel-heading">
              <h3 class="panel-title">Login</h3>
            </div>
            <div class="panel-body">
              <p>Sélectionnez un type d'utilisateur</p>
              <ul class="nav">
              <li role="presentation" <? if(isset($_GET['OPP'])){?> class="active" <? }?>><a href="?OPP">(OPP) Organisation de petits producteurs</a></li>
              <li role="presentation" <? if(isset($_GET['OC'])){?> class="active" <? }?>><a href="?OC">(OC) Organisme de certification</a></li>
              <li role="presentation" <? if(isset($_GET['COM'])){?> class="active" <? }?>><a href="?COM">Entreprises</a></li>
              </ul>
            </div>
            <?php echo $alerta2; ?>
          </div>
        <?php
        }
         ?>
      </div>
      <!-- (SEGUNDA)TERMINA SECCIÓN VENTAN DE LOGEO -->

      <!-- (PRIMERA)INICIA SECCIÓN PRINCIPAL -->
      <div class="col-md-8 col-md-pull-4">  
        <div class="row">
          <div class="col-lg-12">
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

        <?php 
        if(isset($_GET['registroOPP'])){ /////////////// INICIA FORMULARIO DE REGISTRO OPP
        ?>
          <div class="row">
            <div class="col-md-12">
              <?php echo $alerta; ?>
              <form action="" method="POST" class="form-horizontal">
                <div class="panel panel-info">
                  <div class="panel-heading">
                    <h3 class="panel-title">Formulaire d'inscription pour l'organisation des petits producteurs</h3>
                  </div>
                  <div class="panel-body" style="font-size:12px;">
                    <p class="alert alert-warning" style="padding:7px;">Le #SPP et le mot de passe sont fournis par D-SPP, ces données sont envoyées par courrier électronique à la Police provinciale de l'Ontario</p>

                    <!--<div class="form-group">
                      <label for="spp" class="col-sm-2 control-label">#SPP (si vous en avez un)</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="spp" name="spp" placeholder="En caso de contar con uno">
                      </div>
                    </div>-->

                    <div class="form-group">
                      <label for="nombre" class="col-sm-2 control-label">* Nom de l'organisation:</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="" autofocus required>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="abreviacion" class="col-sm-2 control-label">Sigle ou nom gégé</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="abreviacion" placeholder="" name="abreviacion">
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="sitio_web" class="col-sm-2 control-label">Site Web</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="sitio_web" name="sitio_web">
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="email" class="col-sm-2 control-label">* Courrier électronique</label>
                      <div class="col-sm-10">
                        <input type="email" class="form-control" id="email" name="email" placeholder="" required>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="telefono" class="col-sm-2 control-label">* Téléphone de l'organisation</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="telefono" name="telefono" placeholder="" required>
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="sitio_web" class="col-sm-2 control-label">Organisme de certification (Si vous utilisez les services d'un certificateur)</label>
                      <div class="col-sm-10">
                        <select class="form-control" name="idoc" id="idoc">
                          <option value="">Sélectionnez un</option>
                          <?php 
                          while($oc = mysql_fetch_assoc($row_oc)){
                            echo "<option value='".$oc['idoc']."'>".$oc['abreviacion']."</option>";
                          }
                           ?>
                        </select>
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="pais" class="col-sm-2 control-label">Pays</label>
                      <div class="col-sm-10">
                        <select name="pais" id="pais" class="form-control" required>
                          <option value="">Sélectionnez un pays</option>
                          <?php 
                          while($pais = mysql_fetch_assoc($row_pais)){
                            echo "<option value='".utf8_encode($pais['nombre'])."'>".utf8_encode($pais['nombre'])."</option>";
                          }
                           ?>
                        </select>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="ciudad" class="col-sm-2 control-label">Ville</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="ciudad" name="ciudad">
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="direccion_oficina" class="col-sm-2 control-label">Adresse du bureau</label>
                      <div class="col-sm-10">
                        <input type="text" id="direccion_oficina" class="form-control" name="direccion_oficina" placeholder="">
                      </div>
                    </div>
                    <p class="alert alert-warning text-center" style="padding:7px;">Données fiscales (facultatif)</p>
                    <div class="form-group">
                      <label for="razon_social" class="col-sm-2 control-label">Registre du commerce</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="razon_social" name="razon_social">
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="direccion_fiscal" class="col-sm-2 control-label">Adresse fiscale</label>
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
                    <button type="submit" class="btn btn-success form-control"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Créer un utilisateur</button>

                  </div>
                </div>
              </form>
            </div>
          </div>

        <?php
        ///////////////////// TERMINA FORMULARIO DE REGISTRO OPP
        }else if(isset($_GET['registroCOM'])){ /////////////INICIA FORMULARIO DE REGISTRO EMPRESA
        ?>
          <div class="row">
            <div class="col-md-12">
              <?php echo $alerta; ?>
              <form action="" method="POST" class="form-horizontal">
                <div class="panel panel-info">
                  <div class="panel-heading">
                    <h3 class="panel-title">Formulaire d'inscription aux entreprises</h3>
                  </div>
                  <div class="panel-body" style="font-size:12px;">
                    <p class="alert alert-warning" style="padding:7px;">Le #SPP et le mot de passe sont fournis par D-SPP, ces données sont envoyées par courrier électronique</p>

                    <!--<div class="form-group">
                      <label for="spp" class="col-sm-2 control-label">#SPP (si vous en avez un)</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="spp" name="spp" placeholder="En caso de contar con uno">
                      </div>
                    </div>-->

                    <div class="form-group">
                      <label for="nombre" class="col-sm-2 control-label">* Nom de l'entreprise</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="" autofocus required>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="abreviacion" class="col-sm-2 control-label">Sigle ou nom abrégé</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="abreviacion" placeholder="" name="abreviacion">
                      </div>
                    </div>

                     <div class="form-group">
                        <p class="col-sm-2 text-right"><strong>TYPE DE ENTREPRISE</strong></p>
                        <div class="col-sm-10">
                          <div class="checkbox">
                            <label class="col-sm-4">
                              <input type="checkbox" id="maquilador" name="maquilador" value="1"> SOUS-TRAITANT
                              <p style="color:#7f8c8d;">Fournisseurs de services impliqués dans le marketing ou l'emballage du produit, sans qu'il y ait un processus d'achat de celui-ci.</p>
                              
                            </label>
                            <label class="col-sm-4">
                              <input type="checkbox" id="comprador" name="comprador" value="1"> ACHETEUR FINAL
                              <p style="color:#7f8c8d;">Une entreprise qui achète des produits certifiés sous le symbole du petit producteur pour les mettre sur le marché de consommation finale sous son nom ou sa marque et répond aux critères respectifs de la norme générale du symbole des petits producteurs.</p>
                              
                            </label>
                            <label class="col-sm-4">
                              <input type="checkbox" id="intermediario" name="intermediario" value="1"> INTERMEDIAIRE
                              <p style="color:#7f8c8d;">Les entreprises de marketing qui achètent et vendent les produits du Symbole des petits producteurs sans mettre le produit sous leur nom ou leur marque sur le marché de consommation finale.</p>
                              
                            </label>
                          </div>
                        </div>
                      </div>


                    <div class="form-group">
                      <label for="sitio_web" class="col-sm-2 control-label">Organisme de certification (Si vous utilisez les services d'un organisme de certification)</label>
                      <div class="col-sm-10">
                        <select class="form-control" name="idoc" id="idoc">
                          <option value="">Sélectionnez un</option>
                          <?php 
                          while($oc = mysql_fetch_assoc($row_oc)){
                            echo "<option value='".$oc['idoc']."'>".$oc['abreviacion']."</option>";
                          }
                           ?>
                        </select>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="sitio_web" class="col-sm-2 control-label">Site Web</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="sitio_web" name="sitio_web">
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="telefono" class="col-sm-2 control-label">* Téléphone de bureau</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="telefono" name="telefono" placeholder="" required>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="email" class="col-sm-2 control-label">* Courrier électronique</label>
                      <div class="col-sm-10">
                        <input type="email" class="form-control" id="email" name="email" placeholder="" required>
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="pais" class="col-sm-2 control-label">Pays</label>
                      <div class="col-sm-10">
                        <select name="pais" id="pais" class="form-control" required>
                          <option value="">Sélectionnez un pays</option>
                          <?php 
                          while($pais = mysql_fetch_assoc($row_pais)){
                            echo "<option value='".utf8_encode($pais['nombre'])."'>".utf8_encode($pais['nombre'])."</option>";
                          }
                           ?>
                        </select>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="ciudad" class="col-sm-2 control-label">City</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="ciudad" name="ciudad">
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="direccion_oficina" class="col-sm-2 control-label">Adresse du bureau</label>
                      <div class="col-sm-10">
                        <input type="text" id="direccion_oficina" class="form-control" name="direccion_oficina" placeholder="">
                      </div>
                    </div>
                    <p class="alert alert-warning text-center" style="padding:7px;">Informations Fiscales (Facultatif)</p>
                    <div class="form-group">
                      <label for="razon_social" class="col-sm-2 control-label">Registre du commerce</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="razon_social" name="razon_social">
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="direccion_fiscal" class="col-sm-2 control-label">Adresse fiscale</label>
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
                    <input type="hidden" name="registro_empresa" value="1">
                    <button type="submit" class="btn btn-success form-control" onclick="return validar();"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Créer un utilisateur</button>

                  </div>
                </div>
              </form>
            </div>
          </div>
        <?php
        ////////////////// TERMINA FORMULARIO DE REGISTRO EMPRESA
        }else{
        //////////////////////////////  INICIA PORTADA PRINCIPAL
        ?>
          <div class="row">
            <div class="col-md-12">
              <p class="alert alert-warning">
                Pour avoir une meilleure performance au sein de D-SPP.ORG, nous vous recommandons d'utiliser les navigateurs suivants: <a href="https://www.google.com.mx/chrome/browser/desktop/" target="_new">Google Chrome</a>, <a href="https://www.mozilla.org/es-MX/firefox/new/" target="_new">Mozilla Firefox</a>, cliquez sur le nom si vous ne l'avez pas.
              </p>
            </div> 

            <div class="col-md-4">
              <img src="../img/FUNDEPPO.jpg" class="text-center img-responsive" alt="SPP GLOBAL">
            </div>
            <div class="col-md-8">
              <h5 class="text-justify" style="color:#27ae60">
                ¿Quiénes son las Organizaciones de Pequeños Produtores certificadas con el SPP?
              </h5>
              <p class="text-justify">
                Desde el lanzamiento a nivel global en el año 2011, muchas Organizaciones de Pequeños Productores se acercaron para obtener la certificación. Desde esa fecha ya hay muchas Organizaciones que confían en el Símbolo de Pequeños Productores.
              </p>
              <p class="text-justify alert alert-success" style="padding:5.5px;">
                <a href="lista_opp.php">Consultez la liste des petites organisations de producteurs certifiées avec le SPP ici.</a>
              </p>
              <h5 class="text-justify" style="color:#27ae60">¿Quiénes son los Compradores?</h5>
              <p class="text-justify">
                Existen muchas empresas comprometidas con la misión y visión del SPP y apoyan íntegramente al desarrollo de las Organizaciones de Pequeños Productores.
              </p>
              <p class="text-justify alert alert-success" style="padding:5.5px;">
                <a href="lista_empresas.php">Consultez la liste des sociétés enregistrées auprès du SPP ici.</a>
              </p>
            </div>


            <div class="col-md-12">
            <hr>
              <div class="panel panel-success">
                <div class="panel-heading">
                  <h3 class="panel-title">¿QUÉ ES EL SPP?</h3>
                </div>
                <div class="panel-body text-justify">
                  <p>
                    El Símbolo de Pequeños Productores, SPP, es un sello que representa una alianza entre pequeños productores organizados para construir un mercado local y global que valoriza la identidad y las aportaciones económicas, sociales, culturales y ecológicas de las Organizaciones de Pequeños Productores y sus productos. Esta alianza se basa en una relación de colaboración, confianza y corresponsabilidad entre mujeres y hombres pequeños productores, compradores y consumidores. El SPP está respaldado por un sistema de certificación independiente.
                  </p>
                  <p>
                    El SPP está respaldado por un sistema de certificación independiente para garantizar al consumidor que los productos son provenientes de auténticas organizaciones democráticas y autogestionarias de pequeños productores, producidos bajo criterios de sustentabilidad económica, social, cultural y ecológica, y comercializados bajo condiciones justas.
                  </p>
                  <!--El Símbolo de Pequeños Productores es una iniciativa lanzada en el año 2006 por la CLAC (Coordinadora Latinoamericana y del Caribe de Pequeños Productores de Comercio Justo) con el apoyo del movimiento de Comercio Justo y Economía Solidaria de varios continentes. Para garantizar el adecuado uso de este Símbolo, las organizaciones de pequeños productores crearon la FUNDEPPO (Fundación de Pequeños Productores Organizados), la cual permite asegurar que este Símbolo realmente beneficie a los pequeños productores, las comunidades y los consumidores. FUNDEPPO trabaja con organismos y profesionales calificados para certificar de manera independiente y confiable el cumplimiento de las normas del Símbolo.-->
                  <hr>
                  <a class="" href="http://spp.coop/" target="_blank">Aller au site web</a>
                </div>
              </div>
            </div>
          </div>

        <?php
        } ///////////////////// TERMINA PORTADA PRINCIPAL
        ?>       
      </div>
      <!-- (PRIMERA)TERMINA SECCIÓN PRINCIPAL -->

    </div>
  </div>

  <script>
    function validar(){
      /*tipo_solicitud = document.getElementsByName("tipo_solicitud");

       if(){

       }
      // INICIA SELECCION TIPO SOLICITUD
      var seleccionado = false;
      for(var i=0; i<tipo_solicitud.length; i++) {    
        if(tipo_solicitud[i].checked) {
          seleccionado = true;
          break;
        }
      }
       
      if(!seleccionado) {
        alert("Debes de seleccionar un Tipo de Solicitud");
        return false;
      }*/
      var maquilador = document.getElementById('maquilador').checked;
      var intermediario = document.getElementById('intermediario').checked;
      var comprador = document.getElementById('comprador').checked;
      var seleccionado = false;
      if(maquilador){
        seleccionado = true;
      }
      if(intermediario){
        seleccionado = true;
      }
      if(comprador){
        seleccionado = true;
      }

      if(seleccionado == false){
        alert('Debes seleccionar el Tipo de Empresa');
      }
      
    }
  </script>

    <script>
      $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    })
    </script>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="../bootstrap/js/bootstrap.min.js"></script>

  </body>
</html>