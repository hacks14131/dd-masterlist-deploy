<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class CreateAdminUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            User::create([
                'username'   => 'admin' . $i,
                'password'   => Hash::make('admin' . $i),
                'name'       => 'admin' . $i,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
