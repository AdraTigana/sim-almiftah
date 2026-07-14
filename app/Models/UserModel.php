<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'email', 'password', 'nama', 'nip', 'role_id', 'is_active', 'avatar',
        'remember_token', 'remember_expires'
    ];
    protected $useTimestamps    = true;
    protected $useSoftDeletes   = true;

    protected $validationRules  = [
        'email'    => 'required|valid_email|is_unique[users.email,id,{id}]',
        'password' => 'required|min_length[6]',
        'nama'     => 'required|min_length[3]',
        'role_id'  => 'required|numeric',
    ];

    public function getUserWithRole(int $id): ?array
    {
        return $this->select('users.*, roles.kode as role_kode, roles.nama as role_nama')
                    ->join('roles', 'roles.id = users.role_id')
                    ->find($id);
    }

    public function findByEmail(string $email): ?array
    {
        return $this->select('users.*, roles.kode as role_kode, roles.nama as role_nama')
                    ->join('roles', 'roles.id = users.role_id')
                    ->where('users.email', $email)
                    ->where('users.is_active', 1)
                    ->first();
    }

    public function findByRememberToken(string $token): ?array
    {
        return $this->select('users.*, roles.kode as role_kode, roles.nama as role_nama')
                    ->join('roles', 'roles.id = users.role_id')
                    ->where('users.remember_token', $token)
                    ->where('users.remember_expires >=', date('Y-m-d H:i:s'))
                    ->where('users.is_active', 1)
                    ->first();
    }
}
