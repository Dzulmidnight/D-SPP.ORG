<?php 
require_once('../../Connections/dspp.php'); 
mysql_select_db($database_dspp, $dspp);

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


$fecha = time();
$idoc = $_SESSION['idoc'];


$currentPage = $_SERVER["PHP_SELF"];



/*if(isset($_GET['query'])){
	$query_opp = "SELECT *, opp.idopp AS 'idOPP', opp.nombre AS 'nombreOPP', opp.estado AS 'estadoOPP', status.idstatus, status.nombre AS 'nombreStatus', certificado.idcertificado, certificado.vigenciainicio, certificado.vigenciafin, status_pagina.nombre AS 'nombreEstatusPagina', status_publico.nombre AS 'nombreEstatusPublico' FROM opp LEFT JOIN status ON opp.estado = status.idstatus  LEFT JOIN status_pagina ON opp.estatusPagina = status_pagina.idEstatusPagina LEFT JOIN status_publico ON opp.estatusPublico = status_publico.idstatus_publico LEFT JOIN certificado ON opp.idopp = certificado.idopp where idoc='".$_SESSION['idoc']."' ORDER BY opp.nombre ASC";
}else{
	$query_opp = "SELECT *, opp.idopp AS 'idOPP', opp.nombre AS 'nombreOPP', opp.estado AS 'estadoOPP', status.idstatus, status.nombre AS 'nombreStatus', certificado.idcertificado, certificado.vigenciainicio, certificado.vigenciafin, status_pagina.nombre AS 'nombreEstatusPagina', status_publico.nombre AS 'nombreEstatusPublico' FROM opp LEFT JOIN status ON opp.estado = status.idstatus LEFT JOIN status_pagina ON opp.estatusPagina = status_pagina.idEstatusPagina LEFT JOIN status_publico ON opp.estatusPublico = status_publico.idstatus_publico LEFT JOIN certificado ON opp.idopp = certificado.idopp where idoc='".$_SESSION['idoc']."' ORDER BY opp.nombre ASC";
}*/



$query = "SELECT opp.* FROM opp WHERE idoc = $idoc";
$ejecutar = mysql_query($query,$dspp) or die(mysql_error());


$total_opp = mysql_num_rows($ejecutar);


?>

<div class="row">
  <div class="col-md-12">
    <table class="table table-bordered">
      <thead>
        <tr>
          <td><button class="btn btn-primary"></button></td>
          <td>Total OPP(s): <?php echo $total_opp; ?></td>
        </tr>
        <tr>
          <th class="text-center" style="width:100px;">#SPP</th>
          <th class="text-center" style="width:100px;">Nombre</th>
          <th class="text-center">Abreviación</th>
          <th class="text-center">Estatus Interno<br>(proceso certificación)</th>
          <th class="text-center">Estatus Certificado</th>
          <th class="text-center">Vigencia Fin<br>(certificado)</th>
          <!--<th class="text-center">Abreviación</th>-->
          <th class="text-center">Nº de Socios</th>
          <!--<th class="text-center">Email</th>
          <th class="text-center">Teléfono Oficinas</th>
          <th class="text-center">País</th>-->
          <!--<th class="text-center">OC</th>-->
          <!--<th class="text-center">Razón social</th>-->
          <!--<th class="text-center">Dirección Oficinas</th>-->
          <!--<th class="text-center">Dirección fiscal</th>-->
          <!--<th class="text-center">RFC</th>-->
          <th class="text-center">Acciones</th>
        </tr>    
      </thead>
      <tbody>
      <?php 
      while($opp = mysql_fetch_assoc($ejecutar)){

      }
       ?>
      </tbody>
    </table>
  </div>
</div>



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
