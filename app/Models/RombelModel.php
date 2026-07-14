<?php

namespace App\Models;

use CodeIgniter\Model;

class RombelModel extends Model
{
    protected $table            = 'rombel';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['nama', 'tahun_ajar_id', 'kelas', 'is_active', 'walas_id'];
    protected $useTimestamps    = true;
}
