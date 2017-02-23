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

if(isset($_POST['agregar_formato']) && $_POST['agregar_formato'] == 1){
	
	$fecha_registro = time();
	$tipo_moneda = 'USD';

		if(isset($_POST['spp'])){
			$spp = $_POST['spp'];
		}else{
			$spp = NULL;
		}
		if(isset($_POST['nombre_opp'])){
			$opp = $_POST['nombre_opp'];
		}else{
			$opp = NULL;
		}
		if(isset($_POST['pais'])){
			$pais = $_POST['pais'];
		}else{
			$pais = NULL;
		}
		if(isset($_POST['fecha_facturacion'])){
			$fecha_facturacion = strtotime($_POST['fecha_facturacion']);
		}else{
			$fecha_facturacion = NULL;
		}
		if(isset($_POST['primer_intermediario'])){
			$primer_intermediario = $_POST['primer_intermediario'];
		}else{
			$primer_intermediario = NULL;
		}
		if(isset($_POST['segundo_intermediario'])){
			$segundo_intermediario = $_POST['segundo_intermediario'];
		}else{
			$segundo_intermediario = NULL;
		}
		if(isset($_POST['clave_contrato'])){
			$clave_contrato = $_POST['clave_contrato'];
		}else{
			$clave_contrato = NULL;
		}
		if(isset($_POST['fecha_contrato'])){
			$fecha_contrato = strtotime($_POST['fecha_contrato']);
		}else{
			$fecha_contrato = NULL;
		}
		if(isset($_POST['producto_general'])){
			$producto_general = $_POST['producto_general'];
		}else{
			$producto_general = NULL;
		}
		if(isset($_POST['producto_especifico'])){
			$producto_especifico = $_POST['producto_especifico'];
		}else{
			$producto_especifico = NULL;
		}

		if(isset($_POST['unidad_cantidad_total_factura'])){
			$unidad_cantidad_total_factura = $_POST['unidad_cantidad_total_factura'];
		}else{
			$unidad_cantidad_total_factura = NULL;
		}

		if(isset($_POST['cantidad_total_factura'])){
			$cantidad_total_factura = $_POST['cantidad_total_factura'];
		}else{
			$cantidad_total_factura = NULL;
		}


		if(isset($_POST['precio_sustentable_minimo'])){
			$precio_sustentable_minimo = $_POST['precio_sustentable_minimo'];
		}else{
			$precio_sustentable_minimo = NULL;
		}
		if(isset($_POST['reconocimiento_organico'])){
			$reconocimiento_organico = $_POST['reconocimiento_organico'];
		}else{
			$reconocimiento_organico = NULL;
		}
		if(isset($_POST['incentivo_spp'])){
			$incentivo_spp = $_POST['incentivo_spp'];
		}else{
			$incentivo_spp = NULL;
		}
		if(isset($_POST['otros_premios'])){
			$otros_premios = $_POST['otros_premios'];
		}else{
			$otros_premios = NULL;
		}
		if(isset($_POST['precio_total_unitario'])){
			$precio_total_unitario = $_POST['precio_total_unitario'];
		}else{
			$precio_total_unitario = NULL;
		}
		if(isset($_POST['valor_total_contrato'])){
			$valor_total_contrato = $_POST['valor_total_contrato'];
		}else{
			$valor_total_contrato = NULL;
		}
		if(isset($_POST['cuota_uso_reglamento'])){
			$cuota_uso_reglamento = $_POST['cuota_uso_reglamento'];
		}else{
			$cuota_uso_reglamento = NULL;
		}
		if(isset($_POST['total_a_pagar'])){
			$total_a_pagar = $_POST['total_a_pagar'];
		}else{
			$total_a_pagar = 0;
		}

		//Iniciamos insertar formato_compras
			$insertSQL = sprintf("INSERT INTO formato_compras(idtrim, idempresa, spp, opp, pais, fecha_facturacion, primer_intermediario, segundo_intermediario, clave_contrato, fecha_contrato, producto_general, producto_especifico, unidad_cantidad_factura, cantidad_total_factura, precio_sustentable_minimo, reconocimiento_organico, incentivo_spp, otros_premios, precio_total_unitario, valor_total_contrato, cuota_uso_reglamento, total_a_pagar, fecha_registro) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
				GetSQLValueString($idtrim, "text"),
				GetSQLValueString($idempresa, "int"),
				GetSQLValueString($spp, "text"),
				GetSQLValueString($opp, "text"),
				GetSQLValueString($pais, "text"),
				GetSQLValueString($fecha_facturacion, "int"),
				GetSQLValueString($primer_intermediario, "text"),
				GetSQLValueString($segundo_intermediario, "text"),
				GetSQLValueString($clave_contrato, "text"),
				GetSQLValueString($fecha_contrato, "text"),
				GetSQLValueString($producto_general, "text"),
				GetSQLValueString($producto_especifico, "text"),
				GetSQLValueString($unidad_cantidad_total_factura, "text"),
				GetSQLValueString($cantidad_total_factura, "text"),
				GetSQLValueString($precio_sustentable_minimo, "text"),
				GetSQLValueString($reconocimiento_organico, "text"),
				GetSQLValueString($incentivo_spp, "text"),
				GetSQLValueString($otros_premios, "text"),
				GetSQLValueString($precio_total_unitario, "text"),
				GetSQLValueString($valor_total_contrato, "text"),
				GetSQLValueString($cuota_uso_reglamento, "text"),
				GetSQLValueString($total_a_pagar, "text"),
				GetSQLValueString($fecha_registro, "int"));
			$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
			//$idformato_compras = mysql_insert_id($dspp);
		//Termina insertar formato compras
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
	$txt_trim = 'trim'.$_GET['trim'];
	$txt_idtrim = 'idtrim'.$_GET['trim'];
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
		<p class="alert alert-info" style="margin-bottom:0px;padding:5px;"><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> Los campos marcados en color azul son opcionales, dicha informacion será de utilitdad para la evaluación de la certificación.</p>
		<p class="alert alert-success" style="padding:5px;"><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> Los campos marcados en color verde son obligatorios.</p>
	
		<form class="form-horizontal" method="POST">
		 	<table class="table table-bordered table-condensed" style="font-size:11px;" id="tablaInforme">
		 		<thead>
		 			<tr class="success">
		 				<th class="text-center">
							#
		 				</th>
						<th class="text-center">#SPP</th>
						<th class="text-center">Nombre OPP proovedora</th>
						<th class="text-center">País de OPP proveedora</th>
						<th class="text-center">Fecha de Facturación</th>
						<th class="text-center">Primer Intermediario</th>
						<th class="text-center">Segundo Intermediario</th>
						<th colspan="2" class="text-center">Referencia Contrato Original con OPP</th>
						<th class="text-center">Producto General</th>
						<th class="text-center">Producto Especifico</th>
						<th colspan="2" class="text-center">Cantidad Total Conforme Factura</th>
						<th class="text-center">Precio Sustentable Mínimo</th>
						<th class="text-center">Reconocimiento Orgánico</th>
						<th class="text-center">Incentivo SPP</th>
						<th class="text-center">Otros premios</th>
						<th class="text-center">Precio Total Unitario pagado</th>
						<th class="text-center">Valor Total Contrato</th>
						<th class="text-center">Cuota de Uso Reglamento</th>
						<th class="text-center">Total a pagar</th>
		 			</tr>
		 		</thead>
		 		<tbody>
		 			<tr>
		 				<!-- INICIA NUMERO CONSECUTIVO -->
		 				<td>
		 					Número Consecutivo
		 				</td>
		 				<!-- TERMINA NUMERO CONSECUTIVO -->

		 				<!-- INICIA #SPP -->
		 				<td>
		 					Codigo de identificación SPP
		 				</td>
		 				<!-- TERMINA #SPP -->
						
						<!-- INICIA NOMBRE DE LA OPP -->
		 				<td>
		 					Nombre de la OPP de donde proviene el producto.
		 				</td>
		 				<!-- TERMINA NOMBRE DE A OPP

		 				<!-- INICIA PAIS DE LA OPP -->
		 				<td>
		 					País sede la OPP de donde proviene el productos
		 				</td>
		 				<!-- TERMINA PAIS DE LA OPP -->

		 				<!-- INICIA FECHA DE LA FACTURACION -->
		 				<td>
		 					La fecha de la compra del producto por el Comprador Final, usuario del SPP(de la OPP directamente o a través de algún intermediario).
		 				</td>
		 				<!-- TERMINA FECHA DE LA FACTURACION -->

		 				<!-- INICIA PRIMER INTERMEDIARIO -->
		 				<td>
		 					Nombre de la contraparte comercial que hace la compra del prodcuto a la OPP, si no es el Comprador Final. Si es el Comprador Final poner guión
		 				</td>
		 				<!-- TERMINA PRIMER INTERMEDIARIO -->

		 				<!-- INICIA SEGUNDO INTERMEDIARIO -->
		 				<td>
		 					Nombre de la contraparte comercial que hace la compra del producto al Primer Intermediario, si no es el Comprador Final. Si no existe o es el Comprador Final, poner guión.
		 				</td>
		 				<!-- TERMINA SEGUNDO INTERMEDIARIO -->

		 				<!-- INICIA REFERENCIA CONTRATO -->
			 				<td>
			 					Número o la clave del Contrato Original, es decir, el contrato de venta de la OPP al Comprador o al Primer Intermediario.
			 				</td>
			 				<td>
			 					Fecha del Contrato Original de venta de la OPP.
			 				</td>
		 				<!-- TERMINA REFERENCIA CONTRATO -->

		 				<!-- INICIA PRODUCTO GENERAL -->
		 				<td>
		 					Producto general. Ej: Café, Miel, Platano, Azúcar.
		 				</td>
		 				<!-- TERMINA PRODUCTO GENERAL -->

		 				<td>
		 					Producto especifico. Ej: Café verde arábica, Miel tipo A, Chips de platano, Azúcar blanco refinado.
		 				</td>
		 				<!-- INICIA CANTIDAD TOTAL CONFORME FACTURA -->
			 				<td>
			 					Unidad de medida utilizada (kg, t, lb, qq, etc)
			 				</td>
			 				<td>
			 					Cantidad total del producto en unidad de medida final indicada en el contrato original
			 				</td>
		 				<!-- TERMINA CANTIDAD TOTAL CONFORME FACTURA -->

		 				<!-- INICIA PRECIO SUSTENTABLE MINIMO -->
		 				<td>
		 					Importe pagado por unidad(USD)
		 				</td>
						<!-- TERMINA PRECIO SUSTENTABLE MINIMO -->

		 				<!-- INICIA RECONOCIMIENTO ORGANICO -->
		 				<td>
		 					Importe pagado por unidad(USD)
		 				</td>
						<!-- TERMINA RECONOCIMIENTO ORGANICO -->

		 				<!-- INICIA INCENTIVO SPP -->
		 				<td>
		 					Importe pagado por unidad(USD)
		 				</td>
						<!-- TERMINA INCENTIVO SPP -->

		 				<!-- INICIA OTROS PREMIOS -->
		 				<td>
		 					Importe pagado por unidad(USD)
		 				</td>
						<!-- TERMINA OTROS PREMIOS -->

		 				<!-- INICIA PRECIO TOTAL UNITARIO PAGADO -->
		 				<td>
		 					Importe pagado por unidad(USD)
		 				</td>
						<!-- TERMINA PRECIO TOTAL UNITARIO PAGADO -->

		 				<!-- INICIA VALOR TOTAL CONTRATO -->
		 				<td>
		 					Valor total del contrato (en divisa conforme Lista de Precios Sustentables del SPP)
		 				</td>
						<!-- TERMINA VALOR TOTAL CONTRATO -->

		 				<!-- INICIA CUOTA DE USO REGLAMENTO -->
		 				<td>
		 					Cuota vigente establecida conforme Reglamento de Costos de SPP
		 				</td>
						<!-- TERMINA CUOTA DE USO REGLAMENTO -->

		 				<!-- INICIA TOTAL A PAGAR -->
		 				<td>
		 					Total a pagar a SPP Global conforme cuota de uso y volumen de lote
		 				</td>
						<!-- TERMINA TOTAL A PAGAR -->
		 			</tr>

		 				<?php 
		 					switch ($_GET['trim']) {
		 						case '1':
		 							$num_trim = 'trim1';
		 							break;
		 						case '2':
		 							$num_trim = 'trim2';
		 							break;
		 						case '3':
		 							$num_trim = 'trim3';
		 							break;
		 						case '4':
		 							$num_trim = 'trim4';
		 							break;

		 						
		 						default:
		 							# code...
		 							break;
		 					}
							$row_registro = mysql_query("SELECT formato_compras.* FROM formato_compras WHERE formato_compras.idtrim  = '$informe_general[$num_trim]'");
							$contador = 1;

							while($formato = mysql_fetch_assoc($row_registro)){
							?>
								<tr class="active">
									<td><?php echo $contador; ?></td>
									<td><?php echo $formato['spp']; ?></td>
									<td><?php echo $formato['opp']; ?></td>
									<td><?php echo $formato['pais']; ?></td>
									<td><?php echo date('d/m/Y',$formato['fecha_facturacion']); ?></td>
									<td><?php echo $formato['primer_intermediario']; ?></td>
									<td><?php echo $formato['segundo_intermediario']; ?></td>
									<td><?php echo $formato['clave_contrato']; ?></td>
									<td><?php echo date('d/m/Y',$formato['fecha_contrato']); ?></td>
									<td><?php echo $formato['producto_general']; ?></td>
									<td><?php echo $formato['producto_especifico']; ?></td>
									<td><?php echo $formato['unidad_cantidad_factura']; ?></td>
									<td><?php echo $formato['cantidad_total_factura']; ?></td>
									<td><?php echo $formato['precio_sustentable_minimo']; ?></td>
									<td><?php echo $formato['reconocimiento_organico']; ?></td>
									<td><?php echo $formato['incentivo_spp']; ?></td>
									<td><?php echo $formato['otros_premios']; ?></td>
									<td><?php echo $formato['precio_total_unitario']; ?></td>
									<td><?php echo $formato['valor_total_contrato']; ?></td>
									<td><?php echo $formato['cuota_uso_reglamento']; ?></td>
									<td style="background-color:#e74c3c;color:#ecf0f1;"><?php echo $formato['total_a_pagar']; ?></td>
								</tr>
							<?php
							$contador++;
							}
		 				 ?>
					<tr class="success">
						<td class="warning"></td> <!-- # -->

						<td>
							
							<!--<input type="text" name="busqueda" id="busqueda" value="" placeholder="" maxlength="30" autocomplete="off" />-->
								<input type="text" name="spp" id="spp" value="" placeholder="#SPP" maxlength="30" autocomplete="off" onKeyUp="buscar();" required />
													
						</td>


						<!--<td class="success"><!-- #SPP(codigo de identificación)-->
							<!--* <input type="text" name="spp" id="" placeholder="#SPP" autofocus required>
						</td>-->

						<td class="warning"><!-- nombre de la opp -->
							<textarea id="nombre_opp" name="nombre_opp" value="" placeholder="Nombre de la OPP"></textarea>

							<!--<input type="text" name="nombre_opp" id="" placeholder="Nombre de la OPP">-->
						</td>

						<td class="warning"><!-- pais de la opp proveedora -->
			              <!--<select name="pais" id="pais" class="" required>
			                <option value="">Selecciona un País</option>
			                <?php 
			                $row_pais = mysql_query("SELECT * FROM paises", $dspp) or die(mysql_error());
			                while($pais = mysql_fetch_assoc($row_pais)){
			                  echo "<option value='".utf8_encode($pais['nombre'])."'>".utf8_encode($pais['nombre'])."</option>";
			                }
			                 ?>
			              </select>-->
			              <input id="pais" name="pais" value="" placeholder="pais">
						</td>

						<td class="success"><!-- fecha de facturación -->
							<input type="date" name="fecha_facturacion" id="" placeholder="dd-mm-aaaa" required>
						</td>

						<td class="info"><!-- primer intermediario -->
							<input type="text" name="primer_intermediario" id="" placeholder="primer intermediario" onBlur=" ponerMayusculas(this)">
						</td>

						<td class="info"><!-- segundo intermediario -->
							<input type="text" name="segundo_intermediario" id="" placeholder="segundo intermediario" onBlur=" ponerMayusculas(this)">
						</td>

						<td class="info"><!-- REFERENCIA CONTRATO ORIGINAL -->
							<input type="text" name="clave_contrato" id="" placeholder="clave del contrato" onBlur=" ponerMayusculas(this)"><!-- #clave del contrato -->
						</td>
						<td class="info">
							<input type="date" name="fecha_contrato" id="" placeholder="dd-mm-aaaa"><!-- #fecha del contrato -->
						</td><!-- REFERENCIA CONTRATO ORIGINAL -->

						<td><!-- producto general -->
							<input type="text" name="producto_general" id="" placeholder="Ej: café, miel, azucar" onBlur=" ponerMayusculas(this)" required>
						</td>

						<td class="success"><!-- producto especifico -->
							<input type="text" name="producto_especifico" id="" placeholder="Ej: café verde, miel de abeja, azucar refinada" onBlur=" ponerMayusculas(this)" required>
						</td>

						<td class="success"><!-- CANTIDAD TOTAL CONFORME FACTURA -->
							<select name="unidad_cantidad_total_factura" required><!-- #unidad de medida -->
								<option value="Qq">Qq</option>
								<option value="Lb">Lb</option>
								<option value="Kg">Kg</option>
								<option value="Unidad">Unidad</option>
							</select>
						</td>
						<td class="success">
							<input type="number" step="any" id="cantidad_total_factura" name="cantidad_total_factura" onChange="calcular();" onBlur=" ponerMayusculas(this)" required><!-- #cantidad total -->
						</td><!-- CANTIDAD TOTAL CONFORME FACTURA -->

						<td class="info"><!-- precio sustentable minimo -->
							<input type="number" step="any" name="precio_sustentable_minimo" id="" placeholder="importe pagado">
						</td>

						<td class="info"><!-- reconocimiento organico -->
							<input type="number" step="any" name="reconocimiento_organico" id="" placeholder="importe pagado">
						</td>

						<td class="info"><!-- incentivo spp -->
							<input type="number" step="any" name="incentivo_spp" id="" placeholder="importe pagado">
						</td>

						<td class="info"><!-- otros premios -->
							<input type="number" step="any" name="otros_premios" id="" placeholder="importe pagado">
						</td>

						<td class="success"><!-- precio total unitario pagado -->
							<input type="number" step="any" id="precio_total_unitario" name="precio_total_unitario" id="precio_total_unitario" onChange="calcular();" value="0" placeholder="Ej: 40" required>
						</td>

						<td class="warning"><!-- valor total contrato -->
							<input type="text" id="valor_total_contrato" name="valor_total_contrato" id="" placeholder="Valor total del Contrato" readonly>
						</td>

						<td class="warning"><!-- cuota de uso reglamento -->
							<input type="text" id="cuota_uso_reglamento" name="cuota_uso_reglamento" id="cuota_uso_reglamento" value="0" placeholder="Cuota uso reglamento" readonly>
						</td>

						<td class="warning"><!-- total a pagar -->
							<input type="text" name="total_a_pagar" id="total_a_pagar" placeholder="Total a pagar" readonly>
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
$(document).ready(function() {
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
function ponerMayusculas(nombre) 
{ 
nombre.value=nombre.value.toUpperCase(); 
} 
var contador=0;
var cuota_fija_anual = 0.01;
	function calcular(){
		cantidad_total_factura = document.getElementById("cantidad_total_factura").value;
		precio_total_unitario = document.getElementById("precio_total_unitario").value;

		//calculamos el valor total contrato
		valor_total_contrato = parseFloat(cantidad_total_factura) * parseFloat(precio_total_unitario);
		total_contrato_redondeado = parseFloat(valor_total_contrato.toFixed(2));
		//calculamos el valor de la cuota de uso reglamento
		//cuota_uso_reglamento = valor_total_contrato * cuota_fija_anual;
		//calculamos el total a pagar
		total_a_pagar = valor_total_contrato * cuota_fija_anual;

		total_redondeado = parseFloat(total_a_pagar.toFixed(2));

		//calculamos el valor total del contrato

		//21_07_2017 valor_total_contrato_redondeado = parseFloat(valor_total_contrato.toFixed(2));
		/* se redondea el resultado a 2 decimales */
		//valor_total_contrato = parseFloat(Math.round((precio_total_unitario * peso_cantidad_total_contrato) * 100) / 100).toFixed(2);
		document.getElementById("valor_total_contrato").value = total_contrato_redondeado; 
		document.getElementById("cuota_uso_reglamento").value = '1%'; 
		document.getElementById("total_a_pagar").value = total_redondeado; 

		//calculamos el total a pagar
		/*if(isNaN(cuota_uso_reglamento)){ // revisamos si es porcentaje
			//alert("ES PORCENTAJE : "+cuota_uso_reglamento);
			total_final = parseFloat(valor_total_contrato_redondeado) * (0.01);
		}else{	//si es solo numero
			//alert("ES NUMERO: "+cuota_uso_reglamento);
			total_final = parseFloat(peso_cantidad_total_contrato) * parseFloat(cuota_uso_reglamento);
		}*/
	}


</script>