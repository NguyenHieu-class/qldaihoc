<?php

namespace Database\Seeders;

use App\Models\Faculty;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FacultySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faculties = [
            [
                'name' => 'Khoa Công nghệ thông tin',
                'code' => 'CNTT',
                'description' => 'Khoa đào tạo các ngành liên quan đến công nghệ thông tin, khoa học máy tính, kỹ thuật phần mềm.',
            ],
            [
                'name' => 'Khoa Kinh tế',
                'code' => 'KT',
                'description' => 'Khoa đào tạo các ngành liên quan đến kinh tế, quản trị kinh doanh, tài chính ngân hàng.',
            ],
            [
                'name' => 'Khoa Ngoại ngữ',
                'code' => 'NN',
                'description' => 'Khoa đào tạo các ngành liên quan đến ngôn ngữ Anh, Pháp, Trung, Nhật, Hàn.',
            ],
            [
                'name' => 'Khoa Điện - Điện tử',
                'code' => 'DĐT',
                'description' => 'Khoa đào tạo các ngành liên quan đến kỹ thuật điện, điện tử, tự động hóa.',
            ],
            [
                'name' => 'Khoa Xây dựng',
                'code' => 'XD',
                'description' => 'Khoa đào tạo các ngành liên quan đến xây dựng, kiến trúc, quy hoạch đô thị.',
            ],
        ];

        foreach ($faculties as $faculty) {
            Faculty::create($faculty);
        }
    }
}
