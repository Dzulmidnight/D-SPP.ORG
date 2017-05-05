  <!------>
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

    if(isset($_POST['agregar_lista']) && $_POST['agregar_lista'] == 1){
      $insertSQL = sprintf("INSERT INTO lista_contactos (nombre, descripcion) VALUES (%s, %s)",
        GetSQLValueString(strtoupper($_POST['nombre_actual']), "text"), 
        GetSQLValueString($_POST['descripcion_lista'], "text"));
      $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

      $mensaje = "Nueva Lista de Contactos Creada";
    }
    if(isset($_POST['agregar_contacto']) && $_POST['agregar_contacto'] == 1){
     $insertSQL = sprintf("INSERT INTO contactos (lista_contactos, nombre, cargo, telefono1, telefono2, email1, email2, pais, direccion) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
      GetSQLValueString($_POST['idlista'], "int"),
      GetSQLValueString($_POST['nombre'], "text"),
      GetSQLValueString($_POST['cargo'], "text"),
      GetSQLValueString($_POST['telefono1'], "text"),
      GetSQLValueString($_POST['telefono2'], "text"),
      GetSQLValueString($_POST['email1'], "text"), 
      GetSQLValueString($_POST['email2'], "text"),
      GetSQLValueString($_POST['pais'], "text"),
      GetSQLValueString($_POST['direccion'], "text"));
     $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
     $mensaje = "Nuevo Contacto Agregado";
    }
    if(isset($_POST['actualizar_nombre_lista']) && $_POST['actualizar_nombre_lista'] == 1){
      $updateSQL = sprintf("UPDATE lista_contactos SET nombre = %s WHERE idlista_contactos = %s",
        GetSQLValueString($_POST['titulo_lista'], "text"),
        GetSQLValueString($_POST['idlista_contactos'], "int"));
      $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());
      $mensaje = 'Titulo de lista actualizado';
    }
    if(isset($_POST['eliminar_contacto']) && $_POST['eliminar_contacto'] == 1){
      $deleteSQL = sprintf("DELETE FROM contactos WHERE idcontacto = %s",
        GetSQLValueString($_POST['idcontacto'], "int"));
      $eliminar = mysql_query($deleteSQL, $dspp) or die(mysql_error());

      $mensaje = "Contacto Eliminado";
    }
    if(isset($_POST['notificaciones']) && $_POST['notificaciones'] == 1){
      $updateSQL = sprintf("UPDATE lista_contactos SET notificaciones = %s WHERE idlista_contactos = %s",
        GetSQLValueString($_POST['permitir_notificaciones'], "int"),
        GetSQLValueString($_POST['idlista_contactos'], "int"));
      $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

      $mensaje = "Se ha activado las notificaciones de la lista";
    }

    $row_lista_contactos = mysql_query("SELECT * FROM lista_contactos", $dspp) or die(mysql_error());
    $total_listas = mysql_num_rows($row_lista_contactos);

    $row_paises = mysql_query("SELECT * FROM paises", $dspp) or die(mysql_error());
    ?>
      <?php 
      if(isset($mensaje)){
      ?>
        <div class="col-md-12 alert alert-success alert-dismissible" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <?php echo $mensaje; ?>
        </div>
      <?php
      }
      ?>

<div class="row" style="margin-left:-40px;">
  <div class="col-md-12">
    <!-- INICIA MENU OPCIONES -->
    <div class="btn-group" role="group" aria-label="...">
      <a href="?CORREO&select=notificaciones" <?php if($_GET['select'] == 'notificaciones'){ echo 'class="btn btn-sm btn-primary"'; }else{ echo 'class="btn btn-sm btn-default"'; } ?>>Notificaciones</a>
      <a href="?CORREO&select=listas" <?php if($_GET['select'] == 'listas'){ echo 'class="btn btn-sm btn-primary"'; }else{ echo 'class="btn btn-sm btn-default"'; } ?>>Listas de Contactos</a>
    </div>
    <!-- FIN MENU OPCIONES -->
    <div class="row">
    <?php 
    if($_GET['select'] == 'notificaciones'){
      include('notificaciones.php');
    }else if($_GET['select'] == 'listas'){
      include('listas.php');
    }
    ?>

  <!------>
  </div>
</div>