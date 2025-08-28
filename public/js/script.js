// --- 1. Mock Data (Converted from lib/driver-mock-data.ts) ---
// Keep mock data here as it's used by JS logic.
// For a real app, this data would come from Laravel controllers via AJAX or Blade props.
const mockDriverProfile = {
    id: "driver001",
    name: "Nguyễn Văn Tài",
    avatarUrl: "/placeholder.svg?width=128&height=128",
    isActive: true,
    vehicle: "Honda Wave Alpha",
    licensePlate: "59-T1 123.45",
    idCardNumber: "012345678910",
    bankAccount: {
        bankName: "Vietcombank",
        accountNumber: "0071000123456",
        accountHolderName: "NGUYEN VAN TAI",
    },
    phone: "0987654321",
};

const generateItems = () => {
    const foodItems = [{
            name: "Cơm gà xối mỡ",
            price: 45000
        },
        {
            name: "Pizza Hải Sản",
            price: 150000
        },
        {
            name: "Bún bò Huế",
            price: 50000
        },
        {
            name: "Trà sữa trân châu",
            price: 40000
        },
        {
            name: "Phở Bò Tái",
            price: 55000
        },
    ];
    const numItems = Math.floor(Math.random() * 2) + 1;
    const selectedItems = [];
    for (let i = 0; i < numItems; i++) {
        const item = foodItems[Math.floor(Math.random() * foodItems.length)];
        selectedItems.push({
            ...item,
            quantity: Math.floor(Math.random() * 2) + 1
        });
    }
    return selectedItems;
};

const calculateTotalAmount = (items) => {
    return items.reduce((sum, item) => sum + item.price * item.quantity, 0);
};

const calculateShippingFee = (distanceKm) => {
    if (distanceKm <= 0) return 0;

    // Theo spec mới:
    // - Phí km đầu: 10,000đ (cho 1 km đầu tiên)
    // - Giá/km tiếp theo: 5,000đ/km
    const firstKmFee = 10000;
    const additionalKmFee = 5000;

    if (distanceKm <= 1) {
        return firstKmFee;
    }

    // Round up distance for fair pricing
    const additionalKm = Math.ceil(distanceKm - 1);
    return firstKmFee + (additionalKm * additionalKmFee);
};

// IMPORTANT: This mockOrders array will be the source of truth for client-side updates.
// In a real app, you'd fetch this from an API and update it.
const mockOrders = [{
        id: "DH001",
        customerName: "Trần Thị Bích",
        deliveryAddress: "123 Đường Sư Vạn Hạnh, P.12, Q.10, TP. HCM",
        pickupBranch: "Chi nhánh 1", // Sư Vạn Hạnh
        customerPhone: "0901234567",
        orderTime: "2025-06-07 10:00",
        status: "Chờ nhận",
        items: generateItems(),
        distanceKm: 2.5,
        pickupCoordinates: {
            lat: 10.774,
            lng: 106.668
        }, // Approx. Sư Vạn Hạnh
        deliveryCoordinates: {
            lat: 10.765,
            lng: 106.675
        }, // Approx. nearby
    },
    {
        id: "DH002",
        customerName: "Lê Văn Cường",
        deliveryAddress: "456 Đường Nguyễn Trãi, P.8, Q.5, TP. HCM",
        pickupBranch: "Chi nhánh 2", // Nguyễn Trãi
        customerPhone: "0912345678",
        orderTime: "2025-06-07 10:15",
        status: "Chờ nhận",
        items: generateItems(),
        distanceKm: 4.0,
        pickupCoordinates: {
            lat: 10.755,
            lng: 106.66
        }, // Approx. Nguyễn Trãi
        deliveryCoordinates: {
            lat: 10.74,
            lng: 106.65
        }, // Approx. nearby
    },
    {
        id: "DH003",
        customerName: "Phạm Thị Dung",
        deliveryAddress: "789 Đường Cách Mạng Tháng Tám, P.15, Q.10, TP. HCM",
        pickupBranch: "Chi nhánh 1", // Sư Vạn Hạnh
        customerPhone: "0923456789",
        orderTime: "2025-06-07 09:30",
        status: "Đang giao",
        items: generateItems(),
        distanceKm: 3.2,
        estimatedDeliveryTime: "2025-06-07 10:45",
        pickupCoordinates: {
            lat: 10.774,
            lng: 106.668
        },
        deliveryCoordinates: {
            lat: 10.785,
            lng: 106.68
        },
    },
    {
        id: "DH004",
        customerName: "Hoàng Văn Em",
        deliveryAddress: "101 Đường Lê Văn Sỹ, P.13, Q.Phú Nhuận, TP. HCM",
        pickupBranch: "Chi nhánh 3", // Lê Văn Sỹ
        customerPhone: "0934567890",
        orderTime: "2025-06-06 18:00",
        status: "Đã hoàn thành",
        items: generateItems(),
        distanceKm: 6.1,
        pickupCoordinates: {
            lat: 10.79,
            lng: 106.678
        }, // Approx. Lê Văn Sỹ
        deliveryCoordinates: {
            lat: 10.8,
            lng: 106.69
        },
    },
    {
        id: "DH005",
        customerName: "Võ Thị Lan",
        deliveryAddress: "234 Đường 3 Tháng 2, P.10, Q.10, TP. HCM",
        pickupBranch: "Chi nhánh 1", // Sư Vạn Hạnh
        customerPhone: "0945678901",
        orderTime: "2025-06-07 11:00",
        status: "Chờ nhận",
        items: generateItems(),
        distanceKm: 1.5,
        notes: "Gọi trước khi giao 5 phút.",
        pickupCoordinates: {
            lat: 10.774,
            lng: 106.668
        },
        deliveryCoordinates: {
            lat: 10.77,
            lng: 106.67
        },
    },
].map((order) => {
    const totalAmount = calculateTotalAmount(order.items);
    const shippingFee = calculateShippingFee(order.distanceKm);
    return {
        ...order,
        totalAmount,
        shippingFee,
        finalTotal: totalAmount + shippingFee,
        driverEarnings: shippingFee, // Assuming driver earns the full shipping fee for simplicity
    };
});

