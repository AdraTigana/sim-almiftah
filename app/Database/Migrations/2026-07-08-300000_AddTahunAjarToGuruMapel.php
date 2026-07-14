<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTahunAjarToGuruMapel extends Migration
{
    public function up()
    {
        $this->forge->addColumn('guru_mapel', [
            'tahun_ajar_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true,
            ],
        ]);
        $this->forge->addForeignKey('tahun_ajar_id', 'tahun_ajar', 'id', 'SET NULL', 'CASCADE', 'fk_gmapel_ta');

        // Isi existing record dengan tahun ajar aktif
        $db = \Config\Database::connect();
        $current = $db->table('tahun_ajar')->where('is_current', 1)->get()->getRowArray();
        if ($current) {
            $db->table('guru_mapel')->set('tahun_ajar_id', $current['id'])->update();
        }
    }

    public function down()
    {
        $this->forge->dropForeignKey('guru_mapel', 'fk_gmapel_ta');
        $this->forge->dropColumn('guru_mapel', 'tahun_ajar_id');
    }
}
