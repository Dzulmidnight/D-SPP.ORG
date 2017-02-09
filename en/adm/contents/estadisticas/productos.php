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

$row_paises = mysql_query("SELECT * FROM vw_paises", $dspp) or die(mysql_error());
?>

<div class="col-md-12">
  <div class="row">
    <h4>Productos por País</h4>
    <table class="table table-bordered table-hover table-condensed" style="font-size:10px;">
      <thead>
        <tr class="success">
          <th>No</th>
          <th>PAÍS</th>
          <?php 
          $array_productos = array();
          $row_productos = mysql_query("SELECT productos.producto_general FROM productos WHERE idopp IS NOT NULL AND productos.producto_general IS NOT NULL GROUP BY productos.producto_general ORDER BY productos.producto_general ASC", $dspp) or die(mysql_error());
          while($productos = mysql_fetch_assoc($row_productos)){
            $array_productos[] = $productos['producto_general'];
          ?>
          <th><?php echo $productos['producto_general']; ?></th>
          <?php
          }
           ?>

        </tr>
      </thead>
      <tbody>
        <?php
        $contador = 1;
        while($pais = mysql_fetch_assoc($row_paises)){
        ?>
          <tr>
            <td><?php echo $contador; ?></td>
            <td><?php echo $pais['pais']; ?></td>
            <?php
              for ($i=0; $i < count($array_productos); $i++) { 
                $row_pais_producto = mysql_query("SELECT opp.idopp, opp.pais, productos.producto_general FROM productos INNER JOIN opp ON productos.idopp = opp.idopp WHERE opp.pais = '$pais[pais]' AND productos.producto_general = '$array_productos[$i]' AND (productos.producto_general IS NOT NULL) GROUP BY opp.idopp ORDER BY opp.idopp ASC", $dspp) or die(mysql_error());
                $total_pais_producto = mysql_num_rows($row_pais_producto);

                if($total_pais_producto > 0){
                  echo "<td style='background-color:#2ecc71;text-align:center'>X</td>";
                }else{
                  echo "<td>-</td>";
                }
              }
             ?>
          </tr>
        <?php
        $contador++;
        }
         ?>
      </tbody>
    </table>
  </div>  
</div>
