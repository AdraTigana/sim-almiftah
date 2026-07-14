<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Profil extends BaseController
{
    public function index(): string
    {
        $userId = session()->get('userId');
        $userModel = new UserModel();
        $user = $userModel->getUserWithRole($userId);

        return $this->render('admin/profil', [
            'title' => 'Profil',
            'user'  => $user,
        ]);
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

        return redirect()->to('admin/profil')->with('message', 'Profil berhasil diperbarui.');
    }
}
