<?php 
require_once('../Connections/dspp.php'); 

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

if(isset($_POST['opp_delete'])){
  $query=sprintf("delete from opp where idopp = %s",GetSQLValueString($_POST['idopp'], "text"));
  $ejecutar=mysql_query($query,$dspp) or die(mysql_error());
}

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_opp = 40;
$pageNum_opp = 0;
if (isset($_GET['pageNum_opp'])) {
  $pageNum_opp = $_GET['pageNum_opp'];
}
$startRow_opp = $pageNum_opp * $maxRows_opp;

mysql_select_db($database_dspp, $dspp);

if(isset($_POST['consultar']) && $_POST['consultar'] == 1){
	$buscar = $_POST['buscar'];
	$query = "SELECT opp.idopp, opp.idoc, opp.spp, opp.nombre, opp.abreviacion AS 'abreviacion_opp', opp.ciudad, opp.pais, opp.email, opp.sitio_web, opp.telefono, opp.direccion_oficina, opp.estatus_opp, opp.estatus_publico, opp.estatus_interno, opp.estatus_dspp, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', estatus_interno.nombre 'nombre_interno', estatus_dspp.nombre 'nombre_dspp', num_socios.numero, MAX(certificado.idcertificado) AS 'idcertificado', MAX(certificado.vigencia_fin) AS 'fecha_fin' FROM opp LEFT JOIN oc ON opp.idoc = oc.idoc LEFT JOIN estatus_publico ON opp.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON opp.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON opp.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN num_socios ON opp.idopp = num_socios.idopp LEFT JOIN certificado ON opp.idopp = certificado.idopp WHERE opp.spp LIKE '%$buscar%' OR opp.nombre LIKE '%$buscar%' OR opp.abreviacion LIKE '%$buscar%' AND opp.estatus_opp != 'ARCHIVADO' GROUP BY opp.idopp ORDER BY fecha_fin DESC";
	$row_opp = mysql_query($query, $dspp) or die(mysql_error());
}

?>
<p class="alert alert-info" style="padding:5px;">
	Pour obtenir plus d'informations sur une organisation ou une entreprise, entrez le numéro SPP, le sigle ou le nom de celle-ci.
</p>
<form action="" method="POST">
	<div class="input-group">
		<span class="input-group-btn">
			<button class="btn btn-success" type="submit" name="consultar" value="1">Chercher</button>
		</span>
		<input type="text" class="form-control" id="buscar" name="buscar" placeholder="#SPP, Nom, Sigle" required>
	</div><!-- /input-group -->
</form>


