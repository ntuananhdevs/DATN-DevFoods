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

        // Lấy tất cả categories để hiển thị filter
        $categories = Category::where('status', true)
            ->withCount(['products' => function($query) {
                $query->where('status', 'selling');
            }])
            ->get();

        // Query cơ bản cho products
        $query = Product::with([
            'category',
            'images' => function($query) {
                $query->orderBy('is_primary', 'desc');
            },
            'reviews' => function($query) {
                $query->where('approved', true);
            },
            'variants.branchStocks' => function($query) use ($selectedBranchId) {
                if ($selectedBranchId) {
                    $query->where('branch_id', $selectedBranchId);
                }
            },
            'variants.variantValues'
        ])
        ->where('status', 'selling');

        // Filter by branch but show all products (including out of stock)
        if ($selectedBranchId) {
            $query->whereHas('variants.branchStocks', function($query) use ($selectedBranchId) {
                $query->where('branch_id', $selectedBranchId);
            });
        }

        // Filter theo category nếu có
        $selectedCategoryIds = [];
        if ($request->has('category') && $request->category != '') {
            $catParam = $request->category;
            if (strpos($catParam, ',') !== false) {
                $selectedCategoryIds = array_filter(explode(',', $catParam));
                $query->whereIn('category_id', $selectedCategoryIds);
            } else {
                $selectedCategoryIds = [$catParam];
                $query->where('category_id', $catParam);
            }
        }

        // Search theo nhiều trường liên quan đến sản phẩm
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('sku', 'like', "%$search%")
                  ->orWhere('short_description', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%")
                  ->orWhereRaw("JSON_EXTRACT(ingredients, '$[*]') like ?", ["%$search%"])
                  ->orWhereRaw("CAST(base_price AS CHAR) like ?", ["%$search%"])
                  ->orWhereRaw("CAST(preparation_time AS CHAR) like ?", ["%$search%"]);
            });
        }

        // Sắp xếp
        $sortBy = $request->get('sort', 'created_at');
        switch ($sortBy) {
            case 'price-asc':
                $query->orderBy('base_price', 'asc');
                break;
            case 'price-desc':
                $query->orderBy('base_price', 'desc');
                break;
            case 'name-asc':
                $query->orderBy('name', 'asc');
                break;
            case 'popular':
                $query->withCount('reviews')
                      ->orderBy('reviews_count', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(15);

        // Lấy danh sách mã giảm giá đang hoạt động
        $now = Carbon::now();
        $currentDayOfWeek = $now->dayOfWeekIso;
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
        \Illuminate\Support\Facades\Log::debug('Active Discount Codes in index page:', [
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

        // Obtener favoritos del usuario
        $favorites = [];
        if (Auth::check()) {
            // Si el usuario está autenticado, obtener de la base de datos
            $favorites = Favorite::where('user_id', Auth::id())
                ->pluck('product_id')
                ->toArray();
        } elseif ($request->session()->has('wishlist_items')) {
            // Si no está autenticado pero tiene favoritos en sesión
            $favorites = $request->session()->get('wishlist_items', []);
        }

        // Thêm thông tin rating trung bình cho mỗi sản phẩm
        $products->getCollection()->transform(function ($product) use ($favorites, $selectedBranchId, $activeDiscountCodes) {
            $product->average_rating = $product->reviews->avg('rating') ?? 0;
            $product->reviews_count = $product->reviews->count();
            $product->primary_image = $product->images->where('is_primary', true)->first()
                                    ?? $product->images->first();

            // Transform image URL to S3 URL if using S3
            if ($product->primary_image) {
                $product->primary_image->s3_url = asset('storage/' . $product->primary_image->img);
            }

            // Marcar como favorito si está en la lista
            $product->is_favorite = in_array($product->id, $favorites);

            // Check if the product has stock
            if ($selectedBranchId) {
                // Find any variant with stock > 0
                $product->has_stock = $product->variants->contains(function($variant) use ($selectedBranchId) {
                    return $variant->branchStocks->contains(function($stock) use ($selectedBranchId) {
                        return $stock->branch_id == $selectedBranchId && $stock->stock_quantity > 0;
                    });
                });

                // Get first variant (regardless of stock)
                $product->first_variant = ProductVariant::where('product_id', $product->id)
                                        ->whereHas('branchStocks', function($query) use ($selectedBranchId) {
                                            $query->where('branch_id', $selectedBranchId);
                                        })
                                        ->orderBy('id', 'asc')
                                        ->first();
            } else {
                // If no branch selected, check for any stock
                $product->has_stock = $product->variants->contains(function($variant) {
                    return $variant->branchStocks->contains(function($stock) {
                        return $stock->stock_quantity > 0;
                    });
                });

                // Get first variant (regardless of stock)
                $product->first_variant = ProductVariant::where('product_id', $product->id)
                                        ->whereHas('branchStocks')
                                        ->orderBy('id', 'asc')
                                        ->first();
            }

            // Tính giá thấp nhất bao gồm cả biến thể
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

            // Lấy các mã giảm giá áp dụng cho sản phẩm này
            $product->applicable_discount_codes = $activeDiscountCodes->filter(function($discountCode) use ($product) {
                // Chỉ áp dụng cho tất cả sản phẩm nếu applicable_items === 'all_items' (và giữ applicable_scope === 'all' nếu có)
                if (($discountCode->applicable_scope === 'all') || ($discountCode->applicable_items === 'all_items')) {
                    // Kiểm tra điều kiện tối thiểu nếu có
                    if ($discountCode->min_requirement_type && $discountCode->min_requirement_value > 0) {
                        if ($discountCode->min_requirement_type === 'order_amount') {
                            // Không kiểm tra ở đây, chỉ kiểm tra khi checkout
                            return true;
                        } elseif ($discountCode->min_requirement_type === 'product_price') {
                            // Sử dụng giá thấp nhất để kiểm tra điều kiện
                            if ($product->min_price < $discountCode->min_requirement_value) {
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
                    if ($product->min_price < $discountCode->min_requirement_value) {
                        return false;
                    }
                }
                return $applies;
            });

            return $product;
        });

        return view("customer.shop.index", compact('products', 'categories', 'selectedCategoryIds'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Get selected branch ID from BranchService
        $currentBranch = $this->branchService->getCurrentBranch();
        $selectedBranchId = $currentBranch ? $currentBranch->id : null;

        $product = Product::with([
            'category',
            'images' => function($query) {
                $query->orderBy('is_primary', 'desc');
            },
            'reviews' => function($query) {
                $query->where('approved', true)
                      ->with('user')
                      ->orderBy('created_at', 'desc');
            },
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
        ])->findOrFail($id);

        // Tính toán thông tin rating
        $product->average_rating = $product->reviews->avg('rating') ?? 0;
        $product->reviews_count = $product->reviews->count();

        // Add S3 URLs to all product images
        foreach ($product->images as $image) {
            $image->s3_url = asset('storage/' . $image->img);
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
            'reviews' => function($query) {
                $query->where('approved', true);
            },
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
                $relatedProduct->primary_image->s3_url = asset('storage/' . $relatedProduct->primary_image->img);
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
            'branches'
        ));
    }
    public function showComboDetail(Request $request, $id)
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
    ])->findOrFail($id);

    // Lấy tồn kho tại chi nhánh hiện tại (nếu có)
    $branchStocks = $combo->comboBranchStocks;

    // Chuẩn bị dữ liệu sản phẩm trong combo
    $items = $combo->comboItems->map(function($item) {
        $variant = $item->productVariant;
        $product = $variant->product;
        return [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_ingredients' => $product->ingredients, // Lấy thành phần sản phẩm
            'variant_id' => $variant->id,
            'variant_name' => $variant->variant_description ?? null,
            'quantity' => $item->quantity,
            'image' => $product->primaryImage ? asset('storage/' . $product->primaryImage->img) : null,
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

    return view('customer.shop.show-combo', [
        'combo' => $combo,
        'items' => $items,
        'branchStocks' => $branchStocks,
        'selectedBranch' => $currentBranch,
        'relatedProducts' => $relatedProducts,
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
            'rating' => 'required|integer|min:0|max:5',
            'review' => 'nullable|string|max:2000',
            'review_image' => 'nullable|image|max:2048',
            'is_anonymous' => 'nullable|boolean',
        ]);

        $product = Product::findOrFail($id);
        $user = $request->user();

        // Kiểm tra user đã mua sản phẩm này chưa (đã có đơn hàng delivered chứa product này)
        $order = \App\Models\Order::where('customer_id', $user->id)
            ->where('status', 'delivered')
            ->whereHas('orderItems.productVariant', function($q) use ($id) {
                $q->where('product_id', $id);
            })
            ->orderByDesc('order_date')
            ->first();
        if (!$order) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Bạn chỉ có thể đánh giá sản phẩm đã mua!'], 403);
            }
            return redirect()->back()->with('error', 'Bạn chỉ có thể đánh giá sản phẩm đã mua!');
        }

        // TẠM THỜI BỎ QUA KIỂM TRA ORDER
        $review = new \App\Models\ProductReview();
        $review->user_id = $user->id;
        $review->product_id = $product->id;
        $review->order_id = $order->id;
        $review->branch_id = $request->input('branch_id');
        $review->rating = $request->input('rating');
        $review->review = $request->input('review');
        $review->review_date = now();
        $review->approved = false;
        $review->is_verified_purchase = true;
        $review->is_anonymous = $request->boolean('is_anonymous', false);
        $review->helpful_count = 0;
        $review->report_count = 0;
        $review->is_featured = false;

        if ($request->hasFile('review_image')) {
            // Nếu có ảnh cũ thì xóa khỏi S3
            if ($review->review_image) {
                \Storage::disk('s3')->delete($review->review_image);
            }
            $path = $request->file('review_image')->store('reviews', 's3');
            $review->review_image = $path;
        }

        $review->save();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Đánh giá của bạn đã được gửi và đang chờ duyệt!']);
        }
        return redirect()->route('products.show', $product->id)->with('success', 'Đánh giá của bạn đã được gửi và đang chờ duyệt!');
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
}

