<?php require_once('../Connections/dspp.php'); ?>
<?php
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
$timeActual = time();

if(isset($_POST['com_delete'])){
  $query=sprintf("delete from com where idcom = %s",GetSQLValueString($_POST['idcom'], "text"));
  $ejecutar=mysql_query($query,$dspp) or die(mysql_error());
}

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_com = 40;
$pageNum_com = 0;
if (isset($_GET['pageNum_com'])) {
  $pageNum_com = $_GET['pageNum_com'];
}
$startRow_com = $pageNum_com * $maxRows_com;

mysql_select_db($database_dspp, $dspp);
if(isset($_GET['query'])){
  //$query_com = "SELECT * FROM com where idoc='".$_GET['query']."' ORDER BY nombre ASC";
 $query_com = "SELECT *, com.idcom AS 'idCOM', com.nombre AS 'nombreCOM', com.estado AS 'estadoCOM', com.estatusPagina, status.idstatus, status.nombre AS 'nombreStatus', certificado.idcertificado, certificado.vigenciainicio, certificado.vigenciafin, status_pagina.nombre AS 'nombreEstatusPagina', status_publico.nombre AS 'nombreEstatusPublico' FROM com LEFT JOIN status ON com.estado = status.idstatus LEFT JOIN status_pagina ON com.estatusPagina = status_pagina.idEstatusPagina LEFT JOIN status_publico ON com.estatusPublico = status_publico.idstatus_publico LEFT JOIN certificado ON com.idcom = certificado.idcom WHERE idoc = $_GET[query] AND (com.estado IS NULL OR com.estado != 'ARCHIVADO') ORDER BY com.idcom ASC";

   $queryExportar = "SELECT com.*, contacto.*  FROM com LEFT JOIN contacto ON com.idcom = contacto.idcom WHERE idoc = $_GET[query] AND (com.estado IS NULL OR com.estado != 'ARCHIVADO') ORDER BY com.idcom ASC";

}else if(isset($_POST['filtroPalabra']) && $_POST['filtroPalabra'] == "1" && $_POST['palabraClave'] != NULL){
  $palabraClave = $_POST['palabraClave'];


  //$query_com = "SELECT * FROM com WHERE idf LIKE '%$palabraClave%' OR nombre LIKE '%$palabraClave%' OR abreviacion LIKE '%$palabraClave%' OR sitio_web LIKE '%$palabraClave%' OR email LIKE '%$palabraClave%' OR pais LIKE '%$palabraClave%' OR direccion_fiscal LIKE '%$palabraClave%' OR rfc LIKE '%$palabraClave%' ORDER BY idcom ASC";

  $query_com = "SELECT *, com.idcom AS 'idCOM' ,com.nombre AS 'nombreCOM', com.estado AS 'estadoCOM' , com.estatusPagina, status.idstatus, status.nombre AS 'nombreStatus', certificado.idcertificado, certificado.vigenciainicio, certificado.vigenciafin, status_pagina.nombre AS 'nombreEstatusPagina', status_publico.nombre AS 'nombreEstatusPublico' FROM com LEFT JOIN status ON com.estado = status.idstatus LEFT JOIN status_pagina ON com.estatusPagina = status_pagina.idEstatusPagina LEFT JOIN status_publico ON com.estatusPublico = status_publico.idstatus_publico LEFT JOIN certificado ON com.idcom = certificado.idcom WHERE (com.estado != 'ARCHIVADO' OR com.estado IS NULL) AND ((idf LIKE '%$palabraClave%') OR (com.nombre LIKE '%$palabraClave%') OR (com.abreviacion LIKE '%$palabraClave%') OR (sitio_web LIKE '%$palabraClave%') OR (email LIKE '%$palabraClave%') OR (pais LIKE '%$palabraClave%') OR (direccion_fiscal LIKE '%$palabraClave%') OR (rfc LIKE '%$palabraClave%')) ORDER BY com.idcom ASC";

  $queryExportar = "SELECT com.*, contacto.*  FROM com LEFT JOIN contacto ON com.idcom = contacto.idcom WHERE (com.estado != 'ARCHIVADO' OR com.estado IS NULL) AND ((com.idf LIKE '%$palabraClave%') OR (com.nombre LIKE '%$palabraClave%') OR (com.abreviacion LIKE '%$palabraClave%') OR (sitio_web LIKE '%$palabraClave%') OR (email LIKE '%$palabraClave%') OR (pais LIKE '%$palabraClave%') OR (direccion_fiscal LIKE '%$palabraClave%') OR (rfc LIKE '%$palabraClave%')) ORDER BY com.idcom ASC";

}else if(isset($_POST['busquedaPais']) && $_POST['busquedaPais'] == "1" && $_POST['busquedaPais'] != NULL){
  $pais = $_POST['pais'];
  //$query_com = "SELECT * FROM com WHERE pais LIKE '%$pais%'";
  $query_com = "SELECT *, com.idcom AS 'idCOM' ,com.nombre AS 'nombreCOM', com.estado AS 'estadoCOM' , com.estatusPagina, status.idstatus, status.nombre AS 'nombreStatus', certificado.idcertificado, certificado.vigenciainicio, certificado.vigenciafin, status_pagina.nombre AS 'nombreEstatusPagina', status_publico.nombre AS 'nombreEstatusPublico' FROM com LEFT JOIN status ON com.estado = status.idstatus LEFT JOIN status_pagina ON com.estatusPagina = status_pagina.idEstatusPagina LEFT JOIN status_publico ON com.estatusPublico = status_publico.idstatus_publico LEFT JOIN certificado ON com.idcom = certificado.idcom WHERE com.pais = '$pais' ORDER BY com.idcom ASC";

  $queryExportar = "SELECT com.*, contacto.*  FROM com LEFT JOIN contacto ON com.idcom = contacto.idcom WHERE com.pais = '$pais' ORDER BY com.idcom ASC";

}else if(isset($_POST['busquedaOC']) && $_POST['busquedaOC'] == 1){

  $idoc = $_POST['idoc'];

    $query_com = "SELECT *, com.idcom AS 'idCOM' ,com.nombre AS 'nombreCOM', com.estado AS 'estadoCOM' , com.estatusPagina, status.idstatus, status.nombre AS 'nombreStatus', certificado.idcertificado, certificado.vigenciainicio, certificado.vigenciafin, status_pagina.nombre AS 'nombreEstatusPagina', status_publico.nombre AS 'nombreEstatusPublico' FROM com LEFT JOIN status ON com.estado = status.idstatus LEFT JOIN status_pagina ON com.estatusPagina = status_pagina.idEstatusPagina LEFT JOIN status_publico ON com.estatusPublico = status_publico.idstatus_publico LEFT JOIN certificado ON com.idcom = certificado.idcom WHERE com.idoc = '$idoc' ORDER BY com.idcom ASC";

    $queryExportar = "SELECT com.*, contacto.*  FROM com LEFT JOIN contacto ON com.idcom = contacto.idcom WHERE com.idoc = '$idoc' ORDER BY com.idcom ASC";
  

}else if(isset($_POST['busquedaEstatus']) && $_POST['busquedaEstatus'] == 1){
  $estatus = $_POST['estatus'];

  $query_com = "SELECT *, com.idcom AS 'idCOM' ,com.nombre AS 'nombreCOM', com.estado AS 'estadoCOM' , com.estatusPagina, status.idstatus, status.nombre AS 'nombreStatus', certificado.idcertificado, certificado.vigenciainicio, certificado.vigenciafin, status_pagina.nombre AS 'nombreEstatusPagina', status_publico.nombre AS 'nombreEstatusPublico' FROM com LEFT JOIN status ON com.estado = status.idstatus LEFT JOIN status_pagina ON com.estatusPagina = status_pagina.idEstatusPagina LEFT JOIN status_publico ON com.estatusPublico = status_publico.idstatus_publico LEFT JOIN certificado ON com.idcom = certificado.idcom WHERE com.estado = '$estatus' ORDER BY com.idcom ASC";

  $queryExportar = "SELECT com.*, contacto.*  FROM com LEFT JOIN contacto ON com.idcom = contacto.idcom WHERE com.estado = '$estatus' ORDER BY com.idcom ASC";

}else{
  //$query_com = "SELECT com.* FROM com ORDER BY idcom ASC";
  $query_com = "SELECT *, com.idcom AS 'idCOM', com.nombre AS 'nombreCOM', com.estado AS 'estadoCOM', com.estatusPagina, status.idstatus, status.nombre AS 'nombreStatus', certificado.idcertificado, certificado.vigenciainicio, certificado.vigenciafin, status_pagina.nombre AS 'nombreEstatusPagina', status_publico.nombre AS 'nombreEstatusPublico' FROM com LEFT JOIN status ON com.estado = status.idstatus LEFT JOIN status_pagina ON com.estatusPagina = status_pagina.idEstatusPagina LEFT JOIN status_publico ON com.estatusPublico = status_publico.idstatus_publico LEFT JOIN certificado ON com.idcom = certificado.idcom WHERE (com.estado IS NULL) OR (com.estado != 'ARCHIVADO') ORDER BY com.idcom ASC";
  $queryExportar = "SELECT com.*, contacto.*  FROM com LEFT JOIN contacto ON com.idcom = contacto.idcom WHERE (com.estado IS NULL) OR (com.estado != 'ARCHIVADO') ORDER BY com.idcom ASC";

}
$query_limit_com = sprintf("%s LIMIT %d, %d", $query_com, $startRow_com, $maxRows_com);
$com = mysql_query($query_limit_com, $dspp) or die(mysql_error());
//$row_com = mysql_fetch_assoc($com);

