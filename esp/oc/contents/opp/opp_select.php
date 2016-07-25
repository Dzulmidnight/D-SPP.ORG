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
	$query_opp = "SELECT *, opp.idopp AS 'idOPP', opp.nombre AS 'nombreOPP', opp.estado AS 'estadoOPP', status.idstatus, status.nombre AS 'nombreStatus', certificado.idcertificado, certificado.vigenciainicio, certificado.vigenciafin, status_pagina.nombre AS 'nombreEstatusPagina', status_publico.nombre AS 'nombreEstatusPublico' FROM opp LEFT JOIN status ON opp.estado = status.idstatus  LEFT JOIN status_pagina ON opp.estatusPagina = status_pagina.idEstatusPagina LEFT JOIN status_publico ON opp.estatusPublico = status_publico.idstatus_publico LEFT JOIN certificado ON opp.idopp = certificado.idopp where idoc='".$_SESSION['idoc']."' ORDER BY opp.nombre ASC";
}else{
	$query_opp = "SELECT *, opp.idopp AS 'idOPP', opp.nombre AS 'nombreOPP', opp.estado AS 'estadoOPP', status.idstatus, status.nombre AS 'nombreStatus', certificado.idcertificado, certificado.vigenciainicio, certificado.vigenciafin, status_pagina.nombre AS 'nombreEstatusPagina', status_publico.nombre AS 'nombreEstatusPublico' FROM opp LEFT JOIN status ON opp.estado = status.idstatus LEFT JOIN status_pagina ON opp.estatusPagina = status_pagina.idEstatusPagina LEFT JOIN status_publico ON opp.estatusPublico = status_publico.idstatus_publico LEFT JOIN certificado ON opp.idopp = certificado.idopp where idoc='".$_SESSION['idoc']."' ORDER BY opp.nombre ASC";
}



//  $query_opp = "SELECT *, opp.idopp AS 'idOPP', opp.nombre AS 'nombreOPP', opp.estado AS 'estadoOPP', opp.estatusPagina, status.idstatus, status.nombre AS 'nombreStatus', certificado.idcertificado, certificado.vigenciainicio, certificado.vigenciafin, status_pagina.nombre AS 'nombreEstatusPagina', status_publico.nombre AS 'nombreEstatusPublico' FROM opp LEFT JOIN status ON opp.estado = status.idstatus LEFT JOIN status_pagina ON opp.estatusPagina = status_pagina.idEstatusPagina LEFT JOIN status_publico ON opp.estatusPublico = status_publico.idstatus_publico LEFT JOIN certificado ON opp.idopp = certificado.idopp WHERE (opp.estado IS NULL) OR (opp.estado != 'ARCHIVADO') ORDER BY opp.idopp ASC";


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


