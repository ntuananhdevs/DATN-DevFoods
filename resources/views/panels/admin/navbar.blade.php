@if($configData["mainLayoutType"] == 'horizontal' && isset($configData["mainLayoutType"]))
<nav class="header-navbar navbar-expand-lg navbar navbar-with-menu {{ $configData['navbarColor'] }} navbar-fixed">
  <div class="navbar-header d-xl-block d-none">
    <ul class="nav navbar-nav flex-row">
      <li class="nav-item"><a class="navbar-brand" href="dashboard-analytics">
          <div class="brand-logo"></div>
        </a></li>
    </ul>
  </div>
  @else
  <nav
    class="header-navbar navbar-expand-lg navbar navbar-with-menu {{ $configData['navbarClass'] }} navbar-light navbar-shadow {{ $configData['navbarColor'] }}">
    @endif
    <div class="navbar-wrapper">
      <div class="navbar-container content">
        <div class="navbar-collapse" id="navbar-mobile">
          <div class="mr-auto float-left bookmark-wrapper d-flex align-items-center">
            <ul class="nav navbar-nav">
              <li class="nav-item mobile-menu d-xl-none mr-auto"><a class="nav-link nav-menu-main menu-toggle hidden-xs"
                  href="#"><i class="ficon feather icon-menu"></i></a></li>
            </ul>
            <!-- Các phần bookmark đã được loại bỏ -->
          </div>
          <ul class="nav navbar-nav float-right">
            <!-- Phần ngôn ngữ đã được loại bỏ -->
            <!-- Phần phóng to đã được loại bỏ -->
            <!-- Phần tìm kiếm đã được loại bỏ -->
            
            <li class="dropdown dropdown-notification nav-item"><a class="nav-link nav-link-label" href="#"
                data-toggle="dropdown"><i class="ficon feather icon-bell"></i><span
                  class="badge badge-pill badge-primary badge-up">5</span></a>
              <ul class="dropdown-menu dropdown-menu-media dropdown-menu-right">
                <li class="dropdown-menu-header">
                  <div class="dropdown-header m-0 p-2">
                    <h3 class="white">5 New</h3><span class="grey darken-2">App Notifications</span>
                  </div>
                </li>
                <li class="scrollable-container media-list">
                  <a class="d-flex justify-content-between" href="javascript:void(0)">
                    <div class="media d-flex align-items-start">
                      <div class="media-left"><i class="feather icon-plus-square font-medium-5 primary"></i></div>
                      <div class="media-body">
                        <h6 class="primary media-heading">You have new order!</h6><small class="notification-text"> Are
                          your going to meet me
                          tonight?</small>
                      </div><small>
                        <time class="media-meta" datetime="2015-06-11T18:29:20+08:00">9 hours
                          ago</time></small>
                    </div>
                  </a>
                  <a class="d-flex justify-content-between" href="javascript:void(0)">
                    <div class="media d-flex align-items-start">
                      <div class="media-left"><i class="feather icon-download-cloud font-medium-5 success"></i></div>
                      <div class="media-body">
                        <h6 class="success media-heading red darken-1">99% Server load</h6>
                        <small class="notification-text">You got new order of goods.</small>
                      </div><small>
                        <time class="media-meta" datetime="2015-06-11T18:29:20+08:00">5 hour
                          ago</time></small>
                    </div>
                  </a><a class="d-flex justify-content-between" href="javascript:void(0)">
                    <div class="media d-flex align-items-start">
                      <div class="media-left"><i class="feather icon-alert-triangle font-medium-5 danger"></i></div>
                      <div class="media-body">
                        <h6 class="danger media-heading yellow darken-3">Warning notifixation
                        </h6><small class="notification-text">Server have 99% CPU usage.</small>
                      </div><small>
                        <time class="media-meta" datetime="2015-06-11T18:29:20+08:00">Today</time></small>
                    </div>
                  </a><a class="d-flex justify-content-between" href="javascript:void(0)">
                    <div class="media d-flex align-items-start">
                      <div class="media-left"><i class="feather icon-check-circle font-medium-5 info"></i></div>
                      <div class="media-body">
                        <h6 class="info media-heading">Complete the task</h6><small class="notification-text">Cake
                          sesame snaps cupcake</small>
                      </div><small>
                        <time class="media-meta" datetime="2015-06-11T18:29:20+08:00">Last
                          week</time></small>
                    </div>
                  </a><a class="d-flex justify-content-between" href="javascript:void(0)">
                    <div class="media d-flex align-items-start">
                      <div class="media-left"><i class="feather icon-file font-medium-5 warning"></i></div>
                      <div class="media-body">
                        <h6 class="warning media-heading">Generate monthly report</h6><small
                          class="notification-text">Chocolate cake oat cake tiramisu
                          marzipan</small>
                      </div><small>
                        <time class="media-meta" datetime="2015-06-11T18:29:20+08:00">Last
                          month</time></small>
                    </div>
                  </a>
                </li>
                <li class="dropdown-menu-footer"><a class="dropdown-item p-1 text-center" href="javascript:void(0)">Read
                    all notifications</a></li>
              </ul>
            </li>
            <!-- Mail đã được di chuyển sang phải -->
            <li class="nav-item d-none d-lg-block">
              <a class="nav-link" href="app-email" data-toggle="tooltip" data-placement="top" title="Email">
                <i class="ficon feather icon-mail"></i>
              </a>
            </li>
            
            <li class="dropdown dropdown-user nav-item">
              <a class="dropdown-toggle nav-link dropdown-user-link" href="#" data-toggle="dropdown">
                <div class="user-nav d-sm-flex d-none">
                  <span class="user-name text-bold-600">{{ Auth::user()->name ?? 'Admin' }}</span>
                  <span class="user-status text-success">{{ Auth::user()->role->name ?? 'Online' }}</span>
                </div>
                <span>
                  <img class="round" src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : asset('images/portrait/small/avatar-s-11.jpg') }}" alt="avatar" height="40" width="40" />
                </span>
              </a>
              <div class="dropdown-menu dropdown-menu-right shadow-lg border-0">
                <div class="dropdown-user-details p-2 mb-2 border-bottom">
                  <div class="d-flex align-items-center">
                    <div class="avatar mr-2">
                      <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : asset('images/portrait/small/avatar-s-11.jpg') }}" alt="avatar" height="50" width="50" class="round" />
                    </div>
                    <div>
                      <h6 class="mb-0">{{ Auth::user()->name ?? 'Admin' }}</h6>
                      <small class="text-muted">{{ Auth::user()->email ?? 'admin@example.com' }}</small>
                    </div>
                  </div>
                </div>
                <a class="dropdown-item" href="page-user-profile">
                  <i class="feather icon-user mr-1"></i> Edit Profile
                </a>
                <a class="dropdown-item" href="app-email">
                  <i class="feather icon-mail mr-1"></i> My Inbox
                </a>
                <a class="dropdown-item" href="app-todo">
                  <i class="feather icon-check-square mr-1"></i> Task
                </a>
                <a class="dropdown-item" href="app-chat">
                  <i class="feather icon-message-square mr-1"></i> Chats
                </a>
                <div class="dropdown-divider"></div>
                <form action="{{ route('admin.logout') }}" method="POST" style="display: inline;">
                  @csrf
                  <a href="javascript:void(0)" onclick="this.closest('form').submit();" class="dropdown-item text-danger">
                    <i class="feather icon-power mr-1"></i> Logout
                  </a>
                </form>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </nav>

  {{-- Search Start Here --}}
  <!-- Phần tìm kiếm đã được loại bỏ hoàn toàn -->
  {{-- Search Ends --}}
  <!-- END: Header-->
  <style>
    /* CSS cho navbar ở đây */
    .header-navbar {
      box-shadow: 0 4px 20px 0 rgba(0, 0, 0, 0.1);
    }
    
    /* CSS cho thanh tìm kiếm bên trái */
    .search-bar {
      margin-left: 15px;
    }
    
    .search-input-group {
      display: flex;
      align-items: center;
      background-color: rgba(115, 103, 240, 0.05);
      border-radius: 30px;
      padding: 0.4rem 1rem;
      transition: all 0.3s ease;
      width: 250px;
    }
    
    .search-input-group:hover, .search-input-group:focus-within {
      background-color: rgba(115, 103, 240, 0.1);
      box-shadow: 0 3px 10px rgba(115, 103, 240, 0.1);
    }
    
    .search-icon {
      color: #7367f0;
      margin-right: 8px;
    }
    
    .search-input {
      border: none;
      background: transparent;
      padding: 0;
      font-size: 0.9rem;
      color: #626262;
      width: 100%;
    }
    
    .search-input:focus {
      outline: none;
      box-shadow: none;
    }
    
    .search-input::placeholder {
      color: #b8c2cc;
      font-size: 0.9rem;
    }
    
    /* CSS hiện tại */
    .dropdown-user-link img {
      border: 2px solid #7367f0;
      transition: all 0.3s ease;
    }
    
    .dropdown-user-link img:hover {
      transform: scale(1.05);
    }
    
    .dropdown-menu-right {
      border-radius: 0.5rem;
    }
    
    .dropdown-item {
      padding: 0.75rem 1.5rem;
      transition: all 0.2s ease;
      border-radius: 0.3rem;
      margin: 0.2rem 0;
      display: flex;
      align-items: center;
    }
    
    .dropdown-item i {
      font-size: 1.1rem;
      margin-right: 0.75rem;
    }
    
    .dropdown-item:hover {
      background-color: #f8f8f8;
      transform: translateX(5px);
    }
    
    .dropdown-item.text-danger:hover {
      background-color: #fee;
    }
    
    .dropdown-user-details {
      background-color: #f8f9fa;
      border-radius: 0.3rem 0.3rem 0 0;
      padding: 1rem;
      transition: all 0.3s ease;
    }
    
    .dropdown-user-details:hover {
      background-color: #f0f0f0;
    }
  </style>