<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\PresensiModel;

class Presensi extends BaseController
{
    public function saveBatch()
    {
        $post = $this->request->getPost();
        $userId = session()->get('userId');
        $rombelId = $post['rombel_id'];
        $mapelId = $post['mapel_id'];
        $tanggal = $post['tanggal'] ?? date('Y-m-d');
        $statuses = $post['status'] ?? [];

        if (empty($rombelId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Rombel tidak dipilih.']);
        }

        $allowed = ['hadir', 'sakit', 'izin', 'alpha'];
        $model = new PresensiModel();
        $db = \Config\Database::connect();
        $db->transStart();
        $model->where('rombel_id', $rombelId)->where('mapel_id', $mapelId)->where('tanggal', $tanggal)->delete();
        $batch = [];
        foreach ($statuses as $siswaId => $status) {
            $status = strtolower(trim($status));
            if (!in_array($status, $allowed)) continue;
            $batch[] = [
                'siswa_id'   => $siswaId,
                'rombel_id'  => $rombelId,
                'mapel_id'   => $mapelId,
                'user_id'    => $userId,
                'status'     => $status,
                'tanggal'    => $tanggal,
                'keterangan' => $post['keterangan'][$siswaId] ?? '',
            ];
        }
        if (!empty($batch)) {
            $model->insertBatch($batch);
        }
        $db->transComplete();
        if ($db->transStatus() === false) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan presensi.']);
        }
        return $this->response->setJSON(['success' => true, 'message' => 'Presensi berhasil disimpan.']);
    }

    public function syncBatch()
    {
        $input = $this->request->getJSON(true);
        $items = $input['items'] ?? [];

        if (empty($items)) {
            return $this->response->setJSON(['success' => true, 'count' => 0]);
        }

        $allowed = ['hadir', 'sakit', 'izin', 'alpha'];
        $model = new PresensiModel();
        $db = \Config\Database::connect();
        $count = 0;

        foreach ($items as $item) {
            $status = strtolower(trim($item['status'] ?? ''));
            if (!in_array($status, $allowed)) continue;

            $db->transStart();
            $model
                ->where('rombel_id', $item['rombel_id'] ?? 0)
                ->where('mapel_id', $item['mapel_id'] ?? 0)
                ->where('siswa_id', $item['siswa_id'] ?? 0)
                ->where('tanggal', $item['tanggal'] ?? date('Y-m-d'))
                ->delete();
            $model->insert([
                'siswa_id'   => $item['siswa_id'] ?? 0,
                'rombel_id'  => $item['rombel_id'] ?? 0,
                'mapel_id'   => $item['mapel_id'] ?? 0,
                'user_id'    => session()->get('userId'),
                'status'     => $status,
                'tanggal'    => $item['tanggal'] ?? date('Y-m-d'),
                'keterangan' => $item['keterangan'] ?? '',
            ]);
            $db->transComplete();
            if ($db->transStatus() !== false) $count++;
        }

        return $this->response->setJSON(['success' => true, 'count' => $count]);
    }
}
