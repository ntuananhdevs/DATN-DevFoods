<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CHECKING DATABASE DATA ===\n\n";

// Check Users
echo "📋 USERS:\n";
$users = App\Models\User::select('id', 'full_name', 'email')->take(5)->get();
foreach ($users as $user) {
    echo "ID: {$user->id}, Name: {$user->full_name}, Email: {$user->email}\n";
}

echo "\n📍 ADDRESSES:\n";
$addresses = App\Models\Address::select('id', 'user_id', 'address_line', 'city')->take(5)->get();
foreach ($addresses as $address) {
    echo "ID: {$address->id}, User ID: {$address->user_id}, Address: {$address->address_line}, City: {$address->city}\n";
}

echo "\n🍔 PRODUCTS:\n";
$products = App\Models\Product::select('id', 'name', 'base_price')->take(5)->get();
foreach ($products as $product) {
    echo "ID: {$product->id}, Name: {$product->name}, Price: {$product->base_price}\n";
}

echo "\n🏪 BRANCHES:\n";
$branches = App\Models\Branch::select('id', 'name', 'address')->take(3)->get();
foreach ($branches as $branch) {
    echo "ID: {$branch->id}, Name: {$branch->name}, Address: {$branch->address}\n";
}

echo "\n=== SUGGESTED TEST DATA ===\n";
$firstUser = $users->first();
$firstAddress = $addresses->where('user_id', $firstUser->id ?? 1)->first();
$firstProduct = $products->first();
$secondProduct = $products->skip(1)->first();

if ($firstUser && $firstAddress && $firstProduct) {
    echo "Valid test data:\n";
    echo "{\n";
    echo "  \"user_id\": {$firstUser->id},\n";
    echo "  \"address_id\": {$firstAddress->id},\n";
    echo "  \"payment_method\": \"cod\",\n";
    echo "  \"note\": \"Test order via API\",\n";
    echo "  \"items\": [\n";
    echo "    {\"product_id\": {$firstProduct->id}, \"quantity\": 2}";
    if ($secondProduct) {
        echo ",\n    {\"product_id\": {$secondProduct->id}, \"quantity\": 1}";
    }
    echo "\n  ]\n";
    echo "}\n";
} else {
    echo "❌ Not enough data in database for testing\n";
}

?> 