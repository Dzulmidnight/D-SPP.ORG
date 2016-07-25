<?php require_once('../Connections/dspp.php'); /*
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

if(isset($_POST['com_delete'])){
	$query=sprintf("delete from com where idcom = %s",GetSQLValueString($_POST['idcom'], "text"));
	$ejecutar=mysql_query($query,$dspp) or die(mysql_error());
}

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_com = 20;
$pageNum_com = 0;
if (isset($_GET['pageNum_com'])) {
  $pageNum_com = $_GET['pageNum_com'];
}
$startRow_com = $pageNum_com * $maxRows_com;

mysql_select_db($database_dspp, $dspp);
if(isset($_GET['query'])){
	$query_com = "SELECT * FROM com where idoc='".$_SESSION['idoc']."' ORDER BY nombre ASC";
}else{
	$query_com = "SELECT * FROM com where idoc='".$_SESSION['idoc']."' ORDER BY nombre ASC";
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
?>
<div class="panel panel-default">
  <div class="panel-heading">Lista Empresas</div>
  <div class="panel-body">
    <table class="table table-bordered table-hover" style="font-size:12px;">
      <thead>
        <tr>
          <th>IDF</th>
          <!--<th>Nombre</th>-->
          <!--<th>Abreviación</th>-->
          <th>Información</th>
          <!--<th>Email</th>
          <th>Teléfono Oficinas</th>
          <th>País</th>-->
          <!--<th>OC</th>-->
          <th>Dirección Oficinas</th>
          <th>Dirección Fiscal</th>
          <th>RFC</th>
          <th>RUC</th>
          <th>Eliminar</th>
        </tr>
      </thead>
      <tbody>
        <?php $cont=0; while ($row_com = mysql_fetch_assoc($com)) {$cont++; ?>
          <tr class="text-justify" style="font-size:12px;">
            <td>
              <a class="btn btn-sm btn-primary" style="width:100%" href="?COM&amp;detail&amp;idcom=<?php echo $row_com['idcom']; ?>&contact">Consultar<br><?php echo $row_com['idf']; ?></a><hr style="padding-bottom:0;margin:10px"><?php echo "<p>".$row_com['nombre'].", <u>".$row_com['abreviacion']."</u></p>"; ?>
            </td>
            <!--<td><?php echo $row_com['nombre'].", <u>".$row_com['abreviacion']."</u>"; ?></td>-->
            <!--<td><?php echo $row_com['abreviacion']; ?></td>-->
            <td>
              <?php 
                echo "<p>".$row_com['sitio_web']."</p><p>".$row_com['email']."</p><p>".$row_com['telefono']."</p>".$row_com['pais']; 
              ?>
            </td>
            <!--<td><?php echo $row_com['telefono']; ?></td>
            <td><?php echo $row_com['email']; ?></td>
            <td><?php echo $row_com['pais']; ?></td>-->


            <td><?php echo $row_com['direccion']; ?></td>
            <td><?php echo $row_com['direccion_fiscal']; ?></td>
            <td><?php echo $row_com['rfc']; ?></td>
            <td><?php echo $row_com['ruc']; ?></td>
            <td>
            <form action="" method="post">
            <button class="btn btn-danger" type="submit" value="Eliminar">
              <span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Eliminar
            </button>
            
            <input type="hidden" value="com eliminado correctamente" name="mensaje" />
            <input type="hidden" value="1" name="com_delete" />
            <input type="hidden" value="<?php echo $row_com['idcom']; ?>" name="idcom" />
            </form>
            </td>
          </tr>
          <?php }  ?>
          <? if($cont==0){?>
          <tr><td colspan="11" class="alert alert-info" role="alert">No se encontraron registros</td></tr>
          <? }?>
      </tbody>
    </table>
  </div>
</div>

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
*/?>

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

$maxRows_com = 20;
$pageNum_com = 0;
if (isset($_GET['pageNum_com'])) {
  $pageNum_com = $_GET['pageNum_com'];
}
$startRow_com = $pageNum_com * $maxRows_com;

