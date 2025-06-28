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
        $manager = Auth::user();
        $branch = Branch::where('manager_user_id', $manager->id)->first();
        $products = $branch ? $branch->products()->with(['primaryImage', 'category', 'branchStocks'])->get() : collect();
        return view('branch.products', compact('products', 'branch'));
    }

    public function indexCombo()
    {
        $manager = Auth::user();
        $branch = Branch::where('manager_user_id', $manager->id)->first();
        $combos = Combo::whereHas('productVariants.branchStocks', function ($q) use ($branch) {
            $q->where('branch_id', $branch->id);
        })->with(['productVariants.product'])->get();
        return view('branch.combos', compact('combos', 'branch'));
    }

    public function indexTopping()
    {
        $manager = Auth::user();
        $branch = Branch::where('manager_user_id', $manager->id)->first();
        $toppings = Topping::whereHas('toppingStocks', function ($q) use ($branch) {
            $q->where('branch_id', $branch->id);
        })->with(['toppingStocks'])->get();
        return view('branch.toppings', compact('toppings', 'branch'));
    }
}
