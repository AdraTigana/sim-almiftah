# Product Requirements Document (PRD)
**Project Name:** Sistem Informasi Manajemen Program Al-Miftah Lil Ulum (Berbasis PWA)
**Platform:** Web-based (Progressive Web App - Mobile & Desktop)
**Tech Stack:** CodeIgniter 4 (MVC), Tailwind CSS, MySQL, JavaScript (Service Workers)
[cite_start]**Target User:** Admin, Koordinator Program, Wali Kelas, dan Guru/Ustadz [cite: 801, 669, 670]

---

## 1. Product Vision & Problem Statement
[cite_start]Pondok Pesantren MTI Canduang membutuhkan sistem digital untuk mengelola progres akademik santri pada Program Al-Miftah Lil Ulum[cite: 83, 86, 97]. 

**Masalah Utama:**
1. [cite_start]Pencatatan manual atau via *spreadsheet* lokal memicu *data siloing* (fragmentasi)[cite: 91, 93, 106].
2. [cite_start]Rekapitulasi rapor memakan waktu lama dan rentan *human error*[cite: 95, 107].
3. [cite_start]Infrastruktur jaringan internet di area pesantren sering disrupsi, membuat aplikasi web konvensional tidak reliabel untuk *input* data harian[cite: 98, 99].

**Solusi:**
[cite_start]Sebuah sistem MVC CodeIgniter 4 yang dibungkus dengan teknologi PWA *offline-first*[cite: 101]. Sistem bersifat dinamis, mampu mengakomodasi struktur kurikulum tanpa *hardcode*, dan memiliki antarmuka yang sangat responsif menggunakan Tailwind CSS.

---

## 2. Design System & UI/UX Guidelines
Sistem harus memberikan *User Experience* (UX) yang *clean*, cepat, dan intuitif, terutama bagi Ustadz yang menginput data dari layar *smartphone*.

* **Tema Utama:** Nuansa Islami Pesantren modern.
* **Warna Primer (Primary):** Hijau Emerald (`#10B981` atau `#059669`) merepresentasikan ketenangan, religiusitas, dan pertumbuhan.
* **Warna Sekunder (Secondary):** Abu-abu terang (`#F3F4F6`) untuk *background* dan Putih (`#FFFFFF`) untuk *card* agar data mudah dibaca.
* **Aksen (Accent):** Emas/Kuning kalem (`#F59E0B`) untuk *highlight* atau tombol aksi sekunder.
* **Tipografi:** 'Inter' atau 'Poppins' untuk keterbacaan tinggi pada UI, dipadukan dengan font 'Amiri' khusus untuk elemen teks Arab (jika ada).
* **Responsivitas:** *Mobile-First Design*. Semua tabel panjang harus menggunakan *horizontal scroll* atau berubah menjadi *card layout* pada layar kecil.

---

## 3. Arsitektur Teknis (MVC & PWA)

### 3.1 CodeIgniter 4 (MVC Implementation)
* [cite_start]**Model (*Fat Models*):** Seluruh *query* SQL, relasi *database* (JOIN), dan kalkulasi nilai (misal: hitung IPK atau rerata Rapor) HARUS dilakukan di dalam Model menggunakan *Query Builder* CI4[cite: 420, 429].
* [cite_start]**Controller (*Skinny Controllers*):** Hanya bertugas menerima *request*, memanggil *method* di Model, dan me-return *View* atau JSON[cite: 423, 424].
* **View:** Dibangun menggunakan HTML murni yang disuntikkan  *utility classes* Tailwind CSS. [cite_start]Jangan gunakan *file* CSS eksternal tambahan kecuali sangat terpaksa[cite: 421].

### 3.2 Progressive Web App (PWA) Environment
[cite_start]Sistem harus terdeteksi sebagai aplikasi yang dapat diinstal (Add to Home Screen)[cite: 215, 257].
* [cite_start]**Web App Manifest (`manifest.json`):** Mendefinisikan nama aplikasi, *theme_color* (Hijau Primer), dan variasi resolusi *icon*[cite: 255, 256].
* **Service Worker (`sw.js`):**
    * [cite_start]**Cache Strategy (Assets):** Gunakan *Cache-First* untuk file CSS Tailwind, ikon, gambar statis, dan file JavaScript[cite: 248].
    * [cite_start]**Cache Strategy (Data):** Gunakan *Network-First* atau *Stale-While-Revalidate* untuk mengambil data santri dan *dashboard*[cite: 250, 252].
* **Background Sync (Offline Input):** Saat Ustadz menyimpan nilai namun kondisi *offline*, data dilempar ke `IndexedDB` *browser*. [cite_start]Ketika jaringan kembali menyala, *Service Worker* otomatis melakukan *push* (sinkronisasi) data tersebut ke *controller* CI4[cite: 101, 102, 226].

---

## 4. Core Features Requirements

