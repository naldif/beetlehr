<?php

namespace App\Actions\Options;

use App\Models\BpjstkSetting;


class GetBpjstkNppOptions
{
    public function handle()
    {
        $bpjstk = BpjstkSetting::where('status', true)->get()->pluck('name', 'id');

        return $bpjstk;
    }
}
