<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function logout(): RedirectResponse
    {
        Auth::logout(); // Xoá phiên đăng nhập

        // Clear session và chuyển hướng
        session()->invalidate();
        session()->regenerateToken();
        session()->flash('success', 'Đăng xuất thành công.');

        return redirect()->route('admin.login');
    }
}