<?php

namespace App\Controllers\WaliKelas;

use App\Controllers\BaseController;
use App\Models\RombelModel;
use App\Models\SiswaRombelModel;
use App\Models\TahunAjarModel;
use App\Models\MapelModel;

class Rekapitulasi extends BaseController
{
    public function index()
    {
        $userId = session()->get('userId');
        $rombelModel = new RombelModel();
        $taModel = new TahunAjarModel();

        $tahunAktif = $taModel->getActive();
        $selectedTaId = $this->request->getGet('tahun_ajar_id') ?: ($tahunAktif['id'] ?? null);

        $rombelQuery = $rombelModel->select('rombel.*, tahun_ajar.tahun as tahun_ajar_nama')
                       ->join('tahun_ajar', 'tahun_ajar.id = rombel.tahun_ajar_id', 'left')
                       ->where('rombel.walas_id', $userId)
                       ->where('rombel.is_active', 1);
        if ($selectedTaId) {
            $rombelQuery->where('rombel.tahun_ajar_id', $selectedTaId);
        }
        $rombelSaya = $rombelQuery->findAll();

        return $this->render('walas/rekapitulasi', [
            'title'      => 'Rekapitulasi',
            'rombelSaya' => $rombelSaya,
            'selectedTaId' => $selectedTaId,
            'tahunAjar'  => $taModel->orderBy('tahun', 'DESC')->findAll(),
        ]);
    }

    public function kelas(int $rombelId)
    {
        $userId = session()->get('userId');
        $rombelModel = new RombelModel();
        $srModel = new SiswaRombelModel();
        $mapelModel = new MapelModel();
        $taModel = new TahunAjarModel();
        $db = \Config\Database::connect();

        $rombel = $rombelModel->select('rombel.*, tahun_ajar.tahun as tahun_ajar_nama')
                   ->join('tahun_ajar', 'tahun_ajar.id = rombel.tahun_ajar_id', 'left')
                   ->where('rombel.walas_id', $userId)
                   ->find($rombelId);

        if (!$rombel) {
            return redirect()->to('walas/rekapitulasi')->with('error', 'Akses ditolak.');
        }

        $siswa = $srModel->getSiswaByRombel($rombelId);
        $siswaIds = array_column($siswa, 'siswa_id');

        $rekap = [];

        if (!empty($siswaIds)) {
            $allMapel = $mapelModel->where('is_active', 1)->orderBy('kelompok')->orderBy('urutan')->findAll();

            foreach ($allMapel as $m) {
                $sub = $db->table('progres_santri')
                    ->select('siswa_id, MAX(created_at) as max_created')
                    ->where('mapel_id', $m['id'])
                    ->whereIn('siswa_id', $siswaIds)
                    ->groupBy('siswa_id');
                $subQuery = $sub->getCompiledSelect();

                $rows = $db->table('progres_santri p')
                    ->select('p.siswa_id, p.nilai')
                    ->join('(' . $subQuery . ') latest', 'latest.siswa_id = p.siswa_id AND latest.max_created = p.created_at')
                    ->where('p.mapel_id', $m['id'])
                    ->whereIn('p.siswa_id', $siswaIds)
                    ->get()
                    ->getResultArray();

                if (empty($rows)) continue;

                $jml = count($rows);
                $sumNilai = 0;
                $tuntas = 0;
                foreach ($rows as $r) {
                    if ($r['nilai'] !== null) $sumNilai += $r['nilai'];
                    if (isTuntas($r['nilai'])) $tuntas++;
                }

                $rataNilai = $jml > 0 ? round($sumNilai / $jml, 1) : 0;

                $rekap[] = [
                    'mapel'       => $m['nama'],
                    'kkm'         => $m['kkm'] ?? 65,
                    'jumlah'      => $jml,
                    'rata_nilai'  => $rataNilai,
                    'tuntas'      => $tuntas,
                    'belum_tuntas' => $jml - $tuntas,
                ];
            }
        }

        return $this->render('walas/rekapitulasi_kelas', [
            'title'  => 'Rekapitulasi - ' . ($rombel['nama'] ?? ''),
            'rombel' => $rombel,
            'rekap'  => $rekap,
        ]);
    }
}
