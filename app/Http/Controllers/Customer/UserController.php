<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\EditProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{
    //
    public function showProfile()
    {
        $user = Auth::user();
        return view('customer.user.Profile', compact('user'));
    }
    public function ShowForm(){
        $user = Auth::user();
        return view('customer.user.EditProfile', compact('user'));
    }

    public function updateProfile(EditProfileRequest $request)
    {
        try {
            $user = Auth::user();
            $data = $request->only(['full_name', 'phone']);
            if ($request->hasFile('avatar')) {
                // Xoá ảnh cũ nếu có
                if ($user->avatar && Storage::disk('public')->exists('avatars/' . $user->avatar)) {
                    Storage::disk('public')->delete('avatars/' . $user->avatar);
                }
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $data['avatar'] = basename($avatarPath); // Hoặc dùng $avatarPath nếu bạn muốn lưu cả folder
            }

            $user->update($data);
    
            return redirect()->route('customer.profile')->with('success', 'Thông tin cá nhân đã được cập nhật thành công.');
    
        } catch (\Exception $e) {
            \Log::error('Error updating profile for user ' . ($user->id ?? 'unknown') . ': ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra, vui lòng thử lại sau.');
        }
    }

    
}
