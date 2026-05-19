<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        if (session()->get('logged_in')) {
            return redirect()->to('/dashboard');
        }

        /*$data = [
            'validation' => \Config\Services::validation(),
            'captcha_question' => $this->generateCaptcha() // Panggil fungsi captcha
        ];

        return view('auth/login', $data);*/
        try {

            // test koneksi DB ringan (optional tapi bagus)
            db_connect()->connect();

        } catch (\Throwable $e) {

            return view('auth/login', [
                'validation' => \Config\Services::validation(),
                'captcha_question' => $this->generateCaptcha(),
                'db_error' => true
            ]);
        }

        return view('auth/login', [
            'validation' => \Config\Services::validation(),
            'captcha_question' => $this->generateCaptcha(),
            'db_error' => false
        ]);
    }

    // Fungsi Helper Captcha (Private)
    private function generateCaptcha()
    {
        $angka1 = rand(3, 20);
        $angka2 = rand(3, 20);
        $hasil   = $angka1 + $angka2;

        // Simpan hasil jawaban ke session
        session()->set('captcha_login', $hasil);

        // Kembalikan string pertanyaan
        return "$angka1 + $angka2";
    }

    public function attemptLogin()
    {
        $userModel = new UserModel();
        $username   = $this->request->getPost('username');
        $password   = $this->request->getPost('password');
        $captcha    = $this->request->getPost('captcha'); // Ambil input captcha

        $sessionCaptcha = session()->get('captcha_login');
        
        if ($captcha != $sessionCaptcha) {
            session()->setFlashdata('error', 'Security Code (Captcha) salah! Silakan coba lagi.');
            return redirect()->to('/login')->withInput();
        }

        $user = $userModel->getUserWithRule($username);
        if (!$user) {
            session()->setFlashdata('error', 'Username atau Password salah.');
            return redirect()->to('/login')->withInput();
        }

        if (password_verify($password, $user['password_hash'])) {
            $sessionData = [
                'id'        => $user['id'],
                'username'  => $user['username'],
                'name'      => $user['full_name'],
                'logged_in' => TRUE,
                'genre'     => $user['genre'],
                'rule_name' => $user['rule_name']
            ];
            session()->set($sessionData);

            return redirect()->to('/dashboard');
        } else {
            session()->setFlashdata('error', 'Username atau Password salah.');
            return redirect()->to('/login')->withInput();
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
        // return view('auth/login', $data);
    }
}