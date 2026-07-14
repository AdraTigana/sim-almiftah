# 05 — Refactor Kode

Pendekatan: **Minimal Change, Maximum Impact.** Setiap refactor harus berdampak nyata tanpa mengubah arsitektur besar.

---

## Tahap 1: Ekstrak Duplikasi — PredikatHelper

### 6× duplikasi → 1 helper

**File baru:** `app/Helpers/PredikatHelper.php`

```php
<?php
// Helper konversi nilai angka ke predikat huruf (A-E)
// Threshold: A>=85, B>=70, C>=55, D>=40, E<40

function predikatAngkaKeHuruf($nilai): string {
    if ($nilai === null || $nilai === '—') return '—';
    $n = (int)$nilai;
    if ($n >= 85) return 'A';
    if ($n >= 70) return 'B';
    if ($n >= 55) return 'C';
    if ($n >= 40) return 'D';
    return 'E';
}
```

**File yang diubah:**
| File | Method/Fungsi | Perubahan |
|------|--------------|-----------|
| `Guru/Nilai.php:689-696` | `_calculatePredikat()` | Panggil `predikatAngkaKeHuruf()` |
| `Guru/Dashboard.php:124-133` | `_predikatLabel()` | Panggil helper |
| `Guru/RekapKelas.php:214-222` | `_predikat()` | Panggil helper |
| `WaliKelas/Dashboard.php:125-131` | `_predikatLabel()` | Ganti threshold ke 85/70/55/40 (sama dengan yg lain) |
| `WaliKelas/Cetak.php:119-127` | `$predikatFn` | Panggil helper |
| `Helpers/RaporExcel.php:213-219` | `angkaKeHuruf()` | Panggil helper |

**Dampak:** 50+ baris kode dihapus, threshold konsisten di 1 tempat.

---

## Tahap 2: Ekstrak Duplikasi — TimeHelper

### 3× duplikasi → 1 helper

**File baru:** `app/Helpers/TimeHelper.php`

```php
<?php
// Helper format waktu relatif (Indonesia)

function timeAgo($datetime): string {
    if (!$datetime) return '';
    $diff = time() - strtotime($datetime);
    if ($diff < 60) return 'baru saja';
    if ($diff < 3600) return floor($diff / 60) . ' menit lalu';
    if ($diff < 86400) return floor($diff / 3600) . ' jam lalu';
    return date('d M', strtotime($datetime));
}
```

**File yang diubah:**
| File | Perubahan |
|------|-----------|
| `Admin/Dashboard.php:103-111` | Hapus method, panggil `timeAgo()` |
| `Guru/Dashboard.php:136-144` | Hapus method, panggil `timeAgo()` |
| `WaliKelas/Dashboard.php:134-142` | Hapus method, panggil `timeAgo()` |

---

## Tahap 3: Ekstrak Duplikasi — Jilid Display Name

### 5× duplikasi → 1 helper/fungsi

**Opsi:** Tambah method static di `JilidModel` atau helper function.

```php
// Fungsi untuk dapat display name jilid berdasarkan urutan dan isTasmi
function jilidDisplayName(int $urutan, string $jilidNama, bool $isTasmi): string {
    if (!$isTasmi) {
        return match ($urutan) {
            1 => 'Nilai Harian',
            2 => 'Nilai Tugas',
            default => $urutan >= 3 ? 'Nilai Ujian' : $jilidNama,
        };
    }
    return $urutan >= 3 ? 'Nilai Ujian' : $jilidNama;
}
```

**File yang diubah:**
| File | Baris | Perubahan |
|------|-------|-----------|
| `Guru/Nilai.php:107-118` | 2 blok | Panggil helper |
| `Guru/Nilai.php:285-295` | 2 blok | Panggil helper |
| `Guru/Dashboard.php:92-104` | 1 blok | Panggil helper |
| `Guru/RekapKelas.php:122-135` | 1 blok | Panggil helper |
| `WaliKelas/Dashboard.php:75-87` | 1 blok | Panggil helper |

---

## Tahap 4: Ekstrak Magic Number Mapel ID

### `Guru/Nilai.php` — hardcoded ID 1 dan 9

**Solusi:** Gunakan kolom `mapel.kode` atau tambah kolom `is_tasmi` di tabel mapel. Alternatif minimal: definisikan konstanta di controller atau model.

```php
// Di Nilai.php atau Model
const MAPEL_TASMI_IDS = [1, 9];

function isTasmi(int $mapelId): bool {
    return in_array($mapelId, self::MAPEL_TASMI_IDS);
}
```

**Dampak:** Minimal, hanya pindahkan magic number ke konstanta.

---

## Tahap 5: Break Down God Functions (Progresif)

### `Nilai::siswa()` — 160 baris

```php
public function siswa($siswaId, $mapelId, $rombelId) {
    $data = $this->_loadSiswaData($siswaId, $mapelId, $rombelId);
    return $this->render('guru/nilai_siswa', $data);
}

private function _loadSiswaData(...): array {
    // ... 160 baris logika dipindah ke sini
}
```

**Split `Nilai.php` (697 baris) — hanya jika perlu:**

| File Baru | Isi |
|-----------|-----|
| `Guru/NilaiInput.php` | `siswa()`, `mapel()`, `index()` |
| `Guru/NilaiAkhir.php` | `saveAkhir()` |
| `Guru/NilaiAjax.php` | `getSiswa()`, `getJilid()`, `getKriteria()`, `getDetailJilid()`, `formJilid()` |
| `Guru/NilaiSync.php` | `syncBatch()`, `save()` |

**Prioritas:** RENDAH — hanya lakukan jika ada waktu. Controller 697 baris masih functional.

---

## Tahap 6: Nama Variable Menyesatkan

### `Admin/Dashboard.php`

| Baris | Sebelum | Sesudah |
|-------|---------|---------|
| 32 | `$aktivitasModel = new ProgresSantriModel()` | `$progresModel = new ProgresSantriModel()` |
| 72 | `$jilidModel = new ProgresSantriModel()` | `$progresModel2 = new ProgresSantriModel()` |

Perubahan 2 baris, dampak besar pada readability.
