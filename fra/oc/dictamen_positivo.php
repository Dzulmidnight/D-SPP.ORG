<?php 
require_once('../Connections/dspp.php'); 
mysql_select_db($database_dspp, $dspp);
 ?>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Avis positif</title>
</head>
<body>
<?php 
	if (isset($_GET['oc'])) {
		$row_oc = mysql_query("SELECT nombre, email1, email2 FROM oc WHERE idoc = $_GET[oc]", $dspp) or die(mysql_error());
		$oc = mysql_fetch_assoc($row_oc);
	}
	if(isset($_GET['opp'])){
		$nombre_opp = $_GET['opp'];
		
		if(isset($_GET['renovacion_positivo'])){
		?>
			<table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
			  <tbody>
			    <tr>
			      <th rowspan="2" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
			      <th scope="col" align="left" width="280"><p>Sujet: <span style="color:red">Notification d'avis</span></p></th>

			    </tr>
			    <tr>
			     <th scope="col" align="left" width="280"><p>Á: <span style="color:red"><?php echo $nombre_opp; ?></span></p></th>
			    </tr>

			    <tr>
			      <td colspan="2" style="text-align:justify">
			      	<p>
			      		1. Nous, <span style="color:red"><?php echo $oc['nombre'] ?></span>, Organisme de Certification autorisé par SPP Global, avons l'honneur de vous informer par la présente du résultat positif de l'évaluation SPP.
			      	</p>

			        <p>
			         	2. Pour terminer le processus, veuillez procéder au paiement de l'adhésion à SPP Global pour le montant indiqué (En annexe, les coordonnée bancaires, merci de lire les Conditions générales de paiement pour éviter le paiement d'intérêts). Une fois le paiement réalisé, merci d'accéder à votre compte et de télécharger le justificatif bancaire.
			        </p>
			        <p>
						3. Une fois que SPP Global aura confirmé au travers du système la réception du paiement dans le compte de SPP Global, il sera procédé à la délivrance de votre certificat.   
					</p>
					<p>
						<b style="color:red">
							Not : Le paiement de l'adhésion SPP vaut ratification de la signature du contrat d'utilisation, il n'est donc pas nécessaire de signer le contrat chaque année lorsque vous renouvelez votre certificat.
						</b>
			        </p>
			      </td>
			    </tr>
			    <tr>
			      <td><p><strong>Documents annexes</strong></p></td>
			    </tr>
			    <tr>
			      <td>
			        <ul>
			        	<li>Coordonnées bancaires</li>
			        </ul>
			      </td>
			    </tr>
			    <tr>
			      <td colspan="2" style="text-align:justify">
			      	<p>
			      		Après avoir réalisé le paiement, merci de télécharger le justificatif de paiement grâce au système D-SPP, en accédant à votre compte d'OPP (organisation de petits producteurs) à l'adresse suivante <a href="http://d-spp.org/fra/?OPP">http://d-spp.org/fra/?OPP</a>.
			      	</p>
			      </td>
			    </tr>
			    <tr>
			      <td colspan="2">
			        <p>En cas de doute ou de question, merci d'écrire à <span style="color:red"><?php echo $oc['email1']; if(isset($oc['email2'])){echo ' ó '.$oc['email2']; } ?></span></p>
			      </td>
			    </tr>
			  </tbody>
			</table>

		<?php
		}else if(isset($_GET['renovacion_negativo'])){
		?>
			<table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
			  <tbody>
			    <tr>
			      <th rowspan="2" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
			      <th scope="col" align="left" width="280"><p>Sujet: <span style="color:red">Notification d'avis</span></p></th>

			    </tr>
			    <tr>
			     <th scope="col" align="left" width="280"><p>Á: <span style="color:red"><?php echo $nombre_opp; ?></span></p></th>
			    </tr>

			    <tr>
			      <td colspan="2" style="text-align:justify">
			      	<p>
			      		Nous, <span style="color:red"><?php echo $oc['nombre'] ?></span>, Organisme de certification autorisé par SPP Global, avons le regret de vous informer par la présente que l'avis de l'évaluation pour la certification SPP est  négatif.
			      	</p>
			      </td>
			    </tr>
			    <tr>
			      <td colspan="2">
			        <p>En cas de doute ou de question, merci d'écrire à <span style="color:red"><?php echo $oc['email1']; if(isset($oc['email2'])){echo ' ó '.$oc['email2']; } ?></span></p>
			      </td>
			    </tr>
			  </tbody>
			</table>

		<?php
		}else if(isset($_GET['negativo'])){
		?>
			<table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
			  <tbody>
			    <tr>
			      <th rowspan="2" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
			      <th scope="col" align="left" width="280"><p>Sujet: <span style="color:red">Notification d'avis</span></p></th>

			    </tr>
			    <tr>
			     <th scope="col" align="left" width="280"><p>Á: <span style="color:red"><?php echo $nombre_opp; ?></span></p></th>
			    </tr>

			    <tr>
			      <td colspan="2" style="text-align:justify">
			      	<p>
			      		Nous, <span style="color:red"><?php echo $oc['nombre'] ?></span>, Organisme de certification autorisé par SPP Global, avons le regret de vous informer par la présente que l'avis de l'évaluation pour la certification SPP est  négatif.
			      	</p>
			      </td>
			    </tr>

			    <tr>
			      <td colspan="2">
			        <p>En cas de doute ou de question, merci d'écrire à <span style="color:red"><?php echo $oc['email1']; if(isset($oc['email2'])){echo ' ó '.$oc['email2']; } ?></span></p>
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
			      <th scope="col" align="left" width="280"><p>Sujet: <span style="color:red">Notification d'avis</span></p></th>

			    </tr>
			    <tr>
			     <th scope="col" align="left" width="280"><p>Á: <span style="color:red"><?php echo $nombre_opp; ?></span></p></th>
			    </tr>

			    <tr>
			      <td colspan="2" style="text-align:justify">
			      	<p>
			      		1. Nous, <span style="color:red"><?php echo $oc['nombre'] ?></span>, Organisme de Certification autorisé par SPP Global, avons l'honneur de vous informer par la présente du résultat positif de l'évaluation SPP.
			      	</p>
					<p>
						2. Pour terminer le processus, merci de lire de manière attentive les documents joints et ensuite de signer le <span style="color:red">Contrat d'utilisation et l'Accusé de réception</span>. Veuillez compléter les informations de votre organisation et le représentant légal dans les textes marqués en rouge dans le contrat d'utilisation.
					</p>
					<p>
						3. Une fois que vous aurez signé les documenst indiqués, accédez à votre compte et téléchargez les documents pour qu'ils soient revus par SPP Global.
					</p>

			        <p>
			        	4. Une fois que SPP Global aura confirmé au travers du système la réception des documents, il sera procédé à la délivrance du certificat.
			        </p>
			      </td>
			    </tr>
			    <tr>
			      <td><p><strong>Documents annexes</strong></p></td>
			    </tr>
			    <tr>
			      <td>
			        <ul>
			        	<li>Contrat d'utilisation</li>
			        	<li>Manuel SPP</li>
			        	<li>Accusé de réception</li>
			        	<li>Coordonnées bancaires</li>
			        </ul>
			      </td>
			    </tr>
			    <tr>
			      <td colspan="2" style="text-align:justify">
			        <p style="color:red"><strong>MEMBRESÍA SPP</strong></p>
			        <p>
			        	Adicionalmente se solicita de la manera más atenta, se proceda con el <span style="color:red">pago de membresìa a SPP Global</span>, de acuerdo al monto indicado de: <strong style="color:red;">$ xxx</strong>. (Se anexan los datos bancarios, favor de leer las Disposiciones Generales de Pago para evitar se generen intereses). Una vez que haya realizado el pago, favor de <span style="color:red">entrar a su cuenta y cargar el comprobante bancario</span>.
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
			        <p>En cas de doute ou de question, merci d'écrire à <span style="color:red"><?php echo $oc['email1']; if(isset($oc['email2'])){echo ' ó '.$oc['email2']; } ?></span></p>
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
            <th scope="col" align="left" width="280"><p>Sujet: <span style="color:red">Notification d'avis</span></p></th>

          </tr>
          <tr>
           <th scope="col" align="left" width="280"><p>Á: <span style="color:red"><?php echo $nombre_empresa; ?></span></p></th>
          </tr>

          <tr>
            <td colspan="2">
                <p>
					1. Nosotros <span style="color:red"><?php echo $oc['nombre'] ?></span> como Organismo de Certificación autorizado por SPP Global, nos complace informar por este medio que la evaluaciòn SPP fue concluida con resultado positivo
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
              <p>En cas de doute ou de question, merci d'écrire à <span style="color:red"><?php echo $oc['email1']; if(isset($oc['email2'])){echo ' ó '.$oc['email2']; } ?></span></p>
            </td>
          </tr>
        </tbody>
      </table>
	<?php
	}
 ?>


</body>
</html>