<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRaporTable extends Migration
{
    public function up()
    {
        $this->db->query("CREATE TYPE rapor_status_enum AS ENUM('draft', 'final')");

        $this->forge->addField([
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'siswa_id'      => ['type' => 'INT', 'unsigned' => true],
            'rombel_id'     => ['type' => 'INT', 'unsigned' => true],
            'periode'       => ['type' => 'VARCHAR', 'constraint' => 50],
            'tahun_ajar'    => ['type' => 'VARCHAR', 'constraint' => 20],
            'status'        => ['type' => 'rapor_status_enum', 'default' => 'draft'],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('siswa_id', 'siswa', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('rombel_id', 'rombel', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('rapor_header');

        $this->forge->addField([
            'id'              => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'rapor_header_id' => ['type' => 'INT', 'unsigned' => true],
            'mapel_id'        => ['type' => 'INT', 'unsigned' => true],
            'jilid_id'        => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'nilai_rata'      => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
            'predikat'        => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
            'catatan'         => ['type' => 'TEXT', 'null' => true],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('rapor_header_id', 'rapor_header', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('mapel_id', 'mapel', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('jilid_id', 'jilid', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('rapor_detail');
    }

    public function down()
    {
        $this->forge->dropTable('rapor_detail');
        $this->forge->dropTable('rapor_header');
        $this->db->query("DROP TYPE IF EXISTS rapor_status_enum");
    }
}
