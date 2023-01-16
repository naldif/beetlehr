<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
    public function shift_detail()
    {
        return $this->belongsTo(Shift::class, 'shift_id', 'id')->withTrashed();
    }

    public function user_detail()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withTrashed();
    }
}
