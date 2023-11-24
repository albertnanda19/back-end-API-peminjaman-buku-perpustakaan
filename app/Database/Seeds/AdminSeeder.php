<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'username' => 'admin1',
                'email'    => 'admin1@example.com',
                'password' => password_hash('adminpassword1', PASSWORD_DEFAULT),
            ],
            [
                'username' => 'admin2',
                'email'    => 'admin2@example.com',
                'password' => password_hash('adminpassword2', PASSWORD_DEFAULT),
            ],
        ];

        $this->db->table('admins')->insertBatch($data);
    }
}
