# 06 — Optimasi Sistem

---

## 1. Database Transaction pada Delete+Insert

### Masalah
`Presensi::saveBatch()`, `Nilai::save()`, `Admin::Guru::update()` melakukan delete lalu insert tanpa transaction. Jika insert gagal, data hilang.

### Solusi
Bungkus dengan transaction CI4:

```php
$db = \Config\Database::connect();
$db->transStart();

$model->where(...)->delete();
$model->insertBatch($batch);

$db->transComplete();

if ($db->transStatus() === false) {
    return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan.']);
}
```

**File terdampak:**
- `app/Controllers/Guru/Presensi.php:saveBatch()`
- `app/Controllers/Guru/Nilai.php:save()` dan `saveAkhir()`
- `app/Controllers/Admin/Guru.php:update()` dan `assign()`
- `app/Controllers/Admin/Rombel.php:addSiswa()` dan `removeSiswa()`

---

## 2. Replace Raw SQL dengan Query Builder

### Masalah
3 file di walas menggunakan `$db->query("SELECT ... WHERE id IN ($list)")` dengan string concatenation.

### Solusi
Ganti dengan Query Builder bawaan CI4:

```php
// SEBELUM
$siswaIdList = implode(',', array_map('intval', $siswaIds));
$db->query("SELECT ... WHERE ps.siswa_id IN ($siswaIdList) ...");

// SESUDAH
$builder = $db->table('progres_santri ps');
$builder->select('...')
    ->whereIn('ps.siswa_id', $siswaIds)
    ->where('ps.rombel_id', $rombelId)
    ->groupBy('m.nama');
```

**File terdampak:**
- `WaliKelas/Rekapitulasi.php:47-57`
- `WaliKelas/Cetak.php:81-92, 101-113`
- `WaliKelas/Rapor.php:66-78, 86-98`

---

## 3. CSRF Protection

### Masalah
Semua filter keamanan dikomentari di Filters.php.

### Solusi
1. Aktifkan CSRF filter global
2. Test setiap POST endpoint untuk memastikan token dikirim
3. Untuk AJAX endpoints, kirim CSRF token via header `X-CSRF-TOKEN`

**Di `Config/Filters.php`:**
```php
public array $globals = [
    'before' => [
        'csrf',
    ],
];
```

**Di layout `app.php` — tambahkan meta tag:**
```html
<meta name="csrf-token" content="<?= csrf_hash() ?>">
```

**Di fetch request — kirim header:**
```javascript
headers: {
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
}
```

---

## 4. XSS Prevention — esc() di Views

### Masalah
20+ file view output user data tanpa `esc()`.

### Solusi
Systematic audit: cari `<?= $` di semua file view dan ganti pola yang output user data:

```php
// SEBELUM
<?= $siswa['nama'] ?>

// SESUDAH  
<?= esc($siswa['nama']) ?>
```

**Prioritas:** View yang paling sering diakses publik.

---

## 5. Rate Limiting Login

### Masalah
Tidak ada batasan percobaan login.

### Solusi Minimal
Session-based counter di `Auth.php`:

```php
$attempts = session()->get('login_attempts') ?? 0;
if ($attempts >= 5) {
    $lockout = session()->get('login_lockout');
    if ($lockout && time() - $lockout < 300) {
        return redirect()->back()->with('error', 'Terlalu banyak percobaan. Coba 5 menit lagi.');
    }
    session()->remove('login_attempts');
    session()->remove('login_lockout');
}
// ... setelah login gagal
session()->set('login_attempts', $attempts + 1);
session()->set('login_lockout', time());
```

Perubahan minimal (< 15 baris), dampak keamanan tinggi.

---

## 6. Matikan DBDebug untuk Production

### Masalah
`Config/Database.php:36` — `'DBDebug' => true` bocor detail error.

### Solusi
Pindahkan ke .env:

```
# .env
database.default.DBDebug = false
```

Atau di config:

```php
'DBDebug' => ENVIRONMENT === 'development' ? true : false,
```

---

## 7. Encryption Key

### Masalah
Hardcoded di .env.

### Solusi
Hapus dari .env yang di-version-control. Tambah ke .env.example dengan placeholder:

```
# .env.example
encryption.key = hex2bin:XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
```

Generated key per deployment via CLI:
```
php spark key:generate
```

---

## 8. Error Handling — Silent Failures

### Masalah
Delete operations di admin tidak cek return value.

### Solusi
```php
// SEBELUM
$model->delete($id);
return redirect()->back()->with('message', 'Berhasil dihapus.');

// SESUDAH
if ($model->delete($id)) {
    return redirect()->back()->with('message', 'Berhasil dihapus.');
}
return redirect()->back()->with('error', 'Gagal menghapus.');
```

**File terdampak:**
- `Admin/Rombel.php:116`
- `Admin/TahunAjar.php:39`
- `Admin/Kurikulum.php:46, 65, 83`
- `Admin/Santri.php:52`
