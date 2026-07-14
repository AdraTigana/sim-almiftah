# 03 — Struktur Folder (Proposed)

Prinsip: **Minimal Change, Maximum Impact.** Tidak ada restrukturisasi besar. Hanya tambah 2 file helper, hapus 2 file dead code.

---

## Sebelum

```
app/
├── Controllers/
│   ├── Admin/
│   │   ├── Dashboard.php
│   │   ├── Guru.php
│   │   ├── Kurikulum.php
│   │   ├── Rombel.php
│   │   ├── Santri.php
│   │   └── TahunAjar.php
│   ├── Guru/
│   │   ├── Dashboard.php
│   │   ├── InputSaya.php
│   │   ├── Kelompok.php          ← DEAD CODE (no route)
│   │   ├── Nilai.php
│   │   ├── Presensi.php
│   │   ├── PresensiRekap.php
│   │   ├── Profil.php
│   │   └── RekapKelas.php
│   ├── WaliKelas/
│   │   ├── Cetak.php
│   │   ├── Dashboard.php
│   │   ├── Profil.php
│   │   ├── Rapor.php
│   │   └── Rekapitulasi.php
│   ├── Auth.php
│   └── BaseController.php
├── Helpers/
│   └── auth_helper.php
├── Views/
│   ├── admin/          (9 file)
│   ├── guru/           (13 file)
│   ├── walas/          (5 file)
│   ├── layouts/        (6 file)
│   ├── components/     (1 file: _breadcrumb.php)
│   └── errors/         (3 file)
├── Models/             (12 file)
├── Database/
│   └── Migrations/     (21 file)
├── Config/             (14 file)
└── Filters/
    └── RoleFilter.php
```

## Sesudah

```
app/
├── Controllers/        ← TIDAK BERUBAH (hapus 1 file)
├── Helpers/
│   ├── auth_helper.php
│   ├── PredikatHelper.php      ← BARU (extract 6× duplikasi)
│   └── TimeHelper.php          ← BARU (extract 3× duplikasi)
├── Views/              ← TIDAK BERUBAH (hapus 1 file)
├── Models/             ← TIDAK BERUBAH
└── ...
```

## Ringkasan Perubahan

| Tipe | Item | Detail |
|------|------|--------|
| Hapus | `Controllers/Guru/Kelompok.php` | Dead code, tidak punya route |
| Hapus | `Views/welcome_message.php` | Default CI4, tidak dipakai |
| Tambah | `Helpers/PredikatHelper.php` | 1 fungsi untuk semua grade threshold |
| Tambah | `Helpers/TimeHelper.php` | 1 fungsi `timeAgo()` untuk 3 controller |
| Hapus | `Config/Database.php:54-158` | ~100 baris komentar DB config usang |
