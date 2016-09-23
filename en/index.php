<?php require_once('Connections/dspp.php');
      require_once('../Connections/mail.php');
      
        //$asunto = "Nuevo Registro - D-SPP( Datos de Acceso )";

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
?>
<?php
// *** Validate request to login to this site.
if (!isset($_SESSION)) {
  session_start();
}

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
  $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}

/****************************** INICIA FORMULARIO INSERTAR OPP **************************************************/
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  mysql_select_db($database_dspp, $dspp);

  $pais = $_POST['pais'];

  $query = "SELECT * FROM opp WHERE pais = '$pais'";
  $ejecutar = mysql_query($query) or die(mysql_error());
  $datos_opp = mysql_fetch_assoc($ejecutar);
  $fecha = $_POST['fecha_inclusion'];

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
  }
  //echo "se ha creado un nuevo idf de opp el cual es: <b>$idfopp</b>";

  $logitud = 8;
  $psswd = substr( md5(microtime()), 1, $logitud);

  $idfoc = $_POST['idfoc'];
  $query = "SELECT idoc,idf FROM oc WHERE idf = '$idfoc'";
  $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
  $oc = mysql_fetch_assoc($ejecutar);


  $insertSQL = sprintf("INSERT INTO opp (idf, password, nombre, abreviacion, sitio_web, email, pais, idoc, razon_social, direccion_fiscal, rfc, fecha_inclusion) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($idfopp, "text"),
                       GetSQLValueString($psswd, "text"),
                       GetSQLValueString($_POST['nombre'], "text"),
                       GetSQLValueString($_POST['abreviacion'], "text"),
                       GetSQLValueString($_POST['sitio_web'], "text"),
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($_POST['pais'], "text"),
                       GetSQLValueString($oc['idoc'], "int"),
                       GetSQLValueString($_POST['razon_social'], "text"),
                       GetSQLValueString($_POST['direccion_fiscal'], "text"),
                       GetSQLValueString($_POST['rfc'], "text"),
                       GetSQLValueString($_POST['fecha_inclusion'], "int"));


  $Result1 = mysql_query($insertSQL, $dspp) or die(mysql_error());



        $destinatario = $_POST['email'];
        $asunto = "D-SPP Datos de Usuario / User Data"; 


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
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form2")) {
  mysql_select_db($database_dspp, $dspp);

  $pais = $_POST['pais'];

  $query = "SELECT * FROM com WHERE pais = '$pais'";
  $ejecutar = mysql_query($query) or die(mysql_error());
  $datos_com = mysql_fetch_assoc($ejecutar);
  $fecha = $_POST['fecha_inclusion'];

  setlocale(LC_ALL, 'en_US.UTF8');

  if(!empty($_POST['idf'])){
    $idfcom = $_POST['idf'];
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

    $idfcom = "COM-".$paisDigitos."-".$fechaDigitos."-".$contador;

    while ($datos_com = mysql_fetch_assoc($ejecutar)) {
      if($datos_com['idf'] == $idfcom){
        //echo "<b style='color:red'>es igual el OPP con id: $datos_com[idf]</b><br>";
        $contador++;
        $contador = str_pad($contador, 3, "0", STR_PAD_LEFT);
        $idfcom = "COM-".$paisDigitos."-".$fechaDigitos."-".$contador;
      }/*else{
        echo "el id encontrado es: $datos_com[idf]<br>";
      }*/
      
    }
  }
  //echo "se ha creado un nuevo idf de opp el cual es: <b>$idfcom</b>";

  $logitud = 8;
  $psswd = substr( md5(microtime()), 1, $logitud);

