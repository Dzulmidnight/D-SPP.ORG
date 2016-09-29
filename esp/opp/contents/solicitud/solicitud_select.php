<?php 
require_once('../Connections/dspp.php'); 
require_once('../Connections/mail.php');
mysql_select_db($database_dspp, $dspp);

if (!isset($_SESSION)) {
  session_start();
  
  $redireccion = "../index.php?OPP";

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

/**** VARIABLES GLOBALES *******/
$correo_certificacion = "cert@spp.coop";
$fecha = time();
$idopp = $_SESSION['idopp'];
/********************************/


/*************************** VARIABLES DE CONTROL **********************************/
  $estado_interno = "2";

//  $validacionStatus = $row_opp['status'] != 1 && $row_opp['status'] != 2 && $row_opp['status'] != 3 && $row_opp['status'] != 14 && $row_opp['status'] != 15;

/*************************** VARIABLES DE CONTROL **********************************/
/// INICIA SE ACEPTA O RECHAZA COTIZACIÓN
if(isset($_POST['cotizacion']) ){
  $estatus_dspp = $_POST['cotizacion'];
  
  if($estatus_dspp == 5){ // se acepta la cotización, modificamos la solicitud y fijamos las fechas del periodo de objeción
    $updateSQL = sprintf("UPDATE solicitud_certificacion SET fecha_aceptacion = %s WHERE idsolicitud_certificacion = %s",
      GetSQLValueString($fecha, "int"),
      GetSQLValueString($_POST['idsolicitud_certificacion'], "int"));
    $actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());

    //CALCULAMOS Y FIJAMOS EL PERIODO DE OBJECIÓN
    $periodo = 15*(24*60*60); //calculamos los segundos de 15 dias
    $fecha_inicio = time();
    $fecha_fin = $fecha + $periodo;
    $estatus_objecion = 'EN ESPERA';

    //INSERTAMOS EL PERIODO DE OBJECIÓN
    $insertSQL = sprintf("INSERT INTO periodo_objecion (idsolicitud_certificacion, fecha_inicio, fecha_fin, estatus_objecion) VALUES (%s, %s, %s, %s)",
      GetSQLValueString($_POST['idsolicitud_certificacion'], "int"),
      GetSQLValueString($fecha_inicio, "int"),
      GetSQLValueString($fecha_fin, "int"),
      GetSQLValueString($estatus_objecion, "text"));
    $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());


    $mensaje = "La cotización ha sido aceptada, el periodo de objeción ha empezado, en breve seras contactado";
  }else{
    $mensaje = "La cotización ha sido rechazada";
  }
  
  //INSERTAMOS EL PROCESO DE CERTIFICACIÓN
  $insertSQL = sprintf("INSERT INTO proceso_certificacion (idsolicitud_certificacion, estatus_dspp, fecha_registro) VALUES (%s, %s, %s)",
    GetSQLValueString($_POST['idsolicitud_certificacion'], "int"),
    GetSQLValueString($estatus_dspp, "int"),
    GetSQLValueString($fecha, "int"));
  $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
}
/// TERMINA SE ACEPTA O RECHAZA COTIZACIÓN

