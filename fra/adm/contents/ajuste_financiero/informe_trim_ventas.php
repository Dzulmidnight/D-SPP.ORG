<?php 
require_once('../Connections/dspp.php'); 
require_once('../Connections/mail.php');

mysql_select_db($database_dspp, $dspp);


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
$idtrim = 'T_PRUEBA2';

if(isset($_POST['agregar_formato']) && $_POST['agregar_formato'] == 1){
	
	$fecha_elaboracion = time();


		if(isset($_POST['comprador'])){
			$comprador = $_POST['comprador'];
		}else{
			$comprador = NULL;
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
		}/*
		if(isset($_POST['total'])){
			$total = $_POST['total'];
		}else{
			$total = NULL;
		}*/
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
		/*if(isset($_POST['unidad_cuota_uso_reglamento'])){
			$unidad_cuota_uso_reglamento = $_POST['unidad_cuota_uso_reglamento'];
		}else{
			$unidad_cuota_uso_reglamento = NULL;
		}*/


			$fecha_compra = strtotime($fecha_compra);
			//Iniciamos insertar formato_ventas
				$insertSQL = sprintf("INSERT INTO formato_ventas(idtrim, comprador, pais, fecha_compra, producto_general, producto_especifico, total_contrato, fecha_elaboracion) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
					GetSQLValueString($idtrim, "text"),
					GetSQLValueString($comprador, "text"),
					GetSQLValueString($pais, "text"),
					GetSQLValueString($fecha_compra, "int"),
					GetSQLValueString($producto_general, "text"),
					GetSQLValueString($producto_especifico, "text"),
					GetSQLValueString($valor_total_contrato, "double"),
					//GetSQLValueString($total, "double"),
					GetSQLValueString($fecha_elaboracion, "int"));
				$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

				$idformato_ventas = mysql_insert_id($dspp);

				//inciamos insertar intermediarios
				$insertSQL = sprintf("INSERT INTO intermediarios(idformato_ventas, primero, segundo) VALUES(%s, %s, %s)",
					GetSQLValueString($idformato_ventas, "int"),
					GetSQLValueString($primer_intermediario, "text"),
					GetSQLValueString($segundo_intermediario, "text"));
				$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
				//terminamos insertar intermediarios

				//inciamos insertar referencia_contrato
				$fecha_contrato = strtotime($fecha_contrato);
				$insertSQL = sprintf("INSERT INTO referencia_contrato(clave, fecha, idformato_ventas) VALUES (%s, %s, %s)",
					GetSQLValueString($clave_contrato, "text"),
					GetSQLValueString($fecha_contrato, "in"),
					GetSQLValueString($idformato_ventas, "int"));
				$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
				//terminamos insertar referencia_contrato

				//iniciamos insertar cantidad_total_contrato
				$insertSQL = sprintf("INSERT INTO cantidad_total_contrato(peso, unidad, idformato_ventas) VALUES(%s, %s, %s)",
					GetSQLValueString($peso_cantidad_total_contrato, "double"),
					GetSQLValueString($unidad_cantidad_total_contrato, "text"),
					GetSQLValueString($idformato_ventas, "int"));
				$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
				//terminamos insertar cantidad_total_contrato

				//iniciamos insertar peso_total_reglamento
				$insertSQL = sprintf("INSERT INTO peso_total_reglamento(peso, unidad, idformato_ventas) VALUES (%s, %s, %s)",
					GetSQLValueString($peso_total_reglamento, "double"),
					GetSQLValueString($unidad_peso_total_reglamento, "text"),
					GetSQLValueString($idformato_ventas, "int"));
				$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
				//terminamos insertar peso_total_reglamento

				//iniciamos insertar precio_total_unitario
				$insertSQL = sprintf("INSERT INTO precio_total_unitario(precio, unidad, idformato_ventas) VALUES (%s, %s, %s)",
					GetSQLValueString($precio_precio_total_unitario, "double"),
					GetSQLValueString($unidad_precio_total_unitario, "text"),
					GetSQLValueString($idformato_ventas, "int"));
				$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
				//terminamos insertar precio_total_unitario

				//inciamos insertar precio_sustentable
				$insertSQL = sprintf("INSERT INTO precio_sustentable(precio, unidad, idformato_ventas) VALUES (%s, %s, %s)",
					GetSQLValueString($precio_precio_sustentable, "double"),
					GetSQLValueString($unidad_precio_sustentable, "text"),
					GetSQLValueString($idformato_ventas, "int"));
				$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
				//termianos insertar precio_sustentable

				//iniciamos insertar reconocimiento_organico
				$insertSQL = sprintf("INSERT INTO reconocimiento_organico(precio, unidad, idformato_ventas) VALUES (%s, %s, %s)",
					GetSQLValueString($precio_reconocimiento_organico, "double"),
					GetSQLValueString($unidad_reconocimiento_organico, "text"),
					GetSQLValueString($idformato_ventas, "text"));
				$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
				//terminamos insertar reconocimiento_organico

				//iniciamos insertar incentivo_spp
				$insertSQL = sprintf("INSERT INTO incentivo_spp(precio, unidad, idformato_ventas) VALUES (%s, %s, %s)",
					GetSQLValueString($precio_incentivo_spp, "double"),
					GetSQLValueString($unidad_incentivo_spp, "text"), 
					GetSQLValueString($idformato_ventas, "int"));
				$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
				//terminamos insertar incetivo_spp

				//iniciamos insertar cuota_uso_reglamento
				/*$insertSQL = sprintf("INSERT INTO cuota_uso_reglamento(cuota, unidad, idformato_ventas) VALUES (%s, %s, %s)",
					GetSQLValueString($cuota_uso_reglamento, "text"),
					GetSQLValueString($unidad_cuota_uso_reglamento, "text"),
					GetSQLValueString($idformato_ventas, "int"));
				$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());*/
				//terminamos insertar cuota_uso_reglamento
			//Termina insertar formato compras

}

