<?php

/**
 * Test script for API Order endpoint
 * 
 * This script demonstrates how to call the new API endpoint
 * Run this script from the command line: php test_api_order.php
 */

// Sample data for testing (updated with valid IDs)
$testData = [
    'user_id' => 6,        // User ID = 6
    'address_id' => 13,    // Address ID = 12 (belongs to user 6)
    'payment_method' => 'vnpay',  // VNPAY payment method
    'note' => 'Test order via API - Giao trong giờ hành chính - VNPAY',
    'items' => [
        ['product_id' => 1, 'quantity' => 2],  // Burger Bò Phô Mai
        ['product_id' => 2, 'quantity' => 1]   // Burger Gà Giòn
    ]
];

// API endpoint URL (adjust as needed)
$apiUrl = 'http://localhost:8000/api/orders';

// Initialize cURL
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);

// Execute the request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Check for cURL errors
if (curl_error($ch)) {
    echo "cURL Error: " . curl_error($ch) . "\n";
    curl_close($ch);
    exit(1);
}

curl_close($ch);

// Display results
echo "=== API Order Test Results ===\n";
echo "HTTP Code: " . $httpCode . "\n";
echo "Response: " . $response . "\n";

// Parse and display formatted response
$responseData = json_decode($response, true);
if ($responseData) {
    echo "\n=== Formatted Response ===\n";
    if (isset($responseData['success']) && $responseData['success']) {
        echo "✅ SUCCESS: " . $responseData['message'] . "\n";
        echo "Order ID: " . $responseData['order_id'] . "\n";
        echo "Order Code: " . $responseData['order_code'] . "\n";
        echo "Total Amount: " . number_format($responseData['total_amount']) . " VND\n";
    } else {
        echo "❌ ERROR: " . $responseData['message'] . "\n";
        if (isset($responseData['errors'])) {
            echo "Validation Errors:\n";
            foreach ($responseData['errors'] as $field => $messages) {
                echo "  - $field: " . implode(', ', $messages) . "\n";
            }
        }
    }
} else {
    echo "❌ Failed to parse JSON response\n";
}

echo "\n=== Test Data Used ===\n";
echo json_encode($testData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

?> 