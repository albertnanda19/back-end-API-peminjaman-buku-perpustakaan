<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run()
    {
        $faker = \Faker\Factory::create();

        for ($i = 1; $i <= 500; $i++) {
            $data = [
                'judul' => $faker->sentence(3),
                'pengarang' => $faker->name,
                'kategori' => $faker->word,
                'status' => 'available',
                'jumlah' => $faker->numberBetween(10, 100),
            ];

            $this->db->table('books')->insert($data);
        }
    }
}
