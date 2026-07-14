# 02 — Masalah Ditemukan

Format: `[PRIORITAS] [KATEGORI] Judul — File:line`

---

## 🔴 KEAMANAN — KRITIS

### 🔴 SQL Injection — Raw Query dengan Concatenation
- `app/Controllers/WaliKelas/Rekapitulasi.php:47-57` — `IN ($siswaIdList)` concatenation
- `app/Controllers/WaliKelas/Cetak.php:81-92, 101-113` — raw query
- `app/Controllers/WaliKelas/Rapor.php:66-78, 86-98` — raw query

**Dampak:** Data bisa bocor atau korup jika input tidak divalidasi sempurna.
**Solusi:** Ganti semua raw query ke Query Builder + parameterized binding.

---

### 🔴 Encryption Key Hardcoded
- `.env:54` — `hex2bin:b1fa849069fd0e1a2a0d1da572f501525d1a1c09bb24a47aeebe2d2de12d66a7`

**Dampak:** Semua instalasi pakai key yang sama — session bisa didekrip jika .env bocor.
**Solusi:** Pindahkan ke environment variable terpisah, regenerate per deployment.

---

### 🔴 CSRF Protection Dinonaktifkan Global
- `app/Config/Filters.php:74-84` — Semua filter keamanan (CSRF, Honeypot) dikomentari

**Dampak:** Semua POST endpoint rentan Cross-Site Request Forgery.
**Solusi:** Aktifkan CSRF filter global, verifikasi setiap POST endpoint tetap jalan.

---

### 🔴 XSS Sistemik — Output Tanpa Escaping
- 20+ file view menggunakan `<?= $variable ?>` tanpa `esc()` untuk data dari user/database

**File terdampak (sebagian):**
- `app/Views/admin/dashboard.php` - nama santri, aktivitas
- `app/Views/admin/santri.php` - nama, NIS
- `app/Views/admin/guru.php` - nama guru
- `app/Views/admin/rombel.php` - nama rombel
- `app/Views/guru/nilai_mapel.php` - nama siswa, NIS
- `app/Views/guru/dashboard.php` - nama siswa, mapel
- `app/Views/guru/presensi_rekap.php` - nama siswa, keterangan
- `app/Views/guru/nilai_siswa.php` - nama siswa, mapel, rombel
- `app/Views/walas/dashboard.php` - data progres
- `app/Views/walas/rekapitulasi.php` - nama mapel
- `app/Views/walas/rapor_kelas.php` - nama siswa, NIS
- `app/Views/walas/cetak.php` - nama, NIS

**Dampak:** Eksekusi script berbahaya di browser pengguna.
**Solusi:** Bungkus setiap output user data dengan `esc()` di semua view.

---

### 🔴 safeBlock/safeUnblock adalah Empty Stubs
- `app/Views/layouts/app.php:205-208`

**Dampak:** Semua panggilan `safeBlock('#modal')` di admin/guru/walas TIDAK menampilkan loading state. User tidak dapat feedback visual saat operasi AJAX.
**Solusi:** Implementasi `safeBlock` untuk menampilkan overlay loading, `safeUnblock` untuk menghilangkannya.

---

## 🟠 TINGGI

### 🟠 Inconsistensi Predikat Threshold (6× Duplikasi)
- `Guru/Nilai.php:691-695` — 85/70/55/40
- `Guru/Dashboard.php:126-133` — 85/70/55/40
- `Guru/RekapKelas.php:216-221` — 85/70/55/40
- `WaliKelas/Dashboard.php:127-131` — **90/80/70** (BERBEDA!)
- `WaliKelas/Cetak.php:121-127` — 85/70/55/40
- `Helpers/RaporExcel.php:215-219` — 85/70/55/40

**Dampak:** Dashboard walas pakai threshold berbeda dari sistem lainnya.
**Solusi:** Ekstrak ke `PredikatHelper.php`.

---

### 🟠 Duplikasi `_timeAgo` (3×)
- `Admin/Dashboard.php:103-111` — "m lalu"
- `Guru/Dashboard.php:136-144` — "menit lalu"
- `WaliKelas/Dashboard.php:134-142` — "menit lalu"

**Dampak:** Inconsistensi teks, maintenance burden.
**Solusi:** Ekstrak ke `TimeHelper.php`.

---

