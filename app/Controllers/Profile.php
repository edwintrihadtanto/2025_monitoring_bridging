<?php

namespace App\Controllers;

use App\Models\UserModel;

class Profile extends BaseController
{
    public function index()
    {
        $userModel = new UserModel();
        $userId = session()->get('id');

        // Ambil data user saat ini
        $user = $userModel->find($userId);

        $data = [
            'user' => $user,
            'validation' => \Config\Services::validation()
        ];

        return $this->renderView('profile/index', $data);
    }

    public function updatePassword()
    {
        $userModel = new UserModel();
        $userId = session()->get('id');
        $user = $userModel->find($userId);

        // 1. Validasi Input
        $rules = [
            'full_name'  => 'required',
            'current_password' => 'required',
            'new_password'     => 'required|min_length[6]',
            'confirm_password'  => 'required|matches[new_password]'
        ];

        if (!$this->validate($rules)) {
            // Jika validasi gagal, kembalikan error JSON
            return $this->response->setJSON([
                'status' => false,
                'message' => implode('<br>', $this->validator->getErrors())
            ]);
        }

        // 2. Cek Password Lama
        $currentPass = $this->request->getPost('current_password');
        if (!password_verify($currentPass, $user['password_hash'])) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Password saat ini salah!'
            ]);
        }

        // 3. Update Password Baru
        $newPass = $this->request->getPost('new_password');
        $userModel->update($userId, [
            'password_hash' => password_hash($newPass, PASSWORD_DEFAULT),
            'full_name'  => $this->request->getPost('full_name')
        ]);

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Password berhasil diubah! Silakan login ulang nanti.'
        ]);
    }
}