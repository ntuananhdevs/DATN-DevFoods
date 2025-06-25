<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\VariantValue;
use Illuminate\Support\Facades\DB;

echo "Total VariantValue count: " . VariantValue::count() . "\n\n";

echo "Checking for duplicate values:\n";
$duplicates = VariantValue::select('value', DB::raw('count(*) as count'))
    ->groupBy('value')
    ->having('count', '>', 1)
    ->get();

if ($duplicates->count() > 0) {
    echo "Found duplicate values:\n";
    foreach ($duplicates as $duplicate) {
        echo "- '{$duplicate->value}' appears {$duplicate->count} times\n";
    }
} else {
    echo "No duplicate values found.\n";
}

echo "\nSample VariantValues:\n";
$samples = VariantValue::take(10)->get();
foreach ($samples as $sample) {
    echo "ID: {$sample->id}, Value: '{$sample->value}', Attribute ID: {$sample->variant_attribute_id}\n";
}