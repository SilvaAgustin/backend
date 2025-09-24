<?php 
// ===== CRUD Akun =====
// diasumsikan $mysqli sudah terhubung dari index.php

$aksi = "";
$id_akun = $nama = $email = $password = $no_hp = $tanggal_daftar = $alamat = "";

// Tangani aksi tambah, edit, hapus
if (isset($_GET['tambah'])) {
    $aksi = "tambah-akun";

} elseif (isset($_GET['tambah-akun'])) {
    $nama = $_POST['txtNama'];
    $email = $_POST['txtEmail'];
    $password = password_hash($_POST['txtPassword'], PASSWORD_DEFAULT);
    $no_hp = $_POST['txtNoHp'];
    $tanggal_daftar = !empty($_POST['txtTanggalDaftar']) ? $_POST['txtTanggalDaftar'] : date('Y-m-d H:i:s');
    $alamat = $_POST['txtAlamat'];

    $stmt = $mysqli->prepare("INSERT INTO akun (nama,email,password,no_hp,tanggal_daftar,alamat) VALUES (?,?,?,?,?,?)");
    $stmt->bind_param("ssssss",$nama,$email,$password,$no_hp,$tanggal_daftar,$alamat);
    if ($stmt->execute()) header("Location: index.php?page=akun");

} elseif (isset($_GET['edit'])) {
    $stmt = $mysqli->prepare("SELECT * FROM akun WHERE id_akun=?");
    $stmt->bind_param("i", $_GET['edit']);
    $stmt->execute();
    $akunEdit = $stmt->get_result()->fetch_assoc();

    $aksi = "edit-akun";
    $id_akun = $akunEdit['id_akun'];
    $nama = $akunEdit['nama'];
    $email = $akunEdit['email'];
    $password = $akunEdit['password'];
    $no_hp = $akunEdit['no_hp'];
    $tanggal_daftar = $akunEdit['tanggal_daftar'];
    $alamat = $akunEdit['alamat'];

} elseif (isset($_GET['edit-akun'])) {
    $id_akun = $_POST['txtIdAkun'];
    $nama = $_POST['txtNama'];
    $email = $_POST['txtEmail'];
    $passwordInput = $_POST['txtPassword'];
    $no_hp = $_POST['txtNoHp'];
    $tanggal_daftar = $_POST['txtTanggalDaftar'];
    $alamat = $_POST['txtAlamat'];

    // Jika password tidak kosong, hash baru, jika kosong gunakan password lama
    $password = !empty($passwordInput) ? password_hash($passwordInput,PASSWORD_DEFAULT) : $_POST['txtPasswordLama'];

    $stmt = $mysqli->prepare("UPDATE akun SET nama=?,email=?,password=?,no_hp=?,tanggal_daftar=?,alamat=? WHERE id_akun=?");
    $stmt->bind_param("ssssssi",$nama,$email,$password,$no_hp,$tanggal_daftar,$alamat,$id_akun);
    if ($stmt->execute()) header("Location: index.php?page=akun");

} elseif (isset($_GET['hapus'])) {
    $id_akun = $_GET['hapus'];
    $stmt = $mysqli->prepare("DELETE FROM akun WHERE id_akun=?");
    $stmt->bind_param("i",$id_akun);
    if ($stmt->execute()) header("Location: index.php?page=akun");
}

// Ambil semua data akun untuk JS
$resultAkun = $mysqli->query("SELECT * FROM akun");

