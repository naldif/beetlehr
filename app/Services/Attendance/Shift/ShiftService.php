<?php

namespace App\Services\Attendance\Shift;

use Carbon\Carbon;
use App\Models\Shift;
use App\Models\Branch;

class ShiftService
{

    public function getData($request)
    {
        $search = $request->search;
        $sort_by_name = $request->sort_by_name;
        $filter_normal_shift = $request->filter_normal_shift === 'true' ? false : true;
        $filter_night_shift = $request->filter_night_shift === 'true' ? true : false;
        $filter_branch = $request->filter_branch;

        $query = Shift::with('branch')->whereHas('branch')->whereIn('is_night_shift', [$filter_normal_shift, $filter_night_shift]);

        // Search By Name
        $query->when(request('search', false), function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%');
        });

        $query->when(request('sort_by_name', false), function ($q) use ($sort_by_name) {
            $q->orderBy('name', $sort_by_name);
        });

        $query->when(request('filter_branch', false), function ($q) use ($filter_branch) {
            $q->whereHas('branch', function ($qu) use ($filter_branch){
                if($filter_branch !== 'all'){
                    $qu->where('branch_id', $filter_branch);
                }
            });
        });

        return $query->paginate(10);
    }

    public function storeData($request){
        $input = $request->only(['name','is_night_shift']);
        $input['start_time'] = Carbon::parse($request->start_time)->format('H:i');
        $input['end_time'] = Carbon::parse($request->end_time)->format('H:i');

        if($request->branch_id === 'all') {
            $branch_ids = Branch::get()->pluck('id');
            foreach ($branch_ids as $key => $value) {
                $input['branch_id'] = $value;
                $query = Shift::create($input);
            }
        }else{
            $input['branch_id'] = $request->branch_id;
            $query = Shift::create($input);
        }

        return $query;
    }

    public function updateData($id,$request){
        $input = $request->only(['branch_id','name','is_night_shift']);
        $input['start_time'] = Carbon::parse($request->start_time)->format('H:i');
        $input['end_time'] = Carbon::parse($request->end_time)->format('H:i');

        $query = Shift::findOrFail($id);
        $query->update($input);

        return $query;
    }

    public function destroyData($id){
        $query = Shift::findOrFail($id);
        $query->delete();

        return $query;
    }
}
