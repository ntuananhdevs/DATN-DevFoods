@extends('layouts/admin/contentLayoutMaster')

@section('title', 'Cài đặt chung')

@section('content')
<div class="fade-in flex flex-col gap-4 pb-4">
    <!-- Main Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="flex aspect-square w-10 h-10 items-center justify-center rounded-lg bg-primary text-primary-foreground">
                <i class="fas fa-cogs text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-3xl font-bold tracking-tight">Cài đặt chung</h2>
                <p class="text-muted-foreground">Quản lý các cài đặt hệ thống</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" 
                class="btn btn-primary flex items-center"
                onclick="openAddModal()">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Thêm cài đặt
            </button>
        </div>
    </div>

    <!-- Content Card -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên cài đặt</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giá trị</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mô tả</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày tạo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($settings as $setting)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-semibold text-gray-900">{{ $setting->key }}</span>
                                @if(in_array($setting->key, ['tax_rate', 'free_shipping_threshold']))
                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Cố định</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="editable-value text-gray-900" data-id="{{ $setting->id }}" data-field="value">
                                    {{ $setting->value }}
                                </span>
                                @if($setting->key === 'tax_rate')
                                    <small class="text-gray-500 ml-1">%</small>
                                @elseif($setting->key === 'free_shipping_threshold' || $setting->key === 'shipping_fee')
                                    <small class="text-gray-500 ml-1">VND</small>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="editable-value text-gray-900" data-id="{{ $setting->id }}" data-field="description">
                                    {{ $setting->description ?? 'Không có mô tả' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $setting->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button type="button" class="inline-flex items-center px-3 py-1 border border-blue-300 text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-md text-sm mr-2 transition duration-150 ease-in-out edit-btn" 
                                        data-id="{{ $setting->id }}" 
                                        data-key="{{ $setting->key }}"
                                        data-value="{{ addslashes($setting->value) }}"
                                        data-description="{{ addslashes($setting->description) }}"
                                        data-fixed="{{ in_array($setting->key, ['tax_rate', 'free_shipping_threshold']) ? 'true' : 'false' }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                @if(!in_array($setting->key, ['tax_rate', 'free_shipping_threshold']))
                                <button type="button" class="inline-flex items-center px-3 py-1 border border-red-300 text-red-700 bg-red-50 hover:bg-red-100 rounded-md text-sm transition duration-150 ease-in-out delete-btn" 
                                        data-id="{{ $setting->id }}" 
                                        data-key="{{ $setting->key }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Setting Modal -->
<div id="dtmodalInfoModal" class="dtmodal-overlay">
    <div class="dtmodal-container dtmodal-info">
        <div class="dtmodal-header">
            <div class="dtmodal-icon-wrapper">
                <div class="dtmodal-icon">
                    <i class="fas fa-plus"></i>
                </div>
            </div>
            <div class="dtmodal-title-content">
                <h3 class="dtmodal-title">Thêm cài đặt mới</h3>
            </div>
            <button class="dtmodal-close" onclick="dtmodalCloseModal('dtmodalInfoModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="dtmodal-body">
            <form id="addSettingForm">
                <div class="space-y-4">
                    <div>
                        <label for="add_key" class="block text-sm font-medium text-gray-700 mb-2">Tên cài đặt <span class="text-red-500">*</span></label>
                        <input type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" id="add_key" name="key">
                        <div class="invalid-feedback text-red-500 text-sm mt-1 hidden"></div>
                    </div>
                    <div>
                        <label for="add_value" class="block text-sm font-medium text-gray-700 mb-2">Giá trị <span class="text-red-500">*</span></label>
                        <input type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" id="add_value" name="value">
                        <div class="invalid-feedback text-red-500 text-sm mt-1 hidden"></div>
                    </div>
                    <div>
                        <label for="add_description" class="block text-sm font-medium text-gray-700">Mô tả</label>
                        <textarea class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" id="add_description" name="description" rows="3"></textarea>
                        <div class="invalid-feedback text-red-500 text-sm mt-1 hidden"></div>
                    </div>
                </div>
            </form>
        </div>
        <div class="dtmodal-footer">
            <button class="dtmodal-btn dtmodal-btn-outline" onclick="dtmodalCloseModal('dtmodalInfoModal')">Hủy</button>
            <button class="dtmodal-btn dtmodal-btn-primary" onclick="submitAddForm()">Thêm cài đặt</button>
        </div>
    </div>
</div>

<!-- Edit Setting Modal -->
<div id="dtmodalWarningModal" class="dtmodal-overlay">
    <div class="dtmodal-container dtmodal-warning">
        <div class="dtmodal-header">
            <div class="dtmodal-icon-wrapper">
                <div class="dtmodal-icon">
                    <i class="fas fa-edit"></i>
                </div>
            </div>
            <div class="dtmodal-title-content">
                <h3 class="dtmodal-title">Chỉnh sửa cài đặt</h3>
            </div>
            <button class="dtmodal-close" onclick="dtmodalCloseModal('dtmodalWarningModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="dtmodal-body">
            <form id="editSettingForm">
                <input type="hidden" id="edit_id" name="id">
                <div class="space-y-4">
                    <div>
                        <label for="edit_key" class="block text-sm font-medium text-gray-700 mb-2">Tên cài đặt <span class="text-red-500">*</span></label>
                        <input type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" id="edit_key" name="key">
                        <div class="invalid-feedback text-red-500 text-sm mt-1 hidden"></div>
                    </div>
                    <div>
                        <label for="edit_value" class="block text-sm font-medium text-gray-700 mb-2">Giá trị <span class="text-red-500">*</span></label>
                        <input type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" id="edit_value" name="value">
                        <div class="invalid-feedback text-red-500 text-sm mt-1 hidden"></div>
                    </div>
                    <div>
                        <label for="edit_description" class="block text-sm font-medium text-gray-700">Mô tả</label>
                        <textarea class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" id="edit_description" name="description" rows="3"></textarea>
                        <div class="invalid-feedback text-red-500 text-sm mt-1 hidden"></div>
                    </div>
                </div>
            </form>
        </div>
        <div class="dtmodal-footer">
            <button class="dtmodal-btn dtmodal-btn-outline" onclick="dtmodalCloseModal('dtmodalWarningModal')">Hủy</button>
            <button class="dtmodal-btn dtmodal-btn-primary" onclick="submitEditForm()">Cập nhật</button>
        </div>
    </div>
</div>

<!-- Delete Setting Modal -->
<div id="dtmodalDangerModal" class="dtmodal-overlay">
    <div class="dtmodal-container dtmodal-danger">
        <div class="dtmodal-header">
            <div class="dtmodal-icon-wrapper">
                <div class="dtmodal-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
            <div class="dtmodal-title-content">
                <h3 class="dtmodal-title">Xác nhận xóa</h3>
            </div>
            <button class="dtmodal-close" onclick="dtmodalCloseModal('dtmodalDangerModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="dtmodal-body">
            <p class="text-gray-600">Bạn có chắc chắn muốn xóa cài đặt "<span id="delete_key_name" class="font-semibold"></span>"?</p>
            <p class="text-sm text-red-600 mt-2">Hành động này không thể hoàn tác.</p>
            <input type="hidden" id="delete_id">
        </div>
        <div class="dtmodal-footer">
            <button class="dtmodal-btn dtmodal-btn-outline" onclick="dtmodalCloseModal('dtmodalDangerModal')">Hủy</button>
            <button class="dtmodal-btn dtmodal-btn-danger" onclick="submitDeleteForm()">Xóa</button>
        </div>
    </div>
</div>
</div>
@endsection

@push('scripts')
<script>
// Modal functions
function openAddModal() {
    clearErrors('add');
    dtmodalShowModal('info');
}

function submitAddForm() {
    const formData = {
        key: $('#add_key').val(),
        value: $('#add_value').val(),
        description: $('#add_description').val()
    };

    // Client-side validation
    if (!formData.key.trim()) {
        showError('add_key', 'Tên cài đặt là bắt buộc');
        return;
    }
    if (!formData.value.trim()) {
        showError('add_value', 'Giá trị là bắt buộc');
        return;
    }

    $.ajax({
        url: '{{ route("admin.general_settings.store") }}',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: formData,
        success: function(response) {
            if (response.success) {
                dtmodalCloseModal('dtmodalInfoModal');
                setTimeout(() => location.reload(), 500);
            }
        },
        error: function(xhr) {
            const errors = xhr.responseJSON?.errors;
            clearErrors('add');
            
            if (errors) {
                Object.keys(errors).forEach(function(key) {
                    showError(`add_${key}`, errors[key][0]);
                });
            }
        }
    });
}

function submitDeleteForm() {
    const id = $('#delete_id').val();

    $.ajax({
        url: `/admin/general-settings/${id}`,
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                dtmodalCloseModal('dtmodalDangerModal');
                setTimeout(() => location.reload(), 500);
            }
        },
        error: function(xhr) {
            console.error('Error deleting setting:', xhr);
        }
    });
}

