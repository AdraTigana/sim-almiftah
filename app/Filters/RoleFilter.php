<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use App\Models\UserModel;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = service('session');

        if (!$session->get('isLoggedIn')) {
            // Coba remember cookie
            $request = service('request');
            $token = $request->getCookie('remember_token');
            if ($token) {
                $userModel = new UserModel();
                $user = $userModel->findByRememberToken($token);
                if ($user) {
                    $session->set([
                        'isLoggedIn' => true,
                        'userId'     => $user['id'],
                        'nama'       => $user['nama'],
                        'email'      => $user['email'],
                        'role'       => $user['role_kode'],
                        'role_nama'  => $user['role_nama'],
                    ]);
                    return;
                }
            }
            return redirect()->to('auth/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if (!empty($arguments)) {
            $userRole = $session->get('role');
            if (!in_array($userRole, $arguments, true)) {
                return redirect()->to('auth/login')->with('error', 'Akses ditolak.');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
