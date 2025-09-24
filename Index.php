<?php
include "koneksi.php";
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Coffee Shop</title>
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="Responsive.css" />
    <link rel="stylesheet" href="login.css" />
    <link 
  href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" 
  rel="stylesheet"/>

  </head>

  <body>
    <!-- Header -->
    <header class="header">
      <div class="header-content">
        <div class="logo-section">
          <img
            class="logo"
            src="./IMAGE/LOGO.png"
            alt="Coffee Shop Logo"
          />
          <div class="location">
            <span>📍</span>
            <span>PADUAN TEA UNM</span>
          </div>
        </div>

        <div class="search-container">
          <div class="search-icon">🔍</div>
          <input
            type="text"
            class="search-box"
            placeholder="Search coffee and more"
          />
        </div>

        <!-- User actions -->
        <div class="icon-header">
          <a href="./CartPage(keranjang)/Cart.html" class="link-halaman">
          <button class="icon-btn" id="cartBtn">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="16"
              height="16"
              fill="currentColor"
              class="bi bi-bag-fill"
              viewBox="0 0 16 16"
            >
              <path
                d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4z"
              />
            </svg>
            <span id="cartCount" class="cart-badge">0</span>
          </button>
        </a>

          <button class="icon-btn">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="16"
              height="16"
              fill="currentColor"
              class="bi bi-heart-fill"
              viewBox="0 0 16 16"
            >
              <path
                fill-rule="evenodd"
                d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314"
              />
            </svg>
          </button>

          <button class="icon-btn" id="openLogin">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="28"
              height="28"
              fill="currentColor"
              class="bi bi-person-circle"
              viewBox="0 0 16 16"
            >
              <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
              <path
                fill-rule="evenodd"
                d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"
              />
            </svg>
          </button>
        </div>
      </div>
    </header>

    <!-- LOGIN PAGE -->
    <div class="overlay" id="overlay">
      <div class="login-popup">
        <button class="close-btn" id="closeLogin">&times;</button>
        <h2>LOGIN</h2>
        <hr />
        <input type="email" placeholder="E-mail" />
        <input type="password" placeholder="Password" />
        <a href="#">Forgot your password?</a>
        <button class="login-btn">Login</button>
        <hr />
        <p>Don't have an account? <a href="#" id="openSignup">Sign up</a></p>
        <p id="loginError" style="color: red; display: none;">Username atau password salah!</p>
      </div>
    </div>

    <!-- Popup Sign Up -->
    <div id="signupModal" class="modal">
      <div class="modal-regist">
        <span id="closeSignup" class="close">&times;</span>
        <h2 class="regist-font">Create Account</h2>
        <form id="signupForm">
          <input class="form-input"
            type="text"
            name="txtNama"
            placeholder="Nama Lengkap"
            required
          />
          <input class="form-input" type="email" name="txtEmail" placeholder="Email" required />
          <input class="form-input"
            type="password"
            name="txtPassword"
            placeholder="Password"
            required
          />
          <input class="form-input" type="text" name="txtNoHp" placeholder="No. HP" required />
          <input class="form-input" type="text" name="txtAlamat" placeholder="Alamat" required />
          <button class="create-account" type="submit">Tambah Akun</button>
        </form>
        <p id="registerSuccess" style="color: green; display: none;">Registrasi berhasil! Silakan login.</p>
      </div>
    </div>
    <!-- LOGIN PAGE -->

    <!-- Hero Section -->
    <main>
      <section class="hero">
        <div class="hero-content">
          <div class="hero-text">
            <h1 class="hero-title">Summer's brightest new drinks</h1>
            <p class="hero-subtitle">
              Paduan Tea adalah brand minuman lokal yang menghadirkan racikan
              teh pilihan dengan sentuhan modern. Setiap sajian diramu dari
              bahan alami berkualitas, dipadukan dengan rasa yang unik dan
              menyegarkan. Dengan cita rasa khas nusantara yang dipadukan
              inovasi kekinian, Paduan Tea siap menemani momen santai, bekerja,
              maupun berkumpul bersama orang tercinta.
            </p>
            <a href="#"><button class="cta-button" onclick="scrollToProducts()"></a>
              Order Now
            </button>
          </div>

          <div class="drinks-scroll" id="drinksScroll">
            <div class="drink-item" onclick="selectDrink(0)">
              <img
                src="./image//Desain tanpa judul/1.png"
                alt="Iced Drink"
                class="drink-icon"
              />
              <div class="drink-name">Iced Drink</div>
            </div>
            <div class="drink-item" onclick="selectDrink(1)">
              <img
                src="./image//Desain tanpa judul/2.png"
                alt="Hot Coffee"
                class="drink-icon"
              />
              <div class="drink-name">Hot Coffee</div>
            </div>
            <div class="drink-item" onclick="selectDrink(2)">
              <img
                src="./image//Desain tanpa judul/3.png"
                alt="Cold Brew"
                class="drink-icon"
              />
              <div class="drink-name">Cold Brew</div>
            </div>
            <div class="drink-item" onclick="selectDrink(3)">
              <img
                src="./image//Desain tanpa judul/4.png"
                alt="Smoothie"
                class="drink-icon"
              />
              <div class="drink-name">Smoothie</div>
            </div>
            <div class="drink-item" onclick="selectDrink(4)">
              <img
                src="./image//Desain tanpa judul/5.png"
                alt="Bubble Tea"
                class="drink-icon"
              />
              <div class="drink-name">Bubble Tea</div>
            </div>
            <div class="drink-item" onclick="selectDrink(5)">
              <img
                src="./image//Desain tanpa judul/6.png"
                alt="Milk Drink"
                class="drink-icon"
              />
              <div class="drink-name">Milk Drink</div>
            </div>
          </div>
        </div>
      </section>

      <!-- Menu Category -->
      <nav class="menu-category">
        <button class="category-btn active">COFFEE</button>
        <button class="category-btn">FRESH DRINK</button>
        <button class="category-btn">MILK TEA</button>
        <button class="category-btn">CHOCOLATE</button>
        <button class="category-btn">VARIANT HOT</button>
        <button class="category-btn">PAKET</button>
      </nav>

      <section class="iklan">
        <div class="iklan-products">
          <img src="/image/LOGO.png" alt="" class="img-iklan" />
          <img src="image//LOGO.png" alt="" class="img-iklan" />
          <img src="/image//LOGO.png" alt="" class="img-iklan" />
        </div>
      </section>

      <!-- Product Sections -->
      <!-- Product Sections -->
