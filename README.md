# Báo cáo Dự án Quản lý Đại Học

## Chương 1 – Tổng quan về ứng dụng web
Ứng dụng nhằm hỗ trợ công tác quản lý giảng dạy trong nhà trường. Đối tượng sử dụng gồm quản trị viên, giáo viên và sinh viên. Các chức năng chính bao gồm quản lý khoa, ngành học, lớp học, môn học, mở lớp, tính lương giảng dạy và báo cáo trực quan.

## Chương 2 – Kiến trúc và công nghệ sử dụng
Ứng dụng xây dựng theo kiến trúc Laravel MVC, sử dụng MySQL làm cơ sở dữ liệu và Blade cho phần giao diện. Các thành phần chính:
- **Controllers**: xử lý nghiệp vụ và điều hướng dữ liệu.
- **Models**: đại diện cho các bảng trong cơ sở dữ liệu.
- **Views**: giao diện người dùng được viết bằng Blade.
- **Services**: lớp trung gian phục vụ các nghiệp vụ phức tạp như tính lương.
- **Routes**: định tuyến HTTP cho toàn bộ ứng dụng.
- **Middleware**: kiểm soát truy cập và các bước xử lý trước khi vào Controller.

## Chương 3 – Cài đặt các chức năng
Cấu trúc thư mục chính của dự án:
- `app/Http/Controllers`: chứa các controller.
- `app/Models`: các model tương ứng với bảng dữ liệu.
- `app/Services`: các service hỗ trợ.
- `routes/web.php`: khai báo route cho ứng dụng.
- `resources/views`: chứa các view Blade.

Chi tiết các chức năng:
1. **Quản lý Khoa** – `FacultyController`, bảng `faculties`.
2. **Quản lý Ngành Học** – `MajorController`, bảng `majors`.
3. **Quản lý Lớp Học** – `ClassController`, bảng `classes`.
4. **Quản lý Học Phần/Môn Học** – `SubjectController`, bảng `subjects`.
5. **Quản lý Lớp Học Phần** – `ClassSectionController`, bảng `class_sections`.
6. **Quản lý Giáo Viên** – `TeacherController`, bảng `teachers`.
7. **Quản lý Bằng Cấp** – `DegreeController`, bảng `degrees`.
8. **Quản lý Năm Học** – `AcademicYearController`, bảng `academic_years`.
9. **Quản lý Kỳ Học** – `SemesterController`, bảng `semesters`.
10. **Quản lý Mở Môn Học** – `CourseOfferingController`, bảng `course_offerings`.
11. **Quản lý Mở Lớp Học Phần** – tạo/gán lớp từ `ClassSectionController`.
12. **Quản lý Hệ số Lớp** – `ClassSizeCoefficientController`, bảng `class_size_coefficients`.
13. **Quản lý Mức Lương Giảng Dạy** – `TeachingRateController`, bảng `teaching_rates`.
14. **Quản lý Bảng Lương** – `PayrollController` và `TeachingPaymentService`.
15. **Chức năng Báo Cáo bằng Biểu Đồ** – `ReportController`, view tại `resources/views/reports`.

## Hướng dẫn triển khai và chạy dự án
1. Cài đặt PHP, Composer, Node.js và MySQL.
2. Tạo file `.env` từ mẫu `.env.example`, khai báo thông tin kết nối MySQL.
3. Chạy `composer install` để cài đặt thư viện PHP.
4. Chạy `npm install && npm run build` để biên dịch tài nguyên frontend.
5. Thực hiện `php artisan key:generate`.
6. Chạy `php artisan migrate` để tạo bảng dữ liệu, có thể `php artisan db:seed` để sinh dữ liệu mẫu nếu cần.
7. Khởi động server với `php artisan serve` và truy cập theo địa chỉ hiển thị.

Tài khoản mặc định (có thể tùy chỉnh trong seeders):
- Quản trị viên: `admin@example.com` / `password`.
- Giáo viên: `teacher@example.com` / `password`.
- Sinh viên: `student@example.com` / `password`.

Sau khi đăng nhập, người dùng sẽ được phân quyền tương ứng để thao tác các chức năng.
