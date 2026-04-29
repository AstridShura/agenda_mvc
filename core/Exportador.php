<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

/**
 * CLASE Exportador (OPTIMIZADA PARA ALTO RENDIMIENTO)
 * 
 * Exporta datos a Excel (.xlsx), PDF, Word (.docx) e Imagen (.jpg) con estilos profesionales.
 * - Estilos aplicados por bloques en Excel (Evita Memory Limit).
 * - Lógica de formateo centralizada (DRY).
 * - Detección estricta de fechas y booleanos.
 * ============================================================
 */
class Exportador
{
    // ─────────────────────────────────────────────────────────
    /**
     * EXCEL — Exporta datos a formato .xlsx (Optimizado)
     */
    public static function excel(
        array  $datos,
        array  $columnas,
        string $titulo,
        string $archivo
    ): void {

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle(substr($titulo, 0, 31));

        $totalCols = count($columnas);
        $letraFin  = Coordinate::stringFromColumnIndex($totalCols);

        // ── FILA 1: Título ───────────────────────────────────
        $sheet->mergeCells('A1:' . $letraFin . '1');
        $sheet->setCellValue('A1', $titulo);
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'size'  => 14,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1a1a2e'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(32);

        // ── FILA 2: Fecha y total ────────────────────────────
        $sheet->mergeCells('A2:' . $letraFin . '2');
        $sheet->setCellValue('A2',
            'Generado: ' . date('d/m/Y H:i:s') .
            '   |   Total: ' . count($datos) . ' registros'
        );
        $sheet->getStyle('A2')->applyFromArray([
            'font' => [
                'italic' => true,
                'size'   => 9,
                'color'  => ['rgb' => '666666'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(16);

        // ── FILA 3: Encabezados ──────────────────────────────
        $colIdx = 1;
        foreach ($columnas as $campo => $encabezado) {
            $letra     = Coordinate::stringFromColumnIndex($colIdx);
            $coordenada = $letra . '3';

            $sheet->setCellValue($coordenada, $encabezado);
            $sheet->getStyle($coordenada)->applyFromArray([
                'font' => [
                    'bold'  => true,
                    'size'  => 10,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '0f3460'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => 'AAAAAA'],
                    ],
                ],
            ]);
            $colIdx++;
        }
        $sheet->getRowDimension(3)->setRowHeight(22);

        // ── FILAS 4+: INSERCIÓN PURA DE DATOS (Sin estilos) ─
        $fila = 4;
        foreach ($datos as $registro) {
            $colIdx = 1;
            foreach ($columnas as $campo => $encabezado) {
                $valor = self::formatearDato($campo, $registro[$campo] ?? '');
                $letra = Coordinate::stringFromColumnIndex($colIdx);
                
                // Solo insertamos el valor, sin aplicar estilos
                $sheet->setCellValue($letra . $fila, $valor);
                $colIdx++;
            }
            $fila++;
        }

        // ── APLICACIÓN DE ESTILOS EN BLOQUE (La Magia) ──────
        if ($fila > 4) {
            $rangoDatos = 'A4:' . $letraFin . ($fila - 1);

            // 1. Bordes para TODA la tabla de datos de una vez
            $sheet->getStyle($rangoDatos)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => 'DDDDDD'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            // 2. Color de fondo alternado (Solo recorre filas pares)
            for ($i = 4; $i < $fila; $i += 2) {
                $sheet->getStyle("A{$i}:{$letraFin}{$i}")
                      ->getFill()->setFillType(Fill::FILL_SOLID)
                      ->getStartColor()->setRGB('f0f4f8');
            }
        }

        // ── ÚLTIMA FILA: Total ───────────────────────────────
        $sheet->mergeCells('A' . $fila . ':' . $letraFin . $fila);
        $sheet->setCellValue(
            'A' . $fila,
            'Total exportado: ' . count($datos) . ' registros'
        );
        $sheet->getStyle('A' . $fila)->applyFromArray([
            'font' => [
                'bold'   => true,
                'italic' => true,
                'size'   => 9,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'e8ecf0'],
            ],
        ]);

        // ── Autoajuste de columnas INTELIGENTE ───────────────
        if (count($datos) < 500) {
            // Datasets pequeños: Calculamos el ancho óptimo
            foreach (range(1, $totalCols) as $idx) {
                $sheet->getColumnDimensionByColumn($idx)->setAutoSize(true);
            }
        } else {
            // Datasets grandes: Ancho fijo para no colgar el servidor
            foreach (range(1, $totalCols) as $idx) {
                $sheet->getColumnDimensionByColumn($idx)->setWidth(22);
            }
        }

        // ── Fijar encabezados al hacer scroll ────────────────
        $sheet->freezePane('A4');

        // ── Enviar al browser ────────────────────────────────
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $archivo . '.xlsx"');
        header('Cache-Control: max-age=0');
        header('Pragma: public');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        
        // Limpiar memoria
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        exit();
    }

