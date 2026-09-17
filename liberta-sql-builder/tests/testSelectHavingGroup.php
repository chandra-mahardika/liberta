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

/**
 *** Urutan di Query ***
 * WHERE
 * GROUP BY
 * HAVING
 * ORDER BY
 * LIMIT
 */

$selectHaving = $db->table('orders')
                    ->select([
                        'customerNumber',
                        'COUNT(*) AS total',
                        'MAX(orderDate) AS last_order'
                    ])
                    ->groupBy('customerNumber')
                    ->having('COUNT(*)', '>', 3)
                    ->orderBy('last_order', 'DESC')
                    ->get();

$eol = (php_sapi_name() === 'cli') ? PHP_EOL : '<br>';

$first = true;

echo "Select Where Between{$eol}";

foreach ($selectHaving as $row) {
    $data = (array) $row;

    if ($first) {
        echo implode(' | ', array_keys($data)) . $eol;
        $first = false;
    }

    echo implode(' | ', $data) . $eol;
}

