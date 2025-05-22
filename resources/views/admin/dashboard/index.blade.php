@extends('layouts.admin.contentLayoutMaster')

@section('title', 'Dashboard')
@section('description', 'Tổng quan về hoạt động kinh doanh của bạn')

@section('content')
<div class="fade-in">
    <div>
        <h2 class="text-3xl font-bold tracking-tight">Dashboard</h2>
        <p class="text-muted-foreground">Tổng quan về hoạt động kinh doanh của bạn</p>
    </div>

    <div class="mt-6">
        <div class="flex border-b space-x-1">
            <button class="px-4 py-2 text-sm font-medium border-b-2 border-primary" id="tab-overview">Tổng quan</button>
            <button class="px-4 py-2 text-sm font-medium text-muted-foreground" id="tab-analytics">Phân tích</button>
        </div>
        
        <div id="content-overview" class="space-y-4 mt-4">
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <div class="card overflow-hidden border-l-4 border-l-green-500">
                    <div class="p-6">
                        <div class="flex flex-row items-center justify-between pb-2">
                            <h3 class="text-sm font-medium">Doanh thu</h3>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
                                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                        </div>
                        <div class="text-2xl font-bold">45,231,890đ</div>
                        <div class="flex items-center text-xs text-green-500">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                                <path d="m5 12 7-7 7 7"></path>
                                <path d="M12 19V5"></path>
                            </svg>
                            <span>+20.1% so với tháng trước</span>
                        </div>
                    </div>
                </div>
                
                <div class="card overflow-hidden border-l-4 border-l-blue-500">
                    <div class="p-6">
                        <div class="flex flex-row items-center justify-between pb-2">
                            <h3 class="text-sm font-medium">Đơn hàng</h3>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
                                <circle cx="8" cy="21" r="1"></circle>
                                <circle cx="19" cy="21" r="1"></circle>
                                <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
                            </svg>
                        </div>
                        <div class="text-2xl font-bold">+573</div>
                        <div class="flex items-center text-xs text-blue-500">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                                <path d="m5 12 7-7 7 7"></path>
                                <path d="M12 19V5"></path>
                            </svg>
                            <span>+12.2% so với tháng trước</span>
                        </div>
                    </div>
                </div>
                
                <div class="card overflow-hidden border-l-4 border-l-purple-500">
                    <div class="p-6">
                        <div class="flex flex-row items-center justify-between pb-2">
                            <h3 class="text-sm font-medium">Sản phẩm</h3>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
                                <path d="M2.97 12.92A2 2 0 0 0 2 14.63v3.24a2 2 0 0 0 .97 1.71l3 1.8a2 2 0 0 0 2.06 0L12 19v-5.5l-5-3-4.03 2.42Z"></path>
                                <path d="m7 16.5-4.74-2.85"></path>
                                <path d="m7 16.5 5-3"></path>
                                <path d="M7 16.5v5.17"></path>
                                <path d="M12 13.5V19l3.97 2.38a2 2 0 0 0 2.06 0l3-1.8a2 2 0 0 0 .97-1.71v-3.24a2 2 0 0 0-.97-1.71L17 10.5l-5 3Z"></path>
                                <path d="m17 16.5-5-3"></path>
                                <path d="m17 16.5 4.74-2.85"></path>
                                <path d="M17 16.5v5.17"></path>
                                <path d="M7.97 4.42A2 2 0 0 0 7 6.13v4.37l5 3 5-3V6.13a2 2 0 0 0-.97-1.71l-3-1.8a2 2 0 0 0-2.06 0l-3 1.8Z"></path>
                                <path d="M12 8 7.26 5.15"></path>
                                <path d="m12 8 4.74-2.85"></path>
                                <path d="M12 13.5V8"></path>
                            </svg>
                        </div>
                        <div class="text-2xl font-bold">1,324</div>
                        <div class="flex items-center text-xs text-purple-500">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                                <path d="m5 12 7-7 7 7"></path>
                                <path d="M12 19V5"></path>
                            </svg>
                            <span>+42 sản phẩm mới</span>
                        </div>
                    </div>
                </div>
                
                <div class="card overflow-hidden border-l-4 border-l-amber-500">
                    <div class="p-6">
                        <div class="flex flex-row items-center justify-between pb-2">
                            <h3 class="text-sm font-medium">Khách hàng</h3>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                        <div class="text-2xl font-bold">+2,350</div>
                        <div class="flex items-center text-xs text-amber-500">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                                <path d="m5 12 7-7 7 7"></path>
                                <path d="M12 19V5"></path>
                            </svg>
                            <span>+18.1% so với tháng trước</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-7">
                <div class="card col-span-4">
                    <div class="p-6">
                        <h3 class="text-lg font-medium">Doanh thu theo tháng</h3>
                        <p class="text-sm text-muted-foreground">Biểu đồ hiển thị doanh thu theo từng tháng trong năm</p>
                        <div class="mt-4 h-[240px] bg-muted/20 rounded-md flex items-center justify-center">
                            <p class="text-muted-foreground">Biểu đồ doanh thu theo tháng</p>
                        </div>
                    </div>
                </div>
                
                <div class="card col-span-3">
                    <div class="p-6">
                        <h3 class="text-lg font-medium">Đơn hàng gần đây</h3>
                        <p class="text-sm text-muted-foreground">Có 12 đơn hàng trong ngày hôm nay.</p>
                        <div class="mt-4 space-y-4">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-9 h-9 rounded-full bg-primary/10">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                                        <path d="m7.5 4.27 9 5.15"></path>
                                        <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"></path>
                                        <path d="m3.3 7 8.7 5 8.7-5"></path>
                                        <path d="M12 22V12"></path>
                                    </svg>
                                </div>
                                <div class="ml-4 space-y-1">
                                    <p class="text-sm font-medium leading-none">Nguyễn Văn A</p>
                                    <p class="text-sm text-muted-foreground">2 sản phẩm • 1,250,000đ</p>
                                </div>
                                <div class="ml-auto font-medium text-green-500 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                                        <path d="m5 12 7-7 7 7"></path>
                                        <path d="M12 19V5"></path>
                                    </svg>
                                    Hoàn thành
                                </div>
                            </div>
                            
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-9 h-9 rounded-full bg-primary/10">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                                        <rect width="20" height="14" x="2" y="5" rx="2"></rect>
                                        <line x1="2" x2="22" y1="10" y2="10"></line>
                                    </svg>
                                </div>
                                <div class="ml-4 space-y-1">
                                    <p class="text-sm font-medium leading-none">Trần Thị B</p>
                                    <p class="text-sm text-muted-foreground">5 sản phẩm • 3,750,000đ</p>
                                </div>
                                <div class="ml-auto font-medium text-amber-500 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                                        <path d="M5 12h14"></path>
                                        <path d="m12 5 7 7-7 7"></path>
                                    </svg>
                                    Đang giao
                                </div>
                            </div>
                            
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-9 h-9 rounded-full bg-primary/10">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                                        <path d="m7.5 4.27 9 5.15"></path>
                                        <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"></path>
                                        <path d="m3.3 7 8.7 5 8.7-5"></path>
                                        <path d="M12 22V12"></path>
                                    </svg>
                                </div>
                                <div class="ml-4 space-y-1">
                                    <p class="text-sm font-medium leading-none">Lê Văn C</p>
                                    <p class="text-sm text-muted-foreground">1 sản phẩm • 850,000đ</p>
                                </div>
                                <div class="ml-auto font-medium text-red-500 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                                        <path d="M12 19V5"></path>
                                        <path d="m5 12 7 7 7-7"></path>
                                    </svg>
                                    Đã hủy
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <button class="btn btn-ghost w-full flex items-center justify-center">
                                Xem tất cả đơn hàng
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-1">
                                    <path d="M5 12h14"></path>
                                    <path d="m12 5 7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="grid gap-4 md:grid-cols-2">
                <div class="card">
                    <div class="p-6">
                        <h3 class="text-lg font-medium">Sản phẩm theo danh mục</h3>
                        <p class="text-sm text-muted-foreground">Phân bổ sản phẩm theo từng danh mục</p>
                        <div class="mt-4 h-[240px] bg-muted/20 rounded-md flex items-center justify-center">
                            <p class="text-muted-foreground">Biểu đồ sản phẩm theo danh mục</p>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="p-6">
                        <h3 class="text-lg font-medium">Hoạt động gần đây</h3>
                        <p class="text-sm text-muted-foreground">Các hoạt động mới nhất trong hệ thống</p>
                        <div class="mt-4 space-y-4">
                            <div class="flex items-start">
                                <div class="mr-4 mt-0.5">
                                    <span class="badge badge-primary flex h-6 w-6 items-center justify-center rounded-full p-0 text-xs">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="9" cy="7" r="4"></circle>
                                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                        </svg>
                                    </span>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-sm font-medium leading-none">Khách hàng mới đăng ký</p>
                                    <p class="text-sm text-muted-foreground">Trần Văn D đã tạo tài khoản mới</p>
                                    <p class="text-xs text-muted-foreground">2 giờ trước</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="mr-4 mt-0.5">
                                    <span class="badge badge-primary flex h-6 w-6 items-center justify-center rounded-full bg-green-500 p-0 text-xs">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="8" cy="21" r="1"></circle>
                                            <circle cx="19" cy="21" r="1"></circle>
                                            <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
                                        </svg>
                                    </span>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-sm font-medium leading-none">Đơn hàng mới</p>
                                    <p class="text-sm text-muted-foreground">Đơn hàng #12345 đã được tạo</p>
                                    <p class="text-xs text-muted-foreground">3 giờ trước</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="mr-4 mt-0.5">
                                    <span class="badge badge-primary flex h-6 w-6 items-center justify-center rounded-full bg-amber-500 p-0 text-xs">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="m7.5 4.27 9 5.15"></path>
                                            <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"></path>
                                            <path d="m3.3 7 8.7 5 8.7-5"></path>
                                            <path d="M12 22V12"></path>
                                        </svg>
                                    </span>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-sm font-medium leading-none">Cập nhật trạng thái đơn hàng</p>
                                    <p class="text-sm text-muted-foreground">Đơn hàng #12342 đã được giao</p>
                                    <p class="text-xs text-muted-foreground">5 giờ trước</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="mr-4 mt-0.5">
                                    <span class="badge badge-primary flex h-6 w-6 items-center justify-center rounded-full bg-blue-500 p-0 text-xs">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M2.97 12.92A2 2 0 0 0 2 14.63v3.24a2 2 0 0 0 .97 1.71l3 1.8a2 2 0 0 0 2.06 0L12 19v-5.5l-5-3-4.03 2.42Z"></path>
                                            <path d="m7 16.5-4.74-2.85"></path>
                                            <path d="m7 16.5 5-3"></path>
                                            <path d="M7 16.5v5.17"></path>
                                            <path d="M12 13.5V19l3.97 2.38a2 2 0 0 0 2.06 0l3-1.8a2 2 0 0 0 .97-1.71v-3.24a2 2 0 0 0-.97-1.71L17 10.5l-5 3Z"></path>
                                            <path d="m17 16.5-5-3"></path>
                                            <path d="m17 16.5 4.74-2.85"></path>
                                            <path d="M17 16.5v5.17"></path>
                                            <path d="M7.97 4.42A2 2 0 0 0 7 6.13v4.37l5 3 5-3V6.13a2 2 0 0 0-.97-1.71l-3-1.8a2 2 0 0 0-2.06 0l-3 1.8Z"></path>
                                            <path d="M12 8 7.26 5.15"></path>
                                            <path d="m12 8 4.74-2.85"></path>
                                            <path d="M12 13.5V8"></path>
                                        </svg>
                                    </span>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-sm font-medium leading-none">Sản phẩm mới</p>
                                    <p class="text-sm text-muted-foreground">10 sản phẩm mới đã được thêm vào</p>
                                    <p class="text-xs text-muted-foreground">1 ngày trước</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <button class="btn btn-ghost w-full flex items-center justify-center">
                                Xem tất cả hoạt động
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-1">
                                    <path d="M5 12h14"></path>
                                    <path d="m12 5 7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="content-analytics" class="space-y-4 mt-4 hidden">
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <div class="card">
                    <div class="p-6">
                        <h3 class="text-lg font-medium">Thống kê chi tiết</h3>
                        <p class="text-sm text-muted-foreground">Xem thống kê chi tiết trong Analytics</p>
                        <div class="flex flex-col items-center justify-center py-6">
                            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
                                <path d="M3 3v18h18"></path>
                                <path d="M18 17V9"></path>
                                <path d="M13 17V5"></path>
                                <path d="M8 17v-3"></path>
                            </svg>
                            <p class="mt-4 text-center text-sm text-muted-foreground">
                                Để xem thống kê chi tiết hơn, vui lòng truy cập trang Analytics
                            </p>
                        </div>
                        <div class="mt-4">
                            <a href="#" class="btn btn-primary w-full">Đi đến Analytics</a>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="p-6">
                        <h3 class="text-lg font-medium">Thống kê bán hàng</h3>
                        <p class="text-sm text-muted-foreground">Xem thống kê bán hàng trong eCommerce</p>
                        <div class="flex flex-col items-center justify-center py-6">
                            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
                                <circle cx="8" cy="21" r="1"></circle>
                                <circle cx="19" cy="21" r="1"></circle>
                                <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
                            </svg>
                            <p class="mt-4 text-center text-sm text-muted-foreground">
                                Để xem thống kê bán hàng chi tiết, vui lòng truy cập trang eCommerce
                            </p>
                        </div>
                        <div class="mt-4">
                            <a href="#" class="btn btn-primary w-full">Đi đến eCommerce</a>
                        </div>
                    </div>
                </div>
                
                <div class="card lg:col-span-1">
                    <div class="p-6">
                        <h3 class="text-lg font-medium">Báo cáo tùy chỉnh</h3>
                        <p class="text-sm text-muted-foreground">Tạo báo cáo tùy chỉnh theo nhu cầu</p>
                        <div class="flex flex-col items-center justify-center py-6">
                            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
                                <path d="M3 3v18h18"></path>
                                <path d="M18 17V9"></path>
                                <path d="M13 17V5"></path>
                                <path d="M8 17v-3"></path>
                            </svg>
                            <p class="mt-4 text-center text-sm text-muted-foreground">
                                Tạo báo cáo tùy chỉnh với các chỉ số và thông số bạn quan tâm
                            </p>
                        </div>
                        <div class="mt-4">
                            <button class="btn btn-primary w-full">Tạo báo cáo mới</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabOverview = document.getElementById('tab-overview');
        const tabAnalytics = document.getElementById('tab-analytics');
        const contentOverview = document.getElementById('content-overview');
        const contentAnalytics = document.getElementById('content-analytics');
        
        tabOverview.addEventListener('click', function() {
            tabOverview.classList.add('border-b-2', 'border-primary');
            tabOverview.classList.remove('text-muted-foreground');
            tabAnalytics.classList.remove('border-b-2', 'border-primary');
            tabAnalytics.classList.add('text-muted-foreground');
            
            contentOverview.classList.remove('hidden');
            contentAnalytics.classList.add('hidden');
        });
        
        tabAnalytics.addEventListener('click', function() {
            tabAnalytics.classList.add('border-b-2', 'border-primary');
            tabAnalytics.classList.remove('text-muted-foreground');
            tabOverview.classList.remove('border-b-2', 'border-primary');
            tabOverview.classList.add('text-muted-foreground');
            
            contentAnalytics.classList.remove('hidden');
            contentOverview.classList.add('hidden');
        });
    });
</script>
@endsection
