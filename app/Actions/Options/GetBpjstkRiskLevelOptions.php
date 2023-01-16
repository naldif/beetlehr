<?php

namespace App\Actions\Options;

use App\Models\BpjstkRiskLevel;


class GetBpjstkRiskLevelOptions
{
    public function handle()
    {
        $riskLevels = [];
        $chooses = BpjstkRiskLevel::get();
        foreach ($chooses as $choose) {
            $riskLevels[$choose->id] = $choose->name . ' - ' . $choose->precentage . ' %';
        }

        return $riskLevels;
    }
}
