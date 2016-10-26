<?php
require_once("dompdf/dompdf_config.inc.php");

# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_dspp = "localhost";
$database_dspp = "dspporg_dspp";
//$username_dspp = "root";
//$password_dspp = "";

$username_dspp = "dspporg_user";
$password_dspp = "]ng@XX(4R6iM";
$dspp = mysql_connect($hostname_dspp, $username_dspp, $password_dspp) or trigger_error(mysql_error(),E_USER_ERROR); 


mysql_select_db($database_dspp, $dspp);
set_time_limit(300);


if(isset($_POST['generarPDF']) && $_POST['generarPDF'] == 'pdf'){
	$codigoHTML = $_POST['codigoHTML'];

	$codigoHTML=utf8_decode($codigoHTML);
	$dompdf=new DOMPDF();
	$dompdf->set_paper("legal","landscape");
	$dompdf->load_html($codigoHTML);
	ini_set("memory_limit","265M");
	$dompdf->render();
	$dompdf->stream("Reporte_tabla_usuarios.pdf");
}

if(isset($_POST['generarExcel']) && $_POST['generarExcel'] == 'sdfgsfg'){
	$codigoHTML = $_POST['codigoHTML'];
	header("Content-type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=Lista_OPP.xls");
	echo $codigoHTML;
}

if(isset($_GET['generarExcel']) && $_GET['generarExcel'] == 'tabla'){
	$codigoHTML = $_POST['codigoHTML'];
	header("Content-type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=Lista_OPP.xls");
	echo $codigoHTML;
}

if(isset($_POST['reportePDF']) && $_POST['reportePDF'] == "1"){
	$query_opp = $_POST['consultaPDF'];
	$row_opp = mysql_query($query_opp) or die(mysql_error());


	$codigoHTML='
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Documento sin título</title>
	<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
	
	</head>
	<body>';


	$codigoHTML.=' <table width="100%" class="table table-bordered" border="1" cellspacing="0" cellpadding="0" style="font-size:10px;">
	  <tr>
	    <td colspan="18"><CENTER><strong>LISTA OPP</strong></CENTER></td>
	  </tr>
	  <tr bgcolor="#40d47e" style="text-align:center">
	    <td><strong>IDOPP</strong></td>
	    <td><strong>IDF</strong></td>
	    <td><strong>PASSWORD</strong></td>
	    <td><strong>NOMBRE</strong></td>
	    <td><strong>ABREVIACION</strong></td>
	    <td><strong>SITIO WEB</strong></td>
	    <td><strong>EMAIL</strong></td>
	    <td><strong>TELEFONO</strong></td>
	    <td><strong>PAIS</strong></td>
	    <td><strong>CIUDAD</strong></td>
	    <td><strong>IDOC</strong></td>
	    <td><strong>RAZON SOCIAL</strong></td>
	    <td><strong>DIRECCION</strong></td>
	    <td><strong>DIRECCION FISCAL</strong></td>
	    <td><strong>RFC</strong></td>
	    <td><strong>RUC</strong></td>
	    <td><strong>FECHA INCLUSION</strong></td>
	    <td><strong>ESTADO</strong></td>
	  </tr>
	  ';

	while($opp = mysql_fetch_assoc($row_opp)){
		$codigoHTML.= '
		<tr>
			<td>'.$opp['idopp'].'</td>
			<td>'.$opp['idf'].'</td>
			<td>'.$opp['password'].'</td>
			<td>'.$opp['nombre'].'</td>
			<td>'.$opp['abreviacion'].'</td>
			<td>'.$opp['sitio_web'].'</td>
			<td>'.$opp['email'].'</td>
			<td>'.$opp['telefono'].'</td>
			<td>'.$opp['pais'].'</td>
			<td>'.$opp['ciudad'].'</td>
			<td>'.$opp['idoc'].'</td>
			<td>'.$opp['razon_social'].'</td>
			<td>'.$opp['direccion'].'</td>
			<td>'.$opp['direccion_fiscal'].'</td>
			<td>'.$opp['rfc'].'</td>
			<td>'.$opp['ruc'].'</td>
			<td>'.$opp['fecha_inclusion'].'</td>
			<td>'.$opp['estado'].'</td>		
		</tr>
		';

	}
		
	$codigoHTML.='
	</table>
	</body>
	</html>';


	$codigoHTML=utf8_decode($codigoHTML);
	$dompdf=new DOMPDF();
	$dompdf->set_paper("legal","landscape");
	$dompdf->load_html($codigoHTML);
	ini_set("memory_limit","265M");

	$dompdf->render();
	$dompdf->stream("Reporte_tabla_usuarios.pdf");
	

}

