<?php 
  require_once('../../Connections/dspp.php');
  require_once('../../mpdf/mpdf.php');
  /** Se agrega la libreria PHPExcel */
  require_once '../../PHPExcel/PHPExcel.php';
  mysql_select_db($database_dspp, $dspp);
  function mayuscula($variable) {
    $variable = strtr(strtoupper($variable),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
    return $variable;
  }



    $idsolicitud_registro = $_POST['idsolicitud_registro'];

    $query = "SELECT solicitud_registro.*, oc.idoc, oc.nombre AS 'nombre_oc', empresa.spp AS 'spp_empresa', empresa.nombre AS 'nombre_empresa', empresa.direccion_oficina, empresa.pais, empresa.email AS 'email_empresa', empresa.sitio_web, empresa.telefono AS 'telefono_empresa', empresa.rfc, empresa.ciudad AS 'ciudad_empresa', porcentaje_productoVentas.organico, porcentaje_productoVentas.comercio_justo, porcentaje_productoVentas.spp, porcentaje_productoVentas.sin_certificado FROM solicitud_registro INNER JOIN oc ON solicitud_registro.idoc = oc.idoc LEFT JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa LEFT JOIN porcentaje_productoVentas ON solicitud_registro.idsolicitud_registro = porcentaje_productoVentas.idsolicitud_registro WHERE solicitud_registro.idsolicitud_registro = $idsolicitud_registro";

    $row_solicitud = mysql_query($query,$dspp) or die(mysql_error()); 

    $solicitud = mysql_fetch_assoc($row_solicitud);

  $query_certificaciones = "SELECT * FROM certificaciones WHERE idsolicitud_registro = '$solicitud[idsolicitud_registro]'";
    $row_certificaciones = mysql_query($query_certificaciones, $dspp) or die(mysql_error());

    $total_certificaciones = mysql_num_rows($row_certificaciones);

    $query_productos = "SELECT * FROM productos WHERE idsolicitud_registro = '$solicitud[idsolicitud_registro]'";
    $row_productos = mysql_query($query_productos, $dspp) or die(mysql_error());


    $nomCertificaciones = 29;
    $subPreguntas = ($nomCertificaciones + $total_certificaciones) + 1;
      $subRespuestas = $subPreguntas + 1;

    $encabezadoProductos = $subRespuestas + 2;
      $subEncabezados = $encabezadoProductos + 1;
        $nomProductos = $subEncabezados + 1;

    // Se crea el objeto PHPExcel
    $objPHPExcel = new PHPExcel();

    // Se asignan las propiedades del libro
    $objPHPExcel->getProperties()->setCreator("spp global") //Autor
               ->setLastModifiedBy("spp global") //Ultimo usuario que lo modificó
               ->setTitle("Demande d'inscription")
               ->setSubject("Demande d'inscription")
               ->setDescription("Demande d'inscription")
               ->setKeywords("Demande d'inscription")
               ->setCategory("Demande d'inscription");

    $tituloReporte = "Demande d'inscription";

    
    $objPHPExcel->setActiveSheetIndex(0)
                ->mergeCells('A1:H1')
                //preguntas
                  ->mergeCells('A3:B3')
                  ->mergeCells('C3:D3')
                  ->mergeCells('E3:F3')
                  ->mergeCells('G3:H3')
                    //respuestas
                    ->mergeCells('A4:B4')
                    ->mergeCells('C4:D4')
                    ->mergeCells('E4:F4')
                    ->mergeCells('G4:H4')
                // DATOS GENERALES
                ->mergeCells('A6:H6')
                  ->mergeCells('A8:B8')
                // PERSONAS DE CONTACTO
                ->mergeCells('A11:H11')
                  // titulos
                  ->mergeCells('A13:B13')
                  ->mergeCells('C13:D13')
                  ->mergeCells('E13:F13')
                  ->mergeCells('G13:H13')

                  ->mergeCells('A14:B14')
                  ->mergeCells('C14:D14')
                  ->mergeCells('E14:F14')
                  ->mergeCells('G14:H14')

                  ->mergeCells('A15:B15')
                  ->mergeCells('C15:D15')
                  ->mergeCells('E15:F15')
                  ->mergeCells('G15:H15')

                  ->mergeCells('A16:B16')
                  ->mergeCells('C16:D16')
                  ->mergeCells('E16:F16')
                  ->mergeCells('G16:H16')

                  ->mergeCells('A17:B17')
                  ->mergeCells('C17:D17')
                  ->mergeCells('E17:F17')
                  ->mergeCells('G17:H17')
                // DATOS DE OPERACIÓN
                ->mergeCells('A19:H19')
                  ->mergeCells('A21:B21')
                  ->mergeCells('C21:D21')
                  ->mergeCells('E21:F21')
                  ->mergeCells('G21:H21')

                  ->mergeCells('A22:B22')
                  ->mergeCells('C22:D22')
                  ->mergeCells('E22:F22')
                  ->mergeCells('G22:H22')
                /// TABLA DE CERTIFICACIONES
                ->mergeCells('A28:H28')
                  ->mergeCells('A29:B29')
                  ->mergeCells('C29:D29')
                  ->mergeCells('E29:F29')
                  ->mergeCells('G29:H29')
                /// ENCABEZADO TABLA PRODUCTOS
                ->mergeCells('A'.$encabezadoProductos.':H'.$encabezadoProductos);
            
    // Se agregan los titulos del reporte
    $objPHPExcel->setActiveSheetIndex(0)
          ->setCellValue('A1',$tituloReporte)
          ->setCellValue('A3','FECHA DE ELABORACIÓN')
          ->setCellValue('C3', 'TIPO DE SOLICITUD')
          ->setCellValue('E3', 'CODIGO DE IDENTIFICACIÓN SPP')
          ->setCellValue('G3', 'TIPO DE PROCEDIMIENTO DE CERTIFICACIÓN')
          ->setCellValue('A6', 'DATOS GENERALES')
          ->setCellValue('A8', 'NOMBRE COMPLETO DE LA EMPRESA')
          ->setCellValue('C8', 'DIRECCIÓN COMPLETA DE SUS OFICINAS')
          ->setCellValue('D8', 'PAÍS')
          ->setCellValue('E8', 'CORREO ELECTRONICO')
          ->setCellValue('F8', 'TELEFONO DE LA ORGANIZACIÓN')
          ->setCellValue('G8', 'SITIO WEB')
          ->setCellValue('H8', 'DATOS FISCALES')
          ->setCellValue('A11', 'PERSONAS DE CONTACTO')
          ->setCellValue('A13', 'NOMBRE')
          ->setCellValue('C13', 'CARGO')
          ->setCellValue('E13', 'CORREO ELECTRONICO')
          ->setCellValue('G13', 'TELEFONO(S)')
          ->setCellValue('A19', 'DATOS DE OPERACIÓN')

          ->setCellValue('A24', '1. ¿CUÁLES SON LAS ORGANIZACIONES DE PEQUEÑOS PRODUCTORES A LAS QUE LES COMPRA O PRETENDE
COMPRAR BAJO EL ESQUEMA DEL SÍMBOLO DE PEQUEÑOS PRODUCTORES?')

          ->setCellValue('B24', '2. ¿QUIÉN O QUIÉNES SON LOS PROPIETARIOS DE LA EMPRESA?')

          ->setCellValue('C24', '3. ESPECIFIQUE QUÉ PRODUCTO(S) QUIERE INCLUIR EN EL CERTIFICADO DEL SÍMBOLO DE PEQUEÑOS
PRODUCTORES PARA LOS CUALES EL ORGNISMO DE CERTIFICACIÓN REALIZARÁ LA EVALUACIÓN.')

          ->setCellValue('D24', '4. SI SU EMPRESA ES UN COMPRADOR FINAL, MENCIONE SI QUIEREN INCLUIR ALGÚN CALIFICATIVO ADICIONAL PARA
USO COMPLEMENTARIO CON EL DISEÑO GRÁFICO DEL SÍMBOLO DE PEQUEÑOS PRODUCTORES')

          ->setCellValue('E24', '5. INDIQUE EL ALCANCE QUE TIENE LA EMPRESA')

          ->setCellValue('F24', '6. EXPLIQUE SI SUBCONTRATA LOS SERVICIOS DE PLANTAS DE PROCESAMIENTO, EMPRESAS DE
COMERCIALIZACIÓN O EMPRESAS QUE REALICEN LA IMPORTACIÓN O EXPORTACIÓN, SI LA RESPUESTA ES
AFIRMATIVA, MENCIONE EL NOMBRE Y EL SERVICIO QUE REALIZA')

          ->setCellValue('G24', '7. SI SUBCONTRATA LOS SERVICIOS DE PLANTAS DE PROCESAMIENTO, EMPRESAS DE COMERCIALIZACIÓN O
EMPRESAS QUE REALICEN LA IMPORTACIÓN O EXPORTACIÓN, INDIQUE SI ESTAS ESTAN REGISTRADAS O VAN A
REALIZAR EL REGISTRO BAJO EL PROGRAMA DEL SPP O SERÁN CONTROLADAS A TRAVÉS DE SU EMPRESA.')

          ->setCellValue('H24', '8. ADICIONAL A SUS OFICINAS CENTRALES, ESPECIFIQUE CUÁNTOS CENTROS DE ACOPIO, ÁREAS DE PROCESAMIENTO U OFICINAS ADICIONALES TIENE.')

          ->setCellValue('A27', '9. CUENTA CON UN SISTEMA DE CONTROL INTERNO PARA DAR CUMPLIMIENTO A LOS CRITERIOS DE LA NORMA
GENERAL DEL SÍMBOLO DE PEQUEÑOS PRODUCTORES, EN SU CASO EXPLIQUE.')

          ->setCellValue('A28', '10. LLENAR LA TABLA DE ACUERDO A LAS CERTIFICACIONES QUE TIENE, (EJEMPLO: EU, NOP, JASS, FLO, etc)')
          ->setCellValue('A29', 'CERTIFICACIÓN')
          ->setCellValue('C29', 'CERTIFICADORA')
          ->setCellValue('E29', 'AÑO INICIAL DE CERTIFICACIÓN')
          ->setCellValue('G29', '¿HA SIDO INTERRUMPIDA?')
          /// SUB PREGUNTAS
            ->setCellValue('A'.$subPreguntas, '12. DE LAS CERTIFICACIONES CON LAS QUE CUENTA, EN SU MÁS RECIENTE EVALUACIÓN INTERNA Y EXTERNA,
¿CUÁNTOS INCUMPLIMIENTOS SE IDENTIFICARON? Y EN SU CASO, ¿ESTÁN RESUELTOS O CUÁL ES SU ESTADO?')
            ->setCellValue('B'.$subPreguntas, '13. DEL TOTAL DE SU COMERCIALIZACIÓN EL CICLO PASADO, ¿QUÉ PORCENTAJE FUERON REALIZADAS BAJO LOS
ESQUEMAS CERTIFICADOS DE ORGÁNICO, COMERCIO JUSTO Y/O SÍMBOLO DE PEQUEÑOS PRODUCTORES?')
            ->setCellValue('C'.$subPreguntas, '14. TUVO COMPRAS SPP DURANTE EL CICLO DE REGISTRO ANTERIOR?')
            ->setCellValue('D'.$subPreguntas, '15. SI SU RESPUESTA FUE POSITIVA, FAVOR DE INIDICAR EL RANGO DEL VALOR TOTAL DE SUS COMPRAS SPP DEL
CICLO ANTERIOR')
            ->setCellValue('E'.$subPreguntas, '16. FECHA ESTIMADA PARA COMENZAR A USAR EL SÍMBOLO DE PEQUEÑOS PRODUCTORES:')

          /// ENCABEZADO PRODUCTOS
          ->setCellValue('A'.$encabezadoProductos, 'PRODUCTOS DE LA ORGANIZACIÓN')
            //// sub-encabezados
            ->setCellValue('A'.$subEncabezados, 'PRODUCTO')
            ->setCellValue('B'.$subEncabezados, 'VOLUMEN TOTAL ESTIMADO A COMERCIALIZAR')
            ->setCellValue('C'.$subEncabezados, 'PRODUCTO TERMINADO')
            ->setCellValue('D'.$subEncabezados, 'MATERIA PRIMA')
            ->setCellValue('E'.$subEncabezados, 'PAÍS(ES) DE DESTINO')
            ->setCellValue('F'.$subEncabezados, 'MARCA PROPIA')
            ->setCellValue('G'.$subEncabezados, 'MARCA DE UN CLIENTE')
            ->setCellValue('H'.$subEncabezados, 'SIN CLIENTE AUN');

  
     //// VACIAR INFORMACIÓN
    $datos_fiscales = 'DIRECCIÓN FISCAL'.$solicitud['direccion_fiscal'].', RFC: '.$solicitud['rfc'].', RUC'.$solicitud['ruc'];
    $alcance_empresa = '';
    $porcentajeVentas = '';

    if($solicitud['produccion']){
      $alcance_empresa .= 'PRODUCCIÓN - ';
    }else if($solicitud['procesamiento']){
    $alcance_empresa .= 'PROCESAMIENTO - ';
    }else if($solicitud['importacion']){
    $alcance_empresa .= 'IMPORTACIÓN - ';
    }

    $query_porcentajeVentas = "SELECT * FROM porcentaje_productoVentas WHERE idsolicitud_registro = $solicitud[idsolicitud_registro]";
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


    $objPHPExcel->setActiveSheetIndex(0)
          // INFORMACIÓN SOLICITUD
                ->setCellValue('A4',  date('Y-m-d', $solicitud['fecha_registro']))
                ->setCellValue('C4', $solicitud['tipo_solicitud'])
                ->setCellValue('E4', $solicitud['spp_empresa'])
                ->setCellValue('G4', $solicitud['tipo_procedimiento'])

                // DATOS GENERALES
                ->setCellValue('A9', $solicitud['nombre_empresa'])
                ->setCellValue('C9', $solicitud['direccion_oficina'])
                ->setCellValue('D9', $solicitud['pais'])
                ->setCellValue('E9', $solicitud['email_empresa'])
                ->setCellValue('F9', $solicitud['telefono_empresa'])
                ->setCellValue('G9', $solicitud['sitio_web'])
                ->setCellValue('H9', $datos_fiscales)

                /// contactos
                ->setCellValue('A14', $solicitud['contacto1_nombre'])
                ->setCellValue('C14', $solicitud['contacto1_cargo'])
                ->setCellValue('E14', $ssololicitud['contacto1_email'])
                ->setCellValue('G14', $solicitud['contacto1_telefono'])
                /// contactos
                ->setCellValue('A15', $solicitud['contacto2_nombre'])
                ->setCellValue('C15', $solicitud['contacto2_cargo'])
                ->setCellValue('E15', $solicitud['contacto2_email'])
                ->setCellValue('G15', $solicitud['contacto2_telefono'])
                /// contactos
                ->setCellValue('A16', $solicitud['adm1_nombre'])
                ->setCellValue('C16', $solicitud['adm1_email'])
                ->setCellValue('E16', 'ADMINISTRADOR')
                ->setCellValue('G16', $solicitud['adm1_telefono'])
                /// contactos
                ->setCellValue('A17', $solicitud['adm2_nombre'])
                ->setCellValue('C17', $solicitud['adm2_email'])
                ->setCellValue('E17', 'ADMINISTRADOR')
                ->setCellValue('G17', $solicitud['adm2_telefono'])

                //DATOS DE OPERACIÓN

                  /// preguntas
                  ->setCellValue('A25', $solicitud['preg1'])
                  ->setCellValue('B25', $solicitud['preg2'])
                  ->setCellValue('C25', $solicitud['preg3'])
                  ->setCellValue('D25', $solicitud['preg4'])
                  ->setCellValue('E25', $alcance_empresa)
                  ->setCellValue('F25', $solicitud['preg6'])
                  ->setCellValue('G25', $solicitud['preg7'])
                  ->setCellValue('H25', $solicitud['preg8'])
                  ->setCellValue('B27', $solicitud['preg10']);
        

              // TABLA CERTIFICACIONES
                  $contador = $nomCertificaciones;
                while($certificaciones = mysql_fetch_assoc($row_certificaciones)){
                  $objPHPExcel->setActiveSheetIndex(0)
                  ->setCellValue('A'.$contador, $certificaciones['certificacion'])
                  ->setCellValue('C'.$contador, $certificaciones['certificadora'])
                  ->setCellValue('E'.$contador, $certificaciones['ano_inicial'])
                  ->setCellValue('G'.$contador, $certificaciones['interrumpida']);
                  $contador++;
                }
                /// Preguntas despues de la tabla certificaciones
                $objPHPExcel->setActiveSheetIndex(0)
                  ->setCellValue('A'.$subRespuestas, $solicitud['preg12'])
                  ->setCellValue('B'.$subRespuestas, $porcentajeVentas)
                  ->setCellValue('C'.$subRespuestas, $solicitud['preg13'])
                  ->setCellValue('D'.$subRespuestas, $solicitud['preg14'])
                  ->setCellValue('A'.$subRespuestas, $solicitud['preg15']);


              // Productos de la tabla productos
              while($productos = mysql_fetch_assoc($row_productos)){
                $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$nomProductos, $productos['producto'])
                ->setCellValue('B'.$nomProductos, $productos['volumen'])
                ->setCellValue('C'.$nomProductos, $productos['terminado'])
                ->setCellValue('D'.$nomProductos, $productos['materia'])
                ->setCellValue('E'.$nomProductos, $productos['destino'])
                ->setCellValue('F'.$nomProductos, $productos['marca_propia'])
                ->setCellValue('G'.$nomProductos, $productos['marca_cliente'])
                ->setCellValue('H'.$nomProductos, $productos['sin_cliente']);
                $nomProductos++;
              }

    $estiloTituloColumnas = array(
            

            'fill'  => array(
        'type'    => PHPExcel_Style_Fill::FILL_SOLID,
        'color'   => array('rgb' => 'B8D186')
      ),
            /*'borders' => array(
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
            ),*/
      'alignment' =>  array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
              'wrap'          => TRUE
        ));

  $estiloPreguntas = array(
            

            'fill'  => array(
        'type'    => PHPExcel_Style_Fill::FILL_SOLID,
        'color'   => array('rgb' => 'ecf0f1')
      ),
           
      'alignment' =>  array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
              'wrap'          => TRUE
        ));

 
  /// APLICAR FORMATO DE COLOR Y TIPO DE TEXTO A LAS CELDAS
    $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($estiloTituloColumnas);
    $objPHPExcel->getActiveSheet()->getStyle('A6')->applyFromArray($estiloTituloColumnas);
    $objPHPExcel->getActiveSheet()->getStyle('A11')->applyFromArray($estiloTituloColumnas);
    $objPHPExcel->getActiveSheet()->getStyle('A19')->applyFromArray($estiloTituloColumnas);
    $objPHPExcel->getActiveSheet()->getStyle('A27')->applyFromArray($estiloPreguntas);
    /// ENCABEZADO DE LA TABLA PRODUCTOS
    $objPHPExcel->getActiveSheet()->getStyle('A'.$encabezadoProductos)->applyFromArray($estiloTituloColumnas);

  /// APLICAR ESTILO A LAS PREGUNTAS
    $objPHPExcel->getActiveSheet()->getStyle('A3')->applyFromArray($estiloPreguntas);
    $objPHPExcel->getActiveSheet()->getStyle('C3')->applyFromArray($estiloPreguntas);
    $objPHPExcel->getActiveSheet()->getStyle('E3')->applyFromArray($estiloPreguntas);
    $objPHPExcel->getActiveSheet()->getStyle('G3')->applyFromArray($estiloPreguntas);

    $objPHPExcel->getActiveSheet()->getStyle('A8')->applyFromArray($estiloPreguntas);
    $objPHPExcel->getActiveSheet()->getStyle('C8')->applyFromArray($estiloPreguntas);
    $objPHPExcel->getActiveSheet()->getStyle('D8')->applyFromArray($estiloPreguntas);
    $objPHPExcel->getActiveSheet()->getStyle('E8')->applyFromArray($estiloPreguntas);
    $objPHPExcel->getActiveSheet()->getStyle('F8')->applyFromArray($estiloPreguntas);
    $objPHPExcel->getActiveSheet()->getStyle('G8')->applyFromArray($estiloPreguntas);
    $objPHPExcel->getActiveSheet()->getStyle('H8')->applyFromArray($estiloPreguntas);

    $objPHPExcel->getActiveSheet()->getStyle('A13')->applyFromArray($estiloPreguntas);
    $objPHPExcel->getActiveSheet()->getStyle('C13')->applyFromArray($estiloPreguntas);
    $objPHPExcel->getActiveSheet()->getStyle('E13')->applyFromArray($estiloPreguntas);
    $objPHPExcel->getActiveSheet()->getStyle('G13')->applyFromArray($estiloPreguntas);

    $objPHPExcel->getActiveSheet()->getStyle('A21')->applyFromArray($estiloPreguntas);
    $objPHPExcel->getActiveSheet()->getStyle('C21')->applyFromArray($estiloPreguntas);
    $objPHPExcel->getActiveSheet()->getStyle('E21')->applyFromArray($estiloPreguntas);
    $objPHPExcel->getActiveSheet()->getStyle('G21')->applyFromArray($estiloPreguntas);

    $objPHPExcel->getActiveSheet()->getStyle('A24')->applyFromArray($estiloPreguntas);
    $objPHPExcel->getActiveSheet()->getStyle('B24')->applyFromArray($estiloPreguntas);
    $objPHPExcel->getActiveSheet()->getStyle('C24')->applyFromArray($estiloPreguntas);
    $objPHPExcel->getActiveSheet()->getStyle('D24')->applyFromArray($estiloPreguntas);
    $objPHPExcel->getActiveSheet()->getStyle('E24')->applyFromArray($estiloPreguntas);
    $objPHPExcel->getActiveSheet()->getStyle('F24')->applyFromArray($estiloPreguntas);
    $objPHPExcel->getActiveSheet()->getStyle('G24')->applyFromArray($estiloPreguntas);
    $objPHPExcel->getActiveSheet()->getStyle('H24')->applyFromArray($estiloPreguntas);

    $objPHPExcel->getActiveSheet()->getStyle('A28')->applyFromArray($estiloTituloColumnas);
    //$objPHPExcel->getActiveSheet()->getStyle('C28')->applyFromArray($estiloPreguntas);
    //$objPHPExcel->getActiveSheet()->getStyle('E28')->applyFromArray($estiloPreguntas);
    //$objPHPExcel->getActiveSheet()->getStyle('G28')->applyFromArray($estiloPreguntas);

    /// Sub Preguntas despues de la tabla certificaciones
      $objPHPExcel->getActiveSheet()->getStyle('A'.($subPreguntas))->applyFromArray($estiloPreguntas);
      $objPHPExcel->getActiveSheet()->getStyle('B'.($subPreguntas))->applyFromArray($estiloPreguntas);
      $objPHPExcel->getActiveSheet()->getStyle('C'.($subPreguntas))->applyFromArray($estiloPreguntas);
      $objPHPExcel->getActiveSheet()->getStyle('D'.($subPreguntas))->applyFromArray($estiloPreguntas);
      $objPHPExcel->getActiveSheet()->getStyle('E'.($subPreguntas))->applyFromArray($estiloPreguntas);

      // Sub Encabezados de la tabla productos
      $objPHPExcel->getActiveSheet()->getStyle('A'.$subEncabezados)->applyFromArray($estiloPreguntas);
      $objPHPExcel->getActiveSheet()->getStyle('B'.$subEncabezados)->applyFromArray($estiloPreguntas);
      $objPHPExcel->getActiveSheet()->getStyle('C'.$subEncabezados)->applyFromArray($estiloPreguntas);
      $objPHPExcel->getActiveSheet()->getStyle('D'.$subEncabezados)->applyFromArray($estiloPreguntas);
      $objPHPExcel->getActiveSheet()->getStyle('E'.$subEncabezados)->applyFromArray($estiloPreguntas);
      $objPHPExcel->getActiveSheet()->getStyle('F'.$subEncabezados)->applyFromArray($estiloPreguntas);
      $objPHPExcel->getActiveSheet()->getStyle('G'.$subEncabezados)->applyFromArray($estiloPreguntas);
      $objPHPExcel->getActiveSheet()->getStyle('H'.$subEncabezados)->applyFromArray($estiloPreguntas);




    /// AJUSTAR EL TEXTO DE LAS COLUMNAS
  $objPHPExcel->getActiveSheet()->getStyle('A1:H1'.$objPHPExcel->getActiveSheet()->getHighestRow())
    ->getAlignment()->setWrapText(true);
   
    //$objPHPExcel->getActiveSheet()->getStyle('A3:K3')->applyFromArray($estiloTituloColumnas);   
  
    /// APLICAR TAMAÑO PREDEFINIDO A LAS COLUMNAS
    for($i = 'A'; $i <= 'H'; $i++){
      $objPHPExcel->setActiveSheetIndex(0)      
        ->getColumnDimension($i)->setWidth(30);
    }
    
    /// APLICAR FUENTE Y TAMAÑO DE LETRA GENERAL
    /*$estilo = array(
    'font' => array(
                'name'      => 'Helvetica Neue',
                'bold'      => true,                          
                'color'     => array(
                    'rgb' => '2c3e50'
                )
            )
    );

    $objPHPExcel->getDefaultStyle()
    ->applyFromArray($estilo);
    */

    
    // Se asigna el nombre a la hoja
    $objPHPExcel->getActiveSheet()->setTitle('Demande d\'inscription');

    // Se activa la hoja para que sea la que se muestre cuando el archivo se abre
    $objPHPExcel->setActiveSheetIndex(0);
    // Inmovilizar paneles 
    //$objPHPExcel->getActiveSheet(0)->freezePane('A4');
    $objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(0,4);

    // Se manda el archivo al navegador web, con el nombre que se indica (Excel2007)
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Demande d\'inscription.xls"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    exit;
  


 ?>