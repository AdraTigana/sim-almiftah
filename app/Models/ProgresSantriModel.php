<?php

namespace App\Models;

use CodeIgniter\Model;

class ProgresSantriModel extends Model
{
    protected $table            = 'progres_santri';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'siswa_id', 'mapel_id', 'kategori_id', 'detail_kategori_id', 'user_id', 'rombel_id',
        'nilai', 'predikat', 'catatan', 'kriteria_data', 'local_id', 'sync_status',
        'nilai_p', 'nilai_k', 'nilai_s', 'predikat_p', 'predikat_k', 'predikat_s'
    ];
    protected $useTimestamps    = true;

    protected $validationRules = [
        'siswa_id' => 'required|numeric',
        'mapel_id' => 'required|numeric',
        'user_id'  => 'required|numeric',
        'nilai'    => 'permit_empty|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
        'nilai_p'  => 'permit_empty|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
        'nilai_k'  => 'permit_empty|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
        'nilai_s'  => 'permit_empty|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
    ];
}
