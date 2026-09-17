<?php
function printResultVerticalSimple(mixed $data): void {
    $eol = (php_sapi_name() === 'cli') ? PHP_EOL : '<br>';

    // 1. Decode JSON string kalau perlu
    if (is_string($data)) {
        $decoded = json_decode($data, true);
        if ($decoded !== null) $data = $decoded;
    }

    // 2. Normalisasi single object jadi array of objects
    if (is_object($data)) $data = [$data];

    if (!is_array($data) || empty($data)) {
        echo "No data found" . $eol;
        return;
    }

    $recordNumber = 1;

    foreach ($data as $row) {
        if (is_object($row)) $row = (array) $row;

        echo "Record #{$recordNumber}{$eol}";

        foreach ($row as $key => $value) {
            echo str_pad($key, 12) . ": " . $value . $eol;
        }

        echo str_repeat('-', 30) . $eol; // pemisah antar record
        $recordNumber++;
    }
}
