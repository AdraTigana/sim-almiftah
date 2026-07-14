<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGroupUrutanToMapel extends Migration
{
    public function up()
    {
        $this->forge->addColumn('mapel', [
            'group_urutan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 1,
            ],
        ]);

        $this->db->table('mapel')
            ->where('nama', 'Fiqih Lisan')
            ->update(['group_urutan' => 2]);
    }

    public function down()
    {
        $this->forge->dropColumn('mapel', 'group_urutan');
    }
}