if(isset($_POST['reporteExcel']) && $_POST['reporteExcel'] == "2"){
	header("Content-type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=Lista_OPP.xls");

	$query_opp = $_POST['consultaXLS'];
	$row_opp = mysql_query($query_opp) or die(mysql_error());	


	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>LISTA DE USUARIOS</title>
	</head>
	<body>
	<table width="100%" border="1" cellspacing="0" cellpadding="0">
	  <tr>
	     <td colspan="18"><CENTER><strong>LISTA OPP</strong></CENTER></td>
	  </tr>
	  <tr bgcolor="red">
	    <td><strong>IDOPP</strong></td>
	    <td><strong>IDF</strong></td>
	    <td><strong>PASSWORD</strong></td>
	    <td><strong>NOMBRE</strong></td>
	    <td><strong>ABREVIACION</strong></td>
	    <td><strong>SITIO WEB</strong></td>
	    <td><strong>EMAIL</strong></td>
	    <td><strong>TELEFONO</strong></td>
	    <td><strong>PAIS</strong></td>
	    <td><strong>CIUDAD</strong></td>
	    <td><strong>IDOC</strong></td>
	    <td><strong>RAZON SOCIAL</strong></td>
	    <td><strong>DIRECCION</strong></td>
	    <td><strong>DIRECCION FISCAL</strong></td>
	    <td><strong>RFC</strong></td>
	    <td><strong>RUC</strong></td>
	    <td><strong>FECHA INCLUSION</strong></td>
	    <td><strong>ESTADO</strong></td>
	  </tr>
	  
	<?PHP

	while($opp = mysql_fetch_assoc($row_opp)){				

	?>  
	 <tr>
			<td><?php echo $opp['idopp']?></td>
			<td><?php echo $opp['idf']?></td>
			<td><?php echo $opp['password']?></td>
			<td><?php echo $opp['nombre']?></td>
			<td><?php echo $opp['abreviacion']?></td>
			<td><?php echo $opp['sitio_web']?></td>
			<td><?php echo $opp['email']?></td>
			<td><?php echo $opp['telefono']?></td>
			<td><?php echo $opp['pais']?></td>
			<td><?php echo $opp['ciudad']?></td>
			<td><?php echo $opp['idoc']?></td>
			<td><?php echo $opp['razon_social']?></td>
			<td><?php echo $opp['direccion']?></td>
			<td><?php echo $opp['direccion_fiscal']?></td>
			<td><?php echo $opp['rfc']?></td>
			<td><?php echo $opp['ruc']?></td>
			<td><?php echo $opp['fecha_inclusion']?></td>
			<td><?php echo $opp['estado']?></td>	                    
	 </tr> 
	  <?php
	}
	  ?>
	</table>
	</body>
	</html>


<?php 
}

