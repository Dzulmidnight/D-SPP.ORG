<div class="row">
  <div class="col-md-12">
    <h4>NUEVAS ORGANIZACIONES CERTIFICADAS</h4>
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
// ORGANIZACIONES CERTIFICADAS POR PRIMERA VEZ
/// DEBE DE TENER 1 SOLICITUD, PERO ESTA SOLICITUD DEBE DE SER NUEVA
/// SELECCIONA LAS ORGANIZACIONES("OPP") QUE TIENEN SOLICITUD NUEVA, QUE YA CUENTAN CON UN DICTAMEN POSITIVO Y YA SE LES HA ENTREGADO CERTIFICADO(o no) 
/// DEBEN DE TENER UN ESTATUS-DSPP 13 (certificada)
/// DEBEN DE TENER UN ESTATUS_PUBLICO 2 (certificado)
/// EL ESTATUS_INTERNO DE LA SOLICITUD DEBE DE SER DIFERENTE A 9 (dictamen negativo)
///PARA EL NUMERO DE SOCIOS TOMAMOS LA RESP1 QUE ES "NUMERO DE SOCIOS"
  $query = "SELECT solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp, COUNT(solicitud_certificacion.idopp) AS 'total' FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE opp.estatus_publico = 2 AND opp.estatus_dspp = 13 GROUP BY solicitud_certificacion.idopp";
  $consultar = mysql_query($query,$dspp) or die(mysql_error());
  $total_registro = mysql_num_rows($consultar);
  $arreglo_idopp = '';
  $contador = 1;
  while($numero_solicitudes = mysql_fetch_assoc($consultar)){
    if($numero_solicitudes['total'] == 1){
      if($contador < $total_registro){
        $arreglo_idopp .= 'solicitud_certificacion.idopp = '.$numero_solicitudes['idopp'].' OR ';
      }else{
        $arreglo_idopp .= 'solicitud_certificacion.idopp = '.$numero_solicitudes['idopp'];
      }
    }
    $contador++;
  }

  /*echo $contador;
  echo '<p>'.$arreglo_idopp.'</p>';*/

  $query = "SELECT solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE ($arreglo_idopp) AND solicitud_certificacion.tipo_solicitud = 'NUEVA' AND solicitud_certificacion.estatus_interno != 9"; 
  $consultar2 = mysql_query($query,$dspp) or die(mysql_error());
  $total_registro = mysql_num_rows($consultar2);
  $arreglo2_idopp = '';
  $contador = 1;
  while($detalle = mysql_fetch_assoc($consultar2)){
 
      if($contador < $total_registro){
        $arreglo2_idopp .= 'solicitud_certificacion.idopp = '.$detalle['idopp'].' OR ';
      }else{
        $arreglo2_idopp .= 'solicitud_certificacion.idopp = '.$detalle['idopp'];
      }
      $contador++;
      //$query = "SELECT solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp, COUNT(solicitud_certificacion.idopp) AS 'total' FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE solicitud_certificacion.tipo_solicitud = 'NUEVA' AND opp.estatus_publico = 2 AND opp.estatus_dspp = 13 GROUP BY solicitud_certificacion.idopp";
    
  }

  /*echo $contador;
  echo '<p>'.$arreglo2_idopp.'</p>';*/

  $query = "SELECT opp.idopp, opp.spp, opp.nombre AS 'nombre_opp', opp.abreviacion AS 'abreviacion_opp', opp.estatus_opp, opp.pais, oc.abreviacion AS 'abreviacion_oc', solicitud_certificacion.idsolicitud_certificacion,  solicitud_certificacion.tipo_solicitud, solicitud_certificacion.estatus_dspp, solicitud_certificacion.estatus_interno, solicitud_certificacion.resp1 AS 'num_socios', estatus_dspp.nombre AS 'nombre_estatus_dspp', estatus_interno.nombre AS 'nombre_estatus_interno', certificado.idcertificado FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp INNER JOIN oc ON solicitud_certificacion.idoc = oc.idoc LEFT JOIN estatus_dspp ON solicitud_certificacion.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN estatus_interno ON solicitud_certificacion.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN certificado ON solicitud_certificacion.idsolicitud_certificacion = certificado.idsolicitud_certificacion WHERE (opp.estatus_publico = 2 AND opp.estatus_dspp = 13 AND solicitud_certificacion.estatus_interno != 9 AND solicitud_certificacion.tipo_solicitud = 'NUEVA') AND $arreglo2_idopp ORDER BY opp.nombre";
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
        echo '<p>'.$informacion['idcertificado'].'</p>';
        if(isset($informacion['idcertificado'])){
          $query_certificado = "SELECT * FROM certificado WHERE idcertificado = $informacion[idcertificado]";
          $consultar_certificado = mysql_query($query_certificado,$dspp) or die(mysql_error());
          $detalle_certificado = mysql_fetch_assoc($consultar_certificado);
          $tiempo = strtotime($detalle_certificado['vigencia_fin']);
          echo '<a href="'.$detalle_certificado['archivo'].'" target="_new">'.date('d/m/Y', $tiempo).' <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span></a>';
          //echo "EXISTE CERTIFICADO DESDE SOLICITUD";
        }else{
          $query_certificado = "SELECT * FROM certificado WHERE idopp = $informacion[idopp]";
          $consultar_certificado = mysql_query($query_certificado,$dspp) or die(mysql_error());
          $detalle_certificado = mysql_fetch_assoc($consultar_certificado);
          $tiempo = strtotime($detalle_certificado['vigencia_fin']);
          echo '<a style="color:#e74c3c" href="#" data-toggle="tooltip" title="ESTA ORGANIZACIÓN SE ENCUENTRA CERTIFICADA PERO AUN NO SE HA CARGADO EL CERTIFICADO EN LA SECCIÓN DE SOLICITUDES, fecha modificada manualmente" target="_new">'.date('d/m/Y', $tiempo).' <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span></a>';
        }
        /*if($informacion['estatus_opp'] == 13){ /// SI LA FECHA DEL CERTIFICADO FUE CARGADA DESDE LA SECCIÓN DE "INFORMACIÓN OPP" REALIZAMOS LA CONSULTA A LA TABLA CERTIFICADOS
          $query = "SELECT idcertificado, vigencia_inicio, vigencia_fin FROM certificado WHERE idopp = $informacion[idopp]";
          $consultar_certificado = mysql_query($query,$dspp) or die(mysql_error());
          $detalle_certificado = mysql_fetch_assoc($consultar_certificado);
          $fecha_certificado = strtotime($detalle_certificado['vigencia_fin']);
          $vigencia_fin = date('d/m/Y', $fecha_certificado);
          echo '<p>'.$vigencia_fin.'</p>';
        }else{
          echo 'EN PROCESO';
        } */
      ?>
    </td>
    <!-- ESTATUS DE LA OPP -->
    <td>
      <?php 
      if($informacion['estatus_opp'] == 'CERTIFICADO' || $informacion['estatus_opp'] == 13){ // la organización se ha certificado, desde la tabla de información OPP, pero dentro de la tabla de solicitudes no se ha terminado el proceso
        //estatus_dspp 13 = CERTIFICADA
      echo '<span style="color:#2ecc71">CERTIFICADA</span>';
        //echo '<a href="#" style="color:#e67e22" data-toggle="tooltip" title="ESTA ORGANIZACIÓN SE ENCUENTRA CERTIFICADA PERO AUN NO SE HA CARGADO EL CERTIFICADO EN LA SECCIÓN DE SOLICITUDES"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span> CERTIFICADA</a>';
      }else{
        echo 'EN PROCESO';
      }
      
      ?>
    </td>
    <!-- PRODUCTOS -->
    <td>
    <?php 
      $query = "SELECT GROUP_CONCAT(producto SEPARATOR ' , ') AS lista_producto_especifico FROM productos WHERE idsolicitud_certificacion = $informacion[idsolicitud_certificacion]";
      $consultar_productos = mysql_query($query,$dspp) or die(mysql_error());
      $detalle_productos = mysql_fetch_assoc($consultar_productos);
      echo '<p>'.utf8_encode($detalle_productos['lista_producto_especifico']).'</p>';
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