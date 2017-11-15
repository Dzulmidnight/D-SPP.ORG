<?php 
require_once('../Connections/dspp.php');
mysql_select_db($database_dspp, $dspp);

function sanear_string($string)
{
 
    $string = trim($string);
 
    $string = str_replace(
        array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
        array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
        $string
    );
 
    $string = str_replace(
        array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
        array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
        $string
    );
 
    $string = str_replace(
        array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
        array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
        $string
    );
 
    $string = str_replace(
        array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
        array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
        $string
    );
 
    $string = str_replace(
        array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
        array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
        $string
    );
 
    $string = str_replace(
        array('ñ', 'Ñ', 'ç', 'Ç'),
        array('n', 'N', 'c', 'C',),
        $string
    );

 
 
    return $string;
}
  /// CONSULTAMOS LAS ORGANIZACONES QUE ESTAN EN PROCESO Y POR ESO NO DEBEN ESTAR EN LAS CERTIFICADAS
  $array_opp = '';
  $array_opp2 = '';
  $consultar_numero = mysql_query("SELECT opp.idopp, opp.abreviacion, solicitud_certificacion.idsolicitud_certificacion, COUNT(idsolicitud_certificacion) AS 'total_solicitudes' FROM opp LEFT JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp WHERE (opp.estatus_opp != 'CANCELADO' AND opp.estatus_opp != 'SUSPENDIDO' AND opp.estatus_opp != 'CERTIFICADO' AND opp.estatus_opp != 'ARCHIVADO') AND opp.estatus_opp = 0 OR opp.estatus_opp IS NULL OR opp.estatus_opp = 1 OR opp.estatus_opp = 4 GROUP BY opp.idopp ORDER BY opp.idopp", $dspp) or die(mysql_error());
  $num_registros = mysql_num_rows($consultar_numero);
  $contador = 1;
  while ($detalle_numero = mysql_fetch_assoc($consultar_numero)) {
    if($detalle_numero['total_solicitudes'] <= 1){
      if($contador < $num_registros){
        $array_opp .= 'opp.idopp(<span style="color:red">'.$detalle_numero['abreviacion'].'</span>) = '.$detalle_numero['idopp'].' OR ';
        $array_opp2 .= 'opp.idopp != '.$detalle_numero['idopp'].' AND ';
      }else{
        $array_opp .= 'opp.idopp(<span style="color:red">'.$detalle_numero['abreviacion'].'</span>) = '.$detalle_numero['idopp'];
        $array_opp2 .= 'opp.idopp != '.$detalle_numero['idopp'];
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
      $array_archivadas2 .= 'opp.idopp(<span style="color:red">'.$archivadas['abreviacion'].'</span>) = '.$archivadas['idopp'].' OR ';
      $array_archivadas .= 'opp.idopp != '.$archivadas['idopp'].' AND ';
    }else{
      $array_archivadas2 .= 'opp.idopp(<span style="color:red">'.$archivadas['abreviacion'].'</span>) = '.$archivadas['idopp'];
      $array_archivadas .= 'opp.idopp != '.$archivadas['idopp'];
    }
    $contador++;
  }

  //CONSULTAMOS LAS ORGANIZACIONES CANCELADAS
  $array_canceladas = '';
  $contador = 1;
  $query_canceladas = mysql_query("SELECT opp.idopp FROM opp LEFT JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp WHERE opp.estatus_interno = 10 OR solicitud_certificacion.estatus_interno = 10 OR opp.estatus_opp = 'CANCELADO' ORDER BY opp.nombre", $dspp) or die(mysql_error());
  $total_canceladas = mysql_num_rows($query_canceladas);
  while($canceladas = mysql_fetch_assoc($query_canceladas)){
    if($contador < $total_canceladas){
      $array_canceladas .= 'opp.idopp != '.$canceladas['idopp'].' AND ';
    }else{
      $array_canceladas .= 'opp.idopp != '.$canceladas['idopp'];
    }
    $contador++;
  }
  $query_tota_opps = mysql_query("SELECT idopp FROM opp GROUP BY idopp",$dspp) or die(mysql_error());
  $total_opps = mysql_num_rows($query_tota_opps);



$array_certificadas = '';
$query = "";
$row_certificadas = mysql_query("SELECT opp.idopp,  opp.spp, opp.email, opp.telefono, opp.password, opp.sitio_web, opp.nombre AS 'nombre_opp', opp.abreviacion AS 'abreviacion_opp', opp.pais, oc.abreviacion AS 'abreviacion_oc', opp.estatus_opp AS 'opp_estatus_opp', opp.estatus_publico AS 'opp_estatus_publico', opp.estatus_interno AS 'opp_estatus_interno', opp.estatus_dspp AS 'opp_estatus_dspp', MAX(solicitud_certificacion.idsolicitud_certificacion) AS 'idsolicitud_certificacion', solicitud_certificacion.tipo_solicitud, solicitud_certificacion.estatus_interno AS 'solicitud_estatus_interno', solicitud_certificacion.estatus_dspp AS 'solicitud_estatus_dspp', certificado.idcertificado, certificado.vigencia_inicio, certificado.vigencia_fin, certificado.archivo AS 'certificado' FROM opp LEFT JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp LEFT JOIN oc ON solicitud_certificacion.idoc = oc.idoc LEFT JOIN certificado ON solicitud_certificacion.idsolicitud_certificacion = certificado.idsolicitud_certificacion WHERE $array_opp2 AND $array_archivadas AND $array_canceladas GROUP BY opp.idopp ORDER BY opp.idopp", $dspp) or die(mysql_error());
$total_certificadas = mysql_num_rows($row_certificadas);
$contador_opps = 0;
/*while($certificadas = mysql_fetch_assoc($row_certificadas)){
    if($contador < $total_certificadas){
      $array_certificadas .= 'opp.idopp = '.$certificadas ['idopp'].' OR ';
    }else{
      $array_certificadas .= 'opp.idopp = '.$certificadas ['idopp'];
    }
    $contador++;
}*/
 $row_paises = mysql_query("SELECT pais FROM opp GROUP BY pais", $dspp) or die(mysql_error());
 $contador = 0;
 $mapa = '';
 //$consultar = mysql_query($query,$dspp) or die(mysql_error());
  while($paises = mysql_fetch_assoc($row_paises)){
    $row_certificadas = mysql_query("SELECT opp.idopp, opp.pais, solicitud_certificacion.tipo_solicitud, solicitud_certificacion.estatus_interno AS 'solicitud_estatus_interno', certificado.idcertificado, certificado.vigencia_inicio, certificado.vigencia_fin, certificado.archivo AS 'certificado' FROM opp LEFT JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp LEFT JOIN certificado ON solicitud_certificacion.idsolicitud_certificacion = certificado.idsolicitud_certificacion WHERE $array_opp2 AND $array_archivadas AND $array_canceladas AND opp.pais = '$paises[pais]' GROUP BY opp.idopp ORDER BY opp.idopp", $dspp) or die(mysql_error());
    $opps_certificadas = mysql_fetch_assoc($row_certificadas);
    $total_certificadas = mysql_num_rows($row_certificadas);
    
    if($total_certificadas > 0){
      $mapa = "['$paises[pais]', $total_certificadas],";
      echo "['$paises[pais]', $total_certificadas],";
    }
    //echo 'PAIS: '.$paises['pais'].' - Num: '.$otro_total.'<br>';
  }
$mapa = "['Germany', 200],['United States', 300],['Brazil', 400],['Canada', 500],['France', 600],['RU', 700]";

?>


    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {
        'packages':['geochart'],
        // Note: you will need to get a mapsApiKey for your project.
        // See: https://developers.google.com/chart/interactive/docs/basic_load_libs#load-settings
        'mapsApiKey': 'AIzaSyB0yHe2WVMjkilm056vaEN3CBUYFB3aa-w'
      });
      google.charts.setOnLoadCallback(drawRegionsMap);

      function drawRegionsMap() {
        var data = google.visualization.arrayToDataTable([
          <?php 
          $row_paises = mysql_query("SELECT pais FROM opp GROUP BY opp.pais", $dspp) or die(mysql_error());  
          while($row = mysql_fetch_assoc($row_paises)) { 
          ?>
            ['<?php echo $row['pais']; ?>', <?php echo $row['numero']; ?>],
          <?php } ?>
        ]);

        var options = {};

        var chart = new google.visualization.GeoChart(document.getElementById('regions_div'));

        chart.draw(data, options);
      }
    </script>

    <div id="regions_div" style="width: 900px; height: 500px;"></div>


   <!-- <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {
        'packages':['geochart'],
        // Note: you will need to get a mapsApiKey for your project.
        // See: https://developers.google.com/chart/interactive/docs/basic_load_libs#load-settings
        'mapsApiKey': 'AIzaSyD-9tSrke72PouQMnMX-a7eZSW0jkFMBWY'
      });
      google.charts.setOnLoadCallback(drawRegionsMap);

      function drawRegionsMap() {
        var data = google.visualization.arrayToDataTable([
          ['Country', 'Organizaciones'],
         <?php 
         //$query = "SELECT pais FROM opp GROUP BY pais";
         $row_paises = mysql_query("SELECT pais FROM opp GROUP BY pais", $dspp) or die(mysql_error());
         //$consultar = mysql_query($query,$dspp) or die(mysql_error());
          while($paises = mysql_fetch_assoc($row_paises)){
            $row_certificadas = mysql_query("SELECT opp.idopp, opp.pais, solicitud_certificacion.tipo_solicitud, solicitud_certificacion.estatus_interno AS 'solicitud_estatus_interno', certificado.idcertificado, certificado.vigencia_inicio, certificado.vigencia_fin, certificado.archivo AS 'certificado' FROM opp LEFT JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp LEFT JOIN certificado ON solicitud_certificacion.idsolicitud_certificacion = certificado.idsolicitud_certificacion WHERE $array_opp2 AND $array_archivadas AND $array_canceladas AND opp.pais = '$paises[pais]' GROUP BY opp.idopp ORDER BY opp.idopp", $dspp) or die(mysql_error());
            $total_certificadas = mysql_fetch_assoc($row_certificadas);
            $otro_total = mysql_num_rows($row_certificadas);
            echo "['$paises[pais]', $otro_total],";
            //echo 'PAIS: '.$paises['pais'].' - Num: '.$otro_total.'<br>';
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

        var options = {colorAxis: {colors: ['#27ae60', '#e67e22', '#e74c3c']}};
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
    <div class="col-md-9">
      <?php echo $array_certificadas; ?>
      <h4>MAPA DE ORGANIZACIONES SPP CERTIFICADAS <?php echo 'TOTAL: '.$total_certificadas; ?></h4>
      <div class="col-xs-12">
        <div id="regions_div" style="width: 900px; height: 400px;"></div>
      </div>
    </div>

    <div class="col-md-3" style="height: 500px;overflow: scroll;">
      <table class="table table-bordered table-striped table-condensed" style="font-size:12px;">
        <tr style="background-color:#4caf50;color:#ffffff">
          <th>#</th>
          <th>País</th>
          <th>Organizaciones (<span style="color:red"><?php echo $total_certificadas; ?></span>)</th>
        </tr>
        <?php 
        $row_paises = mysql_query("SELECT pais FROM opp GROUP BY pais", $dspp) or die(mysql_error());
        $contador_paises = 1;
        while($paises = mysql_fetch_assoc($row_paises)){
          $row_certificadas = mysql_query("SELECT opp.idopp, opp.pais, solicitud_certificacion.tipo_solicitud, solicitud_certificacion.estatus_interno AS 'solicitud_estatus_interno', certificado.idcertificado, certificado.vigencia_inicio, certificado.vigencia_fin, certificado.archivo AS 'certificado' FROM opp LEFT JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp LEFT JOIN certificado ON solicitud_certificacion.idsolicitud_certificacion = certificado.idsolicitud_certificacion WHERE $array_opp2 AND $array_archivadas AND $array_canceladas AND opp.pais = '$paises[pais]' GROUP BY opp.idopp ORDER BY opp.idopp", $dspp) or die(mysql_error());
          $total_certificadas = mysql_fetch_assoc($row_certificadas);
          $otro_total = mysql_num_rows($row_certificadas);
          //echo 'PAIS: '.$paises['pais'].' - Num: '.$otro_total.'<br>';
          if($otro_total > 0){
            echo '<tr>';
              echo '<td>'.$contador_paises.'</td>';
              echo '<td>'.$paises['pais'].'</td>';
              echo '<td>'.$otro_total.'</td>';
            echo '</tr>';
            $contador_paises++;
          }
        }
         ?>
      </table>
    </div>

    </div>