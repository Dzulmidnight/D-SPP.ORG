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
if(isset($_POST['agregar_formato']) && $_POST['agregar_formato'] == 1){
	
	$fecha_elaboracion = time();

	if(isset($_POST['contador_formato'])){
		$contador_formato = $_POST['contador_formato'];
	}else{
		$contador = NULL;
		$mensaje = "NO SE ENCONTRARON DATOS";
	}

		if(isset($_POST['opp'])){
			$opp = $_POST['opp'];
		}else{
			$opp = NULL;
		}
		if(isset($_POST['pais'])){
			$pais = $_POST['pais'];
		}else{
			$pais = NULL;
		}
		if(isset($_POST['fecha_compra'])){
			$fecha_compra = $_POST['fecha_compra'];
		}else{
			$fecha_compra = NULL;
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
		if(isset($_POST['valor_total_contrato'])){
			$valor_total_contrato = $_POST['valor_total_contrato'];
		}else{
			$valor_total_contrato = NULL;
		}
		if(isset($_POST['total'])){
			$total = $_POST['total'];
		}else{
			$total = NULL;
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
		if(isset($_POST['producto_general'])){
			$producto_general = $_POST['producto_general'];
		}else{
			$producto_general = NULL;
		}
		if(isset($_POST['clave_contrato'])){
			$clave_contrato = $_POST['clave_contrato'];
		}else{
			$clave_contrato = NULL;
		}
		if(isset($_POST['fecha_contrato'])){
			$fecha_contrato = $_POST['fecha_contrato'];
		}else{
			$fecha_contrato = NULL;
		}
		if(isset($_POST['producto_especifico'])){
			$producto_especifico = $_POST['producto_especifico'];
		}else{
			$producto_especifico = NULL;
		}
		if(isset($_POST['peso_cantidad_total_contrato'])){
			$peso_cantidad_total_contrato = $_POST['peso_cantidad_total_contrato'];
		}else{
			$peso_cantidad_total_contrato = NULL;
		}
		if(isset($_POST['unidad_cantidad_total_contrato'])){
			$unidad_cantidad_total_contrato = $_POST['unidad_cantidad_total_contrato'];
		}else{
			$unidad_cantidad_total_contrato = NULL;
		}
		if(isset($_POST['peso_total_reglamento'])){
			$peso_total_reglamento = $_POST['peso_total_reglamento'];
		}else{
			$peso_total_reglamento = NULL;
		}
		if(isset($_POST['unidad_peso_total_reglamento'])){
			$unidad_peso_total_reglamento = $_POST['unidad_peso_total_reglamento'];
		}else{
			$unidad_peso_total_reglamento = NULL;
		}
		if(isset($_POST['precio_precio_total_unitario'])){
			$precio_precio_total_unitario = $_POST['precio_precio_total_unitario'];
		}else{
			$precio_precio_total_unitario = NULL;
		}
		if(isset($_POST['unidad_precio_total_unitario'])){
			$unidad_precio_total_unitario = $_POST['unidad_precio_total_unitario'];
		}else{
			$unidad_precio_total_unitario = NULL;
		}

		if(isset($_POST['precio_precio_sustentable'])){
			$precio_precio_sustentable = $_POST['precio_precio_sustentable'];
		}else{
			$precio_precio_sustentable = NULL;
		}
		if(isset($_POST['unidad_precio_sustentable'])){
			$unidad_precio_sustentable = $_POST['unidad_precio_sustentable'];
		}else{
			$unidad_precio_sustentable = NULL;
		}
		if(isset($_POST['precio_reconocimiento_organico'])){
			$precio_reconocimiento_organico = $_POST['precio_reconocimiento_organico'];
		}else{
			$precio_reconocimiento_organico = NULL;
		}
		if(isset($_POST['unidad_reconocimiento_organico'])){
			$unidad_reconocimiento_organico = $_POST['unidad_reconocimiento_organico'];
		}else{
			$unidad_reconocimiento_organico = NULL;
		}
		if(isset($_POST['precio_incentivo_spp'])){
			$precio_incentivo_spp = $_POST['precio_incentivo_spp'];
		}else{
			$precio_incentivo_spp = NULL;
		}
		if(isset($_POST['unidad_incentivo_spp'])){
			$unidad_incentivo_spp = $_POST['unidad_incentivo_spp'];
		}else{
			$unidad_incentivo_spp = NULL;
		}
		if(isset($_POST['cuota_uso_reglamento'])){
			$cuota_uso_reglamento = $_POST['cuota_uso_reglamento'];
		}else{
			$cuota_uso_reglamento = NULL;
		}
		if(isset($_POST['unidad_cuota_uso_reglamento'])){
			$unidad_cuota_uso_reglamento = $_POST['unidad_cuota_uso_reglamento'];
		}else{
			$unidad_cuota_uso_reglamento = NULL;
		}

		for($i=1;$i<=count($contador_formato);$i++){
			$fecha_compra = strtotime($fecha_compra[$i]);
			//Iniciamos insertar formato_compras
				$insertSQL = sprintf("INSERT INTO formato_compras(idempresa, opp, pais, fecha_compra, producto_general, producto_especifico, valor_total_contrato, total, fecha_elaboracion) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
					GetSQLValueString($idempresa, "int"),
					GetSQLValueString($opp[$i], "text"),
					GetSQLValueString($pais[$i], "text"),
					GetSQLValueString($fecha_compra, "int"),
					GetSQLValueString($producto_general[$i], "text"),
					GetSQLValueString($producto_especifico[$i], "text"),
					GetSQLValueString($valor_total_contrato[$i], "int"),
					GetSQLValueString($total[$i], "int"),
					GetSQLValueString($fecha_elaboracion, "int"));
				$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

				$idformato_compras = mysql_insert_id($dspp);

				//inciamos insertar intermediarios
				$insertSQL = sprintf("INSERT INTO intermediarios(idformato_compras, primero, segundo) VALUES(%s, %s, %s)",
					GetSQLValueString($idformato_compras, "int"),
					GetSQLValueString($primer_intermediario[$i], "text"),
					GetSQLValueString($segundo_intermediario[$i], "text"));
				$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
				//terminamos insertar intermediarios

				//inciamos insertar referencia_contrato
				$fecha_contrato = strtotime($fecha_contrato[$i]);
				$insertSQL = sprintf("INSERT INTO referencia_contrato(clave, fecha, idformato_compras) VALUES (%s, %s, %s)",
					GetSQLValueString($clave_contrato[$i], "text"),
					GetSQLValueString($fecha_contrato, "in"),
					GetSQLValueString($idformato_compras, "int"));
				$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
				//terminamos insertar referencia_contrato

				//iniciamos insertar cantidad_total_contrato
				$insertSQL = sprintf("INSERT INTO cantidad_total_contrato(peso, unidad, idformato_compras) VALUES(%s, %s, %s)",
					GetSQLValueString($peso_cantidad_total_contrato[$i], "int"),
					GetSQLValueString($unidad_cantidad_total_contrato[$i], "int"),
					GetSQLValueString($idformato_compras, "int"));
				$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
				//terminamos insertar cantidad_total_contrato

				//iniciamos insertar peso_total_reglamento
				$insertSQL = sprintf("INSERT INTO peso_total_reglamento(peso, unidad, idformato_compras) VALUES (%s, %s, %s)",
					GetSQLValueString($peso_total_reglamento[$i], "int"),
					GetSQLValueString($unidad_peso_total_reglamento[$i], "text"),
					GetSQLValueString($idformato_compras, "int"));
				$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
				//terminamos insertar peso_total_reglamento

				//iniciamos insertar precio_total_unitario
				$insertSQL = sprintf("INSERT INTO precio_total_unitario(precio, unidad, idformato_compras) VALUES (%s, %s, %s)",
					GetSQLValueString($precio_precio_total_unitario[$i], "int"),
					GetSQLValueString($unidad_precio_total_unitario[$i], "text"),
					GetSQLValueString($idformato_compras, "int"));
				$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
				//terminamos insertar precio_total_unitario

				//inciamos insertar precio_sustentable
				$insertSQL = sprintf("INSERT INTO precio_sustentable(precio, unidad, idformato_compras) VALUES (%s, %s, %s)",
					GetSQLValueString($precio_precio_sustentable[$i], "int"),
					GetSQLValueString($unidad_precio_sustentable[$i], "text"),
					GetSQLValueString($idformato_compras, "int"));
				$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
				//termianos insertar precio_sustentable

				//iniciamos insertar reconocimiento_organico
				$insertSQL = sprintf("INSERT INTO reconocimiento_organico(precio, unidad, idformato_compras) VALUES (%s, %s, %s)",
					GetSQLValueString($precio_reconocimiento_organico[$i], "int"),
					GetSQLValueString($unidad_reconocimiento_organico[$i], "text"),
					GetSQLValueString($idformato_compras, "text"));
				$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
				//terminamos insertar reconocimiento_organico

				//iniciamos insertar incentivo_spp
				$insertSQL = sprintf("INSERT INTO incentivo_spp(precio, unidad, idformato_compras) VALUES (%s, %s, %s)",
					GetSQLValueString($precio_incentivo_spp[$i], "int"),
					GetSQLValueString($unidad_incentivo_spp[$i], "text"), 
					GetSQLValueString($idformato_compras, "int"));
				$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
				//terminamos insertar incetivo_spp

				//iniciamos insertar cuota_uso_reglamento
				$insertSQL = sprintf("INSERT INTO cuota_uso_reglamento(cuota, unidad, idformato_compras) VALUES (%s, %s, %s)",
					GetSQLValueString($cuota_uso_reglamento[$i], "int"),
					GetSQLValueString($unidad_cuota_uso_reglamento[$i], "text"),
					GetSQLValueString($idformato_compras, "int"));
				$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
				//terminamos insertar cuota_uso_reglamento
			//Termina insertar formato compras
		}



}

?>

<h3>Nuevo Informe Trimestral</h3>
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

	<div class="col-md-12">
		<form class="form-horizontal" method="POST">
		 	<table class="table table-bordered table-condensed" style="font-size:11px;" id="tablaInforme">
		 		<thead>
		 			<tr class="success">
		 				<th class="text-center">
							<button type="button" onclick="tablaInforme()" class="btn btn-xs btn-primary" aria-label="Left Align">
							  <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
							</button>
		 				</th>
						<th class="text-center">OPP</th>
						<th class="text-center">País de la OPP</th>
						<th class="text-center">Fecha de Compra</th>
						<th class="text-center">Primer Intermediario</th>
						<th class="text-center">Segundo Intermediario</th>
						<th class="text-center">Tipo de Producto</th>
						<th colspan="2" class="text-center">Referencia Contrato Original con OPP</th>
						<th class="text-center">Producto Especifico de acuerdo al contrato original</th>
						<th colspan="2" class="text-center">Cantidad Total Conforme Contrato</th>
						<th colspan="2" class="text-center">Peso Total Conforme Unidad de Medida Reglamento de Uso</th>
						<th colspan="2" class="text-center">Precio Total Unitario</th>
						<th colspan="2" class="text-center">Precio Sustentable Minimo</th>
						<th colspan="2" class="text-center">Reconocimiento Orgánico</th>
						<th colspan="2" class="text-center">Incentivo SPP</th>
						<th class="text-center">Valor Total Contrato</th>
						<th colspan="2" class="text-center">Cuota de Uso Reglamento</th>
						<th class="text-center">Total a Pagar</th>
		 			</tr>
		 		</thead>
		 		<tbody>
		 			<tr>
		 				<td>
		 					Clic para agregar un nuevo espacio.
		 				</td>
		 				<td>
		 					Nombre de la OPP de donde proviene el producto.
		 				</td>
		 				<td>
		 					País sede la OPP de donde priviene el producto.
		 				</td>
		 				<td>
		 					Fecha de embarque del producto. la fecha de la compra del producto por el Comprador Final, usuario del SPP.
		 				</td>
		 				<td>
		 					Nombre de la contraparte comercial que hace la compra del producto a la OPP, si no es el Comprador Final.
		 				</td>
		 				<td>
		 					Nombre de la contraparte comercial que hace la compra del producto al Primer Intermediario, si no el Comprador Final.
		 				</td>
		 				<td>
		 					El producto que se comercializa con certificación SPP. Ej: Café, Miel, Banano, etc.
		 				</td>
		 				<!-- INICIA REFERENCIA CONTRATO -->
			 				<td>
			 					Número o clave del Contrato Original, es decir, el contrato de venta de la OPP al Comprador o al Primer Intermediario.
			 				</td>
			 				<td>
			 					Fecha del Contrato Original de venta de la OPP.
			 				</td>
		 				<!-- TERMINA REFERENCIA CONTRATO -->
		 				<td>
		 					Producto especifico. Ej: Café verde arábica, Miel tipo A, Chips de platano.
		 				</td>
		 				<!-- INICIA CANTIDAD TOTAL CONFORME CONTRATO -->
			 				<td>
			 					Cantidad total del producto en unidad de medida final indicada en el contrato original, Ej: 417.26
			 				</td>
			 				<td>
			 					Unidad de medida utilizada.
			 				</td>
		 				<!-- TERMINA CANTIDAD TOTAL CONFORME CONTRATO -->
		 				<!-- INICIA PESO TOTAL CONFORME UNIDAD DE MEDIDA REGLAMENTO DE USO -->
			 				<td>
			 					Conversión del peso total conforme a unidades de la cuota de uso establecida.
			 				</td>
			 				<td>
			 					Unidad de medida utilizada.
			 				</td>
		 				<!-- TERMINA PESO TOTAL CONFORME UNIDAD DE MEDIDA REGLAMENTO DE USO -->
		 				<!-- INICIA PRECIO TOTAL UNITARIO -->
			 				<td>
			 					Precio Unitario total pagado (en unidades de peso y divisa conforme lista de precios SPP GLOBAL).
			 				<td>
			 					Unidad de medida del precio pagado por el comprador.
			 				</td>
		 				<!-- TERMINA PRECIO TOTAL UNITARIO -->
		 				<!-- INICIA PRECIO SUSTENTABLE MINIMO -->
			 				<td>
			 					Precio Unitario total pagado (en unidades de peso y divisa conforme lista de precios SPP GLOBAL).
			 				</td>
			 				<td>
			 					Unidad de medida del precio pagado por el Comprador.
			 				</td>
		 				<!-- TERMINA PRECIO SUSTENTABLE MINIMO -->
		 				<!-- INICIA RECONOCIMIENTO ORGANICO -->
			 				<td>
			 					Precio pagado (en unidades de peso y divisa conforme lista de precios SPP GLOBAL)
			 				</td>
			 				<td>
			 					Unidad de medida del precio pagado por el Comprador
			 				</td>
		 				<!-- TERMINA RECONOCIMIENTO ORGANICO -->
		 				<!-- INICIA INCENTIVO SPP -->
			 				<td>
			 					Precio pagado (en unidades de peso y divisa conforme lista de precios SPP GLOBAL).
			 				</td>
			 				<td>
			 					Unidad de medida del precio pagado por el Comprador.
			 				</td>
		 				<!-- TERMINA INCENTIVO SPP -->
		 				<td>
		 					Valor total del contrato.
		 				</td>
		 				<!-- INICIA CUOTA DE USO REGLAMENTO -->
		 					<td>
		 						Cuota establecida conforme Reglamento de Costos SPP GLOBAL para el producto y término comercial.
		 					</td>
		 					<td>
		 						Unidad de medida conforme a Reglamento de Costos.
		 					</td>
		 				<!-- TERMIAN CUOTA DE USO REGLAMENTO -->
		 				<td>
		 					Total a pagar a SPP GLOBAL conforme cuota de uso y volumen de lote.
		 				</td>
		 			</tr>
		 			<tr>
		 				<td colspan="26"><button class="btn btn-success" type="submit" style="width:100%" name="agregar_formato" value="1">Guardar Información</button></td>
		 			</tr>
		 		</tbody>
		 	</table>
		</form>		
	</div>
</div>
<script>
var contador=0;
	function tablaInforme()
	{
		contador++;
		var table = document.getElementById("tablaInforme");
		{
			var row = table.insertRow(2);
			var cell1 = row.insertCell(0);
			var cell2 = row.insertCell(1);
			var cell3 = row.insertCell(2);
			var cell4 = row.insertCell(3);

			var cell5 = row.insertCell(4);
			var cell6 = row.insertCell(5);
			var cell7 = row.insertCell(6);
			var cell8 = row.insertCell(7);
			var cell9 = row.insertCell(8);

			var cell10 = row.insertCell(9);
			var cell11 = row.insertCell(10);
			var cell12 = row.insertCell(11);
			var cell13 = row.insertCell(12);
			var cell14 = row.insertCell(13);

			var cell15 = row.insertCell(14);
			var cell16 = row.insertCell(15);
			var cell17 = row.insertCell(16);
			var cell18 = row.insertCell(17);
			var cell19 = row.insertCell(18);

			var cell20 = row.insertCell(19);
			var cell21 = row.insertCell(20);
			var cell22 = row.insertCell(21);
			var cell23 = row.insertCell(22);
			var cell24 = row.insertCell(23);
			var cell25 = row.insertCell(24);
			var cell26 = row.insertCell(25);




			cell1.innerHTML = contador+'<input type="hidden" name="contador_formato['+contador+']" id="" value="'+contador+'">';
			//nombre del opp
			cell2.innerHTML = '<input type="text" name="opp['+contador+']" id="" placeholder="opp">';
			//pais del opp
			cell3.innerHTML = '<input type="text" name="pais['+contador+']" id="" placeholder="pais">';
			//fecha de compra
			cell4.innerHTML = '<input type="date" name="fecha_compra['+contador+']" id="" placeholder="dd/mm/aaaa">';
			//primer intermediario
			cell5.innerHTML = '<input type="text" name="primer_intermediario['+contador+']" id="" placeholder="primer intermediario">';
			//segundo intermediario
			cell6.innerHTML = '<input type="text" name="segundo_intermediario['+contador+']" id="" placeholder="segundo_intermediario">';

			//TIPO DE PRODUCTO (producto_general)
			cell7.innerHTML = '<input type="text" name="producto_general['+contador+']" id="" placeholder="producto_general">';

			//INICIA referencia_contrato
				cell8.innerHTML = '<input type="text" name="clave_contrato['+contador+']" id="" placeholder="clave_contrato">';

				cell9.innerHTML = '<input type="date" name="fecha_contrato['+contador+']" id="" placeholder="dd/mm/aaaa">';
			//TERMINA referencia_contrato

			//producto_especifico
			cell10.innerHTML = '<input type="text" name="producto_especifico['+contador+']" id="" placeholder="producto_especifico">';

			//INICIA cantidad_total_contrato
				cell11.innerHTML = '<input type="text" style="background-color:gray" name="peso_cantidad_total_contrato['+contador+']" id="peso_cantidad_total_contrato" onChange="calcular();" readonly placeholder="cantidad">';

				cell12.innerHTML = '<input type="text" name="unidad_cantidad_total_contrato['+contador+']" id="" placeholder="unidad">';
			//TERMINA cantidad_total_contrato

			//INICIA peso_total_reglamento
				cell13.innerHTML = '<input type="text" name="peso_total_reglamento['+contador+']" id="" placeholder="unidad">';

				cell14.innerHTML = '<input type="text" name="unidad_peso_total_reglamento['+contador+']" id="" placeholder="medida">';
			//TERMINA peso_total_reglamento

			//INICIA precio_total_unitario
				cell15.innerHTML = '<input type="text" name="precio_precio_total_unitario['+contador+']" id="precio_total_unitario" placeholder="precio" onChange="calcular();" readonly style="background-color:gray">';

				cell16.innerHTML = '<input type="text" name="unidad_precio_total_unitario['+contador+']" id="" placeholder="unidad_medida">';
			// TERMINA precio_total_unitario

			//INICIA PRECIO SUSTENTABLE MINIMO precio_sustentable
				cell17.innerHTML = '<input type="text" name="precio_precio_sustentable['+contador+']" id="precio_sustentable_minimo" onChange="calcular();" value="0" placeholder="precio">';

				cell18.innerHTML = '<input type="text" name="unidad_precio_sustentable['+contador+']" id="" placeholder="unidad_medida">';
			// TERMINA PRECIO SUSTENTABLE MINIMO precio_sustentable

			// INICIA RECONOCIMIENTO ORGANICO reconocimiento_organico
				cell19.innerHTML = '<input type="text" name="precio_reconocimiento_organico['+contador+']" id="precio_reconocimiento_organico" onChange="calcular();" value="0" placeholder="precio">';

				cell20.innerHTML = '<input type="text" name="unidad_reconocimiento_organico['+contador+']" id="" placeholder="unidad_medida">';
			// TERMINA RECONOCIMIENTO ORGANICO reconocimiento_organico

			//INICIA incentivo_spp
				cell21.innerHTML = '<input type="text" name="precio_incentivo_spp['+contador+']" id="precio_incentivo_spp" onChange="calcular();" value="0" placeholder="precio">';

				cell22.innerHTML = '<input type="text" name="unidad_incentivo_spp['+contador+']" id="" placeholder="unidad_medida">';
			// TERMINA incentivo_spp

			// VALOR TOTAL CONTRATO
			cell23.innerHTML = '<input type="text" style="background-color:gray" name="valor_total_contrato['+contador+']" id="total_contrato" onChange="calcular();" value="0.0" readonly placeholder="valor_total">';

			//INICIA cuota_uso_reglamento
				cell24.innerHTML = '<input type="text" name="cuota_uso_reglamento['+contador+']" i d="cuota_uso" value="30" placeholder="cuota">';

				cell25.innerHTML = '<input type="text" name="unidad_cuota_uso_reglamento['+contador+']" id="" placeholder="unidad">';
			//TERMINA cuota_uso_reglamento

			//TOTAL A PAGAR
			cell26.innerHTML = '<input type="text" style="background-color:gray" name="total['+contador+']" id="resultado_total" onChange="calcular();" value="0.0" readonly placeholder="total">';

		}
	}

	function calcular(){
		precio_total_unitario = document.getElementById("precio_total_unitario").value;
		cantidad_total_contrato = document.getElementById("cantidad_total_contrato").value;

		precio_sustentable_minimo = document.getElementById("precio_sustentable_minimo").value;
		precio_reconocimiento_organico = document.getElementById("precio_reconocimiento_organico").value;
		precio_incentivo_spp = document.getElementById("precio_incentivo_spp").value;

		precio_total_unitario = parseFloat(precio_sustentable_minimo)+parseFloat(precio_reconocimiento_organico)+parseFloat(precio_incentivo_spp);

		document.getElementById("precio_total_unitario").value = precio_total_unitario;

		//calculamos el valor total del contrato
		valor_total_contrato = parseFloat(precio_total_unitario)*parseFloat(cantidad_total_contrato);
		document.getElementById("valor_total_contrato").value = valor_total_contrato;
	}


</script>