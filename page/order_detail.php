<?php
// koneksi database
include __DIR__ . "/../koneksi.php";

// --- Proses Tambah Order Detail ---
if (isset($_POST['tambah'])) {
    $id_order     = $_POST['id_order'];
    $id_menu      = $_POST['id_menu'];
    $jumlah       = $_POST['jumlah'];
    $harga_satuan = $_POST['harga_satuan'];
    $sub_total    = $_POST['sub_total'];
    $diskon       = $_POST['diskon'];
    $total_item   = $_POST['total_item'];
    $id_voucher   = !empty($_POST['id_voucher']) ? $_POST['id_voucher'] : "NULL";

    $sql = "INSERT INTO order_detail 
            (id_order, id_menu, jumlah, harga_satuan, sub_total, diskon, total_item, id_voucher) 
            VALUES 
            ('$id_order','$id_menu','$jumlah','$harga_satuan','$sub_total','$diskon','$total_item',$id_voucher)";
    $mysqli->query($sql);

    header("Location: index.php?page=order_detail");
    exit;
}

// --- Proses Edit Order Detail ---
if (isset($_POST['update'])) {
    $id_detail    = $_POST['id_order_detail'];
    $id_order     = $_POST['id_order'];
    $id_menu      = $_POST['id_menu'];
    $jumlah       = $_POST['jumlah'];
    $harga_satuan = $_POST['harga_satuan'];
    $sub_total    = $_POST['sub_total'];
    $diskon       = $_POST['diskon'];
    $total_item   = $_POST['total_item'];
    $id_voucher   = !empty($_POST['id_voucher']) ? $_POST['id_voucher'] : "NULL";

    $sql = "UPDATE order_detail SET 
                id_order='$id_order',
                id_menu='$id_menu',
                jumlah='$jumlah',
                harga_satuan='$harga_satuan',
                sub_total='$sub_total',
                diskon='$diskon',
                total_item='$total_item',
                id_voucher=$id_voucher
            WHERE id_order_detail='$id_detail'";
    $mysqli->query($sql);

    header("Location: index.php?page=order_detail");
    exit;
}

// --- Proses Hapus ---
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $mysqli->query("DELETE FROM order_detail WHERE id_order_detail=$id");
    header("Location: index.php?page=order_detail");
    exit;
}

// --- Ambil data order_detail ---
$sql = "SELECT od.*, 
               m.nama_menu, 
               v.kode_voucher
        FROM order_detail od
        LEFT JOIN menu m ON od.id_menu = m.id_menu
        LEFT JOIN voucher v ON od.id_voucher = v.id_voucher
        ORDER BY od.id_order_detail DESC";
$resultDetail = $mysqli->query($sql);

// --- Ambil data untuk form ---
$orders   = $mysqli->query("SELECT id_order FROM orders ORDER BY id_order DESC");
$menus    = $mysqli->query("SELECT id_menu, nama_menu FROM menu ORDER BY nama_menu ASC");
$vouchers = $mysqli->query("SELECT id_voucher, kode_voucher FROM voucher WHERE status='aktif' ORDER BY kode_voucher ASC");
?>

