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

if(isset($_POST['agregar_registro']) && $_POST['agregar_registro'] == 1){
	$pais = $_POST['pais'];
	$ventas_totales = $_POST['ventas_totales'];
	$tipo_moneda = strtoupper($_POST['tipo_moneda']);

	$insertSQL = sprintf("INSERT INTO trim_productos_empresas(pais, ventas_totales, tipo_moneda) VALUES (%s, %s, %s)",
		GetSQLValueString($pais, "text"),
		GetSQLValueString($ventas_totales, "text"),
		GetSQLValueString($tipo_moneda, "text"));
	$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
}
?>

<div class="row">

	<div class="col-md-12">

		<form class="form-horizontal" method="POST">
		 	<table class="table table-bordered table-condensed" style="font-size:11px;" id="tablaInforme">
		 		<thead>
		 			<tr>
		 				<th colspan="4"><h4>VALOR DE VENTAS TRIMESTRALES PRODUCTOS TERMINADOS SPP POR PAÍS</h4></th>
		 			</tr>
		 		</thead>
		 		<tbody>
		 			<tr class="success" style="font-size:12px;">
		 				<td>
		 					#
		 				</td>
		 				<td>
		 					País
		 				</td>
		 				<td>
		 					Valor de ventas totales SPP
		 				</td>
		 				<td>
		 					Tipo moneda
		 				</td>
		 			</tr>
		 			<?php 
		 			$row_trim_empresa = mysql_query("SELECT * FROM trim_productos_empresas", $dspp) or die(mysql_error());
		 			$num_trim_empresa = mysql_num_rows($row_trim_empresa);
		 			if($num_trim_empresa > 0){
		 				$contador = 1;
		 				while($trim_empresa = mysql_fetch_assoc($row_trim_empresa)){
		 				?>
		 				<tr>
		 					<td><?php echo $contador; ?></td>
		 					<td><?php echo $trim_empresa['pais']; ?></td>
		 					<td><?php echo $trim_empresa['ventas_totales']; ?></td>
		 					<td><?php echo $trim_empresa['tipo_moneda']; ?></td>
		 				</tr>
		 				<?php
		 				$contador++;
		 				}
		 			}else{
		 				echo "<tr class='warning'><td colspan='4'>No se encontraton registros</td></tr>";
		 			}
		 			 ?>
					<tr class="success">
						<td></td>
						<td>
			              <select class="form-control" name="pais" id="pais" class="" required>
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
							<input type="number" step="any" class="form-control" name="ventas_totales" placeholder="Valor ventas totales" required>
						</td>
						<td>
							<input type="text" class="form-control" name="tipo_moneda" placeholder="Ej: $USD" required>
						</td>
					</tr>
		 			<tr>
		 				<td colspan="4"><button class="btn btn-sm btn-primary" type="submit" style="width:100%" name="agregar_registro" value="1"><span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Guardar Registro</button></td>
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