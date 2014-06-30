<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UtilReportes
 *
 * @author lopez
 */
class UtilReportes {

    public static function getPrestamoEquipos($argumentos) {
        extract($argumentos);

        //construccion de la clausula where
        $where = '';
        if (count($estado) == 1) {
            $where = "where r.estado = $estado[0]";
            error_log("     where --> $where");
        }
        if (count($estado) == 2) {
            $where = "where r.estado = $estado[0] or r.estado = $estado[1]";
        }
        if (count($estado) == 3) {
            $where = "where r.estado = $estado[0] or r.estado = $estado[1] or r.estado = $estado[2]";
        }


        $estados = array('0' => 'solicitado', '1' => 'en uso', '2' => 'entregado', '3' => 'vencido');

//consulta

        $reporte = UtilConexion::$pdo->query("select r.id reserva, r.fk_equipo id_equipo, e.descripcion descripcion_equipo, r.fecha_inicio , r.fk_usuario id_usuario, u.nombre nombre_usuario, u.apellido apellido_usuario,r.estado estado_reserva, r.observaciones , r.fecha_fin, r.color,re.nombre responsable
                                                from (((reserva_equipo r JOIN usuario u ON r.fk_usuario = u.codigo)JOIN equipo e ON r.fk_equipo=e.id) JOIN usuario re ON r.fk_responsable=re.codigo)$where")->fetchAll(PDO::FETCH_ASSOC);

        //si la consulta retorna datos
        if (count($reporte) > 0) {

            //crea el archivo nuevo      
            $objPHPExcel = new PHPExcel();
            $objWorksheet = $objPHPExcel->setActiveSheetIndex();

            //nombre la hoja
            $objWorksheet->setTitle('Reporte');

            //orientacion pagina
            $objWorksheet->getPageSetup()->setHorizontalCentered(true);
            $objWorksheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
            $objWorksheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);


            //encabezado
            $objWorksheet->getHeaderFooter()->setOddHeader('&C&HUniversidad de Caldas');
            $objWorksheet->getHeaderFooter()->setOddFooter('&L&B' . 'Reporte Generado &D&T' . '&RPage &P of &N');

            $objWorksheet->setShowGridlines(false);
/////////// //formato de celdas

            $objWorksheet->GetColumnDimension('B')->SetWidth(12);
            $objWorksheet->GetColumnDimension('C')->SetWidth(20);
            $objWorksheet->GetColumnDimension('D')->SetWidth(20);
            $objWorksheet->GetColumnDimension('E')->SetWidth(10);
            $objWorksheet->GetColumnDimension('F')->SetWidth(10);
            $objWorksheet->GetColumnDimension('G')->SetWidth(20);
            $objWorksheet->GetColumnDimension('H')->SetWidth(20);
            $objWorksheet->GetColumnDimension('I')->SetWidth(10);
            $objWorksheet->GetColumnDimension('J')->SetWidth(20);
            $objWorksheet->GetColumnDimension('k')->SetWidth(10);
            $objWorksheet->GetColumnDimension('L')->SetWidth(20);
            $objWorksheet->GetColumnDimension('M')->SetWidth(20);
            $objWorksheet->GetRowDimension('3')->SetRowHeight(17);

            $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')
                    ->setSize(10);
//combinar celdas

            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B3:M3');



            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B3', "REPORTE PRESTAMO EQUIPOS");
            $objWorksheet->getStyle('B3')
                    ->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('033C68');
            $objWorksheet->getStyle('B3')
                    ->getFont()
                    ->setSize(14)
                    ->getColor()->setARGB('F0FFFF');
            $objWorksheet->getStyle('B3:M3')
                    ->getborders()
                    ->getallborders()
                    ->setborderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
            $objWorksheet->getStyle('B3:M3')
                    ->getAlignment()->sethorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER_CONTINUOUS);

//              
//encabezados de la tabla
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('B4', "CODIGO\nRESERVA")
                    ->setCellValue('C4', "FECHA\nINICIO")
                    ->setCellValue('D4', "FECHA\nFIN")
                    ->setCellValue('E4', "COLOR")
                    ->setCellValue('F4', "ID\nUSUARIO")
                    ->setCellValue('G4', "NOMBRE\nUSUARIO")
                    ->setCellValue('H4', "APELLIDO\nUSUARIO")
                    ->setCellValue('I4', "ID\nEQUIPO")
                    ->setCellValue('J4', "DESCRIPCION\nEQUIPO")
                    ->setCellValue('K4', "ESTADO")
                    ->setCellValue('L4', "OBSERVACIONES")
                    ->setCellValue('M4', "RESPONSABLE");

            $objWorksheet->getStyle('B4:M4')
                    ->getAlignment()->setWrapText(true);

            $objWorksheet->getStyle('B4:M4')
                    ->getAlignment()->setvertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);


//        
//       
            $styleArray = array(
                'font' => array(
                    'bold' => true,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                ),
                'borders' => array(
                    'inside' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                    'outline' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
//		'bottom'	=> array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
//                'right'		=> array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
//                'left'		=> array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                    'rotation' => 90,
                    'startcolor' => array(
                        'argb' => 'FFA0A0A0',
                    ),
                    'endcolor' => array(
                        'argb' => 'FFFFFFFF',
                    ),
                ),
            );



