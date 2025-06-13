<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'credits',
        'description',
    ];

    /**
     * Lấy tất cả điểm số của môn học này
     */
    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }
} 