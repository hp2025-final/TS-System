<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->boot();

$users = App\Models\User::all();
foreach ($users as $user) {
    echo "Email: " . $user->email . " - Name: " . $user->name . "\n";
}
