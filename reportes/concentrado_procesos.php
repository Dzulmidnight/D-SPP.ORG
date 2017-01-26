<?php 
	require_once('../Connections/dspp.php');
	require_once('../mpdf/mpdf.php');
	/** Se agrega la libreria PHPExcel */
	require_once '../PHPExcel/PHPExcel.php';
  mysql_select_db($database_dspp, $dspp);
  ////********* ****** GENERAMOS LA LISTA DE OPPS EN PDF
  if(isset($_POST['reporte_pdf']) && $_POST['reporte_pdf'] == 1){

  $contenido = "";
  $row_pais = mysql_query("SELECT opp.pais FROM opp GROUP BY opp.pais ORDER BY opp.pais ASC", $dspp) or die(mysql_error());

    $contenido .= '<h4>Estatus Solicitudes de Certificación</h4>
    <table class="table table-bordered table-hover table-condensed">
      <thead>
        <tr class="success">
          <th style="font-size:30px;text-align: left;">#</th>
          <th style="font-size:30px;text-align: left;">País</th>
          <th style="font-size:30px;text-align: left;">Solicitud Inicial(<small>Son OPP que han ingresado por primera vez y solo han cargado la solicitud</small>)</th>
          <th style="font-size:30px;text-align: left;">Solicitud(<small>Son OPP nuevas que han ingresado su solicitud y se les ha enviado una cotizacion</small>)</th>
          <th style="font-size:30px;text-align: left;">En Proceso(<small>OPPs que han aceptado la cotización y ha iniciado su proceso de certificacion</small>)</th>
          <th style="font-size:30px;text-align: left;">Evaluación Positiva(<small>OPPs que han finalizado el proceso de certificación con una evaluación positiva</small>)</th>
          <th style="font-size:30px;text-align: left;">Subtotal Proceso</th>
          <th style="font-size:30px;text-align: left;">Certificada(<small>Se incluyen todas las OPPs que se les ha entragado certificado, ya sean nuevas o renovación</small>)</th>
          <th style="font-size:30px;text-align: left;">Inactiva</th>
          <th style="font-size:30px;text-align: left;">Suspendida(<small>OPPs que han sido formalmente suspendidas</small>)</th>
          <th style="font-size:30px;text-align: left;">Expirado(OPPs, que ha expirado las fechas de sus certificados)</th>
          <th style="font-size:30px;text-align: left;">Subtotal Certificación</th>
          <th style="font-size:30px;text-align: left;">Total</th>

       </tr>';


      $contenido .= '</thead>';
      $contenido .= '<tbody>';
        $contador = 1;
        $total_solicitud_inicial = 0;
        $total_solicitud = 0;
        $total_en_proceso = 0;
        $total_ev_positiva = 0;
        $total_sub_total_proceso = 0;
        $total_certificada = 0;
        $total_inactiva = 0;
        $total_suspendida = 0;
        $total_expirado = 0;
        $total_sub_certificacion = 0;
        $total = 0;
        while($pais = mysql_fetch_assoc($row_pais)){
          $num_sub_total_proceso = 0;
          $num_sub_total_certificacion = 0;
          $num_total = 0;
          //query SOLCITIUD INICIAL
          $row_solicitud_inicial = mysql_query("SELECT solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE solicitud_certificacion.tipo_solicitud = 'NUEVA' AND solicitud_certificacion.cotizacion_opp IS NULL AND opp.pais = '$pais[pais]'", $dspp);
          $num_solicitud_inicial = mysql_num_rows($row_solicitud_inicial);
          $total_solicitud_inicial += $num_solicitud_inicial;

          //query SOLICITUD
          $row_solicitud = mysql_query("SELECT solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE solicitud_certificacion.tipo_solicitud = 'NUEVA' AND solicitud_certificacion.cotizacion_opp IS NOT NULL AND solicitud_certificacion.estatus_dspp = 4 AND opp.pais = '$pais[pais]'", $dspp);
          $num_solicitud = mysql_num_rows($row_solicitud);
          $total_solicitud += $num_solicitud;


          //query EN PROCESO,que han aceptado la cotizacion y estan en proceso de certificacion, por lo tanto no pueden tener dictamen positivo, negativo
          $row_en_proceso = mysql_query("SELECT solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE solicitud_certificacion.tipo_solicitud = 'NUEVA' AND solicitud_certificacion.fecha_aceptacion IS NOT NULL AND (solicitud_certificacion.estatus_dspp = 5 || solicitud_certificacion.estatus_dspp = 6 || solicitud_certificacion.estatus_dspp = 7 || solicitud_certificacion.estatus_dspp = 8 || solicitud_certificacion.estatus_dspp = 9) AND (solicitud_certificacion.estatus_interno != 8 || solicitud_certificacion.estatus_interno IS NULL) AND opp.pais = '$pais[pais]'", $dspp);
          $num_en_proceso = mysql_num_rows($row_en_proceso); 
          $total_en_proceso += $num_en_proceso; 

          //query EVALUACION POSITIVA
          $row_ev_positiva = mysql_query("SELECT solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE solicitud_certificacion.tipo_solicitud = 'NUEVA' AND (solicitud_certificacion.estatus_interno = 8 AND solicitud_certificacion.estatus_dspp != 12) AND opp.pais = '$pais[pais]'", $dspp);
          $num_ev_positiva = mysql_num_rows($row_ev_positiva);
          $total_ev_positiva += $num_ev_positiva;  

          //query SUB TOTAL EN PROCESO
          $num_sub_total_proceso = $num_solicitud_inicial + $num_solicitud + $num_en_proceso + $num_ev_positiva;
          $total_sub_total_proceso += $num_sub_total_proceso;

          //query CERTIFICADAS
          $row_certificadas = mysql_query("SELECT opp.idopp, certificado.idopp FROM opp INNER JOIN certificado ON opp.idopp = certificado.idopp WHERE opp.pais = '$pais[pais]' AND (opp.estatus_dspp != 16 AND opp.estatus_interno != 10 AND opp.estatus_interno != 11)", $dspp);
          $num_certificadas = mysql_num_rows($row_certificadas);
          $total_certificada += $num_certificadas;

          //query INACTIVA, en inactivas estamo contando las opp con estatus cancelado(10)
          $row_inactiva = mysql_query("SELECT opp.idopp, certificado.idopp FROM opp INNER JOIN certificado ON opp.idopp = certificado.idopp WHERE opp.pais = '$pais[pais]' AND opp.estatus_interno = 10", $dspp);
          $num_inactiva = mysql_num_rows($row_inactiva);
          $total_inactiva += $num_inactiva;


          //query SUSPENDIDA, en inactivas estamo contando las opp con estatus suspendido(11), falta ver con alejandra si se dejan esta, ya que falta checar lo de "suspencion formal"
          $row_suspendida = mysql_query("SELECT opp.idopp, certificado.idopp FROM opp INNER JOIN certificado ON opp.idopp = certificado.idopp WHERE opp.pais = '$pais[pais]' AND opp.estatus_interno = 11", $dspp);
          $num_suspendida = mysql_num_rows($row_suspendida);
          $total_suspendida += $num_suspendida;

          //query EXPIRADO, se cuentan las OPP con estatus_dspp = certificado expirado y que no tengan estatus_interno "CANCELADO"
          $row_expirado = mysql_query("SELECT opp.idopp, certificado.idopp FROM opp INNER JOIN certificado ON opp.idopp = certificado.idopp WHERE opp.pais = '$pais[pais]' AND (opp.estatus_dspp = 16 AND opp.estatus_interno != 10)", $dspp);
          $num_expirado = mysql_num_rows($row_expirado);
          $total_expirado += $num_expirado;

          // num subtotal certificacion
          $num_sub_total_certificacion = $num_certificadas + $num_inactiva + $num_suspendida + $num_expirado;
          $total_sub_certificacion += $num_sub_total_certificacion;

          //num TOTAL
          $num_total = $num_sub_total_proceso + $num_sub_total_certificacion;
          $total += $num_total;
    
        $contenido .= '<tr>
          <td style="font-size:30px;text-align: left;">'.$contador.'</td>
          <td style="font-size:30px;text-align: left;">'.$pais['pais'].'</td>
          <!--INICIA SOLICITUD INICIAL: debemos seleccionar las nuevas OPPs-->
          <td style="font-size:30px;text-align: left;">'.$num_solicitud_inicial.'</td>

          <!--INICIA SOLICITUD-->
          <td style="font-size:30px;text-align: left;">'.$num_solicitud.'</td>

          <!--INICIA EN PROCESO-->
          <td style="font-size:30px;text-align: left;">'.$num_en_proceso.'</td>

          <!--INICIA EVALUACION POSITIVA-->
          <td style="font-size:30px;text-align: left;">'.$num_ev_positiva.'</td>

          <!--INICIA SUBTOTAL EN PROCESO-->
          <td class="success text-center">'.$num_sub_total_proceso.'</td>

          <!--INICIA CERTIFICAD-->
          <td style="font-size:30px;text-align: left;">'.$num_certificadas.'</td>

          <!--INICIA INACTIVA-->
          <td style="font-size:30px;text-align: left;">'.$num_inactiva.'</td>

          <!--INICIA SUSPENDIDA-->
          <td style="font-size:30px;text-align: left;">'.$num_suspendida.'</td>

          <!--INICIA EXPIRADO-->
          <td style="font-size:30px;text-align: left;">'.$num_expirado.'</td>

          <!--INICIA SUBTOTAL CERTIFICACION-->
          <td style="font-size:30px;text-align: left;">'.$num_sub_total_certificacion.'</td>

          <!--INICIA TOTAL-->
          <td style="font-size:30px;text-align: left;background-color:#e74c3c;color:#ecf0f1">'.$num_total.'</td>
        </tr>';

        $contador++;
        }
         $contenido .= '<tr>
           <td style="font-size:30px;text-align: left;" colspan="2">Total</td>
           <td style="font-size:30px;text-align: left;">'.$total_solicitud_inicial.'</td>
           <td style="font-size:30px;text-align: left;">'.$total_solicitud.'</td>
           <td style="font-size:30px;text-align: left;">'.$total_en_proceso.'</td>
           <td style="font-size:30px;text-align: left;">'.$total_ev_positiva.'</td>
           <td class="success text-center">'.$total_sub_total_proceso.'</td>
           <td style="font-size:30px;text-align: left;">'.$total_certificada.'</td>
           <td style="font-size:30px;text-align: left;">'.$total_inactiva.'</td>
           <td style="font-size:30px;text-align: left;">'.$total_suspendida.'</td>
           <td style="font-size:30px;text-align: left;">'.$total_expirado.'</td>
           <td class="success text-center">'.$total_sub_certificacion.'</td>
           <td style="font-size:30px;text-align: left;background-color:#e74c3c;color:#ecf0f1">'.$total.'</td>
         </tr>
      </tbody>
    </table>';


    $mpdf = new mPDF('c', 'A2');
    $mpdf->setAutoTopMargin = 'pad'; //activamos el margin-top para que respete el header
    $mpdf->keep_table_proportions = TRUE; //deshabilitamos el auto-size de la tabla
    $mpdf->SetHTMLHeader('
    <header class="clearfix">
      <div>
        <table style="padding:0px;margin-top:-20px;">
          <tr>
            <td style="text-align:left;margin-bottom:0px;">
                  <div>
                <img src="img/FUNDEPPO.jpg" >
                  </div>
            </td>
            <td style="text-align:right;font-size:12px;">
                  <div>
                <h2>
                  Concetrado Procesos Certificación - Registro
                </h2>             
                  </div>
                  <div>Símbolo de Pequeños Productores</div>
                  <div>'.date('d/m/Y', time()).'</div>
            </td>
          </tr>
        </table>
      </div>
    </header>
      ');
    $css = file_get_contents('css/style_concentrado.css');  
    $mpdf->AddPage('L');


    $mpdf->pagenumPrefix = 'Página / Page ';
    $mpdf->pagenumSuffix = ' - ';
    $mpdf->nbpgPrefix = ' de ';
    //$mpdf->nbpgSuffix = ' pages';
    $mpdf->SetFooter('{PAGENO}{nbpg}');
    $mpdf->writeHTML($css,1);
    $mpdf->writeHTML($contenido);
    $mpdf->Output('concentrado_procesos cert-reg.pdf', 'I');
  }

  if(isset($_POST['lista_pdf']) && $_POST['lista_pdf'] == 1){
    $query = $_POST['query_pdf'];
    $row_opp = mysql_query($query,$dspp) or die(mysql_error());


    $html = '

      <div>
        <table border="1" >
          <tr style="background-color:#B8D186">
            <td style="text-align: center;">#</td>
            <td style="text-align: center;">
              NOMBRE DE LA ORGANIZACIÓN / ORGANIZATION´S NAME
            </td>
            <td style="text-align: center;">
              ABREVIACIÓN / SHORT NAME
            </td>
            <td style="text-align: center;">
              PAÍS / COUNTRY
            </td>
            <td style="text-align: center;">
              PRODUCTO(S) CERTIFICADO/ CERTIFIED PRODUCTS
            </td>
            <td style="text-align: center;">
              FECHA SIGUIENTE EVALUACIÓN/ NEXT EVALUATION DATE
            </td>
            <td style="text-align: center;">
              ESTATUS / STATUS
            </td>
            <td style="text-align: center;">
              ENTIDAD QUE OTORGÓ EL CERTIFICADO / ENTITY THAT GRANTED CERTIFICATE
            </td>
            <td style="text-align: center;">
              IDENTIFICACIÓN / IDENTIFICATION
            </td>
            <td style="text-align: center;">
              SITIO WEB / WEB SITE
            </td>
            <td style="text-align: center;">
              CORREO ELECTRÓNICO / EMAIL
            </td>
            <td style="text-align: center;">
              TELÉFONO / TELEPHONE
            </td>
            <td style="text-align: center;">
              CONTACTOS / CONTACTS
            </td>

          </tr>
    ';
    $contador = 1;
    while($opp = mysql_fetch_assoc($row_opp)){
      $row_producto = mysql_query("SELECT * FROM productos WHERE idopp = $opp[idopp]", $dspp) or die(mysql_error());
      $producto = '';
      $total_producto = mysql_num_rows($row_producto);
      $row_contactos = mysql_query("SELECT * FROM contactos WHERE idopp = $opp[idopp]", $dspp) or die(mysql_error());
      $total_contactos = mysql_num_rows($row_contactos);

      $cont = 1;
      while($detalle_producto = mysql_fetch_assoc($row_producto)){
        if($cont < $total_producto){
          $producto .= $detalle_producto['producto'].', ';
        }else{
          $producto .= $detalle_producto['producto'];
        }
        $cont++;
      }

      $fecha = strtotime($opp['fecha_fin']);
      if(!empty($fecha)){
        $vigencia = date('d/m/Y', $fecha);
      }else{
        $vigencia = '<p style="color:#e74c3c">No Disponible</p>';
      }

      $html .= '
      <tr>
        <td style="font-size:12px;text-align: left;">'.$contador.'</td>
        <td style="font-size:12px;text-align: left;">'.$opp['nombre'].'</td>
        <td style="font-size:12px;text-align: left;">'.$opp['abreviacion_opp'].'</td>
        <td style="font-size:12px;text-align: center;">'.$opp['pais'].'</td>
        <td style="font-size:12px;text-align: left;">'.$producto.'</td>
        <td style="font-size:12px;text-align: center;">'.$vigencia.'</td>
        <td style="font-size:12px;text-align: left;">'.$opp['nombre_publico'].'</td>
        <td style="font-size:12px;text-align: center;">'.$opp['abreviacion_oc'].'</td>
        <td style="font-size:12px;text-align: center;">'.$opp['spp_opp'].'</td>
        <td style="font-size:12px;text-align: left;width: 10%">'.$opp['sitio_web'].'</td>
        <td style="font-size:12px;text-align: left;width: 10%">'.$opp['email'].'</td>
        <td style="font-size:12px;text-align: left;">'.$opp['telefono'].'</td>
        <td style="font-size:12px;text-align: left;">';
        if($total_contactos){
          $html .= '
            <table border="1" style="margin:0px;padding:0px;">
              <tr style="width:400px;">
                <td style="text-align: center;">Nombre</td>
                <td style="text-align: center;">Cargo</td>
                <td style="text-align: center;">Email</td>
                <td style="text-align: center;">Telefono</td>
              </tr>';
              while($contactos = mysql_fetch_assoc($row_contactos)){
                $html .= '
                <tr style="width:400px;">
                  <td style="text-align: left;">'.$contactos['nombre'].'</td>
                  <td style="text-align: left;">'.$contactos['cargo'].'</td>
                  <td style="text-align: left;">
                    Email 1: <span style="color:red">'.$contactos['email1'].'</span><br>
                    Email 2: <span style="color:red">'.$contactos['email2'].'</span>
                  </td>                
                  <td style="text-align: left;">
                    Tel 1: <span style="color:red">'.$contactos['telefono1'].'</span><br>
                    Tel 2: <span style="color:red">'.$contactos['telefono2'].'</span>
                  </td>
                </tr>';
              }
        $html .= '
            </table>';
        }else{
          $html .= '<strong style="color:red">No Disponible</strong>';
        }
      $html .= '
        </td>
      </tr>';
      $contador++;
    }
    $html .='
      </table>
      <h2>NOTAS:</h2>
      <h2>1. El estatus de \'En Revisión\' significa que la OPP puede encontrarse en cualquiera de los siguientes sub estatus: \'En proceso de renovación\', \'Certificado expirado\' o \'Suspendido\' </h2>
      <h2>2. Es responsabilidad de los interesados verificar si la OPP se encuentran en proceso de renovación del certificado, cuando en la presente lista se indica que el estatus es "En Revisión"</h2>
      <h2>3. El estatus de \'Cancelado\' siginifica que la OPP ya no esta certificada por Incumplimiento con el Marco Regulatorio SPP o por renuncia voluntaria. Si fue cancelado por incumpliento con el marco regulatorio, deberá esperar dos años a partir de la cancelación para volver a solicitar la certificación.</h2>

    </div>';

    $mpdf = new mPDF('c', 'A2');
    $mpdf->setAutoTopMargin = 'pad';
    $mpdf->keep_table_proportions = TRUE;
    $mpdf->SetHTMLHeader('
    <header class="clearfix">
      <div>
        <table style="padding:0px;margin-top:-20px;">
          <tr>
            <td style="text-align:left;margin-bottom:0px;font-size:12px;">
                  <div>
                <img src="img/FUNDEPPO.jpg" >
                  </div>
            </td>
            <td style="text-align:right;font-size:12px;">
                  <div>
                <h2>
                  Lista de Organizaciones de Pequeños Productores /List of Small Producers´ Organizations
                </h2>             
                  </div>
                  <div>Símbolo de Pequeños Productores</div>
                  <div>'.date('d/m/Y', time()).'</div>
            </td>
          </tr>
        </table>
      </div>
    </header>
      ');
    $css = file_get_contents('css/style.css');  
    $mpdf->AddPage('L');
    $mpdf->pagenumPrefix = 'Página / Page ';
    $mpdf->pagenumSuffix = ' - ';
    $mpdf->nbpgPrefix = ' de ';
    //$mpdf->nbpgSuffix = ' pages';
    $mpdf->SetFooter('{PAGENO}{nbpg}');
    $mpdf->writeHTML($css,1);
    $mpdf->writeHTML($html);
    $mpdf->Output('reporte.pdf', 'I');

  }

	////********* ****** GENERAMOS LA LISTA DE OPPS EN EXCEL
  if(isset($_POST['lista_publica_excel']) && $_POST['lista_publica_excel'] == 2){
    $query = $_POST['query_excel'];
    $row_opp = mysql_query($query,$dspp) or die(mysql_error()); 
    // Se crea el objeto PHPExcel
    $objPHPExcel = new PHPExcel();

    // Se asignan las propiedades del libro
    $objPHPExcel->getProperties()->setCreator("spp global") //Autor
               ->setLastModifiedBy("spp global") //Ultimo usuario que lo modificó
               ->setTitle("LISTA ORGANIZACIONES DE PEQUEÑOS PRODUCTORES")
               ->setSubject("LISTA ORGANIZACIONES DE PEQUEÑOS PRODUCTORES")
               ->setDescription("LISTA ORGANIZACIONES DE PEQUEÑOS PRODUCTORES")
               ->setKeywords("LISTA ORGANIZACIONES DE PEQUEÑOS PRODUCTORES")
               ->setCategory("LISTA ORGANIZACIONES");

    $tituloReporte = "Lista de Organizaciones de Pequeños Productores /List of Small Producers´ Organizations ";
    $titulosColumnas = array('Nº', 'NOMBRE DE LA ORGANIZACIÓN', 'ABREVIACIÓN', 'PAÍS', 'PRODUCTO(S) CERTIFICADO', 'FECHA SIGUIENTE EVALUACIÓN', 'ENTIDAD QUE OTORGÓ EL CERTIFICADO', '#SPP', 'EMAIL', 'SITIO WEB', 'TELÉFONO');
    
    $objPHPExcel->setActiveSheetIndex(0)
                ->mergeCells('A1:K1');
            
    // Se agregan los titulos del reporte
    $objPHPExcel->setActiveSheetIndex(0)
          ->setCellValue('A1',$tituloReporte)
                ->setCellValue('A3',  $titulosColumnas[0])
                ->setCellValue('B3',  $titulosColumnas[1])
                ->setCellValue('C3',  $titulosColumnas[2])
                ->setCellValue('D3',  $titulosColumnas[3])
                ->setCellValue('E3',  $titulosColumnas[4])
                ->setCellValue('F3',  $titulosColumnas[5])
                ->setCellValue('G3',  $titulosColumnas[6])
                ->setCellValue('H3',  $titulosColumnas[7])
                ->setCellValue('I3',  $titulosColumnas[8])
                ->setCellValue('J3',  $titulosColumnas[9])
                ->setCellValue('K3',  $titulosColumnas[10]);
    
    //Se agregan los datos de los alumnos
    $i = 4;
    $contador = 1;
    while ($opp = mysql_fetch_assoc($row_opp)) {
      $fecha = strtotime($opp['fecha_fin']);
      if(!empty($fecha)){
        $vigencia = date('d/m/Y', $fecha);
      }else{
        $vigencia = 'No Disponible';
      }
      $productos = '';
      $query_producto = mysql_query("SELECT * FROM productos WHERE idopp = $opp[idopp]", $dspp) or die(mysql_error());
      $total = mysql_num_rows($query_producto);
      $cont = 1;
      while($row_producto = mysql_fetch_assoc($query_producto)){
        if($cont < $total){
          $productos .= $row_producto['producto'].", ";
        }else{
          $productos .= $row_producto['producto'];
        }
        $cont++;
      }

      $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$i,  $contador)
                ->setCellValue('B'.$i,  $opp['nombre'])
                ->setCellValue('C'.$i,  $opp['abreviacion'])
                ->setCellValue('D'.$i,  $opp['pais'])
                ->setCellValue('E'.$i,  $productos)
                ->setCellValue('F'.$i,  $vigencia)
                ->setCellValue('G'.$i,  $opp['abreviacion_oc'])
                ->setCellValue('H'.$i,  $opp['spp'])
                ->setCellValue('I'.$i,  $opp['email'])
                ->setCellValue('J'.$i,  $opp['sitio_web'])
                ->setCellValue('K'.$i,  $opp['telefono']);
          $i++;
          $contador++;
    }
    $estiloTituloReporte = array(
          'font' => array(
            'name'      => 'Verdana',
              'bold'      => true,
              'italic'    => false,
                'strike'    => false,
                'size' =>16,
                'color'     => array(
                    'rgb' => '000000'
                  )
            ),
          'fill' => array(
        'type'  => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb' => 'FFFFFF')
      ),
            'borders' => array(
                'allborders' => array(
                  'style' => PHPExcel_Style_Border::BORDER_NONE                    
                )
            ), 
            'alignment' =>  array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
              'rotation'   => 0,
              'wrap'          => TRUE
        )
        );

    $estiloTituloColumnas = array(
            'font' => array(
                'name'      => 'Arial',
                'bold'      => true,                          
                'color'     => array(
                    'rgb' => '#191919'
                )
            ),
            /*'fill'  => array(
        'type'    => PHPExcel_Style_Fill::FILL_SOLID,
        'color'   => array('argb' => 'FFd9b7f4')
      ),*/

            'fill'  => array(
        'type'    => PHPExcel_Style_Fill::FILL_SOLID,
        'color'   => array('rgb' => 'B8D186')
      ),
            'borders' => array(
              'top'     => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM ,
                    'color' => array(
                        'rgb' => '143860'
                    )
                ),
                'bottom'     => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM ,
                    'color' => array(
                        'rgb' => '143860'
                    )
                )
            ),
      'alignment' =>  array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
              'wrap'          => TRUE
        ));

    $estiloInformacion = new PHPExcel_Style();
    $estiloInformacion->applyFromArray(
      array(
              'font' => array(
                'name'      => 'Arial',               
                'color'     => array(
                    'rgb' => '000000'
                )
            ),
            /*'fill'  => array(
        'type'    => PHPExcel_Style_Fill::FILL_SOLID,
        'color'   => array('argb' => 'FFd9b7f4')
      ),*/
            'borders' => array(
                'left'     => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN ,
                  'color' => array(
                    'rgb' => '3a2a47'
                    )
                )             
            )
        ));

    $objPHPExcel->getActiveSheet()->getStyle('A1:K1')->applyFromArray($estiloTituloReporte);
    $objPHPExcel->getActiveSheet()->getStyle('A3:K3')->applyFromArray($estiloTituloColumnas);   
    $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A4:K".($i-1));
        
    for($i = 'A'; $i <= 'K'; $i++){
      $objPHPExcel->setActiveSheetIndex(0)      
        ->getColumnDimension($i)->setAutoSize(TRUE);
    }
    
    // Se asigna el nombre a la hoja
    $objPHPExcel->getActiveSheet()->setTitle('Lista organizaciones');

    // Se activa la hoja para que sea la que se muestre cuando el archivo se abre
    $objPHPExcel->setActiveSheetIndex(0);
    // Inmovilizar paneles 
    //$objPHPExcel->getActiveSheet(0)->freezePane('A4');
    $objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(0,4);

    // Se manda el archivo al navegador web, con el nombre que se indica (Excel2007)
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Lista_organizaciones.xls"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    exit;
  }
	if(isset($_POST['lista_excel']) && $_POST['lista_excel'] == 2){
		$query = $_POST['query_excel'];
		$row_opp = mysql_query($query,$dspp) or die(mysql_error());	
		// Se crea el objeto PHPExcel
		$objPHPExcel = new PHPExcel();

		// Se asignan las propiedades del libro
		$objPHPExcel->getProperties()->setCreator("spp global") //Autor
							 ->setLastModifiedBy("spp global") //Ultimo usuario que lo modificó
							 ->setTitle("Reporte Excel con PHP y MySQL")
							 ->setSubject("Reporte Excel con PHP y MySQL")
							 ->setDescription("Reporte de alumnos")
							 ->setKeywords("reporte alumnos carreras")
							 ->setCategory("Reporte excel");

		$tituloReporte = "Lista de Organizaciones de Pequeños Productores";
		$titulosColumnas = array('Nº', 'NOMBRE DE LA ORGANIZACIÓN', 'ABREVIACIÓN', 'PAÍS', 'PRODUCTO(S) CERTIFICADO', 'FECHA SIGUIENTE EVALUACIÓN', 'ESTATUS', 'ENTIDAD QUE OTORGÓ EL CERTIFICADO', '#SPP', 'PASSWORD', 'EMAIL', 'SITIO WEB', 'TELÉFONO');
		
		$objPHPExcel->setActiveSheetIndex(0)
        		    ->mergeCells('A1:M1');
						
		// Se agregan los titulos del reporte
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1',$tituloReporte)
        		    ->setCellValue('A3',  $titulosColumnas[0])
		            ->setCellValue('B3',  $titulosColumnas[1])
        		    ->setCellValue('C3',  $titulosColumnas[2])
            		->setCellValue('D3',  $titulosColumnas[3])
            		->setCellValue('E3',  $titulosColumnas[4])
            		->setCellValue('F3',  $titulosColumnas[5])
            		->setCellValue('G3',  $titulosColumnas[6])
            		->setCellValue('H3',  $titulosColumnas[7])
            		->setCellValue('I3',  $titulosColumnas[8])
            		->setCellValue('J3',  $titulosColumnas[9])
            		->setCellValue('K3',  $titulosColumnas[10])
                ->setCellValue('L3',  $titulosColumnas[10])
            		->setCellValue('M3',  $titulosColumnas[11]);
		
		//Se agregan los datos de los alumnos
		$i = 4;
		$contador = 1;
		while ($opp = mysql_fetch_assoc($row_opp)) {
      $fecha = strtotime($opp['fecha_fin']);
      if(!empty($fecha)){
        $vigencia = date('d/m/Y', $fecha);
      }else{
        $vigencia = 'No Disponible';
      }
      $productos = '';
      $query_producto = mysql_query("SELECT * FROM productos WHERE idopp = $opp[idopp]", $dspp) or die(mysql_error());
      $total = mysql_num_rows($query_producto);
      $cont = 1;
      while($row_producto = mysql_fetch_assoc($query_producto)){
        if($cont < $total){
          $productos .= $row_producto['producto'].", ";
        }else{
          $productos .= $row_producto['producto'];
        }
        $cont++;
      }

			$objPHPExcel->setActiveSheetIndex(0)
        		    ->setCellValue('A'.$i,  $contador)
		            ->setCellValue('B'.$i,  $opp['nombre'])
        		    ->setCellValue('C'.$i,  $opp['abreviacion_opp'])
            		->setCellValue('D'.$i, 	$opp['pais'])
            		->setCellValue('E'.$i, 	$productos)
            		->setCellValue('F'.$i, 	$vigencia)
            		->setCellValue('G'.$i, 	$opp['nombre_publico'])
            		->setCellValue('H'.$i, 	$opp['abreviacion_oc'])
            		->setCellValue('I'.$i, 	$opp['spp_opp'])
                ->setCellValue('J'.$i,  $opp['password'])
            		->setCellValue('K'.$i, 	$opp['email'])
            		->setCellValue('L'.$i, 	$opp['sitio_web'])
            		->setCellValue('M'.$i, 	$opp['telefono']);
					$i++;
					$contador++;
		}
		$estiloTituloReporte = array(
        	'font' => array(
	        	'name'      => 'Verdana',
    	        'bold'      => true,
        	    'italic'    => false,
                'strike'    => false,
               	'size' =>16,
	            	'color'     => array(
    	            	'rgb' => '000000'
        	       	)
            ),
	        'fill' => array(
				'type'	=> PHPExcel_Style_Fill::FILL_SOLID,
				'color'	=> array('rgb' => 'FFFFFF')
			),
            'borders' => array(
               	'allborders' => array(
                	'style' => PHPExcel_Style_Border::BORDER_NONE                    
               	)
            ), 
            'alignment' =>  array(
        			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        			'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        			'rotation'   => 0,
        			'wrap'          => TRUE
    		)
        );

		$estiloTituloColumnas = array(
            'font' => array(
                'name'      => 'Arial',
                'bold'      => true,                          
                'color'     => array(
                    'rgb' => '#191919'
                )
            ),
           	/*'fill' 	=> array(
				'type'		=> PHPExcel_Style_Fill::FILL_SOLID,
				'color'		=> array('argb' => 'FFd9b7f4')
			),*/

           	'fill' 	=> array(
				'type'		=> PHPExcel_Style_Fill::FILL_SOLID,
				'color'		=> array('rgb' => 'B8D186')
			),
            'borders' => array(
            	'top'     => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM ,
                    'color' => array(
                        'rgb' => '143860'
                    )
                ),
                'bottom'     => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM ,
                    'color' => array(
                        'rgb' => '143860'
                    )
                )
            ),
			'alignment' =>  array(
        			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        			'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        			'wrap'          => TRUE
    		));

		$estiloInformacion = new PHPExcel_Style();
		$estiloInformacion->applyFromArray(
			array(
           		'font' => array(
               	'name'      => 'Arial',               
               	'color'     => array(
                   	'rgb' => '000000'
               	)
           	),
           	/*'fill' 	=> array(
				'type'		=> PHPExcel_Style_Fill::FILL_SOLID,
				'color'		=> array('argb' => 'FFd9b7f4')
			),*/
           	'borders' => array(
               	'left'     => array(
                   	'style' => PHPExcel_Style_Border::BORDER_THIN ,
	                'color' => array(
    	            	'rgb' => '3a2a47'
                   	)
               	)             
           	)
        ));

		$objPHPExcel->getActiveSheet()->getStyle('A1:M1')->applyFromArray($estiloTituloReporte);
		$objPHPExcel->getActiveSheet()->getStyle('A3:M3')->applyFromArray($estiloTituloColumnas);		
		$objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A4:M".($i-1));
				
		for($i = 'A'; $i <= 'M'; $i++){
			$objPHPExcel->setActiveSheetIndex(0)			
				->getColumnDimension($i)->setAutoSize(TRUE);
		}
		
		// Se asigna el nombre a la hoja
		$objPHPExcel->getActiveSheet()->setTitle('Lista organizaciones');

		// Se activa la hoja para que sea la que se muestre cuando el archivo se abre
		$objPHPExcel->setActiveSheetIndex(0);
		// Inmovilizar paneles 
		//$objPHPExcel->getActiveSheet(0)->freezePane('A4');
		$objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(0,4);

		// Se manda el archivo al navegador web, con el nombre que se indica (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="Lista_organizaciones.xls"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}
 ?>