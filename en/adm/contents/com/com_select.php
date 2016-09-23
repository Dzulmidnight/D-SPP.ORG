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

if(isset($_POST['com_delete'])){
  $query=sprintf("delete from com where idcom = %s",GetSQLValueString($_POST['idcom'], "text"));
  $ejecutar=mysql_query($query,$dspp) or die(mysql_error());
}

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_com = 40;
$pageNum_com = 0;
if (isset($_GET['pageNum_com'])) {
  $pageNum_com = $_GET['pageNum_com'];
}
$startRow_com = $pageNum_com * $maxRows_com;

mysql_select_db($database_dspp, $dspp);
if(isset($_GET['query'])){
  $query_com = "SELECT * FROM com where idoc='".$_GET['query']."' ORDER BY nombre ASC";
}else if(isset($_POST['filtroPalabra']) && $_POST['filtroPalabra'] == "1" && $_POST['palabraClave'] != NULL){
  $palabraClave = $_POST['palabraClave'];


  $query_com = "SELECT * FROM com WHERE idf LIKE '%$palabraClave%' OR nombre LIKE '%$palabraClave%' OR abreviacion LIKE '%$palabraClave%' OR sitio_web LIKE '%$palabraClave%' OR email LIKE '%$palabraClave%' OR pais LIKE '%$palabraClave%' OR razon_social LIKE '%$palabraClave%' OR direccion_fiscal LIKE '%$palabraClave%' OR rfc LIKE '%$palabraClave%' ORDER BY nombre ASC";

}else if(isset($_POST['filtroPais']) && $_POST['filtroPais'] == "2" && $_POST['busquedaPais'] != NULL){
  $pais = $_POST['busquedaPais'];
  $query_com = "SELECT * FROM com WHERE pais LIKE '%$pais%'";
}else{
  $query_com = "SELECT * FROM com ORDER BY nombre ASC";
}
$query_limit_com = sprintf("%s LIMIT %d, %d", $query_com, $startRow_com, $maxRows_com);
$com = mysql_query($query_limit_com, $dspp) or die(mysql_error());
//$row_com = mysql_fetch_assoc($com);

if (isset($_GET['totalRows_com'])) {
  $totalRows_com = $_GET['totalRows_com'];
} else {
  $all_com = mysql_query($query_com);
  $totalRows_com = mysql_num_rows($all_com);
}
$totalPages_com = ceil($totalRows_com/$maxRows_com)-1;

$queryString_com = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_com") == false && 
        stristr($param, "totalRows_com") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_com = "&" . htmlentities(implode("&", $newParams));
  }
}


$queryString_com = sprintf("&totalRows_com=%d%s", $totalRows_com, $queryString_com);
?>
<script language="JavaScript"> 
function preguntar(){ 
    if(!confirm('¿Estas seguro de eliminar el registro?, se eliminara toda la información relacionada con el mismo')){ 
       return false; } 
} 
</script>
  <hr>
  <div class="row">
    <div class="col-xs-6">
      <h5 class="alert alert-info" >Busqueda extendida(idf, nombre, abreviacion, sitio web, email, país, etc...). Sensible a acentos.</h5>

      <form method="post" name="filtro" action="" enctype="application/x-www-form-urlencoded">
        <div class="input-group">
          <input type="text" class="form-control" name="palabraClave" placeholder="Palabra clave...">
          <span class="input-group-btn">
            <input type="hidden" name="filtroPalabra" value="1">
            <button class="btn btn-default" type="submit">Buscar !</button>
          </span>
        </div><!-- /input-group -->        
      </form>
    </div><!-- /.col-lg-6 -->

    <div class="col-xs-4">
      <h5 class="alert alert-info" >Consultar COMs por país. Sensible a acentos</h5>
      <form method="post" name="filtro2" action="" enctype="application/x-www-form-urlencoded">      
        <select class="form-control chosen-select-deselect" data-placeholder="Buscar por país" name="busquedaPais" id="" onchange="this.form.submit()">
          <option value="">Selecciona un país</option>
          <?php 
            $query = "SELECT * FROM paises";
            $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
            while($row_paises = mysql_fetch_assoc($ejecutar)){
          ?>
            <option value="<?php echo utf8_encode($row_paises['nombre']);?>"><?php echo utf8_encode($row_paises['nombre']) ?></option>
          <?php
            }
          ?>
        </select>
        <input type="hidden" name="filtroPais" value="2">
      </form>
    </div>
  </div>
  <hr>


