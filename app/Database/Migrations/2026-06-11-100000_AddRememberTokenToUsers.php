<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRememberTokenToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'remember_token' => ['type' => 'VARCHAR', 'constraint' => 128, 'null' => true],
            'remember_expires' => ['type' => 'DATETIME', 'null' => true],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', ['remember_token', 'remember_expires']);
    }
}
