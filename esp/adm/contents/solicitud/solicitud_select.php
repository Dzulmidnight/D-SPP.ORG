<?php 
require_once('../Connections/dspp.php'); 
require_once('../Connections/mail.php');

//error_reporting(E_ALL ^ E_DEPRECATED);
mysql_select_db($database_dspp, $dspp);

if (!isset($_SESSION)) {
  session_start();
  
  $redireccion = "../index.php?ADM";

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
$fecha = time();

if(isset($_POST['aprobar_periodo']) && $_POST['aprobar_periodo'] == 1){
  $estatus_dspp = 6; //INICIA PERIODO DE OBJECIÓN
  $idperiodo_objecion = $_POST['idperiodo_objecion'];
  $estatus_objecion = "ACTIVO";
  
  //INSERTAMOS EL PROCESO DE CERTIFICACIÓN
  $insertSQL = sprintf("INSERT INTO proceso_certificacion (idsolicitud_certificacion, estatus_dspp, fecha_registro) VALUES (%s, %s, %s)",
    GetSQLValueString($_POST['idsolicitud_certificacion'], "int"),
    GetSQLValueString($estatus_dspp, "text"),
    GetSQLValueString($fecha, "int"));
  $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

  //ACTUALIZAMOS EL PERIODO DE OBJECIÓN
  $updateSQL = sprintf("UPDATE periodo_objecion SET estatus_objecion = %s WHERE idperiodo_objecion = %s",
    GetSQLValueString($estatus_objecion, "text"),
    GetSQLValueString($idperiodo_objecion, "int"));
 $actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());

}
//SE CARGA Y ENVIA LA RESOLUCIÓN DE OBJECIÓN
if(isset($_POST['enviar_resolucion']) && $_POST['enviar_resolucion'] == 1){
  $ruta = "../../archivos/admArchivos/resolucion/";

  if(!empty($_FILES['cargar_resolucion']['name'])){
    $_FILES['cargar_resolucion']['name'];
        move_uploaded_file($_FILES["cargar_resolucion"]["tmp_name"], $ruta.$fecha."_".$_FILES["cargar_resolucion"]["name"]);
        $resolucion = $ruta.basename($fecha."_".$_FILES["cargar_resolucion"]["name"]);
  }else{
    $resolucion = NULL;
  }
  //ACTUALIZAMOS EL PERIODO DE OBJECIÓN
  $estatus_objecion = 'FINALIZADO';


  $updateSQL = sprintf("UPDATE periodo_objecion SET estatus_objecion = %s, observacion = %s, dictamen = %s, documento = %s WHERE idperiodo_objecion = %s",
    GetSQLValueString($estatus_objecion, "text"),
    GetSQLValueString($_POST['observacion'], "text"),
    GetSQLValueString($_POST['dictamen'], "text"),
    GetSQLValueString($resolucion, "text"),
    GetSQLValueString($_POST['idperiodo_objecion'], "int"));
  $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

  $mensaje = "Se ha enviado correctamente la resolucion de objeción";

}


$row_solicitud = mysql_query("SELECT solicitud_certificacion.idsolicitud_certificacion AS 'idsolicitud', solicitud_certificacion.fecha_registro, opp.nombre AS 'nombre_opp', opp.abreviacion AS 'abreviacion_opp', proceso_certificacion.idproceso_certificacion, proceso_certificacion.estatus_interno, proceso_certificacion.estatus_dspp, estatus_dspp.nombre AS 'nombre_dspp', solicitud_certificacion.cotizacion_opp, periodo_objecion.* FROM solicitud_certificacion LEFT JOIN opp ON solicitud_certificacion.idopp = opp.idopp LEFT JOIN proceso_certificacion ON solicitud_certificacion.idsolicitud_certificacion = proceso_certificacion.idsolicitud_certificacion LEFT JOIN periodo_objecion ON solicitud_certificacion.idsolicitud_certificacion = periodo_objecion.idsolicitud_certificacion LEFT JOIN estatus_dspp ON proceso_certificacion.estatus_dspp = estatus_dspp.idestatus_dspp ORDER BY proceso_certificacion.idproceso_certificacion DESC LIMIT 1", $dspp) or die(mysql_error());

