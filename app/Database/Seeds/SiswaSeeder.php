<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SiswaSeeder extends Seeder
{
    public function run()
    {
        $siswa = [
            ['nis' => 'ALM001', 'nama' => 'Ahmad Fauzi',      'jenkel' => 'L', 'tempat_lahir' => 'Padang',     'tanggal_lahir' => '2008-05-12', 'alamat' => 'Padang Panjang', 'nama_wali' => 'H. Abdullah',  'is_active' => 1],
            ['nis' => 'ALM002', 'nama' => 'Muhammad Rizki',    'jenkel' => 'L', 'tempat_lahir' => 'Bukittinggi', 'tanggal_lahir' => '2008-08-20', 'alamat' => 'Bukittinggi',      'nama_wali' => 'H. Zainuddin', 'is_active' => 1],
            ['nis' => 'ALM003', 'nama' => 'Ali Akbar',         'jenkel' => 'L', 'tempat_lahir' => 'Payakumbuh',  'tanggal_lahir' => '2009-01-15', 'alamat' => 'Payakumbuh',       'nama_wali' => 'H. Syafruddin','is_active' => 1],
            ['nis' => 'ALM004', 'nama' => 'Hasan Basri',       'jenkel' => 'L', 'tempat_lahir' => 'Solok',       'tanggal_lahir' => '2008-11-30', 'alamat' => 'Solok',            'nama_wali' => 'H. Ahmad',     'is_active' => 1],
            ['nis' => 'ALM005', 'nama' => 'Husain Al-Hafizh',  'jenkel' => 'L', 'tempat_lahir' => 'Padang',     'tanggal_lahir' => '2009-03-22', 'alamat' => 'Padang',           'nama_wali' => 'H. Mansur',    'is_active' => 1],
            ['nis' => 'ALM006', 'nama' => 'Fatimah Az-Zahra',  'jenkel' => 'P', 'tempat_lahir' => 'Padang',     'tanggal_lahir' => '2008-07-18', 'alamat' => 'Padang',           'nama_wali' => 'H. Yusuf',     'is_active' => 1],
            ['nis' => 'ALM007', 'nama' => 'Aisyah Nur',        'jenkel' => 'P', 'tempat_lahir' => 'Bukittinggi', 'tanggal_lahir' => '2009-05-09', 'alamat' => 'Bukittinggi',      'nama_wali' => 'H. Hamid',     'is_active' => 1],
            ['nis' => 'ALM008', 'nama' => 'Khadijah Al-Kubra', 'jenkel' => 'P', 'tempat_lahir' => 'Payakumbuh',  'tanggal_lahir' => '2008-12-25', 'alamat' => 'Payakumbuh',       'nama_wali' => 'H. Rasyid',    'is_active' => 1],
        ];
        $this->db->table('siswa')->insertBatch($siswa);
    }
}
