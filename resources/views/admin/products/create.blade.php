@extends('layouts/admin/contentLayoutMaster')
@section('content')


    <div class="ap-container">
      <!-- Page header -->
      <div class="ap-page-header">
        <h1 class="ap-page-title">Thêm Sản Phẩm Mới</h1>
        <p class="ap-page-description">Nhập thông tin chi tiết để tạo sản phẩm mới</p>
      </div>

      <form id="add-product-form">
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
                  <input type="text" id="name" name="name" class="ap-form-input" required placeholder="Nhập tên sản phẩm">
                </div>

                <div class="ap-form-grid-cols-2">
                  <div class="ap-form-group">
                    <label for="category" class="ap-form-label">Danh mục <span class="ap-text-red-500">*</span></label>
                    <select id="category" name="category" class="ap-form-select" required>
                      <option value="">Chọn danh mục</option>
                      <option value="1">Quần áo</option>
                      <option value="2">Giày dép</option>
                      <option value="3">Phụ kiện</option>
                      <option value="4">Đồ điện tử</option>
                      <option value="5">Đồ gia dụng</option>
                    </select>
                  </div>
                  <div class="ap-form-group">
                    <label for="salePrice" class="ap-form-label">Giá bán</label>
                    <div class="ap-form-input-with-icon">
                      <span class="ap-icon">₫</span>
                      <input type="number" id="salePrice" name="salePrice" class="ap-form-input" placeholder="0">
                    </div>
                  </div>
                </div>

                <div class="ap-form-grid-cols-2">
                  <div class="ap-form-group">
                    <label for="deliveryTime" class="ap-form-label">Thời gian chuẩn bị<span class="ap-text-red-500">*</span></label>
                    <input type="text" id="deliveryTime" name="deliveryTime" class="ap-form-input" required placeholder="Nhập Thời gian chuẩn bị">
                  </div>
                  <div class="ap-form-group">
                    <label for="stock" class="ap-form-label">Tồn kho<span class="ap-text-red-500">*</span></label>
                    <input type="number" id="stock" name="stock" class="ap-form-input" required placeholder="0">
                  </div>
                </div>

                <div class="ap-form-grid-cols-2">
                  <div class="ap-form-group">
                    <label for="stock" class="ap-form-label">Tồn kho <span class="ap-text-red-500">*</span></label>
                    <input type="number" id="stock" name="stock" class="ap-form-input" required placeholder="0">
                  </div>
                  <div class="ap-form-group">
                    <label for="weight" class="ap-form-label">Trọng lượng (gram)</label>
                    <input type="number" id="weight" name="weight" class="ap-form-input" placeholder="0">
                  </div>
                </div>

                <div class="ap-form-group">
                  <label for="description" class="ap-form-label">Mô tả chi tiết</label>
                  <textarea id="description" name="description" class="ap-form-textarea" rows="5" placeholder="Nhập mô tả chi tiết về sản phẩm"></textarea>
                </div>

                <div class="ap-form-grid-cols-2">
                  
                </div>

                <div class="ap-form-group">
                  <label class="ap-form-label">Trạng thái</label>
                  <div class="ap-flex ap-gap-4">
                    <div class="ap-form-checkbox">
                      <input type="radio" id="status-active" name="status" value="active" checked>
                      <label for="status-active">Đang bán</label>
                    </div>
                    <div class="ap-form-checkbox">
                      <input type="radio" id="status-inactive" name="status" value="inactive">
                      <label for="status-inactive">Ngừng bán</label>
                    </div>
                  </div>
                </div>

                <div class="ap-form-group">
                  <div class="ap-form-checkbox">
                    <input type="checkbox" id="featured" name="featured">
                    <label for="featured">Sản phẩm nổi bật</label>
                  </div>
                </div>
              </div>

              <div class="ap-space-y-4">
                <div class="ap-form-group">
                  <label class="ap-form-label">Hình ảnh sản phẩm <span class="ap-text-red-500">*</span></label>
                  <div class="ap-image-upload">
                    <div class="ap-image-upload-placeholder" id="image-placeholder">
                      <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="17 8 12 3 7 8"></polyline>
                        <line x1="12" y1="3" x2="12" y2="15"></line>
                      </svg>
                      <p class="ap-text-sm ap-text-gray-500 ap-mb-2">Kéo thả hoặc nhấp để tải lên</p>
                      <button type="button" class="ap-btn ap-btn-outline ap-btn-sm" onclick="document.getElementById('file-upload').click()">Chọn hình ảnh</button>
                      <input type="file" id="file-upload" name="images[]" multiple accept="image/*" class="ap-hidden" onchange="handleFileUpload(this)">
                    </div>
                  </div>
                  <div class="ap-image-gallery" id="image-gallery"></div>
                  <p class="ap-text-xs ap-text-gray-500 ap-mt-2">Có thể tải lên nhiều hình ảnh. Hình đầu tiên sẽ là hình ảnh chính.</p>
                </div>

                <div class="ap-form-group">
                  <label class="ap-form-label">Video sản phẩm</label>
                  <input type="text" id="video" name="video" class="ap-form-input" placeholder="Nhập URL video YouTube hoặc Vimeo">
                </div>

                <div class="ap-form-group">
                  <label class="ap-form-label">Tài liệu đính kèm</label>
                  <input type="file" id="documents" name="documents[]" multiple class="ap-form-input" accept=".pdf,.doc,.docx,.xls,.xlsx">
                  <p class="ap-text-xs ap-text-gray-500 ap-mt-2">Định dạng hỗ trợ: PDF, DOC, DOCX, XLS, XLSX</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Product Attributes and Variants -->
        <div class="ap-tabs">
          <div class="ap-tabs-list">
            <div class="ap-tab active" data-tab="attributes">Thuộc tính</div>
            <div class="ap-tab" data-tab="variants">Biến thể</div>
            <div class="ap-tab" data-tab="advanced">Nâng cao</div>
          </div>

          <!-- Attributes Tab -->
          <div class="ap-tab-content active" id="attributes-tab">
            <div class="ap-card">
              <div class="ap-card-header">
                <div>
                  <h2 class="ap-card-title">Thuộc tính sản phẩm</h2>
                  <p class="ap-card-description">Thêm các thuộc tính và giá trị cho sản phẩm</p>
                </div>
                <button type="button" class="ap-btn ap-btn-primary" id="add-attribute-btn">
                  <svg class="ap-mr-2" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                  </svg>
                  Thêm thuộc tính
                </button>
              </div>
              <div class="ap-card-content">
                <div id="attributes-container">
                  <!-- Attribute rows will be added here -->
                  <div class="ap-attribute-row" data-attribute-id="1">
                    <div class="ap-attribute-name">
                      <div class="ap-form-group">
                        <label class="ap-form-label">Tên thuộc tính</label>
                        <input type="text" name="attributes[1][name]" class="ap-form-input" value="Màu sắc">
                      </div>
                      <div class="ap-form-checkbox ap-mt-2">
                        <input type="checkbox" id="attribute-1-variant" name="attributes[1][is_variant]" checked>
                        <label for="attribute-1-variant">Dùng cho biến thể</label>
                      </div>
                      <div class="ap-flex ap-justify-end ap-mt-2">
                        <button type="button" class="ap-btn ap-btn-ghost ap-btn-sm ap-text-red-500 delete-attribute" data-attribute-id="1">
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
                      <div class="ap-border ap-rounded">
                        <div class="ap-attribute-value-item">
                          <input type="text" name="attributes[1][values][]" class="ap-form-input" value="Đen">
                          <button type="button" class="ap-btn ap-btn-ghost ap-btn-sm ap-text-red-500 delete-value">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                              <path d="M3 6h18"></path>
                              <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                              <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                            </svg>
                          </button>
                        </div>
                        <div class="ap-attribute-value-item">
                          <input type="text" name="attributes[1][values][]" class="ap-form-input" value="Trắng">
                          <button type="button" class="ap-btn ap-btn-ghost ap-btn-sm ap-text-red-500 delete-value">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                              <path d="M3 6h18"></path>
                              <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                              <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                            </svg>
                          </button>
                        </div>
                        <div class="ap-attribute-value-item">
                          <input type="text" name="attributes[1][values][]" class="ap-form-input" value="Xanh">
                          <button type="button" class="ap-btn ap-btn-ghost ap-btn-sm ap-text-red-500 delete-value">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                              <path d="M3 6h18"></path>
                              <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                              <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                            </svg>
                          </button>
                        </div>
                      </div>
                      <div class="ap-attribute-add-value">
                        <button type="button" class="ap-btn ap-btn-outline ap-btn-sm add-value-btn" data-attribute-id="1">
                          <svg class="ap-mr-2" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                          </svg>
                          Thêm giá trị
                        </button>
                      </div>
                    </div>
                  </div>

                  <div class="ap-attribute-row" data-attribute-id="2">
                    <div class="ap-attribute-name">
                      <div class="ap-form-group">
                        <label class="ap-form-label">Tên thuộc tính</label>
                        <input type="text" name="attributes[2][name]" class="ap-form-input" value="Kích thước">
                      </div>
                      <div class="ap-form-checkbox ap-mt-2">
                        <input type="checkbox" id="attribute-2-variant" name="attributes[2][is_variant]" checked>
                        <label for="attribute-2-variant">Dùng cho biến thể</label>
                      </div>
                      <div class="ap-flex ap-justify-end ap-mt-2">
                        <button type="button" class="ap-btn ap-btn-ghost ap-btn-sm ap-text-red-500 delete-attribute" data-attribute-id="2">
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
                      <div class="ap-border ap-rounded">
                        <div class="ap-attribute-value-item">
                          <input type="text" name="attributes[2][values][]" class="ap-form-input" value="S">
                          <button type="button" class="ap-btn ap-btn-ghost ap-btn-sm ap-text-red-500 delete-value">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                              <path d="M3 6h18"></path>
                              <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                              <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                            </svg>
                          </button>
                        </div>
                        <div class="ap-attribute-value-item">
                          <input type="text" name="attributes[2][values][]" class="ap-form-input" value="M">
                          <button type="button" class="ap-btn ap-btn-ghost ap-btn-sm ap-text-red-500 delete-value">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                              <path d="M3 6h18"></path>
                              <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                              <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                            </svg>
                          </button>
                        </div>
                        <div class="ap-attribute-value-item">
                          <input type="text" name="attributes[2][values][]" class="ap-form-input" value="L">
                          <button type="button" class="ap-btn ap-btn-ghost ap-btn-sm ap-text-red-500 delete-value">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                              <path d="M3 6h18"></path>
                              <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                              <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                            </svg>
                          </button>
                        </div>
                        <div class="ap-attribute-value-item">
                          <input type="text" name="attributes[2][values][]" class="ap-form-input" value="XL">
                          <button type="button" class="ap-btn ap-btn-ghost ap-btn-sm ap-text-red-500 delete-value">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                              <path d="M3 6h18"></path>
                              <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                              <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                            </svg>
                          </button>
                        </div>
                      </div>
                      <div class="ap-attribute-add-value">
                        <button type="button" class="ap-btn ap-btn-outline ap-btn-sm add-value-btn" data-attribute-id="2">
                          <svg class="ap-mr-2" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                          </svg>
                          Thêm giá trị
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Variants Tab -->
          <div class="ap-tab-content" id="variants-tab">
            <div class="ap-card">
              <div class="ap-card-header">
                <div>
                  <h2 class="ap-card-title">Biến thể sản phẩm</h2>
                  <p class="ap-card-description">Quản lý các biến thể dựa trên thuộc tính</p>
                </div>
                <div class="ap-flex ap-gap-2">
                  <button type="button" class="ap-btn ap-btn-outline" id="generate-variants-btn">
                    <svg class="ap-mr-2" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M8 3v3a2 2 0 0 1-2 2H3"></path>
                      <path d="M21 8h-3a2 2 0 0 1-2-2V3"></path>
                      <path d="M3 16h3a2 2 0 0 1 2 2v3"></path>
                      <path d="M16 21v-3a2 2 0 0 1 2-2h3"></path>
                    </svg>
                    Tạo tự động
                  </button>
                  <button type="button" class="ap-btn ap-btn-primary" id="add-variant-btn">
                    <svg class="ap-mr-2" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
                        <th class="ap-text-right">Thao tác</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td class="ap-font-mono">AT-D-S</td>
                        <td>
                          <div class="ap-flex ap-flex-wrap ap-gap-1">
                            <span class="ap-badge">Màu sắc: Đen</span>
                            <span class="ap-badge">Kích thước: S</span>
                          </div>
                        </td>
                        <td>199.000 ₫</td>
                        <td>10</td>
                        <td class="ap-text-right">
                          <div class="ap-flex ap-justify-end ap-gap-2">
                            <button type="button" class="ap-btn ap-btn-ghost ap-btn-icon edit-variant" data-id="1">
                              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                              </svg>
                            </button>
                            <button type="button" class="ap-btn ap-btn-ghost ap-btn-icon ap-text-red-500 delete-variant" data-id="1">
                              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 6h18"></path>
                                <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                              </svg>
                            </button>
                          </div>
                        </td>
                      </tr>
                      <tr>
                        <td class="ap-font-mono">AT-D-M</td>
                        <td>
                          <div class="ap-flex ap-flex-wrap ap-gap-1">
                            <span class="ap-badge">Màu sắc: Đen</span>
                            <span class="ap-badge">Kích thước: M</span>
                          </div>
                        </td>
                        <td>199.000 ₫</td>
                        <td>15</td>
                        <td class="ap-text-right">
                          <div class="ap-flex ap-justify-end ap-gap-2">
                            <button type="button" class="ap-btn ap-btn-ghost ap-btn-icon edit-variant" data-id="2">
                              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                              </svg>
                            </button>
                            <button type="button" class="ap-btn ap-btn-ghost ap-btn-icon ap-text-red-500 delete-variant" data-id="2">
                              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 6h18"></path>
                                <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                              </svg>
                            </button>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <!-- Advanced Tab -->
          <div class="ap-tab-content" id="advanced-tab">
            <div class="ap-card">
              <div class="ap-card-header">
                <div>
                  <h2 class="ap-card-title">Cài đặt nâng cao</h2>
                  <p class="ap-card-description">Cấu hình thêm cho sản phẩm</p>
                </div>
              </div>
              <div class="ap-card-content">
                <div class="ap-form-grid">
                  <div class="ap-form-grid-cols-2">
                    <div class="ap-form-group">
                      <label for="tax-class" class="ap-form-label">Thuế</label>
                      <select id="tax-class" name="tax_class" class="ap-form-select">
                        <option value="">Không áp dụng</option>
                        <option value="standard">Tiêu chuẩn (10%)</option>
                        <option value="reduced">Giảm (5%)</option>
                        <option value="zero">Không thuế (0%)</option>
                      </select>
                    </div>
                    <div class="ap-form-group">
                      <label for="shipping-class" class="ap-form-label">Vận chuyển</label>
                      <select id="shipping-class" name="shipping_class" class="ap-form-select">
                        <option value="">Mặc định</option>
                        <option value="free">Miễn phí vận chuyển</option>
                        <option value="flat">Phí cố định</option>
                        <option value="weight">Theo cân nặng</option>
                      </select>
                    </div>
                  </div>

                  <div class="ap-form-grid-cols-3">
                    <div class="ap-form-group">
                      <label for="length" class="ap-form-label">Chiều dài (cm)</label>
                      <input type="number" id="length" name="length" class="ap-form-input" placeholder="0">
                    </div>
                    <div class="ap-form-group">
                      <label for="width" class="ap-form-label">Chiều rộng (cm)</label>
                      <input type="number" id="width" name="width" class="ap-form-input" placeholder="0">
                    </div>
                    <div class="ap-form-group">
                      <label for="height" class="ap-form-label">Chiều cao (cm)</label>
                      <input type="number" id="height" name="height" class="ap-form-input" placeholder="0">
                    </div>
                  </div>

                  <div class="ap-form-group">
                    <label for="purchase-note" class="ap-form-label">Ghi chú mua hàng</label>
                    <textarea id="purchase-note" name="purchase_note" class="ap-form-textarea" rows="3" placeholder="Ghi chú sẽ được gửi đến khách hàng sau khi mua sản phẩm"></textarea>
                  </div>

                  <div class="ap-form-group">
                    <div class="ap-form-checkbox">
                      <input type="checkbox" id="enable-reviews" name="enable_reviews" checked>
                      <label for="enable-reviews">Cho phép đánh giá</label>
                    </div>
                  </div>

                  <div class="ap-form-group">
                    <div class="ap-form-checkbox">
                      <input type="checkbox" id="sold-individually" name="sold_individually">
                      <label for="sold-individually">Giới hạn một sản phẩm trên mỗi đơn hàng</label>
                    </div>
                  </div>

                  <div class="ap-form-group">
                    <label for="backorders" class="ap-form-label">Đặt hàng khi hết hàng</label>
                    <select id="backorders" name="backorders" class="ap-form-select">
                      <option value="no">Không cho phép</option>
                      <option value="notify">Cho phép, nhưng thông báo khách hàng</option>
                      <option value="yes">Cho phép</option>
                    </select>
                  </div>

                  <div class="ap-form-group">
                    <label for="low-stock-threshold" class="ap-form-label">Ngưỡng cảnh báo tồn kho thấp</label>
                    <input type="number" id="low-stock-threshold" name="low_stock_threshold" class="ap-form-input" placeholder="Ví dụ: 5">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Save Button -->
        <div class="ap-sticky-footer">
          <button type="button" class="ap-btn ap-btn-outline" id="save-draft-btn">Lưu nháp</button>
          <button type="submit" class="ap-btn ap-btn-primary ap-btn-lg" id="save-product-btn">
            <svg class="ap-mr-2" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
  // Tab switching
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

  // File upload images
  function handleFileUpload(input) {
    const files = input.files;
    const gallery = document.getElementById('image-gallery');
    if (files.length > 0) {
      document.getElementById('image-placeholder').style.display = 'none';
      gallery.innerHTML = '';
      for (let file of files) {
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
          removeBtn.onclick = () => {
            div.remove();
            if (gallery.children.length === 0) {
              document.getElementById('image-placeholder').style.display = 'flex';
            }
          };

          div.appendChild(img);
          div.appendChild(removeBtn);
          gallery.appendChild(div);
        };
        reader.readAsDataURL(file);
      }
    }
  }

  // Tags
  const tagsContainer = document.getElementById('tags-container');
  tagsContainer.addEventListener('keydown', (event) => {
    if(event.target.id === 'tag-input' && event.key === 'Enter'){
      event.preventDefault();
      const input = event.target;
      const value = input.value.trim();
      if(value){
        // Tránh tag trùng lặp
        const existingTags = Array.from(tagsContainer.querySelectorAll('.ap-tag')).map(t => t.textContent.trim().slice(0, -1));
        if(!existingTags.includes(value)){
          const tag = document.createElement('div');
          tag.className = 'ap-tag';
          tag.innerHTML = `${value} <span class="ap-tag-remove" style="cursor:pointer;">×</span>`;
          tagsContainer.insertBefore(tag, input);
          input.value = '';
          updateHiddenTags();
        } else {
          input.value = '';
        }
      }
    }
  });

  tagsContainer.addEventListener('click', (e) => {
    if (e.target.classList.contains('ap-tag-remove')) {
      const tag = e.target.parentElement;
      tag.remove();
      updateHiddenTags();
    }
  });

  function updateHiddenTags(){
    const tags = tagsContainer.querySelectorAll('.ap-tag');
    const values = Array.from(tags).map(tag => tag.textContent.trim().slice(0,-1));
    document.getElementById('tags-hidden').value = values.join(',');
  }

  // SEO Preview & slug auto-gen
  const nameInput = document.getElementById('name');
  const slugInput = document.getElementById('slug');
  const metaTitleInput = document.getElementById('meta-title');
  const metaDescriptionInput = document.getElementById('meta-description');
  const seoPreviewTitle = document.getElementById('seo-preview-title');
  const seoPreviewUrl = document.getElementById('seo-preview-url');
  const seoPreviewDescription = document.getElementById('seo-preview-description');

  [nameInput, slugInput, metaTitleInput, metaDescriptionInput].forEach(input => {
    if(input){
      input.addEventListener('input', updateSeoPreview);
    }
  });

