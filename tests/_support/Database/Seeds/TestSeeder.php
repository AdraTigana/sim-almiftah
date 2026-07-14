<?php

namespace Tests\Support\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TestSeeder extends Seeder
{
    public function run(): void
    {
        // roles
        $this->db->table('roles')->insertBatch([
            ['id' => 1, 'nama' => 'Admin'],
            ['id' => 2, 'nama' => 'Guru'],
            ['id' => 3, 'nama' => 'Wali Kelas'],
        ]);

        // users (password: password123 hashed)
        $hash = password_hash('password123', PASSWORD_DEFAULT);
        $this->db->table('users')->insertBatch([
            ['id' => 1, 'role_id' => 1, 'nama' => 'Admin User', 'email' => 'admin@test.com', 'username' => 'admin', 'password' => $hash, 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s')],
            ['id' => 2, 'role_id' => 2, 'nama' => 'Guru User', 'email' => 'guru@test.com', 'username' => 'guru', 'password' => $hash, 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s')],
        ]);

        // tahun_ajar
        $this->db->table('tahun_ajar')->insert([
            'id' => 1, 'tahun' => '2025/2026', 'semester' => 'Genap', 'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // mapel
        $this->db->table('mapel')->insertBatch([
            ['id' => 1, 'nama' => 'Tahfidz', 'kelompok' => 'Tahfidz', 'urutan' => 1, 'is_active' => 1],
            ['id' => 2, 'nama' => 'Kitabah', 'kelompok' => 'Kitabah', 'urutan' => 2, 'is_active' => 1],
            ['id' => 9, 'nama' => 'Ghorib Musykilat', 'kelompok' => 'Tahfidz', 'urutan' => 3, 'is_active' => 1],
        ]);

        // kategori
        $this->db->table('kategori')->insertBatch([
            ['id' => 1, 'mapel_id' => 2, 'nama' => 'Harian', 'urutan' => 1, 'created_at' => date('Y-m-d H:i:s')],
            ['id' => 2, 'mapel_id' => 2, 'nama' => 'Tugas', 'urutan' => 2, 'created_at' => date('Y-m-d H:i:s')],
            ['id' => 3, 'mapel_id' => 2, 'nama' => 'Ujian', 'urutan' => 3, 'created_at' => date('Y-m-d H:i:s')],
            ['id' => 5, 'mapel_id' => 1, 'nama' => 'Tasmi 1', 'urutan' => 5, 'created_at' => date('Y-m-d H:i:s')],
        ]);

        // kriteria_penilaian
        $this->db->table('kriteria_penilaian')->insertBatch([
            ['id' => 1, 'kategori_id' => 1, 'label' => 'Kelancaran', 'input_type' => 'angka'],
            ['id' => 2, 'kategori_id' => 1, 'label' => 'Makharijul Huruf', 'input_type' => 'angka'],
            ['id' => 3, 'kategori_id' => 2, 'label' => 'Tugas 1', 'input_type' => 'angka'],
        ]);

        // rombel
        $this->db->table('rombel')->insert([
            'id' => 1, 'nama' => '7A', 'tingkat' => 7,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // siswa
        $this->db->table('siswa')->insertBatch([
            ['id' => 1, 'nis' => 'S001', 'nama' => 'Ahmad Santri', 'created_at' => date('Y-m-d H:i:s')],
            ['id' => 2, 'nis' => 'S002', 'nama' => 'Budi Santri', 'created_at' => date('Y-m-d H:i:s')],
        ]);

        // siswa_rombel
        $this->db->table('siswa_rombel')->insertBatch([
            ['siswa_id' => 1, 'rombel_id' => 1, 'tahun_ajar_id' => 1, 'created_at' => date('Y-m-d H:i:s')],
            ['siswa_id' => 2, 'rombel_id' => 1, 'tahun_ajar_id' => 1, 'created_at' => date('Y-m-d H:i:s')],
        ]);

        // guru_mapel
        $this->db->table('guru_mapel')->insert([
            'guru_id' => 2, 'mapel_id' => 2, 'rombel_id' => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
