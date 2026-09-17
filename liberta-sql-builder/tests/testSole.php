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

$user = $db
    ->table('users')
    ->where('email', '=', 'alice@example.com')
    ->sole();

$eol = (php_sapi_name() === 'cli') ? PHP_EOL : '<br>';

echo "Select Sole{$eol}";

$data = (array) $user;

// header (nama kolom)
echo implode(' | ', array_keys($data)) . $eol;

// data
echo implode(' | ', $data) . $eol;
