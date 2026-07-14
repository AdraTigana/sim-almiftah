# 07 — UI/UX Review

---

## Ringkasan Temuan

19 kategori, ~140 temuan. Berikut prioritas berdasarkan **dampak ke pengguna** dan **effort perbaikan**.

---

## 🔴 Prioritas 1 — Critical (Dampak Tinggi, Effort Rendah)

### 1.1 `safeBlock`/`safeUnblock` Tidak Berfungsi
- **File:** `app/Views/layouts/app.php:205-208`
- **Masalah:** Fungsi empty stub — tidak ada loading state saat operasi AJAX
- **Dampak:** User klik tombol simpan/hapus → tidak ada feedback visual → bisa klik berulang
- **Solusi:** Implementasi overlay loading spinner

```javascript
function safeBlock(selector) {
    const el = document.querySelector(selector);
    if (!el) return;
    el.style.position = 'relative';
    const overlay = document.createElement('div');
    overlay.className = 'loading-overlay';
    overlay.innerHTML = '<div class="spinner"></div>';
    el.appendChild(overlay);
}
function safeUnblock(selector) {
    const el = document.querySelector(selector);
    if (!el) return;
    const overlay = el.querySelector('.loading-overlay');
    if (overlay) overlay.remove();
}
```

### 1.2 Label Form Tidak Ada `for` Attribute
- **File:** Semua form di aplikasi
- **Dampak:** Screen reader tidak bisa asosiasikan label dengan input
- **Solusi:** Tambah `for` attribute — perubahan 1 atribut per label, tidak ubah struktur

### 1.3 Touch Target Icon Button < 44px
- **File:** `admin/santri.php`, `admin/rombel.php`, `admin/tahun_ajar.php`
- **Dampak:** Sulit ditekan di mobile
- **Solusi:** Tambah `min-h-[44px] min-w-[44px]` — tambah 2 class

### 1.4 Attendance Radio Touch Target Terlalu Kecil
- **File:** `guru/presensi.php:68-95`
- **Dampak:** Label Hadir/Sakit/Izin/Alpha susah ditekan di mobile
- **Solusi:** Tambah `py-2` (dari `py-1.5`) → ukuran naik dari ~22px ke ~28px

---

## 🟠 Prioritas 2 — High (Dampak Sedang, Effort Rendah)

### 2.1 Mobile Sidebar Tanpa Backdrop
- **File:** `layouts/topbar.php:3`, `layouts/app.php`
- **Masalah:** Sidebar muncul di atas konten tanpa backdrop gelap
- **Solusi:** Tambah `div.backdrop` saat sidebar dibuka

### 2.2 Modal Tidak Ada ARIA Roles
- **File:** Semua modal di admin/guru
- **Solusi:** Tambah `role="dialog" aria-modal="true" aria-labelledby="modalTitle"`

### 2.3 Tab Component Tidak Ada ARIA Roles
- **File:** `guru/nilai_siswa.php:40-54`, `admin/kurikulum.php:14-33`
- **Solusi:** Tambah `role="tab"`, `role="tabpanel"`, `aria-selected`, `aria-controls`

### 2.4 Walas Bottom Nav Tidak Ada "Rekapitulasi"
- **File:** `layouts/bottomnav.php:43-54`
- **Solusi:** Tambah 1 item navigasi (icon: `assessment`)

### 2.5 Admin Sidebar Tidak Ada Link Profil
- **File:** `layouts/sidebar_admin.php`
- **Solusi:** Tambah link Profil (sama seperti sidebar guru/walas)

### 2.6 Export Report Button Bahasa Inggris
- **File:** `admin/dashboard.php:28`
- **Solusi:** Ganti "Export Report" → "Ekspor Laporan"

### 2.7 Table Search Input Tidak Ada Label
- **File:** `admin/dashboard.php:110,128`, `admin/santri.php:38,41,46`
- **Solusi:** Tambah `aria-label="Cari ..."`

### 2.8 Breadcrumb Item Terakhir Tidak Ada `aria-current`
- **File:** `components/_breadcrumb.php`
- **Solusi:** Tambah `aria-current="page"` pada item terakhir

---

## 🟡 Prioritas 3 — Medium (Dampak Rendah, Effort Rendah)

### 3.1 `text-headline-md-mobile` Tidak Terdefinisi
- **File:** `guru/dashboard.php:12`
- **Solusi:** Hapus class atau tambah di config Tailwind

### 3.2 Login Page Tailwind Config Duplicate
- **File:** `auth/login.php:14-16`
- **Masalah:** Memiliki config Tailwind berbeda dari app.php
- **Solusi:** Extract warna ke file terpisah, atau sync manual

### 3.3 Kurikulum Delete Pakai Full Page Reload
- **File:** `admin/kurikulum.php:73, 140, 246`
- **Solusi:** Konsistenkan dengan fetch() + safeNotify()

### 3.4 Error Pages Tidak Pakai Layout App
- **File:** `views/errors/html/error_*.php`
- **Solusi:** Integrasikan dengan layout app

### 3.5 Welcome Page Default CI4
- **File:** `views/welcome_message.php`
- **Solusi:** Hapus (sudah di 04_file_yang_dihapus.md)

---

## 🟢 Prioritas 4 — Low (Nice to Have)

### 4.1 Hamburger Button Tidak Ada `aria-label`
- **File:** `layouts/topbar.php:3`

### 4.2 Profile Dropdown Tidak Ada ARIA
- **File:** `layouts/topbar.php:18, 25-55`

### 4.3 Online/Offline Status Tidak Ada `role="status"`
- **File:** `layouts/topbar.php:7-8`

### 4.4 Body Tag Tidak Ada `role` atau `aria-label`
- **File:** `layouts/app.php:167`

### 4.5 Sidebar Tidak Ada `aria-label`
- **File:** Semua sidebar

### 4.6 Table Tidak Ada `<caption>`
- **File:** Semua table

### 4.7 Tabs `nilai_siswa.php` — Tab Content Loading State
- Sudah ada spinner "Memuat..." — good
- Tapi tab switching tidak disable tab lain saat loading

### 4.8 Website Title Hardcoded di Layout
- **File:** `layouts/app.php:7` — "Al-Miftah MIS" tidak bisa diubah per page
- **Solusi:** Gunakan section `$this->section('title_suffix')` atau config

---

## Perubahan Prioritas (Berdasarkan Dampak/Effort)

| No | Item | Dampak | Effort | Prioritas |
|----|------|--------|--------|-----------|
| 1 | safeBlock/safeUnblock | Tinggi | Rendah (2 fungsi) | 🔴 1 |
| 2 | `for` attribute label | Tinggi | Rendah (tambah atribut) | 🔴 2 |
| 3 | Touch target icon button | Sedang | Rendah (tambah class) | 🟠 3 |
| 4 | Sidebar backdrop mobile | Sedang | Rendah | 🟠 4 |
| 5 | Modal ARIA roles | Sedang | Rendah | 🟠 5 |
| 6 | Bottom nav walas | Rendah | Rendah | 🟡 6 |
| 7 | Admin sidebar profil | Rendah | Rendah | 🟡 7 |
| 8 | Export Report label | Rendah | Sangat Rendah | 🟡 8 |
| 9 | Tab ARIA roles | Rendah | Rendah | 🟡 9 |
| 10 | Welcome page | Rendah | Sangat Rendah | 🟢 10 |
