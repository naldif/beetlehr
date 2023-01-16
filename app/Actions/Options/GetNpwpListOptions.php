<?php

namespace App\Actions\Options;

use App\Models\NpwpList;


class GetNpwpListOptions
{
    public function handle()
    {
        $npwp = NpwpList::get()->pluck('npwp_name', 'id');

        return $npwp;
    }
}