if(isset($_POST['generarExcel']) && $_POST['generarExcel'] == "tabla"){ //***********************************************
	header("Content-type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=Lista_OPP.xls");

        $query = "SELECT opp.*, solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.op_resp11, solicitud_certificacion.op_resp12, solicitud_certificacion.op_resp13  FROM opp INNER JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp";
        $ejecutar = mysql_query($query,$dspp) or die(mysql_error());	


	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>LISTA DE USUARIOS</title>
	</head>
	<body>
		<table class="table" border="1">
			<thead>
				<tr>
					<th colspan="9">Lista OPP</th>
				</tr>
				<tr>
					<th>Nº</th>
					<th>#SPP</th>
					<th>Nombre</th>
					<th>Pais</th>
					<th>Certificación</th>
					<th>Productos</th>
					<th style="font-size:11px;">DEL TOTAL DE SUS VENTAS ¿QUÉ PORCENTAJE DEL PRODUCTO CUENTA CON LA CERTIFICACIÓN DE ORGÁNICO, COMERCIO JUSTO Y/O SÍMBOLO DE PEQUEÑOS PRODUCTORES?</th>
					<th style="font-size:11px;"> ¿TUVO VENTAS SPP DURANTE EL CICLO DE CERTIFICACIÓN ANTERIOR?</th>
					<th>Total Ventas</th>
				</tr>
			</thead>
			<tbody>
				<?php 
				$contador = 1;
				while($opp = mysql_fetch_assoc($ejecutar)){
					//$queryCertificacion = "SELECT * FROM certificaciones WHERE idsolicitud_certificacion = $opp[idsolicitud_certificacion] AND (certificacion LIKE '%Orgánica%' OR certificacion LIKE '%ORGANICO%' OR certificacion LIKE '%Organica%' OR certificacion LIKE '%FLO%' OR certificacion LIKE '%flo%')";
					$queryCertificacion = "SELECT * FROM certificaciones WHERE idsolicitud_certificacion = $opp[idsolicitud_certificacion]";
					$ejecutarCertificacion = mysql_query($queryCertificacion,$dspp) or die(mysql_error());

					$queryProducto = "SELECT * FROM productos WHERE idsolicitud_certificacion = $opp[idsolicitud_certificacion]";
					$ejecutarProducto = mysql_query($queryProducto,$dspp) or die(mysql_error());
				?>
					<tr>
						<td><?php echo $contador; ?></td>
						<td><?php echo strtoupper($opp['idf']); ?></td>
						<td><?php echo strtoupper($opp['nombre']); ?></td>
						<td><?php echo strtoupper($opp['pais']); ?></td>
						<td>
							<?php 
							while($certificacion = mysql_fetch_assoc($ejecutarCertificacion)){
								echo strtoupper($certificacion['certificacion']);
								echo "<br>";
							}
							 ?>
						</td>
						<td>
							<?php 
							while($producto = mysql_fetch_assoc($ejecutarProducto)){
								echo strtoupper($producto['producto']);
								echo "<br>";
							}
							 ?>
						</td>
						<td><?php echo strtoupper($opp['op_resp11']); ?></td>
						<td><?php echo strtoupper($opp['op_resp12']); ?></td>
						<td><?php echo strtoupper($opp['op_resp13']); ?></td>
						
			</tr>
				<?php
				$contador++;
				}
				 ?>
			</tbody>
		</table>
	</body>
	</html>


<?php 
}//***********************************************//***********************************************//***********************************************



	if(isset($_POST['contactoPDF']) && $_POST['contactoPDF'] == 1){
		$queryContacto = $_POST['queryPDF'];
		$contador = 1;
		$ejecutar = mysql_query($queryContacto,$dspp) or die(mysql_error());


		$codigoHTML='
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Documento sin título</title>
		</head>
		<body>';

		$codigoHTML.='<table width="100%" border="1" cellspacing="0" cellpadding="0" style="font-size:10px;">
			  <tr>
			    <td colspan="18"><CENTER><strong>LISTA OPP</strong></CENTER></td>
			  </tr>
			  <tr bgcolor="#40d47e" style="text-align:center">
			    <td><strong>Nº</strong></td>
			    <td><strong>IDF</strong></td>
			    <td><strong>NOMBRE</strong></td>
			    <td><strong>ABREVIACION</strong></td>
			    <td><strong>EMAIL</strong></td>
			    <td><strong>TELEFONO</strong></td>
			    <td><strong>PAIS</strong></td>
			    <td><strong>DIRECCION</strong></td>
			    <td><strong>DIRECCION FISCAL</strong></td>
			    <td>CONTACTO</td>
			    <td>CARGO</td>

			    <td>TELEFONO 1</td>
			    <td>TELEFONO 2</td>
			    <td>EMAIL 1</td>
			    <td>EMAIL 2</td>
			  </tr>';
			  
			  while ($row_contacto = mysql_fetch_assoc($ejecutar)) {
			  	if(($contador%2) == 0){
			  		$color = "style=background:#ecf0f1;";
			  	}else{
			  		$color = "style=background:#fff;";
			  	}

				$codigoHTML.='
				<tr '.$color.'>	  	
					<td>'.$contador.'</td>
				  	<td>'.$row_contacto['idf'].'</td>
				  	<td>'.$row_contacto['nombre'].'</td>
				  	<td>'.$row_contacto['abreviacion'].'</td>
				  	<td>'.$row_contacto['email'].'</td>
				  	<td>'.$row_contacto['telefono'].'</td>
				  	<td>'.$row_contacto['pais'].'</td>
				  	<td>'.$row_contacto['direccion'].'</td>
				  	<td>'.$row_contacto['direccion_fiscal'].'</td>
				  	<td>'.$row_contacto['contacto'].'</td>
				  	<td>'.$row_contacto['cargo'].'</td>
				  	<td>'.$row_contacto['telefono1'].'</td>
				  	<td>'.$row_contacto['telefono2'].'</td>
				  	<td>'.$row_contacto['email1'].'</td>
				  	<td>'.$row_contacto['email2'].'</td>
			  	</tr>';	

			  	$contador++;
			  }
			 
			
		$codigoHTML.='
		</table>
		</body>
		</html>';


		$codigoHTML=utf8_decode($codigoHTML);
		$dompdf=new DOMPDF();
		$dompdf->set_paper("legal","landscape");
		$dompdf->load_html($codigoHTML);
		ini_set("memory_limit","265M");

		$dompdf->render();
		$dompdf->stream("Reporte_tabla_usuarios.pdf");
	}

	if(isset($_POST['contactoExcel']) && $_POST['contactoExcel'] == 2){
		header("Content-type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=Lista_OPP.xls");
		$queryContacto = $_POST['queryExcel'];
		$contador = 1;
		$ejecutar = mysql_query($queryContacto,$dspp) or die(mysql_error());

		?>

		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>LISTA DE USUARIOS</title>
		</head>
		<body>
		<table width="100%" border="1" cellspacing="0" cellpadding="0" style="font-size:10px;">
		  <tr>
		    <td colspan="18"><CENTER><strong>LISTA OPP</strong></CENTER></td>
		  </tr>
		  <tr bgcolor="#40d47e" style="text-align:center">
		    <td><strong>Nº</strong></td>
		    <td><strong>IDF</strong></td>
		    <td><strong>NOMBRE</strong></td>
		    <td><strong>ABREVIACION</strong></td>
		    <td><strong>EMAIL</strong></td>
		    <td><strong>TELEFONO</strong></td>
		    <td><strong>PAIS</strong></td>
		    <td><strong>DIRECCION</strong></td>
		    <td><strong>DIRECCION FISCAL</strong></td>
		    <td>CONTACTO</td>
		    <td>CARGO</td>

		    <td>TELEFONO 1</td>
		    <td>TELEFONO 2</td>
		    <td>EMAIL 1</td>
		    <td>EMAIL 2</td>
		  </tr>
		<?php
		while ($row_contacto = mysql_fetch_assoc($ejecutar)) {
		  	if(($contador%2) == 0){
		  		$color = "style=background:#bdc3c7;";
		  	}else{
		  		$color = "style=background:red;";
		  	}
		?>
			<tr>	  	
				<td><?php echo $contador; ?></td>
			  	<td><?php echo $row_contacto['idf']; ?></td>
			  	<td><?php echo $row_contacto['nombre']; ?></td>
			  	<td><?php echo $row_contacto['abreviacion']; ?></td>
			  	<td><?php echo $row_contacto['email']; ?></td>
			  	<td><?php echo $row_contacto['telefono']; ?></td>
			  	<td><?php echo $row_contacto['pais']; ?></td>
			  	<td><?php echo $row_contacto['direccion']; ?></td>
			  	<td><?php echo $row_contacto['direccion_fiscal']; ?></td>
			  	<td><?php echo $row_contacto['contacto']; ?></td>
			  	<td><?php echo $row_contacto['cargo']; ?></td>

			  	<td><?php echo $row_contacto['telefono1']; ?></td>
			  	<td><?php echo $row_contacto['telefono2']; ?></td>
			  	<td><?php echo $row_contacto['email1']; ?></td>
			  	<td><?php echo $row_contacto['email2']; ?></td>
		  	</tr>	
		<?php
		  	$contador++;
		}
		?>
		</table>
		</body>
		</html>
<?php
	}
 ?>