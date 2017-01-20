<?php 
function clase_boton($valor1, $valor2)
{	
	$valor1 = $_GET[$valor1];
	if($valor1 == $valor2){
		$valor = 'class="btn btn-sm btn-primary"';
	}else{
		$valor = 'class="btn btn-sm btn-default"';
	}
    echo $valor;
}
 ?>