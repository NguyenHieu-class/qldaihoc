<?php

use App\Http\Controllers\ClassController;
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
use App\Http\Controllers\ClassSizeCoefficientController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\PayrollController;
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
Auth::routes();

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
Route::get('/dashboard/admin', [DashboardController::class, 'admin'])->name('dashboard.admin');
Route::get('/dashboard/teacher', [DashboardController::class, 'teacher'])->name('dashboard.teacher');
Route::get('/dashboard/head', [DashboardController::class, 'departmentHead'])->name('dashboard.head');
Route::get('/dashboard/student', [DashboardController::class, 'student'])->name('dashboard.student');

// Nhóm route yêu cầu xác thực và quyền admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Quản lý khoa
    Route::resource('faculties', FacultyController::class);
    
    // Quản lý ngành học
    Route::resource('majors', MajorController::class);
    
    // Quản lý lớp học
    Route::resource('classes', ClassController::class);
    
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

    // Mở môn học
    Route::resource('course-offerings', CourseOfferingController::class);

    // Lớp học phần
    Route::post('class-sections/generate', [ClassSectionController::class, 'generate'])->name('class-sections.generate');
    Route::get('reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::get("reports/sections", [\App\Http\Controllers\ReportController::class, "sectionsBySemester"])->name("reports.sections");
    Route::get("reports/workload", [\App\Http\Controllers\ReportController::class, "teacherWorkload"])->name("reports.workload");
    Route::get("reports/open-rate", [\App\Http\Controllers\ReportController::class, "subjectOpenRate"])->name("reports.open_rate");
    Route::resource('class-sections', ClassSectionController::class);
});

// Nhóm route yêu cầu xác thực và quyền admin hoặc giáo viên
Route::middleware(['auth', 'role:admin,teacher'])->group(function () {
    // Quản lý sinh viên
    Route::resource('students', StudentController::class);

    // Quản lý điểm số
    Route::resource('grades', GradeController::class);

    // Bảng lương giáo viên
    Route::get('payrolls/export', [PayrollController::class, 'exportAll'])->name('payrolls.export');
    Route::get('payrolls/{teacher}/export', [PayrollController::class, 'exportDetail'])->name('payrolls.export_detail');
    Route::get('payrolls', [PayrollController::class, 'index'])->name('payrolls.index');
    Route::get('payrolls/{teacher}', [PayrollController::class, 'show'])->name('payrolls.show');
    Route::get('payrolls/sections/{classSection}/export', [PayrollController::class, 'exportSection'])->name('payrolls.section_export');
    Route::get('payrolls/sections/{classSection}', [PayrollController::class, 'sectionDetail'])->name('payrolls.section');
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
});
