<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@odikshop.com'],
            [
                'name' => 'Odik Admin',
                'phone' => '01700000000',
                'password' => Hash::make('admin1234'),
                'is_admin' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'user@odikshop.com'],
            [
                'name' => 'Demo User',
                'phone' => '01800000000',
                'password' => Hash::make('user1234'),
                'is_admin' => false,
            ]
        );
    }
}
