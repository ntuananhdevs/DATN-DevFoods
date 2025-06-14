// // Main Application JavaScript

// // Global App State
// window.AppState = {
//   user: null,
//   isOnline: false,
//   orders: [],
//   notifications: [],
//   earnings: {
//     today: { total: 250000, orders: 5 },
//     week: { total: 1750000, orders: 35 },
//     month: { total: 7500000, orders: 150 },
//   },
// }

// // Utility Functions
// const Utils = {
//   // Format currency to Vietnamese Dong
//   formatCurrency(amount) {
//     return new Intl.NumberFormat("vi-VN", {
//       style: "currency",
//       currency: "VND",
//     }).format(amount)
//   },

//   // Format time
//   formatTime(timeString) {
//     return new Date(timeString).toLocaleTimeString("vi-VN", {
//       hour: "2-digit",
//       minute: "2-digit",
//     })
//   },

//   // Format date
//   formatDate(timeString) {
//     return new Date(timeString).toLocaleDateString("vi-VN", {
//       day: "2-digit",
//       month: "2-digit",
//       year: "numeric",
//     })
//   },

//   // Show toast notification
//   showToast(message, type = "info") {
//     const container = document.getElementById("toast-container")
//     if (!container) return

//     const toast = document.createElement("div")
//     toast.className = `toast ${type} fade-in`

//     const icon = {
//       success: "fas fa-check-circle text-green-600",
//       error: "fas fa-exclamation-circle text-red-600",
//       warning: "fas fa-exclamation-triangle text-yellow-600",
//       info: "fas fa-info-circle text-blue-600",
//     }[type]

//     toast.innerHTML = `
//             <div class="flex items-center gap-3">
//                 <i class="${icon}"></i>
//                 <span class="flex-1">${message}</span>
//                 <button onclick="this.parentElement.parentElement.remove()" class="text-gray-400 hover:text-gray-600">
//                     <i class="fas fa-times"></i>
//                 </button>
//             </div>
//         `

//     container.appendChild(toast)

//     // Auto remove after 5 seconds
//     setTimeout(() => {
//       if (toast.parentElement) {
//         toast.remove()
//       }
//     }, 5000)
//   },

//   // Local Storage helpers
//   storage: {
//     get(key) {
//       try {
//         const item = localStorage.getItem(key)
//         return item ? JSON.parse(item) : null
//       } catch {
//         return null
//       }
//     },

//     set(key, value) {
//       try {
//         localStorage.setItem(key, JSON.stringify(value))
//         return true
//       } catch {
//         return false
//       }
//     },

//     remove(key) {
//       localStorage.removeItem(key)
//     },
//   },

//   // API helpers
//   api: {
//     async request(url, options = {}) {
//       try {
//         const response = await fetch(url, {
//           headers: {
//             "Content-Type": "application/json",
//             ...options.headers,
//           },
//           ...options,
//         })

//         if (!response.ok) {
//           throw new Error(`HTTP error! status: ${response.status}`)
//         }

//         return await response.json()
//       } catch (error) {
//         console.error("API request failed:", error)
//         throw error
//       }
//     },
//   },
// }

// // Navigation Helper
// const Navigation = {
//   init() {
//     this.updateActiveNavItem()
//     this.bindEvents()
//   },

//   updateActiveNavItem() {
//     const currentPage = this.getCurrentPage()
//     const navItems = document.querySelectorAll(".nav-item")

//     navItems.forEach((item) => {
//       const page = item.getAttribute("data-page")
//       if (page === currentPage) {
//         item.classList.add("active")
//       } else {
//         item.classList.remove("active")
//       }
//     })
//   },

//   getCurrentPage() {
//     const path = window.location.pathname
//     const filename = path.split("/").pop().replace(".html", "")
//     return filename || "dashboard"
//   },

//   bindEvents() {
//     // Handle back button
//     window.addEventListener("popstate", () => {
//       this.updateActiveNavItem()
//     })
//   },
// }

// // Authentication Helper
// const Auth = {
//   isLoggedIn() {
//     return Utils.storage.get("isLoggedIn") === true
//   },

