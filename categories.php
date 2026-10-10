<?php
require 'config.php'; require_login(); $page_title='Kategori'; $error='';
if($_SERVER['REQUEST_METHOD']==='POST'){require_admin();$name=trim($_POST['name']??'');if($name==='')$error='Nama kategori wajib diisi.';else{try{$s=$pdo->prepare("INSERT INTO categories(name) VALUES(?)");$s->execute([$name]);flash('Kategori ditambahkan.');header('Location: categories.php');exit;}catch(PDOException $e){$error='Nama kategori sudah ada.';}}}
if(isset($_GET['delete'])){require_admin();try{$s=$pdo->prepare("DELETE FROM categories WHERE id=?");$s->execute([(int)$_GET['delete']]);flash('Kategori dihapus.');}catch(PDOException $e){flash('Kategori tidak bisa dihapus karena masih digunakan produk.');}header('Location: categories.php');exit;}
$rows=$pdo->query("SELECT c.*,COUNT(p.id) total FROM categories c LEFT JOIN products p ON p.category_id=c.id GROUP BY c.id ORDER BY c.name")->fetchAll();require 'partials/header.php';?>
<div class="page-head"><div><h1>Kategori ♡</h1><div class="sub">Kelompokkan produk agar lebih mudah dicari.</div></div></div>
<?php if($error):?><p class="error"><?=e($error)?></p><?php endif;?>
<?php if($_SESSION['user']['role']==='admin'):?><div class="card"><form method="post" class="form-grid"><div><label>Nama kategori baru</label><input name="name" required placeholder="Contoh: Kemeja"></div><div style="align-self:end"><button class="btn">＋ Tambah Kategori</button></div></form></div><?php endif;?>
<div class="card table-wrap"><table><thead><tr><th>Nama Kategori</th><th>Jumlah Produk</th><?php if($_SESSION['user']['role']==='admin'):?><th>Aksi</th><?php endif;?></tr></thead><tbody><?php foreach($rows as $r):?><tr><td><?=e($r['name'])?></td><td><?=$r['total']?></td><?php if($_SESSION['user']['role']==='admin'):?><td><a class="btn btn-danger" href="?delete=<?=$r['id']?>" onclick="return confirm('Hapus kategori?')">Hapus</a></td><?php endif;?></tr><?php endforeach;?></tbody></table></div>
<?php require 'partials/footer.php';?>
