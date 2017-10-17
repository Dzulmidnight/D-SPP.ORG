<?php 
require_once('../../Connections/dspp.php'); 

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
//opp.abreviacion LIKE _utf8 '%$palabra%' COLLATE utf8_general_ci

$fecha = time();
$idoc = $_SESSION['idoc'];

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_empresa = 20;
$pageNum_empresa = 0;
if (isset($_GET['pageNum_empresa'])) {
  $pageNum_empresa = $_GET['pageNum_empresa'];
}
$startRow_empresa = $pageNum_empresa * $maxRows_empresa;

mysql_select_db($database_dspp, $dspp);

if(isset($_POST['buscar']) && $_POST['buscar'] == 1){
  $busqueda = $_POST['campo_buscar'];

  $query_empresa = "SELECT empresa.*, estatus_interno.idestatus_interno, estatus_interno.nombre AS 'nombre_interno', MAX(certificado.idcertificado) AS 'idcertificado', MAX(certificado.vigencia_inicio) AS 'fecha_inicio', MAX(certificado.vigencia_fin) AS 'fecha_fin', certificado.estatus_certificado, estatus_publico.idestatus_publico, estatus_publico.nombre AS 'nombre_publico' FROM empresa LEFT JOIN estatus_interno ON empresa.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_publico ON empresa.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN certificado ON empresa.idempresa = certificado.idempresa WHERE empresa.idoc = $idoc AND (empresa.spp LIKE '%$busqueda%' OR empresa.nombre LIKE '%$busqueda%' OR empresa.abreviacion LIKE '%$busqueda%') GROUP BY empresa.idempresa ORDER BY fecha_fin DESC";
}else{
  $query_empresa = "SELECT empresa.*, estatus_interno.idestatus_interno, estatus_interno.nombre AS 'nombre_interno', MAX(certificado.idcertificado) AS 'idcertificado', MAX(certificado.vigencia_inicio) AS 'fecha_inicio', MAX(certificado.vigencia_fin) AS 'fecha_fin', certificado.estatus_certificado, estatus_publico.idestatus_publico, estatus_publico.nombre AS 'nombre_publico' FROM empresa LEFT JOIN estatus_interno ON empresa.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_publico ON empresa.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN certificado ON empresa.idempresa = certificado.idempresa WHERE empresa.idoc = $idoc GROUP BY empresa.idempresa ORDER BY fecha_fin DESC";
}



$query_limit_empresa = sprintf("%s LIMIT %d, %d", $query_empresa, $startRow_empresa, $maxRows_empresa);
$empresa = mysql_query($query_limit_empresa, $dspp) or die(mysql_error());
//$row_empresa = mysql_fetch_assoc($empresa);

if (isset($_GET['totalRows_empresa'])) {
  $totalRows_empresa = $_GET['totalRows_empresa'];
} else {
  $all_empresa = mysql_query($query_empresa);
  $totalRows_empresa = mysql_num_rows($all_empresa);
}
$totalPages_empresa = ceil($totalRows_empresa/$maxRows_empresa)-1;

$queryString_empresa = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_empresa") == false && 
        stristr($param, "totalRows_empresa") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_empresa = "&" . htmlentities(implode("&", $newParams));
  }
}


