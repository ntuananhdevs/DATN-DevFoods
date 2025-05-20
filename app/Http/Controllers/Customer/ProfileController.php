<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\EditProfileRequest;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    //
    public function profile()
    {
        return view('customer.profile.index');
    }
}