?>

<div class="row">

	<div class="col-md-12">

		<form class="form-horizontal" method="POST">
		 	<table class="table table-bordered table-condensed" style="font-size:11px;" id="tablaInforme">
		 		<thead>
		 			<tr>
		 				<th colspan="18"><h4>Formato Trimestral Ventas SPP</h4></th>
		 			</tr>
		 			<tr class="success">
		 				<th class="text-center">
					
		 				</th>
						<th class="text-center">Nombre del Comprador Final</th>
						<th class="text-center">País del Comprador Final</th>
						<th class="text-center">Fecha de Compra(dd/mm/aaaa)</th>
						<th class="text-center">Primer Intermediario</th>
						<th class="text-center">Segundo Intermediario</th>
						<th class="text-center">Tipo de Producto</th>
						<th colspan="2" class="text-center">Referencia Contrato Original con OPP</th>
						<th class="text-center">Producto Especifico de acuerdo al contrato original</th>
						<th colspan="2" class="text-center">Cantidad Total Conforme Contrato</th>
						<th colspan="2" class="text-center">Peso Total Conforme Unidad de Medida Reglamento de Uso</th>
						<th colspan="2" class="text-center">Precio Sustentable Minimo</th>
						<th colspan="2" class="text-center">Reconocimiento Orgánico</th>
						<th colspan="2" class="text-center">Incentivo SPP</th>
						<th colspan="2" class="text-center">Precio Total Unitario</th>
						<th class="text-center">Valor Total Contrato</th>
		 			</tr>
		 		</thead>
		 		<tbody>
		 			<tr>
		 				<td>
		 					#
		 				</td>
		 				<td>
		 					Nombre del Comprador final que pone el producto en el mercado.
		 				</td>
		 				<td>
		 					País donde esta ubicado el Comprador final que pone el producto en el mercado.
		 				</td>
		 				<td>
		 					La Fecha de la compra del producto por el Comprador Final, usuario del SPP(de la OPP directamente o a través de algun intermediario).
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

		 				<!-- INICIA PRECIO TOTAL UNITARIO -->
			 				<td>
			 					Precio Unitario total pagado (en unidades de peso y divisa conforme lista de precios SPP GLOBAL).
			 				<td>
			 					Unidad de medida del precio pagado por el comprador.
			 				</td>
		 				<!-- TERMINA PRECIO TOTAL UNITARIO -->


		 			</tr>
		 				<?php
							$row_registro = mysql_query("SELECT formato_ventas.idtrim, formato_ventas.comprador, formato_ventas.pais, formato_ventas.fecha_compra, formato_ventas.producto_general, formato_ventas.producto_especifico, formato_ventas.total_contrato, intermediarios.primero, intermediarios.segundo, referencia_contrato.clave, referencia_contrato.fecha AS 'fecha_contrato', cantidad_total_contrato.peso AS 'ctc_peso', cantidad_total_contrato.unidad AS 'ctc_unidad', peso_total_reglamento.peso AS 'ptr_peso', peso_total_reglamento.unidad AS 'ptr_unidad', precio_total_unitario.precio AS 'ptu_precio', precio_total_unitario.unidad AS 'ptu_unidad', precio_sustentable.precio AS 'ps_precio', precio_sustentable.unidad AS 'ps_unidad', reconocimiento_organico.precio AS 'ro_precio', reconocimiento_organico.unidad AS 'ro_unidad', incentivo_spp.precio AS 'incentivo_precio', incentivo_spp.unidad AS 'incentivo_unidad' FROM formato_ventas INNER JOIN intermediarios ON formato_ventas.idformato_ventas = intermediarios.idformato_ventas INNER JOIN referencia_contrato ON formato_ventas.idformato_ventas = referencia_contrato.idformato_ventas INNER JOIN cantidad_total_contrato ON formato_ventas.idformato_ventas = cantidad_total_contrato.idformato_ventas INNER JOIN peso_total_reglamento ON formato_ventas.idformato_ventas = peso_total_reglamento.idformato_ventas INNER JOIN precio_total_unitario ON formato_ventas.idformato_ventas = precio_total_unitario.idformato_ventas INNER JOIN precio_sustentable ON formato_ventas.idformato_ventas = precio_sustentable.idformato_ventas INNER JOIN reconocimiento_organico ON formato_ventas.idformato_ventas = reconocimiento_organico.idformato_ventas INNER JOIN incentivo_spp ON formato_ventas.idformato_ventas = incentivo_spp.idformato_ventas");
							$contador = 1;

							while($informacion_formato = mysql_fetch_assoc($row_registro)){
							?>
								<tr class="active">
									<td><?php echo $contador; ?></td>
									<td><?php echo $informacion_formato['comprador']; ?></td>
									<td><?php echo $informacion_formato['pais']; ?></td>
									<td><?php echo date('d/m/Y',$informacion_formato['fecha_compra']); ?></td>
									<td><?php echo $informacion_formato['primero']; ?></td>
									<td><?php echo $informacion_formato['segundo']; ?></td>
									<td><?php echo $informacion_formato['producto_general']; ?></td>
									<td><?php echo $informacion_formato['clave']; ?></td>
									<td><?php echo date('d/m/Y',$informacion_formato['fecha_contrato']); ?></td>
									<td><?php echo $informacion_formato['producto_especifico']; ?></td>
									<td><?php echo $informacion_formato['ctc_peso']; ?></td>
									<td><?php echo $informacion_formato['ctc_unidad']; ?></td>
									<td><?php echo $informacion_formato['ptr_peso']; ?></td>
									<td><?php echo $informacion_formato['ptr_unidad']; ?></td>
									<td><?php echo $informacion_formato['ptu_precio']; ?></td>
									<td><?php echo $informacion_formato['ptu_unidad']; ?></td>
									<td><?php echo $informacion_formato['ps_precio']; ?></td>
									<td><?php echo $informacion_formato['ps_unidad']; ?></td>
									<td><?php echo $informacion_formato['ro_precio']; ?></td>
									<td><?php echo $informacion_formato['ro_unidad']; ?></td>
									<td><?php echo $informacion_formato['incentivo_precio']; ?></td>
									<td><?php echo $informacion_formato['incentivo_unidad']; ?></td>
									<td><?php echo $informacion_formato['total_contrato']; ?></td>

								</tr>
							<?php
							$contador++;
							}
		 				 ?>
					<tr class="success">
						<td></td>
						<td>
							<input type="text" name="comprador" id="" placeholder="comprador" autofocus required>
						</td>
						<td>
			              <select name="pais" id="pais" class="" required>
			                <option value="">Selecciona un País</option>
			                <?php 
			                $row_pais = mysql_query("SELECT * FROM paises", $dspp) or die(mysql_error());
			                while($pais = mysql_fetch_assoc($row_pais)){
			                  echo "<option value='".utf8_encode($pais['nombre'])."'>".utf8_encode($pais['nombre'])."</option>";
			                }
			                 ?>
			              </select>
						</td>
						<td>
							<input type="date" name="fecha_compra" id="" placeholder="dd/mm/aaaa">
						</td>
						<td>
							<input type="text" name="primer_intermediario" id="" placeholder="primer intermediario">
						</td>
						<td>
							<input type="text" name="segundo_intermediario" id="" placeholder="segundo_intermediario">
						</td>
						<td>
							<input type="text" name="producto_general" id="" placeholder="producto_general">
						</td>
						<td>
							<input type="text" name="clave_contrato" id="" placeholder="clave_contrato">
						</td>
						<td>
							<input type="date" name="fecha_contrato" id="" placeholder="dd/mm/aaaa">
						</td>
						<td>
							<input type="text" name="producto_especifico" id="" placeholder="producto_especifico">
						</td>
						<td>
							<input type="number" step="any" name="peso_cantidad_total_contrato" id="peso_cantidad_total_contrato"  placeholder="Ej: 417.26">
						</td>
						<td>
							<select name="unidad_cantidad_total_contrato">
								<option value="Qq">Qq</option>
								<option value="Lb">Lb</option>
								<option value="Kg">Kg</option>
								<option value="unidad">unidad</option>
							</select>
						</td>
						<td>
							<input type="number" step="any" name="peso_total_reglamento" id="" placeholder="Ej: 417.26">
						</td>
						<td>
							<select name="unidad_peso_total_reglamento">
								<option value="Lb">Lb</option>
								<option value="Kg">Kg</option>
								<option value="unidad">unidad</option>
							</select>
						</td>
						<td>
							<input type="text" name="precio_precio_total_unitario" id="precio_total_unitario" placeholder="precio"  value="0"  >
						</td>
						<td>
							<input type="text" name="unidad_precio_total_unitario" id="" placeholder="unidad_medida">
						</td>
						<td>
							<input type="number" step="any" name="precio_precio_sustentable" id="precio_sustentable_minimo"  value="0" placeholder="Ej: 160">
						</td>
						<td>
							<input type="text" name="unidad_precio_sustentable" id="" placeholder="unidad_medida">
						</td>
						<td>
							<input type="number" step="any" name="precio_reconocimiento_organico" id="precio_reconocimiento_organico"  value="0" placeholder="Ej: 40">
						</td>
						<td>
							<input type="text" name="unidad_reconocimiento_organico" id="" placeholder="unidad_medida">
						</td>
						<td>
							<input type="number" step="any" name="precio_incentivo_spp" id="precio_incentivo_spp"  value="0" placeholder="Ej: 20">
						</td>
						<td>
							<input type="text" name="unidad_incentivo_spp" id="" placeholder="unidad_medida">
						</td>
						<td>
							<input type="text"  name="valor_total_contrato" id="valor_total_contrato"  value="0.0"  placeholder="valor_total">
						</td>
					</tr>
		 			<tr>
		 				<td colspan="6"><button class="btn btn-sm btn-primary" type="submit" style="width:100%" name="agregar_formato" value="1"><span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Guardar Registro</button></td>
		 			</tr>
		 		</tbody>
		 	</table>
		</form>	
	</div>
