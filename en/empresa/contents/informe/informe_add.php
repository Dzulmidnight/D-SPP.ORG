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
$tipo_de_empresa = $empresa['comprador'];
$idempresa = $_SESSION['idempresa'];
$idtrim = $_GET['idtrim'];
$anio_actual = date('Y',time());
$row_configuracion = mysql_query("SELECT * FROM porcentaje_ajuste WHERE anio = $anio_actual", $dspp) or die(mysql_error());
$configuracion = mysql_fetch_assoc($row_configuracion);


if(isset($_POST['eliminar_registro']) && $_POST['eliminar_registro'] != 0){
	$idregistro = $_POST['eliminar_registro'];

	$deleteSQL = sprintf("DELETE FROM formato_compras WHERE idformato_compras = $idregistro",
		GetSQLValueString($idregistro, "int"));
	$eliminar = mysql_query($deleteSQL, $dspp) or die(mysql_error());
}

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
		if(isset($_POST['valor_cuota'])){ /// antes total_a_pagar
			$valor_cuota = $_POST['valor_cuota'];
		}else{
			$valor_cuota = 0;
		}
		if(isset($_POST['producto_terminado'])){
			$producto_terminado = $_POST['producto_terminado'];
		}else{
			$producto_terminado = NULL;
		}
		if($producto_terminado == 'SI'){
			if(isset($_POST['valor_ingredientes'])){
				$valor_ingredientes = $_POST['valor_ingredientes'];
			}else{
				$valor_ingredientes = NULL;
			}
			$porcentaje_valor_ingredientes = ($valor_ingredientes * 0.25);

			if(isset($_POST['se_exporta'])){
				$se_exporta = $_POST['se_exporta'];

				if($se_exporta == 'DIRECTAMENTE'){
					$valor_final_ingredientes = ($porcentaje_valor_ingredientes * 0.75);
				}else if($se_exporta == 'INTERMEDIARIO'){
					$valor_final_ingredientes = ($porcentaje_valor_ingredientes * 0.25);
				}
			}else{
				$se_exporta = NULL;
				$valor_final_ingredientes = 0;
			}

		}else{
			$se_exporta = NULL;
			$valor_ingredientes = NULL;
		}

		$total_a_pagar = $valor_cuota + $valor_final_ingredientes;

		//Iniciamos insertar formato_compras
			$insertSQL = sprintf("INSERT INTO formato_compras(idtrim, idempresa, spp, opp, pais, fecha_facturacion, primer_intermediario, segundo_intermediario, clave_contrato, fecha_contrato, producto_general, producto_especifico, producto_terminado, se_exporta, valor_ingredientes, valor_final_ingredientes, unidad_cantidad_factura, cantidad_total_factura, precio_sustentable_minimo, reconocimiento_organico, incentivo_spp, otros_premios, precio_total_unitario, valor_total_contrato, cuota_uso_reglamento, valor_cuota, total_a_pagar, fecha_registro) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
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
				GetSQLValueString($producto_terminado, "text"),
				GetSQLValueString($se_exporta, "text"),
				GetSQLValueString($valor_ingredientes, "text"),
				GetSQLValueString($valor_final_ingredientes, "text"),
				GetSQLValueString($unidad_cantidad_total_factura, "text"),
				GetSQLValueString($cantidad_total_factura, "text"),
				GetSQLValueString($precio_sustentable_minimo, "text"),
				GetSQLValueString($reconocimiento_organico, "text"),
				GetSQLValueString($incentivo_spp, "text"),
				GetSQLValueString($otros_premios, "text"),
				GetSQLValueString($precio_total_unitario, "text"),
				GetSQLValueString($valor_total_contrato, "text"),
				GetSQLValueString($cuota_uso_reglamento, "text"),
				GetSQLValueString($valor_cuota, "text"),
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
		echo "<p class='alert alert-danger'><span class='glyphicon glyphicon-ban-circle' aria-hidden='true'></span> No more records can be added to the <b>quarterly format</b> since it was completed.</p>";
	}else if($trim[$txt_estatus] == 'EN ESPERA'){
		echo "<p class='alert alert-danger'><span class='glyphicon glyphicon-ban-circle' aria-hidden='true'></span> No more records can be added to the <b>quarterly format</b>, as it is being reviewed.</p>";
	}else if($trim[$txt_estatus] == 'APROBADO'){
		echo "<p class='alert alert-danger'><span class='glyphicon glyphicon-ban-circle' aria-hidden='true'></span> No more records can be added to the <b>quarterly format</b>, since it is under review.</p>";
	}else{
	?>

		<p class="alert alert-info" style="margin-bottom:0px;padding:5px;"><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> Fields marked in blue are optional, this information will be useful for the evaluation of certification.</p>
		<p class="alert alert-success" style="margin-bottom:0px;padding:5px;"><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> The fields marked in green are mandatory.</p>
		<p class="alert alert-warning" style="padding:5px;"><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> The fields marked in yellow are completed automatically.</p>

		 	<table class="table table-bordered table-condensed" style="font-size:11px;" id="tablaInforme">
		 		<thead>
		 			<tr class="success">
		 				<th class="text-center">
							#
		 				</th>
						<th class="text-center">#SPP</th>
						<th class="text-center">Provider SPO Name</th>
						<th class="text-center">Provider SPO country</th>
						<th class="text-center">Billing Date</th>
						<th class="text-center">First Intermediate</th>
						<th class="text-center">Second Intermediate</th>
						<th colspan="2" class="text-center">Reference Original Contract with SPO</th>
						<th class="text-center">General Product</th>
						<th class="text-center">Specific Product</th>
						<?php 
						if($tipo_de_empresa){
						?>
							<th colspan="3" class="text-center">
								Finished product
							</th>

						<?php
						}
						 ?>
						<th colspan="2" class="text-center">Total Amount in line with Contract</th>
						<th class="text-center">Minimum Sustainable Price</th>
						<th class="text-center">Organic Recognition</th>
						<th class="text-center">SPP incentive</th>
						<th class="text-center">Other prizes</th>
						<th class="text-center">Total Unit Price</th>
						<th class="text-center">Total Contract Value</th>
						<th class="text-center">User's Fee, in line with Regulations </th>
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

		 				<!-- INICIA #SPP -->
		 				<td>
		 					SPP identification code
		 				</td>
		 				<!-- TERMINA #SPP -->
						
						<!-- INICIA NOMBRE DE LA OPP -->
		 				<td>
		 					Name of the SPO where the product comes from.
		 				</td>
		 				<!-- TERMINA NOMBRE DE A OPP

		 				<!-- INICIA PAIS DE LA OPP -->
		 				<td>
		 					Country where the SPO originates the products
		 				</td>
		 				<!-- TERMINA PAIS DE LA OPP -->

		 				<!-- INICIA FECHA DE LA FACTURACION -->
		 				<td>
		 					The date of the purchase of the product by the Final Purchaser, SPP user (from the OPP directly or through an intermediary).
		 				</td>
		 				<!-- TERMINA FECHA DE LA FACTURACION -->

		 				<!-- INICIA PRIMER INTERMEDIARIO -->
		 				<td>
		 					Name of the commercial counterpart who makes the purchase of the product to the SPO, if not the Final Purchaser. If it is the Final Buyer put the script
		 				</td>
		 				<!-- TERMINA PRIMER INTERMEDIARIO -->

		 				<!-- INICIA SEGUNDO INTERMEDIARIO -->
		 				<td>
		 					Name of the commercial counterpart who makes the purchase of the product to the First Intermediary, if not the Final Purchaser. If it does not exist or is the Final Buyer, put a hyphen.
		 				</td>
		 				<!-- TERMINA SEGUNDO INTERMEDIARIO -->

		 				<!-- INICIA REFERENCIA CONTRATO -->
			 				<td>
			 					Number or key of the Original Contract, ie the contract of sale of the OPP to the Buyer or First Intermediary.
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
		 				<?php 
		 				if($tipo_de_empresa){
		 				?>
			 				<td colspan="3">
			 					Finished product?
			 				</td>
		 				<?php
		 				}
		 				 ?>
		 				<!-- INICIA CANTIDAD TOTAL CONFORME FACTURA -->
			 				<td>
			 					Unit of measure used (kg, to, lb, qq, etc.)
			 				</td>
			 				<td>
			 					Total quantity of product in final unit of measure indicated in the original contract
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
							$row_registro = mysql_query("SELECT formato_compras.* FROM formato_compras WHERE formato_compras.idtrim  = '$informe_general[$num_trim]'");
							$contador = 1;

							while($formato = mysql_fetch_assoc($row_registro)){
							?>
								<form action="" method="POST">
									<tr class="active">
										<td>
											<button type="submit" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete record" name="eliminar_registro" value="<?php echo $formato['idformato_compras']; ?>" onclick="return confirm('Are you sure to delete the record?');"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
											<?php echo $contador; ?>
										</td>
										<td><?php echo $formato['spp']; ?></td>
										<td><?php echo $formato['opp']; ?></td>
										<td><?php echo $formato['pais']; ?></td>
										<td><?php echo date('d/m/Y',$formato['fecha_facturacion']); ?></td>
										<td><?php echo $formato['primer_intermediario']; ?></td>
										<td><?php echo $formato['segundo_intermediario']; ?></td>
										<td><?php echo $formato['clave_contrato']; ?></td>
										<td>
											<?php
											if(isset($formato['fecha_contrato'])){
												echo date('d/m/Y',$formato['fecha_contrato']); 
											}
											?>
										</td>
										<td><?php echo $formato['producto_general']; ?></td>
										<td><?php echo $formato['producto_especifico']; ?></td>
										<?php 
										if($tipo_de_empresa){
										?>
											<td><?php echo $formato['producto_terminado']; ?></td>
											<td><?php echo 'Value ingredients: <span style="color:red">'.$formato['valor_ingredientes']."</span>"; ?></td>
											<td><?php echo 'It export: <span style="color:red">'.$formato['se_exporta'].'</span>'; ?></td>
										<?php
										}
										 ?>
										<td><?php echo $formato['unidad_cantidad_factura']; ?></td>
										<td><?php echo number_format($formato['cantidad_total_factura'],2); ?></td>
										<td><?php echo $formato['precio_sustentable_minimo']; ?></td>
										<td><?php echo $formato['reconocimiento_organico']; ?></td>
										<td><?php echo $formato['incentivo_spp']; ?></td>
										<td><?php echo $formato['otros_premios']; ?></td>
										<td><?php echo $formato['precio_total_unitario']; ?></td>
										<td><?php echo number_format($formato['valor_total_contrato'],2); ?></td>
										<td><?php echo $formato['cuota_uso_reglamento']; ?></td>
										<td style="background-color:#e74c3c;color:#ecf0f1;"><?php echo number_format($formato['total_a_pagar'],2); ?></td>
									</tr>
								</form>
							<?php
							$contador++;
							}
		 				 ?>
		<form class="form-horizontal" method="POST">
					<tr class="success">
						<td class="warning"></td> <!-- # -->

						<td>
							<select name="spp" id="spp" onChange="buscar();" required>
								<option>List of organizations</option>
								<?php 
								$row_opp = mysql_query("SELECT spp, abreviacion FROM opp ORDER BY spp", $dspp) or die(mysql_error());
								$contador = 1;
								while($opp = mysql_fetch_assoc($row_opp)){
									echo "<option value='".$opp['spp']."'>".$opp['spp']." | ".mayus($opp['abreviacion'])."</option>";
									$contador++;
								}
								 ?>
							</select>													
						</td>

						<td class="warning"><!-- nombre de la opp -->
							<textarea id="nombre_opp" name="nombre_opp" value="" placeholder="Name of SPO"></textarea>
						</td>

						<td class="warning">
			              <input id="pais" name="pais" value="" placeholder="pais">
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
						<?php 
						if($tipo_de_empresa){
						?>
							<td>
								Is it a finished product?
								<label class="radio-inline">
								  <input type="radio" name="producto_terminado" id="inlineRadio1" value="SI" onchange="mostrar()"> YES
								</label>
								<br>
								<label class="radio-inline">
								  <input type="radio" name="producto_terminado" id="inlineRadio2" value="NO" onchange="ocultar()"> NO
								</label>
							</td>
							<td style="border-left-style:hidden;">
								<div id="div_multingrediente" style="display:none;background-color:#e74c3c;color:#ecf0f1;padding:10px;">
									Total value of the ingredients
									<input style="color:black" type="number" step="any" name="valor_ingredientes" placeholder="Value">
								</div>
							</td>
							<td style="border-left-style:hidden;">
								<div id="div_oculto" style="display:none;background-color:#e74c3c;color:#ecf0f1;padding:10px;">
									Purchased directly from the organization or through an intermediary
									<label class="radio-inline">
									  <input type="radio" name="se_exporta" id="" value="DIRECTAMENTE"> Directly
									</label>
									<br>
									<label class="radio-inline">
									  <input type="radio" name="se_exporta" id="" value="INTERMEDIARIO"> Through an intermediary
									</label>
								</div>	
						<?php
						}
						 ?>
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
							<input type="number" step="any" name="incentivo_spp" id="" placeholder="amount paid">
						</td>

						<td class="info"><!-- otros premios -->
							<input type="number" step="any" name="otros_premios" id="" placeholder="amount paid">
						</td>

						<td class="success"><!-- precio total unitario pagado -->
							<input type="number" step="any" id="precio_total_unitario" name="precio_total_unitario" id="precio_total_unitario" onChange="calcular();" value="0" placeholder="Ej: 40" required>
						</td>

						<td class="warning"><!-- valor total contrato -->
							<input type="text" id="valor_total_contrato" name="valor_total_contrato" id="" placeholder="Total Contract Value" readonly>
						</td>

						<td class="warning"><!-- cuota de uso reglamento -->
							<input type="text" id="cuota_uso_reglamento" name="cuota_uso_reglamento" id="cuota_uso_reglamento" value="0" placeholder="Usage fee" readonly>
						</td>

						<td class="warning"><!-- total a pagar -->
							<input type="text" name="valor_cuota" id="valor_cuota" placeholder="Total to pay" readonly>
						</td>

					</tr>
		 			<tr>
		 				<?php 
		 				if($tipo_de_empresa){
		 				?>
		 					<td colspan="6"><button class="btn btn-primary" type="submit" style="width:100%" name="agregar_formato" value="1" onclick="return validar()">Save Record</button></td>
		 				<?php
		 				}else{
		 				?>
		 					<td colspan="6"><button class="btn btn-primary" type="submit" style="width:100%" name="agregar_formato" value="1">Save Record</button></td>
		 				<?php
		 				}
		 				 ?>
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
    if(valor_campo == 'SI'){
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
		document.getElementById('div_multingrediente').style.display = 'block';
	}
	function ocultar()
	{
		document.getElementById('div_oculto').style.display = 'none';
		document.getElementById('div_multingrediente').style.display = 'none';
	}
</script>


<script>
$(document).ready(function() {
//    $("#resultadoBusqueda").val('<p>CAMPO VACIO</p>');
    $("#nombre_opp").val('Name of SPO');
//    $("#resultadoBusqueda").val('<p>CAMPO VACIO</p>');
    $("#pais").val('Country of the SPO');
});

function buscar() {
    var textoBusqueda = $("select#spp").val();
 
     if (textoBusqueda != "") {
        $.post("../../nombre_ajax.php", {valorBusqueda: textoBusqueda}, function(nombre_opp) {
            $("#nombre_opp").val(nombre_opp);
         }); 
     } else { 
        $("#nombre_opp").val('Name of the SPO');
     };

     if (textoBusqueda != "") {
        $.post("../../pais_ajax.php", {valorBusqueda: textoBusqueda}, function(nombre_pais) {
            $("#pais").val(nombre_pais);
         }); 
     } else { 
        $("#pais").val('SPO Country');
     };

};
</script>

<script>
function ponerMayusculas(nombre) 
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
		valor_cuota = (valor_total_contrato * cuota_fija_anual) / 100;

		total_redondeado = parseFloat(valor_cuota.toFixed(2));

		//calculamos el valor total del contrato

		//21_07_2017 valor_total_contrato_redondeado = parseFloat(valor_total_contrato.toFixed(2));
		/* se redondea el resultado a 2 decimales */
		//valor_total_contrato = parseFloat(Math.round((precio_total_unitario * peso_cantidad_total_contrato) * 100) / 100).toFixed(2);
		document.getElementById("valor_total_contrato").value = total_contrato_redondeado; 
		document.getElementById("cuota_uso_reglamento").value = "<?php echo $configuracion['cuota_compradores']; ?> %"; 
		document.getElementById("valor_cuota").value = total_redondeado; 

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