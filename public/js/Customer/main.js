document.addEventListener("DOMContentLoaded", () => {
    // Scroll Progress Bar
    const scrollProgressBar = document.querySelector(".scroll-progress-bar")
  
    window.addEventListener("scroll", () => {
      const scrollTop = window.scrollY
      const scrollHeight = document.documentElement.scrollHeight - window.innerHeight
      const scrollPercentage = (scrollTop / scrollHeight) * 100
      scrollProgressBar.style.width = scrollPercentage + "%"
    })
  
    // Mobile Menu Toggle
    const mobileMenuBtn = document.querySelector(".mobile-menu-btn")
    const mobileMenu = document.querySelector(".mobile-menu")
    const mobileMenuClose = document.querySelector(".mobile-menu-close")
  
    if (mobileMenuBtn && mobileMenu && mobileMenuClose) {
      mobileMenuBtn.addEventListener("click", () => {
        mobileMenu.classList.add("active")
        document.body.style.overflow = "hidden"
      })
  
      mobileMenuClose.addEventListener("click", () => {
        mobileMenu.classList.remove("active")
        document.body.style.overflow = ""
      })
    }
  
    // Header Scroll Effect
    const mainNav = document.querySelector(".main-nav")
    const logo = document.querySelector(".logo img")
  
    window.addEventListener("scroll", () => {
      if (window.scrollY > 50) {
        mainNav.classList.add("scrolled")
        // Xóa dòng thay đổi chiều cao logo
        // if (logo) logo.style.height = "3rem"
      } else {
        mainNav.classList.remove("scrolled")
        // Xóa dòng thay đổi chiều cao logo
        // if (logo) logo.style.height = "3.5rem"
      }
    })
  
    // Carousel
    const slides = document.querySelectorAll(".carousel-slide")
    const indicators = document.querySelectorAll(".indicator")
    const prevBtn = document.querySelector(".carousel-control.prev")
    const nextBtn = document.querySelector(".carousel-control.next")
  
    if (slides.length > 0) {
      let currentSlide = 0
      let slideInterval
  
      const startSlideshow = () => {
        slideInterval = setInterval(() => {
          goToSlide((currentSlide + 1) % slides.length)
        }, 5000)
      }
  
      const stopSlideshow = () => {
        clearInterval(slideInterval)
      }
  
      const goToSlide = (n) => {
        slides[currentSlide].classList.remove("active")
        indicators[currentSlide].classList.remove("active")
  
        currentSlide = n
  
        slides[currentSlide].classList.add("active")
        indicators[currentSlide].classList.add("active")
      }
  
      if (prevBtn && nextBtn) {
        prevBtn.addEventListener("click", () => {
          stopSlideshow()
          goToSlide(currentSlide === 0 ? slides.length - 1 : currentSlide - 1)
          startSlideshow()
        })
  
        nextBtn.addEventListener("click", () => {
          stopSlideshow()
          goToSlide((currentSlide + 1) % slides.length)
          startSlideshow()
        })
      }
  
      indicators.forEach((indicator, index) => {
        indicator.addEventListener("click", () => {
          stopSlideshow()
          goToSlide(index)
          startSlideshow()
        })
      })
  
      startSlideshow()
    }
  
    // Stats Counter Animation
    const statNumbers = document.querySelectorAll(".stat-number")
  
    if (statNumbers.length > 0) {
      const animateCounter = (element, target) => {
        let current = 0
        const increment = target > 1000 ? 50 : target > 100 ? 5 : 1
        const duration = 2000
        const steps = Math.ceil(target / increment)
        const stepTime = Math.floor(duration / steps)
  
        const timer = setInterval(() => {
          current += increment
          if (current >= target) {
            element.textContent = target + "+"
            clearInterval(timer)
          } else {
            element.textContent = current + "+"
          }
        }, stepTime)
      }
  
      const observer = new IntersectionObserver(
        (entries) => {
          entries.forEach((entry) => {
            if (entry.isIntersecting) {
              const target = Number.parseInt(entry.target.getAttribute("data-count"))
              animateCounter(entry.target, target)
              observer.unobserve(entry.target)
            }
          })
        },
        { threshold: 0.5 },
      )
  
      statNumbers.forEach((stat) => {
        observer.observe(stat)
      })
    }
  
    // Product Modal
    const productCards = document.querySelectorAll(".product-card")
    const productModal = document.getElementById("productModal")
    const modalClose = document.querySelector(".modal-close")
  
    // Sample product data
    const products = [
      {
        id: 1,
        name: "Gà Giòn Vui Vẻ (1 miếng)",
        description:
          "Gà rán giòn thơm ngon, hương vị đặc trưng của Jollibee với lớp bột chiên giòn rụm và thịt gà mềm, thơm ngon.",
        price: "40.000đ",
        image: "images/product-1.jpg",
        ingredients: ["Thịt gà tươi", "Bột chiên xù đặc biệt", "Gia vị Jollibee độc quyền", "Dầu thực vật"],
        allergens: ["Gluten", "Đậu nành"],
        nutritionalInfo: {
          calories: "290 kcal",
          protein: "17g",
          fat: "18g",
          carbs: "15g",
        },
        options: [
          { id: 1, name: "Đùi gà", price: "+0đ" },
          { id: 2, name: "Cánh gà", price: "+0đ" },
          { id: 3, name: "Ức gà", price: "+0đ" },
        ],
        addons: [
          { id: 1, name: "Khoai tây chiên (vừa)", price: "25.000đ" },
          { id: 2, name: "Nước ngọt (vừa)", price: "15.000đ" },
          { id: 3, name: "Sốt mayonnaise", price: "5.000đ" },
        ],
      },
      {
        id: 2,
        name: "Gà Sốt Cay (1 miếng)",
        description: "Gà rán phủ sốt cay đặc biệt, cay nồng hấp dẫn, thịt gà mềm, thơm ngon.",
        price: "45.000đ",
        image: "images/product-2.jpg",
        ingredients: [
          "Thịt gà tươi",
          "Bột chiên xù đặc biệt",
          "Sốt cay Jollibee",
          "Gia vị Jollibee độc quyền",
          "Dầu thực vật",
        ],
        allergens: ["Gluten", "Đậu nành", "Ớt"],
        nutritionalInfo: {
          calories: "320 kcal",
          protein: "18g",
          fat: "20g",
          carbs: "16g",
        },
        options: [
          { id: 1, name: "Đùi gà", price: "+0đ" },
          { id: 2, name: "Cánh gà", price: "+0đ" },
          { id: 3, name: "Ức gà", price: "+0đ" },
        ],
        addons: [
          { id: 1, name: "Khoai tây chiên (vừa)", price: "25.000đ" },
          { id: 2, name: "Nước ngọt (vừa)", price: "15.000đ" },
          { id: 3, name: "Sốt mayonnaise", price: "5.000đ" },
        ],
      },
      {
        id: 3,
        name: "Burger Gà Giòn",
        description: "Burger với lớp thịt gà giòn, rau tươi và sốt mayonnaise đặc biệt, đậm đà hương vị.",
        price: "50.000đ",
        image: "images/product-3.jpg",
        ingredients: ["Bánh mì burger", "Thịt gà chiên giòn", "Rau xà lách", "Cà chua", "Sốt mayonnaise đặc biệt"],
        allergens: ["Gluten", "Đậu nành", "Trứng"],
        nutritionalInfo: {
          calories: "450 kcal",
          protein: "22g",
          fat: "25g",
          carbs: "35g",
        },
        addons: [
          { id: 1, name: "Thêm phô mai", price: "10.000đ" },
          { id: 2, name: "Khoai tây chiên (vừa)", price: "25.000đ" },
          { id: 3, name: "Nước ngọt (vừa)", price: "15.000đ" },
        ],
      },
      {
        id: 4,
        name: "Mỳ Ý Sốt Bò Bằm",
        description: "Mỳ Ý với sốt bò bằm đậm đà, thơm ngon, kết hợp với phô mai và gia vị đặc biệt.",
        price: "45.000đ",
        originalPrice: "55.000đ",
        image: "images/product-4.jpg",
        ingredients: ["Mỳ Ý", "Thịt bò xay", "Sốt cà chua", "Phô mai", "Gia vị Ý đặc biệt"],
        allergens: ["Gluten", "Sữa"],
        nutritionalInfo: {
          calories: "520 kcal",
          protein: "25g",
          fat: "18g",
          carbs: "65g",
        },
        options: [
          { id: 1, name: "Cỡ vừa", price: "+0đ" },
          { id: 2, name: "Cỡ lớn", price: "+20.000đ" },
        ],
        addons: [
          { id: 1, name: "Thêm phô mai", price: "10.000đ" },
          { id: 2, name: "Bánh mì nướng tỏi", price: "15.000đ" },
          { id: 3, name: "Nước ngọt (vừa)", price: "15.000đ" },
        ],
      },
    ]
  
    if (productCards.length > 0 && productModal) {
      // Modal elements
      const modalProductImage = document.getElementById("modalProductImage")
      const modalProductTitle = document.getElementById("modalProductTitle")
      const modalProductPrice = document.getElementById("modalProductPrice")
      const modalProductOriginalPrice = document.getElementById("modalProductOriginalPrice")
      const modalProductDescription = document.getElementById("modalProductDescription")
      const modalProductOptions = document.getElementById("modalProductOptions")
      const modalProductAddons = document.getElementById("modalProductAddons")
      const modalIngredients = document.getElementById("modalIngredients")
      const modalAllergens = document.getElementById("modalAllergens")
      const modalAllergensContainer = document.getElementById("modalAllergensContainer")
      const modalNutrition = document.getElementById("modalNutrition")
      const modalTotalPrice = document.getElementById("modalTotalPrice")
  
      // Quantity controls
      const decreaseBtn = document.querySelector(".quantity-btn.decrease")
      const increaseBtn = document.querySelector(".quantity-btn.increase")
      const quantityValue = document.querySelector(".quantity-value")
  
      // Tab controls
      const tabBtns = document.querySelectorAll(".tab-btn")
      const tabPanels = document.querySelectorAll(".tab-panel")
  
      // Open modal with product details
      const openProductModal = (productId) => {
        const product = products.find((p) => p.id === productId)
  
        if (product) {
          // Set basic product info
          modalProductImage.src = product.image
          modalProductTitle.textContent = product.name
          modalProductPrice.textContent = product.price
          modalProductDescription.textContent = product.description
          modalTotalPrice.textContent = product.price
  
          // Set original price if exists
          if (product.originalPrice) {
            modalProductOriginalPrice.textContent = product.originalPrice
            modalProductOriginalPrice.style.display = "inline"
          } else {
            modalProductOriginalPrice.style.display = "none"
          }
  
          // Reset quantity
          quantityValue.textContent = "1"
  
          // Set options if exist
          if (product.options && product.options.length > 0) {
            modalProductOptions.innerHTML = ""
            product.options.forEach((option) => {
              const optionItem = document.createElement("div")
              optionItem.className = "option-item"
              optionItem.dataset.optionId = option.id
              optionItem.innerHTML = `
                              <div class="option-info">
                                  <input type="radio" name="option" id="option-${option.id}" ${option.id === 1 ? "checked" : ""}>
                                  <label for="option-${option.id}">${option.name}</label>
                              </div>
                              <span class="option-price">${option.price}</span>
                          `
              if (option.id === 1) {
                optionItem.classList.add("active")
              }
              optionItem.addEventListener("click", function () {
                document.querySelectorAll(".option-item").forEach((item) => {
                  item.classList.remove("active")
                })
                this.classList.add("active")
                document.getElementById(`option-${option.id}`).checked = true
                updateTotalPrice()
              })
              modalProductOptions.appendChild(optionItem)
            })
            document.querySelector(".product-options").style.display = "block"
          } else {
            document.querySelector(".product-options").style.display = "none"
          }
  
          // Set addons if exist
          if (product.addons && product.addons.length > 0) {
            modalProductAddons.innerHTML = ""
            product.addons.forEach((addon) => {
              const addonItem = document.createElement("div")
              addonItem.className = "addon-item"
              addonItem.dataset.addonId = addon.id
              addonItem.dataset.addonPrice = addon.price
              addonItem.innerHTML = `
                              <div class="addon-info">
                                  <input type="checkbox" id="addon-${addon.id}">
                                  <label for="addon-${addon.id}">${addon.name}</label>
                              </div>
                              <span class="addon-price">${addon.price}</span>
                          `
              addonItem.addEventListener("click", function () {
                const checkbox = this.querySelector('input[type="checkbox"]')
                checkbox.checked = !checkbox.checked
                this.classList.toggle("active", checkbox.checked)
                updateTotalPrice()
              })
              modalProductAddons.appendChild(addonItem)
            })
            document.querySelector(".product-addons").style.display = "block"
          } else {
            document.querySelector(".product-addons").style.display = "none"
          }
  
          // Set ingredients if exist
          if (product.ingredients && product.ingredients.length > 0) {
            modalIngredients.innerHTML = ""
            product.ingredients.forEach((ingredient) => {
              const li = document.createElement("li")
              li.textContent = ingredient
              modalIngredients.appendChild(li)
            })
          } else {
            modalIngredients.innerHTML = "<p>Thông tin đang được cập nhật.</p>"
          }
  
          // Set allergens if exist
          if (product.allergens && product.allergens.length > 0) {
            modalAllergens.innerHTML = ""
            product.allergens.forEach((allergen) => {
              const span = document.createElement("span")
              span.className = "allergen-tag"
              span.textContent = allergen
              modalAllergens.appendChild(span)
            })
            modalAllergensContainer.style.display = "block"
          } else {
            modalAllergensContainer.style.display = "none"
          }
  
          // Set nutrition info if exists
          if (product.nutritionalInfo) {
            modalNutrition.innerHTML = ""
            Object.entries(product.nutritionalInfo).forEach(([key, value]) => {
              const tr = document.createElement("tr")
              tr.innerHTML = `
                              <td>${key.charAt(0).toUpperCase() + key.slice(1)}</td>
                              <td>${value}</td>
                          `
              modalNutrition.appendChild(tr)
            })
          } else {
            modalNutrition.innerHTML = '<tr><td colspan="2">Thông tin dinh dưỡng đang được cập nhật.</td></tr>'
          }
  
          // Show modal
          productModal.classList.add("active")
          document.body.style.overflow = "hidden"
        }
      }
  
      // Update total price based on quantity, options and addons
      const updateTotalPrice = () => {
        const productId = Number.parseInt(document.querySelector(".product-card[data-product-id]").dataset.productId)
        const product = products.find((p) => p.id === productId)
  
        if (product) {
          let basePrice = Number.parseInt(product.price.replace(/\D/g, ""))
          const quantity = Number.parseInt(quantityValue.textContent)
  
          // Add option price if selected
          const selectedOption = document.querySelector(".option-item.active")
          if (selectedOption) {
            const optionPrice = selectedOption.querySelector(".option-price").textContent
            if (optionPrice !== "+0đ") {
              basePrice += Number.parseInt(optionPrice.replace(/[^\d]/g, ""))
            }
          }
  
          // Calculate total
          let total = basePrice * quantity
  
          // Add addon prices
          const selectedAddons = document.querySelectorAll('.addon-item input[type="checkbox"]:checked')
          selectedAddons.forEach((addon) => {
            const addonItem = addon.closest(".addon-item")
            const addonPrice = Number.parseInt(addonItem.dataset.addonPrice.replace(/\D/g, ""))
            total += addonPrice * quantity
          })
  
          // Update total price display
          modalTotalPrice.textContent = total.toLocaleString("vi-VN") + "đ"
        }
      }
  
      // Quantity controls
      if (decreaseBtn && increaseBtn && quantityValue) {
        decreaseBtn.addEventListener("click", () => {
          const quantity = Number.parseInt(quantityValue.textContent)
          if (quantity > 1) {
            quantityValue.textContent = quantity - 1
            updateTotalPrice()
          }
        })
  
        increaseBtn.addEventListener("click", () => {
          const quantity = Number.parseInt(quantityValue.textContent)
          quantityValue.textContent = quantity + 1
          updateTotalPrice()
        })
      }
  
      // Tab controls
      if (tabBtns.length > 0 && tabPanels.length > 0) {
        tabBtns.forEach((btn) => {
          btn.addEventListener("click", function () {
            const tabId = this.dataset.tab
  
            // Remove active class from all tabs and panels
            tabBtns.forEach((btn) => btn.classList.remove("active"))
            tabPanels.forEach((panel) => panel.classList.remove("active"))
  
            // Add active class to clicked tab and corresponding panel
            this.classList.add("active")
            document.getElementById(tabId).classList.add("active")
          })
        })
      }
  
      // Add click event to product cards
      productCards.forEach((card) => {
        card.addEventListener("click", function () {
          const productId = Number.parseInt(this.dataset.productId)
          openProductModal(productId)
        })
  
        // Prevent modal from opening when clicking add to cart button
        const addToCartBtn = card.querySelector(".add-to-cart-btn")
        if (addToCartBtn) {
          addToCartBtn.addEventListener("click", (e) => {
            e.stopPropagation()
            console.log("Add to cart:", card.dataset.productId)
          })
        }
      })
  
      // Close modal
      if (modalClose) {
        modalClose.addEventListener("click", () => {
          productModal.classList.remove("active")
          document.body.style.overflow = ""
        })
  
        // Close modal when clicking outside
        productModal.addEventListener("click", (e) => {
          if (e.target === productModal) {
            productModal.classList.remove("active")
            document.body.style.overflow = ""
          }
        })
      }
    }
  })
  