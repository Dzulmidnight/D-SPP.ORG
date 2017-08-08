<?php 


if(isset($_POST["formularioComprobante"]) && $_POST['formularioComprobante'] == 1){
	echo "SI ENTRO AL FORMULARIO<br>";
	if(isset($_POST['aceptar'])){
		$aceptar = $_POST['aceptar'];
		 echo "EL VALOR DE ACEPTAR ES: ".$aceptar;
	}
	if(isset($_POST['denegar'])){
		$denegar = $_POST['denegar'];
		echo "EL VALOR DE DENEGAR ES: ".$denegar;
	}
}else{
	echo "NO SE TOMO EL VALOR DEL FORMULARIO";
}
 ?>