<?php

namespace App\Services;

use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;

class ExportService
{
    /**
     * Export collection to Excel (.xlsx).
     *
     * @param Collection $data
     * @param array      $headers  Column header labels
     * @param string     $filename Without extension
     */
    public function toExcel(Collection $data, array $headers, string $filename = 'export'): Response
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Write headers
        foreach ($headers as $col => $label) {
            $sheet->setCellValue([$col + 1, 1], $label);
        }

        // Write rows
        $row = 2;
        foreach ($data as $item) {
            $col = 1;
            foreach (array_keys($headers) as $key) {
                $value = is_array($item) ? ($item[$key] ?? '') : ($item->{$key} ?? '');
                $sheet->setCellValue([$col, $row], $value);
                $col++;
            }
            $row++;
        }

        // Style header
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->getFont()->setBold(true);
        foreach (range(1, count($headers)) as $col) {
            $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'export') . '.xlsx';
        $writer->save($tempFile);

        return response()->download($tempFile, "{$filename}.xlsx")->deleteFileAfterSend(true);
    }

    /**
     * Export data to PDF using a Blade view.
     *
     * @param string     $view     Blade view path
     * @param array      $data     Data passed to view
     * @param string     $filename Without extension
     */
    public function toPdf(string $view, array $data, string $filename = 'export'): Response
    {
        $pdf = Pdf::loadView($view, $data)->setPaper('a4', 'portrait');

        return $pdf->download("{$filename}.pdf");
    }

    /**
     * Export form submissions to Excel.
     */
    public function submissionsToExcel(\App\Models\Form $form): Response
    {
        $submissions = $form->submissions()
            ->with('user')
            ->where('status', 'approved')
            ->get();

        $fieldLabels = $form->fields->pluck('label', 'id')->toArray();

        $rows = $submissions->map(function ($sub) use ($fieldLabels) {
            $row = [
                'student_code' => $sub->user?->student_code ?? '–',
                'full_name'    => $sub->user?->full_name ?? '–',
                'email'        => $sub->user?->email ?? '–',
                'submitted_at' => $sub->submitted_at?->format('d/m/Y H:i'),
                'status'       => $sub->status,
            ];

            foreach ($sub->data as $fieldId => $value) {
                $label = $fieldLabels[$fieldId] ?? "Field {$fieldId}";
                $row[$label] = is_array($value) ? implode(', ', $value) : $value;
            }

            return $row;
        });

        $headers = array_combine(
            array_keys($rows->first() ?? []),
            array_map('ucwords', array_map(fn($k) => str_replace('_', ' ', $k), array_keys($rows->first() ?? [])))
        );

        return $this->toExcel($rows, $headers, "submissions_{$form->id}");
    }
}