const mockDeliveryHistory = mockOrders
    .filter((order) => order.status === "Đã hoàn thành")
    .map((order) => ({
        ...order,
        rating: Math.floor(Math.random() * 3) + 3,
        customerFeedback: Math.random() > 0.5 ? "Giao hàng nhanh, tài xế thân thiện." : "Đồ ăn ngon, shipper nhiệt tình.",
    }));

const mockNotifications = [{
        id: "notif001",
        type: "new_order",
        title: "Đơn hàng mới!",
        message: "Có đơn hàng mới DH001 đang chờ bạn nhận tại Chi nhánh 1.",
        timestamp: "2025-06-07 10:01",
        read: false,
        orderId: "DH001",
        link: "/driver/orders/DH001", // Updated to Laravel route format
    },
    {
        id: "notif002",
        type: "status_update",
        title: "Cập nhật đơn hàng DH003",
        message: "Đơn hàng DH003 đã được cập nhật trạng thái: Đang giao.",
        timestamp: "2025-06-07 09:35",
        read: true,
        orderId: "DH003",
    },
    {
        id: "notif003",
        type: "earning_report",
        title: "Báo cáo thu nhập",
        message: "Thu nhập ngày 06/06/2025 của bạn là 150.000đ. Chi tiết...",
        timestamp: "2025-06-07 08:00",
        read: true,
        link: "/driver/history", // Updated to Laravel route format
    },
    {
        id: "notif004",
        type: "system_message",
        title: "Bảo trì hệ thống",
        message: "Hệ thống sẽ bảo trì từ 02:00 đến 03:00 ngày 08/06/2025.",
        timestamp: "2025-06-06 17:00",
        read: false,
    },
];

// --- 2. Utility Functions ---
function cn(...args) {
    return args.filter(Boolean).join(" ");
}

function formatCurrency(amount) {
    return amount.toLocaleString("vi-VN") + "đ";
}

// This function is now less critical as Blade handles initial badge rendering,
// but useful if JS needs to dynamically change a badge.
function getStatusBadgeClass(status) {
    switch (status) {
        case "Chờ nhận":
            return "badge-info";
        case "Đang giao":
            return "badge-warning";
        case "Đã hoàn thành":
            return "badge-success";
        case "Đã hủy":
            return "bg-destructive text-destructive-foreground";
        default:
            return "bg-secondary text-secondary-foreground";
    }
}

// This function is now less critical as Blade handles initial alert rendering,
// but useful if JS needs to dynamically create an alert.
function getAlertVariantClass(variant) {
    switch (variant) {
        case "success":
            return "alert-success";
        case "warning":
            return "alert-warning";
        case "info":
            return "alert-info";
        case "destructive":
            return "border-destructive/50 text-destructive dark:border-destructive [&>svg]:text-destructive";
        default:
            return "bg-background text-foreground";
    }
}

// --- 3. Toast Notification System ---
function showToast(options) {
    const toastContainer = document.getElementById("toast-container");
    if (!toastContainer) return;

    const toastDiv = document.createElement("div");
    toastDiv.className = cn(
        "relative p-4 rounded-lg shadow-lg flex items-center gap-3 transition-all duration-300 ease-out transform translate-x-full opacity-0",
        options.variant === "success" ? "bg-green-500 text-white" :
        options.variant === "destructive" ? "bg-red-500 text-white" :
        "bg-gray-800 text-white"
    );
    toastDiv.innerHTML = `
    <div>
      <p class="font-semibold">${options.title}</p>
      ${options.description ? `<p class="text-sm opacity-90">${options.description}</p>` : ""}
    </div>
    <button class="absolute top-1 right-2 text-white opacity-70 hover:opacity-100" onclick="this.parentElement.remove()">
      &times;
    </button>
  `;

    toastContainer.appendChild(toastDiv);

    // Animate in
    setTimeout(() => {
        toastDiv.classList.remove("translate-x-full", "opacity-0");
        toastDiv.classList.add("translate-x-0", "opacity-100");
    }, 10);

    // Auto-remove
    setTimeout(() => {
        toastDiv.classList.remove("translate-x-0", "opacity-100");
        toastDiv.classList.add("translate-x-full", "opacity-0");
        toastDiv.addEventListener("transitionend", () => toastDiv.remove());
    }, options.duration || 3000);
}

// --- 4. Mapbox Integration (Adapted for direct use) ---
const MAPBOX_ACCESS_TOKEN = "pk.eyJ1IjoibmhhdG5ndXllbnF2IiwiYSI6ImNtYjZydDNnZDAwY24ybm9qcTdxcTNocG8ifQ.u7X_0DfN7d52xZ8cGFbWyQ";
let mapboxMap = null;
let mapboxDriverMarker = null;
let mapboxDriverMoveInterval = null;

