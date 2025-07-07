<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TS-POS Database Check</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">TS-POS Database Status</h1>
        
        <?php
        try {
            // Check if we can connect to Laravel
            require_once __DIR__ . '/../vendor/autoload.php';
            $app = require_once __DIR__ . '/../bootstrap/app.php';
            
            echo '<div class="bg-green-50 border border-green-200 p-4 rounded-lg mb-4">';
            echo '<h2 class="text-lg font-semibold text-green-800">✅ Laravel Application Loaded</h2>';
            echo '</div>';
            
            // Check database connection
            try {
                $pdo = new PDO('sqlite:' . __DIR__ . '/../database/database.sqlite');
                echo '<div class="bg-green-50 border border-green-200 p-4 rounded-lg mb-4">';
                echo '<h2 class="text-lg font-semibold text-green-800">✅ Database Connection: OK</h2>';
                echo '</div>';
                
                // Check for tables
                $tables = ['users', 'collections', 'dresses', 'dress_items', 'sales', 'sale_items'];
                $existingTables = [];
                
                foreach ($tables as $table) {
                    $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='$table'");
                    if ($stmt->fetch()) {
                        $existingTables[] = $table;
                    }
                }
                
                if (count($existingTables) === count($tables)) {
                    echo '<div class="bg-green-50 border border-green-200 p-4 rounded-lg mb-4">';
                    echo '<h2 class="text-lg font-semibold text-green-800">✅ All Required Tables Exist</h2>';
                    echo '<ul class="text-sm text-green-700 mt-2">';
                    foreach ($existingTables as $table) {
                        echo "<li>• $table</li>";
                    }
                    echo '</ul>';
                    echo '</div>';
                } else {
                    echo '<div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg mb-4">';
                    echo '<h2 class="text-lg font-semibold text-yellow-800">⚠️ Missing Tables</h2>';
                    echo '<p class="text-sm text-yellow-700 mt-2">Run: <code>php artisan migrate</code></p>';
                    echo '</div>';
                }
                
                // Check for admin user
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE email = 'admin@tspos.com'");
                $result = $stmt->fetch();
                
                if ($result['count'] > 0) {
                    echo '<div class="bg-green-50 border border-green-200 p-4 rounded-lg mb-4">';
                    echo '<h2 class="text-lg font-semibold text-green-800">✅ Admin User Exists</h2>';
                    echo '<p class="text-sm text-green-700">Email: admin@tspos.com</p>';
                    echo '</div>';
                } else {
                    echo '<div class="bg-red-50 border border-red-200 p-4 rounded-lg mb-4">';
                    echo '<h2 class="text-lg font-semibold text-red-800">❌ No Admin User Found</h2>';
                    echo '<p class="text-sm text-red-700 mt-2">Run: <code>php artisan db:seed</code></p>';
                    echo '</div>';
                }
                
                // Check collections count
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM collections");
                $result = $stmt->fetch();
                echo '<div class="bg-blue-50 border border-blue-200 p-4 rounded-lg mb-4">';
                echo '<h2 class="text-lg font-semibold text-blue-800">📊 Collections: ' . $result['count'] . '</h2>';
                echo '</div>';
                
                // Check dress items count
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM dress_items");
                $result = $stmt->fetch();
                echo '<div class="bg-blue-50 border border-blue-200 p-4 rounded-lg mb-4">';
                echo '<h2 class="text-lg font-semibold text-blue-800">👗 Dress Items: ' . $result['count'] . '</h2>';
                echo '</div>';
                
            } catch (PDOException $e) {
                echo '<div class="bg-red-50 border border-red-200 p-4 rounded-lg mb-4">';
                echo '<h2 class="text-lg font-semibold text-red-800">❌ Database Error</h2>';
                echo '<p class="text-sm text-red-700">' . $e->getMessage() . '</p>';
                echo '</div>';
            }
            
        } catch (Exception $e) {
            echo '<div class="bg-red-50 border border-red-200 p-4 rounded-lg mb-4">';
            echo '<h2 class="text-lg font-semibold text-red-800">❌ Application Error</h2>';
            echo '<p class="text-sm text-red-700">' . $e->getMessage() . '</p>';
            echo '</div>';
        }
        ?>
        
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-xl font-semibold mb-4">🔧 Setup Commands</h2>
            
            <div class="space-y-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="font-semibold mb-2">If tables are missing:</h3>
                    <code class="text-sm bg-gray-800 text-green-400 p-2 rounded block">php artisan migrate</code>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="font-semibold mb-2">If no sample data:</h3>
                    <code class="text-sm bg-gray-800 text-green-400 p-2 rounded block">php artisan db:seed</code>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="font-semibold mb-2">Reset everything:</h3>
                    <code class="text-sm bg-gray-800 text-green-400 p-2 rounded block">php artisan migrate:fresh --seed</code>
                </div>
            </div>
        </div>
        
        <div class="mt-6 text-center">
            <a href="demo.html" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 mr-4">
                Try Demo Login
            </a>
            <a href="../public" class="bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600">
                Access Main App
            </a>
        </div>
    </div>
</body>
</html>
