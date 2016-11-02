<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Dictamen Positivo</title>
</head>
<body>
<?php 
	if(isset($_GET['opp'])){
		$nombre_opp = $_GET['opp'];
		
		if(isset($_GET['renovacion'])){
		?>
			<table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
			  <tbody>
			    <tr>
			      <th rowspan="2" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
			      <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">NOTIFICACIÓN DE DICTAMEN</span></p></th>

			    </tr>
			    <tr>
			     <th scope="col" align="left" width="280"><p>Para: <span style="color:red"><?php echo $nombre_opp; ?></span></p></th>
			    </tr>

			    <tr>
			      <td colspan="2" style="text-align:justify">
			        <p>1. Nosotros (Nombre del OC) como Organismo de Certificación autorizado por SPP Global, nos complace informar por este medio que la evaluaciòn SPP fue concluida con resultado positivo.</p>
			        <p>
			          2. Para concluir el proceso, se solicita de la manera más atenta se proceda con el pago de membresìa a SPP Global, de acuerdo al monto indicado. (Se anexan los datos bancarios, favor de leer las Disposiciones Generales de Pago para evitar se generen intereses). Una vez que haya realizado el pago, favor de entrar a su cuenta y cargar el comprobante bancario.
			        </p>
			        <p>
						4. Una vez que SPP Global confirme a través del Sistema la recepción del pago en la cuenta de SPP Global, se procedera a hacer entrega del certificado.   
					</p>
					<p>
						<b style="color:red">
							NOTA: El pago de membresìa se considera una ratificación de la firma de Contrato de Uso por lo que no es necesario firmar el contrato cada año que renuevan su certificado.
						</b>
			        </p>
			      </td>
			    </tr>
			    <tr>
			      <td><p><strong>DOCUMENTOS ANEXOS</strong></p></td>
			    </tr>
			    <tr>
			      <td>
			        <ul>
			        	<li>Datos Bancarios</li>
			        </ul>
			      </td>
			    </tr>
			    <tr>
			      <td colspan="2" style="text-align:justify">
			        <p>
			          DESPUÉS DE REALIZAR EL PAGO POR FAVOR PROCEDA A CARGAR  EL <span style="color:red">COMPROBANTE DE PAGO</span> POR MEDIO DEL SISTEMA D-SPP, ESTO INGRESANDO EN SU CUENTA DE OPP(Organización de Pequeños Productores) EN LA SIGUIENTE DIRECCIÓN <a href="http://d-spp.org/esp/?OPP">http://d-spp.org/esp/?OPP</a>.
			        </p>
			      </td>
			    </tr>
			    <tr>
			      <td colspan="2">
			        <p>En caso de cualquier duda o aclaración por favor escribir a <span style="color:red">cert@spp.coop</span> o <span style="color:red">soporte@d-spp.org</span></p>
			      </td>
			    </tr>
			  </tbody>
			</table>

		<?php
		}else{
		?>
			<table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
			  <tbody>
			    <tr>
			      <th rowspan="2" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
			      <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">NOTIFICACIÓN DE DICTAMEN</span></p></th>

			    </tr>
			    <tr>
			     <th scope="col" align="left" width="280"><p>Para: <span style="color:red"><?php echo $nombre_opp; ?></span></p></th>
			    </tr>

			    <tr>
			      <td colspan="2" style="text-align:justify">
			        <p>1. Nosotros (Nombre del OC) como Organismo de Certificación autorizado por SPP Global, nos complace informar por este medio que la evaluaciòn SPP fue concluida con resultado positivo.</p>
			        <p>
			          2. Para concluir el proceso, se solicita de la manera más atenta leer los documentos anexos y posteriormente <span style="color:red">firmar el Contrato de Uso y Acuse de Recibo</span>.
			        </p>
			        <p>
			        	Una vez que haya firmado los documentos indicados, <span style="color:red">ingresar a su cuenta y cargar los documentos para que éstos sean revisados por SPP Global</span>.
			        </p>
			        <p>
			        	3. Una vez que SPP Global confirme a través del Sistema la recepción de los documentos y la recepciòn del pago en la cuenta de SPP Global, el Organismo de Certificaciòn hará entrega del Certificad
			        </p>
			      </td>
			    </tr>
			    <tr>
			      <td><p><strong>DOCUMENTOS ANEXOS</strong></p></td>
			    </tr>
			    <tr>
			      <td>
			        <ul>
			        	<li>Contrato de Uso</li>
			        	<li>Manual del SPP</li>
			        	<li>Acuse de Recibo</li>
			        	<li>Datos Bancarios</li>
			        </ul>
			      </td>
			    </tr>
			    <tr>
			      <td colspan="2" style="text-align:justify">
			        <p style="color:red"><strong>MEMBRESÍA SPP</strong></p>
			        <p>
			        	Adicionalmente se solicita de la manera más atenta, se proceda con el <span style="color:red">pago de membresìa a SPP Global</span>, de acuerdo al monto indicado de: <strong style="color:red;">'.$_POST['monto_membresia'].'</strong>. (Se anexan los datos bancarios, favor de leer las Disposiciones Generales de Pago para evitar se generen intereses). Una vez que haya realizado el pago, favor de <span style="color:red">entrar a su cuenta y cargar el comprobante bancario</span>.
			        </p>
			        <p>
			          LOS DATOS BANCARIOS SE ENCUENTRAN ANEXOS AL CORREO.
			        </p>
			        <p>
			          DESPUÉS DE REALIZAR EL PAGO POR FAVOR PROCEDA A CARGAR EL <span style="color:red">CONTRATO DE USO FIRMADO</span> ASÍ MISMO EL <span style="color:red">COMPROBANTE DE PAGO</span> POR MEDIO DEL SISTEMA D-SPP, ESTO INGRESANDO EN SU CUENTA DE OPP(Organización de Pequeños Productores) EN LA SIGUIENTE DIRECCIÓN <a href="http://d-spp.org/esp/?OPP">http://d-spp.org/esp/?OPP</a>.
			        </p>
			      </td>
			    </tr>
			    <tr>
			      <td colspan="2">
			        <p>En caso de cualquier duda o aclaración por favor escribir a <span style="color:red">cert@spp.coop</span> o <span style="color:red">soporte@d-spp.org</span></p>
			      </td>
			    </tr>
			  </tbody>
			</table>
		<?php
		}
	}
	if(isset($_GET['empresa'])){
		$nombre_empresa = $_GET['empresa'];
	?>
      <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;text-align:justify" border="0" width="650px">
        <tbody>
          <tr>
            <th rowspan="2" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
            <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">NOTIFICACIÓN DE DICTAMEN</span></p></th>

          </tr>
          <tr>
           <th scope="col" align="left" width="280"><p>Para: <span style="color:red"><?php echo $nombre_empresa; ?></span></p></th>
          </tr>

          <tr>
            <td colspan="2">
                <p>
					1. Nosotros (Nombre del OC) como Organismo de Certificación autorizado por SPP Global, nos complace informar por este medio que la evaluaciòn SPP fue concluida con resultado positivo
                </p>
                <p>
                  2. Para concluir el proceso, se solicita de la manera más atenta leer los documentos anexos y posteriormente <span style="color:red">firmar el Contrato de Uso y Acuse de Recibo</span>.
                </p>
                <p>
                	Una vez que haya firmado los documentos indicados, <span style="color:red">ingresar a su cuenta y cargar los documentos para que éstos sean revisados por SPP Global</span>. 
                </p>
                <p>
                	4. Una vez que SPP Global confirme a través del Sistema la recepción de los documentos , se procedera a hacer entrega del certificado.
                </p>
            </td>
          </tr>
          <tr>
            <td><p><strong>Documentos Anexos</strong></p></td>
          </tr>
          <tr>
            <td>
              <ul>
	        	<li>Contrato de Uso</li>
	        	<li>Manual SPP</li>
	        	<li>Acuse de Recibo</li>
              </ul>
            </td>
          </tr>
          <tr>
            <td colspan="2" style="text-align:justify">
                <p>
                 PARA PODER CARGAR LOS DOCUMENTOS DEBE DE INGRESAR EN SU CUENTA DE EMPRESA EN LA SIGUIENTE DIRECCIÓN <a href="http://d-spp.org/esp/?COM">http://d-spp.org/esp/?COM</a>.
                </p>
            </td>
          </tr>
          <tr>
            <td colspan="2" style="padding-top:10px;">
              <p>En caso de cualquier duda o aclaración por favor escribir a <span style="color:red">cert@spp.coop</span> o <span style="color:red">soporte@d-spp.org</span></p>
            </td>
          </tr>
        </tbody>
      </table>
	<?php
	}
 ?>


</body>
</html>