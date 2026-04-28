<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

/**
 * ============================================================
 * CLASE Exportador (OPTIMIZADA PARA ALTO RENDIMIENTO)
 * ============================================================
 * Exporta datos a Excel (.xlsx) y PDF.
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
     * 
     * Procesa el valor crudo de la BD para mostrarlo correctamente
     * tanto en Excel como en PDF.
     * 
     * @param string $campo Nombre de la columna en la BD
     * @param mixed  $valor Valor original
     * @return string Valor formateado
     */
    private static function formatearDato(string $campo, mixed $valor): string
    {
        $valorStr = (string) ($valor ?? '');
        if (empty($valorStr)) return '';

        // 1. Fechas (Regex estricto: Espera formato Y-m-d o Y-m-d H:i:s de la BD)
        if (preg_match('/^\d{4}-\d{2}-\d{2}/', $valorStr)) {
            $timestamp = strtotime($valorStr);
            if ($timestamp !== false) {
                // Si tiene horas:minutos, las muestra. Si no, solo fecha.
                return (strpos($valorStr, ':') !== false) 
                    ? date('d/m/Y H:i', $timestamp) 
                    : date('d/m/Y', $timestamp);
            }
        }

        // 2. Booleanos / Estados (Lista blanca estricta para evitar falsos positivos)
        // NOTA: Si tienes otros campos como 'estado', 'vigente', agrégalo en este array.
        $camposBooleanos = ['activo', 'estado', 'status'];
        if (in_array(strtolower($campo), $camposBooleanos, true)) {
            // Considera Activo si es 1, "1", true, "true" o "si"
            if (in_array(strtolower($valorStr), ['1', 'true', 'si', 'sí'], true)) {
                return 'Activo';
            }
            return 'Inactivo';
        }

        return $valorStr;
    }
}