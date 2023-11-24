<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Models\BookModel;

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
}
