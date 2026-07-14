<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MapelModel;
use App\Models\KategoriModel;
use App\Models\KriteriaPenilaianModel;

class Kurikulum extends BaseController
{
    public function index()
    {
        $mapelModel = new MapelModel();
        $kategoriModel = new KategoriModel();
        $kriteriaModel = new KriteriaPenilaianModel();

        $data['title'] = 'Kurikulum';
        $data['tab'] = $this->request->getGet('tab') ?? 'mapel';
        $data['mapel'] = $mapelModel->orderBy('nama')->findAll();
        $data['kategori'] = $kategoriModel->select('kategori.*, mapel.nama as mapel_nama')
                                      ->join('mapel', 'mapel.id = kategori.mapel_id')
                                      ->orderBy('kategori.urutan', 'ASC')->findAll();
        $data['kriteria'] = $kriteriaModel->select('kriteria_penilaian.*, mapel.nama as mapel_nama, kategori.nama as kategori_nama')
                                          ->join('mapel', 'mapel.id = kriteria_penilaian.mapel_id')
                                          ->join('kategori', 'kategori.id = kriteria_penilaian.kategori_id', 'left')
                                          ->orderBy('kriteria_penilaian.created_at', 'DESC')->findAll();

        return $this->render('admin/kurikulum', $data);
    }

    public function createMapel()
    {
        $model = new MapelModel();
        if ($model->save($this->request->getPost())) {
            session()->setFlashdata('success', 'Mapel berhasil ditambahkan.');
        } else {
            session()->setFlashdata('error', 'Data mapel tidak valid. Periksa kembali isian.');
        }
        return redirect()->to(base_url('admin/kurikulum?tab=mapel'));
    }

    public function deleteMapel(int $id)
    {
        $model = new MapelModel();
        if ($model->delete($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Mapel berhasil dihapus.']);
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Gagal menghapus mapel. Mungkin masih memiliki kategori terkait.']);
    }

    public function createKategori()
    {
        $model = new KategoriModel();
        if ($model->save($this->request->getPost())) {
            session()->setFlashdata('success', 'Kategori berhasil ditambahkan.');
        } else {
            session()->setFlashdata('error', 'Data kategori tidak valid. Periksa kembali isian.');
        }
        $filterMapel = $this->request->getPost('filter_mapel');
        $query = 'tab=kategori';
        if (!empty($filterMapel)) $query .= '&filter_mapel=' . urlencode($filterMapel);
        return redirect()->to(base_url('admin/kurikulum?' . $query));
    }

    public function deleteKategori(int $id)
    {
        $model = new KategoriModel();
        if ($model->delete($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Kategori berhasil dihapus.']);
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Gagal menghapus kategori.']);
    }

    public function getKategori(int $id)
    {
        $model = new KategoriModel();
        $kategori = $model->find($id);
        if (!$kategori) {
            return $this->response->setJSON(['success' => false, 'message' => 'Kategori tidak ditemukan.']);
        }
        return $this->response->setJSON(['success' => true, 'data' => $kategori]);
    }

    public function updateKategori(int $id)
    {
        $model = new KategoriModel();
        $data = [
            'mapel_id'     => $this->request->getPost('mapel_id'),
            'nama'         => trim($this->request->getPost('nama')),
            'urutan'       => $this->request->getPost('urutan'),
            'hitung_kosong'=> $this->request->getPost('hitung_kosong') ? 1 : 0,
        ];
        if (empty($data['nama'])) {
            return redirect()->back()->withInput()->with('error', 'Nama kategori tidak boleh kosong.');
        }
        $filterMapel = $this->request->getPost('filter_mapel');
        $query = 'tab=kategori';
        if (!empty($filterMapel)) $query .= '&filter_mapel=' . urlencode($filterMapel);
        if ($model->update($id, $data)) {
            return redirect()->to(base_url('admin/kurikulum?' . $query))->with('success', 'Kategori berhasil diperbarui.');
        }
        return redirect()->back()->withInput()->with('error', 'Gagal memperbarui kategori.');
    }

    public function createKriteria()
    {
        $model = new KriteriaPenilaianModel();
        if ($model->save($this->request->getPost())) {
            session()->setFlashdata('success', 'Kriteria berhasil ditambahkan.');
        } else {
            session()->setFlashdata('error', 'Data kriteria tidak valid. Periksa kembali isian.');
        }
        $filterMapel = $this->request->getPost('filter_mapel');
        $filterKategori = $this->request->getPost('filter_kategori');
        $query = 'tab=kriteria';
        if (!empty($filterMapel)) $query .= '&filter_mapel=' . urlencode($filterMapel);
        if (!empty($filterKategori)) $query .= '&filter_kategori=' . urlencode($filterKategori);
        return redirect()->to(base_url('admin/kurikulum?' . $query));
    }

    public function deleteKriteria(int $id)
    {
        $model = new KriteriaPenilaianModel();
        if ($model->delete($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Kriteria berhasil dihapus.']);
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Gagal menghapus kriteria.']);
    }

    public function getKriteria(int $id)
    {
        $model = new KriteriaPenilaianModel();
        $kriteria = $model->find($id);
        if (!$kriteria) {
            return $this->response->setJSON(['success' => false, 'message' => 'Kriteria tidak ditemukan.']);
        }
        return $this->response->setJSON(['success' => true, 'data' => $kriteria]);
    }

    public function updateKriteria(int $id)
    {
        $model = new KriteriaPenilaianModel();
        $data = [
            'mapel_id'    => $this->request->getPost('mapel_id'),
            'kategori_id' => $this->request->getPost('kategori_id') ?: null,
            'nama'        => trim($this->request->getPost('nama')),
            'bobot'       => $this->request->getPost('bobot') ?: 100,
            'skala_max'   => $this->request->getPost('skala_max') ?: 100,
            'input_type'  => $this->request->getPost('input_type') ?: 'number',
        ];
        if (empty($data['nama'])) {
            return redirect()->back()->withInput()->with('error', 'Nama kriteria tidak boleh kosong.');
        }
        $filterMapel = $this->request->getPost('filter_mapel');
        $filterKategori = $this->request->getPost('filter_kategori');
        $query = 'tab=kriteria';
        if (!empty($filterMapel)) $query .= '&filter_mapel=' . urlencode($filterMapel);
        if (!empty($filterKategori)) $query .= '&filter_kategori=' . urlencode($filterKategori);
        if ($model->update($id, $data)) {
            return redirect()->to(base_url('admin/kurikulum?' . $query))->with('success', 'Kriteria berhasil diperbarui.');
        }
        return redirect()->back()->withInput()->with('error', 'Gagal memperbarui kriteria.');
    }
}
