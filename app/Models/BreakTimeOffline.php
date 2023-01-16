<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BreakTimeOffline extends Model
{
    use HasFactory;

    protected $table = 'break_offline_attendances';
    protected $guarded = ['id'];

    protected $casts = [
        'files_start_break' => 'array',
        'files_end_break' => 'array'
    ];
}
