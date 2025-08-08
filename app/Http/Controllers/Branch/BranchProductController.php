<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Branch;
use App\Models\Combo;
use App\Models\Topping;
use App\Models\Product;

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

    /**
     * Display the specified product.
     */
    public function show($slug)
    {
        $manager = Auth::guard('manager')->user();
        $branch = $manager ? $manager->branch : null;

        if (!$branch) {
            return redirect()->back()->with('error', 'Không tìm thấy thông tin chi nhánh');
        }

        $product = Product::with([
            'category',
            'images',
            'variants.branchStocks' => function ($query) use ($branch) {
                $query->where('branch_id', $branch->id);
            },
            'variants.variantValues.attribute',
            'toppings',
            'reviews.customer'
        ])->where('slug', $slug)
          ->firstOrFail();

        return view('branch.products.show', compact('product', 'branch'));
    }

    /**
     * Display the specified combo.
     */
    public function showCombo($slug)
    {
        $manager = Auth::guard('manager')->user();
        $branch = $manager ? $manager->branch : null;

        if (!$branch) {
            return redirect()->back()->with('error', 'Không tìm thấy thông tin chi nhánh');
        }

        $combo = Combo::with([
            'category',
            'comboItems.productVariant.product.images',
            'comboItems.productVariant.variantValues.attribute',
            'comboBranchStocks' => function ($query) use ($branch) {
                $query->where('branch_id', $branch->id);
            },
            'reviews.user'
        ])->where('slug', $slug)
          ->firstOrFail();

        return view('branch.combos.show', compact('combo', 'branch'));
    }
}
