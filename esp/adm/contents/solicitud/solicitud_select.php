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
    GetSQLValueString($estatus_dspp, "int"),
    GetSQLValueString($fecha, "int"));
  $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

  //ACTUALIZAMOS EL PERIODO DE OBJECIÓN
  $updateSQL = sprintf("UPDATE periodo_objecion SET estatus_objecion = %s WHERE idperiodo_objecion = %s",
    GetSQLValueString($estatus_objecion, "text"),
    GetSQLValueString($idperiodo_objecion, "int"));
 $actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());

 $mensaje = "Se ha iniciado el Periodo de Objeción";

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
//SE APRUEBA EL COMPROBANTE DE PAGO
if(isset($_POST['aprobar_comprobante']) && $_POST['aprobar_comprobante'] == 1){
  $estatus_comprobante = "ACEPTADO"; //se acepta el comprobante
  $estatus_membresia = "APROBADA"; //se acepta la membresia
  $estatus_dspp = 18; //MEMBRESIA APROBADA
  //actualizamos comprobante_pago
  $updateSQL = sprintf("UPDATE comprobante_pago SET estatus_comprobante = %s WHERE idcomprobante_pago = %s",
    GetSQLValueString($estatus_comprobante, "text"),
    GetSQLValueString($_POST['idcomprobante_pago'], "int"));
  $actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());
  //actualizamos la membresia
  $updateSQL = sprintf("UPDATE membresia SET estatus_membresia = %s, fecha_registro = %s WHERE idmembresia = %s",
    GetSQLValueString($estatus_membresia, "text"),
    GetSQLValueString($fecha, "int"),
    GetSQLValueString($_POST['idmembresia'], "int"));
  $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

  //insertarmos el proceso_certificacion
  $insertSQL = sprintf("INSERT INTO proceso_certificacion(idsolicitud_certificacion, estatus_dspp, fecha_registro) VALUES (%s, %s, %s)",
    GetSQLValueString($_POST['idsolicitud_certificacion'], "int"),
    GetSQLValueString($estatus_dspp, "int"),
    GetSQLValueString($fecha, "int"));
  $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

  $mensaje = "Se ha aprobado la membresia";

}
//SE RECHAZA EL COMPROBANTE DE PAGO
if(isset($_POST['rechazar_comprobante']) && $_POST['rechazar_comprobante'] == 2){
  $estatus_comprobante = "RECHAZADO"; //se rechaza el comprobante
  $estatus_membresia = "RECHAZADO"; //se rechaza la membresia
  //actualizamos comprobante_pago
  $updateSQL = sprintf("UPDATE comprobante_pago SET estatus_comprobante = %s, observaciones = %s WHERE idcomprobante_pago = %s",
    GetSQLValueString($estatus_comprobante, "text"),
    GetSQLValueString($_POST['observaciones_comprobante'], "text"),
    GetSQLValueString($_POST['idcomprobante_pago'], "int"));
  $actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());
  //actualizamos la membresia
  $updateSQL = sprintf("UPDATE membresia SET estatus_membresia = %s WHERE idmembresia = %s",
    GetSQLValueString($estatus_membresia, "text"),
    GetSQLValueString($_POST['idmembresia'], "int"));
  $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

  $mensaje = "Se ha rechaza la membresia y el OPP ha sido notificado";
}

//SE APRUEBA EL CONTRATO DE USO
if(isset($_POST['aprobar_contrato']) && $_POST['aprobar_contrato'] == 1){
  $estatus_dspp = 19; //CONTRATO DE USO APROBADO
  $estatus_contrato = "ACEPTADO";;
  //actualizamos el contrato de uso
  $updateSQL = sprintf("UPDATE contratos SET estatus_contrato = %s WHERE idcontrato = %s",
    GetSQLValueString($estatus_contrato, "text"),
    GetSQLValueString($_POST['idcontrato'], "int"));
  $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

  //creamos el proceso_certificacion
  $insertSQL = sprintf("INSERT INTO proceso_certificacion(idsolicitud_certificacion, estatus_dspp, fecha_registro) VALUES(%s, %s, %s)",
    GetSQLValueString($_POST['idsolicitud_certificacion'], "int"),
    GetSQLValueString($estatus_dspp, "int"),
    GetSQLValueString($fecha, "int"));
  $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

  $mensaje = "Se ha aprobado el \"Contrato de Uso\"";

}