### 🟠 Duplikasi Jilid Display Name Mapping (5×)
- `Guru/Nilai.php:107-118` — `$map = [1 => 'Nilai Harian', ...]`
- `Guru/Nilai.php:285-295` — duplikasi lagi di method sama
- `Guru/Dashboard.php:92-104`
- `Guru/RekapKelas.php:122-135`
- `WaliKelas/Dashboard.php:75-87`

**Solusi:** Ekstrak ke method/model atau helper.

---

### 🟠 Magic Number Mapel ID (1 dan 9 untuk Tasmi')
- `Guru/Nilai.php:107, 111, 285-286`

**Dampak:** Asumsi database ID tertentu — jika data id berubah, logic rusak.
**Solusi:** Gunakan kode mapel atau kolom penanda, bukan hardcoded ID.

---

### 🟠 God Function: `Nilai::siswa()` — 160 Baris
- `app/Controllers/Guru/Nilai.php:92-251`

**Dampak:** Sulit dibaca, di-test, dan dipelihara. Menangani display + load data.
**Solusi:** Ekstrak logika load data ke method terpisah.

---

### 🟠 God Function: `Nilai::save()` — 100+ Baris
- `app/Controllers/Guru/Nilai.php:347-449`

**Dampak:** Validasi, insert, delete, response handling dalam satu fungsi.
**Solusi:** Ekstrak validasi dan insert ke method terpisah.

---

### 🟠 God Function: `Nilai::saveAkhir()` — 100+ Baris
- `app/Controllers/Guru/Nilai.php:451-570`

---

### 🟠 God Function: `Cetak::excel()` — 131 Baris
- `app/Controllers/WaliKelas/Cetak.php:51-181`

---

### 🟠 God Function: `RaporExcel::generate()` — 184 Baris
- `app/Helpers/RaporExcel.php:27-211`

---

### 🟠 Nama Variable Menyesatkan (BUG)
- `Admin/Dashboard.php:32` — `$aktivitasModel = new ProgresSantriModel()`
- `Admin/Dashboard.php:72` — `$jilidModel = new ProgresSantriModel()`

**Dampak:** Developer lain bingung. `$jilidModel` meng-shadow dan sebenarnya bukan model jilid.
**Solusi:** Ganti nama variable sesuai isi.

---

### 🟠 No Input Validation di Semua POST Endpoint
- `Admin/Guru::store()` — tidak validasi
- `Admin/Santri::store()` — tidak validasi
- `Admin/Rombel::create()` — tidak validasi
- `Admin/Kurikulum::createMapel()` — tidak validasi
- `Admin/TahunAjar::create()` — tidak validasi
- `Guru/Presensi::saveBatch()` — tidak validasi status

**Dampak:** Data invalid bisa masuk database.
**Solusi:** Tambah `$this->validate()` di setiap POST.

---

### 🟠 No Rate Limiting di Login
- `app/Controllers/Auth.php` — tidak ada batas percobaan

**Dampak:** Brute force terbuka lebar.
**Solusi:** Tambah session-based attempt counter.

---

### 🟠 No Database Transaction pada Delete+Insert
- `Guru/Presensi::saveBatch():74` — delete dulu, insert — jika gagal, data hilang
- `Guru/Nilai::save():382-385, 420-432` — sama
- `Admin/Guru::update():112, 125-127` — sama
- `Admin/Rombel::addSiswa()` — tanpa transaction

**Dampak:** Data corruption jika proses terputus di tengah.
**Solusi:** Bungkus dengan `$this->db->transStart()` / `transComplete()`.

---

### 🟠 Hardcoded Informasi Yayasan
- `WaliKelas/Cetak.php:165-168` — nama yayasan, madrasah, alamat
- `Helpers/RaporExcel.php:49-61` — duplikasi data yang sama

**Dampak:** Tidak bisa diubah tanpa edit kode.
**Solusi:** Pindahkan ke config atau database setting.

---

## 🟡 SEDANG

### 🟡 Controller `Guru\Kelompok` Tanpa Route
- `app/Controllers/Guru/Kelompok.php` — file eksis tapi tidak ada route

**Solusi:** Tambah route atau hapus file.

---

### 🟡 Dead Code: Komentar DB Config
- `app/Config/Database.php:54-158` — ~100 baris konfigurasi SQLite, Postgre, SQLSRV, OCI8 dikomentari

**Solusi:** Hapus komentar tersebut.

---

