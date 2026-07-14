# Diagram Sistem Informasi Akademik Al-Miftah

Dokumen ini berisi **Activity Diagram** dan **Sequence Diagram** untuk setiap modul utama sistem.

---

## Daftar Isi

1. [Login](#1-login)
2. [Mengelola Master Data](#2-mengelola-master-data)
3. [Mengelola Data Akademik](#3-mengelola-data-akademik)
4. [Mengelola Nilai Santri](#4-mengelola-nilai-santri)
5. [Mengelola Presensi Santri](#5-mengelola-presensi-santri)
6. [Melihat Laporan Kelas](#6-melihat-laporan-kelas)
7. [Mencetak Laporan](#7-mencetak-laporan)
8. [Mengatur Profil](#8-mengatur-profil)

---

## 1. Login

### Activity Diagram

```mermaid
flowchart TD
    A([Mulai]) --> B[Akses halaman login]
    B --> C[Tampilkan form login]
    C --> D[Isi email & password]
    D --> E[Submit]
    E --> F{Validasi input}
    F -->|Gagal| C
    F -->|Sukses| G{Cari user di DB}
    G -->|Tidak ditemukan| H[Tampilkan error<br/>'Email/password salah']
    H --> C
    G -->|Ditemukan| I{password_verify}
    I -->|Salah| H
    I -->|Cocok| J[Set session:<br/>userId, role, nama, isLoggedIn]
    J --> K{Arahkan sesuai role}
    K -->|admin| La[Redirect ke /admin]
    K -->|guru| Lb[Redirect ke /guru]
    K -->|walas| Lc[Redirect ke /walas]
    La --> Z([Selesai])
    Lb --> Z
    Lc --> Z

    style A fill:#4CAF50,color:#fff
    style Z fill:#f44336,color:#fff
    style F stroke:#FF9800,stroke-width:2px
    style G stroke:#FF9800,stroke-width:2px
    style I stroke:#FF9800,stroke-width:2px
    style K stroke:#FF9800,stroke-width:2px
```

### Sequence Diagram

```mermaid
sequenceDiagram
    participant U as User
    participant B as Browser
    participant A as Auth Controller
    participant DB as Database
    participant S as Session

    U->>B: Akses /
    B->>A: GET /auth/login
    A->>B: Render login.php
    B->>U: Tampilkan form login

    U->>B: Isi email + password
    B->>A: POST /auth/login
    A->>A: Validasi input (required)
    alt Validasi gagal
        A->>B: Redirect + error validasi
        B->>U: Tampilkan error
    else Validasi sukses
        A->>DB: Cari user by email
        DB-->>A: Data user (id, password_hash, role_id)
        alt User tidak ditemukan
            A->>B: Redirect + error 'Email/password salah'
        else User ditemukan
            A->>A: password_verify(password, hash)
            alt Password salah
                A->>B: Redirect + error 'Email/password salah'
            else Password cocok
                A->>S: set(userId, role, nama, isLoggedIn)
                A->>DB: Cari role by role_id
                DB-->>A: role.kode
                alt role = admin
                    A->>B: Redirect ke /admin
                else role = guru
                    A->>B: Redirect ke /guru
                else role = walas
                    A->>B: Redirect ke /walas
                end
                B->>U: Tampilkan dashboard
            end
        end
    end
```

---

## 2. Mengelola Master Data

### 2.1 Data Siswa (CRUD)

#### Activity Diagram

```mermaid
flowchart TD
    A([Mulai]) --> B[Akses menu Data Santri]
    B --> C{Sudah login?}
    C -->|Ya| D[GET /admin/santri]
    D --> E[Tampilkan daftar santri<br/>+ fitur: Tambah, Edit, Hapus, Import Excel]
    E --> F{Pilih aksi}

    F -->|Tambah| G[Tampilkan form tambah]
    G --> H[Isi data santri]
    H --> I[Submit POST /admin/santri/store]
    I --> J{Validasi}
    J -->|Gagal| G
    J -->|Sukses| K[Simpan ke DB]
    K --> L[Flash message 'Berhasil']
    L --> E

    F -->|Edit| M[Pilih santri]
    M --> N[GET /admin/santri/get/{id}]
    N --> O[Tampilkan form edit]
    O --> P[Ubah data]
    P --> Q[Submit POST /admin/santri/update/{id}]
    Q --> R{Validasi}
    R -->|Gagal| O
    R -->|Sukses| S[Update DB]
    S --> L

    F -->|Hapus| T[Konfirmasi hapus]
    T --> U[POST /admin/santri/delete/{id}]
    U --> V[Soft delete DB]
    V --> L

    F -->|Import Excel| W[Pilih file .xlsx]
    W --> X[POST /admin/santri/import-excel]
    X --> Y[Proses & validasi tiap baris]
    Y --> Z{Ada error?}
    Z -->|Ya| AA[Tampilkan error per baris]
    AA --> W
    Z -->|Tidak| AB[Import batch ke DB]
    AB --> L

    C -->|Tidak| AC[Redirect ke login]
    AC --> AD([Selesai])

    style A fill:#4CAF50,color:#fff
    style AD fill:#f44336,color:#fff
    style F stroke:#FF9800,stroke-width:2px
    style J stroke:#FF9800,stroke-width:2px
    style R stroke:#FF9800,stroke-width:2px
    style Z stroke:#FF9800,stroke-width:2px
```

#### Sequence Diagram

```mermaid
sequenceDiagram
    participant A as Admin
    participant B as Browser
    participant C as Admin\Santri Controller
    participant M as SiswaModel
    participant DB as Database

    Note over A,B: Melihat daftar santri
    A->>B: Klik menu Data Santri
    B->>C: GET /admin/santri
    C->>M: findAll() / ->paginate()
    M->>DB: SELECT * FROM siswa
    DB-->>M: Result set
    M-->>C: Array data santri
    C-->>B: Render admin/santri.php
    B-->>A: Tampilkan tabel + tombol aksi

    Note over A,B: Menambah santri
    A->>B: Klik "Tambah"
    B-->>A: Tampilkan form modal
    A->>B: Isi data + submit
    B->>C: POST /admin/santri/store
    C->>C: Validasi input
    alt Validasi gagal
        C-->>B: Response error
        B-->>A: Tampilkan error
    else Validasi sukses
        C->>M: insert($data)
        M->>DB: INSERT INTO siswa
        DB-->>M: OK
        M-->>C: Insert ID
        C-->>B: JSON {success: true}
        B-->>A: Notifikasi + reload tabel
    end

    Note over A,B: Mengedit santri
    A->>B: Klik "Edit" pada baris
    B->>C: GET /admin/santri/get/{id}
    C->>M: find($id)
    M->>DB: SELECT * FROM siswa WHERE id=?
    DB-->>M: Data santri
    M-->>C: Array data
    C-->>B: JSON data santri
    B-->>A: Form edit terisi
    A->>B: Ubah data + submit
    B->>C: POST /admin/santri/update/{id}
    C->>M: update($id, $data)
    M->>DB: UPDATE siswa SET ... WHERE id=?
    DB-->>M: OK
    M-->>C: true
    C-->>B: JSON {success: true}
    B-->>A: Notifikasi + reload

    Note over A,B: Menghapus santri
    A->>B: Klik "Hapus" + konfirmasi
    B->>C: POST /admin/santri/delete/{id}
    C->>M: delete($id) [soft delete]
    M->>DB: UPDATE siswa SET deleted_at=NOW() WHERE id=?
    DB-->>M: OK
    M-->>C: true
    C-->>B: JSON {success: true}
    B-->>A: Notifikasi + reload

    Note over A,B: Import Excel
    A->>B: Pilih file .xlsx
    B->>C: POST /admin/santri/import-excel
    C->>C: Baca file, parse tiap baris
    C->>M: insertBatch($rows)
    M->>DB: INSERT INTO siswa (batch)
    DB-->>M: OK
    M-->>C: Affected rows
    C-->>B: Redirect + flash message
    B-->>A: Notifikasi hasil import
```

### 2.2 Data Guru (CRUD)

#### Activity Diagram

```mermaid
flowchart TD
    A([Mulai]) --> B[Akses menu Data Guru]
    B --> C[GET /admin/guru]
    C --> D[Tampilkan daftar guru<br/>+ fitur: Tambah, Edit, Hapus, Atur Mapel]
    D --> E{Pilih aksi}

    E -->|Tambah| F[Tampilkan form tambah]
    F --> G[Isi data guru + pilih role]
    G --> H[Submit POST /admin/guru/store]
    H --> I{Validasi}
    I -->|Gagal| F
    I -->|Sukses| J[Simpan user ke DB]
    J --> K[Simpan assignment mapel<br/>(jika ada)]
    K --> L[Flash message]
    L --> D

    E -->|Edit| M[Pilih guru]
    M --> N[GET /admin/guru/get/{id}]
    N --> O[Tampilkan form edit<br/>+ checkbox mapel]
    O --> P[Ubah data]
    P --> Q[Submit POST /admin/guru/update/{id}]
    Q --> R{Validasi}
    R -->|Gagal| O
    R -->|Sukses| S[Update user DB]
    S --> T[Hapus assignment lama]
    T --> U[Simpan assignment baru]
    U --> L

    E -->|Hapus| V[Konfirmasi]
    V --> W[POST /admin/guru/delete/{id}]
    W --> X[Soft delete user]
    X --> L

    E -->|Atur Mapel| Y[Pilih tahun ajar]
    Y --> Z[Tampilkan checkbox:<br/>mapel × rombel]
    Z --> AA[Centang/ubah]
    AA --> AB[Submit POST /admin/guru/assign/{id}]
    AB --> AC[Hapus assignment lama]
    AC --> AD[Simpan assignment baru]
    AD --> L

    style A fill:#4CAF50,color:#fff
    style L fill:#2196F3,color:#fff
    style E stroke:#FF9800,stroke-width:2px
    style I stroke:#FF9800,stroke-width:2px
    style R stroke:#FF9800,stroke-width:2px
```

#### Sequence Diagram

```mermaid
sequenceDiagram
    participant A as Admin
    participant B as Browser
    participant C as Admin\Guru Controller
    participant U as UserModel
    participant G as GuruMapelModel
    participant T as TahunAjarModel
    participant DB as Database

    Note over A,B: Menambah guru
    A->>B: Isi form + submit
    B->>C: POST /admin/guru/store
    C->>C: Validasi input
    C->>U: insert($data)
    U->>DB: INSERT INTO users
    DB-->>U: OK (user_id)
    U-->>C: Insert ID
    C->>C: _saveAssignments(assign, user_id)
    C->>G: insertBatch($batch)
    G->>DB: INSERT INTO guru_mapel
    DB-->>G: OK
    G-->>C: true
    C-->>B: JSON {success: true}
    B-->>A: Notifikasi

    Note over A,B: Atur Mapel (popup)
    A->>B: Pilih tahun ajar
    B->>C: (via JS) rebuildAssignCheckboxes(taId)
    C-->>B: Data mapel + rombel
    B-->>A: Tampilkan checkbox
    A->>B: Centang/ubah + submit
    B->>C: POST /admin/guru/assign/{id}
    C->>G: where(user_id, taId)->delete()
    G->>DB: DELETE FROM guru_mapel
    DB-->>G: OK
    C->>G: insertBatch($batch)
    G->>DB: INSERT INTO guru_mapel
    DB-->>G: OK
    G-->>C: true
    C-->>B: JSON {success: true}
    B-->>A: Notifikasi + reload
```

---

## 3. Mengelola Data Akademik

### Activity Diagram

```mermaid
flowchart TD
    A([Mulai]) --> B[Akses menu Akademik]
    B --> C{Pilih submenu}

    C -->|Tahun Ajar| D[GET /admin/tahun-ajar]
    D --> E[Tampilkan daftar tahun ajar]
    E --> F{Pilih aksi}
    F -->|Tambah| G[Form + submit]
    G --> H[Simpan tahun ajar]
    H --> E
    F -->|Edit| I[Form + submit]
    I --> J[Update tahun ajar]
    J --> E
    F -->|Set Aktif| K[POST set-active/{id}]
    K --> L[Nonaktifkan semua]
    L --> M[Aktifkan yang dipilih]
    M --> E
    F -->|Hapus| N[Konfirmasi + delete]
    N --> E

    C -->|Rombel| O[GET /admin/rombel]
    O --> P[Tampilkan daftar rombel]
    P --> Q{Pilih aksi}
    Q -->|Tambah| R[Form + submit]
    R --> S[Simpan rombel]
    S --> P
    Q -->|Edit| T[Form + submit]
    T --> U[Update rombel]
    U --> P
    Q -->|Assign Walas| V[Pilih walas]
    V --> W[POST assign-walas]
    W --> P

    C -->|Kurikulum| X[GET /admin/kurikulum]
    X --> Y[Tampilkan: Mapel, Kategori, Kriteria]
    Y --> Z{Pilih kelola}
    Z -->|Mapel| AA[CRUD mapel]
    Z -->|Kategori| AB[CRUD kategori]
    Z -->|Kriteria| AC[CRUD kriteria]

    style A fill:#4CAF50,color:#fff
    style C stroke:#FF9800,stroke-width:2px
    style F stroke:#FF9800,stroke-width:2px
    style Q stroke:#FF9800,stroke-width:2px
    style Z stroke:#FF9800,stroke-width:2px
```

### Sequence Diagram

```mermaid
sequenceDiagram
    participant A as Admin
    participant B as Browser
    participant C as Controller
    participant M as Model
    participant DB as Database

    Note over A,B: CRUD Tahun Ajar
    A->>B: Klik Tahun Ajar
    B->>C: GET /admin/tahun-ajar
    C->>M: findAll()
    M->>DB: SELECT * FROM tahun_ajar
    DB-->>M: Result
    M-->>C: Data tahun ajar
    C-->>B: Render admin/tahun_ajar.php
    B-->>A: Tabel + action

    A->>B: Tambah tahun ajar
    B->>C: POST /admin/tahun-ajar/create
    C->>M: insert($data)
    M->>DB: INSERT INTO tahun_ajar
    DB-->>M: OK
    M-->>C: true
    C-->>B: Redirect + flash
    B-->>A: Tampilkan pesan sukses

    Note over A,B: CRUD Rombel
    A->>B: Klik Rombel
    B->>C: GET /admin/rombel
    C->>M: findAll() with tahun_ajar join
    M->>DB: SELECT * FROM rombel JOIN tahun_ajar
    DB-->>M: Result
    M-->>C: Data rombel
    C-->>B: Render admin/rombel.php
    B-->>A: Tabel + tombol aksi

    Note over A,B: Assign Walas ke Rombel
    A->>B: Pilih walas + submit
    B->>C: POST /admin/rombel/assign-walas/{id}
    C->>M: update($id, ['walas_id' => $walasId])
    M->>DB: UPDATE rombel SET walas_id=? WHERE id=?
    DB-->>M: OK
    M-->>C: true
    C-->>B: JSON {success: true}
    B-->>A: Notifikasi

    Note over A,B: CRUD Kurikulum (Mapel)
    A->>B: Klik Kurikulum
    B->>C: GET /admin/kurikulum
    C->>M: findAll() mapel + kategori + kriteria
    M->>DB: SELECT ... JOIN ...
    DB-->>M: Result
    M-->>C: All data
    C-->>B: Render admin/kurikulum.php
    B-->>A: Panel mapel + kategori + kriteria

    A->>B: Tambah mapel
    B->>C: POST /admin/kurikulum/mapel/create
    C->>M: insert mapel
    M->>DB: INSERT INTO mapel
    DB-->>M: OK
    M-->>C: true
    C-->>B: JSON {success: true}
    B-->>A: Notifikasi + reload
```

---

## 4. Mengelola Nilai Santri

### Activity Diagram

```mermaid
flowchart TD
    A([Mulai]) --> B[Login sebagai Guru]
    B --> C[Menu Kelas Saya]
    C --> D[GET /guru/input-saya]
    D --> E[Tampilkan card mapel × rombel]
    E --> F[Pilih mapel + rombel]
    F --> G[GET /guru/nilai/mapel/{mapelId}/kelas/{rombelId}]
    G --> H[Tampilkan tabel nilai:<br/>santri + kategori + input]
    H --> I{Pilih aksi}

    I -->|Input nilai| J[Klik sel nilai]
    J --> K[GET form kategori]
    K --> L[Input nilai + predikat]
    L --> M[Submit POST /guru/nilai/save]
    M --> N[Simpan ke progres_santri]
    N --> O[Hitung predikat otomatis]
    O --> P[Update tampilan]
    P --> H

    I -->|Input semua kategori| Q[Input nilai untuk setiap kategori]
    Q --> R[Submit POST /guru/nilai/save-akhir]
    R --> S[Simpan batch progres_santri<br/>+ hitung rata-rata]
    S --> P

    I -->|Lihat detail santri| T[Klik nama santri]
    T --> U[GET /guru/nilai/siswa/{siswaId}/mapel/{mapelId}/kelas/{rombelId}]
    U --> V[Tampilkan semua kategori + nilai]

    I -->|Sync batch| W[Submit POST /guru/nilai/sync-batch]
    W --> X[Update banyak nilai sekaligus]
    X --> P

    style A fill:#4CAF50,color:#fff
    style I stroke:#FF9800,stroke-width:2px
```

### Sequence Diagram

```mermaid
sequenceDiagram
    participant G as Guru
    participant B as Browser
    participant C as Guru\Nilai Controller
    participant P as ProgresSantriModel
    participant K as KategoriModel
    participant KR as KriteriaModel
    participant DB as Database

    Note over G,B: Memilih kelas & mapel
    G->>B: Klik card mapel × rombel
    B->>C: GET /guru/nilai/mapel/{id}/kelas/{id}
    C->>C: _canAccess(rombelId, mapelId)
    alt Tidak punya akses
        C-->>B: Redirect + error
        B-->>G: Tampilkan error
    else Punya akses
        C->>K: getByMapel($mapelId) with kriteria
        K->>DB: SELECT ... JOIN kategori_kriteria
        DB-->>K: Kategori + kriteria
        K-->>C: Array kategori
        C->>P: getLatestByMapel($rombelId, $mapelId)
        P->>DB: SELECT progres + siswa + nilai
        DB-->>P: Result
        P-->>C: Array progres santri
        C-->>B: Render guru/nilai_mapel.php
        B-->>G: Tabel nilai + form input
    end

    Note over G,B: Input nilai
    G->>B: Klik sel → isi nilai
    B->>C: POST /guru/nilai/save
    C->>C: Validasi input
    C->>P: insert/update progres
    P->>DB: INSERT INTO progres_santri
    DB-->>P: OK
    P-->>C: true
    C->>C: _calculatePredikat(nilai)
    C-->>B: JSON {success, predikat}
    B-->>G: Sel terupdate + predikat muncul

    Note over G,B: Input akhir (semua kategori)
    G->>B: Klik "Simpan Akhir"
    B->>C: POST /guru/nilai/save-akhir
    C->>P: insertBatch (semua kategori)
    P->>DB: INSERT INTO progres_santri (batch)
    DB-->>P: OK
    P-->>C: Affected rows
    C->>C: Hitung rata-rata per kategori
    C-->>B: JSON {success, data}
    B-->>G: Tampilan terbarui
```

---

## 5. Mengelola Presensi Santri

### Activity Diagram

```mermaid
flowchart TD
    A([Mulai]) --> B[Login sebagai Guru]
    B --> C[Menu Presensi]
    C --> D[GET /guru/presensi]
    D --> E[Filter: tahun ajar, mapel, rombel, tanggal]
    E --> F[Pilih filter]
    F --> G[GET /guru/presensi/get-siswa]
    G --> H[Tampilkan daftar santri + status presensi]
    H --> I{Pilih aksi}

    I -->|Input per santri| J[Pilih status:<br/>Hadir/Sakit/Izin/Alpha]
    J --> K[Submit POST /guru/presensi/save]
    K --> L[Simpan/update presensi]
    L --> H

    I -->|Input batch| M[Pilih status untuk semua]
    M --> N[Submit POST /guru/presensi/save-batch]
    N --> O[Simpan batch presensi]
    O --> H

    I -->|Sync batch| P[Edit banyak + submit]
    P --> Q[POST /guru/presensi/sync-batch]
    Q --> O

    I -->|Lihat rekap| R[GET /guru/presensi/rekap]
    R --> S[Tampilkan rekapitulasi<br/>presensi per santri]

    style A fill:#4CAF50,color:#fff
    style I stroke:#FF9800,stroke-width:2px
```

### Sequence Diagram

```mermaid
sequenceDiagram
    participant G as Guru
    participant B as Browser
    participant C as Guru\Presensi Controller
    participant P as PresensiModel
    participant SR as SiswaRombelModel
    participant DB as Database

    Note over G,B: Memuat halaman presensi
    G->>B: Klik menu Presensi
    B->>C: GET /guru/presensi
    C->>C: Load filter (mapel + rombel milik guru)
    C-->>B: Render guru/presensi.php
    B-->>G: Tampilkan filter + tabel kosong

    Note over G,B: Filter & load siswa
    G->>B: Pilih rombel + mapel + tanggal
    B->>C: GET /guru/presensi/get-siswa
    C->>SR: getSiswaByRombel($rombelId)
    SR->>DB: SELECT siswa JOIN siswa_rombel
    DB-->>SR: Daftar siswa
    SR-->>C: Array siswa

    C->>P: where(rombel_id, mapel_id, tanggal)->findAll()
    P->>DB: SELECT * FROM presensi WHERE ...
    DB-->>P: Presensi existing
    P-->>C: Array presensi

    C-->>B: JSON {siswa, presensi}
    B-->>G: Tampilkan tabel + status presensi

    Note over G,B: Simpan presensi batch
    G->>B: Pilih status untuk semua + submit
    B->>C: POST /guru/presensi/save-batch
    C->>C: Validasi
    C->>P: upsertBatch($data)
    P->>DB: INSERT ... ON DUPLICATE KEY UPDATE
    DB-->>P: Affected rows
    P-->>C: true
    C-->>B: JSON {success: true}
    B-->>G: Status terbarui semua

    Note over G,B: Lihat rekap
    G->>B: Klik menu Rekap Presensi
    B->>C: GET /guru/presensi/rekap
    C->>P: select SUM(status) GROUP BY siswa
    P->>DB: SELECT siswa_id, status, COUNT(*) ...
    DB-->>P: Rekap data
    P-->>C: Array rekap
    C-->>B: Render guru/presensi_rekap.php
    B-->>G: Tabel rekap presensi
```

---

## 6. Melihat Laporan Kelas

### Activity Diagram

```mermaid
flowchart TD
    A([Mulai]) --> B[Login sebagai Wali Kelas]
    B --> C[Menu Rapor]
    C --> D[GET /walas/rapor]
    D --> E[Filter tahun ajar]
    E --> F[Tampilkan card grid rombel]
    F --> G[Klik card rombel]
    G --> H[GET /walas/rapor/kelas/{id}]
    H --> I[Tampilkan tabel santri + presensi]
    I --> J{Pilih aksi}

    J -->|Detail santri| K[Klik Detail]
    K --> L[GET /walas/rapor/siswa/{rombelId}/{siswaId}]
    L --> M[Tampilkan nilai per mapel<br/>+ presensi + peringkat]
    M --> N[Klik Cetak Rapor]
    N --> O[Download Excel]

    J -->|Cetak langsung| P[Klik Cetak di baris]
    P --> O

    J -->|Kembali| Q[Klik Kembali]
    Q --> F

    B --> R[Menu Rekapitulasi]
    R --> S[GET /walas/rekapitulasi]
    S --> T[Card grid rombel]
    T --> U[Klik card]
    U --> V[GET /walas/rekapitulasi/kelas/{id}]
    V --> W[Tampilkan tabel rekap nilai:<br/>santri × mapel + tuntas/tidak]

    style A fill:#4CAF50,color:#fff
    style J stroke:#FF9800,stroke-width:2px
```

### Sequence Diagram

```mermaid
sequenceDiagram
    participant W as Wali Kelas
    participant B as Browser
    participant C as WaliKelas\Rapor Controller
    participant RC as WaliKelas\Rekapitulasi Controller
    participant P as ProgresSantriModel
    participant PR as PresensiModel
    participant R as RombelModel
    participant SR as SiswaRombelModel
    participant DB as Database

    Note over W,B: Melihat daftar rombel
    W->>B: Klik Rapor
    B->>C: GET /walas/rapor
    C->>R: where(walas_id, userId)->findAll()
    R->>DB: SELECT * FROM rombel WHERE walas_id=?
    DB-->>R: Rombel list
    R-->>C: Data rombel
    C-->>B: Render walas/rapor.php
    B-->>W: Card grid rombel

    Note over W,B: Melihat santri per rombel
    W->>B: Klik card rombel
    B->>C: GET /walas/rapor/kelas/{id}
    C->>R: find($id)
    R->>DB: SELECT * FROM rombel WHERE id=?
    DB-->>R: Data rombel
    R-->>C: Rombel data

    C->>SR: getSiswaByRombel($id)
    SR->>DB: SELECT siswa JOIN siswa_rombel
    DB-->>SR: Array siswa
    SR-->>C: Siswa list

    C->>PR: whereIn(siswa_id, rombel_id)->groupBy(siswa)
    PR->>DB: SELECT SUM(status) GROUP BY siswa_id
    DB-->>PR: Presensi aggregation
    PR-->>C: Data presensi per siswa

    C-->>B: Render walas/rapor_kelas.php
    B-->>W: Tabel santri + badge presensi

    Note over W,B: Rekapitulasi
    W->>B: Klik Rekapitulasi
    B->>RC: GET /walas/rekapitulasi
    RC->>R: where(walas_id, userId)->findAll()
    R-->>RC: Rombel list
    RC-->>B: Render walas/rekapitulasi.php
    B-->>W: Card grid rombel

    W->>B: Klik card rombel
    B->>RC: GET /walas/rekapitulasi/kelas/{id}
    RC->>P: getNilaiByRombel($id)
    P->>DB: SELECT progres JOIN siswa JOIN mapel WHERE rombel_id=?
    DB-->>P: Nilai data
    P-->>RC: Array [siswa][mapel] = nilai
    RC->>RC: isTuntas(nilai, kkm)
    RC-->>B: Render walas/rekapitulasi_kelas.php
    B-->>W: Tabel rekapitulasi
```

---

## 7. Mencetak Laporan

### Activity Diagram

```mermaid
flowchart TD
    A([Mulai]) --> B[Login sebagai Wali Kelas / Admin]
    B --> C[Menu Cetak Rapor]
    C --> D[GET /{role}/cetak]
    D --> E[Filter tahun ajar]
    E --> F[Tampilkan card grid rombel]
    F --> G[Klik card rombel]
    G --> H[GET /{role}/cetak/rombel/{id}]
    H --> I[Tampilkan tabel santri + tombol Cetak]
    I --> J[Klik Cetak Rapor]
    J --> K{Format apa?}

    K -->|Excel| L[GET /{role}/cetak/excel/{rombelId}/{siswaId}]
    L --> M[Load template format_rapor.xlsx]
    M --> N[Query data: nilai, presensi, siswa, walas]
    N --> O[Placeholder replacement:<br/>[NAMA_SISWA], [NILAI_1], dll]
    O --> P[Stream file .xlsx ke browser]
    P --> Q([Selesai])

    style A fill:#4CAF50,color:#fff
    style Q fill:#f44336,color:#fff
    style K stroke:#FF9800,stroke-width:2px
```

### Sequence Diagram

```mermaid
sequenceDiagram
    participant U as User (Walas/Admin)
    participant B as Browser
    participant C as WaliKelas\Cetak Controller
    participant R as RombelModel
    participant SR as SiswaRombelModel
    participant P as ProgresSantriModel
    participant PR as PresensiModel
    participant DB as Database

    Note over U,B: Pilih rombel
    U->>B: Akses /{role}/cetak
    B->>C: GET /{role}/cetak
    C->>R: where(is_active=1)->findAll()
    R-->>C: Rombel list
    C-->>B: Render cetak.php (card grid)
    B-->>U: Card rombel

    U->>B: Klik card
    B->>C: GET /{role}/cetak/rombel/{id}
    C->>R: find($id)
    R-->>C: Rombel data
    C->>SR: getSiswaByRombel($id)
    SR-->>C: Array siswa
    C-->>B: Render cetak_rombel.php
    B-->>U: Tabel siswa + tombol Cetak

    Note over U,B: Cetak Excel
    U->>B: Klik "Cetak Rapor"
    B->>C: GET /{role}/cetak/excel/{rombelId}/{siswaId}

    C->>C: _prepareData(rombelId, siswaId)
    C->>R: find($rombelId) [dengan walas_id filter]
    R-->>C: Data rombel

    C->>P: getNilaiAkhirBySiswa(siswaId)
    P->>DB: SELECT progres JOIN mapel WHERE siswa_id=?
    DB-->>P: Nilai per mapel
    P-->>C: Array nilai

    C->>PR: where(siswa_id, rombel_id)->first()
    PR->>DB: SELECT SUM(status) FROM presensi
    DB-->>PR: Presensi totals
    PR-->>C: Data presensi

    C->>C: Hitung rata-rata + peringkat
    C-->>C: Data lengkap untuk template

    Note over C: RaporExcelTemplate
    C->>C: new RaporExcelTemplate(format_rapor.xlsx)
    C->>C: str_replace placeholder di semua cell
    C->>C: Output Excel ke response

    C-->>B: Download file .xlsx
    B-->>U: Simpan / buka Excel
```

---

## 8. Mengatur Profil

### Activity Diagram

```mermaid
flowchart TD
    A([Mulai]) --> B[Login sebagai Admin / Guru / Walas]
    B --> C[Menu Profil]
    C --> D[GET /{role}/profil]
    D --> E[Tampilkan data profil:<br/>- Avatar inisial<br/>- Form: Nama, Email, NIP (readonly), Role (readonly)<br/>- Status Aktif<br/>- Ubah Password<br/>- Mata Pelajaran Diampu (Guru)]

    E --> F{Pilih aksi}

    F -->|Ubah Data Diri| G[Edit Nama / Email]
    G --> H[Submit POST /{role}/profil/update]
    H --> I{Validasi server}
    I -->|Nama/email kosong| J[Error 'wajib diisi']
    J --> G
    I -->|Email duplikat| K[Error 'sudah digunakan']
    K --> G
    I -->|Sukses| L[Update DB]
    L --> M[Update session 'nama']
    M --> N[Flash message 'Profil berhasil diperbarui']
    N --> E

    F -->|Ubah Password| O[Isi password lama + baru]
    O --> P{Validasi}
    P -->|Lama kosong| Q[Error]
    Q --> O
    P -->|Baru kosong| R[Error]
    R --> O
    P -->|Baru < 6 karakter| S[Error]
    S --> O
    P -->|Lama salah| T[Error 'tidak sesuai']
    T --> O
    P -->|Sukses| U[Hash password baru]
    U --> L

    F -->|Atur Penugasan (Guru)| V[Buka modal Atur Kelas]
    V --> W[Tampilkan checkbox:<br/>mapel × rombel]
    W --> X[Centang/ubah]
    X --> Y[Submit POST /guru/input-saya/assign]
    Y --> Z[Delete assignment lama<br/>(per tahun ajar)]
    Z --> AA[Insert batch baru]
    AA --> AB[Flash message]
    AB --> E

    style A fill:#4CAF50,color:#fff
    style F stroke:#FF9800,stroke-width:2px
    style I stroke:#FF9800,stroke-width:2px
    style P stroke:#FF9800,stroke-width:2px
```

### Sequence Diagram

```mermaid
sequenceDiagram
    participant U as User
    participant B as Browser
    participant C as {Role}\Profil Controller
    participant UM as UserModel
    participant GM as GuruMapelModel
    participant DB as Database

    Note over U,B: Melihat profil
    U->>B: Klik Profil
    B->>C: GET /{role}/profil
    C->>UM: getUserWithRole(userId)
    UM->>DB: SELECT users.*, roles.nama, roles.kode FROM users JOIN roles
    DB-->>UM: Data user + role
    UM-->>C: Array user

    alt Role = guru
        C->>GM: where(user_id)->findAll() dgn join mapel + rombel
        GM->>DB: SELECT guru_mapel JOIN mapel JOIN rombel
        DB-->>GM: Array assignment
        GM-->>C: Data mapel diampu
    end

    C-->>B: Render profil.php
    B-->>U: Biodata + form

    Note over U,B: Update profil
    U->>B: Edit nama/email + submit
    B->>C: POST /{role}/profil/update

    C->>C: trim(nama, email)
    alt Nama/email kosong
        C-->>B: Redirect + error
    else Cek duplikat email
        C->>UM: where(email)->where(id != userId)->first()
        UM-->>C: Ada/tidak
        alt Email sudah dipakai
            C-->>B: Redirect + error 'Email sudah digunakan'
        else Email unik
            alt Ada perubahan password
                C->>C: Validasi password (lama+baru wajib, min 6)
                alt Validasi gagal
                    C-->>B: Redirect + error
                else Validasi sukses
                    C->>C: password_verify(password_lama, hash)
                    alt Password lama salah
                        C-->>B: Redirect + error
                    else Password cocok
                        C->>C: password_hash(password_baru)
                        C->>UM: skipValidation(true)->update(id, data)
                        UM->>DB: UPDATE users SET ... WHERE id=?
                        DB-->>UM: OK
                        UM-->>C: true
                    end
                end
            else Tidak ada perubahan password
                C->>UM: skipValidation(true)->update(id, data)
                UM->>DB: UPDATE users SET nama=?, email=? WHERE id=?
                DB-->>UM: OK
                UM-->>C: true
            end

            C->>C: session()->set('nama', $nama)
            C-->>B: Redirect + flash 'Profil berhasil diperbarui'
            B-->>U: Notifikasi sukses
        end
    end

    Note over U,B: Atur Penugasan (khusus Guru)
    U->>B: Buka modal Atur Kelas
    B-->>U: Checkbox mapel × rombel
    U->>B: Centang + submit
    B->>C: POST /guru/input-saya/assign

    C->>C: Ambil tahun_ajar_id dari POST
    C->>C: db->transBegin()
    C->>GM: where(user_id, taId)->delete()
    GM->>DB: DELETE FROM guru_mapel WHERE ...
    DB-->>GM: OK

    alt Ada assignment dipilih
        C->>GM: insertBatch($batch)
        GM->>DB: INSERT INTO guru_mapel (batch)
        DB-->>GM: OK
    end

    alt transStatus() === false
        C->>C: db->transRollback()
        C-->>B: Redirect + error 'Gagal'
    else Sukses
        C->>C: db->transCommit()
        C-->>B: Redirect + flash 'Penugasan berhasil'
    end

    B-->>U: Notifikasi + reload
```

---

## Legenda

### Activity Diagram

| Simbol | Makna |
|--------|-------|
| `([Mulai])` / `([Selesai])` | Start / End |
| `[Persegi panjang]` | Action / Proses |
| `{Belah ketupat}` | Decision / Pilihan |
| `--[Label]-->` | Arrow dengan label |

### Sequence Diagram

| Simbol | Makna |
|--------|-------|
| `actor` | Pelaku (User) |
| `participant` | Komponen sistem |
| `->>` | Request / panggilan |
| `-->>` | Response / return |
| `alt ... else ... end` | Conditional branch |
| `Note over A,B` | Catatan / penjelasan |
