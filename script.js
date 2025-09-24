// =========================
// Global / Utilities
// =========================
let selectedProduct = null; // akan menyimpan produk yang sedang dipilih di popup

// =============================
// Drink selection functionality
// =============================
function selectDrink(index) {
  const drinks = document.querySelectorAll('.drink-item');
  drinks.forEach((drink, i) => {
    drink.style.background =
      i === index ? 'rgba(255,255,255,0.3)' : 'rgba(255,255,255,0.1)';
  });
}

// =============================
// Carousel functionality
// =============================
let currentSlide = 0;
const totalSlides = 3;

function changeSlide(index) {
  currentSlide = index;
  updateIndicators();
  console.log(`Slide changed to: ${index}`);
}

function updateIndicators() {
  const indicators = document.querySelectorAll('.indicator');
  indicators.forEach((indicator, i) => {
    indicator.classList.toggle('active', i === currentSlide);
  });
}

function autoAdvance() {
  currentSlide = (currentSlide + 1) % totalSlides;
  changeSlide(currentSlide);
}
setInterval(autoAdvance, 5000);

// =============================
// Filter & navigation & search
// =============================
function setActiveFilter(button) {
  document
    .querySelectorAll('.filter-btn')
    .forEach((btn) => btn.classList.remove('active'));
  button.classList.add('active');
  console.log(`Filter selected: ${button.textContent}`);
}

document.querySelectorAll('.nav-item').forEach((item) => {
  item.addEventListener('click', function (e) {
    e.preventDefault();
    document
      .querySelectorAll('.nav-item')
      .forEach((nav) => nav.classList.remove('active'));
    this.classList.add('active');
  });
});

const searchBox = document.querySelector('.search-box');
if (searchBox) {
  searchBox.addEventListener('keypress', function (e) {
    if (e.key === 'Enter') {
      const searchTerm = this.value.trim();
      if (searchTerm) {
        console.log(`Searching for: ${searchTerm}`);
        alert(`Searching for: ${searchTerm}`);
      }
    }
  });
}

// =============================
// Misc UI helpers
// =============================
function toggleMobileMenu() {
  const nav = document.querySelector('.nav-content');
  if (!nav) return;
  nav.style.display = nav.style.display === 'none' ? 'flex' : 'none';
}
document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
  anchor.addEventListener('click', function (e) {
    e.preventDefault();
    const target = document.querySelector(this.getAttribute('href'));
    if (target) target.scrollIntoView({ behavior: 'smooth' });
  });
});

// page load/scroll effects
window.addEventListener('load', function () {
  document.body.style.opacity = '1';
  document.body.style.transition = 'opacity 0.3s ease-in-out';
});
window.addEventListener('scroll', function () {
  const header = document.querySelector('.header');
  if (!header) return;
  header.style.boxShadow =
    window.scrollY > 100
      ? '0 4px 8px rgba(0,0,0,0.15)'
      : '0 2px 4px rgba(0,0,0,0.1)';
});

// initialize small UI things after DOM ready
document.addEventListener('DOMContentLoaded', function () {
  selectDrink(0);
  document.querySelectorAll('.drink-item').forEach((item) => {
    item.addEventListener('mouseenter', function () {
      this.style.transform = 'translateY(-5px) scale(1.05)';
    });
    item.addEventListener('mouseleave', function () {
      this.style.transform = 'translateY(0) scale(1)';
    });
  });
});

// =============================
// Scroll / nav buttons for product sections
// =============================
function scrollProducts(sectionId, direction) {
  const scrollContainer = document.getElementById(sectionId + '-scroll');
  if (!scrollContainer) return;
  const scrollAmount = 250;
  scrollContainer.scrollBy({
    left: direction === 'left' ? -scrollAmount : scrollAmount,
    behavior: 'smooth',
  });
  setTimeout(() => updateNavButtons(sectionId), 300);
}