            $objWorksheet->getStyle('B4:M4')->applyFromArray($styleArray);



            $styleArray1 = array(
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => array(
                        'argb' => 'CAC8C8',
                    ),
                ),
                'borders' => array(
                    'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                    'left' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
                    'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
                ),
            );

//       
            $f = 5;
            //escribe en la hoja de calculo el resultado de la consulta
            foreach ($reporte as $fila) {
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('B' . $f, $fila['reserva'])
                        ->setCellValue('C' . $f, $fila['fecha_inicio'])
                        ->setCellValue('D' . $f, $fila['fecha_fin'])
                        ->setCellValue('E' . $f, $fila['color'])
                        ->setCellValue('F' . $f, $fila['id_usuario'])
                        ->setCellValue('G' . $f, $fila['nombre_usuario'])
                        ->setCellValue('H' . $f, $fila['apellido_usuario'])
                        ->setCellValue('I' . $f, $fila['id_equipo'])
                        ->setCellValue('J' . $f, $fila['descripcion_equipo'])
                        ->setCellValue('K' . $f, $estados[$fila['estado_reserva']])
                        ->setCellValue('L' . $f, $fila['observaciones'])
                        ->setCellValue('M' . $f, $fila['responsable']);
                $objWorksheet->getStyle('B' . $f . ':' . 'M' . $f)->applyFromArray($styleArray1);

                if ($f % 2 != 0) {
                    $objWorksheet->getStyle('B' . $f . ':' . 'M' . $f)->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()->setARGB('FFFFE0');
                }
                $f++;
            }
            $objWorksheet->getStyle('B3' . ':M' .
                            $objWorksheet->getHighestRow())->getborders()
                    ->getoutline()
                    ->setborderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);


            //guardar el archivo
            $callStartTime = microtime(true);
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $fecha = date('Y-m-d--h-i-s');
            $nombreArchivo = "ReportePrestamoEquipos $fecha.xlsx";
            $rutaArchivo = UtilConexion::$rutaDescargas . $nombreArchivo;
            $objWriter->save($rutaArchivo);
            $callEndTime = microtime(true);
            $callTime = $callEndTime - $callStartTime;
//        echo json_encode($nombreArchivo);
            echo json_encode(array("msj" => "bien", "var_ratu" => $nombreArchivo));
        }
        //si la consulta no retornó datos
        else {

            echo json_encode(array("msj" => "No hay datos"));
        }
    }

    public static function getPrestamoEquiposporFecha($argumentos) {
        extract($argumentos);
        $inicio = $fechainicial . ' 00:00:00';
        $fin = $fechafinal . ' 00:00:00';

        //construccion clausula where
        $where = "where fecha_inicio between '$inicio' and '$fin'";

        $estados = array('0' => 'solicitado', '1' => 'en uso', '2' => 'entregado', '3' => 'vencido');
        $reporte = UtilConexion::$pdo->query("select r.id reserva, r.fk_equipo id_equipo, e.descripcion descripcion_equipo, r.fecha_inicio , r.fk_usuario id_usuario, u.nombre nombre_usuario, u.apellido apellido_usuario,r.estado estado_reserva, r.observaciones , r.fecha_fin, r.color,re.nombre responsable
                                                from (((reserva_equipo r JOIN usuario u ON r.fk_usuario = u.codigo)JOIN equipo e ON r.fk_equipo=e.id) JOIN usuario re ON r.fk_responsable=re.codigo)$where")->fetchAll(PDO::FETCH_ASSOC);

        if (count($reporte) > 0) {

//creacion y formato libro de exel
//crea el archivo nuevo      
            $objPHPExcel = new PHPExcel();
            $objWorksheet = $objPHPExcel->setActiveSheetIndex();
            //nombre la hoja
            $objWorksheet->setTitle('Reporte');

            //orientacion pagina
            $objWorksheet->getPageSetup()->setHorizontalCentered(true);
            $objWorksheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
            $objWorksheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);


            //encabezado
            $objWorksheet->getHeaderFooter()->setOddHeader('&C&HUniversidad de Caldas');
            $objWorksheet->getHeaderFooter()->setOddFooter('&L&B' . 'Reporte Generado &D&T' . '&RPage &P of &N');
            $objWorksheet->setShowGridlines(false);
/////////// //formato de celdas

            $objWorksheet->GetColumnDimension('B')->SetWidth(12);
            $objWorksheet->GetColumnDimension('C')->SetWidth(20);
            $objWorksheet->GetColumnDimension('D')->SetWidth(20);
            $objWorksheet->GetColumnDimension('E')->SetWidth(10);
            $objWorksheet->GetColumnDimension('F')->SetWidth(10);
            $objWorksheet->GetColumnDimension('G')->SetWidth(20);
            $objWorksheet->GetColumnDimension('H')->SetWidth(20);
            $objWorksheet->GetColumnDimension('I')->SetWidth(10);
            $objWorksheet->GetColumnDimension('J')->SetWidth(20);
            $objWorksheet->GetColumnDimension('k')->SetWidth(10);
            $objWorksheet->GetColumnDimension('L')->SetWidth(20);
            $objWorksheet->GetColumnDimension('M')->SetWidth(20);
            $objWorksheet->GetRowDimension('3')->SetRowHeight(17);

            $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')
                    ->setSize(10);
//combinar celdas
//combinar celdas

            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B3:M3');



            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B3', "REPORTE PRESTAMO EQUIPOS");
            $objWorksheet->getStyle('B3')
                    ->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('033C68');
            $objWorksheet->getStyle('B3')
                    ->getFont()
                    ->setSize(14)
                    ->getColor()->setARGB('F0FFFF');
            $objWorksheet->getStyle('B3:M3')
                    ->getborders()
                    ->getallborders()
                    ->setborderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
            $objWorksheet->getStyle('B3:M3')
                    ->getAlignment()->sethorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER_CONTINUOUS);

//              
//encabezados de la tabla
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('B4', "CODIGO\nRESERVA")
                    ->setCellValue('C4', "FECHA\nINICIO")
                    ->setCellValue('D4', "FECHA\nFIN")
                    ->setCellValue('E4', "COLOR")
                    ->setCellValue('F4', "ID\nUSUARIO")
                    ->setCellValue('G4', "NOMBRE\nUSUARIO")
                    ->setCellValue('H4', "APELLIDO\nUSUARIO")
                    ->setCellValue('I4', "ID\nEQUIPO")
                    ->setCellValue('J4', "DESCRIPCION\nEQUIPO")
                    ->setCellValue('K4', "ESTADO")
                    ->setCellValue('L4', "OBSERVACIONES")
                    ->setCellValue('M4', "RESPONSABLE");

            $objWorksheet->getStyle('B4:M4')
                    ->getAlignment()->setWrapText(true);

            $objWorksheet->getStyle('B4:M4')
                    ->getAlignment()->setvertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);