<section class="product-section" id="cold-brew-2">
  <div class="section-header">
    <h2 class="section-title">Cold Brew</h2>
    <div class="fitur-lainnya">Lainnya</div>
  </div>
  <div class="products-container drinks-grid">

    <div class="products-container drinks-grid">
  <!-- PRODUK 1 -->
  <div onclick="openProductPopup(this, event)" class="product-card">
    <div class="product-image">
      <img src="./image//Desain tanpa judul/1.png" alt="Cold Brew Original" class="drink-image" />
      <button class="favorite-btn" onclick="toggleFavorite(this, event)">♡</button>
    </div>
    <div class="product-info">
      <div class="product-name">Cold Brew Original</div>
      <div class="product-footer">
        <div class="product-price" data-small="8000" data-medium="10000" data-large="12000">Rp 10.000</div>
      </div>
    </div>
  </div>

  <!-- PRODUK 2 -->
  <div onclick="openProductPopup(this, event)" class="product-card">
    <div class="product-image">
      <img src="./image//Desain tanpa judul/2.png" alt="Vanilla Sweet Cream Cold Brew" class="drink-image" />
      <button class="favorite-btn" onclick="toggleFavorite(this, event)">♡</button>
    </div>
    <div class="product-info">
      <div class="product-name">Vanilla Sweet Cream Cold Brew</div>
      <div class="product-footer">
        <div class="product-price" data-small="12000" data-medium="15000" data-large="18000">Rp 15.000</div>
      </div>
    </div>
  </div>

  <!-- PRODUK 3 -->
  <div onclick="openProductPopup(this, event)" class="product-card">
    <div class="product-image">
      <img src="./image//Desain tanpa judul/3.png" alt="Caramel Cold Brew" class="drink-image" />
      <button class="favorite-btn" onclick="toggleFavorite(this, event)">♡</button>
    </div>
    <div class="product-info">
      <div class="product-name">Caramel Cold Brew</div>
      <div class="product-footer">
        <div class="product-price" data-small="13000" data-medium="16000" data-large="19000">Rp 16.000</div>
      </div>
    </div>
  </div>

  <!-- PRODUK 4 -->
  <div onclick="openProductPopup(this, event)" class="product-card">
    <div class="product-image">
      <img src="./image//Desain tanpa judul/4.png" alt="Mocha Cold Brew" class="drink-image" />
      <button class="favorite-btn" onclick="toggleFavorite(this, event)">♡</button>
    </div>
    <div class="product-info">
      <div class="product-name">Mocha Cold Brew</div>
      
      <div class="product-footer">
        <div class="product-price" data-small="14000" data-medium="17000" data-large="20000">Rp 17.000</div>
      </div>
    </div>
  </div>

  <!-- PRODUK 5 -->
  <div onclick="openProductPopup(this, event)" class="product-card">
    <div class="product-image">
      <img src="./image//Desain tanpa judul/5.png" alt="Hazelnut Cold Brew" class="drink-image" />
      <button class="favorite-btn" onclick="toggleFavorite(this, event)">♡</button>
    </div>
    <div class="product-info">
      <div class="product-name">Hazelnut Cold Brew</div>
      <div class="product-footer">
        <div class="product-price" data-small="15000" data-medium="18000" data-large="21000">Rp 18.000</div>
      </div>
    </div>
  </div>

  <!-- PRODUK 6 -->
  <div onclick="openProductPopup(this, event)" class="product-card">
    <div class="product-image">
      <img src="./image//Desain tanpa judul/6.png" alt="Coconut Cold Brew" class="drink-image" />
      <button class="favorite-btn" onclick="toggleFavorite(this, event)">♡</button>
    </div>
    <div class="product-info">
      <div class="product-name">Coconut Cold Brew</div>
      <div class="product-footer">
        <div class="product-price" data-small="15000" data-medium="18000" data-large="21000">Rp 18.000</div>
      </div>
    </div>
  </div>