function updateNavButtons(sectionId) {
  const scrollContainer = document.getElementById(sectionId + '-scroll');
  if (!scrollContainer) return;
  const section = scrollContainer.closest('.product-section');
  if (!section) return;
  const prevBtn = section.querySelector('.nav-btn:first-child');
  const nextBtn = section.querySelector('.nav-btn:last-child');
  if (prevBtn) prevBtn.disabled = scrollContainer.scrollLeft <= 0;
  if (nextBtn) {
    const maxScroll = scrollContainer.scrollWidth - scrollContainer.clientWidth;
    nextBtn.disabled = scrollContainer.scrollLeft >= maxScroll - 10;
  }
}

// =============================
// Toggle Favorite
// =============================
function toggleFavorite(btn, event) {
  event.stopPropagation();
  if (btn.classList.contains('liked')) {
    btn.classList.remove('liked');
    btn.textContent = '♡';
    btn.style.color = '#666';
  } else {
    btn.classList.add('liked');
    btn.textContent = '♥';
    btn.style.color = '#e74c3c';
  }
  btn.style.transform = 'scale(1.3)';
  setTimeout(() => (btn.style.transform = 'scale(1)'), 200);
}

// =============================
// Toast helper (versi final)
// =============================
function showToast(message) {
  let container = document.querySelector('.toast-container');
  if (!container) {
    container = document.createElement('div');
    container.className = 'toast-container';
    container.style.cssText = `
      position: fixed;
      top: 20px;
      right: 20px;
      display: flex;
      flex-direction: column;
      gap: 10px;
      z-index: 9999;
    `;
    document.body.appendChild(container);
  }

  const toast = document.createElement('div');
  toast.className = 'toast';
  toast.innerHTML = `
    <span style="font-size:16px;margin-right:8px;display:inline-flex;align-items:center;">✅</span> ${message}
  `;
  toast.style.cssText = `
    display: flex;
    align-items: center;
    background: #00754a;
    color: #fff;
    padding: 12px 18px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    opacity: 0;
    transform: translateX(100%);
    transition: opacity 0.4s ease, transform 0.4s ease;
  `;

  container.appendChild(toast);
  setTimeout(() => {
    toast.style.opacity = '1';
    toast.style.transform = 'translateX(0)';
  }, 50);
  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateX(100%)';
    setTimeout(() => toast.remove(), 400);
  }, 3000);
}

let selectProduct = null; // simpan produk yang dipilih

// =============================
// OPEN POPUP (klik tombol +)
// =============================
function openProductPopup(btn, event) {
  event.stopPropagation();

  const productCard = btn.closest('.product-card');
  const productName = productCard.querySelector('.product-name').textContent;
  const priceElement = productCard.querySelector('.product-price');
  const productImage = productCard.querySelector('img').getAttribute('src');

  // Simpan semua harga size
  const prices = {
    Small: parseInt(priceElement.dataset.small || 0, 10),
    Medium: parseInt(priceElement.dataset.medium || 0, 10),
    Large: parseInt(priceElement.dataset.large || 0, 10),
  };

  // Simpan produk ke global
  selectedProduct = {
    name: productName,
    image: productImage,
    prices: prices,
    price: prices.Medium, // default Medium
  };

  // Isi popup
  const popup = document.getElementById('productPopup');
  if (popup) {
    document.getElementById('popupName').textContent = productName;
    document.getElementById('popupPrice').textContent =
      'Rp ' + prices.Medium.toLocaleString();
    document.getElementById('popupImage').src = productImage;
    document.getElementById('popupSize').value = 'Medium'; // default
    popup.style.display = 'flex';
  }
}

// =============================
// UPDATE HARGA ketika pilih size
// =============================
document.addEventListener('DOMContentLoaded', function () {
  const sizeSelect = document.getElementById('popupSize');
  if (sizeSelect) {
    sizeSelect.addEventListener('change', function () {
      if (selectedProduct) {
        const chosenSize = this.value;
        const newPrice = selectedProduct.prices[chosenSize] || 0;
        selectedProduct.price = newPrice; // update harga global
        document.getElementById('popupPrice').textContent =
          'Rp ' + newPrice.toLocaleString();
      }
    });
  }
});

