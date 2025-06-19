@extends('layouts/admin/contentLayoutMaster')

@section('title', 'Chỉnh Sửa Topping')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Chỉnh Sửa Topping: {{ $topping->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.toppings.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>

                <form action="{{ route('admin.toppings.update', $topping) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <!-- Tên topping -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Tên Topping <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', $topping->name) }}" 
                                           placeholder="Nhập tên topping"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Giá -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="price">Giá <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" 
                                               class="form-control @error('price') is-invalid @enderror" 
                                               id="price" 
                                               name="price" 
                                               value="{{ old('price', $topping->price) }}" 
                                               placeholder="0"
                                               min="0"
                                               step="1000"
                                               required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">VNĐ</span>
                                        </div>
                                        @error('price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Mô tả -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description">Mô tả</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" 
                                              name="description" 
                                              rows="3" 
                                              placeholder="Nhập mô tả topping">{{ old('description', $topping->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Hình ảnh hiện tại -->
                            <div class="col-md-6">
                                @if($topping->image)
                                    <div class="form-group">
                                        <label>Hình ảnh hiện tại</label>
                                        <div class="current-image">
                                            <img src="{{ asset('storage/' . $topping->image) }}" 
                                                 alt="{{ $topping->name }}" 
                                                 class="img-thumbnail" 
                                                 style="max-width: 200px; max-height: 200px;">
                                            <div class="mt-2">
                                                <label class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" name="remove_image" value="1">
                                                    <span class="custom-control-label">Xóa hình ảnh này</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Upload hình ảnh mới -->
                                <div class="form-group">
                                    <label for="image">{{ $topping->image ? 'Thay đổi hình ảnh' : 'Hình ảnh' }}</label>
                                    <div class="custom-file">
                                        <input type="file" 
                                               class="custom-file-input @error('image') is-invalid @enderror" 
                                               id="image" 
                                               name="image" 
                                               accept="image/*">
                                        <label class="custom-file-label" for="image">Chọn hình ảnh...</label>
                                        @error('image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="form-text text-muted">
                                        Chấp nhận: JPG, PNG, GIF. Kích thước tối đa: 2MB
                                    </small>
                                </div>
                                
                                <!-- Preview image -->
                                <div id="image-preview" class="mt-2" style="display: none;">
                                    <img id="preview-img" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                </div>
                            </div>

                            <!-- Trạng thái -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Trạng thái</label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" 
                                               class="custom-control-input" 
                                               id="active" 
                                               name="active" 
                                               value="1" 
                                               {{ old('active', $topping->active) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="active">
                                            Kích hoạt
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Topping sẽ hiển thị cho khách hàng khi được kích hoạt
                                    </small>
                                </div>

                                <!-- Thông tin bổ sung -->
                                <div class="form-group">
                                    <label>Thông tin</label>
                                    <div class="info-box">
                                        <small class="text-muted">
                                            <strong>Ngày tạo:</strong> {{ $topping->created_at->format('d/m/Y H:i') }}<br>
                                            <strong>Cập nhật:</strong> {{ $topping->updated_at->format('d/m/Y H:i') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Cập nhật Topping
                        </button>
                        <a href="{{ route('admin.toppings.index') }}" class="btn btn-secondary ml-2">
                            <i class="fas fa-times"></i> Hủy
                        </a>
                        <button type="button" class="btn btn-danger ml-auto" data-toggle="modal" data-target="#deleteModal">
                            <i class="fas fa-trash"></i> Xóa Topping
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa topping "{{ $topping->name }}"? Hành động này không thể hoàn tác.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                <form action="{{ route('admin.toppings.destroy', $topping) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Custom file input label
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass('selected').html(fileName);
        
        // Preview image
        if (this.files && this.files[0]) {
            let reader = new FileReader();
            reader.onload = function(e) {
                $('#preview-img').attr('src', e.target.result);
                $('#image-preview').show();
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Format price input
    $('#price').on('input', function() {
        let value = $(this).val().replace(/[^0-9]/g, '');
        if (value) {
            $(this).val(parseInt(value));
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.custom-file-label.selected {
    color: #495057;
}

.img-thumbnail {
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
    padding: 0.25rem;
}

.info-box {
    background-color: #f8f9fa;
    padding: 10px;
    border-radius: 4px;
    border: 1px solid #e9ecef;
}

.current-image {
    text-align: center;
}
</style>
@endpush