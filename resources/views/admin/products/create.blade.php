@extends('layouts/admin/contentLayoutMaster')
@section('title', 'Thêm Sản Phẩm Mới')

{{-- Include core + vendor Styles --}}
@section('content')

<div class="ap-container">
  <!-- Page header -->
  <div class="ap-page-header">
    <h1 class="ap-page-title">Thêm Sản Phẩm Mới</h1>
    <p class="ap-page-description">Nhập thông tin chi tiết để tạo sản phẩm mới</p>
  </div>

  <form id="add-product-form" method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
    @csrf
    <!-- Basic Information -->
    <div class="ap-card">
      <div class="ap-card-header">
        <div>
          <h2 class="ap-card-title">Thông tin cơ bản</h2>
          <p class="ap-card-description">Nhập thông tin cơ bản của sản phẩm</p>
        </div>
      </div>
      <div class="ap-card-content">
        <div class="ap-form-grid ap-form-grid-2">
          <div class="ap-space-y-5">

            <div class="ap-form-group">
              <label for="name" class="ap-form-label">Tên sản phẩm <span class="ap-text-red-500">*</span></label>
              <input type="text" id="name" name="name" class="ap-form-input" required placeholder="Nhập tên sản phẩm" />
            </div>

            <div class="ap-form-grid-cols-2">
              <div class="ap-form-group">
                <label for="category_id" class="ap-form-label">Danh mục <span class="ap-text-red-500">*</span></label>
                <select id="category_id" name="category_id" class="ap-form-select" required>
                  <option value="">Chọn danh mục</option>
                  @foreach($categories as $category)
                  <option value="{{ $category->id }}">{{ $category->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="ap-form-group">
                <label for="base_price" class="ap-form-label">Giá gốc <span class="ap-text-red-500">*</span></label>
                <div class="ap-form-input-with-icon">
                  <span class="ap-icon">₫</span>
                  <input type="number" id="base_price" name="base_price" class="ap-form-input" required placeholder="0" min="0" step="0.01" />
                </div>
              </div>
            </div>

            <div class="ap-form-grid-cols-2">
              <div class="ap-form-group">
                <label for="preparation_time" class="ap-form-label">Thời gian chuẩn bị (phút) <span class="ap-text-red-500">*</span></label>
                <input type="number" id="preparation_time" name="preparation_time" class="ap-form-input" required placeholder="Nhập thời gian chuẩn bị" min="0" />
              </div>
              <div class="ap-form-group">
                <label for="stock_quantity" class="ap-form-label">Tồn kho <span class="ap-text-red-500">*</span></label>
                <input type="number" id="stock_quantity" name="stock_quantity" class="ap-form-input" required placeholder="0" min="0" />
              </div>
            </div>

            <div class="ap-form-group">
              <label for="shortDescription" class="ap-form-label">Mô tả ngắn gọn</label>
              <textarea id="shortDescription" name="shortDescription" class="ap-form-textarea" rows="1" placeholder="Nhập mô tả ngắn"></textarea>
            </div>

            <div class="ap-form-group">
              <label for="description" class="ap-form-label">Mô tả chi tiết</label>
              <textarea id="description" name="description" class="ap-form-textarea" rows="5" placeholder="Nhập mô tả chi tiết về sản phẩm"></textarea>
            </div>

            <div class="ap-form-group">
              <label class="ap-form-label">Trạng thái</label>
              <div class="ap-flex ap-gap-4">
                <div class="ap-form-checkbox">
                  <input type="radio" id="status_active" name="status" value="active" checked />
                  <label for="status_active">Đang bán</label>
                </div>
                <div class="ap-form-checkbox">
                  <input type="radio" id="status_inactive" name="status" value="inactive" />
                  <label for="status_inactive">Ngừng bán</label>
                </div>
              </div>
            </div>

            <div class="ap-form-group">
              <div class="ap-form-checkbox">
                <input type="checkbox" id="featured" name="featured" />
                <label for="featured">Sản phẩm nổi bật</label>
              </div>
            </div>
          </div>

          <div class="ap-space-y-4">
            <div class="ap-form-group">
              <label class="ap-form-label">Hình ảnh sản phẩm <span class="ap-text-red-500">*</span></label>
              <div class="ap-image-upload">
                <div class="ap-image-upload-placeholder" id="image-placeholder">
                  <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="17 8 12 3 7 8"></polyline>
                    <line x1="12" y1="3" x2="12" y2="15"></line>
                  </svg>
                  <p class="ap-text-sm ap-text-gray-500 ap-mb-2">Kéo thả hoặc nhấp để tải lên</p>
                  <button type="button" class="ap-btn ap-btn-outline ap-btn-sm" onclick="document.getElementById('file-upload').click()">Chọn hình ảnh</button>
                  <input type="file" id="file-upload" name="images[]" multiple accept="image/*" class="ap-hidden" onchange="handleFileUpload(this)" />
                </div>
              </div>
              <div class="ap-image-gallery" id="image-gallery"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Thuộc tính & Biến thể -->
    <div class="ap-tabs">
      <div class="ap-tabs-list">
        <div class="ap-tab active" data-tab="attributes">Thuộc tính</div>
        <div class="ap-tab" data-tab="variants">Biến thể</div>
      </div>

      <div class="ap-tab-content active" id="attributes-tab">
        <div class="ap-card">
          <div class="ap-card-header">
            <div>
              <h2 class="ap-card-title">Thuộc tính sản phẩm</h2>
              <p class="ap-card-description">Thêm các thuộc tính và giá trị cho sản phẩm</p>
            </div>
            <button type="button" class="ap-btn ap-btn-primary" id="add-attribute-btn">
              <svg class="ap-mr-2" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
              </svg>
              Thêm thuộc tính
            </button>
          </div>
          <div class="ap-card-content" id="attributes-container">
            {{-- Thuộc tính load động --}}
          </div>
        </div>
      </div>

      <div class="ap-tab-content" id="variants-tab">
        <div class="ap-card">
          <div class="ap-card-header">
            <div>
              <h2 class="ap-card-title">Biến thể sản phẩm</h2>
              <p class="ap-card-description">Quản lý các biến thể dựa trên thuộc tính</p>
            </div>
            <div class="ap-flex ap-gap-2">
              <button type="button" class="ap-btn ap-btn-outline" id="generate-variants-btn">
                <svg class="ap-mr-2" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M8 3v3a2 2 0 0 1-2 2H3"></path>
                  <path d="M21 8h-3a2 2 0 0 1-2-2V3"></path>
                  <path d="M3 16h3a2 2 0 0 1 2 2v3"></path>
                  <path d="M16 21v-3a2 2 0 0 1 2-2h3"></path>
                </svg>
                Tạo tự động
              </button>
              <button type="button" class="ap-btn ap-btn-primary" id="add-variant-btn">
                <svg class="ap-mr-2" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="12" y1="5" x2="12" y2="19"></line>
                  <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Thêm biến thể
              </button>
            </div>
          </div>
          <div class="ap-card-content ap-p-0">
            <div class="ap-table-container">
              <table class="ap-table" id="variants-table">
                <thead>
                  <tr>
                    <th>SKU</th>
                    <th>Thuộc tính</th>
                    <th>Giá</th>
                    <th>Tồn kho</th>
                    <th>Ảnh</th>
                    <th>Trạng thái</th>
                    <th class="ap-text-right">Thao tác</th>
                  </tr>
                </thead>
                <tbody>
                  {{-- Biến thể sẽ thêm bằng JS hoặc backend --}}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- Nút Lưu -->
    <div class="ap-sticky-footer">
      <button type="submit" class="ap-btn ap-btn-primary ap-btn-lg" id="save-product-btn">
        <svg class="ap-mr-2" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
          <polyline points="17 21 17 13 7 13 7 21"></polyline>
          <polyline points="7 3 7 8 15 8"></polyline>
        </svg>
        Tạo sản phẩm
      </button>
    </div>

  </form>
</div>

<script>
  // Chuyển tab
  const tabs = document.querySelectorAll('.ap-tab');
  const tabContents = document.querySelectorAll('.ap-tab-content');

  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      const tabId = tab.getAttribute('data-tab');
      tabs.forEach(t => t.classList.remove('active'));
      tabContents.forEach(c => c.classList.remove('active'));
      tab.classList.add('active');
      document.getElementById(`${tabId}-tab`).classList.add('active');
    });
  });

  // Upload hình ảnh
  function handleFileUpload(input) {
    const files = input.files;
    const gallery = document.getElementById('image-gallery');
    if (files.length > 0) {
      document.getElementById('image-placeholder').style.display = 'none';
      gallery.innerHTML = '';

      Array.from(files).forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = e => {
          const div = document.createElement('div');
          div.className = 'ap-image-item';

          const img = document.createElement('img');
          img.src = e.target.result;
          img.alt = 'Product image';

          const removeBtn = document.createElement('button');
          removeBtn.className = 'ap-image-item-remove';
          removeBtn.innerHTML = '×';

          // Xóa ảnh trong gallery và reset input file nếu không còn ảnh
          removeBtn.onclick = () => {
            div.remove();
            if (gallery.children.length === 0) {
              document.getElementById('image-placeholder').style.display = 'flex';
              // Reset input file để có thể upload lại file cũ nếu muốn
              input.value = '';
            }
          };

          div.appendChild(img);
          div.appendChild(removeBtn);
          gallery.appendChild(div);
        };
        reader.readAsDataURL(file);
      });
    } else {
      gallery.innerHTML = '';
      document.getElementById('image-placeholder').style.display = 'flex';
    }
  }

  // Thuộc tính container và biến đếm
  const attributesContainer = document.getElementById('attributes-container');
  let attributeCounter = 1; // Bắt đầu từ 1, bạn có thể tăng nếu muốn

  // Thêm thuộc tính mới
  document.getElementById('add-attribute-btn').addEventListener('click', () => {
    const attributeRow = document.createElement('div');
    attributeRow.className = 'ap-attribute-row';
    attributeRow.dataset.attributeId = attributeCounter;

    attributeRow.innerHTML = `
      <div class="ap-attribute-name">
        <div class="ap-form-group">
          <label class="ap-form-label">Tên thuộc tính</label>
          <input type="text" name="attributes[${attributeCounter}][name]" class="ap-form-input" placeholder="Nhập tên thuộc tính">
        </div>
        <div class="ap-form-checkbox ap-mt-2">
          <input type="checkbox" id="attribute-${attributeCounter}-variant" name="attributes[${attributeCounter}][is_variant]">
          <label for="attribute-${attributeCounter}-variant">Dùng cho biến thể</label>
        </div>
        <div class="ap-flex ap-justify-end ap-mt-2">
          <button type="button" class="ap-btn ap-btn-ghost ap-btn-sm ap-text-red-500 delete-attribute" data-attribute-id="${attributeCounter}">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M3 6h18"></path>
              <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
              <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
            </svg>
            Xóa
          </button>
        </div>
      </div>
      <div class="ap-attribute-values">
        <label class="ap-form-label">Giá trị thuộc tính</label>
        <div class="ap-border ap-rounded"></div>
        <div class="ap-attribute-add-value">
          <button type="button" class="ap-btn ap-btn-outline ap-btn-sm add-value-btn" data-attribute-id="${attributeCounter}">
            <svg class="ap-mr-2" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="12" y1="5" x2="12" y2="19"></line>
              <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Thêm giá trị
          </button>
        </div>
      </div>
    `;

    attributesContainer.appendChild(attributeRow);
    attributeCounter++;
  });

  // Event delegation xử lý xóa thuộc tính, thêm/xóa giá trị thuộc tính
  attributesContainer.addEventListener('click', e => {
    const target = e.target;

    // Xóa thuộc tính
    if (target.closest('.delete-attribute')) {
      const btn = target.closest('.delete-attribute');
      const attributeRow = btn.closest('.ap-attribute-row');
      if (attributeRow) attributeRow.remove();
      return;
    }

    // Thêm giá trị thuộc tính
    if (target.closest('.add-value-btn')) {
      const btn = target.closest('.add-value-btn');
      const attributeId = btn.dataset.attributeId;
      const valuesContainer = btn.closest('.ap-attribute-values').querySelector('.ap-border');

      const valueItem = document.createElement('div');
      valueItem.className = 'ap-attribute-value-item';
      valueItem.innerHTML = `
        <input type="text" name="attributes[${attributeId}][values][]" class="ap-form-input" placeholder="Nhập giá trị thuộc tính">
        <button type="button" class="ap-btn ap-btn-ghost ap-btn-sm ap-text-red-500 delete-value">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 6h18"></path>
            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
          </svg>
        </button>
      `;
      valuesContainer.appendChild(valueItem);
      return;
    }

    // Xóa giá trị thuộc tính
    if (target.closest('.delete-value')) {
      const btn = target.closest('.delete-value');
      const valueItem = btn.closest('.ap-attribute-value-item');
      if (valueItem) valueItem.remove();
      return;
    }
  });

  // Form submit demo (bạn xử lý gửi form thực tế theo backend)
  document.getElementById('add-product-form').addEventListener('submit', e => {
    // e.preventDefault(); // Bỏ comment nếu muốn test form submit thật
    alert('Sản phẩm đã được tạo thành công!');
  });
</script>

@endsection
