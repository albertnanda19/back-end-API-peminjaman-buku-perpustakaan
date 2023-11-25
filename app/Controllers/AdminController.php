<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Models\PeminjamanModel;
use App\Models\BookModel;
use App\Models\MemberModel;

class AdminController extends BaseController
{
    use ResponseTrait;

    public function approvePeminjaman($peminjamanId)
    {

        $peminjamanModel = new PeminjamanModel();
        $bookModel = new BookModel();

        $peminjaman = $peminjamanModel->find($peminjamanId);

        if (!$peminjaman) {
            return $this->failNotFound('Peminjaman not found');
        }

        $peminjamanModel->update($peminjamanId, ['status' => 'approve']);

        $bookModel->update($peminjaman['id_buku'], ['status' => 'unavailable']);

        return $this->respond(['message' => 'Peminjaman approved successfully']);
    }

    public function rejectPeminjaman($peminjamanId)
    {
        $peminjamanModel = new PeminjamanModel();
        $bookModel = new BookModel();

        $peminjaman = $peminjamanModel->find($peminjamanId);

        if (!$peminjaman) {
            return $this->failNotFound('Peminjaman not found');
        }

        $peminjamanModel->update($peminjamanId, ['status' => 'reject']);

        if ($peminjaman['status'] === 'approve') {
            $bookModel->update($peminjaman['id_buku'], ['status' => 'available']);
        }

        return $this->respond(['message' => 'Peminjaman rejected']);
    }

    public function clearRejectedPeminjaman()
    {
        $peminjamanModel = new PeminjamanModel();
        $bookModel = new BookModel();

        $rejectedPeminjaman = $peminjamanModel->where('status', 'reject')->findAll();

        if (empty($rejectedPeminjaman)) {
            return $this->respond(['message' => 'No rejected peminjaman found.']);
        }

        foreach ($rejectedPeminjaman as $peminjaman) {
            $peminjamanModel->delete($peminjaman['id']);

            $bookModel->update($peminjaman['id_buku'], ['status' => 'available']);
        }

        return $this->respond(['message' => 'All rejected peminjaman cleared successfully']);
    }

    public function getAllMembers()
    {
        $memberModel = new MemberModel();

        // $members = $memberModel->findAll();
        $members = $memberModel->select('id, username, email, created_at')->findAll();

        return $this->respond(['members' => $members]);
    }

    public function deleteMember($userId)
    {
        $memberModel = new MemberModel();

        $member = $memberModel->find($userId);

        if (!$member) {
            return $this->failNotFound("Member nof found");
        }

        $memberModel->delete($userId);

        return $this->respond(['message' => 'Member account deleted successfully']);
    }
}
