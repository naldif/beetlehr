<?php

namespace App\Services\Settings\Company;

use App\Actions\Utility\GetFile;
use App\Models\CompanyInformation;

class ProfileService
{
    public function getProfileCompany()
    {
        $getFile = new GetFile();
        $companyInformation = CompanyInformation::first();

        if(isset($companyInformation)) {
            $result = [
                'name' => $companyInformation->name ?: 'BattleHR',
                'logo' => $companyInformation->logo ? $getFile->handle($companyInformation->logo)->full_path : asset('img/beetlehr-logo.svg'),
                'address' => $companyInformation->address ?: '-',
                'email' => $companyInformation->email ?: 'developer@beetlehr.com',
                'phone_number' => $companyInformation->phone_number ?: '-',
                'company_name' => $companyInformation->company_name ?: 'BattleHR',
                'status' => $companyInformation->status ?: 'healthy'
            ];
        }else {
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

    public function updateProfilePicture($file)
    {
        $companyInformation = CompanyInformation::first();

        if(isset($companyInformation)){
            $companyInformation->update([
                'logo' => $file->id
            ]);
        }else{
            $companyInformation = CompanyInformation::create([
                'logo' => $file->id
            ]);
        }

        return $companyInformation;
    }

    public function updateProfile($request)
    {
        $companyInformation = CompanyInformation::first();

        if (isset($companyInformation)) {
            $companyInformation->update([
                'name' => $request->name,
                'company_name' => $request->company_name,
                'address' => $request->address,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'status' => $request->status
            ]);
        } else {
            $companyInformation = CompanyInformation::create([
                'name' => $request->name,
                'company_name' => $request->company_name,
                'address' => $request->address,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'status' => $request->status
            ]);
        }

        return $companyInformation;
    }
}
