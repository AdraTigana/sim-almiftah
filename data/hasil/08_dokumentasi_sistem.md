# 08 — Dokumentasi Sistem

Dokumentasi tingkat tinggi. Gunakan sebagai peta navigasi untuk memahami gambaran besar proyek.

---

## Auth
- **Login** — Validasi kredensial, session start, redirect berdasarkan role
- **Logout** — Hapus session + remember me cookie
- **Remember Me** — Token acak disimpan di cookie + database
- **Role Filter** — Middleware per-group route (admin/guru/walas)

## Admin — Manajemen Master Data
- **CRUD Santri** — Tambah/edit/hapus/import Excel santri
- **CRUD Guru** — Tambah/edit/hapus guru + assign mapel
- **CRUD Rombel** — Tambah/edit/hapus rombel + atur anggota
- **CRUD Mapel** — Kelola mata pelajaran
- **CRUD Jilid** — Kelola jilid per mapel
- **CRUD Kriteria** — Kelola kriteria penilaian per jilid
- **CRUD Tahun Ajar** — Kelola tahun ajaran aktif
- **Kurikulum** — Tab management mapel + jilid + kriteria
- **Dashboard** — Statistik santri, guru, nilai, aktivitas terbaru

## Guru — Penilaian
- **Dashboard** — Ringkasan progres + statistik hari ini
- **Kelas Saya** — Daftar mapel+rombel yang diampu
- **Input Nilai** — Per-siswa multi-jilid, kriteria per tipe input
- **Nilai Akhir** — Mode auto/manual per jilid
- **Offline Sync** — IndexedDB + SW untuk input tanpa koneksi
- **Rekap Nilai** — Ringkasan nilai per kelas

## Guru — Presensi
- **Entry Presensi** — Per tanggal+mapel, batch status H/S/I/A
- **Simpan AJAX** — Fetch POST, stay on page, toast notifikasi
- **Rekap Kehadiran** — Matriks siswa × tanggal dengan tooltip keterangan

## Wali Kelas — Rapor
- **Dashboard** — Statistik kelas, progres terakhir
- **Rapor Kelas** — Accordion per siswa, detail nilai per mapel
- **Predikat** — Konversi nilai angka ke huruf (A-E)
- **Cetak Excel** — Generate rapor .xlsx via PhpSpreadsheet
- **Rekapitulasi** — Rekap nilai per mapel untuk satu rombel

## PWA
- **Service Worker** — Cache static assets + offline fallback
- **IndexedDB** — Antrian pending sync untuk data offline
- **Manifest** — Aplikasi bisa di-install ke home screen

## Komponen UI
- **Breadcrumb** — Navigasi hirarki halaman
- **Sidebar** — Navigasi utama (responsive: hidden on mobile)
- **Bottom Nav** — Navigasi mobile (fixed bottom)
- **Topbar** — Status online, user info, profil dropdown
- **Glass Card** — Wrapper konten dengan shadow dan rounded-3xl
- **btn-primary** — Tombol aksi utama dengan hover/active effect
- **Flashdata Toast** — Notifikasi via SweetAlert2 toast

## Routing
- `/` — Redirect ke login
- `/auth/*` — Login, logout
- `/admin/*` — Admin panel
- `/guru/*` — Guru panel
- `/walas/*` — Wali Kelas panel
- Filter per group via `RoleFilter`