</div>


  </div>
</section>


<!-- BARIS KE 2 -->

  


    </main>

    <!-- Top Section -->
    <div class="top-section">
      <h1>Check out our best Tea & Coffee</h1>
      <button>Explore our products</button>
    </div>

    <!-- Testimonials -->
    <section class="testimonials">
      <h2>Our Happy Customers</h2>
      <div class="testimonial-container">
        <div class="testimonial">
          <img src="" alt="Customer" />
          <h4>John Adams</h4>
          <div class="stars">★★★★★</div>
          <p>
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque ut.
          </p>
        </div>
        <div class="testimonial">
          <img src="" alt="Customer" />
          <h4>Sam Williams</h4>
          <div class="stars">★★★★★</div>
          <p>
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque ut.
          </p>
        </div>
        <div class="testimonial">
          <img src="" alt="Customer" />
          <h4>Angela Dominic</h4>
          <div class="stars">★★★★★</div>
          <p>
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque ut.
          </p>
        </div>
      </div>
    </section>

    <section class="subscribe">
      <!-- Maps di kiri -->
      <div class="subscribe-left">
        <!-- Google Maps Embed -->
        <iframe
          src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3952.2673944946536!2d110.3694898741608!3d-7.861597579093373!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a57f5e329f0a5%3A0x6b83b97ad0f3c7d!2sYogyakarta!5e0!3m2!1sen!2sid!4v1692187844103!5m2!1sen!2sid"
          width="100%"
          height="250"
          style="border: 0; border-radius: 15px"
          allowfullscreen=""
          loading="lazy"
          referrerpolicy="no-referrer-when-downgrade"
        >
        </iframe>
      </div>

      <!-- Form di kanan -->
      <div class="subscribe-right">
        <h3>Join in and get 15% Off!</h3>
        <div class="form-group">
          <input type="email" placeholder="Enter your email" />
          <button>Subscribe</button>
        </div>
      </div>
    </section>

    <!-- Bottom Section -->
    <div class="bottom-section">
      <div class="col">
        <h4>PADUAN TEA</h4>
        <p>Best quality coffee beans for your perfect cup.</p>
      </div>
      <div class="col">
        <h4>Services</h4>
        <ul>
          <li><a href="#">Delivery</a></li>
          <li><a href="#">Wholesale</a></li>
          <li><a href="#">Custom Orders</a></li>
        </ul>
      </div>
      <div class="col">
        <h4>About Us</h4>
        <ul>
          <li><a href="#">Our Story</a></li>
          <li><a href="#">Careers</a></li>
          <li><a href="#">Blog</a></li>
        </ul>
      </div>
      <div class="col">
        <h4>Social Media</h4>
        <div class="social">
          <a href="#">Fb</a>
          <a href="#">Tw</a>
          <a href="#">Ig</a>
        </div>
      </div>
    </div>

    <!-- ================== PRODUCT DETAIL POPUP ================== -->
