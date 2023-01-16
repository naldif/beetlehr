<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'requester' => 'array',
        'meta_data' => 'array',
    ];

    public function approval_steps()
    {
        return $this->hasMany(ApprovalStep::class);
    }

    public function requester_detail()
    {
        return $this->belongsTo(Employee::class, 'requester_id', 'id')->withTrashed();
    }
}
