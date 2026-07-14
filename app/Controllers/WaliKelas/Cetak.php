<?php

namespace App\Controllers\WaliKelas;

use App\Controllers\BaseController;
use App\Models\RombelModel;
use App\Models\SiswaRombelModel;
use App\Models\ProgresSantriModel;
use App\Models\PresensiModel;
use App\Models\MapelModel;
use App\Models\TahunAjarModel;

class Cetak extends BaseController
{
    protected function _prepareData(int $rombelId, int $siswaId): array
    {
        $userId = session()->get('userId');
        $role   = session()->get('role');
        $rombelModel = new RombelModel();
        $presensiModel = new PresensiModel();
        $siswaModel = new \App\Models\SiswaModel();
        $mapelModel = new MapelModel();

        $rombelQuery = $rombelModel->select('rombel.*, tahun_ajar.tahun as tahun_ajar_nama')
                   ->join('tahun_ajar', 'tahun_ajar.id = rombel.tahun_ajar_id', 'left');
        if ($role !== 'admin') {
            $rombelQuery->where('rombel.walas_id', $userId);
        }
        $rombel = $rombelQuery->find($rombelId);

        if (!$rombel) {
            return [];
        }

        $siswa = $siswaModel->find($siswaId);
        if (!$siswa) {
            return [];
        }

        $allMapel = $mapelModel->where('is_active', 1)
                       ->orderBy('kelompok')
                       ->orderBy('group_urutan')
                       ->orderBy('urutan')
                       ->findAll();

        $db = \Config\Database::connect();

        $subLatest = $db->table('progres_santri')
            ->select('mapel_id, MAX(created_at) as max_created')
            ->where('siswa_id', $siswaId)
            ->groupBy('mapel_id');
        $subLatestQuery = $subLatest->getCompiledSelect();

        $rows = $db->table('progres_santri p')
            ->select('m.nama as mapel, p.*')
            ->join('mapel m', 'm.id = p.mapel_id', 'left')
            ->join('(' . $subLatestQuery . ') latest', 'latest.mapel_id = p.mapel_id AND latest.max_created = p.created_at')
            ->where('p.siswa_id', $siswaId)
            ->get()
            ->getResultArray();

        $nilaiByMapel = [];
        foreach ($rows as $n) {
            $nilaiByMapel[$n['mapel']] = $n;
        }

        $rerataByMapel = [];
        $srModelLocal = new \App\Models\SiswaRombelModel();
        $siswaIds = $srModelLocal->where('rombel_id', $rombelId)->findAll();
        $siswaIdList = array_column($siswaIds, 'siswa_id');

        $subRerata = $db->table('progres_santri')
            ->select('mapel_id, MAX(created_at) as max_created')
            ->whereIn('siswa_id', $siswaIdList)
            ->groupBy('mapel_id, siswa_id');
        $subRerataQuery = $subRerata->getCompiledSelect();

        $rr = $db->table('progres_santri ps')
            ->select('m.nama as mapel, AVG(ps.nilai) as rerata')
            ->join('mapel m', 'm.id = ps.mapel_id', 'left')
            ->join('(' . $subRerataQuery . ') latest', 'latest.mapel_id = ps.mapel_id AND latest.max_created = ps.created_at')
            ->where('ps.rombel_id', $rombelId)
            ->groupBy('m.nama')
            ->get()
            ->getResultArray();

        foreach ($rr as $r) {
            $rerataByMapel[$r['mapel']] = $r['rerata'];
        }

        $mapelGroups = [];
        $groupIdx = 0;
        $prevKelompok = null;
        $prevGroupUrutan = null;

        foreach ($allMapel as $m) {
            $kelompok = $m['kelompok'] ?? 'Lainnya';
            $groupUrutan = (int)($m['group_urutan'] ?? 1);
            $urutan = (int)($m['urutan'] ?? 0);

            $isNewGroup = ($kelompok !== $prevKelompok)
                       || ($prevKelompok !== null && $groupUrutan !== $prevGroupUrutan);

            if ($isNewGroup) {
                $groupIdx++;
                $mapelGroups[$groupIdx] = [
                    'nama'   => $kelompok,
                    'mapels' => [],
                ];
            }

            $n = $nilaiByMapel[$m['nama']] ?? null;
            $nilai = $n['nilai'] ?? ($n['nilai_p'] ?? '—');
            $rerata = $rerataByMapel[$m['nama']] ?? '—';
            $rerataRounded = $rerata !== '—' ? round((float)$rerata) : '—';

            $mapelGroups[$groupIdx]['mapels'][] = [
                'nama'            => $m['nama'],
                'kkm'             => $m['kkm'] ?? 70,
                'nilai_p'         => $nilai,
                'predikat_p'      => predikatNilai($nilai),
                'rerata'          => $rerataRounded,
                'rerata_predikat' => predikatNilai($rerataRounded),
                'urutan'          => $urutan,
            ];

            $prevKelompok = $kelompok;
            $prevGroupUrutan = $groupUrutan;
        }

        $presensi = $presensiModel->select("
                SUM(CASE WHEN status = 'hadir' THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN status = 'sakit' THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN status = 'izin' THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN status = 'alpha' THEN 1 ELSE 0 END) as alpha,
                COUNT(*) as total
            ")
            ->where('siswa_id', $siswaId)
            ->where('rombel_id', $rombelId)
            ->first();

        $nilaiValues = [];
        foreach ($mapelGroups as $group) {
            foreach ($group['mapels'] as $m) {
                if ($m['nilai_p'] !== '—' && $m['nilai_p'] !== null) {
                    $nilaiValues[] = (int)$m['nilai_p'];
                }
            }
        }
        $rataSiswa = !empty($nilaiValues) ? round(array_sum($nilaiValues) / count($nilaiValues)) : '—';

        $allSiswaInRombel = $srModelLocal->where('rombel_id', $rombelId)->findAll();
        $allSiswaIds = array_column($allSiswaInRombel, 'siswa_id');
        $totalSiswa = count($allSiswaIds);

        $peringkat = null;
        if ($totalSiswa > 0) {
            $subAvg = $db->table('progres_santri')
                ->select('siswa_id, mapel_id, MAX(created_at) as max_created')
                ->whereIn('siswa_id', $allSiswaIds)
                ->groupBy('siswa_id, mapel_id');
            $subAvgQuery = $subAvg->getCompiledSelect();

            $studentAvgs = $db->table('progres_santri ps')
                ->select('ps.siswa_id, AVG(ps.nilai) as avg_nilai')
                ->join('(' . $subAvgQuery . ') latest',
                    'latest.siswa_id = ps.siswa_id AND latest.mapel_id = ps.mapel_id AND latest.max_created = ps.created_at')
                ->whereIn('ps.siswa_id', $allSiswaIds)
                ->groupBy('ps.siswa_id')
                ->orderBy('avg_nilai', 'DESC')
                ->get()
                ->getResultArray();

            $ranking = 1;
            foreach ($studentAvgs as $sa) {
                if ((int)$sa['siswa_id'] === $siswaId) {
                    $peringkat = $ranking;
                    break;
                }
                $ranking++;
            }
        }

        $bulan = (int)date('n');
        $semester = ($bulan >= 7 && $bulan <= 12) ? 'Ganjil' : 'Genap';

        $walasUser = $rombel['walas_id'] ? model('App\Models\UserModel')->find($rombel['walas_id']) : null;

        return [
            'logo_path'        => ROOTPATH . 'public/icons/icon_mti.png',
            'siswa'            => $siswa,
            'rombel'           => $rombel,
            'tahun_ajar'       => $rombel['tahun_ajar_nama'] ?? $rombel['tahun_ajar'] ?? '',
            'semester'         => $semester,
            'mapelGroups'      => $mapelGroups,
            'presensi'         => $presensi,
            'tempat_tanggal'   => 'Candung, ' . date('d F Y'),
            'nama_walas'       => $walasUser['nama'] ?? '—',
            'walas_nuptk'      => $walasUser['nip'] ?? '',
            'raisul_nama'      => 'Drs. H. Anas Khatib Bandaro, MM',
            'raisul_nip'       => 'NIP: ................................',
            'rata_siswa'       => $rataSiswa,
            'peringkat'        => $peringkat,
            'total_siswa'      => $totalSiswa,
        ];
    }
    public function index()
    {
        $userId = session()->get('userId');
        $role   = session()->get('role');
        $rombelModel = new RombelModel();
        $taModel = new TahunAjarModel();
        $tahunAktif = $taModel->getActive();
        $selectedTa = $this->request->getGet('tahun_ajar_id') ?: ($tahunAktif['id'] ?? null);

        $rombelQuery = $rombelModel->select('rombel.*, tahun_ajar.tahun as tahun_ajar_nama')
                       ->join('tahun_ajar', 'tahun_ajar.id = rombel.tahun_ajar_id', 'left')
                       ->where('rombel.is_active', 1);
        if ($role !== 'admin') {
            $rombelQuery->where('rombel.walas_id', $userId);
        }
        if ($selectedTa) {
            $rombelQuery->where('rombel.tahun_ajar_id', $selectedTa);
        }
        $rombelSaya = $rombelQuery->findAll();

        $prefix = $role === 'admin' ? 'admin' : 'walas';

        return $this->render('walas/cetak', [
            'title'      => 'Cetak Rapor',
            'rombelSaya' => $rombelSaya,
            'selectedTa' => $selectedTa,
            'tahunAjar'  => $taModel->orderBy('tahun', 'DESC')->findAll(),
            'rolePrefix' => $prefix,
        ]);
    }

    public function rombel(int $rombelId)
    {
        $userId = session()->get('userId');
        $role   = session()->get('role');
        $rombelModel = new RombelModel();
        $srModel = new SiswaRombelModel();

        $rombelQuery = $rombelModel->select('rombel.*, tahun_ajar.tahun as tahun_ajar_nama')
                       ->join('tahun_ajar', 'tahun_ajar.id = rombel.tahun_ajar_id', 'left')
                       ->where('rombel.is_active', 1);
        if ($role !== 'admin') {
            $rombelQuery->where('rombel.walas_id', $userId);
        }
        $rombel = $rombelQuery->find($rombelId);

        if (!$rombel) {
            $prefix = $role === 'admin' ? 'admin' : 'walas';
            return redirect()->to($prefix . '/cetak')->with('error', 'Akses ditolak.');
        }

        $siswaList = $srModel->getSiswaByRombel($rombelId);
        $prefix = $role === 'admin' ? 'admin' : 'walas';

        return $this->render('walas/cetak_rombel', [
            'title'      => 'Cetak Rapor - ' . esc($rombel['nama']),
            'rombel'     => $rombel,
            'siswaList'  => $siswaList,
            'rolePrefix' => $prefix,
        ]);
    }

    public function excel(int $rombelId, int $siswaId)
    {
        $role = session()->get('role');
        $prefix = $role === 'admin' ? 'admin' : 'walas';

        $data = $this->_prepareData($rombelId, $siswaId);
        if (empty($data)) {
            return redirect()->to($prefix . '/cetak')->with('error', 'Akses ditolak.');
        }

        $templatePath = ROOTPATH . 'data/rapor/format_rapor.xlsx';
        $helper = new \App\Helpers\RaporExcelTemplate($templatePath);
        return $helper->generate($data);
    }
}
    