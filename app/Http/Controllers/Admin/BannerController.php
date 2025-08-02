<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index(Request $request)
    {
        try {
            Banner::where('end_at', '<', now())->update(['is_active' => false]);
            $search = $request->input('search');
            $query = Banner::orderBy('created_at', 'desc');

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%');
                });
            }

            $banners = $query->paginate(5);
            $banners->appends(['search' => $search]);

            return view('admin.banner.index', compact('banners', 'search'));
        } catch (\Exception $e) {
            Log::error('Error in BannerController@index: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi tải danh sách banner.');
        }
    }


    public function create()
    {
        return view('admin.banner.create');
    }


    public function store(Request $request)
    {
        try {
            // Xác định validation rules dựa trên vị trí
            $rules = [
                'image_path' => 'nullable|image|max:5120',
                'image_link' => 'nullable|url',
                'link' => ['nullable', 'string', 'regex:/^\/shop\/products\/[a-z0-9\-]+$/'],
                'position' => 'required|string',
                'title' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'start_at' => 'required|date',
                'end_at' => 'required|date|after:start_at',
                'is_active' => 'required|boolean',
            ];

            // Chỉ thêm validation cho order khi vị trí là homepage
            if ($request->position === 'homepage') {
                $rules['order'] = 'required|integer|min:0';
            } else {
                $rules['order'] = [
                    'nullable',
                    function ($attribute, $value, $fail) {
                        // Nếu không phải homepage mà người dùng vẫn nhập order
                        if (!is_null($value)) {
                            $fail('Chỉ banner có vị trí là "homepage" mới được có thứ tự hiển thị.');
                        }
                    },
                ];
                // ✅ Đảm bảo luôn ghi đè giá trị order về null nếu không phải homepage
                $request->merge(['order' => null]);
            }            
            $validated = $request->validate($rules, [
                'order.unique' => 'Vị trí này đã được sử dụng bởi banner khác',
                'required' => ':attribute không được để trống.',
                'string' => ':attribute phải là chuỗi.',
                'url' => ':attribute phải là URL hợp lệ.',
                'link.regex' => 'Link sản phẩm không hợp lệ. Định dạng đúng là /shop/products/slug (ví dụ: /shop/products/banh-mi-thit-nuong).',
                'max' => ':attribute không được vượt quá :max KB.',
                'date' => ':attribute phải là ngày hợp lệ.',
                'after' => ':attribute phải sau ngày bắt đầu.',
                'image' => ':attribute phải là hình ảnh.',
                'boolean' => ':attribute không hợp lệ.'
            ], [
                'image_path' => 'Hình ảnh tải lên',
                'image_link' => 'Đường dẫn ảnh',
                'link' => 'Link sản phẩm',
                'title' => 'Tiêu đề',
                'description' => 'Mô tả',
                'start_at' => 'Ngày bắt đầu',
                'end_at' => 'Ngày kết thúc',
                'is_active' => 'Trạng thái',
                'order' => 'Thứ tự hiển thị'
            ]);

            $hasFile = $request->hasFile('image_path');
            $hasLink = $request->filled('image_link');

            if ($hasFile && $hasLink) {
                return back()->withErrors([
                    'image_path' => 'Chỉ được chọn một: ảnh tải lên hoặc đường dẫn ảnh.',
                    'image_link' => 'Chỉ được chọn một: ảnh tải lên hoặc đường dẫn ảnh.',
                ])->withInput();
            }

            if (!$hasFile && !$hasLink) {
                return back()->withErrors([
                    'image_path' => 'Bạn phải chọn ảnh tải lên hoặc nhập đường dẫn ảnh.',
                ])->withInput();
            }

            if ($hasFile) {
                $image = $request->file('image_path');
                $filename = uniqid() . '.' . $image->getClientOriginalExtension();
                $path = 'banners/' . $filename;
                Storage::disk('s3')->put($path, file_get_contents($image));
                $validated['image_path'] = $path; // hoặc Storage::disk('s3')->url($path) nếu bạn muốn lưu URL đầy đủ
                unset($validated['image_link']);
            } else {
                $validated['image_path'] = $request->input('image_link');
                unset($validated['image_link']);
            }

            // Kiểm tra trùng thứ tự hiển thị nếu vị trí là homepage và có order
            if ($request->position === 'homepage' && isset($validated['order'])) {
                $existingBanner = Banner::where('order', $validated['order'])
                    ->where('position', 'homepage')
                    ->first();

                if ($existingBanner) {
                    // Có banner trùng thứ tự, hiển thị thông báo cho người dùng
                    return back()->withInput()->with('duplicate_order', [
                        'banner_id' => $existingBanner->id,
                        'banner_title' => $existingBanner->title,
                        'order' => $validated['order']
                    ])->with('toast', [
                        'type' => 'warning',
                        'title' => 'Cảnh báo',
                        'message' => 'Thứ tự hiển thị ' . $validated['order'] . ' đã được sử dụng bởi banner "' . $existingBanner->title . '". Vui lòng chọn thứ tự khác hoặc điều chỉnh thứ tự của banner hiện có.'
                    ]);
                }
            }

            Banner::create($validated);

            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công',
                'message' => 'Banner đã được tạo thành công'
            ]);
            return redirect()->route('admin.banners.index');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error in BannerController@store: ' . $e->getMessage());
            session()->flash('toast', [
                'type' => 'error',
                'title' => 'Lỗi',
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
            return back()->withInput();
        }
    }



    public function show(string $id)
    {
        try {
            $banner = Banner::findOrFail($id);
            return view('admin.banner.show', compact('banner'));
        } catch (\Exception $e) {
            Log::error('Error in BannerController@show: ' . $e->getMessage());
            return redirect()->route('admin.banners.index')
                ->with('toast', [
                    'type' => 'error',
                    'title' => 'Lỗi',
                    'message' => 'Không tìm thấy banner'
                ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $banner = Banner::findOrFail($id);

            // Generate image URL for display
            $bannerImageUrl = null;
            if ($banner->image_path) {
                // Check if it's already a full URL
                if (filter_var($banner->image_path, FILTER_VALIDATE_URL)) {
                    $bannerImageUrl = $banner->image_path;
                } else {
                    // It's an S3 path, generate the full URL
                    $bannerImageUrl = Storage::disk('s3')->url($banner->image_path);
                }
            }

            return view('admin.banner.edit', compact('banner', 'bannerImageUrl'));
        } catch (\Exception $e) {
            Log::error('Error in BannerController@edit: ' . $e->getMessage());
            return redirect()->route('admin.banners.index')
                ->with('toast', [
                    'type' => 'error',
                    'title' => 'Lỗi',
                    'message' => 'Không tìm thấy banner cần chỉnh sửa'
                ]);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $banner = Banner::findOrFail($id);

            // Xác định validation rules dựa trên vị trí
            $rules = [
                'image_path' => 'nullable|image|max:5120',
                'image_link' => 'nullable|url',
                'link' => ['nullable', 'string', 'regex:/^\/shop\/products\/[a-z0-9\-]+$/'],
                'position' => 'required|string',
                'title' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'start_at' => 'required|date',
                'end_at' => 'required|date|after:start_at',
                'is_active' => 'required|boolean',
            ];
            // Chỉ thêm validation cho order khi vị trí là homepage
            if ($request->position === 'homepage') {
                $rules['order'] = 'required|integer|min:0';
            } else {
                $rules['order'] = [
                    'nullable',
                    function ($attribute, $value, $fail) {
                        // Nếu không phải homepage mà người dùng vẫn nhập order
                        if (!is_null($value)) {
                            $fail('Chỉ banner có vị trí là "homepage" mới được có thứ tự hiển thị.');
                        }
                    },
                ];
                // ✅ Đảm bảo luôn ghi đè giá trị order về null nếu không phải homepage
                $request->merge(['order' => null]);
            }
            $validated = $request->validate($rules, [
                'order.unique' => 'Vị trí này đã được sử dụng bởi banner khác',
                'required' => ':attribute không được để trống.',
                'string' => ':attribute phải là chuỗi.',
                'url' => ':attribute phải là URL hợp lệ.',
                'link.regex' => 'Link sản phẩm không hợp lệ. Định dạng đúng là /shop/products/slug (ví dụ: /shop/products/banh-mi-thit-nuong).',
                'max' => ':attribute không được vượt quá :max KB.',
                'date' => ':attribute phải là ngày hợp lệ.',
                'after' => ':attribute phải sau ngày bắt đầu.',
                'image' => ':attribute phải là hình ảnh.',
                'boolean' => ':attribute không hợp lệ.'
            ], [
                'image_path' => 'Hình ảnh tải lên',
                'image_link' => 'Đường dẫn ảnh',
                'link' => 'Link sản phẩm',
                'title' => 'Tiêu đề',
                'description' => 'Mô tả',
                'start_at' => 'Ngày bắt đầu',
                'end_at' => 'Ngày kết thúc',
                'is_active' => 'Trạng thái',
                'order' => 'Thứ tự hiển thị'
            ]);

            // CHỈ CHO PHÉP 1 TRONG 2
            $hasFile = $request->hasFile('image_path');
            $hasLink = $request->filled('image_link');

            if ($hasFile && $hasLink) {
                return back()->withErrors([
                    'image_path' => 'Chỉ được chọn một: ảnh tải lên hoặc đường dẫn ảnh.',
                    'image_link' => 'Chỉ được chọn một: ảnh tải lên hoặc đường dẫn ảnh.',
                ])->withInput();
            }

            if ($hasFile) {
                if ($banner->image_path && !filter_var($banner->image_path, FILTER_VALIDATE_URL)) {
                    Storage::disk('s3')->delete($banner->image_path);
                }
                $path = $request->file('image_path')->store('banners', 's3');
                $validated['image_path'] = $path;
                unset($validated['image_link']);
            } else if ($hasLink) {
                if ($banner->image_path && !filter_var($banner->image_path, FILTER_VALIDATE_URL)) {
                    Storage::disk('s3')->delete($banner->image_path);
                }

                $validated['image_path'] = $request->input('image_link');
                unset($validated['image_link']);
            } else {
                // Không có ảnh mới được chọn, giữ nguyên ảnh cũ
                unset($validated['image_path']);
                unset($validated['image_link']);
            }

            // Kiểm tra trùng thứ tự hiển thị nếu vị trí là homepage và có order
            if ($request->position === 'homepage' && isset($validated['order'])) {
                $existingBanner = Banner::where('order', $validated['order'])
                    ->where('position', 'homepage')
                    ->where('id', '!=', $id)
                    ->first();

                if ($existingBanner) {
                    // Có banner trùng thứ tự, hiển thị thông báo cho người dùng
                    return back()->withInput()->with('duplicate_order', [
                        'banner_id' => $existingBanner->id,
                        'banner_title' => $existingBanner->title,
                        'order' => $validated['order']
                    ])->with('toast', [
                        'type' => 'warning',
                        'title' => 'Cảnh báo',
                        'message' => 'Thứ tự hiển thị ' . $validated['order'] . ' đã được sử dụng bởi banner "' . $existingBanner->title . '". Vui lòng chọn thứ tự khác hoặc điều chỉnh thứ tự của banner hiện có.'
                    ]);
                }
            }

            // Nếu vị trí không phải homepage, đặt order thành null
            if ($request->position !== 'homepage') {
                $validated['order'] = null;
            }

            $banner->update($validated);

            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công',
                'message' => 'Banner đã được cập nhật thành công'
            ]);

            return redirect()->route('admin.banners.index');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error in BannerController@update: ' . $e->getMessage());
            session()->flash('toast', [
                'type' => 'error',
                'title' => 'Lỗi',
                'message' => 'Có lỗi xảy ra khi cập nhật banner: ' . $e->getMessage()
            ]);
            return back()->withInput();
        }
    }


    public function destroy(string $id)
    {
        try {
            $banner = Banner::findOrFail($id);

            if ($banner->image_path && !filter_var($banner->image_path, FILTER_VALIDATE_URL)) {
                // Chỉ xóa nếu là ảnh lưu trên S3
                if (Storage::disk('s3')->exists($banner->image_path)) {
                    Storage::disk('s3')->delete($banner->image_path);
                } else {
                    Log::warning("Không tìm thấy ảnh trên S3 để xóa: " . $banner->image_path);
                }
            }

            $banner->delete();

            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Thành công',
                'message' => 'Banner đã được xóa thành công'
            ]);

            return redirect()->route('admin.banners.index');
        } catch (\Exception $e) {
            Log::error('Error in BannerController@destroy: ' . $e->getMessage());

            session()->flash('toast', [
                'type' => 'error',
                'title' => 'Lỗi',
                'message' => 'Có lỗi xảy ra khi xóa banner: ' . $e->getMessage()
            ]);

            return redirect()->route('admin.banners.index');
        }
    }
    public function toggleStatus($id)
    {
        try {
            $banner = Banner::findOrFail($id);
            $banner->is_active = !$banner->is_active;
            $banner->save();

            return response()->json([
                'success' => true,
                'message' => $banner->is_active ? 'Đã kích hoạt banner thành công' : 'Đã vô hiệu hóa banner thành công',
                'is_active' => $banner->is_active
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkStatusUpdate(Request $request)
    {
        try {
            $request->validate([
                'ids' => 'required',
                'status' => 'required|boolean'
            ]);

            $idsInput = $request->input('ids');

            // Xử lý dữ liệu ids để đảm bảo là mảng
            if (is_string($idsInput)) {
                $ids = json_decode($idsInput, true);
                if (json_last_error() !== JSON_ERROR_NONE || !is_array($ids)) {
                    return back()->with('toast', [
                        'type' => 'error',
                        'title' => 'Lỗi',
                        'message' => 'Dữ liệu ids không hợp lệ, phải là một mảng'
                    ]);
                }
            } else {
                $ids = $idsInput;
            }
            if (!is_array($ids)) {
                return back()->with('toast', [
                    'type' => 'error',
                    'title' => 'Lỗi',
                    'message' => 'Dữ liệu ids không hợp lệ, phải là một mảng'
                ]);
            }

            $status = $request->input('status');

            $updated = Banner::whereIn('id', $ids)->update(['is_active' => $status]);

            if ($updated === 0) {
                return back()->with('toast', [
                    'type' => 'error',
                    'title' => 'Lỗi',
                    'message' => 'Không có banner nào được cập nhật'
                ]);
            }

            $statusText = $status ? 'kích hoạt' : 'vô hiệu hóa';
            return back()->with('toast', [
                'type' => 'success',
                'title' => 'Thành công',
                'message' => 'Đã ' . $statusText . ' ' . $updated . ' banner thành công'
            ]);
        } catch (\Exception $e) {
            return back()->with('toast', [
                'type' => 'error',
                'title' => 'Lỗi',
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
        }
    }
    public function searchProducts(Request $request)
    {
        $query = $request->get('q', '');
        $id = $request->get('id');
        $slug = $request->get('slug');
        $products = Product::query();
        
        if ($id) {
            $products->where('id', $id);
        } elseif ($slug) {
            $products->where('slug', $slug);
        } elseif ($query) {
            $products->where('name', 'like', '%' . $query . '%');
        } else {
            return response()->json([]);
        }
        
        return response()->json(
            $products->select('id', 'name', 'slug')
                ->limit(10)
                ->get()
        );
    }
}
