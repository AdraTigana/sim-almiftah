<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMapelIdToPresensi extends Migration
{
    public function up()
    {
        $this->forge->addColumn('presensi', [
            'mapel_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => false,
            ],
        ]);
        $this->forge->addForeignKey('mapel_id', 'mapel', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->forge->dropForeignKey('presensi', 'presensi_mapel_id_foreign');
        $this->forge->dropColumn('presensi', 'mapel_id');
    }
}
