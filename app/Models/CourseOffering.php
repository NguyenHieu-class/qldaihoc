<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseOffering extends Model
{
    use HasFactory;

    public const STATUS_OPEN = 'open';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_CLOSED = 'closed';

    public const STATUS_LABELS = [
        self::STATUS_OPEN => 'Đã mở',
        self::STATUS_ACTIVE => 'Đang hoạt động',
        self::STATUS_CLOSED => 'Đã đóng',
    ];

    protected $fillable = [
        'subject_id',
        'semester_id',
        'status',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }
}