/*  $idfoc = $_POST['idfoc'];
  $query = "SELECT idoc,idf FROM oc WHERE idf = '$idfoc'";
  $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
  $oc = mysql_fetch_assoc($ejecutar);*/


  $insertSQL = sprintf("INSERT INTO com (idf, nombre, password, abreviacion, sitio_web, email, telefono, pais, direccion, fecha_inclusion, estado, direccion_fiscal, rfc, ruc, ciudad) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($idfcom, "text"),
                       GetSQLValueString($_POST['nombre'], "text"),
                       GetSQLValueString($psswd, "text"),
                       GetSQLValueString($_POST['abreviacion'], "text"),
                       GetSQLValueString($_POST['sitio_web'], "text"),
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($_POST['telefono'], "text"),
                       GetSQLValueString($_POST['pais'], "text"),
                       GetSQLValueString($_POST['direccion'], "text"),
                       GetSQLValueString($_POST['fecha_inclusion'], "text"),
                       GetSQLValueString($_POST['estado'], "text"),
                       GetSQLValueString($_POST['direccion_fiscal'], "text"),
                       GetSQLValueString($_POST['rfc'], "text"),
                       GetSQLValueString($_POST['ruc'], "text"),
                       GetSQLValueString($_POST['ciudad'], "text"));


  $Result1 = mysql_query($insertSQL, $dspp) or die(mysql_error());



        $destinatario = $_POST['email'];
        $asunto = "D-SPP Datos de Usuario / User Data"; 


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
              <td align="left"><br><b>#SPP:</b> <span style="color:#27ae60;">'.$idfcom.'</span></td>
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
if (isset($_POST['IDF'])) {

    $loginUsername=$_POST['IDF'];
    $password=$_POST['password'];
    $MM_fldUserAuthorization = "clase";
    $MM_redirectLoginSuccess = "opp/main_menu.php";
    $MM_redirectLoginFailed = "?OPP";
    $MM_redirecttoReferrer = false;
    mysql_select_db($database_dspp, $dspp);
      
    $LoginRS__query=sprintf("SELECT idopp, idf, password, nombre AS 'nombreOPP' FROM opp WHERE idf=%s AND password=%s",
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
      $_SESSION["nombre"] = $loginStrGroup['nombre'];
      $_SESSION["nombreOPP"] = $loginStrGroup['nombreOPP'];
      $_SESSION["idopp"] = $loginStrGroup['idopp'];
      $_SESSION["idf"] = $loginStrGroup['idf'];    
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

if (isset($_POST['IDF_COM'])) {

    $loginUsername=$_POST['IDF_COM'];
    $password=$_POST['password'];
    $MM_fldUserAuthorization = "clase";
    $MM_redirectLoginSuccess = "com/main_menu.php";
    $MM_redirectLoginFailed = "?COM";
    $MM_redirecttoReferrer = false;
    mysql_select_db($database_dspp, $dspp);
      
    $LoginRS__query=sprintf("SELECT idcom, idf, password, nombre FROM com WHERE idf=%s AND password=%s",
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
      $_SESSION["nombreCOM"] = $loginStrGroup['nombre'];
      $_SESSION["idcom"] = $loginStrGroup['idcom'];
      $_SESSION["idf"] = $loginStrGroup['idf'];    
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

if (isset($_POST['IDF_OC'])) {
  $loginUsername=$_POST['IDF_OC'];
  $password=$_POST['password'];
  $MM_fldUserAuthorization = "clase";
  $MM_redirectLoginSuccess = "oc/main_menu.php";
  $MM_redirectLoginFailed = "?OC";
  $MM_redirecttoReferrer = false;
  mysql_select_db($database_dspp, $dspp);
    
  $LoginRS__query=sprintf("SELECT idoc, idf, password, nombre FROM oc WHERE idf=%s AND password=%s",
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
    $_SESSION["nombre"] = $loginStrGroup['nombre'];
    $_SESSION["nombreOC"] = $loginStrGroup['nombre'];
    $_SESSION["idoc"] = $loginStrGroup['idoc'];
    $_SESSION["idf"] = $loginStrGroup['idf'];    
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
<html lang="en">
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
<li role="presentation" <? if(isset($_GET['OPP'])){?> class="active" <? }?>><a href="?OPP">OPP</a></li>
<li role="presentation" <? if(isset($_GET['OC'])){?> class="active" <? }?>><a href="?OC">OC</a></li>
<li role="presentation" <? if(isset($_GET['COM'])){?> class="active" <? }?>><a href="?COM">COM</a></li>
<li role="presentation" <? if(isset($_GET['ADM'])){?> class="active" <? }?>><a href="?ADM">ADM</a></li>
<li role="presentation" <? if(isset($_GET['RECURSOS'])){?> class="active" <? }?>><a href="#">RESOURCES</a></li>

</ul>

<hr>

<div class="col-sm-8">

  <!-------------------------------------------- INICIO INICIO DE SESION OPP ------------------------------------------------------------------------>

  <?php if(isset($_GET['registroOPP'])){ 

    mysql_select_db($database_dspp, $dspp);
    $query_pais = "SELECT nombre FROM paises ORDER BY nombre ASC";
    $pais = mysql_query($query_pais, $dspp) or die(mysql_error());
    $row_pais = mysql_fetch_assoc($pais);
    $totalRows_pais = mysql_num_rows($pais);


    $query_oc = "SELECT idoc, idf, abreviacion, pais FROM oc ORDER BY nombre ASC";
    $oc = mysql_query($query_oc, $dspp) or die(mysql_error());
    $row_oc = mysql_fetch_assoc($oc);
    $totalRows_oc = mysql_num_rows($oc);
    ?>
<!-------------------------------------------- INICIO FORMULARIO DE REGISTRO OPP  ------------------------------------------------------------------------>

  <div>
    <div class="panel panel-primary">
      <div class="panel-heading">
        <h3 class="panel-title text-center">NEW REGISTER</h3>
      </div>
      <div class="panel-body">

        <form class="" method="post" name="registroOpp" action="<?php echo $loginFormAction; ?>">
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
      <th colspan="2" class="alert alert-warning">The #SPP and password are provided by D-SPP, these data are sent by email to OPP</th>
    </tr>
    <tr valign="baseline">
      <th colspan="1" class="alert alert-info">* Required Information</th>
    </tr>

            <tr valign="baseline">
              <th nowrap align="left">IDF or #SPP <br>(if you have one)</th>
              <td><input class="form-control" type="text" id="idf" name="idf" value="" size="32"></td>
            </tr>
            <tr valign="baseline">
              <th nowrap align="left">* Name OPP</th>
              <td><input class="form-control" type="text" id="nombre" name="nombre" value="" size="32" required></td>
            </tr>
            <tr valign="baseline">
              <th nowrap align="left">Abbreviation OPP</th>
              <td><input class="form-control" type="text" id="abreviacion" name="abreviacion" value="" size="32" ></td>
            </tr>
            <tr valign="baseline">
              <th nowrap align="left">Web Site</th>
              <td><input class="form-control" type="text" id="sitio_web" name="sitio_web" value="" size="32" ></td>
            </tr>
            <tr valign="baseline">
              <th nowrap align="left">* Email Contact</th>
              <td><input class="form-control" type="email" id="email" name="email" value="" size="32" required></td>
            </tr>
            <tr valign="baseline">
              <th nowrap align="left">* Country</th>
              <td><select  class="form-control" id="pais" name="pais" required>
              <option value="">Select One</option>
        <?php 
        do {  
        ?>
        <option class="form-control" value="<?php echo utf8_encode($row_pais['nombre']);?>" ><?php echo utf8_encode($row_pais['nombre']);?></option>
        <?php
        } while ($row_pais = mysql_fetch_assoc($pais));
        ?>
              </select></td>
            <tr>

            <tr valign="baseline">
              <th nowrap align="left">Business name</th>
              <td><input class="form-control" type="text" id="razon_socila" name="razon_social" value="" size="32"></td>
            </tr>
            <tr valign="baseline">
              <th nowrap align="left">Fiscal address</th>
              <td><input class="form-control" type="text" id="direccion_fiscal" name="direccion_fiscal" value="" size="32" ></td>
            </tr>
            <tr valign="baseline">
              <th nowrap align="left">RFC</th>
              <td><input class="form-control" type="text" id="rfc" name="rfc" value="" size="32"></td>
            </tr>
            <tr valign="baseline">
              <td nowrap align="right">&nbsp;</td>
              <td>
                <!--<input name="new_btn" id="new_btn" class="btn btn-primary" type="submit" value="Agregar OPP">-->

              <button class="btn btn-success" id="registrarse" name="registrarse" type="submit" value="Enviar" aria-label="Left Align">
                <span class="glyphicon glyphicon-open-file" aria-hidden="true"></span>  Sign in
              </button>

              </td>
            </tr>
          </table>
          <input type="hidden" id="fecha_inclusion" name="fecha_inclusion" value="<?php echo time()?>">
          <input type="hidden" name="idfoc" value="<?php echo $_GET['IDFOC']; ?>">
          <input type="hidden" name="MM_insert" value="form1">
        </form>
      </div>
    </div>
  </div> 
<!-------------------------------------------- FIN FORMULARIO DE REGISTRO OPP  ------------------------------------------------------------------------>

  <?php }else if(isset($_GET['registroCOM'])){ 
 
    mysql_select_db($database_dspp, $dspp);
    $query_pais = "SELECT nombre FROM paises ORDER BY nombre ASC";
    $pais = mysql_query($query_pais, $dspp) or die(mysql_error());
    $row_pais = mysql_fetch_assoc($pais);
    $totalRows_pais = mysql_num_rows($pais);


    $query_oc = "SELECT idoc, idf, abreviacion, pais FROM oc ORDER BY nombre ASC";
    $oc = mysql_query($query_oc, $dspp) or die(mysql_error());
    $row_oc = mysql_fetch_assoc($oc);
    $totalRows_oc = mysql_num_rows($oc);

    ?>

<!-------------------------------------------- INICIO FORMULARIO DE REGISTRO COM  ------------------------------------------------------------------------>

  <div>
    <div class="panel panel-primary">
      <div class="panel-heading">
        <h3 class="panel-title text-center">NEW REGISTER</h3>
      </div>
      <div class="panel-body">

        <form class="" method="post" name="registroOpp" action="<?php echo $loginFormAction; ?>">
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
      <th colspan="2" class="alert alert-warning">The #SPP and password are provided by D-SPP, these data are sent by email to OPP</th>
    </tr>
    
    <tr valign="baseline">
      <th colspan="1" class="alert alert-info">* Required Information</th>
    </tr>

            <tr valign="baseline">
              <th nowrap align="left">IDF or #SPP <br>(if you have one)</th>
              <td><input class="form-control" type="text" id="idf" name="idf" value="" size="32"></td>
            </tr>
            <tr valign="baseline">
              <th nowrap align="left">* Company Name</th>
              <td><input class="form-control" type="text" id="nombre" name="nombre" value="" size="32" required></td>
            </tr>
            <tr valign="baseline">
              <th nowrap align="left">Abbreviation</th>
              <td><input class="form-control" type="text" id="abreviacion" name="abreviacion" value="" size="32"></td>
            </tr>
            <tr valign="baseline">
              <th nowrap align="left">Web Site</th>
              <td><input class="form-control" type="text" id="sitio_web" name="sitio_web" value="" size="32" ></td>
            </tr>
            <tr valign="baseline">
              <th nowrap align="left">* Email Contact</th>
              <td><input class="form-control" type="email" id="email" name="email" value="" size="32" required></td>
            </tr>
            <tr valign="baseline">
              <th nowrap align="left">Phone (COUNTRY CODE + AREA CODE + NUMBER)</th>
              <td><input class="form-control" type="text" id="telefono" name="telefono" value="" size="32"></td>
            </tr>
            <tr valign="baseline">
              <th nowrap align="left">* Country</th>
              <td><select  class="form-control" id="pais" name="pais" required>
              <option value="">Select One</option>
        <?php 
        do {  
        ?>
        <option class="form-control" value="<?php echo utf8_encode($row_pais['nombre']);?>" ><?php echo utf8_encode($row_pais['nombre']);?></option>
        <?php
        } while ($row_pais = mysql_fetch_assoc($pais));
        ?>
              </select></td>
            <tr>
            <tr valign="baseline">
              <th nowrap align="left">City</th>
              <td><input class="form-control" type="text" id="ciudad" name="ciudad" value="" size="32"></td>
            </tr>


            <tr valign="baseline">
              <th nowrap align="left">Company address</th>
              <td><input class="form-control" type="text" id="direccion" name="direccion" value="" size="32"></td>
            </tr>
            <tr>
              <td colspan="2" class="alert alert-warning text-center">Fiscal data (Optional)</td>
            </tr>
            <tr valign="baseline">
              <th nowrap align="left">Fiscal address</th>
              <td><input class="form-control" type="text" id="direccion_fiscal" name="direccion_fiscal" value="" size="32"></td>
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
                <!--<input name="new_btn" id="new_btn" class="btn btn-primary" type="submit" value="Agregar OPP">-->

              <button class="btn btn-success" id="registrarse" name="registrarse" type="submit" value="Enviar" aria-label="Left Align">
                <span class="glyphicon glyphicon-open-file" aria-hidden="true"></span> Sign in
              </button>

              </td>
            </tr>
          </table>
          <input type="hidden" id="fecha_inclusion" name="fecha_inclusion" value="<?php echo time()?>">
          <input type="hidden" name="idfoc" value="<?php echo $_GET['IDFOC']; ?>">
          <input type="hidden" name="MM_insert" value="form2">
        </form>
      </div>
    </div>
  </div> 
<!-------------------------------------------- FIN FORMULARIO DE REGISTRO COM  ------------------------------------------------------------------------>


  <?php }else{ ?>
    <? if(isset($_GET['mensaje'])){?>
      <p>
      <div class="alert alert-success" role="alert"><strong>Data recorded correctly, your #IDF and password have been sent to the email that I provide, such data are necessary to log on, </strong> if you do not receive any email, please go to the spam box.</div>
      </p>
    <? }?>
  

  <div class="col-lg-12 col-md-6" align="center">
    <img src="img/FUNDEPPO.jpg" class="text-center img-responsive" alt="FUNDEPPO">
  </div>
  <div class="col-lg-12 col-md-6">
    <div class="panel panel-success">
      <div class="panel-heading">
        <h3 class="panel-title">WHAT IS THE SPP?</h3>
      </div>
      <div class="panel-body text-justify">
        <p>
          The Small Producers’ Symbol, SPP, is a label that represents an alliance among organized small producers to build a local and global market that values the identity and the economic, social, cultural and ecological contributions of products from Small Producers’ Organizations. This alliance is based on a relationship of collaboration, trust and co-responsibility among women and men who are small producers, with buyers and consumers. The SPP is backed by an independent certification system.
          The SPP represents the identity of organized small fair trade producers, to distinguish us in local and global markets with our products and values.
        </p>
        <p>
          The SPP is backed by an independent certification system, guaranteeing consumers that products come from authentic, democratic, self-managing organizations of small producers, and that they have been produced in line with criteria for economic, social, cultural and ecological sustainability, and commercialized under fair conditions.
        </p>


        <!--El Símbolo de Pequeños Productores es una iniciativa lanzada en el año 2006 por la CLAC (Coordinadora Latinoamericana y del Caribe de Pequeños Productores de Comercio Justo) con el apoyo del movimiento de Comercio Justo y Economía Solidaria de varios continentes. Para garantizar el adecuado uso de este Símbolo, las organizaciones de pequeños productores crearon la FUNDEPPO (Fundación de Pequeños Productores Organizados), la cual permite asegurar que este Símbolo realmente beneficie a los pequeños productores, las comunidades y los consumidores. FUNDEPPO trabaja con organismos y profesionales calificados para certificar de manera independiente y confiable el cumplimiento de las normas del Símbolo.-->
        <hr>
        <a class="btn btn-success" href="http://spp.coop/" target="_blank" role="button">Web Site</a>
      </div>
    </div>
  </div>
  <div class="col-lg-12 col-md-12">
    <p class="alert alert-warning">To have better performance within D-SPP.ORG we recommend using the following browsers: <a href="https://www.google.com.mx/chrome/browser/desktop/" target="_new">Google Chrome</a>, <a href="https://www.mozilla.org/es-MX/firefox/new/" target="_new">Mozilla Firefox</a>, to click on the name in case of not having it.</p>
  </div>
  <?php } ?>
</div>




<div class="col-sm-4">

<? if(isset($_GET['OPP'])){?>
<!-------------------------------------------- INICIO INICIO DE SESION OPP ------------------------------------------------------------------------>

<div class="col-xs-10 alert alert-info">
    <div>
      <h3 class="well">login OPP</h3>
    </div>
    <div class="panel-body">
      <form ACTION="<?php echo $loginFormAction; ?>" METHOD="POST" class="form-signin" id="opp">
       
        <input type="text" id="inputIDF" name="IDF" class="form-control" placeholder="#SPP" required autofocus>
        <br>
        <input type="password" id="inputPassword" class="form-control" name="password" placeholder="Password" required>
        <br>
        <a href="#">Did you forget your password?</a>
        <br>
        <button class="btn btn-primary btn-block" type="submit" style="border-radius:0px;">Log in</button>
        <a class="btn btn-danger btn-block" style="border-radius:0px;" type="submit" name="registrarse" <?php if(isset($_GET['IDFOC'])){echo "href='?registro&IDFOC=$_GET[IDFOC]'";}else{ echo "href='?registroOPP'";} ?>>Sign in</a>
      </form>
    </div>

    <!--<form ACTION="<?php echo $loginFormAction; ?>" METHOD="POST" class="form-signin" id="adm">
      <h2 class="form-signin-heading">Datos de ingreso</h2>
      <label for="inputEmail" class="sr-only">Email address</label>
      <input type="text" id="inputIDF" name="username" class="form-control" placeholder="Username" required autofocus>
      <label for="inputPassword" class="sr-only">Password</label>
      <input type="password" id="inputPassword" class="form-control" name="password" placeholder="Password" required>
      <button class="btn btn-lg btn-primary btn-block" type="submit">Log in</button>
    </form>-->

</div>
<!-------------------------------------------- FIN INICIO DE SESION OPP ------------------------------------------------------------------------>

<? } else if(isset($_GET['OC'])){?>
<!-------------------------------------------- INICIO INICIO DE SESION OC ------------------------------------------------------------------------>

  <div class="col-xs-10 alert alert-success">
      <div>
        <h3 class="well">login OC</h3>
      </div>
      <div class="panel-body">
        <form ACTION="<?php echo $loginFormAction; ?>" METHOD="POST" class="form-signin" id="oc">
         
          <input type="text" id="inputIDF" name="IDF_OC" class="form-control" placeholder="#SPP" required autofocus>
          <br>
          <input type="password" id="inputPassword" class="form-control" name="password" placeholder="Password" required>
          <br>
          <a href="#">Did you forget your password?</a>
          <br>
          <button class="btn btn-primary btn-block" style="border-radius:0px;" type="submit">Log in</button>

        </form>
      </div>
  <!--
    <form class="form-signin" id="oc">
      <h2 class="form-signin-heading">Ingreso OC</h2>
      <label for="inputEmail" class="sr-only">Email address</label>
      <input type="text" id="inputIDF" name="IDF" class="form-control" placeholder="IDF" required autofocus>
      <label for="inputPassword" class="sr-only">Password</label>
      <input type="password" id="inputPassword" class="form-control" name="password" placeholder="Password" required>
      <button class="btn btn-lg btn-primary btn-block" type="submit">Log in</button>
    </form>
  -->



  </div>
<!-------------------------------------------- FIN INICIO DE SESION OC ------------------------------------------------------------------------>


<? }else if(isset($_GET['COM'])){?>
<!-------------------------------------------- INICIO INICIO DE SESION COM ------------------------------------------------------------------------>

<div class="col-xs-10 alert alert-warning"  >
      <div >
        <h3 class="well">login COM</h3>
      </div>
      <div class="panel-body">
        <form ACTION="<?php echo $loginFormAction; ?>" METHOD="POST" class="form-signin" id="com">
          <input type="text" id="inputIDF" name="IDF_COM" class="form-control" placeholder="#SPP" required autofocus>
          <br>
          <input type="password" id="inputPassword" class="form-control" name="password" placeholder="Password" required>
          <br>
          <a href="#">Did you forget your password?</a>
          <br>
          <button class="btn btn-primary btn-block" style="border-radius:0px;" type="submit">Log in</button>
          <a class="btn btn-danger btn-block" style="border-radius:0px;" type="submit" name="registrarse" <?php if(isset($_GET['IDFOC'])){echo "href='?registro&IDFOC=$_GET[IDFOC]'";}else{ echo "href='?registroCOM'";} ?>>Sign in</a>

        </form>
      </div>
    

 <!-- <form class="form-signin" id="com">
    <h2 class="form-signin-heading">Ingreso COM</h2>

    <input type="text" id="inputIDF" name="IDF" class="form-control" placeholder="IDF" required autofocus>
    <label for="inputPassword" class="sr-only">Password</label>
    <input type="password" id="inputPassword" class="form-control" name="password" placeholder="Password" required>
    <button class="btn btn-lg btn-primary btn-block" type="submit">Log in</button>
  </form>-->



</div>
<!-------------------------------------------- FIN INICIO DE SESION COM ------------------------------------------------------------------------>

<? }else if(isset($_GET['ADM']) or isset($_GET['adm'])){?>
  <div class="col-xs-10 alert alert-success"  >
    <div class="well well-lg">
      <h3>FUNDEPPO</h3>
    </div>
    <form ACTION="<?php echo $loginFormAction; ?>" METHOD="POST" class="form-signin" id="adm">
      <!--<h2 class="form-signin-heading">Datos de ingreso</h2>-->
      <label for="inputEmail" class="sr-only">Email address</label>
      <input type="text" id="inputIDF" name="username" class="form-control" placeholder="Username" required autofocus>
      <label for="inputPassword" class="sr-only">Password</label>
      <input type="password" id="inputPassword" class="form-control" name="password" placeholder="Password" required>
      <button class="btn btn-lg btn-primary btn-block" type="submit">Log in</button>
    </form>
  </div>
<? }else if(isset($_GET['RECURSOS']) or isset($_GET['RECURSOS'])){?>
    <a href="../archivos/recursos/MODULO-OC-D-SPP_marzo-2016.pdf">MODULE OC</a>
<?php }else{ ?>

  <div class="col-xs-10">
    <div class="panel panel-primary">
      <div class="panel-heading">
        <h3 class="panel-title">Login</h3>
      </div>
      <div class="panel-body">
        <p>Select a user type</p>
        <ul class="nav nav-pills">
        <li role="presentation" <? if(isset($_GET['OPP'])){?> class="active" <? }?>><a href="?OPP">OPP</a></li>
        <li role="presentation" <? if(isset($_GET['OC'])){?> class="active" <? }?>><a href="?OC">OC</a></li>
        <li role="presentation" <? if(isset($_GET['COM'])){?> class="active" <? }?>><a href="?COM">COM</a></li>
        </ul>
      </div>
    </div>
  </div>

<? }?>








</div>
    </div> <!-- /container -->


    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->

    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <!--<script src="js/bootstrap.min.js"></script>-->
       

  </body>
</html>