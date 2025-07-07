<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TS-POS Simple Demo</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">TS-POS Simple Demo</h1>
        
        <?php
        try {
            // Direct database connection test
            $pdo = new PDO('sqlite:' . __DIR__ . '/../database/database.sqlite');
            
            echo '<div class="bg-green-50 border border-green-200 p-4 rounded-lg mb-6">';
            echo '<h2 class="text-xl font-semibold text-green-800">✅ Database Connected</h2>';
            echo '</div>';
            
            // Test login credentials
            $email = 'admin@tspos.com';
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                echo '<div class="bg-green-50 border border-green-200 p-4 rounded-lg mb-6">';
                echo '<h2 class="text-xl font-semibold text-green-800">✅ Admin User Found</h2>';
                echo '<p>Name: ' . htmlspecialchars($user['name']) . '</p>';
                echo '<p>Email: ' . htmlspecialchars($user['email']) . '</p>';
                echo '<p>Role: ' . htmlspecialchars($user['role']) . '</p>';
                echo '<p>Created: ' . htmlspecialchars($user['created_at']) . '</p>';
                echo '</div>';
                
                // Test password (for demo purposes)
                if (password_verify('password', $user['password'])) {
                    echo '<div class="bg-green-50 border border-green-200 p-4 rounded-lg mb-6">';
                    echo '<h2 class="text-xl font-semibold text-green-800">✅ Password Verification: SUCCESS</h2>';
                    echo '<p>The admin password "password" is correct!</p>';
                    echo '</div>';
                } else {
                    echo '<div class="bg-red-50 border border-red-200 p-4 rounded-lg mb-6">';
                    echo '<h2 class="text-xl font-semibold text-red-800">❌ Password Verification: FAILED</h2>';
                    echo '</div>';
                }
            } else {
                echo '<div class="bg-red-50 border border-red-200 p-4 rounded-lg mb-6">';
                echo '<h2 class="text-xl font-semibold text-red-800">❌ Admin User Not Found</h2>';
                echo '</div>';
            }
            
            // Show collections
            $stmt = $pdo->query("SELECT * FROM collections ORDER BY name");
            $collections = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if ($collections) {
                echo '<div class="bg-white p-6 rounded-lg shadow-lg mb-6">';
                echo '<h2 class="text-xl font-semibold mb-4">📚 Collections (' . count($collections) . ')</h2>';
                echo '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">';
                
                foreach ($collections as $collection) {
                    echo '<div class="border rounded-lg p-4">';
                    echo '<h3 class="font-semibold">' . htmlspecialchars($collection['name']) . '</h3>';
                    echo '<p class="text-sm text-gray-600">' . htmlspecialchars($collection['description']) . '</p>';
                    echo '<p class="text-sm"><strong>Discount:</strong> ' . $collection['discount_percentage'] . '%</p>';
                    echo '<p class="text-sm"><strong>Status:</strong> ' . htmlspecialchars($collection['status']) . '</p>';
                    echo '</div>';
                }
                
                echo '</div>';
                echo '</div>';
            }
            
            // Show dress items with barcodes
            $stmt = $pdo->query("SELECT * FROM dress_items ORDER BY barcode LIMIT 10");
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if ($items) {
                echo '<div class="bg-white p-6 rounded-lg shadow-lg mb-6">';
                echo '<h2 class="text-xl font-semibold mb-4">👗 Sample Dress Items (First 10)</h2>';
                echo '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">';
                
                foreach ($items as $item) {
                    echo '<div class="border rounded-lg p-4">';
                    echo '<h3 class="font-semibold">Barcode: ' . htmlspecialchars($item['barcode']) . '</h3>';
                    echo '<p class="text-sm"><strong>Size:</strong> ' . htmlspecialchars($item['size']) . '</p>';
                    echo '<p class="text-sm"><strong>Color:</strong> ' . htmlspecialchars($item['color']) . '</p>';
                    echo '<p class="text-sm"><strong>Status:</strong> ' . htmlspecialchars($item['status']) . '</p>';
                    if ($item['discount_percentage'] > 0) {
                        echo '<p class="text-sm text-green-600"><strong>Discount:</strong> ' . $item['discount_percentage'] . '%</p>';
                    }
                    echo '</div>';
                }
                
                echo '</div>';
                echo '</div>';
            }
            
            // Count totals
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM collections");
            $collectionCount = $stmt->fetch()['count'];
            
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM dresses");
            $dressCount = $stmt->fetch()['count'];
            
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM dress_items");
            $itemCount = $stmt->fetch()['count'];
            
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
            $userCount = $stmt->fetch()['count'];
            
            echo '<div class="bg-blue-50 border border-blue-200 p-6 rounded-lg">';
            echo '<h2 class="text-xl font-semibold text-blue-900 mb-4">📊 Database Summary</h2>';
            echo '<div class="grid grid-cols-2 md:grid-cols-4 gap-4">';
            echo '<div class="text-center"><div class="text-2xl font-bold text-blue-600">' . $userCount . '</div><div class="text-sm">Users</div></div>';
            echo '<div class="text-center"><div class="text-2xl font-bold text-green-600">' . $collectionCount . '</div><div class="text-sm">Collections</div></div>';
            echo '<div class="text-center"><div class="text-2xl font-bold text-purple-600">' . $dressCount . '</div><div class="text-sm">Dresses</div></div>';
            echo '<div class="text-center"><div class="text-2xl font-bold text-orange-600">' . $itemCount . '</div><div class="text-sm">Items</div></div>';
            echo '</div>';
            echo '</div>';
            
        } catch (PDOException $e) {
            echo '<div class="bg-red-50 border border-red-200 p-4 rounded-lg mb-4">';
            echo '<h2 class="text-lg font-semibold text-red-800">❌ Database Error</h2>';
            echo '<p class="text-sm text-red-700">' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '</div>';
        } catch (Exception $e) {
            echo '<div class="bg-red-50 border border-red-200 p-4 rounded-lg mb-4">';
            echo '<h2 class="text-lg font-semibold text-red-800">❌ Error</h2>';
            echo '<p class="text-sm text-red-700">' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '</div>';
        }
        ?>
        
        <div class="mt-8 text-center">
            <a href="demo.html" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 mr-4">
                Try Vue.js Demo
            </a>
            <a href="check.php" class="bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600 mr-4">
                Laravel Check
            </a>
            <a href="api-test.php" class="bg-purple-500 text-white px-6 py-2 rounded-lg hover:bg-purple-600">
                API Test
            </a>
        </div>
    </div>
</body>
</html>
