<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TuitionSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'per_credit_amount',
    ];
}
