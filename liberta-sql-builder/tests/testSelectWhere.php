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

$selectWhere = $db->table('users')
                ->where('active', '=', 1)
                ->orNotWhere('role', '=', 'admin')
                ->whereNull('deleted_at')
                ->whereIn('id', [1, 2, 3])
                ->get();

$eol = (php_sapi_name() === 'cli') ? PHP_EOL : '<br>';

echo "Select where, orNotWhere, whereNull, whereIn{$eol}";

echo "SELECT * FROM users WHERE active = :b0 OR NOT role = :b1 AND deleted_at IS NULL AND id IN (:b2, :b3, :b4)";
echo "{$eol}{$eol}";
printResult($selectWhere);
echo "{$eol}";
echo "Json{$eol}";
printJsonVerticalDebug($selectWhere);
