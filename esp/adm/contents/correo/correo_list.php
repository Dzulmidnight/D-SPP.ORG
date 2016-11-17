<?php 
require_once('../Connections/dspp.php'); 
require_once('../Connections/mail.php');

//error_reporting(E_ALL ^ E_DEPRECATED);
mysql_select_db($database_dspp, $dspp);

if (!isset($_SESSION)) {
  session_start();
  
  $redireccion = "../index.php?ADM";

  if(!$_SESSION["autentificado"]){
    header("Location:".$redireccion);
  }
}
if (!function_exists("GetSQLValueString")) {
  function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
  {
    if (PHP_VERSION < 6) {
      $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
    }

    $theValue = function_exissts("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

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
<div class="row">
  <!---- INICIA MENU LISTA DE CORREOS ---->
  <div class="col-md-3">
    <h4>Lista Correos D-SPP</h4>
    <div class="list-group">
      <a href="#" class="list-group-item disabled">
        ¿Correos que desea consultar?
      </a>
      <div class="checkbox list-group-item">
        <label>
          <input type="checkbox" value="">
          Todos
        </label>
      </div>
      <div class="checkbox list-group-item">
        <label>
          <input type="checkbox" value="">
          tr
        </label>
      </div>

    </div>

  </div>
  <!---- TERMINA MENU LISTA DE CORREOS ---->

  <!---- INICIA SECCIÓN LISTADO DE CORREOS ---->
  <div class="col-md-9">
    <?php 
    $row_correos = "SELECT * FROM "
     ?>
  </div>
  <!---- INICIA SECCIÓN LISTADO DE CORREOS ---->


</div>