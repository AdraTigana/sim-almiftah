<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\MapelModel;
use App\Models\PresensiModel;
use App\Models\SiswaRombelModel;
use App\Models\RombelModel;

class PresensiRekap extends BaseController
{
    public function index(): string
    {
        $rombelModel = new RombelModel();
        $rombel = $rombelModel->where('is_active', 1)->findAll();

        $rombelId = $this->request->getGet('rombel_id');
        $mapelId = $this->request->getGet('mapel_id');

        $siswaList = [];
        $dates = [];
        $presensiData = [];
        $mapel = null;
        $rombelNama = null;

        if ($rombelId && $mapelId) {
            $mapelModel = new MapelModel();
            $mapel = $mapelModel->find($mapelId);

            $rombelData = $rombelModel->find($rombelId);
            $rombelNama = $rombelData['nama'] ?? null;

            $srModel = new SiswaRombelModel();
            $siswaList = $srModel->getSiswaByRombel($rombelId);

            $presensiModel = new PresensiModel();
            $records = $presensiModel
                ->where('rombel_id', $rombelId)
                ->where('mapel_id', $mapelId)
                ->orderBy('tanggal', 'ASC')
                ->findAll();

            $dates = array_unique(array_column($records, 'tanggal'));
            sort($dates);

            foreach ($records as $record) {
                $presensiData[$record['siswa_id']][$record['tanggal']] = [
                    'status'     => $record['status'],
                    'keterangan' => $record['keterangan'],
                ];
            }
        }

        return $this->render('guru/presensi_rekap', [
            'title'        => 'Rekap Kehadiran',
            'rombel'       => $rombel,
            'siswaList'    => $siswaList,
            'dates'        => $dates,
            'presensiData' => $presensiData,
            'rombelId'     => $rombelId,
            'mapel'        => $mapel,
            'mapelId'      => $mapelId,
            'rombelNama'   => $rombelNama,
        ]);
    }
}
