# Sequence Diagram — SIM Al-Miftah

---

## 1. Login Flow

```plantuml
@startuml
actor ":User:" as User
participant "Browser" as Browser
participant "LoginController" as LoginCtrl
participant "UserModel" as UserModel
database "Database" as DB
participant "Session" as Session

User -> Browser: Input email & password
Browser -> LoginCtrl: POST /auth/login
LoginCtrl -> UserModel: verify(email, password)
UserModel -> DB: SELECT * FROM users WHERE email = ?
DB --> UserModel: user data (hashed password)
UserModel -> LoginCtrl: user object or null

alt Login berhasil
  LoginCtrl -> Session: set session data
  alt Remember Me checked
    LoginCtrl -> UserModel: save remember_token
    UserModel -> DB: UPDATE users SET remember_token = ?
    LoginCtrl -> Browser: set cookie remember_token (30 days)
  end
  LoginCtrl --> Browser: redirect to {role}/dashboard
  Browser --> User: Tampilkan dashboard
else Login gagal
  LoginCtrl --> Browser: redirect back with error flashdata
  Browser --> User: Tampilkan "Email atau password salah"
end
@enduml
```

---

## 2. Input Nilai Online

```plantuml
@startuml
actor ":Guru:" as Guru
participant "Browser\n(nilai_siswa)" as Browser
participant "Service Worker" as SW
participant "IndexedDB" as IDB
participant "NilaiController" as NilaiCtrl
participant "ProgresSantriModel" as Model
database "Database" as DB

== Pilih Siswa & Jilid ==
Guru -> Browser: Pilih siswa
Guru -> Browser: Pilih tab jilid
Browser -> NilaiCtrl: GET /nilai/siswa/form/{siswaId}/{mapelId}/{rombelId}/{jilidId}
NilaiCtrl -> Model: getOrCreate(siswaId, mapelId, jilidId)
Model -> DB: SELECT * FROM progres_santri WHERE ...
Model -> NilaiCtrl: existing progres or null
NilaiCtrl --> Browser: HTML form + riwayat nilai

== Input Nilai ==
Guru -> Browser: Input nilai per-kriteria (onInput)
Browser -> Browser: Hitung total & predikat otomatis
Browser -> IDB: savePendingKriteria(data)

== Sync via Service Worker ==
Browser -> SW: postMessage({ type: 'sync' })
SW -> NilaiCtrl: fetch POST /nilai/sync-batch (items: [...])
NilaiCtrl -> Model: _upsertKriteria() untuk setiap item
Model -> DB: DELETE + INSERT (upsert)
DB --> Model: success
Model -> NilaiCtrl: saved records
NilaiCtrl --> SW: JSON { success: true, results: [...] }
SW -> IDB: deletePendingSyncItems(localIds)
SW -> Browser: postMessage({ type: 'sync-success', count })
Browser -> Browser: updateGlobalSyncStatus() → hijau
Browser -> Guru: Tampilkan status "Tersimpan"
@enduml
```

---

## 3. Input Nilai Offline + Sync

```plantuml
@startuml
actor ":Guru:" as Guru
participant "Browser" as Browser
participant "IndexedDB" as IDB
participant "Service Worker" as SW
participant "NilaiController" as NilaiCtrl
database "Database" as DB

== Offline Phase ==
note over Guru: Koneksi internet terputus
Guru -> Browser: Input nilai per-kriteria
Browser -> Browser: Hitung total & predikat
Browser -> IDB: savePendingKriteria({ type:'kriteria', ... })
Browser -> Browser: updateGlobalSyncStatus() → kuning
note right: Data tersimpan lokal

== Reconnect Phase ==
note over Browser: Event 'online' terdeteksi
Browser -> SW: postMessage({ type: 'sync' })
SW -> IDB: getPendingSyncItems()
IDB --> SW: [item1, item2, ...]
SW -> NilaiCtrl: fetch POST /nilai/sync-batch (X-Offline-Sync: true)
NilaiCtrl -> NilaiCtrl: _upsertKriteria() untuk tiap item
NilaiCtrl -> DB: batch UPSERT
DB --> NilaiCtrl: success
NilaiCtrl --> SW: { success: true, results: [...] }
alt Semua sukses
  SW -> IDB: deletePendingSyncItems(syncedIds)
  SW -> Browser: postMessage({ type: 'sync-success', count })
  Browser -> Browser: updateGlobalSyncStatus() → hijau
  Browser -> Guru: Notifikasi "Data tersinkronasi"
else Ada yang gagal
  SW -> Browser: postMessage({ type: 'sync-error', errors: [...] })
  Browser -> Guru: Notifikasi error
end
@enduml
```

---

## 4. Presensi

