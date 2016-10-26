<?php 
	require_once('../Connections/dspp.php');
	require_once('../mpdf/mpdf.php');
	/** Se agrega la libreria PHPExcel */
	require_once '../PHPExcel/PHPExcel.php';
  mysql_select_db($database_dspp, $dspp);
  ////********* ****** GENERAMOS LA LISTA DE OPPS EN PDF
  if(isset($_POST['lista_pdf']) && $_POST['lista_pdf'] == 1){
    $query = $_POST['query_pdf'];
    $row_opp = mysql_query($query,$dspp) or die(mysql_error());


    $html = '

      <header class="clearfix">
        <div>
          <table style="padding:0px;margin:0px;">
        <tr>
          <td style="text-align:left;margin-bottom:0px;:11px;">
                <div>
              <img src="img/FUNDEPPO.jpg" >
                </div>
          </td>
          <td style="text-align:right;:11px;">
                <div>
              <h2>
                Solicitud de Certificación para Organizaciones de Pequeños Productores
              </h2>             
                </div>
                <div>Símbolo de Pequeños Productores</div>
                <div>Versión 7. 26-Ene-2015</div>
          </td>
        </tr>
          </table>
        </div>
      </header>
      <div>
        <table border="1" style="padding:0px;margin:0px;">
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
            <td style="text-align: center;width:400px;">
              SITIO WEB / WEB SITE
            </td>
            <td style="text-align: center;">
              CORREO ELECTRÓNICO / EMAIL
            </td>
            <td style="text-align: center;">
              TELÉFONO / TELEPHONE
            </td>
          </tr>
    ';
    $contador = 1;
    while($opp = mysql_fetch_assoc($row_opp)){
      $row_producto = mysql_query("SELECT * FROM productos WHERE idopp = $opp[idopp]", $dspp) or die(mysql_error());
      $producto = '';
      $total_producto = mysql_num_rows($row_producto);
      $cont = 1;
      while($detalle_producto = mysql_fetch_assoc($row_producto)){
        if($cont < $total_producto){
          $producto .= $detalle_producto['producto'].', ';
        }else{
          $producto .= $detalle_producto['producto'];
        }
        $cont++;
      }

      $fecha = strtotime($opp['vigencia_fin']);
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
        <td style="font-size:12px;text-align: left;">'.$opp['nombre_dspp'].'</td>
        <td style="font-size:12px;text-align: center;">'.$opp['abreviacion_oc'].'</td>
        <td style="font-size:12px;text-align: center;">'.$opp['spp_opp'].'</td>
        <td style="font-size:12px;text-align: left;">'.$opp['sitio_web'].'</td>
        <td style="font-size:12px;text-align: left;">'.$opp['email'].'</td>
        <td style="font-size:12px;text-align: left;">'.$opp['telefono'].'</td>
      </tr>';
      $contador++;
    }
    $html .='
      </table>
    </div>';

    $mpdf = new mPDF('c', 'A2');
    $mpdf->SetHTMLHeader('<div style="text-align: right; font-weight: bold;">My document</div>');
    $css = file_get_contents('css/style.css');  
    $mpdf->AddPage('L');
    $mpdf->writeHTML($css,1);
    $mpdf->writeHTML($html);
    $mpdf->Output('reporte.pdf', 'I');

  }

	////********* ****** GENERAMOS LA LISTA DE OPPS EN EXCEL
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
		$titulosColumnas = array('Nº', 'NOMBRE DE LA ORGANIZACIÓN', 'ABREVIACIÓN', 'PAÍS', 'PRODUCTO(S) CERTIFICADO', 'FECHA SIGUIENTE EVALUACIÓN', 'ESTATUS', 'ENTIDAD QUE OTORGÓ EL CERTIFICADO', '#SPP', 'EMAIL', 'SITIO WEB', 'TELÉFONO');
		
		$objPHPExcel->setActiveSheetIndex(0)
        		    ->mergeCells('A1:L1');
						
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
            		->setCellValue('L3',  $titulosColumnas[11]);
		
		//Se agregan los datos de los alumnos
		$i = 4;
		$contador = 1;
		while ($opp = mysql_fetch_assoc($row_opp)) {
      $fecha = strtotime($opp['vigencia_fin']);
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
            		->setCellValue('G'.$i, 	$opp['estatus_opp'])
            		->setCellValue('H'.$i, 	$opp['abreviacion_oc'])
            		->setCellValue('I'.$i, 	$opp['spp_opp'])
            		->setCellValue('J'.$i, 	$opp['email'])
            		->setCellValue('K'.$i, 	$opp['sitio_web'])
            		->setCellValue('L'.$i, 	$opp['telefono']);
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

		$objPHPExcel->getActiveSheet()->getStyle('A1:L1')->applyFromArray($estiloTituloReporte);
		$objPHPExcel->getActiveSheet()->getStyle('A3:L3')->applyFromArray($estiloTituloColumnas);		
		$objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A4:L".($i-1));
				
		for($i = 'A'; $i <= 'L'; $i++){
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