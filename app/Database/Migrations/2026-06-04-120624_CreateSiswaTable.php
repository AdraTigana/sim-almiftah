<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSiswaTable extends Migration
{
    public function up()
    {
        $this->db->query("CREATE TYPE jenkel_enum AS ENUM('L', 'P')");

        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'nis'         => ['type' => 'VARCHAR', 'constraint' => 30, 'unique' => true],
            'nama'        => ['type' => 'VARCHAR', 'constraint' => 150],
            'jenkel'      => ['type' => 'jenkel_enum', 'default' => 'L'],
            'tempat_lahir' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'tanggal_lahir' => ['type' => 'DATE', 'null' => true],
            'alamat'      => ['type' => 'TEXT', 'null' => true],
            'nama_wali'   => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'is_active'   => ['type' => 'TINYINT', 'default' => 1],
            'deleted_at'  => ['type' => 'DATETIME', 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('siswa');
    }

    public function down()
    {
        $this->forge->dropTable('siswa');
        $this->db->query("DROP TYPE IF EXISTS jenkel_enum");
    }
}
