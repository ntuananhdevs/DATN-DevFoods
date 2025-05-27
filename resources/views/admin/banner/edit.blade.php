@extends('layouts/admin/contentLayoutMaster')
@section('title', 'Chỉnh sửa banner')
@section('content')
    <div class="container mx-auto p-4 sm:p-6 lg:p-8 bg-white dark:bg-gray-800 shadow-xl rounded-lg">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6 pb-3 border-b border-gray-200 dark:border-gray-700">
            Sửa Banner</h1>
            
        @if (session('duplicate_order'))
        <div class="mb-6 p-4 border-l-4 border-yellow-500 bg-yellow-100 dark:bg-yellow-900 dark:border-yellow-600 text-yellow-700 dark:text-yellow-200">
            <h3 class="font-bold">Cảnh báo: Trùng thứ tự hiển thị</h3>
            <p class="mb-2">Thứ tự hiển thị {{ session('duplicate_order')['order'] }} đã được sử dụng bởi banner "{{ session('duplicate_order')['banner_title'] }}".</p>
            <p class="mb-4">Bạn có thể:</p>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.banners.edit', session('duplicate_order')['banner_id']) }}" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md transition-colors">
                    Điều chỉnh thứ tự của banner hiện có
                </a>
                <button type="button" onclick="document.getElementById('order').focus()" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md transition-colors">
                    Chọn thứ tự khác
                </button>
            </div>
        </div>
        @endif

        <form class="space-y-6" action="{{ route('admin.banners.update', $banner->id) }}" method="POST"
            enctype="multipart/form-data">
            @method('PUT')
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="title">Tiêu đề
                    banner</label>
                <input
                    class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-900 dark:text-gray-100 @error('title') border-red-500 @enderror"
                    type="text" id="title" name="title" value="{{ old('title', $banner->title) }}">
                @error('title')
                    <span class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ảnh banner</label>

                <div class="flex border-b border-gray-200 dark:border-gray-700 mb-3">
                    <button type="button"
                        class="banner-form-tab px-4 py-2 -mb-px font-semibold text-gray-600 dark:text-gray-300 border-b-2 border-transparent hover:border-indigo-500 focus:outline-none active"
                        data-tab="upload">Upload ảnh</button>
                    <button type="button"
                        class="banner-form-tab px-4 py-2 font-semibold text-gray-600 dark:text-gray-300 border-b-2 border-transparent hover:border-indigo-500 focus:outline-none"
                        data-tab="link">Nhập link ảnh</button>
                </div>

                <div class="banner-form-tab-content active" data-tab-content="upload">
                    <div class="relative">
                        <label
                            class="w-full flex flex-col items-center px-4 py-6 bg-white dark:bg-gray-700 text-indigo-600 dark:text-indigo-400 rounded-lg shadow-lg tracking-wide uppercase border border-indigo-600 dark:border-indigo-400 cursor-pointer hover:bg-indigo-600 dark:hover:bg-indigo-500 hover:text-white dark:hover:text-gray-100 transition-colors duration-200">
                            <svg class="w-8 h-8" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path
                                    d="M16.88 9.1A4 4 0 0 1 16 17H5a5 5 0 0 1-1-9.9V7a3 3 0 0 1 4.52-2.59A4.98 4.98 0 0 1 17 8c0 .38-.04.74-.12 1.1zM11 11h3l-4-4-4 4h3v3h2v-3z" />
                            </svg>
                            <span class="mt-2 text-base leading-normal">Chọn file ảnh</span>
                            <input class="hidden @error('image_path') border-red-500 @enderror" type="file"
                                id="image_path" name="image_path" accept="image/*">
                        </label>
                        @error('image_path')
                            <span class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    <div
                        class="mt-4 p-4 border border-dashed border-gray-300 dark:border-gray-600 rounded-md flex flex-col items-center justify-center min-h-[150px]">
                        <img class="max-w-full max-h-[300px] object-contain hidden" id="image-preview">
                        <div class="text-gray-500 dark:text-gray-400" id="preview-placeholder">Xem trước ảnh banner</div>
                    </div>
                </div>

                <div class="banner-form-tab-content hidden" data-tab-content="link">
                    <input
                        class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-900 dark:text-gray-100 @error('image_link') border-red-500 @enderror"
                        type="url" id="image_link" name="image_link" placeholder="Nhập link ảnh"
                        value="{{ old('image_link', $banner->image_path) }}">
                    @error('image_link')
                        <span class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</span>
                    @enderror
                    <div
                        class="mt-4 p-4 border border-dashed border-gray-300 dark:border-gray-600 rounded-md flex flex-col items-center justify-center min-h-[150px]">
                        <img class="max-w-full max-h-[300px] object-contain hidden" id="link-preview">
                        <div class="text-gray-500 dark:text-gray-400" id="link-placeholder">Xem trước ảnh từ link</div>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="description">Mô tả
                    banner</label>
                <textarea
                    class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm min-h-[100px] resize-y text-gray-900 dark:text-gray-100 @error('description') border-red-500 @enderror"
                    id="description" name="description">{{ old('description', $banner->description) }}</textarea>
                @error('description')
                    <span class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</span>
                @enderror
            </div>

            @php
                $productId = null;
                if ($banner->link) {
                    $parts = explode('/', $banner->link);
                    $productId = end($parts);
                }
            @endphp

            <div x-data="{
                query: '',
                products: [],
                selectedProduct: null,
                productId: '{{ $productId }}',
            }" class="relative" x-init="if (productId) {
                fetch(`{{ route('admin.banners.search.product') }}?id=${productId}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.length > 0) {
                            selectedProduct = data[0];
                            query = data[0].name;
                        }
                    });
            }" @click.outside="products = []">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Link Sản Phẩm
                </label>

                <input type="text" x-model="query"
                    @input.debounce.300ms="
            fetch(`{{ route('admin.banners.search.product') }}?q=${query}`)
                .then(res => res.json())
                .then(data => products = data)"
                    placeholder="Nhập tên sản phẩm..."
                    class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 dark:text-gray-100">

                <!-- Dropdown sản phẩm -->
                <ul x-show="query && products.length"
                    class="absolute z-10 mt-1 w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg max-h-60 overflow-auto">
                    <template x-for="product in products" :key="product.id">
                        <li @click="selectedProduct = product; query = product.name; products = []"
                            class="px-4 py-2 hover:bg-indigo-100 dark:hover:bg-indigo-600 hover:cursor-pointer">
                            <span x-text="product.name"></span>
                        </li>
                    </template>
                </ul>

                <!-- Hidden input để submit -->
                <input type="hidden" name="link"
                    :value="selectedProduct ? `/shop/products/${selectedProduct.id}` : '{{ $banner->link }}'">
            </div>


            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="position">Vị trí hiển
                    thị (trên trang)</label>
                <select
                    class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-900 dark:text-gray-100 @error('position') border-red-500 @enderror"
                    id="position" name="position">
                    <option value="homepage" {{ old('position', $banner->position) == 'homepage' ? 'selected' : '' }}>Trang
                        chủ (Homepage)</option>
                    <option value="footers" {{ old('position', $banner->position) == 'footers' ? 'selected' : '' }}>Chân
                        trang (Footers)</option>
                    <option value="promotions" {{ old('position', $banner->position) == 'promotions' ? 'selected' : '' }}>
                        Khuyến mãi (Promotions)</option>
                    <option value="menu" {{ old('position', $banner->position) == 'menu' ? 'selected' : '' }}>Menu
                    </option>
                    <option value="branch" {{ old('position', $banner->position) == 'branch' ? 'selected' : '' }}>Chi nhánh
                        (Branch)</option>
                    <option value="abouts" {{ old('position', $banner->position) == 'abouts' ? 'selected' : '' }}>Giới
                        thiệu (Abouts)</option>
                    <option value="supports" {{ old('position', $banner->position) == 'supports' ? 'selected' : '' }}>Hỗ
                        trợ (Supports)</option>
                    <option value="contacts" {{ old('position', $banner->position) == 'contacts' ? 'selected' : '' }}>Liên
                        hệ (Contacts)</option>
                </select>
                @error('position')
                    <span class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</span>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="is_active">Trạng
                        thái hiển thị</label>
                    <select
                        class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-900 dark:text-gray-100 @error('is_active') border-red-500 @enderror"
                        id="is_active" name="is_active">
                        <option value="1" {{ old('is_active', $banner->is_active) == '1' ? 'selected' : '' }}>Hiển
                            thị
                        </option>
                        <option value="0" {{ old('is_active', $banner->is_active) == '0' ? 'selected' : '' }}>Ẩn
                        </option>
                    </select>
                    @error('is_active')
                        <span class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div id="order-field" style="{{ old('position', $banner->position) != 'homepage' ? 'display: none;' : '' }}">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="order">Thứ tự
                        hiển thị</label>
                    <input type="number"
                        class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-900 dark:text-gray-100 @error('order') border-red-500 @enderror"
                        id="order" name="order" value="{{ old('order', $banner->order) }}" {{ old('position', $banner->position) != 'homepage' ? 'disabled' : '' }}>
                    @error('order')
                        <span class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="start_at">Thời
                        gian bắt đầu hiển thị</label>
                    <input
                        class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-900 dark:text-gray-100 @error('start_at') border-red-500 @enderror"
                        type="date" id="start_at" name="start_at"
                        value="{{ old('start_at', $banner->start_at ? date('Y-m-d', strtotime($banner->start_at)) : '') }}">
                    @error('start_at')
                        <span class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="end_at">Thời
                        gian kết thúc hiển thị</label>
                    <input
                        class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-900 dark:text-gray-100 @error('end_at') border-red-500 @enderror"
                        type="date" id="end_at" name="end_at"
                        value="{{ old('end_at', $banner->end_at ? date('Y-m-d', strtotime($banner->end_at)) : '') }}">
                    @error('end_at')
                        <span class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <button
                class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-600 dark:focus:ring-offset-gray-800 transition-colors duration-200"
                type="submit">Sửa Banner</button>
        </form>
    </div>
@endsection
<script src="//unpkg.com/alpinejs" defer></script>
<script>
    document.addEventListener('DOMContentLoaded', function(e) {
        // e.preventDefault(); // Bỏ dòng này, vì nó làm chặn sự kiện load mặc định

        const tabs = document.querySelectorAll('.banner-form-tab');
        const tabContents = document.querySelectorAll('.banner-form-tab-content');
        const imagePathInput = document.getElementById('image_path');
        const imageLinkInput = document.getElementById('image_link');
        const imagePreview = document.getElementById('image-preview');
        const previewPlaceholder = document.getElementById('preview-placeholder');
        const linkPreview = document.getElementById('link-preview');
        const linkPlaceholder = document.getElementById('link-placeholder');
        const existingImagePath = "{{ $banner->image_path ?? '' }}";

        function setActiveTab(tabName) {
            tabs.forEach(t => {
                if (t.dataset.tab === tabName) {
                    t.classList.add('active', 'border-indigo-500', 'text-indigo-600',
                        'dark:text-indigo-400', 'dark:border-indigo-400');
                    t.classList.remove('border-transparent', 'text-gray-600', 'dark:text-gray-300');
                } else {
                    t.classList.remove('active', 'border-indigo-500', 'text-indigo-600',
                        'dark:text-indigo-400', 'dark:border-indigo-400');
                    t.classList.add('border-transparent', 'text-gray-600', 'dark:text-gray-300');
                }
            });

            tabContents.forEach(content => {
                if (content.dataset.tabContent === tabName) {
                    content.classList.add('active');
                    content.classList.remove('hidden');
                } else {
                    content.classList.remove('active');
                    content.classList.add('hidden');
                }
            });

            if (tabName === 'upload') {
                imagePathInput.disabled = false;
                imageLinkInput.disabled = true;
            } else { // tabName === 'link'
                imagePathInput.disabled = true;
                imageLinkInput.disabled = false;
            }
        }

        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const tabName = this.dataset.tab;
                setActiveTab(tabName);
            });
        });

        let initialTab = 'upload';
        let prefillLink = false;
        const bannerImageUrl = "{{ $bannerImageUrl ?? '' }}";

        if (existingImagePath) {
            let isUrl = false;
            try {
                new URL(existingImagePath);
                isUrl = true;
            } catch (_) {
                /* not a valid URL */
            }

            if (isUrl) {
                initialTab = 'link';
                imageLinkInput.value = existingImagePath;
                prefillLink = true;
            } else {
                initialTab = 'upload';
                if (imagePreview && previewPlaceholder && bannerImageUrl) {
                    console.log("Loading preview image for upload tab:", bannerImageUrl);
                    imagePreview.src = bannerImageUrl;
                    imagePreview.style.display = 'block';
                    previewPlaceholder.style.display = 'none';
                }
            }
        }

        setActiveTab(initialTab);

        if (prefillLink && imageLinkInput.value) {
            if (linkPreview && linkPlaceholder) {
                linkPreview.src = imageLinkInput.value;
                linkPreview.style.display = 'block';
                linkPlaceholder.style.display = 'none';
            }
        }

        const bannerForm = document.querySelector(
            'form[action="{{ route('admin.banners.update', $banner->id) }}"]');
        if (bannerForm) {
            bannerForm.addEventListener('submit', function(e) {
                const activeTabName = document.querySelector('.banner-form-tab.active')?.dataset.tab;
                if (activeTabName === 'upload') {
                    imageLinkInput.disabled = true;
                    imagePathInput.disabled = false;
                } else if (activeTabName === 'link') {
                    imagePathInput.disabled = true;
                    imageLinkInput.disabled = false;
                } else {
                    console.error('No active tab found during form submission.');
                }
            });
        } else {
            console.error('Banner form not found for attaching submit listener.');
        }
        
        // Xử lý hiển thị/ẩn trường thứ tự hiển thị dựa trên vị trí được chọn
        const positionSelect = document.getElementById('position');
        const orderField = document.getElementById('order-field');
        const orderInput = document.getElementById('order');
        
        function handlePositionChange() {
            if (positionSelect.value === 'homepage') {
                orderField.style.display = '';
                orderInput.disabled = false;
                if (!orderInput.value) {
                    orderInput.value = '0';
                }
            } else {
                orderField.style.display = 'none';
                orderInput.disabled = true;
                orderInput.value = '';
            }
        }
        
        positionSelect.addEventListener('change', handlePositionChange);
        // Gọi hàm khi trang được tải để đảm bảo trạng thái ban đầu đúng
        handlePositionChange();

        if (imagePathInput && imagePreview && previewPlaceholder) {
            imagePathInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        imagePreview.style.display = 'block';
                        previewPlaceholder.style.display = 'none';
                    };
                    reader.readAsDataURL(file);
                } else {
                    imagePreview.src = '';
                    imagePreview.style.display = 'none';
                    previewPlaceholder.style.display = 'block';
                }
            });
        }

        if (imageLinkInput && linkPreview && linkPlaceholder) {
            imageLinkInput.addEventListener('input', function() {
                const url = this.value.trim();
                if (url) {
                    linkPreview.src = url;
                    linkPreview.style.display = 'block';
                    linkPlaceholder.style.display = 'none';
                } else {
                    linkPreview.src = '';
                    linkPreview.style.display = 'none';
                    linkPlaceholder.style.display = 'block';
                }
            });
            if (initialTab === 'link' && imageLinkInput.value) {
                imageLinkInput.dispatchEvent(new Event('input'));
            }
        }
    });
</script>
