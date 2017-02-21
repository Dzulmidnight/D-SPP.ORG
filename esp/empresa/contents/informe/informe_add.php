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
	
	$fecha_elaboracion = time();


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


			$fecha_compra = strtotime($fecha_compra);
			//Iniciamos insertar formato_compras
				$insertSQL = sprintf("INSERT INTO formato_compras(idtrim, idempresa, opp, pais, fecha_compra, producto_general, producto_especifico, valor_total_contrato, total, fecha_elaboracion) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
					GetSQLValueString($idtrim, "text"),
					GetSQLValueString($idempresa, "int"),
					GetSQLValueString($opp, "text"),
					GetSQLValueString($pais, "text"),
					GetSQLValueString($fecha_compra, "int"),
					GetSQLValueString($producto_general, "text"),
					GetSQLValueString($producto_especifico, "text"),
					GetSQLValueString($valor_total_contrato, "double"),
					GetSQLValueString($total, "double"),
					GetSQLValueString($fecha_elaboracion, "int"));
				$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

				$idformato_compras = mysql_insert_id($dspp);

				//inciamos insertar intermediarios
				$insertSQL = sprintf("INSERT INTO intermediarios(idformato_compras, primero, segundo) VALUES(%s, %s, %s)",
					GetSQLValueString($idformato_compras, "int"),
					GetSQLValueString($primer_intermediario, "text"),
					GetSQLValueString($segundo_intermediario, "text"));
				$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
				//terminamos insertar intermediarios

				//inciamos insertar referencia_contrato
				$fecha_contrato = strtotime($fecha_contrato);
				$insertSQL = sprintf("INSERT INTO referencia_contrato(clave, fecha, idformato_compras) VALUES (%s, %s, %s)",
					GetSQLValueString($clave_contrato, "text"),
					GetSQLValueString($fecha_contrato, "in"),
					GetSQLValueString($idformato_compras, "int"));
				$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
				//terminamos insertar referencia_contrato

				//iniciamos insertar cantidad_total_contrato
				$insertSQL = sprintf("INSERT INTO cantidad_total_contrato(peso, unidad, idformato_compras) VALUES(%s, %s, %s)",
					GetSQLValueString($peso_cantidad_total_contrato, "double"),
					GetSQLValueString($unidad_cantidad_total_contrato, "text"),
					GetSQLValueString($idformato_compras, "int"));
				$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
				//terminamos insertar cantidad_total_contrato

				//iniciamos insertar peso_total_reglamento
				$insertSQL = sprintf("INSERT INTO peso_total_reglamento(peso, unidad, idformato_compras) VALUES (%s, %s, %s)",
					GetSQLValueString($peso_total_reglamento, "double"),
					GetSQLValueString($unidad_peso_total_reglamento, "text"),
					GetSQLValueString($idformato_compras, "int"));
				$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
				//terminamos insertar peso_total_reglamento

				//iniciamos insertar precio_total_unitario
				$insertSQL = sprintf("INSERT INTO precio_total_unitario(precio, unidad, idformato_compras) VALUES (%s, %s, %s)",
					GetSQLValueString($precio_precio_total_unitario, "double"),
					GetSQLValueString($unidad_precio_total_unitario, "text"),
					GetSQLValueString($idformato_compras, "int"));
				$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
				//terminamos insertar precio_total_unitario

				//inciamos insertar precio_sustentable
				$insertSQL = sprintf("INSERT INTO precio_sustentable(precio, unidad, idformato_compras) VALUES (%s, %s, %s)",
					GetSQLValueString($precio_precio_sustentable, "double"),
					GetSQLValueString($unidad_precio_sustentable, "text"),
					GetSQLValueString($idformato_compras, "int"));
				$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
				//termianos insertar precio_sustentable

				//iniciamos insertar reconocimiento_organico
				$insertSQL = sprintf("INSERT INTO reconocimiento_organico(precio, unidad, idformato_compras) VALUES (%s, %s, %s)",
					GetSQLValueString($precio_reconocimiento_organico, "double"),
					GetSQLValueString($unidad_reconocimiento_organico, "text"),
					GetSQLValueString($idformato_compras, "text"));
				$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
				//terminamos insertar reconocimiento_organico

				//iniciamos insertar incentivo_spp
				$insertSQL = sprintf("INSERT INTO incentivo_spp(precio, unidad, idformato_compras) VALUES (%s, %s, %s)",
					GetSQLValueString($precio_incentivo_spp, "double"),
					GetSQLValueString($unidad_incentivo_spp, "text"), 
					GetSQLValueString($idformato_compras, "int"));
				$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
				//terminamos insertar incetivo_spp

				//iniciamos insertar cuota_uso_reglamento
				$insertSQL = sprintf("INSERT INTO cuota_uso_reglamento(cuota, unidad, idformato_compras) VALUES (%s, %s, %s)",
					GetSQLValueString($cuota_uso_reglamento, "text"),
					GetSQLValueString($unidad_cuota_uso_reglamento, "text"),
					GetSQLValueString($idformato_compras, "int"));
				$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
				//terminamos insertar cuota_uso_reglamento
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


		<p class="alert alert-info" style="padding:7px;margin-bottom:0px;"><strong>Agregar Registro al Trimestre <?php echo $idtrim; ?></strong></p>
	
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
							$row_registro = mysql_query("SELECT formato_compras.*, precio_minimo.precio AS 'pm_precio', precio_minimo.unidad AS 'pm_unidad', precio_total_unitario.precio AS 'ptu_precio', precio_total_unitario.unidad AS 'ptu_unidad', incentivo_spp.precio AS 'incentivo_precio', incentivo_spp.unidad AS 'incentivo_unidad', reconocimiento_organico.precio AS 'ro_precio', reconocimiento_organico.unidad AS 'ro_unidad', otros_premios.precio AS 'otros_precio', otros_premios.unidad AS 'otros_unidad' FROM formato_compras INNER JOIN precio_minimo ON formato_compras.idformato_compras = precio_minimo.idformato_compras INNER JOIN precio_total_unitario ON formato_compras.idformato_compras = precio_total_unitario.idformato_compras INNER JOIN incentivo_spp ON formato_compras.idformato_compras = incentivo_spp.idformato_compras INNER JOIN reconocimiento_organico ON formato_compras.idformato_compras = reconocimiento_organico.idformato_compras INNER JOIN otros_premios ON formato_compras.idformato_compras = otros_premios.idformato_compras WHERE formato_compras.idtrim  = '$informe_general[$num_trim]'");
							$contador = 1;

							while($informacion_formato = mysql_fetch_assoc($row_registro)){
							?>
								<tr class="active">
									<td><?php echo $contador; ?></td>
									<td><?php echo $informacion_formato['opp']; ?></td>
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
									<td><?php echo $informacion_formato['valor_total_contrato']; ?></td>
									<td><?php echo $informacion_formato['cur_cuota']; ?></td>
									<td><?php echo $informacion_formato['cur_unidad']; ?></td>
									<td style="background-color:#e74c3c;color:#ecf0f1;"><?php echo $informacion_formato['total']; ?></td>
								</tr>
							<?php
							$contador++;
							}
		 				 ?>
					<tr class="success">
						<td class="warning"></td> <!-- # -->

						<td>
							<form action="" method="POST">
							<!--<input type="text" name="busqueda" id="busqueda" value="" placeholder="" maxlength="30" autocomplete="off" />-->
								<input type="text" name="spp" id="spp" value="" placeholder="#SPP" maxlength="30" autocomplete="off" onKeyUp="buscar();" required />
							</form>							
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
							* <input type="date" name="fecha_facturacion" id="" placeholder="dd/mm/yyyy" required>
						</td>

						<td class="info"><!-- primer intermediario -->
							<input type="text" name="primer_intermediario" id="" placeholder="primer intermediario">
						</td>

						<td class="info"><!-- segundo intermediario -->
							<input type="text" name="segundo_intermediario" id="" placeholder="segundo intermediario">
						</td>

						<td class="info"><!-- REFERENCIA CONTRATO ORIGINAL -->
							<input type="text" name="clave_contrato" id="" placeholder="clave del contrato"><!-- #clave del contrato -->
						</td>
						<td class="info">
							<input type="text" name="fecha_contrato" id="" placeholder="fecha del contrato original"><!-- #fecha del contrato -->
						</td><!-- REFERENCIA CONTRATO ORIGINAL -->

						<td><!-- producto general -->
							* <input type="text" name="producto_general" id="" placeholder="Ej: café, miel, azucar" required>
						</td>

						<td class="success"><!-- producto especifico -->
							* <input type="text" name="producto_especifico" id="" placeholder="Ej: café verde, miel de abeja, azucar refinada" required>
						</td>

						<td class="success"><!-- CANTIDAD TOTAL CONFORME FACTURA -->
							* <select name="unidad_cantidad_total_factura" required><!-- #unidad de medida -->
								<option value="Qq">Qq</option>
								<option value="Lb">Lb</option>
								<option value="Kg">Kg</option>
							</select>
						</td>
						<td class="success">
							* <input type="number" step="any" name="cantidad_total_factura" required><!-- #cantidad total -->
						</td><!-- CANTIDAD TOTAL CONFORME FACTURA -->

						<td class="info"><!-- precio sustentable minimo -->
							<input type="number" step="any" name="precio_sustentable" id="" placeholder="importe pagado">
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
							* <input type="number" step="any" name="precio_reconocimiento_organico" id="precio_reconocimiento_organico" onChange="calcular();" value="0" placeholder="Ej: 40">
						</td>

						<td class="warning"><!-- valor total contrato -->
							<input type="text" name="unidad_reconocimiento_organico" id="" placeholder="unidad_medida">
						</td>

						<td class="warning"><!-- cuota de uso reglamento -->
							<input type="number" step="any" name="precio_incentivo_spp" id="precio_incentivo_spp" onChange="calcular();" value="0" placeholder="Ej: 20">
						</td>

						<td class="warning"><!-- total a pagar -->
							<input type="text" name="unidad_incentivo_spp" id="" placeholder="unidad_medida">
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
        $("#nombre_opp").val('CAMPO VACIO');
     };

     if (textoBusqueda != "") {
        $.post("../../pais_ajax.php", {valorBusqueda: textoBusqueda}, function(nombre_pais) {
            $("#pais").val(nombre_pais);
         }); 
     } else { 
        $("#pais").val('CAMPO VACIO');
     };

};
</script>

<script>
var contador=0;
	function calcular(){
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
		document.getElementById("valor_total_contrato").value = valor_total_contrato_redondeado; 

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
	}


</script>