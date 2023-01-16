<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function employee_groups()
    {
        return $this->hasMany(EmployeeGroup::class);
    }

    public static function boot()
    {
        parent::boot();
        self::deleting(function ($group) { // before delete() method call this
            $group->employee_groups()->each(function ($employee_groups) {
                $employee_groups->delete(); // <-- direct deletion
            });
        });
    }
}
