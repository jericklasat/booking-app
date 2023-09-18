<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_day_available',
        'end_day_available',
        'min_time_available',
        'max_time_available',
        'deleted_at'
    ];
}
