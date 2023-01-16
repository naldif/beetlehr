<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NoticeBoard extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'notice_boards';

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function branch_detail()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id')->withTrashed();
    }
}
