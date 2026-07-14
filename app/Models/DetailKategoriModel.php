<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailKategoriModel extends Model
{
    protected $table            = 'detail_kategori';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['kategori_id', 'nama', 'halaman', 'urutan'];
    protected $useTimestamps    = true;

    public function getByKategori(int $kategoriId)
    {
        return $this->where('kategori_id', $kategoriId)->orderBy('urutan')->findAll();
    }
}
