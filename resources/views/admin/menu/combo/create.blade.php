@extends('layouts/admin/contentLayoutMaster')

@section('title', 'Thêm Combo Mới')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Thêm Combo Mới</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.combos.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>

                <form action="{{ route('admin.combos.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <!-- Tên combo -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Tên Combo <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name') }}" 
                                           placeholder="Nhập tên combo"
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
                                               value="{{ old('price') }}" 
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
                            <!-- Giá gốc -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="original_price">Giá gốc</label>
                                    <div class="input-group">
                                        <input type="number" 
                                               class="form-control @error('original_price') is-invalid @enderror" 
                                               id="original_price" 
                                               name="original_price" 
                                               value="{{ old('original_price') }}" 
                                               placeholder="0"
                                               min="0"
                                               step="1000">
                                        <div class="input-group-append">
                                            <span class="input-group-text">VNĐ</span>
                                        </div>
                                        @error('original_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="form-text text-muted">
                                        Giá gốc để tính % giảm giá (không bắt buộc)
                                    </small>
                                </div>
                            </div>

                            <!-- Danh mục -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="category_id">Danh mục</label>
                                    <select class="form-control @error('category_id') is-invalid @enderror" 
                                            id="category_id" 
                                            name="category_id">
                                        <option value="">Chọn danh mục</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" 
                                                    {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Mô tả ngắn -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="short_description">Mô tả ngắn</label>
                                    <textarea class="form-control @error('short_description') is-invalid @enderror" 
                                              id="short_description" 
                                              name="short_description" 
                                              rows="2" 
                                              placeholder="Nhập mô tả ngắn về combo">{{ old('short_description') }}</textarea>
                                    @error('short_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Mô tả chi tiết -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description">Mô tả chi tiết</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" 
                                              name="description" 
                                              rows="4" 
                                              placeholder="Nhập mô tả chi tiết về combo">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Hình ảnh -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="image">Hình ảnh chính</label>
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

                            <!-- Cài đặt -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Trạng thái</label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" 
                                               class="custom-control-input" 
                                               id="active" 
                                               name="active" 
                                               value="1" 
                                               {{ old('active', true) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="active">
                                            Kích hoạt
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Combo sẽ hiển thị cho khách hàng khi được kích hoạt
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label>Nổi bật</label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" 
                                               class="custom-control-input" 
                                               id="featured" 
                                               name="featured" 
                                               value="1" 
                                               {{ old('featured') ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="featured">
                                            Đánh dấu nổi bật
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Combo nổi bật sẽ hiển thị ở vị trí ưu tiên
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Chọn sản phẩm cho combo -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Sản phẩm trong combo <span class="text-danger">*</span></label>
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">Chọn sản phẩm</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row" id="products-selection">
                                                @foreach($products as $product)
                                                    <div class="col-md-4 mb-3">
                                                        <div class="card product-card" data-product-id="{{ $product->id }}">
                                                            <div class="card-body p-2">
                                                                <div class="custom-control custom-checkbox">
                                                                    <input type="checkbox" 
                                                                           class="custom-control-input product-checkbox" 
                                                                           id="product_{{ $product->id }}" 
                                                                           name="products[]" 
                                                                           value="{{ $product->id }}">
                                                                    <label class="custom-control-label" for="product_{{ $product->id }}">
                                                                        <strong>{{ $product->name }}</strong>
                                                                    </label>
                                                                </div>
                                                                @if($product->image)
                                                                    <img src="{{ asset('storage/' . $product->image) }}" 
                                                                         alt="{{ $product->name }}" 
                                                                         class="img-fluid mt-2" 
                                                                         style="max-height: 80px;">
                                                                @endif
                                                                <p class="text-muted small mt-1 mb-0">
                                                                    {{ number_format($product->price, 0, ',', '.') }} VNĐ
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            @error('products')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Lưu Combo
                        </button>
                        <a href="{{ route('admin.combos.index') }}" class="btn btn-secondary ml-2">
                            <i class="fas fa-times"></i> Hủy
                        </a>
                    </div>
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

    // Format price inputs
    $('#price, #original_price').on('input', function() {
        let value = $(this).val().replace(/[^0-9]/g, '');
        if (value) {
            $(this).val(parseInt(value));
        }
    });

    // Product selection styling
    $('.product-checkbox').on('change', function() {
        let card = $(this).closest('.product-card');
        if ($(this).is(':checked')) {
            card.addClass('selected');
        } else {
            card.removeClass('selected');
        }
    });

    // Calculate original price suggestion
    $('.product-checkbox').on('change', function() {
        let totalPrice = 0;
        $('.product-checkbox:checked').each(function() {
            let productCard = $(this).closest('.product-card');
            let priceText = productCard.find('.text-muted').text();
            let price = parseInt(priceText.replace(/[^0-9]/g, ''));
            totalPrice += price;
        });
        
        if (totalPrice > 0 && !$('#original_price').val()) {
            $('#original_price').val(totalPrice);
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

.product-card {
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
    cursor: pointer;
}

.product-card:hover {
    border-color: #007bff;
    box-shadow: 0 2px 4px rgba(0,123,255,0.25);
}

.product-card.selected {
    border-color: #28a745;
    background-color: #f8fff9;
    box-shadow: 0 2px 4px rgba(40,167,69,0.25);
}

.product-card img {
    object-fit: cover;
    border-radius: 4px;
}
</style>
@endpush