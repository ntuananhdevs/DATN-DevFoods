<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';

// Get users with manager role (assuming role_id 2 is for branch managers)
$users = \App\Models\User::whereHas('roles', function($query) {
    $query->where('name', 'branch_manager');
})->orWhereHas('roles', function($query) {
    $query->where('name', 'manager');
})->get();

echo "Branch managers in database:\n";
foreach ($users as $user) {
    echo "ID: {$user->id}, Email: {$user->email}, Name: {$user->full_name}\n";
}

echo "\nTotal managers: " . $users->count() . "\n";

// Also check all users to see available emails
echo "\nAll users:\n";
$allUsers = \App\Models\User::select('id', 'email', 'full_name')->get();
foreach ($allUsers as $user) {
    echo "ID: {$user->id}, Email: {$user->email}, Name: {$user->full_name}\n";
}