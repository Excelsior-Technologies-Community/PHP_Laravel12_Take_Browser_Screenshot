<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=PHP_Laravel12_Take_Browser_Screenshot', 'root', '');
$stmt = $pdo->query("SHOW TABLES LIKE 'migrations'");
$exists = $stmt->fetch();

if ($exists) {
    echo "Migrations table EXISTS\n";
    $stmt = $pdo->query("SELECT * FROM migrations");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $row['migration'] . " (batch " . $row['batch'] . ")\n";
    }
} else {
    echo "Migrations table does NOT exist\n";
}

$stmt2 = $pdo->query("SHOW TABLES");
while ($row = $stmt2->fetch(PDO::FETCH_NUM)) {
    echo "Table: " . $row[0] . "\n";
}
