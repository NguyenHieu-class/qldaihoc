<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tuition extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_OVERDUE = 'overdue';

    protected $fillable = [
        'student_id',
        'semester_id',
        'amount',
        'status',
        'due_date',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_at' => 'datetime',
    ];

    /**
     * Danh sách trạng thái hỗ trợ hiển thị.
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => 'Chờ thanh toán',
            self::STATUS_PAID => 'Đã thanh toán',
            self::STATUS_OVERDUE => 'Quá hạn',
        ];
    }

    /**
     * Sinh viên liên quan.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Học kỳ tương ứng.
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Nhãn hiển thị trạng thái.
     */
    public function getStatusLabelAttribute(): string
    {
        return self::statuses()[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Màu sắc phù hợp cho badge trạng thái.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PAID => 'bg-success',
            self::STATUS_OVERDUE => 'bg-danger',
            default => 'bg-warning text-dark',
        };
    }

    /**
     * Kiểm tra xem khoản học phí đã quá hạn chưa.
     */
    public function getIsOverdueAttribute(): bool
    {
        if ($this->status === self::STATUS_PAID || ! $this->due_date) {
            return false;
        }

        return $this->due_date->isPast();
    }
}
