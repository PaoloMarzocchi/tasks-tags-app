<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'demo@local'],
            [
                'name' => 'Demo User',
                'password' => Hash::make('demo1234'),
            ]
        );
    }
}
