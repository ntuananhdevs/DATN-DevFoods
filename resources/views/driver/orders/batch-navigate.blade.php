@extends('layouts.driver.masterLayout')

@section('title', 'Điều hướng ghép đơn')

@push('styles')
<!-- Mapbox GL JS -->
<link href='https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css' rel='stylesheet' />
<script src='https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js'></script>

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
<style>
    :root {
        --primary-color: #2563eb;
        --primary-hover: #1d4ed8;
        --success-color: #10b981;
        --success-hover: #059669;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
        --purple-color: #8b5cf6;
        --gray-50: #f9fafb;
        --gray-100: #f3f4f6;
        --gray-200: #e5e7eb;
        --gray-300: #d1d5db;
        --gray-400: #9ca3af;
        --gray-500: #6b7280;
        --gray-600: #4b5563;
        --gray-700: #374151;
        --gray-800: #1f2937;
        --gray-900: #111827;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        --border-radius: 12px;
        --border-radius-lg: 16px;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Header */
    .header {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        backdrop-filter: blur(10px);
        box-shadow: var(--shadow-md);
        border-bottom: 1px solid var(--gray-200);
        padding: 16px 20px;
        margin-bottom: 20px;
    }

    .header-content {
        display: flex;
        align-items: center;
        gap: 16px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .header-title {
        font-size: 20px;
        font-weight: 700;
        color: var(--gray-900);
        margin-bottom: 2px;
    }

    .header-subtitle {
        font-size: 14px;
        color: var(--gray-500);
        font-weight: 500;
    }

    /* Buttons */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 12px 20px;
        border: 1px solid var(--gray-300);
        border-radius: var(--border-radius);
        background: white;
        color: var(--gray-700);
        text-decoration: none;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        transition: var(--transition);
        box-shadow: var(--shadow-sm);
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
        color: white;
        border-color: var(--primary-color);
    }

    .btn-success {
        background: linear-gradient(135deg, var(--success-color) 0%, var(--success-hover) 100%);
        color: white;
        border-color: var(--success-color);
    }

    .btn-sm {
        padding: 8px 16px;
        font-size: 13px;
    }

    .btn-ghost {
        border: none;
        background: transparent;
        box-shadow: none;
    }

    .btn-ghost:hover {
        background: var(--gray-100);
        transform: none;
    }

    /* Cards */
    .card {
        background: white;
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow);
        border: 1px solid var(--gray-200);
        transition: var(--transition);
        overflow: hidden;
        margin-bottom: 20px;
    }

    .card:hover {
        box-shadow: var(--shadow-lg);
        transform: translateY(-2px);
    }

    .card-header {
        padding: 24px;
        border-bottom: 1px solid var(--gray-100);
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    }

    .card-content {
        padding: 24px;
    }

    .card-title {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 6px;
        color: var(--gray-900);
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .card-description {
        color: var(--gray-600);
        font-size: 15px;
        font-weight: 400;
    }

    /* Map styles */
    .map-container {
        position: relative;
        height: calc(100vh - 120px);
        min-height: 500px;
        background: var(--gray-100);
        border-radius: var(--border-radius);
        overflow: hidden;
    }

    #map {
        width: 100%;
        height: 100%;
    }

    #map-loading {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--gray-100);
        z-index: 10;
    }

    .loading-spinner {
        display: inline-block;
        width: 24px;
        height: 24px;
        border: 3px solid var(--gray-300);
        border-radius: 50%;
        border-top-color: var(--primary-color);
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Map Controls */
    .map-controls {
        position: absolute;
        top: 20px;
        right: 20px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        z-index: 10;
    }

    .control-group {
        display: flex;
        flex-direction: column;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-lg);
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .control-btn {
        width: 40px;
        height: 40px;
        border: none;
        background: transparent;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: var(--transition);
        color: var(--gray-700);
        font-size: 16px;
    }

    .control-btn:hover {
        background: var(--primary-color);
        color: white;
    }

    .control-btn + .control-btn {
        border-top: 1px solid var(--gray-200);
    }

    /* Route Controls */
    .route-controls {
        position: absolute;
        top: 20px;
        left: 20px;
        z-index: 10;
    }

    .route-toggle {
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 10px 16px;
        border-radius: var(--border-radius);
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: var(--shadow-lg);
        transition: var(--transition);
    }

    .route-toggle:hover {
        background: var(--primary-hover);
        transform: translateY(-2px);
    }

    .route-toggle.hidden {
        background: var(--gray-600);
    }

    /* Stats */
    .grid-3 {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }

    .stat-item {
        text-align: center;
        padding: 20px;
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-radius: var(--border-radius);
        border: 1px solid var(--gray-200);
        transition: var(--transition);
    }

    .stat-item:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }

    .stat-value {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .stat-label {
        font-size: 14px;
        color: var(--gray-600);
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Legend */
    .legend {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        justify-content: center;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        background: var(--gray-50);
        border-radius: var(--border-radius);
        border: 1px solid var(--gray-200);
    }

    .legend-icon {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 12px;
        font-weight: 700;
        box-shadow: var(--shadow-sm);
    }

    /* Badges */
    .badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge-blue { 
        background: #dbeafe; 
        color: #1e40af; 
    }
    .badge-green { 
        background: #d1fae5; 
        color: #065f46; 
    }
    .badge-yellow { 
        background: #fef3c7; 
        color: #92400e; 
    }
    .badge-purple { 
        background: #e9d5ff; 
        color: #6b21a8; 
    }

    /* Responsive */
    @media (max-width: 768px) {
        .map-container {
            height: calc(100vh - 140px);
            min-height: 400px;
        }
        
        .map-controls {
            top: 16px;
            right: 16px;
        }
        
        .route-controls {
            top: 16px;
            left: 16px;
        }
        
        .control-btn {
            width: 36px;
            height: 36px;
        }
        
        .grid-3 {
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }
    }

    /* Customer List Styles */
    .customer-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        padding: 20px;
    }

    .customer-card {
        background: white;
        border: 2px solid var(--gray-200);
        border-radius: 16px;
        transition: var(--transition);
        cursor: pointer;
        position: relative;
        overflow: hidden;
        padding: 20px;
    }

    .customer-card:hover {
        border-color: var(--primary-color);
        box-shadow: var(--shadow-xl);
        transform: translateY(-4px);
    }

    .customer-card.collapsed {
        padding: 12px 20px;
    }

    .customer-card.collapsed .customer-details,
    .customer-card.collapsed .order-meta,
    .customer-card.collapsed .customer-notes,
    .customer-card.collapsed .customer-actions {
        display: none;
    }

    .customer-card.collapsed .customer-avatar {
        width: 40px;
        height: 40px;
        margin: 0 auto 8px auto;
        margin-top: 0;
        font-size: 16px;
    }

    .customer-card.collapsed .customer-name {
        font-size: 14px;
        margin-bottom: 4px;
    }

    .customer-card.collapsed .customer-phone {
        font-size: 12px;
        margin-bottom: 4px;
    }

    .customer-card.collapsed .customer-rating {
        font-size: 12px;
        margin-bottom: 8px;
    }

    .customer-card.collapsed .order-status {
        margin: 8px 0 0 0;
        padding: 4px 0 0 0;
    }

    .expand-indicator {
        position: absolute;
        bottom: 8px;
        right: 12px;
        color: var(--gray-400);
        font-size: 12px;
        transition: var(--transition);
    }

    .customer-card.collapsed .expand-indicator::after {
        content: "Nhấn để xem chi tiết";
    }

    .customer-card:not(.collapsed) .expand-indicator::after {
        content: "Nhấn để thu gọn";
    }

    .customer-card:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .customer-card.selected {
        border-color: var(--primary-color);
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.05) 0%, rgba(37, 99, 235, 0.02) 100%);
    }

    .customer-number {
        position: absolute;
        top: 12px;
        left: 12px;
        width: 28px;
        height: 28px;
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 700;
        box-shadow: var(--shadow-md);
    }

    .customer-card.collapsed .customer-number {
        position: static;
        margin: 0 auto 8px auto;
        width: 20px;
        height: 20px;
        font-size: 10px;
    }

    .customer-avatar {
        width: 64px;
        height: 64px;
        background: linear-gradient(135deg, var(--gray-100) 0%, var(--gray-200) 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px auto;
        margin-top: 24px;
        border: 4px solid white;
        box-shadow: var(--shadow-lg);
        font-size: 24px;
        font-weight: 700;
        color: var(--gray-600);
    }

    .customer-name {
        font-size: 16px;
        font-weight: 700;
        color: var(--gray-900);
        margin: 0 0 8px 0;
        text-align: center;
        line-height: 1.2;
    }

    .customer-phone {
        font-size: 14px;
        color: var(--gray-600);
        text-align: center;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .customer-rating {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        color: var(--warning-color);
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 16px;
    }

    .customer-details {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 16px;
    }

    @media (max-width: 768px) {
        .customer-details {
            grid-template-columns: 1fr;
        }
    }

    .address-section {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 12px;
        background: var(--gray-50);
        border-radius: 8px;
    }

    .address-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 14px;
        flex-shrink: 0;
    }

    .pickup-icon {
        background: linear-gradient(135deg, var(--success-color) 0%, #059669 100%);
    }

    .delivery-icon {
        background: linear-gradient(135deg, var(--danger-color) 0%, #dc2626 100%);
    }

    .address-info {
        flex: 1;
    }

    .address-label {
        font-size: 12px;
        font-weight: 600;
        color: var(--gray-600);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }

    .address-text {
        font-size: 14px;
        color: var(--gray-900);
        line-height: 1.4;
    }

    .address-time {
        font-size: 12px;
        color: var(--gray-600);
        margin-top: 4px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .order-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
        margin-bottom: 12px;
        justify-content: center;
    }

    .order-value {
        font-size: 16px;
        font-weight: 600;
        color: var(--success-color);
    }

    .order-items {
        font-size: 14px;
        color: var(--gray-600);
        text-align: center;
        margin-bottom: 8px;
    }

    .priority-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .priority-normal {
        background: var(--gray-100);
        color: var(--gray-700);
    }

    .priority-urgent {
        background: #fef3c7;
        color: #92400e;
    }

    .customer-notes {
        font-size: 14px;
        color: var(--gray-700);
        background: #dbeafe;
        padding: 12px;
        border-radius: 8px;
        border-left: 4px solid var(--primary-color);
        margin-top: 12px;
    }

    .customer-actions {
        display: flex;
        gap: 8px;
        margin-top: 16px;
    }

    .action-btn {
        flex: 1;
        padding: 10px 16px;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .action-btn-primary {
        background: var(--primary-color);
        color: white;
    }

    .action-btn-primary:hover {
        background: var(--primary-hover);
    }

    .action-btn-secondary {
        background: var(--gray-100);
        color: var(--gray-700);
    }

    .action-btn-secondary:hover {
        background: var(--gray-200);
    }

    /* Collapsible functionality */
    .collapsible-header {
        cursor: pointer;
        transition: var(--transition);
    }

    .collapsible-header:hover {
        background: var(--gray-50);
    }

    .collapsible-icon {
        transition: transform 0.3s ease;
    }

    .collapsible-icon.collapsed {
        transform: rotate(180deg);
    }

    .flex {
        display: flex;
    }

    .items-center {
        align-items: center;
    }

    .justify-between {
        justify-content: space-between;
    }

    .truncate {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .text-center {
        text-align: center;
    }

    .text-xs {
        font-size: 12px;
    }

    .mb-2 {
        margin-bottom: 8px;
    }

    /* Focus styles for accessibility */
    .btn:focus,
    .control-btn:focus {
        outline: 2px solid var(--primary-color);
        outline-offset: 2px;
    }

    /* Order status styles */
    .order-status {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 12px 0;
        padding: 8px 0;
        border-top: 1px solid var(--gray-200);
    }

    .status-label {
        font-weight: 500;
        color: var(--gray-700);
        font-size: 14px;
    }

    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-pending {
        background: #fef3c7;
        color: #92400e;
    }

    .status-confirmed {
        background: #dbeafe;
        color: #1e40af;
    }

    .status-preparing {
        background: #fde68a;
        color: #d97706;
    }

    .status-ready {
        background: #d1fae5;
        color: #065f46;
    }

    .status-picked-up {
        background: #e0e7ff;
        color: #3730a3;
    }

    .status-delivering {
        background: #fed7d7;
        color: #c53030;
    }

    .status-delivered {
        background: #c6f6d5;
        color: #22543d;
    }

    .status-cancelled {
        background: #fed7d7;
        color: #c53030;
    }

    /* Status action buttons */
    .status-actions {
        display: flex;
        gap: 8px;
        margin-top: 8px;
        flex-wrap: wrap;
    }

    .status-btn {
        padding: 6px 12px;
        border: none;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .status-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .status-btn-confirm {
        background: #3b82f6;
        color: white;
    }

    .status-btn-prepare {
        background: #f59e0b;
        color: white;
    }

    .status-btn-ready {
        background: #10b981;
        color: white;
    }

    .status-btn-pickup {
        background: #8b5cf6;
        color: white;
    }

    .status-btn-deliver {
        background: #ef4444;
        color: white;
    }

    .status-btn-complete {
        background: #059669;
        color: white;
    }

    .status-btn-received {
        background: #8b5cf6;
        color: white;
    }

    .status-btn-cancel {
        background: #dc2626;
        color: white;
    }

    /* Notification animations */
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }

    /* Batch Controls Styles */
    .batch-status-info {
        background: var(--gray-50);
        border: 1px solid var(--gray-200);
        border-radius: var(--border-radius);
        padding: 16px;
        margin-bottom: 16px;
    }

    .batch-status-summary {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
    }

    .batch-status-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        color: white;
        font-size: 14px;
    }

    .batch-status-text {
        flex: 1;
    }

    .batch-status-title {
        font-weight: 600;
        color: var(--gray-900);
        margin-bottom: 4px;
    }

    .batch-status-description {
        font-size: 14px;
        color: var(--gray-600);
    }

    .batch-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }

    .batch-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 20px;
        border: 1px solid var(--gray-300);
        border-radius: var(--border-radius);
        background: white;
        color: var(--gray-700);
        text-decoration: none;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        transition: var(--transition);
        box-shadow: var(--shadow-sm);
        min-width: 160px;
        justify-content: center;
    }

    .batch-btn:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .batch-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
        box-shadow: var(--shadow-sm);
    }

    .batch-btn-primary {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
        color: white;
        border-color: var(--primary-color);
    }

    .batch-btn-success {
        background: linear-gradient(135deg, var(--success-color) 0%, var(--success-hover) 100%);
        color: white;
        border-color: var(--success-color);
    }

    .batch-btn-warning {
        background: linear-gradient(135deg, var(--warning-color) 0%, #d97706 100%);
        color: white;
        border-color: var(--warning-color);
    }

    .batch-btn-danger {
        background: linear-gradient(135deg, var(--danger-color) 0%, #dc2626 100%);
        color: white;
        border-color: var(--danger-color);
    }
</style>
@endpush

@section('content')
<div class="pt-4 p-4">
    <!-- Header -->
    <div class="header">
        <div class="header-content">
            <button class="btn btn-ghost btn-sm" onclick="history.back()">
                <i data-lucide="arrow-left" style="width: 20px; height: 20px;"></i>
            </button>
            <div>
                <h1 class="header-title">Đơn hàng ghép #{{ $batchGroupId }}</h1>
                <p class="header-subtitle">{{ $batchOrders->count() }} khách hàng • Tuyến đường tối ưu • 
                @php
                    $totalDistance = 0;
                    $totalValue = $batchOrders->sum('total_amount');
                    // Tính tổng khoảng cách ước tính giữa các điểm
                    for ($i = 0; $i < $batchOrders->count() - 1; $i++) {
                        $order1 = $batchOrders[$i];
                        $order2 = $batchOrders[$i + 1];
                        
                        $lat1 = $order1->address->latitude ?? $order1->guest_latitude ?? 0;
                        $lng1 = $order1->address->longitude ?? $order1->guest_longitude ?? 0;
                        $lat2 = $order2->address->latitude ?? $order2->guest_latitude ?? 0;
                        $lng2 = $order2->address->longitude ?? $order2->guest_longitude ?? 0;
                        
                        if ($lat1 && $lng1 && $lat2 && $lng2) {
                            $earthRadius = 6371;
                            $dLat = deg2rad($lat2 - $lat1);
                            $dLng = deg2rad($lng2 - $lng1);
                            $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng/2) * sin($dLng/2);
                            $c = 2 * atan2(sqrt($a), sqrt(1-$a));
                            $totalDistance += $earthRadius * $c;
                        }
                    }
                @endphp
                {{ number_format($totalDistance, 1) }}km</p>
            </div>
        </div>
    </div>

    <!-- Map Section -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">
                <i data-lucide="map-pin" style="width: 24px; height: 24px; color: var(--primary-color);"></i>
                Bản đồ tuyến đường
            </h2>
            <p class="card-description">Theo dõi các điểm lấy hàng (P) và giao hàng (D) với tuyến đường được tối ưu hóa</p>
        </div>
        <div class="card-content" style="padding: 0;">
            <div class="map-container">
                <div id="map-loading">
                    <div style="text-align: center;">
                        <div class="loading-spinner" style="margin-bottom: 16px;"></div>
                        <p style="color: var(--gray-600); font-weight: 500;">Đang tải bản đồ và tuyến đường...</p>
                    </div>
                </div>
                <div id="map"></div>
                
                <!-- Route Controls -->
                <div class="route-controls">
                    <div class="route-toggle active" id="routeToggle" onclick="toggleRoute()">
                        <i data-lucide="route" style="width: 18px; height: 18px;"></i>
                        <span>Hiện tuyến đường</span>
                    </div>
                </div>
                
                <!-- Map Controls -->
                <div class="map-controls">
                    <!-- Zoom Controls -->
                    <div class="control-group">
                        <button class="control-btn" onclick="zoomIn()" title="Phóng to">
                            <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
                        </button>
                        <button class="control-btn" onclick="zoomOut()" title="Thu nhỏ">
                            <i data-lucide="minus" style="width: 18px; height: 18px;"></i>
                        </button>
                    </div>

                    <!-- Location Controls -->
                    <button class="control-btn" onclick="goToUserLocation()" title="Về vị trí hiện tại">
                        <i data-lucide="locate" style="width: 18px; height: 18px;"></i>
                    </button>

                    <button class="control-btn" onclick="fitAllMarkers()" title="Xem tất cả điểm">
                        <i data-lucide="map-pin" style="width: 18px; height: 18px;"></i>
                    </button>

                    <button class="control-btn" id="mapStyleBtn" onclick="toggleMapStyle()" title="Chuyển đổi kiểu bản đồ">
                        <i data-lucide="satellite" style="width: 18px; height: 18px;"></i>
                    </button>

                    <button class="control-btn" id="navigationBtn" onclick="toggleNavigationMode()" title="Chế độ điều hướng">
                        <i data-lucide="compass" style="width: 18px; height: 18px;"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Overview -->
    <div class="card">
        <div class="card-header">
            <div class="flex items-center justify-between">
                <h2 class="card-title">Tổng quan đơn hàng</h2>
                <span class="badge badge-blue">Đã nhận</span>
            </div>
            <p class="card-description">Thông tin tổng hợp về đơn hàng ghép này</p>
        </div>
        <div class="card-content">
            <div class="grid-3">
                <div class="stat-item">
                    <div class="stat-value" style="color: var(--success-color);">{{ number_format($batchOrders->sum('total_amount')) }}đ</div>
                    <div class="stat-label">Tổng giá trị</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" style="color: var(--primary-color);">{{ $batchOrders->count() }} đơn</div>
                    <div class="stat-label">Số đơn hàng</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" style="color: var(--warning-color);" id="total-distance">{{ number_format($totalDistance, 1) }} km</div>
                    <div class="stat-label">Tổng khoảng cách</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer List -->
    <div class="card">
        <div class="card-header collapsible-header" onclick="toggleCustomerList()">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="card-title">
                        <i data-lucide="users" style="width: 24px; height: 24px; color: var(--primary-color);"></i>
                        Danh sách khách hàng
                    </h2>
                    <p class="card-description">Chi tiết thông tin từng khách hàng trong đơn hàng ghép</p>
                </div>
                <i id="customerListIcon" data-lucide="chevron-up" class="collapsible-icon" style="width: 24px; height: 24px; color: var(--gray-500);"></i>
            </div>
        </div>
        <div class="card-content" id="customerListContent">
            <div class="customer-list" id="customer-list">
                <!-- Customer items will be dynamically generated here -->
            </div>
        </div>
    </div>

    <!-- Batch Controls -->
    <div class="card" id="batch-controls">
        <div class="card-header">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="card-title">
                        <i data-lucide="settings" style="width: 24px; height: 24px; color: var(--primary-color);"></i>
                        Điều khiển đơn ghép
                    </h2>
                    <p class="card-description">Thay đổi trạng thái tất cả đơn hàng trong batch cùng lúc</p>
                </div>
            </div>
        </div>
        <div class="card-content">
            <div class="batch-status-info" id="batch-status-info">
                <!-- Batch status info will be dynamically generated here -->
            </div>
            <div class="batch-actions" id="batch-actions">
                <!-- Batch action buttons will be dynamically generated here -->
            </div>
        </div>
    </div>

    <!-- Map Legend -->
    <div class="card">
        <div class="card-content">
            <h3 class="font-semibold text-gray-900 mb-4" style="font-size: 18px;">Chú thích bản đồ</h3>
            <div class="legend">
                <div class="legend-item">
                    <div class="legend-icon" style="background: linear-gradient(135deg, var(--success-color) 0%, #059669 100%);">P</div>
                    <span style="font-weight: 500;">Điểm lấy hàng</span>
                </div>
                <div class="legend-item">
                    <div class="legend-icon" style="background: linear-gradient(135deg, var(--danger-color) 0%, #dc2626 100%);">D</div>
                    <span style="font-weight: 500;">Điểm giao hàng</span>
                </div>
                <div class="legend-item">
                    <div class="legend-icon" style="background: linear-gradient(135deg, var(--warning-color) 0%, #d97706 100%);">!</div>
                    <span style="font-weight: 500;">Đơn hàng gấp</span>
                </div>
                <div class="legend-item">
                    <div class="legend-icon" style="background: linear-gradient(135deg, var(--primary-color) 0%, #1d4ed8 100%);">
                        <i data-lucide="user" style="width: 12px; height: 12px;"></i>
                    </div>
                    <span style="font-weight: 500;">Vị trí tài xế</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Toast Container --}}
