<?php

namespace App\Models;

use CodeIgniter\Model;

class GuruMapelModel extends Model
{
    protected $table            = 'guru_mapel';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['user_id', 'mapel_id', 'rombel_id', 'tahun_ajar_id'];
    protected $useTimestamps    = true;

    public function getGuruWithMapel(int $userId)
    {
        return $this->select('guru_mapel.*, mapel.nama as mapel_nama, rombel.nama as rombel_nama')
                    ->join('mapel', 'mapel.id = guru_mapel.mapel_id')
                    ->join('rombel', 'rombel.id = guru_mapel.rombel_id', 'left')
                    ->where('guru_mapel.user_id', $userId)
                    ->findAll();
    }
}
