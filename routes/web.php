<?php

use App\Http\Controllers\ClassController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\MajorController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\CourseOfferingController;
use App\Http\Controllers\ClassSectionController;
use App\Http\Controllers\DegreeController;
use App\Http\Controllers\TeachingRateController;
use App\Http\Controllers\TuitionSettingController;
use App\Http\Controllers\TuitionController;
use App\Http\Controllers\StudentTuitionController;
use App\Http\Controllers\ClassSizeCoefficientController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\TeacherClassController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\PasswordResetRequestController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Trang chủ
Route::get('/', function () {
    return view('welcome');
});

// Xác thực
Auth::routes(['reset' => false]);

// Quên mật khẩu cho giáo viên và sinh viên
Route::get('password/forgot', [PasswordResetRequestController::class, 'create'])->name('password.request');
Route::post('password/forgot', [PasswordResetRequestController::class, 'store'])->name('password.request.store');

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
Route::get('/dashboard/admin', [DashboardController::class, 'admin'])->name('dashboard.admin');
Route::get('/dashboard/teacher', [DashboardController::class, 'teacher'])->name('dashboard.teacher');
Route::get('/dashboard/student', [DashboardController::class, 'student'])->name('dashboard.student');

// Nhóm route yêu cầu xác thực và quyền admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Quản lý khoa
    Route::resource('faculties', FacultyController::class);
    
    // Quản lý ngành học
    Route::resource('majors', MajorController::class);
    
    // Quản lý lớp học
    Route::resource('classes', ClassController::class)->except(['show']);
    
    // Quản lý môn học
    Route::resource('subjects', SubjectController::class);

    // Quản lý học vị
    Route::resource('degrees', DegreeController::class);

    // Quản lý năm học
    Route::resource('academic-years', AcademicYearController::class);

    // Quản lý học kỳ
    Route::resource('semesters', SemesterController::class);

    // Quản lý giáo viên
    Route::resource('teachers', TeacherController::class);

    // Quản lý hệ số và mức lương giảng dạy
    Route::resource('teaching-rates', TeachingRateController::class);
    Route::resource('class-size-coefficients', ClassSizeCoefficientController::class);
    Route::get('tuition-settings', [TuitionSettingController::class, 'index'])->name('tuition-settings.index');
    Route::put('tuition-settings', [TuitionSettingController::class, 'update'])->name('tuition-settings.update');

    // Mở môn học
    Route::resource('course-offerings', CourseOfferingController::class);

    // Lớp học phần
    Route::post('class-sections/generate', [ClassSectionController::class, 'generate'])->name('class-sections.generate');
    Route::get('reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::get("reports/sections", [\App\Http\Controllers\ReportController::class, "sectionsBySemester"])->name("reports.sections");
    Route::get("reports/workload", [\App\Http\Controllers\ReportController::class, "teacherWorkload"])->name("reports.workload");
    Route::get("reports/open-rate", [\App\Http\Controllers\ReportController::class, "subjectOpenRate"])->name("reports.open_rate");
    Route::get('class-sections/{classSection}/students/create', [ClassSectionController::class, 'createStudentSelection'])
        ->name('class-sections.students.create');
    Route::post('class-sections/{classSection}/students', [ClassSectionController::class, 'addStudent'])
        ->name('class-sections.students.store');
    Route::resource('class-sections', ClassSectionController::class)->except(['show']);
    Route::resource('tuitions', TuitionController::class);

    // Nhập sinh viên từ file CSV
    Route::get('students/template', [StudentController::class, 'downloadTemplate'])->name('students.template');
    Route::post('students/import', [StudentController::class, 'import'])->name('students.import');

    // Yêu cầu đặt lại mật khẩu
    Route::get('password-reset-requests', [PasswordResetRequestController::class, 'index'])->name('password-reset-requests.index');
    Route::post('password-reset-requests/process', [PasswordResetRequestController::class, 'process'])->name('password-reset-requests.process');

    // Nhật ký hệ thống
    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
});

// Nhóm route yêu cầu xác thực và quyền admin hoặc giáo viên
Route::middleware(['auth', 'role:admin,teacher'])->group(function () {
    // Quản lý sinh viên
    Route::resource('students', StudentController::class);

    // Quản lý điểm số
    Route::resource('grades', GradeController::class);

    // Bảng lương giáo viên
    Route::get('payrolls/export', [PayrollController::class, 'exportAll'])->name('payrolls.export');
    Route::post('payrolls/export-selected', [PayrollController::class, 'exportSelected'])->name('payrolls.export_selected');
    Route::post('payrolls/teacher/export-selected', [PayrollController::class, 'exportTeacherSelected'])
        ->name('payrolls.teacher_export_selected');
    Route::get('payrolls/{teacher}/export', [PayrollController::class, 'exportDetail'])->name('payrolls.export_detail');
    Route::get('payrolls', [PayrollController::class, 'index'])->name('payrolls.index');
    Route::get('payrolls/{teacher}', [PayrollController::class, 'show'])->name('payrolls.show');
    Route::get('payrolls/sections/{classSection}/export', [PayrollController::class, 'exportSection'])->name('payrolls.section_export');
    Route::patch('payrolls/sections/{classSection}/payment-status', [PayrollController::class, 'updatePaymentStatus'])
        ->name('payrolls.section_payment_status');
    Route::get('payrolls/sections/{classSection}', [PayrollController::class, 'sectionDetail'])->name('payrolls.section');
});

// Trang thông tin lớp học cho tất cả vai trò có thể truy cập hệ thống
Route::middleware(['auth', 'role:admin,teacher,student'])->group(function () {
    Route::get('classes/{class}', [ClassController::class, 'show'])->name('classes.show');
    Route::get('class-sections/{classSection}', [ClassSectionController::class, 'show'])->name('class-sections.show');
});

// Nhóm route dành riêng cho giáo viên
Route::middleware(['auth', 'role:teacher'])->group(function () {
    Route::get('teacher/classes', [TeacherClassController::class, 'index'])->name('teacher.classes.index');
    Route::get('teacher/classes/{classSection}/grades', [TeacherClassController::class, 'gradebook'])->name('teacher.classes.gradebook');
    Route::post('teacher/classes/{classSection}/grades/{student}', [TeacherClassController::class, 'storeGrade'])->name('teacher.classes.gradebook.store');
});

// Đổi mật khẩu cho giáo viên và sinh viên
Route::middleware(['auth', 'role:teacher,student'])->group(function () {
    Route::get('password/change', [PasswordController::class, 'edit'])->name('password.change');
    Route::put('password/change', [PasswordController::class, 'update'])->name('password.update');
});

// Route xem bảng điểm sinh viên (cho admin, giáo viên và sinh viên đó)
Route::get('/students/{student}/transcript', [GradeController::class, 'studentTranscript'])
    ->name('students.transcript')
    ->middleware('auth');

// Đăng ký lớp học phần cho sinh viên
Route::middleware(['auth', 'role:student'])->group(function () {
    Route::get('enrollments', [\App\Http\Controllers\EnrollmentController::class, 'index'])->name('enrollments.index');
    Route::post('class-sections/{classSection}/enroll', [\App\Http\Controllers\EnrollmentController::class, 'store'])->name('enrollments.store');
    Route::delete('enrollments/{enrollment}', [\App\Http\Controllers\EnrollmentController::class, 'destroy'])->name('enrollments.destroy');
    Route::get('my-tuitions', [StudentTuitionController::class, 'index'])->name('student.tuitions.index');
});