//        
//       
            $styleArray = array(
                'font' => array(
                    'bold' => true,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                ),
                'borders' => array(
                    'inside' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                    'outline' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                    'rotation' => 90,
                    'startcolor' => array(
                        'argb' => 'FFA0A0A0',
                    ),
                    'endcolor' => array(
                        'argb' => 'FFFFFFFF',
                    ),
                ),
            );



            $objWorksheet->getStyle('B4:M4')->applyFromArray($styleArray);



            $styleArray1 = array(
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => array(
                        'argb' => 'CAC8C8',
                    ),
                ),
                'borders' => array(
                    'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                    'left' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
                    'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
                ),
            );





            $f = 5;


            //escribe en la hoja de calculo el resultado de la consulta
            foreach ($reporte as $fila) {
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('B' . $f, $fila['reserva'])
                        ->setCellValue('C' . $f, $fila['fecha_inicio'])
                        ->setCellValue('D' . $f, $fila['fecha_fin'])
                        ->setCellValue('E' . $f, $fila['color'])
                        ->setCellValue('F' . $f, $fila['id_usuario'])
                        ->setCellValue('G' . $f, $fila['nombre_usuario'])
                        ->setCellValue('H' . $f, $fila['apellido_usuario'])
                        ->setCellValue('I' . $f, $fila['id_equipo'])
                        ->setCellValue('J' . $f, $fila['descripcion_equipo'])
                        ->setCellValue('K' . $f, $estados[$fila['estado_reserva']])
                        ->setCellValue('L' . $f, $fila['observaciones'])
                        ->setCellValue('M' . $f, $fila['responsable']);


                $objWorksheet->getStyle('B' . $f . ':' . 'M' . $f)->applyFromArray($styleArray1);

                if ($f % 2 != 0) {
                    $objWorksheet->getStyle('B' . $f . ':' . 'M' . $f)->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()->setARGB('FFFFE0');
                }
                $f++;
            }
            $objWorksheet->getStyle('B3' . ':M' .
                            $objWorksheet->getHighestRow())->getborders()
                    ->getoutline()
                    ->setborderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);


            //guardar el archivo
            $callStartTime = microtime(true);
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $fecha = date('Y-m-d--h-i-s');
            $nombreArchivo = "ReportePrestamoEquiposporfecha $fecha.xlsx";
            $rutaArchivo = UtilConexion::$rutaDescargas . $nombreArchivo;
            $objWriter->save($rutaArchivo);
            $callEndTime = microtime(true);
            $callTime = $callEndTime - $callStartTime;
