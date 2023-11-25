<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Models\MemberModel;

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
}