mysql_select_db($database_dspp, $dspp);
if(isset($_GET['query'])){
  $query_com = "SELECT *, com.idcom AS 'idCOM', com.nombre AS 'nombreCOM', com.estado AS 'estadoCOM', status.idstatus, status.nombre AS 'nombreStatus', certificado.idcertificado, certificado.vigenciainicio, certificado.vigenciafin, status_pagina.nombre AS 'nombreEstatusPagina', status_publico.nombre AS 'nombreEstatusPublico' FROM com LEFT JOIN status ON com.estado = status.idstatus  LEFT JOIN status_pagina ON com.estatusPagina = status_pagina.idEstatusPagina LEFT JOIN status_publico ON com.estatusPublico = status_publico.idstatus_publico LEFT JOIN certificado ON com.idcom = certificado.idcom WHERE idoc='".$_SESSION['idoc']."' ORDER BY com.nombre ASC";
}else{
  $query_com = "SELECT *, com.idcom AS 'idCOM', com.nombre AS 'nombreCOM', com.estado AS 'estadoCOM', status.idstatus, status.nombre AS 'nombreStatus', certificado.idcertificado, certificado.vigenciainicio, certificado.vigenciafin, status_pagina.nombre AS 'nombreEstatusPagina', status_publico.nombre AS 'nombreEstatusPublico' FROM com LEFT JOIN status ON com.estado = status.idstatus LEFT JOIN status_pagina ON com.estatusPagina = status_pagina.idEstatusPagina LEFT JOIN status_publico ON com.estatusPublico = status_publico.idstatus_publico LEFT JOIN certificado ON com.idcom = certificado.idcom WHERE idoc='".$_SESSION['idoc']."' ORDER BY com.nombre ASC";
}



//  $query_com = "SELECT *, com.idcom AS 'idOPP', opp.nombre AS 'nombreCOM', com.estado AS 'estadoCOM', com.estatusPagina, status.idstatus, status.nombre AS 'nombreStatus', certificado.idcertificado, certificado.vigenciainicio, certificado.vigenciafin, status_pagina.nombre AS 'nombreEstatusPagina', status_publico.nombre AS 'nombreEstatusPublico' FROM com LEFT JOIN status ON com.estado = status.idstatus LEFT JOIN status_pagina ON com.estatusPagina = status_pagina.idEstatusPagina LEFT JOIN status_publico ON opp.estatusPublico = status_publico.idstatus_publico LEFT JOIN certificado ON com.idcom = certificado.idcom WHERE (com.estado IS NULL) OR (com.estado != 'ARCHIVADO') ORDER BY com.idcom ASC";


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


