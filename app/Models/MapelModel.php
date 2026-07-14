<?php

namespace App\Models;

use CodeIgniter\Model;

class MapelModel extends Model
{
    protected $table            = 'mapel';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['nama', 'singkatan', 'deskripsi', 'kelompok', 'urutan', 'kkm', 'is_active'];
    protected $useTimestamps    = true;
    protected $useSoftDeletes   = false;
}