//SE RECHAZA EL CONTRATO DE USO
if(isset($_POST['rechazar_contrato']) && $_POST['rechazar_contrato'] == 2){
  $estatus_dspp = 19; //CONTRATO DE USO APROBADO
  $estatus_contrato = "RECHAZADO";;
  //actualizamos el contrato de uso
  $updateSQL = sprintf("UPDATE contratos SET estatus_contrato = %s, observaciones = %s WHERE idcontrato = %s",
    GetSQLValueString($estatus_contrato, "text"),
    GetSQLValueString($_POST['observaciones_contrato'], "text"),
    GetSQLValueString($_POST['idcontrato'], "int"));
  $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

  $mensaje = "Se ha rechazado el \"Contrato de Uso\"";
}
//SE APRUEBA O RECHAZA EL INFORME Y DICTAMEN DE EVALUACION
if(isset($_POST['informe_dictamen']) && $_POST['informe_dictamen'] == 1){
  //actualizamos el informe de evaluacion
  $updateSQL = sprintf("UPDATE informe_evaluacion SET estatus_informe = %s WHERE idinforme_evaluacion = %s",
    GetSQLValueString($_POST['estatus_informe'], "text"),
    GetSQLValueString($_POST['idinforme_evaluacion'], "int"));
  $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

  //actualizamos el dictamen de evaluacion
  $updateSQL = sprintf("UPDATE dictamen_evaluacion SET estatus_dictamen = %s WHERE iddictamen_evaluacion = %s",
    GetSQLValueString($_POST['estatus_dictamen'], "text"),
    GetSQLValueString($_POST['iddictamen_evaluacion'], "int"));
  $actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());

  $mensaje = "Se ha notificado al OC";
}


$row_solicitud = mysql_query("SELECT solicitud_certificacion.idsolicitud_certificacion AS 'idsolicitud', solicitud_certificacion.fecha_registro, opp.nombre AS 'nombre_opp', opp.abreviacion AS 'abreviacion_opp', proceso_certificacion.idproceso_certificacion, proceso_certificacion.estatus_interno, proceso_certificacion.estatus_dspp, estatus_dspp.nombre AS 'nombre_dspp', solicitud_certificacion.cotizacion_opp, periodo_objecion.*, membresia.idmembresia, membresia.estatus_membresia, contratos.idcontrato, contratos.estatus_contrato, certificado.idcertificado, informe_evaluacion.idinforme_evaluacion, dictamen_evaluacion.iddictamen_evaluacion FROM solicitud_certificacion LEFT JOIN opp ON solicitud_certificacion.idopp = opp.idopp LEFT JOIN proceso_certificacion ON solicitud_certificacion.idsolicitud_certificacion = proceso_certificacion.idsolicitud_certificacion LEFT JOIN periodo_objecion ON solicitud_certificacion.idsolicitud_certificacion = periodo_objecion.idsolicitud_certificacion LEFT JOIN estatus_dspp ON proceso_certificacion.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN membresia ON solicitud_certificacion.idsolicitud_certificacion = membresia.idsolicitud_certificacion LEFT JOIN contratos ON solicitud_certificacion.idsolicitud_certificacion = contratos.idsolicitud_certificacion LEFT JOIN certificado ON solicitud_certificacion.idsolicitud_certificacion = certificado.idsolicitud_certificacion LEFT JOIN informe_evaluacion ON solicitud_certificacion.idsolicitud_certificacion = informe_evaluacion.idsolicitud_certificacion LEFT JOIN dictamen_evaluacion ON solicitud_certificacion.idsolicitud_certificacion = dictamen_evaluacion.idsolicitud_certificacion ORDER BY proceso_certificacion.idproceso_certificacion DESC LIMIT 1", $dspp) or die(mysql_error());

