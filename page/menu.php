<?php
// koneksi database
include __DIR__ . "/../koneksi.php";

// --- Proses Tambah Menu ---
if (isset($_POST['tambah'])) {
    $id_lokasi = $_POST['id_lokasi'];
    $nama_menu = $_POST['nama_menu'];
    $deskripsi = $_POST['deskripsi'];
    $harga     = $_POST['harga'];
    $stok      = $_POST['stok'];

    // upload gambar
    $gambar = "";
    if (!empty($_FILES['gambar']['name'])) {
        $uploadDir = __DIR__ . "/../uploads/";

        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $filename = time() . "_" . basename($_FILES['gambar']['name']);
        $targetFile = $uploadDir . $filename;
        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $targetFile)) {
             $gambar = "admin/uploads/" . $filename;
        }
    }

    $sql = "INSERT INTO menu (id_lokasi, nama_menu, deskripsi, harga, stok, gambar)
            VALUES ('$id_lokasi','$nama_menu','$deskripsi','$harga','$stok','$gambar')";
    $mysqli->query($sql);
    header("Location: index.php?page=menu");
    exit;
}

// --- Proses Edit Menu ---
if (isset($_POST['edit'])) {
    $id_menu   = $_POST['id_menu'];
    $id_lokasi = $_POST['id_lokasi'];
    $nama_menu = $_POST['nama_menu'];
    $deskripsi = $_POST['deskripsi'];
    $harga     = $_POST['harga'];
    $stok      = $_POST['stok'];

    // cek jika ada gambar baru
    $gambar = $_POST['gambar_lama'];
    if (!empty($_FILES['gambar']['name'])) {
        $uploadDir = __DIR__ . "/../uploads/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $filename = time() . "_" . basename($_FILES['gambar']['name']);
        $targetFile = $uploadDir . $filename;
        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $targetFile)) {
            // hapus gambar lama
            if (!empty($gambar) && file_exists(__DIR__ . "/../" . $gambar)) {
                unlink(__DIR__ . "/../" . $gambar);
            }
            $gambar = "uploads/" . $filename;
        }
    }

    $sql = "UPDATE menu 
            SET id_lokasi='$id_lokasi', nama_menu='$nama_menu', deskripsi='$deskripsi',
                harga='$harga', stok='$stok', gambar='$gambar'
            WHERE id_menu='$id_menu'";
    $mysqli->query($sql);
    header("Location: index.php?page=menu");
    exit;
}

// --- Proses Hapus Menu ---
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $res = $mysqli->query("SELECT gambar FROM menu WHERE id_menu=$id");
    if ($res && $row = $res->fetch_assoc()) {
        if (!empty($row['gambar']) && file_exists(__DIR__ . "/../" . $row['gambar'])) {
            unlink(__DIR__ . "/../" . $row['gambar']);
        }
    }
    $mysqli->query("DELETE FROM menu WHERE id_menu=$id");
    header("Location: index.php?page=menu");
    exit;
}

// --- Ambil data menu (join lokasi)---
$sql = "SELECT m.*, l.nama_outlet
        FROM menu m
        LEFT JOIN lokasi_outlet l ON m.id_lokasi=l.id_lokasi
        ORDER BY m.id_menu DESC";
$resultMenu = $mysqli->query($sql);

$lokasiData = $mysqli->query("SELECT * FROM lokasi_outlet ORDER BY nama_outlet ASC");
?>

<!-- ====== CSS Menu ====== -->
<style>
.container-menu {padding:20px;}
h1 {margin-bottom:20px;font-size:24px;color:#2e7d32;} /* hijau tua */
.actions {margin-bottom:15px;}

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

/* Tabel */
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
img.thumb {max-width:80px;max-height:80px;border-radius:4px;}

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

<div class="container-menu">
  <h1>Manajemen Menu</h1>

  <!-- Tombol tambah -->
  <div class="actions">
    <button onclick="showFormMenu()" class="btn-primary">+ Tambah Menu</button>
  </div>

  <!-- Tabel menu -->
  <div class="table-container">
    <table class="table" id="menuTable">
      <thead>
        <tr>
          <th>ID</th>
          <th>Outlet</th>
          <th>Nama Menu</th>
          <th>Deskripsi</th>
          <th>Harga</th>
          <th>Stok</th>
          <th>Gambar</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = $resultMenu->fetch_assoc()): ?>
        <tr>
          <td><?= $row['id_menu'] ?></td>
          <td><?= htmlspecialchars($row['nama_outlet']) ?></td>
          <td><?= htmlspecialchars($row['nama_menu']) ?></td>
          <td><?= htmlspecialchars($row['deskripsi']) ?></td>
          <td>Rp <?= number_format($row['harga'],0,",",".") ?></td>
          <td><?= $row['stok'] ?></td>
          <td>
            <?php if(!empty($row['gambar'])): ?>
              <img src="<?= $row['gambar'] ?>" class="thumb">
            <?php else: ?>
              <span>-</span>
            <?php endif; ?>
          </td>
          <td>
            <!-- Tombol Edit (hijau) -->
            <button class="btn-primary btn-sm" 
                    onclick="showFormEditMenu(<?= htmlspecialchars(json_encode($row)) ?>)">
              Edit
            </button>
            <!-- Tombol Hapus -->
            <a href="index.php?page=menu&hapus=<?= $row['id_menu'] ?>" 
               class="btn-danger btn-sm" 
               onclick="return confirm('Hapus menu ini?')">Hapus</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Overlay -->
