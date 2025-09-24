<?php
// koneksi database
include __DIR__ . "/../koneksi.php";

// --- Proses Tambah Voucher ---
if (isset($_POST['tambah'])) {
    $kode_voucher   = $_POST['kode_voucher'];
    $nama_voucher   = $_POST['nama_voucher'];
    $deskripsi      = $_POST['deskripsi'];
    $jenis_voucher  = $_POST['jenis_voucher'];
    $nilai_voucher  = $_POST['nilai_voucher'];
    $tanggal_mulai  = $_POST['tanggal_mulai'];
    $tanggal_selesai= $_POST['tanggal_selesai'];
    $status         = $_POST['status'];

    $sql = "INSERT INTO voucher (kode_voucher, nama_voucher, deskripsi, jenis_voucher, nilai_voucher, tanggal_mulai, tanggal_selesai, status)
            VALUES ('$kode_voucher','$nama_voucher','$deskripsi','$jenis_voucher','$nilai_voucher','$tanggal_mulai','$tanggal_selesai','$status')";
    $mysqli->query($sql);

    header("Location: index.php?page=voucher");
    exit;
}

// --- Proses Update Voucher ---
if (isset($_POST['update'])) {
    $id             = $_POST['id_voucher'];
    $kode_voucher   = $_POST['kode_voucher'];
    $nama_voucher   = $_POST['nama_voucher'];
    $deskripsi      = $_POST['deskripsi'];
    $jenis_voucher  = $_POST['jenis_voucher'];
    $nilai_voucher  = $_POST['nilai_voucher'];
    $tanggal_mulai  = $_POST['tanggal_mulai'];
    $tanggal_selesai= $_POST['tanggal_selesai'];
    $status         = $_POST['status'];

    $sql = "UPDATE voucher SET 
              kode_voucher='$kode_voucher',
              nama_voucher='$nama_voucher',
              deskripsi='$deskripsi',
              jenis_voucher='$jenis_voucher',
              nilai_voucher='$nilai_voucher',
              tanggal_mulai='$tanggal_mulai',
              tanggal_selesai='$tanggal_selesai',
              status='$status'
            WHERE id_voucher='$id'";
    $mysqli->query($sql);

    header("Location: index.php?page=voucher");
    exit;
}

// --- Proses Hapus Voucher ---
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $mysqli->query("DELETE FROM voucher WHERE id_voucher=$id");
    header("Location: index.php?page=voucher");
    exit;
}

// --- Ambil data voucher ---
$sql = "SELECT * FROM voucher ORDER BY id_voucher DESC";
$resultVoucher = $mysqli->query($sql);
?>

