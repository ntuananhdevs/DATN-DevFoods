<?php

namespace App\Observers;

use App\Models\Combo;
use Illuminate\Support\Facades\Log;

class ComboObserver
{
    /**
     * Handle the Combo "updating" event.
     */
    public function updating(Combo $combo): void
    {
        // Kiểm tra nếu quantity đang được cập nhật
        if ($combo->isDirty('quantity')) {
            $newQuantity = $combo->quantity;
            $oldQuantity = $combo->getOriginal('quantity');
            
            Log::info('Combo quantity updating', [
                'combo_id' => $combo->id,
                'combo_name' => $combo->name,
                'old_quantity' => $oldQuantity,
                'new_quantity' => $newQuantity
            ]);
            
            // Nếu số lượng về 0, tự động đặt trạng thái thành không hoạt động
            if ($newQuantity <= 0) {
                $combo->active = false;
                Log::info('Combo automatically deactivated due to zero quantity', [
                    'combo_id' => $combo->id,
                    'combo_name' => $combo->name
                ]);
            }
            // Nếu số lượng > 0 và combo đang không hoạt động, có thể tự động kích hoạt lại
            elseif ($newQuantity > 0 && !$combo->active && $oldQuantity <= 0) {
                $combo->active = true;
                Log::info('Combo automatically reactivated due to positive quantity', [
                    'combo_id' => $combo->id,
                    'combo_name' => $combo->name
                ]);
            }
        }
    }

    /**
     * Handle the Combo "updated" event.
     */
    public function updated(Combo $combo): void
    {
        // Log thông tin sau khi cập nhật
        if ($combo->wasChanged('quantity') || $combo->wasChanged('active')) {
            Log::info('Combo updated', [
                'combo_id' => $combo->id,
                'combo_name' => $combo->name,
                'quantity' => $combo->quantity,
                'active' => $combo->active,
                'changes' => $combo->getChanges()
            ]);
        }
    }

    /**
     * Handle the Combo "created" event.
     */
    public function created(Combo $combo): void
    {
        // Kiểm tra trạng thái khi tạo mới
        if ($combo->quantity <= 0) {
            $combo->update(['active' => false]);
            Log::info('New combo created with zero quantity, automatically deactivated', [
                'combo_id' => $combo->id,
                'combo_name' => $combo->name
            ]);
        }
    }
}