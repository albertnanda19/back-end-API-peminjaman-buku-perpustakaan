<?php

namespace App\Models;

use CodeIgniter\Model;

class BookModel extends Model
{
    protected $table = 'buku';
    protected $primaryKey = 'id';
    protected $allowedFields = ['judul', 'pengarang', 'kategori', 'tahun_terbit', 'deskripsi', 'status', 'created_at', 'updated_at'];

    protected $useTimestamps = true;

    public function getBookById($id)
    {
        return $this->find($id);
    }

}