<div id="dtmodal-toast-container" class="fixed top-4 right-4 z-[100] space-y-2"></div>

{{-- Confirmation Modal --}}
<div id="confirmationModal"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-sm">
        <div class="p-6 text-center">
            <div id="modalIcon"
                class="mx-auto w-12 h-12 rounded-full flex items-center justify-center text-xl bg-blue-100 text-blue-600 mb-4">
                <i class="fas fa-question text-2xl"></i>
            </div>
            <h3 id="modalTitle" class="text-lg font-semibold text-gray-900">Tiêu đề Modal</h3>
            <p id="modalMessage" class="text-sm text-gray-500 mt-2">Nội dung modal.</p>
        </div>
        <div class="flex items-center bg-gray-50 px-6 py-4 gap-3 rounded-b-lg">
            <button id="modalCancel" type="button"
                class="w-full py-2 px-4 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">Hủy
                bỏ</button>
            <button id="modalConfirm" type="button"
                class="w-full py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">Xác
                nhận</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Lucide Icons -->
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

<script>
    // Initialize Mapbox access token
    mapboxgl.accessToken = "{{ config('services.mapbox.access_token') }}" || 'pk.eyJ1IjoibmhhdG5ndXllbnF2IiwiYSI6ImNtYjZydDNnZDAwY24ybm9qcTdxcTNocG8ifQ.u7X_0DfN7d52xZ8cGFbWyQ';

    // Real data from database
    const customers = [
        @foreach($batchOrders as $order)
        {
            id: 'ORD{{ $order->id }}',
            name: '{{ $order->customer_name }}',
            phone: '{{ $order->customer_phone }}',
            rating: {{ $order->customer && $order->customer->driverRatings->count() > 0 ? number_format($order->customer->driverRatings->avg('rating'), 1) : 5.0 }},
            pickupAddress: '{{ $order->branch->address ?? "Địa chỉ chi nhánh" }}',
            deliveryAddress: '{{ $order->display_full_delivery_address }}',
            items: '{{ $order->orderItems->map(function($item) { return $item->quantity . "x " . $item->product_name; })->implode(", ") }}',
            orderValue: {{ $order->total_amount }},
            notes: '{{ $order->notes ?? "Không có ghi chú" }}',
            priority: '{{ $order->is_urgent ? "urgent" : "normal" }}',
            estimatedPickupTime: '{{ $order->pickup_time ? \Carbon\Carbon::parse($order->pickup_time)->format("H:i") : "Chưa xác định" }}',
            estimatedDeliveryTime: '{{ $order->delivery_time ? \Carbon\Carbon::parse($order->delivery_time)->format("H:i") : "Chưa xác định" }}',
            orderStatus: '{{ $order->status }}',
            pickupCoords: [
                {{ $order->branch && $order->branch->longitude ? $order->branch->longitude : 106.7017 }}, 
                {{ $order->branch && $order->branch->latitude ? $order->branch->latitude : 10.7769 }}
            ],
            deliveryCoords: [
                {{ $order->address && $order->address->longitude ? $order->address->longitude : ($order->guest_longitude ?? 106.6953) }}, 
                {{ $order->address && $order->address->latitude ? $order->address->latitude : ($order->guest_latitude ?? 10.7756) }}
            ],
            pickupCoordinates: [
                {{ $order->branch && $order->branch->longitude ? $order->branch->longitude : 106.7017 }}, 
                {{ $order->branch && $order->branch->latitude ? $order->branch->latitude : 10.7769 }}
            ],
            deliveryCoordinates: [
                {{ $order->address && $order->address->longitude ? $order->address->longitude : ($order->guest_longitude ?? 106.6953) }}, 
                {{ $order->address && $order->address->latitude ? $order->address->latitude : ($order->guest_latitude ?? 10.7756) }}
            ],
            status: '{{ $order->status }}'
        }@if(!$loop->last),@endif
        @endforeach
    ];

    // Global variables
    let map;
    let mapStyle = 'streets';
    let isNavigationMode = false;
    let userLocation = null;
    let showRoute = true;
    let routeCoordinates = [];
    let routeInfo = {
        distance: 0,
        duration: 0
    };

    // Global utility function for showing toasts
    window.showToast = function(type, options) {
        const toastContainer = document.getElementById('dtmodal-toast-container');
        if (!toastContainer) {
            console.error('Toast container not found!');
            return;
        }

        const toastId = 'toast-' + Date.now();
        const toastElement = document.createElement('div');
        toastElement.id = toastId;
        toastElement.className =
            `relative flex items-center w-full max-w-xs p-4 rounded-lg shadow-md mt-2 text-white transform transition-all ease-out duration-300 translate-x-full opacity-0`;
        let bgColor = '';
        let iconClass = '';
        switch (type) {
            case 'success':
                bgColor = 'bg-green-500';
                iconClass = 'fas fa-check-circle';
                break;
            case 'error':
                bgColor = 'bg-red-500';
                iconClass = 'fas fa-times-circle';
                break;
            case 'warning':
                bgColor = 'bg-yellow-500';
                iconClass = 'fas fa-exclamation-triangle';
                break;
            case 'info':
                bgColor = 'bg-blue-500';
                iconClass = 'fas fa-info-circle';
                break;
            default:
                bgColor = 'bg-gray-700';
                iconClass = 'fas fa-bell';
        }

        toastElement.classList.add(bgColor);
        toastElement.innerHTML = `
            <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg">
                <i class="${iconClass}"></i>
            </div>
            <div class="ml-3 text-sm font-normal">
                ${options.title ? `<p class="font-bold">${options.title}</p>` : ''}
                ${options.message}
            </div>
            <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-transparent text-white rounded-lg p-1.5 inline-flex items-center justify-center h-8 w-8" data-dismiss-target="#${toastId}" aria-label="Close">
                <span class="sr-only">Close</span>
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
            </button>
        `;

        toastContainer.appendChild(toastElement);
        // Animate in
        setTimeout(() => {
            toastElement.classList.remove('translate-x-full', 'opacity-0');
            toastElement.classList.add('translate-x-0', 'opacity-100');
        }, 100);
        // Auto-dismiss
        const duration = options.duration || 5000;
        setTimeout(() => {
            toastElement.classList.remove('translate-x-0', 'opacity-100');
            toastElement.classList.add('translate-x-full', 'opacity-0');
            toastElement.addEventListener('transitionend', () => toastElement.remove());
        }, duration);
        // Manual dismiss
        toastElement.querySelector('[data-dismiss-target]').addEventListener('click', () => {
            toastElement.classList.remove('translate-x-0', 'opacity-100');
            toastElement.classList.add('translate-x-full', 'opacity-0');
            toastElement.addEventListener('transitionend', () => toastElement.remove());
        });
    };

    // Global utility function for showing modals
    window.showModal = function(title, message, onConfirm, options = {}) {
        const modal = document.getElementById('confirmationModal');
        const modalTitle = document.getElementById('modalTitle');
        const modalMessage = document.getElementById('modalMessage');
        const modalIcon = document.getElementById('modalIcon');
        const modalConfirmBtn = document.getElementById('modalConfirm');
        const modalCancelBtn = document.getElementById('modalCancel');
        if (!modal || !modalTitle || !modalMessage || !modalConfirmBtn || !modalCancelBtn || !modalIcon) {
            console.error('Modal elements not found!');
            return;
        }

        modalTitle.textContent = title;
        modalMessage.textContent = message;
        // Set icon and colors
        modalIcon.className = 'mx-auto w-12 h-12 rounded-full flex items-center justify-center mb-4';
        modalIcon.innerHTML = `<i class="${options.icon || 'fas fa-question'} text-2xl"></i>`;
        
        // Set icon background and text colors
        const iconBgColors = {
            'blue': 'bg-blue-100 text-blue-600',
            'green': 'bg-green-100 text-green-600',
            'red': 'bg-red-100 text-red-600',
            'purple': 'bg-purple-100 text-purple-600',
            'yellow': 'bg-yellow-100 text-yellow-600'
        };
        modalIcon.className += ' ' + (iconBgColors[options.iconColor] || 'bg-blue-100 text-blue-600');
        
        modalConfirmBtn.textContent = options.confirmText || 'Đồng ý';
        
        // Set confirm button colors
        const confirmColors = {
            'blue': 'bg-blue-600 hover:bg-blue-700 text-white',
            'green': 'bg-green-600 hover:bg-green-700 text-white',
            'red': 'bg-red-600 hover:bg-red-700 text-white',
            'purple': 'bg-purple-600 hover:bg-purple-700 text-white'
        };
        modalConfirmBtn.className = `w-full py-2 px-4 rounded-md shadow-sm text-sm font-medium transition ${confirmColors[options.confirmColor] || 'bg-blue-600 hover:bg-blue-700 text-white'}`;

        modalCancelBtn.textContent = options.cancelText || 'Hủy bỏ';

        // Show modal
        modal.classList.remove('hidden');

        // Handle confirm
        const handleConfirm = () => {
            modal.classList.add('hidden');
            if (onConfirm) onConfirm();
            modalConfirmBtn.removeEventListener('click', handleConfirm);
            modalCancelBtn.removeEventListener('click', handleCancel);
        };

        // Handle cancel
        const handleCancel = () => {
            modal.classList.add('hidden');
            modalConfirmBtn.removeEventListener('click', handleConfirm);
            modalCancelBtn.removeEventListener('click', handleCancel);
        };

        modalConfirmBtn.addEventListener('click', handleConfirm);
        modalCancelBtn.addEventListener('click', handleCancel);

        // Close on backdrop click
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                handleCancel();
            }
        });
    };

    // Calculate optimal route using Mapbox Directions API
    async function calculateOptimalRoute() {
        // Check if any customer is in delivery mode (in_transit or delivered)
        const deliveringCustomers = customers.filter(customer => customer.orderStatus === 'in_transit' || customer.orderStatus === 'delivered');
        
        if (deliveringCustomers.length > 0) {
            // If any order is in delivery mode, show route to delivery points only
            return await calculateDeliveryRoute();
        } else {
            // Normal route calculation for pickup and delivery
            return await calculateFullRoute();
        }
    }
    
    // Calculate route for delivery only (after pickup)
    async function calculateDeliveryRoute() {
        // Start from user location
        const startPoint = userLocation || customers[0].deliveryCoords;
        
        // Get delivery points for all orders
        const deliveryPoints = customers
            .map(customer => customer.deliveryCoords);
        
        if (deliveryPoints.length === 0) {
            return [startPoint];
        }
        
        // Create waypoints: Start -> D1 -> D2 -> D3...
        const waypoints = [startPoint, ...deliveryPoints];
        
        return await getRouteFromAPI(waypoints);
    }
    
    // Calculate full route (pickup and delivery)
    async function calculateFullRoute() {
        // Start from user location if available, otherwise use first pickup
        const startPoint = userLocation || customers[0].pickupCoords;
        
        // Create waypoints: Start -> P1 -> D1 -> P2 -> D2 -> P3 -> D3
        const waypoints = [startPoint];
        
        // Add pickup and delivery points in order
        customers.forEach(customer => {
            // Only add pickup point if not in delivery mode
            if (customer.orderStatus !== 'in_transit' && customer.orderStatus !== 'delivered') {
                waypoints.push(customer.pickupCoords);
            }
            waypoints.push(customer.deliveryCoords);
        });
        
        return await getRouteFromAPI(waypoints);
    }
    
    // Helper function to get route from Mapbox API
    async function getRouteFromAPI(waypoints) {
        // Convert waypoints to string format for API
        const coordinatesString = waypoints.map(coord => coord.join(',')).join(';');
        
        try {
            const response = await fetch(`https://api.mapbox.com/directions/v5/mapbox/driving/${coordinatesString}?geometries=geojson&overview=full&steps=true&access_token=${mapboxgl.accessToken}`);
            const data = await response.json();
            
            if (data.routes && data.routes.length > 0) {
                const route = data.routes[0];
                
                // Update route info
                routeInfo.distance = (route.distance / 1000).toFixed(1); // Convert to km
                routeInfo.duration = Math.round(route.duration / 60); // Convert to minutes
                
                // Update UI with route info
                updateRouteInfo();
                
                return route.geometry.coordinates;
            } else {
                // Fallback to straight lines if API fails
                return waypoints;
            }
        } catch (error) {
            console.error('Error fetching route:', error);
            // Fallback to straight lines if API fails
            return waypoints;
        }
    }

    // Initialize map
    function initMap() {
        // Calculate center point
        const allCoords = customers.flatMap(customer => [
            customer.pickupCoords,
            customer.deliveryCoords
        ]);
        const centerLng = allCoords.reduce((sum, coord) => sum + coord[0], 0) / allCoords.length;
        const centerLat = allCoords.reduce((sum, coord) => sum + coord[1], 0) / allCoords.length;

        map = new mapboxgl.Map({
            container: 'map',
            style: 'mapbox://styles/mapbox/streets-v12',
            center: [centerLng, centerLat],
            zoom: 13
        });

        map.on('load', async () => {
            // Hide loading spinner
            const loadingElement = document.getElementById('map-loading');
            if (loadingElement) {
                loadingElement.style.display = 'none';
            }
            
            // Ensure map is visible
            const mapElement = document.getElementById('map');
            if (mapElement) {
                mapElement.style.display = 'block';
                mapElement.style.visibility = 'visible';
                mapElement.style.opacity = '1';
            }
            
            // Add route source and layer
            await addRouteLayer();
            addMarkers();
            
            // Show all delivery markers
            setTimeout(() => {
                showDeliveryMarkers();
            }, 100);
            
            fitAllMarkers();
            
            // Force map resize to ensure proper display
            setTimeout(() => {
                map.resize();
            }, 100);
        });
    }

    // Add route layer to map
    async function addRouteLayer() {
        // Remove existing route layers and sources if they exist
        removeExistingRouteLayers();
        
        const route = await calculateOptimalRoute();
        
        // Check if any customer is in delivery mode
        const deliveringCustomers = customers.filter(customer => 
            customer.orderStatus === 'in_transit' || 
            customer.orderStatus === 'delivered'
        );
        
        if (deliveringCustomers.length > 0) {
            // Show delivery route only (green color for delivery)
            addDeliveryRouteLayer(route);
        } else {
            // Show full route with segments
            addFullRouteLayer(route);
        }
    }
    
    // Remove existing route layers
    function removeExistingRouteLayers() {
        const layersToRemove = ['route-animated', 'route', 'route-outline'];
        const sourcesToRemove = ['route-animated', 'route'];
        
        // Remove segment layers
        for (let i = 1; i <= 10; i++) {
            layersToRemove.push(`route-segment-${i}`);
            layersToRemove.push(`route-segment-outline-${i}`);
            sourcesToRemove.push(`route-segment-${i}`);
        }
        
        layersToRemove.forEach(layerId => {
            if (map.getLayer(layerId)) {
                map.removeLayer(layerId);
            }
        });
        
        sourcesToRemove.forEach(sourceId => {
            if (map.getSource(sourceId)) {
                map.removeSource(sourceId);
            }
        });
    }
    
    // Add delivery route layer (after pickup)
    function addDeliveryRouteLayer(route) {
        // Add route source
        map.addSource('route', {
            'type': 'geojson',
            'data': {
                'type': 'Feature',
                'properties': {},
                'geometry': {
                    'type': 'LineString',
                    'coordinates': route
                }
            }
        });

        // Add route layer with green color for delivery
        map.addLayer({
            'id': 'route',
            'type': 'line',
            'source': 'route',
            'layout': {
                'line-join': 'round',
                'line-cap': 'round'
            },
            'paint': {
                'line-color': '#10b981', // Green for delivery
                'line-width': [
                    'interpolate',
                    ['linear'],
                    ['zoom'],
                    10, 4,
                    15, 7,
                    18, 10
                ],
                'line-opacity': 0.8
            }
        });

        // Add route outline
        map.addLayer({
            'id': 'route-outline',
            'type': 'line',
            'source': 'route',
            'layout': {
                'line-join': 'round',
                'line-cap': 'round'
            },
            'paint': {
                'line-color': '#ffffff',
                'line-width': [
                    'interpolate',
                    ['linear'],
                    ['zoom'],
                    10, 6,
                    15, 9,
                    18, 12
                ],
                'line-opacity': 0.6
            }
        }, 'route');

        // Add animated route for visual effect
        addAnimatedRoute(route);
        
        // Hide route legend since we're only showing delivery
        const routeLegend = document.getElementById('routeLegend');
        if (routeLegend) {
            routeLegend.style.display = 'none';
        }
    }
    
    // Add full route layer with segments
    function addFullRouteLayer(route) {
        // Add route source
        map.addSource('route', {
            'type': 'geojson',
            'data': {
                'type': 'Feature',
                'properties': {},
                'geometry': {
                    'type': 'LineString',
                    'coordinates': route
                }
            }
        });

        // Add route layer with gradient effect
        map.addLayer({
            'id': 'route',
            'type': 'line',
            'source': 'route',
            'layout': {
                'line-join': 'round',
                'line-cap': 'round'
            },
            'paint': {
                'line-color': [
                    'interpolate',
                    ['linear'],
                    ['line-progress'],
                    0, '#10b981',
                    0.5, '#2563eb', 
                    1, '#ef4444'
                ],
                'line-width': [
                    'interpolate',
                    ['linear'],
                    ['zoom'],
                    10, 3,
                    15, 6,
                    18, 8
                ],
                'line-opacity': 0.8
            }
        });

        // Add route outline for better visibility
        map.addLayer({
            'id': 'route-outline',
            'type': 'line',
            'source': 'route',
            'layout': {
                'line-join': 'round',
                'line-cap': 'round'
            },
            'paint': {
                'line-color': '#ffffff',
                'line-width': [
                    'interpolate',
                    ['linear'],
                    ['zoom'],
                    10, 5,
                    15, 8,
                    18, 10
                ],
                'line-opacity': 0.6
            }
        }, 'route');

        // Add animated route for visual effect
        addAnimatedRoute(route);
        
        // Show route legend for full route
        const routeLegend = document.getElementById('routeLegend');
        if (routeLegend) {
            routeLegend.style.display = 'block';
        }
    }

    // Add animated route effect
    function addAnimatedRoute(coordinates) {
        // Create animated line source
        map.addSource('route-animated', {
            'type': 'geojson',
            'data': {
                'type': 'Feature',
                'properties': {},
                'geometry': {
                    'type': 'LineString',
                    'coordinates': []
                }
            }
        });

        // Add animated layer
        map.addLayer({
            'id': 'route-animated',
            'type': 'line',
            'source': 'route-animated',
            'layout': {
                'line-join': 'round',
                'line-cap': 'round'
            },
            'paint': {
                'line-color': '#ffffff',
                'line-width': 2,
                'line-opacity': 0.9,
                'line-dasharray': [2, 4]
            }
        });

        // Animate the route drawing
        let step = 0;
        const animateRoute = () => {
            if (step < coordinates.length) {
                const currentCoords = coordinates.slice(0, step + 1);
                map.getSource('route-animated').setData({
                    'type': 'Feature',
                    'properties': {},
                    'geometry': {
                        'type': 'LineString',
                        'coordinates': currentCoords
                    }
                });
                step += Math.max(1, Math.floor(coordinates.length / 100));
                requestAnimationFrame(animateRoute);
            }
        };
        
        setTimeout(animateRoute, 500);
    }

    // Update route information in UI
    function updateRouteInfo() {
        // Update the header subtitle with real route info
        const headerSubtitle = document.querySelector('.header-subtitle');
        if (headerSubtitle && routeInfo.distance > 0) {
            headerSubtitle.textContent = `${customers.length} khách hàng • Tuyến đường tối ưu • ${routeInfo.distance}km`;
        }

        // Update the total distance in overview stats
        const distanceElement = document.getElementById('total-distance');
        if (distanceElement && routeInfo.distance > 0) {
            distanceElement.textContent = `${routeInfo.distance} km`;
        }
        
        // Log for debugging
        console.log('Route info updated:', routeInfo);
    }

    // Add markers to map
    function addMarkers() {
        // Group customers by delivery coordinates to handle overlapping markers
        const deliveryGroups = {};
        const pickupGroups = {};
        
        customers.forEach((customer, index) => {
            // Group by delivery coordinates
            const deliveryKey = `${customer.deliveryCoords[0]}_${customer.deliveryCoords[1]}`;
            if (!deliveryGroups[deliveryKey]) {
                deliveryGroups[deliveryKey] = [];
            }
            deliveryGroups[deliveryKey].push({...customer, originalIndex: index});
            
            // Group by pickup coordinates
            const pickupKey = `${customer.pickupCoords[0]}_${customer.pickupCoords[1]}`;
            if (!pickupGroups[pickupKey]) {
                pickupGroups[pickupKey] = [];
            }
            pickupGroups[pickupKey].push({...customer, originalIndex: index});
        });
        
        // Add pickup markers with offset for overlapping locations
        Object.values(pickupGroups).forEach(group => {
            group.forEach((customer, groupIndex) => {
                // Calculate offset for overlapping markers
                const offsetDistance = 0.0001; // Small offset in degrees
                const angle = (groupIndex * 360 / group.length) * (Math.PI / 180);
                const offsetLng = customer.pickupCoords[0] + (Math.cos(angle) * offsetDistance * groupIndex);
                const offsetLat = customer.pickupCoords[1] + (Math.sin(angle) * offsetDistance * groupIndex);
                
                const pickupEl = document.createElement('div');
                pickupEl.className = 'marker pickup-marker';
                pickupEl.id = `pickup-marker-${customer.id}`;
                
                // Show count if multiple orders at same location
                const displayText = group.length > 1 ? `P${customer.originalIndex + 1}` : `P${customer.originalIndex + 1}`;
                const markerSize = group.length > 1 ? '36px' : '32px';
                
                pickupEl.innerHTML = `
                    <div style="
                        width: ${markerSize}; 
                        height: ${markerSize}; 
                        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: white;
                        font-weight: bold;
                        font-size: 14px;
                        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
                        border: 2px solid white;
                        position: relative;
                    ">${displayText}
                    ${group.length > 1 ? `<div style="position: absolute; top: -5px; right: -5px; background: #059669; color: white; border-radius: 50%; width: 16px; height: 16px; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: bold;">${group.length}</div>` : ''}
                    </div>
                `;

                const pickupMarker = new mapboxgl.Marker(pickupEl)
                    .setLngLat([offsetLng, offsetLat])
                    .setPopup(new mapboxgl.Popup({ offset: 25 })
                        .setHTML(`
                            <div style="padding: 8px;">
                                <h4 style="margin: 0 0 8px 0; color: #10b981;">Điểm lấy hàng ${group.length > 1 ? `(${group.length} đơn)` : ''}</h4>
                                ${group.map(c => `
                                    <div style="margin-bottom: 8px; padding-bottom: 8px; border-bottom: 1px solid #eee;">
                                        <p style="margin: 0; font-size: 14px;"><strong>${c.name}</strong></p>
                                        <p style="margin: 4px 0 0 0; font-size: 12px; color: #666;">${c.pickupAddress}</p>
                                        <p style="margin: 4px 0 0 0; font-size: 11px; color: #888;">Tọa độ: ${c.pickupCoords[1].toFixed(6)}, ${c.pickupCoords[0].toFixed(6)}</p>
                                    </div>
                                `).join('')}
                            </div>
                        `))
                    .addTo(map);
                
                // Store pickup marker reference
                pickupEl.markerInstance = pickupMarker;
            });
        });
        
        // Add delivery markers with offset for overlapping locations
        Object.values(deliveryGroups).forEach(group => {
            group.forEach((customer, groupIndex) => {
                // Calculate offset for overlapping markers
                const offsetDistance = 0.0001; // Small offset in degrees
                const angle = (groupIndex * 360 / group.length) * (Math.PI / 180);
                const offsetLng = customer.deliveryCoords[0] + (Math.cos(angle) * offsetDistance * groupIndex);
                const offsetLat = customer.deliveryCoords[1] + (Math.sin(angle) * offsetDistance * groupIndex);
                
                const deliveryEl = document.createElement('div');
                deliveryEl.className = 'marker delivery-marker';
                deliveryEl.id = `delivery-marker-${customer.id}`;
                
                // Show count if multiple orders at same location
                const displayText = group.length > 1 ? `D${customer.originalIndex + 1}` : `D${customer.originalIndex + 1}`;
                const markerSize = group.length > 1 ? '36px' : '32px';
                
                deliveryEl.innerHTML = `
                    <div style="
                        width: ${markerSize}; 
                        height: ${markerSize}; 
                        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: white;
                        font-weight: bold;
                        font-size: 14px;
                        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
                        border: 2px solid white;
                        position: relative;
                    ">${displayText}
                    ${group.length > 1 ? `<div style="position: absolute; top: -5px; right: -5px; background: #dc2626; color: white; border-radius: 50%; width: 16px; height: 16px; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: bold;">${group.length}</div>` : ''}
                    </div>
                `;

                const deliveryMarker = new mapboxgl.Marker(deliveryEl)
                    .setLngLat([offsetLng, offsetLat])
                    .setPopup(new mapboxgl.Popup({ offset: 25 })
                        .setHTML(`
                            <div style="padding: 8px;">
                                <h4 style="margin: 0 0 8px 0; color: #dc2626;">Điểm giao hàng ${group.length > 1 ? `(${group.length} đơn)` : ''}</h4>
                                ${group.map(c => `
                                    <div style="margin-bottom: 8px; padding-bottom: 8px; border-bottom: 1px solid #eee;">
                                        <p style="margin: 0; font-size: 14px;"><strong>${c.name}</strong></p>
                                        <p style="margin: 4px 0 0 0; font-size: 12px; color: #666;">${c.deliveryAddress}</p>
                                        <p style="margin: 4px 0 0 0; font-size: 11px; color: #888;">Tọa độ: ${c.deliveryCoords[1].toFixed(6)}, ${c.deliveryCoords[0].toFixed(6)}</p>
                                    </div>
                                `).join('')}
                            </div>
                        `));
                
                // Always add delivery marker to map for all orders
                deliveryMarker.addTo(map);
                
                // Store delivery marker reference
                deliveryEl.markerInstance = deliveryMarker;
            });
        });

        // Add user location marker if available
        if (userLocation) {
            const userEl = document.createElement('div');
            userEl.innerHTML = `
                <div style="
                    width: 36px; 
                    height: 36px; 
                    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: white;
                    font-size: 16px;
                    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
                    border: 3px solid white;
                    animation: pulse 2s infinite;
                ">
                    <i class="fas fa-user"></i>
                </div>
            `;

            new mapboxgl.Marker(userEl)
                .setLngLat(userLocation)
                .setPopup(new mapboxgl.Popup({ offset: 25 })
                    .setHTML(`<div style="padding: 8px;"><h4 style="margin: 0; color: #2563eb;">Vị trí của bạn</h4><p style="margin: 4px 0 0 0; font-size: 11px; color: #888;">Tọa độ: ${userLocation[1].toFixed(6)}, ${userLocation[0].toFixed(6)}</p></div>`))
                .addTo(map);
        }
    }

    // Hide pickup markers when in delivery mode
    function hidePickupMarkers() {
        // Check if any customer is in delivery mode
        const hasDeliveringCustomers = customers.some(customer => 
            customer.orderStatus === 'in_transit' || customer.orderStatus === 'delivered'
        );
        
        console.log('hidePickupMarkers called, hasDeliveringCustomers:', hasDeliveringCustomers);
        
        if (hasDeliveringCustomers) {
            // Hide all pickup markers when any customer is in delivery mode
            customers.forEach(customer => {
                const pickupMarkerEl = document.getElementById(`pickup-marker-${customer.id}`);
                console.log(`Checking pickup marker for customer ${customer.id}:`, pickupMarkerEl, pickupMarkerEl?.markerInstance);
                if (pickupMarkerEl && pickupMarkerEl.markerInstance) {
                    pickupMarkerEl.markerInstance.remove();
                    console.log(`Removed pickup marker for customer ${customer.id}`);
                }
            });
        }
    }
    
    // Show pickup markers when not in delivery mode
    function showPickupMarkers() {
        // Check if no customer is in delivery mode
        const hasDeliveringCustomers = customers.some(customer => 
            customer.orderStatus === 'in_transit' || customer.orderStatus === 'delivered'
        );
        
        if (!hasDeliveringCustomers) {
            // Show all pickup markers when no customer is in delivery mode
            customers.forEach(customer => {
                const pickupMarkerEl = document.getElementById(`pickup-marker-${customer.id}`);
                if (pickupMarkerEl && pickupMarkerEl.markerInstance) {
                    pickupMarkerEl.markerInstance.addTo(map);
                }
            });
        }
    }

    // Show delivery markers for all orders
    function showDeliveryMarkers() {
        customers.forEach(customer => {
            const deliveryMarkerEl = document.getElementById(`delivery-marker-${customer.id}`);
            if (deliveryMarkerEl && deliveryMarkerEl.markerInstance) {
                deliveryMarkerEl.markerInstance.addTo(map);
            }
        });
    }

    // Hide delivery markers (currently disabled to show all markers)
    function hideDeliveryMarkers() {
        // Comment out to always show delivery markers
        // customers.forEach(customer => {
        //     if (customer.orderStatus !== 'in_transit' && customer.orderStatus !== 'delivered') {
        //         const deliveryMarkerEl = document.getElementById(`delivery-marker-${customer.id}`);
        //         if (deliveryMarkerEl && deliveryMarkerEl.markerInstance) {
        //             deliveryMarkerEl.markerInstance.remove();
        //         }
        //     }
        // });
    }

    // Map control functions
    function zoomIn() {
        map.zoomIn();
    }

    function zoomOut() {
        map.zoomOut();
    }

    function goToUserLocation() {
        if (userLocation) {
            map.flyTo({
                center: userLocation,
                zoom: 15,
                duration: 1000
            });
        } else {
            alert('Không thể xác định vị trí của bạn');
        }
    }

    function fitAllMarkers() {
        const bounds = new mapboxgl.LngLatBounds();
        
        customers.forEach(customer => {
            bounds.extend(customer.pickupCoords);
            bounds.extend(customer.deliveryCoords);
        });
        
        if (userLocation) {
            bounds.extend(userLocation);
        }
        
        map.fitBounds(bounds, {
            padding: 50,
            duration: 1000
        });
    }

    function toggleRoute() {
        showRoute = !showRoute;
        const btn = document.getElementById('routeToggle');
        const routeLegend = document.getElementById('routeLegend');
        
        if (showRoute) {
            // Show all route layers
            if (map.getLayer('route-outline')) {
                map.setLayoutProperty('route-outline', 'visibility', 'visible');
            }
            if (map.getLayer('route')) {
                map.setLayoutProperty('route', 'visibility', 'visible');
            }
            if (map.getLayer('route-animated')) {
                map.setLayoutProperty('route-animated', 'visibility', 'visible');
            }
            
            // Show segment layers if they exist
            for (let i = 1; i <= 10; i++) {
                if (map.getLayer(`route-segment-${i}`)) {
                    map.setLayoutProperty(`route-segment-${i}`, 'visibility', 'visible');
                }
                if (map.getLayer(`route-segment-outline-${i}`)) {
                    map.setLayoutProperty(`route-segment-outline-${i}`, 'visibility', 'visible');
                }
            }
            
            // Show route legend based on current state
            const deliveringCustomers = customers.filter(customer => 
                customer.orderStatus === 'in_transit' || 
                customer.orderStatus === 'delivered'
            );
            
            if (routeLegend && deliveringCustomers.length === 0) {
                routeLegend.style.display = 'block';
            }
            
            btn.classList.add('active');
            btn.classList.remove('hidden');
            btn.innerHTML = '<i data-lucide="route" style="width: 18px; height: 18px;"></i><span>Ẩn tuyến đường</span>';
        } else {
            // Hide all route layers
            if (map.getLayer('route-outline')) {
                map.setLayoutProperty('route-outline', 'visibility', 'none');
            }
            if (map.getLayer('route')) {
                map.setLayoutProperty('route', 'visibility', 'none');
            }
            if (map.getLayer('route-animated')) {
                map.setLayoutProperty('route-animated', 'visibility', 'none');
            }
            
            // Hide segment layers if they exist
            for (let i = 1; i <= 10; i++) {
                if (map.getLayer(`route-segment-${i}`)) {
                    map.setLayoutProperty(`route-segment-${i}`, 'visibility', 'none');
                }
                if (map.getLayer(`route-segment-outline-${i}`)) {
                    map.setLayoutProperty(`route-segment-outline-${i}`, 'visibility', 'none');
                }
            }
            
            // Hide route legend
            if (routeLegend) {
                routeLegend.style.display = 'none';
            }
            
            btn.classList.remove('active');
            btn.classList.add('hidden');
            btn.innerHTML = '<i data-lucide="route" style="width: 18px; height: 18px;"></i><span>Hiện tuyến đường</span>';
        }
        lucide.createIcons();
    }

    function toggleMapStyle() {
        const btn = document.getElementById('mapStyleBtn');
        
        if (mapStyle === 'streets') {
            map.setStyle('mapbox://styles/mapbox/satellite-v9');
            mapStyle = 'satellite';
            btn.innerHTML = '<i data-lucide="map" style="width: 18px; height: 18px;"></i>';
        } else {
            map.setStyle('mapbox://styles/mapbox/streets-v12');
            mapStyle = 'streets';
            btn.innerHTML = '<i data-lucide="satellite" style="width: 18px; height: 18px;"></i>';
        }
        
        // Re-add layers after style change
        map.once('styledata', async () => {
            await addRouteLayer();
            addMarkers();
            
            // Apply marker visibility logic after style change
            setTimeout(() => {
                hideDeliveryMarkers();
                showDeliveryMarkers();
            }, 100);
        });
        
        lucide.createIcons();
    }

    function toggleNavigationMode() {
        isNavigationMode = !isNavigationMode;
        const btn = document.getElementById('navigationBtn');
        
        if (isNavigationMode) {
            btn.classList.add('active');
            map.flyTo({
                bearing: 45,
                pitch: 60,
                duration: 1000
            });
        } else {
            btn.classList.remove('active');
            map.flyTo({
                bearing: 0,
                pitch: 0,
                duration: 1000
            });
            fitAllMarkers();
        }
    }

    // Get user location on page load
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const { latitude, longitude } = position.coords;
                userLocation = [longitude, latitude];
            },
            (error) => {
                console.log('Error getting location:', error);
            }
        );
    }

    // Render customer list
    function renderCustomerList() {
        const customerListContainer = document.getElementById('customer-list');
        if (!customerListContainer) return;
        
        customerListContainer.innerHTML = '';
        
        customers.forEach((customer, index) => {
            const customerItem = document.createElement('div');
            customerItem.className = 'customer-card collapsed';
            customerItem.setAttribute('data-customer-id', customer.id);
            customerItem.onclick = () => toggleCustomerDetails(customer.id);
            customerItem.innerHTML = `
                <div class="customer-number">${index + 1}</div>
                <div class="customer-avatar">${customer.name.charAt(0).toUpperCase()}</div>
                <div class="customer-name">${customer.name}</div>
                <div class="customer-phone">
                    <i data-lucide="phone" style="width: 14px; height: 14px;"></i>
                    ${customer.phone}
                </div>
                <div class="customer-rating">
                    <i data-lucide="star" style="width: 14px; height: 14px; fill: currentColor;"></i>
                    ${customer.rating}
                </div>
                
                <div class="customer-details">
                    <div class="address-section">
                        <div class="address-icon pickup-icon">P</div>
                        <div class="address-info">
                            <div class="address-label">Điểm lấy hàng</div>
                            <div class="address-text">${customer.pickupAddress}</div>
                            <div class="address-time">
                                <i data-lucide="clock" style="width: 12px; height: 12px;"></i>
                                ${customer.estimatedPickupTime}
                            </div>
                        </div>
                    </div>
                    
                    <div class="address-section">
                        <div class="address-icon delivery-icon">D</div>
                        <div class="address-info">
                            <div class="address-label">Điểm giao hàng</div>
                            <div class="address-text">${customer.deliveryAddress}</div>
                            <div class="address-time">
                                <i data-lucide="clock" style="width: 12px; height: 12px;"></i>
                                ${customer.estimatedDeliveryTime}
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="order-meta">
                    <div class="order-value">${customer.orderValue.toLocaleString('vi-VN')}đ</div>
                    <div class="order-items">${customer.items}</div>
                    <div class="priority-badge ${customer.priority === 'urgent' ? 'priority-urgent' : 'priority-normal'}">
                        ${customer.priority === 'urgent' ? 'Gấp' : 'Bình thường'}
                    </div>
                </div>
                
                ${customer.notes ? `<div class="customer-notes">
                    <i data-lucide="message-circle" style="width: 14px; height: 14px; margin-right: 6px;"></i>
                    ${customer.notes}
                </div>` : ''}
                
                <div class="order-status">
                    <div class="status-label">Trạng thái:</div>
                    <div class="status-badge status-${customer.orderStatus.toLowerCase().replace(' ', '-')}" id="status-${customer.id}">
                        ${getStatusText(customer.orderStatus)}
                    </div>
                </div>
                
                <div class="customer-actions">
                    <button class="action-btn action-btn-secondary" onclick="callCustomer('${customer.phone}')">
                        <i data-lucide="phone" style="width: 16px; height: 16px;"></i>
                        Gọi điện
                    </button>
                    ${getStatusButtons(customer.id, customer.orderStatus)}
                </div>
                <div class="expand-indicator"></div>
            `;
            
            customerListContainer.appendChild(customerItem);
        });
        
        // Recreate icons after adding HTML
        setTimeout(() => lucide.createIcons(), 10);
    }
    
    // Call customer function
    function callCustomer(phone) {
        window.location.href = `tel:${phone}`;
    }

    // Toggle customer details
    function toggleCustomerDetails(customerId) {
        const customerCard = document.querySelector(`[data-customer-id="${customerId}"]`);
        if (customerCard) {
            customerCard.classList.toggle('collapsed');
        }
    }
    


    // Get status text in Vietnamese
    function getStatusText(status) {
        const statusMap = {
            'awaiting_driver': 'Chờ tài xế',
            'driver_confirmed': 'Tài xế xác nhận',
            'waiting_driver_pick_up': 'Đang di chuyển đến điểm lấy hàng',
            'driver_picked_up': 'Đã lấy hàng',
            'in_transit': 'Đang giao',
            'delivered': 'Đã giao',
            'delivery_failed': 'Giao hàng thất bại',
            'item_received': 'Khách đã nhận',
            'cancelled': 'Đã hủy'
        };
        return statusMap[status] || status;
    }

    // Get status buttons based on current status
    function getStatusButtons(orderId, currentStatus) {
        let buttons = '';
        
        // Check if all orders have the same status (for batch control)
        const allStatuses = customers.map(c => c.orderStatus);
        const uniqueStatuses = [...new Set(allStatuses)];
        const allSameStatus = uniqueStatuses.length === 1;
        
        // For statuses before 'in_transit', if all orders have same status, don't show individual buttons
        const batchControlStatuses = ['awaiting_driver', 'driver_confirmed', 'waiting_driver_pick_up', 'driver_picked_up'];
        if (allSameStatus && batchControlStatuses.includes(currentStatus)) {
            buttons = `
                <div style="padding: 8px; text-align: center; color: var(--gray-600); font-size: 12px; font-style: italic;">
                    Sử dụng "Điều khiển đơn ghép" ở trên để cập nhật tất cả đơn cùng lúc
                </div>
            `;
            return buttons ? `<div class="status-actions">${buttons}</div>` : '';
        }
        
        switch (currentStatus) {
            case 'awaiting_driver':
                buttons = `
                    <button class="status-btn status-btn-confirm" onclick="updateOrderStatus('${orderId}', 'driver_confirmed')">
                        <i data-lucide="check" style="width: 14px; height: 14px;"></i>
                        Xác nhận nhận đơn
                    </button>
                    <button class="status-btn status-btn-cancel" onclick="updateOrderStatus('${orderId}', 'cancelled')">
                        <i data-lucide="x" style="width: 14px; height: 14px;"></i>
                        Từ chối
                    </button>
                `;
                break;
                
            case 'driver_confirmed':
                buttons = `
                    <button class="status-btn status-btn-pickup" onclick="updateOrderStatus('${orderId}', 'waiting_driver_pick_up')">
                        <i data-lucide="navigation" style="width: 14px; height: 14px;"></i>
                        Bắt đầu di chuyển đến điểm lấy hàng
                    </button>
                `;
                break;
                
            case 'waiting_driver_pick_up':
                buttons = `
                    <button class="status-btn status-btn-confirm" onclick="updateOrderStatus('${orderId}', 'driver_picked_up')">
                        <i data-lucide="package" style="width: 14px; height: 14px;"></i>
                        Đã có mặt - Xác nhận lấy hàng
                    </button>
                `;
                break;
                
            case 'driver_picked_up':
                buttons = `
                    <button class="status-btn status-btn-deliver" onclick="updateOrderStatus('${orderId}', 'in_transit')">
                        <i data-lucide="truck" style="width: 14px; height: 14px;"></i>
                        Bắt đầu giao hàng
                    </button>
                `;
                break;
                
            case 'in_transit':
                buttons = `
                    <button class="status-btn status-btn-complete" onclick="updateOrderStatus('${orderId}', 'delivered')">
                        <i data-lucide="check-circle" style="width: 14px; height: 14px;"></i>
                        Đã giao hàng thành công
                    </button>
                    <button class="status-btn status-btn-cancel" onclick="updateOrderStatus('${orderId}', 'delivery_failed')">
                        <i data-lucide="x-circle" style="width: 14px; height: 14px;"></i>
                        Giao hàng thất bại
                    </button>
                `;
                break;
                
            case 'delivered':
                buttons = `
                    <button class="status-btn status-btn-received" onclick="updateOrderStatus('${orderId}', 'item_received')">
                        <i data-lucide="user-check" style="width: 14px; height: 14px;"></i>
                        Khách đã nhận hàng
                    </button>
                `;
                break;
                
            case 'delivery_failed':
                buttons = `
                    <button class="status-btn status-btn-deliver" onclick="updateOrderStatus('${orderId}', 'in_transit')">
                        <i data-lucide="truck" style="width: 14px; height: 14px;"></i>
                        Thử giao lại
                    </button>
                `;
                break;
                
            default:
                // Không có nút cho các trạng thái khác
                buttons = '';
        }
        
        return buttons ? `<div class="status-actions">${buttons}</div>` : '';
    }

    // Update order status with modal confirmation
    function updateOrderStatus(orderId, newStatus) {
        const statusMessages = {
            'driver_confirmed': {
                title: 'Xác nhận nhận đơn',
                message: 'Bạn có chắc chắn muốn xác nhận nhận đơn hàng này?',
                successMessage: 'Đã xác nhận nhận đơn thành công!',
                icon: 'fas fa-check-circle',
                iconColor: 'green',
                confirmColor: 'green'
            },
            'cancelled': {
                title: 'Từ chối đơn hàng',
                message: 'Bạn có chắc chắn muốn từ chối đơn hàng này?',
                successMessage: 'Đã từ chối đơn hàng!',
                icon: 'fas fa-times-circle',
                iconColor: 'red',
                confirmColor: 'red'
            },
            'waiting_driver_pick_up': {
                title: 'Bắt đầu di chuyển',
                message: 'Bạn có sẵn sàng di chuyển đến điểm lấy hàng?',
                successMessage: 'Đã bắt đầu di chuyển đến điểm lấy hàng!',
                icon: 'fas fa-route',
                iconColor: 'blue',
                confirmColor: 'blue'
            },
            'driver_picked_up': {
                title: 'Xác nhận lấy hàng',
                message: 'Bạn đã lấy hàng thành công?',
                successMessage: 'Đã xác nhận lấy hàng thành công!',
                icon: 'fas fa-box',
                iconColor: 'green',
                confirmColor: 'green'
            },
            'in_transit': {
                title: 'Bắt đầu giao hàng',
                message: 'Bạn có sẵn sàng bắt đầu giao hàng?',
                successMessage: 'Đã bắt đầu giao hàng!',
                icon: 'fas fa-truck',
                iconColor: 'blue',
                confirmColor: 'blue'
            },
            'delivered': {
                title: 'Xác nhận giao hàng',
                message: 'Bạn đã giao hàng thành công?',
                successMessage: 'Đã giao hàng thành công!',
                icon: 'fas fa-check-circle',
                iconColor: 'green',
                confirmColor: 'green'
            },
            'delivery_failed': {
                title: 'Giao hàng thất bại',
                message: 'Xác nhận giao hàng thất bại?',
                successMessage: 'Đã cập nhật trạng thái giao hàng thất bại!',
                icon: 'fas fa-exclamation-triangle',
                iconColor: 'red',
                confirmColor: 'red'
            },
            'item_received': {
                title: 'Khách đã nhận hàng',
                message: 'Xác nhận khách hàng đã nhận hàng?',
                successMessage: 'Đã xác nhận khách hàng nhận hàng!',
                icon: 'fas fa-user-check',
                iconColor: 'green',
                confirmColor: 'green'
            }
        };

        const config = statusMessages[newStatus] || {
            title: 'Xác nhận',
            message: 'Bạn có chắc chắn muốn thực hiện hành động này?',
            successMessage: 'Cập nhật thành công!',
            icon: 'fas fa-question',
            iconColor: 'blue',
            confirmColor: 'blue'
        };

        showModal(config.title, config.message, () => {
            sendRequest(orderId, newStatus, config.successMessage);
        }, {
            icon: config.icon,
            iconColor: config.iconColor,
            confirmColor: config.confirmColor,
            confirmText: 'Xác nhận'
        });
    }

    // Send request to update order status
    async function sendRequest(orderId, newStatus, successMessage) {
        try {
            // Show loading state
            const statusElement = document.getElementById(`status-${orderId}`);
            if (statusElement) {
                statusElement.innerHTML = '<i data-lucide="loader-2" style="width: 12px; height: 12px; animation: spin 1s linear infinite;"></i> Đang cập nhật...';
            }

            // Extract numeric ID from orderId (remove 'ORD' prefix if present)
            const numericOrderId = orderId.toString().replace('ORD', '');

            // Send request to update status using batch route
            const response = await fetch(`/driver/orders/batch/{{ $batchGroupId }}/${numericOrderId}/update-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    status: newStatus
                })
            });

            // Check if response is ok before parsing JSON
            if (!response.ok) {
                const text = await response.text();
                console.error('Server error:', text);
                throw new Error(`Server error: ${response.status} - ${response.statusText}`);
            }

            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                console.error('Non-JSON response:', text);
                throw new Error('Server trả về response không phải JSON. Có thể bạn chưa đăng nhập hoặc không có quyền truy cập.');
            }

            const result = await response.json();

            if (result.success) {
                // Update customer data
                const customerIndex = customers.findIndex(c => c.id === orderId);
                if (customerIndex !== -1) {
                    customers[customerIndex].orderStatus = newStatus;
                }

                // Re-render customer list to update UI
                renderCustomerList();
                renderBatchControls();
                
                // Update route and markers when status changes
                if (newStatus === 'in_transit' || newStatus === 'delivered') {
                    // Re-calculate and update route based on new status
                    setTimeout(async () => {
                        if (map && map.isStyleLoaded()) {
                            await addRouteLayer();
                            // Hide pickup markers when in delivery mode
                            hidePickupMarkers();
                            // Show delivery markers when in delivery mode
                            showDeliveryMarkers();
                        }
                    }, 500);
                } else {
                    // Show pickup markers when not in delivery mode
                    setTimeout(() => {
                        if (map && map.isStyleLoaded()) {
                            showPickupMarkers();
                            // Hide delivery markers when not in delivery mode
                            hideDeliveryMarkers();
                        }
                    }, 500);
                }

                // Show success toast
                showToast('success', {
                    message: successMessage,
                    duration: 3000
                });
            } else {
                throw new Error(result.message || 'Có lỗi xảy ra');
            }
        } catch (error) {
            console.error('Error updating order status:', error);
            showToast('error', {
                message: 'Không thể cập nhật trạng thái: ' + error.message,
                duration: 5000
            });
            
            // Restore original status display
            renderCustomerList();
        }
    }



    // Batch control functions
    function getBatchStatus() {
        const statuses = customers.map(c => c.orderStatus);
        const uniqueStatuses = [...new Set(statuses)];
        
        if (uniqueStatuses.length === 1) {
            return uniqueStatuses[0];
        } else {
            // Mixed statuses - find the most common one or earliest stage
            const statusOrder = ['awaiting_driver', 'driver_confirmed', 'waiting_driver_pick_up', 'driver_picked_up', 'in_transit', 'delivered', 'delivery_failed', 'item_received', 'cancelled'];
            for (let status of statusOrder) {
                if (statuses.includes(status)) {
                    return status;
                }
            }
        }
        return 'mixed';
    }

    function renderBatchControls() {
        const batchStatusInfo = document.getElementById('batch-status-info');
        const batchActions = document.getElementById('batch-actions');
        
        if (!batchStatusInfo || !batchActions) return;
        
        const currentBatchStatus = getBatchStatus();
        const statuses = customers.map(c => c.orderStatus);
        const uniqueStatuses = [...new Set(statuses)];
        
        // Render status info
        let statusIcon, statusTitle, statusDescription, iconColor;
        
        if (uniqueStatuses.length === 1) {
            switch (currentBatchStatus) {
                case 'awaiting_driver':
                    statusIcon = 'clock';
                    statusTitle = 'Chờ xác nhận';
                    statusDescription = 'Tất cả đơn hàng đang chờ tài xế xác nhận';
                    iconColor = 'var(--warning-color)';
                    break;
                case 'driver_confirmed':
                    statusIcon = 'check-circle';
                    statusTitle = 'Đã xác nhận';
                    statusDescription = 'Tất cả đơn hàng đã được xác nhận';
                    iconColor = 'var(--success-color)';
                    break;
                case 'waiting_driver_pick_up':
                    statusIcon = 'navigation';
                    statusTitle = 'Đang di chuyển';
                    statusDescription = 'Đang di chuyển đến điểm lấy hàng';
                    iconColor = 'var(--primary-color)';
                    break;
                case 'driver_picked_up':
                    statusIcon = 'package';
                    statusTitle = 'Đã lấy hàng';
                    statusDescription = 'Đã lấy tất cả hàng, sẵn sàng giao';
                    iconColor = 'var(--purple-color)';
                    break;
                case 'in_transit':
                    statusIcon = 'truck';
                    statusTitle = 'Đang giao hàng';
                    statusDescription = 'Đang giao hàng cho từng khách hàng';
                    iconColor = 'var(--warning-color)';
                    break;
                default:
                    statusIcon = 'info';
                    statusTitle = 'Trạng thái khác';
                    statusDescription = 'Đơn hàng ở trạng thái: ' + getStatusText(currentBatchStatus);
                    iconColor = 'var(--gray-500)';
            }
        } else {
            statusIcon = 'layers';
            statusTitle = 'Trạng thái hỗn hợp';
            statusDescription = `Có ${uniqueStatuses.length} trạng thái khác nhau trong batch`;
            iconColor = 'var(--gray-500)';
        }
        
        batchStatusInfo.innerHTML = `
            <div class="batch-status-summary">
                <div class="batch-status-icon" style="background: ${iconColor};">
                    <i data-lucide="${statusIcon}" style="width: 20px; height: 20px;"></i>
                </div>
                <div class="batch-status-text">
                    <div class="batch-status-title">${statusTitle}</div>
                    <div class="batch-status-description">${statusDescription}</div>
                </div>
            </div>
        `;
        
        // Render action buttons
        let actionButtons = '';
        
        if (uniqueStatuses.length === 1) {
            switch (currentBatchStatus) {
                case 'awaiting_driver':
                    actionButtons = `
                        <button class="batch-btn batch-btn-success" onclick="updateBatchStatus('driver_confirmed')">
                            <i data-lucide="check" style="width: 16px; height: 16px;"></i>
                            Xác nhận tất cả đơn
                        </button>
                        <button class="batch-btn batch-btn-danger" onclick="updateBatchStatus('cancelled')">
                            <i data-lucide="x" style="width: 16px; height: 16px;"></i>
                            Từ chối batch
                        </button>
                    `;
                    break;
                case 'driver_confirmed':
                    actionButtons = `
                        <button class="batch-btn batch-btn-primary" onclick="updateBatchStatus('waiting_driver_pick_up')">
                            <i data-lucide="navigation" style="width: 16px; height: 16px;"></i>
                            Bắt đầu di chuyển lấy hàng
                        </button>
                    `;
                    break;
                case 'waiting_driver_pick_up':
                    actionButtons = `
                        <button class="batch-btn batch-btn-success" onclick="updateBatchStatus('driver_picked_up')">
                            <i data-lucide="package" style="width: 16px; height: 16px;"></i>
                            Xác nhận đã lấy tất cả hàng
                        </button>
                    `;
                    break;
                case 'driver_picked_up':
                    actionButtons = `
                        <button class="batch-btn batch-btn-warning" onclick="updateBatchStatus('in_transit')">
                            <i data-lucide="truck" style="width: 16px; height: 16px;"></i>
                            Bắt đầu giao hàng
                        </button>
                    `;
                    break;
            }
        } else {
            actionButtons = `
                <div style="padding: 12px; text-align: center; color: var(--gray-600); font-size: 14px;">
                    <i data-lucide="info" style="width: 16px; height: 16px; margin-right: 8px;"></i>
                    Các đơn hàng có trạng thái khác nhau. Vui lòng cập nhật từng đơn riêng lẻ.
                </div>
            `;
        }
        
        batchActions.innerHTML = actionButtons;
        
        // Re-create icons
        setTimeout(() => lucide.createIcons(), 10);
    }

    async function updateBatchStatus(newStatus) {
        const statusMessages = {
            'driver_confirmed': {
                title: 'Xác nhận nhận batch',
                message: 'Bạn có chắc chắn muốn xác nhận nhận tất cả đơn hàng trong batch này?',
                successMessage: 'Đã xác nhận nhận tất cả đơn hàng trong batch!'
            },
            'waiting_driver_pick_up': {
                title: 'Bắt đầu di chuyển',
                message: 'Bạn có chắc chắn muốn bắt đầu di chuyển đến điểm lấy hàng?',
                successMessage: 'Đã bắt đầu di chuyển đến điểm lấy hàng!'
            },
            'driver_picked_up': {
                title: 'Xác nhận lấy hàng',
                message: 'Bạn có chắc chắn đã lấy tất cả hàng trong batch này?',
                successMessage: 'Đã xác nhận lấy tất cả hàng!'
            },
            'in_transit': {
                title: 'Bắt đầu giao hàng',
                message: 'Bạn có chắc chắn muốn bắt đầu giao hàng? Sau bước này, bạn sẽ giao hàng cho từng khách hàng riêng lẻ.',
                successMessage: 'Đã bắt đầu giao hàng! Bây giờ bạn có thể giao hàng cho từng khách hàng.'
            },
            'cancelled': {
                title: 'Từ chối batch',
                message: 'Bạn có chắc chắn muốn từ chối toàn bộ batch này?',
                successMessage: 'Đã từ chối batch!'
            }
        };
        
        const config = statusMessages[newStatus];
        if (!config) return;
        
        // Show confirmation modal
        const confirmed = await new Promise((resolve) => {
            showModal(config.title, config.message, () => resolve(true), {
                icon: 'fas fa-question',
                iconColor: 'blue',
                confirmColor: 'blue',
                confirmText: 'Xác nhận',
                onCancel: () => resolve(false)
            });
        });
        
        if (!confirmed) return;
        
        try {
            // Update UI to show loading
            const batchActions = document.getElementById('batch-actions');
            if (batchActions) {
                batchActions.innerHTML = `
                    <div style="padding: 20px; text-align: center; color: var(--gray-600);">
                        <i data-lucide="loader-2" style="width: 20px; height: 20px; animation: spin 1s linear infinite; margin-right: 8px;"></i>
                        Đang cập nhật trạng thái...
                    </div>
                `;
                lucide.createIcons();
            }
            
            // Use the first order ID for the batch update
            const firstOrderId = customers[0].id.toString().replace('ORD', '');
            
            const response = await fetch(`/driver/orders/batch/{{ $batchGroupId }}/${firstOrderId}/update-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    status: newStatus
                })
            });
            
            if (!response.ok) {
                throw new Error(`Server error: ${response.status}`);
            }
            
            const result = await response.json();
            
            if (result.success) {
                // Update all customer statuses
                customers.forEach(customer => {
                    customer.orderStatus = newStatus;
                });
                
                // Re-render everything
                renderCustomerList();
                renderBatchControls();
                
                // Update map if needed
                if (newStatus === 'in_transit') {
                    setTimeout(async () => {
                        if (map && map.isStyleLoaded()) {
                            await addRouteLayer();
                            hidePickupMarkers();
                        }
                    }, 500);
                }
                
                showToast('success', {
                    message: config.successMessage,
                    duration: 3000
                });
            } else {
                throw new Error(result.message || 'Có lỗi xảy ra');
            }
        } catch (error) {
            console.error('Error updating batch status:', error);
            showToast('error', {
                message: 'Không thể cập nhật trạng thái batch: ' + error.message,
                duration: 5000
            });
            
            // Restore original controls
            renderBatchControls();
        }
    }

    // Initialize everything when page loads
    document.addEventListener('DOMContentLoaded', () => {
        initMap();
        renderCustomerList();
        renderBatchControls();
        setTimeout(() => lucide.createIcons(), 100);
    });

    // Handle window resize
    window.addEventListener('resize', () => {
        if (map) {
            setTimeout(() => {
                map.resize();
            }, 100);
        }
    });
