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

    $segundaHoja = $objPHPExcel->createSheet(); 
    $tercerHoja = $objPHPExcel->createSheet(); 
    $cuartaHoja = $objPHPExcel->createSheet(); 
    $quintaHoja = $objPHPExcel->createSheet(); 

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
          //// TITULOS
          ->setCellValue('A3','FECHA DE ELABORACIÓN')
          ->setCellValue('A4', 'TIPO DE SOLICITUD')
          ->setCellValue('A5', 'CODIGO DE IDENTIFICACIÓN SPP')
          ->setCellValue('A6', 'TIPO DE PROCEDIMIENTO DE CERTIFICACIÓN')
          ->setCellValue('A7', 'NOMBRE COMPLETO DE LA ORGANIZACIÓN')
          ->setCellValue('A8', 'DIRECCIÓN COMPLETA DE SUS OFICINAS')
          ->setCellValue('A9', 'PAÍS')
          ->setCellValue('A10', 'CORREO ELECTRONICO')
          ->setCellValue('A11', 'TELEFONO DE LA ORGANIZACIÓN')
          ->setCellValue('A12', 'SITIO WEB')
          ->setCellValue('A13', 'DIRECCIÓN FISCAL')
          ->setCellValue('A14', 'RFC')
          ->setCellValue('A15', 'RUC')
          //// INFORMACIÓN
          ->setCellValue('B3',  date('Y-m-d', $solicitud['fecha_registro']))
          ->setCellValue('B4', $solicitud['tipo_solicitud'])
          ->setCellValue('B5', $solicitud['spp_opp'])
          ->setCellValue('B6', $solicitud['tipo_procedimiento'])

          // DATOS GENERALES
          ->setCellValue('B7', $solicitud['nombre_opp'])
          ->setCellValue('B8', $solicitud['direccion_oficina'])
          ->setCellValue('B9', $solicitud['pais'])
          ->setCellValue('B10', $solicitud['email_opp'])
          ->setCellValue('B11', $solicitud['telefono_opp'])
          ->setCellValue('B12', $solicitud['sitio_web'])
          ->setCellValue('B13', $solicitud['direccion_fiscal'])
          ->setCellValue('B14', $solicitud['rfc'])
          ->setCellValue('B15', $solicitud['ruc']);

    // NOMBRE DE LA PESTAÑA(hoja)
    $objPHPExcel->getActiveSheet(0)->setTitle('DATOS GENERALES');


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
    $objPHPExcel->getActiveSheet(0)->getStyle('A1:A100')->applyFromArray($estiloTituloColumnas);
    $objPHPExcel->getActiveSheet(0)->getStyle('B1:B100')->applyFromArray($estiloPreguntas);


    ///// HOJA PERSONAS DE CONTACTO ////
    $objPHPExcel->setActiveSheetIndex(1)
          //// TITULOS
          ->setCellValue('A3', 'NOMBRE')
          ->setCellValue('A4', 'CARGO')
          ->setCellValue('A5', 'CORREO ELECTRONICO')
          ->setCellValue('A6', 'TELEFONO')
            //datos
            ->setCellValue('B3', $solicitud['contacto1_nombre']) 
            ->setCellValue('B4', $solicitud['contacto1_cargo'])
            ->setCellValue('B5', $solicitud['contacto1_email'])
            ->setCellValue('B6', $solicitud['contacto1_telefono'])
          /// TITULOS
          ->setCellValue('A3', 'NOMBRE')
          ->setCellValue('A4', 'CARGO')
          ->setCellValue('A5', 'CORREO ELECTRONICO')
          ->setCellValue('A6', 'TELEFONO')
            /// datos
            ->setCellValue('B7', $solicitud['contacto2_nombre'])
            ->setCellValue('B8', $solicitud['contacto2_cargo'])
            ->setCellValue('B9', $solicitud['contacto2_email'])
            ->setCellValue('B10', $solicitud['contacto2_telefono'])
          /// TITULOS
          ->setCellValue('A3', 'NOMBRE')
          ->setCellValue('A4', 'CARGO')
          ->setCellValue('A5', 'CORREO ELECTRONICO')
          ->setCellValue('A6', 'TELEFONO')
            // datos
            ->setCellValue('B11', $solicitud['adm1_nombre'])
            ->setCellValue('B12', $solicitud['adm1_email'])
            ->setCellValue('B13', 'ADMINISTRADOR')
            ->setCellValue('B14', $solicitud['adm1_telefono'])
          /// contactos
          ->setCellValue('A3', 'NOMBRE')
          ->setCellValue('A4', 'CARGO')
          ->setCellValue('A5', 'CORREO ELECTRONICO')
          ->setCellValue('A6', 'TELEFONO')
            // datos
            ->setCellValue('B15', $solicitud['adm2_nombre'])
            ->setCellValue('B16', $solicitud['adm2_email'])
            ->setCellValue('B17', 'ADMINISTRADOR')
            ->setCellValue('B18', $solicitud['adm2_telefono']);

    // NOMBRE DE LA PESTAÑA(hoja)
    $objPHPExcel->getActiveSheet(1)->setTitle('PERSONAS DE CONTACTO');

    /// AJUSTAR EL TEXTO DE LAS COLUMNAS
    $objPHPExcel->getActiveSheet(1)->getStyle('A1:B1'.$objPHPExcel->getActiveSheet(1)->getHighestRow())
      ->getAlignment()->setWrapText(true);
    //$objPHPExcel->getActiveSheet()->getStyle('A3:K3')->applyFromArray($estiloTituloColumnas);   
    /// APLICAR TAMAÑO PREDEFINIDO A LAS COLUMNAS
    for($i = 'A'; $i <= 'B'; $i++){
      $objPHPExcel->setActiveSheetIndex(1)      
        ->getColumnDimension($i)->setWidth(60);
    }

    /// APLICAR FORMATO DE COLOR Y TIPO DE TEXTO A LAS CELDAS
    $objPHPExcel->getActiveSheet(1)->getStyle('A1:A100')->applyFromArray($estiloTituloColumnas);
    $objPHPExcel->getActiveSheet(1)->getStyle('B1:B100')->applyFromArray($estiloPreguntas);


    ///// HOJA DATOS DE OPERACIÓN ////
    $objPHPExcel->setActiveSheetIndex(2)
          //// TITULOS
          ->setCellValue('A3', 'NÚMERO DE SOCIOS PRODUCTORES')
          ->setCellValue('A4', 'NÚMERO DE SOCIOS PRODUCTORES DEL (DE LOS) PRODUCTO(S) A INCLUIR EN LA CERTIFICACIÓN ')
          ->setCellValue('A5', 'VOLUMEN(ES) DE PRODUCCIÓN TOTAL POR PRODUCTO (UNIDAD DE MEDIDA) ')
          ->setCellValue('A6', 'TAMAÑO MÁXIMO DE LA UNIDAD DE PRODUCCIÓN POR PRODUCTOR DEL (DE LOS) PRODUCTO(S) A INCLUIR EN LA CERTIFICACIÓN:')
          ->setCellValue('A7', '1.- EXPLIQUE SI SE TRATA DE UNA ORGANIZACIÓN DE PEQUEÑOS PRODUCTORES DE 1ER, 2DO, 3ER O 4TO GRADO, ASÍ COMO EL NÚMERO DE OPP DE 3ER, 2DO O 1ER GRADO, Y EL NÚMERO DE COMUNIDADES, ZONAS O GRUPOS DE TRABAJO, EN SU CASO, CON LAS QUE CUENTA:')
          ->setCellValue('A8', '2. ESPECIFIQUE QUE? PRODUCTO(S) QUIERE INCLUIR EN EL CERTIFICADO DEL SÍMBOLO DE PEQUEÑOS PRODUCTORES PARA LOS CUALES EL ORGNISMO DE CERTIFICACIÓN REALIZARÁ LA EVALUACIÓN.')
          ->setCellValue('A9', '3. MENCIONE SI SU ORGANIZACIÓN QUIERE INCLUIR ALGÚN CALIFICATIVO ADICIONAL PARA USO COMPLEMENTARIO CON EL DISEÑO GRÁFICO DEL SÍMBOLO DE PEQUEÑOS PRODUCTORES.')
          ->setCellValue('A10', '4. INDIQUE EL ALCANCE QUE TIENE LA ORGANIZACIÓN DE PEQUEÑOS PRODUCTORES:')
          ->setCellValue('A11', '5. ESPECIFIQUE SI SUBCONTRATA LOS SERVICIOS DE PLANTAS DE PROCESAMIENTO, EMPRESAS DE COMERCIALIZACIÓN O EMPRESAS QUE REALICEN LA IMPORTACIÓN O EXPORTACIÓN, SI LA RESPUESTA ES AFIRMATIVA, MENCIONE EL NOMBRE Y EL SERVICIO QUE REALIZA:')
          ->setCellValue('A12', '6. SI SUBCONTRATA LOS SERVICIOS DE PLANTAS DE PROCESAMIENTO, EMPRESAS DE COMERCIALIZACIÓN O EMPRESAS QUE REALICEN LA IMPORTACIÓN O EXPORTACIÓN, INDIQUE SI ESTAS EMPRESAS VAN A REALIZAR EL REGISTRO BAJO EL PROGRAMA DEL SPP O SERÁN CONTROLADAS A TRAVE?S DE LA ORGANIZACIÓN DE PEQUEÑOS PRODUCTORES:')
          ->setCellValue('A13', '7. ADICIONAL A SUS OFICINAS CENTRALES, ESPECIFIQUE CUÁNTOS CENTROS DE ACOPIO, ÁREAS DE PROCESAMIENTO U OFICINAS ADICIONALES TIENEN:')
          ->setCellValue('A14', '8. ¿CUENTA CON UN SISTEMA DE CONTROL INTERNO PARA DAR CUMPLIMIENTO A LOS CRITERIOS DE LA NORMA GENERAL DEL SÍMBOLO DE PEQUEÑOS PRODUCTORES?, EN SU CASO, EXPLIQUE:')

            // datos
            ->setCellValue('B3', $solicitud['resp1'])
            ->setCellValue('B4', $solicitud['resp2'])
            ->setCellValue('B5', $solicitud['resp3'])
            ->setCellValue('B6', $solicitud['resp4'])
            ->setCellValue('B7', $solicitud['op_preg1'])
            ->setCellValue('B8', $solicitud['op_preg2'])
            ->setCellValue('B9', $solicitud['op_preg3'])
            ->setCellValue('B10', $alcance_opp)
            ->setCellValue('B11', $solicitud['op_preg5'])
            ->setCellValue('B12', $solicitud['op_preg6'])
            ->setCellValue('B13', $solicitud['op_preg7'])
            ->setCellValue('B14', $solicitud['op_preg8']);

    // NOMBRE DE LA PESTAÑA(hoja)
    $objPHPExcel->getActiveSheet(2)->setTitle('DATOS DE OPERACIÓN');

    /// AJUSTAR EL TEXTO DE LAS COLUMNAS
    $objPHPExcel->getActiveSheet(2)->getStyle('A1:B1'.$objPHPExcel->getActiveSheet(2)->getHighestRow())
      ->getAlignment()->setWrapText(true);
    //$objPHPExcel->getActiveSheet()->getStyle('A3:K3')->applyFromArray($estiloTituloColumnas);   
    /// APLICAR TAMAÑO PREDEFINIDO A LAS COLUMNAS
    for($i = 'A'; $i <= 'B'; $i++){
      $objPHPExcel->setActiveSheetIndex(2)      
        ->getColumnDimension($i)->setWidth(60);
    }

    /// APLICAR FORMATO DE COLOR Y TIPO DE TEXTO A LAS CELDAS
    $objPHPExcel->getActiveSheet(2)->getStyle('A1:A100')->applyFromArray($estiloTituloColumnas);
    $objPHPExcel->getActiveSheet(2)->getStyle('B1:B100')->applyFromArray($estiloPreguntas);


    $query_porcentajeVentas = "SELECT * FROM porcentaje_productoVentas WHERE idsolicitud_certificacion = $solicitud[idsolicitud_certificacion]";
    $row_porcentajeVentas = mysql_query($query_porcentajeVentas, $dspp) or die(mysql_error());

    $porcentajes = mysql_fetch_assoc($row_porcentajeVentas);

    if(!empty($porcentajes['organico'])){
      $porcentajeVentas .= 'ORGANICO: '.$porcentajes['organico'].'%, ';
    }else if(!empty($porcentajes['comercio_justo'])){
      $porcentajeVentas .= 'COMERCIO JUSTO: '.$porcentajes['comercio_justo'].'%, ';
    }else if(!empty($porcentajes['spp'])){
      $porcentajeVentas .= 'SPP: '.$porcentajes['spp'].'%, ';
    }else if(!empty($porcentajes['sin_certificado'])){
      $porcentajeVentas .= 'OTRO: '.$porcentajes['sin_certificado'].'%, ';
    }



    ///// HOJA CERTIFICACIONES ////
    $objPHPExcel->setActiveSheetIndex(3);

        // TABLA CERTIFICACIONES
              $contador = 3;
              $contador1 = 3;
              $contador2 = 4;
              $contador3 = 5;
              $contador4 = 6;
            while($certificaciones = mysql_fetch_assoc($row_certificaciones)){
              $objPHPExcel->setActiveSheetIndex(3)

              /// TITULOS
              ->setCellValue('A'.$contador++, 'CERTIFICACIÓN')
              ->setCellValue('B'.$contador1++, $certificaciones['certificacion'])
              ->setCellValue('A'.$contador++, 'CERTIFICADORA')
              ->setCellValue('B'.$contador1++, $certificaciones['certificadora'])
                /// datos
                ->setCellValue('A'.$contador++, 'AÑO INICIAL DE LA CERTIFICACIÓN')
                ->setCellValue('B'.$contador1++, $certificaciones['ano_inicial'])
                ->setCellValue('A'.$contador++, '¿HA SIDO INTERRUMPIDA?')
                ->setCellValue('B'.$contador1++, $certificaciones['interrumpida']);

                $contador++;
                $contador1++;
            }
            $objPHPExcel->setActiveSheetIndex(3)

              ->setCellValue('A'.$contador+=2, 'DE LAS CERTIFICACIONES CON LAS QUE CUENTA, EN SU MÁS RECIENTE EVALUACIÓN INTERNA Y EXTERNA, ¿CUÁNTOS INCUMPLIMIENTOS SE IDENTIFICARON? Y EN SU CASO, ¿ESTÁN RESUELTOS O CUÁL ES SU ESTADO?')
                ->setCellValue('B'.$contador1+=2, $solicitud['op_preg10'])
              
              ->setCellValue('A'.$contador++, '¿TUVO VENTAS SPP DURANTE EL CICLO DE CERTIFICACIÓN ANTERIOR?')
                ->setCellValue('B'.$contador1++, $solicitud['op_preg12'])
              
              ->setCellValue('A'.$contador++, 'SI SU RESPUESTA FUE POSITIVA, FAVOR DE INIDICAR EL RANGO DEL VALOR TOTAL DE SUS VENTAS SPP DEL CICLO ANTERIOR')
                ->setCellValue('B'.$contador1++, $solicitud['op_preg13'])
              
              ->setCellValue('A'.$contador++, 'DEL TOTAL DE SUS VENTAS ¿QUÉ PORCENTAJE DEL PRODUCTO CUENTA CON LA CERTIFICACIÓN DE ORGÁNICO, COMERCIO JUSTO Y/O SÍMBOLO DE PEQUEÑOS PRODUCTORES?')
                ->setCellValue('B'.$contador1++, $porcentajeVentas)
              
              ->setCellValue('A'.$contador++, 'FECHA ESTIMADA PARA COMENZAR A USAR EL SÍMBOLO DE PEQUEÑOS PRODUCTORES:')
                ->setCellValue('B'.$contador1++, $solicitud['op_preg14']);
                
    // NOMBRE DE LA PESTAÑA(hoja)
    $objPHPExcel->getActiveSheet(3)->setTitle('CERTIFICACIONES');

    /// AJUSTAR EL TEXTO DE LAS COLUMNAS
    $objPHPExcel->getActiveSheet(3)->getStyle('A1:B1'.$objPHPExcel->getActiveSheet(3)->getHighestRow())
      ->getAlignment()->setWrapText(true);
    //$objPHPExcel->getActiveSheet()->getStyle('A3:K3')->applyFromArray($estiloTituloColumnas);   
    /// APLICAR TAMAÑO PREDEFINIDO A LAS COLUMNAS
    for($i = 'A'; $i <= 'B'; $i++){
      $objPHPExcel->setActiveSheetIndex(3)      
        ->getColumnDimension($i)->setWidth(60);
    }
    /// APLICAR FORMATO DE COLOR Y TIPO DE TEXTO A LAS CELDAS
    $objPHPExcel->getActiveSheet(3)->getStyle('A1:A100')->applyFromArray($estiloTituloColumnas);
    $objPHPExcel->getActiveSheet(3)->getStyle('B1:B100')->applyFromArray($estiloPreguntas);


    ///// HOJA PRODUCTOS ////
    $objPHPExcel->setActiveSheetIndex(4);

        // TABLA PRODUCTOS
              $contador = 3;
              $contador1 = 3;

            while($productos = mysql_fetch_assoc($row_productos)){
              $objPHPExcel->setActiveSheetIndex(4)

              ->setCellValue('A'.$contador++, 'PRODUCTO')
                ->setCellValue('B'.$contador1++, $productos['producto'])

              ->setCellValue('A'.$contador++, 'VOLUMEN TOTAL ESTIMADO A COMERCIALIZAR')
                ->setCellValue('B'.$contador1++, $productos['volumen'])

              ->setCellValue('A'.$contador++, 'PRODUCTO TERMINADO')
                ->setCellValue('B'.$contador1++, $productos['terminado'])

              ->setCellValue('A'.$contador++, 'MATERIA PRIMA')
                ->setCellValue('B'.$contador1++, $productos['materia'])

              ->setCellValue('A'.$contador++, 'PAÍS(ES) DE DESTINO')
                ->setCellValue('B'.$contador1++, $productos['destino'])

              ->setCellValue('A'.$contador++, 'MARCA PROPIA')
                ->setCellValue('B'.$contador1++, $productos['marca_propia'])

              ->setCellValue('A'.$contador++, 'MARCA DE UN CLIENTE')
                ->setCellValue('B'.$contador1++, $productos['marca_cliente'])

              ->setCellValue('A'.$contador++, 'SIN CLIENTE AUN')
                ->setCellValue('B'.$contador1++, $productos['sin_cliente']);
              
              $contador++;
              $contador1++;
                
            }
            
    // NOMBRE DE LA PESTAÑA(hoja)
    $objPHPExcel->getActiveSheet(4)->setTitle('PRODUCTOS');
    /// AJUSTAR EL TEXTO DE LAS COLUMNAS
    $objPHPExcel->getActiveSheet(4)->getStyle('A1:B1'.$objPHPExcel->getActiveSheet(4)->getHighestRow())
      ->getAlignment()->setWrapText(true);
    //$objPHPExcel->getActiveSheet()->getStyle('A3:K3')->applyFromArray($estiloTituloColumnas);   
    /// APLICAR TAMAÑO PREDEFINIDO A LAS COLUMNAS
    for($i = 'A'; $i <= 'B'; $i++){
      $objPHPExcel->setActiveSheetIndex(4)      
        ->getColumnDimension($i)->setWidth(60);
    }

    /// APLICAR FORMATO DE COLOR Y TIPO DE TEXTO A LAS CELDAS
    $objPHPExcel->getActiveSheet(4)->getStyle('A1:A100')->applyFromArray($estiloTituloColumnas);
    $objPHPExcel->getActiveSheet(4)->getStyle('B1:B100')->applyFromArray($estiloPreguntas);


    // Se activa la hoja para que sea la que se muestre cuando el archivo se abre
    $objPHPExcel->setActiveSheetIndex(0);
    // Inmovilizar paneles 
    //$objPHPExcel->getActiveSheet(0)->freezePane('A4');
    //$objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(0,4);

    // Se manda el archivo al navegador web, con el nombre que se indica (Excel2007)
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="SOLICITUD_DE_CERTIFICACIÓN.xls"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    exit;
  


 ?>