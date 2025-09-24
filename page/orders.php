<?php
// koneksi database
include __DIR__ . "/../koneksi.php";

// --- Proses Tambah Order ---
if (isset($_POST['tambah'])) {
    $id_akun          = $_POST['id_akun'];
    $alamat_lengkap   = $_POST['alamat_lengkap'];
    $id_lokasi        = $_POST['id_lokasi'];
    $tanggal_order    = date("Y-m-d H:i:s");
    $status_order     = "baru";
    $total_harga      = $_POST['total_harga'];
    $metode_pembayaran= $_POST['metode_pembayaran'];
    $tipe_pembelian   = $_POST['tipe_pembelian'];
    $id_voucher       = !empty($_POST['id_voucher']) ? $_POST['id_voucher'] : "NULL";

    // Simpan alamat baru ke tabel alamat_user
    $mysqli->query("INSERT INTO alamat_user (id_akun, alamat_lengkap, is_default) 
                    VALUES ('$id_akun', '$alamat_lengkap', 0)");
    $id_alamat_user = $mysqli->insert_id;

    // Simpan order
    $sql = "INSERT INTO orders 
            (id_akun, id_alamat_user, id_lokasi, tanggal_order, status_order, total_harga, metode_pembayaran, tipe_pembelian, id_voucher) 
            VALUES 
            ('$id_akun','$id_alamat_user','$id_lokasi','$tanggal_order','$status_order','$total_harga','$metode_pembayaran','$tipe_pembelian',$id_voucher)";
    $mysqli->query($sql);

    header("Location: index.php?page=orders");
    exit;
}

// --- Proses Hapus ---
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $mysqli->query("DELETE FROM orders WHERE id_order=$id");
    header("Location: index.php?page=orders");
    exit;
}

// --- Proses Update Status ---
if (isset($_POST['update_status'])) {
    $id = $_POST['id_order'];
    $status = $_POST['status_order'];
    $mysqli->query("UPDATE orders SET status_order='$status' WHERE id_order=$id");
    header("Location: index.php?page=orders");
    exit;
}

// --- Proses Edit Order ---
if (isset($_POST['edit_order'])) {
    $id_order         = $_POST['id_order'];
    $id_akun          = $_POST['id_akun'];
    $alamat_lengkap   = $_POST['alamat_lengkap'];
    $id_lokasi        = $_POST['id_lokasi'];
    $total_harga      = $_POST['total_harga'];
    $metode_pembayaran= $_POST['metode_pembayaran'];
    $tipe_pembelian   = $_POST['tipe_pembelian'];
    $id_voucher       = !empty($_POST['id_voucher']) ? $_POST['id_voucher'] : "NULL";

    // Update alamat_user
    $rowAlamat = $mysqli->query("SELECT id_alamat_user FROM orders WHERE id_order=$id_order")->fetch_assoc();
    $id_alamat_user = $rowAlamat['id_alamat_user'];
    $mysqli->query("UPDATE alamat_user SET alamat_lengkap='$alamat_lengkap' WHERE id_alamat_user=$id_alamat_user");

    // Update order
    $sql = "UPDATE orders 
            SET id_akun='$id_akun', id_lokasi='$id_lokasi', total_harga='$total_harga', 
                metode_pembayaran='$metode_pembayaran', tipe_pembelian='$tipe_pembelian', id_voucher=$id_voucher 
            WHERE id_order=$id_order";
    $mysqli->query($sql);

    header("Location: index.php?page=orders");
    exit;
}

// --- Ambil data orders ---
$sql = "SELECT o.*, 
               a.nama AS nama_akun, 
               au.alamat_lengkap AS alamat_user,
               l.nama_outlet, 
               v.kode_voucher
        FROM orders o
        LEFT JOIN akun a ON o.id_akun=a.id_akun
        LEFT JOIN alamat_user au ON o.id_alamat_user=au.id_alamat_user
        LEFT JOIN lokasi_outlet l ON o.id_lokasi=l.id_lokasi
        LEFT JOIN voucher v ON o.id_voucher=v.id_voucher
        ORDER BY o.id_order DESC";
$resultOrders = $mysqli->query($sql);

// --- Ambil data untuk form ---
$akun     = $mysqli->query("SELECT id_akun, nama FROM akun ORDER BY nama ASC");
$outlet   = $mysqli->query("SELECT id_lokasi, nama_outlet FROM lokasi_outlet ORDER BY nama_outlet ASC");
$vouchers = $mysqli->query("SELECT id_voucher, kode_voucher FROM voucher WHERE status='aktif' ORDER BY kode_voucher ASC");
?>

