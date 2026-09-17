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

$user = $db->table('users')
        ->orderBy('id', 'desc')
        ->orderBy('name')
        ->get();

$eol = (php_sapi_name() === 'cli') ? PHP_EOL : '<br>';

$first = true;

echo "Order By{$eol}";

foreach ($user as $row) {
    $data = (array) $row;

    if ($first) {
        echo implode(' | ', array_keys($data)) . $eol;
        $first = false;
    }

    echo implode(' | ', $data) . $eol;
}
