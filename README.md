# Sistem Pengumpulan Tugas Praktikum

Aplikasi web untuk pengumpulan tugas praktikum mahasiswa dan pengelolaannya oleh admin lab. Mahasiswa mengisi form, mengunggah berkas, dan berkas tersimpan rapi di Google Drive. Admin mengatur mata kuliah, modul, dan kelas, lalu menilai, mengarsipkan, dan mengekspor rekap pengumpulan.

Dibangun dengan Laravel 12 + MySQL + Google Drive API.

## Fitur

**Halaman publik (mahasiswa)**
- Form pengumpulan: nama, NIM, mata kuliah, modul (dinamis per matkul), kelas, dan berkas (maks 20 MB)
- Berkas tersimpan di Google Drive dengan struktur folder `Mata Kuliah / Modul / Kelas`
- Penamaan berkas otomatis dan konsisten
- Modul yang sudah lewat tenggat otomatis ditolak, modul yang diarsipkan tidak tampil di form
- Dwibahasa Indonesia dan Inggris

**Panel admin**
- Dashboard dengan langkah awal, ringkasan, dan pengingat tugas yang belum dinilai
- Kelola mata kuliah, modul (dengan tenggat dan gambar), dan kelas lab
- Daftar pengumpulan dengan pencarian, filter (matkul, modul, kelas, status nilai, status tampil), dan pengurutan
- Penilaian dan umpan balik per pengumpulan
- Arsip untuk menyembunyikan data dari form mahasiswa tanpa menghapusnya
- Aksi massal: nilai, arsipkan, tampilkan, hapus, dan ekspor CSV (semua atau yang dipilih)
- Kelola API key untuk integrasi pihak ketiga

**API v1**
- Autentikasi memakai API key lewat header `X-API-KEY`
- CRUD mata kuliah, modul, dan kelas
- Baca data pengumpulan beserta nilai, ubah nilai, arsipkan, dan hapus
- Dokumentasi lengkap tersedia di `API.md`

## Kebutuhan sistem

- PHP 8.2 atau lebih baru
- Composer
- MySQL
- Node.js dan npm (untuk membangun aset frontend)
- Akun Google dengan akses Drive API (Client ID, Client Secret, Refresh Token, dan ID folder tujuan)

## Instalasi

```bash
git clone <url-repo>
cd tugas_lab

composer install
cp .env.example .env
php artisan key:generate

npm install
npm run build

php artisan migrate --force
```

Buat akun admin pertama lewat tinker atau seeder yang tersedia, lalu jalankan server pengembangan:

```bash
php artisan serve
```

Buka `http://localhost:8000` untuk form pengumpulan dan `http://localhost:8000/login` untuk panel admin.

## Konfigurasi penting di `.env`

| Kunci | Keterangan |
| --- | --- |
| `DB_*` | Koneksi database MySQL |
| `GOOGLE_DRIVE_CLIENT_ID`, `GOOGLE_DRIVE_CLIENT_SECRET`, `GOOGLE_DRIVE_REFRESH_TOKEN` | Kredensial Drive API |
| `GOOGLE_DRIVE_FOLDER_ID` | Folder root tempat semua berkas disimpan |
| `SESSION_DRIVER`, `CACHE_STORE`, `QUEUE_CONNECTION` | Disarankan `database` untuk instalasi standar |

## Alur kerja admin baru

1. Tambah mata kuliah di menu Mata Kuliah
2. Tambah modul beserta tenggat di menu Modul Praktikum
3. Tambah kelas di menu Kelas Lab
4. Bagikan tautan form (`/`) ke praktikan
5. Nilai pengumpulan yang masuk, arsipkan yang sudah selesai, ekspor CSV bila perlu rekap

## Pengujian

```bash
php artisan test
```

Tes mencakup autentikasi API key, CRUD API, penilaian, arsip dan visibilitas, aksi massal, serta render semua halaman admin.

## Lisensi

Proyek ini memakai lisensi [MIT](https://opensource.org/licenses/MIT).
