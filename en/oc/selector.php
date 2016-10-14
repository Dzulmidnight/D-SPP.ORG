<?php 
if(isset($_GET['SOLICITUD'])){
	include ("contents/solicitud/solicitud.php");
}else if(isset($_GET['OPP'])){
	include ("contents/opp/opp.php");
}else if(isset($_GET['OC'])){
	include ("contents/oc/oc.php");
}else if(isset($_GET['EMPRESAS'])){
	include ("contents/empresa/empresa.php");
}else{
	include("contents/main.php");
}
 ?>
