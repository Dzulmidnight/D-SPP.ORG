<?php 
	require_once('../mpdf/mpdf.php');
	require_once('../en/Connections/dspp.php');
	  mysql_select_db($database_dspp, $dspp);

	$idsolicitud_registro = $_POST['idsolicitud_registro'];



	$query = "SELECT solicitud_registro.*, oc.idoc, oc.nombre AS 'nombre_oc', empresa.spp AS 'spp_empresa', empresa.nombre AS 'nombre_empresa', empresa.direccion_oficina, empresa.pais, empresa.email AS 'email_empresa', empresa.sitio_web, empresa.telefono AS 'telefono_empresa', empresa.rfc, empresa.ciudad AS 'ciudad_empresa', porcentaje_productoVentas.organico, porcentaje_productoVentas.comercio_justo, porcentaje_productoVentas.spp, porcentaje_productoVentas.sin_certificado FROM solicitud_registro INNER JOIN oc ON solicitud_registro.idoc = oc.idoc LEFT JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa LEFT JOIN porcentaje_productoVentas ON solicitud_registro.idsolicitud_registro = porcentaje_productoVentas.idsolicitud_registro WHERE solicitud_registro.idsolicitud_registro = $idsolicitud_registro";
	//$query = "SELECT formato_cliente.*, cliente.* FROM formato_cliente INNER JOIN cliente ON formato_cliente.idcliente = cliente.idcliente WHERE idsolicitud_registro = $idsolicitud_registro";
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
	if($solicitud['importacion']){
		$alcance .= 'IMPORTACIÓN - IMPORTING<br>';
	}


	$html = '
      <div>
        <table border="1" style="padding:0px;margin:0px;">
			<tr>
				<td style="text-align:left;margin-bottom:0px;font-size:9px;">
			        <table border="1">
						<tr>
							<td>FECHA DE ELABORACIÓN</td>
							<td><h3>'.date("d/m/Y", $solicitud['fecha_registro']).'</h3></td>
						</tr>
						<tr>
							<td>TIPO DE SOLICITUD</td>
							<td style="background-color:#3498db;text-align:center"><b>'.$solicitud['tipo_solicitud'].'</b></td>
						</tr>
			        </table>
				</td>
				<td style="text-align:right;font-size:9px;">
			        <table border="1">
						<tr>
							<td colspan="3" style="background-color:#B8D186"><b>CODIGO DE IDENTIFICACIÓN SPP(#SPP):</b></td>
							<td colspan="2" style="background-color:#B8D186;"><h3>'.$solicitud['spp_empresa'].'</h3></td>
						</tr>
						<tr style="background-color:#bdc3c7;">

							<td style="text-align:left">PROCEDIMIENTO DE CERTIFICACIÓN</td>
							<td style="text-align:center;'.$color1.';">DOCUMENTAL "ACORTADO"</td>
							<td style="text-align:center;'.$color2.';">DOCUMENTAL "NORMAL"</td>
							<td style="text-align:center;'.$color3.';">COMPLETO "IN SITU"</td>
							<td style="text-align:center;'.$color4.';">COMPLETO "A DISTANCIA"</td>
						</tr>
			        </table>
				</td>
			</tr>
        </table>
      </div>

      <div>
		<table border="1" style="font-size:11px;">
			<tr>
				<td colspan="8" style="text-align:center;background-color:#B8D186"><h3>DATOS GENERALES</h3></td>
			</tr>
			<tr>
				<td colspan="8" style="text-align:left">
			        <div>NOMBRE COMPLETO DE LA EMPRESA:</div>
			        <div class="respuesta"><b>'.$solicitud['nombre_empresa'].'</b></div>
				</td>
			</tr>
			<tr>
				<td colspan="7" style="text-align:left">
			        <div>DIRECCIÓN COMPLETA DE SUS OFICINAS CENTRALES (CALLE, BARRIO, LUGAR, REGIÓN):</div>
			        <div class="respuesta"><b>'.$solicitud['direccion_oficina'].'</b></div>
				</td>
				<td colspan="1" style="text-align:left">
			        <div>PAÍS:</div>
			        <div class="respuesta"><b>'.$solicitud['pais'].'</b></div>		
				</td>
			</tr>
			<tr>
				<td colspan="8" style="text-align:left">
			        <div>CORREO ELECTRÓNICO</div>
			        <div class="respuesta"><b>'.$solicitud['email_empresa'].'</b></div>
				</td>
			</tr>
			<tr>
				<td colspan="4" style="text-align:left">
			        <div>SITIO WEB:</div>
			        <div class="respuesta"><b>'.$solicitud['sitio_web'].'</b></div>
				</td>
				<td colspan="4" style="text-align:left">
			        <div>TELÉFONOS (CÓDIGO DE PAÍS+ CÓDIGO DE ÁREA + NÚMERO):</div>
			        <div class="respuesta"><b>'.$solicitud['telefono_empresa'].'</b></div>		
				</td>
			</tr>
			<tr>
				<td colspan="8" style="text-align:left">
			        <div>
			        	DATOS FISCALES (DATOS PARA FACTURACIÓN COMO DOMICILIO, RFC, RUC, CIUDAD, PAÍS, ETC):
			        	<hr>
			        </div>

			        <div class="respuesta">DIRECCION: <b>'.$solicitud['direccion_fiscal'].'</b></div>
			        <div class="respuesta">RFC: <b>'.$solicitud['rfc'].'</b></div>
			        <div class="respuesta">RUC: <b>'.$solicitud['ruc'].'</b></div>
			        <div class="respuesta">CIUDAD: <b>'.$solicitud['ciudad_empresa'].'</b></div>
			        <div class="respuesta">PAIS: <b>'.$solicitud['pais'].'</b></div>
				</td>
			</tr>
			<tr>
				<td colspan="4" style="text-align:left">
			        <div>PERSONA(S) DE CONTACTO SOLICITUD:</div>
			        <div class="respuesta"><b>'.$solicitud['contacto1_nombre'].'</b></div>
			        <div class="respuesta"><b>'.$solicitud['contacto2_nombre'].'</b></div>

			        <div>CORRREO ELECTRÓNICO PERSONA(S) DE CONTACTO:</div>
			        <div class="respuesta"><b>'.$solicitud['contacto1_email'].'</b></div>
			        <div class="respuesta"><b>'.$solicitud['contacto2_email'].'</b></div>


			    </td>
			    <td colspan="4" style="text-align:left">
			        <div>CARGO(S):</div>
			        <div class="respuesta"><b>'.$solicitud['contacto1_cargo'].'</b></div>
			        <div class="respuesta"><b>'.$solicitud['contacto2_cargo'].'</b></div>

			        <div>TELÉFONO(S) PERSONA(S) DE CONTACTO:</div>
			        <div class="respuesta"><b>'.$solicitud['contacto1_telefono'].'</b></div>
			        <div class="respuesta"><b>'.$solicitud['contacto2_telefono'].'</b></div>
				</td>

			</tr>
			<tr>
				<td colspan="4" style="text-align:left">
			        <div>PERSONA DEL ÁREA ADMINSITRATIVA:</div>
			        <div class="respuesta"><b>'.$solicitud['adm1_nombre'].'</b></div>
			        <div class="respuesta"><b>'.$solicitud['adm2_nombre'].'</b></div>


			        <div>CORRREO ELECTRÓNICO DEL ÁREA ADMINSITRATIVA:</div>
			        <div class="respuesta"><b>'.$solicitud['adm1_email'].'</b></div>
			        <div class="respuesta"><b>'.$solicitud['adm2_email'].'</b></div>


			    </td>
			    <td colspan="4" style="text-align:left">
			        <div>TELÉFONO PERSONA DEL ÁREA ADMINSITRATIVA:</div>
			        <div class="respuesta"><b>'.$solicitud['adm1_telefono'].'</b></div>
			        <div class="respuesta"><b>'.$solicitud['adm2_telefono'].'</b></div>


				</td>
			</tr>

			<tr>
				<td colspan="8" style="text-align:center;background-color:#B8D186"><h3>DATOS DE OPERACIÓN</h3></td>
			</tr>
			<tr>
				<td  style="text-align:left" colspan="8">
			        <div>1.	¿CUÁLES SON LAS ORGANIZACIONES DE PEQUEÑOS PRODUCTORES A LAS QUE LES COMPRA O PRETENDE COMPRAR BAJO EL ESQUEMA DEL SÍMBOLO DE PEQUEÑOS PRODUCTORES?</div>
			        <div class="respuesta"><b>'.$solicitud['preg1'].'</b></div>	
				</td>
			</tr>
			<tr>
				<td style="text-align:left" colspan="8">
			        <div>2.	¿QUIÉN O QUIÉNES SON LOS PROPIETARIOS DE LA EMPRESA?</div>
			        <div class="respuesta"><b>'.$solicitud['preg2'].'</b></div>	
				</td>
			</tr>

			<tr>
				<td style="text-align:left" colspan="8">
			        <div>3.	ESPECIFIQUE QUÉ PRODUCTO(S) QUIERE INCLUIR EN EL CERTIFICADO DEL SÍMBOLO DE PEQUEÑOS PRODUCTORES PARA LOS CUALES EL ORGNISMO DE CERTIFICACIÓN REALIZARÁ LA EVALUACIÓN.</div>
			        <div class="respuesta"><b>'.$solicitud['preg3'].'</b></div>	
				</td>
			</tr>

			<tr>
				<td style="text-align:left" colspan="8">
			        <div>4.	SI SU EMPRESA ES UN COMPRADOR FINAL, MENCIONE SI QUIEREN INCLUIR ALGÚN CALIFICATIVO ADICIONAL PARA USO COMPLEMENTARIO CON EL DISEÑO GRÁFICO DEL SÍMBOLO DE PEQUEÑOS PRODUCTORES</div>
			        <div class="respuesta"><b>'.$solicitud['preg4'].'</b></div>	
				</td>
			</tr>
			<tr>
				<td style="text-align:left" colspan="8">
			        <div>5.	INDIQUE EL ALCANCE QUE TIENE LA EMPRESA</div>
			        <div class="respuesta"><b>'.$alcance.'</b></div>
				</td>
			</tr>

			<tr>
				<td style="text-align:left" colspan="8">
			        <div>6.	EXPLIQUE SI SUBCONTRATA LOS SERVICIOS DE PLANTAS DE PROCESAMIENTO, EMPRESAS DE COMERCIALIZACIÓN O EMPRESAS QUE REALICEN LA IMPORTACIÓN O EXPORTACIÓN, SI LA RESPUESTA ES AFIRMATIVA, MENCIONE EL NOMBRE Y EL SERVICIO QUE REALIZA </div>
			        <div class="respuesta"><b>'.$solicitud['preg6'].'</b></div>	
				</td>
			</tr>
			<tr>
				<td style="text-align:left" colspan="8">
			        <div>7.	SI SUBCONTRATA LOS SERVICIOS DE PLANTAS DE PROCESAMIENTO, EMPRESAS DE COMERCIALIZACIÓN O EMPRESAS QUE REALICEN LA IMPORTACIÓN O EXPORTACIÓN, INDIQUE SI ESTAS ESTAN REGISTRADAS O VAN A REALIZAR EL REGISTRO BAJO EL PROGRAMA DEL SPP O SERÁN CONTROLADAS A TRAVÉS DE SU EMPRESA.</div>
			        <div class="respuesta"><b>'.$solicitud['preg8'].'</b></div>	
				</td>
			</tr>
			<tr>
				<td style="text-align:left" colspan="8">
			        <div>9.	EN CASO DE TENER CENTROS DE ACOPIO, ÁREAS DE PROCESAMIENTO U OFICINAS ADICIONALES,  ANEXAR UN CROQUIS GENERAL MOSTRANDO SU UBICACIÓN</div>
			        <div class="respuesta"><b>'.$solicitud['preg9'].'</b></div>	
				</td>
			</tr>
			<tr>
				<td style="text-align:left" colspan="8">
			        <div>10.	CUENTA CON UN SISTEMA DE CONTROL INTERNO PARA DAR CUMPLIMIENTO A LOS CRITERIOS DE LA NORMA GENERAL DEL SÍMBOLO DE PEQUEÑOS PRODUCTORES, EN SU CASO EXPLIQUE.</div>
			        <div class="respuesta"><b>'.$solicitud['preg10'].'</b></div>	
				</td>
			</tr>
			<tr>
				<td style="text-align:left" colspan="8">
			        <div>11. LLENAR LA TABLA DE ACUERDO A LAS CERTIFICACIONES QUE TIENE, (EJEMPLO: EU, NOP, JASS, FLO, etc)</div>	
				</td>
			</tr>';


		$html .= '<tr>

			<td colspan="8">
				<table border="1">
					<tr style="background-color:#3498db">
						<td style="text-align:center">CERTIFICACIÓN</td>
						<td style="text-align:center">CERTIFICADORA</td>
						<td style="text-align:center">AÑO INICIAL DE CERTIFICACIÓN?</td>
						<td style="text-align:center">¿HA SIDO INTERRUMPIDA?</td>	
						<!--<td>
							<button type="button" onclick="tablaCertificaciones()" class="btn btn-primary" aria-label="Left Align">
							  <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
							</button>
							
						</td>-->
					</tr>';


          $query = "SELECT * FROM certificaciones WHERE idsolicitud_registro = $idsolicitud_registro";
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
			        <div>12. DE LAS CERTIFICACIONES CON LAS QUE CUENTA, EN SU MÁS RECIENTE EVALUACIÓN INTERNA Y EXTERNA, ¿CUÁNTOS INCUMPLIMIENTOS SE IDENTIFICARON? Y EN SU CASO, ¿ESTÁN RESUELTOS O CUÁL ES SU ESTADO?</div>
			        <div class="respuesta"><b>'.$solicitud['preg12'].'</b></div>	
				</td>
			</tr>
			<tr>
				<td style="text-align:left" colspan="8">
			        <div>13. DEL TOTAL DE SU COMERCIALIZACIÓN EL CICLO PASADO, ¿QUÉ PORCENTAJE FUERON REALIZADAS BAJO LOS ESQUEMAS CERTIFICADOS DE ORGÁNICO, COMERCIO JUSTO Y/O SÍMBOLO DE PEQUEÑOS PRODUCTORES? </div>
			        <div class="respuesta"><b>% ORGÁNICO:</b> '.$solicitud['organico'].'</div>
			        <div class="respuesta"><b>% COMERCIO JUSTO:</b> '.$solicitud['comercio_justo'].'</div>
			        <div class="respuesta"><b>% SÍMBOLO PEQUEÑOS PRODUCTORES:</b> '.$solicitud['spp'].'</div>
			        <div class="respuesta"><b>% SIN CERTIFICADO:</b> '.$solicitud['sin_certificado'].'</div>
				</td>
			</tr>

			<tr>
				<td style="text-align:left" colspan="8">
			        <div>14. TUVO COMPRAS SPP DURANTE EL CICLO DE REGISTRO ANTERIOR?</div>
			        <div class="respuesta"><b>'.$solicitud['preg13'].'</b></div>	
				</td>
			</tr>
			<tr>
				<td style="text-align:left" colspan="8">
			        <div>15. SI SU RESPUESTA FUE POSITIVA, FAVOR DE INIDICAR EL RANGO DEL VALOR TOTAL DE SUS COMPRAS SPP DEL CICLO ANTERIOR</div>
			        <div class="respuesta"><b>'.$solicitud['preg14'].'</b></div>	
				</td>
			</tr>

			<tr>
				<td style="text-align:left" colspan="8">
			        <div>16. FECHA ESTIMADA PARA COMENZAR A USAR EL SÍMBOLO DE PEQUEÑOS PRODUCTORES:</div>
			        <div class="respuesta"><b>'.$solicitud['preg15'].'</b></div>	
				</td>
			</tr>
		
		</table>

      </div>
	  <div>

		<table border="1">
					<tr style="background-color:#3498db">
						<td style="text-align:center">Producto</td>
						<td style="text-align:center">Volumen Total Estimado a Comercializar</td>
						<td style="text-align:center">Volumen como Producto Terminado</td>
						<td style="text-align:center">Volumen como Materia Prima</td>
						<td style="text-align:center">País(es) de Origen</td>
						<td style="text-align:center">País(es) de Destino</td>
						<!--<td>
							<button type="button" onclick="tablaProductos()" class="btn btn-primary" aria-label="Left Align">
							  <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
							</button>
							
						</td>-->					
					</tr>';


          $query_producto_detalle = "SELECT * FROM productos WHERE idsolicitud_registro = $idsolicitud_registro";
          $producto_detalle = mysql_query($query_producto_detalle, $dspp) or die(mysql_error());
          $contador = 0;
          while($row_producto = mysql_fetch_assoc($producto_detalle)){
      		$html .='

					<tr>
						<td style="text-align:center">
							<b class="respuesta">'.$row_producto['producto'].'</b>
						</td>
						<td style="text-align:center">
							<b class="respuesta">'.$row_producto['volumen_estimado'].'</b>
						</td>
						<td style="text-align:center">	
							<b class="respuesta">'.$row_producto['volumen_terminado'].'</b>
			            </td>          
						<td style="text-align:center">
							<b class="respuesta">'.$row_producto['volumen_materia'].'</b>
						</td>
						<td style="text-align:center">
							<b class="respuesta">'.$row_producto['origen'].'</b>
						</td>
						<td style="text-align:center">
    						<b class="respuesta">'.$row_producto['destino'].'</b>
						</td>
					</tr>';

           $contador++; }
           $html .= '

					<tr>
						<td colspan="8">
							<h6><sup>6</sup> La información proporcionada en esta sección será tratada con plena confidencialidad. Favor de insertar filas adicionales de ser necesario.</h6>
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
				1.	Con el envío de esta solicitud se manifiesta el interés de recibir una propuesta de Registro<br>
				2.	El proceso de Registro comenzará en el momento que se confirme la recepción del pago correspondiente<br>
				3.	La entrega y recepción de esta solicitud no garantiza que el proceso de Registro será positivo.<br>
				4.	Conocer y dar cumplimiento a todos los requisitos de la Norma General del Símbolo de Pequeños Productores que le apliquen como Compradores, Comercializadoras Colectiva de Organizaciones de Pequeños Productores, Intermediarios y Maquiladores, tanto Críticos como Mínimos, independientemente del tipo de evaluación que se realice.
			</td>
		</tr>
		<tr style="background-color:#bdc3c7">
			<td colspan="2" style="text-align:left">
				Nombre de la persona que se responsabiliza de la veracidad de la información del formato y que le dará seguimiento a la solicitud de parte del solicitante:
			</td>
			<td colspan="8" style="text-align:left">
				<b class="respuesta">'.$solicitud['responsable'].'</b>
			</td>
		</tr>
    <tr style="background-color:#bdc3c7">
      <td colspan="2" style="text-align:left">
        OC que recibe la solicitud:
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

			        <div><h2>DERECHOS RESERVADOS</h2></div>
			        <div>Solicitud_Registro_SPP</div>
				</td>
			</tr>
        </table>
      </div>

	';

	$mpdf = new mPDF('c', 'A4');
    $mpdf->setAutoTopMargin = 'pad';
    $mpdf->pagenumPrefix = 'Página ';
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
							Solicitud de Registro para Compradores y otros Actores
						</h2>							
			        </div>
			        <div>Símbolo de Pequeños Productores</div>
			        <div>Versión 7. 26-Ene-2015</div>
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