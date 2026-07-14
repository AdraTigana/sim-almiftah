<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TahunAjarModel;

class TahunAjar extends BaseController
{
    public function index()
    {
        $model = new TahunAjarModel();
        $data['tahunAjar'] = $model->orderBy('tahun', 'DESC')->findAll();
        $data['title'] = 'Tahun Ajaran';
        return $this->render('admin/tahun_ajar', $data);
    }

    public function create()
    {
        $model = new TahunAjarModel();
        if ($model->save($this->request->getPost())) {
            return $this->response->setJSON(['success' => true, 'message' => 'Tahun ajaran berhasil ditambahkan.']);
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Data tahun ajaran tidak valid. Periksa kembali isian.']);
    }

    public function edit(int $id)
    {
        $model = new TahunAjarModel();
        $data = $this->request->getPost();
        if ($model->update($id, $data)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Tahun ajaran berhasil diupdate.']);
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Data tahun ajaran tidak valid. Periksa kembali isian.']);
    }

    public function delete(int $id)
    {
        $model = new TahunAjarModel();
        if ($model->delete($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Tahun ajaran berhasil dihapus.']);
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Tahun ajaran gagal dihapus. Pastikan tidak ada data terkait.']);
    }

    public function setActive(int $id)
    {
        $model = new TahunAjarModel();
        $model->setCurrent($id);
        return $this->response->setJSON(['success' => true, 'message' => 'Tahun Ajar Diaktifkan']);
    }
}