### 🟡 Dead Code: Welcome Page Default CI4
- `app/Views/welcome_message.php` — halaman default tidak dipakai

**Solusi:** Hapus atau redirect.

---

### 🟡 Tidak ada Route untuk Guru\Kelompok
**Solusi:** Tentukan apakah controller ini akan dipakai — jika tidak, hapus.

---

### 🟡 Label Form Tidak Ada `for` Attribute
- **Semua form** di aplikasi — 0 dari seluruh label menggunakan `for=""`

**Dampak:** Aksesibilitas rendah, screen reader tidak bisa navigasi.
**Solusi:** Tambah `for` attribute di setiap `<label>`.

---

### 🟡 Touch Target Icon Button Terlalu Kecil (~30px)
- `admin/santri.php` — edit/delete icon button
- `admin/rombel.php` — action buttons
- `admin/tahun_ajar.php` — edit/delete

**Standar:** Minimum 44×44px (Apple HIG, WCAG).
**Solusi:** Tambah padding atau `min-h-[44px] min-w-[44px]`.

---

### 🟡 Attendance Radio Touch Target (~22px)
- `guru/presensi.php:68-95`

**Solusi:** Tambah padding pada label.

---

### 🟡 Modal Tidak Punya `role="dialog"` atau `aria-modal`
- Semua modal di admin, guru, walas

**Dampak:** Screen reader tidak tahu ini modal.
**Solusi:** Tambah atribut ARIA.

---

### 🟡 Tab Component Tidak Punya Role Tab / Tabpanel
- `guru/nilai_siswa.php:40-54` — tab button
- `admin/kurikulum.php:14-33` — tab link

**Solusi:** Tambah `role="tab"`, `aria-selected`, `aria-controls`, `role="tabpanel"`, `aria-labelledby`.

---

### 🟡 Walas Bottom Nav Tidak Ada "Rekapitulasi"
- `layouts/bottomnav.php` — menu walas only 4 item

**Solusi:** Tambah 1 item navigasi.

---

### 🟡 Admin Sidebar Tidak Ada Link Profil
- `layouts/sidebar_admin.php` — tidak ada menu Profil

**Solusi:** Tambah link Profil.

---

### 🟡 Kurikulum Delete Pakai Full Page Reload
- `admin/kurikulum.php:73-78, 140-145, 246-251` — pakai form submit, bukan AJAX seperti CRUD lain

**Solusi:** Konsistenkan dengan fetch() + safeNotify.

---

### 🟡 Export Report Button Bahasa Inggris
- `admin/dashboard.php:28` — "Export Report" vs aplikasi Indonesia

**Solusi:** Ganti ke "Ekspor Laporan".

---

### 🟡 `text-headline-md-mobile` Tidak Terdefinisi
- `guru/dashboard.php:12` — class Tailwind tidak ada di config

**Dampak:** Class diabaikan, fallback ke ukuran default.
**Solusi:** Hapus atau definisikan di config.

---

### 🟡 Error Pages Tidak Sesuai Tema
- `views/errors/html/error_400.php`, `error_404.php`, `production.php` — standalone HTML, tidak pakai layout app

**Solusi:** Integrasikan dengan layout app.

---

## 🟢 RENDAH

### 🟢 Tidak Ada SRI Integrity pada CDN
- `layouts/app.php:7-12` — Tailwind, Google Fonts, SweetAlert2 tanpa `integrity`

**Dampak:** Jika CDN diretas, aplikasi terkompromi.
**Solusi:** Tambah SRI integrity hash.

---

### 🟢 DBDebug = true
- `Config/Database.php:36` — bocor detail error database di production

**Solusi:** Pindahkan ke .env agar bisa di-set false di production.

---

### 🟢 Campur Bahasa Inggris-Indonesia
- Nama fungsi: `index()`, `delete()` — Inggris
- Nama file view: `santri`, `guru`, `mapel` — Indonesia
- Variable: `$totalSantri`, `$tahunAktif` — mixed

---

### 🟢 Tidak Ada Structured Logging
Semua error handling hanya redirect dengan flashdata. Tidak ada log system.

---

### 🟢 UserModel Password Validation Lemah
- `min_length[6]` — tidak ada kompleksitas

---

### 🟢 Unused Model Method
- `GuruMapelModel::getGuruWithMapel()` — defined tetapi tidak dipanggil mana pun
