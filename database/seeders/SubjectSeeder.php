<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            // Môn học chung
            [
                'name' => 'Toán cao cấp',
                'code' => 'MATH101',
                'credits' => 3,
                'description' => 'Môn học cung cấp kiến thức về đại số tuyến tính, giải tích và phương trình vi phân.',
            ],
            [
                'name' => 'Vật lý đại cương',
                'code' => 'PHYS101',
                'credits' => 3,
                'description' => 'Môn học cung cấp kiến thức cơ bản về cơ học, nhiệt học, điện từ học và quang học.',
            ],
            [
                'name' => 'Tiếng Anh cơ bản',
                'code' => 'ENG101',
                'credits' => 2,
                'description' => 'Môn học cung cấp kiến thức cơ bản về ngữ pháp, từ vựng và kỹ năng giao tiếp tiếng Anh.',
            ],
            [
                'name' => 'Triết học Mác-Lênin',
                'code' => 'PHIL101',
                'credits' => 2,
                'description' => 'Môn học cung cấp kiến thức về triết học Mác-Lênin, chủ nghĩa duy vật biện chứng và chủ nghĩa duy vật lịch sử.',
            ],
            
            // Môn học CNTT
            [
                'name' => 'Lập trình cơ bản',
                'code' => 'IT101',
                'credits' => 3,
                'description' => 'Môn học cung cấp kiến thức cơ bản về lập trình, cấu trúc dữ liệu và giải thuật.',
            ],
            [
                'name' => 'Cơ sở dữ liệu',
                'code' => 'IT201',
                'credits' => 3,
                'description' => 'Môn học cung cấp kiến thức về cơ sở dữ liệu, mô hình dữ liệu và ngôn ngữ truy vấn SQL.',
            ],
            [
                'name' => 'Lập trình hướng đối tượng',
                'code' => 'IT202',
                'credits' => 3,
                'description' => 'Môn học cung cấp kiến thức về lập trình hướng đối tượng, kế thừa, đa hình và trừu tượng hóa.',
            ],
            [
                'name' => 'Mạng máy tính',
                'code' => 'IT301',
                'credits' => 3,
                'description' => 'Môn học cung cấp kiến thức về mạng máy tính, giao thức mạng và bảo mật mạng.',
            ],
            [
                'name' => 'Phát triển ứng dụng web',
                'code' => 'IT302',
                'credits' => 4,
                'description' => 'Môn học cung cấp kiến thức về phát triển ứng dụng web, HTML, CSS, JavaScript và các framework web.',
            ],
            
            // Môn học Kinh tế
            [
                'name' => 'Kinh tế vĩ mô',
                'code' => 'ECON101',
                'credits' => 3,
                'description' => 'Môn học cung cấp kiến thức về kinh tế vĩ mô, tổng cung, tổng cầu và chính sách kinh tế.',
            ],
            [
                'name' => 'Kinh tế vi mô',
                'code' => 'ECON102',
                'credits' => 3,
                'description' => 'Môn học cung cấp kiến thức về kinh tế vi mô, cung cầu, thị trường và hành vi người tiêu dùng.',
            ],
            [
                'name' => 'Nguyên lý kế toán',
                'code' => 'ACC101',
                'credits' => 3,
                'description' => 'Môn học cung cấp kiến thức cơ bản về kế toán, nguyên tắc kế toán và báo cáo tài chính.',
            ],
            [
                'name' => 'Quản trị học',
                'code' => 'MGT101',
                'credits' => 3,
                'description' => 'Môn học cung cấp kiến thức về quản trị, các chức năng quản trị và kỹ năng quản lý.',
            ],
            
            // Môn học Ngoại ngữ
            [
                'name' => 'Ngữ pháp tiếng Anh',
                'code' => 'ENG201',
                'credits' => 3,
                'description' => 'Môn học cung cấp kiến thức chuyên sâu về ngữ pháp tiếng Anh.',
            ],
            [
                'name' => 'Kỹ năng nghe nói tiếng Anh',
                'code' => 'ENG202',
                'credits' => 3,
                'description' => 'Môn học cung cấp kỹ năng nghe và nói tiếng Anh trong các tình huống giao tiếp.',
            ],
            [
                'name' => 'Kỹ năng đọc hiểu tiếng Anh',
                'code' => 'ENG203',
                'credits' => 3,
                'description' => 'Môn học cung cấp kỹ năng đọc hiểu tiếng Anh trong các văn bản học thuật và thực tế.',
            ],
            [
                'name' => 'Kỹ năng viết tiếng Anh',
                'code' => 'ENG204',
                'credits' => 3,
                'description' => 'Môn học cung cấp kỹ năng viết tiếng Anh trong các thể loại văn bản khác nhau.',
            ],
            
            // Môn học Điện - Điện tử
            [
                'name' => 'Mạch điện',
                'code' => 'EE101',
                'credits' => 3,
                'description' => 'Môn học cung cấp kiến thức về mạch điện, phân tích mạch điện và các định luật cơ bản.',
            ],
            [
                'name' => 'Điện tử cơ bản',
                'code' => 'EE201',
                'credits' => 3,
                'description' => 'Môn học cung cấp kiến thức về linh kiện điện tử, mạch điện tử và kỹ thuật số.',
            ],
            [
                'name' => 'Vi xử lý',
                'code' => 'EE301',
                'credits' => 4,
                'description' => 'Môn học cung cấp kiến thức về vi xử lý, kiến trúc máy tính và lập trình vi điều khiển.',
            ],
            
            // Môn học Xây dựng
            [
                'name' => 'Cơ học kết cấu',
                'code' => 'CE101',
                'credits' => 3,
                'description' => 'Môn học cung cấp kiến thức về cơ học kết cấu, phân tích kết cấu và thiết kế kết cấu.',
            ],
            [
                'name' => 'Vật liệu xây dựng',
                'code' => 'CE201',
                'credits' => 3,
                'description' => 'Môn học cung cấp kiến thức về vật liệu xây dựng, tính chất và ứng dụng của vật liệu.',
            ],
            [
                'name' => 'Thiết kế kiến trúc',
                'code' => 'ARCH101',
                'credits' => 4,
                'description' => 'Môn học cung cấp kiến thức về thiết kế kiến trúc, nguyên tắc thiết kế và phương pháp thiết kế.',
            ],
        ];

        foreach ($subjects as $subject) {
            Subject::create($subject);
        }
    }
}
