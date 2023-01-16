<?php

namespace App\Actions\Options;

use App\Models\BpjstkSetting;


class GetBpjstkComponentOptions
{
    public function handle()
    {
        $bpjstk = BpjstkSetting::where('status', true)->get();

        $result = [];
        foreach($bpjstk as $component) {
            $data = [
                'id' => $component->id,
                'components' => [
                    'old_age' => $component->old_age,
                    'life_insurance' => $component->life_insurance,
                    'pension_time' => $component->pension_time,
                ],
            ];

            array_push($result, $data);
        }

        return $result;
    }
}
