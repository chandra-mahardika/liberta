<?php

use Liberta\Sql\DB;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/print_result/resultJsonDebug.php';
require_once __DIR__ . '/print_result/result.php';

$db = new DB([
    'driver'   => 'mysql',
    'host'     => 'localhost',
    'database' => 'sql_test',
    'username' => 'root',
    'password' => 'root'
]);

$selectBetween = $db->table('orders')
                    ->whereBetween('orderDate', '2003-02-21', '2005-01-19')
                    ->get();

$eol = (php_sapi_name() === 'cli') ? PHP_EOL : '<br>';

echo "Select whereBetween{$eol}";

echo "SELECT * FROM orders WHERE orderDate BETWEEN :b0 AND :b1";
echo "{$eol}{$eol}";
printResult($selectBetween);
echo "{$eol}";
echo "Json{$eol}";
printJsonVerticalDebug($selectBetween);
