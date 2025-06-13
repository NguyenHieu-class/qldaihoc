<?php

namespace Database\Seeders;

use App\Models\Faculty;
use App\Models\Major;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MajorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy ID của các khoa
        $cntt = Faculty::where('code', 'CNTT')->first()->id;
        $kt = Faculty::where('code', 'KT')->first()->id;
        $nn = Faculty::where('code', 'NN')->first()->id;
        $ddt = Faculty::where('code', 'DĐT')->first()->id;
        $xd = Faculty::where('code', 'XD')->first()->id;

        $majors = [
            // Ngành thuộc khoa CNTT
            [
                'name' => 'Công nghệ thông tin',
                'code' => 'CNTT',
                'description' => 'Ngành đào tạo về công nghệ thông tin, lập trình, phát triển phần mềm.',
                'faculty_id' => $cntt,
            ],
            [
                'name' => 'Khoa học máy tính',
                'code' => 'KHMT',
                'description' => 'Ngành đào tạo về khoa học máy tính, trí tuệ nhân tạo, học máy.',
                'faculty_id' => $cntt,
            ],
            [
                'name' => 'Kỹ thuật phần mềm',
                'code' => 'KTPM',
                'description' => 'Ngành đào tạo về kỹ thuật phần mềm, quy trình phát triển phần mềm.',
                'faculty_id' => $cntt,
            ],
            
            // Ngành thuộc khoa Kinh tế
            [
                'name' => 'Quản trị kinh doanh',
                'code' => 'QTKD',
                'description' => 'Ngành đào tạo về quản trị kinh doanh, quản lý doanh nghiệp.',
                'faculty_id' => $kt,
            ],
            [
                'name' => 'Tài chính - Ngân hàng',
                'code' => 'TCNH',
                'description' => 'Ngành đào tạo về tài chính, ngân hàng, đầu tư.',
                'faculty_id' => $kt,
            ],
            
            // Ngành thuộc khoa Ngoại ngữ
            [
                'name' => 'Ngôn ngữ Anh',
                'code' => 'NNA',
                'description' => 'Ngành đào tạo về ngôn ngữ Anh, biên-phiên dịch tiếng Anh.',
                'faculty_id' => $nn,
            ],
            [
                'name' => 'Ngôn ngữ Nhật',
                'code' => 'NNJ',
                'description' => 'Ngành đào tạo về ngôn ngữ Nhật, biên-phiên dịch tiếng Nhật.',
                'faculty_id' => $nn,
            ],
            
            // Ngành thuộc khoa Điện - Điện tử
            [
                'name' => 'Kỹ thuật điện',
                'code' => 'KTD',
                'description' => 'Ngành đào tạo về kỹ thuật điện, hệ thống điện.',
                'faculty_id' => $ddt,
            ],
            [
                'name' => 'Kỹ thuật điện tử',
                'code' => 'KTDT',
                'description' => 'Ngành đào tạo về kỹ thuật điện tử, vi mạch điện tử.',
                'faculty_id' => $ddt,
            ],
            
            // Ngành thuộc khoa Xây dựng
            [
                'name' => 'Kỹ thuật xây dựng',
                'code' => 'KTXD',
                'description' => 'Ngành đào tạo về kỹ thuật xây dựng, công trình xây dựng.',
                'faculty_id' => $xd,
            ],
            [
                'name' => 'Kiến trúc',
                'code' => 'KT',
                'description' => 'Ngành đào tạo về kiến trúc, thiết kế kiến trúc.',
                'faculty_id' => $xd,
            ],
        ];

        foreach ($majors as $major) {
            Major::create($major);
        }
    }
}
