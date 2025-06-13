<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DegreeCoefficient extends Model
{
    use HasFactory;

    protected $fillable = ['degree_id', 'coefficient'];

    public function degree(): BelongsTo
    {
        return $this->belongsTo(Degree::class);
    }
}