//        echo json_encode($nombreArchivo);
            echo json_encode(array("msj" => "bien", "var_ratu" => $nombreArchivo));
        } else {

            echo json_encode(array("msj" => "No hay datos"));
        }
    }

    public static function getPrestamoEquiposporEquipo($argumentos) {
        extract($argumentos);

        //construccion clausula where
        $where = "where e.descripcion='$nombre'";

        $estados = array('0' => 'solicitado', '1' => 'en uso', '2' => 'entregado', '3' => 'vencido');
        //consulta
        $reporte = UtilConexion::$pdo->query("select r.id reserva, r.fk_equipo id_equipo, e.descripcion descripcion_equipo, r.fecha_inicio , r.fk_usuario id_usuario, u.nombre nombre_usuario, u.apellido apellido_usuario,r.estado estado_reserva, r.observaciones , r.fecha_fin, r.color,re.nombre responsable
                                                from (((reserva_equipo r JOIN usuario u ON r.fk_usuario = u.codigo)JOIN equipo e ON r.fk_equipo=e.id) JOIN usuario re ON r.fk_responsable=re.codigo)$where")->fetchAll(PDO::FETCH_ASSOC);
        if (count($reporte) > 0) {
//crea el archivo nuevo      
            $objPHPExcel = new PHPExcel();
            $objWorksheet = $objPHPExcel->setActiveSheetIndex();
            //nombre la hoja
            $objWorksheet->setTitle('Reporte');

            //orientacion pagina
            $objWorksheet->getPageSetup()->setHorizontalCentered(true);
            $objWorksheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
            $objWorksheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);


            //encabezado
            $objWorksheet->getHeaderFooter()->setOddHeader('&C&HUniversidad de Caldas');
            $objWorksheet->getHeaderFooter()->setOddFooter('&L&B' . 'Reporte Generado &D&T' . '&RPage &P of &N');

            $objWorksheet->setShowGridlines(false);
/////////// //formato de celdas
            $objWorksheet->GetColumnDimension('B')->SetWidth(12);
            $objWorksheet->GetColumnDimension('C')->SetWidth(20);
            $objWorksheet->GetColumnDimension('D')->SetWidth(20);
            $objWorksheet->GetColumnDimension('E')->SetWidth(10);
            $objWorksheet->GetColumnDimension('F')->SetWidth(10);
            $objWorksheet->GetColumnDimension('G')->SetWidth(20);
            $objWorksheet->GetColumnDimension('H')->SetWidth(20);
            $objWorksheet->GetColumnDimension('I')->SetWidth(10);
            $objWorksheet->GetColumnDimension('J')->SetWidth(20);
            $objWorksheet->GetColumnDimension('k')->SetWidth(10);
            $objWorksheet->GetColumnDimension('L')->SetWidth(20);
            $objWorksheet->GetColumnDimension('M')->SetWidth(20);
            $objWorksheet->GetRowDimension('3')->SetRowHeight(17);

            $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')
                    ->setSize(10);
//combinar celdas
            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B3:L3');



            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B3', "REPORTE PRESTAMO EQUIPOS");
            $objWorksheet->getStyle('B3')
                    ->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('033C68');
            $objWorksheet->getStyle('B3')
                    ->getFont()
                    ->setSize(14)
                    ->getColor()->setARGB('F0FFFF');
            $objWorksheet->getStyle('B3:L3')
                    ->getborders()
                    ->getallborders()
                    ->setborderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
            $objWorksheet->getStyle('B3:M3')
                    ->getAlignment()->sethorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER_CONTINUOUS);

//              
//encabezados de la tabla
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('B4', "CODIGO\nRESERVA")
                    ->setCellValue('C4', "FECHA\nINICIO")
                    ->setCellValue('D4', "FECHA\nFIN")
                    ->setCellValue('E4', "COLOR")
                    ->setCellValue('F4', "ID\nUSUARIO")
                    ->setCellValue('G4', "NOMBRE\nUSUARIO")
                    ->setCellValue('H4', "APELLIDO\nUSUARIO")
                    ->setCellValue('I4', "ID\nEQUIPO")
                    ->setCellValue('J4', "DESCRIPCION\nEQUIPO")
                    ->setCellValue('K4', "ESTADO")
                    ->setCellValue('L4', "OBSERVACIONES")
                    ->setCellValue('M4', "RESPONSABLE");

            $objWorksheet->getStyle('B4:M4')
                    ->getAlignment()->setWrapText(true);

            $objWorksheet->getStyle('B4:M4')
                    ->getAlignment()->setvertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);


//        
//       
            $styleArray = array(
                'font' => array(
                    'bold' => true,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                ),
                'borders' => array(
                    'inside' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                    'outline' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                    'rotation' => 90,
                    'startcolor' => array(
                        'argb' => 'FFA0A0A0',
                    ),
                    'endcolor' => array(
                        'argb' => 'FFFFFFFF',
                    ),
                ),
            );



            $objWorksheet->getStyle('B4:M4')->applyFromArray($styleArray);



            $styleArray1 = array(
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => array(
                        'argb' => 'CAC8C8',
                    ),
                ),
                'borders' => array(
                    'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                    'left' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
                    'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
                ),
            );

            $f = 5;
            //escribe en la hoja de calculo el resultado de la consulta
            foreach ($reporte as $fila) {
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('B' . $f, $fila['reserva'])
                        ->setCellValue('C' . $f, $fila['fecha_inicio'])
                        ->setCellValue('D' . $f, $fila['fecha_fin'])
                        ->setCellValue('E' . $f, $fila['color'])
                        ->setCellValue('F' . $f, $fila['id_usuario'])
                        ->setCellValue('G' . $f, $fila['nombre_usuario'])
                        ->setCellValue('H' . $f, $fila['apellido_usuario'])
                        ->setCellValue('I' . $f, $fila['id_equipo'])
                        ->setCellValue('J' . $f, $fila['descripcion_equipo'])
                        ->setCellValue('K' . $f, $estados[$fila['estado_reserva']])
                        ->setCellValue('L' . $f, $fila['observaciones'])
                        ->setCellValue('M' . $f, $fila['responsable']);
                $objWorksheet->getStyle('B' . $f . ':' . 'M' . $f)->applyFromArray($styleArray1);

                if ($f % 2 != 0) {
                    $objWorksheet->getStyle('B' . $f . ':' . 'M' . $f)->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()->setARGB('FFFFE0');
                }
                $f++;
            }
            $objWorksheet->getStyle('B3' . ':M' .
                            $objWorksheet->getHighestRow())->getborders()
                    ->getoutline()
                    ->setborderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);




            //guardar el archivo
            $callStartTime = microtime(true);
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $fecha = date('Y-m-d--h-i-s');
            $nombreArchivo = "ReportePrestamoEquipospornombre $fecha.xlsx";
            $rutaArchivo = UtilConexion::$rutaDescargas . $nombreArchivo;
            $objWriter->save($rutaArchivo);
            $callEndTime = microtime(true);
            $callTime = $callEndTime - $callStartTime;
