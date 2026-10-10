<?php
require 'config.php'; require_login();
$page_title='Produk'; $error='';
if (isset($_GET['delete'])) {
    require_admin();
    try { $s=$pdo->prepare("DELETE FROM products WHERE id=?"); $s->execute([(int)$_GET['delete']]); flash('Produk berhasil dihapus.'); }
    catch(PDOException $ex){ flash('Produk tidak dapat dihapus karena sudah memiliki histori transaksi.'); }
    header('Location: products.php'); exit;
}
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $id=(int)($_POST['id']??0); $category=(int)($_POST['category_id']??0); $sku=trim($_POST['sku']??''); $name=trim($_POST['name']??'');
    $size=trim($_POST['size']??''); $color=trim($_POST['color']??''); $price=filter_var($_POST['price']??null,FILTER_VALIDATE_FLOAT);
    $stock=filter_var($_POST['stock']??null,FILTER_VALIDATE_INT); $minimum=filter_var($_POST['minimum_stock']??null,FILTER_VALIDATE_INT);
    if(!$category||$sku===''||$name===''||$price===false||$price<0||$stock===false||$stock<0||$minimum===false||$minimum<0) $error='Isi data dengan benar. Harga dan stok tidak boleh negatif.';
    else {
      try {
        if($id){ require_admin(); $s=$pdo->prepare("UPDATE products SET category_id=?,sku=?,name=?,size=?,color=?,price=?,stock=?,minimum_stock=? WHERE id=?"); $s->execute([$category,$sku,$name,$size,$color,$price,$stock,$minimum,$id]); flash('Produk diperbarui.'); }
        else { require_admin(); $s=$pdo->prepare("INSERT INTO products(category_id,sku,name,size,color,price,stock,minimum_stock) VALUES(?,?,?,?,?,?,?,?)"); $s->execute([$category,$sku,$name,$size,$color,$price,$stock,$minimum]); flash('Produk ditambahkan.'); }
        header('Location: products.php'); exit;
      } catch(PDOException $ex){ $error='SKU sudah digunakan atau data tidak valid.'; }
    }
}
$edit=null;
if(isset($_GET['edit'])) { require_admin(); $s=$pdo->prepare("SELECT * FROM products WHERE id=?"); $s->execute([(int)$_GET['edit']]); $edit=$s->fetch(); }
$categories=$pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$q=trim($_GET['q']??''); $cat=(int)($_GET['category']??0);
$sort=$_GET['sort']??'name'; $dir=strtoupper($_GET['dir']??'ASC');
$allowed=['name'=>'p.name','price'=>'p.price','stock'=>'p.stock','sku'=>'p.sku'];
if(!isset($allowed[$sort])) $sort='name'; if(!in_array($dir,['ASC','DESC'],true)) $dir='ASC';
$where=[];$params=[];
if($q!==''){ $where[]='(p.name LIKE ? OR p.sku LIKE ?)'; $params[]="%$q%";$params[]="%$q%";}
if($cat){$where[]='p.category_id=?';$params[]=$cat;}
$sqlWhere=$where?' WHERE '.implode(' AND ',$where):'';
$s=$pdo->prepare("SELECT COUNT(*) FROM products p$sqlWhere");$s->execute($params);$total=(int)$s->fetchColumn();
$perPage=8;$pages=max(1,(int)ceil($total/$perPage));$page=max(1,min($pages,(int)($_GET['page']??1)));$offset=($page-1)*$perPage;
$s=$pdo->prepare("SELECT p.*,c.name category_name FROM products p JOIN categories c ON c.id=p.category_id$sqlWhere ORDER BY {$allowed[$sort]} $dir LIMIT $perPage OFFSET $offset");$s->execute($params);$products=$s->fetchAll();
require 'partials/header.php';
?>
<div class="page-head"><div><h1>Produk ♡</h1><div class="sub">Kelola koleksi baju, harga, ukuran, warna, dan stok.</div></div></div>
<?php if($error): ?><p class="error"><?=e($error)?></p><?php endif; ?>
<?php if($_SESSION['user']['role']==='admin'): ?>
<div class="card"><h2><?= $edit?'Edit Produk':'Tambah Produk' ?></h2>
<form method="post"><input type="hidden" name="id" value="<?=e($edit['id']??0)?>"><div class="form-grid">
<div><label>Kategori</label><select name="category_id" required><?php foreach($categories as $c): ?><option value="<?=$c['id']?>" <?=($edit['category_id']??'')==$c['id']?'selected':''?>><?=e($c['name'])?></option><?php endforeach; ?></select></div>
<div><label>SKU / Kode</label><input name="sku" required value="<?=e($edit['sku']??'')?>"></div><div><label>Nama Produk</label><input name="name" required value="<?=e($edit['name']??'')?>"></div>
<div><label>Ukuran</label><input name="size" placeholder="S, M, L, XL" value="<?=e($edit['size']??'')?>"></div><div><label>Warna</label><input name="color" placeholder="Pink, putih..." value="<?=e($edit['color']??'')?>"></div>
<div><label>Harga (Rp)</label><input type="number" name="price" min="0" step="1000" required value="<?=e($edit['price']??'0')?>"></div>
<div><label>Stok awal</label><input type="number" name="stock" min="0" required value="<?=e($edit['stock']??'0')?>"></div><div><label>Minimum stok</label><input type="number" name="minimum_stock" min="0" required value="<?=e($edit['minimum_stock']??'5')?>"></div>
</div><p><button class="btn"><?= $edit?'Simpan Perubahan':'＋ Simpan Produk' ?></button> <?php if($edit): ?><a class="btn btn-light" href="products.php">Batal</a><?php endif; ?></p></form></div>
<?php endif; ?>
<div class="card"><form method="get" class="form-grid"><div><label>Cari nama / SKU</label><input name="q" value="<?=e($q)?>" placeholder="Contoh: blouse"></div><div><label>Filter kategori</label><select name="category"><option value="0">Semua kategori</option><?php foreach($categories as $c): ?><option value="<?=$c['id']?>" <?=$cat===$c['id']?'selected':''?>><?=e($c['name'])?></option><?php endforeach; ?></select></div><div><label>Urutkan</label><select name="sort"><?php foreach(['name'=>'Nama','price'=>'Harga','stock'=>'Stok','sku'=>'SKU'] as $k=>$v): ?><option value="<?=$k?>" <?=$sort===$k?'selected':''?>><?=$v?></option><?php endforeach; ?></select></div><div><label>Arah</label><select name="dir"><option <?=$dir==='ASC'?'selected':''?>>ASC</option><option <?=$dir==='DESC'?'selected':''?>>DESC</option></select></div></div><p><button class="btn">Cari / Filter</button> <a class="btn btn-light" href="products.php">Reset</a></p></form>
<div class="table-wrap"><table><thead><tr><th>SKU</th><th>Produk</th><th>Kategori</th><th>Ukuran / Warna</th><th>Harga</th><th>Stok</th><?php if($_SESSION['user']['role']==='admin'): ?><th>Aksi</th><?php endif; ?></tr></thead><tbody>
<?php foreach($products as $p): ?><tr><td><?=e($p['sku'])?></td><td><?=e($p['name'])?></td><td><?=e($p['category_name'])?></td><td><?=e($p['size'])?> / <?=e($p['color'])?></td><td>Rp <?=number_format($p['price'],0,',','.')?></td><td><span class="badge <?=$p['stock']<=$p['minimum_stock']?'badge-low':''?>"><?=e($p['stock'])?></span></td><?php if($_SESSION['user']['role']==='admin'): ?><td><div class="actions"><a class="btn btn-light" href="?edit=<?=$p['id']?>">Edit</a><a class="btn btn-danger" href="?delete=<?=$p['id']?>" onclick="return confirm('Hapus produk ini?')">Hapus</a></div></td><?php endif; ?></tr><?php endforeach; ?>
<?php if(!$products): ?><tr><td colspan="7" class="empty">Produk belum ada. Tambahkan produk pertama ya! ♡</td></tr><?php endif; ?>
</tbody></table></div>
<div class="pagination"><span class="small">Halaman <?=$page?> dari <?=$pages?> · <?=$total?> produk</span><?php if($page>1): ?><a href="?<?=http_build_query(array_merge($_GET,['page'=>$page-1]))?>">← Sebelumnya</a><?php endif; ?><?php if($page<$pages): ?><a href="?<?=http_build_query(array_merge($_GET,['page'=>$page+1]))?>">Berikutnya →</a><?php endif; ?></div></div>
<?php require 'partials/footer.php'; ?>