<!-- ====== CSS Order Detail ====== -->
<style>
.container-order {padding:20px;}
h1 {margin-bottom:20px;font-size:24px;color:#2e7d32;} /* hijau tua */
.actions {margin-bottom:15px;}

/* Tombol */
button,.btn {padding:6px 12px;border:none;border-radius:6px;cursor:pointer;font-size:13px;}
.btn-primary {background:#28a745;color:#fff;}
.btn-primary:hover {background:#218838;}
.btn-danger {background:#dc3545;color:#fff;}
.btn-danger:hover {background:#c82333;}
.btn-edit {background:#2ecc71;color:#fff;} /* hijau segar untuk Edit */
.btn-edit:hover {background:#27ae60;}
.btn-sm {font-size:12px;padding:4px 8px;}

/* Tabel */
.table-container {overflow-x:auto;background:#fff;padding:15px;border-radius:8px;box-shadow:0 2px 6px rgba(0,0,0,0.1);}
.table {width:100%;border-collapse:collapse;}
.table th,.table td {padding:10px;border-bottom:1px solid #ddd;text-align:left;}
.table th {background:#e8f5e9;color:#2e7d32;} /* hijau muda */
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
.form-popup input, .form-popup select {
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
  <h1>Manajemen Order Detail</h1>

  <!-- Tombol tambah -->
  <div class="actions">
    <button onclick="showFormDetail()" class="btn-primary">+ Tambah Detail</button>
  </div>

  <!-- Tabel order detail -->
  <div class="table-container">
    <table class="table" id="orderDetailTable">
      <thead>
        <tr>
          <th>ID</th>
          <th>ID Order</th>
          <th>Menu</th>
          <th>Jumlah</th>
          <th>Harga Satuan</th>
          <th>Sub Total</th>
          <th>Diskon</th>
          <th>Total Item</th>
          <th>Voucher</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = $resultDetail->fetch_assoc()): ?>
        <tr>
          <td><?= $row['id_order_detail'] ?></td>
          <td><?= $row['id_order'] ?></td>
          <td><?= htmlspecialchars($row['nama_menu']) ?></td>
          <td><?= $row['jumlah'] ?></td>
          <td>Rp <?= number_format($row['harga_satuan'],0,",",".") ?></td>
          <td>Rp <?= number_format($row['sub_total'],0,",",".") ?></td>
          <td>Rp <?= number_format($row['diskon'],0,",",".") ?></td>
          <td>Rp <?= number_format($row['total_item'],0,",",".") ?></td>
          <td><?= $row['kode_voucher'] ?? '-' ?></td>
          <td>
            <button class="btn-edit btn-sm" 
              onclick='showEditForm(<?= json_encode($row) ?>)'>Edit</button>
            <a href="index.php?page=order_detail&hapus=<?= $row['id_order_detail'] ?>" 
               class="btn-danger btn-sm" 
               onclick="return confirm('Hapus detail ini?')">Hapus</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Overlay -->
<div class="overlay" id="overlayDetail" onclick="hideForms()"></div>

<!-- ====== Form Tambah Order Detail ====== -->
<div id="formDetail" class="form-popup">
  <h3>Tambah Order Detail</h3>
  <form method="POST">
    <label>ID Order</label>
    <select name="id_order" required>
      <option value="">-- Pilih Order --</option>
      <?php
      $orders->data_seek(0);
      while($o = $orders->fetch_assoc()): ?>
        <option value="<?= $o['id_order'] ?>"><?= $o['id_order'] ?></option>
      <?php endwhile; ?>
    </select>

    <label>Menu</label>
    <select name="id_menu" required>
      <option value="">-- Pilih Menu --</option>
      <?php
      $menus->data_seek(0);
      while($m = $menus->fetch_assoc()): ?>
        <option value="<?= $m['id_menu'] ?>"><?= htmlspecialchars($m['nama_menu']) ?></option>
      <?php endwhile; ?>
    </select>

    <label>Jumlah</label>
    <input type="number" name="jumlah" min="1" value="1" required>

    <label>Harga Satuan</label>
    <input type="number" step="0.01" name="harga_satuan" required>

    <label>Sub Total</label>
    <input type="number" step="0.01" name="sub_total" required>

    <label>Diskon</label>
    <input type="number" step="0.01" name="diskon" value="0">

    <label>Total Item</label>
    <input type="number" step="0.01" name="total_item" required>

    <label>Voucher</label>
    <select name="id_voucher">
      <option value="">-- Pilih Voucher (Opsional) --</option>
      <?php
      $vouchers->data_seek(0);
      while($v = $vouchers->fetch_assoc()): ?>
        <option value="<?= $v['id_voucher'] ?>"><?= htmlspecialchars($v['kode_voucher']) ?></option>
      <?php endwhile; ?>
    </select>

    <br><br>
    <button type="submit" name="tambah" class="btn-primary">Simpan</button>
    <button type="button" onclick="hideForms()">Batal</button>
  </form>
</div>

<!-- ====== Form Edit Order Detail ====== -->
<div id="formEditDetail" class="form-popup">
  <h3>Edit Order Detail</h3>
  <form method="POST">
    <input type="hidden" name="id_order_detail" id="edit_id_order_detail">

    <label>ID Order</label>
    <input type="text" name="id_order" id="edit_id_order" required>

    <label>Menu</label>
    <input type="text" name="id_menu" id="edit_id_menu" required>

    <label>Jumlah</label>
    <input type="number" name="jumlah" id="edit_jumlah" min="1" required>

    <label>Harga Satuan</label>
    <input type="number" step="0.01" name="harga_satuan" id="edit_harga_satuan" required>

    <label>Sub Total</label>
    <input type="number" step="0.01" name="sub_total" id="edit_sub_total" required>

    <label>Diskon</label>
    <input type="number" step="0.01" name="diskon" id="edit_diskon">

    <label>Total Item</label>
    <input type="number" step="0.01" name="total_item" id="edit_total_item" required>

    <label>Voucher</label>
    <input type="text" name="id_voucher" id="edit_id_voucher">

    <br><br>
    <button type="submit" name="update" class="btn-edit">Update</button>
    <button type="button" onclick="hideForms()">Batal</button>
  </form>
</div>

<!-- ====== JavaScript ====== -->
<script>
function showFormDetail(){
  document.getElementById('formDetail').style.display = 'block';
  document.getElementById('overlayDetail').style.display = 'block';
}
function showEditForm(data){
  document.getElementById('formEditDetail').style.display = 'block';
  document.getElementById('overlayDetail').style.display = 'block';

  document.getElementById('edit_id_order_detail').value = data.id_order_detail;
  document.getElementById('edit_id_order').value = data.id_order;
  document.getElementById('edit_id_menu').value = data.id_menu;
  document.getElementById('edit_jumlah').value = data.jumlah;
  document.getElementById('edit_harga_satuan').value = data.harga_satuan;
  document.getElementById('edit_sub_total').value = data.sub_total;
  document.getElementById('edit_diskon').value = data.diskon;
  document.getElementById('edit_total_item').value = data.total_item;
  document.getElementById('edit_id_voucher').value = data.id_voucher;
}
function hideForms(){
  document.getElementById('formDetail').style.display = 'none';
  document.getElementById('formEditDetail').style.display = 'none';
  document.getElementById('overlayDetail').style.display = 'none';
}
</script>
