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
               ->setTitle("Demande de Certification")
               ->setSubject("Demande de Certification")
               ->setDescription("Demande de Certification")
               ->setKeywords("Demande de Certification")
               ->setCategory("Demande de Certification");

    $tituloReporte = "Demande de Certification";

          
    ///// HOJA DE DATOS GENERALES ////
    $objPHPExcel->setActiveSheetIndex(0)
          //// TITULOS
          ->setCellValue('A3','DATE DE REALISATION')
          ->setCellValue('A4', 'TYPE DE DEMANDE')
          ->setCellValue('A5', 'CODE D’IDENTIFICATION SPP (#SPP)')
          ->setCellValue('A6', 'PROCEDURE DE CERTIFICATION')
          ->setCellValue('A7', 'DENOMINATION SOCIALE COMPLETE DE L’ORGANISATION DE PETITS PRODUCTEURS:  ')
          ->setCellValue('A8', 'ADRESSE COMPLETE DU SIEGE SOCIAL')
          ->setCellValue('A9', 'PAYS')
          ->setCellValue('A10', 'ADRESSE MAIL')
          ->setCellValue('A11', 'TELEPHONE')
          ->setCellValue('A12', 'SITE WEB')
          ->setCellValue('A13', 'ADRESSE FISCALE')
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
    $objPHPExcel->getActiveSheet(0)->setTitle('INFORMATIONS GENERALES');


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
          ->setCellValue('A3', 'NOM')
          ->setCellValue('A4', 'FONCTION')
          ->setCellValue('A5', 'ADRESSE MAIL')
          ->setCellValue('A6', 'TELEPHONE')
            //datos
            ->setCellValue('B3', $solicitud['contacto1_nombre']) 
            ->setCellValue('B4', $solicitud['contacto1_cargo'])
            ->setCellValue('B5', $solicitud['contacto1_email'])
            ->setCellValue('B6', $solicitud['contacto1_telefono'])
          /// TITULOS
          ->setCellValue('A3', 'NOM')
          ->setCellValue('A4', 'FONCTION')
          ->setCellValue('A5', 'ADRESSE MAIL')
          ->setCellValue('A6', 'TELEPHONE')
            /// datos
            ->setCellValue('B7', $solicitud['contacto2_nombre'])
            ->setCellValue('B8', $solicitud['contacto2_cargo'])
            ->setCellValue('B9', $solicitud['contacto2_email'])
            ->setCellValue('B10', $solicitud['contacto2_telefono'])
          /// TITULOS
          ->setCellValue('A3', 'NOM')
          ->setCellValue('A4', 'FONCTION')
          ->setCellValue('A5', 'ADRESSE MAIL')
          ->setCellValue('A6', 'TELEPHONE')
            // datos
            ->setCellValue('B11', $solicitud['adm1_nombre'])
            ->setCellValue('B12', $solicitud['adm1_email'])
            ->setCellValue('B13', 'ADMINISTRADOR')
            ->setCellValue('B14', $solicitud['adm1_telefono'])
          /// contactos
          ->setCellValue('A3', 'NOM')
          ->setCellValue('A4', 'FONCTION')
          ->setCellValue('A5', 'ADRESSE MAIL')
          ->setCellValue('A6', 'TELEPHONE')
            // datos
            ->setCellValue('B15', $solicitud['adm2_nombre'])
            ->setCellValue('B16', $solicitud['adm2_email'])
            ->setCellValue('B17', 'ADMINISTRADOR')
            ->setCellValue('B18', $solicitud['adm2_telefono']);

    // NOMBRE DE LA PESTAÑA(hoja)
    $objPHPExcel->getActiveSheet(1)->setTitle('PERSONNE A CONTACTER');

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
          ->setCellValue('A3', 'NOMBRE DE MEMBRES PRODUCTEURS')
          ->setCellValue('A4', 'NOMBRE DE MEMBRES PRODUCTEURS DU (DES) PRODUIT(S) A INCLUIRE DANS LA CERTIFICATION  ')
          ->setCellValue('A5', 'VOLUME(S) DE PRODUCTION TOTALE PAR PRODUIT (UNITE DE MESURE)')
          ->setCellValue('A6', 'TAILLE MAXIMALE DE L’UNITE DE PRODUCTION PAR PRODUCTEUR DU (DES) PRODUIT(S) A INCLURE DANS LA CERTIFICATION')
          ->setCellValue('A7', '1. INDIQUEZ-S’IL S’AGIT D’UNE ORGANISATION DE PETITS PRODUCTEURS DE 1er, 2eme, 3eme OU 4eme NIVEAU, AINSI QUE LE NOMBRE D’OPP DE 3eme, 2eme OU 1er NIVEAU ET LE NOMBRE DE COMMUNAUTES, DE ZONES OU DE GROUPES DE TRAVAIL DONT VOUS DISPOSEZ')
          ->setCellValue('A8', '2. INDIQUEZ QUEL(S) PRODUIT(S) VOUS SOUHAITEZ INCLURE DANS LA CERTIFICATION DU SYMBOLE DES PETITS PRODUCTEURS POUR LE(S) QUEL (S) L’ORGANISME DE CERTIFICATION REALIZERA L’EVALUATION')
          ->setCellValue('A9', '3. INDIQUEZ SI VOTRE ORGANISATION SOUHAITE INCLURE UNE QUALIFICATION OPTIONNELLE POUR UNE UTILISATION COMPLEMENTAIRE AVEC LE LOGO GRAPHIQUE DU SYMBOLE DES PETITS PRODUCTEURS')
          ->setCellValue('A10', '4. MARQUEZ D’UNE CROIX L’ACTIVITE EXERCEE PAR L’ORGANISATION DES PETITS PRODUCTEURS')
          ->setCellValue('A11', '5. INDIQUEZ SI VOUS UTILISEZ EN SOUS-TRAITANCE LES SERVICES D’USINES DE TRANSFORMATION, D’ENTREPRISES DE COMMERCIALISATION OU D’ENTREPRISES D’IMPORT/EXPORT, LE CAS ECHEANT, MENTIONNEZ LE TYPE DE SERVICE REALISE')
          ->setCellValue('A12', '6. SI VOUS SOUS-TRAITEZ DES SERVICES A DES USINES DE TRANSFORMATION, A DES ENTREPRISES DE COMMERCIALISATION OU A DES ENTREPRISES D’IMPORT/EXPORT, INDIQUEZ SI CELLES-CI SONT ENREGISTREES, EN COURS D’ENREGISTREMENT SOUS LE PROGRAMME DU SPP OU SI ELLES SERONT CONTROLEES AU TRAVERS DE L’ORGANISATION DE PETITS PRODUCTEURS')
          ->setCellValue('A13', '7. EN PLUS DE VOTRE SIEGE SOCIAL, INDIQUEZ LE NOMBRE DE CENTRES DE COLLECTE, DE TRANSFORMATION OU DE BUREAUX SUPPLEMENTAIRES QUE VOUS POSSEDEZ')
          ->setCellValue('A14', '8. EST-CE QUE VOUS DISPOSEZ D’UN SYSTEME DE CONTROLE INTERNE AFIN DE RESPECTER LES CRITERES DE LA NORME GENERALE DU SYMBOLE DES PETITS PRODUCTEURS? DANS CE CAS VEUILLEZ EXPLIQUER')

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
    $objPHPExcel->getActiveSheet(2)->setTitle('DONNÉES D\'OPÉRATION');

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
              ->setCellValue('A'.$contador++, 'CERTIFICATION')
              ->setCellValue('B'.$contador1++, $certificaciones['certificacion'])
              ->setCellValue('A'.$contador++, 'CERTIFICATEUR')
              ->setCellValue('B'.$contador1++, $certificaciones['certificadora'])
                /// datos
                ->setCellValue('A'.$contador++, 'ANNEE DE LA CERTIFICATION')
                ->setCellValue('B'.$contador1++, $certificaciones['ano_inicial'])
                ->setCellValue('A'.$contador++, 'A-T-ELLE ETE INTERROMPUE?')
                ->setCellValue('B'.$contador1++, $certificaciones['interrumpida']);

                $contador++;
                $contador1++;
            }
            $objPHPExcel->setActiveSheetIndex(3)

              ->setCellValue('A'.$contador+=2, 'PARMI LES CERTIFICATIONS DONT VOUS DISPOSEZ ET LORS DE LEUR PLUS RECENTE EVALUATION INTERNE ET EXTERNE, COMBIEN DE NON CONFORMITES ONT ETE IDENTIFIEES? CELLES-CI ONT-ELLES ETE RESOLUES? QUEL EST LEUR ETAT ACTUEL?')
                ->setCellValue('B'.$contador1+=2, $solicitud['op_preg10'])
              
              ->setCellValue('A'.$contador++, 'AVEZ-VOUS REALISE DES VENTES SOUS LE SPP DURANT LE CYCLE DE CERTIFICATION ANTERIEUR ?')
                ->setCellValue('B'.$contador1++, $solicitud['op_preg12'])
              
              ->setCellValue('A'.$contador++, 'LE CAS ECHEANT, MERCI DE MARQUER D’UNE CROIX LE RANG DE LA VALEUR TOTALE DE VOS VENTES SOUS LE SPP POUR LE CYCLE ANTERIEUR SELON LE TABLEAU SUIVANT')
                ->setCellValue('B'.$contador1++, $solicitud['op_preg13'])
              
              ->setCellValue('A'.$contador++, 'SUR L’ENSEMBLE DE VOS VENTES, QUEL EST LE POURCENTAGE REALISE SOUS LES CERTIFICATIONS BIOLOGIQUES, DU COMMERCE EQUITABLE ET / OU DU SYMBOLE DES PETITS PRODUCTEURS ?')
                ->setCellValue('B'.$contador1++, $porcentajeVentas)
              
              ->setCellValue('A'.$contador++, 'DATE ESTIMEE DE DEBUT D’UTILISATION DU SYMBOLE DES PETITS PRODUCTEURS')
                ->setCellValue('B'.$contador1++, $solicitud['op_preg14']);
                
    // NOMBRE DE LA PESTAÑA(hoja)
    $objPHPExcel->getActiveSheet(3)->setTitle('CERTIFICATIONS');

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

              ->setCellValue('A'.$contador++, 'PRODUIT')
                ->setCellValue('B'.$contador1++, $productos['producto'])

              ->setCellValue('A'.$contador++, 'Volume Total Estimé à Commercialiser')
                ->setCellValue('B'.$contador1++, $productos['volumen'])

              ->setCellValue('A'.$contador++, 'Produit Finit')
                ->setCellValue('B'.$contador1++, $productos['terminado'])

              ->setCellValue('A'.$contador++, 'Matière Première')
                ->setCellValue('B'.$contador1++, $productos['materia'])

              ->setCellValue('A'.$contador++, 'Pays de Destination')
                ->setCellValue('B'.$contador1++, $productos['destino'])

              ->setCellValue('A'.$contador++, 'Marque Propre')
                ->setCellValue('B'.$contador1++, $productos['marca_propia'])

              ->setCellValue('A'.$contador++, 'Marque d’un Client')
                ->setCellValue('B'.$contador1++, $productos['marca_cliente'])

              ->setCellValue('A'.$contador++, 'Pas encore de client')
                ->setCellValue('B'.$contador1++, $productos['sin_cliente']);
              
              $contador++;
              $contador1++;
                
            }
            
    // NOMBRE DE LA PESTAÑA(hoja)
    $objPHPExcel->getActiveSheet(4)->setTitle('PRODUITS');
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