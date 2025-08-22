<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "All users in database:\n";
$users = \App\Models\User::select('id', 'email', 'full_name')->get();

foreach ($users as $user) {
    echo "ID: {$user->id}, Email: {$user->email}, Name: {$user->full_name}\n";
}

echo "\nTotal users: " . $users->count() . "\n";

// Check if manager@branch1.com exists
$manager = \App\Models\User::where('email', 'manager@branch1.com')->first();
if ($manager) {
    echo "\nFound manager@branch1.com: ID {$manager->id}, Name: {$manager->full_name}\n";
} else {
    echo "\nmanager@branch1.com not found\n";
}