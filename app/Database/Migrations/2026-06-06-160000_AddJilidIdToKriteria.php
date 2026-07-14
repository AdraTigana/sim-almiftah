<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddJilidIdToKriteria extends Migration
{
    public function up()
    {
        $this->forge->addColumn('kriteria_penilaian', [
            'jilid_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
        ]);
        $this->forge->addForeignKey('jilid_id', 'jilid', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->forge->dropForeignKey('kriteria_penilaian', 'kriteria_penilaian_jilid_id_foreign');
        $this->forge->dropColumn('kriteria_penilaian', 'jilid_id');
    }
}
