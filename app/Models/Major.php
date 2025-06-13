<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Major extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'faculty_id',
    ];

    /**
     * Lấy khoa mà ngành này thuộc về
     */
    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    /**
     * Lấy tất cả các lớp học thuộc ngành này
     */
    public function classes(): HasMany
    {
        return $this->hasMany(Classes::class);
    }
} 