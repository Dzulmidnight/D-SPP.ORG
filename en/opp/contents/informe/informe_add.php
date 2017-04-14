<?php 
require_once('../Connections/dspp.php'); 
require_once('../Connections/mail.php');

mysql_select_db($database_dspp, $dspp);

if (!isset($_SESSION)) {
  session_start();
	
	$redireccion = "../index.php?OPP";

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

$idopp = $_SESSION['idopp'];
$idtrim = $_GET['idtrim'];
$anio_actual = date('Y',time());
$row_configuracion = mysql_query("SELECT * FROM porcentaje_ajuste WHERE anio = $anio_actual", $dspp) or die(mysql_error());
$configuracion = mysql_fetch_assoc($row_configuracion);

if(isset($_POST['agregar_formato']) && $_POST['agregar_formato'] == 1){
	
	$fecha_registro = time();
	$tipo_moneda = 'USD';

		if(isset($_POST['pais_opp'])){
			$pais_opp = $_POST['pais_opp'];
		}else{
			$pais_opp = NULL;
		}
		if(isset($_POST['spp'])){
			$spp = $_POST['spp'];
		}else{
			$spp = NULL;
		}
		if(isset($_POST['nombre_empresa'])){
			$nombre_empresa = $_POST['nombre_empresa'];
		}else{
			$nombre_empresa = NULL;
		}
		if(isset($_POST['pais_empresa'])){
			$pais_empresa = $_POST['pais_empresa'];
		}else{
			$pais_empresa = NULL;
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
		if(isset($_POST['producto_terminado'])){
			$producto_terminado = $_POST['producto_terminado'];
		}else{
			$producto_terminado = NULL;
		}
		if(isset($_POST['se_exporta'])){
			$se_exporta = $_POST['se_exporta'];
		}else{
			$se_exporta = NULL;
		}

		//Iniciamos insertar formato_ventas
			$insertSQL = sprintf("INSERT INTO formato_ventas(idtrim, idopp, pais_opp, spp, empresa, pais_empresa, fecha_facturacion, primer_intermediario, segundo_intermediario, clave_contrato, fecha_contrato, producto_general, producto_especifico, producto_terminado, se_exporta, unidad_cantidad_factura, cantidad_total_factura, precio_sustentable_minimo, reconocimiento_organico, incentivo_spp, otros_premios, precio_total_unitario, valor_total_contrato, cuota_uso_reglamento, total_a_pagar, fecha_registro) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
				GetSQLValueString($idtrim, "text"),
				GetSQLValueString($idopp, "int"),
				GetSQLValueString($pais_opp, "text"),
				GetSQLValueString($spp, "text"),
				GetSQLValueString($nombre_empresa, "text"),
				GetSQLValueString($pais_empresa, "text"),
				GetSQLValueString($fecha_facturacion, "int"),
				GetSQLValueString($primer_intermediario, "text"),
				GetSQLValueString($segundo_intermediario, "text"),
				GetSQLValueString($clave_contrato, "text"),
				GetSQLValueString($fecha_contrato, "text"),
				GetSQLValueString($producto_general, "text"),
				GetSQLValueString($producto_especifico, "text"),
				GetSQLValueString($producto_terminado, "text"),
				GetSQLValueString($se_exporta, "text"),
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
			//$idformato_ventas = mysql_insert_id($dspp);
		//Termina insertar formato compras
}

if(isset($_POST['eliminar_registro']) && $_POST['eliminar_registro'] != 0){
	$idregistro = $_POST['eliminar_registro'];

	$deleteSQL = sprintf("DELETE FROM formato_ventas WHERE idformato_ventas = $idregistro",
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
	$txt_trim = 'trim'.$_GET['trim'];
	$txt_idtrim = 'idtrim'.$_GET['trim'];
	$txt_estatus = 'estado_trim'.$_GET['trim'];
	$row_trim = mysql_query("SELECT * FROM $txt_trim WHERE $txt_idtrim = '$idtrim'", $dspp) or die(mysql_error());
	$trim = mysql_fetch_assoc($row_trim);
	?>
	<div class="col-md-12">

	<?php
	if($trim[$txt_estatus] == 'FINALIZADO'){
		echo "<p class='alert alert-danger'><span class='glyphicon glyphicon-ban-circle' aria-hidden='true'></span> No more records can be added to the <b>quarterly format</b> since it was completed.</p>";
	}else if($trim[$txt_estatus] == 'EN ESPERA'){
		echo "<p class='alert alert-danger'><span class='glyphicon glyphicon-ban-circle' aria-hidden='true'></span> No more records can be added to the <b>quarterly format</b>, as it is being reviewed.</p>";
	}else if($trim[$txt_estatus] == 'APROBADO'){
		echo "<p class='alert alert-danger'><span class='glyphicon glyphicon-ban-circle' aria-hidden='true'></span> No more records can be added to the <b>quarterly format</b>, since it is under review.</p>";
	}else{
	?>

		<!--<p class="alert alert-info" style="padding:7px;margin-bottom:0px;"><strong>Agregar Registro al Trimestre <?php echo $idtrim; ?></strong></p>-->
		<p class="alert alert-info" style="margin-bottom:0px;padding:5px;"><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> The fields marked in blue are optional, this information will be useful for the assessment of certification.</p>
		<p class="alert alert-success" style="margin-bottom:0px;padding:5px;"><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> The fields marked in green are mandatory.</p>
		<p class="alert alert-warning" style="padding:5px;"><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> The fields marked in yellow are completed automatically.</p>
	
		
		 	<table class="table table-bordered table-condensed" style="font-size:11px;" id="tablaInforme">
		 		<thead>
		 			<tr class="success">
		 				<th class="text-center">
							#
		 				</th>
		 				<th class="text-center">Country of the SPO</th>
						<th class="text-center"><span style="color:red">#SPP of the Final Buyer</span></th>
						<th class="text-center"><span style="color:red">Name of the buyer</span></th>
						<th class="text-center"><span style="color:red">Buyer's Country</span></th>
						<th class="text-center">Billing Date</th>
						<th class="text-center">First Intermediate</th>
						<th class="text-center">Second Intermediate</th>
						<th colspan="2" class="text-center">Reference Original Contract with SPO</th>
						<th class="text-center">General Product</th>
						<th class="text-center">Specific Product</th>

							<th colspan="2" class="text-center">
								Finished product
							</th>


						<th colspan="2" class="text-center">Total Amount in line with Contract</th>
						<th class="text-center">Minimum Sustainable Price</th>
						<th class="text-center">Organic Recognition</th>
						<th class="text-center">SPP incentive</th>
						<th class="text-center">Other prizes</th>
						<th class="text-center">Total Unit Price</th>
						<th class="text-center">Total Contract Value</th>
						<th class="text-center">User's Fee, in line with Regulations</th>
						<th class="text-center">Total due</th>
		 			</tr>
		 		</thead>
		 		<tbody>
		 			<tr>
		 				<!-- INICIA NUMERO CONSECUTIVO -->
		 				<td>
		 					Consecutive number
		 				</td>
		 				<!-- TERMINA NUMERO CONSECUTIVO -->

		 				<!-- INICIA PAIS DE LA OPP(definido por el sistema) -->
		 				<td>
		 					Defined by the system
		 				</td>
		 				<!-- TERMINA PAIS DE LA OPP(definido por el sistema) -->

		 				<!-- INICIA #SPP -->
		 				<td>
		 					SPP identification code
		 				</td>
		 				<!-- TERMINA #SPP -->
						
						<!-- INICIA NOMBRE DE LA OPP -->
		 				<td>
		 					Name of Final Buyer
		 				</td>
		 				<!-- TERMINA NOMBRE DE A OPP

		 				<!-- INICIA PAIS DE LA OPP -->
		 				<td>
		 					Host country of the Final buyer
		 				</td>
		 				<!-- TERMINA PAIS DE LA OPP -->

		 				<!-- INICIA FECHA DE LA FACTURACION -->
		 				<td>
		 					The date of the purchase of the product by the Final buyer, SPP user (from the SPO directly or through an intermediary).
		 				</td>
		 				<!-- TERMINA FECHA DE LA FACTURACION -->

		 				<!-- INICIA PRIMER INTERMEDIARIO -->
		 				<td>
		 					Name of the commercial counterpart who makes the purchase of the product to the OPP, if not the Final Purchaser. If it is the Final Buyer put the script.
		 				</td>
		 				<!-- TERMINA PRIMER INTERMEDIARIO -->

		 				<!-- INICIA SEGUNDO INTERMEDIARIO -->
		 				<td>
		 					Name of the commercial counterpart who makes the purchase of the product to the First Intermediary, if not the Final Purchaser. If it does not exist or is the Final Buyer, put a hyphen.
		 				</td>
		 				<!-- TERMINA SEGUNDO INTERMEDIARIO -->

		 				<!-- INICIA REFERENCIA CONTRATO -->
			 				<td>
			 					Number or key of the Original Contract, the contract of sale of the SPO to the Buyer or First Intermediary.
			 				</td>
			 				<td>
			 					Date of the Original Sale Agreement of the SPO.
			 				</td>
		 				<!-- TERMINA REFERENCIA CONTRATO -->

		 				<!-- INICIA PRODUCTO GENERAL -->
		 				<td>
		 					General product. Eg: Coffee, Honey, Platano, Sugar.
		 				</td>
		 				<!-- TERMINA PRODUCTO GENERAL -->

		 				<td>
		 					Specific product. Eg: Green Arabica coffee, Honey type A, Banana chips, Refined white sugar.
		 				</td>

			 				<td colspan="2">
			 					Finished product?
			 				</td>

		 				<!-- INICIA CANTIDAD TOTAL CONFORME FACTURA -->
			 				<td>
			 					Unit of measure used (kg, to, lb, qq, etc.)
			 				</td>
			 				<td>
			 					Total quantity of product in final unit of measure indicated in the original contract.
			 				</td>
		 				<!-- TERMINA CANTIDAD TOTAL CONFORME FACTURA -->

		 				<!-- INICIA PRECIO SUSTENTABLE MINIMO -->
		 				<td>
		 					Amount paid per unit (USD)
		 				</td>
						<!-- TERMINA PRECIO SUSTENTABLE MINIMO -->

		 				<!-- INICIA RECONOCIMIENTO ORGANICO -->
		 				<td>
		 					Amount paid per unit (USD)
		 				</td>
						<!-- TERMINA RECONOCIMIENTO ORGANICO -->

		 				<!-- INICIA INCENTIVO SPP -->
		 				<td>
		 					Amount paid per unit (USD)
		 				</td>
						<!-- TERMINA INCENTIVO SPP -->

		 				<!-- INICIA OTROS PREMIOS -->
		 				<td>
		 					Amount paid per unit (USD)
		 				</td>
						<!-- TERMINA OTROS PREMIOS -->

		 				<!-- INICIA PRECIO TOTAL UNITARIO PAGADO -->
		 				<td>
		 					Amount paid per unit (USD)
		 				</td>
						<!-- TERMINA PRECIO TOTAL UNITARIO PAGADO -->

		 				<!-- INICIA VALOR TOTAL CONTRATO -->
		 				<td>
		 					Total contract value (in foreign currency, according to SPS List of Sustainable Prices)
		 				</td>
						<!-- TERMINA VALOR TOTAL CONTRATO -->

		 				<!-- INICIA CUOTA DE USO REGLAMENTO -->
		 				<td>
		 					Unit of measurement in line with Regulations on Costs
		 				</td>
						<!-- TERMINA CUOTA DE USO REGLAMENTO -->

		 				<!-- INICIA TOTAL A PAGAR -->
		 				<td>
		 					Total to be paid to SPP GLOBAL in line with user's fee and batch volume
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
							$row_registro = mysql_query("SELECT formato_ventas.* FROM formato_ventas WHERE formato_ventas.idtrim  = '$informe_general[$num_trim]'");
							$contador = 1;

							while($formato = mysql_fetch_assoc($row_registro)){
							?>
								<form class="form-horizontal" method="POST">
									<tr class="active">
										<td>
											<button type="submit" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete record" name="eliminar_registro" value="<?php echo $formato['idformato_ventas']; ?>" onclick="return confirm('¿Are you sure to delete the record?');"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
											<?php echo $contador; ?>
</td>
									<td><?php echo $formato['pais_opp']; ?></td>
									<td><?php echo $formato['spp']; ?></td>
									<td><?php echo $formato['empresa']; ?></td>
									<td><?php echo $formato['pais_empresa']; ?></td>
									<td>
										<!--<input type="date" value="<?php echo date('Y-m-d',$formato['fecha_facturacion']); ?>">-->
										<?php echo date('d/m/Y',$formato['fecha_facturacion']); ?>
									</td>
									<td>
										<!--<input type="text" name="primer_intermediario" value="<?php echo $formato['primer_intermediario']; ?>">-->
										<?php echo $formato['primer_intermediario']; ?>
									</td>
									<td>
										<!--<input type="text" name="segundo_intermediario" value="<?php echo $formato['segundo_intermediario']; ?>">-->
										<?php echo $formato['segundo_intermediario']; ?>
									</td>
									<td>
										<!--<input type="text" name="clave_contrato" value="<?php echo $formato['clave_contrato']; ?>">-->
										<?php echo $formato['clave_contrato']; ?>
									</td>
									<td>
										<?php
										if(isset($formato['fecha_contrato'])){
										 	//echo "<input type='date' name='fecha_contrato' value='".date('Y-m-d', $formato['fecha_contrato'])."'>";
											echo date('d/m/Y',$formato['fecha_contrato']); 
										}
										?>
									</td>
									<td>
										<!--<input type="text" name="producto_general" value="<?php echo $formato['producto_general']; ?>">-->
										<?php echo $formato['producto_general']; ?>
									</td>
									<td>
										<!--<input type="text" name="producto_especifico" value="<?php echo $formato['producto_especifico']; ?>">-->
										<?php echo $formato['producto_especifico']; ?>
									</td>

										<td>

											<?php echo $formato['producto_terminado']; ?>
										</td>
										<td>
											<?php echo 'It is exported: <span style="color:red">'.$formato['se_exporta'].'</span>'; ?>
										</td>
						
									<td>
										<!--<input type="text" name="unidad_cantidad_factura" value="<?php echo $formato['cantidad_total_factura']; ?>">-->
										<?php echo $formato['unidad_cantidad_factura']; ?>
									</td>
									<td>
										<!--<input type="text" value="<?php echo $formato['cantidad_total_factura']; ?>">-->
										<?php echo $formato['cantidad_total_factura']; ?>
									</td>
									<td>
										<!--<input type="text" name="precio_sustentable_minimo" value="<?php echo $formato['precio_sustentable_minimo']; ?>">-->
										<?php echo $formato['precio_sustentable_minimo']; ?>
									</td>
									<td>
										<!--<input type="text" name="reconocimiento_organico" value="<?php echo $formato['reconocimiento_organico']; ?>">-->
										<?php echo $formato['reconocimiento_organico']; ?>
									</td>
									<td>
										<!--<input type="text" name="incentivo_spp" value="<?php echo $formato['incentivo_spp']; ?>">-->
										<?php echo $formato['incentivo_spp']; ?>
									</td>
									<td>
										<!--<input type="text" name="otros_premios" value="<?php echo $formato['otros_premios']; ?>">-->
										<?php echo $formato['otros_premios']; ?>
									</td>
									<td>
										<?php echo number_format($formato['precio_total_unitario'],2); ?>
									</td>
									<td><?php echo number_format($formato['valor_total_contrato'], 2); ?></td>
									<td><?php echo $formato['cuota_uso_reglamento']; ?></td>
									<td style="background-color:#e74c3c;color:#ecf0f1;"><?php echo number_format($formato['total_a_pagar'], 2); ?></td>
								</tr>
								</form>
							<?php
							$contador++;
							}
		 				 ?>
		<form class="form-horizontal" method="POST">
					<tr class="success">
						<td class="warning"></td> <!-- # -->
						<td><input type="text" name="pais_opp" value="<?php echo $opp['pais']; ?>" readonly></td>
						<td>
							<?php 
							$row_empresa = mysql_query("SELECT spp, nombre, abreviacion FROM empresa ORDER BY spp", $dspp) or die(mysql_error());
							 ?>
							 <select name="spp" id="spp" onChange="buscar();" required>
							 	<option>List of Companies</option>
							 	<?php 
							 	while($empresa = mysql_fetch_assoc($row_empresa)){
							 		echo "<option value='".$empresa['spp']."'>".$empresa['spp']." | ".mayus($empresa['abreviacion'])."</option>";
							 	}
							 	 ?>
							 </select>
													
						</td>


						<!--<td class="success"><!-- #SPP(codigo de identificación)-->
							<!--* <input type="text" name="spp" id="" placeholder="#SPP" autofocus required>
						</td>-->

						<td class="warning"><!-- nombre de la opp -->
							<textarea id="nombre_empresa" name="nombre_empresa" value="" placeholder="Name of the buyer"></textarea>

							<!--<input type="text" name="nombre_empresa" id="" placeholder="Nombre de la OPP">-->
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
			              <input id="pais_empresa" name="pais_empresa" value="" placeholder="Buyer's Country">
						</td>

						<td class="success"><!-- fecha de facturación -->
							<input type="date" name="fecha_facturacion" id="" placeholder="dd-mm-aaaa" required>
						</td>

						<td class="info"><!-- primer intermediario -->
							<input type="text" name="primer_intermediario" id="" placeholder="First intermediary" onBlur=" ponerMayusculas(this)">
						</td>

						<td class="info"><!-- segundo intermediario -->
							<input type="text" name="segundo_intermediario" id="" placeholder="Second intermediary" onBlur=" ponerMayusculas(this)">
						</td>

						<td class="info"><!-- REFERENCIA CONTRATO ORIGINAL -->
							<input type="text" name="clave_contrato" id="" placeholder="Contract key" onBlur=" ponerMayusculas(this)"><!-- #clave del contrato -->
						</td>
						<td class="info">
							<input type="date" name="fecha_contrato" id="" placeholder="dd-mm-aaaa"><!-- #fecha del contrato -->
						</td><!-- REFERENCIA CONTRATO ORIGINAL -->

						<td><!-- producto general -->
							<input type="text" name="producto_general" id="" placeholder="Eg: coffee, honey, sugar" onBlur=" ponerMayusculas(this)" required>
						</td>

						<td class="success"><!-- producto especifico -->
							<input type="text" name="producto_especifico" id="" placeholder="Eg: green coffee, honey, refined sugar" onBlur=" ponerMayusculas(this)" required>
						</td>

							<td>
								Is it a finished product?
								<label class="radio-inline">
								  <input type="radio" name="producto_terminado" id="inlineRadio1" value="YES" onchange="mostrar()"> YES
								</label>
								<br>
								<label class="radio-inline">
								  <input type="radio" name="producto_terminado" id="inlineRadio2" value="NO" onchange="ocultar()"> NO
								</label>
							</td>
							<td style="border-left-style:hidden;">
								<div id="div_oculto" style="display:none;background-color:#e74c3c;color:#ecf0f1;padding:10px;">
									Purchased directly from the organization or through an intermediary
									<label class="radio-inline">
									  <input type="radio" name="se_exporta" id="" value="DIRECTLY"> Directly
									</label>
									<br>
									<label class="radio-inline">
									  <input type="radio" name="se_exporta" id="" value="INTERMEDIARY"> Through an intermediary
									</label>
								</div>							

						</td>

						<td class="success"><!-- CANTIDAD TOTAL CONFORME FACTURA -->
							<select name="unidad_cantidad_total_factura" required><!-- #unidad de medida -->
								<option value="Qq">Qq</option>
								<option value="Lb">Lb</option>
								<option value="Kg">Kg</option>
								<option value="Unidad">Unity</option>
							</select>
						</td>

						<td class="success">
							<input type="number" step="any" id="cantidad_total_factura" name="cantidad_total_factura" onChange="calcular();" onBlur=" ponerMayusculas(this)" required><!-- #cantidad total -->
						</td><!-- CANTIDAD TOTAL CONFORME FACTURA -->

						<td class="info"><!-- precio sustentable minimo -->
							<input type="number" step="any" name="precio_sustentable_minimo" id="" placeholder="amount paid">
						</td>

						<td class="info"><!-- reconocimiento organico -->
							<input type="number" step="any" name="reconocimiento_organico" id="" placeholder="amount paid">
						</td>

						<td class="info"><!-- incentivo spp -->
							<input type="number" step="any" name="incentivo_spp" id="" placeholder="iamount paid">
						</td>

						<td class="info"><!-- otros premios -->
							<input type="number" step="any" name="otros_premios" id="" placeholder="amount paid">
						</td>

						<td class="success"><!-- precio total unitario pagado -->
							<input type="number" step="any" id="precio_total_unitario" name="precio_total_unitario" id="precio_total_unitario" onChange="calcular();" value="0" placeholder="Eg: 40" required>
						</td>

						<td class="warning"><!-- valor total contrato -->
							<input type="text" id="valor_total_contrato" name="valor_total_contrato" id="" placeholder="Total Contract Value" readonly>
						</td>

						<td class="warning"><!-- cuota de uso reglamento -->
							<input type="text" id="cuota_uso_reglamento" name="cuota_uso_reglamento" id="cuota_uso_reglamento" value="0" placeholder="Usage fee" readonly>
						</td>

						<td class="warning"><!-- total a pagar -->
							<input type="text" name="total_a_pagar" id="total_a_pagar" placeholder="Total to pay" readonly>
						</td>

					</tr>
		 			<tr>
		 				<td colspan="6"><button class="btn btn-primary" type="submit" style="width:100%" name="agregar_formato" value="1" onclick="return validar()">Guardar Registro</button></td>
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
	
  function validar(){

    producto_terminado = document.getElementsByName("producto_terminado");
    // INICIA SELECCION TIPO SOLICITUD
    var valor_campo = '';
    var seleccionado = false;
		var precio_total_unitario = document.getElementById('precio_total_unitario').value;

		if(precio_total_unitario <= 0){
			alert('The total unit price must be greater than 0');
			return false;
		}

    for(var i=0; i<producto_terminado.length; i++) {    
      if(producto_terminado[i].checked) {
      	valor_campo = producto_terminado[i].value;
        seleccionado = true;
        break;
      }
    }
    if(!seleccionado) {
      alert("You must select IF it is a finished product or NO");
      return false;
    }

    se_exporta = document.getElementsByName("se_exporta");
    if(valor_campo == 'YES'){
	    var pregunta = false;
	    for(var i=0; i<se_exporta.length; i++) {    
	      if(se_exporta[i].checked) {
	        pregunta = true;
	        break;
	      }
	    }
	    if(!pregunta) {
	      alert("You must answer if the product is exported \"Directly\" or \"through an intermediary\" ");
	      return false;
	    }
    }
    return true
  }

	function mostrar(){
		document.getElementById('div_oculto').style.display = 'block';
	}
	function ocultar()
	{
		document.getElementById('div_oculto').style.display = 'none';
	}
</script>


<script>
	$(document).ready(function() {
	//    $("#resultadoBusqueda").val('<p>CAMPO VACIO</p>');
	    $("#nombre_empresa").val('Name of Final Buyer');
	//    $("#resultadoBusqueda").val('<p>CAMPO VACIO</p>');
	    $("#pais_empresa").val('Country of Final Buyer');
	});

	function buscar() {
	    var textoBusqueda = $("select#spp").val();
	 
	     if (textoBusqueda != "") {
	        $.post("../../nombre_empresa_ajax.php", {valorBusqueda: textoBusqueda}, function(nombre_empresa) {
	            $("#nombre_empresa").val(nombre_empresa);
	         }); 
	     } else { 
	        $("#nombre_empresa").val('Name of Final Buyer');
	     };

	     if (textoBusqueda != "") {
	        $.post("../../pais_empresa_ajax.php", {valorBusqueda: textoBusqueda}, function(nombre_pais) {
	            $("#pais_empresa").val(nombre_pais);
	         }); 
	     } else { 
	        $("#pais_empresa").val('PCountry of Final Buyer');
	     };

	};
</script>

<script>
function ponerMayusculas(nombre) 
{ 
nombre.value=nombre.value.toUpperCase(); 
} 
var contador=0;
var cuota_fija_anual = <?php echo $configuracion['cuota_productores']; ?>;
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
		document.getElementById("valor_total_contrato").value = total_contrato_redondeado; 
		document.getElementById("cuota_uso_reglamento").value = "<?php echo $configuracion['cuota_productores']; ?> %"; 
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