<?php
// koneksi database
include __DIR__ . "/../koneksi.php";

// --- Proses Tambah Lokasi Outlet ---
if (isset($_POST['tambah'])) {
    $nama_outlet   = $mysqli->real_escape_string($_POST['nama_outlet']);
    $alamat_outlet = $mysqli->real_escape_string($_POST['alamat_outlet']);
    $kota          = $mysqli->real_escape_string($_POST['kota']);
    $provinsi      = $mysqli->real_escape_string($_POST['provinsi']);
    $kode_pos      = $mysqli->real_escape_string($_POST['kode_pos']);

    $sql = "INSERT INTO lokasi_outlet (nama_outlet, alamat_outlet, kota, provinsi, kode_pos)
            VALUES ('$nama_outlet','$alamat_outlet','$kota','$provinsi','$kode_pos')";
    $mysqli->query($sql);
    header("Location: index.php?page=lokasi_outlet");
    exit;
}

// --- Proses Edit Lokasi Outlet ---
if (isset($_POST['edit'])) {
    $id            = intval($_POST['id_lokasi']);
    $nama_outlet   = $mysqli->real_escape_string($_POST['nama_outlet']);
    $alamat_outlet = $mysqli->real_escape_string($_POST['alamat_outlet']);
    $kota          = $mysqli->real_escape_string($_POST['kota']);
    $provinsi      = $mysqli->real_escape_string($_POST['provinsi']);
    $kode_pos      = $mysqli->real_escape_string($_POST['kode_pos']);

    $sql = "UPDATE lokasi_outlet 
            SET nama_outlet='$nama_outlet', alamat_outlet='$alamat_outlet', 
                kota='$kota', provinsi='$provinsi', kode_pos='$kode_pos'
            WHERE id_lokasi=$id";
    $mysqli->query($sql);
    header("Location: index.php?page=lokasi_outlet");
    exit;
}

// --- Proses Hapus Lokasi Outlet ---
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $mysqli->query("DELETE FROM lokasi_outlet WHERE id_lokasi=$id");
    header("Location: index.php?page=lokasi_outlet");
    exit;
}

// --- Ambil data lokasi outlet ---
$sql = "SELECT * FROM lokasi_outlet ORDER BY id_lokasi DESC";
$resultLokasi = $mysqli->query($sql);
?>

<!-- ====== CSS Lokasi Outlet ====== -->
<style>
.container-lokasi {padding:20px;}
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
.form-popup h3 {margin-top:0;color:#2e7d32;}
.form-popup label {font-weight:bold;display:block;margin-top:8px;}
.form-popup input, .form-popup textarea {
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

<div class="container-lokasi">
  <h1>Manajemen Lokasi Outlet</h1>

  <!-- Tombol tambah -->
  <div class="actions">
    <button onclick="showForm('tambah')" class="btn-primary">+ Tambah Outlet</button>
  </div>

  <!-- Tabel lokasi -->
  <div class="table-container">
    <table class="table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nama Outlet</th>
          <th>Alamat</th>
          <th>Kota</th>
          <th>Provinsi</th>
          <th>Kode Pos</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = $resultLokasi->fetch_assoc()): ?>
        <tr>
          <td><?= $row['id_lokasi'] ?></td>
          <td><?= htmlspecialchars($row['nama_outlet']) ?></td>
          <td><?= nl2br(htmlspecialchars($row['alamat_outlet'])) ?></td>
          <td><?= htmlspecialchars($row['kota']) ?></td>
          <td><?= htmlspecialchars($row['provinsi']) ?></td>
          <td><?= htmlspecialchars($row['kode_pos']) ?></td>
          <td>
            <button 
              class="btn-primary btn-sm" 
              onclick="editLokasi(
                <?= $row['id_lokasi'] ?>,
                '<?= htmlspecialchars($row['nama_outlet'],ENT_QUOTES) ?>',
                '<?= htmlspecialchars($row['alamat_outlet'],ENT_QUOTES) ?>',
                '<?= htmlspecialchars($row['kota'],ENT_QUOTES) ?>',
                '<?= htmlspecialchars($row['provinsi'],ENT_QUOTES) ?>',
                '<?= htmlspecialchars($row['kode_pos'],ENT_QUOTES) ?>'
              )">Edit</button>
            <a href="index.php?page=lokasi_outlet&hapus=<?= $row['id_lokasi'] ?>" 
               class="btn-danger btn-sm" 
               onclick="return confirm('Hapus outlet ini?')">Hapus</a>
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
<div id="formLokasi" class="form-popup">
  <h3 id="formTitle">Tambah Outlet</h3>
  <form method="POST">
    <input type="hidden" name="id_lokasi" id="id_lokasi">

    <label>Nama Outlet</label>
    <input type="text" name="nama_outlet" id="nama_outlet" required>

    <label>Alamat</label>
    <textarea name="alamat_outlet" id="alamat_outlet" required></textarea>

    <label>Kota</label>
    <input type="text" name="kota" id="kota" required>

    <label>Provinsi</label>
    <input type="text" name="provinsi" id="provinsi" required>

    <label>Kode Pos</label>
    <input type="text" name="kode_pos" id="kode_pos" required>

    <br><br>
    <button type="submit" name="tambah" id="btnSubmit" class="btn-primary">Simpan</button>
    <button type="button" onclick="hideForm()">Batal</button>
  </form>
</div>

<script>
function showForm(mode){
  document.getElementById('formLokasi').style.display = 'block';
  document.getElementById('overlay').style.display = 'block';
  if(mode==='tambah'){
    document.getElementById('formTitle').innerText = "Tambah Outlet";
    document.getElementById('btnSubmit').name = "tambah";
    document.getElementById('id_lokasi').value = "";
    document.getElementById('nama_outlet').value = "";
    document.getElementById('alamat_outlet').value = "";
    document.getElementById('kota').value = "";
    document.getElementById('provinsi').value = "";
    document.getElementById('kode_pos').value = "";
  }
}

function hideForm(){
  document.getElementById('formLokasi').style.display = 'none';
  document.getElementById('overlay').style.display = 'none';
}

function editLokasi(id, nama, alamat, kota, provinsi, kodepos){
  showForm('edit');
  document.getElementById('formTitle').innerText = "Edit Outlet";
  document.getElementById('btnSubmit').name = "edit";
  document.getElementById('id_lokasi').value = id;
  document.getElementById('nama_outlet').value = nama;
  document.getElementById('alamat_outlet').value = alamat;
  document.getElementById('kota').value = kota;
  document.getElementById('provinsi').value = provinsi;
  document.getElementById('kode_pos').value = kodepos;
}
</script>