```plantuml
@startuml
actor ":Guru:" as Guru
participant "Browser" as Browser
participant "PresensiController" as PresCtrl
participant "SiswaModel" as SiswaModel
participant "PresensiModel" as PresModel
database "Database" as DB

Guru -> Browser: Buka menu Presensi
Guru -> Browser: Pilih Mapel & Rombel & Tanggal
Browser -> PresCtrl: GET /presensi?mapel=X&rombel=Y&tanggal=T
PresCtrl -> SiswaModel: getByRombel(rombelId)
SiswaModel -> DB: SELECT * FROM siswa WHERE id IN (siswa_rombel...)
DB --> SiswaModel: daftar siswa
PresCtrl -> PresModel: getByDate(siswaIds, tanggal)
PresModel -> DB: SELECT * FROM presensi WHERE tanggal = T AND siswa_id IN (...)
DB --> PresModel: data presensi existing
PresModel --> PresCtrl: status per siswa
PresCtrl --> Browser: HTML form dengan status terisi

Guru -> Browser: Ubah status per siswa (Hadir/Sakit/Izin/Alpha)
Guru -> Browser: Klik "Simpan Semua"
Browser -> PresCtrl: POST /presensi/save-batch
PresCtrl -> PresModel: upsertBatch(siswaId, status, tanggal)
PresModel -> DB: INSERT ON DUPLICATE KEY UPDATE
DB --> PresModel: success
PresModel --> PresCtrl: affected rows
PresCtrl --> Browser: redirect + flashdata success
Browser -> Guru: Notifikasi "Presensi tersimpan"
@enduml
```

---

## 5. Cetak Rapor Excel

```plantuml
@startuml
actor ":Wali Kelas:" as WK
participant "Browser" as Browser
participant "CetakController" as CetakCtrl
participant "SiswaModel" as SiswaModel
participant "ProgresSantriModel" as ProgresModel
participant "PresensiModel" as PresModel
participant "RaporExcel" as RaporExcel
database "Database" as DB

WK -> Browser: Buka menu Cetak Rapor
WK -> Browser: Pilih Rombel
Browser -> CetakCtrl: GET /cetak?rombelId=X
CetakCtrl -> SiswaModel: getByRombel(rombelId)
SiswaModel -> DB: SELECT * FROM siswa_rombel JOIN siswa ...
DB --> SiswaModel: daftar siswa
CetakCtrl --> Browser: view dengan daftar siswa

WK -> Browser: Klik "Cetak Rapor" pada salah satu siswa
Browser -> CetakCtrl: GET /cetak/excel/{rombelId}/{siswaId}
CetakCtrl -> SiswaModel: getById(siswaId)
CetakCtrl -> ProgresModel: getBySiswaAndRombel(siswaId, rombelId)
ProgresModel -> DB: SELECT * FROM progres_santri WHERE ...
DB --> ProgresModel: semua nilai siswa
CetakCtrl -> PresModel: getBySiswaAndRombel(siswaId, rombelId)
PresModel -> DB: SELECT * FROM presensi WHERE ...
DB --> PresModel: data absensi
CetakCtrl -> RaporExcel: build(siswa, rombel, scores, attendance)
RaporExcel --> CetakCtrl: PhpSpreadsheet object
CetakCtrl --> Browser: download file .xlsx
Browser --> WK: File terunduh
@enduml
```

---

## 6. Dashboard (Admin)

```plantuml
@startuml
actor ":Admin:" as Admin
participant "Browser" as Browser
participant "DashboardController" as Ctrl
participant "SiswaModel" as SiswaModel
participant "UserModel" as UserModel
participant "JilidModel" as JilidModel
participant "ProgresSantriModel" as ProgresModel
database "Database" as DB

Admin -> Browser: Buka halaman dashboard
Browser -> Ctrl: GET /admin
Ctrl -> SiswaModel: countActive()
Ctrl -> UserModel: countByRole('guru')
Ctrl -> JilidModel: countActive()
Ctrl -> ProgresModel: countFinalGrades()
Ctrl -> ProgresModel: getJilidStats()
Ctrl -> ProgresModel: getRecentActivity(limit=10)

SiswaModel -> DB: SELECT COUNT(*) FROM siswa WHERE is_active = 1
UserModel -> DB: SELECT COUNT(*) FROM users JOIN roles ...
JilidModel -> DB: SELECT COUNT(*) FROM jilid WHERE is_active = 1
ProgresModel -> DB: SELECT AVG(nilai), COUNT(*) FROM progres_santri
ProgresModel -> DB: SELECT COUNT(*) GROUP BY mapel_id, jilid_id
ProgresModel -> DB: SELECT * FROM progres_santri ORDER BY created_at DESC

DB --> SiswaModel: total santri
DB --> UserModel: total guru
DB --> JilidModel: total jilid
DB --> ProgresModel: rata-rata nilai
DB --> ProgresModel: stats per jilid
DB --> ProgresModel: aktivitas terbaru

SiswaModel --> Ctrl: totalSantri
UserModel --> Ctrl: totalGuru
JilidModel --> Ctrl: totalJilid
ProgresModel --> Ctrl: rataNilai, jilidStats, aktivitas

Ctrl --> Browser: view dengan data
Browser --> Admin: Tampilkan kartu statistik, chart, tabel
@enduml
```
