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
$asunto_usuario = "D-SPP Datos de Usuario / User Data";

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
  if(empty($_POST['idoc'])){
    $idoc = NULL;
  }else{
    $idoc = $_POST['idoc'];
  }

  $insertSQL = sprintf("INSERT INTO opp (spp, nombre, abreviacion, password, sitio_web, email, telefono, pais, ciudad, razon_social, direccion_oficina, direccion_fiscal, rfc, ruc, fecha_registro) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                      // GetSQLValueString($oc['idoc'], "text"),
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
              <td align="left">
                <br>
                <b>Pais / Country: </b><span style="color:#27ae60;">'.$_POST['pais'].'</span>
                <br><b>Nombre / Name:</b> <span style="color:#27ae60;">'.$_POST['nombre'].'</span>
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
      $mensaje = "<strong>Registered Data Correctly, please check your email tray, if you can not find your data check your spam tray</strong>";

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
                <td style="text-align:justify;padding-top:10px;"><i>Felicidades, se han registrado sus datos correctamente. A continuación se muestra su <b>#SPP y su contraseña, necesarios para poder inicia sesión</b>: <a href="http://d-spp.org/?COM" target="_new">www.d-spp.org/?COM</a></i>, una vez que haya iniciado sesión se le recomienda cambiar su contraseña en la sección Información COM, en dicha sección se encuentran sus datos los cuales pueden ser modificados en caso de ser necesario.</td>
              </tr>
              <tr>
                <td style="text-align:justify;padding-top:10px;"><i>Congratulations , your data have been recorded correctly. Below is your <b>#SPP and password needed to log in </b>: <a href="http://d-spp.org/?COM" target="_new">www.d-spp.org/?COM</a></i>, once you have logged you are advised to change your password on the Information COM section, in that section are data which can be modified if be necessary.</td>
              </tr>
          <tr>
            <td align="left">
              <br>
              <b>Pais / Country: </b><span style="color:#27ae60;">'.$_POST['pais'].'</span>
              <br><b>Nombre de la Empresa / Company Name:</b> <span style="color:#27ae60;">'.$_POST['nombre'].'</span>
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
    
    $mensaje = "<strong>Data registered correctly, please check your email tray, if you do not find your data check your spam tray</strong>";
}

/****************************** FIN FORMULARIO INSERTAR COM **************************************************/


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
    $_SESSION['idioma'] = "EN";
    $_SESSION['MM_Username'] = $loginUsername;
    $_SESSION['MM_UserGroup'] = $loginStrGroup;	      

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
      $_SESSION['idioma'] = "EN";
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
      $_SESSION['idioma'] = "EN";
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
    $_SESSION['idioma'] = "EN";
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
            IF YOU ARE OR YOU WERE AN ORGANIZATION OR COMPANY CERTIFIED WITH THE SPP, YOU MUST ASK FOR YOUR USER AND PASSWORD AT THE FOLLOWING MAIL: <a href="mailto:soporte@d-spp.org" style="color:#2c3e50">soporte@d-spp.org</a> AND OMMIT THE FOLLOWING FORM.
          </p>';
