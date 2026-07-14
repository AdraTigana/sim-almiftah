<?php

namespace App\Controllers\WaliKelas;

use App\Controllers\BaseController;
use App\Models\SiswaRombelModel;
use App\Models\RombelModel;
use App\Models\ProgresSantriModel;
use App\Models\PresensiModel;
use App\Models\TahunAjarModel;
use App\Models\MapelModel;

class Rapor extends BaseController
{
    public function index()
    {
        $userId = session()->get('userId');
        $rombelModel = new RombelModel();
        $taModel = new TahunAjarModel();

        $selectedTa = $this->request->getGet('tahun_ajar_id');
        $tahunAktif = $taModel->getActive();

        $rombelQuery = $rombelModel->select('rombel.*, tahun_ajar.tahun as tahun_ajar_nama')
                 ->join('tahun_ajar', 'tahun_ajar.id = rombel.tahun_ajar_id', 'left')
                 ->where('rombel.walas_id', $userId)
                 ->where('rombel.is_active', 1);
        $taFilter = $selectedTa ?: ($tahunAktif['id'] ?? null);
        if ($taFilter) {
            $rombelQuery->where('rombel.tahun_ajar_id', $taFilter);
        }
        $rombel = $rombelQuery->findAll();

        return $this->render('walas/rapor', [
            'title'      => 'Rapor Kelas',
            'rombel'     => $rombel,
            'tahunAjar'  => $taModel->orderBy('tahun', 'DESC')->findAll(),
            'selectedTa' => $selectedTa,
        ]);
    }

    public function kelas(int $rombelId)
    {
        $userId = session()->get('userId');
        $srModel = new SiswaRombelModel();
        $rombelModel = new RombelModel();
        $progresModel = new ProgresSantriModel();
        $mapelModel = new MapelModel();

        $rombel = $rombelModel->select('rombel.*, tahun_ajar.tahun as tahun_ajar_nama')
                  ->join('tahun_ajar', 'tahun_ajar.id = rombel.tahun_ajar_id', 'left')
                  ->where('rombel.walas_id', $userId)
                  ->find($rombelId);

        if (!$rombel) {
            return redirect()->to('walas/rapor')->with('error', 'Akses ditolak atau rombel tidak ditemukan.');
        }

        $siswa = $srModel->getSiswaByRombel($rombelId);
        $allMapel = $mapelModel->where('is_active', 1)->orderBy('kelompok')->orderBy('urutan')->findAll();
        $presensiModel = new PresensiModel();
        $db = \Config\Database::connect();

        // Hitung rerata kelas per mapel
        $rerataByMapel = [];
        $siswaIds = array_column($siswa, 'siswa_id');

        $subRerata = $db->table('progres_santri')
            ->select('mapel_id, MAX(created_at) as max_created')
            ->whereIn('siswa_id', $siswaIds)
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

        $dataSiswa = [];
        foreach ($siswa as $s) {
            // Ambil progres per mapel (latest per mapel, all kategori)
            $subLatest = $db->table('progres_santri')
                ->select('mapel_id, MAX(created_at) as max_created')
                ->where('siswa_id', $s['siswa_id'])
                ->groupBy('mapel_id');
            $subLatestQuery = $subLatest->getCompiledSelect();

            $rows = $db->table('progres_santri p')
                ->select('m.nama as mapel, p.*')
                ->join('mapel m', 'm.id = p.mapel_id', 'left')
                ->join('(' . $subLatestQuery . ') latest', 'latest.mapel_id = p.mapel_id AND latest.max_created = p.created_at')
                ->where('p.siswa_id', $s['siswa_id'])
                ->orderBy('m.nama')
                ->get()
                ->getResultArray();

            $nilaiByMapel = [];
            foreach ($rows as $n) {
                $nilaiByMapel[$n['mapel']] = $n;
            }

            // Presensi summary per siswa (all time for this rombel)
            $presensi = $presensiModel->select("
                    SUM(CASE WHEN status = 'hadir' THEN 1 ELSE 0 END) as hadir,
                    SUM(CASE WHEN status = 'sakit' THEN 1 ELSE 0 END) as sakit,
                    SUM(CASE WHEN status = 'izin' THEN 1 ELSE 0 END) as izin,
                    SUM(CASE WHEN status = 'alpha' THEN 1 ELSE 0 END) as alpha,
                    COUNT(*) as total
                ")
                ->where('siswa_id', $s['siswa_id'])
                ->where('rombel_id', $rombelId)
                ->first();

            $dataSiswa[] = [
                'nis'           => $s['nis'],
                'nama'          => $s['nama'],
                'siswa_id'      => $s['siswa_id'],
                'nilai'         => $rows,
                'nilaiByMapel'  => $nilaiByMapel,
                'presensi'      => $presensi,
                'rerataByMapel' => $rerataByMapel,
            ];
        }

        return $this->render('walas/rapor_kelas', [
            'title'     => 'Rapor - ' . ($rombel['nama'] ?? ''),
            'rombel'    => $rombel,
            'dataSiswa' => $dataSiswa,
            'allMapel'  => $allMapel,
        ]);
    }

    public function siswa(int $rombelId, int $siswaId)
    {
        $userId = session()->get('userId');
        $rombelModel = new RombelModel();
        $mapelModel = new MapelModel();
        $presensiModel = new PresensiModel();
        $db = \Config\Database::connect();

        $rombel = $rombelModel->select('rombel.*, tahun_ajar.tahun as tahun_ajar_nama')
                  ->join('tahun_ajar', 'tahun_ajar.id = rombel.tahun_ajar_id', 'left')
                  ->where('rombel.walas_id', $userId)
                  ->find($rombelId);

        if (!$rombel) {
            return redirect()->to('walas/rapor')->with('error', 'Akses ditolak.');
        }

        $siswaModel = new \App\Models\SiswaModel();
        $siswa = $siswaModel->find($siswaId);
        if (!$siswa) {
            return redirect()->to('walas/rapor/kelas/' . $rombelId)->with('error', 'Santri tidak ditemukan.');
        }

        $allMapel = $mapelModel->where('is_active', 1)->orderBy('kelompok')->orderBy('urutan')->findAll();

        // Progres per mapel (latest per mapel)
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
            ->orderBy('m.nama')
            ->get()
            ->getResultArray();

        $nilaiByMapel = [];
        foreach ($rows as $n) {
            $nilaiByMapel[$n['mapel']] = $n;
        }

        // Hitung rerata kelas per mapel
        $srModel = new \App\Models\SiswaRombelModel();
        $siswaIds = $srModel->where('rombel_id', $rombelId)->findAll();
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

        $rerataByMapel = [];
        foreach ($rr as $r) {
            $rerataByMapel[$r['mapel']] = $r['rerata'];
        }

        // Presensi
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

        $walasUser = $rombel['walas_id'] ? model('App\Models\UserModel')->find($rombel['walas_id']) : null;

        return $this->render('walas/rapor_siswa', [
            'title'        => 'Rapor - ' . $siswa['nama'],
            'rombel'       => $rombel,
            'siswa'        => $siswa,
            'allMapel'     => $allMapel,
            'nilaiByMapel' => $nilaiByMapel,
            'rerataByMapel'=> $rerataByMapel,
            'presensi'     => $presensi,
            'namaWalas'    => $walasUser['nama'] ?? '—',
        ]);
    }
}
