<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    /**
     * Hiển thị danh sách sinh viên
     */
    public function index(Request $request)
    {
        $query = Student::with('class.major.faculty');
        
        // Lọc theo lớp
        if ($request->has('class_id') && $request->class_id) {
            $query->where('class_id', $request->class_id);
        }
        
        // Lọc theo ngành học
        if ($request->has('major_id') && $request->major_id) {
            $query->whereHas('class', function($q) use ($request) {
                $q->where('major_id', $request->major_id);
            });
        }
        
        // Lọc theo khoa
        if ($request->has('faculty_id') && $request->faculty_id) {
            $query->whereHas('class.major', function($q) use ($request) {
                $q->where('faculty_id', $request->faculty_id);
            });
        }
        
        // Tìm kiếm theo tên, mã sinh viên hoặc email
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        $students = $query->paginate(10);
        $classes = Classes::with('major.faculty')->get();
        $majors = \App\Models\Major::with('faculty')->get();
        $faculties = \App\Models\Faculty::all();
        
        return view('students.index', compact('students', 'classes', 'majors', 'faculties'));
    }

    /**
     * Hiển thị form tạo sinh viên mới
     */
    public function create()
    {
        $classes = Classes::with('major.faculty')->get();
        return view('students.create', compact('classes'));
    }

    /**
     * Lưu sinh viên mới vào database
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|string|max:50|unique:students',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Nam,Nữ,Khác',
            'email' => 'required|email|unique:students',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'class_id' => 'required|exists:classes,id',
            'create_account' => 'boolean',
            'password' => 'required_if:create_account,1|nullable|min:8',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            $userData = null;
            
            // Tạo tài khoản người dùng nếu được yêu cầu
            if ($request->create_account) {
                $user = User::create([
                    'name' => $request->first_name . ' ' . $request->last_name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'role' => 'student',
                ]);
                
                $userData = $user->id;
            }
            
            // Tạo sinh viên
            $studentData = $request->except('create_account', 'password');
            if ($userData) {
                $studentData['user_id'] = $userData;
            }
            
            Student::create($studentData);
            
            DB::commit();
            
            return redirect()->route('students.index')
                ->with('success', 'Sinh viên đã được tạo thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Đã xảy ra lỗi khi tạo sinh viên: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Hiển thị thông tin chi tiết của sinh viên
     */
    public function show(Student $student)
    {
        $student->load('class.major.faculty', 'grades.subject');
        return view('students.show', compact('student'));
    }

    /**
     * Hiển thị form chỉnh sửa sinh viên
     */
    public function edit(Student $student)
    {
        $classes = Classes::with('major.faculty')->get();
        return view('students.edit', compact('student', 'classes'));
    }

    /**
     * Cập nhật thông tin sinh viên trong database
     */
    public function update(Request $request, Student $student)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|string|max:50|unique:students,student_id,' . $student->id,
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Nam,Nữ,Khác',
            'email' => [
                'required',
                'email',
                Rule::unique('students', 'email')->ignore($student->id),
                Rule::unique('users', 'email')->ignore($student->user_id),
            ],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'class_id' => 'required|exists:classes,id',
            'password' => 'nullable|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $studentData = $request->except('password', 'password_confirmation');
        $student->update($studentData);

        // Cập nhật thông tin người dùng nếu có
        if ($student->user_id) {
            $student->user->update([
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
            ]);

            if ($request->filled('password')) {
                $student->user->update([
                    'password' => Hash::make($request->password),
                ]);
            }
        } elseif ($request->filled('password')) {
            $user = User::create([
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'student',
            ]);

            $student->update([
                'user_id' => $user->id,
            ]);
        }

        return redirect()->route('students.index')
            ->with('success', 'Thông tin sinh viên đã được cập nhật thành công.');
    }

    /**
     * Xóa sinh viên khỏi database
     */
    public function destroy(Student $student)
    {
        DB::beginTransaction();

        try {
            // Xóa tài khoản người dùng nếu có
            if ($student->user_id) {
                $student->user->delete();
            }

            // Xóa sinh viên
            $student->delete();

            DB::commit();

            return redirect()->route('students.index')
                ->with('success', 'Sinh viên đã được xóa thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Đã xảy ra lỗi khi xóa sinh viên: ' . $e->getMessage());
        }
    }

    /**
     * Tải về file CSV mẫu để nhập sinh viên hàng loạt.
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="student_import_template.csv"',
        ];

        $columns = [
            'student_id',
            'first_name',
            'last_name',
            'date_of_birth (YYYY-MM-DD)',
            'gender (Nam/Nữ/Khác)',
            'email',
            'phone',
            'address',
            'class_code',
        ];

        $callback = function () use ($columns) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);
            fputcsv($handle, [
                'SV001',
                'Nguyen',
                'An',
                '2000-01-15',
                'Nam',
                'an.nguyen@example.com',
                '0987654321',
                '123 Đường ABC, Quận 1',
                'CTK42A',
            ]);
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Nhập sinh viên từ file CSV.
     */
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $path = $request->file('csv_file')->getRealPath();
        $handle = fopen($path, 'r');

        if ($handle === false) {
            return redirect()->back()->with('error', 'Không thể đọc file CSV đã tải lên.');
        }

        $header = fgetcsv($handle);

        if (!$header) {
            fclose($handle);
            return redirect()->back()->with('error', 'File CSV không chứa dữ liệu.');
        }

        $normalizedHeader = array_map(function ($value) {
            $normalizedValue = preg_replace('/\s*\(.*?\)\s*/', '', $value);

            return Str::snake(strtolower(trim($normalizedValue)));
        }, $header);

        $expectedHeaders = [
            'student_id',
            'first_name',
            'last_name',
            'date_of_birth',
            'gender',
            'email',
            'phone',
            'address',
            'class_code',
        ];

        if ($normalizedHeader !== $expectedHeaders) {
            fclose($handle);
            return redirect()->back()->with('error', 'Cấu trúc file CSV không hợp lệ. Vui lòng sử dụng file mẫu được cung cấp.');
        }

        $lineNumber = 1;
        $created = 0;
        $errors = [];

        while (($row = fgetcsv($handle)) !== false) {
            $lineNumber++;

            if (count(array_filter($row, fn ($value) => trim((string) $value) !== '')) === 0) {
                continue;
            }

            $data = array_combine($expectedHeaders, array_map('trim', $row));

            if ($data === false) {
                $errors[] = "Dòng {$lineNumber}: Không thể đọc dữ liệu.";
                continue;
            }

            $studentId = $data['student_id'];
            $firstName = $data['first_name'];
            $lastName = $data['last_name'];
            $gender = $data['gender'];
            $email = $data['email'];
            $phone = $data['phone'] ?? null;
            $address = $data['address'] ?? null;
            $classCode = $data['class_code'];

            if (!$studentId || !$firstName || !$lastName || !$email || !$classCode || !$data['date_of_birth'] || !$gender) {
                $errors[] = "Dòng {$lineNumber}: Thiếu thông tin bắt buộc.";
                continue;
            }

            $dateOfBirth = $this->parseDateFromCsv($data['date_of_birth']);
            if (!$dateOfBirth) {
                $errors[] = "Dòng {$lineNumber}: Ngày sinh không hợp lệ.";
                continue;
            }

            if (!in_array($gender, ['Nam', 'Nữ', 'Khác'])) {
                $errors[] = "Dòng {$lineNumber}: Giá trị giới tính phải là Nam, Nữ hoặc Khác.";
                continue;
            }

            $class = Classes::where('code', $classCode)->first();
            if (!$class) {
                $errors[] = "Dòng {$lineNumber}: Không tìm thấy lớp với mã '{$classCode}'.";
                continue;
            }

            if (Student::where('student_id', $studentId)->orWhere('email', $email)->exists()) {
                $errors[] = "Dòng {$lineNumber}: Mã sinh viên hoặc email đã tồn tại.";
                continue;
            }

            if (User::where('email', $email)->exists()) {
                $errors[] = "Dòng {$lineNumber}: Email đã được sử dụng cho tài khoản khác.";
                continue;
            }

            try {
                DB::transaction(function () use (
                    $studentId,
                    $firstName,
                    $lastName,
                    $dateOfBirth,
                    $gender,
                    $email,
                    $phone,
                    $address,
                    $class
                ) {
                    $user = User::create([
                        'name' => $firstName . ' ' . $lastName,
                        'email' => $email,
                        'password' => Hash::make($dateOfBirth->format('dmY')),
                        'role' => 'student',
                    ]);

                    Student::create([
                        'student_id' => $studentId,
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'date_of_birth' => $dateOfBirth->format('Y-m-d'),
                        'gender' => $gender,
                        'email' => $email,
                        'phone' => $phone,
                        'address' => $address,
                        'class_id' => $class->id,
                        'user_id' => $user->id,
                    ]);
                });

                $created++;
            } catch (\Exception $exception) {
                $errors[] = "Dòng {$lineNumber}: " . $exception->getMessage();
            }
        }

        fclose($handle);

        $message = "Đã nhập thành công {$created} sinh viên.";

        return redirect()->route('students.index')
            ->with('success', $message)
            ->with('import_errors', $errors);
    }

    /**
     * Chuyển đổi giá trị ngày tháng trong CSV thành đối tượng Carbon.
     */
    protected function parseDateFromCsv(string $value): ?Carbon
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'd.m.Y', 'm/d/Y'];

        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, $value);
            } catch (\Exception $e) {
                // Tiếp tục thử các định dạng khác
            }
        }

        try {
            return Carbon::parse($value);
        } catch (\Exception $e) {
            return null;
        }
    }
}