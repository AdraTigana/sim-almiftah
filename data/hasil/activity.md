# Activity Diagram — SIM Al-Miftah

---

## 1. Login

```plantuml
@startuml
|User|
start
:Input email & password;
:Submit form;
|System|
:Validasi kredensial;
if (Valid?) then (Ya)
  if (Remember Me?) then (Ya)
    :Generate remember_token;
    :Simpan ke DB + set cookie (30 hari);
  endif
  :Set session;
  :Redirect ke dashboard;
  stop
else (Tidak)
  |User|
  :Tampilkan pesan error;
  -[#D4A017]-> back
  note right: Kembali ke form login
endif
@enduml
```

---

## 2. Kelola Kurikulum (Admin)

```plantuml
@startuml
|Admin|
start
:Buka menu Kurikulum;
:Tampilkan 3 tab: Mapel, Jilid, Kriteria;
|Admin|
if (Pilih tab?) as (tabchoice)

elseif (Mapel)
  :Isi form nama + singkatan;
  :Submit Create / Delete;
  |System|
  if (Create) then (Ya)
    :Simpan ke tabel mapel;
  else (Delete)
    :Hapus dari tabel mapel;
  endif

elseif (Jilid)
  :Pilih Mapel + isi nama + urutan;
  :Toggle hitung_kosong;
  :Submit Create / Delete;
  |System|
  if (Create) then (Ya)
    :Simpan ke tabel jilid;
  else (Delete)
    :Hapus dari tabel jilid;
  endif

elseif (Kriteria)
  :Pilih Mapel & Jilid (opsional);
  :Isi nama + input_type + bobot + skala_max;
  :Submit Create / Delete;
  |System|
  if (Create) then (Ya)
    :Simpan ke tabel kriteria_penilaian;
  else (Delete)
    :Hapus dari tabel kriteria_penilaian;
  endif

endif
stop
@enduml
```

---

## 3. Input Nilai (Guru) — Flow Utama

```plantuml
@startuml
|Guru|
start
:Buka menu Kelas Saya;
:Pilih Mapel & Rombel;
|System|
:Tampilkan daftar siswa;
|Guru|
:Pilih siswa;
|System|
:Load halaman nilai_siswa;
:Tampilkan tab per Jilid + tab Nilai Akhir;
|Guru|

while (Navigasi antar tab?)
  if (Pilih tab Jilid?) then (Ya)
    |System|
    :AJAX load form jilid;
    :Tampilkan tabel kriteria + history;
    |Guru|
    :Input nilai per-kriteria;
    note right: number / text / checkbox
    |System|
    :Hitung total & predikat otomatis;
    :Autosave ke IndexedDB;
    if (Online?) then (Ya)
      :Trigger Service Worker;
      :POST /nilai/sync-batch;
      :Hapus dari IndexedDB;
      :Update status → hijau (tersimpan);
    else (Tidak)
      :Tandai status pending (kuning);
      note right: Menunggu koneksi
    endif
  else (← Pilih tab Nilai Akhir)
    |Guru|
    if (Mode?) as (modechoice)
    elseif (Auto)
      |System|
      :Hitung rata-rata semua jilid;
    elseif (Manual)
      |Guru|
      :Input nilai per-jilid;
    endif
    |Guru|
    :Klik Simpan Nilai Akhir;
    |System|
    :Simpan ke tabel progres_santri (jilid_id = NULL);
    :Update status;

  endif
endwhile
|Guru|
stop
@enduml
```

---

## 4. Presensi (Guru)

```plantuml
@startuml
|Guru|
start
:Buka menu Presensi;
:Pilih Mapel & Rombel;
:Pilih Tanggal;
|System|
:Tampilkan daftar siswa;
|Guru|
for each siswa
  :Pilih status: Hadir / Sakit / Izin / Alpha;
endfor
|Guru|
:Klik Simpan Semua;
|System|
:Upsert batch ke tabel presensi;
:Tampilkan notifikasi sukses;
stop
@enduml
```

---

## 5. Lihat Rapor (Wali Kelas)

```plantuml
@startuml
|Wali Kelas|
start
:Buka menu Rapor Kelas;
:Pilih Rombel;
:Pilih Tahun Ajaran;
|System|
:Query progres_santri per siswa per mapel;
:Query presensi per siswa;
:Tampilkan rapor kelas (accordion);
|Wali Kelas|
for each siswa
  :Lihat nilai semua mapel + predikat;
  :Lihat ringkasan absensi;
endfor
stop
@enduml
```

---

## 6. Cetak Rapor Excel (Wali Kelas)

```plantuml
@startuml
|Wali Kelas|
start
:Buka menu Cetak Rapor;
:Pilih Rombel;
|System|
:Tampilkan daftar siswa;
|Wali Kelas|
:Pilih siswa;
|System|
:Query nilai & absensi;
:Bangun spreadsheet via PhpSpreadsheet;
:Download file .xlsx;
stop
@enduml
```

---

## 7. Kelola Santri (Admin)

```plantuml
@startuml
|Admin|
start
:Buka menu Data Santri;
if (Aksi?) as (aksi)
elseif (Tambah Manual)
  :Isi form (NIS, Nama, Jenkel, TTL, dll);
  :Submit;
  |System|
  :Simpan ke tabel siswa;
elseif (Import Excel)
  :Pilih file .xlsx;
  |System|
  :Upload & parse;
  :Simpan batch ke tabel siswa;
  :Tampilkan hasil import;
elseif (Edit)
  |Admin|
  :Klik tombol edit pada siswa;
  |System|
  :Load data siswa via AJAX;
  |Admin|
  :Ubah data form;
  :Submit;
  |System|
  :Update tabel siswa;
elseif (Hapus)
  |Admin|
  :Klik tombol hapus;
  |System|
  :Tampilkan konfirmasi (SweetAlert2);
  |Admin|
  :Konfirmasi;
  |System|
  :Soft delete dari tabel siswa;
endif
stop
@enduml
```

---

## 8. Offline Sync (Service Worker + IndexedDB)

```plantuml
@startuml
start
:Input nilai saat offline;
:Simpan ke IndexedDB (status: pending);
:Tandai status global → kuning;
note right: Data tersimpan lokal

:Menunggu koneksi kembali...;
while (Belum online?)
  :-- idle --;
endwhile

:Event 'online' terdeteksi / klik Sync;
:Baca semua item pending dari IndexedDB;
:Kirim message ke Service Worker;
:SW fetch POST /nilai/sync-batch;
|System|
if (Response 200 OK?) then (Ya)
  :Hapus item dari IndexedDB;
  :Broadcast 'sync-success' ke halaman;
  :Update status global → hijau;
  :Tampilkan notifikasi sukses;
else (Gagal)
  :Biarkan tetap di IndexedDB;
  :Tampilkan pesan error;
  -[#D4A017]-> back
  note right: Retry nanti
endif
stop
@enduml
```

---

## 9. Login dengan Remember Me

```plantuml
@startuml
|User|
start
:Buka halaman login;
:Input email & password;
:Centang "Ingat Saya" (opsional);
:Submit;
|System|
:Verifikasi email & password (bcrypt);
if (Valid?) then (Ya)

  if (Remember Me dicentang?) then (Ya)
    :Generate token acak 128 char;
    :Simpan token ke tabel users;
    :Set cookie (30 hari);
  endif

  :Set session (isLoggedIn, userId, role);
  :Redirect ke dashboard sesuai role;
  stop

else (Tidak)
  |User|
  :Tampilkan pesan error: "Email atau password salah";
  -[#D4A017]-> back
endif
@enduml
```
