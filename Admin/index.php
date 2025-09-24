<?php
// koneksi ke database
include __DIR__ . "/../koneksi.php";



ob_start(); // aktifkan output buffering biar header() aman
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // mulai session hanya sekali
}



// ambil page dari URL (default: home)
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

switch ($page) {
    case 'akun':
        $includePage = __DIR__ . "/../page/akun.php";
        break;
    case 'history_pembelian':
        $includePage = __DIR__ . "/../page/history_pembelian.php";
        break;
    case 'keranjang':
        $includePage = __DIR__ . "/../page/keranjang.php";
        break;
    case 'log_login':
        $includePage = __DIR__ . "/../page/log_login.php";
        break;
    case 'lokasi_outlet':
        $includePage = __DIR__ . "/../page/lokasi_outlet.php";
        break;
    case 'menu':
        $includePage = __DIR__ . "/../page/menu.php";
        break;
    case 'orders':
        $includePage = __DIR__ . "/../page/orders.php";
        break;
    case 'order_detail':
        $includePage = __DIR__ . "/../page/order_detail.php";
        break;
    case 'voucher':
        $includePage = __DIR__ . "/../page/voucher.php";
        break;
    default:
        $includePage = __DIR__ . "/../page/home.php"; // halaman default
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>PaduanTea Dashboard</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"/>

  <style>
      * {margin:0;padding:0;box-sizing:border-box;}
      body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f5f7fa; color: #333;
      }
      .container {  min-height:100vh; }
      .sidebar { width:240px; background:#065f46; color:white; position:fixed; height:100vh; overflow-y:auto; box-shadow:2px 0 10px rgba(0,0,0,0.1); }
      .logo { padding:20px; font-size:20px; font-weight:bold; border-bottom:1px solid rgba(255,255,255,0.1); }
      .logo span { color:#ff6b35; }
      .nav-menu { padding:10px 0; }
      .nav-item { display:flex; align-items:center; padding:12px 20px; color:rgba(255,255,255,0.9); text-decoration:none; transition:all 0.3s ease; border-left:3px solid transparent; }
      .nav-item:hover,.nav-item.active { background-color:rgba(255,255,255,0.1); border-left:3px solid #ff6b35; color:white; }
      .nav-item i { width:20px; margin-right:12px; text-align:center; }
      .nav-item .badge { background-color:#ef4444; color:white; border-radius:12px; padding:2px 8px; font-size:12px; margin-left:auto; }

      .main-content { margin-left:240px; padding:20px; min-height:100vh;}
      .stats-cards { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:20px; margin-bottom:30px; }
      .stat-card { background:#4ade80; color:white; padding:30px; border-radius:12px; box-shadow:0 4px 6px rgba(0,0,0,0.1); display:flex; align-items:center; justify-content:center; font-size:32px; gap:20px; transition:transform 0.3s ease; }
      .stat-card:nth-child(2){background:#ff6b35;}
      .stat-card:nth-child(3){background:#22c55e;}
      .stat-card:nth-child(4){background:#fbbf24;}
      .stat-card:hover{transform:translateY(-3px);}
      .breadcrumb { display:flex; align-items:center; color:#6b7280; margin-bottom:10px; font-size:14px; }
      .breadcrumb a{ color:#6b7280; text-decoration:none; }
      .breadcrumb i{ margin:0 8px; font-size:12px; }
      .content-header{ display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;}
      .page-title{ font-size:24px; font-weight:600; color:#1f2937;}
      .actions-btn{ background:none; border:none; color:#6b7280; cursor:pointer; padding:8px; border-radius:6px; margin-left:5px; font-size:16px; transition:all 0.3s ease;}
      .actions-btn:hover{ background-color:#f3f4f6; color:#374151;}
      .filters{ background:white; padding:20px; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.1); margin-bottom:20px; display:flex; gap:15px; align-items:center; flex-wrap:wrap;}
      .filter-group{ display:flex; align-items:center; gap:8px;}
      .filter-select,.search-input{ padding:8px 12px; border:1px solid #d1d5db; border-radius:6px; font-size:14px; outline:none; transition:border-color 0.3s ease;}
      .filter-select:focus,.search-input:focus{ border-color:#4ade80; box-shadow:0 0 0 3px rgba(74,222,128,0.1);}
      .search-container{ position:relative; margin-left:auto;}
      .search-input{ padding-left:35px; width:250px;}
      .search-container i{ position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#6b7280;}
      .add-btn{ background:#4ade80; color:white; border:none; padding:10px 20px; border-radius:8px; cursor:pointer; font-weight:500; display:flex; align-items:center; gap:8px; transition:all 0.3s ease;}
      .add-btn:hover{ transform:translateY(-2px); background:#22c55e; box-shadow:0 4px 12px rgba(74,222,128,0.3);}
      .table-container{ background:white; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.1); overflow:hidden;}
      .table{ width:100%; border-collapse:collapse;}
      .table th{ background-color:#f9fafb; padding:12px 15px; text-align:left; font-weight:600; color:#374151; border-bottom:1px solid #f3f4f6; cursor:pointer; font-size:14px;}
      .table th:hover{ background-color:#f3f4f6;}
      .table td{ padding:15px; border-bottom:1px solid #f3f4f6; vertical-align:middle; font-size:14px;}
      .table tr:hover{ background-color:#f9fafb;}
      .checkbox{ width:16px; height:16px; accent-color:#4ade80;}
      .status-badge{ padding:4px 12px; border-radius:20px; font-size:12px; font-weight:500;}
      .status-active{ background-color:#dcfce7; color:#166534;}
      .status-inactive{ background-color:#fee2e2; color:#991b1b;}
      .pagination{ padding:20px; display:flex; justify-content:space-between; align-items:center;}
      .pagination-info{ color:#6b7280; font-size:14px;}
      .bulk-actions{ margin:15px 0; display:flex; gap:10px;}
      .btn-accept{ background:#22c55e; color:white; padding:8px 14px; border:none; border-radius:6px; cursor:pointer; font-weight:500;}
      .btn-accept:hover{ background:#16a34a;}
      .btn-delete{ background:#ef4444; color:white; padding:8px 14px; border:none; border-radius:6px; cursor:pointer; font-weight:500;}
      .btn-delete:hover{ background:#dc2626;}
      @media(max-width:768px){.sidebar{width:200px;}.main-content{margin-left:200px;}.stats-cards{grid-template-columns:repeat(2,1fr);} .filters{flex-direction:column;align-items:stretch;} .search-container{margin-left:0;} .search-input{width:100%;}}
      @media(max-width:640px){.sidebar{width:100%;position:static;height:auto;}.main-content{margin-left:0;}.stats-cards{grid-template-columns:1fr;}}
    </style>
</head>
<body>
  <div class="container">
    <!-- Sidebar -->
    <nav class="sidebar">
      <div class="logo">Paduan<span>Tea</span></div>
      <div class="nav-menu">
        <a href="index.php?page=akun" class="nav-item <?= $page == 'akun' ? 'active' : '' ?>"><i class="fas fa-user"></i> Akun</a>
        <a href="index.php?page=history_pembelian" class="nav-item <?= $page == 'history_pembelian' ? 'active' : '' ?>"><i class="fas fa-history"></i> History Pembelian</a>
        <a href="index.php?page=keranjang" class="nav-item <?= $page == 'keranjang' ? 'active' : '' ?>"><i class="fas fa-shopping-cart"></i> Keranjang</a>
        <a href="index.php?page=log_login" class="nav-item <?= $page == 'log_login' ? 'active' : '' ?>"><i class="fas fa-sign-in-alt"></i> Log Login</a>
        <a href="index.php?page=lokasi_outlet" class="nav-item <?= $page == 'lokasi_outlet' ? 'active' : '' ?>"><i class="fas fa-map-marker-alt"></i> Lokasi Outlet</a>
        <a href="index.php?page=menu" class="nav-item <?= $page == 'menu' ? 'active' : '' ?>"><i class="fas fa-utensils"></i> Menu</a>
        <a href="index.php?page=orders" class="nav-item <?= $page == 'orders' ? 'active' : '' ?>"><i class="fas fa-shopping-bag"></i> Orders <span class="badge">19</span></a>
        <a href="index.php?page=order_detail" class="nav-item <?= $page == 'order_detail' ? 'active' : '' ?>"><i class="fas fa-file-alt"></i> Order Detail</a>
        <a href="index.php?page=voucher" class="nav-item <?= $page == 'voucher' ? 'active' : '' ?>"><i class="fas fa-ticket-alt"></i> Voucher</a>
        <a href="logout.php" class="nav-item"><i class="fas fa-sign-out-alt"></i> Logout</a>
      </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
      <?php include $includePage; ?>
    </div>index
  </div>
</body>
<?php ob_end_flush(); ?>

</html>