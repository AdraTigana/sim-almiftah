<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\GuruMapelModel;
use App\Models\MapelModel;
use App\Models\RombelModel;
use App\Models\RoleModel;
use App\Models\TahunAjarModel;

class Guru extends BaseController
{
    public function index(): string
    {
        $userModel = new UserModel();
        $mapelModel = new MapelModel();
        $rombelModel = new RombelModel();
        $roleModel = new RoleModel();
        $gmModel = new GuruMapelModel();
        $taModel = new TahunAjarModel();

        $guru = $userModel->select('users.*, roles.nama as role_nama, roles.kode as role_kode')
                          ->join('roles', 'roles.id = users.role_id')
                          ->orderBy('users.nama')->findAll();

        $mapelList = $gmModel->select('guru_mapel.user_id, guru_mapel.mapel_id, guru_mapel.rombel_id, guru_mapel.tahun_ajar_id, mapel.nama as mapel_nama, rombel.nama as rombel_nama, tahun_ajar.tahun as tahun_ajar_nama')
                            ->join('mapel', 'mapel.id = guru_mapel.mapel_id')
                            ->join('rombel', 'rombel.id = guru_mapel.rombel_id', 'left')
                            ->join('tahun_ajar', 'tahun_ajar.id = guru_mapel.tahun_ajar_id', 'left')
                            ->findAll();

        $mapelByUser = [];
        foreach ($mapelList as $ml) {
            $mapelByUser[$ml['user_id']][] = $ml;
        }
        foreach ($guru as &$g) {
            $g['mapel_list'] = $mapelByUser[$g['id']] ?? [];
        }
        unset($g);

        $assignments = $gmModel->select('user_id, mapel_id, rombel_id, tahun_ajar_id')->findAll();
        $guruAssignments = [];
        foreach ($assignments as $a) {
            $uid = $a['user_id'];
            $tid = $a['tahun_ajar_id'];
            $mid = $a['mapel_id'];
            $rid = $a['rombel_id'];
            if (!isset($guruAssignments[$uid])) $guruAssignments[$uid] = [];
            if (!isset($guruAssignments[$uid][$tid])) $guruAssignments[$uid][$tid] = [];
            if (!isset($guruAssignments[$uid][$tid][$mid])) $guruAssignments[$uid][$tid][$mid] = [];
            if ($rid) $guruAssignments[$uid][$tid][$mid][] = $rid;
        }

        $tahunAjar = $taModel->orderBy('tahun', 'DESC')->findAll();

        return $this->render('admin/guru', [
            'title'           => 'Kelola Guru',
            'guru'            => $guru,
            'mapel'           => $mapelModel->findAll(),
            'rombel'          => $rombelModel->select('rombel.*, tahun_ajar.tahun as tahun_ajar_nama')
                                             ->join('tahun_ajar', 'tahun_ajar.id = rombel.tahun_ajar_id')
                                             ->where('rombel.is_active', 1)
                                             ->orderBy('tahun_ajar.tahun', 'DESC')
                                             ->orderBy('rombel.nama')
                                             ->findAll(),
            'roles'           => $roleModel->findAll(),
            'tahunAjar'       => $tahunAjar,
            'guruAssignments' => $guruAssignments,
        ]);
    }

    public function get(int $id)
    {
        $userModel = new UserModel();
        $user = $userModel->find($id);
        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'Guru tidak ditemukan.']);
        }
        return $this->response->setJSON($user);
    }

    public function store()
    {
        $userModel = new UserModel();
        $gmModel = new GuruMapelModel();

        $data = [
            'nama'      => $this->request->getPost('nama'),
            'email'     => $this->request->getPost('email'),
            'password'  => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'nip'       => $this->request->getPost('nip'),
            'role_id'   => $this->request->getPost('role_id'),
            'is_active' => 1,
        ];

        $db = \Config\Database::connect();
        $db->transStart();

        $userId = $userModel->insert($data);
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan guru.']);
        }

        $this->_saveAssignments($gmModel, $userId, $this->request->getPost('assign') ?? []);

        $db->transComplete();
        if ($db->transStatus() === false) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan guru.']);
        }
        return $this->response->setJSON(['success' => true, 'message' => 'Guru berhasil ditambahkan.']);
    }

    public function update(int $userId)
    {
        $userModel = new UserModel();
        $gmModel = new GuruMapelModel();

        $data = [
            'nama'    => $this->request->getPost('nama'),
            'email'   => $this->request->getPost('email'),
            'nip'     => $this->request->getPost('nip'),
            'role_id' => $this->request->getPost('role_id'),
        ];

        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $userModel->update($userId, $data);

        $assign = $this->request->getPost('assign') ?? [];
        $taIds = array_keys($assign);
        if (!empty($taIds)) {
            $gmModel->where('user_id', $userId)->whereIn('tahun_ajar_id', $taIds)->delete();
        }
        $this->_saveAssignments($gmModel, $userId, $assign);

        $db->transComplete();
        if ($db->transStatus() === false) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal memperbarui guru.']);
        }
        return $this->response->setJSON(['success' => true, 'message' => 'Guru berhasil diperbarui.']);
    }

    public function delete(int $userId)
    {
        $userModel = new UserModel();
        $user = $userModel->find($userId);
        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'Guru tidak ditemukan.']);
        }
        if ($userModel->delete($userId)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Guru berhasil dihapus.']);
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Gagal menghapus guru.']);
    }

    public function assign(int $userId)
    {
        $gmModel = new GuruMapelModel();
        $assignTaId = $this->request->getPost('assign_ta_id');
        if ($assignTaId) {
            $gmModel->where('user_id', $userId)->where('tahun_ajar_id', $assignTaId)->delete();
        }
        $this->_saveAssignments($gmModel, $userId, $this->request->getPost('assign') ?? []);
        return $this->response->setJSON(['success' => true, 'message' => 'Penugasan mapel berhasil diperbarui.']);
    }

    private function _saveAssignments(GuruMapelModel $gmModel, int $userId, array $assign): void
    {
        $batch = [];
        // assign[ta_id][mapel_id][] = rombel_id
        foreach ($assign as $taId => $mapels) {
            foreach ($mapels as $mapelId => $rombelIds) {
                foreach ($rombelIds as $rombelId) {
                    $batch[] = [
                        'user_id'       => $userId,
                        'mapel_id'      => (int) $mapelId,
                        'rombel_id'     => (int) $rombelId,
                        'tahun_ajar_id' => (int) $taId,
                    ];
                }
            }
        }
        if (!empty($batch)) {
            $gmModel->insertBatch($batch);
        }
    }
}
