<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\GuruMapelModel;
use App\Models\SiswaRombelModel;
use App\Models\KategoriModel;
use App\Models\KriteriaPenilaianModel;
use App\Models\ProgresSantriModel;
use App\Models\RombelModel;

class Nilai extends BaseController
{
    public function index()
    {
        return redirect()->to('guru/input-saya');
    }

    // Halaman 2: Daftar santri dengan rekap nilai per kategori
    public function mapel(int $mapelId, int $rombelId): string
    {
        $mapelModel = new \App\Models\MapelModel();
        $rombelModel = new RombelModel();
        $srModel = new SiswaRombelModel();
        $progresModel = new ProgresSantriModel();
        $kategoriModel = new KategoriModel();

        $mapel = $mapelModel->find($mapelId);
        $rombel = $rombelModel->find($rombelId);
        $siswa = $srModel->getSiswaByRombel($rombelId);
        $kategoriList = $kategoriModel->where('mapel_id', $mapelId)->orderBy('urutan', 'ASC')->findAll();

        if (!$mapel || !$rombel) {
            return redirect()->to('guru/input-saya')->with('error', 'Data tidak ditemukan.');
        }

        $displayNameByKategoriId = kategoriDisplayNameMap($kategoriList, $mapel['id']);

        // Build rekap data per siswa
        $siswaIds = array_column($siswa, 'siswa_id');
        $kategoriIds = array_column($kategoriList, 'id');

        $allProgres = [];
        if (!empty($siswaIds) && !empty($kategoriIds)) {
            $allProgres = $progresModel
                ->whereIn('siswa_id', $siswaIds)
                ->where('mapel_id', $mapelId)
                ->whereIn('kategori_id', $kategoriIds)
                ->orderBy('created_at', 'DESC')
                ->findAll();
        }

        $latestBySiswaKategori = [];
        foreach ($allProgres as $p) {
            $key = $p['siswa_id'] . '_' . $p['kategori_id'];
            if (!isset($latestBySiswaKategori[$key])) {
                $latestBySiswaKategori[$key] = $p;
            }
        }

        $nilaiAkhirRecords = [];
        if (!empty($siswaIds)) {
            $records = $progresModel
                ->whereIn('siswa_id', $siswaIds)
                ->where('mapel_id', $mapelId)
                ->where('kategori_id', null)
                ->orderBy('created_at', 'DESC')
                ->findAll();
            foreach ($records as $r) {
                if (!isset($nilaiAkhirRecords[$r['siswa_id']])) {
                    $nilaiAkhirRecords[$r['siswa_id']] = $r;
                }
            }
        }

        $rekapSiswa = [];
        foreach ($siswa as $s) {
            $sid = $s['siswa_id'];
            $row = [
                'siswa_id'     => $sid,
                'nis'          => $s['nis'],
                'nama'         => $s['nama'],
                'kategoriNilai' => [],
                'nilaiAkhir'   => null,
                'mode'         => null,
            ];

            foreach ($kategoriList as $k) {
                $key = $sid . '_' . $k['id'];
                $row['kategoriNilai'][$k['id']] = isset($latestBySiswaKategori[$key])
                    ? (float)$latestBySiswaKategori[$key]['nilai']
                    : null;
            }

            if (isset($nilaiAkhirRecords[$sid])) {
                $row['nilaiAkhir'] = (float)$nilaiAkhirRecords[$sid]['nilai'];
                $row['mode'] = $nilaiAkhirRecords[$sid]['catatan'] ?? null;
            }

            $row['predikat'] = [
                'label' => predikatLabel($row['nilaiAkhir']),
                'class' => predikatClass($row['nilaiAkhir']),
            ];

            $rekapSiswa[] = $row;
        }

        // Presensi data
        $tab = $this->request->getGet('tab') ?? 'nilai';
        $tanggal = $this->request->getGet('tanggal') ?? date('Y-m-d');
        $presensiModel = new \App\Models\PresensiModel();
        $presensiHariIni = $presensiModel
            ->where('tanggal', $tanggal)
            ->where('rombel_id', $rombelId)
            ->where('mapel_id', $mapelId)
            ->findAll();
        $presensiHariIni = array_column($presensiHariIni, null, 'siswa_id');

        // Rekap presensi — semua tanggal untuk matrix
        $allPresensi = $presensiModel
            ->where('rombel_id', $rombelId)
            ->where('mapel_id', $mapelId)
            ->orderBy('tanggal', 'ASC')
            ->findAll();

        $presensiDates = array_unique(array_column($allPresensi, 'tanggal'));
        sort($presensiDates);

        $presensiMatrix = [];
        foreach ($allPresensi as $pr) {
            $presensiMatrix[$pr['siswa_id']][$pr['tanggal']] = [
                'status'     => $pr['status'],
                'keterangan' => $pr['keterangan'],
            ];
        }

        return $this->render('guru/nilai_mapel', [
            'title'                => 'Nilai - ' . ($mapel['nama'] ?? ''),
            'mapel'                => $mapel,
            'rombel'               => $rombel,
            'siswa'                => $siswa,
            'kategoriList'         => $kategoriList,
            'displayNameByKategoriId' => $displayNameByKategoriId,
            'rekapSiswa'           => $rekapSiswa,
            'tab'                  => $tab,
            'presensiHariIni'      => $presensiHariIni,
            'tanggal'              => $tanggal,
            'presensiDates'        => $presensiDates,
            'presensiMatrix'       => $presensiMatrix,
        ]);
    }

