<?php 
require_once('../Connections/dspp.php'); 
require_once('../Connections/mail.php');
mysql_select_db($database_dspp, $dspp);


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

/*if(isset($_POST['opp_delete'])){
  $query=sprintf("delete from opp where idopp = %s",GetSQLValueString($_POST['idopp'], "text"));
  $ejecutar=mysql_query($query,$dspp) or die(mysql_error());
}*/

/************ INICIA VARIABLES GLOBALES ***************/
$fecha = time();
$idoc = $_SESSION['idoc'];
/**********  TERMINA VARIABLES GLOBALES *****************/
 
if(isset($_POST['cancelar']) && $_POST['cancelar'] == "cancelar"){
  $idsolicitud_certificacion = $_POST['idsolicitud_certificacion'];
  $estatus_interno = 24;  

  $updateSQL = "UPDATE solicitud_certificacion SET status = $estatus_interno WHERE idsolicitud_certificacion = $idsolicitud_certificacion";
  $ejecutar = mysql_query($updateSQL,$dspp) or die(mysql_error());
}

if(isset($_POST['guardar_proceso']) && $_POST['guardar_proceso'] == 1){
  $estatus_dspp = 8; //INICIA PROCESO DE CERTIFICACION
  //ESTATUS INTERNO
  // 8 = DICTAMEN POSITIVO
  //9 = DICTAMEN NEGATIVO
  // INSERTAMOS EL REGISTRO DE PROCESO_CERTIFICACION
  if(isset($_POST['nombre_archivo'])){
    $nombre_archivo = $_POST['nombre_archivo'];
  }else{
    $nombre_archivo = '';
  }
  if(isset($_POST['accion'])){
    $accion = $_POST['accion'];
  }else{
    $accion = '';
  }

  $rutaArchivo = "../../archivos/ocArchivos/anexos/";

  if(!empty($_FILES['archivo_estatus']['name'])){
      $_FILES["archivo_estatus"]["name"];
        move_uploaded_file($_FILES["archivo_estatus"]["tmp_name"], $rutaArchivo.time()."_".$_FILES["archivo_estatus"]["name"]);
        $archivo = $rutaArchivo.basename(time()."_".$_FILES["archivo_estatus"]["name"]);
  }else{
    $archivo = NULL;
  }



  if($_POST['estatus_interno'] == 8){ //checamos si el estatus_interno es positvo
    //creamos la variable del archivo extra
    if(!empty($_FILES['archivo_extra']['name'])){
        $_FILES["archivo_extra"]["name"];
          move_uploaded_file($_FILES["archivo_extra"]["tmp_name"], $rutaArchivo.time()."_".$_FILES["archivo_extra"]["name"]);
          $archivo = $rutaArchivo.basename(time()."_".$_FILES["archivo_extra"]["name"]);
    }else{
      $archivo = NULL;
    }
    $insertSQL = sprintf("INSERT INTO proceso_certificacion (idsolicitud_certificacion, estatus_interno, estatus_dspp, nombre_archivo, accion, archivo, fecha_registro) VALUES (%s, %s, %s, %s, %s, %s, %s)",
      GetSQLValueString($_POST['idsolicitud_certificacion'], "int"),
      GetSQLValueString($_POST['estatus_interno'], "int"),
      GetSQLValueString($estatus_dspp, "int"),
      GetSQLValueString($_POST['nombreArchivo'], "text"),
      GetSQLValueString($accion, "text"),
      GetSQLValueString($archivo, "text"),
      GetSQLValueString($fecha, "int"));
    $insertar = mysql_query($insertSQL,$dspp) or die(mysql_error());

    //creamos el monto del comprobante de pago
    $estatus_comprobante = "EN ESPERA";

    $insertSQL = sprintf("INSERT INTO comprobante_pago(estatus_comprobante, monto) VALUES (%s, %s)",
      GetSQLValueString($estatus_comprobante, "text"),
      GetSQLValueString($_POST['monto_membresia'], "text"));
    $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

    //capturamos el id del comprobante para vincularlo con la membresia
    $idcomprobante_pago = mysql_insert_id($dspp);

    //creamos la membresia
    $estatus_membresia = "EM ESPERA";
    $insertSQL = sprintf("INSERT INTO membresia(estatus_membresia, idopp, idsolicitud_certificacion, idcomprobante_pago) VALUES (%s, %s, %s, %s)",
      GetSQLValueString($estatus_membresia, "text"),
      GetSQLValueString($_POST['idopp'], "int"),
      GetSQLValueString($_POST['idsolicitud_certificacion'], "int"),
      GetSQLValueString($idcomprobante_pago, "int"));
    $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

  }else{
    $insertSQL = sprintf("INSERT INTO proceso_certificacion (idsolicitud_certificacion, estatus_interno, estatus_dspp, nombre_archivo, accion, archivo, fecha_registro) VALUES (%s, %s, %s, %s, %s, %s, %s)",
      GetSQLValueString($_POST['idsolicitud_certificacion'], "int"),
      GetSQLValueString($_POST['estatus_interno'], "int"),
      GetSQLValueString($estatus_dspp, "int"),
      GetSQLValueString($nombre_archivo, "text"),
      GetSQLValueString($accion, "text"),
      GetSQLValueString($archivo, "text"),
      GetSQLValueString($fecha, "int"));
    $insertar = mysql_query($insertSQL,$dspp) or die(mysql_error());
  }

  $mensaje = "Se ha actualizado el Proceso de Certificación";
}

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_opp = 20;
$pageNum_opp = 0;
if (isset($_GET['pageNum_opp'])) {
  $pageNum_opp = $_GET['pageNum_opp'];
}
$startRow_opp = $pageNum_opp * $maxRows_opp;

