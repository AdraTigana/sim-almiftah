<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RombelSeeder extends Seeder
{
    public function run()
    {
        $this->db->table('rombel')->insertBatch([
            ['nama' => 'Al-Miftah 1A', 'tahun_ajar_id' => 2, 'kelas' => '1A', 'is_active' => 1],
            ['nama' => 'Al-Miftah 1B', 'tahun_ajar_id' => 2, 'kelas' => '1B', 'is_active' => 1],
        ]);

        // Siswa Rombel: Ahmad, Rizki, Ali -> 1A ; Hasan, Husain, Fatimah, Aisyah, Khadijah -> 1B
        $this->db->table('siswa_rombel')->insertBatch([
            ['siswa_id' => 1, 'rombel_id' => 1, 'tahun_ajar_id' => 2],
            ['siswa_id' => 2, 'rombel_id' => 1, 'tahun_ajar_id' => 2],
            ['siswa_id' => 3, 'rombel_id' => 1, 'tahun_ajar_id' => 2],
            ['siswa_id' => 4, 'rombel_id' => 2, 'tahun_ajar_id' => 2],
            ['siswa_id' => 5, 'rombel_id' => 2, 'tahun_ajar_id' => 2],
            ['siswa_id' => 6, 'rombel_id' => 2, 'tahun_ajar_id' => 2],
            ['siswa_id' => 7, 'rombel_id' => 2, 'tahun_ajar_id' => 2],
            ['siswa_id' => 8, 'rombel_id' => 2, 'tahun_ajar_id' => 2],
        ]);
    }
}
