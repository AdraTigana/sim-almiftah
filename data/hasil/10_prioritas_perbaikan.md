# 10 — Prioritas Perbaikan

Urutan eksekusi berdasarkan **dampak / effort**.

---

## Fase 1 — Security 🔴

| # | Item | File | Estimasi |
|---|------|------|----------|
| 1 | Tambah `esc()` di semua view | 20+ file | 2-3 jam |
| 2 | Enable CSRF global | `Config/Filters.php` | 30 menit + testing |
| 3 | Replace raw SQL → Query Builder | `WaliKelas/Rekapitulasi.php`, `Cetak.php`, `Rapor.php` | 1 jam |
| 4 | Ganti encryption key ke env-specific | `.env` + regenerate | 10 menit |
| 5 | Matikan DBDebug untuk production | `Config/Database.php` | 5 menit |
| 6 | Tambah rate limiting login | `Auth.php` | 20 menit |

## Fase 2 — Critical Bug Fix 🔴

| # | Item | File | Estimasi |
|---|------|------|----------|
| 7 | Implementasi `safeBlock`/`safeUnblock` | `layouts/app.php` | 30 menit |
| 8 | Fix variable name `$jilidModel` | `Admin/Dashboard.php` | 5 menit |

## Fase 3 — Extract Duplikasi 🟠

| # | Item | File | Estimasi |
|---|------|------|----------|
| 9 | Buat `PredikatHelper.php` | Baru | 30 menit |
| 10 | Buat `TimeHelper.php` | Baru | 20 menit |
| 11 | Extract jilid display name | 5 file | 30 menit |
| 12 | Fix magic number mapel ID | `Guru/Nilai.php` | 15 menit |

## Fase 4 — Error Handling & Transaction 🟠

| # | Item | File | Estimasi |
|---|------|------|----------|
| 13 | Tambah DB transaction | `Presensi.php`, `Nilai.php`, `Admin/Guru.php` | 45 menit |
| 14 | Cek return value delete | `Admin/*.php` | 20 menit |
| 15 | Tambah input validation | Semua POST endpoint | 1 jam |

## Fase 5 — Refactor God Functions 🟠

| # | Item | File | Estimasi |
|---|------|------|----------|
| 16 | Break down `Nilai::siswa()` | `Guru/Nilai.php` | 1 jam |
| 17 | Break down `Nilai::save()` | `Guru/Nilai.php` | 1 jam |
| 18 | Ekstrak helper dari `Cetak::excel()` | `WaliKelas/Cetak.php` | 30 menit |
| 19 | Ekstrak helper dari `RaporExcel::generate()` | `Helpers/RaporExcel.php` | 30 menit |

## Fase 6 — UI/UX 🟡

| # | Item | File | Estimasi |
|---|------|------|----------|
| 20 | Tambah `for` attribute di label | Semua form | 1 jam |
| 21 | Touch target icon button | 3 file admin | 15 menit |
| 22 | Touch target attendance radio | `guru/presensi.php` | 10 menit |
| 23 | Modal ARIA roles | Semua modal | 30 menit |
| 24 | Tab ARIA roles | 2 file | 20 menit |
| 25 | Mobile sidebar backdrop | `layouts/topbar.php` | 15 menit |
| 26 | Bottom nav walas + rekap | `layouts/bottomnav.php` | 10 menit |
| 27 | Admin sidebar + profil | `layouts/sidebar_admin.php` | 10 menit |
| 28 | Export Report → Ekspor Laporan | `admin/dashboard.php` | 5 menit |
| 29 | Tambah `aria-current` di breadcrumb | `components/_breadcrumb.php` | 10 menit |
| 30 | Search input `aria-label` | 3 file | 10 menit |

## Fase 7 — Dead Code Cleanup 🟡

| # | Item | File | Estimasi |
|---|------|------|----------|
| 31 | Hapus `Kelompok.php` | `Controllers/Guru/` | 5 menit |
| 32 | Hapus `welcome_message.php` | `Views/` | 5 menit |
| 33 | Hapus komentar DB config | `Config/Database.php` | 10 menit |
| 34 | Hapus `text-headline-md-mobile` | `guru/dashboard.php` | 5 menit |
| 35 | Kurikulum delete → AJAX | `admin/kurikulum.php` | 20 menit |

## Fase 8 — Testing 🟢

| # | Item | Estimasi |
|---|------|----------|
| 36 | Setup PHPUnit + CIUnitTestCase | 30 menit |
| 37 | Test Auth (login, logout, role) | 1 jam |
| 38 | Test Presensi (saveBatch, validasi) | 1 jam |
| 39 | Test Nilai (save, saveAkhir, predikat) | 2 jam |
| 40 | Test Admin CRUD (santri, guru, rombel) | 2 jam |
| 41 | Test Helper (predikat, timeAgo) | 30 menit |

---

## Timeline Estimasi

| Fase | Item | Total Estimasi |
|------|------|---------------|
| Fase 1 | Security | 4-5 jam |
| Fase 2 | Critical bug fix | 35 menit |
| Fase 3 | Ekstrak duplikasi | 1.5 jam |
| Fase 4 | Error handling | 2 jam |
| Fase 5 | Refactor god functions | 3 jam |
| Fase 6 | UI/UX | 3 jam |
| Fase 7 | Dead code cleanup | 45 menit |
| Fase 8 | Testing | 7 jam |
| **Total** | | **~20-22 jam** |
