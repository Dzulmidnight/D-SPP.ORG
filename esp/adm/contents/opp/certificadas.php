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



$timeActual = time();

  if(isset($_POST['archivar']) && $_POST['archivar'] == 1){

    $miVariable =  $_COOKIE["variable"];
    $token = strtok($miVariable, ",");

     while ($token !== false) 
     {
        $query = "UPDATE opp SET estatus_opp = 'ARCHIVADO' WHERE idopp = $token";
        $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
        //echo "$token<br>";
        $token = strtok(",");
     }
  
      echo '<script>borrarTodo();</script>';
      echo '<script>location.href="?OPP&select";</script>';
  }
  if(isset($_POST['eliminar']) && $_POST['eliminar'] == 2){
    $miVariable =  $_COOKIE["variable"];
    $token = strtok($miVariable, ",");

     while ($token !== false) 
     {

        $deteleSQL = sprintf("DELETE FROM opp WHERE idopp = %s", 
          GetSQLValueString($token, "int"));
        $eliminar = mysql_query($deteleSQL, $dspp) or die(mysql_error());

        //$query = "DELETE FROM opp WHERE idopp = $token";
        //$ejecutar = mysql_query($query,$dspp) or die(mysql_error());
        //echo "$token<br>";
        $token = strtok(",");
     }
      echo '<script>borrarTodo();</script>';
      echo '<script>location.href="?OPP&select";</script>';

  }




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






  /// CONSULTAMOS LAS ORGANIZACONES QUE ESTAN EN PROCESO Y POR ESO NO DEBEN ESTAR EN LAS CERTIFICADAS
  $array_opp = '';
  $array_opp2 = '';
  $consultar_numero = mysql_query("SELECT opp.idopp, opp.estatus_interno, opp.abreviacion, solicitud_certificacion.idsolicitud_certificacion, COUNT(idsolicitud_certificacion) AS 'total_solicitudes' FROM opp LEFT JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp WHERE (opp.estatus_opp != 'CANCELADO' AND opp.estatus_opp != 'SUSPENDIDO' AND opp.estatus_opp != 'CERTIFICADO' AND opp.estatus_opp != 'ARCHIVADO') AND opp.estatus_opp = 0 OR opp.estatus_opp IS NULL OR opp.estatus_opp = 1 OR opp.estatus_opp = 4 GROUP BY opp.idopp ORDER BY opp.idopp", $dspp) or die(mysql_error());
  $num_registros = mysql_num_rows($consultar_numero);
  $contador = 1;
  while ($detalle_numero = mysql_fetch_assoc($consultar_numero)) {
    if($detalle_numero['total_solicitudes'] <= 1){
      if($contador < $num_registros){
        $array_opp .= 'opp.idopp(<span style="color:red">'.$detalle_numero['abreviacion'].'</span>) = '.$detalle_numero['idopp'].' OR ';
        $array_opp2 .= 'opp.idopp != '.$detalle_numero['idopp'].' AND ';
      }else{
        $array_opp .= 'opp.idopp(<span style="color:red">'.$detalle_numero['abreviacion'].'</span>) = '.$detalle_numero['idopp'];
        $array_opp2 .= 'opp.idopp != '.$detalle_numero['idopp'];
      }
    }
    $contador++;
  }


  //CONSULTAMOS LAS ORGANIZACIONES QUE ESTAN "ARCHIVADAS" Y POR ESO NO DEBEN ESTAR ENTRE LAS CERTIFICADAS
  $array_archivadas = '';
  $array_archivadas2 = '';
  $contador = 1;
  $opp_archivadas = mysql_query("SELECT opp.idopp, opp.estatus_interno, opp.abreviacion, solicitud_certificacion.idsolicitud_certificacion FROM opp LEFT JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp WHERE opp.estatus_opp = 'ARCHIVADO' GROUP BY opp.idopp ORDER BY opp.nombre", $dspp) or die(mysql_error());
  $total_archivadas = mysql_num_rows($opp_archivadas);

  while($archivadas = mysql_fetch_assoc($opp_archivadas)){
    if($contador < $total_archivadas){
      $array_archivadas2 .= 'opp.idopp(<span style="color:red">'.$archivadas['abreviacion'].'</span>) = '.$archivadas['idopp'].' OR ';
      $array_archivadas .= 'opp.idopp != '.$archivadas['idopp'].' AND ';
    }else{
      $array_archivadas2 .= 'opp.idopp(<span style="color:red">'.$archivadas['abreviacion'].'</span>) = '.$archivadas['idopp'];
      $array_archivadas .= 'opp.idopp != '.$archivadas['idopp'];
    }
    $contador++;
  }

  //CONSULTAMOS LAS ORGANIZACIONES CANCELADAS
  $array_canceladas = '';
  $contador = 1;
  $query_canceladas = mysql_query("SELECT opp.idopp FROM opp LEFT JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp WHERE opp.estatus_interno = 10 OR solicitud_certificacion.estatus_interno = 10 OR opp.estatus_opp = 'CANCELADO' ORDER BY opp.nombre", $dspp) or die(mysql_error());
  $total_canceladas = mysql_num_rows($query_canceladas);
  while($canceladas = mysql_fetch_assoc($query_canceladas)){
    if($contador < $total_canceladas){
      $array_canceladas .= 'opp.idopp != '.$canceladas['idopp'].' AND ';
    }else{
      $array_canceladas .= 'opp.idopp != '.$canceladas['idopp'];
    }
    $contador++;
  }
  $query_tota_opps = mysql_query("SELECT idopp FROM opp GROUP BY idopp",$dspp) or die(mysql_error());
  $total_opps = mysql_num_rows($query_tota_opps);

  

  ///// CONSULTAMOS LOS CUADROS DE BUSQUEDA -//////////////////////////
  if(isset($_POST['palabra'])){
    //// BUSQUEDA POR PALABRAS
    $palabra = $_POST['palabra'];
    $query = "SELECT opp.idopp, opp.spp, opp.email, opp.telefono, opp.password, opp.sitio_web, opp.nombre AS 'nombre_opp', opp.abreviacion AS 'abreviacion_opp', opp.pais, oc.abreviacion AS 'abreviacion_oc', opp.estatus_opp AS 'opp_estatus_opp', opp.estatus_publico AS 'opp_estatus_publico', opp.estatus_interno AS 'opp_estatus_interno', opp.estatus_dspp AS 'opp_estatus_dspp', MAX(solicitud_certificacion.idsolicitud_certificacion) AS 'idsolicitud_certificacion', solicitud_certificacion.tipo_solicitud, solicitud_certificacion.estatus_interno AS 'solicitud_estatus_interno', solicitud_certificacion.estatus_dspp AS 'solicitud_estatus_dspp'FROM opp LEFT JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp LEFT JOIN oc ON solicitud_certificacion.idoc = oc.idoc WHERE $array_opp2 AND $array_archivadas AND $array_canceladas AND (opp.spp LIKE '%".$palabra."%' OR opp.nombre LIKE '%".$palabra."%' OR opp.abreviacion LIKE '%".$palabra."%') GROUP BY opp.idopp ORDER BY opp.abreviacion";
  }else if(isset($_POST['busqueda_filtros']) && $_POST['busqueda_filtros'] == 1){
    //// BUSQUEDA DE ACUERDO A LOS FILTROS AVANZADOS
    $buscar_oc = $_POST['buscar_oc'];
    $buscar_pais = $_POST['buscar_pais'];
    $buscar_producto = $_POST['buscar_producto'];
    $buscar_estatus = $_POST['buscar_estatus'];
    $buscar_tipo = $_POST['buscar_tipo'];
    $productos = '';

    if(empty($buscar_oc)){
      $q_oc = '';
    }else{
      $q_oc = 'AND opp.idoc = "'.$buscar_oc.'"';
    }

    if(empty($buscar_pais)){
      $q_pais = '';
    }else{
      $q_pais = "AND opp.pais = '$buscar_pais'";
    }
    if(empty($buscar_estatus)){
      $q_estatus = '';
    }else{
      switch ($buscar_estatus) {
        case '13':
          $q_estatus = 'AND (opp.estatus_opp = 13 OR opp.estatus_opp = 14 OR opp.estatus_opp = 15 OR opp.estatus_dspp = 13)';
          break;
        case '14':
          $q_estatus = 'AND opp.estatus_opp = 14';
          break;
        case '15':
          $q_estatus = 'AND opp.estatus_opp = 15';
          break;
        case '16':
          $q_estatus = 'AND opp.estatus_opp = 16';
          break;
        default:
          $q_estatus = '';
          break;
      }
    }
    if(empty($buscar_tipo)){
      $array_tipo = '';
    }else{
      if($buscar_tipo == 'RENOVACION'){ /////

        $array_tipo = '';
        $contador = 1;
        //$query = "SELECT MAX(idsolicitud_certificacion), idopp, tipo_solicitud FROM solicitud_certificacion WHERE tipo_solicitud = 'NUEVA' GROUP BY idopp";
        $query = "SELECT solicitud_certificacion.idopp, opp.idopp, solicitud_certificacion.tipo_solicitud FROM solicitud_certificacion LEFT JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE (solicitud_certificacion.idsolicitud_certificacion = (SELECT MAX(solicitud_certificacion.idsolicitud_certificacion) FROM solicitud_certificacion WHERE solicitud_certificacion.idopp = opp.idopp)) AND (solicitud_certificacion.tipo_solicitud != 'NUEVA' OR solicitud_certificacion.idsolicitud_certificacion IS NULL) GROUP BY opp.idopp";
        $consultar_tipo = mysql_query($query, $dspp) or die(mysql_error());
        $total_tipo = mysql_num_rows($consultar_tipo);

        while($q_tipo = mysql_fetch_assoc($consultar_tipo)){
          if($contador < $total_tipo){
            $array_tipo .= 'opp.idopp = '.$q_tipo['idopp'].' OR ';
          }else{
            $array_tipo .= 'opp.idopp = '.$q_tipo['idopp'];
          }
          $contador++;
        }
        if(empty($array_tipo)){
          $tipo = '';
        }else{
          $tipo = 'AND ('.$array_tipo.')';
        }

      }else{ ////
        $tipo = '';
        $array_tipo = '';
        $contador = 1;
        //$query = "SELECT MAX(idsolicitud_certificacion), idopp, tipo_solicitud FROM solicitud_certificacion WHERE tipo_solicitud = 'NUEVA' GROUP BY idopp";
        $query = "SELECT solicitud_certificacion.idopp, opp.idopp, solicitud_certificacion.tipo_solicitud FROM solicitud_certificacion LEFT JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE (solicitud_certificacion.idsolicitud_certificacion = (SELECT MAX(solicitud_certificacion.idsolicitud_certificacion) FROM solicitud_certificacion WHERE solicitud_certificacion.idopp = opp.idopp)) AND solicitud_certificacion.tipo_solicitud = '".$buscar_tipo."' GROUP BY solicitud_certificacion.idopp";
        $consultar_tipo = mysql_query($query, $dspp) or die(mysql_error());
        $total_tipo = mysql_num_rows($consultar_tipo);

        while($q_tipo = mysql_fetch_assoc($consultar_tipo)){
          if($contador < $total_tipo){
            $array_tipo .= 'opp.idopp = '.$q_tipo['idopp'].' OR ';
          }else{
            $array_tipo .= 'opp.idopp = '.$q_tipo['idopp'];
          }
          $contador++;
        }
        if(empty($array_tipo)){
          $tipo = '';
        }else{
          $tipo = 'AND ('.$array_tipo.')';
        }

      } /////


    }

    if(empty($buscar_producto)){
      $array_productos = '';
      //SELECT productos.idopp FROM productos INNER JOIN opp ON productos.idopp = opp.idopp WHERE producto_general = 'CAFE' AND opp.pais LIKE '%ecuador%' GROUP BY productos.idopp
    }else{
      $array_productos = '';
      $contador = 1;
      $consultar_productos = mysql_query("SELECT productos.idopp FROM productos INNER JOIN opp ON productos.idopp = opp.idopp WHERE producto_general LIKE '%$buscar_producto%' GROUP BY idopp", $dspp) or die(mysql_error());
      $total_productos = mysql_num_rows($consultar_productos);

      while($q_productos = mysql_fetch_assoc($consultar_productos)){
        if($contador < $total_productos){
          $array_productos .= 'opp.idopp = '.$q_productos['idopp'].' OR ';
        }else{
          $array_productos .= 'opp.idopp = '.$q_productos['idopp'];
        }
        $contador++;
      }
      if(empty($array_productos)){
        $productos = '';
      }else{
        $productos = 'AND ('.$array_productos.')';
      }
    }

    /*$estatus_membresia = $_POST['estatus_membresia'];
    if(empty($estatus_membresia)){
      $q_estatus = "";
    }else if($estatus_membresia == 'TODAS'){
      $q_estatus = "";
    }else{
      $q_estatus = "AND membresia.estatus_membresia = '".$estatus_membresia."'";
    }

    $pais_membresia = $_POST['pais_membresia'];
    if(empty($pais_membresia)){
      $q_pais = "";
    }else{
      $q_pais = "AND opp.pais = '".$pais_membresia."'";
    }

    $anio_membresia = $_POST['anio_membresia'];
    if(empty($anio_membresia)){
      $q_anio = "";
    }else if($anio_membresia == 'TODOS'){
      $q_anio = "";
    }else{
      $q_anio = "AND FROM_UNIXTIME(proceso_certificacion.fecha_registro,'%Y') = '".$anio_membresia."'";
    }*/

    $query = "SELECT opp.idopp, opp.spp, opp.email, opp.telefono, opp.password, opp.sitio_web, opp.nombre AS 'nombre_opp', opp.abreviacion AS 'abreviacion_opp', opp.pais, oc.abreviacion AS 'abreviacion_oc', opp.estatus_opp AS 'opp_estatus_opp', opp.estatus_publico AS 'opp_estatus_publico', opp.estatus_interno AS 'opp_estatus_interno', opp.estatus_dspp AS 'opp_estatus_dspp', MAX(solicitud_certificacion.idsolicitud_certificacion) AS 'idsolicitud_certificacion', solicitud_certificacion.tipo_solicitud, solicitud_certificacion.estatus_interno AS 'solicitud_estatus_interno', solicitud_certificacion.estatus_dspp AS 'solicitud_estatus_dspp' FROM opp LEFT JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp LEFT JOIN oc ON solicitud_certificacion.idoc = oc.idoc WHERE (solicitud_certificacion.idsolicitud_certificacion = (SELECT MAX(idsolicitud_certificacion) FROM solicitud_certificacion WHERE solicitud_certificacion.idopp = opp.idopp) OR solicitud_certificacion.idsolicitud_certificacion IS NULL) AND ($array_opp2 AND $array_archivadas AND $array_canceladas ".$q_oc." ".$q_pais." ".$q_estatus." ".$tipo." ".$productos.") GROUP BY opp.idopp ORDER BY opp.nombre";
  }else{
    /// CONSULTA POR DEFAULT

    $query = "SELECT opp.idopp, opp.spp, opp.email, opp.telefono, opp.password, opp.sitio_web, opp.nombre AS 'nombre_opp', opp.abreviacion AS 'abreviacion_opp', opp.pais, oc.abreviacion AS 'abreviacion_oc', opp.estatus_opp AS 'opp_estatus_opp', opp.estatus_publico AS 'opp_estatus_publico', opp.estatus_interno AS 'opp_estatus_interno', opp.estatus_dspp AS 'opp_estatus_dspp', solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.tipo_solicitud, solicitud_certificacion.estatus_interno AS 'solicitud_estatus_interno', solicitud_certificacion.estatus_dspp AS 'solicitud_estatus_dspp' FROM opp LEFT JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp LEFT JOIN oc ON solicitud_certificacion.idoc = oc.idoc WHERE (solicitud_certificacion.idsolicitud_certificacion = (SELECT MAX(idsolicitud_certificacion) FROM solicitud_certificacion WHERE solicitud_certificacion.idopp = opp.idopp) OR solicitud_certificacion.idsolicitud_certificacion IS NULL) AND ($array_opp2 AND $array_archivadas AND $array_canceladas) GROUP BY opp.idopp ORDER BY opp.nombre";
  }
  /*echo $q_estatus.'<br>';
  echo $query;*/
