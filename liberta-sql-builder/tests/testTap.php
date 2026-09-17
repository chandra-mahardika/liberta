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

$user = $db->table('orders')
            ->where('status', '=', 'In Process')
            ->tap(fn ($q) => var_dump($q->toSql()))
            ->get();