//   login(userData) {
//     Utils.storage.set("isLoggedIn", true)
//     Utils.storage.set("userData", userData)
//     window.AppState.user = userData
//   },

//   logout() {
//     Utils.storage.remove("isLoggedIn")
//     Utils.storage.remove("userData")
//     window.AppState.user = null
//     window.location.href = "login.html"
//   },

//   requireAuth() {
//     if (!this.isLoggedIn()) {
//       window.location.href = "login.html"
//       return false
//     }
//     return true
//   },

//   getUserData() {
//     return Utils.storage.get("userData")
//   },
// }

// // Order Management
// const OrderManager = {
//   mockOrders: [
//     {
//       id: 1,
//       customer_name: "Nguyễn Văn A",
//       customer_phone: "0987654321",
//       delivery_address: "123 Đường Láng, Đống Đa, Hà Nội",
//       guest_latitude: 21.0285,
//       guest_longitude: 105.8542,
//       estimated_delivery_time: "2024-01-15T12:30:00",
//       status: "assigned",
//       total_amount: 202000,
//       delivery_fee: 25000,
//       notes: "Gọi điện trước khi đến",
//       items: [
//         { name: "Phở Bò Tái", quantity: 2, price: 80000 },
//         { name: "Chả cá Lã Vọng", quantity: 1, price: 100000 },
//       ],
//     },
//     {
//       id: 2,
//       guest_name: "Trần Thị B",
//       guest_phone: "0912345678",
//       delivery_address: "456 Phố Huế, Hai Bà Trưng, Hà Nội",
//       guest_latitude: 21.0245,
//       guest_longitude: 105.8516,
//       estimated_delivery_time: "2024-01-15T13:00:00",
//       status: "picked_up",
//       total_amount: 141500,
//       delivery_fee: 20000,
//       notes: "Để ở bàn bảo vệ tầng 1",
//       items: [
//         { name: "Bún chả Hà Nội", quantity: 1, price: 70000 },
//         { name: "Nem rán", quantity: 1, price: 50000 },
//       ],
//     },
//   ],

//   getOrders() {
//     return Utils.storage.get("orders") || this.mockOrders
//   },

//   getOrder(id) {
//     const orders = this.getOrders()
//     return orders.find((order) => order.id == id)
//   },

//   updateOrderStatus(id, status) {
//     const orders = this.getOrders()
//     const orderIndex = orders.findIndex((order) => order.id == id)

//     if (orderIndex !== -1) {
//       orders[orderIndex].status = status
//       if (status === "delivered") {
//         orders[orderIndex].actual_delivery_time = new Date().toISOString()
//       }
//       Utils.storage.set("orders", orders)
//       window.AppState.orders = orders
//       return true
//     }
//     return false
//   },

//   getPendingOrders() {
//     const orders = this.getOrders()
//     return orders.filter((order) => order.status === "assigned" || order.status === "picked_up")
//   },
// }

// // Initialize app when DOM is loaded
// document.addEventListener("DOMContentLoaded", () => {
//   // Initialize navigation
//   Navigation.init()

//   // Load user data if logged in
//   if (Auth.isLoggedIn()) {
//     window.AppState.user = Auth.getUserData()
//     window.AppState.orders = OrderManager.getOrders()
//   }

//   // Initialize page-specific functionality
//   const currentPage = Navigation.getCurrentPage()
//   if (window[currentPage + "Init"]) {
//     window[currentPage + "Init"]()
//   }
// })

// // Global functions for Alpine.js
// window.Utils = Utils
// window.Auth = Auth
// window.OrderManager = OrderManager
// window.Navigation = Navigation


// New - NVG
// Global JavaScript functions for the delivery app

// Import mapboxgl
const mapboxgl = require("mapbox-gl")

// GPS and Location Services
class LocationService {
  constructor() {
    this.currentPosition = null
    this.watchId = null
    this.isTracking = false
  }

