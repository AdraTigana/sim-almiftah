<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RestrukturPenilaianSeeder extends Seeder
{
    public function run()
    {
        // 1. Tambah kategori baru: Nahwu Kategori 3, Sharaf Kategori 3
        $newKategori = [
            ['mapel_id' => 2, 'nama' => 'Nilai Ujian', 'urutan' => 3, 'is_active' => 1],
            ['mapel_id' => 3, 'nama' => 'Nilai Ujian', 'urutan' => 3, 'is_active' => 1],
        ];

        // Cek apakah sudah ada
        $existing2 = array_column(
            $this->db->table('kategori')->where('mapel_id', 2)->get()->getResultArray(),
            'urutan'
        );
        $existing3 = array_column(
            $this->db->table('kategori')->where('mapel_id', 3)->get()->getResultArray(),
            'urutan'
        );

        $insert = [];
        foreach ($newKategori as $k) {
            $existing = ($k['mapel_id'] == 2) ? $existing2 : $existing3;
            if (!in_array($k['urutan'], $existing)) {
                $insert[] = $k;
            }
        }

        // Cek dan tambah Tasmi' Kategori 5
        $existingTasmi = array_column(
            $this->db->table('kategori')->where('mapel_id', 1)->orderBy('urutan')->get()->getResultArray(),
            'urutan'
        );
        if (!in_array(5, $existingTasmi)) {
            $insert[] = ['mapel_id' => 1, 'nama' => "Tasmi' Kategori 5", 'urutan' => 5, 'is_active' => 1];
        }

        if (!empty($insert)) {
            $this->db->table('kategori')->insertBatch($insert);
            echo "Tambah " . count($insert) . " kategori baru.\n";
        } else {
            echo "Semua kategori sudah ada.\n";
        }

        // 2. Dapatkan ID kategori setelah insert
        $allKategori2 = $this->db->table('kategori')->where('mapel_id', 2)->orderBy('urutan')->get()->getResultArray();
        $allKategori3 = $this->db->table('kategori')->where('mapel_id', 3)->orderBy('urutan')->get()->getResultArray();
        $allKategori1 = $this->db->table('kategori')->where('mapel_id', 1)->orderBy('urutan')->get()->getResultArray();

        $kategoriMap2 = [];
        foreach ($allKategori2 as $k) {
            $kategoriMap2[$k['urutan']] = $k['id'];
        }
        $kategoriMap3 = [];
        foreach ($allKategori3 as $k) {
            $kategoriMap3[$k['urutan']] = $k['id'];
        }
        $kategoriMap1 = [];
        foreach ($allKategori1 as $k) {
            $kategoriMap1[$k['urutan']] = $k['id'];
        }

        // 3. Hapus kriteria lama untuk non-Tasmi' (mapel_id 2 dan 3)
        $this->db->table('kriteria_penilaian')
            ->whereIn('mapel_id', [2, 3])
            ->delete();
        echo "Kriteria lama non-Tasmi' dihapus.\n";

        // 4. Insert kriteria baru untuk Nahwu (mapel_id=2)
        $kriteriaNahwu = [];
        // Nilai Harian 1-6 (kategori 1)
        for ($i = 1; $i <= 6; $i++) {
            $kriteriaNahwu[] = ['mapel_id' => 2, 'kategori_id' => $kategoriMap2[1], 'nama' => "Nilai Harian $i", 'bobot' => 100, 'skala_max' => 100, 'input_type' => 'number'];
        }
        // Nilai Tugas 1-6 (kategori 2)
        for ($i = 1; $i <= 6; $i++) {
            $kriteriaNahwu[] = ['mapel_id' => 2, 'kategori_id' => $kategoriMap2[2], 'nama' => "Nilai Tugas $i", 'bobot' => 100, 'skala_max' => 100, 'input_type' => 'number'];
        }
        // Ujian (kategori 3)
        $kriteriaNahwu[] = ['mapel_id' => 2, 'kategori_id' => $kategoriMap2[3], 'nama' => 'UTS',   'bobot' => 50,  'skala_max' => 100, 'input_type' => 'number'];
        $kriteriaNahwu[] = ['mapel_id' => 2, 'kategori_id' => $kategoriMap2[3], 'nama' => 'UAS',   'bobot' => 50,  'skala_max' => 100, 'input_type' => 'number'];

        // Insert kriteria baru untuk Sharaf (mapel_id=3)
        $kriteriaSharaf = [];
        // Nilai Harian 1-6 (kategori 1)
        for ($i = 1; $i <= 6; $i++) {
            $kriteriaSharaf[] = ['mapel_id' => 3, 'kategori_id' => $kategoriMap3[1], 'nama' => "Nilai Harian $i", 'bobot' => 100, 'skala_max' => 100, 'input_type' => 'number'];
        }
        // Nilai Tugas 1-6 (kategori 2)
        for ($i = 1; $i <= 6; $i++) {
            $kriteriaSharaf[] = ['mapel_id' => 3, 'kategori_id' => $kategoriMap3[2], 'nama' => "Nilai Tugas $i", 'bobot' => 100, 'skala_max' => 100, 'input_type' => 'number'];
        }
        // Ujian (kategori 3)
        $kriteriaSharaf[] = ['mapel_id' => 3, 'kategori_id' => $kategoriMap3[3], 'nama' => 'UTS',   'bobot' => 50,  'skala_max' => 100, 'input_type' => 'number'];
        $kriteriaSharaf[] = ['mapel_id' => 3, 'kategori_id' => $kategoriMap3[3], 'nama' => 'UAS',   'bobot' => 50,  'skala_max' => 100, 'input_type' => 'number'];

        $this->db->table('kriteria_penilaian')->insertBatch($kriteriaNahwu);
        $this->db->table('kriteria_penilaian')->insertBatch($kriteriaSharaf);
        echo "Kriteria baru non-Tasmi' ditambahkan.\n";

        // 5. Tambah kriteria untuk Tasmi' kategori 5
        if (isset($kategoriMap1[5])) {
            $kriteriaTasmi = [
                ['mapel_id' => 1, 'kategori_id' => $kategoriMap1[5], 'nama' => 'Nilai Ujian', 'bobot' => 100, 'skala_max' => 100, 'input_type' => 'number'],
            ];
            $this->db->table('kriteria_penilaian')->insertBatch($kriteriaTasmi);
            echo "Kriteria Nilai Ujian untuk Tasmi' ditambahkan.\n";
        } else {
            echo "ERROR: Tasmi' kategori 5 tidak ditemukan.\n";
        }
    }
}
