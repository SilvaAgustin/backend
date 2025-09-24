<?php
// koneksi database
include __DIR__ . "/../koneksi.php";

// --- Proses Tambah History ---
if (isset($_POST['tambah'])) {
    $id_order  = intval($_POST['id_order']);
    $id_akun   = intval($_POST['id_akun']);
    $tanggal   = $_POST['tanggal_pembelian'];
    $keterangan= $_POST['keterangan'];
    $total     = floatval($_POST['total_bayar']);

    $sql = "INSERT INTO history_pembelian (id_order, id_akun, tanggal_pembelian, keterangan, total_bayar)
            VALUES ('$id_order','$id_akun','$tanggal','$keterangan','$total')";
    $mysqli->query($sql);
    header("Location: index.php?page=history_pembelian");
    exit;
}

// --- Proses Edit History ---
if (isset($_POST['edit'])) {
    $id        = intval($_POST['id_history']);
    $id_order  = intval($_POST['id_order']);
    $id_akun   = intval($_POST['id_akun']);
    $tanggal   = $_POST['tanggal_pembelian'];
    $keterangan= $_POST['keterangan'];
    $total     = floatval($_POST['total_bayar']);

    $sql = "UPDATE history_pembelian 
            SET id_order='$id_order', id_akun='$id_akun', tanggal_pembelian='$tanggal',
                keterangan='$keterangan', total_bayar='$total'
            WHERE id_history=$id";
    $mysqli->query($sql);
    header("Location: index.php?page=history_pembelian");
    exit;
}

// --- Proses Hapus History ---
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $mysqli->query("DELETE FROM history_pembelian WHERE id_history=$id");
    header("Location: index.php?page=history_pembelian");
    exit;
}

// --- Ambil data history (join akun & order) ---
$sql = "SELECT h.*, a.nama AS nama_akun, o.id_order 
        FROM history_pembelian h
        LEFT JOIN akun a ON h.id_akun = a.id_akun
        LEFT JOIN orders o ON h.id_order = o.id_order
        ORDER BY h.id_history DESC";
$resultHistory = $mysqli->query($sql);

// --- Data referensi akun & order untuk form ---
$akun = $mysqli->query("SELECT id_akun, nama FROM akun ORDER BY nama ASC");
$orders = $mysqli->query("SELECT id_order FROM orders ORDER BY id_order DESC");
?>