    // Halaman 3: Shell + tab kategori, konten form per kategori via AJAX
    public function siswa(int $siswaId, int $mapelId, int $rombelId): string
    {
        $mapelModel = new \App\Models\MapelModel();
        $rombelModel = new RombelModel();
        $siswaModel = new \App\Models\SiswaModel();
        $kategoriModel = new KategoriModel();
        $kriteriaModel = new KriteriaPenilaianModel();
        $progresModel = new ProgresSantriModel();

        $mapel = $mapelModel->find($mapelId);
        $rombel = $rombelModel->find($rombelId);
        $siswa = $siswaModel->find($siswaId);
        $kategoriList = $kategoriModel->where('mapel_id', $mapelId)->orderBy('urutan', 'ASC')->findAll();

        $isTasmi = isMapelTasmi($mapel['id']);
        $displayNameByKategoriId = kategoriDisplayNameMap($kategoriList, $mapel['id']);

        // Tentukan tab aktif dari URL, fallback ke kategori pertama
        $activeKategoriId = $this->request->getGet('kategori');
        if (!$activeKategoriId && !empty($kategoriList)) {
            $activeKategoriId = $kategoriList[0]['id'];
        }

        // Cari kategori yang sesuai dengan active, fallback ke pertama
        $activeKategori = null;
        if (!empty($kategoriList)) {
            foreach ($kategoriList as $k) {
                if ($k['id'] == $activeKategoriId) {
                    $activeKategori = $k;
                    break;
                }
            }
            if (!$activeKategori && $activeKategoriId !== 'akhir') {
                $activeKategori = $kategoriList[0];
                $activeKategoriId = $activeKategori['id'];
            }
        }

        // Load data untuk tab aktif
        $firstTab = null;
        if ($activeKategori) {
            $kriteria = $kriteriaModel->getByMapelAndKategori($mapelId, $activeKategori['id']);
            $progres = $progresModel
                ->where('siswa_id', $siswaId)
                ->where('mapel_id', $mapelId)
                ->where('kategori_id', $activeKategori['id'])
                ->orderBy('created_at', 'DESC')
                ->findAll();
            $kriteriaData = [];
            if (!empty($progres) && !empty($progres[0]['kriteria_data'])) {
                $decoded = json_decode($progres[0]['kriteria_data'], true);
                if (is_array($decoded)) {
                    $kriteriaData = $decoded;
                }
            }
            $firstTab = [
                'kategori'     => $activeKategori,
                'kriteria'     => $kriteria,
                'kriteriaData' => $kriteriaData,
                'progres'      => $progres,
            ];
        }

        // Recap data untuk Nilai Akhir
        $recapData = [];
        $allProgres = $progresModel
            ->where('siswa_id', $siswaId)
            ->where('mapel_id', $mapelId)
            ->where('kategori_id IS NOT NULL')
            ->findAll();
        $progresByKategori = [];
        foreach ($allProgres as $p) {
            if (!isset($progresByKategori[$p['kategori_id']])) {
                $progresByKategori[$p['kategori_id']] = $p;
            }
        }
        foreach ($kategoriList as $k) {
            $kid = $k['id'];
            $p = $progresByKategori[$kid] ?? null;
            $nama = $displayNameByKategoriId[$kid] ?? $k['nama'];
            $kd = ($p && !empty($p['kriteria_data'])) ? json_decode($p['kriteria_data'], true) : [];
            $nilai = $p ? (float)$p['nilai'] : null;

            $entry = ['kategori_id' => $kid, 'nama' => $nama, 'nilai' => $nilai, 'detail' => []];

            if ($p) {
                foreach ($kd as $kData) {
                    if (isset($kData['nilai'])) {
                        $entry['detail'][] = ['kriteria' => $kData['_label'] ?? '', 'nilai' => (int)$kData['nilai']];
                    }
                    if (!empty($kData['selesai'])) {
                        $entry['detail'][] = ['kriteria' => $kData['_label'] ?? '', 'nilai' => 'Selesai'];
                    }
                }
                if (strpos($nama, 'Ujian') !== false && !empty($kd)) {
                    $kValues = [];
                    foreach ($kd as $kData) {
                        if (isset($kData['nilai'])) {
                            $kValues[] = (int)$kData['nilai'];
                        }
                    }
                    if (!empty($kValues)) {
                        $entry['rataKomponen'] = round(array_sum($kValues) / count($kValues), 1);
                    }
                }
            }

            $recapData[] = $entry;
        }

        // Nilai akhir (kategori_id IS NULL)
        $nilaiAkhir = $progresModel
            ->where('siswa_id', $siswaId)
            ->where('mapel_id', $mapelId)
            ->where('kategori_id', null)
            ->orderBy('created_at', 'DESC')
            ->first();

        // Auto grade = rata-rata nilai dari semua kategori
        $autoGrade = null;
        $allNilai = $progresModel
            ->select('nilai')
            ->where('siswa_id', $siswaId)
            ->where('mapel_id', $mapelId)
            ->where('kategori_id IS NOT NULL')
            ->where('nilai IS NOT NULL')
            ->findAll();
        $nilaiValues = array_column($allNilai, 'nilai');
        if (!empty($nilaiValues)) {
            $autoGrade = round(array_sum($nilaiValues) / count($nilaiValues));
        }

        return $this->render('guru/nilai_siswa', [
            'title'                 => 'Nilai - ' . ($siswa['nama'] ?? ''),
            'mapel'                 => $mapel,
            'rombel'                => $rombel,
            'siswa'                 => $siswa,
            'kategoriList'          => $kategoriList,
            'displayNameByKategoriId' => $displayNameByKategoriId,
            'activeKategoriId'      => $activeKategoriId,
            'firstTab'              => $firstTab,
            'nilaiAkhir'            => $nilaiAkhir,
            'autoGrade'             => $autoGrade,
            'recapData'             => $recapData,
            'isTasmi'               => $isTasmi,
        ]);
    }

