          <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
            <tbody>
                <tr>
                  <th rowspan="2" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                  <th scope="col" align="left" width="280">Detalle Reporte Trimestral de Compras SPP</th>
                </tr>
                <tr>
                  <td style="padding-top:10px;">           
                    La empresa <span style="color:red">'.$empresa['abreviacion'].'</span> ha finalizado el <span style="color:red">TRIMESTRE '.$_GET['trim'].'</span>, a continuación se muestran una tabla con el resumen de las operaciones.
                    <hr>
                    <p style="color:#2c3e50;font-weight:bold">El Area de Certificación y Calidad SPP ha autorizado el siguiente Informe Trimestral de Compras, por favor proceda a generar la factura correspondiente(<small style="color:#7f8c8d">Se adjunta el PDF con los registro correspondientes al trimestre finalizado</small>).</p>
                    <p style="color:#2c3e50;font-weight:bold">Una vez creada la factura dar clic en el siguiente enlace para poder adjuntarla. <a href="http://localhost/D-SPP.ORG_2/procesar/facturacion.php?num='.$_GET['trim'].'&trim='.$idtrimestre.'" style="color:red">Clic para poder adjuntar factura</a></p>
                  </td>

                </tr>
                <tr>
                  <td colspan="2">
                    <table style="border: 1px solid #ddd;border-collapse: collapse;font-size:12px;">
                      <tr style="border: 1px solid #ddd;border-collapse: collapse;">
                        <td colspan="7" style="text-align:center">Resumen de operaciones</td>
                      </tr>
                      <tr style="border: 1px solid #ddd;border-collapse: collapse;">
                        <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">Empresa</td>
                        <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">Tipo de Empresa</td>
                        <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">Informe</td>
                        <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">Num. de Contratos</td>
                        <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">Valor total de los contratos</td>
                        <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">Cuota de uso aplicada acorde al año en curso</td>
                        <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">Total cuota de uso</td>
                      </tr>
                      <tr style="border: 1px solid #ddd;border-collapse: collapse;">
                        <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">'.$empresa['abreviacion'].'</td>
                        <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">COMPRADOR FINAL</td>
                        <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">'.$idtrimestre.'</td>
                        <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">'.$num_contratos.'</td>
                        <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">'.$total_valor_contrato.'</td>
                        <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">'.$porcetaje_cuota.'%</td>
                        <td style="padding: 10px;border: 1px solid #ddd;border-collapse: collapse;">'.$total_cuota_uso.'</td>
                      </tr>
                    </table>
                  </td>
                </tr>
            </tbody>
          </table>