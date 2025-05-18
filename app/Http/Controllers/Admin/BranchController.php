<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BranchController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->input('search');
            $query = Branch::when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%")
                        ->orWhere('address', 'LIKE', "%$search%")
                        ->orWhere('phone', 'LIKE', "%$search%")
                        ->orWhere('email', 'LIKE', "%$search%");
                });
            })
            ->orderBy('id', 'asc');

            $branches = $query->paginate(10);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'branches' => $branches->items(),
                    'pagination' => [
                        'total' => $branches->total(),
                        'per_page' => $branches->perPage(),
                        'current_page' => $branches->currentPage(),
                        'last_page' => $branches->lastPage()
                    ]
                ]);
            }

            return view('admin.branch.index', compact('branches'));
        } catch (\Exception $e) {
            Log::error('Error in BranchController@index: ' . $e->getMessage());
           
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi tải danh sách chi nhánh'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi tải danh sách chi nhánh');
        }
    }

    public function create()
    {
        return view('admin.branch.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'address' => 'required',
            'phone' => 'required',
            'email' => 'nullable|email',
            'opening_hour' => 'required|date_format:H:i',
            'closing_hour' => 'required|date_format:H:i|after:opening_hour',
        ]);

        Branch::create($validated);
        
        return redirect()->route('admin.branches.index')->with('success', 'Thêm chi nhánh thành công');
    }

    public function show(Branch $branch)
    {
        return view('admin.branch.show', compact('branch'));
    }

    public function edit(Branch $branch)
    {
        return view('admin.branch.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            // ... existing code ...
        ]);

        $branch->update($validated);
        
        return redirect()->route('admin.branches.index')->with('success', 'Cập nhật thành công');
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();
        return redirect()->back()->with('success', 'Đã xóa chi nhánh');
    }

    /**
     * Thay đổi trạng thái của một chi nhánh
     */
    public function toggleStatus(Branch $branch)
    {
        try {
            $branch->active = !$branch->active;
            $branch->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Đã thay đổi trạng thái chi nhánh thành công'
            ]);
        } catch (\Exception $e) {
            Log::error('Error in BranchController@toggleStatus: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thay đổi trạng thái chi nhánh'
            ], 500);
        }
    }

    /**
     * Cập nhật trạng thái hàng loạt cho nhiều chi nhánh
     */
    public function bulkStatusUpdate(Request $request)
    {
        try {
            $validated = $request->validate([
                'branch_ids' => 'required|array',
                'branch_ids.*' => 'required|integer|exists:branches,id',
                'action' => 'required|in:activate,deactivate'
            ]);
            
            $active = $validated['action'] === 'activate';
            $count = Branch::whereIn('id', $validated['branch_ids'])
                ->update(['active' => $active]);
            
            return response()->json([
                'success' => true,
                'message' => "Đã cập nhật trạng thái $count chi nhánh thành công",
                'count' => $count
            ]);
        } catch (\Exception $e) {
            Log::error('Error in BranchController@bulkStatusUpdate: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật trạng thái hàng loạt'
            ], 500);
        }
    }
}