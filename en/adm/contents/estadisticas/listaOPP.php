<?php require_once('../Connections/dspp.php'); ?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO opp (idf, password, nombre, abreviacion, sitio_web, telefono, email, pais, idoc, razon_social, direccion_fiscal, rfc) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['idf'], "text"),
                       GetSQLValueString($_POST['password'], "text"),
                       GetSQLValueString($_POST['nombre'], "text"),
                       GetSQLValueString($_POST['abreviacion'], "text"),
                       GetSQLValueString($_POST['sitio_web'], "text"),
                       GetSQLValueString($_POST['telefono'], "text"),
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($_POST['pais'], "text"),
                       GetSQLValueString($_POST['idoc'], "int"),
                       GetSQLValueString($_POST['razon_social'], "text"),
                       GetSQLValueString($_POST['direccion_fiscal'], "text"),
                       GetSQLValueString($_POST['rfc'], "text"));

  mysql_select_db($database_dspp, $dspp);
  $Result1 = mysql_query($insertSQL, $dspp) or die(mysql_error());

  $insertGoTo = "main_menu.php?OPP&add&mensaje=OPP agregado correctamente";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_dspp, $dspp);
$query_pais = "SELECT nombre FROM paises ORDER BY nombre ASC";
$pais = mysql_query($query_pais, $dspp) or die(mysql_error());
$row_pais = mysql_fetch_assoc($pais);
$totalRows_pais = mysql_num_rows($pais);

mysql_select_db($database_dspp, $dspp);
$query_oc = "SELECT idoc, idf, abreviacion, pais FROM oc ORDER BY nombre ASC";
$oc = mysql_query($query_oc, $dspp) or die(mysql_error());
$row_oc = mysql_fetch_assoc($oc);
$totalRows_oc = mysql_num_rows($oc);

/* MUESTRA LAS SOLICITUDES CON LOS OPP SEPARADOS
SELECT opp.*, solicitud_certificacion.*, COUNT(solicitud_certificacion.idsolicitud_certificacion) AS "TOTAL_SOLICITUD" FROM opp INNER JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp WHERE opp.pais = "PerÃº" GROUP BY opp.idopp
*/

/*
SELECT opp.idopp, opp.pais, solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp, solicitud_certificacion.idoc , solicitud_certificacion.status ,COUNT(solicitud_certificacion.idsolicitud_certificacion) AS "TOTAL_SOLICITUD" FROM opp INNER JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp WHERE opp.pais = "PerÃº"
*/


//$query_anual = "SELECT fecha_elaboracion, FROM_UNIXTIME(solicitud_certificacion.fecha_elaboracion, '%Y') AS 'estadistica_anual' FROM solicitud_certificacion GROUP BY FROM_UNIXTIME(solicitud_certificacion.fecha_elaboracion, '%Y' )";
//$ejecutar3 = mysql_query($query_anual,$dspp) or die(mysql_error());


?>

