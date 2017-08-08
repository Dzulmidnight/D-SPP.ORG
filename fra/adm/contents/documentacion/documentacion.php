<?php 
require_once('../Connections/dspp.php'); 
require_once('../Connections/mail.php'); 
mysql_select_db($database_dspp, $dspp);

include('documentacion_add.php'); 
//include('estatus_dspp.php'); 
?>