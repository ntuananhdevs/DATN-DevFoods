<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    public function generate(Request $request)
    {
        return response()->json(['success' => true]);
    }
    
    public function updateStatus(Request $request)
    {
        return response()->json(['success' => true]);
    }
    
    public function show(Request $request)
    {
        return response()->json(['success' => true]);
    }
} 