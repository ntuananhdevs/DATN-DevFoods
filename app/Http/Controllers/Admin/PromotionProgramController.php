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
use Illuminate\Support\Str;

class PromotionProgramController extends Controller
{
    public function index()
    {
        // Calculate statistics for promotions
        $now = now();
        $totalPrograms = PromotionProgram::count();
        $activePrograms = PromotionProgram::where('is_active', true)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->count();
        $scheduledPrograms = PromotionProgram::where('is_active', true)
            ->where('start_date', '>', $now)
            ->count();
        $expiredPrograms = PromotionProgram::where(function($query) use ($now) {
                $query->where('end_date', '<', $now)
                    ->orWhere('is_active', false);
            })
            ->count();

        $programs = PromotionProgram::with([
            'discountCodes' => function ($query) {
                $query->select(
                    'discount_codes.id',
                    'discount_type',
                    'discount_value',
                    'current_usage_count',
                    'max_total_usage',
                    'is_active'
                );
            },
            'branches:id',
            'createdBy:id,full_name' // Thay 'full_name' bằng tên cột thực tế
        ])->orderBy('display_order')->paginate(10);
    
        $programs->getCollection()->transform(function ($program) {
            $discountCodes = $program->discountCodes;
    
            // Tính toán phạm vi giá trị giảm giá
            $percentageValues = $discountCodes->where('discount_type', 'percentage')
                ->pluck('discount_value')
                ->filter()
                ->values();
            $fixedAmountValues = $discountCodes->where('discount_type', 'fixed_amount')
                ->pluck('discount_value')
                ->filter()
                ->values();
            $hasFreeShipping = $discountCodes->where('discount_type', 'free_shipping')->isNotEmpty();
    
            $value_range = [];
            if ($percentageValues->isNotEmpty()) {
                $minPercentage = $percentageValues->min();
                $maxPercentage = $percentageValues->max();
                $value_range[] = $minPercentage == $maxPercentage
                    ? "{$minPercentage}%"
                    : "{$minPercentage}% - {$maxPercentage}%";
            }
            if ($fixedAmountValues->isNotEmpty()) {
                $minAmount = $fixedAmountValues->min();
                $maxAmount = $fixedAmountValues->max();
                $value_range[] = $minAmount == $maxAmount
                    ? number_format($minAmount) . ' đ'
                    : number_format($minAmount) . ' đ - ' . number_format($maxAmount) . ' đ';
            }
            if ($hasFreeShipping) {
                $value_range[] = 'Miễn phí vận chuyển';
            }
    
            // Thêm thuộc tính value_range vào đối tượng
            $program->setAttribute('value_range', !empty($value_range) ? implode(', ', $value_range) : 'Chưa xác định');
    
            return $program;
        });
    
        return view('admin.promotions.index', compact('programs', 'totalPrograms', 'activePrograms', 'scheduledPrograms', 'expiredPrograms'));
    }

