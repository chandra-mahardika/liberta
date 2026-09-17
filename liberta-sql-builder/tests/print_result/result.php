<?php

/**
 * Summary of printResult
 * @param mixed $data
 * @return void
 */

 /*
    // Bisa array/stdClass
    printResult($selectWhere);

    // Bisa single object
    $user = $db->table('users')->firstOrFail();
    printResult($user);

    // Bisa JSON string
    $jsonString = json_encode($selectWhere);
    printResult($jsonString);

*/
function printResult(mixed $data): void {
    $eol = (php_sapi_name() === 'cli') ? PHP_EOL : '<br>';

    // Kalau string JSON, decode dulu
    if (is_string($data)) {
        $decoded = json_decode($data, true);
        if ($decoded !== null) {
            $data = $decoded;
        }
    }

    // Kalau array of object / array
    if (is_array($data)) {
        // cek apakah associative array atau array of rows
        $first = true;
        foreach ($data as $row) {
            // convert object ke array
            if (is_object($row)) $row = (array) $row;

            // header kolom
            if ($first) {
                echo implode(' | ', array_keys($row)) . $eol;
                $first = false;
            }

            echo implode(' | ', $row) . $eol;
        }

    } elseif (is_object($data)) {
        // single record
        $row = (array) $data;
        echo implode(' | ', array_keys($row)) . $eol;
        echo implode(' | ', $row) . $eol;

    } else {
        // scalar / string biasa
        echo $data . $eol;
    }
}
