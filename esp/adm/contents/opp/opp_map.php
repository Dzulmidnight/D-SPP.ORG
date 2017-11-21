<?php 
require_once('../Connections/dspp.php');
mysql_select_db($database_dspp, $dspp);


  /// CONSULTAMOS LAS ORGANIZACONES QUE ESTAN EN PROCESO Y POR ESO NO DEBEN ESTAR EN LAS CERTIFICADAS
  $array_proceso = '';
  $array_proceso2 = '';

  $consultar_numero = mysql_query("SELECT opp.idopp, solicitud_certificacion.idsolicitud_certificacion, COUNT(idsolicitud_certificacion) AS 'total_solicitudes' FROM opp LEFT JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp WHERE (opp.estatus_opp != 'CANCELADO' AND opp.estatus_opp != 'SUSPENDIDO' AND opp.estatus_opp != 'CERTIFICADO' AND opp.estatus_opp != 'ARCHIVADO') AND opp.estatus_opp = 0 OR opp.estatus_opp IS NULL OR opp.estatus_opp = 1 OR opp.estatus_opp = 4 GROUP BY opp.idopp ORDER BY opp.idopp", $dspp) or die(mysql_error());
  $total_en_proceso = mysql_num_rows($consultar_numero);
  $num_registros = mysql_num_rows($consultar_numero);
  $contador = 1;
  while ($detalle_numero = mysql_fetch_assoc($consultar_numero)) {
    if($detalle_numero['total_solicitudes'] <= 1){
      if($contador < $num_registros){
        $array_proceso .= 'opp.idopp != '.$detalle_numero['idopp'].' AND ';
        $array_proceso2 .= 'opp.idopp = '.$detalle_numero['idopp'].' OR ';
      }else{
        $array_proceso .= 'opp.idopp != '.$detalle_numero['idopp'];
        $array_proceso2 .= 'opp.idopp = '.$detalle_numero['idopp'];
      }
    }
    $contador++;
  }



  //CONSULTAMOS LAS ORGANIZACIONES QUE ESTAN "ARCHIVADAS" Y POR ESO NO DEBEN ESTAR ENTRE LAS CERTIFICADAS
  $array_archivadas = '';
  $array_archivadas2 = '';
  $contador = 1;
  $opp_archivadas = mysql_query("SELECT opp.idopp, opp.abreviacion, solicitud_certificacion.idsolicitud_certificacion FROM opp LEFT JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp WHERE opp.estatus_opp = 'ARCHIVADO' GROUP BY opp.idopp ORDER BY opp.nombre", $dspp) or die(mysql_error());
  $total_archivadas = mysql_num_rows($opp_archivadas);

  while($archivadas = mysql_fetch_assoc($opp_archivadas)){
    if($contador < $total_archivadas){
      $array_archivadas .= 'opp.idopp != '.$archivadas['idopp'].' AND ';
      $array_archivadas2 .= 'opp.idopp = '.$archivadas['idopp'].' OR ';
    }else{
      $array_archivadas .= 'opp.idopp != '.$archivadas['idopp'];
      $array_archivadas2 .= 'opp.idopp = '.$archivadas['idopp'];
    }
    $contador++;
  }


  //CONSULTAMOS LAS ORGANIZACIONES CANCELADAS
  $array_canceladas = '';
  $array_canceladas2 = '';
  $contador = 1;
  $query_canceladas = mysql_query("SELECT opp.idopp FROM opp LEFT JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp WHERE opp.estatus_interno = 10 OR solicitud_certificacion.estatus_interno = 10 OR opp.estatus_opp = 'CANCELADO' ORDER BY opp.nombre", $dspp) or die(mysql_error());
  $total_canceladas = mysql_num_rows($query_canceladas);
  while($canceladas = mysql_fetch_assoc($query_canceladas)){
    if($contador < $total_canceladas){
      $array_canceladas .= 'opp.idopp != '.$canceladas['idopp'].' AND ';
      $array_canceladas2 .= 'opp.idopp = '.$canceladas['idopp'].' OR ';
    }else{
      $array_canceladas .= 'opp.idopp != '.$canceladas['idopp'];
      $array_canceladas2 .= 'opp.idopp = '.$canceladas['idopp'];
    }
    $contador++;
  }

  $array_certificadas = '';
  $query = "";
  $row_certificadas = mysql_query("SELECT opp.idopp, MAX(solicitud_certificacion.idsolicitud_certificacion) AS 'idsolicitud_certificacion' FROM opp LEFT JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp LEFT JOIN oc ON solicitud_certificacion.idoc = oc.idoc LEFT JOIN certificado ON solicitud_certificacion.idsolicitud_certificacion = certificado.idsolicitud_certificacion WHERE $array_proceso AND $array_archivadas AND $array_canceladas GROUP BY opp.idopp ORDER BY opp.idopp", $dspp) or die(mysql_error());
  $total_certificadas2 = mysql_num_rows($row_certificadas);
  $contador_opps = 0;

  $tipo_mapa = '';
  if(isset($_POST['tipo_mapa'])){
    $tipo_mapa = $_POST['tipo_mapa'];
  }else{  
    $tipo_mapa = 'CERTIFICADAS';
  }