<div class="overlay" id="overlayMenu" onclick="hideFormMenu()"></div>

<!-- ====== Form Tambah Menu ====== -->
<div id="formMenu" class="form-popup">
  <h3>Tambah Menu</h3>
  <form method="POST" enctype="multipart/form-data">
    <label>Outlet</label>
    <select name="id_lokasi" required>
      <option value="">-- Pilih Outlet --</option>
      <?php 
      $lokasiData = $mysqli->query("SELECT * FROM lokasi_outlet ORDER BY nama_outlet ASC");
      while($lok = $lokasiData->fetch_assoc()): ?>
        <option value="<?= $lok['id_lokasi'] ?>"><?= htmlspecialchars($lok['nama_outlet']) ?></option>
      <?php endwhile; ?>
    </select>

    <label>Nama Menu</label>
    <input type="text" name="nama_menu" required>

    <label>Deskripsi</label>
    <textarea name="deskripsi" required></textarea>

    <label>Harga</label>
    <input type="number" step="0.01" name="harga" required>

    <label>Stok</label>
    <input type="number" name="stok" required>

    <label>Gambar</label>
    <input type="file" name="gambar" accept="image/*">

    <br><br>
    <button type="submit" name="tambah" class="btn-primary">Simpan</button>
    <button type="button" onclick="hideFormMenu()">Batal</button>
  </form>
</div>

<!-- ====== Form Edit Menu ====== -->
<div id="formEditMenu" class="form-popup">
  <h3>Edit Menu</h3>
  <form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id_menu" id="edit_id_menu">
    <input type="hidden" name="gambar_lama" id="edit_gambar_lama">

    <label>Outlet</label>
    <select name="id_lokasi" id="edit_id_lokasi" required>
      <option value="">-- Pilih Outlet --</option>
      <?php 
      $lokasiData2 = $mysqli->query("SELECT * FROM lokasi_outlet ORDER BY nama_outlet ASC");
      while($lok = $lokasiData2->fetch_assoc()): ?>
        <option value="<?= $lok['id_lokasi'] ?>"><?= htmlspecialchars($lok['nama_outlet']) ?></option>
      <?php endwhile; ?>
    </select>

    <label>Nama Menu</label>
    <input type="text" name="nama_menu" id="edit_nama_menu" required>

    <label>Deskripsi</label>
    <textarea name="deskripsi" id="edit_deskripsi" required></textarea>

    <label>Harga</label>
    <input type="number" step="0.01" name="harga" id="edit_harga" required>

    <label>Stok</label>
    <input type="number" name="stok" id="edit_stok" required>

    <label>Gambar</label>
    <input type="file" name="gambar" accept="image/*">

    <br><br>
    <button type="submit" name="edit" class="btn-primary">Update</button>
    <button type="button" onclick="hideFormEditMenu()">Batal</button>
  </form>
</div>

<script>
function showFormMenu(){
  document.getElementById('formMenu').style.display = 'block';
  document.getElementById('overlayMenu').style.display = 'block';
}
function hideFormMenu(){
  document.getElementById('formMenu').style.display = 'none';
  document.getElementById('overlayMenu').style.display = 'none';
}
function showFormEditMenu(row){
  document.getElementById('formEditMenu').style.display = 'block';
  document.getElementById('overlayMenu').style.display = 'block';

  // isi data ke form edit
  document.getElementById('edit_id_menu').value = row.id_menu;
  document.getElementById('edit_id_lokasi').value = row.id_lokasi;
  document.getElementById('edit_nama_menu').value = row.nama_menu;
  document.getElementById('edit_deskripsi').value = row.deskripsi;
  document.getElementById('edit_harga').value = row.harga;
  document.getElementById('edit_stok').value = row.stok;
  document.getElementById('edit_gambar_lama').value = row.gambar;
}
function hideFormEditMenu(){
  document.getElementById('formEditMenu').style.display = 'none';
  document.getElementById('overlayMenu').style.display = 'none';
}
</script>
