<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSiswaRombelTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'siswa_id'   => ['type' => 'INT', 'unsigned' => true],
            'rombel_id'  => ['type' => 'INT', 'unsigned' => true],
            'tahun_ajar' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('siswa_id', 'siswa', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('rombel_id', 'rombel', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('siswa_rombel');
    }

    public function down()
    {
        $this->forge->dropTable('siswa_rombel');
    }
}
