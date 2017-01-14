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
    <table class="table table-bordered table-condensed table-hover">
      <thead>
        <tr class="success" style="font-size:10px">
          <th>País</th>
          <th>Num OPPs</th>
          <th>Num Socios</th>
          <?php
          $array_productos = array();
          $row_productos = mysql_query("SELECT productos.producto_general FROM productos INNER JOIN num_socios ON productos.idopp = num_socios.idopp WHERE productos.idopp IS NOT NULL AND productos.producto_general IS NOT NULL GROUP BY producto_general ORDER BY producto_general ASC", $dspp) or die(mysql_error());
          while($productos = mysql_fetch_assoc($row_productos)){
          ?>
          <th style="padding:0px;margin:0px;">
            <table border="1">
              <tr>
                <td colspan="2"><?php echo $productos['producto_general']; ?></td>
              </tr>
              <tr>
                <td width="50%">Número de OPP</td>
                <td width="50%">Número de Productores</td>
              </tr>
            </table>            
          </th>
          <?php
            $array_productos[] = $productos['producto_general'];
          }
           ?>
        </tr>
      </thead>
      <tbody>
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
            for ($i=0; $i < count($array_productos) ; $i++) { 
              $row_opp_productos2 = mysql_query("SELECT opp.idopp, opp.pais, num_socios.idnum_socios, num_socios.idopp, num_socios.numero, productos.producto_general FROM productos INNER JOIN opp ON productos.idopp = opp.idopp INNER JOIN num_socios ON productos.idopp = num_socios.idopp WHERE opp.pais = '$pais[pais]' AND productos.producto_general = '$array_productos[$i]' GROUP BY num_socios.idopp ORDER BY opp.idopp ASC");
              $row_opp_productos = mysql_query("SELECT opp.idopp, opp.pais, num_socios.idnum_socios, num_socios.idopp, num_socios.numero, productos.producto_general FROM productos INNER JOIN opp ON productos.idopp = opp.idopp INNER JOIN num_socios ON productos.idopp = num_socios.idopp WHERE opp.pais = '$pais[pais]' AND productos.producto_general = '$array_productos[$i]' GROUP BY num_socios.idopp ORDER BY opp.idopp ASC");
              $total_opp_productos = mysql_num_rows($row_opp_productos);
              if($total_opp_productos != 0){
                echo "<td style='background-color:#3498db;padding:0px;margin:0px;'>";
              }else{
                echo "<td style='padding:0px;margin:0px;'>";
              }

                echo '<table border="1">';
                  echo '<tr>';
                    //// INICIA NUMERO DE OPPS
                    echo '<td width="50%">';
                      echo '<b style="color:red">'.$total_opp_productos.'</b>';
                      while($opp_productos2 = mysql_fetch_assoc($row_opp_productos)){
                        echo $opp_productos2['idopp'].'('.$opp_productos2['numero'].')'.' - ';
                      }
                    echo '</td>';
                    /// TERMINA NUMERO DE OPPS

                    echo '<td width="50%">';
                      $total_socios_opp = 0;
                      while($opp_productos = mysql_fetch_assoc($row_opp_productos2)){
                        $total_socios_opp += $opp_productos['numero'];
                      }
                      echo 'total:'.$total_socios_opp;
                    echo '</td>';

                  echo '</tr>';
                echo '</table>';

              echo "</td>";
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
      </tbody>
    </table>
  </div>  
</div>
