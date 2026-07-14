<?php

namespace App\Models;

use CodeIgniter\Model;

class TahunAjarModel extends Model
{
    protected $table            = 'tahun_ajar';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['tahun', 'is_active', 'is_current'];
    protected $useTimestamps    = true;

    public function getActive()
    {
        return $this->where('is_active', 1)->where('is_current', 1)->first();
    }

    public function setCurrent(int $id)
    {
        $this->db->table('tahun_ajar')->update(['is_current' => 0]);
        $this->update($id, ['is_current' => 1]);
    }
}
