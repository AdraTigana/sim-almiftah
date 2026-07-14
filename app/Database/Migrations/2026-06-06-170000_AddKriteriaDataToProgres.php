<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKriteriaDataToProgres extends Migration
{
    public function up()
    {
        $this->forge->addColumn('progres_santri', [
            'kriteria_data' => ['type' => 'TEXT', 'null' => true],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('progres_santri', 'kriteria_data');
    }
}
