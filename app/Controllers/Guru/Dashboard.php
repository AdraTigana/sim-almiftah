<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\ProgresSantriModel;
use App\Models\SiswaRombelModel;
use App\Models\GuruMapelModel;
use App\Models\RombelModel;
use App\Models\TahunAjarModel;
use App\Models\KategoriModel;
use App\Models\MapelModel;

class Dashboard extends BaseController
{
    public function index(): string
    {
        $userId = session()->get('userId');
        $progresModel = new ProgresSantriModel();
        $guruMapelModel = new GuruMapelModel();
        $srModel = new SiswaRombelModel();
        $rombelModel = new RombelModel();
        $taModel = new TahunAjarModel();
        $kategoriModel = new KategoriModel();
        $mapelModel = new MapelModel();

        $tahunAktif = $taModel->getActive();
        $taId = $tahunAktif['id'] ?? null;

        // Ambil semua penugasan guru di tahun aktif
        $guruMapel = $guruMapelModel
            ->select('guru_mapel.*, mapel.nama as mapel_nama, rombel.nama as rombel_nama')
            ->join('mapel', 'mapel.id = guru_mapel.mapel_id')
            ->join('rombel', 'rombel.id = guru_mapel.rombel_id', 'left')
            ->where('guru_mapel.user_id', $userId)
            ->where('guru_mapel.tahun_ajar_id', $taId)
            ->findAll();

        $guruMapelIds = array_unique(array_column($guruMapel, 'mapel_id'));
        $rombelIds = array_unique(array_filter(array_column($guruMapel, 'rombel_id')));

        // Total santri & semua ID santri
        $totalSantri = 0;
        $semuaSantriIds = [];
        $santriPerRombel = [];
        foreach ($rombelIds as $rid) {
            $anggota = $srModel->getSiswaByRombel($rid);
            $santriPerRombel[$rid] = $anggota;
            $totalSantri += count($anggota);
            foreach ($anggota as $a) {
                $semuaSantriIds[] = $a['siswa_id'];
            }
        }
        $semuaSantriIds = array_unique($semuaSantriIds);

        // Progres pekan ini
        $progresPekanIni = 0;
        if (!empty($guruMapelIds)) {
            $progresPekanIni = $progresModel
                ->where('user_id', $userId)
                ->whereIn('mapel_id', $guruMapelIds)
                ->where('created_at >=', date('Y-m-d', strtotime('-7 days')))
                ->countAllResults();
        }

        // Per-mapel progress
        $mapelProgress = [];
        foreach ($guruMapel as $gm) {
            $mid = $gm['mapel_id'];
            if (!isset($mapelProgress[$mid])) {
                // Jumlah santri unik untuk mapel ini (dari semua rombel terkait)
                $mapelRombelIds = array_unique(array_filter(
                    array_column(array_filter($guruMapel, fn($g) => $g['mapel_id'] == $mid), 'rombel_id')
                ));
                $santriIds = [];
                foreach ($mapelRombelIds as $rid) {
                    foreach ($santriPerRombel[$rid] ?? [] as $s) {
                        $santriIds[] = $s['siswa_id'];
                    }
                }
                $santriIds = array_unique($santriIds);

                $mapelProgress[$mid] = [
                    'mapel_id'     => $mid,
                    'mapel_nama'   => $gm['mapel_nama'],
                    'rombel'       => [],
                    'total_santri' => count($santriIds),
                    'kategori'     => [],
                ];
            }
            $mapelProgress[$mid]['rombel'][] = [
                'rombel_id'   => $gm['rombel_id'],
                'rombel_nama' => $gm['rombel_nama'],
            ];
        }

        // Isi progress per kategori untuk setiap mapel
        foreach ($mapelProgress as &$mp) {
            $kategoris = $kategoriModel->getByMapel($mp['mapel_id']);
            foreach ($kategoris as $k) {
                $siswaIds = $progresModel
                    ->select('DISTINCT(siswa_id) as siswa_id')
                    ->where('mapel_id', $mp['mapel_id'])
                    ->where('kategori_id', $k['id'])
                    ->where('user_id', $userId)
                    ->findAll();
                $done = count($siswaIds);

                $mp['kategori'][] = [
                    'id'     => $k['id'],
                    'nama'   => $k['nama'],
                    'selesai'=> $done,
                    'total'  => $mp['total_santri'],
                ];
            }
        }
        unset($mp);

        // Progres terakhir (dengan info rombel)
        $progres = $progresModel
            ->select('progres_santri.*, siswa.nama as nama_siswa, mapel.id as mapel_id, mapel.nama as mapel, kategori.nama as kategori, kategori.urutan as kategori_urutan, rombel.nama as rombel_nama')
            ->join('siswa', 'siswa.id = progres_santri.siswa_id')
            ->join('mapel', 'mapel.id = progres_santri.mapel_id', 'left')
            ->join('kategori', 'kategori.id = progres_santri.kategori_id', 'left')
            ->join('rombel', 'rombel.id = progres_santri.rombel_id', 'left')
            ->where('progres_santri.user_id', $userId)
            ->orderBy('progres_santri.created_at', 'DESC')
            ->limit(8)
            ->findAll();

        $progresData = [];
        foreach ($progres as $p) {
            $urutan = (int)$p['kategori_urutan'];
            $kategoriNama = $p['kategori'] ?? '';
            $displayKategori = kategoriDisplayName(['urutan' => $urutan, 'nama' => $kategoriNama], $p['mapel_id']);

            $progresData[] = [
                'nama_siswa'  => $p['nama_siswa'],
                'mapel'       => $p['mapel'] ?? '',
                'kategori'    => $displayKategori,
                'rombel'      => $p['rombel_nama'] ?? '',
                'predikat'    => $p['predikat'] ? $this->_predikatLabel($p['predikat']) : '—',
                'waktu'       => timeAgo($p['created_at']),
            ];
        }

        $kelasDiampu = count($rombelIds);
        $mapelAktif = count($guruMapelIds);

        return $this->render('guru/dashboard', [
            'title'           => 'Dashboard Guru',
            'tahunAktif'      => $tahunAktif,
            'totalSantri'     => $totalSantri,
            'kelasDiampu'     => $kelasDiampu,
            'mapelAktif'      => $mapelAktif,
            'progresPekanIni' => $progresPekanIni,
            'mapelProgress'   => array_values($mapelProgress),
            'progresTerakhir' => $progresData,
        ]);
    }

    private function _predikatLabel($predikat): string
    {
        return predikatLabel($predikat);
    }
}
