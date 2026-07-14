<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddHitungKosongToJilid extends Migration
{
    public function up()
    {
        $this->forge->addColumn('jilid', [
            'hitung_kosong' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('jilid', 'hitung_kosong');
    }
}