<?php 
	if(isset($row_opp)){ /// IF ISSET
		$resultado = mysql_num_rows($row_opp);
		?>
		<table class="table table-bordered table-condensed">
			<thead style="font-size:12px;">
				<tr>
					<th></th>
					<th class="text-center">#SPP</th>
					<th class="text-center">Organisation</th>
					<th class="text-center">Pays</th>
					<th class="text-center">Processus de certification</th>
					<th class="text-center">Date limite du certificat</th>
					<th class="text-center">Produits</th>
					<th class="text-center">Organisme de certification</th>
					<th class="text-center">Certificat</th>
				</tr>
			</thead>
			<tbody style="font-size:11px;">
				<?php 
				while($opp = mysql_fetch_assoc($row_opp)){
					$query_solicitud = "SELECT idsolicitud_certificacion, tipo_solicitud, idopp FROM solicitud_certificacion WHERE idopp = '$opp[idopp]'";
          			$row_solicitud = mysql_query($query_solicitud, $dspp) or die(mysql_error());
          			$solicitud = mysql_fetch_assoc($row_solicitud);
				?>
					<tr>
						<td>
							<button type="button" class="btn btn-xs btn-primary" data-toggle="modal" data-target="<?php echo "#datos".$opp['idopp']; ?>">Consultation<br>des informations générales</button>

							<div id="<?php echo "datos".$opp['idopp']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
								<div class="modal-dialog modal-lg" role="document">
								  <div class="modal-content">
								    <div class="modal-header">
								      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								      <h4 class="text-center modal-title" id="myModalLabel">Informations générales sur l'organisation</h4>
								    </div>
								    <div class="modal-body" style="font-size:13px;">
										<div class="row">
											<div class="col-md-6">
												<p>
													<strong>Nom de l'organisation:</strong><br>
													<?php echo $opp['nombre'].' - <span style="color:red">'.$opp['abreviacion_opp'].'</span>'; ?>													
												</p>
												<p>
													<strong>Pays de l'organisation:</strong>
													<br>
													<?php echo $opp['pais']; ?>
												</p>
												<p>
													<strong>Ville:</strong>
													<br>
													<?php echo $opp['ciudad']; ?>
												</p>
												<p>
													<strong>Site web:</strong>
													<br>
													<?php echo $opp['sitio_web']; ?>					
												</p>
											</div>
											<div class="col-md-6">
												<p>
													<strong>Courriel:</strong>
													<br>
													<?php echo $opp['email']; ?>													
												</p>
												<p>
													<strong>Téléphones:</strong>
													<br>
													<?php echo $opp['telefono']; ?>													
												</p>
												<p>
													<strong>Adresse du bureau:</strong>
													<br>
													<?php echo $opp['direccion_oficina']; ?>
												</p>
											</div>
										</div>
								    </div>
								    <div class="modal-footer">
								      <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
								      <!--<button type="button" class="btn btn-primary">Guardar Cambios</button>-->
								    </div>
								  </div>
								</div>
							</div>
						</td>
						<td class="text-center">
							<?php echo $opp['spp']; ?>
						</td>
						<td class="text-center">
							<?php echo $opp['nombre'].' - <span style="color:red">'.$opp['abreviacion_opp'].'</span>'; ?>
						</td>
						<td class="text-center">
							<?php echo $opp['pais']; ?>
						</td>
			            <!--- INICIA ESTATUS DE LA OPP ---->
			            <td class="text-center">
				            <?php 
				            /*$row_certificadas = mysql_query("SELECT opp.idopp, certificado.idopp FROM opp INNER JOIN certificado ON opp.idopp = certificado.idopp WHERE opp.idopp = '$pais[pais]' AND (opp.estatus_dspp != 16 AND opp.estatus_interno != 10 AND opp.estatus_interno != 11 OR opp.estatus_interno = 15) GROUP BY certificado.idopp", $dspp);
				            $num_certificadas = mysql_num_rows($row_certificadas);
				            $total_certificada += $num_certificadas;
				            */
				            if($opp['estatus_dspp'] == 14 OR $opp['estatus_dspp'] == 15 OR $opp['estatus_dspp'] == 13){
				              echo "<p class='text-center alert alert-success' style='padding:5px;'>Certifié</p>";
				            }else if($solicitud['tipo_solicitud'] == 'RENOVACION' && $opp['estatus_dspp'] = 16 ){
				              echo "<p class='text-center alert alert-warning' style='padding:5px;'>En voie de certification</p>";
				            }else if(!isset($opp['fecha_fin']) && $solicitud['tipo_solicitud'] == 'NUEVA'){
				              echo "<p class='text-center alert alert-info' style='padding:5px;'>Demande initiale</p>";
				            }else if($opp['estatus_dspp'] == 16 && !isset($solicitud['tipo_solicitud'])){
				              echo "<p class='text-center alert alert-danger' style='padding:5px;'>Certificat expiré</p>";
				            }else{
				              echo '<p style="color:red">Non disponible</p>';
				            }
				            /*echo 'interno'.$opp['estatus_interno'].'<br>';
				            echo 'dspp'.$opp['estatus_dspp'].'<br>';
				            echo 'opp'.$opp['estatus_opp'].'<br>';
				            */
				             ?>
			            </td>
			            <!--- TERMINA ESTATUS DE LA OPP ---->
						<!--- INICIA FECHA_FINAL ---->
						<td class="text-center">
						<?php 
						$vigencia_fin = date('d-m-Y', strtotime($opp['fecha_fin']));
						$timeVencimiento = strtotime($opp['fecha_fin']);
						if(isset($opp['fecha_fin'])){
							echo $vigencia_fin;
						}
						?>

						<?php 
							if(isset($opp['idcertificado'])){
							$estatus_certificado = mysql_query("SELECT idcertificado, estatus_certificado, estatus_dspp.nombre FROM certificado LEFT JOIN estatus_dspp ON certificado.estatus_certificado = estatus_dspp.idestatus_dspp WHERE idcertificado = $opp[idcertificado]", $dspp) or die(mysql_error());
							$certificado = mysql_fetch_assoc($estatus_certificado);

							switch ($certificado['estatus_certificado']) {
							  case '13': //certificado "activo"
							    $clase = 'text-center alert alert-success';
							    break;
							  case '14': //certificado "renovacion"
							    $clase = 'text-center alert alert-info';
							    break;
							  case '15': //certificado "por expirar"
							    $clase = 'text-center alert alert-warning';
							    break;
							  case '16': //certificado "Expirado"
							    $clase = 'text-center alert alert-danger';
							    break;

							  default:
							    # code...
							    break;
							}
							 echo "<p style='padding:5px;' class='".$clase."'>".$certificado['nombre']."</p>";
							}else{
							echo "<p style='padding:5px;'>Non disponible</p>";
							}
							//echo $opp['estatus_certificado'];
						?>

						</td>
						<!--- TERMINA FECHA_FINAL ---->

						<!--- INICIA PRODUCTOS ---->
						<td class="text-center">
							<?php 
							$row_productos = mysql_query("SELECT * FROM productos WHERE idopp = $opp[idopp]", $dspp) or die(mysql_error());
							$total_productos = mysql_num_rows($row_productos);
							
							if($total_productos == 0){
								echo 'Non disponible';
							}
							$contador = 1;
							while($productos = mysql_fetch_assoc($row_productos)){
								if($contador < $total_productos){
									echo $productos['producto'].', ';
								}else{
									echo $productos['producto'];
								}
								$contador++;
							}
							?>
						</td>
						<!--- TERMINA PRODUCTOS ---->

						<td class="text-center">
							<?php
							if($opp['abreviacion_oc']){
								echo $opp['abreviacion_oc'];
							}else{
								echo 'Non disponible';
							}
							 ?>
						</td>
						<td class="text-center">
							<?php 
							if(isset($opp['idcertificado'])){
								$row_certificado = mysql_query("SELECT archivo FROM certificado WHERE idcertificado = $opp[idcertificado]", $dspp) or die(mysql_error());
								$certificado = mysql_fetch_assoc($row_certificado);
								if($certificado['archivo']){
									echo "<a href='".$certificado['archivo']."' target='_new'><img src='../../img/logo_certificado.png'></a>";
								}else{
									echo 'Non disponible';
								}
							}else{
								echo 'Non disponible';
							}
							?>
						</td>
					</tr>
				<?php
				}
				 ?>
			</tbody>
		</table>
		<?php
	} /// IF ISSET
 ?>
