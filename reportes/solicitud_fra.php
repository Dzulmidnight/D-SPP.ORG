<?php 
	require_once('../mpdf/mpdf.php');
	require_once('../en/Connections/dspp.php');
	  mysql_select_db($database_dspp, $dspp);

	$idsolicitud_certificacion = $_POST['idsolicitud_certificacion'];



	$query = "SELECT solicitud_certificacion.*, oc.idoc, oc.nombre AS 'nombre_oc', opp.spp AS 'spp_opp', opp.nombre AS 'nombre_opp', opp.direccion_oficina, opp.pais, opp.email AS 'email_opp', opp.sitio_web, opp.telefono AS 'telefono_opp', opp.rfc, opp.ciudad AS 'ciudad_opp', porcentaje_productoVentas.organico, porcentaje_productoVentas.comercio_justo, porcentaje_productoVentas.spp, porcentaje_productoVentas.sin_certificado FROM solicitud_certificacion INNER JOIN oc ON solicitud_certificacion.idoc = oc.idoc LEFT JOIN opp ON solicitud_certificacion.idopp = opp.idopp LEFT JOIN porcentaje_productoVentas ON solicitud_certificacion.idsolicitud_certificacion = porcentaje_productoVentas.idsolicitud_certificacion WHERE solicitud_certificacion.idsolicitud_certificacion = $idsolicitud_certificacion";
	//$query = "SELECT formato_cliente.*, cliente.* FROM formato_cliente INNER JOIN cliente ON formato_cliente.idcliente = cliente.idcliente WHERE idsolicitud_certificacion = $idsolicitud_certificacion";
	$consultar = mysql_query($query,$dspp) or die(mysql_error());
	$solicitud = mysql_fetch_assoc($consultar);

	if($solicitud['tipo_procedimiento'] == 'DOCUMENTAL "ACORTADO"'){
		$color1 = "background-color:#3498db";
	}
	if($solicitud['tipo_procedimiento'] == 'DOCUMENTAL "NORMAL"'){
		$color2 = "background-color:#3498db";
	}
	if($solicitud['tipo_procedimiento'] == 'COMPLETO "IN SITU"'){
		$color3 = "background-color:#3498db";
	}
	if($solicitud['tipo_procedimiento'] == 'COMPLETO "A DISTANCIA"'){
		$color4 = "background-color:#3498db";
	}


	$alcance = '';
	if($solicitud['produccion']){
		$alcance .= 'PRODUCCIÓN - PRODUCTION<br>';
	}
	if($solicitud['procesamiento']){
		$alcance .= 'PROCESAMIENTO - PROCESSING<br>';
	}
	if($solicitud['exportacion']){
		$alcance .= 'EXPORTACIÓN - TRADING<br>';
	}


	$html = '
      <div>
        <table border="1" style="padding:0px;margin:0px;">
			<tr>
				<td style="text-align:left;margin-bottom:0px;font-size:9px;">
			        <table border="1">
						<tr>
							<td>DATE DE REALISATION</td>
							<td><h3>'.date("d/m/Y", $solicitud['fecha_registro']).'</h3></td>
						</tr>
						<tr>
							<td>TYPE DE DEMANDE</td>
							<td style="background-color:#3498db;text-align:center"><b>'.$solicitud['tipo_solicitud'].'</b></td>
						</tr>
			        </table>
				</td>
				<td style="text-align:right;font-size:9px;">
			        <table border="1">
						<tr>
							<td colspan="3" style="background-color:#B8D186"><b>CODE D’IDENTIFICATION SPP (#SPP):</b></td>
							<td colspan="2" style="background-color:#B8D186;"><h3>'.$solicitud['spp_opp'].'</h3></td>
						</tr>
						<tr style="background-color:#bdc3c7;">

							<td style="text-align:left">PROCEDURE DE CERTIFICATION:</td>
							<td style="text-align:center;'.$color1.';">DOSSIER "SIMPLIFIE"</td>
							<td style="text-align:center;'.$color2.';">DOSSIER "NORMAL"</td>
							<td style="text-align:center;'.$color3.';">COMPLET "IN SITU"</td>
							<td style="text-align:center;'.$color4.';">COMPLET "A DISTANCE"</td>
						</tr>
			        </table>
				</td>
			</tr>
        </table>
      </div>

      <div>
		<table border="1" style="font-size:11px;">
			<tr>
				<td colspan="8" style="text-align:center;background-color:#B8D186"><h3>INFORMATIONS GENERALES </h3></td>
			</tr>
			<tr>
				<td colspan="8" style="text-align:left">
			        <div>DENOMINATION SOCIALE COMPLETE DE L’ORGANISATION DE PETITS PRODUCTEURS:</div>
			        <div class="respuesta"><b>'.$solicitud['nombre_opp'].'</b></div>
				</td>
			</tr>
			<tr>
				<td colspan="7" style="text-align:left">
			        <div>ADRESSE COMPLETE DU SIEGE SOCIAL (RUE, VILLE, COMPLEMENT D’ADRESSE, CODE POSTAL, REGION) :</div>
			        <div class="respuesta"><b>'.$solicitud['direccion_oficina'].'</b></div>
				</td>
				<td colspan="1" style="text-align:left">
			        <div>PAYS:</div>
			        <div class="respuesta"><b>'.$solicitud['pais'].'</b></div>		
				</td>
			</tr>
			<tr>
				<td colspan="8" style="text-align:left">
			        <div>ADRESSE MAIL:</div>
			        <div class="respuesta"><b>'.$solicitud['email_opp'].'</b></div>
				</td>
			</tr>
			<tr>
				<td colspan="4" style="text-align:left">
			        <div>SITE WEB:</div>
			        <div class="respuesta"><b>'.$solicitud['sitio_web'].'</b></div>
				</td>
				<td colspan="4" style="text-align:left">
			        <div>TELEPHONE (INDICATIF + NUMERO):</div>
			        <div class="respuesta"><b>'.$solicitud['telefono_opp'].'</b></div>		
				</td>
			</tr>
			<tr>
				<td colspan="8" style="text-align:left">
			        <div>
			        	INFORMATIONS FISCALES (INFORMATIONS POUR LA FACTURATION, DOMICILIATION, REGISTRE DU COMMERCE, VILLE, PAYS, ETC) :
			        	<hr>
			        </div>

			        <div class="respuesta">DOMICILIATION: <b>'.$solicitud['direccion_fiscal'].'</b></div>
			        <div class="respuesta">RFC: <b>'.$solicitud['rfc'].'</b></div>
			        <div class="respuesta">RUC: <b>'.$solicitud['ruc'].'</b></div>
			        <div class="respuesta">VILLE: <b>'.$solicitud['ciudad_opp'].'</b></div>
			        <div class="respuesta">PAYS: <b>'.$solicitud['pais'].'</b></div>
				</td>
			</tr>
			<tr>
				<td colspan="4" style="text-align:left">
			        <div>PERSONNE(S) A CONTACTER :</div>
			        <div class="respuesta"><b>'.$solicitud['contacto1_nombre'].'</b></div>
			        <div class="respuesta"><b>'.$solicitud['contacto2_nombre'].'</b></div>

			        <div>ADRESSE MAIL DES PERSONNES A CONTACTER:</div>
			        <div class="respuesta"><b>'.$solicitud['contacto1_email'].'</b></div>
			        <div class="respuesta"><b>'.$solicitud['contacto2_email'].'</b></div>


			    </td>
			    <td colspan="4" style="text-align:left">
			        <div>FONCTION(S) :</div>
			        <div class="respuesta"><b>'.$solicitud['contacto1_cargo'].'</b></div>
			        <div class="respuesta"><b>'.$solicitud['contacto2_cargo'].'</b></div>

			        <div>TELEPHONES DES PERSONNES A CONTACTER :</div>
			        <div class="respuesta"><b>'.$solicitud['contacto1_telefono'].'</b></div>
			        <div class="respuesta"><b>'.$solicitud['contacto2_telefono'].'</b></div>
				</td>

			</tr>
			<tr>
				<td colspan="4" style="text-align:left">
			        <div>RESPONSABLE DU SERVICE ADMINISTRATIF :</div>
			        <div class="respuesta"><b>'.$solicitud['adm1_nombre'].'</b></div>
			        <div class="respuesta"><b>'.$solicitud['adm2_nombre'].'</b></div>


			        <div>ADRESSE MAIL DU SERVICE ADMINISTRATIF :</div>
			        <div class="respuesta"><b>'.$solicitud['adm1_email'].'</b></div>
			        <div class="respuesta"><b>'.$solicitud['adm2_email'].'</b></div>


			    </td>
			    <td colspan="4" style="text-align:left">
			        <div>TELEPHONE DU RESPONSABLE DU SERVICE ADMINISTRATIF:</div>
			        <div class="respuesta"><b>'.$solicitud['adm1_telefono'].'</b></div>
			        <div class="respuesta"><b>'.$solicitud['adm2_telefono'].'</b></div>


				</td>
			</tr>
			<tr>
				<td colspan="2" style="text-align:left">NOMBRE DE MEMBRES PRODUCTEURS:</td>
				<td class="respuesta" colspan="2" style="text-align:center"><b>'.$solicitud['resp1'].'</b></td>
				<td colspan="2" style="text-align:left">NOMBRE DE MEMBRES PRODUCTEURS DU (DES) PRODUIT(S) A INCLUIRE DANS LA CERTIFICATION :</td>
				<td class="respuesta" colspan="2" style="text-align:center"><b>'.$solicitud['resp2'].'</b></td>

			</tr>
			<tr>
				<td colspan="2" style="text-align:left">VOLUME(S) DE PRODUCTION TOTALE PAR PRODUIT (UNITE DE MESURE) :</td>
				<td class="respuesta" colspan="2" style="text-align:center"><b>'.$solicitud['resp3'].'</b></td>
				<td colspan="2" style="text-align:left">TAILLE MAXIMALE DE L’UNITE DE PRODUCTION PAR PRODUCTEUR DU (DES) PRODUIT(S) A INCLURE DANS LA CERTIFICATION :</td>
				<td class="respuesta" colspan="2" style="text-align:center"><b>'.$solicitud['resp4'].'</b></td>
			</tr>
			

			<tr>
				<td colspan="8" style="text-align:center;background-color:#B8D186"><h3>INFORMATIONS SUR LE TYPE D’OPERATION</h3></td>
			</tr>
			<tr>
				<td  style="text-align:left" colspan="8">
			        <div>1.	INDIQUEZ-S’IL S’AGIT D’UNE ORGANISATION DE PETITS PRODUCTEURS DE 1er, 2eme, 3eme OU 4eme NIVEAU, AINSI QUE LE NOMBRE D’OPP DE 3eme, 2eme OU 1er NIVEAU ET LE NOMBRE DE COMMUNAUTES, DE ZONES OU DE GROUPES DE TRAVAIL DONT VOUS DISPOSEZ : </div>
			        <div class="respuesta"><b>'.$solicitud['op_preg1'].'</b></div>	
				</td>
			</tr>
			<tr>
				<td style="text-align:left">NOMBRE D’OPP DE 3eme NIVEAU :</td>
					<td class="respuesta" style="text-align:center"><b>'.$solicitud['preg1_1'].'</b></td>
				<td style="text-align:left">NOMBRE D’OPP DE 2eme NIVEAU :</td>
					<td class="respuesta" style="text-align:center"><b>'.$solicitud['preg1_2'].'</b></td>
				<td style="text-align:left">NOMBRE D’OPP DE 1er NIVEAU :</td>
					<td class="respuesta" style="text-align:center"><b>'.$solicitud['preg1_3'].'</b></td>
				<td style="text-align:left">NOMBRE DE COMMUNAUTES, DE ZONES OU DE GROUPES DE TRAVAIL :</td>
					<td class="respuesta" style="text-align:center"><b>'.$solicitud['preg1_4'].'</b></td>
			</tr>
			<tr>
				<td style="text-align:left" colspan="8">
			        <div>2.	 INDIQUEZ QUEL(S) PRODUIT(S) VOUS SOUHAITEZ INCLURE DANS LA CERTIFICATION DU SYMBOLE DES PETITS PRODUCTEURS POUR LE(S) QUEL (S) L’ORGANISME DE CERTIFICATION REALIZERA L’EVALUATION. </div>
			        <div class="respuesta"><b>'.$solicitud['op_preg2'].'</b></div>	
				</td>
			</tr>

			<tr>
				<td style="text-align:left" colspan="8">
			        <div>3.	INDIQUEZ SI VOTRE ORGANISATION SOUHAITE INCLURE UNE QUALIFICATION OPTIONNELLE POUR UNE UTILISATION COMPLEMENTAIRE AVEC LE LOGO GRAPHIQUE DU SYMBOLE DES PETITS PRODUCTEURS.</div>
			        <div class="respuesta"><b>'.$solicitud['op_preg3'].'</b></div>	
				</td>
			</tr>
			<tr>
				<td style="text-align:left" colspan="8">
			        <div>4.	MARQUEZ D’UNE CROIX L’ACTIVITE EXERCEE PAR L’ORGANISATION DES PETITS PRODUCTEURS :</div>
			        <div class="respuesta"><b>'.$alcance.'</b></div>
				</td>
			</tr>
			<!--<tr>
				<td colspan="2">
					<div>PRODUCTION</div>
				</td>
				<td colspan="3">
					<div>TRANSFORMATION</div>
				</td>
				<td colspan="3">
					<div>EXPORTATION</div>
				</td>
			</tr>-->
			<tr>
				<td style="text-align:left" colspan="8">
			        <div>5.	INDIQUEZ SI VOUS UTILISEZ EN SOUS-TRAITANCE LES SERVICES D’USINES DE TRANSFORMATION, D’ENTREPRISES DE COMMERCIALISATION OU D’ENTREPRISES D’IMPORT/EXPORT, LE CAS ECHEANT, MENTIONNEZ LE TYPE DE SERVICE REALISE. </div>
			        <div class="respuesta"><b>'.$solicitud['op_preg5'].'</b></div>	
				</td>
			</tr>
			<tr>
				<td style="text-align:left" colspan="8">
			        <div>6.	SI VOUS SOUS-TRAITEZ DES SERVICES A DES USINES DE TRANSFORMATION, A DES ENTREPRISES DE COMMERCIALISATION OU A DES ENTREPRISES D’IMPORT/EXPORT, INDIQUEZ SI CELLES-CI SONT ENREGISTREES, EN COURS D’ENREGISTREMENT SOUS LE PROGRAMME DU SPP OU SI ELLES SERONT CONTROLEES AU TRAVERS DE L’ORGANISATION DE PETITS PRODUCTEURS.</div>
			        <div class="respuesta"><b>'.$solicitud['op_preg6'].'</b></div>	
				</td>
			</tr>
			<tr>
				<td style="text-align:left" colspan="8">
			        <div>7.	EN PLUS DE VOTRE SIEGE SOCIAL, INDIQUEZ LE NOMBRE DE CENTRES DE COLLECTE, DE TRANSFORMATION OU DE BUREAUX SUPPLEMENTAIRES QUE VOUS POSSEDEZ.</div>
			        <div class="respuesta"><b>'.$solicitud['op_preg7'].'</b></div>	
				</td>
			</tr>
			<tr>
				<td style="text-align:left" colspan="8">
			        <div>8.	EST-CE QUE VOUS DISPOSEZ D’UN SYSTEME DE CONTROLE INTERNE AFIN DE RESPECTER LES CRITERES DE LA NORME GENERALE DU SYMBOLE DES PETITS PRODUCTEURS? DANS CE CAS VEUILLEZ EXPLIQUER.</div>
			        <div class="respuesta"><b>'.$solicitud['op_preg8'].'</b></div>	
				</td>
			</tr>
			<tr>
				<td style="text-align:left" colspan="8">
			        <div>9.	REMPLIR LE TABLEAU DE VOS CERTIFICATIONS, (EXEMPLE: EU, NOP, JASS, FLO, etc.)</div>	
				</td>
			</tr>';


		$html .= '<tr>

			<td colspan="8">
				<table border="1">
					<tr style="background-color:#3498db">
						<td style="text-align:center">CERTIFICATION</td>
						<td style="text-align:center">CERTIFICATEUR</td>
						<td style="text-align:center">ANNEE DE LA CERTIFICATION</td>
						<td style="text-align:center">A-T-ELLE ETE INTERROMPUE?</td>	
						<!--<td>
							<button type="button" onclick="tablaCertificaciones()" class="btn btn-primary" aria-label="Left Align">
							  <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
							</button>
							
						</td>-->
					</tr>';


          $query = "SELECT * FROM certificaciones WHERE idsolicitud_certificacion = $idsolicitud_certificacion";
          $consultar = mysql_query($query, $dspp) or die(mysql_error());
          $contador = 0;
          while($certificacion = mysql_fetch_assoc($consultar)){
            $html .= '
            <tr class="respuesta">
            	<td style="text-align:left">
					'.$certificacion['certificacion'].'
            	</td>
            	<td style="text-align:left">
					'.$certificacion['certificadora'].'
            	</td>
            	<td style="text-align:left">
					'.$certificacion['ano_inicial'].'
            	</td>
            	<td style="text-align:left">
					'.$certificacion['interrumpida'].'
            	</td>

            </tr>';

           $contador++; } 
          
   			$html .= '
				</table>			
			</td>
		</tr>


			<tr>
				<td style="text-align:left" colspan="8">
			        <div>10.	PARMI LES CERTIFICATIONS DONT VOUS DISPOSEZ ET LORS DE LEUR PLUS RECENTE EVALUATION INTERNE ET EXTERNE, COMBIEN DE NON CONFORMITES ONT ETE IDENTIFIEES? CELLES-CI ONT-ELLES ETE RESOLUES? QUEL EST LEUR ETAT ACTUEL?  </div>
			        <div class="respuesta"><b>'.$solicitud['op_preg10'].'</b></div>	
				</td>
			</tr>
			<tr>
				<td style="text-align:left" colspan="8">
			        <div>12. AVEZ-VOUS REALISE DES VENTES SOUS LE SPP DURANT LE CYCLE DE CERTIFICATION ANTERIEUR ? </div>
			        <div class="respuesta"><b>'.$solicitud['op_preg12'].'</b></div>	
				</td>
			</tr>
			<tr>
				<td style="text-align:left" colspan="8">
			        <div>13. LE CAS ECHEANT, MERCI DE MARQUER D’UNE CROIX LE RANG DE LA VALEUR TOTALE DE VOS VENTES SOUS LE SPP POUR LE CYCLE ANTERIEUR SELON LE TABLEAU SUIVANT : </div>
			        <div class="respuesta"><b>'.$solicitud['op_preg13'].'</b></div>	
				</td>
			</tr>

			<tr>
				<td style="text-align:left" colspan="8">
			        <div>13_1. SUR L’ENSEMBLE DE VOS VENTES, QUEL EST LE POURCENTAGE REALISE SOUS LES CERTIFICATIONS BIOLOGIQUES, DU COMMERCE EQUITABLE ET / OU DU SYMBOLE DES PETITS PRODUCTEURS ?</div>
			        <div class="respuesta"><b>% BIOLOGIQUES:</b> '.$solicitud['organico'].'</div>
			        <div class="respuesta"><b>% DU COMMERCE EQUITABLE:</b> '.$solicitud['comercio_justo'].'</div>
			        <div class="respuesta"><b>% SYMBOLE DES PETITS PRODUCTEURS:</b> '.$solicitud['spp'].'</div>
			        <div class="respuesta"><b>% SANS CERTIFICAT:</b> '.$solicitud['sin_certificado'].'</div>
				</td>
			</tr>



			<tr>
				<td style="text-align:left" colspan="8">
			        <div>14. DATE ESTIMEE DE DEBUT D’UTILISATION DU SYMBOLE DES PETITS PRODUCTEURS :</div>
			        <div class="respuesta"><b>'.$solicitud['op_preg14'].'</b></div>	
				</td>
			</tr>
		
		</table>

      </div>
	  <div>

		<table border="1">
					<tr>
						<th colspan="8" style="text-align:center;background-color:#B8D186">
							<h3>INFORMATIONS SUR LES PRODUITS POUR LESQUELS VOUS DEMANDEZ A UTILISER LE SYMBOLE<sup>6</sup> </h3>
						</th>
					</tr>
					<tr style="background-color:#3498db">
						<td style="text-align:center">Produit</td>
						<td style="text-align:center">Volume Total Estimé à Commercialiser</td>
						<td style="text-align:center">Produit Finit</td>
						<td style="text-align:center">Matière Première</td>
						<td style="text-align:center">Pays de Destination</td>
						<td style="text-align:center">Marque Propre</td>
						<td style="text-align:center">Marque d’un Client</td>
						<td style="text-align:center">Pas encore de client</td>
						<!--<td>
							<button type="button" onclick="tablaProductos()" class="btn btn-primary" aria-label="Left Align">
							  <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
							</button>
							
						</td>-->					
					</tr>';


          $query_producto_detalle = "SELECT * FROM productos WHERE idsolicitud_certificacion = $idsolicitud_certificacion";
          $producto_detalle = mysql_query($query_producto_detalle, $dspp) or die(mysql_error());
          $contador = 0;
          while($row_producto = mysql_fetch_assoc($producto_detalle)){
      		$html .='

					<tr>
						<td style="text-align:center">
							<b class="respuesta">'.$row_producto['producto'].'</b>
						</td>
						<td style="text-align:center">
							<b class="respuesta">'.$row_producto['volumen'].'</b>
						</td>
						<td style="text-align:center">	
							<b class="respuesta">'.$row_producto['terminado'].'</b>
			            </td>          
						<td style="text-align:center">
							<b class="respuesta">'.$row_producto['materia'].'</b>
						</td>
						<td style="text-align:center">
							<b class="respuesta">'.$row_producto['destino'].'</b>
						</td>
						<td style="text-align:center">
    						<b class="respuesta">'.$row_producto['marca_propia'].'</b>
						</td>
						<td style="text-align:center">
							<b class="respuesta">'.$row_producto['marca_cliente'].'</b>          
						</td>
						<td style="text-align:center">
							<b class="respuesta">'.$row_producto['sin_cliente'].'</b>
						</td>
					</tr>';

           $contador++; }
           $html .= '

					<tr>
						<td colspan="8">
							<h6><sup>6</sup> L’information fournie dans cette section sera traitée en toute confidentialité. Veuillez insérer des colonnes supplémentaires si nécessaire. </h6>
						</td>
					</tr>
				</table>

	  </div>
	<div>
		<table border="1">
		<tr>
			<td colspan="8" style="text-align:center;background-color:#B8D186">
				<h3>COMPROMISOS</h3>
			</td>
		</tr>
		<tr>
			<td colspan="8" style="text-align:left">
				1.	Par l’envoi de cette demande, vous manifestez le souhait de recevoir une proposition d’enregistrement.<br>
				2.	Le processus d’enregistrement débutera dès réception du paiement.<br>
				3.	L’envoi et la réception de cette demande ne garantissent pas l’acceptation de l’enregistrement.<br>
				4.	      Connaître et respecter toutes les exigences de la Norme Générale du Symbole des Petits Producteurs qui vous    sont appliquées en qualité d’Organisations de Petits Producteurs, tant critiques que minima, indépendamment du type d’évaluation réalisée.

			</td>
		</tr>
		<tr style="background-color:#bdc3c7">
			<td colspan="2" style="text-align:left">
				Nom et signature de la personne responsable de la véracité des informations fournies et qui assurera le suivi de cette demande de la part du demandeur :  
			</td>
			<td colspan="8" style="text-align:left">
				<b class="respuesta">'.$solicitud['responsable'].'</b>
			</td>
		</tr>
    <tr style="background-color:#bdc3c7">
      <td colspan="2" style="text-align:left">
        Nom et signature du responsable de la réception de la demande au sein de l’organisme de certification :
      </td>
      <td colspan="8" style="text-align:left">
		<b class="respuesta">'.$solicitud['nombre_oc'].'</b>
      </td>

    </tr>
		</table>

	</div>

      <div>
        <table style="padding:0px;margin:0px;">
			<tr>
				<td style="text-align:left;margin-bottom:0px;font-size:9px;">
			        <div>
						<img src="img/logofundepo.png" >
			        </div>
				</td>
				<td style="text-align:right;font-size:9px;">

			        <div><h2>DROITS RÉSERVÉS ® SPP Global</h2></div>
			        <div>Demande_Certification_SPP_V7_2015-01-27</div>
				</td>
			</tr>
        </table>
      </div>

	';

	$mpdf = new mPDF('c', 'A4');
    $mpdf->setAutoTopMargin = 'pad';
    $mpdf->pagenumPrefix = 'Page ';
    $mpdf->pagenumSuffix = ' - ';

    //$mpdf->nbpgSuffix = ' pages';
    $mpdf->SetFooter('{PAGENO}{nbpg}');

    $mpdf->SetHTMLHeader('
    <header>
      <div>
        <table style="padding:0px;margin:0px;">
			<tr>
				<td style="text-align:left;margin-bottom:0px;font-size:9px;">
			        <div>
						<img src="img/FUNDEPPO.jpg" >
			        </div>
				</td>
				<td style="text-align:right;font-size:9px;">
			        <div>
						<h2>
							Demande de Certification pour les Organisations de Petits Producteurs
						</h2>							
			        </div>
			        <div>Symbole des Petits Producteurs</div>
			        <div>Version 7. 26-Jan-2015</div>
				</td>
			</tr>
        </table>
      </div>
    </header>
      ');
	$css = file_get_contents('css/style.css');	
	$mpdf->writeHTML($css,1);
	$mpdf->writeHTML($html);
	$mpdf->Output('reporte.pdf', 'I');

 ?>