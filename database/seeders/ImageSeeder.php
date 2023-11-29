<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $images= [
            [
                'imageable_id' => 1,
                'image' => 'images/books/Ella.png',
            ],
            [
                'imageable_id' => 1,
                'image' => 'images/books/parent.png',
            ],
            [
                'imageable_id' => 2,
                'image' => 'images/books/Ella.png',
            ],
            [
                'imageable_id' => 2,
                'image' => 'images/books/Ella.png',
            ],
            [
                'imageable_id' => 2,
                'image' => 'images/books/Ella.png',
            ],

        ];
        foreach ($images as $image) {
            DB::table('images')->insert([
                'imageable_id' => $image['imageable_id'],
                'image' => $image['image'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
