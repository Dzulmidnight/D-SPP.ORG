<?php 
  require_once('../Connections/dspp.php');
  require_once('../mpdf/mpdf.php');
  /** Se agrega la libreria PHPExcel */
  require_once '../PHPExcel/PHPExcel.php';
  mysql_select_db($database_dspp, $dspp);
  function mayuscula($variable) {
    $variable = strtr(strtoupper($variable),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
    return $variable;
  }



    $idsolicitud_certificacion = $_POST['idsolicitud_certificacion'];

    $query = "SELECT solicitud_certificacion.*, oc.idoc, oc.nombre AS 'nombre_oc', opp.spp AS 'spp_opp', opp.nombre AS 'nombre_opp', opp.direccion_oficina, opp.pais, opp.email AS 'email_opp', opp.sitio_web, opp.telefono AS 'telefono_opp', opp.rfc, opp.ciudad AS 'ciudad_opp', porcentaje_productoVentas.organico, porcentaje_productoVentas.comercio_justo, porcentaje_productoVentas.spp, porcentaje_productoVentas.sin_certificado FROM solicitud_certificacion INNER JOIN oc ON solicitud_certificacion.idoc = oc.idoc LEFT JOIN opp ON solicitud_certificacion.idopp = opp.idopp LEFT JOIN porcentaje_productoVentas ON solicitud_certificacion.idsolicitud_certificacion = porcentaje_productoVentas.idsolicitud_certificacion WHERE solicitud_certificacion.idsolicitud_certificacion = $idsolicitud_certificacion";

    $row_solicitud = mysql_query($query,$dspp) or die(mysql_error()); 

    $solicitud = mysql_fetch_assoc($row_solicitud);

if($solicitud['produccion']){
      $alcance_opp .= 'PRODUCCIÓN - ';
    }else if($solicitud['procesamiento']){
    $alcance_opp .= 'PROCESAMIENTO - ';
    }else if($solicitud['exportacion']){
    $alcance_opp .= 'EXPORTACIÓN - ';
    }


  $query_certificaciones = "SELECT * FROM certificaciones WHERE idsolicitud_certificacion = '$solicitud[idsolicitud_certificacion]'";
    $row_certificaciones = mysql_query($query_certificaciones, $dspp) or die(mysql_error());

    $total_certificaciones = mysql_num_rows($row_certificaciones);

    $query_productos = "SELECT * FROM productos WHERE idsolicitud_certificacion = '$solicitud[idsolicitud_certificacion]'";
    $row_productos = mysql_query($query_productos, $dspp) or die(mysql_error());


    $nomCertificaciones = 29;
    $subPreguntas = ($nomCertificaciones + $total_certificaciones) + 1;
      $subRespuestas = $subPreguntas + 1;

    $encabezadoProductos = $subRespuestas + 2;
      $subEncabezados = $encabezadoProductos + 1;
        $nomProductos = $subEncabezados + 1;

    // Se crea el objeto PHPExcel
    $objPHPExcel = new PHPExcel();


    $estiloEncabezado = array(
      /// fuente
      'font' => array(
        'color' => array('rgb' => 'ffffff')
      ),
      /// relleno
      'fill'  => array(
        'type'    => PHPExcel_Style_Fill::FILL_SOLID,
        'color'   => array('rgb' => '1C2015')
      ),
      /// bordes
      'borders' => array(
          'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_HAIR
          )
      ),
      /// alineación
      'alignment' =>  array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
              'wrap'          => TRUE
      )
    );

    $estiloTituloColumnas = array(
            
      'fill'  => array(
        'type'    => PHPExcel_Style_Fill::FILL_SOLID,
        'color'   => array('rgb' => 'B8D186')
      ),
      'borders' => array(
          'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_HAIR
          )
      )
    );
      


    $estiloPreguntas = array(
            
      'fill'  => array(
        'type'    => PHPExcel_Style_Fill::FILL_SOLID,
        'color'   => array('rgb' => 'ecf0f1')
      ),
      'borders' => array(
          'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_HAIR
          )
      ),
      'alignment' =>  array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
              'wrap'          => TRUE
        )
    );

    // Se asignan las propiedades del libro
    $objPHPExcel->getProperties()->setCreator("spp global") //Autor
               ->setLastModifiedBy("spp global") //Ultimo usuario que lo modificó
               ->setTitle("SOLICITUD DE CERTIFICACIÓN")
               ->setSubject("SOLICITUD DE CERTIFICACIÓN")
               ->setDescription("SOLICITUD DE CERTIFICACIÓN")
               ->setKeywords("SOLICITUD DE CERTIFICACIÓN")
               ->setCategory("SOLICITUD DE CERTIFICACIÓN");

    $tituloReporte = "SOLICITUD DE CERTIFICACIÓN";

          
    ///// HOJA DE DATOS GENERALES ////
    $objPHPExcel->setActiveSheetIndex(0)
          //// ENCABEZADOS
          ->setCellValue('A1', 'CAMPOS')
          ->setCellValue('B1', 'VALOR')

          //// datos generales
          ->setCellValue('A2','FECHA DE ELABORACIÓN')
            ->setCellValue('B2', date('Y-m-d', $solicitud['fecha_registro']))
          ->setCellValue('A3', 'TIPO DE SOLICITUD')
            ->setCellValue('B3', $solicitud['tipo_solicitud'])
          ->setCellValue('A4', 'CODIGO DE IDENTIFICACIÓN SPP')
            ->setCellValue('B4', $solicitud['spp_opp'])
          ->setCellValue('A5', 'TIPO DE PROCEDIMIENTO DE CERTIFICACIÓN')
            ->setCellValue('B5', $solicitud['tipo_procedimiento'])
          ->setCellValue('A6', 'NOMBRE COMPLETO DE LA ORGANIZACIÓN')
            ->setCellValue('B6', $solicitud['nombre_opp'])
          ->setCellValue('A7', 'DIRECCIÓN COMPLETA DE SUS OFICINAS')
            ->setCellValue('B7', $solicitud['direccion_oficina'])
          ->setCellValue('A8', 'PAÍS')
            ->setCellValue('B8', $solicitud['pais'])
          ->setCellValue('A9', 'CORREO ELECTRONICO')
            ->setCellValue('B9', $solicitud['email_opp'])
          ->setCellValue('A10', 'TELEFONO DE LA ORGANIZACIÓN')
            ->setCellValue('B10', $solicitud['telefono_opp'])
          ->setCellValue('A11', 'SITIO WEB')
            ->setCellValue('B11', $solicitud['sitio_web'])
          ->setCellValue('A12', 'DIRECCIÓN FISCAL')
            ->setCellValue('B12', $solicitud['direccion_fiscal'])
          ->setCellValue('A13', 'RFC')
            ->setCellValue('B13', $solicitud['rfc'])
          ->setCellValue('A14', 'RUC')
            ->setCellValue('B14', $solicitud['ruc']);

          /// personas de contacto
          $row_contactos = mysql_query("SELECT * FROM contactos WHERE idsolicitud_certificacion = $idsolicitud_certificacion", $dspp) or die(mysql_error());
          $contador0 = 1;
          $contador = 16;
          $contador1_1 = 16;
          while($contacto = mysql_fetch_assoc($row_contactos)){
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$contador++, 'CONTACTO '.$contador0)
              ->setCellValue('B'.$contador1_1++, $contacto['nombre'])
            ->setCellValue('A'.$contador++, 'CARGO '.$contador0)
              ->setCellValue('B'.$contador1_1++, $contacto['cargo'])
            ->setCellValue('A'.$contador++, 'CORREO ELECTRONICO '.$contador0)
              ->setCellValue('B'.$contador1_1++, $contacto['email1'])
            ->setCellValue('A'.$contador++, 'TELEFONO '.$contador0)
              ->setCellValue('B'.$contador1_1++, $contacto['telefono1']);

            $contador++;
            $contador1_1++;
            $contador0++;

          }

          $contador++;
          $contador1_2 = $contador;

    $objPHPExcel->setActiveSheetIndex(0)
          //// datos de operación
          ->setCellValue('A'.$contador++, 'NÚMERO DE SOCIOS PRODUCTORES')
            ->setCellValue('B'.$contador1_2++, $solicitud['resp1'])
          ->setCellValue('A'.$contador++, 'NÚMERO DE SOCIOS PRODUCTORES DEL (DE LOS) PRODUCTO(S) A INCLUIR EN LA CERTIFICACIÓN ')
            ->setCellValue('B'.$contador1_2++, $solicitud['resp2'])
          ->setCellValue('A'.$contador++, 'VOLUMEN(ES) DE PRODUCCIÓN TOTAL POR PRODUCTO (UNIDAD DE MEDIDA) ')
            ->setCellValue('B'.$contador1_2++, $solicitud['resp3'])
          ->setCellValue('A'.$contador++, 'TAMAÑO MÁXIMO DE LA UNIDAD DE PRODUCCIÓN POR PRODUCTOR DEL (DE LOS) PRODUCTO(S) A INCLUIR EN LA CERTIFICACIÓN:')
            ->setCellValue('B'.$contador1_2++, $solicitud['resp4'])
          ->setCellValue('A'.$contador++, '1.- EXPLIQUE SI SE TRATA DE UNA ORGANIZACIÓN DE PEQUEÑOS PRODUCTORES DE 1ER, 2DO, 3ER O 4TO GRADO, ASÍ COMO EL NÚMERO DE OPP DE 3ER, 2DO O 1ER GRADO, Y EL NÚMERO DE COMUNIDADES, ZONAS O GRUPOS DE TRABAJO, EN SU CASO, CON LAS QUE CUENTA:')
            ->setCellValue('B'.$contador1_2++, $solicitud['op_preg1'])
          ->setCellValue('A'.$contador++, '2. ESPECIFIQUE QUE? PRODUCTO(S) QUIERE INCLUIR EN EL CERTIFICADO DEL SÍMBOLO DE PEQUEÑOS PRODUCTORES PARA LOS CUALES EL ORGNISMO DE CERTIFICACIÓN REALIZARÁ LA EVALUACIÓN.')
            ->setCellValue('B'.$contador1_2++, $solicitud['op_preg2'])
          ->setCellValue('A'.$contador++, '3. MENCIONE SI SU ORGANIZACIÓN QUIERE INCLUIR ALGÚN CALIFICATIVO ADICIONAL PARA USO COMPLEMENTARIO CON EL DISEÑO GRÁFICO DEL SÍMBOLO DE PEQUEÑOS PRODUCTORES.')
            ->setCellValue('B'.$contador1_2++, $solicitud['op_preg3'])
          ->setCellValue('A'.$contador++, '4. INDIQUE EL ALCANCE QUE TIENE LA ORGANIZACIÓN DE PEQUEÑOS PRODUCTORES:')
            ->setCellValue('B'.$contador1_2++, $alcance_opp)
          ->setCellValue('A'.$contador++, '5. ESPECIFIQUE SI SUBCONTRATA LOS SERVICIOS DE PLANTAS DE PROCESAMIENTO, EMPRESAS DE COMERCIALIZACIÓN O EMPRESAS QUE REALICEN LA IMPORTACIÓN O EXPORTACIÓN, SI LA RESPUESTA ES AFIRMATIVA, MENCIONE EL NOMBRE Y EL SERVICIO QUE REALIZA:')
            ->setCellValue('B'.$contador1_2++, $solicitud['op_preg5'])
          ->setCellValue('A'.$contador++, '6. SI SUBCONTRATA LOS SERVICIOS DE PLANTAS DE PROCESAMIENTO, EMPRESAS DE COMERCIALIZACIÓN O EMPRESAS QUE REALICEN LA IMPORTACIÓN O EXPORTACIÓN, INDIQUE SI ESTAS EMPRESAS VAN A REALIZAR EL REGISTRO BAJO EL PROGRAMA DEL SPP O SERÁN CONTROLADAS A TRAVE?S DE LA ORGANIZACIÓN DE PEQUEÑOS PRODUCTORES:')
            ->setCellValue('B'.$contador1_2++, $solicitud['op_preg6'])
          ->setCellValue('A'.$contador++, '7. ADICIONAL A SUS OFICINAS CENTRALES, ESPECIFIQUE CUÁNTOS CENTROS DE ACOPIO, ÁREAS DE PROCESAMIENTO U OFICINAS ADICIONALES TIENEN:')
            ->setCellValue('B'.$contador1_2++, $solicitud['op_preg7'])
          ->setCellValue('A'.$contador++, '8. ¿CUENTA CON UN SISTEMA DE CONTROL INTERNO PARA DAR CUMPLIMIENTO A LOS CRITERIOS DE LA NORMA GENERAL DEL SÍMBOLO DE PEQUEÑOS PRODUCTORES?, EN SU CASO, EXPLIQUE:')
            ->setCellValue('B'.$contador1_2++, $solicitud['op_preg8']);
         
         $contador++;


        // TABLA CERTIFICACIONES
            $row_certificaciones = mysql_query("SELECT * FROM certificaciones WHERE idsolicitud_certificacion = $idsolicitud_certificacion", $dspp) or die(mysql_error());
              $contador2 = $contador;
              $contador1_3 = $contador;
              $contador0 = 1;
            while($certificaciones = mysql_fetch_assoc($row_certificaciones)){
              $objPHPExcel->setActiveSheetIndex(0)

              /// TITULOS
              ->setCellValue('A'.$contador2++, 'CERTIFICACIÓN '.$contador0)
                ->setCellValue('B'.$contador1_3++, $certificaciones['certificacion'])
              ->setCellValue('A'.$contador2++, 'CERTIFICADORA '.$contador0)
                ->setCellValue('B'.$contador1_3++, $certificaciones['certificadora'])
              ->setCellValue('A'.$contador2++, 'AÑO INICIAL DE LA CERTIFICACIÓN '.$contador0)
                ->setCellValue('B'.$contador1_3++, $certificaciones['ano_inicial'])
              ->setCellValue('A'.$contador2++, '¿HA SIDO INTERRUMPIDA? '.$contador0)
                ->setCellValue('B'.$contador1_3++, $certificaciones['interrumpida']);

                $contador2++;
                $contador1_3++;
                $contador0++;
            }
            $contador2_1 = $contador2+=2;
            $contador1_4 = $contador2_1;

            $objPHPExcel->setActiveSheetIndex(0)
              

              ->setCellValue('A'.$contador2_1++, 'DE LAS CERTIFICACIONES CON LAS QUE CUENTA, EN SU MÁS RECIENTE EVALUACIÓN INTERNA Y EXTERNA, ¿CUÁNTOS INCUMPLIMIENTOS SE IDENTIFICARON? Y EN SU CASO, ¿ESTÁN RESUELTOS O CUÁL ES SU ESTADO?')
                ->setCellValue('B'.$contador1_4++, $solicitud['op_preg10'])
                //->setCellValue('B'.$contador2_11+=2, $solicitud['op_preg10'])
              
              ->setCellValue('A'.$contador2_1++, '¿TUVO VENTAS SPP DURANTE EL CICLO DE CERTIFICACIÓN ANTERIOR?')
                ->setCellValue('B'.$contador1_4++, $solicitud['op_preg12'])
                //->setCellValue('B'.$contador2_11++, $solicitud['op_preg12'])
              
              ->setCellValue('A'.$contador2_1++, 'SI SU RESPUESTA FUE POSITIVA, FAVOR DE INIDICAR EL RANGO DEL VALOR TOTAL DE SUS VENTAS SPP DEL CICLO ANTERIOR')
                ->setCellValue('B'.$contador1_4++, $solicitud['op_preg13'])
                //->setCellValue('B'.$contador2_11++, $solicitud['op_preg13'])
              
              ->setCellValue('A'.$contador2_1++, 'DEL TOTAL DE SUS VENTAS ¿QUÉ PORCENTAJE DEL PRODUCTO CUENTA CON LA CERTIFICACIÓN DE ORGÁNICO, COMERCIO JUSTO Y/O SÍMBOLO DE PEQUEÑOS PRODUCTORES?')
                ->setCellValue('B'.$contador1_4++, $porcentajeVentas)
                //->setCellValue('B'.$contador2_11++, $porcentajeVentas)
              
              ->setCellValue('A'.$contador2_1++, 'FECHA ESTIMADA PARA COMENZAR A USAR EL SÍMBOLO DE PEQUEÑOS PRODUCTORES:')
                ->setCellValue('B'.$contador1_4++, $solicitud['op_preg14']);
                //->setCellValue('B'.$contador1++, $solicitud['op_preg14']);

        // TABLA PRODUCTOS
              $contador2_1++;
              $contador3 = $contador2_1;
              $contador1_5 = $contador2_1;
              $contador0 = 1;

            $row_productos = mysql_query("SELECT * FROM productos WHERE idsolicitud_certificacion = $idsolicitud_certificacion", $dspp) or die(mysql_error());
            while($productos = mysql_fetch_assoc($row_productos)){
              $objPHPExcel->setActiveSheetIndex(0)

              ->setCellValue('A'.$contador3++, 'PRODUCTO '.$contador0)
                ->setCellValue('B'.$contador1_5++, $productos['producto'])
                //->setCellValue('B'.$contador31++, $productos['producto'])

              ->setCellValue('A'.$contador3++, 'VOLUMEN TOTAL ESTIMADO A COMERCIALIZAR '.$contador0)
                ->setCellValue('B'.$contador1_5++, $productos['volumen'])
                //->setCellValue('B'.$contador31++, $productos['volumen'])

              ->setCellValue('A'.$contador3++, 'PRODUCTO TERMINADO '.$contador0)
                ->setCellValue('B'.$contador1_5++, $productos['terminado'])
                //->setCellValue('B'.$contador31++, $productos['terminado'])

              ->setCellValue('A'.$contador3++, 'MATERIA PRIMA '.$contador0)
                ->setCellValue('B'.$contador1_5++, $productos['materia'])
                //->setCellValue('B'.$contador31++, $productos['materia'])

              ->setCellValue('A'.$contador3++, 'PAÍS(ES) DE DESTINO '.$contador0)
                ->setCellValue('B'.$contador1_5++, $productos['destino'])
                //->setCellValue('B'.$contador31++, $productos['destino'])

              ->setCellValue('A'.$contador3++, 'MARCA PROPIA '.$contador0)
                ->setCellValue('B'.$contador1_5++, $productos['marca_propia'])
                //->setCellValue('B'.$contador31++, $productos['marca_propia'])

              ->setCellValue('A'.$contador3++, 'MARCA DE UN CLIENTE '.$contador0)
                ->setCellValue('B'.$contador1_5++, $productos['marca_cliente'])
                //->setCellValue('B'.$contador31++, $productos['marca_cliente'])

              ->setCellValue('A'.$contador3++, 'SIN CLIENTE AUN '.$contador0)
                ->setCellValue('B'.$contador1_5++, $productos['sin_cliente']);
                //->setCellValue('B'.$contador1++, $productos['sin_cliente']);
                $contador3++;
                $contador1_5++;
                $contador0++;
            }

    // NOMBRE DE LA PESTAÑA(hoja)
    $objPHPExcel->getActiveSheet(0)->setTitle('SOLICITUD');


    /// AJUSTAR EL TEXTO DE LAS COLUMNAS
    $objPHPExcel->getActiveSheet(0)->getStyle('A1:B1'.$objPHPExcel->getActiveSheet(0)->getHighestRow())
      ->getAlignment()->setWrapText(true);
    //$objPHPExcel->getActiveSheet()->getStyle('A3:K3')->applyFromArray($estiloTituloColumnas);   
    /// APLICAR TAMAÑO PREDEFINIDO A LAS COLUMNAS
    for($i = 'A'; $i <= 'B'; $i++){
      $objPHPExcel->setActiveSheetIndex(0)      
        ->getColumnDimension($i)->setWidth(60);
    }

    /// APLICAR FORMATO DE COLOR Y TIPO DE TEXTO A LAS CELDAS
    $objPHPExcel->getActiveSheet(0)->getStyle('A1:B1')->applyFromArray($estiloEncabezado);
    $objPHPExcel->getActiveSheet(0)->getStyle('A2:A200')->applyFromArray($estiloTituloColumnas);
    $objPHPExcel->getActiveSheet(0)->getStyle('B2:B200')->applyFromArray($estiloPreguntas);



    // Se activa la hoja para que sea la que se muestre cuando el archivo se abre
    $objPHPExcel->setActiveSheetIndex(0);
    // Inmovilizar paneles 
    //$objPHPExcel->getActiveSheet(0)->freezePane('A4');
    //$objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(0,4);
    $objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(0,2);

    // Se manda el archivo al navegador web, con el nombre que se indica (Excel2007)
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="SOLICITUD_DE_CERTIFICACIÓN.xls"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    exit;
  


 ?>