<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RombelModel;
use App\Models\TahunAjarModel;

class Rombel extends BaseController
{
    public function index()
    {
        $model = new RombelModel();
        $taModel = new TahunAjarModel();
        $userModel = new \App\Models\UserModel();
        $db = \Config\Database::connect();

        $rombel = $model->select('rombel.*, users.nama as walas_nama, tahun_ajar.tahun as tahun_ajar_nama')
                        ->join('users', 'users.id = rombel.walas_id', 'left')
                        ->join('tahun_ajar', 'tahun_ajar.id = rombel.tahun_ajar_id', 'left')
                        ->findAll();

        $santriCounts = $db->table('siswa_rombel')
                           ->select('rombel_id, COUNT(*) as total')
                           ->groupBy('rombel_id')
                           ->get()
                           ->getResultArray();
        $countMap = [];
        foreach ($santriCounts as $c) {
            $countMap[$c['rombel_id']] = $c['total'];
        }
        foreach ($rombel as &$r) {
            $r['santri_count'] = $countMap[$r['id']] ?? 0;
        }
        unset($r);

        $walasList = $userModel->select('users.id, users.nama, users.nip')
                            ->join('roles', 'roles.id = users.role_id')
                            ->where('roles.kode', 'walas')
                            ->orderBy('users.nama')
                            ->findAll();

        $data['title'] = 'Rombel';
        $data['rombel'] = $rombel;
        $data['tahunAjar'] = $taModel->findAll();
        $data['walasList'] = $walasList;

        return $this->render('admin/rombel', $data);
    }

    public function get(int $id)
    {
        $model = new RombelModel();
        $rombel = $model->find($id);
        if (!$rombel) {
            return $this->response->setJSON(['success' => false, 'message' => 'Rombel tidak ditemukan.']);
        }
        return $this->response->setJSON($rombel);
    }

    public function create()
    {
        $model = new RombelModel();
        $data = $this->request->getPost();
        if (empty($data['tahun_ajar_id'])) {
            $taModel = new TahunAjarModel();
            $active = $taModel->where('is_current', 1)->first();
            if ($active) $data['tahun_ajar_id'] = $active['id'];
        }
        if ($model->save($data)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Rombel berhasil ditambahkan.']);
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Data rombel tidak valid. Periksa kembali isian.']);
    }

    public function update(int $id)
    {
        $model = new RombelModel();
        if ($model->update($id, $this->request->getPost())) {
            return $this->response->setJSON(['success' => true, 'message' => 'Rombel berhasil diupdate.']);
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Data rombel tidak valid. Periksa kembali isian.']);
    }

    public function delete(int $id)
    {
        $model = new RombelModel();
        if ($model->delete($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Rombel berhasil dihapus.']);
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Gagal menghapus rombel.']);
    }

    public function getWalas(int $rombelId)
    {
        $model = new RombelModel();
        $rombel = $model->select('walas_id')->find($rombelId);
        if (!$rombel) {
            return $this->response->setJSON(['success' => false, 'message' => 'Rombel tidak ditemukan.']);
        }
        return $this->response->setJSON(['walas_id' => $rombel['walas_id']]);
    }

    public function assignWalas(int $rombelId)
    {
        $model = new RombelModel();
        $walasId = $this->request->getPost('walas_id');

        if (!empty($walasId)) {
            $rombel = $model->select('tahun_ajar_id')->find($rombelId);
            if (!$rombel) {
                return $this->response->setJSON(['success' => false, 'message' => 'Rombel tidak ditemukan.']);
            }
            $exists = $model->where('id !=', $rombelId)
                            ->where('walas_id', $walasId)
                            ->where('tahun_ajar_id', $rombel['tahun_ajar_id'])
                            ->first();
            if ($exists) {
                return $this->response->setJSON(['success' => false, 'message' => 'Guru tersebut sudah menjadi wali kelas di rombel lain pada tahun ajaran yang sama.']);
            }
        }

        if ($model->update($rombelId, ['walas_id' => $walasId])) {
            return $this->response->setJSON(['success' => true, 'message' => 'Wali kelas berhasil ditugaskan.']);
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Gagal menugaskan wali kelas.']);
    }
}
