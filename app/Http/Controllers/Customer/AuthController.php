<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Customer\RegisterRequest;
use Exception;

class AuthController extends Controller
{
    /**
     * Hiển thị form đăng nhập
     */
    public function showLoginForm()
    {
        return view('customer.auth.login');
    }

    public function showRegisterForm()
    {
        return view('customer.auth.register');
    }
    
    public function logout(Request $request)
    {
        //
    }
}
