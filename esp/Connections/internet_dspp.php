<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_dspp = "localhost";
$database_dspp = "dspporg_2";
//$database_dspp = "d-spp";
//$username_dspp = "root";
//$password_dspp = "";

$username_dspp = "dspporg_user";
$password_dspp = "s;.GU!?tJ,Hh";
$dspp = mysql_connect($hostname_dspp, $username_dspp, $password_dspp) or trigger_error(mysql_error(),E_USER_ERROR); 
?>
