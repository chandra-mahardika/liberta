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
 * Menunjuk halaman 2, per halaman 10 baris
 * page = 2
 * perPage = 10
 * offset = (2 - 1) × 10 = 10
 */
$pagination = $db->table('orders')
                    ->select([
                        'customerNumber',
                        'COUNT(*) AS total',
                        'MAX(orderDate) AS last_order'
                    ])
                    ->groupBy('customerNumber')
                    ->having('COUNT(*)', '>', 3)
                    ->orderBy('last_order', 'DESC')
                    ->forPage(1, 20)
                    ->get();

$eol = (php_sapi_name() === 'cli') ? PHP_EOL : '<br>';

echo "Pagination ForPage Group Having {$eol}";
echo "SELECT customerNumber, COUNT(*) AS total, MAX(orderDate) AS last_order FROM orders  GROUP BY customerNumber HAVING COUNT(*) > :b0 ORDER BY last_order DESC LIMIT 20 OFFSET 0";

echo "{$eol}{$eol}";

printJsonVertical($pagination);
