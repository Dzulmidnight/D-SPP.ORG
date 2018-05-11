<?php require_once('Connections/dspp.php');
mysql_select_db($database_dspp, $dspp);
function mayuscula($variable) {
  $variable = strtr(strtoupper($variable),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
  return $variable;
}
 ?>

<!DOCTYPE html>
<html lang="es">
  <head>
<script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
  <script>tinymce.init({ selector:'textarea' });</script>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>D-SPP.ORG</title>

    <!-- Bootstrap core CSS -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="bootstrap/css/bootstrap-theme.css" rel="stylesheet">
    <link href="bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="bootstrap/css/bootstrap-theme.min.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <!--<script src="../js/fileinput.min.js" type="text/javascript"></script>
    <script src="../js/fileinput_locale_es.js"></script>-->


     <!---LIBRERIAS DE Bootstrap File Input-->

    <script type="text/javascript" src="js/bootstrap-filestyle.js"></script>
    <link rel="stylesheet" href="chosen/chosen.css">


    <!------------------- bootstrap-switch -------------->

      <link href="bootstrap-switch-master/bootstrap-switch.css" rel="stylesheet">
      <script src="bootstrap-switch-master/bootstrap-switch.js"></script>

    <!------------------- bootstrap-switch -------------->    

  <style>
  .chosen-container-multi .chosen-choices li.search-field input[type="text"]{padding: 15px;}
  </style>
  </head>

  <body>

    <div class="container">
      <h4>RENOVACIÓN DE SOLICITUD</h4>
      <?php 
      $query = "SELECT opp.idopp, opp.spp, opp.nombre, opp.abreviacion, opp.pais, certificado.idcertificado, certificado.idsolicitud_certificacion, certificado.vigencia_fin, solicitud_certificacion.tipo_solicitud, solicitud_certificacion.fecha_registro FROM certificado INNER JOIN opp ON certificado.idopp = opp.idopp LEFT JOIN solicitud_certificacion ON certificado.idsolicitud_certificacion = solicitud_certificacion.idsolicitud_certificacion WHERE certificado.vigencia_fin LIKE '%2017%' GROUP BY certificado.idopp ORDER BY opp.nombre";
      $row_solicitud = mysql_query($query, $dspp) or die(mysql_error());

       ?>
      <table class="table table-bordered table-condensed" style="font-size:12px;">
        <thead>
          <tr>
            <th>#</th>
            <th>#SPP</th>
            <th>NOMBRE</th>
            <th>ABREVIACIÓN</th>
            <th>PAÍS</th>
            <th>ID SOLICITUD</th>
            <th>FECHA DE SOLICITUD</th>
            <th>TIPO</th>
            <th>FIN DEL CERTIFICADO</th>
            <th>Nº SOLICITUDES</th>
            <th>INICIO SIGUIENTE SOLICITUD</th>
            <th>ID SIGUIENTE SOLICITUD</th>
          </tr>
        </thead>
        <tbody>
        <?php 
        $contador = 1;
        while($solicitud = mysql_fetch_assoc($row_solicitud)){
          /*$query_certificado = "SELECT vigencia_inicio, vigencia_fin FROM certificado WHERE idsolicitud_certificacion = $solicitud[idsolicitud_certificacion]";
          $row_certificado = mysql_query($query_certificado, $dspp) or die(mysql_error());
          $certificado = mysql_fetch_assoc($row_certificado);*/

          $query_total = mysql_query("SELECT idsolicitud_certificacion FROM solicitud_certificacion WHERE solicitud_certificacion.idopp = $solicitud[idopp]", $dspp) or die(mysql_error());
          $total_solicitudes = mysql_num_rows($query_total);

        ?>
        <tr>
          <td><?php echo $contador; ?></td>
          <!-- SPP -->
          <td><?php echo $solicitud['spp']; ?></td>
          <!-- NOMBRE -->
          <td><?php echo mayuscula($solicitud['nombre']); ?></td>
          <!-- ABREVIACIÓN -->
          <td><?php echo mayuscula($solicitud['abreviacion']); ?></td>
          <!-- PAIS DE LA ORGANIZACIÓN -->
          <td><?php echo mayuscula($solicitud['pais']); ?></td>
          <!-- ID DE LA PRIMERA SOLICITUD -->
          <td><?php echo $solicitud['idsolicitud_certificacion']; ?></td>
          <!-- FECHA DE LA SOLICITUD -->
          <td>
            <?php 
            if(isset($solicitud['fecha_registro'])){
              echo date('Y-m-d', $solicitud['fecha_registro']);
            }
             ?>
          </td>
          <!-- TIPO DE SOLICITUD -->
          <td>
            <?php 
            if($solicitud['tipo_solicitud'] == 'NUEVA'){
              echo '<span style="color:green">'.$solicitud['tipo_solicitud'].'</span>';
            }else{
              echo '<span style="color:red">'.$solicitud['tipo_solicitud'].'</span>';
            }
             ?>
          </td>
          <!-- FIN DEL CERTIFICADO -->
          <td><?php echo '<b style="color:red">'.$solicitud['vigencia_fin'].'</b>'; ?></td>
          <!-- TOTAL DE SOLICITUDES EN EL SISTEMA -->
          <td><?php echo $total_solicitudes; ?></td>
          <!-- consultar la siguiente solicitud -->
          <?php 
          $query = "SELECT solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.fecha_registro FROM solicitud_certificacion WHERE solicitud_certificacion.idopp = $solicitud[idopp] AND solicitud_certificacion.idsolicitud_certificacion > '$solicitud[idsolicitud_certificacion]' LIMIT 1";
          $row_siguiente = mysql_query($query, $dspp) or die(mysql_error());
          $siguiente = mysql_fetch_assoc($row_siguiente);
           ?>
          <!-- fecha solicitud 2018 -->
          <td>
          <?php 
          if(isset($siguiente['idsolicitud_certificacion'])){
            echo '<b style="color:green">'.date('Y-m-d', $siguiente['fecha_registro']).'</b>';
          }else{
            echo '<span style="color:red">NO TIENE SOLICITUD</span>';
          }
           ?>
          </td>
          <!-- id solicitud 2018 -->
          <td>
          <?php echo $siguiente['idsolicitud_certificacion']; ?>
          </td>
        </tr>
        <?php
        $contador++;
        }
         ?>
        </tbody>
      </table>
    </div>

<hr>
    <div class="container">
      <?php
      $query = "SELECT solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.tipo_solicitud, solicitud_certificacion.estatus_interno, solicitud_certificacion.estatus_dspp, solicitud_certificacion.fecha_registro, opp.idopp, opp.spp, opp.abreviacion, opp.nombre AS 'nombre_opp', opp.pais, estatus_interno.nombre AS 'nombre_interno', estatus_dspp.nombre AS 'nombre_dspp', oc.abreviacion AS 'nombre_certificadora' FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp LEFT JOIN estatus_interno ON solicitud_certificacion.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON solicitud_certificacion.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN oc ON solicitud_certificacion.idoc = oc.idoc WHERE FROM_UNIXTIME(solicitud_certificacion.fecha_registro, '%Y') = 2017 AND solicitud_certificacion.tipo_solicitud = 'NUEVA' ORDER BY solicitud_certificacion.fecha_registro";
      $query_opp = mysql_query($query,$dspp) or die(mysql_error());
       ?>
      <table class="table table-bordered table-condensed" style="font-size:12px">
        <thead>
          <tr>
            <th>
              #
            </th>
            <th>
              #SPP
            </th>
            <th>
              NOMBRE
            </th>
            <th>
              ABREVIACIÓN
            </th>
            <th>PAIS</th>
            <th>
              FECHA DE SOLICITUD
            </th>
            <th>
              CERTIFICADO
            </th>
            <th>
              CERTIFICADORA
            </th>

            <th>
              ID SOLICITUD
            </th>
            <th>
              ESTATUS INTERNO
            </th>
            <th>
              ESTATUS D-SPP
            </th>
            
            <th>
              TIPO
            </th>
            
            
          </tr>
        </thead>
        <tbody>
          <?php
          $contador = 1;
          while($opp = mysql_fetch_assoc($query_opp)){
            $query_num = "SELECT idsolicitud_certificacion FROM solicitud_certificacion WHERE idopp = $opp[idopp]";
            $query_solicitudes = mysql_query($query_num, $dspp) or die(mysql_error());
            $numero = mysql_num_rows($query_solicitudes);

            $query_certificado = "SELECT idcertificado, vigencia_inicio, vigencia_fin FROM certificado WHERE idsolicitud_certificacion = $opp[idsolicitud_certificacion]";
            $row_certificado = mysql_query($query_certificado,$dspp) or die(mysql_error());
            $num_certificado = mysql_num_rows($row_certificado);
            $certificado = mysql_fetch_assoc($row_certificado);
          ?>
          <tr>
            <td><?php echo $contador; ?></td>

            <td><?php echo $opp['spp']; ?></td>

            <td><?php echo mayuscula($opp['nombre_opp']); ?></td>

            <td><?php echo mayuscula($opp['abreviacion']); ?></td>

            <td><?php echo mayuscula($opp['pais']); ?></td>

            <td><?php echo date('Y-m-d', $opp['fecha_registro']); ?></td>

            <td><?php echo '#'.$opp['idsolicitud_certificacion']; ?></td>
            <td><?php echo '<span style="color:red">'.$opp['nombre_interno'].'</span>'; ?></td>
            <td><?php echo '<span style="color:red">'.$opp['nombre_dspp'].'</span>'; ?></td>
            
            <td><?php echo $opp['tipo_solicitud'] ?></td>
            
            

            
            <!-- fechas del certificado -->
            <td>
            <?php 
            if(isset($certificado['idcertificado'])){
              echo '<span style="color:green">'.$certificado['vigencia_inicio'].'</span>';
            }else{
              if($opp['estatus_interno'] == 8){
                echo '<p style="color:red">CERTIFICADO NO CARGADO</p>';
              }
            }
             ?>
            </td>
            <td>
            <?php 
            if(isset($certificado['idcertificado'])){
              echo '<span style="color:red">'.$certificado['vigencia_fin'].'</span>';
            }else{
              if($opp['estatus_interno'] == 8){
                echo '<p style="color:red">CERTIFICADO NO CARGADO</p>';
              }
            }
             ?>
            </td>
            
            <td><?php echo $opp['idopp'] ?></td>
            <!-- numero de solicitudes -->
            <?php 
            if($numero > 1){
              echo '<td class="warning">'.$numero.'</td>';
            }else{
              echo '<td>'.$numero.'</td>';
            }
             ?>
            <td><?php echo $opp['nombre_certificadora']; ?></td>
          </tr>
          <?php
          $contador++;
          }
           ?>
        </tbody>
      </table>      
    </div>

  </body>
</html>




  <script src="chosen/chosen.jquery.js" type="text/javascript"></script>
  <script type="text/javascript">
    var config = {
      '.chosen-select'           : {},
      '.chosen-select-deselect'  : {allow_single_deselect:true},
      '.chosen-select-no-single' : {disable_search_threshold:10},
      '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
      '.chosen-select-width'     : {width:"95%"}
    }
    for (var selector in config) {
      $(selector).chosen(config[selector]);
    }
  </script>