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

// Tujuan Penggunaan :
// $db->table('users')->select('name, email');
// $db->table('users')->select(['name', 'email']);
// $db->table('users')->select('name,email AS mail');
// $db->table('users')->select('*');

$selectAll = $db->table('users')->select('*')->get();
$selectNormal = $db->table('users')->select('name, email')->get();
$selectColumn = $db->table('users')->select('name AS nama, email')->get();
$selectAlias = $db->table('users')->select(['name as nama', 'email as surat'])->get();

$eol = (php_sapi_name() === 'cli') ? PHP_EOL : '<br>';

$first = true;
$second = true;
$third = true;
$fourth = true;

echo "Select : select('*'){$eol}";
foreach ($selectAll as $row) {
    $data = (array) $row;

    if ($first) {
        echo implode(' | ', array_keys($data)) . $eol;
        $first = false;
    }

    echo implode(' | ', $data) . $eol;
}

echo "{$eol}Select : select('name, email'){$eol}";
foreach ($selectColumn as $row) {
    $data = (array) $row;

    if ($second) {
        echo implode(' | ', array_keys($data)) . $eol;
        $second = false;
    }

    echo implode(' | ', $data) . $eol;
}

echo "{$eol}Select : select('name AS nama, email'){$eol}";
foreach ($selectColumn as $row) {
    $data = (array) $row;

    if ($third) {
        echo implode(' | ', array_keys($data)) . $eol;
        $third = false;
    }

    echo implode(' | ', $data) . $eol;
}

echo "{$eol}Select : select(['name as nama', 'email as surat']){$eol}";
foreach ($selectAlias as $row) {
    $data = (array) $row;

    if ($fourth) {
        echo implode(' | ', array_keys($data)) . $eol;
        $fourth = false;
    }

    echo implode(' | ', $data) . $eol;
}