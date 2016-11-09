<?php 
require_once('../Connections/dspp.php'); 
require_once('../Connections/mail.php'); 

//error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

if (!isset($_SESSION)) {
  session_start();
  
  $redireccion = "../index.php?OC";

  if(!$_SESSION["autentificado"]){
    header("Location:".$redireccion);
  }
}

if (!function_exists("GetSQLValueString")) {
  function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
  {
    if (PHP_VERSION < 6) {
      $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
    }

    $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

    switch ($theType) {
      case "text":
        $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
        break;    
      case "long":
      case "int":
        $theValue = ($theValue != "") ? intval($theValue) : "NULL";
        break;
      case "double":
        $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
        break;
      case "date":
        $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
        break;
      case "defined":
        $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
        break;
    }
    return $theValue;
  }
}
mysql_select_db($database_dspp, $dspp);

$estado = 1;
$administrador = 'cert@spp.coop';

$row_periodo = mysql_query("SELECT * FROM periodo_objecion");

?>
<h4>Menú principal Administrador</h4>

<?php 
  while($periodo = mysql_fetch_assoc($row_periodo)){  //// INICIIA WHILE
      // REVISION PRIMER MENSAJE 7 DIAS
    $num_dias1 = 7;
    $num_dias2 = 10;

    if($periodo['estatus_objecion'] == 'ACTIVO'){
      $dias = $num_dias1 *(24*60*60);
      $alerta2 = $periodo['fecha_inicio'] + $dias;

      if(!isset($periodo['alerta2'])){
        if(time() >= $alerta2){
          $updateSQL = sprintf("UPDATE periodo_objecion SET alerta2 = %s WHERE idperiodo_objecion = %s",
            GetSQLValueString($estado, "int"),
            GetSQLValueString($periodo['idperiodo_objecion'], "int"));
          $actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error()); 

          //////////////////////// INICIA ENVIO DE MENSAJES //////////////////////
          if(isset($periodo['idsolicitud_certificacion'])){
            $row_opp = mysql_query("SELECT solicitud_certificacion.idopp, solicitud_certificacion.idoc, opp.nombre AS 'nombre_opp', opp.pais, oc.nombre AS 'nombre_oc' FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp INNER JOIN oc ON solicitud_certificacion.idoc = oc.idoc WHERE idsolicitud_certificacion = $periodo[idsolicitud_certificacion]", $dspp) or die(mysql_error());
            $informacion_opp = mysql_fetch_assoc($row_opp);

            $asunto = "D-SPP | PRIMER RECORDATORIO: PERIODO DE OBJECIÓN";

            $mensaje = '
              <html>
                <head>
                  <meta charset="utf-8">
                </head>
                <body>
                
                <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;text-align:justify" border="0" width="650px">
                  <tbody>
                    <tr>
                      <th rowspan="2" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                      <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red"><span style="color:#2c3e50">PRIMER RECORDATORIO</span>: PERIODO DE OBJECIÓN</span></p></th>

                    </tr>
                    <tr>
                      <td colspan="2" >
                        <p>
                          Recordatorio enviado del sistema D-SPP, recordandole que el Periodo de Objeción de la Organización: <b style="color:red">'.$informacion_opp['nombre_opp'].'</b> está por concluir (<span style="color:red">faltan 8 días</span>), una vez finalizado el periodo debe de cargar la Resolución de Objeción.
                        </p>
                      </td>
                    </tr>
                    <tr style="width:100%">
                      <td colspan="2">
                          <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px; border:#27ae60" border="1" width="650px">

                            <tr style="font-size: 12px; text-align:center; background-color:#27ae60; color:#ecf0f1;" height="50px;">
                              <td style="text-align:center">Tipo / Type</td>
                              <td style="text-align:center">Nombre de la organización/Organization name</td>
                              <td style="text-align:center">País / Country</td>
                              <td style="text-align:center">Organismo de Certificación / Certification Entity</td>

                              <td style="text-align:center">Tipo de solicitud / Kind of application</td>
                              <td style="text-align:center">Inicio Periodo de Objeción</td>
                              <td style="text-align:center">Fin período de objeción</td>
                            </tr>
                            <tr style="font-size:12px; color:#2c3e50;">
                              <td style="padding:10px;">OPP</td>
                              <td style="padding:10px;">'.$informacion_opp['nombre_opp'].'</td>
                              <td style="padding:10px;">'.$informacion_opp['pais'].'</td>
                              <td style="padding:10px;">'.$informacion_opp['nombre_oc'].'</td>
                              <td style="padding:10px;">Certificación</td>
                              <td style="padding:10px;">'.date('d/m/Y', $periodo['fecha_inicio']).'</td>
                              <td style="padding:10px;background-color:#e74c3c; color:#ecf0f1">'.date('d/m/Y', $periodo['fecha_fin']).'</td>
                            </tr>
                          </table>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="2">
                        <p>En caso de cualquier duda o aclaración por favor escribir a <span style="color:red">soporte@d-spp.org</span></p>
                      </td>
                    </tr>
                  </tbody>
                </table>

                </body>
              </html>
            ';

          }else if(isset($periodo['idsolicitud_registro'])){
            $row_empresa = mysql_query("SELECT solicitud_registro.idempresa, solicitud_registro.idoc, empresa.nombre AS 'nombre_empresa', empresa.pais, oc.nombre AS 'nombre_oc' FROM solicitud_registro INNER JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa INNER JOIN oc ON solicitud_registro.idoc = oc.idoc WHERE idsolicitud_registro = $periodo[idsolicitud_registro]", $dspp) or die(mysql_error());
            $informacion_empresa = mysql_fetch_assoc($row_empresa);

            $asunto = "D-SPP | PRIMER RECORDATORIO: PERIODO DE OBJECIÓN";

            $mensaje = '
              <html>
                <head>
                  <meta charset="utf-8">
                </head>
                <body>
                
                  <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;text-align:justify" border="0" width="650px">
                    <tbody>
                      <tr>
                        <th rowspan="2" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                        <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red"><span style="color:#2c3e50">PRIMER RECORDATORIO</span>: PERIODO DE OBJECIÓN</span></p></th>

                      </tr>
                      <tr>
                        <td colspan="2" >
                          <p>
                            Recordatorio enviado del sistema D-SPP, recordandole que el Periodo de Objeción de la Empresa: <b style="color:red">'.$informacion_empresa['nombre_empresa'].'</b> está por concluir (<span style="color:red">faltan 8 días</span>), una vez finalizado el periodo debe de cargar la Resolución de Objeción.
                          </p>
                        </td>
                      </tr>
                      <tr style="width:100%">
                        <td colspan="2">
                            <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;border: #3498db" border="1" width="650px">

                              <tr style="font-size: 12px; text-align:center; background-color:#3498db; color:#ecf0f1;" height="50px;">
                                <td style="text-align:center">Tipo / Type</td>
                                <td style="text-align:center">Nombre de la organización/Organization name</td>
                                <td style="text-align:center">País / Country</td>
                                <td style="text-align:center">Organismo de Certificación / Certification Entity</td>

                                <td style="text-align:center">Tipo de solicitud / Kind of application</td>
                                <td style="text-align:center">Inicio Periodo de Objeción</td>
                                <td style="text-align:center">Fin período de objeción</td>
                              </tr>
                              <tr style="font-size:12px; color:#2c3e50;">
                                <td style="padding:10px;">Empresa</td>
                                <td style="padding:10px;">'.$informacion_empresa['nombre_empresa'].'</td>
                                <td style="padding:10px;">'.$informacion_empresa['pais'].'</td>
                                <td style="padding:10px;">'.$informacion_empresa['nombre_oc'].'</td>
                                <td style="padding:10px;">Registro</td>
                                <td style="padding:10px;">'.date('d/m/Y', $periodo['fecha_inicio']).'</td>
                                <td style="padding:10px;background-color:#e74c3c; color:#ecf0f1">'.date('d/m/Y', $periodo['fecha_fin']).'</td>
                              </tr>
                            </table>
                        </td>
                      </tr>
                      <tr>
                        <td colspan="2">
                          <p>En caso de cualquier duda o aclaración por favor escribir a <span style="color:red">soporte@d-spp.org</span></p>
                        </td>
                      </tr>
                    </tbody>
                  </table>

                </body>
              </html>
            ';
          }
          $mail->AddAddress($administrador);
          $mail->Subject = utf8_decode($asunto);
          $mail->Body = utf8_decode($mensaje);
          $mail->MsgHTML(utf8_decode($mensaje));
          $mail->Send();
          $mail->ClearAddresses();
          
        }
        //////////////////////// TERMINA ENVIO DE MENSAJES //////////////////////
      }
    }

    // REVISION SEGUNDO MENSAJE 10 DIAS
    if($periodo['estatus_objecion'] == 'ACTIVO'){ 
      $dias = $num_dias2 *(24*60*60);
      $alerta3 = $periodo['fecha_inicio'] + $dias;

      if(!isset($periodo['alerta3'])){ ////// INICIA REVISION SEGUNDO MENSAJE
        if(time() >= $alerta3){
          $updateSQL = sprintf("UPDATE periodo_objecion SET alerta3 = %s WHERE idperiodo_objecion = %s",
            GetSQLValueString($estado, "int"),
            GetSQLValueString($periodo['idperiodo_objecion'], "int"));
          $actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());

          //////////////////////// INICIA ENVIO DE MENSAJES //////////////////////
          if(isset($periodo['idsolicitud_certificacion'])){
            $row_opp = mysql_query("SELECT solicitud_certificacion.idopp, solicitud_certificacion.idoc, opp.nombre AS 'nombre_opp', opp.pais, oc.nombre AS 'nombre_oc' FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp INNER JOIN oc ON solicitud_certificacion.idoc = oc.idoc WHERE idsolicitud_certificacion = $periodo[idsolicitud_certificacion]", $dspp) or die(mysql_error());
            $informacion_opp = mysql_fetch_assoc($row_opp);

            $asunto = "D-SPP | SEGUNDO RECORDATORIO: PERIODO DE OBJECIÓN";

            $mensaje = '
              <html>
                <head>
                  <meta charset="utf-8">
                </head>
                <body>
                
                <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;text-align:justify" border="0" width="650px">
                  <tbody>
                    <tr>
                      <th rowspan="2" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                      <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red"><span style="color:#2c3e50">SEGUNDO RECORDATORIO</span>: PERIODO DE OBJECIÓN</span></p></th>

                    </tr>
                    <tr>
                      <td colspan="2" >
                        <p>
                          Recordatorio enviado del sistema D-SPP, recordandole que el Periodo de Objeción de la Organización: <b style="color:red">'.$informacion_opp['nombre_opp'].'</b> está por concluir (<span style="color:red">faltan 5 días</span>), una vez finalizado el periodo debe de cargar la Resolución de Objeción.
                        </p>
                      </td>
                    </tr>
                    <tr style="width:100%">
                      <td colspan="2">
                          <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px; border:#27ae60" border="1" width="650px">

                            <tr style="font-size: 12px; text-align:center; background-color:#27ae60; color:#ecf0f1;" height="50px;">
                              <td style="text-align:center">Tipo / Type</td>
                              <td style="text-align:center">Nombre de la organización/Organization name</td>
                              <td style="text-align:center">País / Country</td>
                              <td style="text-align:center">Organismo de Certificación / Certification Entity</td>

                              <td style="text-align:center">Tipo de solicitud / Kind of application</td>
                              <td style="text-align:center">Inicio Periodo de Objeción</td>
                              <td style="text-align:center">Fin período de objeción</td>
                            </tr>
                            <tr style="font-size:12px; color:#2c3e50;">
                              <td style="padding:10px;">OPP</td>
                              <td style="padding:10px;">'.$informacion_opp['nombre_opp'].'</td>
                              <td style="padding:10px;">'.$informacion_opp['pais'].'</td>
                              <td style="padding:10px;">'.$informacion_opp['nombre_oc'].'</td>
                              <td style="padding:10px;">Certificación</td>
                              <td style="padding:10px;">'.date('d/m/Y', $periodo['fecha_inicio']).'</td>
                              <td style="padding:10px;background-color:#e74c3c; color:#ecf0f1">'.date('d/m/Y', $periodo['fecha_fin']).'</td>
                            </tr>
                          </table>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="2">
                        <p>En caso de cualquier duda o aclaración por favor escribir a <span style="color:red">soporte@d-spp.org</span></p>
                      </td>
                    </tr>
                  </tbody>
                </table>

                </body>
              </html>
            ';

          }else if(isset($periodo['idsolicitud_registro'])){
            $row_empresa = mysql_query("SELECT solicitud_registro.idempresa, solicitud_registro.idoc, empresa.nombre AS 'nombre_empresa', empresa.pais, oc.nombre AS 'nombre_oc' FROM solicitud_registro INNER JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa INNER JOIN oc ON solicitud_registro.idoc = oc.idoc WHERE idsolicitud_registro = $periodo[idsolicitud_registro]", $dspp) or die(mysql_error());
            $informacion_empresa = mysql_fetch_assoc($row_empresa);

            $asunto = "D-SPP | SEGUNDO RECORDATORIO: PERIODO DE OBJECIÓN";

            $mensaje = '
              <html>
                <head>
                  <meta charset="utf-8">
                </head>
                <body>
                
                  <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;text-align:justify" border="0" width="650px">
                    <tbody>
                      <tr>
                        <th rowspan="2" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                        <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red"><span style="color:#2c3e50">SEGUNDO RECORDATORIO</span>: PERIODO DE OBJECIÓN</span></p></th>

                      </tr>
                      <tr>
                        <td colspan="2" >
                          <p>
                            Recordatorio enviado del sistema D-SPP, recordandole que el Periodo de Objeción de la Empresa: <b style="color:red">'.$informacion_empresa['nombre_empresa'].'</b> está por concluir (<span style="color:red">faltan 5 días</span>), una vez finalizado el periodo debe de cargar la Resolución de Objeción.
                          </p>
                        </td>
                      </tr>
                      <tr style="width:100%">
                        <td colspan="2">
                            <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;border: #3498db" border="1" width="650px">

                              <tr style="font-size: 12px; text-align:center; background-color:#3498db; color:#ecf0f1;" height="50px;">
                                <td style="text-align:center">Tipo / Type</td>
                                <td style="text-align:center">Nombre de la organización/Organization name</td>
                                <td style="text-align:center">País / Country</td>
                                <td style="text-align:center">Organismo de Certificación / Certification Entity</td>

                                <td style="text-align:center">Tipo de solicitud / Kind of application</td>
                                <td style="text-align:center">Inicio Periodo de Objeción</td>
                                <td style="text-align:center">Fin período de objeción</td>
                              </tr>
                              <tr style="font-size:12px; color:#2c3e50;">
                                <td style="padding:10px;">Empresa</td>
                                <td style="padding:10px;">'.$informacion_empresa['nombre_empresa'].'</td>
                                <td style="padding:10px;">'.$informacion_empresa['pais'].'</td>
                                <td style="padding:10px;">'.$informacion_empresa['nombre_oc'].'</td>
                                <td style="padding:10px;">Registro</td>
                                <td style="padding:10px;">'.date('d/m/Y', $periodo['fecha_inicio']).'</td>
                                <td style="padding:10px;background-color:#e74c3c; color:#ecf0f1">'.date('d/m/Y', $periodo['fecha_fin']).'</td>
                              </tr>
                            </table>
                        </td>
                      </tr>
                      <tr>
                        <td colspan="2">
                          <p>En caso de cualquier duda o aclaración por favor escribir a <span style="color:red">soporte@d-spp.org</span></p>
                        </td>
                      </tr>
                    </tbody>
                  </table>

                </body>
              </html>
            ';
          }
          $mail->AddAddress($administrador);
          $mail->Subject = utf8_decode($asunto);
          $mail->Body = utf8_decode($mensaje);
          $mail->MsgHTML(utf8_decode($mensaje));
          $mail->Send();
          $mail->ClearAddresses();

        }
        //////////////////////// TERMINA ENVIO DE MENSAJES //////////////////////
      }
      ////// TERMINA REVISION SEGUNDO MENSAJE

    }
  } //// TERMINA WHILE

?>