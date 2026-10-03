<?php

namespace App\Helpers;

use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportHelper
{
    /**
     * Export data to a downloadable CSV/Excel file
     *
     * @param string $filename Name of the file without extension
     * @param array $headers Column header titles
     * @param array $rows Array of data rows (each row matching the headers)
     * @param array $titleRows Optional title rows to write before headers
     * @return StreamedResponse
     */
    public static function downloadCsv(string $filename, array $headers, array $rows, array $titleRows = []): StreamedResponse
    {
        $fileNameWithExt = $filename . '_' . date('Y-m-d_H-i-s') . '.csv';

        $callback = function () use ($headers, $rows, $titleRows) {
            $file = fopen('php://output', 'w');

            // Write UTF-8 BOM for Microsoft Excel compatibility
            fputs($file, "\xEF\xBB\xBF");

            // Write title rows if provided
            if (!empty($titleRows)) {
                foreach ($titleRows as $titleRow) {
                    fputcsv($file, (array) $titleRow, ';');
                }
            }

            // Write headers
            fputcsv($file, $headers, ';');

            // Write rows
            foreach ($rows as $row) {
                fputcsv($file, $row, ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=\"{$fileNameWithExt}\"",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ]);
    }
}
