/devfoods-frontend
│── /src
│   ├── /api              # 📡 Gọi API
│   │   ├── axiosClient.js # Cấu hình Axios
│   │   ├── authApi.js     # API xác thực (login, register, logout, OAuth)
│   │   ├── userApi.js     # API người dùng
│   │   ├── orderApi.js    # API đơn hàng
│   │   ├── productApi.js  # API sản phẩm
│   │   ├── driverApi.js   # API tài xế
│   │
│   ├── /assets           # 🎨 Ảnh, icon, logo
│   │   ├── logo.png
│   │   ├── icons/
│   │
│   ├── /components       # 🏗 Component tái sử dụng
│   │   ├── common/       # Các component dùng chung
│   │   │   ├── Button.jsx
│   │   │   ├── Input.jsx
│   │   │   ├── Modal.jsx
│   │   │   ├── Navbar.jsx
│   │   │   ├── Sidebar.jsx
│   │   │   ├── ProtectedRoute.jsx  # Bảo vệ route theo quyền
│   │
│   ├── /layouts          # 🏗 Layout theo từng vai trò
│   │   ├── AdminLayout.jsx
│   │   ├── ClientLayout.jsx
│   │   ├── RestaurantLayout.jsx
│   │   ├── DriverLayout.jsx
│   │
│   ├── /pages            # 📄 Các trang chính
│   │   ├── /auth
│   │   │   ├── Login.jsx
│   │   │   ├── Register.jsx
│   │   │   ├── OAuthCallback.jsx # Xử lý login bằng Google/Facebook
│   │   │
│   │   ├── /admin        # 📌 Trang dành cho admin
│   │   │   ├── dashboard/    # 📊 Quản lý chung
│   │   │   │   ├── Dashboard.jsx
│   │   │   │   ├── Statistics.jsx
│   │   │   │   ├── Revenue.jsx
│   │   │
│   │   │   ├── users/        # 👥 Quản lý người dùng
│   │   │   │   ├── UsersList.jsx
│   │   │   │   ├── UserDetail.jsx
│   │   │
│   │   │   ├── orders/       # 🛒 Quản lý đơn hàng
│   │   │   │   ├── OrdersList.jsx
│   │   │   │   ├── OrderDetail.jsx
│   │   │
│   │   │   ├── settings/     # ⚙️ Cấu hình hệ thống
│   │   │   │   ├── GeneralSettings.jsx
│   │   │   │   ├── SecuritySettings.jsx
│   │   │
│   │   ├── /client       # 📌 Trang dành cho khách hàng
│   │   │   ├── home/         # 🏠 Trang chủ
│   │   │   │   ├── Home.jsx
│   │   │   │   ├── FeaturedRestaurants.jsx
│   │   │
│   │   │   ├── restaurant/   # 🍽️ Danh sách nhà hàng
│   │   │   │   ├── RestaurantsList.jsx
│   │   │   │   ├── RestaurantDetail.jsx
│   │   │
│   │   │   ├── orders/       # 🛒 Đơn hàng của khách hàng
│   │   │   │   ├── OrderHistory.jsx
│   │   │   │   ├── OrderTracking.jsx
│   │   │
│   │   │   ├── profile/      # 👤 Hồ sơ cá nhân
│   │   │   │   ├── Profile.jsx
│   │   │   │   ├── EditProfile.jsx
│   │
│   │   ├── /restaurant   # 📌 Trang dành cho nhà hàng
│   │   │   ├── dashboard/    # 📊 Quản lý chung
│   │   │   │   ├── Dashboard.jsx
│   │   │   │   ├── SalesReport.jsx
│   │   │
│   │   │   ├── menu/         # 📜 Quản lý menu
│   │   │   │   ├── MenuList.jsx
│   │   │   │   ├── AddProduct.jsx
│   │   │
│   │   │   ├── orders/       # 🛒 Quản lý đơn hàng
│   │   │   │   ├── ManageOrders.jsx
│   │   │   │   ├── OrderDetail.jsx
│   │   │
│   │   │   ├── settings/     # ⚙️ Cấu hình nhà hàng
│   │   │   │   ├── BusinessSettings.jsx
│   │   │   │   ├── PaymentSettings.jsx
│   │
│   │   ├── /driver       # 📌 Trang dành cho tài xế
│   │   │   ├── dashboard/    # 📊 Bảng điều khiển
│   │   │   │   ├── Dashboard.jsx
│   │   │   │   ├── Earnings.jsx
│   │   │
│   │   │   ├── orders/       # 🚚 Đơn hàng
│   │   │   │   ├── ActiveOrders.jsx
│   │   │   │   ├── OrderHistory.jsx
│   │   │
│   │   │   ├── profile/      # 👤 Hồ sơ tài xế
│   │   │   │   ├── Profile.jsx
│   │   │   │   ├── EditProfile.jsx
│   │
│   ├── /routes           # 🚦 Quản lý route
│   │   ├── index.jsx      # Định tuyến chính
│   │   ├── adminRoutes.jsx  # Route cho admin
│   │   ├── clientRoutes.jsx  # Route cho khách hàng
│   │   ├── restaurantRoutes.jsx  # Route cho nhà hàng
│   │   ├── driverRoutes.jsx  # Route cho tài xế
│   │
│   ├── /store            # 🛍️ Quản lý Zustand
│   │   ├── authStore.js   # Trạng thái xác thực
│   │   ├── userStore.js   # Trạng thái người dùng
│   │
│   ├── /styles           # 🎨 CSS & Tailwind
│   │   ├── globals.css    # CSS toàn cục
│   │
│   ├── /utils            # 🔧 Helper functions
│   │   ├── formatDate.js  # Format ngày giờ
│   │   ├── storage.js     # Lưu JWT vào localStorage
│   │
│── /public               # 🌍 Public files (favicon, manifest.json)
│── .env                  # 🛠 Config môi trường (API URL, OAuth Keys)
│── .gitignore            # 🚫 Bỏ qua file không cần commit
│── index.html            # 📜 Entry point chính
│── vite.config.js        # ⚡ Cấu hình Vite
│── package.json          # 📦 Thông tin package
│── README.md             # 📖 Hướng dẫn sử dụng
