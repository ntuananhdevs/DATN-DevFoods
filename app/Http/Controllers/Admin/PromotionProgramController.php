<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromotionProgram;
use App\Models\DiscountCode;
use App\Models\Branch;
use App\Models\PromotionDiscountCode;
use App\Models\PromotionBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PromotionProgramController extends Controller
{
    public function index()
    {
        $programs = PromotionProgram::with(['discountCodes', 'branches', 'createdBy'])
            ->orderBy('display_order')
            ->paginate(10);
        
        return view('admin.promotions.index', compact('programs'));
    }

    public function show(PromotionProgram $program)
    {
        $program->load(['discountCodes', 'branches', 'createdBy']);
        $availableDiscountCodes = DiscountCode::where('is_active', true)->get();
        $availableBranches = Branch::all();
        
        return view('admin.promotions.show', compact('program', 'availableDiscountCodes', 'availableBranches'));
    }

    public function create()
    {
        $branches = Branch::all();
        return view('admin.promotions.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'thumbnail_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'applicable_scope' => 'required|in:all_branches,specific_branches',
            'branch_ids' => 'required_if:applicable_scope,specific_branches|array',
            'branch_ids.*' => 'exists:branches,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'display_order' => 'required|integer|min:0',
        ]);

        $program = new PromotionProgram($validated);
        $program->created_by = Auth::id();

        // Handle file uploads
        if ($request->hasFile('banner_image')) {
            $program->banner_image = $request->file('banner_image')->store('banners', 'public');
        }
        if ($request->hasFile('thumbnail_image')) {
            $program->thumbnail_image = $request->file('thumbnail_image')->store('thumbnails', 'public');
        }

        $program->save();

        // Link branches if specific_branches
        if ($validated['applicable_scope'] === 'specific_branches') {
            $program->branches()->sync($request->input('branch_ids', []));
        }

        return redirect()->route('admin.promotions.index')->with('success', 'Promotion program created successfully.');
    }

    public function edit(PromotionProgram $program)
    {
        $branches = Branch::all();
        return view('admin.promotions.edit', compact('program', 'branches'));
    }

    public function update(Request $request, PromotionProgram $program)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'thumbnail_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'applicable_scope' => 'required|in:all_branches,specific_branches',
            'branch_ids' => 'required_if:applicable_scope,specific_branches|array',
            'branch_ids.*' => 'exists:branches,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'display_order' => 'required|integer|min:0',
        ]);

        // Handle file uploads
        if ($request->hasFile('banner_image')) {
            $validated['banner_image'] = $request->file('banner_image')->store('banners', 'public');
        }
        if ($request->hasFile('thumbnail_image')) {
            $validated['thumbnail_image'] = $request->file('thumbnail_image')->store('thumbnails', 'public');
        }

        $program->update($validated);

        // Update branches
        if ($validated['applicable_scope'] === 'specific_branches') {
            $program->branches()->sync($request->input('branch_ids', []));
        } else {
            $program->branches()->detach();
        }

        return redirect()->route('admin.promotions.index')->with('success', 'Promotion program updated successfully.');
    }

    public function destroy(PromotionProgram $program)
    {
        $program->delete();
        return redirect()->route('admin.promotions.index')->with('success', 'Promotion program deleted successfully.');
    }

    public function linkDiscountCode(Request $request, PromotionProgram $program)
    {
        $request->validate([
            'discount_code_id' => 'required|exists:discount_codes,id'
        ]);

        $program->discountCodes()->syncWithoutDetaching([$request->discount_code_id]);
        
        return redirect()->route('admin.promotions.show', $program)
            ->with('success', 'Discount code linked successfully.');
    }

    public function unlinkDiscountCode(PromotionProgram $program, DiscountCode $discountCode)
    {
        $program->discountCodes()->detach($discountCode->id);
        
        return redirect()->route('admin.promotions.show', $program)
            ->with('success', 'Discount code unlinked successfully.');
    }

    public function linkBranch(Request $request, PromotionProgram $program)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id'
        ]);

        $program->branches()->syncWithoutDetaching([$request->branch_id]);
        
        return redirect()->route('admin.promotions.show', $program)
            ->with('success', 'Branch linked successfully.');
    }

    public function unlinkBranch(PromotionProgram $program, Branch $branch)
    {
        $program->branches()->detach($branch->id);
        
        return redirect()->route('admin.promotions.show', $program)
            ->with('success', 'Branch unlinked successfully.');
    }
}