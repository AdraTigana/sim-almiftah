<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SiswaModel;
use App\Models\RombelModel;
use App\Models\SiswaRombelModel;
use App\Models\TahunAjarModel;

class Santri extends BaseController
{
    public function index()
    {
        $model = new SiswaModel();
        $rombelModel = new RombelModel();
        $taModel = new TahunAjarModel();
        $db = \Config\Database::connect();

        $santri = $model->orderBy('created_at', 'DESC')->findAll();

        $srRows = $db->table('siswa_rombel')
                     ->select('siswa_rombel.siswa_id, siswa_rombel.rombel_id, rombel.nama as rombel_nama')
                     ->join('rombel', 'rombel.id = siswa_rombel.rombel_id', 'left')
                     ->get()
                     ->getResultArray();
        $rombelNamaMap = [];
        $rombelIdMap = [];
        foreach ($srRows as $sr) {
            $rombelNamaMap[$sr['siswa_id']] = $sr['rombel_nama'] ?? '';
            $rombelIdMap[$sr['siswa_id']] = $sr['rombel_id'] ?? null;
        }
        foreach ($santri as &$s) {
            $sid = $s['id'];
            $s['rombel_nama'] = $rombelNamaMap[$sid] ?? '';
            $s['rombel_id'] = $rombelIdMap[$sid] ?? null;
        }
        unset($s);

        $data['santri'] = $santri;
        $data['title'] = 'Data Santri';
        $data['rombel'] = $rombelModel->findAll();
        $data['tahunAjar'] = $taModel->findAll();
        return $this->render('admin/santri', $data);
    }

    public function get(int $id)
    {
        $model = new SiswaModel();
        $santri = $model->find($id);
        if (!$santri) {
            return $this->response->setJSON(['success' => false, 'message' => 'Santri tidak ditemukan.']);
        }

        $srModel = new SiswaRombelModel();
        $siswaRombel = $srModel->where('siswa_id', $id)->first();
        $santri['rombel_id'] = $siswaRombel['rombel_id'] ?? null;

        return $this->response->setJSON($santri);
    }

    public function store()
    {
        $post = $this->request->getPost();
        if (empty($post['nama'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Nama santri wajib diisi.']);
        }
        if (empty($post['nis'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'NIS wajib diisi.']);
        }
        $db = \Config\Database::connect();
        $db->transStart();

        $model = new SiswaModel();
        if (!$model->save($post)) {
            $db->transRollback();
            return $this->response->setJSON(['success' => false, 'message' => 'Data santri tidak valid. Periksa kembali isian.']);
        }
        $siswaId = $model->insertID;

        if (!empty($post['rombel_id'])) {
            $srModel = new SiswaRombelModel();
            $taModel = new TahunAjarModel();
            $tahunAktif = $taModel->getActive();
            $srModel->save([
                'siswa_id' => $siswaId,
                'rombel_id' => $post['rombel_id'],
                'tahun_ajar_id' => $tahunAktif['id'] ?? null,
            ]);
        }

        $db->transComplete();
        if ($db->transStatus() === false) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan santri.']);
        }
        return $this->response->setJSON(['success' => true, 'message' => 'Santri berhasil ditambahkan.']);
    }

    public function update(int $id)
    {
        $post = $this->request->getPost();
        if (empty($post['nama'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Nama santri wajib diisi.']);
        }
        $model = new SiswaModel();
        unset($post['id']);
        unset($post['nis']);

        $db = \Config\Database::connect();
        $db->transStart();

        if (!$model->update($id, $post)) {
            $db->transRollback();
            return $this->response->setJSON(['success' => false, 'message' => 'Data santri tidak valid. Periksa kembali isian.']);
        }

        $srModel = new SiswaRombelModel();
        $existing = $srModel->where('siswa_id', $id)->first();
        if (!empty($post['rombel_id'])) {
            if ($existing) {
                $srModel->update($existing['id'], ['rombel_id' => $post['rombel_id']]);
            } else {
                $taModel = new TahunAjarModel();
                $tahunAktif = $taModel->getActive();
                $srModel->save([
                    'siswa_id' => $id,
                    'rombel_id' => $post['rombel_id'],
                    'tahun_ajar_id' => $tahunAktif['id'] ?? null,
                ]);
            }
        } elseif ($existing) {
            $srModel->delete($existing['id']);
        }

        $db->transComplete();
        if ($db->transStatus() === false) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal mengupdate santri.']);
        }
        return $this->response->setJSON(['success' => true, 'message' => 'Santri berhasil diupdate.']);
    }

    public function delete(int $id)
    {
        $model = new SiswaModel();
        if ($model->delete($id, true)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Santri berhasil dihapus.']);
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Gagal menghapus santri.']);
    }

    public function importExcel()
    {
        $file = $this->request->getFile('file');
        $ext = $file ? strtolower($file->getExtension()) : '';
        if (!$file || !$file->isValid() || !in_array($ext, ['xlsx', 'xls'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'File tidak valid. Pastikan file berformat .xlsx atau .xls.']);
        }

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
            $rows = $spreadsheet->getActiveSheet()->toArray();
            $model = new SiswaModel();
            $count = 0;
            $duplicates = [];
            $excelNis = [];
            $excelData = [];

            foreach ($rows as $i => $row) {
                if ($i === 0) continue;
                $nis = trim($row[0] ?? '');
                if (empty($nis) || empty(trim($row[1] ?? ''))) continue;
                $excelNis[] = $nis;
                $excelData[$i] = $row;
            }

            $srModel = null;
            $rombelId = $this->request->getPost('rombel_id');
            $tahunAjarId = null;
            if (!empty($rombelId)) {
                $srModel = new SiswaRombelModel();
                $tahunAktif = model(TahunAjarModel::class)->getActive();
                $tahunAjarId = $tahunAktif['id'] ?? null;
            }

            if (!empty($excelNis)) {
                $existing = $model->whereIn('nis', $excelNis)->findAll();
                $existingNis = array_column($existing, 'nis');

                foreach ($excelData as $i => $row) {
                    $nis = trim($row[0] ?? '');
                    if (in_array($nis, $existingNis)) {
                        $duplicates[] = $nis;
                        continue;
                    }

                    $data = [
                        'nis'           => $nis,
                        'nama'          => trim($row[1] ?? ''),
                        'jenkel'        => strtoupper(trim($row[2] ?? '')) === 'P' ? 'P' : 'L',
                        'tempat_lahir'  => trim($row[3] ?? ''),
                        'tanggal_lahir' => !empty($row[4]) ? date('Y-m-d', strtotime($row[4])) : null,
                        'nama_wali'     => trim($row[5] ?? ''),
                        'alamat'        => trim($row[6] ?? ''),
                    ];

                    if ($model->save($data)) {
                        $count++;
                        if ($srModel) {
                            $srModel->save([
                                'siswa_id'      => $model->getInsertID(),
                                'rombel_id'     => $rombelId,
                                'tahun_ajar_id' => $tahunAjarId,
                            ]);
                        }
                    }
                }
            }

            $response = [
                'success' => true,
                'message' => $count . ' santri berhasil diimpor.',
            ];

            if (!empty($duplicates)) {
                $response['duplicates'] = $duplicates;
                $response['message'] .= ' ' . count($duplicates) . ' NIS duplikat ditemukan dan dilewati: ' . implode(', ', $duplicates);
            }

            return $this->response->setJSON($response);
        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Format file tidak didukung. Pastikan file yang diupload adalah file Excel (.xlsx).'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses file: ' . $e->getMessage()
            ]);
        }
    }
}
