<?php
require_once('../Connections/dspp.php');
require_once('../Connections/mail.php');
mysql_select_db($database_dspp, $dspp);




function mayuscula($variable) {
  $variable = strtr(strtoupper($variable),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
  return $variable;
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="img/FUNDEPPO.png">
    <title>SPP GLOBAL | D-SPP</title>

    <!-- Bootstrap core CSS -->
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../bootstrap/css/bootstrap-theme.css" rel="stylesheet">
    <link href="../bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="../bootstrap/css/bootstrap-theme.min.css" rel="stylesheet">

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>


  </head>
<body>
<?php
$salida = "";
$query = "SELECT contactos.*, empresa.abreviacion AS 'abreviacion_empresa', empresa.pais FROM contactos INNER JOIN empresa ON contactos.idempresa = empresa.idempresa GROUP BY contactos.nombre ORDER BY contactos.nombre ASC";

if(isset($_POST['consulta'])){
  $q = $_POST['consulta'];
  $query = "SELECT contactos.*, empresa.abreviacion AS 'abreviacion_empresa', empresa.pais FROM contactos INNER JOIN empresa ON contactos.idempresa = empresa.idempresa WHERE contactos.nombre LIKE '%".$q."%' OR empresa.pais LIKE '%".$q."%' OR empresa.abreviacion LIKE '%".$q."%' GROUP BY contactos.nombre ORDER BY contactos.nombre ASC";
}
$resultado = mysql_query($query,$dspp) or die(mysql_error());
$total = mysql_num_rows($resultado);
$contador = 1;


?>
  <div class="col-md-12">
    <table class="table table-bordered table-condensed" style="font-size:11px;">
      <thead>
        <tr>
          <th colspan="28">CONTACTOS DE LAS EMPRESAS</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>#</td>
          <td class="info">País</td>
          <td class="info">Empresa</td>
          <td class="info">Email Empresa</td>
          <td class="info">Telefono Empresa</td>
          <td colspan="17">Solicitud</td>
          <td>id contactos</td>
          <td>Contactos</td>
          <td>Correo Empresa</td>
          <td>País</td>
          <td>Nombre</td>
          <td>Cargo</td>
          <td>Telefono</td>
          <td>Correo</td>
        </tr>
        <?php 
          $contador = 1;
          $query = "SELECT empresa.idempresa, empresa.nombre AS 'nombre_empresa', empresa.abreviacion, empresa.pais, empresa.email, empresa.telefono, contactos.idcontacto, contactos.nombre AS 'nombre_contacto', contactos.cargo, contactos.telefono1, contactos.telefono2, contactos.email1, contactos.email2, solicitud_registro.idsolicitud_registro, solicitud_registro.contacto1_nombre, solicitud_registro.contacto2_nombre, solicitud_registro.contacto1_cargo, solicitud_registro.contacto2_cargo, solicitud_registro.contacto1_email, solicitud_registro.contacto2_email, solicitud_registro.contacto1_telefono, solicitud_registro.contacto2_telefono, solicitud_registro.adm1_nombre, solicitud_registro.adm2_nombre, solicitud_registro.adm1_email, solicitud_registro.adm2_email, solicitud_registro.adm1_telefono, solicitud_registro.adm2_telefono FROM empresa LEFT JOIN solicitud_registro ON empresa.idempresa = solicitud_registro.idempresa LEFT JOIN contactos ON empresa.idempresa = contactos.idempresa";
          $consultar_empresa = mysql_query($query,$dspp) or die(mysql_error());

          while($datos_empresa = mysql_fetch_assoc($consultar_empresa)){
          ?>
          <tr>
            <td><?php echo $contador; ?></td>
            <td class="info"><?php echo $datos_empresa['pais']; ?></td>
            <td class="info"><?php echo $datos_empresa['nombre_empresa']; ?></td>
            <td class="info"><?php echo $datos_empresa['email'] ?></td>
            <td class="info"><?php echo $datos_empresa['telefono']; ?></td>

            <td class="sucecss"><?php echo $datos_empresa['idsolicitud_registro']; ?></td>
            <td class="sucecss"><?php echo $datos_empresa['contacto1_nombre'] ?></td>
            <td class="sucecss"><?php echo $datos_empresa['contacto1_cargo']; ?></td>
            <td class="sucecss"><?php echo $datos_empresa['contacto1_email']; ?></td>
            <td class="sucecss"><?php echo $datos_empresa['contacto1_telefono']; ?></td>

            <td class="warning"><?php echo $datos_empresa['contacto2_nombre'] ?></td>
            <td class="warning"><?php echo $datos_empresa['contacto2_cargo']; ?></td>
            <td class="warning"><?php echo $datos_empresa['contacto2_email']; ?></td>
            <td class="warning"><?php echo $datos_empresa['contacto2_telefono']; ?></td>


            <td><?php echo $datos_empresa['adm1_nombre'] ?></td>
            <td><?php echo 'ADMINSITRADOR'; ?></td>
            <td><?php echo $datos_empresa['adm1_email']; ?></td>
            <td><?php echo $datos_empresa['adm1_telefono']; ?></td>

            <td><?php echo $datos_empresa['adm2_nombre'] ?></td>
            <td><?php echo 'ADMINSITRADOR'; ?></td>
            <td><?php echo $datos_empresa['adm2_email']; ?></td>
            <td><?php echo $datos_empresa['adm2_telefono']; ?></td>
            <td class="danger"><?php echo $datos_empresa['idcontacto']; ?></td>
            <td class="danger"><?php echo $datos_empresa['nombre_contacto'] ?></td>
            <td class="danger"><?php echo $datos_empresa['cargo']; ?></td>
            <td class="danger"><?php echo $datos_empresa['email1']; ?></td>
            <td class="danger"><?php echo $datos_empresa['email2']; ?></td>
            <td class="danger"><?php echo $datos_empresa['telefono1']; ?></td>
            <td class="danger"><?php echo $datos_empresa['telefono2']; ?></td>

          </tr>
          <?php
          $contador++;
          }
         ?>

      </tbody>
    </table>    
  </div>

<?php
$contador2 = 1;
if($total > 0){
  $salida .= "
    <table class='table table-bordered table-condensed' style='font-size:12px;'>
      <thead>
        <tr>
          <th>#</th>
          <th>Empresa</th>
          <th>País</th>
          <th>Nombre</th>
          <th>Cargo</th>
          <th>Telefono(s)</th>
          <th>Correo(s)</th>
        </tr>
      </thead>
      <tbody>
  ";
  while ($fila = mysql_fetch_assoc($resultado)) {
    $salida .= 
    "<tr>
      <td>".$contador2."</td>
      <!-- ABREVIACIÓN EMPRESA -->
      <td><a href='?EMPRESAS&detail&idempresa=".$fila['idempresa']."'>".mayuscula($fila['abreviacion_empresa'])."</a></td>
      <!-- PAIS -->
      <td>".mayuscula($fila['pais'])."</td>
      <!-- NOMBRE DE CONTACTO -->
      <td>".'<a href="?EMPRESAS&detail&idempresa='.$fila['idempresa'].'&contacto='.$fila['idcontacto'].'">'.mayuscula($fila['nombre']).'</a>'."</td>
      <!-- CARGO -->
      <td>".mayuscula($fila['cargo'])."</td>
      <!-- TELEFONO -->
      <td>
        <b>Tel 1:</b> ".'<span style="color:red">'.$fila['telefono1'].'</span>'."
        <br>
        <b>Tel 2:</b> ".'<span style="color:#e67e22">'.$fila['telefono2'].'</span>'."
      </td>
      <!-- CORREO -->
      <td>
        <b>Correo 1:</b> <span style='color:red'>".$fila['email1']."</span>
        <br>
        <b>Correo 2:</b> <span style='color:#e67e22'>".$fila['email2']."</span>
      </td>
    </tr>"; 
    $contador2++;
  }
  $salida .= "
      </tbody>
    </table>
  ";
}else{
  $salida .= "<p class='alert alert-warning'>No se encontraron coincidencias</p>";
}
echo $salida;
 ?>
</body>
</html>
