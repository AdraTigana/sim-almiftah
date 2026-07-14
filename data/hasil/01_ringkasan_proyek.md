# 01 — Ringkasan Proyek

## Identitas

| Atribut | Nilai |
|---------|-------|
| Nama Sistem | SIM Al-Miftah |
| Jenis | Sistem Informasi Akademik Pondok Pesantren |
| Lokasi | MTI Canduang, Kab. Agam |
| Basis | Manajemen Nilai + Presensi + Rapor Santri |

## Technical Stack

| Lapisan | Teknologi |
|---------|-----------|
| Backend | PHP 8.x, CodeIgniter 4.7.3 |
| Database | MySQL (via MySQLi driver) |
| Frontend | Tailwind CSS (CDN), Google Material Symbols, SweetAlert2 |
| Excel | PhpSpreadsheet (via Composer) |
| PWA | Service Worker, IndexedDB, Manifest JSON |
| Auth | Session-based + Remember Me cookie |

## Struktur Role

```
Admin  ──→ Manajemen master data (santri, guru, rombel, mapel, jilid, dll)
Guru   ──→ Input nilai, presensi, rekap kelas
Walas  ──→ Dashboard, rapor kelas, cetak excel, rekapitulasi
```

## Modul Utama

### Auth
Login/logout, role-based redirect, remember me token.

### Admin
CRUD Santri, Guru, Rombel, Mapel, Jilid, Kriteria, Tahun Ajar — plus import Excel santri.

### Guru — Input Nilai
Multi-jilid per mapel, kriteria per input type (angka, checkbox, pilihan), offline sync via IndexedDB + SW.

### Guru — Presensi
Entry per tanggal+mapel, batch save via AJAX, rekap matriks per rombel.

### Wali Kelas — Rapor
Rapor per-kelas (accordion), detail nilai per mapel, predikat A-E, cetak Excel, rekapitulasi.

### PWA
Offline support: service worker cache + IndexedDB pending sync queue.

## Basis Data

12 tabel inti + 7 tabel relasi/penunjang cakupan: auth, akademik, penilaian, presensi, rapor.

## Arsitektur

Monolith server-rendered dengan AJAX partials untuk interaktivitas. Tidak ada SPA framework. View menggunakan layout inheritance CI4.
