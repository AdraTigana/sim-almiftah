<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveSemesterAndLegacyTahunAjar extends Migration
{
    public function up()
    {
        $this->dropColumnIfExists('rombel', 'tahun_ajar');
        $this->dropColumnIfExists('siswa_rombel', 'tahun_ajar');
        $this->dropColumnIfExists('tahun_ajar', 'semester');
    }

    public function down()
    {
        $this->forge->addColumn('tahun_ajar', [
            'semester' => ['type' => 'semester_enum', 'default' => 'Ganjil'],
        ]);
        $this->forge->addColumn('rombel', [
            'tahun_ajar' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
        ]);
        $this->forge->addColumn('siswa_rombel', [
            'tahun_ajar' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
        ]);
    }

    private function dropColumnIfExists(string $table, string $column): void
    {
        $count = $this->db->query(
            "SELECT COUNT(*) as cnt FROM information_schema.columns
             WHERE table_catalog = current_database() AND table_name = ? AND column_name = ?",
            [$table, $column]
        )->getRow()->cnt;

        if ($count > 0) {
            $this->db->query("ALTER TABLE \"{$table}\" DROP COLUMN \"{$column}\"");
        }
    }
}
