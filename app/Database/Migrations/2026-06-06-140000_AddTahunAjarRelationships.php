<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTahunAjarRelationships extends Migration
{
    public function up()
    {
        // Add tahun_ajar_id to rombel
        $this->forge->addColumn('rombel', [
            'tahun_ajar_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
        ]);
        $this->forge->addForeignKey('tahun_ajar_id', 'tahun_ajar', 'id', 'SET NULL', 'CASCADE', 'fk_rombel_tahun_ajar');

        // Add tahun_ajar_id to rapor_header
        $this->forge->addColumn('rapor_header', [
            'tahun_ajar_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
        ]);
        $this->forge->addForeignKey('tahun_ajar_id', 'tahun_ajar', 'id', 'SET NULL', 'CASCADE', 'fk_rapor_header_tahun_ajar');

        // Add tahun_ajar_id to siswa_rombel
        $this->forge->addColumn('siswa_rombel', [
            'tahun_ajar_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
        ]);
        $this->forge->addForeignKey('tahun_ajar_id', 'tahun_ajar', 'id', 'SET NULL', 'CASCADE', 'fk_siswa_rombel_tahun_ajar');
    }

    public function down()
    {
        $this->forge->dropForeignKey('rombel', 'fk_rombel_tahun_ajar');
        $this->forge->dropColumn('rombel', 'tahun_ajar_id');

        $this->forge->dropForeignKey('rapor_header', 'fk_rapor_header_tahun_ajar');
        $this->forge->dropColumn('rapor_header', 'tahun_ajar_id');

        $this->forge->dropForeignKey('siswa_rombel', 'fk_siswa_rombel_tahun_ajar');
        $this->forge->dropColumn('siswa_rombel', 'tahun_ajar_id');
    }
}
