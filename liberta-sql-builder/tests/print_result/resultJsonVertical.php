<?php

function printJsonVertical(mixed $data): void {
    $eol = (php_sapi_name() === 'cli') ? PHP_EOL : '<br>';

    // 1. Kalau string JSON, decode dulu
    if (is_string($data)) {
        $decoded = json_decode($data, true);
        if ($decoded !== null) $data = $decoded;
    }

    // 2. Encode ulang pakai JSON_PRETTY_PRINT
    $jsonString = json_encode($data, JSON_PRETTY_PRINT);

    // 3. Tambahkan line break untuk browser
    if (php_sapi_name() !== 'cli') {
        $jsonString = nl2br($jsonString);
    }

    echo $jsonString . $eol;
}
