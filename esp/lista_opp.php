<?php 
require_once('../Connections/dspp.php');
require_once('../Connections/mail.php');
mysql_select_db($database_dspp, $dspp);
        //$asunto = "Nuevo Registro - D-SPP( Datos de Acceso )";
//SELECT opp.idopp, opp.idf, opp.nombre, opp.abreviacion, opp.idoc, opp.pais, opp.sitio_web, opp.email, opp.telefono, opp.estatusPagina, opp.estado, oc.abreviacion AS 'abreviacion_oc', status_pagina.nombre AS 'nombre_estatus', certificado.vigenciafin FROM opp LEFT JOIN oc ON opp.idoc = oc.idoc LEFT JOIN status_pagina ON opp.estatusPagina = status_pagina.idEstatusPagina INNER JOIN certificado ON opp.idopp = certificado.idopp WHERE opp.estado  IS NOT NULL AND opp.estado != 'ARCHIVADO' AND opp.situacion != 'CANCELADO' AND opp.situacion != 'NUEVA' ORDER BY certificado.vigenciafin

$row_opp = mysql_query("SELECT opp.*, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', certificado.vigencia_fin FROM opp LEFT JOIN oc ON opp.idoc = oc.idoc LEFT JOIN estatus_publico ON opp.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN certificado ON opp.idopp = certificado.idopp WHERE opp.estatus_opp != 'NUEVA' AND opp.estatus_opp != 'CANCELADA'", $dspp) or die(mysql_error());
$total_opp = mysql_num_rows($row_opp);

$row_pais = mysql_query("SELECT * FROM paises", $dspp) or die(mysql_error());
$row_oc = mysql_query("SELECT * FROM oc", $dspp) or die(mysql_error());
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
          <li role="presentation" <? if(isset($_GET['OPP'])){?> class="active" <? }?>><a href="?OPP" data-toggle="tooltip" data-placement="bottom" title="Clic para iniciar sesión">Organización de Pequeños Productores</a></li>
          <li role="presentation" <? if(isset($_GET['OC'])){?> class="active" <? }?>><a href="?OC" data-toggle="tooltip" data-placement="bottom" title="Clic para iniciar Sesión">Organismo de Certificación</a></li>
          <li role="presentation" <? if(isset($_GET['COM'])){?> class="active" <? }?>><a href="?COM" data-toggle="tooltip" data-placement="bottom" title="Clic para iniciar sesión">EMPRESAS</a></li>
          <li role="presentation" <? if(isset($_GET['ADM'])){?> class="active" <? }?>><a href="?ADM">ADM</a></li>
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
      <div class="col-md-12">
        <div class="col-md-3">
          Palabra
          <input type="text" class="form-control">
        </div>
        <div class="col-md-3">
          Organismo de Certificación
          <select class="form-control">
            <option>Selecciona un organismo de certificación</option>
            <?php 
            while($oc = mysql_fetch_assoc($row_oc)){
              echo "<option value='$oc[abreviacion]'>$oc[abreviacion]</option>";
            }
             ?>
          </select>
        </div>
        <div class="col-md-3">
          País
          <select class="form-control">
            <option>Selecciona un país</option>
            <?php 
            while($pais = mysql_fetch_assoc($row_pais)){
              echo "<option value='$pais[abreviacion]'>$pais[abreviacion]</option>";
            }
             ?>
          </select>
        </div>
        <div class="col-md-3"><button type="button" class="btn btn-success">Buscar</button></div>



        <table class="table table-bordered table-condensed table-striped">
          <thead>
            <tr>
              <th class="text-center warning" colspan="12">Lista de Organizaciones de Pequeños Productores (Total: <?php echo $total_opp; ?>)</th>
            </tr>
            <tr style="font-size:11px;">
              <th class="text-center">Nº</th>
              <th class="text-center">NOMBRE DE LA ORGANIZACIÓN / ORGANIZATION´S NAME</th>
              <th class="text-center">ABREVIACIÓN/ SHORT NAME</th>
              <th class="text-center">PAÍS</th>
              <th class="text-center">PRODUCTO(S) CERTIFICADO/ CERTIFIED PRODUCTS</th>
              <th class="text-center">FECHA SIGUIENTE EVALUACIÓN/ NEXT EVALUATION DATE</th>
              <th class="text-center">ESTATUS/STATUS</th>
              <th class="text-center">ENTIDAD QUE OTORGÓ EL CERTIFICADO/ENTITY THAT GRANTED CERTIFICATE</th>
              <th class="text-center">#SPP</th>
              <th class="text-center">EMAIL</th>
              <th class="text-center">SITIO WEB / WEB SITE</th>
              <th class="text-center">TELÉFONO/TELEPHONE</th>
            </tr>
          </thead>
          <tbody style="font-size:11px;">
            <?php 
            if($total_opp == 0){
              echo "<tr><td class='info' colspan='12'>No se encontraron registros</td></tr>";
            }else{
              $contador = 1;
              while($opp = mysql_fetch_assoc($row_opp)){
              ?>
                <tr>
                  <td><?php echo $contador; ?></td>
                  <td><?php echo $opp['nombre']; ?></td>
                  <td><?php echo $opp['abreviacion']; ?></td>
                  <td><?php echo $opp['pais']; ?></td>
                  <td>
                    <?php 
                    $row_productos = mysql_query("SELECT producto FROM productos WHERE idopp = $opp[idopp]", $dspp) or die(mysql_error());
                    $total_producto = mysql_num_rows($row_productos);
                    $cont = 1;
                    while($producto = mysql_fetch_assoc($row_productos)){
                      if($total_producto == 1){
                        echo $producto['producto'];
                      }else{
                        echo $producto['producto'];
                        if($cont < $total_producto){
                          echo "<span style='color:red'>, </span>";
                        }else{
                          echo ".";
                        }
                      }
                      $cont++;
                    }
                     ?>
                  </td>
                  <td><?php echo $opp['vigencia_fin']; ?></td>
                  <td><?php echo $opp['nombre_publico']; ?></td>
                  <td><?php echo $opp['abreviacion_oc']; ?></td>
                  <td><?php echo $opp['spp']; ?></td>
                  <td><?php echo $opp['email']; ?></td>
                  <td><?php echo $opp['sitio_web']; ?></td>
                  <td><?php echo $opp['telefono']; ?></td>
                </tr>
              <?php
              $contador++;
              }
            }
             ?>
          </tbody>
        </table>
      </div>      
      <!-- (PRIMERA)TERMINA SECCIÓN PRINCIPAL -->

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