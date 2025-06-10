@extends('layouts.driver.masterLayout')

@section('title', 'Cá nhân')

@section('content')
    <div class="p-4 md:p-6 space-y-6">
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm profile-header-card">
            <div class="flex flex-row items-center justify-between p-4">
                <div class="flex items-center gap-4">
                    <img src="{{ $driver->avatarUrl ?? '/placeholder.svg?width=128&height=128' }}" alt="{{ $driver->name }}" width="80" height="80" class="rounded-full border-2 border-primary" />
                    <div>
                        <h3 class="text-2xl font-semibold tracking-tight">{{ $driver->name }}</h3>
                        <p class="text-sm text-muted-foreground">{{ $driver->phone }}</p>
                    </div>
                </div>
                <button id="toggle-edit-profile" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 h-9 px-4 py-2">
                    {{-- Button text and classes will be updated by JS --}}
                </button>
            </div>
            <div class="p-4 pt-0 space-y-4">
                <div class="flex items-center justify-between p-3 bg-muted/50 rounded-md">
                    <label for="driver-status" class="text-base font-medium">Trạng thái hoạt động</label>
                    <button type="button" role="switch" aria-checked="{{ $driver->isActive ? 'true' : 'false' }}" data-state="{{ $driver->isActive ? 'checked' : 'unchecked' }}" class="peer inline-flex h-6 w-11 shrink-0 cursor-pointer items-center rounded-full border-2 border-transparent transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background disabled:cursor-not-allowed disabled:opacity-50 {{ $driver->isActive ? 'bg-primary' : 'bg-input' }}" id="driver-status">
                        <span data-state="{{ $driver->isActive ? 'checked' : 'unchecked' }}" class="pointer-events-none block h-5 w-5 rounded-full bg-background shadow-lg ring-0 transition-transform {{ $driver->isActive ? 'translate-x-5' : 'translate-x-0' }}"></span>
                    </button>
                </div>
                <p class="text-xs text-muted-foreground px-3"></p> {{-- Text updated by JS --}}
            </div>
        </div>

        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-4">
                <h3 class="text-2xl font-semibold leading-none tracking-tight flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 h-5 w-5 text-primary"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> Thông tin cá nhân
                </h3>
            </div>
            <div id="profile-form" class="p-4 pt-0 space-y-4">
                <div>
                    <label for="name" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Họ và tên</label>
                    <input type="text" id="name" value="{{ $driver->name }}" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 mt-1" disabled />
                </div>
                <div>
                    <label for="phone" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Số điện thoại</label>
                    <input type="tel" id="phone" value="{{ $driver->phone }}" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 mt-1" disabled />
                </div>
                <div>
                    <label for="idCard" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Số CCCD</label>
                    <input type="text" id="idCard" value="{{ $driver->idCardNumber }}" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 mt-1" disabled />
                    <p class="text-xs text-muted-foreground mt-1">Thông tin này không thể thay đổi trực tiếp.</p>
                </div>
            </div>
        </div>

        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-4">
                <h3 class="text-2xl font-semibold leading-none tracking-tight flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 h-5 w-5 text-primary"><path d="M2 12h6"/><path d="M16 12h6"/><path d="M12 2v20"/><path d="M12 7l-5 5 5 5"/><path d="M12 17l5-5-5-5"/></svg> Thông tin phương tiện
                </h3>
            </div>
            <div class="p-4 pt-0 space-y-2">
                <p><strong>Phương tiện:</strong> {{ $driver->vehicle }}</p>
                <p><strong>Biển số xe:</strong> {{ $driver->licensePlate }}</p>
                <p class="text-xs text-muted-foreground mt-1">Để thay đổi thông tin phương tiện, vui lòng liên hệ hỗ trợ.</p>
            </div>
        </div>

        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-4">
                <h3 class="text-2xl font-semibold leading-none tracking-tight flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 h-5 w-5 text-primary"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg> Tài khoản ngân hàng
                </h3>
                <p class="text-sm text-muted-foreground">Dùng để nhận tiền ship hàng ngày/tuần.</p>
            </div>
            <div class="p-4 pt-0 space-y-4">
                <div>
                    <label for="bankName" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Tên ngân hàng</label>
                    <input type="text" id="bankName" value="{{ $driver->bankAccount['bankName'] }}" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 mt-1" disabled />
                </div>
                <div>
                    <label for="accountNumber" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Số tài khoản</label>
                    <input type="text" id="accountNumber" value="{{ $driver->bankAccount['accountNumber'] }}" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 mt-1" disabled />
                </div>
                <div>
                    <label for="accountHolderName" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Chủ tài khoản</label>
                    <input type="text" id="accountHolderName" value="{{ $driver->bankAccount['accountHolderName'] }}" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 mt-1" disabled />
                </div>
            </div>
        </div>

        <button id="save-profile-changes" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 w-full" style="display: none;">
            Lưu thay đổi
        </button>

        <button class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-destructive text-destructive-foreground hover:bg-destructive/90 h-10 px-4 py-2 w-full mt-8">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 h-4 w-4"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="17 16 22 12 17 8"/><line x1="22" x2="10" y1="12" y2="12"/></svg> Đăng xuất
        </button>
    </div>
@endsection

@section('page_scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            DriverApp.initProfilePage();
        });
    </script>
@endsection