<?php

namespace App\Services\Api\V1\Server;

use App\Actions\Utility\GetFile;
use App\Models\CompanyInformation;

class ServerService
{
    public function getStatus()
    {
        $getFile = new GetFile();
        $companyInformation = CompanyInformation::first();

        if (isset($companyInformation)) {
            $result = [
                'name' => $companyInformation->name ?: 'BattleHR',
                'logo' => $companyInformation->logo ? $getFile->handle($companyInformation->logo)->full_path : asset('img/beetlehr-logo.svg'),
                'address' => $companyInformation->address ?: '-',
                'email' => $companyInformation->email ?: 'developer@beetlehr.com',
                'phone_number' => $companyInformation->phone_number ?: '-',
                'company_name' => $companyInformation->company_name ?: 'BattleHR',
                'status' => $companyInformation->status ?: 'healthy'
            ];
        } else {
            $result = [
                'name' => 'BattleHR',
                'logo' => asset('img/beetlehr-logo.svg'),
                'address' => '-',
                'email' => 'developer@beetlehr.com',
                'phone_number' => '-',
                'company_name' => 'BattleHR',
                'status' => 'healthy'
            ];
        }

        return $result;
    }
}
