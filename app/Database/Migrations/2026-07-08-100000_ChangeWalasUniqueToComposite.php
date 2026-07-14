<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ChangeWalasUniqueToComposite extends Migration
{
    public function up()
    {
        $this->forge->dropKey('rombel', 'uk_rombel_walas');
        $this->db->query('ALTER TABLE "rombel" ADD CONSTRAINT "uk_walas_tahun_ajar" UNIQUE ("walas_id", "tahun_ajar_id")');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE "rombel" DROP CONSTRAINT IF EXISTS "uk_walas_tahun_ajar"');
        $this->db->query('ALTER TABLE "rombel" ADD CONSTRAINT "uk_rombel_walas" UNIQUE ("walas_id")');
    }
}