/*14_11_2017  echo $productos;
  echo '<hr>';
  echo $query;
  14_11_2017*/
  $consultar = mysql_query($query,$dspp) or die(mysql_error());
 
  $total_organizaciones = mysql_num_rows($consultar);

 ?>

<div class="row">

  <div class="col-md-2">
    <button class="btn btn-sm btn-danger" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
      <span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span> Filtro Avanzado
    </button>  
  </div>
  <div class="col-md-10">
    <form action="" method="POST">
        <div class="input-group">
          <span class="input-group-btn">
            <button class="btn btn-success" name="busqueda_palabra" value="1" type="submit">Buscar</button>
          </span>
          <input type="text" class="form-control" name="palabra" placeholder="Buscar por: #SPP, Nombre, Abreviacion">
        </div><!-- /input-group -->

    </form>
  </div>  
</div>


<!-- CUADRO DE BUSQUEDA AVANZADA -->
<div class="collapse" id="collapseExample">
  
    <form action="" method="POST">
      <div class="col-md-12 alert alert-info">
        <div class="text-center col-md-12">
          <b style="color:#d35400">Seleccione los parametros de los cuales desea realizar la busqueda</b>
        </div> 
        <div class="row">


          <div class="col-xs-3">
            Organismo de Certificación
            <select name="buscar_oc" class="form-control">
              <option value=''>Selecciona un organismo de certificación</option>
              <?php 
              $row_oc = mysql_query("SELECT idoc, abreviacion FROM oc WHERE idoc != 18", $dspp) or die(mysql_error());
              while($oc = mysql_fetch_assoc($row_oc)){
                echo "<option value='$oc[idoc]'>$oc[abreviacion]</option>";
              }
               ?>
            </select>
          </div>
          <div class="col-xs-3">
            País
            <select name="buscar_pais" class="form-control">
              <option value=''>Selecciona un país</option>
              <?php
              $row_pais = mysql_query("SELECT pais FROM opp GROUP BY pais",$dspp) or die(mysql_error());
              while($pais = mysql_fetch_assoc($row_pais)){
                echo "<option value='".$pais['pais']."'>".mayuscula($pais['pais'])."</option>";
              }
               ?>
            </select>
          </div>
          <div class="col-xs-3">
            Estatus del Certificado
            <select name="buscar_estatus" class="form-control">
              <option value=''>Selecciona un estatus</option>
              <option value="13">Certificado</option>
              <option value="14">Aviso de Renovación</option>
              <option value="15">Certificado por Expirar</option>
              <option value="16">Certificado Expirado</option>
            </select>
          </div>
          <div class="col-xs-3">
            Tipo de Organización
            <select name="buscar_tipo" class="form-control">
              <option value=''>Seleccione un tipo</option>
              <option value="NUEVA">NUEVA</option>
              <option value="RENOVACION">EN RENOVACIÓN</option>

            </select>
          </div>
          <div class="col-xs-3">
            Producto
            <select class="form-control" name="buscar_producto" id="">
              <option value=''>Seleccione un producto</option>
              <?php
              $row_productos = mysql_query("SELECT producto_general FROM productos WHERE producto_general IS NOT NULL GROUP BY producto_general", $dspp) or die(mysql_error());
              while($lista_productos = mysql_fetch_assoc($row_productos)){
                echo "<option value='$lista_productos[producto_general]'>$lista_productos[producto_general]</option>";
              }
               ?>
            </select>
          </div>
          <div class="col-xs-12">
            <button type="submit" class="btn btn-success" name="busqueda_filtros" style="width:100%" value="1">Filtrar Información</button>
          </div>
        </div>
      </div>
    </form>

