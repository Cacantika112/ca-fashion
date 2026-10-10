<?php
require 'config.php';
$count = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
if ($count === 0) {
    $stmt = $pdo->prepare("INSERT INTO users (username,password_hash,full_name,role) VALUES (?,?,?,?)");
    $stmt->execute(['admin', password_hash('password', PASSWORD_DEFAULT), 'Administrator', 'admin']);
    $stmt->execute(['staff', password_hash('password', PASSWORD_DEFAULT), 'Staff Toko', 'staff']);
    echo "Akun awal berhasil dibuat.<br>Admin: admin / password<br>Staff: staff / password<br><a href='login.php'>Masuk ke aplikasi</a>";
} else {
    echo "Akun sudah tersedia. <a href='login.php'>Masuk ke aplikasi</a>";
}
