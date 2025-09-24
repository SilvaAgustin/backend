<?php
// koneksi database
include __DIR__ . "/../koneksi.php";

// --- Proses Tambah Keranjang ---
if (isset($_POST['tambah'])) {
    $id_akun   = intval($_POST['id_akun']);
    $id_menu   = intval($_POST['id_menu']);
    $jumlah    = intval($_POST['jumlah']);

    $sql = "INSERT INTO keranjang (id_akun, id_menu, jumlah)
            VALUES ('$id_akun','$id_menu','$jumlah')";
    $mysqli->query($sql);
    header("Location: index.php?page=keranjang");
    exit;
}

// --- Proses Edit Keranjang ---
if (isset($_POST['edit'])) {
    $id        = intval($_POST['id_keranjang']);
    $id_akun   = intval($_POST['id_akun']);
    $id_menu   = intval($_POST['id_menu']);
    $jumlah    = intval($_POST['jumlah']);

    $sql = "UPDATE keranjang 
            SET id_akun='$id_akun', id_menu='$id_menu', jumlah='$jumlah'
            WHERE id_keranjang=$id";
    $mysqli->query($sql);
    header("Location: index.php?page=keranjang");
    exit;
}

// --- Proses Hapus Keranjang ---
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $mysqli->query("DELETE FROM keranjang WHERE id_keranjang=$id");
    header("Location: index.php?page=keranjang");
    exit;
}

// --- Ambil data keranjang (join akun & menu) ---
$sql = "SELECT k.*, 
               a.nama AS nama_akun, 
               m.nama_menu 
        FROM keranjang k
        LEFT JOIN akun a ON k.id_akun = a.id_akun
        LEFT JOIN menu m ON k.id_menu = m.id_menu
        ORDER BY k.id_keranjang DESC";
$resultKeranjang = $mysqli->query($sql);

// --- Data referensi akun & menu untuk form ---
$akun = $mysqli->query("SELECT id_akun, nama FROM akun ORDER BY nama ASC");
$menu = $mysqli->query("SELECT id_menu, nama_menu FROM menu ORDER BY nama_menu ASC");
?>

<!-- ====== CSS Keranjang ====== -->
<style>
.container-keranjang {padding:20px;}
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
  max-width:450px;
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

<div class="container-keranjang">
  <h1>Manajemen Keranjang</h1>

  <!-- Tombol tambah -->
  <div class="actions">
    <button onclick="showForm('tambah')" class="btn-primary">+ Tambah Item</button>
  </div>

  <!-- Tabel keranjang -->
  <div class="table-container">
    <table class="table" id="keranjangTable">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nama Akun</th>
          <th>Nama Menu</th>
          <th>Jumlah</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = $resultKeranjang->fetch_assoc()): ?>
        <tr>
          <td><?= $row['id_keranjang'] ?></td>
          <td><?= htmlspecialchars($row['nama_akun']) ?></td>
          <td><?= htmlspecialchars($row['nama_menu']) ?></td>
          <td><?= $row['jumlah'] ?></td>
          <td>
            <button 
              class="btn-primary btn-sm" 
              onclick="editKeranjang(<?= $row['id_keranjang'] ?>,'<?= $row['id_akun'] ?>','<?= $row['id_menu'] ?>','<?= $row['jumlah'] ?>')">
              Edit
            </button>
            <a href="index.php?page=keranjang&hapus=<?= $row['id_keranjang'] ?>" 
               class="btn-danger btn-sm" 
               onclick="return confirm('Hapus item keranjang ini?')">Hapus</a>
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
<div id="formKeranjang" class="form-popup">
  <h3 id="formTitle">Tambah Item</h3>
  <form method="POST">
    <input type="hidden" name="id_keranjang" id="id_keranjang">

    <label>Nama Akun</label>
    <select name="id_akun" id="id_akun" required>
      <option value="">-- Pilih Akun --</option>
      <?php 
      $resultAkun = $mysqli->query("SELECT id_akun,nama FROM akun ORDER BY nama ASC");
      while($a=$resultAkun->fetch_assoc()): ?>
        <option value="<?= $a['id_akun'] ?>"><?= htmlspecialchars($a['nama']) ?></option>
      <?php endwhile; ?>
    </select>

    <label>Nama Menu</label>
    <select name="id_menu" id="id_menu" required>
      <option value="">-- Pilih Menu --</option>
      <?php 
      $resultMenu = $mysqli->query("SELECT id_menu,nama_menu FROM menu ORDER BY nama_menu ASC");
      while($m=$resultMenu->fetch_assoc()): ?>
        <option value="<?= $m['id_menu'] ?>"><?= htmlspecialchars($m['nama_menu']) ?></option>
      <?php endwhile; ?>
    </select>

    <label>Jumlah</label>
    <input type="number" name="jumlah" id="jumlah" required min="1">

    <br><br>
    <button type="submit" name="tambah" id="btnSubmit" class="btn-primary">Simpan</button>
    <button type="button" onclick="hideForm()">Batal</button>
  </form>
</div>

<script>
function showForm(mode){
  document.getElementById('formKeranjang').style.display = 'block';
  document.getElementById('overlay').style.display = 'block';
  if(mode==='tambah'){
    document.getElementById('formTitle').innerText = "Tambah Item Keranjang";
    document.getElementById('btnSubmit').name = "tambah";
    document.getElementById('id_keranjang').value = "";
    document.getElementById('id_akun').value = "";
    document.getElementById('id_menu').value = "";
    document.getElementById('jumlah').value = "";
  }
}

function hideForm(){
  document.getElementById('formKeranjang').style.display = 'none';
  document.getElementById('overlay').style.display = 'none';
}

function editKeranjang(id, akun, menu, jumlah){
  showForm('edit');
  document.getElementById('formTitle').innerText = "Edit Item Keranjang";
  document.getElementById('btnSubmit').name = "edit";
  document.getElementById('id_keranjang').value = id;
  document.getElementById('id_akun').value = akun;
  document.getElementById('id_menu').value = menu;
  document.getElementById('jumlah').value = jumlah;
}
</script>
