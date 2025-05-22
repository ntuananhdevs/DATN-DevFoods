@extends('layouts.admin.contentLayoutMaster')

@section('title', 'Store Analytics')
@section('description', 'Phân tích chi tiết theo cửa hàng và chi nhánh')

@section('content')
<div class="fade-in">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-3xl font-bold tracking-tight">Thống kê cửa hàng</h2>
            <p class="text-muted-foreground">Phân tích chi tiết theo cửa hàng và chi nhánh</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <select class="border rounded-md px-3 py-2 bg-background text-sm" id="store-select">
                <option value="all">Tất cả cửa hàng</option>
                <option value="store1">Cửa hàng Hà Nội</option>
                <option value="store2">Cửa hàng TP.HCM</option>
                <option value="store3">Cửa hàng Đà Nẵng</option>
                <option value="store4">Cửa hàng Cần Thơ</option>
                <option value="store5">Cửa hàng Hải Phòng</option>
            </select>
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
            <button class="px-4 py-2 text-sm font-medium text-muted-foreground" id="tab-branches">Chi nhánh</button>
            <button class="px-4 py-2 text-sm font-medium text-muted-foreground" id="tab-products">Sản phẩm</button>
            <button class="px-4 py-2 text-sm font-medium text-muted-foreground" id="tab-comparison">So sánh</button>
        </div>
        
        <div id="content-overview" class="space-y-4 mt-4">
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <div class="card overflow-hidden">
                    <div class="p-6">
                        <div class="flex flex-row items-center justify-between pb-2">
                            <h3 class="text-sm font-medium">Tổng doanh thu</h3>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
                                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                        </div>
                        <div class="text-2xl font-bold">8,150,000,000đ</div>
                        <div class="flex items-center text-xs text-green-500">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                                <path d="m5 12 7-7 7 7"></path>
                                <path d="M12 19V5"></path>
                            </svg>
                            <span>10.2% so với tháng trước</span>
                        </div>
                    </div>
                    <div class="h-2 bg-gradient-to-r from-green-500 to-emerald-500"></div>
                </div>
                
                <div class="card overflow-hidden">
                    <div class="p-6">
                        <div class="flex flex-row items-center justify-between pb-2">
                            <h3 class="text-sm font-medium">Tổng đơn hàng</h3>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
                                <circle cx="8" cy="21" r="1"></circle>
                                <circle cx="19" cy="21" r="1"></circle>
                                <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
                            </svg>
                        </div>
                        <div class="text-2xl font-bold">8,150</div>
                        <div class="flex items-center text-xs text-green-500">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                                <path d="m5 12 7-7 7 7"></path>
                                <path d="M12 19V5"></path>
                            </svg>
                            <span>8.2% so với tháng trước</span>
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
                        <div class="text-2xl font-bold">1,000,000đ</div>
                        <div class="flex items-center text-xs text-green-500">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                                <path d="m5 12 7-7 7 7"></path>
                                <path d="M12 19V5"></path>
                            </svg>
                            <span>3.2% so với tháng trước</span>
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
                        <div class="text-2xl font-bold">3.8%</div>
                        <div class="flex items-center text-xs text-green-500">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                                <path d="m5 12 7-7 7 7"></path>
                                <path d="M12 19V5"></path>
                            </svg>
                            <span>0.5% so với tháng trước</span>
                        </div>
                    </div>
                    <div class="h-2 bg-gradient-to-r from-rose-500 to-pink-500"></div>
                </div>
            </div>
            
            <div class="grid gap-4 md:grid-cols-7">
                <div class="card col-span-7 md:col-span-4">
                    <div class="p-6">
                        <h3 class="text-lg font-medium">Doanh thu theo tháng</h3>
                        <p class="text-sm text-muted-foreground">Biểu đồ hiển thị doanh thu theo từng tháng cho mỗi cửa hàng</p>
                        <div class="mt-4 h-[300px] bg-muted/20 rounded-md flex items-center justify-center">
                            <p class="text-muted-foreground">Biểu đồ doanh thu theo tháng</p>
                        </div>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <div class="flex items-center gap-2">
                                <div class="h-3 w-3 rounded-full bg-emerald-500"></div>
                                <span class="text-sm text-muted-foreground">Hà Nội</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="h-3 w-3 rounded-full bg-blue-500"></div>
                                <span class="text-sm text-muted-foreground">TP.HCM</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="h-3 w-3 rounded-full bg-violet-500"></div>
                                <span class="text-sm text-muted-foreground">Đà Nẵng</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="h-3 w-3 rounded-full bg-amber-500"></div>
                                <span class="text-sm text-muted-foreground">Cần Thơ</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="h-3 w-3 rounded-full bg-pink-500"></div>
                                <span class="text-sm text-muted-foreground">Hải Phòng</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card col-span-7 md:col-span-3">
                    <div class="p-6">
                        <h3 class="text-lg font-medium">Phân bổ doanh thu</h3>
                        <p class="text-sm text-muted-foreground">Phân bổ doanh thu theo cửa hàng</p>
                        <div class="mt-4 h-[300px] bg-muted/20 rounded-md flex items-center justify-center">
                            <p class="text-muted-foreground">Biểu đồ phân bổ doanh thu</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="grid gap-4 md:grid-cols-2">
                <div class="card">
                    <div class="p-6">
                        <h3 class="text-lg font-medium">Doanh thu theo danh mục</h3>
                        <p class="text-sm text-muted-foreground">Phân bổ doanh thu theo danh mục sản phẩm</p>
                        <div class="mt-4 h-[300px] bg-muted/20 rounded-md flex items-center justify-center">
                            <p class="text-muted-foreground">Biểu đồ doanh thu theo danh mục</p>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="p-6">
                        <h3 class="text-lg font-medium">Top sản phẩm bán chạy</h3>
                        <p class="text-sm text-muted-foreground">Các sản phẩm có doanh số cao nhất</p>
                        <div class="mt-4 space-y-4">
                            <div class="flex items-center">
                                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-primary/10">
                                    <span class="text-sm font-medium">1</span>
                                </div>
                                <div class="ml-4 space-y-1 flex-1">
                                    <p class="text-sm font-medium leading-none">iPhone 14 Pro Max</p>
                                    <p class="text-xs text-muted-foreground">Điện thoại • 28,990,000đ</p>
                                </div>
                                <div class="ml-auto text-right">
                                    <p class="text-sm font-medium">124 đã bán</p>
                                    <p class="text-xs text-muted-foreground">3.59tr đồng</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center">
                                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-primary/10">
                                    <span class="text-sm font-medium">2</span>
                                </div>
                                <div class="ml-4 space-y-1 flex-1">
                                    <p class="text-sm font-medium leading-none">MacBook Air M2</p>
                                    <p class="text-xs text-muted-foreground">Laptop • 32,990,000đ</p>
                                </div>
                                <div class="ml-auto text-right">
                                    <p class="text-sm font-medium">98 đã bán</p>
                                    <p class="text-xs text-muted-foreground">3.23tr đồng</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center">
                                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-primary/10">
                                    <span class="text-sm font-medium">3</span>
                                </div>
                                <div class="ml-4 space-y-1 flex-1">
                                    <p class="text-sm font-medium leading-none">AirPods Pro</p>
                                    <p class="text-xs text-muted-foreground">Phụ kiện • 5,990,000đ</p>
                                </div>
                                <div class="ml-auto text-right">
                                    <p class="text-sm font-medium">215 đã bán</p>
                                    <p class="text-xs text-muted-foreground">1.28tr đồng</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center">
                                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-primary/10">
                                    <span class="text-sm font-medium">4</span>
                                </div>
                                <div class="ml-4 space-y-1 flex-1">
                                    <p class="text-sm font-medium leading-none">iPad Pro 12.9</p>
                                    <p class="text-xs text-muted-foreground">Máy tính bảng • 25,990,000đ</p>
                                </div>
                                <div class="ml-auto text-right">
                                    <p class="text-sm font-medium">76 đã bán</p>
                                    <p class="text-xs text-muted-foreground">1.97tr đồng</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center">
                                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-primary/10">
                                    <span class="text-sm font-medium">5</span>
                                </div>
                                <div class="ml-4 space-y-1 flex-1">
                                    <p class="text-sm font-medium leading-none">Apple Watch Series 8</p>
                                    <p class="text-xs text-muted-foreground">Đồng hồ • 10,990,000đ</p>
                                </div>
                                <div class="ml-auto text-right">
                                    <p class="text-sm font-medium">112 đã bán</p>
                                    <p class="text-xs text-muted-foreground">1.23tr đồng</p>
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
            </div>
        </div>
        
        <div id="content-branches" class="hidden mt-4">
            <div class="card">
                <div class="p-6">
                    <h3 class="text-lg font-medium">Hiệu suất chi nhánh</h3>
                    <p class="text-sm text-muted-foreground">So sánh hiệu suất giữa các chi nhánh</p>
                    <div class="mt-4 overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b">
                                    <th class="py-3 text-left font-medium">Chi nhánh</th>
                                    <th class="py-3 text-left font-medium">Cửa hàng</th>
                                    <th class="py-3 text-right font-medium">Doanh thu</th>
                                    <th class="py-3 text-right font-medium">Đơn hàng</th>
                                    <th class="py-3 text-right font-medium">Tăng trưởng</th>
                                    <th class="py-3 text-right font-medium">Hiệu suất</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b">
                                    <td class="py-3 font-medium">Chi nhánh Hoàn Kiếm</td>
                                    <td class="py-3">Hà Nội</td>
                                    <td class="py-3 text-right">1,250tr đ</td>
                                    <td class="py-3 text-right">1,250</td>
                                    <td class="py-3 text-right text-green-500">+12.5%</td>
                                    <td class="py-3 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <span>15.3%</span>
                                            <div class="w-16 h-2 bg-muted rounded-full overflow-hidden">
                                                <div class="h-full bg-primary" style="width: 15.3%"></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="border-b">
                                    <td class="py-3 font-medium">Chi nhánh Cầu Giấy</td>
                                    <td class="py-3">Hà Nội</td>
                                    <td class="py-3 text-right">980tr đ</td>
                                    <td class="py-3 text-right">980</td>
                                    <td class="py-3 text-right text-green-500">+8.2%</td>
                                    <td class="py-3 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <span>12.0%</span>
                                            <div class="w-16 h-2 bg-muted rounded-full overflow-hidden">
                                                <div class="h-full bg-primary" style="width: 12.0%"></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="border-b">
                                    <td class="py-3 font-medium">Chi nhánh Quận 1</td>
                                    <td class="py-3">TP.HCM</td>
                                    <td class="py-3 text-right">1,350tr đ</td>
                                    <td class="py-3 text-right">1,350</td>
                                    <td class="py-3 text-right text-green-500">+15.3%</td>
                                    <td class="py-3 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <span>16.6%</span>
                                            <div class="w-16 h-2 bg-muted rounded-full overflow-hidden">
                                                <div class="h-full bg-primary" style="width: 16.6%"></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="border-b">
                                    <td class="py-3 font-medium">Chi nhánh Hải Châu</td>
                                    <td class="py-3">Đà Nẵng</td>
                                    <td class="py-3 text-right">850tr đ</td>
                                    <td class="py-3 text-right">850</td>
                                    <td class="py-3 text-right text-green-500">+7.5%</td>
                                    <td class="py-3 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <span>10.4%</span>
                                            <div class="w-16 h-2 bg-muted rounded-full overflow-hidden">
                                                <div class="h-full bg-primary" style="width: 10.4%"></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="border-b">
                                    <td class="py-3 font-medium">Chi nhánh Ninh Kiều</td>
                                    <td class="py-3">Cần Thơ</td>
                                    <td class="py-3 text-right">750tr đ</td>
                                    <td class="py-3 text-right">750</td>
                                    <td class="py-3 text-right text-green-500">+6.2%</td>
                                    <td class="py-3 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <span>9.2%</span>
                                            <div class="w-16 h-2 bg-muted rounded-full overflow-hidden">
                                                <div class="h-full bg-primary" style="width: 9.2%"></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="grid gap-4 md:grid-cols-2 mt-4">
                <div class="card">
                    <div class="p-6">
                        <h3 class="text-lg font-medium">Bản đồ chi nhánh</h3>
                        <p class="text-sm text-muted-foreground">Vị trí và hiệu suất của các chi nhánh</p>
                        <div class="mt-4 flex h-[400px] items-center justify-center rounded-md border-2 border-dashed">
                            <div class="flex flex-col items-center text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
                                    <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                                <h3 class="mt-4 text-lg font-medium">Bản đồ chi nhánh</h3>
                                <p class="mt-2 text-sm text-muted-foreground">
                                    Bản đồ hiển thị vị trí và hiệu suất của các chi nhánh
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="p-6">
                        <h3 class="text-lg font-medium">Thông tin chi tiết chi nhánh</h3>
                        <p class="text-sm text-muted-foreground">Thông tin chi tiết về từng chi nhánh</p>
                        <div class="mt-4 max-h-[400px] overflow-auto space-y-6">
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
                                            <rect width="16" height="20" x="4" y="2" rx="2" ry="2"></rect>
                                            <path d="M9 22v-4h6v4"></path>
                                            <path d="M8 6h.01"></path>
                                            <path d="M16 6h.01"></path>
                                            <path d="M12 6h.01"></path>
                                            <path d="M8 10h.01"></path>
                                            <path d="M16 10h.01"></path>
                                            <path d="M12 10h.01"></path>
                                            <path d="M8 14h.01"></path>
                                            <path d="M16 14h.01"></path>
                                            <path d="M12 14h.01"></path>
                                        </svg>
                                        <h3 class="font-medium">Chi nhánh Hoàn Kiếm</h3>
                                    </div>
                                    <span class="inline-flex items-center rounded-full border border-green-500 px-2.5 py-0.5 text-xs font-medium text-green-500">
                                        Hà Nội
                                    </span>
                                </div>
                                <div class="grid grid-cols-3 gap-2">
                                    <div class="rounded-md bg-muted p-2">
                                        <p class="text-xs text-muted-foreground">Doanh thu</p>
                                        <p class="font-medium">1,250tr đ</p>
                                    </div>
                                    <div class="rounded-md bg-muted p-2">
                                        <p class="text-xs text-muted-foreground">Đơn hàng</p>
                                        <p class="font-medium">1,250</p>
                                    </div>
                                    <div class="rounded-md bg-muted p-2">
                                        <p class="text-xs text-muted-foreground">Tăng trưởng</p>
                                        <p class="font-medium text-green-500">+12.5%</p>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-muted-foreground">Hiệu suất</span>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm">15.3%</span>
                                        <div class="w-24 h-2 bg-muted rounded-full overflow-hidden">
                                            <div class="h-full bg-primary" style="width: 15.3%"></div>
                                        </div>
                                    </div>
                                </div>
                                <hr class="my-2 border-t border-muted" />
                            </div>
                            
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
                                            <rect width="16" height="20" x="4" y="2" rx="2" ry="2"></rect>
                                            <path d="M9 22v-4h6v4"></path>
                                            <path d="M8 6h.01"></path>
                                            <path d="M16 6h.01"></path>
                                            <path d="M12 6h.01"></path>
                                            <path d="M8 10h.01"></path>
                                            <path d="M16 10h.01"></path>
                                            <path d="M12 10h.01"></path>
                                            <path d="M8 14h.01"></path>
                                            <path d="M16 14h.01"></path>
                                            <path d="M12 14h.01"></path>
                                        </svg>
                                        <h3 class="font-medium">Chi nhánh Quận 1</h3>
                                    </div>
                                    <span class="inline-flex items-center rounded-full border border-blue-500 px-2.5 py-0.5 text-xs font-medium text-blue-500">
                                        TP.HCM
                                    </span>
                                </div>
                                <div class="grid grid-cols-3 gap-2">
                                    <div class="rounded-md bg-muted p-2">
                                        <p class="text-xs text-muted-foreground">Doanh thu</p>
                                        <p class="font-medium">1,350tr đ</p>
                                    </div>
                                    <div class="rounded-md bg-muted p-2">
                                        <p class="text-xs text-muted-foreground">Đơn hàng</p>
                                        <p class="font-medium">1,350</p>
                                    </div>
                                    <div class="rounded-md bg-muted p-2">
                                        <p class="text-xs text-muted-foreground">Tăng trưởng</p>
                                        <p class="font-medium text-green-500">+15.3%</p>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-muted-foreground">Hiệu suất</span>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm">16.6%</span>
                                        <div class="w-24 h-2 bg-muted rounded-full overflow-hidden">
                                            <div class="h-full bg-primary" style="width: 16.6%"></div>
                                        </div>
                                    </div>
                                </div>
                                <hr class="my-2 border-t border-muted" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="content-products" class="hidden mt-4">
            <div class="card">
                <div class="p-6">
                    <h3 class="text-lg font-medium">Top sản phẩm theo cửa hàng</h3>
                    <p class="text-sm text-muted-foreground">Các sản phẩm bán chạy nhất theo từng cửa hàng</p>
                    <div class="mt-4 overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b">
                                    <th class="py-3 text-left font-medium">Mã SP</th>
                                    <th class="py-3 text-left font-medium">Tên sản phẩm</th>
                                    <th class="py-3 text-left font-medium">Danh mục</th>
                                    <th class="py-3 text-right font-medium">Giá bán</th>
                                    <th class="py-3 text-right font-medium">Đã bán</th>
                                    <th class="py-3 text-right font-medium">Doanh thu</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b">
                                    <td class="py-3 font-medium">PROD-1</td>
                                    <td class="py-3">iPhone 14 Pro Max</td>
                                    <td class="py-3">Điện thoại</td>
                                    <td class="py-3 text-right">28,990,000đ</td>
                                    <td class="py-3 text-right">124</td>
                                    <td class="py-3 text-right">3,594,760,000đ</td>
                                </tr>
                                <tr class="border-b">
                                    <td class="py-3 font-medium">PROD-2</td>
                                    <td class="py-3">MacBook Air M2</td>
                                    <td class="py-3">Laptop</td>
                                    <td class="py-3 text-right">32,990,000đ</td>
                                    <td class="py-3 text-right">98</td>
                                    <td class="py-3 text-right">3,233,020,000đ</td>
                                </tr>
                                <tr class="border-b">
                                    <td class="py-3 font-medium">PROD-3</td>
                                    <td class="py-3">AirPods Pro</td>
                                    <td class="py-3">Phụ kiện</td>
                                    <td class="py-3 text-right">5,990,000đ</td>
                                    <td class="py-3 text-right">215</td>
                                    <td class="py-3 text-right">1,287,850,000đ</td>
                                </tr>
                                <tr class="border-b">
                                    <td class="py-3 font-medium">PROD-4</td>
                                    <td class="py-3">iPad Pro 12.9</td>
                                    <td class="py-3">Máy tính bảng</td>
                                    <td class="py-3 text-right">25,990,000đ</td>
                                    <td class="py-3 text-right">76</td>
                                    <td class="py-3 text-right">1,975,240,000đ</td>
                                </tr>
                                <tr class="border-b">
                                    <td class="py-3 font-medium">PROD-5</td>
                                    <td class="py-3">Apple Watch Series 8</td>
                                    <td class="py-3">Đồng hồ</td>
                                    <td class="py-3 text-right">10,990,000đ</td>
                                    <td class="py-3 text-right">112</td>
                                    <td class="py-3 text-right">1,230,880,000đ</td>
                                </tr>
                            </tbody>
                        </table>
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
            
            <div class="grid gap-4 md:grid-cols-2 mt-4">
                <div class="card">
                    <div class="p-6">
                        <h3 class="text-lg font-medium">Phân bổ danh mục theo cửa hàng</h3>
                        <p class="text-sm text-muted-foreground">Phân bổ doanh thu theo danh mục sản phẩm cho mỗi cửa hàng</p>
                        <div class="mt-4 h-[300px] bg-muted/20 rounded-md flex items-center justify-center">
                            <p class="text-muted-foreground">Biểu đồ phân bổ danh mục</p>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="p-6">
                        <h3 class="text-lg font-medium">Hiệu suất sản phẩm theo chi nhánh</h3>
                        <p class="text-sm text-muted-foreground">So sánh hiệu suất bán hàng giữa các chi nhánh</p>
                        <div class="mt-4 space-y-4">
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium">Chi nhánh Hoàn Kiếm</span>
                                    <span class="text-sm text-muted-foreground">1,250tr đ</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar" style="width: 15.3%"></div>
                                </div>
                                <div class="flex items-center justify-between text-xs text-muted-foreground">
                                    <span>Điện thoại: 45%</span>
                                    <span>Laptop: 25%</span>
                                    <span>Khác: 30%</span>
                                </div>
                            </div>
                            
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium">Chi nhánh Quận 1</span>
                                    <span class="text-sm text-muted-foreground">1,350tr đ</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar" style="width: 16.6%"></div>
                                </div>
                                <div class="flex items-center justify-between text-xs text-muted-foreground">
                                    <span>Điện thoại: 40%</span>
                                    <span>Laptop: 30%</span>
                                    <span>Khác: 30%</span>
                                </div>
                            </div>
                            
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium">Chi nhánh Hải Châu</span>
                                    <span class="text-sm text-muted-foreground">850tr đ</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar" style="width: 10.4%"></div>
                                </div>
                                <div class="flex items-center justify-between text-xs text-muted-foreground">
                                    <span>Điện thoại: 50%</span>
                                    <span>Laptop: 20%</span>
                                    <span>Khác: 30%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="content-comparison" class="hidden mt-4">
            <div class="card">
                <div class="p-6">
                    <h3 class="text-lg font-medium">So sánh hiệu suất cửa hàng</h3>
                    <p class="text-sm text-muted-foreground">So sánh hiệu suất giữa các cửa hàng theo thời gian</p>
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-500">
                                        <path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"></path>
                                        <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"></path>
                                        <path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"></path>
                                        <path d="M2 7h20"></path>
                                        <path d="M22 7v3a2 2 0 0 1-2 2v0a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 16 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 12 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 8 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 4 12v0a2 2 0 0 1-2-2V7"></path>
                                    </svg>
                                    <span class="font-medium">Cửa hàng Hà Nội</span>
                                </div>
                                <span class="inline-flex items-center rounded-full bg-emerald-50 text-emerald-700 border-emerald-200 px-2.5 py-0.5 text-xs font-medium border">
                                    +12.5%
                                </span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar" style="width: 85%"></div>
                            </div>
                            <div class="grid grid-cols-3 gap-2 text-sm">
                                <div>
                                    <p class="text-muted-foreground">Doanh thu</p>
                                    <p class="font-medium">3.33tr đ</p>
                                </div>
                                <div>
                                    <p class="text-muted-foreground">Đơn hàng</p>
                                    <p class="font-medium">3,330</p>
                                </div>
                                <div>
                                    <p class="text-muted-foreground">TB/đơn</p>
                                    <p class="font-medium">1tr đ</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-500">
                                        <path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"></path>
                                        <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"></path>
                                        <path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"></path>
                                        <path d="M2 7h20"></path>
                                        <path d="M22 7v3a2 2 0 0 1-2 2v0a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 16 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 12 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 8 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 4 12v0a2 2 0 0 1-2-2V7"></path>
                                    </svg>
                                    <span class="font-medium">Cửa hàng TP.HCM</span>
                                </div>
                                <span class="inline-flex items-center rounded-full bg-blue-50 text-blue-700 border-blue-200 px-2.5 py-0.5 text-xs font-medium border">
                                    +10.3%
                                </span>
                            </div>
                            <div class="progress bg-blue-100">
                                <div class="progress-bar bg-blue-500" style="width: 72%"></div>
                            </div>
                            <div class="grid grid-cols-3 gap-2 text-sm">
                                <div>
                                    <p class="text-muted-foreground">Doanh thu</p>
                                    <p class="font-medium">2.4tr đ</p>
                                </div>
                                <div>
                                    <p class="text-muted-foreground">Đơn hàng</p>
                                    <p class="font-medium">2,400</p>
                                </div>
                                <div>
                                    <p class="text-muted-foreground">TB/đơn</p>
                                    <p class="font-medium">1tr đ</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-violet-500">
                                        <path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"></path>
                                        <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"></path>
                                        <path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"></path>
                                        <path d="M2 7h20"></path>
                                        <path d="M22 7v3a2 2 0 0 1-2 2v0a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 16 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 12 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 8 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 4 12v0a2 2 0 0 1-2-2V7"></path>
                                    </svg>
                                    <span class="font-medium">Cửa hàng Đà Nẵng</span>
                                </div>
                                <span class="inline-flex items-center rounded-full bg-violet-50 text-violet-700 border-violet-200 px-2.5 py-0.5 text-xs font-medium border">
                                    +7.5%
                                </span>
                            </div>
                            <div class="progress bg-violet-100">
                                <div class="progress-bar bg-violet-500" style="width: 25%"></div>
                            </div>
                            <div class="grid grid-cols-3 gap-2 text-sm">
                                <div>
                                    <p class="text-muted-foreground">Doanh thu</p>
                                    <p class="font-medium">850tr đ</p>
                                </div>
                                <div>
                                    <p class="text-muted-foreground">Đơn hàng</p>
                                    <p class="font-medium">850</p>
                                </div>
                                <div>
                                    <p class="text-muted-foreground">TB/đơn</p>
                                    <p class="font-medium">1tr đ</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-amber-500">
                                        <path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"></path>
                                        <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"></path>
                                        <path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"></path>
                                        <path d="M2 7h20"></path>
                                        <path d="M22 7v3a2 2 0 0 1-2 2v0a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 16 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 12 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 8 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 4 12v0a2 2 0 0 1-2-2V7"></path>
                                    </svg>
                                    <span class="font-medium">Cửa hàng Cần Thơ</span>
                                </div>
                                <span class="inline-flex items-center rounded-full bg-amber-50 text-amber-700 border-amber-200 px-2.5 py-0.5 text-xs font-medium border">
                                    +6.2%
                                </span>
                            </div>
                            <div class="progress bg-amber-100">
                                <div class="progress-bar bg-amber-500" style="width: 22%"></div>
                            </div>
                            <div class="grid grid-cols-3 gap-2 text-sm">
                                <div>
                                    <p class="text-muted-foreground">Doanh thu</p>
                                    <p class="font-medium">750tr đ</p>
                                </div>
                                <div>
                                    <p class="text-muted-foreground">Đơn hàng</p>
                                    <p class="font-medium">750</p>
                                </div>
                                <div>
                                    <p class="text-muted-foreground">TB/đơn</p>
                                    <p class="font-medium">1tr đ</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="grid gap-4 md:grid-cols-2 mt-4">
                <div class="card">
                    <div class="p-6">
                        <h3 class="text-lg font-medium">Tỷ lệ chuyển đổi theo cửa hàng</h3>
                        <p class="text-sm text-muted-foreground">So sánh tỷ lệ chuyển đổi giữa các cửa hàng</p>
                        <div class="mt-4 h-[300px] bg-muted/20 rounded-md flex items-center justify-center">
                            <p class="text-muted-foreground">Biểu đồ tỷ lệ chuyển đổi</p>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="p-6">
                        <h3 class="text-lg font-medium">Giá trị đơn hàng trung bình</h3>
                        <p class="text-sm text-muted-foreground">So sánh giá trị đơn hàng trung bình giữa các cửa hàng</p>
                        <div class="mt-4 h-[300px] bg-muted/20 rounded-md flex items-center justify-center">
                            <p class="text-muted-foreground">Biểu đồ giá trị đơn hàng trung bình</p>
                        </div>
                    </div>
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
        
        // Store filter functionality
        const storeSelect = document.getElementById('store-select');
        if (storeSelect) {
            storeSelect.addEventListener('change', function() {
                // Here you would implement the filtering logic
                console.log('Selected store:', this.value);
            });
        }
    });
</script>
@endsection
