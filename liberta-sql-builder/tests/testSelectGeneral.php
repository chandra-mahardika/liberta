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

$users = $db->table('users')->get();
$usersSelect = $db->table('users')->select('name')->get();
$userInject = $db->table('users')
    ->where('email', '=', 'alice@example.com')
    ->get();

echo json_encode($users)."\n";
echo json_encode($usersSelect)."\n";
// Tes Binding (Skenario Sql Injection)
echo json_encode($userInject)."\n";
