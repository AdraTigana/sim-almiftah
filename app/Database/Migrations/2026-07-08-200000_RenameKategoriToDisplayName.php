<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameKategoriToDisplayName extends Migration
{
    public function up()
    {
        // Mapel 2 (Nahwu)
        $this->db->table('kategori')
            ->where('mapel_id', 2)->where('urutan', 1)
            ->update(['nama' => 'Nilai Harian']);
        $this->db->table('kategori')
            ->where('mapel_id', 2)->where('urutan', 2)
            ->update(['nama' => 'Nilai Tugas']);
        $this->db->table('kategori')
            ->where('mapel_id', 2)->where('urutan', 3)
            ->update(['nama' => 'Nilai Ujian']);

        // Mapel 3 (Sharaf)
        $this->db->table('kategori')
            ->where('mapel_id', 3)->where('urutan', 1)
            ->update(['nama' => 'Nilai Harian']);
        $this->db->table('kategori')
            ->where('mapel_id', 3)->where('urutan', 2)
            ->update(['nama' => 'Nilai Tugas']);
        $this->db->table('kategori')
            ->where('mapel_id', 3)->where('urutan', 3)
            ->update(['nama' => 'Nilai Ujian']);

        // Tasmi' (mapel_id 1): kategori urutan 5 -> Nilai Ujian
        $this->db->table('kategori')
            ->where('mapel_id', 1)->where('urutan', 5)
            ->update(['nama' => 'Nilai Ujian']);
    }

    public function down()
    {
        $this->db->table('kategori')
            ->where('mapel_id', 2)->where('urutan', 1)
            ->update(['nama' => 'Nahwu Kategori 1']);
        $this->db->table('kategori')
            ->where('mapel_id', 2)->where('urutan', 2)
            ->update(['nama' => 'Nahwu Kategori 2']);
        $this->db->table('kategori')
            ->where('mapel_id', 2)->where('urutan', 3)
            ->update(['nama' => 'Nahwu Kategori 3']);
        $this->db->table('kategori')
            ->where('mapel_id', 3)->where('urutan', 1)
            ->update(['nama' => 'Sharaf Kategori 1']);
        $this->db->table('kategori')
            ->where('mapel_id', 3)->where('urutan', 2)
            ->update(['nama' => 'Sharaf Kategori 2']);
        $this->db->table('kategori')
            ->where('mapel_id', 3)->where('urutan', 3)
            ->update(['nama' => 'Sharaf Kategori 3']);
        $this->db->table('kategori')
            ->where('mapel_id', 1)->where('urutan', 5)
            ->update(['nama' => "Tasmi' Kategori 5"]);
    }
}
