@extends('layouts/admin/contentLayoutMaster')

@section('title', 'Edit Promotion Program')

@section('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.28.0/feather.min.css" rel="stylesheet">
<style>
    .container {
        max-width: 1200px;
        margin: 0 auto;
        animation: fadeIn 0.6s ease-out;
        padding: 20px;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .header {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 15px;
        padding: 25px 30px;
        margin-bottom: 25px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .header h1 {
        color: #2d3748;
        font-size: 28px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 0;
    }

    .header .icon {
        width: 32px;
        height: 32px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }

    .breadcrumb {
        margin-top: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
        color: #718096;
        font-size: 14px;
    }

    .breadcrumb a {
        color: #667eea;
        text-decoration: none;
        transition: color 0.2s;
    }

    .breadcrumb a:hover {
        color: #764ba2;
    }

    .form-container {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
    }

    .form-section {
        background: #f8fafc;
        border-radius: 12px;
        padding: 25px;
        border: 1px solid #e2e8f0;
    }

    .form-section h3 {
        color: #2d3748;
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-section h3 i {
        color: #667eea;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    label {
        display: block;
        margin-bottom: 8px;
        color: #374151;
        font-weight: 500;
        font-size: 14px;
    }

    .required {
        color: #ef4444;
    }

    .form-control,
    input[type="text"],
    input[type="number"],
    input[type="datetime-local"],
    input[type="file"],
    textarea,
    select {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.2s;
        background: white;
    }

    .form-control:focus,
    input[type="text"]:focus,
    input[type="number"]:focus,
    input[type="datetime-local"]:focus,
    textarea:focus,
    select:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    textarea {
        resize: vertical;
        min-height: 100px;
    }

    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 16px;
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        transition: all 0.2s;
        cursor: pointer;
    }

    .checkbox-group:hover {
        border-color: #667eea;
    }

    .checkbox-group input[type="checkbox"] {
        width: auto;
        margin: 0;
    }

    .checkbox-group label {
        margin: 0;
        cursor: pointer;
        flex: 1;
    }

    .file-input-wrapper {
        position: relative;
        overflow: hidden;
        display: inline-block;
        width: 100%;
    }

    .file-input-wrapper input[type="file"] {
        position: absolute;
        left: -9999px;
    }

    .file-input-label {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 16px;
        background: white;
        border: 2px dashed #d1d5db;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
        color: #6b7280;
    }

    .file-input-label:hover {
        border-color: #667eea;
        background: #f8fafc;
    }

    .file-input-label i {
        color: #667eea;
    }

    .current-file {
        margin-top: 8px;
        padding: 8px 12px;
        background: #f0f9ff;
        border: 1px solid #bae6fd;
        border-radius: 6px;
        font-size: 12px;
        color: #0369a1;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .current-image {
        margin-top: 10px;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #e5e7eb;
    }

    .current-image img {
        width: 100%;
        max-width: 200px;
        height: auto;
        display: block;
    }

    .branch-selection {
        display: none;
    }

    .branch-selection.show {
        display: block;
    }

    select[multiple] {
        min-height: 120px;
    }

    .form-actions {
        margin-top: 30px;
        padding-top: 25px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        gap: 15px;
        justify-content: flex-end;
    }

    .btn {
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
        font-size: 14px;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    }

    .btn-secondary {
        background: #f8fafc;
        color: #374151;
        border: 1px solid #d1d5db;
    }

    .btn-secondary:hover {
        background: #e5e7eb;
    }

    .alert {
        padding: 16px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        border-left: 4px solid;
    }

    .alert-danger {
        background: #fef2f2;
        border-color: #ef4444;
        color: #dc2626;
    }

    .alert ul {
        margin: 0;
        padding-left: 20px;
    }

    .alert li {
        margin-bottom: 4px;
    }

    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        .form-actions {
            flex-direction: column;
        }
        
        .btn {
            justify-content: center;
        }
    }
</style>
@endsection

@section('content')
<div class="container">
    <!-- Header -->
    <div class="header">
        <h1>
            <div class="icon">
                <i data-feather="edit-3"></i>
            </div>
            Edit {{ $program->name }}
        </h1>
        <div class="breadcrumb">
            <a href="{{ route('admin.promotions.index') }}">Promotion Programs</a>
            <i data-feather="chevron-right"></i>
            <a href="{{ route('admin.promotions.show', $program) }}">{{ $program->name }}</a>
            <i data-feather="chevron-right"></i>
            <span>Edit</span>
        </div>
    </div>

    <!-- Error Messages -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form -->
    <div class="form-container">
        <form action="{{ route('admin.promotions.update', $program) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="form-grid">
                <!-- Basic Information -->
                <div class="form-section">
                    <h3>
                        <i data-feather="info"></i>
                        Basic Information
                    </h3>
                    
                    <div class="form-group">
                        <label for="name">Program Name <span class="required">*</span></label>
                        <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $program->name) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" class="form-control" placeholder="Enter program description...">{{ old('description', $program->description) }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="display_order">Display Order <span class="required">*</span></label>
                        <input type="number" id="display_order" name="display_order" class="form-control" value="{{ old('display_order', $program->display_order) }}" min="0" required>
                    </div>
                </div>

                <!-- Schedule & Status -->
                <div class="form-section">
                    <h3>
                        <i data-feather="calendar"></i>
                        Schedule & Status
                    </h3>
                    
                    <div class="form-group">
                        <label for="start_date">Start Date <span class="required">*</span></label>
                        <input type="datetime-local" id="start_date" name="start_date" class="form-control" value="{{ old('start_date', $program->start_date->format('Y-m-d\TH:i')) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="end_date">End Date <span class="required">*</span></label>
                        <input type="datetime-local" id="end_date" name="end_date" class="form-control" value="{{ old('end_date', $program->end_date->format('Y-m-d\TH:i')) }}" required>
                    </div>

                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $program->is_active) ? 'checked' : '' }}>
                            <label for="is_active">Active Program</label>
                            <i data-feather="toggle-right"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured', $program->is_featured) ? 'checked' : '' }}>
                            <label for="is_featured">Featured Program</label>
                            <i data-feather="star"></i>
                        </div>
                    </div>
                </div>

                <!-- Images -->
                <div class="form-section">
                    <h3>
                        <i data-feather="image"></i>
                        Images
                    </h3>
                    
                    <div class="form-group">
                        <label for="banner_image">Banner Image</label>
                        <div class="file-input-wrapper">
                            <input type="file" id="banner_image" name="banner_image" accept="image/*">
                            <label for="banner_image" class="file-input-label">
                                <i data-feather="upload"></i>
                                <span>Choose new banner image...</span>
                            </label>
                        </div>
                        @if ($program->banner_image)
                            <div class="current-file">
                                <i data-feather="file-text"></i>
                                Current: {{ basename($program->banner_image) }}
                            </div>
                            <div class="current-image">
                                <img src="{{ asset('storage/' . $program->banner_image) }}" alt="Current Banner">
                            </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="thumbnail_image">Thumbnail Image</label>
                        <div class="file-input-wrapper">
                            <input type="file" id="thumbnail_image" name="thumbnail_image" accept="image/*">
                            <label for="thumbnail_image" class="file-input-label">
                                <i data-feather="upload"></i>
                                <span>Choose new thumbnail image...</span>
                            </label>
                        </div>
                        @if ($program->thumbnail_image)
                            <div class="current-file">
                                <i data-feather="file-text"></i>
                                Current: {{ basename($program->thumbnail_image) }}
                            </div>
                            <div class="current-image">
                                <img src="{{ asset('storage/' . $program->thumbnail_image) }}" alt="Current Thumbnail">
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Scope & Branches -->
                <div class="form-section">
                    <h3>
                        <i data-feather="map-pin"></i>
                        Applicable Scope
                    </h3>
                    
                    <div class="form-group">
                        <label for="applicable_scope">Scope <span class="required">*</span></label>
                        <select id="applicable_scope" name="applicable_scope" class="form-control" required>
                            <option value="all_branches" {{ old('applicable_scope', $program->applicable_scope) == 'all_branches' ? 'selected' : '' }}>All Branches</option>
                            <option value="specific_branches" {{ old('applicable_scope', $program->applicable_scope) == 'specific_branches' ? 'selected' : '' }}>Specific Branches</option>
                        </select>
                    </div>

                    <div class="form-group branch-selection {{ $program->applicable_scope == 'specific_branches' ? 'show' : '' }}" id="branch_selection">
                        <label for="branch_ids">Select Branches <span class="required">*</span></label>
                        <select name="branch_ids[]" id="branch_ids" class="form-control" multiple>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}" {{ $program->branches->contains($branch->id) || in_array($branch->id, old('branch_ids', [])) ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <a href="{{ route('admin.promotions.show', $program) }}" class="btn btn-secondary">
                    <i data-feather="x"></i>
                    Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i data-feather="save"></i>
                    Update Program
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/feather-icons"></script>
<script>
    feather.replace();

    // Handle scope selection
    document.getElementById('applicable_scope').addEventListener('change', function() {
        const branchSelection = document.getElementById('branch_selection');
        if (this.value === 'specific_branches') {
            branchSelection.classList.add('show');
        } else {
            branchSelection.classList.remove('show');
        }
    });

    // File input labels
    document.querySelectorAll('input[type="file"]').forEach(input => {
        input.addEventListener('change', function() {
            const label = this.nextElementSibling.querySelector('span');
            if (this.files.length > 0) {
                label.textContent = this.files[0].name;
            } else {
                if (this.id === 'banner_image') {
                    label.textContent = 'Choose new banner image...';
                } else {
                    label.textContent = 'Choose new thumbnail image...';
                }
            }
        });
    });
</script>
@endsection