if(isset($_POST['actualizacionCOM']) && $_POST['actualizacionCOM'] == '1'){

    $row_com = mysql_query("SELECT * FROM com WHERE idoc = $_SESSION[idoc]",$dspp) or die(mysql_error());
    $cont = 1;
    $fecha = time();

    while($datosCOM = mysql_fetch_assoc($row_com)){
      //$nombre = "estatusPagina"+$datosCOM['idopp']+"";

      if(isset($_POST['estatusInterno'.$datosCOM['idcom']])){/*********************************** INICIA ESTATUS INTERNO DEL OPP ******************/
        $estatusInterno = $_POST['estatusInterno'.$datosCOM['idcom']];
        echo "este es el 1";

        if(!empty($estatusInterno)){
          echo "este es el 2";
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
          /*$queryPagina = "UPDATE opp SET estatusPagina = $estatusPagina WHERE idopp = $datosCOM[idOPP]";
          $ejecutar = mysql_query($queryPagina,$dspp) or die(mysql_error());
          //echo "cont: $cont | id($datosCOM[idopp]): $estatusInterno<br>";*/
          echo "este es el 3";
        }      
        echo "este es 4";


      }/*********************************** TERMINA ESTATUS INTERNO DEL OPP ****************************************************/


      if(isset($_POST['estatusPublico'.$datosCOM['idcom']])){/*********************************** INICIA ESTATUS PUBLICO DEL OPP ******************/
        $estatusPublico = $_POST['estatusPublico'.$datosCOM['idcom']];

        if(!empty($estatusPublico)){

          $query = "UPDATE com SET estatusPublico = $estatusPublico, estatusPublico = $estatusPublico WHERE idcom = $datosCOM[idcom]";
          $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
          /*$queryPagina = "UPDATE opp SET estatusPagina = $estatusPagina WHERE idopp = $datosCOM[idOPP]";
          $ejecutar = mysql_query($queryPagina,$dspp) or die(mysql_error());
          //echo "cont: $cont | id($datosCOM[idopp]): $estatusInterno<br>";*/
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
        $plazoDespues = ($timeVencimiento - $plazo);
        $prorroga = ($timeVencimiento + $plazo);
            // Calculamos el número de segundos que tienen 60 días

        if(!empty($finCertificado)){ // NO SE INGRESO NINGUNA FECHA

          $row_certificado = mysql_query("SELECT * FROM certificado WHERE idcom = $datosCOM[idcom]", $dspp) or die(mysql_error()); // CONSULTO SI EL OPP CUENTA CON ALGUN REGISTRO DE CERTIFICADO
          $totalCertificado = mysql_num_rows($row_certificado);
          
          if(!empty($totalCertificado)){ // SI CUENTA CON UN REGISTRO, ACTUALIZO EL MISMO
            //$query = "UPDATE certificado SET vigenciafin = '$vigenciafin' WHERE idopp = $datosCOM[idopp]";
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
            //$query = "INSERT INTO certificado(vigenciafin,idopp) VALUES('$vigenciafin',$datosCOM[idopp])";
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

              $query = "INSERT INTO certificado(status, vigenciafin, idcom) VALUES('$estatusCertificado', '$finCertificado', $datosCOM[idcom])";
              $ejecutar = mysql_query($query,$dspp) or die(mysql_error());


              //$actualizar = "UPDATE certificado SET status = '16' WHERE idcertificado = $datosCOM[idcertificado]";
              //$ejecutar = mysql_query($actualizar,$dspp) or die(mysql_error());
            
            /*********************************** FIN, CALCULAMOS FECHAS PARA ASIGNAR EL ESTATUS DEL CERTIFICADO Y DEL OPP ***********************************************/


          }

          //echo "cont: $cont | VIGENCIA FIN($datosCOM[idopp]): $vigenciafin :TOTAL Certificado: $totalCertificado<br>";
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


}


$ejecutar = mysql_query($query_com,$dspp) or die(mysql_error());
$totalCOM = mysql_num_rows($ejecutar);

$queryString_com = sprintf("&totalRows_com=%d%s", $totalRows_com, $queryString_com);
?>
<script language="JavaScript"> 
function preguntar(){ 
    if(!confirm('¿Estas seguro de eliminar el registro?')){ 
       return false; } 
} 
</script>
  <hr>
    <div style="display:inline;margin-right:10em;">
      <button class="btn btn-sm btn-primary" onclick="guardarDatos()"><span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Guardar Cambios</button><!-- BOTON GUARDAR DATOS -->
      | <span class="alert alert-warning" style="padding:7px;">Total COM: <?php echo $totalCOM; ?></span>

    </div>

    <!--<div style="display:inline;margin-right:10em;">
      Exportar Contactos
      <a href="#" onclick="document.formulario1.submit()"><img src="../../img/pdf.png"></a>
      <a href="#" onclick="document.formulario2.submit()"><img src="../../img/excel.png"></a>
    </div>-->


    <table class="table table-bordered table-condensed table-hover" style="font-size:12px;">
      <thead>
        <tr>
          <th class="text-center" style="width:100px;">#SPP</th>
          <th class="text-center">Estatus Interno<br>(proceso certificación)</th>
          <th class="text-center">Estatus Certificado</th>
          <th class="text-center">Fecha Final<br>(certificado)</th>
          <th class="text-center" style="width:100px;">Nombre</th>
          <th class="text-center">Abreviación</th>
          <th class="text-center">Información</th>
          <!--<th class="text-center">Nº de Socios</th>-->
          <!--<th class="text-center">Email</th>
          <th class="text-center">Teléfono Oficinas</th>
          <th class="text-center">País</th>-->
          <!--<th class="text-center">OC</th>-->
          <!--<th class="text-center">Razón social</th>-->
          <th class="text-center">Dirección Oficinas</th>
          <!--<th class="text-center">Dirección fiscal</th>-->
          <!--<th class="text-center">RFC</th>-->
          <th class="text-center">Acciones</th>
        </tr>
      </thead>
      <form name="formularioActualizar" id="formularioActualizar" action="" method="POST">
        <input type="hidden" name="actualizacionCOM" value="1">
        <tbody>
          <?php $cont=0; while ($row_com = mysql_fetch_assoc($com)) {$cont++; ?>
            <tr class="text-justify">
              <td>
                <a class="btn btn-primary btn-xs" style="width:100px;font-size:10px;" href="?COM&amp;detail&amp;idcom=<?php echo $row_com['idCOM']; ?>&contact">Consultar</a>
                <?php 
                if(!empty($row_com['idf'])){
                  echo "<input type='text' style='width:100%;font-size:10px;' name='idf".$row_com['idCOM']."' value='$row_com[idf]'>";
                }else{
                  echo "<input type='text' name='idf".$row_com['idCOM']."' value=''>";
                }
                 ?>
              </td>
              <td>
              <?php 
                $estatusInterno = mysql_query("SELECT com.idcom, com.estatusInterno, status.idstatus, status.nombre AS 'nombreStatus' FROM com LEFT JOIN status ON com.estatusInterno = status.idstatus  WHERE idcom = $row_com[idCOM]",$dspp) or die(mysql_error());
                $row_estatus = mysql_fetch_assoc($estatusInterno);
                if(!empty($row_estatus['estatusInterno'])){
                ?>
                  <select name="estatusInterno<?echo $row_com['idCOM']?>" id="estatusInterno">
                    <option value="">....</option>
                    <option value="21">1ra Evaluación</option>
                    <option value="23">Completar Información</option>
                    <option value="22">2ª Revisión</option>
                    <option value="4">Proceso Interrumpido</option>
                    <option value="5">Evaluación In Situ</option>
                    <option value="6">Informe de Evaluación</option>
                    <option value="7">Acciones Correctivas</option>
                    <option value="8">Dictamen Positivo</option>
                    <option value="9">Dictamen Negativo</option>
                    <option value="14">Cancelada</option>

                    <option value="10">Certificada</option>
                    <option value="12">Certificado por Expirar</option>
                    <option value="11">Certificado Expirado</option>
                    <option value="13">Suspendida</option>
                  </select>
                <?php
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

                if(!empty($row_com['vigenciafin'])){
                  if($timeVencimiento < $timeActual){
                    $alerta = "alert alert-danger";
                  }else{
                    $alerta = "alert alert-success";
                  }
                  echo "<input type='date' name='finCertificado".$row_com['idCOM']."' value='".$row_com['vigenciafin']."' class='text-center'>";
                  echo "<p style='padding:7px;width:80px;' class='text-center $alerta'></p>";
                  //echo "<p style='padding:7px;width:80px;' class='text-center $alerta'><b>$vigenciafin</b></p>";
                }else{
                  echo "<input type='date' name='finCertificado".$row_com['idCOM']."'  class='text-center'>";
                }
              ?>
              </td>

              <td >
                <?php echo $row_com['nombreCOM']; ?>
              </td>
              <td style="color:#27ae60">
                <?php echo $row_com['abreviacion']; ?>
              </td>
              <td>
                <?php echo "<u>".$row_com['sitio_web']."</u><br><u>".$row_com['email']."</u><br><u>".$row_com['telefono']."</u><br>".$row_com['pais']; ?>
              </td>
              <!--<td>
                <?php 
                $numero_socios = mysql_query("SELECT idnumero_socios, idcom, socios FROM numero_socios WHERE idcom = $row_com[idCOM] ORDER BY idnumero_socios DESC",$dspp) or die(mysql_error());
                $row_socios = mysql_fetch_assoc($numero_socios);

                if(!empty($row_socios['socios'])){
                ?>
                  <input type="text" name="numero_socios<?echo $row_com['idCOM']?>" value="<?php echo $row_socios['socios']?>" style="width:100px;">
                <?php }else{ ?>
                  <input type="text" name="numero_socios<?echo $row_com['idCOM']?>"  style="width:100px;">
                <?
                }
                 ?>
              </td>-->
              <td><?php echo $row_com['direccion']; ?></td>
              <!--<td><?php echo $row_com['direccion_fiscal']; ?></td>
              <td><?php echo $row_com['rfc']; ?></td>-->
              <td>
                <a href="?COM&detail&idcom=<?php echo $row_com['idCOM']; ?>&contact" class="btn btn-xs btn-info col-xs-6" data-toggle="tooltip" data-placement="top" title="Editar">
                  <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                </a>

               <!-- <form action="" method="post" name="formularioEliminar" ONSUBMIT="return preguntar();">
                  <button class="btn btn-xs btn-danger col-xs-6" type="subtmit" value="Eliminar" data-toggle="tooltip" data-placement="top" title="Eliminar">
                    <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                  </button>        
                  <input type="hidden" value="OPP eliminado correctamente" name="mensaje" />
                  <input type="hidden" value="1" name="com_delete" />
                  <input type="hidden" value="<?php echo $row_com['idCOM']; ?>" name="idopp" />
                </form>-->

                <!--<form action="" method="post">
                <button class="btn btn-danger" type="submit" value="Eliminar">
                  <span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Eliminar
                </button>
                
                <input type="hidden" value="OPP eliminado correctamente" name="mensaje" />
                <input type="hidden" value="1" name="com_delete" />
                <input type="hidden" value="<?php echo $row_com['idCOM']; ?>" name="idopp" />
                </form>-->
              </td>
            </tr>
            <?php }  ?>
            <? if($cont==0){?>
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

<script>
function guardarDatos(){
  document.getElementById("formularioActualizar").submit();
}

</script>


