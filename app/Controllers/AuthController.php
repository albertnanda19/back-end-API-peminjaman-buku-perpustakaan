<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Controller;
use App\Models\MemberModel;
use App\Models\AdminModel;
use Firebase\JWT\JWT;

class AuthController extends Controller
{
    use ResponseTrait;

    public function memberLogin()
    {
        $request = $this->request->getBody();
        $data = json_decode($request);

        $validation = \Config\Services::validation();
        $validation->setRules([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (!$validation->run((array) $data)) {
            return $this->failValidationErrors($validation->getErrors());
        }

        $memberModel = new MemberModel();
        $member = $memberModel->where('username', $data->username)->first();

        if (!$member || !password_verify($data->password, $member['password'])) {
            return $this->failUnauthorized('Invalid credentials');
        }

        $token = $this->generateToken($member['id'], $member['username'], 'member');

        return $this->respond(['token' => $token]);
    }

    public function adminLogin()
    {
        $request = $this->request->getBody();
        $data = json_decode($request);

        $validation = \Config\Services::validation();
        $validation->setRules([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (!$validation->run((array) $data)) {
            return $this->failValidationErrors($validation->getErrors());
        }

        $adminModel = new AdminModel();
        $admin = $adminModel->where('username', $data->username)->first();

        if (!$admin || !password_verify($data->password, $admin['password'])) {
            return $this->failUnauthorized('Invalid credentials');
        }

        $token = $this->generateToken($admin['id'], $admin['username'], 'admin');

        return $this->respond(['token' => $token]);
    }

    private function generateToken($id, $username, $role)
    {
        $key = getenv('JWT_SECRET');
        $issuedAt = time();
        $expirationTime = $issuedAt + 3600;
        $payload = [
            'id' => $id,
            'username' => $username,
            'role' => $role,
            'iat' => $issuedAt,
            'exp' => $expirationTime,
        ];
        $token = JWT::encode($payload, $key, 'HS256');

        return $token;
    }

    public function memberRegister()
    {
        $request = $this->request->getBody();
        $data = json_decode($request);

        $validation = \Config\Services::validation();
        $validation->setRules([
            'username' => 'required',
            'email' => 'required|valid_email|is_unique[members.email]',
            'password' => 'required|min_length[8]',
        ]);

        if (!$validation->run((array) $data)) {
            return $this->fail($validation->getErrors());
        }

        $memberModel = new MemberModel();

        if ($memberModel->where('email', $data->email)->countAllResults() > 0) {
            return $this->fail('Email is already registered');
        }

        $hashedPassword = password_hash($data->password, PASSWORD_BCRYPT);

        $memberData = [
            'username' => $data->username,
            'email' => $data->email,
            'password' => $hashedPassword,
        ];
        $memberModel->insert($memberData);

        return $this->respond(['message' => 'Member registered successfully']);
    }

    public function updateUsername($userId)
    {
        $memberModel = new MemberModel();

        $member = $memberModel->find($userId);

        if (!$member) {
            return $this->failNotFound('Member not found');
        }

        $requestData = $this->request->getBody();

        $validation = \Config\Services::validation();
        $validation->setRules([
            'username' => 'required|min_length[3]|max_length[255]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->fail($validation->getErrors());
        }

        $memberModel->update($userId, ['username' => $requestData['username']]);

        return $this->respond(['message' => 'Username updated successfully']);
    }
}
