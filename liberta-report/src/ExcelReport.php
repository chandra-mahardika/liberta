<?php

namespace Liberta\Report;

class ExcelReport implements ReportGenerator
{
    private ?string $sheetName = null;

    public function sheetName(string $name): static
    {
        $this->sheetName = $name;
        return $this;
    }

    public function generate(array $data, array $options = []): string
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($this->sheetName ?? $options['sheet'] ?? 'Report');

        $columns = $options['columns'] ?? [];
        $rows = $data['rows'] ?? $data;
        $startRow = $options['start_row'] ?? 1;

        if (empty($columns) && !empty($rows)) {
            $columns = array_keys((array) reset($rows));
        }

        // Headers
        $colIndex = 'A';
        foreach ($columns as $col) {
            $label = is_string($col) ? ucfirst(str_replace('_', ' ', $col)) : $col;
            $sheet->setCellValue("{$colIndex}{$startRow}", $label);
            $sheet->getStyle("{$colIndex}{$startRow}")->getFont()->setBold(true);
            $colIndex++;
        }

        // Data rows
        $rowIndex = $startRow + 1;
        foreach ($rows as $row) {
            $colIndex = 'A';
            foreach ($columns as $col) {
                $key = is_array($col) ? ($col['key'] ?? '') : $col;
                $value = is_array($row) ? ($row[$key] ?? '') : ($row->$key ?? '');
                $sheet->setCellValue("{$colIndex}{$rowIndex}", $value);
                $colIndex++;
            }
            $rowIndex++;
        }

        // Auto-width
        foreach (range('A', $colIndex) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer_Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'xlsx');
        $writer->save($tempFile);

        $content = file_get_contents($tempFile);
        unlink($tempFile);

        return $content;
    }

    public function save(string $path, array $data, array $options = []): void
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($this->sheetName ?? $options['sheet'] ?? 'Report');

        $columns = $options['columns'] ?? [];
        $rows = $data['rows'] ?? $data;

        if (empty($columns) && !empty($rows)) {
            $columns = array_keys((array) reset($rows));
        }

        $colIndex = 'A';
        foreach ($columns as $col) {
            $label = is_string($col) ? ucfirst(str_replace('_', ' ', $col)) : $col;
            $sheet->setCellValue("{$colIndex}1", $label);
            $sheet->getStyle("{$colIndex}1")->getFont()->setBold(true);
            $colIndex++;
        }

        $rowIndex = 2;
        foreach ($rows as $row) {
            $colIndex = 'A';
            foreach ($columns as $col) {
                $key = is_array($col) ? ($col['key'] ?? '') : $col;
                $value = is_array($row) ? ($row[$key] ?? '') : ($row->$key ?? '');
                $sheet->setCellValue("{$colIndex}{$rowIndex}", $value);
                $colIndex++;
            }
            $rowIndex++;
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer_Xlsx($spreadsheet);
        $writer->save($path);
    }

    public function contentType(): string
    {
        return 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    }

    public function extension(): string
    {
        return 'xlsx';
    }
}
