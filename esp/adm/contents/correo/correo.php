<style>
table {
    width:100%;
}
table, th, td {
    border: 1px solid black;
    border-collapse: collapse;
}
th, td {
    padding: 5px;
    text-align: left;
}
table#t01 tr:nth-child(even) {
    background-color: #eee;
}
table#t01 tr:nth-child(odd) {
   background-color:#fff;
}
table#t01 th {
    background-color: black;
    color: white;
}
</style>
<div class="row" style="margin-left:-40px;">
  <div class="col-md-12">
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

    <div class="row">

      <div class="col-md-3">
        <h4>Lista de Contactos Actuales</h4>
        <a href="?CORREO&add&nueva">
          <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Nueva Lista de Contactos
        </a>
        <a href="?CORREO&add" class="btn btn-sm btn-primary" style="width:100%;">Consultar Todos</a>
        <div class="dropdown">
          <button class="btn btn-default dropdown-toggle" style="width:100%;" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
            Admisnitradores
            <span class="caret"></span>
          </button>
          <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
            <li><a href=""><span class="glyphicon glyphicon-user"></span> Agregar Contacto(s)</a></li>
          </ul>
        </div>

        <div class="dropdown">
          <button class="btn btn-default dropdown-toggle" style="width:100%;" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
            Organizaciones (OPP)
            <span class="caret"></span>
          </button>
          <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
            <li>
              <a href="?CORREO&add&editar_lista=OPP&name=OPP" ><span class="glyphicon glyphicon-search"></span> Consultar Contacto(s)</a></a>
            </li>
          </ul>

        </div>

        <div class="dropdown">
          <button class="btn btn-default dropdown-toggle" style="width:100%;" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
            Empresas
            <span class="caret"></span>
          </button>
          <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
            <li>
              <a href="?CORREO&add&editar_lista=EMPRESA&name=EMPRESA" ><span class="glyphicon glyphicon-search"></span> Consultar Contacto(s)</a></a>
            </li>
          </ul>

        </div>
        <?php 
        if($total_listas){
          while($listas = mysql_fetch_assoc($row_lista_contactos)){
          ?>
            <div class="dropdown">
              <button class="btn btn-default dropdown-toggle" style="width:100%;" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                <?php echo $listas['nombre']; ?>
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                <li>
                  <a href="?CORREO&add&editar_lista=<?php echo $listas['idlista_contactos'] ?>&name=<?php echo $listas['nombre']; ?>" ><span class="glyphicon glyphicon-search"></span> Consultar Contacto(s)</a></a>
                </li>
                <li>
                  <a href="?CORREO&add&lista=<?php echo $listas['idlista_contactos'] ?>"><span class="glyphicon glyphicon-user"></span> Agregar Contacto(s)</a></a>
                </li>
              </ul>
            </div>  
          <?php
          }
        }
        ?>
      </div>
      <div class="col-md-9">
        <?php
        if(isset($_GET['lista']) && !empty($_GET['lista'])){
        ?>
          <div class="panel panel-warning">
            <div class="panel-heading">
              <h3 class="panel-title">Formulario Nuevo Contacto</span></h3>
            </div>
            <div class="panel-body">
              <form action="" method="POST">
                <div class="form-group">
                  <label for="nombre">Nombre</label>
                  <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Escriba el nombre del contacto" required>
                </div>
                <div class="form-group">
                  <label for="cargo">Cargo</label>
                  <input type="text" class="form-control" name="cargo" id="cargo" placeholder="Escriba el cargo del contacto">
                </div>
                <div class="form-group">
                  <label for="cargo">País</label>
                  <select name="pais" id="pais" class="form-control">
                    <option value="">Selecciona un País</option>
                    <?php 
                    while($pais = mysql_fetch_assoc($row_paises)){
                      echo "<option value='".utf8_encode($pais['nombre'])."'>".utf8_encode($pais['nombre'])."</option>";
                    }
                     ?>
                  </select>
                </div>
                <div class="form-group">
                  <label for="direccion">Dirección</label>
                  <textarea class="form-control" name="direccion" id="direccion"></textarea>
                  
                </div>


                <div class="form-group">
                  <label for="telefono1">Telefono 1</label>
                  <input type="text" class="form-control" name="telefono1" id="telefono1" placeholder="Escriba el teléfono">
                </div>
                <div class="form-group">
                  <label for="telefono2">Telefono 2</label>
                  <input type="text" class="form-control" name="telefono2" id="telefono2" placeholder="Escriba el teléfono">
                </div>
                <div class="form-group">
                  <label for="email1">Email 1</label>
                  <input type="text" class="form-control" name="email1" id="email1" placeholder="Escriba el email">
                </div>
                <div class="form-group">
                  <label for="email2">Email 2</label>
                  <input type="text" class="form-control" name="email2" id="email2" placeholder="Escriba el email">
                </div>
                <input type="hidden" name="idlista" value="<?php echo $_GET['lista']; ?>">
                <button type="submit" class="btn btn-success" style="width:100%" name="agregar_contacto" value="1"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Agregar Contacto</button>
              </form>
            </div>
          </div>

        <?php
        }else if(isset($_GET['editar_lista'])){
          if($_GET['editar_lista'] == 'OPP'){
            $row_lista = mysql_query("SELECT * FROM contactos WHERE idopp IS NOT NULL", $dspp) or die(mysql_error());
          }else if($_GET['editar_lista'] == 'EMPRESA'){
            $row_lista = mysql_query("SELECT * FROM contactos WHERE idempresa IS NOT NULL", $dspp) or die(mysql_error());
          }else{
            $row_lista = mysql_query("SELECT * FROM contactos WHERE lista_contactos = $_GET[editar_lista]", $dspp) or die(mysql_error());
            $row_notificaciones = mysql_query("SELECT notificaciones FROM lista_contactos WHERE idlista_contactos = $_GET[editar_lista]");
            $permisos = mysql_fetch_assoc($row_notificaciones);
          }

        ?>
          <table class="table table-bordered table-striped table-condensed">
            <thead>
              <tr>
                <th colspan="6" class="info">CONTACTOS AGREGADOS A LA LISTA: <span style="color:red"><?php echo $_GET['name']; ?></span></th>
                <th colspan="2" style="text-align:center; font-size:10px">
                  <p>¿Enviar Notificaciones de Intención?</p>
                  <form action="" method="POST">
                      <label class="radio-inline" style="font-size:12px;">
                        <input type="radio" name="permitir_notificaciones"  id="inlineRadio1" value="1" onChange='this.form.submit();' <?php if(isset($permisos['notificaciones'])){ echo 'checked'; } ?>> SI
                      </label>
                      <label class="radio-inline" style="font-size:12px;">
                        <input type="radio" name="permitir_notificaciones"  id="inlineRadio2" value="0" onChange='this.form.submit();' <?php if(!isset($permisos['notificaciones'])){ echo 'checked'; } ?>> NO
                      </label>
                      <input type="hidden" name="idlista_contactos" value="<?php echo $_GET['editar_lista']; ?>">
                      <input type="hidden" name="notificaciones" value="1">
                  </form>
                </th>
              </tr>
              <tr style="font-size:12px;">
                <th style="text-align:center">Nº</th>
                <th style="text-align:center">Nombre</th>
                <th style="text-align:center">Cargo</th>
                <th style="text-align:center">País</th>
                <th style="text-align:center">Dirección</th>
                <th style="text-align:center">Telefono(s)</th>
                <th style="text-align:center">Email(s)</th>
              </tr>
            </thead>
            <tbody style="font-size:12px;">
              <?php
              $contador = 1;
              while($lista = mysql_fetch_assoc($row_lista)){
              ?>
                <form action="" method="POST">
                  <tr>
                    <td><?php echo $contador; ?></td>
                    <td><?php echo $lista['nombre']; ?></td>
                    <td><?php echo $lista['cargo']; ?></td>
                    <td><?php echo $lista['pais']; ?></td>
                    <td><?php echo $lista['direccion']; ?></td>
                    <td>
                      <?php 
                      echo "Telefono 1: <span style='color:red'>$lista[telefono1]</span><br>";
                      echo "Telefono 2: <span style='color:red'>$lista[telefono2]</span><br>";                      
                       ?>
                    </td>
                    <td>
                      <?php 
                      echo "Email 1: <span style='color:red'>$lista[email1]</span><br>";
                      echo "Email 2: <span style='color:red'>$lista[email2]</span><br>";
                       ?>
                    </td>
                    <td>
                      <button class="btn btn-sm btn-danger" data-toggle="tooltip" title="Eliminar Contacto" type="submit" onclick="return confirm('¿Está seguro ?, los datos se eliminaran permanentemente');" name="eliminar_contacto" value="1"><span aria-hidden="true" class="glyphicon glyphicon-trash"></span></button>

                      <a href="" class="btn btn-sm btn-info" data-toggle="tooltip" title="Editar Contacto"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
                      <input type="hidden" name="idcontacto" value="<?php echo $lista['idcontacto']; ?>">
                    </td>
                  </tr>
                </form>
              <?php
              $contador++;
              }
               ?>
            </tbody>
          </table>
        <?php
        }else if(isset($_GET['nueva'])){
        ?>
          <div class="panel panel-info">
            <div class="panel-heading">
              <h3 class="panel-title">Formulario Nueva Lista de Contactos</h3>
            </div>
            <div class="panel-body">
              <form action="" method="POST">
                <div class="form-group">
                  <label for="nombre_actual">Nombre de la Lista</label>
                  <input type="text" class="form-control" name="nombre_actual" id="nombre_actual" placeholder="Ingrese el nombre de la lista" required>
                </div>
                <div class="form-group">
                  <label for="descripcion_lista">Descripción de la Lista</label>
                  <textarea class="form-control" name="descripcion_lista" id="descripcion_lista"></textarea>
                </div>
                <button type="submit" class="btn btn-success" style="width:100%" name="agregar_lista" value="1"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Crear Nueva Lista</button>
              </form>
            </div>
          </div>
        <?php
        }else{
        ?>
        <?php 
        $row_correos = mysql_query("SELECT contactos.idcontacto, contactos.idopp, contactos.idempresa, contactos.nombre, contactos.cargo, contactos.telefono1, contactos.telefono2, contactos.email1, contactos.email2, opp.abreviacion AS 'abreviacion_opp', empresa.abreviacion AS 'abreviacion_empresa' FROM contactos LEFT JOIN opp ON contactos.idopp = opp.idopp LEFT JOIN empresa ON contactos.idempresa = empresa.idempresa", $dspp) or die(mysql_error());
        ?>
          <table class="table table-bordered table-condensed table-striped">
            <thead style="font-size:12px;">
              <tr>
                <th colspan="3" style="text-align:center;font-size:14px;">Lista de Correos</th>
                <th colspan="2">
                  <div class="col-lg-12">
                      <div class="input-group">
                        <input type="text" class="form-control" placeholder="Buscar ...">
                        <span class="input-group-btn">
                          <button class="btn btn-success" type="button">Buscar!</button>
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
            <tbody style="font-size:11px;">
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
                  <?php echo "Correo 1: <span style='color:red'>".$correos['email1']."</span>"; ?>
                  <br>
                  <?php echo "Correo 2: <span style='color:red'>".$correos['email2']."</span>"; ?>
                </td>
                <td>
                  <?php echo "Telefono 1: <span style='color:red'>".$correos['telefono1']."</span>"; ?>
                  <br>
                  <?php echo "Telefono 2: <span style='color:red'>".$correos['telefono2']."</span>"; ?>
                </td>
              </tr>
            <?php
            $contador++;
            }
             ?>
            </tbody>
          </table>    
        <?php
        }
         ?>

      </div>

    </div>

  <!------>
  </div>
</div>



