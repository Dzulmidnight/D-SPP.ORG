<?
if(isset($_GET['ESTADISTICAS'])){include ("contents/estadisticas/estadisticas.php");}
else
if(isset($_GET['SOLICITUD'])){include ("contents/solicitud/solicitud.php");}
else
if(isset($_GET['CORREO'])){include ("contents/correo/correo.php");}
else
if(isset($_GET['ANEXOS'])){include ("contents/anexos/anexos.php");}
else
if(isset($_GET['OPP'])){include ("contents/opp/opp.php");}
else
if(isset($_GET['OC'])){include ("contents/oc/oc.php");}
else
if(isset($_GET['COM'])){include ("contents/com/com.php");}
else{include("contents/main.php");}
?>