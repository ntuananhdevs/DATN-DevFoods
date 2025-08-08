@php
    use Illuminate\Support\Facades\Storage;
@endphp

@extends('layouts.branch.contentLayoutMaster')

@section('title', 'Chi tiết sản phẩm - ' . $product->name)

@section('content')
<style>
    /* Base layout */
    .page-container {
        min-height: 100vh;
        background-color: #f9fafb;
        padding: 1.5rem 0;
    }
    
    .container-fluid {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1.5rem;
    }
    
    /* Main content card */
    .main-content-card {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }
    
    .main-content-inner {
        padding: 2rem;
    }
    
    .product-layout {
        display: flex;
        gap: 2rem;
    }
    
    /* Image gallery section */
    .image-section {
        flex-shrink: 0;
        width: 320px;
    }
    
    .image-gallery {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    
    .main-image-container {
        width: 320px;
        height: 320px;
        border-radius: 0.5rem;
        overflow: hidden;
        background-color: #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .main-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        cursor: pointer;
    }
    
    .thumbnail-gallery {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.5rem;
        width: 320px;
    }
    
    .thumbnail {
        width: 100%;
        aspect-ratio: 1;
        border-radius: 0.5rem;
        overflow: hidden;
        background-color: #f3f4f6;
        border: 2px solid #d1d5db;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .thumbnail:hover {
        border-color: #9ca3af;
    }
    
    .thumbnail-active {
        border-color: #ef4444 !important;
        box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.2);
    }
    
    .thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    /* Product info section */
    .product-info {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }
    
    .product-header {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }
    
    .product-title {
        font-size: 1.875rem;
        font-weight: 700;
        color: #111827;
        margin: 0;
        line-height: 1.2;
    }
    
    .product-price {
        font-size: 2.25rem;
        font-weight: 700;
        color: #dc2626;
        margin: 0;
    }
    
    .product-details {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        font-size: 0.875rem;
    }
    
    .detail-item {
        display: flex;
        flex-direction: column;
    }
    
    .detail-label {
        font-weight: 500;
        color: #6b7280;
        margin-bottom: 0.25rem;
    }
    
    .detail-value {
        color: #111827;
        font-weight: 500;
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.125rem 0.625rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
        width: fit-content;
    }
    
    .status-active {
        background-color: #dcfce7;
        color: #166534;
    }
    
    .status-warning {
        background-color: #fef3c7;
        color: #92400e;
    }
    
    .status-inactive {
        background-color: #f3f4f6;
        color: #4b5563;
    }
    
    .product-description {
        padding-top: 1.5rem;
        border-top: 1px solid #e5e7eb;
    }
    
    .description-title {
        font-weight: 600;
        color: #111827;
        margin-bottom: 0.5rem;
        font-size: 1rem;
    }
    
    .description-text {
        color: #6b7280;
        line-height: 1.6;
        margin: 0;
    }
    
    .section-divider {
        border: none;
        height: 1px;
        background-color: #e5e7eb;
        margin: 1.5rem 0;
    }
    
    /* Toppings section */
    .toppings-section {
        margin-top: 1.5rem;
    }
    
    .section-title {
        font-weight: 600;
        color: #111827;
        margin-bottom: 0.75rem;
        font-size: 1.125rem;
    }
    
    .toppings-list {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .topping-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0.75rem;
        background-color: #f9fafb;
        border-radius: 0.5rem;
        transition: background-color 0.2s;
    }
    
    .topping-item:hover {
        background-color: #f3f4f6;
    }
    
    .topping-name {
        color: #6b7280;
    }
    
    .topping-price {
        font-weight: 500;
        color: #111827;
    }
    
    /* Variants section */
    .variants-section {
        margin-top: 1.5rem;
    }
    
    .variants-list {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .variants-header {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: #6b7280;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .variant-item {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        padding: 0.75rem;
        background-color: #f9fafb;
        border-radius: 0.5rem;
        transition: background-color 0.2s;
        font-size: 0.875rem;
    }
    
    .variant-item:hover {
        background-color: #f3f4f6;
    }
    
    .variant-name {
        color: #6b7280;
        font-weight: 500;
    }
    
    .variant-price {
        font-weight: 600;
        color: #111827;
    }
    
    /* Page header */
    .page-header {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem 2rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        border: 1px solid #e5e7eb;
    }
    
    .page-title {
        color: #111827;
        font-weight: 700;
        margin: 0;
        font-size: 1.5rem;
    }
    
    .btn-back {
        background: linear-gradient(135deg, #fb923c 0%, #f97316 100%);
        border: 1px solid #fb923c;
        border-radius: 0.5rem;
        padding: 0.5rem 1rem;
        color: white;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 2px 4px rgba(251, 146, 60, 0.2);
    }
    
    /* Modal styles */
    .image-modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.9);
        backdrop-filter: blur(5px);
    }
    
    .modal-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        max-width: 90%;
        max-height: 90%;
        border-radius: 0.75rem;
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    }
    
    .modal-content img {
        width: 100%;
        height: auto;
        display: block;
    }
    
    .close-modal {
        position: absolute;
        top: 15px;
        right: 20px;
        color: white;
        font-size: 2rem;
        font-weight: bold;
        cursor: pointer;
        z-index: 10000;
        background: rgba(0, 0, 0, 0.5);
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .modal-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(0, 0, 0, 0.5);
        color: white;
        border: none;
        font-size: 1.5rem;
        padding: 10px 15px;
        cursor: pointer;
        border-radius: 0.375rem;
        z-index: 10000;
    }
    
    .modal-prev {
        left: 20px;
    }
    
    .modal-next {
        right: 20px;
    }
    
    /* Responsive design */
    @media (max-width: 768px) {
        .product-layout {
            flex-direction: column;
            gap: 1.5rem;
        }
        
        .image-section {
            width: 100%;
        }
        
        .main-image-container {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }
        
        .thumbnail-gallery {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }
        
        .product-title {
            font-size: 1.5rem;
        }
        
        .product-price {
            font-size: 1.875rem;
        }
        
        .product-details {
            grid-template-columns: 1fr;
        }
        
        .main-content-inner {
            padding: 1.5rem;
        }
        
        .page-container {
            padding: 1rem 0;
        }
        
        .page-header {
            padding: 1rem 1.5rem;
        }
    }
