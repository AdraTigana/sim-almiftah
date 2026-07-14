<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FixKategoriStructureSeeder extends Seeder
{
    public function run()
    {
        $db = $this->db;

        // ============================================================
        // 0. Bersihkan duplikat Tasmi' Kategori 5 (mapel_id=1, kategori_id=19)
        //    Hanya hapus jika ada nama kriteria yang persis sama > 1
        // ============================================================
        $kriteriaTasmi5 = $db->table('kriteria_penilaian')
            ->where('mapel_id', 1)
            ->where('kategori_id', 19)
            ->orderBy('id')
            ->get()
            ->getResultArray();

        $namaCount = [];
        foreach ($kriteriaTasmi5 as $k) {
            $namaCount[$k['nama']] = ($namaCount[$k['nama']] ?? 0) + 1;
        }
        $duplicateNama = array_filter($namaCount, fn($c) => $c > 1);

        if (!empty($duplicateNama)) {
            $deleteIds = [];
            $kept = [];
            foreach ($kriteriaTasmi5 as $k) {
                if (($namaCount[$k['nama']] ?? 0) > 1) {
                    if (isset($kept[$k['nama']])) {
                        $deleteIds[] = $k['id'];
                    } else {
                        $kept[$k['nama']] = true;
                    }
                }
            }
            if (!empty($deleteIds)) {
                $db->table('kriteria_penilaian')->whereIn('id', $deleteIds)->delete();
                echo "Duplikat Tasmi' Kategori 5 dibersihkan: hapus " . count($deleteIds) . " kriteria.\n";
            } else {
                echo "Tasmi' Kategori 5: tidak ada duplikat.\n";
            }
        } else {
            echo "Tasmi' Kategori 5: tidak ada duplikat.\n";
        }

        // ============================================================
        // 1. Fix Nahwu Al Miftah (mapel_id=7)
        // ============================================================
        $this->fixMapel(7, 'Nahwu Al Miftah');

        // ============================================================
        // 2. Fix Tahqiq Al Miftah (mapel_id=8)
        // ============================================================
        $this->fixMapel(8, 'Tahqiq Al Miftah');

        // ============================================================
        // 3. Tasmi' Kategori 5 (mapel_id=1, kategori_id=19): Nilai Ujian → UTS + UAS
        // ============================================================
        $tblKriteria = $db->table('kriteria_penilaian');
        $existing = $tblKriteria->where('mapel_id', 1)->where('kategori_id', 19)->get()->getResultArray();
        $currentNames = array_column($existing, 'nama');
        if (!in_array('UTS', $currentNames) || !in_array('UAS', $currentNames)) {
            // Hapus semua kriteria lama kategori 5
            $tblKriteria->where('kategori_id', 19)->delete();
            // Insert UTS + UAS
            $tblKriteria->insertBatch([
                ['mapel_id' => 1, 'kategori_id' => 19, 'nama' => 'UTS', 'bobot' => 50, 'skala_max' => 100, 'input_type' => 'number', 'is_active' => 1],
                ['mapel_id' => 1, 'kategori_id' => 19, 'nama' => 'UAS', 'bobot' => 50, 'skala_max' => 100, 'input_type' => 'number', 'is_active' => 1],
            ]);
            echo "Tasmi' Kategori 5: kriteria diubah jadi UTS + UAS.\n";
        } else {
            echo "Tasmi' Kategori 5: sudah UTS + UAS.\n";
        }

        // ============================================================
        // 4. Tasmi' Sharaf (mapel_id=9): tambah Kategori 3 (UTS + UAS)
        // ============================================================
        $tblKategori = $db->table('kategori');
        $existingKategori9 = $tblKategori->where('mapel_id', 9)->orderBy('id')->get()->getResultArray();
        $byUrutan9 = [];
        foreach ($existingKategori9 as $k) {
            $byUrutan9[$k['urutan']] = $k;
        }

        if (!isset($byUrutan9[3])) {
            $tblKategori->insert([
                'mapel_id' => 9,
                'nama' => "Tasmi' Sharaf Kategori 3",
                'urutan' => 3,
                'is_active' => 1,
            ]);
            $kategori3Id = $db->insertID();
            echo "  Kategori baru: Tasmi' Sharaf Kategori 3 (id=$kategori3Id)\n";

            $tblKriteria->insertBatch([
                ['mapel_id' => 9, 'kategori_id' => $kategori3Id, 'nama' => 'UTS', 'bobot' => 50, 'skala_max' => 100, 'input_type' => 'number', 'is_active' => 1],
                ['mapel_id' => 9, 'kategori_id' => $kategori3Id, 'nama' => 'UAS', 'bobot' => 50, 'skala_max' => 100, 'input_type' => 'number', 'is_active' => 1],
            ]);
            echo "  Kriteria UTS + UAS ditambahkan untuk Tasmi' Sharaf Kategori 3.\n";
        } else {
            echo "Tasmi' Sharaf Kategori 3 sudah ada.\n";
        }

        echo "Selesai.\n";
    }

    /**
     * Pastikan mapel memiliki tepat 3 kategori: urutan 1, 2, 3 dengan nama prefix+" Kategori N".
     * Hapus kategori extra (urutan > 3) jika ada (misalnya dari duplicate run).
     */
    private function fixMapel(int $mapelId, string $prefix)
    {
        $db = $this->db;
        $tblKategori = $db->table('kategori');

        // Ambil semua kategori existing untuk mapel ini
        $all = $tblKategori->where('mapel_id', $mapelId)->orderBy('id')->get()->getResultArray();

        // Group by urutan, simpan yang punya id terkecil
        $byUrutan = [];
        $extraKategoriIds = [];
        foreach ($all as $k) {
            $u = $k['urutan'];
            if (!isset($byUrutan[$u])) {
                $byUrutan[$u] = $k;
            } else {
                $extraKategoriIds[] = $k['id'];
            }
        }

        // Hapus kategori duplikat (urutan sama, id lebih besar)
        if (!empty($extraKategoriIds)) {
            $db->table('kriteria_penilaian')->whereIn('kategori_id', $extraKategoriIds)->delete();
            $tblKategori->whereIn('id', $extraKategoriIds)->delete();
            echo "  Hapus " . count($extraKategoriIds) . " kategori duplikat.\n";
        }

        // Hapus juga kategori dengan urutan > 3
        $all = $tblKategori->where('mapel_id', $mapelId)->orderBy('id')->get()->getResultArray();

        // Pastikan 3 kategori ada (urutan 1, 2, 3)
        $kategoriIds = [];
        for ($urutan = 1; $urutan <= 3; $urutan++) {
            $nama = "$prefix Kategori $urutan";
            if (isset($byUrutan[$urutan])) {
                $id = $byUrutan[$urutan]['id'];
                // Update nama
                $tblKategori->where('id', $id)->update(['nama' => $nama]);
            } else {
                $tblKategori->insert(['mapel_id' => $mapelId, 'nama' => $nama, 'urutan' => $urutan, 'is_active' => 1]);
                $id = $db->insertID();
                echo "  Kategori baru: $nama (id=$id)\n";
            }
            $kategoriIds[$urutan] = $id;
        }

        // Hapus semua kriteria lama untuk kategori 1-3
        $db->table('kriteria_penilaian')
            ->whereIn('kategori_id', array_values($kategoriIds))
            ->delete();

        echo "  Kriteria lama dihapus untuk $prefix.\n";

        // Insert kriteria baru
        $kriteria = [];

        // Nilai Harian 1-6 (urutan 1)
        for ($i = 1; $i <= 6; $i++) {
            $kriteria[] = [
                'mapel_id' => $mapelId,
                'kategori_id' => $kategoriIds[1],
                'nama' => "Nilai Harian $i",
                'bobot' => 100,
                'skala_max' => 100,
                'input_type' => 'number',
            ];
        }

        // Nilai Tugas 1-6 (urutan 2)
        for ($i = 1; $i <= 6; $i++) {
            $kriteria[] = [
                'mapel_id' => $mapelId,
                'kategori_id' => $kategoriIds[2],
                'nama' => "Nilai Tugas $i",
                'bobot' => 100,
                'skala_max' => 100,
                'input_type' => 'number',
            ];
        }

        // Ujian (urutan 3): UTS + UAS
        $kriteria[] = [
            'mapel_id' => $mapelId,
            'kategori_id' => $kategoriIds[3],
            'nama' => 'UTS',
            'bobot' => 50,
            'skala_max' => 100,
            'input_type' => 'number',
        ];
        $kriteria[] = [
            'mapel_id' => $mapelId,
            'kategori_id' => $kategoriIds[3],
            'nama' => 'UAS',
            'bobot' => 50,
            'skala_max' => 100,
            'input_type' => 'number',
        ];

        $db->table('kriteria_penilaian')->insertBatch($kriteria);
        echo "  " . count($kriteria) . " kriteria baru untuk $prefix.\n";
    }
}
