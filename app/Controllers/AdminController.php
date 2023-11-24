<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Models\PeminjamanModel;
use App\Models\BookModel;

class AdminController extends BaseController
{
    use ResponseTrait;

    public function approvePeminjaman($peminjamanId)
    {
        // Pastikan bahwa yang melakukan akses adalah admin
        $decodedToken = $this->request->decodedToken;
        if ($decodedToken->role !== 'admin') {
            return $this->failForbidden('Access denied');
        }

        // Proses persetujuan peminjaman
        $peminjamanModel = new PeminjamanModel();
        $bookModel = new BookModel();

        $peminjaman = $peminjamanModel->find($peminjamanId);

        if (!$peminjaman) {
            return $this->failNotFound('Peminjaman not found');
        }

        // Lakukan proses persetujuan peminjaman
        $peminjamanModel->update($peminjamanId, ['status' => 'approve']);

        // Update status buku yang terkait menjadi 'unavailable'
        $bookModel->update($peminjaman['id_buku'], ['status' => 'unavailable']);

        return $this->respond(['message' => 'Peminjaman approved successfully']);
    }
}