if (isset($_GET['totalRows_com'])) {
  $totalRows_com = $_GET['totalRows_com'];
} else {
  $all_com = mysql_query($query_com);
  $totalRows_com = mysql_num_rows($all_com);
}
$totalPages_com = ceil($totalRows_com/$maxRows_com)-1;

$queryString_com = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_com") == false && 
        stristr($param, "totalRows_com") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_com = "&" . htmlentities(implode("&", $newParams));
  }
}


$queryString_com = sprintf("&totalRows_com=%d%s", $totalRows_com, $queryString_com);


if(isset($_POST['archivar']) && $_POST['archivar'] == 1){

  $miVariable =  $_COOKIE["variable"];
  $token = strtok($miVariable, ",");

   while ($token !== false) 
   {
      $query = "UPDATE com SET estado = 'ARCHIVADO' WHERE idcom = $token";
      $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
      //echo "$token<br>";
      $token = strtok(",");
   }

    echo '<script>borrarTodo();</script>';
    echo '<script>location.href="?COM&select";</script>';
}
if(isset($_POST['eliminar']) && $_POST['eliminar'] == 2){
  $miVariable =  $_COOKIE["variable"];
  $token = strtok($miVariable, ",");

   while ($token !== false) 
   {
      $query = "DELETE FROM com WHERE idcom = $token";
      $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
      //echo "$token<br>";
      $token = strtok(",");
   }
    echo '<script>borrarTodo();</script>';
    echo '<script>location.href="?COM&select";</script>';

}

