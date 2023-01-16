<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceOffline extends Model
{
    use HasFactory;

    protected $table = 'attendance_offline_logs';
    protected $guarded = ['id'];

    protected $casts = [
        'files_clock_in' => 'array',
        'files_clock_out' => 'array'
    ];
}