    // AJAX: Partial form grade untuk satu kategori
    public function formKategori(int $siswaId, int $mapelId, int $rombelId, int $kategoriId): string
    {
        $mapelModel = new \App\Models\MapelModel();
        $rombelModel = new RombelModel();
        $siswaModel = new \App\Models\SiswaModel();
        $kategoriModel = new KategoriModel();
        $kriteriaModel = new KriteriaPenilaianModel();
        $progresModel = new ProgresSantriModel();

        $mapel = $mapelModel->find($mapelId);
        $rombel = $rombelModel->find($rombelId);
        $siswa = $siswaModel->find($siswaId);
        $kategori = $kategoriModel->find($kategoriId);
        $kriteria = $kriteriaModel->getByMapelAndKategori($mapelId, $kategoriId);

        $progres = $progresModel
            ->where('siswa_id', $siswaId)
            ->where('mapel_id', $mapelId)
            ->where('kategori_id', $kategoriId)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        $kriteriaData = [];
        if (!empty($progres) && !empty($progres[0]['kriteria_data'])) {
            $decoded = json_decode($progres[0]['kriteria_data'], true);
            if (is_array($decoded)) {
                $kriteriaData = $decoded;
            }
        }

        $mapelNama = $mapel['nama'] ?? '';
        $displayName = kategoriDisplayName($kategori, $mapel['id']);

        return view('guru/nilai_siswa_form', [
            'mapel'        => $mapel,
            'rombel'       => $rombel,
            'siswa'        => $siswa,
            'kategori'     => $kategori,
            'displayName'  => $displayName,
            'kriteria'     => $kriteria,
            'kriteriaData' => $kriteriaData,
            'progres'      => $progres,
        ]);
    }

