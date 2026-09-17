<?php
/**
 * Print Query Builder Result dengan support alias & nested relation
 *
 * @param mixed $data     Single object, array of objects, atau JSON string
 * @param array|null $metadata   Optional, array dari Builder->selects() ['column'=>'name', 'alias'=>'nama']
 */

/*
    1️⃣ Dengan Query Builder dan metadata
    $builder = $db->table('users')->select(['name as nama', 'email as surat'])->get();
    $metadata = $db->getBuilder()->getSelects(); // contoh: [['column'=>'name','alias'=>'nama'],['column'=>'email','alias'=>'surat']]

    printResultAdvanced($builder, $metadata);

    2️⃣ Single record
    $user = $db->table('users')->where('id',1)->firstOrFail();
    $metadata = $db->getBuilder()->getSelects();

    printResultAdvanced($user, $metadata);

    3️⃣ JSON string
    $json = json_encode($builder);
    printResultAdvanced($json, $metadata
*/
function printResultAdvanced(mixed $data, ?array $metadata = null): void {
    $eol = (php_sapi_name() === 'cli') ? PHP_EOL : '<br>';

    // 1. Decode JSON string kalau perlu
    if (is_string($data)) {
        $decoded = json_decode($data, true);
        if ($decoded !== null) $data = $decoded;
    }

    // 2. Normalisasi single object jadi array of object
    if (is_object($data)) $data = [$data];

    if (!is_array($data) || empty($data)) {
        echo "No data found" . $eol;
        return;
    }

    // 3. Ambil header kolom
    $firstRow = $data[0];
    if (is_object($firstRow)) $firstRow = (array) $firstRow;

    // header = dari metadata jika ada, kalau tidak ambil keys array
    $headers = [];
    if ($metadata) {
        foreach ($metadata as $col) {
            $headers[] = $col['alias'] ?? $col['column'];
        }
    } else {
        $headers = array_keys($firstRow);
    }

    echo implode(' | ', $headers) . $eol;

    // 4. Tampilkan tiap row
    foreach ($data as $row) {
        // convert object ke array
        if (is_object($row)) $row = (array) $row;

        $values = [];
        foreach ($headers as $header) {
            $values[] = $row[$header] ?? ''; // support alias & missing key
        }

        echo implode(' | ', $values) . $eol;
    }
}
