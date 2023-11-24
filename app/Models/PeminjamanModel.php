<?php

namespace App\Models;

use CodeIgniter\Model;

class PeminjamanModel extends Model
{
    protected $table = 'peminjaman';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_buku', 'nama_peminjam', 'status', 'tanggal_peminjaman', 'tanggal_pengembalian'];

    public function getApprovedBooks()
    {
        return $this->where('status', 'approve')->findAll();
    }
}