// This function will be called from order-detail.blade.php
function initMapboxNavigation(order) {
    if (mapboxMap) {
        mapboxMap.remove();
        mapboxMap = null;
    }
    if (mapboxDriverMoveInterval) {
        clearInterval(mapboxDriverMoveInterval);
        mapboxDriverMoveInterval = null;
    }

    const mapContainer = document.getElementById("mapbox-container");
    if (!mapContainer) return;

    mapboxgl.accessToken = MAPBOX_ACCESS_TOKEN;
    mapboxMap = new mapboxgl.Map({
        container: mapContainer,
        style: "mapbox://styles/mapbox/streets-v12",
        center: [order.pickupCoordinates.lng, order.pickupCoordinates.lat],
        zoom: 13,
    });

    mapboxMap.on("load", () => {
        new mapboxgl.Marker({
                color: "blue"
            })
            .setLngLat([order.pickupCoordinates.lng, order.pickupCoordinates.lat])
            .setPopup(new mapboxgl.Popup().setText("Điểm lấy hàng"))
            .addTo(mapboxMap);

        new mapboxgl.Marker({
                color: "red"
            })
            .setLngLat([order.deliveryCoordinates.lng, order.deliveryCoordinates.lat])
            .setPopup(new mapboxgl.Popup().setText("Điểm giao hàng"))
            .addTo(mapboxMap);

        const el = document.createElement("div");
        el.className = "driver-marker";
        el.style.backgroundImage = `url("data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='32' height='32' viewBox='0 0 24 24' fill='green' stroke='white' strokeWidth='1' strokeLinecap='round' strokeLinejoin='round' class='lucide lucide-navigation-2'%3E%3Cpolygon points='12 2 19 21 12 17 5 21 12 2'%3E%3C/polygon%3E%3C/svg%3E")`;
        el.style.width = `32px`;
        el.style.height = `32px`;
        el.style.backgroundSize = "100%";

        mapboxDriverMarker = new mapboxgl.Marker(el)
            .setLngLat([order.pickupCoordinates.lng, order.pickupCoordinates.lat])
            .addTo(mapboxMap);

        getRoute(
            [order.pickupCoordinates.lng, order.pickupCoordinates.lat],
            [order.deliveryCoordinates.lng, order.deliveryCoordinates.lat],
            mapboxMap,
            order.status
        );
    });

    mapboxMap.addControl(new mapboxgl.NavigationControl(), "top-right");
    mapboxMap.addControl(
        new mapboxgl.GeolocateControl({
            positionOptions: {
                enableHighAccuracy: true
            },
            trackUserLocation: true,
            showUserHeading: true,
        }),
        "top-right"
    );
}

async function getRoute(start, end, map, orderStatus) {
    const url = `https://api.mapbox.com/directions/v5/mapbox/driving-traffic/${start.join(",")};${end.join(",")}?steps=true&geometries=geojson&access_token=${mapboxgl.accessToken}&overview=full`;

    try {
        const response = await fetch(url);
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
        }
        const data = await response.json();
        const route = data.routes[0].geometry.coordinates;
        const geojson = {
            type: "Feature",
            properties: {},
            geometry: {
                type: "LineString",
                coordinates: route,
            },
        };

        if (map.getSource("route")) {
            map.getSource("route").setData(geojson);
        } else {
            map.addLayer({
                id: "route",
                type: "line",
                source: {
                    type: "geojson",
                    data: geojson
                },
                layout: {
                    "line-join": "round",
                    "line-cap": "round"
                },
                paint: {
                    "line-color": "#3887be",
                    "line-width": 7,
                    "line-opacity": 0.75
                },
            });
        }

        const bounds = route.reduce((bounds, coord) => bounds.extend(coord), new mapboxgl.LngLatBounds(route[0], route[0]));
        map.fitBounds(bounds, {
            padding: 50
        });

        // Simulate driver movement
        if (orderStatus === "Đang giao" && mapboxDriverMarker) {
            let step = 0;
            if (mapboxDriverMoveInterval) clearInterval(mapboxDriverMoveInterval); // Clear previous interval
            mapboxDriverMoveInterval = setInterval(() => {
                if (step < route.length) {
                    mapboxDriverMarker.setLngLat(route[step]);
                    if (step > 0) {
                        const prevPoint = route[step - 1];
                        const currentPoint = route[step];
                        const bearing = calculateBearing(prevPoint, currentPoint);
                        const markerElement = mapboxDriverMarker.getElement();
                        if (markerElement) {
                            markerElement.style.transform = `rotate(${bearing}deg)`;
                        }
                    }
                    step += 5; // Move faster for demo
                } else {
                    clearInterval(mapboxDriverMoveInterval);
                    mapboxDriverMoveInterval = null;
                    mapboxDriverMarker.setLngLat(route[route.length - 1]); // Ensure it ends at destination
                }
            }, 500);
        } else if (orderStatus === "Đã hoàn thành" && mapboxDriverMarker) {
            mapboxDriverMarker.setLngLat(end);
            const markerElement = mapboxDriverMarker.getElement();
            if (markerElement) markerElement.style.transform = `rotate(0deg)`;
        } else if (orderStatus === "Chờ nhận" && mapboxDriverMarker) {
            mapboxDriverMarker.setLngLat(start);
            const markerElement = mapboxDriverMarker.getElement();
            if (markerElement) markerElement.style.transform = `rotate(0deg)`;
        }

    } catch (error) {
        console.error("Error fetching route:", error);
        const mapContainer = document.getElementById("mapbox-container");
        if (mapContainer) {
            const errorDiv = document.createElement("div");
            errorDiv.className = "absolute top-2 left-2 bg-red-100 text-red-700 p-2 rounded-md text-xs shadow-lg z-10";
            errorDiv.textContent = `Không thể tải lộ trình: ${error.message}. Vui lòng kiểm tra lại tọa độ hoặc API key.`;
            mapContainer.appendChild(errorDiv);
        }
    }
}

