<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\SiswaModel;
use App\Models\ProgresSantriModel;
use App\Models\KategoriModel;
use App\Models\MapelModel;
use App\Models\TahunAjarModel;

class Dashboard extends BaseController
{
    public function index(): string
    {
        $siswaModel = new SiswaModel();
        $progresModel = new ProgresSantriModel();
        $kategoriModel = new KategoriModel();
        $mapelModel = new MapelModel();

        $taModel = new TahunAjarModel();
        $tahunAktif = $taModel->getActive();

        $totalSantri = $siswaModel->where('is_active', 1)->countAllResults();
        $totalGuru   = (new UserModel())->where('role_id !=', 1)->countAllResults();
        $totalMapel     = $mapelModel->where('is_active', 1)->countAllResults();
        $mapelCount  = $mapelModel->where('is_active', 1)->countAllResults();
        // Recent activity
        $aktivitasModel = new ProgresSantriModel();
        $aktivitas = $aktivitasModel->select('progres_santri.created_at, siswa.nama, mapel.nama as mapel_nama, kategori.nama as kategori_nama, progres_santri.predikat')
                     ->join('siswa', 'siswa.id = progres_santri.siswa_id')
                     ->join('mapel', 'mapel.id = progres_santri.mapel_id', 'left')
                     ->join('kategori', 'kategori.id = progres_santri.kategori_id', 'left')
                     ->orderBy('progres_santri.created_at', 'DESC')
                     ->limit(5)
                     ->findAll();

        $aktivitasData = [];
        foreach ($aktivitas as $a) {
            $aktivitasData[] = [
                'nama'      => $a['nama'],
                'aktivitas' => ($a['mapel_nama'] ?? 'Mapel') . ' - ' . ($a['kategori_nama'] ?? '') . ' (' . ($a['predikat'] ?? '—') . ')',
                'waktu'     => timeAgo($a['created_at']),
            ];
        }

        // Recent student updates
        $updatesModel = new ProgresSantriModel();
        $santriUpdates = $updatesModel->select('siswa.nama, siswa.nis, mapel.nama as mapel_nama, kategori.nama as kategori_nama, progres_santri.predikat, progres_santri.created_at, progres_santri.sync_status')
                       ->join('siswa', 'siswa.id = progres_santri.siswa_id')
                       ->join('mapel', 'mapel.id = progres_santri.mapel_id', 'left')
                       ->join('kategori', 'kategori.id = progres_santri.kategori_id', 'left')
                       ->orderBy('progres_santri.created_at', 'DESC')
                       ->limit(5)
                       ->findAll();

        $updatesData = [];
        foreach ($santriUpdates as $s) {
            $updatesData[] = [
                'nama'   => $s['nama'],
                'nis'    => $s['nis'],
                'kategori'  => $s['kategori_nama'] ?? $s['mapel_nama'] ?? '—',
                'status' => $s['predikat'] ?? 'Baru',
                'waktu'  => timeAgo($s['created_at']),
            ];
        }

        // Kategori completion stats for chart
        $statsModel = new ProgresSantriModel();
        $kategoriStats = $statsModel
            ->select('mapel.nama as mapel_nama, kategori.nama as kategori_nama, kategori.urutan, COUNT(DISTINCT progres_santri.siswa_id) as total')
            ->join('mapel', 'mapel.id = progres_santri.mapel_id')
            ->join('kategori', 'kategori.id = progres_santri.kategori_id')
            ->where('progres_santri.nilai >=', 85)
            ->groupBy('progres_santri.kategori_id, mapel.id, mapel.nama, kategori.nama, kategori.urutan')
            ->orderBy('progres_santri.mapel_id')
            ->orderBy('kategori.urutan')
            ->findAll();

        $chartMax = 0;
        foreach ($kategoriStats as $ks) {
            if ($ks['total'] > $chartMax) $chartMax = (int)$ks['total'];
        }
        if ($chartMax === 0) $chartMax = 1; // avoid division by zero

        return $this->render('admin/dashboard', [
            'title'         => 'Dashboard Admin',
            'totalSantri'   => $totalSantri,
            'totalGuru'     => $totalGuru,
            'totalMapel'    => $totalMapel,
            'aktivitas'     => $aktivitasData,
            'santriUpdates' => $updatesData,
            'tahunAktif'    => $tahunAktif,
            'kategoriStats' => $kategoriStats,
            'chartMax'      => $chartMax,
        ]);
    }


}
