<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeaveType extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public function branch_detail()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id')->withTrashed();
    }
}
