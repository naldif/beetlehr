<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'files_clock_in' => 'array',
        'files_clock_out' => 'array',
        'is_force_clock_out' => 'boolean',
    ];

    public function user_detail()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withTrashed();
    }
}
