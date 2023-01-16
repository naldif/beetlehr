<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PphRangeRule extends Model
{
    use HasFactory;

    protected $table = 'pph_range_rules';
    protected $guarded = ['id'];
}
