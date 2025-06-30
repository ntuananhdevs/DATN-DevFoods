<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Branch;
use App\Models\Order;

class BranchOrderController extends Controller
{
    public function index()
    {
        return view('branch.orders.index');
    }
    public function show()
    {
        return view('branch.orders.show');
    }
}
