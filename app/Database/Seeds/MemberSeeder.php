<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MemberSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'username' => 'user1',
                'email'    => 'member1@example.com',
                'password' => password_hash('password1', PASSWORD_DEFAULT),
            ],
            [
                'username' => 'user2',
                'email'    => 'member2@example.com',
                'password' => password_hash('password2', PASSWORD_DEFAULT),
            ],
        ];

        $this->db->table('members')->insertBatch($data);
    }
}
