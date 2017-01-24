<?php 
require_once('../Connections/dspp.php');
require_once('../Connections/mail.php');
mysql_select_db($database_dspp, $dspp);
        //$asunto = "Nuevo Registro - D-SPP( Datos de Acceso )";
//SELECT opp.idopp, opp.idf, opp.nombre, opp.abreviacion, opp.idoc, opp.pais, opp.sitio_web, opp.email, opp.telefono, opp.estatusPagina, opp.estado, oc.abreviacion AS 'abreviacion_oc', status_pagina.nombre AS 'nombre_estatus', certificado.vigenciafin FROM opp LEFT JOIN oc ON opp.idoc = oc.idoc LEFT JOIN status_pagina ON opp.estatusPagina = status_pagina.idEstatusPagina INNER JOIN certificado ON opp.idopp = certificado.idopp WHERE opp.estado  IS NOT NULL AND opp.estado != 'ARCHIVADO' AND opp.situacion != 'CANCELADO' AND opp.situacion != 'NUEVA' ORDER BY certificado.vigenciafin

$row_opp = mysql_query("SELECT opp.idopp, opp.nombre, opp.abreviacion, opp.spp, opp.idoc, opp.pais, opp.estatus_dspp, opp.estatus_interno, oc.abreviacion AS 'abreviacion_oc', estatus_dspp.nombre AS 'nombre_estatus_dspp', estatus_interno.nombre AS 'nombre_estatus_interno', certificado.vigencia_fin, num_socios.numero FROM opp INNER JOIN oc ON opp.idoc = oc.idoc LEFT JOIN estatus_dspp ON opp.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN estatus_interno ON opp.estatus_interno = estatus_interno.idestatus_interno INNER JOIN certificado ON opp.idopp = certificado.idopp LEFT JOIN num_socios ON opp.idopp = num_socios.idopp", $dspp) or die(mysql_error());

?>
<table class="table table-bordered table-condensed" style="font-size:11px;">
  <thead>
    <form action="" method="post" id="orden" enctype="application/x-www-form-urlencoded">
      <th class="text-center">Nº</th>
      <th class="text-center" >
        #SPP<br>
        <button class="btn btn-xs btn-default" name="numero" value="idf" type="submit"><span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span></button>
        <button class="btn btn-xs btn-default" name="numeroDesc" value="idf" type="submit"><span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></button>
      </th>
      <th class="text-center" style="width:150px;">
        Nombre OPP<br>
        <button class="btn btn-xs btn-default" name="nombre" value="abreviacion" type="submit"><span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span></button>
        <button class="btn btn-xs btn-default" name="nombreDesc" value="abreviacion" type="submit"><span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></button>
      </th>
      <th class="text-center" style="width:150px;">
        Abreviación<br>
        <button class="btn btn-xs btn-default" name="abreviacion" value="abreviacion" type="submit"><span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span></button>
        <button class="btn btn-xs btn-default" name="abreviacionDesc" value="abreviacion" type="submit"><span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></button>
      </th>
      <th>OC</th>
      <th>Fecha Certificado</th>
      <th class="text-center">
        Pais<br>
        <button class="btn btn-xs btn-default" name="pais" value="pais" type="submit"><span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span></button>
        <button class="btn btn-xs btn-default" name="paisDesc" value="pais" type="submit"><span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></button>
      </th>
      <th style="width:130px;" class="text-center">
        Estatus Interno<br>
        <button class="btn btn-xs btn-default" name="estatus_interno" value="idstatus" type="submit"><span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span></button>
        <button class="btn btn-xs btn-default" name="estatus_internoDesc" value="idstatus" type="submit"><span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></button>
      </th>
      <th style="width:130px;" class="text-center">
        Estatus Público<br>
        <button class="btn btn-xs btn-default" name="estatus_publico" value="idstatus_publico" type="submit"><span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span></button>
        <button class="btn btn-xs btn-default" name="estatus_publicoDesc" value="idstatus_publico" type="submit"><span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></button>
      </th>
      <th class="text-center warning">
        Estatus General
      </th>
      <th  class="text-center">
        PRODUCTOS
      </th>
      <th style="width:130px;" class="text-center">
        # PRODUCTORES<br>
        <button class="btn btn-xs btn-default" name="productores" value="resp1" type="submit"><span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span></button>
        <button class="btn btn-xs btn-default" name="productoresDesc" value="resp1" type="submit"><span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></button>
      </th>
      <input type="hidden" name="orden" value="orden">
    </form>
  </thead>
  <tbody>
    <?php
    $contador = 1;
    while($opp = mysql_fetch_assoc($row_opp)){
      $fecha = strtotime($opp['vigencia_fin']);
    ?>
      <tr>
        <td><?php echo $contador; ?></td>
        <td><?php echo $opp['spp']; ?></td>
        <td><?php echo $opp['nombre']; ?></td>
        <td><?php echo $opp['abreviacion']; ?></td>
        <td><?php echo $opp['abreviacion_oc']; ?></td>
        <td><?php echo date('d/m/Y', $fecha); ?></td>
        <td><?php echo $opp['pais']; ?></td>
        <td><?php echo $opp['nombre_estatus_interno']; ?></td>
        <td><?php echo $opp['nombre_estatus_dspp']; ?></td>
        <td><?php echo 'estatus general'; ?></td>
        <td><?php echo 'productos'; ?></td>
        <td><?php echo $opp['numero']; ?></td>
      </tr>
    <?php
    $contador++;
    }
     ?>
  </tbody>
</table>