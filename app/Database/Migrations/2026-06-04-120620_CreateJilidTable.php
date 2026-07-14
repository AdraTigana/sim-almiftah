<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJilidTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'mapel_id'   => ['type' => 'INT', 'unsigned' => true],
            'nama'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'urutan'     => ['type' => 'INT', 'default' => 0],
            'is_active'  => ['type' => 'TINYINT', 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('mapel_id', 'mapel', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('jilid');
    }

    public function down()
    {
        $this->forge->dropTable('jilid');
    }
}
