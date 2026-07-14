<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        if (is_logged_in()) {
            return redirect()->to($this->_redirectByRole());
        }

        if ($this->request->getMethod() === 'POST') {
            if ($msg = $this->_checkRateLimit()) {
                return redirect()->back()->withInput()->with('error', $msg);
            }

            $email    = $this->request->getPost('email');
            $password = $this->request->getPost('password');
            $remember = $this->request->getPost('remember');

            $userModel = new UserModel();
            $user      = $userModel->findByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                session()->remove('login_attempts');

                session()->set([
                    'isLoggedIn' => true,
                    'userId'     => $user['id'],
                    'nama'       => $user['nama'],
                    'email'      => $user['email'],
                    'role'       => $user['role_kode'],
                    'role_nama'  => $user['role_nama'],
                ]);

                if ($remember) {
                    $token = bin2hex(random_bytes(64));
                    $userModel->update($user['id'], [
                        'remember_token'   => $token,
                        'remember_expires' => date('Y-m-d H:i:s', strtotime('+30 days')),
                    ]);
                    $this->response->setCookie('remember_token', $token, 60 * 60 * 24 * 30);
                }

                return redirect()->to($this->_redirectByRole())->with('message', 'Selamat datang, ' . $user['nama']);
            }

            $this->_recordFailedAttempt();
            return redirect()->back()->withInput()->with('error', 'Email atau password salah.');
        }

        return view('auth/login');
    }

    private function _checkRateLimit(): ?string
    {
        $attempts = session()->get('login_attempts') ?? [];
        $this->_cleanOldAttempts($attempts);
        session()->set('login_attempts', $attempts);

        if (count($attempts) >= 5) {
            $oldest = $attempts[0];
            $wait = 60 - (time() - $oldest);
            return "Terlalu banyak percobaan. Coba lagi dalam {$wait} detik.";
        }

        return null;
    }

    private function _recordFailedAttempt(): void
    {
        $attempts = session()->get('login_attempts') ?? [];
        $this->_cleanOldAttempts($attempts);
        $attempts[] = time();
        session()->set('login_attempts', $attempts);
    }

    private function _cleanOldAttempts(array &$attempts): void
    {
        $cutoff = time() - 60;
        $attempts = array_values(array_filter($attempts, fn($t) => $t > $cutoff));
    }

    public function logout()
    {
        $userId = session()->get('userId');
        if ($userId) {
            $userModel = new UserModel();
            $userModel->update($userId, ['remember_token' => null, 'remember_expires' => null]);
        }
        $this->response->deleteCookie('remember_token');
        session()->destroy();
        return redirect()->to('auth/login')->with('message', 'Berhasil logout.');
    }

    private function _redirectByRole(): string
    {
        $role = session()->get('role');
        return match ($role) {
            'admin' => 'admin',
            'guru'  => 'guru',
            'walas' => 'walas',
            default => 'auth/login',
        };
    }
}
