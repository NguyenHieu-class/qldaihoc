<?php

namespace Database\Seeders;

use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy danh sách sinh viên
        $students = Student::all();
        
        // Lấy danh sách môn học
        $subjects = Subject::all();
        
        // Học kỳ và năm học
        $semesters = ['Học kỳ 1', 'Học kỳ 2'];
        $currentYear = date('Y');
        
        foreach ($students as $student) {
            // Lấy ngẫu nhiên 5-10 môn học cho mỗi sinh viên
            $randomSubjects = $subjects->random(rand(5, 10));
            
            foreach ($randomSubjects as $index => $subject) {
                // Xác định học kỳ và năm học
                $semester = $semesters[$index % 2];
                $academicYear = $currentYear - ($index % 2);
                
                // Tạo điểm ngẫu nhiên
                $midtermScore = rand(5, 10);
                $finalScore = rand(5, 10);
                $assignmentScore = rand(5, 10);
                
                // Tính điểm tổng kết
                $totalScore = $midtermScore * 0.3 + $finalScore * 0.6 + $assignmentScore * 0.1;
                
                Grade::create([
                    'student_id' => $student->id,
                    'subject_id' => $subject->id,
                    'midterm_score' => $midtermScore,
                    'final_score' => $finalScore,
                    'assignment_score' => $assignmentScore,
                    'total_score' => $totalScore,
                    'semester' => $semester,
                    'academic_year' => $academicYear,
                ]);
            }
        }
    }
}
