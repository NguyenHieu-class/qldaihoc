<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Degree extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'coefficient',
    ];

    /**
     * Lấy tất cả giáo viên có học vị này
     */
    public function teachers(): HasMany
    {
        return $this->hasMany(Teacher::class);
    }
}