</div>
<script>
var contador=0;
	/*function tablaInforme()
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

			//INICIA cantidad_total_contrato CANTIDAD TOTAL CONFORME A CONTRATO
				cell11.innerHTML = '<input type="number" step="any" name="peso_cantidad_total_contrato['+contador+']" id="peso_cantidad_total_contrato"  placeholder="Ej: 417.26">';
				//cell12.innerHTML = '<input type="text" name="unidad_cantidad_total_contrato['+contador+']" id="" placeholder="unidad">';

				cell12.innerHTML = '<select name="unidad_cantidad_total_contrato['+contador+']">'
				+'<option value="Qq">Qq</option>'
				+'<option value="Lb">Lb</option>'
				+'<option value="Kg">Kg</option>'
				+'<option value="unidad">unidad</option>'
				+'</select>';
			//TERMINA cantidad_total_contrato CANTIDAD TOTAL CONFORME A CONTRATO

			//INICIA peso_total_reglamento
				cell13.innerHTML = '<input type="number" step="any" name="peso_total_reglamento['+contador+']" id="" placeholder="Ej: 417.26">';

				//cell14.innerHTML = '<input type="text" name="unidad_peso_total_reglamento['+contador+']" id="" placeholder="medida">';
				cell14.innerHTML = '<select name="unidad_peso_total_reglamento['+contador+']">'
				+ '<option value="Lb">Lb</option>'
				+ '<option value="Kg">Kg</option>'
				+ '<option value="unidad">unidad</option>'
				+ '</select>';

			//TERMINA peso_total_reglamento

			//INICIA precio_total_unitario
				cell15.innerHTML = '<input type="text" name="precio_precio_total_unitario['+contador+']" id="precio_total_unitario" placeholder="precio"  value="0"  >';


				cell16.innerHTML = '<input type="text" name="unidad_precio_total_unitario['+contador+']" id="" placeholder="unidad_medida">';
			// TERMINA precio_total_unitario

			//INICIA PRECIO SUSTENTABLE MINIMO precio_sustentable
				cell17.innerHTML = '<input type="number" step="any" name="precio_precio_sustentable['+contador+']" id="precio_sustentable_minimo"  value="0" placeholder="Ej: 160">';

				cell18.innerHTML = '<input type="text" name="unidad_precio_sustentable['+contador+']" id="" placeholder="unidad_medida">';
			// TERMINA PRECIO SUSTENTABLE MINIMO precio_sustentable

			// INICIA RECONOCIMIENTO ORGANICO reconocimiento_organico
				cell19.innerHTML = '<input type="number" step="any" name="precio_reconocimiento_organico['+contador+']" id="precio_reconocimiento_organico"  value="0" placeholder="Ej: 40">';

				cell20.innerHTML = '<input type="text" name="unidad_reconocimiento_organico['+contador+']" id="" placeholder="unidad_medida">';
			// TERMINA RECONOCIMIENTO ORGANICO reconocimiento_organico

			//INICIA incentivo_spp
				cell21.innerHTML = '<input type="number" step="any" name="precio_incentivo_spp['+contador+']" id="precio_incentivo_spp" onChange="calcular();" value="0" placeholder="Ej: 20">';

				cell22.innerHTML = '<input type="text" name="unidad_incentivo_spp['+contador+']" id="" placeholder="unidad_medida">';
			// TERMINA incentivo_spp

			// VALOR TOTAL CONTRATO
			cell23.innerHTML = '<input type="text"  name="valor_total_contrato['+contador+']" id="valor_total_contrato" onChange="calcular();" value="0.0"  placeholder="valor_total">';

			//INICIA cuota_uso_reglamento
				cell24.innerHTML = '<input type="text" style="background-color:#27ae60;color:#ecf0f1" name="cuota_uso_reglamento['+contador+']"  id="cuota_uso_reglamento" onChange="calcular();" value="0" placeholder="cuota">';

				cell25.innerHTML = '<input type="text" name="unidad_cuota_uso_reglamento['+contador+']" id="" placeholder="unidad">';
			//TERMINA cuota_uso_reglamento

			//TOTAL A PAGAR
			cell26.innerHTML = '<input type="text"  name="total['+contador+']" id="resultado_total" onChange="calcular();" value="0.0"  placeholder="total">';

		}
	}*/

