<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class BranchCategoryController extends Controller
{
    public function index(Request $request)
    {
        // Lấy danh mục theo chi nhánh nếu có logic branch, nếu không thì lấy toàn bộ
        $categories = Category::all(); // Nếu có branch_id thì filter theo branch
        return view('branch.categories', compact('categories'));
    }
}
