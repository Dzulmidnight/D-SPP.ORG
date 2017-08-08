<?php require_once('../Connections/dspp.php'); ?>
<?php
  error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

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

  $theValue = function_exissts("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

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

$query_limit_opp = sprintf("%s LIMIT %d, %d", $query_opp, $startRow_opp, $maxRows_opp);
$opp = mysql_query($query_limit_opp,$dspp) or die(mysql_error());
//$row_opp = mysql_fetch_assoc($opp);


if(isset($_POST['comprobanteMembresia']) && $_POST['comprobanteMembresia'] == "2"){
    $idsolicitud_certificacion = $_POST['idsolicitud'];
    $statusInterno = 10;
    $idexterno = $_POST['idmembresia'];
    $fecha = $_POST['fecha'];
    $identificador = "membresia";
    $idcertificado = $_POST['idcertificado'];

  if(isset($_POST['aprobar'])){
    $status = "APROBADO";
    $insertar = "INSERT INTO fecha (fecha,idexterno,identificador,status) VALUES ($fecha,$idexterno,'$identificador','$status')";
    $ejecutar = mysql_query($insertar,$dspp) or die(mysql_error());
    $actualizar = "UPDATE certificado SET statuspago = '$status' WHERE idcertificado = $idcertificado";
    $ejecutar = mysql_query($actualizar,$dspp) or die(mysql_error());
    //echo $insertar;
    //echo "<br>".$actualizar; 
    $update = "UPDATE solicitud_certificacion SET status = '$statusInterno' WHERE idsolicitud_certificacion = $idsolicitud_certificacion";
    $actualizar = mysql_query($update,$dspp);
  }
  if(isset($_POST['denegar'])){
    $status = "DENEGAR";
    $insertar = "INSERT INTO fecha (fecha,idexterno,identificador,status) VALUES ($fecha,$idexterno,'$identificador','$status')";
    $ejecutar = mysql_query($insertar,$dspp) or die(mysql_error());
    $actualizar = "UPDATE certificado SET statuspago = '$status' WHERE idcertificado = $idcertificado";
    $ejecutar = mysql_query($actualizar,$dspp) or die(mysql_error());

    //echo $insertar; 
    //echo "<br>".$actualizar; 
  }
}



if (isset($_POST['insertarObjecion']) && $_POST['insertarObjecion'] == "periodoObjecion") {

  $fechaInicio = $_POST['fechaInicio'];
  $fechaFin = $_POST['fechaFin'];
  $status = $_POST['statusObjecion_hdn'];

  $observacion = $_POST['observacion_txt'];
  $idopp = $_POST['objecionIdOpp_hdn'];
  $idoc = $_POST['objecionIdOc_hdn'];
  $idsolicitud_certificacion = $_POST['idsolicitud'];

  $query = "INSERT INTO objecion (fechainicio, fechafin, status, adjunto, observacion, idsolicitud) VALUES ('$fechaInicio', '$fechaFin', '$status', '$adjunto', '$observacion', $idsolicitud_certificacion)";

  $insertarQuery = mysql_query($query, $dspp) or die(mysql_error());

  $query = "UPDATE solicitud_certificacion SET status_publico = '$status'";
  $insertar = mysql_query($query,$dspp) or die(mysql_error());

}

if(isset($_POST['resolucionObjecion']) && $_POST['resolucionObjecion'] == "resolucionObjecion"){

  $ruta = "archivos/";
  $idobjecion = $_POST['idobjecion'];

  if(!empty($_FILES['adjunto_fld']['name'])){
    $_FILES['adjunto_fld']['name'];
        move_uploaded_file($_FILES["adjunto_fld"]["tmp_name"], $ruta.time()."_".$_FILES["adjunto_fld"]["name"]);
        $objecionAdjunto = $ruta.basename(time()."_".$_FILES["adjunto_fld"]["name"]);
  }else{
    $objecionAdjunto = NULL;
  }

  $statusObjecion = $_POST['statusObjecion_hdn'];
  $adjunto = $objecionAdjunto;
  $statusInterno = $_POST['statusInterno'];

  $query = "UPDATE objecion SET status = '$statusObjecion', adjunto = '$adjunto' WHERE idobjecion = $idobjecion";
  $insertarResolucion = mysql_query($query, $dspp) or die(mysql_error());

  $query = "UPDATE solicitud_certificacion SET status = '$statusInterno', status_publico = '$statusObjecion'";
  $insertar = mysql_query($query,$dspp) or die(mysql_error());
}


if(isset($_GET['query'])){
  $idoc = $_GET['query'];
  
  $query_buscar = "SELECT opp.* ,solicitud_certificacion.* FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE solicitud_certificacion.idoc = $idoc ORDER BY solicitud_certificacion.fecha_elaboracion DESC";

       $ejecutar_busqueda = mysql_query($query_buscar, $dspp) or die(mysql_error());

}

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


///////////////////////////////////// VARIABLES DE CONTROL ////////////////////////////////////////

  $validacionStatus = $registro_busqueda['status'] != 1 && $registro_busqueda['status'] != 2 && $registro_busqueda['status'] != 3 && $registro_busqueda['status'] != 14 && $registro_busqueda['status'] != 15;

///////////////////////////////////// VARIABLES DE CONTROL ////////////////////////////////////////

?>



<hr>
<div class="table-responsive">
  <table class="table table-condensed table-bordered table-hover">
      <tr class="success">
        <th class="text-center"><h5><b>Ultima <br>Actualización</b></h5></th>
        <th class="text-center">Nombre</th>
        <th class="text-center" colspan="2">Cotizaciones</th>
        <!--<th class="text-center">Sitio WEB</th>-->
        <th class="text-center">Email</th>
        <th class="text-center">País</th>
        <th class="text-center">Status <br>Publico</th>
        <th class="text-center">Status <br>Interno</th>
        <th class="text-center">Propuesta</th>
        <th class="text-center" colspan="2">Periodo de Objeción</th>
        <th class="text-center">Certificado/<br>Membresia</th>
        <th class="text-center">Observaciones</th>
        <!--<th>OC</th>
        <th>Razón social</th>
        <th>Dirección fiscal</th>
        <th>RFC</th>-->
        <!--<th>Eliminar</th>-->
      </tr>

      <?php mysql_select_db($database_dspp, $dspp); ?>


      <?php $cont=0; while($registro_busqueda = mysql_fetch_assoc($ejecutar_busqueda)){ $cont++;?>
        <tr>
      <?php  $fecha = $registro_busqueda['fecha_elaboracion']; ?> 

          <!---------------------------------------- SECCION ULTIMA ACTUALIZACION -------------------------------------->
          <td>
            <h6>
              <a class="btn btn-primary btn-sm" style="width:100%" href="?OC&amp;detailBlock&amp;query=<?php echo $registro_busqueda['idoc']; ?>&amp;formato=<?php echo $registro_busqueda['idsolicitud_certificacion']; ?>">
                <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> <?php echo  date("d/m/Y", $fecha); ?>
              </a> 
            </h6>         
          </td>
          <!---------------------------------------- SECCION ULTIMA ACTUALIZACION -------------------------------------->

          <!---------------------------------------- SECCION  NOMBRE -------------------------------------->
          <td>
            <h6 class="text-center">
              <?php 
                if(isset($registro_busqueda['nombre'])){
                  echo "<p class='alert alert-success'>".$registro_busqueda['nombre']."</p>";
                }else{
                  echo "<p class='alert alert-danger'>No Disponible</p>";
                } 
              ?>
            </h6>
          </td>
          <!---------------------------------------- SECCION  NOMBRE -------------------------------------->


        <!----------------------------------------- COTIZACION OPP ---------------------------------------------->
        <td>
          <h6>
            <a href="http://d-spp.org/oc/<?echo $registro_busqueda['cotizacion_opp']?>" target="_blank" type="button" class="btn <?php if(empty($registro_busqueda['cotizacion_opp'])){ echo 'btn-danger btn-sm';}else{echo 'btn-success btn-sm';} ?>" aria-label="Left Align" <?php if(empty($registro_busqueda['cotizacion_opp'])){echo "disabled";}?>>
              <span class="glyphicon glyphicon-download-alt"></span> OPP
            </a> 
          </h6>
        </td>
        <!----------------------------------------- COTIZACION OPP ---------------------------------------------->


        <!----------------------------------------- COTIZACION FUNDEPPO ---------------------------------------------->
        <td>
          <h6>
            <a href="http://d-spp.org/oc/<?echo $registro_busqueda['cotizacion_adm']?>" target="_blank" type="button" class="btn <?php if(empty($registro_busqueda['cotizacion_adm'])){ echo 'btn-danger btn-sm';}else{echo 'btn-success btn-sm';} ?>" aria-label="Left Align" <?php if(empty($registro_busqueda['cotizacion_adm'])){echo "disabled";}?>>
              <span class="glyphicon glyphicon-download-alt"></span> FUNDEPPO
            </a> 
          </h6>       
        </td>
        <!----------------------------------------- COTIZACION FUNDEPPO ---------------------------------------------->



          <!--<td>
            <small><?php if(empty($registro_busqueda['sitio_web'])){echo "Sitio Web no disponible";}else{echo $registro_busqueda['sitio_web'];} ?></small>
          </td>-->

          <!---------------------------- SECCION EMAIL ---------------------------->
          <td>
            <h6 class="text-center">
              <?php 
                if(isset($registro_busqueda['p1_email'])){
                  echo "<p class='alert alert-success'>".$registro_busqueda['p1_email']."</p>";
                }else{
                  echo "<p class='alert alert-danger'>No Disponible</p>";
                } 
              ?>
            </h6>
          </td>
          <!---------------------------- SECCION EMAIL ---------------------------->

          <!---------------------------- SECCION PAIS ---------------------------->
          <td>
            <h6 class="text-center">
              <?php 
                if(isset($registro_busqueda['pais'])){
                  echo "<p class='alert alert-success'>".$registro_busqueda['pais']."</p>";
                }else{
                  echo "<p class='alert alert-danger'>No Disponible</p>";
                } 
              ?>
            </h6>
          </td>
          <!---------------------------- SECCION PAIS ---------------------------->

          <!------------------------------------ SECCION STATUS PUBLICO ------------------------------------>
          <td>
            <?php 
              $query_status = "SELECT * FROM status_publico WHERE idstatus_publico = $registro_busqueda[status_publico]";
              $ejecutar = mysql_query($query_status,$dspp) or die(mysql_error());
              $estatus_publico = mysql_fetch_assoc($ejecutar);
             ?>

            <h6>
              <?php 
                if($registro_busqueda['status'] == 10){
                  echo "<p class='text-center alert alert-success'><b><u>Certificado</u></b></p>";
                }else{
                   echo "<p class='text-center alert alert-warning'>".$estatus_publico['nombre']."</p>"; 
                }
              ?>
            </h6>
          </td>
          <!------------------------------------ SECCION STATUS PUBLICO ------------------------------------>


          <!------------------------------------ SECCION STATUS INTERNO ------------------------------------>
          <td>
            <?php 
              $query_status = "SELECT * FROM status WHERE idstatus = $registro_busqueda[status]";
              $ejecutar = mysql_query($query_status,$dspp) or die(mysql_error());
              $estatus_interno = mysql_fetch_assoc($ejecutar);

              if($registro_busqueda['status'] == 4 || $registro_busqueda['status'] == 11 || $registro_busqueda['status'] == 13 || $registro_busqueda['status'] == 14 || $registro_busqueda['status'] == 15){
                $colorEstado = "class='text-center alert alert-danger'";
              }else if($registro_busqueda['status'] == 10){
                $colorEstado = "class='text-center alert alert-success'";
              }else{
                $colorEstado = "class='text-center alert alert-warning'";
              }
             ?>

            <h6 <?echo $colorEstado;?>>
              <?php echo $estatus_interno['nombre']; ?>
            </h6>
          </td>
          <!------------------------------------ SECCION STATUS INTERNO ------------------------------------>


          <!--------------------------------------- SECCION PROPUESTA -------------------------------------->
          <td>
            <?php 
              if($registro_busqueda['status'] != 1 && $registro_busqueda['status'] != 2 && $registro_busqueda['status'] != 3 && $registro_busqueda['status'] != 14 && $registro_busqueda['status'] != 15 && $registro_busqueda['status'] != 17){
            ?>
              <h6 class="alert alert-success">Aceptada</h6>
                  <!--<button class="btn btn-success btn-sm" disabled>
                    <span class="glyphicon glyphicon-check" aria-hidden="true"></span> Aceptada
                  </button>-->          
              <?php 
                }else{
              ?>
                <h6 class="alert alert-danger">Pendiente</h6>
                  <!--<button class="btn btn-default btn-sm" disabled>
                    <span class="glyphicon glyphicon-check" aria-hidden="true"></span> Aceptada
                  </button> -->              
            <?php 
              }
            ?>              
          </td>
          <!--------------------------------------- SECCION PROPUESTA -------------------------------------->

              <!-- consulta sobre los datos de objecion -->
              <?php 
               // $queryObjecion = "SELECT * FROM objecion WHERE idopp = $registro_busqueda[idopp] AND idoc = $registro_busqueda[idoc]";
                $queryObjecion = "SELECT * FROM objecion WHERE idsolicitud = $registro_busqueda[idsolicitud_certificacion]";
                $resultado = mysql_query($queryObjecion,$dspp) or die(mysql_error());

                $resultado2 = mysql_fetch_assoc($resultado);

               ?>
              <!-- consulta sobre los datos de objecion -->

            <!--INICIA PERIODO OBJECIÓN-->
          <?php if($registro_busqueda['status'] != 1 && $registro_busqueda['status'] != 2 && $registro_busqueda['status'] != 3 && $registro_busqueda['status'] != 14 && $registro_busqueda['status'] != 15 && $registro_busqueda['status'] != 17){ ?>

          <!-------------------- INICIA SECCION PERIODO DE OBJECION -------------------->
            <td>
              <h6>
                <button <?if(isset($resultado2['idobjecion'])){echo "class='btn btn-success btn-sm'";}else{echo "class='btn btn-danger btn-sm'";}?> data-toggle="modal" <?php echo "data-target='#myModal".$registro_busqueda['idsolicitud_certificacion']."'"?> >
                  <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Periodo Objeción
                </button>
              </h6>
                          <?php 
                            $queryCertificado = "SELECT * FROM certificado WHERE idsolicitud = $registro_busqueda[idsolicitud_certificacion]";
                            $ejecutarCertificado = mysql_query($queryCertificado,$dspp) or die(mysql_error());
                            $certificado = mysql_fetch_assoc($ejecutarCertificado);
                            $num = mysql_num_rows($ejecutarCertificado);

                            if($num != 0 && isset($certificado['statuspago'])){

                              $queryMembresia = "SELECT membresia.*, fecha.*, MAX(fecha.fecha) AS 'ultimafecha' FROM membresia INNER JOIN fecha ON membresia.idmembresia = fecha.idexterno WHERE membresia.idopp = $registro_busqueda[idopp] AND fecha.identificador = 'membresia'";
                              $ejecutar = mysql_query($queryMembresia, $dspp) or die(mysql_error());

                           
                                $membresia = mysql_fetch_assoc($ejecutar);

                                if(isset($membresia['idmembresia'])){
                                  $queryStatus = "SELECT * FROM fecha WHERE fecha = $membresia[ultimafecha]";
                                  $eje = mysql_query($queryStatus,$dspp) or die(mysql_error());
                                  $registroStatus = mysql_fetch_assoc($eje);
                                }
                            
                            }                           
                           ?>


              <!-- Modal -->
              <form action="" method="post" id="periodoObjecion" enctype="application/x-www-form-urlencoded">
                <div class="modal fade" <?php echo "id='myModal".$registro_busqueda['idsolicitud_certificacion']."'" ?> tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Periodo de Objeción</h4>
                      </div>
                      <div class="modal-body">
                        <div class="row">
                          <div class="col-xs-12">
                            <div class="col-xs-6 form-group has-success">
                              <label class="control-label" for="inputSuccess1">Observaciones</label>
                              <?php if(isset($resultado2['observacion'])){ ?>
                                <textarea name="observacion_txt" class="form-control" id="inputSuccess1" cols="7" rows="7" disabled><?php echo $resultado2['observacion']; ?></textarea>
                              <?php }else{ ?>
                                <textarea name="observacion_txt" class="form-control" id="inputSuccess1" cols="7" rows="7"></textarea>
                              <?php } ?>
                              
                            </div>
                            <div class="col-xs-6">
                              <label class="control-label" for="fechaInicio">Fecha de Inicio</label>
                              <?php if(isset($resultado2['fechainicio'])){ ?>
                                <input class="form-control" name="fechaInicio" id="fechaInicio" type="date" placeholder="dd/mm/aaaa" value="<?echo $resultado2['fechainicio'];?>" disabled>
                              <?php }else{ ?>
                                <input class="form-control" name="fechaInicio" id="fechaInicio" type="date" placeholder="dd/mm/aaaa" required>
                              <?php } ?>
                              <hr>
                              <label class="control-label" for="fechaFin">Fecha Final</label>
                              <?php if(isset($resultado2['fechainicio'])){ ?>
                                <input class="form-control" name="fechaFin" id="fechaFin" type="date" placeholder="dd/mm/aaaa" value="<?echo $resultado2['fechafin'];?>" disabled>
                              <?php }else{ ?>
                                <input class="form-control" name="fechaFin" id="fechaFin" type="date" placeholder="dd/mm/aaaa" required>
                              <?php } ?>                                                          
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                        <?php if(empty($resultado2['fechainicio'])){ ?>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <?php } ?>

                        <input type="hidden" name="objecionIdOpp_hdn" value="<?php echo $registro_busqueda['idopp'];?>">
                        <input type="hidden" name="objecionIdOc_hdn" value="<?php echo $registro_busqueda['idoc'];?>">
                        <input type="hidden" name="statusObjecion_hdn" value="6">
                        <input type="hidden" name="insertarObjecion" value="periodoObjecion">
                        <input type="hidden" name="idsolicitud" value="<?echo $registro_busqueda['idsolicitud_certificacion'];?>">

                      </div>
                    </div>
                  </div>
                </div>
              </form>
              <!-- Modal -->
            </td>
            <!------------------------------- INICIA SECCION RESOLUCIÓN DE OBJECION ------------------------------>
            <td>
              <?php if(isset($resultado2['status']) && $resultado2['status'] == "6" || $resultado2['status'] == "7"){ ?>
                <h6>
                  <button <?if(!empty($resultado2['adjunto'])){echo "class='btn btn-success btn-sm'";}else{echo "class='btn btn-danger btn-sm'";}?> data-toggle="modal" <?php echo "data-target='#resolucion".$registro_busqueda['idsolicitud_certificacion']."'"?>>
                    <span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Resolución <br>de Objeción
                  </button>
                </h6>
              <?php }else{ ?>
                <h6>
                  <button class="btn btn-danger btn-sm" data-toggle="modal" <?php echo "data-target='#resolucion".$registro_busqueda['idsolicitud_certificacion']."'"?> disabled>
                    <span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Resolución <br>de Objeción
                  </button>
                </h6>
              <?php } ?>
      
              <?php 
                if(isset($resultado2['idobjecion'])){
               ?>    
              <!-- Modal -->
              <form action="" method="POST" enctype="multipart/form-data">
                
                <div class="modal fade" <?php echo "id='resolucion".$registro_busqueda['idsolicitud_certificacion']."'" ?> tabindex="-1" role="dialog" aria-labelledby="resolucionLabel">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="resolucionLabel">Resolución de Objeción</h4>
                      </div>

                      <div class="modal-body">
                        <div class="row">
                          <div class="col-xs-12">
                            <div class="col-xs-6">
                              <h4 class="control-label" for="status">Status</h4>

                              <?php 
                                $query = "SELECT * FROM status_publico WHERE idstatus_publico = $resultado2[status]";
                                $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
                                $statusResolucion = mysql_fetch_assoc($ejecutar);
                              ?>
                                <p name="status" class="alert alert-success">

                                  <? echo $statusResolucion['nombre'];?>
                                </p>

                            </div>
                            <div class="col-xs-6">
                              <?php if(!empty($resultado2['adjunto'])){ ?>
                                <h4 class="control-label" for="descarga">Descargar Resolución</h4>
                                <br>
                                <a class="col-xs-12 btn btn-info" style="margin-top:-10px;" role="button" name="descarga" href="<?echo $resultado2['adjunto']?>"><span aria-hidden="true" class="glyphicon glyphicon-download-alt"></span> Descargar</a>
                              <?php }else{ ?>
                                <h4 class="control-label" for="">Adjuntar archivo</h4>
                                <input name="adjunto_fld" id="adjunto_fld" type="file" class="filestyle" data-buttonName="btn-info" data-buttonBefore="true" data-buttonText="Cargar Archivo" required> 
                              <?php } ?>     
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                        <?php if(empty($resultado2['adjunto'])){ ?>
                            <button type="submit" class="btn btn-primary">Finalizar</button>
                        <?php } ?>
                          <input type="hidden" name="objecionIdOpp_hdn" value="<?php echo $registro_busqueda['idopp'];?>">
                          <input type="hidden" name="objecionIdOc_hdn" value="<?php echo $registro_busqueda['idoc'];?>">
                          <input type="hidden" name="statusObjecion_hdn" value="7">
                          <input type="hidden" name="statusInterno" value="19">
                          <input type="hidden" name="resolucionObjecion" value="resolucionObjecion">
                          <input type="hidden" name="idobjecion" value="<? echo $resultado2['idobjecion'];?>">
                          <input type="hidden" name="idsolicitud" value="<?echo $registro_busqueda['idsolicitud_certificacion'];?>">                       


                      </div>
                    </div>
                  </div>
                </div>

              </form>
              <!-- Modal -->
              <?php 
                } 
              ?>



            </td>
          <!-------------------- TERMINAR SECCION PERIODO DE OBJECION -------------------->

            <!--FIN PERIODO OBJECIÓN--> 
            
            <!--------------------------INICIA STATUS CERTIFICACION---------------------------->
             <td>
               <h6>
                <button class="btn btn-warning btn-sm" data-toggle="modal" <?php echo "data-target='#certificado".$registro_busqueda['idsolicitud_certificacion']."'"?>>
                  <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Status
                </button>
               </h6>     
            </td>

            <?php 
              $query = "SELECT * FROM fecha";
              $ejecutar1 = mysql_query($query,$dspp) or die(mysql_error());
              $numero = mysql_num_rows($ejecutar1);

              if($numero != 0 && isset($certificado['idcertificado'])){
                $queryFecha = "SELECT fecha.*, MAX(fecha) AS 'ultimaFecha' FROM fecha WHERE idexterno = $certificado[idcertificado] AND identificador = 'certificado'";
                $ejecutar = mysql_query($queryFecha,$dspp) or die(mysql_error());
                $registroFecha = mysql_fetch_assoc($ejecutar);
              }
             ?>
              <!-- Modal -->
              <form action="" method="POST" enctype="multipart/form-data">
                
                <div class="modal fade" <?php echo "id='certificado".$registro_busqueda['idsolicitud_certificacion']."'" ?> tabindex="-1" role="dialog" aria-labelledby="resolucionLabel">
                  <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="resolucionLabel">STATUS CERTIFICADO / MEMBRESIA</h4>
                      </div>

                      <div class="modal-body">
                        <div class="row">
                          <div class="col-xs-12">

                            <div class="col-xs-6">
                              <?php if(empty($certificado['status'])){ ?>
                                <?php if($registro_busqueda['status'] == 19){ ?>
                                  <div class="col-xs-12 alert alert-warning" role="alert">
                                    <div class="col-xs-12">
                                      Se ha iniciado la certificación.
                                    </div>        
                                  </div> 
                                <?php }else{ ?>
                                  <div class="col-xs-12 alert alert-danger" role="alert">
                                    <div class="col-xs-12">
                                      No se ha iniciado la certificación.
                                    </div>        
                                  </div> 
                                <?php } ?>
                              <?php }else{ ?>
                                <div class="col-xs-12 alert alert-success" role="alert">
                                  <div class="col-xs-12">Status Certificado al día: <b><?echo date("Y/m/d", $registroFecha['ultimaFecha']) ?></b></div>
                                  <hr>
                                  <div class="col-xs-12 ">
                                    <?
                                      $query = "SELECT * FROM status WHERE idstatus = $certificado[status]";
                                      $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
                                      $estatus = mysql_fetch_assoc($ejecutar);
                                    ?>
                                    <h4><?php echo $estatus['nombre']; ?></h4>
                                   
                                  </div>        
                                </div>                               
                              <?php } ?>
                              <?php if(empty($certificado['adjunto'])){ ?>
                                <div class="col-xs-12 alert alert-danger">
                                    <div class="col-xs-12"><h4>Certificado</h4></div>
                                    <hr>
                                    <div class="col-xs-12">No se ha finalizado el proceso de certificación</div>
                                </div>                               
                              <?php }else{ ?>
                                <div class="col-xs-12 alert alert-info">
                                    <div class="col-xs-12"><h4>Certificado</h4></div>
                                    <hr>
                                    <div class="col-xs-6">Su certificado vence el dia: <?echo $certificado['vigenciafin']?></div>
                                    <div class="col-xs-6">
                                      <a class="btn btn-success" href="http://d-spp.org/oc/<?echo $certificado['adjunto'];?>" target="_blank">Descargar Certificado</a>
                                    </div> 

                                </div> 
                              <?php } ?>   
                            </div>                              

                            <div class="col-xs-6">
                              <?php if(empty($certificado['adjunto'])){ ?>
                                <div class="col-xs-12 alert alert-danger">
                                  <p style="text-align:justify"><strong>No se ha completado el proceso de certificación, una vez completado se iniciara el proceso de pago de membresia.</strong></p>
                                </div>
                              <?php }else{ ?>
                                <?php if(empty($membresia['idmembresia'])){ ?>
                                  <div class="col-xs-12 alert alert-warning">
                                    <p><strong>No se ha realizado el pago correpondiente, intente revisar más tarde.</strong></p>
                                  </div>
                                <?php }else if(isset($membresia['adjunto'])){ ?>
                                  <div class="col-xs-12 alert alert-info">
                                    <h4>Membresia</h4>
                                    <hr>
                                    <div class="col-xs-8 alert alert-success">
                                      <div class="col-xs-12 text-center">
                                        Fecha: <?echo date("d/m/Y",$membresia['ultimafecha']);?>
                                      </div>
                                      <hr>
                                      <div class="col-xs-12">
                                        <small>Membresia: <strong><?echo $registroStatus['status'];?></strong></small>
                                      </div>
                                     
                                    </div>

                                    <div class="col-xs-4">
                                      <strong>Comprobante</strong>
                                      <a href="http://d-spp.org/opp/<?echo $membresia['adjunto']?>" class="btn btn-danger btn-sm" target="_blank">Descargar <br>Comprobante</a>
                                      
                                    </div>
                                    <?php if($registroStatus['status'] != "APROBADO"){ ?>
                                      <div class="col-xs-12">
                     
                                          <button class="btn btn-success btn-sm" type="submit" name="aprobar" value="aprobado">Aprobar</button>
                                          <button class="btn btn-danger btn-sm" typw="submit" name="denegar" value="denegado">Denegar</button>
                                          <input type="hidden" name="idmembresia" value="<?echo $membresia['idmembresia'];?>">
                                          <input type="hidden" name="idcertificado" value="<?echo $certificado['idcertificado']?>">
                                          <input type="hidden" name="statusInterno" value="10">
                                          <input type="hidden" name="fecha" value="<?echo time()?>">
                                          <input type="hidden" name="comprobanteMembresia" value="2">
                                      </div>                                    
                                    <?php } ?>
                                  </div>                                
                                <?php } ?>

                              <?php } ?>
                            </div>

                          </div>
                        </div>
                      </div>

                      <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                        <?php if(empty($resultado2['adjunto'])){ ?>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        <?php } ?>
                          <input type="hidden" name="objecionIdOpp_hdn" value="<?php echo $registro_busqueda['idopp'];?>">
                          <input type="hidden" name="objecionIdOc_hdn" value="<?php echo $registro_busqueda['idoc'];?>">
                          <input type="hidden" name="statusObjecion_hdn" value="Inicia Periodo de Objeción">
                          
                          <input type="hidden" name="idobjecion" value="<? echo $resultado2['idobjecion'];?>">
                          <input type="hidden" name="idsolicitud" value="<?echo $registro_busqueda['idsolicitud_certificacion'];?>">                          


                      </div>
                    </div>
                  </div>
                </div>

              </form>
              <!-- Modal -->


            <!---------------------------FIN STATUS CERTIFICACION---------------------------->
          <?php }else{ ?>
            <td>
                <!--<?php echo $registro_busqueda["idsolicitud_certificacion"]; ?>-->

              <h6>
                <button class="btn btn-sm btn-default" disabled>Periodo Objeción</button>
              </h6>
            </td>
            <td>
                <h6>
                  <button class="btn btn-default btn-sm" data-toggle="modal" <?php echo "data-target='#resolucion".$registro_busqueda['idsolicitud_certificacion']."'"?> disabled>
                    <span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Resolución <br>de Objeción
                  </button>
                </h6>
            </td>
            <!--FIN PERIODO OBJECIÓN--> 
            
            <!--INICIA CERTIFICACION-->
             <td>     
                <h6>
                  <button class="btn btn-default" data-toggle="modal" <?php echo "data-target='#myModal".$row_opp['idsolicitud_certificacion']."'"?> disabled>
                    <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Status
                  </button>
                </h6>    
            </td>

            <!--FIN CERTIFICACION-->          
          <?php } ?>
          <td>
            <h6>
              <?php if(empty($registro_busqueda['observaciones'])){ ?>
                <button class="btn btn-default btn-sm" disabled>
                  <span class="glyphicon glyphicon-list-alt"></span> Consultar
                </button>         
              <?php }else{ ?>
                <a class="btn btn-info btn-sm" href="?OC&amp;detailBlock&amp;query=<?php echo $registro_busqueda['idoc']; ?>&amp;formato=<?php echo $registro_busqueda['idsolicitud_certificacion']; ?>">
                  <span class="glyphicon glyphicon-list-alt"></span> Consultar
                </a>
              <?php } ?>
            </h6>
          </td>
        <!--<td>-->
        </tr>

      <?php } ?>

      <? if($cont==0){?>
      <tr><td colspan="12" class="alert alert-info" role="alert">No se encontraron registros</td></tr>

      <? }?>
  </table>
</div>



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



      <!--  $query_buscar = "SELECT solicitud_certificacion.* FROM solicitud_certificacion WHERE idopp = '$opp' AND idoc = '$oc' AND FROM_UNIXTIME(fecha_elaboracion, '%d/%m/%Y' ) = '$fecha'";