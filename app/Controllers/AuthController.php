<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UserModel;

class AuthController extends ResourceController
{
    protected $modelName = 'App\Models\UserModel';
    protected $format = 'json';

    public function register()
    {
        $rules = [
            'username' => 'required|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]',
            'confirm_password' => 'required|matches[password]'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $model = new UserModel();
        $data = [
            'username' => $this->request->getVar('username'),
            'email' => $this->request->getVar('email'),
            'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT)
        ];

        $model->insert($data);

        return $this->respondCreated(['message' => 'User registered successfully']);
    }

    public function login()
    {
        $model = new UserModel();
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');

        $user = $model->where('username', $username)->first();

        if (!$user || !password_verify($password, $user['password'])) {
            return $this->failUnauthorized('Invalid Login Credentials');
        }

        return $this->respond(['message' => 'Login Berhasil']);
    }
}