if(isset($_POST['actualizacionOPP']) && $_POST['actualizacionOPP'] == '1OC'){

    $row_opp = mysql_query("SELECT * FROM opp",$dspp) or die(mysql_error());
    $cont = 1;
    $fecha = time();

    while($datosOPP = mysql_fetch_assoc($row_opp)){
      //$nombre = "estatusPagina"+$datosOPP['idopp']+"";

      if(isset($_POST['estatusInterno'.$datosOPP['idopp']])){/*********************************** INICIA ESTATUS INTERNO DEL OPP ******************/
        $estatusInterno = $_POST['estatusInterno'.$datosOPP['idopp']];

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

          $query = "UPDATE opp SET estatusInterno = $estatusInterno, estatusPagina = $estatusPagina WHERE idopp = $datosOPP[idopp]";
          $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
          /*$queryPagina = "UPDATE opp SET estatusPagina = $estatusPagina WHERE idopp = $datosOPP[idOPP]";
          $ejecutar = mysql_query($queryPagina,$dspp) or die(mysql_error());
          //echo "cont: $cont | id($datosOPP[idopp]): $estatusInterno<br>";*/
        }      



      }/*********************************** TERMINA ESTATUS INTERNO DEL OPP ****************************************************/


      if(isset($_POST['estatusPublico'.$datosOPP['idopp']])){/*********************************** INICIA ESTATUS PUBLICO DEL OPP ******************/
        $estatusPublico = $_POST['estatusPublico'.$datosOPP['idopp']];

        if(!empty($estatusPublico)){

          $query = "UPDATE opp SET estatusPublico = $estatusPublico, estatusPublico = $estatusPublico WHERE idopp = $datosOPP[idopp]";
          $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
          /*$queryPagina = "UPDATE opp SET estatusPagina = $estatusPagina WHERE idopp = $datosOPP[idOPP]";
          $ejecutar = mysql_query($queryPagina,$dspp) or die(mysql_error());
          //echo "cont: $cont | id($datosOPP[idopp]): $estatusInterno<br>";*/
        }      



      }/*********************************** TERMINA ESTATUS PUBLICO DEL OPP ****************************************************/



      


      if(isset($_POST['numero_socios'.$datosOPP['idopp']])){/*********************************** INICIA NUMERO DE SOCIOS DEL OPP ******************/
        $numero_socios = $_POST['numero_socios'.$datosOPP['idopp']];

        if(!empty($numero_socios)){
          $consultar = mysql_query("SELECT idopp,socios, fecha_captura FROM numero_socios WHERE idopp = $datosOPP[idopp] ORDER BY fecha_captura DESC LIMIT 1",$dspp) or die(mysql_error());
          $consultaNumeroSocios = mysql_fetch_assoc($consultar);

          if($consultaNumeroSocios['socios'] != $numero_socios){

            $query = "INSERT INTO numero_socios(idopp,socios,fecha_captura) VALUES($datosOPP[idopp], $numero_socios, $fecha)";
            $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
          }

        }      
      }/*********************************** TERMINA NUMERO DE SOCIOS DEL OPP ****************************************************/


      if(isset($_POST['idf'.$datosOPP['idopp']])){/*********************************** INICIA NUMERO #SPP DEL OPP ******************/
        $idf = $_POST['idf'.$datosOPP['idopp']];

        if(!empty($idf)){
          $query = "UPDATE opp SET idf = '$idf' WHERE idopp = $datosOPP[idopp]";
          $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
        }      
      }/*********************************** TERMINA NUMERO #SPP DEL OPP ****************************************************/




      if(isset($_POST['finCertificado'.$datosOPP['idopp']])){ /****************** INICIA VIGENCIA FIN DEL CERTIFICADO ******************/
        $finCertificado = $_POST['finCertificado'.$datosOPP['idopp']];
        $timeActual = time();

        $timeVencimiento = strtotime($finCertificado);
        $timeRestante = ($timeVencimiento - $timeActual);
        $estatusCertificado = "";
        $plazo = 60 *(24*60*60);
        $plazoDespues = ($timeVencimiento - $plazo);
        $prorroga = ($timeVencimiento + $plazo);
            // Calculamos el número de segundos que tienen 60 días

        if(!empty($finCertificado)){ // NO SE INGRESO NINGUNA FECHA

          $row_certificado = mysql_query("SELECT * FROM certificado WHERE idopp = '$datosOPP[idopp]'", $dspp) or die(mysql_error()); // CONSULTO SI EL OPP CUENTA CON ALGUN REGISTRO DE CERTIFICADO
          $totalCertificado = mysql_num_rows($row_certificado);
          
          if(!empty($totalCertificado)){ // SI CUENTA CON UN REGISTRO, ACTUALIZO EL MISMO
            //$query = "UPDATE certificado SET vigenciafin = '$vigenciafin' WHERE idopp = $datosOPP[idopp]";
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

              $actualizar = "UPDATE opp SET estado = '$estatusCertificado' WHERE idopp = '$datosOPP[idopp]'";
              $ejecutar = mysql_query($actualizar,$dspp) or die(mysql_error());

              $query = "UPDATE certificado SET status = '$estatusCertificado', vigenciafin = '$finCertificado' WHERE idopp = '$datosOPP[idopp]'";
              $ejecutar = mysql_query($query,$dspp) or die(mysql_error());


              //$actualizar = "UPDATE certificado SET status = '16' WHERE idcertificado = $datosOPP[idcertificado]";
              //$ejecutar = mysql_query($actualizar,$dspp) or die(mysql_error());
            
            /*********************************** FIN, CALCULAMOS FECHAS PARA ASIGNAR EL ESTATUS DEL CERTIFICADO Y DEL OPP ***********************************************/

          }else{ // SI NO CUENTA CON REGISTRO PREVIO, ENTONCES INSERTO UN NUEVO REGISTRO
            //$query = "INSERT INTO certificado(vigenciafin,idopp) VALUES('$vigenciafin',$datosOPP[idopp])";
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


              $actualizar = "UPDATE opp SET estado = '$estatusCertificado' WHERE idopp = '$datosOPP[idopp]'";
              $ejecutar = mysql_query($actualizar,$dspp) or die(mysql_error());

              $query = "INSERT INTO certificado(status, vigenciafin, idopp) VALUES('$estatusCertificado', '$finCertificado', '$datosOPP[idopp]'')";
              $ejecutar = mysql_query($query,$dspp) or die(mysql_error());


              //$actualizar = "UPDATE certificado SET status = '16' WHERE idcertificado = $datosOPP[idcertificado]";
              //$ejecutar = mysql_query($actualizar,$dspp) or die(mysql_error());
            
            /*********************************** FIN, CALCULAMOS FECHAS PARA ASIGNAR EL ESTATUS DEL CERTIFICADO Y DEL OPP ***********************************************/


          }

          //echo "cont: $cont | VIGENCIA FIN($datosOPP[idopp]): $vigenciafin :TOTAL Certificado: $totalCertificado<br>";
        }      
      }/************************************ TERMINA VIGENCIA FIN DEL CERTIFICADO ***********************************/


      if(isset($_POST['ocAsignado'.$datosOPP['idopp']])){ //********************************** INICIA LA ASIGNACION DE OC ***********************************/
        $ocAsignado = $_POST['ocAsignado'.$datosOPP['idopp']];
        if(!empty($ocAsignado)){
          $update = "UPDATE opp SET idoc = '$ocAsignado' WHERE idopp = '$datosOPP[idopp]'";
          $ejecutar = mysql_query($update,$dspp) or die(mysql_error());
        }
      } //********************************** TERMINA LA ASIGNACION DE OC ***********************************/

      $cont++;
    }
    
    echo '<script>location.href="?OPP&select";</script>';


}


