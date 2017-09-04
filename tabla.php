                                <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px;" border="0" width="650px">
                                    <tr>
                                      <th rowspan="1" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                                      <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">Suspender Organización por falta de pago de la membresía SPP</span></p></th>

                                    </tr>


                                    <tr>
                                      <td colspan="2" style="text-align:justify">
                                        <p>
                                          Organización: <span style="color:red">'.$registros['nombre_opp'].'</span> - ('.$registros['abreviacion_opp'].')
                                        </p>
                                        <p>
                                          En seguimiento a la notificación del dictamen positivo SPP y de acuerdo al plazo máximo de 30 días se han enviado los recordatorios y la alerta de suspensión.
                                        </p>
                                        <p>
                                          Al no haber detectado un comprobante de pago de la membresía SPP cargado dentro del sistema D-SPP, el sistema solicita su permiso para poder enviar la suspensión de dicha organización.
                                        </p>
                                        <p>
                                          A continuación se muestra una tabla con información relevante:
                                        </p>
                                      </td>
                                    </tr>
                                    <tr>
                                      <td colspan="2">
                                        <style>
                                          table.tabla1, td.tabla1, th.tabla1 {    
                                              border: 1px solid #ddd;
                                              text-align: left;
                                          }

                                          table.tabla1 {
                                              border-collapse: collapse;
                                              font-size: 11px;
                                              width: 100%;
                                          }

                                          th.tabla1, td.tabla1 {
                                              padding: 5px;
                                          }
                                        </style>
                                        <table class="tabla1">
                                          <tr class="tabla1">
                                            <th class="tabla1">País</th>
                                            <th class="tabla1">Organización</th>
                                            <th class="tabla1">Periodo de Objeción</th>
                                            <th class="tabla1">Monto Membresía</th>
                                            <th class="tabla1" colspan="3">Fecha Mensajes</th>
                                            
                                          </tr>
                                          <tr class="tabla1">
                                            <td class="tabla1">'.$registros['pais'].'</td>
                                            <td class="tabla1">'.$registros['nombre_opp'].'(<span style="color:red">'.$registros['abreviacion_opp'].'</span>)</td>
                                            <td class="tabla1">'.$registros['fecha_dictamen'].'</td>
                                            <td class="tabla1">'.$registros['monto'].'</td>
                                            <td class="tabla1">'.date('d/m/Y', $recordatorio1).'</td>
                                            <td class="tabla1">'.date('d/m/Y', $recordatorio2).'</td>
                                            <td class="tabla1">'.date('d/m/Y', $alerta_suspension).'</td>
                                            
                                          </tr>
                                        </table>
                                      </td>
                                    </tr>
                                    <tr>
                                      <td colspan="2">
                                        <p>Pasos para suspender una organización:</p>
                                        <ol>
                                          <li>Debes ingresar en tu cuenta de administrador.</li>
                                          <li>Debes seleccionar la opción <span style="color:red;">"Membresias"</span>.</li>
                                          <li>Localizar la fila con el nombre de la Organización.</li>
                                          <li>Dar clic en la opción de <span style="color:red">"Suspender"</span>.</li>
                                        </ol>
                                      </td>
                                    </tr>
                                </table>