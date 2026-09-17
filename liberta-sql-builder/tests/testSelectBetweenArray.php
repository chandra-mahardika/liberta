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

$selectBetween = $db->table('payments')
                    ->whereBetween('amount', [70000, 100000])
                    ->get();

$eol = (php_sapi_name() === 'cli') ? PHP_EOL : '<br>';

$first = true;

echo "Select Where Between Array{$eol}";

echo "SELECT * FROM payments WHERE amount BETWEEN :b0 AND :b1";
echo "{$eol}{$eol}";
printResult($selectBetween);
echo "{$eol}";
echo "Json{$eol}";
printJsonVerticalDebug($selectBetween);
