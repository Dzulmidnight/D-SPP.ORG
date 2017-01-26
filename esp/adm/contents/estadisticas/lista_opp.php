<?php 
require_once('../Connections/dspp.php');
require_once('../Connections/mail.php');
mysql_select_db($database_dspp, $dspp);
        //$asunto = "Nuevo Registro - D-SPP( Datos de Acceso )";
//SELECT opp.idopp, opp.idf, opp.nombre, opp.abreviacion, opp.idoc, opp.pais, opp.sitio_web, opp.email, opp.telefono, opp.estatusPagina, opp.estado, oc.abreviacion AS 'abreviacion_oc', status_pagina.nombre AS 'nombre_estatus', certificado.vigenciafin FROM opp LEFT JOIN oc ON opp.idoc = oc.idoc LEFT JOIN status_pagina ON opp.estatusPagina = status_pagina.idEstatusPagina INNER JOIN certificado ON opp.idopp = certificado.idopp WHERE opp.estado  IS NOT NULL AND opp.estado != 'ARCHIVADO' AND opp.situacion != 'CANCELADO' AND opp.situacion != 'NUEVA' ORDER BY certificado.vigenciafin
if(isset($_POST['buscar_filtros'])){
  $pais = $_POST['pais'];
  $oc = $_POST['oc'];
  $productos = $_POST['productos'];

  /* ESTATUS GENERAL*/
  /*
  EN REVISION: abarca los estatu


  */


  if(!empty($pais) && !empty($oc) && !empty($productos)){
    $row_opp = mysql_query("SELECT opp.idopp, opp.nombre, opp.abreviacion, opp.spp, opp.idoc, opp.pais, opp.estatus_publico, estatus_publico.nombre AS 'nombre_estatus_publico', opp.estatus_dspp, opp.estatus_interno, oc.abreviacion AS 'abreviacion_oc', estatus_dspp.nombre AS 'nombre_estatus_dspp', estatus_interno.nombre AS 'nombre_estatus_interno', MAX(certificado.vigencia_fin) AS 'fecha_certificado', num_socios.numero FROM opp INNER JOIN estatus_publico ON opp.estatus_publico = estatus_publico.idestatus_publico INNER JOIN oc ON opp.idoc = oc.idoc LEFT JOIN estatus_dspp ON opp.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN estatus_interno ON opp.estatus_interno = estatus_interno.idestatus_interno INNER JOIN certificado ON opp.idopp = certificado.idopp LEFT JOIN num_socios ON opp.idopp = num_socios.idopp LEFT JOIN productos ON opp.idopp = productos.idopp WHERE opp.pais = '$pais' AND opp.idoc = $oc AND productos.producto_general LIKE '%$productos%' GROUP BY opp.idopp ORDER BY opp.idopp", $dspp) or die(mysql_error());
  }else if(!empty($pais) && !empty($oc) && empty($productos)){
    $row_opp = mysql_query("SELECT opp.idopp, opp.nombre, opp.abreviacion, opp.spp, opp.idoc, opp.pais, opp.estatus_publico, estatus_publico.nombre AS 'nombre_estatus_publico', opp.estatus_dspp, opp.estatus_interno, oc.abreviacion AS 'abreviacion_oc', estatus_dspp.nombre AS 'nombre_estatus_dspp', estatus_interno.nombre AS 'nombre_estatus_interno', MAX(certificado.vigencia_fin) AS 'fecha_certificado', num_socios.numero FROM opp INNER JOIN estatus_publico ON opp.estatus_publico = estatus_publico.idestatus_publico INNER JOIN oc ON opp.idoc = oc.idoc LEFT JOIN estatus_dspp ON opp.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN estatus_interno ON opp.estatus_interno = estatus_interno.idestatus_interno INNER JOIN certificado ON opp.idopp = certificado.idopp LEFT JOIN num_socios ON opp.idopp = num_socios.idopp LEFT JOIN productos ON opp.idopp = productos.idopp WHERE opp.pais = '$pais' AND opp.idoc = $oc GROUP BY opp.idopp ORDER BY opp.idopp", $dspp) or die(mysql_error());
  }else if(!empty($pais) && empty($oc) && empty($productos)){
    $row_opp = mysql_query("SELECT opp.idopp, opp.nombre, opp.abreviacion, opp.spp, opp.idoc, opp.pais, opp.estatus_publico, estatus_publico.nombre AS 'nombre_estatus_publico', opp.estatus_dspp, opp.estatus_interno, oc.abreviacion AS 'abreviacion_oc', estatus_dspp.nombre AS 'nombre_estatus_dspp', estatus_interno.nombre AS 'nombre_estatus_interno', MAX(certificado.vigencia_fin) AS 'fecha_certificado', num_socios.numero FROM opp INNER JOIN estatus_publico ON opp.estatus_publico = estatus_publico.idestatus_publico INNER JOIN oc ON opp.idoc = oc.idoc LEFT JOIN estatus_dspp ON opp.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN estatus_interno ON opp.estatus_interno = estatus_interno.idestatus_interno INNER JOIN certificado ON opp.idopp = certificado.idopp LEFT JOIN num_socios ON opp.idopp = num_socios.idopp LEFT JOIN productos ON opp.idopp = productos.idopp WHERE opp.pais = '$pais' GROUP BY opp.idopp ORDER BY opp.idopp", $dspp) or die(mysql_error());
  }else if(empty($pais) && !empty($oc) && !empty($productos)){
    $row_opp = mysql_query("SELECT opp.idopp, opp.nombre, opp.abreviacion, opp.spp, opp.idoc, opp.pais, opp.estatus_publico, estatus_publico.nombre AS 'nombre_estatus_publico', opp.estatus_dspp, opp.estatus_interno, oc.abreviacion AS 'abreviacion_oc', estatus_dspp.nombre AS 'nombre_estatus_dspp', estatus_interno.nombre AS 'nombre_estatus_interno', MAX(certificado.vigencia_fin) AS 'fecha_certificado', num_socios.numero FROM opp INNER JOIN estatus_publico ON opp.estatus_publico = estatus_publico.idestatus_publico INNER JOIN oc ON opp.idoc = oc.idoc LEFT JOIN estatus_dspp ON opp.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN estatus_interno ON opp.estatus_interno = estatus_interno.idestatus_interno INNER JOIN certificado ON opp.idopp = certificado.idopp LEFT JOIN num_socios ON opp.idopp = num_socios.idopp LEFT JOIN productos ON opp.idopp = productos.idopp WHERE opp.idoc = $oc AND productos.producto_general LIKE '%$productos%' GROUP BY opp.idopp ORDER BY opp.idopp", $dspp) or die(mysql_error());
  }else if(empty($pais) && empty($oc) && !empty($productos)){
    $row_opp = mysql_query("SELECT opp.idopp, opp.nombre, opp.abreviacion, opp.spp, opp.idoc, opp.pais, opp.estatus_publico, estatus_publico.nombre AS 'nombre_estatus_publico', opp.estatus_dspp, opp.estatus_interno, oc.abreviacion AS 'abreviacion_oc', estatus_dspp.nombre AS 'nombre_estatus_dspp', estatus_interno.nombre AS 'nombre_estatus_interno', MAX(certificado.vigencia_fin) AS 'fecha_certificado', num_socios.numero FROM opp INNER JOIN estatus_publico ON opp.estatus_publico = estatus_publico.idestatus_publico INNER JOIN oc ON opp.idoc = oc.idoc LEFT JOIN estatus_dspp ON opp.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN estatus_interno ON opp.estatus_interno = estatus_interno.idestatus_interno INNER JOIN certificado ON opp.idopp = certificado.idopp LEFT JOIN num_socios ON opp.idopp = num_socios.idopp LEFT JOIN productos ON opp.idopp = productos.idopp WHERE productos.producto_general LIKE '%$productos%' GROUP BY opp.idopp ORDER BY opp.idopp", $dspp) or die(mysql_error());
  }else if(!empty($pais) && empty($oc) && !empty($productos)){
    $row_opp = mysql_query("SELECT opp.idopp, opp.nombre, opp.abreviacion, opp.spp, opp.idoc, opp.pais, opp.estatus_publico, estatus_publico.nombre AS 'nombre_estatus_publico', opp.estatus_dspp, opp.estatus_interno, oc.abreviacion AS 'abreviacion_oc', estatus_dspp.nombre AS 'nombre_estatus_dspp', estatus_interno.nombre AS 'nombre_estatus_interno', MAX(certificado.vigencia_fin) AS 'fecha_certificado', num_socios.numero FROM opp INNER JOIN estatus_publico ON opp.estatus_publico = estatus_publico.idestatus_publico INNER JOIN oc ON opp.idoc = oc.idoc LEFT JOIN estatus_dspp ON opp.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN estatus_interno ON opp.estatus_interno = estatus_interno.idestatus_interno INNER JOIN certificado ON opp.idopp = certificado.idopp LEFT JOIN num_socios ON opp.idopp = num_socios.idopp LEFT JOIN productos ON opp.idopp = productos.idopp WHERE opp.pais = '$pais' AND productos.producto_general LIKE '%$productos%' GROUP BY opp.idopp ORDER BY opp.idopp", $dspp) or die(mysql_error());
  }else if(empty($pais) && !empty($oc) && empty($productos)){
    $row_opp = mysql_query("SELECT opp.idopp, opp.nombre, opp.abreviacion, opp.spp, opp.idoc, opp.pais, opp.estatus_publico, estatus_publico.nombre AS 'nombre_estatus_publico', opp.estatus_dspp, opp.estatus_interno, oc.abreviacion AS 'abreviacion_oc', estatus_dspp.nombre AS 'nombre_estatus_dspp', estatus_interno.nombre AS 'nombre_estatus_interno', MAX(certificado.vigencia_fin) AS 'fecha_certificado', num_socios.numero FROM opp INNER JOIN estatus_publico ON opp.estatus_publico = estatus_publico.idestatus_publico INNER JOIN oc ON opp.idoc = oc.idoc LEFT JOIN estatus_dspp ON opp.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN estatus_interno ON opp.estatus_interno = estatus_interno.idestatus_interno INNER JOIN certificado ON opp.idopp = certificado.idopp LEFT JOIN num_socios ON opp.idopp = num_socios.idopp LEFT JOIN productos ON opp.idopp = productos.idopp WHERE opp.idoc = $oc GROUP BY opp.idopp ORDER BY opp.idopp", $dspp) or die(mysql_error());
  }else{
    $row_opp = mysql_query("SELECT opp.idopp, opp.nombre, opp.abreviacion, opp.spp, opp.idoc, opp.pais, opp.estatus_publico, estatus_publico.nombre AS 'nombre_estatus_publico', opp.estatus_dspp, opp.estatus_interno, oc.abreviacion AS 'abreviacion_oc', estatus_dspp.nombre AS 'nombre_estatus_dspp', estatus_interno.nombre AS 'nombre_estatus_interno', MAX(certificado.vigencia_fin) AS 'fecha_certificado', num_socios.numero FROM opp INNER JOIN estatus_publico ON opp.estatus_publico = estatus_publico.idestatus_publico INNER JOIN oc ON opp.idoc = oc.idoc LEFT JOIN estatus_dspp ON opp.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN estatus_interno ON opp.estatus_interno = estatus_interno.idestatus_interno INNER JOIN certificado ON opp.idopp = certificado.idopp LEFT JOIN num_socios ON opp.idopp = num_socios.idopp LEFT JOIN productos ON opp.idopp = productos.idopp GROUP BY opp.idopp ORDER BY opp.idopp", $dspp) or die(mysql_error());
  }

}else{
  if(isset($_POST['ordenar_desc'])){
    $ordenar = $_POST['ordenar_desc'];
    $row_opp = mysql_query("SELECT opp.idopp, opp.nombre, opp.abreviacion, opp.spp, opp.idoc, opp.pais, opp.estatus_publico, estatus_publico.nombre AS 'nombre_estatus_publico', opp.estatus_dspp, opp.estatus_interno, oc.abreviacion AS 'abreviacion_oc', estatus_dspp.nombre AS 'nombre_estatus_dspp', estatus_interno.nombre AS 'nombre_estatus_interno', MAX(certificado.vigencia_fin) AS 'fecha_certificado', num_socios.numero FROM opp INNER JOIN estatus_publico ON opp.estatus_publico = estatus_publico.idestatus_publico INNER JOIN oc ON opp.idoc = oc.idoc LEFT JOIN estatus_dspp ON opp.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN estatus_interno ON opp.estatus_interno = estatus_interno.idestatus_interno INNER JOIN certificado ON opp.idopp = certificado.idopp LEFT JOIN num_socios ON opp.idopp = num_socios.idopp GROUP BY opp.idopp ORDER BY $ordenar DESC", $dspp) or die(mysql_error());

  }else if(isset($_POST['ordenar_asc'])){
    $ordenar = $_POST['ordenar_asc'];
    $row_opp = mysql_query("SELECT opp.idopp, opp.nombre, opp.abreviacion, opp.spp, opp.idoc, opp.pais, opp.estatus_publico, estatus_publico.nombre AS 'nombre_estatus_publico', opp.estatus_dspp, opp.estatus_interno, oc.abreviacion AS 'abreviacion_oc', estatus_dspp.nombre AS 'nombre_estatus_dspp', estatus_interno.nombre AS 'nombre_estatus_interno', MAX(certificado.vigencia_fin) AS 'fecha_certificado', num_socios.numero FROM opp INNER JOIN estatus_publico ON opp.estatus_publico = estatus_publico.idestatus_publico INNER JOIN oc ON opp.idoc = oc.idoc LEFT JOIN estatus_dspp ON opp.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN estatus_interno ON opp.estatus_interno = estatus_interno.idestatus_interno INNER JOIN certificado ON opp.idopp = certificado.idopp LEFT JOIN num_socios ON opp.idopp = num_socios.idopp GROUP BY opp.idopp ORDER BY $ordenar ASC", $dspp) or die(mysql_error());

  }else{
    $row_opp = mysql_query("SELECT opp.idopp, opp.nombre, opp.abreviacion, opp.spp, opp.idoc, opp.pais, opp.estatus_publico, estatus_publico.nombre AS 'nombre_estatus_publico', opp.estatus_dspp, opp.estatus_interno, oc.abreviacion AS 'abreviacion_oc', estatus_dspp.nombre AS 'nombre_estatus_dspp', estatus_interno.nombre AS 'nombre_estatus_interno', MAX(certificado.vigencia_fin) AS 'fecha_certificado', num_socios.numero FROM opp INNER JOIN estatus_publico ON opp.estatus_publico = estatus_publico.idestatus_publico INNER JOIN oc ON opp.idoc = oc.idoc LEFT JOIN estatus_dspp ON opp.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN estatus_interno ON opp.estatus_interno = estatus_interno.idestatus_interno INNER JOIN certificado ON opp.idopp = certificado.idopp LEFT JOIN num_socios ON opp.idopp = num_socios.idopp LEFT JOIN productos ON opp.idopp = productos.idopp GROUP BY opp.idopp ORDER BY opp.idopp", $dspp) or die(mysql_error());

  }
}