<div id="productPopup" class="popup-overlay">
  <div class="popup-content">
    <span class="popup-close" onclick="closePopup()">&times;</span>
    <div class="popup-body">
      <img id="popupImage" src="" alt="Product" class="popup-image">
      <h2 id="popupName"></h2>
      <p id="popupPrice"></p>

      <!-- Variant -->
      <label>Variant:</label>
      <select id="popupVariant">
        <option value="Hot">Hot</option>
        <option value="Ice">Ice</option>
      </select>

      <!-- Sugar -->
      <label>Sugar:</label>
      <select id="popupSugar">
        <option value="Normal">Normal</option>
        <option value="Less">Less</option>
      </select>

      <!-- Size -->
      <label>Size:</label>
      <select id="popupSize">
        <option value="Small">Small</option>
        <option value="Medium" selected>Medium</option>
        <option value="Large">Large</option>
      </select>

      <!-- Outlet -->
      <label>Outlet:</label>
      <select id="popupOutlet">
        <option value="Parangtambung">Parangtambung</option>
        <option value="Pettarani">Pettarani</option>
        <option value="Mannuruki">Mannuruki</option>
      </select>
      <small class="outlet-detail" id="outletDetail">
        📍 Parangtambung: Jl. Parangtambung No. 45 <br>
        📍 Pettarani: Jl. AP Pettarani No. 10 <br>
        📍 Mannuruki: Jl. Mannuruki Raya No. 7
      </small>

      <!-- Order Type -->
      <label>Order Type:</label>
      <select id="popupOrderType">
        <option value="Delivery">Delivery</option>
        <option value="Dine In">Dine In</option>
        <option value="Pickup">Pickup</option>
      </select>

      <button onclick="confirmAddToCart()" class="popup-add-btn">Add to Cart</button>
    </div>
  </div>
</div>

<!-- ================== END PRODUCT DETAIL POPUP ================== -->



    <!-- Mobile Footer Navigation -->
    <footer class="mobile-footer">
      <a href="#" class="footer-item active">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="24"
          height="24"
          fill="currentColor"
          class="bi bi-house-door-fill"
          viewBox="0 0 16 16"
        >
          <path
            d="M6.5 14.5v-3.505c0-.245.25-.495.5-.495h2c.25 0 .5.25.5.5v3.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5z"
          />
        </svg>
        <span>Home</span>
      </a>
      <a href="#" class="footer-item">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="24"
          height="24"
          fill="currentColor"
          class="bi bi-search"
          viewBox="0 0 16 16"
        >
          <path
            d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"
          />
        </svg>
        <span>Search</span>
      </a>
      <a href="#" class="footer-item">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="24"
          height="24"
          fill="currentColor"
          class="bi bi-bag"
          viewBox="0 0 16 16"
        >
          <path
            d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1zm3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4h-3.5zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V5z"
          />
        </svg>
        <span>Orders</span>
      </a>
      <a href="#" class="footer-item">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="24"
          height="24"
          fill="currentColor"
          class="bi bi-person"
          viewBox="0 0 16 16"
        >
          <path
            d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"
          />
        </svg>
        <span>Account</span>
      </a>
    </footer>

    <script src="script.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  </body>
</html>