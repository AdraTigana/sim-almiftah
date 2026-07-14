<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKompetensiToProgres extends Migration
{
    public function up()
    {
        $this->forge->addColumn('progres_santri', [
            'nilai_p'    => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
            'nilai_k'    => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
            'nilai_s'    => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
            'predikat_p' => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
            'predikat_k' => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
            'predikat_s' => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('progres_santri', ['nilai_p', 'nilai_k', 'nilai_s', 'predikat_p', 'predikat_k', 'predikat_s']);
    }
}
