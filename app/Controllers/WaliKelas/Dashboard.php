<?php

namespace App\Controllers\WaliKelas;

use App\Controllers\BaseController;
use App\Models\ProgresSantriModel;
use App\Models\PresensiModel;
use App\Models\RombelModel;
use App\Models\SiswaRombelModel;
use App\Models\TahunAjarModel;
use App\Models\MapelModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $userId = session()->get('userId');
        $tahunAjarModel = new TahunAjarModel();
        $rombelModel = new RombelModel();
        $srModel = new SiswaRombelModel();
        $mapelModel = new MapelModel();
        $db = \Config\Database::connect();

        $tahunAktif = $tahunAjarModel->getActive();

        $rombelQuery = $rombelModel->where('walas_id', $userId)->where('is_active', 1);
        if ($tahunAktif && isset($tahunAktif['id'])) {
            $rombelQuery->where('tahun_ajar_id', $tahunAktif['id']);
        }
        $rombelSaya = $rombelQuery->findAll();

        $totalSantri = 0;
        $rombelData = [];
        $siswaIds = [];
        $rombelIds = [];

        foreach ($rombelSaya as $r) {
            $anggota = $srModel->getSiswaByRombel($r['id']);
            $totalSantri += count($anggota);
            $ids = array_column($anggota, 'siswa_id');
            $siswaIds = array_merge($siswaIds, $ids);
            $rombelIds[] = $r['id'];
            $rombelData[] = [
                'id'      => $r['id'],
                'nama'    => $r['nama'],
                'jumlah'  => count($anggota),
                'anggota' => $anggota,
            ];
        }

        // === Lulus / Belum Tuntas ===
        $totalLulus = 0;
        $totalBelum = 0;
        if (!empty($siswaIds)) {
            $lulusIds = (new ProgresSantriModel())
                ->select('DISTINCT(siswa_id) as siswa_id')
                ->whereIn('siswa_id', $siswaIds)
                ->where('nilai >=', 70)
                ->findAll();
            $totalLulus = count($lulusIds);
            $totalBelum = count($siswaIds) - $totalLulus;
        }

        // === Progres Terakhir ===
        $progresTerakhir = [];
        if (!empty($siswaIds)) {
            $progresTerakhir = (new ProgresSantriModel())
                ->select('progres_santri.*, siswa.nama as nama_siswa, mapel.id as mapel_id, mapel.nama as mapel, kategori.id as kategori_id, kategori.nama as kategori, kategori.urutan as kategori_urutan')
                ->join('siswa', 'siswa.id = progres_santri.siswa_id')
                ->join('mapel', 'mapel.id = progres_santri.mapel_id', 'left')
                ->join('kategori', 'kategori.id = progres_santri.kategori_id', 'left')
                ->whereIn('progres_santri.siswa_id', $siswaIds)
                ->orderBy('progres_santri.created_at', 'DESC')
                ->limit(8)
                ->findAll();
            foreach ($progresTerakhir as &$p) {
                $p['waktu'] = timeAgo($p['created_at']);
                $p['predikat'] = $this->_predikatLabel($p['nilai']);
                $urutan = (int)$p['kategori_urutan'];
                $kategoriNama = $p['kategori'] ?? '';
                $p['kategori'] = kategoriDisplayName(['urutan' => $urutan, 'nama' => $kategoriNama], $p['mapel_id']);
            }
        }

        // === 1. Santri di Bawah KKM ===
        $santriBawahKkm = [];
        if (!empty($rombelIds)) {
            $allMapel = $mapelModel->where('is_active', 1)->orderBy('kelompok')->orderBy('urutan')->findAll();
            $mapelKkm = [];
            foreach ($allMapel as $m) {
                $mapelKkm[$m['id']] = (int)($m['kkm'] ?? 70);
            }

            foreach ($rombelSaya as $r) {
                $anggota = $srModel->getSiswaByRombel($r['id']);
                $idsRombel = array_column($anggota, 'siswa_id');
                if (empty($idsRombel)) continue;

                $sub = $db->table('progres_santri')
                    ->select('mapel_id, siswa_id, MAX(created_at) as max_created')
                    ->whereIn('siswa_id', $idsRombel)
                    ->groupBy('mapel_id, siswa_id');
                $subQ = $sub->getCompiledSelect();

                $latest = $db->table('progres_santri p')
                    ->select('p.siswa_id, p.mapel_id, p.nilai, m.nama as mapel_nama, s.nama as siswa_nama')
                    ->join('(' . $subQ . ') lx', 'lx.siswa_id = p.siswa_id AND lx.mapel_id = p.mapel_id AND lx.max_created = p.created_at')
                    ->join('mapel m', 'm.id = p.mapel_id')
                    ->join('siswa s', 's.id = p.siswa_id')
                    ->whereIn('p.siswa_id', $idsRombel)
                    ->get()
                    ->getResultArray();

                foreach ($latest as $l) {
                    $kkm = $mapelKkm[$l['mapel_id']] ?? 70;
                    if ($l['nilai'] !== null && (int)$l['nilai'] < $kkm) {
                        $santriBawahKkm[] = [
                            'siswa_nama' => $l['siswa_nama'],
                            'mapel_nama' => $l['mapel_nama'],
                            'nilai'      => (int)$l['nilai'],
                            'kkm'        => $kkm,
                            'rombel'     => $r['nama'],
                        ];
                    }
                }
            }

            // Limit to 10 most critical
            usort($santriBawahKkm, fn($a, $b) => $a['nilai'] <=> $b['nilai']);
            $santriBawahKkm = array_slice($santriBawahKkm, 0, 10);
        }

        // === 2. Rata-rata Nilai per Rombel ===
        $rataRombel = [];
        if (!empty($rombelIds)) {
            foreach ($rombelSaya as $r) {
                $anggota = $srModel->getSiswaByRombel($r['id']);
                $idsRombel = array_column($anggota, 'siswa_id');
                if (empty($idsRombel)) {
                    $rataRombel[] = ['nama' => $r['nama'], 'rata' => '—', 'total_mapel' => 0];
                    continue;
                }

                $sub2 = $db->table('progres_santri')
                    ->select('mapel_id, siswa_id, MAX(created_at) as max_created')
                    ->whereIn('siswa_id', $idsRombel)
                    ->groupBy('mapel_id, siswa_id');
                $subQ2 = $sub2->getCompiledSelect();

                $rata = $db->table('progres_santri p')
                    ->select('AVG(p.nilai) as avg_nilai')
                    ->join('(' . $subQ2 . ') lx', 'lx.siswa_id = p.siswa_id AND lx.mapel_id = p.mapel_id AND lx.max_created = p.created_at')
                    ->whereIn('p.siswa_id', $idsRombel)
                    ->get()
                    ->getRow();

                $countMapel = $db->table('progres_santri p')
                    ->select('COUNT(DISTINCT p.mapel_id) as jml')
                    ->join('(' . $subQ2 . ') lx', 'lx.siswa_id = p.siswa_id AND lx.mapel_id = p.mapel_id AND lx.max_created = p.created_at')
                    ->whereIn('p.siswa_id', $idsRombel)
                    ->get()
                    ->getRow();

                $rataRombel[] = [
                    'nama'        => $r['nama'],
                    'rata'        => $rata && $rata->avg_nilai ? round((float)$rata->avg_nilai, 1) : '—',
                    'total_mapel' => (int)($countMapel->jml ?? 0),
                ];
            }
        }

        // === 3. 5 Santri Alpha Tertinggi ===
        $topAlpha = [];
        if (!empty($siswaIds)) {
            $alphaRows = (new PresensiModel())
                ->select("
                    siswa_id,
                    SUM(CASE WHEN status = 'alpha' THEN 1 ELSE 0 END) as total_alpha,
                    COUNT(*) as total_presensi
                ")
                ->whereIn('siswa_id', $siswaIds)
                ->groupBy('siswa_id')
                ->orderBy('total_alpha', 'DESC')
                ->limit(5)
                ->findAll();

            if (!empty($alphaRows)) {
                $siswaModel = new \App\Models\SiswaModel();
                foreach ($alphaRows as $a) {
                    $s = $siswaModel->find($a['siswa_id']);
                    if ($s) {
                        $topAlpha[] = [
                            'nama'     => $s['nama'],
                            'alpha'    => (int)$a['total_alpha'],
                            'total'    => (int)$a['total_presensi'],
                        ];
                    }
                }
            }
        }

        return $this->render('walas/dashboard', [
            'title'           => 'Dashboard Wali Kelas',
            'tahunAktif'      => $tahunAktif['tahun'] ?? '—',
            'rombelSaya'      => $rombelData,
            'totalSantri'     => $totalSantri,
            'totalLulus'      => $totalLulus,
            'totalBelum'      => $totalBelum,
            'progresTerakhir' => $progresTerakhir,
            'santriBawahKkm'  => $santriBawahKkm,
            'rataRombel'      => $rataRombel,
            'topAlpha'        => $topAlpha,
        ]);
    }

    private function _predikatLabel($nilai): string
    {
        if ($nilai === null) return '—';
        if ($nilai >= 90) return 'A';
        if ($nilai >= 80) return 'B';
        if ($nilai >= 70) return 'C';
        return 'D';
    }
}
