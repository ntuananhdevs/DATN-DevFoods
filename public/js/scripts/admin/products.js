function handleSearch(event) {
        const searchValue = event.target.value.trim();
        const currentUrl = new URL(window.location.href);

        if (searchValue) {
            currentUrl.searchParams.set('search', searchValue);
        } else {
            currentUrl.searchParams.delete('search');
        }

        if (event.key === 'Enter') {
            window.location.href = currentUrl.toString();
        }
    }

    function toggleSelectAll() {
        const selectAllCheckbox = document.getElementById('selectAll');
        const rowCheckboxes = document.getElementsByClassName('row-checkbox');

        selectAllCheckbox.checked = !selectAllCheckbox.checked;
        for (let checkbox of rowCheckboxes) {
            checkbox.checked = selectAllCheckbox.checked;
        }
    }

    document.getElementById('selectAll').addEventListener('change', function() {
        const rowCheckboxes = document.getElementsByClassName('row-checkbox');
        for (let checkbox of rowCheckboxes) {
            checkbox.checked = this.checked;
        }
    });


// DOM Elements
    const avatarButton = document.getElementById('avatar-button');
    const userDropdown = document.getElementById('user-dropdown');
    const tabs = document.querySelectorAll('.ap-tab');
    const tabContents = document.querySelectorAll('.ap-tab-content');
    const addAttributeBtn = document.getElementById('add-attribute-btn');
    const attributesContainer = document.getElementById('attributes-container');
    const addVariantBtn = document.getElementById('add-variant-btn');
    const generateVariantsBtn = document.getElementById('generate-variants-btn');
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    const metaTitleInput = document.getElementById('meta-title');
    const metaDescriptionInput = document.getElementById('meta-description');
    const seoPreviewTitle = document.getElementById('seo-preview-title');
    const seoPreviewUrl = document.getElementById('seo-preview-url');
    const seoPreviewDescription = document.getElementById('seo-preview-description');

    // Toggle user dropdown
    avatarButton.addEventListener('click', () => {
      userDropdown.classList.toggle('show');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', (event) => {
      if (!avatarButton.contains(event.target) && !userDropdown.contains(event.target)) {
        userDropdown.classList.remove('show');
      }
    });

    // Tab functionality
    document.querySelectorAll('.ap-tab').forEach(tab => {
      tab.addEventListener('click', () => {
        const tabId = tab.getAttribute('data-tab');
        document.querySelectorAll('.ap-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.ap-tab-content').forEach(content => content.classList.remove('active'));
        tab.classList.add('active');
        document.getElementById(`${tabId}-tab`).classList.add('active');
      });
    });

    // Handle file upload
    window.handleFileUpload = function(input) {
      const files = input.files;
      const gallery = document.getElementById('image-gallery');
      if (files.length > 0) {
        document.getElementById('image-placeholder').style.display = 'none';
        gallery.innerHTML = '';
        for (let i = 0; i < files.length; i++) {
          const file = files[i];
          const reader = new FileReader();
          reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'ap-image-item';
            const img = document.createElement('img');
            img.src = e.target.result;
            img.alt = 'Product image';
            const removeBtn = document.createElement('button');
            removeBtn.className = 'ap-image-item-remove';
            removeBtn.innerHTML = '×';
            removeBtn.onclick = function() {
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
    };

    // Tags functionality
    function addTag(event) {
      if (event.key === 'Enter') {
        event.preventDefault();
        
        const input = document.getElementById('tag-input');
        const value = input.value.trim();
        
        if (value) {
          const container = document.getElementById('tags-container');
          const hiddenInput = document.getElementById('tags-hidden');
          
          const tag = document.createElement('div');
          tag.className = 'ap-tag';
          tag.innerHTML = `
            ${value}
            <span class="ap-tag-remove" onclick="removeTag(this)">×</span>
          `;
          
          container.insertBefore(tag, input);
          input.value = '';
          
          // Update hidden input with all tags
          updateHiddenTags();
        }
      }
    }

    function removeTag(element) {
      const tag = element.parentElement;
      tag.remove();
      updateHiddenTags();
    }

    function updateHiddenTags() {
      const tags = document.querySelectorAll('.ap-tag');
      const values = Array.from(tags).map(tag => tag.textContent.trim().slice(0, -1));
      document.getElementById('tags-hidden').value = values.join(',');
    }

    // SEO preview functionality
    nameInput.addEventListener('input', updateSeoPreview);
    slugInput.addEventListener('input', updateSeoPreview);
    metaTitleInput.addEventListener('input', updateSeoPreview);
    metaDescriptionInput.addEventListener('input', updateSeoPreview);

    function updateSeoPreview() {
      const name = nameInput.value || 'Tên sản phẩm';
      const slug = slugInput.value || 'ten-san-pham';
      const title = metaTitleInput.value || name;
      const description = metaDescriptionInput.value || 'Mô tả sản phẩm sẽ hiển thị ở đây. Đây là phần mô tả ngắn gọn về sản phẩm của bạn để thu hút khách hàng nhấp vào liên kết.';
      
      seoPreviewTitle.textContent = title;
      seoPreviewUrl.textContent = `www.example.com/san-pham/${slug}`;
      seoPreviewDescription.textContent = description;
    }

    // Auto-generate slug from name
    nameInput.addEventListener('blur', () => {
      if (!slugInput.value && nameInput.value) {
        const slug = nameInput.value
          .toLowerCase()
          .normalize('NFD')
          .replace(/[\u0300-\u036f]/g, '')
          .replace(/[đĐ]/g, 'd')
          .replace(/[^a-z0-9]+/g, '-')
          .replace(/(^-|-$)/g, '');
        
        slugInput.value = slug;
        updateSeoPreview();
      }
    });

    // Add attribute functionality
    let attributeCounter = 3; // Starting from 3 since we already have 2 attributes

    addAttributeBtn.addEventListener('click', () => {
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
              Xóa
            </button>
          </div>
        </div>
        <div class="ap-attribute-values">
          <label class="ap-form-label">Giá trị thuộc tính</label>
          <div class="ap-border ap-rounded">
            <div class="ap-attribute-value-item">
              <input type="text" name="attributes[${attributeCounter}][values][]" class="ap-form-input" placeholder="Nhập giá trị thuộc tính">
              <button type="button" class="ap-btn ap-btn-ghost ap-btn-sm ap-text-red-500 delete-value">×</button>
            </div>
          </div>
          <div class="ap-attribute-add-value">
            <button type="button" class="ap-btn ap-btn-outline ap-btn-sm add-value-btn" data-attribute-id="${attributeCounter}">
              Thêm giá trị
            </button>
          </div>
        </div>
      `;
      
      attributesContainer.appendChild(attributeRow);
      attributeCounter++;
      bindAttributeEvents(attributeRow);
    });

    // Bind events for attribute row
    function bindAttributeEvents(row) {
      row.querySelector('.add-value-btn').addEventListener('click', addAttributeValue);
      row.querySelector('.delete-attribute').addEventListener('click', function() {
        row.remove();
      });
      row.querySelectorAll('.delete-value').forEach(btn => {
        btn.addEventListener('click', function() {
          btn.closest('.ap-attribute-value-item').remove();
        });
      });
    }

    // Initial binding for existing attribute rows
    document.querySelectorAll('.ap-attribute-row').forEach(bindAttributeEvents);

    // Add attribute value
    function addAttributeValue(event) {
      const btn = event.currentTarget;
      const attributeId = btn.dataset.attributeId;
      const valuesContainer = btn.closest('.ap-attribute-values').querySelector('.ap-border');
      const valueItem = document.createElement('div');
      valueItem.className = 'ap-attribute-value-item';
      valueItem.innerHTML = `
        <input type="text" name="attributes[${attributeId}][values][]" class="ap-form-input" placeholder="Nhập giá trị thuộc tính">
        <button type="button" class="ap-btn ap-btn-ghost ap-btn-sm ap-text-red-500 delete-value">×</button>
      `;
      valuesContainer.appendChild(valueItem);
      valueItem.querySelector('.delete-value').addEventListener('click', function() {
        valueItem.remove();
      });
    }

    // Delete attribute functionality
    document.querySelectorAll('.delete-attribute').forEach(btn => {
      btn.addEventListener('click', deleteAttribute);
    });

    function deleteAttribute(event) {
      const btn = event.currentTarget;
      const attributeRow = btn.closest('.ap-attribute-row');
      attributeRow.remove();
    }

    // Delete attribute value functionality
    document.querySelectorAll('.delete-value').forEach(btn => {
      btn.addEventListener('click', deleteAttributeValue);
    });

    function deleteAttributeValue(event) {
      const btn = event.currentTarget;
      const valueItem = btn.closest('.ap-attribute-value-item');
      valueItem.remove();
    }

    // Form submission
    document.getElementById('add-product-form').addEventListener('submit', function(event) {
      // Có thể dùng AJAX ở đây nếu muốn
      // event.preventDefault();
      // Gửi form như bình thường hoặc xử lý theo ý bạn
    });

    // Initialize SEO preview
    updateSeoPreview();