<!-- ====== CSS Voucher ====== -->
<style>
.container-voucher {padding:20px;}
h1 {margin-bottom:20px;font-size:24px;color:#2e7d32;}
.actions {margin-bottom:15px;}
button,.btn {padding:6px 12px;border:none;border-radius:6px;cursor:pointer;font-size:13px;}
.btn-primary {background:#28a745;color:#fff;}
.btn-primary:hover {background:#218838;}
.btn-danger {background:#dc3545;color:#fff;}
.btn-danger:hover {background:#c82333;}
.btn-success {background:#28a745;color:#fff;}
.btn-success:hover {background:#218838;}
.btn-sm {font-size:12px;padding:4px 8px;}
.table-container {overflow-x:auto;background:#fff;padding:15px;border-radius:8px;box-shadow:0 2px 6px rgba(0,0,0,0.1);}
.table {width:100%;border-collapse:collapse;}
.table th,.table td {padding:10px;border-bottom:1px solid #ddd;text-align:left;}
.table th {background:#e8f5e9;color:#2e7d32;}
.table tr:hover {background:#f9f9f9;}
.form-popup {
  background:#fff;padding:20px;border-radius:8px;max-width:600px;
  position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);
  z-index:100;display:none;box-shadow:0 4px 12px rgba(0,0,0,0.2);
}
.form-popup h3 {margin-top:0;color:#2e7d32;}
.form-popup label {font-weight:bold;display:block;margin-top:8px;}
.form-popup input,.form-popup select,.form-popup textarea {
  width:100%;padding:8px;margin-top:4px;border:1px solid #ccc;border-radius:6px;
}
.overlay {position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.4);display:none;z-index:99;}
</style>

<div class="container-voucher">
  <h1>Manajemen Voucher</h1>

  <!-- Tombol tambah -->
  <div class="actions">
    <button onclick="showFormVoucher()" class="btn-primary">+ Tambah Voucher</button>
  </div>

  <!-- Tabel voucher -->
  <div class="table-container">
    <table class="table" id="voucherTable">
      <thead>
        <tr>
          <th>ID</th>
          <th>Kode</th>
          <th>Nama</th>
          <th>Jenis</th>
          <th>Nilai</th>
          <th>Tanggal Mulai</th>
          <th>Tanggal Selesai</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = $resultVoucher->fetch_assoc()): ?>
        <tr>
          <td><?= $row['id_voucher'] ?></td>
          <td><?= htmlspecialchars($row['kode_voucher']) ?></td>
          <td><?= htmlspecialchars($row['nama_voucher']) ?></td>
          <td><?= ucfirst($row['jenis_voucher']) ?></td>
          <td><?= $row['jenis_voucher']=='persen' ? $row['nilai_voucher'].' %' : 'Rp '.number_format($row['nilai_voucher'],0,",",".") ?></td>
          <td><?= $row['tanggal_mulai'] ?></td>
          <td><?= $row['tanggal_selesai'] ?></td>
          <td><?= isset($row['status']) ? ucfirst($row['status']) : '-' ?></td>
          <td>
            <button class="btn-success btn-sm" 
                    onclick='editVoucher(<?= json_encode($row) ?>)'>Edit</button>
            <a href="index.php?page=voucher&hapus=<?= $row['id_voucher'] ?>" 
               class="btn-danger btn-sm" 
               onclick="return confirm('Hapus voucher ini?')">Hapus</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Overlay -->
<div class="overlay" id="overlayVoucher" onclick="hideFormVoucher()"></div>

<!-- ====== Form Tambah/Edit Voucher ====== -->
<div id="formVoucher" class="form-popup">
  <h3 id="formTitle">Tambah Voucher</h3>
  <form method="POST" id="voucherForm">
    <input type="hidden" name="id_voucher" id="id_voucher">

    <label>Kode Voucher</label>
    <input type="text" name="kode_voucher" id="kode_voucher" required>

    <label>Nama Voucher</label>
    <input type="text" name="nama_voucher" id="nama_voucher" required>

    <label>Deskripsi</label>
    <textarea name="deskripsi" id="deskripsi"></textarea>

    <label>Jenis Voucher</label>
    <select name="jenis_voucher" id="jenis_voucher" required>
      <option value="persen">Persen (%)</option>
      <option value="nominal">Nominal (Rp)</option>
    </select>

    <label>Nilai Voucher</label>
    <input type="number" step="0.01" name="nilai_voucher" id="nilai_voucher" required>

    <label>Tanggal Mulai</label>
    <input type="date" name="tanggal_mulai" id="tanggal_mulai" required>

    <label>Tanggal Selesai</label>
    <input type="date" name="tanggal_selesai" id="tanggal_selesai" required>

    <label>Status</label>
    <select name="status" id="status" required>
      <option value="aktif">Aktif</option>
      <option value="nonaktif">Nonaktif</option>
    </select>

    <br><br>
    <button type="submit" name="tambah" id="btnSubmit" class="btn-primary">Simpan</button>
    <button type="button" onclick="hideFormVoucher()" class="btn-danger">Batal</button>
  </form>
</div>

<script>
function showFormVoucher(){
  document.getElementById('voucherForm').reset();
  document.getElementById('id_voucher').value = "";
  document.getElementById('formTitle').innerText = "Tambah Voucher";
  document.getElementById('btnSubmit').name = "tambah";
  document.getElementById('formVoucher').style.display = 'block';
  document.getElementById('overlayVoucher').style.display = 'block';
}
function hideFormVoucher(){
  document.getElementById('formVoucher').style.display = 'none';
  document.getElementById('overlayVoucher').style.display = 'none';
}
function editVoucher(data){
  document.getElementById('formTitle').innerText = "Edit Voucher";
  document.getElementById('btnSubmit').name = "update";
  document.getElementById('id_voucher').value = data.id_voucher;
  document.getElementById('kode_voucher').value = data.kode_voucher;
  document.getElementById('nama_voucher').value = data.nama_voucher;
  document.getElementById('deskripsi').value = data.deskripsi;
  document.getElementById('jenis_voucher').value = data.jenis_voucher;
  document.getElementById('nilai_voucher').value = data.nilai_voucher;
  document.getElementById('tanggal_mulai').value = data.tanggal_mulai;
  document.getElementById('tanggal_selesai').value = data.tanggal_selesai;
  document.getElementById('status').value = data.status;

  document.getElementById('formVoucher').style.display = 'block';
  document.getElementById('overlayVoucher').style.display = 'block';
}
</script>
