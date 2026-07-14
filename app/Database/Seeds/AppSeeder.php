<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AppSeeder extends Seeder
{
    public function run()
    {
        $this->call('RoleSeeder');
        $this->call('UserSeeder');
        $this->call('MapelSeeder');
        $this->call('TasmiItemSeeder');
        $this->call('TahunAjarSeeder');
        $this->call('SiswaSeeder');
        $this->call('RombelSeeder');
        $this->call('GuruMapelSeeder');
    }
}
