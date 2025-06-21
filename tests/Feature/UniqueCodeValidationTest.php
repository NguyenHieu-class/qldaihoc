<?php

namespace Tests\Feature;

use App\Models\Faculty;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UniqueCodeValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_create_duplicate_faculty_code(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $existing = Faculty::factory()->create();

        $data = Faculty::factory()->make(['code' => $existing->code])->toArray();

        $response = $this->actingAs($admin)->post(route('faculties.store'), $data);

        $response->assertSessionHasErrors('code');
        $this->assertEquals(1, Faculty::count());
    }

    public function test_cannot_create_duplicate_teacher_id(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $existing = Teacher::factory()->create();

        $data = Teacher::factory()->make([
            'teacher_id' => $existing->teacher_id,
            'faculty_id' => $existing->faculty_id,
            'degree_id' => $existing->degree_id,
            'user_id' => null,
        ])->toArray();
        $data['create_account'] = false;

        $response = $this->actingAs($admin)->post(route('teachers.store'), $data);

        $response->assertSessionHasErrors('teacher_id');
        $this->assertEquals(1, Teacher::count());
    }

    public function test_cannot_create_duplicate_student_id(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $existing = Student::factory()->create();

        $data = Student::factory()->make([
            'student_id' => $existing->student_id,
            'class_id' => $existing->class_id,
            'user_id' => null,
        ])->toArray();
        $data['create_account'] = false;

        $response = $this->actingAs($admin)->post(route('students.store'), $data);

        $response->assertSessionHasErrors('student_id');
        $this->assertEquals(1, Student::count());
    }
}