<?php 
  //$query_opp = "SELECT opp.idopp, opp.idf, opp.nombre, opp.abreviacion, opp.pais, solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp, solicitud_certificacion.resp1, solicitud_certificacion.status, solicitud_certificacion.status_publico, productos.idproducto, productos.idopp, productos.producto  FROM opp LEFT JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp LEFT JOIN productos ON opp.idopp = productos.idopp";
  if(isset($_POST['filtroPalabra']) && $_POST['filtroPalabra'] == 1){
    $buscar = $_POST['buscar'];

    //$query_opp = "SELECT opp.idopp, opp.idf, opp.nombre, opp.abreviacion, opp.pais, opp.estado AS 'estadoOPP', solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp, solicitud_certificacion.resp1, solicitud_certificacion.status, solicitud_certificacion.status_publico, status.idstatus, status.nombre AS 'estatusInterno', status_publico.idstatus_publico, status_publico.nombre AS 'estatusPublico', productos.idproducto, productos.idopp, productos.producto  FROM opp LEFT JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp LEFT JOIN status ON solicitud_certificacion.status = status.idstatus LEFT JOIN status_publico ON solicitud_certificacion.status_publico = status_publico.idstatus_publico LEFT JOIN productos ON opp.idopp = productos.idopp WHERE (idf LIKE '%$buscar%') OR (opp.nombre LIKE '%$buscar%') OR (opp.abreviacion LIKE '%$buscar%') OR (opp.pais LIKE '%$buscar%') OR (estadoOPP = '%$buscar%') OR (solicitud_certificacion.resp1 LIKE '%$buscar%') OR (estatusInterno LIKE '%$buscar%') OR (estatusPublico LIKE '%$buscar%')";


    $query_opp = "SELECT opp.idopp, opp.idf, opp.nombre, opp.abreviacion, opp.pais, opp.estado AS 'estadoOPP', solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp, solicitud_certificacion.resp1, solicitud_certificacion.status, solicitud_certificacion.status_publico, status.idstatus, status.nombre AS 'estatusInterno', status_publico.idstatus_publico, status_publico.nombre AS 'estatusPublico', productos.idproducto, productos.idopp, productos.producto  FROM opp LEFT JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp LEFT JOIN status ON solicitud_certificacion.status = status.idstatus LEFT JOIN status_publico ON solicitud_certificacion.status_publico = status_publico.idstatus_publico LEFT JOIN productos ON opp.idopp = productos.idopp WHERE (idf LIKE '%$buscar%') OR (opp.nombre LIKE '%$buscar%') OR (opp.abreviacion LIKE '%$buscar%') OR (opp.pais LIKE '%$buscar%') OR (status.nombre LIKE '%$buscar%') OR (status_publico.nombre LIKE '%$buscar%')";

  }else if(isset($_POST['orden'])){
    if(isset($_POST['numero'])){
      $ordenar = $_POST['numero'];
    }
    if(isset($_POST['numeroDesc'])){
      $ordenar = $_POST['numeroDesc']." DESC";
    }

    if(isset($_POST['nombre'])){
      $ordenar = $_POST['nombre'];
    }
    if(isset($_POST['nombreDesc'])){
      $ordenar = $_POST['nombreDesc']." DESC";
    }
    if(isset($_POST['abreviacion'])){
      $ordenar = $_POST['abreviacion'];
    }    
    if(isset($_POST['abreviacionDesc'])){
      $ordenar = $_POST['abreviacionDesc']." DESC";
    }
    if(isset($_POST['pais'])){
      $ordenar = $_POST['pais'];
    }
    if(isset($_POST['paisDesc'])){
      $ordenar = $_POST['paisDesc']." DESC";
    }

    if(isset($_POST['estatus_interno'])){
      $ordenar = $_POST['estatus_interno'];
    }
    if(isset($_POST['estatus_internoDesc'])){
      $ordenar = $_POST['estatus_internoDesc']." DESC";
    }

    if(isset($_POST['estatus_publico'])){
      $ordenar = $_POST['estatus_publico'];
    }
    if(isset($_POST['estatus_publicoDesc'])){
      $ordenar = $_POST['estatus_publicoDesc']." DESC";
    }
    if(isset($_POST['productores'])){
      $ordenar = $_POST['productores'];
    }
    if(isset($_POST['productoresDesc'])){
      $ordenar = $_POST['productoresDesc']." DESC";
    }


    $query_opp = "SELECT opp.idopp, opp.idf, opp.nombre, opp.abreviacion, opp.pais, opp.estado AS 'estadoOPP', solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp, solicitud_certificacion.resp1, solicitud_certificacion.status, solicitud_certificacion.status_publico, status.idstatus, status.nombre AS 'estatusInterno', status_publico.idstatus_publico, status_publico.nombre AS 'estatusPublico', productos.idproducto, productos.idopp, productos.producto  FROM opp LEFT JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp LEFT JOIN status ON solicitud_certificacion.status = status.idstatus LEFT JOIN status_publico ON solicitud_certificacion.status_publico = status_publico.idstatus_publico LEFT JOIN productos ON opp.idopp = productos.idopp ORDER BY $ordenar";

  }else{
    $query_opp = "SELECT opp.idopp, opp.idf, opp.nombre, opp.abreviacion, opp.pais, opp.estado AS 'estadoOPP', solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp, solicitud_certificacion.resp1, solicitud_certificacion.status, solicitud_certificacion.status_publico, status.idstatus, status.nombre AS 'estatusInterno', status_publico.idstatus_publico, status_publico.nombre AS 'estatusPublico', productos.idproducto, productos.idopp, productos.producto  FROM opp LEFT JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp LEFT JOIN status ON solicitud_certificacion.status = status.idstatus LEFT JOIN status_publico ON solicitud_certificacion.status_publico = status_publico.idstatus_publico LEFT JOIN productos ON opp.idopp = productos.idopp WHERE solicitud_certificacion.status != 24";
  }

  $opp = mysql_query($query_opp,$dspp);
  //$row_opp = mysql_fetch_assoc($opp);

  $contador = 1;

  $query_paises = "SELECT * FROM paises";
  $paises = mysql_query($query_paises,$dspp) or die(mysql_error());