<div class="panel panel-default">
  <div class="panel-heading">Lista Empresas</div>
  <div class="panel-body">
    <table class="table table-condensed table-bordered table-hover" style="font-size:12px;">
      <thead>
        <tr>
          <th class="text-center">IDF</th>
          <th class="text-center">Estatus Certificado</th>
          <th class="text-center">Nombre</th>
          <th class="text-center">Abreviación</th>
          <!--<th class="text-center">Sitio WEB</th>
          <th class="text-center">Email COM</th>-->
          <th class="text-center">País</th>
          <th class="text-center">OC</th>
          <th class="text-center">Razón social</th>
          <th class="text-center">Dirección fiscal</th>
          <th class="text-center">RFC</th>
          <th class="text-center">Acciones</th>
        </tr>      
      </thead>
      <tbody>
        <?php $cont=0; while ($row_com = mysql_fetch_assoc($com)) {$cont++; ?>
          <tr>
            <td>
              <a class="btn btn-primary btn-sm" style="width:100%" href="?COM&amp;detail&amp;idcom=<?php echo $row_com['idcom']; ?>&contact">Consultar<br>
                <?php echo $row_com['idf']; ?>
              </a>
            </td>
            <td style="width:150px;">
              <?php 
                if(isset($row_opp['nombreStatus'])){
                  if($row_opp['estado'] == 10){
                    echo "<p class='informacion text-center alert alert-success' style='padding:7px;'>$row_opp[nombreStatus]</p>";
                  }
                  if($row_opp['estado'] == 11){
                    echo "<p class='informacion text-center alert alert-warning' style='padding:7px;'>$row_opp[nombreStatus]</p>";
                  }
                  if($row_opp['estado'] == 16){
                    echo "<p class='informacion text-center alert alert-danger' style='padding:7px;'>$row_opp[nombreStatus]</p>";
                  }
                }else{
                  echo "<p class='text-center'>No Disponible</p>";
                }
               ?>
            </td>
            <td>
              <?php 
                if(isset($row_com['nombre'])){
                  echo "<p>".$row_com['nombre']."</p>";
                }else{
                  echo "<p>No Disponible</p>";
                } 
              ?>
            </td>
            <td>
              <?php 
                if(isset($row_com['abreviacion'])){
                  echo "<p >".$row_com['abreviacion']."</p>";
                }else{
                  echo "<p >No Disponible</p>";
                } 
              ?>
            </td>          
            <!--<td>
              <h6 class="text-center">
                <?php 
                  if(isset($row_com['sitio_web'])){
                    echo "<p class='alert alert-success'>".$row_com['sitio_web']."</p>";
                  }else{
                    echo "<p class='alert alert-danger'>No Disponible</p>";
                  } 
                ?>
              </h6>
            </td>
            <td>
              <h6 class="text-center">
                <?php 
                  if(isset($row_com['email'])){
                    echo "<p class='alert alert-success'>".$row_com['email']."</p>";
                  }else{
                    echo "<p class='alert alert-danger'>No Disponible</p>";
                  } 
                ?>
              </h6>
            </td>-->
            <td>
              <?php echo $row_com['pais']; ?>
            </td>
            <td>      
              <?
                $query_tcom = "SELECT abreviacion FROM oc where idoc='".$row_com['idoc']."'";
                $tcom = mysql_query($query_tcom, $dspp) or die(mysql_error());
                $row_tcom = mysql_fetch_assoc($tcom);
              ?>
              <?php if(isset($row_tcom['abreviacion'])){ ?>
                <a class="btn btn-info btn-sm" style="width:100%" href="?OC&amp;detail&amp;idoc=<?php echo $row_com['idoc']; ?>&contact">Consultar
                  <?php  echo $row_tcom['abreviacion']; ?>
                </a>
              <?php }else{ ?>
                <a class="btn btn-danger btn-sm disabled" style="width:100%" href="?OC&amp;detail&amp;idoc=<?php echo $row_com['idoc']; ?>">
                  No Disponible
                </a>
              <?php } ?>
            </td>
            <td>
              <?php 
                if(isset($row_com['razon_social'])){
                  echo "<p>".$row_com['razon_social']."</p>";
                }else{
                  echo "<p>No Disponible</p>";
                } 
              ?>
            </td>
            <td>
              <?php 
                if(isset($row_com['direccion_fiscal'])){
                  echo "<p>".$row_com['direccion_fiscal']."</p>";
                }else{
                  echo "<p>No Disponible</p>";
                } 
              ?>
            </td>          
            <td>
              <?php 
                if(isset($row_com['rfc'])){
                  echo "<p>".$row_com['rfc']."</p>";
                }else{
                  echo "<p>No Disponible</p>";
                } 
              ?>
            </td>
            <td>
              <a href="?COM&amp;detail&amp;idcom=<?php echo $row_com['idcom']; ?>&contact" class="btn btn-xs btn-info col-xs-6" data-toggle="tooltip" data-placement="top" title="Editar">
                <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
              </a>
              <form action="" method="post" name="formularioEliminar" ONSUBMIT="return preguntar();">
                <button class="btn btn-xs btn-danger col-xs-6" type="subtmit" value="Eliminar" data-toggle="tooltip" data-placement="top" title="Eliminar">
                  <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                </button>        
                <input type="hidden" value="COM eliminado correctamente" name="mensaje" />
                <input type="hidden" value="1" name="com_delete" />
                <input type="hidden" value="<?php echo $row_com['idcom']; ?>" name="idcom" />
              </form>
            </td>
          </tr>
          <?php }  ?>
          <? if($cont==0){?>
          <tr><td colspan="11" class="alert alert-info" role="alert">No se encontraron registros</td></tr>
          <? }?>
      </tbody>
    </table>
  </div>
