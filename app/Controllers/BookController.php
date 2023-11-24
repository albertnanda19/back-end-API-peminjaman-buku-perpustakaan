<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Models\BookModel;
use App\Models\PeminjamanModel;
use App\Models\MemberModel;

class BookController extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        $bookModel = new BookModel();
        $books = $bookModel->findAll();

        return $this->respond(['books' => $books]);
    }

    public function show($id)
    {
        $bookModel = new BookModel();
        $book = $bookModel->find($id);

        if (!$book) {
            return $this->failNotFound('Book not found');
        }

        return $this->respond(['book' => $book]);
    }

    public function borrowBook($userId, $bookId)
    {
        $memberModel = new MemberModel();
        $bookModel = new BookModel();

        $user = $memberModel->find($userId);
        $book = $bookModel->find($bookId);

        if (!$user || !$book) {
            return $this->failNotFound('User or Book not found');
        }

        if ($book['status'] === 'unavailable') {
            return $this->fail('Book is not available for borrowing');
        }

        $peminjamanModel = new PeminjamanModel();
        $data = [
            'id_buku' => $bookId,
            'nama_peminjam' => $user['username'],
            'tanggal_peminjaman' => date('Y-m-d'),
            'tanggal_pengembalian' => date('Y-m-d', strtotime('+7 days')),
            'status' => 'pending',
        ];

        $peminjamanModel->insert($data);

        $bookModel->update($bookId, ['status' => 'unavailable']);

        return $this->respond(['message' => 'Borrow request submitted successfully']);
    }
}
