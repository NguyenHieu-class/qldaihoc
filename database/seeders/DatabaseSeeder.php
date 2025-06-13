<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Gọi các seeder theo thứ tự phụ thuộc
        $this->call([
            UserSeeder::class,         // Tạo tài khoản người dùng
            FacultySeeder::class,      // Tạo khoa
            MajorSeeder::class,        // Tạo ngành học (phụ thuộc vào khoa)
            ClassSeeder::class,        // Tạo lớp học (phụ thuộc vào ngành học)
            SubjectSeeder::class,      // Tạo môn học
            TeacherSeeder::class,      // Tạo giáo viên (phụ thuộc vào khoa và tài khoản)
            StudentSeeder::class,      // Tạo sinh viên (phụ thuộc vào lớp học và tài khoản)
            GradeSeeder::class,        // Tạo điểm số (phụ thuộc vào sinh viên và môn học)
        ]);
    }
}