//        echo json_encode($nombreArchivo);
            echo json_encode(array("msj" => "bien", "var_ratu" => $nombreArchivo));
        } else {

            echo json_encode(array("msj" => "No hay datos"));
        }
    }

    public static function getListaEquipos() {
//         $filas[] = ['id' => 0, 'valor' => 'Seleccione un equipo'];
        $filas = UtilConexion::$pdo->query("SELECT id,descripcion FROM equipo ORDER BY descripcion")->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($filas);
    }

    public static function getPrestamoSalas($argumentos) {
        extract($argumentos);

        $where = '';
        if (count($estado) == 1) {
            $where = "where r.estado = $estado[0]";
        }
        if (count($estado) == 2) {
            $where = "where r.estado = $estado[0] or r.estado = $estado[1]";
        }
        if (count($estado) == 3) {
            $where = "where r.estado = $estado[0] or r.estado = $estado[1] or r.estado = $estado[2]";
        }
        if (count($estado) == 4) {
            $where = "where r.estado = $estado[0] or r.estado = $estado[1] or r.estado = $estado[2] or r.estado = $estado[3]";
        }

        $estados = array('0' => 'solicitado', '1' => 'en uso', '2' => 'entregado', '3' => 'monitoria', '4' => 'inhabilitada');

        //Esta consulta se hace para poder acceder a la tabla reserva_sala de la base de datos 
        $reporte = UtilConexion::$pdo->query("SELECT * FROM reserva_sala r $where")->fetchAll(PDO::FETCH_ASSOC);
        if (count($reporte) > 0) {

            $objPHPExcel = new PHPExcel();
            $objWorksheet = $objPHPExcel->setActiveSheetIndex();
            //nombre la hoja
            $objWorksheet->setTitle('Reporte');
            //orientacion pagina
            $objWorksheet->getPageSetup()->setHorizontalCentered(true);
            $objWorksheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
            $objWorksheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
            //encabezado

            $objWorksheet->setShowGridlines(false);

            $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')
                    ->setSize(10);
            //combinar celdas

            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('D1:H1');
            $objWorksheet->getStyle('D1')
                    ->getAlignment()->sethorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER_CONTINUOUS);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D1', "REPORTE PRESTAMO SALAS");
            $objWorksheet->getStyle('D1')
                    ->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('cccccccc');
            $objWorksheet->getStyle('D1')
                    ->getFont()
                    ->setSize(14)
                    ->getColor()->setARGB('000000');

            $objWorksheet->GetColumnDimension('B')->SetWidth(17);
            $objWorksheet->GetColumnDimension('C')->SetWidth(17);
            $objWorksheet->GetColumnDimension('D')->SetWidth(17);
            $objWorksheet->GetColumnDimension('E')->SetWidth(17);
            $objWorksheet->GetColumnDimension('F')->SetWidth(17);
            $objWorksheet->GetColumnDimension('G')->SetWidth(17);
            $objWorksheet->GetColumnDimension('H')->SetWidth(17);
            $objWorksheet->GetColumnDimension('I')->SetWidth(17);
            $objWorksheet->GetColumnDimension('J')->SetWidth(17);
            $objWorksheet->GetColumnDimension('k')->SetWidth(17);
            $objWorksheet->GetRowDimension('3')->SetRowHeight(13);

            $objWorksheet->getStyle('B3:k3')
                    ->getborders()
                    ->getallborders()
                    ->setborderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);


            $objWorksheet->getStyle('A3:k3')
                    ->getAlignment()->sethorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER_CONTINUOUS);
            $objPHPExcel->setActiveSheetIndex(0) //Esta parte me permite ponerle encabezados a la celda que se requiere
                    ->setCellValue('D1', "REPORTE RESERVA SALAS")
                    ->setCellValue('B3', "COD. RESERVA")
                    ->setCellValue('C3', "SALA")
                    ->setCellValue('D3', 'FECHA INICIO')
                    ->setCellValue('E3', 'ENCARGADO')
                    ->setCellValue('F3', 'ESTADO')
                    ->setCellValue('G3', 'OBSERVACIONES')
                    ->setCellValue('H3', 'FECHA FIN')
                    ->setCellValue('I3', 'ACTIVIDAD')
                    ->setCellValue('J3', 'RESPONSABLE')
                    ->setCellValue('K3', 'COLOR');

            $objWorksheet->getStyle('B3:k3')
                    ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objWorksheet->getStyle('B3:k3')
                    ->getAlignment()->setWrapText(true);

            $styleArray = array(
                'font' => array(
                    'bold' => true,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                ),
                'borders' => array(
                    'bottom' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
                    'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
                    'left' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                    'rotation' => 90,
                    'startcolor' => array(
                        'argb' => 'FFA0A0A0',
                    ),
                    'endcolor' => array(
                        'argb' => 'FFFFFFFF',
                    ),
                ),
            );

            $objWorksheet
                    ->getStyle('B3:k3')->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('cccccccc');
            $objWorksheet
                    ->getStyle('D1')
                    ->getfont()
                    ->setSize(14);


            $f = 4;
            //escribe en la hoja de calculo el resultado de la consulta
            foreach ($reporte as $fila) {
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('B' . $f, $fila['id'])
                        ->setCellValue('C' . $f, $fila['fk_sala'])
                        ->setCellValue('D' . $f, $fila['fecha_inicio'])
                        ->setCellValue('E' . $f, $fila['fk_usuario'])
                        ->setCellValue('F' . $f, $estados[$fila['estado']])
                        ->setCellValue('G' . $f, $fila['observaciones'])
                        ->setCellValue('H' . $f, $fila['fecha_fin'])
                        ->setCellValue('I' . $f, $fila['actividad'])
                        ->setCellValue('J' . $f, $fila['fk_responsable'])
                        ->setCellValue('K' . $f, $fila['color']);

                $objWorksheet->getStyle('B' . $f . ':k' . $f)
                        ->getborders()
                        ->getallborders()
                        ->setborderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);

                $f++;
            }


            $objWorksheet->getStyle('B3:k3')
                    ->getAlignment()->setWrapText(true);

            $objWorksheet->getStyle('B3:k3')
                    ->getAlignment()->sethorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $styleArray = array(
                'font' => array(
                    'bold' => true,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                ),
                'borders' => array(
                    'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                    'bottom' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
                    'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
                    'left' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => array(
                        'argb' => 'CAC8C8',
                    ),
                ),
            );

            $objWorksheet->setTitle('Simple');

            $objPHPExcel->setActiveSheetIndex(0);
            $callStartTime = microtime(true);
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $fecha = date('Y-m-d--h-i-s');
            $nombreArchivo = "ReporteReservaSalas $fecha.xlsx";
            $rutaArchivo = UtilConexion::$rutaDescargas . $nombreArchivo;
            $objWriter->save($rutaArchivo);
            $callEndTime = microtime(true);
            $callTime = $callEndTime - $callStartTime;
            echo json_encode(array("msj" => "bien", "var_ratu" => $nombreArchivo));
        } else {

            echo json_encode(array("msj" => "No hay datos"));
        }
    }

    public static function getPrestamoSalasporFecha($argumentos) {
        extract($argumentos);
        $inicio = $fechainicial . ' 00:00:00';
        $fin = $fechafinal . ' 00:00:00';

        $where = "where r.fecha_inicio between '$inicio' and '$fin'";

        $estados = array('0' => 'solicitado', '1' => 'en uso', '2' => 'entregado', '3' => 'monitoria', '4' => 'inhabilitada');

        //Esta consulta se hace para poder acceder a la tabla reserva_sala de la base de datos 
        $reporte = UtilConexion::$pdo->query("SELECT * FROM reserva_sala r $where")->fetchAll(PDO::FETCH_ASSOC);
        if (count($reporte) > 0) {

            $objPHPExcel = new PHPExcel();
            $objWorksheet = $objPHPExcel->setActiveSheetIndex();
            //nombre la hoja
            $objWorksheet->setTitle('Reporte');
            //orientacion pagina
            $objWorksheet->getPageSetup()->setHorizontalCentered(true);
            $objWorksheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
            $objWorksheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
            //encabezado

            $objWorksheet->setShowGridlines(false);

            $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')
                    ->setSize(10);
            //combinar celdas

            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('D1:H1');
            $objWorksheet->getStyle('D1')
                    ->getAlignment()->sethorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER_CONTINUOUS);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D1', "REPORTE PRESTAMO SALAS");
            $objWorksheet->getStyle('D1')
                    ->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('cccccccc');
            $objWorksheet->getStyle('D1')
                    ->getFont()
                    ->setSize(14)
                    ->getColor()->setARGB('000000');

            $objWorksheet->GetColumnDimension('B')->SetWidth(17);
            $objWorksheet->GetColumnDimension('C')->SetWidth(17);
            $objWorksheet->GetColumnDimension('D')->SetWidth(17);
            $objWorksheet->GetColumnDimension('E')->SetWidth(17);
            $objWorksheet->GetColumnDimension('F')->SetWidth(17);
            $objWorksheet->GetColumnDimension('G')->SetWidth(17);
            $objWorksheet->GetColumnDimension('H')->SetWidth(17);
            $objWorksheet->GetColumnDimension('I')->SetWidth(17);
            $objWorksheet->GetColumnDimension('J')->SetWidth(17);
            $objWorksheet->GetColumnDimension('K')->SetWidth(17);
            $objWorksheet->GetRowDimension('3')->SetRowHeight(13);

            $objWorksheet->getStyle('B3:K3')
                    ->getborders()
                    ->getallborders()
                    ->setborderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);


            $objWorksheet->getStyle('A3:K3')
                    ->getAlignment()->sethorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER_CONTINUOUS);
            $objPHPExcel->setActiveSheetIndex(0) //Esta parte me permite ponerle encabezados a la celda que se requiere
                    ->setCellValue('D1', "REPORTE RESERVA SALAS")
                    ->setCellValue('B3', "COD. RESERVA")
                    ->setCellValue('C3', "SALA")
                    ->setCellValue('D3', 'FECHA INICIO')
                    ->setCellValue('E3', 'ENCARGADO')
                    ->setCellValue('F3', 'ESTADO')
                    ->setCellValue('G3', 'OBSERVACIONES')
                    ->setCellValue('H3', 'FECHA FIN')
                    ->setCellValue('I3', 'ACTIVIDAD')
                    ->setCellValue('J3', 'RESPONSABLE')
                    ->setCellValue('K3', 'COLOR');


            $objWorksheet->getStyle('B3:K3')
                    ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objWorksheet->getStyle('B3:K3')
                    ->getAlignment()->setWrapText(true);

            $styleArray = array(
                'font' => array(
                    'bold' => true,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                ),
                'borders' => array(
                    'bottom' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
                    'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
                    'left' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                    'rotation' => 90,
                    'startcolor' => array(
                        'argb' => 'FFA0A0A0',
                    ),
                    'endcolor' => array(
                        'argb' => 'FFFFFFFF',
                    ),
                ),
            );

            $objWorksheet
                    ->getStyle('B3:J3')->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('cccccccc');
            $objWorksheet
                    ->getStyle('D1')
                    ->getfont()
                    ->setSize(14);


            $f = 4;
            //escribe en la hoja de calculo el resultado de la consulta
            foreach ($reporte as $fila) {
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('B' . $f, $fila['id'])
                        ->setCellValue('C' . $f, $fila['fk_sala'])
                        ->setCellValue('D' . $f, $fila['fecha_inicio'])
                        ->setCellValue('E' . $f, $fila['fk_usuario'])
                        ->setCellValue('F' . $f, $estados[$fila['estado']])
                        ->setCellValue('G' . $f, $fila['observaciones'])
                        ->setCellValue('H' . $f, $fila['fecha_fin'])
                        ->setCellValue('I' . $f, $fila['actividad'])
                        ->setCellValue('J' . $f, $fila['fk_responsable'])
                        ->setCellValue('K' . $f, $fila['color']);

                $objWorksheet->getStyle('B' . $f . ':K' . $f)
                        ->getborders()
                        ->getallborders()
                        ->setborderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);

                $f++;
            }

            $objWorksheet->getStyle('B3:K3')
                    ->getAlignment()->setWrapText(true);

            $objWorksheet->getStyle('B3:K3')
                    ->getAlignment()->sethorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $styleArray = array(
                'font' => array(
                    'bold' => true,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                ),
                'borders' => array(
                    'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                    'bottom' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
                    'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
                    'left' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => array(
                        'argb' => 'CAC8C8',
                    ),
                ),
            );

            $objPHPExcel->setActiveSheetIndex(0);
            //guardar el archivo
            $callStartTime = microtime(true);
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $fecha = date('Y-m-d--h-i-s');
            $nombreArchivo = "ReservaSalasporfecha $fecha.xlsx";
            $rutaArchivo = UtilConexion::$rutaDescargas . $nombreArchivo;
            $objWriter->save($rutaArchivo);
            $callEndTime = microtime(true);
            $callTime = $callEndTime - $callStartTime;
