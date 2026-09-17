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

/**
 * Mengambil nilai dari column
 */
$value = $db->table('users')
            ->where('id', '=', 1)
            ->value('email');

echo json_encode($value);
