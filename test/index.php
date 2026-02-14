<?php

require 'vendor/autoload.php';

use \JordJD\DCOM\DCOM;

// mysqli

$mysqli = DCOM::getConnection("main");
$result = $mysqli->query('select * from media limit 1');
while ($row = $result->fetch_assoc()) {
    var_dump($row);
}

// PDO

$pdo = DCOM::getConnection("secondary");
foreach ($pdo->query('select * from media limit 1') as $row) {
    var_dump($row);
}