\

  if(nameInput && slugInput){
    nameInput.addEventListener('blur', () => {
      if(!slugInput.value && nameInput.value){
        const slug = nameInput.value.toLowerCase()
          .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
          .replace(/[đĐ]/g, 'd')
          .replace(/[^a-z0-9]+/g, '-')
          .replace(/(^-|-$)/g, '');
        slugInput.value = slug;
        updateSeoPreview();
      }
    });
  }

  // Attributes container event delegation
  const attributesContainer = document.getElementById('attributes-container');
  let attributeCounter = 3; // Bắt đầu từ 3

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

  // Xử lý event delegation cho attributesContainer (thêm/xóa giá trị và xóa thuộc tính)
  attributesContainer.addEventListener('click', e => {
    if (e.target.closest('.delete-attribute')) {
      const btn = e.target.closest('.delete-attribute');
      const attributeRow = btn.closest('.ap-attribute-row');
      if(attributeRow) attributeRow.remove();
    }

    if (e.target.closest('.add-value-btn')) {
      const btn = e.target.closest('.add-value-btn');
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
    }

    if (e.target.closest('.delete-value')) {
      const btn = e.target.closest('.delete-value');
      const valueItem = btn.closest('.ap-attribute-value-item');
      if(valueItem) valueItem.remove();
    }
  });

  // Form submit
  document.getElementById('add-product-form').addEventListener('submit', e => {
    e.preventDefault();
    alert('Sản phẩm đã được tạo thành công!');
  });

  // Khởi tạo SEO preview
  updateSeoPreview();
</script>

@endsection

