<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProgresSantriTable extends Migration
{
    public function up()
    {
        $this->db->query("CREATE TYPE sync_status_enum AS ENUM('synced', 'pending')");

        $this->forge->addField([
            'id'              => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'siswa_id'        => ['type' => 'INT', 'unsigned' => true],
            'mapel_id'        => ['type' => 'INT', 'unsigned' => true],
            'jilid_id'        => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'detail_jilid_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'user_id'         => ['type' => 'INT', 'unsigned' => true],
            'rombel_id'       => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'nilai'           => ['type' => 'INT', 'null' => true],
            'predikat'        => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
            'catatan'         => ['type' => 'TEXT', 'null' => true],
            'local_id'        => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'sync_status'     => ['type' => 'sync_status_enum', 'default' => 'synced'],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('siswa_id', 'siswa', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('mapel_id', 'mapel', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('jilid_id', 'jilid', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('detail_jilid_id', 'detail_jilid', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('rombel_id', 'rombel', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('progres_santri');
    }

    public function down()
    {
        $this->forge->dropTable('progres_santri');
        $this->db->query("DROP TYPE IF EXISTS sync_status_enum");
    }
}
