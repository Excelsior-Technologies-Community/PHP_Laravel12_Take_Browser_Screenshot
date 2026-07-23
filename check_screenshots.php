<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=PHP_Laravel12_Take_Browser_Screenshot', 'root', '');
$stmt = $pdo->query("DESCRIBE screenshots");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