if(isset($_POST['actualizacionCOM']) && $_POST['actualizacionCOM'] == 1){/* INICIA BOTON ACTUALIZAR LISTA OPP*/

  $row_com = mysql_query("SELECT * FROM com",$dspp) or die(mysql_error());
  $cont = 1;
  $fecha = time();

  while($datosCOM = mysql_fetch_assoc($row_com)){
    //$nombre = "estatusPagina"+$datosCOM['idcom']+"";

    if(isset($_POST['estatusPagina'.$datosCOM['idcom']])){/*********************************** INICIA ESTATUS PAGINA DEL OPP ******************/
      $estatusPagina = $_POST['estatusPagina'.$datosCOM['idcom']];

      if(!empty($estatusPagina)){
        $query = "UPDATE com SET estatusPagina = $estatusPagina WHERE idcom = $datosCOM[idcom]";
        $ejecutar = mysql_query($query,$dspp) or die(mysql_error());

        //echo "cont: $cont | id($datosCOM[idcom]): $estatusPagina<br>";
      }      
    }/*********************************** TERMINA ESTATUS PAGINA DEL OPP ****************************************************/


    if(isset($_POST['estatusInterno'.$datosCOM['idcom']])){/*********************************** INICIA ESTATUS INTERNO DEL OPP ******************/
      $estatusInterno = $_POST['estatusInterno'.$datosCOM['idcom']];

      if(!empty($estatusInterno)){
        /*
        ESTATUS PAGINA = 
        1.- EN REVISION
        2.- CERTIFICADA
        3.- REGISTRADA
        4.- CANCELADA
        */
        $estatusPagina = "";
        if($estatusInterno == 10){ //ESTATUS PAGINA = CERTIFICADO(REGISTRADO)
          $estatusPagina = 2;
        }else if($estatusInterno == 14 || $estatusInterno == 24){ // ESTATUS PAGINA = CANCELADO
          $estatusPagina = 4;
        }else{ // ESTATUS PAGINA = EN REVISION
          $estatusPagina = 1;
        }

        $query = "UPDATE com SET estatusInterno = $estatusInterno, estatusPagina = $estatusPagina WHERE idcom = $datosCOM[idcom]";
        $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
        /*$queryPagina = "UPDATE opp SET estatusPagina = $estatusPagina WHERE idcom = $datosCOM[idcom]";
        $ejecutar = mysql_query($queryPagina,$dspp) or die(mysql_error());
        //echo "cont: $cont | id($datosCOM[idcom]): $estatusInterno<br>";*/
      }      

    }/*********************************** TERMINA ESTATUS INTERNO DEL OPP ****************************************************/

    if(isset($_POST['estatusPublico'.$datosCOM['idcom']])){/*********************************** INICIA ESTATUS PUBLICO DEL OPP ******************/
      $estatusPublico = $_POST['estatusPublico'.$datosCOM['idcom']];

      if(!empty($estatusPublico)){

        $query = "UPDATE com SET estatusPublico = $estatusPublico, estatusPublico = $estatusPublico WHERE idcom = $datosCOM[idcom]";
        $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
      }      



    }/*********************************** TERMINA ESTATUS PUBLICO DEL OPP ****************************************************/

    if(isset($_POST['numero_socios'.$datosCOM['idcom']])){/*********************************** INICIA NUMERO DE SOCIOS DEL OPP ******************/
      $numero_socios = $_POST['numero_socios'.$datosCOM['idcom']];

      if(!empty($numero_socios)){
        $consultar = mysql_query("SELECT idcom,socios, fecha_captura FROM numero_socios WHERE idcom = $datosCOM[idcom] ORDER BY fecha_captura DESC LIMIT 1",$dspp) or die(mysql_error());
        $consultaNumeroSocios = mysql_fetch_assoc($consultar);

        if($consultaNumeroSocios['socios'] != $numero_socios){

          $query = "INSERT INTO numero_socios(idcom,socios,fecha_captura) VALUES($datosCOM[idcom], $numero_socios, $fecha)";
          $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
        }

      }      
    }/*********************************** TERMINA NUMERO DE SOCIOS DEL OPP ****************************************************/
    if(isset($_POST['idf'.$datosCOM['idcom']])){/*********************************** INICIA NUMERO #SPP DEL OPP ******************/
      $idf = $_POST['idf'.$datosCOM['idcom']];

      if(!empty($idf)){
        $query = "UPDATE com SET idf = '$idf' WHERE idcom = $datosCOM[idcom]";
        $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
      }      
    }/*********************************** TERMINA NUMERO #SPP DEL OPP ****************************************************/

    if(isset($_POST['finCertificado'.$datosCOM['idcom']])){ /****************** INICIA VIGENCIA FIN DEL CERTIFICADO ******************/
      $finCertificado = $_POST['finCertificado'.$datosCOM['idcom']];
      $timeActual = time();

      $timeVencimiento = strtotime($finCertificado);
      $timeRestante = ($timeVencimiento - $timeActual);
      $estatusCertificado = "";
      $plazo = 60 *(24*60*60);
      $plazoDespues = ($timeVencimiento + $plazo);
      $prorroga = ($timeVencimiento + $plazo);
          // Calculamos el número de segundos que tienen 60 días

      if(!empty($finCertificado)){ // NO SE INGRESO NINGUNA FECHA

        $row_certificado = mysql_query("SELECT * FROM certificado WHERE idcom = '$datosCOM[idcom]'", $dspp) or die(mysql_error()); // CONSULTO SI EL OPP CUENTA CON ALGUN REGISTRO DE CERTIFICADO
        $totalCertificado = mysql_num_rows($row_certificado);
        
        if(!empty($totalCertificado)){ // SI CUENTA CON UN REGISTRO, ACTUALIZO EL MISMO
          //$query = "UPDATE certificado SET vigenciafin = '$vigenciafin' WHERE idcom = $datosCOM[idcom]";
          //$ejecutar = mysql_query($query,$dspp) or die(mysql_error());

          /*********************************** INICIA, CALCULAMOS FECHAS PARA ASIGNAR EL ESTATUS DEL CERTIFICADO Y DEL OPP ***********************************************/


          if($timeActual <= $timeVencimiento){
            if($timeRestante <= $plazo){
              $estatusCertificado = 16; // AVISO DE RENOVACIÓN
            }else{
              $estatusCertificado = 10; // CERTIFICADO ACTIVO
            }
          }else{
            if($prorroga >= $timeActual){
              $estatusCertificado = 12; // CERTIFICADO POR EXPIRAR
            }else{
              $estatusCertificado = 11; // CERTIFICADO EXPIRADO
            }
          }

            $actualizar = "UPDATE com SET estado = '$estatusCertificado' WHERE idcom = '$datosCOM[idcom]'";
            $ejecutar = mysql_query($actualizar,$dspp) or die(mysql_error());

            $query = "UPDATE certificado SET status = '$estatusCertificado', vigenciafin = '$finCertificado' WHERE idcom = '$datosCOM[idcom]'";
            $ejecutar = mysql_query($query,$dspp) or die(mysql_error());


            //$actualizar = "UPDATE certificado SET status = '16' WHERE idcertificado = $datosCOM[idcertificado]";
            //$ejecutar = mysql_query($actualizar,$dspp) or die(mysql_error());
          
          /*********************************** FIN, CALCULAMOS FECHAS PARA ASIGNAR EL ESTATUS DEL CERTIFICADO Y DEL OPP ***********************************************/

        }else{ // SI NO CUENTA CON REGISTRO PREVIO, ENTONCES INSERTO UN NUEVO REGISTRO
          //$query = "INSERT INTO certificado(vigenciafin,idcom) VALUES('$vigenciafin',$datosCOM[idcom])";
          //$ejecutar = mysql_query($query,$dspp) or die(mysql_error());
          /*********************************** INICIA, CALCULAMOS FECHAS PARA ASIGNAR EL ESTATUS DEL CERTIFICADO Y DEL OPP ***********************************************/
          /*if($timeVencimiento > $timeActual){
            if($timeRestante <= $plazo){
              $estatusCertificado = 16; // AVISO DE RENOVACIÓN DEL CERTIFICADO, 1º VEZ
            }else{
              $estatusCertificado = 10; // CERTIFICADA, 1º VEZ
            }
          }else{
            $estatusCertificado = 28;
          }*/
          if($timeActual <= $timeVencimiento){
            if($timeRestante <= $plazo){
              $estatusCertificado = 16; // AVISO DE RENOVACIÓN
            }else{
              $estatusCertificado = 10; // CERTIFICADO ACTIVO
            }
          }else{
            if($prorroga >= $timeActual){
              $estatusCertificado = 12; // CERTIFICADO POR EXPIRAR
            }else{
              $estatusCertificado = 11; // CERTIFICADO EXPIRADO
            }
          }

            $actualizar = "UPDATE com SET estado = '$estatusCertificado' WHERE idcom = '$datosCOM[idcom]'";
            $ejecutar = mysql_query($actualizar,$dspp) or die(mysql_error());

            $query = "INSERT INTO certificado(status, vigenciafin, idcom) VALUES('$estatusCertificado', '$finCertificado', '$datosCOM[idcom]')";
            $ejecutar = mysql_query($query,$dspp) or die(mysql_error());


            //$actualizar = "UPDATE certificado SET status = '16' WHERE idcertificado = $datosCOM[idcertificado]";
            //$ejecutar = mysql_query($actualizar,$dspp) or die(mysql_error());
          
          /*********************************** FIN, CALCULAMOS FECHAS PARA ASIGNAR EL ESTATUS DEL CERTIFICADO Y DEL OPP ***********************************************/


        }

        //echo "cont: $cont | VIGENCIA FIN($datosCOM[idcom]): $vigenciafin :TOTAL Certificado: $totalCertificado<br>";
      }      
    }/************************************ TERMINA VIGENCIA FIN DEL CERTIFICADO ***********************************/


    if(isset($_POST['ocAsignado'.$datosCOM['idcom']])){ //********************************** INICIA LA ASIGNACION DE OC ***********************************/
      $ocAsignado = $_POST['ocAsignado'.$datosCOM['idcom']];
      if(!empty($ocAsignado)){
        $update = "UPDATE com SET idoc = '$ocAsignado' WHERE idcom = '$datosCOM[idcom]'";
        $ejecutar = mysql_query($update,$dspp) or die(mysql_error());
      }
    } //********************************** TERMINA LA ASIGNACION DE OC ***********************************/

    $cont++;
  }
  
  echo '<script>location.href="?COM&select";</script>';
} /* TERMINA BOTON ACTUALIZAR LISTA OPP*/

