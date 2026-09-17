<?php

use Liberta\Sql\DB;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/print_result/resultJsonVertical.php';

$db = new DB([
    'driver'   => 'mysql',
    'host'     => 'localhost',
    'database' => 'sql_test',
    'username' => 'root',
    'password' => 'root'
]);

/**
 * Mengambil 10 baris setelah melewati 20 baris pertama
 */
$selectLimit = $db->table('orders')
                    ->orderBy('orderNumber', 'DESC')
                    ->limit(10)
                    ->offset(20)
                    ->get();

$eol = (php_sapi_name() === 'cli') ? PHP_EOL : '<br>';

echo "SELECT * FROM orders ORDER BY orderNumber DESC LIMIT 10 OFFSET 20";

echo "{$eol}{$eol}";

printJsonVertical($selectLimit);
