<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MapelTemplateSeeder extends Seeder
{
    public function run()
    {
        // 1. Rename & update existing mapels
        $this->db->table('mapel')->where('id', 1)->update([
            'nama'      => 'Tasmi\' Al Miftah',
            'kelompok'  => 'BIDANG STUDI WAJIB',
            'urutan'    => 2,
            'kkm'       => 70,
        ]);
        $this->db->table('mapel')->where('id', 2)->update([
            'nama'      => 'Nahwu Al Jurumiyah',
            'kelompok'  => 'BIDANG STUDI POKOK',
            'urutan'    => 4,
            'kkm'       => 70,
        ]);
        $this->db->table('mapel')->where('id', 3)->update([
            'kelompok'  => 'BIDANG STUDI POKOK',
            'urutan'    => 6,
            'kkm'       => 70,
        ]);
        $this->db->table('mapel')->where('id', 7)->update([
            'kelompok'  => 'BIDANG STUDI POKOK',
            'urutan'    => 5,
            'kkm'       => 70,
        ]);
        $this->db->table('mapel')->where('id', 8)->update([
            'kelompok'  => 'BIDANG STUDI WAJIB',
            'urutan'    => 1,
            'kkm'       => 70,
        ]);
        $this->db->table('mapel')->where('id', 9)->update([
            'kelompok'  => 'BIDANG STUDI WAJIB',
            'urutan'    => 3,
            'kkm'       => 70,
        ]);

        // 2. Insert new mapels
        $newMapels = [
            [
                'nama'       => 'Tauhid',
                'singkatan'  => 'TAU',
                'deskripsi'  => 'Ilmu Tauhid',
                'kelompok'   => 'BIDANG STUDI POKOK',
                'urutan'     => 1,
                'kkm'        => 70,
                'is_active'  => 1,
            ],
            [
                'nama'       => 'Akhlak',
                'singkatan'  => 'AKH',
                'deskripsi'  => 'Ilmu Akhlak',
                'kelompok'   => 'BIDANG STUDI POKOK',
                'urutan'     => 2,
                'kkm'        => 70,
                'is_active'  => 1,
            ],
            [
                'nama'       => 'Fiqih',
                'singkatan'  => 'FIQ',
                'deskripsi'  => 'Ilmu Fiqih',
                'kelompok'   => 'BIDANG STUDI POKOK',
                'urutan'     => 3,
                'kkm'        => 70,
                'is_active'  => 1,
            ],
            [
                'nama'       => 'Tahfidz',
                'singkatan'  => 'THF',
                'deskripsi'  => 'Tahfidz Al-Qur\'an',
                'kelompok'   => 'BIDANG STUDI WAJIB',
                'urutan'     => 4,
                'kkm'        => 70,
                'is_active'  => 1,
            ],
            [
                'nama'          => 'Fiqih Lisan',
                'singkatan'     => 'FQL',
                'deskripsi'     => 'Fiqih Lisan (Praktik Fiqih)',
                'kelompok'      => 'BIDANG STUDI WAJIB',
                'urutan'        => 1,
                'group_urutan'  => 2,
                'kkm'           => 70,
                'is_active'     => 1,
            ],
        ];
        $this->db->table('mapel')->insertBatch($newMapels);
    }
}