    public function search(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|in:all,active,scheduled,expired,inactive',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'type' => 'nullable|string'
        ]);

        $search = $request->input('search', '');
        $status = $request->input('status', 'all');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $type = $request->input('type');
        
        $now = now();
        $query = PromotionProgram::with([
            'discountCodes' => function ($query) {
                $query->select(
                    'discount_codes.id',
                    'discount_type',
                    'discount_value',
                    'current_usage_count',
                    'max_total_usage',
                    'is_active'
                );
            },
            'branches:id',
            'createdBy:id,full_name'
        ])->orderBy('display_order');

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($status !== 'all') {
            switch ($status) {
                case 'active':
                    $query->where('is_active', true)
                          ->where('start_date', '<=', $now)
                          ->where('end_date', '>=', $now);
                    break;
                case 'scheduled':
                    $query->where('is_active', true)
                          ->where('start_date', '>', $now);
                    break;
                case 'expired':
                    $query->where('is_active', true)
                          ->where('end_date', '<', $now);
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
            }
        }

        if ($dateFrom) {
            $query->where('end_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->where('start_date', '<=', $dateTo);
        }

        $programs = $query->get();

        // Transform programs to include value_range attribute
        $programs->transform(function ($program) {
            $discountCodes = $program->discountCodes;
    
            // Calculate discount value range
            $percentageValues = $discountCodes->where('discount_type', 'percentage')
                ->pluck('discount_value')
                ->filter()
                ->values();
            $fixedAmountValues = $discountCodes->where('discount_type', 'fixed_amount')
                ->pluck('discount_value')
                ->filter()
                ->values();
            $hasFreeShipping = $discountCodes->where('discount_type', 'free_shipping')->isNotEmpty();
    
            $value_range = [];
            if ($percentageValues->isNotEmpty()) {
                $minPercentage = $percentageValues->min();
                $maxPercentage = $percentageValues->max();
                $value_range[] = $minPercentage == $maxPercentage
                    ? "{$minPercentage}%"
                    : "{$minPercentage}% - {$maxPercentage}%";
            }
            if ($fixedAmountValues->isNotEmpty()) {
                $minAmount = $fixedAmountValues->min();
                $maxAmount = $fixedAmountValues->max();
                $value_range[] = $minAmount == $maxAmount
                    ? number_format($minAmount) . ' đ'
                    : number_format($minAmount) . ' đ - ' . number_format($maxAmount) . ' đ';
            }
            if ($hasFreeShipping) {
                $value_range[] = 'Miễn phí vận chuyển';
            }
    
            $program->setAttribute('value_range', !empty($value_range) ? implode(', ', $value_range) : 'Chưa xác định');
    
            return $program;
        });

        // Calculate statistics for the response
        $totalPrograms = PromotionProgram::count();
        $activePrograms = PromotionProgram::where('is_active', true)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->count();
        $scheduledPrograms = PromotionProgram::where('is_active', true)
            ->where('start_date', '>', $now)
            ->count();
        $expiredPrograms = PromotionProgram::where(function($query) use ($now) {
                $query->where('end_date', '<', $now)
                    ->orWhere('is_active', false);
            })
            ->count();

        $response = [
            'programs' => $programs->map(function ($program) use ($now) {
                // Determine status
                if (!$program->is_active) {
                    $status = 'inactive';
                    $statusText = 'Không hoạt động';
                } elseif ($program->start_date && $now->lt($program->start_date)) {
                    $status = 'scheduled';
                    $statusText = 'Sắp diễn ra';
                } elseif ($program->end_date && $now->gt($program->end_date)) {
                    $status = 'expired';
                    $statusText = 'Đã hết hạn';
                } else {
                    $status = 'active';
                    $statusText = 'Đang hoạt động';
                }
                
                // Determine program type
                $discountTypes = $program->discountCodes ? $program->discountCodes->pluck('discount_type')->unique()->toArray() : [];
                if (count($discountTypes) == 1) {
                    switch ($discountTypes[0] ?? '') {
                        case 'percentage':
                            $typeClass = 'discount';
                            $typeText = 'Giảm giá %';
                            break;
                        case 'fixed_amount':
                            $typeClass = 'discount';
                            $typeText = 'Giảm giá cố định';
                            break;
                        case 'free_shipping':
                            $typeClass = 'special';
                            $typeText = 'Miễn phí vận chuyển';
                            break;
                        default:
                            $typeClass = 'special';
                            $typeText = 'Kết hợp';
                            break;
                    }
                } else {
                    $typeClass = 'special';
                    $typeText = 'Kết hợp';
                }

                return [
                    'id' => $program->id,
                    'name' => $program->name,
                    'description' => $program->description ? Str::limit($program->description, 50) : '',
                    'start_date' => $program->start_date ? $program->start_date->format('d/m/Y') : 'N/A',
                    'end_date' => $program->end_date ? $program->end_date->format('d/m/Y') : 'N/A',
                    'value_range' => $program->value_range,
                    'total_usage_count' => $program->total_usage_count ?? 0,
                    'total_usage_limit' => $program->total_usage_limit,
                    'is_active' => $program->is_active,
                    'status' => $status,
                    'status_text' => $statusText,
                    'type_class' => $typeClass,
                    'type_text' => $typeText,
                    'discount_codes' => $program->discountCodes ? $program->discountCodes->map(function($code) {
                        return [
                            'code' => $code->code,
                            'current_usage_count' => $code->current_usage_count,
                            'max_total_usage' => $code->max_total_usage,
                            'is_active' => $code->is_active
                        ];
                    }) : []
                ];
            }),
            'total_programs' => $totalPrograms,
            'active_programs' => $activePrograms,
            'scheduled_programs' => $scheduledPrograms,
            'expired_programs' => $expiredPrograms
        ];

        return response()->json($response);
    }

    public function show(PromotionProgram $program)
    {
        // Load relationship data
        $program->load([
            'discountCodes' => function($query) {
                $query->with(['branches']);
            }, 
            'branches', 
            'createdBy'
        ]);
        
        // Get available discount codes not already linked
        $linkedDiscountIds = $program->discountCodes->pluck('id')->toArray();
        $availableDiscountCodes = DiscountCode::where('is_active', true)
            ->whereNotIn('id', $linkedDiscountIds)
            ->get();
        
        // Get available branches not already linked (if applicable)
        $linkedBranchIds = $program->branches->pluck('id')->toArray();
        $availableBranches = Branch::whereNotIn('id', $linkedBranchIds)->get();
        
        // Get user ranks for display
        $userRanks = \App\Models\UserRank::orderBy('display_order')->get();
        
        return view('admin.promotions.show', compact('program', 'availableDiscountCodes', 'availableBranches', 'userRanks'));
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

        // Xử lý checkbox is_featured và is_active
        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_active'] = $request->has('is_active');

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

        return redirect()->route('admin.promotions.index')->with('toast', [
            'type' => 'success',
            'title' => 'Thành công!',
            'message' => 'Chương trình khuyến mãi đã được tạo thành công.'
        ]);
    }

    public function edit(PromotionProgram $program)
    {
        // Load the program with its relationships
        $program->load([
            'discountCodes' => function($query) {
                $query->with(['branches']);
            }, 
            'branches'
        ]);
        
        $branches = Branch::all();
        $userRanks = \App\Models\UserRank::orderBy('display_order')->get();
        
        return view('admin.promotions.edit', compact('program', 'branches', 'userRanks'));
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

        // Xử lý checkbox is_featured và is_active
        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_active'] = $request->has('is_active');

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

        return redirect()->route('admin.promotions.index')->with('toast', [
            'type' => 'success',
            'title' => 'Thành công!',
            'message' => 'Chương trình khuyến mãi đã được cập nhật thành công.'
        ]);
    }

    public function destroy(PromotionProgram $program)
    {
        $program->delete();
        return redirect()->route('admin.promotions.index')->with('toast', [
            'type' => 'success',
            'title' => 'Thành công!',
            'message' => 'Chương trình khuyến mãi đã được xóa thành công.'
        ]);
    }

    public function linkDiscountCode(Request $request, PromotionProgram $program)
    {
        $request->validate([
            'discount_code_id' => 'required|exists:discount_codes,id'
        ]);

        $program->discountCodes()->syncWithoutDetaching([$request->discount_code_id]);
        
        return redirect()->route('admin.promotions.show', $program)
            ->with('toast', [
                'type' => 'success',
                'title' => 'Thành công!',
                'message' => 'Mã giảm giá đã được liên kết thành công.'
            ]);
    }

    public function unlinkDiscountCode(PromotionProgram $program, DiscountCode $discountCode)
    {
        $program->discountCodes()->detach($discountCode->id);
        
        return redirect()->route('admin.promotions.show', $program)
            ->with('toast', [
                'type' => 'success',
                'title' => 'Thành công!',
                'message' => 'Đã hủy liên kết mã giảm giá thành công.'
            ]);
    }

    public function linkBranch(Request $request, PromotionProgram $program)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id'
        ]);

        $program->branches()->syncWithoutDetaching([$request->branch_id]);
        
        return redirect()->route('admin.promotions.show', $program)
            ->with('toast', [
                'type' => 'success',
                'title' => 'Thành công!',
                'message' => 'Chi nhánh đã được liên kết thành công.'
            ]);
    }

    public function unlinkBranch(PromotionProgram $program, Branch $branch)
    {
        $program->branches()->detach($branch->id);
        
        return redirect()->route('admin.promotions.show', $program)
            ->with('toast', [
                'type' => 'success',
                'title' => 'Thành công!',
                'message' => 'Đã hủy liên kết chi nhánh thành công.'
            ]);
    }

    public function bulkStatusUpdate(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:promotion_programs,id',
            'action' => 'required|in:activate,deactivate'
        ]);
        
        $isActive = $validated['action'] === 'activate';
        $count = count($validated['ids']);
        
        PromotionProgram::whereIn('id', $validated['ids'])
            ->update(['is_active' => $isActive]);
        
        // Lấy thông tin cập nhật về các chương trình
        $updatedPrograms = PromotionProgram::whereIn('id', $validated['ids'])
            ->select('id', 'name', 'is_active', 'start_date', 'end_date')
            ->get();
        
        $message = $isActive ? 'Kích hoạt' : 'Vô hiệu hóa';
        return response()->json([
            'success' => true,
            'message' => "{$message} thành công {$count} chương trình khuyến mãi",
            'programs' => $updatedPrograms
        ]);
    }
}