<?php

namespace App\Actions\Options;

use App\Models\Shift;

class GetShiftOptions
{
    public function handle($withTrash = false)
    {
        if($withTrash) {
            $shift = Shift::withTrashed()->get()->pluck('name', 'id');
        }else{
            $shift = Shift::get()->pluck('name', 'id');
        }

        return $shift;
    }
}
