<?php
$pdo = new PDO('mysql:host=localhost;dbname=tspos', 'root', '');
$stmt = $pdo->query('SELECT id, name, size, collection_id FROM dresses WHERE size = "unstitched"');
$unstitched_dresses = $stmt->fetchAll();
echo "Unstitched dresses:\n";
foreach ($unstitched_dresses as $dress) {
    echo "ID: {$dress['id']}, Name: {$dress['name']}, Size: {$dress['size']}, Collection: {$dress['collection_id']}\n";
}
echo "\nDress items for unstitched dresses:\n";
foreach ($unstitched_dresses as $dress) {
    $stmt2 = $pdo->prepare('SELECT COUNT(*) as count FROM dress_items WHERE dress_id = ? AND status = "available"');
    $stmt2->execute([$dress['id']]);
    $count = $stmt2->fetch()['count'];
    echo "Dress ID {$dress['id']} ({$dress['name']}): {$count} items\n";
}
?>