$rowCOM = mysql_query("SELECT * FROM com",$dspp) or die(mysql_error());
  $estatusPagina = "";


while ($actualizarCOM = mysql_fetch_assoc($rowCOM)) {

  if($actualizarCOM['estatusInterno'] == 10){ //ESTATUS PAGINA = CERTIFICADO(REGISTRADO)
    $estatusPagina = 2;
  }else if($actualizarCOM['estatusInterno'] == 14 || $actualizarCOM['estatusInterno'] == 24){ // ESTATUS PAGINA = CANCELADO
    $estatusPagina = 4;
  }else{ // ESTATUS PAGINA = EN REVISION
    $estatusPagina = 1;
  }
    
  $query = "UPDATE com SET estatusPagina = $estatusPagina WHERE idcom = $actualizarCOM[idcom]";
  $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
}

?>
<script language="JavaScript"> 
function preguntar(){ 
    if(!confirm('¿Estas seguro de eliminar el registro?, se eliminara toda la información relacionada con el mismo')){ 
       return false; } 
} 
</script>
  <hr>
  <div class="row">
    <div class="col-xs-12">
      
      <div class="alert alert-info col-xs-6" style="padding:7px;">
        <h5 class="" >Busqueda extendida(idf, nombre, abreviacion, sitio web, email, país, etc...). Sensible a acentos.</h5>

        <form method="post" name="filtro" action="" enctype="application/x-www-form-urlencoded">
          <div class="input-group">
            <input type="text" class="form-control" name="palabraClave" placeholder="Palabra clave...">
            <span class="input-group-btn">
              <input type="hidden" name="filtroPalabra" value="1">
              <button class="btn btn-default" type="submit">Buscar !</button>
            </span>
          </div><!-- /input-group -->        
        </form>
      </div><!-- /.col-lg-6 -->

      <div class="col-xs-4 alert alert-info" style="padding:7px;">
        <h5 class="" >Consultar COMs por país. Sensible a acentos</h5>
        <form action="" name="formularioPais" method="POST" enctype="application/x-www-form-urlencoded">      
          <select class="form-control" name="pais" id="" onchange="this.form.submit()">
            <option value="">Selecciona un país</option>
            <?php 
              $query = "SELECT * FROM paises";
              $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
              while($row_paises = mysql_fetch_assoc($ejecutar)){
            ?>
              <option value="<?php echo utf8_encode($row_paises['nombre']);?>"><?php echo utf8_encode($row_paises['nombre']) ?></option>
            <?php
              }
            ?>
          </select>
          <input type="hidden" name="busquedaPais" value="1">
        </form>

        <form action="" name="formularioOC" method="POST" enctype="application/x-www-form-urlencoded">
          <?php 
          $row_oc = mysql_query("SELECT * FROM oc",$dspp) or die(mysql_error());
           ?>
          <select class="form-control" name="idoc" id="" onchange="document.formularioOC.submit()">
            <option value="">Selecciona un OC</option>
            <?php 
            while($datosPais = mysql_fetch_assoc($row_oc)){
            ?>
            <option value="<?php echo utf8_encode($datosPais['idoc']);?>" ><?php echo utf8_encode($datosPais['abreviacion']);?></option>
            <?php
            }
             ?>
          </select>
          <input type="hidden" name="busquedaOC" value="1">
        </form>


      </div>
      <div class="col-xs-2 alert alert-warning">
        <?php
        $ejecutar = mysql_query($query_com,$dspp) or die(mysql_error());
        $total = mysql_num_rows($ejecutar);
        echo "<h5>Total Empresas: <u style='color:red;'>".$total."</u></h5>";
        ?>
      </div>

    </div>
  </div>
  <a class="btn btn-sm btn-warning" href="?COM&filed">Empresa(s) Archivada(s)</a>
    <div style="display:inline;margin-right:10em;">
      <button class="btn btn-sm btn-success" onclick="guardarDatos()"><span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Guardar Cambios</button><!-- BOTON GUARDAR DATOS -->
    </div>
    <div style="display:inline;margin-right:10em;">
      Exportar Contactos
      <a href="#" onclick="document.formulario1.submit()"><img src="../../img/pdf.png"></a>
      <a href="#" onclick="document.formulario2.submit()"><img src="../../img/excel.png"></a>
    </div>

  <form name="formulario1" method="POST" action="../../reporte.php">
    <input type="hidden" name="contactoPDF" value="1">
    <input type="hidden" name="queryPDF" value="<?php echo $queryExportar; ?>">
  </form>
  <form name="formulario2" method="POST" action="../../reporte.php">
    <input type="hidden" name="contactoExcel" value="2">
    <input type="hidden" name="queryExcel" value="<?php echo $queryExportar; ?>">
  </form>

    <table class="table table-condensed table-bordered table-hover" style="font-size:12px;">
      <thead>
        <tr>
          <th class="text-center">IDF</th>
          <th class="text-center">Estatus Pagina</th>
          <th class="text-center">Estatus Publico</th>
          <th class="text-center">Estatus Interno</th>
          <th class="text-center">Estatus Certificado</th>
          <th class="text-center">Vigencia Fin</th>
          <th class="text-center">Nombre</th>
          <th class="text-center">Abreviación</th>
          <!--<th class="text-center">Sitio WEB</th>
          <th class="text-center">Email COM</th>-->
          <th class="text-center">País</th>
          <th class="text-center">OC</th>
          <!--<th class="text-center">Razón social</th>
          <th class="text-center">Dirección fiscal</th>
          <th class="text-center">RFC</th>-->
          <th style="width:60px;">
            <form  style="margin: 0;padding: 0;" action="" method="POST" >            
                <button class="btn btn-xs btn-danger" type="subtmit" value="2"  name="eliminar" data-toggle="tooltip" data-placement="top" title="Eliminar" onclick="return confirm('¿Está seguro ?, los datos se eliminaran permanentemente');" >
                  <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                </button>        
                <button class="btn btn-xs btn-info" type="subtmit" value="1" name="archivar" data-toggle="tooltip" data-placement="top" title="Archivar">
                  <span class="glyphicon glyphicon-folder-open" aria-hidden="true"></span>
                </button> 
            </form>
          </th>
        </tr>      
      </thead>
      <form name="formularioActualizar" id="formularioActualizar" action="" method="POST"><!-- INICIA FORM -->
        <input type="hidden" name="actualizacionCOM" value="1">
        <tbody>
          <?php 
            $contador=0; 
            while ($row_com = mysql_fetch_assoc($com)) {
            $contador++; 

            $rowEstatusPagina = mysql_query("SELECT * FROM status_pagina",$dspp) or die(mysql_error());
          ?>
            <tr>
              <td>
                <a class="btn btn-primary btn-xs" style="width:100%;font-size:10px;" href="?COM&amp;detail&amp;idcom=<?php echo $row_com['idCOM']; ?>&contact">Consultar<br>

                </a>
                <input type="text" name="idf<?echo $row_com['idCOM']?>" value="<?php echo $row_com['idf']; ?>" placeholder="#SPP">
              </td>
              <td>
                <?php 
                if(!empty($row_com['nombreEstatusPagina'])){
                  if($row_com['estatusPagina'] == 4){
                    echo "<p class='text-center alert alert-danger' style='padding:7px;'>".$row_com['nombreEstatusPagina']."</p>";
                  }else if($row_com['estatusPagina'] == 2){
                    echo "<p class='text-center alert alert-success' style='padding:7px;'>".$row_com['nombreEstatusPagina']."</p>";
                  }else{
                    echo "<p class='text-center alert alert-warning' style='padding:7px;'>".$row_com['nombreEstatusPagina']."</p>";
                  }
                  
                }
                 ?>            
              </td>
              <td>
                <?php 
                if(empty($row_com['estatusPublico'])){
                ?>
                  <select name="estatusPublico<?echo $row_com['idCOM']?>" id="estatusPublico">
                    <option value="">---</option>
                    <?php include("../option_estadoPublico.php"); ?>
                  </select>
                <?php
                }else{
                  echo "<p class='alert alert-info' style='padding:7px;'>".$row_com['nombreEstatusPublico']."</p>";
                }
                 ?>
              </td>
              <td>
                <?php 
                  $estatusInterno = mysql_query("SELECT com.idcom, com.estatusInterno, status.idstatus, status.nombre AS 'nombreStatus' FROM com LEFT JOIN status ON com.estatusInterno = status.idstatus  WHERE com.idcom = ".$row_com['idCOM']."",$dspp) or die(mysql_error());
                  $row_estatus = mysql_fetch_assoc($estatusInterno);
                  if(!empty($row_estatus['estatusInterno'])){
                    echo "<p class='alert alert-info text-center' style='padding:7px;'>".$row_estatus['nombreStatus']."</p>";
                  }else{
                  ?>
                  <select name="estatusInterno<?echo $row_com['idCOM']?>" id="estatusInterno">
                    <option value="">---</option>
                    <?php include('../option_estados_adm.php'); ?>
                  </select>
                  <?php
                  }
                  
                ?> 
              </td>
              <td style="width:150px;">
                <?php 
                  if(isset($row_com['nombreStatus'])){
                    if($row_com['estado'] == 10){
                      echo "<input type='text' class='informacion text-center alert alert-success' style='padding:7px;' value='$row_com[nombreStatus]'>"; // CERTIFICADO ACTIVO
                    }
                    if($row_com['estado'] == 11){
                      echo "<input type='text' class='informacion text-center alert alert-danger' style='padding:7px;' value='$row_com[nombreStatus]'>"; // CERTIFICADO EXPIRADO
                    }
                    if($row_com['estado'] == 12){
                      echo "<input type='text' class='informacion text-center alert alert-warning' style='padding:7px;' value='$row_com[nombreStatus]'>"; // CERTIFICADO POR EXPIRAR
                    }
                    if($row_com['estado'] == 16){
                      echo "<input type='text' class='informacion text-center alert alert-info' style='padding:7px;' value='$row_com[nombreStatus]'>"; // AVISO DE RENOVACIÓN
                    }
                  }
                 ?>
              </td>
              <td>
                <?php 
                  $vigenciafin = date('d-m-Y', strtotime($row_com['vigenciafin']));
                  $timeVencimiento = strtotime($row_com['vigenciafin']);
                  $timeRestante = ($timeVencimiento - $timeActual);

                  if(isset($row_com['vigenciafin'])){
                    if($timeVencimiento < $timeActual){
                      $alerta = "alert alert-danger";
                    }else{
                      $alerta = "alert alert-success";
                    }
                    echo "<input type='date' name='finCertificado".$row_com['idCOM']."' value='$row_com[vigenciafin]' class='text-center'>";
                    echo "<p style='padding:7px;width:80px;' class='text-center $alerta'></p>";
                  }else{
                    echo "<input type='date' name='finCertificado".$row_com['idCOM']."' value='$row_com[vigenciafin]' class='text-center'>";
                  }
                ?>
              </td>
              <td>
                <?php 
                  if(isset($row_com['nombreCOM'])){
                    echo "<p class='text-center'>".$row_com['nombreCOM']."</p>";
                  }else{
                    echo "<p class='alert alert-danger'>No Disponible</p>";
                  } 
                ?>
              </td>
              <td>
                <?php 
                  if(isset($row_com['abreviacion'])){
                    echo "<p class='text-center'>".$row_com['abreviacion']."</p>";
                  }else{
                    echo "<p class='alert alert-danger'>No Disponible</p>";
                  } 
                ?>
              </td>          
              <td>
                <?php echo $row_com['pais']; ?>
              </td>
              <td>      
                <?
                  $query_tcom = "SELECT abreviacion FROM oc where idoc='".$row_com['idoc']."'";
                  $tcom = mysql_query($query_tcom, $dspp) or die(mysql_error());
                  $row_tcom = mysql_fetch_assoc($tcom);

                  if(isset($row_tcom['abreviacion'])){ 
                ?>
                  <a style="width:100%" href="?OC&amp;detail&amp;idoc=<?php echo $row_com['idoc']; ?>&contact">
                    <?php  echo "<p class='alert alert-success' style='padding:7px;'>".$row_tcom['abreviacion']."</p>"; ?>
                  </a>
                <?php }else{ 
                  $row_oc = mysql_query("SELECT idoc,nombre,abreviacion FROM oc",$dspp) or die(mysql_error());
                  ?>   
                    <select name="ocAsignado<?echo $row_com['idCOM']?>" id="">
                      <option value="">SELECCIONA UNA OC</option>
                    <?php 
                    while($listaOC = mysql_fetch_assoc($row_oc)){
                      echo "<option value='$listaOC[idoc]'>$listaOC[abreviacion]</option>";
                    }
                    ?>
                    </select>
                <?php } ?>
              </td>
              <td class="text-center">

                <div name="formulario">
                  <input type="checkbox" name="idcomCheckbox" id="<?php echo "idcom".$contador; ?>" value="<?php echo $row_com['idCOM'] ?>" onclick="addCheckbox()">
                </div>
            </td>

            </tr>
            <?php }  ?>
            <? if($contador==0){?>
            <tr><td colspan="11" class="alert alert-info" role="alert">No se encontraron registros</td></tr>
            <? }?>
        </tbody>
      </form>
    </table>



