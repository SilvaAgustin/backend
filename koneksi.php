<?php

// Variabel koneksi db
$local ="localhost";
$user ="root";
$password ="";
$db ="paduan_tea";

// Koneksi ke database
$mysqli = new mysqli($local,$user,$password,$db);

//cek koneksi
If ($mysqli ->connect_errno){
    echo "Failed to connect to MySQL : " . $mysqli->connect_error;
    exit();
}
else{
   // echo "database connected";
}
?>

