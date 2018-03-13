<?php 
require_once('../Connections/dspp.php');
require_once('../Connections/mail.php');
mysql_select_db($database_dspp, $dspp);
        //$asunto = "Nuevo Registro - D-SPP( Datos de Acceso )";

//$row_opp = mysql_query("SELECT opp.*, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', certificado.vigencia_fin FROM opp LEFT JOIN oc ON opp.idoc = oc.idoc LEFT JOIN estatus_publico ON opp.estatus_publico = estatus_publico.idestatus_publico INNER JOIN certificado ON opp.idopp = certificado.idopp WHERE opp.estatus_opp != 'NUEVA' AND opp.estatus_opp != 'CANCELADA' AND opp.estatus_opp != 


?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="img/FUNDEPPO.png">
    <title>SPP GLOBAL | D-SPP</title>

    <!-- Bootstrap core CSS -->
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../bootstrap/css/bootstrap-theme.css" rel="stylesheet">
    <link href="../bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="../bootstrap/css/bootstrap-theme.min.css" rel="stylesheet">

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>


  </head>

  <body>
  <div class="container-fluid">
    <div class="row">
      <!-- INICIA BARRA DE NAVEGACIÓN  -->
      <div class="col-md-12">
        <ul class="nav nav-pills">
          <li role="presentation" style="margin:0px;padding:0px;"><a href="index.php"><img src="../img/FUNDEPPO.png" alt=""></a></li>
          <li role="presentation" <? if(isset($_GET['OPP'])){?> class="active" <? }?>><a href="index.php?OPP" data-toggle="tooltip" data-placement="bottom" title="Clic para iniciar sesión">Organización de Pequeños Productores</a></li>
          <li role="presentation" <? if(isset($_GET['OC'])){?> class="active" <? }?>><a href="index.php?OC" data-toggle="tooltip" data-placement="bottom" title="Clic para iniciar Sesión">Organismo de Certificación</a></li>
          <li role="presentation" <? if(isset($_GET['COM'])){?> class="active" <? }?>><a href="index.php?COM" data-toggle="tooltip" data-placement="bottom" title="Clic para iniciar sesión">EMPRESAS</a></li>
          <li role="presentation" <? if(isset($_GET['ADM'])){?> class="active" <? }?>><a href="index.php?ADM">ADM</a></li>
          <li role="presentation" <? if(isset($_GET['RECURSOS'])){?> class="active" <? }?>><a href="#">RECURSOS</a></li>
        </ul>
      </div>
      <!-- TERMINA BARRA DE NAVEGACIÓN  -->

      <!-- (PRIMERA)INICIA SECCIÓN PRINCIPAL -->
      <div class="col-md-6">
        <p class="alert alert-success" style="padding:9px;"><a href="lista_opp.php"><span class="glyphicon glyphicon-th-list" aria-hidden="true"></span> Revisar Lista de Organizaciones de Pequeños Productores</a></p>
      </div>
      <div class="col-md-6">
        <p class="alert alert-default" style="padding:9px;"><a href="lista_empresas.php"><span class="glyphicon glyphicon-th-list" aria-hidden="true"></span> Revisar Lista de Compradores y otros Actores</a></p>
      </div>
    
      <!-- (PRIMERA)TERMINA SECCIÓN PRINCIPAL -->

    </div>
    <div class="row">
      <div class="col-md-6">

      </div>
      <div class="col-md-6">
        <?php 
        //$row_certificaciones = mysql_query("SELECT certificaciones.idcertificacion, certificaciones.idsolicitud_certificacion, certificaciones.certificacion, certificaciones.certificadora, solicitud_certificacion.idopp, opp.nombre, opp.abreviacion FROM certificaciones INNER JOIN solicitud_certificacion ON certificaciones.idsolicitud_certificacion = solicitud_certificacion.idsolicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE certificaciones.certificacion LIKE '%FAIRTRADE%' OR certificaciones.certificacion LIKE '%NATURLAND%' GROUP BY solicitud_certificacion.idopp", $dspp) or die(mysql_error());
       
        //$row_certificaciones = mysql_query("SELECT certificaciones.idcertificacion, certificaciones.idsolicitud_certificacion, certificaciones.certificacion, certificaciones.certificadora, solicitud_certificacion.idopp, opp.nombre, opp.abreviacion FROM certificaciones INNER JOIN solicitud_certificacion ON certificaciones.idsolicitud_certificacion = solicitud_certificacion.idsolicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp GROUP BY certificaciones.certificacion", $dspp) or die(mysql_error());
        $row_certificaciones = mysql_query("SELECT certificaciones.idcertificacion, certificaciones.idsolicitud_certificacion, certificaciones.certificacion, certificaciones.certificadora, solicitud_certificacion.idopp, opp.nombre, opp.abreviacion FROM certificaciones INNER JOIN solicitud_certificacion ON certificaciones.idsolicitud_certificacion = solicitud_certificacion.idsolicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE certificaciones.certificacion LIKE '%comercio%' OR certificaciones.certificacion LIKE '%Naturland%' OR certificaciones.certificacion LIKE '%Fairtrade%' OR certificaciones.certificacion LIKE '%Fair%' OR certificaciones.certificacion LIKE '%FLO%' OR certificaciones.certificacion LIKE '%SPP%' OR certificaciones.certificacion LIKE '%simbolo%' GROUP BY solicitud_certificacion.idopp ORDER BY opp.nombre", $dspp) or die(mysql_error());
        $total_comercio = mysql_num_rows($row_certificaciones);
        $comercio_justo = '';
        ?>
        <table class="table table-bordered">
          <tr>
            <td>nº</td>
            <td>IDCERTIFICACIÓN</td>
            <td>CERTIFICACIÓN</td>
            <td>IDSOLICITUD</td>
            <td>ABREVIACION</td>
            <td>IDOPP</td>
          </tr>
          <?php
          $contador = 1;
          while($certificaciones = mysql_fetch_assoc($row_certificaciones))
          {
            echo '<tr>';
              echo '<td>'.$contador.'</td>';
              echo '<td>'.$certificaciones['idcertificacion'].'</td>';
              echo '<td>'.$certificaciones['certificacion'].'</td>';
              echo '<td>'.$certificaciones['idsolicitud_certificacion'].'</td>';
              echo '<td>'.$certificaciones['abreviacion'].'</td>';
              echo '<td>'.$certificaciones['idopp'].'</td>';
            echo '</tr>';

            if($contador < $total_comercio){
              $comercio_justo .= 'opp.idopp = '.$certificaciones['idopp'].' OR ';
            }else{
              $comercio_justo .= 'opp.idopp = '.$certificaciones['idopp'];
            }

            
            $contador++;
          }
          ?>
        </table>
        <?php echo $comercio_justo; ?>
        <?php echo '<p>'.$total_comercio.'</p>'; ?>
      </div>

      <div class="col-md-12">        
        <table class="table table-bordered table-condensed">
          <tr>
            <td>nº</td>
            <td>#spp</td>
            <td>Nombre</td>
            <td>Abreviación</td>
            <td>País</td>
            <td>Productos</td>
          </tr>
          <?php 
          $row_opp = mysql_query("SELECT opp.idopp, opp.spp, opp.nombre, opp.abreviacion, opp.pais, solicitud_certificacion.idsolicitud_certificacion FROM opp INNER JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp WHERE $comercio_justo GROUP BY opp.idopp ORDER BY opp.nombre", $dspp) or die(mysql_error());
          $conta = 1;
          while($opp = mysql_fetch_assoc($row_opp)){
          ?>
            <tr>
              <td><?php echo $conta; ?></td>
              <td><?php echo $opp['spp']; ?></td>
              <td><?php echo $opp['nombre']; ?></td>
              <td><?php echo $opp['abreviacion']; ?></td>
              <td><?php echo $opp['pais']; ?></td>
              <td>
                <?php 
                  $query_productos = mysql_query("SELECT GROUP_CONCAT(certificacion SEPARATOR ', ') AS 'lista_productos' FROM certificaciones WHERE certificaciones.idsolicitud_certificacion = '$opp[idsolicitud_certificacion]'", $dspp) or die(mysql_error());
                  $productos = mysql_fetch_assoc($query_productos);
                  echo $productos['lista_productos'];
                  /*
                  if(empty($productos['lista_productos'])){
                    $query_productos = mysql_query("SELECT GROUP_CONCAT(producto SEPARATOR ', ') AS 'lista_productos' FROM productos WHERE idopp = '$informacion[idopp]'", $dspp) or die(mysql_error());
                    $productos = mysql_fetch_assoc($query_productos);
                    echo $productos['lista_productos'];
                  }else{
                    echo '<p style="color:green">'.$productos['lista_productos'].'</p>';
                  }*/
                 ?>
              </td>
            </tr>
          <?php
          $conta++;
          }
           ?>
        </table>
      </div>
    </div>
  </div>

    <script>
      $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    })
    </script>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="../bootstrap/js/bootstrap.min.js"></script>

  </body>
</html>