if(isset($_POST['actualizacion_empresa']) && $_POST['actualizacion_empresa'] == 'actualizar_datos'){

    $row_empresa = mysql_query("SELECT * FROM empresa",$dspp) or die(mysql_error());
    $cont = 1;
    $fecha = time();

    while($datos_empresa = mysql_fetch_assoc($row_empresa)){
      //$nombre = "estatusPagina"+$datos_empresa['idempresa']+"";

      if(isset($_POST['estatus_interno'.$datos_empresa['idempresa']])){/*********************************** INICIA ESTATUS INTERNO DEL OPP ******************/
        $estatus_interno = $_POST['estatus_interno'.$datos_empresa['idempresa']];

        if(!empty($estatus_interno)){
          /*
          ESTATUS PAGINA = 
          1.- EN REVISION
          2.- CERTIFICADA
          3.- REGISTRADA
          4.- CANCELADA
          */
          $estatus_publico = "";
          if($estatus_interno == 10){ // CANCELADO
            $estatus_publico = 3; //cancelado
          }else{ // ESTATUS PAGINA = EN REVISION
            $estatus_publico = 1; //en revision
          }
          $updateSQL = sprintf("UPDATE empresa SET estatus_interno = %s, estatus_publico = %s WHERE idempresa = %s",
            GetSQLValueString($estatus_interno, "int"),
            GetSQLValueString($estatus_publico, "int"),
            GetSQLValueString($datos_empresa['idempresa'], "int"));
          $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

          /*$queryPagina = "UPDATE opp SET estatusPagina = $estatusPagina WHERE idempresa = $datos_empresa[idempresa]";
          $ejecutar = mysql_query($queryPagina,$dspp) or die(mysql_error());
          //echo "cont: $cont | id($datos_empresa[idempresa]): $estatusInterno<br>";*/
        }      



      }/*********************************** TERMINA ESTATUS INTERNO DEL OPP ****************************************************/


      if(isset($_POST['estatus_publico'.$datos_empresa['idempresa']])){/*********************************** INICIA ESTATUS PUBLICO DEL OPP ******************/
        $estatus_publico = $_POST['estatusPublico'.$datos_empresa['idempresa']];

        if(!empty($estatusPublico)){

          $query = "UPDATE empresa SET estatusPublico = $estatusPublico, estatusPublico = $estatusPublico WHERE idempresa = $datos_empresa[idempresa]";
          $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
          /*$queryPagina = "UPDATE opp SET estatusPagina = $estatusPagina WHERE idempresa = $datos_empresa[idempresa]";
          $ejecutar = mysql_query($queryPagina,$dspp) or die(mysql_error());
          //echo "cont: $cont | id($datos_empresa[idempresa]): $estatusInterno<br>";*/
        }      



      }/*********************************** TERMINA ESTATUS PUBLICO DEL OPP ****************************************************/



      


      if(isset($_POST['num_socios'.$datos_empresa['idempresa']])){/*********************************** INICIA NUMERO DE SOCIOS DEL OPP ******************/
        $num_socios = $_POST['num_socios'.$datos_empresa['idempresa']];


        if(!empty($num_socios)){
          $row_socios = mysql_query("SELECT idempresa, numero FROM num_socios WHERE idempresa = ".$datos_empresa['idempresa']."", $dspp) or die(mysql_error());
          $total = mysql_num_rows($row_socios);

          if($total == 0){
            $insertSQL = sprintf("INSERT INTO num_socios(idempresa, numero, fecha_registro) VALUES (%s, %s, %s)",
              GetSQLValueString($datos_empresa['idempresa'], "int"),
              GetSQLValueString($num_socios, "int"),
              GetSQLValueString($fecha, "int"));
            $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

          }else{
            $updateSQL = sprintf("UPDATE num_socios SET numero = %s, fecha_registro = %s WHERE idempresa = %s",
              GetSQLValueString($num_socios, "int"),
              GetSQLValueString($fecha, "int"),
              GetSQLValueString($datos_empresa['idempresa'], "int"));
            $insertar = mysql_query($updateSQL, $dspp) or die(mysql_error());
          }
        }      
      }/*********************************** TERMINA NUMERO DE SOCIOS DEL OPP ****************************************************/


      if(isset($_POST['spp'.$datos_empresa['idempresa']])){/*********************************** INICIA NUMERO #SPP DEL OPP ******************/
        $spp = $_POST['spp'.$datos_empresa['idempresa']];

        if(!empty($spp)){
          $updateSQL = sprintf("UPDATE empresa SET spp = %s WHERE idempresa = %s",
            GetSQLValueString($spp, "text"),
            GetSQLValueString($datos_empresa['idempresa'], "int"));
          $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

        }      
      }/*********************************** TERMINA NUMERO #SPP DEL OPP ****************************************************/




      if(isset($_POST['vigencia_fin'.$datos_empresa['idempresa']])){ /****************** INICIA VIGENCIA FIN DEL CERTIFICADO ******************/
        $vigencia_fin = $_POST['vigencia_fin'.$datos_empresa['idempresa']];
        $timeActual = time();

        $timeVencimiento = strtotime($vigencia_fin);
        $timeRestante = ($timeVencimiento - $timeActual);
        $estatus_certificado = "";
        $plazo = 60 *(24*60*60);
        $plazoDespues = ($timeVencimiento - $plazo);
        $prorroga = ($timeVencimiento + $plazo);
            // Calculamos el número de segundos que tienen 60 días

        if(!empty($vigencia_fin)){ // NO SE INGRESO NINGUNA FECHA

          $row_certificado = mysql_query("SELECT * FROM certificado WHERE idempresa = '$datos_empresa[idempresa]'", $dspp) or die(mysql_error()); // CONSULTO SI EL OPP CUENTA CON ALGUN REGISTRO DE CERTIFICADO
          $totalCertificado = mysql_num_rows($row_certificado);
          
          if(!empty($totalCertificado)){ // SI CUENTA CON UN REGISTRO, ACTUALIZO EL MISMO
            //$query = "UPDATE certificado SET vigenciafin = '$vigenciafin' WHERE idempresa = $datos_empresa[idempresa]";
            //$ejecutar = mysql_query($query,$dspp) or die(mysql_error());

            /*********************************** INICIA, CALCULAMOS FECHAS PARA ASIGNAR EL ESTATUS DEL CERTIFICADO Y DEL OPP ***********************************************/

            if($timeActual <= $timeVencimiento){
              if($timeRestante <= $plazo){
                $estatus_certificado = 14; //estatus_dspp AVISO DE RENOVACIÓN
                $estatus_publico = 2; //certificado
              }else{
                $estatus_certificado = 13; //estatus_dspp CERTIFICADO ACTIVO
                $estatus_publico = 2; //certificado
              }
            }else{
              if($prorroga >= $timeActual){
                $estatus_certificado = 15; //estatus_dspp CERTIFICADO POR EXPIRAR
                $estatus_publico = 2; //certificado
              }else{
                $estatus_certificado = 16; //estatus_dspp CERTIFICADO EXPIRADO
                $estatus_publico = 1; //en revision
              }
            }

            $updateSQL = sprintf("UPDATE empresa SET estatus_empresa = %s, estatus_publico = %s, estatus_dspp = %s WHERE idempresa = %s",
              GetSQLValueString($estatus_certificado, "int"),
              GetSQLValueString($estatus_publico, "int"),
              GetSQLValueString($estatus_certificado, "int"),
              GetSQLValueString($datos_empresa['idempresa'], "int"));
            $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

            $updateSQL = sprintf("UPDATE certificado SET estatus_certificado = %s, vigencia_fin = %s, entidad = %s WHERE idempresa = %s",
              GetSQLValueString($estatus_certificado, "int"),
              GetSQLValueString($vigencia_fin, "text"),
              GetSQLValueString($idoc, "int"),
              GetSQLValueString($datos_empresa['idempresa'], "int"));
            $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());


              //$actualizar = "UPDATE certificado SET status = '16' WHERE idcertificado = $datos_empresa[idcertificado]";
              //$ejecutar = mysql_query($actualizar,$dspp) or die(mysql_error());
            
            /*********************************** FIN, CALCULAMOS FECHAS PARA ASIGNAR EL ESTATUS DEL CERTIFICADO Y DEL OPP ***********************************************/

          }else{ // SI NO CUENTA CON REGISTRO PREVIO, ENTONCES INSERTO UN NUEVO REGISTRO
            //$query = "INSERT INTO certificado(vigenciafin,idempresa) VALUES('$vigenciafin',$datos_empresa[idempresa])";
            //$ejecutar = mysql_query($query,$dspp) or die(mysql_error());
            /*********************************** INICIA, CALCULAMOS FECHAS PARA ASIGNAR EL ESTATUS DEL CERTIFICADO Y DEL OPP ***********************************************/

            if($timeActual <= $timeVencimiento){
              if($timeRestante <= $plazo){
                $estatus_certificado = 14; // AVISO DE RENOVACIÓN
                $estatus_publico = 2; //certificado
              }else{
                $estatus_certificado = 13; // CERTIFICADO ACTIVO
                $estatus_publico = 2; //certificado
              }
            }else{
              if($prorroga >= $timeActual){
                $estatus_certificado = 15; // CERTIFICADO POR EXPIRAR
                $estatus_publico = 2; //certificado
              }else{
                $estatus_certificado = 16; // CERTIFICADO EXPIRADO
                $estatus_publico = 1; //en revision
              }
            }

              $updateSQL = sprintf("UPDATE empresa SET estatus_empresa = %s, estatus_publico = %s, estatus_dspp = %s WHERE idempresa = %s",
                GetSQLValueString($estatus_certificado, "int"),
                GetSQLValueString($estatus_publico, "int"),
                GetSQLValueString($estatus_certificado, "int"),
                GetSQLValueString($datos_empresa['idempresa'], "int"));
              $actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());

              $insertSQL = sprintf("INSERT INTO certificado (idempresa, entidad, estatus_certificado, vigencia_fin) VALUES (%s, %s, %s, %s)",
                GetSQLValueString($datos_empresa['idempresa'], "int"),
                GetSQLValueString($idoc, "int"),
                GetSQLValueString($estatus_certificado, "int"),
                GetSQLValueString($vigencia_fin, "text"));
              $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

              //$actualizar = "UPDATE certificado SET status = '16' WHERE idcertificado = $datos_empresa[idcertificado]";
              //$ejecutar = mysql_query($actualizar,$dspp) or die(mysql_error());
            
            /*********************************** FIN, CALCULAMOS FECHAS PARA ASIGNAR EL ESTATUS DEL CERTIFICADO Y DEL OPP ***********************************************/


          }

          //echo "cont: $cont | VIGENCIA FIN($datos_empresa[idempresa]): $vigenciafin :TOTAL Certificado: $totalCertificado<br>";
        }      
      }/************************************ TERMINA VIGENCIA FIN DEL CERTIFICADO ***********************************/


      if(isset($_POST['ocAsignado'.$datos_empresa['idempresa']])){ //********************************** INICIA LA ASIGNACION DE OC ***********************************/
        $ocAsignado = $_POST['ocAsignado'.$datos_empresa['idempresa']];
        if(!empty($ocAsignado)){
          $update = "UPDATE empresa SET idoc = '$ocAsignado' WHERE idempresa = '$datos_empresa[idempresa]'";
          $ejecutar = mysql_query($update,$dspp) or die(mysql_error());
        }
      } //********************************** TERMINA LA ASIGNACION DE OC ***********************************/

      if(isset($_POST['eliminar_empresa']) && $_POST['eliminar_empresa'] == $datos_empresa['idempresa']){
        //se agrega el estatus "eliminado" a la OPP;
        /*$idempresa = $_POST['idempresa'];
        $estatus_empresa = "ELIMINADO";
        $updateSQL = sprintf("UPDATE opp SET estatus_empresa = %s WHERE idempresa = %s",
          GetSQLValueString($estatus_empresa, "text"),
          GetSQLValueString($idempresa, "int"));
        $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());
        */
        $idempresa = $datos_empresa['idempresa'];
        $deleteSQL = sprintf("DELETE FROM empresa WHERE idempresa = %s", 
          GetSQLValueString($idempresa, "int"));
        $eliminar = mysql_query($deleteSQL, $dspp) or die(mysql_error());

        $mensaje = "Empresa Eliminada Correctamente";

      }


      $cont++;
    }


    echo '<script>location.href="?EMPRESAS&select";</script>';


}

