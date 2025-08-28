<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\Api\OrderController;
use App\Services\ShippingService;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Order API endpoints
// Route::post('/orders', [OrderController::class, 'store']);

// Shipping fee calculation
Route::get('/shipping-fee', function () {
    $orderAmount = request('order_amount', 0);
    $distanceKm = request('distance_km', null);

    $shippingFee = ShippingService::calculateShippingFee($distanceKm, $orderAmount);
    $freeShippingThreshold = \App\Models\GeneralSetting::getFreeShippingThreshold();

    return response()->json([
        'shipping_fee' => $shippingFee,
        'free_shipping_threshold' => $freeShippingThreshold,
        'qualifies_for_free_shipping' => ShippingService::qualifiesForFreeShipping($orderAmount),
        'display_text' => ShippingService::getShippingDisplayText($shippingFee)
    ]);
});

// User Address API endpoints
Route::middleware('auth')->group(function () {
    Route::get('/user/addresses/{id}', function ($id) {
        $address = \App\Models\Address::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$address) {
            return response()->json(['success' => false, 'message' => 'Địa chỉ không tìm thấy'], 404);
        }

        return response()->json([
            'success' => true,
            'address' => [
                'id' => $address->id,
                'user_name' => auth()->user()->full_name,
                'phone_number' => $address->phone_number,
                'full_address' => $address->full_address,
                'city' => $address->city,
                'district' => $address->district,
                'ward' => $address->ward,
                'address_line' => $address->address_line,
                'is_default' => $address->is_default,
            ]
        ]);
    });

    Route::post('/user/addresses', [\App\Http\Controllers\Customer\AddressController::class, 'store'])
        ->middleware('auth:sanctum')
        ->name('api.user.addresses.store');
});

// Location API endpoints (public)
Route::get('/locations/hanoi/districts', function () {
    // Mock data for Hanoi districts - you can replace with real API call
    return response()->json([
        'districts' => [
            ['name' => 'Ba Đình', 'code' => '001'],
            ['name' => 'Hoàn Kiếm', 'code' => '002'],
            ['name' => 'Hai Bà Trưng', 'code' => '003'],
            ['name' => 'Đống Đa', 'code' => '004'],
            ['name' => 'Tây Hồ', 'code' => '005'],
            ['name' => 'Cầu Giấy', 'code' => '006'],
            ['name' => 'Thanh Xuân', 'code' => '007'],
            ['name' => 'Hoàng Mai', 'code' => '008'],
            ['name' => 'Long Biên', 'code' => '009'],
            ['name' => 'Hà Đông', 'code' => '010'],
            ['name' => 'Sơn Tây', 'code' => '011'],
            ['name' => 'Nam Từ Liêm', 'code' => '012'],
            ['name' => 'Bắc Từ Liêm', 'code' => '013'],
        ]
    ]);
});

Route::get('/locations/districts/{code}/wards', function ($code) {
    // Mock data for wards - you can replace with real API call or database
    $wards = [
        '001' => [ // Ba Đình
            ['name' => 'Phường Phúc Xá'],
            ['name' => 'Phường Trúc Bạch'],
            ['name' => 'Phường Vĩnh Phúc'],
            ['name' => 'Phường Cống Vị'],
            ['name' => 'Phường Liễu Giai'],
            ['name' => 'Phường Nguyễn Trung Trực'],
            ['name' => 'Phường Quán Thánh'],
            ['name' => 'Phường Ngọc Hà'],
            ['name' => 'Phường Điện Biên'],
            ['name' => 'Phường Đội Cấn'],
            ['name' => 'Phường Ngọc Khánh'],
            ['name' => 'Phường Kim Mã'],
            ['name' => 'Phường Giảng Võ'],
            ['name' => 'Phường Thành Công'],
        ],
        '002' => [ // Hoàn Kiếm
            ['name' => 'Phường Phúc Tân'],
            ['name' => 'Phường Đồng Xuân'],
            ['name' => 'Phường Hàng Mã'],
            ['name' => 'Phường Hàng Buồm'],
            ['name' => 'Phường Hàng Đào'],
            ['name' => 'Phường Hàng Bồ'],
            ['name' => 'Phường Cửa Đông'],
            ['name' => 'Phường Lý Thái Tổ'],
            ['name' => 'Phường Hàng Bạc'],
            ['name' => 'Phường Hàng Gai'],
            ['name' => 'Phường Chương Dương Độ'],
            ['name' => 'Phường Hàng Trống'],
            ['name' => 'Phường Cửa Nam'],
            ['name' => 'Phường Hàng Bông'],
            ['name' => 'Phường Tràng Tiền'],
            ['name' => 'Phường Trần Hưng Đạo'],
            ['name' => 'Phường Phan Chu Trinh'],
            ['name' => 'Phường Hàng Bài'],
        ],
        // Add more districts as needed
    ];

    $districtWards = $wards[$code] ?? [];

    return response()->json([
        'wards' => $districtWards
    ]);
});

// API routes will be added here when needed

// Driver API endpoints

// Driver location API endpoint
Route::get('/drivers/locations', function () {
    $driverLocations = \App\Models\DriverLocation::with(['driver', 'driver.documents'])
        ->whereHas('driver', function($query) {
            $query->where('status', 'active');
        })
        ->get()
        ->map(function ($location) {
            $driver = $location->driver;
            $documents = $driver->documents->first(); // Lấy thông tin giấy tờ
            
            return [
                'id' => $driver->id,
                'name' => $driver->full_name,
                'phone' => $driver->phone_number,
                'lat' => (float) $location->latitude,
                'lng' => (float) $location->longitude,
                'status' => $driver->driver_status, // Sử dụng accessor đã cập nhật
                'rating' => $driver->rating,
                'totalOrders' => $driver->orders()->count(),
                'updated_at' => $location->updated_at ? $location->updated_at->diffForHumans() : null,
                // Thêm thông tin từ bảng driver_document
                'documents' => $documents ? [
                    'license_number' => $documents->license_number,
                    'license_class' => $documents->license_class,
                    'license_expiry' => $documents->license_expiry,
                    'vehicle_type' => $documents->vehicle_type,
                    'vehicle_color' => $documents->vehicle_color,
                    'license_plate' => $documents->license_plate,
                    'vehicle_registration' => $documents->vehicle_registration
                ] : null
            ];
        });
    
    return response()->json($driverLocations);
});

// Driver detail API endpoint
Route::get('/drivers/{id}', function ($id) {
    $driver = \App\Models\Driver::with('documents')->findOrFail($id);
    $documents = $driver->documents->first();
    
    return response()->json([
        'id' => $driver->id,
        'name' => $driver->full_name,
        'phone' => $driver->phone_number,
        'status' => $driver->driver_status,
        'rating' => $driver->rating,
        'totalOrders' => $driver->orders()->count(),
        'documents' => $documents ? [
            'license_number' => $documents->license_number,
            'license_class' => $documents->license_class,
            'license_expiry' => $documents->license_expiry,
            'vehicle_type' => $documents->vehicle_type,
            'vehicle_color' => $documents->vehicle_color,
            'license_plate' => $documents->license_plate,
            'vehicle_registration' => $documents->vehicle_registration
        ] : null
    ]);
});
