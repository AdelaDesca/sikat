<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Satu-satunya akun yang dapat mengakses sistem.
        User::create([
            'username' => 'admin',
            'password' => 'admin123',
        ]);

        $this->call(BarangSeeder::class);
    }
}
