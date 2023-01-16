<?php

namespace App\Actions\Options;

use App\Models\Branch;


class GetBranchOptions
{
    public function handle()
    {
        $branch = Branch::get()->pluck('name', 'id');

        return $branch;
    }
}