function calculateBearing(startPoint, endPoint) {
    const [lng1, lat1] = startPoint;
    const [lng2, lat2] = endPoint;

    const toRadians = (degrees) => (degrees * Math.PI) / 180;
    const toDegrees = (radians) => (radians * 180) / Math.PI;

    const φ1 = toRadians(lat1);
    const φ2 = toRadians(lat2);
    const Δλ = toRadians(lng2 - lng1);

    const y = Math.sin(Δλ) * Math.cos(φ2);
    const x = Math.cos(φ1) * Math.sin(φ2) - Math.sin(φ1) * Math.cos(φ2) * Math.cos(Δλ);
    const θ = Math.atan2(y, x);

    return (toDegrees(θ) + 360) % 360;
}

// --- 5. Global functions for page-specific JS initialization ---
// These functions will be called from the @section('page_scripts') in each Blade file.

window.DriverApp = {
    mockOrders: mockOrders, // Expose mockOrders globally for other scripts to use
    mockDriverProfile: mockDriverProfile,
    mockNotifications: mockNotifications,
    mockDeliveryHistory: mockDeliveryHistory,
    calculateShippingFee: calculateShippingFee,
    showToast: showToast,
    initMapboxNavigation: initMapboxNavigation,
    // Add other utility functions if needed globally
    cn: cn,
    formatCurrency: formatCurrency,
    getStatusBadgeClass: getStatusBadgeClass,
    getAlertVariantClass: getAlertVariantClass,

    // Function to initialize Orders page logic
    initOrdersPage: function(initialStatus) {
        let currentTab = initialStatus;
        let searchTerm = "";

        const ordersByStatus = (status) => DriverApp.mockOrders.filter((order) => order.status === status &&
            (order.id.toLowerCase().includes(searchTerm.toLowerCase()) ||
                order.customerName.toLowerCase().includes(searchTerm.toLowerCase()) ||
                order.deliveryAddress.toLowerCase().includes(searchTerm.toLowerCase()))
        );

        const updateOrdersDisplay = () => {
            const activeContentDiv = document.querySelector(`#tab-content-${currentTab.replace(/\s/g, '-')}`);
            if (activeContentDiv) {
                activeContentDiv.innerHTML = ordersByStatus(currentTab).length > 0 ?
                    ordersByStatus(currentTab).map(order => `
            <div class="overflow-hidden rounded-lg border bg-card text-card-foreground shadow-sm">
              <div class="p-4 bg-muted/50">
                <div class="flex justify-between items-start">
                  <div>
                    <h3 class="text-base font-semibold tracking-tight">Mã đơn: ${order.id}</h3>
                    <p class="text-xs text-muted-foreground flex items-center">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3 mr-1"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                      ${new Date(order.orderTime).toLocaleString("vi-VN")}
                    </p>
                  </div>
                  <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 ${DriverApp.getStatusBadgeClass(order.status)}">
                    ${order.status}
                  </span>
                </div>
              </div>
              <div class="p-4 text-sm space-y-2">
                <div class="flex items-start">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 mr-2 mt-0.5 text-primary flex-shrink-0"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                  <div>
                    <span class="font-medium">Lấy hàng:</span> ${order.pickupBranch}
                  </div>
                </div>
                <div class="flex items-start">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 mr-2 mt-0.5 text-destructive flex-shrink-0"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                  <div>
                    <span class="font-medium">Giao đến:</span> ${order.deliveryAddress}
                  </div>
                </div>
                <div class="flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 mr-2 text-gray-500 flex-shrink-0"><path d="M22 16.92v3a2 2 0 0 1-2.18 2.02 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.63A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-1.18 2.19l-.7.69a12 12 0 0 0 6.06 6.06l.69-.7a2 2 0 0 1 2.19-1.18 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                  <span>
                    ${order.customerName} - ${order.customerPhone}
                  </span>
                </div>
                <div class="my-2 h-[1px] w-full shrink-0 bg-border"></div>
                <div class="flex justify-between items-center">
                  <p class="font-semibold text-primary">Phí ship: ${DriverApp.formatCurrency(order.shippingFee)}</p>
                  <p class="text-xs text-muted-foreground">${order.distanceKm.toFixed(1)} km</p>
                </div>
              </div>
              <div class="p-4 bg-muted/50">
                <a href="/driver/orders/${order.id}" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 w-full">
                  Xem chi tiết & Nhận đơn
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-2 h-4 w-4"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                </a>
              </div>
            </div>
          `).join("") :
                    `<p class="text-muted-foreground text-center py-8">Không có đơn hàng nào.</p>`;
            }
        };

        document.querySelectorAll(".tab-trigger").forEach(button => {
            button.addEventListener("click", (e) => {
                const newTab = e.currentTarget.dataset.tab;
                currentTab = newTab;
                document.querySelectorAll(".tab-trigger").forEach(btn => {
                    if (btn.dataset.tab === newTab) {
                        btn.dataset.state = "active";
                        btn.classList.add("bg-background", "text-foreground", "shadow-sm");
                        btn.classList.remove("bg-muted", "text-muted-foreground");
                    } else {
                        btn.dataset.state = "inactive";
                        btn.classList.remove("bg-background", "text-foreground", "shadow-sm");
                        btn.classList.add("bg-muted", "text-muted-foreground");
                    }
                });
                document.querySelectorAll(".tab-content").forEach(content => {
                    if (content.id === `tab-content-${newTab.replace(/\s/g, '-')}`) {
                        content.dataset.state = "active";
                        content.classList.remove("hidden");
                    } else {
                        content.dataset.state = "inactive";
                        content.classList.add("hidden");
                    }
                });
                updateOrdersDisplay(); // Update content for the active tab
            });
        });

        const searchInput = document.getElementById("order-search-input");
        if (searchInput) {
            searchInput.addEventListener("input", (e) => {
                searchTerm = e.target.value;
                // Update all tab contents to apply the search filter
                document.querySelectorAll(".tab-content").forEach(content => {
                    const status = content.id.replace('tab-content-', '').replace(/-/g, ' ');
                    content.innerHTML = ordersByStatus(status).length > 0 ?
                        ordersByStatus(status).map(order => `
              <div class="overflow-hidden rounded-lg border bg-card text-card-foreground shadow-sm">
                <div class="p-4 bg-muted/50">
                  <div class="flex justify-between items-start">
                    <div>
                      <h3 class="text-base font-semibold tracking-tight">Mã đơn: ${order.id}</h3>
                      <p class="text-xs text-muted-foreground flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3 mr-1"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        ${new Date(order.orderTime).toLocaleString("vi-VN")}
                      </p>
                    </div>
                    <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 ${DriverApp.getStatusBadgeClass(order.status)}">
                      ${order.status}
                    </span>
                  </div>
                </div>
                <div class="p-4 text-sm space-y-2">
                  <div class="flex items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 mr-2 mt-0.5 text-primary flex-shrink-0"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                    <div>
                      <span class="font-medium">Lấy hàng:</span> ${order.pickupBranch}
                    </div>
                  </div>
                  <div class="flex items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 mr-2 mt-0.5 text-destructive flex-shrink-0"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                    <div>
                      <span class="font-medium">Giao đến:</span> ${order.deliveryAddress}
                    </div>
                  </div>
                  <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 mr-2 text-gray-500 flex-shrink-0"><path d="M22 16.92v3a2 2 0 0 1-2.18 2.02 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.63A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-1.18 2.19l-.7.69a12 12 0 0 0 6.06 6.06l.69-.7a2 2 0 0 1 2.19-1.18 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    <span>
                      ${order.customerName} - ${order.customerPhone}
                    </span>
                  </div>
                  <div class="my-2 h-[1px] w-full shrink-0 bg-border"></div>
                  <div class="flex justify-between items-center">
                    <p class="font-semibold text-primary">Phí ship: ${DriverApp.formatCurrency(order.shippingFee)}</p>
                    <p class="text-xs text-muted-foreground">${order.distanceKm.toFixed(1)} km</p>
                  </div>
                </div>
                <div class="p-4 bg-muted/50">
                  <a href="/driver/orders/${order.id}" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 w-full">
                    Xem chi tiết & Nhận đơn
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-2 h-4 w-4"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                  </a>
                </div>
              </div>
            `).join("") :
                        `<p class="text-muted-foreground text-center py-8">Không có đơn hàng nào.</p>`;
                });
            });
        }
    },

    // Function to initialize Order Detail page logic
    initOrderDetailPage: function(orderId) {
        let order = DriverApp.mockOrders.find((o) => o.id === orderId);
        if (!order) {
            console.error("Order not found for ID:", orderId);
            return;
        }

        DriverApp.initMapboxNavigation(order);

        const updateOrderDetailUI = () => {
            const orderStatusSpan = document.getElementById('order-status-span');
            if (orderStatusSpan) {
                orderStatusSpan.className = `font-semibold ${
          order.status === "Chờ nhận" ? "text-blue-600" :
          order.status === "Đang giao" ? "text-yellow-600" :
          order.status === "Đã hoàn thành" ? "text-green-600" :
          "text-red-600"
        }`;
                orderStatusSpan.textContent = order.status;
            }

            const estimatedDeliveryTimeP = document.getElementById('estimated-delivery-time');
            if (estimatedDeliveryTimeP) {
                if (order.status === "Đang giao" && order.estimatedDeliveryTime) {
                    estimatedDeliveryTimeP.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3 mr-1"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> Dự kiến giao: ${order.estimatedDeliveryTime}`;
                    estimatedDeliveryTimeP.classList.remove('hidden');
                } else {
                    estimatedDeliveryTimeP.classList.add('hidden');
                }
            }

            const actionButtonsContainer = document.getElementById('order-action-buttons');
            if (actionButtonsContainer) {
                let buttonsHtml = '';
                if (order.status === "Chờ nhận") {
                    buttonsHtml = `
            <button id="btn-accept-order" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 w-full sm:w-auto flex-1">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 h-4 w-4"><path d="M2 12h6"/><path d="M16 12h6"/><path d="M12 2v20"/><path d="M12 7l-5 5 5 5"/><path d="M12 17l5-5-5-5"/></svg> Đã nhận hàng & Bắt đầu giao
            </button>
          `;
                } else if (order.status === "Đang giao") {
                    buttonsHtml = `
            <button id="btn-complete-order" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 w-full sm:w-auto flex-1">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 h-4 w-4"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg> Đã giao thành công
            </button>
          `;
                } else if (order.status === "Đã hoàn thành") {
                    buttonsHtml = `
            <div class="relative w-full rounded-lg border p-4 [&>svg~*]:pl-7 [&>svg+div]:translate-y-[-3px] [&>svg]:absolute [&>svg]:left-4 [&>svg]:top-4 [&>svg]:text-foreground alert-success">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
              <h5 class="mb-1 font-medium leading-none tracking-tight">Đơn hàng đã hoàn thành!</h5>
              <div class="text-sm [&_p]:leading-relaxed">Thu nhập ${DriverApp.formatCurrency(order.driverEarnings)} đã được ghi nhận.</div>
            </div>
          `;
                }

                if (order.status === "Chờ nhận" || order.status === "Đang giao") {
                    buttonsHtml += `
            <button id="btn-report-issue" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 w-full sm:w-auto">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 h-4 w-4"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg> Báo cáo sự cố / Hủy đơn
            </button>
          `;
                }
                actionButtonsContainer.innerHTML = buttonsHtml;

                // Re-attach event listeners after updating innerHTML
                document.getElementById("btn-accept-order")?.addEventListener("click", () => {
                    updateOrderStatus(order.id, "Đang giao");
                });
                document.getElementById("btn-complete-order")?.addEventListener("click", () => {
                    updateOrderStatus(order.id, "Đã hoàn thành");
                });
                document.getElementById("btn-report-issue")?.addEventListener("click", () => {
                    updateOrderStatus(order.id, "Đã hủy");
                });
            }
        };

        const updateOrderStatus = (orderId, newStatus) => {
            const orderIndex = DriverApp.mockOrders.findIndex((o) => o.id === orderId);
            if (orderIndex !== -1) {
                const updatedOrder = {
                    ...DriverApp.mockOrders[orderIndex],
                    status: newStatus
                };
                if (newStatus === "Đang giao") {
                    updatedOrder.estimatedDeliveryTime = new Date(Date.now() + 30 * 60 * 1000).toLocaleTimeString("vi-VN", {
                        hour: "2-digit",
                        minute: "2-digit",
                    });
                }
                DriverApp.mockOrders[orderIndex] = updatedOrder; // Update the mock data
                order = updatedOrder; // Update local order reference
                DriverApp.showToast({
                    title: "Cập nhật trạng thái thành công!",
                    description: `Đơn hàng ${order.id} đã được cập nhật thành ${newStatus}.`,
                    variant: "success",
                });
                updateOrderDetailUI(); // Re-render relevant parts of the UI
                DriverApp.initMapboxNavigation(order); // Re-initialize map with new status
            }
        };

        updateOrderDetailUI(); // Initial render of buttons/status
    },

    // Function to initialize Profile page logic
    initProfilePage: function() {
        let driver = {
            ...DriverApp.mockDriverProfile
        }; // Create a mutable copy for editing
        let isEditing = false;

        const renderProfileSection = () => {
            // Update the main profile card
            const profileCardTitle = document.querySelector('.profile-header-card .text-2xl');
            const profileCardDescription = document.querySelector('.profile-header-card .text-sm');
            const profileStatusSwitch = document.getElementById('driver-status');
            const profileStatusText = document.querySelector('.profile-header-card p.text-xs');
            const toggleEditButton = document.getElementById('toggle-edit-profile');

            if (profileCardTitle) profileCardTitle.textContent = driver.name;
            if (profileCardDescription) profileCardDescription.textContent = driver.phone;
            if (profileStatusSwitch) {
                profileStatusSwitch.setAttribute('aria-checked', driver.isActive);
                profileStatusSwitch.dataset.state = driver.isActive ? 'checked' : 'unchecked';
                profileStatusSwitch.querySelector('span').dataset.state = driver.isActive ? 'checked' : 'unchecked';
                profileStatusSwitch.querySelector('span').style.transform = driver.isActive ? 'translateX(20px)' : 'translateX(0px)';
                profileStatusSwitch.classList.toggle('bg-primary', driver.isActive);
                profileStatusSwitch.classList.toggle('bg-input', !driver.isActive);
            }
            if (profileStatusText) {
                profileStatusText.textContent = driver.isActive ? "Bạn đang sẵn sàng nhận đơn hàng mới." : "Bạn đang nghỉ. Bật để tiếp tục nhận đơn.";
            }
            if (toggleEditButton) {
                toggleEditButton.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 h-4 w-4"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/><path d="M19 13v3"/></svg> ${isEditing ? "Hủy" : "Chỉnh sửa"}`;
                toggleEditButton.classList.toggle('bg-primary', isEditing);
                toggleEditButton.classList.toggle('text-primary-foreground', isEditing);
                toggleEditButton.classList.toggle('hover:bg-primary/90', isEditing);
                toggleEditButton.classList.toggle('border', !isEditing);
                toggleEditButton.classList.toggle('border-input', !isEditing);
                toggleEditButton.classList.toggle('bg-background', !isEditing);
                toggleEditButton.classList.toggle('hover:bg-accent', !isEditing);
                toggleEditButton.classList.toggle('hover:text-accent-foreground', !isEditing);
            }

            // Update input fields and save button visibility
            const editableInputs = document.querySelectorAll('#profile-form input:not([disabled])');
            editableInputs.forEach(input => {
                input.disabled = !isEditing;
            });

            document.getElementById('name').value = driver.name;
            document.getElementById('phone').value = driver.phone;
            document.getElementById('bankName').value = driver.bankAccount.bankName;
            document.getElementById('accountNumber').value = driver.bankAccount.accountNumber;
            document.getElementById('accountHolderName').value = driver.bankAccount.accountHolderName;

            const saveButton = document.getElementById('save-profile-changes');
            if (saveButton) {
                saveButton.style.display = isEditing ? 'flex' : 'none';
            }
        };

        // Attach event listeners
        document.getElementById("toggle-edit-profile")?.addEventListener("click", () => {
            isEditing = !isEditing;
            renderProfileSection();
        });

        document.getElementById("driver-status")?.addEventListener("click", () => {
            driver.isActive = !driver.isActive;
            DriverApp.mockDriverProfile.isActive = driver.isActive; // Update global mock
            DriverApp.showToast({
                title: `Trạng thái hoạt động: ${driver.isActive ? "Bật" : "Tắt"}`,
                description: `Bạn đã ${driver.isActive ? "bật" : "tắt"} nhận đơn hàng mới.`,
                variant: driver.isActive ? "default" : "destructive",
            });
            renderProfileSection();
        });

        document.getElementById("name")?.addEventListener("input", (e) => driver.name = e.target.value);
        document.getElementById("phone")?.addEventListener("input", (e) => driver.phone = e.target.value);
        document.getElementById("bankName")?.addEventListener("input", (e) => driver.bankAccount.bankName = e.target.value);
        document.getElementById("accountNumber")?.addEventListener("input", (e) => driver.bankAccount.accountNumber = e.target.value);
        document.getElementById("accountHolderName")?.addEventListener("input", (e) => driver.bankAccount.accountHolderName = e.target.value);
        
        document.getElementById("save-profile-changes")?.addEventListener("click", () => {
            Object.assign(DriverApp.mockDriverProfile, driver); // Persist changes to global mock
            isEditing = false;
            DriverApp.showToast({
                title: "Cập nhật thành công!",
                description: "Thông tin cá nhân của bạn đã được lưu.",
                variant: "success",
            });
            renderProfileSection();
        });

        renderProfileSection(); // Initial render
    },

    // Function to initialize History page logic
    initHistoryPage: function(initialFilter) {
        let filter = initialFilter || "all";

        const renderHistoryList = () => {
            const now = new Date();
            const filteredHistory = DriverApp.mockDeliveryHistory
                .filter((entry) => {
                    const entryDate = new Date(entry.orderTime);
                    if (filter === "today") {
                        return entryDate.toDateString() === now.toDateString();
                    }
                    if (filter === "week") {
                        const oneWeekAgo = new Date(now);
                        oneWeekAgo.setDate(now.getDate() - 7);
                        return entryDate >= oneWeekAgo;
                    }
                    if (filter === "month") {
                        const oneMonthAgo = new Date(now);
                        oneMonthAgo.setMonth(now.getMonth() - 1);
                        return entryDate >= oneMonthAgo;
                    }
                    return true; // 'all'
                })
                .sort((a, b) => new Date(b.orderTime).getTime() - new Date(a.orderTime).getTime());

            const historyListContainer = document.getElementById('history-list-container');
            if (historyListContainer) {
                if (filteredHistory.length > 0) {
                    historyListContainer.innerHTML = filteredHistory.map(entry => `
            <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
              <div class="flex flex-col space-y-1.5 p-4 pb-2">
                <div class="flex justify-between items-start">
                  <div>
                    <h3 class="text-md font-semibold tracking-tight">Đơn hàng: ${entry.id}</h3>
                    <p class="text-xs text-muted-foreground flex items-center">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3 mr-1"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg> ${new Date(entry.orderTime).toLocaleString("vi-VN")}
                    </p>
                  </div>
                  <p class="font-semibold text-green-600 text-md">
                    +${DriverApp.formatCurrency(entry.driverEarnings)}
                  </p>
                </div>
              </div>
              <div class="p-4 pt-0 text-sm space-y-1">
                <p><strong>Khách hàng:</strong> ${entry.customerName}</p>
                <p><strong>Địa chỉ giao:</strong> ${entry.deliveryAddress}</p>
                ${entry.rating ? `
                  <div class="flex items-center">
                    <strong>Đánh giá:</strong>&nbsp;
                    ${Array.from({ length: 5 }).map((_, i) => `
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 ${i < entry.rating ? 'text-yellow-400 fill-yellow-400' : 'text-gray-300'}"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    `).join("")
                } <
                span class = "ml-1" > ($ {
                    entry.rating
                }) < /span> < /
                div >
                    ` : ""}
                ${entry.customerFeedback ? `
                  <p class="text-xs italic text-muted-foreground flex items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3 mr-1 mt-0.5 flex-shrink-0"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/></svg> "${entry.customerFeedback}"
                  </p>
                ` : ""}
              </div>
              <div class="flex items-center p-2 border-t">
                <a href="/driver/orders/${entry.id}" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-xs font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 text-primary underline-offset-4 hover:underline h-9 px-0 py-2">Xem chi tiết đơn</a>
              </div>
            </div>
          `).join(""): `
            <p class="text-muted-foreground text-center py-8">Không có lịch sử giao hàng nào cho bộ lọc này.</p>
          `;
        }
    }
};

document.getElementById("history-filter")?.addEventListener("change", (e) => {
    filter = e.target.value;
    renderHistoryList();
});

renderHistoryList(); // Initial render
},

// Function to initialize Notifications page logic
initNotificationsPage: function() {
    let notifications = [...DriverApp.mockNotifications].sort((a, b) => new Date(b.timestamp).getTime() - new Date(a.timestamp).getTime());
    let feedbackText = "";

    const getIconForType = (type) => {
        switch (type) {
            case "new_order":
                return `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 text-blue-500"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>`;
            case "status_update":
                return `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 text-green-500"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>`;
            case "system_message":
                return `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 text-yellow-500"><path d="m21.73 18.73-1.42-1.42A8 8 0 0 0 12 2C6.48 2 2 6.48 2 12s4.48 10 10 10c2.7 0 5.17-1.1 6.97-2.89l1.42-1.42"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>`;
            case "earning_report":
                return `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 text-purple-500"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>`;
            default:
                return `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 text-gray-500"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>`;
        }
    };

    const renderNotificationsSection = () => {
        const unreadCount = notifications.filter((n) => !n.read).length;
        const notificationsListContainer = document.getElementById('notifications-list-container');
        const unreadCountDisplay = document.getElementById('unread-count-display');
        const markAllReadButton = document.getElementById('mark-all-read');
        const feedbackTextarea = document.getElementById('feedback');

        if (unreadCountDisplay) {
            unreadCountDisplay.textContent = unreadCount > 0 ? `Bạn có ${unreadCount} thông báo chưa đọc.` : "Không có thông báo mới.";
        }
        if (markAllReadButton) {
            markAllReadButton.style.display = unreadCount > 0 ? 'inline-flex' : 'none';
        }
        if (feedbackTextarea) {
            feedbackTextarea.value = feedbackText;
        }

        if (notificationsListContainer) {
            notificationsListContainer.innerHTML = notifications.length > 0 ?
                notifications.map(notif => `
            <div class="${DriverApp.cn(
              "p-3 rounded-lg border flex items-start gap-3 transition-colors hover:bg-muted/50",
              notif.read ? "bg-muted/30 border-transparent" : "bg-background font-medium"
            )}">
              <div class="flex-shrink-0 mt-1">${getIconForType(notif.type)}</div>
              <div class="flex-grow">
                <p class="text-sm font-semibold">${notif.title}</p>
                <p class="${DriverApp.cn("text-xs", notif.read ? "text-muted-foreground" : "text-foreground/80")}">
                  ${notif.message}
                </p>
                <p class="text-xs text-muted-foreground mt-0.5">
                  ${new Date(notif.timestamp).toLocaleString("vi-VN")}
                </p>
                <div class="mt-1.5 space-x-2">
                  ${notif.link ? `
                    <a href="${notif.link}" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 text-primary underline-offset-4 hover:underline h-auto p-0">Xem chi tiết</a>
                  ` : ""}
                  ${!notif.read ? `
                    <button data-id="${notif.id}" class="mark-read-btn inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 text-primary underline-offset-4 hover:underline h-auto p-0">Đánh dấu đã đọc</button>
                  ` : ""}
                  <button data-id="${notif.id}" class="delete-notif-btn inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 text-destructive underline-offset-4 hover:underline h-auto p-0">Xóa</button>
                </div>
              </div>
            </div>
          `).join("") : `
            <p class="text-muted-foreground text-center py-8">Không có thông báo nào.</p>
          `;
        }

        // Re-attach event listeners
        document.querySelectorAll(".mark-read-btn").forEach(btn => {
            btn.addEventListener("click", (e) => {
                const id = e.currentTarget.dataset.id;
                const notif = notifications.find(n => n.id === id);
                if (notif) notif.read = true;
                renderNotificationsSection();
            });
        });

        document.querySelectorAll(".delete-notif-btn").forEach(btn => {
            btn.addEventListener("click", (e) => {
                const id = e.currentTarget.dataset.id;
                notifications = notifications.filter(n => n.id !== id);
                DriverApp.showToast({
                    title: "Đã xóa thông báo."
                });
                renderNotificationsSection();
            });
        });
    };

    document.getElementById("mark-all-read")?.addEventListener("click", () => {
        notifications.forEach(n => n.read = true);
        DriverApp.showToast({
            title: "Đã đánh dấu tất cả là đã đọc."
        });
        renderNotificationsSection();
    });

    document.getElementById("feedback")?.addEventListener("input", (e) => {
        feedbackText = e.target.value;
    });

    document.getElementById("submit-feedback")?.addEventListener("click", () => {
        if (feedbackText.trim() === "") {
            DriverApp.showToast({
                title: "Vui lòng nhập nội dung phản hồi.",
                variant: "destructive"
            });
            return;
        }
        console.log("Feedback submitted:", feedbackText);
        feedbackText = "";
        DriverApp.showToast({
            title: "Gửi phản hồi thành công!",
            description: "Cảm ơn bạn đã đóng góp ý kiến.",
            variant: "success",
        });
        renderNotificationsSection();
    });

    renderNotificationsSection(); // Initial render
}
};