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

$status = 'Shipped';
$min = 9000;

$selectWhen = $db->table('orders')
                    ->when($status, fn ($q, $status) =>
                        $q->where('status', '=', $status)
                    )
                    ->when($min, fn ($q, $min) =>
                        $q->where('amount', '>=', $min)
                    )
                    ->get();

// Memakai Else
$selectWhenElse = $db->table('orders')
                    ->when(
                        $status,
                        fn ($q, $status) => $q->where('status', '=', $status),
                        fn ($q)          => $q->where('status', '!=', 'Shipped')
                    )
                    ->get();

$first = true;

$eol = (php_sapi_name() === 'cli') ? PHP_EOL : '<br>';

echo "Select When{$eol}";

foreach ($selectWhen as $row) {
    $data = (array) $row;

    if ($first) {
        echo implode(' | ', array_keys($data)) . $eol;
        $first = false;
    }

    echo implode(' | ', $data) . $eol;
}

echo "Select When Else{$eol}";

foreach ($selectWhenElse as $row) {
    $data = (array) $row;

    if ($first) {
        echo implode(' | ', array_keys($data)) . $eol;
        $first = false;
    }

    echo implode(' | ', $data) . $eol;
}