</style>
<div class="page-container">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="page-title">Chi tiết sản phẩm</h1>
                <a href="{{ route('branch.products') }}" class="btn-back">
                    <i class="fas fa-arrow-left"></i>
                    Quay lại
                </a>
            </div>
        </div>

        <!-- Main Content Card -->
        <div class="main-content-card">
            <div class="main-content-inner">
                <div class="product-layout">
                    <!-- Image Gallery Section -->
                    <div class="image-section">
                        @if($product->images->count() > 0)
                            <div class="image-gallery">
                                <div class="main-image-container">
                                    <img id="mainImage" 
                                         src="{{ Storage::disk('s3')->url($product->images->first()->img) }}" 
                                         class="main-image" 
                                         alt="{{ $product->name }}"
                                         onclick="openImageModal(0)">
                                </div>
                                
                                <!-- Thumbnail Gallery -->
                                @if($product->images->count() > 1)
                                    <div class="thumbnail-gallery">
                                        @foreach($product->images as $index => $image)
                                            <div class="thumbnail {{ $index == 0 ? 'thumbnail-active' : '' }}" 
                                                 onclick="changeMainImage('{{ Storage::disk('s3')->url($image->img) }}', {{ $index }})">
                                                <img src="{{ Storage::disk('s3')->url($image->img) }}" 
                                                     alt="{{ $product->name }} - {{ $index + 1 }}">
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Image Modal -->
                            <div id="imageModal" class="image-modal" onclick="closeImageModal()">
                                <span class="close-modal" onclick="closeImageModal()">&times;</span>
                                @if($product->images->count() > 1)
                                    <button class="modal-nav modal-prev" onclick="event.stopPropagation(); prevImage()">
                                        <i class="fas fa-chevron-left"></i>
                                    </button>
                                    <button class="modal-nav modal-next" onclick="event.stopPropagation(); nextImage()">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                @endif
                                <div class="modal-content" onclick="event.stopPropagation()">
                                    <img id="modalImage" src="" alt="{{ $product->name }}">
                                </div>
                            </div>
                        @else
                            <div class="image-gallery">
                                <div class="main-image-container">
                                    <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #9ca3af;">
                                        <div style="text-align: center;">
                                            <i class="fas fa-image" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                                            <p>Chưa có hình ảnh</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Product Info Section -->
                    <div class="product-info">
                        <div class="product-header">
                            <h1 class="product-title">{{ $product->name }}</h1>
                            <div class="product-price">{{ number_format($product->base_price, 0, ',', '.') }}đ</div>
                        </div>
                        
                        <div class="product-details">
                            <div class="detail-item">
                                <span class="detail-label">Mã sản phẩm:</span>
                                <span class="detail-value">{{ $product->sku }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Danh mục:</span>
                                <span class="detail-value">{{ $product->category->name ?? 'Chưa phân loại' }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Trạng thái:</span>
                                <div class="detail-value">
                                    @if($product->status == 'selling')
                                        <span class="status-badge status-active">Đang bán</span>
                                    @elseif($product->status == 'out_of_stock')
                                        <span class="status-badge status-warning">Hết hàng</span>
                                    @else
                                        <span class="status-badge status-inactive">Ngừng bán</span>
                                    @endif
                                </div>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Tồn kho:</span>
                                <span class="detail-value">
                                    @php
                                        $stock = $product->branchStocks ? $product->branchStocks->first() : null;
                                    @endphp
                                    @if($stock)
                                        Còn hàng
                                    @else
                                        Chưa có thông tin
                                    @endif
                                </span>
                            </div>
                        </div>
                        
                        @if($product->description)
                            <div class="product-description">
                                <h4 class="description-title">Mô tả:</h4>
                                <p class="description-text">{{ $product->description }}</p>
                            </div>
                        @endif
                        
                        <!-- Toppings Section -->
                        @if($product->toppings->count() > 0)
                            <hr class="section-divider">
                            <div class="toppings-section">
                                <h3 class="section-title">Topping có thể thêm:</h3>
                                
                                <div class="toppings-list">
                                    @foreach($product->toppings as $topping)
                                        <div class="topping-item">
                                            <span class="topping-name">{{ $topping->name }}</span>
                                            <span class="topping-price">+{{ number_format($topping->price, 0, ',', '.') }}đ</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Variants -->
        @if($product->variants && $product->variants->count() > 0)
            <div class="main-content-card" style="margin-top: 1.5rem;">
                <div class="main-content-inner">
                    <div class="variants-section">
                        <h3 class="section-title">Biến thể sản phẩm</h3>
                        
                        <div class="variants-list">
                            <div class="variants-header">
                                <div>Tên biến thể</div>
                                <div>Giá</div>
                                <div>Số lượng</div>
                            </div>
                            
                            @foreach($product->variants as $variant)
                                @php
                                    $branchStock = $variant->branchStocks->first();
                                    $quantity = $branchStock ? $branchStock->stock_quantity : 0;
                                @endphp
                                <div class="variant-item">
                                    <div class="variant-name">{{ $variant->variant_description ?? 'Mặc định' }}</div>
                                    <div class="variant-price">{{ number_format($variant->price, 0, ',', '.') }}đ</div>
                                    <div>
                                        @if($quantity > 0)
                                            <span class="status-badge status-active">{{ $quantity }}</span>
                                        @else
                                            <span class="status-badge status-warning">Hết hàng</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
// Image gallery functionality
let currentImageIndex = 0;
const images = [
    @foreach($product->images as $index => $image)
        '{{ Storage::disk('s3')->url($image->img) }}'{{ !$loop->last ? ',' : '' }}
    @endforeach
];

// Change main image when clicking thumbnail
function changeMainImage(imageSrc, index) {
    const mainImage = document.getElementById('mainImage');
    const thumbnails = document.querySelectorAll('.thumbnail');
    
    // Update main image
    mainImage.src = imageSrc;
    currentImageIndex = index;
    
    // Update active thumbnail
    thumbnails.forEach((thumb, i) => {
        if (i === index) {
            thumb.classList.add('thumbnail-active');
        } else {
            thumb.classList.remove('thumbnail-active');
        }
    });
}

// Open image modal
function openImageModal(index = null) {
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    
    if (index !== null) {
        currentImageIndex = index;
    }
    
    modalImage.src = images[currentImageIndex];
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
}

// Close image modal
function closeImageModal() {
    const modal = document.getElementById('imageModal');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Navigate to previous image in modal
function prevImage() {
    currentImageIndex = (currentImageIndex - 1 + images.length) % images.length;
    const modalImage = document.getElementById('modalImage');
    modalImage.src = images[currentImageIndex];
    
    // Update main image and thumbnail
    const mainImage = document.getElementById('mainImage');
    mainImage.src = images[currentImageIndex];
    updateActiveThumbnail(currentImageIndex);
}

// Navigate to next image in modal
function nextImage() {
    currentImageIndex = (currentImageIndex + 1) % images.length;
    const modalImage = document.getElementById('modalImage');
    modalImage.src = images[currentImageIndex];
    
    // Update main image and thumbnail
    const mainImage = document.getElementById('mainImage');
    mainImage.src = images[currentImageIndex];
    updateActiveThumbnail(currentImageIndex);
}

// Update active thumbnail
function updateActiveThumbnail(index) {
    const thumbnails = document.querySelectorAll('.thumbnail');
    thumbnails.forEach((thumb, i) => {
        if (i === index) {
            thumb.classList.add('thumbnail-active');
        } else {
            thumb.classList.remove('thumbnail-active');
        }
    });
}

// Keyboard navigation
document.addEventListener('keydown', function(e) {
    const modal = document.getElementById('imageModal');
    if (modal.style.display === 'block') {
        if (e.key === 'Escape') {
            closeImageModal();
        } else if (e.key === 'ArrowLeft') {
            prevImage();
        } else if (e.key === 'ArrowRight') {
            nextImage();
        }
    }
});

// Prevent modal close when clicking on image
document.addEventListener('DOMContentLoaded', function() {
    const modalContent = document.querySelector('.modal-content');
    if (modalContent) {
        modalContent.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
});
</script>

@endsection