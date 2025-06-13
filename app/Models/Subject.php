<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\CourseOffering;
use App\Models\ClassSection;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'credits',
        'description',
        'difficulty_ratio',
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
}
