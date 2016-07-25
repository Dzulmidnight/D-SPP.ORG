<?php require_once('../Connections/dspp.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
  
  $redireccion = "../index.php?OC";

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

if(isset($_POST['opp_delete'])){
	$query=sprintf("delete from opp where idopp = %s",GetSQLValueString($_POST['idopp'], "text"));
	$ejecutar=mysql_query($query,$dspp) or die(mysql_error());
}

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_opp = 20;
$pageNum_opp = 0;
if (isset($_GET['pageNum_opp'])) {
  $pageNum_opp = $_GET['pageNum_opp'];
}
$startRow_opp = $pageNum_opp * $maxRows_opp;

mysql_select_db($database_dspp, $dspp);
if(isset($_GET['query'])){
  $query_opp = "SELECT opp.* ,solicitud_certificacion.* FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE solicitud_certificacion.idoc = $_SESSION[idoc] ORDER BY solicitud_certificacion.fecha_elaboracion DESC";

	#$query_opp = "SELECT * FROM solicitud_certificacion where idsolicitud_certificacion ='".$_GET['query']."' ORDER BY fecha DESC";
}else{
  #SELECT solicitud_certificacion.* FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE opp.idopp = 15

  $query_opp = "SELECT opp.* ,solicitud_certificacion.* FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE solicitud_certificacion.idoc = $_SESSION[idoc] ORDER BY solicitud_certificacion.fecha_elaboracion DESC"; 

  #$query_opp = "SELECT opp.* ,solicitud_certificacion.* FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp ORDER BY solicitud_certificacion.fecha_elaboracion ASC";  

	#$query_opp = "SELECT * FROM solicitud_certificacion ORDER BY fecha ASC";
}



if(isset($_POST['statusCertificado']) && $_POST['statusCertificado'] == "statusCertificado"){
  $status = $_POST['status'];

  $idsolicitud = $_POST['idsolicitud'];

  $query = "INSERT INTO certificado (status, idsolicitud) VALUES ('$status', $idsolicitud)";
  $certificado = mysql_query($query, $dspp) or die(mysql_error());

  $idexterno = mysql_insert_id($dspp);
  $identificador = "certificado";
  $fecha = $_POST['statusFecha'];


  $queryFecha = "INSERT INTO fecha (fecha, idexterno, identificador, status) VALUES($fecha, $idexterno, '$identificador', '$status')"; 
  $insertarFecha = mysql_query($queryFecha, $dspp) or die(mysql_error());


}

  if (isset($_POST['actualizarStatus']) && $_POST['actualizarStatus'] == "actualizarStatus") {
    $idcertificado = $_POST['idcertificado'];
    $status = $_POST['status'];    
    $query = "UPDATE certificado SET status = '$status' WHERE idcertificado = $idcertificado";
    $actualizar = mysql_query($query, $dspp) or die(mysql_error());
    $identificador = "certificado";
    $fecha = $_POST['statusFecha'];

    $queryFecha = "INSERT INTO fecha (fecha, idexterno, identificador, status) VALUES($fecha, $idcertificado, '$identificador', '$status')"; 
    $insertarFecha = mysql_query($queryFecha, $dspp) or die(mysql_error());

  }else{
    
  }


if(isset($_POST['cargarCertificado']) && $_POST['cargarCertificado'] == "cargarCertificado"){

  $ruta = "archivos/certificados/";

  if(!empty($_FILES['certificado_fld']['name'])){
    $_FILES['certificado_fld']['name'];
        move_uploaded_file($_FILES["certificado_fld"]["tmp_name"], $ruta.time()."_".$_FILES["certificado_fld"]["name"]);
        $adjunto = $ruta.basename(time()."_".$_FILES["certificado_fld"]["name"]);
  }else{
    $adjunto = NULL;
  }
  
  $vigenciaInicio = $_POST['vigenciaInicio'];
  $vigenciaFin = $_POST['vigenciaFin'];
  //$idoc = $_POST['certificadoIdoc'];
  //$idopp = $_POST['certificadoIdopp'];
  $statusPago = "POR REALIZAR";
  $fecha = $_POST['fechaCarga'];
  $idcertificado = $_POST['idcertificado'];


  $query = "UPDATE certificado SET vigenciainicio = '$vigenciaInicio', vigenciafin = '$vigenciaFin', adjunto = '$adjunto', statuspago = '$statusPago', fechaupload = $fecha WHERE idcertificado = $idcertificado";
  $certificado = mysql_query($query, $dspp) or die(mysql_error());
  //echo "la consulta es: ".$query;
}


$query_limit_opp = sprintf("%s LIMIT %d, %d", $query_opp, $startRow_opp, $maxRows_opp);
$opp = mysql_query($query_limit_opp, $dspp) or die(mysql_error());
//$row_opp = mysql_fetch_assoc($opp);

if (isset($_GET['totalRows_opp'])) {
  $totalRows_opp = $_GET['totalRows_opp'];
} else {
  $all_opp = mysql_query($query_opp);
  $totalRows_opp = mysql_num_rows($all_opp);
}
$totalPages_opp = ceil($totalRows_opp/$maxRows_opp)-1;

$queryString_opp = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_opp") == false && 
        stristr($param, "totalRows_opp") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_opp = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_opp = sprintf("&totalRows_opp=%d%s", $totalRows_opp, $queryString_opp);
?>
<?php 
  $query = "SELECT * FROM status";
  $ejecutarStatus = mysql_query($query,$dspp) or die(mysql_error());
 ?>
<h4>Consulta Solicitudes</h4>
<table class="table table-condensed table-bordered table-hover">
  <tr class="success">
    <th class="text-center"><b>Ultima <br>Actualización</b></th>
    <th class="text-center">Organización</th>
    <th class="text-center" colspan="2">Cotización</th>
    <th class="text-center">Sitio WEB</th>
    <th class="text-center">Email</th>
    <th class="text-center">País</th>
    <th class="text-center">Status</th>
    <th class="text-center" colspan="2">Certificado</th>
    <th class="text-center">Propuesta</th>
    <th class="text-center">Observaciones</th>
    <!--<th>OC</th>
    <th>Razón social</th>
    <th>Dirección fiscal</th>
    <th>RFC</th>-->
    <!--<th>Eliminar</th>-->
  </tr>
  <?php $cont=0; while ($row_opp = mysql_fetch_assoc($opp)) {$cont++; ?>
    <tr>
      <?php  $fecha = $row_opp['fecha_elaboracion']; ?>

        <td>
          <h6>
            <a class="btn btn-primary btn-sm" style="width:100%" href="?SOLICITUD&amp;detailBlock&amp;idsolicitud=<?php echo $row_opp['idsolicitud_certificacion']; ?>&contact" aria-label="Left Align">

              <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
              <?php echo  date("d/m/Y", $fecha); ?>
            </a>
          </h6>
        </td>

        <td>
          <h6><button class="btn btn-default disabled"><?php echo $row_opp['nombre']; ?></button></h6>
        </td>
        <td>
          <h6>
            <a href="http://d-spp.org/oc/<?echo $row_opp['cotizacion_opp']?>" target="_blank" type="button" class="btn <?php if(empty($row_opp['cotizacion_opp'])){ echo 'btn-danger btn-sm';}else{echo 'btn-success btn-sm';} ?>" aria-label="Left Align" <?php if(empty($row_opp['cotizacion_opp'])){echo "disabled";}?>>
              <span class="glyphicon glyphicon-download-alt"></span> OPP
            </a> 
          </h6>
        </td>
        <td>
          <h6>
            <a href="http://d-spp.org/oc/<?echo $row_opp['cotizacion_adm']?>" target="_blank" type="button" class="btn <?php if(empty($row_opp['cotizacion_adm'])){ echo 'btn-danger btn-sm';}else{echo 'btn-success btn-sm';} ?>" aria-label="Left Align" <?php if(empty($row_opp['cotizacion_adm'])){echo "disabled";}?>>
              <span class="glyphicon glyphicon-download-alt"></span> FUNDEPPO
            </a> 
          </h6>       
        </td>


        <td>
          <h6 class="alert alert-success"><?php if(empty($row_opp['sitio_web'])){echo "Sitio Web no disponible";}else{echo $row_opp['sitio_web'];} ?></h6>
        </td>
        <td><h6 class="alert alert-success"><?php echo $row_opp['p1_email']; ?></h6></td>
        <td><h6 class="alert alert-success"><?php echo $row_opp['pais']; ?></h6></td>
        <td>
          <h6 class="alert alert-success">
            <?php echo $row_opp['status']; ?>
          </h6>
        </td>

      <!-- CERTIFICADO -->
       
              <?php 
                $query = "SELECT * FROM certificado WHERE idsolicitud = $row_opp[idsolicitud_certificacion]";
                $ejecutar = mysql_query($query, $dspp) or die(mysql_error());
                $registroCertificado = mysql_fetch_assoc($ejecutar);

                $queryObjecion = "SELECT * FROM objecion WHERE idsolicitud = $row_opp[idsolicitud_certificacion]";
                $ejecutar2 = mysql_query($queryObjecion,$dspp);
                $registroObjecion = mysql_fetch_assoc($ejecutar2); 
               ?>
        <!-- STATUS CERTIFICADO -->
        <td>
      
          <?php if($row_opp['status'] == "APROBADO"){ ?>
            <?php if(isset($registroObjecion['idobjecion'])){ ?>
              <h6>
                <button class="btn btn-success btn-sm" data-toggle="modal" <?php echo "data-target='#status".$row_opp['idsolicitud_certificacion']."'"?>  >
                <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Status
                </button>            
              </h6>
            <?php }else{ ?>
              <h6>
                <button class="btn btn-warning btn-sm" data-toggle="modal" <?php echo "data-target='#status".$row_opp['idsolicitud_certificacion']."'"?>  >
                <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Status
                </button>            
              </h6>
            <?php } ?>

          <?php }else{ ?>
            <h6>
              <button class="btn btn-danger btn-sm" data-toggle="modal" <?php echo "data-target='#status".$row_opp['idsolicitud_certificacion']."'"?>  disabled>
              <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Status
              </button>            
            </h6>          
          <?php } ?>
        </td>

              <!-- Modal -->
              <form action="" method="post" id="statusCertificado" enctype="application/x-www-form-urlencoded">
                <div class="modal fade" <?php echo "id='status".$row_opp['idsolicitud_certificacion']."'" ?> tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Status Certificación</h4>
                      </div>
                      <div class="modal-body">
                        <div class="row">
                            <?php if(empty($registroObjecion['adjunto'])){ ?>
                              <div class="col-xs-12 alert alert-danger">
                                <p>Una vez finalizado el periodo de objeción, podra iniciar el periodo de certificación.</p>
                              </div>

                            <?php }else{ ?>
                              <div class="col-xs-12">
                                <div class="col-xs-12">
                                  <?php if(!empty($registroCertificado['status'])){ ?>
                                    <?
                                      $query = "SELECT * FROM status WHERE idstatus = $registroCertificado[status]";
                                      $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
                                      $estatus = mysql_fetch_assoc($ejecutar);
                                    ?>
                                    <label class="control-label" for="statusActual">Status Actual</label>
                                    <p name="statusActual" class="alert alert-success"><? echo $estatus['nombre'];?></p>

                                    <select name="status" class="form-control" id="status" required>
                                      <option class="form-control" value="">Seleccione un estado</option>
                                      <?php while($row_status = mysql_fetch_assoc($ejecutarStatus)){ ?>
                                        <option class="form-control" value="<?echo $row_status['idstatus'];?>"><?echo $row_status['nombre'];?></option>
                                      <?php } ?>
                                    </select>

                                  <?php }else{ ?>
                                    <label class="control-label" for="status">Status Certificación</label>
                                    <select name="status" class="form-control" id="status" required>
                                      <option class="form-control" value="">Seleccione un estado</option>
                                      <?php while($row_status = mysql_fetch_assoc($ejecutarStatus)){ ?>
                                        <option class="form-control" value="<?echo $row_status['idstatus'];?>"><?echo $row_status['nombre'];?></option>
                                      <?php } ?>
                                    </select>
                                    
                                    <!--<input class="form-control" type="text" name="status" placeholder="Ingresar status">-->

                                  <?php } ?>
                                </div>
                              </div>

                            <?php } ?>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                        <?php if(!empty($registroCertificado['status'])){ ?>
                          <button type="submit" class="btn btn-primary">Actualizar</button>
                          <input type="hidden" name="actualizarStatus" value="actualizarStatus">
                          <input type="hidden" name="idcertificado" value="<?echo $registroCertificado['idcertificado'];?>">
                        <?php }else{ ?>
                          <?php if(!empty($registroObjecion['adjunto'])){ ?>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                          <?php } ?>
                          <input type="hidden" name="statusCertificado" value="statusCertificado">
                        <?php } ?>
                        <!--<input type="hidden" name="statusIdopp" value="<?php echo $row_opp['idopp'];?>">
                        <input type="hidden" name="statusIdoc" value="<?php echo $row_opp['idoc'];?>">-->
                        <input type="hidden" name="statusFecha" value="<?echo time();?>">
                        <input type="hidden" name="idsolicitud" value="<?echo $row_opp['idsolicitud_certificacion'];?>">


                      </div>
                    </div>
                  </div>
                </div>
              </form>
              <!-- Modal -->


        <!-- STATUS CERTIFICADO -->

        <!-- CERTIFICADO CERTIFICADO -->
        <td>
          <?php if(isset($registroCertificado['status'])){ ?>
            <h6>
              <button class="btn btn-success btn-sm" data-toggle="modal" <?php echo "data-target='#certificado".$row_opp['idsolicitud_certificacion']."'"?>  >
              <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Certificado
              </button>            
            </h6>
          <?php }else{ ?>
            <h6>
              <button class="btn btn-danger btn-sm" data-toggle="modal" <?php echo "data-target='#certificado".$row_opp['idsolicitud_certificacion']."'"?>  disabled>
              <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Certificado
              </button>            
            </h6>
          <?php } ?>

        </td>

              <!-- Modal -->
              <form action="" method="post" id="cargarCertificado" enctype="multipart/form-data">
                <div class="modal fade" <?php echo "id='certificado".$row_opp['idsolicitud_certificacion']."'" ?> tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Certificado</h4>
                      </div>
                      <div class="modal-body">
                        <div class="row">
                          <div class="col-xs-12">

                            <div class="col-xs-6">
                              <label class="control-label" for="vigenciaInicio">Vigencia Inicio</label>
                              <?php if(isset($registroCertificado['vigenciainicio'])){ ?>
                                <input class="form-control" name="vigenciaInicio" id="vigenciaInicio" type="date" placeholder="dd/mm/aaaa" value="<?echo $registroCertificado['vigenciainicio'];?>" disabled>
                              <?php }else{ ?>
                                <input class="form-control" name="vigenciaInicio" id="vigenciaInicio" type="date" placeholder="dd/mm/aaaa" required>
                              <?php } ?>
                              <hr>
                              <label class="control-label" for="vigenciaFin">Vigencia Fin</label>
                              <?php if(isset($registroCertificado['vigenciafin'])){ ?>
                                <input class="form-control" name="vigenciaFin" id="vigenciaFin" type="date" placeholder="dd/mm/aaaa" value="<?echo $registroCertificado['vigenciafin'];?>" disabled>
                              <?php }else{ ?>
                                <input class="form-control" name="vigenciaFin" id="vigenciaFin" type="date" placeholder="dd/mm/aaaa" required>
                              <?php } ?>                                                          
                            </div>

                            <div class="col-xs-6">
                              <?php if(!empty($registroCertificado['adjunto'])){ ?>
                                <label class="control-label" for="certificado_fld">Certificado</label>
                                <br>
                                <a class="btn btn-info" href="<?echo $registroCertificado['adjunto'];?>"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Descargar Certificado</a>
                              <?php }else{ ?>
                                <label class="control-label" for="certificado_fld">Cargar Certificado</label>
                                <input class="form-control" name="certificado_fld" id="certificado_fld" type="file" required>
                              <?php } ?>
                            </div>

                          </div>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                        <?php if(empty($registroCertificado['adjunto'])){ ?>
                          <button type="submit" class="btn btn-primary">Guardar</button>
                        <?php }?>
                        <!--<input type="text" name="certificadoIdopp" value="<?php echo $row_opp['idopp'];?>">
                        <input type="text" name="certificadoIdoc" value="<?php echo $row_opp['idoc'];?>">-->
                        <input type="hidden" name="fechaCarga" value="<?echo time();?>">
                        <input type="hidden" name="idcertificado" value="<?echo $registroCertificado['idcertificado'];?>">
                        <input type="hidden" name="cargarCertificado" value="cargarCertificado">
                        <input type="hidden" name="idsolicitud" value="<?echo $row_opp['idsolicitud_certificacion'];?>">

                      </div>
                    </div>
                  </div>
                </div>
              </form>
              <!-- Modal -->




        <!-- CERTIFICADO CERTIFICADO -->

      <!-- CERTIFICADO -->
        <!--<td>-->
        <td>
          <h6>
          <?php 
              if($row_opp['status'] == "APROBADO"){
             ?>
                <button class="btn btn-success btn-sm" disabled>
                  <span class="glyphicon glyphicon-check" aria-hidden="true"></span> Aceptada
                </button>          
            <?php 
              }else{
             ?>
                <button class="btn btn-default btn-sm" disabled>
                  <span class="glyphicon glyphicon-check" aria-hidden="true"></span> Aceptada
                </button>   
            <?php 
              }
             ?>
          </h6>
        </td>
        <td>
          <h6>
            <?php if(empty($row_opp['observaciones'])){ ?>
              <button class="btn btn-default btn-sm" disabled>
                <span class="glyphicon glyphicon-list-alt"></span> Consultar
              </button>         
            <?php }else{ ?>
              <a class="btn btn-info btn-sm" href="?SOLICITUD&amp;detailBlock&amp;idsolicitud=<?php echo $row_opp['idsolicitud_certificacion']; ?>">
                <span class="glyphicon glyphicon-list-alt"></span> Consultar
              </a>
            <?php } ?>
          </h6>
        </td>

        <form action="" method="post">
        <!--<input class="btn btn-danger" type="submit" value="Eliminar" />-->
        <input type="hidden" value="OPP eliminado correctamente" name="mensaje" />
        <input type="hidden" value="1" name="opp_delete" />
        <input type="hidden" value="<?php echo $row_opp['idopp']; ?>" name="idopp" />
        </form>
        <!--</td>-->
    </tr>
    <?php }  ?>
    <? if($cont==0){?>
    <tr><td colspan="11" class="alert alert-info" role="alert">No se encontraron registros</td></tr>
    <? }?>
</table>
<table>
<tr>
<td width="20"><?php if ($pageNum_opp > 0) { // Show if not first page ?>
<a href="<?php printf("%s?pageNum_opp=%d%s", $currentPage, 0, $queryString_opp); ?>">
<span class="glyphicon glyphicon-fast-backward" aria-hidden="true"></span>
</a>
<?php } // Show if not first page ?></td>
<td width="20"><?php if ($pageNum_opp > 0) { // Show if not first page ?>
<a href="<?php printf("%s?pageNum_opp=%d%s", $currentPage, max(0, $pageNum_opp - 1), $queryString_opp); ?>">
<span class="glyphicon glyphicon-backward" aria-hidden="true"></span>
</a>
<?php } // Show if not first page ?></td>
<td width="20"><?php if ($pageNum_opp < $totalPages_opp) { // Show if not last page ?>
<a href="<?php printf("%s?pageNum_opp=%d%s", $currentPage, min($totalPages_opp, $pageNum_opp + 1), $queryString_opp); ?>">
<span class="glyphicon glyphicon-forward" aria-hidden="true"></span>
</a>
<?php } // Show if not last page ?></td>
<td width="20"><?php if ($pageNum_opp < $totalPages_opp) { // Show if not last page ?>
<a href="<?php printf("%s?pageNum_opp=%d%s", $currentPage, $totalPages_opp, $queryString_opp); ?>">
<span class="glyphicon glyphicon-fast-forward" aria-hidden="true"></span>
</a>
<?php } // Show if not last page ?></td>
</tr>
</table>
<?php
mysql_free_result($opp);
?>
