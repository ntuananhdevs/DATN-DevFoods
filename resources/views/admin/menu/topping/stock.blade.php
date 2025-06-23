@extends('layouts/admin/contentLayoutMaster')

@section('title', 'Quản lý tồn kho - ' . $topping->name)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Quản lý tồn kho</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.toppings.index') }}">Toppings</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.toppings.show', $topping->id) }}">{{ $topping->name }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tồn kho</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.toppings.show', $topping->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <!-- Topping Info Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Thông tin Topping</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-2">
                    @if($topping->image)
                        <img src="{{ asset('storage/' . $topping->image) }}" alt="{{ $topping->name }}" class="img-fluid rounded">
                    @else
                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 100px;">
                            <i class="fas fa-image text-muted fa-2x"></i>
                        </div>
                    @endif
                </div>
                <div class="col-md-10">
                    <h5 class="font-weight-bold">{{ $topping->name }}</h5>
                    <p class="text-muted mb-2">{{ $topping->description }}</p>
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Giá:</strong> {{ number_format($topping->price) }}đ
                        </div>
                        <div class="col-md-3">
                            <strong>Trạng thái:</strong>
                            <span class="badge badge-{{ $topping->status === 'active' ? 'success' : 'secondary' }}">
                                {{ $topping->status === 'active' ? 'Hoạt động' : 'Không hoạt động' }}
                            </span>
                        </div>
                        <div class="col-md-3">
                            <strong>Tổng tồn kho:</strong>
                            <span class="badge badge-info">{{ $topping->toppingStocks->sum('quantity') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Management Form -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Cập nhật tồn kho theo chi nhánh</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.toppings.update-stock', $topping->id) }}" method="POST">
                @csrf
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>Chi nhánh</th>
                                <th>Địa chỉ</th>
                                <th>Tồn kho hiện tại</th>
                                <th>Tồn kho mới</th>
                                <th>Thay đổi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($branches as $branch)
                                @php
                                    $currentStock = $topping->toppingStocks->where('branch_id', $branch->id)->first();
                                    $currentQuantity = $currentStock ? $currentStock->quantity : 0;
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $branch->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $branch->phone }}</small>
                                    </td>
                                    <td>
                                        <small>{{ $branch->address }}</small>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $currentQuantity > 0 ? 'success' : 'warning' }}">
                                            {{ $currentQuantity }}
                                        </span>
                                    </td>
                                    <td>
                                        <input type="hidden" name="stocks[{{ $loop->index }}][branch_id]" value="{{ $branch->id }}">
                                        <input type="number" 
                                               name="stocks[{{ $loop->index }}][quantity]" 
                                               value="{{ $currentQuantity }}" 
                                               min="0" 
                                               class="form-control stock-input" 
                                               data-current="{{ $currentQuantity }}"
                                               style="width: 120px;">
                                    </td>
                                    <td>
                                        <span class="change-indicator" data-index="{{ $loop->index }}">-</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Lưu ý:</strong> Việc cập nhật tồn kho sẽ ảnh hưởng đến khả năng bán hàng tại các chi nhánh.
                        </div>
                    </div>
                    <div class="col-md-6 text-right">
                        <button type="button" class="btn btn-secondary mr-2" onclick="resetForm()">
                            <i class="fas fa-undo"></i> Đặt lại
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Cập nhật tồn kho
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Calculate change when stock input changes
    $('.stock-input').on('input', function() {
        const index = $(this).data('current');
        const currentValue = parseInt($(this).data('current'));
        const newValue = parseInt($(this).val()) || 0;
        const change = newValue - currentValue;
        
        const indicator = $(this).closest('tr').find('.change-indicator');
        
        if (change > 0) {
            indicator.html(`<span class="text-success">+${change}</span>`);
        } else if (change < 0) {
            indicator.html(`<span class="text-danger">${change}</span>`);
        } else {
            indicator.html('-');
        }
    });
    
    // Reset form function
    window.resetForm = function() {
        $('.stock-input').each(function() {
            const currentValue = $(this).data('current');
            $(this).val(currentValue);
        });
        $('.change-indicator').html('-');
    };
    
    // Form validation
    $('form').on('submit', function(e) {
        let hasChanges = false;
        $('.stock-input').each(function() {
            const currentValue = parseInt($(this).data('current'));
            const newValue = parseInt($(this).val()) || 0;
            if (currentValue !== newValue) {
                hasChanges = true;
            }
        });
        
        if (!hasChanges) {
            e.preventDefault();
            Swal.fire({
                icon: 'info',
                title: 'Không có thay đổi',
                text: 'Bạn chưa thay đổi số lượng tồn kho nào.',
                confirmButtonText: 'OK'
            });
            return false;
        }
        
        // Show confirmation
        e.preventDefault();
        Swal.fire({
            title: 'Xác nhận cập nhật',
            text: 'Bạn có chắc chắn muốn cập nhật tồn kho cho topping này?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Cập nhật',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });
});
</script>
@endpush
@endsection