?>


    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {
        'packages':['geochart'],
        // Note: you will need to get a mapsApiKey for your project.
        // See: https://developers.google.com/chart/interactive/docs/basic_load_libs#load-settings
        //'mapsApiKey': 'AIzaSyD-9tSrke72PouQMnMX-a7eZSW0jkFMBWY'
        'mapsApiKey': 'AIzaSyB0yHe2WVMjkilm056vaEN3CBUYFB3aa-w'
      });

      google.charts.setOnLoadCallback(drawRegionsMap);

      function drawRegionsMap() {
        var data = google.visualization.arrayToDataTable([
          ['Pais', 'Nº Organizaciones'],
         <?php 
         switch ($tipo_mapa) {
           case 'CERTIFICADAS':
              $row_paises = mysql_query("SELECT pais FROM opp GROUP BY pais", $dspp) or die(mysql_error());
              while($paises = mysql_fetch_assoc($row_paises)){
                $row_certificadas = mysql_query("SELECT opp.idopp, opp.pais, solicitud_certificacion.tipo_solicitud, solicitud_certificacion.estatus_interno AS 'solicitud_estatus_interno', certificado.idcertificado, certificado.vigencia_inicio, certificado.vigencia_fin, certificado.archivo AS 'certificado' FROM opp LEFT JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp LEFT JOIN certificado ON solicitud_certificacion.idsolicitud_certificacion = certificado.idsolicitud_certificacion WHERE $array_proceso AND $array_archivadas AND $array_canceladas AND opp.pais = '$paises[pais]' GROUP BY opp.idopp ORDER BY opp.idopp", $dspp) or die(mysql_error());

                $total_certificadas = mysql_num_rows($row_certificadas);
                if($total_certificadas > 0){
                  echo "['$paises[pais]', $total_certificadas],";
                }
              }
             break;
           case 'EN PROCESO':
              $row_paises = mysql_query("SELECT pais FROM opp GROUP BY pais", $dspp) or die(mysql_error());
              while($paises2 = mysql_fetch_assoc($row_paises)){
                $row_en_proceso = mysql_query("SELECT opp.idopp, opp.pais, solicitud_certificacion.tipo_solicitud, solicitud_certificacion.estatus_interno AS 'solicitud_estatus_interno', certificado.idcertificado, certificado.vigencia_inicio, certificado.vigencia_fin, certificado.archivo AS 'certificado' FROM opp LEFT JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp LEFT JOIN certificado ON solicitud_certificacion.idsolicitud_certificacion = certificado.idsolicitud_certificacion WHERE $array_proceso2 AND opp.pais = '$paises2[pais]' GROUP BY opp.idopp ORDER BY opp.idopp", $dspp) or die(mysql_error());

                $total_en_proceso = mysql_num_rows($row_en_proceso);
                if($total_en_proceso > 0){
                  echo "['$paises2[pais]', $total_en_proceso],";
                }
              }
             break;
           case 'CANCELADAS':
              $row_paises = mysql_query("SELECT pais FROM opp GROUP BY pais", $dspp) or die(mysql_error());
              while($paises = mysql_fetch_assoc($row_paises)){
                $row_canceladas = mysql_query("SELECT opp.idopp, opp.pais, solicitud_certificacion.tipo_solicitud, solicitud_certificacion.estatus_interno AS 'solicitud_estatus_interno', certificado.idcertificado, certificado.vigencia_inicio, certificado.vigencia_fin, certificado.archivo AS 'certificado' FROM opp LEFT JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp LEFT JOIN certificado ON solicitud_certificacion.idsolicitud_certificacion = certificado.idsolicitud_certificacion WHERE $array_canceladas2 AND opp.pais = '$paises[pais]' GROUP BY opp.idopp ORDER BY opp.idopp", $dspp) or die(mysql_error());

                $otro_canceladas = mysql_num_rows($row_canceladas);
                if($otro_canceladas > 0){
                  echo "['$paises[pais]', $otro_canceladas],";
                }
              }
             break;
           case 'ARCHIVADAS':
              $row_paises = mysql_query("SELECT pais FROM opp GROUP BY pais", $dspp) or die(mysql_error());
              while($paises = mysql_fetch_assoc($row_paises)){
                $row_archivadas = mysql_query("SELECT opp.idopp, opp.pais, solicitud_certificacion.tipo_solicitud, solicitud_certificacion.estatus_interno AS 'solicitud_estatus_interno', certificado.idcertificado, certificado.vigencia_inicio, certificado.vigencia_fin, certificado.archivo AS 'certificado' FROM opp LEFT JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp LEFT JOIN certificado ON solicitud_certificacion.idsolicitud_certificacion = certificado.idsolicitud_certificacion WHERE $array_archivadas2 AND opp.pais = '$paises[pais]' GROUP BY opp.idopp ORDER BY opp.idopp", $dspp) or die(mysql_error());
                
                $otro_archivadas = mysql_num_rows($row_archivadas);
                if($otro_archivadas > 0){
                  echo "['$paises[pais]', $otro_archivadas],";
                }
              }
             break;

           default:
             # code...
             break;
         }



    echo "]);";
    ?>  
          /*['Germany', 200],
          ['United States', 300],
          ['Brazil', 400],
          ['Canada', 500],
          ['France', 600],
          ['RU', 700]
        ]);*/

        var options = {
          colorAxis: {colors: ['#27ae60', '#e67e22', '#e74c3c']}
        };
        /*var options = {
          colorAxis: {values: [1, 10, 100, 1000], colors: ['green', '#D1E231', 'orange' ,'red'],},
      backgroundColor: '#81d4fa',
      datalessRegionColor: '#999',
      defaultColor: '#f5f5f5',
        };*/

        var chart = new google.visualization.GeoChart(document.getElementById('regions_div'));

        chart.draw(data, options);
      }
    </script>
    <div class="row">
      <div class="col-md-12">
        <h4>MAPA DE ORGANIZACIONES SPP CERTIFICADAS: 
          <?php
          switch ($tipo_mapa) {
            case 'CERTIFICADAS':
              echo '<span style="color:red">'.$total_certificadas2.'</span>'; 
              break;
            case 'EN PROCESO':
              echo '<span style="color:red">'.$total_en_proceso.'</span>'; 
              break;
            case 'CANCELADAS':
              echo '<span style="color:red">'.$total_canceladas.'</span>'; 
              break;
            case 'ARCHIVADAS':
              echo '<span style="color:red">'.$total_archivadas.'</span>'; 
              break;
            default:
              # code...
              break;
          }
          ?>
        </h4>
        <form action="" method="POST">
          <select name="tipo_mapa" id="tipo_mapa" onchange="this.form.submit()">
            <option <?php if($tipo_mapa == 'CERTIFICADAS'){echo 'selected'; } ?> value="CERTIFICADAS">CERTIFICADAS</option>
            <option <?php if($tipo_mapa == 'EN PROCESO'){echo 'selected'; } ?> value="EN PROCESO">EN PROCESO</option>
            <option <?php if($tipo_mapa == 'CANCELADAS'){echo 'selected'; } ?> value="CANCELADAS">CANCELADAS</option>
            <option <?php if($tipo_mapa == 'ARCHIVADAS'){echo 'selected'; } ?> value="ARCHIVADAS">ARCHIVADAS</option>
          </select>
        </form>
      </div>
      <div class="col-lg-12">
        <div id="regions_div" style="width: 1000px; height: 650px;"></div>
      </div>


    </div>