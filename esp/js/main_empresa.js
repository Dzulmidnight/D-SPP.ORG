$(buscar_datos_empresas());

function buscar_datos_empresas(consulta){
	$.ajax({
		url: 'buscar_empresas.php',
		type: 'POST',
		dataType: 'html',
		data: {consulta: consulta},
	})
	.done(function(respuesta) {
		$("#datos_empresas").html(respuesta);
	})
	.fail(function() {
		console.log("error");
	})
}

$(document).on('keyup', '#caja_busqueda_empresas', function(){
	var valor = $(this).val();
	if(valor != ""){
		buscar_datos_empresas(valor);
	}else{
		buscar_datos_empresas();
	}
});