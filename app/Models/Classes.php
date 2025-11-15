<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classes extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_GRADUATED = 'graduated';

    public const STATUS_LABELS = [
        self::STATUS_ACTIVE => 'Đang hoạt động',
        self::STATUS_INACTIVE => 'Tạm ngưng',
        self::STATUS_GRADUATED => 'Đã tốt nghiệp',
    ];

    public const STATUS_BADGES = [
        self::STATUS_ACTIVE => 'success',
        self::STATUS_INACTIVE => 'secondary',
        self::STATUS_GRADUATED => 'info',
    ];

    protected $fillable = [
        'name',
        'code',
        'major_id',
        'year',
        'status',
    ];

    /**
     * Lấy ngành học mà lớp này thuộc về
     */
    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class);
    }

    /**
     * Lấy tất cả sinh viên thuộc lớp này
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    /**
     * Lấy nhãn trạng thái hiển thị
     */
    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Lấy lớp màu tương ứng với trạng thái
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return self::STATUS_BADGES[$this->status] ?? 'secondary';
    }
}