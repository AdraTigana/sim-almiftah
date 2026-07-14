<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKelompokUrutanKkmToMapel extends Migration
{
    public function up()
    {
        $this->forge->addColumn('mapel', [
            'kelompok' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'urutan'   => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'kkm'      => ['type' => 'INT', 'unsigned' => true, 'default' => 70],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('mapel', ['kelompok', 'urutan', 'kkm']);
    }
}
