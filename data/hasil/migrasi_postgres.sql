-- ============================================================
-- PostgreSQL Schema Dump — SIM Al-Miftah
-- Generated from CI4 migrations (PostgreSQL-adapted)
-- ============================================================

-- ENUM types
CREATE TYPE jenkel_enum AS ENUM('L', 'P');
CREATE TYPE presensi_status_enum AS ENUM('hadir', 'sakit', 'izin', 'alpha');
CREATE TYPE rapor_status_enum AS ENUM('draft', 'final');
CREATE TYPE input_type_enum AS ENUM('number', 'text', 'checkbox');
CREATE TYPE sync_status_enum AS ENUM('synced', 'pending');

-- 1. roles
CREATE TABLE roles (
    id          SERIAL PRIMARY KEY,
    kode        VARCHAR(20) NOT NULL UNIQUE,
    nama        VARCHAR(100) NOT NULL,
    created_at  TIMESTAMP,
    updated_at  TIMESTAMP
);

-- 2. users
CREATE TABLE users (
    id                SERIAL PRIMARY KEY,
    email             VARCHAR(100) NOT NULL UNIQUE,
    password          VARCHAR(255) NOT NULL,
    nama              VARCHAR(150) NOT NULL,
    nip               VARCHAR(50),
    role_id           INTEGER NOT NULL REFERENCES roles(id) ON DELETE CASCADE ON UPDATE CASCADE,
    is_active         SMALLINT DEFAULT 1,
    avatar            VARCHAR(255),
    remember_token    VARCHAR(128),
    remember_expires  TIMESTAMP,
    deleted_at        TIMESTAMP,
    created_at        TIMESTAMP,
    updated_at        TIMESTAMP
);

-- 3. mapel
CREATE TABLE mapel (
    id            SERIAL PRIMARY KEY,
    nama          VARCHAR(100) NOT NULL,
    singkatan     VARCHAR(20),
    deskripsi     TEXT,
    kelompok      VARCHAR(100),
    urutan        INTEGER DEFAULT 0,
    kkm           INTEGER DEFAULT 70,
    is_active     SMALLINT DEFAULT 1,
    group_urutan  INTEGER DEFAULT 1,
    created_at    TIMESTAMP,
    updated_at    TIMESTAMP
);

-- 4. kategori (renamed from jilid)
CREATE TABLE kategori (
    id            SERIAL PRIMARY KEY,
    mapel_id      INTEGER NOT NULL REFERENCES mapel(id) ON DELETE CASCADE ON UPDATE CASCADE,
    nama          VARCHAR(100) NOT NULL,
    urutan        INTEGER DEFAULT 0,
    is_active     SMALLINT DEFAULT 1,
    hitung_kosong SMALLINT DEFAULT 0,
    created_at    TIMESTAMP,
    updated_at    TIMESTAMP
);

-- 5. detail_kategori (renamed from detail_jilid)
CREATE TABLE detail_kategori (
    id          SERIAL PRIMARY KEY,
    kategori_id INTEGER NOT NULL REFERENCES kategori(id) ON DELETE CASCADE ON UPDATE CASCADE,
    nama        VARCHAR(200) NOT NULL,
    halaman     VARCHAR(50),
    urutan      INTEGER DEFAULT 0,
    created_at  TIMESTAMP,
    updated_at  TIMESTAMP
);

-- 6. kriteria_penilaian
CREATE TABLE kriteria_penilaian (
    id          SERIAL PRIMARY KEY,
    mapel_id    INTEGER REFERENCES mapel(id) ON DELETE CASCADE ON UPDATE CASCADE,
    kategori_id INTEGER REFERENCES kategori(id) ON DELETE CASCADE ON UPDATE CASCADE,
    nama        VARCHAR(150) NOT NULL,
    bobot       INTEGER DEFAULT 100,
    skala_max   INTEGER DEFAULT 100,
    input_type  input_type_enum NOT NULL DEFAULT 'number',
    is_active   SMALLINT DEFAULT 1,
    created_at  TIMESTAMP,
    updated_at  TIMESTAMP
);

