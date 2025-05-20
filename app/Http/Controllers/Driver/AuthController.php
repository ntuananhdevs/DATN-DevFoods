<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Driver;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Exception;

class AuthController extends Controller
{
    /**
     * Hiển thị form đăng nhập
     */
    public function showLoginForm()
    {
        try {
            return view('driver.auth.login');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Xử lý đăng nhập
     */
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'phone_number' => 'required|string',
                'password' => 'required|string',
            ], [
                'phone_number.required' => 'Vui lòng nhập số điện thoại',
                'password.required' => 'Vui lòng nhập mật khẩu',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $driver = Driver::where('phone_number', $request->phone_number)->first();

            if (!$driver) {
                return back()->withErrors([
                    'phone_number' => 'Số điện thoại không tồn tại trong hệ thống',
                ])->withInput();
            }

            if (!Hash::check($request->password, $driver->password)) {
                return back()->withErrors([
                    'password' => 'Mật khẩu không chính xác',
                ])->withInput();
            }

            session(['driver_id' => $driver->id]);
            session(['driver_name' => $driver->full_name]);
            session(['driver_phone' => $driver->phone_number]);
            session(['driver_logged_in' => true]);
            
            // Kiểm tra xem đây có phải là lần đăng nhập đầu tiên không
            // Nếu là lần đăng nhập đầu tiên, hiển thị thông báo đổi mật khẩu
            if (!$driver->password_changed) {
                session(['first_login' => true]);
                return redirect()->route('driver.home')->with('warning', 'Vui lòng đổi mật khẩu để bảo mật tài khoản của bạn.');
            }

            return redirect()->route('driver.home')->with('success', 'Đăng nhập thành công!');
        } catch (Exception $e) {
            return back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Đăng xuất
     */
    public function logout(Request $request)
    {
        try {
            // Xóa thông tin tài xế khỏi session
            session()->forget(['driver_id', 'driver_name', 'driver_phone', 'driver_logged_in', 'first_login']);
            
            return redirect()->route('driver.login')->with('success', 'Đăng xuất thành công!');
        } catch (Exception $e) {
            return back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }
}