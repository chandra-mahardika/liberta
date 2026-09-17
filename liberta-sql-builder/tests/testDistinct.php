<?php

use Liberta\Sql\DB;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__. '/print_result/resultVertical.php';

$db = new DB([
    'driver'   => 'mysql',
    'host'     => 'localhost',
    'database' => 'sql_test',
    'username' => 'root',
    'password' => 'root'
]);

// Gunakan Aggregasi untuk column non group, seperti
// ->selectRaw('COUNT(*) as total')
// ->selectRaw('MAX(users.active) as active')
// karena ONLY_FULL_GROUP_BY

$distinct = $db->table('users')
    ->distinct()
    ->select('name')
    ->addSelect('users.active')
    ->selectRaw('COUNT(*) as total')
    ->groupBy('name')
    ->get();

// $distinct = $db->table('users')
//     ->select('name')
//     ->selectRaw('COUNT(*) as total')
//     ->selectRaw('MAX(users.active) as active')
//     ->groupBy('name')
//     ->get();

printResultVerticalSimple($distinct);


