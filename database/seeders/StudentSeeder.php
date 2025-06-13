<?php

namespace Database\Seeders;

use App\Models\Classes;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy danh sách lớp học
        $classes = Classes::all();
        
        // Lấy tài khoản sinh viên
        $studentUsers = User::where('role', 'student')->get();
        
        $i = 0;
        foreach ($classes as $class) {
            // Tạo sinh viên cho mỗi lớp
            for ($j = 1; $j <= 10; $j++) {
                $gender = $j % 2 == 0 ? 'Nam' : 'Nữ';
                $userId = null;
                
                // Gán tài khoản người dùng nếu còn
                if ($i < $studentUsers->count()) {
                    $userId = $studentUsers[$i]->id;
                    $i++;
                }
                
                Student::create([
                    'student_id' => 'SV' . str_pad($class->id * 100 + $j, 7, '0', STR_PAD_LEFT),
                    'first_name' => 'Sinh viên',
                    'last_name' => $class->code . ' ' . $j,
                    'date_of_birth' => date('Y-m-d', strtotime('-' . (18 + $j % 5) . ' years')),
                    'gender' => $gender,
                    'email' => 'sv' . $class->code . $j . '@example.com',
                    'phone' => '097' . str_pad($class->id, 3, '0', STR_PAD_LEFT) . str_pad($j, 4, '0', STR_PAD_LEFT),
                    'address' => 'Địa chỉ sinh viên ' . $j . ', Lớp ' . $class->name,
                    'class_id' => $class->id,
                    'user_id' => $userId,
                ]);
            }
        }
    }
}
