// =========================
// CartPage Script (Cart.js)
// =========================

// Render cart items dari localStorage
function renderCartItems() {
  const cartItemsContainer = document.getElementById("cartItems");
  cartItemsContainer.innerHTML = "";

  // Ambil cart dari localStorage
  let cart = JSON.parse(localStorage.getItem("cart")) || [];

  cart.forEach((product, index) => {
    const cartItem = document.createElement("div");
    cartItem.className = "cart-item";

    cartItem.innerHTML = `
      <div class="product-info">
        <div class="product-image">
          <img src="${product.image}" alt="${product.name}" onerror="this.style.display='none'">
        </div>
        <div class="product-details">
          <h3>${product.name}</h3>
          <p>${product.variant || "-"} • ${product.sugar || "-"} • ${product.size || "-"}</p>
          <small>Outlet: ${product.outlet || "-"}</small><br>
          <small>Order: ${product.orderType || "-"}</small>
        </div>
      </div>

      <!-- Quantity -->
      <div class="quantity-control">
        <button class="quantity-btn" onclick="updateQuantity(${index}, -1)">−</button>
        <span class="quantity-value">${product.quantity}</span>
        <button class="quantity-btn" onclick="updateQuantity(${index}, 1)">+</button>
      </div>

      <!-- Total Price -->
      <div class="item-price">${formatPrice(product.price * product.quantity)}</div>

      <!-- Remove Button -->
      <button class="remove-btn" onclick="removeItem(${index})">×</button>
    `;
    cartItemsContainer.appendChild(cartItem);
  });

  updateSummary();
}

// Format harga langsung Rupiah
function formatPrice(price) {
  if (!price || isNaN(price)) return "Rp 0";
  return "Rp " + price.toLocaleString("id-ID");
}


// Update Quantity
function updateQuantity(index, change) {
  let cart = JSON.parse(localStorage.getItem("cart")) || [];
  cart[index].quantity = Math.max(1, cart[index].quantity + change);
  localStorage.setItem("cart", JSON.stringify(cart));
  renderCartItems();
}

// Remove Item
function removeItem(index) {
  let cart = JSON.parse(localStorage.getItem("cart")) || [];
  if (confirm("Hapus item ini dari keranjang?")) {
    cart.splice(index, 1);
    localStorage.setItem("cart", JSON.stringify(cart));
    renderCartItems();
  }
}

// Update Summary (pakai Rupiah langsung)
function updateSummary() {
  let cart = JSON.parse(localStorage.getItem("cart")) || [];
  const subtotal = cart.reduce(
    (sum, product) => sum + (product.price || 0) * product.quantity,
    0
  );
  document.getElementById("subtotal").textContent = formatPrice(subtotal);
  document.getElementById("totalAmount").textContent = formatPrice(subtotal);
}

// Init
renderCartItems();