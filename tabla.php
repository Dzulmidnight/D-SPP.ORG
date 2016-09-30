    <html>
      <head>
        <meta charset="utf-8">
      </head>
      <body>
      
        <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="700px">
          <thead>
            <tr>
              <th>
                <img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" />
              </th>
              <th style="text-align:left">
                D-SPP | Periodo de Objeción Finalizado / Objection Period Ended
                
              </th>
            </tr>
          </thead>
          <tbody>
            <tr style="text-align:left">
              <td colspan="2"><p><b>Ha finalizado el periodo de objeción con un dictamen <span style="color:red;">'.$_POST['dictame'].'</span></b></p></td>
            </tr>
            <tr> 
              <td colspan="2">Fecha Inicio: <span style="color:red">'.date('d/m/Y', $periodo['fecha_inicio']).'</span></td>
            </tr>
            <tr>
              <td colspan="2">Fecha Fin: <span style="color:red">'.date('d/m/Y', $periodo['fecha_fin']).'</span></td>
            </tr>
            <tr>
              <td colspan="2">
                Se ha iniciado el Proceso de Certificación, por favor ponerse en contacto con su Organismo de Certificación, para cualquier duda o aclaración por favor escribir a cert@spp.coop
                
                <p>Organismo de Certificación: <span style="color:red">'.$detalle_opp['nombre_opp'].'</span></p>
                
                <p>Telefono / phone: <span style="color:red">'.$detalle_opp['telefono'].'</span></p>
                
                <p>Email: <span style="color:red">'.$detalle_opp['email_opp'].'</span></p>
              </td>
            </tr>
            <tr style="width:100%">
              <td colspan="2">
                <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;" border="1" width="650px">

                  <tr style="font-size: 12px; text-align:center; background-color:#dff0d8; color:#3c763d;" height="50px;">
                    <td style="text-align:center">Tipo / Type</td>
                    <td style="text-align:center">Nombre de la Empresa/Company name</td>
                    <td style="text-align:center">Abreviación / Short name</td>
                    <td style="text-align:center">País / Country</td>
                    <td style="text-align:center">Organismo de Certificación / Certification Entity</td>
                    <td style="text-align:center">Tipo de solicitud / Kind of application</td>
                    <td style="text-align:center">Fecha de solicitud/Date of application</td>
                    <td style="text-align:center">Fin período de objeción/Objection period end</td>
                  </tr>
                  <tr>
                    <td>OPP</td>
                    <td>'.$detalle_opp['nombre_opp'].'</td>
                    <td>'.$detalle_opp['abreviacion_opp'].'</td>
                    <td>'.$detalle_opp['pais'].'</td>
                    <td>'.$detalle_opp['nombre_oc'].'</td>
                    <td>Certificación</td>
                    <td>'.date('d/m/Y', $periodo['fecha_inicio']).'</td>
                    <td>'.date('d/m/Y', $periodo['fecha_fin']).'</td>
                  </tr>
              </td>
              </table>
            </tr>

          </tbody>
        </table>

      </body>
    </html>