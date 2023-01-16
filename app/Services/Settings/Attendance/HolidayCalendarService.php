<?php

namespace App\Services\Settings\Attendance;

use App\Models\HolidayCalendar;

class HolidayCalendarService
{
    public function getData($request)
    {
        $search = $request->search;

        // Get designation
        $query = HolidayCalendar::query();

        // Filter By Params
        $query->when(request('search', false), function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%');
        });

        return $query->paginate(10);
    }

    public function storeData($request)
    {
        $input = $request->only('name', 'date');

        $holiday = HolidayCalendar::create($input);

        return $holiday;
    }

    public function updateData($id, $request)
    {
        $input = $request->only('name', 'date');

        $holiday = HolidayCalendar::findOrFail($id);
        $holiday->update($input);

        return $holiday;
    }

    public function deleteData($id)
    {
        $holiday = HolidayCalendar::findOrFail($id);
        $holiday->delete();

        return $holiday;
    }
}
