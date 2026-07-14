<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTahunAjarTable extends Migration
{
    public function up()
    {
        $this->db->query("CREATE TYPE semester_enum AS ENUM('Ganjil', 'Genap')");
        // will be dropped by RemoveSemesterAndLegacyTahunAjar if not needed

        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'tahun'      => ['type' => 'VARCHAR', 'constraint' => 20],
            'semester'   => ['type' => 'semester_enum', 'default' => 'Ganjil'],
            'is_active'  => ['type' => 'TINYINT', 'default' => 1],
            'is_current' => ['type' => 'TINYINT', 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tahun_ajar');
    }

    public function down()
    {
        $this->forge->dropTable('tahun_ajar');
        $this->db->query("DROP TYPE IF EXISTS semester_enum");
    }
}