<table>
  <tr>
  <td width="20"><?php if ($pageNum_com > 0) { // Show if not first page ?>
  <a href="<?php printf("%s?pageNum_com=%d%s", $currentPage, 0, $queryString_com); ?>">
  <span class="glyphicon glyphicon-fast-backward" aria-hidden="true"></span>
  </a>
  <?php } // Show if not first page ?></td>
  <td width="20"><?php if ($pageNum_com > 0) { // Show if not first page ?>
  <a href="<?php printf("%s?pageNum_com=%d%s", $currentPage, max(0, $pageNum_com - 1), $queryString_com); ?>">
  <span class="glyphicon glyphicon-backward" aria-hidden="true"></span>
  </a>
  <?php } // Show if not first page ?></td>
  <td width="20"><?php if ($pageNum_com < $totalPages_com) { // Show if not last page ?>
  <a href="<?php printf("%s?pageNum_com=%d%s", $currentPage, min($totalPages_com, $pageNum_com + 1), $queryString_com); ?>">
  <span class="glyphicon glyphicon-forward" aria-hidden="true"></span>
  </a>
  <?php } // Show if not last page ?></td>
  <td width="20"><?php if ($pageNum_com < $totalPages_com) { // Show if not last page ?>
  <a href="<?php printf("%s?pageNum_com=%d%s", $currentPage, $totalPages_com, $queryString_com); ?>">
  <span class="glyphicon glyphicon-fast-forward" aria-hidden="true"></span>
  </a>
  <?php } // Show if not last page ?></td>
  </tr>