</div>
<!-- TERMINA CUADRO DE BUSQUEDA AVANZADA -->

<table class="table table-bordered table-condensed" style="font-size:11px;">
  <thead>
    <tr>
      <th colspan="3">
        <button class="btn btn-sm btn-info disabled"><span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Guardar Cambios</button>
      </th>
      <th colspan="3">
        Exportar Lista
        <!--<a href="#" target="_blank" onclick="document.formulario1.submit()"><img src="../../img/pdf.png"></a>-->
        <a href="#" onclick="document.formulario2.submit()"><img src="../../img/excel.png"></a>

        <form name="formulario1" method="POST" action="../../reportes/lista_opp.php">
          <input type="hidden" name="lista_pdf" value="1">
          <input type="hidden" name="query_pdf" value="<?php echo $query; ?>">
        </form> 
        <form name="formulario2" method="POST" action="../../reportes/lista_opp.php">
          <input type="hidden" name="lista_excel" value="2">
          <input type="hidden" name="query_excel" value="<?php echo $query; ?>">
        </form>
      </th>
      <th class="success text-center" colspan="6">
        NUMERO DE ORGANIZACIONES: <?php echo $total_organizaciones; ?>
      </th>
    </tr>
    <tr>
      <th style="width:20px;">#</th>
      <th>#SPP</th>
      <th style="width:200px;">ORGANIZACIÓN</th>
      <th style="width:100px;">PAÍS</th>
      <th>OC</th>

      <th>ULTIMA FECHA DE CERTIFICADO</th>
      <!--14_11_2017<th>ESTATUS OPP</th>-->
      <th>ESTATUS CERTIFICADO</th>

      
     <!--<th>ESTATUS PUBLICO</th>-->
      <!--<th>ESTATUS INTERNO</th>-->
      <!--<th>ESTATUS DSPP</th>-->
      <!--17_11_2017<th><a href="#" data-toggle="tooltip" title="Ultimo tipo de solicitud registrado"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span> TIPO SOLICITUD</a></th>-->
      <th>ESTATUS ORGANIZACIÓN</th>
      <th>ID SOLICITUD</th>
      <th>PROCESO SOLICITUD</th>

      <th>PRODUCTOS</th>
      <th>Nº SOCIOS</th>
      <th>
        <!--<form  style="margin: 0;padding: 0;" action="" method="POST" >            
            <button class="btn btn-xs btn-danger disabled" type="subtmit" value="2"  name="eliminar" data-toggle="tooltip" data-placement="top" title="Eliminar" onclick="return confirm('¿Está seguro ?, los datos se eliminaran permanentemente');" >
              <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
            </button>        
            <button class="btn btn-xs btn-info disabled" type="subtmit" value="1" name="archivar" data-toggle="tooltip" data-placement="top" title="Archivar">
              <span class="glyphicon glyphicon-folder-open" aria-hidden="true"></span>
            </button>
        </form>-->
        CERTIFICACIONES
      </th>
    </tr>
  </thead>
  <tbody>
  <?php 
  $contador = 1;
  while($informacion = mysql_fetch_assoc($consultar)){
    $queryCertificado = mysql_query("SELECT idcertificado, vigencia_inicio, vigencia_fin, archivo FROM certificado WHERE idcertificado = (SELECT MAX(idcertificado) FROM certificado WHERE idopp = '$informacion[idopp]')", $dspp) or die(mysql_error());
    $certificado = mysql_fetch_assoc($queryCertificado);

    echo '<tr>';
      echo '<td style="width:20px;">'.$informacion['idopp'].'</td>';
  ?>

    <!-- CODIGO #SPP -->
    <td>
      <a class="btn btn-primary btn-xs" style="width:100%;font-size:10px;" href="?OPP&amp;detail&amp;idopp=<?php echo $informacion['idopp']; ?>">Consultar<br></a>
      <input type="text" name="spp<?php echo $informacion['idopp'];?>" value="<?php echo $informacion['spp']; ?>">
    </td>

    <!-- NOMBRE DE LA ORGANIZACION -->
    <td>
      <?php echo '<span style="color:#bdc3c7">ID: '.$informacion['idopp'].'| </span>'.mayuscula($informacion['nombre_opp']).' (<span style="color:red">'.mayuscula($informacion['abreviacion_opp']).'</span>)'; ?>
    </td>
    <!-- PAIS DE LA ORGANIZACION -->
    <td>
      <?php echo mayuscula($informacion['pais']); ?>
    </td>
    <!-- ORGANISMO DE CERTIFICACION -->
    <td>
      <?php 
      if(isset($informacion['abreviacion_oc'])){
        echo mayuscula($informacion['abreviacion_oc']); 
      }else{
        echo '<span style="color:red">NO DISPONIBLE</span>';
      }
      ?>
    </td>

    <!-- ULTIMA FECHA DE CERTIFICADO -->
    <td>
      <?php
      echo '<p>I: '.$certificado['vigencia_inicio'].'</p>';
      echo '<p class="bg-success">F: '.$certificado['vigencia_fin'].'</p>'
      /*echo 'LA SOLICITUD ES: '.$informacion['idsolicitud_certificacion'];
      $queryCertificado = mysql_query("SELECT idcertificado, vigencia_inicio, vigencia_fin, archivo FROM certificado WHERE idsolicitud_certificacion = '$informacion[idsolicitud_certificacion]'", $dspp) or die(mysql_error());
      $detailCertificado = mysql_fetch_assoc($queryCertificado);

      if(!empty($queryCertificado['vigencia_fin'])){
        echo '<p style="color:red">I: '.$queryCertificado['vigencia_inicio'].'</p>';
        echo '<p style="color:red" class="bg-success">F: '.$queryCertificado['vigencia_fin'].'</p>';
      }else{
        $consulta_certificado = mysql_query("SELECT idcertificado, vigencia_inicio, vigencia_fin FROM certificado WHERE idopp = '$informacion[idopp]'", $dspp) or die(mysql_error());
        $detalle_certificado = mysql_fetch_assoc($consulta_certificado);
        if(isset($detalle_certificado['vigencia_fin'])){
          echo '<p style="color:blue">I: '.$detalle_certificado['vigencia_inicio'].'</p>';
          echo '<p style="color:blue" class="bg-success">F: '.$detalle_certificado['vigencia_fin'].'</p>';
        }else{
          echo '<span style="color:red">No disponible</span>';
        }
      }*/
/*

      if(isset($informacion['vigencia_fin'])){
        echo '<p style="color:red">I: '.$informacion['vigencia_inicio'].'</p>';
        echo '<p style="color:red" class="bg-success">F: '.$informacion['vigencia_fin'].'</p>';
      }else{
        $consulta_certificado = mysql_query("SELECT idcertificado, vigencia_inicio, vigencia_fin FROM certificado WHERE idopp = '$informacion[idopp]'", $dspp) or die(mysql_error());
        $detalle_certificado = mysql_fetch_assoc($consulta_certificado);
        if(isset($detalle_certificado['vigencia_fin'])){
          echo '<p style="color:blue">I: '.$detalle_certificado['vigencia_inicio'].'</p>';
          echo '<p style="color:blue" class="bg-success">F: '.$detalle_certificado['vigencia_fin'].'</p>';
        }else{
          echo '<span style="color:red">No disponible</span>';
        }
      }
      */
      ?>
    </td>
    <!-- ESTATUS OPP (posiblemente cambiarlo por estatus certificado) -->
    <td>
      <?php
      if($informacion['opp_estatus_opp'] != 'CERTIFICADO' && $informacion['opp_estatus_opp'] != 'CANCELADO'){
        $consultar10 = mysql_query("SELECT nombre FROM estatus_dspp WHERE idestatus_dspp = $informacion[opp_estatus_opp]", $dspp) or die(mysql_error());
        $detalle10 = mysql_fetch_assoc($consultar10);
        switch ($informacion['opp_estatus_opp']) {
          case '13': // CERTIFICADA
            //13_11_2017echo '<p class="bg-success">('.$informacion['opp_estatus_opp'].')'.mayuscula($detalle10['nombre']).'</p>';
            echo '<p class="bg-success">'.mayuscula($detalle10['nombre']).'</p>';
            break;
          case '14': // AVISO DE RENOVACIÓN DEL CERTIFICADO
            //13_11_2017echo '<p class="bg-info">('.$informacion['opp_estatus_opp'].')'.mayuscula($detalle10['nombre']).'</p>';
            echo '<p class="bg-info">'.mayuscula($detalle10['nombre']).'</p>';
            break;
          case '15': // CERTIFICADO POR EXPIRAR
            //13_11_2017echo '<p class="bg-warning">('.$informacion['opp_estatus_opp'].')'.mayuscula($detalle10['nombre']).'</p>';
            echo '<p class="bg-warning">'.mayuscula($detalle10['nombre']).'</p>';
            break;
          case '16': // CERTIFICADO EXPIRADO
            //13_11_2017echo '<p class="bg-danger">('.$informacion['opp_estatus_opp'].')'.mayuscula($detalle10['nombre']).'</p>';
            echo '<p class="bg-danger">'.mayuscula($detalle10['nombre']).'</p>';
            break;
        }
      }else{
        echo '<span style="color:red">'.mayuscula($informacion['opp_estatus_opp']).'</span>';
      }
      ?>
    </td>
    <!-- ESTATUS PUBLICO -->
    <!--13_11_2017<td>
      <?php 
        echo '<p>OPP: '.$informacion['opp_estatus_publico'].'</p>';
       ?>
    </td>
    <!-- ESTATUS INTERNO -->
    <!--<td>
      <?php
      $consultar3 = mysql_query("SELECT nombre FROM estatus_interno WHERE idestatus_interno = '$informacion[solicitud_estatus_interno]'", $dspp) or die(mysql_error());
      $detalle3 = mysql_fetch_assoc($consultar3);
        echo '<p>SOLICITUD: '.$informacion['solicitud_estatus_interno'].' - <span style="color:blue">'.$detalle3['nombre'].'</span></p>';
      $consultar4 = mysql_query("SELECT nombre FROM estatus_interno WHERE idestatus_interno = '$informacion[opp_estatus_interno]'", $dspp) or die(mysql_error());
      $detalle4 = mysql_fetch_assoc($consultar4);
        echo '<p>OPP: '.$informacion['opp_estatus_interno'].' - <span style="color:red">'.$detalle4['nombre'].'</span></p>';
       ?>
    </td>-->
    <!-- ESTATUS DSPP -->
    <!--<td>
      <?php
      $consultar5 = mysql_query("SELECT nombre FROM estatus_dspp WHERE idestatus_dspp = '$informacion[solicitud_estatus_dspp]'", $dspp) or die(mysql_error());
      $detalle5 = mysql_fetch_assoc($consultar5);
        echo '<p>SOLICITUD: '.$informacion['solicitud_estatus_dspp'].' - <span style="color:blue">'.$detalle5['nombre'].'</span></p>';
      $consultar6 = mysql_query("SELECT nombre FROM estatus_dspp WHERE idestatus_dspp = '$informacion[opp_estatus_dspp]'", $dspp) or die(mysql_error());
      $detalle7 = mysql_fetch_assoc($consultar6);
        echo '<p>OPP: '.$informacion['opp_estatus_dspp'].' - <span style="color:red">'.$detalle7['nombre'].'</span></p>';
       ?>
    </td>
    <!-- TIPO SOLICITUD -->
    <td>
      <?php
      /*$estado = mysql_query("SELECT estatus_interno FROM estatus_interno WHERE idestatus_interno = '$informacion[solicitud_estatus_interno]'", $dspp) or die(mysql_error());
      $detalle_estado = mysql_fetch_assoc($estado);*/
      if($informacion['opp_estatus_interno'] == 11){
        echo '<span style="color:red">SUSPENDIDA</span>';
      }else if($informacion['opp_estatus_interno'] == 12){
        echo '<span style="color:red">INACTIVA</span>';
      }else{
        if($informacion['tipo_solicitud'] == 'NUEVA'){
          echo '<p class="bg-success">'.$informacion['tipo_solicitud'].'</p>';
        }else if($informacion['tipo_solicitud'] == 'RENOVACION'){
          echo '<p class="bg-warning">'.$informacion['tipo_solicitud'].'</p>';
        }else{
          echo '<p style="color:red">NO DISPONIBLE</p>';
        }

        /*$query_tipo = mysql_query("SELECT solicitud_certificacion.tipo_solicitud FROM solicitud_certificacion WHERE idopp = '$informacion[idopp]'", $dspp) or die(mysql_error());
        $tipo_solicitud = mysql_fetch_assoc($query_tipo);
        $numero_solicitud = mysql_num_rows($query_tipo);
        if($tipo_solicitud['tipo_solicitud'] == 'NUEVA'){
          //echo '<p class="bg-success">'.$tipo_solicitud['tipo_solicitud'].'</p>';
          //echo 'EL NUMERO ES: '.$numero_solicitud;
          //echo '<p>EL TIPO ES: '.$informacion['tipo_solicitud'].'</p>';
        }else if($tipo_solicitud['tipo_solicitud'] == 'RENOVACION'){
          //echo '<p class="bg-warning">'.$tipo_solicitud['tipo_solicitud'].'</p>';
        }else{
          //echo '<p style="color:red">NO DISPONIBLE</p>';
          //echo 'EL NUMERO ES: '.$numero_solicitud;
        }*/
      }
       ?>
    </td>
    <!-- ID DE LA SOLICITUD -->
    <td>
      <?php
      if(isset($informacion['idsolicitud_certificacion'])){
        echo '<a href="?SOLICITUD&idsolicitud='.$informacion['idsolicitud_certificacion'].'">CONSULTAR</a>';
      }else{
        echo '<p style="color:red">NO DISPONIBLE</p>';
      }
      ?>
    </td>
    <!-- PROCESO SOLICITUD -->
    <td>
      <?php
      if(isset($informacion['idsolicitud_certificacion'])){
        $proceso = mysql_query("SELECT idestatus_dspp, nombre FROM estatus_dspp WHERE idestatus_dspp = '$informacion[solicitud_estatus_dspp]'", $dspp) or die(mysql_error());
        $info_proceso = mysql_fetch_assoc($proceso);

        if($informacion['solicitud_estatus_dspp'] == 9){
          $proceso_interno = mysql_query("SELECT nombre FROM estatus_interno WHERE idestatus_interno = '$informacion[solicitud_estatus_interno]'", $dspp) or die(mysql_error());
          $info_proceso_interno = mysql_fetch_assoc($proceso_interno);
          //13_11_2018echo '('.$informacion['solicitud_estatus_dspp'].')'.$info_proceso['nombre'].': <span style="color:green">'.$info_proceso_interno['nombre'].'</span>';
          echo mayuscula($info_proceso['nombre']).': <span style="color:green">'.mayuscula($info_proceso_interno['nombre']).'</span>';
        }else{
          if($info_proceso['idestatus_dspp'] == 12){
            if(file_exists($certificado['archivo'])){
              echo '<a href="'.$certificado['archivo'].'" target="_new"><span class="glyphicon glyphicon-bookmark" aria-hidden="true"></span> '.mayuscula($info_proceso['nombre']).'</a>';
            }else{
              echo mayuscula($info_proceso['nombre']);
            }
            //13_11_2017echo '('.$info_proceso['idestatus_dspp'].')'.$info_proceso['nombre'];
          }else{
            //13_11_2018echo '('.$info_proceso['idestatus_dspp'].')'.$info_proceso['nombre'];
            echo mayuscula($info_proceso['nombre']);
          }
        }
      }else{
        echo '<span style="color:red">SIN SOLICITUD</span>';
      }
       ?>
    </td>

    <!-- PRODUCTOS DE LA ORGANIZACIÓN -->
    <td>
    <?php 
      $query_productos = mysql_query("SELECT GROUP_CONCAT(producto SEPARATOR ', ') AS 'lista_productos' FROM productos WHERE idsolicitud_certificacion = '$informacion[idsolicitud_certificacion]'", $dspp) or die(mysql_error());
      $productos = mysql_fetch_assoc($query_productos);
      if(empty($productos['lista_productos'])){
        $query_productos = mysql_query("SELECT GROUP_CONCAT(producto SEPARATOR ', ') AS 'lista_productos' FROM productos WHERE idopp = '$informacion[idopp]'", $dspp) or die(mysql_error());
        $productos = mysql_fetch_assoc($query_productos);
        echo $productos['lista_productos'];
      }else{
        echo '<p style="color:green">'.$productos['lista_productos'].'</p>';
      }
     ?>
    </td>
    <!-- NUMERO DE SOCIOS DE LA ORGANIZACIÓN -->
    <td>
    <?php 
    if(isset($informacion['idsolicitud_certificacion'])){
      $consultar_socios = mysql_query("SELECT resp1 FROM solicitud_certificacion WHERE idsolicitud_certificacion = $informacion[idsolicitud_certificacion]", $dspp) or die(mysql_error());
      $socios = mysql_fetch_assoc($consultar_socios);
      echo '<span style="color:green">'.$socios['resp1'].'</span>';
    }else{
      $consultar_socios = mysql_query("SELECT numero FROM num_socios WHERE idopp = $informacion[idopp]", $dspp) or die(mysql_error());
      $socios = mysql_fetch_assoc($consultar_socios);
      echo '<span style="color:red">'.$socios['numero'].'</span>';
    }
    ?>
    </td>
    <!-- CERTIFICACIONES CON LAS QUE CUENTA LA OPP -->
    <td>
    <?php 
    $query_certificaciones = mysql_query("SELECT GROUP_CONCAT(certificacion SEPARATOR ', ') AS 'lista_certificaciones' FROM certificaciones WHERE idsolicitud_certificacion = '$informacion[idsolicitud_certificacion]'", $dspp) or die(mysql_error());
    $certificaciones = mysql_fetch_assoc($query_certificaciones);

      echo '<p style="color:green">'.mayuscula($certificaciones['lista_certificaciones']).'</p>';
     ?>
    </td>

  <?php
    echo '</tr>';
    $contador++;
  }
   ?>
  </tbody>
</table>
