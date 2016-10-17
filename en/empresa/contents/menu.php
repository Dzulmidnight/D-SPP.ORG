<?php
require_once('../../Connections/dspp.php');

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
  <li <? if(isset($_GET['SOLICITUD'])){?> class="active" <? }?>>
    <a href="?SOLICITUD&select">Applications</a>
  </li>
  <li <? if(isset($_GET['EMPRESA'])){?> class="active" <?}?>>
    <a href="?EMPRESA&detail">My Account</a>
  </li>
  <li <? if(isset($_GET['.'])){?> class="active" <? }?>>
    <a href="#">---</a>
  </li>
  <li><a href="<?php echo $logoutAction ?>">Logout</a></li>
</ul>