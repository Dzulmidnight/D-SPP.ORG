<div class="row">
  <div class="col-md-12">
    <h4>EN PROCESO</h4>
    <p>SE MOSTRARAN ORGANIZACIONES QUE "HAN CREADO SU USUARIO PERO QUE NO HAN RELLENADO SOLICITUD", ORGANIZACIONES QUE "HAN CARGADO UNA SOLICITUD PERO QUE AUN NO RECIBEN COTIZACIÓN", ORGANIZACIONES QUE "YA SE HA ACEPTADO LA COTIZACION Y QUE HAN INICIADO EL PROCESO DE LA CERTIFICACIÓN"</p>
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
// EN PROCESO LA PRIMERA VEZ
/// SELECCIONA LAS ORGANIZACIONES("OPP") QUE TIENEN SOLICITUD NUEVA, PERO QUE AUN NO SE LES HA ASIGNADO UN DICMTANE POSITIVO
/// DEBEN DE TENER UN ESTATUS-DSPP DEL 1 al 11, o el 17
///PARA EL NUMERO DE SOCIOS TOMAMOS LA RESP1 QUE ES "NUMERO DE SOCIOS"

  /*$query2 = "SELECT idopp FROM solicitud_certificacion WHERE idopp IS NOT NULL GROUP BY idopp ORDER BY idopp";
  $consultar2 = mysql_query($query2,$dspp) or die(mysql_error());
  $ids = '';
  $total = mysql_num_rows($consultar2);
  $contador = 1;
  while($registros = mysql_fetch_assoc($consultar2)){
    if(empty($registros['idsolicitud_certificacion'])){
      if($total > $contador){
        $ids .= 'opp.idopp != '.$registros['idopp'].' AND ';
      }else{
        $ids .= 'opp.idopp != '.$registros['idopp'];
      }
    }
    $contador++;
  }
  echo '<p>'.$total.'</p>';
  echo '<p>'.$ids.'</p>';*/

  $query = "SELECT opp.idopp, opp.spp, opp.nombre AS 'nombre_opp', opp.abreviacion AS 'abreviacion_opp', opp.pais, oc.abreviacion AS 'abreviacion_oc', opp.estatus_opp AS 'opp_estatus_opp', opp.estatus_publico AS 'opp_estatus_publico', opp.estatus_interno AS 'opp_estatus_interno', opp.estatus_dspp AS 'opp_estatus_dspp', MAX(solicitud_certificacion.idsolicitud_certificacion) AS 'idsolicitud_certificacion', solicitud_certificacion.tipo_solicitud, solicitud_certificacion.estatus_interno AS 'solicitud_estatus_interno', solicitud_certificacion.estatus_dspp AS 'solicitud_estatus_dspp' FROM opp LEFT JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp LEFT JOIN oc ON solicitud_certificacion.idoc = oc.idoc GROUP BY opp.idopp";
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
      <th>ORGANIZACIÓN</th>
      <th>PAÍS</th>
      <th>OC</th>
      <th>PROCESO CERTIFICACIÓN</th>
      <th>ESTATUS OPP</th>
      <th>ESTATUS PUBLICO</th>
      <th>ESTATUS INTERNO</th>
      <th>ESTATUS DSPP</th>
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
      <select name="">
        <option>...</option>
        <?php 
        $row_interno = mysql_query("SELECT * FROM estatus_interno", $dspp) or die(mysql_error());
        while($estatus_interno = mysql_fetch_assoc($row_interno)){
        ?>
          <option value=""><?php echo $estatus_interno['nombre']; ?></option>
        <?php
        }
         ?>
      </select>

    </td>
    <!-- ESTATUS OPP -->
    <td>
      <?php 
      $consultar10 = mysql_query("SELECT nombre FROM estatus_dspp WHERE idestatus_dspp = '$informacion[opp_estatus_opp]'", $dspp) or die(mysql_error());
      $detalle10 = mysql_fetch_assoc($consultar10); 
      echo $informacion['opp_estatus_opp'].' - <span style="color:green">'.$detalle10['nombre'].'</span>'; 
      ?>
    </td>
    <!-- ESTATUS PUBLICO -->
    <td>
      <?php 
        echo '<p>OPP: '.$informacion['opp_estatus_publico'].'</p>';
       ?>
    </td>
    <!-- ESTATUS INTERNO -->
    <td>
      <?php
      $consultar3 = mysql_query("SELECT nombre FROM estatus_interno WHERE idestatus_interno = '$informacion[solicitud_estatus_interno]'", $dspp) or die(mysql_error());
      $detalle3 = mysql_fetch_assoc($consultar3);
        echo '<p>SOLICITUD: '.$informacion['solicitud_estatus_interno'].' - <span style="color:blue">'.$detalle3['nombre'].'</span></p>';
      $consultar4 = mysql_query("SELECT nombre FROM estatus_interno WHERE idestatus_interno = '$informacion[opp_estatus_interno]'", $dspp) or die(mysql_error());
      $detalle4 = mysql_fetch_assoc($consultar4);
        echo '<p>OPP: '.$informacion['opp_estatus_interno'].' - <span style="color:red">'.$detalle4['nombre'].'</span></p>';
       ?>
    </td>
    <!-- ESTATUS DSPP -->
    <td>
      <?php
      $consultar5 = mysql_query("SELECT nombre FROM estatus_dspp WHERE idestatus_dspp = '$informacion[solicitud_estatus_dspp]'", $dspp) or die(mysql_error());
      $detalle5 = mysql_fetch_assoc($consultar5);
        echo '<p>SOLICITUD: '.$informacion['solicitud_estatus_dspp'].' - <span style="color:blue">'.$detalle5['nombre'].'</span></p>';
      $consultar6 = mysql_query("SELECT nombre FROM estatus_dspp WHERE idestatus_dspp = '$informacion[opp_estatus_dspp]'", $dspp) or die(mysql_error());
      $detalle7 = mysql_fetch_assoc($consultar6);
        echo '<p>OPP: '.$informacion['opp_estatus_dspp'].' - <span style="color:red">'.$detalle7['nombre'].'</span></p>';
       ?>
    </td>
    <!-- ULTIMA FECHA DE CERTIFICADO -->
    <td>
      <?php 
       /* echo $informacion['idcertificado'];
        if($informacion['estatus_opp'] == 13){ /// SI LA FECHA DEL CERTIFICADO FUE CARGADA DESDE LA SECCIÓN DE "INFORMACIÓN OPP" REALIZAMOS LA CONSULTA A LA TABLA CERTIFICADOS
          $query = "SELECT idcertificado, vigencia_inicio, vigencia_fin FROM certificado WHERE idopp = $informacion[idopp]";
          $consultar_certificado = mysql_query($query,$dspp) or die(mysql_error());
          $detalle_certificado = mysql_fetch_assoc($consultar_certificado);
          $fecha_certificado = strtotime($detalle_certificado['vigencia_fin']);
          $vigencia_fin = date('d/m/Y', $fecha_certificado);
          echo '<p>'.$vigencia_fin.'</p>';
        }else{
          echo 'EN PROCESO';
        }
        */ 
      ?>
    </td>
    <!-- ESTATUS DE LA OPP -->
    <td>
      <?php 
      /*
      if($informacion['estatus_opp'] == 13){ // la organización se ha certificado, desde la tabla de información OPP, pero dentro de la tabla de solicitudes no se ha terminado el proceso
        //estatus_dspp 13 = CERTIFICADA
        echo '<a href="#" style="color:#e67e22" data-toggle="tooltip" title="ESTA ORGANIZACIÓN SE ENCUENTRA CERTIFICADA PERO AUN NO SE HA CARGADO EL CERTIFICADO EN LA SECCIÓN DE SOLICITUDES"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span> CERTIFICADA</a>';
      }else{
        echo 'EN PROCESO';
      }
      */
      ?>
    </td>
    <!-- PRODUCTOS -->
    <td>
 
    </td>
    <!-- NUMERO DE SOCIOS -->
    <td>

    </td>
    <td></td>

  <?php
    echo '</tr>';
    $contador++;
  }
   ?>
  </tbody>
</table>