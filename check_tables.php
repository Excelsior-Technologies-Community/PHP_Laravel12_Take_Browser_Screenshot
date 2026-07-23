<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=PHP_Laravel12_Take_Browser_Screenshot', 'root', '');
$stmt = $pdo->query("SHOW TABLES");
while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
    echo "Table: " . $row[0] . "\n";
}
