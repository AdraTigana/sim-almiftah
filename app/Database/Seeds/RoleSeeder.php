<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['kode' => 'admin', 'nama' => 'Administrator'],
            ['kode' => 'guru',  'nama' => 'Guru/Ustadz'],
            ['kode' => 'walas', 'nama' => 'Wali Kelas'],
        ];
        $this->db->table('roles')->insertBatch($data);
    }
}
