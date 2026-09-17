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

$db->table('orders')
//    ->selectRaw('customer_id, COUNT(*) OVER() AS total')
   ->where('status', '=', 'Shipped')
   ->ddSql();
