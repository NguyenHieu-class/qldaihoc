<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\CourseOffering;
use App\Models\ClassSection;
use App\Models\Faculty;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'credits',
        'description',
        'difficulty_ratio',
        'faculty_id',
    ];

    /**
     * Lấy tất cả điểm số của môn học này
     */
    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    /**
     * Các lần mở môn học trong các học kỳ
     */
    public function courseOfferings(): HasMany
    {
        return $this->hasMany(CourseOffering::class);
    }

    /**
     * Các lớp học phần của môn học
     */
    public function classSections(): HasMany
    {
        return $this->hasMany(ClassSection::class);
    }

    /**
     * Khoa quản lý môn học
     */
    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }
}