    // AJAX: Get siswa by rombel
    public function getSiswa()
    {
        $rombelId = $this->request->getGet('rombel_id');
        $model = new SiswaRombelModel();
        $siswa = $model->getSiswaByRombel($rombelId);
        return $this->response->setJSON($siswa);
    }

    // AJAX: Get kategori by mapel
    public function getKategori()
    {
        $mapelId = $this->request->getGet('mapel_id');
        $model = new KategoriModel();
        $kategori = $model->getByMapel($mapelId);
        return $this->response->setJSON($kategori);
    }

    // AJAX: Get kriteria by mapel (opsional kategori_id)
    public function getKriteria()
    {
        $mapelId = $this->request->getGet('mapel_id');
        $kategoriId = $this->request->getGet('kategori_id');
        $model = new KriteriaPenilaianModel();
        $kriteria = $kategoriId ? $model->getByMapelAndKategori($mapelId, $kategoriId) : $model->getByMapel($mapelId);
        return $this->response->setJSON($kriteria);
    }

    // AJAX: Get detail kategori
    public function getDetailKategori()
    {
        $kategoriId = $this->request->getGet('kategori_id');
        $model = new DetailKategoriModel();
        $detail = $model->getByKategori($kategoriId);
        return $this->response->setJSON($detail);
    }

    private function _canAccess(int $userId, ?int $mapelId, ?int $rombelId): bool
    {
        if (!$mapelId) return false;
        if (!$rombelId) return false;
        $guruMapelModel = new \App\Models\GuruMapelModel();
        $rombelModel = new \App\Models\RombelModel();
        $rombel = $rombelModel->find($rombelId);
        if (!$rombel) return false;
        $assignment = $guruMapelModel
            ->where('user_id', $userId)
            ->where('mapel_id', $mapelId)
            ->where('rombel_id', $rombelId)
            ->where('tahun_ajar_id', $rombel['tahun_ajar_id'])
            ->first();
        return $assignment !== null;
    }