// Siapkan data akun dalam array untuk JS
$akunArray = [];
while($row = $resultAkun->fetch_assoc()) {
    $akunArray[] = [
        'id' => $row['id_akun'],
        'name' => htmlspecialchars($row['nama']),
        'email' => htmlspecialchars($row['email']),
        'password' => '********', // jangan tampilkan password asli
        'registered' => $row['tanggal_daftar'],
        'status' => 'Active', // contoh status, bisa disesuaikan jika ada kolom status
        'address' => htmlspecialchars($row['alamat']),
        'orders' => 0 // contoh data orders, bisa diisi sesuai data sebenarnya
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>PaduanTea Dashboard - Kelola Akun</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
  <style>
    /* ===== Gabungan CSS Anda ===== */
    /* Reset */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f5f7fa;
      color: #333;
    }

    .container {
      display: flex;
      min-height: 100vh;
    }

    /* Sidebar */
    .sidebar {
      width: 240px;
      background: #065f46; /* hijau tua */
      color: white;
      position: fixed;
      height: 100vh;
      overflow-y: auto;
      box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    }

    .logo {
      padding: 20px;
      font-size: 20px;
      font-weight: bold;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .logo span {
      color: #ff6b35;
    }

    .nav-menu {
      padding: 10px 0;
    }

    .nav-item {
      display: flex;
      align-items: center;
      padding: 12px 20px;
      color: rgba(255, 255, 255, 0.9);
      text-decoration: none;
      transition: all 0.3s ease;
      border-left: 3px solid transparent;
    }

    .nav-item:hover,
    .nav-item.active {
      background-color: rgba(255, 255, 255, 0.1);
      border-left: 3px solid #ff6b35;
      color: white;
    }

    .nav-item i {
      width: 20px;
      margin-right: 12px;
      text-align: center;
    }

    .nav-item .badge {
      background-color: #ef4444;
      color: white;
      border-radius: 12px;
      padding: 2px 8px;
      font-size: 12px;
      margin-left: auto;
    }

    /* Main Content */
    .main-content {
      margin-left: 120px;
      flex: 1;
      padding: 20px;
    }

    /* Stats Cards */
    .stats-cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }

    .stat-card {
      background: #4ade80;
      color: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 32px;
      gap: 20px;
      transition: transform 0.3s ease;
    }

    .stat-card:nth-child(2) {
      background: #ff6b35;
    }
    .stat-card:nth-child(3) {
      background: #22c55e;
    }
    .stat-card:nth-child(4) {
      background: #fbbf24;
    }

    .stat-card:hover {
      transform: translateY(-3px);
    }

    /* Breadcrumb */
    .breadcrumb {
      display: flex;
      align-items: center;
      color: #6b7280;
      margin-bottom: 10px;
      font-size: 14px;
    }

    .breadcrumb a {
      color: #6b7280;
      text-decoration: none;
    }

    .breadcrumb i {
      margin: 0 8px;
      font-size: 12px;
    }

    /* Page Header */
    .content-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }

    .page-title {
      font-size: 24px;
      font-weight: 600;
      color: #1f2937;
    }

    .actions-btn {
      background: none;
      border: none;
      color: #6b7280;
      cursor: pointer;
      padding: 8px;
      border-radius: 6px;
      margin-left: 5px;
      font-size: 16px;
      transition: all 0.3s ease;
    }
    .actions-btn:hover {
      background-color: #f3f4f6;
      color: #374151;
    }

    /* Filters */
    .filters {
      background: white;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
      margin-bottom: 20px;
      display: flex;
      gap: 15px;
      align-items: center;
      flex-wrap: wrap;
    }

    .filter-group {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .filter-select,
    .search-input {
      padding: 8px 12px;
      border: 1px solid #d1d5db;
      border-radius: 6px;
      font-size: 14px;
      outline: none;
      transition: border-color 0.3s ease;
    }

    .filter-select:focus,
    .search-input:focus {
      border-color: #4ade80;
      box-shadow: 0 0 0 3px rgba(74, 222, 128, 0.1);
    }

    .search-container {
      position: relative;
      margin-left: auto;
    }

    .search-input {
      padding-left: 35px;
      width: 250px;
    }

    .search-container i {
      position: absolute;
      left: 12px;
      top: 50%;
      transform: translateY(-50%);
      color: #6b7280;
    }

    .add-btn {
      background: #4ade80;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 500;
      display: flex;
      align-items: center;
      gap: 8px;
      transition: all 0.3s ease;
    }

    .add-btn:hover {
      transform: translateY(-2px);
      background: #22c55e;
      box-shadow: 0 4px 12px rgba(74, 222, 128, 0.3);
    }

    /* Table */
    .table-container {
      background: white;
      border-radius: 12px;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
      overflow: hidden;
    }

    .table {
      width: 100%;
      border-collapse: collapse;
    }

    .table th {
      background-color: #f9fafb;
      padding: 12px 15px;
      text-align: left;
      font-weight: 600;
      color: #374151;
      border-bottom: 1px solid #f3f4f6;
      cursor: pointer;
      font-size: 14px;
    }
    .table th:hover {
      background-color: #f3f4f6;
    }

    .table td {
      padding: 15px;
      border-bottom: 1px solid #f3f4f6;
      vertical-align: middle;
      font-size: 14px;
    }

    .table tr:hover {
      background-color: #f9fafb;
    }

    .checkbox {
      width: 16px;
      height: 16px;
      accent-color: #4ade80;
    }

    .status-badge {
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 500;
    }
    .status-active {
      background-color: #dcfce7;
      color: #166534;
    }
    .status-inactive {
      background-color: #fee2e2;
      color: #991b1b;
    }

    /* Pagination */
    .pagination {
      padding: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .pagination-info {
      color: #6b7280;
      font-size: 14px;
    }

    .pagination-controls {
      display: flex;
      gap: 8px;
    }

    .pagination-btn {
      padding: 8px 12px;
      border: 1px solid #d1d5db;
      background: white;
      color: #374151;
      border-radius: 6px;
      cursor: pointer;
      font-size: 14px;
      transition: all 0.3s ease;
    }
    .pagination-btn:hover:not(.active) {
      background-color: #f9fafb;
    }
    .pagination-btn.active {
      background-color: #2dd4bf;
      color: white;
      border-color: #2dd4bf;
    }
    .pagination-btn:disabled {
      opacity: 0.5;
      cursor: not-allowed;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .sidebar {
        width: 200px;
      }
      .main-content {
        margin-left: 200px;
      }
      .stats-cards {
        grid-template-columns: repeat(2, 1fr);
      }
      .filters {
        flex-direction: column;
        align-items: stretch;
      }
      .search-container {
        margin-left: 0;
      }
      .search-input {
        width: 100%;
      }
    }
    @media (max-width: 640px) {
      .sidebar {
        width: 100%;
        position: static;
        height: auto;
      }
      .main-content {
        margin-left: 0;
      }
      .stats-cards {
        grid-template-columns: 1fr;
      }
    }

    .bulk-actions {
      margin: 15px 0;
      display: flex;
      gap: 10px;
    }

    .btn-accept {
      background: #22c55e;
      color: white;
      padding: 8px 14px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 500;
    }

    .btn-accept:hover {
      background: #16a34a;
    }

    .btn-delete {
      background: #ef4444;
      color: white;
      padding: 8px 14px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 500;
    }

    .btn-delete:hover {
      background: #dc2626;
    }

    /* Form Tambah/Edit Akun */
    .card {
      background: white;
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 20px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .card h3 {
      margin-bottom: 15px;
      color: #065f46;
    }
    .mb-2 {
      margin-bottom: 12px;
    }
    label {
      display: block;
      margin-bottom: 6px;
      font-weight: 600;
      color: #065f46;
    }
    input[type="text"],
    input[type="email"],
    input[type="password"],
    input[type="datetime-local"],
    textarea {
      width: 100%;
      padding: 8px 12px;
      border: 1px solid #d1d5db;
      border-radius: 6px;
      font-size: 14px;
      outline: none;
      transition: border-color 0.3s ease;
    }
    input[type="text"]:focus,
    input[type="email"]:focus,
    input[type="password"]:focus,
    input[type="datetime-local"]:focus,
    textarea:focus {
      border-color: #4ade80;
      box-shadow: 0 0 0 3px rgba(74, 222, 128, 0.1);
    }
    button.btn {
      cursor: pointer;
      border-radius: 6px;
      padding: 10px 20px;
      font-weight: 600;
      border: none;
      transition: background-color 0.3s ease;
    }
    button.btn-success {
      background-color: #4ade80;
      color: white;
    }
    button.btn-success:hover {
      background-color: #22c55e;
    }
    a.btn-secondary {
      background-color: #6b7280;
      color: white;
      padding: 10px 20px;
      border-radius: 6px;
      text-decoration: none;
      display: inline-block;
      font-weight: 600;
      margin-left: 10px;
      transition: background-color 0.3s ease;
    }
    a.btn-secondary:hover {
      background-color: #4b5563;
    }
  </style>
</head>
<body>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Stats Cards -->
      <div class="stats-cards">
        <div class="stat-card">
          <i class="fas fa-user"></i>
          <span id="totalUsers">0</span>
        </div>
        <div class="stat-card">
          <i class="fas fa-shopping-bag"></i>
          <span id="totalOrders">0</span>
        </div>
        <div class="stat-card">
          <i class="fas fa-sync-alt"></i>
          <span id="activeUsers">0</span>
        </div>
        <div class="stat-card">
          <i class="fas fa-shopping-cart"></i>
          <span id="inactiveUsers">0</span>
        </div>
      </div>

      <!-- Breadcrumb -->
      <div class="breadcrumb">
        <i class="fas fa-home"></i>
        <a href="#">Dashboard</a>
        <i class="fas fa-chevron-right"></i>
        <span>Kelola Akun</span>
      </div>

      <!-- Page Header -->
      <div class="content-header">
        <h1 class="page-title">Kelola Akun (<span id="totalCustomers">0</span>)</h1>
        <div>
          <button class="actions-btn" onclick="printTable()"><i class="fas fa-print"></i></button>
          <button class="actions-btn" onclick="downloadCSV()"><i class="fas fa-download"></i></button>
          <a href="index.php?page=akun&tambah" class="add-btn"><i class="fas fa-plus"></i> Add Customer </a>
        </div>
      </div>

      <!-- Filters -->
      <div class="filters">
        <div class="filter-group">
          <label>Show</label>
          <select class="filter-select" id="showEntries">
            <option>10</option>
            <option>25</option>
            <option>50</option>
          </select>
        </div>
        <div class="filter-group">
          <input type="date" id="startDate" class="filter-select" />
          <span>/</span>
          <input type="date" id="endDate" class="filter-select" />
        </div>
        <div class="filter-group">
          <label>Status</label>
          <select class="filter-select" id="statusFilter">
            <option value="All">All</option>
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
          </select>
        </div>

        <div class="search-container">
          <i class="fas fa-search"></i>
          <input type="text" class="search-input" placeholder="Search Customer" id="searchInput" />
        </div>
      </div>

      <div class="bulk-actions">
        <button class="btn-accept" onclick="bulkAccept()">Accept Selected</button>
        <button class="btn-delete" onclick="bulkDelete()">Delete Selected</button>
      </div>

      <!-- Form Tambah/Edit Akun -->
      <?php if ($aksi == "tambah-akun" || $aksi == "edit-akun"): ?>
        <div class="card">
          <h3><?= $aksi == "tambah-akun" ? "Tambah Akun Baru" : "Edit Akun" ?></h3>
          <form method="POST" action="index.php?page=akun&<?= $aksi ?>">
            <input type="hidden" name="txtIdAkun" value="<?= htmlspecialchars($id_akun) ?>">
            <input type="hidden" name="txtPasswordLama" value="<?= htmlspecialchars($password) ?>">

            <div class="mb-2">
              <label for="txtNama">Nama</label>
              <input type="text" id="txtNama" name="txtNama" value="<?= htmlspecialchars($nama) ?>" required>
            </div>

            <div class="mb-2">
              <label for="txtEmail">Email</label>
              <input type="email" id="txtEmail" name="txtEmail" value="<?= htmlspecialchars($email) ?>" required>
            </div>

            <div class="mb-2">
              <label for="txtPassword">Password</label>
              <input type="password" id="txtPassword" name="txtPassword" placeholder="<?= $aksi == 'edit-akun' ? 'Kosongkan jika tidak diubah' : 'Masukkan password' ?>" <?= $aksi == 'tambah-akun' ? 'required' : '' ?>>
            </div>

            <div class="mb-2">
              <label for="txtNoHp">No HP</label>
              <input type="text" id="txtNoHp" name="txtNoHp" value="<?= htmlspecialchars($no_hp) ?>">
            </div>

            <div class="mb-2">
              <label for="txtTanggalDaftar">Tanggal Daftar</label>
              <input type="datetime-local" id="txtTanggalDaftar" name="txtTanggalDaftar" value="<?= !empty($tanggal_daftar) ? date('Y-m-d\TH:i', strtotime($tanggal_daftar)) : '' ?>">
            </div>

            <div class="mb-2">
              <label for="txtAlamat">Alamat</label>
              <textarea id="txtAlamat" name="txtAlamat" rows="3"><?= htmlspecialchars($alamat) ?></textarea>
            </div>

            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Simpan</button>
            <a href="index.php?page=akun" class="btn-secondary"><i class="fas fa-times"></i> Batal</a>
          </form>
        </div>
      <?php endif; ?>

      <!-- Tabel Akun -->
      <div class="table-container">
        <table class="table" id="akunTable">
          <thead>
            <tr>
              <th><input type="checkbox" id="selectAll" /></th>
              <th>ID</th>
              <th>Nama</th>
              <th>Email</th>
              <th>No HP</th>
              <th>Tanggal Daftar</th>
              <th>Status</th>
              <th>Alamat</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <!-- Data akan diisi lewat JS -->
          </tbody>
        </table>
      </div>

      <div class="pagination">
        <div class="pagination-info">Showing 0 of 0 entries</div>
        <div class="pagination-controls">
          <button class="pagination-btn" id="prevPage"><i class="fas fa-chevron-left"></i></button>
          <span id="pageNumbers"></span>
          <button class="pagination-btn" id="nextPage"><i class="fas fa-chevron-right"></i></button>
        </div>
      </div>
    </main>
  </div>

  <script>
    // Data akun dari PHP
    let customers = <?= json_encode($akunArray) ?>;
    let filteredCustomers = [...customers];
    let currentPage = 1;
    let entriesPerPage = 10;
    let sortDirection = {};
    
    // Render tabel dengan pagination
    function renderTable() {
      const tbody = document.querySelector("#akunTable tbody");
      tbody.innerHTML = "";

      // Hitung pagination
      const start = (currentPage - 1) * entriesPerPage;
      const end = start + entriesPerPage;
      const pageItems = filteredCustomers.slice(start, end);

      tbody.innerHTML = pageItems.map(c => `
        <tr>
          <td><input type="checkbox" class="checkbox" value="${c.id}"></td>
          <td>${c.id}</td>
          <td>${c.name}</td>
          <td>${c.email}</td>
          <td>${c.no_hp || ''}</td>
          <td>${c.registered}</td>
          <td><span class="status-badge ${c.status === "Active" ? "status-active" : "status-inactive"}">${c.status}</span></td>
          <td>${c.address}</td>
          <td>
            <a href="index.php?page=akun&edit=${c.id}" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
            <a href="index.php?page=akun&hapus=${c.id}" class="btn btn-sm btn-danger" onclick="return confirm('Hapus akun ini?')" title="Hapus"><i class="fas fa-trash"></i></a>
          </td>
        </tr>
      `).join("");

      // Update info jumlah data
      document.querySelector(".pagination-info").textContent =
        `Showing ${start + 1} to ${Math.min(end, filteredCustomers.length)} of ${filteredCustomers.length} entries`;

      // Update total count
      document.getElementById("totalCustomers").textContent = customers.length;

      // Update pagination buttons
      renderPagination();
      updateStats();
    }

    // Render pagination buttons
    function renderPagination() {
      const pageNumbersContainer = document.getElementById("pageNumbers");
      pageNumbersContainer.innerHTML = "";
      const totalPages = Math.ceil(filteredCustomers.length / entriesPerPage);

      for(let i = 1; i <= totalPages; i++) {
        const btn = document.createElement("button");
        btn.className = "pagination-btn" + (i === currentPage ? " active" : "");
        btn.textContent = i;
        btn.addEventListener("click", () => {
          currentPage = i;
          renderTable();
        });
        pageNumbersContainer.appendChild(btn);
      }

      document.getElementById("prevPage").disabled = currentPage === 1;
      document.getElementById("nextPage").disabled = currentPage === totalPages || totalPages === 0;
    }

    document.getElementById("prevPage").addEventListener("click", () => {
      if(currentPage > 1) {
        currentPage--;
        renderTable();
      }
    });

    document.getElementById("nextPage").addEventListener("click", () => {
      const totalPages = Math.ceil(filteredCustomers.length / entriesPerPage);
      if(currentPage < totalPages) {
        currentPage++;
        renderTable();
      }
    });

    // Filter & Search
    document.getElementById("searchInput").addEventListener("input", e => {
      const term = e.target.value.toLowerCase();
      filterData(term);
    });

    document.getElementById("statusFilter").addEventListener("change", e => {
      filterData();
    });

    document.getElementById("startDate").addEventListener("change", filterData);
    document.getElementById("endDate").addEventListener("change", filterData);

    document.getElementById("showEntries").addEventListener("change", e => {
      entriesPerPage = parseInt(e.target.value);
      currentPage = 1;
      renderTable();
    });

    function filterData(searchTerm = null) {
      if(searchTerm === null) {
        searchTerm = document.getElementById("searchInput").value.toLowerCase();
      }
      const status = document.getElementById("statusFilter").value;
      const startDateVal = document.getElementById("startDate").value;
      const endDateVal = document.getElementById("endDate").value;
      const startDate = startDateVal ? new Date(startDateVal) : null;
      const endDate = endDateVal ? new Date(endDateVal) : null;

      filteredCustomers = customers.filter(c => {
        // Search filter
        const matchesSearch = c.name.toLowerCase().includes(searchTerm) || c.email.toLowerCase().includes(searchTerm) || c.address.toLowerCase().includes(searchTerm);

        // Status filter
        const matchesStatus = (status === "All") || (c.status === status);

        // Date filter
        const regDate = new Date(c.registered);
        const matchesDate = (!startDate || regDate >= startDate) && (!endDate || regDate <= endDate);

        return matchesSearch && matchesStatus && matchesDate;
      });

      currentPage = 1;
      renderTable();
    }

    // Bulk Actions
    function bulkAccept() {
      const selected = document.querySelectorAll('tbody .checkbox:checked');
      if (!selected.length) return alert("Pilih minimal satu akun!");
      const ids = Array.from(selected).map(cb => cb.value);
      customers = customers.map(c => ids.includes(c.id.toString()) ? { ...c, status: "Active" } : c);
      filteredCustomers = [...customers];
      renderTable();
    }

    function bulkDelete() {
      const selected = document.querySelectorAll('tbody .checkbox:checked');
      if (!selected.length) return alert("Pilih minimal satu akun!");
      if (!confirm("Yakin ingin menghapus akun yang dipilih?")) return;
      const ids = Array.from(selected).map(cb => cb.value);
      customers = customers.filter(c => !ids.includes(c.id.toString()));
      filteredCustomers = [...customers];
      renderTable();
    }

    // Select All Checkbox
    document.getElementById("selectAll").addEventListener("change", function() {
      const checkboxes = document.querySelectorAll('tbody .checkbox');
      checkboxes.forEach(cb => cb.checked = this.checked);
    });

    // Print Table
    function printTable() {
      const tableContent = document.querySelector(".table-container").innerHTML;
      const newWindow = window.open("", "", "width=900,height=700");
      newWindow.document.write(`
        <html>
          <head>
            <title>Print Akun</title>
            <style>
              table { width: 100%; border-collapse: collapse; }
              th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
              th { background: #f9fafb; }
            </style>
          </head>
          <body>
            <h2>Daftar Akun</h2>
            ${tableContent}
          </body>
        </html>
      `);
      newWindow.document.close();
      newWindow.print();
    }

    // Download CSV
    function downloadCSV() {
      let csv = "ID,Nama,Email,No HP,Tanggal Daftar,Status,Alamat\n";
      customers.forEach(c => {
        csv += `"${c.id}","${c.name}","${c.email}","${c.no_hp || ''}","${c.registered}","${c.status}","${c.address}"\n`;
      });

      const blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
      const link = document.createElement("a");
      link.href = URL.createObjectURL(blob);
      link.download = "akun.csv";
      link.click();
    }

    // Update Statistik Cards
    function updateStats() {
      const totalUsers = customers.length;
      const totalOrders = customers.reduce((sum, c) => sum + (c.orders || 0), 0);
      const activeUsers = customers.filter(c => c.status === "Active").length;
      const inactiveUsers = customers.filter(c => c.status === "Inactive").length;

      document.getElementById("totalUsers").textContent = totalUsers;
      document.getElementById("totalOrders").textContent = totalOrders;
      document.getElementById("activeUsers").textContent = activeUsers;
      document.getElementById("inactiveUsers").textContent = inactiveUsers;
    }

    // Inisialisasi
    document.addEventListener("DOMContentLoaded", () => {
      renderTable();
    });
  </script>
</body>
</html>

       