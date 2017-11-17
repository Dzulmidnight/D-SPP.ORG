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
                    <td colspan="2">
                      <p>
                        1. Nous <span style="color:red"><?php echo $oc['nombre'] ?></span>, comme Organisme de certification autorisé par SPP Global, avons l\'honneur de vous informer par la présente que le résultat de l\'évaluation SPP est <span style="color:red">positif</span>.
                      </p>
                      <p>
                        2. Pour terminer le processus, merci de lire de manière attentive les documents joints et ensuite de signer le Contrat d\'utilisation et l\'Accusé de réception. Veuillez compléter les informations de votre organisation et le représentant légal dans les textes marqués en rouge dans le contrat d\'utilisation.
                      </p>

                      <p>
                        3. Une fois que vous aurez signé les documenst indiqués, accédez à votre compte et téléchargez les documents pour qu\'ils soient revus par SPP Global.
                      </p>

                      <p>
                        4. Une fois que SPP Global aura confirmé au travers du système la réception des documents, il sera procédé à la délivrance du certificat.
                      </p>


                      <p>
                        <b>Adhésion</b>
                      </p>
                      <p>
                        Nous demandons le moyen le plus prudent de procéder au paiement de l\'adhésion à SPP Global. Selon le nombre de membres, le montant de l\'adhésion est de: <strong style="color:red">XXX USD</strong>. (Les coordonnées bancaires sont jointes, veuillez lire les Dispositions générales de paiement)
                      </p>
                      <p>
                        L\'organisation des petits producteurs (OPP) aura un délai maximum de 30 jours civils pour effectuer le paiement et télécharger votre reçu au D-SPP.
                      </p>
                      <p>
                        En cas de problème pour effectuer le paiement en temps voulu, veuillez informer le secteur Administration et Finance de SPP Global (adm@spp.coop).
                      </p>
                      <p>
                        Si vous ne parvenez pas à recevoir un paiement ou une communication de l\'OPP, vous enverrez malheureusement la suspension de votre certificat.
                      </p>

                      <hr>
                    </td>
                  </tr>

                  <tr>
                    <td><p><strong>DOCUMENTS ATTACHÉS / ATTACHED DOCUMENTS</strong></p></td>
                  </tr>
                    <tr style="color:#000">
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
                    <td colspan="2">
                      <h3>English Below</h3>
                    </td>
                  </tr>

                  <tr style="font-style: italic; color: #797979;">
                    <td colspan="2">
                      <p>
                        1. We, <span style="color:red"><?php echo $oc['nombre'] ?></span>, as a Certification Entity authorized by SPP Global, are pleased to inform you, by this means, that the SPP evaluation has been concluded with a <span style="color:red">positive</span> result.
                      </p>
                      <p>
                        2. In order to complete the process, the most careful request is to read the attached documents and subsequently sign the <span style="color:red">User´s Contract and Confirmation of Receipt</span> . Please complete the information of your organization and the legal representative in the texts marked in red within the Contract of Use.
                      </p>
                      <p>
                        3. Once you have signed the indicated documents, enter your account as Small Producers Organization (OPP) within the d-spp.org system and upload the documents so that they are reviewed by SPP Global.
                      </p>
                      <p>
                        4. Once SPP Global confirms the receipt of the documents in the SPP Global account through the System, the Certificate will be delivered.
                      </p>
                      <p>
                        <b>Membership</b>
                      </p>
                      <p>
                        We request the most careful way to <span style="color:red">proceed with the payment of membership to SPP Global</span>. According to the number of members, the amount of the membership is: <strong style="color:red">XXX USD</strong>. (The bank details are attached, please read the General Payment Provisions)
                      </p>

                      <p>
                        The Small Producers Organization (OPP) will have a maximum deadline of 30 calendar days to make the payment and load your receipt to the D-SPP.
                      </p>
                      <p>
                        In case of any problems to make the payment in due time, please inform the Administration and Finance area of SPP Global (adm@spp.coop).
                      </p>
                      <p>
                        Failure to receive payment or communication from the OPP, will unfortunately send the Suspension of your Certificate.
                      </p>

                      <hr>
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