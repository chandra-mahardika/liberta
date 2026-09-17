<?php

use Liberta\Sql\DB;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/print_result/resultJsonDebug.php';
require_once __DIR__ . '/print_result/result.php';

$db = new DB([
    'driver'   => 'mysql',
    'host'     => 'localhost',
    'database' => 'sql_test',
    'username' => 'root',
    'password' => 'root'
]);

$selectLike = $db->table('users')
                    ->like('name', 'Aime')
                    ->get();

$eol = (php_sapi_name() === 'cli') ? PHP_EOL : '<br>';

echo "Select Where LIKE{$eol}";

echo "SELECT * FROM users WHERE name LIKE :b0";
echo "{$eol}{$eol}";
printResult($selectLike);
echo "{$eol}";
echo "Json{$eol}";
printJsonVerticalDebug($selectLike);
