<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          $types = ['Truyện ngắn', 'Truyện dài', 'Thơ', 'Tản văn'];
          foreach ($types as $name) {
        \App\Models\Type::firstOrCreate(['name' => $name]);
    }
    }
}
