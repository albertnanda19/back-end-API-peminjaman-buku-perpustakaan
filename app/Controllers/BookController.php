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

    public function borrowBook($userId, $bookId, $jumlah)
    {
        $memberModel = new MemberModel();
        $bookModel = new BookModel();

        $user = $memberModel->find($userId);
        $book = $bookModel->find($bookId);

        if (!$user || !$book) {
            return $this->failNotFound('User or Book not found');
        }

        if ($book['status'] === 'unavailable' || $book['jumlah'] < $jumlah) {
            return $this->fail('Book is not available for borrowing or insufficient stock');
        }

        $peminjamanModel = new PeminjamanModel();

        $data = [
            'id_buku' => $bookId,
            'nama_peminjam' => $user['username'],
            'status' => 'pending',
            'tanggal_peminjaman' => date('Y-m-d'),
            'tanggal_pengembalian' => date('Y-m-d', strtotime('+7 days')),
            'jumlah' => $jumlah,
        ];

        $peminjamanModel->insert($data);

        return $this->respond(['message' => 'Borrow request submitted successfullyss']);
    }
}
