<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddWalasIdToRombel extends Migration
{
    public function up()
    {
        $this->forge->addColumn('rombel', [
            'walas_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
        ]);
        $this->forge->addForeignKey('walas_id', 'users', 'id', 'SET NULL', 'CASCADE', 'fk_rombel_walas');
        $this->db->query('ALTER TABLE "rombel" ADD CONSTRAINT "uk_rombel_walas" UNIQUE ("walas_id")');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE "rombel" DROP CONSTRAINT IF EXISTS "uk_rombel_walas"');
        $this->db->query('ALTER TABLE "rombel" DROP CONSTRAINT IF EXISTS "fk_rombel_walas"');
        $this->db->query('ALTER TABLE "rombel" DROP COLUMN "walas_id"');
    }
}
