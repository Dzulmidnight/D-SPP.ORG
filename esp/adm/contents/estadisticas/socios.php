<?php 
require_once('../Connections/dspp.php');
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


mysql_select_db($database_dspp, $dspp);


/* MUESTRA LAS SOLICITUDES CON LOS OPPs SEPARADOS
SELECT opp.*, solicitud_certificacion.*, COUNT(solicitud_certificacion.idsolicitud_certificacion) AS "TOTAL_SOLICITUD" FROM opp INNER JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp WHERE opp.pais = "PerÃº" GROUP BY opp.idopp
*/

/*
SELECT opp.idopp, opp.pais, solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp, solicitud_certificacion.idoc , solicitud_certificacion.status ,COUNT(solicitud_certificacion.idsolicitud_certificacion) AS "TOTAL_SOLICITUD" FROM opp INNER JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp WHERE opp.pais = "PerÃº"
opp.pais = 'PerÃº'

SELECT opp.idopp, opp.pais, opp.estatus_opp, opp.estatus_dspp, num_socios.idnum_socios, num_socios.idopp, num_socios.numero FROM num_socios INNER JOIN opp ON num_socios.idopp = opp.idopp WHERE opp.pais = 'PerÃº' AND (opp.estatus_opp != 'CANCELADO' OR opp.estatus_opp != 'ARCHIVADO' OR opp.estatus_opp IS NULL) GROUP BY num_socios.idopp*/
?>

<div class="col-md-12">
  <div class="row">
    <h4>Socios</h4>
    <p class="alert alert-info" style="padding:5px;">Nota: No se incluyen OPPs archivadas ni canceladas, asi como OPPs que no han ingresado su numero de socios.</p>

    <table class="tg table table-bordered table-condensed table-hover">
      <tr class="success" style="font-size:10px">
        <th class="tg-yw4l" rowspan="2">País</th>
        <th class="tg-yw4l" rowspan="2">Num OPPs</th>
        <th class="tg-yw4l" rowspan="2">Num Socios</th>
        <?php
        $array_tabla = array();
        $array_productos = array();
        $row_productos = mysql_query("SELECT productos.producto_general FROM productos INNER JOIN num_socios ON productos.idopp = num_socios.idopp WHERE productos.idopp IS NOT NULL AND productos.producto_general IS NOT NULL GROUP BY producto_general ORDER BY producto_general ASC", $dspp) or die(mysql_error());
        while($productos = mysql_fetch_assoc($row_productos)){
        ?>
          <th class="tg-yw4l" colspan="2">
            <?php echo $productos['producto_general']; ?>
          </th>
        <?php
          $array_tabla[] = "<td style='font-size:10px;'>Número de OPP(s)</td><td style='font-size:10px;'>Número de Productores</td>";
          $array_productos[] = $productos['producto_general'];
        }
         ?>
      </tr>
      <tr>
        <?php 
        for ($i=0; $i < count($array_tabla); $i++) { 
          echo $array_tabla[$i];
        }
         ?>
      </tr>

      <?php
        $num_opp = 0;
        $total_opps = 0;
        $total_socios = 0;
        $row_pais = mysql_query("SELECT opp.pais, num_socios.idopp FROM opp INNER JOIN num_socios ON opp.idopp = num_socios.idopp GROUP BY opp.pais", $dspp) or die(mysql_error());
        while($pais = mysql_fetch_assoc($row_pais)){
          $row_socios = mysql_query("SELECT opp.idopp, opp.pais, opp.estatus_opp, opp.estatus_interno, opp.estatus_dspp, num_socios.idnum_socios, num_socios.idopp, num_socios.numero FROM num_socios INNER JOIN opp ON num_socios.idopp = opp.idopp WHERE (opp.pais = '$pais[pais]') AND ((opp.estatus_opp != 'CANCELADO') AND (opp.estatus_opp != 'ARCHIVADO') AND (opp.estatus_interno != 10) OR (opp.estatus_opp IS NULL)) GROUP BY num_socios.idopp", $dspp) or die(mysql_error());
          $num_opp = mysql_num_rows($row_socios);
          $num_socios = 0;
          while($socios = mysql_fetch_assoc($row_socios)){
            $num_socios += $socios['numero'];
          }
          $total_opps = $num_opp + $total_opps;
          $total_socios += $num_socios;
        ?>
          <tr>
            <td><?php echo $pais['pais']; ?></td>
            <td class="info"><?php echo $num_opp; ?></td>
            <td class="info"><?php echo $num_socios; ?></td>
            <?php
            $total_opp_producto = 0;

            for ($i=0; $i < count($array_productos) ; $i++) { 
              $row_opp_productos = mysql_query("SELECT opp.idopp, opp.pais, num_socios.idnum_socios, num_socios.idopp, num_socios.numero, productos.producto_general FROM productos INNER JOIN opp ON productos.idopp = opp.idopp INNER JOIN num_socios ON productos.idopp = num_socios.idopp WHERE opp.pais = '$pais[pais]' AND productos.producto_general = '$array_productos[$i]' AND ((opp.estatus_opp != 'CANCELADO') AND (opp.estatus_opp != 'ARCHIVADO') AND (opp.estatus_interno != 10) OR (opp.estatus_opp IS NULL)) GROUP BY num_socios.idopp ORDER BY opp.idopp ASC");
              $num_opp_producto = mysql_num_rows($row_opp_productos);
              //definimos el estilo de la tabla cuando cuente con datos
              $estilo = ''; 
              if($num_opp_producto > 0){
                $estilo = "style='background-color:#2980b9;color:#ecf0f1;'";
              }
            ?>
            <td <?php echo $estilo; ?>><?php echo $num_opp_producto; ?></td>
            <td <?php echo $estilo; ?>>
            <?php 
              $total_socios_opp = 0;
              while($opp_productos = mysql_fetch_assoc($row_opp_productos)){
                $total_socios_opp += $opp_productos['numero'];
              }
              echo $total_socios_opp;
             ?>
            </td>
            <?php
            }
            ?>
          </tr>
        <?php
        }
      ?>
      <tr>
        <td>Total:</td>
        <td class="info"><?php echo $total_opps; ?></td>
        <td class="info"><?php echo $total_socios; ?></td>

      </tr>
    </table>
  </div>  
</div>
