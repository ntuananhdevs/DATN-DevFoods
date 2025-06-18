<!-- Branch Change Confirmation Modal -->
<div id="branch-change-confirmation-modal" class="fixed inset-0 bg-black bg-opacity-50 z-[60] flex items-center justify-center" style="display: none;">
    <div class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full mx-4 transform transition-transform">
        <div class="flex flex-col items-center mb-4 text-center">
            <div class="w-14 h-14 bg-orange-100 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-exclamation-triangle text-orange-500 text-xl"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-900 mb-2">Xác nhận thay đổi chi nhánh</h2>
            <p class="text-gray-600 mb-6">Thay đổi chi nhánh sẽ xóa giỏ hàng hiện tại của bạn. Bạn có muốn tiếp tục?</p>
            <div class="flex gap-3 w-full">
                <button id="cancel-branch-change" class="flex-1 border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 py-2 px-4 rounded-md font-medium transition-colors">
                    Hủy bỏ
                </button>
                <button id="confirm-branch-change" class="flex-1 bg-orange-500 hover:bg-orange-600 text-white py-2 px-4 rounded-md font-medium transition-colors">
                    Xác nhận
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Branch Selection Modal -->
<div id="branch-selector-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center" 
     style="display: none;">
    <div class="bg-white rounded-lg shadow-xl p-6 max-w-lg w-full mx-4 transform transition-transform">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-900">Chọn Chi Nhánh</h2>
            @if($hasBranchSelected ?? false)
            <button id="close-branch-modal" class="text-gray-400 hover:text-gray-500">
                <i class="fas fa-times"></i>
            </button>
            @endif
        </div>
        
        <div class="mb-6">
            <div class="bg-orange-50 p-4 mb-4 rounded-lg border border-orange-200">
                <div class="flex items-start">
                    <div class="flex-shrink-0 mt-0.5">
                        <i class="fas fa-info-circle text-orange-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-gray-700">Vui lòng chọn chi nhánh gần bạn để xem sản phẩm có sẵn và tiến hành đặt hàng</p>
                    </div>
                </div>
            </div>
            
            <!-- Location detector option -->
            <button id="detect-location" class="w-full flex items-center justify-center gap-2 border border-orange-500 text-orange-500 hover:bg-orange-50 px-4 py-3 rounded-md font-medium transition-colors mb-4">
                <i class="fas fa-location-arrow"></i>
                <span>Tìm Chi Nhánh Gần Nhất</span>
            </button>
            
            <!-- Search box -->
            <div class="relative mb-4">
                <input type="text" id="branch-search" placeholder="Tìm chi nhánh theo tên hoặc địa chỉ..." 
                       class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 pl-10">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
            
            <!-- Branches list -->
            <div class="max-h-60 overflow-y-auto pr-2" id="branches-list">
                @foreach($branches as $branch)
                <div class="branch-item border-b border-gray-100 last:border-0 hover:bg-orange-50 transition-colors">
                    <label class="flex items-start p-3 cursor-pointer">
                        <input type="radio" name="selected_branch" value="{{ $branch->id }}" 
                               class="mt-1 h-4 w-4 text-orange-500 focus:ring-orange-500 border-gray-300"
                               {{ ($currentBranch && $currentBranch->id == $branch->id) ? 'checked' : '' }}>
                        <div class="ml-3 flex-1">
                            <p class="font-medium text-gray-900">{{ $branch->name }}</p>
                            <p class="text-sm text-gray-500">{{ $branch->address }}</p>
                            <div class="flex items-center text-xs text-gray-500 mt-1">
                                <span class="flex items-center">
                                    <i class="fas fa-clock mr-1"></i> 
                                    {{ $branch->opening_hours }}
                                </span>
                                <span class="mx-2">•</span>
                                <span class="flex items-center">
                                    <i class="fas fa-phone mr-1"></i> 
                                    {{ $branch->phone }}
                                </span>
                            </div>
                        </div>
                    </label>
                </div>
                @endforeach
            </div>
        </div>
        
        <button id="confirm-branch" class="w-full bg-gray-400 text-white px-6 py-3 rounded-md font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
            Xác Nhận Chi Nhánh
        </button>
    </div>
