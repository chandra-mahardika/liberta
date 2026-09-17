# liberta-report

> PDF, Excel, and Word document generation for Liberta microservices.

## Installation

```bash
composer require chandra/liberta-report
```

## Usage

### PDF Report

```php
use Liberta\Report\PdfReport;

$report = new PdfReport();

// HTML to PDF
$pdf = $report->html($html);

// From template
$pdf = $report->template('invoice', [
    'company' => 'Liberta',
    'amount' => 1000000,
    'items' => $items,
]);

// Save to file
$report->save($pdf, '/tmp/invoice.pdf');

// Output to browser
$report->output($pdf, 'invoice.pdf');
```

### Excel Report

```php
use Liberta\Report\ExcelReport;

$report = new ExcelReport();

// From array
$excel = $report->fromArray($headers, $data);

// From template
$excel = $report->template('report', $data);

// Add sheet
$excel = $report->addSheet($excel, 'Sheet2', $headers, $data);

// Save
$report->save($excel, '/tmp/report.xlsx');

// Output
$report->output($excel, 'report.xlsx');
```

### Word Report

```php
use Liberta\Report\WordReport;

$report = new WordReport();

// HTML to Word
$word = $report->html($html);

// From template
$word = $report->template('contract', [
    'client' => 'Client Name',
    'date' => date('Y-m-d'),
    'terms' => $terms,
]);

// Save
$report->save($word, '/tmp/contract.docx');

// Output
$report->output($word, 'contract.docx');
```

### Templates

Templates are stored in `templates/` directory:

```
templates/
├── invoice.html
├── report.xlsx
├── contract.docx
└── ...
```

### Template Variables

```php
// In template: {{variable_name}}
$html = $report->template('invoice', [
    'company' => 'Liberta',
    'amount' => 'Rp 1.000.000',
    'date' => '2026-07-20',
]);

// Loop in template (for Blade-like syntax)
// @foreach($items as $item)
//   <tr>
//     <td>{{item.name}}</td>
//     <td>{{item.quantity}}</td>
//   </tr>
// @endforeach
```

## API

### PdfReport

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `html()` | `string $html` | `string` | HTML to PDF |
| `template()` | `string $name, array $data` | `string` | Render template to PDF |
| `save()` | `string $content, string $path` | `void` | Save PDF |
| `output()` | `string $content, string $filename` | `void` | Output to browser |

### ExcelReport

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `fromArray()` | `array $headers, array $data` | `string` | Create Excel from array |
| `template()` | `string $name, array $data` | `string` | Render template to Excel |
| `addSheet()` | `string $excel, string $name, array $headers, array $data` | `string` | Add worksheet |
| `save()` | `string $content, string $path` | `void` | Save Excel |
| `output()` | `string $content, string $filename` | `void` | Output to browser |

### WordReport

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `html()` | `string $html` | `string` | HTML to Word |
| `template()` | `string $name, array $data` | `string` | Render template to Word |
| `save()` | `string $content, string $path` | `void` | Save Word |
| `output()` | `string $content, string $filename` | `void` | Output to browser |
