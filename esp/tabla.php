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

  $query_oc = "SELECT idoc, spp, abreviacion FROM oc WHERE idoc != 18";
  $consultar_oc = mysql_query($query_oc, $dspp) or die(mysql_error());

?>
  <div class="col-md-12">
    <table class="table table-bordered table-condensed" style="font-size:11px;">
      <thead>
        <tr>
          <th colspan="28">REPORTE GENERAL</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>#SPP</td>
          <td>ABREVIACIÓN</td>
        </tr>
        <?php 
        while($detalles_oc = mysql_fetch_assoc($consultar_oc)){
        ?>
          <tr>
            <td><?php echo $detalles_oc['spp']; ?></td>
            <td><?php echo $detalles_oc['abreviacion']; ?></td>
          </tr>
          <tr>
            <td colspan="2">
              <table class="table table-bordered">
                <tr>
                  <td>adsfadfa</td>
                  <td>adsfadfa</td>
                  <td>adsfadfa</td>
                  <td>adsfadfa</td>
                  <td>adsfadfa</td>
                </tr>
              </table>
            </td>
          </tr>
        <?php
        }
         ?>
      </tbody>
    </table>    
  </div>


</body>
</html>
