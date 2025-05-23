@extends('layouts.admin.contentLayoutMaster')

@section('title', 'eCommerce Dashboard')
@section('description', 'Tổng quan về hoạt động bán hàng và doanh thu')

@section('content')
<div class="fade-in">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-3xl font-bold tracking-tight">eCommerce</h2>
            <p class="text-muted-foreground">Tổng quan về hoạt động bán hàng và doanh thu</p>
        </div>
        <div class="flex items-center gap-2">
            <select class="border rounded-md px-3 py-2 bg-background text-sm">
                <option value="7d">7 ngày qua</option>
                <option value="30d" selected>30 ngày qua</option>
                <option value="90d">90 ngày qua</option>
                <option value="1y">1 năm qua</option>
            </select>
            <button class="btn btn-primary">Xuất báo cáo</button>
        </div>
    </div>

    <div class="mt-6">
        <div class="flex border-b space-x-1">
            <button class="px-4 py-2 text-sm font-medium border-b-2 border-primary" id="tab-overview">Tổng quan</button>
            <button class="px-4 py-2 text-sm font-medium text-muted-foreground" id="tab-products">Sản phẩm</button>
            <button class="px-4 py-2 text-sm font-medium text-muted-foreground" id="tab-orders">Đơn hàng</button>
            <button class="px-4 py-2 text-sm font-medium text-muted-foreground" id="tab-customers">Khách hàng</button>
        </div>
        
        <div id="content-overview" class="space-y-4 mt-4">
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <div class="card overflow-hidden">
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
                            <span>20.1% so với tháng trước</span>
                        </div>
                    </div>
                    <div class="h-2 bg-gradient-to-r from-green-500 to-emerald-500"></div>
                </div>
                
                <div class="card overflow-hidden">
                    <div class="p-6">
                        <div class="flex flex-row items-center justify-between pb-2">
                            <h3 class="text-sm font-medium">Đơn hàng</h3>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
                                <circle cx="8" cy="21" r="1"></circle>
                                <circle cx="19" cy="21" r="1"></circle>
                                <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
                            </svg>
                        </div>
                        <div class="text-2xl font-bold">573</div>
                        <div class="flex items-center text-xs text-green-500">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                                <path d="m5 12 7-7 7 7"></path>
                                <path d="M12 19V5"></path>
                            </svg>
                            <span>12.2% so với tháng trước</span>
                        </div>
                    </div>
                    <div class="h-2 bg-gradient-to-r from-blue-500 to-violet-500"></div>
                </div>
                
                <div class="card overflow-hidden">
                    <div class="p-6">
                        <div class="flex flex-row items-center justify-between pb-2">
                            <h3 class="text-sm font-medium">Giá trị đơn hàng TB</h3>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
                                <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path>
                                <path d="M3 6h18"></path>
                                <path d="M16 10a4 4 0 0 1-8 0"></path>
                            </svg>
                        </div>
                        <div class="text-2xl font-bold">1,250,000đ</div>
                        <div class="flex items-center text-xs text-green-500">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                                <path d="m5 12 7-7 7 7"></path>
                                <path d="M12 19V5"></path>
                            </svg>
                            <span>5.3% so với tháng trước</span>
                        </div>
                    </div>
                    <div class="h-2 bg-gradient-to-r from-amber-500 to-orange-500"></div>
                </div>
                
                <div class="card overflow-hidden">
                    <div class="p-6">
                        <div class="flex flex-row items-center justify-between pb-2">
                            <h3 class="text-sm font-medium">Tỷ lệ chuyển đổi</h3>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
                                <path d="m17 14 3 3-3 3"></path>
                                <path d="M22 17h-8"></path>
                                <path d="m7 10-3-3 3-3"></path>
                                <path d="M2 7h8"></path>
                                <path d="M22 7H12"></path>
                                <path d="M12 17H2"></path>
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
                        <h3 class="text-lg font-medium">Doanh thu theo tháng</h3>
                        <p class="text-sm text-muted-foreground">Biểu đồ hiển thị doanh thu theo từng tháng trong năm</p>
                        <div class="mt-4 h-[300px] bg-muted/20 rounded-md flex items-center justify-center">
                            <p class="text-muted-foreground">Biểu đồ doanh thu theo tháng</p>
                        </div>
                    </div>
                </div>
                
                <div class="card col-span-7 md:col-span-3">
                    <div class="p-6">
                        <h3 class="text-lg font-medium">Doanh thu theo danh mục</h3>
                        <p class="text-sm text-muted-foreground">Phân bổ doanh thu theo danh mục sản phẩm</p>
                        <div class="mt-4 h-[300px] bg-muted/20 rounded-md flex items-center justify-center">
                            <p class="text-muted-foreground">Biểu đồ doanh thu theo danh mục</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="grid gap-4 md:grid-cols-2">
                <div class="card">
                    <div class="p-6">
                        <h3 class="text-lg font-medium">Top sản phẩm bán chạy</h3>
                        <p class="text-sm text-muted-foreground">Các sản phẩm có doanh số cao nhất trong tháng</p>
                        <div class="mt-4 space-y-4">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-md bg-muted flex items-center justify-center">
                                    <span class="text-xs">IP</span>
                                </div>
                                <div class="ml-4 space-y-1 flex-1">
                                    <p class="text-sm font-medium leading-none">iPhone 14 Pro Max</p>
                                    <p class="text-sm text-muted-foreground">28,990,000đ</p>
                                </div>
                                <div class="ml-auto text-right">
                                    <p class="text-sm font-medium">124 đã bán</p>
                                    <div class="flex items-center text-xs text-green-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                                            <path d="m5 12 7-7 7 7"></path>
                                            <path d="M12 19V5"></path>
                                        </svg>
                                        <span>12.5%</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-md bg-muted flex items-center justify-center">
                                    <span class="text-xs">MB</span>
                                </div>
                                <div class="ml-4 space-y-1 flex-1">
                                    <p class="text-sm font-medium leading-none">MacBook Air M2</p>
                                    <p class="text-sm text-muted-foreground">32,990,000đ</p>
                                </div>
                                <div class="ml-auto text-right">
                                    <p class="text-sm font-medium">98 đã bán</p>
                                    <div class="flex items-center text-xs text-green-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                                            <path d="m5 12 7-7 7 7"></path>
                                            <path d="M12 19V5"></path>
                                        </svg>
                                        <span>8.3%</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-md bg-muted flex items-center justify-center">
                                    <span class="text-xs">AP</span>
                                </div>
                                <div class="ml-4 space-y-1 flex-1">
                                    <p class="text-sm font-medium leading-none">AirPods Pro</p>
                                    <p class="text-sm text-muted-foreground">5,990,000đ</p>
                                </div>
                                <div class="ml-auto text-right">
                                    <p class="text-sm font-medium">215 đã bán</p>
                                    <div class="flex items-center text-xs text-green-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                                            <path d="m5 12 7-7 7 7"></path>
                                            <path d="M12 19V5"></path>
                                        </svg>
                                        <span>15.7%</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-md bg-muted flex items-center justify-center">
                                    <span class="text-xs">IP</span>
                                </div>
                                <div class="ml-4 space-y-1 flex-1">
                                    <p class="text-sm font-medium leading-none">iPad Pro 12.9</p>
                                    <p class="text-sm text-muted-foreground">25,990,000đ</p>
                                </div>
                                <div class="ml-auto text-right">
                                    <p class="text-sm font-medium">76 đã bán</p>
                                    <div class="flex items-center text-xs text-red-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                                            <path d="M12 19V5"></path>
                                            <path d="m5 12 7 7 7-7"></path>
                                        </svg>
                                        <span>2.3%</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-md bg-muted flex items-center justify-center">
                                    <span class="text-xs">AW</span>
                                </div>
                                <div class="ml-4 space-y-1 flex-1">
                                    <p class="text-sm font-medium leading-none">Apple Watch Series 8</p>
                                    <p class="text-sm text-muted-foreground">10,990,000đ</p>
                                </div>
                                <div class="ml-auto text-right">
                                    <p class="text-sm font-medium">112 đã bán</p>
                                    <div class="flex items-center text-xs text-green-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                                            <path d="m5 12 7-7 7 7"></path>
                                            <path d="M12 19V5"></path>
                                        </svg>
                                        <span>5.8%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <button class="btn btn-ghost w-full flex items-center justify-center">
                                Xem tất cả sản phẩm
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-1">
                                    <path d="M5 12h14"></path>
                                    <path d="m12 5 7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="p-6">
                        <h3 class="text-lg font-medium">Đơn hàng gần đây</h3>
                        <p class="text-sm text-muted-foreground">Các đơn hàng mới nhất trong hệ thống</p>
                        <div class="mt-4 space-y-4">
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-muted flex items-center justify-center">
                                    <span class="text-xs">NV</span>
                                </div>
                                <div class="ml-4 space-y-1 flex-1">
                                    <p class="text-sm font-medium leading-none">Nguyễn Văn A</p>
                                    <p class="text-xs text-muted-foreground">15/05/2025</p>
                                </div>
                                <div class="ml-auto text-right">
                                    <p class="text-sm font-medium">2,850,000đ</p>
                                    <span class="inline-flex items-center rounded-full border border-green-500 px-2.5 py-0.5 text-xs font-medium text-green-500">
                                        Hoàn thành
                                    </span>
                                </div>
                            </div>
                            
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-muted flex items-center justify-center">
                                    <span class="text-xs">TB</span>
                                </div>
                                <div class="ml-4 space-y-1 flex-1">
                                    <p class="text-sm font-medium leading-none">Trần Thị B</p>
                                    <p class="text-xs text-muted-foreground">15/05/2025</p>
                                </div>
                                <div class="ml-auto text-right">
                                    <p class="text-sm font-medium">4,250,000đ</p>
                                    <span class="inline-flex items-center rounded-full border border-amber-500 px-2.5 py-0.5 text-xs font-medium text-amber-500">
                                        Đang giao
                                    </span>
                                </div>
                            </div>
                            
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-muted flex items-center justify-center">
                                    <span class="text-xs">LC</span>
                                </div>
                                <div class="ml-4 space-y-1 flex-1">
                                    <p class="text-sm font-medium leading-none">Lê Văn C</p>
                                    <p class="text-xs text-muted-foreground">14/05/2025</p>
                                </div>
                                <div class="ml-auto text-right">
                                    <p class="text-sm font-medium">1,750,000đ</p>
                                    <span class="inline-flex items-center rounded-full border border-blue-500 px-2.5 py-0.5 text-xs font-medium text-blue-500">
                                        Đang xử lý
                                    </span>
                                </div>
                            </div>
                            
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-muted flex items-center justify-center">
                                    <span class="text-xs">PT</span>
                                </div>
                                <div class="ml-4 space-y-1 flex-1">
                                    <p class="text-sm font-medium leading-none">Phạm Thị D</p>
                                    <p class="text-xs text-muted-foreground">14/05/2025</p>
                                </div>
                                <div class="ml-auto text-right">
                                    <p class="text-sm font-medium">3,650,000đ</p>
                                    <span class="inline-flex items-center rounded-full border border-green-500 px-2.5 py-0.5 text-xs font-medium text-green-500">
                                        Hoàn thành
                                    </span>
                                </div>
                            </div>
                            
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-muted flex items-center justify-center">
                                    <span class="text-xs">HV</span>
                                </div>
                                <div class="ml-4 space-y-1 flex-1">
                                    <p class="text-sm font-medium leading-none">Hoàng Văn E</p>
                                    <p class="text-xs text-muted-foreground">13/05/2025</p>
                                </div>
                                <div class="ml-auto text-right">
                                    <p class="text-sm font-medium">950,000đ</p>
                                    <span class="inline-flex items-center rounded-full border border-red-500 px-2.5 py-0.5 text-xs font-medium text-red-500">
                                        Đã hủy
                                    </span>
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
            
            <div class="grid gap-4 md:grid-cols-3">
                <div class="card">
                    <div class="p-6">
                        <h3 class="text-sm font-medium">Mục tiêu doanh thu</h3>
                        <div class="mt-2">
                            <div class="text-2xl font-bold">75.5%</div>
                            <div class="mt-2 progress">
                                <div class="progress-bar" style="width: 75.5%"></div>
                            </div>
                            <p class="mt-2 text-xs text-muted-foreground">45.2/60 triệu đồng mục tiêu tháng</p>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="p-6">
                        <h3 class="text-sm font-medium">Đơn hàng đang xử lý</h3>
                        <div class="mt-2 flex items-center justify-between">
                            <div>
                                <div class="text-2xl font-bold">42</div>
                                <p class="text-xs text-muted-foreground">Cần xử lý trong ngày hôm nay</p>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
                                <circle cx="12" cy="12" r="2"></circle>
                                <path d="M12 8v-2"></path>
                                <path d="M12 16v2"></path>
                                <path d="m16 12 2 1"></path>
                                <path d="M8 12 6 11"></path>
                                <path d="m16 8-1.5 1"></path>
                                <path d="M9.5 15 8 16"></path>
                                <path d="m16 16-1.5-1"></path>
                                <path d="M9.5 9 8 8"></path>
                                <rect x="2" y="2" width="20" height="20" rx="5"></rect>
                            </svg>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="p-6">
                        <h3 class="text-sm font-medium">Khuyến mãi đang chạy</h3>
                        <div class="mt-2 flex items-center justify-between">
                            <div>
                                <div class="text-2xl font-bold">3</div>
                                <p class="text-xs text-muted-foreground">Chương trình khuyến mãi đang hoạt động</p>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
                                <path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"></path>
                                <path d="m9 12 2 2 4-4"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="content-products" class="hidden mt-4">
            <div class="h-[400px] flex items-center justify-center bg-muted/20 rounded-md">
                <div class="text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-auto text-muted">
                        <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path>
                        <path d="M3 6h18"></path>
                        <path d="M16 10a4 4 0 0 1-8 0"></path>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium">Phân tích sản phẩm</h3>
                    <p class="mt-2 text-sm text-muted-foreground">Thông tin chi tiết về sản phẩm sẽ hiển thị ở đây</p>
                </div>
            </div>
        </div>
        
        <div id="content-orders" class="hidden mt-4">
            <div class="h-[400px] flex items-center justify-center bg-muted/20 rounded-md">
                <div class="text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-auto text-muted">
                        <path d="m7.5 4.27 9 5.15"></path>
                        <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"></path>
                        <path d="m3.3 7 8.7 5 8.7-5"></path>
                        <path d="M12 22V12"></path>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium">Phân tích đơn hàng</h3>
                    <p class="mt-2 text-sm text-muted-foreground">Thông tin chi tiết về đơn hàng sẽ hiển thị ở đây</p>
                </div>
            </div>
        </div>
        
        <div id="content-customers" class="hidden mt-4">
            <div class="h-[400px] flex items-center justify-center bg-muted/20 rounded-md">
                <div class="text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-auto text-muted">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium">Phân tích khách hàng</h3>
                    <p class="mt-2 text-sm text-muted-foreground">Thông tin chi tiết về khách hàng sẽ hiển thị ở đây</p>
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