$detalle_empresa = mysql_query($query_empresa,$dspp) or die(mysql_error());
$totalEmpresa = mysql_num_rows($detalle_empresa);

$row_interno = mysql_query("SELECT * FROM estatus_interno", $dspp) or die(mysql_error());

$queryString_empresa = sprintf("&totalRows_empresa=%d%s", $totalRows_empresa, $queryString_empresa);
?>
<script language="JavaScript"> 
function preguntar(){ 
    if(!confirm('¿Estas seguro de eliminar el registro?')){ 
       return false; } 
} 
</script>
  <hr>
    <div class="row">
      <div class="col-md-4" >
        <button class="btn btn-sm btn-primary" onclick="guardarDatos()"><span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Guardar Cambios</button><!-- BOTON GUARDAR DATOS -->
        | <span class="alert alert-warning" style="padding:7px;">Total EMPRESAS: <?php echo $totalEmpresa; ?></span>
      </div>
      <form action="" method="POST">
        <div class="col-md-8">
          <div class="input-group">
            <span class="input-group-btn">
              <button class="btn btn-default" type="submit" name="buscar" value="1">Buscar</button>
            </span>
            <input type="text" class="form-control" name="campo_buscar" placeholder="Buscar por: #spp, palabra, abreviación">
          </div><!-- /input-group -->
        </div><!-- /.col-lg-6 -->
      </form>
    </div>

    <!--<div style="display:inline;margin-right:10em;">
      Exportar Contactos
      <a href="#" onclick="document.formulario1.submit()"><img src="../../img/pdf.png"></a>
      <a href="#" onclick="document.formulario2.submit()"><img src="../../img/excel.png"></a>
    </div>-->

    <div class="row">
      <div class="col-md-12">
      <?php 
      if(isset($mensaje)){
      ?>
        <div class="alert alert-success alert-dismissible" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <?php echo $mensaje; ?>
        </div>
      <?php
      }
      ?>
      </div>
    </div>

    <table class="table table-bordered table-condensed table-hover" style="font-size:11px;">
      <thead>
        <tr>
          <th class="text-center" style="width:100px;">#SPP</th>
          <th class="text-center" style="width:100px;">Nombre</th>
          <th class="text-center">Abreviación</th>
          <th class="text-center"><a href="#" data-toggle="tooltip" title="Proceso de Certificación en el que se encuentra la Empresa"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>Proceso certificación</a></th>
          <th class="text-center">
            <a href="#" data-toggle="tooltip" title="Fecha en la que expira el Certificado"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>Fecha Final<br>(Certificado)</a>
          </th>
          <th class="text-center"><a href="#" data-toggle="tooltip" title="Estatus del Certificado definido por la fecha de vigencia final">
            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>Estatus Certificado</a>
          </th>

          <!--<th class="text-center">Abreviación</th>-->
          <th class="text-center">Productos</th>
          <!--<th class="text-center">Email</th>
          <th class="text-center">Teléfono Oficinas</th>
          <th class="text-center">País</th>-->
          <!--<th class="text-center">OC</th>-->
          <!--<th class="text-center">Razón social</th>-->

          <!--<th class="text-center">Dirección fiscal</th>-->
          <!--<th class="text-center">RFC</th>-->
          <th class="text-center">Acciones</th>
        </tr>
      </thead>
      <form name="formularioActualizar" id="formularioActualizar" action="" method="POST">
        <input type="hidden" name="actualizacion_empresa" value="actualizar_datos">
        <tbody>
          <?php 
          if($totalEmpresa == 0){
            echo "<tr><td class='alert alert-info text-center' colspan='10'>No se encontraron registros</td></tr>";
          }else{
            while($empresa = mysql_fetch_assoc($detalle_empresa)){
            ?>
              <tr>
                <td>
                  <input type="text" name="spp<?php echo $empresa['idempresa']; ?>" value="<?php echo $empresa['spp']; ?>">
                  <a class="btn btn-xs btn-primary" style="width:100%;" href="?EMPRESAS&amp;detail&amp;idempresa=<?php echo $empresa['idempresa']; ?>">Consultar</a>
                </td>
                <td>
                  <?php echo $empresa['nombre']; ?>
                </td>
                <td>
                  <?php echo $empresa['abreviacion']; ?>
                </td>
                <td>
                  <select name="estatus_interno<?php echo $empresa['idempresa']; ?>">
                    <option>...</option>
                    <?php 
                    $row_interno = mysql_query("SELECT * FROM estatus_interno", $dspp) or die(mysql_error());
                    while($estatus_interno = mysql_fetch_assoc($row_interno)){
                    ?>
                      <option value="<?php echo $estatus_interno['idestatus_interno'] ?>" <?php if($estatus_interno['idestatus_interno'] == $empresa['estatus_interno']){echo "selected";} ?>><?php echo $estatus_interno['nombre']; ?></option>
                    <?php
                    }
                     ?>
                  </select>
                  <?php echo "<p class='alert alert-info' style='padding:7px;'>$empresa[nombre_interno]</p>"; ?>
                </td>
                <td>
                  <?php 
                    $vigenciafin = date('d-m-Y', strtotime($empresa['fecha_fin']));
                    $timeVencimiento = strtotime($empresa['fecha_fin']);
                  
                   ?>
                  <input type="date" name="vigencia_fin<?php echo $empresa['idempresa']; ?>" value="<?php echo $empresa['fecha_fin']; ?>" readonly>
                </td>

            <!--- INICIA ESTATUS_CERTIFICADO ---->
              <?php 
              if(isset($empresa['idcertificado'])){
                $estatus_certificado = mysql_query("SELECT idcertificado, estatus_certificado, estatus_dspp.nombre FROM certificado LEFT JOIN estatus_dspp ON certificado.estatus_certificado = estatus_dspp.idestatus_dspp WHERE idcertificado = $empresa[idcertificado]", $dspp) or die(mysql_error());
                $certificado = mysql_fetch_assoc($estatus_certificado);

                switch ($certificado['estatus_certificado']) {
                  case '13': //certificado "activo"
                    $clase = 'success';
                    break;
                  case '14': //certificado "renovacion"
                    $clase = 'info';
                    break;
                  case '15': //certificado "por expirar"
                    $clase = 'warning';
                    break;
                  case '16': //certificado "Expirado"
                    $clase = 'danger';
                    break;

                  default:
                    # code...
                    break;
                }
                 echo "<td class='".$clase."'>".$certificado['nombre']."</td>";
              }else{
                echo "<td>No Disponible</td>";
              }
                //echo $opp['estatus_certificado'];
               ?>
            <!--- TERMINA ESTATUS_CERTIFICADO ---->

                <td>
                  <?php 
                  $row_productos = mysql_query("SELECT * FROM productos WHERE idempresa = $empresa[idempresa]", $dspp) or die(mysql_error());
                  $total_productos = mysql_num_rows($row_productos);
                  if($total_productos == 0){
                    echo "No Disponible";
                  }else{

                  }
                  while($productos = mysql_fetch_assoc($row_productos)){
                    echo $productos['producto']."<br>";
                  }
                   ?>
                </td>
                <td>
                  <!-- ELIMINAR EMPRESA -->
                  <button class="btn btn-sm btn-danger" data-toggle="tooltip" title="Eliminar Organización" type="submit" onclick="return confirm('¿Está seguro ?, los datos se eliminaran permanentemente');" name="eliminar_empresa" value="<?php echo $empresa['idempresa']; ?>"><span aria-hidden="true" class="glyphicon glyphicon-trash"></span></button>
                  <input type="hidden" name="idempresa" value="<?php echo $empresa['idempresa']; ?>">
                </td>
              </tr>
            <?php
            }
          }
           ?>
        </tbody>
      </form>
    </table>



