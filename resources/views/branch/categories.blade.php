@extends('layouts.branch.contentLayoutMaster')

@section('title', 'Quản lý Danh mục')
@section('description', 'Quản lý danh mục sản phẩm của chi nhánh')

@section('content')
    <div class="fade-in flex flex-col gap-4 pb-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div
                    class="flex aspect-square w-10 h-10 items-center justify-center rounded-lg bg-primary text-primary-foreground">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="18" height="18" x="3" y="3" rx="2" />
                        <path d="M9 9h6v6H9z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-3xl font-bold tracking-tight">Quản lý Danh mục</h2>
                    <p class="text-muted-foreground">Danh mục sản phẩm của chi nhánh</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="#" class="btn btn-primary flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="mr-2">
                        <path d="M5 12h14"></path>
                        <path d="M12 5v14"></path>
                    </svg>
                    Thêm mới
                </a>
            </div>
        </div>
        <div class="card border rounded-lg overflow-hidden">
            <div class="p-6 border-b">
                <h3 class="text-lg font-medium">Danh sách danh mục</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b">
                        <tr class="text-left">
                            <th class="p-4 font-medium text-muted-foreground">Tên danh mục</th>
                            <th class="p-4 font-medium text-muted-foreground">Mô tả</th>
                            <th class="p-4 font-medium text-muted-foreground">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr class="border-b hover:bg-muted/50 transition-colors">
                                <td class="p-4 font-semibold">{{ $category->name }}</td>
                                <td class="p-4">{{ $category->description }}</td>
                                <td class="p-4">
                                    <div class="flex items-center gap-2">
                                        <a href="#" class="btn btn-outline btn-sm" title="Sửa">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"></path>
                                                <path d="m15 5 4 4"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="p-8 text-center text-gray-500">Không có danh mục nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
