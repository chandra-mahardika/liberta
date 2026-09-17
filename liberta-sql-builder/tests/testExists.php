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

$exists = $db
    ->table('users')
    ->where('email', '=', 'alice@example.com')
    ->exists();

if ($exists) {
    echo "Email sudah terdaftar";
}else{
    echo "Email belum terdaftar";
}

