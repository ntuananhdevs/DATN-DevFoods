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



    // Tab switching giữa Thuộc tính và Biến thể
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

  // Xử lý upload hình ảnh sản phẩm
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

  // Thuộc tính container và counter để thêm thuộc tính mới
  const attributesContainer = document.getElementById('attributes-container');
  let attributeCounter = 3; // Bắt đầu từ 3 vì đã có 2 thuộc tính mặc định

  // Nút thêm thuộc tính
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
    if(target.closest('.delete-attribute')) {
      const btn = target.closest('.delete-attribute');
      const attributeRow = btn.closest('.ap-attribute-row');
      if(attributeRow) attributeRow.remove();
      return;
    }

    // Thêm giá trị thuộc tính
    if(target.closest('.add-value-btn')) {
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
    if(target.closest('.delete-value')) {
      const btn = target.closest('.delete-value');
      const valueItem = btn.closest('.ap-attribute-value-item');
      if(valueItem) valueItem.remove();
      return;
    }
  });
  document.getElementById('add-product-form').addEventListener('submit', e => {
    e.preventDefault();
    alert('Sản phẩm đã được tạo thành công!');
  });