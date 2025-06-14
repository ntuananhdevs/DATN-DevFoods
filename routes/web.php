<?php

// Test route for updating stock
Route::get('/test-update-stock', function() {
    \Log::debug('Starting stock update test');
    
    // Log broadcasting configuration
    \Log::debug('Broadcasting configuration:', [
        'default' => config('broadcasting.default'),
        'connections' => array_keys(config('broadcasting.connections')),
        'pusher_config' => [
            'app_id' => config('broadcasting.connections.pusher.app_id'),
            'key' => config('broadcasting.connections.pusher.key'),
            'secret' => config('broadcasting.connections.pusher.secret'),
            'cluster' => config('broadcasting.connections.pusher.options.cluster'),
            'driver' => config('broadcasting.default')
        ]
    ]);
    
    $branchStock = \App\Models\BranchStock::where('branch_id', 1)
        ->where('product_variant_id', 793)
        ->first();
    
    if (!$branchStock) {
        \Log::debug('Branch stock not found for branch_id: 1, product_variant_id: 793');
        return 'Branch stock not found';
    }
    
    \Log::debug('Found branch stock:', [
        'branch_id' => $branchStock->branch_id,
        'product_variant_id' => $branchStock->product_variant_id,
        'old_stock' => $branchStock->stock_quantity
    ]);
    
    $oldStock = $branchStock->stock_quantity;
    $branchStock->stock_quantity = 0; // Thay đổi số lượng
    $branchStock->save();
    
    \Log::debug('Stock saved with new quantity:', [
        'new_stock' => $branchStock->stock_quantity
    ]);
    
    try {
        // Dispatch the stock updated event directly
        event(new \App\Events\Customer\StockUpdated(
            $branchStock->branch_id,
            $branchStock->product_variant_id,
            $branchStock->stock_quantity
        ));
        
        \Log::debug('Stock update event dispatched successfully');
    } catch (\Exception $e) {
        \Log::error('Error dispatching stock update event:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return "Error dispatching event: " . $e->getMessage();
    }
    
    return "Stock updated from {$oldStock} to {$branchStock->stock_quantity}";
});

