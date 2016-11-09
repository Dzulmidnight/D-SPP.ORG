<?php  
require_once('../Connections/dspp.php');
require_once('../Connections/mail.php');
mysql_select_db($database_dspp, $dspp);

if(isset($_POST['enviar']) && $_POST['enviar'] == 1){
  $asunto = "D-SPP | RECORDATORIO: PERIODO DE OBJECIÓN";

  $mensaje = '
    <html>
      <head>
        <meta charset="utf-8">
      </head>
      <body>
      
                <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;text-align:justify" border="0" width="650px">
                  <tbody>
                    <tr>
                      <th rowspan="2" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                      <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">RECORDATORIO: PERIODO DE OBJECIÓN</span></p></th>

                    </tr>


                    <tr>
                      <td colspan="2" >
                        <p>
                          Este mensaje es envio del sistema D-SPP, recordandole que el Periodo de Objeción de la Empresa: <b style="color:red">Esperanza Cafe</b> está por concluir, una vez finalizado el periodo debe de cargar la Resolución de Objeción.
                        </p>
                      </td>
                    </tr>

		              <tr style="width:100%">
		                <td colspan="2">
		                  	<table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;" border="1" width="650px">

			                    <tr style="font-size: 12px; text-align:center; background-color:#dff0d8; color:#3c763d;" height="50px;">
			                      <td style="text-align:center">Tipo / Type</td>
			                      <td style="text-align:center">Nombre de la organización/Organization name</td>
			                      <td style="text-align:center">País / Country</td>
			                      <td style="text-align:center">Organismo de Certificación / Certification Entity</td>

			                      <td style="text-align:center">Tipo de solicitud / Kind of application</td>
			                      <td style="text-align:center">Inicio Periodo de Objeción</td>
			                      <td style="text-align:center">Fin período de objeción</td>
			                    </tr>
			                    <tr style="font-size:12px">
			                      <td>Empresa</td>
			                      <td>Esperanza Cafe</td>
			                      <td>Francia</td>
			                      <td>BCS</td>
			                      <td>Registro</td>
			                      <td>25/10/2016</td>
			                      <td>09/11/2016</td>
			                    </tr>
		                  	</table>
		                </td>
		              </tr>


                    <tr>
                      <td colspan="2">
                        <p>En caso de cualquier duda o aclaración por favor escribir a <span style="color:red">soporte@d-spp.org</span></p>
                      </td>
                    </tr>
                  </tbody>
                </table>

      </body>
    </html>
  ';

  $mail->AddAddress('cert@spp.coop');
  $mail->Subject = utf8_decode($asunto);
  $mail->Body = utf8_decode($mensaje);
  $mail->MsgHTML(utf8_decode($mensaje));
  if($mail->Send()){
  	$mail->ClearAddresses();
  	echo "<script>alert('se envio')</script>";
  }else{
  	$mail->ClearAddresses();
  	echo "<script>alert('no se envio')</script>";
  }
}

?>
        <html>
          <head>
            <meta charset="utf-8">
          </head>
          <body>
                <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;text-align:justify" border="0" width="650px">
                  <tbody>
                    <tr>
                      <th rowspan="2" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                      <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">RECORDATORIO: PERIODO DE OBJECIÓN</span></p></th>

                    </tr>


                    <tr>
                      <td colspan="2" >
                        <p>
                          Este mensaje es envio del sistema D-SPP, recordandole que el Periodo de Objeción de la Empresa: <b style="color:red">Esperanza Cafe</b> está por concluir, una vez finalizado el periodo debe de cargar la Resolución de Objeción.
                        </p>
                      </td>
                    </tr>

		              <tr style="width:100%">
		                <td colspan="2">
		                  	<table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;" border="1" width="650px">

			                    <tr style="font-size: 12px; text-align:center; background-color:#dff0d8; color:#3c763d;" height="50px;">
			                      <td style="text-align:center">Tipo / Type</td>
			                      <td style="text-align:center">Nombre de la organización/Organization name</td>
			                      <td style="text-align:center">País / Country</td>
			                      <td style="text-align:center">Organismo de Certificación / Certification Entity</td>

			                      <td style="text-align:center">Tipo de solicitud / Kind of application</td>
			                      <td style="text-align:center">Inicio Periodo de Objeción</td>
			                      <td style="text-align:center">Fin período de objeción</td>
			                    </tr>
			                    <tr style="font-size:12px">
			                      <td>Empresa</td>
			                      <td>Esperanza Cafe</td>
			                      <td>Francia</td>
			                      <td>BCS</td>
			                      <td>Registro</td>
			                      <td>25/10/2016</td>
			                      <td>09/11/2016</td>
			                    </tr>
		                  	</table>
		                </td>
		              </tr>


                    <tr>
                      <td colspan="2">
                        <p>En caso de cualquier duda o aclaración por favor escribir a <span style="color:red">soporte@d-spp.org</span></p>
                      </td>
                    </tr>
                  </tbody>
                </table>
                <form action="" method="POST">
                	<button type="submit" name="enviar" value="1">Enviar</button>
                </form>

          </body>
          </html>