<?php  
require_once('Connections/dspp.php');
      require_once('../Connections/mail.php');
        mysql_select_db($database_dspp, $dspp);

        $query = "SELECT opp.*, solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.op_resp11, solicitud_certificacion.op_resp12, solicitud_certificacion.op_resp13  FROM opp INNER JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp";
        $ejecutar = mysql_query($query,$dspp) or die(mysql_error());

?>
    <div style="display:inline;margin-right:10em;">
      Exportar Contactos
      <a href="#" onclick="document.formulario1.submit()"><img src="../img/pdf.png"></a>
      <a href="#" onclick="document.formulario2.submit()"><img src="../img/excel.png"></a>
    </div>
 
  <!--</div>-->

  <form name="formulario1" method="POST" action="../../reporte.php">
    <input type="hidden" name="contactoPDF" value="1">
    <input type="hidden" name="queryPDF" value="<?php echo $queryExportar; ?>">
  </form>
  <form name="formulario2" method="POST" action="../reporte.php">
    <input type="hidden" name="generarExcel" value="tabla">

  </form>

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