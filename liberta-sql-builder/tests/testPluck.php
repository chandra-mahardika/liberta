<?php

use Liberta\Sql\DB;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/print_result/resultJsonVertical.php';

$db = new DB([
    'driver'   => 'mysql',
    'host'     => 'localhost',
    'database' => 'sql_test',
    'username' => 'root',
    'password' => 'root'
]);

$pluck = $db->table('orders')->pluck('orderNumber');
//$pluckWhere = $db->table('users')->where('id', '=', 1)->pluck('email');

//echo json_encode($pluckWhere)."\n";

$eol = (php_sapi_name() === 'cli') ? PHP_EOL : '<br>';

echo "Pluck";

echo "{$eol}{$eol}";

printJsonVertical($pluck);