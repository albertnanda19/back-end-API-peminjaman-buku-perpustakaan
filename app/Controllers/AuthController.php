<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UserModel;
use App\Models\AdminModel;
use \Firebase\JWT\JWT;

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

        $accessTokenExp = time() + 3600;
        $refreshTokenExp = time() + (30 * 24 * 3600);

        $accessToken = $this->generateToken($user['id'], $accessTokenExp);
        $refreshToken = $this->generateToken($user['id'], $refreshTokenExp);

        return $this->respond(['access_token' => $accessToken, 'refresh_token' => $refreshToken]);
    }

    public function adminLogin()
    {
        $model = new AdminModel();
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');

        $admin = $model->where('username', $username)->first();

        if (!$admin || !password_verify($password, $admin['password'])) {
            return $this->failUnauthorized('Invalid Login Credentials');
        }

        $accessTokenExp = time() + 3600;
        $refreshTokenExp = time() + (30 * 24 * 3600);

        $accessToken = $this->generateToken($admin['id'], $accessTokenExp);
        $refreshToken = $this->generateToken($admin['id'], $refreshTokenExp);

        return $this->respond(['access_token' => $accessToken, 'refresh_token' => $refreshToken]);
    }

    private function generateToken($userId, $expiration)
    {
        $privateKey = file_get_contents('../private_key.pem');
        $algorithm = 'RS256';

        $payload = [
            'iss' => 'Perpustakaan',
            'sub' => $userId,
            'iat' => time(),
            'exp' => $expiration,
        ];

        return JWT::encode($payload, $privateKey, $algorithm);
    }
}
