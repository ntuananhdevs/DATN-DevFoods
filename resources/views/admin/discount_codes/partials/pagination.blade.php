@if($discountCodes->hasPages())
<div class="flex items-center justify-between px-4 py-4 border-t">
    <div class="text-sm text-muted-foreground">
        Hiển thị {{ $discountCodes->firstItem() }} đến {{ $discountCodes->lastItem() }} của {{ $discountCodes->total() }} mục
    </div>
    <div class="flex items-center space-x-2">
        {{ $discountCodes->links() }}
    </div>
</div>
@endif 