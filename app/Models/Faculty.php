<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Faculty extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
    ];

    /**
     * Lấy tất cả các ngành học thuộc khoa này
     */
    public function majors(): HasMany
    {
        return $this->hasMany(Major::class);
    }

    /**
     * Lấy tất cả giáo viên thuộc khoa này
     */
    public function teachers(): HasMany
    {
        return $this->hasMany(Teacher::class);
    }
} 