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

$join = $db->table('users')
            ->select(
                'users.name, 
                users.email, 
                users.active, 
                posts.title')
            ->join('posts', 'users.id', 'posts.user_id')
            ->get();

$eol = (php_sapi_name() === 'cli') ? PHP_EOL : '<br>';

printResult($join);
echo ($eol);
echo "Json{$eol}";
printJsonVerticalDebug($join);