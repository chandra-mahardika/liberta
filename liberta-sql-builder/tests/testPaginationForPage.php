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

/**
 * Kapan memakai forPage()
 * Pakai forPage() jika:
 * Data dari UI pagination (page 1,2,3…)
 * API list endpoint
 */
$pagination = $db->table('orders')
                    ->orderBy('orderNumber', 'DESC')
                    ->forPage(2, 10)
                    ->get();

$eol = (php_sapi_name() === 'cli') ? PHP_EOL : '<br>';

echo "Pagination ForPage {$eol}";
echo "SELECT * FROM orders ORDER BY orderNumber DESC LIMIT 10 OFFSET 10";

echo "{$eol}{$eol}";

printJsonVertical($pagination);

