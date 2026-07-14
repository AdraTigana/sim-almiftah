<?php

namespace App\Models;

use CodeIgniter\Model;

class PresensiModel extends Model
{
    protected $table            = 'presensi';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['siswa_id', 'rombel_id', 'mapel_id', 'user_id', 'status', 'tanggal', 'keterangan'];
    protected $useTimestamps    = true;
    protected $useSoftDeletes   = false;
}
