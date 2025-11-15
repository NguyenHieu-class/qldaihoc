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
        'status',
        'payment_status',
    ];

    public const STATUS_OPEN = 'open';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_CLOSED = 'closed';

    public const STATUS_LABELS = [
        self::STATUS_OPEN => 'Đã mở',
        self::STATUS_ACTIVE => 'Đang hoạt động',
        self::STATUS_CLOSED => 'Đã đóng',
    ];

    public const PAYMENT_STATUS_PENDING = 'pending';
    public const PAYMENT_STATUS_PAID = 'paid';
    public const PAYMENT_STATUS_CANCELLED = 'cancelled';
    public const PAYMENT_STATUS_PARTIAL = 'partial';

    public const PAYMENT_STATUS_LABELS = [
        self::PAYMENT_STATUS_PENDING => 'Chờ thanh toán',
        self::PAYMENT_STATUS_PAID => 'Đã thanh toán',
        self::PAYMENT_STATUS_CANCELLED => 'Huỷ',
        self::PAYMENT_STATUS_PARTIAL => 'Thanh toán một phần',
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

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return self::PAYMENT_STATUS_LABELS[$this->payment_status] ?? $this->payment_status;
    }
}