$query = "SELECT solicitud_certificacion.idsolicitud_certificacion AS 'idsolicitud', solicitud_certificacion.tipo_solicitud, solicitud_certificacion.idopp, solicitud_certificacion.idoc, solicitud_certificacion.fecha_registro, solicitud_certificacion.fecha_aceptacion, solicitud_certificacion.cotizacion_opp, solicitud_certificacion.contacto1_email, solicitud_certificacion.contacto2_email, opp.nombre AS 'nombre_opp', opp.abreviacion AS 'abreviacion_opp', opp.email, proceso_certificacion.idproceso_certificacion, proceso_certificacion.estatus_publico, proceso_certificacion.estatus_interno, proceso_certificacion.estatus_dspp, estatus_publico.nombre AS 'nombre_publico', estatus_interno.nombre AS 'nombre_interno', estatus_dspp.nombre AS 'nombre_dspp', periodo_objecion.* FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp LEFT JOIN proceso_certificacion ON solicitud_certificacion.idsolicitud_certificacion = proceso_certificacion.idsolicitud_certificacion LEFT JOIN estatus_publico ON proceso_certificacion.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON proceso_certificacion.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON proceso_certificacion.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN periodo_objecion ON solicitud_certificacion.idsolicitud_certificacion = solicitud_certificacion.idsolicitud_certificacion WHERE solicitud_certificacion.idoc = $idoc ORDER BY proceso_certificacion.idproceso_certificacion DESC LIMIT 1";
$row_solicitud = mysql_query($query,$dspp) or die(mysql_error());

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
    <table class="table table-condensed table-bordered" style="font-size:12px;">
      <thead>
        <tr class="success">
          <th class="text-center">ID</th>
          <th class="text-center">Fecha Solicitud</th>
          <th class="text-center">Organización</th>
          <th class="text-center">Estatus Solicitud</th>
          <th class="text-center">Cotización <br>(Descargable)</th>
          <!--<th class="text-center">Sitio WEB</th>-->
          <!--<th class="text-center">Contacto</th>-->
          <!--<th class="text-center">País</th>-->
          <th class="text-center">Proceso de Objeción</th>
          <th class="text-center">Proceso <br>Certificación</th>
          <th class="text-center" colspan="2">Certificado</th>

          <!--<th class="text-center">Propuesta</th>-->
          <!--<th class="text-center">Observaciones Solicitud</th>-->
          <th class="text-center" colspan="2">Acciones</th>
          <!--<th>OC</th>
          <th>Razón social</th>
          <th>Dirección fiscal</th>
          <th>RFC</th>-->
          <!--<th>Eliminar</th>-->
        </tr>       
      </thead>
      <tbody>
      <?php 
      while($solicitud = mysql_fetch_assoc($row_solicitud)){
      ?>
        <tr>
          <td>
            <?php echo $solicitud['idsolicitud']; ?>
          </td>
          <td>
            <?php echo date('d/m/Y', $solicitud['fecha_registro']); ?>
          </td>
          <td>
            <?php echo $solicitud['abreviacion_opp']; ?>
          </td>
          <td>
            <?php 
            if(isset($solicitud['estatus_dspp'])){
              echo $solicitud['nombre_dspp'];
            }else{
              echo "No Disponible";
            }
             ?>
          </td>

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
            if(isset($solicitud['idperiodo_objecion']) && $solicitud['estatus_objecion'] != 'EN ESPERA'){
            ?>
              <p class="alert alert-info" style="margin-bottom:0;padding:0px;">Inicio: <?php echo date('d/m/Y', $solicitud['fecha_inicio']); ?></p>
              <p class="alert alert-danger" style="margin-bottom:0;padding:0px;">Fin: <?php echo date('d/m/Y', $solicitud['fecha_fin']); ?></p>
              <?php 
              if(isset($solicitud['documento'])){
              ?>
                <p class="alert alert-success" style="margin-bottom:0;padding:0px;">Dictamen: <?php echo $solicitud['dictamen']; ?></p>
                <a class="btn btn-info" style="font-size:12px;width:100%;height:30px;" href='<?php echo $solicitud['documento']; ?>' target='_blank'><span class='glyphicon glyphicon-download' aria-hidden='true'></span> Descargar Resolución</a> 
              <?php
              }
              ?>
            <?php
            }else{
              echo "No Disponible";
            }
            ?>
          </td>
          <!---- INICIA PROCESO DE CERTIFICACIÓN ---->
          <td>
            <form action="" method="POST" enctype="multipart/form-data">
              <?php 
              if(isset($solicitud['dictamen']) && $solicitud['dictamen'] == 'POSITIVO'){
              ?>
                <button type="button" class="btn btn-sm btn-primary" style="width:100%" data-toggle="modal" data-target="<?php echo "#certificacion".$solicitud['idperiodo_objecion']; ?>">Consultar Proceso</button>
                <!-- inicia modal proceso de certificación -->
                <div id="<?php echo "certificacion".$solicitud['idperiodo_objecion']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                  <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Proceso de Certificación</h4>
                      </div>
                      <div class="modal-body">
                        <div class="row">
                          <?php 
                          $row_proceso_certificacion = mysql_query("SELECT proceso_certificacion.*, estatus_interno.nombre FROM proceso_certificacion INNER JOIN estatus_interno ON proceso_certificacion.estatus_interno = estatus_interno.idestatus_interno WHERE proceso_certificacion.idsolicitud_certificacion = '$solicitud[idsolicitud_certificacion]' AND proceso_certificacion.estatus_interno IS NOT NULL", $dspp) or die(mysql_error());
                          while($proceso_certificacion = mysql_fetch_assoc($row_proceso_certificacion)){
                            echo "<div class='col-md-10'>Proceso: $proceso_certificacion[nombre]</div>";
                            echo "<div class='col-md-2'>Fecha: ".date('d/m/Y',$proceso_certificacion['fecha_registro'])."</div>";
                          }

                          if(isset($proceso_certificacion['idproceso_certificacion']) && $proceso_certificacion['estatus_interno'] != '8'){
                          ?>
                          <div class="col-md-12">
                            <select class="form-control" name="estatus_interno" id="statusSelect" onchange="funcionSelect()" required>
                              <option value="">Seleccione el proceso en el que se encuentra</option>
                              <?php 
                              $row_estatus_interno = mysql_query("SELECT * FROM estatus_interno",$dspp) or die(mysql_error());
                              while($estatus_interno = mysql_fetch_assoc($row_estatus_interno)){
                                echo "<option value='$estatus_interno[idestatus_interno]'>$estatus_interno[nombre]</option>";
                              }
                               ?>
                            </select>                        
                          </div>
                          <?php
                          }
                          ?>

                              <div class="col-xs-12" id="divSelect" style="margin-top:10px;">
                                <div class="col-xs-6">
                                  <input style="display:none" id="nombreArchivo" type='text' class='form-control' name='nombre_archivo' placeholder="Nombre del Archivo"/>
                                </div>
                                <!-- INICIA CARGAR ARCHIVO ESTATUS -->
                                <div class="col-xs-6">
                                  <input style="display:none" id="archivo_estatus" type='file' class='form-control' name='archivo_estatus' />
                                </div>
                                <!-- TERMINA CARGAR ARCHIVO ESTATUS -->

                                <!-- INICIA ACCION PROCESO CERTIFICACION -->
                                <textarea class="form-control" id="registroOculto" style="display:none" name="accion" cols="30" rows="10" value="" placeholder="Escribe aquí"></textarea>
                                <!-- TERMINA ACCION PROCESO CERTIFICACION -->
                              </div>

                              <div id="tablaCorreo" style="display:none">
                                <div class="col-xs-12"  style="margin-top:10px;">
                                  <p class="alert alert-info">El siguiente formato sera enviado en breve al OPP</p>
                                  <div class="col-xs-6">
                                    
                                    <div class="col-xs-12">
                                      <div class="col-xs-6"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></div>
                                      <div class="col-xs-6">
                                        <p>Enviado el: <?php echo date("d/m/Y", time()); ?></p>
                                        <p>Para: <span style="color:red"><?php echo $solicitud['nombre_opp']; ?></span></p>
                                        <p>Correo(s): <?php echo $solicitud['contacto1_email']." ".$solicitud['contacto2_email']." ".$solicitud['email']; ?></p>
                                        <p>Asunto: <span style="color:red">Contrato de uso del Simbolo de Pequeños Productores - SPP</span></p>
                                        
                                      </div>
                                    </div>
                                    <div class="col-xs-12">
                                      <h5 class="alert alert-warning">MENSAJE OPP( <small>Cuerpo del mensaje en caso de ser requerido</small>)</h5>
                                      <textarea name="mensajeOPP" class="form-control" id="textareaMensaje" cols="30" rows="10" placeholder="Ingrese un mensaje en caso de que lo deseé"></textarea>

                                    </div>
                                  </div>
                                  <div class="col-xs-6">
                                    <div class="col-xs-12">
                                      <h4>ARCHIVOS ADJUNTOS( <small>Archivos adjuntos dentro del email</small> )</h4>
                                      <?php 
                                      $row_documentacion = mysql_query("SELECT * FROM documentacion WHERE idestatus_interno = 8", $dspp) or die(mysql_error());
                                      while($documetacion = mysql_fetch_assoc($row_documentacion)){

                                        echo "<span class='glyphicon glyphicon-ok' aria-hidden='true'></span> <a href='$documetacion[archivo]' target='_blank'>$documetacion[nombre]</a><br>";
                                      }
                                       ?>
                                      <p class="alert alert-warning" style="padding:5px;">
                                        Enviar archivos en:
                                        <label class="checkbox-inline">
                                          <input type="checkbox" id="inlineCheckbox1" name="espanol" value="1"> Español
                                        </label>
                                        <label class="checkbox-inline">
                                          <input type="checkbox" id="inlineCheckbox2" name="ingles" value="1"> Ingles
                                        </label>

                                      </p>
    
                                    </div>
                                    <div class="col-xs-12">
                                      <h4>ARCHIVO EXTRA( <small>Anexar algun otro archivo en caso de ser requerido</small>)</h4>
                                      <div class="col-xs-12">
                                        <input type="text" class="form-control" name="nombreArchivo" placeholder="Nombre Archivo">
                                      </div>
                                      <div class="col-xs-12">
                                        <input type="file" class="form-control" name="archivo_extra">
                                      </div>
                                    </div>
                                    <div class="col-xs-12">
                                      <h5 class="alert alert-warning">MEMBRESÍA SPP( Indicar el monto total de la membresía )</h5>
                                      <p>Total Membresía: <input type="text" class="form-control" name="monto_membresia" placeholder="Total Membresía"></p>
                                    </div>
                                  </div>

                                </div>
                              </div>

                        </div>
                      </div>
                      <div class="modal-footer">
                        <input type="text" name="idperiodo_objecion" value="<?php echo $solicitud['idperiodo_objecion']; ?>">
                        <input type="text" name="idopp" value="<?php echo $solicitud['idopp']; ?>">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        <?php 
                        if(isset($proceso_certificacion['idproceso_certificacion']) && $proceso_certificacion['estatus_interno'] != '8'){
                        ?>
                        <button type="submit" class="btn btn-success" name="guardar_proceso" value="1">Guardar Proceso</button>
                        <?php
                        }
                         ?>
                        <!--<button type="button" class="btn btn-primary">Guardar Cambios</button>-->
                      </div>
                    </div>
                  </div>
                </div>
                <!-- termina modal proceso de certificación -->
                    <script>
                    function funcionSelect() {
                      var valorSelect = document.getElementById("statusSelect").value;

                      if(valorSelect == '4'){
                        document.getElementById('divSelect').style.display = 'block';
                        document.getElementById("registroOculto").style.display = 'block';
                        document.getElementById("nombreArchivo").style.display = 'block';
                        document.getElementById("archivo_estatus").style.display = 'block';
                        document.getElementById("tablaCorreo").style.display = 'none';
                      }else if(valorSelect == '6'){
                        document.getElementById('divSelect').style.display = 'block';
                        document.getElementById("nombreArchivo").style.display = 'block';
                        document.getElementById("archivo_estatus").style.display = 'block';
                        document.getElementById("registroOculto").style.display = 'block';
                        document.getElementById("tablaCorreo").style.display = 'none';
                      }else if(valorSelect == '7'){
                        document.getElementById('divSelect').style.display = 'block';
                        document.getElementById("nombreArchivo").style.display = 'none';
                        document.getElementById("archivo_estatus").style.display = 'none';
                        document.getElementById("registroOculto").style.display = 'block';
                        document.getElementById("tablaCorreo").style.display = 'none';
                      }else if(valorSelect == '8'){
                        document.getElementById("tablaCorreo").style.display = 'block'; 
                        document.getElementById("nombreArchivo").style.display = 'none';
                        document.getElementById("archivo_estatus").style.display = 'none';
                        document.getElementById("registroOculto").style.display = 'none';
                      }else if(valorSelect == '9'){
                        document.getElementById('divSelect').style.display = 'block';
                        document.getElementById("tablaCorreo").style.display = 'none'; 
                        document.getElementById("nombreArchivo").style.display = 'block';
                        document.getElementById("archivo_estatus").style.display = 'block';
                        document.getElementById("registroOculto").style.display = 'block';                 
                      }else if(valorSelect == '23'){
                        document.getElementById('divSelect').style.display = 'block';
                        document.getElementById("tablaCorreo").style.display = 'none';
                        document.getElementById("nombreArchivo").style.display = 'none'; 
                        document.getElementById("archivo_estatus").style.display = 'none';
                        document.getElementById("registroOculto").style.display = 'block'                  
                      }else{
                        document.getElementById("nombreArchivo").style.display = 'none';
                        document.getElementById("archivo_estatus").style.display = 'none';
                        document.getElementById("registroOculto").style.display = 'none'
                        document.getElementById("tablaCorreo").style.display = 'none';
                        document.getElementById('divSelect').style.display = 'none';
                      }
                    }
                    </script>
              <?php


              }else{
                echo "No Disponible";
              }
               ?>
               <input type="text" name="idsolicitud_certificacion" value="<?php echo $solicitud['idsolicitud']; ?>">
            </form>
          </td>
          <!---- TERMINA PROCESO DE CERTIFICACIÓN ---->
          <?php 
          $query_certificado = "SELECT * FROM certificado WHERE idopp = $solicitud[idopp]";
          $consultar = mysql_query($query_certificado,$dspp) or die(mysql_error());
          $certificado = mysql_fetch_assoc($consultar);
           ?>
          <td>
            <?php 
            if($certificado['idcertificado']){
              echo $certificado['idcertificado'];
            }else{
              echo "No Disponible";
            }
             ?>
          </td>
          <td>
            <?php echo "CERTIFICADO2"; ?>
          </td>
          <!--<td>
            <?php 
            if(isset($solicitud['observaciones'])){
              echo $solicitud['observaciones'];
            }else{
              echo "No Disponible";
            }
             ?>
          </td>-->
          <td>
            <a class="btn btn-primary" data-toggle="tooltip" title="Visualizar Solicitud" href="?SOLICITUD&IDsolicitud=<?php echo $solicitud['idsolicitud']; ?>"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></a>
          </td>
          <td>
            <form action="../../reportes/reporte.php" method="POST" target="_new">
              <button class="btn btn-xs btn-default" data-toggle="tooltip" title="Descargar solicitud" target="_new" type="submit" ><img src="../../img/pdf.png" style="height:30px;" alt=""></button>

              <input type="hidden" name="idsolicitud_certificacion" value="<?php echo $solicitud['idsolicitud']; ?>">
              <input type="hidden" name="generar_formato" value="1">
            </form>
          </td>

        </tr>
      <?php
      }
      ?>
      </tbody>
    </table>
  </div>
</div>

<!--<table>
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