// =============================
// CONFIRM ADD TO CART
// =============================
function confirmAddToCart() {
  if (!selectedProduct) return;

  let cart = JSON.parse(localStorage.getItem('cart')) || [];

  const newItem = {
    id: Date.now(),
    name: selectedProduct.name,
    price: selectedProduct.price, // harga sesuai size terpilih
    image: selectedProduct.image,
    variant: document.getElementById('popupVariant').value,
    sugar: document.getElementById('popupSugar').value,
    size: document.getElementById('popupSize').value,
    outlet: document.getElementById('popupOutlet').value,
    orderType: document.getElementById('popupOrderType').value,
    quantity: 1,
  };

  // Cek kalau produk sama (nama + variant + size + sugar + outlet + orderType)
  const existingProduct = cart.find(
    (item) =>
      item.name === newItem.name &&
      item.variant === newItem.variant &&
      item.sugar === newItem.sugar &&
      item.size === newItem.size &&
      item.outlet === newItem.outlet &&
      item.orderType === newItem.orderType
  );

  if (existingProduct) {
    existingProduct.quantity += 1;
  } else {
    cart.push(newItem);
  }

  localStorage.setItem('cart', JSON.stringify(cart));

  showToast(`${newItem.name} (${newItem.size}) berhasil ditambahkan!`);
  updateCartBadge();
  closePopup();
}

// =============================
// CLOSE POPUP
// =============================
function closePopup() {
  const popup = document.getElementById('productPopup');
  if (popup) popup.style.display = 'none';
  selectedProduct = null;
}

// =============================
// UPDATE CART BADGE
// =============================
function updateCartBadge() {
  const cart = JSON.parse(localStorage.getItem('cart')) || [];
  const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);

  const badge = document.getElementById('cartBadge');
  if (badge) {
    badge.textContent = totalItems;
    badge.style.display = totalItems > 0 ? 'inline-block' : 'none';
  }
}

// =============================
// TOAST NOTIFICATION
// =============================
function showToast(message) {
  let toast = document.createElement('div');
  toast.className = 'toast-message';
  toast.textContent = message;

  document.body.appendChild(toast);

  setTimeout(() => {
    toast.classList.add('show');
  }, 100);

  setTimeout(() => {
    toast.classList.remove('show');
    setTimeout(() => toast.remove(), 300);
  }, 2500);
}

// =============================
// UPDATE CART BADGE
// =============================
function updateCartBadge() {
  const cart = JSON.parse(localStorage.getItem("cart")) || [];
  const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);

  const badge = document.getElementById("cartCount"); // ✅ sesuai HTML kamu
  if (badge) {
    badge.textContent = totalItems;
    badge.style.display = totalItems > 0 ? "inline-block" : "none";
  }

  // Sekalian update isi keranjang kalau ada di cart page
  renderCartItems();
}

// =============================
// RENDER CART ITEMS
// =============================
function renderCartItems() {
  const cart = JSON.parse(localStorage.getItem("cart")) || [];
  const container = document.getElementById("cartItems");
  const totalEl = document.getElementById("cartTotal");

  if (!container) return; // kalau bukan di cart.html, skip

  container.innerHTML = "";

  if (cart.length === 0) {
    container.innerHTML = `<p class="empty-cart">Keranjang kosong 😢</p>`;
    if (totalEl) totalEl.textContent = "Rp 0";
    return;
  }

  let totalPrice = 0;

  cart.forEach((item, index) => {
    const itemEl = document.createElement("div");
    itemEl.className = "cart-item";
    itemEl.innerHTML = `
      <img src="${item.image}" alt="${item.name}" class="cart-item-img">
      <div class="cart-item-info">
        <h3>${item.name}</h3>
        <p>${item.variant}, ${item.size}, ${item.sugar}</p>
        <p>Rp ${item.price.toLocaleString()}</p>
        <div class="cart-qty">
          <button onclick="changeQuantity(${index}, -1)">-</button>
          <span>${item.quantity}</span>
          <button onclick="changeQuantity(${index}, 1)">+</button>
        </div>
      </div>
      <button class="remove-btn" onclick="removeFromCart(${index})">✖</button>
    `;

    container.appendChild(itemEl);
    totalPrice += item.price * item.quantity;
  });

  if (totalEl) {
    totalEl.textContent = "Rp " + totalPrice.toLocaleString();
  }
}

