<?php 
require_once('../../Connections/dspp.php'); 
mysql_select_db($database_dspp, $dspp);

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

$currentPage = $_SERVER["PHP_SELF"];

?>


<ul class="nav nav-sidebar">
  <li <?php if(isset($_GET['SOLICITUD'])){ echo "class='active'"; } ?>>
    <a href="?SOLICITUD&select">Solicitudes</a>
  </li>
  <li <?php if(isset($_GET['OPP'])){ echo "class='active'"; } ?>>
    <a href="?OPP&select">Información OPP</a>
  </li>
  <li <?php if(isset($_GET['EMPRESAS'])){ echo "class='active'"; } ?>>
    <a href="?EMPRESAS&select">Información Empresas</a>
  </li>
  <li <?php if(isset($_GET['OC'])){ echo "class='active'"; } ?>>
    <a href="?OC&detail">Información OC</a>
  </li>  
  <li>
    <a href="#">---</a>
  </li>

  <li><a href="<?php echo $logoutAction ?>">Cerrar Sesión</a></li>

</ul>
