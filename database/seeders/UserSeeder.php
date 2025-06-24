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
        // Tạo tài khoản admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Tạo tài khoản trưởng khoa mặc định
        User::create([
            'name' => 'Trưởng khoa',
            'email' => 'head@example.com',
            'password' => Hash::make('password'),
            'role' => 'department_head',
        ]);

        // Tạo tài khoản giáo viên
        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'name' => "Giáo viên $i",
                'email' => "teacher$i@example.com",
                'password' => Hash::make('password'),
                'role' => 'teacher',
            ]);
        }

        // Tạo tài khoản sinh viên
        for ($i = 1; $i <= 20; $i++) {
            User::create([
                'name' => "Sinh viên $i",
                'email' => "student$i@example.com",
                'password' => Hash::make('password'),
                'role' => 'student',
            ]);
        }
    }
}