?>
<div class="row">
  <div class="col-md-12">
    <table class="table table-bordered" style="font-size:12px">
      <thead>
        <tr>
          <th class="text-center">ID</th>
          <th class="text-center">Fecha Solicitud</th>
          <th class="text-center">Organización</th>
          <th class="text-center">Estatus Solicitud</th>
          <th class="text-center">Cotización</th>
          <th class="text-center">Proceso de Objeción</th>
          <th class="text-center">Proceso Certificación</th>
          <th class="text-center">Certificado</th>
          <!--<th class="text-center">Observaciones Solicitud</th>-->
          <th class="text-center">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <form action="" method="POST" enctype="multipart/form-data">
          <?php 
          while($solicitud = mysql_fetch_assoc($row_solicitud)){
          ?>
            <tr>
              <td>
                <?php echo $solicitud['idsolicitud']; ?>
                <input type="hidden" name="idsolicitud_certificacion" value="<?php echo $solicitud['idsolicitud']; ?>">
              </td>
              <td><?php echo date('d/m/Y',$solicitud['fecha_registro']); ?></td>
              <td><?php echo $solicitud['abreviacion_opp']; ?></td>
              <td><?php echo $solicitud['nombre_dspp']; ?></td>
              <td>
              <?php
              if(isset($solicitud['cotizacion_opp'])){
                 echo "<a class='btn btn-success form-control' style='color:white;height:30px;' href='".$solicitud['cotizacion_opp']."' target='_blank'><span class='glyphicon glyphicon-download' aria-hidden='true'></span> Descargar Cotización</a>";
                 if($solicitud['estatus_dspp'] == 5){ // SE ACEPTA LA COTIZACIÓN
                  echo "<p class='alert alert-success' style='padding:7px;'>Estatus: ".$solicitud['nombre_dspp']."</p>"; 
                 }else if($solicitud['estatus_dspp'] == 17){ // SE RECHAZA LA COTIZACIÓN
                  echo "<p class='alert alert-danger' style='padding:7px;'>Estatus: ".$solicitud['nombre_dspp']."</p>"; 
                 }else{
                  echo "<p class='alert alert-info' style='padding:7px;'>Estatus: ".$solicitud['nombre_dspp']."</p>"; 
                 }

              }else{ // INICIA CARGAR COTIZACIÓN
                echo "No Disponible";
              } // TERMINA CARGAR COTIZACIÓN
               ?>
              </td>
              <td>
                <?php 
                // //CHECAMOS SI LA HORA ACTUAL ES IGUAL o MAYOR A LA FECHA_FINAL DEL PERIODO DE OBJECION
                if(isset($solicitud['idperiodo_objecion']) && $solicitud['estatus_objecion'] == 'ACTIVO'){
                  if($fecha > $solicitud['fecha_fin']){
                    $estatus_dspp = 7; //TERMINA PERIODO DE OBJECIÓN
                    $estatus_objecion = 'FINALIZADO';
                    //INSERTARMOS PROCESO_CERTIFICACION
                    $insertSQL = sprintf("INSERT INTO proceso_certificacion (idsolicitud_certificacion, estatus_dspp, fecha_registro) VALUES(%s, %s, %s)",
                      GetSQLValueString($solicitud['idsolicitud'], "int"),
                      GetSQLValueString($estatus_dspp, "int"),
                      GetSQLValueString($fecha, "int"));
                    $insertar = mysql_query($insertSQL,$dspp) or die(mysql_error());
                    //ACTUALIZAMOS EL PERIODO_OBJECION
                    $updateSQL = sprintf("UPDATE periodo_objecion SET estatus_objecion = %s WHERE idperiodo_objecion = %s",
                      GetSQLValueString($estatus_objecion, "text"),
                      GetSQLValueString($solicitud['idperiodo_objecion'], "int"));
                    $actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());

                  }
                }
                if(isset($solicitud['idperiodo_objecion'])){
                ?>
                  <button type="button" class="btn btn-sm btn-primary" style="width:100%" data-toggle="modal" data-target="<?php echo "#objecion".$solicitud['idperiodo_objecion']; ?>">Proceso Objeción</button>
                <?php
                }else{
                  echo "<button class='btn btn-sm btn-default' style='width:100%' disabled>Consultar Proceso</button>";
                }
                 ?>
                <!-- INICIA MODAL PROCESO DE OBJECIÓN -->

                <div id="<?php echo "objecion".$solicitud['idperiodo_objecion']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                  <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Proceso de Objeción</h4>
                      </div>
                      <div class="modal-body">
                        <div class="row">
                          <div class="col-md-6">
                            <h4>Periodo de Objeción <small>(<?php echo $solicitud['estatus_objecion']; ?>)</small></h4>
                            <p class="alert alert-info" style="padding:7px;">Inicio: <?php echo date('d/m/Y',$solicitud['fecha_inicio']); ?></p>
                            <p class="alert alert-danger" style="padding:7px;">Fin: <?php echo date('d/m/Y',$solicitud['fecha_fin']); ?></p>
                            <?php 
                            if($solicitud['estatus_objecion'] == 'EN ESPERA'){
                              echo '<button type="submit" class="btn btn-success" name="aprobar_periodo" value="1">Aprobar Periodo</button>';
                            }
                            ?>
                          </div>

                          <div class="col-md-6">
                            <?php 
                            if($solicitud['estatus_objecion'] == 'FINALIZADO'){
                            ?>
                              <h4>Resolución de Objeción</h4>
                              <p class="alert alert-info" style="padding:7px;">
                                <b style="margin-right:10px;">Dictamen:</b>
                                <?php 
                                if(empty($solicitud['dictamen'])){
                                ?>
                                  <label class="radio-inline">
                                    <input type="radio" name="dictamen" id="positivo" value="POSITIVO"> Positivo
                                  </label>
                                  <label class="radio-inline">
                                    <input type="radio" name="dictamen" id="negativo" value="NEGATIVO"> Negativo
                                  </label>
                                <?php
                                }else{
                                  echo "<span style='color:#c0392b'>".$solicitud['dictamen']."</span>";
                                }
                                 ?>
                              </p>
                              <label for="observacion">Observaciones</label>
                              <?php 
                              if(empty($solicitud['observacion'])){
                                echo '<textarea name="observacion" id="observacion" class="form-control"></textarea>';
                              }else{
                                echo "<p style='color:#c0392b'>".$solicitud['observacion']."</p>";
                              }

                              if(empty($solicitud['documento'])){
                              ?>
                                <label for="cargar_resolucion">Cargar Resolución</label>
                                <input type="file" class="form-control" id="cargar_resolucion" name="cargar_resolucion" >

                                <button type="submit" class="btn btn-success" style="width:100%" name="enviar_resolucion" value="1">Enviar Resolución</button>
                              <?php
                              }else{
                                echo "<a href='".$solicitud['documento']."' class='btn btn-info' style='width:100%' target='_blank'>Descargar Resolución</a>";
                              }
                               ?>
                            <?php
                            }else{
                              echo "<p class='alert alert-warning'><strong>Una vez finalizado el Periodo de Objeción podra cargar la resolución del mismo</strong></p>";
                            }
                             ?>
                          </div>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <input type="text" name="idperiodo_objecion" value="<?php echo $solicitud['idperiodo_objecion']; ?>">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        <!--<button type="button" class="btn btn-primary">Guardar Cambios</button>-->
                      </div>
                    </div>
                  </div>
                </div>
                <!-- TERMINA MODAL PROCESO DE OBJECIÓN -->

              </td>
              <!----- INICIA PROCESO CERTIFICACIÓN ---->
              <td>
                <button type="button" class="btn btn-sm btn-primary" style="width:100%" data-toggle="modal" data-target="<?php echo "#certificacion".$solicitud['idperiodo_objecion']; ?>">Proceso Certificación</button>
              </td>

                <!-- inicia modal proceo de certificación -->

                <div id="<?php echo "certificacion".$solicitud['idperiodo_objecion']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                  <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Proceso de Certificación</h4>
                      </div>
                      <div class="modal-body">
                        <div class="row">

                            <div class="col-md-12">
                              Historial Estatus Certificación
                            </div>
                            <?php 
                            $row_proceso_certificacion = mysql_query("SELECT proceso_certificacion.*, estatus_interno.nombre FROM proceso_certificacion INNER JOIN estatus_interno ON proceso_certificacion.estatus_interno = estatus_interno.idestatus_interno WHERE idsolicitud_certificacion = $solicitud[idsolicitud] AND estatus_interno IS NOT NULL", $dspp) or die(mysql_error());
                            while($historial_certificacion = mysql_fetch_assoc($row_proceso_certificacion)){
                            echo "<div class='col-md-10'>Proceso: $historial_certificacion[nombre]</div>";
                            echo "<div class='col-md-2'>Fecha: ".date('d/m/Y',$historial_certificacion['fecha_registro'])."</div>";
                            }
                             ?>

                        </div>
                      </div>
                      <div class="modal-footer">
                        <input type="text" name="idperiodo_objecion" value="<?php echo $solicitud['idperiodo_objecion']; ?>">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        <!--<button type="button" class="btn btn-primary">Guardar Cambios</button>-->
                      </div>
                    </div>
                  </div>
                </div>
                <!-- termina modal proceo de certificación -->

              <!----- TERMINA PROCESO CERTIFICACIÓN ---->
              <td><?php echo "ID CERTIFICADO"; ?></td>
              <!--<td><?php echo $solicitud['observaciones']; ?></td>-->
              <td>Acciones</td>
            </tr>
          <?php
          }
           ?>
        </form>
      </tbody>
    </table>
  </div>
</div>