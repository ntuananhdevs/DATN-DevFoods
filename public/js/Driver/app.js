// Main Application JavaScript

// Global App State
window.AppState = {
    user: null,
    isOnline: false,
    orders: [],
    notifications: [],
    earnings: {
      today: { total: 250000, orders: 5 },
      week: { total: 1750000, orders: 35 },
      month: { total: 7500000, orders: 150 },
    },
  }

  // Utility Functions
  const Utils = {
    // Format currency to Vietnamese Dong
    formatCurrency(amount) {
      return new Intl.NumberFormat("vi-VN", {
        style: "currency",
        currency: "VND",
      }).format(amount)
    },

    // Format time
    formatTime(timeString) {
      return new Date(timeString).toLocaleTimeString("vi-VN", {
        hour: "2-digit",
        minute: "2-digit",
      })
    },

    // Format date
    formatDate(timeString) {
      return new Date(timeString).toLocaleDateString("vi-VN", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
      })
    },

    // Show toast notification
    showToast(message, type = "info") {
      const container = document.getElementById("toast-container")
      if (!container) return

      const toast = document.createElement("div")
      toast.className = `toast ${type} fade-in`

      const icon = {
        success: "fas fa-check-circle text-green-600",
        error: "fas fa-exclamation-circle text-red-600",
        warning: "fas fa-exclamation-triangle text-yellow-600",
        info: "fas fa-info-circle text-blue-600",
      }[type]

      toast.innerHTML = `
              <div class="flex items-center gap-3">
                  <i class="${icon}"></i>
                  <span class="flex-1">${message}</span>
                  <button onclick="this.parentElement.parentElement.remove()" class="text-gray-400 hover:text-gray-600">
                      <i class="fas fa-times"></i>
                  </button>
              </div>
          `

      container.appendChild(toast)

      // Auto remove after 5 seconds
      setTimeout(() => {
        if (toast.parentElement) {
          toast.remove()
        }
      }, 5000)
    },

    // Local Storage helpers
    storage: {
      get(key) {
        try {
          const item = localStorage.getItem(key)
          return item ? JSON.parse(item) : null
        } catch {
          return null
        }
      },

      set(key, value) {
        try {
          localStorage.setItem(key, JSON.stringify(value))
          return true
        } catch {
          return false
        }
      },

      remove(key) {
        localStorage.removeItem(key)
      },
    },

    // API helpers
    api: {
      async request(url, options = {}) {
        try {
          const response = await fetch(url, {
            headers: {
              "Content-Type": "application/json",
              ...options.headers,
            },
            ...options,
          })

          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`)
          }

          return await response.json()
        } catch (error) {
          console.error("API request failed:", error)
          throw error
        }
      },
    },
  }

  // Navigation Helper
  const Navigation = {
    init() {
      this.updateActiveNavItem()
      this.bindEvents()
    },

    updateActiveNavItem() {
      const currentPage = this.getCurrentPage()
      const navItems = document.querySelectorAll(".nav-item")

      navItems.forEach((item) => {
        const page = item.getAttribute("data-page")
        if (page === currentPage) {
          item.classList.add("active")
        } else {
          item.classList.remove("active")
        }
      })
    },

    getCurrentPage() {
      const path = window.location.pathname
      const filename = path.split("/").pop().replace(".html", "")
      return filename || "dashboard"
    },

    bindEvents() {
      // Handle back button
      window.addEventListener("popstate", () => {
        this.updateActiveNavItem()
      })
    },
  }

  // Authentication Helper
  const Auth = {
    isLoggedIn() {
      return Utils.storage.get("isLoggedIn") === true
    },

    login(userData) {
      Utils.storage.set("isLoggedIn", true)
      Utils.storage.set("userData", userData)
      window.AppState.user = userData
    },

    logout() {
      Utils.storage.remove("isLoggedIn")
      Utils.storage.remove("userData")
      window.AppState.user = null
      window.location.href = "login.html"
    },

    requireAuth() {
      if (!this.isLoggedIn()) {
        window.location.href = "login.html"
        return false
      }
      return true
    },

    getUserData() {
      return Utils.storage.get("userData")
    },
  }

  // Order Management
  const OrderManager = {
    mockOrders: [
      {
        id: 1,
        customer_name: "Nguyễn Văn A",
        customer_phone: "0987654321",
        delivery_address: "123 Đường Láng, Đống Đa, Hà Nội",
        guest_latitude: 21.0285,
        guest_longitude: 105.8542,
        estimated_delivery_time: "2024-01-15T12:30:00",
        status: "assigned",
        total_amount: 202000,
        delivery_fee: 25000,
        notes: "Gọi điện trước khi đến",
        items: [
          { name: "Phở Bò Tái", quantity: 2, price: 80000 },
          { name: "Chả cá Lã Vọng", quantity: 1, price: 100000 },
        ],
      },
      {
        id: 2,
        guest_name: "Trần Thị B",
        guest_phone: "0912345678",
        delivery_address: "456 Phố Huế, Hai Bà Trưng, Hà Nội",
        guest_latitude: 21.0245,
        guest_longitude: 105.8516,
        estimated_delivery_time: "2024-01-15T13:00:00",
        status: "picked_up",
        total_amount: 141500,
        delivery_fee: 20000,
        notes: "Để ở bàn bảo vệ tầng 1",
        items: [
          { name: "Bún chả Hà Nội", quantity: 1, price: 70000 },
          { name: "Nem rán", quantity: 1, price: 50000 },
        ],
      },
    ],

    getOrders() {
      return Utils.storage.get("orders") || this.mockOrders
    },

    getOrder(id) {
      const orders = this.getOrders()
      return orders.find((order) => order.id == id)
    },

    updateOrderStatus(id, status) {
      const orders = this.getOrders()
      const orderIndex = orders.findIndex((order) => order.id == id)

      if (orderIndex !== -1) {
        orders[orderIndex].status = status
        if (status === "delivered") {
          orders[orderIndex].actual_delivery_time = new Date().toISOString()
        }
        Utils.storage.set("orders", orders)
        window.AppState.orders = orders
        return true
      }
      return false
    },

    getPendingOrders() {
      const orders = this.getOrders()
      return orders.filter((order) => order.status === "assigned" || order.status === "picked_up")
    },
  }

  // Initialize app when DOM is loaded
  document.addEventListener("DOMContentLoaded", () => {
    // Initialize navigation
    Navigation.init()

    // Load user data if logged in
    if (Auth.isLoggedIn()) {
      window.AppState.user = Auth.getUserData()
      window.AppState.orders = OrderManager.getOrders()
    }

    // Initialize page-specific functionality
    const currentPage = Navigation.getCurrentPage()
    if (window[currentPage + "Init"]) {
      window[currentPage + "Init"]()
    }
  })

  // Global functions for Alpine.js
  window.Utils = Utils
  window.Auth = Auth
  window.OrderManager = OrderManager
  window.Navigation = Navigation
