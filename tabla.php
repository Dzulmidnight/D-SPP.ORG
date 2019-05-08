<!DOCTYPE html>
<html>

  <head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">


  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
  </head>
<body>
<div class="container container-fluid">
  <div class="row">
    <?php //if (isset($opp)): ?>
    <div class="col-md-12 bg-black">
          <table style="width:538px; background-color:#ecf0f1" cellspacing="0" cellpadding="0" align="center">
          <tbody>
            <tr>
              <td style="height:65px; background-color:#171a21; border-bottom:1px solid #4d4b48">
              </td>
            </tr>
            <tr>
              <td>
              <table style="padding-left:5px; padding-right:5px; padding-bottom:10px" width="470" cellspacing="0" cellpadding="0" border="0" align="center">
              <tbody>
              <tr>
                <td style="padding-top:32px; text-align: center;">
                  <span style="font-size: 24px; color:#27ae60; font-family: Arial,Helvetica,sans-serif,serif,&quot;EmojiFont&quot;; font-weight: bold; text-align: center;">Nuevo Registro / New Register</span><br>
                </td>
              </tr>
              <tr>
                <td style="padding-top:12px; font-size:17px; color:#27ae60; font-family:Arial,Helvetica,sans-serif; text-align: justify; ">
                  Felicidades, se han registrado sus datos correctamente. A continuación se muestra su #SPP y su contraseña, necesarios para poder iniciar sesión: <a style="color: green;" href="<?php echo base_url('Opp/Iniciar_sesion'); ?>">D-SPP</a>, una vez que haya iniciado sesión se le recomienda cambiar su contraseña en la sección Mi Cuenta, en dicha sección se encuentran sus datos los cuales pueden ser modificados en caso de ser necesario.
                </td>
              </tr>
              <tr>
                <td style="padding-top:24px; padding-bottom:24px">
                  <div>
                    <span class="text-center" style="font-size: 24px; color: rgb(102, 192, 244); font-family: Arial,Helvetica,sans-serif,serif,&quot;EmojiFont&quot;; font-weight: bold;">
                      Información de registro:
                    </span> <br>
                    <ul style="list-style: none; color:#e74c3c; ">
                      <li><b>#SPP: </b><?php echo $opp->spp; ?></li>
                      <li><b>Password:</b> <?php echo $opp->password; ?></li>
                    <li><b>Pais / Country:</b> <?php echo $opp->nombrePais; ?><br/></li>
                    <li><b>Nombre / Name:</b> <?php echo $opp->nombre; ?><br/></li>
                    <li><b>Abreviación / Short name:</b> <?php echo $opp->abreviacion; ?> <br/></li>
                    </ul>
                  </div>
                </td>
              </tr>
              <tr bgcolor="#121a25">
                <td style="padding:20px; font-size:12px; line-height:17px; color:#e74c3c; font-family:Arial,Helvetica,sans-serif">
                   Si usted no sabe el motivo de este correo, contacte a: soporte@d-spp.org
                </td>
              </tr>
              <tr>
                <td style="font-size:12px; color:#6d7880; padding-top:16px; padding-bottom:60px">
                  
                </td>
              </tr>
            </tbody>
            </table>
            </td>
          </tr>
          <tr style="background-color:#000000">
            <td style="padding:12px 24px">
              <table cellspacing="0" cellpadding="0">
                <tbody>
                  <tr>
                    <td width="92"> </td>
                    <td style="font-size:11px; color:#595959; padding-left:12px">
                    </td>
                  </tr>
                </tbody>
              </table>
            </td>
          </tr>
          </tbody>
          </table>
    </div>  
    <?php //endif ?>
    <?php //if (isset($oc)): ?>
      <div class="col-md-12 bg-black">
          <table style="width:538px; background-color:#393836" cellspacing="0" cellpadding="0" align="center">
          <tbody>
            <tr>
              <td style="height:65px; background-color:#171a21; border-bottom:1px solid #4d4b48">
              </td>
            </tr>
            <tr>
              <td bgcolor="#17212e">
              <table style="padding-left:5px; padding-right:5px; padding-bottom:10px" width="470" cellspacing="0" cellpadding="0" border="0" align="center">
              <tbody>
              <tr bgcolor="#17212e">
                <td style="padding-top:32px; text-align: center;">
                  <span style="font-size: 24px; color:#D60C0AFF; font-family: Arial,Helvetica,sans-serif,serif,&quot;EmojiFont&quot;; font-weight: bold; text-align: center;">Nuevo Registro / New Register</span><br>
                </td>
              </tr>
              <tr>
                <td style="padding-top:12px; font-size:17px; color:#c6d4df; font-family:Arial,Helvetica,sans-serif; text-align: justify; ">
                  Felicidades, se han registrado sus datos correctamente. A continuación se muestra su #SPP y su contraseña, necesarios para poder iniciar sesión: <a style="color: green;" href="<?php echo base_url('Oc/Iniciar_sesion'); ?>">D-SPP</a>, una vez que haya iniciado sesión se le recomienda cambiar su contraseña en la sección Mi Cuenta, en dicha sección se encuentran sus datos los cuales pueden ser modificados en caso de ser necesario.
                </td>
              </tr>
              <tr>
                <td style="padding-top:24px; padding-bottom:24px">
                  <div>
                    <span class="text-center" style="font-size: 24px; color: rgb(102, 192, 244); font-family: Arial,Helvetica,sans-serif,serif,&quot;EmojiFont&quot;; font-weight: bold;">
                      Información de registro:
                    </span> <br>
                    <ul style="list-style: none; color:#c6d4df; ">
                      <li><b>#SPP: </b><?php echo $oc->spp; ?></li>
                      <li><b>Password:</b> <?php echo $oc->password; ?></li>
                    <li><b>Pais / Country:</b> <?php echo $oc->nombrePais; ?><br/></li>
                    <li><b>Nombre / Name:</b> <?php echo $oc->nombre; ?><br/></li>
                    <li><b>Abreviación / Short name:</b> <?php echo $oc->abreviacion; ?> <br/></li>
                    </ul>
                  </div>
                </td>
              </tr>
              <tr bgcolor="#121a25">
                <td style="padding:20px; font-size:12px; line-height:17px; color:#c6d4df; font-family:Arial,Helvetica,sans-serif">
                   Si usted no sabe el motivo de este correo, contacte a: soporte@d-spp.org
                </td>
              </tr>
              <tr>
                <td style="font-size:12px; color:#6d7880; padding-top:16px; padding-bottom:60px">
                  
                </td>
              </tr>
            </tbody>
            </table>
            </td>
          </tr>
          <tr style="background-color:#000000">
            <td style="padding:12px 24px">
              <table cellspacing="0" cellpadding="0">
                <tbody>
                  <tr>
                    <td width="92"> </td>
                    <td style="font-size:11px; color:#595959; padding-left:12px">
                    </td>
                  </tr>
                </tbody>
              </table>
            </td>
          </tr>
          </tbody>
          </table>
    </div>
    <?php //endif ?>
  </div>
</div>
</body>
<script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
</html>