<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TahunAjarSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['tahun' => '2025/2026', 'is_active' => 1, 'is_current' => 0],
            ['tahun' => '2025/2026', 'is_active' => 1, 'is_current' => 1],
        ];
        $this->db->table('tahun_ajar')->insertBatch($data);
    }
}
