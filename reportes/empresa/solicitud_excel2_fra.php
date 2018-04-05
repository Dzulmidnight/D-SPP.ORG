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
               ->setTitle("DEMANDE DE CERTIFICATION")
               ->setSubject("DEMANDE DE CERTIFICATION")
               ->setDescription("DEMANDE DE CERTIFICATION")
               ->setKeywords("DEMANDE DE CERTIFICATION")
               ->setCategory("DEMANDE DE CERTIFICATION");

    $tituloReporte = "DEMANDE DE CERTIFICATION";

          
    ///// HOJA DE DATOS GENERALES ////
    $objPHPExcel->setActiveSheetIndex(0)
          //// ENCABEZADOS
          ->setCellValue('A1', 'CHAMPS')
          ->setCellValue('B1', 'VALEUR')

          //// datos generales
          ->setCellValue('A2','DATE DE REALISATION')
            ->setCellValue('B2', date('Y-m-d', $solicitud['fecha_registro']))
          ->setCellValue('A3', 'TYPE DE DEMANDE')
            ->setCellValue('B3', $solicitud['tipo_solicitud'])
          ->setCellValue('A4', 'CODE D’IDENTIFICATION SPP (#SPP)')
            ->setCellValue('B4', $solicitud['spp_opp'])
          ->setCellValue('A5', 'PROCEDURE DE CERTIFICATION')
            ->setCellValue('B5', $solicitud['tipo_procedimiento'])
          ->setCellValue('A6', 'DENOMINATION SOCIALE COMPLETE DE L’ORGANISATION DE PETITS PRODUCTEURS')
            ->setCellValue('B6', $solicitud['nombre_opp'])
          ->setCellValue('A7', 'ADRESSE COMPLETE DU SIEGE SOCIAL')
            ->setCellValue('B7', $solicitud['direccion_oficina'])
          ->setCellValue('A8', 'PAYS')
            ->setCellValue('B8', $solicitud['pais'])
          ->setCellValue('A9', 'ADRESSE MAIL')
            ->setCellValue('B9', $solicitud['email_opp'])
          ->setCellValue('A10', 'TELEPHONE')
            ->setCellValue('B10', $solicitud['telefono_opp'])
          ->setCellValue('A11', 'SITE WEB')
            ->setCellValue('B11', $solicitud['sitio_web'])
          ->setCellValue('A12', 'ADRESSE FISCALE')
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
            ->setCellValue('A'.$contador++, 'CONTACT '.$contador0)
              ->setCellValue('B'.$contador1_1++, $contacto['nombre'])
            ->setCellValue('A'.$contador++, 'FONCTION '.$contador0)
              ->setCellValue('B'.$contador1_1++, $contacto['cargo'])
            ->setCellValue('A'.$contador++, 'ADRESSE MAIL '.$contador0)
              ->setCellValue('B'.$contador1_1++, $contacto['email1'])
            ->setCellValue('A'.$contador++, 'TELEPHONE '.$contador0)
              ->setCellValue('B'.$contador1_1++, $contacto['telefono1']);

            $contador++;
            $contador1_1++;
            $contador0++;

          }

          $contador++;
          $contador1_2 = $contador;

    $objPHPExcel->setActiveSheetIndex(0)
          //// datos de operación
          ->setCellValue('A'.$contador++, 'NOMBRE DE MEMBRES PRODUCTEURS')
            ->setCellValue('B'.$contador1_2++, $solicitud['resp1'])
          ->setCellValue('A'.$contador++, 'NOMBRE DE MEMBRES PRODUCTEURS DU (DES) PRODUIT(S) A INCLUIRE DANS LA CERTIFICATION  ')
            ->setCellValue('B'.$contador1_2++, $solicitud['resp2'])
          ->setCellValue('A'.$contador++, 'VOLUME(S) DE PRODUCTION TOTALE PAR PRODUIT (UNITE DE MESURE)')
            ->setCellValue('B'.$contador1_2++, $solicitud['resp3'])
          ->setCellValue('A'.$contador++, 'TAILLE MAXIMALE DE L’UNITE DE PRODUCTION PAR PRODUCTEUR DU (DES) PRODUIT(S) A INCLURE DANS LA CERTIFICATION')
            ->setCellValue('B'.$contador1_2++, $solicitud['resp4'])
          ->setCellValue('A'.$contador++, '1. INDIQUEZ-S’IL S’AGIT D’UNE ORGANISATION DE PETITS PRODUCTEURS DE 1er, 2eme, 3eme OU 4eme NIVEAU, AINSI QUE LE NOMBRE D’OPP DE 3eme, 2eme OU 1er NIVEAU ET LE NOMBRE DE COMMUNAUTES, DE ZONES OU DE GROUPES DE TRAVAIL DONT VOUS DISPOSEZ')
            ->setCellValue('B'.$contador1_2++, $solicitud['op_preg1'])
          ->setCellValue('A'.$contador++, '2. INDIQUEZ QUEL(S) PRODUIT(S) VOUS SOUHAITEZ INCLURE DANS LA CERTIFICATION DU SYMBOLE DES PETITS PRODUCTEURS POUR LE(S) QUEL (S) L’ORGANISME DE CERTIFICATION REALIZERA L’EVALUATION')
            ->setCellValue('B'.$contador1_2++, $solicitud['op_preg2'])
          ->setCellValue('A'.$contador++, '3. INDIQUEZ SI VOTRE ORGANISATION SOUHAITE INCLURE UNE QUALIFICATION OPTIONNELLE POUR UNE UTILISATION COMPLEMENTAIRE AVEC LE LOGO GRAPHIQUE DU SYMBOLE DES PETITS PRODUCTEURS')
            ->setCellValue('B'.$contador1_2++, $solicitud['op_preg3'])
          ->setCellValue('A'.$contador++, '4. MARQUEZ D’UNE CROIX L’ACTIVITE EXERCEE PAR L’ORGANISATION DES PETITS PRODUCTEURS')
            ->setCellValue('B'.$contador1_2++, $alcance_opp)
          ->setCellValue('A'.$contador++, '5. INDIQUEZ SI VOUS UTILISEZ EN SOUS-TRAITANCE LES SERVICES D’USINES DE TRANSFORMATION, D’ENTREPRISES DE COMMERCIALISATION OU D’ENTREPRISES D’IMPORT/EXPORT, LE CAS ECHEANT, MENTIONNEZ LE TYPE DE SERVICE REALISE')
            ->setCellValue('B'.$contador1_2++, $solicitud['op_preg5'])
          ->setCellValue('A'.$contador++, '6. SI VOUS SOUS-TRAITEZ DES SERVICES A DES USINES DE TRANSFORMATION, A DES ENTREPRISES DE COMMERCIALISATION OU A DES ENTREPRISES D’IMPORT/EXPORT, INDIQUEZ SI CELLES-CI SONT ENREGISTREES, EN COURS D’ENREGISTREMENT SOUS LE PROGRAMME DU SPP OU SI ELLES SERONT CONTROLEES AU TRAVERS DE L’ORGANISATION DE PETITS PRODUCTEURS')
            ->setCellValue('B'.$contador1_2++, $solicitud['op_preg6'])
          ->setCellValue('A'.$contador++, '7. EN PLUS DE VOTRE SIEGE SOCIAL, INDIQUEZ LE NOMBRE DE CENTRES DE COLLECTE, DE TRANSFORMATION OU DE BUREAUX SUPPLEMENTAIRES QUE VOUS POSSEDEZ')
            ->setCellValue('B'.$contador1_2++, $solicitud['op_preg7'])
          ->setCellValue('A'.$contador++, '8. EST-CE QUE VOUS DISPOSEZ D’UN SYSTEME DE CONTROLE INTERNE AFIN DE RESPECTER LES CRITERES DE LA NORME GENERALE DU SYMBOLE DES PETITS PRODUCTEURS? DANS CE CAS VEUILLEZ EXPLIQUER')
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
              ->setCellValue('A'.$contador2++, 'CERTIFICATION '.$contador0)
                ->setCellValue('B'.$contador1_3++, $certificaciones['certificacion'])
              ->setCellValue('A'.$contador2++, 'CERTIFICATEUR '.$contador0)
                ->setCellValue('B'.$contador1_3++, $certificaciones['certificadora'])
              ->setCellValue('A'.$contador2++, 'ANNEE DE LA CERTIFICATION '.$contador0)
                ->setCellValue('B'.$contador1_3++, $certificaciones['ano_inicial'])
              ->setCellValue('A'.$contador2++, 'A-T-ELLE ETE INTERROMPUE? '.$contador0)
                ->setCellValue('B'.$contador1_3++, $certificaciones['interrumpida']);

                $contador2++;
                $contador1_3++;
                $contador0++;
            }
            $contador2_1 = $contador2+=2;
            $contador1_4 = $contador2_1;

            $objPHPExcel->setActiveSheetIndex(0)
              

              ->setCellValue('A'.$contador2_1++, 'DES CERTIFICATIONS AVEC LESQUELLES VOUS AVEZ, DANS VOTRE EVALUATION INTERNE ET EXTERNE LA PLUS RECENTE, COMBIEN DE NON-CONFORMITE ONT ETE IDENTIFIEES? ET DANS VOTRE CAS, ÊTES-VOUS RÉSOLU OU QUEL EST VOTRE ÉTAT?')
                ->setCellValue('B'.$contador1_4++, $solicitud['op_preg10'])
                //->setCellValue('B'.$contador2_11+=2, $solicitud['op_preg10'])
              
              ->setCellValue('A'.$contador2_1++, 'AVEZ-VOUS REALISE DES VENTES SOUS LE SPP DURANT LE CYCLE DE CERTIFICATION ANTERIEUR ?')
                ->setCellValue('B'.$contador1_4++, $solicitud['op_preg12'])
                //->setCellValue('B'.$contador2_11++, $solicitud['op_preg12'])
              
              ->setCellValue('A'.$contador2_1++, 'LE CAS ECHEANT, MERCI DE MARQUER D’UNE CROIX LE RANG DE LA VALEUR TOTALE DE VOS VENTES SOUS LE SPP POUR LE CYCLE ANTERIEUR SELON LE TABLEAU SUIVANT')
                ->setCellValue('B'.$contador1_4++, $solicitud['op_preg13'])
                //->setCellValue('B'.$contador2_11++, $solicitud['op_preg13'])
              
              ->setCellValue('A'.$contador2_1++, 'SUR L’ENSEMBLE DE VOS VENTES, QUEL EST LE POURCENTAGE REALISE SOUS LES CERTIFICATIONS BIOLOGIQUES, DU COMMERCE EQUITABLE ET / OU DU SYMBOLE DES PETITS PRODUCTEURS ?')
                ->setCellValue('B'.$contador1_4++, $porcentajeVentas)
                //->setCellValue('B'.$contador2_11++, $porcentajeVentas)
              
              ->setCellValue('A'.$contador2_1++, 'DATE ESTIMEE DE DEBUT D’UTILISATION DU SYMBOLE DES PETITS PRODUCTEURS')
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

              ->setCellValue('A'.$contador3++, 'PRODUIT '.$contador0)
                ->setCellValue('B'.$contador1_5++, $productos['producto'])
                //->setCellValue('B'.$contador31++, $productos['producto'])

              ->setCellValue('A'.$contador3++, 'VOLUME TOTAL ESTIMÉ À COMMERCIALISER '.$contador0)
                ->setCellValue('B'.$contador1_5++, $productos['volumen'])
                //->setCellValue('B'.$contador31++, $productos['volumen'])

              ->setCellValue('A'.$contador3++, 'PRODUIT FINIT '.$contador0)
                ->setCellValue('B'.$contador1_5++, $productos['terminado'])
                //->setCellValue('B'.$contador31++, $productos['terminado'])

              ->setCellValue('A'.$contador3++, 'MATIÈRE PREMIÈRE '.$contador0)
                ->setCellValue('B'.$contador1_5++, $productos['materia'])
                //->setCellValue('B'.$contador31++, $productos['materia'])

              ->setCellValue('A'.$contador3++, 'PAYS DE DESTINATION '.$contador0)
                ->setCellValue('B'.$contador1_5++, $productos['destino'])
                //->setCellValue('B'.$contador31++, $productos['destino'])

              ->setCellValue('A'.$contador3++, 'MARQUE PROPRE '.$contador0)
                ->setCellValue('B'.$contador1_5++, $productos['marca_propia'])
                //->setCellValue('B'.$contador31++, $productos['marca_propia'])

              ->setCellValue('A'.$contador3++, 'MARQUE D’UN CLIENT '.$contador0)
                ->setCellValue('B'.$contador1_5++, $productos['marca_cliente'])
                //->setCellValue('B'.$contador31++, $productos['marca_cliente'])

              ->setCellValue('A'.$contador3++, 'PAS ENCORE DE CLIENT '.$contador0)
                ->setCellValue('B'.$contador1_5++, $productos['sin_cliente']);
                //->setCellValue('B'.$contador1++, $productos['sin_cliente']);
                $contador3++;
                $contador1_5++;
                $contador0++;
            }

    // NOMBRE DE LA PESTAÑA(hoja)
    $objPHPExcel->getActiveSheet(0)->setTitle('DEMANDE');


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