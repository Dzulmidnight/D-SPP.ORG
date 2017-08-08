<?php 
require_once('../Connections/dspp.php'); 
require_once('../Connections/mail.php');

//error_reporting(E_ALL ^ E_DEPRECATED);
mysql_select_db($database_dspp, $dspp);

if (!isset($_SESSION)) {
  session_start();
  
  $redireccion = "../index.php?ADM";

  if(!$_SESSION["autentificado"]){
    header("Location:".$redireccion);
  }
}
if (!function_exists("GetSQLValueString")) {
  function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
  {
    if (PHP_VERSION < 6) {
      $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
    }

    $theValue = function_exissts("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

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
?>
<div class="row">
  <!---- INICIA MENU LISTA DE CORREOS ---->
  <div class="col-md-3">
    <h4>Lista Correos D-SPP</h4>
    <div class="list-group">
      <a href="#" class="list-group-item disabled">
        ¿Correos que desea consultar?
      </a>
      <div class="checkbox list-group-item">
        <label>
          <input type="checkbox" value="">
          Todos
        </label>
      </div>
      <div class="checkbox list-group-item">
        <label>
          <input type="checkbox" value="">
          OPP
        </label>
      </div>
      <div class="checkbox list-group-item">
        <label>
          <input type="checkbox" value="">
          EMPRESAS
        </label>
      </div>


    </div>

  </div>
  <!---- TERMINA MENU LISTA DE CORREOS ---->

  <!---- INICIA SECCIÓN LISTADO DE CORREOS ---->
  <div class="col-md-9">
    <?php 
    $row_correos = mysql_query("SELECT contactos.idcontacto, contactos.idopp, contactos.idempresa, contactos.nombre, contactos.cargo, contactos.telefono1, contactos.telefono2, contactos.email1, contactos.email2, opp.abreviacion AS 'abreviacion_opp', empresa.abreviacion AS 'abreviacion_empresa' FROM contactos LEFT JOIN opp ON contactos.idopp = opp.idopp LEFT JOIN empresa ON contactos.idempresa = empresa.idempresa", $dspp) or die(mysql_error());
    ?>
      <table class="table table-bordered">
        <thead>
          <tr>
            <th colspan="3">Lista de Correos</th>
            <th colspan="2">
              <div class="col-lg-12">
                  <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search for...">
                    <span class="input-group-btn">
                      <button class="btn btn-default" type="button">Go!</button>
                    </span>
                  </div><!-- /input-group -->
                </div><!-- /.col-lg-6 -->
            </th>
          </tr>
          <tr>
            <th>Nº</th>
            <th>Nombre</th>
            <th>Cargo</th>
            <th>Correo(s)</th>
            <th>Telefono(s)</th>
          </tr>
        </thead>
        <tbody style="font-size:12px;">
        <?php 
        $contador = 1;
        while($correos = mysql_fetch_assoc($row_correos)){
        ?>
          <tr>
            <td><?php echo $contador; ?></td>
            <td>
              <?php 
              if(isset($correos['idopp'])){
                echo '<a href="?OPP&detail&idopp='.$correos['idopp'].'&contacto='.$correos['idcontacto'].'">'.$correos['nombre'].'</a>';
              }else if(isset($correos['idempresa'])){
                echo '<a href="?EMPRESAS&detail&idempresa='.$correos['idempresa'].'&contacto='.$correos['idcontacto'].'">'.$correos['nombre'].'</a>';
              }else{
                echo $correos['nombre'];
              }
               ?>
            </td>
            <td>
              <?php 
              if(isset($correos['idopp'])){
                echo 'Organización: <a href="?OPP&detail&idopp='.$correos['idopp'].'">'.$correos['abreviacion_opp']."</a>";
              }else if(isset($correos['idempresa'])){
                echo 'Empresa: <a href="?EMPRESAS&detail&idempresa='.$correos['idempresa'].'">'.$correos['abreviacion_empresa']."</a>";
              }
               ?>
              <br>
              Cargo: <?php echo $correos['cargo']; ?>
            </td>
            <td>
              <?php echo "Correo 1:".$correos['email1']; ?>
              <br>
              <?php echo "Correo 2:".$correos['email2']; ?>
            </td>
            <td>
              <?php echo "Telefono 1: ".$correos['telefono1']; ?>
              <br>
              <?php echo "Telefono 2: ".$correos['telefono2']; ?>
            </td>
          </tr>
        <?php
        $contador++;
        }
         ?>
        </tbody>
      </table>
  </div>
  <!---- INICIA SECCIÓN LISTADO DE CORREOS ---->


</div>