</script>
@endpush

@extends('layouts.driver.masterLayout')

@section('title', 'Điều hướng ghép đơn')

@push('styles')
<!-- Mapbox GL JS -->
<link href='https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css' rel='stylesheet' />
<script src='https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js'></script>

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
<style>
    :root {
        --primary-color: #2563eb;
        --primary-hover: #1d4ed8;
        --success-color: #10b981;
        --success-hover: #059669;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
        --purple-color: #8b5cf6;
        --gray-50: #f9fafb;
        --gray-100: #f3f4f6;
        --gray-200: #e5e7eb;
        --gray-300: #d1d5db;
        --gray-400: #9ca3af;
        --gray-500: #6b7280;
        --gray-600: #4b5563;
        --gray-700: #374151;
        --gray-800: #1f2937;
        --gray-900: #111827;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        --border-radius: 12px;
        --border-radius-lg: 16px;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Header */
    .header {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        backdrop-filter: blur(10px);
        box-shadow: var(--shadow-md);
        border-bottom: 1px solid var(--gray-200);
        padding: 16px 20px;
        margin-bottom: 20px;
    }

    .header-content {
        display: flex;
        align-items: center;
        gap: 16px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .header-title {
        font-size: 20px;
        font-weight: 700;
        color: var(--gray-900);
        margin-bottom: 2px;
    }

    .header-subtitle {
        font-size: 14px;
        color: var(--gray-500);
        font-weight: 500;
    }

    /* Buttons */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 12px 20px;
        border: 1px solid var(--gray-300);
        border-radius: var(--border-radius);
        background: white;
        color: var(--gray-700);
        text-decoration: none;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        transition: var(--transition);
        box-shadow: var(--shadow-sm);
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
        color: white;
        border-color: var(--primary-color);
    }

    .btn-success {
        background: linear-gradient(135deg, var(--success-color) 0%, var(--success-hover) 100%);
        color: white;
        border-color: var(--success-color);
    }

    .btn-sm {
        padding: 8px 16px;
        font-size: 13px;
    }

    .btn-ghost {
        border: none;
        background: transparent;
        box-shadow: none;
    }

    .btn-ghost:hover {
        background: var(--gray-100);
        transform: none;
    }

    /* Cards */
    .card {
        background: white;
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow);
        border: 1px solid var(--gray-200);
        transition: var(--transition);
        overflow: hidden;
        margin-bottom: 20px;
    }

    .card:hover {
        box-shadow: var(--shadow-lg);
        transform: translateY(-2px);
    }

    .card-header {
        padding: 24px;
        border-bottom: 1px solid var(--gray-100);
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    }

    .card-content {
        padding: 24px;
    }

    .card-title {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 6px;
        color: var(--gray-900);
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .card-description {
        color: var(--gray-600);
        font-size: 15px;
        font-weight: 400;
    }

    /* Map styles */
    .map-container {
        position: relative;
        height: calc(100vh - 120px);
        min-height: 500px;
        background: var(--gray-100);
        border-radius: var(--border-radius);
        overflow: hidden;
    }

    #map {
        width: 100%;
        height: 100%;
    }

    #map-loading {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--gray-100);
        z-index: 10;
    }

    .loading-spinner {
        display: inline-block;
        width: 24px;
        height: 24px;
        border: 3px solid var(--gray-300);
        border-radius: 50%;
        border-top-color: var(--primary-color);
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Map Controls */
    .map-controls {
        position: absolute;
        top: 20px;
        right: 20px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        z-index: 10;
    }

    .control-group {
        display: flex;
        flex-direction: column;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-lg);
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .control-btn {
        width: 40px;
        height: 40px;
        border: none;
        background: transparent;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: var(--transition);
        color: var(--gray-700);
        font-size: 16px;
    }

    .control-btn:hover {
        background: var(--primary-color);
        color: white;
    }

    .control-btn + .control-btn {
        border-top: 1px solid var(--gray-200);
    }

    /* Route Controls */
    .route-controls {
        position: absolute;
        top: 20px;
        left: 20px;
        z-index: 10;
    }

    .route-toggle {
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 10px 16px;
        border-radius: var(--border-radius);
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: var(--shadow-lg);
        transition: var(--transition);
    }

    .route-toggle:hover {
        background: var(--primary-hover);
        transform: translateY(-2px);
    }

    .route-toggle.hidden {
        background: var(--gray-600);
    }

    /* Stats */
    .grid-3 {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }

    .stat-item {
        text-align: center;
        padding: 20px;
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-radius: var(--border-radius);
        border: 1px solid var(--gray-200);
        transition: var(--transition);
    }

    .stat-item:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }

    .stat-value {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .stat-label {
        font-size: 14px;
        color: var(--gray-600);
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Legend */
    .legend {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        justify-content: center;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        background: var(--gray-50);
        border-radius: var(--border-radius);
        border: 1px solid var(--gray-200);
    }

    .legend-icon {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 12px;
        font-weight: 700;
        box-shadow: var(--shadow-sm);
    }

    /* Badges */
    .badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge-blue { 
        background: #dbeafe; 
        color: #1e40af; 
    }
    .badge-green { 
        background: #d1fae5; 
        color: #065f46; 
    }
    .badge-yellow { 
        background: #fef3c7; 
        color: #92400e; 
    }
    .badge-purple { 
        background: #e9d5ff; 
        color: #6b21a8; 
    }

    /* Responsive */
    @media (max-width: 768px) {
        .map-container {
            height: calc(100vh - 140px);
            min-height: 400px;
        }
        
        .map-controls {
            top: 16px;
            right: 16px;
        }
        
        .route-controls {
            top: 16px;
            left: 16px;
        }
        
        .control-btn {
            width: 36px;
            height: 36px;
        }
        
        .grid-3 {
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }
    }

    /* Customer List Styles */
    .customer-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        padding: 20px;
    }

    .customer-card {
        background: white;
        border: 2px solid var(--gray-200);
        border-radius: 16px;
        transition: var(--transition);
        cursor: pointer;
        position: relative;
        overflow: hidden;
        padding: 20px;
    }

    .customer-card:hover {
        border-color: var(--primary-color);
        box-shadow: var(--shadow-xl);
        transform: translateY(-4px);
    }

    .customer-card.collapsed {
        padding: 12px 20px;
    }

    .customer-card.collapsed .customer-details,
    .customer-card.collapsed .order-meta,
    .customer-card.collapsed .customer-notes,
    .customer-card.collapsed .customer-actions {
        display: none;
    }

    .customer-card.collapsed .customer-avatar {
        width: 40px;
        height: 40px;
        margin: 0 auto 8px auto;
        margin-top: 0;
        font-size: 16px;
    }

    .customer-card.collapsed .customer-name {
        font-size: 14px;
        margin-bottom: 4px;
    }

    .customer-card.collapsed .customer-phone {
        font-size: 12px;
        margin-bottom: 4px;
    }

    .customer-card.collapsed .customer-rating {
        font-size: 12px;
        margin-bottom: 8px;
    }

    .customer-card.collapsed .order-status {
        margin: 8px 0 0 0;
        padding: 4px 0 0 0;
    }

    .expand-indicator {
        position: absolute;
        bottom: 8px;
        right: 12px;
        color: var(--gray-400);
        font-size: 12px;
        transition: var(--transition);
    }

    .customer-card.collapsed .expand-indicator::after {
        content: "Nhấn để xem chi tiết";
    }

    .customer-card:not(.collapsed) .expand-indicator::after {
        content: "Nhấn để thu gọn";
    }

    .customer-card:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .customer-card.selected {
        border-color: var(--primary-color);
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.05) 0%, rgba(37, 99, 235, 0.02) 100%);
    }

    .customer-number {
        position: absolute;
        top: 12px;
        left: 12px;
        width: 28px;
        height: 28px;
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 700;
        box-shadow: var(--shadow-md);
    }

    .customer-card.collapsed .customer-number {
        position: static;
        margin: 0 auto 8px auto;
        width: 20px;
        height: 20px;
        font-size: 10px;
    }

    .customer-avatar {
        width: 64px;
        height: 64px;
        background: linear-gradient(135deg, var(--gray-100) 0%, var(--gray-200) 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px auto;
        margin-top: 24px;
        border: 4px solid white;
        box-shadow: var(--shadow-lg);
        font-size: 24px;
        font-weight: 700;
        color: var(--gray-600);
    }

    .customer-name {
        font-size: 16px;
        font-weight: 700;
        color: var(--gray-900);
        margin: 0 0 8px 0;
        text-align: center;
        line-height: 1.2;
    }

    .customer-phone {
        font-size: 14px;
        color: var(--gray-600);
        text-align: center;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .customer-rating {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        color: var(--warning-color);
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 16px;
    }

    .customer-details {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 16px;
    }

    @media (max-width: 768px) {
        .customer-details {
            grid-template-columns: 1fr;
        }
    }

    .address-section {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 12px;
        background: var(--gray-50);
        border-radius: 8px;
    }

    .address-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 14px;
        flex-shrink: 0;
    }

    .pickup-icon {
        background: linear-gradient(135deg, var(--success-color) 0%, #059669 100%);
    }

    .delivery-icon {
        background: linear-gradient(135deg, var(--danger-color) 0%, #dc2626 100%);
    }

    .address-info {
        flex: 1;
    }

    .address-label {
        font-size: 12px;
        font-weight: 600;
        color: var(--gray-600);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }

    .address-text {
        font-size: 14px;
        color: var(--gray-900);
        line-height: 1.4;
    }

    .address-time {
        font-size: 12px;
        color: var(--gray-600);
        margin-top: 4px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .order-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
        margin-bottom: 12px;
        justify-content: center;
    }

    .order-value {
        font-size: 16px;
        font-weight: 600;
        color: var(--success-color);
    }

    .order-items {
        font-size: 14px;
        color: var(--gray-600);
        text-align: center;
        margin-bottom: 8px;
    }

    .priority-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .priority-normal {
        background: var(--gray-100);
        color: var(--gray-700);
    }

    .priority-urgent {
        background: #fef3c7;
        color: #92400e;
    }

    .customer-notes {
        font-size: 14px;
        color: var(--gray-700);
        background: #dbeafe;
        padding: 12px;
        border-radius: 8px;
        border-left: 4px solid var(--primary-color);
        margin-top: 12px;
    }

    .customer-actions {
        display: flex;
        gap: 8px;
        margin-top: 16px;
    }

    .action-btn {
        flex: 1;
        padding: 10px 16px;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .action-btn-primary {
        background: var(--primary-color);
        color: white;
    }

    .action-btn-primary:hover {
        background: var(--primary-hover);
    }

    .action-btn-secondary {
        background: var(--gray-100);
        color: var(--gray-700);
    }

    .action-btn-secondary:hover {
        background: var(--gray-200);
    }

    /* Collapsible functionality */
    .collapsible-header {
        cursor: pointer;
        transition: var(--transition);
    }

    .collapsible-header:hover {
        background: var(--gray-50);
    }

    .collapsible-icon {
        transition: transform 0.3s ease;
    }

    .collapsible-icon.collapsed {
        transform: rotate(180deg);
    }

    .flex {
        display: flex;
    }

    .items-center {
        align-items: center;
    }

    .justify-between {
        justify-content: space-between;
    }

    .truncate {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .text-center {
        text-align: center;
    }

    .text-xs {
        font-size: 12px;
    }

    .mb-2 {
        margin-bottom: 8px;
    }

    /* Focus styles for accessibility */
    .btn:focus,
    .control-btn:focus {
        outline: 2px solid var(--primary-color);
        outline-offset: 2px;
    }

    /* Order status styles */
    .order-status {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 12px 0;
        padding: 8px 0;
        border-top: 1px solid var(--gray-200);
    }

    .status-label {
        font-weight: 500;
        color: var(--gray-700);
        font-size: 14px;
    }

    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-pending {
        background: #fef3c7;
        color: #92400e;
    }

    .status-confirmed {
        background: #dbeafe;
        color: #1e40af;
    }

    .status-preparing {
        background: #fde68a;
        color: #d97706;
    }

    .status-ready {
        background: #d1fae5;
        color: #065f46;
    }

    .status-picked-up {
        background: #e0e7ff;
        color: #3730a3;
    }

    .status-delivering {
        background: #fed7d7;
        color: #c53030;
    }

    .status-delivered {
        background: #c6f6d5;
        color: #22543d;
    }

    .status-cancelled {
        background: #fed7d7;
        color: #c53030;
    }

    /* Status action buttons */
    .status-actions {
        display: flex;
        gap: 8px;
        margin-top: 8px;
        flex-wrap: wrap;
    }

    .status-btn {
        padding: 6px 12px;
        border: none;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .status-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .status-btn-confirm {
        background: #3b82f6;
        color: white;
    }

    .status-btn-prepare {
        background: #f59e0b;
        color: white;
    }

    .status-btn-ready {
        background: #10b981;
        color: white;
    }

    .status-btn-pickup {
        background: #8b5cf6;
        color: white;
    }

    .status-btn-deliver {
        background: #ef4444;
        color: white;
    }

    .status-btn-complete {
        background: #059669;
        color: white;
    }

    .status-btn-received {
        background: #8b5cf6;
        color: white;
    }

    .status-btn-cancel {
        background: #dc2626;
        color: white;
    }

    /* Notification animations */
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }

    /* Batch Controls Styles */
    .batch-status-info {
        background: var(--gray-50);
        border: 1px solid var(--gray-200);
        border-radius: var(--border-radius);
        padding: 16px;
        margin-bottom: 16px;
    }

    .batch-status-summary {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
    }

    .batch-status-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        color: white;
        font-size: 14px;
    }

    .batch-status-text {
        flex: 1;
    }

    .batch-status-title {
        font-weight: 600;
        color: var(--gray-900);
        margin-bottom: 4px;
    }

    .batch-status-description {
        font-size: 14px;
        color: var(--gray-600);
    }

    .batch-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }

    .batch-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 20px;
        border: 1px solid var(--gray-300);
        border-radius: var(--border-radius);
        background: white;
        color: var(--gray-700);
        text-decoration: none;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        transition: var(--transition);
        box-shadow: var(--shadow-sm);
        min-width: 160px;
        justify-content: center;
    }

    .batch-btn:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .batch-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
        box-shadow: var(--shadow-sm);
    }

    .batch-btn-primary {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
        color: white;
        border-color: var(--primary-color);
    }

    .batch-btn-success {
        background: linear-gradient(135deg, var(--success-color) 0%, var(--success-hover) 100%);
        color: white;
        border-color: var(--success-color);
    }

    .batch-btn-warning {
        background: linear-gradient(135deg, var(--warning-color) 0%, #d97706 100%);
        color: white;
        border-color: var(--warning-color);
    }

    .batch-btn-danger {
        background: linear-gradient(135deg, var(--danger-color) 0%, #dc2626 100%);
        color: white;
        border-color: var(--danger-color);
    }
</style>
@endpush

@section('content')
<div class="pt-4 p-4">
    <!-- Header -->
    <div class="header">
        <div class="header-content">
            <button class="btn btn-ghost btn-sm" onclick="history.back()">
                <i data-lucide="arrow-left" style="width: 20px; height: 20px;"></i>
            </button>
            <div>
                <h1 class="header-title">Đơn hàng ghép #{{ $batchId }}</h1>
                <p class="header-subtitle">{{ $batchOrders->count() }} khách hàng • Tuyến đường tối ưu • 
                @php
                    $totalDistance = 0;
                    $totalValue = $batchOrders->sum('total_amount');
                    // Tính tổng khoảng cách ước tính giữa các điểm
                    for ($i = 0; $i < $batchOrders->count() - 1; $i++) {
                        $order1 = $batchOrders[$i];
                        $order2 = $batchOrders[$i + 1];
                        
                        $lat1 = $order1->address->latitude ?? $order1->guest_latitude ?? 0;
                        $lng1 = $order1->address->longitude ?? $order1->guest_longitude ?? 0;
                        $lat2 = $order2->address->latitude ?? $order2->guest_latitude ?? 0;
                        $lng2 = $order2->address->longitude ?? $order2->guest_longitude ?? 0;
                        
                        if ($lat1 && $lng1 && $lat2 && $lng2) {
                            $earthRadius = 6371;
                            $dLat = deg2rad($lat2 - $lat1);
                            $dLng = deg2rad($lng2 - $lng1);
                            $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng/2) * sin($dLng/2);
                            $c = 2 * atan2(sqrt($a), sqrt(1-$a));
                            $totalDistance += $earthRadius * $c;
                        }
                    }
                @endphp
                {{ number_format($totalDistance, 1) }}km</p>
            </div>
        </div>
    </div>

    <!-- Map Section -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">
                <i data-lucide="map-pin" style="width: 24px; height: 24px; color: var(--primary-color);"></i>
                Bản đồ tuyến đường
            </h2>
            <p class="card-description">Theo dõi các điểm lấy hàng (P) và giao hàng (D) với tuyến đường được tối ưu hóa</p>
        </div>
        <div class="card-content" style="padding: 0;">
            <div class="map-container">
                <div id="map-loading">
                    <div style="text-align: center;">
                        <div class="loading-spinner" style="margin-bottom: 16px;"></div>
                        <p style="color: var(--gray-600); font-weight: 500;">Đang tải bản đồ và tuyến đường...</p>
                    </div>
                </div>
                <div id="map"></div>
                
                <!-- Route Controls -->
                <div class="route-controls">
                    <div class="route-toggle active" id="routeToggle" onclick="toggleRoute()">
                        <i data-lucide="route" style="width: 18px; height: 18px;"></i>
                        <span>Hiện tuyến đường</span>
                    </div>
                </div>
                
                <!-- Map Controls -->
                <div class="map-controls">
                    <!-- Zoom Controls -->
                    <div class="control-group">
                        <button class="control-btn" onclick="zoomIn()" title="Phóng to">
                            <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
                        </button>
                        <button class="control-btn" onclick="zoomOut()" title="Thu nhỏ">
                            <i data-lucide="minus" style="width: 18px; height: 18px;"></i>
                        </button>
                    </div>

                    <!-- Location Controls -->
                    <button class="control-btn" onclick="goToUserLocation()" title="Về vị trí hiện tại">
                        <i data-lucide="locate" style="width: 18px; height: 18px;"></i>
                    </button>

                    <button class="control-btn" onclick="fitAllMarkers()" title="Xem tất cả điểm">
                        <i data-lucide="map-pin" style="width: 18px; height: 18px;"></i>
                    </button>

                    <button class="control-btn" id="mapStyleBtn" onclick="toggleMapStyle()" title="Chuyển đổi kiểu bản đồ">
                        <i data-lucide="satellite" style="width: 18px; height: 18px;"></i>
                    </button>

                    <button class="control-btn" id="navigationBtn" onclick="toggleNavigationMode()" title="Chế độ điều hướng">
                        <i data-lucide="compass" style="width: 18px; height: 18px;"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Overview -->
    <div class="card">
        <div class="card-header">
            <div class="flex items-center justify-between">
                <h2 class="card-title">Tổng quan đơn hàng</h2>
                <span class="badge badge-blue">Đã nhận</span>
            </div>
            <p class="card-description">Thông tin tổng hợp về đơn hàng ghép này</p>
        </div>
        <div class="card-content">
            <div class="grid-3">
                <div class="stat-item">
                    <div class="stat-value" style="color: var(--success-color);">{{ number_format($batchOrders->sum('total_amount')) }}đ</div>
                    <div class="stat-label">Tổng giá trị</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" style="color: var(--primary-color);">{{ $batchOrders->count() }} đơn</div>
                    <div class="stat-label">Số đơn hàng</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" style="color: var(--warning-color);" id="total-distance">{{ number_format($totalDistance, 1) }} km</div>
                    <div class="stat-label">Tổng khoảng cách</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer List -->
    <div class="card">
        <div class="card-header collapsible-header" onclick="toggleCustomerList()">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="card-title">
                        <i data-lucide="users" style="width: 24px; height: 24px; color: var(--primary-color);"></i>
                        Danh sách khách hàng
                    </h2>
                    <p class="card-description">Chi tiết thông tin từng khách hàng trong đơn hàng ghép</p>
                </div>
                <i id="customerListIcon" data-lucide="chevron-up" class="collapsible-icon" style="width: 24px; height: 24px; color: var(--gray-500);"></i>
            </div>
        </div>
        <div class="card-content" id="customerListContent">
            <div class="customer-list" id="customer-list">
                <!-- Customer items will be dynamically generated here -->
            </div>
        </div>
    </div>

    <!-- Batch Controls -->
    <div class="card" id="batch-controls">
        <div class="card-header">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="card-title">
                        <i data-lucide="settings" style="width: 24px; height: 24px; color: var(--primary-color);"></i>
                        Điều khiển đơn ghép
                    </h2>
                    <p class="card-description">Thay đổi trạng thái tất cả đơn hàng trong batch cùng lúc</p>
                </div>
            </div>
        </div>
        <div class="card-content">
            <div class="batch-status-info" id="batch-status-info">
                <!-- Batch status info will be dynamically generated here -->
            </div>
            <div class="batch-actions" id="batch-actions">
                <!-- Batch action buttons will be dynamically generated here -->
            </div>
        </div>
    </div>

    <!-- Map Legend -->
    <div class="card">
        <div class="card-content">
            <h3 class="font-semibold text-gray-900 mb-4" style="font-size: 18px;">Chú thích bản đồ</h3>
            <div class="legend">
                <div class="legend-item">
                    <div class="legend-icon" style="background: linear-gradient(135deg, var(--success-color) 0%, #059669 100%);">P</div>
                    <span style="font-weight: 500;">Điểm lấy hàng</span>
                </div>
                <div class="legend-item">
                    <div class="legend-icon" style="background: linear-gradient(135deg, var(--danger-color) 0%, #dc2626 100%);">D</div>
                    <span style="font-weight: 500;">Điểm giao hàng</span>
                </div>
                <div class="legend-item">
                    <div class="legend-icon" style="background: linear-gradient(135deg, var(--warning-color) 0%, #d97706 100%);">!</div>
                    <span style="font-weight: 500;">Đơn hàng gấp</span>
                </div>
                <div class="legend-item">
                    <div class="legend-icon" style="background: linear-gradient(135deg, var(--primary-color) 0%, #1d4ed8 100%);">
                        <i data-lucide="user" style="width: 12px; height: 12px;"></i>
                    </div>
                    <span style="font-weight: 500;">Vị trí tài xế</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Toast Container --}}
