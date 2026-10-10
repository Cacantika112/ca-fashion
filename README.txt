Ca Fashion — MINI PROJECT 3
Inventory Management System untuk toko baju, tema pink.

KEBUTUHAN:
- XAMPP (Apache + MySQL)
- PHP 8+ disarankan
- MySQL/MariaDB

CARA MENJALANKAN:
1. Ekstrak folder toko_baju_pink ke C:\xampp\htdocs\
2. Buka XAMPP Control Panel, jalankan Apache dan MySQL.
3. Buka http://localhost/phpmyadmin
4. Pilih tab Import, pilih file database/schema.sql, lalu klik Import/Go.
5. Buka http://localhost/toko_baju_pink/setup.php untuk membuat akun awal.
6. Buka http://localhost/toko_baju_pink/login.php

AKUN DEMO:
Admin: username admin, password password
Staff: username staff, password password

FITUR:
- Login/logout dengan password hashing dan session.
- Role admin/staff; admin mengelola master data, staff melihat produk dan mencatat transaksi.
- CRUD produk dan kategori; master supplier.
- Relasi kategori-produk (one-to-many), produk-supplier (many-to-many).
- Barang masuk/keluar dengan database transaction dan histori.
- Stok tidak boleh negatif; barang keluar tidak boleh lebih dari stok.
- Search, filter kategori, sorting, pagination.
- Dashboard: total produk, total stok, nilai inventory, stok rendah, transaksi terakhir.
- Prepared statements, validasi server-side, output escaping.

CATATAN:
- Demi keamanan, hapus setup.php setelah akun awal dibuat jika aplikasi dipasang di server publik.
- Ganti password demo setelah login untuk penggunaan nyata.
- Fitur relasi produk-supplier disiapkan dalam schema SQL; halaman supplier menyebutkan tabel relasi.
- Ini mini project pembelajaran, belum mencakup checkout/penjualan kasir.

STRUKTUR:
config.php, setup.php, login.php, dashboard.php, products.php, categories.php,
suppliers.php, stock.php, users.php, logout.php, style.css, partials/, database/
