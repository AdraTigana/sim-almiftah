# 04 — File yang Dihapus

Daftar file yang di-rekomendasikan untuk dihapus.

---

## 1. `app/Controllers/Guru/Kelompok.php`

| Atribut | Nilai |
|---------|-------|
| **Alasan** | Tidak memiliki route, tidak dipanggil dari mana pun |
| **Risiko** | Aman — dead code murni |
| **Verifikasi** | `grep -r "Kelompok" app/Config/Routes.php` → tidak ditemukan |
| **Catatan** | Controller ini tidak direferensikan di routes, view, atau controller lain |

**Aman dihapus.**

---

## 2. `app/Views/welcome_message.php`

| Atribut | Nilai |
|---------|-------|
| **Alasan** | Default CodeIgniter 4 welcome page. Tidak digunakan dalam aplikasi |
| **Risiko** | Aman — tidak ada yang me-render view ini |
| **Verifikasi** | Controller tidak pernah `return view('welcome_message')` |
| **Catatan** | Base URL langsung redirect ke login via route `'/'` di Routes.php |

**Aman dihapus.**

---

## 3. `app/Config/Database.php` (baris 54-158)

| Atribut | Nilai |
|---------|-------|
| **Alasan** | ~100 baris konfigurasi database yang dikomentari (SQLite3, Postgre, SQLSRV, OCI8) |
| **Risiko** | Aman — hanya komentar |
| **Verifikasi** | Tidak digunakan, hanya komentar `//` |
| **Catatan** | Sebaiknya dihapus agar file lebih bersih dan mudah dibaca |

**Aman dihapus.**

---

## 4. File Lain yang Perlu Verifikasi Manual

| File | Alasan | Status |
|------|--------|--------|
| `app/Controllers/Guru/Nilai.php:382-388` | Komentar duplikasi block delete | Bisa dibersihkan saat refactor |
| `app/Views/errors/html/debug.css` | Dipakai oleh error pages? | Perlu verifikasi manual |
| `public/assets/js/db.js` | IndexedDB helper — DIPAKAI oleh SW | **JANGAN HAPUS** |
| `public/assets/icons/` | Icon PWA — DIPAKAI oleh manifest.json | **JANGAN HAPUS** |

---

## Ringkasan

| Yang Dihapus | Ukuran | Risiko |
|-------------|--------|--------|
| `Controllers/Guru/Kelompok.php` | ~70 baris | Aman |
| `Views/welcome_message.php` | ~330 baris | Aman |
| `Config/Database.php:54-158` | ~100 baris | Aman |
| **Total** | **~500 baris** | **Aman semua** |
