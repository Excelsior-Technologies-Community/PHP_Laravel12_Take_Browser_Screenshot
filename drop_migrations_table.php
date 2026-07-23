<?php
$host = '127.0.0.1';
$db = 'PHP_Laravel12_Take_Browser_Screenshot';
$user = 'root';
$pass = '';

$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

$result = $mysqli->query("DROP TABLE IF EXISTS migrations");
if ($result) {
    echo "Dropped migrations table successfully\n";
} else {
    echo "Error: " . $mysqli->error . "\n";
}

$mysqli->close();
