<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;


class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test Admin',
            'username' => 'admin',
            'password' =>  bcrypt('admin'),
            'role' => 'Admin',
            'remember_token' => Str::random(10),
        ]);
        User::factory()->create([
            'name' => 'Test Wali',
            'username' => 'wali',
            'password' =>  bcrypt('wali'),
            'role' => 'Wali Kelas',
            'remember_token' => Str::random(10),
        ]);
        User::factory()->create([
            'name' => 'Test Guru',
            'username' => 'guru',
            'password' =>  bcrypt('guru'),
            'role' => 'Guru',
            'remember_token' => Str::random(10),
        ]);
    }
}
