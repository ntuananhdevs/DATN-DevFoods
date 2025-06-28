<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Điều hướng giao hàng</title>
    
    <!-- Mapbox GL JS -->
    <script src='https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js'></script>
    <link href='https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css' rel='stylesheet' />
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    
    <!-- Font Awesome for modal icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Modal JS -->
    <script src="{{ asset('js/modal.js') }}"></script>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f8fafc;
            color: #1f2937;
            height: 100vh;
            overflow: hidden;
        }

        #map {
            position: absolute;
            top: 0;
            bottom: 0;
            width: 100%;
            height: 100vh;
        }

        .overlay {
            position: absolute;
            z-index: 10;
        }

        .header {
            top: 0;
            left: 0;
            right: 0;
            padding: 1rem;
        }

        .header-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .btn {
            padding: 0.5rem 1rem;
            border: 1px solid #d1d5db;
            background: white;
            color: #374151;
            border-radius: 0.375rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            transition: all 0.2s;
            text-decoration: none;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .btn:hover {
            background: #f9fafb;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .btn-icon {
            padding: 0.75rem;
            width: 3rem;
            height: 3rem;
            border-radius: 50%;
            justify-content: center;
        }

        .btn-primary {
            background: #2563eb;
            border-color: #2563eb;
            color: white;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        .btn-success {
            background: #16a34a;
            border-color: #16a34a;
            color: white;
        }

        .btn-success:hover {
            background: #15803d;
        }

        .btn-danger {
            background: #dc2626;
            border-color: #dc2626;
            color: white;
        }

        .btn-danger:hover {
            background: #b91c1c;
        }

        .badge {
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            color: white;
        }

        .badge-green {
            background: #16a34a;
        }

        .badge-purple {
            background: #7c3aed;
        }

        .card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .customer-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .customer-name {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
        }

        .customer-address {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: #6b7280;
            margin-bottom: 0.5rem;
        }

        .note {
            margin-top: 0.5rem;
            padding: 0.5rem;
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            color: #92400e;
        }

        .map-controls {
            position: absolute;
            right: 1rem;
            bottom: 8rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .bottom-panel {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 1rem;
        }

        .navigation-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .info-item {
            text-align: center;
        }

        .info-value {
            font-size: 1.125rem;
            font-weight: bold;
            color: #1f2937;
        }

        .info-label {
            font-size: 0.875rem;
            color: #6b7280;
        }

        .current-instruction {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .instruction-icon {
            width: 3rem;
            height: 3rem;
            background: #dbeafe;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2563eb;
        }

        .instruction-text {
            font-weight: 500;
            color: #1f2937;
        }

        .instruction-distance {
            font-size: 0.875rem;
            color: #6b7280;
        }

        .action-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.5rem;
        }

        .loading {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            z-index: 1000;
        }

        .spinner {
            width: 3rem;
            height: 3rem;
            border: 2px solid #e5e7eb;
            border-top: 2px solid #2563eb;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 1rem;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .hidden {
            display: none;
        }

        .w-full {
            width: 100%;
        }

        .text-center {
            text-align: center;
        }

        .mt-2 {
            margin-top: 0.5rem;
        }

        .text-gray-500 {
            color: #6b7280;
        }

        .text-sm {
            font-size: 0.875rem;
        }

        .route-line {
            stroke: #2563eb;
            stroke-width: 4;
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        /* Toast Styles - Redesigned */
        .toast {
            position: fixed;
            top: 1rem;
            right: 1rem;
            background: white;
            border-radius: 0.5rem;
            padding: 1rem 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            min-width: 300px;
            max-width: 400px;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .toast.show {
            transform: translateX(0);
        }

        .toast-success {
            background: #f0f9ff;
        }

        .toast-error {
            background: #fef2f2;
        }

        .toast-warning {
            background: #fffbeb;
        }

        .toast-icon {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 1.25rem;
        }

        .toast-success .toast-icon {
            background: #16a34a;
            color: white;
        }

        .toast-error .toast-icon {
            background: #dc2626;
            color: white;
        }

        .toast-warning .toast-icon {
            background: #f59e0b;
            color: white;
        }

        .toast-content {
            flex: 1;
        }

        .toast-title {
            font-size: 1rem;
            font-weight: 600;
            margin: 0 0 0.25rem 0;
            color: #1f2937;
        }

        .toast-message {
            font-size: 0.875rem;
            color: #6b7280;
            margin: 0;
            line-height: 1.4;
        }

        .toast-close {
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            padding: 0.25rem;
            border-radius: 0.25rem;
            transition: all 0.2s;
            flex-shrink: 0;
            font-size: 1.25rem;
            width: 1.5rem;
            height: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .toast-close:hover {
            color: #6b7280;
            background: #f3f4f6;
        }

        /* Progress bar cho auto-dismiss */
        .toast-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: rgba(0, 0, 0, 0.1);
            border-radius: 0 0 0.5rem 0.5rem;
            overflow: hidden;
        }

        .toast-progress-bar {
            height: 100%;
            width: 100%;
            animation: toast-progress 5s linear;
        }

        .toast-success .toast-progress-bar {
            background: #16a34a;
        }

        .toast-error .toast-progress-bar {
            background: #dc2626;
        }

        .toast-warning .toast-progress-bar {
            background: #f59e0b;
        }

        @keyframes toast-progress {
            from { width: 100%; }
            to { width: 0%; }
        }

        /* Container cho multiple toasts */
        .toast-container {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        /* Responsive cho mobile */
        @media (max-width: 640px) {
            .toast {
                min-width: 280px;
                margin-right: 1rem;
                left: 1rem;
                right: 1rem;
                transform: translateY(-100%);
            }

            .toast.show {
                transform: translateY(0);
            }

            .toast-container {
                left: 1rem;
                right: 1rem;
                top: 1rem;
            }
        }
        
        /* Modal Styles - Enhanced Version */
        .dtmodal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.6));
            backdrop-filter: blur(8px);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .dtmodal-overlay.dtmodal-active {
            opacity: 1;
            visibility: visible;
        }
        
        .dtmodal-container {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(255, 255, 255, 0.1);
            max-width: 420px;
            width: 90%;
            max-height: 90vh;
            overflow: hidden;
            transform: scale(0.8) translateY(20px);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }
        
        .dtmodal-active .dtmodal-container {
            transform: scale(1) translateY(0);
        }
        
        .dtmodal-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            
        }
        
        .dtmodal-header {
            padding: 2rem 2rem 1rem 2rem;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            position: relative;
        }
        
        .dtmodal-icon-wrapper {
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        .dtmodal-icon-wrapper::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 50%;
            padding: 2px;
            background: linear-gradient(135deg, currentColor, transparent);
            mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            mask-composite: xor;
            opacity: 0.3;
        }
        
        .dtmodal-success .dtmodal-icon-wrapper {
            background: linear-gradient(135deg, #dcfce7, #bbf7d0);
            color: #16a34a;
        }
        
        .dtmodal-warning .dtmodal-icon-wrapper {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            color: #f59e0b;
        }
        
        .dtmodal-error .dtmodal-icon-wrapper {
            background: linear-gradient(135deg, #fecaca, #fca5a5);
            color: #dc2626;
        }
        
        .dtmodal-info .dtmodal-icon-wrapper {
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            color: #3b82f6;
        }
        
        .dtmodal-icon {
            font-size: 1.5rem;
            z-index: 1;
        }
        
        .dtmodal-title-content {
            flex: 1;
        }
        
        .dtmodal-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin: 0 0 0.5rem 0;
            color: #1f2937;
            line-height: 1.4;
        }
        
        .dtmodal-subtitle {
            font-size: 0.95rem;
            color: #6b7280;
            margin: 0;
            line-height: 1.5;
        }
        
        .dtmodal-close {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            width: 2rem;
            height: 2rem;
            border: none;
            background: #f3f4f6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            color: #6b7280;
        }
        
        .dtmodal-close:hover {
            background: #e5e7eb;
            color: #374151;
            transform: scale(1.1);
        }
        
        .dtmodal-body {
            padding: 0 2rem 1.5rem 2rem;
        }
        
        .dtmodal-message {
            margin: 0;
            color: #4b5563;
            line-height: 1.6;
            font-size: 0.95rem;
        }
        
        .dtmodal-footer {
            padding: 1.5rem 2rem 2rem 2rem;
            display: flex;
            gap: 0.75rem;
            justify-content: flex-end;
        }
        
        .dtmodal-btn {
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid transparent;
            position: relative;
            overflow: hidden;
            min-width: 100px;
        }
        
        .dtmodal-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .dtmodal-btn:hover::before {
            left: 100%;
        }
        
        .dtmodal-btn-outline {
            background: white;
            color: #374151;
            border-color: #d1d5db;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }
        
        .dtmodal-btn-outline:hover {
            background: #f9fafb;
            border-color: #9ca3af;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .dtmodal-btn-primary {
            background: linear-gradient(135deg, #16a34a, #15803d);
            color: white;
            box-shadow: 0 4px 6px rgba(22, 163, 74, 0.25);
        }
        
        .dtmodal-btn-primary:hover {
            background: linear-gradient(135deg, #15803d, #166534);
            transform: translateY(-1px);
            box-shadow: 0 6px 12px rgba(22, 163, 74, 0.35);
        }
        
        .dtmodal-btn-primary:active {
            transform: translateY(0);
        }
        
        /* Toast Styles - Enhanced */
        .dtmodal-toast-container {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 10000;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .dtmodal-toast {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            padding: 1rem;
            min-width: 320px;
            max-width: 400px;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            transform: translateX(100%);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border-left: 4px solid;
            position: relative;
            overflow: hidden;
        }
        
        .dtmodal-toast::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, currentColor, transparent);
        }
        
        .dtmodal-toast.dtmodal-active {
            transform: translateX(0);
        }
        
        .dtmodal-toast-success {
            border-left-color: #16a34a;
            color: #ffff;
        }
        
        .dtmodal-toast-error {
            border-left-color: #dc2626;
            color: #dc2626;
        }
        
        .dtmodal-toast-warning {
            border-left-color: #f59e0b;
            color: #f59e0b;
        }
        
        .dtmodal-toast-info {
            border-left-color: #3b82f6;
            color: #3b82f6;
        }
        
        .dtmodal-toast-icon-wrapper {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: currentColor;
            color: white;
            flex-shrink: 0;
        }
        
        .dtmodal-toast-content {
            flex: 1;
        }
        
        .dtmodal-toast-title {
            font-size: 0.95rem;
            font-weight: 600;
            margin: 0 0 0.25rem 0;
            color: #1f2937;
        }
        
        .dtmodal-toast-message {
            font-size: 0.875rem;
            color: #6b7280;
            margin: 0;
            line-height: 1.4;
        }
        
        .dtmodal-toast-close {
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            padding: 0.25rem;
            border-radius: 0.25rem;
            transition: all 0.2s;
            flex-shrink: 0;
        }
        
        .dtmodal-toast-close:hover {
            color: #6b7280;
            background: #f3f4f6;
        }
        
        .dtmodal-toast-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: rgba(0, 0, 0, 0.1);
        }
        
        .dtmodal-toast-progress-bar {
            height: 100%;
            background: currentColor;
            width: 100%;
            animation: toast-progress 5s linear;
        }
        
        @keyframes toast-progress {
            from { width: 100%; }
            to { width: 0%; }
        }
        
        /* Animation cho mobile */
        @media (max-width: 640px) {
            .dtmodal-container {
                width: 95%;
                margin: 1rem;
            }
            
            .dtmodal-header,
            .dtmodal-body,
            .dtmodal-footer {
                padding-left: 1.5rem;
                padding-right: 1.5rem;
            }
            
            .dtmodal-toast {
                min-width: 280px;
                margin-right: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Loading Screen -->
    <div id="loading" class="loading">
        <div class="spinner"></div>
        <p>Đang tải bản đồ...</p>
    </div>

    <!-- Map Container -->
    <div id="map"></div>

    <!-- Header Overlay -->
    <div class="overlay header">
        <div class="header-controls">
            <a href="{{ route('driver.orders.index') }}" class="btn">
                <i data-lucide="arrow-left"></i>
                Quay lại
            </a>
            <div style="display: flex; gap: 0.5rem;">
                <span id="navigation-badge" class="badge badge-green hidden">
                    <i data-lucide="navigation"></i>
                    Đang điều hướng
                </span>
                <span class="badge badge-purple">
                    <i data-lucide="clock"></i>
                    <span id="remaining-time">0 phút</span>
                </span>
            </div>
        </div>

        <!-- Customer Info Card -->
        <div id="customer-info" class="card">
            <div class="customer-info">
                <div class="customer-name">
                    <i data-lucide="user"></i>
                    <span id="customer-name">Nguyễn Văn An</span>
                </div>
                <button class="btn btn-success" onclick="callCustomer()">
                    <i data-lucide="phone"></i>
                </button>
            </div>
            <div class="customer-address">
                <i data-lucide="map-pin"></i>
                <span id="customer-address">123 Đường Láng, Đống Đa, Hà Nội</span>
            </div>
            <div id="customer-note" class="note">
                <strong>Ghi chú:</strong> <span id="note-text">Giao hàng tại cổng chính, gọi điện trước khi đến</span>
            </div>
            <button class="btn w-full mt-2" onclick="toggleCustomerInfo()">
                <i data-lucide="chevron-down"></i>
                Ẩn
            </button>
        </div>

        <!-- Show Customer Info Button (when hidden) -->
        <button id="show-customer-btn" class="btn hidden" onclick="toggleCustomerInfo()">
            <i data-lucide="user"></i>
            Hiện thông tin khách
        </button>
    </div>

    <!-- Map Controls -->
    <div class="map-controls">
        <button class="btn btn-icon" onclick="zoomIn()">
            <i data-lucide="zoom-in"></i>
        </button>
        <button class="btn btn-icon" onclick="zoomOut()">
            <i data-lucide="zoom-out"></i>
        </button>
        <button class="btn btn-icon" onclick="centerOnUser()">
            <i data-lucide="locate"></i>
        </button>
    </div>

    <!-- Bottom Panel -->
    <div class="overlay bottom-panel">
        <!-- Pre-navigation Panel -->
        <div id="pre-navigation" class="card">
            <div class="navigation-info">
                <div class="info-item">
                    <div class="info-value" id="total-distance">0 km</div>
                    <div class="info-label">Khoảng cách</div>
                </div>
                <div class="info-item">
                    <div class="info-value" id="total-time">0 phút</div>
                    <div class="info-label">Thời gian dự kiến</div>
                </div>
            </div>
            <button id="start-navigation-btn" class="btn btn-primary w-full" onclick="startNavigation()" disabled>
                <i data-lucide="compass"></i>
                Bắt đầu điều hướng
            </button>
            <p id="status-text" class="text-center text-sm text-gray-500 mt-2">
                Đang lấy vị trí...
            </p>
        </div>

        <!-- Navigation Panel -->
        <div id="navigation-panel" class="card hidden">
            <div class="current-instruction">
                <div class="instruction-icon">
                    <i data-lucide="navigation"></i>
                </div>
                <div>
                    <div class="instruction-text" id="current-instruction">Tiếp tục đi thẳng</div>
                    <div class="instruction-distance">
                        Còn <span id="nav-distance">0 km</span> - <span id="nav-time">0 phút</span>
                    </div>
                </div>
            </div>
            <div class="action-buttons">
                <button class="btn" onclick="callCustomer()">
                    <i data-lucide="phone"></i>
                    Gọi khách
                </button>
                <button class="btn btn-success" onclick="completeDelivery()">
                    <i data-lucide="check-circle"></i>
                    Đã giao
                </button>
            </div>
        </div>
    </div>

    <script>
        // Mapbox access token
   mapboxgl.accessToken = "{{ config('services.mapbox.access_token') }}"
        // Global variables
        let map;
        let currentPosition = null;
        let order = null;
        let route = null;
        let navigationStarted = false;
        let watchId = null;
        let userMarker = null;
        let destinationMarker = null;

        // Fake order data
        const orderData = {
            id: 123,
            customer_name: "Trần Thị Bình", 
            customer_phone: "0912345678",
            delivery_address: "48 Tố Hữu, Nam Từ Liêm, Hà Nội",
            guest_latitude: 21.0189,
            guest_longitude: 105.7864,
            notes: "Giao trong giờ hành chính, gọi trước 15 phút"
        };

        // Initialize the application
        function init() {
            // Set order data
            order = orderData;
            updateCustomerInfo();

            // Initialize map
            initMap();

            // Get current location
            getCurrentLocation();

            // Initialize Lucide icons
            lucide.createIcons();
        }

        function initMap() {
            map = new mapboxgl.Map({
                container: 'map',
                style: 'mapbox://styles/mapbox/streets-v12',
                center: [order.guest_longitude, order.guest_latitude],
                zoom: 11 // Giảm zoom để có thể thấy rộng hơn
            });
        
            map.on('load', function() {
                document.getElementById('loading').classList.add('hidden');
                
                // Add destination marker
                if (order) {
                    destinationMarker = new mapboxgl.Marker({ color: '#ef4444' })
                        .setLngLat([order.guest_longitude, order.guest_latitude])
                        .setPopup(new mapboxgl.Popup().setHTML(`
                            <div>
                                <strong>${order.customer_name}</strong><br>
                                ${order.delivery_address}
                            </div>
                        `))
                        .addTo(map);
                }
            });
        }

        function getCurrentLocation() {
            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const { latitude, longitude } = position.coords;
                        currentPosition = { latitude, longitude };
                        
                        // Add user marker
                        if (userMarker) {
                            userMarker.remove();
                        }
                        userMarker = new mapboxgl.Marker({ color: '#3b82f6' })
                            .setLngLat([longitude, latitude])
                            .addTo(map);

                        // Fit map to show both locations
                        fitMapToBothLocations();

                        // Calculate route
                        calculateRoute();
                    },
                    function(error) {
                        console.error("Error getting location:", error);
                        // Use fake location for demo
                        const fakeLocation = { latitude: 21.0245, longitude: 105.8412 };
                        currentPosition = fakeLocation;
                        
                        if (userMarker) {
                            userMarker.remove();
                        }
                        userMarker = new mapboxgl.Marker({ color: '#3b82f6' })
                            .setLngLat([fakeLocation.longitude, fakeLocation.latitude])
                            .addTo(map);

                        // Fit map to show both locations
                        fitMapToBothLocations();

                        calculateRoute();
                        showToast("Thông báo", "Sử dụng vị trí giả để demo", "warning");
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 60000
                    }
                );
            } else {
                // Use fake location for demo
                const fakeLocation = { latitude: 21.0245, longitude: 105.8412 };
                currentPosition = fakeLocation;
                
                if (userMarker) {
                    userMarker.remove();
                }
                userMarker = new mapboxgl.Marker({ color: '#3b82f6' })
                    .setLngLat([fakeLocation.longitude, fakeLocation.latitude])
                    .addTo(map);

                // Fit map to show both locations
                fitMapToBothLocations();

                calculateRoute();
                showToast("Thông báo", "Sử dụng vị trí giả để demo", "warning");
            }
        }

        // Thêm hàm mới để fit map hiển thị cả hai vị trí
        function fitMapToBothLocations() {
            if (!currentPosition || !order) return;
            
            const bounds = new mapboxgl.LngLatBounds();
            
            // Thêm vị trí hiện tại vào bounds
            bounds.extend([currentPosition.longitude, currentPosition.latitude]);
            
            // Thêm vị trí giao hàng vào bounds
            bounds.extend([order.guest_longitude, order.guest_latitude]);
            
            // Fit map để hiển thị cả hai vị trí với padding
            map.fitBounds(bounds, {
                padding: {
                    top: 100,
                    bottom: 200, // Để không bị che bởi bottom panel
                    left: 50,
                    right: 50
                },
                maxZoom: 16 // Giới hạn zoom tối đa
            });
        }

        async function calculateRoute() {
            if (!currentPosition || !order) return;

            try {
                const start = `${currentPosition.longitude},${currentPosition.latitude}`;
                const end = `${order.guest_longitude},${order.guest_latitude}`;
                
                const response = await fetch(
                    `https://api.mapbox.com/directions/v5/mapbox/driving/${start};${end}?geometries=geojson&access_token=${mapboxgl.accessToken}`
                );
                
                const data = await response.json();
                
                if (data.routes && data.routes.length > 0) {
                    route = data.routes[0];
                    
                    // Update UI with route info
                    updateRouteInfo(route);
                    
                    // Enable start navigation button
                    document.getElementById('start-navigation-btn').disabled = false;
                    document.getElementById('status-text').textContent = "Sẵn sàng điều hướng";
                }
            } catch (error) {
                console.error("Error calculating route:", error);
                showToast("Lỗi tính toán đường đi", "Không thể tính toán đường đi. Vui lòng thử lại.", "error");
            }
        }

        function addRouteToMap(route) {
            // Remove existing route
            if (map.getSource('route')) {
                map.removeLayer('route');
                map.removeSource('route');
            }

            // Add route source and layer
            map.addSource('route', {
                type: 'geojson',
                data: {
                    type: 'Feature',
                    properties: {},
                    geometry: route.geometry
                }
            });

            map.addLayer({
                id: 'route',
                type: 'line',
                source: 'route',
                layout: {
                    'line-join': 'round',
                    'line-cap': 'round'
                },
                paint: {
                    'line-color': '#2563eb',
                    'line-width': 6,
                    'line-opacity': 0.8
                }
            });

            // Fit map to route
            const coordinates = route.geometry.coordinates;
            const bounds = coordinates.reduce(function (bounds, coord) {
                return bounds.extend(coord);
            }, new mapboxgl.LngLatBounds(coordinates[0], coordinates[0]));

            map.fitBounds(bounds, {
                padding: 50
            });
        }

        function updateRouteInfo(route) {
            const distance = formatDistance(route.distance);
            const time = formatTime(route.duration);
            
            document.getElementById('total-distance').textContent = distance;
            document.getElementById('total-time').textContent = time;
            document.getElementById('remaining-time').textContent = time;
        }

        function updateCustomerInfo() {
            if (!order) return;
            
            document.getElementById('customer-name').textContent = order.customer_name || "Khách hàng";
            document.getElementById('customer-address').textContent = order.delivery_address || "Địa chỉ giao hàng";
            
            if (order.notes) {
                const noteElement = document.getElementById('customer-note');
                if (noteElement) {
                    noteElement.classList.remove('hidden');
                    document.getElementById('note-text').textContent = order.notes;
                }
            }
        }

        function startNavigation() {
            navigationStarted = true;
            
            // Add route to map when navigation starts
            if (route) {
                addRouteToMap(route);
            }
            
            // Show navigation panel
            document.getElementById('pre-navigation').classList.add('hidden');
            document.getElementById('navigation-panel').classList.remove('hidden');
            document.getElementById('navigation-badge').classList.remove('hidden');
            
            // Start location tracking
            if ("geolocation" in navigator) {
                watchId = navigator.geolocation.watchPosition(
                    function(position) {
                        const { latitude, longitude } = position.coords;
                        currentPosition = { latitude, longitude };
                        
                        // Update user marker
                        if (userMarker) {
                            userMarker.setLngLat([longitude, latitude]);
                        }
                        
                        // Update remaining distance
                        updateNavigationInfo();
                        
                        // Center map on user during navigation
                        map.easeTo({
                            center: [longitude, latitude],
                            zoom: 18
                        });
                    },
                    function(error) {
                        console.error("Error tracking location:", error);
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 5000,
                        maximumAge: 1000
                    }
                );
            }
            
            showToast("Điều hướng", "Đã bắt đầu điều hướng", "success");
        }

        function updateNavigationInfo() {
            if (!currentPosition || !order) return;
            
            const distance = calculateDistance(
                currentPosition.latitude,
                currentPosition.longitude,
                order.guest_latitude,
                order.guest_longitude
            );
            
            const time = Math.round(distance / 40 * 60); // Assuming 40 km/h average speed
            
            document.getElementById('nav-distance').textContent = formatDistance(distance * 1000);
            document.getElementById('nav-time').textContent = formatTime(time * 60);
            document.getElementById('remaining-time').textContent = formatTime(time * 60);
        }

        function completeDelivery() {
            dtmodalCreateModal({
                type: 'success',
                title: 'Xác nhận hoàn thành',
                message: 'Bạn có chắc chắn đã giao hàng thành công cho khách hàng?',
                confirmText: 'Xác nhận',
                cancelText: 'Hủy bỏ',
                onConfirm: function() {
                    // Stop navigation
                    if (watchId) {
                        navigator.geolocation.clearWatch(watchId);
                    }
                    
                    // Show success toast
                    dtmodalShowToast('success', {
                        title: 'Thành công!',
                        message: 'Đã hoàn thành giao hàng'
                    });
                    
                    // Redirect back to orders list after 2 seconds
                    setTimeout(() => {
                        window.location.href = '{{ route("driver.orders.index") }}';
                    }, 2000);
                },
                onCancel: function() {
                    // Do nothing, just close modal
                }
            });
        }

        function callCustomer() {
            if (order && order.customer_phone) {
                if (confirm(`Gọi cho ${order.customer_name} (${order.customer_phone})?`)) {
                    window.location.href = `tel:${order.customer_phone}`;
                }
            } else {
                showToast("Lỗi", "Không có số điện thoại khách hàng", "error");
            }
        }

        function toggleCustomerInfo() {
            const customerInfo = document.getElementById('customer-info');
            const showBtn = document.getElementById('show-customer-btn');
            
            if (customerInfo.classList.contains('hidden')) {
                customerInfo.classList.remove('hidden');
                showBtn.classList.add('hidden');
            } else {
                customerInfo.classList.add('hidden');
                showBtn.classList.remove('hidden');
            }
        }

        // Map control functions
        function zoomIn() {
            map.zoomIn();
        }

        function zoomOut() {
            map.zoomOut();
        }

        function centerOnUser() {
            if (currentPosition) {
                map.flyTo({
                    center: [currentPosition.longitude, currentPosition.latitude],
                    zoom: 18
                });
            }
        }

        function goBack() {
            dtmodalCreateModal({
                type: 'warning',
                title: 'Xác nhận quay lại',
                message: 'Bạn có chắc muốn quay lại? Điều hướng sẽ bị dừng.',
                confirmText: 'Quay lại',
                cancelText: 'Tiếp tục',
                onConfirm: function() {
                    if (watchId) {
                        navigator.geolocation.clearWatch(watchId);
                    }
                    window.location.href = '{{ route("driver.orders.index") }}';
                }
            });
        }

        // Utility functions
        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371; // Radius of the Earth in kilometers
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                    Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                    Math.sin(dLon/2) * Math.sin(dLon/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return R * c;
        }

        function formatDistance(meters) {
            if (meters < 1000) {
                return Math.round(meters) + ' m';
            } else {
                return (meters / 1000).toFixed(1) + ' km';
            }
        }

        function formatTime(seconds) {
            const minutes = Math.round(seconds / 60);
            if (minutes < 60) {
                return minutes + ' phút';
            } else {
                const hours = Math.floor(minutes / 60);
                const remainingMinutes = minutes % 60;
                return hours + 'h ' + remainingMinutes + 'p';
            }
        }

        function showToast(title, message, type = 'info') {
            // Remove existing toast
            const existingToast = document.querySelector('.toast');
            if (existingToast) {
                existingToast.remove();
            }

            // Create toast element
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            
            const iconName = type === 'success' ? 'check-circle' : type === 'error' ? 'x-circle' : type === 'warning' ? 'alert-triangle' : 'info';
            
            toast.innerHTML = `
                <div class="toast-icon">
                    <i data-lucide="${iconName}"></i>
                </div>
                <div class="toast-content">
                    <div class="toast-title">${title}</div>
                    <div class="toast-message">${message}</div>
                </div>
                <button class="toast-close" onclick="this.parentElement.remove()">
                    <i data-lucide="x"></i>
                </button>
                <div class="toast-progress">
                    <div class="toast-progress-bar"></div>
                </div>
            `;

            // Add to page
            document.body.appendChild(toast);
            lucide.createIcons();

            // Show toast
            setTimeout(() => {
                toast.classList.add('show');
            }, 100);

            // Hide toast after 5 seconds
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 300);
            }, 5000);
        }

        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', init);
    </script>
</body>
</html>