function submitEditForm() {
    const id = $('#edit_id').val();
    const formData = {
        key: $('#edit_key').val(),
        value: $('#edit_value').val(),
        description: $('#edit_description').val(),
        _method: 'PUT'
    };

    // Client-side validation
    if (!formData.key.trim()) {
        showError('edit_key', 'Tên cài đặt là bắt buộc');
        return;
    }
    if (!formData.value.trim()) {
        showError('edit_value', 'Giá trị là bắt buộc');
        return;
    }

    $.ajax({
        url: `/admin/general-settings/${id}`,
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: formData,
        success: function(response) {
            if (response.success) {
                dtmodalCloseModal('dtmodalWarningModal');
                setTimeout(() => location.reload(), 500);
            }
        },
        error: function(xhr) {
            const errors = xhr.responseJSON?.errors;
            clearErrors('edit');
            
            if (errors) {
                Object.keys(errors).forEach(function(key) {
                    showError(`edit_${key}`, errors[key][0]);
                });
            }
        }
    });
}

function clearErrors(prefix) {
    document.querySelectorAll('.invalid-feedback').forEach(function(el) {
        el.classList.add('hidden');
        el.textContent = '';
    });
    document.querySelectorAll('input, textarea').forEach(function(el) {
        el.classList.remove('border-red-500');
        el.classList.add('border-gray-300');
    });
}