### 4.1 Master Data Dinamis (Academic Core)
Struktur kurikulum Al-Miftah Lil Ulum tidak boleh di-*hardcode*. [cite_start]Fitur ini dikelola oleh Admin[cite: 830, 831, 669].
* [cite_start]**Kelola Mapel:** CRUD data mata pelajaran inti (Tasmi', Nahwu, Sharaf)[cite: 833].
* **Kelola Jilid & Detail:** Sistem hirarki bertingkat. [cite_start]Admin dapat mengatur `Mapel -> Jilid -> Detail Halaman/Bab`[cite: 834, 836, 841, 843].
* [cite_start]**Kriteria Penilaian Dinamis:** Admin dapat menambah kriteria bobot nilai (Kelancaran, Hafalan, Makhraj) tanpa *alter table database*[cite: 852, 855].

### 4.2 Manajemen Pengguna & Hak Akses (RBAC)
[cite_start]Sistem menggunakan tabel `roles` dan `user_roles` untuk mendukung hak akses ganda (misal: Satu Ustadz bisa menjadi Guru sekaligus Wali Kelas)[cite: 798, 802, 804, 806].
* **Admin:** Kontrol penuh konfigurasi.
* [cite_start]**Guru/Ustadz:** Hanya dapat melihat daftar santri pada mapel dan rombel yang ditugaskan kepadanya di tabel `guru_mapel`[cite: 846, 847].
* [cite_start]**Wali Kelas:** Akses ke rekapitulasi akhir dan menu pencetakan rapor rombelnya[cite: 876, 878].

### 4.3 Input Progres Akademik (Offline-Ready)
[cite_start]Ini adalah fitur utama yang akan sering digunakan (*High Risk / Core*)[cite: 656].
* **UI:** Berupa *form wizard* atau *dropdown* beruntun (Pilih Rombel -> Pilih Siswa -> Pilih Mapel -> Pilih Jilid -> Pilih Status & Kriteria Nilai).
* [cite_start]**Validasi Frontend:** Gunakan JavaScript untuk memastikan nilai kriteria tidak melebihi skala maksimum sebelum *submit*[cite: 385].
* **Mekanisme Offline:** UI harus memberikan indikator visual jelas (misal: *badge* merah "Offline Mode") jika koneksi putus. Tombol submit berubah menjadi "Simpan di Perangkat".

### 4.4 Sistem Pelaporan & Analitik (Dashboard)
* [cite_start]**Kalkulasi Otomatis:** Sistem merangkum `progres_nilai` menjadi `rapor_detail` secara otomatis di akhir periode[cite: 866, 879].
* [cite_start]**Visualisasi (Khusus Pimpinan/Koordinator):** *Dashboard* menggunakan *library chart* (seperti Chart.js atau ApexCharts) untuk memantau grafik kelulusan per jilid secara *real-time*[cite: 656, 670].

---

## 5. Database Schema Adjustments & Validations

[cite_start]Struktur dari `Master db.docx` sudah sangat memadai[cite: 795]. Namun, **wajib** dilakukan satu penambahan krusial untuk mendukung PWA:

[cite_start]**Tabel: `progres_santri`** [cite: 861]
Tambahkan kolom berikut untuk kapabilitas *Background Sync*:
* `local_id VARCHAR(50) NULL` (Diisi UUID dari sisi *client/browser* saat tersimpan *offline*).
* `sync_status ENUM('synced', 'pending') DEFAULT 'synced'` (Penanda apakah rekaman sudah masuk ke MySQL server, atau masih draf sinkronisasi).

*(Catatan untuk Developer: Saat Service Worker me-load data sinkronisasi ke Controller CI4, lakukan pengecekan `WHERE local_id = X` untuk menghindari duplikasi data)*.

---

## 6. Development Milestones

1.  **Phase 1: Foundation (Minggu 1-2)**
    * Setup CI4, konfigurasi `.env`, dan integrasi Tailwind CSS.
    * Migrasi struktur `Master db.docx` menggunakan fitur CI4 *Migrations* dan *Seeders*[cite: 429].
    * Pembuatan RBAC (Admin, Guru, Walas).
2.  **Phase 2: Academic Logic (Minggu 3-4)**
    * Pembuatan modul CRUD dinamis untuk Mapel, Jilid, Detail, dan Kriteria Penilaian.
    * [cite_start]Pembuatan pemetaan `guru_mapel` dan `siswa_rombel`[cite: 825, 845].
3.  **Phase 3: The Engine (Minggu 5-6)**
    * Pembuatan antarmuka Input Progres Santri.
    * Implementasi logika kalkulasi nilai progres ke Rapor.
4.  **Phase 4: PWA & Offline Experience (Minggu 7-8)**
    * Registrasi *Service Worker* dan *Manifest*.
    * Pembuatan skrip `IndexedDB` untuk menyimpan *queue* data *offline*.
    * *Testing* pemutusan jaringan dan verifikasi sinkronisasi *background*.