<?php
$pdo = new PDO('mysql:host=localhost;dbname=tspos', 'root', '');
echo "Checking users...\n";
$users = $pdo->query('SELECT id, name, email, role FROM users')->fetchAll();
foreach ($users as $user) {
    echo "ID: {$user['id']}, Name: {$user['name']}, Email: {$user['email']}, Role: {$user['role']}\n";
}
?>
