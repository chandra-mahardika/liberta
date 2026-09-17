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

$join = $db->table('users u')
   ->leftJoin('posts p', 'u.id', '=', 'p.user_id')
   ->orderBy('u.id', 'ASC') 
   ->get();

$eol = (php_sapi_name() === 'cli') ? PHP_EOL : '<br>';

printResult($join);
echo ($eol);
echo "Json{$eol}";
printJsonVerticalDebug($join);