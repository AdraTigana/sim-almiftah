<?php

namespace App\Models;

use CodeIgniter\Model;

class SiswaModel extends Model
{
    protected $table            = 'siswa';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'nis', 'nama', 'jenkel', 'tempat_lahir', 'tanggal_lahir', 'alamat', 'nama_wali', 'is_active'
    ];
    protected $useTimestamps    = true;
    protected $useSoftDeletes   = true;
}