$ejecutar = mysql_query($query_opp,$dspp) or die(mysql_error());
$totalOPP = mysql_num_rows($ejecutar);

$queryString_opp = sprintf("&totalRows_opp=%d%s", $totalRows_opp, $queryString_opp);
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
      | <span class="alert alert-warning" style="padding:7px;">Total OPP: <?php echo $totalOPP; ?></span>

    </div>

    <!--<div style="display:inline;margin-right:10em;">
      Exportar Contactos
      <a href="#" onclick="document.formulario1.submit()"><img src="../../img/pdf.png"></a>
      <a href="#" onclick="document.formulario2.submit()"><img src="../../img/excel.png"></a>
    </div>-->


    <table class="table table-bordered table-condensed table-hover" style="font-size:11px;">
      <thead>
        <tr>
          <th class="text-center" style="width:100px;">#SPP</th>
          <th class="text-center">Estatus Interno<br>(proceso certificación)</th>
          <th class="text-center">Estatus Certificado</th>
          <th class="text-center">Fecha Final<br>(certificado)</th>
          <th class="text-center" style="width:100px;">Nombre</th>
          <th class="text-center">Abreviación</th>
          <!--<th class="text-center">Abreviación</th>-->
          <th >Información</th>
          <th class="text-center">Nº de Socios</th>
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
        <input type="hidden" name="actualizacionOPP" value="1OC">
        <tbody>
          <?php $cont=0; while ($row_opp = mysql_fetch_assoc($opp)) {$cont++; ?>
            <tr class="text-justify">
              <td>
                <a class="btn btn-primary btn-xs" style="width:100px;font-size:10px;" href="?OPP&amp;detail&amp;idopp=<?php echo $row_opp['idOPP']; ?>&contact">Consultar</a>
                <?php 
                if(!empty($row_opp['idf'])){
                  echo "<input type='text' style='width:100px;font-size:10px;' name='idf".$row_opp['idOPP']."' value='$row_opp[idf]'>";
                }else{
                  echo "<input type='text' name='idf".$row_opp['idOPP']."' value=''>";
                }
                 ?>
              </td>
              <td>
              <?php 
                $estatusInterno = mysql_query("SELECT opp.idopp, opp.estatusInterno, status.idstatus, status.nombre AS 'nombreStatus' FROM opp LEFT JOIN status ON opp.estatusInterno = status.idstatus  WHERE idopp = $row_opp[idOPP]",$dspp) or die(mysql_error());
                $row_estatus = mysql_fetch_assoc($estatusInterno);
                if(!empty($row_estatus['estatusInterno'])){
                ?>
                  <select name="estatusInterno<?echo $row_opp['idOPP']?>" id="estatusInterno">
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
                <select name="estatusInterno<?echo $row_opp['idOPP']?>" id="estatusInterno">
                  <option value="">---</option>
                  <?php include('../option_estados_adm.php'); ?>
                </select>
                <?php
                }
                
              ?> 
              </td>
              <td style="width:150px;">
              <?php 
                if(isset($row_opp['nombreStatus'])){
                  if($row_opp['estado'] == 10){
                    echo "<input type='text' class='informacion text-center alert alert-success' style='padding:7px;' value='$row_opp[nombreStatus]'>"; // CERTIFICADO ACTIVO
                  }
                  if($row_opp['estado'] == 11){
                    echo "<input type='text' class='informacion text-center alert alert-danger' style='padding:7px;' value='$row_opp[nombreStatus]'>"; // CERTIFICADO EXPIRADO
                  }
                  if($row_opp['estado'] == 12){
                    echo "<input type='text' class='informacion text-center alert alert-warning' style='padding:7px;' value='$row_opp[nombreStatus]'>"; // CERTIFICADO POR EXPIRAR
                  }
                  if($row_opp['estado'] == 16){
                    echo "<input type='text' class='informacion text-center alert alert-info' style='padding:7px;' value='$row_opp[nombreStatus]'>"; // AVISO DE RENOVACIÓN
                  }
                }
               ?>
              </td>
              <td>
              <?php 
                $vigenciafin = date('d-m-Y', strtotime($row_opp['vigenciafin']));
                $timeVencimiento = strtotime($row_opp['vigenciafin']);
                $timeRestante = ($timeVencimiento - $timeActual);

                if(!empty($row_opp['vigenciafin'])){
                  if($timeVencimiento < $timeActual){
                    $alerta = "alert alert-danger";
                  }else{
                    $alerta = "alert alert-success";
                  }
                  echo "<input type='date' name='finCertificado".$row_opp['idOPP']."' value='".$row_opp['vigenciafin']."' class='text-center'>";
                  echo "<p style='padding:7px;width:80px;' class='text-center $alerta'></p>";
                  //echo "<p style='padding:7px;width:80px;' class='text-center $alerta'><b>$vigenciafin</b></p>";
                }else{
                  echo "<input type='date' name='finCertificado".$row_opp['idOPP']."'  class='text-center'>";
                }
              ?>
              </td>

              <td>
                <?php echo $row_opp['nombreOPP']; ?>
              </td>
              <td style="color:#27ae60">
                <?php echo $row_opp['abreviacion']; ?>
              </td>
              <td>
                <?php echo "<u>".$row_opp['sitio_web']."</u><br><u>".$row_opp['email']."</u><br><u>".$row_opp['telefono']."</u><br>".$row_opp['pais']; ?>
              </td>
              <td>
                <?php 
                $numero_socios = mysql_query("SELECT idnumero_socios, idopp, socios FROM numero_socios WHERE idopp = $row_opp[idOPP] ORDER BY idnumero_socios DESC",$dspp) or die(mysql_error());
                $row_socios = mysql_fetch_assoc($numero_socios);

                if(!empty($row_socios['socios'])){
                ?>
                  <input type="text" name="numero_socios<?echo $row_opp['idOPP']?>" value="<?php echo $row_socios['socios']?>" style="width:100px;">
                <?php }else{ ?>
                  <input type="text" name="numero_socios<?echo $row_opp['idOPP']?>"  style="width:100px;">
                <?
                }
                 ?>
              </td>
              <td><?php echo $row_opp['direccion']; ?></td>
              <!--<td><?php echo $row_opp['direccion_fiscal']; ?></td>
              <td><?php echo $row_opp['rfc']; ?></td>-->
              <td>
                <a href="?OPP&detail&idopp=<?php echo $row_opp['idOPP']; ?>&contact" class="btn btn-xs btn-info col-xs-6" data-toggle="tooltip" data-placement="top" title="Editar">
                  <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                </a>

               <!-- <form action="" method="post" name="formularioEliminar" ONSUBMIT="return preguntar();">
                  <button class="btn btn-xs btn-danger col-xs-6" type="subtmit" value="Eliminar" data-toggle="tooltip" data-placement="top" title="Eliminar">
                    <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                  </button>        
                  <input type="hidden" value="OPP eliminado correctamente" name="mensaje" />
                  <input type="hidden" value="1" name="opp_delete" />
                  <input type="hidden" value="<?php echo $row_opp['idOPP']; ?>" name="idopp" />
                </form>-->

                <!--<form action="" method="post">
                <button class="btn btn-danger" type="submit" value="Eliminar">
                  <span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Eliminar
                </button>
                
                <input type="hidden" value="OPP eliminado correctamente" name="mensaje" />
                <input type="hidden" value="1" name="opp_delete" />
                <input type="hidden" value="<?php echo $row_opp['idOPP']; ?>" name="idopp" />
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

<script>
function guardarDatos(){
  document.getElementById("formularioActualizar").submit();
}

</script>
