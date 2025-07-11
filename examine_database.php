<?php
$pdo = new PDO('mysql:host=localhost;dbname=tspos', 'root', '');
$tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
echo "Database: tspos\n";
echo "Tables:\n";
foreach ($tables as $table) {
    echo "- $table\n";
    $columns = $pdo->query("DESCRIBE $table")->fetchAll();
    foreach ($columns as $column) {
        echo "  {$column['Field']}: {$column['Type']} {$column['Null']} {$column['Key']} {$column['Default']} {$column['Extra']}\n";
    }
    echo "\n";
}
?>
