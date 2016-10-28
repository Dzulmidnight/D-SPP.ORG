<?php 
	if(isset($_GET['opp'])){
		$nombre_opp = $_GET['opp'];
	?>
		<table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
		  <tbody>
		    <tr>
		      <th rowspan="2" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
		      <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">Dictamen Positivo - SPP</span></p></th>

		    </tr>
		    <tr>
		     <th scope="col" align="left" width="280"><p>Para: <span style="color:red"><?php echo $nombre_opp; ?></span></p></th>
		    </tr>

		    <tr>
		      <td colspan="2" style="text-align:justify">
		        <p>RECIBAN USTEDES UN CORDIAL Y ATENTO SALUDO, ASÍ COMO EL DESEO DE ÉXITO EN TODAS Y CADA UNA DE SUS ACTIVIDADES.</p>
		        <p>
		          NOS COMPLACE INFOMAR QUE FUE CONCLUIDA LA EVALUACION PARA LA CERTIFICACION SPP.  EL DICTAMEN FUE POSITIVO. 
		        </p>
		        <p>                              
		          PARA CONCLUIR EL PROCESO SE SOLICITA <span style="color:red">FIRMAR EL CONTRATO DE USO</span>, UNA VEZ QUE HAYA <span style="color:red">LEIDO EL MANUAL DEL SPP</span>.
		        </p>
		      </td>
		    </tr>
		    <tr>
		      <td><p><strong>DOCUMENTOS ANEXOS</strong></p></td>
		    </tr>
		    <tr>
		      <td>
		        <ul>
		        	<li>Acuse de Recibo SPP</li>
		        	<li>Contrato de Uso SPP</li>
		        	<li>Manual SPP</li>
		        	<li>Datos Bancarios</li>
		        </ul>
		      </td>
		    </tr>
		    <tr>
		      <td colspan="2" style="text-align:justify">
		        <p style="color:red"><strong>MEMBRESÍA SPP</strong></p>
		        <p>
		          ADICIONALMENTE SE SOLICITA DE LA MANERA MÁS ATENTA REALIZAR EL PAGO CORRESPONDIENTE A LA MEMBRESIA SPP POR EL IMPORTE DE: <span style="color:red;">Se debe fijar en el sistema</span>
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
	if(isset($_GET['empresa'])){
		$nombre_empresa = $_GET['empresa'];
	?>
      <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
        <tbody>
          <tr>
            <th rowspan="2" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
            <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">Dictamen Positivo - SPP</span></p></th>

          </tr>
          <tr>
           <th scope="col" align="left" width="280"><p>Para: <span style="color:red"><?php echo $nombre_empresa; ?></span></p></th>
          </tr>

          <tr>
            <td colspan="2">
                <p>RECIBAN USTEDES UN CORDIAL Y ATENTO SALUDO, ASÍ COMO EL DESEO DE ÉXITO EN TODAS Y CADA UNA DE SUS ACTIVIDADES.</p>
                <p>
                  NOS COMPLACE INFOMAR QUE FUE CONCLUIDA LA EVALUACION PARA LA CERTIFICACION SPP.  EL DICTAMEN FUE POSITIVO. 
                </p>
                <p>                              
                  PARA CONCLUIR EL PROCESO SE SOLICITA <span style="color:red">FIRMAR EL CONTRATO DE USO</span>, UNA VEZ QUE HAYA <span style="color:red">LEIDO EL MANUAL DEL SPP</span>.
                </p>
            </td>
          </tr>
          <tr>
            <td><p><strong>Documentos Anexos</strong></p></td>
          </tr>
          <tr>
            <td>
              <ul>
	        	<li>Acuse de Recibo SPP</li>
	        	<li>Contrato de Uso SPP</li>
	        	<li>Manual SPP</li>
              </ul>
            </td>
          </tr>
          <tr>
            <td colspan="2" style="text-align:justify">
                <p>
                  DESPUÉS DE FIRMAR EL <span style="color:red">CONTRATO DE USO</span> PROCEDA A CARGAR EL MISMO POR MEDIO DEL SISTEMA D-SPP, ESTO INGRESANDO EN SU CUENTA DE EMPRESA EN LA SIGUIENTE DIRECCIÓN <a href="http://d-spp.org/esp/?COM">http://d-spp.org/esp/?COM</a>.
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