  startTracking() {
    if (!navigator.geolocation) {
      console.error("Geolocation is not supported by this browser.")
      return
    }

    this.isTracking = true
    this.watchId = navigator.geolocation.watchPosition(
      (position) => {
        this.currentPosition = {
          lat: position.coords.latitude,
          lng: position.coords.longitude,
          accuracy: position.coords.accuracy,
          timestamp: position.timestamp,
        }

        this.updateLocationOnServer()
        this.onLocationUpdate(this.currentPosition)
      },
      (error) => {
        console.error("GPS Error:", error)
        this.onLocationError(error)
      },
      {
        enableHighAccuracy: true,
        timeout: 10000,
        maximumAge: 60000,
      },
    )
  }

  stopTracking() {
    if (this.watchId) {
      navigator.geolocation.clearWatch(this.watchId)
      this.watchId = null
      this.isTracking = false
    }
  }

  updateLocationOnServer() {
    if (!this.currentPosition) return

    fetch("/api/update-location", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
      },
      body: JSON.stringify(this.currentPosition),
    }).catch((error) => {
      console.error("Failed to update location on server:", error)
    })
  }

  onLocationUpdate(position) {
    // Override this method in specific pages
    console.log("Location updated:", position)
  }

  onLocationError(error) {
    // Override this method in specific pages
    console.error("Location error:", error)
  }

  getCurrentPosition() {
    return new Promise((resolve, reject) => {
      if (this.currentPosition) {
        resolve(this.currentPosition)
        return
      }

      navigator.geolocation.getCurrentPosition(
        (position) => {
          const pos = {
            lat: position.coords.latitude,
            lng: position.coords.longitude,
            accuracy: position.coords.accuracy,
          }
          resolve(pos)
        },
        (error) => {
          reject(error)
        },
        {
          enableHighAccuracy: true,
          timeout: 10000,
          maximumAge: 300000,
        },
      )
    })
  }
}

// Map Service for Mapbox integration
class MapService {
  constructor(accessToken) {
    mapboxgl.accessToken = accessToken
    this.map = null
    this.markers = []
    this.routes = []
  }

  initMap(containerId, options = {}) {
    const defaultOptions = {
      style: "mapbox://styles/mapbox/streets-v11",
      center: [105.8194, 21.0227], // Hanoi center
      zoom: 13,
    }

    this.map = new mapboxgl.Map({
      container: containerId,
      ...defaultOptions,
      ...options,
    })

    return this.map
  }

  addMarker(coordinates, options = {}) {
    const marker = new mapboxgl.Marker(options).setLngLat([coordinates.lng, coordinates.lat]).addTo(this.map)

    this.markers.push(marker)
    return marker
  }

  addRoute(coordinates, options = {}) {
    const routeId = "route-" + Date.now()

    this.map.addSource(routeId, {
      type: "geojson",
      data: {
        type: "Feature",
        properties: {},
        geometry: {
          type: "LineString",
          coordinates: coordinates,
        },
      },
    })

    this.map.addLayer({
      id: routeId,
      type: "line",
      source: routeId,
      layout: {
        "line-join": "round",
        "line-cap": "round",
      },
      paint: {
        "line-color": options.color || "#3b82f6",
        "line-width": options.width || 6,
      },
    })

    this.routes.push(routeId)
    return routeId
  }

  getDirections(start, end) {
    const url = `https://api.mapbox.com/directions/v5/mapbox/driving/${start.lng},${start.lat};${end.lng},${end.lat}?steps=true&geometries=geojson&access_token=${mapboxgl.accessToken}`

    return fetch(url)
      .then((response) => response.json())
      .then((data) => {
        if (data.routes && data.routes.length > 0) {
          return data.routes[0]
        }
        throw new Error("No route found")
      })
  }

  fitBounds(coordinates, padding = 50) {
    const bounds = new mapboxgl.LngLatBounds()
    coordinates.forEach((coord) => {
      bounds.extend([coord.lng, coord.lat])
    })
    this.map.fitBounds(bounds, { padding })
  }

  clearMarkers() {
    this.markers.forEach((marker) => marker.remove())
    this.markers = []
  }

