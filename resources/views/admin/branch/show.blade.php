<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết chi nhánh</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --background: #ffffff;
            --foreground: #0f172a;
            --muted: #f1f5f9;
            --muted-foreground: #64748b;
            --card: #ffffff;
            --card-foreground: #0f172a;
            --border: #e2e8f0;
            --input: #e2e8f0;
            --primary: #3b82f6;
            --primary-foreground: #ffffff;
            --secondary: #f1f5f9;
            --secondary-foreground: #0f172a;
            --accent: #f1f5f9;
            --accent-foreground: #0f172a;
            --destructive: #ef4444;
            --destructive-foreground: #ffffff;
            --success: #22c55e;
            --success-foreground: #ffffff;
            --radius: 0.5rem;
            --font-sans: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-sans);
            background-color: var(--background);
            color: var(--foreground);
            line-height: 1.5;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1.5rem 1rem;
        }

        .flex {
            display: flex;
        }

        .flex-col {
            flex-direction: column;
        }

        .items-center {
            align-items: center;
        }

        .justify-between {
            justify-content: space-between;
        }

        .justify-center {
            justify-content: center;
        }

        .gap-2 {
            gap: 0.5rem;
        }

        .gap-3 {
            gap: 0.75rem;
        }

        .gap-4 {
            gap: 1rem;
        }

        .gap-6 {
            gap: 1.5rem;
        }

        .mb-1 {
            margin-bottom: 0.25rem;
        }

        .mb-2 {
            margin-bottom: 0.5rem;
        }

        .mb-3 {
            margin-bottom: 0.75rem;
        }

        .mb-4 {
            margin-bottom: 1rem;
        }

        .mb-6 {
            margin-bottom: 1.5rem;
        }

        .mb-8 {
            margin-bottom: 2rem;
        }

        .ml-1 {
            margin-left: 0.25rem;
        }

        .ml-2 {
            margin-left: 0.5rem;
        }

        .ml-3 {
            margin-left: 0.75rem;
        }

        .ml-11 {
            margin-left: 2.75rem;
        }

        .mr-1 {
            margin-right: 0.25rem;
        }

        .mr-2 {
            margin-right: 0.5rem;
        }

        .mt-1 {
            margin-top: 0.25rem;
        }

        .mt-2 {
            margin-top: 0.5rem;
        }

        .mt-3 {
            margin-top: 0.75rem;
        }

        .mt-4 {
            margin-top: 1rem;
        }

        .mt-5 {
            margin-top: 1.25rem;
        }

        .mt-10 {
            margin-top: 2.5rem;
        }

        .-mt-8 {
            margin-top: -2rem;
        }

        .-mt-10 {
            margin-top: -2.5rem;
        }

        .px-2 {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }

        .px-3 {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }

        .px-4 {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .px-6 {
            padding-left: 1.5rem;
            padding-right: 1.5rem;
        }

        .py-1 {
            padding-top: 0.25rem;
            padding-bottom: 0.25rem;
        }

        .py-1\.5 {
            padding-top: 0.375rem;
            padding-bottom: 0.375rem;
        }

        .py-2 {
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
        }

        .py-4 {
            padding-top: 1rem;
            padding-bottom: 1rem;
        }

        .py-6 {
            padding-top: 1.5rem;
            padding-bottom: 1.5rem;
        }

        .py-8 {
            padding-top: 2rem;
            padding-bottom: 2rem;
        }

        .pb-2 {
            padding-bottom: 0.5rem;
        }

        .pb-6 {
            padding-bottom: 1.5rem;
        }

        .pl-4 {
            padding-left: 1rem;
        }

        .pr-4 {
            padding-right: 1rem;
        }

        .text-center {
            text-align: center;
        }

        .text-xs {
            font-size: 0.75rem;
        }

        .text-sm {
            font-size: 0.875rem;
        }

        .text-lg {
            font-size: 1.125rem;
        }

        .text-xl {
            font-size: 1.25rem;
        }

        .text-2xl {
            font-size: 1.5rem;
        }

        .font-medium {
            font-weight: 500;
        }

        .font-semibold {
            font-weight: 600;
        }

        .font-bold {
            font-weight: 700;
        }

        .font-mono {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        }

        .text-white {
            color: #ffffff;
        }

        .text-primary {
            color: var(--primary);
        }

        .text-muted-foreground {
            color: var(--muted-foreground);
        }

        .text-green-600 {
            color: #16a34a;
        }

        .text-red-600 {
            color: #dc2626;
        }

        .text-amber-600 {
            color: #d97706;
        }

        .text-amber-800 {
            color: #92400e;
        }

        .text-blue-600 {
            color: #2563eb;
        }

        .text-blue-700 {
            color: #1d4ed8;
        }

        .text-blue-800 {
            color: #1e40af;
        }

        .bg-primary {
            background-color: var(--primary);
        }

        .bg-card {
            background-color: var(--card);
        }

        .bg-green-100 {
            background-color: #dcfce7;
        }

        .bg-red-100 {
            background-color: #fee2e2;
        }

        .bg-amber-50 {
            background-color: #fffbeb;
        }

        .bg-amber-100 {
            background-color: #fef3c7;
        }

        .bg-blue-50 {
            background-color: #eff6ff;
        }

        .bg-blue-100 {
            background-color: #dbeafe;
        }

        .bg-blue-600 {
            background-color: #2563eb;
        }

        .bg-primary\/10 {
            background-color: rgba(59, 130, 246, 0.1);
        }

        .bg-gradient-to-r {
            background-image: linear-gradient(to right, var(--tw-gradient-stops));
        }

        .from-primary {
            --tw-gradient-from: var(--primary);
            --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(59, 130, 246, 0));
        }

        .to-primary\/70 {
            --tw-gradient-to: rgba(59, 130, 246, 0.7);
        }

        .rounded-full {
            border-radius: 9999px;
        }

        .rounded-lg {
            border-radius: var(--radius);
        }

        .rounded-md {
            border-radius: calc(var(--radius) - 0.125rem);
        }

        .border {
            border: 1px solid var(--border);
        }

        .border-blue-200 {
            border-color: #bfdbfe;
        }

        .shadow-sm {
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        .shadow-md {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .shadow-lg {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .transition-all {
            transition-property: all;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 300ms;
        }

        .transition-colors {
            transition-property: color, background-color, border-color;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 150ms;
        }

        .transition-transform {
            transition-property: transform;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 300ms;
        }

        .duration-300 {
            transition-duration: 300ms;
        }

        .hover\:scale-105:hover {
            transform: scale(1.05);
        }

        .hover\:-translate-y-1:hover {
            transform: translateY(-0.25rem);
        }

        .hover\:bg-blue-50:hover {
            background-color: #eff6ff;
        }

        .hover\:bg-green-200:hover {
            background-color: #bbf7d0;
        }

        .hover\:bg-primary:hover {
            background-color: var(--primary);
        }

        .hover\:bg-blue-600:hover {
            background-color: #2563eb;
        }

        .hover\:bg-green-600:hover {
            background-color: #16a34a;
        }

        .hover\:bg-amber-600:hover {
            background-color: #d97706;
        }

        .hover\:text-white:hover {
            color: #ffffff;
        }

        .hover\:underline:hover {
            text-decoration: underline;
        }

        .hover\:shadow-md:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .grid {
            display: grid;
        }

        .grid-cols-1 {
            grid-template-columns: repeat(1, minmax(0, 1fr));
        }

        .grid-cols-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .grid-cols-3 {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .divide-x > * + * {
            border-left-width: 1px;
            border-left-style: solid;
            border-left-color: var(--border);
        }

        .divide-y > * + * {
            border-top-width: 1px;
            border-top-style: solid;
            border-top-color: var(--border);
        }

        .w-full {
            width: 100%;
        }

        .w-10 {
            width: 2.5rem;
        }

        .w-12 {
            width: 3rem;
        }

        .w-16 {
            width: 4rem;
        }

        .w-20 {
            width: 5rem;
        }

        .h-1\.5 {
            height: 0.375rem;
        }

        .h-2 {
            height: 0.5rem;
        }

        .h-4 {
            height: 1rem;
        }

        .h-5 {
            height: 1.25rem;
        }

        .h-6 {
            height: 1.5rem;
        }

        .h-8 {
            height: 2rem;
        }

        .h-10 {
            height: 2.5rem;
        }

        .h-12 {
            height: 3rem;
        }

        .h-16 {
            height: 4rem;
        }

        .h-20 {
            height: 5rem;
        }

        .h-24 {
            height: 6rem;
        }

        .h-px {
            height: 1px;
        }

        .flex-shrink-0 {
            flex-shrink: 0;
        }

        .flex-wrap {
            flex-wrap: wrap;
        }

        .overflow-hidden {
            overflow: hidden;
        }

        .truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .whitespace-nowrap {
            white-space: nowrap;
        }

        .italic {
            font-style: italic;
        }

        .absolute {
            position: absolute;
        }

        .relative {
            position: relative;
        }

        .left-1\/2 {
            left: 50%;
        }

        .-bottom-8 {
            bottom: -2rem;
        }

        .-translate-x-1\/2 {
            transform: translateX(-50%);
        }

        .z-10 {
            z-index: 10;
        }

        /* Card Styles */
        .card {
            background-color: var(--card);
            border-radius: var(--radius);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transform: translateY(-2px);
        }

        .card-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin: 0;
        }

        .card-content {
            padding: 1.5rem;
        }

        .card-content-no-padding {
            padding: 0;
        }

        /* Button Styles */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--radius);
            font-weight: 500;
            font-size: 0.875rem;
            line-height: 1.25rem;
            padding: 0.5rem 1rem;
            transition-property: color, background-color, border-color, text-decoration-color, fill, stroke;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 150ms;
            cursor: pointer;
        }

        .btn-primary {
            background-color: var(--primary);
            color: var(--primary-foreground);
            border: 1px solid transparent;
        }

        .btn-primary:hover {
            background-color: #2563eb;
        }

        .btn-outline {
            background-color: transparent;
            color: var(--foreground);
            border: 1px solid var(--border);
        }

        .btn-outline:hover {
            background-color: var(--muted);
        }

        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.75rem;
        }

        .btn-lg {
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
        }

        /* Badge Styles */
        .badge {
            display: inline-flex;
            align-items: center;
            border-radius: 9999px;
            font-weight: 500;
            font-size: 0.75rem;
            line-height: 1;
            padding: 0.25rem 0.75rem;
            white-space: nowrap;
        }

        .badge-outline {
            background-color: transparent;
            border: 1px solid var(--border);
            color: var(--foreground);
        }

        .badge-secondary {
            background-color: var(--secondary);
            color: var(--secondary-foreground);
        }

        /* Tooltip */
        .tooltip {
            position: relative;
            display: inline-block;
        }

        .tooltip .tooltip-text {
            visibility: hidden;
            width: 120px;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 5px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            margin-left: -60px;
            opacity: 0;
            transition: opacity 0.3s;
            font-size: 0.75rem;
        }

        .tooltip:hover .tooltip-text {
            visibility: visible;
            opacity: 1;
        }

        /* Quick Action Items */
        .quick-action {
            border-radius: var(--radius);
            padding: 1rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .quick-action:hover {
            transform: translateY(-0.25rem);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        /* Responsive Styles */
        @media (min-width: 768px) {
            .md\:flex-row {
                flex-direction: row;
            }

            .md\:items-center {
                align-items: center;
            }

            .md\:grid-cols-2 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .md\:w-20 {
                width: 5rem;
            }

            .md\:h-20 {
                height: 5rem;
            }

            .md\:mt-0 {
                margin-top: 0;
            }

            .md\:text-2xl {
                font-size: 1.5rem;
            }
        }

        @media (min-width: 1024px) {
            .lg\:col-span-2 {
                grid-column: span 2 / span 2;
            }

            .lg\:col-span-6 {
                grid-column: span 6 / span 6;
            }

            .lg\:col-span-8 {
                grid-column: span 8 / span 8;
            }

            .lg\:grid-cols-3 {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .lg\:grid-cols-8 {
                grid-template-columns: repeat(8, minmax(0, 1fr));
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div class="bg-primary/10 p-2 rounded-full">
                        <i class="fas fa-building text-primary"></i>
                    </div>
                    <h1 class="text-2xl font-bold">Chi tiết chi nhánh</h1>
                </div>
                <p class="text-muted-foreground ml-11">Quản lý thông tin chi nhánh {{ $branch->name }}</p>
            </div>
            <div class="flex gap-2 mt-4 md:mt-0">
                <a href="#" class="btn btn-primary">
                    <i class="fas fa-edit mr-2"></i>
                    Chỉnh sửa
                </a>
                <a href="branches.html" class="btn btn-outline">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Quay lại
                </a>
            </div>
        </div>

        <!-- Branch Status Card -->
        <div class="card mb-6 overflow-hidden transition-all duration-300">
            <div class="h-16 bg-gradient-to-r from-primary to-primary/70"></div>
            <div class="px-6 -mt-8 pb-6">
                <div class="grid grid-cols-1 lg:grid-cols-8 gap-6">
                    <div class="lg:col-span-6">
                        <div class="flex flex-col md:flex-row md:items-center gap-4">
                            <div class="flex-shrink-0 w-16 h-16 md:w-20 md:h-20 bg-primary text-white rounded-lg flex items-center justify-center shadow-lg transition-transform duration-300 hover:scale-105">
                                <i class="fas fa-building fa-2x"></i>
                            </div>
                            <div>
                                <h2 class="text-xl md:text-2xl font-bold mt-2 md:mt-0">{{ $branch->name }}</h2>
                                <div class="flex items-center text-muted-foreground mt-1">
                                    <i class="fas fa-map-marker-alt text-red-600 mr-2"></i>
                                    <span>{{ $branch->address }}</span>
                                </div>
                                <div class="flex flex-wrap gap-2 mt-3">
                                    <div class="badge px-3 py-1.5 bg-green-100 text-green-600">
                                        <div class="flex items-center">
                                            <div class="h-2 w-2 rounded-full bg-green-600 mr-2"></div>
                                            Đang hoạt động
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1 bg-amber-50 text-amber-800 px-3 py-1.5 rounded-full">
                                        <div class="flex">
                                            <i class="fas fa-star text-amber-600"></i>
                                            <i class="fas fa-star text-amber-600"></i>
                                            <i class="fas fa-star text-amber-600"></i>
                                            <i class="fas fa-star text-amber-600"></i>
                                            <i class="fas fa-star-half-alt text-amber-600"></i>
                                        </div>
                                        <span class="font-medium ml-1">{{ number_format($branch->rating, 1) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="lg:col-span-2">
                        <div class="bg-card rounded-lg shadow-sm p-4">
                            <div class="grid grid-cols-2 divide-x">
                                <div class="pr-4 text-center">
                                    <div class="mx-auto w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mb-2">
                                        <i class="fas fa-sun text-green-600"></i>
                                    </div>
                                    <div class="text-xs text-muted-foreground">Giờ mở cửa</div>
                                    <div class="font-bold text-green-600 mt-1">{{ $branch->opening_hour }}</div>
                                </div>
                                <div class="pl-4 text-center">
                                    <div class="mx-auto w-10 h-10 rounded-full bg-red-100 flex items-center justify-center mb-2">
                                        <i class="fas fa-moon text-red-600"></i>
                                    </div>
                                    <div class="text-xs text-muted-foreground">Giờ đóng cửa</div>
                                    <div class="font-bold text-red-600 mt-1">{{ $branch->closing_hour }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - 2/3 width on large screens -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="card overflow-hidden transition-all duration-300">
                    <div class="card-header pb-2">
                        <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                            <i class="fas fa-info-circle text-primary"></i>
                        </div>
                        <h3 class="card-title">Thông tin cơ bản</h3>
                    </div>
                    <div class="card-content-no-padding">
                        <div class="divide-y">
                            <div class="grid grid-cols-3 py-4 px-6">
                                <div class="font-medium flex items-center">
                                    <i class="fas fa-hashtag mr-2 text-primary/70"></i>
                                    #{{ $branch->id }}
                                </div>
                                <div class="col-span-2">
                                    <span class="badge badge-outline font-mono">#1</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-3 py-4 px-6">
                                <div class="font-medium flex items-center">
                                    <i class="fas fa-building mr-2 text-primary/70"></i>
                                    Tên chi nhánh
                                </div>
                                <div class="col-span-2 font-semibold">{{ $branch->name }}</div>
                            </div>

                            <div class="grid grid-cols-3 py-4 px-6">
                                <div class="font-medium flex items-center">
                                    <i class="fas fa-map-marker-alt mr-2 text-primary/70"></i>
                                    Địa chỉ
                                </div>
                                <div class="col-span-2">{{ $branch->address }}</div>
                            </div>

                            <div class="grid grid-cols-3 py-4 px-6">
                                <div class="font-medium flex items-center">
                                    <i class="fas fa-phone mr-2 text-primary/70"></i>
                                    Số điện thoại
                                </div>
                                <div class="col-span-2">
                                    <a href="tel:0901234567" class="text-primary hover:underline transition-colors">
                                    {{ $branch->phone }}
                                    </a>
                                </div>
                            </div>

                            <div class="grid grid-cols-3 py-4 px-6">
                                <div class="font-medium flex items-center">
                                    <i class="fas fa-envelope mr-2 text-primary/70"></i>
                                    Email
                                </div>
                                <div class="col-span-2">
                                    <a href="mailto:{{ $branch->email }}" class="text-primary hover:underline transition-colors">
                                    {{ $branch->email }}
                                    </a>
                                </div>
                            </div>

                            <div class="grid grid-cols-3 py-4 px-6">
                                <div class="font-medium flex items-center">
                                    <i class="fas fa-calendar mr-2 text-primary/70"></i>
                                    Ngày tạo
                                </div>
                                <div class="col-span-2">
                                    <div class="flex items-center gap-3">
                                        <span class="badge badge-secondary flex items-center">
                                            <i class="fas fa-calendar-alt mr-1"></i>
                                            {{ $branch->created_at->format('d/m/Y') }}
                                        </span>
                                        <span class="text-muted-foreground text-sm flex items-center">
                                            <i class="fas fa-clock mr-1"></i>
                                            {{ $branch->created_at->format('H:i') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Operating Hours -->
                <div class="card transition-all duration-300">
                    <div class="card-header pb-2">
                        <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center">
                            <i class="fas fa-clock text-amber-600"></i>
                        </div>
                        <h3 class="card-title">Giờ hoạt động</h3>
                    </div>
                    <div class="card-content">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-card border rounded-lg p-4 flex items-center gap-4 transition-all duration-300 hover:shadow-md hover:-translate-y-1">
                                <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center shadow-sm">
                                    <i class="fas fa-sun fa-lg text-green-600"></i>
                                </div>
                                <div>
                                    <div class="text-sm text-muted-foreground">Giờ mở cửa</div>
                                    <div class="text-2xl font-bold text-green-600">{{ $branch->opening_hour }}</div>
                                </div>
                            </div>

                            <div class="bg-card border rounded-lg p-4 flex items-center gap-4 transition-all duration-300 hover:shadow-md hover:-translate-y-1">
                                <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center shadow-sm">
                                    <i class="fas fa-moon fa-lg text-red-600"></i>
                                </div>
                                <div>
                                    <div class="text-sm text-muted-foreground">Giờ đóng cửa</div>
                                    <div class="text-2xl font-bold text-red-600">{{ $branch->closing_hour }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - 1/3 width on large screens -->
            <div class="space-y-6">
                <!-- Manager Information -->
                <div class="card transition-all duration-300 overflow-hidden">
                    <div class="card-header pb-2">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                            <i class="fas fa-user-tie text-blue-600"></i>
                        </div>
                        <h3 class="card-title">Quản lý chi nhánh</h3>
                    </div>
                    @if($branch->manager)
                    <div class="card-content-no-padding">
                        <div>
                            <div class="h-20 bg-blue-100"></div>
                            <div class="px-6 pb-6 -mt-10">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 rounded-full bg-blue-600 text-white flex items-center justify-center shadow-lg mb-3">
                                        <i class="fas fa-user-tie fa-2x"></i>
                                    </div>
                                    <h3 class="font-bold text-lg">{{ $branch->manager->full_name }}</h3>
                                    <div class="mt-1 mb-4 px-3 py-1 bg-blue-100 text-blue-800 rounded-full">
                                        <div class="flex items-center">
                                            <div class="h-1.5 w-1.5 rounded-full bg-blue-600 mr-1.5"></div>
                                            Quản lý chi nhánh
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2 w-full mb-3">
                                        <a
                                            href="mailto:{{ $branch->manager->email }}"
                                            class="flex items-center justify-center gap-2 text-sm py-2 border border-blue-200 rounded-md text-blue-700 hover:bg-blue-50 transition-colors"
                                        >
                                            <i class="fas fa-envelope"></i>
                                            Gửi email
                                        </a>
                                        <a
                                            href="tel:{{ $branch->manager->phone }}"
                                            class="flex items-center justify-center gap-2 text-sm py-2 border border-blue-200 rounded-md text-blue-700 hover:bg-blue-50 transition-colors"
                                        >
                                            <i class="fas fa-phone"></i>
                                            Liên hệ
                                        </a>
                                    </div>

                                    <a href="{{ route('admin.branches.assign-manager', $branch->id) }}" class="btn btn-outline w-full">
                                        <i class="fas fa-exchange-alt mr-2"></i>
                                        Thay đổi quản lý
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                        <div class="text-center p-4">
                            <div class="mb-3">
                                <div class="avatar-circle bg-light mx-auto d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; border-radius: 50%;">
                                    <i class="fas fa-user-slash fa-2x text-muted"></i>
                                </div>
                            </div>
                            <h6 class="fw-bold mb-1">Chưa phân công quản lý</h6>
                            <p class="text-muted small mb-3">Chi nhánh này chưa có người quản lý</p>
                            <a href="{{ route('admin.branches.assign-manager', $branch->id) }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Phân công quản lý
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Rating & Reviews -->
                <div class="card transition-all duration-300 overflow-hidden">
                    <div class="card-header pb-2">
                        <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center">
                            <i class="fas fa-star text-amber-600"></i>
                        </div>
                        <h3 class="card-title">Đánh giá khách hàng</h3>
                    </div>
                    <div class="card-content">
                        <div class="text-center">
                            <div class="relative mb-8">
                                <div class="h-24 bg-amber-50 rounded-lg"></div>
                                <div class="absolute left-1/2 -translate-x-1/2 -bottom-8">
                                    <div class="w-16 h-16 rounded-full bg-white shadow-md flex items-center justify-center">
                                        <div class="text-center">
                                            <div class="text-2xl font-bold text-amber-600">{{ number_format($branch->rating, 1) }}</div>
                                            <div class="text-xs text-muted-foreground">/ 5.0</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-center mt-10 mb-3">
                                <i class="fas fa-star text-amber-600 mx-1"></i>
                                <i class="fas fa-star text-amber-600 mx-1"></i>
                                <i class="fas fa-star text-amber-600 mx-1"></i>
                                <i class="fas fa-star text-amber-600 mx-1"></i>
                                <i class="fas fa-star-half-alt text-amber-600 mx-1"></i>
                            </div>
                            <p class="text-sm text-muted-foreground">Dựa trên đánh giá của khách hàng</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card transition-all duration-300">
                    <div class="card-header pb-2">
                        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                            <i class="fas fa-bolt text-green-600"></i>
                        </div>
                        <h3 class="card-title">Thao tác nhanh</h3>
                    </div>
                    <div class="card-content">
                        <div class="grid grid-cols-2 gap-3">
                            <div class="tooltip">
                                <div class="quick-action bg-primary/10 text-primary rounded-lg p-4 text-center cursor-pointer hover:bg-primary hover:text-white">
                                    <div class="flex justify-center mb-2">
                                        <i class="fas fa-chart-bar fa-lg"></i>
                                    </div>
                                    <h6 class="font-medium text-sm">Báo cáo</h6>
                                </div>
                                <span class="tooltip-text">Xem báo cáo chi nhánh</span>
                            </div>

                            <div class="tooltip">
                                <div class="quick-action bg-blue-100 text-blue-600 rounded-lg p-4 text-center cursor-pointer hover:bg-blue-600 hover:text-white">
                                    <div class="flex justify-center mb-2">
                                        <i class="fas fa-users fa-lg"></i>
                                    </div>
                                    <h6 class="font-medium text-sm">Nhân viên</h6>
                                </div>
                                <span class="tooltip-text">Quản lý nhân viên chi nhánh</span>
                            </div>

                            <div class="tooltip">
                                <div class="quick-action bg-green-100 text-green-600 rounded-lg p-4 text-center cursor-pointer hover:bg-green-600 hover:text-white">
                                    <div class="flex justify-center mb-2">
                                        <i class="fas fa-calendar-alt fa-lg"></i>
                                    </div>
                                    <h6 class="font-medium text-sm">Lịch làm việc</h6>
                                </div>
                                <span class="tooltip-text">Xem lịch làm việc</span>
                            </div>

                            <div class="tooltip">
                                <div class="quick-action bg-amber-100 text-amber-600 rounded-lg p-4 text-center cursor-pointer hover:bg-amber-600 hover:text-white">
                                    <div class="flex justify-center mb-2">
                                        <i class="fas fa-cog fa-lg"></i>
                                    </div>
                                    <h6 class="font-medium text-sm">Cài đặt</h6>
                                </div>
                                <span class="tooltip-text">Cài đặt chi nhánh</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add animation to cards on scroll
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.card');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, {
                threshold: 0.1
            });

            cards.forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                observer.observe(card);
            });

            // Add click effect for quick action items
            const quickActions = document.querySelectorAll('.quick-action');
            quickActions.forEach(action => {
                action.addEventListener('click', function() {
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 150);
                });
            });
        });
    </script>
</body>
</html>