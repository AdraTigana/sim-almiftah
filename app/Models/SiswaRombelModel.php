<?php

namespace App\Models;

use CodeIgniter\Model;

class SiswaRombelModel extends Model
{
    protected $table            = 'siswa_rombel';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['siswa_id', 'rombel_id', 'tahun_ajar_id'];
    protected $useTimestamps    = true;

    public function getSiswaByRombel(int $rombelId)
    {
        return $this->select('siswa_rombel.*, siswa.nis, siswa.nama')
                    ->join('siswa', 'siswa.id = siswa_rombel.siswa_id')
                    ->where('siswa_rombel.rombel_id', $rombelId)
                    ->where('siswa.is_active', 1)
                    ->findAll();
    }
}
