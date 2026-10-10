<?php
require 'config.php';
if (logged_in()) { header('Location: dashboard.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare("SELECT id,username,password_hash,full_name,role FROM users WHERE username=?");
    $stmt->execute([$username]); $u = $stmt->fetch();
    if ($u && password_verify($password, $u['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id'=>$u['id'],'username'=>$u['username'],'full_name'=>$u['full_name'],'role'=>$u['role']];
        header('Location: dashboard.php'); exit;
    } else $error = 'Username atau password salah.';
}
$page_title='Login'; require 'partials/header.php';
?>
<div class="login-wrap card">
  <div class="login-logo">♡ Ca Fashion</div><p class="sub" style="text-align:center">Kelola toko bajumu dengan manis dan mudah.</p>
  <?php if ($error): ?><p class="error"><?= e($error) ?></p><?php endif; ?>
  <form method="post">
    <label>Username</label><input name="username" required autofocus>
    <label>Password</label><input type="password" name="password" required>
    <button class="btn" style="width:100%;margin-top:18px">Masuk</button>
  </form>
  <p class="small">Login pertama kali? Buka <a href="setup.php">setup.php</a> untuk membuat akun awal.</p>
</div>
<?php require 'partials/footer.php'; ?>
