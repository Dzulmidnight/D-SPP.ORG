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

mysql_select_db($database_dspp, $dspp);
if(isset($_POST['oc_delete'])){
  $query=sprintf("delete from oc where idoc = %s",GetSQLValueString($_POST['idoc'], "text"));
  $ejecutar=mysql_query($query,$dspp) or die(mysql_error());
}

$maxRows_oc = 20;
$pageNum_oc = 0;
if (isset($_GET['pageNum_oc'])) {
  $pageNum_oc = $_GET['pageNum_oc'];
}
$startdetalle_oc = $pageNum_oc * $maxRows_oc;

$query_oc = "SELECT * FROM oc ORDER BY nombre ASC";
$query_limit_oc = sprintf("%s LIMIT %d, %d", $query_oc, $startdetalle_oc, $maxRows_oc);
$oc = mysql_query($query_limit_oc, $dspp) or die(mysql_error());
//$detalle_oc = mysql_fetch_assoc($oc);

if (isset($_GET['totalRows_oc'])) {
  $totalRows_oc = $_GET['totalRows_oc'];
} else {
  $all_oc = mysql_query($query_oc);
  $totalRows_oc = mysql_num_rows($all_oc);
}
$totalPages_oc = ceil($totalRows_oc/$maxRows_oc)-1;


?>
<script language="JavaScript"> 
function preguntar(){ 
    if(!confirm('¿Estas seguro de eliminar el registro?')){ 
       return false; } 
} 
</script>

<div class="panel panel-default">
  <div class="panel-heading">Lista OC(s)</div>
  <div class="panel-body">
    <table class="table table-condensed table-bordered table-hover" style="font-size:12px;">
    <thead>
      <tr>
        <th class="text-center">#SPP</th>
        <th class="text-center">Nombre</th>
        <th class="text-center">Abreviación</th>
        <th class="text-center">OPP's</th>
        <th class="text-center">Empresas</th>
        <!--<th class="text-center">Solicitudes</th>-->
        <th class="text-center">País</th>
        <th class="text-center">RFC</th>
        <th class="text-center">Acciones</th>
      </tr>
      </thead>
      <tbody>
      <?php while($detalle_oc = mysql_fetch_assoc($oc)){ ?>
        <tr>
        <!-------------------- INICIA SECCION IDF -------------------->      
          <td>
            <a class="btn btn-sm btn-primary" style="width:100%" href="?OC&amp;detail&amp;idoc=<?php echo $detalle_oc['idoc']; ?>&contact">Consultar<br>
              <?php echo $detalle_oc['spp']; ?>
            </a>
          </td>
        <!-------------------- TERMINAR SECCION IDF -------------------->

              <!-------------------- INICIA SECCION NOMBRE -------------------->
          <td>
            <p class="alert alert-success" style="padding:7px;"><?php echo $detalle_oc['nombre']; ?></p>
          </td>
              <!-------------------- TERMINAR SECCION NOMBRE -------------------->

              <!-------------------- INICIA SECCION ABREVIACIÓN -------------------->
          <td>
            <p class="alert alert-success" style="padding:7px;"><?php echo $detalle_oc['abreviacion']; ?></p>
          </td>
              <!-------------------- TERMINAR SECCION ABREVIACIÓN -------------------->

              <!-------------------- INICIA SECCION OPPs -------------------->
          <td align="right">
            <?
              /*$query_topp = "SELECT count(*) as total FROM opp where idoc='".$detalle_oc['idoc']."'";
              $topp = mysql_query($query_topp, $dspp) or die(mysql_error());
              $row_topp = mysql_fetch_assoc($topp);*/

              $query_opp = mysql_query("SELECT * FROM opp WHERE idoc = $detalle_oc[idoc]",$dspp) or die(mysql_error());
              $total_opp = mysql_num_rows($query_opp);
              $opp = mysql_fetch_assoc($query_opp);

            ?>
              <?php if($total_opp == 0){ ?>
                <p class="alert alert-danger text-center">0</p>
              <?php }else{   ?>
                <a class="btn btn-sm btn-success" style="width:100%" href="?OPP&select&query=<? echo $detalle_oc['idoc'];?>">Consultar<br> 
                <? echo $total_opp;?>
                </a>
              <?php } ?>
          </td>
          <td>
            <?php 
              $query_empresa = mysql_query("SELECT * FROM empresa WHERE idoc = $detalle_oc[idoc]", $dspp) or die(mysql_error());
              $total_empresa = mysql_num_rows($query_empresa);
              $empresa = mysql_fetch_assoc($query_empresa);

              if($total_empresa == 0){
                echo '<p class="alert alert-danger text-center">0</p>';
              }else{
              ?>
                <a class="btn btn-sm btn-success" style="width:100%" href="?EMPRESAS&select&query=<? echo $detalle_oc['idoc'];?>">Consultar<br> 
                <? echo $total_empresa;?>
                </a>            
              <?php
              }

             ?>
          </td>
              <!-------------------- TERMINAR SECCION OPPs -------------------->

              <!-------------------- INICIA SECCION SOLICITUDES -------------------->
          <!--<td aling="right">
            <?
              $query_topp2 = "SELECT count(*) as total2 FROM solicitud_certificacion where idoc='".$detalle_oc['idoc']."'";
              $topp2 = mysql_query($query_topp2, $dspp) or die(mysql_error());
              $row_topp2 = mysql_fetch_assoc($topp2);
            ?>
            <?php if($row_topp2['total2'] == 0){ ?>
              <p class="alert alert-danger text-center" style="padding:7px;">0</p>
            <?php }else{   ?>
              <a class="btn btn-sm btn-success" style="width:100%;pading:7px" href="?OC&solicitud&query=<? echo $detalle_oc['idoc'];?>">Consultar <br>
                <? echo $row_topp2['total2'];?>
              </a>   
            <?php } ?>     
          </td>-->
              <!-------------------- TERMINAR SECCION SOLICITUDES -------------------->

              <!-------------------- INICIA SECCION PAIS -------------------->
          <td><p class="alert alert-success" style="padding:7px;"><?php echo $detalle_oc['pais']; ?></p></td>
              <!-------------------- TERMINAR SECCION PAIS -------------------->

              <!-------------------- INICIA SECCION RFC -------------------->
          <td><p class="alert alert-success" style="padding:7px;"><?php echo $detalle_oc['razon_social']; ?></p></td>
              <!-------------------- TERMINAR SECCION RFC -------------------->

              <!-------------------- INICIA SECCION ELIMINAR -------------------->
          <td>
            <a href="?OC&amp;detail&amp;idoc=<?php echo $detalle_oc['idoc']; ?>&contact" class="btn btn-xs btn-info col-xs-6" data-toggle="tooltip" data-placement="top" title="Editar">
              <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
            </a>

            <form action="" method="post" name="formularioEliminar" ONSUBMIT="return preguntar();">
              <button class="btn btn-xs btn-danger col-xs-6" type="subtmit" value="Eliminar" data-toggle="tooltip" data-placement="top" title="Eliminar">
                <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
              </button>        
              <input type="hidden" value="OC eliminado correctamente" name="mensaje" />
              <input type="hidden" value="1" name="oc_delete" />
              <input type="hidden" value="<?php echo $detalle_oc['idoc']; ?>" name="idoc" />
            </form>
          </td>
              <!-------------------- TERMINAR SECCION ELIMINAR -------------------->

        </tr>
        <?php }  ?>
        </tbody>
    </table>
  </div>
</div>

<?php
mysql_free_result($oc);
?>