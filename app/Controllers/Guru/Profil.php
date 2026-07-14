<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\GuruMapelModel;
use App\Models\TahunAjarModel;
use App\Models\MapelModel;
use App\Models\RombelModel;

class Profil extends BaseController
{
    public function index(): string
    {
        $userId = session()->get('userId');
        $userModel = new UserModel();
        $gmModel = new GuruMapelModel();
        $taModel = new TahunAjarModel();
        $mapelModel = new MapelModel();
        $rombelModel = new RombelModel();

        $user = $userModel->select('users.*, roles.nama as role_nama, roles.kode as role_kode')
                ->join('roles', 'roles.id = users.role_id')
                ->find($userId);

        $tahunAktif = $taModel->getActive();

        $mapel = $gmModel->select('mapel.nama as mapel_nama, mapel.singkatan, rombel.nama as rombel_nama, tahun_ajar.tahun as tahun_ajar_nama')
                ->join('mapel', 'mapel.id = guru_mapel.mapel_id')
                ->join('rombel', 'rombel.id = guru_mapel.rombel_id', 'left')
                ->join('tahun_ajar', 'tahun_ajar.id = guru_mapel.tahun_ajar_id', 'left')
                ->where('guru_mapel.user_id', $userId)
                ->findAll();

        $allMapel  = $mapelModel->where('is_active', 1)->orderBy('kelompok')->orderBy('urutan')->findAll();
        $allRombel = $rombelModel->where('is_active', 1)->findAll();

        $currentAssign = [];
        if ($tahunAktif) {
            $rows = $gmModel
                ->select('mapel_id, rombel_id')
                ->where('user_id', $userId)
                ->where('tahun_ajar_id', $tahunAktif['id'])
                ->findAll();
            foreach ($rows as $r) {
                $currentAssign[$r['mapel_id']][] = $r['rombel_id'];
            }
        }

        return $this->render('guru/profil', [
            'title'          => 'Profil Saya',
            'user'           => $user,
            'mapel'          => $mapel,
            'allMapel'       => $allMapel,
            'allRombel'      => $allRombel,
            'tahunAktif'     => $tahunAktif,
            'currentAssign'  => $currentAssign,
        ]);
    }

    public function selfAssign()
    {
        $userId = session()->get('userId');
        $gmModel = new GuruMapelModel();
        $taModel = new TahunAjarModel();

        $tahunAktif = $taModel->getActive();
        if (!$tahunAktif) {
            return redirect()->to('guru/profil')->with('error', 'Tidak ada tahun ajar aktif.');
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        $gmModel->where('user_id', $userId)
                ->where('tahun_ajar_id', $tahunAktif['id'])
                ->delete();

        $assign = $this->request->getPost('assign') ?? [];
        $batch  = [];

        foreach ($assign as $mapelId => $rombelIds) {
            foreach ($rombelIds as $rombelId) {
                $batch[] = [
                    'user_id'       => $userId,
                    'mapel_id'      => (int) $mapelId,
                    'rombel_id'     => (int) $rombelId,
                    'tahun_ajar_id' => (int) $tahunAktif['id'],
                ];
            }
        }
        if (!empty($batch)) {
            $gmModel->insertBatch($batch);
        }

        if ($db->transStatus() === false) {
            $db->transRollback();
            return redirect()->to('guru/profil')->with('error', 'Gagal menyimpan penugasan.');
        }

        $db->transCommit();
        return redirect()->to('guru/profil')->with('message', 'Penugasan berhasil disimpan.');
    }

    public function update()
    {
        $userId = session()->get('userId');
        $userModel = new UserModel();

        $data = [
            'nama'  => trim($this->request->getPost('nama')),
            'email' => trim($this->request->getPost('email')),
        ];

        if (empty($data['nama'])) {
            return redirect()->back()->withInput()->with('error', 'Nama tidak boleh kosong.');
        }
        if (empty($data['email'])) {
            return redirect()->back()->withInput()->with('error', 'Email tidak boleh kosong.');
        }

        $user = $userModel->find($userId);

        // Cek duplikat email (kecuali milik sendiri)
        $existing = $userModel->where('email', $data['email'])->where('id !=', $userId)->first();
        if ($existing) {
            return redirect()->back()->withInput()->with('error', 'Email sudah digunakan oleh pengguna lain.');
        }

        $password = $this->request->getPost('password');
        $passwordBaru = $this->request->getPost('password_baru');

        if (!empty($password) || !empty($passwordBaru)) {
            if (empty($password)) {
                return redirect()->back()->withInput()->with('error', 'Password lama wajib diisi jika ingin mengubah password.');
            }
            if (empty($passwordBaru)) {
                return redirect()->back()->withInput()->with('error', 'Password baru wajib diisi.');
            }
            if (strlen($passwordBaru) < 6) {
                return redirect()->back()->withInput()->with('error', 'Password baru minimal 6 karakter.');
            }
            if (!password_verify($password, $user['password'])) {
                return redirect()->back()->withInput()->with('error', 'Password lama yang Anda masukkan tidak sesuai.');
            }
            $data['password'] = password_hash($passwordBaru, PASSWORD_DEFAULT);
        }

        if (!$userModel->skipValidation(true)->update($userId, $data)) {
            return redirect()->back()->withInput()->with('error', 'Profil gagal diperbarui. Periksa kembali data Anda.');
        }

        session()->set('nama', $data['nama']);

        return redirect()->to('guru/profil')->with('message', 'Profil berhasil diperbarui.');
    }
}
