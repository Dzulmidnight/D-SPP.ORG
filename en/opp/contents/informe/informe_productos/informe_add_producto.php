<?php 
require_once('../Connections/dspp.php'); 
require_once('../Connections/mail.php');

mysql_select_db($database_dspp, $dspp);

if (!isset($_SESSION)) {
  session_start();
	
	$redireccion = "../index.php?EMPRESA";

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

	  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

$idempresa = $_SESSION['idempresa'];
$idtrim = $_GET['idtrim'];
$anio_actual = date('Y',time());
$row_configuracion = mysql_query("SELECT * FROM porcentaje_ajuste WHERE anio = $anio_actual", $dspp) or die(mysql_error());
$configuracion = mysql_fetch_assoc($row_configuracion);

if(isset($_POST['agregar_formato']) && $_POST['agregar_formato'] == 1){
	
	$fecha_registro = time();
	$tipo_moneda = 'USD';


		if(isset($_POST['pais'])){
			$pais = $_POST['pais'];
		}else{
			$pais = NULL;
		}
		if(isset($_POST['ventas_totales'])){
			$ventas_totales = $_POST['ventas_totales'];
		}else{
			$ventas_totales = NULL;
		}
		if(isset($_POST['tipo_moneda'])){
			$tipo_moneda = $_POST['tipo_moneda'];
		}else{
			$tipo_moneda = NULL;
		}

		//Iniciamos insertar formato_producto_empresa
			$insertSQL = sprintf("INSERT INTO formato_producto_empresa(idtrim, idempresa, pais, ventas_totales, tipo_moneda, fecha_registro) VALUES (%s, %s, %s, %s, %s, %s)",
				GetSQLValueString($idtrim, "text"),
				GetSQLValueString($idempresa, "int"),
				GetSQLValueString($pais, "text"),
				GetSQLValueString($ventas_totales, "text"),
				GetSQLValueString($tipo_moneda, "text"),
				GetSQLValueString($fecha_registro, "int"));
			$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
			//$idformato_producto_empresa = mysql_insert_id($dspp);
		//Termina insertar formato compras
}
if(isset($_POST['eliminar_registro']) && $_POST['eliminar_registro'] != 0){
	$idregistro = $_POST['eliminar_registro'];

	$deleteSQL = sprintf("DELETE FROM formato_producto_empresa WHERE idformato_producto_empresa = $idregistro",
		GetSQLValueString($idregistro, "int"));
	$eliminar = mysql_query($deleteSQL, $dspp) or die(mysql_error());
}

?>

<div class="row">
	<div class="col-lg-12">
 		<?php 
 		if(isset($mensaje)){
 		?>
			<div class="alert alert-success alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<?php echo $mensaje; ?>
			</div>
		<?php
 		}
 		 ?>
	</div>
	<?php 
	$txt_trim = 'trim'.$_GET['trim'].'_producto';
	$txt_idtrim = 'idtrim'.$_GET['trim'].'_producto';
	$txt_estatus = 'estado_trim'.$_GET['trim'];
	$row_trim = mysql_query("SELECT * FROM $txt_trim WHERE $txt_idtrim = '$idtrim'", $dspp) or die(mysql_error());
	$trim = mysql_fetch_assoc($row_trim);
	?>
	<div class="col-md-12">
	<?php
	if($trim[$txt_estatus] == 'FINALIZADO'){
		echo "<p class='alert alert-danger'><span class='glyphicon glyphicon-ban-circle' aria-hidden='true'></span> Ya no se puede agregar más registro al <b>Formato Trimestral $idtrim</b>, ya que fue concluido.</p>";
	}else{
	?>


		<!--<p class="alert alert-info" style="padding:7px;margin-bottom:0px;"><strong>Agregar Registro al Trimestre <?php echo $idtrim; ?></strong></p>-->
		<!--<p class="alert alert-info" style="margin-bottom:0px;padding:5px;"><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> Los campos marcados en color azul son opcionales, dicha informacion será de utilitdad para la evaluación de la certificación.</p>
		<p class="alert alert-success" style="padding:5px;"><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> Los campos marcados en color verde son obligatorios.</p>-->
	

			<p class="alert alert-danger" style="margin-bottom:0px;padding:5px;"><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> El valor de ventas totales debe ser expresado en dolares americanos(USD)</p>
		 	<table class="table table-bordered table-condensed" style="font-size:11px;" id="tablaInforme">
		 		<thead>
		 			<tr class="success">
		 				<th class="text-center">
							#
		 				</th>
						<th class="text-center">País destino del producto terminado</th>
						<th class="text-center">Valor de ventas totales SPP</th>
						<th class="text-center">Tipo moneda</th>
		 			</tr>
		 		</thead>
		 		<tbody>

		 				<?php 
		 					switch ($_GET['trim']) {
		 						case '1':
		 							$num_trim = 'trim1_producto';
		 							break;
		 						case '2':
		 							$num_trim = 'trim2_producto';
		 							break;
		 						case '3':
		 							$num_trim = 'trim3_producto';
		 							break;
		 						case '4':
		 							$num_trim = 'trim4_producto';
		 							break;

		 						
		 						default:
		 							# code...
		 							break;
		 					}
							$row_registro = mysql_query("SELECT formato_producto_empresa.* FROM formato_producto_empresa WHERE formato_producto_empresa.idtrim  = '$informe_general_producto[$num_trim]'");
							$contador = 1;
							while($formato = mysql_fetch_assoc($row_registro)){
							?>
								<form action="" method="POST">
									<tr class="active">
										<td>
											<?php echo $contador; ?> <button type="submit" class="btn btn-sm btn-danger" data-toggle="tooltip" title="Eliminar registro" name="eliminar_registro" value="<?php echo $formato['idformato_producto_empresa']; ?>" onclick="return confirm('¿Está seguro de eliminar el registro?');"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
										</td>
										<td><?php echo $formato['pais']; ?></td>
										<td><?php echo $formato['ventas_totales']; ?></td>
										<td><?php echo $formato['tipo_moneda']; ?></td>
									</tr>
								</form>
							<?php
							$contador++;
							}
		 				 ?>
		<form class="form-horizontal" method="POST">
					<tr class="success">
						<td class="warning"></td> <!-- # -->

						<td class="warning"><!-- pais de la opp proveedora -->
			              <select name="pais" id="pais" class="form-control" required>
			                <option value="">País destino</option>
			                <?php 
			                $row_pais = mysql_query("SELECT * FROM paises", $dspp) or die(mysql_error());
			                while($pais = mysql_fetch_assoc($row_pais)){
			                  echo "<option value='".utf8_encode($pais['nombre'])."'>".utf8_encode($pais['nombre'])."</option>";
			                }
			                 ?>
			              </select>
						</td>
						<td>
							<input name="ventas_totales" type="number" step="any" class="form-control" placeholder="VENTAS TOTALES" required>
						</td>
						<td>
							<input name="tipo_moneda" type="text" class="form-control" value="USD" readonly>
						</td>
					</tr>
		 			<tr>
		 				<td colspan="6"><button class="btn btn-primary" type="submit" style="width:100%" name="agregar_formato" value="1">Guardar Registro</button></td>
		 			</tr>
		 		</tbody>
		 	</table>
		</form>
	<?php
	}
	?>	
	</div>
</div>

<script>
/*$(document).ready(function() {
//    $("#resultadoBusqueda").val('<p>CAMPO VACIO</p>');
    $("#nombre_opp").val('Nombre de la OPP');
//    $("#resultadoBusqueda").val('<p>CAMPO VACIO</p>');
    $("#pais").val('Pais de la OPP');
});

function buscar() {
    var textoBusqueda = $("input#spp").val();
 
     if (textoBusqueda != "") {
        $.post("../../nombre_ajax.php", {valorBusqueda: textoBusqueda}, function(nombre_opp) {
            $("#nombre_opp").val(nombre_opp);
         }); 
     } else { 
        $("#nombre_opp").val('Nombre de la OPP');
     };

     if (textoBusqueda != "") {
        $.post("../../pais_ajax.php", {valorBusqueda: textoBusqueda}, function(nombre_pais) {
            $("#pais").val(nombre_pais);
         }); 
     } else { 
        $("#pais").val('País de la OPP');
     };

};
</script>

<script>
/*function ponerMayusculas(nombre) 
{ 
nombre.value=nombre.value.toUpperCase(); 
} 
var contador=0;
var cuota_fija_anual = <?php echo $configuracion['cuota_compradores']; ?>;
//var cuota_fija_anual = 0.01;
	function calcular(){
		cantidad_total_factura = document.getElementById("cantidad_total_factura").value;
		precio_total_unitario = document.getElementById("precio_total_unitario").value;

		//calculamos el valor total contrato
		valor_total_contrato = parseFloat(cantidad_total_factura) * parseFloat(precio_total_unitario);
		total_contrato_redondeado = parseFloat(valor_total_contrato.toFixed(2));
		//calculamos el valor de la cuota de uso reglamento
		//cuota_uso_reglamento = valor_total_contrato * cuota_fija_anual;
		//calculamos el total a pagar
		total_a_pagar = (valor_total_contrato * cuota_fija_anual) / 100;

		total_redondeado = parseFloat(total_a_pagar.toFixed(2));

		//calculamos el valor total del contrato

		//21_07_2017 valor_total_contrato_redondeado = parseFloat(valor_total_contrato.toFixed(2));
		/* se redondea el resultado a 2 decimales */
		//valor_total_contrato = parseFloat(Math.round((precio_total_unitario * peso_cantidad_total_contrato) * 100) / 100).toFixed(2);
		//document.getElementById("valor_total_contrato").value = total_contrato_redondeado; 
		//document.getElementById("cuota_uso_reglamento").value = "<?php echo $configuracion['cuota_compradores']; ?> %"; 
		//document.getElementById("total_a_pagar").value = total_redondeado; 

		//calculamos el total a pagar
		/*if(isNaN(cuota_uso_reglamento)){ // revisamos si es porcentaje
			//alert("ES PORCENTAJE : "+cuota_uso_reglamento);
			total_final = parseFloat(valor_total_contrato_redondeado) * (0.01);
		}else{	//si es solo numero
			//alert("ES NUMERO: "+cuota_uso_reglamento);
			total_final = parseFloat(peso_cantidad_total_contrato) * parseFloat(cuota_uso_reglamento);
		}*/
	//}


</script>