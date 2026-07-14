# 09 — Daftar Komentar

Komentar akan ditambahkan ke file (1-2 baris per method utama). Bahasa Indonesia.

---

## Controllers

### `app/Controllers/Auth.php`
```php
// Validasi login dan redirect berdasarkan role
// Mengelola remember me token
```

### `app/Controllers/BaseController.php`
```php
// Mengatur render view dan data global (session, role, notifikasi)
```

### `app/Controllers/Admin/Dashboard.php`
```php
// Statistik dashboard admin
// Aktivitas terbaru progres santri
```

### `app/Controllers/Admin/Guru.php`
```php
// CRUD data guru dan assign mapel
```

### `app/Controllers/Admin/Santri.php`
```php
// CRUD data santri + import dari Excel
```

### `app/Controllers/Admin/Rombel.php`
```php
// CRUD rombel dan atur anggota
```

### `app/Controllers/Admin/Kurikulum.php`
```php
// Kelola mapel, jilid, dan kriteria penilaian
```

### `app/Controllers/Admin/TahunAjar.php`
```php
// Kelola tahun ajaran dan status aktif
```

### `app/Controllers/Guru/Nilai.php`
```php
// Input nilai santri per mapel+rombel
// Menyimpan progres multi-jilid dan kriteria
// Sinkronisasi offline via IndexedDB
```

### `app/Controllers/Guru/Presensi.php`
```php
// Menyimpan presensi batch per mapel+tanggal
```

### `app/Controllers/Guru/PresensiRekap.php`
```php
// Matriks rekap kehadiran siswa per tanggal
```

### `app/Controllers/Guru/Dashboard.php`
```php
// Ringkasan progres dan aktivitas guru
```

### `app/Controllers/Guru/InputSaya.php`
```php
// Daftar mapel+rombel yang diampu guru
```

### `app/Controllers/Guru/RekapKelas.php`
```php
// Rekap nilai per kelas untuk satu mapel
```

### `app/Controllers/Guru/Profil.php`
```php
// Edit profil dan password guru
```

### `app/Controllers/WaliKelas/Dashboard.php`
```php
// Statistik dashboard wali kelas
```

### `app/Controllers/WaliKelas/Rapor.php`
```php
// Tampilan rapor per kelas dengan detail nilai
```

### `app/Controllers/WaliKelas/Cetak.php`
```php
// Generate rapor dalam format Excel
```

### `app/Controllers/WaliKelas/Rekapitulasi.php`
```php
// Rekap nilai per mapel untuk satu rombel
```

### `app/Controllers/WaliKelas/Profil.php`
```php
// Edit profil dan password wali kelas
```

---

## Filters

### `app/Filters/RoleFilter.php`
```php
// Mengautentikasi pengguna berdasarkan role
// Mengembalikan session dari remember token jika ada
```

---

## Helpers

### `app/Helpers/auth_helper.php`
```php
// Helper untuk pengecekan role dan session
```

### `app/Helpers/PredikatHelper.php` (baru)
```php
// Konversi nilai angka ke predikat huruf (A-E)
```

### `app/Helpers/TimeHelper.php` (baru)
```php
// Format waktu relatif dalam Bahasa Indonesia
```

---

## Models (jika perlu)

### `app/Models/UserModel.php`
```php
// Validasi data user termasuk password
```

### `app/Models/PresensiModel.php`
```php
// CRUD presensi per siswa+mapel+tanggal
```

### `app/Models/ProgresSantriModel.php`
```php
// Menyimpan nilai progres santri per kriteria
```

---

## Views

Tidak perlu komentar di view — nama file dan konteks sudah cukup jelas.
