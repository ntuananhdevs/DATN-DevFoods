<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    // Dashboard - Analytics
    public function index()
    {
        return view("customer.home");
    }
}