    // Save progress
    public function save()
    {
        $post = $this->request->getPost();
        $userId = session()->get('userId');

        $siswaId  = $post['siswa_id'] ?? null;
        $mapelId  = $post['mapel_id'] ?? null;
        $rombelId = $post['rombel_id'] ?? null;
        $kategoriId = $post['kategori_id'] ?? null;

        // Validasi akses guru
        if (!$this->_canAccess($userId, $mapelId, $rombelId)) {
            $msg = 'Anda tidak memiliki akses untuk menilai kelas ini.';
            if ($this->request->isAJAX() || $this->request->hasHeader('X-Offline-Sync')) {
                return $this->response->setJSON(['success' => false, 'message' => $msg]);
            }
            return redirect()->back()->withInput()->with('error', $msg);
        }

        if (empty($siswaId) || empty($mapelId)) {
            $msg = 'Data siswa atau mata pelajaran tidak valid.';
            if ($this->request->isAJAX() || $this->request->hasHeader('X-Offline-Sync')) {
                return $this->response->setJSON(['success' => false, 'message' => $msg]);
            }
            return redirect()->back()->withInput()->with('error', $msg);
        }

        // Gather per-kriteria data
        $kriteriaInput = $post['kriteria'] ?? [];
        $kriteriaModel = new \App\Models\KriteriaPenilaianModel();
        $kriteriaNama = [];
        if (!empty($kriteriaInput)) {
            $kIds = array_keys($kriteriaInput);
            $kList = $kriteriaModel->find($kIds);
            foreach ($kList as $k) {
                $kriteriaNama[$k['id']] = $k['nama'];
            }
        }
        $catatanGabung = $this->_buildCatatanFromKriteria($kriteriaInput, $kriteriaNama, $post['catatan'] ?? '');

        $nilai = $post['nilai'] ?? null;

        $db = \Config\Database::connect();
        $db->transStart();

        $model = new ProgresSantriModel();

        // Hapus progres lama untuk siswa+mapel+kategori yang sama
        $model->where('siswa_id', $siswaId)
              ->where('mapel_id', $mapelId)
              ->where('kategori_id', $kategoriId)
              ->delete();

        $kriteriaData = $this->_buildKriteriaData($kriteriaInput, $kriteriaNama);
        $checkboxTotal = count($kriteriaInput);
        $checkboxChecked = count(array_filter($kriteriaInput, fn($kd) => !empty($kd['selesai'])));
        // Jika semua kriteria checkbox, konversi ke skala 100
        if ($checkboxTotal > 0 && empty(array_filter($kriteriaInput, fn($kd) => isset($kd['nilai'])))) {
            $nilai = $checkboxTotal > 0 ? round(($checkboxChecked / $checkboxTotal) * 100) : null;
        }

        $data = [
            'siswa_id'        => $siswaId,
            'mapel_id'        => $mapelId,
            'kategori_id'     => $kategoriId,
            'user_id'         => $userId,
            'rombel_id'       => $rombelId,
            'nilai'           => ($nilai !== '' && $nilai !== null) ? $nilai : null,
            'predikat'        => $post['predikat'] ?? (($nilai !== '' && $nilai !== null) ? predikatNilai((int)$nilai) : null),
            'catatan'         => $catatanGabung,
            'kriteria_data'   => !empty($kriteriaData) ? json_encode($kriteriaData) : null,
            'local_id'        => $post['local_id'] ?? null,
            'sync_status'     => 'synced',
        ];

        $model->save($data);

        $db->transComplete();
        if ($db->transStatus() === false) {
            $msg = 'Gagal menyimpan nilai. Silakan coba lagi.';
            if ($this->request->isAJAX() || $this->request->hasHeader('X-Offline-Sync')) {
                return $this->response->setJSON(['success' => false, 'message' => $msg]);
            }
            return redirect()->back()->withInput()->with('error', $msg);
        }

        $redirectUrl = 'guru/nilai/siswa/' . $siswaId . '/mapel/' . $mapelId . '/kelas/' . $rombelId;
        if (!empty($kategoriId)) {
            $redirectUrl .= '?kategori=' . $kategoriId;
        }
        if ($this->request->isAJAX() || $this->request->hasHeader('X-Offline-Sync')) {
            return $this->response->setJSON(['success' => true, 'message' => 'Nilai berhasil disimpan.']);
        }
        return redirect()->to($redirectUrl)->with('message', 'Nilai berhasil disimpan.');
    }