//        echo json_encode($nombreArchivo);
            echo json_encode(array("msj" => "bien", "var_ratu" => $nombreArchivo));
        } else {

            echo json_encode(array("msj" => "No hay datos"));
        }
    }

    public static function getPrestamoSalasporSala($argumentos) {
        extract($argumentos);
        //construccion clausula where

        $where = "where r.fk_sala='$nombres'";

        $estados = array('0' => 'solicitado', '1' => 'en uso', '2' => 'entregado', '3' => 'monitoria', '4' => 'inhabilitada');

        //Esta consulta se hace para poder acceder a la tabla reserva_sala de la base de datos 
        $reporte = UtilConexion::$pdo->query("SELECT * FROM reserva_sala r $where")->fetchAll(PDO::FETCH_ASSOC);
        error_log(count($reporte));
        if (count($reporte) > 0) {

            $objPHPExcel = new PHPExcel();
            $objWorksheet = $objPHPExcel->setActiveSheetIndex();
            //nombre la hoja
            $objWorksheet->setTitle('Reporte');
            //orientacion pagina
            $objWorksheet->getPageSetup()->setHorizontalCentered(true);
            $objWorksheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
            $objWorksheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
            //encabezado

            $objWorksheet->setShowGridlines(false);

            $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')
                    ->setSize(10);
            //combinar celdas

            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('D1:H1');
            $objWorksheet->getStyle('D1')
                    ->getAlignment()->sethorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER_CONTINUOUS);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D1', "REPORTE PRESTAMO SALAS");
            $objWorksheet->getStyle('D1')
                    ->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('cccccccc');
            $objWorksheet->getStyle('D1')
                    ->getFont()
                    ->setSize(14)
                    ->getColor()->setARGB('000000');

            $objWorksheet->GetColumnDimension('B')->SetWidth(17);
            $objWorksheet->GetColumnDimension('C')->SetWidth(17);
            $objWorksheet->GetColumnDimension('D')->SetWidth(17);
            $objWorksheet->GetColumnDimension('E')->SetWidth(17);
            $objWorksheet->GetColumnDimension('F')->SetWidth(17);
            $objWorksheet->GetColumnDimension('G')->SetWidth(17);
            $objWorksheet->GetColumnDimension('H')->SetWidth(17);
            $objWorksheet->GetColumnDimension('I')->SetWidth(17);
            $objWorksheet->GetColumnDimension('J')->SetWidth(17);
            $objWorksheet->GetColumnDimension('k')->SetWidth(17);
            $objWorksheet->GetRowDimension('3')->SetRowHeight(13);

            $objWorksheet->getStyle('B3:k3')
                    ->getborders()
                    ->getallborders()
                    ->setborderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);


            $objWorksheet->getStyle('A3:k3')
                    ->getAlignment()->sethorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER_CONTINUOUS);
            $objPHPExcel->setActiveSheetIndex(0) //Esta parte me permite ponerle encabezados a la celda que se requiere
                    ->setCellValue('D1', "REPORTE RESERVA SALAS")
                    ->setCellValue('B3', "COD. RESERVA")
                    ->setCellValue('C3', "SALA")
                    ->setCellValue('D3', 'FECHA INICIO')
                    ->setCellValue('E3', 'ENCARGADO')
                    ->setCellValue('F3', 'ESTADO')
                    ->setCellValue('G3', 'OBSERVACIONES')
                    ->setCellValue('H3', 'FECHA FIN')
                    ->setCellValue('I3', 'ACTIVIDAD')
                    ->setCellValue('J3', 'RESPONSABLE')
                    ->setCellValue('K3', 'COLOR');

            $objWorksheet->getStyle('B3:k3')
                    ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objWorksheet->getStyle('B3:k3')
                    ->getAlignment()->setWrapText(true);

            $styleArray = array(
                'font' => array(
                    'bold' => true,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                ),
                'borders' => array(
                    'bottom' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
                    'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
                    'left' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                    'rotation' => 90,
                    'startcolor' => array(
                        'argb' => 'FFA0A0A0',
                    ),
                    'endcolor' => array(
                        'argb' => 'FFFFFFFF',
                    ),
                ),
            );

            $objWorksheet
                    ->getStyle('B3:k3')->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('cccccccc');
            $objWorksheet
                    ->getStyle('D1')
                    ->getfont()
                    ->setSize(14);


            $f = 4;
            //escribe en la hoja de calculo el resultado de la consulta
            foreach ($reporte as $fila) {
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('B' . $f, $fila['id'])
                        ->setCellValue('C' . $f, $fila['fk_sala'])
                        ->setCellValue('D' . $f, $fila['fecha_inicio'])
                        ->setCellValue('E' . $f, $fila['fk_usuario'])
                        ->setCellValue('F' . $f, $estados[$fila['estado']])
                        ->setCellValue('G' . $f, $fila['observaciones'])
                        ->setCellValue('H' . $f, $fila['fecha_fin'])
                        ->setCellValue('I' . $f, $fila['actividad'])
                        ->setCellValue('J' . $f, $fila['fk_responsable'])
                        ->setCellValue('K' . $f, $fila['color']);

                $objWorksheet->getStyle('B' . $f . ':k' . $f)
                        ->getborders()
                        ->getallborders()
                        ->setborderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);

                $f++;
            }


            $objWorksheet->getStyle('B3:k3')
                    ->getAlignment()->setWrapText(true);

            $objWorksheet->getStyle('B3:k3')
                    ->getAlignment()->sethorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $styleArray = array(
                'font' => array(
                    'bold' => true,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                ),
                'borders' => array(
                    'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                    'bottom' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
                    'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
                    'left' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => array(
                        'argb' => 'CAC8C8',
                    ),
                ),
            );

            $objWorksheet->setTitle('Simple');

            $objPHPExcel->setActiveSheetIndex(0);
            $callStartTime = microtime(true);
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $fecha = date('Y-m-d--h-i-s');
            $nombreArchivo = "ReporteReservaSalasPorSala $fecha.xlsx";
            $rutaArchivo = UtilConexion::$rutaDescargas . $nombreArchivo;
            $objWriter->save($rutaArchivo);
            $callEndTime = microtime(true);
            $callTime = $callEndTime - $callStartTime;
            echo json_encode(array("msj" => "bien", "var_ratu" => $nombreArchivo));
        } else {

            echo json_encode(array("msj" => "No hay datos"));
        }
    }

    public static function getListaSalas() {
//         $filas[] = ['id' => 0, 'valor' => 'Seleccion una sala'];
        $filas = UtilConexion::$pdo->query("SELECT capacidad,nombre FROM sala ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($filas);
    }

}
