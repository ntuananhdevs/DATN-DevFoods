<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Branch;
use App\Models\Combo;
use App\Models\Topping;

class BranchProductController extends Controller
{
    public function index()
    {
        $manager = Auth::guard('manager')->user();
        $branch = $manager ? $manager->branch : null;
        $products = $branch ? $branch->products()->with(['category', 'productImages'])->get() : collect();
        return view('branch.products', compact('products', 'branch'));
    }

    public function indexCombo()
    {
        $manager = Auth::guard('manager')->user();
        $branch = $manager ? $manager->branch : null;
        $combos = $branch ? $branch->combos()->with(['comboItems.product'])->get() : collect();
        return view('branch.combos', compact('combos', 'branch'));
    }

    public function indexTopping()
    {
        $manager = Auth::guard('manager')->user();
        $branch = $manager ? $manager->branch : null;
        $toppings = $branch ? $branch->toppings()->get() : collect();
        return view('branch.toppings', compact('toppings', 'branch'));
    }
}
