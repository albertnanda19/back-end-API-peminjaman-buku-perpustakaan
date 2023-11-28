<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Models\MemberModel;
use App\Models\PeminjamanModel;

class MemberController extends BaseController
{
    use ResponseTrait;

    public function updateUsername($userId)
    {
        $memberModel = new MemberModel();

        $member = $memberModel->find($userId);

        if (!$member) {
            return $this->failNotFound('Member not found');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'username' => 'required|min_length[3]|max_length[255]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->fail($validation->getErrors());
        }

        $newUsername = $this->request->getVar('username');
        $memberModel->update($userId, ['username' => $newUsername]);

        return $this->respond(['message' => 'Username updated successfully']);
    }

    public function getPeminjamanUser($username)
    {
        $peminjamaModel = new PeminjamanModel();

        $peminjaman = $peminjamaModel->where('nama_peminjam', $username)->findAll();

        if (empty($peminjaman)) {
            return $this->respond(['message' => 'No peminjaman found for this user.']);
        }

        return $this->respond(['peminjaman' => $peminjaman]);
    }

    public function getPeminjamanByUserId($userId)
    {
        $peminjamanModel = new PeminjamanModel();
        $memberModel = new MemberModel();

        $user = $memberModel->find($userId);

        if (!$user) {
            return $this->failNotFound('User not found');
        }

        $peminjaman = $peminjamanModel
            ->where('nama_peminjam', $user['username'])
            ->where('status !=', 'returned')
            ->findAll();

        if (empty($peminjaman)) {
            return $this->respond(['message' => 'No peminjaman found for this user.']);
        }

        return $this->respond(['peminjaman' => $peminjaman]);
    }

    public function getHistoryPeminjaman($userId)
    {
        $peminjamanModel = new PeminjamanModel();
        $memberModel = new MemberModel();

        $user = $memberModel->find($userId);

        if (!$user) {
            return $this->fail('User tidak ditemukan');
        }

        $returnedPeminjaman = $peminjamanModel->where('nama_peminjam', $user['username'])->where('status', 'returned')->findAll();

        if (empty($returnedPeminjaman)) {
            return $this->respond(['message' => 'User ini belum meminjam buku']);
        }

        return $this->respond(['returnedpeminjaman' => $returnedPeminjaman]);
    }
}
