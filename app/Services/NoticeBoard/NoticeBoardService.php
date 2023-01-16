<?php

namespace App\Services\NoticeBoard;

use App\Models\NoticeBoard;
use App\Services\FileService;

class NoticeBoardService 
{
    public function getData($request)
    {
        $search = $request->search;
        $filter_branch = $request->filter_branch;

        // Get company
        $query = NoticeBoard::with(['branch_detail']);

        // Filter By Params
        $query->when(request('search', false), function ($q) use ($search) {
            $q->where('title', 'like', '%' . $search . '%');
        });
        $query->when(request('filter_branch', false), function ($q) use ($filter_branch) {
            if ($filter_branch !== 'all') {
                $q->where('branch_id', $filter_branch);
            }
        });

        return $query->paginate(10);
    }  

    public function createData($request)
    {
        $inputs = $request->only(['type', 'start', 'end', 'title']);

        if ($request->branch_id === 'all' || $request->branch_id === 'null') {
            $inputs['branch_id'] = null;
        } else {
            $inputs['branch_id'] = $request->branch_id;
        }
        
        if($request->type === 'document') {
            // Upload File
            $fileService = new FileService();
            $file = $fileService->uploadFile($request->file('file'));
            $inputs['file'] = $file->id;
        }else{
            $inputs['description'] = $request->description;
        }

        $noticeboard = NoticeBoard::create($inputs);
        return $noticeboard;
    }

    public function deleteData($id)
    {
        $noticeboard = NoticeBoard::findOrFail($id);
        $noticeboard->delete();

        return $noticeboard;
    }

    public function updateData($id, $request)
    {
        $inputs = $request->only(['type', 'start', 'end', 'title']);

        if ($request->branch_id === 'all' || $request->branch_id === 'null') {
            $inputs['branch_id'] = null;
        } else {
            $inputs['branch_id'] = $request->branch_id;
        }

        if ($request->type === 'document' && $request->file('file')) {
            // Upload File
            $fileService = new FileService();
            $file = $fileService->uploadFile($request->file('file'));
            $inputs['file'] = $file->id;
        } else {
            $inputs['description'] = $request->description;
        }

        $noticeboard = NoticeBoard::findOrFail($id);
        $noticeboard->update($inputs);
        
        return $noticeboard;
    }
}
