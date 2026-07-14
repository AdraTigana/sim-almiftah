<?php

namespace App\Models;

use CodeIgniter\Model;

class KriteriaPenilaianModel extends Model
{
    protected $table            = 'kriteria_penilaian';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['mapel_id', 'kategori_id', 'nama', 'bobot', 'skala_max', 'input_type', 'is_active'];
    protected $useTimestamps    = true;

    public function getByMapel(int $mapelId)
    {
        return $this->where('mapel_id', $mapelId)->where('is_active', 1)->findAll();
    }

    public function getByMapelAndKategori(int $mapelId, ?int $kategoriId = null)
    {
        $this->where('mapel_id', $mapelId)->where('is_active', 1);
        if ($kategoriId !== null) {
            $this->groupStart()
                ->where('kategori_id', $kategoriId)
                ->orWhere('kategori_id', null)
            ->groupEnd();
        }
        return $this->orderBy('id', 'ASC')->findAll();
    }
}
