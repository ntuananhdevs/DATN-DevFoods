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
        $recentOrders = $user->orders()->with('items.product')->latest()->take(3)->get();

        // Lấy các voucher còn hiệu lực
        $vouchers = $user->userDiscountCodes()->where('status', 'available')->get();

        // Lấy 5 hoạt động điểm thưởng gần nhất
        $pointHistory = $user->rewardPointHistories()->latest()->take(5)->get();

        // Lấy danh sách sản phẩm yêu thích (giả sử 6 sản phẩm)
        $favoriteProducts = $user->favorites()->with('product.primaryImage')->latest()->take(6)->get();

        // --- Logic tính toán hạng thành viên ---
        $allRanks = UserRank::orderBy('min_spending', 'asc')->get();
        $currentRank = $user->userRank;
        
        // Xử lý trường hợp người dùng mới chưa có hạng
        if (!$currentRank && $allRanks->isNotEmpty()) {
            $currentRank = $allRanks->first();
        }

        $nextRank = null;
        $progressPercent = 0;
        $currentPoints = $user->total_spending;

        if ($currentRank) {
            $nextRank = $allRanks->firstWhere('min_spending', '>', $currentRank->min_spending);
            if ($nextRank) {
                $range = $nextRank->min_spending - $currentRank->min_spending;
                $achieved = $currentPoints - $currentRank->min_spending;
                if ($range > 0) {
                    $progressPercent = min(100, ($achieved / $range) * 100);
                }
            } else {
                // Đã đạt hạng cao nhất
                $progressPercent = 100;
            }
        }
        
        // Trả về view với tất cả dữ liệu
        return view('customer.profile.index', compact(
            'user',
            'recentOrders',
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
}