  clearRoutes() {
    this.routes.forEach((routeId) => {
      if (this.map.getLayer(routeId)) {
        this.map.removeLayer(routeId)
      }
      if (this.map.getSource(routeId)) {
        this.map.removeSource(routeId)
      }
    })
    this.routes = []
  }
}

// Notification Service
class NotificationService {
  static show(message, type = "info", duration = 3000) {
    const notification = document.createElement("div")
    notification.className = `fixed top-20 left-4 right-4 z-50 p-4 rounded-lg shadow-lg ${this.getTypeClass(type)}`
    notification.textContent = message

    document.body.appendChild(notification)

    setTimeout(() => {
      notification.remove()
    }, duration)
  }

  static getTypeClass(type) {
    switch (type) {
      case "success":
        return "bg-green-600 text-white"
      case "error":
        return "bg-red-600 text-white"
      case "warning":
        return "bg-yellow-600 text-white"
      default:
        return "bg-blue-600 text-white"
    }
  }

  static requestPermission() {
    if ("Notification" in window && Notification.permission === "default") {
      Notification.requestPermission()
    }
  }

  static push(title, options = {}) {
    if ("Notification" in window && Notification.permission === "granted") {
      new Notification(title, {
        icon: "/favicon.ico",
        ...options,
      })
    }
  }
}

// Audio Service for voice navigation
class AudioService {
  constructor() {
    this.enabled = true
    this.synth = window.speechSynthesis
  }

  speak(text, options = {}) {
    if (!this.enabled || !this.synth) return

    const utterance = new SpeechSynthesisUtterance(text)
    utterance.lang = "vi-VN"
    utterance.rate = options.rate || 1
    utterance.pitch = options.pitch || 1
    utterance.volume = options.volume || 1

    this.synth.speak(utterance)
  }

  toggle() {
    this.enabled = !this.enabled
    if (!this.enabled) {
      this.synth.cancel()
    }
    return this.enabled
  }

  stop() {
    this.synth.cancel()
  }
}

// Initialize global services
const locationService = new LocationService()
const notificationService = NotificationService
const audioService = new AudioService()

// Global utility functions
function formatCurrency(amount) {
  return new Intl.NumberFormat("vi-VN", {
    style: "currency",
    currency: "VND",
  })
    .format(amount)
    .replace("₫", "đ")
}

function formatDistance(meters) {
  if (meters < 1000) {
    return Math.round(meters) + "m"
  }
  return (meters / 1000).toFixed(1) + "km"
}

function formatDuration(seconds) {
  const minutes = Math.round(seconds / 60)
  if (minutes < 60) {
    return minutes + " phút"
  }
  const hours = Math.floor(minutes / 60)
  const remainingMinutes = minutes % 60
  return hours + "h " + remainingMinutes + "p"
}

function debounce(func, wait) {
  let timeout
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout)
      func(...args)
    }
    clearTimeout(timeout)
    timeout = setTimeout(later, wait)
  }
}

// Initialize app when DOM is loaded
document.addEventListener("DOMContentLoaded", () => {
  // Request notification permission
  notificationService.requestPermission()

  // Start location tracking if on relevant pages
  const trackingPages = ["dashboard", "orders", "navigate"]
  const currentPage = document.body.dataset.page

  if (trackingPages.includes(currentPage)) {
    locationService.startTracking()
  }

  // Add click handlers for common elements
  document.querySelectorAll('[data-action="call"]').forEach((button) => {
    button.addEventListener("click", function () {
      const phone = this.dataset.phone
      if (phone) {
        window.location.href = `tel:${phone}`
      }
    })
  })

  // Add confirmation dialogs for important actions
  document.querySelectorAll("[data-confirm]").forEach((button) => {
    button.addEventListener("click", function (e) {
      const message = this.dataset.confirm
      if (!confirm(message)) {
        e.preventDefault()
      }
    })
  })
})

// Cleanup when page unloads
window.addEventListener("beforeunload", () => {
  locationService.stopTracking()
  audioService.stop()
})