<!-- ====== CSS History Pembelian ====== -->
<style>
.container-history {padding:20px;}
h1 {margin-bottom:20px;font-size:24px;color:#2e7d32;} /* hijau tua */
.actions {margin-bottom:15px;}

.table-container {
  overflow-x:auto;
  background:#fff;
  padding:15px;
  border-radius:8px;
  box-shadow:0 2px 6px rgba(0,0,0,0.1);
}
.table {width:100%;border-collapse:collapse;}
.table th,.table td {padding:10px;border-bottom:1px solid #ddd;text-align:left;}
.table th {background:#e8f5e9;color:#2e7d32;} /* hijau muda */
.table tr:hover {background:#f9f9f9;}

/* Tombol */
button,.btn {
  padding:6px 12px;
  border:none;
  border-radius:6px;
  cursor:pointer;
  font-size:13px;
}
.btn-primary {background:#28a745;color:#fff;} /* hijau */
.btn-primary:hover {background:#218838;}
.btn-danger {background:#dc3545;color:#fff;}
.btn-danger:hover {background:#c82333;}
.btn-sm {font-size:12px;padding:4px 8px;}

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
.form-popup h3 {
  margin-top:0;
  color:#2e7d32;
}
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

<div class="container-history">
  <h1>Manajemen History Pembelian</h1>

  <!-- Tombol tambah -->
  <div class="actions">
    <button onclick="showForm('tambah')" class="btn-primary">+ Tambah History</button>
  </div>

  <!-- Tabel history -->
  <div class="table-container">
    <table class="table" id="historyTable">
      <thead>
        <tr>
          <th>ID</th>
          <th>ID Order</th>
          <th>Nama Akun</th>
          <th>Tanggal</th>
          <th>Keterangan</th>
          <th>Total Bayar</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = $resultHistory->fetch_assoc()): ?>
        <tr>
          <td><?= $row['id_history'] ?></td>
          <td><?= $row['id_order'] ?></td>
          <td><?= htmlspecialchars($row['nama_akun']) ?></td>
          <td><?= $row['tanggal_pembelian'] ?></td>
          <td><?= htmlspecialchars($row['keterangan']) ?></td>
          <td><?= number_format($row['total_bayar'],2,',','.') ?></td>
          <td>
            <button 
              class="btn-primary btn-sm" 
              onclick="editHistory(<?= $row['id_history'] ?>,'<?= $row['id_order'] ?>','<?= $row['id_akun'] ?>','<?= $row['tanggal_pembelian'] ?>','<?= htmlspecialchars($row['keterangan'], ENT_QUOTES) ?>','<?= $row['total_bayar'] ?>')">
              Edit
            </button>
            <a href="index.php?page=history_pembelian&hapus=<?= $row['id_history'] ?>" 
               class="btn-danger btn-sm" 
               onclick="return confirm('Hapus history ini?')">Hapus</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Overlay -->
<div class="overlay" id="overlay" onclick="hideForm()"></div>

<!-- Form Tambah/Edit -->
<div id="formHistory" class="form-popup">
  <h3 id="formTitle">Tambah History</h3>
  <form method="POST">
    <input type="hidden" name="id_history" id="id_history">

    <label>ID Order</label>
    <select name="id_order" id="id_order" required>
      <option value="">-- Pilih Order --</option>
      <?php while($o=$orders->fetch_assoc()): ?>
        <option value="<?= $o['id_order'] ?>"><?= $o['id_order'] ?></option>
      <?php endwhile; ?>
    </select>

    <label>Nama Akun</label>
    <select name="id_akun" id="id_akun" required>
      <option value="">-- Pilih Akun --</option>
      <?php while($a=$akun->fetch_assoc()): ?>
        <option value="<?= $a['id_akun'] ?>"><?= htmlspecialchars($a['nama']) ?></option>
      <?php endwhile; ?>
    </select>

    <label>Tanggal Pembelian</label>
    <input type="date" name="tanggal_pembelian" id="tanggal_pembelian" required>

    <label>Keterangan</label>
    <textarea name="keterangan" id="keterangan" rows="3"></textarea>

    <label>Total Bayar</label>
    <input type="number" step="0.01" name="total_bayar" id="total_bayar" required>

    <br><br>
    <button type="submit" name="tambah" id="btnSubmit" class="btn-primary">Simpan</button>
    <button type="button" onclick="hideForm()">Batal</button>
  </form>
</div>

<script>
function showForm(mode){
  document.getElementById('formHistory').style.display = 'block';
  document.getElementById('overlay').style.display = 'block';
  if(mode==='tambah'){
    document.getElementById('formTitle').innerText = "Tambah History Pembelian";
    document.getElementById('btnSubmit').name = "tambah";
    document.getElementById('id_history').value = "";
    document.getElementById('id_order').value = "";
    document.getElementById('id_akun').value = "";
    document.getElementById('tanggal_pembelian').value = "";
    document.getElementById('keterangan').value = "";
    document.getElementById('total_bayar').value = "";
  }
}

function hideForm(){
  document.getElementById('formHistory').style.display = 'none';
  document.getElementById('overlay').style.display = 'none';
}

function editHistory(id, order, akun, tanggal, keterangan, total){
  showForm('edit');
  document.getElementById('formTitle').innerText = "Edit History Pembelian";
  document.getElementById('btnSubmit').name = "edit";
  document.getElementById('id_history').value = id;
  document.getElementById('id_order').value = order;
  document.getElementById('id_akun').value = akun;
  document.getElementById('tanggal_pembelian').value = tanggal;
  document.getElementById('keterangan').value = keterangan;
  document.getElementById('total_bayar').value = total;
}
</script>
