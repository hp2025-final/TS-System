<?php

require __DIR__ . '/vendor/autoload.php';

use App\Models\User;

// Initialize Laravel application
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== LOGIN TEST ===\n\n";

// Check if users exist
$admin = User::where('email', 'admin@tspos.com')->first();
$staff = User::where('email', 'staff@tspos.com')->first();

echo "Admin user: " . ($admin ? "✅ {$admin->name} ({$admin->email})" : "❌ Not found") . "\n";
echo "Staff user: " . ($staff ? "✅ {$staff->name} ({$staff->email})" : "❌ Not found") . "\n\n";

// Test authentication credentials
if ($admin) {
    $passwordCorrect = \Hash::check('password', $admin->password);
    echo "Admin password check: " . ($passwordCorrect ? "✅ Valid" : "❌ Invalid") . "\n";
}

if ($staff) {
    $passwordCorrect = \Hash::check('password', $staff->password);
    echo "Staff password check: " . ($passwordCorrect ? "✅ Valid" : "❌ Invalid") . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
