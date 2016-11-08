<?php 
require_once('../Connections/dspp.php');
require_once('../Connections/mail.php');
mysql_select_db($database_dspp, $dspp);
        //$asunto = "Nuevo Registro - D-SPP( Datos de Acceso )";
//SELECT empresa.idempresa, empresa.idf, empresa.nombre, empresa.abreviacion, empresa.idoc, empresa.pais, empresa.sitio_web, empresa.email, empresa.telefono, empresa.estatusPagina, empresa.estado, oc.abreviacion AS 'abreviacion_oc', status_pagina.nombre AS 'nombre_estatus', certificado.vigenciafin FROM empresa LEFT JOIN oc ON empresa.idoc = oc.idoc LEFT JOIN status_pagina ON empresa.estatusPagina = status_pagina.idEstatusPagina INNER JOIN certificado ON empresa.idempresa = certificado.idempresa WHERE empresa.estado  IS NOT NULL AND empresa.estado != 'ARCHIVADO' AND empresa.situacion != 'CANCELADO' AND empresa.situacion != 'NUEVA' ORDER BY certificado.vigenciafin

if(isset($_POST['busqueda_palabra']) && $_POST['busqueda_palabra'] == 1){
  $palabra = $_POST['palabra'];

  $query_empresa = "SELECT empresa.*, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', MAX(certificado.vigencia_fin) AS 'fecha_fin' FROM empresa LEFT JOIN oc ON empresa.idoc = oc.idoc LEFT JOIN estatus_publico ON empresa.estatus_publico = estatus_publico.idestatus_publico INNER JOIN certificado ON empresa.idempresa = certificado.idempresa WHERE (empresa.spp LIKE '%$palabra%' OR empresa.nombre LIKE '%$palabra%' OR empresa.abreviacion LIKE '%$palabra%' OR empresa.pais LIKE '%$palabra%' OR oc.abreviacion LIKE '%$palabra%' OR empresa.email LIKE '%$palabra%' OR empresa.telefono LIKE '%$palabra%') AND empresa.estatus_empresa != 'NUEVA' AND empresa.estatus_empresa != 'CANCELADA' GROUP BY certificado.idempresa ORDER BY fecha_fin  DESC";
}else if(isset($_POST['busqueda_filtros']) && $_POST['busqueda_filtros'] == 1){
  $idoc = $_POST['idoc'];
  $pais = $_POST['pais'];
  $producto = $_POST['nombre_producto'];
  $idempresa_producto = '';

  if(!empty($pais) && !empty($idoc) && !empty($producto)){ /// BUSQUEDA DE PAIS, OC Y PRODUCTOS

    //$query_productos = mysql_query("SELECT idempresa FROM productos WHERE producto LIKE '%$producto%' GROUP BY idempresa", $dspp) or die(mysql_error());
    $query_productos = mysql_query("SELECT empresa.idempresa, productos.producto FROM empresa LEFT JOIN productos ON empresa.idempresa = productos.idempresa WHERE idoc = $idoc AND pais = '$pais' AND producto LIKE '%$producto%' GROUP BY idempresa", $dspp) or die(mysql_error());
    $total_idempresa = mysql_num_rows($query_productos);
    $cont_idempresa = 1;
    while($producto_empresa = mysql_fetch_assoc($query_productos)){
      $idempresa_producto .= "empresa.idempresa = '$producto_empresa[idempresa]'";
      if($cont_idempresa < $total_idempresa){
        $idempresa_producto .= " OR ";
      }
      $cont_idempresa++;
    }

    $query_empresa = "SELECT empresa.*, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', MAX(certificado.vigencia_fin) AS 'fecha_fin', productos.idproducto, productos.producto FROM empresa LEFT JOIN oc ON empresa.idoc = oc.idoc LEFT JOIN estatus_publico ON empresa.estatus_publico = estatus_publico.idestatus_publico INNER JOIN certificado ON empresa.idempresa = certificado.idempresa LEFT JOIN productos ON empresa.idempresa = productos.idempresa WHERE (empresa.idoc = '$idoc' AND empresa.pais = '$pais' AND $idempresa_producto) AND empresa.estatus_empresa != 'NUEVA' AND empresa.estatus_empresa != 'CANCELADA' GROUP BY certificado.idempresa ORDER BY fecha_fin  DESC";

  }else if(!empty($pais) && !empty($idoc) && empty($producto)){ ///BUSQUEDA DE PAIS Y OC
    $query_empresa = "SELECT empresa.*, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', MAX(certificado.vigencia_fin) AS 'fecha_fin' FROM empresa LEFT JOIN oc ON empresa.idoc = oc.idoc LEFT JOIN estatus_publico ON empresa.estatus_publico = estatus_publico.idestatus_publico INNER JOIN certificado ON empresa.idempresa = certificado.idempresa WHERE (empresa.idoc = '$idoc' AND empresa.pais = '$pais') AND empresa.estatus_empresa != 'NUEVA' AND empresa.estatus_empresa != 'CANCELADA' GROUP BY certificado.idempresa ORDER BY fecha_fin  DESC";
  }else if(empty($pais) && !empty($idoc) && empty($producto)){ ///BUSQUEDA DE OC
    $query_empresa = "SELECT empresa.*, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', MAX(certificado.vigencia_fin) AS 'fecha_fin' FROM empresa LEFT JOIN oc ON empresa.idoc = oc.idoc LEFT JOIN estatus_publico ON empresa.estatus_publico = estatus_publico.idestatus_publico INNER JOIN certificado ON empresa.idempresa = certificado.idempresa WHERE (empresa.idoc = '$idoc') AND empresa.estatus_empresa != 'NUEVA' AND empresa.estatus_empresa != 'CANCELADA' GROUP BY certificado.idempresa ORDER BY fecha_fin  DESC";
  }else if(!empty($pais) && empty($idoc) && empty($producto)){///BUSQUEDA DE PAIS
    $query_empresa = "SELECT empresa.*, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', MAX(certificado.vigencia_fin) AS 'fecha_fin' FROM empresa LEFT JOIN oc ON empresa.idoc = oc.idoc LEFT JOIN estatus_publico ON empresa.estatus_publico = estatus_publico.idestatus_publico INNER JOIN certificado ON empresa.idempresa = certificado.idempresa WHERE (empresa.pais = '$pais') AND empresa.estatus_empresa != 'NUEVA' AND empresa.estatus_empresa != 'CANCELADA' GROUP BY certificado.idempresa ORDER BY fecha_fin  DESC";
  }else if(!empty($pais) && !empty($producto) && empty($idoc)){///BUSQUEDA PAIS Y PRODUCTO
    //$query_productos = mysql_query("SELECT idempresa FROM productos WHERE producto LIKE '%$producto%' GROUP BY idempresa", $dspp) or die(mysql_error());
    $query_productos = mysql_query("SELECT empresa.idempresa, productos.producto FROM empresa LEFT JOIN productos ON empresa.idempresa = productos.idempresa WHERE pais = '$pais' AND producto LIKE '%$producto%' GROUP BY idempresa", $dspp) or die(mysql_error());
    $total_idempresa = mysql_num_rows($query_productos);
    $cont_idempresa = 1;
    while($producto_empresa = mysql_fetch_assoc($query_productos)){
      $idempresa_producto .= "empresa.idempresa = '$producto_empresa[idempresa]'";
      if($cont_idempresa < $total_idempresa){
        $idempresa_producto .= " OR ";
      }
      $cont_idempresa++;
    }

    $query_empresa = "SELECT empresa.*, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', MAX(certificado.vigencia_fin) AS 'fecha_fin', productos.idproducto, productos.producto FROM empresa LEFT JOIN oc ON empresa.idoc = oc.idoc LEFT JOIN estatus_publico ON empresa.estatus_publico = estatus_publico.idestatus_publico INNER JOIN certificado ON empresa.idempresa = certificado.idempresa LEFT JOIN productos ON empresa.idempresa = productos.idempresa WHERE ( empresa.pais = '$pais' AND $idempresa_producto) AND empresa.estatus_empresa != 'NUEVA' AND empresa.estatus_empresa != 'CANCELADA' GROUP BY certificado.idempresa ORDER BY fecha_fin  DESC";
  }else if(!empty($producto) && empty($idoc) && empty($pais)){///BUSQUEDA DE PRODUCTO
    $query_productos = mysql_query("SELECT idempresa FROM productos WHERE producto LIKE '%$producto%' GROUP BY idempresa", $dspp) or die(mysql_error());
    $total_idempresa = mysql_num_rows($query_productos);
    $cont_idempresa = 1;
    while($producto_empresa = mysql_fetch_assoc($query_productos)){
      $idempresa_producto .= "empresa.idempresa = '$producto_empresa[idempresa]'";
      if($cont_idempresa < $total_idempresa){
        $idempresa_producto .= " OR ";
      }
      $cont_idempresa++;
    }

    $query_empresa = "SELECT empresa.*, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', MAX(certificado.vigencia_fin) AS 'fecha_fin' FROM empresa LEFT JOIN oc ON empresa.idoc = oc.idoc LEFT JOIN estatus_publico ON empresa.estatus_publico = estatus_publico.idestatus_publico INNER JOIN certificado ON empresa.idempresa = certificado.idempresa WHERE ($idempresa_producto) AND empresa.estatus_empresa != 'NUEVA' AND empresa.estatus_empresa != 'CANCELADA' GROUP BY certificado.idempresa ORDER BY fecha_fin  DESC";

  }else{
    $query_empresa = "SELECT empresa.*, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', MAX(certificado.vigencia_fin) AS 'fecha_fin' FROM empresa LEFT JOIN oc ON empresa.idoc = oc.idoc LEFT JOIN estatus_publico ON empresa.estatus_publico = estatus_publico.idestatus_publico INNER JOIN certificado ON empresa.idempresa = certificado.idempresa WHERE empresa.estatus_empresa != 'NUEVA' AND empresa.estatus_empresa != 'CANCELADA' GROUP BY certificado.idempresa ORDER BY fecha_fin  DESC";
  }


}else{
  $query_empresa = "SELECT empresa.*, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', MAX(certificado.vigencia_fin) AS 'fecha_fin' FROM empresa LEFT JOIN oc ON empresa.idoc = oc.idoc LEFT JOIN estatus_publico ON empresa.estatus_publico = estatus_publico.idestatus_publico INNER JOIN certificado ON empresa.idempresa = certificado.idempresa WHERE empresa.estatus_empresa != 'NUEVA' AND empresa.estatus_empresa != 'CANCELADA' GROUP BY certificado.idempresa ORDER BY fecha_fin  DESC";
}

