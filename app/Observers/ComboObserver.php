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
            // Không tự động update trạng thái active nữa, chỉ log lại nếu muốn
            Log::info('New combo created with zero quantity, no auto deactivate', [
                'combo_id' => $combo->id,
                'combo_name' => $combo->name
            ]);
        }
    }
}
