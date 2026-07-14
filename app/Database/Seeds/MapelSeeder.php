<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MapelSeeder extends Seeder
{
    public function run()
    {
        // Mapel
        $mapelData = [
            ['nama' => 'Tasmi\'',   'singkatan' => 'TSM', 'deskripsi' => 'Setoran Hafalan Al-Qur\'an'],
            ['nama' => 'Nahwu',     'singkatan' => 'NHW', 'deskripsi' => 'Ilmu Nahwu (Gramatika Arab)'],
            ['nama' => 'Sharaf',    'singkatan' => 'SRF', 'deskripsi' => 'Ilmu Sharaf (Morfologi Arab)'],
        ];
        $this->db->table('mapel')->insertBatch($mapelData);

        // Kategori untuk Nahwu, Sharaf, Tasmi'
        $kategoriData = [
            ['mapel_id' => 2, 'nama' => 'Nilai Harian', 'urutan' => 1],
            ['mapel_id' => 2, 'nama' => 'Nilai Tugas',  'urutan' => 2],
            ['mapel_id' => 3, 'nama' => 'Nilai Harian', 'urutan' => 1],
            ['mapel_id' => 3, 'nama' => 'Nilai Tugas',  'urutan' => 2],
            ['mapel_id' => 1, 'nama' => 'Tasmi\' 1',    'urutan' => 1],
        ];
        $this->db->table('kategori')->insertBatch($kategoriData);

        // Detail Kategori
        $detailData = [
            ['kategori_id' => 1, 'nama' => 'Bab 1: Pengertian Nahwu', 'halaman' => '1-10', 'urutan' => 1],
            ['kategori_id' => 1, 'nama' => 'Bab 2: Kalimah', 'halaman' => '11-25', 'urutan' => 2],
            ['kategori_id' => 1, 'nama' => 'Bab 3: I\'rab', 'halaman' => '26-40', 'urutan' => 3],
            ['kategori_id' => 2, 'nama' => 'Bab 1: Marfu\'at', 'halaman' => '1-15', 'urutan' => 1],
            ['kategori_id' => 2, 'nama' => 'Bab 2: Manshubat', 'halaman' => '16-30', 'urutan' => 2],
            ['kategori_id' => 3, 'nama' => 'Bab 1: Wazan', 'halaman' => '1-12', 'urutan' => 1],
        ];
        $this->db->table('detail_kategori')->insertBatch($detailData);

        // Kriteria Penilaian (tanpa Tasmi' — item dari TasmiItemSeeder)
        $kriteriaData = [
            ['mapel_id' => 2, 'nama' => 'Kelancaran',     'bobot' => 30,  'skala_max' => 100, 'input_type' => 'number'],
            ['mapel_id' => 2, 'nama' => 'Pemahaman',      'bobot' => 50,  'skala_max' => 100, 'input_type' => 'number'],
            ['mapel_id' => 2, 'nama' => 'Praktek',        'bobot' => 20,  'skala_max' => 100, 'input_type' => 'number'],
            ['mapel_id' => 3, 'nama' => 'Kelancaran',     'bobot' => 40,  'skala_max' => 100, 'input_type' => 'number'],
            ['mapel_id' => 3, 'nama' => 'Hafalan Tashrif', 'bobot' => 60, 'skala_max' => 100, 'input_type' => 'number'],
        ];
        $this->db->table('kriteria_penilaian')->insertBatch($kriteriaData);
    }
}
