<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\TeachingRate;

class ClassSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'course_offering_id',
        'subject_id',
        'teacher_id',
        'teaching_rate_id',
        'room',
        'period_count',
        'student_count',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function teachingRate(): BelongsTo
    {
        return $this->belongsTo(TeachingRate::class);
    }

    public function courseOffering(): BelongsTo
    {
        return $this->belongsTo(CourseOffering::class);
    }

    /**
     * Sinh viên đăng ký lớp học phần
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'enrollments')->withTimestamps();
    }

    /**
     * Các đăng ký lớp học phần
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }
}
