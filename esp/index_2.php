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
$fecha = time();
$asunto_usuario = "D-SPP Datos de Usuario / User Data";

/**********************************************/

/****************************** INICIA FORMULARIO INSERTAR OPP **************************************************/
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "registro_opp")) {
  mysql_select_db($database_dspp, $dspp);

  $pais = $_POST['pais'];

  $query = "SELECT idopp, spp, pais FROM opp WHERE pais = '$pais'";
  $ejecutar_spp = mysql_query($query) or die(mysql_error());
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

    while($datos_opp = mysql_fetch_assoc($ejecutar_spp)) {
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

  $spp_oc = $_POST['spp_oc'];
  $query = "SELECT idoc, spp FROM oc WHERE spp = '$spp_oc'";
  $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
  $oc = mysql_fetch_assoc($ejecutar);


  $insertSQL = sprintf("INSERT INTO opp (idoc, spp, nombre, abreviacion, password, sitio_web, email, telefono, pais, ciudad, razon_social, direccion_oficina, direccion_fiscal, rfc, ruc, fecha_registro) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($oc['idoc'], "text"),
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
              <td align="left"><br><b>Nombre / Name:</b> <span style="color:#27ae60;">'.$_POST['nombre'].'</span></td>
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
      //$mail->Username = "soporte@d-spp.org";
      //$mail->Password = "/aung5l6tZ";
      $mail->Subject = utf8_decode($asunto_usuario);
      $mail->Body = utf8_decode($cuerpo);
      $mail->MsgHTML(utf8_decode($cuerpo));
      $mail->Send();
      $mail->ClearAddresses();

      $insertGoTo = "index.php?OPP&mensaje=registrado";
      if (isset($_SERVER['QUERY_STRING'])) {
        $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
        $insertGoTo .= $_SERVER['QUERY_STRING'];
      }
      header(sprintf("Location: %s", $insertGoTo));
}
/****************************** FIN FORMULARIO INSERTAR OPP **************************************************/

/****************************** INICIA FORMULARIO INSERTAR COM **************************************************/
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "registro_empresa")) {
  mysql_select_db($database_dspp, $dspp);

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

    $spp = "COM-".$paisDigitos."-".$fechaDigitos."-".$contador;

    while ($datos_empresa = mysql_fetch_assoc($ejecutar_spp)) {
      if($datos_empresa['spp'] == $spp){
        //echo "<b style='color:red'>es igual el OPP con id: $datos_empresa[idf]</b><br>";
        $contador++;
        $contador = str_pad($contador, 3, "0", STR_PAD_LEFT);
        $spp = "COM-".$paisDigitos."-".$fechaDigitos."-".$contador;
      }/*else{
        echo "el id encontrado es: $datos_empresa[idf]<br>";
      }*/
      
    }
  }
  //echo "se ha creado un nuevo idf de opp el cual es: <b>$idfcom</b>";

  $logitud = 8;
  $psswd = substr( md5(microtime()), 1, $logitud);

  $spp_oc = $_POST['spp_oc'];
  $query = "SELECT idoc,spp FROM oc WHERE spp = '$spp_oc'";
  $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
  $oc = mysql_fetch_assoc($ejecutar);


  $insertSQL = sprintf("INSERT INTO empresa (idoc, spp, nombre, abreviacion, password, sitio_web, email, telefono, pais, ciudad, razon_social, direccion_oficina, direccion_fiscal, rfc, ruc, fecha_registro) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",

     GetSQLValueString($oc['idoc'], "int"),
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
                <td style="text-align:justify;padding-top:10px;"><i>Felicidades, se han registrado sus datos correctamente. A continuación se muestra su <b>#SPP y su contraseña, necesarios para poder inicia sesión</b>: <a href="http://d-spp.org/?COM" target="_new">www.d-spp.org/?COM</a></i>, una vez que haya iniciado sesión se le recomienda cambiar su contraseña en la sección Información COM, en dicha sección se encuentran sus datos los cuales pueden ser modificados en caso de ser necesario.</td>
              </tr>
              <tr>
                <td style="text-align:justify;padding-top:10px;"><i>Congratulations , your data have been recorded correctly. Below is your <b>#SPP and password needed to log in </b>: <a href="http://d-spp.org/?COM" target="_new">www.d-spp.org/?COM</a></i>, once you have logged you are advised to change your password on the Information COM section, in that section are data which can be modified if be necessary.</td>
              </tr>
          <tr>
            <td align="left"><br><b>Nombre de la Empresa / Company Name:</b> <span style="color:#27ae60;">'.$_POST['nombre'].'</span></td>
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
    //$mail->Username = "soporte@d-spp.org";
    //$mail->Password = "/aung5l6tZ";
    $mail->Subject = utf8_decode($asunto_usuario);
    $mail->Body = utf8_decode($cuerpo);
    $mail->MsgHTML(utf8_decode($cuerpo));
    $mail->Send();
    $mail->ClearAddresses();


  $insertGoTo = "index.php?COM&mensaje=registrado";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
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
    $MM_redirectLoginSuccess = "com/main_menu.php";
    $MM_redirectLoginFailed = "?COM";
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

?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="img/FUNDEPPO.png">

    <!-- Enjoy Hint -->

  <!--<link href="enjoyhint/enjoyhint.css" rel="stylesheet">
  <script src="enjoyhint/enjoyhint.min.js"></script>
    <!-- Enjoy Hint -->



    <title>FUNDEPPO | D-SPP</title>

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

  <body>
  
  <div class="container-fluid">  

    <ul class="nav nav-pills">
      <li role="presentation" style="margin:0px;padding:0px;"><a href="index.php"><img src="../img/FUNDEPPO.png" alt=""></a></li>
      <li role="presentation" <? if(isset($_GET['OPP'])){?> class="active" <? }?>><a href="?OPP">OPP</a></li>
      <li role="presentation" <? if(isset($_GET['OC'])){?> class="active" <? }?>><a href="?OC">OC</a></li>
      <li role="presentation" <? if(isset($_GET['COM'])){?> class="active" <? }?>><a href="?COM">EMPRESAS</a></li>
      <li role="presentation" <? if(isset($_GET['ADM'])){?> class="active" <? }?>><a href="?ADM">ADM</a></li>
      <li role="presentation" <? if(isset($_GET['RECURSOS'])){?> class="active" <? }?>><a href="#">RECURSOS</a></li>
    </ul>

    <hr>
    <div class="col-lg-12" >
      <?php 
      if(isset($_GET['listaOPP']) || isset($_GET['listaCOM'])){ // INICIA IF
      ?>
        <div class="col-lg-12 col-md-12" align="center">
          <div class="row">
            <div class="col-lg-6">
              <h5 class="text-justify" style="color:#27ae60">
                ¿Quiénes son las Organizaciones de Pequeños Produtores certificadas con el SPP?
              </h5>
              <p class="text-justify">
                Desde el lanzamiento a nivel global en el año 2011, muchas Organizaciones de Pequeños Productores se acercaron para obtener la certificación. Desde esa fecha ya hay muchas Organizaciones que confían en el Símbolo de Pequeños Productores.
              </p>
              <p class="text-justify" style="padding:5.5px;">
                <a class="btn btn-success" href="?listaOPP">Revisa la lista de Organizaciones de Pequeños Productores certificadas con el SPP aquí.</a>
              </p>
            </div>
            <div class="col-lg-6">
              <h5 class="text-justify" style="color:#27ae60">¿Quiénes son los Compradores?</h5>
              <p class="text-justify">
                Existen muchas empresas comprometidas con la misión y visión del SPP y apoyan íntegramente al desarrollo de las Organizaciones de Pequeños Productores.
              </p>
              <p class="text-justify" style="padding:5.5px;">
                <a class="btn btn-success" href="?listaCOM">Revisa la lista de Empresas registradas con el SPP aquí.</a>
              </p>
            </div>
          </div>
        </div>
        <?php 
        if(isset($_GET['listaOPP'])){ // INICIA IF LISTA OPP
        ?>
          <div class="row">
            <div class="col-lg-12">
              <div class="row">
                <div class="col-md-12">
                  <?php /*
                      if(isset($_POST['buscarPalabra']) && $_POST['buscarPalabra'] == 1){
                        $palabra = $_POST['palabra'];
                        $query = "SELECT opp.idopp, opp.idf, opp.nombre, opp.abreviacion, opp.sitio_web, opp.email, opp.telefono, opp.pais, opp.estado, opp.estatusPagina, status_pagina.nombre AS 'nombreEstatusPagina', solicitud_certificacion.idsolicitud_certificacion, certificado.idcertificado, certificado.vigenciafin, certificado.entidad FROM opp INNER JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp LEFT JOIN certificado ON opp.idopp = certificado.idopp INNER JOIN status_pagina ON opp.estatusPagina = status_pagina.idEstatusPagina WHERE opp.estado  IS NOT NULL AND opp.estado != 'ARCHIVADO' AND (opp.idf LIKE '%$palabra%' OR opp.nombre LIKE '%$palabra%' OR opp.abreviacion LIKE '%$palabra%' OR opp.email LIKE '%$palabra%' OR opp.telefono LIKE '%$palabra%')  GROUP BY opp.idopp  ORDER BY opp.nombre ASC";

                        //$query = "SELECT opp.*,status_pagina.idEstatusPagina, status_pagina.nombre AS 'nombreEstatusPagina', certificado.idcertificado FROM opp LEFT JOIN oc ON opp.idoc = oc.idoc LEFT JOIN status_pagina ON opp.estatusPagina = status_pagina.idEstatusPagina INNER JOIN certificado ON opp.idopp = certificado.idopp WHERE opp.estado  IS NOT NULL AND opp.estado != 'ARCHIVADO' AND (opp.idf LIKE '%$palabra%' OR opp.nombre LIKE '%$palabra%' OR opp.abreviacion LIKE '%$palabra%' OR opp.email LIKE '%$palabra%' OR opp.telefono LIKE '%$palabra%')";
                        $row_opp = mysql_query($query,$dspp) or die(mysql_error());
                        $totalOPP = mysql_num_rows($row_opp);
                      }else if(isset($_POST['busquedaPais']) && $_POST['busquedaPais'] == 1){
                        $pais = $_POST['pais'];
                        $query = "SELECT opp.idopp, opp.idf, opp.nombre, opp.abreviacion, opp.sitio_web, opp.email, opp.telefono, opp.pais, opp.estado, opp.estatusPagina, status_pagina.nombre AS 'nombreEstatusPagina', solicitud_certificacion.idsolicitud_certificacion, certificado.idcertificado, certificado.vigenciafin, certificado.entidad FROM opp INNER JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp LEFT JOIN certificado ON opp.idopp = certificado.idopp INNER JOIN status_pagina ON opp.estatusPagina = status_pagina.idEstatusPagina WHERE opp.estado  IS NOT NULL AND opp.estado != 'ARCHIVADO' AND opp.pais = '".$pais."' GROUP BY opp.idopp  ORDER BY opp.nombre ASC";
                        //$query = "SELECT opp.*,status_pagina.idEstatusPagina, status_pagina.nombre AS 'nombreEstatusPagina', certificado.idcertificado FROM opp LEFT JOIN oc ON opp.idoc = oc.idoc LEFT JOIN status_pagina ON opp.estatusPagina = status_pagina.idEstatusPagina INNER JOIN certificado ON opp.idopp = certificado.idopp WHERE opp.estado  IS NOT NULL AND opp.estado != 'ARCHIVADO' AND opp.pais = '".$pais."'";
                        $row_opp = mysql_query($query,$dspp) or die(mysql_error());
                        $totalOPP = mysql_num_rows($row_opp);

                      }else{
                        //$query = "SELECT opp.*,status_pagina.idEstatusPagina, status_pagina.nombre AS 'nombreEstatusPagina', certificado.idcertificado FROM opp LEFT JOIN oc ON opp.idoc = oc.idoc LEFT JOIN status_pagina ON opp.estatusPagina = status_pagina.idEstatusPagina INNER JOIN certificado ON opp.idopp = certificado.idopp WHERE opp.estado  IS NOT NULL AND opp.estado != 'ARCHIVADO'"; //PONER Y QUE TENGAN CERTIFICADO
                        $query = "SELECT opp.idopp, opp.idf, opp.nombre, opp.abreviacion, opp.sitio_web, opp.email, opp.telefono, opp.pais, opp.estado, opp.estatusPagina, status_pagina.nombre AS 'nombreEstatusPagina', solicitud_certificacion.idsolicitud_certificacion, certificado.idcertificado, certificado.vigenciafin, certificado.entidad FROM opp INNER JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp LEFT JOIN certificado ON opp.idopp = certificado.idopp INNER JOIN status_pagina ON opp.estatusPagina = status_pagina.idEstatusPagina WHERE opp.estado  IS NOT NULL AND opp.estado != 'ARCHIVADO' GROUP BY opp.idopp  ORDER BY opp.nombre ASC";
                        $row_opp = mysql_query($query,$dspp) or die(mysql_error());
                        $totalOPP = mysql_num_rows($row_opp);
                      }*/
                   ?>
                  <table class="table table-bordered table-hover table-condensed" style="font-size:12px">
                    <thead>
                      <tr class="text-center">
                        <th colspan="5" class="text-center" style="background:#e74c3c">
                          <p style="color:#ecf0f1">LISTA DE ORGANIZACIONES DE PEQUEÑOS PRODUCTORES</p>
                        </th>
                        <th colspan="4">
                          <form action="" method="POST" class="navbar-form navbar-left" role="search">
                            <div class="form-group">
                              <input type="text" name="palabra" class="form-control" placeholder="Realizar busqueda">
                            </div>
                            <button type="submit" class="btn btn-default">Buscar</button>
                            <input type="hidden" name="buscarPalabra" value="1">
                          </form>
                        </th>
                        <th>
                          <form action="" method="POST">
                            <label for="pais">Buscar por País</label>
                            <select name="pais" class="form-control" id="pais" onchange="this.form.submit()">
                              <option value="">Selecciona un País</option>
                              <?php 
                              $query = "SELECT * FROM paises"; 
                              $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
                              while($row_pais = mysql_fetch_assoc($ejecutar)){
                                echo '<option value="'.utf8_encode($row_pais['nombre']).'">'.utf8_encode($row_pais['nombre']).'</option>';
                              }
                              ?>
                            </select>
                            <input type="hidden" name="busquedaPais" value="1"> 
                          </form>
                        </th>
                        <th>
                          <p>TOTAL: <?php echo $totalOPP; ?></p>
                        </th>
                      </tr>
                      <tr class="alert alert-success">
                        <th class="text-center">Nº</th>
                        <th class="text-center">IDENTIFICACIÓN</th>
                        <th class="text-center">NOMBRE DE LA ORGANIZACIÓN</th>
                        <th class="text-center">ABREVIACIÓN</th>
                        <th class="text-center">PAÍS</th>
                        <th class="text-center">PRODUCTO CERTIFICADO</th>
                        <th class="text-center">FECHA SIGUIENTE EVALUACIÓN</th>
                        <th class="text-center">ESTATUS</th>
                        <th class="text-center">ENTIDAD QUE OTORGÓ EL CERTIFICADO</th>
                        <th class="text-center">CORREO ELECTRONICO y/o SITIO WEB</th>
                        <th class="text-center">TELÉFONO</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php 
                      $contador = 1;
                      while($datosOPP = mysql_fetch_assoc($row_opp)){
                        if(isset($datosOPP['entidad'])){
                          $queryEntidad = "SELECT idoc, abreviacion FROM oc WHERE idoc = $datosOPP[entidad]";
                          $ejecutar = mysql_query($queryEntidad,$dspp) or die(mysql_error());
                          $row_entidad = mysql_fetch_assoc($ejecutar);
                          $entidad = $row_entidad['abreviacion'];
                        }else{
                          $entidad = "<span style='color:#e74c3c'>No Disponible</span>";
                        }


                        if(empty($datosOPP['idsolicitud_certificacion'])){
                          $producto = "No Disponible";
                        }else{
                          $queryProducto = "SELECT idproducto, producto, idsolicitud_certificacion FROM productos WHERE idsolicitud_certificacion = $datosOPP[idsolicitud_certificacion]";
                          $ejecutar_producto = mysql_query($queryProducto,$dspp) or die(mysql_error());
                        }
                        
                      ?>

                        <tr>
                          <td class="text-center"><?php echo $contador; ?></td>
                          <td class="text-center"><?php echo $datosOPP['idf']; ?></td>
                          <td class=""><?php echo $datosOPP['nombre']; ?></td>
                          <td class=""><?php echo $datosOPP['abreviacion']; ?></td>
                          <td class="text-center"><?php echo $datosOPP['pais']; ?></td>
                          <td class="">
                            <?php 
                            if(isset($producto)){
                              echo "<span style='color:#e74c3c'>".$producto."</span>";
                            }else{
                              while($row_producto = mysql_fetch_assoc($ejecutar_producto)){
                                echo $row_producto['producto']." - ";
                              }
                            }
                             ?>
                          </td>
                          <td class="text-center">
                            <?php 
                            if(empty($datosOPP['vigenciafin'])){
                              echo "<span style='color:#e74c3c'>No Disponible</span>";
                            }else{
                              echo date('d-m-Y', strtotime($datosOPP['vigenciafin']));
                            } 
                            ?>
                          </td>
                          <td class="text-center"><?php echo $datosOPP['nombreEstatusPagina']; ?></td>
                          <td class="text-center">
                            <?php 
                            echo $entidad;
                             ?>
                          </td>
                          <td class=""><?php echo $datosOPP['email']; ?></td>
                          <td class=""><?php echo $datosOPP['telefono']; ?></td>
                        </tr>

                      <?php
                      $contador++;
                      }
                      ?>
                    </tbody>
                  </table>
                </div>          
              </div>
            </div>
          </div>
        <?php
        } // TERMINA IF LISTA OPP
        if(isset($_GET['listaCOM'])){ // INICIA IF LISTA COM

/*

          if(isset($_POST['buscarPalabra']) && $_POST['buscarPalabra'] == 1){
            $palabra = $_POST['palabra'];

            $queryCOM = "SELECT com.idcom, com.nombre, com.pais, com.sitio_web, com.email, com.telefono, com.estado, com.estatusPagina, solicitud_registro.idcom, solicitud_registro.idsolicitud_registro, solicitud_registro.tipo_empresa, status_pagina.idEstatusPagina, status_pagina.nombre AS 'nombreEstatusPagina', certificado.idcertificado, certificado.vigenciafin FROM com INNER JOIN solicitud_registro ON com.idcom = solicitud_registro.idcom INNER JOIN status_pagina ON com.estatusPagina = status_pagina.idEstatusPagina LEFT JOIN certificado ON com.idcom = certificado.idcom  WHERE com.estado  IS NOT NULL AND com.estado != 'ARCHIVADO' AND (com.idf LIKE '%$palabra%' OR com.nombre LIKE '%$palabra%' OR com.abreviacion LIKE '%$palabra%' OR com.email LIKE '%$palabra%' OR com.telefono LIKE '%$palabra%') GROUP BY com.idcom";
            $ejecutar_com = mysql_query($queryCOM,$dspp) or die(mysql_error());

            $totalOPP = mysql_num_rows($ejecutar_com);
          }else if(isset($_POST['busquedaPais']) && $_POST['busquedaPais'] == 1){
            $pais = $_POST['pais'];

            $queryCOM = "SELECT com.idcom, com.nombre, com.pais, com.sitio_web, com.email, com.telefono, com.estado, com.estatusPagina, solicitud_registro.idcom, solicitud_registro.idsolicitud_registro, solicitud_registro.tipo_empresa, status_pagina.idEstatusPagina, status_pagina.nombre AS 'nombreEstatusPagina', certificado.idcertificado, certificado.vigenciafin FROM com INNER JOIN solicitud_registro ON com.idcom = solicitud_registro.idcom INNER JOIN status_pagina ON com.estatusPagina = status_pagina.idEstatusPagina LEFT JOIN certificado ON com.idcom = certificado.idcom  WHERE com.estado  IS NOT NULL AND com.estado != 'ARCHIVADO' AND com.pais = '".$pais."' GROUP BY com.idcom";
            $ejecutar_com = mysql_query($queryCOM,$dspp) or die(mysql_error());
            $totalOPP = mysql_num_rows($ejecutar_com);

          }else{
//SELECT com.idcom, com.nombre, solicitud_registro.idcom, solicitud_registro.idsolicitud_registro FROM com INNER JOIN solicitud_registro ON com.idcom = solicitud_registro.idcom GROUP BY com.idcom

            //$queryCOM = "SELECT com.idcom, com.nombre, solicitud_registro.idsolicitud_registro, solicitud_registro.tipo_empresa, com.pais, com.sitio_web, com.email, com.telefono, com.estado, com.estatusPagina, status_pagina.nombre AS 'nombreEstatusPagina', certificado.idcertificado, certificado.vigenciafin, certificado.idcom FROM com LEFT JOIN solicitud_registro ON com.idcom = solicitud_registro.idcom  INNER JOIN certificado ON com.idcom = certificado.idcom INNER JOIN status_pagina ON com.estatusPagina = status_pagina.idEstatusPagina WHERE com.estado  IS NOT NULL AND com.estado != 'ARCHIVADO'" GROUP BY com.idcom;
            //$queryCOM = "SELECT com.idcom, com.nombre, solicitud_registro.idsolicitud_registro, solicitud_registro.tipo_empresa, com.pais, com.sitio_web, com.email, com.telefono, com.estado, com.estatusPagina, status_pagina.nombre AS 'nombreEstatusPagina', certificado.idcertificado, certificado.vigenciafin, certificado.idcom FROM com LEFT JOIN solicitud_registro ON com.idcom = solicitud_registro.idcom INNER JOIN certificado ON com.idcom = certificado.idcom INNER JOIN status_pagina ON com.estatusPagina = status_pagina.idEstatusPagina WHERE com.estado IS NOT NULL AND com.estado != 'ARCHIVADO' GROUP BY com.idcom";
            $queryCOM = "SELECT com.idcom, com.nombre, com.pais, com.sitio_web, com.email, com.telefono, com.estado, com.estatusPagina, solicitud_registro.idcom, solicitud_registro.idsolicitud_registro, solicitud_registro.tipo_empresa, status_pagina.idEstatusPagina, status_pagina.nombre AS 'nombreEstatusPagina', certificado.idcertificado, certificado.vigenciafin FROM com INNER JOIN solicitud_registro ON com.idcom = solicitud_registro.idcom INNER JOIN status_pagina ON com.estatusPagina = status_pagina.idEstatusPagina LEFT JOIN certificado ON com.idcom = certificado.idcom WHERE com.estado IS NOT NULL AND com.estado != 'ARCHIVADO' GROUP BY com.idcom";

            $ejecutar_com = mysql_query($queryCOM,$dspp) or die(mysql_error());

            $totalOPP = mysql_num_rows($ejecutar_com);
          }
*/


        ?>

        <table class="table table-bordered table-hover table-condensed" style="font-size:12px">
          <thead>
            <tr class="text-center">
              <th colspan="5" class="text-center" style="background:#e74c3c">
                <p style="color:#ecf0f1">LISTA DE ORGANIZACIONES DE PEQUEÑOS PRODUCTORES</p>
              </th>
              <th colspan="2">
                <form action="" method="POST" class="navbar-form navbar-left" role="search">
                  <div class="form-group">
                    <input type="text" name="palabra" class="form-control" placeholder="Realizar busqueda">
                  </div>
                  <button type="submit" class="btn btn-default">Buscar</button>
                  <input type="hidden" name="buscarPalabra" value="1">
                </form>
              </th>
              <th>
                <form action="" method="POST">
                  <label for="pais">Buscar por País</label>
                  <select name="pais" class="form-control" id="pais" onchange="this.form.submit()">
                    <option value="">Selecciona un País</option>
                    <?php 
                    $query = "SELECT * FROM paises"; 
                    $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
                    while($row_pais = mysql_fetch_assoc($ejecutar)){
                      echo '<option value="'.utf8_encode($row_pais['nombre']).'">'.utf8_encode($row_pais['nombre']).'</option>';
                    }
                    ?>
                  </select>
                  <input type="hidden" name="busquedaPais" value="1"> 
                </form>
              </th>
              <th>
                <p>TOTAL: <?php echo $totalOPP; ?></p>
              </th>
            </tr>

            <tr class="alert alert-success" style="padding:7px;">
              <th>Nº</th>
              <th class="text-center">NOMBRE DE LA EMPRESA</th>
              <th class="text-center">TIPO EMPRESA</th>
              <th class="text-center">PAÍS</th>
              <th class="text-center">PRODUCTO(S)</th>
              <th class="text-center">VIGENCIA DEL REGISTRO</th>
              <th class="text-center">ESTATUS</th>
              <th class="text-center">CORREO ELECTRÓNICO / SITIO WEB</th>
              <th class="text-center">TELÉFONO</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            $contador = 1;
            while($row_com = mysql_fetch_assoc($ejecutar_com)){
              if(empty($row_com['idsolicitud_registro'])){
                $producto = "No Disponible";
              }else{
                $queryProducto = "SELECT idproducto, producto, idsolicitud_registro FROM productos WHERE idsolicitud_registro = $row_com[idsolicitud_registro]";
                $ejecutar_producto = mysql_query($queryProducto,$dspp) or die(mysql_error());
              }


            ?>
              <tr>
                <td><?php echo $contador; ?></td>
                <td><?php echo $row_com['nombre']; ?></td>
                <td>
                  <?php 
                  if(empty($row_com['tipo_empresa'])){
                    echo "<span style='color:#e74c3c'>No Disponible</span>";
                  }else{
                    echo $row_com['tipo_empresa'];
                  }
                  ?>
                </td>
                <td><?php echo $row_com['pais']; ?></td>
                <td>
                  <?php 
                  if(isset($producto)){
                    echo "<span style='color:#e74c3c'>".$producto."</span>";
                  }else{
                    while($row_producto = mysql_fetch_assoc($ejecutar_producto)){
                      echo $row_producto['producto']." - ";
                    }
                  }
                   ?>
                </td>
                <td>
                  <?php 
                  if(empty($row_com['vigenciafin'])){
                    echo "<span style='color:#e74c3c'>No Disponible</span>";
                  }else{
                    echo date('d-m-Y', strtotime($row_com['vigenciafin']));
                  } 
                  ?>
                </td>
                <td><?php echo $row_com['nombreEstatusPagina']; ?></td>
                <td><?php echo $row_com['email']; ?></td>
                <td><?php echo $row_com['telefono']; ?></td>
              </tr>
            <?php
            $contador++;
            }
             ?>
          </tbody>
        </table>

        <?php
        }// TERMINA IF LISTA COM
         ?>
        <div class="col-lg-12 well" style="padding:7px;">
          <p>
            NOTAS:
          </p>
          <p>
            1. El estatus de 'En Revisión' significa que la OPP puede encontrarse en cualquiera de los siguientes sub estatus: 'En proceso de renovación', 'Certificado expirado' o 'Suspendido'
          </p>
          <p>
            2. Es responsabilidad de los interesados verificar si la OPP se encuentran en proceso de renovación del certificado, cuando en la presente lista se indica que el estatus es "En Revisión"
          </p>
          <p>
            3. El estatus de 'Cancelado' siginifica que la OPP ya no esta certificada por Incumplimiento con el Marco Regulatorio SPP o por renuncia voluntaria. Si fue cancelado por incumpliento con el marco regulatorio, deberá esperar dos años a partir de la cancelación para volver a solicitar la certificación.
          </p>
        </div>
      <?php
      }else{ //INICIA ELSE
      ?>
          <div class="col-lg-12 col-md-12">
            <p class="alert alert-warning">Para tener un mejor desempeño dentro de D-SPP.ORG le recomendamos usar los siguientes navegadores: <a href="https://www.google.com.mx/chrome/browser/desktop/" target="_new">Google Chrome</a>, <a href="https://www.mozilla.org/es-MX/firefox/new/" target="_new">Mozilla Firefox</a>, dar click sobre el nombre en caso de no contar con el mismo.</p>
          </div>

      <div class="col-lg-8"><!-----------------  INICIA DIV-8 ------------------------>
        
        <?php if(isset($_GET['registroOPP'])){  // INICIA IF REGISTRO_OPP


          $query_pais = "SELECT nombre FROM paises ORDER BY nombre ASC";
          $pais = mysql_query($query_pais, $dspp) or die(mysql_error());
          //$totalRows_pais = mysql_num_rows($pais);


          $query_oc = "SELECT idoc, spp, abreviacion, pais FROM oc ORDER BY nombre ASC";
          $oc = mysql_query($query_oc, $dspp) or die(mysql_error());
          $row_oc = mysql_fetch_assoc($oc);
          $totalRows_oc = mysql_num_rows($oc);
          ?>
          <!-------------------------------------------- INICIO FORMULARIO DE REGISTRO OPP  ------------------------------------------------------------------------>
          <div class="panel panel-primary">
            <div class="panel-heading">
              <h3 class="panel-title text-center">NUEVO REGISTRO</h3>
            </div>
            <div class="panel-body">

              <form class="" method="post" name="registro_opp" action="<?php echo $loginFormAction; ?>">
              <!--<form class="" method="post" name="registroOpp" action="control_procesos.php">-->
                <table class="table col-xs-8">
                  <!--<tr valign="baseline">
                    <th nowrap align="left" width="1">#SPP ó Usuario </th>
                    <td><input autofocus="autofocus" class="form-control" type="text" id="idf" name="idf" value="OPP-" size="32"></td>
                  </tr>-->
                  <!--<tr valign="baseline">
                    <th nowrap align="left">Password</th>
                    <td><input class="form-control" type="text" id="password" name="password" value="" size="32"></td>
                  </tr>-->
                  <tr valign="baseline">
                    <th colspan="2" class="alert alert-warning">El #SPP y la contraseña son proporcionados por D-SPP, dichos datos son enviados por email al OPP</th>
                  </tr>
                  <tr valign="baseline">
                    <th colspan="1" class="alert alert-info">* Información Requerida</th>
                  </tr>

                  <tr valign="baseline">
                    <th nowrap align="left">#SPP <br>(En caso de contar con uno)</th>
                    <td><input class="form-control" type="text" id="spp" name="spp" value="" size="32"></td>
                  </tr>
                  <tr valign="baseline">
                    <th nowrap align="left">* Nombre OPP</th>
                    <td><input class="form-control" type="text" id="nombre" name="nombre" value="" size="32" required></td>
                  </tr>
                  <tr valign="baseline">
                    <th nowrap align="left">Abreviacion OPP</th>
                    <td><input class="form-control" type="text" id="abreviacion" name="abreviacion" value="" size="32" ></td>
                  </tr>
                  <tr valign="baseline">
                    <th nowrap align="left">Sitio_web</th>
                    <td><input class="form-control" type="text" id="sitio_web" name="sitio_web" value="" size="32" ></td>
                  </tr>
                  <tr valign="baseline">
                    <th nowrap align="left">* Email Contacto</th>
                    <td><input class="form-control" type="email" id="email" name="email" value="" size="32" required></td>
                  </tr>
                  <tr valign="baseline">
                    <th nowrap align="left">* Telefono (CÓDIGO DE PAÍS+CÓDIGO DE ÁREA+NÚMERO</th>
                    <td><input class="form-control" type="text" id="telefono" name="telefono" value="" size="32" required></td>
                  </tr>

                  <tr valign="baseline">
                    <th nowrap align="left">* Pais</th>
                    <td>
                      <select  class="form-control" id="pais" name="pais" required>
                      <option value="">Selecciona</option>
                      <?php 
                      while($row_pais = mysql_fetch_assoc($pais)){
                      ?>
                        <option class="form-control" value="<?php echo utf8_encode($row_pais['nombre']); ?>"><?php echo utf8_encode($row_pais['nombre']); ?></option>
                      <?php
                      }
                      ?>
                      </select>
                    </td>
                  <tr>
                  <tr valign="baseline">
                    <th nowrap align="left">Ciudad</th>
                    <td><input class="form-control" type="text" id="ciudad" name="ciudad" value="" size="32" ></td>
                  </tr>
                  <tr valign="baseline">
                    <th nowrap align="left">Dirección Oficina</th>
                    <td><input class="form-control" type="text" id="direccion_oficina" name="direccion_oficina" value="" size="32" ></td>
                  </tr>   
                  <tr>
                    <td colspan="2" class="alert alert-warning text-center">Datos Fiscales (Opcionales)</td>
                  </tr>

                  <tr valign="baseline">
                    <th nowrap align="left">Dirección Fiscal</th>
                    <td><input class="form-control" type="text" id="direccion_fiscal" name="direccion_fiscal" value="" size="32" ></td>
                  </tr>
                  <tr valign="baseline">
                    <th nowrap align="left">Razon_social</th>
                    <td><input class="form-control" type="text" id="razon_socila" name="razon_social" value="" size="32"></td>
                  </tr>

                  <tr valign="baseline">
                    <th nowrap align="left">RFC</th>
                    <td><input class="form-control" type="text" id="rfc" name="rfc" value="" size="32"></td>
                  </tr>
                  <tr valign="baseline">
                    <th nowrap align="left">RUC</th>
                    <td><input class="form-control" type="text" id="ruc" name="ruc" value="" size="32"></td>
                  </tr>

                  <tr valign="baseline">
                    <td nowrap align="right">&nbsp;</td>
                    <td>
                      <button class="btn btn-success" id="registrarse" name="registrarse" type="submit" value="Enviar" aria-label="Left Align">
                        <span class="glyphicon glyphicon-open-file" aria-hidden="true"></span> Registrarse
                      </button>
                    </td>
                  </tr>
                </table>

                <input type="hidden" name="spp_oc" value="<?php echo $_GET['SPP_OC']; ?>">
                <input type="hidden" name="MM_insert" value="registro_opp">
              </form>
            </div>
          </div>

          <!-------------------------------------------- FIN FORMULARIO DE REGISTRO OPP  ------------------------------------------------------------------------>

        <?php }else if(isset($_GET['registroCOM'])){ // INICIA ELSE IF REGISTRO COM
       
          mysql_select_db($database_dspp, $dspp);
          $query_pais = "SELECT nombre FROM paises ORDER BY nombre ASC";
          $pais = mysql_query($query_pais, $dspp) or die(mysql_error());


          $query_oc = "SELECT idoc, spp, abreviacion, pais FROM oc ORDER BY nombre ASC";
          $oc = mysql_query($query_oc, $dspp) or die(mysql_error());
          $row_oc = mysql_fetch_assoc($oc);
          $totalRows_oc = mysql_num_rows($oc);

          ?>

          <!-------------------------------------------- INICIO FORMULARIO DE REGISTRO COM  ------------------------------------------------------------------------>

          <div class="panel panel-primary">
            <div class="panel-heading">
              <h3 class="panel-title text-center">NUEVO REGISTRO EMPRESA</h3>
            </div>
            <div class="panel-body">

              <form class="" method="post" name="registroOpp" action="<?php echo $loginFormAction; ?>">

                <table class="table col-xs-8">
                  <tr valign="baseline">
                    <th colspan="2" class="alert alert-warning">El #SPP y la contraseña son proporcionados por D-SPP, dichos datos son enviados por email a la Empresa</th>
                  </tr>
                  
                  <tr valign="baseline">
                    <th colspan="1" class="alert alert-info">* Información Requerida</th>
                  </tr>
                  <tr valign="baseline">
                    <th nowrap align="left">#SPP <br>(En caso de contar con uno)</th>
                    <td><input class="form-control" type="text" id="spp" name="spp" value="" size="32"></td>
                  </tr>
                  <tr valign="baseline">
                    <th nowrap align="left">* Nombre de la Empresa</th>
                    <td><input class="form-control" type="text" id="nombre" name="nombre" value="" size="32" required></td>
                  </tr>
                  <tr valign="baseline">
                    <th nowrap align="left">Abreviacion</th>
                    <td><input class="form-control" type="text" id="abreviacion" name="abreviacion" value="" size="32"></td>
                  </tr>
                  <tr valign="baseline">
                    <th nowrap align="left">Sitio web</th>
                    <td><input class="form-control" type="text" id="sitio_web" name="sitio_web" value="" size="32" ></td>
                  </tr>
                  <tr valign="baseline">
                    <th nowrap align="left">* Email Contacto</th>
                    <td><input class="form-control" type="email" id="email" name="email" value="" size="32" required></td>
                  </tr>
                  <tr valign="baseline">
                    <th nowrap align="left">* Telefono (CÓDIGO DE PAÍS+CÓDIGO DE ÁREA+NÚMERO)</th>
                    <td><input class="form-control" type="text" id="telefono" name="telefono" value="" size="32" required></td>
                  </tr>
                  <tr valign="baseline">
                    <th nowrap align="left">* Pais</th>
                    <td>
                      <select  class="form-control" id="pais" name="pais" required>
                        <option value="">Selecciona</option>
                        <?php 
                        while($row_pais = mysql_fetch_assoc($pais)) {  
                        ?>
                        <option class="form-control" value="<?php echo utf8_encode($row_pais['nombre']);?>" ><?php echo utf8_encode($row_pais['nombre']);?></option>
                        <?php
                        }
                        ?>
                      </select>
                    </td>
                  <tr>
                  <tr valign="baseline">
                    <th nowrap align="left">Ciudad</th>
                    <td><input class="form-control" type="text" id="ciudad" name="ciudad" value="" size="32"></td>
                  </tr>
                  <tr valign="baseline">
                    <th nowrap align="left">Dirección Oficina</th>
                    <td><input class="form-control" type="text" id="direccion_oficina" name="direccion_oficina" value="" size="32"></td>
                  </tr>
                  <tr>
                    <td colspan="2" class="alert alert-warning text-center">Datos Fiscales (Opcionales)</td>
                  </tr>

                  <tr valign="baseline">
                    <th nowrap align="left">Dirección Fiscal</th>
                    <td><input class="form-control" type="text" id="direccion_fiscal" name="direccion_fiscal" value="" size="32"></td>
                  </tr>
                  <tr valign="baseline">
                    <th nowrap align="left">Razon_social</th>
                    <td><input class="form-control" type="text" id="razon_socila" name="razon_social" value="" size="32"></td>
                  </tr>
                  <tr valign="baseline">
                    <th nowrap align="left">RFC</th>
                    <td><input class="form-control" type="text" id="rfc" name="rfc" value="" size="32" ></td>
                  </tr>
                  <tr valign="baseline">
                    <th nowrap align="left">RUC</th>
                    <td><input class="form-control" type="text" id="ruc" name="ruc" value="" size="32"></td>
                  </tr>
                  <tr valign="baseline">
                    <td nowrap align="right">&nbsp;</td>
                    <td>
                      <button class="btn btn-success" id="registrarse" name="registrarse" type="submit" value="Enviar" aria-label="Left Align">
                        <span class="glyphicon glyphicon-open-file" aria-hidden="true"></span> Registrarse
                      </button>
                    </td>
                  </tr>
                </table>
            
                <input type="hidden" name="spp_oc" value="<?php echo $_GET['SPP_OC']; ?>">
                <input type="hidden" name="MM_insert" value="registro_empresa">
              </form>
            </div>
          </div>
          <!-------------------------------------------- FIN FORMULARIO DE REGISTRO COM  ------------------------------------------------------------------------>
        <?php }else {// ELSE TERMINA REGISTRO COM ?>

          <? if(isset($_GET['mensaje'])){?>
            <p>
              <div class="alert alert-success" role="alert">
                <strong>Datos registrados correctamente, su #IDF y su contraseña se han enviado al correo que proporciono, dichos datos son necesarios para inicia sesión,</strong> en caso de que no reciba ningún email, por favor dirijase a la bandeja de spam.
              </div>
            </p>
          <? }?>

          <div class="col-lg-12 col-md-12" align="center">
            <div class="col-md-6">
              <img src="img/FUNDEPPO.jpg" class="text-center img-responsive" alt="FUNDEPPO">
            </div>
            <div class="col-lg-6 col-md-12">
              <h5 class="text-justify" style="color:#27ae60">
                ¿Quiénes son las Organizaciones de Pequeños Produtores certificadas con el SPP?
              </h5>
              <p class="text-justify">
                Desde el lanzamiento a nivel global en el año 2011, muchas Organizaciones de Pequeños Productores se acercaron para obtener la certificación. Desde esa fecha ya hay muchas Organizaciones que confían en el Símbolo de Pequeños Productores.
              </p>
              <p class="text-justify alert alert-success" style="padding:5.5px;">
                <a href="?listaOPP">Revisa la lista de Organizaciones de Pequeños Productores certificadas con el SPP aquí.</a>
              </p>
              <h5 class="text-justify" style="color:#27ae60">¿Quiénes son los Compradores?</h5>
              <p class="text-justify">
                Existen muchas empresas comprometidas con la misión y visión del SPP y apoyan íntegramente al desarrollo de las Organizaciones de Pequeños Productores.
              </p>
              <p class="text-justify alert alert-success" style="padding:5.5px;">
                <a href="?listaCOM">Revisa la lista de Empresas registradas con el SPP aquí.</a>
              </p>
            </div>
          </div>

        <?php } ?> 

      </div> <!-----------------  TERMINA DIV-8 ------------------------>


      <div class="col-lg-4" > <!-----------------  INICIA DIV-4 ------------------------>

        <? if(isset($_GET['OPP'])){?>
          <!-------------------------------------------- INICIO INICIO DE SESION OPP ------------------------------------------------------------------------>
              <div class="col-xs-12 alert alert-info">
                <div>
                  <h3 class="well">Ingreso OPP</h3>
                </div>
                <div class="panel-body">
                  <form ACTION="<?php echo $loginFormAction; ?>" METHOD="POST" class="form-signin" id="opp">
                   
                    <input type="text" id="SPP_OPP" name="SPP_OPP" class="form-control" placeholder="#SPP" required autofocus>
                    <br>
                    <input type="password" id="inputPassword" class="form-control" name="password" placeholder="Password" required>
                    <br>
                    <a href="#">¿Olvidaste tu contraseña?</a>
                    <br>
                    <button class="btn btn-primary btn-block" type="submit" style="border-radius:0px;">Ingresar</button>
                    <a class="btn btn-danger btn-block" style="border-radius:0px;" type="submit" name="registrarse" <?php if(isset($_GET['SPP_OC'])){echo "href='?registro&SPP_OC=$_GET[SPP_OC]'";}else{ echo "href='?registroOPP'";} ?>>Registrarse</a>
                  </form>
                </div>
              </div>
          <!-------------------------------------------- FIN INICIO DE SESION OPP ------------------------------------------------------------------------>

        <? } else if(isset($_GET['OC'])){?>
          <!-------------------------------------------- INICIO INICIO DE SESION OC ------------------------------------------------------------------------> 
              <div class="col-xs-12 alert alert-danger">
                <div>
                  <h3 class="well">Ingreso OC</h3>
                </div>
                <div class="panel-body">
                  <form ACTION="<?php echo $loginFormAction; ?>" METHOD="POST" class="form-signin" id="oc">
                   
                    <input type="text" id="SPP_OC" name="SPP_OC" class="form-control" placeholder="#SPP" required autofocus>
                    <br>
                    <input type="password" id="inputPassword" class="form-control" name="password" placeholder="Password" required>
                    <br>
                    <a href="#">¿Olvidaste tu contraseña?</a>
                    <br>
                    <button class="btn btn-primary btn-block" style="border-radius:0px;" type="submit">Ingresar</button>

                  </form>
                </div>
              </div>
          <!-------------------------------------------- FIN INICIO DE SESION OC ------------------------------------------------------------------------>


        <? }else if(isset($_GET['COM'])){?>
          <!-------------------------------------------- INICIO INICIO DE SESION COM ------------------------------------------------------------------------>
                  <div class="col-xs-12 alert alert-warning">
                    <div >
                      <h3 class="well">Ingreso EMPRESAS</h3>
                    </div>
                    <div class="panel-body">
                      <form ACTION="<?php echo $loginFormAction; ?>" METHOD="POST" class="form-signin" id="empresa">
                        <input type="text" id="SPP_EMPRESA" name="SPP_EMPRESA" class="form-control" placeholder="#SPP" required autofocus>
                        <br>
                        <input type="password" id="inputPassword" class="form-control" name="password" placeholder="Password" required>
                        <br>
                        <a href="#">¿Olvidaste tu contraseña?</a>
                        <br>
                        <button class="btn btn-primary btn-block" style="border-radius:0px;" type="submit">Ingresar</button>
                        <a class="btn btn-danger btn-block" style="border-radius:0px;" type="submit" name="registrarse" <?php if(isset($_GET['SPP_OC'])){echo "href='?registro&SPP_OC=$_GET[SPP_OC]'";}else{ echo "href='?registroCOM'";} ?>>Registrarse</a>
                      </form>
                    </div>
                  </div>
          <!-------------------------------------------- FIN INICIO DE SESION COM ------------------------------------------------------------------------>

        <? }else if(isset($_GET['ADM']) or isset($_GET['adm'])){?>
            <div class="col-xs-12 alert alert-success"  >
              <div class="well well-lg">
                <h3>FUNDEPPO</h3>
                <p>Inicio de sesión administradores</p>
              </div>
              <form ACTION="<?php echo $loginFormAction; ?>" METHOD="POST" class="form-signin" id="adm">
                <h2 class="form-signin-heading">Datos de ingreso</h2>
                <label for="inputEmail" class="sr-only">Email address</label>
                <input type="text" id="" name="username" class="form-control" placeholder="Username" required autofocus>
                <label for="inputPassword" class="sr-only">Password</label>
                <input type="password" id="inputPassword" class="form-control" name="password" placeholder="Password" required>
                <button class="btn btn-lg btn-primary btn-block" type="submit">Ingresar</button>
              </form>
            </div>
        <? }else if(isset($_GET['RECURSOS']) or isset($_GET['RECURSOS'])){?>
              <a href="../archivos/recursos/MODULO-OC-D-SPP_marzo-2016.pdf">MODULO OC</a>
        <?php }else{ ?>
            <div class="col-xs-12">
              <div class="panel panel-primary">
                <div class="panel-heading">
                  <h3 class="panel-title">Inicio de sesión</h3>
                </div>
                <div class="panel-body">
                  <p>Selecciona un tipo de usuario</p>
                  <ul class="nav nav-pills">
                  <li role="presentation" <? if(isset($_GET['OPP'])){?> class="active" <? }?>><a href="?OPP">OPP</a></li>
                  <li role="presentation" <? if(isset($_GET['OC'])){?> class="active" <? }?>><a href="?OC">OC</a></li>
                  <li role="presentation" <? if(isset($_GET['COM'])){?> class="active" <? }?>><a href="?COM">COM</a></li>
                  </ul>
                </div>
              </div>
            </div>
        <? }?>

      </div> <!-----------------  TERMINA DIV-4 ------------------------>
      <?php // TERMINA ELSE
      }
      if(!isset($_GET['registroCOM']) && !isset($_GET['registroOPP']) && !isset($_GET['listaOPP']) && !isset($_GET['listaCOM'])){
      ?>
          <div class="col-lg-12 col-md-12">
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
                <a class="btn btn-success" href="http://spp.coop/" target="_blank" role="button">Ir al Sitio Web</a>
              </div>
            </div>
          </div>
      <?php 
      }
       ?>
  </div> <!-- /container -->
  </body>
</html>