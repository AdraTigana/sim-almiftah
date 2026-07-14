<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\GuruMapelModel;
use App\Models\TahunAjarModel;
use App\Models\MapelModel;
use App\Models\RombelModel;

class InputSaya extends BaseController
{
    public function index(): string
    {
        $userId = session()->get('userId');
        $guruMapelModel = new GuruMapelModel();
        $taModel = new TahunAjarModel();
        $mapelModel = new MapelModel();
        $rombelModel = new RombelModel();

        $tahunAjar = $taModel->orderBy('tahun', 'DESC')->findAll();
        $tahunAktif = $taModel->getActive();

        $selectedTa = $this->request->getGet('tahun_ajar_id') ?: ($tahunAktif['id'] ?? null);

        // Ambil semua mapel×rombel yang diampu guru ini, filter by tahun ajar
        $guruMapel = $guruMapelModel
            ->select('guru_mapel.*, mapel.nama as mapel_nama, mapel.singkatan as mapel_singkatan, rombel.nama as rombel_nama, tahun_ajar.tahun as tahun_ajar_nama')
            ->join('mapel', 'mapel.id = guru_mapel.mapel_id')
            ->join('rombel', 'rombel.id = guru_mapel.rombel_id', 'left')
            ->join('tahun_ajar', 'tahun_ajar.id = guru_mapel.tahun_ajar_id', 'left')
            ->where('guru_mapel.user_id', $userId)
            ->where('mapel.is_active', 1);

        if ($selectedTa) {
            $guruMapel = $guruMapel->where('guru_mapel.tahun_ajar_id', $selectedTa);
        }

        $guruMapel = $guruMapel->findAll();

        // Group mapel → list of rombels
        $mapelGrouped = [];
        foreach ($guruMapel as $gm) {
            $key = $gm['mapel_id'];
            if (!isset($mapelGrouped[$key])) {
                $mapelGrouped[$key] = [
                    'mapel_id'   => $gm['mapel_id'],
                    'mapel_nama' => $gm['mapel_nama'],
                    'singkatan'  => $gm['mapel_singkatan'],
                    'rombel'     => [],
                ];
            }
            $mapelGrouped[$key]['rombel'][] = [
                'rombel_id'      => $gm['rombel_id'],
                'rombel_nama'    => $gm['rombel_nama'],
                'tahun_ajar_id'   => $gm['tahun_ajar_id'],
                'tahun_ajar_nama' => $gm['tahun_ajar_nama'],
            ];
        }

        $allMapel  = $mapelModel->where('is_active', 1)->orderBy('kelompok')->orderBy('urutan')->findAll();
        $allRombel = $rombelModel->where('is_active', 1)->findAll();

        $currentAssign = [];
        if ($selectedTa) {
            $rows = $guruMapelModel
                ->select('mapel_id, rombel_id')
                ->where('user_id', $userId)
                ->where('tahun_ajar_id', $selectedTa)
                ->findAll();
            foreach ($rows as $r) {
                $currentAssign[$r['mapel_id']][] = $r['rombel_id'];
            }
        }

        $assignTaName = '';
        if ($selectedTa) {
            $taKey = array_search($selectedTa, array_column($tahunAjar, 'id'));
            $assignTaName = $taKey !== false ? $tahunAjar[$taKey]['tahun'] : '';
        }

        return $this->render('guru/input_saya', [
            'title'          => 'Kelas Saya',
            'mapelGrouped'   => $mapelGrouped,
            'tahunAjar'      => $tahunAjar,
            'selectedTa'     => $selectedTa,
            'allMapel'       => $allMapel,
            'allRombel'      => $allRombel,
            'tahunAktif'     => $tahunAktif,
            'currentAssign'  => $currentAssign,
            'assignTaName'   => $assignTaName,
        ]);
    }

    public function selfAssign()
    {
        $userId = session()->get('userId');
        $gmModel = new GuruMapelModel();
        $taModel = new TahunAjarModel();

        $taId = $this->request->getPost('tahun_ajar_id');
        if (!$taId) {
            $tahunAktif = $taModel->getActive();
            $taId = $tahunAktif['id'] ?? null;
        }
        if (!$taId) {
            return redirect()->to('guru/input-saya')->with('error', 'Tidak ada tahun ajar aktif.');
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        $gmModel->where('user_id', $userId)
                ->where('tahun_ajar_id', $taId)
                ->delete();

        $assign = $this->request->getPost('assign') ?? [];
        $batch  = [];

        foreach ($assign as $mapelId => $rombelIds) {
            foreach ($rombelIds as $rombelId) {
                $batch[] = [
                    'user_id'       => $userId,
                    'mapel_id'      => (int) $mapelId,
                    'rombel_id'     => (int) $rombelId,
                    'tahun_ajar_id' => (int) $taId,
                ];
            }
        }
        if (!empty($batch)) {
            $gmModel->insertBatch($batch);
        }

        if ($db->transStatus() === false) {
            $db->transRollback();
            return redirect()->to('guru/input-saya')->with('error', 'Gagal menyimpan penugasan.');
        }

        $db->transCommit();
        return redirect()->to('guru/input-saya')->with('message', 'Penugasan berhasil disimpan.');
    }
}