function mayus($variable) {
$variable = strtr(strtoupper($variable),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
return $variable;
}

$total_registro = mysql_num_rows($row_opp);
$row_pais = mysql_query("SELECT opp.pais FROM opp GROUP BY pais", $dspp) or die(mysql_error());
$row_oc = mysql_query("SELECT idoc, abreviacion FROM oc", $dspp) or die(mysql_error());
$row_productos = mysql_query("SELECT productos.producto_general FROM productos GROUP BY productos.producto_general", $dspp) or die(mysql_error());

?>



<table class="table table-bordered table-condensed table-hover" style="font-size:11px;">
  <thead>
      <tr>
        <th colspan="10">
          <form action="" id="busqueda_filtros" method="POST">
            <select name="pais" id="pais">
              <option value="">Filtrar por País</option>
              <?php 
              while($pais = mysql_fetch_assoc($row_pais)){
                echo '<option value='.$pais['pais'].'>'.$pais['pais'].'</option>';
              }
               ?>
            </select>
            <select name="oc" id="oc">
              <option value="">Filtrar por OC</option>
              <?php 
              while($oc = mysql_fetch_assoc($row_oc)){
                echo '<option value='.$oc['idoc'].'>'.$oc['abreviacion'].'</option>';
              }
               ?>
            </select>
            <select name="productos" id="productos">
              <option value="">Filtrar por Productos</option>
              <?php 
              while($producto = mysql_fetch_assoc($row_productos)){
                echo '<option value='.$producto['producto_general'].'>'.$producto['producto_general'].'</option>';
              }
               ?>
            </select>
            <button class="btn btn-sm btn-default" name="buscar_filtros">Buscar</button>
          </form>
        </th>
        <th colspan="2" style="color:red;font-size:13px;">Numero de Registros: <?php echo $total_registro; ?></th>
      </tr>
      <tr class="success">
        <th class="text-center">Nº</th>
        <th class="text-center" >
          #SPP<br>
          <form action="" method="POST">
            <button class="btn btn-xs btn-default" name="ordenar_desc" value="opp.spp" type="submit"><span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span></button>
            <button class="btn btn-xs btn-default" name="ordenar_asc" value="opp.spp" type="submit"><span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></button>
          </form>
        </th>
        <th class="text-center" style="width:150px;">
          Nombre OPP<br>
          <form action="" method="POST">
            <button class="btn btn-xs btn-default" name="ordenar_desc" value="opp.nombre" type="submit"><span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span></button>
            <button class="btn btn-xs btn-default" name="ordenar_asc" value="opp.nombre" type="submit"><span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></button>
          </form>
        </th>
        <th class="text-center" style="width:150px;">
          Abreviación<br>
          <form action="" method="POST">
            <button class="btn btn-xs btn-default" name="ordenar_desc" value="opp.abreviacion" type="submit"><span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span></button>
            <button class="btn btn-xs btn-default" name="ordenar_asc" value="opp.abreviacion" type="submit"><span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></button>
          </form>
        </th>
        <th>
          OC
          <form action="" method="POST">
            <button class="btn btn-xs btn-default" name="ordenar_desc" value="oc.abreviacion" type="submit"><span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span></button>
            <button class="btn btn-xs btn-default" name="ordenar_asc" value="oc.abreviacion" type="submit"><span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></button>
          </form> 
        </th>
        <th>
          Fecha Certificado
          <form action="" method="POST">
            <button class="btn btn-xs btn-default" name="ordenar_desc" value="fecha_certificado" type="submit"><span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span></button>
            <button class="btn btn-xs btn-default" name="ordenar_asc" value="fecha_certificado" type="submit"><span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></button>
          </form>
        </th>
        <th class="text-center">
          Pais<br>
          <form action="" method="POST">
            <button class="btn btn-xs btn-default" name="ordenar_desc" value="opp.pais" type="submit"><span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span></button>
            <button class="btn btn-xs btn-default" name="ordenar_asc" value="opp.pais" type="submit"><span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></button>
          </form>
        </th>
        <th style="width:130px;" class="text-center">
          Estatus Interno<br>
          <form action="" method="POST">
            <button class="btn btn-xs btn-default" name="ordenar_desc" value="estatus_interno.nombre" type="submit"><span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span></button>
            <button class="btn btn-xs btn-default" name="ordenar_asc" value="estatus_interno.nombre" type="submit"><span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></button>
          </form>
        </th>
        <th style="width:130px;" class="text-center">
          Estatus Certificado<br>
          <form action="" method="POST">
            <button class="btn btn-xs btn-default" name="ordenar_desc" value="idstatus_publico" type="submit"><span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span></button>
            <button class="btn btn-xs btn-default" name="ordenar_asc" value="idstatus_publico" type="submit"><span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></button>
          </form>
        </th>
        <th class="text-center warning">
          Estatus General
          <form action="" method="POST">
            <button class="btn btn-xs btn-default" name="ordenar_desc" value="estatus_publico.nombre" type="submit"><span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span></button>
            <button class="btn btn-xs btn-default" name="ordenar_asc" value="estatus_publico.nombre" type="submit"><span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></button>
          </form>
        </th>
        <th  class="text-center">
          PRODUCTOS
        </th>
        <th style="width:130px;" class="text-center">
          # PRODUCTORES<br>
          <form action="" method="POST">
            <button class="btn btn-xs btn-default" name="ordenar_desc" value="num_socios.numero" type="submit"><span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span></button>
            <button class="btn btn-xs btn-default" name="ordenar_asc" value="num_socios.numero" type="submit"><span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></button>
          </form>
        </th>
      </tr>
  </thead>
  <tbody>
    <?php
    $contador = 1;
    while($opp = mysql_fetch_assoc($row_opp)){
      $row_productos = mysql_query("SELECT productos.idopp, GROUP_CONCAT(productos.producto SEPARATOR ' , ') AS 'productos_opp' FROM productos WHERE productos.idopp = $opp[idopp]", $dspp) or die(mysql_error());
      $productos = mysql_fetch_assoc($row_productos);
      $fecha = strtotime($opp['fecha_certificado']);
    ?>
      <tr>
        <td><?php echo $contador; ?></td>
        <td><?php echo $opp['idopp']." - ".$opp['spp']; ?></td>
        <td><?php echo mayus($opp['nombre']); ?></td>
        <td><?php echo mayus($opp['abreviacion']); ?></td>
        <td><?php echo mayus($opp['abreviacion_oc']); ?></td>
        <td><?php echo date('d/m/Y', $fecha); ?></td>
        <td><?php echo $opp['pais']; ?></td>
        <td>
          <?php
          if(empty($opp['nombre_estatus_interno'])){
            echo '<span style="color:red">No Disponible</span>';
          }else{
            echo $opp['nombre_estatus_interno'];
          } 
          ?>
        </td>
        <td><?php echo$opp['nombre_estatus_dspp']; ?></td>
        <?php 
        if($opp['estatus_publico'] == 1){
          echo "<td class='warning'>$opp[nombre_estatus_publico]</td>";
        }else if($opp['estatus_publico'] == 2){
          echo "<td class='success'>$opp[nombre_estatus_publico]</td>";
        }else{ 
          echo "<td class='danger'>$opp[nombre_estatus_publico]</td>";
        }
        ?>

        <td style="word-wrap: break-word;"><?php echo $productos['productos_opp']; ?></td>
        <td><?php echo $opp['numero']; ?></td>
      </tr>
    <?php
    $contador++;
    }
     ?>
  </tbody>
</table>