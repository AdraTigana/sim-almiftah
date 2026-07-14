<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameJilidToKategori extends Migration
{
    public function up()
    {
        // 1. Drop foreign keys referencing jilid
        $this->dropFkIfExists('detail_jilid', 'detail_jilid_jilid_id_foreign');
        $this->dropFkIfExists('progres_santri', 'progres_santri_jilid_id_foreign');
        $this->dropFkIfExists('rapor_detail', 'rapor_detail_jilid_id_foreign');
        $this->dropFkIfExists('progres_santri', 'progres_santri_detail_jilid_id_foreign');

        // 2. Rename columns in child tables (jilid_id → kategori_id)
        $this->db->query('ALTER TABLE "detail_jilid" RENAME COLUMN "jilid_id" TO "kategori_id"');
        $this->db->query('ALTER TABLE "kriteria_penilaian" RENAME COLUMN "jilid_id" TO "kategori_id"');
        $this->db->query('ALTER TABLE "progres_santri" RENAME COLUMN "jilid_id" TO "kategori_id"');
        $this->db->query('ALTER TABLE "rapor_detail" RENAME COLUMN "jilid_id" TO "kategori_id"');
        $this->db->query('ALTER TABLE "progres_santri" RENAME COLUMN "detail_jilid_id" TO "detail_kategori_id"');

        // 3. Rename tables
        $this->db->query('ALTER TABLE "detail_jilid" RENAME TO "detail_kategori"');
        $this->db->query('ALTER TABLE "jilid" RENAME TO "kategori"');

        // 4. Re-add foreign keys
        $this->db->query('ALTER TABLE "detail_kategori" ADD CONSTRAINT "fk_detail_kategori_kategori" FOREIGN KEY ("kategori_id") REFERENCES "kategori" ("id") ON DELETE CASCADE ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE "progres_santri" ADD CONSTRAINT "fk_progres_santri_kategori" FOREIGN KEY ("kategori_id") REFERENCES "kategori" ("id") ON DELETE CASCADE ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE "rapor_detail" ADD CONSTRAINT "fk_rapor_detail_kategori" FOREIGN KEY ("kategori_id") REFERENCES "kategori" ("id") ON DELETE CASCADE ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE "progres_santri" ADD CONSTRAINT "fk_progres_santri_detail_kategori" FOREIGN KEY ("detail_kategori_id") REFERENCES "detail_kategori" ("id") ON DELETE CASCADE ON UPDATE CASCADE');
    }

    public function down()
    {
        $this->dropFkIfExists('detail_kategori', 'fk_detail_kategori_kategori');
        $this->dropFkIfExists('progres_santri', 'fk_progres_santri_kategori');
        $this->dropFkIfExists('rapor_detail', 'fk_rapor_detail_kategori');
        $this->dropFkIfExists('progres_santri', 'fk_progres_santri_detail_kategori');

        $this->db->query('ALTER TABLE "detail_kategori" RENAME COLUMN "kategori_id" TO "jilid_id"');
        $this->db->query('ALTER TABLE "kriteria_penilaian" RENAME COLUMN "kategori_id" TO "jilid_id"');
        $this->db->query('ALTER TABLE "progres_santri" RENAME COLUMN "kategori_id" TO "jilid_id"');
        $this->db->query('ALTER TABLE "rapor_detail" RENAME COLUMN "kategori_id" TO "jilid_id"');
        $this->db->query('ALTER TABLE "progres_santri" RENAME COLUMN "detail_kategori_id" TO "detail_jilid_id"');

        $this->db->query('ALTER TABLE "kategori" RENAME TO "jilid"');
        $this->db->query('ALTER TABLE "detail_kategori" RENAME TO "detail_jilid"');

        $this->db->query('ALTER TABLE "detail_jilid" ADD CONSTRAINT "detail_jilid_jilid_id_foreign" FOREIGN KEY ("jilid_id") REFERENCES "jilid" ("id") ON DELETE CASCADE ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE "progres_santri" ADD CONSTRAINT "progres_santri_jilid_id_foreign" FOREIGN KEY ("jilid_id") REFERENCES "jilid" ("id") ON DELETE CASCADE ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE "rapor_detail" ADD CONSTRAINT "rapor_detail_jilid_id_foreign" FOREIGN KEY ("jilid_id") REFERENCES "jilid" ("id") ON DELETE CASCADE ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE "progres_santri" ADD CONSTRAINT "progres_santri_detail_jilid_id_foreign" FOREIGN KEY ("detail_jilid_id") REFERENCES "detail_jilid" ("id") ON DELETE CASCADE ON UPDATE CASCADE');
    }

    private function dropFkIfExists(string $table, string $constraint): void
    {
        $count = $this->db->query(
            "SELECT COUNT(*) as cnt FROM information_schema.table_constraints
             WHERE constraint_catalog = current_database() AND table_name = ? AND constraint_name = ?",
            [$table, $constraint]
        )->getRow()->cnt;

        if ($count > 0) {
            $this->db->query("ALTER TABLE \"{$table}\" DROP CONSTRAINT \"{$constraint}\"");
        }
    }
}