-- 6. tahun_ajar (must be before rombel references it)
CREATE TABLE tahun_ajar (
    id          SERIAL PRIMARY KEY,
    tahun       VARCHAR(20) NOT NULL,
    is_active   SMALLINT DEFAULT 1,
    is_current  SMALLINT DEFAULT 0,
    created_at  TIMESTAMP,
    updated_at  TIMESTAMP
);

-- 7. kriteria_penilaian
CREATE TABLE kriteria_penilaian (
    id          SERIAL PRIMARY KEY,
    mapel_id    INTEGER REFERENCES mapel(id) ON DELETE CASCADE ON UPDATE CASCADE,
    kategori_id INTEGER REFERENCES kategori(id) ON DELETE CASCADE ON UPDATE CASCADE,
    nama        VARCHAR(150) NOT NULL,
    bobot       INTEGER DEFAULT 100,
    skala_max   INTEGER DEFAULT 100,
    input_type  input_type_enum NOT NULL DEFAULT 'number',
    is_active   SMALLINT DEFAULT 1,
    created_at  TIMESTAMP,
    updated_at  TIMESTAMP
);

-- 8. rombel
CREATE TABLE rombel (
    id            SERIAL PRIMARY KEY,
    nama          VARCHAR(100) NOT NULL,
    kelas         VARCHAR(50),
    is_active     SMALLINT DEFAULT 1,
    walas_id      INTEGER REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,
    tahun_ajar_id INTEGER REFERENCES tahun_ajar(id) ON DELETE SET NULL ON UPDATE CASCADE,
    created_at    TIMESTAMP,
    updated_at    TIMESTAMP,
    CONSTRAINT uk_walas_tahun_ajar UNIQUE (walas_id, tahun_ajar_id)
);

-- 8. siswa
CREATE TABLE siswa (
    id            SERIAL PRIMARY KEY,
    nis           VARCHAR(30) NOT NULL UNIQUE,
    nama          VARCHAR(150) NOT NULL,
    jenkel        jenkel_enum NOT NULL DEFAULT 'L',
    tempat_lahir  VARCHAR(100),
    tanggal_lahir DATE,
    alamat        TEXT,
    nama_wali     VARCHAR(150),
    is_active     SMALLINT DEFAULT 1,
    deleted_at    TIMESTAMP,
    created_at    TIMESTAMP,
    updated_at    TIMESTAMP
);

-- 9. siswa_rombel
CREATE TABLE siswa_rombel (
    id            SERIAL PRIMARY KEY,
    siswa_id      INTEGER NOT NULL REFERENCES siswa(id) ON DELETE CASCADE ON UPDATE CASCADE,
    rombel_id     INTEGER NOT NULL REFERENCES rombel(id) ON DELETE CASCADE ON UPDATE CASCADE,
    tahun_ajar_id INTEGER REFERENCES tahun_ajar(id) ON DELETE SET NULL ON UPDATE CASCADE,
    created_at    TIMESTAMP,
    updated_at    TIMESTAMP
);

-- 10. guru_mapel
CREATE TABLE guru_mapel (
    id            SERIAL PRIMARY KEY,
    user_id       INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    mapel_id      INTEGER NOT NULL REFERENCES mapel(id) ON DELETE CASCADE ON UPDATE CASCADE,
    rombel_id     INTEGER REFERENCES rombel(id) ON DELETE CASCADE ON UPDATE CASCADE,
    tahun_ajar_id INTEGER REFERENCES tahun_ajar(id) ON DELETE SET NULL ON UPDATE CASCADE,
    created_at    TIMESTAMP,
    updated_at    TIMESTAMP,
    CONSTRAINT fk_gmapel_ta FOREIGN KEY (tahun_ajar_id) REFERENCES tahun_ajar(id) ON DELETE SET NULL ON UPDATE CASCADE
);

