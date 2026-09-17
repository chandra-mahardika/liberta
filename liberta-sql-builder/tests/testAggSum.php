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

$sum = $db->table(table: 'payments')
            ->sum('amount', 'amount')
            ->get();

printJsonVertical($sum);
