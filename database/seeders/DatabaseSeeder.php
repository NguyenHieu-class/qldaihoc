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
            AcademicYearSeeder::class, // Tạo năm học
            SemesterSeeder::class,     // Tạo học kỳ (phụ thuộc năm học)
            TeachingRateSeeder::class, // Tạo mức lương giảng dạy
            ClassSizeCoefficientSeeder::class, // Tạo hệ số sĩ số lớp
            CourseOfferingSeeder::class, // Tạo các học phần và lớp học phần
            EnrollmentSeeder::class,  // Đăng ký lớp học phần
            GradeSeeder::class,        // Tạo điểm số (phụ thuộc vào sinh viên và môn học)
        ]);
    }
}
