<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UserModel;
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

        $token = $this->generateToken($user);

        return $this->respond(['token' => $token]);
    }

    private function generateToken($user)
{
    $key = getenv('JWT_SECRET_KEY') ?: 'default-secret-key';
    $algorithm = 'HS256';
    $accessTokenExp = time() + 3600; 
    $refreshTokenExp = time() + (30 * 24 * 3600); 

    $payload = [
        'iss' => 'your-issuer',
        'sub' => $user['id'],
        'iat' => time(),
        'exp' => $accessTokenExp,
    ];

    $accessToken = JWT::encode($payload, $key, $algorithm);

    $payload['exp'] = $refreshTokenExp;
    $refreshToken = JWT::encode($payload, $key, $algorithm);

    return ['access_token' => $accessToken, 'refresh_token' => $refreshToken];
}

}
