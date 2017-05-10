          <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
            <tbody>
              <tr>
                <th rowspan="6" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                <th scope="col" align="left" width="280"><strong>Notificación de Propuesta / Application Price Quote Accepted ('.date('d/m/Y', $fecha).')</strong></th>
              </tr>

              <tr>
                <td align="left" style="color:#ff738a;">
                  Felicidades se ha aceptado su cotización, será informado una vez que inicie el período de objeción:
                  <br><br>
                  Congratulations. Your price quote has been accepted. You will be informed as soon as the objection period begins.
                </td>
              </tr>

              <tr>
                <td align="left">Teléfono / phone OPP: '.$detalle_opp['telefono'].'</td>
              </tr>
              <tr>
                <td align="left">País / Country: '.$detalle_opp['pais'].'</td>
              </tr>
              <tr>
                <td align="left" style="color:#ff738a;">Nombre: '.$detalle_opp['contacto1_nombre'].' | '.$detalle_opp['contacto1_email'].'</td>
              </tr>
              <tr>
                <td align="left" style="color:#ff738a;">Nombre: '.$detalle_opp['contacto2_nombre'].' | '.$detalle_opp['contacto2_email'].'</td>
              </tr>


              <tr>
                <td colspan="2">
                  <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;" border="1" width="650px">
                    <tbody>
                      <tr style="font-size: 12px; text-align:center; background-color:#dff0d8; color:#3c763d;" height="50px;">
                        <td width="162.5px">Nombre de la organización/Organization name</td>
                        <td width="162.5px">Abreviación / Short name</td>
                        <td width="162.5px">País / Country</td>
                        <td width="162.5px">Organismo de Certificación / Certification Entity</td> 
                      </tr>
                      <tr style="font-size: 12px; text-align:justify">
                        <td style="padding:10px;">
                          '.$detalle_opp['nombre_opp'].'
                        </td>
                        <td style="padding:10px;">
                          '.$detalle_opp['abreviacion_opp'].'
                        </td>
                        <td style="padding:10px;">
                          '.$detalle_opp['pais'].'
                        </td>
                        <td style="padding:10px;">
                          '.$detalle_opp['nombre_oc'].'
                        </td>

                      </tr>

                    </tbody>
                  </table>        
                </td>
              </tr>
            </tbody>
          </table>