📊 Sistem Penilaian Kinerja Karyawan
Selamat datang di Sistem Penilaian Kinerja Karyawan! Sebuah aplikasi web komprehensif yang dirancang untuk membantu perusahaan dalam mengelola data karyawan, absensi, penilaian kinerja, serta bukti dan validasi penjualan dengan struktur peran yang fleksibel.

✨ Fitur Utama
Manajemen Pengguna & Karyawan Terpisah:

Pemisahan jelas antara akun pengguna (untuk login dan peran sistem) dan data spesifik karyawan (detail HR).

Login menggunakan NIP.

Manajemen karyawan (tambah, edit, hapus) dengan otorisasi berbasis peran yang granular.

Penetapan shift kerja per karyawan untuk jadwal yang fleksibel.

Sistem Absensi Canggih:

Check-in dan Check-out harian berdasarkan jadwal shift karyawan.

Penentuan hari kerja/libur otomatis berdasarkan shift.

Laporan absensi harian dengan status (Hadir, Absen, Libur, Belum Bekerja, Absen Manual).

Laporan absensi bulanan komprehensif dengan rekap total jam kerja, lembur, hari hadir/absen/libur.

Filter laporan absensi berdasarkan bulan, tahun, dan karyawan tertentu.

Ekspor Laporan Absensi Bulanan ke PDF.

Modul Penilaian Kinerja:

Kriteria penilaian yang terdefinisi dengan jelas (Produktivitas, Kerjasama Tim, Inisiatif, dll.).

Proses penilaian kinerja karyawan oleh atasan yang berwenang.

Melihat daftar dan detail riwayat penilaian.

Manajemen Bukti & Validasi Penjualan:

Administrator dapat mengunggah bukti penjualan atas nama karyawan mana pun.

Manager dapat memvalidasi bukti penjualan yang berstatus 'pending'.

Melihat riwayat validasi untuk setiap bukti penjualan.

Laporan penjualan yang sudah tervalidasi.

Sistem Peran & Otorisasi Fleksibel:

Owner: Akses penuh (dashboard, kelola karyawan, penilaian, laporan absensi, lihat bukti penjualan, laporan penjualan). Dapat menambah/mengedit peran Admin & Manager.

Admin: Akses luas (dashboard, kelola karyawan, penilaian, laporan absensi, lihat bukti penjualan, laporan penjualan, pengaturan sistem). Dapat menambah/mengedit peran Manager, Administrator, Karyawan.

Manager: Akses manajemen (dashboard, kelola karyawan, penilaian, laporan absensi, validasi penjualan, laporan penjualan). Dapat menambah/mengedit peran Administrator & Karyawan.

Administrator: Akses terbatas (dashboard absensi pribadi, kelola penuh bukti penjualan).

Karyawan: Akses pribadi (dashboard absensi pribadi, ganti password).

🚀 Teknologi yang Digunakan
Backend Framework: Laravel 10.x

Bahasa Pemrograman: PHP 8.2+

Database: MySQL

Frontend Framework: Tailwind CSS

JavaScript: Alpine.js (untuk interaktivitas ringan)

PDF Generation: Barryvdh/Laravel-Dompdf

📦 Instalasi
Ikuti langkah-langkah di bawah ini untuk menginstal dan menjalankan proyek di lingkungan lokal Anda.

Clone Repositori:

git clone https://github.com/your-username/your-repo-name.git
cd your-repo-name

(Ganti your-username/your-repo-name.git dengan URL repositori Anda)

Instal Dependensi Composer:

composer install

Salin File .env:

cp .env.example .env

Konfigurasi .env:
Buka file .env dan sesuaikan pengaturan database Anda:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=penilaian_karyawan # Sesuaikan dengan nama database Anda
DB_USERNAME=root # Sesuaikan dengan username database Anda
DB_PASSWORD= # Sesuaikan dengan password database Anda

# Pastikan ini diatur ke 'database' untuk sesi

SESSION_DRIVER=database

Generate Kunci Aplikasi:

php artisan key:generate

Jalankan Migrasi Database & Seeder:
Ini akan membuat semua tabel database dan mengisi data awal (pengguna owner, admin, manager, shift, kriteria penilaian).

php artisan migrate:fresh --seed

(Peringatan: Perintah ini akan menghapus semua data yang ada di database Anda.)

Buat Symbolic Link untuk Storage:

php artisan storage:link

Jalankan Server Pengembangan Laravel:

php artisan serve

Akses Aplikasi:
Buka browser Anda dan kunjungi http://127.0.0.1:8000 atau http://localhost:8000.

🔑 Penggunaan
Setelah instalasi, Anda dapat login menggunakan kredensial default yang dibuat oleh seeder:

Peran

NIP

Email

Password

Dashboard Awal

Owner

OWN001

owner@example.com

password

/dashboard

Admin

ADM001

admin@example.com

password

/dashboard

Manager

MGR001

manager@example.com

password

/dashboard

Administrator

ADM001 (jika dibuat)

administrator@example.com

password

/absensi/dashboard

Karyawan

(NIP Karyawan yang Anda buat)

(Email Karyawan yang Anda buat)

password

/absensi/dashboard

(Catatan: Jika Anda membuat akun Administrator melalui seeder, NIP-nya mungkin ADM001 atau Anda bisa menyesuaikannya di AdministratorUserSeeder jika Anda membuatnya.)

Struktur Peran & Hak Akses:
Owner:

Dashboard: Umum

Kelola Karyawan: Tambah/Edit/Hapus (Admin, Manager)

Penilaian: Penuh

Absensi: Laporan Harian/Bulanan, Ekspor

Bukti Penjualan: Lihat

Laporan Penjualan: Lihat

Admin:

Dashboard: Umum

Kelola Karyawan: Tambah/Edit/Hapus (Manager, Administrator, Karyawan)

Penilaian: Penuh

Absensi: Laporan Harian/Bulanan, Ekspor, Pengaturan Sistem

Bukti Penjualan: Lihat

Laporan Penjualan: Lihat

Manager:

Dashboard: Umum

Kelola Karyawan: Tambah/Edit (Administrator, Karyawan)

Penilaian: Penuh

Absensi: Laporan Harian/Bulanan, Ekspor

Validasi Penjualan: Penuh

Laporan Penjualan: Lihat

Administrator:

Dashboard: Absensi Pribadi

Absensi: Check-in/Check-out

Bukti Penjualan: Penuh (Unggah, Lihat, Edit, Hapus - semua bukti)

Karyawan:

Dashboard: Absensi Pribadi

Absensi: Check-in/Check-out

Profil: Kelola Profil Pribadi, Ganti Password

🤝 Kontribusi
Jika Anda ingin berkontribusi pada proyek ini, silakan fork repositori dan buat Pull Request dengan fitur atau perbaikan Anda.

📄 Lisensi
Proyek ini dilisensikan di bawah Lisensi MIT. Lihat file LICENSE untuk detail lebih lanjut.
(Anda perlu membuat file https://www.google.com/search?q=LICENSE di root proyek Anda jika belum ada)

Catatan: Pastikan semua komponen Blade kustom seperti <x-stat-card>, <x-quick-action>, <x-admin-button>, dan ikon Heroicons (x-heroicon-o-...) telah terdefinisi dengan benar di proyek Anda. Jika belum, Anda mungkin perlu membuatnya atau menghapusnya dari dashboard.blade.php.
