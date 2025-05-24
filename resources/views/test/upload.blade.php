<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Test Upload AWS S3</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .upload-area {
            border: 3px dashed #dee2e6;
            border-radius: 15px;
            padding: 50px 40px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            background-color: #fafafa;
            min-height: 200px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .upload-area:hover {
            border-color: #007bff;
            background-color: #f0f8ff;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,123,255,0.1);
        }
        .upload-area.dragover {
            border-color: #28a745;
            background-color: #e8f5e9;
            transform: scale(1.02);
            box-shadow: 0 8px 16px rgba(40,167,69,0.2);
        }
        .image-preview {
            max-width: 200px;
            max-height: 200px;
            border-radius: 10px;
            margin: 10px;
        }
        .connection-status {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .status-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .status-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .uploaded-images {
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center mb-4">
                    <i class="fas fa-cloud-upload-alt"></i> Test Upload AWS S3
                </h1>
                
                <!-- Connection Status -->
                <div id="connectionStatus" class="connection-status" style="display: none;"></div>
                
                <div class="row">
                    <!-- Upload Section -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-upload"></i> Upload Ảnh</h5>
                            </div>
                            <div class="card-body">
                                <form id="uploadForm" enctype="multipart/form-data">
                                    @csrf
                                    <div class="upload-area" id="uploadArea">
                                        <i class="fas fa-cloud-upload-alt fa-3x mb-3 text-muted"></i>
                                        <h5 class="mb-2">Kéo thả ảnh vào đây hoặc click để chọn</h5>
                                        <p class="text-muted mb-2">Hỗ trợ: JPG, PNG, GIF (tối đa 2MB)</p>
                                        <small class="text-muted">Hoặc chọn file từ máy tính của bạn</small>
                                        <input type="file" id="imageInput" name="image" accept="image/*,.jpg,.jpeg,.png,.gif" style="display: none;">
                                    </div>
                                    
                                    <div id="imagePreview" class="mt-3" style="display: none;">
                                        <h6>Xem trước:</h6>
                                        <img id="previewImg" class="image-preview" src="">
                                        <div class="mt-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-upload"></i> Upload
                                            </button>
                                            <button type="button" class="btn btn-secondary" onclick="clearPreview()">
                                                <i class="fas fa-times"></i> Hủy
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                
                                <!-- Progress Bar -->
                                <div id="progressBar" class="mt-3" style="display: none;">
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 0%"></div>
                                    </div>
                                </div>
                                
                                <!-- Upload Result -->
                                <div id="uploadResult" class="mt-3"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Images List Section -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5><i class="fas fa-images"></i> Ảnh đã upload</h5>
                                <button class="btn btn-sm btn-outline-primary" onclick="loadImages()">
                                    <i class="fas fa-sync-alt"></i> Làm mới
                                </button>
                            </div>
                            <div class="card-body uploaded-images">
                                <div id="imagesList">
                                    <p class="text-center text-muted">Đang tải...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Test Connection Button -->
                <div class="text-center mt-4">
                    <button class="btn btn-info" onclick="testConnection()">
                        <i class="fas fa-wifi"></i> Test kết nối S3
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Set up CSRF token for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Prevent default drag behaviors on the entire page
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            document.addEventListener(eventName, preventDefaults, false);
            document.body.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        // Upload area functionality
        const uploadArea = document.getElementById('uploadArea');
        const imageInput = document.getElementById('imageInput');
        const imagePreview = document.getElementById('imagePreview');
        const previewImg = document.getElementById('previewImg');

        uploadArea.addEventListener('click', () => imageInput.click());

        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            e.stopPropagation();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragenter', (e) => {
            e.preventDefault();
            e.stopPropagation();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', (e) => {
            e.preventDefault();
            e.stopPropagation();
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            e.stopPropagation();
            uploadArea.classList.remove('dragover');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleFileSelect(files[0]);
            }
        });

        imageInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                handleFileSelect(e.target.files[0]);
            }
        });

        function handleFileSelect(file) {
            console.log('File selected:', file.name, file.type, file.size);
            
            if (!file.type.startsWith('image/')) {
                alert('Vui lòng chọn file ảnh! (JPG, PNG, GIF)');
                return;
            }

            if (file.size > 2 * 1024 * 1024) {
                alert('File quá lớn! Vui lòng chọn file nhỏ hơn 2MB.');
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => {
                previewImg.src = e.target.result;
                imagePreview.style.display = 'block';
                console.log('File preview loaded successfully');
            };
            reader.onerror = () => {
                alert('Có lỗi khi đọc file!');
            };
            reader.readAsDataURL(file);

            // Update the file input
            try {
                const dt = new DataTransfer();
                dt.items.add(file);
                imageInput.files = dt.files;
                console.log('File input updated successfully');
            } catch (error) {
                console.error('Error updating file input:', error);
                // Fallback for older browsers
                imageInput.files = [file];
            }
        }

        function clearPreview() {
            imagePreview.style.display = 'none';
            imageInput.value = '';
            document.getElementById('uploadResult').innerHTML = '';
        }

        // Upload form submission
        document.getElementById('uploadForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData();
            const fileInput = document.getElementById('imageInput');
            
            if (!fileInput.files[0]) {
                alert('Vui lòng chọn ảnh để upload!');
                return;
            }

            formData.append('image', fileInput.files[0]);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

            const progressBar = document.getElementById('progressBar');
            const progressBarInner = progressBar.querySelector('.progress-bar');
            const uploadResult = document.getElementById('uploadResult');

            progressBar.style.display = 'block';
            progressBarInner.style.width = '0%';

            try {
                const response = await fetch('/test/upload', {
                    method: 'POST',
                    body: formData
                });

                progressBarInner.style.width = '100%';

                const result = await response.json();

                setTimeout(() => {
                    progressBar.style.display = 'none';
                    
                    if (result.success) {
                        uploadResult.innerHTML = `
                            <div class="alert alert-success">
                                <h6><i class="fas fa-check-circle"></i> ${result.message}</h6>
                                <p><strong>File:</strong> ${result.original_name}</p>
                                <p><strong>Size:</strong> ${(result.size / 1024).toFixed(2)} KB</p>
                                <p><strong>URL:</strong> <a href="${result.url}" target="_blank">${result.url}</a></p>
                                <img src="${result.url}" class="image-preview" alt="Uploaded image">
                            </div>
                        `;
                        clearPreview();
                        loadImages();
                    } else {
                        uploadResult.innerHTML = `
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i> ${result.message}
                            </div>
                        `;
                    }
                }, 500);

            } catch (error) {
                progressBar.style.display = 'none';
                uploadResult.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> Có lỗi xảy ra: ${error.message}
                    </div>
                `;
            }
        });

        // Load images list
        async function loadImages() {
            const imagesList = document.getElementById('imagesList');
            imagesList.innerHTML = '<p class="text-center text-muted">Đang tải...</p>';

            try {
                const response = await fetch('/test/images');
                const result = await response.json();

                if (result.success) {
                    if (result.images.length === 0) {
                        imagesList.innerHTML = '<p class="text-center text-muted">Chưa có ảnh nào được upload.</p>';
                    } else {
                        imagesList.innerHTML = `
                            <p class="text-muted">Tổng: ${result.total} ảnh</p>
                            ${result.images.map(image => `
                                <div class="border rounded p-2 mb-2">
                                    <div class="row align-items-center">
                                        <div class="col-4">
                                            <img src="${image.url}" class="img-thumbnail" style="max-width: 80px;">
                                        </div>
                                        <div class="col-6">
                                            <small class="d-block"><strong>${image.name}</strong></small>
                                            <small class="text-muted">${(image.size / 1024).toFixed(2)} KB</small>
                                        </div>
                                        <div class="col-2">
                                            <button class="btn btn-sm btn-outline-danger" onclick="deleteImage('${image.name}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            `).join('')}
                        `;
                    }
                } else {
                    imagesList.innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
                }
            } catch (error) {
                imagesList.innerHTML = `<div class="alert alert-danger">Lỗi khi tải danh sách: ${error.message}</div>`;
            }
        }

        // Delete image
        async function deleteImage(filename) {
            if (!confirm('Bạn có chắc chắn muốn xóa ảnh này?')) {
                return;
            }

            try {
                const response = await fetch('/test/images', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ filename: filename })
                });

                const result = await response.json();

                if (result.success) {
                    alert(result.message);
                    loadImages();
                } else {
                    alert('Lỗi: ' + result.message);
                }
            } catch (error) {
                alert('Có lỗi xảy ra: ' + error.message);
            }
        }

        // Test S3 connection
        async function testConnection() {
            const connectionStatus = document.getElementById('connectionStatus');
            connectionStatus.style.display = 'block';
            connectionStatus.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang kiểm tra kết nối...';
            connectionStatus.className = 'connection-status';

            try {
                const response = await fetch('/test/connection');
                const result = await response.json();

                if (result.success) {
                    connectionStatus.innerHTML = `
                        <i class="fas fa-check-circle"></i> ${result.message}
                        <br><small>Bucket: ${result.config.bucket} | Region: ${result.config.region}</small>
                    `;
                    connectionStatus.classList.add('status-success');
                } else {
                    connectionStatus.innerHTML = `<i class="fas fa-times-circle"></i> ${result.message}`;
                    connectionStatus.classList.add('status-error');
                }
            } catch (error) {
                connectionStatus.innerHTML = `<i class="fas fa-times-circle"></i> Lỗi kết nối: ${error.message}`;
                connectionStatus.classList.add('status-error');
            }
        }

        // Load images on page load
        document.addEventListener('DOMContentLoaded', loadImages);
    </script>
</body>
</html> 