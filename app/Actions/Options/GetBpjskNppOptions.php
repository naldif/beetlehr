<?php

namespace App\Actions\Options;

use App\Models\BpjskSetting;


class GetBpjskNppOptions
{
    public function handle()
    {
        $bpjsk = BpjskSetting::where('status', true)->get()->pluck('name', 'id');

        return $bpjsk;
    }
}