?>

<div class="panel panel-info">
  <div class="panel-heading">Lista de Organizaciones de Pequeños Productores </div>
  <div class="panel-body">
    <form action="" method="post" enctype="application/x-www-form-urlencoded" id="buscar">
      <div class="col-xs-6">
          <div class="input-group">
            <input type="text" class="form-control" name="buscar" placeholder="Palabra clave...">
            <span class="input-group-btn">
              <input type="hidden" name="filtroPalabra" value="1">
              <button class="btn btn-default" type="submit">Buscar( Sensible a acentos ) !</button>
            </span>
          </div><!-- /input-group -->  
      </div>
    </form>

    <table class="table table-bordered table-hover" style="font-size:12px;">
      <thead>
    <form action="" method="post" id="orden" enctype="application/x-www-form-urlencoded">
          <th class="text-center">Nº</th>

          <th class="text-center" >
            #SPP<br>
            <button class="btn btn-xs btn-default" name="numero" value="idf" type="submit"><span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span></button>
            <button class="btn btn-xs btn-default" name="numeroDesc" value="idf" type="submit"><span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></button>
          </th>
          <th class="text-center" style="width:150px;">
            Nombre OPP<br>
            <button class="btn btn-xs btn-default" name="nombre" value="abreviacion" type="submit"><span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span></button>
            <button class="btn btn-xs btn-default" name="nombreDesc" value="abreviacion" type="submit"><span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></button>
          </th>
          <th class="text-center" style="width:150px;">
            Abreviación<br>
            <button class="btn btn-xs btn-default" name="abreviacion" value="abreviacion" type="submit"><span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span></button>
            <button class="btn btn-xs btn-default" name="abreviacionDesc" value="abreviacion" type="submit"><span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></button>
          </th>
          <th class="text-center">
            <!------ INICIA CUADRO DE BUSQUEDA ---------->

              <select class="chosen-select-deselect" name="registros[]" id="" multiple>
                <option value="todos">Todos</option>
                <?php 
                while ($row_paises = mysql_fetch_assoc($paises)) {
                  echo "<option value='$row_paises[COLUMN_NAME]'>$row_paises[COLUMN_NAME]</option>";
                }
                 ?>
              </select>
              <br>
            <!------ TERMINA CUADRO DE BUSQUEDA ---------->

            Pais<br>
            <button class="btn btn-xs btn-default" name="pais" value="pais" type="submit"><span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span></button>
            <button class="btn btn-xs btn-default" name="paisDesc" value="pais" type="submit"><span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></button>
          </th>
          <th style="width:130px;" class="text-center">
            Estatus Interno<br>
            <button class="btn btn-xs btn-default" name="estatus_interno" value="idstatus" type="submit"><span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span></button>
            <button class="btn btn-xs btn-default" name="estatus_internoDesc" value="idstatus" type="submit"><span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></button>
          </th>
          <th style="width:130px;" class="text-center">
            Estatus Público<br>
            <button class="btn btn-xs btn-default" name="estatus_publico" value="idstatus_publico" type="submit"><span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span></button>
            <button class="btn btn-xs btn-default" name="estatus_publicoDesc" value="idstatus_publico" type="submit"><span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></button>
          </th>
          <th class="text-center warning">
            Estatus General
          </th>
          <th  class="text-center">
            <!------ INICIA CUADRO DE BUSQUEDA ---------->

              <select class="chosen-select-deselect" name="registros[]" id="" multiple>
                <option value="todos">Todos</option>
                <?php 
                while ($row_certificado = mysql_fetch_assoc($ejecutar)) {
                  echo "<option value='$row_certificado[COLUMN_NAME]'>$row_certificado[COLUMN_NAME]</option>";
                }
                 ?>
              </select>
              <br>

            <!------ TERMINA CUADRO DE BUSQUEDA ---------->

            PRODUCTOS
          </th>
          <th style="width:130px;" class="text-center">
            # PRODUCTORES<br>
            <button class="btn btn-xs btn-default" name="productores" value="resp1" type="submit"><span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span></button>
            <button class="btn btn-xs btn-default" name="productoresDesc" value="resp1" type="submit"><span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></button>
          </th>
          <input type="hidden" name="orden" value="orden">
    </form>
      </thead>
      <?php 
        /*$contSolicitud = 0;
        $contProceso = 0;
        $contPositiva = 0;
        $contCertificada = 0;
        $contTotal = 0;*/
       ?>
 
      <tbody>
        <?php while($row_opp = mysql_fetch_assoc($opp)){ ?>
          <tr>
  <!--------------------------- INICIO  SECCION CONTADOR ---------------------------------------->
            <td> 
              <?php echo "Nº: ".$contador; ?>
            </td>
  <!--------------------------- TERMINA SECCION CONTADOR ---------------------------------------->

  <!--------------------------- INICIO  SECCION IDF ---------------------------------------->
            <td>
              <?php echo $row_opp['idf']; ?>
            </td>
  <!--------------------------- TERMINA  SECCION IDF ---------------------------------------->

  <!--------------------------- INICIO  NOMBRE OPP ---------------------------------------->
            <td>
              <?php echo $row_opp['nombre']; ?>
            </td>
  <!--------------------------- TERMINO  NOMBRE OPP ---------------------------------------->

  <!--------------------------- INICIO  ABREVIACION OPP ---------------------------------------->
            <td>
              <?php echo $row_opp['abreviacion']; ?>
            </td>
  <!--------------------------- TERMINO  ABREVIACION OPP ---------------------------------------->

  <!--------------------------- INICIO  SECCION PAIS ---------------------------------------->
            <td class="text-center">
              <?php 
                echo $row_opp['pais'];
               ?>
            </td>
  <!--------------------------- TERMINA  SECCION PAIS ---------------------------------------->

  <!--------------------------- INICIO SECCION ESTATUS INTERNO ---------------------------------------->
            <td class="text-center">
              <?php 
                //echo $row_opp['status'];
                echo $row_opp['estatusInterno'];
               ?>        
            </td>
  <!--------------------------- TERMINA SECCION ESTATUS INTERNO ---------------------------------------->
              
  <!--------------------------- INICIO SECCION ESTATUS PUBLICO ---------------------------------------->
            <td class="text-center">
              <?php //echo $row_opp['status_publico']; ?>  
              <?php echo $row_opp['estatusPublico']; ?>   
            </td>
  <!--------------------------- FIN SECCION ESTATUS PUBLICO ---------------------------------------->
            <td class="text-center">
              <?php 
              if($row_opp['estadoOPP'] == 10){
                echo "<p class='alert alert-success' style='padding:7px;'>Certificado</p>";
              }else{
                echo "<p class='alert alert-warning' style='padding:7px;'>En Revisión</p>";
              }
               ?>
            </td>

  <!--------------------------- INICIO SECCION PRODUCTOS ---------------------------------------->
            <td class="text-center">
              <?php 
                //$contProducto = 0;
                $queryProducto = "SELECT * FROM productos WHERE idopp = '$row_opp[idopp]'";
                $producto = mysql_query($queryProducto,$dspp) or die(mysql_error());
                $contProducto = mysql_num_rows($producto);

                if($contProducto >= 1){
                  while($row_producto = mysql_fetch_assoc($producto)){
                    echo $row_producto['producto']." , ";
                  }
                }else{
                  echo "No encontrado";
                }
               ?>        
            </td>
  <!--------------------------- TERMINA SECCION PRODUCTOS ---------------------------------------->

  <!--------------------------- INICIO TOTAL SOLICITUDES ---------------------------------------->
            <td class="text-center success">
              <?php 
                echo $row_opp['resp1'];
               ?>        
            </td>
  <!--------------------------- TERMINA TOTAL SOLICITUDES ---------------------------------------->


          </tr>

        <?php $contador++; } ?>
        <tr>
          <td>TOTALES</td>
          <td class="text-center">


          </td>
          <td class="text-center">

          </td>
          <td class="text-center">

          </td>
          <td class="text-center">

          </td>
          <td class="text-center">

          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>