function showError(fieldId, message) {
    const field = document.getElementById(fieldId);
    const feedback = field.parentNode.querySelector('.invalid-feedback');
    
    field.classList.remove('border-gray-300');
    field.classList.add('border-red-500');
    feedback.classList.remove('hidden');
    feedback.textContent = message;
}

$(document).ready(function() {
    // Edit Setting Button
    $('.edit-btn').on('click', function() {
        const id = $(this).data('id');
        const key = $(this).data('key');
        const value = $(this).data('value');
        const description = $(this).data('description');
        const isFixed = $(this).data('fixed') === 'true';

        $('#edit_id').val(id);
        $('#edit_key').val(key);
        $('#edit_value').val(value);
        $('#edit_description').val(description);
        
        // Disable key field for fixed settings
        if (isFixed) {
            $('#edit_key').prop('readonly', true).addClass('bg-gray-100');
        } else {
            $('#edit_key').prop('readonly', false).removeClass('bg-gray-100');
        }

        clearErrors('edit');
        dtmodalShowModal('warning');
    });

    // Delete Setting
    $('.delete-btn').on('click', function() {
        const id = $(this).data('id');
        const key = $(this).data('key');
        
        // Set delete data for modal
        $('#delete_id').val(id);
        $('#delete_key_name').text(key);
        
        // Show delete confirmation modal
        dtmodalShowModal('danger');
    });

    // Close modal when clicking outside
    window.onclick = function(event) {
        const addModal = document.getElementById('addSettingModal');
        const editModal = document.getElementById('editSettingModal');
        
        if (event.target === addModal) {
            closeAddModal();
        }
        if (event.target === editModal) {
            closeEditModal();
        }
    };
});

</script>
<script src="{{ asset('js/modal.js') }}"></script>
@endpush