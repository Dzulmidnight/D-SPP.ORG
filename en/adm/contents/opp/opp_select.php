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

if(isset($_POST['opp_delete'])){
	$query=sprintf("delete from opp where idopp = %s",GetSQLValueString($_POST['idopp'], "text"));
	$ejecutar=mysql_query($query,$dspp) or die(mysql_error());
}

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_opp = 40;
$pageNum_opp = 0;
if (isset($_GET['pageNum_opp'])) {
  $pageNum_opp = $_GET['pageNum_opp'];
}
$startRow_opp = $pageNum_opp * $maxRows_opp;

mysql_select_db($database_dspp, $dspp);

if(isset($_GET['query'])){
  $query_opp = "SELECT *, opp.nombre AS 'nombreOPP', status.idstatus, status.nombre AS 'nombreStatus' FROM opp LEFT JOIN status ON opp.estado = status.idstatus WHERE idoc = $_GET[query] ORDER BY opp.nombre ASC";
    $query_total = "SELECT idopp, COUNT(idopp) AS 'totalOPP' FROM opp WHERE idoc = $_GET[query]";

}else if(isset($_POST['filtroPalabra']) && $_POST['filtroPalabra'] == "1"){
  $palabraClave = $_POST['palabraClave'];

  $query_opp = "SELECT *, opp.nombre AS 'nombreOPP', status.idstatus, status.nombre AS 'nombreStatus' FROM opp LEFT JOIN status ON opp.estado = status.idstatus  WHERE (idf LIKE '%$palabraClave%') OR (opp.nombre LIKE '%$palabraClave%') OR (opp.abreviacion LIKE '%$palabraClave%') OR (sitio_web LIKE '%$palabraClave%') OR (email LIKE '%$palabraClave%') OR (pais LIKE '%$palabraClave%') OR (razon_social LIKE '%$palabraClave%') OR (direccion_fiscal LIKE '%$palabraClave%') OR (rfc LIKE '%$palabraClave%') ORDER BY opp.idopp ASC";

  $query_total = "SELECT COUNT(idopp) AS 'totalPalabra' FROM opp WHERE idf LIKE '%$palabraClave%' OR (nombre LIKE '%$palabraClave%') OR (abreviacion LIKE '%$palabraClave%') OR (sitio_web LIKE '%$palabraClave%') OR (email LIKE '%$palabraClave%') OR (pais LIKE '%$palabraClave%') OR (razon_social LIKE '%$palabraClave%') OR (direccion_fiscal LIKE '%$palabraClave%') OR (rfc LIKE '%$palabraClave%') ORDER BY idopp ASC"; 

}/*else if(isset($_POST['filtroPais']) && $_POST['filtroPais'] == "2" && $_POST['busquedaPais'] != NULL){
  $pais = $_POST['busquedaPais'];
  $query_opp = "SELECT * FROM opp WHERE pais LIKE '%$pais%'";
}*/else{
	$query_opp = "SELECT *, opp.nombre AS 'nombreOPP', status.idstatus, status.nombre AS 'nombreStatus' FROM opp LEFT JOIN status ON opp.estado = status.idstatus ORDER BY opp.idopp ASC";
  $query_total = "SELECT idopp, COUNT(idopp) AS 'totalOPP' FROM opp";
}

$ejecutarTotal = mysql_query($query_total,$dspp) or die(mysql_error());
$totalOPP = mysql_fetch_assoc($ejecutarTotal);

$query_limit_opp = sprintf("%s LIMIT %d, %d", $query_opp, $startRow_opp, $maxRows_opp);
$opp = mysql_query($query_limit_opp, $dspp) or die(mysql_error());

//$row_opp = mysql_fetch_assoc($opp);

if (isset($_GET['totalRows_opp'])) {
  $totalRows_opp = $_GET['totalRows_opp'];
} else {
  $all_opp = mysql_query($query_opp);
  $totalRows_opp = mysql_num_rows($all_opp);
}
$totalPages_opp = ceil($totalRows_opp/$maxRows_opp)-1;

$queryString_opp = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_opp") == false && 
        stristr($param, "totalRows_opp") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_opp = "&" . htmlentities(implode("&", $newParams));
  }
}


$queryString_opp = sprintf("&totalRows_opp=%d%s", $totalRows_opp, $queryString_opp);
?>

