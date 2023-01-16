<?php

namespace App\Services\Api\V1\Noticeboard;

use Carbon\Carbon;
use App\Models\NoticeBoard;
use App\Helpers\Utility\Authentication;

class NoticeBoardService
{
    public function getData($request)
    {
        // Required Init Data
        $employee = Authentication::getEmployeeLoggedIn();
        $per_page = $request->per_page ? $request->per_page : 10;
        $today = Carbon::now()->format('Y-m-d H:i:s');
        $order_by = $request->order_by ? $request->order_by : 'desc';

        // Get Notice Boards 
        return NoticeBoard::where('start', '<=', $today)->where('end', '>=', $today)->where(function ($query) use ($employee) {
            $query->where('branch_id', $employee->branch_id)->orWhere('branch_id', null);
        })->orderBy('created_at', $order_by)->paginate($per_page); 
    }
}
