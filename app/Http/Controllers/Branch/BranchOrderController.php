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
        $manager = Auth::guard('manager')->user();
        $branch = $manager ? $manager->branch : null;
        $orders = $branch ? Order::where('branch_id', $branch->id)->get() : collect();
        return view('branch.orders', compact('orders', 'branch'));
    }
}
