<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\VariantAttribute;
use App\Models\VariantValue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Branch;
use App\Models\Favorite;
use App\Models\DiscountCode;
use App\Models\UserDiscountCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Services\BranchService;
use App\Models\ReviewReport;
use App\Rules\ForbiddenWords;
use App\Notifications\Customer\ReviewLikedNotification;
use App\Notifications\Customer\ReviewReportedNotification;

class ProductController extends Controller
{
    protected $branchService;

    public function __construct(BranchService $branchService)
    {
        $this->branchService = $branchService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get selected branch ID from BranchService
        $currentBranch = $this->branchService->getCurrentBranch();
        $selectedBranchId = $currentBranch ? $currentBranch->id : null;
        
        // Override branch_id if provided in request (for AJAX)
        if ($request->has('branch_id') && $request->branch_id) {
            $selectedBranchId = $request->branch_id;
        }
        
        // Get filter parameters
        $searchTerm = $request->get('search', '');
        $sortBy = $request->get('sort', 'popular');
        $categoryFilter = $request->get('category', '');

        // Lấy tất cả categories để hiển thị filter và lazy load
        $categoriesQuery = Category::where('status', true);
        
        // Filter by category if specified
        if ($categoryFilter) {
            $categoriesQuery->where('id', $categoryFilter);
        }
        
        $categories = $categoriesQuery
            ->with(['products' => function($query) use ($selectedBranchId, $searchTerm, $sortBy) {
                $query->where('status', 'selling');
                
                // Apply search filter
                if ($searchTerm) {
                    $query->where(function($q) use ($searchTerm) {
                        $q->where('name', 'like', '%' . $searchTerm . '%')
                          ->orWhere('description', 'like', '%' . $searchTerm . '%');
                    });
                }
                
                // Apply sorting
                switch ($sortBy) {
                    case 'name-asc':
                        $query->orderBy('name', 'asc');
                        break;
                    case 'name-desc':
                        $query->orderBy('name', 'desc');
                        break;
                    case 'price-asc':
                        $query->orderBy('base_price', 'asc');
                        break;
                    case 'price-desc':
                        $query->orderBy('base_price', 'desc');
                        break;
                    case 'newest':
                        $query->orderBy('created_at', 'desc');
                        break;
                    case 'popular':
                    default:
                        $query->orderBy('favorite_count', 'desc')
                              ->orderBy('created_at', 'desc');
                        break;
                }
                
                $query->with([
                        'category',
                        'combos', // Thêm dòng này để eager load combos
                        'images' => function($q) { $q->orderBy('is_primary', 'desc'); },
                        'reviews' => function($q) { $q->with('user')->orderBy('created_at', 'desc'); },
                        'variants.branchStocks' => function($q) use ($selectedBranchId) {
                            if ($selectedBranchId) $q->where('branch_id', $selectedBranchId);
                        },
                        'variants.variantValues'
                    ]);
                
                // Filter sản phẩm theo stock nếu có branch được chọn
                // Không filter theo stock để hiển thị tất cả sản phẩm (kể cả hết hàng)
                if ($selectedBranchId) {
                    $query->whereHas('variants.branchStocks', function($q) use ($selectedBranchId) {
                        $q->where('branch_id', $selectedBranchId);
                    });
                }
                // Không phân trang
            }, 'combos' => function($query) use ($selectedBranchId, $searchTerm, $sortBy) {
                $query->where('status', 'selling')
                    ->where('active', true);
                
                // Apply search filter for combos
                if ($searchTerm) {
                    $query->where(function($q) use ($searchTerm) {
                        $q->where('name', 'like', '%' . $searchTerm . '%')
                          ->orWhere('description', 'like', '%' . $searchTerm . '%');
                    });
                }
                
                // Apply sorting for combos
                switch ($sortBy) {
                    case 'name-asc':
                        $query->orderBy('name', 'asc');
                        break;
                    case 'name-desc':
                        $query->orderBy('name', 'desc');
                        break;
                    case 'price-asc':
                        $query->orderBy('price', 'asc');
                        break;
                    case 'price-desc':
                        $query->orderBy('price', 'desc');
                        break;
                    case 'newest':
                        $query->orderBy('created_at', 'desc');
                        break;
                    case 'popular':
                    default:
                        $query->orderBy('created_at', 'desc');
                        break;
                }
                
                $query->with(['comboBranchStocks' => function($q) use ($selectedBranchId) {
                        if ($selectedBranchId) {
                            $q->where('branch_id', $selectedBranchId);
                        }
                    }]);
                
                // Không filter theo stock để hiển thị tất cả combo (kể cả hết hàng)
                if ($selectedBranchId) {
                    $query->whereHas('comboBranchStocks', function($q) use ($selectedBranchId) {
                        $q->where('branch_id', $selectedBranchId);
                    });
                }
            }])
            ->get();

        // Lấy danh sách mã giảm giá đang hoạt động (giữ nguyên logic cũ)
        $now = Carbon::now();
        $currentTime = $now->format('H:i:s');
        $activeDiscountCodesQuery = DiscountCode::where('is_active', true)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->where(function($query) use ($selectedBranchId) {
                if ($selectedBranchId) {
                    $query->whereDoesntHave('branches')
                        ->orWhereHas('branches', function($q) use ($selectedBranchId) {
                            $q->where('branches.id', $selectedBranchId);
                        });
                }
            });
        $activeDiscountCodesQuery->where(function($query) {
            $query->where('usage_type', 'public');
            if (Auth::check()) {
                $query->orWhere(function($q) {
                    $q->where('usage_type', 'personal')
                      ->whereHas('users', function($userQuery) {
                          $userQuery->where('user_id', Auth::id());
                      });
                });
            }
        });
        $activeDiscountCodes = $activeDiscountCodesQuery->with(['products' => function($query) {
                $query->with(['product', 'category']);
            }])
            ->get()
            ->filter(function($discountCode) use ($currentTime) {
                if ($discountCode->valid_from_time && $discountCode->valid_to_time) {
                    $from = Carbon::parse($discountCode->valid_from_time)->format('H:i:s');
                    $to = Carbon::parse($discountCode->valid_to_time)->format('H:i:s');
                    if ($from < $to) {
                        if (!($currentTime >= $from && $currentTime <= $to)) return false;
                    } else {
                        if (!($currentTime >= $from || $currentTime <= $to)) return false;
                    }
                }
                return true;
            });

        // Lấy favorites
        $favorites = [];
        if (Auth::check()) {
            $favorites = Favorite::where('user_id', Auth::id())
                ->pluck('product_id')
                ->toArray();
        } elseif ($request->session()->has('wishlist_items')) {
            $favorites = $request->session()->get('wishlist_items', []);
        }

        // Thêm thông tin cho từng product trong từng category
        foreach ($categories as $category) {
            foreach ($category->products as $product) {
                $product->average_rating = $product->reviews->avg('rating') ?? 0;
                $product->reviews_count = $product->reviews->count();
                $product->primary_image = $product->images->where('is_primary', true)->first() ?? $product->images->first();
                if ($product->primary_image) {
                    $product->primary_image->s3_url = \Illuminate\Support\Facades\Storage::disk('s3')->url($product->primary_image->img);
                }
                $product->is_favorite = in_array($product->id, $favorites);
                // Tính toán has_stock dựa trên branch hiện tại
                if ($selectedBranchId) {
                    $product->has_stock = $product->variants->contains(function($variant) use ($selectedBranchId) {
                        return $variant->branchStocks->contains(function($stock) use ($selectedBranchId) {
                            return $stock->branch_id == $selectedBranchId && $stock->stock_quantity > 0;
                        });
                    });
                } else {
                    $product->has_stock = $product->variants->contains(function($variant) {
                        return $variant->branchStocks->contains(function($stock) {
                            return $stock->stock_quantity > 0;
                        });
                    });
                }
                // Giá min/max
                $product->min_price = $product->base_price;
                $product->max_price = $product->base_price;
                if ($product->variants && $product->variants->count() > 0) {
                    $variantPrices = [];
                    foreach ($product->variants as $variant) {
                        $variantPrice = $product->base_price;
                        if ($variant->variantValues && $variant->variantValues->count() > 0) {
                            $variantPrice += $variant->variantValues->sum('price_adjustment');
                        }
                        $variantPrices[] = $variantPrice;
                    }
                    if (!empty($variantPrices)) {
                        $product->min_price = min($variantPrices);
                        $product->max_price = max($variantPrices);
                    }
                }
                // Discount codes
                $product->applicable_discount_codes = $activeDiscountCodes->filter(function($discountCode) use ($product) {
                    // Trường hợp áp dụng cho tất cả các mặt hàng
                    if (($discountCode->applicable_scope === 'all') || ($discountCode->applicable_items === 'all_items')) {
                        if ($discountCode->min_requirement_type && $discountCode->min_requirement_value > 0) {
                            if ($discountCode->min_requirement_type === 'order_amount') {
                                return true;
                            } elseif ($discountCode->min_requirement_type === 'product_price') {
                                if ($product->min_price < $discountCode->min_requirement_value) return false;
                            }
                        }
                        return true;
                    }
                    
                    $applies = false;
                    
                    // Trường hợp áp dụng cho tất cả sản phẩm
                    if ($discountCode->applicable_items === 'all_products') {
                        $applies = true;
                    }
                    // Trường hợp áp dụng cho tất cả danh mục
                    elseif ($discountCode->applicable_items === 'all_categories') {
                        $applies = true;
                    }
                    // Trường hợp áp dụng cho tất cả combo - không áp dụng cho sản phẩm thông thường
                    elseif ($discountCode->applicable_items === 'all_combos') {
                        $applies = false; // Đây là sản phẩm thông thường, không phải combo
                    }
                    // Trường hợp áp dụng cho sản phẩm cụ thể
                    elseif ($discountCode->applicable_items === 'specific_products') {
                        $specificProductIds = $discountCode->specificProducts()->pluck('product_id')->filter()->toArray();
                        if (in_array($product->id, $specificProductIds)) {
                            $applies = true;
                        }
                    }
                    // Trường hợp áp dụng cho danh mục cụ thể
                    elseif ($discountCode->applicable_items === 'specific_categories') {
                        $specificCategoryIds = $discountCode->specificCategories()->pluck('category_id')->filter()->toArray();
                        if (in_array($product->category_id, $specificCategoryIds)) {
                            $applies = true;
                        }
                    }
                    // Trường hợp áp dụng cho biến thể cụ thể
                    elseif ($discountCode->applicable_items === 'specific_variants') {
                        $specificVariantIds = $discountCode->specificVariants()->pluck('product_variant_id')->filter()->toArray();
                        if ($product->variants->whereIn('id', $specificVariantIds)->count() > 0) {
                            $applies = true;
                        }
                    }
                    // Trường hợp áp dụng cho combo cụ thể - không áp dụng cho sản phẩm thông thường
                    elseif ($discountCode->applicable_items === 'specific_combos') {
                        $applies = false; // Đây là sản phẩm thông thường, không phải combo
                    }
                    if ($applies && $discountCode->min_requirement_type === 'product_price' && $discountCode->min_requirement_value > 0) {
                        if ($product->min_price < $discountCode->min_requirement_value) return false;
                    }
                    return $applies;
                });
            }
            
            // Thêm thông tin cho từng combo trong category
            if ($category->combos) {
                foreach ($category->combos as $combo) {
                    // Thêm thông tin ảnh
                    if ($combo->image) {
                        $combo->image_url = \Storage::disk('s3')->url($combo->image);
                    } else {
                        $combo->image_url = asset('images/default-combo.png');
                    }
                    
                    // Tính toán has_stock cho combo
                    if ($selectedBranchId) {
                        $combo->has_stock = $combo->comboBranchStocks->where('branch_id', $selectedBranchId)->sum('quantity') > 0;
                    } else {
                        $combo->has_stock = $combo->comboBranchStocks->sum('quantity') > 0;
                    }
                    
                    // Tính phần trăm giảm giá nếu có
                    if ($combo->original_price && $combo->original_price > $combo->price) {
                        $combo->discount_percent = round((($combo->original_price - $combo->price) / $combo->original_price) * 100);
                    } else {
                        $combo->discount_percent = 0;
                    }
                    
                    // Áp dụng mã giảm giá cho combo
                    $combo->applicable_discount_codes = $activeDiscountCodes->filter(function($discountCode) use ($combo) {
                        // Trường hợp áp dụng cho tất cả các mặt hàng
                        if (($discountCode->applicable_scope === 'all') || ($discountCode->applicable_items === 'all_items')) {
                            if ($discountCode->min_requirement_type && $discountCode->min_requirement_value > 0) {
                                if ($discountCode->min_requirement_type === 'order_amount') {
                                    return true;
                                } elseif ($discountCode->min_requirement_type === 'product_price') {
                                    if ($combo->price < $discountCode->min_requirement_value) return false;
                                }
                            }
                            return true;
                        }
                        
                        $applies = false;
                        
                        // Trường hợp áp dụng cho tất cả combo
                        if ($discountCode->applicable_items === 'all_combos') {
                            $applies = true;
                        }
                        // Trường hợp áp dụng cho combo cụ thể
                        elseif ($discountCode->applicable_items === 'specific_combos') {
                            $specificComboIds = $discountCode->specificCombos()->pluck('combo_id')->filter()->toArray();
                            if (in_array($combo->id, $specificComboIds)) {
                                $applies = true;
                            }
                        }
                        
                        if ($applies && $discountCode->min_requirement_type === 'product_price' && $discountCode->min_requirement_value > 0) {
                            if ($combo->price < $discountCode->min_requirement_value) return false;
                        }
                        return $applies;
                    });
                }
            }
        }

        // Xử lý AJAX request
        if ($request->ajax() || $request->has('ajax')) {
            // Xử lý request cho single product card
            if ($request->has('ajax_single_product') && $request->has('product_id')) {
                $productId = $request->get('product_id');
                
                // Tìm product trong categories đã load
                $product = null;
                foreach ($categories as $category) {
                    $foundProduct = $category->products->where('id', $productId)->first();
                    if ($foundProduct) {
                        $product = $foundProduct;
                        break;
                    }
                }
                
                if ($product) {
                    // Render single product card
                    $html = view('customer.shop._product_card', compact('product'))->render();
                    return response()->json([
                        'success' => true,
                        'html' => $html
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Product not found'
                    ], 404);
                }
            }
            
            // Render partial view cho AJAX (toàn bộ products)
            $html = view('customer.shop._ajax_products', compact('categories', 'selectedBranchId'))->render();
            return response()->json([
                'success' => true,
                'html' => $html
            ]);
        }

        return view("customer.shop.index", compact('categories', 'selectedBranchId'));
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        // Get selected branch ID from BranchService
        $currentBranch = $this->branchService->getCurrentBranch();
        $selectedBranchId = $currentBranch ? $currentBranch->id : null;

        $product = Product::with([
            'category',
            'images' => function($query) {
                $query->orderBy('is_primary', 'desc');
            },
            'reviews' => function($query) { $query->with('user')->orderBy('created_at', 'desc'); },
            'variants.variantValues.attribute',
            'variants.branchStocks' => function($query) use ($selectedBranchId) {
                if ($selectedBranchId) {
                    $query->where('branch_id', $selectedBranchId);
                }
            },
            'toppings' => function($query) use ($selectedBranchId) {
                $query->where('active', true);

                if ($selectedBranchId) {
                    // Only include toppings that have stock at this branch
                    $query->whereHas('toppingStocks', function($q) use ($selectedBranchId) {
                        $q->where('branch_id', $selectedBranchId)
                          ->where('stock_quantity', '>', 0);
                    });
                }
            },
            'toppings.toppingStocks' => function($query) use ($selectedBranchId) {
                if ($selectedBranchId) {
                    $query->where('branch_id', $selectedBranchId);
                }
            }
        ])->where('slug', $slug)->firstOrFail();

        // Tính toán thông tin rating
        $product->average_rating = $product->reviews->avg('rating') ?? 0;
        $product->reviews_count = $product->reviews->count();

        // Add S3 URLs to all product images
        foreach ($product->images as $image) {
            $image->s3_url = \Illuminate\Support\Facades\Storage::disk('s3')->url($image->img);
        }

        // Check if the product has stock
        if ($selectedBranchId) {
            // Find any variant with stock > 0
            $product->has_stock = $product->variants->contains(function($variant) use ($selectedBranchId) {
                return $variant->branchStocks->contains(function($stock) use ($selectedBranchId) {
                    return $stock->branch_id == $selectedBranchId && $stock->stock_quantity > 0;
                });
            });
        } else {
            // If no branch selected, check for any stock
            $product->has_stock = $product->variants->contains(function($variant) {
                return $variant->branchStocks->contains(function($stock) {
                    return $stock->stock_quantity > 0;
                });
            });
        }

        // Lấy các mã giảm giá áp dụng cho sản phẩm này
        $now = Carbon::now();
        $currentDayOfWeek = $now->dayOfWeekIso; // 1 (Monday) - 7 (Sunday)
        $currentTime = $now->format('H:i:s');
        $activeDiscountCodesQuery = DiscountCode::where('is_active', true)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->where(function($query) use ($selectedBranchId) {
                // Nếu có branch được chọn, chỉ lấy mã giảm giá áp dụng cho branch đó hoặc tất cả branch
                if ($selectedBranchId) {
                    $query->whereDoesntHave('branches') // Không có branch nào = áp dụng cho tất cả
                        ->orWhereHas('branches', function($q) use ($selectedBranchId) {
                            $q->where('branches.id', $selectedBranchId);
                        });
                }
            });

        // Chỉ lấy mã giảm giá công khai hoặc mã riêng tư được gán cho người dùng hiện tại
        $activeDiscountCodesQuery->where(function($query) {
            $query->where('usage_type', 'public');

            if (Auth::check()) {
                $query->orWhere(function($q) {
                    $q->where('usage_type', 'personal')
                      ->whereHas('users', function($userQuery) {
                          $userQuery->where('user_id', Auth::id());
                      });
                });
            }
        });

        $activeDiscountCodes = $activeDiscountCodesQuery->with(['products' => function($query) {
                $query->with(['product', 'category']);
            }])
            ->get()
            // Filter theo ngày trong tuần và giờ hợp lệ (đồng nhất với index)
            ->filter(function($discountCode) use ($currentTime) {
                // Kiểm tra giờ hợp lệ
                if ($discountCode->valid_from_time && $discountCode->valid_to_time) {
                    $from = Carbon::parse($discountCode->valid_from_time)->format('H:i:s');
                    $to = Carbon::parse($discountCode->valid_to_time)->format('H:i:s');
                    if ($from < $to) {
                        // Khoảng thời gian trong cùng 1 ngày
                        if (!($currentTime >= $from && $currentTime <= $to)) {
                            return false;
                        }
                    } else {
                        // Khoảng thời gian qua đêm (ví dụ: 22:00 - 02:00)
                        if (!($currentTime >= $from || $currentTime <= $to)) {
                            return false;
                        }
                    }
                }
                return true;
            });

        // Log discount codes for debugging
        \Illuminate\Support\Facades\Log::debug('Active Discount Codes in product detail page:', [
            'product_id' => $product->id,
            'total_codes' => $activeDiscountCodes->count(),
            'public_codes' => $activeDiscountCodes->where('usage_type', 'public')->count(),
            'personal_codes' => $activeDiscountCodes->where('usage_type', 'personal')->count(),
            'codes' => $activeDiscountCodes->map(function($code) {
                return [
                    'id' => $code->id,
                    'code' => $code->code,
                    'usage_type' => $code->usage_type,
                    'applicable_scope' => $code->applicable_scope,
                ];
            })
        ]);

        $product->applicable_discount_codes = $activeDiscountCodes->filter(function($discountCode) use ($product) {
            // Chỉ áp dụng cho tất cả sản phẩm nếu applicable_items === 'all_items' (và giữ applicable_scope === 'all' nếu có)
            if (($discountCode->applicable_scope === 'all') || ($discountCode->applicable_items === 'all_items')) {
                // Kiểm tra điều kiện tối thiểu nếu có
                if ($discountCode->min_requirement_type && $discountCode->min_requirement_value > 0) {
                    if ($discountCode->min_requirement_type === 'order_amount') {
                        // Không kiểm tra ở đây, chỉ kiểm tra khi checkout
                        return true;
                    } elseif ($discountCode->min_requirement_type === 'product_price') {
                        // Nếu giá sản phẩm nhỏ hơn min_requirement_value thì không áp dụng
                        if ($product->base_price < $discountCode->min_requirement_value) {
                            return false;
                        }
                    }
                }
                return true;
            }
            // Kiểm tra áp dụng cụ thể cho sản phẩm
            $applies = $discountCode->products->contains(function($discountProduct) use ($product) {
                if ($discountProduct->product_id === $product->id) {
                    return true; // Áp dụng trực tiếp cho sản phẩm này
                }
                if ($discountProduct->category_id === $product->category_id) {
                    return true; // Áp dụng cho danh mục của sản phẩm này
                }
                return false;
            });
            if ($applies && $discountCode->min_requirement_type === 'product_price' && $discountCode->min_requirement_value > 0) {
                if ($product->base_price < $discountCode->min_requirement_value) {
                    return false;
                }
            }
            return $applies;
        });

        // Lấy các variant attributes và values
        $variantAttributes = VariantAttribute::with([
            'values' => function($query) use ($product) {
                $query->whereHas('productVariants', function($q) use ($product) {
                    $q->where('product_id', $product->id);
                });
            }
        ])
        ->whereHas('values.productVariants', function($query) use ($product) {
            $query->where('product_id', $product->id);
        })
        ->get();

        // Lấy các sản phẩm liên quan cùng danh mục
        $relatedProductsQuery = Product::with([
            'category',
            'images' => function($query) {
                $query->orderBy('is_primary', 'desc');
            },
            'reviews' => function($query) { $query; },
            'variants.variantValues'
        ])
        ->where('category_id', $product->category_id)
        ->where('id', '!=', $product->id)
        ->where('status', 'selling');

        // Filter related products by selected branch
        if ($selectedBranchId) {
            $relatedProductsQuery->whereHas('variants', function($q) use ($selectedBranchId) {
                $q->whereHas('branchStocks', function($q2) use ($selectedBranchId) {
                    $q2->where('branch_id', $selectedBranchId)
                       ->where('stock_quantity', '>', 0);
                });
            });
        }

        $relatedProducts = $relatedProductsQuery->limit(5)->get();

        // Thêm thông tin rating cho related products
        $relatedProducts->transform(function ($relatedProduct) use ($activeDiscountCodes) {
            $relatedProduct->average_rating = $relatedProduct->reviews->avg('rating') ?? 0;
            $relatedProduct->reviews_count = $relatedProduct->reviews->count();

            // Add primary image and S3 URL
            $relatedProduct->primary_image = $relatedProduct->images->where('is_primary', true)->first()
                                    ?? $relatedProduct->images->first();

            if ($relatedProduct->primary_image) {
                $relatedProduct->primary_image->s3_url = \Illuminate\Support\Facades\Storage::disk('s3')->url($relatedProduct->primary_image->img);
            }

            // Tính giá thấp nhất và cao nhất bao gồm cả biến thể
            $relatedProduct->min_price = $relatedProduct->base_price;
            $relatedProduct->max_price = $relatedProduct->base_price;

            if ($relatedProduct->variants && $relatedProduct->variants->count() > 0) {
                $variantPrices = [];

                foreach ($relatedProduct->variants as $variant) {
                    // Tính giá của biến thể này
                    $variantPrice = $relatedProduct->base_price;
                    if ($variant->variantValues && $variant->variantValues->count() > 0) {
                        $variantPrice += $variant->variantValues->sum('price_adjustment');
                    }
                    $variantPrices[] = $variantPrice;
                }

                if (!empty($variantPrices)) {
                    $relatedProduct->min_price = min($variantPrices);
                    $relatedProduct->max_price = max($variantPrices);
                }
            }

            // Lấy các mã giảm giá áp dụng cho sản phẩm liên quan
            $relatedProduct->applicable_discount_codes = $activeDiscountCodes->filter(function($discountCode) use ($relatedProduct) {
                // Chỉ áp dụng cho tất cả sản phẩm nếu applicable_items === 'all_items' (và giữ applicable_scope === 'all' nếu có)
                if ($discountCode->applicable_items === 'all_items') {
                    return true;
                }
                // Kiểm tra áp dụng cụ thể cho sản phẩm
                return $discountCode->products->contains(function($discountProduct) use ($relatedProduct) {
                    if ($discountProduct->product_id === $relatedProduct->id) {
                        return true; // Áp dụng trực tiếp cho sản phẩm này
                    }
                    if ($discountProduct->category_id === $relatedProduct->category_id) {
                        return true; // Áp dụng cho danh mục của sản phẩm này
                    }
                    return false;
                });
            });

            return $relatedProduct;
        });

        // Always get all active branches for the modal to display all options
        $branches = Branch::where('active', true)->get();

        // Đánh dấu sản phẩm đã yêu thích cho user hiện tại
        $product->is_favorite = false;
        if (Auth::check()) {
            $product->is_favorite = Favorite::where('user_id', Auth::id())
                ->where('product_id', $product->id)
                ->exists();
        }

        // Kiểm tra user đã mua sản phẩm này chưa (để hiển thị form review)
        $hasPurchased = false;
        if (Auth::check() && $selectedBranchId) {
            $hasPurchased = \App\Models\Order::where('customer_id', Auth::id())
                ->whereIn('status', ['delivered', 'item_received'])
                ->where('branch_id', $selectedBranchId)
                ->whereHas('orderItems.productVariant', function($q) use ($product) {
                    $q->where('product_id', $product->id);
                })
                ->exists();
        }

        // Tính giá thấp nhất và cao nhất bao gồm cả biến thể (đồng nhất với index)
        $product->min_price = $product->base_price;
        $product->max_price = $product->base_price;

        if ($product->variants && $product->variants->count() > 0) {
            $variantPrices = [];

            foreach ($product->variants as $variant) {
                // Tính giá của biến thể này
                $variantPrice = $product->base_price;
                if ($variant->variantValues && $variant->variantValues->count() > 0) {
                    $variantPrice += $variant->variantValues->sum('price_adjustment');
                }
                $variantPrices[] = $variantPrice;
            }

            if (!empty($variantPrices)) {
                $product->min_price = min($variantPrices);
                $product->max_price = max($variantPrices);
            }
        }

        // DEBUG: Log variant prices to find discrepancy
        \Illuminate\Support\Facades\Log::debug('ProductController@show - Variant Prices for Product ID ' . $product->id, [
            'variant_prices' => $variantPrices ?? [],
            'calculated_min_price' => $product->min_price,
            'base_price' => $product->base_price
        ]);

        // Debug log để kiểm tra giá trị
        \Illuminate\Support\Facades\Log::debug('Product price calculation in show method:', [
            'product_id' => $product->id,
            'base_price' => $product->base_price,
            'min_price' => $product->min_price,
            'max_price' => $product->max_price,
            'variant_prices' => $variantPrices ?? [],
            'applicable_discount_codes_count' => $product->applicable_discount_codes->count()
        ]);

                return view('customer.shop.show', compact(
            'product',
            'variantAttributes',
            'relatedProducts',
            'branches',
            'hasPurchased' // thêm biến này
        ));
    }
    public function showComboDetail(Request $request, $slug)
{
    // Lấy branch hiện tại nếu có
    $currentBranch = $this->branchService->getCurrentBranch();
    $selectedBranchId = $currentBranch ? $currentBranch->id : null;

    // Lấy combo và các quan hệ cần thiết
    $combo = \App\Models\Combo::with([
        'comboItems.productVariant.product.images',
        'comboItems.productVariant.variantValues.attribute',
        'category',
        'comboBranchStocks' => function($query) use ($selectedBranchId) {
            if ($selectedBranchId) {
                $query->where('branch_id', $selectedBranchId);
            }
        }
    ])->where('slug', $slug)->firstOrFail();

    // Lấy tồn kho tại chi nhánh hiện tại (nếu có)
    $branchStocks = $combo->comboBranchStocks;

    // Tính trạng thái còn hàng cho combo ở branch hiện tại
    $combo->has_stock = $branchStocks->sum('quantity') > 0;

    // Chuẩn bị dữ liệu sản phẩm trong combo
    $items = $combo->comboItems->map(function($item) {
        $variant = $item->productVariant;
        $product = $variant->product;
        
        // Xử lý ảnh sản phẩm - tìm ảnh chính hoặc ảnh đầu tiên
        $primaryImage = $product->images->where('is_primary', true)->first() ?? $product->images->first();
        $imageUrl = null;
        if ($primaryImage && $primaryImage->img) {
            $imageUrl = Storage::disk('s3')->url($primaryImage->img);
        }
        
        return [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_ingredients' => $product->ingredients, // Lấy thành phần sản phẩm
            'variant_id' => $variant->id,
            'variant_name' => $variant->variant_description ?? null,
            'quantity' => $item->quantity,
            'image' => $imageUrl,
            'base_price' => $product->base_price,
            'variant_price' => $variant->price,
            'total_price' => $variant->price * $item->quantity,
            'variant_values' => $variant->variantValues->map(function($v) {
                return [
                    'attribute' => $v->attribute->name ?? null,
                    'value' => $v->value,
                    'price_adjustment' => $v->price_adjustment,
                ];
            }),
        ];
    });

    // Lấy các sản phẩm cùng danh mục với combo (trừ các sản phẩm đã nằm trong combo)
    $productIdsInCombo = $items->pluck('product_id')->all();
    $relatedProducts = \App\Models\Product::where('category_id', $combo->category_id)
        ->whereNotIn('id', $productIdsInCombo)
        ->where('status', 'selling')
        ->limit(6)
        ->get();

    // Đảm bảo category đã eager load combos trạng thái 'selling'
    if ($combo->relationLoaded('category') && $combo->category) {
        $combo->category->setRelation('combos', $combo->category->combos->where('status', 'selling'));
    }

    // Lấy reviews cho combo
    $reviews = \App\Models\ProductReview::with(['user', 'replies.user'])
        ->where('combo_id', $combo->id)
        ->orderBy('review_date', 'desc')
        ->get();

    // Tính toán rating trung bình
    $combo->average_rating = $reviews->avg('rating') ?? 0;
    $combo->reviews_count = $reviews->count();

    // Kiểm tra user đã mua combo này chưa (để hiển thị form review)
    $user = auth()->user();
    $hasPurchased = false;
    $canReview = false;
    if ($user) {
        $hasPurchased = \App\Models\Order::where('customer_id', $user->id)
            ->whereIn('status', ['delivered', 'item_received'])
            ->whereHas('orderItems', function($q) use ($combo) {
                $q->where('combo_id', $combo->id);
            })
            ->exists();
        
        if ($hasPurchased) {
            // Kiểm tra đã review chưa
            $existingReview = \App\Models\ProductReview::where('user_id', $user->id)
                ->where('combo_id', $combo->id)
                ->first();
            $canReview = !$existingReview;
        }
    }

    return view('customer.shop.show-combo', [
        'combo' => $combo,
        'items' => $items,
        'branchStocks' => $branchStocks,
        'selectedBranch' => $currentBranch,
        'relatedProducts' => $relatedProducts,
        'reviews' => $reviews,
        'hasPurchased' => $hasPurchased,
        'canReview' => $canReview,
    ]);
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Toggle favorite status for a product (AJAX/API)
     */
    public function toggleFavorite(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Bạn cần đăng nhập để sử dụng chức năng này.'], 401);
        }

        $productId = $request->input('product_id');
        $favorite = $user->favorites()->where('product_id', $productId)->first();
        $product = \App\Models\Product::find($productId);

        if ($favorite) {
            $favorite->delete();
            $isFavorite = false;
            // Giảm favorite_count
            if ($product && $product->favorite_count > 0) {
                $product->decrement('favorite_count');
            }
        } else {
            $user->favorites()->create(['product_id' => $productId]);
            $isFavorite = true;
            // Tăng favorite_count
            if ($product) {
                $product->increment('favorite_count');
            }
        }

        // Optionally: broadcast event here for real-time update
        // event(new FavoriteUpdated($user->id, $productId, $isFavorite));

        return response()->json([
            'success' => true,
            'is_favorite' => $isFavorite,
        ]);
    }

