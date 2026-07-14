<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDetailJilidTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'jilid_id'    => ['type' => 'INT', 'unsigned' => true],
            'nama'        => ['type' => 'VARCHAR', 'constraint' => 200],
            'halaman'     => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'urutan'      => ['type' => 'INT', 'default' => 0],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('jilid_id', 'jilid', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('detail_jilid');
    }

    public function down()
    {
        $this->forge->dropTable('detail_jilid');
    }
}
