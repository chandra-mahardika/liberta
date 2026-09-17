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

$data = [
    'name' => 'Ida Felicia',
];

return $db->table('users')->where('id', '=', 21)->delete();

