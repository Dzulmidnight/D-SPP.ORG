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

    // Se asignan las propiedades del libro
    $objPHPExcel->getProperties()->setCreator("spp global") //Autor
               ->setLastModifiedBy("spp global") //Ultimo usuario que lo modificó
               ->setTitle("Demande de Certification")
               ->setSubject("Demande de Certification")
               ->setDescription("Demande de Certification")
               ->setKeywords("Demande de Certification")
               ->setCategory("Demande de Certification");

    $tituloReporte = "Demande de Certification";

    
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
                ->mergeCells('A27:H27')
                  ->mergeCells('A28:B28')
                  ->mergeCells('C28:D28')
                  ->mergeCells('E28:F28')
                  ->mergeCells('G28:H28')
                /// ENCABEZADO TABLA PRODUCTOS
                ->mergeCells('A'.$encabezadoProductos.':H'.$encabezadoProductos);
            
    // Se agregan los titulos del reporte
    $objPHPExcel->setActiveSheetIndex(0)
          ->setCellValue('A1',$tituloReporte)
          ->setCellValue('A3','DATE DE REALISATION')
          ->setCellValue('C3', 'TYPE DE DEMANDE')
          ->setCellValue('E3', 'CODE D’IDENTIFICATION SPP (#SPP):')
          ->setCellValue('G3', 'PROCEDURE DE CERTIFICATION:')
          ->setCellValue('A6', 'INFORMATIONS GENERALES')
          ->setCellValue('A8', 'DENOMINATION SOCIALE COMPLETE DE L’ORGANISATION DE PETITS PRODUCTEURS:')
          ->setCellValue('C8', 'ADRESSE COMPLETE DU SIEGE SOCIAL')
          ->setCellValue('D8', 'PAYS')
          ->setCellValue('E8', 'ADRESSE MAIL')
          ->setCellValue('F8', 'TELEPHONE')
          ->setCellValue('G8', 'SITE WEB')
          ->setCellValue('H8', 'INFORMATIONS FISCALES')
          ->setCellValue('A11', 'PERSONNE(S) A CONTACTER')
          ->setCellValue('A13', 'NOM')
          ->setCellValue('C13', 'FONCTION(S)')
          ->setCellValue('E13', 'ADRESSE MAIL')
          ->setCellValue('G13', 'TELEPHONE')
          ->setCellValue('A19', 'INFORMATIONS SUR LE TYPE D’OPERATION')
          ->setCellValue('A21', 'NOMBRE DE MEMBRES PRODUCTEURS:')
          ->setCellValue('C21', 'NOMBRE DE MEMBRES PRODUCTEURS DU (DES) PRODUIT(S) A INCLUIRE DANS LA CERTIFICATION')
          ->setCellValue('E21', 'VOLUME(S) DE PRODUCTION TOTALE PAR PRODUIT (UNITE DE MESURE) :')
          ->setCellValue('G21', 'TAILLE MAXIMALE DE L’UNITE DE PRODUCTION PAR PRODUCTEUR DU (DES) PRODUIT(S) A INCLURE DANS LA CERTIFICATION')
          ->setCellValue('A24', '1. INDIQUEZ-S’IL S’AGIT D’UNE ORGANISATION DE PETITS PRODUCTEURS DE 1er, 2eme, 3eme OU 4eme NIVEAU, AINSI QUE LE NOMBRE D’OPP DE 3eme, 2eme OU 1er NIVEAU ET LE NOMBRE DE COMMUNAUTES, DE ZONES OU DE GROUPES DE TRAVAIL DONT VOUS DISPOSEZ :')
          ->setCellValue('B24', '2. INDIQUEZ QUEL(S) PRODUIT(S) VOUS SOUHAITEZ INCLURE DANS LA CERTIFICATION DU SYMBOLE DES PETITS PRODUCTEURS POUR LE(S) QUEL (S) L’ORGANISME DE CERTIFICATION REALIZERA L’EVALUATION.')
          ->setCellValue('C24', '3. INDIQUEZ SI VOTRE ORGANISATION SOUHAITE INCLURE UNE QUALIFICATION OPTIONNELLE POUR UNE UTILISATION COMPLEMENTAIRE AVEC LE LOGO GRAPHIQUE DU SYMBOLE DES PETITS PRODUCTEURS.')
          ->setCellValue('D24', '4. MARQUEZ D’UNE CROIX L’ACTIVITE EXERCEE PAR L’ORGANISATION DES PETITS PRODUCTEURS :')
          ->setCellValue('E24', '5. INDIQUEZ SI VOUS UTILISEZ EN SOUS-TRAITANCE LES SERVICES D’USINES DE TRANSFORMATION, D’ENTREPRISES DE COMMERCIALISATION OU D’ENTREPRISES D’IMPORT/EXPORT, LE CAS ECHEANT, MENTIONNEZ LE TYPE DE SERVICE REALISE')
          ->setCellValue('F24', '6. SI VOUS SOUS-TRAITEZ DES SERVICES A DES USINES DE TRANSFORMATION, A DES ENTREPRISES DE COMMERCIALISATION OU A DES ENTREPRISES D’IMPORT/EXPORT, INDIQUEZ SI CELLES-CI SONT ENREGISTREES, EN COURS D’ENREGISTREMENT SOUS LE PROGRAMME DU SPP OU SI ELLES SERONT CONTROLEES AU TRAVERS DE L’ORGANISATION DE PETITS PRODUCTEURS.')
          ->setCellValue('G24', '7. EN PLUS DE VOTRE SIEGE SOCIAL, INDIQUEZ LE NOMBRE DE CENTRES DE COLLECTE, DE TRANSFORMATION OU DE BUREAUX SUPPLEMENTAIRES QUE VOUS POSSEDEZ.')
          ->setCellValue('H24', '8. EST-CE QUE VOUS DISPOSEZ D’UN SYSTEME DE CONTROLE INTERNE AFIN DE RESPECTER LES CRITERES DE LA NORME GENERALE DU SYMBOLE DES PETITS PRODUCTEURS? DANS CE CAS VEUILLEZ EXPLIQUER')
          ->setCellValue('A27', '9. REMPLIR LE TABLEAU DE VOS CERTIFICATIONS, (EXEMPLE: EU, NOP, JASS, FLO, etc.)')
          ->setCellValue('A28', 'CERTIFICATION')
          ->setCellValue('C28', 'CERTIFICATEUR')
          ->setCellValue('E28', 'ANNEE DE LA CERTIFICATION')
          ->setCellValue('G28', 'A-T-ELLE ETE INTERROMPUE?')
          /// SUB PREGUNTAS
            ->setCellValue('A'.$subPreguntas, '10. PARMI LES CERTIFICATIONS DONT VOUS DISPOSEZ ET LORS DE LEUR PLUS RECENTE EVALUATION INTERNE ET EXTERNE, COMBIEN DE NON CONFORMITES ONT ETE IDENTIFIEES? CELLES-CI ONT-ELLES ETE RESOLUES? QUEL EST LEUR ETAT ACTUEL?')
            ->setCellValue('B'.$subPreguntas, '12. AVEZ-VOUS REALISE DES VENTES SOUS LE SPP DURANT LE CYCLE DE CERTIFICATION ANTERIEUR ?')
            ->setCellValue('C'.$subPreguntas, '13. LE CAS ECHEANT, MERCI DE MARQUER D’UNE CROIX LE RANG DE LA VALEUR TOTALE DE VOS VENTES SOUS LE SPP POUR LE CYCLE ANTERIEUR SELON LE TABLEAU SUIVANT :')
            ->setCellValue('D'.$subPreguntas, '13_1. SUR L’ENSEMBLE DE VOS VENTES, QUEL EST LE POURCENTAGE REALISE SOUS LES CERTIFICATIONS BIOLOGIQUES, DU COMMERCE EQUITABLE ET / OU DU SYMBOLE DES PETITS PRODUCTEURS ?')
            ->setCellValue('E'.$subPreguntas, '14. DATE ESTIMEE DE DEBUT D’UTILISATION DU SYMBOLE DES PETITS PRODUCTEURS :')

          /// ENCABEZADO PRODUCTOS
          ->setCellValue('A'.$encabezadoProductos, 'INFORMATIONS SUR LES PRODUITS POUR LESQUELS VOUS DEMANDEZ A UTILISER LE SYMBOLE')
            //// sub-encabezados
            ->setCellValue('A'.$subEncabezados, 'Produit')
            ->setCellValue('B'.$subEncabezados, 'Volume Total Estimé à Commercialiser')
            ->setCellValue('C'.$subEncabezados, 'Produit Finit')
            ->setCellValue('D'.$subEncabezados, 'Matière Première')
            ->setCellValue('E'.$subEncabezados, 'Pays de Destination')
            ->setCellValue('F'.$subEncabezados, 'Marque Propre')
            ->setCellValue('G'.$subEncabezados, 'Marque d’un Client')
            ->setCellValue('H'.$subEncabezados, 'Pas encore de client');

  
     //// VACIAR INFORMACIÓN
    $datos_fiscales = 'DIRECCIÓN FISCAL'.$solicitud['direccion_fiscal'].', RFC: '.$solicitud['rfc'].', RUC'.$solicitud['ruc'];
    $alcance_opp = '';
    $porcentajeVentas = '';

    if($solicitud['produccion']){
      $alcance_opp .= 'PRODUCCIÓN - ';
    }else if($solicitud['procesamiento']){
    $alcance_opp .= 'PROCESAMIENTO - ';
    }else if($solicitud['exportacion']){
    $alcance_opp .= 'EXPORTACIÓN - ';
    }

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


    $objPHPExcel->setActiveSheetIndex(0)
          // INFORMACIÓN SOLICITUD
                ->setCellValue('A4',  date('Y-m-d', $solicitud['fecha_registro']))
                ->setCellValue('C4', $solicitud['tipo_solicitud'])
                ->setCellValue('E4', $solicitud['spp_opp'])
                ->setCellValue('G4', $solicitud['tipo_procedimiento'])

                // DATOS GENERALES
                ->setCellValue('A9', $solicitud['nombre_opp'])
                ->setCellValue('C9', $solicitud['direccion_oficina'])
                ->setCellValue('D9', $solicitud['pais'])
                ->setCellValue('E9', $solicitud['email_opp'])
                ->setCellValue('F9', $solicitud['telefono_opp'])
                ->setCellValue('G9', $solicitud['sitio_web'])
                ->setCellValue('H9', $datos_fiscales)

                /// contactos
                ->setCellValue('A14', $solicitud['contacto1_nombre'])
                ->setCellValue('C14', $solicitud['contacto1_cargo'])
                ->setCellValue('E14', $solicitud['contacto1_email'])
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
                ->setCellValue('A22', $solicitud['resp1'])
                ->setCellValue('C22', $solicitud['resp2'])
                ->setCellValue('E22', $solicitud['resp3'])
                ->setCellValue('G22', $solicitud['resp4'])
                  /// preguntas
                  ->setCellValue('A25', $solicitud['op_preg1'])
                  ->setCellValue('B25', $solicitud['op_preg2'])
                  ->setCellValue('C25', $solicitud['op_preg3'])
                  ->setCellValue('D25', $alcance_opp)
                  ->setCellValue('E25', $solicitud['op_preg5'])
                  ->setCellValue('F25', $solicitud['op_preg6'])
                  ->setCellValue('G25', $solicitud['op_preg7'])
                  ->setCellValue('H25', $solicitud['op_preg8']);
        

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
                  ->setCellValue('A'.$subRespuestas, $solicitud['op_preg10'])
                  ->setCellValue('B'.$subRespuestas, $solicitud['op_preg12'])
                  ->setCellValue('C'.$subRespuestas, $solicitud['op_preg13'])
                  ->setCellValue('D'.$subRespuestas, $porcentajeVentas)
                  ->setCellValue('A'.$subRespuestas, $solicitud['op_preg14']);


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
    $objPHPExcel->getActiveSheet()->getStyle('A27')->applyFromArray($estiloTituloColumnas);
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

    $objPHPExcel->getActiveSheet()->getStyle('A28')->applyFromArray($estiloPreguntas);
    $objPHPExcel->getActiveSheet()->getStyle('C28')->applyFromArray($estiloPreguntas);
    $objPHPExcel->getActiveSheet()->getStyle('E28')->applyFromArray($estiloPreguntas);
    $objPHPExcel->getActiveSheet()->getStyle('G28')->applyFromArray($estiloPreguntas);

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
    $objPHPExcel->getActiveSheet()->setTitle('Demande de Certification');

    // Se activa la hoja para que sea la que se muestre cuando el archivo se abre
    $objPHPExcel->setActiveSheetIndex(0);
    // Inmovilizar paneles 
    //$objPHPExcel->getActiveSheet(0)->freezePane('A4');
    $objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(0,4);

    // Se manda el archivo al navegador web, con el nombre que se indica (Excel2007)
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="SOLICITUD_DE_CERTIFICACIÓN.xls"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    exit;
  


 ?>