# Product Requirements Document (PRD)

**Nama Produk:** E-Kasir Multi-Outlet (Sistem POS Terintegrasi)
**Versi Dokumen:** 1.0
**Fase:** Konsep & Inisiasi Pengembangan

## 1. Ringkasan Eksekutif

Aplikasi E-Kasir Multi-Outlet adalah Point of Sale (POS) berbasis web responsif yang dirancang untuk mempercepat proses transaksi dan manajemen inventaris bagi ekosistem bisnis multi-cabang (seperti kantin, *retail*, atau *franchise*). Mengusung konsep antarmuka ala *mobile banking* untuk operasional harian di lapangan, aplikasi ini memanfaatkan kamera *smartphone* sebagai *scanner* cerdas untuk input produk dan transaksi. Selain itu, terdapat *dashboard* desktop yang komprehensif untuk pengelola pusat (*owner*/admin).

## 2. Spesifikasi Teknologi (Tech Stack)

* **Backend Framework:** Laravel 13
* **Bahasa Pemrograman:** PHP 8.4
* **Database:** SQLite (Dikonfigurasi untuk mendukung konkurensi ringan multi-tenant)
* **Frontend/UI:** Blade Templating, Tailwind CSS, Alpine.js (TALL Stack) atau integrasi PWA.
* **Library Barcode/Kamera:** ZXing-js / Html5-Qrcode untuk pembacaan *barcode* via browser.
* **Aset Visual:** Ilustrasi 3D modern, *Iconography* 3D (misalnya: *glassmorphism* 3D icons untuk menu).

## 3. Panduan Antarmuka & Pengalaman Pengguna (UI/UX)

* **Tampilan Mobile (Mobile-First POS):**
* Mengadopsi tata letak aplikasi *mobile banking* (misal: Livin/BCA Mobile).
* *Header* menampilkan nama pengguna, nama *outlet*, dan sisa dana operasional (kas).
* Menu disajikan dalam bentuk *grid* berisikan ikon 3D yang interaktif (Menu: Kasir, Tambah Barang, Kas Harian, Riwayat Transaksi).
* Navigasi bawah (*Bottom Navigation Bar*) untuk akses cepat.


* **Tampilan Desktop (Management Dashboard):**
* Tata letak *sidebar* kiri untuk navigasi dan area kerja luas di kanan.
* Visualisasi data dengan grafik interaktif untuk penjualan, pergerakan stok, dan arus kas per *outlet*.
* Tabel data besar (*datatable*) untuk manajemen produk dan laporan lengkap.



## 4. Kebutuhan Fungsional Utama (Key Features)

### A. Manajemen Produk via Mobile (Smart Input)

Memungkinkan staf menambahkan produk baru hanya dengan menggunakan *smartphone* tanpa perlu mengetik panjang.

* **Kamera & Barcode:** Membuka kamera *smartphone* langsung di *browser* untuk memindai *barcode* produk.
* **Pengambilan Gambar:** Mengambil foto produk fisik secara langsung.
* **Formulir Ringkas:** Setelah *scan*, otomatis memunculkan *form* untuk mengisi:
* Nama Barang (Bisa diintegrasikan dengan OCR jika memungkinkan).
* Harga Pokok Penjualan (HPP).
* Harga Jual.
* Stok Awal.



### B. Transaksi Kasir Mobile (Scan & Go)

Proses *checkout* yang didesain untuk kecepatan dan akurasi di layar sentuh.

* **Barcode Scanner Terintegrasi:** Tombol melayang (*floating button*) bergambar *scanner* 3D untuk langsung memindai barang bawaan pembeli. Barang otomatis masuk ke keranjang.
* **Pencarian Manual:** Alternatif mencari produk lewat nama jika *barcode* rusak.
* **Pembayaran:** Input jumlah uang tunai pelanggan (dengan tombol sugesti nominal cepat seperti Rp50.000, Rp100.000) dan kalkulasi kembalian otomatis.

### C. Manajemen Dana Operasional Harian (Arus Kas)

Fitur krusial untuk memantau laci kas secara *real-time*, sangat cocok untuk skema kantin atau toko kelontong.

* **Buka Kas (Open Register):** Input modal awal di laci kas saat pergantian *shift* atau awal hari.
* **Kas Keluar/Masuk (Petty Cash):** Mencatat pengeluaran mendadak (contoh: beli es batu, bayar galon) dari laci kas.
* **Tutup Kas (Close Register):** Rekapitulasi otomatis (Modal Awal + Total Penjualan Tunai - Pengeluaran). Menampilkan selisih jika uang fisik di laci tidak sesuai dengan sistem.

### D. Sistem Multi-Outlet / Multi-Tenant

* **Pemisahan Data:** Setiap entitas toko/kantin memiliki data stok dan kas yang terisolasi.
* **Peran Pengguna (Role Base Access):**
* *Superadmin (Owner):* Melihat dan mengelola seluruh cabang lewat *dashboard* desktop.
* *Manager Outlet:* Mengelola laporan dan inventaris di satu cabang spesifik.
* *Kasir:* Akses terbatas pada layar transaksi *mobile* dan manajemen kas harian.



## 5. Arsitektur Database (Gambaran Singkat SQLite)

Karena menggunakan SQLite, struktur perlu dioptimalkan agar tidak terjadi *database lock*.

* `tenants` (ID, Nama Outlet, Alamat)
* `users` (ID, Tenant_ID, Nama, Role, PIN/Password)
* `products` (ID, Tenant_ID, Nama, Barcode, Foto, HPP, Harga_Jual, Stok)
* `transactions` (ID, Tenant_ID, User_ID, Total, Modal_Awal, Shift)
* `transaction_details` (ID, Transaction_ID, Product_ID, Qty, Harga_Satuan, Subtotal)
* `cash_flows` (ID, Tenant_ID, Tipe [In/Out], Nominal, Keterangan, Waktu)

## 6. Target Penyelesaian & Timeline (Fase Pengembangan)

1. **Minggu 1:** *Setup* repositori Laravel 13 dan PHP 8.4, konfigurasi otentikasi multi-tenant dengan SQLite.
2. **Minggu 2:** Pengembangan *Frontend Mobile* (UI *Mobile Banking*, integrasi *scanner* HTML5).
3. **Minggu 3:** Pengembangan *Backend* (Logika transaksi, CRUD Produk, Kalkulasi Kas Harian).
4. **Minggu 4:** Pembuatan *Dashboard* Desktop untuk Superadmin & *Testing* Menyeluruh.