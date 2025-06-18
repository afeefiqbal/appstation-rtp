<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name'     => 'Admin',
            'email'    => 'admin@rtb.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);

        // Normal User
        User::create([
            'name'     => 'Bidder One',
            'email'    => 'user@rtb.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
        ]);
    }
}
