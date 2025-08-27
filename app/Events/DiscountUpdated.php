<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use App\Models\DiscountCode;
use Illuminate\Support\Facades\Log;

class DiscountUpdated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $discount;
    public $action; // create/update/delete
    public $discountData;

    public function __construct($discount, $action)
    {
        // For deleted action, we don't store the model to avoid serialization issues
        if ($action === 'deleted') {
            $this->discount = null;
        } else {
            $this->discount = $discount;
        }
        
        $this->action = $action;
        
        // Prepare discount data for broadcasting
        $this->discountData = [
            'id' => $discount->id,
            'code' => $discount->code,
            'name' => $discount->name,
            'discount_type' => $discount->discount_type,
            'discount_value' => $discount->discount_value,
            'min_requirement_type' => $discount->min_requirement_type,
            'min_requirement_value' => $discount->min_requirement_value,
            'applicable_scope' => $discount->applicable_scope,
            'applicable_items' => $discount->applicable_items,
            'usage_type' => $discount->usage_type,
            'is_active' => $discount->is_active,
            'is_featured' => $discount->is_featured,
            'start_date' => $discount->start_date,
            'end_date' => $discount->end_date,
            'valid_days_of_week' => $discount->valid_days_of_week,
            'valid_from_time' => $discount->valid_from_time,
            'valid_to_time' => $discount->valid_to_time,
            'max_total_usage' => $discount->max_total_usage,
            'max_usage_per_user' => $discount->max_usage_per_user,
            'affected_products' => $this->getAffectedProducts($discount),
            'timestamp' => now()->toISOString()
        ];
    }

    public function broadcastOn(): Channel
    {
        return new Channel('discounts'); // public channel
    }

    public function broadcastAs()
    {
        return 'discount-updated';
    }

    /**
     * Get the list of product IDs affected by this discount code
     */
    private function getAffectedProducts($discount)
    {
        $affectedProducts = [];
        
        // If this is a delete action, we don't need to get affected products
        // because the discount is being removed, not updated
        if ($this->action === 'deleted') {
            return [];
        }
        
        try {
            switch ($discount->applicable_items) {
                case 'all_items':
                case 'all_products':
                    // Get all active product IDs
                    $affectedProducts = \App\Models\Product::where('status', 'selling')
                        ->pluck('id')
                        ->toArray();
                    break;
                    
                case 'specific_products':
                    // Get specific product IDs from discount_code_products table
                    $affectedProducts = $discount->specificProducts()
                        ->pluck('product_id')
                        ->filter()
                        ->toArray();
                    break;
                    
                case 'specific_categories':
                    // Get product IDs from specific categories
                    $categoryIds = $discount->specificCategories()
                        ->pluck('category_id')
                        ->filter()
                        ->toArray();
                        
                    if (!empty($categoryIds)) {
                        $affectedProducts = \App\Models\Product::whereIn('category_id', $categoryIds)
                            ->where('status', 'selling')
                            ->pluck('id')
                            ->toArray();
                    }
                    break;
                    
                case 'specific_variants':
                    // Get product IDs from specific variants
                    $variantIds = $discount->specificVariants()
                        ->pluck('product_variant_id')
                        ->filter()
                        ->toArray();
                        
                    if (!empty($variantIds)) {
                        $affectedProducts = \App\Models\ProductVariant::whereIn('id', $variantIds)
                            ->with('product')
                            ->get()
                            ->pluck('product.id')
                            ->filter()
                            ->unique()
                            ->toArray();
                    }
                    break;
                    
                default:
                    // For other types like combos, we don't affect product cards
                    $affectedProducts = [];
                    break;
            }
        } catch (\Exception $e) {
            // If there's an error getting affected products (e.g., relationships deleted),
            // return empty array to avoid breaking the event
            Log::warning('Error getting affected products for discount: ' . $e->getMessage());
            return [];
        }
        
        return array_values(array_unique($affectedProducts));
    }
}