    // ─────────────────────────────────────────────────────────
    /**
     * PDF — Exporta datos a formato .pdf
     */
    public static function pdf(
        array  $datos,
        array  $columnas,
        string $titulo,
        string $archivo
    ): void {

        $pdf = new TCPDF('L', 'mm', 'LETTER', true, 'UTF-8', false);

        $pdf->SetCreator('Agenda MVC');
        $pdf->SetAuthor('Agenda MVC');
        $pdf->SetTitle($titulo);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 20);
        $pdf->AddPage();

        // ── Título ───────────────────────────────────────────
        $pdf->SetFont('helvetica', 'B', 15);
        $pdf->SetFillColor(26, 26, 46);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 13, $titulo, 0, 1, 'C', true);

        // ── Subtítulo ────────────────────────────────────────
        $pdf->SetFont('helvetica', 'I', 8);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell(
            0, 6,
            'Generado: ' . date('d/m/Y H:i:s') .
            '   |   Total: ' . count($datos) . ' registros',
            0, 1, 'C'
        );
        $pdf->Ln(3);

        // ── Ancho de columnas ────────────────────────────────
        $anchoDisponible = $pdf->getPageWidth() - 20;
        $totalCols       = count($columnas);
        $anchoCelda      = round($anchoDisponible / $totalCols, 2);

        // ── Encabezados ──────────────────────────────────────
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFillColor(15, 52, 96);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetDrawColor(180, 180, 180);
        $pdf->SetLineWidth(0.2);

        foreach ($columnas as $campo => $encabezado) {
            $pdf->Cell($anchoCelda, 8, $encabezado, 1, 0, 'C', true);
        }
        $pdf->Ln();

        // ── Filas de datos ───────────────────────────────────
        $pdf->SetFont('helvetica', '', 7.5);
        $pdf->SetTextColor(33, 37, 41);

        $filaNum = 0;
        foreach ($datos as $registro) {

            // Color alternado
            $pdf->SetFillColor(($filaNum % 2 === 0) ? 240 : 255, ($filaNum % 2 === 0) ? 244 : 255, ($filaNum % 2 === 0) ? 248 : 255);

            foreach ($columnas as $campo => $encabezado) {
                // Usamos el método centralizado
                $valor = self::formatearDato($campo, $registro[$campo] ?? '');

                // Truncar textos largos de forma segura (sin cortar palabras a medias si es posible)
                if (mb_strlen($valor) > 30) {
                    $valor = mb_strimwidth($valor, 0, 28, '...', 'UTF-8');
                }

                $pdf->Cell($anchoCelda, 7, $valor, 1, 0, 'L', true);
            }
            $pdf->Ln();
            $filaNum++;

            // Nueva página si se llena
            if ($pdf->GetY() > ($pdf->getPageHeight() - 25)) {
                $pdf->AddPage();

                // Repetir encabezados
                $pdf->SetFont('helvetica', 'B', 8);
                $pdf->SetFillColor(15, 52, 96);
                $pdf->SetTextColor(255, 255, 255);
                foreach ($columnas as $campo => $encabezado) {
                    $pdf->Cell($anchoCelda, 8, $encabezado, 1, 0, 'C', true);
                }
                $pdf->Ln();
                
                // Restaurar estilo de datos
                $pdf->SetFont('helvetica', '', 7.5);
                $pdf->SetTextColor(33, 37, 41);
            }
        }

        // ── Pie de página ────────────────────────────────────
        $pdf->SetFont('helvetica', 'I', 7);
        $pdf->SetTextColor(150, 150, 150);
        $pdf->Ln(3);
        $pdf->Cell(
            0, 5,
            'Agenda MVC   |   ' .
            'Página ' . $pdf->getAliasNumPage() .
            ' de '   . $pdf->getAliasNbPages() .
            '   |   ' . date('d/m/Y H:i'),
            0, 1, 'C'
        );

        // ── Descargar ────────────────────────────────────────
        $pdf->Output($archivo . '.pdf', 'D');
        exit();
    }

    // ─────────────────────────────────────────────────────────
    /**
     * FORMATEO CENTRALIZADO (DRY)
     */
    private static function formatearDato(string $campo, mixed $valor): string
    {
        $valorStr = (string) ($valor ?? '');
        if (empty($valorStr)) return '';

        if (preg_match('/^\d{4}-\d{2}-\d{2}/', $valorStr)) {
            $timestamp = strtotime($valorStr);
            if ($timestamp !== false) {
                return (strpos($valorStr, ':') !== false) 
                    ? date('d/m/Y H:i', $timestamp) 
                    : date('d/m/Y', $timestamp);
            }
        }

        $camposBooleanos = ['activo', 'estado', 'status'];
        if (in_array(strtolower($campo), $camposBooleanos, true)) {
            if (in_array(strtolower($valorStr), ['1', 'true', 'si', 'sí'], true)) {
                return 'Activo';
            }
            return 'Inactivo';
        }

        return $valorStr;
    }

    // 28/04/26 ────────────────────────────────────────    
    // Exportador a WORD

    public static function word(array $datos, array $columnas, string $titulo, string $archivo): void 
    {

        //Guardamos el nivel de error actual, apagamos los 'Deprecated' (que PhpWord dispara internamente) 
        //para que no impriman basura y rompan los Headers. 

        $nivelErrorOriginal = error_reporting();
        error_reporting($nivelErrorOriginal & ~E_DEPRECATED);

        try {
            $phpWord = new \PhpOffice\PhpWord\PhpWord();
            
            // ⚠️ CORRECCIÓN APLICADA AQUÍ ⚠️
            // Se ha ELIMINADO la línea: $phpWord->getSettings()->setUpdateFields(true);
            // Razón: Esta línea provoca que Word muestre una alerta de seguridad al abrir el archivo, 
            // ya que forza una actualización de campos. Word calculará PAGE y NUMPAGES automáticamente al renderizar.

            $phpWord->setDefaultFontName('Calibri');
            $phpWord->setDefaultFontSize(11);

            $phpWord->addTableStyle('tablaReporte', [
                'borderSize'  => 6,
                'borderColor' => '30363d',
                'cellMargin'  => 80,
            ], [
                'bgColor'     => '0f3460',
                'color'       => 'FFFFFF',
                'bold'        => true,
                'size'        => 10,
            ]);

            // ── Sección principal ──
            $seccion = $phpWord->addSection([
                'pageSize'     => 'LETTER', 
                'orientation'  => 'landscape',  
                'marginTop'    => 1440,         
                'marginBottom' => 1440, 
                'marginLeft'   => 1440, 
                'marginRight'  => 1440, 
                'headerHeight' => 900,          
                'footerHeight' => 600,          
            ]);

            // ── ENCABEZADO ──
            $header = $seccion->addHeader();
            $headerTable = $header->addTable();
            $headerTable->addRow(500);

            $headerTable->addCell(3000, ['bgColor' => '1a1a2e', 'valign' => 'center'])
                ->addText('📒 Agenda MVC', ['color' => 'FFFFFF', 'bold' => true, 'size' => 14], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::START]);

            $headerTable->addCell(7000, ['bgColor' => '0f3460', 'valign' => 'center'])
                ->addText($titulo, ['color' => 'FFFFFF', 'bold' => true, 'size' => 13], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);

            $headerTable->addCell(3000, ['bgColor' => '1a1a2e', 'valign' => 'center'])
                ->addText(date('d/m/Y H:i'), ['color' => 'FFFFFF', 'size' => 10], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::END]);

            // ── PIE DE PÁGINA ──
            $footer = $seccion->addFooter();
            $footerTable = $footer->addTable();
            $footerTable->addRow(300);

            $footerTable->addCell(6000)->addText(
                'Total de registros: ' . count($datos),
                ['size' => 9, 'color' => '666666', 'italic' => true]
            );

            $celdaPagina = $footerTable->addCell(7000);
            
            $textRun = $celdaPagina->addTextRun([
                'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::END
            ]);
            
            // Estos campos se calcularán visualmente, sin necesidad de forzar actualización XML
            $textRun->addText('Página ', ['size' => 9, 'color' => '666666']);
            $textRun->addField('PAGE', []); 
            $textRun->addText(' de ', ['size' => 9, 'color' => '666666']);
            $textRun->addField('NUMPAGES', []);

            // ── CUERPO ──
            $seccion->addTextBreak(1); 
            
            $seccion->addText('Detalle de registros exportados', [
                'size' => 11, 
                'color' => '555555', 
                'italic' => true
            ]);
            
            $seccion->addTextBreak(1);

            $anchoTotal  = 12960; 
            $totalCols   = count($columnas);
            $anchoCelda  = (int)($anchoTotal / $totalCols);

            $tabla = $seccion->addTable('tablaReporte');

            $tabla->addRow(400);
            foreach ($columnas as $campo => $encabezado) {
                $tabla->addCell($anchoCelda, ['bgColor' => '0f3460', 'valign' => 'center'])
                    ->addText($encabezado, ['bold' => true, 'color' => 'FFFFFF', 'size' => 10], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
            }

            $filaNum = 0;
            foreach ($datos as $registro) {
                $tabla->addRow(350);
                $bgColor = ($filaNum % 2 === 0) ? 'FFFFFF' : 'f0f4f8';

                foreach ($columnas as $campo => $encabezado) {
                    $valor = self::formatearDato($campo, $registro[$campo] ?? '');
                    if (strlen($valor) > 50) $valor = substr($valor, 0, 47) . '...';

                    $tabla->addCell($anchoCelda, ['bgColor' => $bgColor, 'valign' => 'center'])->addText($valor, ['size' => 9, 'color' => '212529']);
                }
                $filaNum++;
            }

            // ── GUARDADO TEMPORAL ──
            $tmpDir = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'tmp';
            if (!is_dir($tmpDir)) {
                mkdir($tmpDir, 0777, true);
            }
            $tempFile = $tmpDir . DIRECTORY_SEPARATOR . $archivo . '.docx';

            $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
            $writer->save($tempFile);

        } catch (\Exception $e) {
            // Si algo falla gravemente, restauramos los errores y matamos el proceso
            error_reporting($nivelErrorOriginal);
            die("Error crítico generando Word: " . $e->getMessage());
        }

        // RESTAURAMOS EL NIVEL DE ERROR ORIGINAL,el entorno vuelve a la normalidad estricta
        error_reporting($nivelErrorOriginal);

        // ── DESCARGA LIMPIA (Aquí no hay basura en pantalla) ──
        if (file_exists($tempFile)) {
            if (ob_get_level()) { ob_end_clean(); }
            header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            header('Content-Disposition: attachment; filename="' . $archivo . '.docx"');
            header('Content-Length: ' . filesize($tempFile));
            header('Cache-Control: max-age=0');
            header('Pragma: public');
            readfile($tempFile);
            unlink($tempFile);
        } else {
            die("Error: No se encontró el archivo temporal.");
        }
        exit();
    }//End function word

    

    /**
     * IMAGEN — Exporta infografía con estadísticas a JPG (CON ACENTOS Y FUENTES TTF)
     */
    public static function imagen(array  $datos, array $columnas, array $stats, string $titulo, string $archivo): void 
    {
        $ancho     = 1200;
        $margen    = 40;
        $filasTabla = min(count($datos), 15); 
        $altoHeader = 120;
        $altoStats  = 130;
        $altoTabla  = 40 + ($filasTabla * 30) + 40;
        $altoFooter = 50;
        $alto       = $altoHeader + $altoStats + $altoTabla + $altoFooter + 20;

        $img = imagecreatetruecolor($ancho, $alto);
        imageantialias($img, true);

        // ── Paleta de colores ────────────────────────────────
        $cBgBody    = self::hex2rgb($img, '#0d1117');
        $cNavOsc    = self::hex2rgb($img, '#1a1a2e');
        $cNavMed    = self::hex2rgb($img, '#0f3460');
        $cBlanco    = self::hex2rgb($img, '#ffffff');
        $cGrisOsc   = self::hex2rgb($img, '#161b22');
        $cGrisMed   = self::hex2rgb($img, '#1c2128');
        $cGrisClaro = self::hex2rgb($img, '#8b949e');
        $cBorde     = self::hex2rgb($img, '#30363d');
        $cAzul      = self::hex2rgb($img, '#0d6efd');
        $cTexto     = self::hex2rgb($img, '#e6edf3');

        // ── NUEVAS FUENTES TRUE TYPE (UTF-8 COMPLETO) ───────
        // Usamos las fuentes nativas de Windows. Quedan increíbles y soportan todo.
        $fuenteNegrita = 'C:\Windows\Fonts\arialbd.ttf';
        $fuenteNormal  = 'C:\Windows\Fonts\arial.ttf';
        
        $sizeGrande = 20;
        $sizeMedia  = 13;
        $sizePeq    = 10;

        imagefilledrectangle($img, 0, 0, $ancho, $alto, $cBgBody);
        imagefilledrectangle($img, 0, 0, $ancho, $altoHeader, $cNavOsc);
        imagefilledrectangle($img, 0, $altoHeader - 5, $ancho, $altoHeader, $cNavMed);
        imagefilledrectangle($img, 0, 0, 8, $altoHeader, $cAzul);

        // Título centrado (usando imagettfbbox para calcular el ancho exacto del texto)
        $bbox = imagettfbbox($sizeGrande, 0, $fuenteNegrita, $titulo);
        $anchoTexto = $bbox[2] - $bbox[0];
        $xTitulo = ($ancho - $anchoTexto) / 2;
        // Nota: Se le suma el tamaño de fuente a la Y porque TTF dibuja desde la línea base
        imagettftext($img, $sizeGrande, 0, max(20, $xTitulo), 30 + $sizeGrande, $cBlanco, $fuenteNegrita, $titulo);

        // Subtítulo
        $subtitulo  = 'Agenda MVC  |  Generado: ' . date('d/m/Y H:i:s') . '  |  Total: ' . count($datos) . ' registros';
        $bbox = imagettfbbox($sizePeq, 0, $fuenteNormal, $subtitulo);
        $anchoSub = $bbox[2] - $bbox[0];
        $xSub = ($ancho - $anchoSub) / 2;
        imagettftext($img, $sizePeq, 0, max(20, $xSub), 70 + $sizePeq, $cGrisClaro, $fuenteNormal, $subtitulo);

        // ═══════════════════════════════════════════════════
        // SECCIÓN 2: TARJETAS DE ESTADÍSTICAS
        // ═══════════════════════════════════════════════════
        $yStats = $altoHeader + 15; 
        $numStats = count($stats);
        $anchoCard = (int)(($ancho - ($margen * 2) - (($numStats - 1) * 15)) / $numStats);
        $xCard = $margen;

        foreach ($stats as $stat) {
            $colorCard = self::hex2rgb($img, $stat['color'] ?? '#0d6efd');
            imagefilledrectangle($img, $xCard, $yStats, $xCard + $anchoCard, $yStats + 100, $cGrisOsc);
            imagefilledrectangle($img, $xCard, $yStats, $xCard + $anchoCard, $yStats + 5, $colorCard);
            imagerectangle($img, $xCard, $yStats, $xCard + $anchoCard, $yStats + 100, $cBorde);
            
            $valorStr = (string)$stat['valor'];
            $bbox = imagettfbbox($sizeGrande, 0, $fuenteNegrita, $valorStr);
            $anchoVal = $bbox[2] - $bbox[0];
            $xVal = $xCard + ($anchoCard / 2) - ($anchoVal / 2);
            imagettftext($img, $sizeGrande, 0, $xVal, $yStats + 25 + $sizeGrande, $colorCard, $fuenteNegrita, $valorStr);
            
            $label = $stat['label'];
            $bbox = imagettfbbox($sizePeq, 0, $fuenteNormal, $label);
            $anchoLabel = $bbox[2] - $bbox[0];
            $xLabel = $xCard + ($anchoCard / 2) - ($anchoLabel / 2);
            imagettftext($img, $sizePeq, 0, max($xCard + 5, $xLabel), $yStats + 70 + $sizePeq, $cTexto, $fuenteNormal, $label);
            
            imageline($img, $xCard + 10, $yStats + 60, $xCard + $anchoCard - 10, $yStats + 61, $cBorde);
            $xCard += $anchoCard + 15;
        }

        // ═══════════════════════════════════════════════════
        // SECCIÓN 3: TABLA DE DATOS
        // ═══════════════════════════════════════════════════
        $yTabla = $altoHeader + $altoStats; 
        $totalCols = count($columnas);
        $anchoCol = (int)(($ancho - ($margen * 2)) / $totalCols);
        
        $tituloSeccion = 'DETALLE DE REGISTROS';
        imagettftext($img, $sizeMedia, 0, $margen, $yTabla + 15 + $sizeMedia, $cGrisClaro, $fuenteNegrita, $tituloSeccion);
        $yTabla += 35;

        // ── Fila de encabezados ──────────────────────────────
        $xCol = $margen;
        foreach ($columnas as $campo => $encabezado) {
            imagefilledrectangle($img, $xCol, $yTabla, $xCol + $anchoCol, $yTabla + 30, $cNavMed);
            imagerectangle($img, $xCol, $yTabla, $xCol + $anchoCol, $yTabla + 30, $cBorde);
            
            // Truncamos respetando caracteres (ahora con UTF-8 real usamos mb_substr)
            $enc = mb_strlen($encabezado) > 10 ? mb_substr($encabezado, 0, 8) . '..' : $encabezado;
            imagettftext($img, $sizePeq, 0, $xCol + 5, $yTabla + 10 + $sizePeq, $cBlanco, $fuenteNegrita, $enc);
            $xCol += $anchoCol;
        }
        $yTabla += 30;

        // ── Filas de datos ───────────────────────────────────
        $filaNum = 0;
        foreach (array_slice($datos, 0, $filasTabla) as $registro) {
            $bgFila = ($filaNum % 2 === 0) ? $cGrisOsc : $cGrisMed; 
            $xCol = $margen;
            
            foreach ($columnas as $campo => $encabezado) {
                imagefilledrectangle($img, $xCol, $yTabla, $xCol + $anchoCol, $yTabla + 28, $bgFila);
                imagerectangle($img, $xCol, $yTabla, $xCol + $anchoCol, $yTabla + 28, $cBorde);
                
                $val = self::formatearDato($campo, $registro[$campo] ?? '');
                
                // Truncado seguro para UTF-8
                $maxChars = (int)($anchoCol / 6) - 1; // Aprox 6 pixeles por letra con Arial 10
                if (mb_strlen($val) > $maxChars) {
                    $val = mb_substr($val, 0, $maxChars - 2) + '..';
                }
                
                imagettftext($img, $sizePeq, 0, $xCol + 5, $yTabla + 8 + $sizePeq, $cTexto, $fuenteNormal, $val);
                $xCol += $anchoCol;
            }
            $yTabla += 28; 
            $filaNum++;
        }

        if (count($datos) > $filasTabla) {
            $nota = '... y ' . (count($datos) - $filasTabla) . ' registros más. Descarga Excel o PDF para ver todos.';
            imagettftext($img, $sizePeq, 0, $margen, $yTabla + 8 + $sizePeq, $cGrisClaro, $fuenteNormal, $nota);
        }

        // ═══════════════════════════════════════════════════
        // SECCIÓN 4: PIE DE PÁGINA
        // ═══════════════════════════════════════════════════
        $yFooter = $alto - $altoFooter;
        imagefilledrectangle($img, 0, $yFooter, $ancho, $alto, $cNavOsc);
        imagefilledrectangle($img, 0, $yFooter, $ancho, $yFooter + 3, $cAzul);
        
        $pieTexto = 'Agenda MVC  —  Reporte generado el ' . date('d/m/Y \a \l\a\s H:i:s');
        $bbox = imagettfbbox($sizePeq, 0, $fuenteNormal, $pieTexto);
        $anchoPie = $bbox[2] - $bbox[0];
        $xPie = ($ancho - $anchoPie) / 2;
        imagettftext($img, $sizePeq, 0, max(20, $xPie), $yFooter + 18 + $sizePeq, $cGrisClaro, $fuenteNormal, $pieTexto);

        // ── ENVÍO 100% A PRUEBA DE FALLOS PARA WINDOWS APACHE ──
        $tmpDir = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'tmp';
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0777, true);
        }
        $tempFile = $tmpDir . DIRECTORY_SEPARATOR . $archivo . '.jpg';

        imagejpeg($img, $tempFile, 95);
        imagedestroy($img);

        if (file_exists($tempFile)) {
            if (ob_get_level()) { ob_end_clean(); }
            header('Content-Type: image/jpeg');
            header('Content-Disposition: attachment; filename="' . $archivo . '.jpg"');
            header('Content-Length: ' . filesize($tempFile));
            header('Cache-Control: max-age=0');
            header('Pragma: public');
            readfile($tempFile);
            unlink($tempFile);
        } else {
            die("Error crítico: No se pudo generar la imagen.");
        }
        exit();
    }

    // ─────────────────────────────────────────────────────────
    private static function hex2rgb($img, string $hex): int
    {
        $hex = ltrim($hex, '#');
        $r   = hexdec(substr($hex, 0, 2));
        $g   = hexdec(substr($hex, 2, 2));
        $b   = hexdec(substr($hex, 4, 2));
        return imagecolorallocate($img, $r, $g, $b);
    }

}