    public function getApplicableDiscounts(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'integer|exists:products,id',
            'branch_id' => 'nullable|integer|exists:branches,id'
        ]);

        $productIds = $request->input('product_ids');
        $selectedBranchId = $request->input('branch_id');

        // Fallback to session-based service if branch_id is not provided in the request
        if (!$selectedBranchId) {
            $currentBranch = $this->branchService->getCurrentBranch();
            $selectedBranchId = $currentBranch ? $currentBranch->id : null;
        }

        // 1. Get all active discount codes
        $now = Carbon::now();
        $activeDiscountCodesQuery = DiscountCode::where('is_active', true)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->where(function ($query) use ($selectedBranchId) {
                if ($selectedBranchId) {
                    $query->whereDoesntHave('branches')
                        ->orWhereHas('branches', function ($q) use ($selectedBranchId) {
                            $q->where('branches.id', $selectedBranchId);
                        });
                }
            });

        $activeDiscountCodesQuery->where(function ($query) {
            $query->where('usage_type', 'public');
            if (Auth::check()) {
                $query->orWhere(function ($q) {
                    $q->where('usage_type', 'personal')
                        ->whereHas('users', function ($userQuery) {
                            $userQuery->where('user_id', Auth::id());
                        });
                });
            }
        });

        $allActiveCodesFromDB = $activeDiscountCodesQuery->with(['products:id,product_id,category_id'])->get();

        // Filter by valid time, just like the index() method does.
        $currentTime = $now->format('H:i:s');
        $activeDiscountCodes = $allActiveCodesFromDB->filter(function($discountCode) use ($currentTime) {
            if ($discountCode->valid_from_time && $discountCode->valid_to_time) {
                $from = Carbon::parse($discountCode->valid_from_time)->format('H:i:s');
                $to = Carbon::parse($discountCode->valid_to_time)->format('H:i:s');
                if ($from < $to) {
                    // Same day time range
                    if (!($currentTime >= $from && $currentTime <= $to)) {
                        return false;
                    }
                } else {
                    // Overnight time range (e.g., 22:00 - 02:00)
                    if (!($currentTime >= $from || $currentTime <= $to)) {
                        return false;
                    }
                }
            }
            return true;
        });

        // 2. Get products with relations needed for price calculation
        $products = Product::with('variants.variantValues', 'category:id,name')
            ->whereIn('id', $productIds)
            ->get();

        // 3. Prepare the response
        $response = [];

        foreach ($products as $product) {
            // Calculate min_price and max_price, to match the logic from the index page perfectly
            $product->min_price = $product->base_price;
            $product->max_price = $product->base_price;

            if ($product->variants && $product->variants->count() > 0) {
                $variantPrices = [];
                foreach ($product->variants as $variant) {
                    $variantPrice = $product->base_price;
                    if ($variant->variantValues && $variant->variantValues->count() > 0) {
                        $variantPrice += $variant->variantValues->sum('price_adjustment');
                    }
                    $variantPrices[] = $variantPrice;
                }
                if (!empty($variantPrices)) {
                    $product->min_price = min($variantPrices);
                    $product->max_price = max($variantPrices);
                }
            }

            // This filtering logic is copied EXACTLY from the index method to ensure consistency
            $applicableCodes = $activeDiscountCodes->filter(function($discountCode) use ($product) {
                if (($discountCode->applicable_scope === 'all') || ($discountCode->applicable_items === 'all_items')) {
                    if ($discountCode->min_requirement_type && $discountCode->min_requirement_value > 0) {
                        if ($discountCode->min_requirement_type === 'order_amount') {
                            return true;
                        } elseif ($discountCode->min_requirement_type === 'product_price') {
                            if ($product->min_price < $discountCode->min_requirement_value) {
                                return false;
                            }
                        }
                    }
                    return true;
                }

                $applies = $discountCode->products->contains(function($discountProduct) use ($product) {
                    if ($discountProduct->product_id === $product->id) return true;
                    if ($discountProduct->category_id === $product->category_id) return true;
                    return false;
                });

                if ($applies && $discountCode->min_requirement_type === 'product_price' && $discountCode->min_requirement_value > 0) {
                    if ($product->min_price < $discountCode->min_requirement_value) {
                        return false;
                    }
                }

                return $applies;
            });

            $response[$product->id] = [];
            foreach ($applicableCodes as $code) {
                $response[$product->id][] = [
                    'code' => $code->code,
                    'name' => $code->name,
                    'discount_type' => $code->discount_type,
                    'discount_value' => $code->discount_value,
                    'min_requirement_type' => $code->min_requirement_type,
                    'min_requirement_value' => $code->min_requirement_value
                ];
            }
        }

        return response()->json($response);
    }

    public function submitReview(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => ['nullable','string','max:2000', new ForbiddenWords],
            'review_image' => 'nullable|image|max:2048',
            'type' => 'required|in:product,combo',
            'branch_id' => 'required|exists:branches,id',
        ], [
            'rating.required' => 'Vui lòng chọn số sao đánh giá.',
            'rating.integer' => 'Số sao phải là số nguyên.',
            'rating.min' => 'Số sao tối thiểu là 1.',
            'rating.max' => 'Số sao tối đa là 5.',
            'review.string' => 'Nội dung đánh giá phải là chuỗi ký tự.',
            'review.max' => 'Nội dung đánh giá không được vượt quá 2000 ký tự.',
            'review_image.image' => 'File phải là hình ảnh.',
            'review_image.max' => 'Kích thước hình ảnh không được vượt quá 2MB.',
            'type.required' => 'Loại đánh giá là bắt buộc.',
            'type.in' => 'Loại đánh giá không hợp lệ.',
            'branch_id.required' => 'Chi nhánh là bắt buộc.',
            'branch_id.exists' => 'Chi nhánh không tồn tại.',
        ]);

        $user = $request->user();
        $type = $request->input('type');
        $branchId = $request->input('branch_id');
        $item = null;
        $order = null;
        $itemType = '';

        if ($type === 'product') {
            $item = Product::findOrFail($id);
            $itemType = 'sản phẩm';
            
            // Kiểm tra user đã mua sản phẩm này ở chi nhánh này chưa
            $order = \App\Models\Order::where('customer_id', $user->id)
                ->whereIn('status', ['delivered', 'item_received'])
                ->where('branch_id', $branchId) // Thêm điều kiện chi nhánh
                ->whereHas('orderItems.productVariant', function($q) use ($id) {
                    $q->where('product_id', $id);
                })
                ->orderByDesc('order_date')
                ->first();
        } else {
            $item = \App\Models\Combo::findOrFail($id);
            $itemType = 'combo';
            
            // Kiểm tra user đã mua combo này ở chi nhánh này chưa
            $order = \App\Models\Order::where('customer_id', $user->id)
                ->whereIn('status', ['delivered', 'item_received'])
                ->where('branch_id', $branchId) // Thêm điều kiện chi nhánh
                ->whereHas('orderItems', function($q) use ($id) {
                    $q->where('combo_id', $id);
                })
                ->orderByDesc('order_date')
                ->first();
        }

        if (!$order) {
            if ($request->expectsJson()) {
                return response()->json(['message' => "Bạn chỉ có thể đánh giá {$itemType} đã mua tại chi nhánh này!"], 403);
            }
            return redirect()->back()->with('error', "Bạn chỉ có thể đánh giá {$itemType} đã mua tại chi nhánh này!");
        }

        // Kiểm tra xem user đã review item này ở chi nhánh này chưa
        $existingReview = \App\Models\ProductReview::where('user_id', $user->id)
            ->where('order_id', $order->id)
            ->where('branch_id', $branchId); // Thêm điều kiện chi nhánh
        
        if ($type === 'product') {
            $existingReview->where('product_id', $item->id);
        } else {
            $existingReview->where('combo_id', $item->id);
        }
        
        $existingReview = $existingReview->first();
        
        if ($existingReview) {
            if ($request->expectsJson()) {
                return response()->json(['message' => "Bạn đã đánh giá {$itemType} này tại chi nhánh này rồi!"], 409);
            }
            return redirect()->back()->with('error', "Bạn đã đánh giá {$itemType} này tại chi nhánh này rồi!");
        }

        $review = new \App\Models\ProductReview();
        $review->user_id = $user->id;
        $review->order_id = $order->id;
        $review->branch_id = $request->input('branch_id');
        $review->rating = $request->input('rating');
        $review->review = $request->input('review');
        $review->review_date = now();
        $review->is_verified_purchase = true;
        $review->helpful_count = 0;
        $review->report_count = 0;
        $review->is_featured = false;

        // Set product_id hoặc combo_id tùy theo type
        if ($type === 'product') {
            $review->product_id = $item->id;
        } else {
            $review->combo_id = $item->id;
        }

        if ($request->hasFile('review_image')) {
            // Nếu có ảnh cũ thì xóa khỏi S3
            if ($review->review_image) {
                \Storage::disk('s3')->delete($review->review_image);
            }
            $path = $request->file('review_image')->store('reviews', 's3');
            $review->review_image = $path;
        }

        $review->save();

        // Broadcast event for real-time review update
        event(new \App\Events\Customer\ReviewCreated($review));

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Đánh giá của bạn đã được gửi!']);
        }
        
        // Redirect về trang tương ứng
        if ($type === 'product') {
            return redirect()->route('products.show', $item->id)->with('success', 'Đánh giá của bạn đã được gửi!');
        } else {
            return redirect()->route('combos.show', $item->id)->with('success', 'Đánh giá của bạn đã được gửi!');
        }
    }

    public function destroyReview(Request $request, $id)
    {
        $review = \App\Models\ProductReview::findOrFail($id);
        $user = $request->user();
        if ($review->user_id !== $user->id) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Bạn không có quyền xóa bình luận này!'], 403);
            }
            return back()->with('error', 'Bạn không có quyền xóa bình luận này!');
        }
        $review->delete();
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Xóa bình luận thành công!']);
        }
        return back()->with('success', 'Xóa bình luận thành công!');
    }

    public function markHelpful($id)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Bạn cần đăng nhập!'], 401);
        }

        $review = \App\Models\ProductReview::findOrFail($id);

        // Kiểm tra nếu user đã bấm rồi thì không cho bấm nữa
        $already = \App\Models\ReviewHelpful::where('user_id', $user->id)->where('review_id', $review->id)->exists();
        if ($already) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã bấm hữu ích!',
                'helpful_count' => $review->helpful_count,
                'review_id' => $review->id
            ]);
        }

        // Tạo record mới
        \App\Models\ReviewHelpful::create([
            'user_id' => $user->id,
            'review_id' => $review->id
        ]);
        $review->helpful_count = $review->helpful_count + 1;
        $review->save();

        // Gửi thông báo đến người viết review
        if ($review->user_id !== $user->id) { // Chỉ gửi thông báo nếu người thích không phải là người viết review
            $review->user->notify(new ReviewLikedNotification($review, $user));
        }

        return response()->json([
            'success' => true,
            'helpful_count' => $review->helpful_count,
            'review_id' => $review->id
        ]);
    }

    public function unmarkHelpful($id)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Bạn cần đăng nhập!'], 401);
        }
        $review = \App\Models\ProductReview::findOrFail($id);
        $helpful = \App\Models\ReviewHelpful::where('user_id', $user->id)->where('review_id', $review->id)->first();
        if (!$helpful) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chưa bấm hữu ích!',
                'helpful_count' => $review->helpful_count,
                'review_id' => $review->id
            ]);
        }
        $helpful->delete();
        if ($review->helpful_count > 0) {
            $review->helpful_count = $review->helpful_count - 1;
            $review->save();
        }
        return response()->json([
            'success' => true,
            'helpful_count' => $review->helpful_count,
            'review_id' => $review->id
        ]);
    }

    /**
     * Báo cáo review sản phẩm
     */
    public function reportReview(Request $request, $id)
    {
        $user = $request->user();
        $review = \App\Models\ProductReview::findOrFail($id);

        // Kiểm tra user đã mua sản phẩm hoặc combo này chưa
        $hasPurchased = false;
        if ($review->product_id) {
            $hasPurchased = \App\Models\Order::where('customer_id', $user->id)
                ->whereIn('status', ['delivered', 'item_received'])
                ->whereHas('orderItems.productVariant', function($q) use ($review) {
                    $q->where('product_id', $review->product_id);
                })
                ->exists();
        } elseif ($review->combo_id) {
            $hasPurchased = \App\Models\Order::where('customer_id', $user->id)
                ->whereIn('status', ['delivered', 'item_received'])
                ->whereHas('orderItems', function($q) use ($review) {
                    $q->where('combo_id', $review->combo_id);
                })
                ->exists();
        }
        if (!$hasPurchased) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chỉ có thể báo cáo đánh giá của sản phẩm đã mua!'
            ], 403);
        }

        // Validate lý do mới
        $request->validate([
            'reason_type' => 'required|string|max:32',
            'reason_detail' => 'nullable|string|max:1000',
        ]);

        // Kiểm tra user đã báo cáo review này chưa
        $already = \App\Models\ReviewReport::where('user_id', $user->id)->where('review_id', $review->id)->exists();
        if ($already) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã báo cáo đánh giá này trước đó!'
            ], 409);
        }

        // Lưu báo cáo
        \App\Models\ReviewReport::create([
            'user_id' => $user->id,
            'review_id' => $review->id,
            'reason_type' => $request->input('reason_type'),
            'reason_detail' => $request->input('reason_detail'),
        ]);

        // Tăng report_count cho review (nếu có trường này)
        if (isset($review->report_count)) {
            $review->report_count = $review->report_count + 1;
            $review->save();
        }

        // Gửi thông báo đến người viết review
        if ($review->user_id !== $user->id) { // Chỉ gửi thông báo nếu người báo cáo không phải là người viết review
            $review->user->notify(new ReviewReportedNotification($review, $user, $request->input('reason_type')));
        }

        return response()->json([
            'success' => true,
            'message' => 'Báo cáo của bạn đã được ghi nhận. Cảm ơn bạn đã phản hồi!'
        ]);
    }


}

