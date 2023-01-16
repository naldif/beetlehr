<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BreakTime extends Model
{
    use HasFactory;

    protected $table = 'breaks';
    protected $guarded = ['id'];

    protected $casts = [
        'files_start_break' => 'array',
        'files_end_break' => 'array'
    ];

    public function attendance_image()
    {
        return $this->belongsTo(AttendanceImage::class);
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }
}
