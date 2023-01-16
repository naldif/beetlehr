<?php

namespace App\Http\Resources\Api\V1\Attendance;

use Carbon\Carbon;
use App\Models\Schedule;
use App\Models\BreakTime;
use App\Helpers\Utility\Time;
use App\Services\FileService;
use App\Actions\Utility\GetFile;
use App\Models\BreakTimeOffline;
use App\Helpers\Utility\Authentication;
use Illuminate\Http\Resources\Json\ResourceCollection;

class AttendanceDetailResource extends ResourceCollection
{
    protected $status;

    public function status($value)
    {
        $this->status = $value;
        return $this;
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $breaks = [];
        $final_work_hours = '00:00:00';
        $file_service = new FileService();
        $get_files = new GetFile();

        if ($this->collection->count() > 0) {
            if($this->status){
                $breaks = BreakTimeOffline::where('attendance_offline_log_id', $this->collection->first()->id)->get();
            }else{
                $breaks = BreakTime::where('attendance_id', $this->collection->first()->id)->get();
            }
            $final_work_hours = $this->collection->first()->total_work_hours;
        }

        $total_breaks = Time::calculateTotalHours(collect($breaks)->pluck('total_work_hours'));
        $break_result = [];
        foreach ($breaks as $q) {
            // Clock In Data
            if ($q->image_id_start_break) {
                $image = $file_service->getFileById($q->image_id_start_break);
                $image_clock_in = [
                    'id' => $image->id,
                    'url' =>  $image->full_path,
                    'file_name' => $image->file_name,
                    'extension' => $image->extension,
                    'size' => $image->size
                ];
            } else {
                $image_clock_in = (object)[];
            }
            $files_clock_in = $get_files->handle($q->files_start_break);
            $data_clock_in = [
                'id' => $q->id,
                'clock' => $q->start_time,
                'type' => "start",
                'latitude' => $q->latitude_start_break,
                'longitude' => $q->longitude_start_break,
                'address' => $q->address_start_break,
                'image' => $image_clock_in,
                'files' => $files_clock_in,
                'notes'  => $q->note_start_break,
            ];
            array_push($break_result, $data_clock_in);

            // Clock Out Data
            if ($q->end_time != null) {
                if ($q->image_id_end_break) {
                    $image = $file_service->getFileById($q->image_id_end_break);
                    $image_clock_out = [
                        'id' => $image->id,
                        'url' =>  $image->full_path,
                        'file_name' => $image->file_name,
                        'extension' => $image->extension,
                        'size' => $image->size
                    ];
                } else {
                    $image_clock_out = (object)[];
                }
                $files_clock_out = $get_files->handle($q->files_end_break);
                $data_clock_out = [
                    'id' => $q->id,
                    'clock' => $q->end_time,
                    'type' => "end",
                    'latitude' => $q->latitude_end_break,
                    'longitude' => $q->longitude_end_break,
                    'address' => $q->address_end_break,
                    'image' => $image_clock_out,
                    'files' => $files_clock_out,
                    'notes'  => $q->note_end_break,
                ];
                array_push($break_result, $data_clock_out);
            }
        }

        return [
            'data' =>  $this->transformCollection($this->collection)->first(),
            'break' => $break_result,
            'total_breaks' => $total_breaks,
            'total_hours' => $final_work_hours,
            'total_breaks_formatted' => Carbon::parse($total_breaks)->format('H') . ' Jam ' . Carbon::parse($total_breaks)->format('i') . ' Menit',
            'total_hours_formatted' => Carbon::parse($final_work_hours)->format('H') . ' Jam ' . Carbon::parse($final_work_hours)->format('i') . ' Menit',
            'meta' => [
                "success" => true,
                "message" => "Success Get Attendance Detail",
                'pagination' => $this->metaData()
            ]
        ];
    }

    public function transformData($data)
    {
        $results = [];

        $employee = Authentication::getEmployeeLoggedIn();
        $timezone = $employee->branch_detail->timezone;
        $schedule = Schedule::where('user_id', $data->user_id)->where('date', $data->date_clock)->first();
        $get_files = new GetFile();
        $file_service = new FileService();
        $image_clock_in = $file_service->getFileById($data->image_id_clock_in);
        $files_clock_in = $get_files->handle($data->files_clock_in);

        $data_clockin = [
            'id' => $data->id,
            'date' => Carbon::parse($data->date_clock)->format('d F Y'),
            'clock' => Carbon::parse($data->clock_in)->timezone($timezone)->format('H:i:s'),
            'clock_gmt' => $data->clock_in,
            'type' => 'in',
            'image' => [
                'id' => $image_clock_in->id,
                'url' =>  $image_clock_in->full_path,
                'file_name' => $image_clock_in->file_name,
                'extension' => $image_clock_in->extension,
                'size' => $image_clock_in->size
            ],
            'files' => $files_clock_in,
            'latitude' => $data->latitude_clock_in,
            'longitude' => $data->longitude_clock_in,
            'address' => $data->address_clock_in,
            'notes' => $data->notes_clock_in,
            'schedule_clock' => (isset($schedule)) ? Carbon::parse($schedule->shift_detail->start_time)->timezone($timezone)->format('H:i:s') : '-',
            'schedule_clock_gmt' => (isset($schedule)) ? $schedule->shift_detail->start_time : '-',
            'is_late' => $data->is_late_clock_in == 0 ? false : true,
        ];
        array_push($results, $data_clockin);
        
        if ($data->clock_out != null) {
            if ($data->is_force_clock_out == 1) {
                $image_clock_out = $file_service->getFileById($data->image_id_clock_out);
            } else {
                $image_clock_out = $file_service->getFileById($data->image_id_clock_out);
            }
            $files_clock_out = $get_files->handle($data->files_clock_out);

            $data_clockout = [
                'id' => $data->id,
                'date' => Carbon::parse($data->date_clock)->format('d F Y'),
                'clock' => Carbon::parse($data->clock_out)->timezone($timezone)->format('H:i:s'),
                'clock_gmt' => $data->clock_out,
                'type' => 'out',
                'image' => [
                    'id' => $image_clock_out->id,
                    'url' =>  $image_clock_out->full_path,
                    'file_name' => $image_clock_out->file_name,
                    'extension' => $image_clock_out->extension,
                    'size' => $image_clock_out->size
                ],
                'files' => $files_clock_out,
                'latitude' => $data->latitude_clock_out,
                'longitude' => $data->longitude_clock_out,
                'address' => $data->address_clock_out,
                'notes' => $data->notes_clock_out,
                'schedule_clock' => (isset($schedule)) ? Carbon::parse($schedule->shift_detail->end_time)->timezone($timezone)->format('H:i:s') : '-',
                'schedule_clock_gmt' => (isset($schedule)) ? $schedule->shift_detail->end_time : '-',
                'is_late' => $data->is_early_clock_out == 0 ? false : true,
            ];
            array_push($results, $data_clockout);
        }

        return $results;
    }

    public function transformCollection($collection)
    {
        return $collection->transform(function ($data) {
            return $this->transformData($data);
        });
    }

    public function metaData()
    {
        return [
            "total" => $this->total(),
            "count" => $this->count(),
            "per_page" => (int)$this->perPage(),
            "current_page" => $this->currentPage(),
            "total_pages" => $this->lastPage(),
            "links" => [
                "next" => $this->nextPageUrl()
            ],
        ];
    }
}
