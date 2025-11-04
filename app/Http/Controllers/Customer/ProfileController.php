<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\EditProfileRequest;
use Illuminate\Http\Request;
use App\Models\UserRank;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\Address;
use App\Models\Branch;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    /**
     * Display the user's profile overview page.
     */
    public function profile()
    {
        $user = Auth::user();

        // Eager load các quan hệ chính để tăng hiệu năng
        $user->load('userRank', 'addresses', 'favorites');

        // Lấy 3 đơn hàng gần nhất
        $recentOrders = $user->orders()->with([
            'orderItems.productVariant.product.primaryImage',
            'orderItems.combo',
            'orderItems.toppings',
            'branch'
        ])->latest()->take(3)->get();

        $orderAll = $user->orders()->count();

        // Lấy các voucher còn hiệu lực
        $vouchers = $user->userDiscountCodes()->where('status', 'available')->get();

        // Lấy 5 hoạt động điểm thưởng gần nhất
        $pointHistory = $user->rewardPointHistories()->latest()->take(5)->get();

        // Lấy danh sách sản phẩm yêu thích (giả sử 6 sản phẩm)
        $favoriteProducts = $user->favorites()->with('product.primaryImage')->latest()->take(6)->get();

        $allRanks = UserRank::orderBy('min_spending', 'asc')->get();
        $currentRank = $user->userRank;

        if (!$currentRank && $allRanks->isNotEmpty()) {
            $currentRank = $allRanks->first();
        }

        $currentPoints = $user->total_spending;

        // Mốc cao nhất dùng để hiển thị max tiến trình
        $maxPoints = $allRanks->max('min_spending');

        // Tính phần trăm từ 0 đến max
        $progressPercent = $maxPoints > 0
            ? min(100, ($currentPoints / $maxPoints) * 100)
            : 0;

        // Tìm mốc tiếp theo (gần nhất lớn hơn điểm hiện tại)
        $nextRank = $allRanks->firstWhere('min_spending', '>', $currentPoints);


        // Trả về view với tất cả dữ liệu
        return view('customer.profile.index', compact(
            'user',
            'recentOrders',
            'orderAll',
            'vouchers',
            'pointHistory',
            'favoriteProducts',
            'currentRank',
            'nextRank',
            'allRanks',
            'currentPoints',
            'progressPercent'
        ));
    }

    /**
     * Show the form for editing the user's profile.
     */
    public function edit()
    {
        return view('customer.profile.edit', ['user' => Auth::user()]);
    }

    /**
     * Show the account settings page.
     */
    public function setting()
    {
        return view('customer.profile.setting', ['user' => Auth::user()]);
    }


    public function update(Request $request)
    {

        $user = Auth::user();


        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['required', 'string', 'max:15', Rule::unique('users')->ignore($user->id)],
            'birthday' => ['nullable', 'date'],
            'gender' => ['nullable', 'string', Rule::in(['male', 'female', 'other'])],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'], // 5MB
        ]);


        if ($request->hasFile('avatar')) {

            if ($user->avatar && $user->avatar !== 'avatars/default.jpg') {
                Storage::disk('s3')->delete($user->avatar);
            }

            $path = $request->file('avatar')->store('avatars', 's3');

            $user->avatar = $path;
        }

        $user->full_name = $validated['first_name'] . ' ' . $validated['last_name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'];
        $user->birthday = $validated['birthday'];
        $user->gender = $validated['gender'];

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật hồ sơ thành công!',
            'avatar_url' => Storage::disk('s3')->url($user->avatar)
        ]);
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->forceFill([
            'password' => Hash::make($validated['password']),
        ])->save();

        return response()->json(['status' => 'password-updated', 'message' => 'Mật khẩu đã được cập nhật thành công!']);
    }

    // API: Lấy danh sách địa chỉ của user
    public function getAddresses()
    {
        $addresses = Auth::user()->addresses()->orderByDesc('is_default')->get();
        return response()->json($addresses);
    }

    // API: Thêm địa chỉ mới
    public function storeAddress(Request $request)
    {
        try {
            $data = $request->validate([
                'recipient_name' => 'required|string|max:255',
                'address_line' => 'required|string|max:255',
                'city' => 'required|string|max:100',
                'district' => 'required|string|max:100',
                'ward' => 'required|string|max:100',
                'phone_number' => 'required|string|max:20',
                'is_default' => 'nullable|boolean',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
            ]);

            $data['user_id'] = Auth::id();

            // Check if user has any existing addresses
            $existingAddressCount = Address::where('user_id', Auth::id())->count();

            // If this is the first address, make it default automatically
            if ($existingAddressCount == 0) {
                $data['is_default'] = true;
            } else {
                // Handle checkbox value properly
                $data['is_default'] = $request->has('is_default') && $request->input('is_default') == '1';
            }

            // If setting as default, remove default from other addresses
            if ($data['is_default']) {
                Address::where('user_id', Auth::id())->update(['is_default' => false]);
            }

            $address = Address::create($data);

            // Prepare response data with all necessary fields
            $responseData = [
                'id' => $address->id,
                'recipient_name' => $address->recipient_name,
                'phone_number' => $address->phone_number,
                'address_line' => $address->address_line,
                'city' => $address->city,
                'district' => $address->district,
                'ward' => $address->ward,
                'latitude' => $address->latitude,
                'longitude' => $address->longitude,
                'is_default' => $address->is_default,
                'full_address' => $address->address_line . ', ' . $address->ward . ', ' . $address->district . ', ' . $address->city
            ];

            return response()->json([
                'success' => true,
                'message' => 'Địa chỉ đã được thêm thành công!',
                'data' => $responseData
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Store address error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thêm địa chỉ: ' . $e->getMessage()
            ], 500);
        }
    }

    // API: Cập nhật địa chỉ
    public function updateAddress(Request $request, $id)
    {
        try {
            $address = Auth::user()->addresses()->findOrFail($id);

            $data = $request->validate([
                'recipient_name' => 'required|string|max:255',
                'address_line' => 'required|string|max:255',
                'city' => 'required|string|max:100',
                'district' => 'required|string|max:100',
                'ward' => 'required|string|max:100',
                'phone_number' => 'required|string|max:20',
                'is_default' => 'boolean',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
            ]);

            // If setting as default, remove default from other addresses
            if (!empty($data['is_default'])) {
                Address::where('user_id', Auth::id())->update(['is_default' => false]);
            }

            $address->update($data);

            // Prepare response data with all necessary fields
            $responseData = [
                'id' => $address->id,
                'recipient_name' => $address->recipient_name,
                'phone_number' => $address->phone_number,
                'address_line' => $address->address_line,
                'city' => $address->city,
                'district' => $address->district,
                'ward' => $address->ward,
                'latitude' => $address->latitude,
                'longitude' => $address->longitude,
                'is_default' => $address->is_default,
                'full_address' => $address->address_line . ', ' . $address->ward . ', ' . $address->district . ', ' . $address->city
            ];

            return response()->json([
                'success' => true,
                'message' => 'Địa chỉ đã được cập nhật thành công!',
                'data' => $responseData
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Update address error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật địa chỉ: ' . $e->getMessage()
            ], 500);
        }
    }

    // API: Xóa địa chỉ
    public function deleteAddress($id)
    {
        try {
            $address = Auth::user()->addresses()->find($id);
            if (!$address) {
                return response()->json(['message' => 'Địa chỉ không tồn tại hoặc không thuộc về bạn'], 404);
            }
            $address->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Delete address error: ' . $e->getMessage());
            return response()->json(['message' => 'Lỗi server: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Lấy danh sách branch (nhà hàng) cho view địa chỉ
     */
    public function getBranchesForMap()
    {
        $branches = Branch::select('id', 'name', 'latitude', 'longitude')->get();
        return response()->json($branches);
    }
}
