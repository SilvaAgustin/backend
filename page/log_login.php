<?php
// koneksi database
include __DIR__ . "/../koneksi.php";

// --- Proses Tambah Log ---
if (isset($_POST['tambah'])) {
    $id_akun = intval($_POST['id_akun']);
    $waktu_login = $_POST['waktu_login'];
    $waktu_logout = !empty($_POST['waktu_logout']) ? $_POST['waktu_logout'] : NULL;
    $ip_address = $_POST['ip_address'];

    $sql = "INSERT INTO log_login (id_akun, waktu_login, waktu_logout, ip_address)
            VALUES ('$id_akun','$waktu_login',".($waktu_logout ? "'$waktu_logout'" : "NULL").",'$ip_address')";
    $mysqli->query($sql);
    header("Location: index.php?page=log_login");
    exit;
}

// --- Proses Edit Log ---
if (isset($_POST['edit'])) {
    $id_login = intval($_POST['id_login']);
    $id_akun = intval($_POST['id_akun']);
    $waktu_login = $_POST['waktu_login'];
    $waktu_logout = !empty($_POST['waktu_logout']) ? $_POST['waktu_logout'] : NULL;
    $ip_address = $_POST['ip_address'];

    $sql = "UPDATE log_login 
            SET id_akun='$id_akun',
                waktu_login='$waktu_login',
                waktu_logout=".($waktu_logout ? "'$waktu_logout'" : "NULL").",
                ip_address='$ip_address'
            WHERE id_login=$id_login";
    $mysqli->query($sql);
    header("Location: index.php?page=log_login");
    exit;
}

// --- Proses Hapus Log ---
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $mysqli->query("DELETE FROM log_login WHERE id_login=$id");
    header("Location: index.php?page=log_login");
    exit;
}

// --- Ambil data log login ---
$sql = "SELECT l.*, a.nama AS nama_akun, a.email 
        FROM log_login l
        LEFT JOIN akun a ON l.id_akun = a.id_akun
        ORDER BY l.id_login DESC";
$resultLog = $mysqli->query($sql);

// --- Data referensi akun ---
$akun = $mysqli->query("SELECT id_akun, nama FROM akun ORDER BY nama ASC");
?>

<!-- ====== CSS Log Login ====== -->
<style>
.container-log {padding:20px;}
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
.table th {background:#e8f5e9;color:#2e7d32;}
.table tr:hover {background:#f9f9f9;}

/* Tombol */
button,.btn {
  padding:6px 12px;
  border:none;
  border-radius:6px;
  cursor:pointer;
  font-size:13px;
}
.btn-primary {background:#28a745;color:#fff;}
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

<div class="container-log">
  <h1>Log Aktivitas Login</h1>

  <!-- Tombol tambah -->
  <div class="actions">
    <button onclick="showForm('tambah')" class="btn-primary">+ Tambah Log</button>
  </div>

  <!-- Tabel log -->
  <div class="table-container">
    <table class="table" id="logTable">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nama Akun</th>
          <th>Email</th>
          <th>Waktu Login</th>
          <th>Waktu Logout</th>
          <th>IP Address</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = $resultLog->fetch_assoc()): ?>
        <tr>
          <td><?= $row['id_login'] ?></td>
          <td><?= htmlspecialchars($row['nama_akun']) ?></td>
          <td><?= htmlspecialchars($row['email']) ?></td>
          <td><?= $row['waktu_login'] ?></td>
          <td><?= $row['waktu_logout'] ? $row['waktu_logout'] : '-' ?></td>
          <td><?= htmlspecialchars($row['ip_address']) ?></td>
          <td>
            <button 
              class="btn-primary btn-sm" 
              onclick="editLog(<?= $row['id_login'] ?>,'<?= $row['id_akun'] ?>','<?= $row['waktu_login'] ?>','<?= $row['waktu_logout'] ?>','<?= $row['ip_address'] ?>')">
              Edit
            </button>
            <a href="index.php?page=log_login&hapus=<?= $row['id_login'] ?>" 
               class="btn-danger btn-sm" 
               onclick="return confirm('Hapus log ini?')">Hapus</a>
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
<div id="formLog" class="form-popup">
  <h3 id="formTitle">Tambah Log</h3>
  <form method="POST">
    <input type="hidden" name="id_login" id="id_login">

    <label>Nama Akun</label>
    <select name="id_akun" id="id_akun" required>
      <option value="">-- Pilih Akun --</option>
      <?php while($a=$akun->fetch_assoc()): ?>
        <option value="<?= $a['id_akun'] ?>"><?= htmlspecialchars($a['nama']) ?></option>
      <?php endwhile; ?>
    </select>

    <label>Waktu Login</label>
    <input type="datetime-local" name="waktu_login" id="waktu_login" required>

    <label>Waktu Logout</label>
    <input type="datetime-local" name="waktu_logout" id="waktu_logout">

    <label>IP Address</label>
    <input type="text" name="ip_address" id="ip_address" required>

    <br><br>
    <button type="submit" name="tambah" id="btnSubmit" class="btn-primary">Simpan</button>
    <button type="button" onclick="hideForm()">Batal</button>
  </form>
</div>

<script>
function showForm(mode){
  document.getElementById('formLog').style.display = 'block';
  document.getElementById('overlay').style.display = 'block';
  if(mode==='tambah'){
    document.getElementById('formTitle').innerText = "Tambah Log";
    document.getElementById('btnSubmit').name = "tambah";
    document.getElementById('id_login').value = "";
    document.getElementById('id_akun').value = "";
    document.getElementById('waktu_login').value = "";
    document.getElementById('waktu_logout').value = "";
    document.getElementById('ip_address').value = "";
  }
}

function hideForm(){
  document.getElementById('formLog').style.display = 'none';
  document.getElementById('overlay').style.display = 'none';
}

function editLog(id, akun, login, logout, ip){
  showForm('edit');
  document.getElementById('formTitle').innerText = "Edit Log";
  document.getElementById('btnSubmit').name = "edit";
  document.getElementById('id_login').value = id;
  document.getElementById('id_akun').value = akun;
  document.getElementById('waktu_login').value = login.replace(" ", "T");
  document.getElementById('waktu_logout').value = logout ? logout.replace(" ", "T") : "";
  document.getElementById('ip_address').value = ip;
}
</script>
