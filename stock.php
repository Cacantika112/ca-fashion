<?php
require 'config.php'; require_login(); $page_title='Transaksi Stok'; $error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 $product=(int)($_POST['product_id']??0);$type=$_POST['type']??'';$qty=filter_var($_POST['quantity']??null,FILTER_VALIDATE_INT);$note=trim($_POST['note']??'');
 if(!$product||!in_array($type,['IN','OUT'],true)||$qty===false||$qty<1)$error='Pilih produk dan masukkan jumlah minimal 1.';
 else{
  try{$pdo->beginTransaction();$s=$pdo->prepare("SELECT stock FROM products WHERE id=? FOR UPDATE");$s->execute([$product]);$p=$s->fetch();if(!$p)throw new Exception('Produk tidak ditemukan.');
   if($type==='OUT'&&$qty>(int)$p['stock'])throw new Exception('Stok tidak cukup. Barang keluar tidak boleh melebihi stok.');
   $delta=$type==='IN'?$qty:-$qty;$s=$pdo->prepare("UPDATE products SET stock=stock+? WHERE id=?");$s->execute([$delta,$product]);
   $s=$pdo->prepare("INSERT INTO stock_movements(product_id,user_id,type,quantity,note) VALUES(?,?,?,?,?)");$s->execute([$product,$_SESSION['user']['id'],$type,$qty,$note]);$pdo->commit();flash('Transaksi stok berhasil disimpan.');header('Location: stock.php');exit;
  }catch(Throwable $ex){if($pdo->inTransaction())$pdo->rollBack();$error=$ex->getMessage();}
 }
}
$products=$pdo->query("SELECT id,sku,name,stock FROM products ORDER BY name")->fetchAll();
$rows=$pdo->query("SELECT sm.*,p.name product_name,p.sku,u.full_name FROM stock_movements sm JOIN products p ON p.id=sm.product_id JOIN users u ON u.id=sm.user_id ORDER BY sm.created_at DESC LIMIT 100")->fetchAll();
require 'partials/header.php';?>
<div class="page-head"><div><h1>Transaksi Stok ♡</h1><div class="sub">Catat barang masuk dan barang keluar. Histori tersimpan otomatis.</div></div></div>
<?php if($error):?><p class="error"><?=e($error)?></p><?php endif;?>
<div class="card"><h2>Catat Transaksi</h2><form method="post" class="form-grid"><div><label>Produk</label><select name="product_id" required><option value="">Pilih produk</option><?php foreach($products as $p):?><option value="<?=$p['id']?>"><?=e($p['sku'].' — '.$p['name'].' (stok: '.$p['stock'].')')?></option><?php endforeach;?></select></div><div><label>Jenis transaksi</label><select name="type"><option value="IN">Barang masuk (+)</option><option value="OUT">Barang keluar (−)</option></select></div><div><label>Jumlah</label><input type="number" name="quantity" min="1" required></div><div><label>Catatan</label><input name="note" placeholder="Contoh: restock / penjualan"></div><div style="align-self:end"><button class="btn">Simpan Transaksi</button></div></form></div>
<div class="card"><h2>Histori Transaksi</h2><div class="table-wrap"><table><thead><tr><th>Waktu</th><th>SKU</th><th>Produk</th><th>Tipe</th><th>Jumlah</th><th>Catatan</th><th>Petugas</th></tr></thead><tbody><?php foreach($rows as $r):?><tr><td><?=e($r['created_at'])?></td><td><?=e($r['sku'])?></td><td><?=e($r['product_name'])?></td><td><span class="badge"><?=$r['type']==='IN'?'Masuk':'Keluar'?></span></td><td><?=$r['quantity']?></td><td><?=e($r['note'])?></td><td><?=e($r['full_name'])?></td></tr><?php endforeach;?><?php if(!$rows):?><tr><td colspan="7" class="empty">Belum ada transaksi.</td></tr><?php endif;?></tbody></table></div></div>
<?php require 'partials/footer.php';?>
