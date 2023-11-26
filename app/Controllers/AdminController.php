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

    public function addBook()
    {
        $request = $this->request;

        $validation = \Config\Services::validation();
        $validation->setRules([
            'judul' => 'required|max_length[255]',
            'pengarang' => 'required|max_length[255]',
            'kategori' => 'required|max_length[255]'
        ]);

        if (!$validation->withRequest($request)->run()) {
            return $this->fail($validation->getErrors(), 400);
        }

        $judul = $request->getVar('judul');
        $pengarang = $request->getVar('pengarang');
        $kategori = $request->getVar('kategori');

        $bookModel = new BookModel();
        $data = [
            'judul' => $judul,
            'pengarang' => $pengarang,
            'kategori' => $kategori,
            'status' => 'available',
        ];

        $bookModel->insert($data);

        return $this->respond(['message' => 'Book added successfully']);
    }

    public function editBook($bookId)
    {
        $request = $this->request;

        $bookModel = new BookModel();
        $book = $bookModel->find($bookId);

        if (!$book) {
            return $this->failNotFound('Book not found');
        }

        $judul = $request->getVar('judul');
        $pengarang = $request->getVar('pengarang');
        $kategori = $request->getVar('kategori');

        $validation = \Config\Services::validation();
        $rules = [];

        if (!empty($judul)) {
            $rules['judul'] = 'required|max_length[255]';
        }

        if (!empty($pengarang)) {
            $rules['pengarang'] = 'required|max_length[255]';
        }

        if (!empty($kategori)) {
            $rules['kategori'] = 'required|max_length[255]';
        }

        $validation->setRules($rules);

        if (!$validation->withRequest($request)->run()) {
            return $this->fail($validation->getErrors(), 400);
        }

        $updateData = [];

        if (!empty($judul)) {
            $updatedData['judul'] = $judul;
        }

        if (!empty($pengarang)) {
            $updatedData['pengarang'] = $pengarang;
        }

        if (!empty($kategori)) {
            $updatedData['kategori'] = $kategori;
        }

        $bookModel->update($bookId, $updatedData);

        return $this->respond(['message' => 'Book updated sucessfully']);
    }

    public function deleteBook($bookId)
    {
        $bookModel = new BookModel();

        $book = $bookModel->find($bookId);

        if (!$book) {
            return $this->failNotFound('Book not found');
        }

        $bookModel->delete($bookId);

        return $this->respond(['message' => 'Book deleted successfully']);
    }
}
