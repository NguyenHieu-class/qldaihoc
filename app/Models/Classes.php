<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classes extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'major_id',
        'year',
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
} 