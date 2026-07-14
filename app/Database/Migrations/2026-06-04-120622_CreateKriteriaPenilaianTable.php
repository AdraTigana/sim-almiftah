<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKriteriaPenilaianTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'mapel_id'    => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'nama'        => ['type' => 'VARCHAR', 'constraint' => 150],
            'bobot'       => ['type' => 'INT', 'default' => 100],
            'skala_max'   => ['type' => 'INT', 'default' => 100],
            'is_active'   => ['type' => 'TINYINT', 'default' => 1],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('mapel_id', 'mapel', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('kriteria_penilaian');
    }

    public function down()
    {
        $this->forge->dropTable('kriteria_penilaian');
    }
}