<div id="dtmodal-toast-container" class="fixed top-4 right-4 z-[100] space-y-2"></div>

{{-- Confirmation Modal --}}
<div id="confirmationModal"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-sm">
        <div class="p-6 text-center">
            <div id="modalIcon"
                class="mx-auto w-12 h-12 rounded-full flex items-center justify-center text-xl bg-blue-100 text-blue-600 mb-4">
                <i class="fas fa-question text-2xl"></i>
            </div>
            <h3 id="modalTitle" class="text-lg font-semibold text-gray-900">Tiêu đề Modal</h3>
            <p id="modalMessage" class="text-sm text-gray-500 mt-2">Nội dung modal.</p>
        </div>
        <div class="flex items-center bg-gray-50 px-6 py-4 gap-3 rounded-b-lg">
            <button id="modalCancel" type="button"
                class="w-full py-2 px-4 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">Hủy
                bỏ</button>
            <button id="modalConfirm" type="button"
                class="w-full py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">Xác
                nhận</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Lucide Icons -->
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

<script>
    // Initialize Mapbox access token
    mapboxgl.accessToken = "{{ config('services.mapbox.access_token') }}" || 'pk.eyJ1IjoibmhhdG5ndXllbnF2IiwiYSI6ImNtYjZydDNnZDAwY24ybm9qcTdxcTNocG8ifQ.u7X_0DfN7d52xZ8cGFbWyQ';

    // Real data from database
    const customers = [
        @foreach($batchOrders as $order)
        {
            id: 'ORD{{ $order->id }}',
            name: '{{ $order->customer_name }}',
            phone: '{{ $order->customer_phone }}',
            rating: {{ $order->customer && $order->customer->driverRatings->count() > 0 ? number_format($order->customer->driverRatings->avg('rating'), 1) : 5.0 }},
            pickupAddress: '{{ $order->branch->address ?? "Địa chỉ chi nhánh" }}',
            deliveryAddress: '{{ $order->display_full_delivery_address }}',
            items: '{{ $order->orderItems->map(function($item) { return $item->quantity . "x " . $item->product_name; })->implode(", ") }}',
            orderValue: {{ $order->total_amount }},
            notes: '{{ $order->notes ?? "Không có ghi chú" }}',
            priority: '{{ $order->is_urgent ? "urgent" : "normal" }}',
            estimatedPickupTime: '{{ $order->pickup_time ? \Carbon\Carbon::parse($order->pickup_time)->format("H:i") : "Chưa xác định" }}',
            estimatedDeliveryTime: '{{ $order->delivery_time ? \Carbon\Carbon::parse($order->delivery_time)->format("H:i") : "Chưa xác định" }}',
            orderStatus: '{{ $order->status }}',
            pickupCoords: [
                {{ $order->branch && $order->branch->longitude ? $order->branch->longitude : 106.7017 }}, 
                {{ $order->branch && $order->branch->latitude ? $order->branch->latitude : 10.7769 }}
            ],
            deliveryCoords: [
                {{ $order->address && $order->address->longitude ? $order->address->longitude : ($order->guest_longitude ?? 106.6953) }}, 
                {{ $order->address && $order->address->latitude ? $order->address->latitude : ($order->guest_latitude ?? 10.7756) }}
            ],
            pickupCoordinates: [
                {{ $order->branch && $order->branch->longitude ? $order->branch->longitude : 106.7017 }}, 
                {{ $order->branch && $order->branch->latitude ? $order->branch->latitude : 10.7769 }}
            ],
            deliveryCoordinates: [
                {{ $order->address && $order->address->longitude ? $order->address->longitude : ($order->guest_longitude ?? 106.6953) }}, 
                {{ $order->address && $order->address->latitude ? $order->address->latitude : ($order->guest_latitude ?? 10.7756) }}
            ],
            status: '{{ $order->status }}'
        }@if(!$loop->last),@endif
        @endforeach
    ];

    // Global variables
    let map;
    let mapStyle = 'streets';
    let isNavigationMode = false;
    let userLocation = null;
    let showRoute = true;
    let routeCoordinates = [];
    let routeInfo = {
        distance: 0,
        duration: 0
    };

    // Global utility function for showing toasts
    window.showToast = function(type, options) {
        const toastContainer = document.getElementById('dtmodal-toast-container');
        if (!toastContainer) {
            console.error('Toast container not found!');
            return;
        }

        const toastId = 'toast-' + Date.now();
        const toastElement = document.createElement('div');
        toastElement.id = toastId;
        toastElement.className =
            `relative flex items-center w-full max-w-xs p-4 rounded-lg shadow-md mt-2 text-white transform transition-all ease-out duration-300 translate-x-full opacity-0`;
        let bgColor = '';
        let iconClass = '';
        switch (type) {
            case 'success':
                bgColor = 'bg-green-500';
                iconClass = 'fas fa-check-circle';
                break;
            case 'error':
                bgColor = 'bg-red-500';
                iconClass = 'fas fa-times-circle';
                break;
            case 'warning':
                bgColor = 'bg-yellow-500';
                iconClass = 'fas fa-exclamation-triangle';
                break;
            case 'info':
                bgColor = 'bg-blue-500';
                iconClass = 'fas fa-info-circle';
                break;
            default:
                bgColor = 'bg-gray-700';
                iconClass = 'fas fa-bell';
        }

        toastElement.classList.add(bgColor);
        toastElement.innerHTML = `
            <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg">
                <i class="${iconClass}"></i>
            </div>
            <div class="ml-3 text-sm font-normal">
                ${options.title ? `<p class="font-bold">${options.title}</p>` : ''}
                ${options.message}
            </div>
            <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-transparent text-white rounded-lg p-1.5 inline-flex items-center justify-center h-8 w-8" data-dismiss-target="#${toastId}" aria-label="Close">
                <span class="sr-only">Close</span>
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
            </button>
        `;

        toastContainer.appendChild(toastElement);
        // Animate in
        setTimeout(() => {
            toastElement.classList.remove('translate-x-full', 'opacity-0');
            toastElement.classList.add('translate-x-0', 'opacity-100');
        }, 100);
        // Auto-dismiss
        const duration = options.duration || 5000;
        setTimeout(() => {
            toastElement.classList.remove('translate-x-0', 'opacity-100');
            toastElement.classList.add('translate-x-full', 'opacity-0');
            toastElement.addEventListener('transitionend', () => toastElement.remove());
        }, duration);
        // Manual dismiss
        toastElement.querySelector('[data-dismiss-target]').addEventListener('click', () => {
            toastElement.classList.remove('translate-x-0', 'opacity-100');
            toastElement.classList.add('translate-x-full', 'opacity-0');
            toastElement.addEventListener('transitionend', () => toastElement.remove());
        });
    };

    // Global utility function for showing modals
    window.showModal = function(title, message, onConfirm, options = {}) {
        const modal = document.getElementById('confirmationModal');
        const modalTitle = document.getElementById('modalTitle');
        const modalMessage = document.getElementById('modalMessage');
        const modalIcon = document.getElementById('modalIcon');
        const modalConfirmBtn = document.getElementById('modalConfirm');
        const modalCancelBtn = document.getElementById('modalCancel');
        if (!modal || !modalTitle || !modalMessage || !modalConfirmBtn || !modalCancelBtn || !modalIcon) {
            console.error('Modal elements not found!');
            return;
        }

        modalTitle.textContent = title;
        modalMessage.textContent = message;
        // Set icon and colors
        modalIcon.className = 'mx-auto w-12 h-12 rounded-full flex items-center justify-center mb-4';
        modalIcon.innerHTML = `<i class="${options.icon || 'fas fa-question'} text-2xl"></i>`;
        
        // Set icon background and text colors
        const iconBgColors = {
            'blue': 'bg-blue-100 text-blue-600',
            'green': 'bg-green-100 text-green-600',
            'red': 'bg-red-100 text-red-600',
            'purple': 'bg-purple-100 text-purple-600',
            'yellow': 'bg-yellow-100 text-yellow-600'
        };
        modalIcon.className += ' ' + (iconBgColors[options.iconColor] || 'bg-blue-100 text-blue-600');
        
        modalConfirmBtn.textContent = options.confirmText || 'Đồng ý';
        
        // Set confirm button colors
        const confirmColors = {
            'blue': 'bg-blue-600 hover:bg-blue-700 text-white',
            'green': 'bg-green-600 hover:bg-green-700 text-white',
            'red': 'bg-red-600 hover:bg-red-700 text-white',
            'purple': 'bg-purple-600 hover:bg-purple-700 text-white'
        };
        modalConfirmBtn.className = `w-full py-2 px-4 rounded-md shadow-sm text-sm font-medium transition ${confirmColors[options.confirmColor] || 'bg-blue-600 hover:bg-blue-700 text-white'}`;

        modalCancelBtn.textContent = options.cancelText || 'Hủy bỏ';

        // Show modal
        modal.classList.remove('hidden');

        // Handle confirm
        const handleConfirm = () => {
            modal.classList.add('hidden');
            if (onConfirm) onConfirm();
            modalConfirmBtn.removeEventListener('click', handleConfirm);
            modalCancelBtn.removeEventListener('click', handleCancel);
        };

        // Handle cancel
        const handleCancel = () => {
            modal.classList.add('hidden');
            modalConfirmBtn.removeEventListener('click', handleConfirm);
            modalCancelBtn.removeEventListener('click', handleCancel);
        };

        modalConfirmBtn.addEventListener('click', handleConfirm);
        modalCancelBtn.addEventListener('click', handleCancel);

        // Close on backdrop click
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                handleCancel();
            }
        });
    };

    // Calculate optimal route using Mapbox Directions API
    async function calculateOptimalRoute() {
        // Check if any customer is in delivery mode (in_transit or delivered)
        const deliveringCustomers = customers.filter(customer => customer.orderStatus === 'in_transit' || customer.orderStatus === 'delivered');
        
        if (deliveringCustomers.length > 0) {
            // If any order is in delivery mode, show route to delivery points only
            return await calculateDeliveryRoute();
        } else {
            // Normal route calculation for pickup and delivery
            return await calculateFullRoute();
        }
    }
    
    // Calculate route for delivery only (after pickup)
    async function calculateDeliveryRoute() {
        // Start from user location
        const startPoint = userLocation || customers[0].deliveryCoords;
        
        // Get delivery points for all orders
        const deliveryPoints = customers
            .map(customer => customer.deliveryCoords);
        
        if (deliveryPoints.length === 0) {
            return [startPoint];
        }
        
        // Create waypoints: Start -> D1 -> D2 -> D3...
        const waypoints = [startPoint, ...deliveryPoints];
        
        return await getRouteFromAPI(waypoints);
    }
    
    // Calculate full route (pickup and delivery)
    async function calculateFullRoute() {
        // Start from user location if available, otherwise use first pickup
        const startPoint = userLocation || customers[0].pickupCoords;
        
        // Create waypoints: Start -> P1 -> D1 -> P2 -> D2 -> P3 -> D3
        const waypoints = [startPoint];
        
        // Add pickup and delivery points in order
        customers.forEach(customer => {
            // Only add pickup point if not in delivery mode
            if (customer.orderStatus !== 'in_transit' && customer.orderStatus !== 'delivered') {
                waypoints.push(customer.pickupCoords);
            }
            waypoints.push(customer.deliveryCoords);
        });
        
        return await getRouteFromAPI(waypoints);
    }
    
    // Helper function to get route from Mapbox API
    async function getRouteFromAPI(waypoints) {
        // Convert waypoints to string format for API
        const coordinatesString = waypoints.map(coord => coord.join(',')).join(';');
        
        try {
            const response = await fetch(`https://api.mapbox.com/directions/v5/mapbox/driving/${coordinatesString}?geometries=geojson&overview=full&steps=true&access_token=${mapboxgl.accessToken}`);
            const data = await response.json();
            
            if (data.routes && data.routes.length > 0) {
                const route = data.routes[0];
                
                // Update route info
                routeInfo.distance = (route.distance / 1000).toFixed(1); // Convert to km
                routeInfo.duration = Math.round(route.duration / 60); // Convert to minutes
                
                // Update UI with route info
                updateRouteInfo();
                
                return route.geometry.coordinates;
            } else {
                // Fallback to straight lines if API fails
                return waypoints;
            }
        } catch (error) {
            console.error('Error fetching route:', error);
            // Fallback to straight lines if API fails
            return waypoints;
        }
    }

    // Initialize map
    function initMap() {
        // Calculate center point
        const allCoords = customers.flatMap(customer => [
            customer.pickupCoords,
            customer.deliveryCoords
        ]);
        const centerLng = allCoords.reduce((sum, coord) => sum + coord[0], 0) / allCoords.length;
        const centerLat = allCoords.reduce((sum, coord) => sum + coord[1], 0) / allCoords.length;

        map = new mapboxgl.Map({
            container: 'map',
            style: 'mapbox://styles/mapbox/streets-v12',
            center: [centerLng, centerLat],
            zoom: 13
        });

        map.on('load', async () => {
            // Hide loading spinner
            const loadingElement = document.getElementById('map-loading');
            if (loadingElement) {
                loadingElement.style.display = 'none';
            }
            
            // Ensure map is visible
            const mapElement = document.getElementById('map');
            if (mapElement) {
                mapElement.style.display = 'block';
                mapElement.style.visibility = 'visible';
                mapElement.style.opacity = '1';
            }
            
            // Add route source and layer
            await addRouteLayer();
            addMarkers();
            
            // Show all delivery markers
            setTimeout(() => {
                showDeliveryMarkers();
            }, 100);
            
            fitAllMarkers();
            
            // Force map resize to ensure proper display
            setTimeout(() => {
                map.resize();
            }, 100);
        });
    }

    // Add route layer to map
    async function addRouteLayer() {
        // Remove existing route layers and sources if they exist
        removeExistingRouteLayers();
        
        const route = await calculateOptimalRoute();
        
        // Check if any customer is in delivery mode
        const deliveringCustomers = customers.filter(customer => 
            customer.orderStatus === 'in_transit' || 
            customer.orderStatus === 'delivered'
        );
        
        if (deliveringCustomers.length > 0) {
            // Show delivery route only (green color for delivery)
            addDeliveryRouteLayer(route);
        } else {
            // Show full route with segments
            addFullRouteLayer(route);
        }
    }
    
    // Remove existing route layers
    function removeExistingRouteLayers() {
        const layersToRemove = ['route-animated', 'route', 'route-outline'];
        const sourcesToRemove = ['route-animated', 'route'];
        
        // Remove segment layers
        for (let i = 1; i <= 10; i++) {
            layersToRemove.push(`route-segment-${i}`);
            layersToRemove.push(`route-segment-outline-${i}`);
            sourcesToRemove.push(`route-segment-${i}`);
        }
        
        layersToRemove.forEach(layerId => {
            if (map.getLayer(layerId)) {
                map.removeLayer(layerId);
            }
        });
        
        sourcesToRemove.forEach(sourceId => {
            if (map.getSource(sourceId)) {
                map.removeSource(sourceId);
            }
        });
    }
    
    // Add delivery route layer (after pickup)
    function addDeliveryRouteLayer(route) {
        // Add route source
        map.addSource('route', {
            'type': 'geojson',
            'data': {
                'type': 'Feature',
                'properties': {},
                'geometry': {
                    'type': 'LineString',
                    'coordinates': route
                }
            }
        });

        // Add route layer with green color for delivery
        map.addLayer({
            'id': 'route',
            'type': 'line',
            'source': 'route',
            'layout': {
                'line-join': 'round',
                'line-cap': 'round'
            },
            'paint': {
                'line-color': '#10b981', // Green for delivery
                'line-width': [
                    'interpolate',
                    ['linear'],
                    ['zoom'],
                    10, 4,
                    15, 7,
                    18, 10
                ],
                'line-opacity': 0.8
            }
        });

        // Add route outline
        map.addLayer({
            'id': 'route-outline',
            'type': 'line',
            'source': 'route',
            'layout': {
                'line-join': 'round',
                'line-cap': 'round'
            },
            'paint': {
                'line-color': '#ffffff',
                'line-width': [
                    'interpolate',
                    ['linear'],
                    ['zoom'],
                    10, 6,
                    15, 9,
                    18, 12
                ],
                'line-opacity': 0.6
            }
        }, 'route');

        // Add animated route for visual effect
        addAnimatedRoute(route);
        
        // Hide route legend since we're only showing delivery
        const routeLegend = document.getElementById('routeLegend');
        if (routeLegend) {
            routeLegend.style.display = 'none';
        }
    }
    
    // Add full route layer with segments
    function addFullRouteLayer(route) {
        // Add route source
        map.addSource('route', {
            'type': 'geojson',
            'data': {
                'type': 'Feature',
                'properties': {},
                'geometry': {
                    'type': 'LineString',
                    'coordinates': route
                }
            }
        });

        // Add route layer with gradient effect
        map.addLayer({
            'id': 'route',
            'type': 'line',
            'source': 'route',
            'layout': {
                'line-join': 'round',
                'line-cap': 'round'
            },
            'paint': {
                'line-color': [
                    'interpolate',
                    ['linear'],
                    ['line-progress'],
                    0, '#10b981',
                    0.5, '#2563eb', 
                    1, '#ef4444'
                ],
                'line-width': [
                    'interpolate',
                    ['linear'],
                    ['zoom'],
                    10, 3,
                    15, 6,
                    18, 8
                ],
                'line-opacity': 0.8
            }
        });

        // Add route outline for better visibility
        map.addLayer({
            'id': 'route-outline',
            'type': 'line',
            'source': 'route',
            'layout': {
                'line-join': 'round',
                'line-cap': 'round'
            },
            'paint': {
                'line-color': '#ffffff',
                'line-width': [
                    'interpolate',
                    ['linear'],
                    ['zoom'],
                    10, 5,
                    15, 8,
                    18, 10
                ],
                'line-opacity': 0.6
            }
        }, 'route');

        // Add animated route for visual effect
        addAnimatedRoute(route);
        
        // Show route legend for full route
        const routeLegend = document.getElementById('routeLegend');
        if (routeLegend) {
            routeLegend.style.display = 'block';
        }
    }

    // Add animated route effect
    function addAnimatedRoute(coordinates) {
        // Create animated line source
        map.addSource('route-animated', {
            'type': 'geojson',
            'data': {
                'type': 'Feature',
                'properties': {},
                'geometry': {
                    'type': 'LineString',
                    'coordinates': []
                }
            }
        });

        // Add animated layer
        map.addLayer({
            'id': 'route-animated',
            'type': 'line',
            'source': 'route-animated',
            'layout': {
                'line-join': 'round',
                'line-cap': 'round'
            },
            'paint': {
                'line-color': '#ffffff',
                'line-width': 2,
                'line-opacity': 0.9,
                'line-dasharray': [2, 4]
            }
        });

        // Animate the route drawing
        let step = 0;
        const animateRoute = () => {
            if (step < coordinates.length) {
                const currentCoords = coordinates.slice(0, step + 1);
                map.getSource('route-animated').setData({
                    'type': 'Feature',
                    'properties': {},
                    'geometry': {
                        'type': 'LineString',
                        'coordinates': currentCoords
                    }
                });
                step += Math.max(1, Math.floor(coordinates.length / 100));
                requestAnimationFrame(animateRoute);
            }
        };
        
        setTimeout(animateRoute, 500);
    }

    // Update route information in UI
    function updateRouteInfo() {
        // Update the header subtitle with real route info
        const headerSubtitle = document.querySelector('.header-subtitle');
        if (headerSubtitle && routeInfo.distance > 0) {
            headerSubtitle.textContent = `${customers.length} khách hàng • Tuyến đường tối ưu • ${routeInfo.distance}km`;
        }

        // Update the total distance in overview stats
        const distanceElement = document.getElementById('total-distance');
        if (distanceElement && routeInfo.distance > 0) {
            distanceElement.textContent = `${routeInfo.distance} km`;
        }
        
        // Log for debugging
        console.log('Route info updated:', routeInfo);
    }

    // Add markers to map
    function addMarkers() {
        // Group customers by delivery coordinates to handle overlapping markers
        const deliveryGroups = {};
        const pickupGroups = {};
        
        customers.forEach((customer, index) => {
            // Group by delivery coordinates
            const deliveryKey = `${customer.deliveryCoords[0]}_${customer.deliveryCoords[1]}`;
            if (!deliveryGroups[deliveryKey]) {
                deliveryGroups[deliveryKey] = [];
            }
            deliveryGroups[deliveryKey].push({...customer, originalIndex: index});
            
            // Group by pickup coordinates
            const pickupKey = `${customer.pickupCoords[0]}_${customer.pickupCoords[1]}`;
            if (!pickupGroups[pickupKey]) {
                pickupGroups[pickupKey] = [];
            }
            pickupGroups[pickupKey].push({...customer, originalIndex: index});
        });
        
        // Add pickup markers with offset for overlapping locations
        Object.values(pickupGroups).forEach(group => {
            group.forEach((customer, groupIndex) => {
                // Calculate offset for overlapping markers
                const offsetDistance = 0.0001; // Small offset in degrees
                const angle = (groupIndex * 360 / group.length) * (Math.PI / 180);
                const offsetLng = customer.pickupCoords[0] + (Math.cos(angle) * offsetDistance * groupIndex);
                const offsetLat = customer.pickupCoords[1] + (Math.sin(angle) * offsetDistance * groupIndex);
                
                const pickupEl = document.createElement('div');
                pickupEl.className = 'marker pickup-marker';
                pickupEl.id = `pickup-marker-${customer.id}`;
                
                // Show count if multiple orders at same location
                const displayText = group.length > 1 ? `P${customer.originalIndex + 1}` : `P${customer.originalIndex + 1}`;
                const markerSize = group.length > 1 ? '36px' : '32px';
                
                pickupEl.innerHTML = `
                    <div style="
                        width: ${markerSize}; 
                        height: ${markerSize}; 
                        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: white;
                        font-weight: bold;
                        font-size: 14px;
                        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
                        border: 2px solid white;
                        position: relative;
                    ">${displayText}
                    ${group.length > 1 ? `<div style="position: absolute; top: -5px; right: -5px; background: #059669; color: white; border-radius: 50%; width: 16px; height: 16px; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: bold;">${group.length}</div>` : ''}
                    </div>
                `;

                const pickupMarker = new mapboxgl.Marker(pickupEl)
                    .setLngLat([offsetLng, offsetLat])
                    .setPopup(new mapboxgl.Popup({ offset: 25 })
                        .setHTML(`
                            <div style="padding: 8px;">
                                <h4 style="margin: 0 0 8px 0; color: #10b981;">Điểm lấy hàng ${group.length > 1 ? `(${group.length} đơn)` : ''}</h4>
                                ${group.map(c => `
                                    <div style="margin-bottom: 8px; padding-bottom: 8px; border-bottom: 1px solid #eee;">
                                        <p style="margin: 0; font-size: 14px;"><strong>${c.name}</strong></p>
                                        <p style="margin: 4px 0 0 0; font-size: 12px; color: #666;">${c.pickupAddress}</p>
                                        <p style="margin: 4px 0 0 0; font-size: 11px; color: #888;">Tọa độ: ${c.pickupCoords[1].toFixed(6)}, ${c.pickupCoords[0].toFixed(6)}</p>
                                    </div>
                                `).join('')}
                            </div>
                        `))
                    .addTo(map);
                
                // Store pickup marker reference
                pickupEl.markerInstance = pickupMarker;
            });
        });
        
        // Add delivery markers with offset for overlapping locations
        Object.values(deliveryGroups).forEach(group => {
            group.forEach((customer, groupIndex) => {
                // Calculate offset for overlapping markers
                const offsetDistance = 0.0001; // Small offset in degrees
                const angle = (groupIndex * 360 / group.length) * (Math.PI / 180);
                const offsetLng = customer.deliveryCoords[0] + (Math.cos(angle) * offsetDistance * groupIndex);
                const offsetLat = customer.deliveryCoords[1] + (Math.sin(angle) * offsetDistance * groupIndex);
                
                const deliveryEl = document.createElement('div');
                deliveryEl.className = 'marker delivery-marker';
                deliveryEl.id = `delivery-marker-${customer.id}`;
                
                // Show count if multiple orders at same location
                const displayText = group.length > 1 ? `D${customer.originalIndex + 1}` : `D${customer.originalIndex + 1}`;
                const markerSize = group.length > 1 ? '36px' : '32px';
                
                deliveryEl.innerHTML = `
                    <div style="
                        width: ${markerSize}; 
                        height: ${markerSize}; 
                        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: white;
                        font-weight: bold;
                        font-size: 14px;
                        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
                        border: 2px solid white;
                        position: relative;
                    ">${displayText}
                    ${group.length > 1 ? `<div style="position: absolute; top: -5px; right: -5px; background: #dc2626; color: white; border-radius: 50%; width: 16px; height: 16px; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: bold;">${group.length}</div>` : ''}
                    </div>
                `;

                const deliveryMarker = new mapboxgl.Marker(deliveryEl)
                    .setLngLat([offsetLng, offsetLat])
                    .setPopup(new mapboxgl.Popup({ offset: 25 })
                        .setHTML(`
                            <div style="padding: 8px;">
                                <h4 style="margin: 0 0 8px 0; color: #dc2626;">Điểm giao hàng ${group.length > 1 ? `(${group.length} đơn)` : ''}</h4>
                                ${group.map(c => `
                                    <div style="margin-bottom: 8px; padding-bottom: 8px; border-bottom: 1px solid #eee;">
                                        <p style="margin: 0; font-size: 14px;"><strong>${c.name}</strong></p>
                                        <p style="margin: 4px 0 0 0; font-size: 12px; color: #666;">${c.deliveryAddress}</p>
                                        <p style="margin: 4px 0 0 0; font-size: 11px; color: #888;">Tọa độ: ${c.deliveryCoords[1].toFixed(6)}, ${c.deliveryCoords[0].toFixed(6)}</p>
                                    </div>
                                `).join('')}
                            </div>
                        `));
                
                // Always add delivery marker to map for all orders
                deliveryMarker.addTo(map);
                
                // Store delivery marker reference
                deliveryEl.markerInstance = deliveryMarker;
            });
        });

        // Add user location marker if available
        if (userLocation) {
            const userEl = document.createElement('div');
            userEl.innerHTML = `
                <div style="
                    width: 36px; 
                    height: 36px; 
                    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: white;
                    font-size: 16px;
                    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
                    border: 3px solid white;
                    animation: pulse 2s infinite;
                ">
                    <i class="fas fa-user"></i>
                </div>
            `;

            new mapboxgl.Marker(userEl)
                .setLngLat(userLocation)
                .setPopup(new mapboxgl.Popup({ offset: 25 })
                    .setHTML(`<div style="padding: 8px;"><h4 style="margin: 0; color: #2563eb;">Vị trí của bạn</h4><p style="margin: 4px 0 0 0; font-size: 11px; color: #888;">Tọa độ: ${userLocation[1].toFixed(6)}, ${userLocation[0].toFixed(6)}</p></div>`))
                .addTo(map);
        }
    }

    // Hide pickup markers when in delivery mode
    function hidePickupMarkers() {
        // Check if any customer is in delivery mode
        const hasDeliveringCustomers = customers.some(customer => 
            customer.orderStatus === 'in_transit' || customer.orderStatus === 'delivered'
        );
        
        console.log('hidePickupMarkers called, hasDeliveringCustomers:', hasDeliveringCustomers);
        
        if (hasDeliveringCustomers) {
            // Hide all pickup markers when any customer is in delivery mode
            customers.forEach(customer => {
                const pickupMarkerEl = document.getElementById(`pickup-marker-${customer.id}`);
                console.log(`Checking pickup marker for customer ${customer.id}:`, pickupMarkerEl, pickupMarkerEl?.markerInstance);
                if (pickupMarkerEl && pickupMarkerEl.markerInstance) {
                    pickupMarkerEl.markerInstance.remove();
                    console.log(`Removed pickup marker for customer ${customer.id}`);
                }
            });
        }
    }
    
    // Show pickup markers when not in delivery mode
    function showPickupMarkers() {
        // Check if no customer is in delivery mode
        const hasDeliveringCustomers = customers.some(customer => 
            customer.orderStatus === 'in_transit' || customer.orderStatus === 'delivered'
        );
        
        if (!hasDeliveringCustomers) {
            // Show all pickup markers when no customer is in delivery mode
            customers.forEach(customer => {
                const pickupMarkerEl = document.getElementById(`pickup-marker-${customer.id}`);
                if (pickupMarkerEl && pickupMarkerEl.markerInstance) {
                    pickupMarkerEl.markerInstance.addTo(map);
                }
            });
        }
    }

    // Show delivery markers for all orders
    function showDeliveryMarkers() {
        customers.forEach(customer => {
            const deliveryMarkerEl = document.getElementById(`delivery-marker-${customer.id}`);
            if (deliveryMarkerEl && deliveryMarkerEl.markerInstance) {
                deliveryMarkerEl.markerInstance.addTo(map);
            }
        });
    }

    // Hide delivery markers (currently disabled to show all markers)
    function hideDeliveryMarkers() {
        // Comment out to always show delivery markers
        // customers.forEach(customer => {
        //     if (customer.orderStatus !== 'in_transit' && customer.orderStatus !== 'delivered') {
        //         const deliveryMarkerEl = document.getElementById(`delivery-marker-${customer.id}`);
        //         if (deliveryMarkerEl && deliveryMarkerEl.markerInstance) {
        //             deliveryMarkerEl.markerInstance.remove();
        //         }
        //     }
        // });
    }

    // Map control functions
    function zoomIn() {
        map.zoomIn();
    }

    function zoomOut() {
        map.zoomOut();
    }

    function goToUserLocation() {
        if (userLocation) {
            map.flyTo({
                center: userLocation,
                zoom: 15,
                duration: 1000
            });
        } else {
            alert('Không thể xác định vị trí của bạn');
        }
    }

    function fitAllMarkers() {
        const bounds = new mapboxgl.LngLatBounds();
        
        customers.forEach(customer => {
            bounds.extend(customer.pickupCoords);
            bounds.extend(customer.deliveryCoords);
        });
        
        if (userLocation) {
            bounds.extend(userLocation);
        }
        
        map.fitBounds(bounds, {
            padding: 50,
            duration: 1000
        });
    }

    function toggleRoute() {
        showRoute = !showRoute;
        const btn = document.getElementById('routeToggle');
        const routeLegend = document.getElementById('routeLegend');
        
        if (showRoute) {
            // Show all route layers
            if (map.getLayer('route-outline')) {
                map.setLayoutProperty('route-outline', 'visibility', 'visible');
            }
            if (map.getLayer('route')) {
                map.setLayoutProperty('route', 'visibility', 'visible');
            }
            if (map.getLayer('route-animated')) {
                map.setLayoutProperty('route-animated', 'visibility', 'visible');
            }
            
            // Show segment layers if they exist
            for (let i = 1; i <= 10; i++) {
                if (map.getLayer(`route-segment-${i}`)) {
                    map.setLayoutProperty(`route-segment-${i}`, 'visibility', 'visible');
                }
                if (map.getLayer(`route-segment-outline-${i}`)) {
                    map.setLayoutProperty(`route-segment-outline-${i}`, 'visibility', 'visible');
                }
            }
            
            // Show route legend based on current state
            const deliveringCustomers = customers.filter(customer => 
                customer.orderStatus === 'in_transit' || 
                customer.orderStatus === 'delivered'
            );
            
            if (routeLegend && deliveringCustomers.length === 0) {
                routeLegend.style.display = 'block';
            }
            
            btn.classList.add('active');
            btn.classList.remove('hidden');
            btn.innerHTML = '<i data-lucide="route" style="width: 18px; height: 18px;"></i><span>Ẩn tuyến đường</span>';
        } else {
            // Hide all route layers
            if (map.getLayer('route-outline')) {
                map.setLayoutProperty('route-outline', 'visibility', 'none');
            }
            if (map.getLayer('route')) {
                map.setLayoutProperty('route', 'visibility', 'none');
            }
            if (map.getLayer('route-animated')) {
                map.setLayoutProperty('route-animated', 'visibility', 'none');
            }
            
            // Hide segment layers if they exist
            for (let i = 1; i <= 10; i++) {
                if (map.getLayer(`route-segment-${i}`)) {
                    map.setLayoutProperty(`route-segment-${i}`, 'visibility', 'none');
                }
                if (map.getLayer(`route-segment-outline-${i}`)) {
                    map.setLayoutProperty(`route-segment-outline-${i}`, 'visibility', 'none');
                }
            }
            
            // Hide route legend
            if (routeLegend) {
                routeLegend.style.display = 'none';
            }
            
            btn.classList.remove('active');
            btn.classList.add('hidden');
            btn.innerHTML = '<i data-lucide="route" style="width: 18px; height: 18px;"></i><span>Hiện tuyến đường</span>';
        }
        lucide.createIcons();
    }

    function toggleMapStyle() {
        const btn = document.getElementById('mapStyleBtn');
        
        if (mapStyle === 'streets') {
            map.setStyle('mapbox://styles/mapbox/satellite-v9');
            mapStyle = 'satellite';
            btn.innerHTML = '<i data-lucide="map" style="width: 18px; height: 18px;"></i>';
        } else {
            map.setStyle('mapbox://styles/mapbox/streets-v12');
            mapStyle = 'streets';
            btn.innerHTML = '<i data-lucide="satellite" style="width: 18px; height: 18px;"></i>';
        }
        
        // Re-add layers after style change
        map.once('styledata', async () => {
            await addRouteLayer();
            addMarkers();
            
            // Apply marker visibility logic after style change
            setTimeout(() => {
                hideDeliveryMarkers();
                showDeliveryMarkers();
            }, 100);
        });
        
        lucide.createIcons();
    }

    function toggleNavigationMode() {
        isNavigationMode = !isNavigationMode;
        const btn = document.getElementById('navigationBtn');
        
        if (isNavigationMode) {
            btn.classList.add('active');
            map.flyTo({
                bearing: 45,
                pitch: 60,
                duration: 1000
            });
        } else {
            btn.classList.remove('active');
            map.flyTo({
                bearing: 0,
                pitch: 0,
                duration: 1000
            });
            fitAllMarkers();
        }
    }

    // Get user location on page load
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const { latitude, longitude } = position.coords;
                userLocation = [longitude, latitude];
            },
            (error) => {
                console.log('Error getting location:', error);
            }
        );
    }

    // Render customer list
    function renderCustomerList() {
        const customerListContainer = document.getElementById('customer-list');
        if (!customerListContainer) return;
        
        customerListContainer.innerHTML = '';
        
        customers.forEach((customer, index) => {
            const customerItem = document.createElement('div');
            customerItem.className = 'customer-card collapsed';
            customerItem.setAttribute('data-customer-id', customer.id);
            customerItem.onclick = () => toggleCustomerDetails(customer.id);
            customerItem.innerHTML = `
                <div class="customer-number">${index + 1}</div>
                <div class="customer-avatar">${customer.name.charAt(0).toUpperCase()}</div>
                <div class="customer-name">${customer.name}</div>
                <div class="customer-phone">
                    <i data-lucide="phone" style="width: 14px; height: 14px;"></i>
                    ${customer.phone}
                </div>
                <div class="customer-rating">
                    <i data-lucide="star" style="width: 14px; height: 14px; fill: currentColor;"></i>
                    ${customer.rating}
                </div>
                
                <div class="customer-details">
                    <div class="address-section">
                        <div class="address-icon pickup-icon">P</div>
                        <div class="address-info">
                            <div class="address-label">Điểm lấy hàng</div>
                            <div class="address-text">${customer.pickupAddress}</div>
                            <div class="address-time">
                                <i data-lucide="clock" style="width: 12px; height: 12px;"></i>
                                ${customer.estimatedPickupTime}
                            </div>
                        </div>
                    </div>
                    
                    <div class="address-section">
                        <div class="address-icon delivery-icon">D</div>
                        <div class="address-info">
                            <div class="address-label">Điểm giao hàng</div>
                            <div class="address-text">${customer.deliveryAddress}</div>
                            <div class="address-time">
                                <i data-lucide="clock" style="width: 12px; height: 12px;"></i>
                                ${customer.estimatedDeliveryTime}
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="order-meta">
                    <div class="order-value">${customer.orderValue.toLocaleString('vi-VN')}đ</div>
                    <div class="order-items">${customer.items}</div>
                    <div class="priority-badge ${customer.priority === 'urgent' ? 'priority-urgent' : 'priority-normal'}">
                        ${customer.priority === 'urgent' ? 'Gấp' : 'Bình thường'}
                    </div>
                </div>
                
                ${customer.notes ? `<div class="customer-notes">
                    <i data-lucide="message-circle" style="width: 14px; height: 14px; margin-right: 6px;"></i>
                    ${customer.notes}
                </div>` : ''}
                
                <div class="order-status">
                    <div class="status-label">Trạng thái:</div>
                    <div class="status-badge status-${customer.orderStatus.toLowerCase().replace(' ', '-')}" id="status-${customer.id}">
                        ${getStatusText(customer.orderStatus)}
                    </div>
                </div>
                
                <div class="customer-actions">
                    <button class="action-btn action-btn-secondary" onclick="callCustomer('${customer.phone}')">
                        <i data-lucide="phone" style="width: 16px; height: 16px;"></i>
                        Gọi điện
                    </button>
                    ${getStatusButtons(customer.id, customer.orderStatus)}
                </div>
                <div class="expand-indicator"></div>
            `;
            
            customerListContainer.appendChild(customerItem);
        });
        
        // Recreate icons after adding HTML
        setTimeout(() => lucide.createIcons(), 10);
    }
    
    // Call customer function
    function callCustomer(phone) {
        window.location.href = `tel:${phone}`;
    }

    // Toggle customer details
    function toggleCustomerDetails(customerId) {
        const customerCard = document.querySelector(`[data-customer-id="${customerId}"]`);
        if (customerCard) {
            customerCard.classList.toggle('collapsed');
        }
    }
    


    // Get status text in Vietnamese
    function getStatusText(status) {
        const statusMap = {
            'awaiting_driver': 'Chờ tài xế',
            'driver_confirmed': 'Tài xế xác nhận',
            'waiting_driver_pick_up': 'Đang di chuyển đến điểm lấy hàng',
            'driver_picked_up': 'Đã lấy hàng',
            'in_transit': 'Đang giao',
            'delivered': 'Đã giao',
            'delivery_failed': 'Giao hàng thất bại',
            'item_received': 'Khách đã nhận',
            'cancelled': 'Đã hủy'
        };
        return statusMap[status] || status;
    }

    // Get status buttons based on current status
    function getStatusButtons(orderId, currentStatus) {
        let buttons = '';
        
        // Check if all orders have the same status (for batch control)
        const allStatuses = customers.map(c => c.orderStatus);
        const uniqueStatuses = [...new Set(allStatuses)];
        const allSameStatus = uniqueStatuses.length === 1;
        
        // For statuses before 'in_transit', if all orders have same status, don't show individual buttons
        const batchControlStatuses = ['awaiting_driver', 'driver_confirmed', 'waiting_driver_pick_up', 'driver_picked_up'];
        if (allSameStatus && batchControlStatuses.includes(currentStatus)) {
            buttons = `
                <div style="padding: 8px; text-align: center; color: var(--gray-600); font-size: 12px; font-style: italic;">
                    Sử dụng "Điều khiển đơn ghép" ở trên để cập nhật tất cả đơn cùng lúc
                </div>
            `;
            return buttons ? `<div class="status-actions">${buttons}</div>` : '';
        }
        
        switch (currentStatus) {
            case 'awaiting_driver':
                buttons = `
                    <button class="status-btn status-btn-confirm" onclick="updateOrderStatus('${orderId}', 'driver_confirmed')">
                        <i data-lucide="check" style="width: 14px; height: 14px;"></i>
                        Xác nhận nhận đơn
                    </button>
                    <button class="status-btn status-btn-cancel" onclick="updateOrderStatus('${orderId}', 'cancelled')">
                        <i data-lucide="x" style="width: 14px; height: 14px;"></i>
                        Từ chối
                    </button>
                `;
                break;
                
            case 'driver_confirmed':
                buttons = `
                    <button class="status-btn status-btn-pickup" onclick="updateOrderStatus('${orderId}', 'waiting_driver_pick_up')">
                        <i data-lucide="navigation" style="width: 14px; height: 14px;"></i>
                        Bắt đầu di chuyển đến điểm lấy hàng
                    </button>
                `;
                break;
                
            case 'waiting_driver_pick_up':
                buttons = `
                    <button class="status-btn status-btn-confirm" onclick="updateOrderStatus('${orderId}', 'driver_picked_up')">
                        <i data-lucide="package" style="width: 14px; height: 14px;"></i>
                        Đã có mặt - Xác nhận lấy hàng
                    </button>
                `;
                break;
                
            case 'driver_picked_up':
                buttons = `
                    <button class="status-btn status-btn-deliver" onclick="updateOrderStatus('${orderId}', 'in_transit')">
                        <i data-lucide="truck" style="width: 14px; height: 14px;"></i>
                        Bắt đầu giao hàng
                    </button>
                `;
                break;
                
            case 'in_transit':
                buttons = `
                    <button class="status-btn status-btn-complete" onclick="updateOrderStatus('${orderId}', 'delivered')">
                        <i data-lucide="check-circle" style="width: 14px; height: 14px;"></i>
                        Đã giao hàng thành công
                    </button>
                    <button class="status-btn status-btn-cancel" onclick="updateOrderStatus('${orderId}', 'delivery_failed')">
                        <i data-lucide="x-circle" style="width: 14px; height: 14px;"></i>
                        Giao hàng thất bại
                    </button>
                `;
                break;
                
            case 'delivered':
                buttons = `
                    <button class="status-btn status-btn-received" onclick="updateOrderStatus('${orderId}', 'item_received')">
                        <i data-lucide="user-check" style="width: 14px; height: 14px;"></i>
                        Khách đã nhận hàng
                    </button>
                `;
                break;
                
            case 'delivery_failed':
                buttons = `
                    <button class="status-btn status-btn-deliver" onclick="updateOrderStatus('${orderId}', 'in_transit')">
                        <i data-lucide="truck" style="width: 14px; height: 14px;"></i>
                        Thử giao lại
                    </button>
                `;
                break;
                
            default:
                // Không có nút cho các trạng thái khác
                buttons = '';
        }
        
        return buttons ? `<div class="status-actions">${buttons}</div>` : '';
    }

    // Update order status with modal confirmation
    function updateOrderStatus(orderId, newStatus) {
        const statusMessages = {
            'driver_confirmed': {
                title: 'Xác nhận nhận đơn',
                message: 'Bạn có chắc chắn muốn xác nhận nhận đơn hàng này?',
                successMessage: 'Đã xác nhận nhận đơn thành công!',
                icon: 'fas fa-check-circle',
                iconColor: 'green',
                confirmColor: 'green'
            },
            'cancelled': {
                title: 'Từ chối đơn hàng',
                message: 'Bạn có chắc chắn muốn từ chối đơn hàng này?',
                successMessage: 'Đã từ chối đơn hàng!',
                icon: 'fas fa-times-circle',
                iconColor: 'red',
                confirmColor: 'red'
            },
            'waiting_driver_pick_up': {
                title: 'Bắt đầu di chuyển',
                message: 'Bạn có sẵn sàng di chuyển đến điểm lấy hàng?',
                successMessage: 'Đã bắt đầu di chuyển đến điểm lấy hàng!',
                icon: 'fas fa-route',
                iconColor: 'blue',
                confirmColor: 'blue'
            },
            'driver_picked_up': {
                title: 'Xác nhận lấy hàng',
                message: 'Bạn đã lấy hàng thành công?',
                successMessage: 'Đã xác nhận lấy hàng thành công!',
                icon: 'fas fa-box',
                iconColor: 'green',
                confirmColor: 'green'
            },
            'in_transit': {
                title: 'Bắt đầu giao hàng',
                message: 'Bạn có sẵn sàng bắt đầu giao hàng?',
                successMessage: 'Đã bắt đầu giao hàng!',
                icon: 'fas fa-truck',
                iconColor: 'blue',
                confirmColor: 'blue'
            },
            'delivered': {
                title: 'Xác nhận giao hàng',
                message: 'Bạn đã giao hàng thành công?',
                successMessage: 'Đã giao hàng thành công!',
                icon: 'fas fa-check-circle',
                iconColor: 'green',
                confirmColor: 'green'
            },
            'delivery_failed': {
                title: 'Giao hàng thất bại',
                message: 'Xác nhận giao hàng thất bại?',
                successMessage: 'Đã cập nhật trạng thái giao hàng thất bại!',
                icon: 'fas fa-exclamation-triangle',
                iconColor: 'red',
                confirmColor: 'red'
            },
            'item_received': {
                title: 'Khách đã nhận hàng',
                message: 'Xác nhận khách hàng đã nhận hàng?',
                successMessage: 'Đã xác nhận khách hàng nhận hàng!',
                icon: 'fas fa-user-check',
                iconColor: 'green',
                confirmColor: 'green'
            }
        };

        const config = statusMessages[newStatus] || {
            title: 'Xác nhận',
            message: 'Bạn có chắc chắn muốn thực hiện hành động này?',
            successMessage: 'Cập nhật thành công!',
            icon: 'fas fa-question',
            iconColor: 'blue',
            confirmColor: 'blue'
        };

        showModal(config.title, config.message, () => {
            sendRequest(orderId, newStatus, config.successMessage);
        }, {
            icon: config.icon,
            iconColor: config.iconColor,
            confirmColor: config.confirmColor,
            confirmText: 'Xác nhận'
        });
    }

    // Send request to update order status
    async function sendRequest(orderId, newStatus, successMessage) {
        try {
            // Show loading state
            const statusElement = document.getElementById(`status-${orderId}`);
            if (statusElement) {
                statusElement.innerHTML = '<i data-lucide="loader-2" style="width: 12px; height: 12px; animation: spin 1s linear infinite;"></i> Đang cập nhật...';
            }

            // Extract numeric ID from orderId (remove 'ORD' prefix if present)
            const numericOrderId = orderId.toString().replace('ORD', '');

            // Send request to update status using batch route
            const response = await fetch(`/driver/orders/batch/{{ $batchId }}/${numericOrderId}/update-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    status: newStatus
                })
            });

            // Check if response is ok before parsing JSON
            if (!response.ok) {
                const text = await response.text();
                console.error('Server error:', text);
                throw new Error(`Server error: ${response.status} - ${response.statusText}`);
            }

            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                console.error('Non-JSON response:', text);
                throw new Error('Server trả về response không phải JSON. Có thể bạn chưa đăng nhập hoặc không có quyền truy cập.');
            }

            const result = await response.json();

            if (result.success) {
                // Update customer data
                const customerIndex = customers.findIndex(c => c.id === orderId);
                if (customerIndex !== -1) {
                    customers[customerIndex].orderStatus = newStatus;
                }

                // Re-render customer list to update UI
                renderCustomerList();
                renderBatchControls();
                
                // Update route and markers when status changes
                if (newStatus === 'in_transit' || newStatus === 'delivered') {
                    // Re-calculate and update route based on new status
                    setTimeout(async () => {
                        if (map && map.isStyleLoaded()) {
                            await addRouteLayer();
                            // Hide pickup markers when in delivery mode
                            hidePickupMarkers();
                            // Show delivery markers when in delivery mode
                            showDeliveryMarkers();
                        }
                    }, 500);
                } else {
                    // Show pickup markers when not in delivery mode
                    setTimeout(() => {
                        if (map && map.isStyleLoaded()) {
                            showPickupMarkers();
                            // Hide delivery markers when not in delivery mode
                            hideDeliveryMarkers();
                        }
                    }, 500);
                }

                // Show success toast
                showToast('success', {
                    message: successMessage,
                    duration: 3000
                });
            } else {
                throw new Error(result.message || 'Có lỗi xảy ra');
            }
        } catch (error) {
            console.error('Error updating order status:', error);
            showToast('error', {
                message: 'Không thể cập nhật trạng thái: ' + error.message,
                duration: 5000
            });
            
            // Restore original status display
            renderCustomerList();
        }
    }



    // Batch control functions
    function getBatchStatus() {
        const statuses = customers.map(c => c.orderStatus);
        const uniqueStatuses = [...new Set(statuses)];
        
        if (uniqueStatuses.length === 1) {
            return uniqueStatuses[0];
        } else {
            // Mixed statuses - find the most common one or earliest stage
            const statusOrder = ['awaiting_driver', 'driver_confirmed', 'waiting_driver_pick_up', 'driver_picked_up', 'in_transit', 'delivered', 'delivery_failed', 'item_received', 'cancelled'];
            for (let status of statusOrder) {
                if (statuses.includes(status)) {
                    return status;
                }
            }
        }
        return 'mixed';
    }

    function renderBatchControls() {
        const batchStatusInfo = document.getElementById('batch-status-info');
        const batchActions = document.getElementById('batch-actions');
        
        if (!batchStatusInfo || !batchActions) return;
        
        const currentBatchStatus = getBatchStatus();
        const statuses = customers.map(c => c.orderStatus);
        const uniqueStatuses = [...new Set(statuses)];
        
        // Render status info
        let statusIcon, statusTitle, statusDescription, iconColor;
        
        if (uniqueStatuses.length === 1) {
            switch (currentBatchStatus) {
                case 'awaiting_driver':
                    statusIcon = 'clock';
                    statusTitle = 'Chờ xác nhận';
                    statusDescription = 'Tất cả đơn hàng đang chờ tài xế xác nhận';
                    iconColor = 'var(--warning-color)';
                    break;
                case 'driver_confirmed':
                    statusIcon = 'check-circle';
                    statusTitle = 'Đã xác nhận';
                    statusDescription = 'Tất cả đơn hàng đã được xác nhận';
                    iconColor = 'var(--success-color)';
                    break;
                case 'waiting_driver_pick_up':
                    statusIcon = 'navigation';
                    statusTitle = 'Đang di chuyển';
                    statusDescription = 'Đang di chuyển đến điểm lấy hàng';
                    iconColor = 'var(--primary-color)';
                    break;
                case 'driver_picked_up':
                    statusIcon = 'package';
                    statusTitle = 'Đã lấy hàng';
                    statusDescription = 'Đã lấy tất cả hàng, sẵn sàng giao';
                    iconColor = 'var(--purple-color)';
                    break;
                case 'in_transit':
                    statusIcon = 'truck';
                    statusTitle = 'Đang giao hàng';
                    statusDescription = 'Đang giao hàng cho từng khách hàng';
                    iconColor = 'var(--warning-color)';
                    break;
                default:
                    statusIcon = 'info';
                    statusTitle = 'Trạng thái khác';
                    statusDescription = 'Đơn hàng ở trạng thái: ' + getStatusText(currentBatchStatus);
                    iconColor = 'var(--gray-500)';
            }
        } else {
            statusIcon = 'layers';
            statusTitle = 'Trạng thái hỗn hợp';
            statusDescription = `Có ${uniqueStatuses.length} trạng thái khác nhau trong batch`;
            iconColor = 'var(--gray-500)';
        }
        
        batchStatusInfo.innerHTML = `
            <div class="batch-status-summary">
                <div class="batch-status-icon" style="background: ${iconColor};">
                    <i data-lucide="${statusIcon}" style="width: 20px; height: 20px;"></i>
                </div>
                <div class="batch-status-text">
                    <div class="batch-status-title">${statusTitle}</div>
                    <div class="batch-status-description">${statusDescription}</div>
                </div>
            </div>
        `;
        
        // Render action buttons
        let actionButtons = '';
        
        if (uniqueStatuses.length === 1) {
            switch (currentBatchStatus) {
                case 'awaiting_driver':
                    actionButtons = `
                        <button class="batch-btn batch-btn-success" onclick="updateBatchStatus('driver_confirmed')">
                            <i data-lucide="check" style="width: 16px; height: 16px;"></i>
                            Xác nhận tất cả đơn
                        </button>
                        <button class="batch-btn batch-btn-danger" onclick="updateBatchStatus('cancelled')">
                            <i data-lucide="x" style="width: 16px; height: 16px;"></i>
                            Từ chối batch
                        </button>
                    `;
                    break;
                case 'driver_confirmed':
                    actionButtons = `
                        <button class="batch-btn batch-btn-primary" onclick="updateBatchStatus('waiting_driver_pick_up')">
                            <i data-lucide="navigation" style="width: 16px; height: 16px;"></i>
                            Bắt đầu di chuyển lấy hàng
                        </button>
                    `;
                    break;
                case 'waiting_driver_pick_up':
                    actionButtons = `
                        <button class="batch-btn batch-btn-success" onclick="updateBatchStatus('driver_picked_up')">
                            <i data-lucide="package" style="width: 16px; height: 16px;"></i>
                            Xác nhận đã lấy tất cả hàng
                        </button>
                    `;
                    break;
                case 'driver_picked_up':
                    actionButtons = `
                        <button class="batch-btn batch-btn-warning" onclick="updateBatchStatus('in_transit')">
                            <i data-lucide="truck" style="width: 16px; height: 16px;"></i>
                            Bắt đầu giao hàng
                        </button>
                    `;
                    break;
            }
        } else {
            actionButtons = `
                <div style="padding: 12px; text-align: center; color: var(--gray-600); font-size: 14px;">
                    <i data-lucide="info" style="width: 16px; height: 16px; margin-right: 8px;"></i>
                    Các đơn hàng có trạng thái khác nhau. Vui lòng cập nhật từng đơn riêng lẻ.
                </div>
            `;
        }
        
        batchActions.innerHTML = actionButtons;
        
        // Re-create icons
        setTimeout(() => lucide.createIcons(), 10);
    }

    async function updateBatchStatus(newStatus) {
        const statusMessages = {
            'driver_confirmed': {
                title: 'Xác nhận nhận batch',
                message: 'Bạn có chắc chắn muốn xác nhận nhận tất cả đơn hàng trong batch này?',
                successMessage: 'Đã xác nhận nhận tất cả đơn hàng trong batch!'
            },
            'waiting_driver_pick_up': {
                title: 'Bắt đầu di chuyển',
                message: 'Bạn có chắc chắn muốn bắt đầu di chuyển đến điểm lấy hàng?',
                successMessage: 'Đã bắt đầu di chuyển đến điểm lấy hàng!'
            },
            'driver_picked_up': {
                title: 'Xác nhận lấy hàng',
                message: 'Bạn có chắc chắn đã lấy tất cả hàng trong batch này?',
                successMessage: 'Đã xác nhận lấy tất cả hàng!'
            },
            'in_transit': {
                title: 'Bắt đầu giao hàng',
                message: 'Bạn có chắc chắn muốn bắt đầu giao hàng? Sau bước này, bạn sẽ giao hàng cho từng khách hàng riêng lẻ.',
                successMessage: 'Đã bắt đầu giao hàng! Bây giờ bạn có thể giao hàng cho từng khách hàng.'
            },
            'cancelled': {
                title: 'Từ chối batch',
                message: 'Bạn có chắc chắn muốn từ chối toàn bộ batch này?',
                successMessage: 'Đã từ chối batch!'
            }
        };
        
        const config = statusMessages[newStatus];
        if (!config) return;
        
        // Show confirmation modal
        const confirmed = await new Promise((resolve) => {
            showModal(config.title, config.message, () => resolve(true), {
                icon: 'fas fa-question',
                iconColor: 'blue',
                confirmColor: 'blue',
                confirmText: 'Xác nhận',
                onCancel: () => resolve(false)
            });
        });
        
        if (!confirmed) return;
        
        try {
            // Update UI to show loading
            const batchActions = document.getElementById('batch-actions');
            if (batchActions) {
                batchActions.innerHTML = `
                    <div style="padding: 20px; text-align: center; color: var(--gray-600);">
                        <i data-lucide="loader-2" style="width: 20px; height: 20px; animation: spin 1s linear infinite; margin-right: 8px;"></i>
                        Đang cập nhật trạng thái...
                    </div>
                `;
                lucide.createIcons();
            }
            
            // Use the first order ID for the batch update
            const firstOrderId = customers[0].id.toString().replace('ORD', '');
            
            const response = await fetch(`/driver/orders/batch/{{ $batchId }}/${firstOrderId}/update-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    status: newStatus
                })
            });
            
            if (!response.ok) {
                throw new Error(`Server error: ${response.status}`);
            }
            
            const result = await response.json();
            
            if (result.success) {
                // Update all customer statuses
                customers.forEach(customer => {
                    customer.orderStatus = newStatus;
                });
                
                // Re-render everything
                renderCustomerList();
                renderBatchControls();
                
                // Update map if needed
                if (newStatus === 'in_transit') {
                    setTimeout(async () => {
                        if (map && map.isStyleLoaded()) {
                            await addRouteLayer();
                            hidePickupMarkers();
                        }
                    }, 500);
                }
                
                showToast('success', {
                    message: config.successMessage,
                    duration: 3000
                });
            } else {
                throw new Error(result.message || 'Có lỗi xảy ra');
            }
        } catch (error) {
            console.error('Error updating batch status:', error);
            showToast('error', {
                message: 'Không thể cập nhật trạng thái batch: ' + error.message,
                duration: 5000
            });
            
            // Restore original controls
            renderBatchControls();
        }
    }

    // Initialize everything when page loads
    document.addEventListener('DOMContentLoaded', () => {
        initMap();
        renderCustomerList();
        renderBatchControls();
        setTimeout(() => lucide.createIcons(), 100);
    });

    // Handle window resize
    window.addEventListener('resize', () => {
        if (map) {
            setTimeout(() => {
                map.resize();
            }, 100);
        }
    });
</script>
@endpush
