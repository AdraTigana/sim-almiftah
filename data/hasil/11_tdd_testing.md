# 11 — TDD / Testing

---

## Framework

Proyek menggunakan **PHPUnit** (bawaan CodeIgniter 4). Tidak ada framework testing tambahan yang terdeteksi.

**Setup:**
```bash
php spark make:command TestSetup
```

Konfigurasi testing sudah ada di `app/Config/Database.php` (bagian `$tests`), tapi perlu diaktifkan.

---

## Prioritas Test

### 🔴 KRITIS — Coverage 90-100%

| Modul | Skenario | Notes |
|-------|----------|-------|
| **Auth** | Login berhasil/gagal, logout, redirect sesuai role, remember me token | Critical path |
| **RoleFilter** | Session valid, expired, remember me restore, wrong role redirect | Security gate |
| **Presensi** | `saveBatch()` validasi status, delete+insert, mapel filter, invalid status rejected | Data integrity |
| **Nilai** | `save()` validasi input, `saveAkhir()` auto/manual, `_upsertKriteria()` insert/update | Core business |
| **Nilai Sync** | `syncBatch()` insert batch, duplicate handling, response format | Offline data |

### 🟠 TINGGI — Coverage 80-85%

| Modul | Skenario |
|-------|----------|
| **Admin Santri** | CRUD validasi, import Excel duplicate NIS |
| **Admin Guru** | CRUD validasi, assign/unassign mapel, password hash |
| **Admin Rombel** | CRUD validasi, addSiswa/removeSiswa duplicate check |
| **Admin Kurikulum** | CRUD mapel, jilid, kriteria — cascade delete |
| **Rapor** | Perhitungan predikat konsisten, rerata kelas, presensi summary |
| **Cetak Excel** | Generate file, struktur sheet, data presensi |

### 🟡 SEDANG — Coverage 70-85%

| Modul | Skenario |
|-------|----------|
| **PredikatHelper** | Semua threshold (≥85, ≥70, ≥55, ≥40, <40), null input, string input |
| **TimeHelper** | Berbagai diff value (detik, menit, jam, hari), null input |
| **JilidDisplayName** | Mapping urutan 1/2/3+, isTasmi true/false |
| **Dashboard Stats** | Query aggregasi, empty data, edge case no siswa |
| **ProgresSantriModel** | Validation rules, insert/update |

### 🟢 RENDAH — Coverage opsional

| Modul | Skenario |
|-------|----------|
| **Views** | Ensure no PHP errors, variable exists (smoke test) |
| **Profile Update** | Password change, old password verification |
| **TahunAjar** | CRUD, set active |

---

## Strategi Test per Modul

### Auth Test
```php
// 1. Login dengan kredensial benar → session terisi + redirect
// 2. Login dengan password salah → error + tetap di login
// 3. Login dengan username tidak ada → error
// 4. Logout → session cleared + redirect ke login
// 5. Remember Me → cookie terisi, session restore
// 6. Akses /admin tanpa login → redirect ke login
// 7. Akses /guru sebagai admin → 403/redirect
```

### Presensi Test
```php
// 1. saveBatch dengan status valid → insert sukses
// 2. saveBatch dengan status invalid "xyz" → reject
// 3. saveBatch tanpa data → return error
// 4. saveBatch overwrite → data lama terhapus, data baru masuk
// 5. Response JSON format correct
```

### Nilai Test
```php
// 1. save() dengan valid data → insert/update
// 2. save() tanpa kriteria → skip
// 3. saveAkhir() mode auto → rata-rata dari progres
// 4. saveAkhir() mode manual → simpan input user
// 5. _upsertKriteria() existing → update
// 6. _upsertKriteria() new → insert
// 7. Predikat threshold → 85=A, 70=B, 55=C, 40=D, <40=E
```

### PredikatHelper Test
```php
// 1. Nilai 100 → 'A'
// 2. Nilai 85 → 'A'
// 3. Nilai 84 → 'B'
// 4. Nilai 70 → 'B'
// 5. Nilai 69 → 'C'
// 6. Nilai 55 → 'C'
// 7. Nilai 54 → 'D'
// 8. Nilai 40 → 'D'
// 9. Nilai 39 → 'E'
// 10. Nilai 0 → 'E'
// 11. Null → '—'
// 12. '—' string → '—'
```

---

## Siklus TDD untuk Refactor

Setiap refactor besar harus melalui:

```
RED   → Buat test untuk behavior yang ada (sebelum refactor)
GREEN → Jalankan test → pastikan FAIL (karena belum ada implementasi)
       → Lakukan refactor → test PASS
REFACTOR → Bersihkan kode, jalankan test lagi
```

### Refactor 1: PredikatHelper
1. Tulis test untuk predikat dengan threshold saat ini (85/70/55/40)
2. Ekstrak helper
3. Ganti semua 6 implementasi dengan panggilan helper
4. Test ulang

### Refactor 2: TimeHelper
1. Tulis test untuk `timeAgo()` dengan berbagai input
2. Ekstrak helper
3. Ganti 3 implementasi
4. Test ulang

### Refactor 3: God Function Nilai::siswa()
1. Tulis integration test yang panggil endpoint dengan sample data
2. Ekstrak logika ke method terpisah
3. Test ulang — pastikan output identik

---

## Target Coverage

| Komponen | Target |
|----------|--------|
| Core Business Logic (Auth, Presensi, Nilai) | 90-100% |
| Critical Systems (Role, Security) | 85-100% |
| Integration (Controller → Model → DB) | 80%+ |
| UI Components (Helper, Utility) | 70-85% |
| Views (Smoke test) | 50%+ |
| **Overall** | **75-85%** |