?>
<div class="row">
  <?php 
  if(isset($mensaje)){
  ?>
  <div class="col-md-12 alert alert-success alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 style="font-size:14px;" class="text-center"><?php echo $mensaje; ?><h4/>
  </div>
  <?php
  }
  ?>

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
          <th class="text-center">Membresia</th>
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
                 echo "<a class='btn btn-success form-control' style='font-size:12px;color:white;height:30px;' href='".$solicitud['cotizacion_opp']."' target='_blank'><span class='glyphicon glyphicon-download' aria-hidden='true'></span> Descargar Cotización</a>";
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
                <?php 
                if(isset($solicitud['estatus_objecion']) && $solicitud['estatus_objecion'] == 'FINALIZADO' && isset($solicitud['documento'])){
                ?>
                <button type="button" class="btn btn-sm btn-primary" style="width:100%" data-toggle="modal" data-target="<?php echo "#certificacion".$solicitud['idperiodo_objecion']; ?>">Proceso Certificación</button>
                <?php
                }else{
                  echo "<button class='btn btn-sm btn-default' disabled>Proceso Certificación</button>";
                }
                ?>
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

              <!-- INICIA MEMBRESIA -->
              <td>
                <?php 
                if(isset($solicitud['idmembresia'])){
                  $row_membresia = mysql_query("SELECT membresia.*, comprobante_pago.* FROM membresia LEFT JOIN comprobante_pago ON membresia.idcomprobante_pago = comprobante_pago.idcomprobante_pago WHERE idmembresia = $solicitud[idmembresia]", $dspp) or die(mysql_error());
                  $membresia = mysql_fetch_assoc($row_membresia);
                ?>
                  <button type="button" class="btn btn-sm btn-primary" style="width:100%" data-toggle="modal" data-target="<?php echo "#membresia".$solicitud['idmembresia']; ?>">Estatus Membresía</button>
                <?php
                }
                 ?>
              </td>

                <!-- inicia modal estatus membresia -->

                <div id="<?php echo "membresia".$solicitud['idmembresia']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                  <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Estatus Membresía</h4>
                      </div>
                      <div class="modal-body">
                        <div class="row">
                          <div class="col-md-12">
                            <?php 
                            if(isset($membresia['idcomprobante_pago'])){
                              echo '<p class="alert alert-info">
                              Estatus Comprobante: <span style="color:red">'.$membresia['estatus_comprobante'].'</span><br>
                              Monto de la membresia: <span style="color:red">'.$membresia['monto'].'</span>
                              </p>';
                            }else{

                            }
                             ?>

                            <p><b>Comprobante de Pago</b></p>
                            <?php 
                              if(!isset($membresia['archivo'])){
                                echo "<p class='alert alert-warning'>Aun no se ha cargado el comprobante de pago</p>";
                              }else{
                                echo "<p class='alert alert-success'>Se ha cargado el comprobante de pago, ahora puede descargarlo. Una vez revisado debera de \"APROBAR\" o \"RECHAZAR\" el comprobante de pago de la membresia</p>";

                              ?>
                                <a href="<?php echo $membresia['archivo']; ?>" target="_blank" class="btn btn-info" style="width:100%">Descargar Comprobante</a>
                                <hr>
                                <?php 
                                if($membresia['estatus_comprobante'] == 'ACEPTADO'){
                                  echo "<p class='text-center alert alert-success'><b>La membresía se ha activado</b></p>";
                                }else{
                                ?>
                                  <p class="alert alert-info">
                                    Para aprobar la membresia debe de "APROBAR" el comprobante de pago, si se "RECHAZA" se le notificara al OPP para que pueda revisarlo y cargar nuevamente uno nuevo.
                                  </p>
                                    <div class="text-center">
                                      <label for="observaciones">Observaciones(<span style="color:red">en caso de ser rechazado</span>)</label>
                                      <textarea name="observaciones_comprobante" id="observaciones_comprobante" class="form-control" placeholder="Observaciones"></textarea>
                                      <input type="hidden" name="idcomprobante_pago" value="<?php echo $membresia['idcomprobante_pago']; ?>">
                                      <input type="hidden" name="idmembresia" value="<?php echo $solicitud['idmembresia']; ?>">
                                      <button type="submit" class="btn btn-sm btn-success" style="width:45%" name="aprobar_comprobante" value="1"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Aprobar</button>
                                      <button type="submit" class="btn btn-sm btn-danger" style="width:45%" name="rechazar_comprobante" value="2"><span class="glyphicon glyphicon-remove"></span> Rechazar</button>
                                    </div>
                                <?php
                                }
                                 ?>
                              <?php
                              }
                             ?>
                          </div>


                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        <!--<button type="button" class="btn btn-primary">Guardar Cambios</button>-->
                      </div>
                    </div>
                  </div>
                </div>
                <!-- termina modal estatus membresia -->

              <!-- TERMINA MEMBRESIA -->
              
              <!----- INICIA VENTANA CERTIFICADO ------>
              <td>
                <button type="button" class="btn btn-sm btn-primary" style="width:100%" data-toggle="modal" data-target="<?php echo "#certificado".$solicitud['idsolicitud_certificacion']; ?>">Consultar Certificado</button>
              </td>
              <?php
              
               ?>
                <!-- inicia modal estatus membresia -->

                <div id="<?php echo "certificado".$solicitud['idsolicitud_certificacion']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                  <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Información Certificado</h4>
                      </div>
                      <div class="modal-body">
                        <div class="row">
                          <div class="col-md-6">
                            <h4>Contrato de Uso</h4>
                            <?php 
                            if(isset($solicitud['idcontrato'])){
                              $row_contrato = mysql_query("SELECT * FROM contratos WHERE idcontrato = $solicitud[idcontrato]", $dspp) or die(mysql_error());
                              $contrato = mysql_fetch_assoc($row_contrato);

                              if($contrato['estatus_contrato'] == "ACEPTADO"){
                                echo "<p class='alert alert-success'>Se ha aceptado el Contrato de Uso</p>";
                                echo "<a href=".$contrato['archivo']." target='_blank' class='btn btn-sm btn-success' style='width:100%'>Descargar Contrato</a>";

                              }else{
                              ?>
                                <a href="<?php echo $contrato['archivo']; ?>" target="_blank" class="btn btn-sm btn-success" style="width:100%">Descargar Contrato</a>
                                <label for="observaciones_contrato">Observaciones (<span style="color:red">en caso de ser rechazado</span>)</label>
                                <textarea name="observaciones_contrato" id="observaciones_contrato" class="form-control" placeholder="Observaciones Contrato"></textarea>
                                <div class="col-md-12">
                                  <button class="btn btn-sm btn-success" name="aprobar_contrato" value="1" style="width:45%"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Aprobar</button>
                                  <button class="btn btn-sm btn-danger" name="rechazar_contrato" value="2" style="width:45%"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Rechazar</button>
                                </div>
                              <?php
                              }
                            ?>

                            <?php
                            }else{
                              echo "<p class='alert alert-warning'>Aun no se ha cargado el <span style='colore:red'>Contrato de Uso</span></p>";
                            }
                             ?>

                            <h4>Informe y Dictamen de Evaluación</h4>
                            <?php 
                            if(isset($solicitud['iddictamen_evaluacion']) && isset($solicitud['idinforme_evaluacion'])){
                              $row_dictamen = mysql_query("SELECT * FROM dictamen_evaluacion WHERE iddictamen_evaluacion = $solicitud[iddictamen_evaluacion]", $dspp) or die(mysql_error());
                              $dictamen = mysql_fetch_assoc($row_dictamen);
                              $row_informe = mysql_query("SELECT * FROM informe_evaluacion WHERE idinforme_evaluacion = $solicitud[idinforme_evaluacion]", $dspp) or die(mysql_error());
                              $informe = mysql_fetch_assoc($row_informe);
                            ?>

                                <div class="alert alert-warning">
                                  <p>
                                    Informe de Evaluación
                                  </p>
                                  <a href="<?php echo $informe['archivo']; ?>" class="btn btn-success" target="_new">Descargar Informe</a>
                                  <label class="radio-inline">
                                    <input type="radio" name="estatus_informe" id="" value="ACEPTADO"> ACEPTADO
                                  </label>
                                  <label class="radio-inline">
                                    <input type="radio" name="estatus_informe" id="" value="RECHAZADO"> RECHAZADO
                                  </label>

                                </div>
                                <div class="alert alert-info">
                                  <p>Dictamen de Evaluación</p>
                                  <a href="<?php echo $dictamen['archivo']; ?>" class="btn btn-success" target="_new">Descargar Dictamen</a>
                                  <label class="radio-inline">
                                    <input type="radio" name="estatus_dictamen" id="inlineRadio1" value="ACEPTADO"> ACEPTADO
                                  </label>
                                  <label class="radio-inline">
                                    <input type="radio" name="estatus_dictamen" id="inlineRadio2" value="RECHAZADO"> RECHAZADO
                                  </label>

                                </div>
                                <input type="text" name="iddictamen_evaluacion" value="<?php echo $dictamen['iddictamen_evaluacion']; ?>">
                                <input type="text" name="idinforme_evaluacion" value="<?php echo $informe['idinforme_evaluacion']; ?>">
                                <?php 
                                if($dictamen['estatus_dictamen'] != "ACEPTADO" && $informe['estatus_informe'] != "ACEPTADO"){
                                ?>
                                  <button type="submit" class="btn btn-success" name="informe_dictamen" value="1" onclick="return validar()">Actualizar Documentos</button>
                                <?php
                                }
                                 ?>
                                
                            <?php
                            }else{
                              echo "<p class='alert alert-warning'>Aun no se ha cargado el \"Informe de Evaluación\" así como el \"Dictamen de Evaluación\"</p>";
                            }
                             ?>
                          </div>
                          <div class="col-md-6">
                            <h4>Certificado</h4>
                            <?php 
                            if(isset($solicitud['idcertificado'])){

                            }else{
                              echo "<p class='alert alert-danger'>Aun no se ha cargado el Certificado</p>";
                            }
                             ?>
                          </div>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <input type="text" name="idcontrato" value="<?php echo $solicitud['idcontrato']; ?>">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        <!--<button type="button" class="btn btn-primary">Guardar Cambios</button>-->
                      </div>
                    </div>
                  </div>
                </div>
                <!-- termina modal estatus membresia -->

              <!----- TERMINA VENTANA CERTIFICADO ------>
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

<script>
  
  function validar(){
   /* valor = document.getElementById("cotizacion_opp").value;
    if( valor == null || valor.length == 0 ) {
      alert("No se ha cargado la cotización de el OPP");
      return false;
    }*/
    
    estatus_informe = document.getElementsByName("estatus_informe");
     
    var seleccionado = false;
    for(var i=0; i<estatus_informe.length; i++) {    
      if(estatus_informe[i].checked) {
        seleccionado = true;
        break;
      }
    }
     
    if(!seleccionado) {
      alert("Debes de seleecionar \"ACEPTAR\" o \"DENEGAR\" el Informe de Evaluación");
      return false;
    }

    estatus_dictamen = document.getElementsByName("estatus_dictamen");
    var seleccionado2 = false;
    for(var i=0; i<estatus_dictamen.length; i++) {    
      if(estatus_dictamen[i].checked) {
        seleccionado2 = true;
        break;
      }
    }
     
    if(!seleccionado2) {
      alert("Debes de seleecionar \"ACEPTAR\" o \"DENEGAR\" el Dictamen de Evaluación");
      return false;
    }


    return true
  }

</script>
