<?php

namespace Liberta\Report;

class PdfReport implements ReportGenerator
{
    private ?string $template = null;

    /**
     * Use an HTML template for the report.
     */
    public function template(string $template): static
    {
        $this->template = $template;
        return $this;
    }

    public function generate(array $data, array $options = []): string
    {
        $html = $this->render($data, $options);
        $pdf = new \Dompdf\Dompdf();

        $pdf->loadHtml($html);
        $pdf->setPaper(
            $options['paper'] ?? 'A4',
            $options['orientation'] ?? 'portrait'
        );
        $pdf->render();

        return $pdf->output();
    }

    public function save(string $path, array $data, array $options = []): void
    {
        $content = $this->generate($data, $options);
        file_put_contents($path, $content);
    }

    public function contentType(): string
    {
        return 'application/pdf';
    }

    public function extension(): string
    {
        return 'pdf';
    }

    private function render(array $data, array $options): string
    {
        if ($this->template !== null && file_exists($this->template)) {
            return $this->renderTemplate($this->template, $data);
        }

        return $this->renderTable($data, $options);
    }

    private function renderTemplate(string $template, array $data): string
    {
        $content = file_get_contents($template);

        foreach ($data as $key => $value) {
            if (is_scalar($value)) {
                $content = str_replace('{' . $key . '}', (string) $value, $content);
            }
        }

        return $content;
    }

    private function renderTable(array $data, array $options): string
    {
        $title = $options['title'] ?? 'Report';
        $columns = $options['columns'] ?? [];
        $rows = $data['rows'] ?? $data;

        $html = '<!DOCTYPE html><html><head><style>';
        $html .= 'body{font-family:sans-serif;font-size:12px;}';
        $html .= 'h1{font-size:18px;margin-bottom:10px;}';
        $html .= 'table{width:100%;border-collapse:collapse;margin-top:10px;}';
        $html .= 'th,td{border:1px solid #ccc;padding:6px 8px;text-align:left;}';
        $html .= 'th{background:#f5f5f5;font-weight:bold;}';
        $html .= '</style></head><body>';
        $html .= "<h1>{$title}</h1>";
        $html .= '<table><thead><tr>';

        if (empty($columns) && !empty($rows)) {
            $columns = array_keys((array) reset($rows));
        }

        foreach ($columns as $col) {
            $label = is_string($col) ? ucfirst(str_replace('_', ' ', $col)) : $col;
            $html .= "<th>{$label}</th>";
        }

        $html .= '</tr></thead><tbody>';

        foreach ($rows as $row) {
            $html .= '<tr>';
            foreach ($columns as $col) {
                $key = is_array($col) ? ($col['key'] ?? '') : $col;
                $value = is_array($row) ? ($row[$key] ?? '') : ($row->$key ?? '');
                $html .= "<td>{$value}</td>";
            }
            $html .= '</tr>';
        }

        $html .= '</tbody></table></body></html>';
        return $html;
    }
}