</div>

<!-- Branch switching button (fixed at bottom corner) -->
<button id="change-branch-btn" class="fixed bottom-4 left-4 bg-white text-orange-500 border border-orange-300 px-4 py-2 rounded-full shadow-lg z-30 flex items-center gap-2 hover:bg-orange-50 {{ !($hasBranchSelected ?? false) ? 'hidden' : '' }}">
    <i class="fas fa-store h-4 w-4"></i>
    @if($currentBranch)
        @php
        $branch = $currentBranch;
        @endphp
        @if($branch)
            <span>{{ $branch->name }}</span>
        @else
            <span>Đổi Chi Nhánh</span>
        @endif
    @else
        <span>Đổi Chi Nhánh</span>
    @endif
</button>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const branchModal = document.getElementById('branch-selector-modal');
    const closeModal = document.getElementById('close-branch-modal');
    const confirmBranch = document.getElementById('confirm-branch');
    const changeBranchBtn = document.getElementById('change-branch-btn');
    const branchSearch = document.getElementById('branch-search');
    const branchItems = document.querySelectorAll('.branch-item');
    const detectLocation = document.getElementById('detect-location');
    const branchChangeConfirmationModal = document.getElementById('branch-change-confirmation-modal');
    const cancelBranchChange = document.getElementById('cancel-branch-change');
    const confirmBranchChange = document.getElementById('confirm-branch-change');
    
    // Variables to store pending branch change
    let pendingBranchSelection = null;
    
    // Debug session status on page load
    console.log('Branch session status on load:', {
        hasSession: {{ ($hasBranchSelected ?? false) ? 'true' : 'false' }},
        branchId: '{{ $currentBranch ? $currentBranch->id : '' }}',
        modalDisplay: branchModal.style.display
    });
    
    // Helper function to get cookie value
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    }
    
    // Helper function to set cookie
    function setCookie(name, value, days) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        const expires = "; expires=" + date.toUTCString();
        document.cookie = name + "=" + value + expires + "; path=/";
    }
    
    // Check for branch selection in cookie
    const branchCookie = getCookie('selected_branch');
    console.log('Branch cookie:', branchCookie);
    
    // Check if we need to show the modal (no branch selected)
    const hasSelectedBranch = {{ ($hasBranchSelected ?? false) ? 'true' : 'false' }} || branchCookie;
    
    if (!hasSelectedBranch) {
        console.log('No branch selected, showing modal');
        branchModal.style.display = 'flex';
        document.body.classList.add('overflow-hidden');
    } else {
        console.log('Branch already selected, hiding modal');
        branchModal.style.display = 'none';
    }
    
    // Function to show modal
    function showBranchModal() {
        branchModal.style.display = 'flex';
        document.body.classList.add('overflow-hidden'); // Prevent body scrolling
    }
    
    // Function to hide modal
    function hideBranchModal() {
        // Only allow hiding if a branch is selected or there's a branch in session
        if (document.querySelector('input[name="selected_branch"]:checked') || {{ ($hasBranchSelected ?? false) ? 'true' : 'false' }}) {
            branchModal.style.display = 'none';
            document.body.classList.remove('overflow-hidden'); // Allow body scrolling
        } else {
            // Prevent closing if no branch is selected
            if (window.showToast) {
                window.showToast('Vui lòng chọn một chi nhánh trước khi tiếp tục', 'warning');
            } else {
                alert('Vui lòng chọn một chi nhánh trước khi tiếp tục');
            }
        }
    }
    
    // Disable confirm button if no branch is selected
    function updateConfirmButton() {
        if (confirmBranch) {
            const selectedBranch = document.querySelector('input[name="selected_branch"]:checked');
            
            // Log for debugging
            console.log('Selected branch:', selectedBranch ? selectedBranch.value : 'none');
            
            if (!selectedBranch) {
                confirmBranch.disabled = true;
                confirmBranch.classList.add('opacity-50', 'cursor-not-allowed', 'bg-gray-400');
                confirmBranch.classList.remove('bg-orange-500', 'hover:bg-orange-600');
            } else {
                confirmBranch.disabled = false;
                confirmBranch.classList.remove('opacity-50', 'cursor-not-allowed', 'bg-gray-400');
                confirmBranch.classList.add('bg-orange-500', 'hover:bg-orange-600');
            }
        }
    }
    
    // Call on page load
    updateConfirmButton();
    
    // Add click event listeners to radio buttons to update button state
    document.querySelectorAll('input[name="selected_branch"]').forEach(radio => {
        radio.addEventListener('change', updateConfirmButton);
    });
    
    // Search functionality
    if (branchSearch) {
        branchSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            branchItems.forEach(item => {
                const branchName = item.querySelector('.font-medium').textContent.toLowerCase();
                const branchAddress = item.querySelector('.text-gray-500').textContent.toLowerCase();
                
                if (branchName.includes(searchTerm) || branchAddress.includes(searchTerm)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
    
    // Close button
    if (closeModal) {
        closeModal.addEventListener('click', hideBranchModal);
    }
    
    // Change branch button
    if (changeBranchBtn) {
        changeBranchBtn.addEventListener('click', function() {
            showBranchModal();
        });
    }
    
    // Detect location
    if (detectLocation) {
        detectLocation.addEventListener('click', function() {
            if (navigator.geolocation) {
                // Show loading state
                this.disabled = true;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang tìm vị trí...';
                
                navigator.geolocation.getCurrentPosition(function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    // Send to server to find nearest branch
                    fetch(`/branches/nearest?lat=${lat}&lng=${lng}`, {
                        method: 'GET',
                        credentials: 'same-origin',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.branch_id) {
                                // Select the radio button for this branch
                                const radio = document.querySelector(`input[name="selected_branch"][value="${data.branch_id}"]`);
                                if (radio) {
                                    // Check the radio button
                                    radio.checked = true;
                                    
                                    // Scroll to this branch
                                    radio.closest('.branch-item').scrollIntoView({
                                        behavior: 'smooth',
                                        block: 'center'
                                    });
                                    
                                    // Manually trigger change event to update confirm button
                                    radio.dispatchEvent(new Event('change'));
                                    
                                    // Ensure the button is enabled (as a backup)
                                    setTimeout(() => {
                                        updateConfirmButton();
                                        
                                        // Log state after update
                                        console.log('After nearest branch selection:');
                                        console.log('- Selected branch:', radio.value);
                                        console.log('- Confirm button disabled:', confirmBranch.disabled);
                                    }, 100);
                                }
                            }
                            // Reset button state
                            detectLocation.disabled = false;
                            detectLocation.innerHTML = '<i class="fas fa-location-arrow"></i> Tìm Chi Nhánh Gần Nhất';
                        })
                        .catch(error => {
                            console.error('Error finding nearest branch:', error);
                            // Reset button state
                            detectLocation.disabled = false;
                            detectLocation.innerHTML = '<i class="fas fa-location-arrow"></i> Tìm Chi Nhánh Gần Nhất';
                        });
                }, function(error) {
                    console.error('Geolocation error:', error);
                    // Reset button state
                    detectLocation.disabled = false;
                    detectLocation.innerHTML = '<i class="fas fa-location-arrow"></i> Tìm Chi Nhánh Gần Nhất';
                    // Show error toast
                    if (window.showToast) {
                        window.showToast('Không thể xác định vị trí của bạn. Vui lòng chọn chi nhánh thủ công.');
                    }
                });
            } else {
                if (window.showToast) {
                    window.showToast('Trình duyệt của bạn không hỗ trợ định vị. Vui lòng chọn chi nhánh thủ công.');
                }
            }
        });
    }
    
    // Handle confirmation dialog buttons
    if (cancelBranchChange) {
        cancelBranchChange.addEventListener('click', function() {
            // Hide the confirmation modal
            branchChangeConfirmationModal.style.display = 'none';
            document.body.classList.remove('overflow-hidden');
            pendingBranchSelection = null;
        });
    }
    
    if (confirmBranchChange) {
        confirmBranchChange.addEventListener('click', function() {
            // Hide the confirmation modal
            branchChangeConfirmationModal.style.display = 'none';
            
            // If there's a pending branch selection, process it
            if (pendingBranchSelection) {
                // Get the selected branch from the pending selection
                const selectedBranch = pendingBranchSelection;
                
                // Show loading state on the confirm button in the original modal
                if (confirmBranch) {
                    confirmBranch.disabled = true;
                    confirmBranch.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
                }
                
                // Set branch in session via AJAX
                fetch('/branches/set-selected', {
                    method: 'POST',
                    credentials: 'same-origin', // Important to include cookies
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        branch_id: selectedBranch.value
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Set cookie directly here as a fallback
                        setCookie('selected_branch', selectedBranch.value, 30); // 30 days
                        console.log('Set branch cookie directly:', selectedBranch.value);
                        
                        // Hide modal
                        hideBranchModal();
                        
                        // Update the UI to reflect the selected branch instead of reloading
                        const branchName = selectedBranch.closest('.branch-item').querySelector('.font-medium').textContent;
                        const branchSelector = document.getElementById('branch-selector-button');
                        if (branchSelector) {
                            branchSelector.innerHTML = `
                                <i class="fas fa-store text-orange-500"></i>
                                <span class="hidden sm:inline text-sm font-medium truncate max-w-28">${branchName}</span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            `;
                        }
                        
                        // Show the change branch button if it's hidden
                        if (changeBranchBtn) {
                            changeBranchBtn.classList.remove('hidden');
                            // Update the text to show branch name
                            changeBranchBtn.innerHTML = `
                                <i class="fas fa-store h-4 w-4"></i>
                                <span>${branchName}</span>
                            `;
                        }
                        
                        // Append the branch ID to all forms as a hidden input
                        document.querySelectorAll('form').forEach(form => {
                            let input = form.querySelector('input[name="branch_id"]');
                            if (!input) {
                                input = document.createElement('input');
                                input.type = 'hidden';
                                input.name = 'branch_id';
                                form.appendChild(input);
                            }
                            input.value = selectedBranch.value;
                        });
                        
                        // If on a product listing page, reload the products
                        if (typeof loadProducts === 'function') {
                            loadProducts();
                        } else {
                            // Store the branch ID in localStorage as a backup
                            localStorage.setItem('selected_branch_id', selectedBranch.value);
                            localStorage.setItem('selected_branch_timestamp', Date.now());
                            
                            // Also set a cookie as fallback
                            setCookie('selected_branch', selectedBranch.value, 30); // 30 days
                            
                            // Always reload the page to ensure proper filtering
                            window.location.href = window.location.pathname + '?branch_id=' + selectedBranch.value;
                        }
                    } else {
                        // Reset button state
                        if (confirmBranch) {
                            confirmBranch.disabled = false;
                            confirmBranch.innerHTML = 'Xác Nhận Chi Nhánh';
                        }
                        
                        if (window.showToast) {
                            window.showToast(data.message || 'Có lỗi xảy ra khi chọn chi nhánh', 'error');
                        } else {
                            alert(data.message || 'Có lỗi xảy ra khi chọn chi nhánh');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error selecting branch:', error);
                    
                    // Reset button state
                    if (confirmBranch) {
                        confirmBranch.disabled = false;
                        confirmBranch.innerHTML = 'Xác Nhận Chi Nhánh';
                    }
                    
                    if (window.showToast) {
                        window.showToast('Có lỗi xảy ra khi chọn chi nhánh', 'error');
                    } else {
                        alert('Có lỗi xảy ra khi chọn chi nhánh');
                    }
                });
                
                // Reset pending selection
                pendingBranchSelection = null;
            }
        });
    }

    // Confirm branch selection
    if (confirmBranch) {
        confirmBranch.addEventListener('click', function() {
            const selectedBranch = document.querySelector('input[name="selected_branch"]:checked');
            
            if (!selectedBranch) {
                if (window.showToast) {
                    window.showToast('Vui lòng chọn một chi nhánh', 'warning');
                } else {
                    alert('Vui lòng chọn một chi nhánh');
                }
                return;
            }
            
            // Check if user is changing branches and has items in cart
            @if($currentBranch && session('cart_count', 0) > 0)
            if (selectedBranch.value != '{{ $currentBranch->id }}') {
                // Show custom confirmation modal instead of browser alert
                pendingBranchSelection = selectedBranch;
                branchChangeConfirmationModal.style.display = 'flex';
                document.body.classList.add('overflow-hidden');
                return;
            }
            @endif
            
            // Show loading state
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
            
            // Set branch in session via AJAX
            fetch('/branches/set-selected', {
                method: 'POST',
                credentials: 'same-origin', // Important to include cookies
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    branch_id: selectedBranch.value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Set cookie directly here as a fallback
                    setCookie('selected_branch', selectedBranch.value, 30); // 30 days
                    console.log('Set branch cookie directly:', selectedBranch.value);
                    
                    // Hide modal
                    hideBranchModal();
                    
                    // Update the UI to reflect the selected branch instead of reloading
                    const branchName = selectedBranch.closest('.branch-item').querySelector('.font-medium').textContent;
                    const branchSelector = document.getElementById('branch-selector-button');
                    if (branchSelector) {
                        branchSelector.innerHTML = `
                            <i class="fas fa-store text-orange-500"></i>
                            <span class="hidden sm:inline text-sm font-medium truncate max-w-28">${branchName}</span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        `;
                    }
                    
                    // Show the change branch button if it's hidden
                    if (changeBranchBtn) {
                        changeBranchBtn.classList.remove('hidden');
                        // Update the text to show branch name
                        changeBranchBtn.innerHTML = `
                            <i class="fas fa-store h-4 w-4"></i>
                            <span>${branchName}</span>
                        `;
                    }
 
                    
                    // Append the branch ID to all forms as a hidden input
                    document.querySelectorAll('form').forEach(form => {
                        let input = form.querySelector('input[name="branch_id"]');
                        if (!input) {
                            input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'branch_id';
                            form.appendChild(input);
                        }
                        input.value = selectedBranch.value;
                    });
                    
                    // If on a product listing page, reload the products
                    if (typeof loadProducts === 'function') {
                        loadProducts();
                    } else {
                        // Store the branch ID in localStorage as a backup
                        localStorage.setItem('selected_branch_id', selectedBranch.value);
                        localStorage.setItem('selected_branch_timestamp', Date.now());
                        
                        // Also set a cookie as fallback
                        setCookie('selected_branch', selectedBranch.value, 30); // 30 days
                        
                        // Always reload the page to ensure proper filtering
                        window.location.href = window.location.pathname + '?branch_id=' + selectedBranch.value;
                    }
                } else {
                    // Reset button state
                    this.disabled = false;
                    this.innerHTML = 'Xác Nhận Chi Nhánh';
                    
                    if (window.showToast) {
                        window.showToast(data.message || 'Có lỗi xảy ra khi chọn chi nhánh', 'error');
                    } else {
                        alert(data.message || 'Có lỗi xảy ra khi chọn chi nhánh');
                    }
                }
            })
            .catch(error => {
                console.error('Error selecting branch:', error);
                
                // Reset button state
                this.disabled = false;
                this.innerHTML = 'Xác Nhận Chi Nhánh';
                
                if (window.showToast) {
                    window.showToast('Có lỗi xảy ra khi chọn chi nhánh', 'error');
                } else {
                    alert('Có lỗi xảy ra khi chọn chi nhánh');
                }
            });
        });
    }
});
</script>