<table>
<tr>
<td width="20"><?php if ($pageNum_empresa > 0) { // Show if not first page ?>
<a href="<?php printf("%s?pageNum_empresa=%d%s", $currentPage, 0, $queryString_empresa); ?>">
<span class="glyphicon glyphicon-fast-backward" aria-hidden="true"></span>
</a>
<?php } // Show if not first page ?></td>
<td width="20"><?php if ($pageNum_empresa > 0) { // Show if not first page ?>
<a href="<?php printf("%s?pageNum_empresa=%d%s", $currentPage, max(0, $pageNum_empresa - 1), $queryString_empresa); ?>">
<span class="glyphicon glyphicon-backward" aria-hidden="true"></span>
</a>
<?php } // Show if not first page ?></td>
<td width="20"><?php if ($pageNum_empresa < $totalPages_empresa) { // Show if not last page ?>
<a href="<?php printf("%s?pageNum_empresa=%d%s", $currentPage, min($totalPages_empresa, $pageNum_empresa + 1), $queryString_empresa); ?>">
<span class="glyphicon glyphicon-forward" aria-hidden="true"></span>
</a>
<?php } // Show if not last page ?></td>
<td width="20"><?php if ($pageNum_empresa < $totalPages_empresa) { // Show if not last page ?>
<a href="<?php printf("%s?pageNum_empresa=%d%s", $currentPage, $totalPages_empresa, $queryString_empresa); ?>">
<span class="glyphicon glyphicon-fast-forward" aria-hidden="true"></span>
</a>
<?php } // Show if not last page ?></td>
</tr>
</table>


<script>
function guardarDatos(){
  document.getElementById("formularioActualizar").submit();
}
</script>