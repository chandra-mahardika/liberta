<?php

use Liberta\Sql\DB;

require_once __DIR__ . '/../vendor/autoload.php';

$db = new DB([
    'driver'   => 'mysql',
    'host'     => 'localhost',
    'database' => 'sql_test',
    'username' => 'root',
    'password' => 'root'
]);

$selectBetween = $db->table('payments')
                    ->where('customerNumber', '=', '103')
                    ->orWhereNotBetween('amount', 1000, to: 7000)
                    ->get();

$eol = (php_sapi_name() === 'cli') ? PHP_EOL : '<br>';

$first = true;

echo "Select Where Between Array{$eol}";

foreach ($selectBetween as $row) {
    $data = (array) $row;

    if ($first) {
        echo implode(' | ', array_keys($data)) . $eol;
        $first = false;
    }

    echo implode(' | ', $data) . $eol;
}
