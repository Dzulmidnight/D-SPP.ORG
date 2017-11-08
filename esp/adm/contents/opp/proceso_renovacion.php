<div class="row">
  <div class="col-md-12">
    <h4>PROCESO DE CERTIFICACION - RENOVACIÓN DEL CERTIFICADO</h4>
  </div>  
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


          <div class="col-xs-4">
            Organismo de Certificación
            <select name="buscar_oc" class="form-control">
              <option value=''>Selecciona un organismo de certificación</option>
              <?php 
              while($oc = mysql_fetch_assoc($row_oc)){
                echo "<option value='$oc[idoc]'>$oc[abreviacion]</option>";
              }
               ?>
            </select>
          </div>
          <div class="col-xs-4">
            País
            <select name="buscar_pais" class="form-control">
              <option value=''>Selecciona un país</option>
              <?php 
              while($pais = mysql_fetch_assoc($row_pais)){
                echo "<option value='".$pais['pais']."'>".mayuscula($pais['pais'])."</option>";
              }
               ?>
            </select>
          </div>
          <div class="col-xs-4">
            Producto
            <select class="form-control" name="buscar_producto" id="">
              <option value=''>Seleccione un producto</option>
              <?php 
              while($lista_productos = mysql_fetch_assoc($query_productos)){
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


<?php 
// EN PROCESO "RENOVACIÓN DEL CERTIFICADO"
/// SELECCIONA LAS ORGANIZACIONES("OPP") QUE TIENEN SOLICITUD "EN RENOVACIÓN", PERO QUE AUN NO SE LES HA ASIGNADO UN DICTAMEN POSITIVO
/// DEBEN DE TENER UN ESTATUS-DSPP DEL 1 al 9, o el 17
///PARA EL NUMERO DE SOCIOS TOMAMOS LA RESP1 QUE ES "NUMERO DE SOCIOS"
  /*$query = "SELECT opp.idopp, opp.spp, opp.nombre AS 'nombre_opp', opp.abreviacion AS 'abreviacion_opp', opp.pais, opp.estatus_opp, oc.abreviacion AS 'abreviacion_oc', solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.tipo_solicitud, solicitud_certificacion.estatus_dspp, solicitud_certificacion.estatus_interno, solicitud_certificacion.resp1 AS 'num_socios', estatus_dspp.nombre AS 'nombre_estatus_dspp', estatus_interno.nombre AS 'nombre_estatus_interno', certificado.idcertificado, certificado.vigencia_fin FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp INNER JOIN oc ON solicitud_certificacion.idoc = oc.idoc LEFT JOIN estatus_dspp ON solicitud_certificacion.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN estatus_interno ON solicitud_certificacion.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN certificado ON opp.idopp = certificado.idopp WHERE (solicitud_certificacion.estatus_dspp BETWEEN 1 AND 11 OR solicitud_certificacion.estatus_dspp = 17) AND solicitud_certificacion.tipo_solicitud = 'RENOVACION' ORDER BY opp.nombre";
  $consultar = mysql_query($query,$dspp) or die(mysql_error());
  $total_organizaciones = mysql_num_rows($consultar);*/

  $query = "SELECT opp.idopp, opp.spp, opp.nombre AS 'nombre_opp', opp.abreviacion AS 'abreviacion_opp', opp.pais, opp.estatus_opp, oc.abreviacion AS 'abreviacion_oc', MAX(solicitud_certificacion.idsolicitud_certificacion) AS 'idsolicitud_certificacion', solicitud_certificacion.tipo_solicitud, solicitud_certificacion.estatus_dspp, solicitud_certificacion.estatus_interno, solicitud_certificacion.resp1 AS 'num_socios', estatus_dspp.nombre AS 'nombre_estatus_dspp', estatus_interno.nombre AS 'nombre_estatus_interno', certificado.idcertificado, certificado.vigencia_fin FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp INNER JOIN oc ON solicitud_certificacion.idoc = oc.idoc LEFT JOIN estatus_dspp ON solicitud_certificacion.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN estatus_interno ON solicitud_certificacion.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN certificado ON opp.idopp = certificado.idopp WHERE (solicitud_certificacion.estatus_dspp BETWEEN 1 AND 11 OR solicitud_certificacion.estatus_interno IS NULL OR solicitud_certificacion.estatus_dspp = 17) AND solicitud_certificacion.tipo_solicitud = 'RENOVACION' GROUP BY solicitud_certificacion.idopp ORDER BY opp.nombre";
  $consultar = mysql_query($query,$dspp) or die(mysql_error());
  $total_organizaciones = mysql_num_rows($consultar);

 ?> 
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
          <input type="hidden" name="query_pdf" value="<?php echo $query_opp; ?>">
        </form> 
        <form name="formulario2" method="POST" action="../../reportes/lista_opp.php">
          <input type="hidden" name="lista_excel" value="2">
          <input type="hidden" name="query_excel" value="<?php echo $query_opp; ?>">
        </form>
      </th>
      <th class="success text-center" colspan="6">
        NUMERO DE ORGANIZACIONES: <?php echo $total_organizaciones; ?>
      </th>
    </tr>
    <tr>
      <th style="width:20px;">#</th>
      <th>#SPP</th>
      <th>SOLICITUD</th>
      <th>ORGANIZACIÓN</th>
      <th>PAÍS</th>
      <th>OC</th>
      <th>PROCESO CERTIFICACIÓN</th>
      <th>ULTIMA FECHA DE CERTIFICADO</th>
      <th>ESTATUS DE LA OPP</th>
      <th>PRODUCTOS</th>
      <th>Nº SOCIOS</th>
      <th>
        <form  style="margin: 0;padding: 0;" action="" method="POST" >            
            <button class="btn btn-xs btn-danger disabled" type="subtmit" value="2"  name="eliminar" data-toggle="tooltip" data-placement="top" title="Eliminar" onclick="return confirm('¿Está seguro ?, los datos se eliminaran permanentemente');" >
              <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
            </button>        
            <button class="btn btn-xs btn-info disabled" type="subtmit" value="1" name="archivar" data-toggle="tooltip" data-placement="top" title="Archivar">
              <span class="glyphicon glyphicon-folder-open" aria-hidden="true"></span>
            </button>
        </form>
      </th>
    </tr>
  </thead>
  <tbody>
  <?php 
  $contador = 1;
  while($informacion = mysql_fetch_assoc($consultar)){
    echo '<tr>';
      echo '<td style="width:20px;">'.$contador.'</td>';
  ?>
    <td>
      <a class="btn btn-primary btn-xs" style="width:100%;font-size:10px;" href="?OPP&amp;detail&amp;idopp=<?php echo $informacion['idopp']; ?>">Consultar<br></a>
      <input type="text" name="spp<?php echo $informacion['idopp'];?>" value="<?php echo $informacion['spp']; ?>">
    </td>
    <!-- TIPO DE SOLICITUD -->
    <td>
      <?php 
        echo '<p class="alert alert-info" style="padding:7px;margin-bottom:0px;">ID: '.$informacion['idsolicitud_certificacion'].'| '.$informacion['tipo_solicitud'].'</p>';
        echo '<p >'.$informacion['nombre_estatus_dspp'].'</p>';
       ?>
    </td>
    <!-- NOMBRE DE LA OPP -->
    <td>
      <?php echo '<span style="color:#bdc3c7">ID: '.$informacion['idopp'].'| </span>'.mayuscula($informacion['nombre_opp']).' (<span style="color:red">'.mayuscula($informacion['abreviacion_opp']).'</span>)'; ?>
    </td>
    <!-- PAIS DE LA OPP -->
    <td>
      <?php echo mayuscula($informacion['pais']); ?>
    </td>
    <!-- OC -->
    <td>
      <?php echo $informacion['abreviacion_oc']; ?>
    </td>

    <!-- PROCESO DE CERTIFICACION -->
    <td>
      <select name="estatus_interno<?php echo $opp['idopp']; ?>">
        <option>...</option>
        <?php 
        $row_interno = mysql_query("SELECT * FROM estatus_interno", $dspp) or die(mysql_error());
        while($estatus_interno = mysql_fetch_assoc($row_interno)){
        ?>
          <option value="<?php echo $estatus_interno['idestatus_interno'] ?>" <?php if($estatus_interno['idestatus_interno'] == $opp['estatus_interno']){echo "selected";} ?>><?php echo $estatus_interno['nombre']; ?></option>
        <?php
        }
         ?>
      </select>
      <?php echo "<p class='alert alert-info' style='padding:7px;'>$informacion[nombre_estatus_interno]</p>"; ?>  
    </td>
    <!-- ULTIMA FECHA DE CERTIFICADO -->
    <td>
      <?php 

        $fecha_certificado = strtotime($informacion['vigencia_fin']);
        $vigencia_fin = date('d/m/Y', $fecha_certificado);
      ?>
      <input type="date" name="vigencia_fin<?php echo $informacion['idopp']; ?>" value="<?php echo $informacion['vigencia_fin']; ?>">
      <?php 
      if(isset($informacion['idcertificado'])){
        $estatus_certificado = mysql_query("SELECT idcertificado, estatus_certificado, estatus_dspp.nombre FROM certificado LEFT JOIN estatus_dspp ON certificado.estatus_certificado = estatus_dspp.idestatus_dspp WHERE idcertificado = $informacion[idcertificado]", $dspp) or die(mysql_error());
        $certificado = mysql_fetch_assoc($estatus_certificado);

        switch ($certificado['estatus_certificado']) {
          case '13': //certificado "activo"
            $clase = 'text-center alert alert-success';
            break;
          case '14': //certificado "renovacion"
            $clase = 'text-center alert alert-info';
            break;
          case '15': //certificado "por expirar"
            $clase = 'text-center alert alert-warning';
            break;
          case '16': //certificado "Expirado"
            $clase = 'text-center alert alert-danger';
            break;

          default:
            # code...
            break;
        }
         echo "<p style='padding:5px;' class='".$clase."'><span style='color:#bdc3c7'>ID: ".$informacion['idcertificado']."| </span>".$certificado['nombre']."</p>";
      }else{
        echo "<p style='padding:5px;'>No Disponible</p>";
      }
        //echo $opp['estatus_certificado'];
      ?>

    </td>
    <!-- ESTATUS DE LA OPP -->
    <td>
      <?php 
      if($informacion['estatus_opp'] == 13){ // la organización se ha certificado, desde la tabla de información OPP, pero dentro de la tabla de solicitudes no se ha terminado el proceso
        //estatus_dspp 13 = CERTIFICADA
        echo '<a href="#" style="color:#e67e22" data-toggle="tooltip" title="ESTA ORGANIZACIÓN SE ENCUENTRA CERTIFICADA PERO AUN NO SE HA CARGADO EL CERTIFICADO EN LA SECCIÓN DE SOLICITUDES"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span> CERTIFICADA</a>';
      }else{
        echo 'EN PROCESO DE RENOVACIÓN';
      }
      
      ?>
    </td>
    <!-- PRODUCTOS -->
    <td>
    <?php 
      $query = "SELECT GROUP_CONCAT(producto SEPARATOR ' , ') AS lista_producto_especifico FROM productos WHERE idopp = $informacion[idopp]";
      $consultar_productos = mysql_query($query,$dspp) or die(mysql_error());
      $detalle_productos = mysql_fetch_assoc($consultar_productos);
      echo '<p>'.utf8_encode(mayuscula($detalle_productos['lista_producto_especifico'])).'</p>';
     ?>
    </td>
    <!-- NUMERO DE SOCIOS -->
    <td>
      <?php echo $informacion['num_socios']; ?>
    </td>
    <td></td>

  <?php
    echo '</tr>';
    $contador++;
  }
   ?>
  </tbody>
</table>