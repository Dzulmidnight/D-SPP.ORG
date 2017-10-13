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



?>
  <div class="col-md-12">
    <table class="table table-bordered table-condensed" style="font-size:12px;">
      <thead>
        <tr>
          <th colspan="28">CONTACTOS DE LAS ORGANIZACIONES</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>#</td>
          <td class="info">País</td>
          <td class="info">Organización</td>
          <td class="info">Email Organización</td>
          <td class="info">Telefono Organización</td>
          <td colspan="17">Solicitud</td>
          <td>id contactos</td>
          <td>Contactos</td>
          <td>Correo Organización</td>
          <td>País</td>
          <td>Nombre</td>
          <td>Cargo</td>
          <td>Telefono</td>
          <td>Correo</td>
        </tr>
        <?php 
          $contador = 1;
          $query = "SELECT opp.idopp, opp.nombre AS 'nombre_opp', opp.abreviacion, opp.pais, opp.email, opp.telefono, contactos.idcontacto, contactos.nombre AS 'nombre_contacto', contactos.cargo, contactos.telefono1, contactos.telefono2, contactos.email1, contactos.email2, solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.contacto1_nombre, solicitud_certificacion.contacto2_nombre, solicitud_certificacion.contacto1_cargo, solicitud_certificacion.contacto2_cargo, solicitud_certificacion.contacto1_email, solicitud_certificacion.contacto2_email, solicitud_certificacion.contacto1_telefono, solicitud_certificacion.contacto2_telefono, solicitud_certificacion.adm1_nombre, solicitud_certificacion.adm2_nombre, solicitud_certificacion.adm1_email, solicitud_certificacion.adm2_email, solicitud_certificacion.adm1_telefono, solicitud_certificacion.adm2_telefono FROM opp LEFT JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp LEFT JOIN contactos ON opp.idopp = contactos.idopp ";
          $consultar_opp = mysql_query($query,$dspp) or die(mysql_error());

          while($datos_opp = mysql_fetch_assoc($consultar_opp)){
          ?>
          <tr>
            <td><?php echo $contador; ?></td>
            <td class="info"><?php echo $datos_opp['pais']; ?></td>
            <td class="info"><?php echo $datos_opp['nombre_opp'].' <span style="color:red">('.$datos_opp['idopp'].')</span>'; ?></td>
            <td class="info"><?php echo $datos_opp['email'] ?></td>
            <td class="info"><?php echo $datos_opp['telefono']; ?></td>

            <td class="sucecss"><?php echo $datos_opp['idsolicitud_certificacion']; ?></td>
            <td class="sucecss">C1_nombre <?php echo $datos_opp['contacto1_nombre'] ?></td>
            <td class="sucecss">C1_cargo <?php echo $datos_opp['contacto1_cargo']; ?></td>
            <td class="sucecss">C1_email <?php echo $datos_opp['contacto1_email']; ?></td>
            <td class="sucecss">C1_telefono <?php echo $datos_opp['contacto1_telefono']; ?></td>

            <td class="warning">C2_nombre <?php echo $datos_opp['contacto2_nombre'] ?></td>
            <td class="warning">C2_cargo <?php echo $datos_opp['contacto2_cargo']; ?></td>
            <td class="warning">C2_email <?php echo $datos_opp['contacto2_email']; ?></td>
            <td class="warning">C2_telefono <?php echo $datos_opp['contacto2_telefono']; ?></td>


            <td>AD1_nombre <?php echo $datos_opp['adm1_nombre'] ?></td>
            <td><?php echo 'ADMINSITRADOR'; ?></td>
            <td>ADM1_email <?php echo $datos_opp['adm1_email']; ?></td>
            <td><?php echo $datos_opp['adm1_telefono']; ?></td>

            <td>AD2_nombre <?php echo $datos_opp['adm2_nombre'] ?></td>
            <td><?php echo 'ADMINSITRADOR'; ?></td>
            <td>AD2_email <?php echo $datos_opp['adm2_email']; ?></td>
            <td>AD2_telefono <?php echo $datos_opp['adm2_telefono']; ?></td>
            <td class="danger"><?php echo $datos_opp['idcontacto']; ?></td>
            <td class="danger"><?php echo $datos_opp['nombre_contacto'] ?></td>
            <td class="danger"><?php echo $datos_opp['cargo']; ?></td>
            <td class="danger"><?php echo $datos_opp['email1']; ?></td>
            <td class="danger"><?php echo $datos_opp['email2']; ?></td>
            <td class="danger"><?php echo $datos_opp['telefono1']; ?></td>
            <td class="danger"><?php echo $datos_opp['telefono2']; ?></td>

          </tr>
          <?php
          $contador++;
          }
         ?>

      </tbody>
    </table>    
  </div>


</body>
</html>
