
<?php
session_start();

// --- Koneksi Database ---
$host = "localhost";
$user = "root"; // ganti sesuai user db
$pass = "";     // ganti sesuai password db
$db   = "paduan_tea"; // ganti sesuai nama database

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("Koneksi gagal: " . $conn->connect_error);
}

// --- Proses Login ---
$error = "";
if (isset($_POST['btnLogin'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // 🔹 Bypass untuk admin default
    if ($username === "admin" && $password === "admin123") {
        $_SESSION['admin'] = "admin";
        header("Location: index.php");
        exit;
    }

    // 🔹 Jika bukan admin default → cek database
    $query = "SELECT * FROM admin WHERE username='$username'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // cek password dengan hash
        if (password_verify($password, $row['password'])) {
            $_SESSION['admin'] = $row['username'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Admin - PaduanTea</title>
  <style>
    * {
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    body {
      margin: 0;
      padding: 0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background: linear-gradient(135deg, #1c4532, #2f855a);
    }
    .login-container {
      background: #fff;
      padding: 40px 35px;
      border-radius: 15px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.3);
      width: 380px;
      animation: fadeIn 0.8s ease-in-out;
      text-align: center;
    }
    .login-container img {
      width: 200px;
      margin-bottom: 20px;
    }
    .login-container h2 {
      margin-bottom: 20px;
      color: #2f855a;
      font-size: 22px;
    }
    .input-group {
      margin-bottom: 20px;
      text-align: left;
    }
    .input-group label {
      font-size: 14px;
      font-weight: bold;
      color: #2f855a;
    }
    .input-group input {
      width: 100%;
      padding: 12px;
      border: 2px solid #c6f6d5;
      border-radius: 8px;
      margin-top: 5px;
      font-size: 15px;
      transition: 0.3s;
    }
    .input-group input:focus {
      border-color: #2f855a;
      outline: none;
    }
    .btn-login {
      width: 100%;
      padding: 12px;
      background: #2f855a;
      border: none;
      border-radius: 8px;
      color: #fff;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.3s ease;
    }
    .btn-login:hover {
      background: #276749;
    }
    .error {
      color: red;
      font-size: 14px;
      margin-bottom: 15px;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
  <div class="login-container">
    <img src="../Image/LOGO.png" alt="Logo PaduanTea">
    <h2>Login Admin</h2>
    <?php if ($error != "") { echo "<div class='error'>$error</div>"; } ?>
    <form method="POST">
      <div class="input-group">
        <label for="username">Username</label>
        <input type="text" name="username" required>
      </div>
      <div class="input-group">
        <label for="password">Password</label>
        <input type="password" name="password" required>
      </div>
      <button type="submit" name="btnLogin" class="btn-login">Login</button>
    </form>
  </div>
</body>
</html>




