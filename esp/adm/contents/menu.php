<?php
require_once('../Connections/dspp.php');

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

<ul class="nav nav-sidebar">
  <li <?php if(isset($_GET['CRM'])){echo "class='active'"; } ?>>
    <a href="?CRM&inicio">CRM</a>
  <li <?php if(isset($_GET['ESTADISTICAS'])){echo "class='active'"; } ?>>
    <a href="?ESTADISTICAS&select">Concentrado Procesos</a>
    <!--<a href="?ESTADISTICAS&select">Estadisticas</a>-->
  </li>
  <li <?php if(isset($_GET['DOCUMENTACION'])){echo "class='active'"; } ?>>
    <a href="?DOCUMENTACION&select">Documentación</a>
  </li>
  <li <?php if(isset($_GET['CORREO'])){echo "class='active'"; } ?>>
    <a href="?CORREO&select">Lista de Contactos</a>
  </li>
  <li <?php if(isset($_GET['SOLICITUD'])){echo "class='active'"; } ?>>
    <a href="?SOLICITUD&select">Solicitudes</a>
  </li>
  <li <?php if(isset($_GET['OPP'])){echo "class='active'"; } ?>>
    <a href="?OPP&select">Información OPP</a>
  </li>
  <li <?php if(isset($_GET['OC'])){echo "class='active'"; } ?>>
    <a href="?OC&select">Información OC</a>
  </li>
  <li <?php if(isset($_GET['EMPRESAS'])){echo "class='active'"; } ?>>
    <a href="?EMPRESAS&select">Información Empresas</a>
  </li>
  <li>
    <a href="#">----</a>
  </li>
  <li>
    <a href="<?php echo $logoutAction ?>">Cerrar Sesión</a>
  </li>
</ul>


