<?php
require 'config.php'; require_login();
$page_title='Dashboard';
$totalProducts=(int)$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalStock=(int)$pdo->query("SELECT COALESCE(SUM(stock),0) FROM products")->fCa FashionetchColumn();
$inventoryValue=(float)$pdo->query("SELECT COALESCE(SUM(stock*price),0) FROM products")->fetchColumn();
$lowStock=(int)$pdo->query("SELECT COUNT(*) FROM products WHERE stock <= minimum_stock")->fetchColumn();
$recent=$pdo->query("SELECT sm.*,p.name AS product_name,u.full_name FROM stock_movements sm JOIN products p ON p.id=sm.product_id JOIN users u ON u.id=sm.user_id ORDER BY sm.created_at DESC LIMIT 6")->fetchAll();
$low=$pdo->query("SELECT name,sku,stock,minimum_stock FROM products WHERE stock <= minimum_stock ORDER BY stock ASC LIMIT 6")->fetchAll();
require 'partials/header.php';
?>
<div class="page-head"><div><h1>Halo, <?= e($_SESSION['user']['full_name']) ?>! ♡</h1><div class="sub">Ringkasan toko baju  hari ini.</div></div><a class="btn" href="products.php">＋ Tambah / Lihat Produk</a></div>
<div class="stats">
 <div class="stat"><div class="label">Total Produk</div><div class="number"><?= $totalProducts ?></div><div class="small">jenis produk terdaftar</div></div>
 <div class="stat"><div class="label">Total Stok</div><div class="number"><?= $totalStock ?></div><div class="small">potong barang tersedia</div></div>
 <div class="stat"><div class="label">Nilai Inventory</div><div class="number" style="font-size:22px">Rp <?= number_format($inventoryValue,0,',','.') ?></div><div class="small">stok × harga jual</div></div>
 <div class="stat"><div class="label">Stok Menipis</div><div class="number"><?= $lowStock ?></div><div class="small">produk perlu diperiksa</div></div>
</div>
<div class="card" style="margin-top:22px"><div class="page-head"><div><h2>♡ Produk Stok Menipis</h2><div class="sub">Produk dengan stok sama dengan atau di bawah batas minimum.</div></div><a href="products.php">Lihat semua →</a></div>
<div class="table-wrap"><table><thead><tr><th>SKU</th><th>Produk</th><th>Stok</th><th>Minimum</th></tr></thead><tbody>
<?php foreach($low as $p): ?><tr><td><?=e($p['sku'])?></td><td><?=e($p['name'])?></td><td><span class="badge badge-low"><?=e($p['stock'])?></span></td><td><?=e($p['minimum_stock'])?></td></tr><?php endforeach; ?>
<?php if(!$low): ?><tr><td colspan="4" class="empty">Semua stok aman. Yeay! ♡</td></tr><?php endif; ?>
</tbody></table></div></div>
<div class="card"><h2>Aktivitas Stok Terbaru</h2><div class="table-wrap"><table><thead><tr><th>Waktu</th><th>Produk</th><th>Tipe</th><th>Jumlah</th><th>Petugas</th></tr></thead><tbody>
<?php foreach($recent as $r): ?><tr><td><?=e($r['created_at'])?></td><td><?=e($r['product_name'])?></td><td><span class="badge"><?= $r['type']==='IN'?'Barang masuk':'Barang keluar' ?></span></td><td><?=e($r['quantity'])?></td><td><?=e($r['full_name'])?></td></tr><?php endforeach; ?>
<?php if(!$recent): ?><tr><td colspan="5" class="empty">Belum ada transaksi stok.</td></tr><?php endif; ?>
</tbody></table></div></div>
<?php require 'partials/footer.php'; ?>