$row_empresa = mysql_query($query_empresa, $dspp) or die(mysql_error());
//$row_empresa = mysql_query("SELECT empresa.*, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', MAX(certificado.vigencia_fin) AS 'fecha_fin' FROM empresa LEFT JOIN oc ON empresa.idoc = oc.idoc LEFT JOIN estatus_publico ON empresa.estatus_publico = estatus_publico.idestatus_publico INNER JOIN certificado ON empresa.idempresa = certificado.idempresa WHERE empresa.estatus_empresa != 'NUEVA' AND empresa.estatus_empresa != 'CANCELADA'", $dspp) or die(mysql_error());
$total_empresa = mysql_num_rows($row_empresa);

$row_pais = mysql_query("SELECT * FROM paises", $dspp) or die(mysql_error());
$row_oc = mysql_query("SELECT * FROM oc", $dspp) or die(mysql_error());
$query_productos = mysql_query("SELECT * FROM productos WHERE productos.idempresa IS NOT NULL GROUP BY producto",$dspp) or die(mysql_error());

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
          <li role="presentation" <? if(isset($_GET['empresa'])){?> class="active" <? }?>><a href="index.php?OPP" data-toggle="tooltip" data-placement="bottom" title="Clic para iniciar sesión">Organización de Pequeños Productores</a></li>
          <li role="presentation" <? if(isset($_GET['OC'])){?> class="active" <? }?>><a href="index.php?OC" data-toggle="tooltip" data-placement="bottom" title="Clic para iniciar Sesión">Organismo de Certificación</a></li>
          <li role="presentation" <? if(isset($_GET['COM'])){?> class="active" <? }?>><a href="index.php?COM" data-toggle="tooltip" data-placement="bottom" title="Clic para iniciar sesión">EMPRESAS</a></li>
          <li role="presentation" <? if(isset($_GET['ADM'])){?> class="active" <? }?>><a href="index.php?ADM">ADM</a></li>
          <li role="presentation" <? if(isset($_GET['RECURSOS'])){?> class="active" <? }?>><a href="#">RECURSOS</a></li>
        </ul>
      </div>
      <!-- TERMINA BARRA DE NAVEGACIÓN  -->

      <!-- (PRIMERA)INICIA SECCIÓN PRINCIPAL -->
      <div class="col-md-6">
        <p class="alert alert-default" style="padding:9px;"><a href="lista_opp.php"><span class="glyphicon glyphicon-th-list" aria-hidden="true"></span> Revisar Lista de Organizaciones de Pequeños Productores</a></p>
      </div>
      <div class="col-md-6">
        <p class="alert alert-success" style="padding:9px;"><a href="lista_empresas.php"><span class="glyphicon glyphicon-th-list" aria-hidden="true"></span> Revisar Lista de Compradores y otros Actores</a></p>
      </div>
      <div class="col-md-12">
        <div class="row">
          <form action="" method="POST">
            <div class="col-md-3">
              <div class="input-group">
                <span class="input-group-btn">
                  <button class="btn btn-default" name="busqueda_palabra" value="1" type="submit">Buscar</button>
                </span>
                <input type="text" class="form-control" name="palabra" placeholder="Buscar por palabra">
              </div><!-- /input-group -->
            </div>
          </form>
          <form action="" method="POST">
            <div class="col-md-9 alert alert-info">
              <div class="text-center col-md-12">
                <b style="color:#d35400">Seleccione los parametros de los cuales desea realizar la busqueda</b>
              </div> 
              <div class="row">
                <div class="col-xs-4">
                  Organismo de Certificación
                  <select name="idoc" class="form-control">
                    <option value=''>Selecciona un organismo de certificación</option>
                    <?php 
                    while($oc = mysql_fetch_assoc($row_oc)){
                      echo "<option value='$oc[idoc]'>$oc[abreviacion]</option>";
                    }
                     ?>
                  </select>
                </div>
                <div class="col-xs-3">
                  País
                  <select name="pais" class="form-control">
                    <option value=''>Selecciona un país</option>
                    <?php 
                    while($pais = mysql_fetch_assoc($row_pais)){
                      echo "<option value='".utf8_encode($pais['nombre'])."'>".utf8_encode($pais['nombre'])."</option>";
                    }
                     ?>
                  </select>
                </div>
                <div class="col-xs-3">
                  Producto
                  <select class="form-control" name="nombre_producto" id="">
                    <option value=''>Seleccione un producto</option>
                    <?php 
                    while($lista_productos = mysql_fetch_assoc($query_productos)){
                      echo "<option value='$lista_productos[producto]'>$lista_productos[producto]</option>";
                    }
                     ?>
                  </select>
                </div>
                <div class="col-xs-2">
                  <button type="submit" class="btn btn-success" name="busqueda_filtros" value="1">Buscar</button>
                </div>
              </div>
            </div>
          </form>

        </div>



        <table class="table table-bordered table-condensed table-striped">
          <thead>
             <tr>
               <td colspan="12" class="info">
                <!--<p>NOTAS:</p>
                <p>1. El estatus de 'En Revisión' significa que la OPP puede encontrarse en cualquiera de los siguientes sub estatus: 'En proceso de renovación', 'Certificado expirado' o 'Suspendido'</p>
                <p>2. Es responsabilidad de los interesados verificar si la OPP se encuentran en proceso de renovación del certificado, cuando en la presente lista se indica que el estatus es "En Revisión"</p>
                <p>3. El estatus de 'Cancelado' siginifica que la OPP ya no esta certificada por Incumplimiento con el Marco Regulatorio SPP o por renuncia voluntaria. Si fue cancelado por incumpliento con el marco regulatorio, deberá esperar dos años a partir de la cancelación para volver a solicitar la certificación.</p>-->
                <h4 style="font-size:13px;text-align:center">NOTA: <span style="color:#e74c3c">ES RESPONSABILIDAD DEL INTERESADO REVISAR EL ESTATUS ESPECÍFICO EN EL QUE SE ENCUENTRA LA EMPRESA</span></h4>
              </td>
             </tr>

            <tr>
              <th colspan="2">
                Exportar: 
                <a href="#" onclick="document.formulario1.submit()"><img src="../img/pdf.png"></a>
                <form name="formulario1" method="POST" action="../reportes/lista_empresas.php">
                  <input type="hidden" name="lista_publica_pdf" value="1">
                  <input type="hidden" name="query_pdf" value="<?php echo $query_empresa; ?>">
                </form> 

              </th>

              <th class="text-center warning" colspan="10">Lista de Compradores Registrados / List of Buyers Registered (Total: <?php echo $total_empresa; ?>)</th>
            </tr>
            <tr style="font-size:11px;">
              <th class="text-center">Nº</th>
              <th class="text-center">NOMBRE DE LA EMPRESA/COMPANY´S NAME</th>
              <th class="text-center">ABREVIACIÓN/ SHORT NAME</th>
              <th class="text-center">PAÍS/COUNTRY</th>
              <th class="text-center">PRODUCTO(s)/PRODUCTS (s)</th>
              <th class="text-center">VIGENCIA DEL REGISTRO / EFFECTIVE DATE OF REGISTRATION</th>
              <!--<th class="text-center">ESTATUS/STATUS</th>-->
              <th class="text-center">ENTIDAD QUE OTORGÓ EL CERTIFICADO/ENTITY THAT GRANTED CERTIFICATE</th>
              <th class="text-center">#SPP</th>
              <th class="text-center">EMAIL</th>
              <th class="text-center">SITIO WEB / WEB SITE</th>
              <th class="text-center">TELÉFONO/TELEPHONE</th>
            </tr>
          </thead>
          <tbody style="font-size:11px;">
            <?php 
            if($total_empresa == 0){
              echo "<tr><td class='info' colspan='12'>No se encontraron registros</td></tr>";
            }else{
              $contador = 1;
              while($empresa = mysql_fetch_assoc($row_empresa)){
                //$row_certificado = mysql_query("SELECT * FROM certificado WHERE idempresa = $empresa[idempresa] ORDER BY certificado.vigencia_fin DESC LIMIT 1", $dspp) or die(mysql_error());
                //$certificado = mysql_fetch_assoc($row_certificado);
                $vigencia = strtotime($empresa['fecha_fin']);
              ?>
                <tr>
                  <td><?php echo $contador; ?></td>
                  <td><?php echo $empresa['nombre']; ?></td>
                  <td><?php echo $empresa['abreviacion']; ?></td>
                  <td><?php echo $empresa['pais']; ?></td>
                  <td>
                    <?php 
                    $row_productos = mysql_query("SELECT producto FROM productos WHERE idempresa = $empresa[idempresa]", $dspp) or die(mysql_error());
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
                  <td><?php echo date('d/m/Y', $vigencia); ?></td>
                  <!--<td><?php echo $empresa['nombre_publico']; ?></td>-->
                  <td><?php echo $empresa['abreviacion_oc']; ?></td>
                  <td><?php echo $empresa['spp']; ?></td>
                  <td><?php echo $empresa['email']; ?></td>
                  <td><?php echo $empresa['sitio_web']; ?></td>
                  <td><?php echo $empresa['telefono']; ?></td>
                </tr>
              <?php
              $contador++;
              }
            }
             ?>
             <!--<tr>
               <td colspan="12" class="info">
                <p>NOTAS:</p>
                <p>1. El estatus de 'En Revisión' significa que la OPP puede encontrarse en cualquiera de los siguientes sub estatus: 'En proceso de renovación', 'Certificado expirado' o 'Suspendido'</p>
                <p>2. Es responsabilidad de los interesados verificar si la OPP se encuentran en proceso de renovación del certificado, cuando en la presente lista se indica que el estatus es "En Revisión"</p>
                <p>3. El estatus de 'Cancelado' siginifica que la OPP ya no esta certificada por Incumplimiento con el Marco Regulatorio SPP o por renuncia voluntaria. Si fue cancelado por incumpliento con el marco regulatorio, deberá esperar dos años a partir de la cancelación para volver a solicitar la certificación.</p>
              </td>
             </tr>-->
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