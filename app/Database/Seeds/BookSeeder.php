<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run()
    {
        $faker = \Faker\Factory::create();

        for ($i = 1; $i <= 10; $i++) {
            $data = [
                'judul' => $faker->sentence(3),
                'pengarang' => $faker->name,
                'kategori' => $faker->word,
                'status' => 'available',
            ];

            $this->db->table('books')->insert($data);
        }
    }
}