<!-- ====== CSS Orders ====== -->
<style>
.container-order {padding:20px;}
h1 {margin-bottom:20px;font-size:24px;color:#2e7d32;} /* hijau tua */
.actions {margin-bottom:15px;}

/* Tombol */
button,.btn {padding:6px 12px;border:none;border-radius:6px;cursor:pointer;font-size:13px;}
.btn-primary {background:#28a745;color:#fff;}
.btn-primary:hover {background:#218838;}
.btn-warning {background:#ffc107;color:#fff;}
.btn-danger {background:#dc3545;color:#fff;}
.btn-danger:hover {background:#c82333;}
.btn-edit {background:#2e7d32;color:#fff;} /* hijau */
.btn-edit:hover {background:#256d2c;}
.btn-sm {font-size:12px;padding:4px 8px;}

/* Tabel */
.table-container {overflow-x:auto;background:#fff;padding:15px;border-radius:8px;box-shadow:0 2px 6px rgba(0,0,0,0.1);}
.table {width:100%;border-collapse:collapse;}
.table th,.table td {padding:10px;border-bottom:1px solid #ddd;text-align:left;}
.table th {background:#e8f5e9;color:#2e7d32;}
.table tr:hover {background:#f9f9f9;}

/* Popup Form */
.form-popup {
  background:#fff;
  padding:20px;
  border:1px solid #ccc;
  border-radius:8px;
  max-width:500px;
  position:fixed;
  top:50%;
  left:50%;
  transform:translate(-50%,-50%);
  z-index:100;
  display:none;
  box-shadow:0 4px 12px rgba(0,0,0,0.2);
}
.form-popup h3 {margin-top:0;color:#2e7d32;}
.form-popup label {font-weight:bold;display:block;margin-top:8px;}
.form-popup input, .form-popup select, .form-popup textarea {
  width:100%;
  padding:8px;
  margin-top:4px;
  border:1px solid #ccc;
  border-radius:6px;
}

/* Overlay */
.overlay {
  position:fixed;
  top:0;left:0;
  width:100%;height:100%;
  background:rgba(0,0,0,0.4);
  display:none;
  z-index:99;
}
</style>

<div class="container-order">
  <h1>Manajemen Orders</h1>

  <!-- Tombol tambah -->
  <div class="actions">
    <button onclick="showFormOrder()" class="btn-primary">+ Tambah Order</button>
  </div>

  <!-- Tabel orders -->
  <div class="table-container">
    <table class="table" id="orderTable">
      <thead>
        <tr>
          <th>ID</th>
          <th>Customer</th>
          <th>Alamat</th>
          <th>Outlet</th>
          <th>Tanggal</th>
          <th>Status</th>
          <th>Total Harga</th>
          <th>Metode</th>
          <th>Tipe</th>
          <th>Voucher</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = $resultOrders->fetch_assoc()): ?>
        <tr>
          <td><?= $row['id_order'] ?></td>
          <td><?= htmlspecialchars($row['nama_akun']) ?></td>
          <td><?= htmlspecialchars($row['alamat_user']) ?></td>
          <td><?= htmlspecialchars($row['nama_outlet']) ?></td>
          <td><?= $row['tanggal_order'] ?></td>
          <td>
            <form method="POST" style="display:inline;">
              <input type="hidden" name="id_order" value="<?= $row['id_order'] ?>">
              <select name="status_order" onchange="this.form.submit()">
                <option value="baru" <?= $row['status_order']=='baru'?'selected':'' ?>>Baru</option>
                <option value="diproses" <?= $row['status_order']=='diproses'?'selected':'' ?>>Diproses</option>
                <option value="dikirim" <?= $row['status_order']=='dikirim'?'selected':'' ?>>Dikirim</option>
                <option value="selesai" <?= $row['status_order']=='selesai'?'selected':'' ?>>Selesai</option>
                <option value="batal" <?= $row['status_order']=='batal'?'selected':'' ?>>Batal</option>
              </select>
              <input type="hidden" name="update_status" value="1">
            </form>
          </td>
          <td>Rp <?= number_format($row['total_harga'],0,",",".") ?></td>
          <td><?= htmlspecialchars($row['metode_pembayaran']) ?></td>
          <td><?= htmlspecialchars($row['tipe_pembelian']) ?></td>
          <td><?= $row['kode_voucher'] ?? '-' ?></td>
          <td>
            <button class="btn-edit btn-sm" 
              onclick="showEditOrder(<?= htmlspecialchars(json_encode($row)) ?>)">Edit</button>
            <a href="index.php?page=orders&hapus=<?= $row['id_order'] ?>" 
               class="btn-danger btn-sm" 
               onclick="return confirm('Hapus order ini?')">Hapus</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Overlay -->
<div class="overlay" id="overlayOrder" onclick="hideFormOrder();hideEditOrder();"></div>

<!-- ====== Form Tambah Order ====== -->
<div id="formOrder" class="form-popup">
  <h3>Tambah Order</h3>
  <form method="POST">
    <label>Customer</label>
    <select name="id_akun" required>
      <option value="">-- Pilih Customer --</option>
      <?php $akun->data_seek(0); while($a = $akun->fetch_assoc()): ?>
        <option value="<?= $a['id_akun'] ?>"><?= htmlspecialchars($a['nama']) ?></option>
      <?php endwhile; ?>
    </select>

    <label>Alamat Lengkap</label>
    <textarea name="alamat_lengkap" required></textarea>

    <label>Outlet</label>
    <select name="id_lokasi" required>
      <option value="">-- Pilih Outlet --</option>
      <?php $outlet->data_seek(0); while($o = $outlet->fetch_assoc()): ?>
        <option value="<?= $o['id_lokasi'] ?>"><?= htmlspecialchars($o['nama_outlet']) ?></option>
      <?php endwhile; ?>
    </select>

    <label>Total Harga</label>
    <input type="number" step="0.01" name="total_harga" required>

    <label for="metode_pembayaran">Metode Pembayaran</label>
    <select id="metode_pembayaran" name="metode_pembayaran" required>
    <option value="Qris">QRIS</option>
    </select>


    <label>Tipe Pembelian</label>
    <select name="tipe_pembelian" required>
      <option value="delivery">Delivery</option>
      <option value="pickup">Pickup</option>
    </select>

    <label>Voucher</label>
    <select name="id_voucher">
      <option value="">-- Pilih Voucher (Opsional) --</option>
      <?php $vouchers->data_seek(0); while($v = $vouchers->fetch_assoc()): ?>
        <option value="<?= $v['id_voucher'] ?>"><?= htmlspecialchars($v['kode_voucher']) ?></option>
      <?php endwhile; ?>
    </select>

    <br><br>
    <button type="submit" name="tambah" class="btn-primary">Simpan</button>
    <button type="button" onclick="hideFormOrder()">Batal</button>
  </form>
</div>

<!-- ====== Form Edit Order ====== -->
<div id="formEditOrder" class="form-popup">
  <h3>Edit Order</h3>
  <form method="POST">
    <input type="hidden" name="id_order" id="edit_id_order">

    <label>Customer</label>
    <select name="id_akun" id="edit_id_akun" required>
      <option value="">-- Pilih Customer --</option>
      <?php $akun->data_seek(0); while($a = $akun->fetch_assoc()): ?>
        <option value="<?= $a['id_akun'] ?>"><?= htmlspecialchars($a['nama']) ?></option>
      <?php endwhile; ?>
    </select>

    <label>Alamat Lengkap</label>
    <textarea name="alamat_lengkap" id="edit_alamat_lengkap" required></textarea>

    <label>Outlet</label>
    <select name="id_lokasi" id="edit_id_lokasi" required>
      <option value="">-- Pilih Outlet --</option>
      <?php $outlet->data_seek(0); while($o = $outlet->fetch_assoc()): ?>
        <option value="<?= $o['id_lokasi'] ?>"><?= htmlspecialchars($o['nama_outlet']) ?></option>
      <?php endwhile; ?>
    </select>

    <label>Total Harga</label>
    <input type="number" step="0.01" name="total_harga" id="edit_total_harga" required>

   <label for="metode_pembayaran">Metode Pembayaran</label>
    <select id="metode_pembayaran" name="metode_pembayaran" required>
    <option value="Qris">QRIS</option>
    </select>

    <label>Tipe Pembelian</label>
    <select name="tipe_pembelian" id="edit_tipe_pembelian" required>
      <option value="delivery">Delivery</option>
      <option value="pickup">Pickup</option>
    </select>

    <label>Voucher</label>
    <select name="id_voucher" id="edit_id_voucher">
      <option value="">-- Pilih Voucher (Opsional) --</option>
      <?php $vouchers->data_seek(0); while($v = $vouchers->fetch_assoc()): ?>
        <option value="<?= $v['id_voucher'] ?>"><?= htmlspecialchars($v['kode_voucher']) ?></option>
      <?php endwhile; ?>
    </select>

    <br><br>
    <button type="submit" name="edit_order" class="btn-edit">Update</button>
    <button type="button" onclick="hideEditOrder()">Batal</button>
  </form>
</div>

<script>
function showFormOrder(){
  document.getElementById('formOrder').style.display = 'block';
  document.getElementById('overlayOrder').style.display = 'block';
}
function hideFormOrder(){
  document.getElementById('formOrder').style.display = 'none';
  document.getElementById('overlayOrder').style.display = 'none';
}
function showEditOrder(data){
  document.getElementById('formEditOrder').style.display = 'block';
  document.getElementById('overlayOrder').style.display = 'block';

  document.getElementById('edit_id_order').value = data.id_order;
  document.getElementById('edit_id_akun').value = data.id_akun;
  document.getElementById('edit_alamat_lengkap').value = data.alamat_user;
  document.getElementById('edit_id_lokasi').value = data.id_lokasi;
  document.getElementById('edit_total_harga').value = data.total_harga;
  document.getElementById('edit_metode_pembayaran').value = data.metode_pembayaran;
  document.getElementById('edit_tipe_pembelian').value = data.tipe_pembelian;
  document.getElementById('edit_id_voucher').value = data.id_voucher;
}
function hideEditOrder(){
  document.getElementById('formEditOrder').style.display = 'none';
  document.getElementById('overlayOrder').style.display = 'none';
}
</script>
