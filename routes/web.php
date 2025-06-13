<?php

use App\Http\Controllers\ClassController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\MajorController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
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
    
    // Quản lý giáo viên
    Route::resource('teachers', TeacherController::class);
});

// Nhóm route yêu cầu xác thực và quyền admin hoặc giáo viên
Route::middleware(['auth', 'role:admin,teacher'])->group(function () {
    // Quản lý sinh viên
    Route::resource('students', StudentController::class);
    
    // Quản lý điểm số
    Route::resource('grades', GradeController::class);
});

// Route xem bảng điểm sinh viên (cho admin, giáo viên và sinh viên đó)
Route::get('/students/{student}/transcript', [GradeController::class, 'studentTranscript'])
    ->name('students.transcript')
    ->middleware('auth');
