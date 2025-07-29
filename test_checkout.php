<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a fake request
$request = Illuminate\Http\Request::create('/customer/checkout', 'GET');
$response = $kernel->handle($request);

echo "Status: " . $response->getStatusCode() . "\n";

// Check if maxDeliveryDistance is passed to view
$content = $response->getContent();
if (strpos($content, 'maxDistance') !== false) {
    echo "✓ maxDistance found in response\n";
    
    // Extract maxDistance value
    preg_match('/maxDistance:\s*(\d+)/', $content, $matches);
    if ($matches) {
        echo "✓ maxDistance value: " . $matches[1] . " km\n";
    }
} else {
    echo "✗ maxDistance not found in response\n";
}

$kernel->terminate($request, $response);