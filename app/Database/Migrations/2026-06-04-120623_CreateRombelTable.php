<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRombelTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'nama'        => ['type' => 'VARCHAR', 'constraint' => 100],
            'tahun_ajar'  => ['type' => 'VARCHAR', 'constraint' => 20],
            'kelas'       => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'is_active'   => ['type' => 'TINYINT', 'default' => 1],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('rombel');
    }

    public function down()
    {
        $this->forge->dropTable('rombel');
    }
}
