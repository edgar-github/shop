<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (config('app.env') === 'local') {
            $seedData = [
                BookSeeder::class,
                ImageSeeder::class,
                UserRoleSeeder::class,
                UserSeeder::class,
            ];
        } else if (config('app.env') === 'production') {
            $seedData = [
                UserRoleSeeder::class,
                UserSeeder::class,
            ];
        }

        if (!empty($seedData))
            $this->call($seedData);
    }
}
