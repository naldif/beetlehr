<?php

namespace App\Actions\Options;

use App\Models\Designation;


class GetDesignationOptions
{
    public function handle()
    {
        $designation = Designation::get()->pluck('name', 'id');

        return $designation;
    }
}