<script language="JavaScript"> 
function preguntar(){ 
    if(!confirm('¿Estas seguro de eliminar el registro?')){ 
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
    <div class="col-xs-6">
      <h4 class="well">
        Consulta OPP  | 
        <span> 
          <?php 
            if(isset($totalOPP['totalPalabra'])){
          ?>
            OPP(s) Encontrados: <strong style="color:red"><?php echo $totalOPP['totalPalabra']; ?></strong> 
          <?php
            }else{
          ?>
            Total OPP(s): <strong style="color:red"><?php echo $totalOPP['totalOPP']; ?></strong>
          <?php
            }
           ?> 
        </span>
      </h4>
    </div>


  </div>
  <hr>

<div class="panel panel-default">
  <div class="panel-heading">
      <!--Lista OPP(s) | <a href="#" onclick="javascript:document.formPDF.submit()" title="Exportar PDF"><img src="../../img/pdf.png" alt=""></a> 
      <a href="#" onclick="javascript:document.formXLS.submit()" title="Exportar EXCEL"><img src="../../img/excel.png" alt=""></a> 

    <form action="../../reporte.php" method="POST" name="formPDF" style="display:inline">
      <input type="text" name="reportePDF" value="1">
      <input type="text" name="consultaPDF" value="<?php echo $query_opp; ?>">
    </form>
    <form action="../../reporte.php" method="POST" name="formXLS" style="display:inline"> 
      <input type="text" name="reporteExcel" value="2" >
      <input type="text" name="consultaXLS" value="<?php echo $query_opp; ?>">
    </form>-->


  </div>
 
  <div class="panel-body">
  <table class="table table-condensed table-bordered table-hover">
    <thead style="font-size:12px;">
      <tr>
        <th class="text-center">IDF</th>
        <th class="text-center">Estatus Certificado</th>
        <th class="text-center" style="width:200px;">Nombre</th>
        <th class="text-center">Abreviación</th>
        <!--<th class="text-center">Sitio WEB</th>-->
        <!--<th class="text-center">Email OPP</th>-->
        <th class="text-center">País</th>
        <th class="text-center">OC</th>
        <!--<th class="text-center">Razón social</th>
        <th class="text-center">Dirección fiscal</th>
        <th class="text-center">RFC</th>-->
        <!--<th class="text-center">Eliminar</th>-->
        <th class="text-center">Acciones</th>
      </tr>      
    </thead>
    <tbody style="font-size:11px">
      <?php $contador=0; while ($row_opp = mysql_fetch_assoc($opp)) {$contador++; ?>
        <tr>
          <td>
            <a class="btn btn-primary btn-sm" style="width:100%" href="?OPP&amp;detail&amp;idopp=<?php echo $row_opp['idopp']; ?>&contact">Consultar<br>
              <?php echo $row_opp['idf']; ?>
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
              if(isset($row_opp['nombreOPP'])){
                echo "<p class='text-center'>".$row_opp['nombreOPP']."</p>";
              }else{
                echo "<p class='alert alert-danger'>No Disponible</p>";
              } 
            ?>
          </td>
          <td>
            <?php 
              if(isset($row_opp['abreviacion'])){
                echo "<p class='text-center'>".$row_opp['abreviacion']."</p>";
              }else{
                echo "<p class='alert alert-danger'>No Disponible</p>";
              } 
            ?>
          </td>          
          <!--<td>
            <h6 class="text-center">
              <?php 
                if(isset($row_opp['sitio_web'])){
                  echo "<p class='text-center'>".$row_opp['sitio_web']."</p>";
                }else{
                  echo "<p class='alert alert-danger'>No Disponible</p>";
                } 
              ?>
            </h6>
          </td>-->
          <!--<td>
            <h6 class="text-center">
              <?php 
                if(isset($row_opp['email'])){
                  echo "<p class='text-center'>".$row_opp['email']."</p>";
                }else{
                  echo "<p class='alert alert-danger'>No Disponible</p>";
                } 
              ?>
            </h6>
          </td>-->
          <td>
            <?php echo $row_opp['pais']; ?>
          </td>
          <td>      
            <?
              $query_topp = "SELECT abreviacion FROM oc where idoc='".$row_opp['idoc']."'";
              $topp = mysql_query($query_topp, $dspp) or die(mysql_error());
              $row_topp = mysql_fetch_assoc($topp);
            ?>
            <?php if(isset($row_topp['abreviacion'])){ ?>
              <a style="width:100%" href="?OC&amp;detail&amp;idoc=<?php echo $row_opp['idoc']; ?>&contact">
                <?php  echo "<p class='alert alert-success' style='padding:7px;'>".$row_topp['abreviacion']."</p>"; ?>
              </a>
            <?php }else{ ?>    
                No Disponible
            <?php } ?>
          </td>
          <!--<td>
            <h6 class="text-center">
              <?php 
                if(isset($row_opp['razon_social'])){
                  echo "<p class='text-center'>".$row_opp['razon_social']."</p>";
                }else{
                  echo "<p class='alert alert-danger'>No Disponible</p>";
                } 
              ?>
            </h6>
          </td>-->
          <!--<td>
            <h6 class="text-center">
              <?php 
                if(isset($row_opp['direccion_fiscal'])){
                  echo "<p class='text-center'>".$row_opp['direccion_fiscal']."</p>";
                }else{
                  echo "<p class='alert alert-danger'>No Disponible</p>";
                } 
              ?>
            </h6>
          </td>-->         
          <!--<td>
            <h6 class="text-center">
              <?php 
                if(isset($row_opp['rfc'])){
                  echo "<p class='text-center'>".$row_opp['rfc']."</p>";
                }else{
                  echo "<p class='alert alert-danger'>No Disponible</p>";
                } 
              ?>
            </h6>
          </td>-->
          <!--<td>
              <form action="" method="post">
              <button class="btn btn-danger btn-sm" type="submit" value="Eliminar">
                <span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Eliminar
              </button>        
              <input type="hidden" value="OPP eliminado correctamente" name="mensaje" />
              <input type="hidden" value="1" name="opp_delete" />
              <input type="hidden" value="<?php echo $row_opp['idopp']; ?>" name="idopp" />
              </form>
          </td>-->
          <td>
            <!--<button class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="Visualizar">
              <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
            </button>-->
            <a href="<?php echo "?OPP&detail&idopp=$row_opp[idopp]&contact"; ?>" class="btn btn-xs btn-info col-xs-6" data-toggle="tooltip" data-placement="top" title="Editar">
              <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
            </a>
            <form action="" method="post" name="formularioEliminar" ONSUBMIT="return preguntar();">
              <button class="btn btn-xs btn-danger col-xs-6" type="subtmit" value="Eliminar" data-toggle="tooltip" data-placement="top" title="Eliminar">
                <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
              </button>        
              <input type="hidden" value="OPP eliminado correctamente" name="mensaje" />
              <input type="hidden" value="1" name="opp_delete" />
              <input type="hidden" value="<?php echo $row_opp['idopp']; ?>" name="idopp" />
            </form>

            <!--<button class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="Eliminar">
               <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
            </button>-->
            
            
           
          </td>
        </tr>
        <?php }  ?>
        <? if($contador==0){?>
        <tr><td colspan="11" class="alert alert-info" role="alert">No se encontraron registros</td></tr>
        <? }?>
    </tbody>
  </table>


  </div>
</div>



<table>
  <tr>
  <td width="20"><?php if ($pageNum_opp > 0) { // Show if not first page ?>
  <a href="<?php printf("%s?pageNum_opp=%d%s", $currentPage, 0, $queryString_opp); ?>">
  <span class="glyphicon glyphicon-fast-backward" aria-hidden="true"></span>
  </a>
  <?php } // Show if not first page ?></td>
  <td width="20"><?php if ($pageNum_opp > 0) { // Show if not first page ?>
  <a href="<?php printf("%s?pageNum_opp=%d%s", $currentPage, max(0, $pageNum_opp - 1), $queryString_opp); ?>">
  <span class="glyphicon glyphicon-backward" aria-hidden="true"></span>
  </a>
  <?php } // Show if not first page ?></td>
  <td width="20"><?php if ($pageNum_opp < $totalPages_opp) { // Show if not last page ?>
  <a href="<?php printf("%s?pageNum_opp=%d%s", $currentPage, min($totalPages_opp, $pageNum_opp + 1), $queryString_opp); ?>">
  <span class="glyphicon glyphicon-forward" aria-hidden="true"></span>
  </a>
  <?php } // Show if not last page ?></td>
  <td width="20"><?php if ($pageNum_opp < $totalPages_opp) { // Show if not last page ?>
  <a href="<?php printf("%s?pageNum_opp=%d%s", $currentPage, $totalPages_opp, $queryString_opp); ?>">
  <span class="glyphicon glyphicon-fast-forward" aria-hidden="true"></span>
  </a>
  <?php } // Show if not last page ?></td>
  </tr>
</table>
<?php
mysql_free_result($opp);
?>
