<?php

namespace App\Http\Controllers\Settings\WorkReport;

use App\Actions\Utility\Setting\GetWorkReportSettingMenu;
use App\Http\Controllers\AdminBaseController;
use App\Services\Settings\WorkReport\WorkReportGeneralService;
use Carbon\Carbon;
use Inertia\Inertia;

class WorkReportController extends AdminBaseController
{
    public function __construct(
        GetWorkReportSettingMenu $getWorkReportSettingMenu,
        WorkReportGeneralService $workReportGeneralService
    ) {
        $this->getWorkReportSettingMenu = $getWorkReportSettingMenu;
        $this->workReportGeneralService = $workReportGeneralService;
    }

    public function workReportGeneralSettingIndex()
    {
        if($this->workReportGeneralService->getMaxTime()) {
            $timeWorkReport['max_time_work_report'] = [
                'hours' => Carbon::parse($this->workReportGeneralService->getMaxTime()['max_time_work_report'])->format('H'),
                'minutes' => Carbon::parse($this->workReportGeneralService->getMaxTime()['max_time_work_report'])->format('i')
            ];
        }else{
            $timeWorkReport['max_time_work_report'] = null;
        }

        return Inertia::render($this->source . 'settings/workReport/general/Index', [
            "title" => 'BattleHR | Setting Work Report',
            "additional" => [
                'menu' => $this->getWorkReportSettingMenu->handle(),
                'data' => $timeWorkReport
            ]
        ]);
    }
}
