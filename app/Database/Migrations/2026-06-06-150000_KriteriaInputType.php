<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class KriteriaInputType extends Migration
{
    public function up()
    {
        $this->db->query("CREATE TYPE input_type_enum AS ENUM('number', 'text', 'checkbox')");
        $this->db->query("ALTER TABLE kriteria_penilaian ADD COLUMN input_type input_type_enum NOT NULL DEFAULT 'number'");
    }

    public function down()
    {
        $this->forge->dropColumn('kriteria_penilaian', 'input_type');
        $this->db->query("DROP TYPE IF EXISTS input_type_enum");
    }
}