/// INICIA ENVIAR COMPROBANTE DE PAGO
if(isset($_POST['enviar_comprobante']) && $_POST['enviar_comprobante'] == 1){
  $estatus_dspp = 10; //membresia cargada
  $estatus_comprobante = 'ENVIADO';
  $nombre = "COMPROBANTE DE PAGO";
  $rutaArchivo = "../../archivos/oppArchivos/membresia/";

  if(!empty($_FILES['comprobante_pago']['name'])){
      $_FILES["comprobante_pago"]["name"];
        move_uploaded_file($_FILES["comprobante_pago"]["tmp_name"], $rutaArchivo.time()."_".$_FILES["comprobante_pago"]["name"]);
        $comprobante_pago = $rutaArchivo.basename(time()."_".$_FILES["comprobante_pago"]["name"]);
  }else{
    $comprobante_pago = NULL;
  }

  //creamos el proceso de certificacion
  $insertSQL = sprintf("INSERT INTO proceso_certificacion(idsolicitud_certificacion, estatus_dspp, nombre, archivo, fecha_registro) VALUES(%s, %s, %s)",
    GetSQLValueString($_POST['idsolicitud_certificacion'], "int"),
    GetSQLValueString($estatus_dspp, "int"),
    GetSQLValueString($nombre, "text"),
    GetSQLValueString($comprobante_pago, "text"),
    GetSQLValueString($fecha, "int"));
  $insertar = mysql_query($insertSQL,$dspp) or die(mysql_error());

  //actualizamos el comprobante de pago membresia
  $updateSQL = sprintf("UPDATE comprobante_pago SET estatus_comprobante = %s, archivo = %s, fecha_registro = %s WHERE idcomprobante_pago = %s",
    GetSQLValueString($estatus_comprobante, "text"),
    GetSQLValueString($comprobante_pago, "text"),
    GetSQLValueString($fecha, "int"),
    GetSQLValueString($_POST['idcomprobante_pago'], "int"));
  $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

  $mensaje = "Se ha enviado el comprobante de pago, en breve seras notificado";
}
/// TERMINA ENVIAR COMPROBANTE DE PAGO

/// INICIA ENVIAR CONTRATO DE USO
if(isset($_POST['enviar_contrato']) && $_POST['enviar_contrato'] == 1){
  $estatus_dspp = 11; //CONTRATO CARGADO
  $estatus_contrato = "ENVIADO";
  $rutaArchivo = "../../archivos/admArchivos/contratos/";
  $nombre = "CONTRATO DE USO";

  if(!empty($_FILES['contrato']['name'])){
      $_FILES["contrato"]["name"];
        move_uploaded_file($_FILES["contrato"]["tmp_name"], $rutaArchivo.time()."_".$_FILES["contrato"]["name"]);
        $contrato = $rutaArchivo.basename(time()."_".$_FILES["contrato"]["name"]);
  }else{
    $contrato = NULL;
  }
  //insertamos el contrato
  $insertSQL = sprintf("INSERT INTO contratos(idsolicitud_certificacion, nombre, archivo, estatus_contrato, fecha_registro) VALUES (%s, %s, %s, %s, %s)",
    GetSQLValueString($_POST['idsolicitud_certificacion'], "int"),
    GetSQLValueString($nombre, "text"),
    GetSQLValueString($contrato, "text"),
    GetSQLValueString($estatus_contrato, "text"),
    GetSQLValueString($fecha, "int"));
  $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

  //insertamos el proceso_certificacion
  $insertSQL = sprintf("INSERT INTO proceso_certificacion (idsolicitud_certificacion, estatus_dspp, nombre_archivo, archivo, fecha_registro) VALUES (%s, %s, %s, %s, %s)",
    GetSQLValueString($_POST['idsolicitud_certificacion'], "int"),
    GetSQLValueString($estatus_dspp, "text"),
    GetSQLValueString($nombre, "text"),
    GetSQLValueString($contrato, "text"),
    GetSQLValueString($fecha, "int"));
  $insertar = mysql_query($insertSQL,$dspp) or die(mysql_error());

  $mensaje = "Se ha enviado el Contrato de Uso, en breve sera contactado";
}
/// TERMINA ENVIAR CONTRATO DE USO


