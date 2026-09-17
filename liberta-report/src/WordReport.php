<?php

namespace Liberta\Report;

class WordReport implements ReportGenerator
{
    private ?string $template = null;

    public function template(string $template): static
    {
        $this->template = $template;
        return $this;
    }

    public function generate(array $data, array $options = []): string
    {
        $phpWord = new \PhpOffice\PhpWord\PhpWord();

        $title = $options['title'] ?? 'Report';
        $phpWord->addTitle($title);

        $columns = $options['columns'] ?? [];
        $rows = $data['rows'] ?? $data;

        if (empty($columns) && !empty($rows)) {
            $columns = array_keys((array) reset($rows));
        }

        // Table
        if (!empty($rows)) {
            $table = $phpWord->addTable();

            // Header row
            $headerRow = $table->addRow();
            foreach ($columns as $col) {
                $label = is_string($col) ? ucfirst(str_replace('_', ' ', $col)) : $col;
                $headerRow->addCell($label)->getStyle()->setBold(true);
            }

            // Data rows
            foreach ($rows as $row) {
                $dataRow = $table->addRow();
                foreach ($columns as $col) {
                    $key = is_array($col) ? ($col['key'] ?? '') : $col;
                    $value = is_array($row) ? ($row[$key] ?? '') : ($row->$key ?? '');
                    $dataRow->addCell((string) $value);
                }
            }
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'docx');
        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempFile);

        $content = file_get_contents($tempFile);
        unlink($tempFile);

        return $content;
    }

    public function save(string $path, array $data, array $options = []): void
    {
        $phpWord = new \PhpOffice\PhpWord\PhpWord();

        $title = $options['title'] ?? 'Report';
        $phpWord->addTitle($title);

        $columns = $options['columns'] ?? [];
        $rows = $data['rows'] ?? $data;

        if (empty($columns) && !empty($rows)) {
            $columns = array_keys((array) reset($rows));
        }

        if (!empty($rows)) {
            $table = $phpWord->addTable();

            $headerRow = $table->addRow();
            foreach ($columns as $col) {
                $label = is_string($col) ? ucfirst(str_replace('_', ' ', $col)) : $col;
                $headerRow->addCell($label)->getStyle()->setBold(true);
            }

            foreach ($rows as $row) {
                $dataRow = $table->addRow();
                foreach ($columns as $col) {
                    $key = is_array($col) ? ($col['key'] ?? '') : $col;
                    $value = is_array($row) ? ($row[$key] ?? '') : ($row->$key ?? '');
                    $dataRow->addCell((string) $value);
                }
            }
        }

        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($path);
    }

    public function contentType(): string
    {
        return 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
    }

    public function extension(): string
    {
        return 'docx';
    }
}
