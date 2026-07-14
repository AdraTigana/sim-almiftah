<?php

namespace App\Controllers\WaliKelas;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Profil extends BaseController
{
    public function index()
    {
        $userId = session()->get('userId');
        $userModel = new UserModel();

        $user = $userModel->getUserWithRole($userId);

        return $this->render('walas/profil', [
            'title' => 'Profil Saya',
            'user'  => $user,
        ]);
    }

    public function update()
    {
        $userId = session()->get('userId');
        $userModel = new UserModel();

        $nama  = trim($this->request->getPost('nama'));
        $email = trim($this->request->getPost('email'));

        if (empty($nama) || empty($email)) {
            return redirect()->to('walas/profil')->with('error', 'Nama dan email wajib diisi.');
        }

        $cek = $userModel->where('email', $email)->where('id !=', $userId)->first();
        if ($cek) {
            return redirect()->to('walas/profil')->with('error', 'Email sudah digunakan pengguna lain.');
        }

        $passwordLama = $this->request->getPost('password');
        $passwordBaru = $this->request->getPost('password_baru');

        $data = [
            'nama'  => $nama,
            'email' => $email,
        ];

        if (!empty($passwordLama) || !empty($passwordBaru)) {
            if (empty($passwordLama) || empty($passwordBaru)) {
                return redirect()->to('walas/profil')->with('error', 'Password lama dan password baru harus diisi.');
            }
            if (strlen($passwordBaru) < 6) {
                return redirect()->to('walas/profil')->with('error', 'Password baru minimal 6 karakter.');
            }
            $user = $userModel->find($userId);
            if (!password_verify($passwordLama, $user['password'])) {
                return redirect()->to('walas/profil')->with('error', 'Password lama yang Anda masukkan tidak sesuai.');
            }
            $data['password'] = password_hash($passwordBaru, PASSWORD_DEFAULT);
        }

        if (!$userModel->skipValidation(true)->update($userId, $data)) {
            return redirect()->to('walas/profil')->with('error', 'Gagal memperbarui profil. Silakan coba lagi.');
        }

        session()->set('nama', $nama);

        return redirect()->to('walas/profil')->with('message', 'Profil berhasil diperbarui.');
    }
}