$query = "SELECT solicitud_certificacion.*, oc.abreviacion AS 'abreviacionOC', periodo_objecion.idperiodo_objecion, periodo_objecion.fecha_inicio, periodo_objecion.fecha_fin, periodo_objecion.estatus_objecion, periodo_objecion.observacion, periodo_objecion.dictamen, periodo_objecion.documento, membresia.idmembresia, certificado.idcertificado, contratos.idcontrato FROM solicitud_certificacion INNER JOIN oc ON solicitud_certificacion.idoc = oc.idoc LEFT JOIN periodo_objecion ON solicitud_certificacion.idsolicitud_certificacion  = periodo_objecion.idsolicitud_certificacion LEFT JOIN membresia ON solicitud_certificacion.idsolicitud_certificacion = membresia.idsolicitud_certificacion LEFT JOIN certificado ON solicitud_certificacion.idopp = certificado.idopp LEFT JOIN contratos ON solicitud_certificacion.idsolicitud_certificacion = contratos.idsolicitud_certificacion WHERE solicitud_certificacion.idopp = $idopp";
$row_solicitud_certificacion = mysql_query($query, $dspp) or die(mysql_error());
$total_solicitudes = mysql_num_rows($row_solicitud_certificacion);



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
    <table class="table table-bordered" style="font-size:12px;">
      <thead>
        <tr class="success">
          <th class="text-center">ID</th>
          <th class="text-center">Fecha</th>
          <th class="text-center">OC</th>
          <th class="text-center">Estatus Solicitud</th>
          <th class="text-center">Cotización</th>
          <th class="text-center">Proceso de Objecion</th>
          <th class="text-center">Proceso Certificación</th>
          <th class="text-center">Membresía SPP</th>
          <th class="text-center">Certificado</th>
          <th class="text-center">Acciones</th>
        </tr>
      </thead>
      <tbody>
      <form action="" method="POST" enctype="multipart/form-data">
        <?php 
        if($total_solicitudes != 0){
          while($solicitud = mysql_fetch_assoc($row_solicitud_certificacion)){
          $query_proceso = "SELECT proceso_certificacion.*, proceso_certificacion.idsolicitud_certificacion, estatus_publico.nombre AS 'nombre_publico', estatus_interno.nombre AS 'nombre_interno', estatus_dspp.nombre AS 'nombre_dspp', membresia.idmembresia, membresia.estatus_membresia, membresia.idcomprobante_pago, membresia.fecha_registro FROM proceso_certificacion LEFT JOIN estatus_publico ON proceso_certificacion.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON proceso_certificacion.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON proceso_certificacion.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN membresia ON proceso_certificacion.idsolicitud_certificacion = membresia.idsolicitud_certificacion WHERE proceso_certificacion.idsolicitud_certificacion =  $solicitud[idsolicitud_certificacion] ORDER BY proceso_certificacion.idproceso_certificacion DESC LIMIT 1";
          $ejecutar = mysql_query($query_proceso,$dspp) or die(mysql_error());
          $proceso_certificacion = mysql_fetch_assoc($ejecutar);
          ?>
            <tr>
              <td>
                <?php echo $solicitud['idsolicitud_certificacion']; ?>
                <input type="hidden" name="idsolicitud_certificacion" value="<?php echo $solicitud['idsolicitud_certificacion']; ?>">
              </td>
              <td><?php echo date('d/m/Y',$solicitud['fecha_registro']); ?></td>
              <td><?php echo $solicitud['abreviacionOC']; ?></td>
              <td><?php echo $proceso_certificacion['nombre_dspp']; ?></td>
              <td>
                <?php
                if(isset($solicitud['cotizacion_opp'])){
                  echo "<a class='btn btn-info form-control' style='font-size:12px;color:white;height:30px;' href='".$solicitud['cotizacion_opp']."' target='_blank'><span class='glyphicon glyphicon-download' aria-hidden='true'></span> Descargar Cotización</a>";

                   if($proceso_certificacion['estatus_dspp'] == 5){ // SE ACEPTA LA COTIZACIÓN
                    echo "<p class='alert alert-success' style='padding:2px;'>Estatus: ".$proceso_certificacion['nombre_dspp']."</p>"; 
                   }else if($proceso_certificacion['estatus_dspp'] == 17){ // SE RECHAZA LA COTIZACIÓN
                    echo "<p class='alert alert-danger' style='padding:2px;'>Estatus: ".$proceso_certificacion['nombre_dspp']."</p>"; 
                   }else{
                    if(empty($solicitud['idperiodo_objecion'])){ //si inicio el periodo de objecion quiere decir que se acepto la cotización
                    ?>
                      <div class="text-center">
                        <button class='btn btn-xs btn-success' type="submit" name="cotizacion" value="5" style='width:45%' data-toggle="tooltip" data-placement="bottom" title="Aceptar cotización"><span class='glyphicon glyphicon-ok'></span></button> 
                        <button class='btn btn-xs btn-danger' style='width:45%' name="cotizacion" value="17" data-toggle="tooltip" data-placement="bottom" title="Rechazar cotización"><span class='glyphicon glyphicon-remove'></span></button>
                      </div>
                    <?php //
                   }
                  }
                }else{
                  echo "COTIZACIÓN OPP";
                }
                ?>
              </td>
              <td>
                <?php
                $row_objecion = mysql_query("SELECT * FROM periodo_objecion WHERE idsolicitud_certificacion = $solicitud[idsolicitud_certificacion]", $dspp) or die(mysql_error());
                $objecion = mysql_fetch_assoc($row_objecion);

                if(empty($objecion['idperiodo_objecion'])){
                  echo "No Disponible";
                }else if($objecion['estatus_objecion'] == 'EN ESPERA'){ // no se muestra nada si esta en espera
                  echo "No Disponible";
                }else{ // si se autorizo se muestra:
                  if(empty($objecion['documento'])){ //si no se ha cargado un documento se muestra el estatus
                  ?>
                    <p class="alert alert-info" style="margin-bottom:0;padding:2px;">Inicio: <?php echo date('d/m/Y', $objecion['fecha_inicio']); ?></p>
                    <p class="alert alert-danger" style="margin-bottom:0;padding:2px;">Fin: <?php echo date('d/m/Y', $objecion['fecha_fin']); ?></p>
                  <?php
                  }else{ // se muestra boton descargar resolución y dictamen del mismo
                   ?>
                    <p class="alert alert-info" style="margin-bottom:0;padding:2px;">Inicio: <?php echo date('d/m/Y', $objecion['fecha_inicio']); ?></p>
                    <p class="alert alert-danger" style="margin-bottom:0;padding:2px;">Fin: <?php echo date('d/m/Y', $objecion['fecha_fin']); ?></p>

                   <p class="alert alert-success" style="margin-bottom:0;padding:2px;">Dictamen: <?php echo $objecion['dictamen']; ?></p>
                   <a class="btn btn-info" style="font-size:12px;width:100%;" href='<?php echo $objecion['documento']; ?>' target='_blank'><span class='glyphicon glyphicon-download' aria-hidden='true'></span> Descargar Resolución</a> 

                  <?php
                  }
                }                
                ?>
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
                            $row_proceso_certificacion = mysql_query("SELECT proceso_certificacion.*, estatus_interno.nombre FROM proceso_certificacion INNER JOIN estatus_interno ON proceso_certificacion.estatus_interno = estatus_interno.idestatus_interno WHERE idsolicitud_certificacion = $solicitud[idsolicitud_certificacion] AND estatus_interno IS NOT NULL", $dspp) or die(mysql_error());
                            while($historial_certificacion = mysql_fetch_assoc($row_proceso_certificacion)){
                            echo "<div class='col-md-10'>Proceso: $historial_certificacion[nombre]</div>";
                            echo "<div class='col-md-2'>Fecha: ".date('d/m/Y',$historial_certificacion['fecha_registro'])."</div>";
                            }
                             ?>

                        </div>
                      </div>
                      <div class="modal-footer">
                        <input type="hidden" name="idperiodo_objecion" value="<?php echo $solicitud['idperiodo_objecion']; ?>">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        <!--<button type="button" class="btn btn-primary">Guardar Cambios</button>-->
                      </div>
                    </div>
                  </div>
                </div>
                <!-- termina modal proceo de certificación -->

              <!----- TERMINA PROCESO CERTIFICACIÓN ---->

              <!----- INICIA MEMBRESIA ------>
              <td>
                <?php 
                if(isset($solicitud['idmembresia'])){
                  $row_membresia = mysql_query("SELECT membresia.*, comprobante_pago.monto, comprobante_pago.estatus_comprobante, comprobante_pago.archivo FROM membresia LEFT JOIN comprobante_pago ON membresia.idcomprobante_pago = comprobante_pago.idcomprobante_pago WHERE idmembresia = $solicitud[idmembresia]", $dspp) or die(mysql_error());
                  $membresia = mysql_fetch_assoc($row_membresia);
                ?>
                  <button type="button" class="btn btn-sm btn-primary" style="width:100%" data-toggle="modal" data-target="<?php echo "#membresia".$solicitud['idperiodo_objecion']; ?>">Estatus Membresia</button>
                <?php
                }else{
                  echo "MEMBRESIA";
                }
                 ?>

                <!-- inicia modal estatus membresia -->

                <div id="<?php echo "membresia".$solicitud['idperiodo_objecion']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                  <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="myModalLabel">Proceso Membresía</h4>
                      </div>
                      <div class="modal-body">
                        <div class="row">
                          <div class="col-md-6">
                            <p class="alert alert-info">Debe cargar el comprobante de pago de la membresia SPP por el monto de <b style="color:red;"><?php echo $membresia['monto'] ?></b>, una vez cargado debe dar clic en enviar. Sera notificado por correo una vez que sea aprobada su membresia</p>
                            <p class="alert alert-warning">Estatus Actual: <?php echo $membresia['estatus_comprobante']; ?></p>
                          </div>
                          <div class="col-md-6">
                            <?php 
                            if(isset($membresia['archivo'] )){
                              if($membresia['estatus_comprobante'] == 'ACEPTADO'){
                                echo "<h4 class='alert alert-success'>Felicidades tu membresía se encuentra activa</h4>";
                              }else if($membresia['estatus_comprobante'] == 'RECHAZADO'){
                                echo "<p class='alert alert-warning'>Se han encontrado irregularidades en su comprobante de pago, por favor reviselo y cargue otro</p>";
                                echo "<p>Observaciones: $membresia[observaciones]</p>";
                              ?>
                                <p class="alert alert-info">
                                  Cargar Comprobante de pago
                                  <input type="file" class="form-control" name="comprobante_pago">
                                </p>
                              <?php
                              }else{
                                echo "SE HA CARGADO EL COMPRANTE POR FAVOR ESPERE";
                              }
                            }else{
                            ?>
                              <p class="alert alert-info">
                                Cargar Comprobante de pago
                                <input type="file" class="form-control" name="comprobante_pago">
                              </p>
                            <?php
                            }
                            ?>
                          </div>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <input type="hidden" name="idperiodo_objecion" value="<?php echo $solicitud['idperiodo_objecion']; ?>">
                        <input type="hidden" name="idcomprobante_pago" value="<?php echo $membresia['idcomprobante_pago']; ?>">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        <?php 
                        if($membresia['estatus_comprobante'] == 'RECHAZADO' || $membresia['estatus_comprobante'] == 'EN ESPERA'){
                          echo '<button type="submit" class="btn btn-primary" name="enviar_comprobante" value="1">Enviar Comprobante</button>';
                        }
                         ?>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- termina modal estatus membresia -->

              </td>
              <!----- TERMINA MEMBRESIA ------>

              <!---- INICIA CONSULTAR CERTIFICADO ---->
              <td>
                <?php 
                if(isset($solicitud['dictamen']) && $solicitud['dictamen'] == "POSITIVO"){
                ?>
                  <button type="button" class="btn btn-sm btn-primary" style="width:100%" data-toggle="modal" data-target="<?php echo "#certificado".$solicitud['idsolicitud_certificacion']; ?>">Consultar Certificado</button>
                <?php
                }else{
                ?>
                  <button type="button" class="btn btn-sm btn-primary" style="width:100%" disabled>Consultar Certificado</button>
                <?php
                }
                 ?>
                <!-- inicia modal estatus_Certificado -->

                <div id="<?php echo "certificado".$solicitud['idsolicitud_certificacion']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                  <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="myModalLabel">Proceso Certificado</h4>
                      </div>
                      <div class="modal-body">
                        <div class="row">
                          <div class="col-md-6">
                            <p class="alert alert-warning">
                              <?php 
                              if(isset($solicitud['idcontrato'])){
                                $row_contrato = mysql_query("SELECT * FROM contratos WHERE idcontrato = $solicitud[idcontrato]", $dspp) or die(mysql_error());
                                $contrato = mysql_fetch_assoc($row_contrato);
                              ?>
                                Se ha cargado el contrato
                              <?php
                              }else{
                              ?>
                                <b>Debe cargar el "Contrato de Uso" firmado, para poder completar el proceso de certficación</b>
                                <input type="file" name="contrato" class="form-control">
                                <button type="submit" class="btn btn-success" style="width:100%" name="enviar_contrato" value="1">Enviar Contrato</button>
                              <?php
                              }
                               ?>
                            </p>

                            <?php 
                            if(isset($solicitud['idcontrato'])){
                              echo "<p class='alert alert-info'>Estatus del Contrato: <span style='color:red'>".$contrato['estatus_contrato']."</span></p>";
                              if($contrato['estatus_contrato'] == "ENVIADO"){
                                echo "<p class='alert alert-warning'>El contratos se encuentra en proceso de revisión. Sera notificado una vez que sea \"APROBADO\" o \"RECHAZADO\"</p>";
                              }else if($contrato['estatus_contrato'] == "ACEPTADO"){
                                echo "<p class='alert alert-success'><b>Se ha aprobado el contrato</b></p>";
                              }else if($contrato['estatus_contrato'] == "RECHAZADO"){
                                echo "<p class='alert alert-danger'>Se ha encontrado irregularidades en el contrato</p>";
                                echo "<p>Observaciones: <span>".$contrato['observaciones']."</span></p>";
                              ?>
                                <b>Debe cargar el "Contrato de Uso" firmado, para poder completar el proceso de certficación</b>
                                <input type="file" name="contrato" class="form-control">
                                <button type="submit" class="btn btn-success" style="width:100%" name="enviar_contrato" value="1">Enviar Contrato</button>
                              <?php
                              }
                            }else{
                              echo '<p class="alert alert-danger">No se ha cargado el "Contrato de Uso"</p>';
                            }
                             ?>
                            
                          </div>
                          <div class="col-md-6">
                            <h4>Certificado</h4>
                            <?php 
                            if(isset($solicitud['idcertificado'])){
                              $row_certificado = mysql_query("SELECT * FROM certificado WHERE idcertificado = $solicitud[idcertificado]", $dspp) or die(mysql_error());
                              $certificado = mysql_fetch_assoc($row_certificado);

                              echo "<p class='alert alert-success'>Felicidades tu Certificado ha sido aprobado.<br> El certificado tienen una vigencia del ".$certificado['fecha_inicio']." al ".$certificado['fecha_fin']."</p>";
                              echo "<a href='".$certificado['archivo']."' class='btn btn-sucess' style='width:100%' target='_blank'>Descargar Certificado</a>";

                            }else{
                              echo "<p class='alert alert-danger'>El Certificado aun no esta disponible</p>";
                            }
                             ?>
                          </div>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- termina modal estatus_Certificado -->
              </td>
              <!----- TERMINA CONSULTAR CERTIFICADO ---->

              <td>
                <a class="btn btn-xs btn-primary" style="display:inline-block" href="?SOLICITUD&amp;detail&amp;idsolicitud=<?php echo $solicitud['idsolicitud_certificacion']; ?>" data-toggle="tooltip" title="Visualizar Solicitud" >
                  <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
                </a>
               <!-- <form action="" method="POST"  style="display:inline-block">-->
                  <button class="btn btn-xs btn-danger" name="eliminar_solicitud" value="1" data-toggle="tooltip" title="Eliminar Solicitud" type="submit" onclick="return confirm('¿Está seguro?, los datos se eliminaran permanentemente');">
                    <span aria-hidden="true" class="glyphicon glyphicon-trash"></span>
                  </button>         
                <!--</form>-->
              </td>

            </tr>
          <?php
          }
        }else{
        ?>
          <tr class="info text-center">
            <td colspan="10">No se encontraron registros</td>
          </tr>
        <?php
        }
         ?>
      </form>
      </tbody>
    </table>
  </div>
</div>



<hr>


<!--
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
</table>-->