</table>
<?php
mysql_free_result($com);
?>


<script language="JavaScript"> 

var contadorPHP = 'qwerty';
var miVariable = [];
var idcom = '';


function addCheckbox(){
  var cont = 0;
  var checkboxIdcom = document.getElementsByName("idcomCheckbox");
//var precio=document.getElementById('precio').value;

  for (var i=0; i<checkboxIdcom.length; i++) {
    if (checkboxIdcom[i].checked == 1) { 
      //alert("EL VALOR ES: "+checkboxIdcom[i].value); 
      //cont = cont + 1; 
      idcom = checkboxIdcom[i].value; 
      sessionStorage[idcom] = idcom; 

    }

  }

  for(var i=0;i<sessionStorage.length;i++){
    var idcom=sessionStorage.key(i);
    miVariable[i] = idcom;
    document.cookie = 'variable='+miVariable;
  }
}



function mostrarDatos(){
  var datosDisponibles=document.getElementById('datosDisponibles');
  datosDisponibles.innerHTML='';
  for(var i=0;i<sessionStorage.length;i++){
    var idcom=sessionStorage.key(i);
    var variablePHP = "<?php $otraVariable = 6; ?>";
    datosDisponibles.innerHTML += '<div>'+idcom+'</div>';
  }
 
}

function limpiarVista() {
var datosDisponibles=document.getElementById('datosDisponibles');
datosDisponibles.innerHTML='Limpiada vista. Los datos permanecen.';
}
 
function borrarTodo() {
  var cookies = document.cookie.split(";");

  for (var i = 0; i < cookies.length; i++) {
    var cookie = cookies[i];
    var eqPos = cookie.indexOf("=");
    var name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
    document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT";
  }
  sessionStorage.clear();  

}


function preguntar(){ 
    if(!confirm('¿Estas seguro de eliminar el registro?, los datos se eliminaran permanentemen')){ 
       return false; } 
} 

function guardarDatos(){
  document.getElementById("formularioActualizar").submit();
}


</script>