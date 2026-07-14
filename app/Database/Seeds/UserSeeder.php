<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'email'    => 'admin@almiftah.sch.id',
                'password' => password_hash('admin123', PASSWORD_BCRYPT),
                'nama'     => 'Admin Pusat',
                'nip'      => 'ADM001',
                'role_id'  => 1,
                'is_active' => 1,
            ],
            [
                'email'    => 'ustadz@almiftah.sch.id',
                'password' => password_hash('guru123', PASSWORD_BCRYPT),
                'nama'     => 'Ustadz Ahmad',
                'nip'      => 'GUR001',
                'role_id'  => 2,
                'is_active' => 1,
            ],
            [
                'email'    => 'walas@almiftah.sch.id',
                'password' => password_hash('walas123', PASSWORD_BCRYPT),
                'nama'     => 'Ustadz Mansur',
                'nip'      => 'WLS001',
                'role_id'  => 3,
                'is_active' => 1,
            ],
        ];
        $this->db->table('users')->insertBatch($data);
    }
}
