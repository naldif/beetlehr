<?php

namespace App\Actions\Utility\Approval;

use Carbon\Carbon;
use App\Models\Leave;
use App\Services\FileService;
use App\Models\AttendanceOffline;

class ApprovalMetaFormat
{
    public function handle($data)
    {
        switch ($data->type) {
            case 'create_leave':
                return $this->formatLeaveMeta($data);
                break;

            case 'attendance_without_schedule':
                return $this->formatAttendanceMeta($data);
                break;

            default:
                return $this->formatDefaultMeta($data);
                break;
        }
    }

    public function formatLeaveMeta($data)
    {
        $leave = Leave::findOrFail($data->reference_id);
        $file_service = new FileService();
        $file = $file_service->getFileById($leave->file);

        return [
            'requester_name' => $data->requester['name'],
            'start_date' => Carbon::parse($data->meta_data['start_date'])->format('Y-m-d'),
            'end_date' => Carbon::parse($data->meta_data['end_date'])->format('Y-m-d'),
            'duration' => $data->meta_data['duration'],
            'reason' => $data->meta_data['reason'],
            'type_label' => 'Time Off',
            'type_mobile' => 'time_off',
            'additional_file' => $file->full_path
        ];
    }

    public function formatAttendanceMeta($data)
    {
        $attendance = AttendanceOffline::findOrFail($data->reference_id);
        $file_service = new FileService();
        $image_clock_in = $file_service->getFileById($attendance->image_id_clock_in);
        $image_clock_out = $file_service->getFileById($attendance->image_id_clock_out);

        return [
            'date' => Carbon::parse($data->meta_data['date'])->format('d F Y'),
            'clock_in' => $data->meta_data['clock_in'],
            'clock_out' => $data->meta_data['clock_out'],
            'notes_clock_in' => $data->meta_data['notes_clock_in'],
            'notes_clock_out' => $data->meta_data['notes_clock_out'],
            'address_clock_in' => $data->meta_data['address_clock_in'],
            'address_clock_out' => $data->meta_data['address_clock_out'],
            'total_work_hours' => $data->meta_data['total_work_hours'],
            'image_clock_in' => $image_clock_in->full_path,
            'image_clock_out' => $image_clock_out->full_path
        ];
    }

    public function formatDefaultMeta($data)
    {
        return [
            'note' => $data->note
        ];
    }
}
