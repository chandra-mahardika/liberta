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

return $db->table('users')
    ->insert([
        'name'  => 'Ernie Ball',
        'email' => 'ernie@galuh.id',
        'password' => password_hash('secret', PASSWORD_BCRYPT)
    ]);