</div>


<table>
  <tr>
  <td width="20"><?php if ($pageNum_com > 0) { // Show if not first page ?>
  <a href="<?php printf("%s?pageNum_com=%d%s", $currentPage, 0, $queryString_com); ?>">
  <span class="glyphicon glyphicon-fast-backward" aria-hidden="true"></span>
  </a>
  <?php } // Show if not first page ?></td>
  <td width="20"><?php if ($pageNum_com > 0) { // Show if not first page ?>
  <a href="<?php printf("%s?pageNum_com=%d%s", $currentPage, max(0, $pageNum_com - 1), $queryString_com); ?>">
  <span class="glyphicon glyphicon-backward" aria-hidden="true"></span>
  </a>
  <?php } // Show if not first page ?></td>
  <td width="20"><?php if ($pageNum_com < $totalPages_com) { // Show if not last page ?>
  <a href="<?php printf("%s?pageNum_com=%d%s", $currentPage, min($totalPages_com, $pageNum_com + 1), $queryString_com); ?>">
  <span class="glyphicon glyphicon-forward" aria-hidden="true"></span>
  </a>
  <?php } // Show if not last page ?></td>
  <td width="20"><?php if ($pageNum_com < $totalPages_com) { // Show if not last page ?>
  <a href="<?php printf("%s?pageNum_com=%d%s", $currentPage, $totalPages_com, $queryString_com); ?>">
  <span class="glyphicon glyphicon-fast-forward" aria-hidden="true"></span>
  </a>
  <?php } // Show if not last page ?></td>
  </tr>
</table>
<?php
mysql_free_result($com);
?>
