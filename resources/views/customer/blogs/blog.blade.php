@extends('layouts.customer.fullLayoutMaster')

@section('title', 'FastFood - Blog & Tin Tức')

@section('content')
    <!-- Blog Header -->
    <div class="container py-5">
        <div class="row align-items-center mb-4">
            <div class="col-md-8">
                <h1 class="mb-2">Blog & Tin Tức</h1>
                <p class="text-muted">Khám phá những bài viết mới nhất về ẩm thực và cập nhật từ FastFood</p>
            </div>
            <div class="col-md-4">
                <form action="{{ url('/blog/search') }}" method="GET">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control" name="query" placeholder="Tìm kiếm bài viết...">
                    </div>
                </form>
            </div>
        </div>

        <!-- Featured Post -->
        @if($featuredPost)
        <div class="card mb-5">
            <div class="row g-0">
                <div class="col-md-6 position-relative">
                    <img src="{{ asset($featuredPost->image) }}" class="img-fluid h-100 object-cover" alt="{{ $featuredPost->title }}">
                    @if($category = $categories->firstWhere('id', $featuredPost->categoryId))
                        <span class="badge bg-orange position-absolute top-0 start-0 m-3">{{ $category->name }}</span>
                    @endif
                </div>
                <div class="col-md-6">
                    <div class="card-body p-4 d-flex flex-column h-100 justify-content-center">
                        <div class="d-flex text-muted mb-3">
                            <div class="me-3">
                                <i class="far fa-calendar me-1"></i>
                                {{ $featuredPost->date }}
                            </div>
                            <div>
                                <i class="far fa-clock me-1"></i>
                                {{ $featuredPost->readTime }} phút đọc
                            </div>
                        </div>
                        <h2 class="card-title mb-3">
                            <a href="{{ url('/blog/' . $featuredPost->slug) }}" class="text-decoration-none text-dark hover-orange">
                                {{ $featuredPost->title }}
                            </a>
                        </h2>
                        <p class="card-text mb-4">{{ $featuredPost->excerpt }}</p>
                        <div class="mt-auto">
                            <a href="{{ url('/blog/' . $featuredPost->slug) }}" class="btn btn-orange">
                                Đọc Tiếp <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="row">
            <!-- Blog Posts -->
            <div class="col-lg-8 order-2 order-lg-1">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Bài Viết Mới Nhất</h2>
                    <div class="d-flex align-items-center">
                        <span class="text-muted me-2">Sắp xếp theo:</span>
                        <select class="form-select form-select-sm" style="width: auto;">
                            <option>Mới nhất</option>
                            <option>Phổ biến nhất</option>
                            <option>Cũ nhất</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    @foreach($posts as $post)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="position-relative">
                                <img src="{{ asset($post->image) }}" class="card-img-top" alt="{{ $post->title }}">
                                @if($category = $categories->firstWhere('id', $post->categoryId))
                                    <span class="badge bg-orange position-absolute top-0 start-0 m-2">{{ $category->name }}</span>
                                @endif
                            </div>
                            <div class="card-body">
                                <div class="d-flex text-muted small mb-2">
                                    <div class="me-3">
                                        <i class="far fa-calendar me-1"></i>
                                        {{ $post->date }}
                                    </div>
                                    <div>
                                        <i class="far fa-clock me-1"></i>
                                        {{ $post->readTime }} phút đọc
                                    </div>
                                </div>
                                <h5 class="card-title">
                                    <a href="{{ url('/blog/' . $post->slug) }}" class="text-decoration-none text-dark hover-orange">
                                        {{ $post->title }}
                                    </a>
                                </h5>
                                <p class="card-text text-muted">{{ Str::limit($post->excerpt, 100) }}</p>
                            </div>
                            <div class="card-footer bg-transparent border-top-0">
                                <a href="{{ url('/blog/' . $post->slug) }}" class="btn btn-outline-secondary w-100">
                                    Đọc Tiếp
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1" aria-disabled="true">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4 order-1 order-lg-2 mb-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h4 class="card-title mb-3">Danh Mục</h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <a href="{{ url('/blog') }}" class="text-decoration-none text-dark hover-orange">Tất cả bài viết</a>
                                <span class="badge bg-secondary rounded-pill">{{ count($posts) }}</span>
                            </li>
                            @foreach($categories as $category)
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <a href="{{ url('/blog/category/' . $category->id) }}" class="text-decoration-none text-dark hover-orange">{{ $category->name }}</a>
                                <span class="badge bg-secondary rounded-pill">{{ $posts->where('categoryId', $category->id)->count() }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <h4 class="card-title mb-3">Bài Viết Phổ Biến</h4>
                        @foreach($popularPosts as $post)
                        <div class="d-flex mb-3">
                            <div class="flex-shrink-0" style="width: 80px; height: 80px;">
                                <img src="{{ asset($post->image) }}" class="img-fluid rounded" alt="{{ $post->title }}">
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-1">
                                    <a href="{{ url('/blog/' . $post->slug) }}" class="text-decoration-none text-dark hover-orange">
                                        {{ Str::limit($post->title, 50) }}
                                    </a>
                                </h6>
                                <div class="small text-muted">{{ $post->date }}</div>
                            </div>
                        </div>
                        @if(!$loop->last)
                            <hr class="my-3">
                        @endif
                        @endforeach
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <h4 class="card-title mb-3">Tags</h4>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($tags as $tag)
                            <a href="{{ url('/blog/tag/' . $tag) }}" class="badge bg-light text-dark text-decoration-none py-2 px-3">{{ $tag }}</a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="card bg-orange-light">
                    <div class="card-body p-4">
                        <h4 class="card-title mb-3">Đăng Ký Nhận Tin</h4>
                        <p class="card-text mb-3">Nhận thông báo khi có bài viết mới</p>
                        <form action="{{ url('/subscribe') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <input type="email" class="form-control" placeholder="Email của bạn" required>
                            </div>
                            <button type="submit" class="btn btn-orange w-100">Đăng Ký</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection