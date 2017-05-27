            <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
              <tbody>
                <tr>
                  <th rowspan="6" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                  <th scope="col" align="left" width="280"><strong>Cotización de Solicitud Aceptada / Application Price Quote Accepted ('.date('d/m/Y', $fecha).')</strong></th>
                </tr>

                <tr>
                  <td align="left" style="color:#ff738a;">
                    Felicidades se ha aceptado su cotización, ahora <span style="color:red">puede iniciar el Proceso de Certificación</span>, ya que esta solicitud se encuentra en proceso de "RENOVACIÓN DEL REGISTRO" :
                    <br><br>
                    Congratulations. Your price quote has been accepted. <span style="color:red">You may now begin the Certification Process</span>, since your application is in the "REGISTRATION RENEWAL" process.
                  </td>
                </tr>

                <tr>
                  <td align="left">Teléfono Empresa / Company phone: '.$detalle_empresa['telefono'].'</td>
                </tr>
                <tr>
                  <td align="left">País / Country: '.$detalle_empresa['pais'].'</td>
                </tr>
                <tr>
                  <td align="left" style="color:#ff738a;">Nombre: '.$detalle_empresa['contacto1_nombre'].' | '.$detalle_empresa['contacto1_email'].'</td>
                </tr>
                <tr>
                  <td align="left" style="color:#ff738a;">Nombre: '.$detalle_empresa['contacto2_nombre'].' | '.$detalle_empresa['contacto2_email'].'</td>
                </tr>


                <tr>
                  <td colspan="2">
                    <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;" border="1" width="650px">
                      <tbody>
                        <tr style="font-size: 12px; text-align:center; background-color:#dff0d8; color:#3c763d;" height="50px;">
                          <td width="162.5px">Nombre de la Empresa/Company name</td>
                          <td width="162.5px">Abreviación / Short name</td>
                          <td width="162.5px">País / Country</td>
                          <td width="162.5px">Organismo de Certificación / Certification Entity</td> 
                        </tr>
                        <tr style="font-size: 12px; text-align:justify">
                          <td style="padding:10px;">
                            '.$detalle_empresa['nombre_empresa'].'
                          </td>
                          <td style="padding:10px;">
                            '.$detalle_empresa['abreviacion_empresa'].'
                          </td>
                          <td style="padding:10px;">
                            '.$detalle_empresa['pais'].'
                          </td>
                          <td style="padding:10px;">
                            '.$detalle_empresa['nombre_oc'].'
                          </td>

                        </tr>

                      </tbody>
                    </table>        
                  </td>
                </tr>
              </tbody>
            </table>