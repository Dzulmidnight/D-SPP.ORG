<?
if(isset($_GET['SOLICITUD'])){include ("contents/solicitud/solicitud.php");}
else
if(isset($_GET['COM'])){include ("contents/com/com.php");}
else{include("contents/main.php");}
?>