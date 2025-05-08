@extends('layouts/admin/contentLayoutMaster')
@section('content')
<div class="data-table-wrapper">
        <!-- Header chính -->
        <div class="data-table-main-header">
            <div class="data-table-brand">
                <div class="data-table-logo">
                    <i class="fas fa-layer-group"></i>
                </div>
                <h1 class="data-table-title">Data Management</h1>
            </div>
            <div class="data-table-header-actions">
                <button class="data-table-btn data-table-btn-outline">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <button class="data-table-btn data-table-btn-outline">
                    <i class="fas fa-download"></i> Export
                </button>
                <button class="data-table-btn data-table-btn-primary">
                    <i class="fas fa-plus"></i> Add New
                </button>
            </div>
        </div>
        
        <!-- Card chứa bảng -->
        <div class="data-table-card">
            <!-- Tiêu đề bảng -->
            <div class="data-table-header">
                <h2 class="data-table-card-title">Transaction Records</h2>
            </div>
            
            <!-- Thanh công cụ -->
            <div class="data-table-controls">
                <div class="data-table-search">
                    <i class="fas fa-search data-table-search-icon"></i>
                    <input type="text" placeholder="Search by name, email or ID..." id="dataTableSearch">
                </div>
                <div class="data-table-actions">
                    <button class="data-table-btn data-table-btn-outline">
                        <i class="fas fa-sliders"></i> Columns
                    </button>
                    <button class="data-table-btn data-table-btn-outline">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
            </div>
            
            <!-- Container bảng -->
            <div class="data-table-container">
                <table class="data-table" id="dataTable">
                    <thead>
                        <tr>
                            <th data-sort="id" class="active-sort">
                                ID <i class="fas fa-arrow-up data-table-sort-icon"></i>
                            </th>
                            <th data-sort="name">
                                Customer <i class="fas fa-sort data-table-sort-icon"></i>
                            </th>
                            <th data-sort="amount">
                                Amount <i class="fas fa-sort data-table-sort-icon"></i>
                            </th>
                            <th data-sort="status">
                                Status <i class="fas fa-sort data-table-sort-icon"></i>
                            </th>
                            <th data-sort="date">
                                Date <i class="fas fa-sort data-table-sort-icon"></i>
                            </th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="dataTableBody">
                        <tr>
                            <td>
                                <div class="data-table-id">
                                    <span class="data-table-id-icon"><i class="fas fa-receipt"></i></span>
                                    INV001
                                </div>
                            </td>
                            <td>
                                <div class="data-table-customer">
                                    <div class="data-table-avatar">JD</div>
                                    <div class="data-table-customer-details">
                                        <div class="data-table-customer-name">John Doe</div>
                                        <div class="data-table-customer-email">john.doe@example.com</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="data-table-amount">$1,250.00</div>
                            </td>
                            <td>
                                <span class="data-table-status data-table-status-pending">
                                    <i class="fas fa-clock"></i> Pending
                                </span>
                            </td>
                            <td>15 Jan 2023</td>
                            <td>
                                <div class="data-table-action-buttons">
                                    <button class="data-table-action-btn data-table-tooltip" data-tooltip="View details" onclick="showRecord('INV001')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="data-table-action-btn edit data-table-tooltip" data-tooltip="Edit record" onclick="editRecord('INV001')">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button class="data-table-action-btn delete data-table-tooltip" data-tooltip="Delete record" onclick="deleteRecord('INV001')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="data-table-id">
                                    <span class="data-table-id-icon"><i class="fas fa-receipt"></i></span>
                                    INV002
                                </div>
                            </td>
                            <td>
                                <div class="data-table-customer">
                                    <div class="data-table-avatar">JS</div>
                                    <div class="data-table-customer-details">
                                        <div class="data-table-customer-name">Jane Smith</div>
                                        <div class="data-table-customer-email">jane.smith@example.com</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="data-table-amount">$350.00</div>
                            </td>
                            <td>
                                <span class="data-table-status data-table-status-processing">
                                    <i class="fas fa-spinner"></i> Processing
                                </span>
                            </td>
                            <td>20 Feb 2023</td>
                            <td>
                                <div class="data-table-action-buttons">
                                    <button class="data-table-action-btn data-table-tooltip" data-tooltip="View details" onclick="showRecord('INV002')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="data-table-action-btn edit data-table-tooltip" data-tooltip="Edit record" onclick="editRecord('INV002')">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button class="data-table-action-btn delete data-table-tooltip" data-tooltip="Delete record" onclick="deleteRecord('INV002')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="data-table-id">
                                    <span class="data-table-id-icon"><i class="fas fa-receipt"></i></span>
                                    INV003
                                </div>
                            </td>
                            <td>
                                <div class="data-table-customer">
                                    <div class="data-table-avatar">RJ</div>
                                    <div class="data-table-customer-details">
                                        <div class="data-table-customer-name">Robert Johnson</div>
                                        <div class="data-table-customer-email">robert.johnson@example.com</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="data-table-amount">$5,000.00</div>
                            </td>
                            <td>
                                <span class="data-table-status data-table-status-success">
                                    <i class="fas fa-check"></i> Success
                                </span>
                            </td>
                            <td>10 Mar 2023</td>
                            <td>
                                <div class="data-table-action-buttons">
                                    <button class="data-table-action-btn data-table-tooltip" data-tooltip="View details" onclick="showRecord('INV003')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="data-table-action-btn edit data-table-tooltip" data-tooltip="Edit record" onclick="editRecord('INV003')">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button class="data-table-action-btn delete data-table-tooltip" data-tooltip="Delete record" onclick="deleteRecord('INV003')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="data-table-id">
                                    <span class="data-table-id-icon"><i class="fas fa-receipt"></i></span>
                                    INV004
                                </div>
                            </td>
                            <td>
                                <div class="data-table-customer">
                                    <div class="data-table-avatar">ED</div>
                                    <div class="data-table-customer-details">
                                        <div class="data-table-customer-name">Emily Davis</div>
                                        <div class="data-table-customer-email">emily.davis@example.com</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="data-table-amount">$750.00</div>
                            </td>
                            <td>
                                <span class="data-table-status data-table-status-failed">
                                    <i class="fas fa-times"></i> Failed
                                </span>
                            </td>
                            <td>05 Apr 2023</td>
                            <td>
                                <div class="data-table-action-buttons">
                                    <button class="data-table-action-btn data-table-tooltip" data-tooltip="View details" onclick="showRecord('INV004')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="data-table-action-btn edit data-table-tooltip" data-tooltip="Edit record" onclick="editRecord('INV004')">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button class="data-table-action-btn delete data-table-tooltip" data-tooltip="Delete record" onclick="deleteRecord('INV004')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="data-table-id">
                                    <span class="data-table-id-icon"><i class="fas fa-receipt"></i></span>
                                    INV005
                                </div>
                            </td>
                            <td>
                                <div class="data-table-customer">
                                    <div class="data-table-avatar">MW</div>
                                    <div class="data-table-customer-details">
                                        <div class="data-table-customer-name">Michael Wilson</div>
                                        <div class="data-table-customer-email">michael.wilson@example.com</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="data-table-amount">$2,100.00</div>
                            </td>
                            <td>
                                <span class="data-table-status data-table-status-success">
                                    <i class="fas fa-check"></i> Success
                                </span>
                            </td>
                            <td>12 May 2023</td>
                            <td>
                                <div class="data-table-action-buttons">
                                    <button class="data-table-action-btn data-table-tooltip" data-tooltip="View details" onclick="showRecord('INV005')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="data-table-action-btn edit data-table-tooltip" data-tooltip="Edit record" onclick="editRecord('INV005')">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button class="data-table-action-btn delete data-table-tooltip" data-tooltip="Delete record" onclick="deleteRecord('INV005')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="data-table-id">
                                    <span class="data-table-id-icon"><i class="fas fa-receipt"></i></span>
                                    INV006
                                </div>
                            </td>
                            <td>
                                <div class="data-table-customer">
                                    <div class="data-table-avatar">SB</div>
                                    <div class="data-table-customer-details">
                                        <div class="data-table-customer-name">Sarah Brown</div>
                                        <div class="data-table-customer-email">sarah.brown@example.com</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="data-table-amount">$890.00</div>
                            </td>
                            <td>
                                <span class="data-table-status data-table-status-pending">
                                    <i class="fas fa-clock"></i> Pending
                                </span>
                            </td>
                            <td>18 Jun 2023</td>
                            <td>
                                <div class="data-table-action-buttons">
                                    <button class="data-table-action-btn data-table-tooltip" data-tooltip="View details" onclick="showRecord('INV006')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="data-table-action-btn edit data-table-tooltip" data-tooltip="Edit record" onclick="editRecord('INV006')">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button class="data-table-action-btn delete data-table-tooltip" data-tooltip="Delete record" onclick="deleteRecord('INV006')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="data-table-id">
                                    <span class="data-table-id-icon"><i class="fas fa-receipt"></i></span>
                                    INV007
                                </div>
                            </td>
                            <td>
                                <div class="data-table-customer">
                                    <div class="data-table-avatar">DM</div>
                                    <div class="data-table-customer-details">
                                        <div class="data-table-customer-name">David Miller</div>
                                        <div class="data-table-customer-email">david.miller@example.com</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="data-table-amount">$1,500.00</div>
                            </td>
                            <td>
                                <span class="data-table-status data-table-status-processing">
                                    <i class="fas fa-spinner"></i> Processing
                                </span>
                            </td>
                            <td>22 Jul 2023</td>
                            <td>
                                <div class="data-table-action-buttons">
                                    <button class="data-table-action-btn data-table-tooltip" data-tooltip="View details" onclick="showRecord('INV007')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="data-table-action-btn edit data-table-tooltip" data-tooltip="Edit record" onclick="editRecord('INV007')">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button class="data-table-action-btn delete data-table-tooltip" data-tooltip="Delete record" onclick="deleteRecord('INV007')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <!-- Trạng thái trống (ẩn mặc định) -->
                <div class="data-table-empty" style="display: none;" id="dataTableEmpty">
                    <i class="fas fa-search"></i>
                    <h3>No records found</h3>
                    <p>Try adjusting your search or filter to find what you're looking for.</p>
                </div>
            </div>
            
            <!-- Phân trang -->
            <div class="data-table-footer">
                <div class="data-table-pagination-info">
                    Showing <span id="startRecord">1</span> to <span id="endRecord">7</span> of <span id="totalRecords">10</span> entries
                </div>
                <div class="data-table-pagination-controls">
                    <button class="data-table-pagination-btn" id="prevBtn" disabled>
                        <i class="fas fa-chevron-left"></i> Previous
                    </button>
                    <button class="data-table-pagination-btn active">1</button>
                    <button class="data-table-pagination-btn">2</button>
                    <button class="data-table-pagination-btn" id="nextBtn">
                        Next <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
@endsection