// =============================
// CHANGE QUANTITY
// =============================
function changeQuantity(index, delta) {
  let cart = JSON.parse(localStorage.getItem("cart")) || [];
  if (!cart[index]) return;

  cart[index].quantity += delta;

  if (cart[index].quantity <= 0) {
    cart.splice(index, 1); // hapus kalau qty 0
  }

  localStorage.setItem("cart", JSON.stringify(cart));
  updateCartBadge();
}

// =============================
// REMOVE ITEM
// =============================
function removeFromCart(index) {
  let cart = JSON.parse(localStorage.getItem("cart")) || [];
  if (!cart[index]) return;

  cart.splice(index, 1);
  localStorage.setItem("cart", JSON.stringify(cart));

  updateCartBadge();
  showToast("Item dihapus dari keranjang!");
}

// =============================
// TOAST NOTIFICATION
// =============================
function showToast(message) {
  let toast = document.createElement("div");
  toast.className = "toast-message";
  toast.textContent = message;

  document.body.appendChild(toast);

  setTimeout(() => {
    toast.classList.add("show");
  }, 100);

  setTimeout(() => {
    toast.classList.remove("show");
    setTimeout(() => toast.remove(), 300);
  }, 2500);
}

// =============================
// INIT
// =============================
document.addEventListener("DOMContentLoaded", updateCartBadge);

// LOGIN PAGE
// script.js
document.addEventListener("DOMContentLoaded", () => {
  console.log("script.js loaded");

  const openBtn = document.getElementById("openLogin");
  const overlay = document.getElementById("overlay");
  const closeBtn = document.getElementById("closeLogin");

  if (!openBtn) console.error("openLogin button not found (id=openLogin)");
  if (!overlay) console.error("overlay element not found (id=overlay)");
  if (!closeBtn) console.error("close button not found (id=closeLogin)");

  function openPopup() {
    if (!overlay) return;
    overlay.classList.add("open");
    // Disable page scroll while modal open
    document.documentElement.style.overflow = "hidden";
    document.body.style.overflow = "hidden";
  }

  function closePopup() {
    if (!overlay) return;
    overlay.classList.remove("open");
    // restore scroll
    document.documentElement.style.overflow = "";
    document.body.style.overflow = "";
  }

  if (openBtn) {
    openBtn.addEventListener("click", (e) => {
      e.preventDefault();
      openPopup();
    });
  }

  if (closeBtn) {
    closeBtn.addEventListener("click", (e) => {
      e.preventDefault();
      closePopup();
    });
  }

  // click outside to close
  if (overlay) {
    overlay.addEventListener("click", (e) => {
      if (e.target === overlay) {
        closePopup();
      }
    });
  }

  // esc to close
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" || e.key === "Esc") {
      // only close if overlay is open
      if (overlay && overlay.classList.contains("open")) closePopup();
    }
  });
});


// SIGNUP

// Ambil elemen modal
const signupModal = document.getElementById("signupModal");
const openSignup = document.getElementById("openSignup"); // Pastikan openSignup ada di halaman
const closeSignup = document.getElementById("closeSignup");

// Ambil form di dalam modal
const signupForm = document.getElementById("signupForm");

// Buka modal saat klik "Sign up"
openSignup.addEventListener("click", (e) => {
  e.preventDefault();
  signupModal.style.display = "flex"; // Menampilkan modal
});

// Tutup modal saat klik close
closeSignup.addEventListener("click", () => {
  signupModal.style.display = "none"; // Menyembunyikan modal
  signupForm.reset(); // Menghapus data form
  location.reload(); // Refresh halaman
});

// Tutup modal jika klik di luar konten
window.addEventListener("click", (e) => {
  if (e.target === signupModal) {
    signupModal.style.display = "none"; // Menyembunyikan modal
    signupForm.reset(); // Menghapus data form
    location.reload(); // Refresh halaman
  }
});

// Submit form SignUp secara tradisional (Tanpa AJAX)
signupForm.addEventListener('submit', function (e) {
  e.preventDefault(); // Menghindari pengiriman form secara default

  // Ambil data form dan kirim menggunakan form submit biasa
  this.submit(); // Kirim form secara tradisional
});

// END SIGNUP