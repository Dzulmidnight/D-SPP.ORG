<?
if(isset($_GET['SOLICITUD'])){include ("contents/solicitud/solicitud.php");}
else
if(isset($_GET['OPP'])){include ("contents/opp/opp.php");}
else
if(isset($_GET['OC'])){include ("contents/oc/oc.php");}
else
if(isset($_GET['COM'])){include ("contents/com/com.php");}
else{include("contents/main.php");}
?>