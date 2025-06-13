<?php

namespace Database\Seeders;

use App\Models\Faculty;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy ID của các khoa
        $faculties = Faculty::all();
        
        // Lấy tài khoản giáo viên
        $teacherUsers = User::where('role', 'teacher')->get();
        
        $i = 0;
        foreach ($faculties as $faculty) {
            // Tạo giáo viên cho mỗi khoa
            for ($j = 1; $j <= 3; $j++) {
                $gender = $j % 2 == 0 ? 'Nam' : 'Nữ';
                $userId = null;
                
                // Gán tài khoản người dùng nếu còn
                if ($i < $teacherUsers->count()) {
                    $userId = $teacherUsers[$i]->id;
                    $i++;
                }
                
                Teacher::create([
                    'teacher_id' => 'GV' . str_pad($faculty->id * 10 + $j, 5, '0', STR_PAD_LEFT),
                    'first_name' => 'Giáo viên',
                    'last_name' => $faculty->code . ' ' . $j,
                    'date_of_birth' => date('Y-m-d', strtotime('-' . (25 + $j) . ' years')),
                    'gender' => $gender,
                    'email' => 'gv' . $faculty->code . $j . '@example.com',
                    'phone' => '098' . str_pad($faculty->id, 3, '0', STR_PAD_LEFT) . str_pad($j, 4, '0', STR_PAD_LEFT),
                    'address' => 'Địa chỉ giáo viên ' . $j . ', ' . $faculty->name,
                    'faculty_id' => $faculty->id,
                    'user_id' => $userId,
                ]);
            }
        }
    }
}
