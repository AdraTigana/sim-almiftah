<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriModel extends Model
{
    protected $table            = 'kategori';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['mapel_id', 'nama', 'urutan', 'is_active', 'hitung_kosong'];
    protected $useTimestamps    = true;

    public function getByMapel(int $mapelId)
    {
        return $this->where('mapel_id', $mapelId)->where('is_active', 1)->orderBy('urutan')->findAll();
    }
}
