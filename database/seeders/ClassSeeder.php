<?php

namespace Database\Seeders;

use App\Models\Classes;
use App\Models\Major;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy ID của các ngành
        $majors = Major::all();
        
        $currentYear = date('Y');
        
        foreach ($majors as $major) {
            // Tạo 2 lớp cho mỗi ngành với các năm khác nhau
            for ($i = 1; $i <= 2; $i++) {
                $year = $currentYear - $i + 1;
                
                Classes::create([
                    'name' => $major->code . $year . $i,
                    'code' => $major->code . $year . $i,
                    'major_id' => $major->id,
                    'year' => $year,
                ]);
            }
        }
    }
}
