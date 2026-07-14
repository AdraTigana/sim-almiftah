<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class GuruMapelSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // Hapus data lama
        $db->table('guru_mapel')->truncate();

        // Ambil semua rombel aktif
        $rombels = $db->table('rombel')->where('is_active', 1)->get()->getResultArray();

        $batch = [];

        // Ustadz Ahmad (id=2) mengajar Tasmi', Nahwu, Sharaf di semua rombel
        foreach ($rombels as $r) {
            foreach ([1 => 'Tasmi\'', 2 => 'Nahwu', 3 => 'Sharaf'] as $mapelId => $mapelNama) {
                $batch[] = [
                    'user_id'       => 2,
                    'mapel_id'      => $mapelId,
                    'rombel_id'     => $r['id'],
                    'tahun_ajar_id' => $r['tahun_ajar_id'],
                ];
            }
        }

        // Wali Kelas Mansur (id=3) mengajar Nahwu di Al-Miftah 1A (rombel_id=1)
        $batch[] = [
            'user_id'       => 3,
            'mapel_id'      => 2,
            'rombel_id'     => 1,
            'tahun_ajar_id' => 2,
        ];

        if (!empty($batch)) {
            $db->table('guru_mapel')->insertBatch($batch);
        }
    }
}
