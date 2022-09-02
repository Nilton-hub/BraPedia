<?php
require __DIR__ . '/../vendor/autoload.php';

$pdo = \src\core\SQLITEConnect::connect();
$createTable = 'CREATE TABLE test(
            id integer primary key,
            field1 TEXT,
            field2 TEXT)';

var_dump(
    $pdo->query("
        SELECT * FROM test
    ")->fetchAll()
);