    public function saveAkhir()
    {
        $post = $this->request->getPost();
        $userId = session()->get('userId');
        $model = new ProgresSantriModel();

        $siswaId  = $post['siswa_id'] ?? null;
        $mapelId  = $post['mapel_id'] ?? null;
        $rombelId = $post['rombel_id'] ?? null;
        $mode     = $post['mode'] ?? 'auto';

        if (empty($siswaId) || empty($mapelId)) {
            $msg = 'Data siswa atau mata pelajaran tidak valid.';
            if ($this->request->hasHeader('X-Offline-Sync')) {
                return $this->response->setJSON(['success' => false, 'message' => $msg]);
            }
            return redirect()->back()->with('error', $msg);
        }

        // Validasi akses guru
        if (!$this->_canAccess($userId, $mapelId, $rombelId)) {
            $msg = 'Anda tidak memiliki akses untuk menilai kelas ini.';
            if ($this->request->hasHeader('X-Offline-Sync')) {
                return $this->response->setJSON(['success' => false, 'message' => $msg]);
            }
            return redirect()->back()->with('error', $msg);
        }

        if ($mode === 'auto') {
            $db = \Config\Database::connect();
            $db->transStart();

            // Hapus nilai akhir lama
            $model->where('siswa_id', $siswaId)
                ->where('mapel_id', $mapelId)
                ->where('kategori_id', null)
                ->delete();

            // Rata-rata dari semua kategori yang sudah ada progresnya
            $allNilai = $model
                ->select('nilai')
                ->where('siswa_id', $siswaId)
                ->where('mapel_id', $mapelId)
                ->where('kategori_id IS NOT NULL')
                ->where('nilai IS NOT NULL')
                ->findAll();
            $nilaiValues = array_column($allNilai, 'nilai');
            $nilai = !empty($nilaiValues) ? round(array_sum($nilaiValues) / count($nilaiValues)) : null;

            $data = [
                'siswa_id'  => $siswaId,
                'mapel_id'  => $mapelId,
                'rombel_id' => $rombelId,
                'user_id'   => $userId,
                'kategori_id' => null,
                'nilai'     => $nilai,
                'predikat'  => $nilai !== null ? $this->_calculatePredikat((int)$nilai) : null,
                'catatan'   => 'auto',
                'sync_status' => 'synced',
            ];

            $model->save($data);

            $db->transComplete();
            if ($db->transStatus() === false) {
                $msg = 'Gagal menyimpan nilai akhir. Silakan coba lagi.';
                if ($this->request->hasHeader('X-Offline-Sync')) {
                    return $this->response->setJSON(['success' => false, 'message' => $msg]);
                }
                return redirect()->back()->with('error', $msg);
            }

            if ($this->request->hasHeader('X-Offline-Sync')) {
                return $this->response->setJSON(['success' => true, 'message' => 'Nilai akhir berhasil disimpan (Otomatis).']);
            }
            return redirect()->to('guru/nilai/siswa/' . $siswaId . '/mapel/' . $mapelId . '/kelas/' . $rombelId . '?kategori=akhir')
                ->with('message', 'Nilai akhir berhasil disimpan (Otomatis).');
        }

        // === Mode Manual: simpan nilai per kategori ===
        $kategoriNilai = $post['kategori_nilai'] ?? [];
        if (empty($kategoriNilai)) {
            $msg = 'Input nilai per kategori terlebih dahulu.';
            if ($this->request->hasHeader('X-Offline-Sync')) {
                return $this->response->setJSON(['success' => false, 'message' => $msg]);
            }
            return redirect()->back()->with('error', $msg);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // Hapus nilai akhir lama
        $model->where('siswa_id', $siswaId)
            ->where('mapel_id', $mapelId)
            ->where('kategori_id', null)
            ->delete();

        $kategoriModel = new KategoriModel();
        $validKategori = $kategoriModel->where('mapel_id', $mapelId)->where('is_active', 1)->findAll();
        $validIds = array_column($validKategori, 'id');

        $totalNilai = 0;
        $countNilai = 0;

        foreach ($kategoriNilai as $kategoriId => $nilaiStr) {
            $kategoriId = (int)$kategoriId;
            if (!in_array($kategoriId, $validIds)) continue;

            $nilaiVal = ($nilaiStr !== '' && $nilaiStr !== null) ? (float)$nilaiStr : null;

            // Hapus progres lama untuk kategori ini
            $model->where('siswa_id', $siswaId)
                ->where('mapel_id', $mapelId)
                ->where('kategori_id', $kategoriId)
                ->delete();

            if ($nilaiVal !== null) {
                $model->save([
                    'siswa_id'   => $siswaId,
                    'mapel_id'   => $mapelId,
                    'rombel_id'  => $rombelId,
                    'user_id'    => $userId,
                    'kategori_id' => $kategoriId,
                    'nilai'      => $nilaiVal,
                    'predikat'   => $this->_calculatePredikat((int)$nilaiVal),
                    'catatan'    => 'manual input dari akhir',
                    'sync_status' => 'synced',
                ]);
                $totalNilai += $nilaiVal;
                $countNilai++;
            }
        }

        // Simpan nilai akhir = rata-rata input manual per kategori
        $nilaiAkhir = $countNilai > 0 ? round($totalNilai / $countNilai) : null;

        $model->save([
            'siswa_id'   => $siswaId,
            'mapel_id'   => $mapelId,
            'rombel_id'  => $rombelId,
            'user_id'    => $userId,
            'kategori_id' => null,
            'nilai'      => $nilaiAkhir,
            'predikat'   => $nilaiAkhir !== null ? $this->_calculatePredikat((int)$nilaiAkhir) : null,
            'catatan'    => 'manual',
            'sync_status' => 'synced',
        ]);

        $db->transComplete();
        if ($db->transStatus() === false) {
            $msg = 'Gagal menyimpan nilai akhir. Silakan coba lagi.';
            if ($this->request->hasHeader('X-Offline-Sync')) {
                return $this->response->setJSON(['success' => false, 'message' => $msg]);
            }
            return redirect()->back()->with('error', $msg);
        }

        if ($this->request->hasHeader('X-Offline-Sync')) {
            return $this->response->setJSON(['success' => true, 'message' => 'Nilai akhir berhasil disimpan (Manual).']);
        }
        return redirect()->to('guru/nilai/siswa/' . $siswaId . '/mapel/' . $mapelId . '/kelas/' . $rombelId . '?kategori=akhir')
            ->with('message', 'Nilai akhir berhasil disimpan (Manual).');
    }

    private function _upsertKriteria($siswaId, $mapelId, $kategoriId, $rombelId, $kriteriaId, $nilai, $selesai, $catatan, $userId): array
    {
        $model = new ProgresSantriModel();
        $kriteriaModel = new KriteriaPenilaianModel();

        $k = $kriteriaModel->find($kriteriaId);
        $label = $k['nama'] ?? 'Kriteria #' . $kriteriaId;

        $existing = $model
            ->where('siswa_id', $siswaId)
            ->where('mapel_id', $mapelId)
            ->where('kategori_id', $kategoriId)
            ->orderBy('created_at', 'DESC')
            ->first();

        $kriteriaData = [];
        if ($existing && !empty($existing['kriteria_data'])) {
            $kriteriaData = json_decode($existing['kriteria_data'], true) ?: [];
        }

        $entry = ['_label' => $label];
        if ($nilai !== null && $nilai !== '') {
            $entry['nilai'] = (int)$nilai;
        }
        if ($selesai) {
            $entry['selesai'] = true;
        }
        if ($catatan) {
            $entry['catatan'] = $catatan;
        }
        $kriteriaData[$kriteriaId] = $entry;

        $totalNilaiRecalculated = null;
        $hasNumberInput = false;
        $totalBobot = 0;
        $totalWeighted = 0;
        $checkboxTotal = 0;
        $checkboxChecked = 0;

        foreach ($kriteriaData as $kId => $kd) {
            if (isset($kd['nilai'])) {
                $hasNumberInput = true;
                $kInfo = $kriteriaModel->find($kId);
                $bobot = $kInfo['bobot'] ?? 0;
                $skala = $kInfo['skala_max'] ?? 100;
                $totalBobot += $bobot;
                $totalWeighted += ((int)$kd['nilai'] / $skala) * $bobot;
            }
            if (!empty($kd['selesai'])) {
                $checkboxChecked++;
            }
            $checkboxTotal++;
        }

        if ($hasNumberInput && $totalBobot > 0) {
            $totalNilaiRecalculated = round(($totalWeighted / $totalBobot) * 100);
        } elseif ($checkboxTotal > 0 && !$hasNumberInput) {
            $totalNilaiRecalculated = round(($checkboxChecked / $checkboxTotal) * 100);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $model->where('siswa_id', $siswaId)
            ->where('mapel_id', $mapelId)
            ->where('kategori_id', $kategoriId)
            ->delete();

        $data = [
            'siswa_id'      => $siswaId,
            'mapel_id'      => $mapelId,
            'kategori_id'   => $kategoriId,
            'user_id'       => $userId,
            'rombel_id'     => $rombelId,
            'nilai'         => $totalNilaiRecalculated,
            'predikat'      => $totalNilaiRecalculated !== null ? $this->_calculatePredikat($totalNilaiRecalculated) : null,
            'kriteria_data' => json_encode($kriteriaData),
            'sync_status'   => 'synced',
        ];

        $model->save($data);

        $db->transComplete();
        $saved = $db->transStatus();

        return [
            'success'  => $saved,
            'nilai'    => $totalNilaiRecalculated,
            'predikat' => $data['predikat'],
        ];
    }

    public function syncBatch()
    {
        $input = $this->request->getJSON(true);
        $items = $input['items'] ?? [];
        if (empty($items)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tidak ada data untuk disinkronkan.']);
        }

        $userId = session()->get('userId');
        $results = [];

        foreach ($items as $item) {
            $results[] = $this->_upsertKriteria(
                $item['siswa_id'] ?? null,
                $item['mapel_id'] ?? null,
                $item['kategori_id'] ?? null,
                $item['rombel_id'] ?? null,
                $item['kriteria_id'] ?? null,
                $item['nilai'] ?? null,
                $item['selesai'] ?? null,
                $item['catatan'] ?? '',
                $userId
            );
        }

        return $this->response->setJSON([
            'success' => true,
            'results' => $results,
        ]);
    }

    private function _buildCatatanFromKriteria(array $kriteriaInput, array $kriteriaNama, string $defaultCatatan = ''): string
    {
        $detailCatatan = [];
        foreach ($kriteriaInput as $kId => $kData) {
            $label = $kriteriaNama[$kId] ?? 'Kriteria #' . $kId;
            $parts = [];
            if (!empty($kData['nilai'])) $parts[] = 'Nilai: ' . (int)$kData['nilai'];
            if (!empty($kData['huruf'])) $parts[] = $kData['huruf'];
            if (!empty($kData['selesai'])) $parts[] = 'Selesai';
            if (!empty($kData['catatan'])) $parts[] = '(' . $kData['catatan'] . ')';
            if (!empty($parts)) {
                $detailCatatan[] = $label . ': ' . implode(' ', $parts);
            }
        }
        return !empty($detailCatatan) ? implode('; ', $detailCatatan) : $defaultCatatan;
    }

    private function _buildKriteriaData(array $kriteriaInput, array $kriteriaNama): array
    {
        $kriteriaData = [];
        foreach ($kriteriaInput as $kId => $kData) {
            $entry = [];
            if (isset($kData['nilai']) && $kData['nilai'] !== '') {
                $entry['nilai'] = (int)$kData['nilai'];
            }
            if (!empty($kData['huruf'])) {
                $entry['huruf'] = $kData['huruf'];
            }
            if (!empty($kData['selesai'])) {
                $entry['selesai'] = true;
            }
            if (!empty($kData['catatan'])) {
                $entry['catatan'] = $kData['catatan'];
            }
            $entry['_label'] = $kriteriaNama[$kId] ?? '';
            $kriteriaData[$kId] = $entry;
        }
        return $kriteriaData;
    }

    private function _calculatePredikat($nilai): string
    {
        return predikatNilai($nilai);
    }
}
