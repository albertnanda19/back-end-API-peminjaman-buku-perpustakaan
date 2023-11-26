<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePeminjamanTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_buku' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
            ],
            'nama_peminjam' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'approve', 'reject'],
                'default' => 'pending',
            ],
            'tanggal_peminjaman' => [
                'type' => 'DATE',
            ],
            'tanggal_pengembalian' => [
                'type' => 'DATE',
            ],
            'jumlah' => [
                'type' => 'INT',
                'default' => 1,
            ]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_buku', 'books', 'id');
        $this->forge->createTable('peminjaman');
    }

    public function down()
    {
        $this->forge->dropTable('peminjaman');
    }
}