/*	function calcular(){
		precio_sustentable_minimo = document.getElementById("precio_sustentable_minimo").value;
		precio_reconocimiento_organico = document.getElementById("precio_reconocimiento_organico").value;
		precio_incentivo_spp = document.getElementById("precio_incentivo_spp").value;
		precio_total_unitario = document.getElementById("precio_total_unitario").value;
		peso_cantidad_total_contrato = document.getElementById("peso_cantidad_total_contrato").value;

		cuota_uso_reglamento = document.getElementById("cuota_uso_reglamento").value;

		//calculamos el precio total unitario
		precio_total_unitario = parseFloat(precio_sustentable_minimo) + parseFloat(precio_reconocimiento_organico) + parseFloat(precio_incentivo_spp);

		document.getElementById("precio_total_unitario").value = precio_total_unitario;

		//calculamos el valor total del contrato
		valor_total_contrato = parseFloat(precio_total_unitario) * parseFloat(peso_cantidad_total_contrato);
		valor_total_contrato_redondeado = parseFloat(valor_total_contrato.toFixed(2));
		/* se redondea el resultado a 2 decimales */
		//valor_total_contrato = parseFloat(Math.round((precio_total_unitario * peso_cantidad_total_contrato) * 100) / 100).toFixed(2);
	/*	document.getElementById("valor_total_contrato").value = valor_total_contrato_redondeado; 

		//calculamos el total a pagar


		if(isNaN(cuota_uso_reglamento)){ // revisamos si es porcentaje
			//alert("ES PORCENTAJE : "+cuota_uso_reglamento);
			total_final = parseFloat(valor_total_contrato_redondeado) * (0.01);
		}else{	//si es solo numero
			//alert("ES NUMERO: "+cuota_uso_reglamento);
			total_final = parseFloat(peso_cantidad_total_contrato) * parseFloat(cuota_uso_reglamento);
		}
		

		document.getElementById("resultado_total").value = total_final;

		/*precio_total_unitario = document.getElementById("precio_total_unitario").value;
		cantidad_total_contrato = document.getElementById("cantidad_total_contrato").value;

		precio_sustentable_minimo = document.getElementById("precio_sustentable_minimo").value;
		precio_reconocimiento_organico = document.getElementById("precio_reconocimiento_organico").value;
		precio_incentivo_spp = document.getElementById("precio_incentivo_spp").value;

		precio_total_unitario = parseFloat(precio_sustentable_minimo)+parseFloat(precio_reconocimiento_organico)+parseFloat(precio_incentivo_spp);*/

		/*document.getElementById("precio_total_unitario").value = precio_total_unitario;

		//calculamos el valor total del contrato
		valor_total_contrato = parseFloat(precio_total_unitario)*parseFloat(cantidad_total_contrato);
		document.getElementById("valor_total_contrato").value = valor_total_contrato;*/
	//}


</script>