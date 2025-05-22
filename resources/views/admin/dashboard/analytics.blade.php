@extends('layouts.admin.contentLayoutMaster')

@section('title', 'Analytics Dashboard')
@section('description', 'Phân tích chi tiết về lưu lượng truy cập và hành vi người dùng')

@section('content')
<div class="fade-in">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-3xl font-bold tracking-tight">Analytics</h2>
            <p class="text-muted-foreground">Phân tích chi tiết về lưu lượng truy cập và hành vi người dùng</p>
        </div>
        <div class="flex items-center gap-2">
            <select class="border rounded-md px-3 py-2 bg-background text-sm">
                <option value="7d">7 ngày qua</option>
                <option value="30d" selected>30 ngày qua</option>
                <option value="90d">90 ngày qua</option>
                <option value="1y">1 năm qua</option>
            </select>
            <button class="btn btn-outline">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                Tùy chỉnh
            </button>
            <button class="btn btn-primary">Xuất báo cáo</button>
        </div>
    </div>

    <div class="mt-6">
        <div class="flex border-b space-x-1">
            <button class="px-4 py-2 text-sm font-medium border-b-2 border-primary" id="tab-overview">Tổng quan</button>
            <button class="px-4 py-2 text-sm font-medium text-muted-foreground" id="tab-audience">Khán giả</button>
            <button class="px-4 py-2 text-sm font-medium text-muted-foreground" id="tab-behavior">Hành vi</button>
            <button class="px-4 py-2 text-sm font-medium text-muted-foreground" id="tab-conversions">Chuyển đổi</button>
        </div>
        
        <div id="content-overview" class="space-y-4 mt-4">
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <div class="card overflow-hidden">
                    <div class="p-6">
                        <div class="flex flex-row items-center justify-between pb-2">
                            <h3 class="text-sm font-medium">Tổng lượt truy cập</h3>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                        <div class="text-2xl font-bold">128,452</div>
                        <div class="flex items-center text-xs text-green-500">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                                <path d="m5 12 7-7 7 7"></path>
                                <path d="M12 19V5"></path>
                            </svg>
                            <span>12.5% so với tháng trước</span>
                        </div>
                    </div>
                    <div class="h-2 bg-gradient-to-r from-green-500 to-emerald-500"></div>
                </div>
                
                <div class="card overflow-hidden">
                    <div class="p-6">
                        <div class="flex flex-row items-center justify-between pb-2">
                            <h3 class="text-sm font-medium">Tỷ lệ thoát</h3>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
                                <path d="M12 19V5"></path>
                                <path d="m5 12 7 7 7-7"></path>
                            </svg>
                        </div>
                        <div class="text-2xl font-bold">32.8%</div>
                        <div class="flex items-center text-xs text-green-500">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                                <path d="M12 19V5"></path>
                                <path d="m5 12 7 7 7-7"></path>
                            </svg>
                            <span>3.2% so với tháng trước</span>
                        </div>
                    </div>
                    <div class="h-2 bg-gradient-to-r from-blue-500 to-violet-500"></div>
                </div>
                
                <div class="card overflow-hidden">
                    <div class="p-6">
                        <div class="flex flex-row items-center justify-between pb-2">
                            <h3 class="text-sm font-medium">Thời gian trung bình</h3>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </div>
                        <div class="text-2xl font-bold">3m 42s</div>
                        <div class="flex items-center text-xs text-green-500">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                                <path d="m5 12 7-7 7 7"></path>
                                <path d="M12 19V5"></path>
                            </svg>
                            <span>8.4% so với tháng trước</span>
                        </div>
                    </div>
                    <div class="h-2 bg-gradient-to-r from-amber-500 to-orange-500"></div>
                </div>
                
                <div class="card overflow-hidden">
                    <div class="p-6">
                        <div class="flex flex-row items-center justify-between pb-2">
                            <h3 class="text-sm font-medium">Tỷ lệ chuyển đổi</h3>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
                                <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                            </svg>
                        </div>
                        <div class="text-2xl font-bold">3.24%</div>
                        <div class="flex items-center text-xs text-red-500">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                                <path d="M12 19V5"></path>
                                <path d="m5 12 7 7 7-7"></path>
                            </svg>
                            <span>1.1% so với tháng trước</span>
                        </div>
                    </div>
                    <div class="h-2 bg-gradient-to-r from-rose-500 to-pink-500"></div>
                </div>
            </div>
            
            <div class="grid gap-4 md:grid-cols-7">
                <div class="card col-span-7 md:col-span-4">
                    <div class="p-6">
                        <h3 class="text-lg font-medium">Lượt truy cập theo thời gian</h3>
                        <p class="text-sm text-muted-foreground">Biểu đồ hiển thị lượt truy cập và lượt xem trang theo thời gian</p>
                        <div class="mt-4 h-[300px] bg-muted/20 rounded-md flex items-center justify-center">
                            <p class="text-muted-foreground">Biểu đồ lượt truy cập theo thời gian</p>
                        </div>
                        <div class="mt-4 flex justify-between">
                            <div class="flex items-center gap-2">
                                <div class="h-3 w-3 rounded-full bg-emerald-500"></div>
                                <span class="text-sm text-muted-foreground">Lượt truy cập</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="h-3 w-3 rounded-full bg-blue-500"></div>
                                <span class="text-sm text-muted-foreground">Lượt xem trang</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card col-span-7 md:col-span-3">
                    <div class="p-6">
                        <h3 class="text-lg font-medium">Nguồn truy cập</h3>
                        <p class="text-sm text-muted-foreground">Phân bổ lưu lượng truy cập theo nguồn</p>
                        <div class="mt-4 h-[300px] bg-muted/20 rounded-md flex items-center justify-center">
                            <p class="text-muted-foreground">Biểu đồ nguồn truy cập</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="grid gap-4 md:grid-cols-2">
                <div class="card">
                    <div class="p-6">
                        <h3 class="text-lg font-medium">Thiết bị truy cập</h3>
                        <p class="text-sm text-muted-foreground">Phân bổ lưu lượng truy cập theo thiết bị</p>
                        <div class="mt-4 h-[300px] bg-muted/20 rounded-md flex items-center justify-center">
                            <p class="text-muted-foreground">Biểu đồ thiết bị truy cập</p>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="p-6">
                        <h3 class="text-lg font-medium">Tỷ lệ thoát theo tháng</h3>
                        <p class="text-sm text-muted-foreground">Tỷ lệ thoát giảm dần theo thời gian</p>
                        <div class="mt-4 h-[300px] bg-muted/20 rounded-md flex items-center justify-center">
                            <p class="text-muted-foreground">Biểu đồ tỷ lệ thoát theo tháng</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="p-6">
                    <h3 class="text-lg font-medium">Phân bố địa lý</h3>
                    <p class="text-sm text-muted-foreground">Lưu lượng truy cập theo quốc gia và khu vực</p>
                    <div class="mt-4 h-[400px] flex items-center justify-center rounded-md border-2 border-dashed">
                        <div class="flex flex-col items-center text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                                <path d="M2 12h20"></path>
                            </svg>
                            <h3 class="mt-4 text-lg font-medium">Bản đồ phân bố địa lý</h3>
                            <p class="mt-2 text-sm text-muted-foreground">
                                Bản đồ thế giới hiển thị phân bố người dùng theo khu vực
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="content-audience" class="hidden mt-4">
            <div class="h-[400px] flex items-center justify-center bg-muted/20 rounded-md">
                <div class="text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-auto text-muted">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium">Phân tích khán giả</h3>
                    <p class="mt-2 text-sm text-muted-foreground">Thông tin chi tiết về người dùng sẽ hiển thị ở đây</p>
                </div>
            </div>
        </div>
        
        <div id="content-behavior" class="hidden mt-4">
            <div class="h-[400px] flex items-center justify-center bg-muted/20 rounded-md">
                <div class="text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-auto text-muted">
                        <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium">Phân tích hành vi</h3>
                    <p class="mt-2 text-sm text-muted-foreground">Thông tin chi tiết về hành vi người dùng sẽ hiển thị ở đây</p>
                </div>
            </div>
        </div>
        
        <div id="content-conversions" class="hidden mt-4">
            <div class="h-[400px] flex items-center justify-center bg-muted/20 rounded-md">
                <div class="text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-auto text-muted">
                        <path d="M3 3v18h18"></path>
                        <path d="M18 17V9"></path>
                        <path d="M13 17V5"></path>
                        <path d="M8 17v-3"></path>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium">Phân tích chuyển đổi</h3>
                    <p class="mt-2 text-sm text-muted-foreground">Thông tin chi tiết về tỷ lệ chuyển đổi sẽ hiển thị ở đây</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('[id^="tab-"]');
        const contents = document.querySelectorAll('[id^="content-"]');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                // Remove active class from all tabs
                tabs.forEach(t => {
                    t.classList.remove('border-b-2', 'border-primary');
                    t.classList.add('text-muted-foreground');
                });
                
                // Add active class to clicked tab
                this.classList.add('border-b-2', 'border-primary');
                this.classList.remove('text-muted-foreground');
                
                // Hide all content
                contents.forEach(content => {
                    content.classList.add('hidden');
                });
                
                // Show corresponding content
                const contentId = this.id.replace('tab-', 'content-');
                document.getElementById(contentId).classList.remove('hidden');
            });
        });
    });
</script>
@endsection
