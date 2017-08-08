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

if(isset($_POST['agregar_plataforma']) && $_POST['agregar_plataforma'] == 1){
	$insertSQL = sprintf("INSERT INTO plataformas_spp(pais) VALUES(%s)",
		GetSQLValueString($_POST['pais'], "text"));
	$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
}
?>
<h4>Plataformas SPP</h4>
<div class="row">
	<div id="div_central" class="col-md-12">
		<table class="table table-bordered table-condensed" style="font-size:12px;">
			<thead>
				<tr>
					<th>
						<button type="button" class="btn btn-sm btn-success" name="agregar_plataforma" value="1" onclick="formulario_plataforma()"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> plataforma</button>
					</th>
					<th colspan="2"><h5>Plataformas SPP</h5></th>
				</tr>
				<tr>
					<th>#</th>
					<th>País</th>
				</tr>
			</thead>
			<tbody>
				<?php 
				$row_plataformas = mysql_query("SELECT * FROM plataformas_spp", $dspp) or die(mysql_error());
				$total_plataformas = mysql_num_rows($row_plataformas);
				
				if($total_plataformas == 0){
					echo "<tr><td colspan='3'>No se encontraron registros</td></tr>";
				}else{
					$contador = 1;
					while($plataformas = mysql_fetch_assoc($row_plataformas)){
					?>
						<tr>
							<td><?php echo $contador; ?></td>
							<td><?php echo $plataformas['pais']; ?></td>
						</tr>
					<?php
					$contador++;
					}
				}
				?>
			</tbody>
		</table>
	</div>
	<div id="div_formulario" class="col-md-6" style="display:none">
		<form action="" method="POST">
			<table class="table table-bordered table-condensed" style="font-size:12px;">
				<tr>
					<td class="text-center info" colspan="2"><h5>Nueva plataforma SPP</h5></td>
				</tr>
				<tr>
					<td>Pais de la Plataforma</td>
					<td>
						<select name="pais" required>
							<option>- - -</option>
							<?php 
							$row_pais = mysql_query("SELECT nombre FROM paises", $dspp) or die(mysql_error());
							while($pais = mysql_fetch_assoc($row_pais)){
								echo '<option value="'.utf8_encode($pais['nombre']).'">'.utf8_encode($pais['nombre']).'</option>';
							}
							?>
						</select>
					</td>
				</tr>
				<!--<tr>
					<td>% asignado a la nueva plataforma</td>
					<td>
						<div class="input-group">
					      <div class="input-group-addon">%</div>
					      <input type="number" step="any" class="form-control" id="porcentaje" name="porcentaje" placeholder="Ej: 10" required>
					    </div>
					</td>
				</tr>-->
				<tr>
					<td colspan="2">
						<button type="submit" class="btn btn-sm btn-warning" name="agregar_plataforma" value="1"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Agregar nueva plataforma</button>
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>

<script>
	function formulario_plataforma(){
		$("#div_central").removeClass("col-md-12");
		$("#div_central").addClass("col-md-6");

		document.getElementById('div_formulario').style.display = 'block';
		//alert('SE LANZA EL FORMULARIO');
	}
		/*contador++;
	var table = document.getElementById("tablaCertificaciones");
	  {
	  var row = table.insertRow(2);
	  var cell1 = row.insertCell(0);
	  var cell2 = row.insertCell(1);
	  var cell3 = row.insertCell(2);
	  var cell4 = row.insertCell(3);

	  cell1.innerHTML = '<input type="text" class="form-control" name="certificacion['+contador+']" id="exampleInputEmail1" placeholder="CERTIFICACIÓN">';
	  cell2.innerHTML = '<input type="text" class="form-control" name="certificadora['+contador+']" id="exampleInputEmail1" placeholder="CERTIFICADORA">';
	  cell3.innerHTML = '<input type="text" class="form-control" name="ano_inicial['+contador+']" id="exampleInputEmail1" placeholder="AÑO INICIAL">';
	  cell4.innerHTML = '<div class="col-md-6">SI<input type="radio" class="form-control" name="interrumpida['+contador+']" value="SI"></div><div class="col-md-6">NO<input type="radio" class="form-control" name="interrumpida['+contador+']" value="NO"></div>';
	  }
	}	*/

</script>

