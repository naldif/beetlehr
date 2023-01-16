<?php

namespace App\Actions\Options;

use App\Models\PtkpTaxList;

class GetPtkpTaxListOptions
{
    public function handle()
    {
        $ptkpTax = PtkpTaxList::get()->pluck('name', 'id');
        return $ptkpTax;
    }
}
