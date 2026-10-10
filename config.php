<?php
session_start();
$host = 'localhost';
$db   = 'toko_baju_pink';
$user = 'root';
$pass = '';
$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    exit('Koneksi database gagal. Pastikan MySQL aktif dan schema.sql sudah diimpor.');
}
function e($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function logged_in() { return isset($_SESSION['user']); }
function require_login() {
    if (!logged_in()) { header('Location: login.php'); exit; }
}
function require_admin() {
    require_login();
    if ($_SESSION['user']['role'] !== 'admin') { http_response_code(403); exit('Akses ditolak. Khusus admin.'); }
}
function flash($message) { $_SESSION['flash'] = $message; }
function show_flash() {
    if (!empty($_SESSION['flash'])) {
        echo '<div class="flash">'.e($_SESSION['flash']).'</div>';
        unset($_SESSION['flash']);
    }
}