-- 11. progres_santri
CREATE TABLE progres_santri (
    id                  SERIAL PRIMARY KEY,
    siswa_id            INTEGER NOT NULL REFERENCES siswa(id) ON DELETE CASCADE ON UPDATE CASCADE,
    mapel_id            INTEGER NOT NULL REFERENCES mapel(id) ON DELETE CASCADE ON UPDATE CASCADE,
    kategori_id         INTEGER REFERENCES kategori(id) ON DELETE CASCADE ON UPDATE CASCADE,
    detail_kategori_id  INTEGER REFERENCES detail_kategori(id) ON DELETE CASCADE ON UPDATE CASCADE,
    user_id             INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    rombel_id           INTEGER REFERENCES rombel(id) ON DELETE CASCADE ON UPDATE CASCADE,
    nilai               INTEGER,
    predikat            VARCHAR(10),
    catatan             TEXT,
    kriteria_data       TEXT,
    nilai_p             DECIMAL(5,2),
    nilai_k             DECIMAL(5,2),
    nilai_s             DECIMAL(5,2),
    predikat_p          VARCHAR(10),
    predikat_k          VARCHAR(10),
    predikat_s          VARCHAR(10),
    local_id            VARCHAR(50),
    sync_status         sync_status_enum NOT NULL DEFAULT 'synced',
    created_at          TIMESTAMP,
    updated_at          TIMESTAMP
);

-- 12. presensi
CREATE TABLE presensi (
    id          SERIAL PRIMARY KEY,
    siswa_id    INTEGER NOT NULL REFERENCES siswa(id) ON DELETE CASCADE ON UPDATE CASCADE,
    rombel_id   INTEGER REFERENCES rombel(id) ON DELETE CASCADE ON UPDATE CASCADE,
    user_id     INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    mapel_id    INTEGER NOT NULL REFERENCES mapel(id) ON DELETE CASCADE ON UPDATE CASCADE,
    status      presensi_status_enum NOT NULL DEFAULT 'hadir',
    tanggal     DATE NOT NULL,
    keterangan  TEXT,
    created_at  TIMESTAMP,
    updated_at  TIMESTAMP
);

-- 14. rapor_header
CREATE TABLE rapor_header (
    id            SERIAL PRIMARY KEY,
    siswa_id      INTEGER NOT NULL REFERENCES siswa(id) ON DELETE CASCADE ON UPDATE CASCADE,
    rombel_id     INTEGER NOT NULL REFERENCES rombel(id) ON DELETE CASCADE ON UPDATE CASCADE,
    tahun_ajar_id INTEGER REFERENCES tahun_ajar(id) ON DELETE SET NULL ON UPDATE CASCADE,
    periode       VARCHAR(50) NOT NULL,
    tahun_ajar    VARCHAR(20) NOT NULL,
    status        rapor_status_enum NOT NULL DEFAULT 'draft',
    created_at    TIMESTAMP,
    updated_at    TIMESTAMP
);

-- 15. rapor_detail
CREATE TABLE rapor_detail (
    id              SERIAL PRIMARY KEY,
    rapor_header_id INTEGER NOT NULL REFERENCES rapor_header(id) ON DELETE CASCADE ON UPDATE CASCADE,
    mapel_id        INTEGER NOT NULL REFERENCES mapel(id) ON DELETE CASCADE ON UPDATE CASCADE,
    kategori_id     INTEGER REFERENCES kategori(id) ON DELETE CASCADE ON UPDATE CASCADE,
    nilai_rata      DECIMAL(5,2),
    predikat        VARCHAR(10),
    catatan         TEXT,
    created_at      TIMESTAMP,
    updated_at      TIMESTAMP
);

-- Migration tracking table (CI4 auto-creates this)
CREATE TABLE IF NOT EXISTS migrations (
    version    BIGINT NOT NULL,
    class      VARCHAR(255) NOT NULL,
    group_name VARCHAR(255) NOT NULL,
    namespace  VARCHAR(255) NOT NULL,
    time       INTEGER NOT NULL,
    batch      INTEGER NOT NULL
);
