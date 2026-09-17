<?php
/**
 * Print Query Builder result as vertical JSON (Postman-style)
 *
 * @param mixed $data  Array, object, or JSON string
 */

 /*
    1. Hasil Query Builder get()
    $users = $db->table('users')
                ->select(['name as nama', 'email as surat', 'role'])
                ->get();

    Print vertical JSON, flatten nested
    printJsonVerticalDebug($users);

    2. Single record
    $user = $db->table('users')->where('id',1)->firstOrFail();
    printJsonVerticalDebug($user);

    3. Bisa juga JSON string
    $jsonString = json_encode($users);
    printJsonVerticalDebug($jsonString);
*/

function printJsonVerticalDebug(mixed $data): void {
    $eol = (php_sapi_name() === 'cli') ? PHP_EOL : '<br>';

    // 1. Decode JSON string jika diperlukan
    if (is_string($data)) {
        $decoded = json_decode($data, true);
        if ($decoded !== null) $data = $decoded;
    }

    // 2. Normalisasi single object jadi array of object
    if (is_object($data)) $data = [$data];

    // 3. Flatten nested objects/arrays recursively
    $flattenRecursive = function ($item) use (&$flattenRecursive) {
        if (is_object($item)) $item = (array) $item;

        if (is_array($item)) {
            $flat = [];
            foreach ($item as $key => $value) {
                if (is_array($value) || is_object($value)) {
                    // flatten nested with key prefix
                    foreach ($flattenRecursive($value) as $subKey => $subVal) {
                        $flat["{$key}.{$subKey}"] = $subVal;
                    }
                } else {
                    $flat[$key] = $value;
                }
            }
            return $flat;
        }
        return $item;
    };

    $final = [];
    foreach ($data as $row) {
        $final[] = $flattenRecursive($row);
    }

    // 4. Encode ke JSON vertical / pretty print
    $jsonString = json_encode($final, JSON_PRETTY_PRINT);

    // 5. Tambahkan <br> kalau browser
    if (php_sapi_name() !== 'cli') {
        $jsonString = nl2br($jsonString);
    }

    echo $jsonString . $eol;
}