$alerta2 = '
           <p style="background-color:#e74c3c; border: solid 2px #c0392b; color:#ecf0f1; text-align:center">
            IF YOU ARE AN ORGANIZATION OR COMPANY CERTIFIED WITH THE SPP, YOU MUST ASK FOR YOUR USER AND PASSWORD AT THE FOLLOWING MAIL: <a href="mailto:soporte@d-spp.org" style="color:#2c3e50">soporte@d-spp.org</a>.
          </p>';

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
    <link rel="icon" href="img/FUNDEPPO.png">
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
          <li role="presentation" <? if(isset($_GET['OPP'])){?> class="active" <? }?>><a href="?OPP" data-toggle="tooltip" data-placement="bottom" title="Click to login">Small Producers' Organization</a></li>
          <li role="presentation" <? if(isset($_GET['OC'])){?> class="active" <? }?>><a href="?OC" data-toggle="tooltip" data-placement="bottom" title="Click to login">Certification Entity</a></li>
          <li role="presentation" <? if(isset($_GET['COM'])){?> class="active" <? }?>><a href="?COM" data-toggle="tooltip" data-placement="bottom" title="Click to login">COMPANIES</a></li>
          <li role="presentation" <? if(isset($_GET['ADM'])){?> class="active" <? }?>><a href="?ADM">ADM</a></li>
          <li role="presentation" <? if(isset($_GET['RECURSOS'])){?> class="active" <? }?>><a href="#">RESOURCES</a></li>
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
              <h4 class="alert alert-success">Login as Small Producers' Organization (SPO)</h4>
            </div>
            <div class="panel-body panel-success">
              <form ACTION="<?php echo $loginFormAction; ?>" METHOD="POST" class="form-signin" id="opp">
               
                <input type="text" id="SPP_OPP" name="SPP_OPP" class="form-control" placeholder="#SPP" required autofocus>
                <br>
                <input type="password" id="inputPassword" class="form-control" name="password" placeholder="Password" required>
                <br>
                <a href="#">Forgot my password</a>
                <br>
                <button class="btn btn-primary btn-block" type="submit">Sing In</button>
                <a class="btn btn-danger btn-block" type="submit" name="registrarse" <?php if(isset($_GET['SPP_OC'])){echo "href='?registro&SPP_OC=$_GET[SPP_OC]'";}else{ echo "href='?registroOPP'";} ?>>No account? Create one!</a>
              </form>
            </div>
         
        <?php
        }else if(isset($_GET['OC'])){
        ?>

            <div>
              <h4 class="alert alert-info">Login as a Certification Entity</h4>
            </div>
            <div class="panel-body">
              <form ACTION="<?php echo $loginFormAction; ?>" METHOD="POST" class="form-signin" id="oc">
               
                <input type="text" id="SPP_OC" name="SPP_OC" class="form-control" placeholder="#SPP" required autofocus>
                <br>
                <input type="password" id="inputPassword" class="form-control" name="password" placeholder="Password" required>
                <br>
                <a href="#">Forgot my password</a>
                <br>
                <button class="btn btn-primary form-control"  type="submit">Sing In</button>

              </form>
            </div>


        <?php
        }else if(isset($_GET['COM'])){
        ?>

            <div >
              <h4 class="alert alert-success">Login as COMPANY</h4>
            </div>
            <div class="panel-body">
              <form ACTION="<?php echo $loginFormAction; ?>" METHOD="POST" class="form-signin" id="empresa">
                <input type="text" id="SPP_EMPRESA" name="SPP_EMPRESA" class="form-control" placeholder="#SPP" required autofocus>
                <br>
                <input type="password" id="inputPassword" class="form-control" name="password" placeholder="Password" required>
                <br>
                <a href="#">Forgot my password</a>
                <br>
                <button class="btn btn-primary btn-block" type="submit">Sing In</button>
                <a class="btn btn-danger btn-block"  type="submit" name="registrarse" <?php if(isset($_GET['SPP_OC'])){echo "href='?registro&SPP_OC=$_GET[SPP_OC]'";}else{ echo "href='?registroCOM'";} ?>>No account? Create one!</a>
              </form>
            </div>
   
        <?php
        }else if(isset($_GET['adm']) || isset($_GET['ADM'])){
        ?>

            <div class="alert alert-info">
              <h3>SPP GLOBAL</h3>
              <p>Inicio de sesión administradores</p>
            </div>
            <div class="panel-body">
            <form ACTION="<?php echo $loginFormAction; ?>" METHOD="POST" class="form-signin" id="adm">
              <h2 class="form-signin-heading">Use your administrator account.</h2>
              <label for="inputEmail" class="sr-only">Email address</label>
              <input type="text" id="" name="username" class="form-control" placeholder="Username" required autofocus>
              <label for="inputPassword" class="sr-only">Password</label>
              <input type="password" id="inputPassword" class="form-control" name="password" placeholder="Password" required>
              <button class="btn btn-lg btn-primary btn-block" type="submit">Sing In</button>
            </form>
            </div>
        <?php
        }else{
        ?>
          <div class="panel panel-primary">
            <div class="panel-heading">
              <h3 class="panel-title">Sign in</h3>
            </div>
            <div class="panel-body">
              <p>Select a user type</p>
              <ul class="nav">
              <li role="presentation" <? if(isset($_GET['OPP'])){?> class="active" <? }?>><a href="?OPP">(SPO) Small Producers' Organization</a></li>
              <li role="presentation" <? if(isset($_GET['OC'])){?> class="active" <? }?>><a href="?OC">(CE) Certification Entity</a></li>
              <li role="presentation" <? if(isset($_GET['COM'])){?> class="active" <? }?>><a href="?COM">COMPANIES</a></li>
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
                    <h3 class="panel-title">Registration Form for Small Producers' Organization (SPO)</h3>
                  </div>
                  <div class="panel-body" style="font-size:12px;">
                    <p class="alert alert-warning" style="padding:7px;">The #SPP and password are provided by D-SPP, these data are sent by email to SPO(Small Producers' Organization)</p>

                    <div class="form-group">
                      <label for="spp" class="col-sm-2 control-label">#SPP (If you have one)</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="spp" name="spp" placeholder="If you have one">
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="nombre" class="col-sm-2 control-label">* Name of the Small Producers' Organization</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Enter the name" autofocus required>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="abreviacion" class="col-sm-2 control-label">Short Name</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="abreviacion" placeholder="Enter the short name" name="abreviacion">
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="sitio_web" class="col-sm-2 control-label">Website</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="sitio_web" name="sitio_web">
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="email" class="col-sm-2 control-label">* Email</label>
                      <div class="col-sm-10">
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter the email" required>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="telefono" class="col-sm-2 control-label">* Office Telephone</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="telefono" name="telefono" placeholder="Enter the office phone" required>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="pais" class="col-sm-2 control-label">Country</label>
                      <div class="col-sm-10">
                        <select name="pais" id="pais" class="form-control" required>
                          <option value="">Select a countrys</option>
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
                      <label for="direccion_oficina" class="col-sm-2 control-label">Office address</label>
                      <div class="col-sm-10">
                        <input type="text" id="direccion_oficina" class="form-control" name="direccion_oficina" placeholder="Office address">
                      </div>
                    </div>
                    <p class="alert alert-warning text-center" style="padding:7px;">Fiscal data (Optional)</p>
                    <div class="form-group">
                      <label for="razon_social" class="col-sm-2 control-label">Business name</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="razon_social" name="razon_social">
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="direccion_fiscal" class="col-sm-2 control-label">Fiscal address</label>
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
                    <button type="submit" class="btn btn-success form-control"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Create account</button>

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
                    <h3 class="panel-title">Company Registration Form</h3>
                  </div>
                  <div class="panel-body" style="font-size:12px;">
                    <p class="alert alert-warning" style="padding:7px;">The #SPP and password are provided by D-SPP, these data are sent by email to the company</p>

                    <div class="form-group">
                      <label for="spp" class="col-sm-2 control-label">#SPP (If you have one)</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="spp" name="spp" placeholder="If you have one">
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="nombre" class="col-sm-2 control-label">* Company name</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Enter the name" autofocus required>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="abreviacion" class="col-sm-2 control-label">Short name</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="abreviacion" placeholder="Enter the short name" name="abreviacion">
                      </div>
                    </div>

                     <div class="form-group">
                        <p class="col-sm-2 text-right"><strong>TYPE OF COMPANY</strong></p>
                        <div class="col-sm-10">
                          <div class="checkbox">
                            <label class="col-sm-4">
                              <input type="checkbox" id="maquilador" name="maquilador" value="1"> MAQUILA COMPANY
                              <p style="color:#7f8c8d;">
                                Service providers that intervene in the trading or processing of products, but do not buy or sell these products.
                              </p>
                            </label>
                            <label class="col-sm-4">
                              <input type="checkbox" id="comprador" name="comprador" value="1"> FINAL BUYER
                              <p style="color:#7f8c8d;">
                                A company that buys products certified with the Small Producers’ Symbol to place them on the final consumer market under its own name or trademark, and that complies with the respective criteria in the applicable Standards of the Small Producer’s Symbol.
                              </p>
                            </label>
                            <label class="col-sm-4">
                              <input type="checkbox" id="intermediario" name="intermediario" value="1"> INTERMEDIARY
                              <p style="color:#7f8c8d;">
                                Trading companies that buy and sell Small Producers’ Symbol products, and do not place these products on the final consumer market under their own name or trademark.
                              </p>
                            </label>


                          </div>
                        </div>
                      </div>


                    <div class="form-group">
                      <label for="sitio_web" class="col-sm-2 control-label">Certification Entity (if you use the services of a certification entity)</label>
                      <div class="col-sm-10">
                        <select class="form-control" name="idoc" id="idoc">
                          <option value="">Choose one</option>
                          <?php 
                          while($oc = mysql_fetch_assoc($row_oc)){
                            echo "<option value='".$oc['idoc']."'>".$oc['abreviacion']."</option>";
                          }
                           ?>
                        </select>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="sitio_web" class="col-sm-2 control-label">Website</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="sitio_web" name="sitio_web">
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="telefono" class="col-sm-2 control-label">* Office Phone</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="telefono" name="telefono" placeholder="Enter the office phone" required>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="email" class="col-sm-2 control-label">* Email</label>
                      <div class="col-sm-10">
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter the email" required>
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="pais" class="col-sm-2 control-label">Country</label>
                      <div class="col-sm-10">
                        <select name="pais" id="pais" class="form-control" required>
                          <option value="">Select Country</option>
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
                      <label for="direccion_oficina" class="col-sm-2 control-label">Office address</label>
                      <div class="col-sm-10">
                        <input type="text" id="direccion_oficina" class="form-control" name="direccion_oficina" placeholder="Office address">
                      </div>
                    </div>
                    <p class="alert alert-warning text-center" style="padding:7px;">Fiscal data (Optional)</p>
                    <div class="form-group">
                      <label for="razon_social" class="col-sm-2 control-label">Business name</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="razon_social" name="razon_social">
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="direccion_fiscal" class="col-sm-2 control-label">Fiscal address</label>
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
                    <button type="submit" class="btn btn-success form-control" onclick="return validar();"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Create account</button>

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
              <p class="alert alert-warning">To have better performance within D-SPP.ORG we recommend using the following browsers: <a href="https://www.google.com.mx/chrome/browser/desktop/" target="_new">Google Chrome</a>, <a href="https://www.mozilla.org/es-MX/firefox/new/" target="_new">Mozilla Firefox</a>, to click on the name in case of not having it.</p>
            </div> 

            <div class="col-md-4">
              <img src="img/FUNDEPPO.jpg" class="text-center img-responsive" alt="SPP GLOBAL">
            </div>
            <div class="col-md-8">
              <h5 class="text-justify" style="color:#27ae60">
                Who are the Small Producers' Organizations certified with the SPP?
              </h5>
              <p class="text-justify">
                Since the launch globally in 2011, many organizations approached for certification. Since that date there are already many organizations that rely on the Small Producers' Symbol.
              </p>
              <p class="text-justify alert alert-success" style="padding:5.5px;">
                <a href="lista_opp.php">Check the list of Small Producers' Organizations certified with the SPP here.</a>
              </p>
              <h5 class="text-justify" style="color:#27ae60">Who are the companies?</h5>
              <p class="text-justify">
                There are many companies committed to the mission and vision of SPP and fully support the development of Small Producers' Organizations.
              </p>
              <p class="text-justify alert alert-success" style="padding:5.5px;">
                <a href="lista_empresas.php">Check the list of companies registered with the SPP here.</a>
              </p>
            </div>


            <div class="col-md-12">
            <hr>
              <div class="panel panel-success">
                <div class="panel-heading">
                  <h3 class="panel-title">WHAT IS THE SPP?</h3>
                </div>
                <div class="panel-body text-justify">
                  <p>
                    The Small Producers’ Symbol, SPP, is a label that represents an alliance among organized small producers to build a local and global market that values the identity and the economic, social, cultural and ecological contributions of products from Small Producers’ Organizations. This alliance is based on a relationship of collaboration, trust and co-responsibility among women and men who are small producers, with buyers and consumers. The SPP is backed by an independent certification system.
                  </p>
                  <p>
                    The SPP is backed by an independent certification system, guaranteeing consumers that products come from authentic, democratic, self-managing organizations of small producers, and that they have been produced in line with criteria for economic, social, cultural and ecological sustainability, and commercialized under fair conditions.
                  </p>
                  <!--El Símbolo de Pequeños Productores es una iniciativa lanzada en el año 2006 por la CLAC (Coordinadora Latinoamericana y del Caribe de Pequeños Productores de Comercio Justo) con el apoyo del movimiento de Comercio Justo y Economía Solidaria de varios continentes. Para garantizar el adecuado uso de este Símbolo, las organizaciones de pequeños productores crearon la FUNDEPPO (Fundación de Pequeños Productores Organizados), la cual permite asegurar que este Símbolo realmente beneficie a los pequeños productores, las comunidades y los consumidores. FUNDEPPO trabaja con organismos y profesionales calificados para certificar de manera independiente y confiable el cumplimiento de las normas del Símbolo.-->
                  <hr>
                  <a class="" href="http://spp.coop/" target="_blank">Go to Web Site</a>
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